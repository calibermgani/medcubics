{!! Form::hidden('contact_same_as_address1',@$patients->address1,['class'=>'form-control','id'=>'contact_same_as_address1']) !!}
{!! Form::hidden('contact_same_as_address2',@$patients->address2,['class'=>'form-control','id'=>'contact_same_as_address2']) !!}
{!! Form::hidden('contact_same_as_city',@$patients->city,['class'=>'form-control','id'=>'contact_same_as_city']) !!}
{!! Form::hidden('contact_same_as_state',@$patients->state,['class'=>'form-control','id'=>'contact_same_as_state']) !!}
{!! Form::hidden('contact_same_as_zip5',@$patients->zip5,['class'=>'form-control','id'=>'contact_same_as_zip5']) !!}
{!! Form::hidden('contact_same_as_zip4',@$patients->zip4,['class'=>'form-control','id'=>'contact_same_as_zip4']) !!}
{!! Form::hidden('contact_count_v2',@$contact_count,['class'=>'form-control','id'=>'contact_count_v2']) !!}

<div class="box-body no-padding margin-t-10">
  <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }      
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
        @if($checkpermission->check_url_permission('patients/{id}/edit') == 1)
			@if(@$patients->is_self_pay!='Yes')
				<a class="font600 font14 js-addmore_insurance form-cursor pull-right " accesskey="n" id="addmore" ><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Insurance</a>
			@endif
        @endif
    </div>
    {!! Form::open(['onsubmit'=>"event.preventDefault();", 'id' => 'js-bootstrap-validator1','class' => 'insurance-info-form medcubicsform','name'=>'insurance-info-form']) !!}
    {!! Form::hidden('next_tab',null,['class'=>'form-control','id'=>'next_tab']) !!}
    {!! Form::hidden('is_self_pay_ori_val',@$patients->is_self_pay,['class'=>'form-control','id'=>'is_self_pay_ori_val']) !!}

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" ><!-- Content Starts -->
		<?php 
			$patient_ins_count = App\Models\Patients\PatientInsurance::patientInsCount((array)@$patients->id);
		?>
		{!! Form::hidden('patient_ins_count',@$patient_ins_count,['class'=>'patient_ins_count','id'=>'patient_ins_count']) !!}
        <div class="box box-view no-border no-shadow bg-transparent margin-b-10">           
            <div class="box-body form-horizontal no-border" style="border-color:#85E2E6"><!-- General Info Box Body Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group no-bottom">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h4 class="med-green margin-b-10" style="margin-bottom: 20px; display: inline; margin-right: 30px;"> Choose your responsible party</h4> 
                            <p class="margin-t-15" style="font-size: 15px; display: inline"><span class="demo-checkbox1"> {!! Form::radio('is_self_pay', 'Yes',(@$patients->is_self_pay=='Yes')?true:null,['class'=>' js-is_self_pay','id'=>'r-selfpay']) !!} {!! Form::label('r-selfpay', 'Self Pay',['class'=>'form-cursor font600 med-radiogray']) !!}</span> &emsp; 
                                <span class="demo-checkbox1" style="">{!! Form::radio('is_self_pay', 'No',(@$patients->is_self_pay=='Yes')?null:true,['class'=>' js-is_self_pay','id'=>'r-insurance']) !!} {!! Form::label('r-insurance', 'Insurance',['class'=>' form-cursor font600 med-radiogray']) !!}</span>
							</p>    
                        </div>
                       
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 p-l-0 js-v2-insurance-responsible hide"><span class="btn btn-medcubics-small js-v2-insurance-responsible-btn" data-url={{ url('patients/'.$id.'/archiveinsurance') }} style="min-width:70px;margin-top:5px;padding: 1px 10px 3px 10px;line-height: 26px;font-size: 15px;">Save</span></div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php
			$count = $insurance_count = count((array)@$patient_insurances);
			if ($count > 0)
				$insurance_count = $count - 1;
			else
				$insurance_count = $count;

			if ($count > 0 && $addmore == 'more')
				$insurance_count = $insurance_count + 1;
        ?>
        
        @if($count > 0)
        
        <div class="box box-view no-border no-shadow margin-b-5">
              
                <div class="box-body no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Coverage Details & Insurance Starts -->                       
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Insurance Col Starts -->
                            <div class="box box-view no-shadow no-border-radius no-bottom"><!-- Insurance Box Starts -->

                                <div class="box-body table-responsive p-b-0"><!-- Insurance box body starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive  no-padding">
                                        <table id="" class="table table-bordered table-separate l-green-b"> 
                                            <thead>
                                                <tr>
                                                    <th class="td-c-1 med-l-bg"></th>
                                                    <th class="med-l-bg med-green font600">Insurance</th>
                                                    <th class="med-l-bg med-green font600">Policy ID</th>
                                                    <th class="med-l-bg med-green font600">Effective Date</th>
                                                    <th class="med-l-bg med-green font600">Termination Date</th> 
                                                    <th class="med-l-bg med-green font600">Status</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
												@foreach(@$patient_insurances as $keys=>$patient_insurance)
												<tr>    
													<?php
													if(@$patient_insurance->category=='Primary') {
														$cat_bg_clr = "pri-bg";
													} elseif(@$patient_insurance->category=='Secondary') {
														$cat_bg_clr = "sec-bg";
													} elseif(@$patient_insurance->category=='Tertiary') {
														$cat_bg_clr = "ter-bg";
													} else {
														$cat_bg_clr = "pri-bg";
													}
													?>
													<td><input type="checkbox" name="pat_inslist_name[]" value="{{@$patient_insurance->id}}" id='{{$keys}}' class="" <?php if(@$patients->is_self_pay=='Yes'){ echo 'disabled="disabled"'; } ?> ><label for="{{$keys}}" class="no-bottom med-darkgray">&nbsp;</label></td>
													<td>
														<span class="{{$cat_bg_clr}}">{{ substr(@$patient_insurance->category, 0, 1) }}</span>
														{{ substr(@$patient_insurance->insurance_details->insurance_name, 0, 25) }}
													</td>
													<td>{{ @$patient_insurance->policy_id }}</td>                               
													<td>
														@if(@$patient_insurance->effective_date!='' && @$patient_insurance->effective_date!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$patient_insurance->effective_date,'dob')}} @else - @endif
													</td>
													<td>
														@if(@$patient_insurance->termination_date!='' && @$patient_insurance->termination_date!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$patient_insurance->termination_date,'dob')}} @else - @endif
													</td>
													<td>
														<?php  
															$date = \App\Http\Helpers\Helpers::timezone(date("Y-m-d H:i:s"),'Y-m-d'); 
															if(@$patient_insurance->effective_date =="0000-00-00" && @$patient_insurance->termination_date =="0000-00-00") {
																$status = "-";
															} elseif(strtotime($date) <= strtotime(@$patient_insurance->termination_date)) {
																$status = "Active";
															} elseif(@$patient_insurance->effective_date != '' && @$patient_insurance->termination_date =="0000-00-00") {
																$status = "Active";
															} else {
																$status = "Inactive";
															}															
														?>
														{{ $status }}
													</td>
												</tr>												
												@endforeach												
											</tbody>
                                        </table>    
                                    </div> 

                                </div><!-- Insurance /.box-body Ends -->
                            </div><!-- Insurance /.box Ends -->
                        </div><!-- Insurance Col Ends -->
                    </div><!-- Coverage Details & Insurance Ends -->

                </div>
            </div>
        
        @endif
 
    </div>
    {!! Form::close() !!}

    <div id="js-allow_add_insurance">
                    
        {!! Form::hidden('insurance_count',$insurance_count,['class'=>'form-control','id'=>'insurance_count']) !!}
        {!! Form::hidden('primary_ins_id',$primary_ins_id,['class'=>'form-control','id'=>'primary_ins_id']) !!}
        {!! Form::hidden('secondary_ins_id',$secondary_ins_id,['class'=>'form-control','id'=>'secondary_ins_id']) !!}
        {!! Form::hidden('tertiary_ins_id',$tertiary_ins_id,['class'=>'form-control','id'=>'tertiary_ins_id']) !!}
        {!! Form::hidden('workerscomp_ins_id',$workerscomp_ins_id,['class'=>'form-control','id'=>'workerscomp_ins_id']) !!}
        {!! Form::hidden('autoaccident_ins_id',$autoaccident_ins_id,['class'=>'form-control','id'=>'autoaccident_ins_id']) !!}
        {!! Form::hidden('attorney_ins_id',$attorney_ins_id,['class'=>'form-control','id'=>'attorney_ins_id']) !!}

        <div id="mybox"></div> 
        @if($count > 0)
        @foreach(@$patient_insurances as $keys=>$patient_insurance)
        <?php $count = $count - 1; ?>
        @include('patients/patients/insurance-form',['patient_insurance' => $patient_insurance, 'cur_count' => $count])  
        @endforeach
        @endif
    </div>

    {!! Form::hidden('current_tab','insurance-info',['class'=>'form-control']) !!}  
    <input id="self_last_name" type="hidden" value="{{@$patients->last_name}}">
    <input id="self_first_name" type="hidden" value="{{@$patients->first_name}}">
    <input id="self_middle_name" type="hidden" value="{{@$patients->middle_name}}">
    <input id="self_ssn" type="hidden" value="{{@$patients->ssn}}">
    <input id="self_gender" type="hidden" value="{{@$patients->gender}}">
    <input id="self_dob" type="hidden" value="{{ date("m/d/Y", strtotime(@$patients->dob)) }}">
    <input id="self_address1" type="hidden" value="{{@$patients->address1}}">
    <input id="self_address2" type="hidden" value="{{@$patients->address2}}">
    <input id="self_city" type="hidden" value="{{@$patients->city}}">
    <input id="self_state" type="hidden" value="{{@$patients->state}}">
    <input id="self_zip5" type="hidden" value="{{@$patients->zip5}}">
    <input id="self_zip4" type="hidden" value="{{@$patients->zip4}}">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">           
        <a href="javascript:void(0);" id="demo" class="js_arrow pull-left">{!! Form::button('<<', ['class'=>'btn btn-medcubics']) !!} </a></center>
		<?php /*
        <!--{!! Form::submit('Save', ['class'=>'btn btn-medcubics form-group']) !!}-->
		*/ 
		$currnet_page = Route::getFacadeRoot()->current()->uri(); 
		/*
        <!--<a href="javascript:void(0)" data-url="{{ url('patients/'.$id.'#insurance-info') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>-->
		*/ ?>
        <a href="javascript:void(0);" class="js_arrow js-next-tab pull-right" @if(@$selectbox =='') id="authorization" @else id="contact" @endif"> {!! Form::button('>>', ['class'=>'btn btn-medcubics']) !!} </a></center>
    </div>

