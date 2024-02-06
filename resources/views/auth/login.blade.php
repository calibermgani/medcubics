@extends('auth/app')

@section('content')
<div class="container-fluid">
    <div class="row">        
        <div class="col-md-12 col-md-pull-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 login-bg">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}" id="js-bootstrap-validator"  style="margin-top:200px; @if($errors->first(0,':message') == 'Enter security code' || $errors->first(0,':message') == 'Invalid security code' || $errors->first(0,':message') == 'Unauthorized access' || $errors->first(0,':message') == 'This workspace not allowed') margin-left:98px; @else margin-left:80px; @endif">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <?php $remember=(@$remember_me == "on") ? 1 : 0; ?>
                            <div class="form-group">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 @if($errors->first('email')) alert-danger @endif" >
                                     {!! Form::email('email',($errors->first('email'))?null:@$cacheemail,['class'=>'form-control','placeholder'=>'Email']) !!}
                                     {!! $errors->first('email', '<span class="no-padding med-red font12" style="position: absolute"> :message</span>')  !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 @if($errors->first('password')) alert-danger @endif">
                                    {!! Form::input('password','password',@$cachepassword,['class'=>'form-control','placeholder'=>'Password']) !!}
                                    {!! $errors->first('password', '<span class="no-padding med-red font12" style="position: absolute"> :message</span>')  !!}
                                </div>
                            </div>
							@if($errors->first(0,':message') == 'Enter security code' || $errors->first(0,':message') == 'Invalid security code' || $errors->first(0,':message') == 'Unauthorized access')
							<div class="form-group">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 @if($errors->first('security')) alert-danger @endif">
                                    {!! Form::input('text','security',@$cachepassword,['class'=>'form-control','placeholder'=>'Security','maxlength'=>4]) !!}
                                    {!! $errors->first('security', '<span class="no-padding med-red font12" style="position: absolute"> :message</span>')  !!}
                                </div>
                            </div>
							@endif
							
                            <div class="col-lg-12 no-padding testser" style="margin-bottom:-19px;margin-left:-11px; margin-top:-10px;"> 
                                {!! $errors->first( 0, '<p class="alert-danger font12"> :message</p>')  !!}
                                @include('layouts/notification')    
                            </div>

                            <div class="form-group">
                                <div class="col-lg-6" style="width: 240px; color: red;"> 
                                    @if(Session::get('infocus')!== null) 
                                    {{ Session::get('infocus') }}
                                    @endif
                                </div>
                            </div>
							<!-- Kannan told to hide the remember me option in login page -->
                            <div class="form-group" style="display:none">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <div class="" style="color:#646464">
                                        <label style="font-size: 13px;">
                                            {!! Form::checkbox('remember','on',$remember,['class'=>'']) !!} Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:25px;">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:50px; margin-bottom:60px;">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <a class="font13 med-green forgot-password" href="{{ url('/password/email') }}">Forgot Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Patient Note Alert Window Starts  -->
 <div id="patientnote_model" class="js_common_modal_popup modal fade">
     <div class="modal-sm-usps">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title">Information</h4>
             </div>
             <div class="modal-body">
                 <input type="hidden" id="redirect_url" value="">
                 <div class="font13 modal-desc text-center med-green line-height-30 font600"></div>
                 <div class="modal-footer">
                     <button class="js_note_confirm btn btn-medcubics-small js_common_modal_popup_save close_popup" data-dismiss="modal" aria-label="Close" id="true" type="button">Ok</button>
                 </div>
             </div>
         </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
 </div><!-- Modal Light Box Ends -->
@endsection

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        /* Login page set focus on the load to email field start */
        @if ($errors -> first('email'))
			$("input[name='email']").focus();
		@elseif($errors -> first('password'))
			$("input[name='password']").focus();
		@else
			$("input[name='email']").focus();
		@endif
        /* Login page set focus on the load to email field end */

        /* Start Hide "Security Code" when typing a new email ID */       
       /* This comment for currently we are not used security code

       $("input[name='email']").blur(function(){
           var email = $("input[name='email']").val();
           var ajaxUrl = "<?php echo url('/check-security-code'); ?>";         
            $.ajax({
                type: "POST",        
                url: ajaxUrl,       
                data: {'email':email,'_token':$('input[name="_token"]').val()},   
                success: function(responce) {
                console.log(responce);                    
                  if(responce == 'Yes'){                                     
                       $("input[name='security']").hide();                   
                       $('.alert-danger').css("display", "none");
                  }else if(responce == 'No'){
                       $("input[name='security']").show();
                       $('.alert-danger').css("display", "block");
                  }                   
               }
            }); 
        });*/
         /* End Hide "Security Code" when typing a new email ID */       

		$('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                email: {
                    message: '',
                    validators: {
                        emailAddress: {
                            message: '{{ trans("common.validation.email_valid") }}'
                        },
                        callback: {
                            message: '{{ trans("common.validation.email") }}',
                            callback: function (value, validator) {
                                if (value == '') {
                                    return {
                                        valid: false,
                                        message: 'Enter email'
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                password: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("admin/adminuser.validation.password") }}'
                        }
                    }
                },
				security: {
                    message: '',
                    validators: {
                        callback: {
							message: '',
							callback: function (value, validator, $field) {
								if (value !=  '') {
									$('.alert-danger').css("display", "none");
									/* return {
										valid: false,
										message: 'Enter security code'
									}; */
								}								
								return true;
							}
                        }
                    }
                }
            }
        });
    });
	
	<!-- .modal-desc class used for append the error msg  -->
	<!-- Revision 1 - Ref: MR-2683 13 Aug 2019: Selva  -->
	function js_alert_popup(msg) {
		$("#patientnote_model .modal-desc").html(msg);
		$("#patientnote_model").modal('show');
		addModalClass();
	}
	
	function addModalClass() {
		setTimeout(function () {
			$('body').addClass('modal-open');
		}, 300);
	}
	<?php if($errors->first(0,':message') == 'This workspace not allowed'){ ?>
	$(document).ready(function(){
		js_alert_popup("Email ID not valid for this workspace");
		$('.alert-danger').hide();
	});
	<?php } ?>

	<?php if($errors->first(0,':message') == 'Enter security code'){ ?>
	$(document).ready(function(){
		js_alert_popup("It seems you are trying to login from a new location or device, Please check your email ID for security code");
	});
	<?php } ?>
	
	
	<?php if($errors->first(0,':message') == 'Invalid security code'){ ?>
	$(document).ready(function(){
		js_alert_popup("You have one attempt left; incase of incorrect code you will be blocked access to your account");
	});
	<?php } ?>
	
	<?php if($errors->first(0,':message') == 'Unauthorized access'){ ?>
	$(document).ready(function(){
		js_alert_popup("You have been blocked access for this account, Please contact your practice admin for more information");
	});
	<?php } ?>
	
	
</script>

@if(Session::has('success'))
<script type="text/javascript">
    $(document).ready(function () {
        js_alert_popup("Successfully verified");
    })
</script>
@endif

@if(Session::has('error'))
<script>
    $(document).ready(function () {
		msg = '<?php echo Session::get('error'); ?>';
        js_alert_popup(msg);
    })
</script>
@endif
@endpush