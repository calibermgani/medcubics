//USPS Address operations
// USPS Address Check
$(document).on('blur click', '.js-address-check', function(){
    var count = 1;
    var current_action_id = $(this).attr('id');
    var check_either = '';
    var current_form_id = $(this).parents("form").attr("id");
    // Get current type class name to fetch type details ///
    var current_address_class = $(this).parents("div .js-address-class").attr("id");
    address_type = $('#' + current_address_class + ' .js-address-type').val();
    address_type_id = $('#' + current_address_class + ' .js-address-type-id').val();
    address_type_category = $('#' + current_address_class + ' .js-address-type-category').val();

    /// Get fields id with its class name - To get value using id ///   
    $('#' + current_address_class + ' .js-address-check').each(function () {
        if (count == 1) {
            address1 = $(this).val();
            address1_id_name = $(this).attr('id');
            address1_name = $(this).attr('name');
            if (current_action_id == address1_id_name)
                check_either = 'address';
        } else if (count == 2) {
            city = $(this).val();
            city_id_name = $(this).attr('id');
            city_name = $(this).attr('name');
            if (current_action_id == city_id_name)
                check_either = 'city';
        } else if (count == 3) {
            state = $(this).val();
            state_id_name = $(this).attr('id');
            state_name = $(this).attr('name');
            if (current_action_id == state_id_name)
                check_either = 'state';
        } else if (count == 4) {
            zip5 = $(this).val();
            zipcode5_id_name = $(this).attr('id');
            zipcode5_name = $(this).attr('name');
            if (current_action_id == zipcode5_id_name)
                check_either = 'zip5';
        } else if (count == 5) {
            zip4 = $(this).val();
            zipcode4_id_name = $(this).attr('id');
            zipcode4_name = $(this).attr('name');
            if (current_action_id == zipcode4_id_name)
                check_either = 'zip4';
        }
        count++;
    });

    //Check for "Same as patient address" checkbox - since patient address will have been validated there is no need to check again
    var same_as_patient_checkbox = $(this).parents("form").find(".js-same_as_patient_address-v2").length;
    //Forms which do not have the checkbox can directly call the AJAX method so its default to "true"
    var sameAddress = true;

    if (same_as_patient_checkbox != 0) {
        if ($('input:checkbox.js-same_as_patient_address-v2').prop('checked') == true) {
            sameAddress = false;
        }
        else {
            sameAddress = true;
        }
    }

    if (address1 != '' && sameAddress && ((city != '' && state != '') || (zip5 != '' && zip5 > 0))) {
        if (city != '' && state != '' && zip5 != '') {
            check_either = 'address';
        } else if (zip5 != '' && (city == '' || state == '')) {
            check_either = 'zip5';
        } else if (zip5 == '' && city != '' && state != '') {
            check_either = 'city';
        }
        var pars = 'address1=' + address1 + '&city=' + city + '&state=' + state + '&zip5=' + zip5 + '&zip4=' + zip4 + '&current_hit=' + check_either;
        $('#' + current_address_class + ' .js-address-success').addClass('hide');
        $('#' + current_address_class + ' .js-address-error').addClass('hide');
        $('#' + current_address_class + ' .js-address-loading').removeClass('hide');
        $.ajax({
            url: api_site_url + '/api/addresscheck',
            dataType: 'json',
            type: 'post',
            data: pars,
            success: function (data) {
                if (data['status'] == 'success') {
                    result_address1 = displayNameFormat(data['data']['address1'][0]);
                    result_city = displayNameFormat(data['data']['city'][0]);
                    result_state = data['data']['state'][0];
                    result_zip5 = data['data']['zip5'][0];
                    result_zip4 = data['data']['zip4'][0];

                    /// Replace USPS return values in address fields ///
                    $('#' + current_address_class + ' #' + address1_id_name).val(result_address1);
                    $('#' + current_address_class + ' #' + city_id_name).val(result_city);
                    $('#' + current_address_class + ' #' + state_id_name).val(result_state);
                    $('#' + current_address_class + ' #' + zipcode5_id_name).val(result_zip5);
                    if (typeof result_zip4  !== "undefined"){
                    $('#' + current_address_class + ' #' + zipcode4_id_name).val(result_zip4);
                    }

                    /// Replace USPS return values in pop up options and input for address flag ///
                    $('#' + current_address_class + ' .js-address-address1').val(result_address1);
                    $('#' + current_address_class + ' .js-address-city').val(result_city);
                    $('#' + current_address_class + ' .js-address-state').val(result_state);
                    $('#' + current_address_class + ' .js-address-zip5').val(result_zip5);
                    if (typeof result_zip4  !== "undefined"){
                    $('#' + current_address_class + ' .js-address-zip4').val(result_zip4);
                    }
                    $('#' + current_address_class + ' .js-address-is-address-match').val('Yes');

                    /// Show tick icon ///
                    $('#' + current_address_class + ' .js-address-success').removeClass('hide');
                    $('#' + current_address_class + ' .js-address-error').addClass('hide');
                    $('#' + current_address_class + ' .js-address-loading').addClass('hide');
                    if ($('#' + current_form_id).hasClass('js-v2-common-info-form')) {
                        $('.medcubicsform:not(".js-v2-common-info-form")#' + current_form_id).bootstrapValidator('revalidateField', address1_name);
                        $('.medcubicsform:not(".js-v2-common-info-form")#' + current_form_id).bootstrapValidator('revalidateField', city_name);
                        $('.medcubicsform:not(".js-v2-common-info-form")#' + current_form_id).bootstrapValidator('revalidateField', state_name);
                        $('.medcubicsform:not(".js-v2-common-info-form")#' + current_form_id).bootstrapValidator('revalidateField', zipcode5_name);
                        $('.medcubicsform:not(".js-v2-common-info-form")#' + current_form_id).bootstrapValidator('revalidateField', zipcode4_name);
                    } else {
                        $('#' + current_form_id).bootstrapValidator('revalidateField', address1_name);
                        $('#' + current_form_id).bootstrapValidator('revalidateField', city_name);
                        $('#' + current_form_id).bootstrapValidator('revalidateField', state_name);
                        $('#' + current_form_id).bootstrapValidator('revalidateField', zipcode5_name);
                        $('#' + current_form_id).bootstrapValidator('revalidateField', zipcode4_name);
                    }
                }
                else if (data['status'] == 'error' && data.message != 'no_validation') {
                    /// Set usps address status and error message for address flag ///          
                    $('#' + current_address_class + ' .js-address-is-address-match').val('No');
                    $('#' + current_address_class + ' .js-address-error-message').val(displayNameFormat(data.message[0]));

                    /// Show wrong icon ///
                    $('#' + current_address_class + ' .js-address-success').addClass('hide');
                    $('#' + current_address_class + ' .js-address-error').removeClass('hide');
                    $('#' + current_address_class + ' .js-address-loading').addClass('hide');
                }
                else if (data['status'] == 'error' && data.message == 'no_validation') {
                    $('#' + current_address_class + ' .js-address-loading').addClass('hide');
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    } else {
        $('#' + current_address_class + ' .js-address-is-address-match').val('No');
        $('#' + current_address_class + ' .js-address-success').addClass('hide');
        $('#' + current_address_class + ' .js-address-error').addClass('hide');
        $('#' + current_address_class + ' .js-address-loading').addClass('hide');
    }
});

//      Displays Address in a popup
$(document).on('click', '.js-address-success', function () {
    current_address_class = $(this).parents("div .js-address-class").attr("id");
    if (current_address_class != null) {
        $('.modal_city').html($('#' + current_address_class + ' .js-address-city').val());
        $('.modal_state').html($('#' + current_address_class + ' .js-address-state').val());
        $('.modal_zip5').html($('#' + current_address_class + ' .js-address-zip5').val() + '-' + $('#' + current_address_class + ' .js-address-zip4').val());
        $('#modal_address').html($('#' + current_address_class + ' .js-address-address1').val());
        $('#modal_city').html($('#' + current_address_class + ' .js-address-city').val());
        $('#modal_state').html($('#' + current_address_class + ' .js-address-state').val());
        if (($('#' + current_address_class + ' .js-address-zip4').val() != '') && ($('#' + current_address_class + ' .js-address-zip4').val() != 0) && typeof $('#' + current_address_class + ' .js-address-zip4').val() != "undefined")
            $('#modal_zip5').html($('#' + current_address_class + ' .js-address-zip5').val() + '-' + $('#' + current_address_class + ' .js-address-zip4').val());
        else
            $('#modal_zip5').html($('#' + current_address_class + ' .js-address-zip5').val());
        $('#modal_show_error_message').addClass('hide');
        $('#modal_show_success_message').removeClass('hide');
    }
});

//      Displays error message in popup
$(document).on('click', '.js-address-error', function () {
    current_address_class = $(this).parents("div .js-address-class").attr("id");
    $('#modal_show_error_message').html($('#' + current_address_class + ' .js-address-error-message').val());
    $('#modal_show_success_message').addClass('hide');
    $('#modal_show_error_message').removeClass('hide').addClass('show');
});


/*** Address fields function start   ***/
function addressValidation(value, required) {
    var value = value.trim();
    var address_length = value.length;
    var regexp = new RegExp(/^[A-Za-z0-9 ]+$/);
    if (address_length == 0 && required == "required") {
        return address1_lang_err_msg;
    } else if (address_length > 0) {
        if ((address_length > address_max_defined_length) && (!regexp.test(value) || regexp.test(value))) {
            return city_max_length_lang_err_msg;
        }
        /* else if(!regexp.test(value)) {
         return alphanumericspace_lang_err_msg;
         } */
        return true;
    }
    return true;
}

function cityValidation(value, required) {
    var city_length = value.length;
    var regexp = new RegExp(/^[A-Za-z ]+$/);
    if (city_length == 0 && required == "required") {
        return city_lang_err_msg;
    } else if (city_length > 0) {
        if ((city_length >= city_max_defined_length) && (!regexp.test(value) || regexp.test(value))) {
            return city_max_length_lang_err_msg;
        }
        else if (!regexp.test(value)) {
            return alphaspace_lang_err_msg;
        }
        return true;
    }
    return true;
}

function stateValidation(value, required) {
    var state_length = value.length;
    if (state_length == 0 && required == "required") {
        return state_lang_err_msg;
    }
    if (state_length > 0 && state_length < state_max_defined_length) {
        return state_limit_lang_err_msg;
    }
    return true;
}

function zip5Validation(value, required) {
    var zip5_length = value.length;
    if (zip5_length == 0 && required == "required") {
        return zip5_lang_err_msg;
    }
    if (zip5_length > 0 && zip5_length < zipcode5_max_defined_length) {
        return zip5_limit_lang_err_msg;
    }
    return true;
}

function zip4Validation(value) {
    var zip4_length = value.length;
    if (zip4_length > 0 && zip4_length < zipcode4_max_defined_length) {
        return zip4_limit_lang_err_msg;
    }
    return true;
}

function lengthValidation(value, variable_name, regex, regex_msg) {
    if ((regex == '' || regex == null) || (eval(regex).test(value))) {
        var variable_length = value.length;
        var get_variable_length = eval(variable_name + "_max_defined_length");
        var get_max_length_lang_err_msg = eval(variable_name + "_max_length_lang_err_msg");
        if (variable_length > 0 && variable_length > get_variable_length) {
            return get_max_length_lang_err_msg;
        }
        return true;
    } else if (!eval(regex).test(value)) {
        return regex_msg;
    }
    return true;
}
/*** Address fields function end   ***/

/*** All letter caps start ****/
$(document).on("keyup", ".js_all_caps_format", function () {
    var str = $(this).val().replace(/ /g, '');
    var str_upper = str.toUpperCase();
    var start = this.selectionStart,
        end = this.selectionEnd;
    $(this).val(str_upper);
    this.setSelectionRange(start, end);
});
/*** All letter caps end ****/

/*** Starts - Convert string to lower case and first letter caps format ***/
function displayNameFormat(output) {
    output = output.toLowerCase();
    var words = output.split(/(\s|-)+/),
        output = [];
    for (var i = 0, len = words.length; i < len; i += 1) {
        if (words[i].length > 0) {
            output.push(words[i][0].toUpperCase() + words[i].toLowerCase().substr(1));
        }
    }
    return output.join('');
}
/*** Ends - Convert string to lower case and first letter caps format ***/

/*** City State Valitation starts ***/
$(document).on('keyup', '.medcubicsform:not(".js-v2-common-info-form") .js-address-check', function () {
    var current_name = $(this).attr("name");
    var zip5 = $(this).hasClass("dm-zip5");
    var zip4 = $(this).hasClass("dm-zip4");
    var form_name = $(this).parents("form").attr("name");

    if (current_name.indexOf("city") >= 0 || zip5 == true) {
        var another_name = $(this).closest('div').next().next().find('input').attr("name");
        if (another_name == null || another_name == "")
            var another_name = $(this).closest('div').next().find('input').attr("name");
        cityStatevalitation(another_name, form_name);
    } else if (current_name.indexOf("state") >= 0 || zip4 == true) {
        var another_name = $(this).closest('div').prev().prev().find('input').attr("name");
        if (another_name == null || another_name == "")
            var another_name = $(this).closest('div').prev().find('input').attr("name");

        cityStatevalitation(another_name, form_name);
    }
});

function spaceReplace(result) {
    if (result == " " || result == "() ") {
        return "";
    }
    if (!result.match(/[a-zA-Z0-9]/)) {
        return "";
    }
    /* var check_spl = result.replace(/[^a-zA-Z0-9]/g, '');
    if (check_spl.length < 3) {
        var check_char = result.split(")");
        return check_char[0];
    }
    else if (check_spl.length < 6) {
        var check_char = result.split("-");
        return check_char[0];
    } */
    var regExp = /^[a-zA-Z0-9(]*$/;
    for (var i = 0, len = result.length; i < len; i++) {
        if (regExp.test(result[i])) {
            var first_char = i;
            var str = result.substring(i, result.length);
            return str;
        }
    }
}

function phoneValidation(value, error, ext_length, ext_msg) {
    var re = new RegExp(/^[0-9() -\s]+$/);
    var find_any_numeric = value.replace(/[^0-9]/gi, ''); // Replace everything that is not a number with nothing
    var number = parseInt(find_any_numeric, 10); // Always hand in the correct base since 010 != 10 in js
    if (!value.match(/[a-zA-Z0-9]/)) {
        return (ext_length > 0) ? ext_msg : true;
    }
    if (value != '' && re.test(value) == false) {
        return only_numeric_lang_err_msg;
    }
    if (number == 0 && find_any_numeric.length >= 10) {
        return phone_number_valid_lang_err_msg;
    } else if (value != '' && value != "(") {
        if (value.search("\\(\[0-9]{3}\\\)\\s[0-9]{3}\-\[0-9]{4}") == -1 && value.search("\\(\[0-9]{3}\\\)[0-9]{3}\-\[0-9]{4}") == -1) {
            return error;
        }
        else
            return true;
    }
    return (ext_length > 0) ? ext_msg : true;
}

function emailValidation(value) {
    var email_length = value.length;
    var regexp = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    if (email_length > 0) {
        if ((email_length >= 100) && (!regexp.test(value) || regexp.test(value))) {
            return email_max_length_lang_err_msg;
        } else if (!regexp.test(value)) {
            return email_valid_lang_err_msg;
        }
        return true;
    }
    return true;
}

/*** Address fields function start   ***/


// NPI Check
$(document).on('blur', '.js-npi-check', function () {
    npi_value = $(this).val();
    var cur_form = $(this);
    current_npi_id_name = $(this).attr('id');
    if ($('#is_provider').length)
        var is_provider = 'yes';
    else
        var is_provider = 'no';
    var pars = 'npi=' + npi_value + '&is_provider=' + is_provider;

    $('input[type=hidden][name="valid_npi_bootstrap"]').val('');

    // When we click the data from model window both model gets hidded so disabled the close activity done by revathi on july 13 2016
    if ($('#ModelForm').length) {
        $('.npi-close').removeAttr('data-dismiss');
    }
    // ends close activity
    if (npi_value != '') {
        if (current_npi_id_name == 'group_npi') {
            $('.js-npi-group-success').addClass('hide');
            $('.js-npi-group-error').addClass('hide');
            $('.js-npi-group-loading').removeClass('hide');
        } else {
            $('.js-npi-individual-success').addClass('hide');
            $('.js-npi-individual-error').addClass('hide');
            $('.js-npi-individual-loading').removeClass('hide');
        }
        $.ajax({
            url: api_site_url + '/api/npicheck',
            dataType: 'json',
            type: 'post',
            data: pars,
            success: function (data, textStatus, jQxhr) {
                $("#provider_types_id").select2({ 'disabled': false });

                if (data['status'] == 'success') {
                    $.each(data['data']['npi_details'], function (key, item) {
                        $('#' + key).val(item);
                        $('#modal_' + key).html(item);
                    });
                    var msg = '';
                    if ($('input[name="entity_type"]:checked').val() == 'Individual' && data['data']['provider']['enumeration_type'] == 'NPI-2') {
                        $("#provider_types_id").select2("val", "");
                        $("#provider_types_id").select2({ 'disabled': true });
                        $('.js-npi-check').closest('div.form-group').removeClass('has-success').addClass('has-error');
                        $('.js-npi-check').closest('div').find("i").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                        //js_alert_popup("Sorry you can't add group 2 NPI. Please add group 1");
                        var msg = "Sorry you can't add Group NPI in Individual entity";
                        $(cur_form).closest('div').find('input.js-npi-check').val('');
                        var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check').attr('name');
                        if (!$('#ModelForm').length) {
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));
                        }
                    }
                    
                    if ($('input[name="entity_type"]:checked').val() == 'Group' && data['data']['provider']['enumeration_type'] == 'NPI-1') {
                        $("#provider_types_id").select2("val", "");
                        $("#provider_types_id").select2({ 'disabled': true });
                        $('.js-npi-check').closest('div.form-group').removeClass('has-success').addClass('has-error');
                        $('.js-npi-check').closest('div').find("i").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                        //js_alert_popup("Sorry you can't add group 2 NPI. Please add group 1");
                        var msg = "Sorry you can't add Individual NPI in Group entity";
                        $(cur_form).closest('div').find('input.js-npi-check').val('');
                        var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check').attr('name');
                        if (!$('#ModelForm').length) {
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));
                        }
                    }
                    if (is_provider == 'yes') {
                        if (data['data']['provider']['enumeration_type'] != 'NPI-2') {
                            $.each(data['data']['provider'], function (key, item) {
                                if (key != 'provider_degrees_id' && key != 'gender_f' && key != 'gender_m') {
                                    var item = (key == "middle_name") ? item[0] : item;
                                    $('#' + key).val(item);
                                }
                            });
                        }

                        if (data['data']['provider']['enumeration_type'] == 'NPI-2') {
                            $("#provider_types_id").select2("val", "");
                            $("#provider_types_id").select2({ 'disabled': true });
                            $('.js-npi-check').closest('div.form-group').removeClass('has-success').addClass('has-error');
                            $('.js-npi-check').closest('div').find("i").removeClass('glyphicon-ok').addClass('glyphicon-remove');
                            //js_alert_popup("Sorry you can't add group 2 NPI. Please add group 1");
                            var msg = "Sorry you can't add Group NPI. Please add Individual NPI";
                            $(cur_form).closest('div').find('input.js-npi-check').val('');
                            var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check').attr('name');
                            if (!$('#ModelForm').length) {
                                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));
                            }
                        } else {
                            //var msg = "Sorry you can't add Individual NPI in Group entity";
                            $('#npi_field_group').addClass('hide');
                            $('#npi_field_individual').removeClass('hide');
                            $('.js-npi-check').closest('div.form-group').removeClass('has-error');
                            $('.js-npi-check').closest('div').find("i").removeClass('glyphicon-remove');
                            if ($('#ModelForm').length) {
                                $('form#ModelForm').bootstrapValidator('revalidateField', 'first_name');
                                $('form#ModelForm').bootstrapValidator('revalidateField', 'last_name');
                            } else {
                                $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('provider_types_id');
                            }
                        }
                    }
                    if (current_npi_id_name == 'group_npi') {
                        if ($('input[name="entity_type"]:checked').val() == 'Group' && data['data']['provider']['enumeration_type'] == 'NPI-1') {
                            $('.js-npi-group-success').addClass('hide');
                        } else {
                            //$('.snackbar-div').trigger('click'); // Close error msg if exist                                
                            $('.js-npi-group-success').removeClass('hide');
                        }
                        $('.js-npi-group-error').addClass('hide');
                        $('.js-npi-group-loading').addClass('hide');
                    } else {
                        if ($('input[name="entity_type"]:checked').val() == 'Individual' && data['data']['provider']['enumeration_type'] == 'NPI-2') {
                            $('.js-npi-individual-success').addClass('hide');
                        } else {
                            //$('.snackbar-div').trigger('click'); // Close error msg if exist
                            $('.js-npi-individual-success').removeClass('hide');
                        }
                        $('.js-npi-individual-error').addClass('hide');
                        $('.js-npi-individual-loading').addClass('hide');
                    }
                    if (data['data']['provider']['enumeration_type'] == 'NPI-2' && is_provider == 'yes') {
                        $('.js-npi-individual-success').addClass('hide');
                        $('.js-npi-individual-error').addClass('hide');
                        $('.js-npi-individual-loading').addClass('hide');
                    }

                    if (data['data']['npi_details']['enumeration_type'] == 'NPI-1') {
                        $('#form-npi-modal .clsNPI1').removeClass('hide');
                        $('#form-npi-modal .clsNPI2').addClass('hide');

                        if ($('#ModelForm').length) {
                            $('form#ModelForm').bootstrapValidator('revalidateField', 'first_name');
                            $('form#ModelForm').bootstrapValidator('revalidateField', 'last_name');
                        } else {
                            if (is_provider == 'yes') {
                                $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', true);
                            }
                        }
                        /*$('.js_provider_ssn').prop("disabled", false);
                         $('.js_provider_dob').prop("disabled", false);
                         $('.js_provider_gender').prop("disabled", false);
                         $('.js_male').iCheck('check');
                         $('#provider_degrees_id').prop("disabled", false);*/
                    } else {
                        $('#form-npi-modal .clsNPI1').addClass('hide');
                        $('#form-npi-modal .clsNPI2').removeClass('hide');
                        if ($('#ModelForm').length) {
                            $('form#ModelForm').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', false);
                        } else {
                            if (is_provider == 'yes') {
                                $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', false);
                            }
                        }
                        /*$('.js_provider_ssn').prop("disabled", false);
                         $('.js_provider_dob').prop("disabled", false);
                         $('.js_provider_gender').prop("disabled", false);
                         $('.js_male').iCheck('check');
                         $('#provider_degrees_id').prop("disabled", false);*/
                    }
                    $('#npi_modal_success').removeClass('hide');
                    $('#npi_modal_error').addClass('hide');

                    $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                    var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check').attr('name');

                    if ($('#ModelForm').length) {
                        $('form#ModelForm').bootstrapValidator('revalidateField', 'first_name');
                        $('form#ModelForm').bootstrapValidator('revalidateField', 'last_name');
                    } else {
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));
                    }

                    if ($('.js-address-check').length) {
                        $('.js-address-check').trigger('blur');
                    }

                } else if (data['message'] != 'no_validation') {
                    $('#enumeration_type').val("");
                    $('#npi_field_group').addClass('hide');
                    $('#npi_field_individual').removeClass('hide');
                    if ($("#first_name").val() == 'a') {
                        $("#first_name").val("");
                        $("#last_name").val("");
                    }
                    $('#npi_error_message').val(displayNameFormat(data['data']['npi_details']['npi_error_message']));
                    $('#is_valid_npi').val(data['data']['npi_details']['is_valid_npi']);
                    $('#modal_npi_error_message').html(displayNameFormat(data['data']['npi_details']['npi_error_message']));

                    if (current_npi_id_name == 'group_npi') {
                        $('.js-npi-group-success').addClass('hide');
                        $('.js-npi-group-error').removeClass('hide');
                        $('.js-npi-group-loading').addClass('hide');
                    } else {
                        $('.js-npi-individual-success').addClass('hide');
                        $('.js-npi-individual-error').removeClass('hide');
                        $('.js-npi-individual-loading').addClass('hide');
                    }
                    $('input[type=hidden][name="valid_npi_bootstrap"]').val('no');

                    var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check').attr('name');
                    if ($('#ModelForm').length) {
                        $('form#ModelForm').bootstrapValidator('revalidateField', 'first_name');
                        $('form#ModelForm').bootstrapValidator('revalidateField', 'last_name');
                        $('form#ModelForm').bootstrapValidator('revalidateField', 'npi');
                    } else {
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));
                    }
                    $('#npi_modal_success').addClass('hide');
                    $('#npi_modal_error').removeClass('hide');

                } else if (data['status'] == 'error' && data['message'] == 'no_validation') {
                    $('.js-npi-individual-loading').addClass('hide');
                    $('.js-npi-group-loading').addClass('hide');
                }
                if(msg!=''){
                    js_sidebar_notification('error', msg);
                }
                $('#js-provider').remove();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
});