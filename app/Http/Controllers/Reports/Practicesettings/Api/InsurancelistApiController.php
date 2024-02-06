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
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Controller;
use App\Models\AdjustmentReason as AdjustmentReason;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Users as User;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use Illuminate\Pagination\LengthAwarePaginator;

class InsurancelistApiController extends Controller {

    public function getInsurancelistApi() {
        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('insurance_listing');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details','searchUserData','search_fields')));
    }

    public function getFilterResultApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $insurances = [];
        $insurance_category = @$request['insurance_category'];
        // Total payers 
        /*$payers = Insurance::selectRaw('insurancetypes.type_name as insurance_category,null as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name,  null as total_charge, null as total_adjusted, null as total_paid, null as insurance_bal, insurancetypes.id as type_id')->leftJoin('claim_info_v1','claim_info_v1.insurance_id','=','insurances.id')->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.claim_insurance_id','=','insurances.id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')->where('claim_info_v1.total_charge','<>',0)->where('pmt_claim_tx_v1.payer_insurance_id','!=',0)->whereRaw('pmt_claim_tx_v1.total_paid <> 0 or pmt_claim_tx_v1.total_withheld <> 0 or pmt_claim_tx_v1.total_writeoff <> 0')->orWhere('claim_info_v1.insurance_id','<>',0)->wherenull('pmt_claim_tx_v1.deleted_at')->wherenull('claim_info_v1.deleted_at')->groupBy('insurances.id');*/
        $payers = Insurance::selectRaw('insurancetypes.type_name as insurance_category, insurances.id as insurance_id, insurances.insurance_name as insurance_name,  insurancetypes.id as type_id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id');

        // Total charges
        $charges = ClaimInfoV1::selectRaw('null as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name, insurancetypes.type_name as insurance_category, sum(claim_info_v1.total_charge) as total_charge, null as total_adjusted, null as total_paid, null as insurance_bal, insurancetypes.id as type_id')->leftJoin("insurances",'insurances.id','=','claim_info_v1.insurance_id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')->where('claim_info_v1.self_pay', 'No')->wherenull('claim_info_v1.deleted_at')->where('claim_info_v1.insurance_id', '!=',0)->groupBy('claim_info_v1.insurance_id');

        // Total Adjustments
        $adjustments = PMTClaimTxV1::selectRaw('null as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name, insurancetypes.type_name as insurance_category, null as total_charge, sum(pmt_claim_tx_v1.total_withheld+pmt_claim_tx_v1.total_writeoff) as total_adjusted, null as total_paid, null as insurance_bal, insurancetypes.id as type_id')->leftJoin("insurances",'insurances.id','=','pmt_claim_tx_v1.payer_insurance_id')->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_tx_v1.deleted_at')->where('pmt_claim_tx_v1.pmt_method','Insurance')->groupBy('pmt_claim_tx_v1.payer_insurance_id');
        
        // Total Insurance Payments
        $insurance = PMTClaimTXV1::selectRaw('null as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name, insurancetypes.type_name as insurance_category, null as total_charge, null as total_adjusted, sum(pmt_claim_tx_v1.total_paid) as total_paid, null as insurance_bal, insurancetypes.id as type_id')
                        //->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin("insurances",'insurances.id','=','pmt_claim_tx_v1.payer_insurance_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->leftJoin("insurances AS i",'i.id','=','pmt_claim_tx_v1.payer_insurance_id')
                        ->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')
                        ->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_tx_v1.deleted_at')
                        //->wherenull('pmt_info_v1.deleted_at')
                        //->wherenull('pmt_info_v1.void_check')
                        ->where('pmt_claim_tx_v1.pmt_method','Insurance')
                        ->groupBy('pmt_claim_tx_v1.payer_insurance_id');
        
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {          
            $search_by['Include Refund'] = 'Yes';
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
        } else {
            $search_by['Include Refund'] = 'No';
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
        }
        
