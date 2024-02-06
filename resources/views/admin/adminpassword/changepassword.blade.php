@extends('admin')
@section('toolbar')

	<div class="row toolbar-header">

		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i> Change Password </small>
			</h1>
			<ol class="breadcrumb">
					
				<li><a href="{{ url('admin/adminuser')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			   @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/adminpassword')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10" >
	{!! Form::open(['method' => 'POST','url' => 'admin/userpassword/changepassword','enctype'=>'multipart/form-data','id'=>'js-bootstrap-validator']) !!}
            <div class="box no-shadow">
                <div class="box-block-header with-border">
                    <i class="fa fa-ellipsis-h"></i> <h3 class="box-title">Change Password</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <!-- form start -->


                <div class="box-body  form-horizontal margin-l-10 margin-t-10">
                    <div class="form-group">
                        {!! Form::label('OldPassword', 'Old Password', ['class'=>'col-md-3 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
                            {!! Form::password('cpassword',['class'=>'form-control old_pwd']) !!}
                            {!! $errors->first('cpassword', '<p> :message</p>')  !!}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('password', 'New Password', ['class'=>' col-md-3 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('password')) error @endif">
							{!! Form::password('password',['class'=>'form-control','maxlength'=>20]) !!} 
							 
                            {!! $errors->first('password', '<p> :message</p>')  !!}
                           
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>
                    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.admin.changepassword") }}' />
                    <div class="form-group">
                        {!! Form::label('Confirm Password', 'Confirm Password', ['class'=>'col-md-3 col-sm-3 control-label']) !!} 
                        <div class="col-lg-3 col-md-3  col-sm-6 col-xs-10 @if($errors->first('con_password')) error @endif">
                            {!! Form::password('con_password',['class'=>'form-control','maxlength'=>20]) !!}
						    {!! $errors->first('con_password', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1 col-xs-2"></div>
                    </div>

                </div><!-- /.box-body -->

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                    {!! Form::submit('Save', ['class' => 'btn  btn-medcubics form-group','id'=>'pwd_change_btn']) !!}
                    <a href="{{url('admin/adminuser')}}"><button type="button" class="btn  btn-medcubics form-group">Cancel </button></a>
                </div>

            </div><!-- /.box -->
		</div><!--/.col (left) -->

  

{!! Form::close() !!}





@stop
<!--End-->
@push('view.scripts')
<script type="text/javascript">
	
    $(document).ready(function () {
		
		$('[name="password"]').on('keyup',function() {
			$('#js_bootstrap_validator').bootstrapValidator('revalidateField', 'con_password');
		});
		$('[name="con_password"]').on('keyup',function() {
			$('#js_bootstrap_validator').bootstrapValidator('revalidateField', 'password');
		});
		
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
								 notEmpty: {
                                    message: '{{ trans("admin/adminuser.validation.confirmpassword") }}'
                                },
								callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var pwd = validator.getFieldElements('password').val();
                                        var c_pwd = value;
										if (pwd != '') {
											if (c_pwd == ''){ 
												var msg = '{{ trans("admin/adminuser.validation.confirmpassword") }}';
												}
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
                        
                    }
                });
    });
</script>
@endpush