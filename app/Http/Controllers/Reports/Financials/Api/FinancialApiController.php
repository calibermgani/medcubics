<?php

namespace App\Http\Controllers\Reports\Financials\Api;

use App;
use Config;
use Response;
use DB;
use Request;
use Auth;
use Input;
use Session;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Patients\Patient as Patient;
use App\Models\Provider as Provider;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use DateInterval;
use DateTime;
use DatePeriod;
use Excel;
use App\Traits\ClaimUtil;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Medcubics\Users as Users;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Patients\ProblemList as ProblemList;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\ProcedureCategory as ProcedureCategory;

class FinancialApiController extends Controller {

    public function getEnddaytotalApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('endday_totals');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details', 'searchUserData', 'search_fields')));
    }

    public function getFilterResultApi($export = "",$data = "") {
        if(isset($data) && !empty($data)){
            $request = $data;
            $practice_id = $data['practice_id'];
        } else{
            $practice_id = '';
            $request = Request::All();
        }
        $search_by = array();
        if (!empty($request['created_at'])) {
            $date = explode('-', $request['created_at']);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-d');
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }
        if (isset($request['exports']) && $request['exports'] == 'pdf'){
            $search_by['Transaction Date'] = App\Http\Helpers\Helpers::timezone($start_date,'m/d/y') . ' to ' . App\Http\Helpers\Helpers::timezone($end_date,'m/d/y');
        }
        else{
            $search_by['Transaction Date'][] = App\Http\Helpers\Helpers::timezone($start_date,'m/d/y') . ' to ' . App\Http\Helpers\Helpers::timezone($end_date,'m/d/y');
        }

        $txt_list = DB::table('pmt_claim_tx_v1')->leftjoin('claim_info_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')
                ->leftJoin('pmt_info_v1', 'pmt_info_v1.id', '=', 'pmt_claim_tx_v1.payment_id')
                ->Selectraw("
                        (pmt_claim_tx_v1.created_at) as trx_date,
                        SUM(IF(pmt_claim_tx_v1.pmt_method = 'Insurance' AND (pmt_claim_tx_v1.pmt_type='Adjustment' OR pmt_claim_tx_v1.pmt_type='Payment') , total_writeoff, 0)) AS writeoff_total,
                        SUM(IF(pmt_claim_tx_v1.pmt_method = 'Insurance' AND (pmt_claim_tx_v1.pmt_type='Adjustment' OR pmt_claim_tx_v1.pmt_type='Payment'), total_withheld, 0)) AS insurance_adjustment,
                        SUM(IF(pmt_claim_tx_v1.pmt_method = 'Patient' AND pmt_claim_tx_v1.pmt_type='Adjustment', total_writeoff, 0)) AS patient_adjustment,

                        SUM(IF(pmt_claim_tx_v1.pmt_method = 'Insurance' AND pmt_claim_tx_v1.pmt_type='Refund' and pmt_info_v1.void_check is null, total_paid, 0)) AS insurance_refund,
                        SUM(IF(pmt_claim_tx_v1.pmt_method = 'Patient' AND pmt_claim_tx_v1.pmt_type='Refund' and pmt_info_v1.void_check is null, total_paid, 0)) AS patient_refund,
                        (SUM(IF(pmt_claim_tx_v1.pmt_method = 'Insurance' AND pmt_claim_tx_v1.pmt_type='Payment', total_paid, 0))) AS insurance_payment,
                        (SUM(IF(pmt_claim_tx_v1.pmt_method = 'Patient' AND (pmt_claim_tx_v1.pmt_type='Payment' OR pmt_claim_tx_v1.pmt_type='Credit Balance'), total_paid, 0))) AS patient_payment,
                        SUM(total_paid) as total_payment,   pmt_claim_tx_v1.id as tx_id,claim_info_v1.id as inf_id
                        ")
                ->where(DB::raw('(pmt_claim_tx_v1.created_at)'), '>=', $start_date)
                ->where(DB::raw('(pmt_claim_tx_v1.created_at)'), '<=', $end_date)
                ->whereNull('pmt_claim_tx_v1.deleted_at')
                ->whereNull(DB::raw('(claim_info_v1.deleted_at)'))
                ->groupby(DB::raw('(pmt_claim_tx_v1.created_at)'));
        

        if (isset($request['facility']) && !empty($request['facility'])) {
            $txt_list->whereIn('claim_info_v1.facility_id', explode(',', $request['facility']));
            $search_name = Facility::select('facility_name');
            $facility_names = $search_name->whereIn('id', explode(',', $request['facility']))->get();
            foreach ($facility_names as $name) {
                $facility_ids[] = $name['facility_name'];
            }
            $search_filter = implode(", ", array_unique($facility_ids));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Facility'] = isset($search_filter) ? $search_filter : [];
            }
            else{
                $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
            }
        }

        if(isset($request['rendering_provider']) && !empty($request['rendering_provider'])) {
            $txt_list->whereIn('claim_info_v1.rendering_provider_id', explode(',', $request['rendering_provider']));
            $renders_id = explode(',', $request['rendering_provider']);
            foreach ($renders_id as $id) {
                $renders_ids[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($renders_ids));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Rendering Provider'] = $search_render;
            }
            else{
                $search_by['Rendering Provider'][] = $search_render;
            }
        }

        if(isset($request['billing_provider']) &&  !empty($request['billing_provider'])) {
            $txt_list->whereIn('claim_info_v1.billing_provider_id', explode(',', $request['billing_provider']));
            $billing_provider_id = explode(',', $request['billing_provider']);
            foreach ($billing_provider_id as $id) {
                $billing_provider_ids[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($billing_provider_ids));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Billing Provider'] = $search_provider;    
            }
            else{
                $search_by['Billing Provider'][] = $search_provider;
            }
        }

        if (isset($request['user']) &&  !empty($request['user'])) {
            $txt_list->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $short_name = DB::connection('responsive')->table('users')
                        ->whereIn('id',explode(',', $request['user']))
                        ->pluck('short_name')->all();
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by["User"] = implode(',',$short_name);
            }
            else{
                $search_by["User"][] = implode(',',$short_name);
            }
        }

        $fin_list = DB::table('claim_info_v1')
                ->select(DB::raw('count(Distinct(claim_info_v1.id)) as claim_count'), DB::raw('sum(claim_info_v1.total_charge) as sum'), DB::raw('(claim_info_v1.created_at) as created_at'))
                ->groupBy(DB::raw('(claim_info_v1.created_at)'))
                ->where(DB::raw('(claim_info_v1.created_at)'), '>=', $start_date)
                ->where(DB::raw('(claim_info_v1.created_at)'), '<=', $end_date)
                ->whereNull('claim_info_v1.deleted_at');
      
        if (isset($request['facility']) && !empty($request['facility']))
                $fin_list->whereIn('claim_info_v1.facility_id', explode(',', $request['facility']));
        
        if (isset($request['rendering_provider']) && !empty($request['rendering_provider']))
            $fin_list->whereIn('claim_info_v1.rendering_provider_id', explode(',', $request['rendering_provider']));
        
        if (isset($request['billing_provider']) && !empty($request['billing_provider']))
            $fin_list->whereIn('claim_info_v1.billing_provider_id', explode(',', $request['billing_provider']));
        
        if (isset($request['user']) && !empty($request['user']))
                $fin_list->whereIn('claim_info_v1.created_by', explode(',', $request['user']));

        $fin_list = $fin_list->get();//->pluck('sum', 'created_at')->all();
         
        $paymentTxn = DB::table('pmt_claim_tx_v1')->leftjoin('claim_info_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')
                ->select(DB::raw('sum(pmt_claim_tx_v1.total_paid) as wallet_amt'), DB::raw('(pmt_claim_tx_v1.created_at) as created_at'))
                ->groupBy(DB::raw('(pmt_claim_tx_v1.created_at)'))
                ->whereNull('pmt_claim_tx_v1.deleted_at')
                //->whereNull('pmt_claim_tx_v1.void_check')
                ->where(DB::raw('(pmt_claim_tx_v1.created_at)'), '>=', $start_date)
                ->where(DB::raw('(pmt_claim_tx_v1.created_at)'), '<=', $end_date)
                ->whereIn('pmt_claim_tx_v1.pmt_type', ['Credit Balance', 'Payment'])->where('pmt_claim_tx_v1.pmt_method', 'Patient');
        if (isset($request['facility']) && !empty($request['facility'])) {
            $paymentTxn->whereIn('claim_info_v1.facility_id', explode(',', $request['facility']));
        }
        if(isset($request['rendering_provider']) && !empty($request['rendering_provider'])) {
            $paymentTxn->whereIn('claim_info_v1.rendering_provider_id', explode(',', $request['rendering_provider']));
        }
        if(isset($request['billing_provider']) &&  !empty($request['billing_provider'])) {
            $paymentTxn->whereIn('claim_info_v1.billing_provider_id', explode(',', $request['billing_provider']));
        }
        if(isset($request['user']) &&  !empty($request['user'])) {
            $paymentTxn->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
         }
        //$paymentTxn->where('pmt_claim_tx_v1.source', '!=', 0);
        $paymentTxn = $paymentTxn->pluck('wallet_amt', 'created_at')->all();

        $refundTxn = DB::table('pmt_info_v1')->leftjoin('claim_info_v1', 'pmt_info_v1.source_id', '=', 'claim_info_v1.id')
                ->select(DB::raw('sum(pmt_info_v1.pmt_amt) as refund_amt'), DB::raw('(pmt_info_v1.created_at) as created_at'))
                ->groupBy(DB::raw('(pmt_info_v1.created_at)'))
                ->whereNull('pmt_info_v1.deleted_at')
                ->whereNull('pmt_info_v1.void_check')
                ->where(DB::raw('(pmt_info_v1.created_at)'), '>=', $start_date)
                ->where(DB::raw('(pmt_info_v1.created_at)'), '<=', $end_date)
                ->where('pmt_info_v1.pmt_type', 'Refund')->where('pmt_info_v1.pmt_method', 'Patient')->where('pmt_info_v1.source', 'posting');
        
        if (isset($request['facility']) && !empty($request['facility'])) {
            $refundTxn->whereIn('claim_info_v1.facility_id', explode(',', $request['facility']));
        }
        if(isset($request['rendering_provider']) && !empty($request['rendering_provider'])) {
            $refundTxn->whereIn('claim_info_v1.rendering_provider_id', explode(',', $request['rendering_provider']));
        }
        if(isset($request['billing_provider']) &&  !empty($request['billing_provider'])) {
            $refundTxn->whereIn('claim_info_v1.billing_provider_id', explode(',', $request['billing_provider']));
        }
        if(isset($request['user']) &&  !empty($request['user'])) {
            $refundTxn->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
        }
        $refundTxn->where('pmt_info_v1.source', '!=', 0);

        ###  start initialize values if no transaction for every the date ###
        ### add to wallet amt for patient payment  ###
        $refundTxn = $refundTxn->pluck('refund_amt', 'created_at')->all();
        $daterange = [];
        $results = [];
       
        
        ###  End initialize values if no transaction for the every date ###
        //dd($txt_list->toSql().json_encode($txt_list->getBindings()));
        
        $txt_list = $txt_list->get();
        $collection = collect($txt_list);
        $collections = collect($fin_list);
        
        if(!empty($collections))
        foreach ($collections as $item) {
            $key = $item->created_at;
            $results[$key]['created_at'] = $key;
            $results[$key]['claims_count'] = $item->claim_count;
            $results[$key]['total_charge'] = $item->sum;
        }
        
        if(!empty($paymentTxn))
        foreach ($paymentTxn as $key => $item) {
            $results[$key]['patient_payment'] = $item;
        }

        if(!empty($refundTxn))
        foreach ($refundTxn as $key => $item) {
            $results[$key]['patient_refund'] = $item;
        }
     
        if(!empty($collection))
        foreach ($collection as $item) {
            $dateStr = $item->trx_date;
            $results[$dateStr]['created_at'] = $dateStr;
            $results[$dateStr]['insurance_adjustment'] = @$item->insurance_adjustment;
            $results[$dateStr]['patient_adjustment'] = @$item->patient_adjustment;
            $results[$dateStr]['insurance_payment'] = @$item->insurance_payment;
            $results[$dateStr]['insurance_refund'] = (isset($item->insurance_refund) && @$item->insurance_refund < 0 ) ? (-1 * @$item->insurance_refund) : @$item->insurance_refund;
            ;
            $results[$dateStr]['writeoff_total'] = @$item->writeoff_total;
            
        }
        $result = [];
        if(!empty($results))
            foreach($results as $k=>$res){
                if(array_key_exists(App\Http\Helpers\Helpers::timezone($k,'m/d/Y'), $result)){
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['claims_count'] += @$res['claims_count'];
                     $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_charge'] += @$res['total_charge'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_adjustment'] += @$res['insurance_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_adjustment'] += @$res['patient_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_payment'] += @$res['insurance_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_payment'] += @$res['patient_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_refund'] += @$res['patient_refund'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_refund'] += (isset($res['insurance_refund']) && @$res['insurance_refund'] < 0 ) ? (-1 * @$res['insurance_refund']) : @$res['insurance_refund'];
                    ;
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['writeoff_total'] += @$res['writeoff_total'];
                    $total_payment_amt = (@$res['insurance_payment'] + @$res['patient_payment']);
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_payment'] += $total_payment_amt;
                }else{
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['created_at'] = App\Http\Helpers\Helpers::timezone($k,'m/d/Y');
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['claims_count'] = @$res['claims_count'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_charge'] = @$res['total_charge'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_adjustment'] = @$res['insurance_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_adjustment'] = @$res['patient_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_payment'] = @$res['insurance_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_payment'] = @$res['patient_payment'];
                    $pat_refund = (isset($refundTxn[$k]) ? $refundTxn[$k] : 0 );
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_refund'] = @$res['patient_refund'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_refund'] = (isset($res['insurance_refund']) && @$res['insurance_refund'] < 0 ) ? (-1 * @$res['insurance_refund']) : @$res['insurance_refund'];
                    ;
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['writeoff_total'] = @$res['writeoff_total'];
                    $total_payment_amt = (@$res['insurance_payment'] + @$res['patient_payment']);
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_payment'] =$total_payment_amt;
                }
            }
        $temp = [];
        if ($export == "") {
            ksort($result);
            $report_array = $this->paginate($result)->toArray();
            $pagination_prt = $this->paginate($result)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $export_array = ksort($result);
        $export_array = $result;
        $temp = array_chunk($result, 25, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($result)) {
            $start = isset($request['page']) ? $request['page'] - 1 : 0;
            $result = $temp[$start];
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'result', 'fin_list', 'daterange', 'search_by', 'export_array')));
    }
    
    public function getFilterResultApiSP($export = "",$data = "") {
        if(isset($data) && !empty($data)){
            $request = $data;
            $practice_id = $data['practice_id'];
        } else{
            $practice_id = '';
            $request = Request::All();
        }
        
        $search_by = array();
        $start_date = $end_date = $billing_provider = $rendering_provider = $facility = $user_ids = '';
        
        if (!empty($request['created_at'])) {
            $date = explode('-', $request['created_at']);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-d');
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }
        $search_by['Transaction Date'][] = App\Http\Helpers\Helpers::timezone($start_date,'m/d/y') . ' to ' . App\Http\Helpers\Helpers::timezone($end_date,'m/d/y');
        
        if (isset($request['facility']) && $request['facility'] != '') {
            $facility = $request['facility'];
            if (strpos($request['facility'], ',') !== false) {
                $search_name = Facility::select('facility_name');
                $facility_names = $search_name->whereIn('id', explode(',', $request['facility']))->get();
                foreach ($facility_names as $name) {
                    $value_names[] = $name['facility_name'];
                }
                $search_filter = implode(", ", array_unique($value_names));
            } else {
                $facility_names = Facility::select('facility_name')->where('id', $request['facility'])->get();
                foreach ($facility_names as $facility_na) {
                    $search_filter = $facility_na['facility_name'];
                }
            }
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }
        if (!empty($request['rendering_provider'])) {
            
            $rendering_provider = $request['rendering_provider'];
            $renders_id = explode(',', $request['rendering_provider']);
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }

        if (!empty($request['billing_provider'])) {
            $billing_provider = $request['billing_provider'];            
            $providers_id = explode(',', $request['billing_provider']);
            foreach ($providers_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            $search_by['Billing Provider'][] = $search_provider;
        }
        if (isset($request['user']) && !empty($request['user'])) {
            $req_user = explode(',', $request['user']);
            $user_ids = $request['user'];
            foreach ($req_user as $key => $value) {
                $short_name[] = DB::connection('responsive')->table('users')
                    ->whereIn('id',explode(',', $value))
                    ->pluck('short_name')->first();
            }
            $search_by["User"][] = implode(',',$short_name);
        }
        
        $txt_list = DB::select('call endDayTotal("' . $start_date . '", "' . $end_date . '", "' . $billing_provider . '",  "' . $rendering_provider . '",  "' . $facility . '", "' . $user_ids . '")');
        
        $fin_list = DB::select('call endDayTotalFinList("' . $start_date . '", "' . $end_date . '", "' . $billing_provider . '",  "' . $rendering_provider . '",  "' . $facility . '", "' . $user_ids . '")');
        
        $payment = DB::select('call endDayTotalPaymentTxn("' . $start_date . '", "' . $end_date . '", "' . $billing_provider . '",  "' . $rendering_provider . '",  "' . $facility . '", "' . $user_ids . '")');
        $payment_result = json_decode(json_encode($payment), true);
        $paymentTxn = array_combine(array_column($payment_result, 'created_at'), array_column($payment_result, 'wallet_amt'));
        
        $refund = DB::select('call endDayTotalRefundTxn("' . $start_date . '", "' . $end_date . '", "' . $billing_provider . '",  "' . $rendering_provider . '",  "' . $facility . '", "' . $user_ids . '")');
        $refund_result = json_decode(json_encode($refund), true);
        $refundTxn = array_combine(array_column($refund_result, 'created_at'), array_column($refund_result, 'refund_amt'));
        
        $daterange = [];
        $results = [];
        
        $collection = collect($txt_list);
        $collections = collect($fin_list);
        
        if(!empty($collections))
        foreach ($collections as $item) {
            $key = $item->created_at;
            $results[$key]['created_at'] = $key;
            $results[$key]['claims_count'] = $item->claim_count;
            $results[$key]['total_charge'] = $item->sum;
        }
        
        if(!empty($paymentTxn))
        foreach ($paymentTxn as $key => $item) {
            $results[$key]['patient_payment'] = $item;
        }

        if(!empty($refundTxn))
        foreach ($refundTxn as $key => $item) {
            $results[$key]['patient_refund'] = $item;
        }
        
        if(!empty($collection))
        foreach ($collection as $item) {
            $dateStr = $item->trx_date;
            $results[$dateStr]['created_at'] = $dateStr;
            $results[$dateStr]['insurance_adjustment'] = @$item->insurance_adjustment;
            $results[$dateStr]['patient_adjustment'] = @$item->patient_adjustment;
            $results[$dateStr]['insurance_payment'] = @$item->insurance_payment;
            $results[$dateStr]['insurance_refund'] = (isset($item->insurance_refund) && @$item->insurance_refund < 0 ) ? (-1 * @$item->insurance_refund) : @$item->insurance_refund;
            ;
            $results[$dateStr]['writeoff_total'] = @$item->writeoff_total;
            
        }
        $result = [];
        
        if(!empty($results))
            foreach($results as $k=>$res){
                if(array_key_exists(App\Http\Helpers\Helpers::timezone($k,'m/d/Y'), $result)){
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['claims_count'] += @$res['claims_count'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_charge'] += @$res['total_charge'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_adjustment'] += @$res['insurance_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_adjustment'] += @$res['patient_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_payment'] += @$res['insurance_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_payment'] += @$res['patient_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_refund'] += @$res['patient_refund'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_refund'] += (isset($res['insurance_refund']) && @$res['insurance_refund'] < 0 ) ? (-1 * @$res['insurance_refund']) : @$res['insurance_refund'];
                    ;
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['writeoff_total'] += @$res['writeoff_total'];
                    $total_payment_amt = (@$res['insurance_payment'] + @$res['patient_payment']);
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_payment'] += $total_payment_amt;
                }else{
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['created_at'] = App\Http\Helpers\Helpers::timezone($k,'m/d/Y');
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['claims_count'] = @$res['claims_count'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_charge'] = @$res['total_charge'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_adjustment'] = @$res['insurance_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_adjustment'] = @$res['patient_adjustment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_payment'] = @$res['insurance_payment'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_payment'] = @$res['patient_payment'];
                    $pat_refund = (isset($refundTxn[$k]) ? $refundTxn[$k] : 0 );
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['patient_refund'] = @$res['patient_refund'];
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['insurance_refund'] = (isset($res['insurance_refund']) && @$res['insurance_refund'] < 0 ) ? (-1 * @$res['insurance_refund']) : @$res['insurance_refund'];
                    ;
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['writeoff_total'] = @$res['writeoff_total'];
                    $total_payment_amt = (@$res['insurance_payment'] + @$res['patient_payment']);
                    $result[App\Http\Helpers\Helpers::timezone($k,'m/d/Y')]['total_payment'] =$total_payment_amt;
                }
            }
            
        $temp = [];
        if ($export == "") {
            ksort($result);
            $report_array = $this->paginate($result)->toArray();
            $pagination_prt = $this->paginate($result)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $export_array = ksort($result);
        $export_array = $result;
        $temp = array_chunk($result, 25, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($result)) {
            $start = isset($request['page']) ? $request['page'] - 1 : 0;
            $result = $temp[$start];
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'result', 'fin_list', 'daterange', 'search_by', 'export_array')));
    }

    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /*     * * Blade - endday total export ** */

    public function getEnddayExportApi($export = '') {
        $request = Input::get();
        if ($request['start-date'] != '')
            $start_date = $request['start-date'];

        if ($request['end-date'] != '')
            $end_date = $request['end-date'];
        // @todo - check and replace new pmt flow
        /*
          $finalcialReport = PaymentClaimDetail::has('payment')->whereHas('payment', function($q){$q->whereIn('payment_type', ["Payment","Adjustment","Refund"])->whereNotIn('type', ["refundwallet"]);})->has('claim')->with('claim','user','payment',"patient")->whereRaw("(payment_type = 'Insurance' or payment_type = 'Patient') and (patient_paid_amt != '0.00' or insurance_paid_amt != '0.00' or total_adjusted != '0.00' or total_withheld != '0.00')")->where('created_at','>=',date("Y-m-d",strtotime($start_date))." 00:00:00")->where('created_at','<=',date("Y-m-d",strtotime($end_date))." 23:59:59");
          $filter_result = $finalcialReport->get();
         */
        $filter_result = [];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result')));
    }

    public function getExportResultApi() {
        $request = Input::get();
        // @todo - check and replace new pmt flow
        $export_result = []; //PaymentClaimDetail::has('payment')->whereHas('payment', function($q){$q->whereIn('payment_type', ["Payment","Adjustment","Refund"])->whereNotIn('type', ["refundwallet"]);})->has('claim')->with('claim','user','payment',"patient")->whereRaw("(payment_type = 'Insurance' or payment_type = 'Patient') and (patient_paid_amt != '0.00' or insurance_paid_amt != '0.00' or total_adjusted != '0.00' or total_withheld != '0.00') ")->where('created_at','>=',date("Y-m-d",strtotime($request['start-date']))." 00:00:00")->where('created_at','<=',date("Y-m-d",strtotime($request['end-date']))." 23:59:59")->get();

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('export_result')));
    }

    public function getUnbilledClaimCreatedApi() {
        $claims_created_date = ClaimInfoV1::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_created_date')));
    }

    /* Unbilled Report Function */

    public function getUnbilledClaimApi($export = '',$datas = '') {
        //echo "test";die;
        if(isset($datas) && !empty($datas))
            $request = $datas;
        else
            $request = Request::All();
        $search_by = array();
        $data = $this->unbilledSearchQuery($request);
        $unbilled_claim = $data['query'];
        if (!empty($request['export'])) {
            $unbilled_claim_details_export = $unbilled_claim->get();
        }
        $search_by = $data['search_by'];
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-', $request['select_transaction_date']);
            $start_date = date("m/d/y", strtotime(trim($date[0])));
            $end_date = date("m/d/y", strtotime(trim($date[1])));
        } else {
            $start_date = $end_date = '';
        }
        else
            $start_date = $end_date = '';
        $total_charges = $unbilled_claim->sum('total_charge');

        if(isset($request['exports']) && $request['exports'] == 'pdf') {
            $unbilled_claim_details = $unbilled_claim->get();
        }
        elseif(isset($request['export']) && $request['export'] == 'xlsx') {
            $unbilled_claim_details = $unbilled_claim->get();
        }
        else {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $unbilled_claim_details = $unbilled_claim->paginate($paginate_count);
            $claim_array = $unbilled_claim_details->toArray();
            $pagination_prt = $unbilled_claim_details->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $claim_array['total'], 'per_page' => $claim_array['per_page'], 'current_page' => $claim_array['current_page'], 'last_page' => $claim_array['last_page'], 'from' => $claim_array['from'], 'to' => $claim_array['to'], 'pagination_prt' => $pagination_prt);
            $claims_list = json_decode($unbilled_claim_details->toJson());
            $unbilled_claim_details = $claims_list->data;
        }

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('unbilled_claim_details','unbilled_claim_details_export' ,'start_date', 'end_date', 'pagination', 'total_charges','search_by', 'unbilled_claim')));
    }

    /* Unbilled Report Function */

    /* Unbilled Report Function Search Query */

    public function unbilledSearchQuery($request) {
        $unbilled_claim = ClaimInfoV1::with(['insurance_details', 'rendering_provider', 'billing_provider', 'patient', 'facility'])->where('claim_submit_count', '0')->whereIn('status', ['Ready', 'Pending', 'Hold']);
        $search_by = [];
       if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-', $request['select_transaction_date']);
            //$start_date = trim($date[0]);
            //$end_date = trim($date[1]);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate(trim($date[0]));
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate(trim($date[1]));
            $search_by['Transaction Date'] = date("m/d/y", strtotime($date[0])) . ' to ' . date("m/d/y", strtotime($date[1]));
            $unbilled_claim->where(DB::raw('(created_at)'), '>=', $start_date)->where(DB::raw('(created_at)'), '<=', $end_date);
           
        }
         if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $date = explode('-', $request['select_date_of_service']);
            $start_date = trim($date[0]);
            $end_date = trim($date[1]);
            $unbilled_claim->where(DB::raw('DATE(date_of_service)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(date_of_service)'), '<=', date("Y-m-d", strtotime($end_date)));
            $search_by['DOS Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date));
        }
        if (isset($request['insurance_id']) && $request['insurance_id'] != '') {
            if (strpos($request['insurance_id'], ',') !== false) {
                $unbilled_claim->whereIn('insurance_id', explode(',', $request['insurance_id']));
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
                $unbilled_claim->where('insurance_id', $request['insurance_id']);
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            $search_by["Insurance"] =  @array_flatten($insurance)[0];
        } else {
            $unbilled_claim->where('insurance_id', '<>', 0);
        }
        if (isset($request['rendering_provider_id']) && $request['rendering_provider_id'] != '') {
            if (strpos($request['rendering_provider_id'], ',') !== false){
                $unbilled_claim->whereIn('rendering_provider_id', explode(',', $request['rendering_provider_id']));
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['rendering_provider_id']))->get()->toArray();
            }else{
                $unbilled_claim->where('rendering_provider_id', $request['rendering_provider_id']);
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['rendering_provider_id'])->get()->toArray();
            }
            $search_by["Rendering Provider"] =  @array_flatten($provider)[0];
        }

        if (isset($request['billing_provider_id']) && $request['billing_provider_id'] != '') {
            if (strpos($request['billing_provider_id'], ',') !== false){
                $unbilled_claim->whereIn('billing_provider_id', explode(',', $request['billing_provider_id']));
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['billing_provider_id']))->get()->toArray();
            }else{
                $unbilled_claim->where('billing_provider_id', $request['billing_provider_id']);
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['billing_provider_id'])->get()->toArray();
            }
            $search_by["Billing Provider"] =  @array_flatten($provider)[0];
        }

        if (isset($request['facility_id']) && $request['facility_id'] != '') {
            if (strpos($request['facility_id'], ',') !== false){
                $unbilled_claim->whereIn('facility_id', explode(',', $request['facility_id']));
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request['facility_id']))->get()->toArray();
            }else{
                $unbilled_claim->where('facility_id', $request['facility_id']);
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->where('id', $request['facility_id'])->get()->toArray();
            }

            $search_by["Facility Name"] =  @array_flatten($facility)[0];
        }
        if (isset($request['user']) && $request['user'] != '') {
            $unbilled_claim->whereIn('created_by', explode(',', $request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'] = $User_name;
        }
        $data['query'] = $unbilled_claim->orderBy('claim_info_v1.id','desc');
        $data['search_by'] = $search_by;
        return $data;
    }

    /* Unbilled Report Function Search Query */

    /* Call Stored procedure for Unbilled Report Function */

    public function getUnbilledClaimApiSP($export = '', $datas = '') {
        if (isset($datas) && !empty($datas))
            $request = $datas;
        else
            $request = Request::All();
        $practice_timezone = Helpers::getPracticeTimeZone();
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $rendering_provider_id = $facility_id = $insurance_id = $user_ids = '';
        $search_by = [];
        
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $date = explode('-', $request['select_transaction_date']);
                $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate(trim($date[0]));
                $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate(trim($date[1]));
                $search_by['Transaction Date'] = date("m/d/y", strtotime($date[0])) . ' to ' . date("m/d/y", strtotime($date[1]));                
            }
            
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                $date = explode('-', $request['select_date_of_service']);
                $dos_start = trim($date[0]);
                $dos_end = trim($date[1]);
                $dos_start_date = date("Y-m-d", strtotime($dos_start));
                $dos_end_date = date("Y-m-d", strtotime($dos_end));
                $search_by['DOS Date'] = date("m/d/y", strtotime($dos_start)) . ' to ' . date("m/d/y", strtotime($dos_end));
            }

        if (isset($request['billing_provider_id']) && $request['billing_provider_id'] != '') {
            $billing_provider_id = $request['billing_provider_id'];
            if (strpos($request['billing_provider_id'], ',') !== false){
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['billing_provider_id']))->get()->toArray();
            }else{
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['billing_provider_id'])->get()->toArray();
            }
            $search_by["Billing Provider"] =  @array_flatten($provider)[0];
        }

        if (isset($request['rendering_provider_id']) && $request['rendering_provider_id'] != '') {
            $rendering_provider_id = $request['rendering_provider_id'];
            if (strpos($request['rendering_provider_id'], ',') !== false){
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['rendering_provider_id']))->get()->toArray();
            }else{
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->where('id', $request['rendering_provider_id'])->get()->toArray();
            }
            $search_by["Rendering Provider"] =  @array_flatten($provider)[0];
        }
        if (isset($request['facility_id']) && $request['facility_id'] != '') {
            $facility_id = $request['facility_id'];
            if (strpos($request['facility_id'], ',') !== false){
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request['facility_id']))->get()->toArray();
            }else{
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->where('id', $request['facility_id'])->get()->toArray();
            }
            $search_by["Facility Name"] =  @array_flatten($facility)[0];
        }
        if (isset($request['insurance_id']) && $request['insurance_id'] != '') {
            $insurance_id = $request['insurance_id'];
            if (strpos($request['insurance_id'], ',') !== false) {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            $search_by["Insurance"] =  @array_flatten($insurance)[0];
        }
        if (isset($request['user']) && !empty($request['user'])) {
            $req_user = explode(',', $request['user']);
            $user_ids = $request['user'];
            foreach ($req_user as $key => $value) {
                $short_name[] = DB::connection('responsive')->table('users')
                    ->whereIn('id',explode(',', $value))
                    ->pluck('short_name')->first();
            }
            $search_by["User"] = implode(',',$short_name);
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
            $sp_return_result = DB::select('call unbilledClaimsAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_id . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->unbilled_count;
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
                if ($paginate_count > $count) {
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }
            $recCount = 0;
            $sp_return_result = DB::select('call unbilledClaimsAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_id . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result1 = (array) $sp_return_result;
            $total_charges = '';

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
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call unbilledClaimsAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_id . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $total_charges = '';
        }
        $unbilled_claim_details = $sp_return_result;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('unbilled_claim_details', 'start_date', 'end_date', 'pagination', 'total_charges', 'search_by')));
    }

    ### Aging report module start ###


    /*     * * index function start ** */

    public function getAgingReportApi() {
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
        $facilities = Facility::getAllfacilities();
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('aging_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities', 'searchUserData', 'search_fields')));
    }

    /*     * * index function end ** */

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    /*     * * search function start ** */

    public function getAgingReportSearchApi($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        DB::enableQueryLog();
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $claimsresult = DB::table('claim_info_v1')
                ->leftjoin('patients', 'patients.id', '=', 'claim_info_v1.patient_id')
                ->leftjoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->leftjoin('insurances', 'insurances.id', '=', 'claim_info_v1.insurance_id')
                ->leftjoin('insurancetypes', 'insurancetypes.id', '=', 'insurances.insurancetype_id')
                ->leftjoin('claim_sub_status', 'claim_sub_status.id', '=', 'claim_info_v1.sub_status_id')
                ->leftjoin('patient_insurance', function($join) {
                    $join->on('patient_insurance.insurance_id', '=', 'claim_info_v1.insurance_id');
                    $join->on('patient_insurance.patient_id', '=', 'patients.id');
                })
                ->SelectRaw("DATE(CONVERT_TZ(claim_info_v1.last_submited_date,'UTC','".$practice_timezone."')) as last_submited_date,
                   claim_info_v1.claim_number,
                   claim_info_v1.insurance_category,
                   claim_info_v1.status,
                   insurancetypes.type_name,
                   claim_info_v1.rendering_provider_id,
                   claim_info_v1.billing_provider_id,
                   claim_info_v1.facility_id,
                   claim_info_v1.total_charge,
                   claim_sub_status.sub_status_desc,
                    DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) as submited_date,
                   DATE_FORMAT(claim_info_v1.date_of_service, '%m/%d/%Y') as dos,
                   (CASE WHEN claim_info_v1.insurance_id != 0
                    THEN insurances.short_name ELSE 'Patient' END) responsibility,
                    (CASE WHEN claim_info_v1.insurance_id != 0
                    THEN insurances.insurance_name ELSE 'Patient' END) responsibility_name,
                    (CASE WHEN claim_info_v1.insurance_id != 0
                    THEN patient_insurance.policy_id ELSE '-Nil-' END) policy_id,
                   patients.account_no,
                    CONCAT(patients.last_name ,', ', patients.first_name, ' ', patients.middle_name) as patient_name,
                    (
                    CASE WHEN claim_info_v1.insurance_id != 0 AND claim_info_v1.claim_submit_count = 0
                    THEN (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj) ELSE 0 END) unbilled,
                    (
                    CASE WHEN claim_info_v1.insurance_id = 0 
                    THEN (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj) ELSE 0 END) pat_bal,
                    (
                    CASE WHEN claim_info_v1.insurance_id != 0 
                    THEN (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) ELSE 0 END) ins_bal, 
                    (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj) as total_bal,DATEDIFF(NOW(),MIN(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')))) as days")
                ->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
                
         //\Log::info('#'.Carbon::now($practice_timezone)->subDays(30).'-'.Carbon::now($practice_timezone)->subDays(59));
        $search_by = array();
        $claim_by = isset($request['claim_by']) ? $request['claim_by'] : "created_date";
        $aging_by = isset($request['aging_by']) ? $request['aging_by'] : "All";
        if($aging_by!="All" &&  $aging_by!="Unbilled"){
            $data_value = explode("-", $aging_by);
            if((empty($data_value[1])) || $data_value[1] == 'above'){
                $end_date = '';
            }else{
                $end_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$data_value[1]-1)));
            }
            if(strpos($aging_by, ">") !== false){
                $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", 149))));
            } else {
                if(@$data_value[0]=='Unbilled'){
                    $start_date = 'Unbilled';
                }else{
                    if(@$data_value[0] == 150){
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]))));                    
                    } else {
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]-1))));
                    }
                }
            }
            if(strpos($aging_by, ">") === false){
                $claimsresult->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$start_date'");
            } else {
                $claimsresult->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$start_date' AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$end_date'");
            }
        }
        
        $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
        
        if(empty($export)){
            if(Auth::check() && Auth::user()->isProvider()){
                $claimsresult->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
            }
        }elseif(isset($request['export_id']) && !empty($request['export_id'])){
            $providerStatus = App\Http\Helpers\Helpers::isProvider($request['export_id']);
            if($providerStatus['status']){
                $claimsresult->where('claim_info_v1.rendering_provider_id',$providerStatus['provider_id']);
            }
        }
        if (!empty($claim_by)) {
            if ($claim_by == 'created_date') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Transaction Date';
                $claimsresult->selectRaw(" 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(29)."') AND DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days30, 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(59)."') AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(30)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days60, 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(89)."') AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(60)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days90, 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(119)."') AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(90)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days120, 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(149)."') AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(120)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days150, 
                IF(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(150)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as daysabove,
                round((claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj))/(claim_info_v1.total_charge/(DATEDIFF(DATE('".Carbon::now($practice_timezone)."'),(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'))))))) as ar_days");
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime(@$date[0]));
                    $end_date = date("Y-m-d", strtotime(@$date[1]));
                    // for passing Aging Date filter parameters
                    
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                    $claimsresult->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date'");
                }
            } else if ($claim_by == 'submitted_date') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Submitted Date';
                $claimsresult->selectRaw(" 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(29)."') AND DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days30, 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(59)."') AND  DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(30)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days60, 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(89)."') AND  DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(60)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days90, 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(119)."') AND  DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(90)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days120, 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(149)."') AND  DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(120)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days150, 
                IF(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(150)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as daysabove,
                round((claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj))/(claim_info_v1.total_charge/(DATEDIFF(DATE('".Carbon::now($practice_timezone)."'),(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."'))))))) as ar_days");
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime(@$date[0]));
                    $end_date = date("Y-m-d", strtotime(@$date[1]));
                    $claimsresult->whereRaw("DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) <= '$end_date' AND  DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')) >= '$start_date'");
                    // for passing Aging Date filter parameters
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                }
            } else if ($claim_by == 'date_of_service') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Date of Service';
                $claimsresult->selectRaw(" 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(29)."') AND DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days30, 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(59)."') AND  DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(30)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days60, 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(89)."') AND  DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(60)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days90, 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(119)."') AND  DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(90)."'),if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0,  ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days120, 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) >= date('".Carbon::now($practice_timezone)->subDays(149)."') AND  DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(120)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as days150, 
                IF(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."')) <= date('".Carbon::now($practice_timezone)->subDays(150)."'), if((claim_info_v1.insurance_id!=0 and claim_info_v1.claim_submit_count > 0) or claim_info_v1.insurance_id=0, ((claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)),0),0) as daysabove,
                round((claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj))/(claim_info_v1.total_charge/(DATEDIFF(DATE('".Carbon::now($practice_timezone)."'),(DATE(CONVERT_TZ(claim_info_v1.date_of_service,'UTC','".$practice_timezone."'))))-1))) as ar_days");
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime($date[0]));
                    $end_date = date("Y-m-d", strtotime($date[1]));
                    $claimsresult->where(DB::raw('DATE(claim_info_v1.date_of_service)'), '>=', $start_date)->where('claim_info_v1.date_of_service', '<=', $end_date);
                    // for passing Aging Date filter parameters
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                } 
            }
        }
        $summary = clone $claimsresult;
        $summary = $summary->toSql();
        
        // Query for aging by bucket search
        if (!empty($request['aging_by'])) {
            if ($aging_by == '150-above') {
                $search = '>150';
            } else {
                $search = $aging_by;
            }
            // for passing filter parameters
            $search_by['Aging Days'][] = $search;
            if ($aging_by == "Unbilled") {
                //Aging by Unbilled
                $claimsresult->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', 0);

            } else if ($aging_by != "All" && $aging_by != "Unbilled") {
                //Aging By Buckets (Billed Charges)
                $claimsresult->where(function($qry){
                    $qry->where(function($query){
                        $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                    })->orWhere('claim_info_v1.insurance_id',0);
               });
            }
        }

        $search_lable = $request['aging_group_by'];
        // Query For Aging Group
        if (!empty($request['aging_group_by'])) {
            $search_by['Aging Group By'][] = rtrim(ucwords(str_replace('_', ' ', $request['aging_group_by'])));;
            if ($request['aging_group_by'] == 'insurance') {
                $claimsresult->where('claim_info_v1.insurance_id', '!=', 0);
                if(!empty($request['aging_insurance_id'])){
                   $claimsresult->where('claim_info_v1.insurance_id', '=', $request['aging_insurance_id']);
                   $search_by['Insurance'][] = $this->getInsuranceName($request['aging_insurance_id']);
               }else{
                   $search_by['Insurance'][] = "All";
               }

            } else if ($request['aging_group_by'] == 'patient') {
                $claimsresult->where('claim_info_v1.insurance_id', 0);
            } else if ($request['aging_group_by'] == 'rendering_provider') {  
                $claimsresult->where('claim_info_v1.rendering_provider_id','<>',0);                
                $summary .= " and rendering_provider_id <> 0";
                if(!empty($request['rendering_provider_id'])){
                    if (strpos($request['rendering_provider_id'], ',') !== false){
                        $claimsresult->whereIn('claim_info_v1.rendering_provider_id', explode(',', $request['rendering_provider_id']));
                        $summary .="  and rendering_provider_id in (".$request['rendering_provider_id'].")";
                    } else{
                        $claimsresult->where('claim_info_v1.rendering_provider_id', '=', $request['rendering_provider_id']);
                        $summary .="  and rendering_provider_id = ".$request['rendering_provider_id'];
                    }
                    $renders_id = explode(',', $request['rendering_provider_id']);
                    foreach ($renders_id as $id) {
                        $value_name[] = App\Models\Provider::getProviderFullName($id);
                    }
                    $search_render = implode(", ", array_unique($value_name));
                    $search_by['Rendering Provider'][] = $search_render;
                }
                $claimsresult->orderBy('claim_info_v1.rendering_provider_id', 'asc');

            }else if ($request['aging_group_by'] == 'billing_provider') {
                $claimsresult->where('claim_info_v1.billing_provider_id','<>',0);   
                $summary .= " and billing_provider_id <> 0";             
                if(!empty($request['billing_provider_id'])){
                   if (strpos($request['billing_provider_id'], ',') !== false){
                       $claimsresult->whereIn('claim_info_v1.billing_provider_id', explode(',', $request['billing_provider_id']));
                       $summary .= " and billing_provider_id in ".explode(',', $request['billing_provider_id']);
                   } else{
                        $claimsresult->where('claim_info_v1.billing_provider_id', '=', $request['billing_provider_id']);
                        $summary .=" and billing_provider_id = ".$request['billing_provider_id'];
                   }
                    $peoviders_id = explode(',', $request['billing_provider_id']);
                    foreach ($peoviders_id as $id) {
                        $value_name[] = App\Models\Provider::getProviderFullName($id);
                    }
                    $search_provider = implode(", ", array_unique($value_name));
                    $search_by['Billing Provider'][] = $search_provider;
                }
                 $claimsresult->orderBy('claim_info_v1.billing_provider_id', 'asc');

            }else if ($request['aging_group_by'] == 'facility') {    
                $claimsresult->where('claim_info_v1.facility_id','<>',0); 
                $summary .= " and claim_info_v1.facility_id <> 0";            
                if(!empty($request['facility_id'])){
                   if (strpos($request['facility_id'], ',') !== false){
                       $claimsresult->whereIn('claim_info_v1.facility_id', explode(',', $request['facility_id']));
                       $summary .= " and claim_info_v1.facility_id in (".$request['facility_id'].")";
                       $search_name = Facility::select('facility_name');
                        $facility_names = $search_name->whereIn('id', explode(',', $request['facility_id']))->get();
                        foreach ($facility_names as $name) {
                            $value_names[] = $name['facility_name'];
                        }
                        $search_filter = implode(", ", array_unique($value_names));
                   }else{
                       $claimsresult->where('claim_info_v1.facility_id', '=', $request['facility_id']);
                       $summary .=" and claim_info_v1.facility_id = ".$request['facility_id'];
                       $facility_names = Facility::select('facility_name')->where('id', $request['facility_id'])->get();
                       foreach ($facility_names as $facility_na) {
                            $search_filter = $facility_na['facility_name'];
                       }
                   }
                   $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
                }
                 $claimsresult->orderBy('claim_info_v1.facility_id', 'asc');

            }
        }
          // Query for User
        if (!empty($request['user'])) {
            $claimsresult->whereIn('claim_info_v1.created_by',explode(',', $request["user"]));
            $summary .= " and claim_info_v1.created_by in (".$request['user'].")";
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        } 

        // Filter by claim status
        if (isset($request['status']) && !empty($request['status']) && $request['status'] != 'All') {
            $claimsresult->whereIn('claim_info_v1.status',explode(',', $request["status"]));
            $explode = explode(',', $request["status"]);
            $status = '';
            foreach($explode as $key => $val){
                if(!empty($val)){
                    if($key!=count($explode)-1)
                        $status .= '"'.$val.'",';
                    else
                        $status .= '"'.$val.'"';
                }
            }
            if($status!='')
                $summary .= " and claim_info_v1.status in (".$status.")";
            $search_by['Status'][] = $request["status"];
        } 

        $pat_name_arr = $ins_name_arr = $ins_policy_arr = [] ;
        $show_flag = $aging_by;
        $group_by = "ALL";
        $claimsresult->groupby('claim_info_v1.id')->whereNull('claim_info_v1.deleted_at');
        $summaries = [];
        if($request['aging_group_by'] == 'facility' || $request['aging_group_by'] == 'billing_provider' || $request['aging_group_by'] == 'rendering_provider'){            
            $summaries = DB::select("select sum(tot.total_charge) as total_charge, tot.".$request['aging_group_by']."_id as ".$request['aging_group_by'].", sum(tot.unbilled) as unbilled, sum(tot.pat_bal) as total_pat, sum(tot.ins_bal) as total_ins, sum(tot.days30) as days30, sum(tot.days60) as days60, sum(tot.days90) as days90, sum(tot.days120) as days120, sum(tot.days150) as days150, sum(tot.daysabove) as daysabove, sum(tot.total_bal) as total from (".$summary." and claim_info_v1.deleted_at is null group by claim_info_v1.id) as tot group by tot.".$request['aging_group_by']."_id");
        }
        if(!empty($summaries)){
            foreach($summaries as $res){
                $summ[$res->{$request['aging_group_by']}]['total_charge'] = $res->total_charge;
                $summ[$res->{$request['aging_group_by']}]['unbilled'] = $res->unbilled;
                $summ[$res->{$request['aging_group_by']}]['total_pat'] = $res->total_pat;
                $summ[$res->{$request['aging_group_by']}]['total_ins'] = $res->total_ins;
                $summ[$res->{$request['aging_group_by']}]['days30'] = $res->days30;
                $summ[$res->{$request['aging_group_by']}]['days60'] = $res->days60;
                $summ[$res->{$request['aging_group_by']}]['days90'] = $res->days90;
                $summ[$res->{$request['aging_group_by']}]['days120'] = $res->days120;
                $summ[$res->{$request['aging_group_by']}]['days150'] = $res->days150;
                $summ[$res->{$request['aging_group_by']}]['daysabove'] = $res->daysabove;
                $summ[$res->{$request['aging_group_by']}]['total'] = $res->total;
            }
        }
        if(isset($summ))
            $summaries = $summ;
        $start_date = (isset($start_date)) ? $start_date : '0000-00-00';
        $end_date = (isset($end_date)) ? $end_date : '0000-00-00';

        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $aging_report_list = $claimsresult->get();
        } elseif (isset($request['exports']) && $request['exports'] == 'xlsx') {
            $aging_report_list = $claimsresult->get();
        }
        else{
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $report_array = $claimsresult->paginate($paginate_count)->toArray();
            $pagination_prt = $claimsresult->paginate($paginate_count)->render();

            if ($pagination_prt == ''){
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            }
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $aging_report_list = $report_array['data'];
        }
       
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('aging_report_list', 'show_flag', 'pagination_prt', 'pagination', 'group_by', 'search_by', 'start_date', 'end_date','search_lable', 'summaries')));
    }
    
    /** Stored procedure for Aging analysis detailed **/
    public function getAgingReportSearchApiSP($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        
        $practice_timezone = Helpers::getPracticeTimeZone();
        $claim_by = $start_date = $end_date = $aging_by = $aging_start_date = $aging_end_date =  $aging_group_by = $aging_insurance_id = $billing_provider_id = $rendering_provider_id = $facility_id = $status = '';
                
        $search_by = array();
        $claim_by = isset($request['claim_by']) ? $request['claim_by'] : "created_date";
        $aging_by = isset($request['aging_by']) ? $request['aging_by'] : "All";
        if($aging_by!="All" &&  $aging_by!="Unbilled"){
            $data_value = explode("-", $aging_by);
            if((empty($data_value[1])) || $data_value[1] == 'above'){
                $aging_end_date = '';
            }else{
                $aging_end_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$data_value[1]-1)));
            }
            if(strpos($aging_by, ">") !== false){
                $aging_start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", 149))));
            } else{
                if(@$data_value[0]=='Unbilled'){
                    $aging_start_date = 'Unbilled';
                }else{
                    if(@$data_value[0] == 150){
                        $aging_start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]))));                    
                    }else{
                        $aging_start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]-1))));
                    }
                }
            }            
        }
        // Query for aging by bucket search
        if (!empty($request['aging_by'])) {
            if ($aging_by == '150-above') {
                $search = '>150';
            } else {
                $search = $aging_by;
            }
            // for passing filter parameters
            $search_by['Aging Days'][] = $search;            
        }
        
        $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
        /*
        if(empty($export)){
            if(Auth::user()->isProvider()){                 
                $claimsresult->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
            }
        }elseif(isset($request['export_id']) && !empty($request['export_id'])){
            $providerStatus = App\Http\Helpers\Helpers::isProvider($request['export_id']);
            if($providerStatus['status']){
                $claimsresult->where('claim_info_v1.rendering_provider_id',$providerStatus['provider_id']);
            }
        } */
        if (!empty($claim_by)) {
            if ($claim_by == 'created_date') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Transaction Date';
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime(@$date[0]));
                    $end_date = date("Y-m-d", strtotime(@$date[1]));
                    // for passing Aging Date filter parameters                    
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                }
            } else if ($claim_by == 'submitted_date') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Submitted Date';
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime(@$date[0]));
                    $end_date = date("Y-m-d", strtotime(@$date[1]));
                    // for passing Aging Date filter parameters
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                }
            } else if ($claim_by == 'date_of_service') {
                // for passing Aging By filter parameters
                $search_by['Aging By'][] = 'Date of Service';
                if ($createdAt != '') {
                    $date = explode('-', $createdAt);
                    $start_date = date("Y-m-d", strtotime($date[0]));
                    $end_date = date("Y-m-d", strtotime($date[1]));
                    // for passing Aging Date filter parameters
                    $search_by['Aging Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
                } 
            }
        }

        $search_lable = $request['aging_group_by'];
        // Query For Aging Group
        if (!empty($request['aging_group_by'])) {
            $search_by['Aging Group By'][] = rtrim(ucwords(str_replace('_', ' ', $request['aging_group_by'])));
            $aging_group_by = $request['aging_group_by'];
            if ($aging_group_by == 'insurance') {
                if(!empty($request['aging_insurance_id'])){
                    $aging_insurance_id = $request['aging_insurance_id'];
                    $search_by['Insurance'][] = $this->getInsuranceName($request['aging_insurance_id']);
                }else{
                   $search_by['Insurance'][] = "All";
                }

            } else if ($aging_group_by == 'rendering_provider') {
                if(!empty($request['rendering_provider_id'])){
                    $rendering_provider_id = $request['rendering_provider_id'];
                    $renders_id = explode(',', $request['rendering_provider_id']);
                    foreach ($renders_id as $id) {
                        $value_name[] = App\Models\Provider::getProviderFullName($id);
                    }
                    $search_render = implode(", ", array_unique($value_name));
                    $search_by['Rendering Provider'][] = $search_render;
                }
            }else if ($aging_group_by == 'billing_provider') {
                if(!empty($request['billing_provider_id'])){
                    $billing_provider_id = $request['billing_provider_id'];
                    $providers_id = explode(',', $request['billing_provider_id']);
                    foreach ($providers_id as $id) {
                        $value_name[] = App\Models\Provider::getProviderFullName($id);
                    }
                    $search_provider = implode(", ", array_unique($value_name));
                    $search_by['Billing Provider'][] = $search_provider;
                }
            }else if ($aging_group_by == 'facility') {
                if(!empty($request['facility_id'])){
                    $facility_id = $request['facility_id'];
                    if (strpos($request['facility_id'], ',') !== false){
                        $search_name = Facility::select('facility_name');
                        $facility_names = $search_name->whereIn('id', explode(',', $request['facility_id']))->get();
                        foreach ($facility_names as $name) {
                            $value_names[] = $name['facility_name'];
                        }
                        $search_filter = implode(", ", array_unique($value_names));
                    }else{                        
                        $facility_names = Facility::select('facility_name')->where('id', $request['facility_id'])->get();
                        foreach ($facility_names as $facility_na) {
                            $search_filter = $facility_na['facility_name'];
                        }
                    }
                    $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
                }
            }
        }

        // Filter by claim status
        if (isset($request['status']) && !empty($request['status']) && $request['status'] != 'All') {
            $status = $request["status"];
            $search_by['Status'][] = $request["status"];
        }
        
        $show_flag = $aging_by;
        $group_by = "ALL";
        $summaries = [];
        if(Auth::check() && Auth::user()->isProvider()){
            $rendering_provider_id = Auth::user()->provider_access_id;
            $value_name[] = App\Models\Provider::getProviderFullName($rendering_provider_id);
            $search_render = implode(", ", array_unique($value_name));
            $login = 'isprovider';
        }else{
            $login = '';
        }
        if($request['aging_group_by'] == 'facility' || $request['aging_group_by'] == 'billing_provider' || $request['aging_group_by'] == 'rendering_provider'){            
            $summaries = DB::select('call arAgingAnalysisSummary("' . $claim_by . '", "' . $start_date . '", "' . $end_date . '",  "' . $aging_by . '",  "' . $aging_start_date . '",  "' . $aging_end_date . '",  "' . $aging_group_by . '", "' . $aging_insurance_id . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '",  "' . $status . '",  "' . $practice_timezone . '","' . $login . '")');
        }
        if(!empty($summaries)){
            foreach($summaries as $res){
                $summ[$res->{$request['aging_group_by']}]['total_charge'] = $res->total_charge;
                $summ[$res->{$request['aging_group_by']}]['unbilled'] = $res->unbilled;
                $summ[$res->{$request['aging_group_by']}]['total_pat'] = $res->total_pat;
                $summ[$res->{$request['aging_group_by']}]['total_ins'] = $res->total_ins;
                $summ[$res->{$request['aging_group_by']}]['days30'] = $res->days30;
                $summ[$res->{$request['aging_group_by']}]['days60'] = $res->days60;
                $summ[$res->{$request['aging_group_by']}]['days90'] = $res->days90;
                $summ[$res->{$request['aging_group_by']}]['days120'] = $res->days120;
                $summ[$res->{$request['aging_group_by']}]['days150'] = $res->days150;
                $summ[$res->{$request['aging_group_by']}]['daysabove'] = $res->daysabove;
                $summ[$res->{$request['aging_group_by']}]['total'] = $res->total;
            }
        }
        if(isset($summ))
            $summaries = $summ;
        
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
            $sp_return_result = DB::select('call arAgingAnalysisDetails("' . $claim_by . '", "' . $start_date . '", "' . $end_date . '",  "' . $aging_by . '",  "' . $aging_start_date . '",  "' . $aging_end_date . '",  "' . $aging_group_by . '", "' . $aging_insurance_id . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '",  "' . $status . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '", "' . $login . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->aging_count;
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
            $sp_return_result = DB::select('call arAgingAnalysisDetails("' . $claim_by . '", "' . $start_date . '", "' . $end_date . '",  "' . $aging_by . '",  "' . $aging_start_date . '",  "' . $aging_end_date . '",  "' . $aging_group_by . '", "' . $aging_insurance_id . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '",  "' . $status . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '", "' . $login . '")');
            $sp_return_result = (array) $sp_return_result;
			
			
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

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call arAgingAnalysisDetails("' . $claim_by . '", "' . $start_date . '", "' . $end_date . '",  "' . $aging_by . '",  "' . $aging_start_date . '",  "' . $aging_end_date . '",  "' . $aging_group_by . '", "' . $aging_insurance_id . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '",  "' . $status . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '", "' . $login . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $aging_report_list = $sp_return_result;
        
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('aging_report_list', 'show_flag', 'pagination_prt', 'pagination', 'group_by', 'search_by', 'start_date', 'end_date','search_lable', 'summaries')));
    }

    ## get insurance name for responsibility

    public static function getInsuranceName($id) {
        $insurance_name = App\Models\Insurance::where('id', $id)->pluck("short_name")->first();
        return $insurance_name;
    }

    ## Get billed bucket amount

    public function getagedate($start, $end, $responsibility, $claim_by, $aging_by, $claim) {
        // get for bucket date
        $start_date = '-' . $start . ' day';
        $start_date = date('Y-m-d  h:i:s', strtotime($start_date));

        $end_date = '-' . $end . ' day';
        $end_date = date('Y-m-d  h:i:s', strtotime($end_date));

        $claimsresult = DB::table('claim_info_v1')->leftjoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')->join('claim_tx_desc_v1', 'claim_tx_desc_v1.claim_id', '=', 'claim_info_v1.id')->SelectRaw("pmt_claim_fin_v1.patient_paid,pmt_claim_fin_v1.insurance_paid,pmt_claim_fin_v1.patient_due,pmt_claim_fin_v1.insurance_due,pmt_claim_fin_v1.insurance_adj,pmt_claim_fin_v1.patient_adj,pmt_claim_fin_v1.withheld,claim_info_v1.id as counts,max(claim_tx_desc_v1.created_at) as created_ats,claim_tx_desc_v1.id as trx_id,claim_tx_desc_v1.pat_bal, claim_info_v1.insurance_id")
                        ->whereNull('claim_info_v1.deleted_at')->where(function($qry) {
            $qry->where(function($query) {
                $query->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '>', 0);
            })->Orwhere(function($query) { // for  patient submitted
                $query->Where('claim_info_v1.insurance_id', 0)->where(function($qryy) {
                    $qryy->whereIn('claim_tx_desc_v1.transaction_type', ["Responsibility", "New Charge"])->where('claim_tx_desc_v1.responsibility', '0')
                            ->whereIn('claim_tx_desc_v1.id', function($query) {
                                $query->selectRaw('max(claim_tx_desc_v1.id)')->from('claim_tx_desc_v1')->groupBy('claim_id');
                            }); // for geting latest reord on created date
                });
            });
        });
        //Model::whereIn('id', function($query) { $query->selectRaw('max(id)')->from('table')->groupBy('thread_id'); })->toSql();
        //echo '<pre>';$claimsresult->where('claim_tx_desc_v1.claim_id',59);
        if ($claim_by == 'created_date') {
            if ($end == "above") {
                $claimsresult->where(DB::raw('DATE(claim_info_v1.created_at)'), "<=", $start_date);
            } else {
                $claimsresult->where(DB::raw('DATE(claim_info_v1.created_at)'), ">=", $end_date)->where(DB::raw('DATE(claim_info_v1.created_at)'), "<=", $start_date);
            }
            if ($responsibility != "Patient") {
                $claimsresult->where('claim_info_v1.claim_submit_count', '!=', 0);
            }
        } else if ($claim_by == 'submitted_date') {

            if ($end == "above") {
                if ($responsibility != 'Patient') {
                    $claimsresult->where('claim_info_v1.last_submited_date', "<=", $start_date);
                } else {
                    $claimsresult->where('claim_tx_desc_v1.created_at', "<=", $start_date);
                }
            } else {
                if ($responsibility != 'Patient') {
                    $claimsresult->where('claim_info_v1.last_submited_date', ">=", $end_date)->where('claim_info_v1.last_submited_date', "<=", $start_date);
                } else {
                    $claimsresult->where('claim_tx_desc_v1.created_at', ">=", $end_date)->where('claim_tx_desc_v1.created_at', "<=", $start_date);
                }
                //  $claimsresult->where('claim_info_v1.claim_submit_count','!=',0);
            }
        } else {
            if ($end == "above") {
                $claimsresult->where('claim_info_v1.date_of_service', "<=", $start_date);
            } else {
                $claimsresult->where('claim_info_v1.date_of_service', ">=", $end_date)->where('claim_info_v1.date_of_service', "<=", $start_date);
            }
            $claimsresult->where('claim_info_v1.claim_submit_count', '!=', 0);
        }

        // $claimsresult->whereIn('claim_info_v1.status',['Paid']);
        $claimsresult->where('claim_info_v1.claim_number', '=', $claim);

        $pmt_amt_claim = $claimsresult->get();
        //echo @$pmt_amt_claim[0]->trx_id.'<br>';

        $pat_bal = @$pmt_amt_claim[0]->patient_due;
        $ins_bal = (@$pmt_amt_claim[0]->insurance_id != 0) ? (@$pmt_amt_claim[0]->insurance_due - (@$pmt_amt_claim[0]->patient_paid + @$pmt_amt_claim[0]->patient_adj)) : @$pmt_amt_claim[0]->insurance_due;
        $total_bal = $pat_bal + $ins_bal;
        return $total_bal;
    }

    ### Export Start ###

    public function getAgingReportExport($unbilled) {
        $total_charge_count = 0;
        $get_list = [];
        $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due = $total_due = 0;
        foreach ($unbilled as $billed) {
            foreach ($billed as $keys => $unbilled) {
                foreach ($unbilled as $unbilled) {
                    $patient_name = Helpers::getNameformat(@$unbilled->patient->last_name, @$unbilled->patient->first_name, @$unbilled->patient->middle_name);
                    $get_arr = [];
                    $get_arr['account_no'] = @$unbilled->patient->account_no;
                    $get_arr['patient_name'] = $patient_name;
                    $get_arr['claim_number'] = @$unbilled->claim_number;
                    $get_arr['date_of_service'] = @$unbilled->date_of_service;
                    $get_arr['insurance_details'] = isset($unbilled->insurance_details) ? $unbilled->insurance_details->insurance_name : 'Patient';
                    $billed_amt += $get_arr['billed'] = @$unbilled->total_charge;
                    $_0to30 += $get_arr['0to30'] = ((@$keys == "0-30")) ? @$unbilled->balance_amt : '0.00';
                    $_31to60 += $get_arr['31to60'] = ((@$keys == "31-60")) ? @$unbilled->balance_amt : '0.00';
                    $_61to90 += $get_arr['61to90'] = ((@$keys == "61-90")) ? @$unbilled->balance_amt : '0.00';
                    $_91to120 += $get_arr['91to120'] = ((@$keys == "91-120")) ? @$unbilled->balance_amt : '0.00';
                    $_121to150 += $get_arr['121to150'] = ((@$keys == "121-150")) ? @$unbilled->balance_amt : '0.00';
                    $_150_above += $get_arr['150toabove'] = ((@$keys == "151-above")) ? @$unbilled->balance_amt : '0.00';
                    $patient_due += $get_arr['patient_due'] = @$unbilled->patient_due;
                    $insurance_due += $get_arr['insurance_due'] = @$unbilled->insurance_due;
                    $total_due += $get_arr['total_due'] = Helpers::priceFormat(@$unbilled->insurance_due + @$unbilled->patient_due);
                    $get_list[$total_charge_count] = $get_arr;
                    $total_charge_count++;
                }
            }
            $get_result = $get_list;
        }
        $get_result[$total_charge_count] = ['account_no' => 'Total', 'patient_name' => '', 'claim_number' => '', 'date_of_service' => '', 'insurance_details' => '', 'billed' => '' . Helpers::priceFormat($billed_amt), '0to30' => '' . Helpers::priceFormat($_0to30), '31to60' => '' . Helpers::priceFormat($_31to60), '61to90' => '' . Helpers::priceFormat($_61to90), '91to120' => '' . Helpers::priceFormat($_91to120), '121to150' => '' . Helpers::priceFormat($_121to150), '150toabove' => '' . Helpers::priceFormat($_150_above), 'patient_due' => '' . Helpers::priceFormat($patient_due), 'insurance_due' => '' . Helpers::priceFormat($insurance_due), 'total_due' => '' . Helpers::priceFormat($total_due)];

        $result["value"] = json_decode(json_encode($get_result));
        $result["exportparam"] = array(
            'filename' => 'Aging Report',
            'heading' => '',
            'fields' => array(
                'account_no' => 'Acc No',
                'patient_name' => 'Patient name',
                'claim_number' => 'Claim No',
                'date_of_service' => 'DOS',
                'insurance_details' => 'Insurance Details',
                'billed' => 'Billing',
                '0to30' => '0 to 30',
                '31to60' => '31 to 60',
                '61to90' => '61 to 91',
                '91to120' => '91 to 120',
                '121to150' => '121 to 150',
                '150toabove' => '150 above',
                'patient_due' => 'Patient Due',
                'insurance_due' => 'Insurance Due',
                'total_due' => 'Total Due',
            )
        );
        return $result;
    }

    ### Export End ####
    /*     * * result function start ** */

    public function getAgingReportResult($request) {
        $unbilled_arr = '';
        ### Initialize two rows of result ###
        $responce['header'] = ["AR Days", "Unbilled($)", '', "0-30($)", '', "31-60($)", '', "61-90($)", '', "91-120($)", '', "121-150($)", '', ">150($)", '', "Totals($)", ''];

        $responce['name'] = ["", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value"];
        $claims = ClaimInfoV1::with(['patient' => function($query) {
                        $query->select('id', 'last_name', 'first_name', 'middle_name', 'account_no');
                    }, 'insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }])
                ->where('claim_submit_count', '==', 0)
                ->where('self_pay', '==', 'No')
                ->orderBy('id', 'asc');
        $responce['header'] = ['Acc No', 'Patient Name', 'Claim No', 'DOS', 'Responsibility', 'Billed($)', "0-30($)", "31-60($)", "61-90($)", "91-120($)", "121-150($)", ">151($)", 'Pat Bal($)', 'Ins Bal($)', "Total Bal($)"];
        $claim_by = $request['claim_by'];
        $age_date = ["0-30", "31-60", "61-90", "91-120", "121-150", "151-above"];

        $provider = $request['rendering_provider_id'];
        $bill_providers = $request['billing_provider_id'];
        $facility = $request['facility_id'];
        $insurance = $request['insurance_id'];

        $render_provider = $billing_provider_id = $facility_id = $insurance_id = [];
        if ($request['aging_by'] == 'all' || $provider != 'all' || $bill_providers != 'all' || $facility_id != 'all' || $insurance_id != 'all') {
            $patient = 'all';
            $claim_count = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $provider, $bill_providers, $patient, $facility, $insurance);
            $render_provider[] = $provider;
            $billing_provider_id[] = $bill_providers;
            $facility_id[] = $facility;
        }


        if ($request['aging_by'] == 'rendering_provider' && $provider == 'all') {
            $rendering_provider = array_unique(ClaimInfoV1::pluck('rendering_provider_id')->all());
            foreach ($rendering_provider as $ren_provider1) {
                $bill_provider = $patients = $insurance = $facility = "all";
                $claim_count[] = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $ren_provider1, $bill_provider, $patients, $facility, $insurance);
                $render_provider[] = $ren_provider1;
            }
            unset($claim_count['unbilled']);
        }

        //Aging by insurance
        if ($request['aging_by'] == 'insurance' && $insurance == 'all') {
            $insurances = array_unique(ClaimInfoV1::pluck('insurance_id')->all());

            foreach ($insurances as $insurances) {
                if ($insurances != 0) {
                    $bill_provider = $patients = $ren_provider = $facility = "all";
                    $claim_count[] = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $ren_provider, $bill_provider, $patients, $facility, $insurances);
                    $insurance_id[] = $insurance;
                    $ins[] = $insurances;
                }
            }
            unset($claim_count['unbilled']);
        }
        //Aging by Billing provider
        if ($request['aging_by'] == 'billing_provider' && $bill_providers == 'all') {
            $billing_provider = array_unique(ClaimInfoV1::pluck('billing_provider_id')->all());
            foreach ($billing_provider as $bill_provider) {
                $ren_provider = $patients = $insurance = "all";
                $claim_count[] = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $ren_provider, $bill_provider, $patients, $facility, $insurance);
                $billing_provider_id[] = $bill_provider;
            }
            unset($claim_count['unbilled']);
        }
        // Aging by patient
        if ($request['aging_by'] == 'patient') {
            $patients = array_unique(ClaimInfoV1::pluck('patient_id')->all());
            foreach ($patients as $patient) {
                $bill_provider = $ren_provider = $insurance = "all";
                $claim_count[] = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $ren_provider, $bill_provider, $patient, $facility, $insurance);
                $patient_id[] = $patient;
            }
            unset($claim_count['unbilled']);
        }
        //Aging by Facility
        if ($request['aging_by'] == 'facility' && $request['facility_id'] == 'all') {
            $facility = array_unique(ClaimInfoV1::pluck('facility_id')->all());
            foreach ($facility as $facility) {
                $bill_provider = $ren_provider = $patient = $insurance = "all";
                $claim_count[] = $this->aging_days1($claim_by, $request["aging_days"], $age_date, $ren_provider, $bill_provider, $patient, $facility, $insurance);
                $facility_id[] = $facility;
            }
            unset($claim_count['unbilled']);
        }

        $responce['title'] = $request;
        $responce['unbilled'] = $claim_count;
        $responce['rendering_provider_id'] = $provider;
        $responce['billing_provider_id'] = $bill_providers;
        $responce['bill_provider'] = @$bill_provider;
        $responce['aging_by'] = $request['aging_by'];
        $responce['render_provider'] = $render_provider;
        $responce['billing_provider'] = $billing_provider_id;
        $responce['patient_id'] = @$patient_id;
        $responce['facility_id'] = $facility_id;
        $responce['insurance_id'] = $insurance;
        $responce['ins'] = @$ins;
        $responce['claim_by'] = @$claim_by;
        $responce['aging_days'] = $request["aging_days"];

        return $responce;
    }

    /*     * * result function end ** */

    function aging_days1($claim_by, $request, $age_date, $provider, $bill_provider, $patient, $facility, $insurance) {

        $unbilled = $total_arr = $billed = [];
        foreach ($age_date as $key => $value) {
            $claims = ClaimInfoV1::with(['patient' => function($query) {
                            $query->select('id', 'last_name', 'first_name', 'middle_name', 'account_no');
                        }, 'insurance_details' => function($query) {
                            $query->select('id', 'short_name');
                        }])->where('claim_submit_count', '=', 0)->where('self_pay', '=', 'No');
            if ($provider != 'all') {
                $claims->where('rendering_provider_id', $provider);
            }
            if ($bill_provider != 'all') {
                $claims->where('billing_provider_id', $bill_provider);
            }

            if ($patient != 'all') {
                $claims->where('patient_id', $patient);
            }
            if ($insurance != 'all') {
                $claims->where('insurance_id', $insurance);
            }
            if ($facility != 'all') {
                $claims->where('facility_id', $facility);
            }
            if (($request == "0-30" || $request == 'all') && $age_date[0] == $value) {
                $last_month_carbon = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(30)));
                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(0)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
                else {
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));
                    $unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                }
                $billed[$value] = $unbilled->get();
            }
            if (($request == "31-60" || $request == 'all') && $age_date[1] == $value) {
                $last_month_carbon = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(60)));
                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(31)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
                else {
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(31)));
                    $unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                }

                $billed[$value] = $unbilled->get();
            }
            if (($request == "61-90" || $request == 'all') && $age_date[2] == $value) {
                $last_month_carbon = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(90)));
                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(61)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
                else {
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(61)));
                    $unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                }
                $billed[$value] = $unbilled->get();
            }
            if (($request == "91-120" || $request == 'all') && $age_date[3] == $value) {
                $last_month_carbon = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(120)));
                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(91)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
                else {
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(91)));
                    $unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                }
                $billed[$value] = $unbilled->get();
            }
            if (($request == "121-150" || $request == 'all') && $age_date[4] == $value) {
                $last_month_carbon = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(150)));
                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(121)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('created_at', '>=', $last_month_carbon)->where('created_at', '<=', $current_month);
                else {
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(121)));
                    $unbilled = $claims->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                }//$unbilled = $claims->orWhere('created_at','>=',$last_month_carbon)->where('created_at','<=',$current_month);
                $billed[$value] = $unbilled->get();
            }
            if (($request == "150-above" || $request == 'all') && $age_date[5] == $value) {

                $current_month = date('Y-m-d h:i:s', strtotime(Carbon::now()->subDay(151)));
                if ($claim_by == 'create_by')
                    $unbilled = $claims->where('claim_submit_count', '=', 0)->where('created_at', '<=', $current_month);
                else {
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(151)));
                    $unbilled = $claims->where('claim_submit_count', '=', 0)->where('date_of_service', '<=', $current_month);
                }
                $billed[$value] = $unbilled->get();
            }
        }
        $res['unbilled'] = $billed;
        return $res;
    }

    /*     * * Aging wise calculate function start here ** */

    public function AgingCalc($type, $type_field, $current_id, $age_date) {
        $result_value = $result_arr = $total_arr = $claims_count = $claims_amt = [];
        foreach ($age_date as $key => $value) {
            ### Patient[Responsibilty] wise get record ###
            if ($type == "patient")
                $claim_arr = ClaimInfoV1::whereIn('status', ['Patient', 'Paid'])->where('self_pay', 'Yes')->where('patient_paid', "!=", '0.00')->where('patient_due', "!=", '0.00');

            ### Insurance[Responsibilty] wise get record ###
            if ($type == "insurance")
                $claim_arr = ClaimInfoV1::where('status', 'Submitted')->where('self_pay', 'No')->where('insurance_due', "!=", '0.00')->where('claim_submit_count', "!=", '0');
            ### Provider, Facility, Insurance wise individual record ###
            if ($type == "provider")
                $claim_arr = ClaimInfoV1::where($type_field, $current_id)->where(function($query) {
                            return $query->where('patient_paid', "!=", '0.00')->orWhere('insurance_paid', "!=", '0.00');
                        })->where(function($query) {
                            return $query->where('patient_due', "!=", '0.00')->orWhere('insurance_due', "!=", '0.00');
                        })->where('claim_submit_count', "!=", '0');

            $date_key = explode("-", $value);
            $start_date = date('Y-m-d h:i:s', strtotime('-' . $date_key[0] . ' day'));
            $end_date = ($date_key[1] == "above") ? 'above' : date('Y-m-d h:i:s', strtotime('-' . $date_key[1] . ' day'));
            if ($end_date == "above") {
                $claim_arr->where('created_at', "<=", $start_date);
            } else {
                $claim_arr->where('created_at', "<=", $start_date)->where('created_at', ">=", $end_date);
            }
            $result['claims'] = (int) $claim_arr->count();
            $result['value'] = (int) $claim_arr->sum('total_charge');
            $claims_count[$key] = $result['claims'];
            $claims_amt[$key] = $result['value'];
            $total_arr[$value] = $result;
        }
        $result_value["aging"] = $total_arr;
        $result_value["claims"] = array_sum($claims_count);
        $result_value["value"] = array_sum($claims_amt);
        return $result_value;
    }

    /*     * * Aging wise calculate function start here ** */
    /*     * * Percentage function start here ** */

    public function SumAndPercentageCalc($array_list) {
        /*         * * Add Two array values start ** */
        $get_combined_value = $this->SumMultiArrayList($array_list);
        $result['total'] = $get_combined_value;
        $result['total'][0] = "Total AR";
        /*         * * Add Two array values end ** */

        /*         * * Get percentage of total values start ** */
        foreach ($get_combined_value as $key => $value) {
            $total_value = $result['total'][count($result['total']) - 1];
            $percentage = 0;
            if ($key != 0 && $key % 2 == 0) {
                if ($total_value > 0 && $value > 0)
                    $percentage = round(($value / $total_value) * 100, 2);
                $percentage_array[$key] = $percentage . "%";
            } else
                $percentage_array[$key] = '';
        }
        $result['total_percentage'] = $percentage_array;
        $result['total_percentage'][0] = "Total AR %";
        /*         * * Get percentage of total values start ** */
        return $result;
    }

    /*     * * Percentage function end here ** */

    /*     * * Adding multi dimentional array function start here ** */

    public function SumMultiArrayList($array) {
        $new_array = $added_array = $a = array();
        foreach ($array as $value) {
            if (count($value) > 0)
                $new_array[] = $value;
        }
        foreach ($new_array as $key => $values_arr) {
            foreach ($values_arr as $key_arr => $values) {

                $value = ($key == 0) ? $new_array[$key][$key_arr] : $new_array[$key][$key_arr] + $a[$key - 1][$key_arr];
                $a[$key][] = (string) $value;
            }
            $added_array = $a[$key];
        }
        return $added_array;
    }

    /*     * * Adding multi dimentional array function end here ** */

    ############ Aging Report End ############
    ##### Work RVU Report Start ###
    // **** Work RVU Reports  ******//
    /***
    Work RVU Report By Thilagavathy
    ****/

    public function getWorkrvuReportApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('workrvu');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
      //  $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData', 'search_fields')));
    }

    public function getWorkrvulistApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $search_by = array();
        $workrvu =ClaimCPTInfoV1::selectRaw('claim_cpt_info_v1.id,claim_cpt_info_v1.cpt_code,claim_cpt_info_v1.patient_id, claim_cpt_info_v1.created_at as transaction_date,claim_cpt_info_v1.claim_id,DATE(claim_cpt_info_v1.dos_from) as date_of_service,claim_cpt_info_v1.charge,claim_cpt_info_v1.unit as units,
            cpts.short_description,
            cpts.medium_description,
            cpts.work_rvu,
            cpts.procedure_category,
            cpts.cpt_hcpcs')
        ->wherenull('claim_cpt_info_v1.deleted_at')
        //->with('cptdetails')
        ->leftjoin(DB::raw("(SELECT
                cpts.short_description,
                cpts.medium_description,
                cpts.work_rvu,
                cpts.procedure_category,
                cpts.cpt_hcpcs            
            FROM cpts
            WHERE cpts.deleted_at IS NULL
                GROUP BY cpts.cpt_hcpcs
            ) as cpts"), function($join) {
            $join->on('cpts.cpt_hcpcs', '=', 'claim_cpt_info_v1.cpt_code');
        })
        ->with('claim_details')
        ->with('patient_details');

        //Search By
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-',$request['select_transaction_date']);
            /*$start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));  */
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date']= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
            }
            else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
               $workrvu->whereHas('claim_details', function($q) use($start_date,$end_date) {
                $q->where(DB::raw('created_at'), '>=', $start_date)->where(DB::raw('created_at'), '<=', $end_date);
                });
              
        }
         if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $date = explode('-',$request['select_date_of_service']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
               $workrvu->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '>=', $start_date)->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '<=', $end_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['DOS']= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
            }
            else{
                $search_by['DOS'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));   
            }
        }
          //  $search_by['Payment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        if(!empty($request['rendering_provider_id'])){
            $renders_id = explode(',',$request['rendering_provider_id']);
             $workrvu->whereHas('claim_details', function($q) use($renders_id) {
                  $q->whereIn('rendering_provider_id',$renders_id);
                });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Rendering Provider'] = $search_render; 
            }
            else{
                $search_by['Rendering Provider'][] = $search_render;
            }
        }

        if(isset($request['exports']) && $request['exports'] == 'pdf') {
            $workrvu_list = $workrvu->get()->toArray();
        }elseif(isset($request['export']) && $request['export'] == 'xlsx') {
            $workrvu_list = $workrvu->get()->toArray();
        }else{
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $workrvu = $workrvu->paginate($paginate_count);
            $ref_array = $workrvu->toArray();
            $pagination_prt = $workrvu->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $workrvu = json_decode($workrvu->toJson());
            $workrvu_list = $workrvu->data;
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('workrvu_list','pagination','pagination_prt','search_by','workrvu_list_data')));
    }
    /* Stored procedure for WorkRVU - Anjukaselvan*/
    public function getWorkrvulistApiSP($export = '', $data = '') {
         if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        
        $practice_timezone = Helpers::getPracticeTimeZone();
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $rendering_provider_id =  '';
        $search_by = array();

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $date = explode('-',$request['select_transaction_date']);
                $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
                $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
            }
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                $date = explode('-',$request['select_date_of_service']);
                $dos_start_date = date("Y-m-d", strtotime($date[0]));
                if($dos_start_date == '1970-01-01'){
                    $dos_start_date = '0000-00-00';
                }
                $dos_end_date = date("Y-m-d", strtotime($date[1]));
                $search_by['DOS'][]= date("m/d/Y",strtotime($dos_start_date)).' to '.date("m/d/Y",strtotime($dos_end_date));
            }

        if(!empty($request['rendering_provider_id'])){
            $rendering_provider_id = $request['rendering_provider_id'];
            foreach ((array)$rendering_provider_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = array_unique($value_name);
            $search_by['Rendering Provider'] = $search_render;
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
            $sp_return_result = DB::select('call workrvu("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->workrvu_count;
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
            $sp_return_result = DB::select('call workrvu("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;

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

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call workrvu("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $workrvu_list = $sp_return_result;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('workrvu_list','pagination','pagination_prt','search_by')));
    }

      ### CHARGE CATEGORY REPORT START ###
     public function getchargecategoryReportApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('charge_category');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
      //  $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData', 'search_fields')));
    }
    public function getchargecategoryresultApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $search_by = array();
        $charges = ClaimCPTInfoV1::selectRaw('claim_cpt_info_v1.id,claim_cpt_info_v1.cpt_code,claim_cpt_info_v1.patient_id, (claim_cpt_info_v1.created_at) as transaction_date,claim_cpt_info_v1.claim_id,DATE(claim_cpt_info_v1.dos_from) as date_of_service,sum(claim_cpt_info_v1.charge) as charge,sum(claim_cpt_info_v1.unit) as units,sum(pmt_claim_cpt_fin_v1.patient_paid) as pat_paid,sum(pmt_claim_cpt_fin_v1.insurance_paid) as ins_paid,sum(pmt_claim_cpt_fin_v1.co_pay) as co_pay,claim_info_v1.rendering_provider_id,cpts.short_description,
            cpts.description,
            cpts.work_rvu,
            cpts.pro_cat,
            cpts.cpt_hcpcs,
            procedure_categories.procedure_category')
        ->wherenull('claim_cpt_info_v1.deleted_at')
       // ->with('claimCptFinDetails')
    //   ->with(['cptdetails' => function($query) {
     //       return $query->groupBy('procedure_category');
      //  }])
        //->with('cptdetails')
        ->leftjoin(DB::raw("(SELECT 
                cpts.short_description,
                cpts.medium_description as description,
                cpts.work_rvu,
                cpts.procedure_category as pro_cat,
                cpts.cpt_hcpcs
            FROM cpts
            WHERE cpts.deleted_at IS NULL
                GROUP BY cpts.cpt_hcpcs
            ) as cpts"), function($join) {
            $join->on('cpts.cpt_hcpcs', '=', 'claim_cpt_info_v1.cpt_code');
        })
        ->leftjoin(DB::raw("(SELECT 
                procedure_categories.id,
                procedure_categories.procedure_category,
                procedure_categories.status
            FROM procedure_categories
            WHERE procedure_categories.deleted_at IS NULL
                and status = 'Active'
            ) as procedure_categories"), function($join) {
            $join->on('procedure_categories.id', '=', 'cpts.pro_cat');
        })
        ->leftjoin('pmt_claim_cpt_fin_v1', 'pmt_claim_cpt_fin_v1.claim_cpt_info_id', '=', 'claim_cpt_info_v1.id')
        ->leftjoin('claim_info_v1', 'claim_info_v1.id', '=', 'claim_cpt_info_v1.claim_id')
        ->groupBy('claim_info_v1.rendering_provider_id')
        ->groupby('claim_cpt_info_v1.cpt_code')
        ->groupBy('claim_cpt_info_v1.claim_id')
        ->orderBy('claim_info_v1.rendering_provider_id','Desc')
        ->with('claim_details')
        ->with('patient_details');//->toSql();dd($charges);

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-',$request['select_transaction_date']);
            /*$start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01  -01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1])); */
            $search_by['Transaction Date'][]= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
               $charges->where(DB::raw('(claim_info_v1.created_at)'), '>=', $start_date)->where(DB::raw('(claim_info_v1.created_at)'), '<=', $end_date);
               
        }
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $date = explode('-',$request['select_date_of_service']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
               $charges->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '>=', $start_date)->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '<=', $end_date);
            $search_by['DOS'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
        }
            //  $search_by['Payment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
      /*  if(!empty($request['cpt_category'])){
            $cpt_category = $request['cpt_category'];
            if($cpt_category != 'All'){
                $charges->whereHas('cptdetails', function($q) use($cpt_category) {
                    $q->where('procedure_category',$cpt_category);
                });
            }
            $search_by['CPT/HCPCS Category'][] = $cpt_category;
        }*/
        if(!empty($request['cpt_category'])){
            $cpt_category = $request['cpt_category'];
            if($cpt_category != '0'){
//                $charges->whereHas('cptdetails', function($q) use($cpt_category) {
//                    $q->where('procedure_category',$cpt_category);
//                });
                $charges->where('cpts.pro_cat',$cpt_category);
                $category_name = ProcedureCategory::where('id',$cpt_category)->where('status','Active')->pluck('procedure_category')->first();
            } else{
                $category_name = 'All';
            }
            $search_by['CPT/HCPCS Category'][] = $category_name;
        }
        if(!empty($request['rendering_provider_id'])){
            $renders_id = explode(',',$request['rendering_provider_id']);
            $charges->whereHas('claim_details', function($q) use($renders_id) {
                $q->whereIn('rendering_provider_id',$renders_id);
            });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }
        
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            $search_by['CPT Type'][] = 'Custom Range';
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])) {                
                $cpts_list = Helpers::CptsRangeBetween($request['custom_type_from'], $request['custom_type_to']);                                  
                $charges->whereIn('claim_cpt_info_v1.cpt_code', $cpts_list);
                //$charges->where('claim_cpt_info_v1.cpt_code','>=',$request['custom_type_from'])->where('claim_cpt_info_v1.cpt_code','<=',$request['custom_type_to']);
            }
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            $search_by['CPT Type'][] = 'CPT Code';
            if(!empty($request['cpt_code_id'])){
                // Search comma separated cpt codes
                $cptCodes = array_map('trim', explode(',', $request['cpt_code_id']));
                $charges->Where(function ($q) use ($cptCodes) {
                    foreach ($cptCodes as $key => $dc) {
                        if($key == 0)
                            $q->where('claim_cpt_info_v1.cpt_code', $dc);
                       else
                            $q->orWhere('claim_cpt_info_v1.cpt_code', $dc);
                    }
                });
                //$cpt_list->where('claim_cpt_info_v1.cpt_code',$request['cpt_code_id']);
                $search_by['CPT Code'][] = $request['cpt_code_id'];
            }
        }
        $charges_list = $charges->get();//dd($charges_list);
        $charges_list = $charges_list->toArray();
        $total_arr = [];
        if(count($charges_list) > 0){
            foreach ($charges_list as $key => $value) {
                $provider_id = $value['claim_details']['rendering_provider_id'];
                $provider_short_name = $value['claim_details']['rend_providers']['short_name'];
                $provider_name = $value['claim_details']['rend_providers']['provider_name'];
                $cpt_code = $value['cpt_code'];
                $charges_lists[$provider_id][$cpt_code]['provider_id'] =$provider_id;
                $charges_lists[$provider_id][$cpt_code]['cpt_code'] =$cpt_code;
                //$charges_lists[$provider_id][$cpt_code]['description'] =$value['cptdetails']['medium_description'];
                $charges_lists[$provider_id][$cpt_code]['description'] =$value['description'];
                //$charges_lists[$provider_id][$cpt_code]['procedure_category'] =$value['cptdetails']['pro_category']['procedure_category'];
                $charges_lists[$provider_id][$cpt_code]['procedure_category'] =$value['procedure_category'];
                $charges_lists[$provider_id][$cpt_code]['provider_short_name'] =$provider_short_name;
                $charges_lists[$provider_id][$cpt_code]['provider_name'] =$provider_name;
                $charges_lists[$provider_id][$cpt_code]['units'] = @$charges_lists[$provider_id][$cpt_code]['units'] + $value['units'];
                $charges_lists[$provider_id][$cpt_code]['ins_paid'] = @$charges_lists[$provider_id][$cpt_code]['ins_paid'] + $value['ins_paid'];
                $charges_lists[$provider_id][$cpt_code]['pat_paid'] = @$charges_lists[$provider_id][$cpt_code]['pat_paid'] + $value['pat_paid'];
                $charges_lists[$provider_id][$cpt_code]['co_pay'] = @$charges_lists[$provider_id][$cpt_code]['co_pay'] + $value['co_pay'];
                $value['payment'] = $charges_lists[$provider_id][$cpt_code]['ins_paid'] + $charges_lists[$provider_id][$cpt_code]['pat_paid'] + $charges_lists[$provider_id][$cpt_code]['co_pay'];
                //$charges_lists[$provider_id][$cpt_code]['work_rvu'] = $value["cptdetails"]['work_rvu'];
                $charges_lists[$provider_id][$cpt_code]['work_rvu'] = $value['work_rvu'];
                $charges_lists[$provider_id][$cpt_code]['charge'] = @$charges_lists[$provider_id][$cpt_code]['charge'] + $value['charge'];
                $charges_lists[$provider_id][$cpt_code]['payment'] =$value['payment'];                
            }
            $count = 0;
            foreach ($charges_lists as $key => $value) {
                foreach($value as $cpt_code_id => $values){
                $provider_id = $key;
                $values['units'] = $values['units'];
                    $temp_array[$count] = $values;
                    $count++;
                }
            }
        } else{
           $charges_lists = [];
           $charges_list = [];
           $temp_array = [];           
        }
            foreach ($temp_array as $key => $value) {
                $provider_id = $value['provider_id'];
                $value['work_rvu'] = $value['work_rvu'];
                $value['payment'] = @$value['payment'] ;
                $total_arr[$provider_id]['last_rec'] = $key;
                $total_arr[$provider_id]['rec_cnt'] = isset($total_arr[$provider_id]['rec_cnt']) ? ($total_arr[$provider_id]['rec_cnt']+1) : 1;
                $total_arr[$provider_id]['units'] = @$total_arr[$provider_id]['units'] + $value['units'];
                $total_arr[$provider_id]['charge'] = @$total_arr[$provider_id]['charge'] + $value['charge'];
                $total_arr[$provider_id]['payment'] = @$total_arr[$provider_id]['payment'] + $value['payment'] ;
                $total_arr[$provider_id]['work_rvu'] = @$total_arr[$provider_id]['work_rvu'] + $value['work_rvu'] ;
            }

        $export_array = $temp_array;
        $temp = [];
        if ($export == "") {
            $report_array = $this->paginate($temp_array)->toArray();
            $pagination_prt = $this->paginate($temp_array)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $temp = array_chunk($temp_array, 25, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($temp_array)) {
            $start = isset($request['page']) ? $request['page'] - 1 : 0;
            $temp_array = $temp[$start];
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges_list','pagination','pagination_prt','search_by','total_arr','export_array','charges_lists','temp_array')));        
    }

    /** Stored procedure for charge category report - Anjukaselvan **/
    public function getchargecategoryresultApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $search_by = array();
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $cpt_category = $cpt_code_id = $cpt_custom_type_from = $cpt_custom_type_to = $rendering_provider_id =  '';
        $charges_lists = $charges_list = $total_arr = [];
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-',$request['select_transaction_date']);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($date[0]);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($date[1]);
            $search_by['Transaction Date'][]= date("m/d/y",strtotime($date[0])).' to '.date("m/d/y",strtotime($date[1]));
        }
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $date = explode('-',$request['select_date_of_service']);
            $dos_start_date = date("Y-m-d", strtotime($date[0]));
            if($dos_start_date == '1970-01-01'){
                $dos_start_date = '0000-00-00';
            }
            $dos_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['DOS'][]= date("m/d/y",strtotime($dos_start_date)).' to '.date("m/d/y",strtotime($dos_end_date));
        }
        if(!empty($request['cpt_category'])){
            $cpt_category = $request['cpt_category'];
            if($cpt_category != '0'){
                $category_name = ProcedureCategory::where('id',$cpt_category)->where('status','Active')->pluck('procedure_category')->first();
            } else{
                $category_name = 'All';
            }
            $search_by['CPT/HCPCS Category'][] = $category_name;
        }
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            $search_by['CPT Type'][] = 'Custom Range';
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $cpt_custom_type_from = $request['custom_type_from'];
                $cpt_custom_type_to = $request['custom_type_to'];
            }
        }
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            $search_by['CPT Type'][] = 'CPT Code';
            if(!empty($request['cpt_code_id'])){
                $cpt_code_id = $request['cpt_code_id'];
                $search_by['CPT Code'][] = $request['cpt_code_id'];
            }
        }
        if(!empty($request['rendering_provider_id'])){
            $rendering_provider_id = $request['rendering_provider_id'];
            $value_name = [];
            if(!empty($rendering_provider_id)) {
                foreach ((array)$rendering_provider_id as $id) {
                    $value_name[] = App\Models\Provider::getProviderFullName($id);
                }
            }
            $search_render = array_unique($value_name);
            $search_by['Rendering Provider'][] = $search_render;
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
            $sp_return_result = DB::select('call chargeCategory("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $cpt_category . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $rendering_provider_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->charge_count;
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
            $sp_return_result = DB::select('call chargeCategory("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $cpt_category . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $rendering_provider_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;

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

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call chargeCategory("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $cpt_category . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $rendering_provider_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $export_array = $sp_return_result;
        }
        $temp_array = $sp_return_result;
        foreach ($temp_array as $key => $value) {
            $value = (array)$value;
            $provider_id = $value['provider_id'];
            $value['work_rvu'] = $value['work_rvu'];
            $value['payment'] = @$value['payment'] ;
            $total_arr[$provider_id]['last_rec'] = $key;
            $total_arr[$provider_id]['rec_cnt'] = isset($total_arr[$provider_id]['rec_cnt']) ? ($total_arr[$provider_id]['rec_cnt']+1) : 1;
            $total_arr[$provider_id]['units'] = @$total_arr[$provider_id]['units'] + $value['units'];
            $total_arr[$provider_id]['charge'] = @$total_arr[$provider_id]['charge'] + $value['charge'];
            $total_arr[$provider_id]['payment'] = @$total_arr[$provider_id]['payment'] + $value['payment'] ;
            $total_arr[$provider_id]['work_rvu'] = @$total_arr[$provider_id]['work_rvu'] + $value['work_rvu'] ;
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges_list','pagination','pagination_prt','search_by','total_arr','export_array','charges_lists','temp_array')));
    }
    ### CHARGE CATEGORY REPORT END ###

    // AR Workbench report start

    public function getWorkbenchReportApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('workbench');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData', 'search_fields')));
    }

    public function getWorkbenchListApi($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
        $search_by = array();
        $workbench = ProblemList::select("problem_lists.*","claim_info_v1.insurance_category","claim_sub_status.sub_status_desc")
                    ->has('claim')->with(['claim' => function($query) {
                        $query->select('id', 'sub_status_id','patient_id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'billing_provider_id','date_of_service','total_charge', 'status', \DB::raw('DATEDIFF(NOW(), date_of_service) as claim_age_days'));
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name', 'facility_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'claim.billing_provider' ,'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }])

                    ->orderBy('problem_lists.id', 'desc')->where('problem_lists.id', function ($sub) {
                        $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
                    })

                    ->leftjoin('claim_info_v1', 'problem_lists.claim_id', '=', 'claim_info_v1.id')
                    ->leftjoin('claim_sub_status', 'claim_sub_status.id', '=', 'claim_info_v1.sub_status_id')
                    /*
                    ->leftjoin('patient_insurance', function($join) {
                        $join->on('patient_insurance.patient_id', '=', 'claim_info_v1.patient_id');
                        $join->on('patient_insurance.insurance_id', '=', 'claim_info_v1.insurance_id');
                    })*/;

        // date_of_service
        if(!empty($request['date_of_service'])){

            $date = explode('-',@$request['date_of_service']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));

            $workbench->WhereHas('claim', function($q)use($start_date, $end_date) {
                $q->where(DB::raw('DATE(date_of_service)'),'>=',$start_date)->where(DB::raw('DATE(date_of_service)'),'<=',$end_date);
            });
            
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['DOS']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }else{
                $search_by['DOS'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
        }

        // rendering_provider
        if (!empty($request['rendering_provider'])) {
            $renders_id = explode(',', $request['rendering_provider']);
            $workbench->WhereHas('claim', function($q)use($renders_id) {
                $q->WhereIn('rendering_provider_id', $renders_id);
            });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Rendering Provider'] = $search_render;
            }else{
                $search_by['Rendering Provider'][] = $search_render;
            }
        }

        // billing_provider
        if (!empty($request['billing_provider'])) {

            $providers_id = explode(',', $request['billing_provider']);
            $workbench->WhereHas('claim', function($q)use($providers_id) {
                $q->WhereIn('billing_provider_id', $providers_id);
            });
            foreach ($providers_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Billing Provider'] = $search_provider;
            }else{
                $search_by['Billing Provider'][] = $search_provider;
            }
        }

        // facility
        if (isset($request['facility']) && $request['facility'] != '') {
            $facility_id = explode(',', $request['facility']);
            $workbench->WhereHas('claim', function($q)use($facility_id) {
                $q->WhereIn('facility_id', $facility_id);
            });

            $facility_names = Facility::select('facility_name')->whereIn('id', explode(',', $request['facility']))->get();
            foreach ($facility_names as $name) {
                $value_names[] = $name['facility_name'];
            }
            $search_filter = implode(", ", array_unique($value_names));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Facility'] = isset($search_filter) ? $search_filter : [];
            }else{
                $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
            }
        }

        // responsibility
        if(!empty($request['ar_responsibility'])){

            $billed_to =  $request['ar_responsibility'];
            if($billed_to != 'All'){
                if($billed_to == 'Patient') {
                    $workbench->Where('claim_info_v1.insurance_id', 0);
                } else {
                    $workbench->Where('claim_info_v1.insurance_id','<>', 0);
                }
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by["Responsibility"] =  @$billed_to;
            }else{
                $search_by["Responsibility"][] =  @$billed_to;
            }
        }

        // responsibility_category
        if(!empty($request['responsibility_category'])){
            $respCat = $request['responsibility_category'];
            if($respCat == 'Patient') {
                 $workbench->Where('claim_info_v1.insurance_id', 0);
            } else {
                $workbench->Where('claim_info_v1.insurance_category', $respCat);
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Category'] = $request['responsibility_category'];
            }else{
                $search_by['Category'][] = $request['responsibility_category'];
            }
        }

        // claim_age
        if(!empty($request['claim_age'])){
            switch ($request['claim_age']) {
                case "0-30":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));
                    $workbench->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = "0-30 Days";
                    }else{
                        $search_by['Claim Age'][] = "0-30 Days";
                    }                    
                    break;

                case "31-60":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(31)));
                    $workbench->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = "31-60 Days";
                    }else{
                        $search_by['Claim Age'][] = "31-60 Days";
                    }                    
                    break;

                case "61-90":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(61)));
                    $workbench->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = "61-90 Days";
                    }else{
                        $search_by['Claim Age'][] = "61-90 Days";
                    }                    
                    break;

                case "91-120":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(91)));
                    $workbench->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = "91-120 Days";
                    }else{
                        $search_by['Claim Age'][] = "91-120 Days";
                    }                    
                    break;

                case "121-150":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(121)));
                    $workbench->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = "121-150 Days";
                    }else{
                        $search_by['Claim Age'][] = "121-150 Days";
                    }                    
                    break;

                case "150-above":
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(151)));
                    $workbench->WhereHas('claim', function($q)use($current_month) {
                        $q->where('claim_submit_count', '=', 0)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Claim Age'] = ">150 Days";
                    }else{
                        $search_by['Claim Age'][] = ">150 Days";
                    }
                    break;
            }
        }

        // claim_status
        if(!empty($request['claim_status'])){
            $status = explode(',', $request['claim_status']);
            if($request['claim_status'] != 'All') {
                $workbench->whereIn('claim_info_v1.status', @$status);
            }
            $search_claimstatus = implode(", ", array_unique($status));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Claim Status'] = $search_claimstatus;
            }else{
                $search_by['Claim Status'][] = $search_claimstatus;
            }
        }

        // workbench_status
        if(!empty($request['workbench_status'])){
            $wbstatus = explode(',', $request['workbench_status']);
            if($request['workbench_status'] != 'All') {
                $workbench->whereIn('problem_lists.status', @$wbstatus);
            }
            $search_wbstatus = implode(", ", array_unique($wbstatus));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Workbench Status'] = $search_wbstatus;
            }else{
                $search_by['Workbench Status'][] = $search_wbstatus;
            }            
        }

        // followup_date
        if(!empty($request['followup_date'])) {

            $date = explode('-',$request['followup_date']);
            $fstart_date = date("Y-m-d", strtotime($date[0]));
            if($fstart_date == '1970-01-01'){
                $fstart_date = '0000-00-00';
            }
            $fend_date = date("Y-m-d", strtotime($date[1]));
            $workbench->where(DB::raw('DATE(fllowup_date)'),'>=',$fstart_date)->where(DB::raw('DATE(fllowup_date)'),'<=',$fend_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Followup Date']= date("m/d/y",strtotime($fstart_date)).' to '.date("m/d/y",strtotime($fend_date));
            }else{
                $search_by['Followup Date'][]= date("m/d/y",strtotime($fstart_date)).' to '.date("m/d/y",strtotime($fend_date));
            }            
        }


        // assigned_to
        if(!empty($request['assigned_to'])){
            $assigned_tos = explode(',', $request['assigned_to']);
            $workbench->whereIn('assign_user_id', @$assigned_tos);

            $User_name =  Users::whereIn('id', $assigned_tos)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Assigned To'] = $User_name;
            }else{
                $search_by['Assigned To'][] = $User_name;
            }
        }

        if ($export == "") {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $workbench = $workbench->paginate($paginate_count);
            // Get export result

            $ref_array = $workbench->toArray();
            $pagination_prt = $workbench->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $workbench = json_decode($workbench->toJson());
            $workbench_list = $workbench->data;
        } else {
            $workbench_list = $workbench->get()->toArray();
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('workbench_list','pagination','pagination_prt','search_by')));
    }
    
    // Stored procedure for AR Work Bench - Anjukaselvan
    public function getWorkbenchListApiSP($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
        $search_by = array();
        
        $dos_start_date = $dos_end_date = $rendering_provider_id = $billing_provider_id = $facility_id = $responsibility = $resp_category = 
        $claim_age = $claim_status = $workbench_status = $followup_start_date = $followup_end_date = $assigned_to = '';
        
        // date_of_service
        if(!empty($request['date_of_service'])){
            $date = explode('-',@$request['date_of_service']);
            $dos_start_date = date("Y-m-d", strtotime($date[0]));
            if($dos_start_date == '1970-01-01'){
                $dos_start_date = '0000-00-00';
            }
            $dos_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['DOS'][]= date("m/d/y",strtotime($dos_start_date)).' to '.date("m/d/y",strtotime($dos_end_date));
        }

        // rendering_provider
        if (!empty($request['rendering_provider'])) {
            $rendering_provider_id = $request['rendering_provider'];
            $renders_id = explode(',', $request['rendering_provider']);
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }

        // billing_provider
        if (!empty($request['billing_provider'])) {
            $billing_provider_id = $request['billing_provider'];
            $providers_id = explode(',', $request['billing_provider']);
            foreach ($providers_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            $search_by['Billing Provider'][] = $search_provider;
        }

        // facility
        if (isset($request['facility']) && $request['facility'] != '') {
            $facility_id = $request['facility'];
            $facility_names = Facility::select('facility_name')->whereIn('id', explode(',', $request['facility']))->get();
            foreach ($facility_names as $name) {
                $value_names[] = $name['facility_name'];
            }
            $search_filter = implode(", ", array_unique($value_names));
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }

        // responsibility
        if(!empty($request['ar_responsibility'])){
            $responsibility = $request['ar_responsibility'];
            $search_by["Responsibility"][] =  @$responsibility;
        }
        
        // responsibility_category
        if(!empty($request['responsibility_category'])){
            $resp_category = $request['responsibility_category'];
            $search_by['Category'][] = $request['responsibility_category'];
        }

        // claim_age
        if(!empty($request['claim_age'])){
            $claim_age = $request['claim_age'];
            switch ($request['claim_age']) {
                case "0-30":                    
                    $search_by['Claim Age'][] = "0-30 Days";
                    break;

                case "31-60":
                    $search_by['Claim Age'][] = "31-60 Days";
                    break;

                case "61-90":
                    $search_by['Claim Age'][] = "61-90 Days";
                    break;

                case "91-120":
                    $search_by['Claim Age'][] = "91-120 Days";
                    break;

                case "121-150":
                    $search_by['Claim Age'][] = "121-150 Days";
                    break;

                case "150-above":
                    $search_by['Claim Age'][] = ">150 Days";
                    break;
            }
        }

        // claim_status
        if(!empty($request['claim_status'])){
            $claim_status = $request['claim_status'];
            $status = explode(',', $request['claim_status']);
            $search_claimstatus = implode(", ", array_unique($status));
            $search_by['Claim Status'][] = $search_claimstatus;
        }

        // workbench_status
        if(!empty($request['workbench_status'])){
            $workbench_status = $request['workbench_status'];
            $wbstatus = explode(',', $request['workbench_status']);
            $search_wbstatus = implode(", ", array_unique($wbstatus));
            $search_by['Workbench Status'][] = $search_wbstatus;
        }

        // followup_date
        if(!empty($request['followup_date'])) {
            $date = explode('-',$request['followup_date']);
            $followup_start_date = date("Y-m-d", strtotime($date[0]));
            if($followup_start_date == '1970-01-01'){
                $followup_start_date = '0000-00-00';
            }
            $followup_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Followup Date'][]= date("m/d/y",strtotime($followup_start_date)).' to '.date("m/d/y",strtotime($followup_end_date));
        }
        
        // assigned_to
        if(!empty($request['assigned_to'])){
            $assigned_to = $request['assigned_to'];
            $assigned_tos = explode(',', $request['assigned_to']);
            $User_name =  Users::whereIn('id', $assigned_tos)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['Assigned To'][] = $User_name;
        }
        //pagination
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
            $workbench_count = DB::select('call arWorkBench("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $billing_provider_id . '", "' . $facility_id . '", "' . $responsibility . '", "' . $resp_category . '", "' . $claim_age . '", "' . $claim_status . '", "' . $workbench_status . '", "' . $followup_start_date . '", "' . $followup_end_date . '", "' . $assigned_to . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $count = $workbench_count[0]->workbench_count;
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
            $workbench = DB::select('call arWorkBench("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $billing_provider_id . '", "' . $facility_id . '", "' . $responsibility . '", "' . $resp_category . '", "' . $claim_age . '", "' . $claim_status . '", "' . $workbench_status . '", "' . $followup_start_date . '", "' . $followup_end_date . '", "' . $assigned_to . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }
            $report_array = $this->paginate($workbench)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);            
            
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $workbench = DB::select('call arWorkBench("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '", "' . $billing_provider_id . '", "' . $facility_id . '", "' . $responsibility . '", "' . $resp_category . '", "' . $claim_age . '", "' . $claim_status . '", "' . $workbench_status . '", "' . $followup_start_date . '", "' . $followup_end_date . '", "' . $assigned_to . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
        }
        $workbench_list = $workbench;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('workbench_list','pagination','pagination_prt','search_by')));
    }

    // AR Workbench report end


    // Denial trend analysis report start

    public function getDenialAnalysisReportApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('denial_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData', 'search_fields')));
    }

    public function getDenialAnalysisListApi($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
        $search_by = array();
        //dd($request);
        /*
        $denial_cpts = PMTClaimCPTTXV1::select("pmt_claim_cpt_tx_v1.*", \DB::raw("MAX(pmt_claim_cpt_tx_v1.id) as max_id"),
                            \DB::raw('pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held) as total_ar_due'),
                            \DB::raw("(SELECT denial_code as rec_denial FROM pmt_claim_cpt_tx_v1 as rTx
                                WHERE rTx.claim_id = pmt_claim_cpt_tx_v1.claim_id
                          AND rTx.claim_cpt_info_id = pmt_claim_cpt_tx_v1.claim_cpt_info_id order by id DESC limit 1
                            ) as rec_denial"))

                            ->with(['claim' => function($query) {
                                $query->select('id', 'patient_id', 'claim_number', 'insurance_id', 'date_of_service','total_charge', 'insurance_category', 'status', \DB::raw('DATEDIFF(NOW(), created_at) as claim_age_days'));
                            }, 'claim.insurance_details' => function($query) {
                                $query->select('id', 'short_name');
                            }, 'lastWorkbench' ,'claimcpt', 'payment_info' ])

                        ->join('pmt_claim_cpt_tx_v1 as last_txn', function($join){
                            $join->on('last_txn.claim_id', '=', 'pmt_claim_cpt_tx_v1.claim_id');
                            $join->on('last_txn.claim_cpt_info_id', '=', 'pmt_claim_cpt_tx_v1.claim_cpt_info_id');
                            $join->where('last_txn.id','=', \DB::raw("MAX(pmt_claim_cpt_tx_v1.id) as max_id"));
                        })

                        ->leftjoin('claim_info_v1', 'pmt_claim_cpt_tx_v1.claim_id', '=', 'claim_info_v1.id')

                        ->leftjoin('pmt_claim_cpt_fin_v1', function($join){
                            $join->on('pmt_claim_cpt_fin_v1.claim_id', '=', 'pmt_claim_cpt_tx_v1.claim_id');
                            $join->on('pmt_claim_cpt_fin_v1.claim_cpt_info_id', '=', 'pmt_claim_cpt_tx_v1.claim_cpt_info_id');
                        })

                        ->leftjoin('patient_insurance', function($join) {
                            $join->on('patient_insurance.patient_id', '=', 'claim_info_v1.patient_id');
                            $join->on('patient_insurance.insurance_id', '=', 'claim_info_v1.insurance_id');
                        })

                        //->where('denial_code', '<>', '')
                        ->where('claim_info_v1.status', '=', 'Denied')
                        ->where('claim_info_v1.insurance_id', '<>', 0)
                        ->orderBy('pmt_claim_cpt_tx_v1.id', 'desc')
                        ->groupby('pmt_claim_cpt_tx_v1.claim_id', 'pmt_claim_cpt_tx_v1.claim_cpt_info_id');
        */

        $denial_cpts = PMTClaimCPTFINV1::select('pmt_claim_cpt_fin_v1.claim_id', 'pmt_claim_cpt_fin_v1.claim_cpt_info_id',
                        'claim_sub_status.sub_status_desc',
                        \DB::raw('MAX(pmt_claim_tx_v1.id) as last_txn_id'),
                       // 'pmt_claim_cpt_tx_v1.denial_code', 'pmt_claim_cpt_tx_v1.pmt_claim_tx_id', //'pmt_claim_cpt_tx_v1.claim_cpt_info_id',
                        \DB::raw('pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held) as total_ar_due'))

                        ->leftjoin('claim_info_v1', 'pmt_claim_cpt_fin_v1.claim_id', '=', 'claim_info_v1.id')

                        ->leftjoin('pmt_claim_tx_v1', function($join) {
                            $join->on('pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id');
                        })
                        ->leftjoin('claim_sub_status', 'claim_sub_status.id', '=', 'claim_info_v1.sub_status_id')
                        
                        ->with([ 'recClaimTxn',
                                'lastcptdenialdesc',
                                'recentCptTxn',
                                'claim' => function($query) {
                                        $query->select('id', 'patient_id', 'claim_number', 'insurance_id', 'rendering_provider_id', 'facility_id', 'date_of_service','total_charge', 'insurance_category', 'status', \DB::raw('DATEDIFF(NOW(), date_of_service) as claim_age_days'));
                                    }, 
                                'claim.insurance_details' => function($query) {
                                    $query->select('id', 'short_name');
                                },
                                'claim.rend_providers' =>function($query){
                                    $query->select('id', 'provider_name', 'short_name as provider_short');
                                },
                                'claim.facility' =>function($query){
                                    $query->select('id', 'facility_name', 'short_name as facility_short');
                                },
                                'lastWorkbench', 'claimcpt'
                            ])

                        ->where('claim_info_v1.status', '=', 'Denied')
                        // ->where('claim_info_v1.insurance_id', '<>', 0)
                        ->where('pmt_claim_tx_v1.pmt_method', '=', 'Insurance')
                        ->groupby('pmt_claim_tx_v1.claim_id', 'pmt_claim_cpt_fin_v1.claim_id', 'pmt_claim_cpt_fin_v1.claim_cpt_info_id')
                        ->orderBy('pmt_claim_tx_v1.id', 'desc')//->toSql()
                        ;//dd($denial_cpts);

        /*
        $denial_cpts->leftjoin('pmt_info_v1', 'pmt_claim_cpt_tx_v1.payment_id', '=','pmt_info_v1.id');

        $denial_cpts->leftjoin('pmt_check_info_v1', function($join) {
            $join->on('pmt_check_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Check'"));
        });

        $denial_cpts->leftjoin('pmt_eft_info_v1', function($join) {
            $join->on('pmt_eft_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'EFT'"));
        });

        $denial_cpts->leftjoin('pmt_card_info_v1', function($join) {
            $join->on('pmt_card_info_v1.id', '=', 'pmt_info_v1.pmt_mode_id');
            $join->on('pmt_info_v1.pmt_mode', '=', DB::raw("'Credit'"));
        });
        */

        // date_of_service
        if(!empty($request['date_of_service'])){

            $date = explode('-',@$request['date_of_service']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));

            $denial_cpts->WhereHas('claim', function($q)use($start_date, $end_date) {
                $q->where(DB::raw('DATE(date_of_service)'),'>=',$start_date)->where(DB::raw('DATE(date_of_service)'),'<=',$end_date);
            });
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['DOS']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            else{
                $search_by['DOS'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            
        }

        // Responsibility
        if(!empty($request['responsibility'])){

            $billed_to =  $request['responsibility'];
            if($billed_to != 'All' && $billed_to != ''){
                $denial_cpts->WhereHas('lastcptdenialdesc', function($q)use($billed_to) {
                    $q->WhereIn('responsibility', explode(',', $billed_to));
                });
            }
            // Get Selected Insurance Names
            $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',',$billed_to))->get()->toArray();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Responsibility'] = @array_flatten($insurance)[0];;    
            }
            else{
                $search_by['Responsibility'][] = @array_flatten($insurance)[0];;    
            }
            
        }
        
        // rendering_provider
        if (!empty($request['rendering_provider'])) {
            $renders_id = explode(',', $request['rendering_provider']);
            $denial_cpts->WhereHas('claim', function($q)use($renders_id) {
                $q->WhereIn('rendering_provider_id', $renders_id);
            });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }
        
        // facility
        if (isset($request['facility']) && $request['facility'] != '') {
            $facility_id = explode(',', $request['facility']);
            $denial_cpts->WhereHas('claim', function($q)use($facility_id) {
                $q->WhereIn('facility_id', $facility_id);
            });

            $facility_names = Facility::select('facility_name')->whereIn('id', explode(',', $request['facility']))->get();
            foreach ($facility_names as $name) {
                $value_names[] = $name['facility_name'];
            }
            $search_filter = implode(", ", array_unique($value_names));
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['CPT Type'] = 'Custom Range';    
            }
            else{
                $search_by['CPT Type'][] = 'Custom Range';    
            }
            
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $custom_type_from = $request['custom_type_from'];
                $custom_type_to = $request['custom_type_to'];
                $denial_cpts->WhereHas('claimcpt', function($q)use($custom_type_from, $custom_type_to) {
                    $q->where('cpt_code','>=',$custom_type_from)->where('cpt_code','<=',$custom_type_to);
                });
            }
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['CPT Type'] = 'CPT Code';   
            }
            else{
             $search_by['CPT Type'][] = 'CPT Code';
            }
            if(!empty($request['cpt_code_id'])){
                $cpt_code_id = $request['cpt_code_id'];
                $denial_cpts->WhereHas('claimcpt', function($q)use($cpt_code_id) {
                    // Comma separated search option added for CPT code.
                    //$q->where('cpt_code','=',$cpt_code_id);
                    $cptCodes = array_map('trim', explode(',', $cpt_code_id));
                    $q->Where(function ($qry) use ($cptCodes) {
                        foreach ($cptCodes as $key => $dc) {
                            if($key == 0)
                                $qry->where('cpt_code', $dc);
                           else
                                $qry->orWhere('cpt_code', $dc);
                        }
                    });

                });
                if (isset($request['exports']) && $request['exports'] == 'pdf') {
                    $search_by['CPT Code'] = $cpt_code_id;    
                }
                else{
                    $search_by['CPT Code'][] = $cpt_code_id;
                }
            }
        }

        // Denied Date
        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            
            // Handle the condition for payment denied date
            $denial_cpts->Where(function ($q) use ($start_date, $end_date) {                

                /*
                $q->WhereHas('lastcptdenialdesc.pmtinfo.checkDetails', function($paymentdetail) use ($start_date, $end_date) {
                    $paymentdetail->where(DB::raw('DATE(check_date)'), '>=', $start_date)->where(DB::raw('DATE(check_date)'), '<=', $end_date);
                });

                $q->orWhereHas('lastcptdenialdesc.pmtinfo.eftDetails', function($paymentdetail) use ($start_date, $end_date) {
                    $paymentdetail->where(DB::raw('DATE(eft_date)'), '>=', $start_date)->where(DB::raw('DATE(eft_date)'), '<=', $end_date);
                });
                */
                /*
                $q->WhereHas('recentCptTxn.payment_info.checkDetails', function($paymentdetail) use ($start_date, $end_date) {
                    $paymentdetail->where(DB::raw('DATE(check_date)'), '>=', $start_date)->where(DB::raw('DATE(check_date)'), '<=', $end_date);
                });

                $q->orWhereHas('recentCptTxn.payment_info.eftDetails', function($paymentdetail) use ($start_date, $end_date) {
                    $paymentdetail->where(DB::raw('DATE(eft_date)'), '>=', $start_date)->where(DB::raw('DATE(eft_date)'), '<=', $end_date);
                });
                */
            });
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Denied Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            else{
                $search_by['Denied Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
        }


        // denial_code
        if(!empty($request['denial_code'])){
            $denailCodes = array_filter(array_map('trim', explode(',', $request['denial_code'])));

            /*
            //$denial_cpts->Where(function ($q) use ($denailCodes) {
            $denial_cpts->WhereHas('recentCptTxn', function ($qry) use ($denailCodes) {
                $qry->Where(function ($q) use ($denailCodes) {
                    foreach ($denailCodes as $key => $dc) {
                        if($key == 0)
                            $q->where('denial_code','like','%' .$dc.'%');
                       else
                            $q->orWhere('denial_code','like','%' .$dc.'%');
                    }
                });
            });
            */

            /*
            $denial_cpts->WhereHas('lastcptdenialdesc', function ($qry) use ($denailCodes) {
                $qry->Where(function ($q) use ($denailCodes) {
                    foreach ($denailCodes as $key => $dc) {
                        if($key == 0)
                            $q->where('value_1','like','%' .$dc.'%');
                       else
                            $q->orWhere('value_1','like','%' .$dc.'%');
                    }
                });
            });
            */
            /*
            $denial_cpts->WhereHas('lastcptdenialdesc.claimcpt_txn', function ($qry) use ($denailCodes) {
                $qry->Where(function ($q) use ($denailCodes) {
                    foreach ($denailCodes as $key => $dc) {
                        if($key == 0)
                            $q->where('denial_code','like','%' .$dc.'%');
                       else
                            $q->orWhere('denial_code','like','%' .$dc.'%');
                    }
                });
            });
            */
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Denial Code'] = $request['denial_code'];    
            }
            else{
                $search_by['Denial Code'][] = $request['denial_code'];    
            }
            
        }

        // claim_age
        if(!empty($request['claim_age']) && $request['claim_age'] != 'All'){
            switch ($request['claim_age']) {
                case "0-30":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));
                    $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = "0-30 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = "0-30 Days";
                    }
                    break;

                case "31-60":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(31)));
                    $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = "31-60 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = "31-60 Days";
                    }
                    break;

                case "61-90":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(61)));
                    $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = "61-90 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = "61-90 Days";
                    }
                    break;

                case "91-120":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(91)));
                    $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = "91-120 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = "91-120 Days";
                    }
                    break;

                case "121-150":
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(121)));
                    $denial_cpts->WhereHas('claim', function($q)use($last_month_carbon, $current_month) {
                        $q->where('date_of_service', '>=', $last_month_carbon)->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = "121-150 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = "121-150 Days";
                    }
                    break;

                case "150-above":
                    $current_month = date('Y-m-d', strtotime(Carbon::now()->subDay(151)));
                    $denial_cpts->WhereHas('claim', function($q)use($current_month) {
                        $q->where('date_of_service', '<=', $current_month);
                    });
                    if (isset($request['exports']) && $request['exports'] == 'pdf'){
                        $search_by['Claim Age'] = ">150 Days";
                    }
                    else{
                        $search_by['Claim Age'][] = ">150 Days";
                    }
                    break;
            }
        }

        // Workbench status
        if(!empty($request['workbench_status'])) {
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Workbench Status'] = $request['workbench_status'];
            }
            else{
                $search_by['Workbench Status'][] = $request['workbench_status'];
            }
        }

        // Workbench status
        if(!empty($request['exclude_zero_ar'])) {
            if($request['exclude_zero_ar'] == 'Exclude') {
                $exclude_zero_ar = $request['exclude_zero_ar'];
                $denial_cpts->where(DB::raw('pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held)'), '<>', 0);
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['$0 Line Item'] = "Remove $0 Line Item";
                }
                else{
                    $search_by['$0 Line Item'][] = "Remove $0 Line Item";
                }
            } else {
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['$0 Line Item'] = "Contains $0 Line Item";
                }
                else{
                    $search_by['$0 Line Item'][] = "Contains $0 Line Item";   
                }
            }
        }

        $workbench_status = isset($request['workbench_status']) ?$request['workbench_status'] : 'Include';

        $txt_list = $denial_cpts->get();
        $collection = collect($txt_list);
        $result = [];

        foreach ($collection as $key => $item) {
            $code_check = $date_check = $ins_check = 1; 
            //$denial_details[$item->claim_cpt_info_id] = PMTClaimCPTFINV1::getClaimCptLastInsuranceDenials($item->last_txn_id, $item->claim_cpt_info_id);

            // denial_code search
            if(!empty($request['denial_code'])){
                $denailCodes = array_filter(array_map('trim', explode(',', @$request['denial_code'])));
                $txn_denials = array_filter(array_map('trim', explode(',', @$item->lastcptdenialdesc->claimcpt_txn->denial_code)));

                $chkResp = array_intersect($txn_denials,$denailCodes);

                // Check if match found then add into result else ignore it.
                 $code_check = (!empty($chkResp)) ? 1 : 0;
            }

            $denied_date = '';
            if(isset($item->lastcptdenialdesc->pmtinfo)) {
                if($item->lastcptdenialdesc->pmtinfo->pmt_mode == 'EFT')
                    $denied_date = $item->lastcptdenialdesc->pmtinfo->eftDetails->eft_date;
                elseif($item->lastcptdenialdesc->pmtinfo->pmt_mode == 'Credit')
                    $denied_date = $item->lastcptdenialdesc->pmtinfo->creditCardDetails->expiry_date ;
                else
                    $denied_date = $item->lastcptdenialdesc->pmtinfo->checkDetails->check_date ;
            }
            if(!empty($request['created_at'])){
                $date = explode('-',$request['created_at']);
                $start_date = date("Y-m-d", strtotime($date[0]));
                if($start_date == '1970-01-01'){
                    $start_date = '0000-00-00';
                }
                $end_date = date("Y-m-d", strtotime($date[1]));
                $date_check = ( ( $denied_date >= $start_date ) && ( $denied_date <= $end_date ) ) ? 1 : 0;
            }
            // Report: Add "Payer" Filter in Denial Trend Analysis Report
            // Rev.1 Ref: MR-2847 - Ravi - 21-09-2019
            $denialIns = '';
            if(isset($item->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id)) {
                $denialIns = $item->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id;
            }    
              
            if(!empty($request['responsibility'])) {                                     
                $billed_to =  $request['responsibility'];
                if($billed_to != 'All' && $billed_to != ''){
                    $ins_check = (in_array($denialIns, explode(',', $billed_to))) ? 1 :0 ;
                }
            }
            
            if($code_check && $date_check && $ins_check) {
                $item->denied_date = $denied_date;
                $result[] = $item;
            }
        }

        $denial_cpt_list = $result;
        $export_array = $result;
        $temp = [];
        if ($export == "") {
            $report_array = $this->paginate($denial_cpt_list)->toArray();
            $pagination_prt = $this->paginate($denial_cpt_list)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $temp = array_chunk($denial_cpt_list, 25, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($denial_cpt_list)) {
            $start = isset($request['page']) ? $request['page'] - 1 : 0;
            $denial_cpt_list = $temp[$start];
        }
        //dd($denial_cpt_list);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('denial_cpt_list','pagination','pagination_prt','search_by','workbench_status','export_array')));

        /*

         $txt_list = $txt_list->get();
        //dd($refundTxn);
        $collection = collect($txt_list);
        $collections = collect($fin_list);
        foreach ($collections as $key => $item) {
            $dateStr = date("m/d/Y", strtotime($key));
            $result[$dateStr]['total_charge'] = $item;
        }

        foreach ($collection as $item) {
            $dateStr = date("m/d/Y", strtotime($item->trx_date));
            $result[$dateStr]['created_at'] = $dateStr;
            $result[$dateStr]['insurance_adjustment'] = @$item->insurance_adjustment;
            $result[$dateStr]['patient_adjustment'] = @$item->patient_adjustment;
            $result[$dateStr]['insurance_payment'] = @$item->insurance_payment;
            $pat_pmt = (isset($paymentTxn
                            [$item->trx_date]) ? $paymentTxn[$item->trx_date] : 0 );
            $result[$dateStr]['patient_payment'] = $pat_pmt;
            $pat_refund = (isset($refundTxn[$item->trx_date]) ? $refundTxn[$item->trx_date] : 0 );
            $result[$dateStr]['patient_refund'] = $pat_refund;
            $result[$dateStr]['insurance_refund'] = (isset($item->insurance_refund) && @$item->insurance_refund < 0 ) ? (-1 * @$item->insurance_refund) : @$item->insurance_refund;
            ;
            $result[$dateStr]['writeoff_total'] = @$item->writeoff_total;
            $total_payment_amt = (@$item->insurance_payment + @$pat_pmt) - ($pat_refund + (isset($item->insurance_refund) && @$item->insurance_refund < 0 ) ? (-1 * @$item->insurance_refund) : @$item->insurance_refund);
            $result[$dateStr]['total_payment'] = App\Http\Helpers\Helpers::priceFormat($total_payment_amt); //@$item->total_payment;
        }
        $export_array = $result;
        $temp = [];
        if ($export == "") {
            $report_array = $this->paginate($result)->toArray();
            $pagination_prt = $this->paginate($result)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
        }
        $temp = array_chunk($result, 25, true);

        $filter_result = "all";
        $start = 0;
        if (!empty($result)) {
            $start = isset($request['page']) ? $request['page'] - 1 : 0;
            $result = $temp[$start];
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'result', 'fin_list', 'daterange', 'search_by', 'export_array')));
        */



        /****

        if ($export == "") {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $denial_cpts = $denial_cpts->paginate($paginate_count);
            // Get export result

            $ref_array = $denial_cpts->toArray();
            $pagination_prt = $denial_cpts->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $denial_cpts = json_decode($denial_cpts->toJson());
            $denial_cpt_list = $denial_cpts->data;
           // dd($denial_cpt_list);
        } else {
            $denial_cpt_list = $denial_cpts->get()->toArray();
        }

        $denial_details = [];
        foreach ($denial_cpt_list as $key => $value) {
            //echo "<pre>"; print_r($value);
            //dd($value->last_txn_id);
            $denial_resp = PMTClaimCPTFINV1::getClaimCptLastInsuranceDenials($value->last_txn_id, $value->claim_cpt_info_id);
           // echo "<pre>"; print_r($denial_resp);
            $denial_details[$value->claim_cpt_info_id] = $denial_resp;
        }
        //dd($denial_cpt_list);        \Log::info($denial_cpt_list);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('denial_cpt_list','pagination','pagination_prt','search_by','workbench_status', 'denial_details')));
        */
    }
    
    //Stored Procedure for Denial Trend Analysis report - Anjukaselvan
    public function getDenialAnalysisListApiSP($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
        $search_by = array();
        
        $dos_start_date = $dos_end_date = $responsibility = $rendering_provider = $facility = $cpt_code_id = $cpt_custom_type_from = $cpt_custom_type_to = $denial_start_date = $denial_end_date = $denial_code = $claim_age = $line_item = '';
        // date_of_service
        if(!empty($request['date_of_service'])){
            $date = explode('-',@$request['date_of_service']);
            $dos_start_date = date("Y-m-d", strtotime($date[0]));
            if($dos_start_date == '1970-01-01'){
                $dos_start_date = '0000-00-00';
            }
            $dos_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['DOS'][]= date("m/d/y",strtotime($dos_start_date)).' to '.date("m/d/y",strtotime($dos_end_date));
        }
        
        // Responsibility
        if(!empty($request['responsibility'])){
            $billed_to =  $request['responsibility'];
            if($billed_to != 'All' && $billed_to != ''){
                $responsibility =  $request['responsibility'];
            }
            // Get Selected Insurance Names
            $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',',$billed_to))->get()->toArray();
            $search_by['Responsibility'][] = @array_flatten($insurance)[0];
        }
        if (!empty($request['rendering_provider'])) {
            $rendering_provider = $request['rendering_provider'];
            $renders_id = explode(',', $request['rendering_provider']);           
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }
        if (isset($request['facility']) && $request['facility'] != '') {
            $facility = $request['facility'];
            $facility_id = explode(',', $request['facility']);
            $facility_names = Facility::select('facility_name')->whereIn('id', explode(',', $request['facility']))->get();
            foreach ($facility_names as $name) {
                $value_names[] = $name['facility_name'];
            }
            $search_filter = implode(", ", array_unique($value_names));
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            $search_by['CPT Type'][] = 'Custom Range';
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $cpt_custom_type_from = $request['custom_type_from'];
                $cpt_custom_type_to = $request['custom_type_to'];
            }
        }

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
             $search_by['CPT Type'][] = 'CPT Code';
            if(!empty($request['cpt_code_id'])){
                $cpt_code_id = $request['cpt_code_id'];                
                $search_by['CPT Code'][] = $cpt_code_id;
            }
        }
        
        // Denied Date
        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);
            $denial_start_date = date("Y-m-d", strtotime($date[0]));
            if($denial_start_date == '1970-01-01'){
                $denial_start_date = '0000-00-00';
            }
            $denial_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Denied Date'][]= date("m/d/y",strtotime($denial_start_date)).' to '.date("m/d/y",strtotime($denial_end_date));
        }
        
        // denial_code
        if(!empty($request['denial_code'])){            
            //Search Based On Regular Expressions
            $denial_code = rtrim(implode('|', array_unique(array_filter(explode(',', $request['denial_code'] )))), ',');
            //$denial_code = "(^|,)(".$denial_code.")(,|$)";            
            $search_by['Denial Code'][] = $request['denial_code'];
        }

        // claim_age
        if(!empty($request['claim_age']) && $request['claim_age'] != 'All'){
            $claim_age = $request['claim_age'];
            switch ($request['claim_age']) {
                case "0-30":
                    $search_by['Claim Age'][] = "0-30 Days";
                    break;

                case "31-60":
                    $search_by['Claim Age'][] = "31-60 Days";
                    break;

                case "61-90":
                    $search_by['Claim Age'][] = "61-90 Days";
                    break;

                case "91-120":
                    $search_by['Claim Age'][] = "91-120 Days";
                    break;

                case "121-150":
                    $search_by['Claim Age'][] = "121-150 Days";
                    break;

                case "150-above":
                    $search_by['Claim Age'][] = ">150 Days";
                    break;
            }
        }

        // Workbench status
        if(!empty($request['workbench_status'])) {
            $search_by['Workbench Status'][] = $request['workbench_status'];
        }

        // Workbench status
        if(!empty($request['exclude_zero_ar'])) {
            if($request['exclude_zero_ar'] == 'Exclude') {
                $line_item = $request['exclude_zero_ar'];
                $search_by['$0 Line Item'][] = "Remove $0 Line Item";
            } else {
                $search_by['$0 Line Item'][] = "Contains $0 Line Item";
            }
        }
        
        $workbench_status = isset($request['workbench_status']) ?$request['workbench_status'] : 'Include';
        
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
            $sp_return_result = DB::select('call arDenialTrendAnalysis("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $responsibility . '", "' . $rendering_provider . '", "' . $facility . '", "' . $cpt_code_id . '", "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $denial_start_date . '", "' . $denial_end_date . '", "' . $denial_code . '", "' . $claim_age . '", "' . $line_item . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->arDenial_count;
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
            $sp_return_result = DB::select('call arDenialTrendAnalysis("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $responsibility . '", "' . $rendering_provider . '", "' . $facility . '", "' . $cpt_code_id . '", "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $denial_start_date . '", "' . $denial_end_date . '", "' . $denial_code . '", "' . $claim_age . '", "' . $line_item . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;

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

        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call arDenialTrendAnalysis("' . $dos_start_date . '", "' . $dos_end_date . '", "' . $responsibility . '", "' . $rendering_provider . '", "' . $facility . '", "' . $cpt_code_id . '", "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $denial_start_date . '", "' . $denial_end_date . '", "' . $denial_code . '", "' . $claim_age . '", "' . $line_item . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $export_array = $sp_return_result;
        $denial_cpt_list = $sp_return_result;
        
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('denial_cpt_list','pagination','pagination_prt','search_by','workbench_status','export_array')));
    }
    // Denial trend analysis report end
}
