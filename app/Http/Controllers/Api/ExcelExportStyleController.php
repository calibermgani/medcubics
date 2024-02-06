<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use App;
use DB;
use Carbon;
use Request;
use Auth;
use PHPExcel;
use Session;
use Route;

class ExcelExportStyleController extends Controller
{
    public function columnFormatSheet($controller_name, $function_name, $practice_id = '') {
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
        $styleArraybtmborder = [
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

        $styleArrayHorizontalleft = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $styleArrayHorizontalright = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ];
        $styleArrayfont9 = [
            'font' => [
                'size' => 9
            ],
        ];
        $styleArrayfontbold = [
            'font' => [
                'bold' => true
            ],
        ];
        $styleArrayRed = [
            'font' => [
                'color' => array('rgb' => '00877f')
            ],
        ];

        $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
        $conditional1->addCondition('0');
        $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKRED);
        $conditional1->getStyle()->getFont()->setBold(true);
        $conditionalStyles[] = $conditional1;

        $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
        $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
        $conditional2->addCondition('Totals');
        $conditional2->getStyle()->getFont()->setBold(true);
        $conditionalStyles1[] = $conditional2;

        // $this->spreadsheet->getActiveSheet()->getStyle('U6:U'.$formatRange.'')->getNumberFormat()->setFormatCode('#,##0.00');
        // $this->spreadsheet->getActiveSheet()->getStyle('V6:V'.$formatRange.'')->getNumberFormat()->setFormatCode('#,##0.00');
        // $this->spreadsheet->getActiveSheet()->getStyle('W6:W'.$formatRange.'')->getNumberFormat()->setFormatCode('#,##0.00');
        // $this->spreadsheet->getActiveSheet()->getStyle('X6:X'.$formatRange.'')->getNumberFormat()->setFormatCode('#,##0.00');
        // $this->spreadsheet->getActiveSheet()->getStyle('Y6:Y'.$formatRange.'')->getNumberFormat()->setFormatCode('#,##0.00');

