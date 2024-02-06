<?php

namespace App\Http\Controllers\Patients\Api;

use App\Models\Template as Template;
use App\Models\Templatetype as Templatetype;
use App\Models\Templatepairs as Templatepairs;
use App\Models\Insurance as Insurance;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Patients\PatientInsurance;
use App\Models\Patients\PatientCorrespondence;
use App\Models\Practice as Practice;
use App\Models\Patients\Patient;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\PatientContact as PatientContact;
use App\Models\Patients\PatientBudget;
use App\Models\Payments\ClaimInfoV1;
use Response;
use Request;
use Validator;
use Auth;
use DB;
use Lang;
use Session;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Traits\ClaimUtil;

class CorrespondenceApiController extends Controller {
    use ClaimUtil;

    /*     * * Correspondence list page start ** */

    public function getindexApi($patient_id = '', $export = '') {
        $id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $patients = Patient::where('id', $id)->first();
            $patient_correspondence = PatientCorrespondence::with('creator', 'insurance', 'template_detail')->where('patient_id', $id)->get();
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patients', 'patient_correspondence')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Correspondence list page end ** */

    /*     * * Correspondence create page start ** */

    public function getCreateApi($patient_id, $template_id) {
        $pat_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $temp_id = Helpers::getEncodeAndDecodeOfId($template_id, 'decode');
        if (Patient::where('id', $pat_id)->count() > 0 && is_numeric($pat_id)) {
            $patients = Patient::where('id', $pat_id)->first()->toArray();
            if (Template::where('id', $temp_id)->count() > 0 && is_numeric($temp_id)) {
                $template = Template::where('id', $temp_id);
                $template_details = $template->first();
                $template_content = $template_details->content;
                preg_match_all('/\##VAR-(.*?)\##/', $template_content, $match);
                $template_variable = $match[0];

                $template_used_variable = array_unique($template_variable);

                $all_pair_variable = Templatepairs::pluck("value", "key")->all();
                $selected_variable = Templatepairs::whereIn('value', $template_used_variable)->pluck("value", "key")->all();
                $set_input_col = Templatepairs::whereIn('value', $template_used_variable)->whereIn("input_types", ["input", "select"])->pluck("value", "key")->all();
                $temp_pair_values = $this->tempPairValues($patients);
                $temp_pair_values['subject'] = '';
                $get_array_list = $temp_pair_values["all_array"];
                unset($temp_pair_values["all_array"]);
                $pair_values = $this->getPairValues($selected_variable, $temp_pair_values);

                $content = strtr($template_content, $pair_values);
                $template_details->content = $content;
                preg_match_all('/\##VAR-(.*?)\##/', $content, $match);
                $remain_variable = $match[0];
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('template_details', 'set_input_col', 'get_array_list', 'temp_pair_values', "all_pair_variable")));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Correspondence create page end ** */

    /*     * * Get Template pair values function start ** */

    public function tempPairValues($patients_detail) {
        $pair_value_arr = array();
        $pat = $patients_detail;        
        $pat_id = $pat['id'];
        $practice = Practice::where('id', Session::get("practice_dbid"))->first();
        $insurances_addr = PatientInsurance::with("insurance_details")->where('patient_id', $pat_id)->get();
        $insurances_addr = (count($insurances_addr) > 0) ? $insurances_addr->toArray() : [];
        $claim_detail = ClaimInfoV1::where('patient_id', $pat_id); //->get()->toArray()
        $claim_arr = ClaimInfoV1::where('patient_id', $pat_id)->pluck('claim_number', 'id')->all();
        $budget_balance = PatientBudget::where('patient_id', $pat_id)->pluck('budget_balance')->first();
        $contact_detail = PatientContact::where("patient_id", $pat_id)->where("category", "Guarantor")->first();
        $contact_detail= (isset($contact_detail) || !empty($contact_detail))? $contact_detail->toArray() : "" ;
        // $contact_detail = ($contact_detail_count > 0) ? $contact_detail->toArray() : '';
        $pair_value_arr["guarantorname"] = $pair_value_arr["patientname"] = Helpers::getNameformat($pat['last_name'], $pat['first_name'], $pat['middle_name']);
        $pair_value_arr["guarantoraddress"] = $pair_value_arr["patientaddress"] = $this->getAddrFormat($pat['address1'], $pat['city'], $pat['state'], $pat['zip5'], $pat['zip4']);
        if ($contact_detail != '') {
            $gur_name = Helpers::getNameformat($contact_detail['guarantor_last_name'], $contact_detail['guarantor_first_name'], $contact_detail['guarantor_middle_name']);
            $pair_value_arr["guarantorname"] = ($gur_name != '') ? $gur_name : $pair_value_arr["patientname"];
            $gur_addr = $this->getAddrFormat($contact_detail['guarantor_address1'], $contact_detail['guarantor_city'], $contact_detail['guarantor_state'], $contact_detail['guarantor_zip5'], $contact_detail['guarantor_zip4']);
            $pair_value_arr["guarantoraddress"] = ($gur_addr != '') ? $gur_addr : $pair_value_arr["patientaddress"];
        }
        $pair_value_arr["guarantorname"] = ($pair_value_arr["guarantorname"] != '') ? $pair_value_arr["guarantorname"] : $pair_value_arr["patientname"];
        $pair_value_arr["accountnumber"] = $pat['account_no'];
        $insurance_addr = $policy_id = $patient_insurances = [];        
        foreach ($insurances_addr as $addr_key => $addr_val) {        
            $address1= @$addr_val['insurance_details']['address_1'];           
            $city= @$addr_val['insurance_details']['city'];
            $state= @$addr_val['insurance_details']['state'];
            $zipcode5= @$addr_val['insurance_details']['zipcode5'];
            $zipcode4= @$addr_val['insurance_details']['zipcode4'];
            $insurance_addr[$addr_val['insurance_id']] = $this->getAddrFormat($address1, $city, $state, $zipcode5, $zipcode4);
            $policy_id[$addr_val['insurance_id']] = $addr_val['policy_id'];
            $patient_insurances[$addr_val['insurance_id']] = $addr_val["insurance_details"]["insurance_name"];
        }       
        $arr["insurancename"] = $patient_insurances;
        $arr["insuranceaddress"] = $insurance_addr;
        $arr["policyid"] = $policy_id;
        $arr["claims"] = $claim_arr;
        $pair_value_arr["date"] = '';        
        $total_bal = $this->getPatientARDue($pat_id);//$claim_detail->sum("balance_amt");        
        $pat_bal = $this->getPatientDue($pat_id); //$claim_detail->sum("patient_due");
        $ins_bal = $this->getPatientInsuranceDue($pat_id); //$claim_detail->sum("insurance_due");
        $pair_value_arr["totalbalance"] = ($total_bal == '') ? "0.00" : $total_bal;
        $pair_value_arr["patientbalance"] = ($pat_bal == '') ? "0.00" : $pat_bal;
        $pair_value_arr["insurancebalance"] = ($ins_bal == '') ? "0.00" : $ins_bal;
        $pair_value_arr["paybydate"] = '';
        $pair_value_arr["to"] = $pat['email'];
        $pair_value_arr["emailaddress"] = '';

        $pair_value_arr["budgetbalance"] = ($budget_balance == '') ? "0.00" : $budget_balance;
        $pair_value_arr["practicename"] = $practice['practice_name'];
        $pair_value_arr["practiceaddress"] = $this->getAddrFormat($practice['mail_add_1'], $practice['mail_city'], $practice['mail_state'], $practice['mail_zip5'], $practice['mail_zip4']);
        $rend_pro = $claim_detail->with("rendering_provider")->get()->toArray();
        $provider = $npi = $ein = [];
        foreach ($rend_pro as $rend_key => $rend_val) {
            $rend_key = $rend_val['rendering_provider_id'];
            if ($rend_key > 0) {
                $assign_val = $rend_val["rendering_provider"];
                $provider[$assign_val['id']] = $assign_val['provider_name'];
                $npi[$assign_val['id']] = $assign_val['npi'];
                $ein[$assign_val['id']] = $assign_val['etin_type_number'];
            }
        }
        $arr["renderingprovider"] = $provider;
        $arr["npi"] = $npi;
        $arr["ein"] = $ein;
        $pair_value_arr["practicephonenumber"] = $practice['phone'];
        $pair_value_arr["practicefaxnumber"] = $practice['fax'];
        $pair_value_arr["all_array"] = $arr;
        return $pair_value_arr;
    }

    /*     * * Get Template pair values function end ** */

    /*     * * Get selected pair values function start ** */

    public function getPairValues($selected_variable, $temp_pair_values) {
        $result = [];
        foreach ($selected_variable as $var_key => $var_val) {
            $result[$var_val] = (isset($temp_pair_values[$var_key]) && $temp_pair_values[$var_key] != '') ? $temp_pair_values[$var_key] : $var_val;
        }
        return $result;
    }

    /*     * * Get selected pair values function end ** */

    /*     * * Get Address Format function start ** */

    public function getAddrFormat($addr1, $city, $state, $zip5, $zip4) {
        $zip4 = ($zip4 == '') ? '' : '-' . $zip4;
        $addr = $addr1 . ",\n<br>" . $city . ", " . $state . ", " . $zip5 . $zip4;
        return $addr;
    }

    /*     * * Get Address Format function end ** */

    /*     * * Correspondence mail send page start ** */

    public function getSendApi($id, $request = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        if (Patient::where('id', $id)->count() > 0 && is_numeric($id) && $request != '') {
            $user = Auth::user()->id;
            $request = Request::all();
            preg_match_all('/\##VAR-(.*?)\##/', $request['message'], $match);
            $empty_variable = $match[0];
            if (count($empty_variable) == 0) {
                $request['patient_id'] = $id;
                $request['dos'] = (isset($request['dosto']) && !empty($request['dosto'])) ? date('Y-m-d', strtotime($request['dosto'])) : (isset($request['dosfrom']) && !empty($request['dosfrom'])) ? date('Y-m-d', strtotime($request['dosfrom'])) : "";
                $data = PatientCorrespondence::create($request);
                $data->created_by = $user;
                $data->save();
                $res['msg'] = $request['message'];
                $res['email'] = $request['email_id'];
                $res['subject'] = $request['subject'];
                $patients = Patient::where('id', $id)->first();
                $patient_name = Helpers::getNameformat($patients->last_name, $patients->first_name, $patients->middle_name);
                $res['name'] = $patient_name;
                $email = CommonApiController::connectEmailApi($res);
                $corr_id = Helpers::getEncodeAndDecodeOfId($data->id, 'encode');
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.mail_send_msg"), 'data' => $corr_id));
            } else {
                $var = implode(", ", $empty_variable);
                return Response::json(array('status' => 'error', 'message' => '', 'data' => $var));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Correspondence mail send page end ** */

    /*     * * Correspondence mail show page start ** */

    public function getshowApi($patient_id, $id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $patient_id)->count() > 0 && is_numeric($patient_id)) {
            if (PatientCorrespondence::where('id', $id)->count() > 0 && is_numeric($id)) {
                $content = PatientCorrespondence::with('creator')->where('id', $id)->where('patient_id', $patient_id)->get();
                $patients = Patient::where('id', $patient_id)->first();
                return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('content', 'patients')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Correspondence mail show page end ** */

    /*     * * Template list page start ** */

    public function gettemplateListApi($patient_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (Patient::where('id', $patient_id)->count() > 0 && is_numeric($patient_id)) {
            $temp_arr = array("Benefit Verifications", "App");
            $templates = Template::with('creator', 'templatetype')->whereHas('templatetype', function($q)use($temp_arr) {
                        $q->whereNotIn('templatetypes', $temp_arr);
                    })->where('status', 'Active')->orderBy('id', 'ASC')->get();
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('templates')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Templatelist page end ** */

    function __destruct() {
        
    }

}
