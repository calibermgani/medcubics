<?php

namespace App\Http\Controllers\Api;

use View;
use DB;
use Carbon;
use Request;
use Auth;
use PHPExcel;
use Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Api\ReportApiController as ReportApiController;
use App\Http\Controllers\Reports\ReportController as ReportController;
use App\Http\Controllers\Reports\PatientController as PatientController;
use App\Http\Controllers\Reports\CollectionController as CollectionController;
use App\Http\Controllers\Reports\Financials\FinancialController as FinancialController;
use App\Http\Controllers\Reports\Appointment\AppointmentController as AppointmentController;
use App\Http\Controllers\Reports\Practicesettings\FacilitylistController as FacilitylistController;
use App\Http\Controllers\Reports\Practicesettings\CptlistController as CptlistController;
use App\Http\Controllers\Reports\Practicesettings\ProviderlistController as ProviderlistController;
use App\Http\Controllers\Reports\Practicesettings\InsurancelistController as InsurancelistController;
use App\Http\Controllers\Reports\PerformanceController as PerformanceController;
use App\Http\Controllers\Charges\ChargeController as ChargeController;
use App\Http\Controllers\Payments\PaymentController as PaymentController;
use App\Http\Controllers\IcdController as IcdController;
use App\Http\Controllers\InsuranceController as InsuranceController;
use App\Http\Controllers\CptController as CptController;
use App\Http\Controllers\EmployerController as EmployerController;
use App\Http\Controllers\FeescheduleController as FeescheduleController;
use App\Http\Controllers\ProviderSchedulerController as ProviderSchedulerController;
use App\Http\Controllers\FacilitySchedulerController as FacilitySchedulerController;
use App\Http\Controllers\PracticeManagecareController as PracticeManagecareController;
use App\Http\Controllers\Patients\ProblemListController as ProblemListController;
use App\Http\Controllers\Scheduler\AppointmentListController as AppointmentListController;
use App\Http\Controllers\Patients\PatientController as PatientsController;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Http\Controllers\Patients\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patients\BillingController as PatientBillingController;
use App\Http\Controllers\Patients\PatientPaymentController as PatientPaymentController;
use App\Http\Controllers\Patients\PatientWalletHistoryController as PatientWalletHistoryController;
use App\Http\Controllers\Reports\streamDownloadCSV\BillingReportsCSV\ChunkReadFilter as ChunkReadFilter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Helpers\Helpers as Helpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Alignment;
use App\Models\Provider as Provider;
use Response;
use Redis;
use Route;
use Minifier;
use PhpOffice\PhpSpreadsheet\Collection\Memory as Memory;
use \PhpOffice\PhpSpreadsheet\Reader\Csv;
use Log;


class SheetJSController extends ExcelExportStyleController
{
    const SIZE_CHUNK = 500;
    
    private $fileHandle;
    public $spreadsheet;
    private $response;
    private $writer;
    private $sheet;
    public $result;

    public function execute()
    {
            ini_set('max_execution_time', '900');
            ini_set('memory_limit', '-1');
            // return  new StreamedResponse(function () {
            //     $this->openFile();
                // $this->addContentHeaderInFile();
                self::generateReport();
            //     $this->closeFile();
            // },
            // http_response_code(200),  $this->headers()  
            // );

    }
    
    private function openFile()
    {
        flush();
        // $this->fileHandle = fopen('php://output', 'w');
        // $buffer = 1024*1024;
        // while (!feof($this->fileHandle))
        // {
        //     echo fread($this->fileHandle, $buffer);
        //     flush(); // this is essential for large downloads
        //     sleep(1);
        // } 
        // fclose($this->fileHandle);
    }

    public function fileContentwritrer()
    {

            $view_html = Response::view('reports/financials/yearend/listitemreportexport', compact('data'));
            $content_html = htmlspecialchars_decode($view_html->getContent());

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();

            $spreadsheet = $reader->loadFromString($content_html);

            $this->spreadsheet = $spreadsheet;

    }

