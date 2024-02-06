<?php
/*
 * Listing the Appointment for scheduler module
 */
?>
@foreach($patient_app as $patient_app)	
<tr>
    <?php
		$patient = @$patient_app->patient;
		$patient_name = App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
		$dob = App\Http\Helpers\Helpers::dateFormat(@$patient->dob, 'dob');
		$provider = @$patient_app->provider;
		$facility = @$patient_app->facility;        
		$time_arr = explode("-", @$patient_app->appointment_time);
		$appt_date = App\Http\Helpers\Helpers::dateFormat($patient_app->scheduled_on, 'date');
		$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_app->patient_id);
		$pro_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$provider->id, 'encode');
        $fac_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$facility->id, 'encode');
        $appt_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_app->id, 'encode');
    ?>
    <td>{{ @$patient->account_no }}</td>
    <td>
        <div class="p-b-0 p-l-0 ">                
            @include ('layouts/patient_hover')                         
        </div>
    </td>
    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->patient->dob,'dob')}}</td>
    <td><a id="someelem{{hash('sha256',$facility->id)}}" class="someelem" data-id="{{hash('sha256',$facility->id)}}" href="javascript:void(0);"> {{ @$facility->short_name }}</a> 
        @include ('layouts/facility_hover')
    </td>
    <td>
		<a id="someelem{{hash('sha256',@$patient_app->provider->id)}}" class="someelem" data-id="{{hash('sha256',@$patient_app->provider->id)}}" href="javascript:void(0);"> {{ @$patient_app->provider->short_name }}</a>
        @include ('layouts/provider_hover')
    </td>
    <td><a class="js_popup_appt form-cursor font13" data-id="{{ $patient_app->id }}" data-url="{{url('scheduler/geteventschedulardate/'.$patient_app->id)}}" >@if(@$patient_app->scheduled_on != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_app->scheduled_on, 'date') }} @endif <i class="fa {{Config::get('cssconfigs.common.edit')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Appointment"></i></a></td>
    <?php //td><a class="js_popup_appt form-cursor font13 font600" data-id="{{ $patient_app->id }}" data-url="{{url('scheduler/geteventschedulardate/'.$patient_app->id)}}" title="Rescheduled Appt">{{ @$appt_date}}</a></td> ?>
    <td><a class="js_popup_appt form-cursor font13" data-id="{{ $patient_app->id }}" data-url="{{url('scheduler/geteventschedulardate/'.$patient_app->id)}}" title="Edit Appointment">{{ @$time_arr[0] }}</a></td>
    <td title="{{ @$patient_app->reasonforvisit->reason}}">{{ @$patient_app->reasonforvisit->reason }} </td>
    <td class="@if(@$patient_app->status == 'Scheduled')  Scheduled @elseif(@$patient_app->status == 'Complete') conformed @elseif(@$patient_app->status == 'Canceled') canceled @elseif(@$patient_app->status == 'Encounter') encounter @else noshow @endif">{{ @$patient_app->status}}</td>
    <td class="td-c-20">
        <span style="padding: 1px;">
            <a target =""  href="#" class="font14 font600 margin-l-3 p-l-5 p-r-5 med-green js-app_resch_appointment @if( ((@$patient_app->status != 'Scheduled') ) ) disabled @endif " data-id="{{$patient_app->id}}" ><i class="fa {{Config::get('cssconfigs.scheduler.scheduled')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Reschedule"></i></a>
            <a target =""  href="#" class="js-app_cancel_appointment font14 font600  p-r-5 med-green @if(@$patient_app->status != 'Scheduled') disabled @endif"  data-id="{{$patient_app->id}}" ><i class="fa {{Config::get('cssconfigs.common.cancel')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Cancel"></i></a>
            <a target =""  href="#" class="js-app_noShow_appointment font14 font600
             p-r-5 med-green @if(@$patient_app->status != 'Scheduled') disabled @endif" data-id="{{$patient_app->id}}" ><i class="fa {{Config::get('cssconfigs.common.noshow')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="No Show"></i></a>
        </span>
		<?php
		   /*  
			<a href="#" class="js-app_delete_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete Appointment" style="border-right:1px solid #e0e0e0; padding: 3px 6px;"><i class="fa fa-trash margin-l-4 med-white"></i> </a>
			<a href="#" class="js-app_resch_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Reschedule Appointment" style="border-right:1px solid #e0e0e0; padding:3px 6px; color:#f2f2f2 ">Rescheduled </a>
			<a href="#" class="" data-id="{{$patient_app->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel Appointment" style="border-right:1px solid #e0e0e0; padding: 3px 6px; " ><i class="fa {{Config::get('cssconfigs.common.cancel')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Cancel"></i> </a>*/ 

			$call_showhide_class = (isset($patient->phone) && !empty($patient->phone)) ? "":"disabled";
			$email_showhide_class = (isset($patient->email) && !empty($patient->email)) ? "":"disabled";
			// $tooltip = (isset($patient->email) && !empty($patient->email)) ? "Email":"Feature not available, Please add Email for Patient to use this feature";
			$phone_class = (isset($patient->phone) && !empty($patient->phone)) ? "js-callmsg-clas cur-pointer" : "" 
		?>
        <span style="padding: 1px;" class="margin-l-3">
            @if(isset($patient->phone) && !empty($patient->phone))
            <a target ="" href="#" class="{{$phone_class}} {{$call_showhide_class}} font14 font600 margin-l-3 p-l-5 p-r-5 med-green" id="js-callmsg-clas" data-user_id="{{$patient_id}}" data-user_type="patient" data-phone= "{{$patient->phone}}">
                <i class="fa {{Config::get('cssconfigs.common.phone')}} " data-placement="bottom" data-toggle="tooltip" data-original-title="Call/SMS"></i>
            </a>
            @else
            <span class="font14 font600 margin-l-3 p-l-5 p-r-5 light-green">
                <i class="fa {{Config::get('cssconfigs.common.phone')}} " data-placement="bottom" data-toggle="tooltip" data-original-title="Feature not available, Please add Phone number for Patient to use this feature"></i>
            </span>
            @endif
            @if(isset($patient->email) && !empty($patient->email))
            <a target ="" href="#" class="{{$email_showhide_class}} font14 font600 p-r-5 med-green"><i class="fa {{Config::get('cssconfigs.common.message')}}  js-emailmsg-clas" data-user_id="{{$patient_id}}" data-placement="bottom" data-email="{{$patient->email}}" data-toggle="tooltip" data-original-title="Email" ></i></a>
            @else
            <span class="font14 font600 p-r-5 light_green"><i class="fa {{Config::get('cssconfigs.common.message')}} js-emailmsg-clas" data-toggle="tooltip" data-original-title="Feature not available, Please add Email for Patient to use this feature" ></i></span>
            @endif
           <!-- <a target ="" href="#" class="font14 font600 p-r-5 med-green"><i class="fa {{Config::get('cssconfigs.common.email')}}   js-emailmsg-clas" data-user_id="{{$patient_id}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Email"></i></a>
            <a target ="" href="#" class="font14 font600 p-r-5 med-green"><i class="fa {{Config::get('cssconfigs.common.fax')}} disabled "  data-placement="bottom" data-toggle="tooltip" data-original-title="Fax"></i></a>-->
        </span>

        <span style="background: #f1fdfc; padding: 1px;" class="margin-l-3">
            <?php /*<a target =""  href="#" class="font14 font600 margin-l-3  p-l-5 p-r-5" style="color:#2EB143"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Create Claim"></i></a>*/?>
            <a target ="_blank"  href="{{ url('patients/'.$patient_id.'/billing/create?rendering_provider_id='.$pro_id.'&facility_id='.$fac_id.'&appointment_id='.$appt_id  )}}" class=" font14 font600 margin-l-3 p-r-5" style="color:#2EB143 "><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Create Claim"></i></a>                                                                        
        </span>
    </td>
</tr>
@endforeach    