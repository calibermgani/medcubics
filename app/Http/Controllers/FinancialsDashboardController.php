<?php 
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Session;
use DB;
use Response;
use App\Http\Controllers\Medcubics\Api\DBConnectionController;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use Carbon\Carbon;
use App\Http\Helpers\Helpers;
class FinancialsDashboardController extends Controller {
    public function __construct() {
        View::share('heading', 'Dashboard');
        View::share('selected_tab', 'dashboard');
        View::share('heading_icon', 'dashboard');
        $new = new DBConnectionController();
        View::share('checkpermission', $new); 
        $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : 0;
        $new->connectPracticeDB($practice_id);
    }
    public function dashboard(Request $request) {
        $request = $request->all();
        $selected_tab = "payment-dashboard";

        if(isset($request['transaction_date'])){
            $exp = explode('-', $request['transaction_date']);
            $request['start_date'] = date('Y-m-d',strtotime($exp[0]));
            $request['end_date'] = date('Y-m-d',strtotime($exp[1]));
        }
        //Billed charges
            $billed = $this->getClaimBilledTotalCharge($request); 
            $billedpercentage = $this->getCurrentMonthBilledPercentage($request);

        // Unbilled charges
            $unbilled = $this->getClaimUnbilledTotalCharge($request);
            $unbilled_percentange = $this->getCurrentMonthUnbilledPercentage($request);

        // Hold charges
            $claimsCount = ClaimInfoV1::count();
            $hold = $this->getChargesHoldApi($claimsCount,$request)->getData()->data;
        
        //Edi Rejection
            $edirejection = $this->getEdiRejectionApi($request);  
            $edirejection_percentage = $this->getCurrentMonthEdiRejectionPercentage($request);  

        //Denied claims
            $chargesDenied = $this->getDeniedApi($claimsCount,$request)->getData()->data;
        
        // Submitted Claims
            $submittedClaims = $this->getReadyToSubmitApi($claimsCount,$request)->getData()->data;

        // Collections Breakup - By Responsibility
            $collectionsBreakup = $this->collectionsBreakup($request);
            $months = $collectionsBreakup['months'];
            $self = $collectionsBreakup['self'];
            $primary = $collectionsBreakup['primary'];
            $secondary = $collectionsBreakup['secondary'];
            $tertiary = $collectionsBreakup['tertiary'];

        // Top 10 CPT's
            $top_ten_cpt = $this->topTenCpt($request);
            $cpt_label = $top_ten_cpt['cpt_label'];
            $current_year_cpt_value = $top_ten_cpt['current_year_cpt_value'];
            $last_year_cpt_value = $top_ten_cpt['last_year_cpt_value'];

        // Top ten Payers
            $top_ten_payer = $this->topTenPayer($request);

        // Collections Vs Adjustments
            $collectionVsAdjustments = $this->collectionVsAdjustments($request);
            $col_vs_adj_months = $collectionVsAdjustments['col_vs_adj_months'];
            $collections = $collectionVsAdjustments['collections'];
            $adjustments = $collectionVsAdjustments['adjustments'];

        // Collections Breakup - By Payers
            $collectionsBreakupByPayers = $this->collectionsBreakupByPayers($request);
            $payers_month = $collectionsBreakupByPayers['payers_month'];
            $payers = $collectionsBreakupByPayers['payers'];

        if(isset($request['filterBy'])){
            return compact('billed', 'billedpercentage', 'unbilled', 'unbilled_percentange', 'hold', 'edirejection', 'edirejection_percentage', 'chargesDenied', 'submittedClaims', 'months','self', 'primary', 'secondary', 'tertiary', 'current_year_cpt_value', 'last_year_cpt_value', 'cpt_label', 'payers_month', 'payers', 'col_vs_adj_months', 'collections', 'adjustments', 'top_ten_payer', 'selected_tab');
        }else{
            return view('dashboard/payment', compact('billed', 'billedpercentage', 'unbilled', 'unbilled_percentange', 'hold', 'edirejection', 'edirejection_percentage', 'chargesDenied', 'submittedClaims', 'months','self', 'primary', 'secondary', 'tertiary', 'current_year_cpt_value', 'last_year_cpt_value', 'cpt_label', 'payers_month', 'payers', 'col_vs_adj_months', 'collections', 'adjustments', 'top_ten_payer', 'selected_tab'));
        }
    }

