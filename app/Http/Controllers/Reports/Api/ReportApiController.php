<?php

namespace App\Http\Controllers\Reports\Api;

use App;
use Request;
use Config;
use Response;
use Input;
use Auth;
use DB;
use Session;
use Carbon\Carbon;
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
use App\Models\Medcubics\Cpt as Cpt;

use App\Models\Cpt as Cpts;
use App\Models\Favouritecpts as Favouritecpts;

use App\Models\Practice as Practice;
use App\Models\Patients\Patient as Patient;
use App\Models\Employer as Employer;
use App\Models\Icd;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PatientStatementTrack as PatientStatementTrack;
use App\Models\STMTHoldReason as STMTHoldReason;
use App\Models\STMTCategory as STMTCategory;
use Log;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Medcubics\Users as Users;

class ReportApiController extends Controller {

    public function getIndexApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    ############ Adjustment Report Start ############

    public function getAdjustmentApi() {
        /* @$facility = Facility::orderBy('id','ASC')->selectRaw('CONCAT(short_name,"-",facility_name) as facility_data, id')->pluck("facility_data", "id")->all(); */

       $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');

        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('adjustment_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

        // Adjustment_reason
        $adj_reason_ins = AdjustmentReason::where('adjustment_type', 'insurance')->pluck('adjustment_shortname', 'id')->all();
        $adj_reason_patient = AdjustmentReason::where('adjustment_type', 'Patient')->pluck('adjustment_shortname', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities', 'adj_reason_patient', 'adj_reason_ins','searchUserData','search_fields')));
    }

    public function getAdjustmentsearchApi($export = '',$data = '') {

        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        
        $instype = isset($request['insurance_charge'])?$request['insurance_charge']:'';
        
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $rendering_provider_id = $facility_id = $insurance_charge = $adjustment_reason_id = $adjustment_reason_CO45 = $adjustment_reason_code_id = $insurance = $reference = $user_ids = '';
        $practice_timezone = Helpers::getPracticeTimeZone();
        $search_by = [];
        
        if (isset($request['insurance_charge']) && ($request['insurance_charge'] == "all")) {
            unset($request['adjustment_reason_id']);
        }
        if ($export != "") {
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $createdAt = isset($request['select_transaction_date']) ? trim($request['select_transaction_date']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
        }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $createdAt = isset($request['select_date_of_service']) ? trim($request['select_date_of_service']) : "";
            $date = explode('-', $createdAt);
            $dos_start_date = date("Y-m-d", strtotime(@$date[0]));
            $dos_end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['DOS'][] = date("m/d/Y", strtotime($dos_start_date)) . ' to ' . date("m/d/Y", strtotime($dos_end_date));
        }

        //Insurance Type
        if(isset($request['insurance_charge'])){
            $insurance_charge = $request['insurance_charge'];
            if ($request['insurance_charge'] == 'self') {
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id'] != ''){
                    $adjustment_reason_id = $request['adjustment_reason_id'];
                }
                $search_by['Payer'][] = ucwords($request['insurance_charge']);
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                $search_by['Adjustment Reason'][] = DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',explode(',',$request['adjustment_reason_id']))->get()[0]->short_name;
            } elseif ($request['insurance_charge'] == 'insurance') {
                $reason = '';
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                    $exp = explode(',',$request['adjustment_reason_id']);
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                if(in_array('CO45',$exp) && count($exp)==1) {
                    $adjustment_reason_CO45 = $request['adjustment_reason_id'];
                    $search_by['Adjustment Reason'][] = 'CO45';
                }else{
                    if(in_array('CO45',$exp)){
                        $reason = 'CO45, ';
                    }
                    if($exp[0]=='0')
                        $reason .='CO253, ';
                    
                    if(in_array('CO45',$exp)){
                        $exp = array_filter($exp,'is_numeric');
                        $imp = implode(',', $exp);
                        $adjustment_reason_code_id = $imp;
                    }else{
                        $exp = array_filter($exp,'is_numeric');
                        $imp = implode(',', $exp);
                        $adjustment_reason_id = $imp;
                    }

                    $reason .= DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',$exp)->get()[0]->short_name;
                    $search_by['Adjustment Reason'][] = $reason;
                }
                $search_by['Payer'][] = ucwords($request['insurance_charge']);
            }
        }
        

        //Billing Provider
        if (!empty($request["billing_provider_id"])) {
            $billing_provider_id = $request["billing_provider_id"];
            $bill_id = (isset($request['export']) || is_string($request["billing_provider_id"])) ? explode(',',$request["billing_provider_id"]):$request["billing_provider_id"];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id', $bill_id)->get()->toArray();
            $search_by['Billing Provider'][] = @array_flatten($provider)[0];
        }
        
        // Rendering Provider
        if (!empty($request["rendering_provider_id"])) {
            $rendering_provider_id = $request["rendering_provider_id"];
            $ren_id = (isset($request['export']) || is_string($request["rendering_provider_id"])) ? explode(',',$request["rendering_provider_id"]):$request["rendering_provider_id"];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id',$ren_id)->get()->toArray();
            $search_by['Rendering Provider'][] = @array_flatten($provider)[0];
        }
        
        //Facility
        if (!empty($request["facility_id"])) {
            $facility_id = $request["facility_id"];
            $faci_id = (isset($request['export']) || is_string($request["facility_id"])) ? explode(',',$request["facility_id"]):$request["facility_id"];
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ',  ') as short_name")->whereIn('id', $faci_id)->get()->toArray();
            $search_by['Facility'][] = @array_flatten($facility)[0];
        }
        
        // Insurance
        if (!empty($request["insurance_id"])) {
            $insurance = $request["insurance_id"];
            $insurance_name = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            $search_by["Insurance"][] =  @array_flatten($insurance_name)[0];
        }
        if (!empty($request["user"])) {
            $user_ids = $request["user"];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        if (isset($request["reference"]) && !empty($request["reference"])) {
            $reference = $request["reference"];
            $search_by['Reference'][] = $request["reference"];
        }
        $sp_return_result = DB::select('call adjustmentAnalysis("'.$start_date.'","'.$end_date.'", "'.$dos_start_date.'", "'.$dos_end_date.'", "'.$billing_provider_id.'", "'.$rendering_provider_id.'", "'.$facility_id.'", "'.$insurance_charge.'", "'.$adjustment_reason_id.'", "'.$insurance.'", "'.$reference.'", "'.$user_ids.'", "'.$practice_timezone.'")');
        $sp_return_result = (array) $sp_return_result;
        
            $header = $search_by;
            $todte = $end_date;$tot_adjs = [];
            $adjustment = $sp_return_result;
           return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustment', 'startdate', 'todte', 'instype','header','tot_adjs')));
        }
        $query = $this->getAdjustmentResult($request);
        $startdate = $query['startdate'];
        $todte = $query['todate'];
        $header = $query['search_by'];
        $adjustment_id = (isset($request["adjustment_reason_id"]) && $request["adjustment_reason_id"]!='')?$request["adjustment_reason_id"]:'';
        $tot_adjs = [];
        $adjustment = $query['adjustment']->get();
        $exp = [];
        if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
            $exp = explode(',',$request['adjustment_reason_id']);
        if(!empty($adjustment)){
            $adjustments = [];
            foreach($adjustment as $adj){
                $adjustments[$adj->claim_id]['self_pay'] = $adj->self_pay;
                $adjustments[$adj->claim_id]['claim_number'] = $adj->claim_number;
                $adjustments[$adj->claim_id]['title'] = $adj->title;
                $adjustments[$adj->claim_id]['first_name'] = $adj->first_name;
                $adjustments[$adj->claim_id]['middle_name'] = $adj->middle_name;
                $adjustments[$adj->claim_id]['last_name'] = $adj->last_name;
                $adjustments[$adj->claim_id]['account_no'] = $adj->account_no;
                $adjustments[$adj->claim_id]['insurance_id'] = $adj->insurance_id;
                $adjustments[$adj->claim_id]['billing_name'] = $adj->billing_name;
                $adjustments[$adj->claim_id]['billing_provider_name'] = $adj->billing_provider_name;
                $adjustments[$adj->claim_id]['rendering_name'] = $adj->rendering_name;
                $adjustments[$adj->claim_id]['rendering_provider_name'] = $adj->rendering_provider_name;
                $adjustments[$adj->claim_id]['facility_short_name'] = $adj->facility_short_name;
                $adjustments[$adj->claim_id]['facility_name'] = $adj->facility_name;
                $adjustments[$adj->claim_id]['adjustment_type'] = $adj->adjustment_type;
                $adjustments[$adj->claim_id]['short_name'] = $adj->short_name;
                $adjustments[$adj->claim_id]['insurance_name'] = $adj->insurance_name;
                $adjustments[$adj->claim_id]['pmt_method'] = $adj->pmt_method;
                $adjustments[$adj->claim_id]['created_by'] = $adj->created_by;                
                $adjustments[$adj->claim_id]['adj_date'] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                $adjustments[$adj->claim_id]['dos_from'] = $adj->dos_from;
                $adjustments[$adj->claim_id]['cpt_code'] = $adj->cpt_code;
                $adjustments[$adj->claim_id]['adjustment_shortname'] = $adj->short_name;
                $adjustments[$adj->claim_id]['adjustment_amt'] = $adj->tot_adj;
                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['dos_from'] = $adj->dos_from;
                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['cpt_code'] = $adj->cpt_code;

                if($adj->pmt_method=="Insurance"){
                    if(empty(array_filter($exp,'is_numeric')) || in_array('CO45',$exp)) {
                        if($adj->writeoff!=0){
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['insurance'][$adj->id] = $adj->reference;
                            $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id]['writeoff'][$adj->id] = $adj->writeoff;
                            $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id]['writeoff'][$adj->id] = $adj->writeoff;
                            if($adj->short_name!='')
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['insurance']['writeoff'][$adj->id] = $adj->short_name;
                            if($adj->created_at!='')
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['insurance']['writeoff'][$adj->id] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance']['writeoff'][$adj->id] = 'CO45';
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['insurance']['writeoff'][$adj->id] = $adj->writeoff;
                        }
                    }
                }
                if(!in_array('CO45',$exp) || count($exp)>1) 
                if($adj->pmt_method=="Insurance"){
                if(!empty($adj->other_adj)) {
                        foreach($adj->other_adj as $val){
                            if(in_array($val->adjustment_id,$exp) || empty($exp)) {
                                $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id][] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['insurance'][$val->id] = $adj->reference;
                                $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id][$val->id] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['insurance'][$val->id] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['insurance'][$val->id] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['insurance'][$val->id] = $adj->short_name;
                                if($val->adjustment_id==0){
                                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance'][$val->id] = 'CO253';
                                }
                                else{
                                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance'][$val->id] = @$val->adjustment_details->adjustment_shortname;
                                }
                            }
                        }
                    }
                }else{
                    $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['self'][$adj->id] = $adj->reference;
                    $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['self'][$adj->id] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['self'][$adj->id] = $adj->adjustment_shortname;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['self'][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['self'][$adj->id] = 'Self';
                }
            }
        }
        ksort($adjustments);
        if(isset($tot_adjs['Insurance']))
            $tot_adjs['Insurance'] = array_flatten($tot_adjs['Insurance']);
        if(isset($tot_adjs['Patient']))
            $tot_adjs['Patient'] = array_flatten($tot_adjs['Patient']);
        //dd($adjustment);
        if ($export == "") {
            $p = Input::get('page', 1);
            $paginate = 25;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($adjustments, $offSet, $paginate,true);
            $adjustments = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($adjustments), $paginate,$p,['path'=>Request::url()]);
            $report_array = $adjustments->toArray();
            $pagination_prt = $adjustments->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $adjustment = $report_array['data'];
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);

        }

        if ($export != "") {
            $adjustment = $adjustments;
           return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustment', 'startdate', 'todte', 'instype','header','tot_adjs')));
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustment', 'total', 'pagination', 'header', 'startdate', 'todte', 'instype', 'adjustment_id','search_by','tot_adjs')));
    }
    public function getAdjustmentResult($request) {
       $query = PMTClaimCPTTXV1::with('other_adj')->selectRaw("pmt_claim_cpt_tx_v1.id, claim_info_v1.id as claim_id, claim_cpt_info_v1.id as cpt_id, claim_info_v1.self_pay, claim_info_v1.claim_number, patients.title, patients.first_name, patients.middle_name, patients.last_name, patients.account_no, claim_info_v1.insurance_id, billing.short_name as billing_name, billing.provider_name as billing_provider_name, render.short_name as rendering_name, render.provider_name as rendering_provider_name, facilities.short_name as facility_short_name, facilities.facility_name as facility_name, pmt_claim_tx_v1.pmt_method as adjustment_type, insurances.short_name, pmt_claim_cpt_tx_v1.created_at, (pmt_claim_cpt_tx_v1.withheld+pmt_claim_cpt_tx_v1.writeoff) as tot_adj, claim_cpt_info_v1.dos_from, claim_cpt_info_v1.cpt_code,  pmt_claim_tx_v1.created_by, pmt_claim_tx_v1.pmt_method, pmt_info_v1.reference, adjustment_reasons.adjustment_shortname, pmt_claim_cpt_tx_v1.writeoff, pmt_claim_cpt_tx_v1.withheld")
                ->leftjoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_cpt_tx_v1.payment_id')
                ->leftjoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_cpt_tx_v1.claim_id')
                ->leftjoin('claim_cpt_info_v1','claim_cpt_info_v1.id','=','pmt_claim_cpt_tx_v1.claim_cpt_info_id')
                ->leftjoin('pmt_claim_tx_v1','pmt_claim_tx_v1.id','=','pmt_claim_cpt_tx_v1.pmt_claim_tx_id')
                ->leftjoin('pmt_adj_info_v1','pmt_adj_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftjoin('adjustment_reasons','adjustment_reasons.id','=','pmt_adj_info_v1.adj_reason_id')
                ->leftjoin('patients','patients.id','=','pmt_claim_tx_v1.patient_id')
                ->leftjoin('insurances','insurances.id','=','pmt_claim_tx_v1.payer_insurance_id')
                ->leftjoin('providers as billing','billing.id','=','claim_info_v1.billing_provider_id')
                ->leftjoin('providers as render','render.id','=','claim_info_v1.rendering_provider_id')
                ->leftjoin('facilities','facilities.id','=','claim_info_v1.facility_id')
                ->whereIn('pmt_claim_tx_v1.pmt_method', ['Patient', 'Insurance'])
                ->whereRaw("(pmt_claim_cpt_tx_v1.writeoff+pmt_claim_cpt_tx_v1.withheld) != 0")
                ->whereRaw('pmt_claim_cpt_tx_v1.claim_cpt_info_id != 0'); 
        $search_by = [];
        $start_date = $end_date ='';
        
        if (isset($request['insurance_charge']) && ($request['insurance_charge'] == "all")) {
            unset($request['adjustment_reason_id']);
        }

        $practice_timezone = Helpers::getPracticeTimeZone();  
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $createdAt = isset($request['select_transaction_date']) ? trim($request['select_transaction_date']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            $query->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
        }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $createdAt = isset($request['select_date_of_service']) ? trim($request['select_date_of_service']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['DOS'][] = date("m/d/Y", strtotime(@$start_date)) . ' to ' . date("m/d/Y", strtotime(@$end_date));
            $query->whereRaw("DATE(claim_info_v1.date_of_service) >= '$start_date' and DATE(claim_info_v1.date_of_service) <= '$end_date'");
        }
//dd($request);
        //Insurance Type
        if(isset($request['insurance_charge']))
        if ($request['insurance_charge'] == 'self') {
            $query->where('pmt_claim_tx_v1.pmt_method', '=', 'Patient');
            if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id'] != ''){
                 $query->whereIn('pmt_adj_info_v1.adj_reason_id', explode(',',$request['adjustment_reason_id']));
            }
            $search_by['Payer'][] = ucwords($request['insurance_charge']);
            if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
            $search_by['Adjustment Reason'][] = DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',explode(',',$request['adjustment_reason_id']))->get()[0]->short_name;
        } elseif ($request['insurance_charge'] == 'insurance') {
            $reason = '';
            $query->where('pmt_claim_tx_v1.pmt_method', '=', 'Insurance');
            if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                $exp = explode(',',$request['adjustment_reason_id']);
            if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
            if(in_array('CO45',$exp) && count($exp)==1) {
                $query->where('pmt_claim_tx_v1.total_writeoff', '!=', 0);
                $search_by['Adjustment Reason'][] = 'CO45';
            }else{
                if(in_array('CO45',$exp)){
                    $reason = 'CO45, ';
                }
                if($exp[0]=='0')
                    $reason .='CO253, ';
                if(in_array('CO45',$exp)){
                    $exp = array_filter($exp,'is_numeric');
                    $imp = implode(',', $exp);
                    $query->whereRaw('(pmt_claim_cpt_tx_v1.writeoff != 0)');
                }else{
                    $exp = array_filter($exp,'is_numeric');
                    $query->whereHas('other_adj', function($q)use($exp){
                        $q->whereIn('claim_cpt_others_adjustment_info_v1.adjustment_id', $exp);
                    });
                }
                $reason .= DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',$exp)->get()[0]->short_name;
                $search_by['Adjustment Reason'][] = $reason;
            }
            $search_by['Payer'][] = ucwords($request['insurance_charge']);
        }

        //Billing Provider
        if (!empty($request["billing_provider_id"])) {
            // Request is string or array based on condition added
            // Rev. 1 Ref: MR-2752 - 28-Aug-2019 Anjukaselvan
            $bill_id = (isset($request['export']) || is_string($request["billing_provider_id"])) ? explode(',',$request["billing_provider_id"]):$request["billing_provider_id"];
            if(is_array($bill_id))
                $query->whereIn('claim_info_v1.billing_provider_id', $bill_id);
            else 
                $query->where('claim_info_v1.billing_provider_id', $bill_id);
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id', $bill_id)->get()->toArray();
            $search_by['Billing Provider'][] = @array_flatten($provider)[0];
        }
        
        // Rendering Provider
        if (!empty($request["rendering_provider_id"])) {
           $ren_id = (isset($request['export']) || is_string($request["rendering_provider_id"])) ? explode(',',$request["rendering_provider_id"]):$request["rendering_provider_id"];
            if(is_array($ren_id))
                $query->whereIn('claim_info_v1.rendering_provider_id', $ren_id);
            else 
                $query->where('claim_info_v1.rendering_provider_id', $ren_id);
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id',$ren_id)->get()->toArray();
            $search_by['Rendering Provider'][] = @array_flatten($provider)[0];
        }
        
        //Facility
        if (!empty($request["facility_id"])) {
            $faci_id = (isset($request['export']) || is_string($request["facility_id"])) ? explode(',',$request["facility_id"]):$request["facility_id"];
            if(is_array($faci_id))
                $query->whereIn('claim_info_v1.facility_id', $faci_id);
            else 
                $query->where('claim_info_v1.facility_id', $faci_id);
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ',  ') as short_name")->whereIn('id', $faci_id)->get()->toArray();
            $search_by['Facility'][] = @array_flatten($facility)[0];
        }
        
        // Insurance
        if (!empty($request["insurance_id"])) {
            $insurance_id = $request["insurance_id"];
			if(is_array($insurance_id)){
				$query->whereIn('pmt_claim_tx_v1.payer_insurance_id', $insurance_id);
				$insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', $insurance_id)->get()->toArray();
			}else{
				$query->whereIn('pmt_claim_tx_v1.payer_insurance_id', explode(',',$insurance_id));
				$insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
			}
            
            $search_by["Insurance"][] =  @array_flatten($insurance)[0];
        }
        if (!empty($request["user"])) {
            $query->whereIn('pmt_claim_cpt_tx_v1.created_by', explode(',',$request["user"]));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        if (isset($request["reference"]) && !empty($request["reference"])) {
            $query->where('pmt_info_v1.reference', 'like', $request["reference"]);
            $search_by['Reference'][] = $request["reference"];
        }

        $result['adjustment'] = $query->orderBy('claim_info_v1.id','asc');
        $result['startdate'] = $start_date;
        $result['todate'] = $end_date;
        $result['search_by'] = $search_by;
        return $result;
    }
    
    /* Stored Procedure for Adjustment analysis */
    public function getAdjustmentsearchApiSP($export = '',$data = '') {
        if (isset($datas) && !empty($datas))
            $request = $datas;
        else
            $request = Request::All();
        
        $start_date = $end_date = $dos_start_date = $dos_end_date = $billing_provider_id = $rendering_provider_id = $facility_id = $insurance_charge = $adjustment_reason_id = $insurance = $reference = $user_ids = '';
        $practice_timezone = Helpers::getPracticeTimeZone();
        $search_by = [];
        
        if (isset($request['insurance_charge']) && ($request['insurance_charge'] == "all")) {
            unset($request['adjustment_reason_id']);
        }
        
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $createdAt = isset($request['select_transaction_date']) ? trim($request['select_transaction_date']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
        }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $createdAt = isset($request['select_date_of_service']) ? trim($request['select_date_of_service']) : "";
            $date = explode('-', $createdAt);
            $dos_start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($dos_start_date == '1970-01-01') {
                $dos_start_date = '0000-00-00';
            }
            $dos_end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['DOS'][] = date("m/d/Y", strtotime($dos_start_date)) . ' to ' . date("m/d/Y", strtotime($dos_end_date));
        }

        //Insurance Type
        if(isset($request['insurance_charge'])){
            $insurance_charge = $request['insurance_charge'];
            if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id'] != ''){
                $adjustment_reason_id = $request['adjustment_reason_id'];
            }
            if ($request['insurance_charge'] == 'self') {
                $search_by['Payer'][] = ucwords($request['insurance_charge']);
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                $search_by['Adjustment Reason'][] = DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',explode(',',$request['adjustment_reason_id']))->get()[0]->short_name;
            } elseif ($request['insurance_charge'] == 'insurance') {
                $reason = '';
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                    $exp = explode(',',$request['adjustment_reason_id']);
                if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
                if(in_array('CO45',$exp) && count($exp)==1) {
                    $search_by['Adjustment Reason'][] = 'CO45';
                }else{
                    if(in_array('CO45',$exp)){
                        $reason = 'CO45, ';
                    }
                    if($exp[0]=='0')
                        $reason .='CO253, ';
                    
                    if(in_array('CO45',$exp)){
                        $exp = array_filter($exp,'is_numeric');
                    }else{
                        $exp = array_filter($exp,'is_numeric');
                    }

                    $reason .= DB::table('adjustment_reasons')->selectRaw("GROUP_CONCAT(adjustment_shortname SEPARATOR ', ') as short_name")->whereIn('id',$exp)->get()[0]->short_name;
                    $search_by['Adjustment Reason'][] = $reason;
                }
                $search_by['Payer'][] = ucwords($request['insurance_charge']);
            }
        }
        

        //Billing Provider
        if (!empty($request["billing_provider_id"])) {
            $billing_provider_id = $request["billing_provider_id"];
            $bill_id = (isset($request['export']) || is_string($request["billing_provider_id"])) ? explode(',',$request["billing_provider_id"]):$request["billing_provider_id"];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id', $bill_id)->get()->toArray();
            $search_by['Billing Provider'][] = @array_flatten($provider)[0];
        }
        
        // Rendering Provider
        if (!empty($request["rendering_provider_id"])) {
            $rendering_provider_id = $request["rendering_provider_id"];
            $ren_id = (isset($request['export']) || is_string($request["rendering_provider_id"])) ? explode(',',$request["rendering_provider_id"]):$request["rendering_provider_id"];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id',$ren_id)->get()->toArray();
            $search_by['Rendering Provider'][] = @array_flatten($provider)[0];
        }
        
        //Facility
        if (!empty($request["facility_id"])) {
            $facility_id = $request["facility_id"];
            $faci_id = (isset($request['export']) || is_string($request["facility_id"])) ? explode(',',$request["facility_id"]):$request["facility_id"];
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ',  ') as short_name")->whereIn('id', $faci_id)->get()->toArray();
            $search_by['Facility'][] = @array_flatten($facility)[0];
        }
        
        // Insurance
        if (!empty($request["insurance_id"])) {
            $insurance = $request["insurance_id"];
            $insurance_name = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            $search_by["Insurance"][] =  @array_flatten($insurance_name)[0];
        }
        if (!empty($request["user"])) {
            $user_ids = $request["user"];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        if (isset($request["reference"]) && !empty($request["reference"])) {
            $reference = $request["reference"];
            $search_by['Reference'][] = $request["reference"];
        }
        $sp_return_result = DB::select('call adjustmentAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $billing_provider_id . '", "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '", "' . $adjustment_reason_id . '", "' . $insurance . '", "' . $reference . '", "' . $user_ids . '", "' . $practice_timezone . '")');
        $sp_return_result = (array) $sp_return_result;
        $header = $search_by;
        $tot_adjs = [];
        $instype = isset($request['insurance_charge'])?$request['insurance_charge']:'';
        $startdate = $start_date;
        $todte = $end_date;
        $adjustment = $sp_return_result;
        if ($export != "") {
            //$adjustment = $adjustments;
           return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustment', 'startdate', 'todte', 'instype','header','tot_adjs')));
        }
        
        $exp = [];
        if(isset($request['adjustment_reason_id']) && $request['adjustment_reason_id']!='')
            $exp = explode(',',$request['adjustment_reason_id']);
        if(!empty($adjustment)){
            $adjustments = [];
            foreach($adjustment as $adj){
                $adjustments[$adj->claim_id]['self_pay'] = $adj->self_pay;
                $adjustments[$adj->claim_id]['claim_number'] = $adj->claim_number;
                $adjustments[$adj->claim_id]['patient_name'] = $adj->patient_name;
                $adjustments[$adj->claim_id]['account_no'] = $adj->account_no;
                $adjustments[$adj->claim_id]['insurance_id'] = $adj->insurance_id;
                $adjustments[$adj->claim_id]['billing_name'] = $adj->billing_name;
                $adjustments[$adj->claim_id]['billing_provider_name'] = $adj->billing_provider_name;
                $adjustments[$adj->claim_id]['rendering_name'] = $adj->rendering_name;
                $adjustments[$adj->claim_id]['rendering_provider_name'] = $adj->rendering_provider_name;
                $adjustments[$adj->claim_id]['facility_short_name'] = $adj->facility_short_name;
                $adjustments[$adj->claim_id]['facility_name'] = $adj->facility_name;
                $adjustments[$adj->claim_id]['adjustment_type'] = $adj->adjustment_type;
                $adjustments[$adj->claim_id]['insurance_short_name'] = $adj->insurance_short_name;
                $adjustments[$adj->claim_id]['insurance_name'] = $adj->insurance_name;
                $adjustments[$adj->claim_id]['pmt_method'] = $adj->pmt_method;
                $adjustments[$adj->claim_id]['created_by'] = $adj->created_by;                
                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['dos_from'] = $adj->dos_from;
                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['cpt_code'] = $adj->cpt_code;
                
                if($adj->pmt_method=="Insurance"){
                    if(empty(array_filter($exp,'is_numeric')) || in_array('CO45',$exp)) {
                        if($adj->writeoff!=0){
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['insurance'][$adj->id] = $adj->reference;
                            $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id]['writeoff'][$adj->id] = $adj->writeoff;
                            $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id]['writeoff'][$adj->id] = $adj->writeoff;
                            if($adj->insurance_short_name!='')
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['insurance']['writeoff'][$adj->id] = $adj->insurance_short_name;
                            if($adj->created_at!='')
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['insurance']['writeoff'][$adj->id] = $adj->created_at;
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance']['writeoff'][$adj->id] = 'CO45';
                            $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['insurance']['writeoff'][$adj->id] = $adj->writeoff;
                        }
                    }
                }
                if(!in_array('CO45',$exp) || count($exp)>1) 
                if($adj->pmt_method=="Insurance"){
                if(!empty($adj->other_adj)) {
                        foreach($adj->other_adj as $val){
                            if(in_array($val->adjustment_id,$exp) || empty($exp)) {
                                $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id][] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['insurance'][$val->id] = $adj->reference;
                                $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id][$val->id] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['insurance'][$val->id] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['insurance'][$val->id] = $val->adjustment_amt;
                                $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['insurance'][$val->id] = $adj->short_name;
                                if($val->adjustment_id==0)
                                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance'][$val->id] = 'CO253';
                                else
                                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['insurance'][$val->id] = $val->adjustment_details->adjustment_shortname;
                            }
                        }
                    }
                }else{
                    $tot_adjs[$adj->pmt_method][$adj->claim_id][$adj->cpt_id][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['reference']['self'][$adj->id] = $adj->reference;
                    $adjustments[$adj->claim_id]['tot_adj'][$adj->claim_id][$adj->cpt_id][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_date']['self'][$adj->id] = App\Http\Helpers\Helpers::timezone($adj->created_at, 'm/d/y');
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_reason']['self'][$adj->id] = $adj->adjustment_shortname;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['adj_amt']['self'][$adj->id] = $adj->writeoff;
                    $adjustments[$adj->claim_id]['cpt'][$adj->cpt_id]['payer']['self'][$adj->id] = 'Self';
                }
            }
        }
        ksort($adjustments);
        if(isset($tot_adjs['Insurance']))
            $tot_adjs['Insurance'] = array_flatten($tot_adjs['Insurance']);
        if(isset($tot_adjs['Patient']))
            $tot_adjs['Patient'] = array_flatten($tot_adjs['Patient']);
                
        if ($export == "") {
            $p = Input::get('page', 1);
            $paginate = 25;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($adjustments, $offSet, $paginate,true);
            $adjustments = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($adjustments), $paginate,$p,['path'=>Request::url()]);
            $report_array = $adjustments->toArray();
            $pagination_prt = $adjustments->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $adjustment = $report_array['data'];
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);

        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adjustment', 'total', 'pagination', 'header', 'startdate', 'todte', 'instype', 'adjustment_id','search_by','tot_adjs')));
    }
    
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        //return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    

    public function getAdjustmentExport($adjustment) {
        $total_charge_count = 0;
        $get_list = [];
        $result = [];
        // @todo check and replace new pmt flow
        /*
        foreach ($adjustment as $key => $value) {
            @$trans_details = PaymentClaimCtpDetail::getClaimCptDetail(@$value->claims->id);
            foreach ($trans_details as $dos_key => $dos_value) {
                $adj = @$dos_value->adjustment + @$dos_value->with_held;
                if (@$adj != '0') {
                    $get_arr = [];
                    $get_arr['created_at'] = Helpers::dateFormat(@$value->created_at);
                    $get_arr['claim_number'] = @$value->claims->claim_number;
                    @$get_arr['dos_from'] = @$dos_value->dosdetails->dos_from;
                    @$get_arr['dos_to'] = @$dos_value->dosdetails->dos_to;
                    $get_arr['account_no'] = @$value->patient->account_no;
                    $get_arr['patient_name'] = @$value->patient->last_name . " " . @$value->patient->first_name;
                    $get_arr['billing_provider_name'] = @$value->claims->billing_provider->short_name;
                    $get_arr['rendering_provider_name'] = @$value->claims->rendering_provider->short_name;
                    $get_arr['facility_detail_name'] = @$value->claims->facility_detail->short_name;
                    $get_arr['responsibility'] = (empty($value->insurancedetail)) ? "Self" : @$value->insurancedetail->insurance_name;
                    $get_arr['cpt_codes'] = @$dos_value->dosdetails->cpt_code;
                    $get_arr['adjustment'] = Helpers::priceFormat(@$dos_value->adjustment + @$dos_value->with_held, 'export');
                    $get_arr['created_by'] = @$value->user->name;
                    $get_list[$total_charge_count] = $get_arr;
                    $total_charge_count++;
                }
            }
            $payment_claim_detail_id[$key] = $value->id;
            $payment_id[$key] = $value->payment_id;
            $get_result = $get_list;
        }
        $total = PaymentClaimCtpDetail::TotalTransfer($payment_claim_detail_id, $payment_id);
        $get_result[$total_charge_count] = ['created_at' => '', 'claim_number' => '', 'dos_from' => '', 'dos_to' => '', 'account_no' => '', 'patient_name' => '', 'billing_provider_name' => '', 'rendering_provider_name' => '', 'facility_detail_name' => '', 'responsibility' => '', 'cpt_codes' => 'Total Adj : ' . Helpers::priceFormat($total['adjusted'], 'export'), 'adjustment' => 'Insurance Adj : ' . Helpers::priceFormat($total['ins_adjusted'], 'export'), 'created_by' => 'Patient Adj : ' . Helpers::priceFormat($total['pat_adjusted'], 'export')];

        $result["value"] = json_decode(json_encode($get_result));
        $result["exportparam"] = array(
            'filename' => 'Adjustment Report',
            'heading' => '',
            'fields' => array(
                'created_at' => 'Adjustment Date',
                'dos_from' => 'Dos from',
                'dos_to' => 'Dos to',
                'account_no' => 'Act No',
                'patient_name' => 'Patient Name',
                'billing_provider_name' => 'Billing Provider',
                'rendering_provider_name' => 'Rendering Provider',
                'facility_detail_name' => 'Facility',
                'responsibility' => 'Responsibility',
                'cpt_codes' => 'CPT Codes',
                'adjustment' => 'Adjustment',
                'created_by' => 'User',
            )
        );
        */
        return $result;
    }

    ############ Adjustment Report End ############
    ############ Aging Report Start ############
    ##### PROCEDURE REPORT Function start #####
      public function getprocedurelistApi() {
        /* @$facility = Facility::orderBy('id','ASC')->selectRaw('CONCAT(short_name,"-",facility_name) as facility_data, id')->pluck("facility_data", "id")->all(); */

        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('procedurereport');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];


        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData','search_fields')));
    }

    public function getproceduresearchApi($export = '', $data = '') {
         if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();//dd($request);
        $search_by = array();
        $practice_timezone = Helpers::getPracticeTimeZone();
        $cpt_list = ClaimCPTInfoV1::selectRaw('claim_cpt_info_v1.id as idn,claim_cpt_info_v1.cpt_code,claim_cpt_info_v1.patient_id,CONVERT_TZ(claim_cpt_info_v1.created_at,"UTC","'.$practice_timezone.'") as charge_date,claim_cpt_info_v1.claim_id,DATE(claim_cpt_info_v1.dos_from) as date_of_service,claim_cpt_info_v1.charge,pmt_claim_cpt_tx_v1.paid as Payment_amount,CONVERT_TZ(pmt_claim_cpt_tx_v1.created_at,"UTC","'.$practice_timezone.'") as payment_date,pmt_claim_tx_v1.payer_insurance_id,pmt_info_v1.void_check,insurancetypes.type_name,pmt_claim_tx_v1.total_allowed as allowed,pmt_claim_cpt_tx_v1.claim_cpt_info_id')
        ->with('claim_details')
        ->with('patient_details')
        ->leftjoin("claim_info_v1",'claim_info_v1.id','=','claim_cpt_info_v1.claim_id')
        ->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.claim_id','=','claim_cpt_info_v1.claim_id')
        ->leftjoin("pmt_claim_cpt_tx_v1",'pmt_claim_cpt_tx_v1.pmt_claim_tx_id','=','pmt_claim_tx_v1.id')
        ->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
        ->leftJoin("insurances",'insurances.id','=','pmt_claim_tx_v1.payer_insurance_id')
        ->leftJoin('insurancetypes','insurancetypes.id','=','insurances.insurancetype_id')
        ->wherenull('claim_cpt_info_v1.deleted_at')
        ->wherenull('pmt_claim_cpt_tx_v1.deleted_at')
        //->wherenull('pmt_info_v1.void_check')
        ->where('pmt_claim_cpt_tx_v1.paid','!=',0)
        ->where('pmt_claim_tx_v1.pmt_method','Insurance')
        ->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment']);

       // ->groupBy('pmt_claim_tx_v1.payer_insurance_id')
     //   ->groupBy(DB::raw('DATE(pmt_claim_cpt_tx_v1.created_at)'))
      //  ->groupBy('claim_cpt_info_v1.claim_id')
     //   ->groupBy('claim_cpt_info_v1.cpt_code');
      //   $cpt_list->where('pmt_claim_cpt_tx_v1.paid')
      // dd($cpt_list->get()->toArray());
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $date = explode('-',$request['select_transaction_date']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Transaction Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
            else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
          //  $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
         //   $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
           // $cpt_list->where(DB::raw('(pmt_claim_cpt_tx_v1.created_at)'), '>=', $start_date)->where(DB::raw('(pmt_claim_cpt_tx_v1.created_at)'), '<=', $end_date);
            $cpt_list->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'"); 
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
            $cpt_list->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '>=', $start_date)->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '<=', $end_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['DOS']= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
            }
            else{
                $search_by['DOS'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
            }
        }
       //  $search_by['Payment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        if(!empty($request['rendering_provider_id'])){
            $renders_id = explode(',',$request['rendering_provider_id']);
             $cpt_list->whereHas('claim_details', function($q) use($renders_id) {
                  $q->whereIn('rendering_provider_id',$renders_id);
                });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Rendering Provider'] = $search_render;
            }
            else{
                $search_by['Rendering Provider'][] = $search_render;
            }
        }
        if(!empty($request['acc_no'])){
            $acc_no =$request['acc_no'];
             $cpt_list->whereHas('patient_details', function($q) use($acc_no) {
                  $q->where('account_no',$acc_no);
                });
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Acc No'] = $acc_no;
            }
            else{
                $search_by['Acc No'][] = $acc_no;
            }
        }
        if(!empty($request['acc_no'])){
            $acc_no =$request['acc_no'];
             $cpt_list->whereHas('patient_details', function($q) use($acc_no) {
                  $q->where('account_no',$acc_no);
                });
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Acc No'] = $acc_no;
            }
            else{
                $search_by['Acc No'][] = $acc_no;   
            }
        }
        if(isset($request['insurance_category']) && !empty($request['insurance_category']) && $request['insurance_category']!='All'){
            $insurance_category =$request['insurance_category'];
            $cpt_list->where('insurancetypes.code',$insurance_category);
            $search_by['Insurance Group By'][] = DB::table('insurancetypes')->where('code',$insurance_category)->pluck('type_name')->first();
        }else{
            $search_by['Insurance Group By'][] = 'All';
        }
        if(!empty($request['insurance'])){
                $insurances = explode(',',$request['insurance']);
                $cpt_list->whereIn('pmt_claim_tx_v1.payer_insurance_id',$insurances);
            if (strpos($request['insurance'], ',') !== false) {
               $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance']))->get()->toArray();
            } else {
               $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance'])->get()->toArray();
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by["Insurance"] =  @array_flatten($insurance)[0];
            }
            else{
                $search_by["Insurance"][] =  @array_flatten($insurance)[0];
            }
        }
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['CPT/HCPCS Type'] = 'Custom Range';
            }
            else{
                $search_by['CPT/HCPCS Type'][] = 'Custom Range';
            }

            if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                $cpts_list = Helpers::CptsRangeBetween($request['custom_type_from'], $request['custom_type_to']);                              
               //$cpt_list->where('claim_cpt_info_v1.cpt_code','>=',$request['custom_type_from'])->where('claim_cpt_info_v1.cpt_code','<=',$request['custom_type_to']);
                $cpt_list->whereIn('claim_cpt_info_v1.cpt_code', $cpts_list);
            }
        }
        if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['CPT/HCPCS Type'] = 'CPT/HCPCS Code';
            }
            else{
                $search_by['CPT/HCPCS Type'][] = 'CPT/HCPCS Code';
            }
            if(!empty($request['cpt_code_id'])){
                // Search comma separated cpt codes
                $cptCodes = array_map('trim', explode(',', $request['cpt_code_id']));
                $cpt_list->Where(function ($q) use ($cptCodes) {
                    foreach ($cptCodes as $key => $dc) {
                        if($key == 0)
                            $q->where('claim_cpt_info_v1.cpt_code', $dc);
                       else
                            $q->orWhere('claim_cpt_info_v1.cpt_code', $dc);
                    }
                });
                //$cpt_list->where('claim_cpt_info_v1.cpt_code',$request['cpt_code_id']);
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['CPT/HCPCS Code'] = $request['cpt_code_id'];
                }
                else{
                    $search_by['CPT/HCPCS Code'][] = $request['cpt_code_id'];
                }
            }
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'CPT'){
         $cpt_list->orderBy('claim_cpt_info_v1.cpt_code', $request['sort_by_order']);
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'DOS'){
         $cpt_list->orderBy(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), $request['sort_by_order']);
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'payment_date'){
         $cpt_list->orderBy('pmt_claim_cpt_tx_v1.created_at', $request['sort_by_order']);
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'charge_date'){
         $cpt_list->orderBy('claim_cpt_info_v1.created_at', $request['sort_by_order']);
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'Insurance'){
         $cpt_list->orderBy('pmt_claim_tx_v1.payer_insurance_id', $request['sort_by_order']);
        }
        if(!empty($request['sort_by']) && $request['sort_by'] == 'CPT'){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Sort By'] = 'CPT/HCPCS';
            }
            else{
                $search_by['Sort By'][] = 'CPT/HCPCS';
            }
        }else{
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Sort By'] = ucwords(str_replace('_', ' ', $request['sort_by']));
            }
            else{
                $search_by['Sort By'][] = ucwords(str_replace('_', ' ', $request['sort_by']));
            }
        }
        if (isset($request['exports']) && $request['exports'] == 'pdf'){
            $search_by['Sort By Order'] = $request['sort_by_order'];
        }
        else{
            $search_by['Sort By Order'][] = $request['sort_by_order'];
        }
        //  dd($cpt_list->get()->toArray());
         $cpt_list->orWhere(function($qry)
            use ($request,$practice_timezone)
            {
                $qry->whereIn('pmt_claim_tx_v1.pmt_method',['Insurance'])
                    ->whereNotIn('pmt_claim_tx_v1.pmt_type', ['Refund','Adjustment']);
                    $qry->where('pmt_claim_cpt_tx_v1.paid','<',0);
             if(isset($request['choose_date']) && !empty($request['choose_date']) &&
                        ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
                    if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                        $date = explode('-',$request['select_transaction_date']);
                        $start_date = date("Y-m-d", strtotime($date[0]));
                        if($start_date == '1970-01-01'){
                            $start_date = '0000-00-00';
                        }
                        $end_date = date("Y-m-d", strtotime($date[1]));
                        $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'"); 
                    }
                   
                 //   dd($qry->get()->toArray());
                    if(isset($request['choose_date']) && !empty($request['choose_date']) &&
                        ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
                    if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
                        $date = explode('-',$request['select_date_of_service']);
                        $start_date = date("Y-m-d", strtotime($date[0]));
                        if($start_date == '1970-01-01'){
                            $start_date = '0000-00-00';
                        }
                        $end_date = date("Y-m-d", strtotime($date[1]));
                        $qry->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '>=', $start_date)->where(DB::raw('DATE(claim_cpt_info_v1.dos_from)'), '<=', $end_date);
                    }
                    if(!empty($request['rendering_provider_id'])){
                        $renders_id = explode(',',$request['rendering_provider_id']);
                         $qry->whereHas('claim_details', function($q) use($renders_id) {
                              $q->whereIn('rendering_provider_id',$renders_id);
                            });
                    }
                    if(!empty($request['acc_no'])){
                        $acc_no =$request['acc_no'];
                         $qry->whereHas('patient_details', function($q) use($acc_no) {
                              $q->where('account_no',$acc_no);
                            });
                    }
                    if(isset($request['insurance_category']) && !empty($request['insurance_category']) && $request['insurance_category']!='All'){
                        $insurance_category =$request['insurance_category'];
                        $qry->where('insurancetypes.code',$insurance_category);
                    }
                    if(!empty($request['insurance'])){
                            $insurances = explode(',',$request['insurance']);
                            $qry->whereIn('pmt_claim_tx_v1.payer_insurance_id',$insurances);
                    }
                    if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){

                        if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                            $cpts_list = Helpers::CptsRangeBetween($request['custom_type_from'], $request['custom_type_to']);                              
                            $qry->whereIn('claim_cpt_info_v1.cpt_code', $cpts_list);
                        }
                    }
                    if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
                        if(!empty($request['cpt_code_id'])){
                            // Search comma separated cpt codes
                            $cptCodes = array_map('trim', explode(',', $request['cpt_code_id']));
                            $qry->Where(function ($q) use ($cptCodes) {
                                foreach ($cptCodes as $key => $dc) {
                                    if($key == 0)
                                        $q->where('claim_cpt_info_v1.cpt_code', $dc);
                                   else
                                        $q->orWhere('claim_cpt_info_v1.cpt_code', $dc);
                                }
                            });
                        }
                    }
            });
    $cpt_list->groupby('pmt_claim_cpt_tx_v1.id');

        if (isset($request['exports']) && $request['exports'] == 'pdf'){
            $cptreport_list = $cpt_list->get()->toArray();
            $cptreport_list =  $this->getCPTSearchApi($cptreport_list,'download');
        } elseif (isset($request['export']) && $request['export'] == 'xlsx'){
            $cptreport_list = $cpt_list->get()->toArray();
            $cptreport_list =  $this->getCPTSearchApi($cptreport_list,'download');
        }
        else{
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $cpt_list = $cpt_list->paginate($paginate_count);
            // Get export result
            $ref_array = $cpt_list->toArray();

            $pagination_prt = $cpt_list->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $cpt_list = json_decode($cpt_list->toJson());
            $cptreport_list =($cpt_list->data);
            $cptreport_list =  $this->getCPTSearchApi($cpt_list->data,Null);
        } 

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cptreport_list','pagination','pagination_prt','search_by')));

    }

    /* CPT value take Start here */
    public function getCPTSearchApi($data,$export=''){
          
            for($i= 0 ;$i< count($data); $i++)
            {              
               // dd($data[$i]['claim_cpt_info_id']);
                if ($export == 'download')
                {
                    $cpt_id = @$data[$i]['claim_cpt_info_id']; 
                    $patient_id=@$data[$i]['patient_id'];
                    $claim_id = @$data[$i]['claim_id'];      
                    if((isset($cpt_id)) && (isset($patient_id)) && (isset($claim_id)))           
                    {                       
                        $cpt_list = ClaimCPTInfoV1::where('patient_id',$patient_id)->where('claim_id',$claim_id)->where('id',$cpt_id)->first()->cpt_code;                
                        if(isset($cpt_list))
                            $data[$i]['cpt_code'] = $cpt_list;                
                    }
                }
                else{
                    $cpt_id = @$data[$i]->claim_cpt_info_id; 
                    $patient_id=@$data[$i]->patient_id;
                    $claim_id = @$data[$i]->claim_id;       
                    if((isset($cpt_id)) && (isset($patient_id)) && (isset($claim_id)))           
                    {   
                        $cpt_list = ClaimCPTInfoV1::where('patient_id',$patient_id)->where('claim_id',$claim_id)->where('id',$cpt_id)->first()->cpt_code;                
                        if(isset($cpt_list))
                            $data[$i]->cpt_code = $cpt_list;                
                    }    
                }                       
            }
           return $data;
    }
    /* CPT value take here END */
    /* Stored procedure for procedure collection - Anjukaselvan*/
    public function getproceduresearchApiSP($export = '', $data = '') {
            if(isset($data) && !empty($data))
                $request = $data;
            else
                $request = Request::All();
            $practice_timezone = Helpers::getPracticeTimeZone();
            $start_date = $end_date = $dos_start_date =  $dos_end_date = $rendering_provider_id = $acc_no = $cpt_code_id = $cpt_custom_type_from = $cpt_custom_type_to = $insurance = $insurance_category = $SortBy = $SortByOrder =  '';
            $search_by = array();

            if(isset($request['choose_date']) && !empty($request['choose_date']) &&
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
                $date = explode('-',$request['select_transaction_date']);
                $start_date = date("Y-m-d", strtotime($date[0]));
                if($start_date == '1970-01-01'){
                    $start_date = '0000-00-00';
                }
                $end_date = date("Y-m-d", strtotime($date[1]));
//                $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
//                $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
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
                $renders_id = explode(',',$request['rendering_provider_id']);
                foreach ($renders_id as $id) {
                    $value_name[] = App\Models\Provider::getProviderFullName($id);
                }
                $search_render = implode(", ", array_unique($value_name));
                $search_by['Rendering Provider'][] = $search_render;
            }
            if(!empty($request['acc_no'])){
                $acc_no =$request['acc_no'];
                $search_by['Acc No'][] = $acc_no;
            }
            if(isset($request['insurance_category']) && !empty($request['insurance_category']) && $request['insurance_category']!='All'){
                $insurance_category = $request['insurance_category'];
                $search_by['Insurance Group By'][] = DB::table('insurancetypes')->where('code',$insurance_category)->pluck('type_name')->first();
            }else{
                $search_by['Insurance Group By'][] = 'All';
            }
            if(!empty($request['insurance'])){
                $insurance = $request['insurance'];
                if (strpos($request['insurance'], ',') !== false) {
                    $insurances = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance']))->get()->toArray();
                } else {
                    $insurances = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance'])->get()->toArray();
                }
                $search_by["Insurance"][] =  @array_flatten($insurances)[0];
            }
            if(!empty($request['cpt_type']) && $request['cpt_type'] == 'custom_type'){
                $search_by['CPT/HCPCS Type'][] = 'Custom Range';
                if(!empty($request['custom_type_from']) && !empty($request['custom_type_to'])){
                    $cpt_custom_type_from = $request['custom_type_from'];
                    $cpt_custom_type_to = $request['custom_type_to'];
                }
            }
            if(!empty($request['cpt_type']) && $request['cpt_type'] == 'cpt_code'){
                $search_by['CPT/HCPCS Type'][] = 'CPT/HCPCS Code';
                if(!empty($request['cpt_code_id'])){
                    $cpt_code_id = $request['cpt_code_id'];
                    $search_by['CPT/HCPCS Code'][] = $request['cpt_code_id'];
                }
            }
            
            if(!empty($request['sort_by'])){
                $SortBy = $request['sort_by'];
                if(!empty($request['sort_by']) && $request['sort_by'] == 'CPT'){
                    $search_by['Sort By'][] = 'CPT/HCPCS';
                }else{
                    $search_by['Sort By'][] = ucwords(str_replace('_', ' ', $request['sort_by']));
                }                
            }
            if(!empty($request['sort_by_order'])){
                $SortByOrder = $request['sort_by_order'];
                $search_by['Sort By Order'][] = $request['sort_by_order'];
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
            $sp_return_result = DB::select('call procedureCollection("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '","' . $acc_no . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $insurance . '", "' . $insurance_category . '", "' . $SortBy . '", "' . $SortByOrder . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->procedure_collection_count;
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
            $sp_return_result = DB::select('call procedureCollection("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '","' . $acc_no . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $insurance . '", "' . $insurance_category . '", "' . $SortBy . '", "' . $SortByOrder . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call procedureCollection("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '", "' . $dos_end_date . '", "' . $rendering_provider_id . '","' . $acc_no . '", "' . $cpt_code_id . '",  "' . $cpt_custom_type_from . '", "' . $cpt_custom_type_to . '", "' . $insurance . '", "' . $insurance_category . '", "' . $SortBy . '", "' . $SortByOrder . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $cptreport_list = $sp_return_result;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cptreport_list','pagination','pagination_prt','search_by')));

    }

    ##### PROCEDURE REPORT Function END #####



    /*     * * index function start ** */

    public function getAgingReportApi() {
        /* $insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
          $facilities = Facility::getAllfacilities(); */
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('aging_summary');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities','search_fields','searchUserData')));
    }

    /*     * * index function end ** */

    /*     * * search function start ** */

    public function getAgingReportSearchApi($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $aging_report = $this->getAgingReportResult($request);
        $result = array();
        $header = $aging_report['header'];
        $title = $aging_report['title'];
        $headers = $aging_report['headers'];
        unset($aging_report['title']);
        unset($aging_report['header']);
        unset($aging_report['headers']);
        foreach ($aging_report as $aging_report_key => $aging_report_value) {
            $result[$aging_report_key] = $aging_report_value;
            if ($request['aging_group_by'] != 'all' && $request['aging_group_by'] != 'patient') {
                foreach ($aging_report['aging_result'] as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }
        unset($result['aging_result']);
        $aging_report_list = $result;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('aging_report_list', 'title', 'header','headers')));
    }

    /*     * * search function end ** */

    /*     * * result function start ** */

    public function getAgingReportResult($request) {

        ### Initialize two rows of result ###
        $providerType = 'false';
        $response['header'] = ["", "Unbilled", '', "0-30", '', "31-60", '', "61-90", '', "91-120", '', "121-150", '', ">150", '', "Total", ''];
        $age_date = ["0-30", "31-60", "61-90", "91-120", "121-150", ">150"];
        $response['name'] = ["", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value", "Claims", "Value"];
        
        if(!isset($request['claim_by'])) {
            $request['claim_by'] = 'created_date';
        }

        if($request['claim_by'] == 'created_date'){
            $get_list_header["Aging By"] = 'Transaction Date';
        }else if($request['claim_by'] == 'submitted_date'){
            $get_list_header["Aging By"] = 'Submitted Date';
        }else {
            $get_list_header["Aging By"] = 'DOS';
        }

        if (isset($request["aging_days"]))
        if ($request["aging_days"] != "All") {
            $age_date = ($request["aging_days"] == "Unbilled") ? [] : [$request["aging_days"]];
            $request["aging_days"] = ($request["aging_days"] == "Unbilled") ? "Unbilled" : $request["aging_days"];
            $response['header'] = ["AR Days", $request["aging_days"], '', "Totals", ''];
            $response['name'] = ["", "Claims", "Value", "Claims", "Value"];
            $get_list_header["Aging Days"] =  $request["aging_days"];
        }else{
            $get_list_header["Aging Days"] =  "All";
        }

        ### Getting 3rd & 4th row result[Responsibility] ###
        if ($request['aging_group_by'] == 'all' || $request['aging_group_by'] == 'patient') {
            if ($request['aging_group_by'] == 'all') {
                $title = "Aging Summary";
            }
            if ($request['aging_group_by'] == 'patient') {
                $title = "Aging Summary";
            }
            $response['insurance'] = $response['patient'] = [];

            ### Getting 3rd row result[Responsibility] ###
            if ($request['aging_group_by'] == 'all' || $request['aging_group_by'] == 'patient') {
                $unbilled_pat_claim_count = $unbilled_pat_claim_charge = 0;
                if ($request["aging_days"] == "All" || $request["aging_days"] == "Unbilled") {

                    $unbilled_pat_claim_count = 0;
                    $unbilled_pat_claim_charge = 0;
                    $response['patient'] = ['Patient', $unbilled_pat_claim_count, $unbilled_pat_claim_charge];
                } else
                    $response['patient'] = ['Patient'];

                $aging_pat_array_val = $this->AgingCalc("patient", '', '', $age_date,$request);

                foreach ($aging_pat_array_val['aging'] as $key => $value) {
                    $response['patient'][] = $value['claims'];
                    $response['patient'][] = $value['value'];
                }

                $total_pat_count = $aging_pat_array_val['claims'] + $unbilled_pat_claim_count;
                $total_pat_charge = $aging_pat_array_val['value'] + $unbilled_pat_claim_charge;
                $response['patient'][] = (int) $total_pat_count;
                $response['patient'][] = (float) $total_pat_charge;
                $get_list_header["Aging Group By"] =  ucfirst($request['aging_group_by']);
            }

            ### Getting 4th row result[Responsibility] ###

            if ($request['aging_group_by'] == 'insurance' || $request['aging_group_by'] == 'all') {
                $unbilled_ins_claim_count = $unbilled_ins_claim_charge = 0;
                if ($request["aging_days"] == "All" || $request["aging_days"] == "Unbilled") {
                    $unbilled_ins_claim = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                                        ->where('claim_info_v1.claim_submit_count', 0)
                                        ->where('claim_info_v1.insurance_id',"!=", '0');
                    if(!isset($request['export_id'])){
                        if(Auth::check() && Auth::user()->isProvider()){ 
                            $providerType = 'true';
                            $unbilled_ins_claim->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                        }
                    }elseif(isset($request['export_id']) && !empty($request['export_id'])){
                        $providerStatus = App\Http\Helpers\Helpers::isProvider($request['export_id']);
                        if($providerStatus['status']){
                            $providerType = 'true';
                            $unbilled_ins_claim->where('claim_info_v1.rendering_provider_id',$providerStatus['provider_id']);
                        }
                    }
                                        
                    $unbnilledCount = clone $unbilled_ins_claim;
                    
                    $unbilled_ins_claim_charge = (float) $unbilled_ins_claim->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)'));
                    $unbilled_ins_claim_count = (int) $unbnilledCount->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->count();
                    $response['insurance'] = ['Insurance', $unbilled_ins_claim_count, $unbilled_ins_claim_charge];
                } else {
                    $response['insurance'] = ['Insurance'];
                }

                $aging_ins_array_val = $this->AgingCalc("insurance", '', '', $age_date,$request);
                
                foreach ($aging_ins_array_val['aging'] as $key => $value) {
                    $response['insurance'][] = $value['claims'];
                    $response['insurance'][] = $value['value'];
                }

                $total_count = $aging_ins_array_val['claims'] + $unbilled_ins_claim_count;
                $total_charge = $aging_ins_array_val['value'] + $unbilled_ins_claim_charge;
                $response['insurance'][] = (int) $total_count;
                $response['insurance'][] = (float) $total_charge;
                $get_list_header["Aging Group By"] =  ucfirst($request['aging_group_by']);
            }

            $array_list = [$response['patient'], $response['insurance']];
            $get_responce = $this->SumAndPercentageCalc($array_list);
            
            if (count($response['patient']) == 0)
                unset($response['patient']);
            
            if (count($response['insurance']) == 0)
                unset($response['insurance']);

            $response['total'] = $get_responce['total'];
            $response['total_percentage'] = $get_responce['total_percentage'];
        }
        // User search
        if (!empty($request['user'])) {
            $User_name =  Users::whereIn('id', $request["user"])->where('status', 'Active')->pluck('name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $get_list_header['User'] = $User_name;
        }
        
        //Aging Group By Provider, Facility, Insurance
        if ($request["aging_group_by"] == "rendering_provider" || $request["aging_group_by"] == "billing_provider" || $request["aging_group_by"] == "facility" || $request["aging_group_by"] == "insurance") {

            //Set Report Title based on "Aging Group By" and get all ID of the respective filter type
            if ($request["aging_group_by"] == "rendering_provider" || $providerType == 'true') {
                $title = "Aging Summary By Rendering Provider";
                $pro_id = Provider::typeBasedProviderlist('Rendering');
                $aging_type = "rendering_provider_id";
            } elseif ($request["aging_group_by"] == "billing_provider") {
                $title = "Aging Summary By Billing Provider";
                $pro_id = Provider::typeBasedProviderlist('Billing');
                $aging_type = "billing_provider_id";
            } elseif ($request["aging_group_by"] == "facility") {
                $title = "Aging Summary By Facility";
                $pro_id = Facility::getAllfacilities();
                $aging_type = "facility_id";
            } elseif ($request["aging_group_by"] == "insurance") {
                $title = "Aging Summary By Insurance";
                $pro_id = Insurance::where('status', 'Active')->orderBy('short_name', 'ASC')->pluck('short_name', 'id')->all();
                $aging_type = "aging_insurance_id";
            }

            //Set Report Header Title
            if(empty($request[$aging_type])){
                $request[$aging_type] = "all";
                $get_list_header[ucwords(str_replace('_', ' ', $request['aging_group_by']))] =  'All';
            }
            else
            {
                $get_list_header[ucwords(str_replace('_', ' ', $request['aging_group_by']))] =  ucwords($pro_id[$request[$aging_type]]);
            }

            //Set Aging Type ID to all if no aging parameter selected (Ex: if "Aging Group By" is Rendering Provider and no provider is selected, get all ID else
            //appropriate selected ID)
            $aging_type_id = ($request[$aging_type] == "all") ? $pro_id : [$request[$aging_type] => $pro_id[$request[$aging_type]]];
            $response['aging_result'] = [];

            //Loop for every Aging Group By ID
            foreach ($aging_type_id as $key => $value) {
                //Initialize Unbilled claim count and value to 0
                $unbilled_pro_claim_count = $unbilled_pro_claim_charge = 0;
                //Initialize Column name for insurance id since its changed in the request
                if($aging_type=='aging_insurance_id')
                    $aging_type = "insurance_id";
                //If "Aging Days" = All or Unbilled get Unbilled count and value
                if ($request["aging_days"] == "All" || $request["aging_days"] == "Unbilled") {
                    //Get all claim with insurance id not equal to "0" and submit count = "0" with the appropriate column based on "Aging Group By"
                        $unbilled_pro_claim = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->where($aging_type, $key)->where('claim_info_v1.claim_submit_count', 0)
                        ->where('claim_info_v1.insurance_id',"!=", '0');
                    
                    if(!isset($request['export_id'])){
                        if(Auth::check() && Auth::user()->isProvider()){ 
                            $providerType = 'true';
                            $unbilled_pro_claim->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                        }
                    }elseif(isset($request['export_id']) && !empty($request['export_id'])){
                        $providerStatus = App\Http\Helpers\Helpers::isProvider($request['export_id']);
                        if($providerStatus['status']){
                            $providerType = 'true';
                            $unbilled_pro_claim->where('claim_info_v1.rendering_provider_id',$providerStatus['provider_id']);
                        }
                    }
                        
                    $unbilled_pro_claim_count = (int) $unbilled_pro_claim->count();
                    $unbilled_pro_claim_charge = (float) $unbilled_pro_claim->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)'));

                    $unbilled_pro_claim_count = (int) $unbilled_pro_claim->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->count();
                    $unbilled_pro_claim_charge = (float) $unbilled_pro_claim->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0')->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj)'));

                    $response['aging_result'][$value] = [$value, $unbilled_pro_claim_count, $unbilled_pro_claim_charge];
                //If aging days is other than Unbilled
                } else {
                    $response['aging_result'][$value] = [$value];
                }
               
                //Calculate Aging Values other than Unbilled
                $aging_pro_array_val = $this->AgingCalc("others", $aging_type, $key, $age_date,$request);

                foreach ($aging_pro_array_val['aging'] as $keys => $values) {
                    $response['aging_result'][$value][] = $values['claims'];

                    $response['aging_result'][$value][] = $values['value'];
                }
                if(array_sum($response['aging_result'][$value])=='0.00'){
                    unset($response['aging_result'][$value]);
                }else{
                    $aging_pro_claim_count = $aging_pro_array_val['claims'] + $unbilled_pro_claim_count;
                    $aging_pro_claim_charge = $aging_pro_array_val['value'] + $unbilled_pro_claim_charge;
                    $response['aging_result'][$value][] = (int) $aging_pro_claim_count;
                    $response['aging_result'][$value][] = (float) $aging_pro_claim_charge;
                }
            }
            
            $get_response = $this->SumAndPercentageCalc($response['aging_result']);
            $response['total'] = $get_response['total'];
            $response['total_percentage'] = $get_response['total_percentage'];
            $get_list_header["Aging Group By"] =  ucwords(str_replace('_', ' ', $request['aging_group_by']));
        }

        $response['title'] = $title;
        $response['headers'] = $get_list_header;
        return $response;
    }

    /*     * * result function end ** */

    /*     * * Aging wise calculate function start here ** */

    public function AgingCalc($type, $type_field, $current_id, $age_date,$request) {
        $result_value = $result_arr = $total_arr = $claims_count = $claims_amt = [];
        DB::enableQueryLog();
        foreach ($age_date as $key => $value) {
            //Joing Financial Table to Get Claim Level Patient Due, Insurance Due, Patient Paid, etc.
            $claim_arr = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');

            if(!isset($request['export_id'])){
                if(Auth::check() && Auth::user()->isProvider()){ 
                    $claim_arr->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                }
            }elseif(isset($request['export_id']) && !empty($request['export_id'])){
                $providerStatus = App\Http\Helpers\Helpers::isProvider($request['export_id']);
                if($providerStatus['status']){
                    $claim_arr->where('claim_info_v1.rendering_provider_id',$providerStatus['provider_id']);
                }
            }

            //Patient Responsibility
            if ($type == "patient"){
                $claim_arr = $claim_arr->where('claim_info_v1.insurance_id', 0);
            }
            //Insurance Responsibility
            if ($type == "insurance"){
                $claim_arr = $claim_arr->where('claim_info_v1.insurance_id', '!=', 0)->where(function($qry){
                            $qry->where(function($query){
                                $query->where('claim_info_v1.claim_submit_count','>' ,0);
                            });
                        });
            }
            //Aging Calculation for all billed other Provider, Facility, Insurance wise individual record ###
            if ($type == "others") {
                if($type_field=='insurance_id'){
                        $claim_arr = $claim_arr->where($type_field, $current_id)->where('claim_info_v1.insurance_id', '!=', 0)->where(function($qry){
                            $qry->where(function($query){
                                $query->where('claim_info_v1.claim_submit_count','>' ,0);
                            });
                        });
                }else{
                    $claim_arr = $claim_arr->where($type_field, $current_id)->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                        })->orWhere('claim_info_v1.insurance_id',0);
                    })->whereNull('claim_info_v1.deleted_at');
                }
            }
            // User search
            if (isset($request['user']) && $request['user'] != '') {
                $claim_arr->whereIn('claim_info_v1.created_by', $request['user']);
            }

            //Explode Date Range for Bill Cycle (Ex: 0-30, 30-60, etc.) and calculate date to filter
            $date_key = strpos($value, '-')?explode("-", $value):explode(">", $value);
            /*$start_date = strpos($value, '-')?date('Y-m-d', strtotime('-' . $date_key[0] . ' day')):date('Y-m-d', strtotime('-' . $date_key[1] . ' day'));
            $end_date = ($date_key[0] == "" && $date_key[1] == 150) ? 'above' : date('Y-m-d', strtotime('-' . $date_key[1] . ' day'));*/
            $practice_timezone = Helpers::getPracticeTimeZone();  
            $data_value = explode("-", $value);
            if((empty($data_value[1]))){
                $end_date = '';
            }else{
                $end_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$data_value[1]-1)));
            }
            if(strpos($value, ">") !== false){
                $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", 149))));
            } else{
                if(@$data_value[0]=='Unbilled'){
                    $start_date = 'Unbilled';
                }else{
                    if(@$data_value[0] == 0){
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]))));                    
                    }
                    else{
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]-1))));
                    }
                }
            }
            //\Log::info($value.'#'.$start_date.'-'.$end_date);
            
            //Filter by "Aging By" filter value (Created Date, DOS and Submitted Date)
            if($request['claim_by'] == 'created_date'){
                if ($end_date == "above") {
                    $claim_arr->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) < '$start_date'");
                } else {
                    $claim_arr->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$start_date' AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$end_date'");
                }
            }else if($request['claim_by'] == 'submitted_date'){
                if ($end_date == "above") {
                    if ($type == "patient"){
                        $claim_arr = DB::select(DB::raw('SELECT SUM(fin.total_charge) - SUM(fin.insurance_paid+fin.patient_paid+fin.withheld+patient_adj+fin.insurance_adj) as patient_dues, COUNT(fin.id) AS counts FROM pmt_claim_fin_v1 fin INNER JOIN ( SELECT claim_id, MAX(created_at) AS created_at FROM claim_tx_desc_v1 WHERE transaction_type IN ("Responsibility", "New Charge") AND responsibility = 0  GROUP BY claim_id HAVING DATE(created_at) <"'.$start_date.'") AS tx ON tx.claim_id = fin.claim_id INNER JOIN ( SELECT id FROM claim_info_v1 where insurance_id = 0 and deleted_at is NULL) AS info ON tx.claim_id = info.id'));
                    } else {
                        $claim_arr->whereDate('claim_info_v1.last_submited_date', "<", $start_date);
                    }
                } else {
                    if ($type == "patient"){
                       $claim_arr = DB::select(DB::raw('SELECT SUM(fin.total_charge) - SUM(fin.insurance_paid+fin.patient_paid+fin.withheld+patient_adj+fin.insurance_adj) as patient_dues, COUNT(fin.id) AS counts FROM pmt_claim_fin_v1 fin INNER JOIN ( SELECT claim_id, MAX(created_at) AS created_at FROM claim_tx_desc_v1 WHERE transaction_type IN ("Responsibility", "New Charge") AND responsibility = 0 GROUP BY claim_id HAVING DATE(created_at) <="'.$start_date.'" and DATE(created_at) >="'.$end_date.'" ) AS tx ON tx.claim_id = fin.claim_id INNER JOIN ( SELECT id FROM claim_info_v1 where insurance_id = 0 and deleted_at is NULL) AS info ON tx.claim_id = info.id'));
                    } else {
                        $claim_arr->whereDate('claim_info_v1.last_submited_date', "<=", $start_date)->whereDate('claim_info_v1.last_submited_date', ">=", $end_date);
                    }
                }
            }else {
                if (empty($end_date)) {
                    $claim_arr->whereDate('claim_info_v1.date_of_service', "<", $start_date);
                } else {
                    $claim_arr->whereDate('claim_info_v1.date_of_service', "<=", $start_date)->whereDate('claim_info_v1.date_of_service', ">=", $end_date);
                }
            }
            //Total Claim Count of the result
            if ($type == "patient" && $request['claim_by'] == 'submitted_date'){
                $result['claims'] = $claim_arr[0]->counts;
            }
            else{
                if ($type == "others"){
                    if ($type_field=='insurance_id')
                        $result['claims'] = (int) $claim_arr->count();
                    else
                        $result['claims'] = (int) $claim_arr->count();
                }
                else{
                    $result['claims'] = (int) $claim_arr->count();
                }
            }
            // Patient AR Due  = Patient Due
            if ($type == "insurance"){
                $result['value'] = (float) $claim_arr->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)'));
            }elseif ($type_field=='insurance_id'){
                $result['value'] = (float) $claim_arr->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)'));
            }   else {
                if ($type == "patient" && $request['claim_by'] == 'submitted_date'){
                    $result['value'] = (float) $claim_arr[0]->patient_dues;
                }
                else{
                    if ($type == "others")
                        $result['value'] = (float) $claim_arr->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)'));
                    else
                        $result['value'] = (float) $claim_arr->sum(DB::raw('(claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)'));
                }
            }
            //Store Aging Days, Claim Count and Claim Amount in an array
            $claims_count[$key] = $result['claims'];
            $claims_amt[$key] = $result['value'];
            $total_arr[$value] = $result;
        }
        //\Log::info(DB::getQueryLog());
        //Store all aging result
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
        $percentage_array = [];
        foreach ($get_combined_value as $key => $value) {
            $total_value = $result['total'][count($result['total']) - 1];
            $percentage = 0;
            if ($key != 0 && $key % 2 == 0) {
                if ($total_value > 0 && $value > 0)
                    $percentage = round((($value / $total_value) * 100),2);
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
                $value = ($key == 0) ? $new_array[$key][$key_arr] : (float)$new_array[$key][$key_arr] + (float)($a[$key - 1][$key_arr]);
                $a[$key][] = (string) $value;
            }
            $added_array = $a[$key];
        }
        return $added_array;
    }

    /*     * * Adding multi dimentional array function end here ** */

    ############ Aging Report End ############
    ############ Appointment Report Start ############

    public function getAppointmentReportApi() {
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
        $facilities = Facility::getAllfacilities();

        // Adjustment_reason
        $adjustment_reason = AdjustmentReason::pluck('adjustment_reason', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities', 'adjustment_reason')));
    }

    public function getAppointmentSearchApi($export = '') {
        $request = Request::All();
        $appointment = $this->getAppointmentResult($request);
        //$appointment_list = $appointment['appointment_list']->get();
        $header = $appointment['header'];
        $column = $appointment['column'];
        /* if ($export != "") {
          $exportparam = array(
          'filename' => 'Appointment Report',
          'heading' => '',
          'fields' => array(
          'Patient Name' => array('table' => '', 'column' => 'patient_id', 'use_function' => ['App\Models\Patients\Patient', 'PatientWithSurname'], 'label' => 'Patient Name'),
          'Act No' => array('table' => 'patient', 'column' => 'account_no', 'label' => 'Act No'),
          'Scheduled Date' => array('table' => '', 'column' => 'created_at', 'use_function' => ['App\Http\Helpers\Helpers', 'dateFormat'], 'label' => 'Scheduled Date'),
          'Appt. Date' => array('table' => '', 'column' => 'scheduled_on', 'use_function' => ['App\Http\Helpers\Helpers', 'dateFormat'], 'label' => 'Appt. Date'),
          'appointment_time' => 'Appt. Time',
          'provider_id' => array('table' => 'provider', 'column' => 'short_name', 'provider_name', 'label' => 'Provider'),
          'facility_id' => array('table' => 'facility', 'column' => 'short_name', 'facility_name', 'label' => 'Facility'),
          'status' => 'Status',
          'Patient Type' => array('table' => '', 'column' => 'patient_id', 'use_function' => ['App\Models\Patients\PatientInsurance', 'PatientCheckwithClaimBasis'], 'label' => 'Patient'),
          'copay_option' => 'Copay Type',
          'copay' => 'Copay Amount',
          'Prev. Appt' => array('table' => '', 'column' => 'patient_id', 'use_function' => ['App\Models\Scheduler\PatientAppointment', 'getLastappointmentDate'], 'label' => 'Prev. Appt'),
          'created_by' => array('table' => 'user', 'column' => 'name', 'label' => 'User'),
          ));
          $callexport = new CommonExportApiController();
          return $callexport->generatemultipleExports($exportparam, $appointment_list, $export);
          } */
        if ($export == "") {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $appointment_list = $appointment['appointment_list']->paginate($paginate_count);
            $appointment_array = $appointment_list->toArray();
            $pagination_prt = $appointment_list->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $appointment_array['total'], 'per_page' => $appointment_array['per_page'], 'current_page' => $appointment_array['current_page'], 'last_page' => $appointment_array['last_page'], 'from' => $appointment_array['from'], 'to' => $appointment_array['to'], 'pagination_prt' => $pagination_prt);
            $app_list = json_decode($appointment_list->toJson());
            $appointment_list = $app_list->data;
        } else {
            $appointment_list = $appointment['appointment_list']->get();
            $app_list = json_decode($appointment_list->toJson());
            $appointment_list = $app_list;
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('appointment_list', 'header', 'column', 'pagination')));
    }

    public function getAppointmentResult($request) {
        $appointment_list = PatientAppointment::with('provider', 'provider.provider_types', 'provider.degrees', 'provider.provider_type_details', 'facility', 'facility.facility_address', 'facility.speciality_details', 'facility.pos_details', 'facility.county', 'patient', 'user', 'claim')->orderBy('scheduled_on', 'DESC');
        $get_list_header = $hide_col = [];
        //Date Select

        /* Facility id not equal to all */
        if ($request["facility_id"] != "" && $request["facility_id"] != "all") {
            $faci_id = $request["facility_id"];
            $appointment_list->where("facility_id", $faci_id); //dd($appointment_list->count());
            $get_list_header["Facility Name"] = Facility::getFacilityName($faci_id);
            $hide_col["facility"] = 1;
        }
        /* Provider id not equal to all  */
        if ($request["provider_id"] != "" && $request["provider_id"] != "all") {
            $provider_id = $request["provider_id"];
            $appointment_list->where("provider_id", $provider_id);
            $get_list_header["Provider"] = Provider::getProviderNamewithDegree($provider_id);
            $hide_col["Provider"] = 1;
        }

        /* 'non_billable_visit' is not empty */
        if ($request['non_billable_visit'] != '') {
            $appointment_list->where('non_billable_visit', $request['non_billable_visit']);
            $get_list_header["Non Billable Visit"] = ucwords($request['non_billable_visit']);
            $hide_col["billable"] = 1;
        }
        /*  Patient type new patient or already exists patient */
        if ($request['patient_option'] != '') {
            $appointment_list->where('is_new_patient', $request['patient_option']);
            $get_list_header["Patient Type"] = $request['patient_option'];
            $hide_col["billable"] = 1;
        }

        if ($request['coverage_status'] != '') {
            $patient_ins = $request['coverage_status'];
            $appointment_list->has('patient')->whereHas('patient', function($q)use($patient_ins) {
                $q->where('is_self_pay', $patient_ins);
            });
        }

        /* Date option is Enter date */
        /* Created based on appointment */
        if ($request['charge_date_option'] == 'enter_date') {
            if ($request['date_option'] == 'enter_date') {
                $from_date = $request['from_date'];
                $to_date = $request['to_date'];
                $start_date = ($request['from_date'] != '') ? Helpers::dateFormat($request['from_date'], 'datedb') : '';
                $end_date = $request['to_date'];
                if ($start_date != '' && $end_date == '')
                    $request['to_date'] = date('m/d/Y');
                $end_date = ($request['to_date'] != '') ? Helpers::dateFormat($request['to_date'], 'datedb') : '';
                $appointment_list->whereRaw("DATE(created_at) >= '$start_date' and DATE(created_at) <= '$end_date'");
                $get_list_header["Scheduled Date"] = $request['from_date'] . "  To " . $request['to_date'];
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'daily') {
                $appointment_list->whereRaw('Date(created_at) = CURDATE()');
                $get_list_header["Scheduled Date"] = ucfirst($request['date_option']);
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'current_month') {
                $appointment_list->where(DB::raw('MONTH(created_at)'), '=', (date('m')));
                $get_list_header["Scheduled Month"] = "Current Month";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'previous_month') {
                $appointment_list->where(DB::raw('MONTH(created_at)'), '=', (date('m')) - 1);
                $get_list_header["Scheduled Month"] = "Previous Month";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'current_year') {
                $appointment_list->where(DB::raw('YEAR(created_at)'), '=', (date('Y')));
                $get_list_header["Scheduled Year"] = "Current Year";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'prev_year') {
                $appointment_list->where(DB::raw('YEAR(created_at)'), '=', (date('Y')) - 1);
                $get_list_header["Scheduled Year"] = "Previous Year";
                $hide_col["scheduled_date"] = 1;
            }
        } else {
            /* Scheduled on based time filter */
            if ($request['date_option'] == 'enter_date') {
                $from_date = $request['from_date'];
                $to_date = $request['to_date'];
                $start_date = ($request['from_date'] != '') ? Helpers::dateFormat($request['from_date'], 'datedb') : '';
                $end_date = $request['to_date'];
                if ($start_date != '' && $end_date == '')
                    $request['to_date'] = date('m/d/Y');
                $end_date = ($request['to_date'] != '') ? Helpers::dateFormat($request['to_date'], 'datedb') : '';
                $appointment_list->whereRaw("DATE(scheduled_on) >= '$start_date' and DATE(scheduled_on) <= '$end_date'");
                $get_list_header["Appointment Date"] = $request['from_date'] . "  To " . $request['to_date'];
                $hide_col["appointment_date"] = 1;
            } elseif ($request['date_option'] == 'daily') {
                $appointment_list->whereRaw('Date(scheduled_on) = CURDATE()');
                $get_list_header["Scheduled Date"] = ucfirst($request['date_option']);
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'current_month') {
                $appointment_list->where(DB::raw('MONTH(scheduled_on)'), '=', (date('m')));
                $get_list_header["Scheduled Month"] = "Current Month";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'previous_month') {
                $appointment_list->where(DB::raw('MONTH(scheduled_on)'), '=', (date('m')) - 1);
                $get_list_header["Scheduled Month"] = "Previous Month";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'current_year') {
                $appointment_list->where(DB::raw('YEAR(scheduled_on)'), '=', (date('Y')));
                $get_list_header["Scheduled Year"] = "Current Year";
                $hide_col["scheduled_date"] = 1;
            } elseif ($request['date_option'] == 'prev_year') {
                $appointment_list->where(DB::raw('YEAR(scheduled_on)'), '=', (date('Y')) - 1);
                $get_list_header["Scheduled Year"] = "Previous Year";
                $hide_col["scheduled_date"] = 1;
            }
        }

        if (isset($request['status_option']) && count($request['status_option']) > 0) {
            $appointment_list->whereIn('status', $request['status_option']);
        }
        $result['appointment_list'] = $appointment_list;
        $result['header'] = $get_list_header;
        $result['column'] = $hide_col;
        return $result;
    }

    ############ Appointment Report End ############
    ############ Charges Report Start ############
    /*     * * Index page start ** */

    public function getChargesApi() {
        /*  $insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
          $facilities = Facility::getAllfacilities();
         */
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('charge_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities','search_fields','searchUserData')));
    }
    public function getChargesPaymentsApi() {

        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('chargesPayments');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities','search_fields','searchUserData')));
    }

    /*     * * Index page end ** */

    /*     * * Search page start ** */

    public function getChargesearchApi($export = '',$data = '') {

        $page_rec_res='';

        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();

        $status_opt = @$request['status_option'];
        $chrg_date_opt = @$request['charge_date_option'];
        $page = "lineitemreport";

        $result = $this->getChargeResult($request);

        $header = $result['header'];
        $column = $result['column'];
        $result['page'] = $page;
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');

        //------------SUMMARY----------
        $claimQry = $result['claims']; 
        $summary_result = clone $result['claims']; 
        $tot_summary = [];
        $summary = $claimQry->orderBy('id','asc')->get(); 
        $patient_count = $summary_result->groupBy('patient_id')->get()->count();
        $tot_summary['total_patient'] = isset($patient_count)?$patient_count:0;
        $tot_summary['total_claim'] = $tot_summary['total_charge'] = $tot_summary['total_cpt'] = $tot_summary['total_unit'] = 0;

        if(!empty($summary))
            foreach($summary as $claims_list){
            $tot_summary['total_charge'] += $claims_list->total_charge;
            $tot_summary['total_cpt'] += count($claims_list->cpttransactiondetails);
            $tot_summary['total_claim'] += 1;
                foreach($claims_list->cpttransactiondetails as $cpt){
                    $tot_summary['total_unit'] +=is_numeric($cpt->unit) ? $cpt->unit : 0;
                }
            }

        if(!isset($request['export']))
            $claims = $result['claims']->take($paginate_count)->get();

        //$total_charge_count = $result['total_charge_count'];
        //$total_cpt_balance = $result['total_cpt_balance'];
        $total_charge_amount = $total_no_of_cpt = 0;

        /*if ($total_charge_count > 0) {
            $total_charge_arr = $result['claims']->pluck('total_charge', 'id')->all();
            $total_charge_amount = number_format(array_sum($total_charge_arr), 2);
            $claim_number_arr = array_keys($total_charge_arr);
            $total_no_of_cpt = ClaimCPTInfoV1::whereIn('claim_id', $claim_number_arr)->whereNotIn('cpt_code', ['patient', 'Patient'])->count();
        }
        $tcc = $total_charge_count;
        $tca = $total_no_of_cpt;*/
        $pagination = '';

        if ($page == "lineitemreport") {
            $singlepage_total_arr = $result['claims']->paginate($paginate_count)->pluck('total_charge', 'id')->all();
            if(isset($request['export']))
                $claims = $summary;
            else
                $claims = $result['claims']->paginate($paginate_count);
            //dd($claims);
            $sinpage_charge_amount = number_format(array_sum($singlepage_total_arr), 2);
            $sinpage_claim_arr = array_keys($singlepage_total_arr);
            $sinpage_total_cpt = ClaimCPTInfoV1::whereIn('claim_id', $sinpage_claim_arr)->whereNotIn('cpt_code', ['patient', 'Patient'])->count();
            $claim_array = $claims->toArray();

            if(!isset($request['export']))
                $pagination_prt = $claims->render();
            else
                $pagination_prt='';

            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';

            if(!isset($request['export'])) {
                $pagination = array('total' => $claim_array['total'], 'per_page' => $claim_array['per_page'], 'current_page' => $claim_array['current_page'], 'last_page' => $claim_array['last_page'], 'from' => $claim_array['from'], 'to' => $claim_array['to'], 'pagination_prt' => $pagination_prt);
            }

            $claims_list = json_decode($claims->toJson());
            
            if(isset($request['export']))
                $claims = $claims_list;
            else
                $claims = $claims_list->data;
        }

        if (isset($request['include_cpt_option'])) {
            if(isset($request['export']))
                $include_cpt_option = explode(',', $request["include_cpt_option"]);
            else
                $include_cpt_option = $request['include_cpt_option'];
        } else {
            $include_cpt_option = array();
        }

        $tdate = $result['header'];

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims', 'page', 'header', 'column', 'pagination', 'include_cpt_option', 'total_no_of_cpt', 'page_rec_res', 'sinpage_charge_amount', 'sinpage_claim_arr', 'sinpage_total_cpt', 'status_opt', 'tdate', 'chrg_date_opt', 'tot_summary')));
    }

    /*     * * Search page end ** */


    /*     * * Search criteria start ** */

    public function getChargeResult($request) { 
	
        $get_function_value = ClaimInfoV1::with([
                    'patient' => function($query) {
                        $query->select('id', 'title', 'first_name', 'last_name', 'middle_name', 'account_no');
                    }, 
                    'rendering_provider' => function($query) {
                        $query->select('id', 'provider_name','short_name');
                    },
                    'facility_detail' => function($query) {
                        $query->select('id', 'facility_name','short_name');
                    },
                    'insurance_details' => function($query) {
                        $query->select('id', 'insurance_name','short_name');
                    },
                    'billing_provider' => function($query) {
                        $query->select('id', 'provider_name','short_name');
                    },
                    'cpttransactiondetails' => function($query) {
                        $query->whereNotIn('cpt_code', ['patient', 'Patient']);
                    },
                    'user' => function($query) {
                        $query->select('id', 'short_name','name');
                    },
                    'dosdetails' => function($query) {
                        $query->select('claim_id', 'id', DB::raw('sum(charge) as total_balance'));
                    },
                    'pos' => function($query) {
                        $query->select('id','code', 'pos');
                    },
                    'pmt_info' => function($query) {
                        $query->select('patient_due','insurance_due', 'claim_id');
                    },
                    'hold_option' => function($query) {
                        $query->select('id', 'option');
                    }
                    
                    ]);

        $get_list_header = $hide_col = [];
        $from_date = $to_date = "";
        // Filter by Transaction Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $exp = explode("-",$request['select_transaction_date']);
            $start_date = str_replace('"', '', $exp[0]);
            $end_date = str_replace('"', '', $exp[1]);
            $from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            if ($from_date != '' && $to_date != '') {
                $get_function_value->whereRaw("created_at >= '$from_date' and created_at <= '$to_date'");
            }
            $get_list_header["Transaction Date"] = date("m/d/y",strtotime($start_date)) . "  To " . date("m/d/y",strtotime($end_date));
            $hide_col["charge_date"] = 1;
        }
        
        // Filter by Dos Date
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $start_date = str_replace('"', '', $exp[0]);
            $end_date = str_replace('"', '', $exp[1]);
            $from_date = date('Y-m-d', strtotime($start_date));
            $to_date = date('Y-m-d', strtotime($end_date));
            if ($from_date != '' && $to_date != '') {
                $get_function_value->whereRaw("(DATE(date_of_service) >= '$from_date' and DATE(date_of_service) <= '$to_date')");
            }
            $get_list_header["Date Of Service"] = date("m/d/Y",strtotime($from_date)) . "  To " . date("m/d/Y",strtotime($to_date));
            $hide_col["chargedos_date"] = 1;
        }
        if(isset($request['status_option']) && !empty($request['status_option'])) {
            if(!isset($request['export']) && is_array($request['status_option'])){
                    if(is_array($request['status_option']) && $request['status_option'][0]!='' )  {
                        if(count($request['status_option'])==1){
                            $request['status_option'] = explode(',', $request['status_option'][0]);
                        }
                    $statusArr = (isset($request['export'])) ? explode(',', $request["status_option"]) : $request["status_option"];
                    $get_list_header["Status"] = is_array($statusArr) ? implode(",", $statusArr) : $statusArr;
                    $hide_col["Status"] = 1;
                    if (in_array("All", $statusArr)) {
                        //
                    } else {
                        $get_function_value->whereIn('status', $statusArr);
                    }
                    if (in_array("Hold", $statusArr)) {
                        $get_list_header["Hold Reason"] = 'All';
                        if(isset($request['hold_reason']) && !empty($request['hold_reason'])) {
                            if(is_array($request['hold_reason']) && $request['hold_reason'][0]!='' )  {
                                if(count($request['hold_reason'])==1) {
                                    $request['hold_reason'] = explode(',', $request['hold_reason'][0]);
                                } else{
                                    $holdReasonArr = $request['hold_reason'];
                                }
                                $holdReasonArr = (isset($request['export'])) ? explode(',',$request['hold_reason']) : $request['hold_reason'];
                                $get_function_value->whereIn('hold_reason_id', $holdReasonArr); 
                                $get_list_header["Hold Reason"] = is_array($holdReasonArr) ? implode(",", $holdReasonArr) : $holdReasonArr;
                            } 
                        }                
                    }
                }
            } else{
                $statusArr =  explode(',', $request["status_option"]);
                $get_list_header["Status"] = is_array($statusArr) ? implode(",", $statusArr) : $statusArr;
                if (in_array("All", $statusArr)) {
                        //
                    } else {
                        $get_function_value->whereIn('status', $statusArr);
                    }
                if (in_array("Hold", $statusArr)) {
                    $get_list_header["Hold Reason"] = 'All';
                    if(isset($request['hold_reason']) && !empty($request['hold_reason'])) {
                        $holdReasonArr = explode(',',$request['hold_reason']);
                        $get_function_value->whereIn('hold_reason_id', $holdReasonArr);
                        $get_list_header["Hold Reason"] = is_array($holdReasonArr) ? implode(",", $holdReasonArr) : $holdReasonArr; 
                    }
                }
            }
        }
        
        if (isset($request["billing_provider_id"]) && !empty($request['billing_provider_id'])) {
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name");
            if(isset($request['export']) || is_string($request["billing_provider_id"])){
                $get_function_value->whereIn("billing_provider_id", explode(',', $request["billing_provider_id"]));
                $provider= $provider->whereIn('id', explode(',', $request["billing_provider_id"]))->get()->toArray();
                $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
            }else{
                if(is_array($request['billing_provider_id']) && array_sum($request['billing_provider_id'])!=0 )   {
                    if(count($request['billing_provider_id'])==1){
                        $request['billing_provider_id'] = explode(',', $request['billing_provider_id'][0]);
                    }
                    $get_function_value->whereIn("billing_provider_id", $request["billing_provider_id"]);
                    $provider= $provider->whereIn('id', $request['billing_provider_id'])->get()->toArray();
                    $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
                    $hide_col["billing"] = 1;
                }
            }
        }
        
        if (isset($request["rendering_provider_id"]) && !empty($request['rendering_provider_id'])) {
            if(isset($request['export']) || is_string($request["rendering_provider_id"])){
                $get_function_value->whereIn("rendering_provider_id", explode(',', $request["rendering_provider_id"]));
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', explode(',', $request['rendering_provider_id']))->get()->toArray();
                $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
            } else {
                if(is_array($request['rendering_provider_id']) && array_sum($request['rendering_provider_id'])!=0 )   {
                    if(count($request['rendering_provider_id'])==1){
                        $request['rendering_provider_id'] = explode(',', $request['rendering_provider_id'][0]);
                    }
                        $get_function_value->whereIn("rendering_provider_id", $request["rendering_provider_id"]);
                        $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', $request['rendering_provider_id'])->get()->toArray();
                        $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
                        $hide_col["rendering"] = 1;
                }
            }
        }
        
        if (isset($request['facility_id']) && !empty($request['facility_id'])) {
            if(isset($request['export']) || is_string($request["facility_id"])) {
                $get_function_value->whereIn("facility_id", explode(',', $request["facility_id"]));
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',', $request["facility_id"]))->get()->toArray();
                $get_list_header["Facility Name"] =  @array_flatten($facility)[0];
            } else {
                if(is_array($request['facility_id']) && array_sum($request['facility_id'])!=0 )   {
                    if(count($request['facility_id'])==1){
                        $request['facility_id'] = explode(',', $request['facility_id'][0]);
                    }
                    $get_function_value->whereIn('facility_id', $request['facility_id']);
                    $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', $request['facility_id'])->get()->toArray();
                    $get_list_header["Facility Name"] =  @array_flatten($facility)[0];
                    $hide_col["facility"] = 1;
                }
            }
        }
        
        if(isset($request['insurance_charge'])){
            if ($request['insurance_charge'] == 'self') {
                $get_function_value->where('self_pay', 'Yes');
                $get_list_header["Payer"] ="Self Pay";
            }
            if ($request['insurance_charge'] == 'insurance') {
                if((isset($request['insurance_id']) && is_array($request['insurance_id']) && array_sum($request['insurance_id'])!=0)) {
                    if(is_array($request['insurance_id']) && array_sum($request['insurance_id'])!=0 || isset($request['export']) || is_string($request["insurance_id"]))   {
                        if(count((array)$request['insurance_id'])==1){
                            $request['insurance_id'] = (isset($request['export']) || is_string($request["insurance_id"]))?explode(',', $request['insurance_id']):explode(',', $request['insurance_id'][0]);
                        }
                        $insurance_id = $request["insurance_id"];
                        $get_function_value->whereIn('insurance_id', $insurance_id);
                      $insurance_name = Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ', ') as insurance_name")->whereIn('id', $insurance_id)->get()->toArray();
                      $hide_col["insurance"] = 1;
                      $get_list_header["Insurance"] = @array_flatten($insurance_name)[0];
                  }

                }else{
                    $get_function_value->where('self_pay', 'No')->where('insurance_id', '!=',0);
                    $get_list_header["Insurance"] = 'All';
                 }
                  $get_list_header["Payer"] ="Insurance Only";
            }
            if ($request['insurance_charge'] == 'all') {
                $get_list_header["Payer"] ="All";
            }
        }
        
        if(isset($request['reference']))
        if ($request['reference'] != '') {
            $get_function_value->where('claim_reference', 'like', '%' .  $request['reference'] . '%');
            $get_list_header["Reference"] =$request['reference'];
        }
        
        if(isset($request["created_by"]) && $request["created_by"] !='') {
            if($request["created_by"] != "") {
                $user = (isset($request['export']) || is_string($request['created_by'])) ? explode(',',$request['created_by']):$request['created_by'];
                if (in_array("0", $user)) {
                }else{
                    $get_function_value->whereIn('created_by',$user);
                }
                $short_name = DB::connection('responsive')->table('users')
                        ->whereIn('id',$user)
                        ->pluck('short_name')->all();
                $get_list_header["User"] = (in_array("0", $user))? ("All".(isset($user[1])? "," :"" ).implode(',',$short_name)) : implode(',',$short_name);
                $hide_col["user"] = 1;
            }
        }
        
        $result['total_cpt_balance'] = $get_function_value->sum('total_charge');
        $total_charge_count = 0;
        $total_charge_count = $get_function_value->count();
		/* \Log::info('get result');
		\Log::info($total_charge_count); */
        $result['claims'] = $get_function_value;
        $result['header'] = $get_list_header;
        $result['column'] = $hide_col;
        $result['total_charge_count'] = $total_charge_count; 
		
        return $result;
    }

    /** Stored procedure for charge analysis - Anjukaselvan **/
    public function getChargesearchApiSP($export = '',$data = '') { 
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        
        $practice_timezone = Helpers::getPracticeTimeZone();
        $start_date = $end_date = $dos_start_date =  $dos_end_date = $billing_provider_id = $rendering_provider_id = $facility_id = $insurance_charge = $insurance = $status_option = $hold_reason = $include_cpt_option = $user_ids = $reference =  '';

        $page = "lineitemreportSP";

        $get_list_header = $hide_col = [];

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
        if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])){
            $exp = explode("-",$request['select_transaction_date']);
            $start = str_replace('"', '', $exp[0]);
            $end = str_replace('"', '', $exp[1]);
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end);
            
            $get_list_header["Transaction Date"] = date("m/d/y",strtotime($start)) . "  To " . date("m/d/y",strtotime($end));
            $hide_col["charge_date"] = 1;
        }

        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS'))
        if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])){
            $exp = explode("-",$request['select_date_of_service']);
            $dos_start = str_replace('"', '', $exp[0]);
            $dos_end = str_replace('"', '', $exp[1]);
            $dos_start_date = date('Y-m-d', strtotime($dos_start));
            $dos_end_date = date('Y-m-d', strtotime($dos_end));

            $get_list_header["Date Of Service"] = date("m/d/Y",strtotime($dos_start_date)) . "  To " . date("m/d/Y",strtotime($dos_end_date));
            $hide_col["chargedos_date"] = 1;
        }
        
        if (isset($request['status_option']) && $request['status_option'] != '' && !empty($request['status_option'])) {
            $status = ($export!='') ? $request['status_option'] : (is_array($request['status_option']) ? implode(",", $request['status_option']): $request['status_option']);
            $status_list = (isset($export) && is_string($request["status_option"])) ? explode(",", $request['status_option']) : $request['status_option'];
            if(is_array($request['status_option'])) {
                $status_option = (in_array("All", $request['status_option'])) ? '' : $status;
            }else {
                $status_option = (stripos($request['status_option'], 'all') !== false) ? '' : $status;
            }
            if (in_array("Hold", $status_list)) {
                $get_list_header["Hold Reason"] = 'All';
                if(isset($request['hold_reason']) && !empty($request['hold_reason'])) {
                    $hold_reason = ($export!='') ? $request['hold_reason'] : (is_array($request['hold_reason']) ? implode(",", $request['hold_reason']) : $request['hold_reason']);
                    $hold_reason_id = (isset($export) && is_string($request["hold_reason"])) ? explode(",", $request['hold_reason']) : $request['hold_reason'];
                    $hold_reason_name = App\Models\Holdoption::selectRaw("GROUP_CONCAT(`option` SEPARATOR ', ') as option_reason")->whereIn('id', $hold_reason_id)->get()->toArray();
                    $get_list_header["Hold Reason"] =  @array_flatten($hold_reason_name)[0];
                }
            }
            $get_list_header["Status"] = $status;
            $hide_col["groupBy"] = 1;
        }
        
        if (isset($request["billing_provider_id"]) && $request["billing_provider_id"] != '') {
            $billing_provider_id = ($export!='') ? $request['billing_provider_id'] : (is_array($request['billing_provider_id']) ? implode(",", $request['billing_provider_id']) : $request['billing_provider_id']);
            $provider_id = (isset($export) && is_string($request["billing_provider_id"])) ? explode(",", $request['billing_provider_id']) : $request['billing_provider_id'];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', $provider_id)->get()->toArray();
            $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
            $hide_col["billing"] = 1;
        }

        if (isset($request["rendering_provider_id"]) && $request["rendering_provider_id"] != '') {
            $rendering_provider_id = ($export!='') ? $request['rendering_provider_id'] : (is_array($request['rendering_provider_id']) ? implode(",", $request['rendering_provider_id']) : $request['rendering_provider_id']);
            $provider_id = (isset($export) && is_string($request["rendering_provider_id"])) ? explode(",", $request['rendering_provider_id']) : $request['rendering_provider_id'];
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name")->whereIn('id', $provider_id)->get()->toArray();
            $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
            $hide_col["rendering"] = 1;
        }

        if (isset($request['facility_id']) && $request['facility_id'] != '') {
            $facility_id = ($export!='') ? $request['facility_id'] : (is_array($request['facility_id']) ? implode(",", $request['facility_id']) : $request['facility_id'] );
            $fac_id = (isset($export) && is_string($request["facility_id"])) ? explode(",", $request['facility_id']) : $request['facility_id'];
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', $fac_id)->get()->toArray();
            $get_list_header["Facility Name"] =  @array_flatten($facility)[0];
            $hide_col["facility"] = 1;
        }

        if(isset($request['insurance_charge']) && $request['insurance_charge'] != ''){
            if ($request['insurance_charge'] == 'self') {
                $insurance_charge = $request['insurance_charge'];
                $get_list_header["Payer"] ="Self Pay";
            }
            if ($request['insurance_charge'] == 'insurance') {
                $insurance_charge = $request['insurance_charge'];
                if(isset($request['insurance_id']) && $request['insurance_id'] != ''){
                    $insurance = ($export!='') ? $request['insurance_id'] : ((is_array($request['insurance_id'])) ? implode(",", $request['insurance_id']) : $request['insurance_id']);
                    $insurance_id = (isset($export) && is_string($request["insurance_id"]))?explode(',', $request["insurance_id"]):$request["insurance_id"];
                    $insurance_name = Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ', ') as insurance_name")->whereIn('id', $insurance_id)->get()->toArray();
                    $hide_col["insurance"] = 1;
                    $get_list_header["Insurance"] = @array_flatten($insurance_name)[0];
                }else{
                    $get_list_header["Insurance"] = 'All';
                 }
                  $get_list_header["Payer"] ="Insurance Only";
            }
            if ($request['insurance_charge'] == 'all') {
                if (isset($export) && $export == "pdf"){
                    $get_list_header["Payer"] ="All";
                }
                else{
                    $get_list_header["Payer"] ="All";   
                }
            }
        }
        
        if (isset($request['reference']) && $request['reference'] != '') {
            $reference = $request['reference'];
            $get_list_header["Reference"] =$request['reference'];
        }
        
        if(isset($request["created_by"]) && $request["created_by"] !='') {
            if($export!='') {
                $user_ids = $request['created_by'];
            } else {
                $user_ids = (is_array($request['created_by'])) ? implode(",", $request['created_by']) : $request['created_by'];
            }
            $user = (isset($export) && is_string($request['created_by'])) ? explode(',',$request['created_by']):$request['created_by'];                
            $short_name = DB::connection('responsive')->table('users')
                    ->whereIn('id',$user)
                    ->pluck('short_name')->all();
            $get_list_header["User"] = (in_array("0", $user))? ("All".(isset($user[1])? "," :"" ).implode(',',$short_name)) : implode(',',$short_name);
            $hide_col["user"] = 1;
        }
        /*if (isset($request['include_cpt_option'])) {
            if(isset($request['export']))
                $include_cpt_option = explode(',', $request["include_cpt_option"][0]);
            else
                $include_cpt_option = $request['include_cpt_option'];
        } else {
            $include_cpt_option = array();
        } */
        /*if (isset($request['include_cpt_option'])) {
            $include_cpt_option = implode(",", $request['include_cpt_option']);
        }*/
        //
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
        $offset = 0;
        $pages = 0;

        if (isset($request['page'])) {
            $pages = $request['page'];
            $offset = ($pages - 1) * $paginate_count;
            $from = $offset + 1;
            $to = $offset + $paginate_count;
        } else {
            $from = 1;
            $to = $paginate_count;
        }
        //echo("CR Start".$start_date." ## ".$end_date."## ".$dos_start_date."##".$dos_end_date."##".$billing_provider_id."##".$rendering_provider_id."##".$facility_id."##".$insurance_charge."##".$insurance."##".$status_option."##".$include_cpt_option."##".$insurance_charge."##".$insurance."##".$status_option."##".$include_cpt_option."##".$user_ids."##".$reference."##".$offset."##".$paginate_count);
        if ($export == "") {
            
            $recCount = 1;
            $sp_return_result = DB::select('call chargesAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '",  "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '",  "' . $insurance . '",  "' . $status_option . '", "' . $hold_reason . '", "' . $include_cpt_option . '",  "' . $user_ids . '",  "' . $reference . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->charges_count;
			/* \Log::info('SP Charge Count');
			\Log::info($count); */
            $patient_count = $sp_count_return_result[1]->charges_count;
            $last_page = 0;
            if ($count != 0)
                $last_page = ceil($count / $paginate_count);
            if (isset($request['page'])) {
                $pages = $request['page'];
                $offset = ($pages - 1) * $paginate_count;
                $from = $offset + 1;
                $to = $offset + $paginate_count;
                if ($pages == $last_page) {
                    $to = $offset + $count % $paginate_count;
                }
            } else {
                if($paginate_count > $count){
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }
            $recCount = 0;
            $sp_return_result = DB::select('call chargesAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '",  "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '",  "' . $insurance . '",  "' . $status_option . '", "' . $hold_reason . '", "' . $include_cpt_option . '",  "' . $user_ids . '",  "' . $reference . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $pages, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);
            
            //for summary
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $summary = DB::select('call chargesAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '",  "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '",  "' . $insurance . '",  "' . $status_option . '", "' . $hold_reason . '", "' . $include_cpt_option . '",  "' . $user_ids . '",  "' . $reference . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $claim_summary = (array) $summary;

        } else {
            $rendering_provider_id = (is_array($rendering_provider_id) ? implode(",", $rendering_provider_id) : $rendering_provider_id);
            $recCount = 1;
            $sp_return_result = DB::select('call chargesAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '",  "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '",  "' . $insurance . '",  "' . $status_option . '", "' . $hold_reason . '", "' . $include_cpt_option . '",  "' . $user_ids . '",  "' . $reference . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $patient_count = $sp_count_return_result[1]->charges_count;

            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            $sp_return_result = DB::select('call chargesAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $dos_start_date . '",  "' . $dos_end_date . '", "' . $billing_provider_id . '",  "' . $rendering_provider_id . '",  "' . $facility_id . '", "' . $insurance_charge . '",  "' . $insurance . '",  "' . $status_option . '", "' . $hold_reason . '", "' . $include_cpt_option . '",  "' . $user_ids . '",  "' . $reference . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
            $claim_summary = (array) $sp_return_result;
        }
        $claims = $sp_return_result;
    
        if (isset($request['include_cpt_option'])) {
            if(isset($request['export']))
                $include_cpt_option = explode(',', $request["include_cpt_option"]);
            else
                $include_cpt_option = $request['include_cpt_option'];
        } else {
            $include_cpt_option = array();
        }
        $header = $get_list_header;

        $column = $sinpage_charge_amount = $sinpage_claim_arr = $sinpage_total_cpt = $status_opt = $tdate = $chrg_date_opt = $tot_summary = '';
        //------------SUMMARY----------
        $summary = $claim_summary;
        $tot_summary = [];
        $tot_summary['total_patient'] = isset($patient_count)?$patient_count:0;
        $tot_summary['total_claim'] = $tot_summary['total_charge'] = $tot_summary['total_cpt'] = $tot_summary['total_unit'] = 0;

        if(!empty($summary))
            foreach($summary as $claims_list){                
                $tot_summary['total_claim'] += 1;
                if(isset($claims_list->claim_dos_list) && $claims_list->claim_dos_list != '') {
                    $claim_line_item = explode("^^", $claims_list->claim_dos_list);
                    foreach($claim_line_item as $claim_line_item_val){
                        if($claim_line_item_val != ''){
                            $line_item_list = explode("$$", $claim_line_item_val);
                            $claim_cpt = $line_item_list[0];
                            $tot_summary['total_cpt'] += count((array)$claim_cpt);
                            if(($line_item_list[0]) != ''){
                                $units = (isset($line_item_list[9]) && !empty($line_item_list[9])) ? $line_item_list[9] : 0;
                            }
                            $charges   = isset($line_item_list[10])?$line_item_list[10]:0.00;
                            $tot_summary['total_charge'] += $charges;
                            $tot_summary['total_unit'] += $units;
                        }
                    }
                }
            }

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims', 'page', 'header', 'column', 'pagination', 'include_cpt_option', 'sinpage_charge_amount', 'sinpage_claim_arr', 'sinpage_total_cpt', 'status_opt', 'tdate', 'chrg_date_opt', 'tot_summary')));
    }


    /*             * * Search criteria End ** */

    public function getChargeExportResult($request) {
        // @todo - check and update new pmt flow.
        /*
        $get_function_value = ClaimInfoV1::leftJoin('claim_cpt_info_v1', function($join) {
                    $join->on('claims.id', '=', 'claim_cpt_info_v1.claim_id');
                })->with('patient', 'provider_details', 'rendering_provider', 'facility_detail', 'insurance_details', 'billing_provider', 'cpttransactiondetails', 'user')->whereNotIn('claimdoscptdetails.cpt_code', ['patient', 'Patient']);

        $from_date = $to_date = "";

        if ($request['date_option'] == 'enter_date') {
            $from_date = date('Y-m-d', strtotime($request['charge_from_date']));
            $to_date = date('Y-m-d', strtotime($request['charge_to_date']));

            if ($from_date != '' && $to_date != '') {
                if ($request['charge_date_option'] == 'transaction_date') {
                    $get_function_value->whereRaw("(claims.created_at >= '$from_date' and claims.created_at <= '$to_date')");
                } elseif ($request['charge_date_option'] == 'dos_date') {
                    $get_function_value->whereRaw("(claims.date_of_service >= '$from_date' and claims.date_of_service <= '$to_date')");
                }
            }
        } elseif ($request['date_option'] == 'daily') {
            $from_date = $to_date = date('m-d-Y');
            $from_date_query = date('Y-m-d');

            if ($request['charge_date_option'] == 'transaction_date') {
                $get_function_value->whereRaw('claims.created_at = ?', array($from_date_query));
            } elseif ($request['charge_date_option'] == 'dos_date') {
                $get_function_value->whereRaw('claims.date_of_service = ?', array($from_date_query));
            }
        } elseif ($request['date_option'] == 'current_month') {
            if ($request['charge_date_option'] == 'transaction_date') {
                $get_function_value->whereRaw('MONTH(claims.created_at) = MONTH(CURDATE()) AND YEAR(claims.created_at) = YEAR(CURDATE())');
                $get_function_value->whereRaw('MONTH(claims.created_at) = MONTH(CURDATE()) AND YEAR(claims.created_at) = YEAR(CURDATE())');
            } elseif ($request['charge_date_option'] == 'dos_date') {
                $get_function_value->whereRaw('MONTH(claims.date_of_service) = MONTH(CURDATE()) AND YEAR(claims.date_of_service) = YEAR(CURDATE())');
            }
        } elseif ($request['date_option'] == 'previous_month') {
            $prev_month_val = date('m', strtotime(date('Y-m-d') . "-1 month"));
            $prev_year_val = date('Y', strtotime(date('Y-m-d') . "-1 month"));
            if ($request['charge_date_option'] == 'transaction_date') {
                $get_function_value->whereRaw("MONTH(claims.created_at) = $prev_month_val AND YEAR(claims.created_at) = $prev_year_val");
            } elseif ($request['charge_date_option'] == 'dos_date') {
                $get_function_value->whereRaw("MONTH(claims.date_of_service) = $prev_month_val AND YEAR(claims.date_of_service) = $prev_year_val");
            }
        } elseif ($request['date_option'] == 'current_year') {
            if ($request['charge_date_option'] == 'transaction_date') {
                $get_function_value->whereRaw("YEAR(claims.created_at) = YEAR(CURDATE())");
            } elseif ($request['charge_date_option'] == 'dos_date') {
                $get_function_value->whereRaw("YEAR(claims.date_of_service) = YEAR(CURDATE())");
            }
        } elseif ($request['date_option'] == 'previous_year') {
            $prev_year = date('Y', strtotime("-1 year"));
            if ($request['charge_date_option'] == 'transaction_date') {
                $get_function_value->whereRaw("YEAR(claims.created_at) = $prev_year");
            } elseif ($request['charge_date_option'] == 'dos_date') {
                $get_function_value->whereRaw("YEAR(claims.date_of_service) = $prev_year");
            }
        }

        if ($request['status_option'] != 'all') {
            $get_function_value->where('claims.status', $request['status_option']);
        }

        if ($request['insurance_id'] != '' && $request['insurance_id'] != 'all') {
            $get_function_value->where('claims.insurance_id', $request['insurance_id']);
        }

        if ($request["billing_provider_id"] != "" && $request["billing_provider_id"] != "all") {
            $get_function_value->where("claims.billing_provider_id", $request["billing_provider_id"]);
        }
        if ($request["rendering_provider_id"] != "" && $request["rendering_provider_id"] != "all") {
            $get_function_value->where("claims.rendering_provider_id", $request["rendering_provider_id"]);
        }

        if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
            $get_function_value->where('claims.facility_id', $request['facility_id']);
        }

        return $get_function_value;
        */
    }

    ############ Charges Report End ############
    ############ Outstanding AR Report Start ############

    /*             * * Index page start ** */

    public function getClaimsApi() {
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
        /* $insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
          $facilities = Facility::getAllfacilities(); */
        $insurance_type = new Insurancetype();
        $insurance_type = $insurance_type->getInsurancetype();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance_type', 'insurance', 'facilities')));
    }

    /*             * * Index page end ** */

    /*             * * Search page start ** */

    public function getClaimsearchApi($export = '') {
        $request = Request::All();
        $page = ($request["list_type"] == "line_items") ? "lineitemreport" : "normalreport";

        $result = $this->getResult($request); //Get claim list with selected criteria
        $header = $result['header'];
        $column = $result['column'];
        $result['page'] = $page;

        if ($export != "") {
            $claims = $result['claims']->get();
            $result = $this->getClaimExport($claims, $page); //Get claim list with selected criteria
            $claims = $result['list'];
            $exportparam = $result['exportparam'];
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $claims, $export);
        }
        $pagination = '';
        //if($page=="lineitemreport") {
        /* there is a memory size issue while exporting the bulk records.*/

        if ($export == "") {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $claims = $result['claims']->paginate($paginate_count);
            //dd($claims);
            $claim_array = $claims->toArray();
            $pagination_prt = $claims->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
    <li class="disabled"><span>&laquo;</span></li>
    <li class="active"><span>1</span></li>
    <li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $claim_array['total'], 'per_page' => $claim_array['per_page'], 'current_page' => $claim_array['current_page'], 'last_page' => $claim_array['last_page'], 'from' => $claim_array['from'], 'to' => $claim_array['to'], 'pagination_prt' => $pagination_prt);
            $claims_list = json_decode($claims->toJson());
            $claims = $claims_list->data;
        }else {
            $claims = $result['claims']->get();
        }
        //}
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims', 'page', 'header', 'column', 'pagination')));
    }

    /*             * * Search page end ** */

    /*             * * Search criteria start ** */

    public function getResult($request) {
        #### Getting variables from response ####
        $get_function_value = ClaimInfoV1::with('patient', 'provider_details', 'rendering_provider', 'facility_detail', 'insurance_details', 'billing_provider')->where(function($query) {
            return $query->whereRaw("balance_amt!='0.00' and (patient_due>0 or insurance_due>0)");
        });
        //dd($request['original_billed_date']);
        if ($request["list_type"] == "line_items")
            $get_function_value->with('cpttransactiondetails')->whereHas('cpttransactiondetails', function($query) {
                $query->where("cpt_code", '!=', 'Patient');
            });
        $get_list_header = [];
        $hide_col = '';

        #### Conditional Based Listing query ####
        if (isset($request['original_billed_date']) && $request['original_billed_date'] != 'all') {
            $get_title = '';
            $start_date = ($request['from_date'] != '') ? Helpers::dateFormat($request['from_date'], 'datedb') : '';
            $end_date = ($request['to_date'] != '') ? Helpers::dateFormat($request['to_date'], 'datedb') : '';

            if ($start_date != '' && $end_date != '') {
                if ($request['original_billed_date'] == "created_at")
                    $get_title = "Created Date";
                if ($request['original_billed_date'] == "date_of_service")
                    $get_title = "DOS";
                if ($request['original_billed_date'] == "entry_date") {
                    $request['original_billed_date'] = "created_at";
                    $get_title = "Original Billed Date";
                }
                if ($request['original_billed_date'] == "last_submited_date")
                    $get_title = "Last Submission Date";
                if ($request['original_billed_date'] == "paid_date") {
                    $get_function_value->has('payment_claimdetail')->whereHas('payment_claimdetail', function($query)use($start_date, $end_date) {
                        $query->whereBetween("created_at", array($start_date, $end_date));
                    });
                    $get_title = "Paid Date";
                } else {
                    $date_search = $request['original_billed_date'];
                    $get_function_value->whereRaw("(".$date_search." >= '$start_date' and created_at <= '$end_date')");
                }
                $get_list_header[$get_title] = $request['from_date'] . " To " . $request['to_date'];
            }
        }

        if (isset($request['insurance_group']) && $request['insurance_group'] != 'all') {
            $ins_type_id = Helpers::getEncodeAndDecodeOfId($request['insurance_group'], 'decode');
            $get_type_name = Insurancetype::where("id", $ins_type_id)->value("type_name");
            $get_ins_id = Insurance::where("insurancetype_id", $ins_type_id)->pluck("id")->all();
            $get_function_value->whereIn('insurance_id', $get_ins_id);
            $get_list_header["Ins. Type"] = $get_type_name;
        }

        if ($request['insurance_id'] != '' && $request['insurance_id'] != 'all') {
            $get_function_value->where('insurance_id', $request['insurance_id']);
            $get_list_header["Insurance"] = Insurance::getInsuranceName($request['insurance_id']);
            $hide_col["insurance"] = 1;
        }

        if (isset($request['insurance_category']) && $request['insurance_category'] != "all") {
            $get_function_value->where('insurance_category', $request['insurance_category'])->where('self_pay', 'No')->where('insurance_id', '!=', 0);
            $get_list_header["Billed To"] = ucwords($request['insurance_category']);
        }

        if ($request["billing_provider_id"] != "" && $request["billing_provider_id"] != "all") {
            $get_function_value->where("billing_provider_id", $request["billing_provider_id"]);
            $get_list_header["Billing Prov"] = Provider::getProviderNamewithDegree($request['billing_provider_id']);
            $hide_col["billing"] = 1;
        }

        if ($request["rendering_provider_id"] != "" && $request["rendering_provider_id"] != "all") {
            $get_function_value->where("rendering_provider_id", $request["rendering_provider_id"]);
            $get_list_header["Rendering Prov"] = Provider::getProviderNamewithDegree($request['rendering_provider_id']);
            $hide_col["rendering"] = 1;
        }

        if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
            $get_function_value->where('facility_id', $request['facility_id']);
            $get_list_header["Facility Name"] = Facility::getFacilityName($request['facility_id']);
            $hide_col["facility"] = 1;
        }

        if (isset($request['balance_option']) && $request['balance_option'] != 'all') {
            $balance = $request['balance_option'];
            if ($balance == 'insurance') {
                $get_function_value->where('patient_due', "0.00")->where('insurance_due', "!=", "0.00");
            } elseif ($balance == 'patient') {
                $get_function_value->where('insurance_due', "0.00")->where('patient_due', "!=", "0.00");
            } elseif ($balance == 'partially_pending') {
                $get_function_value->where('patient_due', "0.00")->where('insurance_due', "!=", "0.00");
            }
        }

        #### Getting Search Basis response ####
        $result['claims'] = $get_function_value;
        $result['header'] = $get_list_header;
        $result['column'] = $hide_col;
        return $result;
    }

    /*             * * Search criteria end ** */

    public function getClaimExport($request, $page) {
        if ($page == "lineitemreport") {
            $get_req_list = [];
            $get_list_count = 0;
            foreach ($request as $req_key => $req_value) {
                foreach ($req_value->cpttransactiondetails as $line_items_key => $line_items_value) {
                    if ($line_items_value->cpt_code != "Patient") {
                        $get_req = [];
                        $get_req['account_no'] = $req_value->patient->account_no;
                        $get_req['patient_name'] = $req_value->patient->title . ". " . $req_value->patient->last_name . ", " . $req_value->patient->first_name . " " . $req_value->patient->middle_name;
                        $get_req['insurance_id'] = (empty($req_value->insurance_details)) ? "Self" : $req_value->insurance_details->insurance_name;
                        $get_req['billing_provider'] = $req_value->billing_provider->short_name;
                        $get_req['rendering_provider'] = $req_value->rendering_provider->short_name;
                        $get_req['facility_detail'] = $req_value->facility_detail->facility_name;
                        $get_req['facility_detail'] = $req_value->facility_detail->facility_name;
                        $get_req['dos_from'] = $line_items_value->dos_from;
                        $get_req['dos_to'] = $line_items_value->dos_to;
                        $get_req['cpt'] = $line_items_value->cpt_code;
                        $get_req['billed'] = Helpers::priceFormat($line_items_value->charge);
                        $get_req['ins_paid'] = Helpers::priceFormat($line_items_value->insurance_paid);
                        $get_req['pat_paid'] = Helpers::priceFormat($line_items_value->patient_paid);
                        $get_req['total_adjusted'] = Helpers::priceFormat($line_items_value->adjustment);
                        $get_req['balance_amt'] = Helpers::priceFormat($line_items_value->balance);
                        $get_req_list[$get_list_count] = $get_req;
                        $get_list_count++;
                    }
                }
                $get_res = $get_req_list;
            }
            $result['exportparam'] = array(
                'filename' => 'Outstanding Claims Report',
                'heading' => '',
                'fields' => array(
                    'account_no' => 'Act No',
                    'patient_name' => 'Patient Name',
                    'insurance_id' => 'Billed To',
                    'billing_provider' => 'Billing',
                    'rendering_provider' => 'Rendering',
                    'facility_detail' => 'Facility',
                    'dos_from' => "DOS From",
                    'dos_to' => "DOS To",
                    'cpt' => "CPT",
                    'billed' => "Billed($)",
                    'ins_paid' => "Ins Paid($)",
                    'pat_paid' => "Pat Paid($)",
                    'total_adjusted' => "Adj($)",
                    'balance_amt' => "Total Bal($)"
            ));
            $result['list'] = json_decode(json_encode($get_res));
        } else {
            $result['exportparam'] = array(
                'filename' => 'Outstanding Claims Report',
                'heading' => '',
                'fields' => array(
                    'Act No' => array('table' => 'patient', 'column' => 'account_no', 'label' => 'Act No'),
                    'Patient Name' => array('table' => 'patient', 'column' => ['last_name', 'first_name'], 'label' => 'Patient Name'),
                    'claim_number' => 'Claim No',
                    'DOS' => array('table' => '', 'column' => 'date_of_service', 'use_function' => ['App\Http\Helpers\Helpers', 'dateFormat'], 'label' => 'DOS'),
                    'Insurance' => array('table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Insurance', 'InsuranceName'], 'label' => 'Insurance'),
                    'Billing' => array('table' => 'billing_provider', 'column' => ['short_name', 'provider_name'], 'label' => 'Billing'),
                    'Rendering' => array('table' => 'rendering_provider', 'column' => ['short_name', 'provider_name'], 'label' => 'Rendering'),
                    'Facility' => array('table' => 'facility_detail', 'column' => ['short_name', 'facility_name'], 'label' => 'Facility'),
                    'Charges' => array('table' => '', 'column' => 'total_charge', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Billed($)'),
                    'Ins Paid' => array('table' => '', 'column' => 'insurance_paid', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Ins Paid($)'),
                    'Pat Paid' => array('table' => '', 'column' => 'pateint_paid', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Pat Paid($)'),
                    'Adj' => array('table' => '', 'column' => 'total_adjusted', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Adj($)'),
                    'Total Bal' => array('table' => '', 'column' => 'balance_amt', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Total Bal($)'),
            ));
            $result['list'] = $request;
        }
        return $result;
    }

    ############ Outstanding AR Report End ############
    ############ Payment Report Start ############

    /*             * * Index page start ** */

    public function getPaymentsApi() {
        /* $insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->lists('insurance_name', 'id');
          $facilities = Facility::getAllfacilities(); */
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
     
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('payment_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities','search_fields','searchUserData')));
    }

    /*             * * Index page end ** */

    /*             * * Search page start ** */

    public function getPaymentSearchApi($export = '', $data = '') {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', -1);
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        if (!isset($request['insurance_charge'])) {
            $request['insurance_charge'] = 'self';
        }  
        $result = $this->getPaymentResult($request); //Get payment list with selected criteria
        $header = $result['header'];
        $column = $result['column'];
        $page = $result['page'];
        $pagination = '';
        $patient_wallet_balance = '';
        $payment = [];
        // Define for pagination count for per page
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');

        // Payment Summary
        if (isset($request['insurance_charge']))
        if ($request['insurance_charge']=='detail') {
            // For patient payments - Detailed transaction
            $pmt = $result['payments']->selectRaw('null as used');
            //$wallet = clone $result['patients'];
            if (isset($request['pmt_mode'])){
                // Request is string or array based on condition added
                // Rev. 1 Ref: MR-2687 - 19-Aug-2019 Anjukaselvan
                $payment_mode = (isset($request['export']) && is_string($request['pmt_mode']))?explode(',',$request['pmt_mode']):$request['pmt_mode'];
                //$wallet = $result['wallet']->selectRaw('-1*pmt_wallet_v1.amount as used')->where('pmt_info_v1.source_id', '!=', 0)->where('pmt_wallet_v1.amount','>',0)->where('pmt_wallet_v1.wallet_Ref_Id','!=',0)->whereIn("pmt_info_v1.pmt_mode", $payment_mode);
                //$patient = $result['patients']->selectRaw('null as used')->where('source_id',0)->whereIn("pmt_mode", $payment_mode);
            }else{
                //$wallet = $result['wallet']->selectRaw('-1*pmt_wallet_v1.amount as used')->where('pmt_info_v1.source_id', '!=', 0)->where('pmt_wallet_v1.amount','>',0)->where('pmt_wallet_v1.wallet_Ref_Id','!=',0);
                //$patient = $result['patients']->selectRaw('null as used')->where('source_id',0);
            }
            if(isset($request["reference"])) {
                if ($request["reference"] != "") {
                    $pmt->whereHas('pmt_info', function($q) use($request) {
                        $q->where('reference','like','%' .$request["reference"].'%');
                    });
                    //$wallet->where('pmt_info_v1.reference','like','%' .$request["reference"].'%');
                    //$patient->where('pmt_info_v1.reference','like','%' .$request["reference"].'%');
                }
            }
            $payment=$pmt->orderBy('created_at','desc')->get();
                    //->union($wallet->getQuery())->union($result['patients']->getQuery())
        } else {
            // For insurance and patient payments
        //dd($result['payments']->orderBy('id','desc')->toSql());
            if($request['insurance_charge']=='insurance') {
                $payment = $result['payments']->orderBy('pmt_claim_tx_v1.id','desc')->get();
            }else{
                $payment = $result['payments']->orderBy('id','desc')->get();
            }
        }//dd($payment);
        // Array initialize for summary calculation
        $dataArr['insPmt'] = $dataArr['patPmt'] = $dataArr['wrtOff'] = $dataArr['insRefund'] = $dataArr['patRefund'] = $dataArr['other'] = $dataArr['allowed']
= $dataArr['deduction'] = $dataArr['copay'] = $dataArr['coins'] = $dataArr['check'] = $dataArr['cash'] = $dataArr['mo'] = $dataArr['cc'] = $dataArr['eft'] = 0;
        // Summary calculation
         foreach($payment as $list){
            $dataArr['allowed'] += $list->total_allowed;
            $dataArr['deduction'] += $list->total_deduction;
            $dataArr['copay'] += $list->total_copay;
            $dataArr['coins'] += $list->total_coins;
            $dataArr['other'] += $list->total_withheld;
            $dataArr['wrtOff'] += $list->total_writeoff;
            if ($request['insurance_charge']=='insurance') {
                $pmt =$list;  // $list->pmt_info;
                $total_paid='total_paid';
            }elseif($request['insurance_charge']=='detail'){
                $pmt = $list;
                $total_paid='total_paid';
            } else {
                $pmt = $list;
                $total_paid='pmt_amt';
            }
            if(isset($pmt->pmt_mode))
            if($pmt->pmt_mode=='Check')
                $dataArr['check'] += $list->$total_paid;
            elseif($pmt->pmt_mode=='Cash')
                $dataArr['cash'] += $list->$total_paid;
            elseif($pmt->pmt_mode=='EFT')
                $dataArr['eft'] += $list->$total_paid;
            elseif($pmt->pmt_mode =='Money Order')
                $dataArr['mo'] += $list->$total_paid;
            else
                $dataArr['cc'] += $list->$total_paid;
            if(isset($pmt->pmt_type))
            if($pmt->pmt_type == 'Payment'){
                if($pmt->pmt_method == 'Insurance')
                    $dataArr['insPmt'] += $list->$total_paid;
                if($pmt->pmt_method == 'Patient')
                    $dataArr['patPmt'] += $list->$total_paid;
            }
            if ($request['insurance_charge']=='detail')
                if($pmt->pmt_type == 'Credit Balance' && $pmt->pmt_method == 'Patient')
                    $dataArr['patPmt'] += $list->$total_paid*(-1);
            if ($request['insurance_charge']=='self')
                if($pmt->pmt_type == 'Credit Balance' && $pmt->pmt_method == 'Patient')
                    $dataArr['patPmt'] += $list->$total_paid;

            if(isset($pmt->pmt_type))
            if($pmt->pmt_type == 'Refund'){
                if($pmt->pmt_method == 'Insurance'){
                    $dataArr['insRefund'] += $list->$total_paid;
                    $dataArr['insPmt'] += $dataArr['insRefund'];
                }
                if($pmt->pmt_method == 'Patient')
                    $dataArr['patRefund'] += $list->$total_paid;
            }
        }
   //     dd($dataArr);
        // For export
        if (isset($request['export']) && $request['export'] == 'pdf') {
            $payments = $payment;
            return Response::json(array('status' => 'success', 'message' => null,
                'data' => compact('payments','page', 'header', 'column', 'patient_wallet_balance','dataArr')));
        } elseif (isset($request['export']) && $request['export'] == 'xlsx') {
            $payments = $payment;
            return Response::json(array('status' => 'success', 'message' => null,
                'data' => compact('payments','page', 'header', 'column', 'patient_wallet_balance','dataArr')));
        }
        // Pagination
        if (isset($request['insurance_charge']))
        if ($request['insurance_charge']=='detail') {
            // Pagination for patient payments - Detailed transaction
            $p = Input::get('page', 1);
            $paginate = $paginate_count;

            $offSet = ($p * $paginate) - $paginate;
            $slice = array_slice($payment->toArray(), $offSet, $paginate,true);
            $payments = new \Illuminate\Pagination\LengthAwarePaginator($slice, count($payment), $paginate,$p,['path'=>Request::url()]);
        } else {
            // Pagination for insurance and patient payments
            $payments = $result['payments']->paginate($paginate_count);
        }

        $claim_id = $payment->pluck("claim_id")->first();
        $payment_array = $payments->toArray();
        // Pagination navigation
        $pagination_prt = $payments->render();
        // Default pagination if single page
        if ($pagination_prt == '')
            $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
        $pagination = array('total' => $payment_array['total'], 'per_page' => $payment_array['per_page'], 'current_page' => $payment_array['current_page'], 'last_page' => $payment_array['last_page'], 'from' => $payment_array['from'], 'to' => $payment_array['to'], 'pagination_prt' => $pagination_prt);
        $payments_list = json_decode($payments->toJson());
        $payments = $payments_list->data;
        $total = 0;

        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('payments', 'total', 'page', 'header', 'column', 'pagination', 'patient_wallet_balance','dataArr')));
    }

    /*             * * Search criteria start ** */
    public function getPaymentResult($request) {
        // For handle undefined index issue

        if (isset($request['insurance_charge'])) {
            if ($request['insurance_charge']=='detail') {
                // Query for patient payments - detailed tranaction
                $get_function_value = PMTClaimTXV1::with('pmt_info','fin','claim','cpt_fin','claim_txn_desc_v1','claim_patient_det')                   
                    ->whereRaw("pmt_method = 'Patient'")->selectRaw('id, payment_id, patient_id,pmt_type,pmt_method,claim_id as source_id,claim_id, null as reference,total_allowed, total_deduction, total_copay, total_coins, total_withheld, total_writeoff, total_paid, posting_date, created_at, created_by, null as pmt_mode');

                if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance', 'Refund']);
                } else {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance']);
                }
                //$get_function_value_patient = PMTInfoV1::with('claim','patient','creditCardDetails','eftDetails','checkDetails','created_user','payment_claim_detail')->whereIn('pmt_type', ['Payment','Credit Balance'])->whereRaw("pmt_method = 'Patient'")->selectRaw('id,id as payment_id, patient_id,pmt_type,pmt_method,source_id, source_id as claim_id, reference, null as total_allowed, null as total_deduction, null as total_copay, null as total_coins, null as total_withheld, null as total_writeoff, pmt_amt as total_paid, null as posting_date, created_at, created_by,pmt_mode');
                //$wallet = PMTInfoV1::leftJoin('pmt_wallet_v1','pmt_wallet_v1.pmt_info_id','=', 'pmt_info_v1.id')->with('claim','patient','creditCardDetails','eftDetails','checkDetails','created_user','payment_claim_detail')->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])->whereRaw("pmt_info_v1.pmt_method = 'Patient'")->selectRaw('pmt_info_v1.id,pmt_info_v1.id as payment_id, pmt_info_v1.patient_id,pmt_info_v1.pmt_type,pmt_info_v1.pmt_method,pmt_info_v1.source_id, pmt_info_v1.source_id as claim_id, pmt_info_v1.reference, null as total_allowed, null as total_deduction, null as total_copay, null as total_coins, null as total_withheld, null as total_writeoff, pmt_info_v1.pmt_amt as total_paid, null as posting_date, pmt_info_v1.created_at, pmt_info_v1.created_by,pmt_info_v1.pmt_mode');
            } elseif ($request['insurance_charge']=='insurance') {
                // Query for insurance payments
                $get_function_value = PMTClaimTXV1::leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id');
                if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {
                    $get_function_value->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
                } else {
                    $get_function_value->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
                }
                
                
                $get_function_value = $get_function_value->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                ->leftJoin('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','pmt_claim_tx_v1.claim_id')
                ->leftJoin('claim_tx_desc_v1','claim_tx_desc_v1.txn_id','=','pmt_claim_tx_v1.id')
                ->leftJoin('patients','patients.id','=','pmt_claim_tx_v1.patient_id')
                ->leftJoin('providers as billing','billing.id','=','claim_info_v1.billing_provider_id')
                ->leftJoin('providers as rendering','rendering.id','=','claim_info_v1.rendering_provider_id')
                ->leftJoin('facilities','facilities.id','=','claim_info_v1.facility_id')
                ->leftJoin('pmt_check_info_v1','pmt_check_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_card_info_v1','pmt_card_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_eft_info_v1','pmt_eft_info_v1.id','=','pmt_info_v1.pmt_mode_id')
                ->leftJoin('pmt_claim_cpt_tx_v1','pmt_claim_cpt_tx_v1.pmt_claim_tx_id','=','pmt_claim_tx_v1.id')
                ->selectRaw('pmt_claim_tx_v1.id, pmt_claim_tx_v1.*,
                 patients.account_no,
                 patients.first_name,
                 patients.last_name,
                 patients.middle_name,
                 patients.title,
                 claim_info_v1.date_of_service,
                 claim_info_v1.claim_number,
                 claim_info_v1.total_charge,
                 pmt_info_v1.pmt_mode,
                 pmt_info_v1.reference,
                 pmt_check_info_v1.check_no,
                 pmt_check_info_v1.check_date,
                 pmt_eft_info_v1.eft_no,
                 pmt_eft_info_v1.eft_date,
                 pmt_card_info_v1.card_last_4,
                 pmt_card_info_v1.created_at as card_date,
                 billing.short_name as billing_short_name,
                 billing.provider_name as billing_full_name,
                 rendering.short_name as rendering_short_name,
                 rendering.provider_name as rendering_full_name,
                 facilities.facility_name as facility_name,
                 facilities.short_name as facility_short_name'
             )->groupBy('pmt_claim_tx_v1.id');
            } elseif ($request['insurance_charge']=='all') {
                // Query for insurance payments
                $get_function_value = PMTClaimTXV1::with('pmt_info','fin','claim','cpt_fin','claim_txn_desc_v1','claim_patient_det');
                if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance', 'Refund']);
                } else {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance']);
                }
            } else {
                // Query for patient payments
                $get_function_value = PMTInfoV1::with('claim','patient','creditCardDetails','eftDetails','checkDetails','created_user','payment_claim_detail');
                if(isset($request['include_refund']) && $request['include_refund'] == 'Yes') {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance', 'Refund']);
                } else {
                    $get_function_value->whereIn('pmt_type', ['Payment','Credit Balance']);
                }
            }
        }

