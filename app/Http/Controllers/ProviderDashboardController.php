<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use Redirect;
use Auth;
use View;
use DB;
use Session;
use App\Dashboard;
use Config;
use App\Http\Controllers\Api\DashboardApiController as DashboardApiController;
use Response;
use App\Models\Eras;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Patients\ProblemList as ProblemList;
use App\Models\Document as Document;
use Cache;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;

class ProviderDashboardController extends Api\ProviderDashboardApiController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        View::share('heading', 'Dashboard');
        View::share('selected_tab', 'dashboard');
        View::share('heading_icon', 'dashboard');
    }

    public function index(Request $request) {
        $selected_tab = "provider-dashboard";
        $user_id = Auth::user()->id;
        $practice_id = (Session::get('practice_dbid')) ? $practice_id = Session::get('practice_dbid') : 4;
        $stats_list = Cache::remember('stats_listnewProvider' . $user_id, 30, function() use($user_id) {

            $stats_list['cpt_code'] = [];//Claims::select(DB::raw('count(*) as order_count, cpt_codes'))
                            //->groupBy('cpt_codes')->orderBy('order_count', 'DESC')->take(5)->get();
            $stats_list['result'] = DB::table('personal_notes')->select('*')->where('user_id', '=', $user_id)->where('date', date('Y-m-d'))->get();
            $stats_list['api_UnBilled'] = $this->getProviderUnBilledApi();                          
            $stats_list['api_UnbilledPercentage'] = $this->getProviderUnbilledPercentageApi();      
            $stats_list['api_EdiRejection'] = $this->getProviderEdiRejectionApi();         
            $stats_list['api_EdiRejectionPercentage'] = $this->getProviderEdiRejectionPercentage(); 
            $stats_list['api_Billed'] = $this->getProviderBilledApi();
            $stats_list['api_BilledPercentageApi'] = $this->getProviderBilledPercentageApi();                  
            
            $stats_list['api_InsPayment'] = $this->getProviderInsPaymentApi();//gdd($stats_list);
            $stats_list['api_PatPayment'] = $this->getProviderPatPaymentApi();
            $stats_list['api_Outstanding_ar'] = $this->getProviderOutstandingArApi();
            $stats_list['api_Insurance_ar'] = $this->getProviderInsuranceArApi();
            $stats_list['api_Patient_ar'] = $this->getProviderPatintArApi();
            $stats_list['api_Chart_data'] = $this->getProviderDashboardChartApi();

            // Dashboard Bottom Left
            $stats_list['api_CleanClaims'] = $this->getProviderCleanClaimsApi();
            $stats_list['api_InsurancePercentage'] = $this->getProviderInsPercentageApi();
            $stats_list['api_PatPymtPercentage'] = $this->getProviderPatPercentageApi();
            $stats_list['api_OutstandingArApi'] = $this->getProviderOutstandingArPercentageApi();
            $data = $this->getProviderDashboardChartApi();
               //
               //  dd($stats_list);
            //** Calculate percentage for last week **//    
           /* $stats_list['api_Weekly_percent_Billed'] = $this->getWeekly_percent_BilledApi();
            $stats_list['api_Weekly_percent_UnBilled'] = $this->getWeekly_percent_UnBilledApi();
            $stats_list['api_Weekly_percent_InsPayment'] = $this->getWeekly_percent_InsPaymentApi();
            $stats_list['api_Weekly_percent_PatPayment'] = $this->getWeekly_percent_PatPaymentApi();
*/
           //$stats_list['api_Weekly_percent_Outstanding_ar'] = $this->getWeekly_percent_Outstanding_arApi();

            $claimsCount = ClaimInfoV1::count();
            // Ready To Submit  
            $api_readyToSubmit = $this->getProviderReadyToSubmitApi($claimsCount);
            $api_response_data = $api_readyToSubmit->getData();
            $stats_list['ReadyToSubmitValue'] = $api_response_data->data->ReadyToSubmitValue;
           // $stats_list['ReadyToSubmitPercentage'] = $api_response_data->data->ReadyToSubmitPercentage;

            // Charges Hold     
            $api_chargesHold = $this->getProviderChargesHoldApi($claimsCount);
            $api_response_data = $api_chargesHold->getData();
            $stats_list['chargesHoldValue'] = (isset($api_response_data->data)) ? $api_response_data->data->chargesHoldValue : 0;
            //$stats_list['chargesHoldPercentage'] = $api_response_data->data->chargesHoldPercentage;

            // Denied
            $api_chargesDenied = $this->geProviderDeniedApi($claimsCount);
            $api_response_data = $api_chargesDenied->getData();
            $stats_list['chargesDeniedValue'] = $api_response_data->data->chargesDeniedValue;
            //$chargesDeniedPercentage = $api_response_data->data->chargesDeniedPercentage;
            // Pending
            $api_chargesPending = $this->getProviderPendingApi($claimsCount);
            $api_response_data = $api_chargesPending->getData();
            $stats_list['chargesPendingValue'] = $api_response_data->data->chargesPendingValue;
            //$chargesPendingPercentage = $api_response_data->data->chargesPendingPercentage; 
            // Dashboard Top
           // $stats_list['count_val'] = PatientAppointment::dashboardEncounter();
            $stats_list['netcollections'] = $this->getProviderNetCollectionsApi();

          
            $eraCount = Eras::count();
            $api_chargesEra = $this->getProviderERAApi($eraCount); 
            $api_response_data = $api_chargesEra->getData();
            $stats_list['chargesEraValue'] = $api_response_data->data->chargesEraValue;
       
            $provider_id = Auth::user()->provider_access_id;
            $stats_list['ar_days'] = \App\Http\Helpers\Helpers::Providerardays();
            $stats_list['problem_list_count'] = ProblemList::getProviderProblemListCount_User();
            $stats_list['document_count'] = Document::getPracticeDocumentAssignedCountByUser();
            $stats_list['ediRejectionCount'] = ClaimInfoV1::where('status','Rejection')->where('rendering_provider_id',$provider_id)->count();
            // Start sidebar notification by baskar -04/02/19
            $stats_list['workbench_by_user'] = 0;//ProblemList::getProviderProblemListCountByUser();
            $stats_list['document_by_user'] = 0;//Document::getPracticeDocumentAssignedCountByUser();
            // End sidebar notification by baskar -04/02/19
            
            // Pie chart
            //$AgingPieValue1='1000';       
            //$total_charge = $this->getProviderClaimsTotalCharges(); //Claims::sum('total_charge');
            $total_charge =ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                        })->orWhere('claim_info_v1.insurance_id',0);
                    })->where('claim_info_v1.rendering_provider_id',$provider_id)
                ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
            $stats_list['AgingPiePercentValue0_30'] = round($this->getProviderAgingPiePercentValueApi0_30($total_charge));
            $stats_list['AgingPiePercentValue31_60'] = round($this->ProviderAgingPiePercentValue31_60($total_charge));
            $stats_list['AgingPiePercentValue61_90'] = round($this->ProviderAgingPiePercentValue61_90($total_charge));
            $stats_list['AgingPiePercentValue91_120'] = round($this->ProviderAgingPiePercentValue91_120($total_charge));
            $stats_list['AgingPiePercentValue121_150'] = round($this->ProviderAgingPiePercentValue121_150($total_charge));
            $stats_list['AgingPiePercentValue150_above'] = round($this->ProviderAgingPiePercentValue150_above($total_charge));
            //chart 
            $chargeBar = [];
            /*
            $chargeBar = Claims::select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"))
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("month(created_at)"))
                    ->whereNotIn('status', ['Hold'])
                    ->lists('total_charge', 'monthNum');
            */
            $chargeBar = $this->getProviderMonthWiseChargeBar('total_charge');
            $chargeArray = array();

            $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
           /* foreach ($monthArray as $month) {
                $chargeArray[$month] = in_array($month, array_flip($chargeBar)) ? $chargeBar[$month] : 0;
            }*/
           /* $stats_list['chargeArray'] = array_values($chargeArray);
            $collectionBar = PMTInfoV1::select(DB::raw('sum(pmt_amt) as payment_amt'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"))
                    ->where('void_check', NULL)
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("month(created_at)"))
                    ->whereIn('pmt_type', ['Payment'])
                    ->lists('payment_amt', 'monthNum');*/
            
            $temp = array();
            $overAll = array();
            $i = 0;
            foreach ($monthArray as $key => $month) {
                $temp['charge'][$i]['value'] = $data[$month]['Charge'];
                $temp['PatientPayment'][$i]['value'] = $data[$month]['PatientPayment'];
                $temp['InsPayment'][$i]['value'] = $data[$month]['InsPayment'];
                $temp['Adjustment'][$i]['value'] = $data[$month]['Adjustment'];
                array_push($overAll, $temp);
                $i = 0;
            }
            $stats_list['charge'] = array_column($overAll, 'charge');
            $stats_list['patpayment'] = array_column($overAll, 'PatientPayment');
            $stats_list['inspayment'] = array_column($overAll, 'InsPayment');
            $stats_list['adjustment'] = array_column($overAll, 'Adjustment');
            /*$paymentArray = array();
            foreach ($monthArray as $month) {
                $paymentArray[$month] = in_array($month, array_flip($collectionBar)) ? $collectionBar[$month] : 0;
            }*/
          //  $stats_list['paymentArray'] = array_values($paymentArray);
            return $stats_list;
        });

        return view('dashboard/provider', compact('stats_list','selected_tab'))                       
                ->with('AgingPiePercentValue0_30', json_encode($stats_list['AgingPiePercentValue0_30'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue31_60', json_encode($stats_list['AgingPiePercentValue31_60'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue61_90', json_encode($stats_list['AgingPiePercentValue61_90'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue91_120', json_encode($stats_list['AgingPiePercentValue91_120'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue121_150', json_encode($stats_list['AgingPiePercentValue121_150'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue150_above', json_encode($stats_list['AgingPiePercentValue150_above'], JSON_NUMERIC_CHECK))
                ->with('charge_data', json_encode(@$stats_list['charge']))
                ->with('pat_payment', json_encode(@$stats_list['patpayment']))
                ->with('ins_payment', json_encode(@$stats_list['inspayment']))
                ->with('adjsutment', json_encode(@$stats_list['adjustment']));  
    }

    public function dashboard1() {
        $selected_tab = "charge-analysis";
        return view('dashboard/dashboard1', compact('selected_tab'));
    }

    public function scheduling_dashboard() {
        $selected_tab = "scheduling-dashboard";
        return view('dashboard/scheduler', compact('selected_tab'));
    }

    public function payment_dashboard() {
        $selected_tab = "payment-dashboard";
        return view('dashboard/payment', compact('selected_tab'));
    }

    public function ar_dashboard() {
        $selected_tab = "ar-dashboard";
        return view('dashboard/armanagement', compact('selected_tab'));
    }

    // Refresh cache for dashboard stats
    public function refreshStats() {
        $practice_id = (Session::get('practice_dbid')) ? Session::get('practice_dbid') : 4;
        Cache::forget('stats_listnewProvider' . Auth::user()->id);
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
    }

}
