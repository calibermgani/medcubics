<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.employer_details") }}' />

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space10"><!-- Contact Person Starts -->
    <div class="box  no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="user"></i> <h3 class="box-title"> Employer Status</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10">   <!-- Box Body Ends -->                                               
       <!--   <div class="form-group hide @if($errors->first('contact_person')) error @endif" >
                {!! Form::label('Employment Status', 'Employment Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('contact_person')) error @endif">
                    {!! Form::select('employer_status', array(''=>'-- Select --')+(array)$employer_status,  null,['class'=>'select2 form-control v2-js-employment_status','id'=>'employer_status-0']) !!}  
                    {!! $errors->first('employer_status', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>

            <!-- start employed added fields 
            <span id="employed_option_sub_field-0" class="employed_option_sub_field-0 hide">
                <div class="form-group">
                    {!! Form::label('organization_name_label', 'Organization Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::text('employer_organization_name',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_organization_name-0']) !!}
                    </div>
                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('occupation_label', 'Occupation', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        {!! Form::text('employer_occupation',null,['class'=>'form-control  -border1','maxlength'=>'50','id'=>'employer_occupation-0']) !!}
                    </div>
                    <div class="col-md-2 col-sm-1 col-xs-2"></div>
                </div>
            </span>
            <!-- end employed added fields -->

            <!-- start student added fields 
            <span id="student_option_sub_field-0" class="student_option_sub_field-0 hide">
                <div class="form-group">
                    {!! Form::label('student_status', 'Student Status', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
                    <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">  
                        {!! Form::select('employer_student_status', [''=>'-- Select --','Full Time' => 'Full Time','Part Time' => 'Part Time','Unknown'=>'Unknown'],'Unknown',['class'=>'select2 form-control','id'=>'employer_student_status-0']) !!}                           
                    </div>                
                </div>
            </span>
            <!-- end student added fields -->

            <div class="form-group">
                {!! Form::label('Employee Name', 'Employer Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label star']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('designation')) error @endif">
                    {!! Form::text('employer_name', null,['class'=>'form-control js-letters-caps-format','id'=>'employer_name']) !!}
                    {!! $errors->first('employer_name', '<p> :message</p>')  !!}
                </div>
                <div class="col-md-1 col-sm-2"></div>
            </div>  

			 
            <div class="form-group">
                {!! Form::label('Phone', 'Phone1',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-5 col-sm-4 col-xs-7 @if($errors->first('employer_phone')) error @endif">
                    {!! Form::text('work_phone', null,['class'=>'work_phone form-control dm-phone']) !!}
                    {!! $errors->first('work_phone', '<p> :message</p>')  !!}             
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    {!! Form::text('work_phone_ext', null,['class'=>'form-control dm-phone-ext','id'=>'work_phone_ext']) !!}                        
                </div>
            </div>   
			
			
			<div class="form-group">
                {!! Form::label('Phone', 'Phone2',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-5 col-sm-4 col-xs-7 @if($errors->first('employer_phone')) error @endif">
                    {!! Form::text('work_phone1', null,['class'=>'form-control dm-phone work_phone1','disabled']) !!}
                    {!! $errors->first('work_phone1', '<p> :message</p>')  !!}             
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    {!! Form::text('work_phone_ext1', null,['class'=>'form-control dm-phone-ext work_phone_ext1','id'=>'work_phone_ext','disabled']) !!}                        
                </div>
            </div> 
					
					
					
				<div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('fax')) error @endif">
                        {!! Form::text('fax', null,['class'=>'form-control  -border1 dm-fax']) !!}
                        {!! $errors->first('fax', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('email')) error @endif">
                        {!! Form::text('emailid', null,['id'=>'email','class'=>'form-control  -border1 js-email-letters-lower-format']) !!}
                        {!! $errors->first('emailid', '<p> :message</p>')  !!}
                    </div>                                    
                </div> 			
					


        </div><!-- /.box-body Ends-->
    </div><!-- /.box -->
</div><!--Employer Col Ends -->


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space10"><!-- Employer Col Starts -->
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="address-book"></i> <h3 class="box-title"> Employer Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class=" js-address-class" id="js-address-general-address"><!-- Address Div Starts -->
                {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
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
                    {!! Form::label('AddressLine1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address_line_1')) error @endif">
                        {!! Form::text('address1',null,['class'=>'form-control js-address-check dm-address','id'=>'address1']) !!}
                        {!! $errors->first('address1', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">
                    {!! Form::label('AddressLine2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('address2')) error @endif">
                        {!! Form::text('address2',null,['class'=>'form-control js-address2-tab dm-address','id'=>'address2']) !!}
                        {!! $errors->first('address2', '<p> :message</p>')  !!}
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group @if($errors->first('city')) error @endif">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        {!! Form::text('city',null,['class'=>' form-control js-address-check dm-address','id'=>'city']) !!}
                        {!! $errors->first('city', '<p> :message</p>')  !!}
                        {!! $errors->first('state', '<p> :message</p>')  !!}
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                        {!! Form::text('state',null,['class'=>'form-control js-address-check js-state-tab dm-state','id'=>'state']) !!} 
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('zip Code', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zip5'))) error @endif">  
                        {!! Form::text('zip5',null,['class'=>' form-control dm-zip5 js-address-check','id'=>'zip5']) !!}
                        @if($errors->first('zip5') && $errors->first('zip4'))
                        {!! $errors->first('zip5', '<p> :message</p>')  !!} 
                        @elseif($errors->first('zip5'))
                        {!! $errors->first('zip5', '<p> :message</p>')  !!}
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 @if($errors->first('zip4'))) error @endif"> 
                        {!! Form::text('zip4',null,['class'=>' form-control dm-zip4 js-address-check','id'=>'zip4']) !!} 
                        @if($errors->first('zip4'))
                        {!! $errors->first('zip4', '<p> :message</p>')  !!}
                        @endif
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2">
                        <span class="add-on js-address-loading hide"><i class="fa fa-spinner spin icon-green-form"></i></span>
                        <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green-form"></i></a></span>    
                        <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red-form"></i></a></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?> 
                    </div>
                </div>
                
                <div class="col-lg-12 col-md-12 hidden-sm hidden-xs margin-b-18">
                &emsp;
            </div>
                
            </div>   <!-- Address Div Ends -->       

            
        </div><!-- /.box-body Ends -->
    </div><!-- /.box Ends -->
</div><!--Employer col (left) Ends -->

<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
@if(strpos($currnet_page, 'edit') !== false)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
		{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
		@if($checkpermission->check_url_permission('employer/delete/{id}'))
		<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('employer/delete/'.$employer->id) }}">Delete</a>
		@endif	
		<a href="javascript:void(0)" data-url="{{ url('employer/'.$employer->id) }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	</div>
@else
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
		{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
		<a href="javascript:void(0)" data-url="{{ url('employer')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	</div>
@endif

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->
@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function(){   
        $('#employer_name').attr('autocomplete','nope');
        $('#work_phone_ext').attr('autocomplete','nope');
        $('#email').attr('autocomplete','nope');
        $('#address1').attr('autocomplete','nope');
        $('#address2').attr('autocomplete','nope');
        $('#city').attr('autocomplete','nope');
        $('#state').attr('autocomplete','nope');
        $('#zip5').attr('autocomplete','nope');
        $('#zip4').attr('autocomplete','nope');
        $('input[name="work_phone"]').attr('autocomplete','nope');
        $('input[name="fax"]').attr('autocomplete','nope');
        $('js-address-check').trigger('blur');  
        $(".work_phone").trigger("keypress");
    });
	
    $(document).on('click', '.confirm', function (e) {
        if ($(this).text() == 'Yes') {
            $('[name="avatar_url"]').val("");
            $(".safari_rounded img").addClass('default').attr('src', $(".fileupload .js_default_img").attr('src'));
            $(".fileupload .js-delete-confirm").addClass('hide');
            $(".fileupload .fileupload-preview.fileupload-exists.thumbnail img").attr('src', $(".fileupload .js_default_img").attr('src'));
            $(".fileupload .fileupload-preview").html('<input type="hidden" name="imagefile" value="" >');
            setTimeout(function () {
                if ($(".fileupload .fileupload-preview").find("img").length)
                    $(".fileupload .fileupload-preview").find("img").attr('src', $(".fileupload .js_default_img").attr('src'));
                else
                    $(".fileupload .fileupload-preview").append('<img src="' + $(".fileupload .js_default_img").attr('src') + '" >');
            }, 50);
        }
    });
	/* work Phone2 is enable or disabled based on work phone number  */
	$(document).on('keypress keyup ', '.work_phone',function(){
	 valu = $('.work_phone').val();
	 leng= valu.length;
	 /* If condition   remove the disabled attribute else condition add the disabled attribute*/
		if(leng >=14){
			$('.work_phone1').attr("disabled",false);//removeAttr("disabled");
			$('.work_phone_ext1').attr("disabled",false);//removeAttr("disabled");
		}	
		else{
			$('.work_phone1').attr('disabled', 'disabled');//removeAttr("disabled");
			$('.work_phone1').attr('disabled', 'disabled');//removeAttr("disabled");
			$('.work_phone_ext1').attr('disabled', 'disabled');//removeAttr("disabled");
		}
		
	});
    $(document).ready(function () { 
        $('#js-bootstrap-validator').bootstrapValidator({
			excluded: [':disabled', ':hidden', ':not(:visible)'],
            message: 'This value is not valid',
            feedbackIcons: {
                valid: '',
                invalid: '',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                employer_name: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/employer.validation.name") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var regexp = new RegExp(/^[A-Za-z ]+$/);
                                var regexp_msg = '{{ trans("common.validation.alphaspace") }}';
                                var msg = lengthValidation(value, 'contact_person', regexp, regexp_msg);
                                if (value.length > 0 && msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                employer_organization_name: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/employer.validation.employer_organization_name") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var regexp = new RegExp(/^[A-Za-z ]+$/);
                                var regexp_msg = '{{ trans("common.validation.alphaspace") }}';
                                var msg = lengthValidation(value, 'contact_person', regexp, regexp_msg);
                                if (value.length > 0 && msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                employer_occupation: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/employer.validation.employer_occupation") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var regexp = new RegExp(/^[A-Za-z ]+$/);
                                var regexp_msg = '{{ trans("common.validation.alphaspace") }}';
                                var msg = lengthValidation(value, 'contact_person', regexp, regexp_msg);
                                if (value.length > 0 && msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                employer_student_status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/employer.validation.employer_student_status") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var regexp = new RegExp(/^[A-Za-z ]+$/);
                                var regexp_msg = '{{ trans("common.validation.alphaspace") }}';
                                var msg = lengthValidation(value, 'contact_person', regexp, regexp_msg);
                                if (value.length > 0 && msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                employer_status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/employer.validation.status") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var regexp = new RegExp(/^[A-Za-z ]+$/);
                                var regexp_msg = '{{ trans("common.validation.alphaspace") }}';
                                var msg = lengthValidation(value, 'contact_person', regexp, regexp_msg);
                                if (value.length > 0 && msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
				
				work_phone: {
					message: '',
					validators: {
						callback: {
							message:'',
							callback: function (value, validator,$field) {
								var work_phone_msg = '{{ trans("common.validation.work_phone_limit") }}';
								var ext_msg = '{{ trans("common.validation.work_phone") }}';
								$fields = validator.getFieldElements('work_phone');
								var ext_length = $fields.closest("div").next().next().find("input").val().length;
								var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
				work_phone1: {
					message: '',
					validators: {
						callback: {
							message:'',
							callback: function (value, validator,$field) {
								var work_phone_msg = '{{ trans("common.validation.work_phone_limit") }}';
								var ext_msg = '{{ trans("common.validation.work_phone") }}';
								$fields = validator.getFieldElements('work_phone1');
								var ext_length = $fields.closest("div").next().next().find("input").val().length;
								var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
				
                /* work_phone: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {

                                var phone_msg = '{{ trans("common.validation.phone_limit") }}';
                                var ext_msg = '{{ trans("common.validation.phone") }}';
                                $fields = validator.getFieldElements('work_phone');
                                var ext_length = $fields.closest("div").next().next().find("input").val().length;
                                var response = phoneValidation(value, phone_msg, ext_length, ext_msg);
                                if (response != true) {
                                    return {
                                        valid: false,
                                        message: response
                                    };
                                }
                                return true;
                            }
                        }
                    }
                }, */
				
				 emailid:{
							message:'',
							validators:{
								
								callback: {
									message: '',
									callback: function (value, validator) {
										var response = emailValidation(value);
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

				fax: {
                            message: '',
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
				
                address1: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = addressValidation(value, "required");
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                address2: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = addressValidation(value);
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                city: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = cityValidation(value, "required");
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                state: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = stateValidation(value, "required");
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                zip5: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = zip5Validation(value, "required");
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                zip4: {
                    message: '',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var msg = zip4Validation(value);
                                if (msg != true) {
                                    return {
                                        valid: false,
                                        message: msg
                                    };
                                }
                                return true;
                            }
                        }
                    }
                }
            }
        });
    });
    $(document).on('change', '.v2-js-employment_status', function () {
        var employment_status_val = $(this).val();
        var curr_id = $(this).attr('id');
        contact_id_val_arr = curr_id.split('-');
        curr_contact_id = contact_id_val_arr[1];
        var form_id_val = 'js-bootstrap-validator';

        if (employment_status_val == 'Employed' || employment_status_val == 'Self Employed') {
            $("#employed_option_sub_field-" + curr_contact_id).removeClass('hide').addClass('show');
            $("#student_option_sub_field-" + curr_contact_id).addClass('hide');
        }
        else if (employment_status_val == 'Student') {
            if ($("#employer_student_status-" + curr_contact_id).val() == "")
                $("#employer_student_status-" + curr_contact_id).select2('val', 'Unknown');
            $("#student_option_sub_field-" + curr_contact_id).removeClass('hide').addClass('show');
            $("#employed_option_sub_field-" + curr_contact_id).addClass('hide');
        }
        else {
            $("#employed_option_sub_field-" + curr_contact_id).addClass('hide');
            $("#student_option_sub_field-" + curr_contact_id).addClass('hide');
        }

        if ($('#' + form_id_val + " .form-group").length) {
            $('#' + form_id_val).data('bootstrapValidator').revalidateField('employer_organization_name');
            $('#' + form_id_val).data('bootstrapValidator').revalidateField('employer_occupation');
            $('#' + form_id_val).data('bootstrapValidator').revalidateField('employer_student_status');
            $('#' + form_id_val).bootstrapValidator('revalidateField', 'employer_organization_name');
        }
    });

    $(document).ready(function () {
        if ($('#employer_status-0').val() != '') {
            $('#employer_status-0').change();
        }
    });

	$(document).on('keyup','.dm-phone-ext',function(){
		$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('work_phone');		
	});
	
	$(document).on('keyup','.dm-phone-ext',function(){
		$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('work_phone1');
	});
</script>
@endpush