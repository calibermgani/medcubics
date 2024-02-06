<?php

namespace App\Http\Controllers\Reports\streamDownloadCSV\BillingReportsCSV;

use DB;
use Carbon;
use Request;
use Auth;
use PHPExcel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;

class ChargePaymentSummaryController extends Controller
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
    }
 
    private function addContentHeaderInFile()
    {
        $request = Request::all();
        // $request['export'] = 'xlsx';
        $header = ['Billing Provider','Total Charges($)','Patient Adjustments($)', 'Insurance Adjustments($)', 'Total Adjustments($)', 'Patient Payments($)', 'Insurance Payments($)', 'Total Payments($)'];
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

        $reportApi = new ReportApiController;

        $request = Request::all();
        if(!isset($request['insurance_type']))
            $request['insurance_type'] = 'all';
        // Charges Summary Start
            $api_response = $reportApi->getChargeResult($request);
            $charge_summary = $api_response['claims']->selectRaw('billing_provider_id,sum(total_charge) as total_charge')->where('billing_provider_id','<>',0)
                            ->groupby('billing_provider_id')->orderBy('id','asc')->get();
            $header = $api_response['header'];
            $column = $api_response['column'];
            // Charges Separate into provider wise
            if(count($charge_summary)>0)
                foreach($charge_summary as $charge){
                    $provider_name = str_replace(',','', @$charge->billing_provider->provider_name);
                    $provider_name = str_replace(' ','_',($provider_name));
                    $charges[$provider_name] = $charge->total_charge;
                }
            else
                $charges = [];
        // Payments Summary Start
            if(!isset($request['insurance_charge']))
                $request['insurance_charge'] = 'all';
            $api_response_insurance = $reportApi->getPaymentResult($request);
            $api_response_data=$api_response_insurance['payments']->orderBy('created_at','desc')->get();
            $payment = [];
            // Payments Separate into provider wise for insurance only
            if($api_response_data)
                foreach($api_response_data as $pmt){
                    $provider_name = str_replace(',','',$pmt['claim']['billing_provider']['provider_name']);
                    $provider_name = str_replace(' ','_',($provider_name));
                    $payment[$provider_name]['billing_provider_id'] = $pmt['claim']['billing_provider_id'];
                    if($pmt['pmt_method']=="Insurance" && $pmt['pmt_type']=="Payment")
                        $payment[$provider_name][$pmt['pmt_method']][] = $pmt['total_paid'];
                }
            $request['insurance_charge'] = 'all';
            $api_response_patient = $reportApi->getPaymentResult($request);
            $exp = isset($request['select_transaction_date']) ? explode("-",$request['select_transaction_date']) : "";
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && $exp != "" && isset($exp[0])) {
                    $start_date = ($exp != "") ? $exp[0] : "";
                    $start_date = \App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);  
                } else {
                    $start_date = "";
                }
            }

            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date']) && $exp != "" && isset($exp[1])) {
                    $end_date = ($exp != "") ? $exp[1] : "";
                    $end_date = \App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
                } else {
                    $end_date = "";
                }            
            }

            if(isset($request['billing_provider_id'])) {
                $api_response_data=\DB::table('pmt_info_v1')->selectRaw('pmt_info_v1.amt_used as pmt_amt,providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.payment_id','=','pmt_info_v1.id')->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')->where('pmt_info_v1.pmt_method','Patient');
                if(isset($request['export'])){
                    $api_response_data->whereIn("billing_provider_id", explode(',', $request["billing_provider_id"]));
                } else {
                    $api_response_data->whereIn("billing_provider_id", $request["billing_provider_id"]);
				}
                $api_response_data->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance']);
            }
            else
                $api_response_data=\DB::table('pmt_info_v1')->selectRaw('pmt_info_v1.amt_used as pmt_amt,providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.payment_id','=','pmt_info_v1.id')->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')->where('pmt_info_v1.pmt_method','Patient')->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance']);
            
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && $start_date != "" && $end_date != "")
                    $api_response_data->whereRaw("DATE(pmt_info_v1.created_at) >= '$start_date' and DATE(pmt_info_v1.created_at) <= '$end_date'");
            }

            $api_response_data = $api_response_data->whereNull('pmt_info_v1.void_check')->whereNull('pmt_info_v1.deleted_at')->groupby('pmt_info_v1.id')->get();
            // Payments Separate into provider wise for patient only
            if($api_response_data)
                foreach($api_response_data as $pmt){
                    if(isset($pmt->provider_name)){
                        $provider_name = str_replace(',','',$pmt->provider_name);
                        $provider_name = str_replace(' ','_',$provider_name);
                    }
                    if(isset($pmt->provider_name)){
                        $payment[$provider_name]['billing_provider_id'] = $pmt->billing_provider_id;
                        $payment[$provider_name][$pmt->pmt_method][] = $pmt->pmt_amt;
                    } else {
                            $payment['wallet'][] = $pmt->pmt_amt;
                    }
                }
            // Payments wallet only
            $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt))-(sum(amt_used)) as pmt_amt')
            ->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at');
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            $wallet->whereRaw("created_at >= '$start_date' and created_at <= '$end_date'");
            $wallet = $wallet->get();
            if($wallet[0]->pmt_amt!=null)
                $payment['wallet'][] = $wallet[0]->pmt_amt;
            // Payment calculation into provider wise for patient and insurance
            if($payment)
            foreach ($payment as $key=>$item) {
                $payments[$key]['Patient'] = isset($item['Patient'])?array_sum($item['Patient']):0;
                $payments[$key]['Insurance'] = isset($item['Insurance'])?array_sum($item['Insurance']):0;
                $payments[$key]['billing_provider_id'] = isset($item['billing_provider_id'])?$item['billing_provider_id']:'';
                if($key=='wallet')
                    $payments[$key] = array_sum($item);
            }
            else
                $payments = [];
        // Adjustment Summary Start
            if(!isset($request['insurance_charge'])){
                $request['insurance_type'] = 'all';
                $request['insurance_charge'] = 'all';
            }
            if(isset($request['select_transaction_date']))
                $request['created_at'] = $request['select_transaction_date'];
            
            $api_response = $reportApi->getAdjustmentResult($request);
            $api_response_data=$api_response['adjustment']->orderBy('created_at','desc')->get();
            $ins_adj = $pat_adj = 0;
            // Adjustment Separate into provider wise
            if(isset($api_response_data))
                foreach($api_response_data as $adj){
                    if($adj->claim->billing_provider != "") {
                        $provider_name = str_replace(',','',$adj->claim->billing_provider->provider_name);
                    }
                    $provider_name = str_replace(' ','_',($provider_name));
                    if($adj->pmt_method=="Insurance")
                        $adjustments[$provider_name][$adj->pmt_method][] = $adj->total_withheld+$adj->total_writeoff;
                    else
                        $adjustments[$provider_name][$adj->pmt_method][] = $adj->total_withheld+$adj->total_writeoff;
                }

            // Adjustment calculation into provider wise
            if(isset($adjustments))
            foreach ($adjustments as $key=>$item) {
                $pmt_adj[$key]['Patient'] = isset($item['Patient'])?array_sum($item['Patient']):0;
                $pmt_adj[$key]['Insurance'] = isset($item['Insurance'])?array_sum($item['Insurance']):0;
            }
            else
                $pmt_adj = [];
        if(!isset($request['billing_provider_id']))
            $header['Billing Provider'] ='All';
        unset($header['Payer']);
        $createdBy = isset($data['created_user']) ? $data['created_user']: '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $billingprov = \DB::table('providers')->leftJoin('claim_info_v1','claim_info_v1.billing_provider_id','=','providers.id')->selectRaw('providers.id,concat(providers.provider_name) as provider_name')->where('providers.provider_types_id','=', 5)->groupby('providers.id');
        if(isset($request['billing_provider_id'])){
            if(is_array($request['billing_provider_id'])){
                $billingprov->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            }
            else {
                if(isset($request['export']))
                    $billingprov->whereIn('claim_info_v1.billing_provider_id',explode(',',$request['billing_provider_id']));
                else
                    $billingprov->where('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            }
        }
        $get_function_value = $billingprov->orderBy('providers.id');
        $chargePayment = $billingprov->get()->toArray();
        $billingprov = $billingprov->get();
        $billingprov_count = count($billingprov)+9;
        $billingprov_count_b = "B".$billingprov_count;

        $data = [];
        $data['billingprov_count_b'] = $billingprov_count_b;
        $data['header'] = $header;
        $data['column'] = $column;
        $data['billingprov'] = $billingprov;

        $this->global_charges = $charges;
        $this->global_payments = $payments;
        $this->global_pmt_adj = $pmt_adj;

        $data['createdBy'] = $createdBy;
        $data['practice_id'] = $practice_id;
        $data['export'] = $export;

        if((isset($charges) && count($charges) > 0) || (isset($payments) && count($payments) > 0) || (isset($pmt_adj) && count($pmt_adj) > 0)) {
            $get_function_value->chunk(self::SIZE_CHUNK, function ($chargePayment) {
            if(isset($chargePayment) && !empty($chargePayment)) {
                foreach ($chargePayment as $data_list) {
                    self::addUserLine($data_list);
                }
            }
        });
        }
        $this->addSummaryInFile();
        $this->processSummary();
    }

public function addUserLine($data_list)
{
        $provider_name = str_replace(',','',$data_list->provider_name);
        $key = str_replace(' ','_',($provider_name));
        $billed = isset($this->global_charges[$key])?$this->global_charges[$key]:0;
        $pat_adj = isset($this->global_pmt_adj[$key]['Patient'])?$this->global_pmt_adj[$key]['Patient']:0;
        $ins_adj = isset($this->global_pmt_adj[$key]['Insurance'])?$this->global_pmt_adj[$key]['Insurance']:0;
        $pat_pmt = isset($this->global_payments[$key]['Patient'])?$this->global_payments[$key]['Patient']:0;
        $ins_pmt = isset($this->global_payments[$key]['Insurance'])?$this->global_payments[$key]['Insurance']:0;
        $tot_adj = $pat_adj+$ins_adj;
        $tot_pmt = $pat_pmt+$ins_pmt;
        $output_row = [str_replace('_', ' ', $key),number_format($billed), number_format($pat_adj), number_format($ins_adj),number_format( $tot_adj), number_format($pat_pmt), number_format($ins_pmt), number_format($tot_pmt)];
        if ($billed || $pat_adj || $ins_adj || $pat_pmt || $ins_pmt!=0) {
            self::putRowInCsv($output_row);
        }

    $this->patient_total_payment += $pat_pmt;
    $this->insurance_total_payment += $ins_pmt;
    $this->total_billed += $billed;
    $this->patient_total_adj += $pat_adj;
    $this->insurance_total_adj += $ins_adj;
    $this->count++;
  
}
    
    public function putRowInCsv (array $chargePayment){
        $fileHandle = fopen('php://output', 'w');
        fputcsv($fileHandle, $chargePayment);
    }
    
    private function addSummaryInFile(){
        $this->putRowInCsv(['']);
        $this->putRowInCsv(['Totals', number_format(array_sum((array)$this->global_charges)), number_format($this->patient_total_adj), number_format($this->insurance_total_adj), number_format($this->patient_total_adj+$this->insurance_total_adj), number_format($this->patient_total_payment), number_format($this->insurance_total_payment), number_format($this->patient_total_payment+$this->insurance_total_payment) ]);
    }

    public function processSummary(){
        $wallet = isset($this->global_payments['wallet'])?$this->global_payments['wallet']:0;
        if($wallet<0)
            $wallet = 0;

            $this->putRowInCsv(['']);
            $this->putRowInCsv(['Wallet Balance', number_format($wallet)]);
            $this->putRowInCsv(['']);
            $this->putRowInCsv(['Copyright © 2019 Medcubics. All rights reserved.']);
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
            'Content-Type' => ' application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ];
    }
}