    // Billed charges
    function getClaimUnbilledTotalCharge($request) {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $unbilledcharge = ClaimInfoV1::where('insurance_id','!=',0)->where('claim_submit_count', 0)->where('status', '!=', 'paid');
        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $unbilledcharge->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $unbilledcharge->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $unbilledcharge->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }
        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $exp = explode('-',Helpers::getPracticeCreatedDate());
            $pratice_create_date = date('Y-m-d', strtotime($exp[0]));
            $cur_date = date('Y-m-d');
            $start = date('Y-m-d', strtotime($request['start_date']));
            $end = date('Y-m-d', strtotime($request['end_date']));
            if(strtotime($pratice_create_date) < strtotime($start)){
                $unbilledcharge->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
            }
        }
        return $unbilledcharges = $unbilledcharge->sum('total_charge');
    }

    // Billed charges percentage
    function getCurrentMonthUnbilledPercentage($request) { 
        $practice_timezone = Helpers::getPracticeTimeZone();    
        $last_months = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')->where('insurance_id','!=',0)->where('claim_submit_count', 0);

        $current_months = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                        ->where('insurance_id','!=',0)->where('claim_submit_count', 0);

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $last_months->whereIn('facility_id',$request['facility_id']);
            $current_months->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $last_months->whereIn('billing_provider_id',$request['billing_provider_id']);
            $current_months->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $last_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
            $current_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }                        
        
        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $last_months->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
            $current_months->whereRaw("claim_info_v1.created_at >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $last_month = $last_months->sum('total_charge');
        $current_month = $current_months->sum('total_charge');

        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }
   
   // Unbilled charges
    function getClaimBilledTotalCharge($request) {
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $billed_charges = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    });

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $billed_charges->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $billed_charges->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $billed_charges->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){ 
            $billed_charges->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }
        $billed = $billed_charges->sum('total_charge'); 
        return $billed;
    }
    
    // Unbilled charges percentage
    function getCurrentMonthBilledPercentage($request) { 
    $practice_timezone = Helpers::getPracticeTimeZone();                
        $last_months = ClaimInfoV1::where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->whereRaw('MONTH(created_at) =  MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)');      
        
        $current_months = ClaimInfoV1::where(function($qry){
                            $qry->where(function($query){ 
                                $query->whereIn('status', ['Ready'])->where('claim_submit_count', '>' ,0);
                            })->orWhereIn('status', ['Patient','Paid','Submitted','Denied','Rejection']);               
                        })->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())');    

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $last_months->whereIn('facility_id',$request['facility_id']);
            $current_months->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $last_months->whereIn('billing_provider_id',$request['billing_provider_id']);
            $current_months->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $last_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
            $current_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){ 
            $last_months->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
            $current_months->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $last_month = $last_months->sum('total_charge');
        $current_month = $current_months->sum('total_charge');

        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;              
    }

    //Hold charges
    function getChargesHoldApi($claimCount = 0,$request) {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $chargesHold_claim = ClaimInfoV1::whereIn('status', ['Hold']);
        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $chargesHold_claim->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $chargesHold_claim->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $chargesHold_claim->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $chargesHold_claim->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $chargesHoldValue = (int) $chargesHold_claim->count();
        // Get Percentage
        $chargesHoldcount = $claimCount; //Claims::count();     
        $chargesHoldPercentage = ($chargesHoldcount != 0) ? round(($chargesHoldValue / $chargesHoldcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesHoldValue', 'chargesHoldPercentage')));
    }

    //Rejected Claims
    function getEdiRejectionApi($request) {        
        $resp = $this->getClaimStats('rejected',$request);
        return $resp['rejected']['total_amount'];
    }

    //Rejected claims percentage
    function getCurrentMonthEdiRejectionPercentage($request) {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $last_months = ClaimInfoV1::whereIn('status', ['Rejection'])
                      ->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)');
        $current_months = ClaimInfoV1::whereIn('status', ['Rejection'])
                      ->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())');

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $last_months->whereIn('facility_id',$request['facility_id']);
            $current_months->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $last_months->whereIn('billing_provider_id',$request['billing_provider_id']);
            $current_months->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $last_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
            $current_months->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $last_months->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
            $current_months->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $last_month = $last_months->sum('total_charge');
        $current_month = $current_months->sum('total_charge');

        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }

    //Denied Claims
    function getDeniedApi($claimCount = 0,$request) {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $chargesDenied_claim = ClaimInfoV1::where('status', "Denied");
        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $chargesDenied_claim->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $chargesDenied_claim->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $chargesDenied_claim->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $chargesDenied_claim->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $chargesDeniedValue = (int) $chargesDenied_claim->count();
        // Get Percentage
        $chargesDeniedcount = $claimCount; // Claims::count();      
        $chargesDeniedPercentage = ($chargesDeniedcount != 0) ? round(($chargesDeniedValue / $chargesDeniedcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesDeniedValue', 'chargesDeniedPercentage')));
    }

    // Submitted Claims
    function getReadyToSubmitApi($claimCount = 0,$request) { 
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $ReadyToSubmitValue = ClaimInfoV1::whereNotIn('status', ['Hold']);
        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $ReadyToSubmitValue->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $ReadyToSubmitValue->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $ReadyToSubmitValue->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }

        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $ReadyToSubmitValue->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        $ReadyToSubmitValue = (int) $ReadyToSubmitValue->sum('claim_submit_count');
        // Get Percentage
        $ReadyToSubmitcount = $claimCount;  
        $ReadyToSubmitPercentage = ($ReadyToSubmitcount != 0) ? round(($ReadyToSubmitValue / $ReadyToSubmitcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('ReadyToSubmitValue', 'ReadyToSubmitPercentage')));
    }

   // Collections Breakup - By Responsibility
    function collectionsBreakup($request){
      
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $end_date = date('Y-m-d H:i:s',strtotime(Carbon::now($practice_timezone)));
        $InsuranceCollections = PMTClaimTXV1::selectRaw('sum(total_paid) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"),"%b") as month, DATE_FORMAT(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as key_value, ins_category as ins_category, pmt_claim_tx_v1.created_at,pmt_method')/*->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    })*/
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->whereRaw("CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."') >= DATE_SUB(  UTC_TIMESTAMP(), INTERVAL 5 month )")
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')),ins_category"));

        //Patient Collections
        //For patient collection, patient refund must be subtracted with patient payment from DB (See documentation for reason)
        $PatientCollections = PMTInfoV1::selectRaw('SUM(pmt_amt) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%b") as month, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as key_value, "patient" as ins_category, pmt_info_v1.created_at,pmt_method')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->where('void_check',Null)
                ->where('pmt_info_v1.deleted_at',Null)
                ->whereRaw("CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."') >= DATE_SUB(  UTC_TIMESTAMP(), INTERVAL 5 month )")
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."'))"));
        
        $PatientRefundCollections = PMTInfoV1::selectRaw('sum(pmt_amt) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%b") as month, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as key_value, "refund" as ins_category, pmt_info_v1.created_at,pmt_method')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Refund'])
                ->where('source','posting')
                ->where('void_check',Null)
                ->where('pmt_info_v1.deleted_at',Null)
                ->whereRaw("CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."') >= DATE_SUB(  UTC_TIMESTAMP(), INTERVAL 5 month )")
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."'))"));
        if(isset($request['filterBy']) && !empty($request['filterBy'])) {
                $InsuranceCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id');
                $PatientCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_info_v1.source_id');
                $PatientRefundCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_info_v1.source_id');
        }

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $PatientCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $PatientCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $PatientCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
        } 
        $values = $InsuranceCollections->unionAll($PatientCollections->getQuery())->unionAll($PatientRefundCollections->getQuery())->orderBy('created_at','asc')->get()->toArray();
        $primary = $secondary = $tertiary = $self = $p = $s = $t = $sf = [];
        if(isset($values) && !empty($values))
        foreach($values as $v){
            //$month[$v['month']]['label'] = $v['key_value'];
            if($v['ins_category'] =='patient')
                $key = 'self';
            else
                $key = $v['ins_category'];
            if($v['ins_category'] == '' && $v['pmt_method']=='Insurance'){
                $key = 'Primary_ins';
            } 
            if($v['ins_category']=='refund')
            $arrays[$v['key_value']]['self']['value'] = (isset($arrays[$v['key_value']]['self']['value']))? $arrays[$v['key_value']]['self']['value'] - ($v['total_paid']):-$v['total_paid'];
          
            else
            $arrays[$v['key_value']][$key]['value'] = $v['total_paid'];
            
        }
        if(isset($values) && !empty($values)){
            $j=5;
            for($i=0;$i<6;$i++){
                $m = date('y_m', strtotime("-$j month"));
                $month[date('M', strtotime("-$j month"))]['label'] = date('M', strtotime("-$j month"));
                if(!array_key_exists($m,$arrays)){
                    $arrays[$m]['primary']['value'] = 0;
                }
                --$j;
            }
            ksort($arrays);
            if(isset($arrays) && !empty($arrays))
                foreach($arrays as $key => $value){
                    if(isset($value['self']))
                        $self[]['value'] = $value['self']['value'];
                    else
                        $self[]['value'] = 0;
                    if(isset($value['Primary']) && isset($value['Primary_ins']))
                        $primary[]['value'] = $value['Primary']['value']+$value['Primary_ins']['value'];
                    elseif(isset($value['Primary'])) 
                        $primary[]['value'] = $value['Primary']['value'];
                    else
                        $primary[]['value'] = 0;
                    if(isset($value['Secondary']))
                        $secondary[]['value'] = $value['Secondary']['value'];
                    else
                        $secondary[]['value'] = 0;
                    if(isset($value['Tertiary']))
                        $tertiary[]['value'] = $value['Tertiary']['value'];
                    else
                        $tertiary[]['value'] = 0;
            }
            if(!empty($month))
                foreach($month as $val){
                    $months[] = $val;
                }
        }else{
            $months = [];
            $self = [];
            $primary = [];
            $secondary =[];
            $tertiary = [];
        }
        $result['collections'] = $values;
        $result['months'] = $months;
        $result['self'] = $self;
        $result['primary'] = $primary;
        $result['secondary'] = $secondary;
        $result['tertiary'] = $tertiary;
        return $result;
    }
    // Top Ten Cpt
    function topTenCpt($request){
        $cpt_currents = ClaimCPTInfoV1::selectRaw('sum(unit) as value, cpt_code')->whereRaw('YEAR(claim_cpt_info_v1.created_at) = YEAR(UTC_TIMESTAMP())');
        
        $cpt_lasts = ClaimCPTInfoV1::selectRaw('sum(unit) as value,cpt_code')->whereRaw("YEAR(claim_cpt_info_v1.created_at) = YEAR(DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 YEAR))");

        /*$fArr1 =  array_merge($cpt_current, array_fill_keys(array_keys(array_diff_key($cpt_last, $cpt_current)), 0));
        $fArr2 =  array_merge($cpt_last, array_fill_keys(array_keys(array_diff_key($cpt_current, $cpt_last)), 0));
        dd($fArr2);*/
        if(isset($request['filterBy']) && !empty($request['filterBy'])) {
                $cpt_currents->leftjoin('claim_info_v1','claim_info_v1.id','=','claim_cpt_info_v1.claim_id');
                $cpt_lasts->leftjoin('claim_info_v1','claim_info_v1.id','=','claim_cpt_info_v1.claim_id');
        }

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $cpt_currents->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $cpt_lasts->whereIn('claim_info_v1.facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $cpt_currents->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $cpt_lasts->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $cpt_currents->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $cpt_lasts->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
        }  
        $cpt_current = $cpt_currents->groupBy('cpt_code')->orderBy('value','desc')->limit(10)->pluck('value','cpt_code')->all();
        $cpt_last = $cpt_lasts->groupBy('cpt_code')->orderBy('value','desc')->limit(10)->pluck('value','cpt_code')->all();
        if((isset($cpt_current) && !empty($cpt_current)) || (isset($cpt_last) && !empty($cpt_last))){
            $temp1 = array_diff_key($cpt_current,$cpt_last);
            $temp2 = array_diff_key($cpt_last,$cpt_current);
            foreach($temp2 as $key=>$val){
                $cpt_current[$key] = 0;
            }

            foreach($temp1 as $key=>$val){
                $cpt_last[$key] = 0;
            }

            foreach ($cpt_current as $key => $val) {
                $current_year_cpt_value[]['value'] =$val;
                $cpt_label[]['label'] = trim($key);
                $last_year_cpt_value[]['value'] = $cpt_last[$key];
            }
        }else{
            $cpt_label = [];
            $current_year_cpt_value = [];
            $last_year_cpt_value = [];            
        }

        $result['cpt_label'] = $cpt_label;
        $result['current_year_cpt_value'] = $current_year_cpt_value;
        $result['last_year_cpt_value'] = $last_year_cpt_value;
        return $result;
    }

    // Top ten Payers
    function topTenPayer($request){
        $top_ten_payer = ClaimInfoV1::join('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')
                    ->join('insurances', 'pmt_claim_tx_v1.payer_insurance_id', '=', 'insurances.id')
                    ->where('pmt_claim_tx_v1.pmt_type','payment')
                    ->selectRaw('if(insurances.short_name is null or insurances.short_name = "", insurances.insurance_name,insurances.short_name) as label, sum(pmt_claim_tx_v1.total_paid) as value')
                    ->groupBy('insurances.id');

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $top_ten_payer->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $top_ten_payer->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $top_ten_payer->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }  

        $top_ten_payer =$top_ten_payer->orderBy('value','desc')->limit(10)->get()->toArray();
        return $top_ten_payer;
    }

    // Collections Vs Adjustments
    function collectionVsAdjustments($request){
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $end_date = date('Y-m-d H:i:s',strtotime(Carbon::now($practice_timezone)));
        $InsuranceCollections = PMTClaimTXV1::selectRaw('sum(total_paid) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as month, ins_category, pmt_claim_tx_v1.created_at')/*->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    })*/
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->whereRaw("pmt_claim_tx_v1.created_at >= DATE_SUB(UTC_TIMESTAMP, INTERVAL 5 month )")
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."'))"));
        //Patient Collections
        //For patient collection, patient refund must be subtracted with patient payment from DB (See documentation for reason)
        $PatientCollections = PMTInfoV1::selectRaw('SUM(pmt_amt) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as month, "patient" as ins_category, pmt_info_v1.created_at')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->where('void_check',Null)
                ->where('pmt_info_v1.deleted_at',Null)
                ->whereRaw('pmt_info_v1.created_at >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -5 month)')
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."'))"));
        
        $PatientRefundCollections = PMTInfoV1::selectRaw('sum(pmt_amt) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as month, "refund" as ins_category, pmt_info_v1.created_at')
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Refund'])
                ->where('source','posting')
                ->where('void_check',Null)
                ->where('pmt_info_v1.deleted_at',Null)
                ->whereRaw('pmt_info_v1.created_at >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -5 month)')
                ->groupBy(DB::raw("month(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."'))"));
                //(sum(total_withheld+total_writeoff)/(sum(total_paid)+sum(total_withheld+total_writeoff)))*100 
        $adjustment = PMTClaimTXV1::selectRaw('sum(total_withheld+total_writeoff) as value,DATE_FORMAT(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"),"%y_%m") as month')->whereRaw('pmt_claim_tx_v1.created_at >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -5 month)');

        if(isset($request['filterBy']) && !empty($request['filterBy'])) {
            $InsuranceCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id');
            $PatientCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_info_v1.source_id');
            $PatientRefundCollections->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_info_v1.source_id');
            $adjustment->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id');
        }

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $PatientCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.facility_id',$request['facility_id']);
            $adjustment->whereIn('claim_info_v1.facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $PatientCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            $adjustment->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $InsuranceCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $PatientCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $PatientRefundCollections->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
            $adjustment->whereIn('claim_info_v1.rendering_provider_id',$request['rendering_provider_id']);
        }  
        $values = $InsuranceCollections->unionAll($PatientCollections->getQuery())->unionAll($PatientRefundCollections->getQuery())->orderBy('created_at','asc')->get()->toArray();
        $adjustments = $adjustment->groupBy(DB::raw("MONTH(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."'))"))->orderBy('pmt_claim_tx_v1.created_at','asc')->pluck('value','month')->all();
        if(isset($values) && !empty($values))
            foreach($values as $v){
                if($v['ins_category']=='refund'){
                    $arrays[$v['month']]['refund'][] = $v['total_paid'];
                }
                else{
                    $arrays[$v['month']]['total_paid'][] = $v['total_paid'];
                }
            }
        if(isset($values) && !empty($values)){
            $j=5;
            for($i=0;$i<6;$i++){
                $m = date('y_m', strtotime("-$j month"));
                $month[date('M', strtotime("-$j month"))]['label'] = date('M', strtotime("-$j month"));
                if(!array_key_exists($m,$arrays)){
                    $arrays[$m]['total_paid'][] = 0;
                    $arrays[$m]['refund'][] =0;
                }
                --$j;
            }
            ksort($arrays);
            if(isset($arrays) && !empty($arrays))
                foreach($arrays as $key=>$val){
                        if(isset($adjustments[$key]))
                            $adjs[]['value'] = $adjustments[$key];
                        else
                            $adjs[]['value'] = 0;
                    if(!empty($val['refund'])){
                        $total_paid = (!empty($val['total_paid']))?array_sum($val['total_paid']):0;
                        $collections[]['value'] = $total_paid-array_sum($val['refund']);
                    }
                    else{
                        $collections[]['value'] = (!empty($val['total_paid']))?array_sum($val['total_paid']):0;
                    }
                }
            if(!empty($month))
            foreach($month as $val){
                $months[] = $val;
            }
        }else{
            $months = [];
            $collections = [];
            $adjs = [];
        }
        $result['col_vs_adj_months'] = $months;
        $result['collections'] = $collections;
        $result['adjustments'] = $adjs;
        return $result;
    }

    // Collections Breakup - By Payers
    function collectionsBreakupByPayers($request){
            $practice_timezone = Helpers::getPracticeTimeZone();  
            $collectionPayers = ClaimInfoV1::join('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')->join('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                    ->join('insurances', 'pmt_claim_tx_v1.payer_insurance_id', '=', 'insurances.id')
                    ->selectRaw('sum(claim_info_v1.insurance_id) as insurances_count, claim_info_v1.insurance_id as ins_id, if(insurances.short_name is null or insurances.short_name = "", insurances.insurance_name,insurances.short_name) as short_name, sum(pmt_claim_tx_v1.total_paid) as total_paid, DATE_FORMAT(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"),"%b") as date, MONTH(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'")) as month, insurance_name')
                    ->where('pmt_claim_tx_v1.total_paid','!=',0)->wherenull('pmt_info_v1.deleted_at')
                    //->wherenull('pmt_info_v1.void_check')
                    ->whereRaw('YEAR(pmt_claim_tx_v1.created_at) = YEAR(UTC_TIMESTAMP())');
            if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
                $collectionPayers->whereIn('facility_id',$request['facility_id']);
            }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
                $collectionPayers->whereIn('billing_provider_id',$request['billing_provider_id']);
            }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
                $collectionPayers->whereIn('rendering_provider_id',$request['rendering_provider_id']);
            } 

            $collectionPayer = $collectionPayers->groupBy('insurances.id',DB::raw('MONTH(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"))'))->orderBy('month')->get()->toArray();

            $payers = []; 
            $payers_month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];        
            
            foreach($payers_month as $pm){
                foreach($collectionPayer as $p){
                        if($pm==$p['date']){
                            $payers[$p['insurance_name']][$p['date']] = $p['total_paid'];
                        }
                }
            }
            $result['payers_month'] = $payers_month;
            $result['payers'] = $payers;
            return $result;
    }

    //Claim statistics
    function getClaimStats($type, $request, $date_range='All', $patient_id=0){
        $resp = [];
        $type = strtolower($type);
        $practice_timezone = Helpers::getPracticeTimeZone();   
        $claims = DB::table('claim_info_v1')  ->leftjoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')->select(DB::raw('sum(claim_info_v1.total_charge) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'),'claim_info_v1.id');

        if((isset($request['filterBy']) && $request['filterBy']=="Facility") && !empty($request['facility_id'])){
            $claims->whereIn('facility_id',$request['facility_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Billing Provider") && !empty($request['billing_provider_id'])){
            $claims->whereIn('billing_provider_id',$request['billing_provider_id']);
        }elseif((isset($request['filterBy']) && $request['filterBy']=="Rendering Provider") && !empty($request['rendering_provider_id'])){
            $claims->whereIn('rendering_provider_id',$request['rendering_provider_id']);
        }
        
        // Filter by Transaction Date
        if(isset($request['start_date'])){
            $claims->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$request['start_date']."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$request['end_date']."'");
        }

        switch ($type) {

            case 'billed':
                 $claims->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->whereRaw('MONTH(claim_info_v1.created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(claim_info_v1.created_at) = YEAR(UTC_TIMESTAMP())');                 
                break;
            
            case 'unbilled':
                 $claims->where(function($qry){
                    $qry->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count',0)->where('claim_info_v1.status', '!=', 'paid');
                }); 
                break;

            case 'hold':
                $claims->whereIn('status', ['Hold']);
                break;   

            case 'rejected':
                $claims->whereIn('status', ['Rejection']);
                break;   
            
            case 'all':                
                $billed = SELF::getClaimStats('billed',$date_range);                
                $unbilled = SELF::getClaimStats('unbilled',$date_range);                                
                $hold = SELF::getClaimStats('hold',$date_range);
                $rejected = SELF::getClaimStats('rejected',$date_range);  

                return array_merge_recursive($billed, $unbilled, $hold, $rejected);                             
                break;                    
        }

        // Date range handled date fo service field
        if(trim($date_range) != '' && trim(strtolower($date_range)) != 'all') {
            $date = explode('-',trim($date_range));
            $from = date("Y-m-d", strtotime(@$date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime(@$date[1]));
            $claims->where(function($query) use ($from, $to){ 
                $query->whereRaw('DATE(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'")) >='.$from)->whereRaw('DATE(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'")) <='.$to);
            });
        }
        if($patient_id != 0) {
            $claims->where('patient_id', $patient_id);
        }
        $claims->whereNull('claim_info_v1.deleted_at');

        $rec = $claims->first();

        if(!empty($rec)) {      
            $total_amount = (!empty($rec->total_amt) )? $rec->total_amt : 0;
            $resp[$type]['total_amount'] =  $total_amount;
            $resp[$type]['total_charges'] = isset($rec->total_charges) ? $rec->total_charges : 0;
        } else {
            $resp[$type]['total_amount'] = $resp[$type]['total_charges'] = 0;
        }
        return $resp;
    }
}
