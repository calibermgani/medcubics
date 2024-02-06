<?php

namespace App\Http\Controllers\App\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Cpt as Cpt;
use App\Models\Document as Document;
use App\Models\Patients\DocumentFollowupList;
use App\Models\Document_categories as Document_categories;
use App\Models\Facility as Facility;
use App\Models\Favouritecpts;
use App\Models\Icd as Icd;
use App\Models\Medcubics\Cpt as AdminCpt;
use App\Models\Medcubics\Faq as FAQ;
use App\Models\Medcubics\Icd as AdminIcd;
use App\Models\Medcubics\Users as User;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use App\Models\Modifier;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Practice as Practice;
use App\Models\Provider as Provider;
use App\Models\State as State;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Traits\CommonUtil;
use Carbon\Carbon;
use Config;
use DB;
use Hash;
use Input;
use Request;
use Response;
use Validator;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController as ChargeV1ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ChargeCaptureAppApiController extends Controller
{
    /*
      |--------------------------------------------------------------------------
      | ChargeCaptureAppApiController
      | @author Manikandan Duraisamy - CD019
      | @author last Update by Baskar
      |--------------------------------------------------------------------------
      |
      | This controller handles  the all the Charger Capture Mobile App Requests.
      | Each  method Handle the single requst and @return the specific datas
      |
      |
     */

    /**
     * get the user List
     * find the Eligible userList with app_name Status
     * @return JSON Response
     */
    use CommonUtil;

    public function getUserList()
    {
        try {
            $userDetails = User::where('app_name', '=', 'CHARGECAPTURE')
                ->where('provider_access_id', '!=', '')
                ->where('status', 'Active')
                ->get();
            $userArrays = array();
            foreach ($userDetails as $user) {
                $temp['userId'] = $user->id;
                $temp['userName'] = Helpers::getNameformat($user->lastname, $user->firstname, '');
                $temp['emailId'] = $user->email;
                $temp['practiceId'] = $user->practice_access_id;
                $temp['providerId'] = $user->provider_access_id;
                $temp['roldId'] = $user->role_id;
                array_push($userArrays, $temp);
            }

            $returnData = array(
                'status' => '0',
                'statusMessage' => 'success',
                'userList' => $userArrays
            );
            return Response::json($returnData);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * Check The User Login Details
     * @param \Illuminate\Http\Request
     * @return JSON Response
     */
    public function checklogindetails()
    {
        $request = Request::all();
        try {
            $id = $request['id'];
            $password = $request['password'];
            $device_id = "TEST123"; //@$request['device_id'];
            $user_details = User::where('id', '=', $id)->where('app_name', 'CHARGECAPTURE')->where('status', 'Active')->first();

            if ($user_details) {
                $table_val = Hash::check($password, $user_details['password']);
                if ($table_val == true) {
                    $authenticationId = "U" . $user_details['id'] . md5(strtotime(date('Y-m-d H:i:s')));
                    $app_user_count = UsersAppDetails::where('user_id', $user_details['id'])->where('mobile_id', $device_id)->count();
                    if ($app_user_count == 0) {
                        UsersAppDetails::create(['user_id' => $user_details['id'], 'mobile_id' => $device_id, 'authentication_id' => $authenticationId, 'last_login_time' => date('Y-m-d H:i:s')]);
                    } else {
                        UsersAppDetails::where('user_id', $user_details['id'])->where('mobile_id', $device_id)->update(['authentication_id' => $authenticationId, 'last_login_time' => date('Y-m-d H:i:s')]);
                    }
                    $provider_id = $user_details->provider_access_id;
                    $practice_id = $user_details->practice_access_id;
                    $facility_access_id = $user_details->facility_access_id;
                    $db = new DBConnectionController();
                    $db->connectPracticeDB($practice_id);
                    $providerData = Provider::find($provider_id);
                    if (isset($providerData)) {
                        $returnData = array(
                            'userId' => $user_details->id,
                            'providerId' => $providerData->id,
                            'practiceId' => $practice_id,
                            'facilityId' => $facility_access_id,
                            'lastName' => $providerData->last_name,
                            'firstName' => $providerData->first_name,
                            'middleName' => $providerData->middle_name,
                            'email' => $providerData->email,
                            'authenticationId' => $authenticationId,
                            'status' => '0',
                            'statusMessage' => 'success',
                        );
                        return Response::json($returnData);
                    } else {
                        return Response::json(array('status' => '1', 'statusMessage' => 'Provider_iD invalid'));
                    }
                } else {
                    return Response::json(array('status' => '1', 'statusMessage' => 'Password invalid'));
                }
            } else {
                return Response::json(array('status' => '1', 'statusMessage' => 'User name invalid'));
            }
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * forgot Password process.
     * @param \Illuminate\Http\Request
     * @param userName.
     * @return JSON Response
     */
    public function forgotpasswordprocess()
    {
        $request = Request::all();
        try {
            $username = $request['username'];
            $emailId = $request['emailid'];
            $userId = $request['userid'];
            $user_details = User::where('id', $userId)->first();
            if (count($user_details)) {
                $chg_password = "US" . strtotime(date('H:i:s')) . "PI" . $user_details['id'];
                $user_details->update(['password' => Hash::make($chg_password), 'password_change_time' => date('Y-m-d H:i:s')]);
                UsersAppDetails::where('user_id', $user_details['id'])->update(['authentication_id' => '']);
                //Start Send Mail to user
                $string = " ================== Medcubics New Password ================== \n";
                $string .= " Time : " . date('H:i:s') . "\n";
                $string .= " User Name : " . $username . "\n";
                $string .= " Password : " . $chg_password . "\n";
                $to = $emailId;
                $subject = "Medcubics Forgot Password";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: Medcubics' . "\r\n";
                mail($to, $subject, $string, $headers);
                //End Send Mail to user
                return Response::json(array('status' => '0', 'statusMessage' => 'success'));
            } else {
                return Response::json(array('status' => '1', 'statusMessage' => 'Invalid user'));
            }
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            write($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get Cpt and Icd With Last Modified Date
     * @param \Illuminate\Http\Request
     * @return JSON Response
     */
    public function getCptAndIcdWithLastModifiedDate()
    {
        try {
            $request = Request::all();
            if (isset($request['lmdate']) && ($request['lmdate'] != '' && $request['action'] == 'updateCheck')) {
                $lmd = $request['lmdate'];
                if ($request['type'] == 'icd') {
                    $newRecords = Icd::where('updated_at', '>', $lmd)->count();
                    if ($newRecords > 0) {
                        $icdDatas = Icd::all();
                        $icdDatasArr = array();
                        foreach ($icdDatas as $icdData) {
                            $temp['recordId'] = $icdData->id;
                            $temp['icdId'] = $icdData->icd_code;
                            $temp['icdType'] = $icdData->icd_type;
                            $temp['header'] = $icdData->header;
                            $temp['shortDescription'] = $icdData->short_description;
                            $temp['mediumDescription'] = $icdData->medium_description;
                            $temp['longDescription'] = $icdData->long_description;
                            $temp['statementDescription'] = $icdData->statement_description;
                            $temp['sex'] = $icdData->sex;
                            $temp['effectiveDate'] = $icdData->effectivedate;
                            $temp['inactiveDate'] = $icdData->inactivedate;
                            $temp['updatedAt'] = Carbon::parse($icdData->updated_at)->toDateTimeString();
                            array_push($icdDatasArr, $temp);
                        }
                        $icdflmd = $icdDatas->sortByDesc('updated_at')->first();
                        $icdLastModifiedDate = Carbon::parse($icdflmd->updated_at)->toDateTimeString();
                        $icdRetrunData = array(
                            'icdDatas' => $icdDatasArr,
                            'icdLastModifiedDate' => $icdLastModifiedDate
                        );
                        //cpt End
                        $icdAndCptReturnDatas = array(
                            'icdData' => $icdRetrunData,
                            'cptData' => '',
                            'status' => '0',
                            'statusMessage' => 'success',
                            'updateCheckingStatus' => 'NewRecords Found ICD',
                        );

                        return Response::json($icdAndCptReturnDatas);
                    } else {
                        $returnData = array(
                            'icdData' => '',
                            'cptData' => '',
                            'status' => '1',
                            'statusMessage' => 'success',
                            'updateCheckingStatus' => 'NewRecordsNotFount in ICD',
                        );

                        return Response::json($returnData);
                    }
                } elseif ($request['type'] == 'cpt') {
                    $lmd = $request['lmdate'];
                    $cptNewRecords = Cpt::where('updated_at', '>', $lmd)->count();
                    if ($cptNewRecords > 0) {
                        //cpt Etart
                        $cptDatas = Cpt::all();
                        $cptDatasArr = array();
                        $temp = array();
                        foreach ($cptDatas as $cptData) {
                            $temp['recordId'] = $cptData->id;
                            $temp['cptHcpcs'] = $cptData->cpt_hcpcs;
                            $temp['codeType'] = $cptData->code_type;
                            $temp['shortDescription'] = $cptData->short_description;
                            $temp['mediumDescription'] = $cptData->medium_description;
                            $temp['longDescription'] = $cptData->long_description;
                            $temp['ageLimit'] = $cptData->age_limit;
                            $temp['billedAmount'] = $cptData->billed_amount;
                            $temp['allowedAmount'] = $cptData->allowed_amount;
                            $temp['posId'] = $cptData->pos_id;
                            $temp['typeOfService'] = $cptData->type_of_service;
                            $temp['updatedAt'] = Carbon::parse($cptData->updated_at)->toDateTimeString();
                            array_push($cptDatasArr, $temp);
                        }
                        $cptflmd = $cptDatas->sortByDesc('updated_at')->first();
                        $cptLastModifiedDate = Carbon::parse($cptflmd->updated_at)->toDateTimeString();
                        $cptRetrunData = array(
                            'cptDatas' => $cptDatasArr,
                            'cptLastModifiedDate' => $cptLastModifiedDate
                        );
                        //cpt End
                        $icdAndCptReturnDatas = array(
                            'icdData' => '',
                            'cptData' => $cptRetrunData,
                            'status' => '0',
                            'statusMessage' => 'success',
                            'updateCheckingStatus' => 'NewRecords Found in CPT',
                        );

                        return Response::json($icdAndCptReturnDatas);
                    } else {
                        $returnData = array(
                            'icdData' => '',
                            'cptData' => '',
                            'status' => '1',
                            'statusMessage' => 'success',
                            'updateCheckingStatus' => 'NewRecords Not Found In CPT',
                        );

                        return Response::json($returnData);
                    }
                }
            } elseif (($request['lmdate'] == '') && ($request['action'] == 'first')) {

                $icdAndCptReturnDatas = $this->fetchAllIcdCptDatas($request);
                $jsondata = Response::json($icdAndCptReturnDatas);
            }
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * fetch All Icd and Cpt Datas
     * @return JSON Response
     */
    public function fetchAllIcdCptDatas($request)
    {
        try {
            $icdDatas = AdminIcd::take(200)->get();
            $icdDatasArr = array();
            foreach ($icdDatas as $icdData) {
                $temp['recordId'] = $icdData->id;
                $temp['icdId'] = $icdData->icd_code;
                $temp['icdType'] = $icdData->icd_type;
                $temp['header'] = $icdData->header;
                $temp['shortDescription'] = $icdData->short_description;
                $temp['mediumDescription'] = $icdData->medium_description;
                $temp['longDescription'] = $icdData->long_description;
                $temp['statementDescription'] = $icdData->statement_description;
                $temp['sex'] = $icdData->sex;
                $temp['effectiveDate'] = $icdData->effectivedate;
                $temp['inactiveDate'] = $icdData->inactivedate;
                $temp['updatedAt'] = Carbon::parse($icdData->updated_at)->toDateTimeString();
                array_push($icdDatasArr, $temp);
            }
            $icdflmd = $icdDatas->sortByDesc('updated_at')->first();

            $icdLastModifiedDate = Carbon::parse($icdflmd->updated_at)->toDateTimeString();
            $icdRetrunData = array(
                'icdDatas' => $icdDatasArr,
                'icdLastModifiedDate' => $icdLastModifiedDate
            );
            //icd End
            //cpt Etart
            //$cptDatas = AdminCpt::take(200)->get();
            $db = new DBConnectionController();
            $db->connectPracticeDB($request['practiceid']);
            $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
            $cptDatas = AdminCpt::whereIn('id', $favourites_ids)->where('status', "Active")->get();
            $cptDatasArr = array();
            $temp = array();

            foreach ($cptDatas as $cptData) {
                $temp['recordId'] = $cptData->id;
                $temp['cptHcpcs'] = $cptData->cpt_hcpcs;
                $temp['codeType'] = $cptData->code_type;
                $temp['shortDescription'] = $cptData->short_description;
                $temp['mediumDescription'] = $cptData->medium_description;
                $temp['longDescription'] = $cptData->long_description;
                $temp['ageLimit'] = $cptData->age_limit;
                $temp['billedAmount'] = $cptData->billed_amount;
                $temp['allowedAmount'] = $cptData->allowed_amount;
                $temp['posId'] = $cptData->pos_id;
                $temp['typeOfService'] = $cptData->type_of_service;
                $temp['updatedAt'] = Carbon::parse($cptData->updated_at)->toDateTimeString();
                array_push($cptDatasArr, $temp);
            }

            $cptflmd = $cptDatas->sortByDesc('updated_at')->first();
            $cptLastModifiedDate = "";
            if ($cptflmd) {
                $cptLastModifiedDate = Carbon::parse($cptflmd->updated_at)->toDateTimeString();
            }

            $cptRetrunData = array(
                'cptDatas' => $cptDatasArr,
                'cptLastModifiedDate' => $cptLastModifiedDate
            );
            //cpt End
            $icdAndCptReturnData = array(
                'icdData' => $icdRetrunData,
                'cptData' => $cptRetrunData,
                'status' => '0',
                'statusMessage' => 'success',
                'updateCheckingStatus' => 'firstHit',
            );
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }

        return $icdAndCptReturnData;
    }

    /**
     * get Facility List
     * @param \Illuminate\Http\Request
     * From Specific practice and provider with lmdate
     * @return JSON Response
     */
    function getFacilityList()
    {
        $request = Request::all();
        try {
            $practiceid = $request['practiceid'];
            $providerId = $request['providerid'];
            $lmd = $request['lmdate'];
            $newFacilitysArr = array();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceid);
            $facilitys = Facility::with('facility_address')->get();
            $facilityLastModifiedDate = $facilitys->sortByDesc('updated_at')->first();
            if ($lmd != '') {
                $facilityNewRecords = Facility::where('updated_at', '>', $lmd)->count();
                if ($facilityNewRecords > 0) {
                    $newFacilitysArr = $this->getAllFacilityDatas();
                    $facilitysArr = array(
                        'facilityDatas' => $newFacilitysArr,
                        'status' => '0',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'New records found in facility',
                        'facilityLastModifiedDate' => Carbon::parse($facilityLastModifiedDate->updated_at)->toDateTimeString()
                    );
                } else {
                    $facilitysArr = array(
                        'facilityDatas' => $newFacilitysArr,
                        'status' => '1',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'New records not found in facility',
                        'facilityLastModifiedDate' => ''
                    );
                }
                return Response::json($facilitysArr);
            } else {
                $facilitysArr = $this->getAllFacilityDatas();
            }
            $facilityReturnData = array(
                'facilityDatas' => $facilitysArr,
                'status' => '0',
                'statusMessage' => 'success',
                'updateCheckingStatus' => 'firstHit',
                'facilityLastModifiedDate' => Carbon::parse($facilityLastModifiedDate->updated_at)->toDateTimeString()
            );
            return Response::json($facilityReturnData);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get getAllFacilityDatas
     * @return Facility Array
     */
    function getAllFacilityDatas()
    {
        $facilitysArr = array();
        $facilitys = Facility::with('fcounty')->get();
        foreach ($facilitys as $facility) {
            $temp['id'] = $facility->id;
            $temp['shortName'] = $facility->short_name;
            $temp['facilityName'] = $facility->facility_name;
            $temp['description'] = $facility->description;
            $temp['specialityId'] = $facility->speciality_id;
            $temp['facilityNpi'] = $facility->facility_npi;
            $temp['claimFormat'] = $facility->claim_format;
            $temp['facilityNpi'] = $facility->facility_npi;
            $temp['specialityId'] = $facility->speciality_id;
            $temp['address'] = $facility->facility_address->address1;
            $temp['city'] = $facility->facility_address->city;
            $temp['state'] = $facility->facility_address->state;
            $temp['pos'] = $facility->pos_details->pos;
            $temp['phone'] = $facility->phone;
            $temp['countyName'] = isset($facility->fcounty->name)?$facility->fcounty->name:'';
            $temp['county'] = $facility->county;
            $temp['status'] = $facility->status;
            $temp['no_of_visit_per_week'] = $facility->no_of_visit_per_week;
            $temp['created_at'] = Carbon::parse($facility->created_at)->toDateTimeString();
            $temp['lastModifiedDate'] = Carbon::parse($facility->updated_at)->toDateTimeString();
            array_push($facilitysArr, $temp);
        }

        return $facilitysArr;
    }

    /**
     * get getPatientList
     * From Practiceid and providerid and lmdate
     * @return Patient Array
     */
    function getPatientList()
    {
        $request = Request::all();
        $patientsArr = array();
        try {
            $practiceid = $request['practiceid'];
            $providerId = $request['providerid'];
            $lmd = $request['lmdate'];
            $patientsArr = array();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceid);

            $patients = Patient::where('status','Active')->get();
            $patientLastModifiedDate = $patients->sortByDesc('updated_at')->first();
            if ($lmd != '') {
                $patientNewRecords = Patient::where('updated_at', '>', $lmd)->count();
                if ($patientNewRecords > 0) {
                    $patientsArr = $this->getAllpatientDatas();
                    $rproviderArr = array();
                    $rproviders = $this->getReferringOrderingSupervising();
                    foreach ($rproviders as $key => $value) {
                        $rprovider['id'] = $key;
                        $rprovider['name'] = $value;
                        array_push($rproviderArr, $rprovider);
                    }
                    $patientsArr = array(
                        'patientData' => $patientsArr,
                        'status' => '0',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'New records found in patient',
                        'patientsLastModifiedDate' => Carbon::parse($patientLastModifiedDate->updated_at)->toDateTimeString(),
                        'ReferringProvidersList' => $rproviderArr
                    );
                } else {
                    $patientsArr = array(
                        'patientData' => $patientsArr,
                        'status' => '1',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'New records not found in patient',
                        'patientsLastModifiedDate' => '',
                        'ReferringProvidersList' => ''
                    );
                }
                return Response::json($patientsArr);
            } else {
                $patientsArr = $this->getAllpatientDatas();
                $rproviderArr = array();
                $rproviders = $this->getReferringOrderingSupervising();
                foreach ($rproviders as $key => $value) {
                    $rprovider['id'] = $key;
                    $rprovider['name'] = $value;
                    array_push($rproviderArr, $rprovider);
                }
            }

            $patientReturnData = array(
                'patientData' => $patientsArr,
                'status' => '0',
                'statusMessage' => 'success',
                'updateCheckingStatus' => 'firstHit',
                'patientsLastModifiedDate' => Carbon::parse($patientLastModifiedDate->updated_at)->toDateTimeString(),
                'ReferringProvidersList' => $rproviderArr
            );
            return Response::json($patientReturnData);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get getAllPatientDatas
     * From current Practice
     * @return Patient Array
     */
    function getAllPatientDatas()
    {
        try {
            $patientsArr = array();
            $allPatientsDataCnt = Patient::where('status','Active')->count();
            $temp = array();
            $padding = 0;
            $batch = 100;
            while ($allPatientsDataCnt > $padding) {
                $patients = Patient::where('status','Active')->orderBy('id', 'asc')
                    ->skip($padding)->take($batch)->get();
                foreach ($patients as $patient) {
                    $temp['id'] = $patient->id;
                    $temp['firstName'] = $patient->first_name;
                    $temp['lastName'] = $patient->last_name;
                    $temp['middleName'] = $patient->middle_name;
                    $temp['patientName'] = Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
                    $temp['account_no'] = $patient->account_no;
                    $temp['dob'] = $patient->dob;
                    $temp['ssn'] = $patient->ssn;
                    $temp['gender'] = $patient->gender;
                    $temp['is_self_pay'] = $patient->is_self_pay;
                    $temp['created_at'] = Carbon::parse($patient->created_at)->toDateTimeString();
                    $temp['phone'] = $patient->phone;
                    $temp['status'] = $patient->status;
                    $temp['lastModifiedDate'] = Carbon::parse($patient->updated_at)->toDateTimeString();
                    array_push($patientsArr, $temp);                    
                }
                $padding = $padding + $batch;
            }
            return $patientsArr;
        }
        catch (\Exception $e) {
                $this->showErrorResponse('chargeCaptureApp', $e);
                return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get getFullPatientDetails
     * @param \Illuminate\Http\Request
     * form spacific practice
     * @return Patient Array
     */
    function getFullPatientDetails()
    {
        $patientsArr = array();
        $request = Request::all();
        $practiceid = $request['practiceid'];
        try {
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceid);
            $patient = patient::with('patient_insurance', 'get_notes_app', 'pos_details')->where('id', $request['patientid'])->first();
            if ($patient) {
                $filename = $patient->avatar_name . '.' . $patient->avatar_ext;
                $img_details = [];
                $img_details['module_name'] = 'patient';
                $img_details['file_name'] = $filename;
                $img_details['practice_name'] = md5('P' . $practiceid);
                $img_details['need_url'] = 'yes';
                $img_details['alt'] = 'patient-image';
                $image_tag = Helpers::checkAndGetAvatar($img_details);
                if ($image_tag == "img/patient_noimage.png")
                    $image_tag = Url('/')."/img/patient_noimage.png";
                $temp['image_tag'] = $image_tag;
                $temp['id'] = $patient->id;
                $temp['firstName'] = $patient->first_name;
                $temp['lastName'] = $patient->last_name;
                $temp['middleName'] = $patient->middle_name;
                $temp['patientName'] = Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
                $temp['account_no'] = $patient->account_no;
                $temp['dob'] = $patient->dob;
                $temp['ssn'] = $patient->ssn;
                $temp['gender'] = $patient->gender;
                $temp['is_self_pay'] = $patient->is_self_pay;
                $temp['created_at'] = Carbon::parse($patient->created_at)->toDateTimeString();
                $temp['phone'] = $patient->phone;
                $notes =[];
                if(isset($patient->get_notes_app) && !empty($patient->get_notes_app))
                    foreach($patient->get_notes_app as $key => $note){
                        $notes[$key]['id'] = $note->id;
                        $notes[$key]['title'] = $note->title;
                        $notes[$key]['content'] = $note->content;
                        $notes[$key]['follow_up_content'] = $note->follow_up_content;
                        $notes[$key]['notes_type'] = $note->notes_type;
                        $notes[$key]['patient_notes_type'] = $note->patient_notes_type;
                        $notes[$key]['claim_id'] = $note->claim_id;
                        $notes[$key]['notes_type_id'] = $note->notes_type_id;
                        $notes[$key]['status'] = $note->status;
                        $notes[$key]['user_id'] = $note->user_id;
                        $notes[$key]['created_by'] = $note->created_by;
                        $notes[$key]['updated_by'] = $note->updated_by;
                        $notes[$key]['created_at'] = $note->created_at;
                        $notes[$key]['updated_at'] = $note->updated_at;
                        $notes[$key]['deleted_at'] = $note->deleted_at;
                        $notes[$key]['date'] = $note->date;
                        if($note->claim_id!=0){
                            $notes[$key]['claim_number'] = $note->claims->claim_number;
                            $notes[$key]['date_of_service'] = date('m/d/Y',strtotime($note->claims->date_of_service));
                        }else{
                            $notes[$key]['claim_number'] = NULL;
                            $notes[$key]['date_of_service'] = NULL;
                        }
                        if($note->patient_notes_type=='claim_denial_notes' && $note->content!=''){
                            $exp = explode('^^^', $note->content);
                            $notes[$key]['denial_date'] = date('m/d/y',strtotime($exp[0]));
                            $notes[$key]['denial_check_no'] = $exp[1];
                            $notes[$key]['denial_billed_to'] = Helpers::getInsuranceFullName($exp[2]);
                            $denial_description = DB::table('codes')->whereIn('id',explode(',',$exp[4]))->get();
                            foreach ($denial_description as $k => $value) {
                            $description[$k]['code'] = $value->transactioncode_id;
                            $description[$k]['description'] = $value->description;
                            }
                            $notes[$key]['denial_description'] = $description;
                        }else{
                            $notes[$key]['denial_date'] = NULL;
                            $notes[$key]['denial_check_no'] = NULL;
                            $notes[$key]['denial_billed_to'] = NULL;
                            $notes[$key]['denial_description'] = NULL;
                        }
                    }
                $temp['notes'] = $notes;
                $temp['status'] = $patient->status;
                $temp['lastModifiedDate'] = Carbon::parse($patient->updated_at)->toDateTimeString();
                $appointments = $patient->patient_sch_appointment;
                $temp1 = array();
                $documentsArr = array();
                $climsArr = array();
                $documents = Document::where('type_id', $patient->id)->where('document_type','patients')->get();
                if ($documents) {
                    foreach ($documents as $document) {
                        array_push($documentsArr, $document);
                    }
                }
                $icdArr = array();
                $cptArr = array();
                $clims = ClaimInfoV1::with('patient', 'refering_provider')->where('patient_id', $patient->id)->orderBy('created_at', 'desc')->first();
                if ($clims) {
                    $temp['admit_date'] = $clims->admit_date;
                    $temp['date_of_service'] = $clims->date_of_service;
                    $refering_pro = $clims->refering_provider;
                    $temp['refering_provider'] = ($refering_pro != null) ? $refering_pro : '';
                    $temp['claim_number'] = $clims->claim_number;
                    $climsIds = $clims->icd_codes;
                    $climasArr = explode(",", $climsIds);
                    $temp1 = array();
                    foreach ($climasArr as $value) {
                        $icdDataArr = DB::table('icd_10')->where('id', $value)->first();
                        if ($icdDataArr) {
                            $temp1['recordId'] = $icdDataArr->id;
                            $temp1['icdId'] = $icdDataArr->icd_code;
                            $temp1['icdType'] = $icdDataArr->icd_type;
                            $temp1['header'] = $icdDataArr->header;
                            $temp1['shortDescription'] = ucwords(strtolower($icdDataArr->short_description));
                            $temp1['mediumDescription'] = $icdDataArr->medium_description;
                            $temp1['longDescription'] = $icdDataArr->long_description;
                            $temp1['statementDescription'] = $icdDataArr->statement_description;
                            $temp1['sex'] = $icdDataArr->sex;
                            $temp1['effectiveDate'] = $icdDataArr->effectivedate;
                            $temp1['inactiveDate'] = $icdDataArr->inactivedate;
                            $temp1['updatedAt'] = Carbon::parse($icdDataArr->updated_at)->toDateTimeString();
                            array_push($icdArr, $temp1);
                        }
                    }
                    $claimsfullRecords = ClaimCPTInfoV1::select('cpt_code')->where('claim_id', $clims->id)->get();
                    $temp2 = array();
                    foreach ($claimsfullRecords as $valuecpt) {
                        $cptDataArr = AdminCpt::where('cpt_hcpcs', $valuecpt->cpt_code)->first();
                        if ($cptDataArr) {
                            $temp2['recordId'] = $cptDataArr->id;
                            $temp2['cptHcpcs'] = $cptDataArr->cpt_hcpcs;
                            $temp2['codeType'] = $cptDataArr->code_type;
                            $temp2['shortDescription'] = $cptDataArr->short_description;
                            $temp2['mediumDescription'] = ucwords(strtolower($cptDataArr->medium_description));
                            $temp2['longDescription'] = $cptDataArr->long_description;
                            $temp2['ageLimit'] = $cptDataArr->age_limit;
                            $temp2['billedAmount'] = $cptDataArr->billed_amount;
                            $temp2['allowedAmount'] = $cptDataArr->allowed_amount;
                            $temp2['posId'] = $cptDataArr->pos_id;
                            $temp2['typeOfService'] = $cptDataArr->type_of_service;
                            $temp2['updatedAt'] = Carbon::parse($cptDataArr->updated_at)->toDateTimeString();
                            array_push($cptArr, $temp2);
                        }
                    }

                    $icdDatas = $icdArr;
                    $temp['icd_codes'] = $icdArr;
                    $temp['cpt_codes'] = $cptArr;
                    $temp['self_pay'] = $patient->is_self_pay;
                    $temp['insurance_category'] = ($patient->is_self_pay=='No')?'Primary':'';
                } else {
                    $temp['admit_date'] = "";
                    $temp['date_of_service'] = "";
                    $temp['refering_provider'] = "";
                    $temp['claim_number'] = "";
                    $temp['icd_codes'] = $icdArr;
                    $temp['cpt_codes'] = $cptArr;
                    $temp['self_pay'] = "";
                    $temp['insurance_category'] = "";
                }
                $temp['documentData'] = $documentsArr;
                array_push($patientsArr, $temp);
                $patientReturnData = array(
                    'patientDatails' => $patientsArr,
                    'status' => '0',
                    'statusMessage' => 'success',
                );
                return Response::json($patientReturnData);
            } else {
                $patientReturnData = array(
                    'patientDatails' => '',
                    'status' => '1',
                    'statusMessage' => 'Patient Id Is Not Found',
                );
                return Response::json($patientReturnData);
            }
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get GetAppData
     * @param \Illuminate\Http\Request
     * form spacific practice
     * get icd,cpt,facility,patient data with version id
     * note: version_id  =  updated_at
     * @return JSON Response
     */
    public function GetAppData()
    {
        try {
            $request = Request::all();
            $validator = Validator::make($request,[
                'practiceid'=>'required'
                ]);
            if($validator->fails()){
                return Response::json(['success'=>0,'message'=>$validator->errors()]);
            }else{
                $practiceid = $request['practiceid'];
                $cptlmd = $request['cptversionid'];
                $cptCount = $request['cptcount'];
                $modifierlmd = $request['modifierversionid'];
                $statelmd = $request['stateversionid'];
                $temp = array();
                $returnArray = array();
                $db = new DBConnectionController();
                $db->connectPracticeDB($practiceid);
                $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
                $getLmdData = Cpt::whereIn('id', $favourites_ids)->where('status', "Active")->pluck('id')->all();
                $rowcount = Favouritecpts::whereIn('cpt_id', $getLmdData)->count();

                if ($cptlmd != '') {
                    $temp = array();
                    $favourites_ids = Favouritecpts::where('updated_at', '>', $cptlmd)->count();
                    $cptNewRecords = $favourites_ids;
                    if ($cptNewRecords > 0) {
                        $cptDatas = $this->getAllFavCptDatas($practiceid);
                        $temp['cptDatas'] = $cptDatas;
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '0';
                        $temp['message'] = 'new Records  Found In cpt';
                    } else {
                        if ($rowcount > $cptCount || $rowcount < $cptCount) {
                            $cptDatas = $this->getAllFavCptDatas($practiceid);
                            $temp['cptDatas'] = $cptDatas;
                            $temp['statusMessage'] = 'success';
                            $temp['status'] = '0';
                            $temp['message'] = 'new Records  Found In cpt';
                        } else {
                            $temp['cptLastModifiedDate'] = '';
                            $temp['statusMessage'] = 'success';
                            $temp['status'] = '1';
                            $temp['message'] = 'new Records Not Found In cpt';
                        }
                    }
                    array_push($returnArray, $temp);
                } else {
                    $temp = array();
                    $cptDatas = $this->getAllFavCptDatas($practiceid);
                    $temp['cptDatas'] = $cptDatas;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'firstHit';
                    array_push($returnArray, $temp);
                }

                if ($modifierlmd != '') {
                    $temp = array();
                    $modifireNewRecords = Modifier::where('updated_at', '>', $modifierlmd)->count();
                    if ($modifireNewRecords > 0) {
                        $modifierDatas = $this->getAllModifierDatas();
                        $temp['modifierDatas'] = $modifierDatas;
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '0';
                        $temp['message'] = 'new Records  Found In modifier';
                    } else {
                        $temp['modifierDatas'] = '';
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '1';
                        $temp['message'] = 'new Records Not Found In modifier';
                    }
                    array_push($returnArray, $temp);
                } else {
                    $temp = array();
                    $modifierDatas = $this->getAllModifierDatas();
                    $temp['modifierDatas'] = $modifierDatas;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'firstHit';
                    array_push($returnArray, $temp);
                }

                if ($statelmd != '') {
                    $temp = array();
                    $stateNewRecords = State::where('updated_at', '>', $cptlmd)->count();
                    if ($stateNewRecords > 0) {
                        $states = $this->getStateData();
                        $temp['stateDatas'] = $states;
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '0';
                        $temp['message'] = 'new Records  Found In State';
                    } else {
                        $temp['cptLastModifiedDate'] = '';
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '1';
                        $temp['message'] = 'new Records Not Found In state';
                    }
                    array_push($returnArray, $temp);
                } else {
                    $temp = array();
                    $stateData = $this->getStateData();
                    $temp['stateData'] = $stateData;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'firstHit';
                    array_push($returnArray, $temp);
                }


                $RetrunDatas = array(
                    'updateStatus' => $returnArray,
                    'status' => '0',
                    'statusMessage' => 'success',
                );
                return Response::json($RetrunDatas);
            }
            
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }
    public function GetAppData_old()
    {
        try {
            $request = Request::all();
            $practiceid = $request['practiceid'];
            $cptlmd = $request['cptversionid'];
            $cptCount = $request['cptcount'];
            $modifierlmd = $request['modifierversionid'];
            $statelmd = $request['stateversionid'];
            $temp = array();
            $returnArray = array();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceid);
            $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
            $getLmdData = AdminCpt::whereIn('id', $favourites_ids)->where('status', "Active")->pluck('id')->all();
            $rowcount = Favouritecpts::whereIn('cpt_id', $getLmdData)->count();

            if ($cptlmd != '') {
                $temp = array();
                $favourites_ids = Favouritecpts::where('updated_at', '>', $cptlmd)->count();
                $cptNewRecords = $favourites_ids;
                if ($cptNewRecords > 0) {
                    $cptDatas = $this->getAllFavCptDatas($practiceid);
                    $temp['cptDatas'] = $cptDatas;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'new Records  Found In cpt';
                } else {
                    if ($rowcount > $cptCount || $rowcount < $cptCount) {
                        $cptDatas = $this->getAllFavCptDatas($practiceid);
                        $temp['cptDatas'] = $cptDatas;
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '0';
                        $temp['message'] = 'new Records  Found In cpt';
                    } else {
                        $temp['cptLastModifiedDate'] = '';
                        $temp['statusMessage'] = 'success';
                        $temp['status'] = '1';
                        $temp['message'] = 'new Records Not Found In cpt';
                    }
                }
                array_push($returnArray, $temp);
            } else {
                $temp = array();
                $cptDatas = $this->getAllFavCptDatas($practiceid);
                $temp['cptDatas'] = $cptDatas;
                $temp['statusMessage'] = 'success';
                $temp['status'] = '0';
                $temp['message'] = 'firstHit';
                array_push($returnArray, $temp);
            }

            if ($modifierlmd != '') {
                $temp = array();
                $modifireNewRecords = Modifier::where('updated_at', '>', $modifierlmd)->count();
                if ($modifireNewRecords > 0) {
                    $modifierDatas = $this->getAllModifierDatas();
                    $temp['modifierDatas'] = $modifierDatas;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'new Records  Found In modifier';
                } else {
                    $temp['modifierDatas'] = '';
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '1';
                    $temp['message'] = 'new Records Not Found In modifier';
                }
                array_push($returnArray, $temp);
            } else {
                $temp = array();
                $modifierDatas = $this->getAllModifierDatas();
                $temp['modifierDatas'] = $modifierDatas;
                $temp['statusMessage'] = 'success';
                $temp['status'] = '0';
                $temp['message'] = 'firstHit';
                array_push($returnArray, $temp);
            }

            if ($statelmd != '') {
                $temp = array();
                $stateNewRecords = State::where('updated_at', '>', $cptlmd)->count();
                if ($stateNewRecords > 0) {
                    $states = $this->getStateData();
                    $temp['stateDatas'] = $states;
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '0';
                    $temp['message'] = 'new Records  Found In State';
                } else {
                    $temp['cptLastModifiedDate'] = '';
                    $temp['statusMessage'] = 'success';
                    $temp['status'] = '1';
                    $temp['message'] = 'new Records Not Found In state';
                }
                array_push($returnArray, $temp);
            } else {
                $temp = array();
                $stateData = $this->getStateData();
                $temp['stateData'] = $stateData;
                $temp['statusMessage'] = 'success';
                $temp['status'] = '0';
                $temp['message'] = 'firstHit';
                array_push($returnArray, $temp);
            }


            $RetrunDatas = array(
                'updateStatus' => $returnArray,
                'status' => '0',
                'statusMessage' => 'success',
            );
            return Response::json($RetrunDatas);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * check The authenticate process
     * @param authenticationid
     * @param $device_id Description
     * @return JSON Response
     */
    public function checkapp_authenticate($authenticationid, $device_id)
    {
        try {
            $app_user_count = UsersAppDetails::where('authentication_id', $authenticationid)->where('mobile_id', $device_id)->count();
            if ($app_user_count > 0) {
                $result = 'success';
            } else {
                $result = 'failure';
            }
            return $result;
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * getAboutData
     * make the About String and return it
     * @return JSON Response
     */

    public function getAboutData()
    {
        $str = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.";
        $RetrunDatas = array(
            'aboutData' => $str,
            'status' => '0',
            'statusMessage' => 'success',
        );
        return Response::json($RetrunDatas);

    }

    /**
     * getMapedModifierwith Cpt
     * @param \Illuminate\Http\Request
     * get the all the maped modifierList with cptDatas Arr
     * @return JSON Response
     */
    public function getMapedModifierWithCpt()
    {
        try {
            $request = Request::all();
            $cpt_arr = $request['cptDatas'];
            $allData = array();
            foreach ($cpt_arr as $val) {
                $temp = array();
                $temp1 = array();
                $modifierArr = array();
                $cptId = $val['id'];
                $cptRecords = AdminCpt::where('id', $cptId)->first();
                $allowed_amount = $cptRecords['allowed_amount'];
                $billed_amount = $cptRecords['billed_amount'];
                $modifiersId = $cptRecords['modifier_id'];
                $array = explode(',', $modifiersId);
                $modifiers = DB::table('modifiers')
                    ->where('deleted_at',NULL)
                    ->whereIn('id', $array)
                    ->get();
                foreach ($modifiers as $modifier) {
                    $temp['id'] = $modifier->id;
                    $temp['code'] = $modifier->code;
                    $temp['modifiersTypeId'] = $modifier->modifiers_type_id;
                    $temp['name'] = $modifier->name;
                    $temp['description'] = $modifier->description;
                    $temp['anesthesiaBaseUnit'] = $modifier->anesthesia_base_unit;
                    $temp['status'] = $modifier->status;
                    $temp['created_by'] = $modifier->created_by;
                    $temp['updated_by'] = $modifier->updated_by;
                    $temp['created_at'] = Carbon::parse($modifier->created_at)->toDateTimeString();
                    $temp['updated_at'] = Carbon::parse($modifier->updated_at)->toDateTimeString();
                    $temp['deleted_at'] = $modifier->deleted_at;
                    array_push($modifierArr, $temp);
                }
                $temp1["id"] = $cptId;
                $temp1["datas"]= $modifierArr;
                $temp1["allowed_amount"] = $allowed_amount;
                $temp1["billed_amount"] = $billed_amount;
                array_push($allData, $temp1);
            }
            if (count($allData) > 0) {
                $RetrunDatas = array(
                    'modifierDatas' => $allData,
                    'status' => '0',
                    'statusMessage' => 'success',
                );
            } else {
                $RetrunDatas = array(
                    'modifierDatas' => $allData,
                    'status' => '1',
                    'statusMessage' => 'success',
                );
            }
            return Response::json($RetrunDatas);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * Add New Claims
     * @param \Illuminate\Http\Request
     * Form spacific practice
     * Create  or update the patient claims,notes and patient
     * @return JSON Response
     */
    public function addNewClaims()
    {
        try {
            $request = Request::all();    
            $requestData = $request['patientData'];
            $practiceId = $requestData['practiceId'];
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceId);
            $authenticationId = $requestData['authenticationId'];
            $patientId = $requestData['patientId'];

            // create new patient
            if ($patientId == 0) {
                $newpatient = array(
                    'is_self_pay' => $requestData['selfPay'],
                    'last_name' => $requestData['lastName'],
                    'first_name' => $requestData['firstName'],
                    'dob' => $requestData['dob'],
                    'gender' => $requestData['gender'],
                    'ssn' => $requestData['ssn'],
                    'medical_chart_no' => $requestData['mrn'],
                    'address1' => $requestData['address1'],
                    'address2' => $requestData['address2'],
                    'city' => $requestData['city'],
                    'state' => $requestData['state'],
                    'zip4' => $requestData['zip4'],
                    'zip5' => $requestData['zip5'],
                    'patient_from' => 'app', //app is default
                    'demo_percentage' => '60',
                    'percentage' => '60',
                    'statements' => 'Yes',
                    'email_notification' => 'No',
                    'phone_reminder' => 'No',                    
                    'status' => 'active'
                );
                $newpatient['bill_cycle'] = $this->getPatientBillingCycle($requestData['lastName']);
                $new_patient = Patient::create($newpatient);
                $pat_id = $new_patient->id;
                $claim_num = $this->create_patient_accno($pat_id, $practiceId);
                $new_patient->account_no = $claim_num;
                $new_patient->patient_from = 'app';
                $new_patient->save();
                $requestData['patientId'] = $patientId = $pat_id;
            }

            $admitDate = $requestData['admitDate'];
            $actionType = $requestData['actionType'];
            $Insurance = $requestData['Insurance'];
            $selfPay = $requestData['selfPay'];
            $dos = $requestData['dos'];
            $notesList = $requestData['NotesList'];
            $notesStatus = '';
            
            
            $addedIcds = $requestData['AddedIcds'];
            $addedCpts = $requestData['AddedCpts'];
            $dos = $requestData['dos'];
            $icdcods = '';

            // Set ICDS upto 12 - Start
            foreach (array_flatten($addedIcds) as $key => $icds) {
                $icd['icd'.++$key] = $icds;
                $j=$key;
            }
            
            for($ko=$j+1;$ko<=12;$ko++)
                $icd['icd'.$ko] = '';
            // Set ICDS upto 12 - End
            
            // Multiple CPTs
            foreach ($addedCpts as $k => $c) {
                // Set ICD map upto 12 - Start
                foreach ($c['icds'] as $ki => $ci) {
                    $cpt_icd_map_key['cpt_icd_map_key'][$k][$ki] = $ci['location'];
                    $cpt_icd_map['cpt_icd_map'][$k][$ki] = $ci['code'];
                    $icd_loc['icd'.++$ki."_".$k] = $ci['location'];
                }

                $cpt_icd_map_key['cpt_icd_map_key'][$k] = implode (",", $cpt_icd_map_key['cpt_icd_map_key'][$k]);
                $cpt_icd_map['cpt_icd_map'][$k] = implode (",", $cpt_icd_map['cpt_icd_map'][$k]);
                
                for($i=$ki+1;$i<=12;$i++)
                    $icd_loc['icd'.$i."_".$k] = '';
                // Set ICD map upto 12 - End
                
                $copay_applied['copay_applied'][$k] = 0;
                $cpt['cpt'][$k] = $c['id'];
                $unit['unit'][$k] = $c['units'];
                $modifier1['modifier1'][$k] = $c['modifier1'];
                $modifier2['modifier2'][$k] = $c['modifier2'];
                $modifier3['modifier3'][$k] = $c['modifier3'];
                $modifier4['modifier4'][$k] = $c['modifier4'];
                $dos_from['dos_from'][$k] = $requestData['dos'];
                $dos_to['dos_to'][$k] = $requestData['dos'];
                $refering_provider_count['refering_provider_count'][$k] = 0;
                $copay_Transcation_ID['copay_Transcation_ID'][$k] = '';
                $box_ids['box_ids'][$k] = '';
                $box_24_AToG['box_24_AToG'][$k] = '';
                // CPT Amount
                $cpt_amt['cpt_amt'][$k] = 1*CPT::where('cpt_hcpcs',$c['id'])->pluck('billed_amount')->first();
                // Charge Amount calculation
                $charge['charge'][$k] = $c['units']*CPT::where('cpt_hcpcs',$c['id'])->pluck('billed_amount')->first();
                $cpt_allowed['cpt_allowed'][$k] = 1*CPT::where('cpt_hcpcs',$c['id'])->pluck('allowed_amount')->first();
            }

            if(!empty(PatientInsurance::where('patient_id',$patientId)->where('category','Primary')->first())){
                $insurance_id ='primary-'.PatientInsurance::where('patient_id',$patientId)->where('category','Primary')->pluck('insurance_id')->first();
            }else{
                $insurance_id = 'self';
            }

            $newClaim = array(
                'self' => ($requestData['responsibility']=='Self Pay')?1:0,
                "charge_add_type" => 'app',
                "status" => 'Hold',
                "rendering_provider_id" => $requestData['providerId'],
                "refering_provider_id" => $requestData['referringProviderId'],
                "billing_provider_id" => 0,
                "facility_id" => $requestData['facilityId'],
                "self_pay" => $requestData['selfPay'],
                'is_hold' => 1,
                'claim_detail_id' => NULL,
                'claim_other_detail_id' => NULL,
                'ambulance_billing_id' => NULL,
                'claim_id' => NULL,
                'statelicence' => NULL,
                'statelicence_billing' => NULL,
                'taxanomy' => NULL,
                'upinno' => NULL,
                'patient_age' => NULL,
                'jsqueryvalue' => NULL,
                'refering_provider' => NULL,
                'facility_clai_no' => NULL,
                'insurance_id' => ($requestData['responsibility']=='Self Pay')?'self':$insurance_id,
                'insurance_category' => (isset($requestData['responsibility']) && $requestData['responsibility']!='Self Pay')?'Primary':'',
                'providertypeid' => 5,
                'patient_id' => Helpers::getEncodeAndDecodeOfId($requestData['patientId'], 'encode'),
                'charge_add_type' => 'billing',
                'rendering_provider_id' => 4,
                'admit_date' => $requestData['dos'],
                'small_date' => $requestData['dos'],
                'big_date' => $requestData['dos'],
                'anesthesia_start' => '',
                'anesthesia_stop' => '',
                'authorization_id' => $requestData['authenticationId'],
                'anesthesia_minute' => '',
                'anesthesia_unit' => '',
                'total_charge' => array_sum(array_flatten($charge)),
                'pos_id' => Facility::where('id',$requestData['facilityId'])->value('pos_id'),
                'auth_no' =>'' ,
                'doi' => '',
                'discharge_date' => NULL,
                'userId'=>$requestData['userId'],
                'note' =>(isset($requestData['NotesList']) && !empty($requestData['NotesList']))?$requestData['NotesList'][0]['content']:'',
                'refering_provider_count' => $refering_provider_count,
                'note_type'=>'app'
            )+$icd+$cpt_icd_map_key+$cpt_icd_map+$icd_loc+$copay_applied+$cpt+$unit+$modifier1+$modifier2+$modifier3+$modifier4+$cpt_amt+$charge+$cpt_allowed+$copay_Transcation_ID+$box_ids+$box_24_AToG+$dos_from+$dos_to;

            $charge = new ChargeV1ApiController();
            $returnData = $charge->createCharge($newClaim);
            $climsId = $returnData->getData()->data;
            $claim_num = $this->generateclaimnumber('', $climsId);
            // New notes create
            foreach ($notesList as $note) {
                $title = $note['title'];
                $content = $note['content'];
                $created_by = $note['created_by'];
                $newnotes = array(
                    'title' => $title,
                    'content' => 'App Notes: '.$content,
                    'notes_type' => 'patient',
                    'patient_notes_type' => "claim_notes",
                    'notes_type_id' => $patientId,
                    'created_by' => $requestData['userId'],
                    'created_at' => Date('Y-m-d H:i:s'),
                    'claim_id' => $climsId
                );
                $nodesCheck = PatientNote::create($newnotes);
                if ($nodesCheck->id) {
                    $notesStatus = 'New Notes Added Successfully';
                } else {
                    $notesStatus = 'Failed to Add New Notes';
                }
            }

            // New document upload
            /*$document_upload = $this->document_upload($request,$practiceId,$patientId,$claim_num,$requestData['userId']);
            if ($document_upload) {
                $documentStatus = 'New document Added Successfully';
            } else {
                $documentStatus = 'Failed to Add New document';
            }*/

            // Update hold option
            $hold_option = \App\Models\Holdoption::where('option', 'claim from app - insurance not selected');
            $hold_optionCount = $hold_option->count();
            $hold_optionId = '';
            if ($hold_optionCount > 0) {
                $record = $hold_option->first();
                $hold_optionId = $record->id;
            } else {
                $newOption = array(
                    'option' => 'claim from app - insurance not selected',
                    'created_by' => $requestData['providerId']
                );
                $newOptionRecord = \App\Models\Holdoption::create($newOption);
                $hold_optionId = $newOptionRecord->id;
            }

            // Update claim number and hold reason
            $clim = ClaimInfoV1::find($returnData->getData()->data);
            $clim->claim_number = $claim_num;
            $clim->hold_reason_id = $hold_optionId;
            $clim->save();

            // Return response for claim, notes, patient and document upload
            if ($climsId > 0) {
                $returnData = array(
                    'status' => '0',
                    'statusMessage' => 'success',
                    'claimsStatus' => 'new claims added successfully',
                    'notesStatus' => $notesStatus,
                    //'documentStatus' => $documentStatus,
                    'patientId' => $patientId,
                    'claim_number' => $claim_num
                );
                return Response::json($returnData);
            } else {
                $returnData = array(
                    'status' => '1',
                    'statusMessage' => 'failure',
                    'claimsStatus' => 'New claims added Failed',
                    'notesStatus' => $notesStatus,
                    //'documentStatus' => $documentStatus,
                    'patientId' => $patientId
                );
                return Response::json($returnData);
            }
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    function addNewPatientFromChromeExtension()
    {
        try {
            $request = Request::all();
            $requestData = $request['patientData'];
            $requestData['dob'] = date('Y-m-d', strtotime(str_replace('-', '/', $requestData['dob'])));
            $requestData['is_self_pay'] = '';
            $requestData['mrn'] = '';
            $practiceId = $requestData['practiceId'];
            $db = new DBConnectionController();

            $newpatient = array(
                'last_name' => $requestData['lastName'],
                'first_name' => $requestData['firstName'],
                'dob' => $requestData['dob'],
                'gender' => $requestData['gender'],
                'ssn' => $requestData['ssn'],
                'middle_name' => $requestData['middleName'],
                'address1' => $requestData['address1'],
                'address2' => $requestData['address2'],
                'city' => $requestData['city'],
                'state' => $requestData['state'],
                'zip4' => $requestData['zip4'],
                'zip5' => $requestData['zip5'],
                'patient_from' => 'app', //app is default
                'demo_percentage' => '60',
                'percentage' => '60',
                'status' => 'active',
                'practiceId' => $practiceId
            );
            $newpatient['bill_cycle'] = $this->getPatientBillingCycle($requestData['lastName']);

            //practice validation
            Validator::extend('practiceIdV', function ($attribute, $value, $parm) {
                $practice_info = Practice::on("responsive")->where('id', $parm)->count();
                return ($practice_info > 0) ? true : false;
            }, 'Practice Id is not fount');

            $rules = Patient::$rules + array(
                    'dob' => 'required|date|after:"1901-01-01"',
                    'practiceId' => 'required|practiceIdV:' . $requestData['practiceId'],
                );

            $validator = Validator::make($newpatient, $rules, Patient::$messages + array('filefield.mimes' => Config::get('siteconfigs.customer_image.defult_image_message')));
            if ($validator->fails()) {
                $errors = $validator->errors();
                return Response::json(
                    array(
                        'patientId' => '0',
                        'status' => '0',
                        'statusMessage' => 'FAILED',
                        'errorMessage' => $errors,
                        'data' => ''));
            } else {
                $db->connectPracticeDB($practiceId);
                $count = Patient::
                where('ssn', $newpatient['ssn'])->
                where('dob', $newpatient['dob'])->count();
                if ($count > 0) {
                    $resultStatus = array(
                        'patientId' => '',
                        'status' => '0',
                        'statusMessage' => 'FAILED',
                        'errorMessage' => array("ssn" => array('ssn is already exists')),
                    );
                    return Response::json($resultStatus);
                } else {
                    $newPatientId = Patient::create($newpatient);
                    $pat_id = $newPatientId->id;
                    $claim_num = $this->create_patient_accno($pat_id);
                    $newPatientId->account_no = $claim_num;
                    $newPatientId->save();
                    if ($newPatientId > '0') {
                        $resArr = array(
                            'patientId' => $pat_id,
                            'status' => '1',
                            'statusMessage' => 'SUCCESS',
                            'errorMessage' => '',
                        );
                        return Response::json($resArr);
                    } else {
                        $resArr = array(
                            'patientId' => '',
                            'status' => '0',
                            'statusMessage' => 'FAILED',
                            'errorMessage' => 'Invalid credential. Contact admin',
                        );
                        return Response::json($resArr);
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::info("Error " . $e->getMessage());
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '0', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    public function checkSsn()
    {
        try {
            $request = Request::all();
            if (!empty($request)) {
                Validator::extend('practiceIdV', function ($attribute, $value, $parm) {
                    $practice_info = Practice::on("responsive")->where('id', $parm)->count();
                    return ($practice_info > 0) ? true : false;
                }, 'Practice Id is not fount');

                $validator = Validator::make($request, [
                    //'dob' => 'required',
                    //'dob' => 'date|after:"1901-01-01"',
                    'ssn' => 'required',
                    'practiceId' => 'required|practiceIdV:' . $request['practiceId'],
                ]);

                if ($validator->fails()) {
                    $resultStatus = array(
                        'icdDatas' => '',
                        'status' => '0',
                        'statusMessage' => $validator->errors(),
                    );
                    return Response::json($resultStatus);
                } else {
                    $db = new DBConnectionController();
                    $db->connectPracticeDB($request['practiceId']);
                    $count = Patient::where('ssn', $request['ssn'])->where('status','Active')->count();
                    if ($count > 0) {
                        $resultStatus = array(
                            'status' => '1',
                            'statusMessage' => 'ssn is already exists',
                        );
                        return Response::json($resultStatus);
                    } else {
                        $resultStatus = array(
                            'status' => '0',
                            'statusMessage' => 'ssn is not found',
                        );
                        return Response::json($resultStatus);
                    }
                }

            }
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    public function getPatientBillingCycle($lastName)
    {
        $first_letter = substr($lastName, 0, 1);
        $first_letter = strtoupper($first_letter);
        $billingCycle = '';
        $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
        for ($i = 0; $i < count($bill_cycle_arr); $i++) {
            $str_arr = explode('-', $bill_cycle_arr[$i]);
            if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                $billingCycle = $str_arr[0] . " - " . $str_arr[1];
            }
        }
        return $billingCycle;
    }

    function addNewPatient($requestData)
    {
        try {

            $newpatient = array(
                'is_self_pay' => $requestData['selfPay'],
                'last_name' => $requestData['lastName'],
                'first_name' => $requestData['firstName'],
                'dob' => $requestData['dob'],
                'gender' => $requestData['gender'],
                'ssn' => $requestData['ssn'],
                'middle_name' => $requestData['middleName'],
                'medical_chart_no' => $requestData['mrn'],
                'address1' => $requestData['address1'],
                'address2' => $requestData['address2'],
                'city' => $requestData['city'],
                'state' => $requestData['state'],
                'zip4' => $requestData['zip4'],
                'zip5' => $requestData['zip5'],
            );
            $new_patient = Patient::create($newpatient);
            return $new_patient->id;

        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * search Icd Datas
     * @param \Illuminate\Http\Request
     * with Help of sstr to get the all the Ices Datas
     * @return  all the Modifer Arrays with mlmd
     */
    function searchIcdDatas()
    {
        try {
            $request = Request::all();
            $sStr = $request['sstr'];
            if ($sStr != '') {
                $icdDatas = AdminIcd::where('icd_code', 'like', '%' . $sStr . '%')
                    ->orwhere('short_description', 'like', '%' . $sStr . '%')
                    ->orwhere('medium_Description', 'like', '%' . $sStr . '%')
                    ->orwhere('long_description', 'like', '%' . $sStr . '%')
                    ->get();
                if (count($icdDatas) > 0) {
                    $icdDatasArr = array();
                    foreach ($icdDatas as $icdData) {
                        $temp['recordId'] = $icdData->id;
                        $temp['icdId'] = $icdData->icd_code;
                        $temp['icdType'] = $icdData->icd_type;
                        $temp['header'] = $icdData->header;
                        $temp['shortDescription'] = ucwords(strtolower($icdData->short_description));
                        $temp['mediumDescription'] = $icdData->medium_description;
                        $temp['longDescription'] = $icdData->long_description;
                        $temp['statementDescription'] = $icdData->statement_description;
                        $temp['sex'] = $icdData->sex;
                        $temp['effectiveDate'] = $icdData->effectivedate;
                        $temp['inactiveDate'] = $icdData->inactivedate;
                        $temp['updatedAt'] = Carbon::parse($icdData->updated_at)->toDateTimeString();
                        array_push($icdDatasArr, $temp);
                    }

                    $icdRetrunData = array(
                        'icdDatas' => $icdDatasArr,
                        'status' => '0',
                        'statusMessage' => 'success',
                    );
                } else {
                    $icdRetrunData = array(
                        'icdDatas' => '',
                        'status' => '1',
                        'statusMessage' => 'Search!No ICD available with searched criteria',
                    );
                }
            } else {
                $icdRetrunData = array(
                    'icdDatas' => '',
                    'status' => '1',
                    'statusMessage' => 'search String Required',
                );
            }
            return Response::json($icdRetrunData);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get modifier List
     * @param \Illuminate\Http\Request
     * Form spacific practice
     * get all modifier datas
     * @return JSON Response
     */
    public function getModifierListwithlmd()
    {
        $request = Request::all();
        try {
            $practiceid = $request['practiceid'];
            $lmd = $request['lmdate'];
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceid);
            $modifier = Modifier::all();
            $modifierLastModifiedDate = $modifier->sortByDesc('updated_at')->first();
            if ($lmd != '') {
                $modifierNewRecords = Modifier::where('updated_at', '>', $lmd)->count();

                if ($modifierNewRecords > 0) {
                    $newModifierArr = $this->getAllModifierDatas();
                    $modifierArr = array(
                        'modifierData' => $newModifierArr,
                        'status' => '1',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'new Records Found In Modifier',
                        'modifierLastModifiedDate' => Carbon::parse($modifierLastModifiedDate->updated_at)->toDateTimeString()
                    );
                    return Response::json($modifierArr);
                } else {
                    $modifierArr = array(
                        'modifierData' => '',
                        'status' => '1',
                        'statusMessage' => 'success',
                        'updateCheckingStatus' => 'new Records Not Found In Modifier',
                    );
                    return Response::json($modifierArr);
                }
            } else {
                $modifierArr = $this->getAllModifierDatas();
            }
            $facilityReturnData = array(
                'modifierData' => $modifierArr,
                'status' => '0',
                'statusMessage' => 'success',
                'modifierLastModifiedDate' => Carbon::parse($modifierLastModifiedDate->updated_at)->toDateTimeString()
            );
            return Response::json($facilityReturnData);
        } catch (\Exception $e) {
            $this->showErrorResponse('chargeCaptureApp', $e);
            return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /**
     * get All ModifierDatas
     * Fetch All the Modifier Datas
     * @return  all the Modifer Arrays with mlmd
     */
    function getAllModifierDatas()
    {
        $modifierArr = array();
        $modifiers = Modifier::all();
        $temp = array();
        foreach ($modifiers as $modifier) {
            $temp['id'] = $modifier->id;
            $temp['code'] = $modifier->code;
            $temp['modifiersTypeId'] = $modifier->modifiers_type_id;
            $temp['name'] = $modifier->name;
            $temp['description'] = $modifier->description;
            $temp['anesthesiaBaseUnit'] = $modifier->anesthesia_base_unit;
            $temp['status'] = $modifier->status;
            $temp['created_by'] = $modifier->created_by;
            $temp['updated_by'] = $modifier->updated_by;
            $temp['created_at'] = Carbon::parse($modifier->created_at)->toDateTimeString();
            $temp['updated_at'] = Carbon::parse($modifier->updated_at)->toDateTimeString();
            $temp['deleted_at'] = Carbon::parse($modifier->deleted_at)->toDateTimeString();
            array_push($modifierArr, $temp);
        }
        $modifierlmd = $modifiers->sortByDesc('updated_at')->first();

        $modifierLastModifiedDate = "";
        if ($modifierlmd) {
            $modifierLastModifiedDate = Carbon::parse($modifierlmd->updated_at)->toDateTimeString();
        }

        $modifierRetrunData = array(
            'modifierDatas' => $modifierArr,
            'modifierLastModifiedDate' => $modifierLastModifiedDate
        );
        return $modifierRetrunData;
    }

    /**
     * get All the faq form admin Table
     * @return  all the Questions
     */
    function getAllFaq()
    {
        $faq = FAQ::select('id', 'question', 'answer', 'status', 'category')
            ->where('status', 'Active')
            ->where('deleted_at', NULL)
            ->where('category', 'CHARGECAPTURE');
        $count = $faq->count();
        if ($count > 0) {
            $returnData = array(
                'faqDatas' => $faq->get(),
                'status' => '0',
                'statusMessage' => 'success',
            );
        } else {
            $returnData = array(
                'faqDatas' => $faq,
                'status' => '1',
                'statusMessage' => 'success',
            );
        }
        return Response::json($returnData);
    }

    /**
     * get All favouriteCpt Datas
     * @practiceId
     * @return  all the cptDatas
     */
    function getAllFavCptDatas($practiceId)
    {
        $cptDatasArr = array();
        $db = new DBConnectionController();
        $db->connectPracticeDB($practiceId);
        $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
        $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
        $getLmdData = Cpt::whereIn('id', $favourites_ids)->where('status', "Active")->pluck('id')->all();
        $rowcount = Favouritecpts::whereIn('cpt_id', $getLmdData)->count();
        $lmdDataFromFav = Favouritecpts::whereIn('cpt_id', $getLmdData)->get();
        $cptDatas = Cpt::whereIn('id', $favourites_ids)->where('status', "Active")->get();
        $cptDatasArr = array();
        $temp = array();
        foreach ($cptDatas as $cptData) {
            $temp['recordId'] = $cptData->id;
            $temp['cptHcpcs'] = $cptData->cpt_hcpcs;
            $temp['codeType'] = $cptData->code_type;
            $temp['shortDescription'] = $cptData->short_description;
            $temp['mediumDescription'] = ucwords(strtolower($cptData->medium_description));
            $temp['longDescription'] = $cptData->long_description;
            $temp['ageLimit'] = $cptData->age_limit;
            $temp['billedAmount'] = $cptData->billed_amount;
            $temp['allowedAmount'] = $cptData->allowed_amount;
            $temp['posId'] = $cptData->pos_id;
            $temp['typeOfService'] = $cptData->type_of_service;
            $temp['updatedAt'] = Carbon::parse($cptData->updated_at)->toDateTimeString();
            array_push($cptDatasArr, $temp);
        }
        $cptflmd = $lmdDataFromFav->sortByDesc('updated_at')->first();
        $cptLastModifiedDate = "";
        if ($cptflmd) {
            $cptLastModifiedDate = Carbon::parse($cptflmd->updated_at)->toDateTimeString();
        }

        $cptRetrunData = array(
            'cptDatas' => $cptDatasArr,
            'cptLastModifiedDate' => $cptLastModifiedDate,
            'cptCount' => $rowcount
        );
        return $cptRetrunData;
    }
    function getAllFavCptDatas_old($practiceId)
    {
        $cptDatasArr = array();
        $db = new DBConnectionController();
        $db->connectPracticeDB($practiceId);
        $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
        $favourites_ids = Favouritecpts::pluck("cpt_id")->all();
        $getLmdData = AdminCpt::whereIn('id', $favourites_ids)->where('status', "Active")->pluck('id')->all();
        $rowcount = Favouritecpts::whereIn('cpt_id', $getLmdData)->count();
        $lmdDataFromFav = Favouritecpts::whereIn('cpt_id', $getLmdData)->get();
        $cptDatas = AdminCpt::whereIn('id', $favourites_ids)->where('status', "Active")->get();
        $cptDatasArr = array();
        $temp = array();
        foreach ($cptDatas as $cptData) {
            $temp['recordId'] = $cptData->id;
            $temp['cptHcpcs'] = $cptData->cpt_hcpcs;
            $temp['codeType'] = $cptData->code_type;
            $temp['shortDescription'] = $cptData->short_description;
            $temp['mediumDescription'] = ucwords(strtolower($cptData->medium_description));
            $temp['longDescription'] = $cptData->long_description;
            $temp['ageLimit'] = $cptData->age_limit;
            $temp['billedAmount'] = $cptData->billed_amount;
            $temp['allowedAmount'] = $cptData->allowed_amount;
            $temp['posId'] = $cptData->pos_id;
            $temp['typeOfService'] = $cptData->type_of_service;
            $temp['updatedAt'] = Carbon::parse($cptData->updated_at)->toDateTimeString();
            array_push($cptDatasArr, $temp);
        }
        $cptflmd = $lmdDataFromFav->sortByDesc('updated_at')->first();
        $cptLastModifiedDate = "";
        if ($cptflmd) {
            $cptLastModifiedDate = Carbon::parse($cptflmd->updated_at)->toDateTimeString();
        }

        $cptRetrunData = array(
            'cptDatas' => $cptDatasArr,
            'cptLastModifiedDate' => $cptLastModifiedDate,
            'cptCount' => $rowcount
        );
        return $cptRetrunData;
    }

    /**
     * getStateData
     * get the all the state Datas
     * @return  all state datas arr
     */
    function getStateData()
    {
        $states = State::all();
        $temp = array();
        $stateArr = array();
        foreach ($states as $state) {
            $temp['state'] = $state->state;
            $temp['code'] = $state->code;
            $temp['created_at'] = Carbon::parse($state->created_at)->toDateTimeString();
            $temp['updated_at'] = Carbon::parse($state->updated_at)->toDateTimeString();
            $temp['deleted_at'] = Carbon::parse($state->deleted_at)->toDateTimeString();
            array_push($stateArr, $temp);
        }
        $statelmd = $states->sortByDesc('updated_at')->first();
        if ($statelmd) {
            $stateLastModifiedDate = Carbon::parse($statelmd->updated_at)->toDateTimeString();
        } else {
            $stateLastModifiedDate = '';
        }

        $stateRetrunData = array(
            'stateDatas' => $stateArr,
            'stateLastModifiedDate' => $stateLastModifiedDate
        );

        return $stateRetrunData;
    }

    /**
     * generate claim number
     * @param type
     * @param claimId
     * @return ClaimNumber
     */
    public function generateclaimnumber($type, $claim_id)
    {
        $claim_number = str_pad($claim_id, 5, '0', STR_PAD_LEFT);
        return $claim_number;
    }

    
   

    /**
     * create patient account Number
     * @param patienId
     * make the new patientAccoutn Number
     * @return Account Number
     */
    public function create_patient_accno($pat_id, $practiceId)
    {        
        // Append prefix practice
        $practice = Practice::where('id', $practiceId)->first();

        $practice_name = @$practice->practice_name;;        
        $practice_Arr = array_map('trim', explode(" ", $practice_name));
        if(COUNT($practice_Arr) > 2 ) {
            $practice_prefix = $practice_Arr[0][0].$practice_Arr[1][0].$practice_Arr[2][0];    
        } elseif(COUNT($practice_Arr) > 1 ) {
            $practice_prefix = $practice_Arr[0][0].substr($practice_Arr[1],0,2);
        } elseif(COUNT($practice_Arr) == 1) {
            $practice_prefix = substr($practice_Arr[0],0,2);
        } else {
            $practice_prefix = ''; // Default
        }
        $acc_no = strtoupper($practice_prefix).str_pad($pat_id, 5, '0', STR_PAD_LEFT);        

        \Log::info("In charge Capture patient create - Patient Account No".$acc_no." ## ".$pat_id);
        return $acc_no;
    }

    /**
     * @param provider type 2,3,4
     * @return Referring, Ordering and Supervising List
     */
    public static function getReferringOrderingSupervising()
    {
        $referring_providers = array();

        $provider_type = array(Config::get('siteconfigs.providertype.Referring'), Config::get('siteconfigs.providertype.Ordering'), Config::get('siteconfigs.providertype.Supervising'));
        $query = DB::table('providers as p')
                ->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')
                ->join('provider_types as pt', 'pt.id', '=', 'p.provider_types_id')
                ->selectRaw('CONCAT(SUBSTR(pt.name, 1,1),"-", p.provider_name," ",REPLACE(pd.degree_name," ","")) as concatname, p.id')
                ->where('p.status', '=', 'Active')
                ->whereIn('provider_types_id', $provider_type)
                ->where('p.deleted_at', NULL);
        
        $referring_providers = $query->selectRaw("p.id AS id")->orderBy('provider_name', 'ASC')->pluck('concatname', 'id')->all();  
        return $referring_providers;  
    }

    /**
     * @param \Illuminate\Http\Request
     * handling The Error
     * @return Account Number
     */
    public function checkProcess()
    {
        $errorArr = array();
        $request = Request::all();
        $errorArr = array();
        $errorReport = $this->handlingError($request['name'], 'name');
        array_push($errorArr, $errorReport);
        $errorReport = $this->handlingError($request['pass'], 'pass');
        array_push($errorArr, $errorReport);
        dd($errorArr);
    }

    /**
     * @param fatch_all_provider_type
     * @type_id
     * @return Patient Account Number
     */
    public function handlingError($value, $name)
    {
        if ($value == "" || $value == "NULL" || $value == "null") {
            $temp = [];
            $temp["" . $name . ""] = ucwords($name) . " is Empty";
            $temp["error Code"] = '24';
            return $temp;
        }
    }
    // Author: Baskar
    // Document Upload
    public function documentUpload(){
        $request = Request::all();
        $validator = Validator::make($request, [
                    'practiceId' => 'required',
                    'patientId' => 'required',
                    'title' => 'required',
                    'category_key' => 'required',
                    'category_id' => 'required',
                    'user_id' => 'required',
                    'claim_number' => 'required'
                ]);
        if ($validator->fails()) {
            return Response::json(array('status' => '1','statusMessage' => 'failure','errors' => $validator->errors()));
        } else {
            //\Log::info('chargeCaptureApp'.json_encode($request));
            $practice_name = md5('P' . $request['practiceId']);
            $db = new DBConnectionController();
            $db->connectPracticeDB($request['practiceId']);
            $type = 'patients';
            if (Input::hasFile('document_file'))
            {
                $image              = Input::file('document_file');
                $filename           = $request['category_key'].'_'.md5(strtotime(date('Y-m-d H:i:s')));
                $extension          = $image->getClientOriginalExtension();
                $OriginalName       = $image->getClientOriginalName();
                $mimeType           = $image->getClientmimeType();
                $filesize           = $image->getClientSize();
                $file_store_name    = $filename .'.'.$extension;
                $file = Request::file('document_file');
                $src    = '';
                try{
                    $store_arr = Helpers::amazon_server_folder_check($type,$file,$file_store_name,$src,$practice_name);
                    $documents = Document::where('title','like',$request['title'].'%')->where('type_id', $request['patientId'])->where('document_type','patients')->count();
                    if($documents==0)
                        $title = $request['title'];
                    else
                        $title = $request['title']."(".$documents.")";
                    $data['practice_id'] = $request['practiceId'];
                    $data['filesize'] = number_format(($filesize/1024),2);
                    $data['type_id'] = $request['patientId'];
                    $data['upload_type'] = 'browse';
                    $data['document_type'] = 'patients';
                    $data['document_extension'] = $extension;
                    $data['title'] = $title;
                    $data['description'] = $request['document_notes'];
                    $data['category'] = $request['category_key'];
                    $data['document_categories_id'] = $request['category_id'];
                    $data['mime'] = $mimeType;
                    $data['original_filename'] = $OriginalName;
                    $data['created_by'] = $request['user_id'];
                    $data['main_type_id'] = $request['patientId'];
                    $data['claim_number_data'] = $request['claim_number'];
                    $data['document_path'] = $store_arr[0];
                    $data['document_domain'] = $store_arr[1];
                    $data['filename'] = $file_store_name;
                    $document = Document::create($data);
                    if (!$document) {
                        return Response::json(array('status' => '1','statusMessage' => 'failure'));
                    } else {
                        $document_followup['document_id'] = $document->id;
                        $document_followup['patient_id'] = $request['patientId'];
                        $document_followup['claim_id'] = $request['claim_number'];
                        $document_followup['assigned_user_id'] = User::where('customer_id',Practice::where('id',$request['practiceId'])->pluck('customer_id')->first())->pluck('id')->first();
                        $document_followup['followup_date'] = date('Y-m-d');
                        $document_followup['priority'] = 'Low';
                        $document_followup['status'] = 'Pending';
                        $document_followup['Assigned_status'] = 'Inactive';
                        $document_followup['notes'] = $request['document_notes'];
                        $document_followup['created_by'] = $request['user_id'];
                        $DocumentFollowupList= DocumentFollowupList::create($document_followup);
                        return Response::json(array('status' => '0','statusMessage' => 'success', 'document' => $document, 'document_followup'=>$DocumentFollowupList,'appResponse' => json_encode($request)));
                    }
                }catch (\Exception $e) {
                    $this->showErrorResponse('chargeCaptureApp', $e);
                    \Log::info("Image upload".json_encode($e->getMessage()));
                    return Response::json(array('status' => '1', 'statusMessage' => 'Invalid credential. Contact admin','error'=>json_encode($e->getMessage())));
                }
                
            }else{
                return Response::json(array('status' => '1','statusMessage' => 'failure', 'error'=>'document file missing'));
            }
        }
    }

    // Author: Baskar
    // Document category list
    public function getDocumentCategoryList()
    {
        $request = Request::all();
        $validator = Validator::make($request, [
                    'practiceId' => 'required',
                ]);
        if ($validator->fails()) {
            return Response::json(array('status' => '1','statusMessage' => 'failure','errors' => $validator->errors()));
        } else {
            return Response::json(array('status' => '0','statusMessage' => 'success', 'data' => Document_categories::where('module_name','patients')->orderBy('category_value','asc')->get()));
        }
    }

}
