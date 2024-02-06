@extends('auth/app-register')

@section('content')
<style type="text/css">

</style>

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form class="js-practice-add" id="register-form" role="form" method="POST" action="{{ url('/auth/practiceCreation') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="row ">
        <div class="col-lg-12">
            <div class="">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-Mail Address">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="password" name="con_password" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="text" name="practice_name" class="form-control" placeholder="Practice Name">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="text" name="npi" class="form-control dm_npi" placeholder="NPI">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="text" name="phone" class="form-control dm_phone" placeholder="Phone">
                        </div>
                    </div>
                </div>


                <div class="row">                    
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::select('speciality_id', array(''=>'-- Select Specialty --')+(array)$specialities,  null,['class'=>'select2 form-control','id'=>'js-speciality-change']) !!}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::select('taxanomy_id', array(''=>'-- Select Taxonomy --')+(array)$taxanomies, null, ['class'=>'select2 form-control','id' => 'taxanomies-list']) !!}
                        </div>
                    </div>
                </div>
                <div class="js-practice-address">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="address_line_1" class="form-control" placeholder="Address Line 1">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="address_line_2" class="form-control" placeholder="Address Line 2">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="city" class="form-control" placeholder="City">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select name="state" class="form-control js-state">
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="zip5" class="form-control dm_zip5" placeholder="Zip Code 5">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="zip4" class="form-control dm_zip4" placeholder="Zip Code 4">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <select name="timezone" class="form-control js-timezone">
                                <option value="" selected>-- Select TimeZone --</option>
                                <option value="America/Chicago">America/Chicago</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="checkbox" class="js-same-as-address" name="same_as_paytoaddress" id="mail_same_address" checked> <label for="mail_same_address" class="js-same-as-address">Mailing Address same as above address</label>
                        </div>
                    </div>
                </div>
                <div class="js-practice-mailing-address" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="mailing_address_line_1" class="form-control" placeholder="Mailing Address Line 1">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="mailing_address_line_2" class="form-control" placeholder="Mailing Address Line 2">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="mailing_city" class="form-control" placeholder="Mailing City">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <select name="mailing_state" class="form-control">
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="mailing_zip5" class="form-control dm_zip5" placeholder="Mailing Zip Code 5">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="text" name="mailing_zip4" class="form-control dm_zip4" placeholder="Mailing Zip Code 4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">
                    <div class="form-group">
                        <button  type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="snackbar-div">
    <h3><span id="show_error_type">Success</span> <i class="fa fa-close pull-right font12 form-cursor med-gray m-r-m-10 margin-t-2"></i></h3>
    <p id="show_error_msg">Your data updated successfully.</p>
</div>
@endsection

@push('view.scripts')
<script src="https://www.google.com/recaptcha/api.js?render=6LdJQMIbAAAAAM7mE9l0k8wR1KEVQcg4cNjlTv19"></script>
   <script>
      $('#register-form').submit(function(event) {
        event.preventDefault();
        var email = $('#email').val();
  
        grecaptcha.ready(function() {
            grecaptcha.execute('6LdJQMIbAAAAAM7mE9l0k8wR1KEVQcg4cNjlTv19', {action: 'subscribe_newsletter'}).then(function(token) {
                $('#register-form').prepend('<input type="hidden" name="token" value="' + token + '">');
                $('#register-form').prepend('<input type="hidden" name="action" value="subscribe_newsletter">');
				if(!$("small.help-block").is(":visible")){
					$('#register-form').unbind('submit').submit();
				}
            });;
        });
  });
  </script>
