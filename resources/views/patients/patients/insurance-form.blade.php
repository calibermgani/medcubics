{!! Form::open(['onsubmit'=>"event.preventDefault();",'name'=>'v2-insuranceeditform_'.@$patient_insurance->id,'id'=>'v2-insuranceeditform_'.@$patient_insurance->id,'class'=>'v2-insurance-info-form medcubicsform js-v2-common-info-form hide']) !!}

<?php 
	$insurance_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_insurance->id,'encode');  
	$patient_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_insurance->patient_id,'encode'); 
    $insurance_status = App\Http\Helpers\Helpers::getInsurancestatus(@$patient_insurance->insurance_id,@$patient_insurance->patient_id);  
	$patientinsurance_insurancetype_id = @$patient_insurance->insurance_details->insurancetype_id;  
	
	if(@$patients->is_self_pay	=='Yes') {	
		$ins_rep_disabled_class = "disabled";
		$ins_rep_readonly_class = "readonly";  		
	} else {
		$ins_rep_disabled_class = "";
		$ins_rep_readonly_class = "";  
	}
?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.insurance") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding no-border  no-shadow" id="js-insurance-add{{ $cur_count }}">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-insurance-parent_div no-padding " id="js-insurance-parent_div{{ $cur_count }}" ><!-- Content Starts -->
        <div id="add-form-value" >
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white">
                <div class="col-lg-12 margin-t-10 no-padding"  style="border-bottom: 2px solid #f0f0f0;">
                    <div class="box-body form-horizontal">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive">
                                    <h4 class="med-darkgray margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> <span id="js-insurance-title_{{ $cur_count }}">@if(isset($patient_insurance->category) &&!empty(@$patient_insurance->category) && (@$patient_insurance->category!='')) {{@$patient_insurance->category}} @else New Category @endif </span></h4>

                                    <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">

                                            <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12  form-horizontal"><!-- Left side Content Starts -->
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                                                {!! Form::label('Category', 'Category', ['class'=>'control-label star']) !!}
                                                                {!! Form::select('category', [''=>'-- Select --','Primary' => 'Primary','Secondary' => 'Secondary','Tertiary' => 'Tertiary','Workers Comp' => 'Workers Comp','Auto Accident' => 'Auto Accident','Attorney' => 'Attorney','Others'=>'Others'],@$patient_insurance->category,['class'=>'select2 form-control js_select_category_class js-ajax-append-category-selection','id'=>'category',$ins_rep_disabled_class,'data-insid'=>$patient_insurance->id]) !!}
                                                            </div>                                                                         
                                                        </div>
                                                    </div>
													
                                                    <?php $disabled  = (@$patientinsurance_insurancetype_id!=0 || @$patients->is_self_pay	=='Yes') ? 'disabled' : ""; ?>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">                                   
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10" data-id="{{ $insurance_status }}">
                                                                {!! Form::label('Name', 'Insurance', ['class'=>'control-label']) !!}
                                                               @if($insurance_status == '1')     
                                                                {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances,  @$patient_insurance->insurance_id.'::'.@$patientinsurance_insurancetype_id,['maxlength'=>'29','class'=>' form-control insurance_id js-sel-modalinsurance-address v2-js-insurance-change','readonly'=>'readonly','id'=>'insurance_id-'.@$patient_insurance->id,$ins_rep_disabled_class,'style'=>'pointer-events:none']) !!} 
                                                                @else
                                                                 {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances,  @$patient_insurance->insurance_id.'::'.@$patientinsurance_insurancetype_id,['maxlength'=>'29','class'=>'select2 form-control insurance_id js-sel-modalinsurance-address v2-js-insurance-change','id'=>'insurance_id-'.@$patient_insurance->id,$ins_rep_disabled_class]) !!} 
                                                                 @endif                                                       
                                                            </div> 
                                                        
                                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 col-lg-offset-1 js-add-new-select hide" id="js-insurance-type-{{@$patient_insurance->id}}">
                                                                <span class="js_common_ins" @if($errors->first('insurancetype_id')) error @endif>
                                                                      {!! Form::label('Insurance Type', 'Insurance Type', ['class'=>'control-label']) !!}
                                                                      {!! Form::select('insurancetype_id', array('' => '-- Select --')+(array)$insurancetypes,  @$patientinsurance_insurancetype_id,['class'=>'form-control select2 js-add-new-select-opt', $disabled,'id'=>'insurancetype_id-'.@$patient_insurance->id]) !!}
                                                                      {!! $errors->first('insurancetype_id', '<p> :message</p>')  !!} 
                                                                </span>


                                                                <span class="hide" id="add_new_span">
                                                                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'control-label']) !!} 
                                                                    <div class="col-lg-6 col-md-7 col-sm-5 col-xs-10">
                                                                        {!! Form::text('newadded',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Insurance Type','data-table-name'=>'insurancetypes','data-field-name'=>'type_name','data-field-id'=>@$patientinsurance_insurancetype_id,'data-label-name'=>'insurance type']) !!}
                                                                        <p class="js-error help-block hide"></p>
                                                                        <p class="pull-right no-bottom">
                                                                            <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                                                                            <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                                                                        </p>
                                                                    </div>
                                                                </span>

                                                            </div> 
                                                        </div>

                                                    </div>

                                                    <?php $chkinsurancetype = App\Http\Controllers\Patients\Api\PatientApiController::InsurancetypeCheck(@$patientinsurance_insurancetype_id);  ?>
                                                    <input type="hidden" id="js_policyid_chk_{{@$patient_insurance->id}}" />
                                                    <input type="hidden" id="js_insurancetype_chk_{{@$patient_insurance->id}}" value="{{ @$chkinsurancetype }}" />
                                                    <div @if(isset($patient_insurance->category) && $patient_insurance->category == 'Secondary') class="col-lg-12" @else class="col-lg-12 hide" @endif id="secondaryInsuranceCode_{{$patient_insurance->id}}">
                                                        <div class="form-group">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 js_medicareins_{{@$patient_insurance->id}}">
                                                                {!! Form::label('If Medicare Secondary ?', 'If Medicare Secondary ?', ['class'=>'control-label']) !!}
                                                                {!! Form::select('medical_secondary_code', array('' => '-- Select --')+(array)$medical_secondary_list,  @$patient_insurance->medical_secondary_code,['class'=>'form-control select2 js-medical-secondary-'.$patient_insurance->id,'data-id'=>@$patient_insurance->id,$ins_rep_disabled_class]) !!}
                                                                {!! $errors->first('medical_secondary_code', '<p> :message</p>')  !!}  
                                                            </div>                                                                         
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                                                <?php $cur_count_val = $cur_count - 1; ?>
                                                                <!--{!! Form::hidden("temp_doc_id['.$cur_count.']",'',['id'=>'temp_doc_id', 'class'=>'jscount-'.$cur_count_val]) !!}-->

                                                                {!! Form::label('Policy ID', 'Policy ID', ['class'=>'control-label star']) !!}
                                                                {!! Form::text('policy_id',@$patient_insurance->policy_id,['autocomplete'=>'off' ,'class'=>'js_no_space form-control policy-check dm-policy-id js-bootstrap-policyid js-all-caps-letter-format','maxlength'=>'29','id'=>'policy_id-'.@$patient_insurance->id,$ins_rep_readonly_class]) !!}
                                                            </div>

                                                            <?php 
																$plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient_insurance->patient_id,@$patient_insurance->insurance_id,@$patient_insurance->policy_id,@$patient_insurance->category);
															?>

                                                            @if($plan_end_date == '0000-00-00' || $plan_end_date == '')

                                                            <?php $getReachEndday = 0; ?>

                                                            @else 
                                                            <?php
																$now = strtotime(date('Y-m-d')); // or your date as well
																$your_date = strtotime($plan_end_date);
																$datediff = $now - $your_date;
																$getReachEndday =  floor($datediff / (60 * 60 * 24));	
															?>
                                                            @endif	
                                                            @if(@$patients->is_self_pay != 'Yes')
                                                            <div class="col-lg-1 col-sm-1 col-xs-2 p-l-0">
                                                                <a id="document_add_modal_link_policy_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.@$id.'/'.@$patient_encode_id.'/Patient_Documents_Insurance_Card_Copy')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} margin-t-20 <?php echo(isset($insurance_policy->category)?'icon-orange-attachment':'icon-green-attachment') ?>" style="margin-top: 24px;"></i></a>

                                                                @if($patient_insurance->policy_id != '' && $patients->is_self_pay != 'Yes')
                                                                <span  class="js_insgray{{ @$patient_insurance->id }} pull-right margin-r-10 margin-t-5" @if((@$patient_insurance->eligibility_verification == '' || @$patient_insurance->eligibility_verification == 'Active' || @$patient_insurance->eligibility_verification == 'Inactive') && (@$patient_insurance->eligibility_verification != 'None' || @$patient_insurance->eligibility_verification != 'Error'))  style="display:none;" @endif >	
                                                                       <a href="javascript:void(0);"><i class="fa fa-user text-gray font14 margin-t-20"></i></a> 
                                                                </span>

                                                                <span class="js_insgreen{{ @$patient_insurance->id }}  pull-right margin-r-10 margin-t-5" @if((@$patient_insurance->eligibility_verification=='' || @$patient_insurance->eligibility_verification == 'Inactive' || @$patient_insurance->eligibility_verification == 'None' || @$patient_insurance->eligibility_verification == 'Error') && (@$patient_insurance->eligibility_verification != 'Active') || $getReachEndday > 0) style="display:none;" @endif >	
                                                                     <a @if(Session::get('practice_dbid') == 40) class="js_get_eligiblity_details_waystar" @else class="js_get_eligiblity_details" @endif data-unid="{{ @$patient_insurance->id }}" data-page="pat_ins" data-patientid="{{ @$id }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font14 margin-t-20" data-toggle="tooltip" data-placement="bottom"  data-original-title="Eligibility Details" ></i></a> 
                                                                </span>

                                                                <span class="js_insred{{ @$patient_insurance->id }}  pull-right margin-r-10 margin-t-5" @if((@$patient_insurance->eligibility_verification=='' || @$patient_insurance->eligibility_verification == 'Active' || @$patient_insurance->eligibility_verification == 'None' || @$patient_insurance->eligibility_verification == 'Error' || $getReachEndday < 0) && (@$patient_insurance->eligibility_verification != 'Inactive') && $getReachEndday <= 0   ) style="display:none;" @endif >	
                                                                      <a @if(Session::get('practice_dbid') == 40) class="js_get_eligiblity_details_waystar" @else class="js_get_eligiblity_details" @endif  data-unid="{{ @$patient_insurance->id }}" data-page="pat_ins" data-patientid="{{ @$id }}" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font14 margin-t-20" data-toggle="tooltip" data-placement="bottom"  data-original-title="Eligibility Details" ></i></a> 
                                                                </span>

                                                                <i class="fa fa-spinner fa-spin font11 patientinsloadingimg{{ @$patient_insurance->id }}  pull-right margin-r-10 margin-t-30" style="display:none;"></i>		

                                                                @endif
																

                                                            </div>
                                                            @endif


                                                            <div class="col-lg-3 col-sm-3 col-xs-2 p-l-0 ">
															&nbsp;<br/>
                                                                <a  data-unid="{{ @$patient_insurance->id }}"  data-patientid="{{ @$id }}" data-page="pat_ins" class="js-patient-eligibility_check" href="javascript:void(0);"><span data-toggle="tooltip" data-placement="bottom" href="" class="btn btn-medcubics-small font600 border-radius-4 margin-l-5 margin-t-2" style="padding: 2px 8px; ">Check Eligibility</span></a>
                                                                
                                                            </div>


                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10" @if(@$registration->group_name_id !=1) style="display:none;" @endif>                                      
                                                                 {!! Form::label('Group Name / ID', 'Group Name / ID', ['class'=>'control-label']) !!}
                                                                 {!! Form::text('group_name',@$patient_insurance->group_name,['autocomplete'=>'off' ,'class'=>'form-control js-all-caps-letter-format dm-group-id','maxlength'=>'28',$ins_rep_readonly_class]) !!}
                                                        </div>                                                                   
                                                    </div>
                                                </div>


                                                <div class="col-lg-12" @if(@$registration->adjustor_ph !=1 || @$registration->adjustor_fax !=1) style="display:none;" @endif>
                                                     <div class="form-group">
                                                        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 hide"  @if(@$registration->adjustor_ph !=1) style="display:none;" @endif>
                                                             {!! Form::label('Adjustor Ph', 'Adjustor Phone', ['class'=>'control-label']) !!}
                                                             {!! Form::text('adjustor_ph',@$patient_insurance->adjustor_ph,['class'=>'form-control js-phone dm-phone','id'=>'phone',$ins_rep_readonly_class]) !!} 
                                                    </div> 

                                                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 hide" @if(@$registration->adjustor_fax !=1) style="display:none;" @endif>
                                                         {!! Form::label('Adjustor Ph', 'Adjustor Fax', ['class'=>'control-label']) !!}
                                                         {!! Form::text('adjustor_fax',@$patient_insurance->adjustor_fax,['class'=>'form-control js-fax dm-fax','id'=>'fax',$ins_rep_readonly_class]) !!}
                                                </div> 
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                                    <i class="fa fa-calendar-o form-icon-demo"></i>   
                                                    {!! Form::label('Effective Date', 'Effective Date', ['class'=>'control-label']) !!}
                                                    {!! Form::text('effective_date', App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patient_insurance->effective_date),['autocomplete'=>'nope', 'id'=>'effective_date-'.@$patient_insurance->id,'placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>(@$patients->is_self_pay!='Yes')? "form-control form-cursor dm-date js_datepicker" : "form-control form-cursor dm-date",$ins_rep_readonly_class]) !!}
                                                </div> 

                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                                    <i class="fa fa-calendar-o form-icon-demo"></i> 
                                                    {!! Form::label('Termination Date', 'Termination Date', ['class'=>'control-label']) !!}
                                                    {!! Form::text('termination_date',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patient_insurance->termination_date),['autocomplete'=>'nope', 'id'=>'termination_date-'.@$patient_insurance->id,'placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>(@$patients->is_self_pay!='Yes')? "form-control form-cursor dm-date js_datepicker" : "form-control form-cursor dm-date",$ins_rep_readonly_class]) !!}
                                                </div> 
                                            </div>
                                        </div>

                                        <div class="no-padding form-horizontal js-address-class" id="js-address-general-address_{{@$patient_insurance->id}}"><!-- Right side Content Starts -->   
                                            <?php
												$first_name = $patient_insurance->first_name;
												$last_name = $patient_insurance->last_name;
												$middle_name = $patient_insurance->middle_name;
												$ssn = $patient_insurance->insured_ssn;
												$dob = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($patient_insurance->insured_dob);
												$address1 = @$patient_insurance->insured_address1;
												$address2 = @$patient_insurance->insured_address2;
												$city = @$patient_insurance->insured_city;
												$state = @$patient_insurance->insured_state;
												$zip5 = @$patient_insurance->insured_zip5;
												$zip4 = @$patient_insurance->insured_zip4;
												$address_flag['general'] = App\Models\AddressFlag::getAddressFlag('patients', @$patient_insurance->id, 'patient_insurance_address');
												//dd($patient_insurance->id);
                                            ?>
                                            {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
                                            {!! Form::hidden('general_address_type','patients',['class'=>'js-address-type']) !!}
                                            {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                            {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                                            {!! Form::hidden('general_address1',@$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                                            {!! Form::hidden('general_city',@$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                                            {!! Form::hidden('general_state',@$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                                            {!! Form::hidden('general_zip5',@$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                                            {!! Form::hidden('general_zip4',@$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                                            {!! Form::hidden('general_is_address_match',@$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                                            {!! Form::hidden('general_error_message',@$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}
                                            {!! Form::hidden('eligibility_verification',@$patient_insurance->eligibility_verification,['class'=>'form-control js-bootstrap-verification','id'=>'insurance_verification']) !!}


                                            <?php
                                            $readonly_class = $disabled_class_male = $disabled_class_female = $disabled_class_other = '';
                                            if (@$patient_insurance->relationship == "Self" || @$patients->is_self_pay == 'Yes') {
                                                $readonly_class = "readonly";
                                                $disabled_class_male = (@$patient_insurance->insured_gender == 'Male') ? '' : 'disabled';
                                                $disabled_class_female = (@$patient_insurance->insured_gender == 'Female') ? '' : 'disabled';
                                                $disabled_class_other = (@$patient_insurance->insured_gender == 'Other') ? '' : 'disabled';
                                            }
                                            
                                            $medicare_insurance_code = Config::get('siteconfigs.medicare_insurance_type_code');
                                            if (in_array(@$patient_insurance->insurance_type_details->code, $medicare_insurance_code) || @$patients->is_self_pay == 'Yes') {
                                                $pat_rel_disabled = 'disabled';
                                            } else {
                                                $pat_rel_disabled = '';
                                            }
                                            ?>
                                            <input id="prev_relationship-{{@$patient_insurance->id}}" value="{{@$patient_insurance->relationship}}" type="hidden">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">                                  
                                                        {!! Form::label('Insured', 'Insured', ['class'=>'control-label']) !!}
                                                        {!! Form::select('relationship',['Self' => 'Self','Spouse' => 'Spouse','Child' => 'Child','Others' => 'Other'],@$patient_insurance->relationship,['class'=>'select2 form-control js-relationship',$pat_rel_disabled,'id'=>'relationship-'.@$patient_insurance->id]) !!}
                                                    </div>                            
                                                </div>
                                            </div>

                                            <?php $insurance_part_dis = (@$patient_insurance->relationship=='Self') ? "hide" : "show";  ?>

                                            <span id="insuredrelation_part-{{@$patient_insurance->id}}" class="{{$insurance_part_dis}}">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 js-bootstrap-lastname-row">                                  
                                                            {!! Form::label('Insured', 'Insured Last Name', ['class'=>'control-label star']) !!}
                                                            {!! Form::text('insured_last_name',$last_name,['autocomplete'=>'nope','class'=>'form-control js-bootstrap-lastname js-letters-caps-format','id'=>'insured_last_name-'.@$patient_insurance->id,$readonly_class]) !!}
                                                        </div> 
                                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 js-bootstrap-firstname-row col-lg-offset-1">                                  
                                                            {!! Form::label('Insured', 'Insured First Name', ['class'=>'control-label star']) !!}
                                                            {!! Form::text('insured_first_name',$first_name,['autocomplete'=>'nope','class'=>'form-control js-bootstrap-firstname js-letters-caps-format','id'=>'insured_first_name-'.@$patient_insurance->id,$readonly_class]) !!}
                                                        </div> 
                                                        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-10">
                                                            {!! Form::label('MI', 'MI', ['class'=>'control-label']) !!}
                                                            {!! Form::text('insured_middle_name',$middle_name,['autocomplete'=>'nope','class'=>'form-control dm-mi js-letters-caps-format','id'=>'insured_middle_name-'.@$patient_insurance->id,$readonly_class]) !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="">
                                                    <div class="col-lg-7 col-md-7 col-sm-5 col-xs-10" id="gender-{{@$patient_insurance->id}}">
                                                        <div class="form-group">
                                                            {!! Form::label('Gender', 'Gender', ['class'=>'control-label star col-lg-12 p-l-0']) !!}
                                                            {!! Form::radio('gender', 'Male',($patient_insurance->insured_gender == "Male") ? true:null,['id'=>$keys.'m','class'=>'',$disabled_class_male,]) !!} {!! Form::label($keys.'m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                                            {!! Form::radio('gender', 'Female',($patient_insurance->insured_gender == "Female") ? true:null,['id'=>$keys.'f','class'=>'',$disabled_class_female,]) !!} {!! Form::label($keys.'f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                                                            {!! Form::radio('gender', 'Other',($patient_insurance->insured_gender != "Male" && $patient_insurance->insured_gender != "Female") ? true:null,['id'=>$keys.'o','class'=>'',$disabled_class_other]) !!} {!! Form::label($keys.'o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                                                        </div> 
                                                    </div>
                                                    <div class="form-group">        
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10" @if(@$registration->insured_ssn !=1) style="display:none;" @endif> 
                                                                {!! Form::label('Insured SSN', 'Insured SSN', ['class'=>'control-label ']) !!}
                                                                {!! Form::text('insured_ssn',$ssn,['class'=>'form-control dm-ssn', 'id' => 'insured_ssn-'.@$patient_insurance->id,'maxlength'=>'9',$readonly_class]) !!} 
                                                            </div>
                                                 
                                                        @if(@$patients->is_self_pay!='Yes')
                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 p-l-0">
                                                            <a id="document_add_modal_link_insured_ssn" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::insurance::'.@$id.'/'.@$patient_encode_id.'/insured_ssn')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} margin-t-20 <?php echo(isset($insurance_ssn->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                                                        </div>
                                                         @endif
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-12 js-insured-dob-part">
                                                <div class="form-group" @if(@$registration->insured_dob !=1) style="display:none;" @endif>
                                                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10" id="gender-{{@$patient_insurance->id}}">                                  
                                                        <i class="fa fa-calendar-o form-icon-demo"></i> 
                                                        {!! Form::label('Insured DOB', 'Insured DOB', ['class'=>'control-label star']) !!}
                                                        {!! Form::text('insured_dob',$dob,['autocomplete'=>'nope' ,'placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js-insurance_dob dm-date','id'=>'insured_dob-'.@$patient_insurance->id,$readonly_class]) !!} 
                                                    </div> 
                                                </div>
                                            </div>
                                            @if(@$patients->is_self_pay!='Yes')
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">                                  
                                                        {!! Form::checkbox('same_as_patient_address', null, (@$patient_insurance->same_patient_address=='yes'?true:null), ['class'=>"js-same_as_patient_address-v2 med-green",'id'=>$keys.'sameaddress']) !!} <label for="{{$keys}}sameaddress" class="med-orange font600">Same as patient address</label> 
                                                    </div> 
                                                </div>
                                            </div>
                                            @endif

                                            <div class="col-lg-12 same_address @if($patient_insurance->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                                <div class="form-group">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('address1')) error @endif">                                  
                                                        {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'control-label star']) !!}
                                                        {!! Form::text('insured_address1',$address1,['autocomplete'=>'nope' ,'maxlength'=>'29','id'=>'insured_address1-'.@$patient_insurance->id,'class'=>'form-control js-address-check js-v2-address1',$ins_rep_readonly_class]) !!}
                                                    </div> 

                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10 col-lg-offset-1 col-md-offset-1">                                  
                                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'control-label']) !!}
                                                        {!! Form::text('insured_address2',$address2,['autocomplete'=>'nope' ,'maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'insured_address2-'.@$patient_insurance->id,$ins_rep_readonly_class]) !!}
                                                    </div> 
                                                </div>
                                            </div>

                                            <div class="col-lg-12 same_address @if($patient_insurance->same_patient_address=='yes') <?php echo 'hide'; ?> @endif">
                                                <div class="form-group">
                                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">                                  
                                                        {!! Form::label('City', 'City', ['class'=>'control-label star']) !!}
                                                        {!! Form::text('insured_city',$city,['autocomplete'=>'nope' ,'maxlength'=>'23','class'=>'form-control js-address-check js-v2-city','id'=>'insured_city-'.@$patient_insurance->id,$ins_rep_readonly_class]) !!}
                                                    </div> 

                                                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">                                  
                                                        {!! Form::label('ST', 'ST', ['class'=>'control-label']) !!}
                                                        {!! Form::text('insured_state',$state,['autocomplete'=>'nope' ,'class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'insured_state-'.@$patient_insurance->id,$ins_rep_readonly_class]) !!}
                                                    </div> 
                                                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10  col-lg-offset-1 col-md-offset-1">                                  
                                                        {!! Form::label('Zip Code', 'Zip Code', ['class'=>'control-label star']) !!}
                                                        {!! Form::text('insured_zip5',$zip5,['autocomplete'=>'nope' ,'class'=>'form-control js-address-check dm-zip5 js-v2-zip5','id'=>'insured_zip5-'.@$patient_insurance->id,'maxlength'=>'5',$ins_rep_readonly_class]) !!}
                                                    </div> 
                                                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">                                  
                                                        {!! Form::label('', '', ['class'=>'control-label']) !!}
                                                        {!! Form::text('insured_zip4',$zip4,['autocomplete'=>'off' ,'class'=>'form-control js-address-check dm-zip4 js-v2-zip4','id'=>'insured_zip4-'.@$patient_insurance->id,'maxlength'=>'4',$ins_rep_readonly_class]) !!}                           
                                                    </div> 
                                                    @if(@$patients->is_self_pay!='Yes')
                                                    <div class="col-md-1 col-sm-2 col-xs-2 p-l-0"> 
                                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form margin-t-22"></i></span>
                                                        <span class="js-address-success margin-t-18 @if($address_flag['general']['is_address_match'] != 'Yes') @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form margin-t-22"></i></a></span>    
                                                        <span class="js-address-error margin-t-18 @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form margin-t-22"></i></a></span>
                                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match'],'css_class_add'); ?>
                                                        <?php echo $value; ?>
                                                    </div>  
                                                    @endif
                                                </div>
                                            </div>

                                        </span>
                                    </div>
                                </div>
                            </div><!-- Left side content Ends-->
                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12" style="background: #f7f7f7">
                                <h4 class="med-green margin-t-20">{{ substr(@$patient_insurance->insurance_details->insurance_name, 0, 25) }} @if(Auth::user()->practice_user_type == 'practice_admin' || Auth::user()->practice_user_type == 'customer')<a href="{{url('insurance/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_insurance->insurance_details->id,'encode').'/edit')}}" target="_blank"><i class="fa fa-edit margin-l-10" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Insurance"></i></a>@endif</h4>
                                <p class="text-justify">

                                    @if(@$patient_insurance->insurance_details->address_1!='' || @$patient_insurance->insurance_details->address_2!='')
                                    @if(@$patient_insurance->insurance_details->address_1!='')
                                    {{ @$patient_insurance->insurance_details->address_1 }}, 
                                    @endif
                                    @if(@$patient_insurance->insurance_details->address_2!='')
                                    {{ @$patient_insurance->insurance_details->address_2 }},
                                    @endif
                                    <br>
                                    @endif

                                    {{ @$patient_insurance->insurance_details->city }} 
                                    @if(@$patient_insurance->insurance_details->state != '') - {{ @$patient_insurance->insurance_details->state }} @endif,

                                    {{ @$patient_insurance->insurance_details->zipcode5 }} 
                                    @if(@$patient_insurance->insurance_details->zipcode4 != '')- {{ @$patient_insurance->insurance_details->zipcode4 }}@endif,
                                    <br>            
                                </p>
                                <p class="line-height-26">
                                    <span class="med-green">Phone : </span> 
                                    <?php $phone_class = (isset($patient_insurance->insurance_details->phone1) && !empty($patient_insurance->insurance_details->phone1))? "js-callmsg-clas cur-pointer": ""?>
                                    <span class="{{$phone_class}}" data-phone= "{{@$patient_insurance->insurance_details->phone1}}" data-user_type="insurance" data-user_id="{{@$patient_insurance->insurance_details->id}}">
                                        @if(@$patient_insurance->insurance_details->phone1){{ @$patient_insurance->insurance_details->phone1 }}
                                        <span class="fa fa-phone-square margin-l-4 med-green" data-placement="bottom"  data-toggle="tooltip" data-original-title="Click"></span> 
                                        @else  - Nil - @endif
                                    </span><br>
                                    <span class="med-green">Fax : </span> @if(@$patient_insurance->insurance_details->fax){{ @$patient_insurance->insurance_details->fax }} @else  - Nil - @endif<br>
                                    <span class="med-green">Email : </span> @if(@$patient_insurance->insurance_details->email){{ @$patient_insurance->insurance_details->email }} @else  - Nil - @endif<br>
                                    <span class="med-green">Insurance Type : </span> 	@if(@$patientinsurance_insurancetype_id){{ @$insurancetypes->$patientinsurance_insurancetype_id }} @else  - Nil - @endif<br>
                                    <span class="med-green">Payer ID : </span> 	@if(@$patient_insurance->insurance_details->payerid){{ @$patient_insurance->insurance_details->payerid }} @else  - Nil - @endif<br>
                                    <span class="med-green">Claim Type : </span> @if(@$patient_insurance->insurance_details->claimtype){{ @$patient_insurance->insurance_details->claimtype }} @else  - Nil - @endif<br>
                                     <span class="med-green">Website: </span>@if(@$patient_insurance->insurance_details->website != '') <a href="{{ @$patient_insurance->insurance_details->website}}" target="_blank">{{ @$patient_insurance->insurance_details->website}}</a> @else - Nil - @endif <br>
                                </p>

                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 p-b-10" style="background: #f7f7f7; margin-top: 30px;">

                                <h4 class="med-darkgray margin-t-20"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Eligibility Reports</h4>
								@if(count((array)$eligibility) > 0)
									
										<?php $count = 0; ?>
										@foreach($eligibility as $list)
											@if($list->insurance_details->insurance_name == $patient_insurance->insurance_details->insurance_name)
												<?php $count++; ?>
												@if($count == 1)
													<table class="popup-table-border table">
													<thead>
														<th class="font600 med-green" style="background: #fff">Date</th>
														<th class="font600 med-green" style="background: #fff">Category</th>
														<th class="font600 med-green" style="background: #fff">Insurance</th>
														<th class="font600 med-green td-c-2" style="background: #fff"></th>
													</thead>
													<tbody>
												@endif
												<tr>
													<td>{{ App\Http\Helpers\Helpers::dateFormat(@$list->created_at,'date')}}</td>
													<td>{{ (!empty($list->edi_filename)) ? 'Eligibility Verification' : 'Benefits Verification' }}</td>
													<td>{{ substr(@$patient_insurance->insurance_details->insurance_name, 0, 25) }}</td>
													<td class="td-c-5 text-center js-prevent-show">
														@if(empty($list->bv_filename))
															 <a  data-unid="{{ $list->patients_id }}" class="js_get_eligiblity_details" data-patientid="{{App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$list->patients_id)}}" data-eligibility="{{App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id,'encode')}}" data-toggle="modal" href="#eligibility_content_popup" ><i class="fa {{Config::get('cssconfigs.patient.file_text')}} cur-pointer" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"></i></a>
														@else
															<a target = "_blank" href= "{{ url('media/patienteligibility') }}/{{ $list->patients_id }}/{{ $list->bv_filename }}"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} cur-pointer" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"></i></a>
														@endif
													</td>
												</tr>
											@endif
										@endforeach
										@if($count == 0)
                                            <tr class="font600">
											No Eligibility Reports </tr>
										@endif
										</tbody>
									</table>
								@else
                                    <span class="font600">No Eligibility Reports</span> 
								@endif
                            </div>


                        </div>
                    </div><!-- /.box-body -->   
                </div>                                
            </div>
        </div>
    </div>

    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 text-center margin-b-23" id="v2-insuranceeditffooter_{{@$patient_insurance->id}}">
        <input data-id="v2-insuranceeditform_{{@$patient_insurance->id}}" class="btn btn-medcubics js-v2-edit-insurance" type="submit" value="Save" {{@$ins_rep_disabled_class}}>
        <a {{@$ins_rep_disabled_class}} class="btn btn-medcubics js-v2-delete-insurance margin-l-10" data-id='{{@$patient_insurance->id}}'> Delete </a>
    </div>
</div>

</div>

</div>

</div><!-- Insurance Box ENds -->
</div><!-- Col 12 Ends -->
{!! Form::close() !!}
@push('view.scripts')
<script type="text/javascript">  
    
</script>
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
@endpush