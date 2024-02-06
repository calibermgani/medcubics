<?php namespace App\Http\Controllers\Scheduler;

use Auth;
use View;
use Redirect;
use Request;
use Session;
use App\Http\Controllers\Scheduler\Api\SchedulerApiController as SchedulerApiController;
use App\Models\Provider;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Patients\Patient;
class SchedulerController extends SchedulerApiController {
    public function __construct()
    {
        View::share ( 'heading', 'Scheduler' );
        View::share ( 'selected_tab', 'scheduler' );
        View::share( 'heading_icon', 'fa-calendar-o');
    }
    public function index()
    {          
        $request = Request::all();     
		// Added session for create appopintment from patient
        if(isset($request['id']) ){  
            session::put('appt_pat', $request['id']);
            return Redirect::to('/scheduler/scheduler');
        }       
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $default_view_list_arr = $api_response_data->data->default_view_list;
        $resource_listing = $api_response_data->data->resource_listing;
        $default_view_list_id = $api_response_data->data->default_view_list_id;
        $index_stats_count = $api_response_data->data->index_stats_count;
        return view ( 'scheduler/scheduler/index', compact ('default_view_list_arr','resource_listing','default_view_list_id','index_stats_count') );
    }
	
    public function setDefaultAndResourceList($defaultView)
    {
        $api_response = $this->setDefaultAndResourceListApi($defaultView);
        $api_response_data = $api_response->getData();
        $default_view_list_arr = $api_response_data->data->default_view_list;
        $resource_listing = $api_response_data->data->resource_listing;
        $default_view_list_id = $api_response_data->data->default_view_list_id;
        $index_stats_count = $api_response_data->data->index_stats_count;
        return view ( 'scheduler/scheduler/default_view_form', compact ('default_view_list_arr','resource_listing','default_view_list_id','index_stats_count') );
    }

    public function getCalendarResourcesListing($type,$default_view,$default_view_list_id,$resource_ids='')
    {       
        $api_response = $this->getCalendarResourcesApi($type,$default_view,$default_view_list_id,$resource_ids);
        $api_response_data = $api_response->getData();
        $resource_listing = $api_response_data->data->resource_listing;
        $index_stats_count = $api_response_data->data->index_stats_count;
        return view ('scheduler/scheduler/scheduler_resource_stats', compact('resource_listing','index_stats_count','default_view'));
    }	 

