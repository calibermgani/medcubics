@extends('auth/app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-pull-3">

            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reset-bg" style="min-height:500px;">

                      
                        <?php //echo $invalid; exit; ?>	
                        @if($invalid == 1)
                        {{ trans("common.validation.forgotlink_expire") }}
                        @elseif($invalid == 2)
                        {{ trans("common.validation.password_updated") }}
                        @else
                        <form class="form-horizontal" id="js-bootstrap-validator" role="form" method="POST" action="{{ url('resetpassword') }}" style="margin-top:220px; margin-left:80px;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">                                
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <label class="col-md-12 control-label" style="margin-left:-10px;margin-bottom: 10px;color:#00877f"><span style="color:#f07d08">E :</span> {{ $get_user_details->email }}</label>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="email" value="{{ $get_user_details->email }}" >
                            <input type="hidden" class="form-control" name="token" value="{{ $token }}" >                     
                            
                            <div class="form-group">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                     <input type="password" class="form-control" name="password" placeholder="Password">
                                    {!! $errors->first('password', '<p class="alert-danger"> :message</p>')  !!}
                                </div>
                            </div>
                            

                            <div class="form-group">                                
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password">
                                    {!! $errors->first('confirmpassword', '<p class="alert-danger"> :message</p>')  !!}
                                </div>
                            </div>

                            <div class="form-group margin-t-20">
                                <div class="col-l-g-6 col-md-6 col-sm-8 col-xs-12">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                    <a href="{{ url('auth/login')}}">{!! Form::button('Cancel', ['class'=>'btn btn-primary']) !!}</a>
                                </div>
                            </div>
                        </form>	
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .panel-heading {
        color: #35acc3;
        font-weight: bold;
        padding-bottom: 20px;
        text-align: center;
    }
</style>
@endsection

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('[name="password"]').on('keyup', function () {
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="confirmpassword"]'));
        });

        $('[name="confirmpassword"]').on('keyup', function () {
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="password"]'));
        });

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                password: {
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator) {
                                var pwd = value;
                                var c_pwd = validator.getFieldElements('confirmpassword').val();
                                var focus = $('[name="password"]').is(':focus');
                                if (pwd == '') {
                                    return {
                                        valid: false,
                                        message: '{{ trans("admin/adminuser.validation.password") }}'
                                    };
                                }
                                else if (focus == true && c_pwd != '' && pwd != c_pwd) {
                                    return {
                                        valid: false,
                                        message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
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
                                if (c_pwd == '') {
                                    return {
                                        valid: false,
                                        message: '{{ trans("admin/adminuser.validation.confirmpassword") }}'
                                    };
                                }
                                else if ($('[name="confirmpassword"]').is(':focus') == true && pwd != '' && pwd != c_pwd) {
                                    return {
                                        valid: false,
                                        message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
                                    };
                                }
                                else if ($('[name="password"]').is(':focus') != '')
                                {
                                    if (pwd != c_pwd) {
                                        return {
                                            valid: false,
                                            message: '{{ trans("admin/adminuser.validation.passwordidentical") }}'
                                        };
                                    }

                                }
                                return true;
                            }
                        }
                    }
                }
            }
        });
    });

    function password_name(value) {
        if (value != '') {
            if (!value.match(/[a-zA-Z]/g))
                return '{{ trans("common.validation.oneletter") }}';
            if (!value.match(/[0-9]/g))
                return '{{ trans("common.validation.onenumeric") }}';
        }
        if (value != "" && value.length < 6) {
            return '{{ trans("common.validation.password_length") }}';
        }
        return true;
    }

</script>
@endpush