<?php

namespace App\Http\Controllers\Scheduler\Api;

use Auth;
use Request;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\ProviderScheduler as ProviderScheduler;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use App\Models\Patients\Patient as Patient;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Models\Patients\PatientEligibility as PatientEligibility;
use App\Models\EdiEligibility as EdiEligibility;
use App\Models\AddressFlag as AddressFlag;
use App\Models\ReasonForVisit as ReasonForVisit;
use App\Models\Patients\PatientAuthorization as PatientAuthorization;
use App\Models\Payments\PMTInfoV1;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App;
use Config;
use Cache;
use DB;
use Storage;
use Lang;
use Carbon;

class SchedulerApiController extends Controller {

    public function getIndexApi() {
        if (Cache::has('default_view_list_id')){
            $default_view_list_id = Cache::get('default_view_list_id');
            $timezone = Facility::where('id', $default_view_list_id)->pluck('timezone')->first();
            if($timezone)
                date_default_timezone_set($timezone);
        }
        $cur_date = date('Y-m-d');
        if (Cache::has('default_view'))
            $default_view = Cache::get('default_view');
        else
            $default_view = Config::get('siteconfigs.scheduler.default_view_facility');

        $scheduler_details = $this->getDefaultViewOptions($default_view);
        $default_view_list = $scheduler_details['default_view_list'];
        $default_view_list_id = $scheduler_details['default_view_list_id'];
        $resource_listing = $scheduler_details['resource_listing'];
        $resource_listing_id = $scheduler_details['resource_listing_id'];

        $index_stats_resp = $this->getappointmentStatsdynamic_countApi($cur_date . '::' . $cur_date, $default_view, $default_view_list_id, $resource_listing_id, 'week');
        $index_stats_resp_data = $index_stats_resp->getData();
        $index_stats_count = (array) $index_stats_resp_data->data->index_stats_count;

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('resource_listing', 'default_view_list', 'default_view_list_id', 'index_stats_count')));
    }

    public function getDefaultViewOptions($default_view) {
        $cur_date = date('Y-m-d');

        $default_view_list_id = '';
        if (Cache::has('default_view_list_id'))
            $default_view_list_id = Cache::get('default_view_list_id');

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_facility')) {
            $default_view_list = Facility::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('facility_name', 'ASC')->get();

            if ($default_view_list_id == '' || !is_numeric($default_view_list_id) || $default_view_list_id == null || $default_view_list_id == 'undefined') {
                if ((count($default_view_list) > 0))
                    $default_view_list_id = $default_view_list[0]->id;
                else {
                    $default_view_list_id = "";
                    //echo "<script>alert('Please add one Facility for the scheduler');</script>";
                }
                // $default_view_list_id = $default_view_list[0]->id; 
                Cache::forever('default_view_list_id', $default_view_list_id);
            }
            $resources = Provider::with('degrees')->GetResoucesById('facility_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('provider_name', 'ASC')->get();
        } elseif ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $default_view_list = Provider::with('degrees')->has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('provider_name', 'ASC')->get();

            if ($default_view_list_id == '' || !is_numeric($default_view_list_id) || $default_view_list_id == null || $default_view_list_id == 'undefined') {
                if ((count($default_view_list) > 0))
                    $default_view_list_id = $default_view_list[0]->id;
                else {
                    $default_view_list_id = "";
                    //echo "<script>alert('Please add one Provider for the scheduler');</script>";
                }
                //$default_view_list_id =$default_view_list[0]->id; 
                Cache::forever('default_view_list_id', $default_view_list_id);
            }
            $resources = Facility::GetResoucesById('provider_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('facility_name', 'ASC')->get();
        }

        $resource_listing = [];
        $resource_listing_id = '0';
        $colorCodes = Config::get('siteconfigs.scheduler.dynamic_color_code');
        $s = 0;

        foreach ($resources as $resources_detail) {
            if ($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
                $resources_detail['resource_name'] = str_limit($resources_detail->provider_name . ' ' . @$resources_detail->degrees->degree_name, 20, '...');
            else
                $resources_detail['resource_name'] = str_limit($resources_detail->facility_name, 25, '...');
            $resources_detail['rgb_color'] = $colorCodes[$s];
            $resource_listing_id .= ',' . $resources_detail->id;
            $resource_listing[] = $resources_detail;
            $s++;
        }
        $scheduler_detail['default_view_list'] = $default_view_list;
        $scheduler_detail['default_view_list_id'] = $default_view_list_id;
        $scheduler_detail['resource_listing'] = $resource_listing;
        $scheduler_detail['resource_listing_id'] = $resource_listing_id;
        return $scheduler_detail;
    }

    public function setDefaultAndResourceListApi($default_view = 'Facility') {
        $cur_date = date('Y-m-d');
        Cache::forever('default_view', $default_view);
        Cache::forget('default_view_list_id');

        $scheduler_details = $this->getDefaultViewOptions($default_view);
        $default_view_list = $scheduler_details['default_view_list'];
        $default_view_list_id = $scheduler_details['default_view_list_id'];
        $resource_listing = $scheduler_details['resource_listing'];
        $index_stats_count = PatientAppointment::getSchedulerCount($default_view_list_id, $cur_date, $default_view);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('resource_listing', 'default_view_list', 'default_view_list_id', 'index_stats_count')));
    }

    public function getCalendarResourcesApi($request_from, $default_view, $default_view_list_id, $resource_ids = '') {
        //$default_view = Cache::get('default_view');
        $resource_arr = explode(',', $resource_ids);
        $cur_date = date('Y-m-d');

        if ($request_from == 'list')
            Cache::forever('default_view_list_id', $default_view_list_id);

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
            $query = Provider::GetResoucesById('facility_id', $default_view_list_id, $cur_date);
        else
            $query = Facility::GetResoucesById('provider_id', $default_view_list_id, $cur_date);

        if ($request_from == 'json')
            $query->whereIn('id', $resource_arr);

        $query->where('status', 'Active');

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
            $query->orderBy('provider_name', 'ASC');
        else
            $query->orderBy('facility_name', 'ASC');

        $resources = $query->get();

        $resource_listing = [];
        $colorCodes = Config::get('siteconfigs.scheduler.dynamic_color_code');
        $s = 0;
        foreach ($resources as $resources_detail) {
            if ($request_from == 'json') {
                $resources_detail['id'] = $resources_detail->id;
                $resources_detail['eventColor'] = $colorCodes[$s];
                $resources_detail['title'] = $resources_detail->short_name;
                /* if($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
                  $resources_detail['title'] = str_limit($resources_detail->provider_name,25,'...');
                  else
                  $resources_detail['title'] = str_limit($resources_detail->facility_name,25,'...'); */
                $resource_listing[] = $resources_detail;
            } else {
                $resources_detail['rgb_color'] = $colorCodes[$s];
                // $resources_detail['resource_name'] = $resources_detail->short_name;
                if ($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
                    $resources_detail['resource_name'] = str_limit($resources_detail->provider_name . ' ' . @$resources_detail->degrees->degree_name, 20, '...');
                else
                    $resources_detail['resource_name'] = str_limit($resources_detail->facility_name, 25, '...');
                $resource_listing[] = $resources_detail;
            }
            $s++;
        }

        if ($request_from == 'json')
            return Response::json($resource_listing);
        else {
            $index_stats_count = PatientAppointment::getSchedulerCount($default_view_list_id, $cur_date, $default_view);
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('resource_listing', 'index_stats_count')));
        }
    }

    public function getCalendarEventsApi($default_view_list_id, $resource_ids) {
        $default_view = Cache::get('default_view');
        $appointments = [];
        $resource_ids_arr = ($resource_ids !== '' && !is_array($resource_ids)) ? explode(",", $resource_ids) : array(); //explode(",",$resource_ids);

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider'))
            $appointments_arr = PatientAppointment::with('patient')->where('provider_id', $default_view_list_id)->whereIn('facility_id', $resource_ids_arr)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete','Encounter'])->orderBy('facility_id', 'ASC')->get();
        else
            $appointments_arr = PatientAppointment::with('patient')->where('facility_id', $default_view_list_id)->whereIn('provider_id', $resource_ids_arr)->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete','Encounter'])->orderBy('provider_id', 'ASC')->get();

        $events_listing = [];
        foreach ($appointments_arr as $appointment) {
            $patient_name = $appointment['patient']['last_name'];

            if ($appointment['patient']['first_name'] != '')
                $patient_name .= ', ' . $appointment['patient']['first_name'];

            if ($appointment['patient']['middle_name'] != '')
                $patient_name .= ' ' . $appointment['patient']['middle_name'];

            $time_slot = explode('-', $appointment->appointment_time);
            $from = date('Y-m-d\TH:i:s.Z\Z', strtotime($appointment->scheduled_on . ' ' . $time_slot[0]));
            $to = date('Y-m-d\TH:i:s.Z\Z', strtotime($appointment->scheduled_on . ' ' . @$time_slot[1]));
            $event['id'] = $appointment->id;
            if ($appointment->status == "Complete" || $appointment->status == "Canceled")
                $event['color'] = "#df7a89";
            else
                $event['color'] = "";
            $event['title'] = $patient_name;
            $event['resourceId'] = ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) ? $appointment->facility_id : $appointment->provider_id;
            $event['start'] = $from;
            $event['end'] = $to;
            $events_listing[] = $event;
        }
        return Response::json($events_listing);
    }

    public function getAppointmentApi() {
        if (Cache::has('default_view_list_id')){
            $default_view_list_id = Cache::get('default_view_list_id');
            $timezone = Facility::where('id', $default_view_list_id)->pluck('timezone')->first();
            if($timezone)
                date_default_timezone_set($timezone);
        }
        $request = Request::all();

        $default_view = Cache::get('default_view');
        $cur_date = date('Y-m-d');//dd($cur_date);
        $default_view_list_id = $request['default_view_list_id'];
        $user_selected_time = explode('T', $request['user_selected_date']);
        $user_selected_date = date("Y-m-d", strtotime($user_selected_time[0]));
        $reason_visit = ReasonForVisit::where('status', 'Active')->pluck('reason', 'id')->all();

        $resources = $resource_available_dates = $user_already_selected_timeslot = $array_of_time = [];
        $resource_id = $request['resource_id'];
        $app_time = $request['resource_id'];
        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $facility_id = $resource_id;
            $provider_id = $default_view_list_id;
            $default_view_list_caption = 'Provider';
            $resource_caption = 'Facility';
            $appointment_slot = (ProviderScheduler::where('facility_id', $facility_id)->where('provider_id', $provider_id)->pluck('appointment_slot'));
            $default_view_list = Provider::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $resources = Facility::GetResoucesById('provider_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
        } else {
            $facility_id = $default_view_list_id;
            $provider_id = $resource_id;
            $default_view_list_caption = 'Facility';
            $resource_caption = 'Provider';
            $appointment_slot = (ProviderScheduler::where('provider_id', $provider_id)->where('facility_id', $facility_id)->value('appointment_slot'));
            $default_view_list = Facility::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
            $resources = Provider::GetResoucesById('facility_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        }
        if ($request['default_view_list_id']) {
            /* Add Slot time Ex:15 mins or 30 mins */
            $newtime = strtotime($request['user_selected_date'] . ' + ' . $appointment_slot . ' minute');
            $tmie_slot = date('h:i a', $newtime);
            /* User selected time */
            if (!empty($user_selected_time[1]))
                $hours = (date("h:i a", strtotime($user_selected_time[1])));
            else
                $hours = '';
            /* Appointment Scheduled */
        }
        $sch_app_time = @$hours . '-' . @$tmie_slot;
        $provider_available_dates = array();
        $provider_available_dates = array();
        if ($resources != '' && $provider_id != '')
            $provider_available_dates = ProviderSchedulerTime::has('providerscheduler')
                            ->whereHas('providerscheduler', function($q) {
                                $q->where('status', 'active');
                            })
                            ->where('facility_id', $facility_id)->where('provider_id', $provider_id)
                            ->where('schedule_date', '>=', $cur_date)->orderBy('schedule_date', 'ASC')->pluck('schedule_date', 'schedule_date')->all();

        if ($provider_available_dates != '') {
            if ($user_selected_date != '') {
                if (!in_array($user_selected_date, $provider_available_dates))
                    $user_selected_date = '';
                else {
                    $duration = ProviderScheduler::getProviderAppointmentSlotDuration($facility_id, $provider_id, $user_selected_date);
                    $user_already_selected_timeslot = PatientAppointment::getAppointmentSlotTime($facility_id, $provider_id, $user_selected_date);
                    $array_of_time_arr = $this->getAvailableTimeArr($facility_id, $provider_id, $user_selected_date, $duration);
                    if (is_array($array_of_time_arr[0]) && is_array($user_already_selected_timeslot)) {
                        $array_of_time[0] = array_diff($array_of_time_arr[0], $user_already_selected_timeslot);
                    }
                }
            }
            $provider_available_dates = implode(',', $provider_available_dates);
        }
        $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();

        /// Get address for usps ///
        $addressFlag['general']['address1'] = '';
        $addressFlag['general']['city'] = '';
        $addressFlag['general']['state'] = '';
        $addressFlag['general']['zip5'] = '';
        $addressFlag['general']['zip4'] = '';
        $addressFlag['general']['is_address_match'] = '';
        $addressFlag['general']['error_message'] = '';

        if (@$user_selected_time[1])
            $user_selected_slot_time = strtotime($user_selected_time[1]);
        else {
            //TO do Payment   
            $payment_check_no = []; //PMTInfoV1::where('check_no','<>','')->pluck('check_no')->all();
        }
        //dd($payment_check_no);
        $user_selected_slot_time = "";
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('reason_visit', 'default_view_list', 'resources', 'provider_available_dates', 'user_selected_date', 'sch_app_time', 'payment_check_no', 'insurances', 'user_already_selected_timeslot', 'array_of_time', 'addressFlag', 'default_view', 'default_view_list_id', 'resource_id', 'default_view_list_caption', 'resource_caption', 'user_selected_slot_time')));
    }

    public function getNewPatientApi() {
        /// Get address for usps ///
        $addressFlag['general']['address1'] = '';
        $addressFlag['general']['city'] = '';
        $addressFlag['general']['state'] = '';
        $addressFlag['general']['zip5'] = '';
        $addressFlag['general']['zip4'] = '';
        $addressFlag['general']['is_address_match'] = '';
        $addressFlag['general']['error_message'] = '';

        $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurances', 'addressFlag')));
    }

    public function getAvailableSlotTimeByDateApi() {
        $request = Request::all();
        $cur_date = date('Y-m-d');
        $default_view = $request['default_view'];
        $default_view_list_id = $request['default_view_list_id'];
        $resource_id = $request['resource_id'];

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $facility_id = $resource_id;
            $provider_id = $default_view_list_id;
        } else {
            $facility_id = $default_view_list_id;
            $provider_id = $resource_id;
        }

        $user_selected_date = date("Y-m-d", strtotime($request['user_selected_date']));
        $user_selected_time = '';
        $user_already_selected_timeslot = PatientAppointment::getAppointmentSlotTime($facility_id, $provider_id, $user_selected_date);
        $duration = ProviderScheduler::getProviderAppointmentSlotDuration($facility_id, $provider_id, $user_selected_date);
        $array_of_time_arr = $this->getAvailableTimeArr($facility_id, $provider_id, $user_selected_date, $duration);
        if ($array_of_time_arr[0] != '') {
            $array_of_time_arr_all = call_user_func_array('array_merge', $array_of_time_arr);
            $time_slot_arr = array_diff($array_of_time_arr_all, $user_already_selected_timeslot);
        } else
            $time_slot_arr = [];

        $array_of_time = '<option value="">-- Select --</option>';
        foreach ($time_slot_arr as $slot) {
            $array_of_time .= '<option value="' . $slot . '">' . $slot . '</option>';
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('array_of_time', 'user_already_selected_timeslot', 'user_selected_time','time_slot_arr')));
    }

    public function getPatientSearchResults($patient_search_category = '') {
        $request = Request::all();
        $query = Patient::with('insured_detail', 'insured_detail.insurance_details', 'authorization_details')
                ->select('*', 'id')
                ->where('status', 'Active');
            // For specific users find    
               if(strpos(trim($request['term']), "##")){
                    $srch_arr = explode('##', trim($request['term']));
                    $query->where("id","=",$srch_arr[1]);
               }
        $search_arr = explode(' ', trim($request['term'], ' '));
        if (count($search_arr) > 2) {
            foreach ($search_arr as $search) {
                $query->where(function($sub_query)use ($search) {
                    $sub_query->where('last_name', 'LIKE', '%' . $search . '%')->orWhere('first_name', 'LIKE', '%' . $search . '%')->orWhere('account_no', 'LIKE', '%' . $search . '%')->orWhere('ssn', 'LIKE', '%' . $search . '%')->orWhere('address1', 'LIKE', '%' . $search . '%')->orWhere('address2', 'LIKE', '%' . $search . '%')->orWhere('city', 'LIKE', '%' . $search . '%')->orWhere('state', 'LIKE', '%' . $search . '%')->orWhere('zip5', 'LIKE', '%' . $search . '%')->orWhere('zip4', 'LIKE', '%' . $search . '%');
                });
            }
        } elseif (count($search_arr) == 2) {
            $query->where(function($sub_query)use ($search_arr) {
                $sub_query->where('first_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('first_name', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('last_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('last_name', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('account_no', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('account_no', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('ssn', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('ssn', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('address1', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address1', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('address2', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address2', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('city', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('city', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('state', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('state', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('zip5', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip5', 'LIKE', '%' . $search_arr[1] . '%')->orWhere('zip4', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip4', 'LIKE', '%' . $search_arr[1] . '%');
            });
        } elseif (count($search_arr) < 2) {
            if (preg_match("^\\d{1,2}/\\d{2}/\\d{4}^", $search_arr[0])) {
                $search1 = date("Y-m-d", strtotime($search_arr[0]));
                $query->where(function($sub_query)use ($search_arr, $search1) {
                    $sub_query->where('last_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('first_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('account_no', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('dob', 'LIKE', '%' . $search1 . '%')->orWhere('ssn', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address1', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address2', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('city', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('state', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip5', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip4', 'LIKE', '%' . $search_arr[0] . '%');
                });
            } else {
                $query->where(function($sub_query)use ($search_arr) {
                    $sub_query->where('last_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('first_name', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('account_no', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('ssn', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address1', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('address2', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('city', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('state', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip5', 'LIKE', '%' . $search_arr[0] . '%')->orWhere('zip4', 'LIKE', '%' . $search_arr[0] . '%');
                });
            }
        }

        $patients_arr = $query->orderBy('last_name',"ASC")->take(20)->get();
        $patients = [];
        foreach ($patients_arr as $patient_details) {
            $patient_details->patient_encodeid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_details->id, 'encode');
            $patient_details->value = $patient_details->last_name . " " . $patient_details->first_name;

            if ($patient_details->dob != '0000-00-00')
                $patient_details->value .= ", " . App\Http\Helpers\Helpers::dateFormat($patient_details->dob, 'dob');
            /* Patient balance show in New Appointment in existing patient */

            if ($patient_details->ssn != '')
                $patient_details->value .= ", " . $patient_details->ssn;
            foreach ($patient_details->insured_detail as $insured_detail) {
                if (@$insured_detail->insurance_id === @$patient_details->authorization_details[0]->insurance_id) {
                    if (count($patient_details->authorization_details) > 0)
                        $patient_details->auth_remain = ($patient_details->authorization_details[0]->alert_visit_remains >= $patient_details->authorization_details[0]->visit_remaining) ? $patient_details->authorization_details[0]->visit_remaining : '';
                    else
                        $patient_details->auth_remain = '';
                } else
                    $patient_details->auth_remain = '';
            }
            $patient_details->zipcode = $patient_details->zip5;
            if ($patient_details->zip4 != '' && $patient_details->zip4 != '0___')
                $patient_details->zipcode .= '-' . $patient_details->zip4;

            if ($patient_details->dob == '0000-00-00') {
                $patient_details->dob = '';
            } else {
                $patient_details->dob = App\Http\Helpers\Helpers::dateFormat($patient_details->dob, 'dob');
            }
            $patient_details->balance = Patient::getPatientAR($patient_details->id);
            //  $patient_details->value.=", ".$patient_details->balance;
            foreach ($patient_details->insured_detail as $patient_insurance) {                
                if ($patient_insurance->category == 'Primary' && isset($patient_insurance->insurance_details)) {
                    $patient_details->primary_insurance = @$patient_insurance->insurance_details->insurance_name;
                    $patient_details->primary_insurance_policy_id = @$patient_insurance->policy_id;
                    $patient_id = $patient_insurance->patient_id;
                    $insurance_id = $patient_insurance->insurance_id;
                    $getauth_alert = PatientAuthorization::getalertonAuthorization($patient_id, $insurance_id);
                    $patient_details->autorization_detail = $getauth_alert;
                    $plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient_details->id, @$patient_insurance->insurance_id, @$patient_insurance->policy_id);
                    if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
                        $getReachEndday = 0;
                    } else {
                        $now = strtotime(date('Y-m-d')); // or your date as well
                        $your_date = strtotime($plan_end_date);
                        $datediff = $now - $your_date;
                        $getReachEndday = floor($datediff / (60 * 60 * 24));
                    }
                    $patient_details->getReachEndday = $getReachEndday;
                } elseif ($patient_insurance->category == 'Secondary' && isset($patient_insurance->insurance_details) ) {
                    $patient_details->secondary_insurance = $patient_insurance->insurance_details->insurance_name;
                    $patient_details->secondary_insurance_policy_id = $patient_insurance->policy_id;
                }
            }
            $patients[] = $patient_details;
        }
        //dd($patients);
        return Response::json($patients);
    }
	
	

    public function getResourcesByDefaultViewListId() {
        $request = Request::all();
        $default_view = $request['default_view'];
        $default_view_list_id = $request['default_view_list_id'];
        $cur_date = date('Y-m-d');
        $resources = '<option value="">-- Select --</option>';

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $resources_arr = Facility::GetResoucesById('provider_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('facility_name', 'ASC')->get();

            foreach ($resources_arr as $resource) {
                $resources .= '<option value="' . $resource->id . '">' . $resource->facility_name . '</option>';
            }
        } else {
            $resources_arr = Provider::GetResoucesById('facility_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('provider_name', 'ASC')->get();

            foreach ($resources_arr as $resource) {
                $resources .= '<option value="' . $resource->id . '">' . $resource->provider_name . '</option>';
            }
        }
        return Response::json($resources);
    }

    public function getScheduleDatesByResourceId() {
        $request = Request::all();
        $default_view = $request['default_view'];
        $default_view_list_id = $request['default_view_list_id'];
        $resource_id = $request['resource_id'];
        $cur_date = date('Y-m-d');
        $provider_available_dates = '';

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $facility_id = $resource_id;
            $provider_id = $default_view_list_id;
        } else {
            $facility_id = $default_view_list_id;
            $provider_id = $resource_id;
        }

        if ($facility_id != '' && $provider_id != '') {
            // Provider Schedule date ( Start date ) is showed 
            $cur_date = ProviderScheduler::getProviderScheduleDate($facility_id, $provider_id);
            //Get Provider Scheduler table in available date show here
            $provider_available_dates = ProviderSchedulerTime::GetScheduleDatesByProviderAndFacilityId($facility_id, $provider_id, $cur_date)->orderBy('schedule_date', 'ASC')->pluck('schedule_date', 'schedule_date')->all();
            $provider_available_dates = implode(',', $provider_available_dates);
        }
        return Response::json($provider_available_dates);
    }

    public function storeAppointment() {
        $request = Request::all();
        if ($request['copay_option'] == '') {
          $request['copay'] = $request['copay_check_number'] = $request['copay_card_type'] = $request['copay_date'] = $request['copay_details'] = "";
        }
        if(isset($request['money_order_no']) && $request['money_order_no'] != '' && $request['copay_option'] !='' ){
            $request['copay_check_number'] = $request['money_order_no'];
        }

        $default_view = $request['default_view'];
        $default_view_list_id = $request['default_view_list_id'];
        $resource_id = $request['resource_id'];
        $scheduled_on = date("Y-m-d", strtotime($request['scheduled_on']));
        $appointment_time = $request['appointment_time'];
        $cur_date = date('Y-m-d');
        if ($request['dob'] == "" || $request['dob'] == "01/01/1901") {
            $request['dob'] = "1901-01-01";
        }

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $request['facility_id'] = $facility_id = $resource_id;
            $request['provider_id'] = $provider_id = $default_view_list_id;
        } else {
            $request['facility_id'] = $facility_id = $default_view_list_id;
            $request['provider_id'] = $provider_id = $resource_id;
        }
        // For existing patients zip validation issue while create appointment
        // Rev. 1 - Ravi - 01-10-2019
        $rules = PatientAppointment::$rules;
        if(isset($request['patient_id'])) {
            unset($rules['zip5']);
        }

        $validator = Validator::make($request, $rules, PatientAppointment::$messages);
        if (!$validator->fails()) { 
            $return_arr = $this->checkAndGetScheduerAvailableDate($facility_id, $provider_id, $scheduled_on);
            $available_appointment_dates = $return_arr['available_appointment_dates'];
            $to_get_provider_scheduler_id = $return_arr['to_get_provider_scheduler_id'];

            if (in_array($appointment_time, $available_appointment_dates)) {
                $patient_id = $this->addNewOrExistingPatientForAppointment($request);
                if (($request['patient_id'] == '' || $request['patient_id'] == 'new') and $request['patient_temp_id'] != 'null' and $request['patient_temp_id'] != '') {
                    $patient_temp_id = $request['patient_temp_id'];
                    $PatientEligibility = PatientEligibility::where('temp_patient_id', $patient_temp_id)->first();
                    $get_filename = $PatientEligibility->edi_filename;
                    $get_temp_file_path = $PatientEligibility->edi_file_path;

                    $get_firstfilepath = substr($get_temp_file_path, 0, 25);
                    $new_tempfilepath = $get_firstfilepath . $patient_id . '/';

                    if (App::environment() == 'production')
                        $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
                    else
                        $path_medcubic = public_path() . '\\';

                    mkdir($path_medcubic . str_replace('/', '\\', $new_tempfilepath));
                    copy($path_medcubic . str_replace('/', '\\', $get_temp_file_path) . $get_filename, $path_medcubic . str_replace('/', '\\', $new_tempfilepath) . $get_filename);
                    unlink($path_medcubic . str_replace('/', '\\', $get_temp_file_path) . $get_filename);

                    PatientEligibility::where('temp_patient_id', '=', $patient_temp_id)->update(['patients_id' => $patient_id, 'edi_file_path' => $new_tempfilepath, 'temp_patient_id' => '']);
                    EdiEligibility::where('temp_patient_id', '=', $patient_temp_id)->update(['patient_id' => $patient_id, 'temp_patient_id' => '']);
                }
                /*  Check in and check out time calculation. If Empty Appointment time apply the date  */
                $appointment_time = $request['appointment_time'];
                if ($appointment_time != "") {
                    $appointment_time = explode('-', strtoupper($appointment_time));
                    //Empty check & check out
                    if (($request['check_in_time'] == '') && ($request['check_out_time'] == '')) {
                        $request['check_in_time'] = $appointment_time[0];
                        $request['check_out_time'] = $appointment_time[1];
                    } elseif (($request['check_in_time'] == '') && ($request['check_out_time'] != '')) {
                        $request['check_in_time'] = $appointment_time[0];
                    }
                }
                $request['patient_id'] = $patient_id;
                $request['created_by'] = Auth::user()->id;
                $request['status'] = 'Scheduled';
                $request['scheduled_on'] = date("Y-m-d", strtotime($scheduled_on));
                $request['provider_scheduler_id'] = array_search($appointment_time, $to_get_provider_scheduler_id);
                $request['copay_date'] = ($request['copay_date'] !== '') ? date("Y-m-d", strtotime($request['copay_date'])) : '';
                $result = PatientAppointment::create($request);
                $result->checkin_time = $request['check_in_time'];
                $result->checkout_time = $request['check_out_time'];
                $result->copay_details = @$request['copay_details'];
                $result->save();

                if ($request['copay_option'] != '') {
                    $pay_type = 'scheduler';
                    $pay_type_id = $result->id;
                    $pay_chk_no = $pay_chk_date = $pay_crd_type = "";
                    if ($request['copay_option'] == "Cash") {
                        $payment_mode = "Cash";
                    } elseif ($request['copay_option'] == "Check") {
                        $payment_mode = "Check";
                        $pay_chk_no = $request['copay_check_number'];
                        $pay_chk_date = $request['copay_date'];
                    } elseif ($request['copay_option'] == "CC") {
                        $payment_mode = "Credit";
                        $pay_crd_type = $request['copay_card_type'];
                        $pay_chk_date = $request['copay_date'];
                    }elseif ($request['copay_option'] == "Money Order") {
                        $payment_mode = "Money Order";
                        $pay_crd_type = $request['money_order_no'];
                        $request['money_order_date'] = $request['copay_date'];
                        $pay_chk_date = $request['copay_date'];
                    }
                    $pay_data = array('payment_method' => 'Patient', 'payment_type' => 'Payment', 'payment_mode' => $payment_mode, 'payment_amt' => $request['copay'], 'check_no' => $pay_chk_no, 'check_date' => $pay_chk_date, 'card_type' => $pay_crd_type, 'patient_id' => $patient_id);
                    /* Payment table store in  called to ApymentV1APIController file */
                    $request['patient_id'] = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
                    $request['payment_amt_pop'] = $request['copay'];
                    $request['payment_mode'] = $payment_mode;
                    $request['reference'] =  $request['copay_details'];
                    $request['copay_detail'] =  $request['copay_details'];
                    $request['payment_method'] = 'Patient';
                    $request['payment_type'] = 'Payment';
                    $request['copay_amt'] = $request['copay'];
                    $request['name_on_card'] = $pay_crd_type;
                    $request['cardexpiry_date'] = $request['copay_date']; 
                    $request['check_date'] = $pay_chk_date;
                    $request['source'] = 'scheduler';
                    $request['check_no'] = $pay_chk_no; 
                    $paymentV1 = new PaymentV1ApiController();
                    $paymentV1->createWalletData($request);                   
                    
                    //PMTInfoV1::savePaymentDetail($pay_data, $pay_type, $pay_type_id);
                }
                return Response::json(array('status' => 'success', 'message' => null));
            } else {
                return Response::json(array('status' => 'error', 'message' => null));
            }
        } else {            
            return Response::json(array('status' => 'error', 'message' => "Please try again later"));
        }
    }

    public function getAppointmentDetailsApi() {
        $request = Request::all();
        $cur_date = '0000-00-00';
        $event_id = $request['event_id'];
        $array_of_time = [];
        $appointment_details = PatientAppointment::with('patient', 'facility', 'provider', 'provider.degrees', 'patient.insured_detail')->where('id', $event_id)->first();
        $reason_visit = ReasonForVisit::where('status', 'Active')->pluck('reason', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('reason_visit', 'appointment_details')));
    }

    public function getAppointmentByEventApi() {
        $request = Request::all();
        $cur_date = '0000-00-00';
        $event_id = $request['event_id'];
        $visit_status = $request['visit_status'];
        $default_view = Cache::get('default_view');
        $array_of_time = [];
        $appointment_details = PatientAppointment::with('patient', 'facility', 'provider', 'provider.degrees', 'patient.insured_detail', 'patient.insured_detail.insurance_details')->where('id', $event_id)->first();

        $facility_id = $appointment_details->facility_id;
        $provider_id = $appointment_details->provider_id;
        $user_selected_date = $appointment_details->scheduled_on;
        $user_selected_time = $appointment_details->appointment_time;
        $reason_visit = ReasonForVisit::where('status', 'Active')->pluck('reason', 'id')->all();

        $providers = [];
        $provider_available_dates = $user_already_selected_timeslot = $array_of_time = [];
        $facilities = Facility::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                    $q->where('schedule_date', '>=', $cur_date);
                })->where('status', 'Active')->orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();

        if ($facility_id != '')
            $providers = Provider::GetResoucesByFacility($facility_id, $cur_date)->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();

        if ($providers != '' && $provider_id != '')
            $provider_available_dates = ProviderSchedulerTime::has('providerscheduler')
                            ->whereHas('providerscheduler', function($q) {
                                $q->where('status', 'active');
                            })
                            ->where('facility_id', $facility_id)->where('provider_id', $provider_id)
                            ->where('schedule_date', '>=', $cur_date)->orderBy('schedule_date', 'ASC')->pluck('schedule_date', 'schedule_date')->all();

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $resource_id = $facility_id;
            $default_view_list_id = $provider_id;
            $default_view_list_caption = 'Provider';
            $resource_caption = 'Facility';

            $default_view_list = Provider::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $resources = Facility::GetResoucesById('provider_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
        } else {
            $resource_id = $provider_id;
            $default_view_list_id = $facility_id;
            $default_view_list_caption = 'Facility';
            $resource_caption = 'Provider';

            $default_view_list = Facility::has('providerschedulertime')->whereHas('providerschedulertime', function($q) use($cur_date) {
                        $q->where('schedule_date', '>=', $cur_date);
                    })->where('status', 'Active')->orderBy('facility_name', 'ASC')->pluck('facility_name', 'id')->all();
            $resources = Provider::GetResoucesById('facility_id', $default_view_list_id, $cur_date)->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        }

        if ($provider_available_dates != '') {
            if ($user_selected_date != '') {
                if (!in_array($user_selected_date, $provider_available_dates))
                    $user_selected_date = '';
            }
            $user_already_selected_timeslot = PatientAppointment::getAppointmentSlotTime($facility_id, $provider_id, $user_selected_date);
            $provider_available_dates = implode(',', $provider_available_dates);
            $duration = ProviderScheduler::getProviderAppointmentSlotDuration($facility_id, $provider_id, $user_selected_date);
            $array_of_time_arr = $this->getAvailableTimeArr($facility_id, $provider_id, $user_selected_date, $duration);
            $user_selected_time_slot_arr = [$user_selected_time => $user_selected_time];
            $user_already_selected_timeslot = array_diff($user_already_selected_timeslot, $user_selected_time_slot_arr);
            if ((count($array_of_time_arr) > 0) && ( $array_of_time_arr[0] != '' ))
                $array_of_time[0] = array_diff($array_of_time_arr[0], $user_already_selected_timeslot);
            else
                $array_of_time[0] = [];
        }
        /* Issue MED-1637
         * Error Desc: Edit Popup appointment did not show
         * Fix: "Rescheduled" was misspelled as "Reschedule"
         * Date: June 1, 2017, Nallasivam
         */
        //dd($appointment_details->status  == 'Scheduled');
        if ($visit_status == 'Reschedule') {
            $visit_status = ['Rescheduled' => 'Rescheduled'];
        }else if($visit_status == 'NoShow') {
            $visit_status = 'NoShow';
        } else {
            if ($appointment_details->status == 'Scheduled')
                $visit_status = ['Scheduled' => 'Scheduled', 'Rescheduled' => 'Rescheduled', 'Canceled' => 'Canceled', 'No Show' => 'No Show'];
            elseif ($appointment_details->status == 'Complete')
                $visit_status = ['Complete' => 'Complete'];
            elseif ($appointment_details->status == 'Canceled')
                $visit_status = ['Canceled' => 'Canceled'];
            elseif ($appointment_details->status == 'No Show')
                $visit_status = ['No Show' => 'No Show'];
        }
        $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();

        /// Get address for usps ///
        $addressFlag['general']['address1'] = '';
        $addressFlag['general']['city'] = '';
        $addressFlag['general']['state'] = '';
        $addressFlag['general']['zip5'] = '';
        $addressFlag['general']['zip4'] = '';
        $addressFlag['general']['is_address_match'] = '';
        $addressFlag['general']['error_message'] = '';

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('reason_visit', 'facilities', 'facility_id', 'providers', 'provider_id', 'provider_available_dates', 'user_selected_date', 'insurances', 'appointment_details', 'visit_status', 'array_of_time', 'user_already_selected_timeslot', 'addressFlag', 'default_view_list', 'resources', 'default_view', 'default_view_list_id', 'resource_id', 'default_view_list_caption', 'resource_caption')));
    }

    public function updateAppointment() {
        $request = Request::all();
		if ($request['copay_option'] == '') {
          $request['copay'] = $request['copay_check_number'] = $request['copay_card_type'] = $request['copay_date'] = $request['copay_details'] = "";
        }
        if(isset($request['money_order_no']) && $request['money_order_no'] != '' && $request['copay_option'] !='' ){
            $request['copay_check_number'] = $request['money_order_no'];
        }
        
		if(!isset($request['event_id']))
			return Response::json(array('status' => 'error', 'message' => "Invalid event ID"));
		
        $event_id = $request['event_id'];
        $status = $request['visit_status'];
        $cur_date = date('Y-m-d');

        $default_view = $request['default_view'];
        $default_view_list_id = $request['default_view_list_id'];
        $resource_id = $request['resource_id'];

        if ($request['dob'] == "" || $request['dob'] == "01/01/1901") {
            $request['dob'] = "1901-01-01";
        }

        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $request['facility_id'] = $facility_id = $resource_id;
            $request['provider_id'] = $provider_id = $default_view_list_id;
        } else {
            $request['facility_id'] = $facility_id = $default_view_list_id;
            $request['provider_id'] = $provider_id = $resource_id;
        }
        /*
            ## Appointment time Check in & check out time  
        */
        $appointment_time = $request['appointment_time'];
         if ($appointment_time != "") {
             $app_time = explode('-', strtoupper($appointment_time));
             $request['check_in_time'] = $app_time[0];
             $request['check_out_time'] = $app_time[1];
         }
        if ($status != 'Rescheduled') {
            $appointment = PatientAppointment::where('id', $event_id)->first();
            $appointment->status = $status;
            $appointment->reason_for_visit = $request['reason_for_visit'];
            $appointment->checkin_time = $request['check_in_time'];
            $appointment->checkout_time = $request['check_out_time'];
            $appointment->copay_option = $request['copay_option'];
            $appointment->copay = $request['copay'];
            $appointment->copay_card_type = $request['copay_card_type'];
            $appointment->copay_check_number = $request['copay_check_number'];
            $request['copay_date'] = ($request['copay_date'] !== '') ? date("Y-m-d", strtotime($request['copay_date'])) : '';
            $appointment->copay_date = $request['copay_date'];
            $appointment->copay_details = $request['copay_details'];
            if ($status == 'Canceled' || $status == 'No Show') {
                $appointment->cancel_delete_reason = $request['reason'];
            }
            $appointment->non_billable_visit = $request['non_billable_visit'];

            $appointment->save();
             if ($request['copay_option'] != '') {
                    $pay_type = 'scheduler';
                    $pay_type_id = $event_id;
                    $pay_chk_no = $pay_chk_date = $pay_crd_type = "";
                    if ($request['copay_option'] == "Cash") {
                        $payment_mode = "Cash";
                    } elseif ($request['copay_option'] == "Check") {
                        $payment_mode = "Check";
                        $pay_chk_no = $request['copay_check_number'];
                        $pay_chk_date = $request['copay_date'];
                    } elseif ($request['copay_option'] == "CC") {
                        $payment_mode = "Credit";
                        $pay_crd_type = $request['copay_card_type'];
                        $pay_chk_date = $request['copay_date'];
                    }elseif ($request['copay_option'] == "Money Order") {
                        $payment_mode = "Money Order";
                        $pay_crd_type = $request['money_order_no'];
                        $request['money_order_date'] = $request['copay_date'];
                        $pay_chk_date = $request['copay_date'];
                    }
                    $pay_data = array('payment_method' => 'Patient', 'payment_type' => 'Payment', 'payment_mode' => $payment_mode, 'payment_amt' => $request['copay'], 'check_no' => $pay_chk_no, 'check_date' => $pay_chk_date, 'card_type' => $pay_crd_type, 'patient_id' => $request['patient_id']);
                    /* Payment table store in  called to PaymentV1APIController file */
                    $request['patient_id'] = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'encode');
                    $request['payment_amt_pop'] = $request['copay'];
                    $request['payment_mode'] = $payment_mode;
                    $request['reference'] =  $request['copay_details'];
                    $request['copay_detail'] =  $request['copay_details'];
                    $request['payment_method'] = 'Patient';
                    $request['payment_type'] = 'Payment';
                    $request['copay_amt'] = $request['copay'];
                    $request['name_on_card'] = $pay_crd_type;
                    $request['cardexpiry_date'] = $request['copay_date']; 
                    $request['check_date'] = $pay_chk_date;
                    $request['source'] = 'scheduler';
                    $request['check_no'] = $pay_chk_no; 
                    $paymentV1 = new PaymentV1ApiController();
                    $paymentV1->createWalletData($request);                   
                    
                }

            return Response::json(array('status' => 'success', 'message' => null));
        } else {
            $validator = Validator::make($request, PatientAppointment::$rules, PatientAppointment::$messages);

            if ($validator->fails()) {
                $facility_id = $request['facility_id'];
                $provider_id = $request['provider_id'];
                $scheduled_on = date("Y-m-d", strtotime($request['scheduled_on']));
                $appointment_time = $request['appointment_time'];
                $cur_date = date('Y-m-d');

                $return_arr = $this->checkAndGetScheduerAvailableDate($facility_id, $provider_id, $scheduled_on, $event_id);
                $available_appointment_dates = $return_arr['available_appointment_dates'];
                $to_get_provider_scheduler_id = $return_arr['to_get_provider_scheduler_id'];
                if (in_array($appointment_time, $available_appointment_dates)) {
                    $request['patient_id'] = $this->addNewOrExistingPatientForAppointment($request);
                    $request['created_by'] = Auth::user()->id;
                    $request['status'] = 'Scheduled';
                    $request['scheduled_on'] = date("Y-m-d", strtotime($scheduled_on));
                    $request['provider_scheduler_id'] = array_search($appointment_time, $to_get_provider_scheduler_id);
                    $patient_appt = PatientAppointment::create($request);
                    //Appointment  Analysis Reports: When rescheduled, paid date is incorrect Start
                    $patient_appt->copay_date = date("Y-m-d", strtotime($request['copay_date']));
                    //End
                    $patient_appt->checkin_time = $request['check_in_time'];
                    $patient_appt->checkout_time = $request['check_out_time'];
                    $patient_appt->copay_details = $request['copay_details'];
                    $patient_appt->save();

                    $appointment = PatientAppointment::where('id', $event_id)->first();
                    $appointment->status = 'Rescheduled';
                    $appointment->reason_for_visit = $request['reason_for_visit'];
                    $appointment->checkin_time = $request['check_in_time'];
                    $appointment->checkout_time = $request['check_out_time'];
                    $appointment->copay_option = $request['copay_option'];
                    $appointment->copay = $request['copay'];
                    $appointment->copay_details = $request['copay_details'];
                    $appointment->non_billable_visit = $request['non_billable_visit'];
                    $appointment->rescheduled_reason = 'Rescheduled via popup';
                    $appointment->rescheduled_from = $patient_appt->id;
                    $appointment->save();

                    return Response::json(array('status' => 'success', 'message' => $patient_appt->id));
                } else {
                    return Response::json(array('status' => 'error', 'message' => null));
                }
            }
            return Response::json(array('status' => 'success', 'message' => null));
        }
    }

    public function addNewOrExistingPatientForAppointment($request) {
        if ($request['patient_id'] == 'new' || $request['patient_id'] == '') {
            $bill_cycle['A-G'] = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
            $bill_cycle['H-M'] = ['h', 'i', 'j', 'k', 'l', 'm'];
            $bill_cycle['N-S'] = ['n', 'o', 'p', 'q', 'r', 's'];
            $bill_cycle['T-Z'] = ['t', 'u', 'v', 'w', 'x', 'y', 'z'];
            $first_letter = strtolower(@$request['last_name'][0]);
            if (in_array($first_letter, $bill_cycle['A-G']))
                $bill_cycle = 'A - G';
            elseif (in_array($first_letter, $bill_cycle['H-M']))
                $bill_cycle = 'H - M';
            elseif (in_array($first_letter, $bill_cycle['N-S']))
                $bill_cycle = 'N - S';
            elseif (in_array($first_letter, $bill_cycle['T-Z']))
                $bill_cycle = 'T - Z';

            if ($request['dob'] != "" && $request['dob'] != "1901-01-01") {
                $dob = explode("/", $request['dob']);
                $curMonth = date("m");
                $curDay = date("j");
                $curYear = date("Y");
                $age = $curYear - $dob[2];
                if ($curMonth < $dob[0] || ($curMonth == $dob[0] && $curDay < $dob[1]))
                    $age--;
                $dob_val = date('Y-m-d', strtotime($request['dob']));
            } else {
                $age = '';
                $dob_val = $request['dob'];
            }

            $patient = new Patient;
            $patient->last_name = $request['last_name'];
            $patient->first_name = $request['first_name'];
            $patient->middle_name = $request['middle_name'];
            $patient->dob = $dob_val;
            $patient->gender = $request['gender'];
            $patient->address1 = $request['address1'];
            $patient->address2 = $request['address2'];
            $patient->city = $request['city'];
            $patient->state = $request['state'];
            $patient->zip5 = $request['zip5'];
            $patient->zip4 = $request['zip4'];
            $patient->phone = $request['mobile'];
            $patient->work_phone = $request['home_phone'];
            $patient->is_self_pay = $request['is_self_pay'];
            $patient->ssn = $request['ssn'];
            $patient->bill_cycle = $bill_cycle;
            $patient->percentage = '100';
            $patient->demo_percentage = '60';
            $patient->ins_percentage = '40';

            if ($age >= 18)
                $patient->demographic_status = 'Complete';
            else
                $patient->demographic_status = 'Incomplete';

            $patient->save();
            $patient->account_no = App\Models\Patients\Patient::generatePatientAccNo($patient->id);
            $patient->save();

            /* Starts - address flag update */
            $address_flag = array();
            $address_flag['type'] = 'patients';
            $address_flag['type_id'] = $patient->id;
            $address_flag['type_category'] = 'general_information';
            $address_flag['address2'] = $request['general_address1'];
            $address_flag['city'] = $request['general_city'];
            $address_flag['state'] = $request['general_state'];
            $address_flag['zip5'] = $request['general_zip5'];
            $address_flag['zip4'] = $request['general_zip4'];
            $address_flag['is_address_match'] = $request['general_is_address_match'];
            $address_flag['error_message'] = $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            /* Ends - address flag update */

            if ($patient) {
                $request['patient_id'] = $patient->id;
                if ($request['primary_insurance_id'] != '' && $request['primary_insurance_policy_id'] != '') {
                    $pateint_insurance = new PatientInsurance;
                    $pateint_insurance->patient_id = $patient->id;
                    $pateint_insurance->last_name = $request['last_name'];
                    $pateint_insurance->first_name = $request['first_name'];
                    $pateint_insurance->middle_name = $request['middle_name'];
                    $pateint_insurance->insurance_id = $request['primary_insurance_id'];
                    $pateint_insurance->policy_id = $request['primary_insurance_policy_id'];
                    $pateint_insurance->category = 'Primary';
                    $pateint_insurance->save();
                }

                /* 	if($request['secondary_insurance_id'] != '' && $request['secondary_insurance_policy_id'] != '')
                  {
                  $pateint_insurance_sec = new PatientInsurance;
                  $pateint_insurance_sec->patient_id = $patient->id;
                  $pateint_insurance_sec->last_name = $request['last_name'];
                  $pateint_insurance_sec->first_name = $request['first_name'];
                  $pateint_insurance_sec->middle_name = $request['middle_name'];
                  $pateint_insurance_sec->insurance_id = $request['secondary_insurance_id'];
                  $pateint_insurance_sec->policy_id = $request['secondary_insurance_policy_id'];
                  $pateint_insurance_sec->category = 'Primary';
                  $pateint_insurance_sec->save();
                  } */
            }
            return $patient->id;
        }
        return $request['patient_id'];
    }

    public function checkAndGetScheduerAvailableDate($facility_id, $provider_id, $scheduled_on, $event_id = '') {
        $provider_timer = ProviderSchedulerTime::GetScheduleDatesByProviderAndFacilityId($facility_id, $provider_id)->where('schedule_date', $scheduled_on)->get();
        $array_of_time = [];
        $to_get_provider_scheduler_id = [];
        foreach ($provider_timer as $availableDates) {
            $starttime = $availableDates->from_time;
            $endtime = $availableDates->to_time;
            $duration = ProviderScheduler::getProviderAppointmentSlotDuration($facility_id, $provider_id, $scheduled_on);
            $avail_date_slot = App\Http\Helpers\Helpers::getTimeSlotByGivenTime($starttime, $endtime, $duration);
            $to_get_provider_scheduler_id[$availableDates->id] = $avail_date_slot;
            $array_of_time = $array_of_time + $avail_date_slot;
        }
        $existing_appointment_slot = PatientAppointment::getAppointmentSlotTime($facility_id, $provider_id, $scheduled_on, $event_id);
        $available_appointment_dates = array_diff($array_of_time, $existing_appointment_slot);
        $return_arr['available_appointment_dates'] = $available_appointment_dates;
        $return_arr['to_get_provider_scheduler_id'] = $to_get_provider_scheduler_id;
        return $return_arr;
    }

    public function getAvailableTimeArr($facility_id, $provider_id, $scheduled_on, $duration) {
        $provider_available_dates = ProviderSchedulerTime::has('providerscheduler')
                        ->whereHas('providerscheduler', function($q) {
                            $q->where('status', 'active');
                        })
                        ->where('facility_id', $facility_id)->where('provider_id', $provider_id)
                        ->where('schedule_date', $scheduled_on)->get();
        $array_of_time = [];
        $exclude_past_time = 'no';
         /*  
            ### Appointment slot time is Currently stop in Preview date show option Enabled.

        if ($scheduled_on <= date("Y-m-d"))
           $exclude_past_time = 'yes';
        */
        foreach ($provider_available_dates as $availableDates) {
            $starttime = $availableDates->from_time;
            $endtime = $availableDates->to_time;
            $array_of_time[] = App\Http\Helpers\Helpers::getTimeSlotByGivenTime($starttime, $endtime, $duration, $exclude_past_time);
        }

        return $array_of_time;
    }

    public function rescheduleAppointmentWithDrag() {
        $request = Request::all();
        $event_id = $request['id'];
        $default_view = Cache::get('default_view');
        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {
            $provider_id = $request['default_view_list_id'];
            $facility_id = $request['resource_id'];
        } else {
            $facility_id = $request['default_view_list_id'];
            $provider_id = $request['resource_id'];
        }
        $start_date = strtotime($request['start_date']);
        $scheduled_on = date("Y-m-d", $start_date);
        $from_time = date("h:i a", $start_date);
        $end_date = $request['end_date'];
        $cur_date = date('Y-m-d');
        $duration = ProviderScheduler::getProviderAppointmentSlotDuration($facility_id, $provider_id, $scheduled_on);
        $to_time = $start_date + (60 * $duration);
        $to_time = date("h:i a", $to_time);
        $available_time_slot_details = $this->checkAndGetScheduerAvailableDate($facility_id, $provider_id, $scheduled_on);
        $available_time_slot = $available_time_slot_details['available_appointment_dates'];
        $to_get_provider_scheduler_id = $available_time_slot_details['to_get_provider_scheduler_id'];
        $appointment_time = $from_time . '-' . $to_time;
        $start_date = explode(' ', $request['start_date']);
        $today = date("Y-m-d");
        $time = date("H:i");
        $am_pm = date("a");
        /* Drag the Appointment time for passed date and time   */
        /** past date move option is enable. 
        if ((($start_date[0]) >= ($today))/*  && ($start_date[1] >= $time) && (($start_date[2] == $am_pm) || ($start_date[2] == $am_pm)) ) {*/
            if (count($available_time_slot) > 0) {
                if (in_array($appointment_time, $available_time_slot)) {
                    $event_details = PatientAppointment::where('id', $event_id)->first();
                    $request['provider_id'] = $provider_id;
                    $request['facility_id'] = $facility_id;
                    $request['status'] = 'Scheduled';
                    $request['patient_id'] = $event_details->patient_id;
                    $request['reason_for_visit'] = $event_details->reason_for_visit;
                    $request['copay_option'] = $event_details->copay_option;
                    $request['copay'] = $event_details->copay;
                    $request['copay_details'] = @$event_details->copay_details;
                    $request['provider_scheduler_id'] = @$event_details->provider_scheduler_id;
                    $request['is_new_patient'] = @$event_details->is_new_patient;
                    $request['checkin_time'] = @$event_details->checkin_time;
                    $request['checkout_time'] = @$event_details->checkout_time;
                    $request['rescheduled_from'] = @$event_details->rescheduled_from;
                    $request['rescheduled_reason'] = @$event_details->rescheduled_reason;
                    $request['cancel_delete_reason'] = @$event_details->cancel_delete_reason;
                    $request['copay_check_number'] = @$event_details->copay_check_number;
                    $request['copay_card_type'] = @$event_details->copay_card_type;
                    $request['copay_date'] = @$event_details->copay_date;
                    $request['non_billable_visit'] = $event_details->non_billable_visit;
                    $request['updated_at'] = '0000-00-00 00:00:00';
                    $request['scheduled_on'] = $scheduled_on;
                    $request['appointment_time'] = $appointment_time;
                    $request['created_by'] = Auth::user()->id;
                    $request['provider_scheduler_id'] = array_search($appointment_time, $to_get_provider_scheduler_id);
                    $patient_appt = PatientAppointment::create($request);

                    $event_details->status = 'Rescheduled';
                    $event_details->rescheduled_reason = 'Drag and Drop';
                    $event_details->rescheduled_from = $patient_appt->id;
                    $event_details->save();

                    return Response::json(array('status' => 'success', 'message' => 'Updated Successfully'));
                } else {
                    return Response::json(array('status' => 'error', 'message' => 'Appointment not available'));
                }
            } else {
                return Response::json(array('status' => 'error', 'message' => 'Provider not available'));
            }
        /*} else {
            return Response::json(array('status' => 'error', 'message' => 'Please move to future date'));
        }*/

        return Response::json(array('status' => 'success', 'message' => null));
    }

    public function appointmentdeletecancelprocess($event_str_arr) {
        $request = Request::all();
        $event_arr = explode("::", $event_str_arr);
        $event_id = $event_arr[0];
        $operation = $event_arr[1];
        $cancel_delete_reason = $request['reason'];
        if ($operation == 'cancel')
            PatientAppointment::where('id', $event_id)->update(['status' => 'Canceled', 'cancel_delete_reason' => $cancel_delete_reason]);
        else {
            PatientAppointment::where('id', $event_id)->update(['cancel_delete_reason' => $cancel_delete_reason]);
            PatientAppointment::where('id', $event_id)->delete();
        }
        return Response::json(array('status' => 'success', 'event_id' => $event_id, 'operation' => $operation));
    }

    /*** Start to New Select the Reason for Visit	 ***/
    public function addnewApi($addedvalue) {
        $request = Request::all();
        $tablename = $request['tablename'];
        $fieldname = $request['fieldname'];

        if (DB::table($tablename)->where($fieldname, '=', $addedvalue)->where('deleted_at',null)->count() == 0) {
            $data[$fieldname] = $addedvalue;
            $data['created_by'] = Auth::user()->id;
            $data['created_at'] = date('Y-m-d h:i:s');
            $data['updated_at'] = date('Y-m-d h:i:s');
            DB::table($tablename)->insert($data);
            $get_id = DB::getPdo()->lastInsertId();
            return $get_id;
        } else {
            return "error";
        }
    }
    /*** End to New Select the Reason for Visit	 ***/

    public function getappointmentStatsdynamic_countApi($scheduler_calendar_val, $default_view_option_val, $default_view_list_option_val, $resource_option_val, $view_option) {

        if ($default_view_option_val == 'Provider') {
            $field_name = 'provider_id';
            $res_field_name = 'facility_id';
        } else {
            $field_name = 'facility_id';
            $res_field_name = 'provider_id';
        }
        $selected_date = $scheduler_calendar_val;
        $resource_ids_arr = ($resource_option_val !== '' && !is_array($resource_option_val)) ? explode(",", $resource_option_val) : array(); //explode(",",$resource_option_val);

        if ($view_option == 'week') {
            $selected_date_arr = explode('::', $selected_date);
            $from_date = date('Y-m-d', strtotime($selected_date_arr[0]));//dd($from_date);
            $end_date = date('Y-m-d', strtotime($selected_date_arr[1]));
            /*
              $index_stats_count['scheduled'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on','>=',$from_date)->where('scheduled_on','<=',$end_date)->where('status','Scheduled')->count();
              $index_stats_count['checkin'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on','>=',$from_date)->where('scheduled_on','<=',$end_date)->where('status','In Session')->count();
              $index_stats_count['completed'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on','>=',$from_date)->where('scheduled_on','<=',$end_date)->where('status','Complete')->count();
              $index_stats_count['cancelled'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on','>=',$from_date)->where('scheduled_on','<=',$end_date)->whereIn('status',['Cancelled','No Show'])->count();
             */
            $mytime = Carbon\Carbon::now();
            $mytime->toDateString();
           
            $total_appointments = PatientAppointment::where($field_name, $default_view_list_option_val)->whereIn($res_field_name, $resource_ids_arr)
                            ->where('scheduled_on', '>=', $from_date)->where('scheduled_on', '<=', $end_date)
                            //->select(DB::raw('sum(case when status = "Scheduled" then 1 else 0 end) AS scheduled_count, sum(case when status = "In Session" then 1 else 0 end) AS no_show_count, sum(case when (status = "Complete") then 1 else 0 end) AS completed_count, sum(case when (status = "Cancelled" OR status = "No Show") then 1 else 0 end) AS cancelled_count') )->first();    
                            ->select(DB::raw('sum(case when (status = "Scheduled") then 1 else 0 end) AS scheduled_count, sum(case when status = "No Show" then 1 else 0 end) AS no_show_count, sum(case when (status = "Complete") then 1 else 0 end) AS completed_count, sum(case when status = "Canceled" then 1 else 0 end) AS canceled_count, sum(case when status = "Encounter" then 1 else 0 end) AS encounter_count'))->first();
            
            $index_stats_count['encounter'] = ($total_appointments->encounter_count) ? $total_appointments->encounter_count : 0;
            $index_stats_count['scheduled'] = ($total_appointments->scheduled_count) ? $total_appointments->scheduled_count : 0;
            $index_stats_count['no_show'] = ($total_appointments->no_show_count) ? $total_appointments->no_show_count : 0;
            $index_stats_count['completed'] = ($total_appointments->completed_count) ? $total_appointments->completed_count : 0;
            $index_stats_count['canceled'] = ($total_appointments->canceled_count) ? $total_appointments->canceled_count : 0;
        } else {
            /*
              $index_stats_count['scheduled'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on',$selected_date)->where('status','Scheduled')->count();
              $index_stats_count['checkin'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on',$selected_date)->where('status','In Session')->count();
              $index_stats_count['completed'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on',$selected_date)->where('status','Complete')->count();
              $index_stats_count['cancelled'] = PatientAppointment::where($field_name,$default_view_list_option_val)->whereIn($res_field_name,$resource_ids_arr)->where('scheduled_on',$selected_date)->whereIn('status',['Cancelled','No Show'])->count();
             */
            $total_appointments = PatientAppointment::where($field_name, $default_view_list_option_val)->whereIn($res_field_name, $resource_ids_arr)->where('scheduled_on', $selected_date)
                    ->select(DB::raw('sum(case when status = "Scheduled" then 1 else 0 end) AS scheduled_count, sum(case when status = "In Session" then 1 else 0 end) AS checkin_count, sum(case when (status = "Complete") then 1 else 0 end) AS completed_count, sum(case when (status = "Canceled" OR status = "No Show") then 1 else 0 end) AS canceled_count, sum(case when status = "Encounter" then 1 else 0 end) AS encounter_count'))
                    ->first();
            $index_stats_count['encounter'] = ($total_appointments->encounter_count) ? $total_appointments->encounter_count : 0;
            $index_stats_count['scheduled'] = ($total_appointments->scheduled_count) ? $total_appointments->scheduled_count : 0;
            $index_stats_count['checkin'] = ($total_appointments->checkin_count) ? $total_appointments->checkin_count : 0;
            $index_stats_count['completed'] = ($total_appointments->completed_count) ? $total_appointments->completed_count : 0;
            $index_stats_count['canceled'] = ($total_appointments->canceled_count) ? $total_appointments->canceled_count : 0;
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('index_stats_count')));
    }

    public function geteventschedulardate($event_id) {
        $appointment_details = PatientAppointment::where('id', $event_id)->first();
        return Response::json(array('status' => 'success', 'scheduled_on_date' => $appointment_details['scheduled_on']));
    }

    public function checknoApiUnique($check_no, $patientId) {
        $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($check_no, 'Patient', 'Check', 'patientPayment',$patientId);
       
        if ($checkNoStatus) {
            return Response::json(array('status' => 'error', 'message' => 'The copay check number has already been taken', 'data' => $checkNoStatus));
        } else {
            return Response::json(array('status' => 'success', 'message' => 'erAPI', 'data' => $checkNoStatus));
        }
    }
	
	public function getDocumentPatientSearchResults($patient_search_category = '') {
        $request = Request::all();
        $query = Patient::with('insured_detail', 'insured_detail.insurance_details', 'authorization_details')
                ->select('*', 'id')
                ->where('status', 'Active');
            // For specific users find    
               if(strpos(trim($request['term']), "##")){
                    $srch_arr = explode('##', trim($request['term']));
                    $query->where("id","=",$srch_arr[1]);
               }

        $search_arr = explode(' ', trim($request['term'], ' '));

			
            $dynamic_name = trim($request['term']);
			$dynamic_name = str_replace("'",'',$dynamic_name);
			$dynamic_name = str_replace('"','',$dynamic_name);
            $query->Where(function ($sub_query) use ($dynamic_name) {
                if (strpos($dynamic_name, ",") !== false) {
                    $nameArr = explode(",", $dynamic_name);
                    $temp = explode(" ", @trim($nameArr[1]));
                    $nameStr = trim($nameArr[0]);
                    if(isset($temp[0])) 
                        $nameStr =$nameStr." ".trim($temp[0]).((isset($temp[1])) ? " ".trim($temp[1]) :'');
                    $query = $sub_query->orWhere(DB::raw('CONCAT(last_name," ", first_name, " ", middle_name)'),  'like', "%{$nameStr}%" )->orWhere('account_no', 'LIKE', '%' . $nameStr . '%')->orWhere('ssn', 'LIKE', '%' . $nameStr . '%');
                } else {
                   
                    $query = $sub_query->orWhere(function ($sub_query) use ($dynamic_name) {
                        $sub_sql = '';
                        $searchValues = array_filter(explode(" ", $dynamic_name));
                        if(isset($searchValues[1])){
                            $nameStr =trim(@$searchValues[0])." ".trim(@$searchValues[1]);
                            $query = $sub_query->orWhere(DB::raw('CONCAT(last_name," ", first_name, " ", middle_name)'),  'like', "%{$nameStr}%" )->orWhere('account_no', 'LIKE', '%' . $nameStr . '%')->orWhere('ssn', 'LIKE', '%' . $nameStr . '%');
                        } else {
                            $sub_sql = '';
                            foreach ($searchValues as $searchKey) {
                                $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                                $sub_sql .= "last_name LIKE '%$searchKey%' OR first_name LIKE '%$searchKey%' OR middle_name LIKE '%$searchKey%' OR account_no LIKE '%$searchKey%' OR ssn LIKE '%$searchKey%'";
                            }
                            if ($sub_sql != '')
                                $sub_query->whereRaw($sub_sql);
                        }
                    });
                }
            });
       




       

        $patients_arr = $query->orderBy('first_name','asc')->take(20)->get();
        $patients = [];
        foreach ($patients_arr as $patient_details) {
            $patient_details->patient_encodeid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_details->id, 'encode');
            $patient_details->value = $patient_details->last_name . " " . $patient_details->first_name;

            if ($patient_details->dob != '0000-00-00')
                $patient_details->value .= ", " . App\Http\Helpers\Helpers::dateFormat($patient_details->dob, 'dob');
            /* Patient balance show in New Appointment in existing patient */

            if ($patient_details->ssn != '')
                $patient_details->value .= ", " . $patient_details->ssn;
            foreach ($patient_details->insured_detail as $insured_detail) {
                if (@$insured_detail->insurance_id === @$patient_details->authorization_details[0]->insurance_id) {
                    if (count($patient_details->authorization_details) > 0)
                        $patient_details->auth_remain = ($patient_details->authorization_details[0]->alert_visit_remains >= $patient_details->authorization_details[0]->visit_remaining) ? $patient_details->authorization_details[0]->visit_remaining : '';
                    else
                        $patient_details->auth_remain = '';
                } else
                    $patient_details->auth_remain = '';
            }
            $patient_details->zipcode = $patient_details->zip5;
            if ($patient_details->zip4 != '' && $patient_details->zip4 != '0___')
                $patient_details->zipcode .= '-' . $patient_details->zip4;

            if ($patient_details->dob == '0000-00-00') {
                $patient_details->dob = '';
            } else {
                $patient_details->dob = App\Http\Helpers\Helpers::dateFormat($patient_details->dob, 'dob');
            }
            $patient_details->balance = Patient::getPatientAR($patient_details->id);
            //  $patient_details->value.=", ".$patient_details->balance;
            foreach ($patient_details->insured_detail as $patient_insurance) {                
                if ($patient_insurance->category == 'Primary' && isset($patient_insurance->insurance_details)) {
                    $patient_details->primary_insurance = @$patient_insurance->insurance_details->insurance_name;
                    $patient_details->primary_insurance_policy_id = @$patient_insurance->policy_id;
                    $patient_id = $patient_insurance->patient_id;
                    $insurance_id = $patient_insurance->insurance_id;
                    $getauth_alert = PatientAuthorization::getalertonAuthorization($patient_id, $insurance_id);
                    $patient_details->autorization_detail = $getauth_alert;
                    $plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient_details->id, @$patient_insurance->insurance_id, @$patient_insurance->policy_id);
                    if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
                        $getReachEndday = 0;
                    } else {
                        $now = strtotime(date('Y-m-d')); // or your date as well
                        $your_date = strtotime($plan_end_date);
                        $datediff = $now - $your_date;
                        $getReachEndday = floor($datediff / (60 * 60 * 24));
                    }
                    $patient_details->getReachEndday = $getReachEndday;
                } elseif ($patient_insurance->category == 'Secondary' && isset($patient_insurance->insurance_details) ) {
                    $patient_details->secondary_insurance = $patient_insurance->insurance_details->insurance_name;
                    $patient_details->secondary_insurance_policy_id = $patient_insurance->policy_id;
                }
            }
            $patients[] = $patient_details;
        }
        //dd($patients);
        return Response::json($patients);
    }

}