    public function getAppointment($id=''){
        $api_response = $this->getAppointmentApi();
        $api_response_data = $api_response->getData();
        $default_view = $api_response_data->data->default_view;
        $default_view_list_caption = $api_response_data->data->default_view_list_caption;
        $resource_caption = $api_response_data->data->resource_caption;
        $default_view_list = $api_response_data->data->default_view_list;
        $default_view_list_id = $api_response_data->data->default_view_list_id;
        $resource_id = $api_response_data->data->resource_id;
        $resources = $api_response_data->data->resources;
        $sch_app_time = $api_response_data->data->sch_app_time;		
        $provider_available_dates = $api_response_data->data->provider_available_dates; 
        $user_selected_date = $api_response_data->data->user_selected_date;
        $insurances = $api_response_data->data->insurances;
        $array_of_time = $api_response_data->data->array_of_time;
        $payment_check_no = @$api_response_data->data->payment_check_no;
        $user_already_selected_timeslot = $api_response_data->data->user_already_selected_timeslot;
        $address_flags = (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
		$reason_visit = $api_response_data->data->reason_visit;
		$user_selected_slot_time = $api_response_data->data->user_selected_slot_time;
        $request = Request::all();
		// If appointment create for a patient handled
        $appt_pat =  session::get('appt_pat',0);
        session::forget('appt_pat');
        $patient = ($appt_pat != "0" ) ? (Patient::getPatientSearchname($appt_pat)."##".$appt_pat): "";
        $patient_id = ($appt_pat != "0" ) ? $appt_pat : "";
        
		return view ('scheduler/scheduler/appointment', compact('reason_visit','default_view_list', 'resources', 'provider_available_dates','user_selected_date','insurances','user_already_selected_timeslot','sch_app_time','payment_check_no','array_of_time','address_flag','default_view','default_view_list_id','resource_id','default_view_list_caption','resource_caption','user_selected_slot_time','patient','patient_id'));
    }
	
	public function getNewPatient(){
		$api_response = $this->getNewPatientApi();
		$api_response_data = $api_response->getData();
        $insurances = $api_response_data->data->insurances;
		$address_flags = (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
		return view ('scheduler/scheduler/newpatientform', compact('insurances','address_flag'));
	}
	
    public function getAvailableSlotTimeByDate()
    {
        $api_response = $this->getAvailableSlotTimeByDateApi();
        $api_response_data = $api_response->getData();
        $array_of_time = $api_response_data->data->array_of_time;
        $user_selected_time = $api_response_data->data->user_selected_time;
        $user_already_selected_timeslot = $api_response_data->data->user_already_selected_timeslot;   
		$user_selected_slot_time = "00000";
        return view ('scheduler/scheduler/available_time_slot', compact('array_of_time', 'user_already_selected_timeslot', 'user_selected_time','user_selected_slot_time'));
    }

    public function getAppointmentDetails(){
        $api_response = $this->getAppointmentDetailsApi();
        $api_response_data = $api_response->getData();        
        $appointment_details = $api_response_data->data->appointment_details;  
		$reason_visit = json_decode(json_encode($api_response_data->data->reason_visit), True);  
		
        return view ('scheduler/scheduler/updateappointment', compact('reason_visit','appointment_details'));
    }

    public function getAppointmentDetailsReschedule(){
        $api_response = $this->getAppointmentByEventApi();
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $facility_id = $api_response_data->data->facility_id;
        $provider_id = $api_response_data->data->provider_id;
        $providers = $api_response_data->data->providers;
        $provider_available_dates = $api_response_data->data->provider_available_dates; 
        $user_selected_date = $api_response_data->data->user_selected_date;
        $insurances = $api_response_data->data->insurances;
        $appointment_details = $api_response_data->data->appointment_details;
        $visit_status = $api_response_data->data->visit_status;
        $array_of_time = $api_response_data->data->array_of_time;
        $user_already_selected_timeslot = $api_response_data->data->user_already_selected_timeslot;
        $address_flags = (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
		
		$default_view_list = $api_response_data->data->default_view_list;
		$resources = $api_response_data->data->resources;
		$default_view = $api_response_data->data->default_view;
		$default_view_list_id = $api_response_data->data->default_view_list_id;
		$resource_id = $api_response_data->data->resource_id;
		$default_view_list_caption = $api_response_data->data->default_view_list_caption;
		$resource_caption = $api_response_data->data->resource_caption;
		$reason_visit = $api_response_data->data->reason_visit;
		$user_selected_slot_time = "";
		
        return view ('scheduler/scheduler/updateappointment_scheduler', compact('reason_visit','facilities', 'facility_id', 'providers', 'provider_id','provider_available_dates','user_selected_date','insurances','appointment_details','visit_status','array_of_time','user_already_selected_timeslot','address_flag', 'default_view_list', 'resources', 'default_view','default_view_list_id','resource_id','default_view_list_caption','resource_caption','user_selected_slot_time'));
		
		//return view ('scheduler/scheduler/appointment', compact('default_view_list', 'resources', 'default_view','default_view_list_id','resource_id','default_view_list_caption','resource_caption'));
    }
	
	/*** Start to New Select the Reason for Visit ***/
	public function addnewselect()
	{
		$tablename = Request::input('tablename');
		$fieldname = Request::input('fieldname');
		$addedvalue = Request::input('addedvalue');		
		return $this->addnewApi($addedvalue);			
	}
	/*** End to New Select the Reason for Visit ***/
	
	public function getappointmentStatsdynamic_count($scheduler_calendar_val,$default_view_option_val,$default_view_list_option_val,$resource_option_val,$view_option)
    {       
        $api_response = $this->getappointmentStatsdynamic_countApi($scheduler_calendar_val,$default_view_option_val,$default_view_list_option_val,$resource_option_val,$view_option);
        $api_response_data = $api_response->getData();
        $index_stats_count = $api_response_data->data->index_stats_count;
        return view ('scheduler/scheduler/appointment_stats', compact('index_stats_count'));
    }
	
	
	public function mail_test(){
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 3;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'medcubics@gmail.com';                 // SMTP username
			$mail->Password = 'medcubics2017';                           // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('medcubics@gmail.com', 'Mailer');
			$mail->addAddress('selvakumar.velraj@clouddesigners.com', 'selva');     // Add a recipient
			$mail->addReplyTo('no-reply@annexmed.com', 'No-reply');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Here is the subject';
			$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		}
	}
	
}