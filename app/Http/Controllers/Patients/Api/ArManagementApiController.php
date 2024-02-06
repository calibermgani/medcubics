<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;

use App\Models\Provider as Provider;
use App\Models\Patients\Patient as Patient;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Code as Code;
use Input;
use Auth;
use Response;
use Request;
use DB;
use Session;
use Lang;
use App\Http\Helpers\Helpers as Helpers;
use View;
use Cache;
use App\Models\Practice as Practice;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Patients\ProblemList as ProblemList;
use App\Models\Pos as Pos;
use App\Models\Icd as Icd;
use App\Models\Cpt as Cpt;
use App\Models\Patients\PatientBudget;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;

use Config;  
use App\Http\Controllers\Patients\Api\BillingApiController as BillingApiController;
use App\Http\Controllers\Payments\Api\PatientPaymentApiController as PatientPaymentApiController;
use App\Http\Controllers\Armanagement\Api\ArmanagementApiController as ArmanagementApiMainController;
use App\Models\FollowupCategory as FollowupCategory;
use App\Models\FollowupQuestion as FollowupQuestion;
use App\Traits\ClaimUtil;
use App\Models\Holdoption as Holdoption;

class ArManagementApiController extends Controller {

    use ClaimUtil;

   /* public function getIndexApi($patient_id) {
        $this->getAR_SummaryPageDataApi();
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }*/
    public function getIndexApi($patient_id) {
       
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
             $arsummary = $this->getAR_SummaryPageDataApi($patient_id);
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('arsummary')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    public function getListsApi($patient_id) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
		// Added hold reason for bulk hold option in armanagement
		// Revision 1 : MR-2786 : 4 Sep 2019
		$hold_options = Holdoption::where('status', 'Active')->pluck('option', 'id')->all();
		$encodeClaimIds = [];
        if (Patient::where('id', $patient_id)->count()) {
            if (Request::ajax()) {
                if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                    $request = Request::all();
                    $query = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail','paymentInfo', 'pmtClaimFinData','followup_details', 'claim_sub_status')->where('patient_id', $patient_id);
                    //->whereNotIn('status', ['Hold']);
                    
                    if (@$request['billing_provider_id'] != '') {
                        $query->where('billing_provider_id', $request['billing_provider_id']);
                    }

                    if (@$request['rendering_provider_id'] != '') {
                        $query->where('billing_provider_id', $request['rendering_provider_id']);
                    }

                    if (@$request['referring_provider_id'] != '') {
                        $query->where('refering_provider_id', $request['referring_provider_id']);
                    }

                    if (@$request['insurance_id'] != '') {
                        $query->where('insurance_id', $request['insurance_id']);
                    }

                    if (@$request['facility_id'] != '') {
                        $query->where('facility_id', $request['facility_id']);
                    }

                    if (@$request['dos_from'] != '' && @$request['dos_to'] != '') {
                        $from = date("Y-m-d", strtotime($request['dos_from']));
                        $to = date("Y-m-d", strtotime($request['dos_to']));
                        $query->whereBetween('date_of_service', [$from, $to]);
                    }

                    if (@$request['billed_option'] != '' && @$request['billed'] != '') {
                        if ($request['billed_option'] == 'lessthan')
                            $billed_option = '<';
                        elseif ($request['billed_option'] == 'lessequal')
                            $billed_option = '<=';
                        elseif ($request['billed_option'] == 'equal')
                            $billed_option = '=';
                        elseif ($request['billed_option'] == 'greaterthan')
                            $billed_option = '>';
                        elseif ($request['billed_option'] == 'greaterequal')
                            $billed_option = '>=';
                        else
                            $billed_option = '=';
                        $billed_amount = @$request['billed'];

                        $query->whereHas('dosdetails', function ($q) use ($billed_amount, $billed_option) {
                            $q->select(DB::raw("SUM(charge) as total_charge"))
                                    ->groupBy('claim_id')
                                    ->having('total_charge', $billed_option, $billed_amount)
                                    ->where('is_active', 1);
                        });
                    }
                    $claims_list = $query->orderBy('id', 'DESC')->get();
                    // New pmt flow integration start
                    for ($m = 0; $m < count($claims_list); $m++) {
                        if (!empty($claims_list)) {
                            $claim_id = $claims_list[$m]->id;
                            $total_charge = $claims_list[$m]->total_charge;
                            $claimSubmittedCount = $claims_list[$m]->claim_submit_count;
                            $paymentInfoId = @$claims_list[$m]->paymentInfo->id;
                            $claimStatus = @$claims_list[$m]->status;
                            $pmtClaimFinData = (!empty($claims_list[$m]->pmtClaimFinData)) ? $claims_list[$m]->pmtClaimFinData : [];
                            //if any payment made against claims unbilled 0 and claimsubmitted count> 0
                            if ($claimSubmittedCount > 0 || $paymentInfoId > 0 && $claimStatus != 'Ready') {
                                $claims_list[$m]['unbilled'] = false;
                            } else {
                                $claims_list[$m]['unbilled'] = true;
                            }
                            //select the finData using claim_id                            
                            /*
                            $paymentV1 = new PaymentV1ApiController();
                            $resultData = $paymentV1->getClaimsFinDetails($claim_id, $total_charge);                            
                            $claim_lists[$m]['total_paid'] = $resultData['total_paid'];
                            $claim_lists[$m]['balance_amt'] = $resultData['balance_amt'];   
                            */                            
                            $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                            $claims_list[$m]['total_paid'] = $finDet['totalPaid'];
                            $claims_list[$m]['balance_amt'] = $finDet['balance'];
							$claims_list[$m]['patient_due'] = $finDet['patient_due'];                    
							$claims_list[$m]['insurance_due'] = $finDet['insurance_due'];
                        }
                    }
                    // New pmt flow integration end   
                }
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list','hold_options','encodeClaimIds')));
            } else {
                if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                    $claims_lists = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail', 'paymentInfo', 'pmtClaimFinData','followup_details', 'claim_sub_status')->where('patient_id', $patient_id);
                    $claims_list = $claims_lists->orderBy('id', 'DESC')->get();
					
					$claimsIds = $claims_lists->pluck('claim_number')->all();		
					/* Armanagement  bulk notes and workbench chnaged based on all */
					/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
					$encodeClaimIds = [];
					foreach($claimsIds as $list){
						$encodeClaimIds[] = $list;
					}

                    // New pmt flow integration start
                    for ($m = 0; $m < count($claims_list); $m++) {
                        if (!empty($claims_list)) {
                            $claim_id = $claims_list[$m]->id;
                            $total_charge = $claims_list[$m]->total_charge;
                            $claimSubmittedCount = $claims_list[$m]->claim_submit_count;
                            $paymentInfoId = @$claims_list[$m]->paymentInfo->id;
                            $claimStatus = @$claims_list[$m]->status;
                            $pmtClaimFinData = (!empty($claims_list[$m]->pmtClaimFinData)) ? $claims_list[$m]->pmtClaimFinData : [];
                            //if any payment made against claims unbilled 0 and claimsubmitted count> 0
                            if ($claimSubmittedCount > 0 || $paymentInfoId > 0 && $claimStatus != 'Ready') {
                                $claims_list[$m]['unbilled'] = false;
                            } else {
                                $claims_list[$m]['unbilled'] = true;
                            }
                            //select the finData using claim_id                            
                            /*
                            $paymentV1 = new PaymentV1ApiController();
                            $resultData = $paymentV1->getClaimsFinDetails($claim_id, $total_charge);                            
                            $claims_list[$m]['total_paid'] = $resultData['total_paid'];
                            $claims_list[$m]['balance_amt'] = $resultData['balance_amt'];   
                           */                            
                            $finDet = ClaimInfoV1::getClaimCptFinBalance($total_charge, $pmtClaimFinData);
                            $claims_list[$m]['total_paid'] = $finDet['totalPaid'];
                            $claims_list[$m]['balance_amt'] = $finDet['balance'];
							$claims_list[$m]['patient_due'] = $finDet['patient_due'];                    
							$claims_list[$m]['insurance_due'] = $finDet['insurance_due'];
                        }
                    }
                    // New pmt flow integration end   
                }else{
					// added empty for no claims found for this patient
					$encodeClaimIds = [];
				}
                $practice_id = Session::get('practice_dbid');

                $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
                $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
                $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
                $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
                $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();

                $billing_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
                $rendering_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
                $referring_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
                $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
                $facility = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();

                $category = FollowupCategory::where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();
				$patient_insurance = Patient::getARPatientInsurance($patient_id);
                $question = FollowupCategory::with(['question' => function($query) {
                                $query->where('deleted_at', null)->where('status', 'Active');
                            }])->where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'user_list', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question','patient_insurance','encodeClaimIds','hold_options')));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    public function getFollowupListsApi($patient_id) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
		/* Changed condition for notes showing in armanagement */
		/* MEDV2-1060 : AR Management:Inactive claim notes and denials codes should not shown in AR Management */
			$followup_claim_ids = PatientNote::where('title', 'ArManagement')->where('status','Active')->pluck('claim_id')->all();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->whereNotIn('status', ['Hold'])->whereIn('id', $followup_claim_ids)->orderBy('id', 'DESC')->get();
            }