        if($controller_name == "ReportController") {
            switch($function_name){
                case 'getAgingReportSearchExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientDemographicsExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientAddressListExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientIcdWorksheetExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientWalletHistoryExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientStatementHistoryExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'patientStatementStatusExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'refundsearchexport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 8).'')->applyFromArray($styleArrayfont9);
                break;
                case 'adjustmentSearchexport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 7).'')->applyFromArray($styleArrayfont9);
                break;
                case 'chargesearchexport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break; 
                case 'chargepaymentsearch':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();                
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A7:H'.$formatRange.'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle('B'.$formatRange.'')->applyFromArray($styleArrayfontbold);
                break;
                case 'financialSearchExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();                
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:O6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:O7')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A8:O'.($formatRange - 1).'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle('A'.($highestRow - 2).':O'.($highestRow - 2).'')->applyFromArray($styleArrayfontbold);
                break; 
                case 'proceduresearchExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break; 
                case 'paymentsearchexport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "FinancialController") {
            switch($function_name){
                case 'denialAnalysisSearchExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'agingDetailsReportExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'workbenchSearchExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;
                case 'chargecategorysearchExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A7:H'.$formatRange.'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle('A'.($highestRow - 2).':H'.($highestRow - 2).'')->applyFromArray($styleArrayfontbold);
                break;
                case 'unbilledexport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:K6')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A7:K'.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;  
                case 'endDayExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:J7')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A8:K'.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                case 'workrvusearchExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A6:L6')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A7:L'.$formatRange.'')->applyFromArray($styleArrayfont9);            
                break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "InsuranceController") {
            switch($function_name){
                case 'getInsuranceExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('F6:F'.$formatRange.'')->applyFromArray($styleArrayHorizontalleft);
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "CptController") {
            switch($function_name){
                case 'getCptFavoritesExport':
                $this->spreadsheet->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:A'.$formatRange.'')->applyFromArray($styleArrayHorizontalleft);
                $this->spreadsheet->getActiveSheet()->getStyle('C6:C'.$formatRange.'')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
                $this->spreadsheet->getActiveSheet()->getStyle('D6:D'.$formatRange.'')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PaymentController") {
            switch($function_name){
                case 'export_e_remittance':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                case 'paymentsExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "ProblemListController") {
            switch($function_name){
                case 'getWorkbenchListExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                    break;
                case 'getProblemListExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                    break;                    
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "AppointmentListController") {
            switch($function_name){
                case 'schedulerTableDataExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:I5')->applyFromArray($styleArraybtmborder);
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PatientsController") {
            switch($function_name){
                case 'getPatientExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:L'.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
            case 'archiveInsuranceExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "ClaimControllerV1") {
            switch($function_name){
                case 'ClaimsDataSearchExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                    break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PatientAppointmentController") {
            switch($function_name){
                case 'getAppointmentExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:F'.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PatientBillingController") {
            switch($function_name){
                case 'getBillingExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }elseif($controller_name == "ChargeController") {
            switch($function_name){
                case 'chargesExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        } elseif($controller_name == "PatientPaymentController") {
            switch($function_name){
                case 'getPaymentExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'B'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:M5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PerformanceController") {
            switch($function_name){
                case 'weeklyBillingReportExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $this->spreadsheet->getActiveSheet()->getStyle('A4:AP5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:AP'.$formatRange.'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle('C6:C'.$formatRange.'')->applyFromArray($styleArrayHorizontalleft);
                $this->spreadsheet->getActiveSheet()->getStyle('M6:M'.$formatRange.'')->applyFromArray($styleArrayHorizontalleft);
                $this->spreadsheet->getActiveSheet()->getStyle('AP6:AP'.$formatRange.'')->applyFromArray($styleArrayHorizontalleft);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;   
                case 'providerSummaryExport': 
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                break;  
                case 'monthendperformanceExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();                    
                break;
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "CollectionController") {
            switch($function_name){
                case 'insuranceOverPaymentSearchexport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break; 
                case 'patientInsurancePaymentSearchexport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $highestColLastRow = $this->spreadsheet->getActiveSheet()->getHighestRow('I');
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.$highestColLastRow.'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                    
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "AppointmentController") {
            switch($function_name){
                case 'appointmentanalysisExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                   
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PatientController") {
            switch($function_name){
                case 'walletBalanceSearchExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle("G7:G".$highestRow."")->getNumberFormat()->setFormatCode('#,##0.00');
                $this->spreadsheet->getActiveSheet()->getStyle("H7:H".$highestRow."")->getNumberFormat()->setFormatCode('#,##0.00');
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "FacilitylistController") {
            switch($function_name){
                case 'facilityListSummaryExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "InsurancelistController") {
            switch($function_name){
                case 'insuranceListExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.($highestRow - 2).'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "CptlistController") {
            switch($function_name){
                case 'cptListExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $highestRowLastCell = $this->spreadsheet->getActiveSheet()->getHighestRow("".$highestCol."");
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.$highestRowLastCell.'')->applyFromArray($styleArrayfont9);
                for ($i = 'B'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        }  elseif($controller_name == "ProviderlistController") {
            switch($function_name){
                case 'providerListExport': 
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $highestRowLastCell = $this->spreadsheet->getActiveSheet()->getHighestRow("".$highestCol."");
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.'6')->applyFromArray($styleArraybtmborder);
                $this->spreadsheet->getActiveSheet()->getStyle('A7:'.$highestCol.$highestRowLastCell.'')->applyFromArray($styleArrayfont9);
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PatientWalletHistoryController") {
            switch($function_name){
                case 'paymentWalletExport': 
                $highestRow = $this->spreadsheet->getActiveSheet()->getHighestRow();
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) { 
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                $this->spreadsheet->getActiveSheet()->getStyle("D6:D".($highestRow - 2)."")->getNumberFormat()->setFormatCode('#,##0.00');
                $this->spreadsheet->getActiveSheet()->getStyle("E6:E".($highestRow - 2)."")->getNumberFormat()->setFormatCode('#,##0.00');
                $this->spreadsheet->getActiveSheet()->getStyle("F6:F".($highestRow - 2)."")->getNumberFormat()->setFormatCode('#,##0.00');
                break;                  
                default:
                    \Log::info("Style applied");		
            }
        } elseif($controller_name == "PracticeManagecareController") {
            switch($function_name){
                case 'practiceManagedCareExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }elseif($controller_name == "EmployerController") {
            switch($function_name){
                case 'getEmployerExport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }elseif($controller_name == "FeescheduleController") {
            switch($function_name){
                case 'getReport':
                $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                for ($i = 'A'; $i <= $highestCol; $i++) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                }
                $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }elseif($controller_name == "ProviderSchedulerController") {
            switch($function_name){
                case 'getProviderSchedulerExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                case 'providerScheduledListExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }elseif($controller_name == "FacilitySchedulerController") {
            switch($function_name){
                case 'getFacilitySchedulerExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                case 'facilityScheduledListExport':
                    $highestCol = $this->spreadsheet->getActiveSheet()->getHighestColumn();
                    for ($i = 'A'; $i <= $highestCol; $i++) {
                        $this->spreadsheet->getActiveSheet()->getColumnDimension(''.$i.'')->setAutoSize(true);
                    }
                    $this->spreadsheet->getActiveSheet()->getStyle('A5:'.$highestCol.'5')->applyFromArray($styleArraybtmborder);
                    $formatRange = $this->spreadsheet->getActiveSheet()->getHighestRow() - 2;
                    $this->spreadsheet->getActiveSheet()->getStyle('A6:'.$highestCol.$formatRange.'')->applyFromArray($styleArrayfont9);
                break;
                default:
                    \Log::info("Style applied");
            }
        }
    }

    public function headContentWriter($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,"decode");
        $heading_name = \App\Models\Practice::getPracticeName($practice_id);
        $user = Auth::user()->name;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
        // Set cell name and merge cells
        $this->spreadsheet->setCellValue('A1', $heading_name);
        $this->spreadsheet->setCellValue('A2', 'End of the Day Totals');
        $this->spreadsheet->setCellValue('A3', 'User : '.$user.' | Created : '.$date.' ');
    }

    public function chargesearchexport() {
        @$claims = $this->result['claims'];
        @$header = $this->result['header'];
        @$column = $this->result['column'];
        @$include_cpt_option = $this->result['include_cpt_option'];
        @$sinpage_charge_amount = $this->result['sinpage_charge_amount'];
        @$sinpage_claim_arr = $this->result['sinpage_claim_arr'];
        @$sinpage_total_cpt = $this->result['sinpage_total_cpt'];
        @$status_option = $this->result['status_option'];
        @$ftdate = $this->result['ftdate'];
        @$charge_date_opt = $this->result['charge_date_opt'];
        @$tot_summary = $this->result['tot_summary'];
        @$user_names = $this->result['user_names'];
        @$createdBy = $this->result['createdBy'];
        @$practice_id = $this->result['practice_id'];
        @$page = $this->result['page'];
        @$export = $this->result['export'];
        $tableHead = ['DOS', 'Claim No', 'Acc No', 'Patient', 'Billing', 'Rendering', 'Facility', 'POS', 'Responsibility', 'Insurance Type', 'CPT', 'Units', 'Charges($)', 'Paid($)', 'Total Balance($)', 'Status', 'Entry Date', 'Reference', 'User'];
        if((in_array('include_cpt_description',$include_cpt_option)))
            array_splice($tableHead, 12, 0, 'CPT Description');
        if((in_array('include_modifiers',$include_cpt_option)))
            array_splice($tableHead, 13, 0, 'Modifiers');
        if((in_array('include_icd',$include_cpt_option)))
            array_splice($tableHead, 14, 0, 'ICD-10');            
// dd($include_cpt_option);
        $columnLetter = 'A';
        foreach ($tableHead as $columnName) {
            $this->spreadsheet->setCellValue($columnLetter.'5', $columnName);
            $columnLetter++;
        }

        if(count((array)$claims)>0) {
            $count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; 
            $claim_bal_total = $total_claim = $total_cpt =  0; $claim_units_total = 0;  $claim_cpt_total = 0;
            $i = 6;
            // dd($claims);
            foreach($claims as $claims_list) {
                $set_title = (@$claims_list->title)? @$claims_list->title.". ":'';
                $patient_name = $set_title.$claims_list->last_name .', '. $claims_list->first_name .' '. $claims_list->middle_name;

                $dos = $cpt = $cpt_description = $modifier1 = $modifier2 = $modifier3 = $modifier4 = $icd_10 = '';
                $units = $charges = $paid = $total_bal = 0;  

                $claims_list->claim_dos_list = '1$$07/26/2018$$00100$$Anesthesia for procedures on$$22$$25$$26$$47$$001.2$$1$$150.00$$110.00$$0.00';


                if(isset($claims_list->claim_dos_list) && $claims_list->claim_dos_list != '') {
                    $claim_line_item = explode("^^", $claims_list->claim_dos_list);
                        $columnLetter = 'A'; 
                    foreach($claim_line_item as $claim_line_item_val){
                        if($claim_line_item_val != ''){
                            $line_item_list = explode("$$", $claim_line_item_val);
                            $claim_cpt = $line_item_list[0];
                            if(($line_item_list[0]) != ''){
                                $dos       = $line_item_list[1];
                                $cpt       = $line_item_list[2];
                                $cpt_description = $line_item_list[3];
                                $modifier1 = $line_item_list[4];
                                $modifier2 = $line_item_list[5];
                                $modifier3 = $line_item_list[6];
                                $modifier4 = $line_item_list[7];
                                $icd_10    = $line_item_list[8];
                                $units     = $line_item_list[9]; 
                                $charges   = $line_item_list[10];
                                $paid      = $line_item_list[11];
                                $total_bal = $line_item_list[12];                                                
                            }
                        }
                        $columnLetter = 'A';
                        $this->spreadsheet->setCellValue($columnLetter.$i, $dos);
                        $this->spreadsheet->setCellValueExplicit(++$columnLetter.$i, @$claims_list->claim_number, DataType::TYPE_STRING);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->account_no);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, $patient_name);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->billProvider_short_name);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->rendProvider_short_name);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->facility_short_name);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->code." - ".@$claims_list->pos);
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, $claims_list->self_pay == "Yes" ? 'Self' : @$claims_list->insurance_short_name); 
                        if(isset($claims_list->type_name)) {
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->type_name); }
                        else {
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, '');
                        }
                        $this->spreadsheet->setCellValueExplicit(++$columnLetter.$i, @$cpt, DataType::TYPE_STRING);

                        if(in_array('include_cpt_description',$include_cpt_option)) {
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, $cpt_description);                            
                        }  
                        if(in_array('include_modifiers',$include_cpt_option)) {
                            $modifier_arr = array();
                            if ($modifier1 != '')
                                array_push($modifier_arr, $modifier1);
                            if ($modifier2 != '')
                                array_push($modifier_arr, $modifier2);
                            if ($modifier3 != '')
                                array_push($modifier_arr, $modifier3);
                            if ($modifier4 != '')
                                array_push($modifier_arr, $modifier4);
                            if (count($modifier_arr) > 0) {
                                $modifier_val = implode($modifier_arr, ',');
                            } else {
                                $modifier_val = '-Nil-';
                            }
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$modifier_val); 
                        } 
                        $exp = explode(',', $icd_10);
                        if(in_array('include_icd',$include_cpt_option)) {
                            $this->spreadsheet->setCellValue(++$columnLetter.$i, @$icd_10);     
                        }
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$units);     
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$charges);   
                        $this->spreadsheet->getStyle($columnLetter.$i)->getNumberFormat()->setFormatCode('#,##0.00');  
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$paid);     
                        $this->spreadsheet->getStyle($columnLetter.$i)->getNumberFormat()->setFormatCode('#,##0.00');
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$total_bal);     
                        $this->spreadsheet->getStyle($columnLetter.$i)->getNumberFormat()->setFormatCode('#,##0.00');
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, @$claims_list->status);     
                        if(@$claims_list->entry_date != "0000-00-00" && $claims_list->entry_date != "1970-01-01" ) {
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, App\Http\Helpers\Helpers::timezone(@$claims_list->entry_date, 'm/d/y'));     
                        } 
                        $this->spreadsheet->setCellValueExplicit(++$columnLetter.$i, @$claims_list->claim_reference, DataType::TYPE_STRING);     
                        if($claims_list->created_by != 0 && isset($user_names[@$claims_list->created_by]) ) {
                        $this->spreadsheet->setCellValue(++$columnLetter.$i, $user_names[@$claims_list->created_by]);         
                        }

                        $claim_billed_total += @$charges;
                        $claim_paid_total += $paid;
                        $claim_bal_total += $total_bal;
                        $claim_units_total += 0;
                        $claim_cpt_total += count($claim_cpt); 
                        // $columnLetter++;
                        $i++;
                    } 
                }
                $claim_billed_total = 0;
                $claim_paid_total = 0;
                $claim_bal_total = 0;
                $count++; 
            }
            
            $highestCol = $this->spreadsheet->getHighestColumn();
            $highestRow = $this->spreadsheet->getHighestRow();
            // dd($highestRow);
            $columnLetter = 'A';
            $this->spreadsheet->setCellValue('A'.($highestRow+2), 'Summary');
            $this->spreadsheet->setCellValue('A'.($highestRow+3), '');
            $this->spreadsheet->setCellValue('B'.($highestRow+3), 'Counts');
            $this->spreadsheet->setCellValue('C'.($highestRow+3), 'Value($)');

            $this->spreadsheet->setCellValue('A'.($highestRow+4), 'Total Patients');
            $this->spreadsheet->setCellValue('B'.($highestRow+4), @$tot_summary->total_patient);
            $this->spreadsheet->setCellValue('C'.($highestRow+4), @$tot_summary->total_charge);

            $this->spreadsheet->setCellValue('A'.($highestRow+5), 'Total CPT');
            $this->spreadsheet->setCellValue('B'.($highestRow+5), @$claim_cpt_total);
            $this->spreadsheet->setCellValue('C'.($highestRow+5), @$tot_summary->total_charge);

            $this->spreadsheet->setCellValue('A'.($highestRow+6), 'Total Units');
            $this->spreadsheet->setCellValue('B'.($highestRow+6), @$claim_units_total);
            $this->spreadsheet->setCellValue('C'.($highestRow+6), @$tot_summary->total_charge);

            $this->spreadsheet->setCellValue('A'.($highestRow+7), 'Total Charges');
            $this->spreadsheet->setCellValue('B'.($highestRow+7), @$tot_summary->total_claim);
            $this->spreadsheet->setCellValue('C'.($highestRow+7), @$tot_summary->total_charge);

            $this->spreadsheet->setCellValue('A'.($highestRow+8), 'Copyright '.date("Y").' Medcubics. All rights reserved.');
        }
    }
}
