<?php

namespace App\Http\Controllers\Reports\streamDownloadCSV\BillingReportsCSV;

use DB;
use Carbon;
use Request;
use Auth;
use PHPExcel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Financials\Api\FinancialApiController as FinancialApiController;
use App\Http\Controllers\Reports\Financials\FinancialController as FinancialController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnbilledClaimAnalysisController extends Controller
{
    const SIZE_CHUNK = 500;
    
    private $fileHandle;
    private $count = 0; 
    private $patient_total_payment = 0;
    private $insurance_total_payment = 0;
    private $total_billed = 0;
    private $patient_total_adj = 0;
    private $insurance_total_adj = 0;
    private $global_charges;
    private $global_payments;
    private $global_pmt_adj;

    public function execute()
    {
        return new StreamedResponse(function () {
            $this->openFile();
            $this->addContentHeaderInFile();
            self::processUsers();
            $this->closeFile();
        },
        Response::HTTP_OK, $this->headers()
    );
    }
    
    private function openFile()
    {
        $this->fileHandle = fopen('php://output', 'w');
        while (!feof($this->fileHandle))
        {
            echo fread($this->fileHandle, 65536);
            flush(); // this is essential for large downloads
        } 
    }
 
    private function addContentHeaderInFile()
    {
        $request = Request::all();
        // $request['export'] = 'xlsx';
        $header = ['Acc No','Patient Name','DOS', 'Claim No', 'Payer', 'Facility', 'Rendering', 'Billing', 'Created Date', 'Days Since Created', 'Charges($)'];
        $transaction_date = explode("-", $request['select_transaction_date']);

        $this->putRowInCsv(['']);
        $this->putRowInCsv([' ',' ',' ','Charges & Payments Summary']);
        $this->putRowInCsv([' ',' ','Transaction Date : "'.$transaction_date[0].'" to "'.$transaction_date[1].'"']);
        // App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',$practice_id)
        // Transaction Date : 07/01/19 To 07/08/19 | Billing Provider : All
        $this->putRowInCsv(['']);
        $this->putRowInCsv($header);
    }
    
    public function processUsers($export = '', $data = '')
    {

        $request = Request::all();
        $data = $request;
        $export = "csv";
        $financialApi = new FinancialApiController;

        $api_response = $financialApi->getUnbilledClaimApi($export,$data);// DB
        $api_response_data = $api_response->getData();

        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
        $unbilled_claim = $api_response_data->data->unbilled_claim;
        $total_charges = $api_response_data->data->total_charges;
        $search_by = $api_response_data->data->search_by;
        $date = date('m-d-Y');
        $timestamp = time();
        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $data['unbilled_claim_details'] = $unbilled_claim_details;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['total_charges'] = $total_charges;
        $data['createdBy'] = $createdBy;
        $data['practice_id'] = $practice_id;
        $data['export'] = $export;
 
        $get_function_value = $unbilled_claim;
        $unbilledClaims = $unbilled_claim_details;

            // $get_function_value->chunk(self::SIZE_CHUNK, function ($unbilledClaims) {
                if(count($unbilledClaims) > 0) {
                foreach ($unbilledClaims as $lists) {
                    self::addUserLine($lists);
                }
                }
            // });
        // $this->addSummaryInFile();
        $this->processSummary();
    }

public function addUserLine($lists)
{
    if(isset($lists->account_no) && $lists->account_no != ''){ 
        $pat_name = @$lists->last_name .', '. @$lists->first_name .' '. @$lists->middle_name;
        $dos = Helpers::checkAndDisplayDateInInput(@$lists->date_of_service, '','-');    
        $created_date = Helpers::timezone(@$lists->created_at, 'm/d/y');    
        $daysSinceCreated = Helpers::daysSinceCreatedCount(date('Y-m-d',strtotime(@$lists->created_at)));

        $output_row = [@$lists->account_no, $pat_name, $dos, @$lists->claim_number, @$lists->insurance_short_name, @$lists->facility, @$lists->rendering_provider, @$lists->billing_provider, @$created_date, @$daysSinceCreated, number_format(@$lists->total_charge, 2)];    
        self::putRowInCsv($output_row);
    } else {
        $pat_name = Helpers::getNameformat(@$lists->patient->last_name,@$lists->patient->first_name,@$lists->patient->middle_name);
        $dos = Helpers::checkAndDisplayDateInInput(@$lists->date_of_service, '','-');
        $insurance_name = Helpers::getInsuranceName(@$lists->insurance_id);
        $created_date = Helpers::timezone(@$lists->created_at, 'm/d/y');    
        $daysSinceCreated = Helpers::daysSinceCreatedCount(date('Y-m-d',strtotime(@$lists->created_at)));

        $output_row = [@$lists->patient->account_no, $pat_name, $dos, @$lists->claim_number, @$insurance_name, @$lists->facility->short_name, @$lists->rendering_provider->short_name, @$lists->billing_provider->short_name, @$created_date, @$daysSinceCreated, number_format(@$lists->total_charge, 2)];
        self::putRowInCsv($output_row);
    }
  
}
    
    public function putRowInCsv (array $unbilledClaims){
        $fileHandle = fopen('php://output', 'w');
        fputcsv($fileHandle, $unbilledClaims);
    }
    
    private function addSummaryInFile(){
        $this->putRowInCsv(['']);
    }

    public function processSummary(){
            $this->putRowInCsv(['']);
            $year = date("Y");
            $this->putRowInCsv(['Copyright © '.$year.' Medcubics. All rights reserved.']);
    }

    private function closeFile()
    {
        fclose($this->fileHandle);
        exit;
    }
    
    private function headers()
    {
        $date = date('m-d-Y');
        $name = 'Charges_Payments_Summary_' .$date.'.csv';
        return [
            'Content-Type' => ' application/force-download',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
            'X-Accel-Buffering' => 'no'
        ];
    }
}
