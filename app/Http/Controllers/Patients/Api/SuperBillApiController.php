<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientInsurance;
use App\Models\Patients\SuperbillExistingIcd;
use App\Models\Patients\SuperbillExistingCpt;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Provider;
use App\Http\Controllers\Patients\Api\BillingApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Icd as Icd;
use App\Models\Cpt as Cpt;
use App\Models\SuperbillTemplate as Superbill;
use App\Models\Modifier as Modifier;
use Auth;
use Response;
use Config;
use Request;
use Validator;
use DB;
use Lang;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Cpt as AdminCpt;
use App\Models\Favouritecpts as Favouritecpts;

class SuperBillApiController extends Controller {

    public function getCreateApi($patient_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if ((isset($patient_id) && is_numeric($patient_id)) && (Patient::where('id', $patient_id)->count()) > 0) {
            $database_name = 'responsive';
            $existing_icd_details = SuperbillExistingIcd::where('patient_id', $patient_id)->pluck('icd_ids')->all();
            $existing_icds_arr = Icd::on("responsive")->whereIn('id', explode(",", $existing_icd_details))->select('id', 'icd_code', 'short_description')->get();
            $existing_cpt_details = SuperbillExistingCpt::where('patient_id', $patient_id)->pluck('cpt_ids')->all();
            $existing_cpts_arr = Cpt::on("responsive")->whereIn('id', explode(",", $existing_cpt_details))->select('id', 'cpt_hcpcs', 'short_description')->get();
            $providers = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', '=', 'Active')->where('p.deleted_at', NULL)->where('p.provider_types_id', config('siteconfigs.providertype.Rendering'))->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();

            $claims_list = ClaimInfoV1::with('billing_provider', 'rendering_provider')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();

            $insurance_arr = PatientInsurance::with('insurance_details')->where('patient_id', $patient_id)->where('category', 'Primary')->first();
            $patient_detail = Patient::findOrFail($patient_id); /* Patients details */
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_detail', 'providers', 'existing_icds_arr', 'existing_cpts_arr', 'claims_list', 'insurance_arr')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    public function get_seleted_icd_details($icd_code) {
        $database_name = 'responsive';
        $icd_details = Icd::on("responsive")->where('id', $icd_code)->select('id', 'icd_code', 'short_description')->get();
        $icd_details = json_decode(json_encode($icd_details), true);
        return Response::json(array('icd_details' => $icd_details[0]));
    }

    public function get_seleted_cpt_details($cpt_icd_codes) {

        $icd_ids_details = "";
        $cpt_icd_codes = explode("::", $cpt_icd_codes);
        $cpt_code = $cpt_icd_codes[0];
        $icd_code = $cpt_icd_codes[1];
        $database_name = 'responsive';
        $cpt_details = Cpt::on("responsive")->where('id', $cpt_code)->select('id', 'cpt_hcpcs', 'short_description')->get();
        $cpt_details = json_decode(json_encode($cpt_details), true);

        if ($icd_code != 'no') {
            $icd_ids = explode(",", trim($icd_code, ","));
            $icd_ids_arr = Icd::on("responsive")->wherein('id', $icd_ids)->select('id', 'icd_code', 'short_description')->get();
            $icd_ids_arr = json_decode(json_encode($icd_ids_arr), true);
            $icd_array = array();

            foreach ($icd_ids_arr as $k2 => $v2) {
                $icd_array[$v2['id']] = array('icd_code' => $v2['icd_code'], 'short_description' => $v2['short_description']);
            }

            foreach ($icd_ids as $k3 => $v4) {
                $icd_ids_details .= substr($icd_array[$v4]['short_description'], 0, 8) . " - (" . $icd_array[$v4]['icd_code'] . "),&nbsp;&nbsp;&nbsp;";
            }
            $icd_ids_details = trim($icd_ids_details, ",&nbsp;&nbsp;&nbsp;");
        }

        return Response::json(array('cpt_details' => $cpt_details[0], 'icd_ids_details' => $icd_ids_details));
    }

    public function superbillformvalidation($tab_type) {
        $request = Request::all();
        $current_date = date('m/d/Y');
        $errors = $no_errors = array();
        $icd_tab_error = $cpt_tab_error = $claim_tab_error = "";

        if ($request['date_of_service'] == "") {
            $errors[] = 'date_of_service_err';
            $icd_tab_error = "yes";
        } else {
            if (preg_match("^\\d{1,2}/\\d{2}/\\d{4}^", $request['date_of_service'])) {
                if (strtotime($current_date) < strtotime($request['date_of_service'])) {
                    $errors[] = 'date_of_service_err';
                    $icd_tab_error = "yes";
                } else {
                    $no_errors[] = 'date_of_service_err';
                }
            } else {
                $errors[] = 'date_of_service_err';
                $icd_tab_error = "yes";
            }
        }

        if ($request['providers_id'] == "") {
            $errors[] = 'providers_id_err';
            $icd_tab_error = "yes";
        } else {
            $no_errors[] = 'providers_id_err';
        }

        if ($request['selected_codes_ids_arr'] == "") {
            $errors[] = 'selected_codes_ids_arr_err';
            $icd_tab_error = "yes";
        } else {
            $no_errors[] = 'selected_codes_ids_arr_err';
        }

        if ($tab_type != "icd_tab") {
            if ($request['selected_codes_cpts_arr'] == "") {
                $errors[] = 'selected_codes_cpts_arr_err';
                $cpt_tab_error = "yes";
            } else {
                $no_errors[] = 'selected_codes_cpts_arr_err';
            }
        } else {
            $cpt_tab_error = "";
            $no_errors[] = 'selected_codes_cpts_arr_err';
        }

        return Response::json(array('tab_type' => $tab_type, 'errors' => $errors, 'no_errors' => $no_errors, 'icd_tab_error' => $icd_tab_error, 'cpt_tab_error' => $cpt_tab_error, 'claim_tab_error' => $claim_tab_error));
    }

    public function superbill_getseletedproviderdetails($provider_id) {
        $request = Request::all();
        $provider_details = Provider::with('degrees')->where('id', $provider_id)->first();
        $superbill_list = Superbill::where('provider_id', $provider_id)->where('status', 'Active')->orderBy('template_name', 'ASC')->pluck('template_name', 'id')->all();
        $superbill_list[0] = "-- Select --";
        $icd_popup_ids = explode(",", $request['selected_codes_ids_arr']);
        $database_name = 'responsive';
        $icd_popup_list = Icd::on("responsive")->wherein('id', $icd_popup_ids)->select('id', 'icd_code', 'short_description')->get();

        return Response::json(array('provider_id' => $provider_id, 'provider_details' => $provider_details, 'superbill_list' => $superbill_list, 'icd_popup_list' => $icd_popup_list));
    }

    public function superbill_getseletedtemplatedetails($template_id, $sel_cpts_vals) {

        $superbill_arr = Superbill::find($template_id)->toArray();
        $superbill_arr['get_list_order'] = explode(",", $superbill_arr['get_list_order']);

        if ($superbill_arr["skin_procedures_units"] != '')
            $superbill_arr["skin_procedures_units"] = explode(",", $superbill_arr["skin_procedures_units"]);
        if ($superbill_arr["medications_units"] != '')
            $superbill_arr["medications_units"] = explode(",", $superbill_arr["medications_units"]);

        $database_name = 'responsive';
        foreach ($superbill_arr['get_list_order'] as $key => $list_value) {
            $superbill_arr[$list_value] = explode(",", $superbill_arr[$list_value]);
            $cpt_list = Cpt::on("responsive")->whereIn('cpt_hcpcs', $superbill_arr[$list_value])->select('id', 'short_description', 'cpt_hcpcs')->groupBy('cpt_hcpcs')->orderBy('short_description', 'ASC')->get()->toArray();
            $superbill_arr[$list_value] = $cpt_list;
        }
        $superbill_name = $superbill_arr['template_name'];
        if ($sel_cpts_vals != 'no') {
            $sel_cpts_vals = explode(",", $sel_cpts_vals);
        } else {
            $sel_cpts_vals = array();
        }
        return view('patients/superbill/template', compact('superbill_arr', 'superbill_name', 'sel_cpts_vals'));
    }

    public function superbill_getcreatebilltab_details() {
        $request = Request::all();
        $max_count_icds_for_cpt = 0;

        $database_name = 'responsive';
        $bill_icd_list = array();
        $icd_ids = explode(",", $request['selected_codes_ids_arr']);
        $icd_list = Icd::on("responsive")->wherein('id', $icd_ids)->select('id', 'icd_code', 'short_description')->get();
        $icd_list = json_decode(json_encode($icd_list), true);
        $icd_array = array();
        foreach ($icd_list as $k => $v) {
            $icd_array[$v['id']] = array('icd_code' => $v['icd_code'], 'short_description' => $v['short_description']);
        }

        foreach ($icd_ids as $k1 => $v1) {
            $bill_icd_list[$v1] = array('icd_code' => $icd_array[$v1]['icd_code'], 'short_description' => $icd_array[$v1]['short_description']);
        }

        $bill_cpt_list = array();
        $cpt_ids = explode(",", $request['selected_codes_cpts_arr']);
        $cpt_list = Cpt::on("responsive")->wherein('id', $cpt_ids)->select('id', 'cpt_hcpcs', 'short_description')->get();
        $cpt_list = json_decode(json_encode($cpt_list), true);

        $cpt_array = array();
        foreach ($cpt_list as $k2 => $v2) {
            $cpt_array[$v2['id']] = array('cpt_hcpcs' => $v2['cpt_hcpcs'], 'short_description' => $v2['short_description']);
        }

        $cpt_icd_display_arr = array();
        $cpt_icd_display_new_arr = array();
        foreach ($cpt_ids as $k3 => $v4) {
            $bill_cpt_list[$v4] = array('cpt_hcpcs' => $cpt_array[$v4]['cpt_hcpcs'], 'short_description' => $cpt_array[$v4]['short_description']);

            $icd_for_cpt_tmp_arr = explode(",", $request['icd_for_cpt_' . $v4]);
            if ($max_count_icds_for_cpt < count($icd_for_cpt_tmp_arr)) {
                $max_count_icds_for_cpt = count($icd_for_cpt_tmp_arr);
            }

            $cpt_icd_display_arr[$cpt_array[$v4]['cpt_hcpcs']] = Icd::on("responsive")->wherein('id', $icd_for_cpt_tmp_arr)->pluck('icd_code', 'id')->all();

            foreach ($icd_for_cpt_tmp_arr as $tmp_value) {
                $cpt_icd_display_new_arr[$cpt_array[$v4]['cpt_hcpcs']][$tmp_value] = $cpt_icd_display_arr[$cpt_array[$v4]['cpt_hcpcs']][$tmp_value];
            }
        }
        $modifier = Modifier::where('status', 'Active')->pluck('code', 'id')->all();
        return view('patients/superbill/create_bill_main_list', compact('modifier', 'bill_icd_list', 'bill_cpt_list', 'max_count_icds_for_cpt', 'cpt_icd_display_arr', 'cpt_icd_display_new_arr'));
    }

    public function getStoreApi() {
        $request = Request::all();
        // / dd($request);
        $patient_id = $request['patient_id'];
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $ins_datas = array();
        $ins_datas['patient_id'] = $patient_id;
        $ins_datas['rendering_provider_id'] = $request['providers_id']; // To remove provider id field from database doneby revathi on 8/7/2016
        $ins_datas['template_id'] = $request['templates_id'];
        $ins_datas['date_of_service'] = date('Y-m-d', strtotime($request['date_of_service']));
        $ins_datas['icd_codes'] = $request['selected_codes_ids_arr'];
        $ins_datas['cpt_codes'] = $request['selected_codes_cpts_arr'];
        $ins_datas['cpt_codes_icd'] = "";
        $ins_datas['charge_add_type'] = "esuperbill";
        $cpt_codes_arr = explode(",", $request['selected_codes_cpts_arr']);
        foreach ($cpt_codes_arr as $cpt_ids_val) {
            $ins_datas['cpt_codes_icd'] .= $request['icd_for_cpt_' . $cpt_ids_val] . "::";
        }
        $ins_datas['cpt_codes_icd'] = trim($ins_datas['cpt_codes_icd'], "::");
        $ins_datas['notes'] = $request['superbill_note'];
        $result = ClaimInfoV1::create($ins_datas);

        $existing_icd_count = SuperbillExistingIcd::where('patient_id', $patient_id)->count();
        $existing_cpt_count = SuperbillExistingCpt::where('patient_id', $patient_id)->count();
        if ($existing_icd_count > 0) {
            $existing_icd_details = SuperbillExistingIcd::where('patient_id', $patient_id)->select('icd_ids')->first();
            $existing_icd_details = json_decode(json_encode($existing_icd_details), true);
            $existing_icd_details_arr = explode(",", $existing_icd_details['icd_ids']);
            $current_icd_details_arr = explode(",", $request['selected_codes_ids_arr']);
            $update_icd_details_arr = array_merge($current_icd_details_arr, $existing_icd_details_arr);
            $update_icd_details_arr = array_unique($update_icd_details_arr);
            $update_icd_details = implode(",", $update_icd_details_arr);
            SuperbillExistingIcd::where('patient_id', $patient_id)->update(['icd_ids' => $update_icd_details]);
        } else {
            $ins_datas1 = array();
            $ins_datas1['patient_id'] = $patient_id;
            $ins_datas1['icd_ids'] = $request['selected_codes_ids_arr'];
            SuperbillExistingIcd::create($ins_datas1);
        }
        if ($existing_cpt_count > 0) {
            $existing_cpt_details = SuperbillExistingCpt::where('patient_id', $patient_id)->select('cpt_ids')->first();
            $existing_cpt_details = json_decode(json_encode($existing_cpt_details), true);
            $existing_cpt_details_arr = explode(",", $existing_cpt_details['cpt_ids']);
            $current_cpt_details_arr = explode(",", $request['selected_codes_cpts_arr']);
            $update_cpt_details_arr = array_merge($current_cpt_details_arr, $existing_cpt_details_arr);
            $update_cpt_details_arr = array_unique($update_cpt_details_arr);
            $update_cpt_details = implode(",", $update_cpt_details_arr);
            SuperbillExistingCpt::where('patient_id', $patient_id)->update(['cpt_ids' => $update_cpt_details]);
        } else {
            $ins_datas2 = array();
            $ins_datas2['patient_id'] = $patient_id;
            $ins_datas2['cpt_ids'] = $request['selected_codes_cpts_arr'];
            SuperbillExistingCpt::create($ins_datas2);
        }
        $billed_total = 0;
        // Done by revathi on May 06 2016 to insert record into claimsdoscptdetails
        if (!empty($request['selected_codes_cpts_arr'])) {
            $exploded = explode(',', $request['selected_codes_ids_arr']);
            $exploded_cpts = explode(',', $request['selected_codes_cpts_arr']);
            $exploded_icd = array_combine(range(1, count($exploded)), $exploded);
            for ($i = 0; $i < count($exploded_cpts); $i++) {
                $cpt_id = $exploded_cpts[$i];
                $icd_ids = $request['icd_for_cpt_' . $cpt_id];
                $icd_list = Icd::getIcdValuelists($icd_ids);
                $dos_spt_details[$i]['dos_from'] = date('Y-m-d', strtotime($request['date_of_service']));
                $dos_spt_details[$i]['dos_to'] = date('Y-m-d', strtotime($request['date_of_service']));
                $dos_spt_details[$i]['cpt_code'] = AdminCpt::where('id', $cpt_id)->pluck('cpt_hcpcs')->first();
                $dos_spt_details[$i]['claim_id'] = $result->id;
                $dos_spt_details[$i]['unit'] = $request['unit'][$i];
                $dos_spt_details[$i]['modifier1'] = $request['modifier1'][$i];
                $dos_spt_details[$i]['modifier2'] = $request['modifier2'][$i];
                $dos_spt_details[$i]['charge'] = $request['charge'][$i];
                $dos_spt_details[$i]['patient_id'] = $patient_id;
                $billed_total += !(empty($request['charge'][$i])) ? $request['charge'][$i] : 0;
                $find = array_values($exploded_icd);
                $replace = array_keys($exploded_icd);
                $dos_spt_details[$i]['cpt_icd_map_key'] = str_ireplace($find, $replace, $icd_ids);
                $dos_spt_details[$i]['cpt_icd_code'] = implode(',', $icd_list);
            }
        }
        // @todo - check and replace new pmt
        ClaimCPTInfoV1::insert($dos_spt_details);
        //Update claim number Starts
        $superbill = new BillingApiController();
        $claim_number = $superbill->generateclaimnumber('Ebill', $result->id);
        $result->update(['claim_number' => $claim_number, 'total_charge' => $billed_total]);
        //Update claim number Ends		
        if (isset($request['fromedit']) && $request['fromedit'] == 1)
            $dos_spt_details[$i]['id'] = Helpers::getEncodeAndDecodeOfId($request['ids'][$i], 'decode');
        $dos_spt_details[$i]['patient_id'] = $request['patient_id'];  // Need to check DOS for each claim with patient_id
        if ($dos_spt_details[0]['dos_from'])
            $request['date_of_service'] = $dos_spt_details[0]['dos_from']; // To save First record of from to date
        if ($dos_spt_details[0]['cpt_code'])
            $request['cpt_codes'] = $dos_spt_details[0]['cpt_code']; // To save First record of from to date

        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');

        return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'patient_id' => $patient_id));
    }

    public function get_superbill_search_icd_cpt_list() {
        /*         * * Get search details starts ** */
        $request = Request::all();
        $keyword = $request['search_keyword'];
        $search_from = isset($request['search_from']) ? $request['search_from'] : '';
        $search_for = "imo_" . $request['from'];
        $database_name = 'responsive';

        /*         * * Get search details end ** */

        /*         * * IMO or DB Search function starts ** */
        $search_from = config('siteconfigs.icdcptsearch.type'); //get site config variable
        if ($search_from == "imo") {
            ### IMO Search function starts ###
            $advanced_api = ApiConfig::where('api_for', $search_for)->where('api_status', 'Active')->first();
            $get_cpt_API = DBConnectionController::getUserAPIIds('imo_cpt');
            $get_icd_API = DBConnectionController::getUserAPIIds('imo_icd');
            if ($advanced_api && ($get_cpt_API == 1 || $get_icd_API == 1)) {
                $result = $this->getAdvancedResult($advanced_api, $keyword, 20);
                if (preg_match("/Request Denied/", $result, $matches)) {
                    $error_message = explode(":", $result);
                    /* if($request['from']=='icd') {
                      $database_name	= 'responsive';
                      $search = $request['search_keyword'];
                      $sel_icds_arr = Icd::on($database_name)->where('icd_code', $search)->select('id','icd_code AS ICD10CM_CODE', 'short_description AS ICD10CM_TITLE')->first()->toArray();
                      $imo_icd_list = array();
                      $i = count($sel_icds_arr);
                      foreach($sel_icds_arr as $key=>$value){
                      $imo_icd_list['@attributes'][$key] = $value;
                      }
                      return view ('patients/superbill/icd_admin_search_part', compact('imo_icd_list','sel_icds_arr'));
                      } */
                    return $error_message[1];
                } else {
                    $array = json_decode(json_encode((array) simplexml_load_string($result)), 1);
                    if (isset($array['item'])) {
                        if (count($array['item']) === 1) {
                            $tmp_arr = $array['item'];
                            unset($array['item']);
                            $array['item'][0] = $tmp_arr;
                        }
                        if ($request['from'] == 'icd') {
                            $sel_icds_arr = array();
                            $imo_icd_list = $array['item'];
                            if ($request['sel_icds'] != '') {
                                $sel_icds = explode(",", $request['sel_icds']);
                                $sel_icds_arr = Icd::on("responsive")->wherein('id', $sel_icds)->pluck('icd_code', 'id')->all();
                            }
                            if (!empty($search_from) && $search_from == "charge")
                                return view('patients/billing/icd_chrage_imo_search_part', compact('imo_icd_list', 'sel_icds_arr'));
                            else
                                return view('patients/superbill/icd_imo_search_part', compact('imo_icd_list', 'sel_icds_arr'));
                        } elseif ($request['from'] == 'cpt') {
                            $imo_cpt_list = $array['item'];
                            $sel_cpts_arr = array();
                            if ($request['sel_cpts'] != '') {
                                $sel_cpts = explode(",", $request['sel_cpts']);
                                $sel_cpts_arr = Cpt::on("responsive")->wherein('id', $sel_cpts)->pluck('cpt_hcpcs', 'id')->all();
                            }
                            return view('patients/superbill/cpt_imo_search_part', compact('imo_cpt_list', 'sel_cpts_arr'));
                        }
                    } else
                        return Lang::get("common.validation.no_record");
                }
            }
            else {
                return Lang::get("common.validation.no_credentials_msg");
            }
            ### IMO Search function end ###
        } else {
            ### DB Search function starts ###
            if ($request['from'] == 'icd') {

                if (strpos($keyword, '@') !== false) {
                    $keyword = str_replace('@', '', $keyword);
                    $sel_icds_arr = Icd::where('icd_code', 'LIKE', $keyword . '%')                                
                                ->select('id', 'icd_code AS ICD10CM_CODE', 'short_description AS ICD10CM_TITLE')
                                ->take(20)->get()->toArray();
                } else {
                    $sel_icds_arr = Icd::where('icd_code', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('short_description', 'LIKE', '%' . $keyword . '%')
                                ->select('id', 'icd_code AS ICD10CM_CODE', 'short_description AS ICD10CM_TITLE')
                                ->take(20)->get()->toArray();    
                }    
                
                $imo_icd_list = array();
                foreach ($sel_icds_arr as $key => $value) {
                    $imo_icd_list[$key]['@attributes'] = $value;
                }
                if (!empty($search_from) && $search_from == "charge")
                    return view('patients/billing/icd_chrage_imo_search_part', compact('imo_icd_list', 'sel_icds_arr'));
                else
                    return view('patients/superbill/icd_imo_search_part', compact('imo_icd_list', 'sel_icds_arr'));
            } elseif ($request['from'] == 'cpt') {
                $imo_cpt_list = $this->search_cpt_from_favorite($keyword);
                $sel_cpts_arr = array();
                $search_type = "dbsearch";
                if ($request['sel_cpts'] != '') {
                    $sel_cpts = explode(",", $request['sel_cpts']);
                    $sel_cpts_arr = Cpt::on("responsive")->wherein('id', $sel_cpts)->pluck('cpt_hcpcs', 'id')->all();
                }
                return view('patients/superbill/cpt_imo_search_part', compact('imo_cpt_list', 'sel_cpts_arr', 'search_type'));
            }
            ### DB Search function end ###
        }
        /*         * * IMO or DB Search function end ** */
    }

    /* getImoResult Start */

    public function getAdvancedResult($advanced_api, $keyword, $limit) {
        $url = $advanced_api->url;
        $user_credential = $advanced_api->usps_user_id;
        $host = $advanced_api->host;
        $port = $advanced_api->port;
        $soap_request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
		<Execute xmlns="http://www.e-imo.com/">
		<Value>search^' . $limit . '|5|1|2^' . $keyword . '^' . $user_credential . '</Value>
		<Host>' . $host . '</Host>
		<Port>' . $port . '</Port>
		</Execute>
		</soap:Body>
		</soap:Envelope>';
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_request);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        $result = curl_exec($soap_do);
        $result = html_entity_decode($result);
        $result = str_replace('</ExecuteResult></ExecuteResponse></soap:Body></soap:Envelope>', '', $result);
        $result = str_replace('<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><ExecuteResponse xmlns="http://www.e-imo.com/"><ExecuteResult>', '', $result);
        return $result;
    }

    /* getImoResult End */

    public function select_api_search_icd_cpt_list() {
        $request = Request::all();
        $search_value = $request['search_value'];
        $from = $request['from'];
        $sel_from = isset($request['sel_from']) ? $request['sel_from'] : '';

        if ($from == "icd") {

            $search_value_arr = explode("::", $search_value);
            $icd_res = Icd::firstOrCreate(['icd_code' => $search_value_arr[0]]);

            $icd_res1 = json_decode(json_encode($icd_res), true);
            if (!isset($icd_res1['short_description'])) {
                $icd_res->short_description = $search_value_arr[1];
                $icd_res->save();
            }

            if ($sel_from == 'charge') {
                return $icd_res1;
            } else {
                return $icd_res1['id'];
            }
        } elseif ($from == "cpt") {

            $search_value_arr = explode("::", $search_value);
            $cpt_res = Cpt::firstOrCreate(['cpt_hcpcs' => $search_value_arr[0]]);
            $cpt_res1 = json_decode(json_encode($cpt_res), true);
            if (!isset($cpt_res1['short_description'])) {
                $cpt_res->short_description = $search_value_arr[1];
                $cpt_res->medium_description = $search_value_arr[2];
                $cpt_res->long_description = $search_value_arr[3];
                $cpt_res->save();
            }
            return $cpt_res1['id'];
        }
    }

    public function search_cpt_from_favorite($keyword) {
        $cpt_ids = Favouritecpts::pluck('cpt_id')->all();
        $imo_cpt_list = AdminCpt::select('cpt_hcpcs', 'short_description', 'medium_description', 'long_description', 'pli_rvu', 'medicare_global_period', 'modifier_id', 'work_rvu', 'facility_practice_rvu', 'nonfacility_practice_rvu', 'total_facility_rvu', 'total_nonfacility_rvu', 'created_at', 'updated_at', 'created_by', 'updated_by')
                ->where(function($query) use ($cpt_ids) {
                    $query->whereIn('id', $cpt_ids);
                })
                ->where(function($query) use($keyword) {
                    if (strpos($keyword, '@') !== false) {
                        $keyword = str_replace('@', '', $keyword);
                        $query->orwhere('cpt_hcpcs', 'LIKE', '%' . $keyword . '%');
                    } else {
                        $query->orwhere('cpt_hcpcs', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('short_description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('long_description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('work_rvu', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('total_facility_rvu', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('total_nonfacility_rvu', 'LIKE', '%' . $keyword . '%');
                    }
                })->where('status', 'Active')
                ->take(60)
                ->get();
        return $imo_cpt_list;
    }

}