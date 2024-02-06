@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> View </span></small>
        </h1>
        <ol class="breadcrumb">
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1) 
            <li><a href="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')	

<span style="display:none;">
    {{ $segment = Request::segment(3) }} 
</span>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
    <?php $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'encode'); ?>
    <a href="{{ url('profile/personaldetails/'.$id) }}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
  	
</div>

<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10">
    <div class="box no-shadow box-primary" style="border-top:3px solid #00877f;">


        <div class="box-body  form-horizontal">


            <!-- Profile Image -->

            <div class="text-center">
                <div class="fileupload {{ ($customers->avatar_name == '' )? 'fileupload-new' : 'fileupload-exists' }}" data-provides="fileupload">
                    <div class="fileupload-new thumbnail"> 
                        <div class="safari_rounded">
                            {!! HTML::image('img/noimage.jpg') !!}
                        </div>
                    </div>
                    <div class="fileupload-preview fileupload-exists thumbnail">

                        <?php
							$filename = @Auth::user ()->avatar_name.'.'.@Auth::user ()->avatar_ext;
							$img_details = [];
							$img_details['file_name']=$filename;
							$img_details['practice_name']="admin";
							$img_details['module_name']=(Auth::user ()->practice_user_type == "customer") ? 'customers' : 'user';
							$img_details['class']='';
							$img_details['alt']='user-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
                        {!! $image_tag !!} 

                    </div>
                    <div>



                    </div>
                    @if($errors->first('image'))
                    <div class="error" >
                        {!! $errors->first('image', '<p > :message</p>')  !!}
                    </div>
                    @endif
                </div>
            </div>
            <?php //dd($customers);?>
            <h3 class="profile-username text-center margin-b-1">{!! @$customers->name; !!}</h3>
            <h5 class="med-orange text-center margin-b-15">@if($customers->dob != "0000-00-00" && $customers->dob != "" && $customers->dob != "1901-01-01")<i class="fa fa-birthday-cake no-print"></i> {{ App\Http\Helpers\Helpers::dateFormat(@$customers->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$customers->dob) }} - @endif{!! @$customers->gender; !!}</h5>
            <p class="text-muted text-center border-bottom-f0f0f0 border-green p-b-15"> </p>

            <ul class="list-group">
                <li class="list-group-item font600" style="border-bottom: 1px dashed #f0f0f0;">
                    Blogs  <a class="pull-right">{!! @$total_blogs; !!}</a>
                </li>
                <li class="list-group-item font600" style="border-bottom: 1px dashed #f0f0f0;">
                    Notes <a class="pull-right">{!! @$total_notes; !!}</a>
                </li>
                <li class="list-group-item font600">
                    Messages <a class="pull-right">{!! $total_messages; !!}</a>
                </li>
            </ul>
            <a href="{{ url('profile/changepassword') }}" class="btn btn-medcubics btn-block" >Change Password</a>
            <!-- /.box -->
        </div>
    </div>
