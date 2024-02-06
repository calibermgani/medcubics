<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Patients\Patient as Patient;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as Users;
use App\Models\Patients\ProblemList as ProblemList;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use Lang;

class ProblemListApiController extends Controller {

    public function getIndexApi($patient_id, $export = '') {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if ($patient_id != '' && Patient::where('id', $patient_id)->count()) {
            $last_addin_problemlist = [];

            $problem_list = ProblemList::has('claim')->with('claim', 'claim.facility_detail', 'claim.insurance_details', 'claim.rendering_provider', 'claim.rendering_provider.degrees', 'patient', 'user', 'created_by')->where('patient_id', $patient_id);
            $request = Request::all();

            if (isset($request) && count($request) > 0 && (Request::ajax() || (isset($request['export']) && $request['export'] == 'yes')) ) {
                if (isset($request['claim_number']) && $request['claim_number'] != "") {
                    $problem_list->where('claim_id', $request['claim_number']);
                }
                if (isset($request['billing_provider_id']) && $request['billing_provider_id'] != "") {
                    $billing_provider_id = $request['billing_provider_id'];
                    $problem_list->whereHas('claim', function($q) use ($billing_provider_id) {
                        $q->where('billing_provider_id', $billing_provider_id);
                    });
                }
                if (isset($request['rendering_provider_id']) && $request['rendering_provider_id'] != "") {
                    $rendering_provider_id = $request['rendering_provider_id'];
                    $problem_list->whereHas('claim', function($q) use ($rendering_provider_id) {
                        $q->where('rendering_provider_id', $rendering_provider_id);
                    });
                }
                if (isset($request['referring_provider_id']) && $request['referring_provider_id'] != "") {
                    $refering_provider_id = $request['referring_provider_id'];
                    $problem_list->whereHas('claim', function($q) use ($refering_provider_id) {
                        $q->where('refering_provider_id', $refering_provider_id);
                    });
                }
                if (isset($request['insurance_id']) && $request['insurance_id'] != "") {
                    $insurance_id = $request['insurance_id'];
                    $problem_list->whereHas('claim', function($q) use ($insurance_id) {
                        $q->where('insurance_id', $insurance_id);
                    });
                }
                if (isset($request['facility_id']) && $request['facility_id'] != "") {
                    $facility_id = $request['facility_id'];
                    $problem_list->whereHas('claim', function($q) use ($facility_id) {
                        $q->where('facility_id', $facility_id);
                    });
                }
                if (isset($request['follow_from']) && $request['follow_from'] != "" && $request['follow_to'] != "") {
                    $from_date = date('Y-m-d', strtotime($request['follow_from']));
                    $to_date = date('Y-m-d', strtotime($request['follow_to']));
                    $problem_list->whereRaw("DATE(fllowup_date) >= '$from_date' and DATE(fllowup_date) <= '$to_date'");
                }

                if (isset($request['description']) && $request['description'] != "") {
                    $problem_list->where('problem_lists.description','like','%' .$request['description'].'%');
                }

                if (isset($request['billed']) && $request['billed'] != "") {
                    $billed = $request['billed'];
                    if ($request['billed_option'] == "greaterthan")
                        $problem_list->whereHas('claim', function($q) use ($billed) {
                            $q->where('total_allowed', ' =', $billed);
                        });
                    elseif ($request['billed_option'] == "greaterthan")
                        $problem_list->whereHas('claim', function($q) use ($billed) {
                            $q->where('total_allowed', ' =', $billed);
                        });
                    elseif ($request['billed_option'] == "lessthan")
                        $problem_list->whereHas('claim', function($q) use ($billed) {
                            $q->where('total_allowed', ' =', $billed);
                        });
                    elseif ($request['billed_option'] == "lessthan")
                        $problem_list->whereHas('claim', function($q) use ($billed) {
                            $q->where('total_allowed', ' =', $billed);
                        });
                    elseif ($request['billed_option'] == "lessequal")
                        $problem_list->whereHas('claim', function($q) use ($billed) {
                            $q->where('total_allowed', ' =', $billed);
                        });
                }
            }
            $problem_list = $problem_list->orderBy('id', 'desc')->get();
            $paymentV1ApiController = new PaymentV1ApiController();
            foreach ($problem_list as $problem_list) {
                if ($problem_list->claim_id != null && $problem_list->claim_id != '') {
                    $last_prob_list_arr = ProblemList::with('claim', 'claim.facility_detail', 'claim.insurance_details', 'claim.rendering_provider', 'claim.rendering_provider.degrees', 'patient', 'user', 'created_by')->where('patient_id', $patient_id)->where('claim_id', $problem_list->claim_id)->orderBy('id', 'desc')->first();
                    $last_prob_list_arr->claim->date_of_service = Helpers::dateFormat($last_prob_list_arr->claim->date_of_service, 'claimdate');
                    $resultData = $paymentV1ApiController->getClaimsFinDetails($problem_list->claim_id, $problem_list->total_charge);
                    $last_prob_list_arr->claim->total_paid  = $resultData['total_paid']; 
                    $last_prob_list_arr->claim->balance_amt = $resultData['balance_amt'];
                    $last_addin_problemlist[$problem_list->claim_id] = $last_prob_list_arr;
                }
            }
            if ($export != "") {
                $exportparam = array(
                    'filename' => 'Patient_problem_lists',
                    'heading' => 'Patient ProblemList',
                    'fields' => array(
                        'dos' => array('table' => 'claim', 'column' => 'date_of_service', 'label' => 'DOS'),
                        'claim_number' => array('table' => 'claim', 'column' => 'claim_number', 'label' => 'Claim No'),
                        'Provider' => array('table' => 'claim.rendering_provider', 'column' => 'id', 'use_function' => ['App\Models\Provider', 'getProviderShortName'], 'label' => 'Provider'),
                        'Facility ' => array('table' => 'claim.facility_detail', 'column' => 'short_name', 'label' => 'Facility'),
                        'Billed To' => array('table' => 'claim', 'column' => 'insurance_id', 'use_function' => ['App\Models\Insurance', 'InsuranceName'], 'label' => 'Billed To'),
                        'total_charge' => array('table' => 'claim', 'column' => 'total_charge', 'label' => 'Billed Amt'),
                        'total_paid ' => array('table' => 'claim', 'column' => 'total_paid', 'label' => 'Paid'),
                        'ar due' => array('table' => 'claim', 'column' => 'balance_amt', 'label' => 'AR Due'),
                        'status' => 'Status',
                        'fllowup_date' => 'Followup Date',
                        'assign_user_id' => array('table' => 'user', 'column' => 'short_name', 'label' => 'Assign To'),
                        'priority' => 'Priority'
                    )
                );
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $last_addin_problemlist, $export);
            }
            $practice = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->where('deleted_at', Null)->pluck('name')->all();
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');

            $ClaimController  = new ClaimControllerV1();
            $search_fields_data = $ClaimController->generateSearchPageLoad('patient_workbench');
            $searchUserData = $search_fields_data['searchUserData'];
            $search_fields = $search_fields_data['search_fields'];
            
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice', 'patient_id', 'last_addin_problemlist', 'loop','searchUserData','search_fields')));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'No patient Available', 'data' => ''));
        }
    }

    public function getListIndexApi($patient_id='',$export=''){
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $request = Request::all();
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $type = (!empty(@$request['dataArr']['data']['type'])) ? 'assigned' : '' ;
        $problem_list = $this->problemListFilterQuery($request,$export);
        $current_user_id = Auth::user()->id;
        $url = Request::url();
        if(strpos($url, 'patients') !== false ) {
            $count = (!empty($type)) ? $problem_list->where('assign_user_id', $current_user_id)->count() : $problem_list
            ->where('problem_lists.patient_id', $patient_id)->count();
        }
        else {
            $count = (!empty($type)) ? $problem_list->where('assign_user_id', $current_user_id)->count() : $problem_list->count();
        }
        $problem_list = (!empty($type)) ? $problem_list->where('assign_user_id', $current_user_id) : $problem_list;
        $problem_list = $problem_list->where('problem_lists.patient_id', $patient_id);
        $problem_list = (isset($request['export'])) ? $problem_list->get() : $problem_list->skip($start)->take($len)->get();
        $problem_list_data = [];
        foreach ($problem_list as $prb_list) {
            if ($prb_list->claim_id != null && $prb_list->claim_id != '') {
                $last_prob_list_arr = $prb_list;
                $claim_id = $prb_list->claim_id;
                $total_charge = $prb_list->claim['total_charge'];
                $pmtClaimFinData = (!empty($prb_list->claim->pmtClaimFinData)) ? $prb_list->claim->pmtClaimFinData : [];
                $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                $last_prob_list_arr->claim->date_of_service = Helpers::dateFormat($last_prob_list_arr->claim->date_of_service, 'claimdate');
                $last_prob_list_arr->claim->total_paid  = $finDet['totalPaid']; 
                $last_prob_list_arr->claim->balance_amt = $finDet['balance'];
                $problem_list_data[$claim_id] = $last_prob_list_arr;
            }
        }
        return Response::json(array('status' => 'success', 'data' => compact('problem_list_data','count')));
    }    

    public function getCreateApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $practice = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->where('deleted_at', Null)->pluck('name')->all();
        $claims_number = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $id)->pluck('claim_number')->all();
        @$claim_number = implode(",", $claims_number);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice', 'claims_number', 'claim_number')));
    }

    public function getProblemNewStoreApi($request, $patient_id, $claim_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        // Check the option for unique
        $claim_number = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('claim_number', $claim_id)->value('id');
        $validator = Validator::make($request, ProblemList::$rules, ProblemList::$messages);
        // Check validation.
        if (!$validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();
            $request['claim_id'] = $claim_number;
            $request['fllowup_date'] = date("y-m-d", strtotime($request['fllowup_date']));
            $problemList = ProblemList::create($request);
            $user = Auth::user()->id;
            $problemList->assign_user_id = $request['assign_user_id'];
            $problemList->patient_id = $patient_id;
            $problemList->priority = $request['priority'];
            $problemList->created_by = $user;
            $problemList->save();
            $problemList->id = Helpers::getEncodeAndDecodeOfId($problemList->id, 'encode');
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => compact('patient_id', 'problemList', 'claims_list', 'claim_number')));
        }
    }

    public function getShowApiList($patient_id, $id) {

        $problemlist = ProblemList::with('claim', 'patient', 'user', 'created_by')->where('claim_id', $id)->orderBy('id', 'desc')->orderBy('fllowup_date', 'desc')->get();
       // $practice = Users::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->where('deleted_at', Null)->lists('name', 'id');
        $practice = Helpers::user_list();
        $claims_number = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('id', $id)->value('claim_number');
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice', 'problemlist', 'patient_id', 'claims_number', 'id')));
    }

    public function getProblemStoreApi($request, $patient_id, $claim_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        // Check the option for unique
        $claimInfo = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('claim_number', $claim_id)->first();
        $claim_number = $claimInfo->id;
        $patient_id = $claimInfo->patient_id;
        $validator = Validator::make($request, ProblemList::$rules, ProblemList::$messages);
        // Check validation.
        if (!$validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();
            $request['claim_id'] = $claim_number;
            $request['fllowup_date'] = date("y-m-d", strtotime($request['fllowup_date']));
            $problemList = ProblemList::create($request);
            $user = Auth::user()->id;
            $problemList->assign_user_id = $request['assign_user_id'];
            $problemList->patient_id = $patient_id;
            $problemList->priority = $request['priority'];
            $problemList->created_by = $user;
            $problemList->save();
            $problemlist = ProblemList::with('claim', 'patient', 'user', 'created_by')->where('claim_id', $claim_number)->orderBy('id', 'desc')->get();
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => compact('patient_id', 'claims_number', 'problemlist', 'claims_list')));
        }
    }

    public function getProblemListApi($type = null) {  
        $ClaimController  = new ClaimControllerV1();    
        $pageIp = (!empty($type)) ? 'armanagement_workbench_assigned' : 'armanagement_workbench_total'; 
        $search_fields_data = $ClaimController->generateSearchPageLoad($pageIp);
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'data' => compact('searchUserData','search_fields')));
    }
    
    public function getProblemListFilterApi($type='',$export=''){
        $request = Request::all();
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        if(isset($request['dataArr'] )){
            $request['export'] = json_decode(@$request['dataArr']['data']['export']);
        }
        if(isset($request['export'])) {
            $type = ($type == 'myproblemlist') ? 'assigned' : '' ;
        } else {
            $type = (!empty(@$request['dataArr']['data']['type'])) ? 'assigned' : '' ;
        }
        $problem_list = $this->problemListFilterQuery($request,$export);
        $current_user_id = Auth::user()->id;
        $count = (!empty($type)) ? $problem_list->where('assign_user_id', $current_user_id)->count() : $problem_list->count();
        $problem_list = (!empty($type)) ? $problem_list->where('assign_user_id', $current_user_id) : $problem_list;
        $problem_list = (isset($request['export'])) ? $problem_list->get() : $problem_list->skip($start)->take($len)->get();
        $problem_list_data = [];
        foreach ($problem_list as $prb_list) {
            if ($prb_list->claim_id != null && $prb_list->claim_id != '') {
                $last_prob_list_arr = $prb_list;
                $claim_id = $prb_list->claim_id;
                $total_charge = $prb_list->claim['total_charge'];
                $pmtClaimFinData = (!empty($prb_list->claim->pmtClaimFinData)) ? $prb_list->claim->pmtClaimFinData : [];
                $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                $last_prob_list_arr->claim->date_of_service = Helpers::dateFormat($last_prob_list_arr->claim->date_of_service, 'claimdate');
                $last_prob_list_arr->claim->total_paid  = $finDet['totalPaid']; 
                $last_prob_list_arr->claim->balance_amt = $finDet['balance'];
                $problem_list_data[$claim_id] = $last_prob_list_arr;
            }
        }
        return Response::json(array('status' => 'success', 'data' => compact('problem_list_data','count')));
    }
    
    
    public function problemListFilterQuery($request,$export){
        $problem_list = ProblemList::has('claim')->select('problem_lists.*')->with(['claim' => function($query)use($request) {
                        $query->select('id', 'claim_number', 'facility_id', 'insurance_id', 'rendering_provider_id', 'date_of_service','total_charge', 'sub_status_id');
                    }, 'claim.facility_detail' => function($query) {
                        $query->select('id', 'short_name', 'facility_name');
                    }, 'claim.insurance_details' => function($query) {
                        $query->select('id', 'short_name');
                    }, 'claim.rendering_provider', 'patient' => function($query) {
                        $query->select('id', 'account_no','last_name','first_name','middle_name','title','account_no','dob','gender','address1','city','state','zip5','zip4','phone','mobile','is_self_pay');
                    }, 'user' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'created_by' => function($query) {
                        $query->select('id', 'name', 'short_name');
                    }, 'claim.claim_sub_status' => function($query) {
                        $query->select('id', 'sub_status_desc');
                    }])->where('problem_lists.id', function ($sub) {
            $sub->selectRaw('max(id)')->from('problem_lists as pl')->where('pl.claim_id', DB::raw('problem_lists.claim_id'));
        });
        // Added sorting option for armanagement workbench 
        // Revision 1 : MR-2793 : 05 Sep 2019 :Aelva 
        $problem_list->leftjoin('claim_info_v1', function($join) {
            $join->on('claim_info_v1.id', '=', 'problem_lists.claim_id');
        });
        
        $problem_list->join('patients', function($join) {
            $join->on('patients.id', '=', 'problem_lists.patient_id');
        });
        
        $problem_list->leftjoin('providers as rendering_provider', function($join) {
            $join->on('rendering_provider.id', '=', 'claim_info_v1.rendering_provider_id');
        });

        $problem_list->leftjoin('insurances', function($join) {
            $join->on('insurances.id', '=', 'claim_info_v1.insurance_id');
        });

        $problem_list->leftjoin('facilities', function($join) {
            $join->on('facilities.id', '=', 'claim_info_v1.facility_id');
        });
        
        $problem_list->leftjoin(DB::raw("(SELECT      
          claim_id,     
          total_charge,patient_due, insurance_due,
          sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj)) as tot_ar,
          SUM(pmt_claim_fin_v1.insurance_due) as total_ins_due,
          SUM(pmt_claim_fin_v1.patient_due) as total_pat_due,
          (pmt_claim_fin_v1.patient_due+pmt_claim_fin_v1.insurance_due) as total_due,
          (pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid) as tot_paid
          FROM pmt_claim_fin_v1
          WHERE pmt_claim_fin_v1.deleted_at IS NULL
          GROUP BY pmt_claim_fin_v1.claim_id
          ) as pmt_claim_fin_v1"), function($join) {
            $join->on('pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id');
        });
        
        
        if (!empty($request['order'])) {
            $orderField = ($request['order'][0]['column']) ? $request['order'][0]['column'] : 'id';
            switch ($orderField) {
                case '1':
                    $orderByField = 'claim_info_v1.date_of_service';        // DOS
                    break;

                case '2':
                    $orderByField = 'claim_info_v1.claim_number';           
                    break;
                
                case '3':
                    $orderByField = 'patients.last_name';          
                    break;
                    
                case '4':
                    $orderByField = 'rendering_provider.short_name';        
                    break;
                    
                case '5':
                    $orderByField = 'facilities.short_name';               
                    break;

                case '6':
                    $orderByField = 'insurances.short_name';               
                    break;
                    
                case '7':
                    $orderByField = 'claim_info_v1.total_charge';               
                    break;
                
                case '8':
                    $orderByField = 'pmt_claim_fin_v1.total_due';               
                    break;
                
                case '9':
                    $orderByField = 'pmt_claim_fin_v1.tot_paid';               
                    break;
                    
                case '10':
                    $orderByField = 'problem_lists.status';               
                    break;
                
                case '11':
                    $orderByField = 'problem_lists.fllowup_date';               
                    break;
                

                default:
                    $orderByField = 'claim_info_v1.id';
                    break;
            }
            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
            $problem_list->orderBy($orderByField, $orderByDir);
            //\Log::info($orderByField. $orderByDir);
        }
        
        if(isset($export) && $export !=''){
            $problem_list->orderBy('claim_info_v1.date_of_service', 'desc');
        }
        if(!empty(json_decode(@$request['dataArr']['data']['facility_id']))){
            $facility_id = json_decode($request['dataArr']['data']['facility_id']);
            $problem_list->WhereHas('claim', function($q)use($facility_id) {
                $q->WhereIn('facility_id',$facility_id);
            });         
        } 
        if(!empty(json_decode(@$request['dataArr']['data']['patient_id']))){
            $patient_id = json_decode($request['dataArr']['data']['patient_id']);
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
            $problem_list->where('problem_lists.patient_id',$patient_id);
        }
        if(!empty(json_decode(@$request['dataArr']['data']['followup_date']))){
            $date = explode('-',json_decode($request['dataArr']['data']['followup_date']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->where(DB::raw('DATE(fllowup_date)'),'>=',$from)->where(DB::raw('DATE(fllowup_date)'),'<=',$to);           
        }
        if(!empty(json_decode(@$request['dataArr']['data']['created_at']))){
            $date = explode('-',json_decode($request['dataArr']['data']['created_at']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->where(DB::raw('DATE(fllowup_date)'),'>=',$from)->where(DB::raw('DATE(fllowup_date)'),'<=',$to);           
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['dos']))){
            $date = explode('-',json_decode($request['dataArr']['data']['dos']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->WhereHas('claim', function($q)use($from, $to) {
                $q->where(DB::raw('DATE(date_of_service)'),'>=',$from)->where(DB::raw('DATE(date_of_service)'),'<=',$to);
            });           
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['assigned_to']))){
            $problem_list->whereIn('problem_lists.assign_user_id',json_decode(@$request['dataArr']['data']['assigned_to']));           
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['status']))){
            $problem_list->whereIn('problem_lists.status',json_decode(@$request['dataArr']['data']['status']));           
        }

        if (!empty(json_decode(@$request['dataArr']['data']['status_reason']))) {
            $problem_list->whereIn('claim_info_v1.sub_status_id', json_decode($request['dataArr']['data']['status_reason']));
        }
        
        if (isset($request['dataArr']['data']['status_reason']) && !empty(json_decode(@$request['dataArr']['data']['status_reason'])) && count(array_filter(json_decode(@$request['dataArr']['data']['status_reason'])))!=count(json_decode(@$request['dataArr']['data']['status_reason']))) {
            $problem_list->orWhereNull('claim_info_v1.sub_status_id');
        }

        if(!empty(json_decode(@$request['dataArr']['data']['priority']))){ 
            $problem_list->whereIn('problem_lists.priority',json_decode(@$request['dataArr']['data']['priority']));           
        }
        
        /*
            Workbench: Description should be in Export and show the notes when hover the mouse 
            Rev. 1, Ref: MR-2873 - Ravi - 16-09-19
        */    
        if(!empty(json_decode(@$request['dataArr']['data']['description']))){
            $dc = json_decode(@$request['dataArr']['data']['description']);
            $problem_list->where('problem_lists.description','like','%' .$dc.'%');
        }        
        
        if(!empty(json_decode(@$request['dataArr']['data']['billed_to']))){       
            $billed_to = json_decode($request['dataArr']['data']['billed_to']);
            $problem_list->WhereHas('claim', function($q)use($billed_to) {
                $q->WhereIn('insurance_id',$billed_to);
            }); 
        }

        if(!empty(json_decode(@$request['dataArr']['data']['claim_no']))){
            $claim_no = json_decode($request['dataArr']['data']['claim_no']);
            $problem_list->WhereHas('claim', function($q)use($claim_no) {
                $q->Where('claim_number',$claim_no);
            });             
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['rendering_provider_id']))){
            $rendering_provider_id = json_decode($request['dataArr']['data']['rendering_provider_id']);
            $problem_list->WhereHas('claim', function($q)use($rendering_provider_id) {
                $q->WhereIn('rendering_provider_id',$rendering_provider_id);
            });             
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['patient_name']))){
            $dynamic_name = json_decode($request['dataArr']['data']['patient_name']);
            $problem_list->WhereHas('claim.patient', function($q)use($dynamic_name) {
                $q->Where(DB::raw('CONCAT(last_name,", ", first_name)'),  'like', "%{$dynamic_name}%" );
            }); 
        }
        
        ##Export block started              
        if (isset($request['facility_id']) && $request['facility_id']!='') {
            $facility_id = explode(',',$request['facility_id']);
            $problem_list->WhereHas('claim', function($q)use($facility_id) {
                $q->WhereIn('facility_id', $facility_id);
            });
        }

        if(!empty(json_decode(@$request['dataArr']['data']['created_at']))){
            $date = explode('-', json_decode(@$request['dataArr']['data']['created_at']));
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->where(DB::raw('DATE(fllowup_date)'), '>=', $from)->where(DB::raw('DATE(fllowup_date)'), '<=', $to);
        }
        
        if (isset($request['created_at']) && $request['created_at']!='') {
            $date = explode('-',$request['created_at']);
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->where(DB::raw('DATE(fllowup_date)'),'>=',$from)->where(DB::raw('DATE(fllowup_date)'),'<=',$to);           
        }

        if (isset($request['dos']) && $request['dos']!='') {
            $date = explode('-', $request['dos']);
            $from = date("Y-m-d", strtotime($date[0]));
            $to = date("Y-m-d", strtotime($date[1]));
            $problem_list->WhereHas('claim', function($q)use($from, $to) {
                $q->where(DB::raw('DATE(date_of_service)'), '>=', $from)->where(DB::raw('DATE(date_of_service)'), '<=', $to);
            });
        }

        if (isset($request['assigned_to']) && $request['assigned_to']!='') {
            $problem_list->whereIn('problem_lists.assign_user_id', explode(',',@$request['assigned_to']));
        }

        if (isset($request['status']) && $request['status']!='') {
            $problem_list->whereIn('problem_lists.status', explode(',',@$request['status']));
        }

        if (isset($request['priority']) && $request['priority']!='') {
            $problem_list->whereIn('problem_lists.priority', explode(',',@$request['priority']));
        }

        if (isset($request['description']) && $request['description'] != "") {
            $problem_list->where('problem_lists.description','like','%' .$request['description'].'%');
        }

        if (isset($request['billed_to']) && $request['billed_to']!='') {
            $billed_to = explode(',',$request['billed_to']);
            $problem_list->WhereHas('claim', function($q)use($billed_to) {
                $q->WhereIn('insurance_id', $billed_to);
            });
        }
        if (isset($request['claim_no']) && $request['claim_no']!='') {
            $claim_no = $request['claim_no'];
            $problem_list->WhereHas('claim', function($q)use($claim_no) {
                $q->Where('claim_number', $claim_no);
            });
        }

        if (isset($request['rendering_provider_id']) && $request['rendering_provider_id']!='') {
            $rendering_provider_id = explode(',',$request['rendering_provider_id']);
            $problem_list->WhereHas('claim', function($q)use($rendering_provider_id) {
                $q->WhereIn('rendering_provider_id', $rendering_provider_id);
            });
        }

        if (isset($request['patient_name']) && $request['patient_name']!='') {
            $dynamic_name = $request['patient_name'];
            $problem_list->WhereHas('claim.patient', function($q)use($dynamic_name) {
                $q->Where(DB::raw('CONCAT(last_name,", ", first_name)'), 'like', "%{$dynamic_name}%");
            });
        }
        ##Export block end        
        return $problem_list;
    }
        

    function __destruct() {
        
    }

}