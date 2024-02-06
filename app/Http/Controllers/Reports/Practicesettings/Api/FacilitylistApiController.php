<?php

namespace App\Http\Controllers\Reports\Practicesettings\Api;

use App;
use Request;
use Config;
use Response;
use Input;
use DB;
use Session;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Insurancetype as Insurancetype;
use App\Models\Patients\Payment as Payment;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Controller;
use App\Models\AdjustmentReason as AdjustmentReason;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Users as User;
use App\Models\Pos as Pos;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimFinV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use Illuminate\Pagination\LengthAwarePaginator;


class FacilitylistApiController extends Controller {

    public function getFacilitylistApi() {
        $pos_list = Pos::select('code', 'id', 'pos')->get();
        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('facility_list');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('pos_list', 'cliam_date_details','search_fields')));
    }

    public function getFilterResultApi($export = '') {
        $request = Request::All();
        $practiceopt = "provider_list";
        /*$practiceopt = @$request['practiceoption'];
        if ($request['hidden_from_date'] == '')
            $start_date = @$request['from_date'];
        else
            $start_date = @$request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = @$request['to_date'];
        else
            $end_date = @$request['hidden_to_date'];
        $pos_id = @$request['pos_code'];
        // To Provider
        //$get_facilities = Facility::with('pos_details','user')->get();
        if ($practiceopt == "provider_list") {
            $filter_group_fac_lists = Facility::with(['facility_user_details', 'pos_details' => function($query) {
                            $query->select('*');
                        }])->select('pos_id', 'facility_name', 'created_by', 'created_at');
        } else {
            if ($pos_id == 'all') {
                $filter_group_fac_lists = Facility::with(['claimInfo', 'pos_details', 'claim_unit' => function($q) {
                                $q->selectRaw('sum(unit) as unit')->groupBy('facility_id');
                            }])->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
            } else {
                $filter_group_fac_lists = Facility::whereHas('pos_details', function($query) use ($pos_id) {
                            $query->where('id', $pos_id);
                        })->with(['claimInfo', 'pos_details', 'claim_unit' => function($q) {
                                $q->selectRaw('sum(unit) as unit')->groupBy('facility_id');
                            }])->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
            }
            
        }*/
        $search_by = array();
        $filter_group_fac_lists = Facility::with(['facility_user_details', 'pos_details' => function($query) {
                            $query->select('*');
                        }])->select('pos_id', 'facility_name', 'created_by', 'created_at');
        if (isset($request['transaction_date']) && $request['transaction_date'] != '') {
            $exp = explode("-",$request['transaction_date']);
            $start_date = str_replace('"', '', $exp[0]);
            $end_date = str_replace('"', '', $exp[1]);
            $filter_group_fac_lists = $filter_group_fac_lists->whereHas('claimInfo', function($query) use ($start_date,$end_date) {
                            $query->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                        });
            $search_by['Transaction Date'][] = $start_date.' to '.$end_date;
        } else{
            $start_date = "";
            $end_date = "";
        }
        if (!empty($request['user'])) {
            $filter_group_fac_lists = $filter_group_fac_lists->whereIn('created_by', $request['user']);                      
            $User_name =  User::whereIn('id', $request["user"])->where('status', 'Active')->pluck('name', 'id')->all();          
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }
     
        if ($export == "") {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_group_fac_list = $filter_group_fac_lists->paginate($paginate_count);
            $claim_array = $filter_group_fac_list->toArray();
            $pagination_prt = $filter_group_fac_list->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
            <li class="disabled"><span>&laquo;</span></li> 
            <li class="active"><span>1</span></li>
            <li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $claim_array['total'], 'per_page' => $claim_array['per_page'], 'current_page' => $claim_array['current_page'], 'last_page' => $claim_array['last_page'], 'from' => $claim_array['from'], 'to' => $claim_array['to'], 'pagination_prt' => $pagination_prt);
            $claims_list = json_decode($filter_group_fac_list->toJson());
            $filter_group_fac_list = $claims_list->data;
        }else {
            $filter_group_fac_list = $filter_group_fac_lists->get();
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'pos_id', 'filter_group_fac_list', 'practiceopt', 'pagination','search_by')));
    }

    public function getFacilityListExportApi($export = '') {

        $request = Request::All();
        $practiceopt = $request['practiceoption'];
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        $pos_id = $request['pos_code'];
        // To Provider  
        //$get_facilities = Facility::with('pos_details','user')->get();
        if ($practiceopt == "provider_list") {
            $filter_group_fac_list = Facility::with(['facility_user_details', 'pos_details' => function($query) {
                            $query->select('*');
                        }])->select('pos_id', 'facility_name', 'created_by', 'created_at')->get();
        } else {
            if ($pos_id == 'all') {
                /* $filter_group_fac_list = Claims::with('facility','claim_unit_details','pos_details')->get(); */
                $filter_group_fac_lists = ClaimInfoV1::with('facility', 'claim_unit_details', 'pos')->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->select('*')->get();
            } else {
                /* $filter_group_fac_lists = Claims::with(['facility','claim_unit_details','pos_details'=>  function($query)use($pos_id) { $query->where('code',$pos_id);} ])->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->get(); */
                $filter_group_fac_lists = ClaimInfoV1::whereHas('pos', function($query) use ($pos_id) {
                            $query->where('id', $pos_id);
                        })->with(['facility', 'claim_unit_details', 'pos_details'])->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->get();


                
            }
            $total_charge = $total_adjusment = $unit = $patient_due = $insurance_due = $balance_amt = 0;
            
            $filter_group_fac_list = [];
            if (!empty($filter_group_fac_lists)) {
                foreach ($filter_group_fac_lists as $filter_group_fac_lists) {
                    $facility_name = @$filter_group_fac_lists->facility->facility_name;
                    $pos = @$filter_group_fac_lists->pos_details->code;
                    @$total_charge = @$total_charge + @$filter_group_fac_lists->total_charge;
                    $total_adjusment = @$total_adjusment + @$filter_group_fac_lists->total_adjusted;
                    $unit = @$unit + @$filter_group_fac_lists->claim_unit_details->unit;
                    $total_paid = @$total_adjusment + @$filter_group_fac_lists->total_paid;
                    $patient_due = @$patient_due + @$filter_group_fac_lists->patient_due;
                    $insurance_due = @$insurance_due + @$filter_group_fac_lists->insurance_due;
                    $balance_amt = @$balance_amt + @$filter_group_fac_lists->balance_amt;
                    $filter_group_fac_list[$facility_name][@$pos]['charges'] = @$total_charge;
                    $filter_group_fac_list[$facility_name][@$pos]['total_adjusment'] = @$total_adjusment;
                    $filter_group_fac_list[$facility_name][@$pos]['total_paid'] = @$total_paid;
                    $filter_group_fac_list[$facility_name][@$pos]['units'] = @$unit;
                    $filter_group_fac_list[$facility_name][@$pos]['patient_due'] = @$patient_due;
                    $filter_group_fac_list[$facility_name][@$pos]['insurance_due'] = @$insurance_due;
                    $filter_group_fac_list[$facility_name][@$pos]['balance_amt'] = @$balance_amt;
                    $filter_group_fac_list['pos'][$pos] = @$filter_group_fac_lists->pos_details->pos;
                }
            }
        }


        $total_cpt_count = 0;
        @$count = 0;
        @$total_adj = 0;
        @$patient_total = 0;
        @$insurance_total = 0;
        @$get_list = array();

        if ($practiceopt == "provider_list") {
            foreach ($filter_group_fac_list as $list) {

                $data['faclity_name'] = @$list->facility_name;
                $data['pos'] = @$list->pos_details->pos;

                $data['created_at'] = date('m/d/Y', strtotime(@$list->created_at));
                $data['user'] = @$list->facility_user_details->short_name;

                $get_list[$total_cpt_count] = $data;
                $total_cpt_count++;
            }


            $get_export_result = $get_list;

            $get_export_result[$total_cpt_count] = ['faclity_name' => '', 'pos' => '', 'created_at' => '', 'user' => ''];

            $result["value"] = json_decode(json_encode($get_export_result));
            $result["exportparam"] = array(
                'filename' => 'Facility Summary',
                'heading' => '',
                'fields' => array(
                    'faclity_name' => 'Facility Name',
                    'pos' => 'Type',
                    'created_at' => 'Created On',
                    'user' => 'User'
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
        } else {
            @$pos = @$filter_group_fac_list->pos;


            unset($filter_group_fac_list['pos']);
            foreach ($filter_group_fac_list as $key => $lists) {
                foreach ($lists as $new_data => $list) {

                    $data['faclity_name'] = @$key;
                    $data['pos'] = Pos::where('code', $new_data)->pluck('pos')->all();
                    $data['units'] = $list['units'];
                    $data['charges'] = @$list['charges'];
                    $data['adjs'] = @$list['total_adjusment'];
                    $data['payments'] = @$list['total_paid'];
                    $data['pat_balance'] = @$list['patient_due'];
                    $data['ins_balance'] = @$list['insurance_due'];
                    $data['total_balance'] = @$list['balance_amt'];
                    @$total_adj = $total_adj + @$list['total_adjusment'];
                    @$patient_total = $patient_total + @$list['patient_due'];
                    @$insurance_total = $insurance_total + @$list['insurance_due'];
                    $get_list[$total_cpt_count] = $data;
                    $total_cpt_count++;
                }
            }

            $get_export_result = $get_list;

            $get_export_result[$total_cpt_count] = ['faclity_name' => '', 'pos' => '', 'units' => '', 'charges' => '', 'adjs' => Helpers::priceFormat($total_adj), 'payments' => '', 'pat_balance' => Helpers::priceFormat(@$patient_total), 'ins_balance' => Helpers::priceFormat(@$insurance_total), 'total_balance' => ''];

            $result["value"] = json_decode(json_encode($get_export_result));
            $result["exportparam"] = array(
                'filename' => 'Facility Summary',
                'heading' => '',
                'fields' => array(
                    'faclity_name' => 'Facility Name',
                    'pos' => 'POS',
                    'units' => 'Units',
                    'charges' => 'Charges($)',
                    'adjs' => 'Adjs($)',
                    'payments' => 'Payments($)',
                    'pat_balance' => 'Pat Balance($)',
                    'ins_balance' => 'Ins Balance($)',
                    'total_balance' => 'Total Balance($)'
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
        }
    }
    //-------------------------- START FACILITY SUMMARY LIST BY BASKAR -----------------------

    public function getFacilitySummarylistApi() {
        $pos_list = Pos::select('code', 'id', 'pos')->get();
        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('facility_summary');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('pos_list', 'cliam_date_details','search_fields','searchUserData')));
    }
    
    public function getFilterResultSummaryApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $practiceopt="";
        
        // Total charges
        $charges = ClaimInfoV1::selectRaw('claim_info_v1.id, sum(claim_info_v1.total_charge) as total_charge,claim_info_v1.facility_id,f.facility_name,p.pos, claim_info_v1.facility_id,claim_info_v1.created_at,claim_info_v1.pos_id,p.code')->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')->join('pos AS p','p.id','=','claim_info_v1.pos_id')->where('claim_info_v1.facility_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->wherenull('claim_info_v1.deleted_at')->wherenull('f.deleted_at');

        // Total Adjustments
        $adjustments = PMTClaimTxV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_withheld+pmt_claim_tx_v1.total_writeoff) as total_adjusted,f.facility_name, claim_info_v1.facility_id,pmt_claim_tx_v1.created_at,p.pos,claim_info_v1.pos_id,p.code')->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')->join('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')->join('pos AS p','p.id','=','claim_info_v1.pos_id')->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')->where('claim_info_v1.facility_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_tx_v1.deleted_at')->wherenull('f.deleted_at')->whereRaw("(pmt_claim_tx_v1.total_writeoff <> '0' or pmt_claim_tx_v1.total_withheld <> '0')");

        // Total Patient Payments
        /*
        $patient = PMTInfoV1::join('claim_info_v1','claim_info_v1.id','=','pmt_info_v1.source_id')
                ->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')
                ->join('pos AS p','p.id','=','claim_info_v1.pos_id')
                ->where('claim_info_v1.facility_id','<>',0)
                ->where('claim_info_v1.pos_id','<>',0)
                ->where('pmt_info_v1.pmt_method','Patient')                
                ->wherenull('claim_info_v1.deleted_at')
                ->wherenull('pmt_info_v1.deleted_at')
                ->wherenull('f.deleted_at')
                ->whereRaw('pmt_info_v1.void_check is null');
        
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
            $patient = $patient->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance','Refund'])
                         ->selectRaw('claim_info_v1.id, ((sum(IF(pmt_info_v1.pmt_method = "Patient" AND (pmt_info_v1.pmt_type="Payment" OR pmt_info_v1.pmt_type="Credit Balance" ), pmt_info_v1.pmt_amt, 0))) - (sum(IF(pmt_info_v1.pmt_method = "Patient" AND (pmt_info_v1.pmt_type="Refund") , pmt_info_v1.pmt_amt, 0)))) as total_paid, claim_info_v1.facility_id,pmt_info_v1.created_at,f.facility_name, IF(f.facility_name is null or f.facility_name = "", "wallet", p.pos) as pos,claim_info_v1.pos_id,p.code');
        } else {
            $patient = $patient->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])
                        ->selectRaw('claim_info_v1.id, sum(pmt_info_v1.pmt_amt) as total_paid, claim_info_v1.facility_id,pmt_info_v1.created_at,f.facility_name,IF(f.facility_name is null or f.facility_name = "", "wallet", p.pos) as pos,claim_info_v1.pos_id,p.code');
        }
        */
        $patient = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_paid) as total_paid,f.facility_name, claim_info_v1.facility_id,pmt_claim_tx_v1.created_at,pos,claim_info_v1.pos_id,p.code')
                    ->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                    ->join('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                    ->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')
                    ->join('pos AS p','p.id','=','claim_info_v1.pos_id')
                    ->where('claim_info_v1.facility_id','<>',0)
                    ->where('claim_info_v1.pos_id','<>',0)
                    ->wherenull('claim_info_v1.deleted_at')
                    ->wherenull('pmt_claim_tx_v1.deleted_at')
                    ->where('pmt_claim_tx_v1.pmt_method','Patient')                    
                    ->wherenull('f.deleted_at');
                    //->whereHas('pmt_info', function($q)  {
                    //    $q->whereRaw('void_check is null');
                    //});
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
            $patient = $patient->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance','Refund']);
        } else {
            $patient = $patient->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
        }

        // Total Insurance Payments
        $insurance = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_paid) as total_paid,f.facility_name, claim_info_v1.facility_id,pmt_claim_tx_v1.created_at,pos,claim_info_v1.pos_id,p.code')
                    ->join('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                    ->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')
                    ->join('pos AS p','p.id','=','claim_info_v1.pos_id')
                    ->where('claim_info_v1.facility_id','<>',0)
                    ->where('claim_info_v1.pos_id','<>',0)
                    ->wherenull('claim_info_v1.deleted_at')
                    ->wherenull('pmt_claim_tx_v1.deleted_at')
                    ->where('pmt_claim_tx_v1.pmt_method','Insurance');
                    //->wherenull('f.deleted_at')->whereHas('pmt_info', function($q)  {
                    //   $q->whereRaw('void_check is null');
                    //});
                    
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance','Refund']);
            $search_by['Include Refund'] = 'Yes'; 
        } else {
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
            $search_by['Include Refund'] = 'No'; 
        }

        // Total Patient Balance
        $patient_bal = ClaimInfoV1::selectRaw('claim_info_v1.id, SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)
 as patient_due,f.facility_name, claim_info_v1.facility_id,claim_info_v1.created_at,pos,claim_info_v1.pos_id,p.code')->join('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')->join('pos AS p','p.id','=','claim_info_v1.pos_id')->where('claim_info_v1.facility_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_fin_v1.deleted_at')->where('claim_info_v1.insurance_id',0)->wherenull('f.deleted_at');

        // Total Insurance Balance
        $insurance_bal = ClaimInfoV1::selectRaw('claim_info_v1.id, SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)
  as insurance_bal,f.facility_name, claim_info_v1.facility_id,claim_info_v1.created_at,pos,claim_info_v1.pos_id,p.code')->join('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')->join("facilities AS f",'f.id','=','claim_info_v1.facility_id')->join('pos AS p','p.id','=','claim_info_v1.pos_id')->where('claim_info_v1.facility_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_fin_v1.deleted_at')->wherenull('f.deleted_at')->where('claim_info_v1.insurance_id','!=',0);

        // Total Units
        $unit_details = ClaimInfoV1::join("facilities AS f",'f.id','=','claim_info_v1.facility_id')->selectRaw('claim_info_v1.id,f.facility_name,sum(claim_cpt_info_v1.unit) as unit,claim_cpt_info_v1.created_at, claim_info_v1.facility_id,pos,claim_info_v1.pos_id,p.code')->join('pos AS p','p.id','=','claim_info_v1.pos_id')->join('claim_cpt_info_v1','claim_cpt_info_v1.claim_id','=','claim_info_v1.id')->where('claim_info_v1.facility_id','<>',0)->where('claim_info_v1.pos_id','<>',0)->wherenull('f.deleted_at');
        $practice_timezone = Helpers::getPracticeTimeZone();
        if ($request['transaction_date'] != '') {
            $exp = explode("-",$request['transaction_date']);
            $start_date = date("Y-m-d",strtotime($exp[0]));
            $end_date = date("Y-m-d",strtotime($exp[1]));
            $charges->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $adjustments->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $patient->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $insurance->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $patient_bal->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $insurance_bal->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $unit_details->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date)); 
        } else{
            $start_date = "";
            $end_date = "";
        }
        
        $header = [];
        if (isset($request['facility_id'])) {
            if(isset($request['export']) || is_string($request['facility_id'])){
                $fac_id = explode(',', $request['facility_id']);
            }else{  
                $fac_id = $request['facility_id'];
            }
            $charges = $charges->whereIn('claim_info_v1.facility_id',$fac_id);
            $adjustments = $adjustments->whereIn('claim_info_v1.facility_id',$fac_id);
            $patient = $patient->whereIn('claim_info_v1.facility_id',$fac_id);
            $insurance = $insurance->whereIn('claim_info_v1.facility_id',$fac_id);
            $patient_bal = $patient_bal->whereIn('claim_info_v1.facility_id',$fac_id);
            $insurance_bal = $insurance_bal->whereIn('claim_info_v1.facility_id',$fac_id);
            $unit_details = $unit_details->whereIn('claim_info_v1.facility_id',$fac_id);
            $search_by['Facility'] = array_flatten(Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id',$fac_id)->get()->toArray())[0];
            $header = $fac_id;
        } else{
            $search_by['Facility'] = 'All';
        }
           
        $charges = $charges->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $adjustments = $adjustments->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $patient = $patient->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $insurance = $insurance->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $patient_bal = $patient_bal->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $insurance_bal = $insurance_bal->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $unit_details = $unit_details->groupBy('claim_info_v1.pos_id','claim_info_v1.facility_id')->get();
        $result = [];

        foreach($charges as $charge){
            $result[$charge->facility_id.'_'.$charge->pos_id]['facility_name'] = $charge->facility_name;
            $result[$charge->facility_id.'_'.$charge->pos_id]['pos'] = $charge->pos;
            $result[$charge->facility_id.'_'.$charge->pos_id]['code'] = $charge->code;
            $result[$charge->facility_id.'_'.$charge->pos_id]['facility_id'] = $charge->facility_id;
            $result[$charge->facility_id.'_'.$charge->pos_id]['charges'] = $charge->total_charge;
        }
        foreach($adjustments as $adjustment){
            $result[$adjustment->facility_id.'_'.$adjustment->pos_id]['facility_name'] = $adjustment->facility_name;
            $result[$adjustment->facility_id.'_'.$adjustment->pos_id]['pos'] = $adjustment->pos;
            $result[$adjustment->facility_id.'_'.$adjustment->pos_id]['code'] = $adjustment->code;
            $result[$adjustment->facility_id.'_'.$adjustment->pos_id]['facility_id'] = $adjustment->facility_id;
            $result[$adjustment->facility_id.'_'.$adjustment->pos_id]['adjustments'] = $adjustment->total_adjusted;
        }
        foreach($patient as $pat){
            $result[$pat->facility_id.'_'.$pat->pos_id]['facility_name'] = $pat->facility_name;
            $result[$pat->facility_id.'_'.$pat->pos_id]['pos'] = $pat->pos;
            $result[$pat->facility_id.'_'.$pat->pos_id]['code'] = $pat->code;
            $result[$pat->facility_id.'_'.$pat->pos_id]['facility_id'] = $pat->facility_id;
            $result[$pat->facility_id.'_'.$pat->pos_id]['patient'] = $pat->total_paid;
        }
        foreach($insurance as $ins){
            $result[$ins->facility_id.'_'.$ins->pos_id]['facility_name'] = $ins->facility_name;
            $result[$ins->facility_id.'_'.$ins->pos_id]['pos'] = $ins->pos;
            $result[$ins->facility_id.'_'.$ins->pos_id]['code'] = $ins->code;
            $result[$ins->facility_id.'_'.$ins->pos_id]['facility_id'] = $ins->facility_id;
            $result[$ins->facility_id.'_'.$ins->pos_id]['insurance'] = $ins->total_paid;
        }
        foreach($patient_bal as $pat_bal){
            $result[$pat_bal->facility_id.'_'.$pat_bal->pos_id]['facility_name'] = $pat_bal->facility_name;
            $result[$pat_bal->facility_id.'_'.$pat_bal->pos_id]['pos'] = $pat_bal->pos;
            $result[$pat_bal->facility_id.'_'.$pat_bal->pos_id]['code'] = $pat_bal->code;
            $result[$pat_bal->facility_id.'_'.$pat_bal->pos_id]['facility_id'] = $pat_bal->facility_id;
            $result[$pat_bal->facility_id.'_'.$pat_bal->pos_id]['patient_bal'] = $pat_bal->patient_due;
        }
        foreach($insurance_bal as $ins_bal){
            $result[$ins_bal->facility_id.'_'.$ins_bal->pos_id]['facility_name'] = $ins_bal->facility_name;
            $result[$ins_bal->facility_id.'_'.$ins_bal->pos_id]['pos'] = $ins_bal->pos;
            $result[$ins_bal->facility_id.'_'.$ins_bal->pos_id]['code'] = $ins_bal->code;
            $result[$ins_bal->facility_id.'_'.$ins_bal->pos_id]['facility_id'] = $ins_bal->facility_id;
            $result[$ins_bal->facility_id.'_'.$ins_bal->pos_id]['insurance_bal'] = $ins_bal->insurance_bal;
        }
        foreach($unit_details as $unit){
            $result[$unit->facility_id.'_'.$unit->pos_id]['facility_name'] = $unit->facility_name;
            $result[$unit->facility_id.'_'.$unit->pos_id]['pos'] = $unit->pos;
            $result[$unit->facility_id.'_'.$unit->pos_id]['code'] = $unit->code;
            $result[$unit->facility_id.'_'.$unit->pos_id]['facility_id'] = $unit->facility_id;
            $result[$unit->facility_id.'_'.$unit->pos_id]['unit_details'] = $unit->unit;
        }
        $charges = array_sum(array_column($result, 'charges'));
        $adjustments = array_sum(array_column($result, 'adjustments'));
        $patient = array_sum(array_column($result, 'patient'));
        $insurance = array_sum(array_column($result, 'insurance'));
        $patient_bal = array_sum(array_column($result, 'patient_bal'));
        $insurance_bal = array_sum(array_column($result, 'insurance_bal'));
        $unit_details = array_sum(array_column($result, 'unit_details'));
        
        // Payments wallet only - total payment amount minus amount used calculation used. 
        $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt');
        /* - Wallet amount till now not consider the transaction date
        if (isset($request['transaction_date']) && $request['transaction_date'] != '') {
            $wallet->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
        }
        */
        $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
        $wallet = ($wallet[0]->pmt_amt!=null) ? $wallet[0]->pmt_amt : 0;

        if (!isset($request['export'])) {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $p = Input::get('page', 1);
            $paginate = $paginate_count;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($result, $offSet, $paginate,true);
            $facilities = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($result), $paginate,$p,['path'=>Request::url()]);
            $pagination_prt = $facilities->render();
            $facilities = $facilities->toArray();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
            <li class="disabled"><span>&laquo;</span></li> 
            <li class="active"><span>1</span></li>
            <li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $facilities['total'], 'per_page' => $facilities['per_page'], 'current_page' => $facilities['current_page'], 'last_page' => $facilities['last_page'], 'from' => $facilities['from'], 'to' => $facilities['to'], 'pagination_prt' => $pagination_prt);
            $facilities = $facilities['data'];
        }else {
            $facilities = $result;
            $pagination = '';
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'pos_id', 'charges', 'practiceopt', 'pagination','unit_details','adjustments','patient','insurance','patient_bal','insurance_bal','facilities','search_by','practiceopt','header', 'wallet')));
    }
    //---------------------------- END FACILITY SUMMARY LIST BY BASKAR ---------------------
    
    /** Stored Procedure for Facility Summary - Anjukaselvan**/
    
    public function getFilterResultSummaryApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $practiceopt="";
        
        $start_date = $end_date = $facility_id =  '';        
            
        if ($request['transaction_date'] != '') {
            $exp = explode("-",$request['transaction_date']);
            $start = str_replace('"', '', $exp[0]);
            $end = str_replace('"', '', $exp[1]);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end);
            $search_by['Transaction Date'] = date("m/d/y", strtotime($start)) . ' to ' . date("m/d/y", strtotime($end)); 
        } else{
            $start_date = "";
            $end_date = "";
        }
        if ($export == "") {
            if(isset($request['facility_id']) && $request['facility_id'] != '') {
                $facility_id = implode(",", $request['facility_id']);
                //$fac_id = explode(',', $request['facility_id'][0]);
                $fac_id = $request['facility_id'];    
                $search_by['Facility'] = array_flatten(Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id',$fac_id)->get()->toArray())[0];
            }else{
                $search_by['Facility'] = 'All';
            }
        }else if(isset($request['facility_id[]']) && $request['facility_id[]'] != ''){
            $facility_id = $request['facility_id[]'];
        }
        
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
            $search_by['Include Refund'] = 'Yes'; 
        } else {
            $search_by['Include Refund'] = 'No'; 
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
            $facility = DB::select('call facilitySummary("' . $facility_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $count = $facility[0]->facility_count;
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
            // Total facility 
            $facility = DB::select('call facilitySummary("' . $facility_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }
            $report_array = $this->paginate($facility)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);            
            
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            // Total facility 
            $facility = DB::select('call facilitySummary("' . $facility_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
        }
            $facilities = $facility;
            // Total charges
            $charges_list = DB::select('call facilitySummaryCharges("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $charges_result = json_decode(json_encode($charges_list), true);
            $charges = array_combine(array_column($charges_result, 'pos'), array_column($charges_result, 'total_charge'));

            // Total Adjustments
            $adjustments_list = DB::select('call facilitySummaryAdjustments("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $adjustments_result = json_decode(json_encode($adjustments_list), true);
            $adjustments = array_combine(array_column($adjustments_result, 'pos'), array_column($adjustments_result, 'total_adjusted'));

            // Total Patient Payments
            $patient_pay_list = DB::select('call facilitySummaryPatientPayment("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $patient_pay_result = json_decode(json_encode($patient_pay_list), true);
            $patient = array_combine(array_column($patient_pay_result, 'pos'), array_column($patient_pay_result, 'total_paid'));

            // Total Insurance Payments
            $insurance_list = DB::select('call facilitySummaryInsurancePayment("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $insurance_result = json_decode(json_encode($insurance_list), true);
            $insurance = array_combine(array_column($insurance_result, 'pos'), array_column($insurance_result, 'total_paid'));

            // Total Patient Balance
            $patient_bal_list = DB::select('call facilitySummaryPatientBalance("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $patient_bal_result = json_decode(json_encode($patient_bal_list), true);
            $patient_bal = array_combine(array_column($patient_bal_result, 'pos'), array_column($patient_bal_result, 'patient_due'));

            // Total Insurance Balance
            $insurance_bal_list = DB::select('call facilitySummaryInsuranceBalance("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $insurance_bal_result = json_decode(json_encode($insurance_bal_list), true);
            $insurance_bal = array_combine(array_column($insurance_bal_result, 'pos'), array_column($insurance_bal_result, 'insurance_bal'));

            // Total Units
            $unit_details_list = DB::select('call facilitySummaryUnitDetails("' . $start_date . '", "' . $end_date . '", "' . $facility_id . '")');
            $unit_details_result = json_decode(json_encode($unit_details_list), true);
            $unit_details = array_combine(array_column($unit_details_result, 'pos'), array_column($unit_details_result, 'unit'));
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'charges','unit_details','adjustments','patient','insurance','patient_bal','insurance_bal','facilities', 'pagination','search_by','practiceopt')));
    }

    //  ****    Provider List  ****//
    /* public function getProvidersettingsApi(){
      $cliam_date_details = Facility::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
      //$billing_provider_list = Provider::select('provider_name','id')->get();

      $user_list = User::select('name','id')->get();

      return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('cliam_date_details','user_list')));
      } */
    //****   Group By ****//
    /* public function getGroupFilterResultApi(){
      $request = Request::All();
      if($request['hidden_from_date'] == '')
      $start_date = $request['from_date'];
      else
      $start_date = $request['hidden_from_date'];
      if($request['hidden_to_date'] == '')
      $end_date = $request['to_date'];
      else
      $end_date = $request['hidden_to_date'];

      $pos_id=$request['pos_code'];

      //$filter_group_fac_list = Claims::with('claim_unit_details','pos_details')->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->select('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at')->get();
      if($pos_id == 'all')
      {
      $filter_group_fac_list = Claims::with('facility','claim_unit_details','pos_details')->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->groupBy('pos_id')->select('*')->get();
      }
      else
      {
      $filter_group_fac_list = Claims::with(['facility','claim_unit_details','pos_details'=>  function($query)use($pos_id) { $query->where('code',$pos_id);} ])->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->groupBy('pos_id')->get();
      }
      return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('start_date','end_date','filter_group_fac_list','pos_id')));

      } */
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }
}