    public function spreadsheetFormating($spreadsheet) {

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
        $styleArraycenter = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);  
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(30);
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayHead);
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraycenter);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArraycenter);
        $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArraycenter);

        $this->spreadsheet = $spreadsheet;
    }

    public function spreadsheetStyles() {
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

        return $styleArraybtmborder;
    }

    private function closeFile()
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        // $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->spreadsheet);
        // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
        ob_end_clean();
        $writer->save('php://output');
        // fclose($this->fileHandle);
        exit;
    }

    private function headers()
    {
        $request = Request::all();
        $date = Helpers::timezone(date("m/d/y H:i:s"), 'm/d/Y');
        $name = ''.$request['report_name'].'_' .$date.'.xlsx';
        return [
            'Content-Type' => 'application/openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
            'Cache-Control' => 'max-age=0'
            // 'X-Accel-Buffering' => 'no',
            // 'Pragma' => 'no-cache',
            // 'Expires' => 0,
            // 'no-cache' => 'true',
            // 'must-revalidate' => 'true'
        ];
    }

    public function generateReport() {
        $request = Request::all();
        $data = $request;
        $export = "xlsx";

        // dd($request);

        if($request['controller_name'] == "FinancialController") {
            $func = new FinancialController();
            switch($request['function_name']){
            case 'unbilledexport':
                $result = $func->unbilledexport($export,$data);
                break;
            case 'endDayExport':
                $result = $func->endDayExport($export,$data);
                break;                                
            case 'workrvusearchExport':
                $result = $func->workrvusearchExport($export,$data);
                break;
            case 'chargecategorysearchExport':
                $result = $func->chargecategorysearchExport($export,$data);
                break;
            case 'workbenchSearchExport':
                $result = $func->workbenchSearchExport($export,$data);
                break;
            case 'agingDetailsReportExport':
                $result = $func->agingDetailsReportExport($export,$data);
                break;
            case 'denialAnalysisSearchExport':
                $result = $func->denialAnalysisSearchExport($export,$data);
                break;                                                
            default:
            \Log::info("CRON Function Not Assigned: Controller");
            }
        } elseif ($request['controller_name'] == "ReportController") {
            $func = new ReportController();
            switch($request['function_name']){
            case 'financialSearchExport':
                $result = $func->financialSearchExport($export,$data);
                break;
            case 'paymentsearchexport':
                $result = $func->paymentsearchexport($export,$data);
                break;
            case 'refundsearchexport':
                $result = $func->refundsearchexport($export,$data);
                break;
            case 'adjustmentSearchexport':
                $result = $func->adjustmentSearchexport($export,$data);
                break;
            case 'proceduresearchExport':
                $result = $func->proceduresearchExport($export,$data);
                break;
            case 'patientIcdWorksheetExport':
                $result = $func->patientIcdWorksheetExport($export,$data);
                break;
            case 'patientStatementHistoryExport':
                $result = $func->patientStatementHistoryExport($export,$data);
                break;
            case 'patientDemographicsExport':
                $result = $func->patientDemographicsExport($export,$data);
                break;
            case 'patientAddressListExport':
                $result = $func->patientAddressListExport($export,$data);
                break;
            case 'patientWalletHistoryExport':
                $result = $func->patientWalletHistoryExport($export,$data);
                break;
            case 'patientStatementStatusExport':
                $result = $func->patientStatementStatusExport($export,$data);
                break;
            case 'getAgingReportSearchExport':
                $result = $func->getAgingReportSearchExport($export,$data);
                break;
            case 'chargesearchexport':
                $result = $func->chargesearchexport($export, $data);
                break;
            case 'chargepaymentsearch':
                $result = $func->chargepaymentsearch($export,$data);
                break;                                                                                                                                                                                                
            default:
            // \Log::info("CRON Function Not Assigned: Controller");
            }
        } elseif ($request['controller_name'] == "CollectionController") { 
            $func = new CollectionController();
            switch($request['function_name']){
            case 'insuranceOverPaymentSearchexport':
                $result = $func->insuranceOverPaymentSearchexport($export,$data);
                break;
            case 'patientInsurancePaymentSearchexport':
                $result = $func->patientInsurancePaymentSearchexport($export,$data);
                break;                                                               
            default:
            \Log::info("CRON Function Not Assigned: Controller");
            }
        } elseif($request['controller_name'] == 'AppointmentController'){
            $func = new AppointmentController();
            switch($request['function_name']){
                case 'appointmentanalysisExport':
                    $result = $func->appointmentanalysisExport($export,$data);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientController'){
            $func = new PatientController();
            switch($request['function_name']){
                case 'walletBalanceSearchExport':
                    $result = $func->walletBalanceSearchExport($export,$data);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'FacilitylistController'){
            $func = new FacilitylistController();
            switch($request['function_name']){
                case 'facilityListSummaryExport':
                    $result = $func->facilityListSummaryExport($export,$data);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'CptlistController'){
            $func = new CptlistController();
            switch($request['function_name']){
                case 'cptListExport':
                    $result = $func->cptListExport($export,$data);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'ProviderlistController'){
            $func = new ProviderlistController();
            switch($request['function_name']){
                case 'providerListExport':
                    $result = $func->providerListExport($export,$data);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PerformanceController'){
            $func = new PerformanceController();
            switch($request['function_name']){
                case 'monthendperformanceExport':
                    $result = $func->monthendperformanceExport($export,$data);
                    break;
                case 'providerSummaryExport':
                        $result = $func->providerSummaryExport($export,$data);
                        break;
                case 'weeklyBillingReportExport':
                        $result = $func->weeklyBillingReportExport();
                        break;                                            
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'InsurancelistController'){
            $func = new InsurancelistController();
            switch($request['function_name']){
                case 'insuranceListExport':
                    $result = $func->insuranceListExport($export,$data);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'ChargeController'){
            $func = new ChargeController();
            switch($request['function_name']){
                case 'chargesExport':
                    $result = $func->chargesExport($export,null);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PaymentController'){
            $func = new PaymentController();
            switch($request['function_name']){
                case 'paymentsExport':
                    $result = $func->paymentsExport($export);
                    break;
                case 'export_e_remittance':
                    $result = $func->export_e_remittance($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'IcdController'){
            $func = new IcdController();
            switch($request['function_name']){
                case 'getIcdExport':
                    $result = $func->getIcdExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'InsuranceController'){
            $func = new InsuranceController();
            switch($request['function_name']){
                case 'getInsuranceExport':
                    $result = $func->getInsuranceExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'CptController'){
            $func = new CptController();
            switch($request['function_name']){
                case 'getCptFavoritesExport':
                    $result = $func->getCptFavoritesExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PracticeManagecareController'){
            $func = new PracticeManagecareController();
            switch($request['function_name']){
                case 'practiceManagedCareExport':
                    $result = $func->practiceManagedCareExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'ProblemListController'){
            $func = new ProblemListController();
            $current_route = Route::getFacadeRoot()->current()->uri();
            if((strrpos($current_route, "my-problem-list") !== FALSE)) {
                $type = "myproblemlist";
            } else {
                $type = "problemlist";
            }
            switch($request['function_name']){
                case 'getWorkbenchListExport':
                    $result = $func->getWorkbenchListExport($type,$export);
                    break;
                case 'getProblemListExport':
                    $result = $func->getProblemListExport($request['patient_id'],$export);
                    break;                                       
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'AppointmentListController'){
            $func = new AppointmentListController();
            switch($request['function_name']){
                case 'schedulerTableDataExport':
                    $result = $func->schedulerTableDataExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientsController'){
            $func = new PatientsController();
            switch($request['function_name']){
                case 'getPatientExport':
                    $result = $func->getPatientExport($export);
                    break;
                case 'archiveInsuranceExport':
                    $result = $func->archiveInsuranceExport($request['patient_id'], $export);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'ClaimControllerV1'){
            $func = new ClaimControllerV1();
            $current_route = Route::getFacadeRoot()->current()->uri();
            $route_segments = explode('/', $current_route);
            $last_segment = end($route_segments);
            if($last_segment == 'electronic') {
                $type = $last_segment;
            } elseif($last_segment == 'paper') {
                $type = $last_segment;
            } elseif($last_segment == 'error') {
                $type = $last_segment;
            } elseif($last_segment == 'submitted') {
                $type = $last_segment;
            } elseif($last_segment == 'rejected') {
                $type = $last_segment;
            }    
            switch($request['function_name']){
                case 'ClaimsDataSearchExport':
                    $result = $func->ClaimsDataSearchExport($type,$export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientAppointmentController'){
            $func = new PatientAppointmentController();
            switch($request['function_name']){
                case 'getAppointmentExport':
                    $result = $func->getAppointmentExport($request['patient_id'], $export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientBillingController'){
            $func = new PatientBillingController();
            switch($request['function_name']){
                case 'getBillingExport':
                    $result = $func->getBillingExport($request['patient_id'], $export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientPaymentController'){
            $func = new PatientPaymentController();
            switch($request['function_name']){
                case 'getPaymentExport':
                    $result = $func->getPaymentExport($request['patient_id'], $request['tab'], $request['export'], $export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        } elseif($request['controller_name'] == 'PatientWalletHistoryController'){
            $func = new PatientWalletHistoryController();
            switch($request['function_name']){
                case 'paymentWalletExport':
                    $result = $func->paymentWalletExport($request['patient_id'], $export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        }elseif($request['controller_name'] == 'EmployerController'){
            $func = new EmployerController();
            switch($request['function_name']){
                case 'getEmployerExport':
                    $result = $func->getEmployerExport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        }elseif($request['controller_name'] == 'FeescheduleController'){
            $func = new FeescheduleController();
            switch($request['function_name']){
                case 'getReport':
                    $result = $func->getReport($export);
                    break;                   
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        }elseif($request['controller_name'] == 'ProviderSchedulerController'){
            $func = new ProviderSchedulerController();
            switch($request['function_name']){
                case 'getProviderSchedulerExport':
                    $result = $func->getProviderSchedulerExport($export);
                    break;
                case 'providerScheduledListExport':
                    $result = $func->providerScheduledListExport($request['provider_id'],$export);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        }elseif($request['controller_name'] == 'FacilitySchedulerController'){
            $func = new FacilitySchedulerController();
            switch($request['function_name']){
                case 'getFacilitySchedulerExport':
                    $result = $func->getFacilitySchedulerExport($export);
                    break;
                case 'facilityScheduledListExport':
                    $result = $func->facilityScheduledListExport($request['facility_id'],$export);
                    break;
                default:
                    \Log::info("CRON Function Not Assigned: Controller");		
            }
        }
       $test = ['test','testse','tsdg'];
    //    dd($result);  
       print_r(json_encode($result));die;
        return Response::json(array('status'=>'success', 'message'=>"test",'data'=>$test));die;
        return response()->json([
            'data' => $result
        ]);
        exit;
        // dd($result);
        \PhpOffice\PhpSpreadsheet\Settings::setCache(new Memory());
        // dd($result);
        // $view_html = Response::view(''.$result['file_path'].'', compact('result'));
        $view_html = view(''.$result['file_path'].'', ['result' => $result])->render();
        libxml_use_internal_errors(true);
        $content_html = htmlspecialchars_decode($view_html);   
        // dd($content_html) ;
        // dd("sdfds");
        // dd($content_html);
        // ob_start();
        // $searchdata = array(
        //     '/\>[^\S ]+/s', // remove whitespaces after tags
        //     '/[^\S ]+\</s', // remove whitespaces before tags
        //     '/(\s)+/s' // remove multiple whitespace sequences
        // );
        // $replacedata = array('>','<','\\1');
        // $content_html = preg_replace($searchdata, $replacedata, $content_html);
        // ob_end_clean();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Html');
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->loadFromString($content_html);  
        // $this->result = $result;    
        // $spreadsheet = new Spreadsheet();

        $this->spreadsheet = $spreadsheet->getActiveSheet(); //;
        //  $this->headContentWriter($result['practice_id']);
        //  $this->chargesearchexport(); 
         $this->columnFormatSheet($request['controller_name'], $request['function_name'], $result['practice_id']);
        SELF::spreadsheetFormating($spreadsheet); 
    }


}

