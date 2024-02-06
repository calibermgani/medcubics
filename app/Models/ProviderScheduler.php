<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;
use DB;
use Config;
use App\Models\ProviderSchedulerTime as ProviderSchedulerTime;
use App\Models\Facility as Facility;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\CronController as CronController;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;

class ProviderScheduler extends Model
{
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

    public function provider_type_details()
    {
        return $this->belongsTo('App\Models\Provider_type','provider_types_id','id');
    }
	
    public function facility()
    {
        return $this->belongsTo('App\Models\Facility');
    }
	
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider','provider_id','id');
    }
	
    public function provider_scheduler_time()
    {
        return $this->belongsTo('App\Models\ProviderSchedulerTime','id','provider_scheduler_id');
    }

    protected $fillable = [ 
                    'provider_id','facility_id','start_date','end_date','no_of_occurrence','end_date_option','schedule_type','repeat_every',
                    'weekly_available_days','monthly_visit_type','monthly_visit_type_date','monthly_visit_type_day_week','monthly_visit_type_day_dayname',
                    'monthly_visit_type_week','monday_selected_times','tuesday_selected_times','wednesday_selected_times','thursday_selected_times',
                    'friday_selected_times','saturday_selected_times','sunday_selected_times','provider_reminder_sms','provider_reminder_phone',
                    'provider_reminder_email','patient_reminder_sms','patient_reminder_phone', 'patient_reminder_email','notes','status',
                    'created_by','updated_by','appointment_slot'        
    ];
    public static $rules = [ 
                    'last_name' => 'required',
                    'npi' => 'nullable|digits:10',
                    'phone' => 'required',
                    'fax' => 'required',
                    'email' => 'nullable|email',
                    'website' => 'nullable|url',
                    'address_1' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'zipcode5' => 'required|digits:5'
    ];

    public static $messages = [
                    'last_name.required' => 'Enter your last name!',
                    'provider_types_id.required' => 'Select any provider types!',	
                    'provider_types_id.provider_type_validator' => 'Selected provider type already exists',	
                    'additional_provider_type.additional_provider_type_validator' => 'Selected provider type already exists',	
                    'npi.required' => 'Enter npi number!',
                    'npi.digits' => 'Enter valid npi number!',
                    'phone.required' => 'Enter phone!',
                    'fax.required' => 'Enter fax!',
                    'email.email' => 'Enter valid email!',
                    'website.url' => 'Enter valid website!',
                    'address_1.required' => 'Enter address1!',
                    'city.required' => 'Enter city!',
                    'state.required' => 'Enter state!',
                    'zipcode5.required' => 'Enter zipcode!',
                    'zipcode5.digits' => 'Enter valid zipcode5!',
    ];
	
	public static function getAllProviderSchedulerByFacilityId($facility_id,$provider_id)
    {
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
        return ProviderScheduler::where('facility_id',$facility_id)->where('provider_id',$provider_id)->orderBy('id','ASC')->get();
    }
	
	public static function getAllfacilitySchedulerByProviderId($provider_id,$facility_id)
    {
		$facility_id = Helpers::getEncodeAndDecodeOfId($facility_id,'decode');
		$provider_id = Helpers::getEncodeAndDecodeOfId($provider_id,'decode');
        return ProviderScheduler::where('provider_id',$provider_id)->where('facility_id',$facility_id)->orderBy('id','ASC')->get();
    }
	
    public static function getProviderAppointmentSlotDuration($facility_id,$provider_id, $schedule_date)
    {
        $appointment_slot = ProviderScheduler::has('provider_scheduler_time')->whereHas('provider_scheduler_time', function($q) use($schedule_date){$q->where('schedule_date', $schedule_date);})->where('facility_id',$facility_id)->where('provider_id',$provider_id)->orderBy('id','ASC')->pluck('appointment_slot')->first();
        if($appointment_slot == 0 || $appointment_slot == '')
            $appointment_slot = Config::get('siteconfigs.provider_scheduler.appointment_slot');
        return $appointment_slot;
    }
	/***   Get provider alert message function starts   ***/
	public static function getProviderRecord($alertby,$alertbefore)
	{
		if($alertby !="" && $alertbefore !="")
		{ 
			$provider_record 	= ProviderSchedulerTime::with('provider_scheduler','facility','provider')
								->whereHas('provider_scheduler', function($q){$q->whereRaw('(provider_reminder_sms ="on" or provider_reminder_email ="on" or provider_reminder_phone ="on")');})
								->whereRaw('(sms_reminder_status ="No" or phone_reminder_status ="No" or email_reminder_status ="No")')->orderBy("provider_id","ASC")->orderBy("facility_id","ASC")->take(10);
			if($alertby =="hour")	$identify 	= 	'+'.$alertbefore.' hours';
			if($alertby =="day")	$identify 	= 	$alertbefore.' day';
			$intimate_date 		=  	date('Y-m-d', strtotime($identify));
			$day_represent 		= 	strtolower(date('l', strtotime($identify)));
			
			$provider_record->where('schedule_date',$intimate_date)->where("day",$day_represent);
			if($alertby =="hour")
			{
				$intimate_time_hr 	=  	date('h', strtotime($identify));
				$intimate_time_zone =  	date('a', strtotime($identify));
				$get_result = $provider_record->where("from_time","LIKE",$intimate_time_hr."%".$intimate_time_zone)->get()->toArray();
			} elseif($alertby =="day") {
				$get_result = $provider_record->get()->toArray();
			}
			$get_message =[];
			
			$sms_fac_s_count = $sms_fac_d_count=$new_s_count=$new_p_count=$new_m_count=$phone_fac_s_count=$phone_fac_d_count =$mail_fac_s_count=$mail_fac_d_count =1;
			$s_key = $p_key=$m_key =0;
			$set_sm =$set_ph =$set_em ='';
			$set_msg_sms =$set_msg_ph =$set_msg_em ='';
			//dd($get_result);
			foreach($get_result as $key => $val)
			{
				$sms_msg = $phone_msg=$mail_msg =''; 
				$xp_key = (isset($get_result[$key-1]['provider_id'])) ? $get_result[$key-1]['provider_id'] : '';
				$xf_key = (isset($get_result[$key-1]['facility_id'])) ? $get_result[$key-1]['facility_id'] : '';
				if($val['provider_scheduler']['provider_reminder_sms']=="on") 
				{
					$result = self::getArray($s_key,$get_result[$key]['provider_id'],$xp_key,$get_result[$key]['facility_id'],$xf_key,$sms_fac_s_count,$sms_fac_d_count,$set_sm,$val['facility']['facility_name'],$set_msg_sms);
					$sms_fac_s_count 	= 	$result['sms_fac_s_count'];
					$set_msg_sms 		= 	$result['set_msg_sms'];
					$sms_fac_d_count 	= 	$result['sms_fac_d_count'];
					$set_sm 			= 	$result['set_sm'];
					$sms_msg 			=	$result['sms_msg'];
					$s_key++;
				}
				if($val['provider_scheduler']['provider_reminder_phone']=="on") 
				{
					$result = self::getArray($p_key,$get_result[$key]['provider_id'],$xp_key,$get_result[$key]['facility_id'],$xf_key,$phone_fac_s_count,$phone_msg,$phone_fac_d_count,$set_ph,$val['facility']['facility_name'],$set_msg_ph);
					$phone_fac_s_count 	= 	$result['sms_fac_s_count'];
					$set_msg_ph 		= 	$result['set_msg_sms'];
					$phone_fac_d_count 	= 	$result['sms_fac_d_count'];
					$set_ph 			= 	$result['set_sm'];
					$phone_msg 			=	$result['sms_msg'];
					$p_key++;
				}
				if($val['provider_scheduler']['provider_reminder_email']=="on") 
				{					
					$result = self::getArray($m_key,$get_result[$key]['provider_id'],$xp_key,$get_result[$key]['facility_id'],$xf_key,$mail_fac_s_count,$mail_msg,$mail_fac_d_count,$set_em,$val['facility']['facility_name'],$set_msg_em);
					$mail_fac_s_count 	= 	$result['sms_fac_s_count'];
					$set_msg_em 		= 	$result['set_msg_sms'];
					$mail_fac_d_count 	= 	$result['sms_fac_d_count'];
					$set_em 			= 	$result['set_sm'];
					$mail_msg 			=	$result['sms_msg'];
					$m_key++;
				}
				/*** Content makes here start  ***/
				$intimate_date= Helpers::dateFormat($val['schedule_date'],'date');
				$get_slice = explode('/',$_SERVER['REQUEST_URI']);
				
				$patient_id = Helpers::getEncodeAndDecodeOfId($val['provider']['id'],'encode');
				$facility_id = Helpers::getEncodeAndDecodeOfId($val['facility']['id'],'encode');
				$date = Helpers::getEncodeAndDecodeOfId($val['schedule_date'],'encode');
				
				$url ="http://".$_SERVER['HTTP_HOST']."/".$get_slice[1]."/scheduler/list/".$patient_id."/".$facility_id."/".$date;
				
				$header = "Hello ".$val['provider']['provider_name'].","."\n"."This is a reminder that you have ";
				$footer =" on ".$intimate_date."\n Thanks,\n Medcubics.";
				$mail_footer =" on ".$intimate_date."<a href=".$url.' target="_blank">see more... </a>\n Thanks,\n Medcubics.';
				/*** Content makes here end  ***/
				if($sms_msg !='') $get_message['sms'][$val['id']] = $header.$sms_msg.$footer;
				if($phone_msg !='') $get_message['phone'][$val['id']] = $header.$phone_msg.$footer;
				if($mail_msg !='') $get_message['email'][$val['id']] = $header.$mail_msg.$mail_footer;
			}
			$sms_sent 	= (isset($get_message['sms'])) ? self::requestSendsms($get_message['sms'],$val['provider']):'';
			$call_sent 	= (isset($get_message['phone'])) ? self::requestSendphone($get_message['phone'],$val['provider']):'';
			$mail_sent 	= (isset($get_message['email'])) ? self::requestSendemail($get_message['email'],$val['provider']):'';
			return $get_message;
		}
	}
	/*** Get provider alert message function ends ***/
	
	
	/*** Get every records with checking condition starts ***/
	public static function getArray($s_key,$cpro,$prepro,$cfac,$prefac,$sms_fac_s_count,$sms_fac_d_count,$set_sm,$fac_name,$set_msg_sms)
	{
		if(($s_key>0)&&($cpro == $prepro))
		{
			if($cfac == $prefac)
			{
				$sms_fac_s_count++;
				$sms_pro_count_in = $sms_fac_s_count;
				$set_msg_sms = $sms_pro_count_in." appoinment with us ".$fac_name;
				$sms_msg = $set_msg_sms;
			}
			else
			{
				$sms_pro_count_out = $sms_fac_d_count;
				$set_sm = $set_sm." and ".$sms_pro_count_out." appoinment with us ".$fac_name;
				$sms_msg = $set_msg_sms.$set_sm;
			}
		}
		else
		{
			$new_s_count =1;
			$set_msg_sms = $new_s_count." appoinment with us ".$fac_name;
			$sms_msg = $set_msg_sms;
		}
		$result['sms_fac_s_count']=$sms_fac_s_count;
		$result['set_msg_sms']=$set_msg_sms;
		$result['sms_fac_d_count']=$sms_fac_d_count;
		$result['set_sm']=$set_sm;
		$result['set_msg_sms']=$set_msg_sms;
		$result['sms_msg']=$sms_msg;
		return $result;
    }
	/*** Get every records with checking condition ends ***/
	
	/*** Trigger sms send function starts ***/
	public static function requestSendsms($msg,$provider)
    {
		$provider_phone = preg_replace('/\D/', '', $provider['phone']);
		foreach($msg as $key => $val)
		{
			$msg_status ="success";//Apps come here
			$msg_status = CommonApiController::connectSmsApi($val,$provider_phone);
			ProviderSchedulerTime::where('id',$key)->where('provider_id',$provider['id'])->update(["sms_reminder_status"=>"Yes"]);
		}
		$status_msg[] = $msg_status;
		return $status_msg;
    }
	/*** Trigger sms send function ends ***/
	
	/*** Trigger making call function starts ***/
	public static function requestSendphone($msg,$provider)
    {
		$provider_phone = preg_replace('/\D/', '', $provider['phone']);
		foreach($msg as $key => $val)
		{
			$msg_status ="success";//Apps come here
			//$msg_status = CommonApiController::connectPhoneApi($val,$provider_phone);
			//ProviderSchedulerTime::where('id',$get_msg_content[0])->where('provider_id',$provider['id'])->update(["phone_reminder_status"=>"Yes"]);
		}
		$status_msg[] = $msg_status;
		return $status_msg;
    }
	/*** Trigger making call function ends ***/
	
	/*** Trigger send mail function starts ***/
	public static function requestSendemail($msg,$provider)
    {
		$provider['email'] = "gopal.nmg@gmail.com";
		foreach($msg as $key => $val)
		{
			$res = array('email'	=>	$provider['email'],
						'subject'	=>	"Reminder mail",
						'msg'		=>	$val,
						'name'		=>	$provider['provider_name']
						);
			$msg_status = CommonApiController::connectEmailApi($res);	
			ProviderSchedulerTime::where('id',$key)->where('provider_id',$provider['id'])->update(["email_reminder_status"=>"Yes"]);
		}
		$status_msg[] = $msg_status;
		return $status_msg;
    }
	/*** Trigger send mail function ends ***/
	public static function getScheduledCountByProviderId($fetch_id,$type='Facility')
	{
		$fetch_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($fetch_id,'decode');
		if($type == 'Provider')
			return ProviderScheduler::where('provider_id',$fetch_id)->count();
		else
			return ProviderScheduler::where('facility_id',$fetch_id)->count();
	}

	/* Scheduler module practice provider scheduler empty */
	public static function providerSchedulerCount()
	{
		$provider_scheduler_count = ProviderScheduler::count();
		return $provider_scheduler_count;
	}
	// Main scheduler Provider start date is showed on New appointment Apointment time calculation 
	public static function getProviderScheduleDate($facility_id, $provider_id)	{        
        return ProviderScheduler::where('facility_id',$facility_id)->where('provider_id',$provider_id)->pluck('start_date')->first();
    }
}