        // Total Insurance Balance
        $insurance_bal = ClaimInfoV1::selectRaw('null as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name, insurancetypes.type_name as insurance_category, null as total_charge, null as total_adjusted, null as total_paid, sum(claim_info_v1.total_charge - (patient_paid+patient_adj+insurance_paid+insurance_adj+withheld)) as insurance_bal, insurancetypes.id as type_id')->leftJoin("insurances",'insurances.id','=','claim_info_v1.insurance_id')->leftJoin('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')->wherenull('claim_info_v1.deleted_at')->wherenull('pmt_claim_fin_v1.deleted_at')->where('claim_info_v1.insurance_id','!=',0)->groupBy('claim_info_v1.insurance_id');

        // Total Units
        $unit_details = ClaimInfoV1::selectRaw('sum(claim_cpt_info_v1.unit) as units, insurances.id as insurance_id, insurances.insurance_name as insurance_name, insurancetypes.type_name as insurance_category, null as total_charge, null as total_adjusted, null as total_paid, null as insurance_bal, insurancetypes.id as type_id')->leftJoin("insurances",'insurances.id','=','claim_info_v1.insurance_id')->leftJoin('claim_cpt_info_v1','claim_cpt_info_v1.claim_id','=','claim_info_v1.id')->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')->groupBy('claim_info_v1.insurance_id');

        if($insurance_category != 'All'){
            $payers->where('insurances.insurancetype_id',$insurance_category);
            $charges->where('insurances.insurancetype_id',$insurance_category);
            $adjustments->where('insurances.insurancetype_id',$insurance_category);
            $insurance->where('insurances.insurancetype_id',$insurance_category);
            $insurance_bal->where('insurances.insurancetype_id',$insurance_category);
            $unit_details->where('insurances.insurancetype_id',$insurance_category);
            $insGrpBy = DB::table('insurancetypes')->where('id',$insurance_category)->pluck('type_name')->all();
            if(!empty($insGrpBy)) {
                $search_by['Insurance Group By'] = implode(", ", array_unique($insGrpBy));;
            }
        }

        if(isset($request['user']) && $request['user'] != '') {
            $payers->whereIn('insurances.created_by', explode(',', $request['user']));
            $charges->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $adjustments->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $insurance->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $insurance_bal->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $unit_details->whereIn('claim_info_v1.created_by', explode(',', $request['user']));
            $User_name =  User::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();          
            $User_name = implode(", ", array_unique($User_name));
            $search_by['Users'] = $User_name;
        }   else{
            $search_by['Users'] = 'All';
        }
        
        $practice_timezone = Helpers::getPracticeTimeZone();
        if (!empty($request['created_at'])) {
            $exp = explode('-', $request['created_at']);
            $start_date = date("Y-m-d", strtotime($exp[0]));
            $end_date = date("Y-m-d", strtotime($exp[1]));
            $search_by['Transaction Date'] = date("m/d/y", strtotime($exp[0])) . ' to ' . date("m/d/y", strtotime($exp[1])); 
            $charges->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $adjustments->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $insurance->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $insurance_bal->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
            $unit_details->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
        } else{
            $start_date = "";
            $end_date = "";
        }
        
        $total_pmt = $tot_units = $tot_charges = $total_adj = $patient_total = $insurance_total = $total = $count = $payments = 0;
        $payer_clone = clone $payers->get();
        $charges = $charges->pluck('total_charge','insurance_id')->all();
        $adjustments = $adjustments->pluck('total_adjusted','insurance_id')->all();
        $insurance = $insurance->pluck('total_paid','insurance_id')->all();
        $insurance_bal = $insurance_bal->pluck('insurance_bal','insurance_id')->all();
        $unit_details = $unit_details->pluck('units','insurance_id')->all();
        
