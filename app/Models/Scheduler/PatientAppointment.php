<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;
use Config;
use Carbon\Carbon;
use App\Http\Helpers\Helpers as Helpers;

class PatientAppointment extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
	
	public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }

    public function degrees() {
        return $this->belongsTo('App\Models\Provider_degree', 'provider_degrees_id', 'id');
    }

    public function facility() {
        return $this->belongsTo('App\Models\Medcubics\Facility', 'facility_id', 'id');
    }

    public function provider() {
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
    }

    public function reasonforvisit() {
        return $this->belongsTo('App\Models\ReasonForVisit', 'reason_for_visit', 'id');
    }

    public function patient() {
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id')->with('patient_claim_fin','patient_insurance');
    }
    public function created_user()
    {
        return $this->belongsTo('App\Models\Medcubics\Users', 'created_by', 'id')->select('id', 'name', 'short_name');
    }      
    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function claim() {
        //return $this->belongsTo('App\Models\Patients\Claims', 'patient_id', 'patient_id');
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'patient_id', 'patient_id');
    }

    protected $fillable = [
        'provider_id', 'facility_id', 'patient_id', 'provider_scheduler_id', 'scheduled_on', 'appointment_time',
        'is_new_patient', 'reason_for_visit', 'status', 'checkin_time', 'checkout_time', 'copay_option', 'copay',
        'non_billable_visit', 'created_by', 'updated_by', 'rescheduled_from', 'rescheduled_reason', 'cancel_delete_reason', 'copay_details', 'copay_check_number', 'copay_card_type', 'copay_date'
    ];
    public static $rules = [
        'provider_id' => 'required',
        'facility_id' => 'required',
        'patient_search' => 'required_without:is_new_patient',
        'last_name' => 'required_with:is_new_patient',
        'first_name' => 'required_with:is_new_patient',
        'dob' => 'required_with:is_new_patient',
        'address1' => 'required_with:is_new_patient',
        'city' => 'required_with:is_new_patient',
        'state' => 'required_with:is_new_patient',
        'zip5' => 'required_with:is_new_patient|numeric|min:5',
        'scheduled_on' => 'required',
        'appointment_time' => 'required',
        'reason_for_visit' => 'required'
    ];
    public static $messages = [
        'provider_id.required' => 'Select provider',
        'facility_id.required' => 'Select facility',
        'last_name.required_with' => 'Enter last name',
        'first_name.required_with' => 'Enter first name',
        'dob.required_with' => 'Enter DOB',
        'address1.required_with' => 'Enter address1',
        'city.required_with' => 'Enter city',
        'state.required_with' => 'Enter state',
        'zip5.required_with' => 'Enter zip code',
        'zip5.min' => 'Enter valid zip code',
        'patient_search.required_without' => 'Select patient',
        'scheduled_on.required' => 'Select appointment date',
        'appointment_time.required' => 'Select appointment time',
        'reason_for_visit.required' => 'Enter reason for visit',
    ];

    public static function getAppointmentSlotTime($facility_id, $provider_id, $scheduled_on, $event_id = '') {
        $querey = PatientAppointment::where('facility_id', $facility_id)->where('provider_id', $provider_id)->where('scheduled_on', $scheduled_on)
                ->whereIn('status', ['Scheduled', 'Confirmed', 'Not Confirmed', 'Arrived', 'In Session', 'Complete']);

        if ($event_id != '')
            $querey->where('id', '!=', $event_id);

        $appointment_dates = $querey->get();
        $available_dates_arr = [];
        foreach ($appointment_dates as $available_dates) {
            $available_dates_arr[] = $available_dates->appointment_time;
        }
        return $available_dates_arr;
    }

    public static function getSchedulerCount($default_view_list_id, $selected_date, $default_view) {
        if ($default_view == Config::get('siteconfigs.scheduler.default_view_provider'))
            $field_name = 'provider_id';
        else
            $field_name = 'facility_id';
        $cur_month = date('m');
        $arr['scheduled'] = PatientAppointment::where($field_name, $default_view_list_id)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->where('status', 'Scheduled')->count();
        $arr['encounter'] = PatientAppointment::where($field_name, $default_view_list_id)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->where('status', 'Encounter')->count();
        $arr['completed'] = PatientAppointment::where($field_name, $default_view_list_id)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->where('status', 'Complete')->count();
        $arr['cancelled'] = PatientAppointment::where($field_name, $default_view_list_id)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->where('status', 'Cancelled')->count();
        $arr['no_show'] = PatientAppointment::where($field_name, $default_view_list_id)->whereRaw('MONTH(scheduled_on) = ?', [$cur_month])->where('status', 'No Show')->count();
        return $arr;
    }

    /*     * * getting Last App Date for patients starts ** */

    public static function getLastappointmentDate($id) {
        $value = PatientAppointment::whereRaw('Date(scheduled_on) != CURDATE()')->where('patient_id', $id)->where('status', 'Complete')->orderBy('scheduled_on', "DESC")->pluck('scheduled_on')->first();
        $date_format = ($value != '') ? Helpers::dateFormat($value, 'date') : '-Nil-';
        return $date_format;
    }

    /*     * * getting Last App Date for patients ends ** */
    /* Dashboard  */

    public static function getEncounter() {
        $end_date = (date("Y-m-d"));
        $total['encounter'] = PatientAppointment::where('status', 'Encounter')->count();
        $total_app = PatientAppointment::whereIn('status', ['Scheduled', 'Rescheduled', 'Encounter'])->count();
        if (!empty($total['encounter']) && !empty($total_app))
            $total['percentage'] = round(($total['encounter'] / $total_app) * 100, 2);
        else
            $total['percentage'] = 0;
        return $total;
    }

    public static function dashboardEncounter() {
        /* Current month Scheduled appointments */
        $curr_start_date = Carbon::now()->startOfMonth();
        $curr_start_date = explode(" ", $curr_start_date);
        $end_date = (date("Y-m-d"));
        $curr_mth_encounter = PatientAppointment::where('scheduled_on', '<=', $end_date)->where('scheduled_on', '>=', $curr_start_date[0])->where('status', 'Encounter')->count();
        /* Last month Scheduled appointments  */
        $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonth()->toDateString();
        $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->subMonth()->toDateString();
        $last_mth_encounter = PatientAppointment::where('scheduled_on', '<=', $lastDayofPreviousMonth)->where('scheduled_on', '>=', $firstDayofPreviousMonth)->where('status', 'Encounter')->count();
        /* Appointment calculate for current month not equal to empty  */
        if (($curr_mth_encounter > 0))
            $total_encounter = ($curr_mth_encounter / ($last_mth_encounter + $curr_mth_encounter)) * 100;
        else
            $total_encounter = 0;
        $encounter['total_encounter'] = $total_encounter;
        $encounter['curr_mth_encounter'] = $curr_mth_encounter;

        return $encounter;
    }
    /*  Patient Appointment status  changed in currrent Patient, same has provider, facility  */
    public static function updateAppointmentOnchargecreation($facility_id, $provider_id, $patient_id, $dos, $appointment_id){
        /* Date format change DB like 'YYYY-MM-DD'*/
        $dos =  Helpers::dateFormat($dos,'datedb'); 
		if(isset($appointment_id) && !empty($appointment_id)){
			$appt_id = Helpers::getEncodeAndDecodeOfId($appointment_id, 'decode');
			$status = ['Scheduled', 'Rescheduled', 'Encounter'];
			$appointment = self::where('id', $appt_id)->where('facility_id', $facility_id)->where('provider_id', $provider_id)->where('patient_id', $patient_id)->where('scheduled_on', $dos)->whereIn('status', $status);
			$appointments = $appointment->get();
			/*   Count the no.of appointment */
			if ($appointment->count() > 0) {
				foreach ($appointments as $appointment) {
					$appointment->update(['status' => 'Complete']);
					$appointment->save();
				}
			}
		}else{
			$status = ['Scheduled', 'Rescheduled', 'Encounter'];
			$appointment = self::where('facility_id', $facility_id)->where('provider_id', $provider_id)->where('patient_id', $patient_id)->where('scheduled_on', $dos)->whereIn('status', $status);
			$appointments = $appointment->get();
			/*   Count the no.of appointment */
			if ($appointment->count() > 0) {
				foreach ($appointments as $appointment) {
					$appointment->update(['status' => 'Complete']);
					$appointment->save();
				}
			}
		}
        
    }
    //appointment update query ends

}