</div>  <!--- End Insurance -->                  

<div id="add_new_insurance" class="modal fade in" data-keyboard="false"></div>
<div class="js_add_new_ins_form hide">
    <div class="modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Insurance</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'name'=>'v2_insurance_form','id'=>'js-bootstrap-validator-insurance','class'=>'policy-check-text v2-insurance-info-form popupmedcubicsform js-v2-common-info-form']) !!}
                <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.insurance") }}' />
                <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->


                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class" id="js-address-general-address">

                            <div class="form-group margin-b-10">
                                {!! Form::label('Category', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10 @if($errors->first('category')) error @endif">
                                    {!! Form::select('category', [''=>'-- Select --','Primary' => 'Primary','Secondary' => 'Secondary','Tertiary' => 'Tertiary','Workers Comp' => 'Workers Comp','Auto Accident' => 'Auto Accident','Attorney' => 'Attorney','Others'=>'Others'],null,['class'=>'select_2 form-control js_select_category_class js-popup-category-selection', 'id' => 'js-category']) !!}
                                </div>
                            </div>

                            <div class="form-group margin-b-10">
                                {!! Form::label('insurance name', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">  
                                    {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$insurances,  null,['class'=>'select_2 form-control insurance_id js-sel-modalinsurance-address v2-js-insurance-change','id'=>'insurance_id-0']) !!} 
                                </div>

                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2  p-l-0">
                                    <a href="#js-model-insurance-details" data-toggle="modal" data-target="#js-model-insurance-details"  tabindex="-1"><i class="fa fa-search icon-green-form"></i></a>
                                </div>

                            </div>
							<?php /*
                            <!-- <div class="form-group margin-b-10">
                                {!! Form::label('Insurance Type', 'Insurance Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('type')) error @endif">
                                    {!! Form::select('category1', [''=>'-- Select --','Medicare' => 'Medicare','Medcaid' => 'Medcaid','Commercial' => 'Commercial','Others'=>'Others'],null,['class'=>'select2 form-control js_select_category_class']) !!}
                                </div>
                            </div> -->
							*/ ?>
                            <div class="js-add-new-select hide" id="js-insurance-type-0">
                                <div class="form-group margin-b-10 js_common_ins">
                                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::select('insurancetype_id', array('' => '-- Select --')+(array)$insurancetypes,  null,['class'=>'form-control select_2 js-add-new-select-opt', 'id'=>'insurancetype_id-0']) !!}
                                    </div>
                                    <div class="col-sm-1 col-xs-2"></div>
                                </div> 
                                <div class="form-group hide" id="add_new_span">
                                    {!! Form::label('InsuranceType', 'Insurance Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::text('newadded',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Insurance Type','data-table-name'=>'insurancetypes','data-field-name'=>'type_name','data-field-id'=>'','data-label-name'=>'insurance type']) !!}
                                        <p class="js-error help-block hide"></p>
                                        <p class="pull-right no-bottom">
                                            <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                                            <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="js_policyid_chk_0" />
                            <input type="hidden" id="js_insurancetype_chk_0" value="" />
                            <div class="form-group margin-b-10 js_medicareins_0 hide">
                                {!! Form::label('MedicareType', 'If Medicare Secondary ?', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}   
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::select('medical_secondary_code', array('' => '-- Select --')+(array)$medical_secondary_list,NULL,['class'=>'form-control','data-id'=>0]) !!}
                                    {!! $errors->first('medical_secondary_code', '<p> :message</p>')  !!}  
                                </div>
                                <div class="col-sm-1 col-xs-2"></div>
                            </div>
                            <div class="form-group margin-b-10">
                                {!! Form::label('policy_id', 'Policy ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 js-bootstrap-policyid-row">
                                    {!! Form::text('policy_id',null,['maxlength'=>'29','class'=>'js_no_space form-control policy-check dm-policy-id js-bootstrap-policyid js-all-caps-letter-format', 'id'=>'policy_id-0', 'autocomplete'=>'nope']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-10">
                                    <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray" data-page="pat_ins" style="display:none; width: 7px;"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-gray font10"></i></a> 
                                    <i class="fa fa-spinner fa-spin eligibilityloadingimg font11" style="display:none;"></i>

                                    <a class="js_get_eligiblity_details js_eliactive" title="Eligibility Details" data-page="pat_ins" style="display:none; width: 7px;" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-green font10"></i></a>

                                    <a class="js_get_eligiblity_details js_eliinactive" title="Eligibility Details" data-page="pat_ins" style="display:none; width: 7px;" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-red font10"></i></a>
                                </div>                               
                            </div>

                            <div class="form-group margin-b-10 " @if(@$registration->group_name_id !=1) style="display:none;" @endif>
                                 {!! Form::label('group_name', 'Group Name / ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                 <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::text('group_name',null,['class'=>'form-control js-all-caps-letter-format dm-group-id', 'autocomplete'=>'nope']) !!}
                                </div>
                            </div>                           

                            <div class="form-group margin-b-10 hide" @if(@$registration->adjustor_ph !=1) style="display:none;" @endif>
                                 {!! Form::label('adjustor_ph', 'Adjustor Ph', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                 <div class="col-lg-5 col-md-6 col-sm-7 col-xs-10">
                                    {!! Form::text('adjustor_ph',null,['class'=>'form-control js-phone dm-phone','id'=>'phone']) !!}
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>    
                  
                            <div class="form-group margin-b-10 hide" @if(@$registration->adjustor_fax !=1) style="display:none;" @endif>
                                 {!! Form::label('adjustor_fax', 'Adjustor Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                 <div class="col-lg-5 col-md-6 col-sm-7 col-xs-10">
                                    {!! Form::text('adjustor_fax',null,['class'=>'form-control js-fax dm-fax','id'=>'fax']) !!}
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>

                            <input id="prev_relationship-0" type="hidden">
                            <div class="form-group margin-b-10">
                                {!! Form::label('Relationship', 'Insured ', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                <div class="col-lg-5 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::select('relationship', ['Self' => 'Self','Spouse' => 'Spouse','Child' => 'Child','Others '=>'Other'],null,['class'=>'select_2 form-control js-relationship','id'=>'relationship-0']) !!}
                                </div>
                            </div>                        

                            <span id="insuredrelation_part-0" class="hide">
                                <div class="form-group margin-b-10">
                                    {!! Form::label('LastName', 'Insured Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 js-bootstrap-lastname-row">
                                        {!! Form::text('insured_last_name',@$patients->last_name,['class'=>'form-control js-bootstrap-lastname js-letters-caps-format','id'=>'insured_last_name-0','readonly','autocomplete'=>'nope']) !!}
                                    </div>
                                </div>
                                <div class="form-group margin-b-10">
                                    {!! Form::label('First Name', 'Insured First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
                                        {!! Form::text('insured_first_name',@$patients->first_name,['class'=>'form-control js-bootstrap-firstname js-letters-caps-format','id'=>'insured_first_name-0','readonly','autocomplete'=>'nope']) !!}
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 js-bootstrap-firstname-row">
                                        {!! Form::text('insured_middle_name',@$patients->middle_name,['class'=>'form-control dm-mi js-letters-caps-format','id'=>'insured_middle_name-0','placeholder'=>'MI','readonly','autocomplete'=>'nope']) !!}
                                    </div>
                                </div>

                                <div class="form-group margin-b-10">
                                    {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10" id="gender-0">
                                        <?php
											$disabled_class_male     = (@$patients->gender == 'Male')?'':'disabled';
											$disabled_class_female   = (@$patients->gender == 'Female')?'':'disabled';
											$disabled_class_other    = (@$patients->gender == 'Others')?'':'disabled';
										?>
                                        {!! Form::radio('gender', 'Male',(@$patients->gender == 'Male')?true:null,['class'=>'',$disabled_class_male,'id'=>'c-male']) !!} {!! Form::label('c-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                        {!! Form::radio('gender', 'Female',(@$patients->gender == 'Female')?true:null,['class'=>'',$disabled_class_female,'id'=>'c-female']) !!} {!! Form::label('c-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                        {!! Form::radio('gender', 'Other',(@$patients->gender == 'Others')?true:null,['class'=>'',$disabled_class_other,'id'=>'c-others']) !!} {!! Form::label('c-others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                                    </div>                              
                                </div>

                                <div class="form-group margin-b-10" @if(@$registration->insured_ssn !=1) style="display:none;" @endif>
                                     {!! Form::label('insured_ssn', 'Insured SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                     <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10">
                                        {!! Form::text('insured_ssn',@$patients->ssn,['class'=>'form-control dm-ssn','id'=>'insured_ssn-0','maxlength'=>'9','readonly']) !!} 
                                    </div>
                                </div>

                                <div class="form-group margin-b-10 js-insured-dob-part" @if(@$registration->insured_dob !=1) style="display:none;" @endif>
                                     {!! Form::label('insured_dob', 'Insured DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-10 control-label star']) !!} 

                                     <div class="col-lg-5 col-md-6 col-sm-5 col-xs-10 @if($errors->first('insured_dob')) error @endif">
                                        <i class="fa fa-calendar-o form-icon"></i> 
                                        {!! Form::text('insured_dob',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patients->dob),['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js-insurance_dob dm-date','id'=>'insured_dob-0','readonly', 'autocomplete'=>'nope']) !!} 
                                    </div>
                                </div>

                                {!! Form::hidden('general_address_type','patients',['class'=>'js-address-type']) !!}
                                {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                                {!! Form::hidden('general_address_type_category','patient_contact_address',['class'=>'js-address-type-category']) !!}
                                {!! Form::hidden('general_address1',null,['class'=>'js-address-address1']) !!}
                                {!! Form::hidden('general_city',null,['class'=>'js-address-city']) !!}
                                {!! Form::hidden('general_state',null,['class'=>'js-address-state']) !!}
                                {!! Form::hidden('general_zip5',null,['class'=>'js-address-zip5']) !!}
                                {!! Form::hidden('general_zip4',null,['class'=>'js-address-zip4']) !!}
                                {!! Form::hidden('general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
                                {!! Form::hidden('general_error_message',null,['class'=>'js-address-error-message']) !!}

                                <div class="form-group margin-b-10">
                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12 control-label"></div>
                                    <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                        {!! Form::checkbox('same_as_patient_address', null, true, ['class'=>"js-same_as_patient_address-v2 med-green",'id'=>'sameaddress-insurance']) !!} <label for="sameaddress-insurance" class="med-orange font600">Same as patient address</label>  
                                    </div>
                                </div>

                                <div class="form-group margin-b-10 same_address">
                                    {!! Form::label('address1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address1')) error @endif">
                                        {!! Form::text('insured_address1',@$patients->address1,['maxlength'=>'29','class'=>'form-control js-address-check js-v2-address1','id'=>'insured_address1-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div> 

                                <div class="form-group margin-b-10 same_address">
                                    {!! Form::label('address2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                            
                                        {!! Form::text('insured_address2',@$patients->address2,['maxlength'=>'50','class'=>'form-control js-address2-tab js-v2-address2','id'=>'insured_address2-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2"></div>
                                </div>           

                                <div class="form-group margin-b-10 same_address">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                                        {!! Form::text('insured_city',@$patients->city,['maxlength'=>'23','class'=>'form-control js-address-check js-v2-city','id'=>'insured_city-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label star']) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 p-l-0"> 
                                        {!! Form::text('insured_state',@$patients->state,['class'=>'form-control js-all-caps-letter-format js-address-check js-state-tab js-v2-state','maxlength'=>'2','id'=>'insured_state-0','autocomplete'=>'nope']) !!}
                                    </div>
                                </div>   
                                <div class="form-group margin-b-10 same_address">
                                    {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zipcode5')) error @endif ">
                                        {!! Form::text('insured_zip5',@$patients->zip5,['class'=>'form-control js-address-check dm-zip5 js-v2-zip5','maxlength'=>'5','id'=>'insured_zip5-0','autocomplete'=>'nope']) !!}
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 @if($errors->first('zipcode4')) error @endif ">
                                        {!! Form::text('insured_zip4',@$patients->zip4,['class'=>'form-control js-address-check dm-zip4 js-v2-zip4','maxlength'=>'4','id'=>'insured_zip4-0','autocomplete'=>'nope']) !!}
                                    </div>

                                    <div class="col-md-1 col-sm-2 col-xs-2">            
                                        <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                        <span class="js-address-success hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                        <span class="js-address-error hide"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view(@$address_flag['general']['is_address_match']); ?>
                                        <?php echo $value; ?>
                                    </div> 
                                </div>

                            </span>

                            <div class="form-group margin-b-10">
                                {!! Form::label('effective_date_label', 'Effective Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                                    <i class="fa fa-calendar-o form-icon"></i>                      
                                    {!! Form::text('effective_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js_datepicker dm-date','id'=>'effective_date-0', 'autocomplete'=>'nope']) !!}  
                                </div>
                            </div>

                            <div class="form-group margin-b-10">
                                {!! Form::label('termination_date_label', 'Termination Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                                        
                                    <i class="fa fa-calendar-o form-icon"></i> 
                                    {!! Form::text('termination_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control form-cursor js_datepicker dm-date','id'=>'termination_date-0', 'autocomplete'=>'nope']) !!}
                                </div>
                            </div>

                        </div>

                    </div><!-- /.box-body -->   
                </div><!-- /.box Ends Contact Details-->

                <div id="insurance-info-footer" class="modal-footer">
                    <input id="js-insuranceform-submit-button-v2" accesskey="s" data-id="js-bootstrap-validator-insurance" class="btn btn-medcubics-small" type="submit" value="Save">
                    <button class="btn btn-medcubics-small" data-dismiss="modal" type="button">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div><!-- Modal Light Box Ends --> 
<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>
@push('view.scripts')
{!! HTML::script('js/address_check.js') !!}
<script type="text/javascript">
    // disableAutoFill('#js-bootstrap-validator-insurance');
    $("js-address-check").trigger("blur");

	<?php if($get_default_timezone){ ?> 
	var get_default_timezone = '<?php echo $get_default_timezone; ?>';
    <?php }else{?>
    var get_default_timezone = '';
	<?php }?>
	
	$(document).on("keypress", ".js_no_space", function (e) { 
		if (e.keyCode == 32) // 32 is the ASCII value for a space
			e.preventDefault();
	});
	if ($("div").hasClass("js-add-new-select")) {
		$("div.js-add-new-select").find('select:not("#newadded_cms_type")').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
	}

    $(document).on('change', '.js-add-new-select-opt', function (event) {
		// console.log('1714');
		//$('.js-add-new-select-opt').change(function(){
		var current_divid = $(this).parents('div.js-add-new-select').attr('id');
		var selected_value = $(this).val();
		$('#' + current_divid).find('p.js-error').html('').removeClass('show').addClass('hide');
		if (selected_value == '0') {
			$(this).closest('.js_common_ins').addClass('hide');
			$('#' + current_divid).children("#add_new_span").removeClass('hide').addClass('show');
			$('#' + current_divid).find('#newadded').val('');
			$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		} else {
			$("#add_new_span").removeClass('show').addClass('hide');
			$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
		}
	});

	$(document).on('keyup', '#newadded', function () {
		// console.log('1730');
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		if ($(this).val() != null) {
			var seldivid = $(this).parents('div.js-add-new-select').attr('id');
			$('#' + seldivid).find('p.js-error').removeClass('show').addClass('hide');
		}
	});

	$(document).on("click", 'div.js-add-new-select #add_new_save', function () {
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
		//$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_save').click(function(){
		var lblname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-label-name');
		var insurance_type = $(this).parents('div.js-add-new-select').find("#newadded").val();
		var regex = new RegExp("^[a-zA-Z ]+$");
		if (!insurance_type || !regex.test(insurance_type)) {
			$(this).parents('div.js-add-new-select').find("#newadded").parent('div').addClass('has-error');
			$(this).parents('div.js-add-new-select').find('p.js-error').html('');
			if (!insurance_type) {
				$(this).parents('div.js-add-new-select').find('p.js-error').html(insurancetype + ' ' + lblname);
			} else {
				$(this).parents('div.js-add-new-select').find('p.js-error').html(only_alpha_lang_err_msg);
			}
			$(this).parents('div.js-add-new-select').find('p.js-error').removeClass('hide').addClass('show');
		} else {
			$(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
			var tablename = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-table-name');
			var fieldname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-field-name');
			var addedvalue = $(this).parents('div.js-add-new-select').find('#newadded').val();
			var seldivid = $(this).parents('div.js-add-new-select').attr('id');
			var pars = 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue;

			if (seldivid == 'js-insurance-type' && $('#newadded_cms_type').length) {
				var insCmsType = $(this).parents('div.js-add-new-select').find('#newadded_cms_type').val();
				pars = pars + '&cms_type=' + insCmsType;
			}

			var value = addedvalue.trim();
			var changed_string = value.toLowerCase();
			if (changed_string != 'App' && changed_string != "app") {
				url_path = (window.location.pathname).split("/");
				if (url_path[2] == 'templates') {
					$.ajax({
						url: api_site_url + '/addnewselect',
						type: 'get',
						data: pars,
						success: function (data) {
							if (data == '2') {
								$('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
								$('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
								$('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
								$('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added                            
							} else {
								$('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
								$('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
								$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
								getoptionvalues(tablename, fieldname, seldivid, addedvalue);
							}
						},
						error: function (jqXhr, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					});
				}//Template if
			}//App if

			$.ajax({
				url: api_site_url + '/addnewselect',
				type: 'get',
				data: pars,
				success: function (data) {
					if (data == '2') {
						$('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
						$('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
						$('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
						$('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added

					} else {
						//$("#add_new_span").removeClass('show').addClass('hide');                  
						//$('.js_common_ins').removeClass('hide').addClass('show');

						$('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
						$('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
						$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
						getoptionvalues(tablename, fieldname, seldivid, addedvalue);
					}
				},
				error: function (jqXhr, textStatus, errorThrown) {
					console.log(errorThrown);
				}
			});
		}

		if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
			$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
			$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
			var hold_reason_val = $('input[name="hold_reason_exist"]').val();
			setTimeout(function () {
				if ($('input[name="other_reason"]').val() != '' && !hold_reason_val) {
					$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', false);
					$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
				} else {
					$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', true);
					$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
				}
			}, 500);
		}
	});

	$(document).on("click", "div.js-add-new-select #add_new_cancel", function () {
		//$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_cancel').click(function(){        
		$(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
		$(this).parents('div.js-add-new-select').find("#add_new_span").removeClass('show').addClass('hide');
		var seldivid = $(this).parents('div.js-add-new-select').attr('id');
		$(this).parents('#' + seldivid).find('.js-add-new-select-opt').closest('.js_common_ins').removeClass('hide').addClass('show');
		$(this).parents('#' + seldivid).find('.js-add-new-select-opt').select2("val", "");
		if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
			$('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
			$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
		}
		$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
	});

	function getoptionvalues(tablename, fieldname, seldivid, addedvalue) {
		$.ajax({
			type: "GET",
			url: api_site_url + '/getoptionvalues',
			data: 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue,
			success: function (data) {
				$('#' + seldivid).find("select.js-add-new-select-opt").html(data);
				if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-hold-reason') {
					$('#js-hold-reason').change();
				} else {
					$('#' + seldivid).find("select.js-add-new-select-opt").select2();
				}
			}
		});
	}
</script>
@endpush