        foreach($payer_clone as  $list){
            $insurance_name = $list['insurance_name'];
            $insurance_id = $list['insurance_id'];
            $units = (isset($unit_details[$insurance_id]))?$unit_details[$insurance_id]:0;
            $total_charge = isset($charges[$insurance_id])?$charges[$insurance_id]:0;
            $adjustment = isset($adjustments[$insurance_id])?$adjustments[$insurance_id]:0;
            $pmt = isset($insurance[$insurance_id])?$insurance[$insurance_id]:0;
            $ins_bal = isset($insurance_bal[$insurance_id])?$insurance_bal[$insurance_id]:0;
            //if(($units || $total_charge || $adjustment || $pmt || $ins_bal)!=0){
            if($insurance_category=='All'){
                $insurances[$list['insurance_id']]['insurance_id'] = $insurance_id;
                $insurances[$list['insurance_id']]['units'] = $units;
                $insurances[$list['insurance_id']]['total_charge'] = $total_charge;
                $insurances[$list['insurance_id']]['adjustment'] = $adjustment;
                $insurances[$list['insurance_id']]['pmt'] = $pmt;
                $insurances[$list['insurance_id']]['ins_bal'] = $ins_bal;
                $insurances[$list['insurance_id']]['insurance_category'] = $list['insurance_category'];
                $insurances[$list['insurance_id']]['insurance_name'] = $list['insurance_name'];
            } elseif($insurance_category==$list['type_id']){
                $insurances[$list['insurance_id']]['insurance_id'] = $insurance_id;
                $insurances[$list['insurance_id']]['units'] = $units;
                $insurances[$list['insurance_id']]['total_charge'] = $total_charge;
                $insurances[$list['insurance_id']]['adjustment'] = $adjustment;
                $insurances[$list['insurance_id']]['pmt'] = $pmt;
                $insurances[$list['insurance_id']]['ins_bal'] = $ins_bal;
                $insurances[$list['insurance_id']]['insurance_category'] = $list['insurance_category'];
                $insurances[$list['insurance_id']]['insurance_name'] = $list['insurance_name'];
            }
            //}
            $tot_units +=$units;    
            $tot_charges +=$total_charge;    
            $total_adj +=$adjustment;    
            $total_pmt +=$pmt;    
            $insurance_total += $ins_bal;
        }
         if ($export == "") {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $p = Input::get('page', 1);
            $paginate = $paginate_count;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($insurances, $offSet, $paginate,true);
            $payers = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($insurances), $paginate,$p,['path'=>Request::url()]);
            $payer_array = $payers->toArray();
            $pagination_prt = $payers->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $payer_array['total'], 'per_page' => $payer_array['per_page'], 'current_page' => $payer_array['current_page'], 'last_page' => $payer_array['last_page'], 'from' => $payer_array['from'], 'to' => $payer_array['to'], 'pagination_prt' => $pagination_prt);
            $payers = json_decode($payers->toJson());
            $payers = $payers->data;
         }else {
             $payers = $insurances;
             //$payers = json_decode($payers->toJson());
             $pagination ='';
         }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'payers', 'charges', 'pagination','search_by','adjustments', 'insurance', 'insurance_bal', 'unit_details','tot_units','tot_charges','total_adj','total_pmt','insurance_total','export')));
    }
    
    /** Stored Procedure for Payers Summary - Anjukaselvan**/
    public function getFilterResultApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $insurance_category = $user_ids = $start_date = $end_date =  '';
        
        $insurance_category = @$request['insurance_category'];
        if($insurance_category != 'All'){
            $insGrpBy = DB::table('insurancetypes')->where('id',$insurance_category)->pluck('type_name')->all();
            if(!empty($insGrpBy)) {
                $search_by['Insurance Group By'] = implode(", ", array_unique($insGrpBy));
            }
        }
        
        if(isset($request['user']) && $request['user'] != '') {
            $user_ids = $request['user'];
            $User_name =  User::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['Users'] = $User_name;
        } else {
            $search_by['Users'] = 'All';
        }
        
        if (!empty($request['created_at'])) {
            $exp = explode('-', $request['created_at']);
            $start_date = date("Y-m-d", strtotime($exp[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($exp[1]));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $search_by['Transaction Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date)); 
        } else{
            $start_date = "";
            $end_date = "";
        }
        
        if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {          
            $search_by['Include Refund'] = 'Yes';
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
        } else {
            $search_by['Include Refund'] = 'No';
            $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
        }
        
       /* $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
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
            $payers_count = DB::select('call payerSummary("' . $insurance_category . '","' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $count = $payers_count[0]->insurance_count;
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
            // Total Payers 
            $payers = DB::select('call payerSummary("' . $insurance_category . '","' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }
            $report_array = $this->paginate($payers)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);            
            
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            // Total Payers 
            $payers = DB::select('call payerSummary("' . $insurance_category . '","' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $pagination ='';
        }

         // Total payers 
        $payers = $payers;
         */
        $recCount = 0;
        $paginate_count = 0;
        $offset = 0;
        // Total Payers 
        $payers = DB::select('call payerSummary("' . $insurance_category . '","' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');        

        // Total charges
        $charges_list = DB::select('call payerSummaryCharge("' . $insurance_category . '","' . $user_ids . '", "' . $start_date . '", "' . $end_date . '")');
        $charges_result = json_decode(json_encode($charges_list), true);
        $charges = array_combine(array_column($charges_result, 'insurance_name'), array_column($charges_result, 'total_charge'));

        // Total Adjustments
        $adjustments_list = DB::select('call payerSummaryAdjustment("' . $insurance_category . '","' . $user_ids . '", "' . $start_date . '", "' . $end_date . '")');
        $adjustments_result = json_decode(json_encode($adjustments_list), true);
        $adjustments = array_combine(array_column($adjustments_result, 'insurance_name'), array_column($adjustments_result, 'total_adjusted'));
        
        // Total Insurance Payments
        $insurance_list = DB::select('call payerSummaryInsurance("' . $insurance_category . '","' . $user_ids . '", "' . $start_date . '", "' . $end_date . '")');
        $insurance_result = json_decode(json_encode($insurance_list), true);
        $insurance = array_combine(array_column($insurance_result, 'insurance_name'), array_column($insurance_result, 'total_paid'));

        // Total Insurance Balance
        $insurance_bal_list = DB::select('call payerSummaryInsBalance("' . $insurance_category . '","' . $user_ids . '", "' . $start_date . '", "' . $end_date . '")');
        $insurance_bal_result = json_decode(json_encode($insurance_bal_list), true);
        $insurance_bal = array_combine(array_column($insurance_bal_result, 'insurance_name'), array_column($insurance_bal_result, 'insurance_bal'));
        
        // Total Units
        $unit_details_list = DB::select('call payerSummaryUnits("' . $insurance_category . '","' . $user_ids . '", "' . $start_date . '", "' . $end_date . '")');
        $unit_details_result = json_decode(json_encode($unit_details_list), true);
        $unit_details = array_combine(array_column($unit_details_result, 'insurance_name'), array_column($unit_details_result, 'units'));
        
        $total_pmt = $tot_units = $tot_charges = $total_adj = $patient_total = $insurance_total = $total = $count = $payments = 0;
        $payer_clone = $payers;
        $insurances = [];
        foreach($payer_clone as  $list){
            $insurance_name = $list->insurance_name;
            $insurance_id = $list->insurance_id;
            $type_id = $list->type_id;
            $units = (isset($unit_details[$insurance_name]))?$unit_details[$insurance_name]:0;
            $total_charge = isset($charges[$insurance_name])?$charges[$insurance_name]:0;
            $adjustment = isset($adjustments[$insurance_name])?$adjustments[$insurance_name]:0;
            $pmt = isset($insurance[$insurance_name])?$insurance[$insurance_name]:0;
            $ins_bal = isset($insurance_bal[$insurance_name])?$insurance_bal[$insurance_name]:0;
            if($insurance_category=='All'){
                $insurances[$insurance_id]['units'] = $units;
                $insurances[$insurance_id]['total_charge'] = $total_charge;
                $insurances[$insurance_id]['adjustment'] = $adjustment;
                $insurances[$insurance_id]['pmt'] = $pmt;
                $insurances[$insurance_id]['ins_bal'] = $ins_bal;
                $insurances[$insurance_id]['insurance_category'] = $list->insurance_category;
                $insurances[$insurance_id]['insurance_name'] = $list->insurance_name;
            } elseif($insurance_category==$type_id){
                $insurances[$insurance_id]['units'] = $units;
                $insurances[$insurance_id]['total_charge'] = $total_charge;
                $insurances[$insurance_id]['adjustment'] = $adjustment;
                $insurances[$insurance_id]['pmt'] = $pmt;
                $insurances[$insurance_id]['ins_bal'] = $ins_bal;
                $insurances[$insurance_id]['insurance_category'] = $list->insurance_category;
                $insurances[$insurance_id]['insurance_name'] = $list->insurance_name;
            }
            $tot_units +=$units;    
            $tot_charges +=$total_charge;    
            $total_adj +=$adjustment;    
            $total_pmt +=$pmt;    
            $insurance_total += $ins_bal;
        }
        if ($export == "") {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $p = Input::get('page', 1);
            $paginate = $paginate_count;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($insurances, $offSet, $paginate,true);
            $payers = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($insurances), $paginate,$p,['path'=>Request::url()]);
            $payer_array = $payers->toArray();
            $pagination_prt = $payers->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $payer_array['total'], 'per_page' => $payer_array['per_page'], 'current_page' => $payer_array['current_page'], 'last_page' => $payer_array['last_page'], 'from' => $payer_array['from'], 'to' => $payer_array['to'], 'pagination_prt' => $pagination_prt);
            $payers = json_decode($payers->toJson());
            $payers = $payers->data;
        }else {
            $payers = $insurances;
            $pagination ='';
        }
        
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'payers', 'charges', 'pagination','search_by','adjustments', 'insurance', 'insurance_bal', 'unit_details','tot_units','tot_charges','total_adj','total_pmt','insurance_total','export')));
    }

    public function getInsListExportApi($export = '') {

        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];

        $ins_catid = $request['insurance_category'];
        if ($ins_catid == 'all') {
            $export_insuran_result = ClaimInfoV1::with(['insurance_details', 'claim_unit_details', 'patient_insurance' => function($query) {
                            $query->select('id');
                        }])->whereIn('id', [2, 5, 7])->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,insurance_id')->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted - patient_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at')->groupBy('insurance_id')->get();
        } else {
            //$filter_insuran_result = Claims::with(['insurance_details','claim_unit_details','patient_insurance'=>  function($query)use($ins_catid) { $query->where('id',$ins_catid);} ])->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->selectRaw('id,insurance_id')->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted - patient_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at')->groupBy('insurance_id') ->get();
            $export_insuran_result = ClaimInfoV1::with(['insurance_details', 'claim_unit_details', 'patient_insurance' => function($query)use($ins_catid) {
                            $query->select('id');
                        }])->where('id', $ins_catid)->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,insurance_id')->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted - patient_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at')->groupBy('insurance_id')->get();
        }

        $total_cpt_count = 0;
        @$count = 0;
        @$total_adj = 0;
        @$patient_total = 0;
        @$insurance_total = 0;
        @$get_list = array();

        foreach ($export_insuran_result as $list) {
            //dd($export_insuran_result);
            $data['ins_name'] = @$list->insurance_details->insurance_name;
            $data['ins_type'] = @$list->insurance_details->insurancetype->type_name;
            if (@$list->claim_unit_details->unit != '')
                $data['units'] = $list->claim_unit_details->unit;
            else
                $data['units'] = '0';
            $data['charges'] = @$list->total_charge;
            $data['adjs'] = @$list->total_adjusted;
            $data['payments'] = @$list->patient_paid;
            $data['pat_balance'] = @$list->patient_due;
            $data['ins_balance'] = @$list->insurance_due;
            $data['total_balance'] = @$list->balance_amt;
            @$total_adj = $total_adj + $list->total_adjusted;
            @$patient_total = $patient_total + $list->patient_due;
            @$insurance_total = $insurance_total + $list->insurance_due;
            $get_list[$total_cpt_count] = $data;
            $total_cpt_count++;
        }

        $total_cpt_count = $total_cpt_count + 1;

        $get_export_result = $get_list;

        $get_export_result[$total_cpt_count] = ['ins_name' => '', 'ins_type' => '', 'units' => '', 'charges' => '', 'adjs' => Helpers::priceFormat($total_adj), 'payments' => '', 'pat_balance' => Helpers::priceFormat($patient_total), 'ins_balance' => Helpers::priceFormat($insurance_total), 'total_balance' => ''];

        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'Insurance Summary',
            'heading' => '',
            'fields' => array(
                'ins_name' => 'Ins Name',
                'ins_type' => 'Ins Type',
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
    
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }

}