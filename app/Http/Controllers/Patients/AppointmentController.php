<?php namespace App\Http\Controllers\Patients;
use Auth;
use View;
use Input;
use Session;
use Request;
use Redirect;
use Validator;
use App\Http\Controllers\Patients\Api\AppointmentApiController as AppointmentApiController;

use App\Models\Patients\Patient as Patient;
use App\Http\Helpers\Helpers as Helpers;
use PDF;
use Excel;
use App\Exports\BladeExport;

class AppointmentController extends Api\AppointmentApiController 
{
	public function __construct()
	{
		View::share ( 'heading', 'Patient' );
		View::share ( 'selected_tab', 'billing' );
		View::share( 'heading_icon', 'fa-user');
	}
	/**
	 * Display a listing of the resource. 
	 *
	 * @return Response
	 */
    public function index($id) {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'failure')
            return Redirect::to('patients')->with('message', $api_response_data->message);
        $patients = $api_response_data->data->patients;
        $patient_appointment = $api_response_data->data->patient_appointment;
        $rendering_provider = @$api_response_data->data->rendering_provider;
        $reason = @$api_response_data->data->reason;
        $facility = @$api_response_data->data->facility;

        return view('patients/appointments/appointments', compact('patients', 'patient_appointment', 'rendering_provider', 'reason', 'facility'));
    }

    public function getAppointmentExport($id='', $export=''){
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        $patients = $api_response_data->data->patients;
        $patient_appointment = $api_response_data->data->patient_appointment;	
        $rendering_provider = @$api_response_data->data->rendering_provider;	
        $reason = @$api_response_data->data->reason;	
        $facility = @$api_response_data->data->facility;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Appointments_List_' . $date;

        if ($export == 'pdf') {
            $html = view('patients/appointments/appointments_export_pdf', compact('patients', 'patient_appointment', 'rendering_provider', 'reason', 'facility', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'patients/appointments/appointments_export';
            $data['patients'] = $patients;
            $data['patient_appointment'] = $patient_appointment;
            $data['rendering_provider'] = $rendering_provider;
            $data['reason'] = $reason;
            $data['facility'] = $facility;
            $data['export'] = $export;
            $data['file_path'] = $filePath;

            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } 
    }
        
	public function searchIndexlist($id)
	{
		$api_response = $this->getIndexApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{//dd('sds');
			$patients 			 = 	$api_response_data->data->patients;	
			$patient_appointment = 	$api_response_data->data->patient_appointment;	
			$rendering_provider 	 = 	$api_response_data->data->rendering_provider;	
			$reason 	 = 	$api_response_data->data->reason;	
			$facility 	 = 	$api_response_data->data->facility;	
			return view ( 'patients/appointments/appointments_list', compact ('patients','patient_appointment','rendering_provider','reason','facility') );
		}
		else
		{
			print_r($api_response_data->message);
			exit;
		}
	}
	public function appointmentMailSend($userId)
	{
		if($userId != ''){
			$userId = Helpers::getEncodeAndDecodeOfId($userId, 'decode');
			$patient = patient::where('id', $userId)->first();
			
				$patientName = Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);

				$message = trans("email/email.security_email");			
				$oldPhrase = ["VAR_PATIENT_USER", "VAR_SITE_NAME"];
				$newPhrase   = [$patientName, "Medcubics"];
				$newMessage = str_replace($oldPhrase, $newPhrase, $message);
				$view = view('emails/appointments',compact('newMessage'));				
				$Subject = 'Appointment Mail';
			if($patient->email != ''){					
				$deta = array('name'=> $patientName,'email'=> $patient->email,'subject'=> $Subject,'msg' => $view,'attachment'=>'');							
				Helpers::sendMail($deta);
				return 'success';
			}else{
				return 'Error';		
			}
		}
	}
}