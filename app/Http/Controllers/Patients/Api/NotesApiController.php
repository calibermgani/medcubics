<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\Patient as Patient;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\PatientStatementSettings as PatientStatementSettings;
use App\Http\Controllers\Api\PatientbulkstatementApiController as PatientbulkstatementApiController;
use App\Models\Payments\ClaimInfoV1;
use Input;
use Lang;
use DB;
use App\Models\Medcubics\Users as Users;

class NotesApiController extends Controller {
    /*     * * lists page Starts ** */

    public function getIndexApi($id = '', $export = "") {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $notes = PatientNote::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'),DB::raw('CONVERT_TZ(deleted_at,"UTC","'.$practice_timezone.'") as deleted_at'))->with('user', 'claims')->where('notes_type', "patient")->where('notes_type_id', $id)->whereIn('patient_notes_type', ['alert_notes', 'patient_notes', 'claim_notes', 'statement_notes','claim_denial_notes'])->whereIn('status', ['Active', 'Inactive'])->orderBy('id', 'DESC')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('deleted_at','!=',NULL)->where('patient_notes_type','claim_denial_notes');
                        })->orWhere('deleted_at',NULL);
                    })->withTrashed()->get();
         
            $type_details = Patient::findOrFail($id);
          
            $claims_id = ClaimInfoV1::where('patient_id', $id)->pluck('claim_number', 'id')->all();
            $type_details->enc_id = Helpers::getEncodeAndDecodeOfId($type_details->id, 'encode');
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('notes', 'type_details', 'claims_id')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * lists page Ends ** */

    /*     * * Create page Starts ** */

    public function getCreateApi($id = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $type_details = Patient::findOrFail($id); // Patients details

            $claims_id = ClaimInfoV1::where('patient_id', $id)->select(DB::raw("CONCAT(claim_number, ' - ', DATE_FORMAT(date_of_service, '%m/%d/%Y')) as claim_number_concat"), 'claim_number')
            ->pluck('claim_number_concat', 'claim_number')->all();
            $type_details->enc_id = Helpers::getEncodeAndDecodeOfId($type_details->id, 'encode');
            $notes_exist = PatientNote::whereIn('patient_notes_type', ['alert_notes', 'statement_notes'])->where('notes_type_id', $id)->groupBy('patient_notes_type')->get();
            // If no statement msg is given by default practice statement message has been shown here
            if (!PatientNote::where('notes_type', "patient")->where('notes_type_id', $id)->where('patient_notes_type', 'statement_notes')->count()) {
                $notes_exist = $this->getStatementData($id, $notes_exist);
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_id', 'type_details', 'tab_insurance', 'registration', 'notes_exist')));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Create page Ends ** */

    public function getStatementData($id, $notes) {
        $pat_st_settings_msg = PatientStatementSettings::select('paymentmessage_1', 'created_at')->first();
        $notes = $notes->toArray();
        if (!empty($pat_st_settings_msg)) {
            $notes[count($notes)] = ['id' => 0, 'content' => $pat_st_settings_msg->paymentmessage_1, 'patient_notes_type' => 'statement_notes', 'created_at' => Date('Y/m/d', strtotime($pat_st_settings_msg->created_at))];
        }
        $notes = (object) $notes;
        return $notes;
    }

    /*     * * Store Function Starts ** */

    public function getStoreApi($id = '', $request = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            if ($request == '')
                $request = Request::all();

            $rules = PatientNote::$rules;
            $messages = PatientNote::messages();
            /* Validator::extend('chk_notes_type_exists', function($attribute, $value, $parameters) use($id)
              {
              $patient_notes_type = Input::get($parameters[0]);
              if($patient_notes_type=='alert_notes')
              return (PatientNote::where('notes_type',"patient")->where('notes_type_id',$id)->where('patient_notes_type','alert_notes')->count() > 0)	? false : true;
              else
              return true;
              }); */
            $patient_notes_type = $request['patient_notes_type'];

            /* if($patient_notes_type=='alert_notes' || $patient_notes_type=='statement_notes') {
              $statement_id = PatientNote::where('notes_type',"patient")->where('notes_type_id',$id)->where('patient_notes_type',$patient_notes_type)->pluck('id');
              }
              if(!empty($statement_id)){
              $request['exist_id'] = $statement_id;
              } */
            //$rules 	   = $rules+array('patient_notes_type' => 'required|chk_notes_type_exists:patient_notes_type');
            $rules = $rules + array('patient_notes_type' => 'required');
            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
            } else {

                if (!empty($request['exist_id']) && $request['exist_id'] != 0) {
                    $type_id = $request['exist_id'];
                    $notes = PatientNote::find($type_id);
                    $notes->update($request);
                    $user = Auth::user()->id;
                    $notes->updated_by = $user;
                    $notes->save();
                } else {
                    $data['notes_type'] = "patient";
                    $data['notes_type_id'] = $id;
                    $data['created_by'] = Auth::user()->id;
                    $data['content'] = $request['content'];
                    $data['patient_notes_type'] = $request['patient_notes_type'];
                    if ($request['patient_notes_type'] != 'claim_notes') {
                        $data['claim_id'] = '';
                        $data['created_at'] = Date('Y-m-d H:i:s');
                        PatientNote::insert($data);
                    } else {
                        if ($request['claim_id'][0] == "all") {
                            $request['claim_id'] = ClaimInfoV1::where('patient_id', $id)->whereNull('deleted_at')->pluck('claim_number')->all();
                        }
                        foreach ($request['claim_id'] as $claim_id) {
							$claimInfo = ClaimInfoV1::where('claim_number', $claim_id)->get()->first();
                            $data['created_at'] = Date('Y-m-d H:i:s');
                            $data['claim_id'] = $claimInfo->id;
                            PatientNote::insert($data);
                        }
                    }
                }
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.note_create_msg"), 'data' => ''));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    /*     * * Store Function Ends ** */

    /*     * * Edit page Starts ** */

    public function getEditApi($type_id, $id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() && is_numeric($id)) {
            $type_details = Patient::findOrFail($id); // Patients details 
            //$claims_id = ClaimInfoV1::where('patient_id', $id)->lists('claim_number', 'id');
			
			// Patient notes module edit popup claims no with dos not showing issues fixed
			// Revision 1 : MR-2747 : 26 Aug 2019 : Selva
			
			$claims_id = ClaimInfoV1::where('patient_id', $id)->whereNull('deleted_at')->select(DB::raw("CONCAT(claim_number, ' - ', DATE_FORMAT(date_of_service, '%m/%d/%Y')) as claim_number_concat"), 'claim_number')->pluck('claim_number_concat', 'claim_number')->all();
            $type_id = Helpers::getEncodeAndDecodeOfId($type_id, 'decode');
            if (PatientNote::where('id', $type_id)->count()) {
                $notes = PatientNote::where('id', $type_id)->first();
                $type_detailsID = Helpers::getEncodeAndDecodeOfId($type_details->id, 'encode');
                $notesID = Helpers::getEncodeAndDecodeOfId($notes->id, 'encode');
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_id', 'notes', 'type_details','type_detailsID','notesID')));
            } else {
                return Response::json(array('status' => 'failure_note', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /*     * * Edit page Ends ** */

    /*     * * Update Function Starts ** */

    public function getUpdateApi($type_id, $request = '', $id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $type_id = Helpers::getEncodeAndDecodeOfId($type_id, 'decode');
            if (PatientNote::where('id', '=', $type_id)->count() > 0 && is_numeric($type_id)) {
                if ($request == '')
                    $request = Request::all();

                $rules = PatientNote::$rules;
                $messages = PatientNote::messages();
                /* Validator::extend('chk_notes_type_exists', function($attribute, $value, $parameters) use($id, $type_id)
                  {
                  $patient_notes_type = Input::get($parameters[0]);
                  if($patient_notes_type=='alert_notes')
                  {
                  $count = PatientNote::where('notes_type',"patient")->where('notes_type_id',$id)->where('patient_notes_type','alert_notes')->where('id','!=',$type_id)->count();
                  return ($count > 0)	? false : true;
                  }
                  else
                  {
                  return true;
                  }
                  }); */
                $rules = $rules + array('patient_notes_type' => 'required:patient_notes_type');
                $validator = Validator::make($request, $rules, $messages);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
                } else {
                    $notes = PatientNote::find($type_id);
                    $notes->update($request);
                    $user = Auth::user()->id;
                    $notes->updated_by = $user;
                    //$notes->claim_id = $request['claim_id'];
                    $notes->save();
                    return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.note_update_msg"), 'data' => ''));
                }
            } else {
                return Response::json(array('status' => 'failure_note', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /*     * * Update Function Ends ** */

    /*     * * Delete Function Starts ** */

    public function getDeleteApi($type_id, $patient_id) {
        $id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $type_id = Helpers::getEncodeAndDecodeOfId($type_id, 'decode');
            if (PatientNote::where('notes_type', "patient")->where('id', $type_id)->count() > 0 && is_numeric($type_id)) {
                PatientNote::where('notes_type', "patient")->where('id', $type_id)->delete();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.note_delete_msg"), 'data' => ''));
            } else {
                return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /*     * * Delete Function Starts ** */

    public function getPatientNoteApi($id) {
        if (isset($id) && !is_numeric($id)) {
            $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
            $deceased_date_val = "";
            if (Patient::where('id', $id)->count()) {
                $deceased_date = Patient::where('id', $id)->value('deceased_date');
                if ($deceased_date != '' && $deceased_date != '0000-00-00') {
                    $deceased_date_val = date('m/d/Y', strtotime($deceased_date));
                }
            }
        }
        if (PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->count()) {
            if ($deceased_date_val != '') {
                $get_Info = PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->first()->content;
                echo $get_Info . "<br>Deceased date : $deceased_date_val";
            } else {
                echo $get_Info = PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->first()->content;
            }
        } elseif ($deceased_date_val != '') {
            echo "Deceased date : $deceased_date_val";
        }

        $patbulkobj = new PatientbulkstatementApiController;
        $psettings = PatientStatementSettings::first();

        if (isset($psettings->alert) && $psettings->alert == 1) {
            if (isset($psettings->statementcycle) && $psettings->statementcycle != 'All') {
                $get_currentweek = $patbulkobj->weekOfMonth(date('Y-m-d'));
                $get_addweek = '';
                for ($i = 1; $i <= $get_currentweek; $i++) {
                    $bulkstatementlist = $patbulkobj->getBulkStatementListApi('patientlisting', $i, $id);

                    if (is_array($bulkstatementlist)) {
                        if (count($bulkstatementlist) > 0) {
                            $get_addweek .= 'Week ' . $i . '/';
                        }
                    }
                }
                $collect_week_detail = rtrim($get_addweek, '/');
                if ($collect_week_detail != '')
                    echo '<br>Patient statement are due for ' . $collect_week_detail;
            }
        }
    }
	
	public function getChangeStatusApi(){
		$request = Request::all();
		if(!empty($request['note_id'])){
			$note_id = Helpers::getEncodeAndDecodeOfId($request['note_id'], 'decode');
			if($request['status'] == 'Active')
				$status = 'Inactive';
			else
				$status = 'Active';
			PatientNote::where('id',$note_id)->update(['status'=>$status]);
			return Response::json(array('status' => 'success', 'message' => 'Successfully status changed', 'data' => compact('status')));
		}else{
			return Response::json(array('status' => 'error', 'message' => 'Something worng', 'data' => 'null'));
		}
	}

    function __destruct() {
        
    }

}
