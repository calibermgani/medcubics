<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as User;
use App\Models\Eras;
use Input;
use Auth;
use Response;
use Request;
use App;
use DB;
use Lang;
use Carbon\Carbon;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Traits\ProviderClaimUtil;

class ProviderDashboardApiController extends Controller {
    use ProviderClaimUtil;
    
    /* Dashboard Top: Starts */    
    //Dashboard Top: Un Billed Charges
    //Desc: Total Billed Charges of the practice
    //Tech: Sum of total_charges: Claims which are not submitted even once, and claims whose Status are 'Hold' and 'Pending'
    public function getProviderUnBilledApi() {
        //$resp = Helpers::getClaimStats('unbilled');
        //return $resp['unbilled']['total_amount'];
        return $this->getProviderClaimUnbilledTotalCharge();
    }

    //Unbilled Monthly variations
    public function getProviderUnbilledPercentageApi() {        
        return $this->getProviderCurrentMonthUnbilledPercentage();
    }

    //Dashboard Top: EDI Rejections
    //Desc: Accounts receivable for the current practice
    //Tech: Sum of total patient due and insurance due where status is not 'Hold' or 'Pending'
    public function getProviderEdiRejectionApi() {        
        $resp = $this->getProviderClaimStats('rejected');
        return $resp['rejected']['total_amount'];
        //return $this->getEdiRejectionTotal();
    }

    //EDI Rejection Monthly variations
    public function getProviderEdiRejectionPercentage() {        
        return $this->getCurrentMonthEdiRejectionPercentage();
    }

    //Dashboard Top: Billed Charges
    //Desc: Total Billed Charges of the practice
    //Tech: Sum of total_charges: Claims which are not submitted even once, and claims whose Status are 'Ready' and 'Pending'
    public function getProviderBilledApi() {
        //$resp = $this->getClaimStats('billed');
        // return $resp['billed']['total_amount'];           
        return $this->getProviderClaimBilledTotalCharge();
    }

    public function getProviderBilledPercentageApi() {        
        return $this->getProviderCurrentMonthBilledPercentage();
    }

    //Dashboard Top: Insurance Payment
    //Desc: Insurance payment received for the current month
    //Tech: Sum of insurance_paid
    public function getProviderInsPaymentApi() {
    $getinsPmt = Helpers::getProviderCurrentMonthYearTotalCollections();
        return Helpers::priceFormat($getinsPmt['payment_month'],'yes');
    }
    //Dashboard Top: Patient Payment
    //Desc: Total Patient payment received
    //Tech: Sum of patient_paid
    public function getProviderPatPaymentApi() {
        $getpatPmt = Helpers::getProviderCurrentMonthYearTotalCollections();
        return Helpers::priceFormat(($getpatPmt['pat_payment_month'] - $getpatPmt['pat_refund_month']),'yes');
    }  
    //Dashboard Top: Outstanding AR
    //Desc: Accounts receivable for the current practice
    //Tech: Sum of total patient due and insurance due where status is not 'Hold' or 'Pending'
    public function getProviderOutstandingArApi() {       
        return $this->getClaimTotalOutstandingAr();
    }

    public function getProviderInsuranceArApi() {        
        return $this->getClaimTotalInsuranceAr();
    }

