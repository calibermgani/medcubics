<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
{!! Form::open(['url'=>'patients/billing_provider','id' => 'ModelForm','files' => true,'class'=>'popupmedcubicsform']) !!}
    <div class="box-body form-horizontal no-padding no-bottom">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
            <input type="hidden" name="valid_npi_bootstrap" value="" />                             
                {!! Form::label('Npi', 'NPI', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::text('npi', null,['title'=>'NPI','class'=>'form-control Select provider type dm-npi js-npi-check input-sm-modal-billing','id'=>'npi', 'autocomplete'=>'off']) !!}
                    {!! Form::hidden('type','provider',['id'=>'type']) !!}
					{!! Form::hidden('type_id',null,['id'=>'type_id']) !!}
					{!! Form::hidden('type_category','Individual',['id'=>'type_category']) !!}
                </div> 
             <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                        <span class="js-npi-individual-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
                        <span class="js-npi-individual-success hide"><a data-toggle="modal" href="" data-target="#form-npi-modal"><i class="fa fa-check icon-green-form"></i></a></span>
                        <span class="js-npi-individual-error hide"><a data-toggle="modal" href="" data-backdrop="false" data-target="#form-npi-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                        <?php $value = App\Http\Helpers\Helpers::commonNPIcheck_view('yes', 'induvidual'); ?>   
                        <?php echo $value; ?>  
                    </div>
            </div>
             <div id="is_provider"></div>
             <div class="form-group">
                {!! Form::label('Provider Type', 'Provider Type', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">      
                        {!! Form::select('provider_types_id', array(''=>'-- Select --')+(array)$provider_type, null,['class'=>'select2 form-control','id'=>'provider_types_id']) !!}   
                    </div>                                        
            </div>
            <div class="form-group">
                {!! Form::label('Gender', 'Gender', ['class'=>'col-lg-5 col-md-4 col-sm-5 col-xs-3 control-label star']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">
                    {!! Form::radio('gender', 'Male',true,['id'=>'gender_m','class'=>'flat-red']) !!} {!! Form::label('gender_m', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    {!! Form::radio('gender', 'Female',null,['id'=>'gender_f','class'=>'flat-red']) !!} {!! Form::label('gender_f', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('gender', 'Others',null,['id'=>'gender_o','class'=>'flat-red']) !!} {!! Form::label('gender_o', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
            </div>
             <div class="form-group">
                {!! Form::label('Degree', 'Degree', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('provider_degrees_id', array(''=>'-- Select --')+(array)$provider_degree, null,['class'=>'select2 form-control','id'=>'provider_degrees_id']) !!}                    
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>            
            {!! Form::hidden('organization_name',null,['placeholder'=>'Organization Name','class'=>'form-control','id'=>'organization_name']) !!}
            <div class="form-group">                             
                {!! Form::label('Last Name', 'Last Name', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('last_name',null,['class'=>'form-control input-sm-modal-billing js-letters-caps-format','id'=>'last_name','autocomplete'=>'off']) !!}                        
                </div>                        
            </div>
            <div class="form-group">                             
                {!! Form::label('First Name', 'First Name', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('first_name',null,['class'=>'form-control input-sm-modal-billing js-letters-caps-format ','id'=>'first_name','autocomplete'=>'off']) !!}
                </div>                        
            </div>
            <div class="form-group">                             
                {!! Form::label('Middle Initial', 'Middle Initial', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}                           
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
                    {!! Form::text('middle_name',null,['title'=>'Middle Initial', 'maxlength' => '1','class'=>'form-control input-sm-modal-billing  md-mi js-letters-caps-format','id'=>'middle_name','autocomplete'=>'off']) !!}
                </div>                        
            </div>
            <div class="form-group">
             {!! Form::label('Short Name', 'Short Name', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600 star']) !!}
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
					{!! Form::text('short_name',null,['class'=>'form-control input-sm-modal-billing js_all_caps_format', 'maxlength' => '3','id'=>'short_name','autocomplete'=>'off']) !!}
				</div>
			</div>
            <div class="form-group">
                {!! Form::label('Phone', 'Phone', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::text('phone', null,['id'=>'phone','class'=>'form-control input-sm-modal-billing inputmask-phone dm-phone','autocomplete'=>'off']) !!}                        
                </div>                        
            </div>
            <div class="form-group">                             
                {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('fax')) error @endif">
                    {!! Form::text('fax', null,['id'=>'fax','class'=>'form-control input-sm-modal-billing dm-phone','autocomplete'=>'off']) !!}
                    {!! $errors->first('fax', '<p> :message</p>')  !!}    
                </div>                        
            </div>            
        </div>
        <!-- The below were added only to avoid undefined index error on dbconnection controller-->
        <div class=" js-address-class" id="js-address-general-address">
            {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
            {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
            {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
            {!! Form::hidden('general_address1',null,['class'=>'js-address-address1']) !!}
            {!! Form::hidden('general_city',null,['class'=>'js-address-city']) !!}
            {!! Form::hidden('general_state',null,['class'=>'js-address-state']) !!}
            {!! Form::hidden('general_zip5',null,['class'=>'js-address-zip5']) !!}
            {!! Form::hidden('general_zip4',null,['class'=>'js-address-zip4']) !!}
            {!! Form::hidden('general_is_address_match',null,['class'=>'js-address-is-address-match']) !!}
            {!! Form::hidden('general_error_message',null,['class'=>'js-address-error-message']) !!}
        </div>
        <div class="modal-footer">
			{!! Form::submit("Submit", ['class'=>'btn btn-medcubics-small js-modelform-submit']) !!}
			<!--<button class="btn btn-medcubics-small js_popup_commonform_reset" type="button">Cancel</button> -->
			<button class="btn btn-medcubics-small close_popup" type="button">Close</button>
		</div>
 {!! Form::close() !!}
    </div><!-- /.box-body -->
@include ('practice/layouts/npi_form_modal')
</div>
<script type="text/javascript">   
	$(document).ready(function () {
		$('#ModelForm')
			.bootstrapValidator({
				excluded: ':disabled',
				message: 'This value is not valid',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					last_name: {
						message: '',
						trigger: 'change keyup',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/provider.validation.last_name") }}'
							},
							regexp:{
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var lastName_value = value.trim();
									if(lastName_value.length !=0) {
										var return_option = referprovidernameValidation();
										if(return_option == false) {
											return {
													valid: false,
													message: '{{ trans("common.validation.provider_name_limit") }}'
												}; 
										}                                
									}
									return true;
								}
							}
						}
					},
					first_name: { 
						message: '',
						trigger: 'change keyup',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/provider.validation.first_name") }}'
							},
							regexp:{
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alphaspace") }}'
							},
							callback: {
								message: '',
								callback: function (value, validator) {
									var firstName_value = value.trim();
									if(firstName_value.length !=0) {
										var return_option = referprovidernameValidation();
										if(return_option == false) {
											return {
													valid: false,
													message: '{{ trans("common.validation.provider_name_limit") }}'
												}; 
										}                                 
									}
									return true;
								}
							}
						}
					},
					middle_name: {
						message: '',
						trigger: 'change keyup',
						validators: {
							regexp:{
								regexp: /^[A-Za-z ]+$/,
								message: '{{ trans("common.validation.alpha") }}'
							},
							callback: {
								message: 'Name allowed 24 characters',
								callback: function (value, validator) {
									var middle_name_value = value.trim();
									if(middle_name.length !=0) {
										var return_option = referprovidernameValidation();
										if(return_option == false) {
											return {
												valid: false,
												message: '{{ trans("common.validation.provider_name_limit") }}'
											}; 
										}                                
									}
									return true;
								}
							}
						}
					},
					provider_types_id: {
						 message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/patients/popup_provider.validation.provider_types_id") }}'
							}
						}
					},
					short_name: {
						trigger: 'change',
						message: '',
						validators: {
							notEmpty: {
								message: '{{ trans("practice/practicemaster/provider.validation.short_name") }}'
							},
							regexp:{
								regexp: /^[A-Za-z\s]+$/,
								message: '{{ trans("common.validation.alpha") }}'
							},
							callback: {
								message: '{{ trans("common.validation.shortname_regex") }}',
								callback: function (value, validator) {
									var get_val = validator.getFieldElements('short_name').val();                                      
									var value = value.replace(/ /g, '');
									var get_val = get_val.replace(/ /g, '');
									var regExp = /^[a-zA-Z]*$/;
									if (get_val != '' && get_val.length < 3 && regExp.test(value)) 
										return false;
									return true;
								}
							},
							remote: {
								message: 'Short name already exists',
								url: api_site_url+'/providerShortNameValidation',
								data: function(validator, $field, value) {
									return {
										providerId: validator.getFieldElements('provider_types_id').val(),
										_token:$('input[name="_token"]').val(),
									};
								},
								type: 'post'
							}
						}
					},
					npi: {
						trigger: 'change keyup',
						validators: {
							callback: {
								message: '{{ trans("common.validation.npi_regex") }}',
								callback: function (value, validator) {
									console.log("npi valid check");
									if (value == "") {
										$('input[type=hidden][name="valid_npi_bootstrap"]').val('');
										return {
											valid: false,
											message: '{{ trans("common.validation.npi") }}'
										};
									}
									else if (value.search("[0-9]{10}") == -1) {
										$('input[type=hidden][name="valid_npi_bootstrap"]').val('');
										return {
											valid: false,
											message: '{{ trans("common.validation.npi_regex") }}'
										};
									}
									else {
										if ($('input[type=hidden][name="valid_npi_bootstrap"]').val() != '') {
											return {
												valid: false,
												message: '{{ trans("common.validation.npi_validcheck") }}'
											};
										}
									}
									return true;
								}
							}
						}
					},
					phone: {
						message: '',
						trigger: 'change keyup',
						validators: {
							callback: {                                   
								callback: function (value, validator) {
									var cell_phone_msg = '{{ trans("common.validation.phone_limit") }}';
									var response = phoneValidation(value,cell_phone_msg);
									if(response !=true) {
										return {
											valid: false, 
											message: response
										};
									}
									return true;
								}
							},                               
						}
					},
					fax: {
					  message: '',
					  trigger: 'change keyup',
						validators: {
							callback: {
								message:'',
								callback: function (value, validator) {
									var fax_msg = '{{ trans("common.validation.fax_limit") }}';
									var response = phoneValidation(value,fax_msg);
									if(response !=true) {
										return {
											valid: false, 
											message: response
										};
									}
									return true;
								}
							}
						}
				},
				provider_degrees_id:{
					message:'',
					enabled:true,
					validators:{
						notEmpty: {
							message: '{{ trans("practice/practicemaster/provider.validation.provider_degree") }}'
						}
					}
				},
			}
		});               
		$('[name="npi"]').on('change',function(){
			$('#ModelForm')
				.data('bootstrapValidator')
				.updateStatus('npi', 'NOT_VALIDATED')
				.validateField('npi');
		});
	});

	function referprovidernameValidation() {
		var lastName    = $("#last_name").val();
		var firstName   = $("#first_name").val();
		var middleName  = $("#middle_name").val();
		var lastName_value  = lastName.trim();
		var firstName_value = firstName.trim();
		var middleName_value = middleName.trim();
		var add_length = lastName_value.length + middleName_value.length + firstName_value.length ;
		var return_option = (add_length>24) ? false : true;
		return return_option;
	}
	
	$(document).on('keyup change' ,'[name="last_name"],[name="first_name"],[name="middle_name"]', function () { 
		$('#ModelForm').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
		$('#ModelForm').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
		$('#ModelForm').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
	});

	$(document).on('change','[name="provider_types_id"]',function(){
		$('#ModelForm').bootstrapValidator('revalidateField', $('input[name="short_name"]'));
	});

</script>                                