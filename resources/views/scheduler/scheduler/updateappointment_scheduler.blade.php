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

<div class="modal-md">
    <div class="modal-content modal-border">

        <div class="modal-body no-padding yes-border med-border-color">
			<div class="modal-header no-border-radius margin-b-10">
				<button type="button" class="close close_popup js_recentform" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Update Appointment</h4>
			</div>
            <!-- <i class="fa fa-times-circle font14 cur-pointer pull-right med-green bg-white" style="margin-top:-6px; margin-right:-6px;" data-dismiss='modal'></i>  -->
            {!! Form::open(['name'=>'myform','id'=>'js-bootstrap-validator','class'=>'popupmedcubicsform']) !!}
            {!! Form::hidden('event_id',$appointment_details->id,['id'=>'event_id']) !!}
            {!! Form::hidden('set_check_in_time',$appointment_details->checkin_time,['id'=>'set_check_in_time']) !!} 
            {!! Form::hidden('set_check_out_time',$appointment_details->checkout_time,['id'=>'set_check_out_time']) !!}
            {!! Form::hidden('set_reasonforvisit',$appointment_details->reason_for_visit,['id'=>'set_reasonforvisit']) !!}
            {!! Form::hidden('set_visitstatus',$appointment_details->status,['id'=>'set_visitstatus']) !!}
            <small class="help-block hide" id="js-error-msg"></small>

            <?php $patient_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($appointment_details->patient_id,'encode'); ?>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="box box-info no-padding no-shadow no-border margin-b-5">
                    <div class="box-body form-horizontal no-padding">
                       
                        <div class="form-group" style="margin-left:-5px;">                             
                            {!! Form::label('Visit Status', 'Visit Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                                @if($visit_status == 'NoShow') 
                                    {!! Form::select('visit_status',['No Show'=>'No Show'],null,['class'=>'select2 form-control input-sm-modal js_scheduled_status']) !!} 
							 	@elseif($visit_status != 'Reschedule') 
									<?php 
										(@$visit_status->Canceled ='Canceled');									
										if($visit_status == 'undefined') {
											$visit_status = ['Canceled' => 'Canceled'];
										} 
									?>
                                    {!! Form::select('visit_status',@$visit_status,null,['class'=>'select2 form-control input-sm-modal js-visit_status']) !!}
								@else
									{!! Form::select('visit_status',['Rescheduled'=>'Rescheduled'],null,['class'=>'select2 form-control input-sm-modal js_scheduled_status']) !!} 
								@endif		
                                    
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div> 

                        <div class="js-reason hide">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10 no-padding ">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0 no-padding margin-b-10 margin-t-m-10">
                                    <span class="med-orange reasontitle font600 padding-0-4">Reason for Cancel</span>
                                </div>
                                <div class="form-group padding-4">                             
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                                        {!! Form::textarea('reason',null,['class'=>'form-control', 'rows' => 2, 'cols' => 40]) !!}
                                    </div>                        
                                </div>

                            </div>	
                        </div>	

                        <div id="js-nonedit-appointment" class="no-padding">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0 margin-b-10">
                                    <span class="med-orange font600" style="margin-left:-5px;">General Details</span>
                                </div>
                                <div class="margin-l-10"> 
									<div class="form-group">                             
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green"> Facility</div>                        
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">{{ @$appointment_details->facility->facility_name}}</div>
									</div>
									<div class="form-group "> 
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green"> Provider</div>                 
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">{{ @$appointment_details->provider->provider_name}} {{ @$appointment_details->provider->degrees->degree_name}}</div> 
									</div>
									<div class="form-group">                             
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green p-r-0"> Scheduled On</div>                                         
										<div class="col-lg-5 col-md-5 col-sm-6 col-xs-10"><span class="bg-date">{{App\Http\Helpers\Helpers::dateFormat($user_selected_date,'date')}}</span></div>                        
									</div>   
									@if(@$appointment_details->appointment_time != '')
									<div class="form-group">                                                    
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green"> Appt Time</div>                                               
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">{{@$appointment_details->appointment_time}}</div>
									</div>   
									@endif
								</div>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0 no-padding margin-b-10">
                                    <span class="med-orange padding-0-4 font600">Patient Details</span>
                                </div>
                                <div class="margin-l-10">
                                <div class="form-group">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 med-green font600"> Patient</div>                     
                                    <div class="col-lg-9 col-md-9 col-sm-9">
                                        <span class="med-orange ">{{ @$appointment_details->patient->last_name.', '.@$appointment_details->patient->first_name.' '.@$appointment_details->patient->middle_name }}   </span> <a href="{{url('patients/'.$patient_encode_id.'/edit')}}" target="_blank"><i class="fa fa-edit form-cursor margin-l-10" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i></a>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">@if(@$appointment_details->patient->dob != "0000-00-00"  && @$appointment_details->patient->dob != "" && @$appointment_details->patient->dob != "1901-01-01")<span class="med-green font600"> DOB :</span> <span class="sm-size"> {{ App\Http\Helpers\Helpers::dateFormat(@$appointment_details->patient->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$appointment_details->patient->dob) }}, @else @endif @if($appointment_details->patient->gender == 'Male') Male @elseif($appointment_details->patient->gender == 'Female') @else Others @endif </span> </div>                             
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding"> <span class="med-green font600"> Acc No :</span> {{@$appointment_details->patient->account_no}}</div>   
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  no-padding"><span class="med-green font600"> SSN :</span> @if(@$appointment_details->patient->ssn != ''){{@$appointment_details->patient->ssn}} @else -- Nil -- @endif</div>                                         
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">{{@$appointment_details->patient->address1}} @if(@$appointment_details->patient->address2 != ''), {{@$appointment_details->patient->address2}}@endif</div>                             
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            @if(@$appointment_details->patient->city != ''){{@$appointment_details->patient->city}}, @endif @if(@$appointment_details->patient->state != '') {{@$appointment_details->patient->state}},@endif {{@$appointment_details->patient->zip5}} @if(@$appointment_details->patient->zip4 != '0___')-{{@$appointment_details->patient->zip4}}
											@endif
										</div>                            
                                    </div>

                                </div>       
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 no-padding">
                                        <div class="form-group">                             
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12  font600 med-green"> Home Phone </div>                                                
                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 no-padding">&nbsp; @if(@$appointment_details->patient->phone != '') {{@$appointment_details->patient->phone}} @else -- Nil -- @endif</div>
                                        </div>                                   
                                    </div>

                                    @if(@$appointment_details->patient->mobile != '')
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding">
                                        <div class="form-group">     
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <span class="font600 med-green">Cell Phone :</span> {{@$appointment_details->patient->mobile}}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if(!empty(@$appointment_details->patient->insured_detail))   
                                @foreach(@$appointment_details->patient->insured_detail as $patient_insurance)
                                @if(@$patient_insurance->category == 'Primary')
                                <div class="form-group">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green"> Primary Ins</div>
                                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12"> {{@$patient_insurance->insurance_details->insurance_name}}</div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 font600 med-green"> Policy ID</div>                           
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
										{{@$patient_insurance->policy_id}}                                       
										<?php 
											$plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$appointment_details->patient->id,@$patient_insurance->insurance_id,@$patient_insurance->policy_id); 
											if($plan_end_date == '0000-00-00' || $plan_end_date == '') {
												$getReachEndday = 0;
											} else {
												$now = strtotime(date('Y-m-d')); // or your date as well
												$your_date = strtotime($plan_end_date);
												$datediff = $now - $your_date;
												$getReachEndday =  floor($datediff / (60 * 60 * 24));	
											}	
										?>	
                                        <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray" @if(@$appointment_details->patient->eligibility_verification == 'Active' ||@$appointment_details->patient->eligibility_verification == 'Inactive') style="display:none;" @endif data-patientid="{{ $appointment_details->patient->id }}" data-category="Primary" title="Check Eligibility"><i class="fa fa-user text-gray font10"></i></a> 
                                        <i class="fa fa-spinner fa-spin eligibilityloadingimg font11" style="display:none;"></i>
                                        <a class="js_get_eligiblity_details js_eliactive" title="Eligibility Details" @if(@$appointment_details->patient->eligibility_verification == 'None' || @$appointment_details->patient->eligibility_verification == 'Inactive' || @$appointment_details->patient->eligibility_verification == 'Error' || $getReachEndday > 0) style="display:none;" @endif data-patientid="{{ $appointment_details->patient->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a>
                                        <a class="js_get_eligiblity_details js_eliinactive" title="Eligibility Details" @if((@$appointment_details->patient->eligibility_verification == 'None' || @$appointment_details->patient->eligibility_verification == 'Active' || @$appointment_details->patient->eligibility_verification == 'Error') && $getReachEndday <= 0) style="display:none;" @endif data-patientid="{{ $appointment_details->patient->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a>
                                    </div>
                                </div>
                                @endif                              
                                @endforeach 
                                @endif                              
                                </div>
                            </div>             
                        </div>
                        <div id="js-edit-appointment" class="hide col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0 margin-b-10">
                                    <span class="med-orange font600" style="margin-left:-5px;">General Details</span>
                                </div>
                            </div>
                            {!! Form::hidden('default_view',$default_view,['id'=>'default_view']) !!}
                            {!! Form::hidden('default_view_list_caption',$default_view_list_caption,['id'=>'default_view_list_caption']) !!}
                            {!! Form::hidden('resource_caption',$resource_caption,['id'=>'resource_caption']) !!}
                            <div class="margin-l-10">
                            <div class="form-group">                             
                                {!! Form::label($default_view_list_caption, $default_view_list_caption, ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-7 select2-white-popup">
                                    {!! Form::select('default_view_list_id',[''=>'-- Select --']+(array)$default_view_list,$default_view_list_id,['class'=>'select2 form-control input-sm-modal-billing','id'=>'js-ptsh_default_view_list']) !!}
                                    <small class="help-block hide" id="js-error-default_view_list_id"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label($resource_caption, $resource_caption, ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7 select2-white-popup">
                                    {!! Form::select('resource_id',[''=>'-- Select --']+(array)$resources,$resource_id,['class'=>'select2 form-control input-sm-header-billing','id'=>'js-ptsh_resource']) !!}
                                    <small class="help-block hide" id="js-error-resource_id"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Schedule On', 'Schedule On', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}    
                                <?php
									$user_selected_time_convert = '';
									if ($user_selected_date != '')
										$user_selected_time_convert = date("m/d/Y", strtotime($user_selected_date));
                                ?>                       
                                <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 select2-white-popup">
                                    <span id="scheduled_on_icon"><i class="fa fa-calendar-o form-icon-billing"></i></span>
                                    {!! Form::text('scheduled_on',$user_selected_time_convert,['id'=>'scheduled_on','readonly','class'=>'form-control input-sm-header-billing form-cursor dm-date']) !!}   
                                    <small class="help-block hide" id="js-error-scheduled_on"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Appointment Time', 'Appt Time', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 select2-white-popup">
                                    <div id="js-available_slot_timings">
                                        {!! Form::select('appointment_time',[''=>'-- Select --']+(array)@$array_of_time[0],@$appointment_details->appointment_time,['class'=>'select2 form-control','id'=>'appointment_time']) !!}
                                    </div>
                                    <small class="help-block hide" id="js-error-appointment_time"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
<?php /*
                            <div class="form-group">                            
                                {!! Form::label('Search Category', 'Search Patient', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing font600 med-green']) !!}                           
                                <div class="col-lg-3 col-md-3 col-sm-7 col-xs-10 select2-white-popup">
                                    {!! Form::select('patient_search_category',['all'=>'-- Select --','first_name'=>'First Name','last_name'=>'Last Name','acc_no'=>'Acc No','dob'=>'DOB','ssn'=>'SSN','address'=>'Address'],'all',['class'=>'select2 form-control','id'=>'js-patient_search_category']) !!}
                                </div>                        
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                {!! Form::text('patient_search',@$appointment_details->patient->last_name.' '.@$appointment_details->patient->first_name,['id'=>'patient_search','class'=>'form-control input-sm-header-billing']) !!}
								
                                </div>
                                </div>
								
							<div class="form-group">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"></div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<span style="display:none">
                                {!! Form::checkbox('is_new_patient','yes',null,['class'=>'flat-red','id'=>'is_new_patient']) !!} </span>
								<label for="is_new_patient" class="font600 med-orange form-cursor text-underline"><i class="fa fa-plus med-orange margin-r-5"></i>Quick Add</label>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<span class="font600 med-green">
								<i class="fa fa-user"></i>New Patient </span>
								<a target="_blank" href="{{ url('patients/create') }}"><i data-placement='bottom' data-toggle='tooltip' data-original-title='Create New Patient' class="fa fa-plus-circle med-orange"></i></a>
                                    <small class="help-block hide" id="js-error-patient_search"></small>
								
								</div>
							</div>		
*/ ?>
                            
                            <div id="js-searched_patient" @if(@$appointment_details == '') class="hide" @endif>

                                 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0 no-padding margin-b-10 margin-r-10" style="margin-left:-10px;">
                                    <span class="med-orange padding-0-4 font600">Patient Details</span>
                                </div>
                                    <div class="form-group">
                                        {!! Form::hidden('patient_id',@$appointment_details->patient_id,['id'=>'patient_id','class'=>'form-control input-sm-modal form-cursor']) !!}  
                                        {!! Form::label('Name', 'Name', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!}                           
                                        <div class="col-lg-6 col-md-6 col-sm-6">
											<span class="js-searched_patient_cls " id="js-search_patient_last_name">{{@$appointment_details->patient->last_name}}</span>, <span id="js-search_patient_first_name">{{@$appointment_details->patient->first_name}}</span> <span id="js-search_patient_middle_name">{{@$appointment_details->patient->middle_name}}</span> <a href="{{url('patients/'.$patient_encode_id.'/edit')}}" target="_blank" class="js-edit_patient_a_tag"><i class="fa fa-edit form-cursor margin-l-10" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i></a>
										</div>                        
                                    </div>
                                    <div class="form-group">                             
                                        {!! Form::label('First Name', 'DOB', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!}                           
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><span class="sm-size"><span id="js-search_patient_dob"> @if(@$appointment_details->patient->dob != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$appointment_details->patient->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$appointment_details->patient->dob) }} @else @endif </span></span> <span id="js-search_patient_gender">, @if(@$appointment_details->patient->gender == 'Male') Male @elseif (@$appointment_details->patient->gender == 'Female') @else Others @endif </span> </div>                        
                                        <div class="col-sm-1"></div>
                                    </div>

                                    <div class="form-group margin-t-m-3">
                                        <div class="col-lg-3 col-md-3 col-sm-3 med-green font600">Address</div>
                                        <div class="col-lg-7 col-md-7 col-sm-7">
                                            <span id="js-search_patient_address1">{{@$appointment_details->patient->address1}}</span>,<br>
                                            <span id="js-search_patient_city">{{@$appointment_details->patient->city}}</span>, <span id="js-search_patient_state">{{@$appointment_details->patient->state}}</span>, <span id="js-search_patient_zipcode">{{@$appointment_details->patient->zip5}}</span>
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>    

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 no-padding">
                                            <div class="form-group">                             
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12  font600 med-green"> Home Phone </div>                                                
                                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 no-padding">&nbsp; @if(@$appointment_details->patient->phone != '') {{@$appointment_details->patient->phone}} @else -- Nil -- @endif</div>
                                            </div>                                   
                                        </div>

                                        @if(@$appointment_details->patient->mobile != '')
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding">
                                            <div class="form-group">     
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <span class="font600 med-green">Cell Phone :</span> {{@$appointment_details->patient->mobile}}</div>                           
                                            </div>
                                        </div>
                                        @endif
                                    </div>                                               

                                    @foreach(@$appointment_details->patient->insured_detail as $patient_insurance)
                                    @if(@$patient_insurance->category == 'Primary')
                                    <div class="form-group">                             
                                        {!! Form::label('Primary Insurance', 'Primary Ins', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!}
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="js-search_patient_primary_insurance">{{@$patient_insurance->insurance_details->insurance_name}}</div>
                                        <div class="col-sm-1"></div>
                                    </div>
									
									<?php
										$plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$appointment_details->patient->id,@$patient_insurance->insurance_id,@$patient_insurance->policy_id);
                                        $getauth_alert = App\Models\Patients\PatientAuthorization::getalertonAuthorization(@$appointment_details->patient->id,@$patient_insurance->insurance_id);  
										if(@$plan_end_date == '0000-00-00' || @$plan_end_date == '') {
											@$getReachEndday = 0;
										} else  {
											 $now = strtotime(date('Y-m-d')); 
											$your_date = strtotime(@$plan_end_date);
											$datediff = @$now - @$your_date;

											@$getReachEndday =  floor(@$datediff / (60 * 60 * 24));	
										}	
									?>									
                                    <div class="form-group">                             
                                        {!! Form::label('Policy ID', 'Policy ID', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!}                           
                                        <div class="col-lg-4 col-md-6 col-sm-6" id="js-search_patient_primary_insurance_policy_id">{{@$patient_insurance->policy_id}} </div> 

                                        <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray"  @if(@$patient_insurance->eligibility_verification == 'Active' ||@$patient_insurance->eligibility_verification == 'Inactive') style="display:none;" @endif data-patientid="{{ $patient_insurance->patient_id }}" data-category="Primary" title="Check Eligibility"><i class="fa fa-user text-gray font10"></i></a> 
                                        <i class="fa fa-spinner fa-spin eligibilityloadingimg" style="font-size: 11px; display:none;"></i>
                                        <a class="js_get_eligiblity_details js_eliactive" title="Eligibility Details" @if(@$patient_insurance->eligibility_verification == 'None' || @$patient_insurance->eligibility_verification == 'Inactive' || @$patient_insurance->eligibility_verification == 'Error' || @$getReachEndday > 0) style="display:none;" @endif data-patientid="{{ $patient_insurance->patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a>
                                        <a class="js_get_eligiblity_details js_eliinactive" title="Eligibility Details" @if((@$patient_insurance->eligibility_verification == 'None' || @$patient_insurance->eligibility_verification == 'Active' || @$patient_insurance->eligibility_verification == 'Error') && @$getReachEndday <= 0 ) style="display:none;" @endif data-patientid="{{ $patient_insurance->patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a>
                                    </div>
                                    <?php 
										$display_class = (!@$getauth_alert)?'style="display:none;"':"";
										$url = url('/patients/').$patient_encode_id.'/billing_authorization/appointment';
									?>
                                    <div class="form-group margin-t-m-3 js-show-authorization {{$display_class}}">    
                                        <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
											
											<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Authorization</div>
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<a class="js-authpopup btn btn-medcubics-small" href="#" data-toggle="modal" data-target="#auth" data-url="{{@$url}}" tabindex="-1">
												Auth details</a>
											</div>
										</div> 
                                    </div> 
                                    @endif     
                                    @endforeach   
                                    <!--<div class="form-group margin-t-m-3">                       
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <p class="help-block" id="js-search_auth_remain"></p>
                                        </div>      
                                    </div>   
                                    -->                         
                                </div>
                            </div>
                            <div id="js-new_patient" class="hide js-address-class">     
                                {!! Form::hidden('general_address_type','provider',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                {!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                {!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
                             <div class="form-group">   
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 med-green font600">Name</div>
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 font600">
										{!! Form::hidden('last_name',null,['id'=>'js_lastname']) !!} 
										{!! Form::hidden('first_name',null,['id'=>'js_firstname']) !!}   
										{!! Form::hidden('middle_name',null,['id'=>'js_middlename']) !!}    
										<span class="js_lastname"></span>, <span class="js_firstname"></span> <span class="js_middlename"></span>
									</div>
								</div>	
								
								 <div class="form-group margin-t-m-3">
									<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Address</div>                        
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::hidden('address1',null,['id'=>'js_address1']) !!}
										{!! Form::hidden('address2',null,['id'=>'js_address2']) !!}
										{!! Form::hidden('city',null,['id'=>'js_city']) !!} 
										{!! Form::hidden('state',null,['id'=>'js_state']) !!}   
										{!! Form::hidden('zip5',null,['id'=>'js_zip5']) !!}   
										{!! Form::hidden('zip4',null,['id'=>'js_zip4']) !!}
										<span class="js_address1"></span>,<br>
										<span class="js_city"></span>,  <span class="js_state"></span>, <span class="js_zip5"></span>-<span class="js_zip4"></span>
									</div>
									<div class="col-sm-1"></div>
								</div>
								
								<div class="form-group margin-t-m-3">                             
									{!! Form::label('DOB', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::hidden('dob',null,['id'=>'js_dob']) !!}
										{!! Form::hidden('gender',null,['id'=>'js_gender']) !!}   		
										<span class="js_dob_gender"></span>
									</div>
									<div class="col-sm-1"></div>
								</div>
								
								<div class="form-group margin-t-m-3">   
									<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Cell Phone</div> 
									{!! Form::hidden('mobile',null,['id'=>'js_mobile']) !!}
									<div class="col-lg-7 col-md-7 col-sm-7 js_mobile"></div>                      
									<div class="col-sm-1"></div>
								</div>

								<div class="form-group margin-t-m-3">  
									<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Home Phone</div>  
									{!! Form::hidden('home_phone',null,['id'=>'js_home_phone']) !!}   	
									<div class="col-lg-7 col-md-7 col-sm-7 js_home_phone" ></div>                         
									<div class="col-sm-1"></div>
								</div>
								{!! Form::hidden('is_self_pay',null,['id'=>'js_selfpay']) !!}   	
							   							   
								<div class="form-group margin-t-m-3">    
									<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Primary Ins</div> 
									 {!! Form::hidden('primary_insurance_id',null,['id'=>'js_primary_ins']) !!}   
									<div class="col-lg-7 col-md-7 col-sm-7 js_primary_ins"></div>
									<div class="col-sm-1"></div>
								</div>

							   
								<div class="form-group margin-t-m-3">                       
									<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Policy ID</div>{!! Form::hidden('primary_insurance_policy_id',null,['id'=>'js_primary_insurance_policy_id']) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										<div class="js_primary_insurance_policy_id"></div>  
									</div>         
								</div> 
                                 <div class="form-group margin-t-m-3 js-show-authorization" style="display:none;">    
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Auth No</div>                        
                                    <div class="col-lg-12 col-md-12 col-sm-12">                                       
                                        <a class="js-authpopup" href="#" data-toggle="modal" data-target="#auth" data-url="patients/MQ==/billing_authorization" tabindex="-1">
                                        Auth details</a>
                                    </div>  
                                </div> 
								
                            </div>
                            </div>
                        </div>


                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding med-bg-f0f0f0 margin-b-10">
                                <span class="med-orange padding-0-4 font600">Visit Details</span>
                            </div> 
                            <div class="margin-l-10 margin-r-10">
								<div class="form-group js-add-new-reason" id="js-new-reason" >                             
									{!! Form::label('Reason for visit', 'Reason for Visit', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-popup']) !!}                           
									<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 select2-white-popup">

										{!! Form::select('reason_for_visit', array('' => '-- Select --') + (array)$reason_visit,  null,['class'=>'form-control select2 js-add-new-reasonvisit']) !!}

										<!--	 {!! Form::textarea('reason_for_visit',@$appointment_details->reason_for_visit,['id'=>'duration','class'=>'form-control','style'=>'min-height:50px;']) !!}    -->
										<small class="help-block hide" id="js-error-reason_for_visit"></small>
									</div>  
									<div class="form-group hide" id="add_new_span">
										{!! Form::label('', '', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
										<div class="col-lg-8 col-md-7 col-sm-7 col-xs-10">
											{!! Form::text('newadded',null,['id'=>'newadded_visit','class'=>'form-control input-sm-modal-billing','data-table-name'=>'reason_for_visits','data-field-name'=>'reason','data-field-id'=>null,'data-label-name'=>'Reason for visit']) !!}
											<p class="js-error help-block hide"></p>
											<p class="pull-right no-bottom">
												<a href="javascript:void(0)" id="new_save_visit"><i class="fa {{Config::get('cssconfigs.common.save')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="Save"></i></a>
												<a href="javascript:void(0)" id="new_cancel_visit"><i class="fa {{Config::get('cssconfigs.common.cancel')}} margin-l-5" data-placement="bottom" data-toggle="tooltip" data-original-title="Cancel"></i></a> 
											</p>
										</div>
									</div>
								</div>

								<div class="form-group">
									<div class="col-lg-12 col-md-12 col-sm-12 no-padding">
										{!! Form::label('Check In Time', 'Check In', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label-popup']) !!}                           
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bootstrap-timepicker">
											<i class="fa fa-clock-o form-icon-billing" onclick= "iconclick('check_in_time')"></i> 
											{!! Form::text('check_in_time',null,['id'=>'check_in_time','class'=>'form-control input-sm-modal-billing timepicker1 dm-time']) !!}   
											<small class="help-block hide" id="js-error-check_in_time"></small>
										</div>                        
										{!! Form::label('Check Out', 'Check Out', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label-popup']) !!} 
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  bootstrap-timepicker p-l-0">
											<i class="fa fa-clock-o form-icon-billing" onclick= "iconclick('check_out_time')"></i> 
											{!! Form::text('check_out_time',null,['id'=>'check_out_time','class'=>'form-control input-sm-modal-billing timepicker1 dm-time']) !!}   
											<small class="help-block hide" id="js-error-check_out_time"></small>
										</div>                                                
									</div>
								</div>                                                

								<?php
							   // dd($appointment_details);
									$copay_date = 'Co-Pay Date';
									if(@$appointment_details->copay_option!=''){
										if(@$appointment_details->copay_option=='Check'){
											$copay_chk_prt = '';
											$copay_crt_prt = 'hide';
											$copay_money_prt = 'hide';
										}elseif(@$appointment_details->copay_option=='CC'){
											$copay_crt_prt = '';
											$copay_chk_prt = 'hide';
											$copay_money_prt = 'hide';
										}elseif(@$appointment_details->copay_option=='Money Order'){
											$copay_crt_prt = 'hide';
											$copay_chk_prt = 'hide';
											$copay_money_prt = '';
										   //  $copay_date = 'MO Date';
										}else{
											$copay_crt_prt = 'hide';
											$copay_chk_prt = 'hide';
											$copay_money_prt = 'hide';
										}
										$copay_dte_prt = '';
										$copay_disabled_class = "disabled";
										$copay_readonly_class = "readonly";  
										$copay_date_id = 'copay_edit_date';
										$money_readonly = "readonly";  
									}else{
										$copay_money_prt = 'hide';
										$copay_chk_prt = 'hide';
										$copay_crt_prt = 'hide';
										$copay_dte_prt = 'hide';
										$copay_disabled_class = "";
										$copay_readonly_class = "";  
										$copay_date_id = 'copay_date';
										$money_readonly = "";
									}
								?>

								<div class="form-group">
									<div class="col-lg-12 col-md-12 col-sm-12 no-padding">
										{!! Form::label('Co-Pay', 'Co-Pay', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label-popup']) !!}    
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 select2-white-popup">
											{!! Form::select('copay_option',[''=>'-- Select --','Cash'=>'Cash','Check'=>'Check','CC'=>'Credit','Money Order' => "MO"],@$appointment_details->copay_option,['id'=>'copay_option','class'=>'select2 form-control input-sm-modal',$copay_disabled_class]) !!}
											<small class="help-block hide" id="js-error-copay_option"></small>
										</div>                        
										{!! Form::label('Co-Pay Amt', 'Amount', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-6  control-label-popup']) !!}
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 p-l-0">                       
											{!! Form::text('copay',@$appointment_details->copay,['id'=>'copay_amount','class'=>'form-control js_amt_format allownumericwithdecimal input-sm-header-billing',$copay_readonly_class]) !!}
											<small class="help-block hide" id="js-error-copay"></small>
										</div>                                           
									</div>
								</div>                                              
								
								<div class="form-group js_copay_check_part {{@$copay_chk_prt}}">                         
									<input type="hidden" id="check_no_minlength" value="{{Config::get('siteconfigs.payment.check_no_minlength')}}" />
									{!! Form::label('Check No', 'Check No', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
									<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
										{!! Form::text('copay_check_number',@$appointment_details->copay_check_number,['id'=>'copay_check_number','class'=>'js_all_caps_format form-control input-sm-header-billing',$copay_readonly_class]) !!}   
									</div>                        
									<div class="col-sm-1"></div>
								</div>
								
								<div class="form-group js_copay_card_part {{$copay_crt_prt}}">                         
									{!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
									<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
										{!! Form::select('copay_card_type',['Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$appointment_details->copay_card_type,['class'=>'select2 form-control','id'=>'js-patient_search_category',$copay_disabled_class]) !!}
									</div>                        
									<div class="col-sm-1"></div>
								</div>
								
								<div class="form-group js_copay_date_part {{$copay_dte_prt}}">                             
									{!! Form::label('Co-Pay Date', $copay_date, ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
									<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
										<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
										<?php
											$appointment_details_copay_dte = '';
											if(@$appointment_details->copay_date != '' && @$appointment_details->copay_date!='0000-00-00')
												$appointment_details_copay_dte = date("m/d/Y", strtotime(@$appointment_details->copay_date));
										?>     
										{!! Form::text('copay_date',@$appointment_details_copay_dte,['id'=>@$copay_date_id,'placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing dm-date',$copay_readonly_class]) !!}   
										<small class="help-block hide" id="js-error-copay_date"></small>
									</div>                        
									<div class="col-sm-1"></div>
								</div>
								
								
								<div class="form-group js-hide-money {{$copay_money_prt}}">  
									{!! Form::label('Money order No.', 'MO No', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label-popup']) !!}
									<div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
										{!! Form::text('money_order_no',@$appointment_details->copay_check_number,['class'=>'form-control input-sm-header-billing','maxlength'=>25, $money_readonly]) !!}
									</div>                        
									<div class="col-sm-1"></div>
								</div>     
															
								<div class="form-group">                             
									{!! Form::label('Co-Pay Details', 'Co-Pay Details', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label-popup']) !!}                           
									<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
										{!! Form::text('copay_details',@$appointment_details->copay_details,['id'=>'copay_details','class'=>'form-control input-sm-header-billing',$copay_readonly_class]) !!}   
										<small class="help-block hide" id="js-error-copay_details"></small>
									</div>                                            
								</div>

								<div class="form-group no-bottom">                             
									{!! Form::label('Non Billable Visit', 'Non Billable Visit', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}                           
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
										{!! Form::radio('non_billable_visit','yes',(@$appointment_details->non_billable_visit=='Yes')? true:'',['class'=>'','id'=>'c-non-y']) !!} {!! Form::label('c-non-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!}  &emsp;
										{!! Form::radio('non_billable_visit','no',(@$appointment_details->non_billable_visit=='No')? true:'',['class'=>'','id'=>'c-non-n']) !!} {!! Form::label('c-non-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
										<small class="help-block hide" id="js-error-non_billable_visit"></small>
									</div>                                           
								</div>
							</div>
                        </div>                        
                    </div>
                </div>
            </div>     
            
            <div class="text-center margin-b-10">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn','accesskey'=>'s']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js-app_appointment_cancel','data-id'=>@$appointment_details->id]) !!}
            </div>

            {!! Form::close() !!}
            <input type='hidden' id='provider_available_dates' value="{{$provider_available_dates}}">
            <input type='hidden' id='user_selected_date' value="{{$user_selected_date}}">
            
            <div class="modal-footer no-padding med-bg-green">
                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                    <span class="med-white line-height-26 font600">Links :</span>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-9 col-xs-9">
                    <div class="payment-links pull-right">
                        <ul class="nav nav-pills line-height-26">                                               
                            <li><a href="{{url('patients/'.$patient_encode_id.'/billing')}}" target="_blank" class="js-edit_patient_a_tag"><i class="fa fa-pencil med-white" data-placement="bottom" data-toggle="tooltip" data-original-title="Charges"></i></a></li>
                            <li><a href="{{url('patients/'.$patient_encode_id.'/payments')}}" target="_blank"><i class="fa fa-money med-white" data-placement="bottom"  data-toggle="tooltip" data-original-title="Payments"></i></a></li>
                            <li><a href="{{url('patients/'.$patient_encode_id.'/ledger')}}" target="_blank"><i class="fa fa-newspaper-o med-white" data-placement="bottom"  data-toggle="tooltip" data-original-title="Ledger"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>            
        </div>
    </div> <!-- /.modal-content -->
</div> <!-- /.modal-dialog -->