//        dd($request);
        // Initialize search parameters headers and view format
        $page = $facility_id = $billing_provider_id = $rendering_provider_id = $pmt_mode = $ins_id = $user = '';
        $get_list_header = [];
        $hide_col = [];
        $hide_col["payment_type"] = 1;
        // Filter by Facility
        if (isset($request['facility_id'])) {
            if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
                $facility_id = (isset($request['export']))?explode(',',$request['facility_id']):$request['facility_id'];
                if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereIn('claim_info_v1.facility_id', $facility_id);
                }else{
                    $get_function_value->whereHas('claim', function($q)use($facility_id) {
                        $q->whereIn('facility_id', $facility_id);
                    });
                }
                /*if(isset($get_function_value_patient)){
                    $get_function_value_patient->whereHas('claim', function($q)use($facility_id) {
                        $q->whereIn('facility_id', $facility_id);
                    });
                    $wallet->whereHas('claim', function($q)use($facility_id) {
                        $q->whereIn('facility_id', $facility_id);
                    });
                }*/
                $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', $facility_id)->get()->toArray();
                $get_list_header["Facility"] =  @array_flatten($facility)[0];
                $hide_col["facility"] = 1;
            }
        }

        // Filter by Billing provider
        if (isset($request['billing_provider_id'])) {
            if ($request["billing_provider_id"] != "" && $request["billing_provider_id"] != "all") {
                $billing_provider_id = (isset($request['export']))?explode(',',$request['billing_provider_id']):$request['billing_provider_id'];
                if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereIn('claim_info_v1.billing_provider_id', $billing_provider_id);
                }else{
                    $get_function_value->whereHas('claim', function($q)use($billing_provider_id) {
                        $q->whereIn('billing_provider_id', $billing_provider_id);
                    });
                }
                /*if(isset($get_function_value_patient)){
                    $get_function_value_patient->whereHas('claim', function($q)use($billing_provider_id) {
                        $q->whereIn('billing_provider_id', $billing_provider_id);
                    });
                    $wallet->whereHas('claim', function($q)use($billing_provider_id) {
                        $q->whereIn('billing_provider_id', $billing_provider_id);
                    });
                }*/
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $billing_provider_id)->get()->toArray();
                $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
                $hide_col["billing"] = 1;
            }
        }

        // Filter by Rendering provider
        if (isset($request['rendering_provider_id'])) {
            if ($request["rendering_provider_id"] != "" && $request["rendering_provider_id"] != "all") {
                $rendering_provider_id = (isset($request['export']))?explode(',',$request['rendering_provider_id']):$request['rendering_provider_id'];
                if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereIn('claim_info_v1.rendering_provider_id', $rendering_provider_id);
                }else{
                    $get_function_value->whereHas('claim', function($q)use($rendering_provider_id) {
                        $q->whereIn('rendering_provider_id', $rendering_provider_id);
                    });
                }
                /*if(isset($get_function_value_patient)){
                    $get_function_value_patient->whereHas('claim', function($q)use($rendering_provider_id) {
                        $q->whereIn('rendering_provider_id', $rendering_provider_id);
                    });
                    $wallet->whereHas('claim', function($q)use($rendering_provider_id) {
                        $q->whereIn('rendering_provider_id', $rendering_provider_id);
                    });
                }*/
                $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $rendering_provider_id)->get()->toArray();
                $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
                $hide_col["rendering"] = 1;
            }
        }
        // Filter by Reference
        if(isset($request["reference"])) {
            if ($request["reference"] != "") {
                $ref = $request['reference'];
                if ($request['insurance_charge']=='insurance') {
                    $get_function_value->where('pmt_info_v1.reference','like','%' .$ref.'%');
                }elseif ($request['insurance_charge']=='self') {
                    $get_function_value->where('pmt_info_v1.reference','like','%' .$ref.'%');
                }
                $get_list_header["Reference"] = $ref;
                $hide_col["reference"] = 1;
            }
        }
        // Filter by User
        if(isset($request["created_by"]) && !empty($request["created_by"])) {
            if ($request["created_by"] != ""  && is_array($request["created_by"])) {
                // Request is string or array based on condition added
                // Rev. 1 Ref: MR-2618 - 03-Aug-2019 Ravi
                $user = (isset($request['export']) || is_string($request['created_by'])) ? explode(',',$request['created_by']):$request['created_by'];
                    $short_name = DB::connection('responsive')->table('users')
                            ->whereIn('id',$user)
                            ->pluck('short_name')->all();
                    if($request['insurance_charge']=='insurance') {
                        $get_function_value->whereIn('pmt_claim_tx_v1.created_by',$user);
                    }else{
                        $get_function_value->whereIn('created_by',$user);
                    }
                    /*if(isset($get_function_value_patient)){
                        $get_function_value_patient->whereIn('created_by',$user);
                        $wallet->whereIn('pmt_info_v1.created_by',$user);
                    }*/
                $get_list_header["User"] = implode(',', $short_name);
                $hide_col["user"] = 1;
            }
        }
        $start_date = $end_date = $start_date_dos = $end_date_dos = '';
        $practice_timezone = Helpers::getPracticeTimeZone();
        // Filter by transaction date
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='transaction_date') ) {
            if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date'])) {
                $exp = explode("-",$request['select_transaction_date']);
                $start_date = date("Y-m-d",strtotime($exp[0]));
                $end_date = date("Y-m-d",strtotime($exp[1]));
                $get_list_header["Transaction Date"] = date("m/d/y",strtotime($start_date)) . "  To " . date("m/d/y",strtotime($end_date));
                if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'"); 
                }else{
                    $get_function_value->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$end_date'"); 
                }
				
				/* if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'"); 
                }else{
                    $get_function_value->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'"); 
                } */
				
				
                /*if(isset($get_function_value_patient)){                    
                    $get_function_value_patient->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'"); 
                    $wallet->whereRaw("DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
                }*/
            }
        }
        // Filter by date of service
        if(isset($request['choose_date']) && !empty($request['choose_date']) &&
            ($request['choose_date']=='all' || $request['choose_date']=='DOS') ) {
            if(isset($request['select_date_of_service']) && !empty($request['select_date_of_service'])) {
                $exp = explode("-",$request['select_date_of_service']);
                $start_date_dos = date("Y-m-d",strtotime($exp[0]));
                $end_date_dos = date("Y-m-d",strtotime($exp[1]));
                if($request['insurance_charge']=='insurance') {
                    $get_function_value->whereRaw("claim_info_v1.date_of_service >= '$start_date_dos' and claim_info_v1.date_of_service <= '$end_date_dos'");
                }else{
                    $get_function_value->whereHas('claim', function($q) use($start_date_dos,$end_date_dos) {
                                $q->whereRaw("date_of_service >= '$start_date_dos' and date_of_service <= '$end_date_dos'");});
                }
                /*if(isset($get_function_value_patient)){
                    $get_function_value_patient->whereHas('claim', function($q) use($start_date_dos,$end_date_dos) {
                                $q->whereRaw("date_of_service >= '$start_date_dos' and date_of_service <= '$end_date_dos'");});
                    $wallet->whereHas('claim', function($q) use($start_date_dos,$end_date_dos) {
                                $q->whereRaw("date_of_service >= '$start_date_dos' and date_of_service <= '$end_date_dos'");});
                }*/
                $get_list_header["DOS"] = date("m/d/Y",strtotime($start_date_dos)) . "  To " . date("m/d/Y",strtotime($end_date_dos));
            }
        }
        // Filter by payer detail
        if (isset($request['insurance_charge'])) {
            if ($request['insurance_charge']=='detail') {
                $get_function_value = $get_function_value->whereRaw("pmt_method = 'Patient'");
                //$get_function_value_patient = $get_function_value_patient->whereRaw("pmt_method = 'Patient'");
                //$wallet = $wallet->whereRaw("pmt_info_v1.pmt_method = 'Patient'");
                $get_list_header["Payer"] = "Patient Payments – Detailed Transaction";
                $hide_col["patient"] = 1;
            }
        }
        // Filter by payer detail
        if (isset($request['insurance_charge'])) {
            if ($request['insurance_charge']=='self') {
                $get_function_value = $get_function_value->whereRaw("pmt_method = 'Patient'");
                //$get_function_value_patient = $get_function_value_patient->whereRaw("pmt_method = 'Patient'");
                $get_list_header["Payer"] = "Patient Payments";
                $hide_col["patient"] = 1;
            }
        }

        // To set default view for page
        $page = "normal";

        // Filter by payer all insurance
        if (isset($request['insurance_charge'])) {
            if ($request['insurance_charge'] == 'insurance') {
                if(isset($request['insurance_id'])){
                    $ins_id = (isset($request['export']))?explode(',',$request['insurance_id']) : $request['insurance_id'];
                    $get_function_value = $get_function_value->whereIn('pmt_claim_tx_v1.payer_insurance_id',$ins_id);
                    $get_list_header["Payer"] = "Insurance Only";
                    $get_list_header["Insurance"] = @array_flatten(Insurance::selectRaw("GROUP_CONCAT(insurance_name SEPARATOR ' , ') as insurance_name")->whereIn('id',$ins_id)->get()->toArray())[0];
                 }else{
                     $get_function_value = $get_function_value->whereRaw("pmt_claim_tx_v1.pmt_method = 'Insurance'");
                     $get_list_header["Payer"] = "Insurance Only";
                     $get_list_header["Insurance"] = "All";
                 }
                $hide_col["insurance"] = 1;
            }
        }
        // Filter by payment mode
        if (isset($request['pmt_mode'])){
            // Request is string or array based on condition added
            // Rev. 1 Ref: MR-2618 - 03-Aug-2019 Ravi
            $pmt_mode = (isset($request['export']))? explode(',', $request['pmt_mode']) : $request['pmt_mode'];
            if (isset($request['insurance_charge']) && $request['insurance_charge'] == 'self') {
                $get_function_value = $get_function_value->whereIn("pmt_mode", $pmt_mode);
            }elseif (isset($request['insurance_charge']) && $request['insurance_charge']=='detail'){
                $get_function_value->whereHas('pmt_info', function($q) use($pmt_mode)  {
                            $q->whereIn("pmt_mode", $pmt_mode);
                        });
                //$get_function_value_patient->whereIn("pmt_mode", $pmt_mode);
                //$wallet->whereIn("pmt_info_v1.pmt_mode", $pmt_mode);
            }else{
                $get_function_value->whereIn("pmt_info_v1.pmt_mode", $pmt_mode);
            }
            $get_list_header["Payment"] = implode(',',$pmt_mode);
            $hide_col["pmt_mode"] = 1;
        }
        // Skip to Deleted check
        if (isset($request['insurance_charge']) && $request['insurance_charge']=='insurance') {
           /* $get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
        }elseif (isset($request['insurance_charge']) && $request['insurance_charge']=='detail'){
                /*$get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
            //$get_function_value_patient->whereRaw('void_check is null');
            //$wallet->whereRaw('pmt_info_v1.void_check is null');
        }elseif (isset($request['insurance_charge']) && $request['insurance_charge']=='all') {
            /*$get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
        }else{
                $get_function_value->whereRaw('void_check is null');
        }

        // Filter by options
        if (isset($request['insurance_charge']) &&  $request['insurance_charge']=='insurance') {
            if (isset($request['Options'])) {
                if($request['Options'] == 'zero_payments') {
                    $p = "lineitem";
                    $hide_col["zero_payments"] = 1;
                    $get_list_header["Show"] = "Zero Payments";
                    $get_function_value->orWhere(function($qry) use ($request,$start_date,$end_date,$start_date_dos,$end_date_dos,$facility_id,$billing_provider_id,$rendering_provider_id,$pmt_mode,$ins_id,$user,$practice_timezone){
                        $qry->whereIn('pmt_claim_tx_v1.pmt_method',['Insurance','Patient'])->whereNotIn('pmt_claim_tx_v1.pmt_type', ['Refund','Adjustment'])->where('pmt_claim_tx_v1.total_paid','<=',0);
                        // Filter by payer
                        if (isset($request['insurance_charge']))
                            if ($request['insurance_charge']=='detail') {
                                $qry->whereRaw("pmt_method = 'Patient'");
                            } elseif ($request['insurance_charge'] == 'insurance') {
                                if(isset($request['insurance_id'])){
                                     $qry->whereIn('pmt_claim_tx_v1.payer_insurance_id',$ins_id);
                                 }else{
                                     $qry->whereRaw("pmt_claim_tx_v1.pmt_method = 'Insurance'");
                                 }
                            }
                        // Filter by transaction date
                        if(!empty($start_date) && !empty($end_date)){
                            $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                        } 
						
						/* if(!empty($start_date) && !empty($end_date)){
                            $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                        } */
                        // Filter by dos date
                        if(!empty($start_date_dos) && !empty($end_date_dos)) {
                            $qry->whereRaw("claim_info_v1.date_of_service >= '$start_date_dos' and claim_info_v1.date_of_service <= '$end_date_dos'");
                        }
                        // Filter by facility
                        if (isset($request['facility_id']))
                            if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.facility_id', $facility_id);
                            }
                        // Filter by billing provider
                        if (isset($request['billing_provider_id']))
                            if ($request['billing_provider_id'] != '' && $request['billing_provider_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.billing_provider_id', $billing_provider_id);
                            }
                        // Filter by rendering provider
                        if (isset($request['rendering_provider_id']))
                            if ($request['rendering_provider_id'] != '' && $request['rendering_provider_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.rendering_provider_id', $rendering_provider_id);
                            }
                        // Filter by Reference
                        if(isset($request["reference"]))
                            if ($request["reference"] != "") {
                                $ref = $request['reference'];
                                $qry->where('pmt_info_v1.reference','like','%' .$ref.'%');
                            }

                        // Filter by User
                        if(isset($request["created_by"]))
                            if ($request["created_by"] != ""  && is_array($request["created_by"])) {
                                $user_id = DB::connection('responsive')->table('users')
                                        ->whereIn('id',$user)
                                        ->pluck('short_name')->all();
                                $qry->whereIn('pmt_claim_tx_v1.created_by',$user_id);
                            }
                        // Filter by payment mode
                        if (isset($request['pmt_mode'])){
                            $qry->whereIn("pmt_info_v1.pmt_mode", $pmt_mode);
                        }
                    });
                } else {
                    $get_list_header["Show"] = "Line Item Payments";
                    $get_function_value->where('total_paid','!=',0)->orWhere(function($qry)
                        use ($request,$start_date,$end_date,$start_date_dos,$end_date_dos,$facility_id,$billing_provider_id,$rendering_provider_id,$pmt_mode,$ins_id,$user,$practice_timezone)
                        {
                        $qry->whereIn('pmt_claim_tx_v1.pmt_method',['Insurance','Patient'])->whereNotIn('pmt_claim_tx_v1.pmt_type', ['Refund','Adjustment'])->where('pmt_claim_tx_v1.total_paid','<',0);
                        // Filter by payer
                        if (isset($request['insurance_charge']))
                            if ($request['insurance_charge']=='detail') {
                                $qry->whereRaw("pmt_method = 'Patient'");
                            } elseif ($request['insurance_charge'] == 'insurance') {
                                if(isset($request['insurance_id'])){
                                     $qry->whereIn('pmt_claim_tx_v1.payer_insurance_id',$ins_id);
                                 }else{
                                     $qry->whereRaw("pmt_claim_tx_v1.pmt_method = 'Insurance'");
                                 }
                            }
                        // Filter by transaction date
                        if(!empty($start_date) && !empty($end_date)){
                            $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                        }
						
						/* if(!empty($start_date) && !empty($end_date)){
                            $qry->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                        } */
                        // Filter by dos date
                        if(!empty($start_date_dos) && !empty($end_date_dos)) {
                            $qry->whereRaw("claim_info_v1.date_of_service >= '$start_date_dos' and claim_info_v1.date_of_service <= '$end_date_dos'");
                        }
                        // Filter by facility
                        if (isset($request['facility_id']))
                            if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.facility_id', $facility_id);
                            }
                        // Filter by billing provider
                        if (isset($request['billing_provider_id']))
                            if ($request['billing_provider_id'] != '' && $request['billing_provider_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.billing_provider_id', $billing_provider_id);
                            }
                        // Filter by rendering provider
                        if (isset($request['rendering_provider_id']))
                            if ($request['rendering_provider_id'] != '' && $request['rendering_provider_id'] != 'all') {
                                $qry->whereIn('claim_info_v1.rendering_provider_id', $rendering_provider_id);
                            }
                        // Filter by Reference
                        if(isset($request["reference"]))
                            if ($request["reference"] != "") {
                                $ref = $request['reference'];
                                    $qry->where('pmt_info_v1.reference','like','%' .$ref.'%');
                            }

                        // Filter by User
                        if(isset($request["created_by"]))
                            if ($request["created_by"] != ""  && is_array($request["created_by"])) {
                                $user_id = DB::connection('responsive')->table('users')
                                        ->whereIn('id',$user)
                                        ->pluck('short_name')->all();
                                $qry->whereIn('pmt_claim_tx_v1.created_by',$user_id);
                            }
                        // Filter by payment mode
                        if (isset($request['pmt_mode'])){
                            $qry->whereIn("pmt_info_v1.pmt_mode", $pmt_mode);
                        }
                    });
                }
                if ($request['Options'] == 'line_items') {
                    $page = "lineitem";
                }
            }else{
                $get_function_value->where('total_paid','!=',0)->orWhere(function($qry) use ($request,$start_date,$end_date,$start_date_dos,$end_date_dos,$facility_id,$billing_provider_id,$rendering_provider_id,$pmt_mode,$ins_id,$user,$practice_timezone){
                    $qry->whereIn('pmt_claim_tx_v1.pmt_method',['Insurance','Patient'])->whereNotIn('pmt_claim_tx_v1.pmt_type', ['Refund','Adjustment'])->where('pmt_claim_tx_v1.total_paid','<',0);
                    // Filter by payer
                    if (isset($request['insurance_charge']))
                        if ($request['insurance_charge']=='detail') {
                            $qry->whereRaw("pmt_method = 'Patient'");
                        } elseif ($request['insurance_charge'] == 'insurance') {
                            if(isset($request['insurance_id'])){
                                 $qry->whereIn('pmt_claim_tx_v1.payer_insurance_id',$ins_id);
                             }else{
                                 $qry->whereRaw("pmt_claim_tx_v1.pmt_method = 'Insurance'");
                             }
                        }
                    // Filter by transaction date
                    if(!empty($start_date) && !empty($end_date)){
                        $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                    }
					
					/* if(!empty($start_date) && !empty($end_date)){
                        $qry->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'");
                    } */
                    // Filter by dos date
                    if(!empty($start_date_dos) && !empty($end_date_dos)) {
                        $qry->whereRaw("claim_info_v1.date_of_service >= '$start_date_dos' and claim_info_v1.date_of_service <= '$end_date_dos'");
                    }
                    // Filter by facility
                    if (isset($request['facility_id']))
                        if ($request['facility_id'] != '' && $request['facility_id'] != 'all') {
                            $qry->whereIn('claim_info_v1.facility_id', $facility_id);
                        }
                    // Filter by billing provider
                    if (isset($request['billing_provider_id']))
                        if ($request['billing_provider_id'] != '' && $request['billing_provider_id'] != 'all') {
                            $qry->whereIn('claim_info_v1.billing_provider_id', $billing_provider_id);
                        }
                    // Filter by rendering provider
                    if (isset($request['rendering_provider_id']))
                        if ($request['rendering_provider_id'] != '' && $request['rendering_provider_id'] != 'all') {
                            $qry->whereIn('claim_info_v1.rendering_provider_id', $rendering_provider_id);
                        }
                    // Filter by Reference
                    if(isset($request["reference"]))
                        if ($request["reference"] != "") {
                            $ref = $request['reference'];
                            $qry->where('pmt_info_v1.reference','like','%' .$ref.'%');
                        }

                    // Filter by User
                    if(isset($request["created_by"]))
                        if ($request["created_by"] != ""  && is_array($request["created_by"])) {
                            $user_id = DB::connection('responsive')->table('users')
                                    ->whereIn('id',$user)
                                    ->pluck('short_name')->all();
                            $qry->whereIn('pmt_claim_tx_v1.created_by',$user_id);
                        }
                    // Filter by payment mode
                    if (isset($request['pmt_mode'])){
                        $qry->whereIn("pmt_info_v1.pmt_mode", $pmt_mode);
                    }
                });
            }
        }
        // Skip to Deleted check
        if ($request['insurance_charge']=='insurance') {
            /*$get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
        }elseif ($request['insurance_charge']=='detail'){
                /*$get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
            //$get_function_value_patient->whereRaw('void_check is null');
            //$wallet->whereRaw('pmt_info_v1.void_check is null');
        }elseif ($request['insurance_charge']=='all') {
            /*$get_function_value->whereHas('pmt_info', function($q)  {
                        $q->whereRaw('void_check is null');
                    });*/
        }else{
                $get_function_value->whereRaw('void_check is null');
        }

        #### Getting Search Basis response ####
        /*if(isset($get_function_value_patient)){
            $result['patients'] = $get_function_value_patient;
            $result['wallet'] = $wallet;
        }*/
      //  dd($get_function_value->get());
        $result['payments'] = $get_function_value;
        $result['header'] = $get_list_header;
        $result['column'] = $hide_col;
        $result['page'] = $page;

        return $result;
    }

    public function getPaymentExport($request, $payment, $total) {
        $result = $payment;
        if ($request['payment_type'] == 'patient') {
            $get_list = [];
            $result_count = 0;
            foreach ($result as $key => $value) {
                $tab_value = App\Models\Patients\Patient::getPatienttabData($value->patient_id);
                $get_arr = [];
                $get_arr['patient_name'] = $value->patient->last_name . " " . $value->patient->first_name . "," . $value->patient->middle_name;
                $get_arr['acc_no'] = $value->patient->account_no;
                $get_arr['total_billed'] = $tab_value['billed'];
                $get_arr['patient_paid'] = $value->patient_paid;
                $get_arr['insurance_paid'] = $value->insurance_paid;
                $get_arr['patient_bal'] = $value->patient_bal;
                $get_arr['insurance_bal'] = $value->insurance_bal;
                $get_arr['total_adj'] = $value->adj + $value->withheld;
                if (isset($request['overpayment']) && $request['overpayment'] == 'patient')
                    $get_arr['wallet'] = $tab_value['wallet_balance'];
                $get_list[$result_count] = $get_arr;
                $result_count++;
            }
            $get_list[$result_count + 1] = ['acc_no' => '', 'patient_name' => '', 'total_billed' => 'Total Billed  : ' . Helpers::priceFormat($total['billed']), 'patient_paid' => 'Patient Paid  : ' . Helpers::priceFormat($total['pat_pay']), 'insurance_paid' => 'Insurance Paid  : ' . Helpers::priceFormat($total['ins_pay']), 'patient_bal' => 'Total Paid  : ' . Helpers::priceFormat($total['total_paid']), 'insurance_bal' => 'Total Transfers  : ' . Helpers::priceFormat($total['trans']), 'total_adj' => 'Total Adj : ' . Helpers::priceFormat($total['adjusted'])];
            if (isset($request['overpayment']) && $request['overpayment'] == 'patient')
                $get_list[$result_count + 1] = $get_list[$result_count + 1] + ['wallet' => ''];
            $response["value"] = json_decode(json_encode($get_list));

            $response["exportparam"] = array(
                'filename' => 'Payments Report',
                'heading' => '',
                'fields' => array(
                    'acc_no' => 'Acc No',
                    'patient_name' => 'Patient Name',
                    'total_billed' => 'Total Billed($)',
                    'patient_paid' => 'Patient Paid($)',
                    'insurance_paid' => 'Insurance Paid($)',
                    'patient_bal' => 'Patient Bal($)',
                    'insurance_bal' => 'Insurance Bal($)',
                    'total_adj' => 'total Adj($)',
                )
            );
            if (isset($request['overpayment']) && $request['overpayment'] == 'patient') {
                $response["exportparam"]['fields'] = $response["exportparam"]['fields'] + array('wallet' => 'Wallet Bal');
            }
            return $response;
        } else {

            $get_list = $get_list_arr = [];
            $count = 0;
            foreach ($result as $key => $value) {
                //$tab_value = PaymentClaimCtpDetail::getClaimCptDetail($value->claim->id);
                $tab_value = PaymentClaimCtpDetail::ClaimTransationDetail($value->claim->id);
                $adj = $value->total_adjusted + $value->total_withheld;
                $ins_over_pay = $value->claim->insurance_paid - $value->claim->total_allowed;
                $trans_amt = $tab_value->co_pay + $tab_value->co_ins + $tab_value->deductable;

                $get_arr['account_no'] = $value->patient->account_no;
                $get_arr['patient_name'] = Helpers::getNameformat($value->patient->last_name, $value->patient->first_name, $value->patient->middle_name);
                $get_arr['payment_type'] = $value->payment_type;
                $get_arr['claim_num'] = $value->claim->claim_number;
                $get_arr['billing'] = Provider::getProviderShortName($value->claim->billing_provider_id);
                $get_arr['rendering'] = Provider::getProviderShortName($value->claim->rendering_provider_id);
                $get_arr['facility'] = Facility::getFacilityShortName($value->claim->rendering_provider_id);
                $get_arr['reference'] = ($value->payment->reference == '') ? '-Nil-' : $value->payment->reference;
                $type = $value->payment->payment_mode;
                $get_arr['payment_mode'] = ($type == '') ? '-Nil-' : $type;
                $get_arr['pay_information'] = '-Nil-';

                if ($type == 'Check' || $type == 'EFT') {
                    $get_arr['pay_information'] = "Check Number : " . $value->payment->check_no . "\n Check Date : " . Helpers::dateFormat($value->payment->check_date, 'claimdate') . "\n Deposit Date : " . @$value->payment->deposit_date;
                }
                if ($type == 'Money Order') {
                    $get_arr['pay_information'] = "Check No : " . $value->payment->check_no . "\n Bank : " . $value->payment->bankname . "\n Branch : " . $value->payment->bank_branch;
                }
                if ($type == 'Credit') {
                    $get_arr['pay_information'] = "Card Type : " . $value->payment->card_type . "\n Card No : " . $value->payment->card_no . "\n Name On Card : " . $value->payment->name_on_card;
                }

                $get_arr['insurance'] = (!empty($value->payment->insurancedetail)) ? $value->payment->insurancedetail->insurance_name : 'Self';
                $get_arr['billed'] = Helpers::priceFormat($value->claim->total_charge);
                $get_arr['allowed'] = Helpers::priceFormat($value->total_allowed);
                $get_arr['total_paid'] = Helpers::priceFormat($value->patient_paid_amt + $value->insurance_paid_amt);
                $get_arr['patient_paid'] = Helpers::priceFormat($value->patient_paid_amt);
                $get_arr['insurance_paid'] = Helpers::priceFormat($value->insurance_paid_amt);
                $get_arr['adj'] = Helpers::priceFormat($adj);
                $get_arr['trans'] = Helpers::priceFormat($trans_amt);
                $get_arr['user'] = $value->user->name;
                if (isset($request['overpayment']) && $request['overpayment'] == 'insurance') {
                    if ($tab_value->balance_amt < 0) {
                        $overpay = -($tab_value->balance_amt);
                        $get_arr['ins_overpay'] = $overpay;
                    } else {
                        $get_arr['ins_overpay'] = '0';
                    }
                }
                $get_list_arr[$count] = $get_arr;
                $count++;
            }
            //$total = PaymentClaimCtpDetail::getTotalTransfer();
            $get_list_arr[$count] = ['account_no' => '', 'patient_name' => '', 'payment_type' => '', 'claim_num' => '', 'billing' => '', 'rendering' => '', 'facility' => '', 'reference' => '', 'payment_mode' => '', 'pay_information' => '', 'insurance' => '', 'billed' => '', 'allowed' => '', 'total_paid' => 'Total Payment  : ' . Helpers::priceFormat($total['total_paid']),
                'patient_paid' => 'Total Pat.Payment  : ' . Helpers::priceFormat($total['pat_pay']),
                'insurance_paid' => 'Total Ins.Payment  : ' . Helpers::priceFormat($total['ins_pay']),
                'adj' => 'Total Adj  : ' . Helpers::priceFormat($total['adjusted']),
                'trans' => 'Total Transfers  : ' . Helpers::priceFormat($total['trans']), 'user' => ''];
            if (isset($request['overpayment']) && $request['overpayment'] == 'insurance')
                $get_list_arr[$count] = $get_list_arr[$count] + ['ins_overpay' => ''];

            $response["value"] = json_decode(json_encode($get_list_arr));
            $exportparam = array(
                'filename' => 'Payments Report',
                'heading' => '',
                'fields' => array(
                    'account_no' => 'Acc No',
                    'patient_name' => 'Patient Name',
                    'payment_type' => 'Pay Type',
                    'claim_num' => 'Claim',
                    'billing' => 'Bill',
                    'rendering' => 'Rend',
                    'facility' => 'Fac',
                    'reference' => "Ref",
                    'payment_mode' => 'Pay Mode',
                    'pay_information' => 'Paying Information',
                    'insurance' => 'Ins',
                    'billed' => 'Billed($)',
                    'allowed' => 'Allowed($)',
                    'total_paid' => 'Tot Paid($)',
                    'patient_paid' => 'Pat Paid($)',
                    'insurance_paid' => 'Ins Paid($)',
                    'adj' => 'Adj($)',
                    'trans' => 'Trans',
                    'user' => 'User',
                )
            );

            if (isset($request['overpayment']) && $request['overpayment'] == 'insurance') {
                $exportparam['fields'] = $exportparam['fields'] + array(
                    'Over Payment' => array('table' => '', 'column' => 'ins_overpay', 'use_function' => ['App\Http\Helpers\Helpers', 'priceFormat'], 'label' => 'Over Payment')
                );
            }
            $response["exportparam"] = $exportparam;
            return $response;
        }
    }

    /*             * * Payment report module search page end ** */

    ############ Payment Report End ############
    ############ Refund Report Start ############

    public function getRefundsApi() {
        /*  $insurance = Insurance::where('status','Active')->orderBy('insurance_name', 'ASC')->lists('insurance_name', 'id');
          $facilities = Facility::getAllfacilities(); */
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('refund_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $insurance = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->selectRaw('CONCAT(short_name,"-",insurance_name) as insurance_data, id')->pluck('insurance_data', 'id')->all();
        $facilities = Facility::allFacilityShortName('name');
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance', 'facilities','searchUserData','search_fields')));
    }

    public function getRefundsearchApi($export = '', $data = '') {

        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $patient_refund_amt = $ins_refund_amt = $total_refund = 0;

        // Get refund result

        ### Start Search Fields filering data select ###
        $search_by = array();
        if(!empty($request['refund_type'])){
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Refund Type'] = ($request['refund_type'] == 'patient') ? 'Patient Refund' :'Insurance Refund';
            } else{
                $search_by['Refund Type'][] = ($request['refund_type'] == 'patient') ? 'Patient Refund' :'Insurance Refund';
            }
        }
        
        if(!empty($request['facility_id'])){
           if (strpos($request['facility_id'], ',') !== false){
               $search_name = Facility::select('facility_name');
                $facility_names = $search_name->whereIn('id', explode(',', $request['facility_id']))->get();
                foreach ($facility_names as $name) {
                    $value_names[] = $name['facility_name'];
                }
                $search_filter = implode(", ", array_unique($value_names));
            } else {
               $facility_names = Facility::select('facility_name')->where('id', $request['facility_id'])->get();
               foreach ($facility_names as $facility_na) {
                    $search_filter = $facility_na['facility_name'];
               }
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Facility'] = isset($search_filter) ? $search_filter : [];
            }
            else{
                $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
            }
        }
       
        if(!empty($request['billing_provider_id'])){
            $peoviders_id = explode(',', $request['billing_provider_id']);
            foreach ($peoviders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Billing Provider'] = $search_provider;
            } else {
                $search_by['Billing Provider'][] = $search_provider;
            }
        }
        
        if(!empty($request['rendering_provider_id'])){
           $renders_id = explode(',', $request['rendering_provider_id']);
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Rendering Provider'] = $search_render;
            } else {
                $search_by['Rendering Provider'][] = $search_render;
            }
        }
        
        if(!empty($request['created_at'])){
            $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date'] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            }
            else{
                $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        } else {
            $start_date = date("Y-m-d", strtotime('1970-01-01'));
            $end_date = date("Y-m-d", strtotime(date("Y/m/d")));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }
        
        if(!empty($request['reference'])){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Reference'] = $request['reference'];
            } else {
                $search_by['Reference'][] = $request['reference'];
            }
        }
        
         ## Search By User
        if(isset($request['user']) && $request['user'] !=''){
            $user = (isset($request['export']) || is_string($request['user'])) ? explode(',',$request['user']):$request['user'];
            $User_name =  Users::whereIn('id', $user)->where('status', 'Active')->pluck('short_name', 'id')->all();
            $search_by['User'][] = (in_array("0", $user))? ("All".(isset($user[1])? "," :"" ).implode(',',$User_name)) : implode(',',$User_name);            
        }
        
        if (!empty($request['insurance_id'])) {
            if (strpos($request['insurance_id'], ',') !== false) {
               $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
               $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by["Payer"] =  @array_flatten($insurance)[0];
            } else {
                $search_by["Payer"][] =  @array_flatten($insurance)[0];
            }
        }

        ### End Search Fields filering data select ###
        ### Start For Unposted Check Event ###
        if(!empty($request['include'])){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Include'] = ($request['include'] == 'unposted') ? 'Unposted': 'Wallet Refund'; 
            }
            else{
                $search_by['Include'][] = ($request['include'] == 'unposted') ? 'Unposted': 'Wallet Refund'; 
            }
            $refundresult = $this->getRefundResultUnposted($request);
        } else {
            $refundresult = $this->getRefundResult($request);
        }

        $type = ucfirst($request['refund_type']);
        $total_refund = clone $refundresult['get_refund_data'];
      
        $refund_result = $refundresult['get_refund_data']->get();
        
        // $this->getRefundExport($request, $export, $refundresult, $patient_refund_amt, $ins_refund_amt, $total_refund);

        $pagination = $insrefunds = $unposted =  $wallet = '';
        //ini_set('memory_limit', '2G');
        //ini_set('max_execution_time', 300);
        if(!empty($request['include']) && $request['include'] == 'unposted'){
            $unposted ="unposted";
            $wallet = "";
        }
        if(!empty($request['include']) && $request['include'] == 'wallet'){
            $wallet ="wallet";
            $unposted ="";
        }
        if($type == "Patient" && ($unposted == '')  && ($wallet == '')) {
             $refundresult['get_refund_data']->groupBy('pmt_info_v1.patient_id');
        }

        $db_data = (($unposted != '') || ($wallet != '')) ? "abs(sum(pmt_amt))":"abs(sum(total_paid))";
        
        $refund_value['insurance'] = ($type == "Insurance")?$total_refund->select("*",DB::raw("$db_data as payment_amt"))->pluck("payment_amt")->first():$refundresult['refund_data_other'];        
        $refund_value['patient'] = ($type == "Patient")?$total_refund->select("*",DB::raw("$db_data as payment_amt"))->value("payment_amt"):$refundresult['refund_data_other'];
         $refund_value['total'] = $refund_value['insurance'] + $refund_value['patient'];

        if (isset($request['exports']) && $request['exports'] == 'pdf'){
            $get_refund_data = $refundresult['get_refund_data']->get();   
        }elseif (isset($request['export']) && $request['export'] == 'xlsx'){
            $get_refund_data = $refundresult['get_refund_data']->get();   
        }else {
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $insrefunds = $refundresult['get_refund_data']->paginate($paginate_count);
            // Get export result

            $ref_array = $insrefunds->toArray();
            $pagination_prt = $insrefunds->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $insrefunds = json_decode($insrefunds->toJson());
            $get_refund_data = $insrefunds->data;
        }

        $refund_type = $request['refund_type'];
        $header = $refundresult['get_list_header'];
        $column = $refundresult['columns'];
        //$unposted = (!empty($request['include']))? "unposted":"";
       // $wallet = (!empty($request['wallet']))? "wallet":"";

        //$get_refund_data = $refundresult['get_refund_data']->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('refund_type', 'refund_result', 'refund_value', 'header', 'column', 'pagination', 'get_refund_data', 'unposted', 'wallet','search_by','start_date','end_date')));
    }

    // When clicks on unposted check amount query fetching starts here
    public function getRefundResultUnposted($request)
    {
        $unposted_check = PMTInfoV1::with('checkDetails', 'created_user','patient')->where('pmt_type', 'Refund')->has('claims', '<', 1);
         if (!empty($request['created_at'])) {
          //  if ($request['date_option'] == "enter_date") {
            $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            if ($start_date != '' && $end_date != '') {
                $unposted_check->whereRaw("(created_at) >= '$start_date' and (created_at) <= '$end_date'");
            }
        }
        
        if(!empty($request["reference"]))  {
            $unposted_check->where('reference', 'LIKE','%'.$request["reference"].'%');
            $get_list_header["Reference"] = $request["reference"];
            $hide_col["reference"] = 1;
        }
        
        if(!empty($request["user"]))  {
            $unposted_check->whereIn('pmt_info_v1.created_by',explode(',', $request["user"]));
        }

        if(!empty($request["insurance_id"]) && $request['refund_type'] == 'insurance' && $request["insurance_id"] != "all")
            $unposted_check->where('insurance_id',$request["insurance_id"]);

        if ($request['refund_type'] == 'insurance') {  // Find the result based on insurance wise
            $get_refund_data_patient = clone  $unposted_check;
            $get_refund_data_other = $get_refund_data_patient->whereRaw("pmt_info_v1.pmt_method = 'Patient'")->select(DB::raw('abs(sum(pmt_info_v1.pmt_amt)) as payment_amt'))->whereNotIn("source",['refundwallet'])->value("payment_amt");
            $get_refund_data = $unposted_check->where("pmt_method", 'Insurance');
            $get_refund_data->where('pmt_info_v1.void_check', NULL);
            $get_list_header["Refund Type"] = "Insurance";
            $hide_col["Type"] = 1;

        } elseif ($request['refund_type'] == 'patient') {
            // Find the result based on patient wise
            $get_refund_data_insurance = clone  $unposted_check;
            $get_refund_data_other = $get_refund_data_insurance->whereRaw("pmt_method = 'Insurance'")->select(DB::raw("abs(sum(pmt_info_v1.pmt_amt)) as payment_amt"))->value("payment_amt");
            $get_refund_data = (isset($request['include']) && $request['include']=='wallet')?$unposted_check->where("pmt_method", 'Patient')->whereIn("source",['refundwallet']):$unposted_check->where("pmt_method", 'Patient')->whereNotIn("source",['refundwallet']);
           // dd($get_refund_data->get());
            $get_refund_data->where('pmt_info_v1.void_check', NULL);
            $get_list_header["Refund Type"] = "Patient";
            $hide_col["Type"] = 1;
        }
        $refundresult = [];
        $refundresult['get_list_header'] = $get_list_header;
        $refundresult['refund_data_other'] = (isset($request['include']))?"0":$get_refund_data_other;
        $refundresult['get_refund_data'] = $get_refund_data;
        $refundresult['columns'] = $hide_col;
       // dd($refundresult);
        return $refundresult;
    }

    // When clicks on unposted check amount query fetching ends here
    public function getRefundResult($request) {
        $get_list_header = $hide_col = [];
        $type = ucfirst($request['refund_type']);
        $get_refund_data = PMTClaimTXV1::join('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')->where('pmt_claim_tx_v1.pmt_type', 'Refund');
        /*->whereHas('latest_payment_check' ,function ($query) use ($request){
                $query->where('void_check', NULL)->whereNull('deleted_at');
        });*/
        // $get_list_header = $hide_col = '';
        if (!empty($request['created_at'])) {
           // if ($request['date_option'] == "enter_date") {
            $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $start_date = Helpers::dateFormat($start_date, 'datedb');
            $end_date = Helpers::dateFormat($end_date, 'datedb');
            //$start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            //$end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            if ($start_date != '' && $end_date != '') {
                $get_refund_data->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','America/Denver')) >= '$start_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','America/Denver')) <= '$end_date'");
            }
        }
        if (!empty($request['insurance_id']) && $type != 'Patient') {
            $get_refund_data->whereIn('payer_insurance_id', explode(',',$request['insurance_id']));
            $get_list_header["Billed To"] = Insurance::getInsuranceName($request['insurance_id']);
            $hide_col["insurance"] = 1;
        }

        // Find billing provider based
        if (!empty($request["billing_provider_id"])) {
            $get_refund_data->whereHas('claim', function($query) use ($request) {
                $query->whereIn('billing_provider_id', explode(',',$request["billing_provider_id"]));
            });
            $get_list_header["Billing Prov"] = Provider::getProviderNamewithDegree($request['billing_provider_id']);
            $hide_col["billing"] = 1;
        }
        // Find rendering provider based
        if (!empty($request["rendering_provider_id"])) {
            $get_refund_data->whereHas('claim', function($query) use ($request) {
                $query->whereIn('rendering_provider_id', explode(',',$request["rendering_provider_id"]));
            });
            $get_list_header["Rendering Prov"] = Provider::getProviderNamewithDegree($request['rendering_provider_id']);
            $hide_col["rendering"] = 1;
        }

        // Find facility based
        if (!empty($request['facility_id'])) {
            $get_refund_data->whereHas('claim', function($query) use ($request) {
                $query->whereIn('facility_id', explode(',',$request["facility_id"]));
            });
            $get_list_header["Facility Name"] = Facility::getFacilityName($request['facility_id']);
            $hide_col["facility"] = 1;
        }
        if(!empty($request["reference"])){
            $get_refund_data->where('pmt_info_v1.reference', 'LIKE','%'.$request["reference"].'%');
            $get_list_header["Reference"] = $request["reference"];
                $hide_col["reference"] = 1;
        }
        if(!empty($request["user"]))  {
            $get_refund_data->whereIn('pmt_claim_tx_v1.created_by',explode(',', $request["user"]));
        }
           //      dd($get_refund_data);
        if ($request['refund_type'] == 'insurance') {  // Find the result based on insurance wise
            $get_refund_data_patient = clone  $get_refund_data;
            $get_refund_data_other = $get_refund_data_patient->whereRaw("pmt_info_v1.pmt_method = 'Insurance'")->select(DB::raw("(sum(pmt_claim_tx_v1.total_paid)) as payment_amt"))->value("payment_amt");
           // $get_refund_data = $get_refund_data->whereRaw("pmt_info_v1.pmt_method = 'Insurance'")->select("*");
            $get_refund_data->whereRaw("pmt_info_v1.pmt_method = 'Insurance'")->with(['latest_payment_check','claim', 'payment_info', 'user']) ;
            $get_list_header["Refund Type"] = "Insurance";
            $hide_col["Type"] = 1;

        } elseif ($request['refund_type'] == 'patient') {
            $get_refund_data_insurance = clone  $get_refund_data;
            // Find the result based on patient wise
            $get_refund_data_other = $get_refund_data_insurance->whereRaw("pmt_info_v1.pmt_method = 'Insurance'")->select(DB::raw('abs(sum(pmt_claim_tx_v1.total_paid)) as payment_amt'))->value("payment_amt");

            $get_refund_data = $get_refund_data->whereRaw("pmt_info_v1.pmt_method = 'Patient'")->where('pmt_info_v1.void_check', NULL)->select('pmt_claim_tx_v1.patient_id','pmt_claim_tx_v1.claim_id','pmt_claim_tx_v1.created_by',DB::raw("sum(pmt_claim_tx_v1.total_paid) as total_paid"))->with('claim_patient_det', 'user');
            $get_list_header["Refund Type"] = "Patient";
            $hide_col["Type"] = 1;
        }
       //dd($get_refund_data->toSql());
        $refundresult = [];
        $refundresult['get_list_header'] = $get_list_header;
        $refundresult['patient'] = $get_refund_data;
        $refundresult['insurance'] = $get_refund_data;
        $refundresult['refund_data_other'] = $get_refund_data_other;
        $refundresult['get_refund_data'] = $get_refund_data;
        $refundresult['columns'] = $hide_col;

        return $refundresult;
    }
    
    //Stored Procedure for Refund Analysis - Anjukaselvan
    
    public function getRefundsearchApiSP($export = '', $data = '') {

        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $start_date = $end_date = $billing_provider = $rendering_provider = $facility = $refund_type = $insurance_id = $include = $reference = $user = '';
        $pagination = $insrefunds = $unposted =  $wallet = '';
        // Get refund result
        ### Start Search Fields filering data select ###
        $search_by = array();
        
        if (!empty($request['created_at'])) {
            $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }
        if(!empty($request['billing_provider_id'])){
            $billing_provider = $request['billing_provider_id'];
            $providers_id = explode(',', $request['billing_provider_id']);
            foreach ($providers_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            $search_by['Billing Provider'][] = $search_provider;
        }
        if(!empty($request['rendering_provider_id'])){
            $rendering_provider = $request['rendering_provider_id'];
            $renders_id = explode(',', $request['rendering_provider_id']);
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name));
            $search_by['Rendering Provider'][] = $search_render;
        }
        if(!empty($request['facility_id'])){
            $facility = $request['facility_id'];
            if (strpos($request['facility_id'], ',') !== false) {
                $search_name = Facility::select('facility_name');
                $facility_names = $search_name->whereIn('id', explode(',', $request['facility_id']))->get();
                foreach ($facility_names as $name) {
                    $value_names[] = $name['facility_name'];
                }
                $search_filter = implode(", ", array_unique($value_names));
            } else {
                $facility_names = Facility::select('facility_name')->where('id', $request['facility_id'])->get();
                foreach ($facility_names as $facility_na) {
                    $search_filter = $facility_na['facility_name'];
                }
            }
            $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
        }
        if(!empty($request['refund_type'])){
            $refund_type = $request['refund_type'];
            $search_by['Refund Type'][] = ($request['refund_type'] == 'patient') ? 'Patient Refund' :'Insurance Refund';
        }
        if(!empty($request["insurance_id"]) && $request['refund_type'] == 'insurance' && $request["insurance_id"] != "all"){
            $insurance_id = $request["insurance_id"];
            if (strpos($request['insurance_id'], ',') !== false) {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_name")->whereIn('id', explode(',', $request['insurance_id']))->get()->toArray();
            } else {
                $insurance = Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ', ') as short_name")->where('id', $request['insurance_id'])->get()->toArray();
            }
            $search_by["Payer"][] = @array_flatten($insurance)[0];
        }
        
        if(!empty($request['include'])){
            $include = $request['include'];
            $search_by['Include'][] = $request['include'] ;
        } else {
            $include = '';
        }

        if(!empty($request['reference'])){
            $reference = $request['reference'];
            $search_by['Reference'][] = $request['reference'];
        }
         ## Search By User
        if(!empty($request['user'])){
            $user = $request['user'];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
                         ->pluck('short_name', 'id')->all();
            $User_name = implode(",", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        ### End Search Fields filering data select ###
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
            if($include != ''){
                $sp_result_count = DB::select('call refundAnalysisUnposted("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }elseif($include == ''){
                $sp_result_count = DB::select('call refundAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }
            $count = (isset($sp_result_count[0]->refund_count)) ? $sp_result_count[0]->refund_count : 0;
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
            if($include != ''){
                $sp_return_result = DB::select('call refundAnalysisUnposted("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }elseif($include == ''){
                $sp_return_result = DB::select('call refundAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }
            $get_refund_data = $sp_return_result;
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
            //Export
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            if($include != ''){
                $sp_return_result = DB::select('call refundAnalysisUnposted("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }elseif($include == ''){
                $sp_return_result = DB::select('call refundAnalysis("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            }
            $get_refund_data = $sp_return_result;
        }
        $refund_result = $sp_return_result;
        //For Summary
        $refund_amt = 0; 
        foreach ($refund_result as $list) {
            $refund_amt += abs($list->refund_amt);
        }
        if($include != ''){
                $payment_amt_result = DB::select('call refundAnalysisUnpostedOther("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '")');
                $payment_amt = (isset($payment_amt_result[0]->payment_amt)) ? $payment_amt_result[0]->payment_amt : 0;
        }elseif($include == ''){
                $payment_amt_result = DB::select('call refundAnalysisOther("' . $start_date . '", "' . $end_date . '",  "' . $billing_provider . '", "' . $rendering_provider . '", "' . $facility . '",  "' . $refund_type . '",  "' . $insurance_id . '", "' . $include . '", "' . $reference . '", "' . $user . '")');
                $payment_amt = (isset($payment_amt_result[0]->payment_amt)) ? $payment_amt_result[0]->payment_amt : 0;
        }
       
        $type = ucfirst($request['refund_type']);
        $refund_value['insurance'] = ($type == "Insurance")?$refund_amt:$payment_amt;
        $refund_value['patient'] = ($type == "Patient")?$refund_amt:$payment_amt;
        $refund_value['total'] = $refund_value['insurance'] + $refund_value['patient'];
        
        if(!empty($request['include']) && $request['include'] == 'unposted'){
            $unposted ="unposted";
            $wallet = "";
        }
        if(!empty($request['include']) && $request['include'] == 'wallet'){
            $wallet ="wallet";
            $unposted ="";
        }
        $refund_type = $request['refund_type'];
        $header = '';
        $column = '';
        //dd($refund_result);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('refund_type', 'refund_result', 'refund_value', 'header', 'column', 'pagination', 'get_refund_data', 'unposted', 'wallet','search_by','start_date','end_date')));
    }

    public function getRefundExport($request, $export, $refundresult, $patient_refund_amt, $ins_refund_amt, $total_refund) {

        $patient_refund_amt = $ins_refund_amt = $total_refund = 0;
        if ($export != "" && $request['refund_type'] == 'patient') {
            $patientrefunds = PMTInfoV1::select("*", DB::raw('sum(IF(pmt_type = "Refund", pmt_amt, 0)) as claimrefund, sum(IF(source = "refundwallet", payment_amt, 0)) as directrefund'))->groupBy('patient_id')->where('pmt_method', '=', 'patient')->get();

            // Create array for patient export option
            $patient_r = $patient_list = array();
            foreach ($patientrefunds as $key => $refund_value) {
                $refund_amt = $refund_value->claimrefund + $refund_value->directrefund;
                $patient_name = Helpers::getNameformat(@$refund_value->patient->last_name, @$refund_value->patient->first_name, @$refund_value->patient->middle_name);

                $patient_r['patient_name'] = $patient_name;
                $patient_r['actno'] = $refund_value->patient->account_no;
                $patient_r['refundamt'] = Helpers::priceFormat(@$refund_amt);
                $patient_r['user'] = ucwords($refund_value->created_user->name);

                $patient_list[$key] = $patient_r;
            }

            $patient_list[$key + 1] = ['patient_name' => '', 'actno' => 'Total Ins.Refunds :' . Helpers::priceFormat($ins_refund_amt), 'refundamt' => 'Total Pat.Refunds :' . Helpers::priceFormat($patient_refund_amt), 'user' => 'Total Refunds :' . Helpers::priceFormat($total_refund)];
            $get_patientres = json_decode(json_encode($patient_list));

            $exportparam = array(
                'filename' => 'Refund Report',
                'heading' => '',
                'fields' => array(
                    'patient_name' => 'Patient Name',
                    'actno' => 'Acc No',
                    'refundamt' => 'Refund Amt',
                    'user' => 'User'
            ));

            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $get_patientres, $export);
        }


        // Insurance export option
        if ($export != "" && $request['refund_type'] == 'insurance') {
            // Create array for insurance export option
            $exportinsrefunds = $refundresult['insurance']->get();
            $insurance_r = $insurance_list = array();

            $i = 1;
            foreach ($exportinsrefunds as $key => $refund_value) {
                $patient_name = Helpers::getNameformat(@$refund_value->claim->patient->last_name, @$refund_value->claim->patient->first_name, @$refund_value->claim->patient->middle_name);

                foreach ($refund_value->paymentcptdetail as $paymentcptdetails) {
                    $refund_amt = 0 - $paymentcptdetails->paid_amt;
                    if ($refund_amt != 0) {
                        $insurance_r['transaction_date'] = Helpers::dateFormat($refund_value->created_at, 'date');
                        $insurance_r['claimno'] = $refund_value->claim->claim_number;
                        $insurance_r['patientname'] = $patient_name;
                        $insurance_r['actno'] = $refund_value->claim->patient->account_no;
                        $insurance_r['responsibility'] = $refund_value->insurance_detail->insurance_name;
                        $insurance_r['rendering'] = $refund_value->claim->rendering_provider->short_name;
                        $insurance_r['billing'] = $refund_value->claim->billing_provider->short_name;
                        $insurance_r['facility'] = $refund_value->claim->facility_detail->short_name;
                        $insurance_r['dosfrom'] = Helpers::dateFormat($paymentcptdetails->dosdetails->dos_from, 'dob');
                        $insurance_r['dosto'] = Helpers::dateFormat($paymentcptdetails->dosdetails->dos_to, 'dob');
                        $insurance_r['cpt'] = $paymentcptdetails->dosdetails->cpt_code;
                        $insurance_r['checkdate'] = Helpers::dateFormat($refund_value->payment->check_date, 'date');
                        $insurance_r['check'] = $refund_value->payment->check_no;
                        $insurance_r['refundamt'] = Helpers::priceFormat($refund_amt);
                        $insurance_r['user'] = ucwords(@$refund_value->payment->created_user->name);
                        $insurance_list[$i] = $insurance_r;
                        $i++;
                    }
                }
            }

            $insurance_list[$i + 1] = ['transaction_date' => '', 'claimno' => '', 'patientname' => '', 'actno' => '', 'responsibility' => '', 'rendering' => '', 'billing' => '', 'facility' => '', 'dosfrom' => '', 'dosto' => '', 'cpt' => '', 'checkdate' => '', 'check' => 'Total Ins.Refunds: ' . Helpers::priceFormat($ins_refund_amt), 'refundamt' => 'Total Pat.Refunds: ' . Helpers::priceFormat($patient_refund_amt), 'user' => 'Total Refunds: ' . Helpers::priceFormat($total_refund)];

            $get_insres = json_decode(json_encode($insurance_list));

            $exportparam = array(
                'filename' => 'Refund Report',
                'heading' => '',
                'fields' => array(
                    'transaction_date' => 'Transaction Date',
                    'claimno' => 'Claim No',
                    'patientname' => 'Patient Name',
                    'actno' => 'Acc No',
                    'responsibility' => 'Responsiblity',
                    'rendering' => 'Rendering',
                    'billing' => 'Billing',
                    'facility' => 'Facility',
                    'dosfrom' => 'DOS From',
                    'dosto' => 'DOS To',
                    'cpt' => 'CPT',
                    'checkdate' => 'Check Date',
                    'check' => 'Check No',
                    'refundamt' => 'Refund Amt',
                    'user' => 'User'
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $get_insres, $export);
        }
    }

    ############ Refund Report End ############
    ############ Finanical Report Start ############

    public function getYearendApi($export = '') {
        $practice_date = ClaimInfoV1::where('created_at','!=', "0000-00-00 00:00:00")->min('created_at');
        $practice_created_date = date('Y', strtotime($practice_date));
        $array_year = [];
        for ($i = $practice_created_date; $i <= date('Y'); $i++) {
            $array_year[$i] = $i;
        }
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('year_end_report');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('array_year','searchUserData','search_fields')));
    }

    public function getFinancialSearchApi($export = "", $data = "") {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();

        $financial = $this->getFinancialResult($request);
        $search_by = array();
        ### Start Search filter names ###
       if(!empty($request['facility_id'])){
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
           if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Facility'] = isset($search_filter) ? $search_filter : [];
           }
           else{
                 $search_by['Facility'][] = isset($search_filter) ? $search_filter : [];
           }
        }
       // dd($request['billing_provider_id']);
        if(!empty($request['billing_provider_id'])){
            $peoviders_id = explode(',', $request['billing_provider_id']);
            foreach ($peoviders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_provider = implode(", ", array_unique($value_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Billing Provider'] = $search_provider;
            }
            else{
                $search_by['Billing Provider'][] = $search_provider;
            }
        }
        if(!empty($request['rendering_provider_id'])){
               $renders_id = explode(',', $request['rendering_provider_id']);
                foreach ($renders_id as $id) {
                    $value_name[] = App\Models\Provider::getProviderFullName($id);
                }
                $search_render = implode(", ", array_unique($value_name));
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['Rendering Provider'] = $search_render;
                }
                else{
                    $search_by['Rendering Provider'][] = $search_render;
                }
        }
       if(!empty($request['referring_provider'])){
               $renders_id = explode(',', $request['referring_provider']);
                foreach ($renders_id as $id) {
                    $value_name[] = App\Models\Provider::getProviderFullName($id);
                }
                $search_render = implode(", ", array_unique($value_name));
                if (isset($request['exports']) && $request['exports'] == 'pdf'){
                    $search_by['Reffering Provider'] = $search_render;
                }
                else{
                    $search_by['Reffering Provider'][] = $search_render;
                }
        }
       ## Search By User
        if(!empty($request['created_by']) && is_array($request['created_by'])){
            $User_name =  Users::whereIn('id', explode(',', $request["created_by"]))->where('status', 'Active')
            ->pluck('name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['User'] = $User_name;
            }
            else{
                $search_by['User'][] = $User_name;
            }
        }
        if(!empty($request['year'])){
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Year'] = $request['year'];
            }
            else{
                $search_by['Year'][] = $request['year'];
            }
        }
        ### End Search filter names ###

        $claims = $financial['claim'];
        
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims','search_by')));
    }

    public function getFinancialResult($request) {
        $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $year_key = '';

        if (isset($request["year"]) && $request["year"] != "") {
            $year_key = $request["year"];
        } elseif (isset($request["year_option"]) && $request["year_option"] == "previous_year") {
            $year_key = date('Y') - 1;
        } else {
            $year_key = date('Y');
        }
        foreach ($months as $key => $value) {
            $month_key = intval($key);
            $claim[$value] = $this->getMonthRecord($month_key, @$year_key, $request);
        }

        $result['claim'] = $claim;
        return $result;
    }

    public function getMonthRecord($month_key, $year_key, $request) {
        $result['year_key'] = substr($year_key, -2);
        $header_list = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $claim_obj = DB::table('claim_info_v1')
                         ->leftJoin('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                      //   ->Leftjoin('patients', 'claim_info_v1.patient_id', '=', 'patients.id')
                         // ->join('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')
                         ->select(DB::raw('COUNT(claim_info_v1.id) as claims_visits'),
                                  DB::raw('sum(claim_info_v1.total_charge) as value'),
                                  DB::raw('SUM(
                                    CASE WHEN insurance_id = 0
                                    THEN (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld + patient_adj + pmt_claim_fin_v1.insurance_adj) ELSE 0 END) patient_due,
                                    SUM(
                                    CASE WHEN insurance_id != 0
                                    THEN (claim_info_v1.total_charge) - (pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) ELSE 0 END) insurance_due'),
                                 
                                  DB::raw('SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+patient_adj+pmt_claim_fin_v1.insurance_adj) as total_due'),
                                  'claim_info_v1.id'
                        )
                        ->where(DB::raw('MONTH(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'"))'), $month_key)
                        ->where(DB::raw('YEAR(CONVERT_TZ(claim_info_v1.created_at,"UTC","'.$practice_timezone.'"))'), $year_key)
                        ->whereNull('claim_info_v1.deleted_at');
        // Facility
        $faci_id = $bill_id = $ref_id =  $ren_id = $user_id = '';

        if (isset($request["facility_id"]) && !empty($request["facility_id"])) {
            if (strpos($request['facility_id'], ',') !== false)
                $faci_id = explode(',',$request["facility_id"]);
            else
                $faci_id = (array)$request["facility_id"];
            $claim_obj->whereIn("claim_info_v1.facility_id", $faci_id);
        }

        if (isset($request["referring_provider"]) && !empty($request["referring_provider"])) {
            if (strpos($request['referring_provider'], ',') !== false)
                $ref_id = explode(',',$request["referring_provider"]);
            else
                $ref_id = (array)$request["referring_provider"];
            $claim_obj->whereIn("claim_info_v1.refering_provider_id", $ref_id);
        }
        //Billing Provider
        if (isset($request["billing_provider_id"]) && !empty($request["billing_provider_id"])) {
            if (strpos($request['billing_provider_id'], ',') !== false)
                $bill_id = explode(',',$request["billing_provider_id"]);
            else
                $bill_id = (array)$request["billing_provider_id"];

            $claim_obj->whereIn('claim_info_v1.billing_provider_id', $bill_id);
        }
        // Rendering Provider
        if (isset($request["rendering_provider_id"]) && !empty($request["rendering_provider_id"])) {
            if (strpos($request['rendering_provider_id'], ',') !== false)
                $ren_id = explode(',',$request["rendering_provider_id"]);
            else
                $ren_id = (array)$request["rendering_provider_id"];
            $claim_obj->whereIn('claim_info_v1.rendering_provider_id', $ren_id);
        }
       ## User
        /*if (isset($request["created_by"]) && !empty($request["created_by"])) {
            if (strpos($request['created_by'], ',') !== false)
                $user_id = explode(',',$request["created_by"]);
            else
                $user_id = (array)$request["created_by"];
            $claim_obj->whereIn('claim_info_v1.created_by', $user_id);
        }*/
        $result['count'] = $claim_obj->count();
        //dd($claim_obj->toSql());
        $claim_id = $claim_obj->pluck("claim_info_v1.id")->first();
        
         $pat_pmt =  DB::table('pmt_claim_tx_v1')->leftjoin('claim_info_v1', 'claim_info_v1.id', '=', 'pmt_claim_tx_v1.claim_id')->where('pmt_claim_tx_v1.pmt_method','Patient')->where(DB::raw('MONTH(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"))'), $month_key)->where(DB::raw('YEAR(CONVERT_TZ(pmt_claim_tx_v1.created_at,"UTC","'.$practice_timezone.'"))'), $year_key)->whereNull('pmt_claim_tx_v1.deleted_at');
             if (!empty($faci_id)) {
                $pat_pmt->whereIn('claim_info_v1.facility_id', $faci_id);
             }
             if (!empty($bill_id)) {
                $pat_pmt->whereIn('claim_info_v1.billing_provider_id', $bill_id);
             }
             if (!empty($ren_id)) {
                $pat_pmt->whereIn('claim_info_v1.rendering_provider_id', $ren_id);
             }
             if (!empty($ref_id)) {
                $pat_pmt->whereIn('claim_info_v1.refering_provider_id', $ref_id);
             }
             if (!empty($user_id)) {
                $pat_pmt->whereIn('pmt_claim_tx_v1.created_by', $user_id);
             }
        // Payments wallet only - total payment amount minus amount used calculation used.
        $wallet_amt = 0;
        if(empty($faci_id) && empty($bill_id) && empty($ren_id) && empty($ref_id)) {
            $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt')->where(DB::raw('MONTH(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"))'), $month_key)->where(DB::raw('YEAR(CONVERT_TZ(pmt_info_v1.created_at,"UTC","'.$practice_timezone.'"))'), $year_key);
            $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet[0]->pmt_amt!=null)
                $wallet_amt = $wallet[0]->pmt_amt;
        }
        $patient_payment = clone $pat_pmt;
        $patient_refund = clone $pat_pmt;
      //  dd($pat_pmt->toSql());
        $result['patient_payment'] = $patient_payment->whereIn('pmt_claim_tx_v1.pmt_type',['Payment','Credit Balance'])->sum(DB::raw('pmt_claim_tx_v1.total_paid'))+$wallet_amt;
        $result['patient_refund'] = (-1)*$patient_refund->where('pmt_claim_tx_v1.pmt_type','Refund')->sum(DB::raw('pmt_claim_tx_v1.total_paid'));
        
        $pmt_claim = PMTClaimTXV1::where(DB::raw('MONTH(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))'), $month_key)->where(DB::raw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))'), $year_key)
        ->whereHas('claim', function($query) use ($request,$faci_id, $bill_id,  $ren_id, $ref_id, $user_id){
             if (!empty($faci_id)) {
                $query->whereIn('facility_id', $faci_id);
             }
              if (!empty($bill_id)) {
                $query->whereIn('billing_provider_id', $bill_id);
             }
              if (!empty($ren_id)) {
                $query->whereIn('rendering_provider_id', $ren_id);
             }
             if (!empty($ref_id)) {
                $query->whereIn('refering_provider_id', $ref_id);
             }
          /*   if (!empty($user_id)) {
                $query->whereIn('created_by', $user_id);
             }  */
        });
         if (!empty($user_id)) {
              $pmt_claim->whereIn('created_by', $user_id);
          }
        $adj_claim = clone $pmt_claim;
        /*$pmt_claim->whereHas('latest_payment', function($query) {
            $query->where('void_check', NULL);
        });*/
        $pmt_claim_adj = $adj_claim;
        $pmt_claim_patient_payment = clone $pmt_claim;
        $pmt_claim_patient_paid = clone $pmt_claim;
        $pmt_claim_patient_refund = clone $pmt_claim;
        $pmt_claim_ins_refund = clone $pmt_claim;
        $pmt_claim_insurance_payment = clone $pmt_claim;
        $adjustment =$pmt_claim_adj
             ->select('pmt_method',DB::raw('sum(pmt_claim_tx_v1.total_writeoff+pmt_claim_tx_v1.total_withheld) AS adjustment,CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'")'))
        ->groupBy('pmt_method')->groupby(DB::raw('MONTH(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))'))->groupby(DB::raw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))'))
            ->get()->toArray();

        $key_patient = array_search('Patient', array_column($adjustment, 'pmt_method'));
        $key_insurance = array_search('Insurance', array_column($adjustment, 'pmt_method'));
        $result['patient_adjusted'] = ($key_patient !== false && isset($adjustment[$key_patient]) && !empty($adjustment[$key_patient]))?$adjustment[$key_patient]['adjustment']:"0.00";
        $result['insurance_adj'] = ($key_insurance !== false && isset($adjustment[$key_insurance]) && !empty($adjustment[$key_insurance]))?$adjustment[$key_insurance]['adjustment']:"0.00";
        // if($month_key == 7)
           // dd($result['patient_adjusted']);
      /*  $result['patient_payment'] = $pmt_claim_patient_payment
        ->where('pmt_method', "Patient")
        ->where('total_paid', '>', 0)
        ->whereIn('pmt_type', ["Payment"])
        ->sum('total_paid');*/

         $result['patient_paid'] = $pmt_claim_patient_paid
        ->where('pmt_method', "Patient")
        ->where('total_paid', '<', 0)
        ->whereIn('pmt_type', ["Payment"])
        ->sum('total_paid');

      /*  $result['patient_refund'] = $pmt_claim_patient_refund
        ->where('pmt_method', "Patient")
        ->where('total_paid', '<', 0)
        ->where('pmt_type', "Refund")
        ->sum('total_paid');*/

        $result['ins_refund'] = $pmt_claim_ins_refund
        ->where('pmt_method', "Insurance")
        //->where('total_paid', '<', 0)
        ->where('pmt_type', "Refund") // consider only refund Payment
        ->sum('total_paid');

        $result['insurance_payment'] = $pmt_claim_insurance_payment
        ->where('pmt_method', "Insurance")
        //->where('total_paid', '>', 0)
        ->where('pmt_type','Payment') // consider only refund Payment
        ->sum('total_paid');
        $result['tot_refund'] = $result['ins_refund'] + $result['patient_refund'];
        $result['total_adjusted'] = $result['patient_adjusted'] + $result['insurance_adj'];
        $data = (array)$claim_obj->first();
        if(empty($data)){
            $data = array (
                  'value' => '0.00',
                  'patient_adjusted' => '0.00',
                  'insurance_adj' =>'0.00',
                  'total_adjusted' => '0.00',
                  'patient_payment' => '0.00',
                  'insurance_payment' => '0.00',
                  'total_paid' =>'0.00',
                  'patient_due' => '0.00',
                  'insurance_due' => '0.00',
                  'total_due' => '0.00',
                )  ;
        }
        $result = array_merge($data, $result);
        return $result;
    }

    ############ Finanical Report End ############

    public function getInsuranceListApi($type_id) {
        $insurance = Insurance::where('status', 'Active');

        if ($type_id != "all") {
            $type_id = Helpers::getEncodeAndDecodeOfId($type_id, 'decode');
            $insurance = $insurance->where("insurancetype_id", $type_id);
        }
        $insurance_list = $insurance->pluck("insurance_name", "id")->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance_list')));
    }

    public function getPayeryearApi() {
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details')));
    }

    public function getPayerFilterApi() {
        $request = Request::All();

        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];

        if ($request['payer'] == 'all') {
            $claim_detail = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id');
        } elseif ($request['payer'] == 'patient') {
            $claim_detail = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('insurance_id', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id');
        } elseif ($request['payer'] == 'insurance') {
            $claim_detail = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('insurance_id', '!=', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id');
        }
        $pagination = '';
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
        $claim_details = $claim_detail->paginate($paginate_count);
        $claim_details_array = $claim_details->toArray();
        $pagination_prt = $claim_details->render();
        if ($pagination_prt == '')
            $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
        $pagination = array('total' => $claim_details_array['total'], 'per_page' => $claim_details_array['per_page'], 'current_page' => $claim_details_array['current_page'], 'last_page' => $claim_details_array['last_page'], 'from' => $claim_details_array['from'], 'to' => $claim_details_array['to'], 'pagination_prt' => $pagination_prt);
        $claim_data = json_decode($claim_details->toJson());
        $claim_details = $claim_data->data;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_details', 'start_date', 'end_date', 'pagination')));
    }

    /** * Blade - Export payer analysis ** */

    public function getPayerfilterexportApi() {

        $request = Input::get();

        if ($request['start-date'] != '')
            $start_date = $request['start-date'];

        if ($request['end-date'] != '')
            $end_date = $request['end-date'];
        if ($request['payer'] == 'all') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        } elseif ($request['payer'] == 'patient') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->where('insurance_id', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        } elseif ($request['payer'] == 'insurance') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->where('insurance_id', '!=', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_details', 'start_date', 'end_date')));
    }

    public function getPayerexportApi($export = '') {
        $request = Input::get();

        if ($request['start-date'] != '')
            $start_date = $request['start-date'];

        if ($request['end-date'] != '')
            $end_date = $request['end-date'];

        if ($request['payer'] == 'all') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        } elseif ($request['payer'] == 'patient') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('insurance_id', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        } elseif ($request['payer'] == 'insurance') {
            $claim_details = ClaimInfoV1::with('insurance_details')->where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->where('insurance_id', '!=', 0)->select(DB::raw('sum(total_charge) as Billed,insurance_id,sum(insurance_paid) as insurancepaid,sum(patient_paid) as patientpaid,sum(patient_adjusted) as adjusted,sum(balance_amt) as balanced'))->groupBy('insurance_id')->get();
        }
        $total_billed = 0;
        $total_paids = 0;
        $total_adjusted = 0;
        $total_balanced = 0;
        $total_charge_count = 0;
        $get_list = array();
        foreach ($claim_details as $list) {
            $total_billed = $total_billed + $list->Billed;
            if ($list->insurance_details != NULL)
                $total_paids = $total_paids + $list->insurancepaid;
            else
                $total_paids = $total_paids + $list->patientpaid;
            $total_adjusted = $total_adjusted + $list->adjusted;
            $total_balanced = $total_balanced + $list->balanced;
            if ($list->insurance_details != NUll) {
                $export_data['payer'] = $list->insurance_details->insurance_name;
                $export_data['billed'] = $list->Billed;
                $export_data['paid'] = $list->insurancepaid;
                $export_data['adj'] = $list->adjusted;
                $export_data['bal'] = $list->balanced;
            } else {
                $export_data['payer'] = "Patient";
                $export_data['billed'] = $list->Billed;
                $export_data['paid'] = $list->insurancepaid;
                $export_data['adj'] = $list->adjusted;
                $export_data['bal'] = $list->balanced;
            }

            $get_list[$total_charge_count] = $export_data;
            $total_charge_count++;
        }
        $get_list[$total_charge_count] = ['payer' => '', 'billed' => '', 'paid' => '', 'adj' => '', 'bal' => ''];
        $total_charge_count = $total_charge_count + 1;
        $get_list[$total_charge_count] = ['payer' => '', 'billed' => 'Total Billed:' . $total_billed, 'paid' => 'Total Paid:' . $total_paids, 'adj' => 'Total Adjustments:' . $total_adjusted, 'bal' => 'Total Balance:' . $total_balanced];
        $get_result = $get_list;
        $result["value"] = json_decode(json_encode($get_result));
        $result["exportparam"] = array(
            'filename' => 'Payer Analysis',
            'heading' => '',
            'fields' => array(
                'payer' => 'Payer',
                'billed' => 'Billed',
                'paid' => 'Paid',
                'adj' => 'Adjustments',
                'bal' => 'Balance',
        ));

        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    public function getProviderreimbursementApi() {
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        $rendenring_provider_list = Provider::has('renderingclaims')->where('provider_types_id', 1)->select('provider_name', 'short_name', 'id')->get();
        $billing_provider_list = Provider::has('billingclaims')->where('provider_types_id', 5)->select('provider_name', 'short_name', 'id')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details', 'rendenring_provider_list', 'billing_provider_list')));
    }

    public function getProviderFilterApi() {
        $request = Request::All();
        $start_date = ($request['hidden_from_date'] == '') ? $request['from_date'] : $request['hidden_from_date'];
        $end_date = ($request['hidden_to_date'] == '') ? $request['to_date'] : $request['hidden_to_date'];
        $temp_data_arr = [];
        $data_value = [];
        $data = array();
        $rendering_provider = $request['rendering_provider'];
        $billing_provider = $request['billing_provider'];
        /* Get rendering provider and billing provider data starts here */
        $provider_arr = [Config::get('siteconfigs.providertype.Billing'), Config::get('siteconfigs.providertype.Rendering')];
        $provider_info = Provider::whereIn('provider_types_id', $provider_arr)
                        ->where(function($query) {
                            $query->has('renderingclaims')->orhas('billingclaims');
                        })
                        ->join('provider_types', 'providers.provider_types_id', '=', 'provider_types.id')->selectRaw('providers.id, provider_types_id, provider_types.name as provider_type, CONCAT(provider_name," - ",provider_types.name) as provider_name')->pluck('provider_name', 'id')->all();
        $data_value = [];
        /* Get rendering provider and billing provider data ends here */
        if ($rendering_provider != 'all' && $billing_provider != 'all') {
            $provider_data['billing_provider_id'] = ClaimInfoV1::where('billing_provider_id', $billing_provider)->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, billing_provider_id'))->pluck('facility_ids', 'billing_provider_id')->all();
            $provider_data['rendering_provider_id'] = ClaimInfoV1::where('rendering_provider_id', $rendering_provider)->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, rendering_provider_id'))->pluck('facility_ids', 'rendering_provider_id')->all();
            $data_value = $this->getProviderFacility($provider_data);
        } else if ($rendering_provider == 'all' && $billing_provider == 'all') {
            $provider_data['rendering_provider_id'] = ClaimInfoV1::groupBy('rendering_provider_id')->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, rendering_provider_id'))
            ->pluck('facility_ids', 'rendering_provider_id')->all();
            $provider_data['billing_provider_id'] = ClaimInfoV1::groupBy('billing_provider_id')->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, billing_provider_id'))
            ->pluck('facility_ids', 'billing_provider_id')->all();
            $data_value = $this->getProviderFacility($provider_data);
        } else if ($rendering_provider == 'all' && $billing_provider != 'all') {
            $provider_data['rendering_provider_id'] = ClaimInfoV1::groupBy('rendering_provider_id')->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, rendering_provider_id'))
            ->pluck('facility_ids', 'rendering_provider_id')->all();
            $provider_data['billing_provider_id'] = ClaimInfoV1::where('billing_provider_id', $billing_provider)->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, billing_provider_id'))
            ->pluck('facility_ids', 'billing_provider_id')->all();
            $data_value = $this->getProviderFacility($provider_data);
        } else if ($billing_provider == 'all' && $rendering_provider != 'all') {
            $provider_data['billing_provider_id'] = ClaimInfoV1::groupBy('billing_provider_id')->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, billing_provider_id'))
            ->pluck('facility_ids', 'billing_provider_id')->all();
            $provider_data['rendering_provider_id'] = ClaimInfoV1::where('rendering_provider_id', $rendering_provider)->select(DB::raw('group_concat(DISTINCT facility_id) as facility_ids, rendering_provider_id'))
            ->pluck('facility_ids', 'rendering_provider_id')->all();
            $data_value = $this->getProviderFacility($provider_data);
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('data', 'start_date', 'end_date', 'rendering_provider', 'billing_provider', 'provider_info', 'data_value')));
    }

    /*             * * Blade - Export provider analysis ** */

    public function getProviderFacility($provider_data) {
        $data_value = [];
        foreach ($provider_data as $key => $values) {
            foreach ($values as $key_data => $value) {
                $facility_ids = explode(',', $value);
                foreach ($facility_ids as $facility_id) {
                    $data_value[$key_data][$facility_id] = ClaimInfoV1::where($key, $key_data)->where('facility_id', $facility_id)
                                    ->selectRaw('facility_id, rendering_provider_id,billing_provider_id,sum(total_charge) as total_charge, sum(total_adjusted) as total_adjusted, sum(total_paid) as total_paid, sum(patient_due) as patient_due, sum(insurance_due) as insurance_due, sum(balance_amt) as balance_amt')->with('facility_detail')->get();
                }
            }
        }
        return $data_value;
    }

    public function getProviderExportApi() {
        $request = Input::get();

        if ($request['start-date'] != '')
            $start_date = $request['start-date'];

        if ($request['end-date'] != '')
            $end_date = $request['end-date'];
        $data = array();
        $rendering_provider = $request['rendering_provider'];
        $billing_provider = $request['billing_provider'];
        if ($request['rendering_provider'] != '') {
            if ($request['rendering_provider'] == 'all')
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->get()->toArray();
            else
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->where('rendering_provider_id', $request['rendering_provider'])->get()->toArray();

            foreach ($claims_details as $claim_list) {
                $rendering_provider_id = $claim_list['rendering_provider_id'];
                $facility_id = $claim_list['facility_id'];
                $provider_details = Provider::with('provider_types')->where('id', $rendering_provider_id)->where('deleted_at', NULL)->select('provider_types_id', 'provider_name')->get()->toArray();
                $facility_details = Facility::where('id', $facility_id)->where('deleted_at', NULL)->select('facility_name')->get()->toArray();
                if (!empty($provider_details) && !empty($facility_details)) {
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'];

                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'];
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'];

                    $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['rendering'] = $provider_details[0]['provider_name'];

                    $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['facility_name'] = $facility_details[0]['facility_name'];
                }
            }
        }
        if ($request['billing_provider'] != '') {
            if ($request['billing_provider'] == 'all')
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->get()->toArray();
            else
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)))->where('created_at', '<=', date("Y-m-d", strtotime($end_date)))->where('billing_provider_id', $request['billing_provider'])->get()->toArray();
            foreach ($claims_details as $claim_list) {
                $billing_provider_id = $claim_list['billing_provider_id'];
                $facility_id = $claim_list['facility_id'];
                $provider_details = Provider::with('provider_types')->where('id', $billing_provider_id)->select('provider_types_id', 'provider_name')->get()->toArray();

                $facility_details = Facility::where('id', $facility_id)->select('facility_name')->get()->toArray();
                if (!empty($provider_details) && !empty($facility_details)) {
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'];

                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'];
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'];

                    $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['rendering'] = $provider_details[0]['provider_name'];

                    $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['facility_name'] = $facility_details[0]['facility_name'];
                }
            }
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('data', 'start_date', 'end_date', 'rendering_provider', 'billing_provider')));
    }

    public function getProviderFilterExportApi($export = '') {
        $request = Input::get();

        if ($request['start-date'] != '')
            $start_date = $request['start-date'];

        if ($request['end-date'] != '')
            $end_date = $request['end-date'];

        $data = array();

        if ($request['rendering_provider'] != '') {
            if ($request['rendering_provider'] == 'all')
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->get()->toArray();
            else
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('rendering_provider_id', $request['rendering_provider'])->get()->toArray();

            foreach ($claims_details as $claim_list) {
                $rendering_provider_id = $claim_list['rendering_provider_id'];
                $facility_id = $claim_list['facility_id'];
                $provider_details = Provider::with('provider_types')->where('id', $rendering_provider_id)->select('provider_types_id', 'provider_name')->get()->toArray();
                $facility_details = Facility::where('id', $facility_id)->select('facility_name')->get()->toArray();
                if (!empty($provider_details) && !empty($facility_details)) {
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'];

                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'];
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal']))
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'] + $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'];

                    $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['rendering'] = $provider_details[0]['provider_name'];

                    $data[$provider_details[0]['provider_types']['name']][$rendering_provider_id][$claim_list['facility_id']]['facility_name'] = $facility_details[0]['facility_name'];
                }
            }
        }
        if ($request['billing_provider'] != '') {
            if ($request['billing_provider'] == 'all')
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->get()->toArray();
            else
                $claims_details = ClaimInfoV1::where('deleted_at', NULL)->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00")->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59")->where('billing_provider_id', $request['billing_provider'])->get()->toArray();
            foreach ($claims_details as $claim_list) {
                $billing_provider_id = $claim_list['billing_provider_id'];
                $facility_id = $claim_list['facility_id'];
                $provider_details = Provider::with('provider_types')->where('id', $billing_provider_id)->select('provider_types_id', 'provider_name')->get()->toArray();

                $facility_details = Facility::where('id', $facility_id)->select('facility_name')->get()->toArray();
                if (!empty($provider_details) && !empty($facility_details)) {
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['chrg'] = $claim_list['total_charge'];

                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['ptms'] = $claim_list['total_paid'];
                    if (!empty($data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal']))
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'] + $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'];
                    else
                        $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['bal'] = $claim_list['balance_amt'];

                    $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['rendering'] = $provider_details[0]['provider_name'];

                    $data[$provider_details[0]['provider_types']['name']][$billing_provider_id][$claim_list['facility_id']]['facility_name'] = $facility_details[0]['facility_name'];
                }
            }
        }
        $total_charge_count = 0;
        $get_list = array();
        foreach ($data as $key => $list) {
            foreach ($list as $sublist) {

                foreach ($sublist as $subsublist) {
                    $export_data['provider_name'] = $subsublist['rendering'] . " " . ( $key );
                    $export_data['facility_name'] = $subsublist['facility_name'];
                    $export_data['charges'] = $subsublist['chrg'];
                    $export_data['payments'] = $subsublist['ptms'];
                    $export_data['balance'] = $subsublist['bal'];

                    $get_list[$total_charge_count] = $export_data;
                    $total_charge_count++;
                }
            }
        }
        $get_result = $get_list;

        $result["value"] = json_decode(json_encode($get_result));
        $result["exportparam"] = array(
            'filename' => 'Provider Reimbursement Analysis',
            'heading' => '',
            'fields' => array(
                'provider_name' => 'Provider Name',
                'facility_name' => 'Facility Name',
                'charges' => 'Charges',
                'payments' => 'Payments',
                'balance' => 'Balance',
        ));

        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     *  Patient Reports Starts here
     *  Reports for Patient Address List starts here
     *  @return patient_created_date
     */
    public function getPatientAddressListCreatedApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('patient_addresslist');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $patient_created_date = Patient::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_created_date','searchUserData','search_fields')));
    }

    /**
     * Result of Patient Address List

     * @Return start_date,end_date,filter_result.
     */
    public function getPatientAddressListFilterApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
        $search_by = array();    
        $start_date = $end_date = '';
        $filter_data = Patient::where('id','<>',0)->whereNull('deleted_at');
        if ($createdAt != '') {
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date'] = date("m/d/y", strtotime(@$start_date)).' to '.date("m/d/y", strtotime(@$end_date));
            }
            else{
                $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)).' to '.date("m/d/y", strtotime(@$end_date));
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $filter_data->where(DB::raw('(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));

        }

         if (isset($request['user']) && $request['user'] != '') {
            $first_user_id = Patient::where('created_by','!=',0)->orderby('id','asc')->value('created_by');
            if(in_array($first_user_id,explode(',', $request['user']))){
                $filter_data->whereIn('created_by', explode(',', $request['user']))->orWhereIn('created_by',[0,1]);
            }
            else{
                $filter_data->whereIn('created_by', explode(',', $request['user']));
            }
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
            ->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['User'] = $User_name;
            }
            else{
                $search_by['User'][] = $User_name;
            }
        }
         if(isset($request['export'])) {
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        } else{
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_data->paginate($paginate_count);
            $report_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list->data;
        }//dd($filter_result->count());
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    /* patient address listing stored procedure */
    public function getPatientAddressListSPFilterApi($export = '', $data = '') {
        if (isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();

        $start_date = $end_date = $user_ids = '';

       $search_by = [];

        $createdAt = isset($request['created_at']) ? trim($request['created_at']) : "";
        if ($createdAt != '') {
            $date = explode('-', $createdAt);
            $start_date = date("Y-m-d", strtotime(@$date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime(@$date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime(@$start_date)) . ' to ' . date("m/d/y", strtotime(@$end_date));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }

        if (isset($request['user']) && $request['user'] != '') {
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
            ->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $user_ids = $request['user'];
            $search_by['User'][] = $User_name;
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
            $sp_return_result = DB::select('call patientAddressList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->patient_count;
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
            $sp_return_result = DB::select('call patientAddressList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientAddressList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'search_by')));
    }

    /**
     *  Patient Address List export starts Here
     */
    public function getPatientAddressListExportApi($export = '') {

        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];

        $export_addresslist_result = Patient::where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->get();

        $total_adrs_count = 0;
        foreach ($export_addresslist_result as $list) {
            //$data['created_at'] = date("m/d/Y",strtotime($list->created_at));
            $data['last_name'] = @$list->last_name;
            $data['first_name'] = @$list->first_name;
            $data['middle_name'] = @$list->middle_name;
            $data['gender'] = @$list->gender;
            $data['dob'] = @$list->dob;
            $data['ssn'] = @$list->ssn;
            $data['account_no'] = @$list->account_no;
            $data['address1'] = @$list->address1;
            $data['address2'] = @$list->address2;
            $data['city'] = @$list->city;
            $data['state'] = @$list->state;
            $data['zip5'] = @$list->zip5;
            $data['zip4'] = @$list->zip4;
            $get_list[$total_adrs_count] = $data;
            $total_adrs_count++;
        }
        $get_export_result = $get_list;
        $get_export_result[$total_adrs_count] = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'gender' => '', 'dob' => '', 'ssn' => '', 'account_no' => '', 'address1' => '', 'address2' => '', 'city' => '', 'state' => '', 'zip5' => '', 'zip4' => ''];
        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'Patient Address List',
            'heading' => '',
            'fields' => array(
                'last_name' => 'Last Name',
                'first_name' => 'First Name',
                'middle_name' => 'MI',
                'gender' => 'Gender',
                'dob' => 'DOB',
                'ssn' => 'SSN',
                'account_no' => 'Acc No',
                'address1' => 'Address1',
                'address2' => 'Address2',
                'city' => 'City',
                'state' => 'State',
                'zip5' => 'zip5',
                'zip4' => 'zip4',
        ));
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     *  Patient Address List Ends Here
     */

    /**
     *  Patient Demographics List start here
     *  @return patient_create_date.
     */
    public function getPatientDemographicsListApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('patientdemographics');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $patient_create_date = Patient::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_create_date','searchUserData','search_fields')));
    }

    /**
     * Result of Patient Demographics List used stored procedure
     * @Return start_date,end_date,patient_demographics_filter.
     */
    /* patient demographic stored procedure */
    public function getPatientDemographicsSPFilterApi($export = '', $data = '') {
        $request = (!empty($data)) ? $data : Request::All();

        $start_date = $end_date = $dob_start_date = $dob_end_date = $user_ids = $bill_cycle = $emp_name = '';
        $search_by = [];
        if (!empty($request['created_at'])) {
            $date = explode('-', $request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-t');
        }
        $search_by['Transaction Date'][] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date));
        $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
        $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);

        if (!empty($request['dob_search'])) {
            $date = explode('-', $request['dob_search']);
            $dob_start_date = date("Y-m-d", strtotime($date[0]));
            if ($dob_start_date == '1970-01-01') {
                $dob_start_date = '0000-00-00';
            }
            $dob_end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['DOB'][]= date("m/d/Y",strtotime($dob_start_date)).' to '.date("m/d/Y",strtotime($dob_end_date));
        }

        /*if (!empty($request['bill_cycle'])) {
            if (strpos($request['bill_cycle'], ',') !== false) {
                $bill_cycle = $request['bill_cycle'];
                $bill_cycles = array_filter(explode(",", $bill_cycle));
                foreach ($bill_cycles as $cycles) {
                    $bill = explode(' - ', $cycles);
                    $bill1 = $bill[0];
                    $bill2 = $bill[1];
                }
            } else {
                $bill = explode(' - ', $request['bill_cycle']);
                $bill1 = $bill[0];
                $bill2 = $bill[1];
            }
        }*/
        if (isset($request['bill_cycle']) && $request['bill_cycle'] != "") {
            $bill_cycle = $request['bill_cycle'];
            $search_by['Bill Cycle'][] =  $request['bill_cycle'];
        }

        if (!empty($request['user'])) {
            $user_ids = $request['user'];
            $User_name = Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }
        if (!empty($request['emp_name'])) {
            $emp_name = $request['emp_name'];
            $search_by['Employer Name'][] =  $request['emp_name'];
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
        //echo("CR Start".$start_date." ## ".$end_date."## ".$dob_start_date."##".$dob_end_date."##".$bill1."##".$bill2."##".$user_ids."##".$emp_name);
        if ($export == "") {

            $recCount = 1;
            $sp_return_result = DB::select('call patientsDemographic("' . $start_date . '", "' . $end_date . '","' . $dob_start_date . '", "' . $dob_end_date . '", "' . $bill_cycle . '",  "' . $user_ids . '",  "' . $emp_name . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->patient_count;
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
            }else{
                if($paginate_count > $count){
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }
            $recCount = 0;
            $sp_return_result = DB::select('call patientsDemographic("' . $start_date . '", "' . $end_date . '","' . $dob_start_date . '", "' . $dob_end_date . '", "' . $bill_cycle . '",  "' . $user_ids . '",  "' . $emp_name . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientsDemographic("' . $start_date . '", "' . $end_date . '","' . $dob_start_date . '", "' . $dob_end_date . '", "' . $bill_cycle . '",  "' . $user_ids . '",  "' . $emp_name . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'search_by')));
    }

    /**
    * Normal patient demo listing
    */
    public function getPatientDemographicsFilterApi($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
            $filter_data = Patient::with(['contact_details' => function($q) {
                $q->whereIn('category', ["Emergency Contact", "Guarantor", "Employer"])
                ->orderby('id', 'desc');
            }])->with(['patient_insurance' => function($q) {
                $q->whereIn('category', ["Primary", "Secondary", "Tertiary"])
                ->orderby(DB::raw('DATE(updated_at)'), 'desc')->groupby('category', 'patient_id');
            }])->with('creator');
        $search_by = array();
        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Transaction Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $filter_data->where(DB::raw('(created_at)'), '>=', $start_date)->where(DB::raw('(created_at)'), '<=', $end_date);
        }else{
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Transaction Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
        }
        if(!empty($request['dob_search'])){
            $date = explode('-',$request['dob_search']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
                 $filter_data->where(DB::raw('DATE(dob)'), '>=', $start_date)->where(DB::raw('DATE(dob)'), '<=', $end_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['DOB']= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
            }else{
                $search_by['DOB'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
            }
        }
        if(!empty($request['bill_cycle'])){
            if( strpos($request['bill_cycle'], ',') !== false ){
                $bill_cycle = $request['bill_cycle'];
                $filter_data->Where(function ($query) use ($bill_cycle) {
                    $bill_cycles = array_filter(explode(",", $bill_cycle));
                    $sub_sql = '';
                    foreach ($bill_cycles as $cycles) {
                        $bill = explode(' - ',$cycles);
                        $bill1= $bill[0];
                        $bill2= $bill[1];
                        
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        $sub_sql .= "LOWER(LEFT(last_name, 1)) between '". $bill1."' and '". $bill2."'";                        
                    }
                    if ($sub_sql != '')
                        $query->orWhereRaw($sub_sql);
                })->orderby('patients.last_name','asc');                
            } else{
                $bill = explode(' - ',$request['bill_cycle']);
                $bill1= $bill[0];
                $bill2= $bill[1];
                $filter_data->whereRaw("LOWER(LEFT(last_name, 1)) between '". $bill1."' and '". $bill2."'")->orderby('patients.last_name','asc');                
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Bill Cycle'] =  $request['bill_cycle'];                
            }else{
                $search_by['Bill Cycle'][] =  $request['bill_cycle'];            
            }
        }
        if(!empty($request['user'])){
            $filter_data->WhereIn('created_by',explode(',',$request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
            ->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['User'] = $User_name;
            }else{
                $search_by['User'][] = $User_name;
            }
        }
        if(!empty($request['emp_name'])){
            $emp_name = $request['emp_name'];
            $filter_data->with('contact_details')->whereHas('contact_details' ,function($q)use($emp_name) {
                $q->Where('employer_name', 'LIKE', '%' . $emp_name . '%');
            });
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Employer Name'] =  $request['emp_name'];
            }else{
                $search_by['Employer Name'][] =  $request['emp_name'];
            }
        }
        
        if (isset($request['export']) && $request['export'] == 'pdf'){
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        } elseif (isset($request['export']) && $request['export'] == 'xlsx'){
            $filter_result = $filter_data->get();
        }else{
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_data->paginate($paginate_count);
            $report_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list->data;
        } 
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    /**
     *  Patient Demographics Export Starts here
     */


    public function getPatientDemographicsExportApi($export = '') {
        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];

        $export_demographics_result = Patient::with(['contact_details' => function($q) {
                $q->whereIn('category', ["Emergency Contact"])->
                        orderby(DB::raw('DATE(updated_at)'), 'desc')->limit(1);
            }])->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->with('creator')->get();

        $total_demo_count = 0;
        foreach ($export_demographics_result as $list) {
            //$data['created_at'] = date("m/d/Y",strtotime($list->created_at));
            $data['last_name'] = @$list->last_name;
            $data['first_name'] = @$list->first_name;
            $data['middle_name'] = @$list->middle_name;
            $data['gender'] = @$list->gender;
            $data['dob'] = @$list->dob;
            $data['ssn'] = @$list->ssn;
            $data['account_no'] = @$list->account_no;
            $data['responsibility'] = (@$list->is_self_pay == 'Yes') ? 'Self Pay' : "insurance";
            $data['phone'] = @$list->phone;
            $data['email_id'] = @$list->email;
            $data['guarantor_name'] = Helpers::getNameformat(@$list->guarantor_last_name, @$list->guarantor_first_name, @$list->guarantor_middle_name);
            if (count(@$list->contact_details) > 0) {
                foreach (@$list->contact_details as $contact_details) {
                    $data['er_contactpersion'] = Helpers::getNameformat(@$contact_details->emergency_last_name, @$contact_details->emergency_first_name, @$contact_details->emergency_middle_name);
                    $data['er_home_phone'] = @$contact_details->emergency_home_phone;
                    $data['er_cell_phone'] = @$contact_details->emergency_cell_phone;
                }
            } else {
                $data['er_contactpersion'] = '-';
                $data['er_home_phone'] = '-';
                $data['er_cell_phone'] = '-';
            }
            $data['employer_name'] = @$list->employer_name;
            $data['user'] = @$list->creator->short_name;
            $data['created_date'] = Helpers::dateFormat(@$list->created_at, 'date');
            $get_list[$total_demo_count] = $data;
            $total_demo_count++;
        }
        $get_export_result = $get_list;
        $get_export_result[$total_demo_count] = ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'gender' => '', 'dob' => '', 'ssn' => '', 'account_no' => '', 'responsibility' => '', 'phone' => '', 'email_id' => '', 'guarantor_name' => '', 'er_contactpersion' => '', 'er_home_phone' => '', 'er_cell_phone' => '', 'employer_name' => '', 'created_date' => '', 'user' => ''];
        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'Patient Demographics Report',
            'heading' => '',
            'fields' => array(
                'last_name' => 'LastName',
                'first_name' => 'First Name',
                'middle_name' => 'Middle Name',
                'gender' => 'Gender',
                'dob' => 'Dob',
                'ssn' => 'SSN',
                'account_no' => 'Acc No',
                'responsibility' => 'Responsibility',
                'phone' => 'Phone',
                'email_id' => 'Email Id',
                'guarantor_name' => 'Guarantor Name',
                'er_contactpersion' => 'ER ContactPersion',
                'er_home_phone' => 'ER Home Phone',
                'er_cell_phone' => 'ER Cell Phone',
                'employer_name' => 'Employer Name',
                'created_date' => 'Created Date',
                'user' => 'User'));
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     *  Patient Demographics List Ends here
     */

    /**
     *  Patient Patient Icd Worksheet List start here
     *  @return patient_create_date.
     */
    public function getPatientIcdWorksheetListApi() {
        $patient_create_date = Patient::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('patienticdworksheet');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_create_date','search_fields','searchUserData')));
    }

    /**
     * Result of Patient Icd Worksheet List
     * @Return start_date,end_date,payment_details.
     */
    public function getPatientIcdWorksheetFilterApi($export = '', $data = '') {
        $search_by = array();
        $request = (!empty($data)) ? $data : Request::All();

        if(isset($request['icd'])){
            $icd = explode(',', $request['icd']);
            $icd = array_flatten(Icd::whereIn('icd_code',$icd)->select('id')->get()->toArray());
        }
        $transaction_date = isset($request['transaction_date'])?str_replace('"', '', $request['transaction_date']):"";

        $dob_search = isset($request['dob_search'])?str_replace('"', '', $request['dob_search']):"";
        $payment_data = Patient::with(['patient_insurance' => function($q){
                    $q->where('category','Primary');
                }])->has('patient_claim')->with(['patient_claim' => function($query) {
                        $query->select('patient_id', 'icd_codes','insurance_id');
                    }])->where('status','=','Active');

        if ($transaction_date != ""){
            $exp = explode("-",$transaction_date);
            $start_date = date("m/d/y", strtotime($exp[0]));
            $end_date = date("m/d/y", strtotime($exp[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date'] = $start_date.' to '.$end_date;
            }
            else{
                $search_by['Transaction Date'][] = $start_date.' to '.$end_date;    
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $payment_data->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
        }else{
            $start_date = "";
            $end_date = "";
        }
    
        if(isset($request['acc_no']) && $request['acc_no']!=""){
            $payment_data->where('account_no', 'like', '%'.$request['acc_no'].'%');
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Patient Acc No'] = $request['acc_no'];    
            }
            else{
                $search_by['Patient Acc No'][] = $request['acc_no'];    
            }                
        }
        if(isset($request['insurance_id']) && $request['insurance_id']!=""){
            $insurance = explode(',',$request['insurance_id']);
            $payment_data->whereHas('patient_insurance',function($q)use($insurance){
                $q->where('category','Primary')->whereIn('insurance_id',$insurance);
            });
            $insurance_name = Insurance::selectRaw('GROUP_CONCAT(short_name SEPARATOR ",") as short_name')->whereIn('id',$insurance)->get()->toArray();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Insurance'] = $insurance_name[0]['short_name'];    
            }
            else{
                $search_by['Insurance'][] = $insurance_name[0]['short_name'];    
            }
        }//dd($payment_data->get()->toArray());
        if(isset($request['bill_cycle'])){
            if( strpos($request['bill_cycle'], ',') !== false ){
                $bill_cycle = $request['bill_cycle'];
                $payment_data->Where(function ($query) use ($bill_cycle) {
                    $bill_cycles = array_filter(explode(",", $bill_cycle));
                    $sub_sql = '';
                    foreach ($bill_cycles as $cycles) {
                        $bill = explode(' - ',$cycles);
                        $bill1= $bill[0];
                        $bill2= $bill[1];
                        
                        $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                        $sub_sql .= "LOWER(LEFT(last_name, 1)) between '". $bill1."' and '". $bill2."'";                        
                    }
                    if ($sub_sql != '')
                            $query->orWhereRaw($sub_sql);
                    })->orderby('last_name','asc');                
            } else{
                    $bill = explode(' - ',$request['bill_cycle']);
                    $bill1= $bill[0];
                    $bill2= $bill[1];
                    $payment_data->whereRaw("LOWER(LEFT(last_name, 1)) between '". $bill1."' and '". $bill2."'")->orderby('last_name','asc');                
            }
            if (isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Bill Cycle'] =  $request['bill_cycle'];                
            }else{
                $search_by['Bill Cycle'][] =  $request['bill_cycle'];            
            }
        }
           

        if(!empty($icd)){
            foreach($icd as $key=>$icd_code){
                if($key==0){
                    $q = "(find_in_set('$icd_code',icd_codes) ";
                }else{
                    if((count($icd)-1)==$key)
                        $q .= "or find_in_set('$icd_code',icd_codes)";
                    else
                        $q .= "or find_in_set('$icd_code',icd_codes)";
                }
            }
            $q .= ")";
                    $payment_data->whereHas('patient_claim' , function($query) use ($q) {
                        $query->whereRaw($q);
                    });
            if(isset($request['exports']) && $request['exports']!=""){
                $search_by['ICD 10'] = implode(',',array_flatten(ICD::whereIn('id',$icd)->select('icd_code')->get()->toArray()));    
            }
            else{
                $search_by['ICD 10'][] = implode(',',array_flatten(ICD::whereIn('id',$icd)->select('icd_code')->get()->toArray()));    
            }
                
        }
        if(!empty($request['user'])){
            $payment_data->whereHas('patient_claim' , function($query) use ($request) {
                    $query->whereIn("created_by", explode(',', $request['user']));
            });
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if(isset($request['exports']) && $request['exports']!=""){
                $search_by['User'][] = $User_name;    
            }
            else{
                $search_by['User'][] = $User_name;    
            }
            
        }
        if ($dob_search != ""){
            $exp = explode("-",$dob_search);
            $dob_start_date = $exp[0];
            $dob_end_date = $exp[1];
            $payment_data = $payment_data->where(DB::raw('DATE(dob)'), '>=', date("Y-m-d", strtotime($dob_start_date)))->where(DB::raw('DATE(dob)'), '<=', date("Y-m-d", strtotime($dob_end_date)));
            if(isset($request['exports']) && $request['exports']!=""){
                $search_by['DOB'] = $dob_start_date.' to '.$dob_end_date;   
            }
            else{
             $search_by['DOB'][] = $dob_start_date.' to '.$dob_end_date;
            }
        }
        if(isset($request['export']) && $request['export']!=""){
            $payment_details = $payment_data->get();
            foreach ($payment_details as $key => $payment_detail) {
                $icd_ids = $payment_detail->patient_claim->pluck('icd_codes')->all();
                $value = implode(",", array_unique(explode(",", implode(',', array_values($icd_ids)))));
                $payment_details[$key]->patient_icd = $value;
            }
            $claims_list = json_decode($payment_details->toJson());
            $payment_details = $claims_list;
        }
        else {

            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $claims = $payment_data->paginate($paginate_count);
            $claim_array = $claims->toArray();
            $pagination_prt = $claims->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $claim_array['total'], 'per_page' => $claim_array['per_page'], 'current_page' => $claim_array['current_page'], 'last_page' => $claim_array['last_page'], 'from' => $claim_array['from'], 'to' => $claim_array['to'], 'pagination_prt' => $pagination_prt);
            $payment_details = $claims;
            foreach ($payment_details as $key => $payment_detail) {
                $icd_ids = $payment_detail->patient_claim->pluck('icd_codes')->all();
                $value = implode(",", array_unique(explode(",", implode(',', array_values($icd_ids)))));
                $payment_details[$key]->patient_icd = $value;
            }
            $claims_list = json_decode($payment_details->toJson());
            $payment_details = $claims_list->data;
        } 
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'payment_details', 'pagination','search_by')));
    }
    /** store procedure for icd work sheet **/
    public function getPatientIcdWorksheetFilterApiSP($export = '', $data = '') {
        $request = (!empty($data)) ? $data : Request::All();
        $start_date = $end_date = $dob_start_date =  $dob_end_date = $user_ids = $acc_no = $icd = $bill_cycle = $insurance = '';
        $search_by = [];

        if(isset($request['icd']) && $request['icd']!=""){
            $icd = $request['icd'];
        }
        $transaction_date = isset($request['transaction_date'])?str_replace('"', '', $request['transaction_date']):"";

        $dob_search = isset($request['dob_search'])?str_replace('"', '', $request['dob_search']):"";

        if(!empty($request['transaction_date'])){
            $date = explode('-',$request['transaction_date']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
        }
        $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
        $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);


       if(!empty($request['dob_search'])){
           $date = explode('-',$request['dob_search']);
           $dob_start_date = date("Y-m-d", strtotime($date[0]));
           if($dob_start_date == '1970-01-01'){
               $dob_start_date = '0000-00-00';
           }
           $dob_end_date = date("Y-m-d", strtotime($date[1]));
       }

        if(isset($request['more'])){
            if(isset($request['acc_no']) && $request['acc_no']!=""){
                $acc_no = $request['acc_no'];
                $search_by['Patient Acc No'][] = $request['acc_no'];
            }
            if(isset($request['insurance_id']) && $request['insurance_id']!=""){
                $insurance = implode(",",$request['insurance_id']);
            }

            if(isset($request['bill_cycle']) && $request['bill_cycle']!=""){
                $bill_cycle = implode(",",$request['bill_cycle']);
            }
        }
        if(!empty($request['user'])){
            $user_ids  = $request['user'];
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
            $sp_return_result = DB::select('call patientIcdWorksheet("' . $start_date . '", "' . $end_date . '",  "' . $dob_start_date . '", "' . $dob_end_date . '", "' . $user_ids . '",  "' . $acc_no . '",  "' . $icd . '", "' . $bill_cycle . '", "' . $insurance . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->patient_count;
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
            $sp_return_result = DB::select('call patientIcdWorksheet("' . $start_date . '", "' . $end_date . '",  "' . $dob_start_date . '", "' . $dob_end_date . '", "' . $user_ids . '",  "' . $acc_no . '",  "' . $icd . '", "' . $bill_cycle . '", "' . $insurance . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientIcdWorksheet("' . $start_date . '", "' . $end_date . '",  "' . $dob_start_date . '", "' . $dob_end_date . '", "' . $user_ids . '",  "' . $acc_no . '",  "' . $icd . '", "' . $bill_cycle . '", "' . $insurance . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $payment_details = $sp_return_result;

        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'payment_details', 'pagination','search_by')));
    }

    /**
     * patient icd worksheet Export Starts here
     */
    public function getFilterIcdWorksheetExportApi($export = '') {

        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        $payment_details = Patient::with(['patient_claim' => function($query) {
                        $query->select('patient_id', 'icd_codes');
                    }])->has('patient_claim')->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->get();

        /*                                 * *
          $total_demo_count = 0;
          foreach($export_demographics_result as $list)
          {
          //$data['created_at'] = date("m/d/Y",strtotime($list->created_at));
          $data['last_name'] = $list->last_name;
          $data['first_name'] = $list->first_name;
          $data['middle_name'] = @$list->middle_name;
          $data['gender'] = $list->gender;
          $data['dob'] =$list->dob;
          $data['ssn'] = @$list->ssn;
          $data['account_no'] = $list->account_no;
          $data['responsibility'] = ($list->is_self_pay == 'Yes') ? 'Self Pay' : "insurance";
          $data['phone'] = $list->phone;
          $data['email_id'] = $list->email;
          $data['guarantor_name'] = Helpers::getNameformat($list->guarantor_last_name, @$list->guarantor_first_name,@$list->guarantor_middle_name);
          if(count(@$list->contact_details) > 0)
          {
          foreach(@$list->contact_details as $contact_details)
          {
          $data['er_contactpersion'] = Helpers::getNameformat(@$contact_details->emergency_last_name, @$contact_details->emergency_first_name, @$contact_details->emergency_middle_name);
          $data['er_home_phone'] = @$contact_details->emergency_home_phone;
          $data['er_cell_phone'] = @$contact_details->emergency_cell_phone;
          }
          }
          else
          {
          $data['er_contactpersion']='-';
          $data['er_home_phone']='-';
          $data['er_cell_phone']='-';
          }
          $data['employer_name'] = $list->employer_name;
          $data['user'] = $list->creator->name;
          $data['created_date'] = Helpers::dateFormat($list->created_at, 'date');
          $get_list[$total_demo_count] = $data;
          $total_demo_count++;
          }
          $get_export_result=$get_list;
          $get_export_result[$total_demo_count] = ['last_name'=>'','first_name'=>'','middle_name'=>'','gender'=>'','dob'=>'','ssn'=>'','account_no'=>'','responsibility'=>'','phone'=>'','email_id'=>'','guarantor_name'=>'','er_contactpersion'=>'','er_home_phone'=>'','er_cell_phone'=>'','employer_name'=>'','created_date'=>'','user'=>''];
          $result["value"] = json_decode(json_encode($get_export_result));
          $result["exportparam"] = array(
          'filename' => 'Patient Demographics Report',
          'heading' => '',
          'fields' => array(
          'last_name' => 'LastName',
          'first_name' => 'First Name',
          'middle_name' => 'Middle Name',
          'gender' => 'Gender',
          'dob' => 'Dob',
          'ssn' => 'SSN',
          'account_no' => 'Acc No',

          'responsibility' => 'Responsibility',
          'phone' => 'Phone',
          'email_id' => 'Email Id',
          'guarantor_name' => 'Guarantor Name',
          'er_contactpersion' => 'ER ContactPersion',
          'er_home_phone' => 'ER Home Phone',
          'er_cell_phone' => 'ER Cell Phone',
          'employer_name' => 'Employer Name',
          'created_date' => 'Created Date',
          'user' => 'User'
          ));
          //dd($result);
          $callexport = new CommonExportApiController();
          return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
         * * */
        foreach ($payment_details as $key => $payment_detail) {
            $icd_ids = @$payment_detail->patient_claim->pluck('icd_codes')->all();

            $value = implode(",", array_unique(explode(",", implode(',', array_values($icd_ids)))));
            $payment_details[$key]->patient_icd = $value;
        }
        $total_icd_count = 0;
        foreach ($payment_details as $payment_detal) {
            $icd_values = Icd::getIcdValues(@$payment_detal->patient_icd);
            @$icd_val[] = count($icd_values);
        }

        @$maxval = max($icd_val);
        foreach ($payment_details as $payment_detal) {
            $icd_values = Icd::getIcdValues(@$payment_detal->patient_icd);
            $data['name'] = Helpers::getNameformat(@$payment_detal->last_name, @$payment_detal->first_name, @$payment_detal->middle_name);
            $data['dob'] = @$payment_detal->dob;
            $data['account_no'] = @$payment_detal->account_no;
            $cart = array();
            for ($i = 1; $i <= @$maxval; $i++) {
                if (isset($icd_values[$i]))
                    $data['icd' . $i] = $icd_values[$i];
                else
                    $data['icd' . $i] = '-';
                //array_push($cart,'-');
            }
            //$data['icd']=$cart;
            $get_list[$total_icd_count] = $data;
            $total_icd_count++;
        }
        //dd($get_list);
        $get_export_result = $get_list;
        $get_export_result[$total_icd_count] = ['name' => '', 'dob' => '', 'account_no' => '', 'icd' => ''];
        $result["value"] = json_decode(json_encode($get_export_result));
        /* $icd1=array();
          for($i=1;$i<=$maxval;$i++)
          {
          $icd1[]='ICD10';
          }
          //dd($icd1);
          $ca=array();

          array_push($ca,$icd1); */
        //dd($ca);
        $result["exportparam"] = array(
            'filename' => 'Patient ICD Worksheet Report',
            'heading' => '',
            'fields' => array(
                'name' => 'Name',
                'dob' => 'DOB',
                'account_no' => 'Acc No',
            //'icd' => $ca[0]
        ));
        for ($i = 1; $i <= $maxval; $i++) {
            $result["exportparam"]['fields']['icd' . $i] = 'ICD10';
        }
        //echo "<pre>";var_dump($result["exportparam"]);
        //echo "<pre>"; var_dump($result["value"]);
        //dd($result);
        //dd($result["value"]);
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     * patient icd worksheet list ends here
     */

    /**
     * patient AR report starts here
     */
    public function getPatientArReportListApi() {
        $patient_create_date = Patient::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_create_date')));
    }

    public function getPatientArReportApi() {
        $age_dates = ["0-30", "31-60", "61-90", "91-120", "121-150", "150-above"];
        $request = Request::All();
        $patar = [];
        $patient_data = [];
        //
        $arresult = [];
        $agingdays = $request['aging_days'];

        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        //$arresult=[];

        if ($agingdays == "0-30") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "31-60") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(31)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "61-90") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(61)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "91-120") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(91)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "121-150") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(121)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "150-above") {
            //$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(150)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(151)));
            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '<>', '0000-00-00')->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } else {

            // $billed[$value] =$unbilled->get();
            foreach ($age_dates as $key => $age_date) {
                if ($age_date != "150-above") {
                    $ageingdate = explode('-', $age_date);
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay($ageingdate[1])));
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay($ageingdate[0])));
                    $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
                }
                if ($age_date == "150-above") {
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(151)));
                    $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '<>', '0000-00-00')->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
                }
                foreach ($patient_claims as $patient_claim) {
                    $patient_data[$patient_claim->account_no][$age_date] = $patient_claim->balance_amt;
                }
            }
        }
        //$patient_data[$key]=$patient_data;
        $arresult = $patient_claims;
        //dd($arresult);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('arresult', 'agingdays', 'start_date', 'end_date', 'patient_data')));
    }

    public function getPatientArExportApi($export = '') {
        $age_dates = ["0-30", "31-60", "61-90", "91-120", "121-150", "150-above"];
        $request = Request::All();
        $patar = [];
        $patient_data = [];

        $arresult = [];
        $agingdays = $request['aging_days'];
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        //$arresult=[];

        if ($agingdays == "0-30") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(30)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(0)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "31-60") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(60)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(31)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "61-90") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(90)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(61)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "91-120") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(120)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(91)));

            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "121-150") {
            $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay(150)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(121)));
            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } elseif ($agingdays == "150-above") {
            //$last_month_carbon = date('Y-m-d',strtotime(Carbon::now()->subDay(150)));
            $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(151)));
            $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '<>', '0000-00-00')->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
        } else {
            // $billed[$value] =$unbilled->get();
            foreach ($age_dates as $key => $age_date) {

                if ($age_date != "150-above") {
                    $ageingdate = explode('-', $age_date);
                    $last_month_carbon = date('Y-m-d', strtotime(Carbon::now()->subDay($ageingdate[1])));
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay($ageingdate[0])));
                    $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '>=', $last_month_carbon)->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
                }
                if ($age_date == "150-above") {
                    $current_month = date('Y-m-d ', strtotime(Carbon::now()->subDay(151)));
                    $patient_claims = ClaimInfoV1::LeftJoin('patients', 'patients.id', '=', 'patient_id')->selectRaw('account_no, CONCAT(first_name," ", Last_Name, " ",middle_name) as full_name ,sum(balance_amt) as balance_amt')->where(DB::raw('DATE(patients.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(patients.created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->where('submited_date', '<>', '0000-00-00')->where('submited_date', '<=', $current_month)->groupBy('claim_info_v1.patient_id')->get();
                }
                foreach ($patient_claims as $patient_claim) {
                    $patient_data[$patient_claim->account_no][$age_date] = $patient_claim->balance_amt;
                }
            }
        }
        //$patient_data[$key]=$patient_data;
        $arresult = $patient_claims;
        $total_ar_count = 0;
        @$count = 0;
        @$total_adj = 0;
        @$patient_total = 0;
        @$insurance_total = 0;
        @$get_list = array();

        if ($agingdays == 'all') {
            foreach ($patient_data as $key => $patient_data) {
                $data['acc_no'] = @$key;
                $pname = Patient::where('account_no', $key)->select('last_name', 'middle_name', 'first_name')->first();
                $name = App\Http\Helpers\Helpers::getNameformat(@$pname->last_name, @$pname->first_name, @$pname->middle_name);
                $data['patient_name'] = @$name;
                $data['0-30'] = (@$patient_data['0-30'] != '') ? @$patient_data['0-30'] : "0.00";
                $data['31-60'] = (@$patient_data['31-60'] != '') ? @$patient_data['31-60'] : "0.00";
                $data['61-90'] = (@$patient_data['61-90'] != '') ? @$patient_data['61-90'] : "0.00";
                $data['91-120'] = (@$patient_data['91-120'] != '') ? @$patient_data['91-120'] : "0.00";
                $data['121-150'] = (@$patient_data['121-150'] != '') ? @$patient_data['121-150'] : "0.00";
                $data['150-above'] = (@$patient_data['150-above'] != '') ? @$patient_data['150-above'] : "0.00";

                $a = array(@$patient_data['0-30'], @$patient_data['31-60'], @$patient_data['61-90'], @$patient_data['91-120'], @$patient_data['121-150'], @$patient_data['150-above']);
                $data['total'] = array_sum($a);
                $get_list[$total_ar_count] = $data;
                $total_ar_count++;
            }
        } else {
            foreach ($arresult as $list) {
                $data['acc_no'] = @$list->account_no;
                $data['patient_name'] = @$list->full_name;
                $data['0-30'] = (@$agingdays == '0-30') ? @$list->balance_amt : "0.00";
                $data['31-60'] = (@$agingdays == '31-60') ? @$list->balance_amt : "0.00";
                $data['61-90'] = (@$agingdays == '61-90') ? @$list->balance_amt : "0.00";
                $data['91-120'] = (@$agingdays == '91-120') ? @$list->balance_amt : "0.00";
                $data['121-150'] = (@$agingdays == '121-150') ? @$list->balance_amt : "0.00";
                $data['150-above'] = (@$agingdays == '150-above') ? @$list->balance_amt : "0.00";
                $data['total'] = @$list->balance_amt;
                $get_list[$total_ar_count] = $data;
                $total_ar_count++;
            }
        }

        $get_export_result = $get_list;
        $get_export_result[$total_ar_count] = ['acc_no' => '', 'patient_name' => '', '0-30' => '', '31-60' => '', '61-90' => '', '91-120' => '', '121-150' => '', '150-above' => '', 'total' => ''];
        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'Patient Aging Analysis',
            'heading' => '',
            'fields' => array(
                'acc_no' => 'Acc No',
                'patient_name' => 'Patient Name',
                '0-30' => '0-30($)',
                '31-60' => '31-60($)',
                '61-90' => '61-90($)',
                '91-120' => '91-120($)',
                '121-150' => '121-150($)',
                '150-above' => '151-Above($)',
                'total' => 'Total($)'
        ));
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     * patient AR report ends here
     */
    /**
     * patient reports ends here.
     */

    /**
     *  Pratice Setting Reports starts here
     *  Employer List start here
     *  @Return employer_create_date.
     */
    public function getEmployerListApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('employer_summery');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

        $employer_create_date = Employer::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('employer_create_date','searchUserData','search_fields')));
    }

    /**
     * Result of Employer List
     * @Return start_date,end_date,employer_filter_result.
     */
    public function getEmployerListReportApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $search_by = array();
        $start_date = $end_date = '0000-00-00';
        if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime($start_date)).' to '.date("m/d/y", strtotime($end_date));

        }
        $filter_result_data = Employer::where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));

        if (isset($request['user']) && $request['user'] != '') {
            $filter_result_data->whereIn('created_by', explode(',', $request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')
            ->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }

        if ($export == "") {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_result_data->paginate($paginate_count);
            $filter_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $filter_array['total'], 'per_page' => $filter_array['per_page'], 'current_page' => $filter_array['current_page'], 'last_page' => $filter_array['last_page'], 'from' => $filter_array['from'], 'to' => $filter_array['to'], 'pagination_prt' => $pagination_prt);
            $filter_list = json_decode($filter_result->toJson());
            $filter_result = $filter_list->data;
        } else {
            $filter_result = $filter_result_data->get();
            $filter_list = json_decode($filter_result->toJson());
            $filter_result = $filter_list;
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    //Stored procedure for employers in practice indicators
    public function getEmployerListReportApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $start_date = $end_date = $user_ids = '';
        $search_by = array();
        $start_date = $end_date = '0000-00-00';
        if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Transaction Date'][] = date("m/d/y", strtotime($start_date)).' to '.date("m/d/y", strtotime($end_date));
        }
        if (isset($request['user']) && $request['user'] != '') {
           $user_ids = $request['user'];
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
            $sp_return_result = DB::select('call employers("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->employers_count;
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
            $sp_return_result = DB::select('call employers("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call employers("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;


        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    public function getEmployerListExportApi($export = '') {
        $request = Request::All();
        if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        $export_result = Employer::where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->get();

        $total_charge_count = 0;
        foreach ($export_result as $list) {
            //$data['created_at'] = date("m/d/Y",strtotime($list->created_at));
            $data['employer_name'] = @$list->employer_name;
            $data['address1'] = @$list->address1;
            $data['address2'] = @$list->address2;
            $data['city'] = @$list->city;
            $data['state'] = @$list->state;
            $data['zip5'] = @$list->zip5;
            $data['zip4'] = @$list->zip4;
            $get_list[$total_charge_count] = $data;
            $total_charge_count++;
        }
        $get_export_result = $get_list;
        $get_export_result[$total_charge_count] = ['employer_name' => '', 'address1' => '', 'address2' => '', 'city' => '', 'state' => '', 'zip5' => '', 'zip4' => ''];
        $result["value"] = json_decode(json_encode($get_export_result));
        $result["exportparam"] = array(
            'filename' => 'Employer List',
            'heading' => '',
            'fields' => array(
                'employer_name' => 'Employer Name',
                'address1' => 'Address Line 1',
                'address2' => 'Address Line 2',
                'city' => 'City',
                'state' => 'State',
                'zip5' => 'zip5',
                'zip4' => 'zip4',
        ));
        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    /**
     *  Employer List Ends here
     *  Practice Setting Ends here
     */


    /* Patient wallet history report start */

    public function getPatientWalletHistoryListApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('wallet_history');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData','search_fields')));
    }


    public function getPatientWalletHistoryFilterApi($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $filter_data = PMTInfoV1::with(['patient','checkDetails','creditCardDetails', 'eftDetails','insurancedetail', 'created_user'])
                    ->where('pmt_method', 'Patient')
                    ->where(DB::raw('(pmt_info_v1.pmt_amt - pmt_info_v1.amt_used)'), '>', 0)
                    //->whereIn('pmt_type', ['Payment', 'Refund', 'Credit Balance'])
                    ->whereIn('pmt_type', ['Payment', 'Credit Balance'])
                    ->where('void_check', NULL)
                    ->whereNull('deleted_at')
                    ->where('pmt_amt', '>', 0);
        $search_by = array();
       if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
            $filter_data->where(DB::raw('(created_at)'), '>=', $start_date)
                           ->where(DB::raw('(created_at)'), '<=', $end_date);
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Transaction Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            else{
                $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));    
            }
            
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }

        if(!empty($request['pmt_mode'])){
            $pmt_modes = explode(',', $request['pmt_mode']);
            $filter_data->whereIn('pmt_mode', $pmt_modes);
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Payment Mode'] =  $request['pmt_mode'];    
            }
            else{
                $search_by['Payment Mode'][] =  $request['pmt_mode'];    
            }
            
        }
        if (isset($request['user']) && $request['user'] != '') {
            $filter_data->whereIn('created_by', explode(',', $request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['User'] = $User_name;    
            } 
            else{
                $search_by['User'][] = $User_name;    
            }
            
        }

        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        } elseif(isset($request['export']) && $request['export'] == 'xlsx') {
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        }else {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_data->paginate($paginate_count);
            $report_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list->data;
        } 
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'search_by')));
    }
    /* store procedure function for patient wallethistory */
    public function getPatientWalletHistoryFilterApiSP($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $start_date = $end_date = $pmt_modes = $user_ids = '';
        $search_by = array();
       if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
        }

        $search_by['Transaction Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
        $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
        $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        if(!empty($request['pmt_mode'])){
            $pmt_modes = $request['pmt_mode'];
            $search_by['Payment Mode'][] =  $request['pmt_mode'];
        }
        if (isset($request['user']) && $request['user'] != '') {
            $user_ids = $request["user"];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
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
            $sp_return_result = DB::select('call patientWalletHistory("' . $start_date . '", "' . $end_date . '",  "' . $pmt_modes . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->pmt_info_count;
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
            $sp_return_result = DB::select('call patientWalletHistory("' . $start_date . '", "' . $end_date . '",  "' . $pmt_modes . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientWalletHistory("' . $start_date . '", "' . $end_date . '",  "' . $pmt_modes . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'search_by')));
    }

    /* Patient wallet history report end */

    /* Patient statement history report start */

    public function getPatientStatementHistoryListApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('statement_history');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData','search_fields')));
    }

     public function getPatientStatementHistoryFilterApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();

        $filter_data = PatientStatementTrack::with(['patient_detail']);
         $search_by = array();
        if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $filter_data->where(DB::raw('DATE(send_statement_date)'), '>=', $start_date)
                           ->where(DB::raw('DATE(send_statement_date)'), '<=', $end_date);
            if(isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['Statement Date'] =  date('m/d/y',strtotime(@$start_date)).' to '.date('m/d/y',strtotime(@$end_date));    
            }
            else{
                $search_by['Statement Date'][] =  date('m/d/y',strtotime(@$start_date)).' to '.date('m/d/y',strtotime(@$end_date));    
            }
            
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
        }

        if(!empty($request['no_of_statement'])){
            $filter_data->where('statements', '=',  $request['no_of_statement']);
            if(isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['No. of Statements'] = $request['no_of_statement'];   
            }
            else{
             $search_by['No. of Statements'][] = $request['no_of_statement'];
            }
        }
        if (isset($request['user']) && $request['user'] != '') {
            $filter_data->whereIn('created_by', explode(',', $request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            if(isset($request['exports']) && $request['exports'] == 'pdf'){
                $search_by['User'] = $User_name;    
            }
            else{
                $search_by['User'][] = $User_name;    
            }
            
        }
        /*if(isset($request['exports']) && $request['exports'] == 'pdf'){
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        } elseif(isset($request['exports']) && $request['exports'] == 'xlsx'){
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        }*/
        if(isset($request['export']) && $request['export'] == 'xlsx'){
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        }

        else{
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_data->paginate($paginate_count);
            $report_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list->data;
        } 
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }
    /* Call store procedure statement history */
    public function getPatientStatementHistoryFilterApiSP($export = '', $data = '') {
        if (isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $start_date = $end_date = $stmt = $user_ids = '';
        $search_by = array();
        if (!empty($request['created_at'])) {
            $date = explode('-', $request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if ($start_date == '1970-01-01') {
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Statement Date'][] = date('m/d/y', strtotime(@$start_date)) . ' to ' . date('m/d/y', strtotime(@$end_date));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date = date('Y-m-t');
        }

        if (!empty($request['no_of_statement'])) {
            $stmt = $request['no_of_statement'];
            $search_by['No. of Statements'][] = $request['no_of_statement'];
        }
        if (isset($request['user']) && $request['user'] != '') {
            $user_ids = $request['user'];
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
            $sp_return_result = DB::select('call patientStatementHistory("' . $start_date . '", "' . $end_date . '",  "' . $stmt . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->patientstatement_track_count;
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
            $sp_return_result = DB::select('call patientStatementHistory("' . $start_date . '", "' . $end_date . '",  "' . $stmt . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientStatementHistory("' . $start_date . '", "' . $end_date . '",  "' . $stmt . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;

        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination', 'search_by')));
    }

    /* Patient statement history report end */


    /* Patient statement status report start */

    public function getPatientStatementStatusListApi() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('statement_status');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData','search_fields')));
    }

     public function getPatientStatementStatusFilterApi($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();

        //$filter_data = PatientStatementTrack::LeftJoin('patients', 'patients.id', '=', 'patient_id') ->with(['patient_detail']);

        $filter_data = Patient::with(['stmt_category_info','stmt_holdreason_info']);
        $search_by = array();
        //dd($request);

        if(isset($request['acc_no']) && $request['acc_no']!=""){
            $filter_data->where('patients.account_no', 'like', '%'.$request['acc_no'].'%');
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Patient Acc No'] = $request['acc_no'];    
            }
            else{
                $search_by['Patient Acc No'][] = $request['acc_no'];    
            }
        }

        if(isset($request['patient_name']) && $request['patient_name']!=""){
           // $filter_data->where('patients.stmt_category', 'like', '%'.$request['statement_category'].'%');
            $dynamic_name = $request['patient_name'];

            $filter_data->Where(function ($filter_data) use ($dynamic_name) {
                if (strpos(strtolower($dynamic_name), ",") !== false) {
                    $searchValues = array_filter(explode(", ", $dynamic_name));
                    foreach ($searchValues as $value) {
                        if ($value !== '') {
                            $filter_data = $filter_data->orWhere('last_name', 'like', "%{$value}%")
                                    ->orWhere('middle_name', 'like', "%{$value}%")
                                    ->orWhere('first_name', 'like', "%{$value}%");
                        }
                    }
                } else {
                    $filter_data = $filter_data->orWhere('last_name', 'LIKE', '%' . $dynamic_name . '%')
                            ->orWhere('middle_name', 'LIKE', '%' . $dynamic_name . '%')
                            ->orWhere('first_name', 'LIKE', '%' . $dynamic_name . '%');
                }
            });
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Patient Name'] = $request['patient_name'];    
            }
            else{
                $search_by['Patient Name'][] = $request['patient_name'];    
            }
        }

        if(!empty($request['dob_search'])){
            $date = explode('-',$request['dob_search']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
                 $filter_data->where(DB::raw('DATE(dob)'), '>=', $start_date)->where(DB::raw('DATE(dob)'), '<=', $end_date);
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['DOB']= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));    
            }
            else{
                $search_by['DOB'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));    
            }
            
        }

        if(isset($request['ssn']) && $request['ssn']!=""){
            $filter_data->where('patients.ssn', 'like', '%'.$request['ssn'].'%');
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Patient SSN'] = $request['ssn'];    
            }
            else{
                $search_by['Patient SSN'][] = $request['ssn'];    
            }
        }

        if(isset($request['statements']) && $request['statements']!="" && $request['statements']!="All"){
            $filter_data->where('patients.statements', 'like', '%'.$request['statements'].'%');
            $search_by['Patient Statement'][] = $request['statements'];

            if($request['statements'] == "Hold") {
                // Handle hold reason and hold release date only when statement selected as Hold
                if(isset($request['hold_reason']) && $request['hold_reason']!=""){
                    $hldReasons = array_filter(explode(",", $request['hold_reason']));
                    $filter_data->whereIn('patients.hold_reason', $hldReasons);
                    $hold_reason = STMTHoldReason::where('status', 'Active')->whereIn('id', $hldReasons)->pluck('hold_reason')->all();
                    $reasons = (!empty($hold_reason) ) ? implode(", ",$hold_reason) : '';
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Statement Hold Reason'] = @$reasons;    
                    }
                    else{
                        $search_by['Statement Hold Reason'][] = @$reasons;    
                    }
                }

                if(!empty($request['hold_releasedate'])){
                    $date = explode('-',$request['hold_releasedate']);
                    $start_date = date("Y-m-d", strtotime($date[0]));
                    if($start_date == '1970-01-01'){
                        $start_date = '0000-00-00';
                    }
                    $end_date = date("Y-m-d", strtotime($date[1]));
                    $filter_data->where(DB::raw('DATE(hold_release_date)'), '>=', $start_date)
                         ->where(DB::raw('DATE(hold_release_date)'), '<=', $end_date);
                    if (isset($request['exports']) && $request['exports'] == 'pdf') {
                        $search_by['Statement Hold Release Date']= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));    
                    }
                    else{
                        $search_by['Statement Hold Release Date'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));    
                    }
                }

            }
        }

        if(isset($request['statement_category']) && $request['statement_category']!=""){

            $stmtCat = array_filter(explode(",", $request['statement_category']));
            $filter_data->whereIn('patients.stmt_category', $stmtCat);
            $stmt_category = STMTCategory::where('status', 'Active')->whereIn('id', $stmtCat)->pluck('category')->all();
            $catNames = (!empty($stmt_category) ) ? implode(", ",$stmt_category) : '';
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Statement Category']= @$catNames;    
            }
            else{
                $search_by['Statement Category'][] = @$catNames;    
            }
        }
        /*
        if(!empty($request['created_at'])) {
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $filter_data->where(DB::raw('DATE(send_statement_date)'), '>=', $start_date)
                           ->where(DB::raw('DATE(send_statement_date)'), '<=', $end_date);
            $search_by['Statement Date'][] =  date('m/d/y',strtotime(@$start_date)).' to '.date('m/d/y',strtotime(@$end_date));
        } else {
            $start_date = date('Y-m-01'); // hard-coded '01' for first day
            $end_date  = date('Y-m-t');
        }


        if(!empty($request['no_of_statement'])){
            $filter_data->where('statements', '=',  $request['no_of_statement']);
             $search_by['No. of Statements'][] = $request['no_of_statement'];
        }
        if (isset($request['user']) && $request['user'] != '') {
            $filter_data->whereIn('created_by', explode(',', $request['user']));
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->lists('short_name', 'id');
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }
        */
        if ( (isset($request['export']) && $request['export'] == 'xlsx') || (isset($request['export']) && $request['export'] == 'pdf')) {
            $filter_result = $filter_data->get();
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list;
        } else {
            $pagination = '';
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $filter_result = $filter_data->paginate($paginate_count);
            $report_array = $filter_result->toArray();
            $pagination_prt = $filter_result->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $report_array['total'], 'per_page' => $report_array['per_page'], 'current_page' => $report_array['current_page'], 'last_page' => $report_array['last_page'], 'from' => $report_array['from'], 'to' => $report_array['to'], 'pagination_prt' => $pagination_prt);
            $reports_list = json_decode($filter_result->toJson());
            $filter_result = $reports_list->data;
        }
        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    /* stored procedure for statement status */

    public function getPatientStatementStatusFilterApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $acc_no = $patient_name = $dob_start_date = $dob_end_date = $ssn = $statements = $hold_reason = $start_date = $end_date = $statement_category = '';
        $search_by = array();

        if(isset($request['acc_no']) && $request['acc_no']!=""){
            $acc_no = $request['acc_no'];
            $search_by['Patient Acc No'][] = $request['acc_no'];
        }

        if(isset($request['patient_name']) && $request['patient_name']!=""){
            $patient_name = ucwords(preg_replace('/\s+/', ' ', (str_replace(",", " ",$request['patient_name'])) ));
            $search_by['Patient Name'][] = $request['patient_name'];
        }

        if(!empty($request['dob_search'])){
            $date = explode('-',$request['dob_search']);
            $dob_start_date = date("Y-m-d", strtotime($date[0]));
            if($dob_start_date == '1970-01-01'){
                $dob_start_date = '0000-00-00';
            }
            $dob_end_date = date("Y-m-d", strtotime($date[1]));

            $search_by['DOB'][]= date("m/d/Y",strtotime($dob_end_date)).' to '.date("m/d/Y",strtotime($dob_end_date));
        }

        if(isset($request['ssn']) && $request['ssn']!=""){
            $ssn = $request['ssn'];
            $search_by['Patient SSN'][] = $request['ssn'];
        }

        if(isset($request['statements']) && $request['statements']!="" && $request['statements']!="All"){
            $statements = $request['statements'];
            $search_by['Patient Statement'][] = $request['statements'];

            if($request['statements'] == "Hold") {
                // Handle hold reason and hold release date only when statement selected as Hold
                if(isset($request['hold_reason']) && $request['hold_reason']!=""){
                    $hold_reason = $request['hold_reason'];
                }

                if(!empty($request['hold_releasedate'])){
                    $date = explode('-',$request['hold_releasedate']);
                    $start_date = date("Y-m-d", strtotime($date[0]));
                    if($start_date == '1970-01-01'){
                        $start_date = '0000-00-00';
                    }
                    $end_date = date("Y-m-d", strtotime($date[1]));

                    $search_by['Statement Hold Release Date'][]= date("m/d/Y",strtotime($start_date)).' to '.date("m/d/Y",strtotime($end_date));
                }

            }
        }

        if(isset($request['statement_category']) && $request['statement_category']!=""){
            $statement_category = $request['statement_category'];
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
            $sp_return_result = DB::select('call patientStatementStatus("' . $acc_no . '", "' . $patient_name . '",  "' . $dob_start_date . '",  "' . $dob_end_date . '", "' . $ssn . '", "' . $statements . '",  "' . $hold_reason . '", "' . $start_date . '", "' . $end_date . '",  "' . $statement_category . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_count_return_result = (array) $sp_return_result;
            $count = $sp_count_return_result[0]->patient_count;
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
            $sp_return_result = DB::select('call patientStatementStatus("' . $acc_no . '", "' . $patient_name . '",  "' . $dob_start_date . '",  "' . $dob_end_date . '", "' . $ssn . '", "' . $statements . '",  "' . $hold_reason . '", "' . $start_date . '", "' . $end_date . '",  "' . $statement_category . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
            $sp_return_result = DB::select('call patientStatementStatus("' . $acc_no . '", "' . $patient_name . '",  "' . $dob_start_date . '",  "' . $dob_end_date . '", "' . $ssn . '", "' . $statements . '",  "' . $hold_reason . '", "' . $start_date . '", "' . $end_date . '",  "' . $statement_category . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $sp_return_result = (array) $sp_return_result;
        }
        $filter_result = $sp_return_result;

        return Response::json(array('status' => 'success', 'message' => null,
                    'data' => compact('start_date', 'end_date', 'filter_result', 'pagination','search_by')));
    }

    /* Patient statement status report end */

    //Stream download
    public function download()
    {
        try{
            $headers = [
                    'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
                ,   'Content-type'        => 'text/csv'
                ,   'Content-Disposition' => 'attachment; filename=Payment_Reports.csv'
                ,   'Expires'             => '0'
                ,   'Pragma'              => 'public'
            ];
            $callback = function() {
            $FH = fopen('php://output', 'w');
            $data = [];
            ini_set('memory_limit','2G');

            # add headers for each column in the CSV download
            $head = ['Transaction Date', 'Acc No', 'Patient Name', 'DOS', 'Claim No', 'Rendering Provider', 'Facility', 'Payer', 'Payment Date', 'Payment Type', 'Check/EFT/CC No', 'Check/EFT/CC Date', 'Billed', 'Allowed', 'W/O', 'Ded', 'Co-Pay', 'Co-Ins', 'Other Adjustment', 'Paid', 'Reference'];
            fputcsv($FH, $head);

            //Get Data
            $list = $this->getPaymentSearchApi($export = 'yes');
            $list = (array) $list->getData()->data->payments;

            //Add data each row in CSV download
            foreach($list as $payments_list){
                $patient = @$payments_list->claim_patient_det;
                $claim = @$payments_list->claim;
                $check_details = @$payments_list->pmt_info->check_details;
                $eft_details = @$payments_list->pmt_info->eft_details;
                $creditCardDetails = @$payments_list->pmt_info->credit_card_details;
                $set_title = ($patient->title)? @$patient->title.". ":'';
                $patient_name =     $set_title."". App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name);
                $data['Transaction Date'] = date('m/d/y',strtotime(@$payments_list->created_at));
                $data['Acc No'] = @$patient->account_no;
                $data['Patient Name'] = $patient_name;
                $data['DOS'] = App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate') ;
                $data['Claim No'] = @$claim->claim_number;
                $data['Rendering Provider'] = @$payments_list->claim->rendering_provider->short_name ;
                $data['Facility'] = @$payments_list->claim->facility_detail->short_name ;
                if($payments_list->payer_insurance_id==0)
                    $data['Payer'] = 'Self';
                else
                    $data['Payer'] = App\Http\Helpers\Helpers::getInsuranceName($payments_list->payer_insurance_id);
                $data['Payment Date'] = App\Http\Helpers\Helpers::dateFormat($payments_list->posting_date);
                $data['Payment Type'] = @$payments_list->pmt_info->pmt_mode;
                if($payments_list->pmt_info->pmt_mode =='Check'){
                    if(!empty($check_details->check_no) || $check_details->check_no==0)
                        $data['Check/EFT/CC No'] = 'Check No: '.@$check_details->check_no;
                } elseif($payments_list->pmt_info->pmt_mode =='EFT'){
                    if(!empty($eft_details->eft_no) || $eft_details->eft_no==0)
                        $data['Check/EFT/CC No'] = 'EFT No: '.@$eft_details->eft_no;
                } elseif($payments_list->pmt_info->pmt_mode =='Money Order'){
                    if(!empty($check_details->check_no) || $check_details->check_no==0){
                        $exp=explode("MO-", @$check_details->check_no);
                        $data['Check/EFT/CC No'] = 'MO No: '.$exp[1];
                    }
                } elseif($payments_list->pmt_info->pmt_mode =='Credit'){
                    if($creditCardDetails->card_last_4 != '')
                        $data['Check/EFT/CC No'] = 'Card No: '.@$creditCardDetails->card_last_4;
                } elseif($payments_list->pmt_info->pmt_mode =='Cash'){
                    $data['Check/EFT/CC No'] = '-Nil-';
                } else{
                    $data['Check/EFT/CC No'] = '-Nil-';
                }

                if($payments_list->pmt_info->pmt_mode =='Check'){
                    if(!empty($check_details->check_date))
                        $data['Check/EFT/CC Date'] = 'Check Date: '.@$check_details->check_date;
                } elseif($payments_list->pmt_info->pmt_mode =='EFT'){
                    if(!empty($eft_details->eft_date))
                        $data['Check/EFT/CC Date'] = 'EFT Date: '.@$eft_details->eft_date;
                } elseif($payments_list->pmt_info->pmt_mode =='Money Order'){
                    if(!empty($check_details->check_date))
                        $data['Check/EFT/CC Date'] = 'MO Date: '.@$check_details->check_date;
                } elseif($payments_list->pmt_info->pmt_mode =='Credit'){
                    if($creditCardDetails->created_at != '')
                        $data['Check/EFT/CC Date'] = 'Card Date: '.@$creditCardDetails->created_at;
                } elseif($payments_list->pmt_info->pmt_mode =='Cash'){
                    $data['Check/EFT/CC Date'] = '-Nil-';
                } else{
                    $data['Check/EFT/CC Date'] = '-Nil-';
                }
                $data['Billed'] = @$claim->total_charge;
                $data['Allowed'] = @$payments_list->total_allowed;
                $data['W/O'] = @$payments_list->total_writeoff;
                $data['Ded'] = @$payments_list->total_deduction;
                $data['Co-Pay'] = @$payments_list->total_copay;
                $data['Co-Ins'] = @$payments_list->total_coins;
                $data['Other Adjustment'] = @$payments_list->total_withheld;
                $data['Paid'] = App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid);
                if($payments_list->pmt_info->reference =='')
                    $data['Reference'] = '-Nil-';
                else
                    $data['Reference'] = @$payments_list->pmt_info->reference;
                $data['User'] = @$payments_list->pmt_info->created_user->short_name ;
                fputcsv($FH, $data);
            }
                fclose($FH);
            };

            return response()->stream($callback, 200, $headers);
        } catch(\Expection $e){
            \Log::info($e->getMessage());
        }

    }
}