    public function getProviderPatintArApi() {        
        return $this->getClaimTotalPatintAr();
    }
    /*     * *  Get charges for PIE Chart   ** */
    public function getProviderDashboardChartApi() { 
        $provider_id = Auth::user()->provider_access_id; 
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $practice_timezone = Helpers::getPracticeTimeZone();
        $chargeBar = [];
        $chargeBar = DB::table('claim_info_v1')->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->select(DB::raw('sum(claim_info_v1.total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
            ->orderBy("claim_info_v1.created_at")
            ->where("claim_info_v1.rendering_provider_id",$provider_id)
            ->groupBy(DB::raw("month(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'))"))
            ->whereRaw('year(claim_info_v1.created_at) = year(UTC_TIMESTAMP())')
            ->whereNull('claim_info_v1.deleted_at')
            ->get('patient_paid', 'monthNum', 'total_charge')->toArray();             
         //   $chargeBar = $chargeBar;  
            
        $i = 0;
        $data = [];

        foreach ($chargeBar as $key => $value) {  
            $data[$value->monthNum]['Charge'] = $value->total_charge;
        }
        
        $patientPmt = $patientRefundPmt = [];
        $patientPmt = PMTInfoV1::with('claim')->where('pmt_method','Patient')->whereIn('pmt_type',['Payment','Credit Balance'])->where('void_check',Null)
                       ->whereHas('claim', function($q)use($provider_id) {
                            $q->where('rendering_provider_id', $provider_id);
                        })
                        ->where('deleted_at',Null)
                        ->select(DB::raw('sum(`pmt_amt`) as `pmt_amt`'),DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
                        ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                        ->whereRaw('year(pmt_info_v1.created_at) = year(UTC_TIMESTAMP())')
                        ->get()->toArray();
        foreach($patientPmt as $key => $value){ 
            $data[$value['monthNum']]['PatientPayment'] = $value['pmt_amt'];
        }

        $patientRefundPmt = PMTInfoV1::with('claim')->where('pmt_method','Patient')->whereIn('pmt_type',['Refund'])
                           ->whereHas('claim', function($q)use($provider_id) {
                                $q->where('rendering_provider_id', $provider_id);
                            })
                            ->where('source','posting')->where('void_check',Null)
                            ->where('deleted_at',Null)
                            ->select(DB::raw('sum(`pmt_amt`) as `pmt_amt`'),DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
                            ->whereRaw('year(pmt_info_v1.created_at) = year(UTC_TIMESTAMP())')
                            ->groupBy(DB::raw("month(created_at)"))->get()->toArray();
        
        foreach($patientRefundPmt as $key => $value){
            $data[$value['monthNum']]['PatientPayment'] = isset($data[$value['monthNum']]['PatientPayment']) ? ($data[$value['monthNum']]['PatientPayment'] - $value['pmt_amt']) : 0;
        }
        $insurancePmt = PMTClaimTXV1::with('claim')->where('pmt_method','Insurance')->whereIn('pmt_type',['Payment','Refund'])
                       ->whereHas('claim', function($q)use($provider_id) {
                            $q->where('rendering_provider_id', $provider_id);
                        })
                        ->select(DB::raw('sum(`total_paid`) as `total_paid`'),DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
                        ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                        ->where('deleted_at',Null)
                        ->whereRaw('year(created_at) = year(UTC_TIMESTAMP())')->get()->toArray();
        
        foreach($insurancePmt as $key => $value){
            $data[$value['monthNum']]['InsPayment'] = $value['total_paid'];
        }
        
        $adjPmt = PMTClaimTXV1::with('claim')->whereIn('pmt_method',['Insurance','Patient'])->whereIn('pmt_type',['Payment','Adjustment'])
                   ->whereHas('claim', function($q)use($provider_id) {
                        $q->where('rendering_provider_id', $provider_id);
                    })
                    ->select(DB::raw('sum(`total_withheld` + `total_writeoff`) as `adjustment`'),DB::raw("DATE_FORMAT(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
                    ->groupBy(DB::raw("month(CONVERT_TZ(created_at,'UTC','".$practice_timezone."'))"))
                    ->where('deleted_at',Null)
                    ->whereRaw('year(created_at) = year(UTC_TIMESTAMP())')->get()->toArray();
        
        foreach($adjPmt as $key => $value){
            $data[$value['monthNum']]['Adjustment'] = $value['adjustment'];
        }
        
        foreach ($monthArray as $key => $value) {
            if (!array_key_exists($value, $data)) {
                $data[$value]['Charge'] = "0.00";
                $data[$value]['PatientPayment'] = "0.00";
                $data[$value]['InsPayment'] = "0.00";
                $data[$value]['Adjustment'] = "0.00";
            } else {
                if(!array_key_exists('Charge', $data[$value]))
                    $data[$value]['Charge'] = "0.00";
                if(!array_key_exists('PatientPayment', $data[$value]))
                    $data[$value]['PatientPayment'] = "0.00";
                if(!array_key_exists('InsPayment', $data[$value]))
                    $data[$value]['InsPayment'] = "0.00";
                if(!array_key_exists('Adjustment', $data[$value]))
                    $data[$value]['Adjustment'] = "0.00";
            }
        }
        return $data;
    }

    //Dashboard Bottom Left: Clean Claims
    //Desc: Percentage of accepted claims in a practice per day
    //Tech: Accepted Claims / Total Claims per day without resubmission 'claim_submit_count',=,'1'

    public function getProviderCleanClaimsApi() {
        $provider_id = Auth::user()->provider_access_id;
        $total_claims = ClaimTXDESCV1::with('claim_info')->whereIn('transaction_type', ['Submitted', 'Resubmitted'])->whereRaw('DATE(created_at) = DATE(UTC_TIMESTAMP())')->whereHas('claim_info', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })->count();
        
        $accepted_claims = ClaimTXDESCV1::with('claim_info')->whereIn('transaction_type', ['Clearing House Accepted'])->whereRaw('DATE(created_at) = DATE(UTC_TIMESTAMP())')->whereHas('claim_info', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })->count();
        if ($total_claims != '0')
            return ($accepted_claims / $total_claims) * 100;
        else
            return 0;
    }
    //Dashboard Top: Percentage 
    public function getProviderInsPercentageApi() {       
        return $this->getCurrentMonthInsPaidPercentage();
    }
   
  
    public function getProviderPatPercentageApi() {        
        return $this->getCurMonPatPercentage();
    } 

    public function getProviderOutstandingArPercentageApi() {        
        return $this->getCurMonOutstandingArPerc();
    }

    /* Dashboard Top: Ends */
     /** To Do List* */
    public function getProviderReadyToSubmitApi($claimCount = 0) {   // Get Value
        $provider_id = Auth::user()->provider_access_id;   
        $ReadyToSubmitValue = ClaimInfoV1::whereIn('status', ['Ready'])->where('rendering_provider_id',$provider_id)->whereNull('deleted_at');
        $ReadyToSubmitValue = (int) $ReadyToSubmitValue->count();
        // Get Percentage
        $ReadyToSubmitcount = $claimCount;  
        $ReadyToSubmitPercentage = ($ReadyToSubmitcount != 0) ? round(($ReadyToSubmitValue / $ReadyToSubmitcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('ReadyToSubmitValue', 'ReadyToSubmitPercentage')));
    }

    public function getProviderChargesHoldApi($claimCount = 0) {
        $provider_id = Auth::user()->provider_access_id;  
        $chargesHold_claim = ClaimInfoV1::whereIn('status', ['Hold'])->where('rendering_provider_id',$provider_id)->whereNull('deleted_at');
        $chargesHoldValue = (int) $chargesHold_claim->count();
        // Get Percentage
        $chargesHoldcount = $claimCount; //Claims::count();     
        $chargesHoldPercentage = ($chargesHoldcount != 0) ? round(($chargesHoldValue / $chargesHoldcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesHoldValue', 'chargesHoldPercentage')));
    }

    public function geProviderDeniedApi($claimCount = 0) {
        $provider_id = Auth::user()->provider_access_id;
        $chargesDenied_claim = ClaimInfoV1::where('status', "Denied")->where('rendering_provider_id',$provider_id)->whereNull('deleted_at');
        $chargesDeniedValue = (int) $chargesDenied_claim->count();
        // Get Percentage
        $chargesDeniedcount = $claimCount; // Claims::count();      
        $chargesDeniedPercentage = ($chargesDeniedcount != 0) ? round(($chargesDeniedValue / $chargesDeniedcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesDeniedValue', 'chargesDeniedPercentage')));
    }

    public function getProviderPendingApi($claimCount = 0) {
        $provider_id = Auth::user()->provider_access_id;  
        $chargesPending_claim = ClaimInfoV1::where('status', "Pending")->where('rendering_provider_id',$provider_id)->whereNull('deleted_at');
        $chargesPendingValue = (int) $chargesPending_claim->count();
        // Get Percentage
        $chargesPendingcount = $claimCount; //Claims::count();     
        $chargesPendingPercentage = ($chargesPendingcount != 0) ? round(($chargesPendingValue / $chargesPendingcount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesPendingValue', 'chargesPendingPercentage')));
    }

    /* Dashboard Bottom Left: Starts */

    public function getProviderNetCollectionsApi() {        
        return $this->getClaimTotalCollections();
    }
   
     public function getProviderERAApi($eraCount = 0) {
        $Eras_claim = Eras::where('status', 'No');
        $chargesEraValue = (int) $Eras_claim->count();
        // Get Percentage
        $chargesEracount = $eraCount; // Eras::count();     
        $chargesEraPercentage = ($chargesEracount != 0) ? round(($chargesEraValue / $chargesEracount) * 100, 2) : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('chargesEraValue', 'chargesEraPercentage')));
    }

    public function getProviderAgingPiePercentValueApi0_30($total_charge = 0) {
        $last_month_carbon = 30;
        $current_month = 0;
        
        /*$total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$last_month_carbon.' day)')->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge'); */
        $maincond = $this->getProviderClaimsTotalCreatedBetween($last_month_carbon, $current_month);
        return $percentage = ($total_charge != 0) ? ($maincond / $total_charge)*100 : 0;
    }

    public function ProviderAgingPiePercentValue31_60($total_charge = 0) {
        // $claims = Claims::with('patient','insurance_details')->where('claim_submit_count','=',0);            
        $last_month_carbon = 60;
        $current_month = 31;
        /*$total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$last_month_carbon.' day)')->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge'); */
        $maincond = $this->getProviderClaimsTotalCreatedBetween($last_month_carbon, $current_month);
        //Claims::where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month)->sum('total_charge');
        //$total_charge = Claims::sum('total_charge');
        return $percentage = ($total_charge != 0) ?($maincond / $total_charge)*100 : 0;
    }

    public function ProviderAgingPiePercentValue61_90($total_charge = 0) {
        //$claims = Claims::with('patient','insurance_details')->where('claim_submit_count','=',0);         
        $last_month_carbon = 90;
        $current_month = 61;
        /*$total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$last_month_carbon.' day)')->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge');*/
        $maincond = $this->getProviderClaimsTotalCreatedBetween($last_month_carbon, $current_month);
        //Claims::where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month)->sum('total_charge');
        //$total_charge = Claims::sum('total_charge');          
        return $percentage = ($total_charge != 0) ?($maincond / $total_charge)*100 : 0;
    }

    public function ProviderAgingPiePercentValue91_120($total_charge = 0) {
        $user = Auth::user()->id;
        // $claims = Claims::with('patient','insurance_details')->where('claim_submit_count','=',0);            
        $last_month_carbon = 120;
        $current_month = 91;
        /*$total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$last_month_carbon.' day)')->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge');*/
        $maincond = $this->getProviderClaimsTotalCreatedBetween($last_month_carbon, $current_month);
        //Claims::where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month)->sum('total_charge');
        //$total_charge = Claims::sum('total_charge');
        return $percentage = ($total_charge != 0) ?($maincond / $total_charge)*100 : 0;
    }

    public function ProviderAgingPiePercentValue121_150($total_charge = 0) {
        //$claims = Claims::with('patient','insurance_details')->where('claim_submit_count','=',0);         
        $last_month_carbon = 150;
        $current_month = 121;
       /* $total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$last_month_carbon.' day)')->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge');*/
        $maincond = $this->getProviderClaimsTotalCreatedBetween($last_month_carbon, $current_month);
        //Claims::where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month)->sum('total_charge');
        //$total_charge = Claims::sum('total_charge');
        return $percentage = ($total_charge != 0) ?($maincond / $total_charge)*100 : 0;
    }


    public function ProviderAgingPiePercentValue150_above($total_charge = 0) {
        //$claims = Claims::with('patient','insurance_details')->where('claim_submit_count','=',0);    
        $last_month_carbon = 'above';       
        $current_month = 151;
        /*$total_charge = ClaimInfoV1::where('rendering_provider_id',Auth::user()->provider_access_id)->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$current_month.' day)')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->sum('total_charge'); */
        $maincond = $this->getProviderClaimsTotalCreatedBetween($current_month, $last_month_carbon);
        // Claims::where('created_at', '<=', $current_month)->sum('total_charge');
        //$total_charge = Claims::sum('total_charge');              
        return $percentage = ($total_charge != 0) ?($maincond / $total_charge)*100 : 0;
    }

    /*     * *********  Get Last First week Charge  ********************* */

    public function getProviderLastFirstweekDates() {
        $cur_date = strtotime(date('Y-m-d')); // Change to whatever date you need
        // Get the day of the week: Sunday = 0 to Saturday = 6
        $previousweekcurdate = $cur_date - (7 * 24 * 3600);
        $cur_date = $previousweekcurdate;
        $dotw = date('w', $cur_date);
        if ($dotw > 1) {
            $pre_sunday = $cur_date - (($dotw - 1) * 24 * 60 * 60) - (24 * 60 * 60);
            $next_satday = $cur_date + ((7 - $dotw) * 24 * 60 * 60) - (24 * 60 * 60);
        } elseif ($dotw == 1) {
            $pre_sunday = $cur_date - (24 * 60 * 60);
            $next_satday = $cur_date + ((7 - $dotw) * 24 * 60 * 60) - (24 * 60 * 60);
        } elseif ($dotw == 0) {
            $pre_sunday = $cur_date - (6 * 24 * 60 * 60) - (24 * 60 * 60);
            $next_satday = $cur_date - (24 * 60 * 60);
        }
        $get_last_first_sunday = date('Y-m-d', $pre_sunday);
        $get_last_first_satday = date('Y-m-d', $next_satday);

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('get_last_first_sunday', 'get_last_first_satday')));
    }

    /*     * *********  Get Last Second week Charge  ********************* */

    public function getLastSecondweekDates() {
        $cur_date = strtotime(date('Y-m-d')); // Change to whatever date you need
        // Get the day of the week: Sunday = 0 to Saturday = 6
        $previousweekcurdate = $cur_date - (14 * 24 * 3600);
        $cur_date = $previousweekcurdate;
        $dotw = date('w', $cur_date);
        if ($dotw > 1) {
            $pre_sunday = $cur_date - (($dotw - 1) * 24 * 60 * 60) - (24 * 60 * 60);
            $next_satday = $cur_date + ((7 - $dotw) * 24 * 60 * 60) - (24 * 60 * 60);
        } elseif ($dotw == 1) {
            $pre_sunday = $cur_date - (24 * 60 * 60);
            $next_satday = $cur_date + ((7 - $dotw) * 24 * 60 * 60) - (24 * 60 * 60);
        } elseif ($dotw == 0) {
            $pre_sunday = $cur_date - (6 * 24 * 60 * 60) - (24 * 60 * 60);
            $next_satday = $cur_date - (24 * 60 * 60);
        }
        $get_last_second_sunday = date('Y-m-d', $pre_sunday);
        $get_last_second_satday = date('Y-m-d', $next_satday);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('get_last_second_sunday', 'get_last_second_satday')));
    }

    /** calculate charge billed * */
    public function getWeekly_percent_BilledApi() {
        /*
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;

        $firstWeekChargeBilled = Claims::whereNotIn('status', ['Hold'])->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('total_charge');
        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekChargeBilled = Claims::whereNotIn('status', ['Hold'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('total_charge');

        // Calculate percentage of carge        
        return $firstWeekChargeBilled == 0 ? 0 : (($firstWeekChargeBilled - $secondWeekChargeBilled) / $firstWeekChargeBilled) * 100;
        */
        return $this->getWeeklyBilledPercent();
    }

    /** calculate charge UnBilled * */
    public function getWeekly_percent_UnBilledApi() {
        /*
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekChargeUnBilled = Claims::whereIn('status', ['Ready'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('total_charge');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekChargeUnBilled = Claims::whereIn('status', ['Ready'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('total_charge');

        // Calculate percentage of carge        
        return $firstWeekChargeUnBilled == 0 ? 0 : (($firstWeekChargeUnBilled - $secondWeekChargeUnBilled) / $firstWeekChargeUnBilled) * 100;
        */
        return $this->getWeeklyUnBilledPercent();
    }

    /** calculate charge Insurance payment * */
    public function getWeekly_percent_InsPaymentApi() {
        /*
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekChargeInsPayment = Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('insurance_paid');
        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekChargeInsPayment = Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('insurance_paid');

        // Calculate percentage of carge        
        return $firstWeekChargeInsPayment == 0 ? 0 : (($firstWeekChargeInsPayment - $secondWeekChargeInsPayment) / $firstWeekChargeInsPayment) * 100;
        */
        return $this->getWeeklyInsPaymentPercentage();
    }

    /** calculate charge Pat payment * */
    public function getWeekly_percent_PatPaymentApi() {
        /*
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekChargePatPayment = Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('patient_paid');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;
        $secondWeekChargePatPayment = Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('patient_paid');

        // Calculate percentage of carge            
        return $firstWeekChargePatPayment == 0 ? 0 : (($firstWeekChargePatPayment - $secondWeekChargePatPayment) / $firstWeekChargePatPayment) * 100;
        */
        return $this->getWeeklyPatPaymentPercent();
    }

    /** calculate charge Outstanding AR payment * */
    public function getWeekly_percent_Outstanding_arApi() {
        /*
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekChargeOutstanding_ar = Claims::where("patient_id", "!=", "0")->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum(DB::raw('patient_due + insurance_due'));

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekChargeOutstanding_ar = Claims::where("patient_id", "!=", "0")->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum(DB::raw('patient_due + insurance_due'));
        // Calculate percentage of carge
        return $firstWeekChargeOutstanding_ar == 0 ? 0 : (($firstWeekChargeOutstanding_ar - $secondWeekChargeOutstanding_ar) / $firstWeekChargeOutstanding_ar) * 100;
        */
        return $this->getWeeklyOutstandingPercent();
    }   

    public function getProblemlistApi() {
        
    }

    /** Key Performance Indicator* */
    public function getProceduresIndicatorApi() {
        /* Current month ICD used in charges */
        $curr_start_date = Carbon::now()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString(); //(date("Y-m-d"));
        $claimdoscptdetails = ClaimCPTInfoV1:: whereNotIn('cpt_code', ['patient'])
        ->where('created_at', '<=', $end_date)
        ->where('created_at', '>=', $curr_start_date)
        ->pluck('cpt_icd_map_key')->all();
        $icd = $result = [];
        foreach ($claimdoscptdetails as $key => $claimdoscptdetail)
            $icd[] = explode(',', $claimdoscptdetail);
        foreach ($icd as $key => $value)
            $result = array_merge($result, $value);
        $cur_mnth_cpt_icd = count(array_unique($result));
        /* Last month ICD used in charges count */
        $last_mnth_start_date = Carbon::now()->subMonth()->toDateString();
        $last_mnth_end_date = Carbon::now()->endOfMonth()->subMonth()->toDateString(); //(date("Y-m-d"));
        $last_mnth_claimdoscptdetails = ClaimCPTInfoV1:: whereNotIn('cpt_code', ['patient'])->where('created_at', '<=', $last_mnth_end_date)->where('created_at', '>=', $last_mnth_start_date)->pluck('cpt_icd_map_key')->all();
        $last_mnth_icd = $last_mnth_result = [];
        foreach ($last_mnth_claimdoscptdetails as $key => $claimdoscptdetail)
            $last_mnth_icd[] = explode(',', $claimdoscptdetail);
        foreach ($last_mnth_icd as $key => $value)
            $last_mnth_result = array_merge($last_mnth_result, $value);
        $last_mnth_cpt_icd = count(array_unique($last_mnth_result));
        if ($cur_mnth_cpt_icd > 0)
            $total_icd = ($cur_mnth_cpt_icd / ($cur_mnth_cpt_icd + $last_mnth_cpt_icd)) * 100;
        else
            $total_icd = 0;
        $procedures_amount['current_mnth'] = $cur_mnth_cpt_icd;
        $procedures_amount['total_icd'] = $total_icd;
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekProcedures = ClaimCPTInfoV1::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('charge');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;
        $secondWeekProcedures = ClaimCPTInfoV1::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('charge');
        $proceduresPercentage = $firstWeekProcedures == 0 ? 0 : (($firstWeekProcedures - $secondWeekProcedures) / $firstWeekProcedures) * 100;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('procedures_amount', 'proceduresPercentage')));
    }

    public function getChargesIndicatorApi() {
        $charges_amount = $this->getProviderClaimsTotalCharges(); // Claims::sum(DB::raw('total_charge'));

        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        //Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('total_charge');
        $firstWeekCharges = $this->getClaimsTotalCreatedBetween($get_last_first_sunday, $get_last_first_satday,'charge');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekCharges = $this->getClaimsTotalCreatedBetween($get_last_second_sunday, $get_last_second_satday,'charge');
        //Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('total_charge');
        $chargesPercentage = $firstWeekCharges == 0 ? 0 : (($firstWeekCharges - $secondWeekCharges) / $firstWeekCharges)* 100;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges_amount', 'chargesPercentage')));
    }

    public function getReceiptsIndicatorApi() {
        $receipts_amount = PMTInfoV1::whereIn('pmt_type', ['Payment'])->where('pmt_method', "Insurance")->orwhere('pmt_method', "Patient")->whereNotIn('source', ['refundwallet'])->sum('pmt_amt');
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekReceipts = PMTInfoV1::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->whereIn('pmt_type', ['Payment'])->where('pmt_method', "Insurance")->orwhere('pmt_method', "Patient")->whereNotIn('source', ['refundwallet'])->sum('pmt_amt');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekReceipts = PMTInfoV1::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->whereIn('pmt_type', ['Payment'])->where('pmt_method', "Insurance")->orwhere('pmt_method', "Patient")->whereNotIn('source', ['refundwallet'])->sum('pmt_amt');
        $receiptsPercentage = $firstWeekReceipts == 0 ? 0 : (($firstWeekReceipts - $secondWeekReceipts) / $firstWeekReceipts) * 100;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('receipts_amount', 'receiptsPercentage')));
    }

    //
    public function getAdjustmentsIndicatorApi() {
        $adjustments_amount = $this->getClaimsTotalAdjusted();//Claims::whereRaw("(total_adjusted <> '0' or total_withheld <> '0')")->sum('total_charge');
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekAdjustments = $this->getClaimsTotalCreatedBetween($get_last_first_sunday, $get_last_first_satday, 'adjustment');
        //Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->whereRaw("(total_adjusted <> '0' or total_withheld <> '0')")->sum('total_charge');
        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekAdjustments = $this->getClaimsTotalCreatedBetween($get_last_second_sunday, $get_last_second_satday, 'adjustment');
        // Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->whereRaw("(total_adjusted <> '0' or total_withheld <> '0')")->sum('total_charge');

        $adjustmentsPercentage = $firstWeekAdjustments == 0 ? 0 : (($firstWeekAdjustments - $secondWeekAdjustments) / $firstWeekAdjustments) * 100;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustments_amount', 'adjustmentsPercentage')));
    }

    public function getRefundsIndicatorApi() {
        $refunds_amount = PMTInfoV1::whereRaw('(source = "refundwallet" or pmt_type = "Refund")')->where('void_check', '!=', 1)->sum('amt_used');
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekrefunds = PMTInfoV1::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->whereRaw('(source = "refundwallet" or pmt_type = "Refund")')->sum('pmt_amt');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekrefunds = PMTInfoV1::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->whereRaw('(source = "refundwallet" or pmt_type = "Refund")')->sum('pmt_amt');

        $refundsPercentage = $firstWeekrefunds == 0 ? 0 : (($firstWeekrefunds - $secondWeekrefunds) / $firstWeekrefunds)* 100;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('refunds_amount', 'refundsPercentage')));
    }

    public function getARBalanceIndicatorApi() {
        $ARBalance_amount = $this->getClaimsTotalARBalance();//Claims::whereNotIn('status', ['Hold'])->sum('balance_amt');
        //last first week
        $get_preWeeks = $this->getLastFirstweekDates()->getData();
        $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
        $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
        $firstWeekARBalance = $this->getClaimsTotalCreatedBetween($get_last_first_sunday, $get_last_first_satday, 'ar_due');
        //Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->whereNotIn('status', ['Hold'])->sum('balance_amt');

        //last second week
        $get_secWeeks = $this->getLastSecondweekDates()->getData();
        $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
        $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

        $secondWeekARBalance = $this->getClaimsTotalCreatedBetween($get_last_second_sunday, $get_last_second_satday,'ar_due');
        // Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->whereNotIn('status', ['Hold'])->sum('balance_amt');

        $ARBalancePercentage = $firstWeekARBalance == 0 ? 0 : (($firstWeekARBalance - $secondWeekARBalance) / $firstWeekARBalance) * 100;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('ARBalance_amount', 'ARBalancePercentage')));
    }

}