            $practice_id = Session::get('practice_dbid');

            $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
            $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
            $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
            $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
            $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();

            $billing_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
            $rendering_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
            $referring_provider = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
            $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
            $facility = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();

            $category = FollowupCategory::where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

            $question = FollowupCategory::with(['question' => function($query) {
                            $query->where('deleted_at', null)->where('status', 'Active');
                        }])->where('deleted_at', null)->where('status', 'Active')->select('id', 'name', 'label_name')->get()->toArray();

            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'user_list', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    public function getViewApi($patient_id, $claim_id = null) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();

        $providers = Provider::getBillingAndRenderingProvider('yes');
        $rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);

        $patient_insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                    $q->where('patient_id', $patient_id);
                })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->get();
        $insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                    $q->where('patient_id', $patient_id)->where('category', 'Primary');
                })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->first();
        $insurance_data = [];
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'employer_details', 'dosdetails')->where('id', $claim_id)->first();
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_id', 'facilities', 'providers', 'rendering_providers', 'referring_providers', 'billing_providers', 'insurances', 'patient_detail', 'modifier', 'claims_list', 'hold_options', 'insurance_data')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    public function getclaimtabdetailsApi() {
        $request = Request::all();
		
        if (isset($request['prev_sel_claim_id_values']) && $request['prev_sel_claim_id_values'] == "") {
            $added_claim_tabs = $request['selected_claim_id_values'];
            $remove_claim_tabs = "";
        } else {
            $prev_sel_claim_id_arr = (!empty($request['prev_sel_claim_id_values'])) ? explode(",", $request['prev_sel_claim_id_values']) : [];
            $selected_claim_id_arr = (!empty($request['selected_claim_id_values'])) ? explode(",", $request['selected_claim_id_values']) : [];
            $remove_claim_arr = array_diff($prev_sel_claim_id_arr, $selected_claim_id_arr);
            $added_claim_arr = array_diff($selected_claim_id_arr, $prev_sel_claim_id_arr);
            $added_claim_tabs = implode(",", $added_claim_arr);
            $remove_claim_tabs = implode(",", $remove_claim_arr);
        }
        $claim_ids_arr = !empty($added_claim_tabs) ? explode(",", $added_claim_tabs) : [];
        
        $practice_timezone = Helpers::getPracticeTimeZone();
        $claim_detail = ClaimInfoV1::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(submited_date,"UTC","'.$practice_timezone.'") as submited_date'),DB::raw('CONVERT_TZ(last_submited_date,"UTC","'.$practice_timezone.'") as last_submited_date'))->with(['billing_provider', 'insurance_details', 'facility_detail', 'rendering_provider', 'refering_provider', 'documents', 
                    'claim_sub_status'=>function($query) {
                        $query->select('id', 'sub_status_desc');
                    },
                    'claim_notes_details' => function($query2) use ($practice_timezone) {
                        $query2->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->orderBy('id', 'desc');
                    }, 
                    'eligibility_list', 'patient', 'problem_list', 'dosdetails', 
                    'claim_details' => function($query) {
                        $query->select('id', 'claim_id', 'illness_box14', 'box_23','box23_type');
                    }])->whereIn('claim_number', $claim_ids_arr)->get();
		$patient_id = @$claim_detail[0]->patient_id;
        $paymentV1ApiController = new PaymentV1ApiController();
        foreach ($claim_detail as $key => $claim_data) {
            $create_claim_id = $claim_data->id;
            $total_charge = $claim_data->total_charge;
            // Fetching financial datas
            $resultData = $paymentV1ApiController->getClaimsFinDetails($create_claim_id, $total_charge);

            $claim_detail[$key]->{'total_paid'} = $resultData['total_paid'];
            $claim_detail[$key]->{'total_adjusted'} = $resultData['totalAdjustment'];
            $claim_detail[$key]->{'total_withheld'} = $resultData['withheld'];
            $claim_detail[$key]->{'patient_due'} = $resultData['patient_due'];
            $claim_detail[$key]->{'insurance_due'} = $resultData['insurance_due'];
            $claim_detail[$key]->{'claim_tx_list'} = $this->getClaimTxnDesc($create_claim_id);
            $claim_detail[$key]->{'cpt_tx_list'} = $this->getClaimCptTxnDesc($create_claim_id);
            $claim_detail[$key]->{'attachment_detail'} = $this->getClaimAttachemts($create_claim_id);
        }
       
        /*         * ***************************************************************************************************************** */
        $rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $pos = Pos::pluck('code', 'id')->all();
        /*         * ***************************************************************************************************************** */

        $practice_id = 4;
        if (Session::get('practice_dbid'))
            $practice_id = Session::get('practice_dbid');
        $practice_det = Practice::with('speciality_details')->where('id', $practice_id)->first();

        $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
        $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
        $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
        $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
        $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();
        $denial_codes_arr = [];
        //$denial_codes_arr = Code::select('id','transactioncode_id','description')->get(); // Commented this since no need to load this on page load. instead called on open of the popup
        //$denial_codes_arr = Code::where('codecategory_id', 3)->get();	
        $patient_notes = is_null($patient_id) ? [] : PatientNote::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->where('notes_type', 'patient')->where('patient_notes_type', 'patient_notes')->where('status','Active')->where('notes_type_id', $patient_id)->with('user')->get();
		
		/* Patient Insurance list */
		
		$patient_insurance = is_null($patient_id) ? [] :  PatientInsurance::getInsurance($patient_id);
		/* Patient Insurance list */
		
        return Response::json(array('status' => 'success', 'added_claim_tabs' => $added_claim_tabs, 'remove_claim_tabs' => $remove_claim_tabs, 'claim_detail' => $claim_detail, 'practice_det' => $practice_det, 'user_list' => $user_list, 'tab_type' => $request['tab_type'], 'rendering_providers' => $rendering_providers, 'referring_providers' => $referring_providers, 'billing_providers' => $billing_providers, 'facilities' => $facilities, 'pos' => $pos, 'denial_codes_arr' => $denial_codes_arr, 'patient_notes' => $patient_notes,'patient_insurance'=>$patient_insurance));
    }

    public function getclaimnotesaddedApi() {
        $request = Request::all();
        $claim_id_array = explode(",", $request['claim_id']);
        $claim_detail = ClaimInfoV1::whereIn('claim_number', $claim_id_array)->selectRaw('id, patient_id, claim_number, DATE_FORMAT(date_of_service, "%d-%l-%Y") as date_of_service')->get();
        $created_by = Auth::user()->id;
        $created_name = Auth::user()->name;
        $created_date = date('d M Y', strtotime(date('Y-m-d H:i:s')));
        foreach ($claim_detail as $claim_detail_val) {
            PatientNote::insert(['title' => 'ArManagement', 'content' => $request['claim_notes'], 'notes_type' => 'patient', 'patient_notes_type' => 'claim_notes', 'claim_id' => $claim_detail_val['id'], 'notes_type_id' => $claim_detail_val['patient_id'], 'created_by' => $created_by, 'created_at' => Date('Y-m-d H:i:s')]);
        }
        $claim_detail_dos = $claim_detail->pluck('date_of_service', 'claim_number')->all();
        return Response::json(array('status' => 'success', 'data' => compact('created_name', 'created_date', 'claim_detail_dos')));
    }

    public function getclaimdenailnotesaddedApi() {
        $request = Request::all();
        $claim_id = $request['claim_id'];
        $denialInsuranceId = explode('-',$request['denial_insurance'])[1];
        $claim_detail = ClaimInfoV1::where('claim_number', $claim_id)
            ->selectRaw('id, patient_id,insurance_id,self_pay, status, insurance_category, claim_number, DATE_FORMAT(date_of_service, "%d-%l-%Y") as date_of_service')->first();
        $paymentV1 = new PaymentV1ApiController();
        $request['claim_id'] = $claim_detail['id'];
        $request['patient_id'] = $claim_detail['patient_id'];
        $request['claim_details'] = $claim_detail;
        $actionStatus = $paymentV1->addDenialFromArManagment($request);
        if ($actionStatus) {
            $created_by = Auth::user()->id;
            $created_name = Auth::user()->name;
            $date_of_service = $claim_detail['date_of_service'];
            $created_date = date('d M Y', strtotime(date('Y-m-d H:i:s')));
            $content = $request['denial_date'] . "^^^" . $request['check_no'] . "^^^" . $denialInsuranceId . "^^^" . $request['reference'] . "^^^" . implode(",", $request['denial_codes']);
            $denial_code_result = array();
            foreach ($request['denial_codes'] as $denial_code_value) {
                $denial_code_result[$denial_code_value] = "A0 	Patient refund amount";
            }
            $denial_insurance_name = Insurance::where('id', '=', $denialInsuranceId)->value('insurance_name');
            PatientNote::create(['title' => 'ArManagement', 'content' => $content, 'notes_type' => 'patient', 'patient_notes_type' => 'claim_denial_notes', 'claim_id' => $claim_detail['id'], 'notes_type_id' => $claim_detail['patient_id'], 'created_by' => $created_by]);
            return Response::json(array('status' => 'success', 'data' => compact('created_name', 'created_date', 'date_of_service', 'content', 'denial_insurance_name', 'denial_code_result')));
        }else {
            return Response::json(array('status' => 'failed', 'data' => 'failed'));
        }
    }
    public function getclaimassignaddedApi() {
        $request = Request::all();
        $id_array = (isset($request['claim_id'])) ? explode(",", $request['claim_id']) : [];        
        $claim_id_array = [];
        foreach ($id_array as $clID) {
            $claim_id_array[] = (is_numeric($clID)) ? $clID : Helpers::getEncodeAndDecodeOfId($clID, 'decode');        
        }

        if (!strpos($request['claim_id'], ',') !== false) {
            $claim_detail = ClaimInfoV1::where('id', $claim_id_array)->selectRaw('id, patient_id')->get();
        } else {
            $claim_detail = ClaimInfoV1::whereIn('claim_number', $claim_id_array)->selectRaw('id, patient_id')->get();
        }
        $created_by = Auth::user()->id;
        foreach ($claim_detail as $claim_detail_val) {
            ProblemList::create(['patient_id' => $claim_detail_val['patient_id'], 'claim_id' => $claim_detail_val['id'], 'assign_user_id' => $request['assign_to'], 'fllowup_date' => date("y-m-d", strtotime($request['follow_up_date'])), 'status' => $request['status'], 'priority' => $request['priority'], 'description' => $request['description'], 'created_by' => $created_by]);
        }
        return Response::json(array('status' => 'success'));
    }

    public function getclaimstatusnotesaddedApi() {
        $request = Request::all();
		
        $content_val = $request;
		$source_id = time();
		if(isset($request['check_box_claim_note'])){
			$noteStatus = 'Active';
		}else{
			$noteStatus = 'Hidden';
		}
		
		
		$temp = $request;
		$temp = array_filter($temp);
		
		$claim_detail = ClaimInfoV1::where('claim_number', $temp['claim_id'])->selectRaw('id, patient_id')->first();
		
		$created_by = Auth::user()->id;
		$notes = '';
		
		if(isset($temp['followup_rep_name']))
			$notes .= ' Rep Name: '.$temp['followup_rep_name'].', ';
		
		if(isset($temp['followup_dos']))
			$notes .= ' Date: '.$temp['followup_dos'].', ';
		
		if(isset($temp['followup_phone']))
			$notes .= ' Phone: '.$temp['followup_phone'].', ';
		
		if(isset($temp['followup_phone_ext']))
			$notes .= ' Ext: '.$temp['followup_phone_ext'].", ";
		
		if(isset($temp['insurance'])){
			$insuranceId = explode('-',$temp['insurance']);
			$insuranceName = Insurance::where('id',$insuranceId[1])->select('insurance_name')->get()->first();
			$notes .= ' Insurance: '.$insuranceName->insurance_name.' - '.$insuranceId[0];
		}
		
		if(isset($temp['claim_status_radio']))
			$notes .= ', Type: '.str_replace("_"," ",$temp['claim_status_radio'])." ";
		
		$callerQuestion = '';
		$excludeArr = ['followup_rep_name','followup_dos','followup_phone','followup_phone_ext','insurance','claim_status_radio','claim_id','form_modue_type','_token','check_box_claim_note'];
		 foreach($temp as $key => $Qlist){
			if(!in_array($key,$excludeArr)){
				$arrayIndex = explode('~~',$key);
				if(isset($arrayIndex[2]))
					$callerQuestion = $callerQuestion . str_replace("_"," ",$arrayIndex[2]).": ".$Qlist.', ';
			}
		}
		
		$notes .= $callerQuestion;
		
		PatientNote::create(['title' => '', 'content' => $notes, 'follow_up_content' => '', 'notes_type' => 'patient', 'patient_notes_type' => 'claim_notes', 'claim_id' => $claim_detail['id'], 'notes_type_id' => $claim_detail['patient_id'], 'created_by' => $created_by, 'user_id' => '','source_id' => $source_id,'status' => $noteStatus]);
		
		/* Sample response 
		
		Array
				(
					[_token] => 9OHpljPZNbFOtEriW4JXLErzhi5cNhEghLy1WKQq
					[followup_rep_name] => fdghfgh
					[followup_dos] => 03/18/2020
					[followup_phone] => (901) 115-1515
					[followup_phone_ext] => 5675
					[insurance] => Primary-2
					[claim_status_radio] => Claim_In_Process
					[Claim_In_Process~~When_did_you_receive_the_claim?~~Clm_rcvd_date] => 03/24/2020
					[Claim_In_Process~~What's_the_processing_time_?~~Clm_processing_time] => ftghfg
					[Claim_In_Process~~When_shall_i_callback?~~Callback_after] => hfghf
					[Claim_In_Process~~What's_the_reference_number_?~~Ref_No] => ghfg
					[claim_id] => 06624
					[form_modue_type] => follow_up
				)
		
		 Sample response */
		
        unset($content_val['_token']);
        unset($content_val['form_modue_type']);
        unset($content_val['claim_id']);
        unset($content_val['followup_rep_name']);
        unset($content_val['followup_dos']);
        unset($content_val['followup_phone']);
        unset($content_val['followup_phone_ext']);
        unset($content_val['user_id']);
        $patient_notes_type = @$request['claim_status_radio'];
        $follow_up_content = "";

        if ($request['form_modue_type'] == 'follow_up') {
            $follow_up_content = @$request['followup_rep_name'] . "^^::^^" . @$request['followup_phone'] . "^^::^^" . @$request['followup_dos'] . "^^::^^" . @$request['followup_phone_ext'];
        }
        /* if($patient_notes_type=='claim_nis'){
          if(@$request['claim_nis_effective_date_from']==''&&@$request['claim_nis_effective_date_to']==''&&@$request['claim_nis_cord_benefit_prio']==''&&@$request['claim_nis_filling_limit']==''&&@$request['claim_nis_claim_mail_add']==''&&@$request['claim_nis_fax_number_attention']==''&&@$request['claim_nis_electronic_payerid']==''&&@$request['claim_nis_reference_number']==''&&@$request['claim_nis_notes']=='')
          {
          $content_val = "";
          }
          else
          {
          $content_val = @$request['claim_nis_effective_date_from']."||::||".@$request['claim_nis_effective_date_to']."||::||".@$request['claim_nis_cord_benefit_prio']."||::||".@$request['claim_nis_filling_limit']."||::||".@$request['claim_nis_claim_mail_add']."||::||".@$request['claim_nis_fax_number_attention']."||::||".@$request['claim_nis_electronic_payerid']."||::||".@$request['claim_nis_reference_number']."||::||".@$request['claim_nis_notes'];
          }
          }
          elseif($patient_notes_type=='claim_in_process'){
          if(@$request['claim_inprocess_receive_on']==''&&@$request['claim_inprocess_processing_time']==''&&@$request['claim_inprocess_reference_number']==''&&@$request['claim_inprocess_notes']=='')
          {
          $content_val = "";
          }
          else
          {
          $content_val = @$request['claim_inprocess_receive_on']."||::||".@$request['claim_inprocess_processing_time']."||::||".@$request['claim_inprocess_reference_number']."||::||".@$request['claim_inprocess_notes'];
          }
          }
          elseif($patient_notes_type=='claim_paid'){
          if(!isset($request['claim_paid_type'])&&@$request['claim_paid_processed_date']==''&&@$request['claim_paid_amount']==''&&@$request['claim_paid_allowed_amount']==''&&@$request['claim_paid_coinsurance']==''&&@$request['claim_paid_copay']==''&&@$request['claim_paid_deductible']==''&&!isset($request['claim_paid_copy_eob'])&&@$request['claim_paid_patient_plan']==''&&@$request['claim_paid_eft_check_number']==''&&@$request['claim_paid_bulk_check_amount']==''&&@$request['claim_paid_cash_date']==''&&@$request['claim_paid_reference_number']==''&&@$request['claim_paid_callback_date']==''&&@$request['claim_paid_pay_address']==''&&!isset($request['claim_paid_req_stop_payment']))
          {
          $content_val = "";
          }
          else
          {
          $content_val = @$request['claim_paid_type']."||::||".@$request['claim_paid_processed_date']."||::||".@$request['claim_paid_amount']."||::||".@$request['claim_paid_allowed_amount']."||::||".@$request['claim_paid_coinsurance']."||::||".@$request['claim_paid_copay']."||::||".@$request['claim_paid_deductible']."||::||".@$request['claim_paid_patient_plan']."||::||".@$request['claim_paid_copy_eob']."||::||".@$request['claim_paid_eft_check_number']."||::||".@$request['claim_paid_bulk_check_amount']."||::||".@$request['claim_paid_cash_date']."||::||".@$request['claim_paid_reference_number']."||::||".@$request['claim_paid_callback_date']."||::||".@$request['claim_paid_pay_address']."||::||".@$request['claim_paid_req_stop_payment'];
          }
          }
          elseif($patient_notes_type=='claim_denied'){
          if($request['claim_denied_date']==''&&$request['claim_denied_policy_active_date']==''&&$request['claim_denied_filing_limit']==''&&$request['claim_denied_electronic_payerid']==''&&$request['claim_denied_appeal_limit']==''&&$request['claim_denied_appeal_fax_number']==''&&$request['claim_denied_mailing_address']==''&&$request['claim_denied_appeal_mailing_address']==''&&$request['claim_denied_reference_number']==''&&$request['claim_denied_callback_date']=='')
          {
          $content_val = "";
          }
          else
          {
          $content_val = @$request['claim_denied_date']."||::||".@$request['claim_denied_policy_active_date']."||::||".@$request['claim_denied_filing_limit']."||::||".@$request['claim_denied_electronic_payerid']."||::||".@$request['claim_denied_appeal_limit']."||::||".@$request['claim_denied_appeal_fax_number']."||::||".@$request['claim_denied_mailing_address']."||::||".@$request['claim_denied_appeal_mailing_address']."||::||".@$request['claim_denied_reference_number']."||::||".@$request['claim_denied_callback_date'];
          }
          }
          elseif($patient_notes_type=='left_voice_message'){
          $content_val = @$request['left_voice_msg'];
          }
          elseif($patient_notes_type=='claim_pending'){
          if($request['claim_pending_receive_date']==''&&$request['claim_pending_reason']==''&&$request['claim_pending_attachment_mailing_add']==''&&$request['claim_pending_filing_limit']==''&&$request['claim_pending_appeal_limit']==''&&$request['claim_pending_mailing_address']==''&&$request['claim_pending_appeal_mailing_address']==''&&$request['claim_pending_reference_number']==''&&$request['claim_pending_callback_date']=='')
          {
          $content_val = "";
          }
          else
          {
          $content_val = @$request['claim_pending_receive_date']."||::||".@$request['claim_pending_reason']."||::||".@$request['claim_pending_attachment_mailing_add']."||::||".@$request['claim_pending_filing_limit']."||::||".@$request['claim_pending_appeal_limit']."||::||".@$request['claim_pending_mailing_address']."||::||".@$request['claim_pending_appeal_mailing_address']."||::||".@$request['claim_pending_reference_number']."||::||".@$request['claim_pending_callback_date'];
          }
          } */

        /*
          $claim_id 	= $request['claim_id'];
          $claim_detail 	= Claims::where('claim_number',$claim_id)->selectRaw('id, patient_id')->first();
          $created_by		= Auth::user()->id;
          if($content_val!='' || $follow_up_content!=''){
          PatientNote::create(['title'=>'ArManagement','content'=>$content_val,'follow_up_content'=>$follow_up_content,'notes_type'=>'patient','patient_notes_type'=>$patient_notes_type,'claim_id'=>$claim_detail['id'],'notes_type_id'=>$claim_detail['patient_id'],'created_by'=>$created_by]);
          }

          $claim_armanagement_status = strtolower(str_replace('_',' ',$patient_notes_type));
          $claim_detail->update(['claim_armanagement_status'=>ucwords($claim_armanagement_status)]);
         */
        return Response::json(['content' => $content_val, 'follow_up_content' => $follow_up_content, 'patient_notes_type' => $patient_notes_type,'source_id' => $source_id]);
    }

    public function getclaimstatusfinalnotesaddedApi() {
        $request = Request::all();
		
        /* if (!isset($request['user_id']) || empty($request['user_id'])) {
            return Response::json(['status' => 'failure']);
        } */
        $ar_notes_val = $this->delete_all_between('<style type="text/css">', '</style>', $request['ar_notes_val']);
        $ar_notes_val = trim(htmlspecialchars(addslashes(str_replace("<hr />", "", $ar_notes_val))));
        $claim_id = $request['curr_claim_val'];
        $claim_status_option = @$request['claim_status_option'];
        $user_id = ''; //$request['user_id'];

        $claim_detail = ClaimInfoV1::where('claim_number', $claim_id)->selectRaw('id, patient_id')->first();
        $created_by = Auth::user()->id;

        $notesInfo = PatientNote::create(['title' => 'ArManagement', 'content' => $ar_notes_val, 'follow_up_content' => '', 'notes_type' => 'patient', 'patient_notes_type' => 'followup_notes', 'claim_id' => $claim_detail['id'], 'notes_type_id' => $claim_detail['patient_id'], 'created_by' => $created_by, 'user_id' => $user_id]);
		
		/* Session having ar_source_id only can execu this line */
		if(Session::has('ar_source_id')){
			$source_id = $notesInfo->id;
			PatientNote::where('source_id',Session::get('ar_source_id'))->update(['source_id'=>$source_id]);
			Session::forget('ar_source_id');
		}else{
			\Log::info('ar Source id - Log - : '.Session::get('ar_source_id'));
		}
		

        $claim_armanagement_status = strtolower(str_replace('_', ' ', $claim_status_option));
        $claim_detail->update(['claim_armanagement_status' => ucwords($claim_armanagement_status)]);

        return Response::json(['status' => 'success']);
    }

    public function delete_all_between($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        return str_replace($textToDelete, '', $string);
    }

    public function getclaimchargeeditprocessApi() {
        $request = Request::all();
        if (!empty($request['patient_id']))
            $request['patient_id'] = $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        $request['claim_id'] = ClaimInfoV1::where('claim_number', '=', $request['claim_id'])->value('id');

        if (!empty($request['icd1'])) {
            for ($i = 1; $i <= 12; $i++) {
                $icd_list[$i] = !empty($request['icd' . $i]) ? Icd::getIcdIds($request['icd' . $i]) : '';
            }
            $icd_lists = array_filter($icd_list);
            $request['icd_codes'] = implode(',', $icd_lists);
        }

        $request['hold_reason_id'] = '';
        $request['status'] = 'Ready';
        $request['is_hold'] = '';

        if (isset($request['self']) && @$request['self'] == 1) {
            $request['self_pay'] = 'Yes';
            $request['status'] = 'Patient';
            $request['insurance_id'] = '';
            $request['insurance_category'] = '';
        }
        if (isset($request['insurance_id']) && $request['insurance_id'] != 'self' && $request['insurance_id'] != '') {
            $request['self_pay'] = 'No';
            $insurance_category = isset($request['insurance_category']) ? explode('-', @$request['insurance_category']) : '';
            $request['insurance_category'] = $insurance_category[0];
        }

        $request['doi'] = !empty($request['doi']) ? date('Y-m-d', strtotime($request['doi'])) : '';
        $request['admit_date'] = !empty($request['admit_date']) ? date('Y-m-d', strtotime($request['admit_date'])) : '';
        $request['discharge_date'] = !empty($request['discharge_date']) ? date('Y-m-d', strtotime($request['discharge_date'])) : '';
        //$request['entry_date']	= !empty($request['entry_date']) ?date('Y-m-d', strtotime($request['entry_date'])):'';
        $allowed_total = 0;
        if (!empty($request)) {
            $dos_spt_details = [];
            $unit_cal = 0;
            for ($i = 0; $i < count($request['cpt']); $i++) {
                //if (!empty($request['cpt'][$i]) && Cpt::on('responsive')->where('cpt_hcpcs', $request['cpt'][$i])->count() == 0) {  // CPT backend validation
                if (!empty($request['cpt'][$i]) && Cpt::where('cpt_hcpcs', $request['cpt'][$i])->count() == 0) {  // CPT backend validation
                    return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
                }
                if (!empty($request['cpt'][$i])) {
                    $dos_spt_details[$i]['dos_from'] = date('Y-m-d', strtotime($request['dos_from'][$i]));
                    $dos_spt_details[$i]['dos_to'] = date('Y-m-d', strtotime($request['dos_to'][$i]));
                    $dos_spt_details[$i]['cpt_code'] = $request['cpt'][$i];
                    $dos_spt_details[$i]['modifier1'] = $request['modifier1'][$i];
                    $dos_spt_details[$i]['modifier2'] = $request['modifier2'][$i];
                    $dos_spt_details[$i]['modifier3'] = $request['modifier3'][$i];
                    $dos_spt_details[$i]['modifier4'] = $request['modifier4'][$i];
                    $dos_spt_details[$i]['charge'] = $request['charge'][$i];
                    $dos_spt_details[$i]['cpt_allowed_amt'] = $request['cpt_allowed'][$i];
                    $dos_spt_details[$i]['cpt_billed_amt'] = $request['cpt_amt'][$i];
                    if (!empty($unit) && $unit != 0) {
                        $dos_spt_details[$i]['unit'] = $unit;
                    } else {
                        $dos_spt_details[$i]['unit'] = $request['unit'][$i];
                    }
                    $dos_spt_details[$i]['cpt_icd_code'] = $request['cpt_icd_map'][$i];
                    $dos_spt_details[$i]['cpt_icd_map_key'] = $request['cpt_icd_map_key'][$i];
                    $allowed_total += $request['cpt_allowed'][$i];
                    if (isset($request['ids'][$i]))
                        $dos_spt_details[$i]['id'] = Helpers::getEncodeAndDecodeOfId($request['ids'][$i], 'decode');
                    $dos_spt_details[$i]['patient_id'] = $request['patient_id'];  // Need to check DOS for each claim with patient_id
                    if ($dos_spt_details[0]['dos_from'])
                        $request['date_of_service'] = $dos_spt_details[0]['dos_from']; // To save First record of from to date
                    if ($dos_spt_details[0]['cpt_code'])
                        $request['cpt_codes'] = $dos_spt_details[0]['cpt_code']; // To save First record of from to date						
                }
            }

            unset($request['dos_from'], $request['dos_to'], $request['cpt'], $request['modifier1'], $request['modifier2'], $request['modifier3'], $request['modifier4'], $request['charge'], $request['unit'], $request['cpt_icd_map'], $request['ids']);

            $request['created_by'] = Auth::user()->id;
            $request['total_allowed'] = $allowed_total;
            $request['balance_amt'] = $request['total_charge'];

            if (!empty($request['copay_amt']) && $request['copay_amt'] != 0) {
                PatientBudget::collect_patient_payment($request['patient_id'], $request['copay_amt']); // This is used for budget concept
            }
            $result = ClaimInfoV1::find($request['claim_id']);

            $bill_obj = new BillingApiController();
            // Billed amount change option after patient payment has been done starts
            $remaining = $request['total_charge'] - $result->patient_paid;
            if ($request['total_charge'] > $result->total_charge && $result->patient_paid > 0 || $request['total_charge'] > $result->total_charge && $remaining >= 0) { // when patient paid was done as well as billed amount also changed.
                $request['balance_amt'] = $bill_obj->changeBilledAmountEntryProcess($request);
                if ($remaining == 0) {
                    $request['total_paid'] = DB::raw("total_paid -" . $request['total_charge']);
                    $request['patient_paid'] = DB::raw("patient_paid -" . $request['total_charge']);
                    // @todo check and update new pmt flow
                    //DB::table('claimdoscptdetails')->where('claim_id', $result->id)->where('cpt_code', 'Patient')->update(['patient_paid' => DB::raw('patient_paid - '.abs($remaining))]); // Update CPT line item tables						
                }
            } elseif ($request['total_charge'] < $result->total_charge && $result->patient_paid > 0 && $remaining >= 0) {
                $request['balance_amt'] = $bill_obj->changeBilledAmountEntryProcess($request);
            } elseif ($request['total_charge'] < $result->total_charge && $result->patient_paid > 0 && $remaining < 0) {
                $bill_obj->changeBilledAmountEntryProcess($request);
                $paymentobj = new PaymentApiController();
                $paymentobj->moveAmountToWallet($result->patient_id, $result->id, abs($remaining));
                $request['balance_amt'] = 0;
            } elseif ($request['total_charge'] == $result->total_charge && $result->patient_paid > 0) {
                $request['balance_amt'] = $remaining;
            } else {
                $request['balance_amt'] = $request['total_charge'];
            }
            // Billed amount change option after patient payment has been done ends	
            //dd($request);

            $result->update($request);

            for ($j = 0; $j < count($dos_spt_details); $j++) {
                $dos_spt_details[$j]['claim_id'] = $result->id;
            }

            //Claimdoscptdetail::where('claim_id',$result->id)->forceDelete();  // This was called before submission
            //Claimdoscptdetail::insert($dos_spt_details);
            //Claimdoscptdetail::where('claim_id', $result->id)->update($dos_spt_details); 

            foreach ($dos_spt_details as $dos_spt_detail) {
                if (isset($dos_spt_detail['id']) && $dos_spt_detail['id']) {
                    ClaimCPTInfoV1::where('id', $dos_spt_detail['id'])->update($dos_spt_detail);
                    // After submission only edit comes and we do not delete line item data							
                } else {
                    $dos_spt_detail['claim_id'] = $result->id;
                    ClaimCPTInfoV1::insert($dos_spt_detail);
                }
            }

            // This is to update line item delete on edit process
            if (isset($request['item_ids']) && !empty($request['item_ids'])) {
                $claim_dos_ids = explode(',', $request['item_ids']);
                $claim_id_list = array_map(function($claim_dos_ids) {
                    return Helpers::getEncodeAndDecodeOfId($claim_dos_ids, 'decode');
                }, $claim_dos_ids);
                // @todo - check and replace new pmt flow	
                ClaimCPTInfoV1::whereIn('id', $claim_id_list)->delete();
            }

            if (!empty($result->document_path) && !empty($result->cmsform)) {
                $bill_obj->deleteExistingdocument($result->id);
            }
            $store_arr = $bill_obj->generatecms1500($result->id);
        }

        /*         * * If copay available Payment related code starts here** */
        if ($request['claim_id'] == '' && !empty($request['copay_amt']) || !empty($request['claim_id']) && isset($request['copay']) && $request['copay_amt'] != 0) {
            $payment_data['type'] = 'charge';
            $payment_data['payment_mode'] = $request['copay'];
            $payment_data['payment_amt'] = $request['copay_amt'];
            $payment_data['check_no'] = $request['check_no'];
            $payment_data['card_type'] = $request['card_type'];
            $payment_data['check_date'] = $request['check_date'];
            $payment_data['reference'] = $request['copay_detail'];
            $payment_data['patient_id'] = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'encode');
            $claim_id = Helpers::getEncodeAndDecodeOfId($result->id, 'encode');
            $payment_data['tot_billed_amt'] = $request['total_charge'];
            $payment_data['payment_type'] = 'Payment';
            if (isset($dos_spt_details[0]) && !empty($dos_spt_details[0])) {
                $payment_data['cpt'][0] = $dos_spt_details[0]['cpt_code'];
                $payment_data['dos'][0] = $dos_spt_details[0]['dos_from'];
                $payment_data['cpt_billed_amt'][0] = $request['total_charge'];
                $remaining = $request['total_charge'] - $request['copay_amt'];
                $payment_data['patient_paid'][0] = ($remaining < 0) ? $request['total_charge'] : $request['copay_amt'];
                $payment_data['patient_balance'][0] = ($remaining > 0) ? $remaining : 0;
                $payment_data['balance'][0] = $request['total_charge'];
                $payment_data['paid_amt'][0] = ($remaining < 0) ? $request['total_charge'] : $request['copay_amt'];
                $payment_data['claim_id'][0] = $claim_id;
            }

            $payment = new PatientPaymentApiController();
            $payment->getStoreApi($payment_data);
        }
        /*         * * If copay available Payment related code ends here** */


        /** Store claim notes starts* */
        if (!empty($result->id) && !empty($request['note'])) {
            $bill_obj->savepatientnotes($request['note'], $result->id, $request['patient_id']);
        }
        /** Store claim notes ends* */
        return Response::json(array('status' => 'success'));
    }

    public function getclaimchargeeditdetailsApi($claim_id) {
        $claim_detail = ClaimInfoV1::with(['billing_provider', 'insurance_details', 'facility_detail', 'rendering_provider', 'refering_provider', 'claim_notes_details', 'insurancePaymentTx', 'problem_list', 'eligibility_list', 'patient', 'dosdetails', 'employer_details', 'pos', 'claim_details' => function($query) {
                        $query->select('id', 'claim_id', 'illness_box14', 'box_23');
                    }])->where('claim_number', $claim_id)->get();

        /*         * ***************************************************************************************************************** */
        $rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $referring_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')]);
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $pos = Pos::pluck('code', 'id')->all();
        /*         * ***************************************************************************************************************** */

        $practice_id = 4;
        if (Session::get('practice_dbid'))
            $practice_id = Session::get('practice_dbid');
        $practice_det = Practice::with('speciality_details')->where('id', $practice_id)->first();

        $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
        $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
        $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
        $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
        $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('name', 'id')->all();

        return Response::json(array('status' => 'success', 'claim_detail' => $claim_detail, 'practice_det' => $practice_det, 'user_list' => $user_list, 'rendering_providers' => $rendering_providers, 'referring_providers' => $referring_providers, 'billing_providers' => $billing_providers, 'facilities' => $facilities, 'pos' => $pos));
    }

    public function getdenialsearchlistApi() {
        $request = Request::all();
        $denial_claim_number = $request['claim_number'];
        $denial_search_str = $request['denial_search_str'];
        if ($denial_search_str != '') {
            $search_str_like = " transactioncode_id like '$denial_search_str' or transactioncode_id like '$denial_search_str%' or transactioncode_id like '%$denial_search_str' or transactioncode_id like '%$denial_search_str%' or description like '$denial_search_str' or description like '$denial_search_str%' or description like '%$denial_search_str' or description like '%$denial_search_str%' ";
            $denial_codes_arr = Code::whereRaw("($search_str_like)")->select('id', 'transactioncode_id', 'description')->get();
            //$denial_codes_arr = Code::whereRaw("(codecategory_id=3 and ($search_str_like))")->get();
        } else {
            $denial_codes_arr = Cache::remember('denial_codes_arr', 22 * 60, function() {
                        return Code::select('id', 'transactioncode_id', 'description')->get();
                    });
            //$denial_codes_arr = Code::where('codecategory_id', 3)->get();
        }
        $sel_code_val = $request['sel_code_val'];
        return Response::json(array('status' => 'success', 'data' => compact('denial_claim_number', 'denial_codes_arr', 'sel_code_val')));
    }

    public function claimholdprocessApi() {
        $request = Request::all();
        if (@$request['hold_type'] == 'claim_hold') {
            $claim_id = @$request['hold_id'];
            ClaimInfoV1::where('id', $claim_id)->update(['status' => 'Pending']);
        }
        if (@$request['hold_type'] == 'statement_hold') {
            $patient_id = $request['hold_id'];
            // Update statement hold details
            $update_arr['statements'] = 'Hold';
            if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
            } else {
                $update_arr['hold_release_date'] = "0000-00-00";
            }

            if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                $update_arr['hold_reason'] = $request['hold_reason'];
            } else {
                $update_arr['hold_reason'] = 0;
            }

            Patient::where('id', $patient_id)->update($update_arr);
        }
        return Response::json(array('status' => 'success'));
    }    

	public function setpatientholdApi($patient_id){
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');

        $request = Request::all();
        // Update statement hold details
        $update_arr['statements'] = 'Hold';
        if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
            $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
        } else {
            $update_arr['hold_release_date'] = "0000-00-00";
        }

        if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
            $update_arr['hold_reason'] = $request['hold_reason'];
        } else {
            $update_arr['hold_reason'] = 0;
        }
		$resp = Patient::where('id',$patient_id)->update($update_arr); 
		return Response::json(array('status' => 'success'));
	}  

	public function setpatientunholdApi($patient_id){
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $update_arr['statements'] = 'Yes';
        $update_arr['hold_release_date'] = "0000-00-00";
        $update_arr['hold_reason'] = 0;

		Patient::where('id',$patient_id)->update($update_arr);
        //Update payment hold reason as empty when statement hold released.
        ClaimInfoV1::where('patient_id', $patient_id)->where('payment_hold_reason', 'patient')
                    ->update(['payment_hold_reason' => '']);
		return Response::json(array('status' => 'success'));
	}   
    
    public function getAR_SummaryPageDataApi($patient_id) 
    {    
           $getardata = new ArmanagementApiMainController();      
           $data['claim_status_wise_count'] = $getardata->getArByCountByStatus($patient_id);            
           $data['patient_claims_status'] =  $getardata->getBalanceByStatus($patient_id);
           $data['ins_category_value'] = $getardata->getPatientInsuranceCategoryPayment($patient_id);
           $data['payment_graph'] = $getardata->getInsuranceCategoryPayment($patient_id);
           //dd($data)         ;
           return $data;
     }
	 /**
     * To store claim denail notes in payment posting.
     * Param: claim_id, denial_date, check_no, denial_insurance, reference, denial_codes
     *	For Examp: 	denial_date: 04/10/2018
					check_no: 546y567
					denial_insurance: 1
					reference: '' 
					denial_codes[]: 1
					claim_id: 00004
     */
	 public function storeARClaimDenailNotes($data){
		try {
			$claim_id = $data['claim_id'];
			$claim_detail = ClaimInfoV1::where('id', $claim_id)->selectRaw('id, patient_id, claim_number, DATE_FORMAT(date_of_service, "%d-%l-%Y") as date_of_service')->first();
			$created_by = Auth::user()->id;
			$created_name = Auth::user()->name;
			$date_of_service = $claim_detail['date_of_service'];
			$created_date = date('d M Y', strtotime(date('Y-m-d H:i:s')));
			$content = $data['denial_date'] . "^^^" . $data['check_no'] . "^^^" . $data['denial_insurance'] . "^^^" . $data['reference'] . "^^^" . implode(",", $data['denial_codes']);
			$denial_code_result = array();
			foreach ($data['denial_codes'] as $denial_code_value) {
				$denial_code_result[$denial_code_value] = "A0 	Patient refund amount";
			}
			PatientNote::create(['title' => 'ArManagement', 'content' => $content, 'notes_type' => 'patient', 'patient_notes_type' => 'claim_denial_notes', 'claim_id' => $claim_detail['id'], 'notes_type_id' => $claim_detail['patient_id'], 'created_by' => $created_by]);
			return Response::json(array('status' => 'error','msg'=>'successfully added'));
		}catch (Exception $e) {
			return Response::json(array('status' => 'error','msg'=>$e->getMessage()));
		 }
		
	}
 /* 
 * Author : Selvakumar V
 * Desc : Showing the followup history in popup
 * Created On : 25-Apr-2018
 */	
	public function getFolloupHistoryPopupApi($claim_no){
		$claim_detail = ClaimInfoV1::with(['claim_notes_details' => function($query2) {
                        $query2->orderBy('id', 'desc');
                    }])->where('claim_number', $claim_no)->get();
		return Response::json(array('status' => 'success','data'=>$claim_detail));
	}   

	public function getaddedfollowupdetailsApi(){
		$request = Request::all();
		$id = $request['id'];
		$patientInfo = PatientNote::where('source_id',$id)->get()->first();
		if(isset($patientInfo->status) && $patientInfo->status == 'Hidden'){
			PatientNote::where('source_id',$id)->update(['status' => 'Active']);
			return Response::json(array('status' => 'successfully converted claim note'));
		}else{
			return Response::json(array('status' => 'Already converted claim note'));
		}
	
	}

}
