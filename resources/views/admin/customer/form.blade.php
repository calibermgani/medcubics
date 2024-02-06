<span style="display:none;">
    {{ $segment = Request::segment(3) }} 
</span>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.customer") }}' />

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10">
	<div class="box no-shadow">
		<div class="box-block-header with-border">
			 <i class="livicon" data-name="user"></i> <h3 class="box-title">Customer Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->

		<div class="box-body  form-horizontal margin-l-10">

			<div class="form-group">
				{!! Form::label('Customer Type', 'Customer Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('customer_type')) error @endif">
					{!! Form::select('customer_type', [
					'' => '-- Select --',
					'Billing'   => 'Billing',
					'Provider'  => 'Provider'],null,['class'=>'form-control select2']
					) !!}
					{!! $errors->first('customer_type', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>

			<div class="form-group">
				{!! Form::label('Contact Person', 'Contact Person', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('contact_person')) error @endif">
					{!! Form::text('contact_person',null,['class'=>'form-control js-letters-caps-format']) !!}
					{!! $errors->first('contact_person', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>


			<div class="form-group">
				{!! Form::label('Designation', 'Designation', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('designation')) error @endif">
					{!! Form::select('designation', [
					'' => '-- Select --',
					'CEO'   => 'CEO',
					'Director'   => 'Director',
					'Associate'   => 'Associate',
					'Administrator'   => 'Administrator',
					'Office Manager'   => 'Office Manager',
					'Billing Manager'  => 'Billing Manager'],null,['class'=>'form-control select2']
					) !!}
					{!! $errors->first('designation', '<p> :message</p>')  !!}
				</div>
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10"></div>
			</div>
			<div class="form-group">
				{!! Form::label('Gender', 'Gender',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-8 @if($errors->first('gender')) error @endif">
					{!! Form::radio('gender', 'Male',true,['class'=>'','id'=>'c-male']) !!} {!! Form::label('c-male', 'Male',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					{!! Form::radio('gender', 'Female',null,['class'=>'','id'=>'c-female']) !!} {!! Form::label('c-female', 'Female',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					{!! Form::radio('gender', 'Others',null,['class'=>'','id'=>'c-others']) !!} {!! Form::label('c-others', 'Others',['class'=>'med-darkgray font600 form-cursor']) !!}
					{!! $errors->first('gender', '<p> :message</p>')  !!}
				</div>
			</div>			
			<div class="form-group">
				{!! Form::label('Password', 'Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
						{!! Form::password('password',['class'=>'form-control','maxlength'=>20]) !!} 
					 
					{!! $errors->first('password', '<p> :message</p>')  !!}
				</div>
				<div class="col-lg-6 col-md-7 col-sm-6 col-xs-10"></div>
			</div>
			
			<div class="form-group">
				{!! Form::label('Confirm Password', 'Confirm Password', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-6 col-md-7  col-sm-6 col-xs-10 @if($errors->first('con_password')) error @endif">
					{!! Form::password('con_password',['class'=>'form-control','maxlength'=>20]) !!}
					{!! $errors->first('con_password', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1 col-xs-2"></div>
			</div>
			
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div><!--/.col (left) -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10" >
	<div class="box no-shadow">
		<div class="box-block-header with-border">
		   <i class="livicon" data-name="address-book"></i> <h3 class="box-title">Mailing Address</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->

		<div class="box-body  form-horizontal margin-l-10">

			<div class=" js-address-class" id="js-address-general-address">

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
					{!! Form::label('user name', 'User Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 @if($errors->first('lastname')) error @endif">
						{!! Form::text('lastname',null,['class'=>'form-control ','id'=>'lastname','maxlength'=>'50','placeholder'=>'Last Name']) !!}
						{!! $errors->first('lastname', '<p> :message</p>')  !!}
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 @if($errors->first('firstname')) error @endif">
						{!! Form::text('firstname',null,['class'=>'form-control','id'=>'firstname','maxlength'=>'50','placeholder'=>'First Name']) !!}
						{!! $errors->first('firstname', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>
				
				<div class="form-group">
					{!! Form::label('AddressLine1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('addressline1')) error @endif">
						{!! Form::text('addressline1',null,['class'=>'form-control js-address-check dm-address','id'=>'addressline1']) !!}
						{!! $errors->first('addressline1', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>


				<div class="form-group">
					{!! Form::label('AddressLine2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 @if($errors->first('addressline2')) error @endif">
						{!! Form::text('addressline2',null,['class'=>'form-control js-address2-tab dm-address','id'=>'addressline2']) !!}
						{!! $errors->first('addressline2', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>                                                
				
				<div class="form-group">
				{!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                                  
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('city')) error @endif">  
					{!! Form::text('city',null,['class'=>' form-control js-address-check dm-address','id'=>'city']) !!}
						{!! $errors->first('city', '<p> :message</p>')  !!}
					
				</div>{!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 @if($errors->first('state')) error @endif">
					{!! Form::text('state',null,['class'=>' form-control js-address-check js-state-tab dm-state','id'=>'state']) !!}
					{!! $errors->first('state', '<p> :message</p>')  !!}
				</div>
			</div>   

				<div class="form-group">
					{!! Form::label('zip Code', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 @if($errors->first('zipcode5')||($errors->first('zipcode4'))) error @endif">
						{!! Form::text('zipcode5',null,['class'=>' form-control js-address-check dm-zip5','id'=>'zipcode5']) !!}

						@if($errors->first('zipcode5') && $errors->first('zipcode4'))
							{!! $errors->first('zipcode5', '<p> :message</p>')  !!}
						@elseif($errors->first('zipcode5'))
							{!! $errors->first('zipcode5', '<p> :message</p>')  !!}
						@elseif($errors->first('zipcode4'))
							{!! $errors->first('zipcode4', '<p> :message</p>')  !!}
						@endif                                
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
						{!! Form::text('zipcode4',null,['class'=>' form-control js-address-check dm-zip4','id'=>'zipcode4']) !!}
					</div>
					<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
						<span class="add-on js-address-loading hide"><i class="fa fa-spinner fa-spin icon-green-form"></i></span>
						<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>
						<?php echo $value;?>                                  
					</div>                         
				</div>
				<div class="form-group">
					{!! Form::label('status1', 'Status',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label']) !!}
					<div class="col-lg-8 col-md-7 col-sm-7 col-xs-8 @if($errors->first('status')) error @endif">
						{!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
						{!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!}
						{!! $errors->first('status', '<p> :message</p>')  !!}
					</div>
				</div>	
			</div>
		</div>
	</div><!-- /.box-body -->
</div><!-- /.box -->

        
<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
	{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}

	<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
	@if(strpos($currnet_page, 'edit') !== false && $checkpermission->check_adminurl_permission('admin/customer/delete/{cust_id}') == 1)
		<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('admin/customer/delete/'.$customers->id) }}">Delete</a>
	@endif
	
	@if(strpos($currnet_page, 'edit') === false)
	<a href="javascript:void(0)" data-url="{{ url('admin/customer')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
	
	@if(strpos($currnet_page, 'edit') !== false)
	<a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customers->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
	@endif
</div>


<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
 @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->


@push('view.scripts')
<script type="text/javascript">
	var base = '<?php echo url('/'); ?>';
	avator_url = base+"/img/noimage.jpg";
	$(document).on('keydown', '[name="password"],[name="con_password"]', function(e) {
		if (e.keyCode == 32) return false;
	});
	$(document).on('change', '.btn-file input[type="file"]', function (e) {
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			if(new_file) {
				$(".js-delete-confirm").addClass('hide'); 
			}
			else {
				$(".js-delete-confirm").removeClass('hide'); 
			}
		}, 10);
		
	});
	$(document).on('click', '.confirm', function (e) {
		if ($(this).text() == 'Yes') {
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			if(new_file) {
				$(".fileupload.fileupload-new img").attr('src', avator_url);
				$(".js-delete-confirm").addClass('hide'); 
				$(".fileupload.fileupload-new .fileupload-preview").html('<input type="hidden" name="imagefile" value="" >');
			}
			else {
				if($(".safari_rounded img").hasClass('default')) {
					$(".js-delete-confirm").addClass('hide'); 
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
					$('[name="image"]').val("");
				}
				else {
					$('[name="image"]').val("");
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
				}
				$(".fileupload fileupload-preview.fileupload-exists.thumbnail").html('');
			}
		}
	});
	$(document).on('change', '.btn-file input[type="file"]', function (e) {
		if($(this).val() ==""){
			$(".fileupload.fileupload-exists .fileupload-preview").find("img").attr('src', $(".fileupload .js_default_img").attr('src'));
		}
		var img_file = $(this).val();
		img_file = img_file.split(".");
		var file_type = img_file[img_file.length-1];
		if((file_type !="jpg") || (file_type !="png") || (file_type !="jpeg") ) {
			$('.fileupload-preview').html('<img class="js_default_img" src="'+avator_url+'">');
			$( ".thumbnail .fileupload-preview" ).html();
			//$(".js_default_img").removeClass('hide')
		}
		e.preventDefault();
		setTimeout(function(){
			var new_file = $(".fileupload").hasClass('fileupload-new'); 
			if(new_file) {
				$(".fileupload .js-delete-confirm").addClass('hide'); 
			}
			else {
				$(".fileupload .js-delete-confirm").removeClass('hide'); 
			}
		}, 50);		
	});
	
	$(document).on('click', '.confirm', function (e) {
		if ($(this).text() == 'Yes') {
			var new_file = $(".fileupload").hasClass('fileupload-new');
			if(new_file) {
				$(".fileupload.fileupload-new img").attr('src', $(".fileupload .js_default_img").attr('src'));
				$(".fileupload .js-delete-confirm").addClass('hide'); 
				$(".safari_rounded img").addClass('default'); 
				$(".fileupload.fileupload-new .fileupload-preview").html('<input type="hidden" name="imagefile" value="" >');
			}
			else {
				if($(".safari_rounded img").hasClass('default')) {
					$(".fileupload .js-delete-confirm").addClass('hide'); 
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
					$('[name="avatar_url"]').val("");
				}
				else {
					$('[name="avatar_url"]').val("");
					$(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
				}
				$(".fileupload fileupload-preview.fileupload-exists.thumbnail").html('');
			}
		}
	});
	$(document).on('keypress','.js_space_restrict', function (e) {
		var regex = new RegExp("^[a-zA-Z0-9]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}

		e.preventDefault();
		return false;
	});
    $(document).ready(function() {
		/*
        $('[name="phone"]').on('change',function(){
                $('#js_bootstrap_validator')
                .data('bootstrapValidator')
                .updateStatus('phone', 'NOT_VALIDATED')
                .validateField('phone');
            });

        $('[name="mobile"]').on('change',function(){
                $('#js_bootstrap_validator')
                .data('bootstrapValidator')
                .updateStatus('mobile', 'NOT_VALIDATED')
                .validateField('mobile');
            });*/

        $('[name="firstname"]').on('keyup',function(){
			$('#js_bootstrap_validator').bootstrapValidator('revalidateField', 'lastname');
		});
		$('[name="password"]').on('keyup',function() {
			$('#js_bootstrap_validator').bootstrapValidator('revalidateField', 'con_password');
		});
		$('[name="con_password"]').on('keyup',function() {
			$('#js_bootstrap_validator').bootstrapValidator('revalidateField', 'password');
		});
		
		
        $('#js_bootstrap_validator')

            .bootstrapValidator({
                message: 'This value is not valid',
                        excluded: ':disabled',
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
						image: {
							validators: {
								file: {
									extension: 'png,jpg,jpeg',
									type: 'image/png,image/jpg,image/jpeg',
									maxSize: 1024*1024, // 1 MB
									message: '{{ trans("common.validation.image_maxsize_valid") }}'
								}
							}
						},
                        customer_name:{
                            message:'',
                            validators:{
                                notEmpty:{
                                    message: '{{ trans("admin/customer.validation.customer_name") }}'
                                    },
								regexp:{
									regexp: /^[a-zA-Z ]{0,50}$/,
									message: '{{ trans("common.validation.alphaspace") }}'
								}
                            }
                        },
						short_name: {
							message: '',
							trigger: 'change keyup',
							validators: {
								notEmpty: {
									message: '{{ trans("practice/practicemaster/provider.validation.short_name") }}'
								},
								callback: {
									message: '{{ trans("common.validation.shortname_regex") }}',
									callback: function (value, validator) {
										var get_val = validator.getFieldElements('short_name').val();
										if (get_val != '' && get_val.length < 3 ) 
											return false;
										return true;
									}
								}
							}
						},
						avatar_url:{
							message:'',
							validators:{
								file: {
									extension: 'jpeg,jpg,png,gif',
									message: attachment_valid_lang_err_msg
								},
								callback: {
									message: attachment_length_lang_err_msg,
									callback: function (value, validator,$field) {
										if($('[name="avatar_url"]').val() !="") {
											var size = parseFloat($('[name="avatar_url"]')[0].files[0].size/1024).toFixed(2);
											var get_image_size = Math.ceil(size);
											return (get_image_size>filesize_max_defined_length)?false : true;
										}
										return true;
									}
								}
							}
						},
                        customer_desc:{
                            message:'',
                            validators:{
                                notEmpty:{
                                    message: '{{ trans("common.validation.description") }}'
                                    }
                            }
                        },
                        customer_type:{
                            message:'',
                            validators:{
                                notEmpty:{
                                    message: '{{ trans("admin/customer.validation.customer_type") }}'
                                    }
                            }
                        },
                        contact_person:{
                            message:'',
                            validators:{
								notEmpty: {
                                    message: '{{ trans("admin/customer.validation.contact_person") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var alphaspace = '{{ trans("common.validation.alphaspace") }}';
										var regex = new RegExp(/^[A-Za-z ]+$/);
										var msg = lengthValidation(value,'contact_person',regex,alphaspace);
										if(value != '' && msg != true){
											return {
												valid: false,
												message: msg
											};
										}
										return true;
									}
								}
                               /* notEmpty:{
                                    message: 'Enter contact person!'
                                },
								callback: {
									message: 'Alphabets only allowed.',
									callback: function (value) {
										var str = value;
										var regex = new RegExp("^[a-zA-Z ]+$");
										if (!regex.test(str)) {
											return false;
										}
										/*else if (str != ""){
										return {
													valid: false,
													message: 'Characters only allowed'
												};
											
										}
										else{
										return true;
										}* /
										return true;
									}
								}*/
							}
                        },
                        designation:{
                            message:'',
                            validators:{
                                notEmpty:{
                                    message: '{{ trans("admin/customer.validation.designation") }}'
                                    }
                            }
                        },
						password: {
							validators: {
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var value_length = $(".js-delete-confirm").length;
                                        var pwd = value;
                                        var c_pwd = validator.getFieldElements('con_password').val();
                                        if (pwd == '' && value_length == "0") {
                                            return {
                                                valid: false,
                                                message: '{{ trans("admin/adminuser.validation.password") }}'
                                            };
                                        }
                                        else if (c_pwd != '' && pwd != c_pwd) {
                                            return {
                                                valid: false,
                                                message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
                                            };
                                        }
										password = password_name(value);
										if(password !=true) {
											return {
												valid: false, 
												message: password
											};
										}
                                        return true;
                                    }
                                }
							}
						},
						
						con_password: {
							validators: {
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var pwd = validator.getFieldElements('password').val();
                                        var c_pwd = value;
										if (pwd != '') {
											if (c_pwd == '')
												var msg = '{{ trans("admin/adminuser.validation.confirmpassword") }}';
											else if(pwd != c_pwd)
												var msg = '{{ trans("admin/adminuser.validation.passwordidentical") }}';
											else
												return true;
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
						lastname: {
							message: '',
							validators: {
								notEmpty:{
									message: '{{ trans("common.validation.lastname") }}'
								},
								regexp: {
									regexp: /^[a-zA-Z ]{0,50}$/,
									message: '{{ trans("common.validation.alphaspace") }}'
								}
							}
						},
						firstname: {
							message: '',
							validators: {
								notEmpty:{
									message: '{{ trans("common.validation.firstname") }}'
								},
								regexp: {
									regexp: /^[a-zA-Z ]{0,50}$/,
									message: '{{ trans("common.validation.alphaspace") }}'
								}
							}
						},
                        addressline1:{
                                message:'',
                                validators:{
                                   callback: {
										message: '',
										callback: function (value, validator) {
											var msg = addressValidation(value,"required");
											if(msg != true){
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
						  addressline2: {
							 message: '',
							 validators: {
								callback: {
										message: '',
										callback: function (value, validator) {
											var msg = addressValidation(value);
											if(msg != true){
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
                        city:{
                                message:'',
                                validators:{
                                    callback: {
										message: '',
										callback: function (value, validator) {
											var msg = cityValidation(value,"required");
											if(msg != true){
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
                        state:{
                                message:'',
                                validators:{
                                    callback: {
										message: '',
										callback: function (value, validator) {
											var msg = stateValidation(value,"required");
											if(msg != true){
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
                        zipcode5:{
                                message:'',
                                validators:{
                                    callback: {
										message: '',
										callback: function (value, validator) {
											var msg = zip5Validation(value,"required");
											if(msg != true){
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
						zipcode4: {
                            message: '',
							trigger: 'change keyup',
                            validators: {
                               message: '',
								callback: {
									message: '',
									callback: function (value, validator) {
										var msg = zip4Validation(value);
										if(msg != true){
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
                        phone: {
                                message: '',
                                validators:{
                                    callback: {
										message:'',
										callback: function (value, validator,$field) {
											var phone_msg = '{{ trans("common.validation.phone_limit") }}';
											var ext_msg = '{{ trans("common.validation.phone") }}';
											$fields = validator.getFieldElements('phone');
											var ext_length = $fields.closest("div").next().next().find("input").val().length;
											var response = phoneValidation(value,phone_msg,ext_length,ext_msg);
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
                          mobile: {
                                message: '',
                                validators:{
                                   callback: {
										message:'',
										callback: function (value, validator) {
											var mobile_msg = '{{ trans("common.validation.cell_phone_limit") }}';
											var response = phoneValidation(value,mobile_msg);
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
                        email:{
							message:'',
							validators:{
								notEmpty:{
									message: '{{ trans("common.validation.email") }}'
								},
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

/*
                        dob:{
							message:'',
								validators:{
									date:{
										  format:'MM/DD/YYYY',
										  message: '{{ trans("common.validation.date_format") }}'
										},
									callback: {
										message: '{{ trans("practice/practicemaster/provider.validation.date_format") }}',
										callback: function(value, validator, $field) {
											var dob = $('#js-bootstrap-validator').find('[name="provider_dob"]').val();
											var current_date=new Date(dob);
											var d=new Date();	
											return (dob !='' && d.getTime() < current_date.getTime())? false : true;
										}
									}
									
								}
                            },
*/
                }
        });
    });
	
</script>
@endpush
