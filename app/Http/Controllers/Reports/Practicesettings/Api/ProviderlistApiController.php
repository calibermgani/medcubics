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
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Users as User;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Medcubics\Users as Users;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderlistApiController extends Controller {

    public function getProviderlistApi($summary="") {
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        $ClaimController  = new ClaimControllerV1();  
        if($summary)
            $search_fields_data = $ClaimController->generateSearchPageLoad('provider_summary');
        else
            $search_fields_data = $ClaimController->generateSearchPageLoad('provider_list');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        //$user_list = User::select('name','id')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details','search_fields','searchUserData')));
    }

    public function getFilterResultApi($export = '') {
        $request = Request::All();
        if(isset($request['practiceoption']))
            $practiceopt = $request['practiceoption'];
        else
            $practiceopt ="";
        /*if ($request['hidden_from_date'] == '')
            $start_date = $request['from_date'];
        else
            $start_date = $request['hidden_from_date'];
        if ($request['hidden_to_date'] == '')
            $end_date = $request['to_date'];
        else
            $end_date = $request['hidden_to_date'];
        $provider_type_id = @$request['provider_type'];*/
        $get_provider = [];
        $billing_provider = [];
        $pagination = '';
        if ($practiceopt == "provider_list") {
            $get_provider_data = Provider::with(['provider_types', 'provider_user_details' => function($query) {
                            $query->select('*');
                        }])->whereIn('provider_types_id', [1, 5])->select('provider_name', 'provider_types_id', 'created_by', 'created_at');
            if ($request['transaction_date'] != '') {
                $exp = explode("-",$request['transaction_date']);
                $start_date = str_replace('"', '', $exp[0]);
                $end_date = str_replace('"', '', $exp[1]);
                $get_provider_data = $get_provider_data->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
            } else{
                $start_date = "";
                $end_date = "";
            }
            if ($export == "") {
                $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
                $get_provider = $get_provider_data->paginate($paginate_count);
                $provider_array = $get_provider->toArray();
                $pagination_prt = $get_provider->render();
                if ($pagination_prt == '')
                    $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
                $pagination = array('total' => $provider_array['total'], 'per_page' => $provider_array['per_page'], 'current_page' => $provider_array['current_page'], 'last_page' => $provider_array['last_page'], 'from' => $provider_array['from'], 'to' => $provider_array['to'], 'pagination_prt' => $pagination_prt);
                $providers_list = json_decode($get_provider->toJson());
                $get_provider = $providers_list->data;
            } else {
                $get_provider = $get_provider_data->get();
            }
        } else {

            DB::enableQueryLog();
             if ($request['provider_type'] ==1 || $request['provider_type'] ==0) {
                $get_provider_data = DB::table("claim_info_v1 AS c")->selectRaw('p.id,p.provider_name,pt.name, sum(pcf.total_charge) as total_charge, sum(pcf.patient_adj) as patient_adj, sum(pcf.insurance_adj) as insurance_adj, sum(pcf.withheld) as withheld, sum(pcf.patient_paid) as patient_paid, sum(pcf.insurance_paid) as insurance_paid, sum(pcf.patient_due) as patient_due, sum(pcf.insurance_due) as insurance_due,sum(patient_adj+insurance_adj + withheld) as total_adjusted, sum(patient_paid + insurance_paid) as total_paid, sum(patient_due) as patient_due, sum(insurance_due) as insurance_due,sum(insurance_due - patient_paid) as insurance_bal, sum(patient_due + insurance_due - patient_paid) as balance_amt,c.created_at')
                        ->join('providers AS p', function($join)
                         {
                             $join->on('p.id', '=', 'c.rendering_provider_id');
                         })
                        ->leftJoin('provider_types AS pt','pt.id','=','p.provider_types_id')
                        ->leftJoin('pmt_claim_fin_v1 AS pcf','pcf.claim_id','=','c.id');

                $rendering_unit = DB::table("claim_info_v1 AS c")->selectRaw('p.id,p.provider_name, pt.name,sum(cci.unit) as unit,c.created_at')->join('providers AS p','p.id','=','c.rendering_provider_id')->leftJoin('provider_types AS pt','pt.id','=','p.provider_types_id')->leftJoin('claim_cpt_info_v1 AS cci','cci.claim_id','=','c.id');
                if ($request['transaction_date'] != '') {
                    $exp = explode("-",$request['transaction_date']);
                    $start_date = str_replace('"', '', $exp[0]);
                    $end_date = str_replace('"', '', $exp[1]);
                    $get_provider_data = $get_provider_data->where(DB::raw('DATE(c.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(c.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                    $rendering_unit = $rendering_unit->where(DB::raw('DATE(c.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(c.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                } else{
                    $start_date = "";
                    $end_date = "";
                }

                if (isset($request['rendering_id'])) {
                    $get_provider_data = $get_provider_data->where('p.provider_types_id', '=', 1)
                                        ->whereIn('p.id',$request['rendering_id']);
                    $rendering_unit = $rendering_unit->whereIn('p.id',[$request['rendering_id']])->where('cci.deleted_at', NULL)->pluck('unit','provider_name')->all();
                } else {
                    $get_provider_data = $get_provider_data->whereIn('p.provider_types_id',[1,5]);
                    $rendering_unit = $rendering_unit->where('cci.deleted_at', NULL)->pluck('unit','provider_name')->all();
                }

                $get_provider_data = $get_provider_data->where('pcf.deleted_at', NULL);
                
                    
                $get_provider = $get_provider_data->get();
            }else{
                $get_provider = [];
                $rendering_unit = [];
            }
            if ($request['provider_type'] ==5 || $request['provider_type'] ==0) {
                $billing_provider = DB::table("claim_info_v1 AS c")->selectRaw('p.id,p.provider_name,pt.name, sum(pcf.total_charge) as total_charge, sum(pcf.patient_adj) as patient_adj, sum(pcf.insurance_adj) as insurance_adj, sum(pcf.withheld) as withheld, sum(pcf.patient_paid) as patient_paid, sum(pcf.insurance_paid) as insurance_paid, sum(pcf.patient_due) as patient_due, sum(pcf.insurance_due) as insurance_due,sum(patient_adj+insurance_adj + withheld) as total_adjusted, sum(patient_paid + insurance_paid) as total_paid, sum(patient_due) as patient_due, sum(insurance_due) as insurance_due,sum(insurance_due - patient_paid) as insurance_bal, sum(patient_due + insurance_due - patient_paid) as balance_amt,c.created_at')
                        ->join('providers AS p', function($join)
                         {
                             $join->on('p.id','=','c.billing_provider_id');
                         })
                        ->leftJoin('provider_types AS pt','pt.id','=','p.provider_types_id')
                        ->leftJoin('pmt_claim_fin_v1 AS pcf','pcf.claim_id','=','c.id');
                        
                $billing_unit = DB::table("claim_info_v1 AS c")->selectRaw('p.id,p.provider_name,pt.name,sum(cci.unit) as unit,c.created_at')->join('providers AS p','p.id','=','c.billing_provider_id')->leftJoin('provider_types AS pt','pt.id','=','p.provider_types_id')->leftJoin('claim_cpt_info_v1 AS cci','cci.claim_id','=','c.id');
                if ($request['transaction_date'] != '') {
                    $exp = explode("-",$request['transaction_date']);
                    $start_date = str_replace('"', '', $exp[0]);
                    $end_date = str_replace('"', '', $exp[1]);
                    $billing_provider = $billing_provider->where(DB::raw('DATE(c.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(c.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                    $billing_unit = $billing_unit->where(DB::raw('DATE(c.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(c.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                } else{
                    $start_date = "";
                    $end_date = "";
                }
                if (isset($request['billing_id'])) {
                    $billing_provider = $billing_provider->where('p.provider_types_id', '=', 5)
                                        ->whereIn('p.id',$request['billing_id']);
                    $billing_unit = $billing_unit->whereIn('p.id',[$request['billing_id']])->where('cci.deleted_at', NULL)->pluck('unit','provider_name')->all();
                } else {
                    $billing_provider = $billing_provider->whereIn('p.provider_types_id',[1,5]);
                    $billing_unit = $billing_unit->where('cci.deleted_at', NULL)->pluck('unit','provider_name')->all();
                }
                $billing_provider = $billing_provider->where('pcf.deleted_at', NULL);
                $billing_provider = $billing_provider->get();
                //dd($request['billing_id']);
            }else{
                $billing_provider = [];
                $billing_unit = [];
            }
            /*$get_provider_data = ClaimInfoV1::with('claim_unit_details', 'rendering_provider_claim')->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, rendering_provider_id')->groupBy('rendering_provider_id');*/
            /*$billing_provider = ClaimInfoV1::with('claim_unit_details', 'billing_provider')->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, billing_provider_id')->groupBy('billing_provider_id')->get();*/
            //dd(DB::getQueryLog());
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'get_provider', 'practiceopt', 'provider_type_id', 'billing_provider', 'rendering_unit', 'billing_unit', 'pagination')));
    }

    public function getProviderFilterResultApi($export = '', $data = '') {
        if(isset($data) && !empty($data)){
            $request = $data;
        }
        else{
            $request = Request::All();//dd($request);
        }
        if(isset($request['practiceoption'])){
            $practiceopt = $request['practiceoption'];
        }elseif (isset($request['report_name'])) {
            $practiceopt = str_replace('-', '_', $request['report_name']);
        }
        else{
            $practiceopt ="";
        }
        $get_list_header =[];
        $start_date = '';
        $end_date ='';
        $get_provider = [];
        $billing_provider = [];
        $pagination = '';
        if ($practiceopt == "provider_list") {
            $get_provider_data = Provider::with(['provider_types', 'provider_user_details' => function($query) {
                            $query->select('*');
                        }])->whereIn('provider_types_id', [1, 5])->select('provider_name', 'provider_types_id', 'created_by', 'created_at');
            if(isset($request['transaction_date'])){
                if ($request['transaction_date'] != '') {
                    $exp = explode("-",$request['transaction_date']);
                    $start_date = str_replace('"', '', $exp[0]);
                    $end_date = str_replace('"', '', $exp[1]);
                    $get_provider_data = $get_provider_data->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
                } else{
                    $start_date = "";
                    $end_date = "";
                }
            }
            
            if (isset($request['user']) && $request['user'] != '') {
                $get_provider_data->whereIn('created_by', explode(',', $request['user'])); 
                $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('name', 'id')->all();          
                $User_name = implode(", ", array_unique($User_name));
                $get_list_header['User'] = $User_name;
            } 
            
            // if ($export == "") {
                $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
                $get_provider = $get_provider_data->paginate($paginate_count);
                $provider_array = $get_provider->toArray();
                $pagination_prt = $get_provider->render();
                if ($pagination_prt == '')
                    $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
                $pagination = array('total' => $provider_array['total'], 'per_page' => $provider_array['per_page'], 'current_page' => $provider_array['current_page'], 'last_page' => $provider_array['last_page'], 'from' => $provider_array['from'], 'to' => $provider_array['to'], 'pagination_prt' => $pagination_prt);
                $providers_list = json_decode($get_provider->toJson());
                $get_provider = $providers_list->data;
            // } else {
            //     $get_provider = $get_provider_data->get();
            // }

        } else {
            // Total provider_list 
            $provider_list = Provider::selectRaw('providers.id,providers.provider_name, providers.short_name, providers.created_at')->wherenull('providers.deleted_at');
            
            // Total charges
            $charges = ClaimInfoV1::selectRaw('claim_info_v1.id, sum(claim_info_v1.total_charge) as total_charge, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('p.deleted_at');

            // Total Patient Adjustments
            $pat_adj = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_writeoff) as pat_adj, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        //->join('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->where('pmt_claim_tx_v1.pmt_method','Patient')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_tx_v1.deleted_at')
                        ->wherenull('p.deleted_at')
                        ->whereRaw("(pmt_claim_tx_v1.total_writeoff <> '0' or pmt_claim_tx_v1.total_withheld <> '0')");

            // Total Insurance Adjustments
            $ins_adj = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_withheld) as ins_adj, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        //->join('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->where('pmt_claim_tx_v1.pmt_method','Insurance')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_tx_v1.deleted_at')
                        ->wherenull('p.deleted_at');

            // Total Writeoff
            $writeoff = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_writeoff) as total_writeoff, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        //->join('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->where('pmt_claim_tx_v1.pmt_method','Insurance')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_tx_v1.deleted_at')
                        ->wherenull('p.deleted_at');
            
            // Total Patient Payments
            /*
            $patient = DB::table('pmt_info_v1')
                        ->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.payment_id','=','pmt_info_v1.id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->where('pmt_info_v1.pmt_method','Patient');
                        
            if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                $patient = $patient->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance','Refund'])
                            ->selectRaw('p.provider_name,p.id as provider_id, pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method, ((sum(IF(pmt_info_v1.pmt_method = "Patient" AND (pmt_info_v1.pmt_type="Payment" OR pmt_info_v1.pmt_type="Credit Balance" ), pmt_info_v1.amt_used, 0))) - (sum(IF(pmt_info_v1.pmt_method = "Patient" AND (pmt_info_v1.pmt_type="Refund") , pmt_info_v1.amt_used, 0)))) as total_paid');
            } else {
                $patient = $patient->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance'])
                            ->selectRaw('pmt_info_v1.amt_used as total_paid,p.provider_name,p.id as provider_id, pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method');
            }
            */
            $patient = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_paid) as total_paid, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        //->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_tx_v1.deleted_at')
                        ->where('pmt_claim_tx_v1.pmt_method','Patient')
                        ->wherenull('p.deleted_at');
            
            if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                $patient = $patient->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
            } else {
                $patient = $patient->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
            }
            
            // Total Insurance Payments
            $insurance = PMTClaimTXV1::selectRaw('claim_info_v1.id, sum(pmt_claim_tx_v1.total_paid) as total_paid, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        //->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                        ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_tx_v1.deleted_at')
                        ->where('pmt_claim_tx_v1.pmt_method','Insurance')
                        ->wherenull('p.deleted_at');
            
            if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
                $get_list_header['Include Refund'] = 'Yes'; 
            } else {
                $insurance = $insurance->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
                $get_list_header['Include Refund'] = 'No'; 
            }

            // Total Patient Balance
            $patient_bal = ClaimInfoV1::selectRaw('claim_info_v1.id, SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj) as patient_due, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        ->leftJoin('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_fin_v1.deleted_at')
                        ->where('claim_info_v1.insurance_id',0)
                        ->wherenull('p.deleted_at');

            // Total Insurance Balance
            $insurance_bal = ClaimInfoV1::selectRaw('claim_info_v1.id,SUM(claim_info_v1.total_charge) - SUM(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.withheld+pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_adj)  as insurance_bal, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        ->leftJoin('pmt_claim_fin_v1','pmt_claim_fin_v1.claim_id','=','claim_info_v1.id')
                        ->wherenull('claim_info_v1.deleted_at')
                        ->wherenull('pmt_claim_fin_v1.deleted_at')
                        ->wherenull('p.deleted_at')->where('claim_info_v1.insurance_id', '!=', 0);

            // Total Units
            $units = ClaimInfoV1::selectRaw('claim_info_v1.id, p.provider_name,sum(claim_cpt_info_v1.unit) as unit, p.provider_name, p.id as provider_id, claim_info_v1.billing_provider_id, claim_info_v1.rendering_provider_id, claim_info_v1.created_at')
                        ->leftJoin('claim_cpt_info_v1','claim_cpt_info_v1.claim_id','=','claim_info_v1.id')
                        ->wherenull('p.deleted_at');
            
            if(isset($request['provider_type'])) {
                if($request['provider_type']==1){
                    $get_list_header["Provider Type"] = 'Rendering';
                    $charges->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $writeoff->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $pat_adj->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $ins_adj->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0);
                    $patient->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('p.provider_types_id', 1);
                    $insurance->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $patient_bal->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $insurance_bal->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    $units->leftJoin("providers AS p",'p.id','=','claim_info_v1.rendering_provider_id')->where('claim_info_v1.rendering_provider_id','<>',0)->where('p.provider_types_id', 1);
                    if ($request['provider_type']==1 && isset($request['rendering_id'])) {
                        // For export data come as comma separated string have to convert it to array
                        // Rev.1 03-Aug-2019 Ravi
                        $rendering_ids = (isset($request['export']) || is_string($request['rendering_id'])) ? explode(',', $request['rendering_id']) : $request['rendering_id'];
                        $provider_list->whereIn('id', $rendering_ids);
                        $charges->whereIn('p.id',$rendering_ids);
                        $writeoff->whereIn('p.id',$rendering_ids);
                        $pat_adj->whereIn('p.id',$rendering_ids);
                        $ins_adj->whereIn('p.id', $rendering_ids);
                        $patient->whereIn('p.id', $rendering_ids);
                        $insurance->whereIn('p.id', $rendering_ids);
                        $patient_bal->whereIn('p.id', $rendering_ids);
                        $insurance_bal->whereIn('p.id', $rendering_ids);
                        $units->whereIn('p.id', $rendering_ids);
                        $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $rendering_ids)->get()->toArray();
                        $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
                    } else {
                        $get_list_header["Rendering Provider"] =  'All';
                    }
                } else {
                    $get_list_header["Provider Type"] = 'Billing';
                    $charges->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $writeoff->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $pat_adj->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $ins_adj->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $patient->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('p.provider_types_id', 5);
                    $insurance->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $patient_bal->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $insurance_bal->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('p.provider_types_id', 5);
                    $units->leftJoin("providers AS p",'p.id','=','claim_info_v1.billing_provider_id')->where('claim_info_v1.billing_provider_id','<>',0)->where('provider_types_id', 5);
                    if ($request['provider_type']==5 && isset($request['billing_id'])) {
                        // For export data come as comma separated string have to convert it to array
                        // Rev.1 03-Aug-2019 Ravi
                        $billing_ids = (isset($request['export']) || is_string($request['billing_id'])) ? explode(',', $request['billing_id']) : $request['billing_id'];
                        $provider_list->whereIn('id', $billing_ids);
                        $charges->whereIn('p.id', $billing_ids);
                        $writeoff->whereIn('p.id', $billing_ids);
                        $pat_adj->whereIn('p.id', $billing_ids);
                        $ins_adj->whereIn('p.id', $billing_ids);
                        $patient->whereIn('p.id', $billing_ids);
                        $insurance->whereIn('p.id', $billing_ids);
                        $patient_bal->whereIn('p.id', $billing_ids);
                        $insurance_bal->whereIn('p.id', $billing_ids);
                        $units->whereIn('p.id', $billing_ids);
                        $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $billing_ids)->get()->toArray();
                        $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
                    }else{
                        $get_list_header["Billing Provider"] =  'All';
                    }
                }
            }
            
            $practice_timezone = Helpers::getPracticeTimeZone();
            if (isset($request['transaction_date']) && $request['transaction_date'] != '') {
                $exp = explode("-",$request['transaction_date']);
                $start_date = str_replace('"', '', $exp[0]);
                $end_date = str_replace('"', '', $exp[1]);
                $from_date = date('Y-m-d', strtotime($start_date));
                $to_date = date('Y-m-d', strtotime($end_date));
                /*$from_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
                $to_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);*/
                $charges->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $writeoff->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $pat_adj->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $ins_adj->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $patient->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to_date."'");
                $insurance->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to_date."'");
                $patient_bal->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $insurance_bal->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $units->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$from_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$to_date'");
                $get_list_header['Transaction Date'] = date("m/d/y", strtotime($start_date)) . ' to ' . date("m/d/y", strtotime($end_date)); 
            } else{
                $start_date = "";
                $end_date = "";
            }
            
            // For forming array proviedr id used instead of provider name
            //  Rev. I - Ref. MR-2784 - Ravi - 03-09-2019
            if(isset($request['provider_type'])) {
                if($request['provider_type']==1){
                    $charges = $charges->groupBy("claim_info_v1.rendering_provider_id")->pluck('total_charge','p.provider_id')->all();
                    $writeoff = $writeoff->groupBy("claim_info_v1.rendering_provider_id")->pluck('total_writeoff','p.provider_id')->all();
                    $pat_adj = $pat_adj->groupBy("claim_info_v1.rendering_provider_id")->pluck('pat_adj','p.provider_id')->all();
                    $ins_adj = $ins_adj->groupBy("claim_info_v1.rendering_provider_id")->pluck('ins_adj','p.provider_id')->all();
                    $patients = $patient->groupBy("claim_info_v1.rendering_provider_id")->get();
                    //->whereNull('pmt_info_v1.void_check')
                    $insurance = $insurance->groupBy("claim_info_v1.rendering_provider_id")->pluck('total_paid','p.provider_id')->all();
                    $patient_bal = $patient_bal->groupBy("claim_info_v1.rendering_provider_id")->pluck('patient_due','p.provider_id')->all();
                    $insurance_bal = $insurance_bal->groupBy("claim_info_v1.rendering_provider_id")->pluck('insurance_bal','p.provider_id')->all();
                    $units = $units->groupBy("claim_info_v1.rendering_provider_id")->pluck('unit','p.provider_id')->all();
                    $provider_list = $provider_list->where('provider_types_id', 1);
                }else{
                    $charges = $charges->groupBy("claim_info_v1.billing_provider_id")->pluck('total_charge','p.provider_id')->all();
                    $writeoff = $writeoff->groupBy("claim_info_v1.billing_provider_id")->pluck('total_writeoff','p.provider_id')->all();
                    $pat_adj = $pat_adj->groupBy("claim_info_v1.billing_provider_id")->pluck('pat_adj','p.provider_id')->all();
                    $ins_adj = $ins_adj->groupBy("claim_info_v1.billing_provider_id")->pluck('ins_adj','p.provider_id')->all();
                    $patients = $patient->groupBy("claim_info_v1.billing_provider_id")->get();
                    //->whereNull('pmt_info_v1.void_check')
                    $insurance = $insurance->groupBy("claim_info_v1.billing_provider_id")->pluck('total_paid','p.provider_id')->all();
                    $patient_bal = $patient_bal->groupBy("claim_info_v1.billing_provider_id")->pluck('patient_due','p.provider_id')->all();
                    $insurance_bal = $insurance_bal->groupBy("claim_info_v1.billing_provider_id")->pluck('insurance_bal','p.provider_id')->all();
                    $units = $units->groupBy("claim_info_v1.billing_provider_id")->pluck('unit','p.provider_id')->all();
                    $provider_list = $provider_list->where('provider_types_id', 5);
                }
            }

            $units = array_except($units, ['']);
            $patient_bal = array_except($patient_bal, ['']);
            $insurance_bal = array_except($insurance_bal, ['']);
            $patient = [];
            // Payments Separate into provider wise for patient only
            if($patients) {
                foreach($patients as $pmt){
                    if(isset($pmt->provider_name)){
                        //$provider_name = str_replace(',','',$pmt->provider_name);
                        //$provider_name = str_replace(' ','_',$provider_name);
                    }
                    if(isset($pmt->provider_name)){
                        $patient[$pmt->provider_id][] = $pmt->total_paid;
                    } else {
                            //$patient['wallet'][] = $pmt->total_paid;
                    }
                }
            }
            
            // Payments wallet only - total payment amount minus amount used calculation used. 
            $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt');
            /* Wallet amount till now not consider the transaction date
            if (isset($request['transaction_date']) && $request['transaction_date'] != '') {
                $wallet->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$from_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$to_date."'");
            }
            */
            $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet[0]->pmt_amt!=null)
                $patient['wallet'] = $wallet[0]->pmt_amt;
            
            if($patient){
                foreach ($patient as $key=>$item) {
                    if($key=='wallet')
                        $patient_payments[$key] = $item;
                    else
                        $patient_payments[$key] = array_sum($item);
                }
            } else {
                $patient_payments = [];
            }
            $patient = $patient_payments;
            if (!isset($request['export'])) {
                $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
                $provider_list = $provider_list->paginate($paginate_count);
                $providers = $provider_list->toArray();
                $pagination_prt = $provider_list->render();
                if ($pagination_prt == '')
                    $pagination_prt = '<ul class="pagination">
                <li class="disabled"><span>&laquo;</span></li> 
                <li class="active"><span>1</span></li>
                <li><a class="disabled" rel="next">&raquo;</a></li></ul>';
                $pagination = array('total' => $providers['total'], 'per_page' => $providers['per_page'], 'current_page' => $providers['current_page'], 'last_page' => $providers['last_page'], 'from' => $providers['from'], 'to' => $providers['to'], 'pagination_prt' => $pagination_prt);
                $claims_list = json_decode($provider_list->toJson());
                $providers = $claims_list->data;
            }else {
                $providers = $provider_list->get()->toArray();
                $pagination = '';
            }
        }
        $header = $get_list_header;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'providers', 'practiceopt', 'pagination','header', 'charges', 'writeoff', 'pat_adj', 'ins_adj', 'patient', 'insurance', 'patient_bal', 'insurance_bal', 'units')));     
    }
    
    //** Stored procedure for Provider Summary - Anjukaselvan **//
    public function getProviderFilterResultApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();//dd($request);
        if(isset($request['practiceoption']))
            $practiceopt = $request['practiceoption'];
        else
            $practiceopt ="";        
            
        $get_list_header =[];
        $get_provider = [];
        $billing_provider = [];
        $pagination = '';
        $charges = '';
        $start_date = $end_date = $user_ids = $provider_type = $rendering_id = $billing_id =  '';
        
        if ($practiceopt == "provider_list") {
            $get_provider = '';
        } else {
            if(isset($request['provider_type']))
            if($request['provider_type']==1){
                $get_list_header["Provider Type"] = 'Rendering';
                $provider_type = $request['provider_type'];
                if ($export == "") {
                    if ($request['provider_type']==1 && isset($request['rendering_id'])) {
                        $rendering_id = implode(", ", $request['rendering_id']);
                        $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $request['rendering_id'])->get()->toArray();
                        $get_list_header["Rendering Provider"] = @array_flatten($provider)[0];
                    }else{
                        $get_list_header["Rendering Provider"] =  'All';
                    }
                }else if($request['provider_type']==1 && isset($request['rendering_id[]']) && $request['rendering_id[]'] != ''){
                    $rendering_id = $request['rendering_id[]'];
                }
            } else {
                $get_list_header["Provider Type"] = 'Billing';
                $provider_type = $request['provider_type'];
                if ($export == "") {
                    if ($request['provider_type']==5 && isset($request['billing_id'])) {
                        $billing_id = implode(", ", $request['billing_id']);
                        $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ' | ') as provider_name")->whereIn('id', $request['billing_id'])->get()->toArray();
                        $get_list_header["Billing Provider"] =  @array_flatten($provider)[0];
                    }else{
                        $get_list_header["Billing Provider"] =  'All';
                    }
                }else if($request['provider_type']==5 && isset($request['billing_id[]']) && $request['billing_id[]'] != ''){
                    $billing_id = $request['billing_id[]'];
                }
            }
            if ($request['transaction_date'] != '') {
                $exp = explode("-",$request['transaction_date']);
                $start = str_replace('"', '', $exp[0]);
                $end = str_replace('"', '', $exp[1]);
                $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start);
                $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end);
                $get_list_header['Transaction Date'] = date("m/d/y", strtotime($start)) . ' to ' . date("m/d/y", strtotime($end)); 
            } else{
                $start_date = "";
                $end_date = "";
            }
            
            if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                $get_list_header['Include Refund'] = 'Yes'; 
            } else {
                $get_list_header['Include Refund'] = 'No'; 
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
                $sp_count_result = DB::select('call providerSummary("' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
                $sp_count_return_result = (array) $sp_count_result;
                $count = $sp_count_return_result[0]->provider_count;
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
                $sp_return_result = DB::select('call providerSummary("' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
                
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
                $sp_return_result = DB::select('call providerSummary("' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
                $pagination = '';
            }
            // Total provider_list 
            $providers = $sp_return_result;
            // Total charges
            $charges_list = DB::select('call providerSummaryCharges("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $charges_result = json_decode(json_encode($charges_list), true);
            $charges = array_combine(array_column($charges_result, 'provider_id'), array_column($charges_result, 'total_charge'));
            
            // Total Patient Adjustments
            $pat_adj_list = DB::select('call providerSummaryPatAdj("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $pat_adj_result = json_decode(json_encode($pat_adj_list), true);
            $pat_adj = array_combine(array_column($pat_adj_result, 'provider_id'), array_column($pat_adj_result, 'pat_adj'));
            
            // Total Insurance Adjustments
            $ins_adj_list = DB::select('call providerSummaryInsAdj("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $ins_adj_result = json_decode(json_encode($ins_adj_list), true);
            $ins_adj = array_combine(array_column($ins_adj_result, 'provider_id'), array_column($ins_adj_result, 'ins_adj'));
            
            // Total Writeoff
            $writeoff_list = DB::select('call providerSummaryWriteoff("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $writeoff_result = json_decode(json_encode($writeoff_list), true);
            $writeoff = array_combine(array_column($writeoff_result, 'provider_id'), array_column($writeoff_result, 'total_writeoff'));
            
            // Total Patient Payments
            $patients = DB::select('call providerSummaryPatient("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            
            // Total Insurance Payments
            $insurance_list = DB::select('call providerSummaryInsurance("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $insurance_result = json_decode(json_encode($insurance_list), true);
            $insurance = array_combine(array_column($insurance_result, 'provider_id'), array_column($insurance_result, 'total_paid'));
            
            // Total Patient Balance
            $patient_bal_list = DB::select('call providerSummaryPatientBal("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $patient_bal_result = json_decode(json_encode($patient_bal_list), true);
            $patient_bal = array_combine(array_column($patient_bal_result, 'provider_id'), array_column($patient_bal_result, 'patient_due'));
            
            // Total Insurance Balance
            $insurance_bal_list = DB::select('call providerSummaryInsuranceBal("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $insurance_bal_result = json_decode(json_encode($insurance_bal_list), true);
            $insurance_bal = array_combine(array_column($insurance_bal_result, 'provider_id'), array_column($insurance_bal_result, 'insurance_bal'));
            
            // Total Units
            $units_list = DB::select('call providerSummaryUnits("' . $start_date . '", "' . $end_date . '","' . $provider_type . '", "' . $rendering_id . '",  "' . $billing_id . '")');
            $units_result = json_decode(json_encode($units_list), true);
            $units = array_combine(array_column($units_result, 'provider_id'), array_column($units_result, 'unit'));
            
            //
            $units = array_except($units, ['']);
            $patient_bal = array_except($patient_bal, ['']);
            $insurance_bal = array_except($insurance_bal, ['']);
            $patient = [];
            // Payments Separate into provider wise for patient only
            if($patients) {
                foreach($patients as $pmt){
                    if(isset($pmt->provider_name)){
                        $patient[$pmt->provider_id][] = $pmt->total_paid;
                    } else {
                        //$patient['wallet'][] = $pmt->total_paid;
                    }
                }
            }    
            // Payments wallet only
            $wallet = DB::select('call providerSummaryWallet("' . $start_date . '", "' . $end_date . '")');//dd($wallet);
            if($wallet[0]->pmt_amt!=null)
                $patient['wallet'] = $wallet[0]->pmt_amt;
            
            if($patient){
                foreach ($patient as $key=>$item) {
                    if($key=='wallet')
                        $patient_payments[$key] = $item;
                    else
                        $patient_payments[$key] = array_sum($item);
                }
            }
            else
                $patient_payments = [];
            $patient = $patient_payments;
        }
        $header = $get_list_header;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('start_date', 'end_date', 'providers', 'practiceopt', 'pagination','header', 'charges', 'writeoff', 'pat_adj', 'ins_adj', 'patient', 'insurance', 'patient_bal', 'insurance_bal', 'units')));     
    }
    
    //  ****    Provider List  ****//
    public function getProvidersettingsApi() {
        $cliam_date_details = ClaimInfoV1::select(DB::raw('YEAR(date_of_service) as year'))->distinct()->get();
        $billing_provider_list = Provider::select('provider_name', 'id')->get();

        $user_list = User::select('name', 'id')->get();

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cliam_date_details', 'billing_provider_list', 'user_list')));
    }

    public function getProviderListExportApi($export = '') {
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

        $provider_type_id = @$request['provider_type'];
        //dd($provider_type_id);
        // To Provider

        $get_provider = [];
        $billing_provider = [];

        if ($practiceopt == "provider_list") {

            $get_provider = Provider::with(['provider_types', 'provider_user_details' => function($query) {
                            $query->select('*');
                        }])->whereIn('provider_types_id', [1, 5])->select('provider_name', 'provider_types_id', 'created_by', 'created_at')->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->get();
        } else {

            $get_provider = ClaimInfoV1::with('claim_unit_details', 'rendering_provider_claim')->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, rendering_provider_id')->groupBy('rendering_provider_id')->get();

            $billing_provider = ClaimInfoV1::with('claim_unit_details', 'billing_provider')->where('deleted_at', NULL)->where(DB::raw('DATE(created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(created_at)'), '<=', date("Y-m-d", strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, billing_provider_id')->groupBy('billing_provider_id')->get();
        }

        $total_cpt_count = 0;
        @$count = 0;
        @$total_adj = 0;
        @$patient_total = 0;
        @$insurance_total = 0;
        @$get_list = array();

        if ($practiceopt == "provider_list") {
            foreach ($get_provider as $list) {

                $data['provider_name'] = @$list->provider_name;
                $data['type'] = @$list->provider_types->name;

                $data['created_at'] = date('m/d/Y', strtotime(@$list->created_at));
                $data['user'] = @$list->provider_user_details->short_name;

                $get_list[$total_cpt_count] = $data;
                $total_cpt_count++;
            }

            $get_export_result = $get_list;

            $get_export_result[$total_cpt_count] = ['provider_name' => '', 'type' => '', 'created_at' => '', 'user' => ''];

            $result["value"] = json_decode(json_encode($get_export_result));
            $result["exportparam"] = array(
                'filename' => 'Provider Summary',
                'heading' => '',
                'fields' => array(
                    'provider_name' => 'Provider Name',
                    'type' => 'Type',
                    'created_at' => 'Created On',
                    'user' => 'User'
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
        } else {
            if ($provider_type_id == '1' || $provider_type_id == 'all') {
                foreach ($get_provider as $list) {
                    //dd($export_insuran_result);
                    $data['ins_name'] = @$list->rendering_provider_claim->provider_name;
                    $data['ins_type'] = "Rendering Provider";
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
            }
            if ($provider_type_id == '5' || $provider_type_id == 'all') {
                foreach ($billing_provider as $list) {
                    $data['provider_name'] = @$list->billing_provider->provider_name;
                    $data['provider_type'] = "Billing Provider";
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
            }

            $get_export_result = $get_list;
            $get_export_result[$total_cpt_count] = ['provider_name' => '', 'provider_type' => '', 'units' => '', 'charges' => '', 'adjs' => Helpers::priceFormat($total_adj), 'payments' => '', 'pat_balance' => Helpers::priceFormat($patient_total), 'ins_balance' => Helpers::priceFormat($insurance_total), 'total_balance' => ''];

            $result["value"] = json_decode(json_encode($get_export_result));
            $result["exportparam"] = array(
                'filename' => 'Provider Summary',
                'heading' => '',
                'fields' => array(
                    'provider_name' => 'Provider Name',
                    'provider_type' => 'Type',
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
      $provider_type_id=$request['provider_type'];

      $rendering_provider_claim = Claims::with('claim_unit_details','rendering_provider_claim')->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, rendering_provider_id')->groupBy('rendering_provider_id')->get();
      $billing_provider = Claims::with('claim_unit_details','billing_provider')->where('deleted_at',NULL)->where('created_at','>=',date("Y-m-d",strtotime($start_date)))->where('created_at','<=',date("Y-m-d",strtotime($end_date)))->selectRaw('id,sum(total_charge) as total_charge,sum(total_adjusted) as total_adjusted,sum(patient_paid) as patient_paid,sum(patient_due) as patient_due,sum(insurance_due) as insurance_due,sum(balance_amt) as balance_amt,created_at, billing_provider_id')->groupBy('billing_provider_id')->get();

      return Response::json(array('status' => 'success', 'message' => null,'data' =>compact('start_date','end_date','rendering_provider_claim','billing_provider','provider_type_id')));

      } */

    /* public function getproviderlistExportApi($export=''){
      $request = Input::get();

      if($request['start-date'] != '')
      $start_date = $request['start-date'];

      if($request['end-date'] != '')
      $end_date = $request['end-date'];
      $data = array();
      foreach($data as $sublist){
      //dd($sublist);
      }
      echo "fff";exit;
      } */
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }
    //stored procedure for provider list
    /*if ($practiceopt == "provider_list") {            
            if(isset($request['transaction_date']))
            if ($request['transaction_date'] != '') {
                $exp = explode("-",$request['transaction_date']);
                $start_date = str_replace('"', '', $exp[0]);
                $end_date = str_replace('"', '', $exp[1]);
            } else{
                $start_date = "";
                $end_date = "";
            }
            if (isset($request['user']) && $request['user'] != '') {
                $user_ids = $request['user'];
                $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('name', 'id')->all();
                $User_name = implode(", ", array_unique($User_name));
                $get_list_header['User'] = $User_name;
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
                $sp_count_result = DB::select('call providerList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
                $sp_count_return_result = (array) $sp_count_result;
                $count = $sp_count_return_result[0]->provider_count;
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
                $sp_return_result = DB::select('call providerList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
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
                $get_provider = $sp_return_result;
            } else {
                $recCount = 0;
                $paginate_count = 0;
                $offset = 0;
                $sp_return_result = DB::select('call providerList("' . $start_date . '", "' . $end_date . '",  "' . $user_ids . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
                $sp_return_result = (array) $sp_return_result;
                $get_provider = $sp_return_result;
            }
             }else{
             provider summary result } */
}