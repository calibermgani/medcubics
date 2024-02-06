            {!! Form::open(['name'=>'myform','id'=>'js-newpatient-validator','class'=>'popupmedcubicsform']) !!}
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border tabs-border margin-t-10 js_datepicker_scroll js_quick_patient">
                
                 <p class="no-bottom  margin-t-m-10"><span class="bg-white padding-0-4 med-orange font600">Personal Details</span></p>
                <div class="box box-info no-shadow no-border modal-timing-bg no-bottom">
                    <div class="box-body  form-horizontal">
                       <?php $ssnvalue = App\Models\Patients\Patient::ssnloop(); ?>
						<script>
							var tempArray = <?php echo $ssnvalue; ?>;
						</script>
                        <div id="js-new_patient" class="js-address-class">  
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
                            
                            <input type="hidden" value='<?php echo $ssnvalue; ?>' class='js_sche_snn' >
                            <div class="form-group">                             
                                {!! Form::label('Last Name', 'Last Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('last_name',null,['id'=>'last_name','class'=>'form-control input-sm-header-billing js-letters-caps-format','maxlength'=>'50','tabindex'=>'1']) !!}   
                                    <small class="help-block hide" id="js-error-last_name"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                    

                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                                    {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                         
                                    <div class="col-lg-5 col-md-5 col-sm-6 select2-white-popup">
                                        {!! Form::text('first_name',null,['id'=>'first_name','class'=>'form-control input-sm-header-billing js-letters-caps-format','maxlength'=>'50','tabindex'=>'2']) !!}   
                                        <small class="help-block hide" id="js-error-first_name"></small>
                                    </div>                        
                                    {!! Form::label('Middle Name', 'MI', ['class'=>'col-lg-1 col-md-1 col-sm-3 control-label-popup','style'=>'color:#00877f !important;']) !!} 
                                    <div class="col-lg-1 col-md-1 col-sm-6 p-l-0">
                                        {!! Form::text('middle_name',null,['id'=>'middle_name','class'=>'form-control input-sm-header-billing dm-mi js-letters-caps-format','tabindex'=>'2','style'=>'border-color:#d2d6de !important;box-shadow:none;']) !!}    
                                        <small class="help-block hide" id="js-error-middle_name"></small>
                                    </div>                        
                                    <div class="col-sm-1"></div>
                                </div>                    
                            </div>              

                            <div class="form-group">                             
                                {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('address1',null,['autocomplete'=>'nope','id'=>'address1','class'=>'form-control input-sm-header-billing js-address-check','maxlength'=>'28','tabindex'=>'3']) !!}   
                                    <small class="help-block hide" id="js-error-address1"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('address2',null,['autocomplete'=>'nope','id'=>'address2','class'=>'form-control input-sm-header-billing js-address2-tab','maxlength'=>'50','tabindex'=>'4']) !!}   
                                    <small class="help-block hide" id="js-error-address2"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                   


                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                       
                                    <div class="col-lg-4 col-md-4 col-sm-6 select2-white-popup">
                                        {!! Form::text('city',null,['autocomplete'=>'nope','id'=>'city','class'=>'form-control input-sm-header-billing js-address-check','maxlength'=>'24','tabindex'=>'5']) !!} 
                                        <small class="help-block hide" id="js-error-city"></small>
                                    </div>                        
                                    {!! Form::label('State', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-3 control-label-popup star']) !!}        
                                    <div class="col-lg-2 col-md-2 col-sm-6 p-l-0">
                                        {!! Form::text('state',null,['autocomplete'=>'nope','id'=>'state','class'=>'form-control js-all-caps-letter-format input-sm-header-billing js-address-check','maxlength'=>'2','tabindex'=>'5']) !!}   
                                        <small class="help-block hide" id="js-error-state"></small>
                                    </div>                        
                                    <div class="col-sm-1"></div>
                                </div>
                            </div>                    

                            <div class="form-group">                             
                                {!! Form::label('Zip Code', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    {!! Form::text('zip5',null,['autocomplete'=>'nope','id'=>'zip5','class'=>'form-control input-sm-header-billing js-address-check dm-zip5','tabindex'=>'6']) !!}
                                    <small class="help-block hide" id="js-error-zip5"></small>
                                </div> 
                                <div class="col-lg-3 col-md-3 col-sm-3">
                                    {!! Form::text('zip4',null,['autocomplete'=>'nope','id'=>'zip4','class'=>'form-control input-sm-header-billing js-address-check dm-zip4','tabindex'=>'6']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1">                                      
                                    <span class="js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                                    <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                                    <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                                    <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                                    <?php echo $value; ?> 
                                </div>
                                
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('DOB', 'DOB', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                                    <span id="new_patient_dob_icon"><i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i></span>
                                    {!! Form::text('dob',null,['id'=>'dob','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control input-sm-header-billing js_patient_dob dm-date','tabindex'=>'7', 'autocomplete' => 'nope']) !!}   
                                    <small class="help-block hide" id="js-error-dob"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            <div class="form-group">                             
                                {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    {!! Form::radio('gender','Male',null,['class'=>'','id'=>'gender_m','tabindex'=>'8']) !!} 
									{!! Form::label('gender_m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;  
                                    {!! Form::radio('gender','Female',null,['class'=>'','id'=>'gender_f','tabindex'=>'8']) !!} 
									{!! Form::label('gender_f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                                    {!! Form::radio('gender','Others',null,['class'=>'','id'=>'gender_o','tabindex'=>'8']) !!} 
									{!! Form::label('gender_o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                    <small class="help-block hide" id="js-error-gender"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group">                             
                                {!! Form::label('SSN', 'SSN', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('ssn',null,['id'=>'ssn','class'=>'form-control dm-ssn','tabindex'=>'9']) !!} 
                                    <small class="help-block hide" id="js-error-ssn"></small>
                                </div>                      
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('mobile',null,['id'=>'mobile','class'=>'form-control input-sm-header-billing dm-phone','tabindex'=>'10']) !!}   
                                    <small class="help-block hide" id="js-error-mobile_phone"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Home Phone', 'Home Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('home_phone',null,['id'=>'home_phone','class'=>'form-control input-sm-header-billing dm-phone','tabindex'=>'11']) !!}   
                                    <small class="help-block hide" id="js-error-home_phone"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group">                             
                                {!! Form::label('Self Pay', '', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    {!! Form::radio('is_self_pay','Yes',null,['class'=>'','id'=>'c-yes','tabindex'=>'12']) !!} {!! Form::label('c-yes', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;  
                                    {!! Form::radio('is_self_pay','No',true,['class'=>'','id'=>'c-no','tabindex'=>'12']) !!} {!! Form::label('c-no', 'No',['class'=>'med-darkgray font600 form-cursor']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group js-primary-ins-part">                             
                                {!! Form::label('Primary Insurance', 'Primary Ins', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7 select2-white-popup">
                                    {!! Form::select('primary_insurance_id',[''=>'-- Select --']+(array)$insurances,null,['class'=>'select2 form-control input-sm-header-billing', 'id' => 'primary_insurance_id','tabindex'=>'13']) !!}
                                    <small class="help-block hide" id="js-error-primary_insurance_id"></small>
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            <div class="form-group js-policy-id-part">                             
                                {!! Form::label('Policy ID', 'Policy ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label-popup star']) !!}                           
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    {!! Form::text('primary_insurance_policy_id',null,['class'=>'form-control input-sm-header-billing js-all-caps-letter-format','id' => 'primary_insurance_policy_id','maxlength'=>'29','tabindex'=>'14']) !!}   
                                    <small class="help-block hide" id="js-error-primary_insurance_policy_id"></small>
                                </div>     
                                <div class="col-md-1 col-sm-2 col-xs-2">            
                                    <a href="javascript:void(0);" class="js-patient-eligibility_check eligibility_gray_temp" data-patientid="" data-category="Primary" style="display:none; width: 7px;" title="Check Eligibility"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-gray font10"></i></a> 
                                    <i class="fa fa-spinner fa-spin eligibilityloadingimg font11" style="display:none;"></i>    
                                    <a class="js_get_temp_eligiblity_details js_eliactive_temp" style="display:none; width: 7px;" data-tempid="" data-type="" data-category="Primary" data-toggle="modal" title="Eligibility Details" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-green font10"></i></a>
                                    <a class="js_get_temp_eligiblity_details js_eliinactive_temp" style="display:none; width: 7px;" data-tempid="" data-type="" data-category="Primary" data-toggle="modal" title="Check Eligibility Details" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} text-red font10"></i></a>
                                </div>                   
                                <div class="col-sm-1"></div>
                            </div>
                            {!! Form::hidden('patient_temp_id','null',['class'=>'js-temp_id']) !!}
                        </div>
                    </div>
                </div>
            </div>      

            <div class="modal-footer no-padding">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small js_psubmit_form margin-t-10','accesskey'=>'s']) !!}
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js_pclose_form margin-t-10','data-dismiss'=>'modal']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->   

<script type="text/javascript">    
	// disableAutoFill('#js-newpatient-validator');
	$('#js-newpatient-validator').find('input:visible').each(function () {
        $(this).attr("autocomplete", "nope");
    });
</script>