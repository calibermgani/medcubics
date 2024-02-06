@extends('admin')
@section('toolbar')
@include('profile/changepassword/tabs')	

{!! Form::open(['method' => 'POST','url' => 'profile/changepassword','enctype'=>'multipart/form-data','id'=>'js-bootstrap-validator']) !!}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10" >
		<!-- For avoid chrome password manager update -->
		<input style="display:none">
		<input type="password" style="display:none">
		
		<div class="box no-shadow">
			<div class="box-block-header with-border">
				<i class="fa fa-ellipsis-h"></i> <h3 class="box-title">Change Password</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<!-- form start -->
			{!! Form::input('password', 'oldpass', null,['style'=>'display:none', 'autocomplete' => "off"]) !!}
			<div class="box-body  form-horizontal margin-l-10 margin-t-10">
				<div class="form-group">
					{!! Form::label('OldPassword', 'Old Password', ['class'=>'col-md-3 col-sm-3 control-label star']) !!} 
					<div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
						{!! Form::input('password', 'cpassword', null,['class'=>'form-control old_pwd', 'autocomplete' => "off"]) !!}
						{!! $errors->first('cpassword', '<p> :message</p>')  !!}
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>

				<div class="form-group">
					{!! Form::label('password', 'New Password', ['class'=>' col-md-3 col-sm-3 control-label star']) !!} 
					<div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
						{!! Form::input('password', 'password', null,['class'=>' form-control','id'=>'new_pwd', 'autocomplete' => "off"]) !!}
						{!! $errors->first('password', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>
				<!------>
				<div class="form-group">
					{!! Form::label('Confirm Password', 'Confirm Password', ['class'=>'col-md-3 col-sm-3 control-label star']) !!} 
					<div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('confirmpassword')) error @endif">
						{!! Form::input('password', 'confirmpassword', null,['class'=>'form-control', 'autocomplete' => "off"]) !!}
						{!! $errors->first('confirmpassword', '<p> :message</p>')  !!}
					</div>
					<div class="col-sm-1 col-xs-2"></div>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
				{!! Form::submit('Save', ['class' => 'btn  btn-medcubics form-group','id'=>'pwd_change_btn']) !!}
			<?php $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'encode'); ?>	
				<a href="{{ url('profile/personaldetails/'.$id) }}" style="margin-left:40px"><button type="button" class="btn  btn-medcubics form-group">Cancel </button></a>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!--/.col (left) --> 
{!! Form::close() !!}
@stop
<!--End-->
@push('view.scripts')
<script type="text/javascript">
	$('[name="password"]').on('keyup',function() {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'confirmpassword');
	});
	$('[name="confirmpassword"]').on('keyup',function() {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'password');
	});

    $(document).ready(function () {
		
        $('#js-bootstrap-validator')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					cpassword: {
						validators: {
							notEmpty: {
								message: 'Please enter current  password'
							},
						}
					},
					password: {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var value_length = $(".js-delete-confirm").length;
									var pwd = value;
									var c_pwd = validator.getFieldElements('confirmpassword').val();
									if (pwd == '' && value_length == "0") {
										return {
											valid: false,
											message: 'Enter password'
										};
									}
									else if (c_pwd != '' && pwd != c_pwd) {
										return {
											valid: false,
											message: 'Password and confirm password must be the same'
										};
									} 
									else if(pwd.length > 20){
										return {
											valid: false,
											message: 'Password contain only lessthan 20 characters'
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
					confirmpassword: {
						validators: {
							callback: {
								message: '',
								callback: function (value, validator) {
									var pwd = validator.getFieldElements('password').val();
									var c_pwd = value;
									if (pwd != '') {
										if (c_pwd == ''){
											var msg = 'Enter confirm password';
										}
										else if(pwd != c_pwd)
											var msg = 'Password and confirm password must be the same';
										else if(c_pwd.length > 20)
											var msg = 'Password contain only lessthan 20 characters';
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
				}
			});
    });
</script>
@endpush