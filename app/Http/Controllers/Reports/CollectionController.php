<?php 
namespace App\Http\Controllers\Reports;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Request;
use Redirect;
use DB;
use View;
use Config;
use PDF;
use Excel;
use Session;
use Response;
use Url;
use Auth;
use Log;
use Carbon\Carbon;
use App\Http\Controllers\Medcubics\Api\DBConnectionController;
use App\Models\Medcubics\Users;
use Illuminate\Pagination\Paginator;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Claims\ClaimControllerV1;
use App\Models\ReportExport as ReportExportTask;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Insurance;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\BladeExport;

class CollectionController extends Controller {

    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'barchart');
        $new = new DBConnectionController();
        View::share('checkpermission', $new); 
        $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : 0;
        $new->connectPracticeDB($practice_id);
    }

    /**
     * Display a listing of the insurance over payment.
     *
     * @return Response
     */
    public function insuranceOverpaymentList()
    {
        // Get filter option from db
        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('insurance_over_payment');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        // Define to selected tab to view in reports
        $selected_tab = "collection-report";
        // Define to heading in left sidebar
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : 0;
        return view('reports/collections/insurance_overpayment/list', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData', 'practice_id'));
    }

    /**
     * Filter apply for insurance over payment.
     *
     * @return Response
     */
    public function insuranceOverpaymentSearch($export = '',$data = ''){
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            return $this->insuranceOverpaymentSearchSP($export, $data); // Store procedure
        }
        // Get request
        if(!empty($data))
            $request = $data;
        else
            $request = Request::all();
        if(isset($data['practice_id'])){
            $practice_id = $data['practice_id'];
            $new = new DBConnectionController();
            $new->connectPracticeDB($practice_id);
        }
        $practice_timezone = Helpers::getPracticeTimeZone();
        // Initialize filter parameters to view
        $header = [];

        // Over Payment Query
        // Table claim_info_v1 with left join pmt_claim_fin_v1, pmt_claim_cpt_fin_v1, providers, facilities and patients
        $overpayments = ClaimInfoV1::selectRaw('claim_info_v1.claim_number, DATE_FORMAT(claim_info_v1.date_of_service,"%m/%d/%Y") as dos, claim_info_v1.total_charge, claim_info_v1.billing_provider_id, claim_info_v1.facility_id, claim_info_v1.patient_id, CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'") as date, claim_info_v1.created_at as created_date, providers.short_name as provider_short_name, providers.provider_name as provider_name ,f.short_name as facility_short_name, f.facility_name as facility_name, patient.account_no, patient.first_name, patient.last_name, patient.middle_name, (pmt.withheld+pmt.insurance_adj+pmt.patient_adj) as adjustment, pmt.insurance_paid+pmt.patient_paid as insurance_paid, (case when claim_info_v1.insurance_id != 0 then pmt.insurance_due-(pmt.patient_paid+pmt.patient_adj) else pmt.insurance_due+pmt.patient_due end ) as ar_due')
            ->leftJoin('pmt_claim_fin_v1 AS pmt', 'pmt.claim_id', '=', 'claim_info_v1.id')
            ->leftJoin('pmt_claim_cpt_fin_v1 AS pmt_cpt', 'pmt_cpt.claim_id', '=', 'claim_info_v1.id')
            ->leftJoin('providers AS providers', 'providers.id', '=', 'claim_info_v1.billing_provider_id')
            ->leftJoin('facilities AS f', 'f.id', '=', 'claim_info_v1.facility_id')
            ->leftJoin('patients AS patient', 'patient.id', '=', 'claim_info_v1.patient_id')
            ->whereRaw('pmt_cpt.insurance_balance < 0')->orderBy('claim_info_v1.id','desc');

        // Filter by Transaction Date
        // if(isset($request['choose_date']) && !empty($request['choose_date']) && 
  //           ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $exp = explode("-",$request['select_transaction_date']);          
            $start_date = date("Y-m-d", strtotime($exp[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }           
            $end_date = date("Y-m-d", strtotime($exp[1]));  
            $header['Transaction Date'] = date("m/d/y",strtotime($start_date)) . " To " . date("m/d/y",strtotime($end_date));

        //  $start_date = Helpers::utcTimezoneStartDate($start_date);
        //  $end_date = Helpers::utcTimezoneEndDate($end_date);
        //  $overpayments->whereRaw("(claim_info_v1.created_at) >= '$start_date' and (claim_info_v1.created_at) <= '$end_date'");
            $overpayments->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");   
        }

        // Filter by DOS
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $start_date = $exp[0];
            $end_date = $exp[1];
            $start_date = Helpers::dateFormat($start_date, 'datedb');
            $end_date = Helpers::dateFormat($end_date, 'datedb');
            $overpayments->whereRaw("DATE(claim_info_v1.date_of_service) >= '$start_date' and DATE(claim_info_v1.date_of_service) <= '$end_date'");  
            $header['DOS'] = date("m/d/Y",strtotime($start_date)) . "  To " . date("m/d/Y",strtotime($end_date));
        }
        
        // Filter by Billing provider
        if(isset($request['billing_provider_id']) && !empty($request['billing_provider_id'])){
            if(isset($request['export']) || is_string($request["billing_provider_id"])){
                $overpayments->whereIn("claim_info_v1.billing_provider_id", explode(',', $request["billing_provider_id"]));
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', explode(',', $request["billing_provider_id"]))->get()->toArray();                
            } else {
                if(is_array($request['billing_provider_id']) && array_sum($request['billing_provider_id'])!=0 )   {
                    if(count($request['billing_provider_id'])==1){
                        $request['billing_provider_id'] = explode(',', $request['billing_provider_id'][0]);
                    }
                    $overpayments->whereIn("claim_info_v1.billing_provider_id", $request["billing_provider_id"]);
                    $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $request['billing_provider_id'])->get()->toArray();
                }
            }
            $header['Billing Provider'] = @array_flatten($provider)[0];            
        }

        // Filter by facility
        if(isset($request['facility']) && !empty($request['facility'])){
            if(isset($request['export']) || is_string($request["facility"])){
                $overpayments->whereIn("claim_info_v1.facility_id", explode(',', $request["facility"]));
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request["facility"]))->get()->toArray();
            } else {
                if(is_array($request['facility']) && array_sum($request['facility'])!=0 )   {
                    if(count($request['facility'])==1){
                        $request['facility'] = explode(',', $request['facility'][0]);
                    }
                    $overpayments->whereIn("claim_info_v1.facility_id", $request["facility"]);
                    $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', $request['facility'])->get()->toArray();
                }
            }
            $header['Facility'] = @array_flatten($facility)[0];            
        }

        // To check export or view
        if(isset($request['exports']) && $request['exports'] == 'pdf'){
            $overpayment = $overpayments->get();
            return compact('overpayment','header');
        } elseif (isset($request['export']) && $request['export'] == 'xlsx') {
            $overpayment = $overpayments->get();
            return compact('overpayment','header');
        }else {
            $pagination = '';
            // Define for pagination count for per page
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            
            // Get records per page
            $overpayment = $overpayments->paginate($paginate_count);
            $overpayment_pagination = $overpayment->toArray();
            
            // Pagination navigation
            $pagination_prt = $overpayment->render();
            
            // Default pagination if single page
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            // To set pagination datas
            $pagination = array('total' => $overpayment_pagination['total'], 'per_page' => $overpayment_pagination['per_page'], 'current_page' => $overpayment_pagination['current_page'], 'last_page' => $overpayment_pagination['last_page'], 'from' => $overpayment_pagination['from'], 'to' => $overpayment_pagination['to'], 'pagination_prt' => $pagination_prt);
            
            // Separate data only
            $overpayment = $overpayment;
               
            return view('reports/collections/insurance_overpayment/report', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData','overpayment','header','pagination'));
        }
    }
        
    /* Stored procedure for insurance over payments - Anjukaselvan */
    public function insuranceOverpaymentSearchSP($export = '', $data = '') {
        // Get request
        if (!empty($data))
            $request = $data;
        else
            $request = Request::all();
        if (isset($data['practice_id'])) {
            $practice_id = $data['practice_id'];
            $new = new DBConnectionController();
            $new->connectPracticeDB($practice_id);
        }
        $practice_timezone = Helpers::getPracticeTimeZone();
        // Initialize filter parameters to view
        $header = [];
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $facility_id = '';

        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $exp = explode("-",$request['select_transaction_date']);
            $start_date = date("Y-m-d", strtotime($exp[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }           
            $end_date = date("Y-m-d", strtotime($exp[1]));
            $header['Transaction Date'] = date("m/d/y", strtotime($start_date)) . "  To " . date("m/d/y", strtotime($end_date));
        }
        
        // Filter by DOS
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $dos_start_date = $exp[0];
            $dos_end_date = $exp[1];
            $dos_start_date = Helpers::dateFormat($dos_start_date, 'datedb');
            $dos_end_date = Helpers::dateFormat($dos_end_date, 'datedb');
            $header['DOS'] = date("m/d/Y",strtotime($dos_start_date)) . "  To " . date("m/d/Y",strtotime($dos_end_date));
        }
        // Filter by Billing provider
        if (isset($request['billing_provider_id']) && !empty($request['billing_provider_id'])) {
            $billing_provider_id = $request['billing_provider_id'];
            $explode_billing = explode(',', $request['billing_provider_id']);
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $explode_billing)->get()->toArray();
            $header['Billing Provider'] = @array_flatten($provider)[0];
        }

        // Filter by facility
        if (isset($request['facility']) && !empty($request['facility'])) {
            $facility_id = $request['facility'];
            $explode_facility = explode(',', $request['facility']);
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', $explode_facility)->get()->toArray();
            $header['Facility'] = @array_flatten($facility)[0];
        }
        
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
        $offset = 0;
        $page = 0;

        if (isset($request['page'])) {
            $page = $request['page'];
            $offset = ($page - 1) * $paginate_count;
            $from = $offset + 1;
            $to = $offset + $paginate_count;
        } else {
            $from = 1;
            $to = $paginate_count;
        }
        if ($export == "") {

            $recCount = 1;
            $sp_return_result = DB::select('call insuranceOverPayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $billing_provider_id . '", "' . $facility_id . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->insuranceOverPayment_count;
            $last_page = 0;
            if ($count != 0)
                $last_page = ceil($count / $paginate_count);
            if (isset($request['page'])) {
                $page = $request['page'];
                $offset = ($page - 1) * $paginate_count;
                $from = $offset + 1;
                $to = $offset + $paginate_count;
                if ($page == $last_page) {
                    $to = $offset + $count % $paginate_count;
                }
            } else {
                if($paginate_count > $count){
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }
            $recCount = 0;
            $sp_return_result = DB::select('call insuranceOverPayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $billing_provider_id . '", "' . $facility_id . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array)$sp_return_result;

            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }

            $report_array = $this->paginate($sp_return_result)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);
            
            $overpayment = $sp_return_result;
            return view('reports/collections/insurance_overpayment/report', compact('overpayment', 'header', 'pagination'));

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call insuranceOverPayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $billing_provider_id . '", "' . $facility_id . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $overpayment = $sp_return_result;
            return compact('overpayment', 'header');
        }
    }

    // -------------------------------------- Start - Export insurance over payment ------------------------------------
    public function insuranceOverPaymentSearchexport($export = '',$data = ''){
        // Send request and get data
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $overpayments = $this->insuranceOverpaymentSearchSP($export, $data); // Stored procedure
        } else {
            $overpayments = $this->insuranceOverpaymentSearch($export, $data); // DB
        }

        $overpayment = $overpayments['overpayment'];
        $header = $overpayments['header'];
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Insurance_Over_Payment_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        // Load and save PDF Format
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/collections/insurance_overpayment/report_export_pdf';
            $report_name = "Insurance Over Payment";
            $data = ['overpayment' => $overpayment, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'header' => $header];
            return $data;
        } 

        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/collections/insurance_overpayment/report_export';
            $data['overpayment'] = $overpayment;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['header'] = $header;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        // Load and save CSV Format
        } 
        // Status change to report_export_task table
        if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }
    }
    // -------------------------------------- End - Export insurance over payment ------------------------------------

    /**
     * Display a listing of the patient and insurance payment.
     *
     * @return Response
     */
    public function patientInsurancePaymentList(){ 
        // Get filter option from db
        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('patient_insurance_payment');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        // Define to selected tab to view in reports
        $selected_tab = "collection-report";
        // Define to heading in left sidebar
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : 0;
        return view('reports/collections/patient_insurance_payment/list', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData', 'practice_id'));
    }

    /**
     * Filter apply for patient and insurance payment.
     *
     * @return Response
     */
    public function patientInsurancePaymentSearch($export = '',$data = ''){
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            return $this->patientInsurancePaymentSearchSP($export, $data); // Store procedure
        }
        // Get request
        if(!empty($data))
            $request = $data;
        else
            $request = Request::all();
        if(isset($data['practice_id'])){
            $practice_id = $data['practice_id'];
            $new = new DBConnectionController();
            $new->connectPracticeDB($practice_id);
        }
        // Initialize filter parameters to view
        $header = [];

        // Query for insurance payments
        $insurance_payments = PMTClaimTXV1::with('user','insurance_detail')
                                ->selectRaw('pmt_info_v1.id, pmt_info_v1.patient_id, pmt_info_v1.source_id, pmt_info_v1.created_at as transaction_date, patients.account_no, patients.title, patients.first_name, patients.last_name, patients.middle_name,  DATE_FORMAT(claim_info_v1.date_of_service,"%m/%d/%Y") as dos, claim_info_v1.claim_number,
                                CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.short_name END AS payer,
                                CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.insurance_name END AS payer_name, pmt_info_v1.pmt_mode,
                                CASE WHEN pmt_mode="Check" THEN pmt_check_info_v1.check_no WHEN pmt_mode="Money Order" THEN pmt_check_info_v1.check_no WHEN  pmt_mode="EFT" THEN pmt_eft_info_v1.eft_no WHEN pmt_mode="Credit" THEN  pmt_card_info_v1.card_last_4 ELSE "-Nil-" END AS pmt_mode_no,
                                CASE WHEN pmt_mode="Check" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN pmt_mode="Money Order" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN  pmt_mode="EFT" THEN DATE_FORMAT(pmt_eft_info_v1.eft_date,"%m/%d/%y") WHEN pmt_mode="Credit" THEN  DATE_FORMAT(pmt_card_info_v1.created_at,"%m/%d/%y") ELSE "-Nil-" END AS pmt_mode_date,
                                pmt_info_v1.pmt_amt,
                                pmt_claim_tx_v1.total_paid, pmt_info_v1.reference,pmt_info_v1.created_by' )
            ->leftJoin('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
            ->leftJoin('claim_info_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')
            ->leftJoin('patients', 'patients.id', '=', 'pmt_claim_tx_v1.patient_id')
            ->leftJoin('insurances', 'insurances.id', '=', 'pmt_info_v1.insurance_id')
            ->leftJoin('pmt_check_info_v1', 'pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
            ->leftJoin('pmt_card_info_v1', 'pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
            ->leftJoin('pmt_eft_info_v1', 'pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
            /*->where(function($qry){
                $qry->where(function($query){
                $query->where('pmt_info_v1.pmt_mode','check')->where('pmt_check_info_v1.check_no','<>',0);
            })->orWhere('pmt_info_v1.pmt_mode','eft')->where('pmt_eft_info_v1.eft_no','<>',0)->orWhere('pmt_info_v1.pmt_mode','Credit');
            })*/
            ->where('pmt_claim_tx_v1.pmt_method','Insurance')
            ->whereNull('claim_info_v1.deleted_at')
            ->whereNull('pmt_claim_tx_v1.deleted_at')
            ->whereNull('patients.deleted_at')
            ->whereNull('pmt_check_info_v1.deleted_at')
            ->whereNull('pmt_card_info_v1.deleted_at')
            ->whereNull('pmt_eft_info_v1.deleted_at')
            ->orderBy('pmt_info_v1.created_at','desc');
        //dd($insurance_payments->get()->toArray());
                
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {          
            $search_by['Include Refund'] = 'Yes';
            $insurance_payments = $insurance_payments->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
        } else {
            $search_by['Include Refund'] = 'No';
            $insurance_payments = $insurance_payments->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
        }
        
        // Query for Patient payments
        /*
        $patient_payments = PMTInfoV1::with('created_user','insurancedetail')
                            ->selectRaw('pmt_info_v1.id, pmt_info_v1.patient_id, pmt_info_v1.source_id, pmt_info_v1.created_at as transaction_date, patients.account_no, patients.title, patients.first_name, patients.last_name, patients.middle_name, DATE_FORMAT(claim_info_v1.date_of_service,"%m/%d/%Y") as dos, claim_info_v1.claim_number, 
                            CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.short_name END AS payer, 
                            CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.insurance_name END AS payer_name,
                            pmt_info_v1.pmt_mode,
                            CASE WHEN pmt_mode="Check" THEN pmt_check_info_v1.check_no WHEN pmt_mode="Money Order" THEN pmt_check_info_v1.check_no WHEN  pmt_mode="EFT" THEN pmt_eft_info_v1.eft_no WHEN pmt_mode="Credit" THEN  pmt_card_info_v1.card_first_4 ELSE "-Nil-" END AS pmt_mode_no, 
                            CASE WHEN pmt_mode="Check" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN pmt_mode="Money Order" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN  pmt_mode="EFT" THEN DATE_FORMAT(pmt_eft_info_v1.eft_date,"%m/%d/%y") WHEN pmt_mode="Credit" THEN  DATE_FORMAT(pmt_card_info_v1.created_at,"%m/%d/%y") ELSE "-Nil-" END AS pmt_mode_date,
                            pmt_info_v1.pmt_amt, 
                            pmt.total_paid, 
                            pmt_info_v1.reference,
                            pmt_info_v1.created_by')
                        ->leftJoin('pmt_claim_tx_v1 AS pmt', 'pmt.payment_id', '=', 'pmt_info_v1.id')
                        ->leftjoin('claim_info_v1', 'claim_info_v1.id', '=', 'pmt_info_v1.source_id')
                        ->leftJoin('patients', 'patients.id', '=', 'pmt_info_v1.patient_id')
                        ->leftJoin('insurances', 'insurances.id', '=', 'pmt_info_v1.insurance_id')
                        ->leftJoin('pmt_check_info_v1', 'pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                        ->leftJoin('pmt_card_info_v1', 'pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                        ->leftJoin('pmt_eft_info_v1', 'pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                        ->where('pmt_info_v1.pmt_method','Patient')
                        ->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])
                        ->whereRaw('pmt_info_v1.void_check is null')
                        ->whereNull('claim_info_v1.deleted_at')
                        ->whereNull('pmt.deleted_at')
                        ->whereNull('patients.deleted_at')
                        ->whereNull('pmt_check_info_v1.deleted_at')
                        ->whereNull('pmt_card_info_v1.deleted_at')
                        ->whereNull('pmt_eft_info_v1.deleted_at')
                        ->orderBy('pmt_info_v1.created_at','desc')->groupBY('pmt_info_v1.id');
        */
        
        $patient_payments = PMTClaimTXV1::with('user','insurance_detail') 
                                ->selectRaw('pmt_info_v1.id, pmt_info_v1.patient_id, pmt_info_v1.source_id, pmt_claim_tx_v1.created_at as transaction_date, patients.account_no, patients.title, patients.first_name, patients.last_name, patients.middle_name, DATE_FORMAT(claim_info_v1.date_of_service,"%m/%d/%Y") as dos, claim_info_v1.claim_number, 
                                CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.short_name END AS payer, 
                                CASE WHEN pmt_info_v1.pmt_method="Patient" THEN "Patient" ELSE insurances.insurance_name END AS payer_name, 
                                pmt_info_v1.pmt_mode,
                                CASE WHEN pmt_mode="Check" THEN pmt_check_info_v1.check_no WHEN pmt_mode="Money Order" THEN pmt_check_info_v1.check_no WHEN  pmt_mode="EFT" THEN pmt_eft_info_v1.eft_no WHEN pmt_mode="Credit" THEN  pmt_card_info_v1.card_last_4 ELSE "-Nil-" END AS pmt_mode_no,
                                CASE WHEN pmt_mode="Check" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN pmt_mode="Money Order" THEN DATE_FORMAT(pmt_check_info_v1.check_date,"%m/%d/%y") WHEN  pmt_mode="EFT" THEN DATE_FORMAT(pmt_eft_info_v1.eft_date,"%m/%d/%y") WHEN pmt_mode="Credit" THEN  DATE_FORMAT(pmt_card_info_v1.created_at,"%m/%d/%y") ELSE "-Nil-" END AS pmt_mode_date, 
                                pmt_info_v1.pmt_amt, pmt_claim_tx_v1.total_paid, pmt_info_v1.reference,pmt_claim_tx_v1.created_by')
                            ->leftJoin('pmt_info_v1', 'pmt_claim_tx_v1.payment_id', '=', 'pmt_info_v1.id')
                            ->leftJoin('claim_info_v1', 'claim_info_v1.id','=' ,'pmt_claim_tx_v1.claim_id')
                            //->join('claim_info_v1', 'claim_info_v1.id', '=', 'pmt_info_v1.source_id')
                            ->leftJoin('patients', 'patients.id', '=', 'pmt_info_v1.patient_id')
                            ->leftJoin('insurances', 'insurances.id', '=', 'pmt_info_v1.insurance_id')
                            ->leftJoin('pmt_check_info_v1', 'pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                            ->leftJoin('pmt_card_info_v1', 'pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                            ->leftJoin('pmt_eft_info_v1', 'pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id')
                            ->where('pmt_info_v1.pmt_method','Patient')
                            //->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])
                            //->whereRaw('pmt_info_v1.void_check is null')
                            ->whereNull('claim_info_v1.deleted_at')
                            ->whereNull('pmt_claim_tx_v1.deleted_at')
                            ->whereNull('patients.deleted_at')
                            ->whereNull('pmt_check_info_v1.deleted_at')
                            ->whereNull('pmt_card_info_v1.deleted_at')
                            ->whereNull('pmt_eft_info_v1.deleted_at')
                            ->orderBy('pmt_claim_tx_v1.created_at','desc')
                            ->groupBY('pmt_claim_tx_v1.id');
                            
                            
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {          
            $header['Include Refund'] = 'Yes';
            $patient_payments = $patient_payments->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
        } else {
            $header['Include Refund'] = 'No';
            $patient_payments = $patient_payments->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
        }
        
        $practice_timezone = Helpers::getPracticeTimeZone();
               
        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $exp = explode("-",$request['select_transaction_date']);
                $start_date = $exp[0];
                $end_date = $exp[1];
                $start_date = Helpers::dateFormat($start_date, 'datedb');
                $end_date = Helpers::dateFormat($end_date, 'datedb');
                $header['Transaction Date'] = date("m/d/y",strtotime($start_date)) . "  To " . date("m/d/y",strtotime($end_date));
                /*$start_date = Helpers::utcTimezoneStartDate($start_date);
                $end_date = Helpers::utcTimezoneEndDate($end_date);*/
                $insurance_payments->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");  
                $patient_payments->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");  
            }
        }
        
        // Filter by Dos Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $start_date = $exp[0];
            $end_date = $exp[1];
            $start_date = Helpers::dateFormat($start_date, 'datedb');
            $end_date = Helpers::dateFormat($end_date, 'datedb');
            $insurance_payments->whereRaw("DATE(claim_info_v1.date_of_service) >= '$start_date' and DATE(claim_info_v1.date_of_service) <= '$end_date'");  
            $patient_payments->whereRaw("DATE(claim_info_v1.date_of_service) >= '$start_date' and DATE(claim_info_v1.date_of_service) <= '$end_date'");  
            $header['DOS'] = date("m/d/Y",strtotime($start_date)) . "  To " . date("m/d/Y",strtotime($end_date));
        }

        // Filter by User
        if (isset($request["user"]) && !empty($request["user"])) {
            $user = (isset($request['export']) || is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
            $insurance_payments->whereIn('pmt_info_v1.created_by', $user);
            $patient_payments->whereIn('pmt_info_v1.created_by', $user);
            $User_name =  Users::whereIn('id', $user)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $header['User'] = $User_name;
        }
        
        // Except zero payments 
        if(empty($request['options'])){
            $insurance_payments->where('total_paid','!=',0);
            $patient_payments->where('pmt_amt','!=',0);
        }else{
            $header["Include"] = "Zero Payments";
        }

        $insurance_payments->orWhere(function($qry)
            use ($request,$practice_timezone)
            {
                $qry->whereIn('pmt_claim_tx_v1.pmt_method',['Insurance'])
                    ->whereNotIn('pmt_claim_tx_v1.pmt_type', ['Refund','Adjustment']);
                if(empty($request['options'])){
                    $qry->where('total_paid','!=',0);
                }else{
                    $qry->where('pmt_claim_tx_v1.total_paid','<=',0);
                }
                    
                // Filter by Transaction Date
                if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                    ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
                if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                    $exp = explode("-",$request['select_transaction_date']);
                    $start_date = $exp[0];
                    $end_date = $exp[1];
                    $start_date = Helpers::dateFormat($start_date, 'datedb');
                    $end_date = Helpers::dateFormat($end_date, 'datedb');

                    /*$start_date = Helpers::utcTimezoneStartDate($start_date);
                    $end_date = Helpers::utcTimezoneEndDate($end_date);*/
                    $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");  
                }

                // Filter by Dos Date
                if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                    ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
                if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                    $exp = explode("-",$request['select_date_of_service']);
                    $start_date = $exp[0];
                    $end_date = $exp[1];
                    $start_date = Helpers::dateFormat($start_date, 'datedb');
                    $end_date = Helpers::dateFormat($end_date, 'datedb');
                    $qry->whereRaw("DATE(claim_info_v1.date_of_service) >= '$start_date' and DATE(claim_info_v1.date_of_service) <= '$end_date'");
                }

                // Filter by User
                if (isset($request["user"]) && !empty($request["user"])) {
                    $user = (isset($request['export']) || is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
                    $qry->whereIn('pmt_info_v1.created_by', $user);
                }
                
                // Except zero payments 
                /*if(empty($request['options'])){
                    $qry->where('total_paid','!=',0);
                    
                }*/
                //$qry->whereRaw('pmt_info_v1.void_check is null')->whereNull('claim_info_v1.deleted_at');
            });
            
        $patient_summary = clone $patient_payments;
        $insurance_summary = clone $insurance_payments;//dd($insurance_summary->toSql());
        $patient_summary = $patient_summary->get();
        $insurance_summary = $insurance_summary->get();
        $patient_total = $insurance_total = 0;
        if(isset($patient_summary) && !empty($patient_summary)) {
            foreach($patient_summary as $pat){
                $patient_total += $pat->total_paid;
            }
        }
        
        if(isset($insurance_summary) && !empty($insurance_summary)) {
            foreach($insurance_summary as $ins){
                $insurance_total += $ins->total_paid;
            }
        }
        
        // To check export or view
        if(isset($request['export'])){
            if (isset($request['payer']))
            if ($request['payer']=='all') {
                    $payments=$insurance_payments->unionAll($patient_payments->getQuery());
                $header["Payer"] = "All Payments";
            }elseif ($request['payer']=='self') {
                $payments = $patient_payments;
                $header["Payer"] = "Patient Payments";
            }else{
                $header["Payer"] = "Insurance Payments";
                if (isset($request['insurance_id']))
                if ($request['insurance_id']!='') {
                    if(is_array($request['insurance_id']) && array_sum($request['insurance_id'])!=0 )   {
                        if(count($request['insurance_id'])==1){
                            $request['insurance_id'] = explode(',', $request['insurance_id'][0]);
                        }
                        $insurance_payments->whereIn("pmt_info_v1.insurance_id",$request['insurance_id']);
                        $header["Insurance"] = @array_flatten(Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ' , ') as insurance_name")->whereIn('id',$request['insurance_id'])->get()->toArray())[0];
                    }
                }
                $payments = $insurance_payments;
            }
            $payment = $payments->orderBy('transaction_date','desc')->get();
            return compact('payment','patient_total','insurance_total','header');
        }else{
            $pagination = '';
            // Define for pagination count for per page
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            if (isset($request['payer']))
            if ($request['payer']=='all') {
                $header["Payer"] = "All Payments";
                $payments=$insurance_payments->unionAll($patient_payments->getQuery())->orderBy('transaction_date','desc')->get();
                $p = Input::get('page', 1);
                $paginate = $paginate_count;
                $offSet = ($p * $paginate) - $paginate;
                $slice = array_slice($payments->toArray(), $offSet, $paginate,true);
                $payment = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($payments), $paginate,$p,['path'=>Request::url()]);
                $payment_pagination = $payment->toArray();
            }elseif ($request['payer']=='self') {
                $payment = $patient_payments->paginate($paginate_count);
                $payment_pagination = $payment->toArray();
                $header["Payer"] = "Patient Payments";
            }else{
                $header["Payer"] = "Insurance Payments";
                if (isset($request['insurance_id'])) {
                    if ($request['insurance_id']!='') {
                        $insurance_payments->whereIn("pmt_info_v1.insurance_id",$request['insurance_id']);
                        $header["Insurance"] = @array_flatten(Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ' , ') as insurance_name")->whereIn('id',$request['insurance_id'])->get()->toArray())[0];
                    }
                }
                $payment = $insurance_payments->paginate($paginate_count);
                $payment_pagination = $payment->toArray();
            }
            // Pagination navigation
            $pagination_prt = $payment->render();
            
            // Default pagination if single page
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            // To set pagination datas
            $pagination = array('total' => $payment_pagination['total'], 'per_page' => $payment_pagination['per_page'], 'current_page' => $payment_pagination['current_page'], 'last_page' => $payment_pagination['last_page'], 'from' => $payment_pagination['from'], 'to' => $payment_pagination['to'], 'pagination_prt' => $pagination_prt);
            
            // Separate data only
                //$payment = $payment->toArray()['data'];
            $payment = json_decode (json_encode ($payment->toArray()['data']));          
                $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
            return view('reports/collections/patient_insurance_payment/report', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData','payment','header','pagination','user_names','patient_total','insurance_total'));
        }
    }
        
    /* Stored procedure for patient insurance payments - Anjukaselvan*/
    public function patientInsurancePaymentSearchSP($export = '', $data = '') {
        // Get request
        if (!empty($data))
            $request = $data;
        else
            $request = Request::all(); 
        
        if (isset($data['practice_id'])) {
            $practice_id = $data['practice_id'];
            $new = new DBConnectionController();
            $new->connectPracticeDB($practice_id);                
        }
        // Initialize filter parameters to view
        $header = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $payer = $insurance_id = $option_zero_payments = $user_ids = '';
        
        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $exp = explode("-",$request['select_transaction_date']);
                $start_date = $exp[0];
                $end_date = $exp[1];
                $header['Transaction Date'] = date("m/d/y", strtotime($start_date)) . "  To " . date("m/d/y", strtotime($end_date));
                $start_date = Helpers::utcTimezoneStartDate($start_date);
                $end_date = Helpers::utcTimezoneEndDate($end_date);
            }
            
        if(isset($request['choose_date']) && !empty($request['choose_date']) && 
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                $exp = explode("-",$request['select_date_of_service']);
                $dos_start_date = $exp[0];
                $dos_end_date = $exp[1];
                $dos_start_date = Helpers::dateFormat($dos_start_date, 'datedb');
                $dos_end_date = Helpers::dateFormat($dos_end_date, 'datedb');
                $header['DOS'] = date("m/d/Y",strtotime($dos_start_date)) . "  To " . date("m/d/Y",strtotime($dos_end_date));
            }
        
        // Filter by User
        if (isset($request["user"]) && !empty($request["user"])) {
            $user_ids = (isset($request['export']) || is_string($request['user'])) ? $request['user']:implode(',',$request['user']);
            $user = (isset($request['export']) || is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
            $User_name =  Users::whereIn('id', $user)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $header['User'] = $User_name;
        }

        // Except zero payments 
        if (isset($request['options'])) {
            $option_zero_payments = $request['options'];
            $header["Include"] = "Zero Payments";
        }

        if (isset($request['payer'])){
            $payer = $request['payer'];
            if ($request['payer'] == 'all') {
                $header["Payer"] = "All Payments";
            } elseif ($request['payer'] == 'self') {
                $header["Payer"] = "Patient Payments";
            } else {
                $header["Payer"] = "Insurance Payments";
                if (isset($request['insurance_id']))
                    if ($request['insurance_id']!='') {
                        $insurance_id = (isset($request['export']) || is_string($request['insurance_id'])) ? $request['insurance_id']:implode(",", $request['insurance_id']);
                        $insurance = (isset($request['export']) || is_string($request['insurance_id'])) ? explode(",", $request['insurance_id']):$request['insurance_id'];
                        $header["Insurance"] = @array_flatten(Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ' , ') as insurance_name")->whereIn('id',$insurance)->get()->toArray())[0];
                    }
            }
        }
        
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
        $offset = 0;
        $page = 0;

        if (isset($request['page'])) {
            $page = $request['page'];
            $offset = ($page - 1) * $paginate_count;
            $from = $offset + 1;
            $to = $offset + $paginate_count;
        } else {
            $from = 1;
            $to = $paginate_count;
        }
        if ($export == "") {                

            $recCount = 1;
            $sp_return_result = DB::select('call patientInsurancePayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $payer . '", "' . $insurance_id . '", "' . $option_zero_payments . '", "' . $user_ids . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->pmt_count;
            $last_page = 0;
            if ($count != 0)
                $last_page = ceil($count / $paginate_count);
            if (isset($request['page'])) {
                $page = $request['page'];
                $offset = ($page - 1) * $paginate_count;
                $from = $offset + 1;
                $to = $offset + $paginate_count;
                if ($page == $last_page) {
                    $to = $offset + $count % $paginate_count;
                }
            } else {
                if($paginate_count > $count){
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }
            $recCount = 0;
            $sp_return_result = DB::select('call patientInsurancePayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $payer . '", "' . $insurance_id . '", "' . $option_zero_payments . '", "' . $user_ids . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array)$sp_return_result;

            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }

            $report_array = $this->paginate($sp_return_result)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);
            
            $payment = $sp_return_result;
            $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
            //summary
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call patientInsurancePayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $payer . '", "' . $insurance_id . '", "' . $option_zero_payments . '", "' . $user_ids . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $summary = $sp_return_result;
            $patient_total = $insurance_total = 0;
            if(isset($summary) && !empty($summary))
            foreach($summary as $pat){
                if($pat->payer == 'Patient'){
                    $patient_total += $pat->pmt_amt;
                }else{
                    $insurance_total += $pat->total_paid;
                }
            }
            return view('reports/collections/patient_insurance_payment/report', compact('payment', 'header', 'pagination', 'user_names','patient_total','insurance_total'));

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call patientInsurancePayment("' . $start_date . '", "' . $end_date . '","' . $dos_start_date . '", "' . $dos_end_date . '",  "' . $payer . '", "' . $insurance_id . '", "' . $option_zero_payments . '", "' . $user_ids . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $payment = $sp_return_result;
            //summary export
            $patient_total = $insurance_total = 0;
            if(isset($payment) && !empty($payment))
            foreach($payment as $pat){
                if($pat->payer == 'Patient'){
                    $patient_total += $pat->pmt_amt;
                }else{
                    $insurance_total += $pat->total_paid;
                }
            }
            return compact('payment','header','patient_total','insurance_total');
        }
    }

    // -------------------------------------- Start - Export patient and insurance payment ------------------------------------
    public function patientInsurancePaymentSearchexport($export = '',$data = ''){
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $payment = $this->patientInsurancePaymentSearchSP($export, $data); // Stored procedure
        } else {
            $payment = $this->patientInsurancePaymentSearch($export, $data); // DB
        }

        // $payment = $payment->getallheaders()();
        $header = $payment['header'];
        $payment_count1 = count((array)$payment['payment'])+10;
        $payment_count2 = count((array)$payment['payment'])+12;
        $payment_c_summary = "B".$payment_count1.":"."B".$payment_count2;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Patient_Insurance_Payment_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $user_names =  Users::where('status', 'Active')->pluck('name', 'id')->all();
        
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/collections/patient_insurance_payment/report_export_pdf';
            $report_name = "Patient and Insurance Payment";
            $data = ['payment' => $payment, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'header' => $header, 'payment_count1' => $payment_count1, 'payment_count2' => $payment_count2, 'payment_c_summary' => $payment_c_summary, 'user_names' => $user_names];
            return $data;
        }
                
        if ($export == 'xlsx' || $export == 'csv') {
            ini_set('precision', 20);
            $filePath = 'reports/collections/patient_insurance_payment/report_export';
            $data['payment_c_summary'] = $payment_c_summary;
            $data['payment'] = $payment;
            $data['user_names'] = $user_names;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            $data['header'] = $header;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        // Load and save CSV Format
        } 
        // Status change to report_export_task table
        // if(isset($data['export_id'])){
        //     ReportExportTask::where('id',$data['export_id'])->update([ 'status'=>'Completed']);
        // }
    }
    // -------------------------------------- End - Export patient and insurance payment ------------------------------------
        
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }

}