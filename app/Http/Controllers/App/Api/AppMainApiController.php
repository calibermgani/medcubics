<?php

namespace App\Http\Controllers\App\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Medcubics\Users as User;
use App\Models\Medcubics\UsersAppDetails as UsersAppDetails;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Provider as Provider;
use App\Models\Patients\Patient as Patient;
use App\Models\Patients\PatientEligibility as PatientEligibility;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\PatientContact as PatientContact;
use App\Models\Questionnaries as Questionnaries;
use App\Models\QuestionnariesAnswer as QuestionnariesAnswer;
use App\Models\QuestionnariesTemplate as QuestionnariesTemplate;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use App\Models\State as State;
use App\Models\Patients\PatientNote as PatientNote;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\PatientBudget as PatientBudget;
use App\Models\Template;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use Response;
use Hash;
use Request;
use DB;
use Auth;
use Input;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Traits\ClaimUtil;

class AppMainApiController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | AppMainApiController
      | @author last Update by Baskar
      |--------------------------------------------------------------------------
     */
    /*     * * Start check login details  ** */
    use ClaimUtil;

    public function getlogindetails() {
        $request = Request::all();
        try {
            $validator = Validator::make(Request::all(),['username'=>'required','password'=>'required']);
            if ($validator->fails()) {
                return Response::json(array('loginStatus' => '1', 'StatusMessage' => $validator->errors()));
            }else{
                $username = $request['username'];
                $password = $request['password'];
                $device_id = "TEST123"; //@$request['device_id'];
                $user_details = User::where('email', $username)->where('useraccess', 'app')->where('status', 'Active')->first();
                if ($user_details) {
                    $table_val = Hash::check($password, $user_details['password']);
                    if ($table_val == true) {
                        $practice_access_id = $user_details['practice_access_id'];
                        $facility_access_id = $user_details['facility_access_id'];
                        $authenticationId = "U" . $user_details['id'] . md5(strtotime(date('Y-m-d H:i:s')));
                        $app_user_count = UsersAppDetails::where('user_id', $user_details['id'])->where('mobile_id', $device_id)->count();
                        if ($app_user_count == 0) {
                            UsersAppDetails::create(['user_id' => $user_details['id'], 'mobile_id' => $device_id, 'authentication_id' => $authenticationId, 'last_login_time' => date('Y-m-d H:i:s')]);
                        } else {
                            UsersAppDetails::where('user_id', $user_details['id'])->where('mobile_id', $device_id)->update(['authentication_id' => $authenticationId, 'last_login_time' => date('Y-m-d H:i:s')]);
                        }
                        $practice_det = Practices::where('status', 'Active')->where('id', $practice_access_id)->first();
                        $practiceName = $practice_det['practice_name'];
                        $practiceId = $practice_det['id'];
                        $db = new DBConnectionController();
                        $db->connectPracticeDB($practice_det['id']);
                        $facility_det = Facility::where('id', $facility_access_id)->where('status', 'Active')->first();
                        $facilityName = $facility_det['facility_name'];
                        $facilityId = $facility_det['id'];
                        return Response::json(array('loginStatus' => '0', 'StatusMessage' => 'success', 'authenticationId' => $authenticationId, 'userAliasName' => $user_details['name'], 'userID' => $user_details['id'], 'practiceName' => $practiceName, 'practiceId' => $practiceId, 'facilityName' => $facilityName, 'facilityId' => $facilityId));
                    } else {
                        return Response::json(array('loginStatus' => '1', 'StatusMessage' => 'Password invalid'));
                    }
                } else {
                    return Response::json(array('loginStatus' => '1', 'StatusMessage' => 'User name invalid'));
                }
            }
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End check login details  ** */

    /*     * * Start check forgotpassword process  ** */

    public function forgotpasswordprocess() {
        $request = Request::all();
        try {
            $validator = Validator::make(Request::all(),['username'=>'required']);
            if ($validator->fails()) {
                return Response::json(array('loginStatus' => '1', 'StatusMessage' => $validator->errors()));
            }else{
                $username = $request['username'];
                $user_details = User::where('email', $username)->first();
                if (count($user_details)) {
                    $chg_password = "US" . strtotime(date('H:i:s')) . "PI" . $user_details['id'];
                    $user_details->update(['password' => Hash::make($chg_password), 'password_change_time' => date('Y-m-d H:i:s')]);
                    UsersAppDetails::where('user_id', $user_details['id'])->update(['authentication_id' => '']);
                    //Start Send Mail to user
                    $string = " ================== Medcubics New Password ================== \n";
                    $string .= " Time : " . date('H:i:s') . "\n";
                    $string .= " User Name : " . $username . "\n";
                    $string .= " Password : " . $chg_password . "\n";
                    $to = $username;
                    $subject = "Medcubics Forgot Password";
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: Medcubics' . "\r\n";
                    mail($to, $subject, $string, $headers);
                    //End Send Mail to user
                    return Response::json(array('status' => '0', 'StatusMessage' => 'success'));
                } else {
                    return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid user'));
                }
            }
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End check forgotpassword process  ** */

    /*     * * Start check app authenticate process  ** */

    public function checkapp_authenticate($authenticationid, $device_id) {
        try {
            return 'success'; //TEST
            $app_user_count = UsersAppDetails::where('authentication_id', $authenticationid)->where('mobile_id', $device_id)->count();
            if ($app_user_count > 0) {
                $result = 'success';
            } else {
                $result = 'failure';
            }
            return $result;
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End check app authenticate process  ** */


    /*     * * Start getProviderListByschedulardate process ** */

    public function getProviderListByschedulardate() {
        $request = Request::all();
        try {

            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $schedulardate = @$request['schedulardate'];

            if ($schedulardate == "")
                $schedulardate = date('Y-m-d');
            $cur_month = date("m", strtotime($schedulardate));
            $cur_year = date("Y", strtotime($schedulardate));
            $prev_month = date("m", strtotime($schedulardate . " -1 month"));
            $prev_year = date("Y", strtotime($schedulardate . " -1 month"));
            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0) {
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            }
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $total_appointment_today = PatientAppointment::where('facility_id', $facilityid)->where('scheduled_on', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->get()->count();
            $appointment_stats = PatientAppointment::getSchedulerCount($facilityid, $schedulardate, 'facility_id');


            $total_appointment_month = PatientAppointment::where('facility_id', $facilityid)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->whereRaw('YEAR(scheduled_on) = ?', [$cur_year])->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->get()->count();
            $total_appointment_prev_month = PatientAppointment::where('facility_id', $facilityid)->whereRaw('MONTH(scheduled_on) = ?', [$prev_month])->whereRaw('YEAR(scheduled_on) = ?', [$prev_year])->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->get()->count();

            if ($total_appointment_prev_month == 0)
                $diff_month_percentage = $total_appointment_month;
            elseif ($total_appointment_month == 0)
                $diff_month_percentage = -$total_appointment_prev_month;
            elseif ($total_appointment_month > $total_appointment_prev_month)
                $diff_month_percentage = ($total_appointment_month - $total_appointment_prev_month) / $total_appointment_prev_month * 100;
            elseif ($total_appointment_prev_month > $total_appointment_month)
                $diff_month_percentage = -(($total_appointment_prev_month - $total_appointment_month) / $total_appointment_month * 100);
            $percentage_diff_from_last_month = round($diff_month_percentage);

            $provider_list = ProviderSchedulerTime::where('facility_id', $facilityid)->where('schedule_date', $schedulardate)->pluck('provider_id')->all();
            $providers_list = [];
            if (count($provider_list > 0)) {
                $provider_list = array_unique($provider_list);
                $provider_list_res = Provider::with('degrees', 'speciality')->whereIn('id', $provider_list)->orderBy('provider_name', 'ASC')->get();

                if (count($provider_list_res) > 0) {
                    foreach ($provider_list_res as $key => $value) {
                        $providers_list[$key]['provider_id'] = $value->id;
                        $providers_list[$key]['speciality'] = @$value->speciality->speciality;
                        $providers_list[$key]['provider_name'] = $value->provider_name . ' ' . @$value->degrees->degree_name;
                        $providers_list[$key]['provider_phone'] = $value->phone;
                        $providers_list[$key]['provider_ssn'] = $value->ssn;
                        $providers_list[$key]['appointment_count'] = PatientAppointment::where('facility_id', $facilityid)->where('scheduled_on', $schedulardate)->where('provider_id', $value->id)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->get()->count();
                    }
                }
            }

            $templates_list = Template::select('content', 'id')->whereHas('templatetype', function($q) {
                        $q->where('templatetypes', 'App');
                    })->where('status', 'Active')->get()->toArray();

            $templates_list_new = array();
            foreach ($templates_list as $key => $value) {
                $value['content'] = str_replace("##VAR-PRACTICENAME##", $practice_info['practice_name'], $value['content']);
                $value['content'] = str_replace("##VAR-PRACTICEPHNO##", $practice_info['phone'], $value['content']);
                $value['content'] = str_replace("\r\n", '', $value['content']);

                $templates_list_new[$value['id']] = "<!DOCTYPE html><html><body>" . $value['content'] . "</body></html>";
            }

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'appointment_stats' => $appointment_stats, 'percentage_diff_from_last_month' => $percentage_diff_from_last_month, 'total_appointment_today' => $total_appointment_today, 'providers_list' => $providers_list, 'templates_list' => $templates_list_new));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getProviderListByschedulardate process ** */

    /*     * * Start getPatientDetailsByproviderDate process ** */

    public function getPatientDetailsByproviderDate() {
        $request = Request::all();

        try {

            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $providerid = $request['providerid'];
            $schedulardate = $request['schedulardate'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $patient_lists = PatientAppointment::with(array('patient' => function($query) {
                            $query->select('id', 'first_name', 'last_name', 'middle_name', 'address1', 'address2', 'city', 'state', 'zip5', 'zip4', 'is_self_pay', 'gender', 'ssn', 'dob', 'phone', 'email', 'mobile', 'account_no', 'avatar_name', 'avatar_ext');
                        }))->where('facility_id', $facilityid)->where('provider_id', $providerid)->where('scheduled_on', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->orderBy('appointment_time', 'ASC')->get();
            $patient_list_arr = [];
            foreach ($patient_lists as $appointment) {
                $patient_appointment['scheduled_on'] = Helpers::dateFormat($appointment->scheduled_on, 'date');
                $patient_appointment['appointment_time'] = $appointment->appointment_time;
                $patient_appointment['status'] = $appointment->status;
                $patient_appointment['checkin_time'] = $appointment->checkin_time;
                $patient_appointment['checkout_time'] = $appointment->checkout_time;
                $patient_appointment['copay_option'] = $appointment->copay_option;
                $patient_appointment['copay'] = $appointment->copay;
                $patient_appointment['patient_id'] = $appointment->patient->id;
                $patient_appointment['account_no'] = $appointment->patient->account_no;
                $patient_appointment['first_name'] = $appointment->patient->first_name;
                $patient_appointment['last_name'] = $appointment->patient->last_name;
                $patient_appointment['middle_name'] = $appointment->patient->middle_name;
                $patient_appointment['address1'] = $appointment->patient->address1;
                $patient_appointment['address2'] = $appointment->patient->address2;
                $patient_appointment['city'] = $appointment->patient->city;
                $patient_appointment['state'] = $appointment->patient->state;
                $patient_appointment['zip5'] = $appointment->patient->zip5;
                $patient_appointment['zip4'] = $appointment->patient->zip4;
                $patient_appointment['is_self_pay'] = $appointment->patient->is_self_pay;
                $patient_appointment['gender'] = $appointment->patient->gender;
                $patient_appointment['ssn'] = $appointment->patient->ssn;
                $patient_appointment['dob'] = Helpers::dateFormat($appointment->patient->dob, 'dob');
                $patient_appointment['age'] = date_diff(date_create(@$appointment->patient->dob), date_create('today'))->y;
                $patient_appointment['phone'] = $appointment->patient->phone;
                $patient_appointment['email'] = $appointment->patient->email;
                $patient_appointment['mobile'] = $appointment->patient->mobile;

                /// Image ///
                $filename = @$appointment->patient->avatar_name . '.' . @$appointment->patient->avatar_ext;
                $img_details = [];
                $img_details['module_name'] = 'patient';
                $img_details['file_name'] = $filename;
                $img_details['practice_name'] = md5('P' . $practiceid);
                $img_details['need_url'] = 'yes';
                $img_details['alt'] = 'patient-image';
                $patient_appointment['image_tag'] = ($filename!='.')? Helpers::checkAndGetAvatar($img_details): url("/").'/'.Helpers::checkAndGetAvatar($img_details);
                /// Image ///

                $patient_list_arr[] = $patient_appointment;
            }

            $patient_detail_result = [];
            if (count($patient_list_arr) > 0) {
                $def_pat_id = $patient_list_arr[0]['patient_id'];
                $patient_detail_result = $this->getPatientDetails($def_pat_id, $providerid, $schedulardate, $practiceid);
            } else {
                $patient_detail_result = ['' => ''];
            }

            $questionaries_res = QuestionnariesTemplate::with('questionnaries_option')->orderBy('question_order', 'ASC')->get();
            $questionaries = [];
            foreach ($questionaries_res as $k => $question) {
				
                $questionaries[$k]['question_id'] = $question->id;
                $questionaries[$k]['title'] = $question->title;
                $questionaries[$k]['question'] = $question->question;
                $questionaries[$k]['answer_type'] = $question->answer_type;
                $questionaries[$k]['questionnaries_option'] = [];
                if ($question->answer_type != 'text') {
                    foreach ($question->questionnaries_option as $options) {
                        $option_arr['id'] = $options->id;
                        $option_arr['option'] = $options->option;
                        $questionaries[$k]['questionnaries_option'][] = $option_arr;
                    }
                }
            }

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'patient_list_arr' => $patient_list_arr, 'patient_detail_result' => $patient_detail_result, 'questionaries' => $questionaries));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    public function getPatientDetails($def_pat_id, $providerid, $schedulardate, $practiceid) {

        try {

            $patient_det = Patient::where('id', $def_pat_id)->selectRaw('id,first_name,last_name,middle_name,address1,address2,city,state,zip5,zip4,is_self_pay,gender,ssn,DATE_FORMAT(dob, "%m/%d/%Y") as dob,age,phone,email,mobile,race,guarantor_first_name,guarantor_last_name,guarantor_middle_name,guarantor_relationship,employment_status,employer_name,marital_status,organization_name,occupation,student_status,statements_sent,bill_cycle,account_no,work_phone,work_phone_ext,avatar_name,avatar_ext')->first();
            $patient_guarantor_det = PatientContact::where('patient_id', $def_pat_id)->where('category', 'Guarantor')->first();
            $patient_emergency_contact_det = PatientContact::where('patient_id', $def_pat_id)->where('category', 'Emergency Contact')->first();
            $patient_insurances = PatientInsurance::with(array('insurance_details' => function($query) {
                            $query->select('id', 'insurance_name');
                        }))->where('patient_id', $def_pat_id)->get()->toArray();
            $past_history = PatientAppointment::where('provider_id', $providerid)->where('patient_id', $def_pat_id)->where('scheduled_on', '<', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->orderBy('id', 'desc')->get()->toArray();

            /// Starts - Get Patient Notes ///
            $patient_alerts = [];
            $patient_notes = [];
            $patient_notes_arr = PatientNote::with('user')->where('notes_type', 'patient')->where('status','Active')->where('notes_type_id', $def_pat_id)->where('patient_notes_type', 'patient_notes')->get();
            $patient_alerts['title'] = @$patient_notes_arr->title;
            $patient_alerts['content'] = @$patient_notes_arr->content;
            /// Ends - Get Patient Notes ///
            /// Image ///
            $filename = @$patient_det->avatar_name . '.' . @$patient_det->avatar_ext;
            $img_details = [];
            $img_details['module_name'] = 'patient';
            $img_details['file_name'] = $filename;
            $img_details['practice_name'] = md5('P' . $practiceid);

            $img_details['need_url'] = 'yes';
            $img_details['alt'] = 'patient-image';

            $patient_det['image_tag'] = ($filename!='.')? Helpers::checkAndGetAvatar($img_details): url('/').'/'.Helpers::checkAndGetAvatar($img_details);
            /// Image ///
            /// Starts - Get patient general information ///	
            $cur_date = date("Y-m-d");
            $patient_det['last_appoinment'] = PatientAppointment::where('patient_id', $def_pat_id)
                                                ->where('deleted_at', NULL)
                                                ->where('scheduled_on', '<=', $cur_date)
                                                ->orderBy('scheduled_on', 'desc')
                                                ->take(1)->pluck('scheduled_on')->first();
            $patient_det['last_appoinment'] = ($patient_det['last_appoinment']) ? Helpers::dateFormat($patient_det['last_appoinment'], 'date') : '-';
            $patient_det['next_appoinment'] = PatientAppointment::where('patient_id', $def_pat_id)->where('scheduled_on', '>', $cur_date)
                                                ->orderBy('scheduled_on', 'asc')
                                                ->take(1)->pluck('scheduled_on')->first();
            $patient_det['next_appoinment'] = ($patient_det['next_appoinment']) ? Helpers::dateFormat($patient_det['next_appoinment'], 'date') : '-';
            $patient_det['last_statement'] = "- Nil -";
            $patient_det['statement'] = "- Nil -";
            $patient_det['insurance_balance'] = $this->getPatientInsuranceDue($def_pat_id);//Claims::where('patient_id', $def_pat_id)->sum('insurance_due');
            $patient_det['patient_balance'] = $this->getPatientDue($def_pat_id);//Claims::where('patient_id', $def_pat_id)->sum('patient_due');
            $patient_det['budget_plan'] = (PatientBudget::where('patient_id', $def_pat_id)->count()) ? 'Yes' : 'No';
            $last_payment_date_cnt = PMTInfoV1::where('patient_id', $def_pat_id)->where('pmt_method', 'Patient')->count();
            if ($last_payment_date_cnt > 0) {
                $last_payment_date_val = PMTInfoV1::where('patient_id', $def_pat_id)->where('pmt_method', 'Patient')->orderBy('id', 'DESC')->take(1)->pluck('created_at')->first();
                $patient_det['last_payment_date'] = date('m/d/Y', strtotime($last_payment_date_val));
            } else {
                $patient_det['last_payment_date'] = "- Nil -";
            }

            /// Ends - Get patient general information ///
            if ($patient_guarantor_det) {
                $patient_guarantor['guarantor_last_name'] = $patient_guarantor_det->guarantor_last_name;
                $patient_guarantor['guarantor_middle_name'] = $patient_guarantor_det->guarantor_middle_name;
                $patient_guarantor['guarantor_first_name'] = $patient_guarantor_det->guarantor_first_name;
                $patient_guarantor['guarantor_relationship'] = $patient_guarantor_det->guarantor_relationship;
                $patient_guarantor['guarantor_home_phone'] = $patient_guarantor_det->guarantor_home_phone;
                $patient_guarantor['guarantor_cell_phone'] = $patient_guarantor_det->guarantor_cell_phone;
                $patient_guarantor['guarantor_email'] = $patient_guarantor_det->guarantor_email;
                $patient_guarantor['guarantor_address1'] = $patient_guarantor_det->guarantor_address1;
                $patient_guarantor['guarantor_address2'] = $patient_guarantor_det->guarantor_address2;
                $patient_guarantor['guarantor_city'] = $patient_guarantor_det->guarantor_city;
                $patient_guarantor['guarantor_state'] = $patient_guarantor_det->guarantor_state;
                $patient_guarantor['guarantor_zip5'] = $patient_guarantor_det->guarantor_zip5;
                $patient_guarantor['guarantor_zip4'] = $patient_guarantor_det->guarantor_zip4;
            } else {
                $patient_guarantor = ['' => ''];
            }

            if ($patient_emergency_contact_det) {
                $patient_emergency_contact['emergency_last_name'] = $patient_emergency_contact_det->emergency_last_name;
                $patient_emergency_contact['emergency_middle_name'] = $patient_emergency_contact_det->emergency_middle_name;
                $patient_emergency_contact['emergency_first_name'] = $patient_emergency_contact_det->emergency_first_name;
                $patient_emergency_contact['emergency_relationship'] = $patient_emergency_contact_det->emergency_relationship;
                $patient_emergency_contact['emergency_home_phone'] = $patient_emergency_contact_det->emergency_home_phone;
                $patient_emergency_contact['emergency_cell_phone'] = $patient_emergency_contact_det->emergency_cell_phone;
                $patient_emergency_contact['emergency_email'] = $patient_emergency_contact_det->emergency_email;
                $patient_emergency_contact['emergency_address1'] = $patient_emergency_contact_det->emergency_address1;
                $patient_emergency_contact['emergency_address2'] = $patient_emergency_contact_det->emergency_address2;
                $patient_emergency_contact['emergency_city'] = $patient_emergency_contact_det->emergency_city;
                $patient_emergency_contact['emergency_state'] = $patient_emergency_contact_det->emergency_state;
                $patient_emergency_contact['emergency_zip5'] = $patient_emergency_contact_det->emergency_zip5;
                $patient_emergency_contact['emergency_zip4'] = $patient_emergency_contact_det->emergency_zip4;
            } else {
                $patient_emergency_contact = ['' => ''];
            }
            $patient_det['patient_guarantor'] = $patient_guarantor;
            $patient_det['patient_emergency_contact'] = $patient_emergency_contact;
            $patient_det['patient_insurances'] = $patient_insurances;
            $patient_det['past_history'] = $past_history;
            $patient_det['patient_alerts'] = $patient_alerts;
            $patient_detail_result = $patient_det;
            return $patient_detail_result;
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getPatientDetailsByproviderDate process ** */

    /*     * * Start getPatientDetailsByproviderAppointment process ** */

    public function getPatientDetailsByproviderAppointment() {
        $request = Request::all();

        try {
            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $providerid = $request['providerid'];
            $schedulardate = $request['schedulardate'];
            $patientid = $request['patientid'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $patient_detail_result = $this->getPatientDetails($patientid, $providerid, $schedulardate, $practiceid);

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'patient_detail_result' => $patient_detail_result));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getPatientDetailsByproviderAppointment process ** */

    /*     * * Start getPatientDetailsEditbaseproviderAppointment process ** */

    public function getPatientDetailsEditbaseproviderAppointment() {
        $request = Request::all();
        try {
            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $providerid = $request['providerid'];
            $schedulardate = $request['schedulardate'];
            $patientid = $request['patientid'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $patient_det = Patient::where('id', $patientid)->get()->toArray();
            $patient_insurances = PatientInsurance::where('patient_id', $patientid)->get()->toArray();
            $questionaries_res = QuestionnariesTemplate::with('questionnaries_option')->whereHas('questionnaries', function($q) use($facilityid, $providerid) {
                        $q->where('facility_id', $facilityid)->where('provider_id', $providerid);
                    })->orderBy('question_order', 'ASC')->get();
            $questionaries = [];
            foreach ($questionaries_res as $k => $question) {
                $questionaries[$k]['question_id'] = $question->id;
                $questionaries[$k]['title'] = $question->title;
                $questionaries[$k]['question'] = $question->question;
                $questionaries[$k]['answer_type'] = $question->answer_type;
                $questionaries[$k]['questionnaries_option'] = [];
                if ($question->answer_type != 'text') {
                    foreach ($question->questionnaries_option as $options) {
                        $option_arr['id'] = $options->id;
                        $option_arr['option'] = $options->option;
                        $questionaries[$k]['questionnaries_option'][] = $option_arr;
                    }
                }
            }

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'patient_details' => $patient_det, 'patient_insurances' => $patient_insurances, 'questionaries' => $questionaries));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getPatientDetailsEditbaseproviderAppointment process ** */

    /*     * * Start PatientDetailsEditUniquevalidation process ** */

    public function PatientDetailsEditUniquevalidation() {
        $request = Request::all();
        try {
            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $patientid = $request['patientid'];
            $dob = $request['dob'];
            $ssn = $request['ssn'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $count = Patient::where('ssn', $ssn)->where('dob', $dob)->where('id', '!=', $patientid)->count();
            if ($count > 0)
                return Response::json(array('loginStatus' => '1', 'StatusMessage' => 'SSN and Date Of Birth unique'));
            else
                return Response::json(array('loginStatus' => '0', 'StatusMessage' => 'success'));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End PatientDetailsEditUniquevalidation process ** */

    public function getPrepopulatedList() {
        try {
            $states = State::orderBy('code', 'ASC')->pluck('code')->all();

            $request = Request::all();
            $practiceid = $request['practiceid'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);
            $insurances = Insurance::select('id', 'insurance_name', 'short_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'phone1')->orderBy('insurance_name', 'ASC')->get();
            $guarantor_relationship = array('-- Select --',  "Self", "Brother", "Child", "Father", "Friend", "Grand Child", "Grand Father", "Grand Mother", "Guardian", "Mother", "Neighbor", "Sister", "Spouse", "Others");
            $employment_status = array('-- Select --', 'Employed', 'Self Employed',  'Retired', 'Active Military Duty', 'Unknown');
            $student_status = array('Full Time', 'Part Time', 'Unknown');
            $insurance_category = array('-- Select --', 'Primary', 'Secondary', 'Tertiary', 'Workers Comp', 'Auto Accident', 'Attorney', 'Other');
            $insured_relationship = array('Self', 'Spouse', 'Child', 'Others');
            $scheduler_status_color_code = array('Scheduled' => '#2c9707', 'Confirmed' => '#f2b100', 'Not Confirmed' => '#f2b100', 'Arrived ' => '#f2b100', 'In Session ' => '#2c9707', 'Complete ' => '#2c9707', 'Cancelled ' => '#b64050');
            $pre_populated_list = array('insurances' => $insurances, 'guarantor_relationship' => $guarantor_relationship, 'employment_status' => $employment_status, 'student_status' => $student_status, 'insurance_category' => $insurance_category, 'insured_relationship' => $insured_relationship, 'states' => $states, 'scheduler_status_color_code' => $scheduler_status_color_code);
            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'pre_populated_list' => $pre_populated_list));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    public function logoutprocess() {
        try {
            Auth::logout();
            return Response::json(array('status' => '0', 'StatusMessage' => 'Logout successfully'));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    public function tmppatient_img() {
        try {
            $request = Request::all();

            //TESTING
            /* $storage_path 	= storage_path()."\manual_logs";
              $path = $storage_path.'\app_img_19-10-2016.txt';
              $base64_string = 'data:image/jpg;base64,'.file_get_contents($path); */

            $base64_string = 'data:image/jpg;base64,' . $request['image'];

            list($typecont, $data) = explode(';', $base64_string);
            $typecont_arr = explode('/', $typecont);
            $data = explode(',', $base64_string);
            Storage::disk('local_manual_log')->put('manual_logs/' . uniqid() . '.' . $typecont_arr[1], base64_decode($data[1]));

            //TEST Mail
            $logpath = 'storage/manual_logs/app_img_' . date('d-m-Y') . '.txt';
            $string = " ============== App Encode Image Mail ========== \n";
            $string .= " Time : " . date('H:i:s') . "\n";
            $string .= " Log Path : " . $logpath . "\n";
            $to = "anitha@mail.annexmed.in";
            $subject = "App Encode Image Mail - " . date('d-m-Y H:i:s');
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Admin' . "\r\n";
            $headers .= 'Cc: developers@clouddesigners.com' . "\r\n";
            mail($to, $subject, $string, $headers);

            return Response::json(array('status' => '0', 'StatusMessage' => 'success'));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * Start getProviderListIntakeBydate process ** */

    public function getProviderListIntakeBydate() {
        $request = Request::all();
        try {

            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $schedulardate = @$request['schedulardate'];
            $search_keyword = @$request['search_keyword'];

            if ($schedulardate == "")
                $schedulardate = date('Y-m-d');

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0) {
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            }
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $provider_list = ProviderSchedulerTime::where('facility_id', $facilityid)->where('schedule_date', $schedulardate)->pluck('provider_id')->all();
            $def_pro_id = '';
            $providers_list = [];
            if (count($provider_list > 0)) {
                $provider_list = array_unique($provider_list);
                $provider_list_obj = Provider::with('degrees', 'speciality', 'provider_types')->whereIn('id', $provider_list);

                if ($search_keyword != '') {
                    $provider_list_obj->where('provider_name', 'LIKE', '%' . $search_keyword . '%');
                }

                $provider_list_res = $provider_list_obj->orderBy('provider_name', 'ASC')->get();

                if (count($provider_list_res) > 0) {
                    foreach ($provider_list_res as $key => $value) {
                        if ($def_pro_id == '')
                            $def_pro_id = $value->id;
                        $providers_list[$key]['provider_id'] = $value->id;
                        $providers_list[$key]['provider_name'] = $value->provider_name . ' ' . @$value->degrees->degree_name;
                        $providers_list[$key]['npi'] = $value->npi;
                        $providers_list[$key]['provider_type'] = $value->provider_types->name . ' Provider';

                        $provider_sch_time = ProviderSchedulerTime::where('facility_id', $facilityid)->where('provider_id', $value->id)->where('schedule_date', $schedulardate)->select(DB::raw("CONCAT(from_time,' to ',to_time) AS sch_time"))->pluck('sch_time')->all();
                        $providers_list[$key]['scheduler_time'] = $provider_sch_time;
                    }
                }
            }

            $patients_list = [];

            // Get patient details by first default provider.
            if ($def_pro_id != '') {
                $patient_lists = PatientAppointment::with(array('patient' => function($query) {
                                $query->select('id', 'first_name', 'last_name', 'middle_name', 'ssn', 'dob', 'avatar_name', 'avatar_ext');
                            }))->where('facility_id', $facilityid)->where('provider_id', $def_pro_id)->where('scheduled_on', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->orderBy('appointment_time', 'ASC')->get();

                foreach ($patient_lists as $appointment) {
                    $patient_appointment['patient_id'] = $appointment->patient->id;
                    $patient_appointment['first_name'] = $appointment->patient->first_name;
                    $patient_appointment['last_name'] = $appointment->patient->last_name;
                    $patient_appointment['middle_name'] = $appointment->patient->middle_name;
                    $patient_appointment['ssn'] = $appointment->patient->ssn;
                    $patient_appointment['dob'] = Helpers::dateFormat($appointment->patient->dob, 'dob');
                    $patient_appointment['appointment_time'] = $appointment->appointment_time;
                    $patient_appointment['provider_id'] = $def_pro_id;
                    $patient_appointment['schedulardate'] = $schedulardate;

                    /// Image ///
                    $filename = @$appointment->patient->avatar_name . '.' . @$appointment->patient->avatar_ext;
                    $img_details = [];
                    $img_details['module_name'] = 'patient';
                    $img_details['file_name'] = $filename;
                    $img_details['practice_name'] = md5('P' . $practiceid);
                    $img_details['need_url'] = 'yes';
                    $img_details['alt'] = 'patient-image';

                    $patient_appointment['image_tag'] = ($filename!='.')? Helpers::checkAndGetAvatar($img_details): url("/").'/'.Helpers::checkAndGetAvatar($img_details);
                    /// Image ///

                    $patients_list[] = $patient_appointment;
                }
            }

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'providers_list' => $providers_list, 'patients_list' => $patients_list));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getProviderListIntakeBydate process ** */

    /*     * * Start getPatDetailsIntakeByProviderDate process ** */

    public function getPatDetailsIntakeByProviderDate() {
        $request = Request::all();

        try {

            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $providerids = explode(',', $request['providerid']);
            $schedulardate = $request['schedulardate'];
            $search_keyword = @$request['search_keyword'];

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0)
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $patients_list = [];
            if (count($providerids) > 0) {

                if ($search_keyword != '') {
                    $search_arr = explode(' ', $search_keyword);

                    $sub_qq = array('patient' => function($query) use ($search_arr) {
                        foreach ($search_arr as $search) {
                            $query->where('last_name', 'LIKE', '%' . rtrim($search, ',') . '%')->orWhere('first_name', 'LIKE', '%' . $search . '%')->orWhere('middle_name', 'LIKE', '%' . $search . '%');
                        }
                        $query->select('id', 'first_name', 'last_name', 'middle_name', 'ssn', 'dob', 'avatar_name', 'avatar_ext');
                    });
                } else {
                    $sub_qq = array('patient' => function($query) {
                            $query->select('id', 'first_name', 'last_name', 'middle_name', 'ssn', 'dob', 'avatar_name', 'avatar_ext');
                        });
                }

                foreach ($providerids as $providerid_value) {
                    $patient_lists = PatientAppointment::with($sub_qq)->where('facility_id', $facilityid)->where('provider_id', $providerid_value)->where('scheduled_on', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->orderBy('appointment_time', 'ASC')->get();

                    foreach ($patient_lists as $appointment) {
                        if (isset($appointment->patient->id)) {

                            $patient_appointment['patient_id'] = $appointment->patient->id;
                            $patient_appointment['first_name'] = $appointment->patient->first_name;
                            $patient_appointment['last_name'] = $appointment->patient->last_name;
                            $patient_appointment['middle_name'] = $appointment->patient->middle_name;
                            $patient_appointment['ssn'] = $appointment->patient->ssn;
                            $patient_appointment['dob'] = Helpers::dateFormat($appointment->patient->dob, 'dob');
                            $patient_appointment['appointment_time'] = $appointment->appointment_time;
                            $patient_appointment['provider_id'] = $providerid_value;
                            $patient_appointment['schedulardate'] = $schedulardate;

                            /// Image ///
                            $filename = @$appointment->patient->avatar_name . '.' . @$appointment->patient->avatar_ext;
                            $img_details = [];
                            $img_details['module_name'] = 'patient';
                            $img_details['file_name'] = $filename;
                            $img_details['practice_name'] = md5('P' . $practiceid);
                            $img_details['need_url'] = 'yes';
                            $img_details['alt'] = 'patient-image';

                            $patient_appointment['image_tag'] = ($filename!='.')? Helpers::checkAndGetAvatar($img_details): url("/").'/'.Helpers::checkAndGetAvatar($img_details);
                            /// Image ///

                            $patients_list[] = $patient_appointment;
                        }
                    }
                }
            }
            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'patients_list' => $patients_list));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getPatDetailsIntakeByProviderDate process ** */

    // NEWLY ADDED (17/03/2017)

    /*     * * Start getProviderAndPatientList process ** */
    public function getProviderAndPatientList() {
        $request = Request::all();
		try {
            $practiceid = $request['practiceid'];
            $facilityid = $request['facilityid'];
            $schedulardate = @$request['schedulardate'];
            $providerid = @$request['providerid'];
            if ($schedulardate == "")
                $schedulardate = date('Y-m-d');
            $providerids = $providerid != "" ? explode(',', $request['providerid']) : array();

            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0) {
                $practice_info = Practices::where('status', 'Active')->where('id', $practiceid)->first();
            }
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            //Get Provider List
            $provider_list = ProviderSchedulerTime::where('facility_id', $facilityid)->pluck('provider_id')->all();
            $def_pro_id = '';
            $providers_list = [];
            $selected_provider = [];
            if (count($provider_list > 0)) {
                $provider_list = array_unique($provider_list);
                $provider_list_res = Provider::with('degrees', 'speciality', 'provider_types')->whereIn('id', $provider_list)->orderBy('provider_name', 'ASC')->get();
                if (count($provider_list_res) > 0) {
                    foreach ($provider_list_res as $key => $value) {
                        if ($def_pro_id == '')
                            $def_pro_id = $value->id;
                        $providers_list[$key]['provider_id'] = $value->id;
                        $providers_list[$key]['provider_name'] = $value->provider_name . ' ' . @$value->degrees->degree_name;
                        $providers_list[$key]['npi'] = $value->npi;
                        $providers_list[$key]['provider_type'] = $value->provider_types->name . ' Provider';

                        $provider_sch_time = ProviderSchedulerTime::where('facility_id', $facilityid)->where('provider_id', $value->id)->where('schedule_date', $schedulardate)->select(DB::raw("CONCAT(from_time,' to ',to_time) AS sch_time"))->pluck('sch_time')->all();
                        $providers_list[$key]['scheduler_time'] = $provider_sch_time;

                        $curr_pr_id = $value->id;
                        $questionaries_res = QuestionnariesTemplate::with('questionnaries_option')->whereHas('questionnaries_app', function($q) use($facilityid, $curr_pr_id) {
                                    $q->where('facility_id', $facilityid)->where('provider_id', $curr_pr_id);
                                })->orderBy('question_order', 'ASC')->get();
                        $questionaries = [];
                        foreach ($questionaries_res as $k => $question) {
                            $questionaries[$k]['question_id'] = $question->id;
                            $questionaries[$k]['title'] = $question->title;
                            $questionaries[$k]['question'] = $question->question;
                            $questionaries[$k]['template_id'] = $question->template_id;
                            $questionaries[$k]['answer_type'] = $question->answer_type;
                            $questionaries[$k]['questionnaries_option'] = [];
                            if ($question->answer_type != 'text') {
                                foreach ($question->questionnaries_option as $options) {
                                    $option_arr['id'] = $options->id;
                                    $option_arr['option'] = $options->option;
                                    $questionaries[$k]['questionnaries_option'][] = $option_arr;
                                }
                            }
                        }
                        $providers_list[$key]['questionaries'] = $questionaries;
                    }
                }
            }
            //End Provider List

            if (count($providerids))
                $selected_provider = $providerids;
            elseif ($def_pro_id != '')
                $selected_provider[] = $def_pro_id;

            // Get patient list by provider
            $patients_list = [];
			 $selected_provider = array_unique($provider_list);
            if (count($selected_provider)) {
                foreach ($selected_provider as $pro_id_val) {
                    $patient_lists = PatientAppointment::with(array('patient' => function($query) {
                                    $query->select('id', 'first_name', 'last_name', 'middle_name', 'ssn', 'dob', 'gender', 'avatar_name', 'avatar_ext');
                                }))->where('facility_id', $facilityid)->where('provider_id', $pro_id_val)->where('scheduled_on', $schedulardate)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete', 'Cancelled','Encounter'])->orderBy('appointment_time', 'ASC')->get();

                    foreach ($patient_lists as $appointment) {
                        $patient_appointment['patient_id'] = $appointment->patient->id;
                        $patient_appointment['first_name'] = $appointment->patient->first_name;
                        $patient_appointment['last_name'] = $appointment->patient->last_name;
                        $patient_appointment['middle_name'] = $appointment->patient->middle_name;
                        $patient_appointment['dob'] = Helpers::dateFormat($appointment->patient->dob, 'dob');
                        $patient_appointment['gender'] = $appointment->patient->gender;
                        $patient_appointment['ssn'] = $appointment->patient->ssn;
                        $patient_appointment['appointment_time'] = $appointment->appointment_time;
                        $patient_appointment['provider_id'] = $pro_id_val;
                        $patient_appointment['schedulardate'] = $schedulardate;

                        /// Image ///
                        $filename = @$appointment->patient->avatar_name . '.' . @$appointment->patient->avatar_ext;
                        $img_details = [];
                        $img_details['module_name'] = 'patient';
                        $img_details['file_name'] = $filename;
                        $img_details['practice_name'] = md5('P' . $practiceid);
                        $img_details['need_url'] = 'yes';
                        $img_details['alt'] = 'patient-image';
                        $image_tag = ($filename!='.')? Helpers::checkAndGetAvatar($img_details): url('/').'/'.Helpers::checkAndGetAvatar($img_details);
                        /*if ($image_tag == "img/patient_noimage.png")
                            $image_tag = "https://clouddesigners.in/medcubic/img/patient_noimage.png";*/
                        $patient_appointment['image_tag'] = $image_tag;
                        /// Image ///

                        $patients_list[] = $patient_appointment;
                    }
                }
            }

            $templates_list = Template::select('content', 'id','name')->whereHas('templatetype', function($q) {
                        $q->where('templatetypes', 'App');
                    })->where('status', 'Active')->get()->toArray();

            $templates_list_new = array();
            /*
                Template Keys
            ##VAR-INSUREDNAME##
            ##VAR-SSN##
            ##VAR-PATIENTNAME##
            ##VAR-PATIENTSIGN##
            ##VAR-DATE##
            ##VAR-PATIENTNAME##
            ##VAR-RENDERINGDOCTORNAME##
            if we change any key from web need to change the app side also
            */
            foreach ($templates_list as $key => $value) {
                $key_value = strtolower(str_replace(' ', '_', $value['name']));
                $value['content'] = str_replace("##VAR-PRACTICENAME##", $practice_info['practice_name'], $value['content']);
                $value['content'] = str_replace("##VAR-PRACTICEPHNO##", $practice_info['phone'], $value['content']);
                $value['content'] = str_replace("\r\n", '', $value['content']);
                $templates_list_new[$key_value] = "<!DOCTYPE html><html><body>" . $value['content'] . "</body></html>";
            }
            ksort($templates_list_new);
            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'providers_list' => $providers_list, 'patients_list' => $patients_list, 'templates_list_new' => $templates_list_new));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

    /*     * * End getProviderAndPatientList process ** */

    public function getPatientInfDetails() {
		
        $request = Request::all();
		
        try {
            $practiceid = @$request['practiceid'];
            $facilityid = @$request['facilityid'];
            $providerid = @$request['providerid'];
            $patientid = @$request['patientid'];
            $cur_date = date("Y-m-d");

            $practice_info = Practices::where('id', $practiceid)->first();
			
            if (count($practice_info) == 0) {
                $practice_info = Practices::where('status', 'Active')->where('id', $practiceid)->first();
            }
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);

            $patient_det = Patient::where('id', $patientid)->selectRaw('id,first_name,last_name,middle_name,address1,address2,city,UCASE(state) as state,zip5,zip4,is_self_pay,gender,ssn,DATE_FORMAT(dob, "%m/%d/%Y") as dob,phone,email,mobile,race,marital_status,organization_name,occupation,student_status,statements_sent,bill_cycle,account_no,work_phone,work_phone_ext,avatar_name,avatar_ext,created_at,last_statement_sent_date,updated_at')->first();
			/*Patient Contact table value assign in the patient listing page  */
			$patient_emp_con =(!empty(PatientContact::where('patient_id',$patientid)->orderBy('updated_at','DESC')->where('category','Employer')->select('id','employer_name','employer_status')->first()))?PatientContact::where('patient_id',$patientid)->orderBy('updated_at','DESC')->where('category','Employer')->select('id','employer_name','employer_status')->first():'';
			/* Assign the value for patient contact table */
			$patient_det['organization_name'] = isset($patient_emp_con['employer_name'])?$patient_emp_con['employer_name']:'';
			$patient_det['occupation'] = isset($patient_emp_con['employer_status'])?$patient_emp_con['employer_status']:'';
			
			$patient_details = array();
            $patient_details['first_name'] = $patient_det['first_name'];
            $patient_details['last_name'] = $patient_det['last_name'];
            $patient_details['middle_name'] = $patient_det['middle_name'];
            $patient_details['dob'] = $patient_det['dob'];
            $patient_details['age'] = Helpers::dob_age($patient_det['dob']);
            $patient_details['age_in_years'] = explode(' ',$patient_details['age'])[0];
            $patient_details['gender'] = $patient_det['gender'];
            $patient_details['account_no'] = $patient_det['account_no'];

            $patient_details['last_appoinment'] = PatientAppointment::where('patient_id', $patientid)->where('scheduled_on', '<=', $cur_date)->take(1)->pluck('scheduled_on')->first();
            $patient_details['last_appoinment'] = ($patient_details['last_appoinment']) ? Helpers::dateFormat($patient_details['last_appoinment'], 'date') : '- Nil -';
            $patient_details['next_appoinment'] = PatientAppointment::where('patient_id', $patientid)->where('scheduled_on', '>', $cur_date)->take(1)->pluck('scheduled_on')->first();
            $patient_details['next_appoinment'] = ($patient_details['next_appoinment']) ? Helpers::dateFormat($patient_details['next_appoinment'], 'date') : '- Nil -';
            $patient_details['last_payment'] = !empty(PMTInfoV1::selectRaw('(CASE WHEN pmt_method="Insurance" THEN amt_used ELSE pmt_amt END) AS pmt_amt')->where('patient_id',$patientid)->whereNull('void_check')->orderBy('created_at','desc')->first())?(PMTInfoV1::selectRaw('(CASE WHEN pmt_method="Insurance" THEN amt_used ELSE pmt_amt END) AS pmt_amt')->where('patient_id',$patientid)->whereNull('void_check')->orderBy('created_at','desc')->first()->toArray()['pmt_amt']):'0.00';
            $patient_details['insurance_balance'] = $this->getPatientInsuranceDue($patientid);
            $patient_details['patient_balance'] = $this->getPatientDue($patientid);
            $patient_details['ar_due'] = number_format(str_replace(",", "", $this->getPatientInsuranceDue($patientid))+str_replace(",", "", $this->getPatientDue($patientid)),2);

            $patient_address_details = array();
            $patient_address_details['address1'] = $patient_det['address1'];
            $patient_address_details['address2'] = $patient_det['address2'];
            $patient_address_details['city'] = $patient_det['city'];
            $patient_address_details['state'] = $patient_det['state'];
            $patient_address_details['zip5'] = $patient_det['zip5'];
            $patient_address_details['zip4'] = $patient_det['zip4'];
            $patient_address_details['ssn'] = $patient_det['ssn'];
            $patient_address_details['cell_phone'] = $patient_det['phone'];

            $patient_red_alerts_details = array();
            $patient_red_alerts_details['cancelled_appts'] = PatientAppointment::where('facility_id', $facilityid)->where('provider_id', $providerid)->where('patient_id', $patientid)->where('status', 'Cancelled')->get()->count();
            $patient_red_alerts_details['return_check'] = 0;
            $patient_red_alerts_details['statement'] = (!empty(Patient::where('id', $patientid)->where('statements', '<>', 'Unknown')->pluck('statements')->first()))?Patient::where('id', $patientid)->where('statements', '<>', 'Unknown')->pluck('statements')->first() :'No';
            $patient_red_alerts_details['eligibility'] = (PatientEligibility::where('patients_id', $patientid)->count())?'Yes':'No';
            $patient_red_alerts_details['statement_sent'] = $patient_det['statements_sent'];
            $patient_red_alerts_details['budget_plan'] = (PatientBudget::where('patient_id', $patientid)->count()) ? 'Yes' : 'No';
			$patient_insurance_details = PatientInsurance::with(array('insurance_details' => function($query) {
                            $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4');
                        }))->where('patient_id', $patientid)->select('id', 'insurance_id', 'relationship', 'insured_phone','insured_ssn', 'policy_id', 'first_name', 'last_name', 'middle_name', 'insured_gender', 'insured_dob','insured_address1','insured_address2','insured_city','same_patient_address',DB::raw('(CASE WHEN category != "Others" THEN category ELSE "Other" END) as category'),DB::raw('UCASE(insured_state) as insured_state'), 'insured_zip5', 'insured_zip4',DB::raw('(CASE WHEN insured_dob != 0000-00-00 THEN DATE_FORMAT(insured_dob, "%m/%d/%Y") ELSE "- Nil -" END) AS insured_dob'),DB::raw('(CASE WHEN effective_date != 0000-00-00 THEN DATE_FORMAT(effective_date, "%m/%d/%y") ELSE "- Nil -" END) AS effective_date'), DB::raw('(CASE WHEN termination_date != 0000-00-00 THEN DATE_FORMAT(termination_date, "%m/%d/%y") ELSE "- Nil -" END) AS termination_date'), DB::raw('(CASE WHEN DATE(termination_date) >= CURDATE() THEN "Active"  WHEN termination_date = 0000-00-00 THEN "- Nil -" ELSE "Inactive" END) AS status'))->get()->toArray();
			$patient_general_details = array();
            $patient_general_details['created_at'] = Helpers::dateFormat($patient_det['created_at'],'date');
            $patient_general_details['bill_cycle'] = $patient_det['bill_cycle'];
            $patient_general_details['last_statement'] = $patient_det['last_statement_sent_date'] != "0000-00-00" ? Helpers::dateFormat($patient_det['last_statement_sent_date'], 'date') : "- Nil -";

            $patient_general_details['budget_amount'] = PatientBudget::where('patient_id', $patientid)->sum('budget_amt');
            $patient_general_details['billed_amount'] = $this->getPatientBilledAmount($patientid);
            $patient_general_details['unbilled_amount'] = $this->getPatientUnBilledAmount($patientid); 
            $patient_general_details['insurance_payment'] = $this->getPatientInsurancePayment($patientid);
            $patient_general_details['patient_payment'] = number_format(PMTInfoV1::where('patient_id',$patientid)->whereIn('pmt_type', ['Payment','Credit Balance'])->where('pmt_method', 'Patient')->whereNull('void_check')->sum('pmt_amt'),2);
            //$this->getPatientPayment($patientid);

            $patient_notes_details = PatientNote::where('notes_type', 'patient')->where('patient_notes_type', 'patient_notes')->where('notes_type_id', $patientid)->where('status','Active')->where('title', '!=', 'ArManagement')->selectRaw('CONCAT(UCASE(LEFT(REPLACE(patient_notes_type,"_"," "), 1)), 
                             SUBSTRING(REPLACE(patient_notes_type,"_"," "), 2)) as title, content, DATE_FORMAT(created_at,"%m-%d-%y %H:%i:%s") as date')->get()->toArray();

            $patient_guarantor_contact_details = PatientContact::where('patient_id', $patientid)->where('category', 'Guarantor')->selectRaw('id,patient_id,category,guarantor_last_name as last_name,guarantor_middle_name as middle_name,guarantor_first_name as first_name,guarantor_relationship as relationship,guarantor_home_phone as home_phone,guarantor_cell_phone as cell_phone,guarantor_email as email,guarantor_address1 as address1,guarantor_address2 as address2,guarantor_city as city,UCASE(guarantor_state) as state,guarantor_zip5 as zip5,guarantor_zip4 as zip4,DATE_FORMAT(created_at, "%m/%d/%y") as created_date')->get()->toArray();
            $patient_emergency_contact_details = PatientContact::where('patient_id', $patientid)->where('category', 'Emergency Contact')->selectRaw('id,patient_id,category,emergency_last_name as last_name,emergency_middle_name as middle_name,emergency_first_name as first_name,emergency_relationship as relationship,emergency_home_phone as home_phone,emergency_cell_phone as cell_phone,emergency_email as email,emergency_address1 as address1,emergency_address2 as address2,emergency_city as city,UCASE(emergency_state) as state,emergency_zip5 as zip5,emergency_zip4 as zip4,DATE_FORMAT(created_at, "%m/%d/%y") as created_date')->get()->toArray();

            return Response::json(array('status' => '0', 'StatusMessage' => 'success', 'patient_details' => $patient_details, 'patient_address_details' => $patient_address_details, 'patient_red_alerts_details' => $patient_red_alerts_details, 'patient_insurance_details' => $patient_insurance_details, 'patient_general_details' => $patient_general_details, 'patient_notes_details' => $patient_notes_details, 'patient_guarantor_contact_details' => $patient_guarantor_contact_details, 'patient_emergency_contact_details' => $patient_emergency_contact_details, 'patient_inforamtion_det' => $patient_det,'patient_emp_contact'=>$patient_emp_con));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => json_encode($e->getMessage())));
        }
    }

    /* Store the Patient information */

    public function storePatient() {
        /* if request is come try block is work otherwise Catch block is work */
        try {
            $request = Request::all();
            $practiceid = @$request['practiceid'];
            $facilityid = @$request['facilityid'];
            $providerid = @$request['providerid'];
            $patientid = @$request['patientid'];
            $cur_date = date("Y-m-d");
            $practice_info = Practices::where('id', $practiceid)->first();
            if (count($practice_info) == 0) {
                $practice_info = Practices::where('status', 'Active')->where('id', '4')->first();
            }
            $db = new DBConnectionController();
            $db->connectPracticeDB($practice_info['id']);
            /* calling function for storeAppInfo() */
            $this->storeAppInfo();
            return Response::json(array('status' => '0', 'StatusMessage' => 'success'));
            //return Response::json(array('status' => '1', 'StatusMessage' => json_encode($request['patient_insurance_details']['insurance_details'][0])));
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => json_encode($e->getMessage())));
        }
    }

    /* Patient storePatient function called here */

    public function storeAppInfo($patient_id = '') {
        $request = Request::all();
        $user_id = $patient_id = $request['user_id'];
		
        /* New patient means request patient id =0 */
        if ($patient_id == '0')
            $patient_id = '';
        /* Patient Validation check DOB and SSn is same */
        if ($request['dob'] != "1901-01-01" && $request['dob'] != "" && $request['ssn'] != "") {
            Validator::extend('chk_ssn_dob_unique', function($attribute, $value, $parameters) {
                $ssn_un = Input::get($parameters[0]);
                $dob_un = date('Y-m-d', strtotime(Input::get($parameters[1])));
                $patient_id_un = $parameters[2];
                if ($patient_id_un != '')
                    $count = Patient::where('ssn', $ssn_un)->where('id', '!=', $patient_id_un)->count();
                else
                    $count = Patient::where('ssn', $ssn_un)->count();
                /* if already same DOB and same ssn count is 1 return false */
                if ($count > 0)
                    return false;
                else
                    return true;
            });
            $rules = Patient::$rules + array('ssn' => 'chk_ssn_dob_unique:ssn,dob,' . $patient_id);
        }elseif($request['dob'] == "" && $request['ssn'] != ""){
			 Validator::extend('chk_ssn_dob_unique', function($attribute, $value, $parameters) {
                $ssn_un = Input::get($parameters[0]);
                $dob_un = date('Y-m-d', strtotime(Input::get($parameters[1])));
                $patient_id_un = $parameters[2];
                if ($patient_id_un != '')
                    $count = Patient::where('ssn', $ssn_un)->where('id', '!=', $patient_id_un)->count();
                else
                    $count = Patient::where('ssn', $ssn_un)->count();
                /* if already same DOB and same ssn count is 1 return false */
                if ($count > 0)
                    return false;
                else
                    return true;
            });
            $rules = Patient::$rules + array('ssn' => 'chk_ssn_dob_unique:ssn,dob,' . $patient_id);
		}else {
            $rules = Patient::$rules;
        }

        /* DOB empty & Change the format 0000-00-00 */
        if ($request['dob'] == "" || $request['dob'] == "01/01/1901")
            $request['dob'] = "1901-01-01";
        /* DOB given   */
        if ($request['dob'] != "1901-01-01")
            $request['dob'] = date('Y-m-d', strtotime($request['dob']));
        /* Deceased date  */
        if (($request['deceased_date'] != "0000-00-00") && ($request['deceased_date'] != ""))
            $request['deceased_date'] = date('Y-m-d', strtotime($request['deceased_date']));
        /* New patient */
        if ($patient_id == '') {
            $request['created_by'] = $request['user_id'];
            $request['is_self_pay'] = 'Yes';
            $result = Patient::create($request);
            /* image field add */
            if (Input::hasFile('filefield')) {
                $filename = rand(11111, 99999);
                $extension = Input::hasFile('filefield') ? Input::file('filefield')->getClientOriginalExtension() : '.jpg';
                $filestoreName = $filename . '.' . $extension;
                $resize = array('150', '150');
                if (Input::hasFile('filefield')) {
                    $image = Input::file('filefield');
                    Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName);
                }
                $result->avatar_name = $filename;
                $result->avatar_ext = $extension;
            }
            /* Patient account number is created */
            $result->account_no = $this->create_patient_accno($result->id); //create patient account number
            $result->save();
            $patient_id = $result->id;
        } else {
            /* Already added patient */
            if (Patient::where('id', $patient_id)->count() > 0 && is_numeric($patient_id)) {
                /* Table check for the id  */
                $patients = Patient::findOrFail($patient_id);
                /* If Image file upload */
                if (Input::hasFile('filefield')) {
                    $old_filename = $patients->avatar_name;
                    $old_extension = $patients->avatar_ext;
                    $filestoreoldName = $old_filename . '.' . $old_extension;
                    /* image file storeformat */
                    $filename = rand(11111, 99999);
                    $extension = Input::hasFile('filefield') ? Input::file('filefield')->getClientOriginalExtension() : '.jpg';
                    $filestoreName = $filename . '.' . $extension;
                    $resize = array('150', '150');
                    if (Input::hasFile('filefield')) {
                        $image = Input::file('filefield');
                        Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName, $filestoreoldName);
                    }
                    $patients->avatar_name = $filename;
                    $patients->avatar_ext = $extension;
                }
                /* Old patient detail alter here */
                $patients->updated_by = $request['user_id'];
                $patients->updated_at = date("Y-m-d H:i:s");
                $patients->update($request);
            }
        }
        /* Patient insurance table added */
        if(isset($request['patient_insurance_details']) && !empty($request['patient_insurance_details']))
        foreach ($request['patient_insurance_details'] as $key => $list) {
            $arrData = array(
                "patient_id" => $patient_id,
                "category" => ($list['category']=='Other')?'Others':$list['category'],
                "relationship" => (isset($list['insurance_insured'])) ?$list['insurance_insured']:'',
                "insurance_id" => $list['insurance_id'],
                "last_name" => $list['insured_last_name'],
                "first_name" => $list['insured_first_name'],
                "middle_name" => $list['insured_middle_name'],
                "insured_gender" => $list['insured_gender'],
                "insured_dob" => (isset($list['effective_date']) && ($list['insured_dob'] != "- Nil -")) ? date("Y-m-d", strtotime(@$list['insured_dob'])) : '',
                "insured_address1" => (@$list['insured_address1'] != "") ? $list['insured_address1'] : '',
                "insured_address2" => (@$list['insured_address2'] != "") ? $list['insured_address2'] : '',
                "insured_city" => (isset($list['insured_city'])) ? $list['insured_city'] : '',
                "insured_state" => (isset($list['insured_state'])) ? $list['insured_state'] : '',
                "same_patient_address" => (isset($list['insurance_details']['is_patient_address'])) ? $list['insurance_details']['is_patient_address'] : 'no',
                "insured_zip5" => (@$list['insured_zip5'] != "") ? $list['insured_zip5'] : '',
                "insured_zip4" => (@$list['insured_zip4'] != "") ? $list['insured_zip4'] : '',
                "policy_id" => $list['policy_id'],
                "group_name" => (isset($list['insurance_insured'])) ?$list['group_name']:'',
                "status" => (isset($list['status'])) ?$list['status']:'',
                "effective_date" => (isset($list['effective_date']) && ($list['effective_date'] != "- Nil -") && ($list['effective_date'] != "")) ? date("Y-m-d", strtotime($list['effective_date'])) : '0000-00-00',
                "termination_date" => (isset($list['termination_date']) && ($list['termination_date'] != "- Nil -") && ($list['termination_date'] != "")) ? date("Y-m-d", strtotime($list['termination_date'])) : '0000-00-00',
            );
            /* Patient insurance new  or old patient */
            if (($list['insurance_type']) == 'new') {
                /* New patient insurance Added here */
                $patients = PatientInsurance::create($arrData);
                $patients->created_by = $user_id;
                $patients->created_at = date("Y-m-d H:i:s");
                $patients->save();
            } else {
                /* Patient  insurance detail update */
                $patients = PatientInsurance::findOrFail($list['id']);
                $patients->updated_by = $user_id;
                $patients->updated_at = date("Y-m-d H:i:s");
                $patients = $patients->update($arrData);
            }
        }
        /* Patient Contact details added and edit */
        if(isset($request['patient_occupation_details']) && !empty($request['patient_occupation_details']))
        foreach ($request['patient_occupation_details'] as $emp_list) {
            $emp_Data = array(
                "patient_id" => $patient_id,
                "category" => 'Employer',
                "employer_status" => $emp_list['employer_status'],
                "employer_name" => $emp_list['employer_name'],
                "employer_occupation" => '',
                "employer_student_status" => $emp_list['employer_student_status'],
            );
            if (empty($emp_list['id'])) {
                /* New Employer Contact add here */
                if($emp_list['employer_occupation'] !=""){
                    $patients_con = PatientContact::create($emp_Data);
                    $patients_con->created_at = date("Y-m-d H:i:s");
                    $patients_con->created_by = $user_id;
                    $patients_con->save();
                }
            } else {
                /* Already Added Patient details */
                $patients_con = PatientContact::findOrFail($emp_list['id']);
                $patients_con->updated_at = date("Y-m-d H:i:s");
                $patients_con->updated_by = $user_id;
                $patients_con->update($emp_Data);
            }
        }
        /* guarantor_contact_details add or edit */
        if(isset($request['patient_guarantor_contact_details']) && !empty($request['patient_guarantor_contact_details']))
        foreach ($request['patient_guarantor_contact_details'] as $gen_list) {
            $gen_Data = array(
                "patient_id" => $patient_id,
                "category" => 'Guarantor',
                "guarantor_last_name" => @$gen_list['guarantor_last_name'],
                "guarantor_middle_name" => $gen_list['guarantor_middle_name'],
                "guarantor_first_name" => $gen_list['guarantor_first_name'],
                "guarantor_relationship" => $gen_list['guarantor_relationship'],
                "guarantor_cell_phone" => $gen_list['guarantor_cell_phone'],
                "guarantor_address1" => $gen_list['guarantor_address1'],
                "guarantor_address2" => $gen_list['guarantor_address2'],
                "guarantor_city" => $gen_list['guarantor_city'],
                "guarantor_state" => $gen_list['guarantor_state'],
                "guarantor_zip5" => $gen_list['guarantor_zip5'],
                "guarantor_zip5" => $gen_list['guarantor_zip4'],
            );
            /* Table add or edit contact_deatails  */
            if (empty($gen_list['id'])) {
                /* New Guarantor add on Patient_contact table */
                if($gen_list['guarantor_last_name'] !="" && $gen_list['guarantor_first_name'] !=""){
                    $patients_con = PatientContact::create($gen_Data);
                    $patients_con->created_at = date("Y-m-d H:i:s");
                    $patients_con->created_by = $user_id;
                    $patients_con->save();
                }
            } else {
                /* Already Added Guarantor */
                $patients_con = PatientContact::findOrFail($gen_list['id']);
                $patients_con->updated_at = date("Y-m-d H:i:s");
                $patients_con->updated_by = $user_id;
                $patients_con->update($gen_Data);
            }
        }
        //patient_emergency_contact_details
        if(isset($request['patient_emergency_contact_details']) && !empty($request['patient_emergency_contact_details']))
        foreach ($request['patient_emergency_contact_details'] as $emergency_list) {
            /* emergency details get */
            $emergency_Data = array(
                "patient_id" => $patient_id,
                "category" => 'Emergency Contact',
                "emergency_last_name" => isset($emergency_list['emergency_last_name'])?$emergency_list['emergency_last_name']:'',
                "emergency_first_name" => isset($emergency_list['emergency_first_name'])?$emergency_list['emergency_first_name']:'',
                "emergency_middle_name" => isset($emergency_list['emergency_middle_name'])?$emergency_list['emergency_middle_name']:'',
                "emergency_relationship" => isset($emergency_list['emergency_relationship'])?$emergency_list['emergency_relationship']:'',
                "emergency_cell_phone" => isset($emergency_list['emergency_cell_phone'])?$emergency_list['emergency_cell_phone']:'',
                "emergency_address1" => isset($emergency_list['emergency_address1'])?$emergency_list['emergency_address1']:'',
                "emergency_address2" => isset($emergency_list['emergency_address2'])?$emergency_list['emergency_address2']:'',
                "emergency_city" => isset($emergency_list['emergency_city'])?$emergency_list['emergency_city']:'',
                "emergency_state" => isset($emergency_list['emergency_state'])?$emergency_list['emergency_state']:'',
                "emergency_zip5" => isset($emergency_list['emergency_zip5'])?$emergency_list['emergency_zip5']:'',
                "emergency_zip4" => isset($emergency_list['emergency_zip4'])?$emergency_list['emergency_zip4']:'',
            );
            /* New emergency */
            if (empty($emergency_list['id'])) {
                    $pat_contact = PatientContact::create($emergency_Data);
                    $patients_con->created_at = date("Y-m-d H:i:s");
                    $patients_con->created_by = $user_id;
                    $patients_con->save();
            } else {
                /* Already added emergency */
                $patients_con = PatientContact::findOrFail($emergency_list['id']);
                $patients_con->updated_at = date("Y-m-d H:i:s");
                $patients_con->updated_by = $user_id;
                $patients_con = $patients_con->update($emergency_Data);
            }
        }

        //patient_medical_history_details
        if(isset($request['patient_medical_history_details']) && !empty($request['patient_medical_history_details']))
        foreach ($request['patient_medical_history_details'] as $patient_medical_history) {
            $provider_id = $patient_medical_history['provider_id'];
            $questionariesDatas = Questionnaries::where('provider_id', $provider_id)->first();
			
            if($questionariesDatas){
                $patient_id = $patient_medical_history['patient_id'];
                $templateId = $questionariesDatas->template_id;
                $questionnaries_id = $questionariesDatas->id;
                $option_id = $patient_medical_history['option_id'];
                $question_id = $patient_medical_history['question_id'];
                $answer = $patient_medical_history['answer'];

                $questionaries_data = array(
                    "patient_id" => $patient_id,
                    "template_id" => $templateId,
                    "questionnaries_template_id" => $question_id,
                    "questionnaries_option_id" => $option_id,
                    "answer" => $answer
                );
               
                $questionaries = QuestionnariesAnswer::create($questionaries_data);
                $questionaries->created_at = date("Y-m-d H:i:s");
                $questionaries->created_by = $provider_id;
                $questionaries->save();
			}
        }
    }

    /* create new patient patient acco */

    public function create_patient_accno($pat_id) {        
        $acc_no = Patient::generatePatientAccNo($pat_id);
        return $acc_no;
    }

    // manikandan work

    public function updatePatientImg() {
        try {
            $request = Request::all();

            $patientDatas = $request['patientDatas'];
            $patientData = $patientDatas[0];
            $patientId = $patientData['patient_id'];
            $providerId = $patientData['providerid'];
            $practiceId = $patientData['practiceid'];
            $userid = $patientData['userid'];
            if ($patientId != '') {
                $data = $patientData['patient_image'] == "" ? $patientData['patient_sig_image'] : $patientData['patient_image'];
                $imgFormat = $patientData['signature_image'];
                $updateImgStatus = $this->createImg($data, $imgFormat, $patientId, $practiceId, $userid);
                if ($updateImgStatus) {
                    $returnData = array(
                        'status' => '1',
                        'statusMessage' => 'Image Updated successfully',
                    );
                } else {
                    $returnData = array(
                        'status' => '0',
                        'statusMessage' => 'Image Updatation failed.Contact admin',
                    );
                }
            }
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
        return Response::json($returnData);
    }

    public function createImg($data, $imgFormat, $patientId, $practiceId, $providerId) {
        try {
            $res = Request::All();
            $path_mypatient = public_path() . '/';
            $path = $path_mypatient . 'patient_updatedImg_fromApp/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $data = str_replace('data:image/png;base64,', '', $data);
            $data = str_replace(' ', '+', $data);
            $data = base64_decode($data);
            $img_id = uniqid() . '.' . $imgFormat;
            $fileLocation = $path . '' . $img_id;
            $fileput = file_put_contents($fileLocation, $data);
            $db = new DBConnectionController();
            $db->connectPracticeDB($practiceId);
            $patients = Patient::findOrFail($patientId);
            if ($patients) {
                $old_filename = $patients->avatar_name;
                $old_extension = $patients->avatar_ext;
                $practiceName = md5('P' . $practiceId);
                $filestoreoldName = $old_filename . '.' . $old_extension;
                /* image file storeformat */
                $newFileName = rand(11111, 99999);
                $newExtension = '.jpg';
                $filestoreName = $newFileName . '.' . $newExtension;
                $resize = array('150', '150');
                $image_path = public_path() . '';
                $src = $fileLocation;
                $returnData = Helpers::mediauploadpath($practiceName, 'patient', '', '', $filestoreName, $filestoreoldName, '', $src);
                if ($returnData) {
                    $patients->avatar_name = $newFileName;
                    $patients->avatar_ext = $newExtension;
                    $patients->updated_by = $providerId;
                    $patients->updated_at = date("Y-m-d H:i:s");
                    $patients->save();

                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } catch (\Exception $e) {
            $current_date = date('Y-m-d');
            $current_time = date('Y-m-d H:i:s');
            $fp = fopen("App_err_log_" . $current_date . ".txt", 'a+');
            fwrite($fp, "\n Current Time => $current_time \n");
            fwrite($fp, " Err Message => " . $e->getMessage() . " \n");
            return Response::json(array('status' => '1', 'StatusMessage' => 'Invalid credential. Contact admin'));
        }
    }

}