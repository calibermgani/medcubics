<?php

namespace App\Http\Controllers\Reports\streamDownloadCSV\BillingReportsCSV;

use DB;
use Carbon;
use Request;
use Auth;
use PHPExcel;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use Symfony\Component\HttpFoundation\StreamedResponse;
// use Illuminate\Http\Response;
use App\Http\Helpers\Helpers as Helpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Alignment;
use App\Models\Provider as Provider;
use Response;

class YearEndFinancialsController extends Controller
{
    const SIZE_CHUNK = 500;
    
    private $fileHandle;
    private $spreadsheet;
    private $response;
    private $writer;
    private $sheet;

    public function execute()
    {
        $this->response =  new StreamedResponse(function () {
            $this->openFile();
            // $this->addContentHeaderInFile();
            self::fileContentwritrer();
            $this->closeFile();
        },
        http_response_code(200), $this->headers()  
    );
        return $this->response;
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
        $spreadsheet = new Spreadsheet();
        // Get active sheet - it is also possible to retrieve a specific sheet
        $sheet = $spreadsheet->getActiveSheet();
    
        $styleArrayHead = [
            'font' => [
                'bold' => true,
                'size' => 13.5,
                'color' => array('rgb' => '00877f')
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $styleArraycenter = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $styleArrayallbordeer = [
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'inside' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $styleArrayallbordeer = [
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];


        $request = Request::all();
        $practice_id =Session::get('practice_dbid');
        $heading_name = \App\Models\Practice::getPracticeName($practice_id);
        $user = Auth::user()->short_name;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
        // Set cell name and merge cells
        $sheet->setCellValue('A1', $heading_name)->mergeCells('A1:O1');
        $sheet->setCellValue('A2', 'Year End Financials')->mergeCells('A2:O2');
        $sheet->setCellValue('A3', 'User : '.$user.' | Created : '.$date.' ')->mergeCells('A3:O3');

        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
        // $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18.5);
        // $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        // $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12); 
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);  
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(30);

        $sheet->getStyle('A1')->applyFromArray($styleArrayHead);
        $sheet->getStyle('A2')->applyFromArray($styleArraycenter);
        $sheet->getStyle('A3')->applyFromArray($styleArraycenter);
        $sheet->getStyle('A4')->applyFromArray($styleArraycenter);
        $sheet->getStyle('A6:J6')->applyFromArray($styleArray);
        $sheet->getStyle('A7:J7')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getStyle('B:o')->getNumberFormat()->setFormatCode('#,##0.00');
    
        // Set column names
        $head = ['Adjustments($)', 'Refund($)', 'Payments($)'];
        $i = 0;
        foreach ($head as $colName) {
            if ($i == 0) {
                $sheet->setCellValue('D6', $colName)->mergeCells('D6:E6');
            } elseif ($i == 1) {
                $sheet->setCellValue('F6', $colName)->mergeCells('F6:G6');
            } else {
                $sheet->setCellValue('H6', $colName)->mergeCells('H6:I6');
            }
        $i++;
        }

        $header = ['Date-Day','Charges($)','Write-off($)', 'Insurance', 'Patient', 'Insurance', 'Patient', 'Insurance', 'Patient', 'Total Payments($)'];
        $columnLetter = 'A';
        foreach ($header as $columnName) {
            if($columnLetter == "D" || $columnLetter == "E" || $columnLetter == "F" || $columnLetter == "G" || $columnLetter == "H" || $columnLetter == "I") {  
                $sheet->setCellValue($columnLetter.'7', $columnName);
            } else {
                $sheet->setCellValue($columnLetter.'6', $columnName)->mergeCells("".$columnLetter."6:".$columnLetter."7");
            }
            $columnLetter++;

        }

        $this->spreadsheet = $spreadsheet;
        $this->sheet = $sheet;

        SELF::fileContentwritrer($sheet);

    }

    public function fileContentwritrer()
    {

        $columnValues = SELF::processdata();
        $data = $columnValues;

            $view_html = Response::view('reports/financials/yearend/listitemreportexport', compact('data'));
            $content_html = htmlspecialchars_decode($view_html->getContent());

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();

            $spreadsheet = $reader->loadFromString($content_html);

            $styleArrayHead = [
                'font' => [
                    'bold' => true,
                    'size' => 13.5,
                    'color' => array('rgb' => '00877f')
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
            $styleArray = [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'inside' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            // $reader->getStyle('A1')->applyFromArray($styleArrayHead);
            // $sheet->getStyle('A6:J6')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A6:O6')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A7:O7')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A20:O20')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);  
            $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(30);
            $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayHead);

            $spreadsheet->getActiveSheet()->getStyle('B:O')->getNumberFormat()->setFormatCode('#,##0.00');

                $this->spreadsheet = $spreadsheet;


        // $i = 8; // Beginning row for active sheet
        // foreach ($columnValues as $columnValue) {
        //     // isset($dates->total_charge) ? $dates->total_charge : 0;
        //     $columnLetter = 'A';
        //     foreach ($columnValue as $key => $value) {
        //         if($key == "created_at") {
        //             $value = $value.'-'.date('D', strtotime($value));
        //         } elseif($key == "total_charge") {
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "writeoff_total") {
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "insurance_adjustment") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "insurance_adjustment") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "patient_adjustment") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "insurance_refund") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "patient_refund") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "insurance_payment") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "patient_payment") { 
        //             $value = isset($value) ? $value : 0;
        //         } elseif ($key == "total_payment") { 
        //             $value = isset($value) ? $value : 0;
        //         } 
        //         $sheet->setCellValue($columnLetter.$i, $value);
        //         $columnLetter++;
        //     }
        //     $i++;
        // }

        // $i = $i + 1;
        // $sheet->setCellValue("A".$i, "Copyright © 2019 Medcubics. All rights reserved.")->mergeCells('A'.$i.':J'.$i.'');

    }
    
    public function processdata($export = '', $data = '')
    {

        $request = Request::all();
        $data = $request;
        $export = "csv";
        $reportsApi = new ReportApiController;
        
        $api_response = $reportsApi->getFinancialSearchApi($export, $data);
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $search_by = $api_response_data->data->search_by;
        $claims_count = 20;
        $claims_count_co = "C".$claims_count.":"."O".$claims_count;
        $date = date('m-d-Y');

        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $data['claims_count_co'] = $claims_count_co;
        $data['claims'] = $claims;
        $data['createdBy'] = $createdBy;
        $data['practice_id'] = $practice_id;
        $data['export'] = $export;
        $data['search_by'] = $search_by;

        // $i = 0; $string = "";
        // foreach($search_by as $key => $val) {
        // if($i > 0) {
        //     // $this->sheet->setCellValue("A4", $value);
        //     $string .= " | ";
        // }
        // $string .= $key." : ".$val[0];
        // // <span>{!! $key !!} : </span>{{ @$val[0] }}                           
        // $i++;
        // }
        // $this->sheet->setCellValue("A4", $string)->mergeCells("A4:J4");

        return $data;
            // $get_function_value->chunk(self::SIZE_CHUNK, function ($unbilledClaims) {
                if(count($unbilledClaims) > 0) {
                foreach ($unbilledClaims as $lists) {
                    self::addUserLine($lists);
                }
                }
            // });
        // $this->addSummaryInFile();
        // $this->processSummary();
    }
    
    public function bladeConditions($spreadsheet){

    }

    private function closeFile()
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xls');
        // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($this->spreadsheet);
        ob_end_clean();
        $writer->save('php://output');
        fclose($this->fileHandle);
        exit;
    }
    
    private function addSummaryInFile(){
        $this->putRowInCsv(['']);
    }

    public function processSummary(){
            $this->putRowInCsv(['']);
            $year = date("Y");
            $this->putRowInCsv(['Copyright © '.$year.' Medcubics. All rights reserved.']);
    }

    private function headers()
    {
        $date = date('m-d-Y');
        $name = 'Year-End-Financials' .$date.'.xls';
        return [
            'Content-Type' => 'application/openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'max-age=0',
            'no-cache' => 'true',
            'must-revalidate' => 'true'
        ];
    }
}