<script>
    $(function () {
        $('.dm_phone,.dm_fax').mask('(SSS) SSS-SSSS', {translation: {'S': {pattern: /[a-zA-Z0-9]/, optional: true}}});
        $('.dm_npi').mask('0000000000');
        $('.dm_state').mask('SS');
        $('.dm_zip5').mask('00000');
        $('.dm_zip4').mask('0000');
    });
    var api_site_url = '{{url("/")}}';
    $(document).on('change', '.js-same-as-address', function () {
        if ($(this).prop('checked') == true) {
            $('.js-practice-mailing-address').css("display", "none");
        } else {
            $('.js-practice-mailing-address').css("display", "block");
        }
    });

    function emailValidation(value) {
        var email_length = value.length;
        var regexp = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
        if (email_length > 0) {
            if ((email_length >= 100) && (!regexp.test(value) || regexp.test(value))) {
                return 'Only 100 characters allowed';
            } else if (!regexp.test(value)) {
                return 'Email is not valid ';
            }
            return true;
        }
        return true;
    }

    function phoneValidation(value, error, ext_length, ext_msg) {
        var re = new RegExp(/^[0-9() -\s]+$/);
        var find_any_numeric = value.replace(/[^0-9]/gi, ''); // Replace everything that is not a number with nothing
        var number = parseInt(find_any_numeric, 10); // Always hand in the correct base since 010 != 10 in js
        if (!value.match(/[a-zA-Z0-9]/)) {
            return (ext_length > 0) ? ext_msg : true;
        }
        if (value != '' && re.test(value) == false) {
            return 'Only Numbers allowed';
        }
        if (number == 0 && find_any_numeric.length >= 10) {
            return 'Number looks invalid';
        } else if (value != '' && value != "(") {
            if (value.search("\\(\[0-9]{3}\\\)\\s[0-9]{3}\-\[0-9]{4}") == -1 && value.search("\\(\[0-9]{3}\\\)[0-9]{3}\-\[0-9]{4}") == -1) {
                return error;
            } else
                return true;
        }
        return (ext_length > 0) ? ext_msg : true;
    }

    $('.js-practice-add').bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled, :hidden',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            email: {
                validators: {
                    notEmpty: {
                        message: 'Enter email'
                    },
                    remote: {
                        message: 'Email ID already exist',
                        url: api_site_url + '/userEmailValidate',
                        data: {
                            'email': $('input[name="email"]'),
                            'user_id': $('input[name="user_id"]').val(),
                            '_token': $('input[name="_token"]').val()
                        },
                        type: 'POST'
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator) {
                            var response = emailValidation(value);
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
            },
            name: {
                validators: {
                    notEmpty: {
                        message: 'Enter name'
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: 'Alphabets only should accept'
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
                            } else if (c_pwd != '' && pwd != c_pwd) {
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
            con_password: {
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator) {
                            var pwd = validator.getFieldElements('password').val();
                            var c_pwd = value;
                            if (c_pwd == '') {
                                return {
                                    valid: false,
                                    message: 'Enter confirm password'
                                };
                            }
                            if (pwd != '') {
                                if (c_pwd == '') {
                                    var msg = '{{ trans("admin/adminuser.validation.confirmpassword") }}';
                                } else if (pwd != c_pwd)
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
            practice_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter practice name'
                    },
                    remote: {
                        message: 'Practice Name already exist',
                        url: api_site_url + '/practiceNameValidate',
                        data: {
                            'practice_name': $('input[name="practice_name"]'),
                            '_token': $('input[name="_token"]').val()
                        },
                        type: 'POST'
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: 'Alphabets only should accept'
                    }
                }
            },
            phone: {
                message: 'Enter phone number',
                validators: {
                    notEmpty: {
                        message: 'Enter phone number'
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator) {
                            var mobile_msg = 'Phone no. must be 10 digits';
                            var response = phoneValidation(value, mobile_msg);
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
            },
            npi: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    notEmpty: {
                        message: 'Enter NPI'
                    },
                    regexp: {
                        regexp: /^\d{10}$/,
                        message: 'NPI must be 10 digits'
                    }
                }
            },
            speciality_id: {
                validators: {
                    notEmpty: {
                        message: 'Select speciality'
                    }
                }
            },
            taxanomy_id: {
                validators: {
                    notEmpty: {
                        message: 'Select taxonomy'
                    }
                }
            },
            address_line_1: {
                validators: {
                    notEmpty: {
                        message: 'Enter address line 1'
                    }
                }
            },
            city: {
                validators: {
                    notEmpty: {
                        message: 'Enter city'
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: 'Alphabets only should accept'
                    }
                }
            },
            state: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    notEmpty: {
                        message: 'Select state'
                    },
                    regexp: {
                        regexp: /^[A-Za-z]{2}$/,
                        message: 'Must be 2 Characters'
                    }
                }
            },
            zip5: {
                message: 'Enter zip code 5',
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Enter zip code 5'
                    },
                    regexp: {
                        regexp: /^\d{5}$/,
                        message: 'Zip code must be 5 digits'
                    }
                }
            },
            zip4: {
                message: 'Enter zip code 4',
                trigger: 'change keyup',
                validators: {
                    regexp: {
                        regexp: /^\d{4}$/,
                        message: 'Zip code must be 4 digits'
                    }
                }
            },
            mailing_address_line_1: {
                validators: {
                    notEmpty: {
                        message: 'Enter address line 1'
                    }
                }
            },
            mailing_city: {
                validators: {
                    notEmpty: {
                        message: 'Enter city'
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: 'Alphabets only should accept'
                    }
                }
            },
            mailing_state: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    notEmpty: {
                        message: 'Select state'
                    },
                    regexp: {
                        regexp: /^[A-Za-z]{2}$/,
                        message: 'Must be 2 Characters'
                    }
                }
            },
            mailing_zip5: {
                message: 'Enter zip code 5',
                trigger: 'change keyup',
                validators: {
                    regexp: {
                        regexp: /^\d{5}$/,
                        message: 'Zip code must be 5 digits'
                    }
                }
            },
            mailing_zip4: {
                message: 'Enter zip code 4',
                trigger: 'change keyup',
                validators: {
                    regexp: {
                        regexp: /^\d{4}$/,
                        message: 'Zip code must be 4 digits'
                    }
                }
            }
        }
    });

    $("#js-speciality-change").change(function () {
        selectfacilitytexonomy($(this).val());
    });

    function selectfacilitytexonomy(spaciality_id) {
        $.ajax({
            type: "GET",
            url: api_site_url + '/gettaxanomies',
            data: 'specialities_id=' + spaciality_id,
            success: function (data) {
                $("#js_drop_down").find('option:gt(0)').remove();
                $("#taxanomies-list").html(data);
                $("#taxanomies-list").val($('#taxanomies-list option:nth-child(1)').val()).trigger('change');
            }
        });
    }


    function password_name(value) {
        var atleastone_letter_lang_err_msg = 'Atleast one alpha character is must';
        var atleastone_number_lang_err_msg = 'Atleast one numeric is must';
        var min_length_lang_err_msg = 'Password must be minimum 6 letter';

        if (value != '') {
            if (!value.match(/[a-zA-Z]/g))
                return atleastone_letter_lang_err_msg;
            if (!value.match(/[0-9]/g))
                return atleastone_number_lang_err_msg;
        }
        if (value != "" && value.length < 6) {
            return min_length_lang_err_msg;
        }
        return true;
    }

    // This function used for sidebar notification
    function js_sidebar_notification(type, msg) {
        if (msg != '') {
            $("#show_error_type").html(type);
            $("#show_error_msg").html(msg);
            if (type == 'success')
                $('.snackbar-div').removeClass('error').addClass(type);
            else
                $('.snackbar-div').removeClass('success').addClass(type);
            $('.snackbar-div').addClass('show');
            if (type != 'error') {
                setTimeout(function () {
                    $('.snackbar-div').removeClass('show');
                    $('.snackbar-div').removeClass(type);
                }, 2500);
            } else {
                setTimeout(function () {
                    //$('.snackbar-div').removeClass(type);
                }, 2500);
            }
        }
    }


    $(document).on('change', '.js-state', function () {
        if ($(this).val() == 'AL' || $(this).val() == 'AR' || $(this).val() == 'IL' || $(this).val() == 'IA' || $(this).val() == 'LA' || $(this).val() == 'MN' || $(this).val() == 'MS' || $(this).val() == 'MO' || $(this).val() == 'OK' || $(this).val() == 'WI') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Chicago">America/Chicago</option>');
        } else if ($(this).val() == 'AZ' || $(this).val() == 'CO' || $(this).val() == 'MT' || $(this).val() == 'NM' || $(this).val() == 'UT' || $(this).val() == 'WY') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Chicago">America/Phoenix</option>');
        } else if ($(this).val() == 'CA' || $(this).val() == 'WA') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Los_Angeles">America/Los_Angeles</option>');
        } else if ($(this).val() == 'CT' || $(this).val() == 'DE' || $(this).val() == 'GA' || $(this).val() == 'ME' || $(this).val() == 'MD' || $(this).val() == 'MA' || $(this).val() == 'NH' || $(this).val() == 'NJ' || $(this).val() == 'NY' || $(this).val() == 'NC' || $(this).val() == 'OH' || $(this).val() == 'PA' || $(this).val() == 'RI' || $(this).val() == 'SC' || $(this).val() == 'VT' || $(this).val() == 'VA' || $(this).val() == 'WV') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/New_York">America/New_York</option>');
        } else if ($(this).val() == 'FL' || $(this).val() == 'IN' || $(this).val() == 'KY' || $(this).val() == 'MI' || $(this).val() == 'TN') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/New_York">America/New_York</option><option value="America/Chicago">America/Chicago</option>');
        } else if ($(this).val() == 'ID') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Phoenix">America/Phoenix</option><option value="America/Los_Angeles">America/Los_Angeles</option>');
        } else if ($(this).val() == 'KS' || $(this).val() == 'NE' || $(this).val() == 'ND' || $(this).val() == 'SD' || $(this).val() == 'TX') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Chicago">America/Chicago</option><option value="America/Phoenix">America/Phoenix</option>');
        } else if ($(this).val() == 'AK') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Anchorage">America/Anchorage</option><option value="America/Adak">America/Adak</option>');
        } else if ($(this).val() == 'HI') {
            $('select[name="timezone"] option').remove();
            $('select[name="timezone"]').append('<option value="America/Adak">America/Adak</option>');
        }
    });

</script>
@if(Session::has('success'))
<script type="text/javascript">
    $(document).ready(function () {
        msg = '<?php echo Session::get('success'); ?>';
        js_sidebar_notification('success', msg);
    })
</script>
@endif

@if(Session::has('error'))
<script>
    $(document).ready(function () {
        msg = '<?php echo Session::get('error'); ?>';
        js_sidebar_notification('error', msg);
    })
</script>
@endif

@endpush