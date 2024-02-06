<style>
    .ui-autocomplete {
        position: fixed;
        top: 100%;
        left: 0;
        z-index: 1051 !important;
        float: left;
        display: none;
        min-width: 160px;
        width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
<div class="modal-md-scheduler" id="js_open_scheduler_pop_up">
    <div class="modal-content clsViewAppointment">      
        
        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.scheduler.appointment_popup") }}' />
        <div class="modal-body no-padding yes-border med-border-color">
            {!! Form::open(['name'=>'myform','id'=>'js-bootstrap-validator_show']) !!}
            {!! Form::hidden('event_id',$appointment_details->id,['id'=>'event_id']) !!}
            <small class="help-block hide" id="js-error-msg"></small>  
            <div class='box-body no-padding'> 
                <i class="fa fa-times-circle font14 cur-pointer pull-right med-green bg-white js_recentform" style="margin-top:-6px; margin-right: -6px;" data-dismiss='modal'></i>                    
                <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 no-padding" style="background:#e5faf0;">
                    <div style="text-align:center">
                        <style>
                            .css_image{ border-radius:50%; border:2px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);-webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);margin-top:10px;margin-bottom:10px;box-shadow: 0 0 5px rgba(0,0,0,0.5); }
                        </style>
                        <?php 
							$filename = $appointment_details->patient->avatar_name.'.'.$appointment_details->patient->avatar_ext;
							$img_details = [];
							$img_details['module_name']='patient';
							$img_details['file_name']=$filename;
							$img_details['practice_name']="";
							$img_details['style']='width:60px;height:60px;';
							$img_details['alt']='appointment-image';
							$img_details['class']='css_image';
							$image_tag = App\Http\Helpers\Helpers::checkandgetavatar($img_details);
						?>	
                        {!! $image_tag !!}
                    </div>
                    <div style="text-align:center; padding-bottom: 8px;">
                        <p class="no-margin">
                            <span class="med-green font600"> </span><span class="font600 med-green"> 
                                @if($appointment_details->patient->title){{ @$appointment_details->patient->title }}. @endif {{ @$appointment_details->patient->last_name.', '.@$appointment_details->patient->first_name.' '.@$appointment_details->patient->middle_name }}
                            </span>
                        </p>
                        <p>
                            <span class="med-orange sm-size margin-l-5">
								@if(@$appointment_details->patient->dob != "0000-00-00"  && @$appointment_details->patient->dob != "" && @$appointment_details->patient->dob != "1901-01-01")
									{{App\Http\Helpers\Helpers::dateFormat(@$appointment_details->patient->dob,'dob')}}, {{ App\Http\Helpers\Helpers::dob_age(@$appointment_details->patient->dob) }}, 
								@endif
								{{ @$appointment_details->patient->gender }}
                            </span>
                        </p>
                        <p>
                            <span>{{@$appointment_details->patient->address1}}<br> {{@$appointment_details->patient->city}}, {{$appointment_details->patient->state}}, {{ @$appointment_details->patient->zip5}} @if(@$appointment_details->patient->zip4 != '') - {{ @$appointment_details->patient->zip4}} @endif</span>
                        </p> 

                    </div>    
                    <div style="margin-bottom:7px">
                        <?php
							$appt_split_time = explode('-', $appointment_details->appointment_time);
                        ?>
                        <span class="margin-l-5 med-gray-dark">{{$appt_split_time[0]}}</span> <span class=" {{@$appointment_details->status}} pull-right margin-r-5 font600">@if(@$appointment_details->status == 'Canceled') Canceled @else {{@$appointment_details->status}} @endif</span> 
                    </div>
                </div>

                <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding">                                          
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Acc No</div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"><span class="">{{@$appointment_details->patient->account_no}}</span></div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Facility</div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">{{@$appointment_details->facility->facility_name}}</div>                                                 
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600 "> Provider</div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">{{ucwords(strtolower(@$appointment_details->provider->provider_name))}} {{@$appointment_details->provider->degrees->degree_name}}</div> 
                    </div>
                    <?php
						$insurance_details = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName($appointment_details->patient_id, 'yes');
                    ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Primary</div>             
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> {{@$insurance_details['name']}}</div> 
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Policy ID</div>             
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> {{@$insurance_details['policy_id']}} 
                            @if(@$insurance_details['policy_id'])
								
							<?php 
								$plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$insurance_details['patient_id'], @$insurance_details['insurance_id'], @$insurance_details['policy_id']); 
								if($plan_end_date == '0000-00-00' || $plan_end_date == '') {
									$getReachEndday = 0;
								} else {
									$now = strtotime(date('Y-m-d')); // or your date as well
									$your_date = strtotime($plan_end_date);
									$datediff = $now - $your_date;
									$getReachEndday =  floor($datediff / (60 * 60 * 24));	
								}	
							?>

                            <span class="js_insgray{{ @$insurance_details['id'] }}" @if((@$insurance_details['eligibility_verification']=='' || @$insurance_details['eligibility_verification'] == 'Active' || @$insurance_details['eligibility_verification'] == 'Inactive') && (@$insurance_details['eligibility_verification'] != 'None' || @$insurance_details['eligibility_verification'] != 'Error'))  style="display:none;" @endif >	
                                  <a title="Check Eligibility" data-unid="{{ @$insurance_details['id'] }}"  data-patientid="{{ @$insurance_details['patient_id'] }}" data-category="{{ @$insurance_details['category'] }}" class="js-patient-eligibility_check" href="javascript:void(0);"><i class="fa fa-user text-gray font10"></i></a> 
                            </span>

                            <span class="js_insgreen{{ @$insurance_details['id'] }}" @if((@$insurance_details['eligibility_verification']=='' || @$insurance_details['eligibility_verification'] == 'Inactive' || @$insurance_details['eligibility_verification'] == 'None' || @$insurance_details['eligibility_verification'] == 'Error') && (@$insurance_details['eligibility_verification'] != 'Active') || $getReachEndday > 0) style="display:none;" @endif >	
                                  <a title="Eligibility Details" class="js_get_eligiblity_details" data-unid="{{ @$insurance_details['id'] }}" data-patientid="{{ @$insurance_details['patient_id'] }}" data-category="{{ @$insurance_details['category'] }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a> 
                            </span>

                            <span class="js_insred{{ @$insurance_details['id'] }}" @if((@$insurance_details['eligibility_verification']=='' || @$insurance_details['eligibility_verification'] == 'Active' || @$insurance_details['eligibility_verification'] == 'None' || @$insurance_details['eligibility_verification'] == 'Error' || $getReachEndday < 0) && (@$insurance_details['eligibility_verification'] != 'Inactive') && $getReachEndday <= 0   ) style="display:none;" @endif >	
                                  <a title="Eligibility Details" class="js_get_eligiblity_details" data-unid="{{ @$insurance_details['id'] }}" data-patientid="{{ @$insurance_details['patient_id'] }}" data-category="{{ @$insurance_details['category'] }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a> 
                            </span>
							
							<i class="fa fa-spinner fa-spin font11 patientloadingimg{{ @$insurance_details['patient_id'] }}" style="display:none;"></i>	

                            @endif
                        </div> 
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Appt On </div>             
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> <span class="bg-date">{{App\Http\Helpers\Helpers::dateFormat(@$appointment_details->scheduled_on,'date')}}</span></div>
                    </div>                          
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Visit Reason </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> {{  @ucwords($reason_visit[$appointment_details->reason_for_visit]) }} </div>
                    </div> 
                                      
                </div>
                @if(@$appointment_details->status!='Complete' && @$appointment_details->status!='Canceled')
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="background:#e5faf0">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding scheduler-dropdown" style=" background: #00877f; border-top:2px solid #00877f; border-bottom:1px solid #00877f;">
                        <div class="btn-group">
                            <a href="#" class="js-app_resch_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Reschedule Appointment" style="border-right:1px solid #e0e0e0; padding:3px 6px; color:#f2f2f2 ">Rescheduled </a>                                             
                            <?php 
								$patient_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$appointment_details->patient_id,'encode');
								$fac_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$appointment_details->facility->id,'encode');
								$pro_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$appointment_details->provider->id,'encode'); 
							?>
                            <a href="{{url('patients/'.$patient_encode_id.'/ledger')}}" target="_blank" style="border-right:1px solid #e0e0e0; padding: 3px 6px; color:#f2f2f2 " ><span data-placement="bottom" data-toggle="tooltip" data-original-title="View Ledger">Ledger</span> </a>
						</div>
                        <div class="btn-group pull-right">
                            <a href="#" class="js-app_edit_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit Appointment" style="border-right:1px solid #e0e0e0; border-left: 1px solid #e0e0e0; padding: 3px 6px"><i class="fa fa-edit margin-l-4 med-white"></i> </a>                                             
                            <a href="#" class="js-app_cancel_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel Appointment" style="border-right:1px solid #e0e0e0; padding: 3px 6px; " ><i class="fa fa-ban margin-l-4 med-white"></i> </a>
                            <a href="#" class="js-app_delete_appointment" data-id="{{$appointment_details->id}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete Appointment" style="border-right:1px solid #e0e0e0; padding: 3px 6px;"><i class="fa fa-trash margin-l-4 med-white"></i> </a>

                            <a href="#" data-toggle="dropdown" class="dropdown-toggle med-white" style="border-right:1px solid #f0f0f0; padding: 3px 6px;">More <i class="fa fa-caret-down med-white"></i></a>                                                                                 
                            <ul class="dropdown-menu pull-right" style="line-height:10px;" style="margin-top:2px;">
                                <!--<li><a href="#" data-toggle="modal" data-target="#form-address-modal"><i class="fa fa-check" ></i> Eligibility Check</a></li>-->
                                <li><a href="{{url('patients/'.$patient_encode_id.'/edit/insurance')}}" target="_blank"><i class="fa fa-bank"></i>Insurance</a></li>
                                <!--<li><a href="{{url('scheduler/list/'.$pro_id.'/'.$fac_id.'/-/'.$patient_encode_id)}}" target="_blank"><i class="fa fa-calendar"></i>Appt List</a></li>-->
								<li><a href="{{url('patients/'.$patient_encode_id.'/appointments')}}" target="_blank"><i class="fa fa-calendar"></i>Appt List</a>
                                <li><a href="{{url('patients/'.$patient_encode_id.'/edit/authorization')}}" target="_blank"><i class="fa fa-shield"></i>Authorization</a></li>
                                <li><a href="{{url('patients/'.$patient_encode_id.'/problemlist')}}" target="_blank"><i class="fa fa-navicon"></i>Workbench</a></li>
                                <li><a href="{{url('patients/'.$patient_encode_id.'/notes')}}" target="_blank"><i class="fa fa-sticky-note"></i>Notes</a></li>
                                <li><a href="{{url('patients/'.$patient_encode_id.'/documents')}}" target="_blank"><i class="fa fa-folder-open"></i>Documents</a></li>
                                <!--li><a href="{{url('patients/'.$patient_encode_id.'/reports')}}"" target="_blank"><i class="fa fa-file-word-o"></i>Reports</a></li-->
                            </ul>
                        </div> 
                    </div>     
                </div>                             
                @endif
                @if($appointment_details->status=='Canceled')
                 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-canceled padding-6-0">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><span class="canceled font600">Canceled Reason : </span> {{ ($appointment_details->cancel_delete_reason) }} </div>
				</div>   
				@endif   
            {!! Form::close() !!}
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->