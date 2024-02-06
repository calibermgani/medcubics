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
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Payments\PMTInfoV1;
//use App\Models\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Users as User;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use Illuminate\Pagination\LengthAwarePaginator;

class CptlistApiController extends Controller {

    public function getCptlistApi() {
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('cpt_summary');
        $search_fields = $search_fields_data['search_fields'];
        $searchUserData = $search_fields_data['searchUserData'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details','search_fields','searchUserData')));
    }

    public function getFilterResultApi($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
		
        // Total CPT List 
        $cpt_list = ClaimCPTInfoV1::selectRaw('distinct claim_cpt_info_v1.id,claim_cpt_info_v1.cpt_code,      
            claim_cpt_info_v1.created_at,        
            sum(claim_cpt_info_v1.unit) as unit, 
            sum(claim_cpt_info_v1.charge) as total_charge,          
            sum(pmt_claim_cpt_fin_v1.patient_paid) as patient_paid,
            sum(pmt_claim_cpt_fin_v1.insurance_paid) as insurance_paid,
            sum(pmt_claim_cpt_fin_v1.patient_paid+pmt_claim_cpt_fin_v1.insurance_paid) as total_paid,
            sum(pmt_claim_cpt_fin_v1.patient_adjusted) as pat_adj,
            sum(pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held) as ins_adj,
            sum(pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held) as tot_adj,
            
            sum(pmt_claim_cpt_fin_v1.patient_balance) as patient_bal,
            sum(pmt_claim_cpt_fin_v1.insurance_balance) as insurance_bal,

            sum(pmt_claim_cpt_fin_v1.cpt_charge-(pmt_claim_cpt_fin_v1.patient_paid + pmt_claim_cpt_fin_v1.insurance_paid +pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.with_held)) as total_ar_due,
            cpts.description
			'
            /*            
            sum(pmt_claim_cpt_fin_v1.insurance_balance) as insurance_bal,
            sum(pmt_claim_cpt_fin_v1.patient_balance) as patient_bal,            
            sum(pmt_claim_cpt_fin_v1.insurance_balance - (pmt_claim_cpt_fin_v1.patient_paid+pmt_claim_cpt_fin_v1.patient_adjusted)) as insurance_bal'
            */
            )       
        ->join("pmt_claim_cpt_fin_v1",'pmt_claim_cpt_fin_v1.claim_cpt_info_id','=','claim_cpt_info_v1.id')
        ->leftjoin(DB::raw("(SELECT
                cpts.short_description,
                cpts.medium_description as description,
                cpts.cpt_hcpcs            
            FROM cpts
            WHERE cpts.deleted_at IS NULL
                GROUP BY cpts.cpt_hcpcs
            ) as cpts"), function($join) {
            $join->on('cpts.cpt_hcpcs', '=', 'claim_cpt_info_v1.cpt_code');
        })
        ->wherenull('claim_cpt_info_v1.deleted_at')
        ->groupBy('claim_cpt_info_v1.cpt_code');
        

        $search_by = $patient = [];
        // Filter by Transaction date    
        if(isset($request['created_at']) && $request['created_at'] != '') {
            $exp = explode("-",$request['created_at']);
            $start_date = str_replace('"', '', $exp[0]);
            $end_date = str_replace('"', '', $exp[1]);
            // $from_date = date('Y-m-d', strtotime($start_date));
            // $to_date = date('Y-m-d', strtotime($end_date));
            $from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $cpt_list->Where(function ($q) use ($from_date, $to_date) {    
                $q->whereRaw("( (claim_cpt_info_v1.created_at) >= '$from_date' and (claim_cpt_info_v1.created_at) <= '$to_date' )");
            });            
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date)); 
            }
            else{
                $search_by['Transaction Date'][] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date)); 
            }
        } else{
            $start_date = "";
            $end_date = "";
        }

        /*
        if(isset($request['cpt_code']) &&$request['cpt_code'] != '') {
            $cpt_list = $cpt_list->where('claim_cpt_info_v1.cpt_code','=',$request['cpt_code']);
            $chargedet->where('claim_cpt_info_v1.cpt_code','=',$request['cpt_code']);
            $search_by['CPT Code'][] = $request['cpt_code'];
        }
        */

        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['CPT Type'] = 'Custom Range'; 
            }
            else{
                $search_by['CPT Type'][] = 'Custom Range'; 
            }
            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $cpts_list = Helpers::CptsRangeBetween($request['custom_type_from'], $request['custom_type_to']);                                  
               //$cpt_list->where('claim_cpt_info_v1.cpt_code','>=',$request['custom_type_from'])->where('claim_cpt_info_v1.cpt_code','<=',$request['custom_type_to']);
                $cpt_list = $cpt_list->whereIn('claim_cpt_info_v1.cpt_code', $cpts_list);

                //$cpt_list = $cpt_list->where('claim_cpt_info_v1.cpt_code','>=',$custom_type_from)->where('claim_cpt_info_v1.cpt_code', '<=', $custom_type_to);   
            }
        } 
                 
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['CPT Type'] = 'CPT Code'; 
            }
            else{
                $search_by['CPT Type'][] = 'CPT Code'; 
            }
            if(!empty($request['cpt_code_id'])){
                $cpt_code_id = $request['cpt_code_id'];

                $cpt_list->Where(function ($q) use ($cpt_code_id) { 
                    // Comma separated search option added for CPT code.
                    //$q->where('cpt_code','=',$cpt_code_id); 
                    $cptCodes = array_map('trim', explode(',', $cpt_code_id));            
                    $q->Where(function ($qry) use ($cptCodes) {
                        foreach ($cptCodes as $key => $dc) { 
                            if($key == 0)
                                $qry->where('claim_cpt_info_v1.cpt_code', $dc); 
                           else  
                                $qry->orWhere('claim_cpt_info_v1.cpt_code', $dc); 
                        }
                    }); 
                });
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['CPT Code'] = $cpt_code_id;
                }
                else{
                    $search_by['CPT Code'][] = $cpt_code_id;
                }
            }
        }
        if (isset($request['user']) && $request['user'] != '') {
            // Request is string or array based on condition added
            // Rev. 1 Ref: MR-2659 - 22-Aug-2019 Anjukaselvan
            $user = (is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
            $cpt_list = $cpt_list->whereIn('claim_cpt_info_v1.created_by',$user);
            $User_name =  User::whereIn('id', $user)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['User'] = $User_name;
            }
            else{
                $search_by['User'][] = $User_name;
            }
        }
		
        // Payments wallet only
        $patient['wallet'] = 0;

        $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt))-(sum(amt_used)) as pmt_amt');
        if(isset($from_date) && $from_date != '' && isset($end_date) && $end_date != '')
            $wallet->whereRaw("DATE(created_at) >= '$from_date' and DATE(created_at) <= '$to_date'");

        $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
        if($wallet[0]->pmt_amt!=null)
            $patient['wallet'] = $wallet[0]->pmt_amt;

        $cptsummary = clone  $cpt_list;
        $cpt_summary = $cptsummary->get()->toArray();
        
        $cpt_codes = [];
        foreach ($cpt_summary as $item) {
            $cpt_codes[] = $item['cpt_code'];
        } 
        // DESC
        $cptDesc = DB::table('cpts')->whereIn('cpt_hcpcs', $cpt_codes)->pluck('medium_description', 'cpt_hcpcs')->all();

        if (!isset($request['export'])) {           

            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $cpt_list = $cpt_list->paginate($paginate_count);
            $cpts = $cpt_list->toArray();
            $pagination_prt = $cpt_list->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
            <li class="disabled"><span>&laquo;</span></li> 
            <li class="active"><span>1</span></li>
            <li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $cpts['total'], 'per_page' => $cpts['per_page'], 'current_page' => $cpts['current_page'], 'last_page' => $cpts['last_page'], 'from' => $cpts['from'], 'to' => $cpts['to'], 'pagination_prt' => $pagination_prt);
            $claims_list = json_decode($cpt_list->toJson());
            $cpts = $claims_list->data;
        } else {
            $cpts = $cpt_list->get()->toArray();
            $pagination = '';
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'pagination','search_by', 'cpts', 'patient', 'cpt_summary', 'cptDesc')));
    }
    
    /** Stored Procedure for CPT Summary - Anjukaselvan **/
    
    public function getFilterResultApiSP($export = '', $data = '') {
        $request = (isset($data) && !empty($data)) ? $data : Request::All();
        $search_by = $patient = [];
        $start_date = $end_date = $cpt_custom_type_from = $cpt_custom_type_to = $cpt_code_id = $user_ids =  '';
        // Filter by Transaction date    
        if(isset($request['created_at']) && $request['created_at'] != '') {
            $exp = explode("-",$request['created_at']);
            $start = str_replace('"', '', $exp[0]);
            $end = str_replace('"', '', $exp[1]);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end);
            $search_by['Transaction Date'][] = date("m/d/y", strtotime($start)) . ' to ' . date("m/d/y", strtotime($end));
        } else{
            $start_date = "";
            $end_date = "";
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
        if ($export == "") {
            if(isset($request['user']) && $request['user'] != '') {
                $user_ids = implode(",", $request['user']);
                $User_name =  User::whereIn('id', $request["user"])->where('status', 'Active')->pluck('short_name', 'id')->all();
                $User_name = implode(", ", array_unique($User_name));
                $search_by['User'][] = $User_name;
            }
        }else if(isset($request['user[]']) && $request['user[]'] != ''){
            $user_ids = $request['user[]'];
        }
        //sp start
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
            $sp_return = DB::select('call cptSummary("' . $start_date . '", "' . $end_date . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $cpt_code_id . '", "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return; 
            $count = (isset($sp_count_return_result[0]->cpt_summary_count)) ? $sp_count_return_result[0]->cpt_summary_count : 0;
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
            //echo("CR Start".$start_date." ## ".$end_date."## ".$cpt_custom_type_from."##".$cpt_custom_type_to."##".$cpt_code_id."##".$user_ids."##".$offset."##".$recCount);
            $sp_return_result = DB::select('call cptSummary("' . $start_date . '", "' . $end_date . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $cpt_code_id . '", "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            
            // Summary result
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_summary = DB::select('call cptSummary("' . $start_date . '", "' . $end_date . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $cpt_code_id . '", "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $cpt_summary = (array) $sp_return_summary;
            
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call cptSummary("' . $start_date . '", "' . $end_date . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $cpt_code_id . '", "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $cpt_summary = $sp_return_result;
        }
        $cpts = $sp_return_result;
        
        
        // Payments wallet only
        //$patient['wallet'] = 0;
        $patient['wallet'] = isset($cpts[0]->pmt_amt) ?$cpts[0]->pmt_amt : 0;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'pagination','search_by', 'cpts', 'patient', 'cpt_summary', 'cptDesc')));
    }
    
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);

        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getCptListExportApi($export = '') {

        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        $export_cptlist_result = ClaimInfoV1::with(['cptdetails', 'claim_unit_details' => function($query) {
                        $query->where('cpt_code', '!=', 'Patient');
                    }])->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,cpt_codes')->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at')->groupBy('cpt_codes')->get();

        $total_cpt_count = 0;
        @$count = 0;
        @$total_adj = 0;
        @$patient_total = 0;
        @$insurance_total = 0;
        $get_list = array();
        foreach ($export_cptlist_result as $list) {

            $data['procedure'] = @$export_cptlist_result[$count]->cptdetails->cpt_hcpcs;
            $data['description'] = @$export_cptlist_result[$count]->cptdetails->medium_description;
            $data['units'] = @$export_cptlist_result[$count]->claim_unit_details->unit;
            $data['charges'] = @$export_cptlist_result[$count]->total_charge;
            $data['adjs'] = @$list->pat_adj+@$list->ins_adj;
            $data['payments'] = @$list->total_paid;
            $data['pat_balance'] = @$list->patient_bal;
            $data['ins_balance'] = @$list->insurance_bal;
            $data['total_balance'] = @$list->patient_bal+@$list->insurance_bal;

            //$data['charges'] = @$list->total_charge;
            //$data['adjs'] = @$list->total_adjusted;
            //$data['payments'] = @$list->patient_paid;
            //$data['pat_balance'] = @$list->patient_due;
            //$data['ins_balance'] = @$list->insurance_due;
            //$data['total_balance'] = @$list->balance_amt;
            //@$total_adj = $total_adj + $list->total_adjusted;
            //@$patient_total = $patient_total + $list->patient_due;
            //@$insurance_total = $insurance_total + $list->insurance_due;
            $get_list[$total_cpt_count] = $data;
            $total_cpt_count++;
        }

        $total_cpt_count = $total_cpt_count + 1;

        $get_export_result = $get_list;

        $get_export_result[$total_cpt_count] = ['procedure' => '', 'description' => '', 'units' => '', 'charges' => '', 'adjs' => Helpers::priceFormat($total_adj), 'payments' => '', 'pat_balance' => Helpers::priceFormat($patient_total), 'ins_balance' => Helpers::priceFormat($insurance_total), 'total_balance' => ''];



        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'CPT Summary',
            'heading' => '',
            'fields' => array(
                'procedure' => 'Procedure',
                'description' => 'Description',
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
