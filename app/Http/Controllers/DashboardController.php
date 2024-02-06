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

class DashboardController extends Api\DashboardApiController {

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
        $user_id = Auth::user()->id;
        $practice_id = (Session::get('practice_dbid')) ? $practice_id = Session::get('practice_dbid') : 4;
        $stats_list = Cache::remember('stats_listnew' . $practice_id, 30, function() use($user_id) {

            $stats_list['cpt_code'] = [];//Claims::select(DB::raw('count(*) as order_count, cpt_codes'))
                            //->groupBy('cpt_codes')->orderBy('order_count', 'DESC')->take(5)->get();
            $stats_list['result'] = DB::table('personal_notes')->select('user_id', 'date', 'notes')->where('user_id', '=', $user_id)->where('date', date('Y-m-d'))->get();
            $stats_list['api_UnBilled'] = $this->getUnBilledApi();                          
            $stats_list['api_UnbilledPercentage'] = $this->getUnbilledPercentageApi();      
            $stats_list['api_EdiRejection'] = $this->getEdiRejectionApi();         
            $stats_list['api_EdiRejectionPercentage'] = $this->getEdiRejectionPercentage(); 
            $stats_list['api_Billed'] = $this->getBilledApi();
            $stats_list['api_BilledPercentageApi'] = $this->getBilledPercentageApi();                  
            
            $stats_list['api_InsPayment'] = $this->getInsPaymentApi();
            $stats_list['api_PatPayment'] = $this->getPatPaymentApi();
            $stats_list['api_Outstanding_ar'] = $this->getOutstandingArApi();
            $stats_list['api_Insurance_ar'] = $this->getInsuranceArApi();
            $stats_list['api_Patient_ar'] = $this->getPatintArApi();
            $stats_list['api_Chart_data'] = $this->getDashboardChartApi();

            // Dashboard Bottom Left
            $stats_list['api_CleanClaims'] = $this->getCleanClaimsApi();
            $stats_list['api_InsurancePercentage'] = $this->getInsPercentageApi();
            $stats_list['api_PatPymtPercentage'] = $this->getPatPercentageApi();
            $stats_list['api_OutstandingArApi'] = $this->getOutstandingArPercentageApi();
            $data = $this->getDashboardChartApi();
               //
               //  dd($stats_list);
            //** Calculate percentage for last week **//	
           /* $stats_list['api_Weekly_percent_Billed'] = $this->getWeekly_percent_BilledApi();
            $stats_list['api_Weekly_percent_UnBilled'] = $this->getWeekly_percent_UnBilledApi();
            $stats_list['api_Weekly_percent_InsPayment'] = $this->getWeekly_percent_InsPaymentApi();
            $stats_list['api_Weekly_percent_PatPayment'] = $this->getWeekly_percent_PatPaymentApi();
*/
           //$stats_list['api_Weekly_percent_Outstanding_ar'] = $this->getWeekly_percent_Outstanding_arApi();

            $claimsCount = ClaimInfoV1::whereNull('deleted_at')->count();
            // Ready To Submit	
            $api_readyToSubmit = $this->getReadyToSubmitApi($claimsCount);
            $api_response_data = $api_readyToSubmit->getData();
            $stats_list['ReadyToSubmitValue'] = $api_response_data->data->ReadyToSubmitValue;
           // $stats_list['ReadyToSubmitPercentage'] = $api_response_data->data->ReadyToSubmitPercentage;

            // Charges Hold		
            $api_chargesHold = $this->getChargesHoldApi($claimsCount);
            $api_response_data = $api_chargesHold->getData();
            $stats_list['chargesHoldValue'] = (isset($api_response_data->data)) ? $api_response_data->data->chargesHoldValue : 0;
            //$stats_list['chargesHoldPercentage'] = $api_response_data->data->chargesHoldPercentage;

            // Denied
            $api_chargesDenied = $this->getDeniedApi($claimsCount);
            $api_response_data = $api_chargesDenied->getData();
            $stats_list['chargesDeniedValue'] = $api_response_data->data->chargesDeniedValue;
            //$chargesDeniedPercentage = $api_response_data->data->chargesDeniedPercentage;
            // Pending
            $api_chargesPending = $this->getPendingApi($claimsCount);
            $api_response_data = $api_chargesPending->getData();
            $stats_list['chargesPendingValue'] = $api_response_data->data->chargesPendingValue;
            //$chargesPendingPercentage = $api_response_data->data->chargesPendingPercentage; 
            // Dashboard Top
           // $stats_list['count_val'] = PatientAppointment::dashboardEncounter();
            $stats_list['netcollections'] = $this->getNetCollectionsApi();

            // Problem List
            // $api_problemlist_count = $this->getProblemlistApi(); - @@ not used
            // Era
            $eraCount = Eras::count();
            $api_chargesEra = $this->getERAApi($eraCount);
            $api_response_data = $api_chargesEra->getData();
            $stats_list['chargesEraValue'] = $api_response_data->data->chargesEraValue;
            //$chargesEraPercentage = $api_response_data->data->chargesEraPercentage;
            //** Key Performance Indicator **//
            // Procedures
           /* $api_Procedures = $this->getProceduresIndicatorApi();
            $api_response_data = $api_Procedures->getData();
            $stats_list['procedures_amount'] = $api_response_data->data->procedures_amount;
           $stats_list['proceduresPercentage'] = $api_response_data->data->proceduresPercentage;*/

            // Charges		
           /* $api_Charges = $this->getChargesIndicatorApi();
            $api_response_data = $api_Charges->getData();
            $stats_list['charges_amount'] = $api_response_data->data->charges_amount;
            $stats_list['chargesPercentage'] = $api_response_data->data->chargesPercentage;*/

            // Receipts
           /* $api_Receipts = $this->getReceiptsIndicatorApi();
            $api_response_data = $api_Receipts->getData();
            $stats_list['receipts_amount'] = $api_response_data->data->receipts_amount;
            $stats_list['receiptsPercentage'] = $api_response_data->data->receiptsPercentage;
*/
            //Adjustments
           /* $api_Adjustments = $this->getAdjustmentsIndicatorApi();
            $api_response_data = $api_Adjustments->getData();
            $stats_list['adjustments_amount'] = $api_response_data->data->adjustments_amount;
            $stats_list['adjustmentsPercentage'] = $api_response_data->data->adjustmentsPercentage;*/

            // Refunds
           /* $api_Refunds = $this->getRefundsIndicatorApi();
            $api_response_data = $api_Refunds->getData();
            $stats_list['refunds_amount'] = $api_response_data->data->refunds_amount;
            $stats_list['refundsPercentage'] = $api_response_data->data->refundsPercentage;*/

            // ARBalance
          /*  $api_ARBalance = $this->getARBalanceIndicatorApi();
            $api_response_data = $api_ARBalance->getData();
            $stats_list['ARBalance_amount'] = $api_response_data->data->ARBalance_amount;
            $stats_list['ARBalancePercentage'] = $api_response_data->data->ARBalancePercentage;*/

          /*  $stats_list['adjustment_percentage'] = PMTInfoV1::CheckInsuranceAdjusted();
            $stats_list['refund_amt_percentage'] = PMTInfoV1::CheckTotalRefund();
           
            $stats_list['last_week_days'] = ClaimInfoV1::arDays('week');*/
            // Get start date and end date for 182 days
            $stats_list['ar_days'] = \App\Http\Helpers\Helpers::ardays();
            $stats_list['problem_list_count'] = ProblemList::getProblemListCount_User();
            $stats_list['document_count'] = Document::getPracticeDocumentAssignedCount();
            $stats_list['ediRejectionCount'] = ClaimInfoV1::where('status','Rejection')->whereNull('deleted_at')->count();
            // Start sidebar notification by baskar -04/02/19
            $stats_list['workbench_by_user'] = ProblemList::getProblemListCountByUser();
            $stats_list['document_by_user'] = Document::getPracticeDocumentAssignedCountByUser();
            // Follow up problem list count for logged user
            $stats_list['followup_by_user'] = ProblemList::getDueProblemListCountByUser();
            // End sidebar notification by baskar -04/02/19
			
            // Pie chart
            //$AgingPieValue1='1000';		
            $total_charge = $this->getClaimsTotalCharges(); //Claims::sum('total_charge');

            $aging_value_total =$this->getAgingPiePercentValueApi0_30($total_charge)+$this->AgingPiePercentValue31_60($total_charge)+$this->AgingPiePercentValue61_90($total_charge)+$this->AgingPiePercentValue91_120($total_charge)+$this->AgingPiePercentValue121_150($total_charge)+$this->AgingPiePercentValue151_180($total_charge);
            //+$this->AgingPiePercentValue180_above($total_charge);      

            $stats_list['AgingPiePercentValue0_30'] = ($aging_value_total > 0) ? round(($this->getAgingPiePercentValueApi0_30($total_charge)/$aging_value_total)*100,2) : 0;
            $stats_list['AgingPiePercentValue31_60'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue31_60($total_charge)/$aging_value_total)*100,2) : 0;
            $stats_list['AgingPiePercentValue61_90'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue61_90($total_charge)/$aging_value_total)*100,2) : 0;
            $stats_list['AgingPiePercentValue91_120'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue91_120($total_charge)/$aging_value_total)*100,2) : 0;
            $stats_list['AgingPiePercentValue121_150'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue121_150($total_charge)/$aging_value_total)*100,2) : 0;
            $stats_list['AgingPiePercentValue151_180'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue151_180($total_charge)/$aging_value_total)*100,2) : 0;
            //$stats_list['AgingPiePercentValue180_above'] = ($aging_value_total > 0) ? round(($this->AgingPiePercentValue180_above($total_charge)/$aging_value_total)*100) : 0;
            //chart	
            $chargeBar = [];
            /*
            $chargeBar = Claims::select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(created_at,'%M') as monthNum"))
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("month(created_at)"))
                    ->whereNotIn('status', ['Hold'])
                    ->lists('total_charge', 'monthNum');
            */
            $chargeBar = $this->getMonthWiseChargeBar('total_charge');
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
   
        return view('dashboard/index', compact('stats_list'))                       
                ->with('AgingPiePercentValue0_30', json_encode($stats_list['AgingPiePercentValue0_30'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue31_60', json_encode($stats_list['AgingPiePercentValue31_60'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue61_90', json_encode($stats_list['AgingPiePercentValue61_90'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue91_120', json_encode($stats_list['AgingPiePercentValue91_120'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue121_150', json_encode($stats_list['AgingPiePercentValue121_150'], JSON_NUMERIC_CHECK))
                ->with('AgingPiePercentValue151_180', json_encode($stats_list['AgingPiePercentValue151_180'], JSON_NUMERIC_CHECK))
                //->with('AgingPiePercentValue180_above', json_encode($stats_list['AgingPiePercentValue180_above'], JSON_NUMERIC_CHECK))
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
        Cache::forget('stats_listnew' . $practice_id);
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
    }

}
