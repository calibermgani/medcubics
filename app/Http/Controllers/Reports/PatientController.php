<?php 
namespace App\Http\Controllers\Reports;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Helpers\Helpers;
use App\Http\Controllers\Claims\ClaimControllerV1;
use App\Models\ReportExport as ReportExportTask;
use App\Models\Patients\Patient;
use App\Exports\BladeExport;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use ClaimUtil;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTCheckInfoV1;
use App\Models\Payments\PMTEFTInfoV1;
use App\Models\Payments\PMTCardInfoV1;
use Input;
use Lang;
class PatientController extends Controller {
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
	 * Display a listing of the wallet balance.
	 *
	 * @return Response
	 */
    public function walletBalance(){
        // Get filter option from db
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('wallet_balance');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        // Define to selected tab to view in reports
        $selected_tab = "patients-report";
        // Define to heading in left sidebar
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        return view('reports/patients/walletbalance/list', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData'));        
    }

	/**
	 * Filter apply for wallet balance.
	 *
	 * @return Response
	 */
    public function walletBalanceSearch($export = '',$data = ''){
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            return $this->walletBalanceSearchSP($export, $data); // Store procedure
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

        // Wallet balance Query
        // Table patients with left join pmt_claim_fin_v1, pmt_wallet_v1
        $patients = Patient::selectRaw('patients.id, patients.account_no, patients.first_name, patients.last_name, patients.middle_name, DATE_FORMAT(patients.dob,"%m/%d/%Y") AS dob, patients.statements, patients.statements_sent, CASE WHEN patients.last_statement_sent_date="00/00/00" THEN "- Nil -"  ELSE DATE_FORMAT(patients.last_statement_sent_date,"%m/%d/%y") END as last_statement,CASE WHEN (SELECT SUM(pmt_amt)-SUM(amt_used)  FROM pmt_info_v1 WHERE pmt_info_v1.patient_id = patients.id AND deleted_at is null AND pmt_info_v1.pmt_method = "Patient" AND pmt_info_v1.pmt_type IN ("Payment","Credit Balance") AND pmt_info_v1.void_check IS NULL AND pmt_info_v1.pmt_amt > 0)!=0 THEN (SELECT SUM(pmt_amt)-SUM(amt_used) FROM pmt_info_v1 WHERE pmt_info_v1.patient_id = patients.id AND deleted_at is null AND pmt_info_v1.pmt_method = "Patient" AND pmt_info_v1.pmt_type IN ("Payment","Credit Balance") AND pmt_info_v1.void_check IS NULL AND pmt_info_v1.pmt_amt > 0) ELSE 0.00 END as wallet_balance, CASE WHEN (SELECT SUM(pmt.patient_due) FROM pmt_claim_fin_v1 pmt WHERE pmt.patient_id = patients.id and deleted_at is null)!=0 THEN (SELECT SUM(pmt.patient_due) FROM pmt_claim_fin_v1 pmt WHERE pmt.patient_id = patients.id and deleted_at is null) ELSE 0.00 END as patient_balance')
            ->where('status','Active')->groupBy('patients.id')
            ->orderBy('patients.id','desc');
	    
        // Filter by Account no
        if(isset($request['acc_no']) && !empty($request['acc_no'])){
            $patients->where("patients.account_no", "like", "%".$request['acc_no']."%");  
            $header['Account No'] = $request['acc_no'];
        }

        // Filter by Patient Name
        if(isset($request['patient_name']) && !empty($request['patient_name'])){
            $patient_name = explode(',', $request['patient_name']);
            if(!empty($patient_name)){
                if(isset($patient_name[0]))
                $patients->where("patients.last_name", "like", "%".trim($patient_name[0])."%");
                if(isset($patient_name[1]))
                $patients->where("patients.first_name", "like", "%".trim($patient_name[1])."%");
            }
                $header['Patient Name'] = $request['patient_name'];
        }

        // Filter by statements
        if(isset($request['statements']) && !empty($request['statements']) && $request['statements'] !='All'){
            $patients->where("patients.statements", $request['statements']);  
            $header['Statements'] = $request['statements'];
        }
        $patients = $patients->havingRaw('(SELECT SUM(wallet.amount) FROM pmt_wallet_v1 wallet WHERE wallet.patient_id = patients.id and deleted_at is null) != 0');
        // To check export or view
        if(isset($request['exports']) && $request['exports'] == 'pdf'){
            $patients = $patients->get();
            return compact('patients','header');
        } elseif(isset($request['export']) && $request['export'] == 'xlsx'){
            $patients = $patients->get();
            return compact('patients','header');
        } else{
            $pagination = '';
            // Define for pagination count for per page
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
			
            // Get records per page
            $patient = $patients->paginate($paginate_count);
            $patient_pagination = $patient->toArray();
	        
            // Pagination navigation
            $pagination_prt = $patient->render();
	        
            // Default pagination if single page
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
                // To set pagination datas
	        $pagination = array('total' => $patient_pagination['total'], 'per_page' => $patient_pagination['per_page'], 'current_page' => $patient_pagination['current_page'], 'last_page' => $patient_pagination['last_page'], 'from' => $patient_pagination['from'], 'to' => $patient_pagination['to'], 'pagination_prt' => $pagination_prt);
	        
	        // Separate data only
	        //$patient = $patient->toArray()['data'];
                $patient = $patient;
            return view('reports/patients/walletbalance/report', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData','patient','header','pagination'));
        }
    }
        
    /** Stored procedure for patient wallet balance - Anjukaselvan **/
    public function walletBalanceSearchSP($export = '',$data = ''){
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
        $acc_no = $patient_name = $statements = '';		

        // Filter by Account no
        if(isset($request['acc_no']) && !empty($request['acc_no'])){
            $acc_no = $request['acc_no'];
            $header['Account No'] = $request['acc_no'];
        }

        // Filter by Patient Name
        if(isset($request['patient_name']) && !empty($request['patient_name'])){
            $patient_name = ucwords(preg_replace('/\s+/', ' ', (str_replace(",", " ",$request['patient_name'])) ));
            $header['Patient Name'] = $request['patient_name'];
        }

        // Filter by statements
        if(isset($request['statements']) && !empty($request['statements']) && $request['statements'] !='All'){
            $statements = $request['statements'];
            $header['Statements'] = $request['statements'];
        }
        //
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
            $sp_return_result = DB::select('call walletBalance("' . $acc_no . '", "' . $patient_name . '",  "' . $statements . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->walletBalance_count;
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
            $sp_return_result = DB::select('call walletBalance("' . $acc_no . '", "' . $patient_name . '",  "' . $statements . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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

            $patient = $sp_return_result;

            return view('reports/patients/walletbalance/report', compact('patient','header','pagination'));

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call walletBalance("' . $acc_no . '", "' . $patient_name . '",  "' . $statements . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $patients = $sp_return_result;
            return compact('patients','header');
        }
    }

    // -------------------------------------- Start - Export Wallet Balance ------------------------------------
    public function walletBalanceSearchExport($export = '',$data = ''){
        // Send request and get data
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  { 
            $patients = $this->walletBalanceSearchSP($export, $data); // Stored procedure 
        } else {
            $patients = $this->walletBalanceSearch($export, $data); // DB 
        }
        //dd($patients['patients']);
        $patient = $patients['patients'];
        $header = $patients['header'];
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Wallet_Balance_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
          $report_name = 'Wallet Balance';
          $view_path = 'reports/patients/walletbalance/report_export_pdf';
          $data = ['patient' => $patient, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'header' => $header];
          return $data;
        }        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/patients/walletbalance/report_export';
            $data['patient'] = $patient;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['header'] = $header;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            return $data;
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        // Status change to report_export_task table
        if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }
    }
    // -------------------------------------- End - Export Wallet Balance ------------------------------------
        
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }

    /**
     * Display a listing of the wallet balance.
     *
     * @return Response
     */
    public function itemizedBill(){
        // Get filter option from db
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('itemized_bill');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        // Define to selected tab to view in reports
        $selected_tab = "patients-report";
        // Define to heading in left sidebar
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        return view('reports/patients/itemizedBill/list', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData'));        
    }

    /**
     * Filter apply for Itemized Bill.
     *
     * @return Response
     */
    public function itemizedBillSearch($export = '',$data = ''){
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
        $practice_timezone = Helpers::getPracticeTimeZone(); 
        // Itemized Bill Query
        $query = ClaimInfoV1::selectRaw('
            claim_info_v1.id,
            claim_cpt_tx_desc_v1.claim_cpt_info_id as cpt_id,
            patients.id as patient_id,
            patients.account_no,
            patients.first_name,
            patients.last_name,
            patients.middle_name,
            DATE_FORMAT(patients.dob, "%m/%d/%Y") AS dob,
            patients.gender,
            patients.ssn,
            patients.address1,
            patients.id as patient_id,
            claim_info_v1.status,
            claim_info_v1.date_of_service,
            claim_info_v1.claim_number,
            claim_info_v1.icd_codes,
            claim_info_v1.insurance_id,
            insurances.short_name as insurance_name,
            rendering.short_name as rendering_short_name,
            rendering.provider_name as rendering_name,
            billing.short_name as billing_short_name,
            billing.provider_name as billing_name,
            facilities.short_name as facility_short_name,
            facilities.facility_name,
            pmt_claim_cpt_tx_v1.withheld,
            pmt_claim_cpt_tx_v1.writeoff,
            pmt_claim_cpt_tx_v1.paid,
            pmt_claim_cpt_tx_v1.deduction,
            pmt_claim_cpt_tx_v1.coins,
            pmt_claim_cpt_tx_v1.copay,
            pmt_claim_cpt_tx_v1.denial_code,
            pmt_claim_tx_v1.total_deduction,
            pmt_claim_tx_v1.total_coins,
            pmt_claim_tx_v1.total_copay,
            pmt_claim_tx_v1.payer_insurance_id,
            pmt_claim_tx_v1.ins_category,
            pmt_info_v1.pmt_mode,
            pmt_info_v1.pmt_mode_id,
            pmt_info_v1.id as payment_id,
            claim_cpt_tx_desc_v1.id as cpt_tx_desc_id,
            claim_cpt_tx_desc_v1.value_1 as claim_cpt_value1,
            claim_cpt_tx_desc_v1.value_2,
            claim_cpt_tx_desc_v1.transaction_type as claim_cpt_transaction_type,
            claim_cpt_tx_desc_v1.responsibility as claim_cpt_responsibility,
            claim_cpt_tx_desc_v1.txn_id as claim_cpt_txn_id,
            claim_cpt_tx_desc_v1.pat_bal as claim_cpt_pat_bal,
            claim_cpt_tx_desc_v1.ins_bal as claim_cpt_ins_bal,
            DATE(CONVERT_TZ(claim_cpt_tx_desc_v1.created_at,"UTC","'.$practice_timezone.'")) as claim_cpt_created_at,
            claim_info_v1.total_charge,
            claim_cpt_info_v1.cpt_code,
            claim_cpt_info_v1.charge')
            ->leftJoin('pos', 'pos.id', '=', 'claim_info_v1.pos_id')
            ->leftJoin('providers as rendering', 'rendering.id', '=', 'claim_info_v1.rendering_provider_id')
            ->leftJoin('providers as billing', 'billing.id', '=', 'claim_info_v1.billing_provider_id')
            ->leftJoin('providers as refering', 'refering.id', '=', 'claim_info_v1.refering_provider_id')
            ->leftJoin('facilities', 'facilities.id', '=', 'claim_info_v1.facility_id')
            ->leftJoin('patients', 'patients.id', '=', 'claim_info_v1.patient_id')
            ->leftJoin('claim_cpt_tx_desc_v1', 'claim_cpt_tx_desc_v1.claim_id', '=', 'claim_info_v1.id')
            ->join('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
            ->leftJoin('pmt_claim_cpt_fin_v1', 'pmt_claim_cpt_fin_v1.claim_id', '=', 'claim_info_v1.id')
            ->leftJoin('claim_cpt_info_v1', 'claim_cpt_info_v1.id', '=', 'claim_cpt_tx_desc_v1.claim_cpt_info_id')
            ->leftJoin('pmt_claim_cpt_tx_v1', 'pmt_claim_cpt_tx_v1.id', '=', 'claim_cpt_tx_desc_v1.txn_id')
            ->leftJoin('pmt_claim_tx_v1', 'pmt_claim_tx_v1.id', '=', 'pmt_claim_cpt_tx_v1.pmt_claim_tx_id')
            ->leftJoin('pmt_info_v1', 'pmt_info_v1.id', '=', 'pmt_claim_tx_v1.payment_id')
            ->leftJoin('insurances', 'insurances.id', '=', 'pmt_claim_tx_v1.payer_insurance_id')
            ->leftJoin('cpts', 'cpts.cpt_hcpcs', '=', 'claim_cpt_info_v1.cpt_code')
            ->where('patients.status','Active')->groupBy('claim_cpt_tx_desc_v1.id');
        
        // Filter by Account no
        if(isset($request['acc_no']) && !empty($request['acc_no'])){
            $query->where("patients.account_no", $request['acc_no']);  
            $header['Account No'] = $request['acc_no'];
        }

        // Filter by Patient Name
        if(isset($request['export'])){
            if(!isset($request['patient_name']))
                $request['patient_name'] = "";
        }
        $patient_name = explode(',', $request['patient_name']);

        if($patient_name[0]=='' && empty($request['acc_no'])){
            $patient_name[0] = 123456789123456789;
        }else{
            if($patient_name[0]!='')
            $header['Patient Name'] = $request['patient_name'];
        }
        if(isset($patient_name[0]))
        $query->where("patients.last_name", "like", "%".trim($patient_name[0])."%");
        if(isset($patient_name[1])){
            $first_name = explode(' ', trim($patient_name[1]));
            if(!empty($first_name))
                $query->where("patients.first_name", "like", "%".trim($first_name[0])."%");
        }
        $patient = $query->orderBy('claim_cpt_tx_desc_v1.id','asc')->get();
        $patients = [];
        $i = 0;
        if(!empty($patient)){
            foreach($patient as $res){
                $patients[$res->id]['claim']['id'] = $res->id;
                $patients[$res->id]['claim']['account_no'] = $res->account_no;
                $patients[$res->id]['claim']['first_name'] = $res->first_name;
                $patients[$res->id]['claim']['last_name'] = $res->last_name;
                $patients[$res->id]['claim']['middle_name'] = $res->middle_name;
                $patients[$res->id]['claim']['dob'] = $res->dob;
                $patients[$res->id]['claim']['gender'] = $res->gender;
                $patients[$res->id]['claim']['ssn'] = $res->ssn;
                $patients[$res->id]['claim']['address1'] = $res->address1;
                $patients[$res->id]['claim']['status'] = $res->status;
                $patients[$res->id]['claim']['date_of_service'] = $res->date_of_service;
                $patients[$res->id]['claim']['claim_number'] = $res->claim_number;
                $patients[$res->id]['claim']['icd_codes'] = $res->icd_codes;
                $patients[$res->id]['claim']['rendering_short_name'] = $res->rendering_short_name;
                $patients[$res->id]['claim']['rendering_name'] = $res->rendering_name;
                $patients[$res->id]['claim']['billing_short_name'] = $res->billing_short_name;
                $patients[$res->id]['claim']['billing_name'] = $res->billing_name;
                $patients[$res->id]['claim']['facility_short_name'] = $res->facility_short_name;
                $patients[$res->id]['claim']['facility_name'] = $res->facility_name;
                $patients[$res->id]['claim']['total_charge'] = $res->total_charge;
                $patients[$res->id]['claim']['payer_insurance_id'] = $res->payer_insurance_id;
                $patients[$res->id]['claim']['insurance_id'] = $res->insurance_id;
                $patients[$res->id]['claim']['patient_id'] = $res->patient_id;
                if($i==0){
                    $wallet = Patient::selectRaw('CASE WHEN (SELECT SUM(wallet.amount) FROM pmt_wallet_v1 wallet WHERE wallet.patient_id = patients.id and deleted_at is null)!=0 THEN (SELECT SUM(wallet.amount) FROM pmt_wallet_v1 wallet WHERE wallet.patient_id = patients.id and deleted_at is null) ELSE 0.00 END as wallet_balance')
                    ->where('status','Active')->where('id',$res->patient_id)->first()['wallet_balance'];
                }
                $i++;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['cpt_id'] = $res->cpt_id;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['cpt_code'] = $res->cpt_code;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['charge'] = $res->charge;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['cpt_tx_desc_id'] = $res->cpt_tx_desc_id;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_created_at'] = $res->claim_cpt_created_at;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_value1'] = $res->claim_cpt_value1;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['value_2'] = '';
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_transaction_type'] = $res->claim_cpt_transaction_type;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_responsibility'] = $res->claim_cpt_responsibility;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_txn_id'] = $res->claim_cpt_txn_id;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_pat_bal'] = $res->claim_cpt_pat_bal;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_ins_bal'] = $res->claim_cpt_ins_bal;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['withheld'] = $res['withheld'];
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['writeoff'] = $res['writeoff'];
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['paid'] = $res['paid'];
                $respCat = ($res['claim_cpt_transaction_type'] != 'Responsibility' && isset($res['ins_category']) && !is_numeric($res['ins_category'])) ? @$res['ins_category'] : "";
                    
                    if($res['claim_cpt_transaction_type'] == 'Insurance Payment' && $respCat == '') {
                      $respCat = 'Others';
                    }
                    /*if($res['ins_category']!='')
                    dd($res['paid']);*/
                    if($respCat =='Primary')
                        $resp_bg_class = "pri-bg";
                    elseif($respCat =='Secondary')
                        $resp_bg_class = "sec-bg";
                    elseif($respCat =='Tertiary')
                        $resp_bg_class = "ter-bg";
                    else
                        $resp_bg_class = "pri-bg";
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['respCat'] = $respCat;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['resp_bg_class'] = $resp_bg_class;
                $responsibility = $desc = $charges = $pmt_type = '';
                    $pmts = $adj = 0;
                    //Log::info($res['claim_cpt_transaction_type']);
                $responsibility = ($res['claim_cpt_responsibility']!=0)?Helpers::getInsuranceName($res['claim_cpt_responsibility']):"Patient";
                switch ($res['claim_cpt_transaction_type']) {
                        case 'New Charge':
                            // New line item added
                            $desc = Lang::get('payments/claim_transaction.cpt_txn.charge_created_desc');
                            $charges = $res->charge;
                            // Need to take charge amount from claim cpt desc value json string if available, otherwise take from cpt_info 
                            if($res['claim_cpt_value1'] != '') {
                              $descDet = json_decode($res['claim_cpt_value1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  $charges = trim($descDet['charge_amt']);
                                }
                              }
                            }
                            if ($responsibility == '')
                                $responsibility = 'Patient';
                            //$total_charge = ($res['ins_bal'] + $res['pat_bal']) - $adj;
                            break;

                        case 'Responsibility':
                            // Responsibility modified for CPT
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.transfer_to_ins_desc");
                            $responsibility = ($responsibility == '') ? 'Patient' : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $responsibility, $desc);

                            // Set old responsiblity 
                            $old_resp = ($res['claim_cpt_value1'] > 0) ? ClaimInfoV1::GetInsuranceName($res['claim_cpt_value1']) : 'Patient';
                            $responsibility = ($old_resp == "Self" || $old_resp == '' ) ? 'Patient': $old_resp;
                            $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['claim_cpt_responsibility'] = $res['claim_cpt_value1'];
                            $pmts = $charges = '';
                            break;

                        case 'Patient Payment':
                            // Patient Payment posted
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_pmt_paid_desc");
                            $patient_id = @$res['patient_id'];
                            $pat_name = 'Patient';
                            $desc = str_replace("VAR_PAT_NAME", $pat_name, $desc);
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            $pmts = @$res['paid'];
                            break;

                        case 'Insurance Payment':
                            // Insurance payment posted
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_paid_desc");
                            $resp = ($res['claim_cpt_value1'] > 0 && $res['claim_cpt_value1'] != "") ? ClaimInfoV1::GetInsuranceName($res['claim_cpt_value1']) : $responsibility;
                            $desc = str_replace("VAR_INS_NAME", $resp, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$res['payment_id']);
                            $pmts = @$res['paid'];
                            //$total_charge = ($ins_bal + $pat_bal) - $adj;

                            if (@$res['deduction'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['deduction'], $resp);
                            }
                            if (@$res['coins'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['coins'], $resp);
                            }
                            if (@$res['copay'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['copay'], $resp);
                            }
                                // Other adjustment details shown instead of withheld.
                                $adjs = ClaimInfoV1::getClaimOtherAdjDetails($res['id'], @$res['claim_cpt_txn_id'], @$res['cpt_id']);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".Helpers::priceFormat($adjRec['adj_amt']);
                                  }
                                }
                                $desc .=$adj_resp;
                            // if adjustment applied it needs to append
                            
                            if (@$res['writeoff'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['writeoff'], $resp);
                            }
                            
                            if($res['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $res['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$res['writeoff'] !=0 || @$res['withheld'] != 0 ){
                                $adj = $res['writeoff'] + $res['withheld']; // $pmtInfo->adj_amount;  
                            }
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            break;
                            
                        case 'Change Payment':
                            // Line item payment modified
                            break;

                        case 'Denials':
                            // Claim denied for the CPT
                            // Pmt : AAR?
                            // Denial/Remark Codes - PRM102,PRM100
                            $desc = '';
                            if ($responsibility != '') {
                                $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_paid_desc");
                                $desc = "\n" . str_replace("VAR_INS_NAME", $responsibility, $desc);
                            }

                            if(isset($res['paid']))
                              $pmts = @$res['paid'];
                            if (@$res['deduction'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['deduction'], $resp);
                            }
                            if (@$res['coins'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['coins'], $resp);
                            }
                            if (@$res['copay'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['copay'], $resp);
                            }
                            // Other adjustment details shown instead of withheld.
                                $adjs = ClaimInfoV1::getClaimOtherAdjDetails($res['id'], @$res['claim_cpt_txn_id'], @$res['cpt_id']);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".Helpers::priceFormat($adjRec['adj_amt']);
                                  }
                                }
                                $desc .=$adj_resp;
                            if (@$res['writeoff'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['writeoff'], $resp);
                            }
                            if (@$res->denial_code != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $res->denial_code))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$res['writeoff'] !=0 || @$res['withheld'] != 0 ){
                                $adj = $res['writeoff'] + $res['withheld']; // $pmtInfo->adj_amount;  
                            } 
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            break;

                        case 'Insurance Refund':
                            // Refunded to insurance
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$res['payment_id']);
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            $pmts = @$res['paid'];
                            break;

                        case 'Patient Refund':
                            // Refunded to patient
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.refund_txn_desc");
                            $desc = str_replace("VAR_SHORT_NAME", 'Patient', $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$res['payment_id']);
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            $pmts = $res['paid'];
                            break;

                        case 'Patient Adjustment':
                            // Patient payment adjustmented
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_adj_txn_desc"); // pat_adj_txn
                            $pmtInfo = PMTInfoV1::getPaymentInfoDetailsById(@$res['payment_id']);
                            $reason = (@$pmtInfo->pmtadjustment_details['adjustment_shortname'] != '' ) ?@$pmtInfo->pmtadjustment_details['adjustment_shortname'] : @$pmtInfo->pmtadjustment_details['adjustment_reason'];
                            $desc = str_replace("VAR_REASON", $reason, $desc);
                            $adj = @$res['writeoff'] + @$res['withheld']; // $pmtInfo->adj_amount;
                            break;

                        case 'Insurance Adjustment':
                            // Insurance payment adjusted
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.ins_adj_txn_desc");
                            $resp = ($res['claim_cpt_value1'] > 0 && $res['claim_cpt_value1'] != "") ? ClaimInfoV1::GetInsuranceName($res['claim_cpt_value1']) : $responsibility;
                            $desc = str_replace("VAR_REASON", $resp, $desc);
                            //$pmtInfo = PMTInfoV1::getPaymentInfoById($claimId, @$res['payment_id']);
                            $pmts = @$res['paid'];
                            //$total_charge = ($ins_bal + $pat_bal) - $adj;

                            if (@$res['deduction'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_ded_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['deduction'], $resp);
                            }
                            if (@$res['coins'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coins_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['coins'], $resp);
                            }
                            if (@$res['copay'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_coppay_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['copay'], $resp);
                            }
                            //if (@$res['withheld'] != 0) {
                            // Other adjustment details shown instead of withheld.
                                $adjs = ClaimInfoV1::getClaimOtherAdjDetails($res['id'], @$res['claim_cpt_txn_id'], @$res['cpt_id']);
                                $adj_resp = '';
                                if(!empty($adjs)) {
                                  foreach($adjs as $adjRec) {
                                    $adj_resp .="\n" .$adjRec['adj_code'].": ".$adjRec['adj_amt'];
                                  }
                                }
                                $desc .=$adj_resp;
                                /*
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.pmt_withheld_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['withheld'], $resp);
                                */
                            //}
                            // if adjustment applied it needs to append
                            if (@$res['writeoff'] != 0) {
                                $resp = Lang::get("payments/claim_transaction.cpt_txn.ins_pmt_adj_txn_desc");
                                $desc .= "\n" . str_replace("VAR_TXN_AMOUNT", $res['writeoff'], $resp);
                            }
                            if($res['value_2'] != '') {
                                $denial_desc = Lang::get("payments/claim_transaction.cpt_txn.denial_code_desc");
                                $denail_codes = implode(':', array_filter(array_unique(explode(',', $res['value_2']))));
                                $desc .= "\n" . str_replace("VAR_CODES", rtrim($denail_codes, ':'), $denial_desc);
                            }
                            // if writeoff / withheld provided then its sum in adjustment column
                            if(@$res['writeoff'] !=0 || @$res['withheld'] != 0 ){
                                $adj = $res['writeoff'] + $res['withheld']; // $pmtInfo->adj_amount;  
                            }
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            break;

                        case 'Edit Charge':
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.charge_updated_desc");
                            // Need to take charge amount from claim cpt desc value json string if available, otherwise take from cpt_info
                            if($res['claim_cpt_value1'] != '') {
                            $descDet = json_decode($res['claim_cpt_value1'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                if(!empty($descDet) && isset($descDet['charge_amt']) && trim($descDet['charge_amt']) != '') {
                                  $charges = trim($descDet['charge_amt']);
                                }
                              }
                            }
                            break;

                        case 'Wallet':
                            // Wallet transaction made for an CPT
                            //   - |  - | 1/4/2018 | Patient | Excess amount 17 moved to wallet?| - | -17 | 0 |  0 |  80
                            $pmts = number_format($res['claim_cpt_value1'], 2);
                            $desc = Lang::get("payments/claim_transaction.claim_txn.excess_wallet_transfer_desc");
                            $pmtsVal = ($pmts < 0 ) ? -1 * $pmts : $pmts;
                            $desc = str_replace("VAR_AMOUNT", $pmtsVal, $desc);
                            $pmts = -1*$pmts;
                            break;

                        case 'Void Check':
                            if($res['value_2'] != '' && $res['value_2'] == 1)
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_ins_check_desc");
                            else{
                              $desc = Lang::get("payments/claim_transaction.claim_txn.void_check_desc"); 
                              $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['value_2'] = "Patient";
                            }
                            $pmts = @$res['paid'];
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            $desc = str_replace("VAR_TXN_AMOUNT", $pmts, $desc);
                            break;

                        case 'Patient Credit Balance':
                        case 'Credit Balance':
                            $desc = Lang::get("payments/claim_transaction.cpt_txn.pat_cr_bal_txn_desc");
                            // Handle credit balance
                            if($res['claim_cpt_value1'] != "" ){
                              $descDet = json_decode($res['claim_cpt_value1'], true);
                              if (json_last_error() === JSON_ERROR_NONE && is_array($descDet)) {
                                foreach ($descDet as $key => $detVal) {
                                  $pmtInfo = PMTInfoV1::with('checkDetails','creditCardDetails','eftDetails')
                                             // ->where('source_id', $claimId)
                                              ->where('id', @$detVal['pmt_info_id'])->first();
                                  if(!empty($pmtInfo)){
                                    if($pmtInfo->pmt_mode == 'Check'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CHK# ".@$pmtInfo->checkDetails['check_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'EFT'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from EFT# ".@$pmtInfo->eftDetails['eft_no'];  
                                    } elseif($pmtInfo->pmt_mode == 'Credit'){
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CREDIT# ".@$pmtInfo->creditCardDetails['card_last_4']; 
                                    } else {
                                      $desc .= "\n Pmt: " .$detVal['amountApplied']." from CASH"; 
                                    }
                                  }
                                }
                              } 
                            }
                            $pmt_type = @$res->pmt_mode;
                            if ($pmt_type == 'Check') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "Chk No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'Money Order') {
                                $pmt = PMTCheckInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('check_no')->first();
                                $pmt_type = "MO No. " . @$pmt->check_no;
                            } elseif ($pmt_type == 'EFT') {
                                $pmt = PMTEFTInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('eft_no')->first();
                                $pmt_type = "EFT No. " . @$pmt->eft_no;
                            } elseif ($pmt_type == 'Credit') {
                                $pmt = PMTCardInfoV1::where('id', $res['pmt_mode_id'])
                                            ->selectRaw('card_last_4')->first();
                                $pmt_type = (@$pmt->card_last_4 != '') ? "Credit Card No. " . @$pmt->card_last_4 : "Credit Card";
                            }
                            // handle value2 txn ids and make it sum
                            if(trim($res['value_2']) != "" ){
                              $txnIds = array_filter(array_unique(explode(',', $res['value_2'])));
                              $pmts = 0;
                              foreach ($txnIds as $key => $txId) {
                                $txnAmt = PMTInfoV1::getClaimCptTxAmtById($txId);
                                $pmts +=$txnAmt; 
                              }
                            }
                            break;

                        case 'Submitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Submitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.submitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_edi_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Resubmitted Paper': // Handle Edi Submitted
                            $desc = Lang::get("payments/claim_transaction.claim_txn.resubmitted_paper_ins_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;      

                        case 'Payer Rejected':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Payer Accepted':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_payer_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Rejection':  // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_rej_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        case 'Clearing House Accepted': // Handle ClearingHouse Status
                            $desc = Lang::get("payments/claim_transaction.claim_txn.clearinghouse_acc_desc");
                            $desc = str_replace("VAR_SHORT_NAME", $responsibility, $desc);
                            break;

                        default:
                            $desc = $res['transaction_type'];
                            break;
                    }
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['desc'] = $desc;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['adj'] = $adj;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['pmt_type'] = $pmt_type;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['pmts'] = $pmts;
                $patients[$res->id]['CPT'][$res->cpt_id][$res->cpt_tx_desc_id]['responsibility'] = $responsibility;
                $i++;
            }
        }
        // To check export or view
        if(isset($request['exports']) && $request['exports'] == 'pdf'){
            $patients = $patients;
            return compact('patients','header','wallet');
        } elseif(isset($request['export']) && $request['export'] == 'xlsx'){
            $patients = $patients;
            return compact('patients','header','wallet');
        } else{
            $pagination = [];
            $p = Input::get('page', 1);
            $paginate = 2;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($patients, $offSet, $paginate,true);
            $patients = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($patients), $paginate,$p,['path'=>Request::url()]);
            $report_array = $patients->toArray();
            //dd($report_array);
            $pagination_prt = $patients->render();
            
            // Default pagination if single page
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
                // To set pagination datas
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            
            $patient = $patients;
        }
            return view('reports/patients/itemizedBill/report', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData','patient','header','pagination','wallet'));
    }
        
        // ----------------------------- Start - Export Patient - Itemized Bill -------------------------
        public function itemizedBillSearchExport($export = '',$data = ''){
            // Send request and get data
            $patients = $this->itemizedBillSearch($export, $data); // DB 
            $patient = $patients['patients'];
            $wallet = $patients['wallet'];
            $header = $patients['header'];
            $date = date('m-d-Y');
            // $name = $data['export_id'].'X0X'.'Wallet_Balance_' . $date;
            $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
            $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

            $request = Request::all();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
              $report_name = 'Patient - Itemized Bill';
              $view_path = 'reports/patients/itemizedBill/report_export_pdf';
              $data = ['patient' => $patient, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'header' => $header];
              return $data;
            }        
            if ($export == 'xlsx' || $export == 'csv') {
                $filePath = 'reports/patients/itemizedBill/report_export';
                $data['patient'] = $patient;
                $data['wallet'] = $wallet;
                $data['createdBy'] = $createdBy;
                $data['practice_id'] = $practice_id;
                $data['header'] = $header;
                $data['file_path'] = $filePath;
                $data['export'] = $export;
                return $data;
                // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
                $type = '.xls';
            }
            // Status change to report_export_task table
            if(isset($data['export_id'])){
                ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
            }
        }
    // ------------------------ End - Export Patient - Itemized Bill ------------------------------------
}