</div>

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 margin-t-10">
    <div class="box no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="user"></i> <h3 class="box-title">User Details</h3>
            <div class="box-tools pull-right hide">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus "></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10 p-b-0">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding "><!-- Financial Red Alert Dates Starts -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-sm-04 col-xs-12 pr-t-m-20 no-padding" style="border-right: 1px solid #c1e9e5;"><!-- Red Alerts Starts -->
                    <div class="box box-view-border no-shadow no-border-radius no-border"><!-- Red Alert Box Starts -->
                        <div class="box-header-view-white  no-border-radius pr-p-t-20"><!-- Box Header Starts -->

                        </div><!-- /.box-header ends  -->
                        <div class="box-body table-responsive p-b-0"><!-- Red Alert Box Body Starts -->
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10 pr-l-5 ">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-6">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">Designation</p>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p>@if($customers->designation !=''){!! @$customers->designation; !!} @else <span class="nil">- Nil -</span> @endif </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-6">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">Department</p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""> @if($customers->department !=''){!! @$customers->department; !!} @else <span class="nil">- Nil -</span> @endif </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-6">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">Language</p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""> {!! @$customers->language->language; !!} </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-6">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding ">
                                        <p class="med-green font600">Ethnicity </p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""> {!! @$customers->ethnicity->name; !!} </p>
                                    </div>
                                </div>


                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">User Access</p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""> <span style="border:1px solid #ccc; padding: 2px 10px; border-radius: 20px;">@if($customers->useraccess =='web') Web @elseif($customers->useraccess =='app') App @endif</span> </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">User Type</p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""> {!! @$customers->user_type; !!} </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font600">Status</p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class=""><span class="bg-active"> {!! @$customers->status; !!} </span></p>
                                    </div>
                                </div>



                            </div>
                        </div><!-- Red Alert box-body ends -->
                    </div><!-- Red Alert box ends -->
                </div><!-- Red Alert Ends -->

                <div class="col-lg-6 col-md-6 col-sm-12 col-sm-04 col-xs-12 pr-t-m-20"><!-- Red Alerts Starts -->
                    <div class="box box-view-border no-shadow no-border-radius no-border"><!-- Red Alert Box Starts -->
                        <div class="box-header-view-white  no-border-radius pr-p-t-20"><!-- Box Header Starts -->

                        </div><!-- /.box-header ends  -->
                        <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10 pr-l-5 ">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-bottom: 1px dashed #f0f0f0;">
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font16"><i class="fa fa-map" style="background: #e7f8f6; padding: 16px;border-radius: 50%;"></i></p>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 no-padding">
                                        <p class="no-bottom">@if($customers->addressline1 !=''){!! @$customers->addressline1 !!}, @else <span class="nil">- Nil -</span> @endif @if($customers->city !=''){!! @$customers->city !!}, {!! @$customers->state !!} @else &emsp; @endif</p>
                                        <p class="">{!! @$customers->zipcode5 !!} @if($customers->zipcode4 != "") - {!! @$customers->zipcode4 !!}@endif @if($customers->zipcode5 =='') &emsp; @endif</p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="border-bottom: 1px dashed #f0f0f0;">
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font16"><i class="fa fa-envelope" style="background: #e7f8f6; padding: 16px;border-radius: 50%;"></i></p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-b-10">
                                        <p class="font16" style="padding: 12px 0px 0px 0px;"> {!! @$customers->email; !!} </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="border-bottom: 1px dashed #f0f0f0;">
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font16"><i class="fa fa-print" style="background: #e7f8f6; padding: 16px;border-radius: 50%;"></i></p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-b-10">
                                        <p class="font16" style="padding: 12px 0px 0px 0px;"> @if($customers->fax !=''){!! @$customers->fax; !!} @else <span class="nil">- Nil -</span> @endif </p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 no-padding">
                                        <p class="med-green font16"><i class="fa fa-phone" style="background: #e7f8f6; padding:14px 16px;border-radius: 50%;"></i></p>
                                    </div>
                                    <!-- Popup message open from Patient_statement.js called here-->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                        <p class="font16" style="padding: 12px 0px 0px 0px;">@if($customers->phone !=''){!! @$customers->phone; !!} @else <span class="nil">- Nil -</span> @endif</p>
                                    </div>
                                </div>
                            </div>
                        </div><!-- Red Alert box-body ends -->
                    </div><!-- Red Alert box ends -->
                </div><!-- Red Alert Ends -->
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
<!-- Modal Light Box starts -->
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->

@push('view.scripts')

<script type="text/javascript">
    $('[name="password"]').on('keyup', function () {
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'confirmpassword');
    });
    $('[name="confirmpassword"]').on('keyup', function () {
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
                                        else if (pwd.length > 20) {
                                            return {
                                                valid: false,
                                                message: 'Password contain only lessthan 20 characters'
                                            };
                                        }
                                        password = password_name(value);
                                        if (password != true) {
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
                                            if (c_pwd == '') {
                                                var msg = 'Enter confirm password';
                                            }
                                            else if (pwd != c_pwd)
                                                var msg = 'Password and confirm password must be the same';
                                            else if (c_pwd.length > 20)
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

@stop            