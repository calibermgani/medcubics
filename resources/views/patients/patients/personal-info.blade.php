<?php 
	if(!isset($get_default_timezone)){
	   $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
	}  
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">

        <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Personal Details</h4>

        <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 bg-white no-padding">
                <!-- Personal Info Starts -->            
                <div class="box box-view no-bottom no-border-radius no-shadow p-b-8">                    
                    <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>
                    @if($current_page == 'patients/create')
                    {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
                    @endif

                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.demographics") }}' />

                    <div class="js-address-class"  id="js-address-personal-info-address">
                        {!! Form::hidden('pia_address_type','patients_personal',['class'=>'js-address-type']) !!}
                        {!! Form::hidden('pia_address_type_id',@$patients->id,['class'=>'js-address-type-id']) !!}
                        {!! Form::hidden('pia_address_type_category','personal_info_address',['class'=>'js-address-type-category']) !!}
                        {!! Form::hidden('pia_address1',@$address_flag['pia']['address1'],['class'=>'js-address-address1']) !!}
                        {!! Form::hidden('pia_city',@$address_flag['pia']['city'],['class'=>'js-address-city']) !!}
                        {!! Form::hidden('pia_state',@$address_flag['pia']['state'],['class'=>'js-address-state']) !!}
                        {!! Form::hidden('pia_zip5',@$address_flag['pia']['zip5'],['class'=>'js-address-zip5']) !!}
                        {!! Form::hidden('pia_zip4',@$address_flag['pia']['zip4'],['class'=>'js-address-zip4']) !!}
                        {!! Form::hidden('pia_is_address_match',@$address_flag['pia']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                        {!! Form::hidden('pia_error_message',@$address_flag['pia']['error_message'],['class'=>'js-address-error-message']) !!}

                        <div class="box-body form-horizontal bg-white margin-t-5 no-padding">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="col-lg-12 p-l-0">
                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 @if($errors->first('last_name')) error @endif">
                                            {!! Form::label('Last Name', 'Last Name', ['class'=>'control-label star']) !!}
                                            {!! Form::text('last_name',null,['class'=>'form-control   js-letters-caps-format','id'=>'last_name','maxlength'=>'50','autocomplete'=>'nope','tabindex'=>'1']) !!}
                                            {!! $errors->first('last_name', '<p> :message</p>')  !!}        
                                        </div>                                                        
                                    </div>    
                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('middle_name')) error @endif">
                                            {!! Form::label('MI', 'MI', ['class'=>'control-label']) !!}
                                            {!! Form::text('middle_name',null,['maxlength'=>'1','class'=>'form-control js-letters-caps-format','id'=>'middle_name','tabindex'=>'3','autocomplete'=>'nope']) !!}
                                            {!! $errors->first('middle_name', '<p> :message</p>')  !!}      
                                        </div> 

                                        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('title')) error @endif">
                                            {!! Form::label('Title', 'Title', ['class'=>'control-label']) !!}
                                            {!! Form::select('title', [''=>'-- Select --','Mr' => 'Mr','Mrs' => 'Mrs','Ms' => 'Ms','Sr'=>'Sr','Jr'=>'Jr'],null,['class'=>'select2 form-control','tabindex'=>'3']) !!}
                                            {!! $errors->first('title', '<p> :message</p>')  !!}
                                        </div>                               
                                    </div>
                                    <div class="form-group margin-b-15">
                                        @if(strpos($current_page, 'edit') !== false)
											<?php
												$patients->dob = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patients->dob,'1901-01-01','');
												$patients->deceased_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patients->deceased_date,'1901-01-01','');
											?>
                                        @endif

                                        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10">
                                            <!-- <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i> -->
                                            {!! Form::label('DOB', 'DOB', ['class'=>'control-label star']) !!}
                                            {!! Form::text('dob',(isset($patients->dob)?date('m/d/Y',strtotime($patients->dob)):null),['autocomplete'=>'nope','id'=>'txtAge','class'=>'form-control form-cursor txtAge dm-date','tabindex'=>'6','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                            <span id="lblAge" class="patient-validation"></span>
                                            <span id="lblError"></span>  
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                                            {!! Form::label('Age', 'Age', ['class'=>'control-label']) !!}
                                            {!! Form::text('age',null,['id'=>'age','readonly','class'=>'form-control','tabindex'=>'-1']) !!} 
                                        </div>                                
                                    </div>
                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-9 col-md-9 col-sm-6 col-xs-10 @if($errors->first('address1')) error @endif">
                                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'control-label star']) !!}
                                            {!! Form::text('address1',null,['maxlength'=>'28','id'=>'address1','class'=>'form-control   js-address-check','tabindex'=>'8','autocomplete'=>'nope']) !!}
                                            {!! $errors->first('address1', '<p> :message</p>')  !!}    
                                        </div>                                                            
                                    </div>
                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                            {!! Form::label('City', 'City', ['class'=>'control-label star']) !!}
                                            {!! Form::text('city',null,['maxlength'=>'24','class'=>'form-control   js-address-check','id'=>'city','tabindex'=>'10','autocomplete'=>'nope']) !!}
                                        </div> 

                                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                            {!! Form::label('State', 'ST', ['class'=>'control-label star']) !!}
                                            {!! Form::text('state',null,['class'=>'form-control js-all-caps-leter-format js-state-tab js-address-check', 'maxlength'=>'2','id'=>'state','tabindex'=>'11','autocomplete'=>'nope']) !!}   
                                        </div>
                                    </div>

                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-9 col-md-9 col-sm-6 col-xs-10 @if($errors->first('mobile')) error @endif">
                                            {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'control-label']) !!}
                                            {!! Form::text('mobile',null,['class'=>'form-control   dm-phone','id'=>'mobile','tabindex'=>'14','autocomplete'=>'nope']) !!}
                                            {!! $errors->first('mobile', '<p> :message</p>')  !!}       
                                        </div>                        
                                    </div>

                                    <div class="form-group margin-b-15">
                                        @if(@$registration->email_id ==1)
                                        <div class="col-lg-9 col-md-9 col-sm-6 col-xs-10 @if($errors->first('email')) error @endif">
                                            {!! Form::label('Email', 'Email', ['class'=>'control-label']) !!}
                                            {!! Form::text('email',null,['id'=>'js_email_demo','class'=>'form-control','tabindex'=>'16','autocomplete'=>'nope']) !!}
                                        </div> 
                                        @endif                                
                                    </div>                        
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1">
                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-6 col-xs-10 @if($errors->first('first_name')) error @endif">
                                        {!! Form::label('First Name', 'First Name', ['class'=>'control-label star']) !!}
                                        {!! Form::text('first_name',null,['class'=>'form-control   js-letters-caps-format','id'=>'first_name','maxlength'=>'50','autocomplete'=>'nope','tabindex'=>'2']) !!}
                                        {!! $errors->first('first_name', '<p> :message</p>')  !!}        
                                    </div>                         
                                </div>

                                <div class="form-group margin-b-15">
                                     @if(isset($patients->id))
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 @if($errors->first('gender')) error @endif">
                                      <label for="gender" class="control-label col-lg-12 no-padding star">Gender</label>
                                      
                                      <input name="gender" id="gender_m" class = "gender" type="radio" value="Male" <?php if($patients->gender == 'Male'){echo 'checked';} ?> data-bv-field="gender" tabindex="4">
                                      <label for="gender_m" class="med-darkgray font600 form-cursor">Male &emsp;</label>
                                      
                                      <input name="gender" id="gender_f" type="radio" value="Female" <?php if($patients->gender == 'Female'){echo 'checked';} ?> class="gender" data-bv-field="gender" tabindex="4">
                                      <label for="gender_f" class="med-darkgray font600 form-cursor">Female &emsp;</label>
                                      
                                      <input name="gender" id="gender_o" type="radio" value="Others" <?php if($patients->gender == 'Others'){echo 'checked';} ?> class="gender" data-bv-field="gender" tabindex="4"><i class="form-control-feedback" data-bv-icon-for="gender" style="display: none;"></i>
                                      <label for="gender_o" class="med-darkgray font600 form-cursor">Others</label>
                                       {!! $errors->first('gender', '<p> :message</p>')  !!} 
                                    </div>   
                                    @else
                                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 @if($errors->first('gender')) error @endif">
										  <label for="gender" class="control-label col-lg-12 no-padding star">Gender</label>
										  
										  <input name="gender" id="gender_m" class = "gender" type="radio" value="Male" data-bv-field="gender" tabindex="4">
										  <label for="gender_m" class="med-darkgray font600 form-cursor">Male &emsp;</label>
										  
										  <input name="gender" id="gender_f" type="radio" value="Female" class="gender" data-bv-field="gender" tabindex="4">
										  <label for="gender_f" class="med-darkgray font600 form-cursor">Female &emsp;</label>
										  
										  <input name="gender" id="gender_o" type="radio" value="Others" class="gender" data-bv-field="gender" tabindex="4"><i class="form-control-feedback" data-bv-icon-for="gender" style="display: none;"></i>
										  <label for="gender_o" class="med-darkgray font600 form-cursor">Others</label>
										   {!! $errors->first('gender', '<p> :message</p>')  !!}
									   </div>
                                    @endif                      
                                </div>
                                @if(isset($patients->id))
                                  <input type="hidden" name="patient_id" value='{{$patients->id}}' /> 
                                @endif  
                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-6 col-xs-10 @if($errors->first('ssn')) error @endif">
                                        {!! Form::label('Social Security Number', 'Social Security Number', ['class'=>'control-label col-lg-12 no-padding']) !!}
                                        {!! Form::text('ssn',null,['class'=>'form-control   dm-ssn','tabindex'=>'7','autocomplete'=>'nope']) !!}
                                        {!! $errors->first('ssn', '<p> :message</p>') !!}  
                                    </div>    
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 p-l-0 margin-t-18">                            
                                        <a id="document_add_modal_link_ssn" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/patients/'.@$id.'/ssn')}}" @else data-url="{{url('api/adddocumentmodal/patients/0/ssn')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_ssn->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                                    </div>
                                </div>

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                        {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'control-label']) !!}
                                        {!! Form::text('address2',null,['maxlength'=>'50','class'=>'form-control   js-address2-tab','tabindex'=>'9','autocomplete'=>'nope']) !!}    
                                    </div>                            
                                </div>

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('zipcode5')) error @endif">
                                        {!! Form::label('Zip Code', 'Zip Code', ['class'=>'control-label star']) !!}
                                        {!! Form::text('zip5',null,['class'=>'form-control dm-zip5 js-address-check', 'id'=>'zipcode5','maxlength'=>'5','tabindex'=>'12','autocomplete'=>'nope']) !!}   
                                    </div> 

                                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('zipcode4')) error @endif">
                                        {!! Form::label('', '', ['class'=>'control-label']) !!}
                                        {!! Form::text('zip4',null,['class'=>'form-control dm-zip4 js-address-check', 'id'=>'zipcode4','maxlength'=>'4','tabindex'=>'13','autocomplete'=>'nope']) !!} 
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 p-l-0">            
                                        <span class="js-address-loading hide margin-t-18"><i class="fa fa-spinner fa-spin icon-green-form margin-t-22"></i></span>
                                        <span class="js-address-success margin-t-18 @if($address_flag['pia']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form margin-t-22"></i></a></span>    
                                        <span class="js-address-error margin-t-18 @if($address_flag['pia']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form margin-t-22"></i></a></span>
                                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['pia']['is_address_match'],'css_class_add'); ?>
                                        <?php echo $value; ?> 
                                    </div>   
                                                              
                                </div>

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('phone')) error @endif">
                                        {!! Form::label('Home Phone', 'Home Phone', ['class'=>'control-label']) !!}
                                        {!! Form::text('phone',null,['class'=>'form-control  dm-phone','id'=>'phone','tabindex'=>'15','autocomplete'=>'nope']) !!}
                                        {!! $errors->first('phone', '<p> :message</p>')  !!}     
                                    </div>                            
                                </div>

                                <div class="form-group margin-b-15">
                                    @if(@$registration->marital_status ==1)
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 ">
                                        {!! Form::label('Marital Status', 'Marital Status', ['class'=>'control-label']) !!}
                                        {!! Form::select('marital_status', [''=>'-- Select --','Single' => 'Single','Married' => 'Married','Divorced' => 'Divorced','Partnered'=>'Partnered','Separated'=>'Separated','Widowed'=>'Widowed','Unknown'=>'Unknown'],null,['class'=>'select2 form-control','tabindex'=>'17']) !!}
                                    </div>                    
                                    @endif 
                                </div>
                            </div>
                        </div>

                    </div><!-- Personal Info Box Body Ends -->
                </div>

                <!-- Personal Info Ends -->
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <center class="margin-t-20">{!! HTML::image('img/cam.png',null,['class'=>'hidden-xs text-center text-centre img-responsive','style'=>'margin-top:26%;']) !!}</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h3 class="text-center">Patient Photo</h3>

                    <center>
                        <?php $webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); ?>
                        {!! Form::hidden('upload_type','browse') !!}
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="js-upload margin-t-15">
                                <div class="@if($errors->first('filefield')) error @endif">
                                    <span class="fileContainer" style="background-color: rgb(240, 125, 8); color: rgb(255, 255, 255); border: medium none; font-size: 12px; padding: 4px 16px;"> 
                                        {!! Form::file('filefield',['class'=>'default uploadFile','id'=>'filefield','accept'=>'image/png, image/gif, image/jpeg','style'=>'height: 30px; width: 20px;']) !!}Upload  </span>
                                    <span class="error" >{!! $errors->first('filefield',  '<p> :message</p>')  !!} </span>
                                    &emsp;<div style="margin-top: 5px;display: inline;" class="js-display-error"></div>
                                    <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                                </div>
                            </div>  
                            <div class="js-photo" style="display:none">
                                {!! Form::label('', '', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-4 control-label']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-6 no-padding">
                                    <span class="fileContainer js-webcam-class" style="padding:1px 20px 1px 11px;">
                                        <input type="hidden" class="js_err_webcam" /> Webcam</span>
                                    {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                    &emsp;<div style="margin-top: 5px;" class="js-display-error"></div>
                                </div>               
                            </div>
                        </div>
                        <input type="hidden" name="scanner_filename" id="scanner_filename">
                        <input type="hidden" name="scanner_image" id="scanner_image">
                    </center>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding " style="border-bottom: 2px solid #f0f0f0;">

        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 no-padding ">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margin-t-10">
                <div class="box box-view no-shadow no-bottom p-b-8 no-border-radius" >
                    <h4 class="med-darkgray margin-t-15 margin-b-20 no-bottom "><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Guarantor Details</h4>
                    <div class="box-body form-horizontal bg-white">
                        <!-- Guarantor Personal Details: Tab Index allocated (30 to 39) -->

                        <!-- Relationship -->
                        <div class="form-group margin-t-10 margin-b-15">

                            <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10">
                                {!! Form::label('Gua. Relationship', 'Guarantor Relationship', ['class'=>'control-label']) !!}
                                {!! Form::select('guarantor_relationship', [''=>'-- Select --','Self'=>'Self','Brother' => 'Brother','Child'=>'Child','Father'=>'Father','Friend'=>'Friend','GrandChild'=>'Grandchild','GrandFather'=>'Grandfather','GrandMother'=>'Grandmother','Guardian'=>'Guardian','Mother'=>'Mother','Neighbor'=>'Neighbor','Sister' => 'Sister','Spouse' => 'Spouse','Others'=>'Other'],$gu_relationship,['class'=>'select2 form-control  guarantor_relationship','tabindex'=>'30']) !!}                                            
                            </div>  
                             <input type="hidden" name="gu_self_check" id="gu_self_check" value='{{@$gu_self_check}}' />                                       
                        </div>

                        <!-- Last Name-->
                        <div class="form-group margin-b-15">
                            <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 @if($errors->first('gurantor_last_name')) error @endif">
                                {!! Form::label('Last Name', 'Last Name', ['class'=>'control-label']) !!}
                                {!! Form::text('guarantor_last_name',$gu_last_name,['class'=>'form-control   js-letters-caps-format','maxlength'=>'50','tabindex'=>'31','autocomplete'=>'nope']) !!}
                                {!! $errors->first('guarantor_last_name', '<p> :message</p>')  !!}             
                            </div>                               
                        </div>                        
                        <!-- First Name | Middle Name-->
                        <div class="form-group margin-b-15">
                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10 @if($errors->first('gurantor_first_name')) error @endif">
                                {!! Form::label('First Name', 'First Name', ['class'=>'control-label']) !!}
                                {!! Form::text('guarantor_first_name',$gu_first_name,['class'=>'form-control   js-letters-caps-format','maxlength'=>'50','tabindex'=>'32','autocomplete'=>'nope']) !!}
                                {!! $errors->first('guarantor_first_name', '<p> :message</p>')  !!}               
                            </div>                                
                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-10 @if($errors->first('gurantor_middle_name')) error @endif">
                                {!! Form::label('MI', 'MI', ['class'=>'control-label']) !!}
                                {!! Form::text('guarantor_middle_name',$gu_middle_name,['class'=>'form-control   js-letters-caps-format dm-mi','tabindex'=>'33','autocomplete'=>'nope']) !!}
                                {!! $errors->first('guarantor_middle_name', '<p> :message</p>')  !!}
                            </div>
                        </div>                       

                        <!-- Guarantor Employment Details: Tab Index allocated (40 to 50) -->                    
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 margin-t-10 col-lg-offset-1 col-md-offset-1">
                <div class="box box-view no-shadow no-bottom p-b-25 no-border-radius">
                    <h4 class="med-darkgray margin-t-15 margin-b-10 margin-l-m-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Employment Details</h4>
                    <div class="box-body form-horizontal bg-white no-padding">
                        <!-- Guarantor Personal Details: Tab Index allocated (30 to 39) -->

                        <!-- Guarantor Employment Details: Tab Index allocated (40 to 50) -->
                                         
						<div class="form-group margin-t-10 margin-b-15">
							<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
								{!! Form::label('employment_status', 'Employment Status', ['class'=>'control-label']) !!}  
								{!! Form::select('employment_status', [''=>'-- Select --','Employed' => 'Employed','Self Employed' => 'Self Employed','Retired'=>'Retired','Active Military Duty'=>'Active Military Duty','Unknown'=>'Unknown'],@$emp_relationship,['class'=>'select2 form-control js-employment_status  ','tabindex'=>'40']) !!}
							</div>                               
						</div>

						<!-- start employer added fields -->
						<span class="employed_option_sub_field @if(@$emp_relationship!='Employed' && @$emp_relationship != 'Self Employed') hide @endif">

							<div class="form-group margin-b-15">
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
									{!! Form::label('Employer Name', 'Employer Name', ['class'=>'control-label']) !!}
									{!! Form::text('employer_name',@$employer_name,['class'=>'form-control emp_search_texts','maxlength'=>'50','tabindex'=>'41','autocomplete'=>'nope']) !!}
								</div>                                                     
							</div>
							<div class="form-group margin-b-15">                                   
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
									{!! Form::label('Occupation', 'Occupation', ['class'=>'control-label']) !!}
									{!! Form::text('occupation',@$emp_occupation,['class'=>'form-control','maxlength'=>'50','tabindex'=>'42','autocomplete'=>'nope' ]) !!}        
								</div>                      
							</div>

						</span>
                        <!-- End employer added fields -->

						<!-- start student added fields -->
						<span class="student_option_sub_field @if(@$emp_student_status!='Student') hide @endif">

							<div class="form-group margin-b-15">
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
									{!! Form::label('Occupation', 'Occupation', ['class'=>'control-label']) !!}
									<?php @$emp_student_status = (@$emp_student_status!='')? @$emp_student_status:'Unknown' ?>
									{!! Form::select('student_status', [''=>'-- Select --','Full Time' => 'Full Time','Part Time' => 'Part Time','Unknown'=>'Unknown'],@$emp_student_status,['class'=>'select2 form-control','tabindex'=>'43','autocomplete'=>'nope']) !!}  
								</div>       
							</div>                                
						</span>
						<!-- End student added fields --> 
						<div class="form-group margin-b-15" @if(@$emp_relationship=="Retired" || @$emp_relationship=="Unknown") style="display: none;"@endif>
							<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('work_phone')) error @endif">
								{!! Form::label('Work Phone', 'Work Phone', ['class'=>'control-label']) !!}
								{!! Form::text('work_phone',@$emp_work_phone,['class'=>'form-control   dm-phone','id'=>'phone','tabindex'=>'44','autocomplete'=>'nope']) !!}
								{!! $errors->first('work_phone', '<p> :message</p>')  !!}    
							</div> 
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
								{!! Form::label('Ext', 'Ext', ['class'=>'control-label']) !!}
								{!! Form::text('work_phone_ext',@$emp_phone_ext,['class'=>'form-control   dm-phone-ext','id'=>'work_phone_ext','tabindex'=>'45','autocomplete'=>'nope']) !!}
								{!! $errors->first('work_phone_ext', '<p> :message</p>')  !!}       
							</div>  
							<input type="hidden" name="exist_emp_id" value='' />                    
						</div>
                       
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">

        <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Other Details</h4>

        <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 bg-white no-padding">
                <!-- Personal Info Starts -->            
                <div class="box box-view no-bottom no-border-radius no-shadow p-b-8">                                       
                    <div class="">
                        <div class="js-address-class"  id="js-address-personal-other-address">    
                            {!! Form::hidden('poa_address_type','patients_personal',['class'=>'js-address-type']) !!}
                            {!! Form::hidden('poa_address_type_id',@$patients->id,['class'=>'js-address-type-id']) !!}
                            {!! Form::hidden('poa_address_type_category','personal_other_address',['class'=>'js-address-type-category']) !!}
                            {!! Form::hidden('poa_address1',@$address_flag['poa']['address1'],['class'=>'js-address-address1']) !!}
                            {!! Form::hidden('poa_city',@$address_flag['poa']['city'],['class'=>'js-address-city']) !!}
                            {!! Form::hidden('poa_state',@$address_flag['poa']['state'],['class'=>'js-address-state']) !!}
                            {!! Form::hidden('poa_zip5',@$address_flag['poa']['zip5'],['class'=>'js-address-zip5']) !!}
                            {!! Form::hidden('poa_zip4',@$address_flag['poa']['zip4'],['class'=>'js-address-zip4']) !!}
                            {!! Form::hidden('poa_is_address_match',@$address_flag['poa']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                            {!! Form::hidden('poa_error_message',@$address_flag['poa']['error_message'],['class'=>'js-address-error-message']) !!}
							<div class="box-body form-horizontal bg-white margin-t-5 no-padding">
								<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

									<!-- Statement category block start -->
									<div class="form-group">
										<div class="col-lg-5 col-md-5 col-sm-10 col-xs-10 @if($errors->first('stmt_category')) error @endif">
											{!! Form::label('stmt_category', 'Statement Category', ['class'=>'control-label']) !!}
											{!! Form::select('stmt_category', array(''=>'-- Select --')+(array)@$stmt_category,null,['class'=>'select2 form-control js_stmt_cat','tabindex'=>'53']) !!}
										</div>                          
										<div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
											{!! Form::label('Statements', 'Statements', ['class'=>'control-label']) !!}
											{!! Form::select('statements', ['Yes' => 'Yes','Hold' => 'Hold','Insurance Only'=>'Insurance Only'],null,['class'=>'select2 form-control js-stmts','tabindex'=>'53']) !!}
										</div>                          
									</div>
									<?php									
										$readonly = "readonly=readonly"; $disabled_class = 'disabled';
										if(isset($patients->statements) && $patients->statements == 'Hold') {
											$readonly = $disabled_class = '';
										}
										$hold_rel_date = isset($patients->hold_release_date)? App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patients->hold_release_date): null;
										$pat_address =  true;
										$other_address =  false;
										if(isset($patients->other_status) && $patients->other_status != ''){    
											if($patients->other_status == 'Patient Address'){
												$pat_address =  true;$other_address =  false;}else{$pat_address= false; $other_address =  true;
											} 
										}                                     
									 ?>  								
									<!-- Statement hold reason block start -->
									<div class="form-group">
										<div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('hold_reason')) error @endif">
											{!! Form::label('hold_reason', 'Hold Reason', ['class'=>'control-label']) !!}
											{!! Form::select('hold_reason', array(''=>'-- Select --')+(array)@$stmt_holdreason,null,['class'=>'select2 form-control js_hold_blk','tabindex'=>'53', $disabled_class]) !!} 
											{!! $errors->first('hold_reason', '<p> :message</p>')  !!}               
										</div>         
										
										<div class="col-lg-4 col-md-4 col-sm-6 col-xs-10 @if($errors->first('hold_release_date')) error @endif">
											{!! Form::label('hold_release_date', 'Hold Release Date', ['class'=>'control-label']) !!}
											{!! Form::text('hold_release_date',(isset($patients->hold_release_date)?$hold_rel_date:null),['id'=>'hold_release_date','class'=>'form-control form-cursor dm-date js_hold_blk','tabindex'=>'80','placeholder'=>Config::get('siteconfigs.default_date_format'), $disabled_class,'autocomplete'=>'off']) !!}
										</div>
									</div> 
									<!-- Statement hold reason block ends -->
									<div class="form-group margin-t-10 margin-b-15">
										<div class="col-lg-10 col-md-10 col-sm-6 col-xs-10 margin-b-10"> 
											{!! Form::label('send_statement_to', 'Send Statement To', ['class'=>'control-label col-lg-12 no-padding','style'=>'margin-bottom:8px;']) !!}
											{!! Form::radio('send_statement_to', 'Patient Address',$pat_address ,['class'=>'send_statement_to','id'=>'pat-addr','tabindex'=>'53']) !!} {!! Form::label('pat-addr', 'Patient Address',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
											{!! Form::radio('send_statement_to', 'Other Address', $other_address,['class'=>'send_statement_to','id'=>'other-addr','tabindex'=>'54']) !!} {!! Form::label('other-addr', 'Other Address',['class'=>'med-darkgray font600 form-cursor']) !!} 
										</div>                        
									</div>

									<div class="patient-other-address @if($pat_address) hide @endif">
										<div class="form-group margin-t-10">
											<div class="col-lg-9 col-md-9 col-sm-6 col-xs-10 @if($errors->first('address1')) error @endif">
												{!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'control-label star']) !!}
												{!! Form::text('other_address1',null,['maxlength'=>'28','id'=>'stmt-address1','class'=>'form-control   js-address-check','tabindex'=>'55','autocomplete'=>'nope']) !!}
												{!! $errors->first('stmt-address1', '<p> :message</p>')  !!}    
											</div>                                                            
										</div>
										<div class="form-group margin-t-10">
											<div class="col-lg-9 col-md-9 col-sm-6 col-xs-10">
												{!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'control-label']) !!}
												{!! Form::text('other_address2',null,['maxlength'=>'50','class'=>'form-control   js-address2-tab','tabindex'=>'56','autocomplete'=>'nope']) !!}
											</div>                            
										</div>
										<div class="form-group margin-t-10 col-md-8" style="padding-left: 0px;">
											<div class="col-lg-12 col-md-12 col-sm-6 col-xs-10">
												{!! Form::label('City', 'City', ['class'=>'control-label star']) !!}
												{!! Form::text('other_city',null,['maxlength'=>'24','class'=>'form-control   js-address-check','id'=>'stmt-city','tabindex'=>'57','autocomplete'=>'nope']) !!}
											</div> 

										</div>
                                        <div class="form-group margin-t-10 col-md-4">
											<div class="col-lg-12 col-md-12 col-sm-6 col-xs-10">
												{!! Form::label('State', 'ST', ['class'=>'control-label star']) !!}
												{!! Form::text('other_state',null,['class'=>'form-control js-all-caps-leter-format js-state-tab js-address-check', 'maxlength'=>'2','id'=>'stmt-state','tabindex'=>'58','autocomplete'=>'nope']) !!}   
											</div>
                                        </div>
									</div>
								</div>

								<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1">
									<div class="form-group">                                
										@if(@$registration->driving_license ==1)
										<div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 @if($errors->first('driver_license')) error @endif">
											{!! Form::label('Driving License', 'Driving License', ['class'=>'control-label']) !!}
											{!! Form::text('driver_license',null,['maxlength'=>'15','class'=>'form-control','tabindex'=>'51','autocomplete'=>'nope']) !!}  
											{!! $errors->first('driver_license', '<p> :message</p>')  !!}
										</div>
										<div class="col-md-1 col-sm-1 col-xs-2 p-l-0 margin-t-18">
										<!--a data-toggle="modal" href="#form-content"><i class="fa fa-sellsy icon-green-form"></i></a-->
											<a id="document_add_modal_link_driving_license" href="#document_add_modal" @if(strpos($current_page, 'edit') !== false) data-url="{{url('api/adddocumentmodal/patients/'.@$id.'/Patient_Documents_Driving_License')}}" @else data-url="{{url('api/adddocumentmodal/patients/0/Patient_Documents_Driving_License')}}" @endif data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($documents_licence->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
										</div>
										@endif    
									</div>  
									<div class="form-group ">
										@if(@$registration->primary_care_provider ==1)
										<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('gurantor_last_name')) error @endif">
											{!! Form::label('PCP', 'PCP', ['class'=>'control-label']) !!}
											{!! Form::select('provider_id', array('' => '-- Select --') + (array)$providers,  $provider_id,['class'=>'select2 form-control','tabindex'=>'52']) !!} 
										</div> 
										@endif                               
									</div>  

									<div class="form-group margin-t-10 margin-b-15">
										<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('gurantor_first_name')) error @endif">
											{!! Form::label('Referring Provider', 'Referring Provider', ['class'=>'control-label','style'=>'margin-bottom:6px;']) !!}
											{!! Form::select('referring_provider_id', array(''=>'-- Select --')+(array)@$referringProviders,  @$referring_provider_id,['class'=>'select2 form-control','id'=>'referring_provider_id','tabindex'=>'54']) !!}
										</div>                              
									</div>

									<div class="form-group margin-t-10 margin-b-15">
										@if(@$registration->primary_facility ==1)  
										<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('gurantor_first_name')) error @endif">
											{!! Form::label('PCP', 'Primary Facility', ['class'=>'control-label','style'=>'margin-bottom:6px;']) !!}
											{!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities,  $facility_id,['class'=>'select2 form-control','id'=>'facility_id','tabindex'=>'54']) !!}
										</div>   
										@endif                            
									</div> 
									<div class="patient-other-address @if($pat_address) hide @endif">
									

									<div class="form-group margin-t-10">
										<div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('zipcode5')) error @endif">
											{!! Form::label('Zip Code', 'Zip Code', ['class'=>'control-label star']) !!}
											{!! Form::text('other_zip5',null,['class'=>'form-control dm-zip5 js-address-check', 'id'=>'otherzipcode5','maxlength'=>'5','tabindex'=>'60','autocomplete'=>'nope']) !!}   
										</div> 

										<div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 @if($errors->first('zipcode4')) error @endif">
											{!! Form::label('', '', ['class'=>'control-label']) !!}
											{!! Form::text('other_zip4',null,['class'=>'form-control dm-zip4 js-address-check', 'id'=>'otherzipcode4','maxlength'=>'4','tabindex'=>'61','autocomplete'=>'nope']) !!} 
										</div>
										<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 p-l-0">            
											<span class="js-address-loading hide margin-t-18"><i class="fa fa-spinner fa-spin icon-green-form margin-t-22"></i></span>
											<span class="js-address-success margin-t-18 @if($address_flag['poa']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form margin-t-22"></i></a></span>    
											<span class="js-address-error margin-t-18 @if($address_flag['poa']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form margin-t-22"></i></a></span>
											<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['poa']['is_address_match'],'css_class_add'); ?>
											<?php echo $value; ?> 
										</div>   
																  
									</div>
								</div>
								</div>
							</div>
						</div>
                    </div><!-- Personal Info Box Body Ends -->
                </div>
                <!-- Personal Info Ends -->
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">

        <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Emergency Details</h4>

        <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 bg-white no-padding">
                <!-- Personal Info Starts -->            
                <div class="box box-view no-bottom no-border-radius no-shadow p-b-8">                                       
                    <div class="">

                        <div class="box-body form-horizontal bg-white margin-t-5 no-padding">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                 <div class="form-group">
                                     <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10">
										{!! Form::label('emergency_relationship', 'Relationship', ['class'=>'control-label']) !!}
										{!! Form::select('emergency_relationship',  [''=>'-- Select --','Child' => 'Child','Father' => 'Father','Mother' => 'Mother','Spouse'=>'Spouse','Neighbor'=>'Neighbor','GrandMother'=>'Grandmother','GrandFather'=>'Grandfather','GrandChild'=>'Grandchild','Friend'=>'Friend','Brother'=>'Brother','Sister'=>'Sister','Guardian'=>'Guardian','Others'=>'Others'],@$emer_relationship,['class'=>'select2 form-control  emergency_relationship','tabindex'=>'71']) !!}
                                    </div>                                                         
                                </div>  
                                <div class="form-group">
                                    <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 @if($errors->first('emer_last_name')) error @endif">
                                        {!! Form::label('emer_last_name_label', 'Last Name', ['class'=>'control-label']) !!}
                                        {!! Form::text('emer_last_name',@$emer_last_name,['class'=>'form-control js-letters-caps-format emer_last_name','maxlength'=>'50','tabindex'=>'73','autocomplete'=>'nope']) !!}
                                        {!! $errors->first('emer_last_name', '<p> :message</p>') !!}           
                                    </div>                                                           
                                </div>

                                <div class="form-group">
                                    <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10">
                                        {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'control-label']) !!}
                                        {!! Form::text('emer_cell_phone',@$emer_cell_phone,['class'=>'form-control dm-phone','id'=>'emer_cell_phone','tabindex'=>'75','autocomplete'=>'nope']) !!}
                                    </div>                                                         
                                </div>                                        
                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1">
                                  <div class="form-group">
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                        {!! Form::label('Email', 'Email', ['class'=>'control-label']) !!}
                                        {!! Form::text('emer_email',@$emer_email,['class'=>'form-control','id'=>'emer_email','tabindex'=>'72','autocomplete'=>'nope']) !!}  
                                    </div>                                                          
                                </div>

                                <div class="form-group">
                                    <div class="col-lg-8 col-md-7 col-sm-6 col-xs-10 @if($errors->first('emer_first_name')) error @endif">
                                        {!! Form::label('emer_first_name_label', 'First Name', ['class'=>'control-label']) !!}
                                        {!! Form::text('emer_first_name',@$emer_first_name,['class'=>'form-control js-letters-caps-format','maxlength'=>'50','tabindex'=>'74','autocomplete'=>'nope']) !!}
                                        {!! $errors->first('emer_first_name', '<p> :message</p>')  !!}               
                                    </div>   
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-10 @if($errors->first('gurantor_middle_name')) error @endif">
                                        {!! Form::label('MI', 'MI', ['class'=>'control-label']) !!}
                                        {!! Form::text('emer_middle_name',@$emer_mi_name,['class'=>'form-control   js-letters-caps-format dm-mi','tabindex'=>'74','autocomplete'=>'nope']) !!}
                                        {!! $errors->first('guarantor_middle_name', '<p> :message</p>')  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- Personal Info Box Body Ends -->
                </div>

                <!-- Personal Info Ends -->
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">

        <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Additional Details</h4>

        <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 bg-white no-padding">
                <!-- Personal Info Starts -->            
                <div class="box box-view no-bottom no-border-radius no-shadow p-b-8">                                       
                    <div class="">

                        <div class="box-body form-horizontal bg-white margin-t-5 no-padding">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                @if(@$registration->ethnicity ==1)            

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 ">
                                        {!! Form::label('Ethnicity', 'Ethnicity', ['class'=>'control-label']) !!}
                                        {!! Form::select('ethnicity_id', array(''=>'-- Select --')+(array)$ethnicity,  $ethnicity_id,['class'=>'select2 form-control','id'=>'ethnicity_id','tabindex'=>'76']) !!}   
                                    </div>                        
                                </div>

                                @endif

                                @if(@$registration->race ==1)       

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 ">
                                        {!! Form::label('Race', 'Race', ['class'=>'control-label']) !!}
                                        {!! Form::select('race', [''=>'-- Select --','Asian' => 'Asian','Aslakan Eskimo'=>'Aslakan Eskimo','Black' => 'Black','Native American' => 'Native American','Patient Declined'=>'Patient Declined','Pacific Islander'=>'Pacific Islander','Unknown'=>'Unknown','White'=>'White'],null,['class'=>'select2 form-control','tabindex'=>'78']) !!}                          
                                    </div>                        
                                </div>
                                @endif

                                @if(@$registration->preferred_language ==1)   

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-9 col-md-9 col-sm-10 col-xs-10 ">
                                        {!! Form::label('Pref. Language', 'Pref. Language', ['class'=>'control-label']) !!}
                                        <?php $language_id  = $language_id=="" ? '5' : $language_id; ?>
                                        {!! Form::select('language_id', array('' => '-- Select --') + (array)$languages,  $language_id,['class'=>'select2 form-control','tabindex'=>'80']) !!}
                                    </div>                        
                                </div>
                                @endif

                                @if(@$registration->auto_phone_call_reminder ==1)              

                                <div class="form-group margin-t-10 margin-b-15">
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-10">                            
                                        {!! Form::label('Phone Reminders', 'Phone Reminders', ['class'=>'control-label col-lg-12 no-padding font14','style'=>'font-size:15px;margin-bottom:8px;']) !!}
                                        {!! Form::radio('phone_reminder', 'Yes', false,['tabindex'=>'82','class'=>'','id'=>'p-yes']) !!} {!! Form::label('p-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                        {!! Form::radio('phone_reminder', 'No', true,['tabindex'=>'82','class'=>'','id'=>'p-no']) !!} {!! Form::label('p-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                    </div>                        
                                </div>

                                @endif

                                @if(@$registration->send_email_notification ==1)              

                                <div class="form-group margin-b-15">
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-10">                            
                                        {!! Form::label('Email Reminders', 'Email Reminders', ['class'=>'control-label col-lg-12 no-padding font14','style'=>'font-size:15px;margin-bottom:8px;']) !!}
                                        {!! Form::radio('email_notification', 'Yes', false,['tabindex'=>'84','class'=>'','id'=>'e-yes']) !!} {!! Form::label('e-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                        {!! Form::radio('email_notification', 'No', true,['tabindex'=>'84','class'=>'','id'=>'e-no']) !!} {!! Form::label('e-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                    </div>                        
                                </div>

                                @endif

                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1">

                                @if(@$registration->preferred_communication ==1)    
                        
                                    <div class="form-group margin-b-15">
                                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 ">
                                            {!! Form::label('Pref. Communication', 'Pref. Communication', ['class'=>'control-label']) !!}
                                            {!! Form::select('preferred_communication', [''=>'-- Select --','Text Message' => 'Text Message','Voice Calls' => 'Voice Calls','Regular Mail' => 'Regular Mail','Email'=>'Email'],null,['id'=>'js_preferred_communication','tabindex'=>'77','class'=>'js_preferred_communication select2 form-control  -border1']) !!}
                                        </div>                        
                                    </div>
                                        
                                @endif
                        
                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 @if($errors->first('medical_chart_no')) error @endif">
										<!-- Changed Medical chart no sizt 10 to 30 billing team request : Selvakumar : Aug 13 2019 -->
                                        {!! Form::label('Chart No', 'Chart No', ['class'=>'control-label']) !!}
                                        {!! Form::text('medical_chart_no',null,['class'=>'form-control ','tabindex'=>'79','maxlength'=>'30', 'autocomplete'=>'nope']) !!}
                                        {!! $errors->first('medical_chart_no', '<p> :message</p>')  !!}
                                    </div>                        
                                </div>
                            
                                <div class="form-group margin-b-15">
                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 ">
                                        <!--<i class="fa fa-calendar-o form-icon"></i>-->
                                        {!! Form::label('Deceased Date', 'Deceased Date', ['class'=>'control-label']) !!}
                                        {!! Form::text('deceased_date',(isset($patients->deceased_date)  && ($patients->deceased_date != '') ? date('m/d/Y',strtotime($patients->deceased_date)):null), ['id'=>'deceased_date', 'class'=>'form-control form-cursor dm-date', 'tabindex'=>'81', 'placeholder'=>Config::get('siteconfigs.default_date_format'),  'autocomplete'=>'nope']) !!} 
                                    </div>                        
                                </div>
                            
                                <div class="form-group margin-b-15">
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-10">                            
                                        {!! Form::label('Status', 'Status', ['class'=>'control-label col-lg-12 no-padding font14','style'=>'font-size:15px;margin-bottom:8px;']) !!}
                                        {!! Form::radio('status', 'Active', true,['tabindex'=>'83','class'=>'','id'=>'s-active']) !!} {!! Form::label('s-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                                        {!! Form::radio('status', 'Inactive', false,['tabindex'=>'83','class'=>'','id'=>'s-inactive']) !!} 
										{!! Form::label('s-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                    </div>                        
                                </div>

                            </div>
                        </div>

                    </div><!-- Personal Info Box Body Ends -->
                </div>

                <!-- Personal Info Ends -->
            </div>

        </div>
    </div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">
        <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Alert Notes</h4>
        <!-- Personal Info Starts -->            
        <div class="box box-view no-bottom no-border-radius no-shadow p-b-8">                                       
            <div class="">

                <div class="box-body form-horizontal bg-white margin-t-5 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                <?php 
                                    $notes_created_by = @$patient_alert_note->created_by; 
                                    $alert_note = @$patient_alert_note->content;                                         
                                    $readonly = "readonly=readonly";
                                    if(@$notes_created_by == Auth::user()->id || Auth::user()->user_id == 1 || empty($alert_note)) {
                                       $readonly = ''; 
									}
								?>
                                {!! Form::textarea('patient_alert_note',null,['class'=>'form-control no-border', 'tabindex'=>'85', 'placeholder'=>'Enter your notes', 'style'=>'height:70px;', $readonly]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Personal Info Box Body Ends -->
        </div>
        <!-- Personal Info Ends -->
    </div>
</div>

{!! Form::hidden('current_tab','personal-info',['class'=>'form-control']) !!}
{!! Form::hidden('bill_cycle',@$patients->bill_cycle,['id'=>'bill_cycle']) !!}
{!! Form::hidden('statements_sent',@$patients_statements_sent_cnt) !!}

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-b-20">
    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics form-group js-v2-demography-submit', 'accesskey'=>'s','id' => 'js-v2-demography-submit','tabindex'=>'86']) !!}
    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
    @if( (strpos($currnet_page, 'edit') != false) || (strpos($currnet_page, 'nexttab') != false))
        @if(@$claims_count==0 && (@$practice_user_type=='practice_admin' || @$practice_user_type=='customer'))
            <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('patients/'.@$id.'/delete') }}">Delete</a>
        @endif
        <a href="javascript:void(0);" class="js_arrow pull-right" id="insurance">  {!! Form::button('>>', ['class'=>'btn btn-medcubics']) !!} </a></center>
    @else
        <a href="javascript:void(0)" data-url="{{ url('patients') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
    @endif
</div>

<!-- Modal Light Box starts -->  
<div id="form-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Driving License Number</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list">
                    <li class="nav-header">Upload</li>
                    <li><input class="input-xlarge" value="" type="file" name="upload"></li>
                    <li class="nav-header">Message</li>
                    <li><textarea class="form-control" placeholder="Description"></textarea></li>
                </ul> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-medcubics-small">Submit</button>
                <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div style="display:none" id="js-show-webcam">
    <?php $document_type = "patients"; ?>
    @if($document_type=='patients')
    @include ('layouts/webcam', ['type' => 'patient_document'])
    @endif
</div>

@push('view.scripts')
<script type="text/javascript">
	//  $(".js-address-success" ).removeClass("hide");
	<?php if($get_default_timezone){ ?> 
	var get_default_timezone = '<?php echo $get_default_timezone; ?>';
    <?php } else { ?>
    var get_default_timezone = '';
	<?php } ?>
</script>
@endpush