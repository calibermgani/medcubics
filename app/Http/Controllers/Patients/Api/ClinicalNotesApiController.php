<?php

namespace App\Http\Controllers\Patients\api;

use App\Http\Controllers\Documents\Api\DocumentApiController as DocumentApiController;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use App\User as User;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Patients\ClinicalNotes as ClinicalNotes;
use App\Models\Document_categories as ClinicalCategory;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Models\Practice as Practice;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use Validator;
use Response;
use Lang;
use Request;
use Auth;
use Session;

class ClinicalNotesApiController extends Controller {
    /*     * * Lising page start ** */

    public function getIndexApi($id, $export = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        if ((isset($id) && is_numeric($id)) && (Patient::where('id', $id)->count()) > 0) {

            $clinical_notes = ClinicalNotes::with('user', 'claim', 'rendering_provider', 'facility_detail', 'category_type', 'facility_detail.facility_address', 'facility_detail.speciality_details', 'facility_detail.pos_details', 'rendering_provider.degrees', 'rendering_provider.provider_types')->where('document_type', "patients")->where('type_id', $id)->where('clinical_note', "Yes")->orderBy('id', 'DESC')->get();
            if ($export != "") {
                $exportparam = array(
                    'filename' => 'Clinical Notes',
                    'heading' => '',
                    'fields' => array(
                        'dos' => 'DOS',
                        'title' => 'Title',
                        'Claim' => array('table' => 'claim', 'column' => 'claim_number', 'label' => 'Claim No'),
                        'Facility' => array('table' => 'facility_detail', 'column' => 'short_name', 'label' => 'Facility'),
                        'Rendering' => array('table' => 'rendering_provider', 'column' => 'id', 'use_function' => ['App\Models\Provider', 'getProviderShortName'], 'label' => 'Rendering'),
                        'Category' => array('table' => 'category_type', 'column' => 'category_value', 'label' => 'Category'),
                        'Created By' => array('table' => 'user', 'column' => 'short_name', 'label' => 'Created By'),
                        'created_at' => 'Created On')
                );
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $clinical_notes, $export);
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('clinical_notes')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Lising page End ** */

    /*     * * Create page Start ** */

    public function getcreateApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ((isset($id) && is_numeric($id)) && (Patient::where('id', $id)->count()) > 0) {
            $provider = Provider::typeBasedProviderlist('Rendering');
            $facility = Facility::getAllfacilities();
            $claims_arr = ClaimInfoV1::where('patient_id', $id)->orderBy('id', 'DESC')->pluck('id', 'claim_number')->all();
            $claims = array_flip(array_map(array($this, 'myfunction'), $claims_arr));
            $clinical_category = ClinicalCategory::where("module_name", "clinicalnotes")->pluck('category_value', 'id')->all();
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('provider', 'facility', 'clinical_category', 'claims')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Create page End ** */

    /*     * * Store page Start ** */

    public function getStoreApi($patient_id, $request = "") {
        $pat_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if ($request == '' && ((isset($pat_id) && is_numeric($pat_id)) && (Patient::where('id', $pat_id)->count()) > 0 )) {
            $request = Request::all();
            $request["claim_id"] = Helpers::getEncodeAndDecodeOfId($request["claim_id"], 'decode');
            $request["dos"] = Helpers::dateFormat($request["dos"], 'datedb');
            $file = Request::file('filefield');
            $set_err = $src = '';
            $request['practice_id'] = Practice::where("id", Session::get("practice_dbid"))->value('id');
            $request['document_type'] = "patients";
            $request['type_id'] = $request['main_type_id'] = $pat_id;
            $validation_msg = new DocumentApiController();
            $response = $validation_msg->getValidation($file, $request);
            if (isset($response['filefield']))
                $request['filefield'] = $response['filefield'];
            if ($response['status'] == "error")
                return Response::json(array('status' => $response['status'], 'message' => $response['message'], 'data' => ''));
            $rules = ClinicalNotes::$rules + ['filefield' => 'required'];
            $validator = Validator::make($request, $rules, ClinicalNotes::messages());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
            } else {
                //Get file extesion and path info
                $request = $validation_msg->getReqValue($response['max_size'], $file, $request);
                if ($request == "error") {
                    return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
                } else {
                    $request['patient_id'] = $request['type_id'];
                    $request['document_extension'] = $request['ext'];
                    $request['clinical_note'] = "Yes";
                    $data = ClinicalNotes::create($request);
                    $file_store_name = md5($data->id . strtotime(date('Y-m-d H:i:s'))) . '.' . $request['ext'];
                    $store_arr = Helpers::amazon_server_folder_check($request['document_type'], $file, $file_store_name, $src);
                    $data->filename = $file_store_name;
                    $data->document_path = $store_arr[0];
                    $data->document_domain = $store_arr[1];
                    $data->save();
                    $affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize', $response['max_size']);
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => $request['document_type']));
                }
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Store page End ** */

    /*     * * Show page Start ** */

    public function getShowApi($patient_id, $filename) {
        $pat_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if ((isset($pat_id) && is_numeric($pat_id)) && (Patient::where('id', $pat_id)->count()) > 0) {
            if (ClinicalNotes::where('type_id', $pat_id)->where('clinical_note', "Yes")->where('filename', $filename)->count() > 0) {
                $picture = ClinicalNotes::where('type_id', $pat_id)->where('clinical_note', "Yes")->where('filename', $filename)->first();
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('picture')));
            } else {
                return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Show page End ** */

    /*     * * Edit page Start ** */

    public function getEditApi($id, $clinical_id) {
        $pat_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $cli_id = Helpers::getEncodeAndDecodeOfId($clinical_id, 'decode');
        if ((isset($pat_id) && is_numeric($pat_id)) && (Patient::where('id', $pat_id)->count()) > 0) {
            if ((isset($cli_id) && is_numeric($cli_id)) && (ClinicalNotes::where('id', $cli_id)->count() > 0)) {
                $provider = Provider::typeBasedProviderlist('Rendering');
                $facility = Facility::getAllfacilities();
                $claims_arr = ClaimInfoV1::where('patient_id', $pat_id)->orderBy('id', 'DESC')->pluck('id', 'claim_number')->all();
                $claims = array_flip(array_map(array($this, 'myfunction'), $claims_arr));
                $clinical_category = ClinicalCategory::where("module_name", "clinicalnotes")->pluck('category_value', 'id')->all();
                $clinical_detail = ClinicalNotes::findOrFail($cli_id);
                $clinical_detail->claim_id = ($clinical_detail->claim_id != 0 && $clinical_detail->claim_id != '') ? Helpers::getEncodeAndDecodeOfId($clinical_detail->claim_id, 'encode') : $clinical_detail->claim_id;
                $clinical_detail->id = $clinical_id;
                $clinical_detail->dos = date('m/d/Y', strtotime($clinical_detail->dos));
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('provider', 'facility', 'clinical_detail', 'clinical_category', 'claims')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Edit page End ** */

    /*     * * Update page Start ** */

    public function getUpdateApi($patient_id, $claim_id, $request = "") {
        $pat_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $cli_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        if ($request == '' && ((isset($pat_id) && is_numeric($pat_id)) && (Patient::where('id', $pat_id)->count()) > 0 )) {
            $request = Request::all();
            $request["claim_id"] = Helpers::getEncodeAndDecodeOfId($request["claim_id"], 'decode');
            $request["dos"] = Helpers::dateFormat($request["dos"], 'datedb');
            $file = Request::file('filefield');
            $set_err = $src = '';
            $request['practice_id'] = Practice::where("id", Session::get("practice_dbid"))->value('id');
            $request['document_type'] = "patients";
            $request['type_id'] = $request['main_type_id'] = $pat_id;
            $request['type_id'] = $pat_id;

            if ($file != '') {
                $validation_msg = new DocumentApiController();
                $response = $validation_msg->getValidation($file, $request);
                if ($response['status'] == "error")
                    return Response::json(array('status' => $response['status'], 'message' => $response['message'], 'data' => ''));
            }
            $validator = Validator::make($request, ClinicalNotes::$rules, ClinicalNotes::messages());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
            } else {
                if ($file != '')
                //Get file extesion and path info
                    $request = $validation_msg->getReqValue($response['max_size'], $file, $request);
                if ($request == "error") {
                    return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
                } else {
                    $request['patient_id'] = $request['type_id'];
                    if ($file != '')
                        $request['document_extension'] = $request['ext'];

                    $request['patient_id'] = $request['type_id'];
                    $request['clinical_note'] = "Yes";

                    $data = ClinicalNotes::findOrFail($cli_id);
                    $data->update($request);
                    $data->updated_by = Auth::user()->id;
                    $data->save();
                    if ($file != '') {
                        $file_store_name = md5($data->id . strtotime(date('Y-m-d H:i:s'))) . '.' . $request['ext'];
                        $store_arr = Helpers::amazon_server_folder_check($request['document_type'], $file, $file_store_name, $src);
                        $data->filename = $file_store_name;
                        $data->document_path = $store_arr[0];
                        $data->document_domain = $store_arr[1];
                        $data->save();
                        $affectedRows = User::where('id', Auth::user()->id)->increment('maximum_document_uploadsize', $response['max_size']);
                    }
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => $request['document_type']));
                }
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Update page End ** */

    /*     * * Delete page Start ** */

    public function getDestroyApi($p_id, $id) {

        $pat_id = Helpers::getEncodeAndDecodeOfId($p_id, 'decode');
        $cli_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ((isset($pat_id) && is_numeric($pat_id)) && (Patient::where('id', $pat_id)->count()) > 0) {
            if ((isset($cli_id) && is_numeric($cli_id)) && (ClinicalNotes::where('id', $cli_id)->count() > 0)) {
                ClinicalNotes::Where('id', $cli_id)->where('type_id', $pat_id)->delete();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * Delete page End ** */

    /*     * * Get Claim detail via ajax function starts ** */

    public function claimdetailsApi($patient_id, $claim_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        if ($patient_id && $claim_id) {
            $claims = ClaimInfoV1::select('date_of_service', 'rendering_provider_id', 'facility_id')->where('patient_id', $patient_id)->where('id', $claim_id)->first();
            if(!empty($claims)){
                $claims = $claims->toArray();
                $claims["date_of_service"] = date('m/d/Y', strtotime($claims["date_of_service"]));
            }            
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $claims));
        }
    }

    /*     * * Get Claim detail via ajax function ends ** */

    function myfunction($num) {
        return(Helpers::getEncodeAndDecodeOfId($num, 'encode'));
    }

}
