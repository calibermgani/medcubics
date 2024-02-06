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
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close close_popup js_recentform" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">New Appointment</h4>
        </div>
        <div class="modal-body p-b-0">


			<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.scheduler.appointment_popup") }}' />
			
            {!! Form::open(['name'=>'myform','id'=>'js-bootstrap-validator','class'=>'popupmedcubicsform']) !!}
            {!! Form::hidden('default_view',$default_view,['id'=>'default_view']) !!} 
            {!! Form::hidden('default_view_list_caption',$default_view_list_caption,['id'=>'default_view_list_caption']) !!}
            {!! Form::hidden('resource_caption',$resource_caption,['id'=>'resource_caption']) !!}
            <small class="help-block hide" id="js-error-msg"></small>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border tabs-border margin-t-10">
                <p class="no-bottom  margin-t-m-10"><span class="bg-white padding-0-4 med-orange font600">General Info</span></p>
                <div class="box box-info no-shadow no-border modal-timing-bg no-bottom">
                    <div class="box-body  form-horizontal">
                        <div class="form-group">                             
                            {!! Form::label($default_view_list_caption, $default_view_list_caption, ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}
                            <div class="col-lg-7 col-md-7 col-sm-7 select2-white-popup">
                                {!! Form::select('default_view_list_id',[''=>'-- Select --']+(array)$default_view_list,$default_view_list_id,['class'=>'select2 form-control input-sm-modal-billing','id'=>'js-ptsh_default_view_list']) !!}
                                <small class="help-block hide" id="js-error-default_view_list_id"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group">                             
                            {!! Form::label($resource_caption, $resource_caption, ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                            <div class="col-lg-7 col-md-7 col-sm-7 select2-white-popup">
                                {!! Form::select('resource_id',[''=>'-- Select --']+(array)$resources,$resource_id,['class'=>'select2 form-control input-sm-modal-billing','id'=>'js-ptsh_resource']) !!}
                                <small class="help-block hide" id="js-error-resource_id"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group">   
                            <?php
                            $user_selected_time_convert = '';
                            if ($user_selected_date != '')
                                $user_selected_time_convert = date("m/d/Y", strtotime($user_selected_date));
                            ?>
                            {!! Form::label('Schedule On', 'Schedule On', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 select2-white-popup">
                                <span id="scheduled_on_icon"><i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i></span>
                                {!! Form::text('scheduled_on', $user_selected_time_convert,['id'=>'scheduled_on','readonly','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing form-cursor dm-date', 'onselect'=>"myFunction()"]) !!}   
                                <small class="help-block hide" id="js-error-scheduled_on"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group">                             
                            {!! Form::label('Appointment Time', 'Appt Time', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 select2-white-popup">
                                <div id="js-available_slot_timings">
                                    {!! Form::select('appointment_time',[''=>'-- Select --']+(array)@$array_of_time[0],$sch_app_time,['class'=>'select2 form-control','id'=>'appointment_time']) !!}
                                </div>
                                <small class="help-block hide" id="js-error-appointment_time"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>                      


              <!--          <div class="form-group">                            
                            {!! Form::label('Search Category', 'Search Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-billing font600 med-green']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10 select2-white-popup">
                                {!! Form::select('patient_search_category',['all'=>'-- Select --','first_name'=>'First Name','last_name'=>'Last Name','acc_no'=>'Acc No','dob'=>'DOB','ssn'=>'SSN'   ,'address'=>'Address'],'all',['class'=>'select2 form-control','id'=>'js-patient_search_category']) !!}
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div> -->

                        <div class="form-group">                            
                            {!! Form::label('Search Patient', 'Search Patient', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-billing font600 med-green star']) !!}
                            <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                {!! Form::text('patient_search',null,['id'=>'patient_search','class'=>'form-control  input-sm-header-billing','autocomplete'=>'nope','placeholder'=>'LN, FN, Acc No, SSN, DOB, Address']) !!}
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>


                        <div class="form-group">                            
                            {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-billing font600 med-green']) !!}                           
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 bg-aqua" style="padding-top:5px;">                                
                               
                                <a target="_blank" href="{{ url('patients/create') }}" accesskey="n" class="font600 med-orange "><i class="fa fa-plus-circle med-orange margin-r-5"></i><span class="text-underline">N</span>ew Patient</a> <span class="font600 med-green">&nbsp;</span>  <span class="med-gray-dark margin-r-5">|</span>
								<span style="display:none">
                                {!! Form::checkbox('is_new_patient','yes',null,['class'=>'flat-red hide','id'=>'is_new_patient']) !!}</span> <label for="is_new_patient" class="font600 med-orange form-cursor js_quick_add" ><i class="fa fa-plus med-orange margin-r-5"></i>Quick Add</label> 
                                <small class="help-block hide" id="js-error-patient_search"></small>
                            </div>                        
                            
                        </div>


                        <div id="js-searched_patient" class="hide no-padding no-b-l no-b-r yes-border tabs-border margin-b-10">
                            <div class="margin-t-10">
                                <div class="form-group">     
                                    {!! Form::hidden('patient_id',null,['id'=>'patient_id','class'=>'form-control input-sm-header-billing']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 med-green font600">Name</div>                            
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 font600">
                                        <span class="js-searched_patient_cls " id="js-search_patient_last_name"></span>, <span id="js-search_patient_first_name"></span> <span id="js-search_patient_middle_name"></span> <a href="javascript:void(0);" target="_blank" class="js-edit_patient_a_tag pull-right med-orange text-underline"><i class="fa fa-edit form-cursor margin-l-10 margin-r-5" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i>Edit Demo</a>
                                    </div>                                                       
                                </div>
                                
                                <!-- Patient balance show here-->
                                <div class="form-group margin-t-m-3">                             
                                    {!! Form::label('Patient Bal', 'Patient Balance', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                        <span id="js-search_patient_bal" class="bg-green-date font600"></span>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>

                                <div class="form-group margin-t-m-3">                             
                                    {!! Form::label('DOB', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                        <i class="fa fa-birthday-cake med-gray"></i> <span id="js-search_patient_dob"></span>, <span id="js-search_patient_gender"></span>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>

                                <div class="form-group margin-t-m-3 js_search_ssn">   
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">SSN</div>   
                                    <?php $tot_ssn_list =(App\Models\Patients\Patient::total_ssn());?>
                                   
                                    <div class="col-lg-7 col-md-7 col-sm-7"><i class="fa fa-pencil med-gray"></i> <span  id="js-search_patient_ssn"></span></div>                        
                                    <div class="col-sm-1"></div>
                                </div>
                                
								
                                <div class="form-group margin-t-m-3">
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Address</div>                        
                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                        <i class="fa {{Config::get('cssconfigs.admin.pos')}} med-gray"></i> <span id="js-search_patient_address1"></span>,<br>
                                        <span id="js-search_patient_city"></span>,  <span id="js-search_patient_state"></span>, <span id="js-search_patient_zipcode"></span>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>                  

								
                                <div class="form-group margin-t-m-3 js_search_phon">   
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Cell Phone</div>                                                   
                                    <div class="col-lg-7 col-md-7 col-sm-7"><i class="fa fa-phone med-gray"></i> <span  id="js-search_patient_mobile"></span></div>                        
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group margin-t-m-3 js_search_mob">  
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Home Phone</div>                        
                                    <div class="col-lg-7 col-md-7 col-sm-7"><i class="fa fa-tty med-gray"></i> <span id="js-search_patient_home_phone"></span></div>                         
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group margin-t-m-3 js_search_app_ins">    
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600"> Ins</div>                        
                                    <div class="col-lg-7 col-md-7 col-sm-7"><i class="fa {{Config::get('cssconfigs.common.insurance')}} med-gray"></i><span id="js-search_patient_primary_insurance"></span></div>                        
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="form-group margin-t-m-3 js_search_app_policy">                       
                                    <div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Policy ID</div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div><i class="fa fa-pencil med-gray"></i> <span id="js-search_patient_primary_insurance_policy_id"></span></div>  
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 js_eligiblity_status">                                         
                                        <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray" style="display:none;" data-patientid="" data-category="Primary"><i data-placement="bottom" data-toggle="tooltip" data-original-title="Check Eligibility"  class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-gray font10"></i></a> 
                                        <i class="fa fa-spinner fa-spin eligibilityloadingimg font11" style="display:none;"></i>
                                        <a class="js_get_eligiblity_details js_eliactive" style="display:none;" data-patientid="" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i data-placement="bottom" data-toggle="tooltip" data-original-title="Eligibility Details" class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-green font10"></i></a>
                                        <a class="js_get_eligiblity_details js_eliinactive" style="display:none;" data-patientid="" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i data-placement="bottom" data-toggle="tooltip" data-original-title="Eligibility Details" class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-red font10"></i></a>
                                    </div>                    
                                </div> 
                                <div class="form-group margin-t-m-3 js-show-authorization" style="display:none;">    
                                    <div class="col-lg-12 col-md-12 col-sm-12 no-padding">

										<div class="col-lg-4 col-md-4 col-sm-4 med-green font600 margin-t-8">Authorization</div>                        
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">                                       
										<a class="js-authpopup btn btn-medcubics-small" href="#" data-toggle="modal" data-target="#auth" data-url="patients/MQ==/billing_authorization" tabindex="-1">
											Auth Details</a>
										</div>

									</div> 
                                </div> 
                                
                            </div>
                        </div>

                        <div id="js-new_patient" class="hide js-address-class">  
                            {!! Form::hidden('general_address_type','patient',['class'=>'js-address-type']) !!}
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
								<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">SSN</div> {!! Form::hidden('ssn',null,['id'=>'js_ssn']) !!}
								<div class="col-lg-7 col-md-7 col-sm-7 js_ssn"></div>                        
								<div class="col-sm-1"></div>
                            </div>
							
							<div class="form-group margin-t-m-3">   
								<div class="col-lg-4 col-md-4 col-sm-4 med-green font600">Cell Phone</div> {!! Form::hidden('mobile',null,['id'=>'js_mobile']) !!}
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
                            {!! Form::hidden('patient_temp_id','null',['class'=>'js-temp_id']) !!}
                        </div>
                        <div class="form-group js-add-new-reason" id="js-new-reason" >                             
                            {!! Form::label('Reason for Visit', 'Reason for Visit', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!}                           
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 select2-white-popup">

                                {!! Form::select('reason_for_visit', array('' => '-- Select --') + (array)$reason_visit,  null,['class'=>'form-control select2 js-add-new-reasonvisit input-sm-modal-billing']) !!}

                                <small class="help-block hide" id="js-error-reason_for_visit"></small>
                            </div>  
                            <div class="form-group hide" id="add_new_span">
                                {!! Form::label('', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::text('newadded',null,['id'=>'newadded_visit','class'=>'form-control input-sm-header-billing','data-table-name'=>'reason_for_visits','data-field-name'=>'reason','data-field-id'=>null,'data-label-name'=>'Reason for visit']) !!}
                                    <p class="js-error help-block hide"></p>
                                    <p class="pull-right no-bottom">
                                        <a href="javascript:void(0)" id="new_save_visit" class="font600 "><i class="fa {{Config::get('cssconfigs.common.save')}}" ></i> Save </a> <span class="margin-l-5">|</span>
                                        <a href="javascript:void(0)" id="new_cancel_visit" class="margin-l-5 font600 "><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                                {!! Form::label('Check In Time', 'Check In', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup']) !!}
                                <div class="col-lg-3 col-md-2 col-sm-3 col-xs-6 bootstrap-timepicker">
                                    <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('check_in_time')"></i> 
                                    {!! Form::text('check_in_time',null,['id'=>'check_in_time','class'=>'form-control input-sm-header-billing timepicker1 dm-time', 'autocomplete'=>'nope']) !!}   
                                    <small class="help-block hide" id="js-error-check_in_time"></small>
                                </div>                        
                                {!! Form::label('Check Out', 'Out', ['class'=>'col-lg-2 col-md-3 col-sm-2 col-xs-12 control-label-popup']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 bootstrap-timepicker p-l-0">
                                    <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('check_out_time')"></i> 
                                    {!! Form::text('check_out_time',null,['id'=>'check_out_time','class'=>'form-control input-sm-header-billing timepicker1 dm-time', 'autocomplete'=>'nope']) !!}
                                    <small class="help-block hide" id="js-error-check_out_time"></small>
                                </div>                                                        
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                                {!! Form::label('Co-Pay', 'Co-Pay', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}    
                                <div class="col-lg-3 col-md-3 col-sm-3 select2-white-popup">
                                    {!! Form::select('copay_option',[''=>'-- Select --','Cash'=>'Cash','Check'=>'Check','CC'=>'Credit','Money Order' => "MO"],null,['id'=>'copay_option','class'=>'select2 form-control input-sm-modal']) !!}
                                    <small class="help-block hide" id="js-error-copay_option"></small>
                                </div>                        
                                {!! Form::label('Co-Pay Amt', 'Amount', ['class'=>'col-lg-2 col-md-2 col-sm-2 control-label-popup']) !!}
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 p-l-0">                                    
                                    {!! Form::text('copay',null,['id'=>'copay_amount','class'=>'form-control js_amt_format allownumericwithdecimal input-sm-header-billing']) !!}
                                    <small class="help-block hide" id="js-error-copay"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                        </div>

                        <div class="form-group js_copay_check_part hide">                         
                            <input type="hidden" id="check_no_minlength" value="{{Config::get('siteconfigs.payment.check_no_minlength')}}" />
                            {!! Form::label('Check No', 'Check No', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
                                {!! Form::text('copay_check_number',null,['id'=>'copay_check_number','class'=>'form-control js_all_caps_format input-sm-header-billing js-check-number','maxlength'=>20]) !!}   
								{!! $errors->first('copay_check_number', '<p> :message</p>')  !!}
								<span id="check_no"></span>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                          <div class="form-group js-hide-money hide">                         
                            
                            {!! Form::label('Money order No.', 'MO No', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
                                {!! Form::text('money_order_no',null,['class'=>'form-control input-sm-header-billing','maxlength'=>25]) !!}
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>                       

                        <div class="form-group js_copay_card_part hide">                         
                            {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
                                {!! Form::select('copay_card_type',['Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],null,['class'=>'select2 form-control','id'=>'js-patient_search_category']) !!}   
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>

                        <div class="form-group js_copay_date_part hide">                             
                            {!! Form::label('Co-Pay Date', 'Co-Pay Date', ['class'=>'js-label-change col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}
                            <div class="col-lg-5 col-md-5 col-sm-7 col-xs-10">
                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                {!! Form::text('copay_date',null,['id'=>'copay_date','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing dm-date', 'autocomplete'=>'nope']) !!}   
                                <small class="help-block hide" id="js-error-copay_date"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>

                        <div class="form-group-billing">                             
                            {!! Form::label('Co-Pay Details', 'Co-Pay Details', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                                {!! Form::text('copay_details',null,['id'=>'copay_details','class'=>'form-control input-sm-header-billing', 'maxlength' => 30, 'autocomplete' =>'nope' ]) !!}   
                                <small class="help-block hide" id="js-error-copay_details"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>
                        <div class="form-group">                             
                            {!! Form::label('Non Billable Visit', 'Non Billable Visit', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-billing med-green font600']) !!}                           
                            <div class="col-lg-7 col-md-7 col-sm-7">
                                {!! Form::radio('non_billable_visit','yes',null,['class'=>'','id'=>'c-non-y']) !!} {!! Form::label('c-non-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!}  &emsp;
                                {!! Form::radio('non_billable_visit','no',true,['class'=>'','id'=>'c-non-n']) !!} {!! Form::label('c-non-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                <small class="help-block hide" id="js-error-non_billable_visit"></small>
                            </div>                        
                            <div class="col-sm-1"></div>
                        </div>               
                    </div>
                </div>
            </div>      

            <div class="modal-footer">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js-submit-btn margin-t-10','accesskey'=>'s']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js_recentform margin-t-10','data-dismiss'=>'modal']) !!}
            </div>

            {!! Form::close() !!}
            <input type='hidden' id='provider_available_dates' value="{{$provider_available_dates}}">
            <input type='hidden' id='user_selected_date' value="{{$user_selected_date}}">
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    disableAutoFill('#js-bootstrap-validator');
    var tem_ssn = new Array();
    var tem_dob = new Array();
	var current_date = '{{date("Y-m-d")}}';
        <?php /* Scheduler Quick patient ssn validation check unique validation */
            foreach($tot_ssn_list as $key => $val){ ?>
                tem_dob.push('<?php echo $val; ?>');
                tem_ssn.push('<?php echo $key; ?>');
        <?php }       
        if($patient !=""){
        ?>
        $(document).ready(function () {
            
			$(".modal-content #js-bootstrap-validator #patient_search").val("{{$patient}}");          
			patient_search_func();          
			$('#patient_search').autocomplete('search', $('#patient_search').val());     
      
			$('#patient_search').on('autocompleteopen', function(){                  
				$('#patient_search').data('ui-autocomplete')._trigger('select', 'autocompleteselect', {item:{value:$(".ui-autocomplete > li:first").trigger("click")}});            
            });
			$( "#patient_search" ).on( "keyup keypress blur change", function() {
				//  console.log("test");
				$('#patient_search').unbind("autocompleteopen");
			});  
			setTimeout(function(){    
				$("#patient_id").val("{{$patient_id}}");
			}, 1000);         
        });       
		<?php }?>
</script>