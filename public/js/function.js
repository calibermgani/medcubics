/*/////////////////////////////////////////////////////////////////////////////
 Author:    Anitha
 Date:      08 March 2016
 Updated:   Kannan
 
Common functions throughout Medcubics
 ----------- INDEX -------------
 1. Common throughout Medcubics
 2. USPS Address operations
 3. Practice Settings

/*/////////////////////////////////////////////////////////////////////////////

//1.    Common throughout Medcubics
//1.1   Remove Hash in URL
var ser = $(location).attr('href').split("/").splice(0, 3).join("/");
var url_curr = window.location.href;
if (url_curr.indexOf("?") == -1) {
    removeHash();  // Removes query string parameter too, so we put condition
}
function removeHash() {
    window.location.hash = ''; // for older browsers, leaves a # behind
    history.pushState('', document.title, window.location.pathname); // nice and clean
}
//1.2.  Remove Hash on delete
$(".js-delete-confirm,.js_delete_confirm").click(function (e) {
    setTimeout(function () {
        removeHash();
    }, 5);
});
//1.2.  Help Menu click
$(".js-help").click(function (e) {
    $(this).attr("href", '').attr("data-target", "#js-help-modal"); //temprory solution to remove #id
    url = $(this).attr('data-url');

    if (url != '') {
        $.ajax({
            url: url,
            type: 'get',
            data: '',
            success: function (data, textStatus, jQxhr) {
                removeHash();
                split_result = data.split('~~');
                $('#js-help-modal-msg').html(split_result[1]);
                $('#js-help-modal-title').html(split_result[0]);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
});
//1.3.  ReadMore
readMore();
function readMore() {
    jQuery(function () {
        var minimized_elements = $('p.push');
        minimized_elements.each(function () {
            var t = $(this).text();
            if (t.length < 300)
                return;

            $(this).html(
                t.slice(0, 250) + '<span>... </span><a href="#" class="more"><i class="fa fa-plus-circle"></i></a>' +
                '<span style="display:none;">' + t.slice(250, t.length) + ' <a href="#" class="less"><i class="fa fa-minus-circle"></i></a></span>'
            );
        });

        $('a.more', minimized_elements).click(function (event) {
            event.preventDefault();
            $(this).hide().prev().hide();
            $(this).next().show();
        });

        $('a.less', minimized_elements).click(function (event) {
            event.preventDefault();
            $(this).parent().hide().prev().show().prev().show();
        });
    });
}

///////////////////////////////////////////////////////////////////////////////
//2.    USPS Address operations
//2.1   USPS Address Check
// $(".js-address-check" ).trigger( "blur" );
$(document).on('blur', '.js-address-check:visible', function () {
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
        if ($('#' + current_address_class + 'input:checkbox.js-same_as_patient_address-v2').prop('checked') == true) {
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
                    if (typeof result_zip4 !== "undefined") {
                        $('#' + current_address_class + ' #' + zipcode4_id_name).val(result_zip4);
                    }

                    /// Replace USPS return values in pop up options and input for address flag ///
                    $('#' + current_address_class + ' .js-address-address1').val(result_address1);
                    $('#' + current_address_class + ' .js-address-city').val(result_city);
                    $('#' + current_address_class + ' .js-address-state').val(result_state);
                    $('#' + current_address_class + ' .js-address-zip5').val(result_zip5);
                    if (typeof result_zip4 !== "undefined") {
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

                        form_id_val_arr = current_form_id.split('_');
                        if(Array.isArray(form_id_val_arr) && form_id_val_arr[0] == 'v2-insuranceeditform'){
                         //   insurance_form_id = form_id_val_arr[1];
                            insuranceForm(current_form_id);
                            $('#' + current_form_id).data('bootstrapValidator').revalidateField('insured_city');
                            $('#' + current_form_id).data('bootstrapValidator').revalidateField('insured_state');
                            $('#' + current_form_id).data('bootstrapValidator').revalidateField('insured_zip5');
                            $('#' + current_form_id).data('bootstrapValidator').revalidateField('insured_zip4');
                        }

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
//2.2   USPS Success icon click
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
//2.3   USPS Error icon click
//      Displays error message in popup
$(document).on('click', '.js-address-error', function () {
    current_address_class = $(this).parents("div .js-address-class").attr("id");
    $('#modal_show_error_message').html($('#' + current_address_class + ' .js-address-error-message').val());
    $('#modal_show_success_message').addClass('hide');
    $('#modal_show_error_message').removeClass('hide').addClass('show');
});

///////////////////////////////////////////////////////////////////////////////
//3     Practice Settings
//3.1   CPT
//3.1.1 Add to Favorites
$(document).on('click', '.js-favourite-record', function (event) {
    $('select[name="popup_procedure_category"]').val('').trigger('change');
    var url_name = $("li.active").text();
    var url_name = url_name.replace(/\s/g, '').slice(0, 3);
    var id = $(this).attr('data-id');
    var ajax_url = $(this).attr('data-url');
    var obj = this;
    var data_original = $(obj).find("i").attr('data-original-title') + "?";

    $("#js-favourite-message").text(data_original);

    event.stopPropagation();

    $("#js-favourite-confirm")
        .modal({ show: 'false' })
        .one('click', '.confirm', function (e) {
            var confirmation = $(this).text();
            if (confirmation == "Yes") {
                $.ajax({
                    url: ajax_url,
                    dataType: 'json',
                    type: 'get',
                    data: { 'check': 'Yes' },
                    success: function (response) {
                        if (response.validation == "Yes") {
                            $("#js-favourite-category-update")
                                .modal({ show: 'false' })
                                .one('click', '.confirm', function (e) {
                                    var confirmation = $(this).text();
                                    if (confirmation == "Update") {
                                        if ($('select[name="popup_procedure_category"]').val() == null)
                                            category = 'Diagnostic';
                                        else
                                            category = $('select[name="popup_procedure_category"]').val();
                                        $.ajax({
                                            url: ajax_url,
                                            dataType: 'json',
                                            type: 'get',
                                            data: { 'procedure_category': category },
                                            success: function (data, textStatus, jQxhr) {
                                                if (data.success == 0) {
                                                    js_sidebar_notification('success', 'Remove from favorite');
                                                    //CPT favourite in ajax
                                                    if (url_name == "CPT") {
                                                        //CPT favourite in ajax
                                                        $.get(api_site_url + "/listfavourites", function (data) {
                                                            $('.js_cpt_favourites').html(data);
                                                            loaddatatablefavcpt();
                                                        });
                                                    }

                                                    $(obj).find("i").attr('data-original-title', 'Are you sure to add to favorites');
                                                    if ($(obj).find("#js-showpage-favourite-msg").length >= 1)
                                                        $(obj).find("#js-showpage-favourite-msg").html('Are you sure to add to favorites');
                                                    $(obj).find("i").removeClass('fa-star');
                                                    $(obj).find("i").addClass('fa-star-o');
                                                } else {
                                                    js_sidebar_notification('success', 'Added favorite');
                                                    $(obj).find("i").attr('data-original-title', 'Are you sure to remove from favorites');
                                                    if ($(obj).find("#js-showpage-favourite-msg").length >= 1)
                                                        $(obj).find("#js-showpage-favourite-msg").html('Are you sure to remove from favorites');
                                                    $(obj).find("i").removeClass('fa-star-o');
                                                    $(obj).find("i").addClass('fa-star');
                                                }
                                                getmodifierandcpt();
                                            },
                                            error: function (jqXhr, textStatus, errorThrown) {
                                                console.log("Add to Favorites: " + errorThrown);
                                            }
                                        });

                                    }
                                });

                        } else {
                            $.ajax({
                                url: ajax_url,
                                dataType: 'json',
                                type: 'get',
                                success: function (data, textStatus, jQxhr) {
                                    if (data.success == 0) {
                                        //CPT favourite in ajax
                                        js_sidebar_notification('success', 'Remove from favorite');
                                        if (url_name == "CPT") {
                                            //CPT favourite in ajax
                                            $.get(api_site_url + "/listfavourites", function (data) {
                                                $('.js_cpt_favourites').html(data);
                                                loaddatatablefavcpt();
                                            });
                                        }

                                        $(obj).find("i").attr('data-original-title', 'Are you sure to add to favorites');
                                        if ($(obj).find("#js-showpage-favourite-msg").length >= 1)
                                            $(obj).find("#js-showpage-favourite-msg").html('Are you sure to add to favorites');
                                        $(obj).find("i").removeClass('fa-star');
                                        $(obj).find("i").addClass('fa-star-o');
                                    } else {
                                        js_sidebar_notification('success', 'Added favorite');
                                        $(obj).find("i").attr('data-original-title', 'Are you sure to remove from favorites');
                                        if ($(obj).find("#js-showpage-favourite-msg").length >= 1)
                                            $(obj).find("#js-showpage-favourite-msg").html('Are you sure to remove from favorites');
                                        $(obj).find("i").removeClass('fa-star-o');
                                        $(obj).find("i").addClass('fa-star');
                                    }
                                    getmodifierandcpt();
                                },
                                error: function (jqXhr, textStatus, errorThrown) {
                                    console.log("Add to Favorites: " + errorThrown);
                                }
                            });
                        }

                    }
                })
            }
        });
});

/* $(document).on('change','select[name="popup_procedure_category"]',function(){
	if($(this).val() == ''){
		$('.popup_procedure_category_btn').removeAttr('data-dismiss','modal');
		$('.proc-category-error').html('<i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Procedure category is mandatory to add a CPT as favorites </i>');
	}else{ 
		$('.popup_procedure_category_btn').attr('data-dismiss','modal');
		$('.proc-category-error').html("");
	}
});

$(document).on('click','.popup_procedure_category_btn',function(){ 
	if($('select[name="popup_procedure_category"]').val() == ''){
		$('.proc-category-error').html('<i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Procedure category is mandatory to add a CPT as favorites </i>');
	}
}); */


//3.2   Provider
//3.2.1 Checkbox Click: Provider Details - ETIN Type / Tax ID
$(document).on('ifToggled click', '.etin_type', function (event) {
    if ($(this).is(':checked')) {
        category = this.value;
        if (category == 'SSN') {
            $("#etin_ssn").attr('checked', true);
            $("#etin_tax").attr('checked', false);
            setTimeout(function () {
                //$("#etin_type_number").inputmask("mask", {"mask": "999-99-9999"});
                $("#etin_type_number").removeClass('dm-tax-id');
                $("#etin_type_number").addClass('dm-ssn');
                $("#document_add_modal_link_tax_id_part").hide();
                $("#document_add_modal_link_ssn_part").show();
            }, 500);
        }
        else if (category == 'TAX ID') {
            $("#etin_tax").attr('checked', true);
            $("#etin_ssn").attr('checked', false);
            setTimeout(function () {
                //$("#etin_type_number").inputmask({"mask": "99-9999999"}); 
                $("#etin_type_number").removeClass('dm-ssn');
                $("#etin_type_number").addClass('dm-tax-id');
                $("#document_add_modal_link_ssn_part").hide();
                $("#document_add_modal_link_tax_id_part").show();
            }, 500);
        }
    }
});
//3.3   Practice
//3.3.1 Checkbox click: Mailing address same as pay to address
$(document).on('ifToggled click change', '.js-same-as-address', function () {
    if ($(this).is(':checked')) {
        $('#mail_add_1').val($('#pay_add_1').val());
        $('#mail_add_2').val($('#pay_add_2').val());
        $('#mail_city').val($('#pay_city').val());
        $('#mail_state').val($('#pay_state').val());
        $('#mail_zip5').val($('#pay_zip5').val());
        $('#mail_zip4').val($('#pay_zip4').val());

        $('#js-address-mailling-address .js-address-address1').val($('#js-address-pay-to-address .js-address-address1').val());
        $('#js-address-mailling-address .js-address-city').val($('#js-address-pay-to-address .js-address-city').val());
        $('#js-address-mailling-address .js-address-state').val($('#js-address-pay-to-address .js-address-state').val());
        $('#js-address-mailling-address .js-address-zip5').val($('#js-address-pay-to-address .js-address-zip5').val());
        $('#js-address-mailling-address .js-address-zip4').val($('#js-address-pay-to-address .js-address-zip4').val());
        $('#js-address-mailling-address .js-address-is-address-match').val($('#js-address-pay-to-address .js-address-is-address-match').val());
        $('#js-address-mailling-address .js-address-error-message').val($('#js-address-pay-to-address .js-address-error-message').val());
        if ($('#js-address-mailling-address .js-address-is-address-match').val() === 'Yes') {
            /// Show tick icon ///
            $('#js-address-mailling-address .js-address-success').removeClass('hide');
            $('#js-address-mailling-address .js-address-error').addClass('hide');
        } else if ($('#js-address-mailling-address .js-address-is-address-match').val() === 'No') {
            /// Show wrong icon ///
            $('#js-address-mailling-address .js-address-success').addClass('hide');
            $('#js-address-mailling-address .js-address-error').removeClass('hide');
        }
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_add_1');
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_add_2');
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_city');
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_state');
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_zip4');
        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('mail_zip5');
    } else {
        $('#mail_add_1').val('');
        $('#mail_add_2').val('');
        $('#mail_city').val('');
        $('#mail_state').val('');
        $('#mail_zip5').val('');
        $('#mail_zip4').val('');
    }
})

//3.3.2 Uncheck "Mailing address same as pay to address" when address changed
$("#js-address-mailling-address input, #js-address-pay-to-address input").keyup(function () {
    $('.js-same-as-address').removeAttr('checked');
});

//3.3.3 NPI Check
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
                                $('form#ModelForm').bootstrapValidator('revalidateField', 'npi');
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
                if (msg != '' && msg != undefined) {
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

//3.3.4 Entity Type: Show fields based on entity type
$(document).on('ifToggled click change', '.js-entity-type', function () {
    var chk = $(this).is(":checked");
    if (chk == true) {
        if ($(this).val() == 'Individual') {
            $('#js-group-entity-type').addClass('hide');
            $('#js-individual-entity-type').removeClass('hide');
            $('#group_npi').val('');
            $('#group_tax_id').val('');
            $('#npi').val('');
            $('#tax_id').val('');
            $('.js-npi-individual-success').addClass('hide');
            $('.js-npi-individual-error').addClass('hide');
        } else {
            $('#js-group-entity-type').removeClass('hide');
            $('#js-individual-entity-type').addClass('hide');
            $('#group_npi').val('');
            $('#group_tax_id').val('');
            $('#npi').val('');
            $('#tax_id').val('');
            $('.js-npi-group-success').addClass('hide');
            $('.js-npi-group-error').addClass('hide');
        }
    }
});

//3.3.5 Permission page checkbox
$(document).on('ifToggled change', '.js_submenu', function () {
    var chk = $(this).is(":checked");
    var get_class = $(this).attr('class');
    var split_class = get_class.split(' ');
    var cur_class = split_class[0];
    var data_class = $(this).attr('data-class');
    allmoduleCheck(data_class);
    menuCheck(cur_class);
});

$(document).on('ifToggled click change', '.js_menu', function () {
    var check_status = $(this).is(":checked");
    var get_id = $(this).attr('id');
    clas_name = (get_id == "js_select_all") ? "js_submenu" : get_id;
    $("." + clas_name).prop('checked', check_status);
    menuCheck(clas_name);
});

function menuCheck(cur_class) {
    var total_count = 0;
    $("." + cur_class + ".js_submenu").each(function (i) {
        total_count += 1;
    });
    var checked_count = 0;
    var dataclass = $("#" + cur_class + ".js_menu").attr('data-class');
    $('.' + cur_class + '.js_submenu:checked').each(function (i) {
        checked_count += 1;
    });
    if (total_count == checked_count) {
        $("#" + cur_class + ".js_menu").prop('checked', true);
    } else {
        $("#" + cur_class + ".js_menu").prop('checked', false);
    }
    allCheck(".js_submenu");
    allmoduleCheck(dataclass)
}

function allCheck(class_name) {
    var dataclass = $(this).attr('data-class');
    var checked_count = total_count = 0;
    $(class_name).each(function (i) {
        total_count += 1;
    });
    $(class_name + ':checked').each(function (i) {
        checked_count += 1;
    });
    allmoduleCheck(dataclass)
    if (total_count == checked_count) {
        $("#js_select_all").prop('checked', true);
    } else {
        $("#js_select_all").prop('checked', false);
    }
    //$('input[type="checkbox"].flat-red').iCheck('update');
    //$('input[type="checkbox"]#js_select_all').iCheck('update');
    if ($('#js_select_all').is(':checked')) {
        $('input[name="permission_module"]').prop('checked', true);
    } else {
        $('input[name="permission_module"]').prop('checked', false);
    }
}

$(document).on('click change', '#js_practice_mainmodule', function () {
    var data_class = $(this).attr("data_class");
    var status = false;
    if ($(this).is(':checked')) {
        var status = true;
    }
    $('div.' + data_class).find(':checkbox').each(function () {
        $(this).prop('checked', status);
    });
    var permission_module_length = $('input[name="permission_module"]').length;
    var permission_module_checked_len = $('input[name="permission_module"]:checked').length;
    if (permission_module_length == permission_module_checked_len) {
        $("#js_select_all").prop('checked', true);
    } else {
        $("#js_select_all").prop('checked', false);
    }
})

function allmoduleCheck(data_class) {
    if (typeof data_class != 'undefined') {
        var submodule_length = $('input[data-class=' + data_class + ']').length;
        var sub_checked_len = $('input[data-class=' + data_class + ']:checked').length;
        if (submodule_length == sub_checked_len) {
            $('input[data_class=' + data_class + ']').prop('checked', true);
        } else {

            $('input[data_class=' + data_class + ']').prop('checked', false);
        }
    }
}
/*** Set Permission page admin side and Patient registration ends ***/

/* Ends - Same as pay to address for mailling address uncheck when user change values in pay to address or mailling address */

$(document).ready(function () {
    //**create all types of user hide element in admin start**//
    $(".js_customer").hide();
    $(".js_customer_practice").hide();
    $(".js_user_access").hide();
    $(".js_prac_access_app").hide();
    $(".practice_role").hide();
    $(".js_provider").hide();
    //**create all types of user hide element in admin end**//

    /***  patients-> new Appointment POPUP URL Passing Start ***/
    var data = sessionStorage.getItem("New Appointment");
    if (data == "reload") {
        setTimeout(function () {
            $(".js-new_appointment").trigger('click');
            sessionStorage.setItem("New Appointment", "");
        }, 100);
    }
    /***  patients-> new Appointment POPUP URL Passing End ***/

    if ($('.js-check-each').length) {
        var parent_id = $('.js-check-each').attr("data-id");
        $('.' + parent_id + '.js_submenu').each(function (index, element) {
            var id = $(this).attr("id");
            menuCheck(id);
        });
    }
    $('b[role="presentation"]').hide();
    $('.select2-selection__arrow').append('<i class="fa fa-angle-down"></i>');

    // webcam check for default upload type when page loads
    if ($('.js-upload-type').length) {
        radio = $('input[name=upload_type]:checked').val();
        if (radio == 'browse') {
            $('.js-photo').hide();
            $('.js-upload').show();
            $('#webcam_div').hide();
            $('#js-show-webcam').hide();
        } else if (radio == 'scanner') {

            $('.js-scanner').show();
            $('.js-photo').hide();
            $('.js-upload').hide();
            $('#webcam_div').hide();
            $('#js-show-webcam').hide();
        } else {
            $('.js-photo').show();
            $('.js-upload').hide();
            $('#webcam_div').hide();
            $('#js-show-webcam').hide();
        }
    }

    if ($('.js-practice-user').length) {
        radio = $('input[name=practice_user_type]:checked').val();
        if (radio == 'practice_admin') {
            $('.js-practice-user').show();
        } else {
            $('.js-practice-user').hide();
        }
    }
    $(".checkAll").change(function () {
        var parent_id = $(this).attr('id');
        $("." + parent_id).prop('checked', $(this).prop("checked"));
    });

    $(".flat_red").change(function () {
        var parent_class = $(this).attr('data-id');
        if ($('.' + parent_class).length == $('.' + parent_class + ':checked').length)
            $("#" + parent_class).prop('checked', 'checked');
        else
            $("#" + parent_class).prop('checked', '');
    });

    /* Starts - Script for DataTables and sorting */
    if ($("#mcsorting").length) {
        $('#mcsorting').dataTable();
        var oTable = $('#mcsorting').dataTable();
        oTable.fnSearchHighlighting();
    }
    /* Ends - Script for DataTables and sorting */

    /* Starts - Print page */
    $(".js-print").click(function (e) {
        e.preventDefault();
		// First time print option after page load issues fixed 
		setTimeout(function(){jQuery('#js-print-main-div').print();}, 10);
        
    });
    /* payment claim popup - Print page */
    $(document).on('click', '.js-print-popup', function () {
        modelID = $(this).parents("div.modal").attr("id");
        $('#view_transaction').addClass('in');
        $("#view_transaction").attr("aria-expanded", "true");
        if ($('#view_transaction tr td a').hasClass('toggle-plus')) {
            $("tr[class^='blk_']").toggle();
            $("tr[class^='blk_']").find("td:first-child > a").toggleClass("toggle-plus toggle-minus");
        }
        if ($('a[href="#view_transaction"]').hasClass('collapsed')) {
            setTimeout(function () {
                $('#view_transaction').removeClass('in');
                $("#view_transaction").attr("aria-expanded", "false");
            }, 200);
        }
        $('#' + modelID).print();
        setTimeout(function () {
            
        }, 200);
    });
    /* Ends - Print page */
    $(".js-edittor-submit").click(function () {
        $("input").each(function () {
            $(this).attr("value", $(this).val());
        });
        $('[type=text], textarea').each(function () {
            this.defaultValue = this.value;
        });
        $('[type=checkbox], [type=radio]').each(function () {
            this.defaultChecked = this.checked;
        });
        $('select option').each(function () {
            this.defaultSelected = this.selected;
        });
        $('#hidden-text').val($('.js-test-content').html());
    });

    /** Starts - Table row single click for all listing **/
    /*
      $(document).on('click', '.js-table-click', function () {
          var getUrl = $(this).attr('data-url');
          if (getUrl == '' || getUrl == null)
              var getUrl = $(this).closest("tr").attr('data-url');
          if ($(this).hasClass("js-document"))
              window.open(getUrl, '_blank');
          else
              window.location = getUrl;
      });
	*/
    $(document).on('click', 'tr.js-table-click td:not(".js-prevent-show")', function () {
        var getUrl = $(this).attr('data-url');
        if (getUrl == '' || getUrl == null)
            var getUrl = $(this).closest("tr").attr('data-url');
        if ($(this).hasClass("js-document"))
            window.open(getUrl, '_blank');
        else
            window.location = getUrl;
    });

    /** Ends - Table single click for all listing **/
    /*** Documents open without delete button function start ***/
    $(document).on('click', '.js_table_click td:not(".js-prevent-show")', function () {
        var getUrl = $(this).closest("tr").attr('data-url');
        window.open(getUrl);
    });
    /*** Documents open without delete button function end ***/

    /** Starts - UserActivity for listing **/
    $(document).on('click', '.js-useractivity-click', function () {

        var getUrl = $(this).attr('data-url');
        var getActivity = $(this).attr('data-activity');
        var getAction = $(this).attr('data-action');
        var getModule = $(this).attr('data-module');

        if (getActivity != '' && getModule != 'notes' && getModule != 'patients-notes' && getModule != 'document' && getModule != 'providerdocuments' && getModule != 'templatetypes') {
            if (getActivity == 'admin' || getActivity == 'practice') {
                window.open(getUrl, '_blank');
            }
            else {
                $.ajax({
                    url: api_site_url + '/admin/setuserpractice/' + getActivity,
                    type: 'get',
                    success: function (data, textStatus, jQxhr) {
                        window.open(getUrl, '_blank');
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        }
    });
    /** Ends - UserActivity for listing **/

    // Admin Provider //
    $(document).delegate('.js-npi-check-master', 'blur', function () {
        npi_value = $(this).val();
        var cur_form = $(this);
        current_npi_id_name = $(this).attr('id');
        var pars = 'npi=' + npi_value + '&is_provider=yes';
        $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
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

                    if (data['status'] == 'success') {
                        $.each(data['data']['npi_details'], function (key, item) {
                            $('#' + key).val(item);
                            $('#modal_' + key).html(item);
                        });

                        $.each(data['data']['provider'], function (key, item) {
                            if (key != 'provider_degrees_id' && key != 'gender_f' && key != 'gender_m') {
                                var item = (key == "middle_name") ? item[0] : item;
                                $('#' + key).val(item);
                            }
                        });

                        $("#js-speciality-change option:contains(" + data['data']['npi_details']['taxonomies_desc'] + ")").attr('selected', 'selected').trigger('change');
                        $("#js-speciality-change").select2();
                        setTimeout(function () {
                            $("#taxanomies-list option:contains('207Q00000X')").attr('selected', 'selected'); $("#taxanomies-list").select2();
                        }, 2500);

                        if (data['data']['provider']['enumeration_type'] == 'NPI-2') {

                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', false);
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'provider_degrees_id');

                            //$('.js_provider_ssn').prop("disabled", true);
                            //$('.js_provider_dob').prop("disabled", true);
                            //$('.js_provider_gender').prop("disabled", true);
                            //$('.js_provider_gender').iCheck('uncheck');
                            //$('#provider_degrees_id').prop("disabled", true);
                            $("#provider_types_id").select2("val", "");
                            $('.js-other-provider-options').addClass('hide');
                            $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
                            $('#npi_field_group').removeClass('hide');
                            $('#npi_field_individual').addClass('hide');
                            $("#first_name").val("a");
                            $("#last_name").val("a");

                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'organization_name', true);
                            /** MR-2351 issue ***/
                            $(".js_provider_ssn").prop('disabled', true);
                            $("#provider_degrees_id").prop('disabled', true);
                            $("#job_title").prop('disabled', true);
                            $("input[name='gender']").prop('disabled', true);
                            $("#providerdob").prop('disabled', true);
                            /*$('#etin_type_ssn').addClass('hide');
                             $('input[name="etin_type"]:radio[value="TAX ID"]').prop("checked", true);
                             $('input[name="etin_type"]:radio').iCheck('update');
                             //$('input[type=text][name="etin_type_number"]').inputmask("99-9999999");
                             $("#etin_type_number").removeClass('dm-ssn');
                             $("#etin_type_number").addClass('dm-tax-id');
                             $('input[type=text][name="etin_type_number"]').closest('div.form-group').find('label').html('TAX ID');*/

                        } else {
                            //$('.js_provider_ssn').prop("disabled", false);
                            //$('.js_provider_dob').prop("disabled", false);
                            //$('.js_provider_gender').prop("disabled", false);
                            //$('.js_male').iCheck('check');
                            //$('#provider_degrees_id').prop("disabled", false);
                            $("#provider_types_id").select2("val", "");
                            $('.js-other-provider-options').addClass('hide');
                            $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
                            $('#npi_field_group').addClass('hide');
                            $('#npi_field_individual').removeClass('hide');
                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'organization_name', false);
                            $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', true);
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'provider_degrees_id');
                            /** MR-2351 issue ***/
                            $(".js_provider_ssn").prop('disabled', false);
                            $("#provider_degrees_id").prop('disabled', false);
                            $("#job_title").prop('disabled', false);
                            $("input[name='gender']").prop('disabled', false);
                            $("#providerdob").prop('disabled', false);
                            /*if($('#etin_type_ssn').attr('class') == 'hide'){
                             $('#etin_type_ssn').removeClass('hide');
                             }
                             $('input[type=text][name="etin_type_number"]').closest('div.form-group').find('label').html('SSN or TAX ID');*/
                        }
                        if (current_npi_id_name == 'group_npi') {
                            $('.js-npi-group-success').removeClass('hide');
                            $('.js-npi-group-error').addClass('hide');
                            $('.js-npi-group-loading').addClass('hide');
                        } else {
                            $('.js-npi-individual-success').removeClass('hide');
                            $('.js-npi-individual-error').addClass('hide');
                            $('.js-npi-individual-loading').addClass('hide');
                        }
                        if (data['data']['npi_details']['enumeration_type'] == 'NPI-1') {
                            $('#form-npi-modal .clsNPI1').removeClass('hide');
                            $('#form-npi-modal .clsNPI2').addClass('hide');
                        } else {
                            $('#form-npi-modal .clsNPI1').addClass('hide');
                            $('#form-npi-modal .clsNPI2').removeClass('hide');
                        }

                        $('#npi_modal_success').removeClass('hide');
                        $('#npi_modal_error').addClass('hide');

                        $('input[type=hidden][name="valid_npi_bootstrap"]').val('');
                        var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check-master').attr('name');
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));

                    } else if (data['message'] != 'no_validation') {

                        $('#enumeration_type').val("");
                        $('#npi_field_group').addClass('hide');
                        $('#npi_field_individual').removeClass('hide');
                        if ($("#first_name").val() == 'a') {
                            $("#first_name").val("");
                            $("#last_name").val("");
                        }

                        $('#npi_error_message').val(data['data']['npi_details']['npi_error_message']);
                        $('#is_valid_npi').val(data['data']['npi_details']['is_valid_npi']);
                        $('#modal_npi_error_message').html(data['data']['npi_details']['npi_error_message']);

                        if (current_npi_id_name == 'group_npi') {
                            $('.js-npi-group-success').addClass('hide');
                            $('.js-npi-group-error').removeClass('hide');
                            $('.js-npi-group-loading').addClass('hide');
                        } else {
                            $('.js-npi-individual-success').addClass('hide');
                            $('.js-npi-individual-error').removeClass('hide');
                            $('.js-npi-individual-loading').addClass('hide');
                        }
                        $('#npi_modal_success').addClass('hide');
                        $('#npi_modal_error').removeClass('hide');

                        /*if($('#etin_type_ssn').attr('class') == 'hide'){
                         $('#etin_type_ssn').removeClass('hide');
                         }
                         $('input[type=text][name="etin_type_number"]').closest('div.form-group').find('label').html('SSN or TAX ID');*/

                        $('input[type=hidden][name="valid_npi_bootstrap"]').val('no');
                        //$(cur_form).closest('div').find('input.js-npi-check').val('');
                        var curr_npi_name = $(cur_form).closest('div').find('input.js-npi-check-master').attr('name');
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="' + curr_npi_name + '"]'));

                    } else if (data['status'] == 'error' && data['message'] == 'no_validation') {
                        $('.js-npi-individual-loading').addClass('hide');
                        $('.js-npi-group-loading').addClass('hide');

                        /*if($('#etin_type_ssn').attr('class') == 'hide'){
                         $('#etin_type_ssn').removeClass('hide');
                         }
                         $('input[type=text][name="etin_type_number"]').closest('div.form-group').find('label').html('SSN or TAX ID');*/
                    }
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    });
    /* Ends - NPI validation */
});

//Js menu check
/*$(document).delegate('.js-menucheck', 'click', function() {
 
 id = $(this).attr('id');  
 if ($(this).prop('checked')==true){ 
 $('.js-sub-'+id).prop('checked', 'checked');
 } else {
 $('.js-sub-'+id).attr("checked", false);
 }
 });*/
cpt_lists = [];
function getmodifierandcpt() {
    var url = api_site_url + '/getmodifier';
    var cpt_lists = [];
    $.get(url, function (data) {
        cpt_mod = [];
        modifier = data.modifier;
        cpt = data.cpt;
        cpt_billed_arr = data.cpt_billed_amt;
        modifier_arr = $.map(modifier, function (el) {
            return el;
        });
        cpt_arr = $.map(cpt, function (el) {
            return el;
        });
        //cpt_mod_arr = $.map(data.cpt_with_modifier, function(val, index) { return index +':'+val;});              
        cpt_mod_arr = data.cpt_with_modifier;
        multi = data.data;
        localStorage.setItem("cpt_lists", JSON.stringify(cpt_arr));
        localStorage.setItem("cpt_mod_arr", JSON.stringify(cpt_mod_arr));
        localStorage.setItem("cpt_billed_arr", JSON.stringify(cpt_billed_arr));
        localStorage.setItem("data", JSON.stringify(multi));
    });
}

// Practice User type
//$("body").delegate('#practice_user', 'change', function() {   
$(document).on('ifToggled change', "input[name='practice_user_type']", function () {
    if ($(this).is(":checked") == true && $(this).val() == 'practice_admin') {
        $('#admin_practice_permission').val('');
        $("#admin_practice_permission option[value='']").attr('selected', true);
        $('.js-practice-user').removeClass('hide').addClass('show');
        $('.js-permission-user').removeClass('show').addClass('hide');
    } else {
        $('#admin_practice_id').val('');
        $('.js-practice-user').removeClass('show').addClass('hide');
        $('.js-permission-user').removeClass('hide').addClass('show');
    }
});

$("#js_get_roles_permissions").change(function () {
    var selected_role_id = $(this).val();
    if (selected_role_id != '') {
        $.ajax({
            type: "GET",
            url: api_site_url + '/admin/getrolespermissions',
            data: 'selected_role_id=' + selected_role_id,
            dataType: "json",
            success: function (data) {
                $('#permissions_forusers').find('input[type=checkbox]:checked').removeAttr('checked');
                for (var i in data) {
                    val = data[i].split('_');
                    $("input[name='" + data[i] + "']").prop('checked', true);
                    $("input[value='" + val[0] + "']").prop('checked', true);
                }
                $('.js-check-each').each(function (index, element) {
                    var $childCheckboxes = $(this).find('input.js-subselect'),
                        no_checked = $childCheckboxes.filter(':checked').length;

                    if ($childCheckboxes.length == no_checked) {
                        val = $(this).children().find('.js_menu').prop('checked', true);
                        dataclass = $(this).children().find('.js_menu').attr("data-class");
                        allmoduleCheck(dataclass);
                    }
                });
                allCheck(".js_select_all.js_menu");
            }
        });
    } else {
        allCheck(".js_select_all.js_menu");
    }
});

/****   Start to API Settings ***/
// Start to display the selected the practice API in role settings page.
$(document).on('change', '.js_set_api', function () {
    var selected_practice_id = $(this).val();
    if (selected_practice_id != '') {
        var user_id = $('input[name=user_id]').val();
        $.ajax({
            type: "GET",
            url: api_site_url + '/admin/getuserapi',
            data: 'practice_id=' + selected_practice_id + '&userid=' + user_id,
            success: function (result) {
                $('.js_api_settings').html(result);
                $('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
            }
        });
    }
});
// End to display the selected the practice API in role settings page.

// Start to display the selected user api in user API settings page.
$(document).on('change', '.js_get_user_api', function () {
    var user_id = $(this).val();
    $('input[type=checkbox]').iCheck('uncheck');
    $.ajax({
        type: "GET",
        url: api_site_url + '/api/getpracticeuserapi',
        data: 'userid=' + user_id,
        success: function (result) {
            if (result.length != 0) {
                $.each(result, function (key, item) {
                    $('#' + item.api_id).iCheck('check');
                });
            }
        }
    });
});
// End to display the selected user api in user API settings page.
$(document).on('click', '.js_updateapisettings', function () {
    window.onbeforeunload = UnPopIt;
    var get_remove = $('input[name=removed_api]').val();
    if (get_remove != '') {
        get_remove = get_remove.substring(0, get_remove.length - 1);
        $.ajax({
            type: "GET",
            url: api_site_url + '/api/getpracticedisabledapi',
            data: 'remove_api=' + get_remove,
            success: function (result) {
                if (result.length != 0) {
                    $.confirm({
                        text: "This API already used by " + result + ". Are you sure you want to disabled this API?",
                        confirm: function () {
                            var formname = $(".medcubicsform").attr('name');
                            document.forms[formname].submit();
                        },
                        cancel: function () {
                            window.location.href = window.location.href;
                        }
                    });
                } else {
                    var formname = $(".medcubicsform").attr('name');
                    document.forms[formname].submit();
                }
            }
        });
    } else {
        var formname = $(".medcubicsform").attr('name');
        document.forms[formname].submit();
    }
});

/*** start to encode Base64 *** /
 var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
 /*** end to encode Base64 ***/

var ids = '';
$(document).on('ifToggled change', '.js_api_check', function () {
    var chk = $(this).is(":checked");
    if (chk == false) {
        ids += $(this).attr('data-api') + ',';
    }
    $('input[name=removed_api]').val(ids);
});

/****   End to API Settings ***/

$('#permissions_forusers').delegate('.js-select-practice', 'click', function () {
    id = $(this).attr('id');
    var totalSeen = $("input#" + id).length;
    var checked = $("input#" + id + ":checked").length;
    if (totalSeen == checked) {
        $('.js-menu-practice-' + id).prop('checked', true);
    } else {
        $('.js-menu-practice-' + id).prop('checked', false);
    }
});

$(".js-sidebar-toggle").click(function () {
    var sidebar_collapse = 'show';
    if ($("body").hasClass("sidebar-collapse"))
        var sidebar_collapse = 'hide';

    var pars = 'is_sidebar_collapse=' + sidebar_collapse;
    $.ajax({
        url: api_site_url + '/collapse',
        type: 'get',
        data: pars,
        success: function (data) {
            // 
        },
        error: function (jqXhr, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
});

$(document).on('mouseover', '.mm', function (event) {
    if ($('.on-hover-content').is(':visible')) {
        $(".on-hover-content").hide();
        event.stopPropagation();
        $(".on-hover-content").fadeOut('fast');
    }
    $(this).siblings(".on-hover-content").fadeIn("9000");
});

//.mouseleave(function(){
$(document).on('mouseleave', '.mm', function (event) {
    $(this).siblings(".on-hover-content").fadeOut('fast');
});

$("#js-speciality-change").change(function () {
    selectfacilitytexonomy($(this).val());
});

$("#js-speciality2-change").change(function () {
    selectfacilitytexonomy2($(this).val());
});

function selectfacilitytexonomy2(spaciality_id) {
    $.ajax({
        type: "GET",
        url: api_site_url + '/gettaxanomies',
        data: 'specialities_id=' + spaciality_id,
        success: function (data) {
            $("#taxanomies2-list").html(data);
            $("#taxanomies2-list").val($('#taxanomies2-list option:nth-child(1)').val()).trigger('change');
        }
    });
}

// Stop ajax request

$(document).on("change", 'input[name="filefield"]', function () {
    var form_clas_name = $(this).parents("form").hasClass('medcubicsform');
    if (form_clas_name) {
        $(".medcubicsform").bootstrapValidator('addField', "filefield");
        $(".medcubicsform").bootstrapValidator('addField', "js_err_webcam");
        $(".medcubicsform").bootstrapValidator('revalidateField', "filefield");
        //$(".medcubicsform").bootstrapValidator('revalidateField', "js_err_webcam");
    } else {
        var form_id = $(this).parents("form").attr('id');
        $('#' + form_id).bootstrapValidator('addField', "filefield");
        $('#' + form_id).bootstrapValidator('addField', "js_err_webcam");
        $('#' + form_id).bootstrapValidator('revalidateField', "filefield");
        //$('#' + form_id).bootstrapValidator('revalidateField', "js_err_webcam");
    }
});

$(document).on("ifToggled change", ".js-upload-type:checked", function () {
    val = $(this).val();
    var form_clas_name = $(this).parents("form").hasClass('medcubicsform');
    if (form_clas_name) {
        var get_id = $(".medcubicsform");
    } else {
        var form_id = $(this).parents("form").attr('id');
        var get_id = $('#' + form_id);
    }

    if (val == 'browse') {
        get_id.find('.js-photo').hide();
        get_id.find('.js-photo-patient').hide();
        get_id.find('.js-upload').show();
        get_id.find('.js-upload-image').show();
        get_id.find('#webcam_div').hide();
        get_id.find('#js-show-webcam').hide();
        get_id.find('.js-scanner').hide();
        get_id.find('[name="filefield"]').val('');
        get_id.find('.js-display-error').html('');
    } else if (val == 'scanner') {
        get_id.find('.js-scanner').show();
        get_id.find('.js-photo').hide();
        get_id.find('.js-photo-patient').hide();
        get_id.find('.js-upload').hide();
        get_id.find('.js-upload-image').hide();
        get_id.find('#webcam_div').hide();
        get_id.find('#js-show-webcam').hide();
    } else {
        get_id.find('.js-photo').show();
        get_id.find('.js-upload').hide();
        get_id.find('.js-upload-image').hide();
        get_id.find('.js-scanner').hide();
        get_id.find('.js-photo-patient').show();
    }
    var form_clas_name = $(this).parents("form").hasClass('medcubicsform');
    if (form_clas_name) {
        get_id.bootstrapValidator('addField', "filefield");
        get_id.bootstrapValidator('addField', "js_err_webcam");
        get_id.bootstrapValidator('revalidateField', "filefield");
        get_id.bootstrapValidator('revalidateField', "js_err_webcam");
    } else {
        get_id.bootstrapValidator('addField', "filefield");
        get_id.bootstrapValidator('addField', "js_err_webcam");
        get_id.bootstrapValidator('revalidateField', "filefield");
        get_id.bootstrapValidator('revalidateField', "js_err_webcam");
    }
});
//webcam functions ends

$('table#list-cpt th').click(function (e) {
    // 
});

//Used in on load of facility add 
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

//starts add new option for select box
if ($("div").hasClass("js-add-new-select")) {
    $("div.js-add-new-select").find('select:not("#newadded_cms_type")').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
}

$(document).on('change', '.js-add-new-select-opt', function (event) {
    //$('.js-add-new-select-opt').change(function(){
    var current_divid = $(this).parents('div.js-add-new-select').attr('id');
    var selected_value = $(this).val();
    $('#' + current_divid).find('p.js-error').html('').removeClass('show').addClass('hide');
    if (selected_value == '0') {
        $(this).closest('.js_common_ins').addClass('hide');
        $('#' + current_divid).children("#add_new_span").removeClass('hide').addClass('show');
        $('#' + current_divid).find('#newadded').val('');
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    } else {
        $("#add_new_span").removeClass('show').addClass('hide');
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
    }
});

$(document).on('keyup', '#newadded', function () {
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    if ($(this).val() != null) {
        var seldivid = $(this).parents('div.js-add-new-select').attr('id');
        $('#' + seldivid).find('p.js-error').removeClass('show').addClass('hide');
    }
});

$(document).on("click", 'div.js-add-new-select #add_new_save', function () {
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    //$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_save').click(function(){
    var lblname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-label-name');
    var insurance_type = $(this).parents('div.js-add-new-select').find("#newadded").val();
    // var regex = new RegExp("^[a-zA-Z ]+$");
    var regex = new RegExp("^[a-zA-Z 0-9!@#\$%\^\&*\)\(+=._-]+$");
    if (!insurance_type || !regex.test(insurance_type)) {
        $(this).parents('div.js-add-new-select').find("#newadded").parent('div').addClass('has-error');
        $(this).parents('div.js-add-new-select').find('p.js-error').html('');
        if (!insurance_type) {
            $(this).parents('div.js-add-new-select').find('p.js-error').html(insurancetype + ' ' + lblname);
        } else {
            $(this).parents('div.js-add-new-select').find('p.js-error').html(only_alpha_lang_err_msg);
        }
        $(this).parents('div.js-add-new-select').find('p.js-error').removeClass('hide').addClass('show');
    } else {
        $(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
        var tablename = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-table-name');
        var fieldname = $(this).parents('div.js-add-new-select').find('#newadded').attr('data-field-name');
        var addedvalue = $(this).parents('div.js-add-new-select').find('#newadded').val();
        var seldivid = $(this).parents('div.js-add-new-select').attr('id');
        var pars = 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue;

        if (seldivid == 'js-insurance-type' && $('#newadded_cms_type').length) {
            var insCmsType = $(this).parents('div.js-add-new-select').find('#newadded_cms_type').val();
            pars = pars + '&cms_type=' + insCmsType;
        }

        var value = addedvalue.trim();
        var changed_string = value.toLowerCase();
        if (changed_string != 'App' && changed_string != "app") {
            url_path = (window.location.pathname).split("/");
            if (url_path[2] == 'templates') {
                $.ajax({
                    url: api_site_url + '/addnewselect',
                    type: 'get',
                    data: pars,
                    success: function (data) {
                        if (data == '2') {
                            $('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
                            $('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
                            $('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
                            $('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added                            
                        } else {
                            $('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
                            $('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
                            $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
                            getoptionvalues(tablename, fieldname, seldivid, addedvalue);
                        }
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }//Template if
        }//App if

        $.ajax({
            url: api_site_url + '/addnewselect',
            type: 'get',
            data: pars,
            success: function (data) {
                if (data == '2') {
                    $('#' + seldivid).find("#newadded").parent('div').addClass('has-error');
                    $('#' + seldivid).find('p.js-error').html(exist_insurancetype + ' ' + lblname);
                    $('#' + seldivid).find('p.js-error').removeClass('hide').addClass('show');
                    $('input[name="hold_reason_exist"]').val(1); // For hold readon add new it was added

                } else {
                    //$("#add_new_span").removeClass('show').addClass('hide');                  
                    //$('.js_common_ins').removeClass('hide').addClass('show');

                    $('#' + seldivid).find("#add_new_span").removeClass('show').addClass('hide');
                    $('#' + seldivid).find(".js_common_ins").removeClass('hide').addClass('show');
                    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
                    getoptionvalues(tablename, fieldname, seldivid, addedvalue);
                }
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
        $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
        $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
        var hold_reason_val = $('input[name="hold_reason_exist"]').val();
        setTimeout(function () {
            if ($('input[name="other_reason"]').val() != '' && !hold_reason_val) {
                $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', false);
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
            } else {
                $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'other_reason', true);
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'other_reason');
            }
        }, 500);
    }
});

$(document).on("click", "div.js-add-new-select #add_new_cancel", function () {
    //$('.js-add-new-select-opt').parents('div.js-add-new-select').find('#add_new_cancel').click(function(){        
    $(this).parents('div.js-add-new-select').find("#newadded").parent('div').removeClass('has-error');
    $(this).parents('div.js-add-new-select').find("#add_new_span").removeClass('show').addClass('hide');
    var seldivid = $(this).parents('div.js-add-new-select').attr('id');
    $(this).parents('#' + seldivid).find('.js-add-new-select-opt').closest('.js_common_ins').removeClass('hide').addClass('show');
    $(this).parents('#' + seldivid).find('.js-add-new-select-opt').select2("val", "");
    if ($(this).parents('div.js-add-new-select').hasClass('hold-option')) {
        $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'hold_reason_id', true);
        $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'hold_reason_id');
    }
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
});

function getoptionvalues(tablename, fieldname, seldivid, addedvalue) {
    $.ajax({
        type: "GET",
        url: api_site_url + '/getoptionvalues',
        data: 'tablename=' + tablename + '&fieldname=' + fieldname + '&addedvalue=' + addedvalue,
        success: function (data) {
            $('#' + seldivid).find("select.js-add-new-select-opt").html(data);
            if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-hold-reason') {
                $('#js-hold-reason').change();
            } else if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-claim-substatus') {
                $('#js-claim-substatus').change();
			}// else if ($('#' + seldivid).find("select.js-add-new-select-opt").attr('id') == 'js-claim-review-substatus') { 
			else if($('#' + seldivid).find("select.js-add-new-select-opt").hasClass('js-ar-review-substatus')) {
				var ident = $('#' + seldivid).find("select.js-add-new-select-opt").attr('data-id');
				$('#js-claim-review-substatus_'+ident).change();
            } else {
                $('#' + seldivid).find("select.js-add-new-select-opt").select2();
            }
        }
    });
}

//ends add new option for select box                            
function myCall() {
    var patient_id = $('#patient_id').val();
    var request = $.ajax({
        url: api_site_url + '/patients/' + patient_id + '/addmore',
        type: "GET",
        dataType: "html"
    });
    request.done(function (msg) {
        $("#mybox").html(msg);
    });
    request.fail(function (jqXHR, textStatus) {
        js_alert_popup("Request failed: " + textStatus);
    });
}

// Patients
// ForUrlShow

$(function () {
    var hash = window.location.hash;
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');

    $('.table-container a').click(function (e) {
        $(this).tab('show');
        var scrollmem = $('body').scrollTop();
        window.location.hash = this.hash;
        $('html,body').scrollTop(scrollmem);
    });
});

$(document).ready(function () {
    if (location.hash) {
        $('a[href=' + location.hash + ']').tab('show');
    }
    $(document.body).on("click", "a[data-toggle]", function (event) {
        location.hash = this.getAttribute("href");
    });
});

$(window).on('popstate', function () {
    var anchor = location.hash || $("a[data-toggle=tab]").first().attr("href");
    $('a[href="' + anchor + '"]').tab('show');
});

/*** End ForUrlShow ***/

/*** Patient Create ***/
$(document).ready(function () {
    /*$(document).on('submit', '#authorization', function()
     {
     var patient_id = $('#patient_id').val();
     var data1 = $(this).serialize();
     $.ajax({
     type : 'POST',
     url  : api_site_url+'/patientsprofile/'+patient_id,
     data : data1,
     success :  function(result){
     var split_result = result.split('~~');
     if(split_result[0] == 'success')
     window.location = api_site_url+'/patients/'+patient_id+'/edit#'+split_result[1];
     }
     });
     return false;
     });*/
});
/*** End Patient ***/

/*** Age Calculation ***/
function CalculateAge(DOB) {
    if (DOB.value != '') {
        now = new Date()
        dob = DOB.value.split('-');
        if (dob.length === 3) {
            born = new Date(dob[0], dob[1] * 1 - 1, dob[2]);
            age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
            if (isNaN(age)) {
                document.getElementById('lblAge').innerHTML = '';
                alert('Input date is incorrect!');
            } else {
                document.getElementById('age').value = age;
            }
        }
    }
}
/*** End Age Calculation ***/

/*************************************************/
/********** Starts - Provider Scheduler **********/
/*************************************************/
$(document).on('change', '.js-end_date_option', function () {
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'no_of_occurrence');
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'end_date');
});

$("a[data-target=#provider_scheduler_modal]").click(function (e) {
    e.preventDefault();
    var target = $(this).attr("data-url");
    //Load the url and show modal on success
    $("#provider_scheduler_modal .modal-body").load(target, function () {
        $('#provider_scheduler_modal').on('show.bs.modal', function () {
            $.AdminLTE.boxWidget.activate();
            //Add a datepicker widget for selecting dates
            /*$('#end_date').datepicker({dateFormat: "mm/dd/yy"});
             
             $("#start_date").datepicker({
             dateFormat: "mm/dd/yy", 
             minDate:  0,
             onSelect: function(date){            
             var date1 = $('#start_date').datepicker('getDate');           
             var date = new Date( Date.parse( date1 ) ); 
             date.setDate( date.getDate() + 1 );        
             var newDate = date.toDateString(); 
             newDate = new Date( Date.parse( newDate ) );                      
             $('#end_date').datepicker("option","minDate",newDate);            
             }
             }); */

            $('input[name="end_date_option"]:checked').each(function () {
                if (this.value == 'never') {
                    $("#end_date").val('');
                    $('input[name="no_of_occurrence"]').val('0');
                }
                // Called for initial enable / disable handle
                endDateOptionEnableORDisable();
            });

            $(function () {
                $("#start_date").datepicker({
                    // minDate: 0,
                    dateFormat: "mm/dd/yy",
                    onSelect: function (date) {
                        var date1 = $('#start_date').datepicker('getDate');
                        var date = new Date(Date.parse(date1));
                        date.setDate(date.getDate() + 1);
                        var newDate = date.toDateString();
                        newDate = new Date(Date.parse(newDate));
                        $('#end_date').datepicker("option", "minDate", newDate);
                    },
                    onClose: function (selectedDate) {
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'start_date');
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'end_date');
                    }
                });

                $("#end_date").datepicker({
                    minDate: "0M",
                    dateFormat: "mm/dd/yy",
                    onClose: function (selectedDate) {
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'start_date');
                        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'end_date');
                    }
                });
            });
            $(".js-onoff-checkbox").bootstrapSwitch();

            $('#js-bootstrap-validator')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: '',
                        invalid: '',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        facility_id: {
                            validators: {
                                notEmpty: {
                                    message: facility_id_lang_err_msg
                                }
                            }
                        },
                        start_date: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                /*date: {
                                 message: date_format_lang_err_msg
                                 },*/
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var end_date_option = $('input[type=radio]:checked').val();
                                        var end_date = validator.getFieldElements('end_date').val();
                                        if (end_date_option == 'on') {
                                            var response = startdateFunction(value, end_date);
                                            if (response != true) {
                                                return {
                                                    valid: false,
                                                    message: response
                                                };
                                            }
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        end_date: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                /*date: {
                                 message: date_format_lang_err_msg
                                 },*/
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var end_date_option = $('input[type=radio]:checked').val();
                                        var eff_date = validator.getFieldElements('start_date').val();
                                        var newdate = new Date();
                                        var d1 = newdate.setHours(0, 0, 0, 0);
                                        var dateobj = value.split('/');
                                        var day = dateobj[1];
                                        var month = dateobj[0]
                                        var year = dateobj[2]
                                        value_date = (year + '-' + month + '-' + day)
                                        var d2 = new Date(value_date);
                                        if (end_date_option == 'on') {
                                            if ((value != '') && (d1 > d2)) {
                                                return {
                                                    valid: false,
                                                    message: date_format_lang_err_msg
                                                };
                                            }
                                            if (value == '') {
                                                return {
                                                    valid: false,
                                                    message: enddate_req_lang_err_msg
                                                };
                                            } else {
                                                var ter_date = value;
                                                var response = enddateFunction(eff_date, ter_date);
                                                if (response != true) {
                                                    return {
                                                        valid: false,
                                                        message: response
                                                    };
                                                }
                                            }
                                            //alert(value)
                                            var currentDate = new Date()
                                            //console.log(d1.getMonth());
                                            //console.log(d2.getMonth());
                                            if (d1 > d2) {
                                                alert("The first date is after the second date!");
                                            }
                                            return true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        no_of_occurrence: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var end_date_option = $('input[type=radio]:checked').val();
                                        if (end_date_option == 'after') {
                                            if (value == '') {
                                                return {
                                                    valid: false,
                                                    message: enter_occurence_lang_err_msg
                                                };
                                            }
                                        }
                                        return true;
                                    }
                                }
                            }
                        }
                        /*start_date: {
                         message: '',
                         trigger: 'keyup change',
                         validators: {                                                  
                         callback: {
                         message: 'common.validation.inactivedate',
                         callback: function (value, validator) {
                         var m = validator.getFieldElements('end_date').val();
                         var n = value;
                         var date_format = new Date(n);
                         var end_date_format = new Date(m);
                         if (value == ''){
                         return {
                         valid: false,
                         message: 'Select start date'
                         };
                         } else if (n != '' && date_format == "Invalid Date"){
                         return {
                         valid: false,
                         message: 'Enter valid date'
                         };
                         } else if(n != '' && date_format != "Invalid Date" && m != '' && end_date_format !="Invalid Date"){
                         var getdate = daydiff(parseDate(date_format), parseDate(end_date_format));
                         if(getdate > 0) {
                         return  true; 
                         } 
                         else {
                         return {
                         valid: false,
                         message: 'Start date should not be after end date'
                         }; 
                         }
                         }
                         else
                         return true;
                         }
                         }
                         }
                         },
                         end_date: {
                         message: '',
                         trigger: 'keyup change',
                         validators: {                          
                         callback: {
                         message: '',
                         callback: function (value, validator) {
                         var end_date_option = $('input[type=radio]:checked').val();
                         if(end_date_option == 'on'){
                         var mm = $('#start_date').val(); 
                         var nn = value;
                         var eff_format = new Date(mm);
                         var ter_format = new Date(nn);
                         if (ter_format !="Invalid Date" && nn != '' && eff_format !="Invalid Date") {
                         var getdate = daydiff(parseDate(mm), parseDate(nn));
                         if(getdate > 0) {
                         return  true; 
                         } 
                         else {
                         return {
                         valid: false,
                         message: 'End date should not be before start date'
                         }; 
                         }
                         }
                         else if (mm != '' && eff_format !="Invalid Date") {
                         if (nn == ''){
                         return {
                         valid: false,
                         message: 'Select end date'
                         }; 
                         } else {
                         return true;
                         } 
                         }
                         else
                         return true;
                         } else
                         return true;
                         }
                         }
                         }
                         }*/
                    }
                }).on('success.form.bv', function (e) {
                    $('.js_practice_sch_footer').removeClass('show').addClass('hide');
                    $('.js_practice_sch_footer_load').removeClass('hide').addClass('show');

                    // Prevent form submission
                    e.preventDefault();

                    var disabled_form_ids = [];
                    $('#js-bootstrap-validator .js-display-days-cls :input:disabled').each(function () {
                        disabled_form_ids.push($(this).attr('id'));
                    });
                    var myform = $('#js-bootstrap-validator');
                    var disabled = myform.find(':input:disabled').removeAttr('disabled');
                    var serialized = myform.serialize();
                    //disabled.attr('disabled','disabled');
                    // Save button enable in for  clicked time revaildate the end date option  
                    endDateOptionEnableORDisable();

                    //          $('.js-submit-btn').attr('disabled',false);
                    $('.help-block').addClass('hide');
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: api_site_url + '/api/storeproviderschedulersettings',
                        data: serialized,
                        success: function (result) {
                            if (result['status'] == 'success') {
                                $('.modal-body').html(create_lang_err_msg);
                                $('#provider_scheduler_modal').modal("hide");
                                window.location.reload(true);
                                return false;
                                //redirect_url = api_site_url+'practicescheduler/provider/'+$('#provider_id').val();
                            } else {
                                $('.js_practice_sch_footer_load').removeClass('show').addClass('hide');
                                $('.js_practice_sch_footer').removeClass('hide').addClass('show');
                                var length_of_disabled_div = disabled_form_ids.length;
                                for (i = 0; i < length_of_disabled_div; i++) {
                                    div_id_name = disabled_form_ids[i];
                                    $('#provider_scheduler_modal #' + div_id_name).attr("disabled", true);
                                }
                                ;

                                error_type = result['data']['error_array']['error_type'];
                                error_type_value = result['data']['error_array']['error_type_value'];

                                $('#js-error-msg').html(result['message']);
                                $('#js-error-msg').removeClass('hide');

                                if (error_type == 'days_timings') {
                                    split_days = error_type_value.split(', ');
                                    split_days_count = split_days.length;
                                    for (i = 0; i < split_days_count; i++) {
                                        day_name = split_days[i].toLowerCase();
                                        $('#js-error-' + day_name).html(time_slot_empty_lang_err_msg);//select time error
                                        $('#js-error-' + day_name).removeClass('hide');
                                    }
                                } else if (error_type == 'mismatch_time_selection') {
                                    $.each(error_type_value, function (key, value) {
                                        if (value > 0) {
                                            $('#js-error-' + key).html(from_to_time_slot_lang_err_msg); //select from to time error
                                            $('#js-error-' + key).removeClass('hide');
                                        }
                                    });
                                } else {
                                    $('#js-error-' + error_type).html(error_type_value);
                                    $('#js-error-' + error_type).removeClass('hide');
                                }
                                return false;
                            }
                        }
                    });
                    return false;
                });
        });
        $('#provider_scheduler_modal').on('hidden.bs.modal', function () {
            $("#provider_scheduler_modal .modal-body").html('');
            //$('#js-bootstrap-validator').bootstrapValidator('resetForm', true);
            //window.location.reload(true);
        });
        /* $('#provider_scheduler_modal').on('hide.bs.modal', function(){    
         $('.modal-body').html('');
         $('#js-bootstrap-validator').bootstrapValidator('resetForm', true);
         window.location.reload(true);
         });*/
        $("#provider_scheduler_modal").modal("show");
        return false;
    });
});

/* Starts - Display fields depending upon schedule type */
$(document).on('change', '#schedule_type', function () {
    var current_selected_value = $(this).val();
    current_selected_value = current_selected_value.toLowerCase();
    $('#provider_scheduler_modal .js-schedule-type-option-cls').addClass('hide');
    $('#provider_scheduler_modal .js-schedule-type-title-cls').addClass('hide');
    $('#provider_scheduler_modal .js-display-days-cls').addClass('hide');

    $('#provider_scheduler_modal #js-schedule-type-title-' + current_selected_value).removeClass('hide');
    $('#provider_scheduler_modal #js-schedule-type-option-' + current_selected_value).removeClass('hide');

    if (current_selected_value == 'daily') {
        $('#provider_scheduler_modal .js-repeat-caption').html('day&nbsp;(s)');
        $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
    } else if (current_selected_value == 'weekly') {
        $('#provider_scheduler_modal .js-repeat-caption').html('week&nbsp;(s)');
    } else if (current_selected_value == 'monthly') {
        $('#provider_scheduler_modal .js-repeat-caption').html('month&nbsp;(s)');

        if ($('#provider_scheduler_modal #js-visit-by').val() == 'day') {
            $('#provider_scheduler_modal .js-display-days-cls').addClass('hide');
            var selected_day_option_name = $('#provider_scheduler_modal #js-monthly-day-option').val();
            $('#provider_scheduler_modal #js-day-parent-' + selected_day_option_name).removeClass('hide');
        } else if ($('#provider_scheduler_modal #js-visit-by').val() == 'date' || $('#provider_scheduler_modal #js-visit-by').val() == 'week') {
            $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
        }
    } else {
        $('#provider_scheduler_modal .js-repeat-caption').html('day&nbsp;(s)');
        $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
    }
});
/* Ends - Display fields depending upon schedule type */

/*  Starts - (Monthly) Display fields depending upon monthly visit by option */
$(document).on('change', '#js-visit-by', function () {
    var current_selected_value = $(this).val();
    $('#provider_scheduler_modal .js-display-days-cls').addClass('hide');
    if (current_selected_value == 'date') {
        $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-date').removeClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-day').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-week').addClass('hide');
    } else if (current_selected_value == 'day') {
        $('#provider_scheduler_modal #js-monthly-visit-option-date').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-day').removeClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-week').addClass('hide');
        var selected_day_option_name = $('#provider_scheduler_modal #js-monthly-day-option').val();
        $('#provider_scheduler_modal #js-day-parent-' + selected_day_option_name).removeClass('hide');
    } else if (current_selected_value == 'week') {
        $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-date').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-day').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-week').removeClass('hide');
    } else {
        $('#provider_scheduler_modal #js-monthly-visit-option-date').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-day').addClass('hide');
        $('#provider_scheduler_modal #js-monthly-visit-option-week').addClass('hide');
    }
});
/* Ends - (Monthly) Display fields depending upon monthly visit by option */

/*  Starts - (Monthly->visitby(day)) Display days from and to option depending upon day selection in monthly day visit by option */
$(document).on('change', '#js-monthly-day-option', function () {
    var current_selected_value = $(this).val();
    $('#provider_scheduler_modal .js-display-days-cls').addClass('hide');
    $('#provider_scheduler_modal #js-day-parent-' + current_selected_value).removeClass('hide');
});
/* Ends - (Monthly->visitby(day)) Display days from and to option depending upon day selection in monthly day visit by option */

/* Starts - Fetch facility working hours timings depending upon facility select */
$(document).on('change', '#js-provider-scheduler-facility', function () {
    var facility_id = $(this).val();
    var pars = 'provider_id' + $('#provider_scheduler_modal #provider_id').val() + '&facility_id=' + facility_id;
    if (facility_id != '') {
        $('.js_practice_sch_footer').removeClass('show').addClass('hide');
        $('.js_practice_sch_footer_load').removeClass('hide').addClass('show');
        $.ajax({
            type: 'get',
            url: api_site_url + '/api/getworkinghourstimingsforprovider/' + $("#provider_scheduler_modal #provider_id").val() + '/' + facility_id,
            success: function (data) {
                resetTimings();
                $('#start_date').val(data['data']['facility_details']['start_date']);
                $('#end_date').val(data['data']['facility_details']['end_date']);
                $('#provider_scheduler_modal .js-day-timings-monday-from').html('<option value="">-- From --</option>' + data['data']['days']['monday']);
                $('#provider_scheduler_modal .js-day-timings-tuesday-from').html('<option value="">-- From --</option>' + data['data']['days']['tuesday']);
                $('#provider_scheduler_modal .js-day-timings-wednesday-from').html('<option value="">-- From --</option>' + data['data']['days']['wednesday']);
                $('#provider_scheduler_modal .js-day-timings-thursday-from').html('<option value="">-- From --</option>' + data['data']['days']['thursday']);
                $('#provider_scheduler_modal .js-day-timings-friday-from').html('<option value="">-- From --</option>' + data['data']['days']['friday']);
                $('#provider_scheduler_modal .js-day-timings-saturday-from').html('<option value="">-- From --</option>' + data['data']['days']['saturday']);
                $('#provider_scheduler_modal .js-day-timings-sunday-from').html('<option value="">-- From --</option>' + data['data']['days']['sunday']);

                /// Show available works hours ///
                $('#provider_scheduler_modal .js-show-available-timings-monday').html(data['data']['facility_details']['monday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-tuesday').html(data['data']['facility_details']['tuesday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-wednesday').html(data['data']['facility_details']['wednesday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-thursday').html(data['data']['facility_details']['thursday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-friday').html(data['data']['facility_details']['friday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-saturday').html(data['data']['facility_details']['saturday_available_time']);
                $('#provider_scheduler_modal .js-show-available-timings-sunday').html(data['data']['facility_details']['sunday_available_time']);

                var weely_all = 'no';
                $('#provider_scheduler_modal #js-weekly_all').removeClass('disabled');
                $('#provider_scheduler_modal .js-weekly-day-selection').removeClass('disabled');
                //alert(data['data']['facility_details']['sunday_available_time']);
                if (data['data']['facility_details']['monday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_monday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['tuesday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_tuesday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['wednesday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_wednesday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['thursday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_thursday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['friday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_friday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['saturday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_saturday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (data['data']['facility_details']['sunday_available_time'] == 'Not available') {
                    $('#provider_scheduler_modal #weekly_sunday_p_tag').addClass('disabled');
                    weely_all = 'yes';
                }
                if (weely_all == 'yes')
                    $('#provider_scheduler_modal #js-weekly_all').addClass('disabled');

                if (facility_id != '') {
                    if (data['data']['facility_details']['phone'] != '') {
                        var facility_details_phone = data['data']['facility_details']['phone'];
                    } else {
                        var facility_details_phone = "&nbsp;&nbsp;- Nil -&nbsp;&nbsp;";
                    }
                    if (data['data']['facility_details']['email'] != '') {
                        var facility_details_email = data['data']['facility_details']['email'];
                    } else {
                        var facility_details_email = "&nbsp;&nbsp;- Nil -&nbsp;&nbsp;";
                    }
                    $('.js-show-by-facility').removeClass('hide');
                    $('#js-facility-details-name').html(data['data']['facility_details']['name']);
                    $('#js-facility-details-address').html(data['data']['facility_details']['address']);
                    $('#js-facility-details-zipcode').html(data['data']['facility_details']['zipcode']);
                    $('#js-facility-details-phone').html(facility_details_phone);
                    $('#js-facility-details-email').html(facility_details_email);
                    if (data['data']['facility_details']['filename'] == '' || data['data']['facility_details']['filename'] == '.') {
                        $('#js-facility-details-icon').attr('src', api_site_url + '/' + data['data']['facility_details']['facility_icon']);
                    } else {
                        $('#js-facility-details-icon').attr('src', data['data']['facility_details']['facility_icon']);
                    }
                } else {
                    resetTimings();
                    $('.js-show-by-facility').addClass('hide');
                }
                $('.js_practice_sch_footer_load').removeClass('show').addClass('hide');
                $('.js_practice_sch_footer').removeClass('hide').addClass('show');
            },
            error: function (jqXhr, textStatus, errorThrown) {
                $('.js-show-by-facility').addClass('hide');
                console.log(errorThrown);
            }
        });
    } else {
        resetTimings();
        $('.js-show-by-facility').addClass('hide');
        $('.js_practice_sch_footer_load').removeClass('show').addClass('hide');
        $('.js_practice_sch_footer').removeClass('hide').addClass('show');
    }
});

/// Reset all select box timings ///
function resetTimings() {
    $('#provider_scheduler_modal .js-from-selection-check').html('<option value="">-- From --</option>');
    $('#provider_scheduler_modal .js-to-selection-check').html('<option value="">-- To --</option>');
    $('#provider_scheduler_modal .js-additional-timing-set').addClass('hide');
    $('#provider_scheduler_modal .js-delete-more').removeClass('hide');
    $('#provider_scheduler_modal .js-add-more').removeClass('hide');
    //$('#provider_scheduler_modal .js-add-more-icon_1').removeClass('hide'); 
    $('#provider_scheduler_modal .js-from-selection-check').attr("disabled", false);
    $('#provider_scheduler_modal .js-to-selection-check').attr("disabled", false);
}

/* Starts - Disbale facility working hours to timings depending upon from timings selection */
$(document).on('change', '.js-from-selection', function () {
    current_parent_id = $(this).parents("div .form-group-billing").attr("id");

    current_id_name = $(this).attr("id");
    current_selection_value = $(this).val();
    split_current_id_name = current_id_name.split('from');
    to_id_by_current_id_name = split_current_id_name[0] + 'to' + split_current_id_name[1];

    var opt_vals = [];
    var reached_current_val = 0;
    $('#' + current_id_name + ' option').each(function () {
        value = $(this).val();
        if (reached_current_val == 1)
            opt_vals.push('<option value="' + value + '">' + value + '</option>');
        if (reached_current_val == 0 && current_selection_value == value)
            reached_current_val = 1;
    });
    $('#' + current_parent_id + ' #' + to_id_by_current_id_name).html('<option value="">-- To --</option>' + opt_vals);
    if (current_selection_value != '') {
        $('.alert-danger').addClass("hide");
    }
});

$(document).on('change', '.js-to-selection-check', function () {
    if ($(this).val() != '') {
        $('.alert-danger').addClass("hide");
    }
});

$(document).on('click', '.js_rmv_err_alt', function () {
    $('.alert-danger').addClass("hide");
});

/* Ends - Disbale facility working hours to timings depending upon from timings selection */

/* Starts - Add more */
$(document).on('click', '.js-add-more', function () {
    current_parent_id = $(this).parents("div .form-group-billing").attr("id");

    /// Get current div from and to values ///
    current_from_value = $("#" + current_parent_id + " .js-from-selection-check").val();
    current_to_value = $("#" + current_parent_id + " .js-to-selection-check").val();

    if (current_from_value != '' && current_to_value != '') {
        split_next_parent_id = current_parent_id.split('_');
        get_next_parent_id = split_next_parent_id[0];

        hide_div = $(this).attr("data-add_more_hide");
        show_div = $(this).attr("data-add_more_show");

        /// Get selected day options and remove already selected timings from the list and display it in next from option ///
        var get_current_day = $("#" + current_parent_id + " .js-from-selection-check").attr('id');
        var selected_day = get_current_day.split('_');
        var selected_from_string = $("#" + selected_day[0] + "_from" + hide_div).html();

        from_limit = '<option value="">-- From --</option>';
        to_limit = '<option value="' + current_to_value + '">' + current_to_value + '</option>';

        var from_index = selected_from_string.indexOf(from_limit);
        var to_index = selected_from_string.indexOf(to_limit);
        var final = selected_from_string.slice(from_index, to_index);
        selected_from_string = selected_from_string.replace(final, '');

        $('#' + get_next_parent_id + '_' + show_div + ' .js-from-selection').html('<option value="">-- From --</option>' + selected_from_string);

        /// Hide and show previous and after div /// 
        $('#' + current_parent_id + ' .js-add-more-icon_' + hide_div).addClass('hide');
        $('#' + get_next_parent_id + '_' + show_div).removeClass('hide');

        /// Disable before selected timings ///
        $('#' + current_parent_id + ' .js-add-more-disable_' + hide_div).attr("disabled", true);
    } else {
        $('.alert-danger').removeClass("hide");
    }
});
/* Ends - Add more */

/* Starts - Delete add more */
$(document).on('click', '.js-delete-more', function () {
    current_parent_id = $(this).parents("div .form-group-billing").attr("id");
    split_next_parent_id = current_parent_id.split('_');
    get_next_parent_id = split_next_parent_id[0];

    hide_div = $(this).attr("data-add_more_hide");
    show_div = $(this).attr("data-add_more_show");

    /// Hide and show previous and after div ///
    $('#' + get_next_parent_id + '_' + show_div + ' .js-add-more-icon_' + show_div).removeClass('hide');
    $('#' + get_next_parent_id + '_' + hide_div).addClass('hide');

    /// Reset from and to values of current day selection ///
    $('#' + get_next_parent_id + '_' + hide_div + ' .js-from-selection-check').val('');
    $('#' + get_next_parent_id + '_' + hide_div + ' .js-to-selection-check').html('<option value="">-- To --</option>');

    /// Enable before selected timings ///
    $('#' + get_next_parent_id + '_' + show_div + ' .js-add-more-disable_' + show_div).attr("disabled", false);
});
/* Ends - Delete add more */

// (Weekly->individual day checkbox selection) Display day time selection display option depending upon day selelction in weekly.
$(document).on('click', '.js-weekly-day-selection', function () {
    var get_current_p_tag_name = $(this).attr('name');
    var get_current_p_value = $("#provider_scheduler_modal #" + get_current_p_tag_name).val();
    if ($(this).hasClass('active')) {
        $('#provider_scheduler_modal #js-day-parent-' + get_current_p_value).addClass('hide');
        $("#provider_scheduler_modal #" + get_current_p_tag_name).removeAttr("checked");
    } else {
        $('#provider_scheduler_modal #js-day-parent-' + get_current_p_value).removeClass('hide');
        $("#provider_scheduler_modal #" + get_current_p_tag_name).prop("checked", "checked");
    }
    if ($("#provider_scheduler_modal .js-cls-weekly-day-selection").length == $("#provider_scheduler_modal .js-cls-weekly-day-selection:checked").length) {
        $("#provider_scheduler_modal #weekly_all").prop("checked", "checked");
        $("#provider_scheduler_modal #js-weekly_all").addClass("active");
    } else {
        $("#provider_scheduler_modal #weekly_all").removeAttr("checked");
        $("#provider_scheduler_modal #js-weekly_all").removeClass("active");
    }
});

$(document).on('click', '#js-weekly_all', function () {
    if ($('#provider_scheduler_modal #weekly_all').is(':checked')) {
        $('#provider_scheduler_modal .js-weekly-day-selection').removeClass('active');
        $('#provider_scheduler_modal .js-cls-weekly-day-selection').prop('checked', '');
        $('#provider_scheduler_modal #weekly_all').prop('checked', '');
        $('#provider_scheduler_modal .js-display-days-cls').addClass('hide');
    } else {
        $('#provider_scheduler_modal .js-weekly-day-selection').addClass('active');
        $('#provider_scheduler_modal .js-cls-weekly-day-selection').prop('checked', 'checked');
        $('#provider_scheduler_modal #weekly_all').prop('checked', 'checked');
        $('#provider_scheduler_modal .js-display-days-cls').removeClass('hide');
    }
});
// Weekly provider scheduler - if all checkbox are selected, check the selectall checkbox and viceversa
/*************************************************/
/********** Ends - Provider Scheduler **********/
/*************************************************/

// Insurance Self
function InsuranceSelf(Relationship) {
    if (Relationship.value == 'Self') {
        document.getElementById('insurance_last_name').value = document.getElementById('last_name').value;
        document.getElementById('insurance_first_name').value = document.getElementById('first_name').value;
        document.getElementById('insurance_mi').value = document.getElementById('personal_mi').value;
        document.getElementById('insured_dob').value = document.getElementById('dob').value;
        document.getElementById('insurance_address').value = document.getElementById('address1').value;
        document.getElementById('insurance_home_phone').value = document.getElementById('phone').value;
        document.getElementById('insurance_cell_phone').value = document.getElementById('mobile').value;
    } else {
        document.getElementById('insurance_last_name').value = '';
        document.getElementById('insurance_first_name').value = '';
        document.getElementById('insurance_mi').value = '';
        document.getElementById('insured_dob').value = '';
        document.getElementById('insurance_address').value = '';
        document.getElementById('insurance_home_phone').value = '';
        document.getElementById('insurance_cell_phone').value = '';
    }
}
//Insurance Self

//webcam capture
function my_completion_handler(msg) {
    var jsonString = JSON.parse(msg);
    var filename = '';
    filename = jsonString.data.filename;
    if (msg.match(/(http\:\/\/\S+)/)) {
        var image_url = RegExp.$1;
        // show JPEG image in page
        document.getElementById('upload_results').innerHTML =
            'Snapshot<br>' +
            '<a href="' + image_url + '" target"_blank"><img src="' + image_url + '"></a>';
        $('#webcam_image').val(1);
        $('#filename_image').val(filename);
        // reset camera for another shot
        webcam.reset();
    }
    else
        js_alert_popup("PHP Error: " + msg);
}

function isFlashEnabled() {
    var hasFlash = false;
    try {
        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
        if (fo)
            hasFlash = true;
    } catch (e) {
        if (navigator.mimeTypes["application/x-shockwave-flash"] != undefined)
            hasFlash = true;
    }
    return hasFlash;
}

$(document).on('keypress, keydown', '.js-address2-tab', function (e) {
    if (!e.shiftKey && e.keyCode === 9) {
        $(this).closest('div.form-group').next('div.form-group').find('.js-state-tab').focus();
    }
});

$(document).on('change', '.js-sel-provider-change', function (e) {
    var sel_provider_id = this.value;
    var curr_li = $(this).closest('div.form-group').find('p.js-sel-provider-type-dis');
    if (sel_provider_id != '') {
        $.ajax({
            type: 'GET',
            url: api_site_url + '/get_sel_provider_type_display/' + sel_provider_id,
            success: function (result) {
                curr_li.html(result);
                curr_li.removeClass('hide').addClass('show');
            }
        });
    } else {
        curr_li.html('');
        curr_li.removeClass('show').addClass('hide');
    }
});

$(document).ready(function () {
    $(document).on('mouseover', '.js-cls-tooltip', function (e) {
        tooltip_id = $(this).attr("data-fetchid");
        var tool_tip_content = $('.js-tooltip_' + tooltip_id).html();
        $('[data-toggle="tooltip"]').tooltip({ title: $('.js-tooltip_' + $(this).attr("data-fetchid")).html(), animation: false, html: true, trigger: "manual", delay: { show: 500, hide: 75 } });
    });
});

function daydiff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}

function parseDate(str) {
    var mdy = str.split('/')
    return new Date(mdy[2], mdy[0] - 1, mdy[1]);
}

function checkvalid(str) {
    var mdy = str.split('/');
    if (mdy[0] > 12 || mdy[1] > 31 || mdy[2].length < 4 || mdy[0] == '00' || mdy[0] == '0' || mdy[1] == '00' || mdy[1] == '0' || mdy[2] == '0000') {
        return false;
    }
}

function startdateFunction(start_date, end_date) {
    var date_format = new Date(end_date);
    if (start_date == '') {
        return strdate_req_lang_err_msg;
    }
    if (end_date != '' && date_format != "Invalid Date") {
        return (start_date == '') ? strdate_req_lang_err_msg : true;
    }
    return true;
}

function enddateFunction(start_date, end_date) {
    var eff_format = new Date(start_date);
    var ter_format = new Date(end_date);
    if (end_date == '') {
        return enddate_req_lang_err_msg;
    }
    if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
        var getdate = daydiff(parseDate(start_date), parseDate(end_date));
        return (getdate > 0) ? true : end_date_lang_err_msg;
    }
    else if (start_date != '' && eff_format != "Invalid Date") {
        return (end_date == '') ? enddate_req_lang_err_msg : true;

    }
    return true;
}

/*** Starts - First letter caps for first and last name and all letters caps for group id, group name and policy id ***/
$(document).delegate('.js-letters-caps-format', 'keyup', function (e) {             // Delegate changed by revathi due to apply on popup data tooo
    $('.js-letters-caps-format').upperFirstAll();
});

(function ($) {
    $.fn.extend({
        // With every keystroke capitalize first letter of ALL words in the text
        upperFirstAll: function () {
            $(this).keyup(function (event) {
                var box = event.target;
                var txt = $(this).val();
                var start = box.selectionStart;
                var end = box.selectionEnd;

                $(this).val(txt.toLowerCase().replace(/^(.)|(\s|\-)(.)/g,
                    function (c) {
                        return c.toUpperCase();
                    }));
                box.setSelectionRange(start, end);
            });
            return this;
        }
    });
}(jQuery));

$(document).on('keyup', '.js-all-caps-letter-format', function (e) {
    var str = $(this).val();
    var start = this.selectionStart,
        end = this.selectionEnd;
    $(this).val(str.toUpperCase());
    this.setSelectionRange(start, end);
});

$(document).on('keyup', '.js-email-letters-lower-format', function (e) {
    if (!(e.keyCode == 8) && !(e.keyCode == 16) && !(e.keyCode == 35) && !(e.keyCode == 36) && !(e.keyCode == 37) && !(e.keyCode == 38) && !(e.keyCode == 39) && !(e.keyCode == 40)) {
        var str = $(this).val();
        var str1 = str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toLowerCase() + txt.substr(1).toLowerCase();
        });
        var start = this.selectionStart,
            end = this.selectionEnd;
        $(this).val(str1);
        this.setSelectionRange(start, end);
    }
});
//textarea first word first letter only uppercase format
$(document).on('keyup', '.js-firstletter-caps-format', function () {
    dataValue = $(this).val();
    resValue = titleCase(dataValue);
    $(this).val(resValue);
});

function titleCase(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/*** Temporary solution for data mask plugin in safari only starts ***/
$(document).on('keypress keydown keyup', '.medcubicsform input[type="text"],.popupmedcubicsform input[type="text"]', function (event) {
    var self = this;
    var current_class = $(this).attr("class");
    if (typeof current_class !== "undefined") {
        if (current_class.indexOf("dm-") > -1) {
            var regex = new RegExp("^[a-zA-Z0-9]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            var str = $(self).val();
            var start = self.selectionStart;
            var end = self.selectionEnd;
            if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 && regex.test(key)) {
                var regexp = new RegExp(/^[A-Za-z0-9]+$/);
                var identify = str.charAt(end - 1);
                if (!regexp.test(identify)) {
                    for (var i = end; i < str.length; i++) {
                        if (regexp.test(str.charAt(i))) {
                            start = i + 1;
                            end = i + 1;
                            break;
                        }
                    }
                }
                self.setSelectionRange(start, end);
            }
        }
    }
});
/*** Temporary solution for data mask plugin in safari only end ***/

/*
 $(document).on( 'keypress keyup', '.js_amount_separation', function () {
 var response_val = numSeparator($(this).val());
 var start = this.selectionStart,
 end = this.selectionEnd;
 $(this).val(response_val);
 this.setSelectionRange(start, end);
 });
 $(document).on( 'blur', '.js_amount_separation', function () {
 var response_val = $(this).val();
 if(response_val.length >0) {
 var value= response_val.toString().split(".");
 if(value[1].length ==1) {
 value[1] =  value[1]+0;
 var replaced_str =  value.join(".");
 $(this).val(replaced_str);
 }
 }
 });
 
 function numSeparator(str) {
 if(str =='') return '';
 var n= str.toString().split(".");
 if(str.length > 12){
 n[0]=str.substr(0, 10);
 n[1]=str.substr(11, 12);
 }  
 n[1] = (n.length>1)? n[1] : "00";
 var special_char=[];
 special_char[0] = n[0].replace(/[^0-9]/g, "");
 special_char[1] = n[1].replace(/[^0-9]/g, "");
 var get_decimal_str =  special_char.join(".");
 if(special_char[1].length ==1)
 var decimal_str =  get_decimal_str;
 else
 var decimal_str =  parseFloat(get_decimal_str).toFixed(2);
 var new_decimal_str =  decimal_str.split(".");
 new_decimal_str[0] = new_decimal_str[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
 var newstr =  new_decimal_str.join(".");
 var total_length = newstr.length;
 if(total_length>13)
 newstr = numSeparator(newstr);
 return newstr;
 
 //var get_convert = (5 <= value[1].slice(2,3)) ? 1 : 0;
 //var second_val   = parseInt(value[1].slice(1,2))+parseInt(get_convert);
 //var value_string= second_val.toString();
 //value[1]     = value[1].slice(0,1)+value_string;
 //var replaced_str =  value.join(".");
 }*/
$(document).on('blur', '.js_amount_separation', function (e) {
    var response_val = $(this).val();
    if (response_val.length > 0) {
        var count_length = response_val.split(".").length - 1;
        if (count_length > 0) {
            var value = response_val.split(".");
            var replaced_str = (value[0].length == 0) ? 0 + "." + value[1] : parseFloat(response_val).toFixed(2);
            var replaced_str = (value[1].length == 1) ? response_val + 0 : parseFloat(response_val).toFixed(2);
        } else {
            replaced_str = response_val + ".00";
        }
        var start = this.selectionStart;
        end = this.selectionEnd;
        $(this).val(replaced_str);
        this.setSelectionRange(start, end);
    }
});

$(document).on('keydown', '.js_amount_separation', function (e) {
    if ($.inArray(e.keyCode, [116, 45, 189, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A,Ctrl+C,Ctrl+V, Command+A
        ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        if ((e.keyCode == 189 || e.keyCode == 45) && $(this).val().indexOf('-') != -1) {
            event.preventDefault();
        }
        else
            return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

function getMinAlert(min_value, max_value, error_msg) {
    if ((min_value == '') || (max_value == '') || (min_value == null) || (max_value == null))
        return true;
    else {
        var min_value = parseFloat(min_value);
        var max_value = parseFloat(max_value);
        return (max_value >= min_value) ? true : error_msg;
    }
    return true;
}
/*** Ends - First letter caps for first and last name and all letters caps for group id, group name and policy id ***/

/*** Starts - Scroll bar afer certain height ***/
$('#chat-box').slimScroll({
    height: '70px'
});

$('#checkboxlist').slimScroll({
    height: '170px'
});

$('.superbill-list').slimScroll({
    height: '125px'
});
$('.scheduler-notes').slimScroll({
    height: '73px'
});

$('.scheduler-timetable').slimScroll({
    height: '300px'
});

$('.superbill-extra').slimScroll({
    height: '120px'
});

$('.pateint-esuperbill-scroll').slimScroll({
    height: '480px'
});

$('#chat-box1').slimScroll({
    height: '70px'
});

$('#chat-scheduler').slimScroll({
    height: '145px'
});

$('#chat-resource').slimScroll({
    height: '140px'
});

$('.ledger-ins').slimScroll({
    height: '206px'
});

$('.ledger-insurance').slimScroll({
    height: '232px'
});

$('.ledger-insurance1').slimScroll({
    height: '157px'
});

$('.demographics-add').slimScroll({
    height: '750px'
});

$('.demographics-add1').slimScroll({
    height: '600px'
});

$('.ledger-scroll').slimScroll({
    height: '248px'
});

$('.ledger-apts').slimScroll({
    height: '110px'
});

$('.chat-facility').slimScroll({
    height: '140px'
});

$('.chat-scheduler').slimScroll({
    height: '158px'
});

$('.mail-list').slimScroll({
    height: '318px'
});

$('.mail-list-body').slimScroll({
    height: '380px'
});

$('.mail-compose').slimScroll({
    height: '367px'
});

$('.ar-notes').slimScroll({
    height: '150px'
});

$('.ar-notes-scroll').slimScroll({
    height: '120px'
});

$('.ar-denials').slimScroll({
    height: '300px'
});

$('.pymt-codes').slimScroll({
    height: '112px'
});

$('.payment-ids').slimScroll({
    height: '368px'
});

$('.chat-npi').slimScroll({
    height: '208px'
});

/* Modal Popup Draggable * / Edited by Gopal [mention with parent div]
 $(".modal-dialog").draggable({
 handle: ".modal-content",                
 containment: '.wrapper'
 });*/

/* Table tr show hide starts*/
$(document).on('click', '.toggler', function (e) {
    e.preventDefault();
    var tag_name = $(this).prop("tagName");
    if (tag_name == "TD" || tag_name == "td")
        var object = $(this).find("a");
    else
        var object = $(this);
    object.toggleClass("toggle-plus toggle-minus");
    $('.cat' + object.attr('data-prod-cat')).toggle();
});
/* Table tr show hide Ends*/


$(document).on('click', '.txtoggler', function (e) {
    e.preventDefault();
    var tag_name = $(this).prop("tagName");
    if (tag_name == "TD" || tag_name == "td")
        var object = $(this).find("a");
    else
        var object = $(this);
    var ident = object.attr('data-prod-cat');
    //object.toggleClass("toggle-plus toggle-minus");  
    if ($(this).parents('table').find('.blk_' + ident).length > 1) {
        $('.blk_' + ident).toggle();
        $('.blk_' + ident).find("td:first-child > a").toggleClass("toggle-plus toggle-minus");
        //$('.oblk_' + ident).toggle();
        //$('.oblk_' + ident).find("td:first-child > a").toggleClass("toggle-plus toggle-minus");    
    } else {
        return false;
    }
});

var $content = $(".notes");
$(".ar-checkbox").on("click", function (e) {
    $(this).toggleClass("expanded");
    $content.slideToggle();
});

var $content = $(".transactions1");
$(".ar-checkbox1").on("click", function (e) {
    $(this).toggleClass("kl");
    $content.slideToggle();
});

/*** Ends - Scroll bar afer certain height ***/

/*** Starts - Displaying patient deails afer scrolling certain height ***/
var elementPosition = $('#navigation').offset();
$(window).scroll(function () {
    if ($(window).scrollTop() > 120) {
        $('#navigation').css('position', 'fixed').css('top', '45px').css('width', '86.8%');
    } else {
        $('#navigation').css('position', 'static').css('width', '100%');
    }
});

$(document).scroll(function () {
    var y = $(this).scrollTop();
    if (y > 180) {
        $('.bottomMenu').fadeIn();
    } else {
        $('.bottomMenu').fadeOut();
    }
});
/*** Ends - Displaying patient deails afer scrolling certain height ***/

/*** Starts - Toggle Used in Charge Entry Screen ***/
$(document).ready(function () {
    $('.nav-toggle').click(function () {
        //get collapse content selector
        var collapse_content_selector = $(this).attr('href');

        //make the collapse content to be shown or hide
        var toggle_switch = $(this);
        $(collapse_content_selector).toggle(function () {
            if ($(this).css('display') == 'none') {
                //change the button label to be 'Show'
                toggle_switch.html('+');
            } else {
                //change the button label to be 'Hide'
                toggle_switch.html('-');
            }
        });
    });
    $(".timepicker").timepicker({
        showInputs: false
    });
    $("[id^='form_container']").hide();
    $("#show_hide_details").click(function () {
        $(this).parents('tr').next("h3").toggle();
    });

});
/*** Ends - Toggle Used in Charge Entry Screen ***/

/*** admin site disable inspect element in right click, f12 and shift+ctrl+i start  *** /
 /*     
 $(document).ready(function()
 { 
 $(document).bind("contextmenu",function(e){
 return false;
 }); 
 $(document).keydown(function(event) {
 if(event.keyCode==123) {
 return false;
 }
 else if(event.ctrlKey && event.shiftKey && event.keyCode==73) {        
 return false;  //Prevent from ctrl+shift+i
 }
 });
 });
 /*** disable inspect element in right click, f12 and shift+ctrl+i end ***/

/** Get filename for uploading files starts**/

$(document).on('change', 'input[type="file"]', function () {
    var url = window.location.href;
    var arr = url.split('/');
    if (jQuery.inArray("documentsummary#create_document", arr) != -1 || jQuery.inArray("documents", arr) != -1 || (jQuery.inArray("patients", arr) != -1 && jQuery.inArray("edit", arr) != -1) || jQuery.inArray("facility", arr) != -1 || jQuery.inArray("provider", arr) != -1) { } else {
        var get_form_id = $(this).parents("form").attr("id");
        var file = $('#' + get_form_id).find('input[name="' + $(this).attr("name") + '"]').val().replace(/C:\\fakepath\\/i, '');
        var file_name = file.substring(0, 30);
        var file = (file.length > 30) ? file_name + ".." : file; // changed due to insufficient space
        $(this).parents("span").closest("div").find('.js-display-error').html(file);
        if (file != '') {
            $(".removeFile").show();
        }
    }
});

/** Get filename for uploading files ends**/

/** Insurance details in modal box start **/
$(document).delegate("a[data-target=#js-model-insurance-details]", 'click', function (e) {
    $(".js-sel-modalinsurance-address").attr('data-id', '');
    $('.js_modal_search_insurance_keyword').val('');
    $('#search_insurance_keyword_err').addClass('hide');
    $('#search_insurance_keyword_err_content').html('');
    $('#insurance_search_result').html('');
    $("#js_insurace_search_category_modal").select2("val", "insurance_name");
    $(this).parent('div').prev('div').find(".js-sel-modalinsurance-address").attr('data-id', 'CMINS99');
});

$(document).on('click', '.js_modal_search_insurance_button', function () {
    var serach_keyword = $(".js_modal_search_insurance_keyword").val();
    var serach_category = $("#js_insurace_search_category_modal").val();
    serach_keyword = serach_keyword.replace(/ +$/, "");
    if (serach_keyword == "") {
        $('#insurance_search_result').html('');
        $('#search_insurance_keyword_err').removeClass('hide');
        $('#search_insurance_keyword_err_content').html('Please enter search keyword!');
    } else if (serach_keyword.length < 4) {
        $('#insurance_search_result').html('');
        $('#search_insurance_keyword_err').removeClass('hide');
        $('#search_insurance_keyword_err_content').html('Enter more than 3 characters!');
    } else if (/^[a-zA-Z0-9- ,]*$/.test(serach_keyword) == false) {
        $('#insurance_search_result').html('');
        $('#search_insurance_keyword_err').removeClass('hide');
        $('#search_insurance_keyword_err_content').html('Special characters are not allowed');
    } else {
        $('#insurance_search_result').css('text-align', 'center').html('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing');
        $('#search_insurance_keyword_err').addClass('hide');
        $('#search_insurance_keyword_err_content').html('');
        var target = api_site_url + '/api/getinsurance_details_modal/' + serach_keyword + '/' + serach_category;
        $.ajax({
            type: 'GET',
            url: target,
            success: function (res) {
                $('#insurance_search_result').css('text-align', 'initial').html(res);
                $('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
            }
        });
    }
});

$(document).on('ifChecked change', "input[name='js_modal_insurance_id']", function () {
    var sel_value = this.value;
    setTimeout(function () {
        $('select.js-sel-modalinsurance-address[data-id="CMINS99"]').select2("val", sel_value);
        $('select.js-sel-modalinsurance-address[data-id="CMINS99"]').trigger("change");
        //$('#js-model-insurance-details').modal('hide');
        addModal('js-model-insurance-details');
    }, 0);
});

$(document).on('click', ".js_insurance_search_modal_close", function () {
    addModal('js-model-insurance-details');
});

/** Insurance details in modal box end **/

/*** Start to hide arrow button in dropdown ***/
$(document).on('change', '.select2', function () {
    var idname = $(this).prev().attr('id');
    if ($(this).next().attr('data-bv-icon-for') != undefined) {
        $('#' + idname).find('.select2-arrow b').css('display', 'none');
    }
});
/*** End to hide arrow button in dropdown ***/

$(document).delegate("a[data-target=#js-model-swith-patient]", 'click', function (e) {
    $('#search_swith_patient_keyword_err').addClass('hide');
    $('#search_swith_patient_keyword_err_content').html('');
    $('#swith_patient_search_result').html('');
    $(".js_modal_search_swith_patient_keyword").val('').focus();
});

$(document).on('change', '.js_modal_search_patient_by', function () {
    var serach_by = $(this).val();
    if (serach_by != 'dob') {
        $('.js_modal_search_swith_patient_keyword').removeClass('dm-date ssn').val("");
        $('.js_modal_search_swith_patient_keyword').unmask();
        if (serach_by == 'ssn') { $('.js_modal_search_swith_patient_keyword').attr('maxlength', '9'); }
    } else {
        $('.js_modal_search_swith_patient_keyword').addClass('dm-date').val("");
    }
});

$(document).on('click', '.js_modal_search_swith_patient_button', function () {
    $('#swith_patient_search_result').html('');
    var search_by = $("#js_modal_search_patient_by").val();
    var search_keyword = $(".js_modal_search_swith_patient_keyword").val();
    search_keyword = search_keyword.replace(/ +$/, "");
    if (search_keyword == "") {
        $('#search_swith_patient_keyword_err').removeClass('hide');
        $('#search_swith_patient_keyword_err_content').html(kwd_req_lang_err_msg);
    } else if (search_keyword.length < 3) {
        $('#search_swith_patient_keyword_err').removeClass('hide');
    } else if (search_by == 'ssn' && search_keyword.length != 9) {
        $('#search_swith_patient_keyword_err').removeClass('hide');
        $('#search_swith_patient_keyword_err_content').html("SSN must be 9 digits");
    } else if (search_by == 'ssn' && isNaN(search_keyword)) {
        $('#search_swith_patient_keyword_err').removeClass('hide');
        $('#search_swith_patient_keyword_err_content').html("SSN must be digits only allowed");
    } else {
        $('#search_swith_patient_keyword_err').addClass('hide');
        $('#search_swith_patient_keyword_err_content').html('');
        var data = "search_by=" + search_by + "&search_keyword=" + search_keyword;
        var target = api_site_url + '/api/getswitchpatient_details_modal';
        $.ajax({
            type: 'POST',
            url: target,
            data: data,
            success: function (res) {
                //$('.js_modal_search_swith_patient_keyword').val('');
                $('#swith_patient_search_result').html(res);
                $("#patientmodal_details_tbl").DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "info": false,
                });
            }
        });
    }
});

$(document).on('click', '#notification-practice-user', function () {
    var current_page = $('#current_page').val();
    $.ajax({
        type:"GET",
        url: api_site_url+"/practice/Notification/msg-notes",        
        success: function(result){
            //console.log(result);
            $("#dropdown-menu-notification").html(result);
           // $('#notification-practice-user').children(’.dropdown-menu’).toggle();
        }
    });
});

$(document).on('click', '#wishlist-practice-user', function () { 
    var current_page = $('#current_page').val();
    $.ajax({
        type:"GET",
        url: api_site_url+"/practice/Notification/wishlist",
      //  data: "current_page="+current_page,        
        success: function(result){
            //console.log(result);
            $("#dropdown-menu-wishlist").html(result);
           // $('#notification-practice-user').children(’.dropdown-menu’).toggle();
        }
    });
});

/*** Start to ask confirmation if any changes in form ***/
$(document).on('click', '.js_cancel_site', function () {
    var collecturl = $(this).closest("a").attr('data-url');
    window.location.href = collecturl;
});

$("a").click(function () {
    window.onbeforeunload = UnPopIt;
});

$(document).on('click', '[type=submit],[type=button]', function () {
    window.onbeforeunload = UnPopIt;
});

function PopIt() {
    return "Are you sure you want to close the window?";
}

function UnPopIt() { /* nothing to return */
}

// set 0, while click document module link.
var mg = 0;
$(document).on('keyup change', '.popupmedcubicsform input,input[type="radio"]:checked,input[type="checkbox"]:checked,.popupmedcubicsform textarea,.popupmedcubicsform select', function () {
    mg++;
});

$(document).on('click', '.close_popup,.close:not(".normal_popup_form")', function () { //alert('1');
    var form_id = $(this).closest(".modal.in").attr('id');
    var data_form_id = $(this).parents(".modal.in").attr('data-id');
    var form_name = $(this).attr('data-name');
    //console.log("form_id"+form_id);
    if (mg != '0' && ms != 'submitted' && form_id != 'eligibility_content_popup' && form_id != 'session_model' && data_form_id != 'session_model' && typeof form_id != "undefined") {
        //console.log("In true 1 #3019");
        var confirm_msg = ($('#' + form_id + " .js_set_confirm_msg").length > 0) ? $('#' + form_id + " .js_set_confirm_msg").val() : "Would you like to save the changes made?";
        var model_text_msg = "Would you like to save the attachments?";
        if (form_id == 'document_add_modal') {
            $("#session_model .med-green").html(model_text_msg);
        } else
            $("#session_model .med-green").html(confirm_msg);
        $("#session_model")
            .modal({ show: 'false', keyboard: false })
            .on('click', '.js_session_confirm', function (e) {
                var conformation = $(this).attr('id');
                if (conformation == "true") {
                    $('.js_move_message').trigger("click"); //Bulk statement Edit message-> close button press time
                    //   $('#' + form_id).find('input[type="submit"]').trigger("click"); // validation
                    if ($(".popupmedcubicsform").hasClass("js-auth-form")) {
                        mg = 0;
                    }
                    window.onbeforeunload = UnPopIt;
                    addModalClass(); //check and add class if any popup exist
                } else {
                    $('#' + form_id).find("select").val("").select2();
                    window.onbeforeunload = UnPopIt;
                    setTimeout(function () {
                        addModal(form_id);
                    }, 10);
                    mg = 0;
                }
            });
    }
    else {
        mg = 0;
        addModal(form_id);
    }
    // Checked form id attribute exist checking included.
    if (typeof form_id !== typeof undefined && form_id !== false && form_id.match("^denial_details_")) {
        // Cleared denails codes from model window, to avoid hanging on the browser window issue
        $("#" + form_id).find(".table-borderless > tbody").html("");
    }
});

function addModal(form_id, event_id) {
    setTimeout(function () {
        if ($('.modal:visible').length > 1) {
            $('#' + form_id).modal('hide');
            addModalClass();
            if (form_id == "fullCalendarModal_schedular" && event_id != '') {
                reloadOldform("fullCalendarModal", event_id);
            }
        } else {

            $('#' + form_id).modal('hide');
            setTimeout(function () {
                $('body').removeClass('modal-open').removeAttr("style");
            }, 400);//Getting exact popup close function works
        }
    }, 300);

}

function addModalClass() {
    setTimeout(function () {
        $('body').addClass('modal-open');
    }, 300);
}

removeModalClass();
function removeModalClass() {
    $('.modal.in').removeClass('in').attr("aria-hidden", "true");
    $('body').removeClass('modal-open').removeAttr("style");   //Getting exact popup close function works
}
set_val = 1;
// To avoid unnessary popup display from charges page starts here
/*$(document).ready(function () {
    set_val = 1;
});*/
f = 0;
$(document).on('keyup change', '.medcubicsform input,input[type="radio"]:checked,input[type="checkbox"]:checked,.medcubicsform textarea,.medcubicsform select', function (event) {
    if (event.which != 13 && set_val != 1) {
        window.onbeforeunload = PopIt;
        f++;
    } else if (set_val == 1) {
        set_val = 0;
    }
});


// To avoid unnessary popup display from charges page ends here
function clearpopItValue() {
    var f = 0;
    var mg = 0;
}

/*** start to confirmation for billing *** / 
 var g = 1;
 $(document).on('change','.js_billingform select',function(event) {
 f++;
 if(f==1 && g==1){
 g++;
 f--;
 }
 });        
 /*** End to confirmation for billing *** / 
 
 $(document).on('change','.medcubicsform select',function(event) {
 if(!$(".medcubicsform").hasClass( "js_billingform" )) {
 window.onbeforeunload = PopIt;
 f++;
 }
 });*/

$(document).on('click', '.js_next_process', function (ev) {
    var cancel_payment_length = $('.js-cancel-payment').length;
    var collecturl = $(this).attr('href');

    if (collecturl == 'javascript:void(0)') {
        var collecturl = $(this).attr('data-url');
    }

    /* Form save in any change in form  */
    if ($(this).parents("div.wrapper").find('.medcubicsform').length != 0 && !cancel_payment_length) {
        ev.preventDefault();
        if (f != 0) {
            /* patient contact patient Authorization form submit disable  Multiple form is showed on same page*/
            if ($(".tab-pane.active").length > 0 && ($(".tab-pane.active").attr("id") == "contact-info" || $(".tab-pane.active").attr("id") == "authorization-info")) {
                window.location.href = collecturl;
                return false;
            }
            var confirm_msg = ($(".tab-pane.active").length > 0) ? $(".tab-pane.active").find(".js_set_confirm_msg").val() : '';
            if (confirm_msg == '') {
                confirm_msg = ($(".js_set_confirm_msg").length > 0) ? $(".js_set_confirm_msg").val() : "";
            }
            if (confirm_msg == '') {
                confirm_msg = "Would you like to save the changes made?";
            }
            //for submodule click event popup button text change in patient demographic
            if ($(this).parents("div.wrapper").find('.medcubicsform').hasClass('patients-info-form')) {
                $("#session_model").find(".js_session_confirm:first").text("Yes");
                $("#session_model").find(".js_session_confirm:last").text("No");
            }
            //for submodule click event popup text message change in patient charge created
            if ($(this).parents("div.wrapper").find('.medcubicsform').hasClass('js_billingform')) {
                confirm_msg = "Please enter the required fields";
                $("#session_model").find(".js_session_confirm:first").text("Continue");
                $("#session_model").find(".js_session_confirm:last").text("Ignore");
            }

            $("#session_model .med-green").html(confirm_msg);
            $("#session_model")
                .modal({ show: 'false', keyboard: false })
                .one('click', '.js_session_confirm', function (e) {
                    var conformation = $(this).attr('id');
                    if (conformation == "true") {
                        if ($(".medcubicsform").hasClass("contact-info-form") || $(".medcubicsform").hasClass("insurance-info-form") || $(".medcubicsform").hasClass("authorization-info-form")) {
                            $(".medcubicsform").submit();
                        } else {
                            var bootstrapValidator = $("#js_bootstrap_validator,#js-bootstrap-validator,#js-bootstrap-validator1").data('bootstrapValidator');
                            bootstrapValidator.validate();
                            if (bootstrapValidator.isValid()) {
                                var formname = $(".medcubicsform").attr('name');
                                document.forms[formname].submit();
                            }
                        }
                    } else {
                        window.location.href = collecturl;
                    }
                });
        } else if (!cancel_payment_length) {
            window.location.href = collecturl;
        }
    } else if (!cancel_payment_length) {
        window.location.href = collecturl;
    }
});
/*** End to ask confirmation if any changes in form ***/

/*** Start to practice submenu in API Settings ***/
$(document).on('ifToggled click change', '.js_submenu_api', function (ev) {
    var collectapi = $(this).attr('data-id');
    if ($(this).prop('checked') == true) {
        var gettype = $(this).parents('.js-each-api').find('.js_menu').attr('type');
        if (gettype != 'checkbox') {
            if ($('.' + collectapi).parent().attr('aria-checked') != true) {
                $(this).parents('.js-each-api').find('.js_menu').prop('checked', true);
                var getvalue = $(this).parents('.js-each-api').find('.js_menu').attr('value');
                $('.js_menu_api').each(function () {
                    var main_api_name = $(this).attr('value');
                    if (getvalue != main_api_name) {
                        $(this).prop('checked', false);
                    }
                });
                $('.js_submenu_api').each(function () {
                    var api_name = $(this).attr('data-id');
                    var gettype_other = $(this).parents('.js-each-api').find('.js_menu').attr('type');
                    if (gettype_other != 'checkbox') {
                        if (api_name != collectapi && gettype == 'radio') {
                            $(this).prop('checked', false);
                        }
                    }
                });
                //$('input.flat-red').iCheck('update');
            }
        }
    } else {
        var curent_class = $(this).parents('.js-each-api').find('.js_menu').attr('id');
        var checked_count = 0;
        $('.' + curent_class + '.js_submenu:checked').each(function (i) {
            checked_count += 1;
        });

        if (checked_count == 0) {
            $("#" + curent_class).prop('checked', false);
            //$('input.flat-red').iCheck('update');
        }
    }
});

if ($('.js-each-api').length) {
    $('.js-each-api').each(function () {
        //console.log("menu check");
        var parent_id = $(this).attr("data-id");
        menuCheck(parent_id);
    });
}
/*** End to practice submenu in API Settings ***/

/*** Start to common alert popup ***/
function js_alert_popup(msg) {
    $("#patientnote_model .med-green").html(msg);
    $("#patientnote_model").modal('show');
    addModalClass();
}

/*$('#patientnote_model').on('shown.bs.modal', function (e) {   
 $(body).removeClass('modal-open');
 $('#patientnote_model').addClass('modal-open');
 alert("ks")
 });
 
 $('#patientnote_model').on('hidden.bs.modal', function (e) {
 //$('#patientnote_model').modal('hide');
 //$('body').removeClass('modal-open');
 $('.modal-backdrop').remove();
 alert("k")
 });*/

function js_alert_popup_title(msg, title) {
    if (title != null) {
        $("#patientnote_model .modal-title").html(title);
    }
    $("#patientnote_model .med-green").html(msg);
    $("#patientnote_model")
        .modal({ show: 'open', keyboard: false });
}
/*** End to common alert popup ***/

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

$(document).on('keyup', '.medcubicsform input[type="text"]:not(".js_need_regex"),.popupmedcubicsform input[type="text"]:not(".js_need_regex")', function (e) {
    var result = $(this).val();
    if ($('input[name="payment_type"]').val() != "Adjustment") {
        var str = spaceReplace(result);
    }
    var first_char = result.charAt(0).replace(/\s/g, "");
    var start = this.selectionStart,
        end = this.selectionEnd;
    if ($('input[name="payment_type"]').val() != "Adjustment") {
        $(this).val(str);
    }
    this.setSelectionRange(start, end);
});

$(document).on('keypress', '.dm-phone', function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

function cityStatevalitation(another_name, form_name) {
    $('[name="' + form_name + '"]').bootstrapValidator('revalidateField', another_name);
}

$(document).on('keyup', 'input.dm-phone-ext', function () {
    var current_class = $(this).attr('name');
    var form_name = $(this).parents("form").attr('name');
    var form_id = $(this).parents("form").attr('id');
    var finder_name = (form_name == '' || form_name == null) ? "#" + form_id : '[name="' + form_name + '"]';
    if (current_class.match("ext").length > 0) {
        var another_name = $(this).closest("div").prev().prev().find("input").attr("name");
        if (another_name == null || another_name == "") {
            var another_name = $(this).closest('div').prev().find('input').attr("name");
        }
        if ($(this).closest(".form-group").hasClass("has-feedback")) {
            $(finder_name).bootstrapValidator('revalidateField', another_name);
        }
    }
});
/*** City State Valitation end ***/

/*** DOS date when enter 2 digit it has come select dropdown function start ***/
$(document).on("select2-close keyup", 'select.js-dos-select-year', function (event) {
    var year_val = $(this).val();
    var get_input_val = $(this).closest(".js_select_range").next("input.dm-date").val().split("/");
    if (year_val == '' || year_val == null)
        year_val = get_input_val[2];
    var new_date = get_input_val[0] + "/" + get_input_val[1] + "/" + year_val;
    var dos_selector = $(this).closest(".js_select_range").next("input.dm-date");
    dos_selector.val(new_date).focus().unbind('keyup');
    // This is changed for charges page validation
    if (dos_selector.hasClass('js_validate_date'))
        dos_selector.trigger('change');
    $(".js_select_range").remove();
});
/*
MED-2835
$(document).on('keyup', 'input.dm-date', function (event) {
    var code = event.keyCode || event.which;
    if (code != 27) {
        var object = this;
        var current_name = $(this).attr('name');
        if (typeof current_name !== "undefined") {
            var current_selctor_name = current_name.toLowerCase();
            if ((current_selctor_name.indexOf("dos") >= 0 || current_selctor_name.indexOf("date_of_service") >= 0) && current_selctor_name.indexOf("followup_dos") < 0 && current_selctor_name.indexOf("dosfrom") && current_selctor_name.indexOf("dosto")) {
                var current_value_arr = $(this).val().split("/");
                if (current_value_arr.length > 2 && current_value_arr[2].length == 2) {
                    $(object).datepicker("hide");
                    var year_range = getYearRange(current_value_arr[2]);
                    var html = '<span class="js_select_range"><span id="set_dos_dropdown"> <select class="select2 js-dos-select-year no-padding">' + year_range + '</select></span></span>';
                    if ($("#set_dos_dropdown").length == 0)
                        $(object).before(html);
                    else
                        $("#set_dos_dropdown select").html(year_range);

                    $("#set_dos_dropdown select").select2({
                        minimumResultsForSearch: -1
                    });
                    $("#set_dos_dropdown select").select2("open");
                    $("#set_dos_dropdown select").select2("val", "");
                    /*if($(object).closest(".form-group").hasClass("has-feedback") || $(object).closest(".form-group-billing").hasClass("form-group-billing")) {
                     var form_id = $(this).parents("form").attr('id');
                     var form_name = $(this).parents("form").attr('name');
                     var finder_name = (form_id ==''|| form_id ==null) ?  '[name="'+form_name+'"]' : "#"+form_id ;
                     $(finder_name).bootstrapValidator('revalidateField', current_name);
                     }
                }
            }
        }
    }
});*/

$(document).on('keyup', 'input.dm-date', function (event) {
    if ($('select.js-dos-select-year').length == 0 && !$(this).datepicker("widget").is(":visible")) {
        var object = this;
        var current_name = $(this).attr('name');
        if (typeof current_name !== "undefined") {
            var current_selctor_name = current_name.toLowerCase();
            if (current_selctor_name.indexOf("dos") >= 0 || current_selctor_name.indexOf("date_of_service") >= 0) {
                var current_value_arr = $(this).val().split("/");
                if (current_value_arr.length > 2 && current_value_arr[2].length == 2) {
                    var currentDate = new Date();
                    var get_full_year = currentDate.getFullYear().toString();
                    var get_year = get_full_year.substr(0, 2) + current_value_arr[2];
                    var new_date = current_value_arr[0] + "/" + current_value_arr[1] + "/" + get_year;
                    $(object).val(new_date);
                    if ($(this).hasClass('js_validate_date'))
                        $(this).change()
                }
            }
        }
    }
});

function getYearRange(twodigit_year) {
    var currentDate = new Date();
    var get_full_year = currentDate.getFullYear().toString();
    var get_year = parseInt(get_full_year.substr(0, 2) + twodigit_year);
    var html = "";
    var get_year_arr = [get_year];
    for (var i = 0; i < 30; i++) {
        get_year_str = parseInt(currentDate.getFullYear()) - i;
        get_year_arr.push(get_year_str);
    }
    get_year_arr.sort().reverse();
    var uniqueNames = [];
    $.each(get_year_arr, function (index, val) {
        if ($.inArray(val, uniqueNames) === -1) {
            uniqueNames.push(val);
            if (get_year == val)
                html += '<option value="' + val + '" selected>' + val + '</option>';
            else
                html += '<option value="' + val + '">' + val + "</option>";
        }
    });
    return html;
}
/*** DOS date when enter 2 digit it has come select dropdown function end ***/

/*** AR Management followup details start ****/
$(document).ready(function () {
    $('input[type="radio"]').click(function () {
        if ($(this).attr("value") == "claim_nis") {
            $(".followup-box").not(".claim_nis").hide();
            $(".claim_nis").show();
        }
        if ($(this).attr("value") == "claim_in_process") {
            $(".followup-box").not(".claim_in_process").hide();
            $(".claim_in_process").show();
        }
        if ($(this).attr("value") == "claim_paid") {
            $(".followup-box").not(".claim_paid").hide();
            $(".claim_paid").show();
        }
        if ($(this).attr("value") == "claim_denied") {
            $(".followup-box").not(".claim_denied").hide();
            $(".claim_denied").show();
        }
        if ($(this).attr("value") == "left_voice_message") {
            $(".followup-box").not(".left_voice_message").hide();
            $(".left_voice_message").show();
        }
        if ($(this).attr("value") == "others") {
            $(".followup-box").not(".others").hide();
            $(".others").show();
        }

        /*** Main PAymetnt Screen Radio buttons for Patients ****/
        if ($(this).attr("value") == "pat_payment") {
            $(".followup-box").not(".pat_payment").hide();
            $(".pat_payment").show();
        }
        if ($(this).attr("value") == "pat_refund") {
            $(".followup-box").not(".pat_refund").hide();
            $(".pat_refund").show();
        }
        if ($(this).attr("value") == "pat_adjustment") {
            $(".followup-box").not(".pat_adjustment").hide();
            $(".pat_adjustment").show();
        }
        if ($(this).attr("value") == "pat_creditbalance") {
            $(".followup-box").not(".pat_creditbalance").hide();
            $(".pat_creditbalance").show();
        }
    });

    /*** FAQ Accordian details start ****/
    $('.collapse').on('shown.bs.collapse', function () {
        $(this).parent().find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
    }).on('hidden.bs.collapse', function () {
        $(this).parent().find(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
    });
    /*** FAQ Accordian details ends ****/

    /*** Onclick Popover content ****/
    $("[data-toggle=popover]").popover({
        html: true,
        content: function () {
            return $('#popover-content').html();
        }
    });
});
/*** AR Management followup details ends ****/

/*** Password Common condition check start ***/
//var city_msg  = '{{ trans("common.validation.city_required") }}';
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

/*** Password Common condition check end ***/

/*** Amount separate with decimal start ***/
$(document).on('blur', '.js_amt_format', function () {
    value = $(this).val();
    if (value != '' && !isNaN(value)) {
        var num = parseFloat(value).toFixed(2);
        $(this).val(num);
    }
});

/*** Amount separate with decimal end ***/

/*** Customer Users fields for UserAccess start***/
$(document).on('ifToggled click change', '.js_useraccess', function () {
    if ($(this).is(':checked')) {
        current_useraccess = $(this).attr('data-id');
        $('.js_access').addClass("hide");
        $('#' + current_useraccess).removeClass("hide");
    }
});

$(document).on('change', "#js-selet-app", function () {
    var value = $(this).val();
    $('.js-app-data').hide();
    $("#" + value).show();
});

$(document).on('change', '.practice_useraccess_name', function () {
    var practice_name = $(this).val();
    if (practice_name != '') {
        $.ajax({
            url: api_site_url + '/admin/api/useraccess/' + practice_name,
            type: 'get',
            success: function (msg) {
                facility = msg.facility;
                provider = msg.provider;
                // console.log("provider" + provider);
                $('#selected_list').find('option:gt(0)').remove();
                $.each(facility, function (i, val) {
                    $('#selected_list').append("<option value='" + i + "'>" + val + "</option>");
                });
                $.each(provider, function (i, val) {
                    $('#selected_provider_list').append("<option value='" + i + "'>" + val + "</option>");
                });
                //$('.choose_facility').select2();
            }
        });
    }
});

/*** Customer Users fields for UserAccess end***/

/*** start to patient Notes popup ***/
$(window).load(function () {
    //patient_notes(null);      //patient  notes changed 
});

function patient_notes(url_id) {
    // Only continue in patient module.
    if (window.location.href.indexOf("patients") > -1 || url_id != null) {
        url = window.location.pathname.split("/patients/");
        if (url.length == 2 || url_id != null) {

            if (url_id == null)
                url = url[1].split("/");
            // Pop up is not coming If the below words are in url.
            var collect_ignoreword = ['create', 'edit', 'notes', 'delete', 'list', 'budgetplan'];
            // match words with url.
            var diff = $(collect_ignoreword).not(url).get().length;

            // No matches then continue to display the popup.
            if (url.length < 3 && diff == 6 || url_id != null) {
                if (url_id == null)
                    url_id = url[0];

                $.ajax({
                    url: api_site_url + '/patients/checkpatientnote/' + url_id,
                    type: 'GET',
                    success: function (msg) { 
                        if ($.trim(msg) != '') {
                            // js_alert_popup_title(msg, 'Alert Notes1');
                            $("#alert-notes-msg").html(msg);
                            $("#showmenu-bar").removeClass('hide');
                        }
                    }
                });
            }
        }
    }
}
/*** End to patient Notes popup ***/

/*** Tooltip for mouseover starts ***/
var tooltipTimeout;
var tooltip1Timeout;
$(document).on({
    mouseenter: function () {
        var links = $(this);
        var t = links.offset().top;
        var docheight = $(document).height();
        var screenTop = $(window).scrollTop();
        var id = $(this).data('id');
        var h = $('.js-tooltip_' + id).height();
        var currentlinkheight = links.height();
        var currenttop = t - screenTop;
        var bottom = $(window).height() - h - currenttop;
        var isEntirelyVisible = (bottom > 30);

        if ($('#js_page_name').val() == 'refunds') {
            var first = t - 510 - h;
            var second = t - 490 - h;
            var third = t - 460 - h;
            var fourth = t - 480 - h;
        } else if ($('#js_page_name').val() == 'patients') {
            var first = t - 400 - h;
            var second = t - 430 - h;
            var third = t - 450 - h;
            var fourth = t - 420 - h;
        } else if ($('#js_page_name').val() == 'charges' || $('#js_page_name').val() == 'patientcharges' || $('#js_page_name').val() == 'patientpayment') {
            var first = t - 350 - h;
            var second = t - 380 - h;
            var third = t - 400 - h;
            var fourth = t - 370 - h;
        } else if ($('#js_page_name').val() == 'claims') {
            var first = t - 250 - h;
            var second = t - 280 - h;
            var third = t - 300 - h;
            var fourth = t - 270 - h;
        } else {
            var first = -h + 40;
            var second = -h + 20;
            var third = -h - 10;
            var fourth = -h - 10;
        }

        if ($('.js-tooltip_' + id).height() >= 180 && $('.js-tooltip_' + id).height() <= 200) {
            var settop = first;
        } else if ($('.js-tooltip_' + id).height() == 160) {
            var settop = second;
        } else if ($('.js-tooltip_' + id).height() >= 140 && $('.js-tooltip_' + id).height() <= 159) {
            var settop = third;
        } else if ($('.js-tooltip_' + id).height() >= 100 && $('.js-tooltip_' + id).height() <= 139) {
            var settop = fourth;
        }

        tooltip1Timeout = setTimeout(function () {
            if (!isEntirelyVisible) {
                var tooltip1 = $("<div id='tooltip1' class='tooltip1' style='top:" + settop + "px;'>" + $('.js-tooltip_' + id).html() + "</div>");
            } else {
                var tooltip1 = $("<div id='tooltip1' class='tooltip1'>" + $('.js-tooltip_' + id).html() + "</div>");
            }
            tooltip1.appendTo($("#someelem" + id));
        }, 700);
    },
    mouseleave: function () {
        hideTooltip();
    }
}, '.someelem');

function hideTooltip() {
    clearTimeout(tooltip1Timeout);
    $("#tooltip1").fadeOut().remove();
}
/*** Tooltip for mouseover ends ***/

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

function iconclick(type) {
    $('input[name=' + type + ']').focus();
}

$(document).delegate('.npi-close', 'click', function () {
    $('#form-npi-modal').hide();
});
/*
 function getCookie(cname) {
 var name = cname + "=";
 var ca = document.cookie.split(';');
 for(var i = 0; i <ca.length; i++) {
 var c = ca[i];
 while (c.charAt(0)==' ') {
 c = c.substring(1);
 }
 if (c.indexOf(name) == 0) {
 return c.substring(name.length,c.length);
 }
 }
 return "";
 }
 $(document).ajaxStart(function() { 
 $("#js_wait_popup").modal("show");
 });
 $(document).ajaxStop(function() { 
 addModal("js_wait_popup");
 });*/

function callicheck() {
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
}

function problemlistCreate() {
    $('#js-bootstrap-validator').bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            'claim_search': {
                validators: {
                    notEmpty: {
                        message: 'Select claim number'
                    }
                }
            },
            'assign_user_id': {
                validators: {
                    notEmpty: {
                        message: 'Select assigned user'
                    }
                }
            },
            'fllowup_date': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Select followup Date'
                    },
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var fllowup_date = $('.followup_date').val();
                            var current_date = new Date(fllowup_date);
                            var d = new Date(get_practice_timzone);
                            if (fllowup_date != '' && (d.getTime() - 96000000) > current_date.getTime()) {
                                return {
                                    valid: false,
                                    message: "Followup date give future date"
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            'priority': {
                validators: {
                    notEmpty: {
                        message: 'Select priority'
                    }
                }
            },
            'description': {
                validators: {
                    notEmpty: {
                        message: 'Enter description '
                    }
                }
            },
            'status': {
                validators: {
                    notEmpty: {
                        message: 'Select status'
                    }
                }
            }
        }
    })
	.on('success.form.bv', function (e) {
		// Prevent form submission
		e.preventDefault();
		$('#js_create_problem_list_footer').removeClass('show').addClass('hide');
		$("#js_create_problem_list_loading").removeClass('hide').addClass('show');
		var myform = $('#js-bootstrap-validator');
		var disabled = myform.find(':input:disabled').removeAttr('disabled');
		var serialized = myform.serialize();
		var patient_id = $('.js_patient_id').val();
		//var claim_id =$('.claim_no').val();
		var claim_id = $(".claim_no option:selected").text();
		$('.js_show_problem').addClass('hide');
		$.ajax({
			type: 'POST',
			url: api_site_url + '/patients/' + patient_id + '/problem/createstore/' + claim_id,
			data: serialized,
			success: function (result) {
				$("#create_problem_list").modal("hide");
				$(".js_problem_list_loop").html('');
				$(".js_problem_list_loop").html(result);
				//$("#example1").dataTable({"aaSorting": []});
				js_sidebar_notification('success', 'Claims assigned to Work Bench successfully');
				setTimeout(function () {
					// Reload the workbench list.
					getData();
				}, 500);
				/* $("#add_prm_success-alert").removeClass('hide').addClass('show');
				$("#add_prm_success-alert").fadeTo(1000, 600).slideUp(600, function () {
					$("#add_prm_success-alert").alert('close');
				}); */
			}
		});
		return false;
	});
}

/*** Patient Problem List page Start ***/
//Create New Problem Start
$(document).on('click', '.js-new_problem_list', function () {
    $("#show_problem_list").html('');
    target = $(this).attr('data-url');
    $("#create_problem_list").load(target, function () {
        $('#create_problem_list').on('show.bs.modal', function (e) {
            $(function () {
                var eventDates = {};
                eventDates[new Date(get_default_timezone)] = new Date(get_default_timezone);

                $('#create_problem_list .followup_date').datepicker({
                    changeMonth: false,
                    changeYear: false,
                    minDate: new Date(get_default_timezone),
                    dateFormat: 'mm/dd/yy',
                    yearRange: '0+:2150',
                    beforeShowDay: function (d) {
                        setTimeout(function () {
                            $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');
                        }, 10);

                        var highlight = eventDates[d];
                        if (highlight) {
                            return [true, "ui-state-highlight", ''];
                        } else {

                            return [true, '', ''];
                        }
                    }
                });
            });
            problemlistCreate();
            $.AdminLTE.boxWidget.activate();
            $("select.select2").select2();
        });
        $("#create_problem_list").modal("show");
    });
});

//Create New Problem End
function problemlistShow() {
    $('#js-bootstrap-validators').bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            'claim_search': {
                validators: {
                    notEmpty: {
                        message: 'Select claim number'
                    }
                }
            },
            'assign_user_id': {
                validators: {
                    notEmpty: {
                        message: 'Select assigned user'
                    }
                }
            },
            'fllowup_date': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Select followup Date'
                    },
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var fllowup_date = $('.followup_date').val();
                            var current_date = new Date(fllowup_date);
                            var d = new Date(get_default_timezone);
                            if (fllowup_date != '' && (d.getTime() - 96000000) > current_date.getTime()) {
                                return {
                                    valid: false,
                                    message: "Followup date give future date"
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            'priority': {
                validators: {
                    notEmpty: {
                        message: 'Select priority'
                    }
                }
            },
            'description': {
                validators: {
                    notEmpty: {
                        message: 'Enter the description '
                    }
                }
            },
            'status': {
                validators: {
                    notEmpty: {
                        message: 'Select status'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        // Prevent form submission
        e.preventDefault();
        $("#js_edit_problem_list").removeClass('show').addClass('hide');
        $("#js_edit_problem_list_loading").removeClass('hide').addClass('show');
        var myform = $('#js-bootstrap-validators');
        var disabled = myform.find(':input:disabled').removeAttr('disabled');
        var serialized = myform.serialize();
        var patient_id = $('.js_patient_id').val();
        var claim_id = $('.js_claim_no').val();
        var claim = $('.js_claim').val();
        $.ajax({
            type: 'POST',
            url: api_site_url + '/patients/' + patient_id + '/problem/store/' + claim_id,
            data: serialized,
            success: function (result) {
                $("#js_edit_problem_list_loading").removeClass('show').addClass('hide');
                $("#js_edit_problem_list").removeClass('hide').addClass('show');
                //$("#edit_success_alert_part").removeClass('hide').addClass('show');
                js_sidebar_notification('success', 'Workbench added successfully');
                $("#edit_success_msg").removeAttr('style').fadeTo(1000, 600).slideUp(600);
                //$("#edit_success_alert_part").addClass('hide');
                $(".js_problem_scroll").html(result);
                var form_tag = $('#js-bootstrap-validators');
                form_tag[0].reset();
                form_tag.data("bootstrapValidator").resetForm();
                form_tag.find("select.select2").select2('val', '');
                setTimeout(function () {
                    // Reload the workbench list.
                    getData();
                }, 500);
            }
        });
        return false;
    });
}

//Show And add the Problem in the list Start
$(document).on('click', '.js-claimlink', function (event) {
    event.cancelBubble = true;
    event.stopPropagation();
    var url = $(this).attr('href');
    window.location = url;
    return true;
});

$(document).on('click', '.js_show_problem_list', function (e) {
    $("#create_problem_list").html('');
    $("#show_problem_list").html('');
    $("#js_wait_popup").modal("show");
    target = $(this).attr('data-url');
    $("#show_problem_list").load(target, function () {
        $('#show_problem_list').on('show.bs.modal', function (e) {
              var eventDates = {};
            eventDates[new Date(get_default_timezone)] = new Date(get_default_timezone);
            $(function () {
                $('#show_problem_list .followup_date').datepicker({
                    changeMonth: false,
                    changeYear: false,
                    minDate: new Date(get_default_timezone),
                    dateFormat: 'mm/dd/yy',
                    yearRange: '0+:2150',
                    beforeShowDay: function(d) {
                        setTimeout(function() {
                             $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
                         }, 10);

                        var highlight = eventDates[d];
                        if( highlight ) {
                             return [true, "ui-state-highlight", ''];
                        } else {
                           
                             return [true, '', ''];
                        }
                    }
                });
            });
            $("#success-alert").addClass("hide");
            $.AdminLTE.boxWidget.activate();

            //Scroll button
            $('.js_problem_scroll').slimScroll({
                height: '150px'
            });
            problemlistShow();
            $("select.select2.form-control").select2();
        });
        $("#show_problem_list").modal("show");
        $("#js_wait_popup").modal("hide");
    });
});

//Show And add the Problem in the list End
/*** Patient Problem List page End ***/

$(document).on('click ifToggled change', 'tr.js_show_document_assigned_list td:not(".js-prevent-show")', function (e) {
    var target = $(e.target);
    // console.log(target);
    if (target.is(".js-prevent-action")) {
        $("div.js_model_show_document_assigned_list").attr('id', "");
    } else {
        $("div.js_model_show_document_assigned_list").attr('id', "show_document_assigned_list");
        $("#create_problem_list").html('');
        $("#show_document_assigned_list").html('');
        displayLoadingImage();
        target = $(this).parent('tr').attr('data-url');
        document_id = $(this).parent('tr').attr('data-document-id');

        $("#show_document_assigned_list").load(target, function () {
			hideLoadingImage();
            //$('#show_document_assigned_list').on('show.bs.modal', function (e) {
            var eventDates = {};
            eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );     
            $(function () {
                $('#followup_date').datepicker({
                    changeMonth: false,
                    changeYear: false,                 
                    dateFormat: 'mm/dd/yy',
                    yearRange: '0+:2150',
                    minDate:new Date(get_default_timezone),
                    beforeShowDay: function(d) {
                        setTimeout(function() {
                        $(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
                         }, 10);

                        var highlight = eventDates[d];
                        if( highlight ) {
                             return [true, "ui-state-highlight", ''];
                        } else {
                             return [true, '', ''];
                        }
                    }
                });
            });
            $("#success-alert").addClass("hide");
            $.AdminLTE.boxWidget.activate();

            //Scroll button
            $('.js_problem_scroll').slimScroll({
                height: '150px'
            });
            documentlistShow(document_id);
            //$("select.select2.form-control").select2();
            //});
            $("#show_document_assigned_list").modal("show");
            //$("#js_wait_popup").modal("hide");
        });
    }

});
//open datepicker if click date icon in followup date
$(document).on('click', '#followup_date_icon', function () {
    $('#followup_date').focus();
});

/*$(document).on('click','tr.js_show_document_assigned_list td.js-prevent-show','',function(e){
 setTimeout(function(){  $('#show_document_assigned_list').modal('toggle'); }, 80);     
 });*/

$(document).on('click ifToggled', '.js-prevent-action', function (e) {
    //$("div.js_model_show_document_assigned_list").attr('id', ""); 
});

function documentlistShow(document_id) {
    $('#js-bootstrap-validators').bootstrapValidator({
        message: 'This value is not valid',
        excluded: ':disabled',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            'claim_search': {
                validators: {
                    notEmpty: {
                        message: 'Select claim number'
                    }
                }
            },
            'assign_user_id': {
                validators: {
                    notEmpty: {
                        message: 'Select assigned user'
                    }
                }
            },
            'fllowup_date': {
                trigger: 'change keyup',
                validators: {
                    notEmpty: {
                        message: 'Select followup Date'
                    },
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var fllowup_date = $('.followup_date').val();
                            var current_date = new Date(fllowup_date);
                            var d = new Date(get_default_timezone);
                            if (fllowup_date != '' && (d.getTime() - 96000000) > current_date.getTime()) {
                                return {
                                    valid: false,
                                    message: "Followup date give future date"
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            'priority': {
                validators: {
                    notEmpty: {
                        message: 'Select priority'
                    }
                }
            },
            'notes': {
                validators: {
                    notEmpty: {
                        message: 'Enter the notes '
                    }
                }
            },
            'status': {
                validators: {
                    notEmpty: {
                        message: 'Select status'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        // Prevent form submission
        e.preventDefault();
        $("#js_edit_problem_list").removeClass('show').addClass('hide');
        $("#js_edit_problem_list_loading").removeClass('hide').addClass('show');
        var myform = $('#js-bootstrap-validators');
        var disabled = myform.find(':input:disabled').removeAttr('disabled');
        var serialized = myform.serialize();
        $.ajax({
            type: 'POST',
            url: api_site_url + '/patients/document-assigned/' + document_id + '/store',
            data: serialized,
            success: function (result) {
                updateassignedhistory(document_id, result);
                /* $("#js_edit_problem_list_loading").removeClass('show').addClass('hide');
                 $("#js_edit_problem_list").removeClass('hide').addClass('show');
                 $("#edit_success_alert_part").removeClass('hide').addClass('show');
                 $("#edit_success_msg").removeAttr('style').fadeTo(1000, 600).slideUp(600);
                 //$("#edit_success_alert_part").addClass('hide');
                 $(".js_problem_scroll").html(result);
                 var form_tag = $('#js-bootstrap-validators');
                 form_tag[0].reset();   
                 form_tag.data("bootstrapValidator").resetForm();
                 form_tag.find("select.select2").select2('val',''); */
                $('.js_documentlist_update').click();
                $("#show_assigned_msg").append(' <p class="alert alert-error" id="success-alert">Successfully Assigned User</p>');
                js_sidebar_notification('success', 'Successfully Updated');
            }
        });
        return false;
    });
}

function updateassignedhistory(doc_id, result) {
    sel = $("tr[data-document-show='js_update_row_" + doc_id + "']");
    if (result != '') {
        $.each(JSON.parse(result), function (key, data) {
            sel.find("." + key).html(data);
        })
    }
}
/*** Search option enable start ***/

checkTableListForExport();
function checkTableListForExport() {
    var check_empty_table = $("table tr td").hasClass("dataTables_empty");
    var check_table = $("table:visible").length;
    if (check_empty_table || check_table == 0) {
        $(".js_claim_export").addClass("hide");
    } else {
        //Hide Processing & Download icons

        // Hide PDF export icon for otherthan reports page. For now listing page PDF icon disabled. 
            // $('.js_claim_export').removeClass("hide");
        if($("input[name='report_name']").val() == "Charge Analysis Detailed" || $("input[name='report_name']").val() == "Patient and Insurance Payment" || $("input[name='report_name']").val() == "Unbilled Claims Analysis" || $("input[name='report_name']").val() == "End of the Day Totals" || $("input[name='report_name']").val() == "Work RVU Report" || $("input[name='report_name']").val() == "Charges Payments Summary" || $("input[name='report_name']").val() == "Charge Category Report" || $("input[name='report_name']").val() == "Refund Analysis - Detailed" || $("input[name='report_name']").val() == "Procedure Collection Report Insurance Only" || $("input[name='report_name']").val() == "Insurance Over Payment" || $("input[name='report_name']").val() == "Appointment Analysis Report" || $("input[name='report_name']").val() == "Demographic Sheet" || $("input[name='report_name']").val() == "Address Listing" || $("input[name='report_name']").val() == "Wallet History - Detailed" || $("input[name='report_name']").val() == "Wallet Balance" || $("input[name='report_name']").val() == "Aging Summary" || $("input[name='report_name']").val() == "Denial Trend Analysis"  || $("input[name='report_name']").val() == "Cpt Hcpcs Summary"  || $("input[name='report_name']").val() == "Year End Financials" || $("input[name='report_name']").val() == "Payment Analysis Detailed Report" || $("input[name='report_name']").val() == "Adjustment Analysis Detailed" || $("input[name='report_name']").val() == "ICD Worksheet" || $("input[name='report_name']").val() == "Statement Status Detailed" || $("input[name='report_name']").val() == "AR Work Bench Report" || $("input[name='report_name']").val() == "Payer Summary" || $("input[name='report_name']").val() == "Provider Summary" || $("input[name='report_name']").val() == "getPaymentExport" || $("input[name='report_name']").val() == 'Facility Summary'){
            // $(".js_search_export_csv").parent('.js_claim_export').removeClass("hide");
            // $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");

            if($('.xlsx_report_export_spinner').is(":visible")) {
                $(".js_search_export_csv").addClass("hide");
                $('.xlsx_report_export_spinner').removeClass('hide');
            }
            else if($('.xlsx_report_export_download').is(":visible")){
                $(".js_search_export_csv").removeClass("hide");
                $('.xlsx_report_export_download').addClass('hide');
            }
            else{
                $('.js_claim_export').removeClass("hide");
                $(".js_search_export_csv").removeClass("hide");
            }

            if($('.pdf_report_export_spinner').is(":visible")) {
                $(".js_search_export_pdf").addClass("hide");
                $('.pdf_report_export_spinner').removeClass('hide');
            }
            else if($('.pdf_report_export_download').is(":visible")){
                $(".js_search_export_pdf").removeClass("hide");
                $('.pdf_report_export_download').addClass('hide');
            }
            else{
                $('.js_claim_export').removeClass("hide");
                $(".js_search_export_pdf").removeClass("hide");
            }

            // $('.xlsx_report_export_spinner').addClass('hide');    
            // $('.pdf_report_export_spinner').addClass('hide');    

            // $('.xlsx_report_export_download').addClass('hide');    
            // $('.pdf_report_export_download').addClass('hide');    
        } else if ($("input[name='file_name").val() == 'Electronic_Claims_list') {} {
            $(".js_search_export_csv").parent('.js_claim_export').removeClass("hide");
            // $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");
        }        
    }
}

$(document).on('keyup', function (e) {
    if ($('.js_common_modal_popup').is(':visible')) {
        var key = e.which;
        var form_id = $(".js_common_modal_popup.in").attr("id");
        if (key == 13) {
            $('.js_common_modal_popup_save').trigger('click');
            //addModal(form_id);
        } else if (key == 27) {
            $('.js_common_modal_popup_cancel').trigger('click');
            //addModal(form_id);
        }
    }
});

$(document).on('click', ".js_usps_add_modal_close_btn", function () {
    addModal('form-address-modal');
});

setTimeout(function () {
    clearpopItValue();
}, 1010); //Getting reinitialized confirm popup value

/*** Common Export with searched critiria start ***/
/*** Commented since not going to use
$(document).on('click', '.js_search_export', function (e) {
    if ($(".js_search_basis_export").length > 0) {
        //e.preventDefault();    
        var get_form_id = $(".js_filter_search_submit").parents('form').attr("id");
        if (get_form_id != '' && typeof get_form_id != "undefined") {
            var url = $("#" + get_form_id).attr("action");
            var get_param = $("#" + get_form_id).serialize();
            $(this).attr('href', url + '/export/' + $(this).attr('data-option') + '?' + get_param);
        }
        removeHash();
    }
});
*/
/*** Common Export with searched critiria end ***/

/*** Common Processing Image functions start ***/
function processingImageShow(param, event) {
    var get_processing_html = $("#js_wait_alert_confirm").html();
    if (event == "show") {
        $(param).prepend(get_processing_html);
        $(param).find(".js_processing_image").removeClass("hide");
    } else if (event == "hide") {
        $(param).find(".js_wait_alert_confirm").remove();
    }
}
/*** Common Processing Image functions end ***/

/*** Start to ajax fatal error message store in log file ***/
/* $(document).ajaxError(
 function (event, jqXHR, ajaxSettings, thrownError) {
 if(chk_env_site != 'local')
 console.clear();
 
 if(thrownError != '')
 js_alert_popup(thrownError);
 }); */

/*** End to ajax fatal error message store in log file ***/
// Revision 1 - Kannan - 29 Aug 2019 - Provider Login Disabled Module Icon Message
$(document).on('click', '.disabled-module', function (e) {
   js_alert_popup("Access to this module has been restricted");
});

/*** Start to insurance remove alert message ***/
$(document).on('click', '.js-checkins-delete', function (e) {
    js_alert_popup(error_insurance_del_msg);
});
/*** End to insurance remove alert message ***/

/*** Start tab key press function ***/
var get_current_id = new Array();
var form_key_count;
$(function () {
    return $(document).on('show.bs.modal', '.modal', function () {
        form_key_count = mg = 0;
        get_current_id.push($(this).attr("id"));
        var zIndex = 999 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            $('.fixed .main-header').css('z-index', 999);    // This is changed due to dropdoen issue when open model window        
        }, 0);
    });
});

$(document).on('hidden.bs.modal', '.modal', function () {
    form_key_count = mg = 0;
    get_current_id.pop();
    if (!$('.modal:visible').length)
        $('.fixed .main-header').css('z-index', 99999); // This is changed due to dropdoen issue when open model window
});

$(document).on("focus", 'input,button,radio,checkbox,select,textarea', function (e) {
    if ($(e.target).attr("name") != "gender")
        gender_err = '';
    if (get_current_id[get_current_id.length - 1] == '' || get_current_id[get_current_id.length - 1] == null) {
        get_current_id.push($(".modal.in").attr("id"));
        form_key_count = 0;
    }
    var current_id = get_current_id[get_current_id.length - 1];
    if (form_key_count == 0 && $("#" + current_id).find("button").length == 1)
        form_key_count = 0;
    else if (form_key_count == 0 && !$("#" + current_id).find(".btn:not('.hide .btn'):last").is(":focus"))
        form_key_count = 1;
    else if (form_key_count != 0 && $("#" + current_id).find(".btn:not('.hide .btn'):last").is(":focus"))
        form_key_count = 0;
    //function for when tab key focusing on gender radio button have to check the male radio button default   
});
//for gender focus on demiography site 
$(".gender").focus(function () {
    var value = 'Male';
    var validresult = $('small[data-bv-for="gender"]').attr("data-bv-result");
    if (validresult == "INVALID" && gender_err != 1) {
        if ($(this).prop('checked') != true) {
            //     $("input[name=gender][value=" + value + "]").prop('checked', true); 
            //    $('.patients-info-form').bootstrapValidator('revalidateField', 'gender');
        }
    } else if (validresult == "INVALID" && gender_err == 1) {

    } else {
        var values = $(this).val();
        //     $("input[name=gender][value=" + values + "]").prop('checked', true); 
    }
});
$(document).on('click', '.js_check_open .btn', function (event) {
    var current_val = $(".js_check_open").hasClass("open");
    if (current_val)
        $(".js_check_open").removeClass("open");
    else
        $(".js_check_open").addClass("open");
});
var active = "no";
$(document).on('keydown', function (e) {
    if ($("body").hasClass("modal-open")) {
        var type = $("#create_charge.modal.in").find('select,input,textarea,a').filter(':visible:last').attr('type');
        var keyCode = e.keyCode || e.which;
        var current_id = get_current_id[get_current_id.length - 1];
        if (keyCode == 9 && form_key_count == 0) {
            active = document.activeElement.type;
            if (active == "submit")
                $("#create_charge.modal.in").find('select,input,textarea,a').filter(':visible:first').focus();
        }
    }
});
/*** End tab key press function ***/

/* Start charge close modal*/
$(document).on('hide.bs.modal', '#create_charge', function () {
    i = 0;
});
/* End charge close modal*/

/*** Template slider toggle fuction start ***/
$(document).on('click', ".js_accordion_header", function (event) {
    $(this).parent(".accordion-group").find(".js_accordion_content").slideToggle("slow");
});

/* Toggle control when edit icon click   */
$(document).on('click', ".js_accordion_header .js-category-edit", function (event) {
    event.stopPropagation();
});

/*** Template slider toggle fuction end ***/

/*** Prevent one more AJAX hit same time fuction start ***/
var ajax_url = [];
$.ajaxSetup({
    beforeSend: function (jqXHR) {
        var index = $.inArray(this.url, ajax_url);
        ajax_url.push(this.url);
        if (index !== -1) {
            jqXHR.abort();
        }
    },
    complete: function (jqXHR) {
        ajax_url = [];
    }
});

/*** Prevent one more AJAX hit same time fuction end ***/

/***  patients-> new Appointment POPUP URL Passing Start ***/

$(document).on('click', '.js_scheduler_arg', function () {
    sessionStorage.setItem("New Appointment", "reload");
});

/***  POPUP URL Passing End***/

// Given space for invalid avathar image name.
$('input[type=file]').change(function () {
    $('.fileupload-preview').css("line-height", "14px").css("word-wrap", "break-word");
});

// Search function related code starts here
/// Starts Date Validation (From and To Date) ///
function searchStartDate(start_date, end_date) {
    if (start_date == '') {
        return start_date_req_lang_err_msg;
    }
    var date_format = new Date(end_date);
    if (end_date != '' && date_format != "Invalid Date") {
        return (start_date == '') ? start_date_req_lang_err_msg : true;
    }
    return true;
}

function searchEndDate(start_date, end_date) {
    if (end_date == '') {
        return end_date_req_lang_err_msg;
    }
    var eff_format = new Date(start_date);
    var ter_format = new Date(end_date);
    if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
        var getdate = searchDaydiff(parseDate(start_date), parseDate(end_date));
        return (getdate >= 0) ? true : end_date_val_lang_err_msg;
    } else if (start_date != '' && eff_format != "Invalid Date") {
        return (end_date == '') ? end_date_req_lang_err_msg : true;
    }
    return true;
}

function searchDaydiff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}

$(document).on('click', '.js_search_reset', function () {
    $('input:text').val("");
    $(".select2").select2("val", "");
    $.AdminLTE.boxWidget.activate();
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'billed_option');
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'billed');
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'balance_option');
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'balance');
    $('#js_common_search_form').bootstrapValidator('disableSubmitButtons', false);
    searchform();
    return false;
});

$(document).on('change', '.search_start_date, .search_end_date', function () {
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
});

if ($("#js_common_search_form").length > 0) {
    $('#js_common_search_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: ':disabled, :hidden, :not(:visible)',
        fields: {
            search_start_date: {
                selector: '.search_start_date',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_valid_lang_err_msg
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var get_field = $field.parents(".js_date_validation").attr("id");
                            var end_date = $("#" + get_field + " .search_end_date").val();
                            var response = true;
                            if (end_date != '' && value == '' || end_date != '' && value != '') {
                                var response = searchStartDate(value, end_date);
                            }
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
            search_end_date: {
                message: '',
                selector: '.search_end_date',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_valid_lang_err_msg
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var get_field = $field.parents(".js_date_validation").attr("id");
                            var start_date = $("#" + get_field + " .search_start_date").val();
                            var end_date = value;
                            var response = true;
                            if (start_date != '' && value == '' || start_date != '' && value != '') {
                                var response = searchEndDate(start_date, end_date);
                            }
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
            /* js_main_select:{                        
             message:'',
             selector: '.js_main_select',
             validators:{
             callback: {
             message: "Select option",
             callback: function(value, validator, $field) {
             var get_field = $field.parents(".js-select-div").attr("id");
             var sub_val = $("#"+get_field+" .js_sub_select").val();                            
             if (value !== '') {                                   
             $('form#js_common_search_form').bootstrapValidator('enableFieldValidators', 'js_sub_select', true)
             $('form#js_common_search_form').bootstrapValidator('revalidateField', 'js_sub_select'); 
             return true;
             }else{
             if(sub_val !=''){
             return false;
             }
             $("#"+get_field+" .js_sub_select").val(""); 
             $('form#js_common_search_form').bootstrapValidator('enableFieldValidators', 'js_sub_select', false)
             $('form#js_common_search_form').bootstrapValidator('revalidateField', 'js_sub_select');
             return true;
             }             
             }
             }
             }
             }, 
             js_sub_select:{
             enabled: false,
             selector: '.js_sub_select',
             message:"Enter amount",
             validators:{                                                               
             callback: {
             message: "Enter amount",
             callback: function(value, validator, $field) {
             var get_field = $field.parents(".js-select-div").attr("id");                        
             var mode = $("#"+get_field+" select").val(); 
             console.log(" jssubselect get filed and mode"+mode);
             console.log("value"+value);
             if(value == '' && mode != '')      {
             console.log("comes false");
             return {
             valid: false,
             message : "Enter amount"
             };   
             }                      
             return true;                                     
             }
             }                               
             }
             }, */
            billed: {
                trigger: 'keyup change',
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var billed_option = $("#billed_option").val();
                            if (billed_option != '' && value == '') {
                                return {
                                    valid: false,
                                    message: 'Enter billed amount'
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            billed_option: {
                trigger: 'keyup change',
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var billed = $("#billed").val();
                            if (billed != '' && value == '') {
                                return {
                                    valid: false,
                                    message: 'Select billed amount search by'
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            balance: {
                trigger: 'keyup change',
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var balance_option = $("#balance_option").val();
                            if (balance_option != '' && value == '') {
                                return {
                                    valid: false,
                                    message: 'Enter balance amount'
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            balance_option: {
                trigger: 'keyup change',
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var balance = $("#balance").val();
                            if (balance != '' && value == '') {
                                return {
                                    valid: false,
                                    message: 'Select balance amount search by'
                                };
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            payby_start_date: {
                message: '',
                selector: '.payby_start_date',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_valid_lang_err_msg
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var end_date = $(".payby_end_date").val();
                            var response = searchStartDate(value, end_date);
                            if (end_date != '' && value == '') {
                                return {
                                    valid: false,
                                    message: response
                                };
                            }
                            else if (response != true && (end_date != '' && value != '')) {
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
            payby_end_date: {
                message: '',
                selector: '.payby_end_date',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_valid_lang_err_msg
                    },
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {
                            var start_date = $(".payby_start_date").val();
                            var response = searchEndDate(start_date, value);
                            if (start_date != '' && value == '') {
                                return {
                                    valid: false,
                                    message: response
                                };
                            } else if (response != true && (start_date != '' && value != '')) {
                                return {
                                    valid: false,
                                    message: response
                                };
                            }
                            return true;
                        }
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        searchform();
        $('#js_common_search_form').bootstrapValidator('disableSubmitButtons', false);
    });
}

$(document).on('keyup change', '#billed_option, #billed', function () {
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'billed_option');
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'billed');
});

$(document).on('keyup change', '#balance_option, #balance', function () {
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'balance_option');
    $('#js_common_search_form').bootstrapValidator('revalidateField', 'balance');
});
/*
 $(document).on('keyup','#billed',function() {
 fv = $('#js_common_search_form').data('bootstrapValidator');
 if($(this).val()!=''){
 fv.enableFieldValidators('billed_option', true).revalidateField('billed_option');
 }
 else{
 fv.enableFieldValidators('billed_option', false).revalidateField('billed_option');
 }
 });*/
/*
 $(document).delegate(".js_sub_select", 'change', function(){           
 $('form#js_common_search_form').bootstrapValidator('revalidateField', 'js_main_select');
 $('form#js_common_search_form').bootstrapValidator('revalidateField', 'js_sub_select'); 
 }); */

$(document).on('change', '.payby_start_date,.payby_end_date', function () {
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.payby_start_date'));
    $('#js_common_search_form').bootstrapValidator('revalidateField', $('.payby_end_date'));
});

$(document).on('change', '.js_main_select', function () {
    setTimeout(function () {
        //$('form#js_common_search_form').bootstrapValidator('revalidateField', 'balance_option');  
    }, 500);
});

$(document).on('change keyup', '.balance_option', function () {
    setTimeout(function () {
        //$('form#js_common_search_form').bootstrapValidator('revalidateField', 'js_main_select');  
    }, 500);
});

function searchform() {
    var serialized_form_data = $('#js_common_search_form').serialize();
    var url = $('#js_common_search_form').attr('action');
    var datadivid = $('#js_common_search_form').attr('data_divid');
    //processingImageShow("#js_table_search_listing","show");
    $("#js_wait_popup").modal("show");
    $.ajax({
        url: url,
        type: 'POST',
        data: serialized_form_data,
        success: function (result_values) {
            //processingImageShow("#js_table_search_listing","hide");             
            $('#js_table_search_listing').html(result_values);
            $('#' + datadivid).modal("hide");
            $("#js_wait_popup").modal("hide");
            var get_table_id = $("table").attr("id");
            if ($(".js_no_change").length > 0)
                $('#' + get_table_id).DataTable(); //Used for modifier, Codes
            else
                $('#' + get_table_id).DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "responsive": true,
                    "order": [1, 'asc'],
                    "columnDefs": [{ "orderable": false, "targets": 0 }]
                });
            $.AdminLTE.boxWidget.activate();
        }
    });
}

$(document).on('click', '.js_search_export', function (e) {
    var data_arr = '';
    var name_data_arr = '';
    if ($('input[name="aging_summary"]').val() == 'aging-summary') {
        $('select.auto-generate').each(function () {
            if ($(this).select2('val') != '')
                data_arr += $(this).attr('name') + '=' + $(this).select2('val') + '&';
        });
    } else {
        $('select.auto-generate:visible').each(function () {
            if ($(this).select2('val') != '')
                data_arr += $(this).attr('name') + '=' + $(this).select2('val') + '&';
        });
    }
	
    $('input.auto-generate:visible').each(function () {
        if ($(this).val() != '')
            data_arr += $(this).attr('name') + '=' + $(this).val() + '&';
    });

    /* Showing generate report filter popup value are getting here */

    $('select.auto-generate:visible').each(function () {
        if ($(this).select2('val') != '') {
            var label_name = $(this).attr('data-label-name');
            var label_value = '';
            $(this).children("option:selected").each(function () {
                label_value += $(this).text() + '~';
            });
            if (label_value != '')
                name_data_arr += label_name + "=" + label_value + '&';
        }
    });

    $('input.auto-generate:visible').each(function () {
        if ($(this).val() != '')
            name_data_arr += $(this).attr('data-label-name') + '=' + $(this).val() + '&';
    });

    /* Showing generate report filter popup value are getting here */

    if ($('input[name="practiceoption"]').length > 0)
        data_arr += $('input[name="practiceoption"]').attr('name') + '=' + $('input[name="practiceoption"]').val() + '&';
    final_data = data_arr + "_token=" + $('input[name=_token]').val() + "&export=yes";
    var url = $(this).attr('data-url');
    if (url.length) {
        if ($(this).attr('href') != '#')
            $(this).attr('href', url + '/' + $(this).attr('data-option') + '?' + final_data);
    }

    report_controller_name = $('input[name="report_controller_name"]').val();
    //var data_option = $(this).attr('data-option');
    if (report_controller_name != "" && typeof report_controller_name != "undefined") {
        var url = api_site_url + "/generateReportExport";
        $.ajax({
            url: url,
            type: 'post',
            data: { '_token': $('input[name="token"]').val(), 'report_url': url + '/' + $(this).attr('data-option') + '?' + final_data, 'report_controller_name': $('input[name="report_controller_name"]').val(), 'report_controller_func': $('input[name="report_controller_func"]').val(), 'report_name': $('input[name="report_name"]').val(), 'parameter': name_data_arr },
            success: function (data) {
                /*if ($('input[name="report_name"]').val() == 'Charge Analysis Detailed') {
                    if(data_option==='xlsx'){
                        js_alert_popup('Converting Text to Column and Downloading processing');
                    }else{
                        js_alert_popup('Please check the generated reports section for downloads');
                    }
                    $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
                    $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/reports/generated_reports" class="btn btn-medcubics-small">OK</a>');
                } else {*/
                js_alert_popup('Please check the generated reports section for downloads');
                $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').html('');
                $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').append('<a href="' + api_site_url + '/reports/generated_reports" class="btn btn-medcubics-small">OK</a>');
                //}
            }
        })
        return false;
    }
    //removeHash();    
});
// Search function related code ends here

$(document).on('change', '.js_change_date_option', function (e) {
    var current_val = $(this).val();
    if (current_val == "enter_date" || current_val == "" || typeof current_val == "undefined") {
        var str_date = '';
        var end_date = '';
    } else {
        var str_date = getStartDate(current_val);
        var end_date = getEndDate(current_val);
    }
    if ($(".search_start_date").length > 0) {
        $(".search_start_date").val(str_date);
        $(".search_end_date").val(end_date);
        $(".search_start_date,.search_end_date").trigger("keyup");
        if (current_val != "enter_date" && current_val != "" && typeof current_val != "undefined") {
            $(".search_start_date,.search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
        } else {
            $(".search_start_date,.search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
        }
        $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
        $('#js_common_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
    }
});

function getStartDate(date_option) {
    var d = new Date(get_default_timezone);// get practice timezone
    switch (date_option) {
        case "daily":
            var strDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "current_month":
            var date = new Date(d.getFullYear(), (d.getMonth()), 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "previous_month":
            var date = new Date(d.getFullYear(), (d.getMonth() - 1), 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "current_year":
            var date = new Date(d.getFullYear(), 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "previous_year":
            var date = new Date(d.getFullYear() - 1, 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        default:
            var strDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
    }
    return MakeDate(strDate);
}

function getEndDate(date_option) { 
    var d = new Date(get_default_timezone);// get practice timezone
    switch (date_option) {
        case "daily":
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
            break;

        case "current_month":
            var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "previous_month":
            var date = new Date(d.getFullYear(), (d.getMonth()), 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        case "current_year":
            var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;

        case "previous_year":
            var date = new Date(d.getFullYear(), 0, 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;

        default:
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
    }
    return MakeDate(endDate);
}

function MakeDate(date_value) {
    var date = date_value.split("/");
    date[0] = ((date[0]) < 10) ? "0" + date[0] : date[0];
    date[1] = ((date[1]) < 10) ? "0" + date[1] : date[1];
    var return_date = date.join("/");
    return return_date;
}

$(document).on('focus', '.timepicker1', function () {
    $(".timepicker1").timepicker();
});

//Enter key press to submit the form START 
$(document).ready(function () {
    $(document).on('keypress', function (e) {
        taget_element = e.target;
        var target_e_class = $(taget_element);
        if (e.keyCode == 13 && !$('.js-avoid-savepopup:visible').length && !$('.js-denied-popup-div:visible').length && !$('.js-is_self_pay:visible').length && f != 0 && !(target_e_class.hasClass('js_modal_confirm1')) && !(target_e_class.hasClass('close_popup'))) {
            var form_id = $(".medcubicsform").attr('id');
            if (f != 0) {
                e.preventDefault();
                // var formname = $(".medcubicsform").attr('name');             
                var confirm_msg = ($('#' + form_id + " .js_set_confirm_msg").length > 0) ? $('#' + form_id + " .js_set_confirm_msg").val() : "Would you like to save the changes made?";
                // confirm_msg = "Please enter required fields.";
                //console.log($("#"+form_id).attr("class"));console.log("#"+form_id+"#");
                //for enter the form popup text message change in patient demographic and main charge created
                if ($("#" + form_id).hasClass('patients-info-form') || form_id == 'js-batch-submit') {
                    confirm_msg = "Please enter the required fields";
                    $("#session_model").find(".js_session_confirm:first").text("Continue");
                    $("#session_model").find(".js_session_confirm:last").text("Ignore");
                }
                //for enter the form popup button text message change in patient charge created
                if ($("#" + form_id).hasClass('js_billingform')) {
                    $("#session_model").find(".js_session_confirm:first").text("Yes");
                    $("#session_model").find(".js_session_confirm:last").text("No");
                }
                $("#session_model .med-green").html(confirm_msg);
                $("#session_model")
                    .modal({ show: 'false', keyboard: false })
                    .one('click', '.js_session_confirm', function (e) {
                        var conformation = $(this).attr('id');
                        if (conformation == "true") {
                            if ($(".medcubicsform").hasClass("contact-info-form") || $(".medcubicsform").hasClass("insurance-info-form") || $(".medcubicsform").hasClass("authorization-info-form")) {
                                if (!$('.js-error-class').length)
                                    $(".medcubicsform").submit();
                            } else {

                                $("#js-bootstrap-validator").data('bootstrapValidator').updateStatus('cpt', 'NOT_VALIDATED').validateField('cpt');
                                var bootstrapValidator = $("#js_bootstrap_validator,#js-bootstrap-validator,#js-bootstrap-validator1, #documentaddmodalform, #js-insurance-form, #js-payment").data('bootstrapValidator');
                                bootstrapValidator.validate();
                                if (bootstrapValidator.isValid()) {
                                    taget = $(taget_element).parents("form").find("input[type='submit']");
                                    if (typeof taget != "undefined") {
                                        taget.trigger("click");
                                    }
                                    else {
                                        var formname = $(".medcubicsform").attr('name');
                                        if (!$('.js-error-class').length)
                                            document.forms[formname].submit();
                                    }

                                }
                            }
                        } else {
                            return false;
                        }
                    });
            } else {

            }
        } else {

        }
    });
});

//END for form submit

// Start model form tab-index should work inside model only
$(window).on('show.bs.modal', function () {
    setTimeout(function () {
        var visible_modal = $('.modal.in').attr('id');
        $('#' + visible_modal + ' :input:first').focus();
        $('#' + visible_modal + ' :input:last').on('keydown', function (e) {
            if ($("this:focus") && (e.which == 9)) {
                e.preventDefault();
                $('#' + visible_modal + ' :input:first').focus();
            }
        });
    }, 100);
});
// End model form tab-index should work inside model only

$(document).on('click', '.js_era_download', function () {
    var url = api_site_url + "/payments/manual-download-e-remittance";
    $.ajax({
        url: url,
        type: 'get',
        success: function (error_code) {
            if (error_code != '' && error_code != undefined) {
                js_sidebar_notification('success', error_code);
                //$("#patientnote_model .med-green").html(error_code);
                //$("#patientnote_model").modal({show: 'open', keyboard: false});
                if (error_code == 'ERA 835 downloaded') {
                    setTimeout(function () { location.reload(); }, 500);
                }
            }
        }
    })
});

/*******************************
 *******************************
 Twilio work Start || Author :Manikandan CD-19.
 *******************************
 *******************************/
$(document).on("click", ".js-mesgclass", function () {
    $('.js_show_details').hide();
    var data_id = $(this).attr("data-id");
    $("div#" + data_id).show();
});

// email send for appointments list 
$(document).on("click", ".js-emailmsg-clas", function () {
    var userId = $(this).attr("data-user_id");
    var url = api_site_url + "/appt-email-send/" + userId;
    $.get(url, function (data) {
        if (data == 'success') {
            js_sidebar_notification('success', 'Mail Sent Successfully');
        } else {
            js_sidebar_notification('error', 'Invalid Mail');
        }
    });
});

$(document).on("click", ".js-callmsg-clas", function () {
    $("#js_wait_popup").modal("show");
    var path = $(location).attr('href');
    var split_data = path.split("/");
    var userId;
    /* if (split_data[4] = "patients") {
         userId = split_data[5];
     } */
    var userId = $(this).attr("data-user_id");
    var type = $(this).attr("data-user_type");
    var phone_number = btoa($(this).attr("data-phone"));
    var phone = $(this).attr("data-phone");
    var url = api_site_url + "/callhistory/" + phone_number + "/" + userId + "/" + type;
    $.get(url, function (data) {
        if (data == 'invalid') {
            js_sidebar_notification('error', 'Invalid Phone Number');
            $("#js_wait_popup").modal("hide");
        } else {
            getAndSetTheTwilioToken(phone);
            $("#js_wait_popup").modal("hide");
            $("#js-phone-popup .modal-body").html(data);
            $("#js-phone-popup .userPhone").text("" + phone + "");
            $('#phoneNumVal').val(phone);
            $("#js-phone-popup").modal("show");
        }
    });
});

$(document).on("click", ".js-mesgclass", function () {
    $('.js_show_details').hide();
    $("#msgDiv").addClass("hide");
    var data_id = $(this).attr("data-id");
    $("div#" + data_id).removeClass("hide");
    $("div#" + data_id).show();
});

function getAndSetTheTwilioToken(phone) {
    $("#msgDiv").addClass("hide");
    var phone_number = btoa(phone.replace(/[()-]/gi, '').replace(" ", ""));
    var url = api_site_url + "/getTwilioToken/" + phone_number;

    $.getJSON(url)
        .done(function (data) {

            console.log('Token: ' + data.token);
            $("#js-phone-popup .toNumber").val("" + data.toNum + "");
            // Setup Twilio.Device
            Twilio.Device.setup(data.token);
            Twilio.Device.ready(function (device) {

            });
            Twilio.Device.error(function (error) {

                Twilio.Device.disconnectAll();
            });
            Twilio.Device.connect(function (conn) {
                console.log('Successfully established call!');
            });
            Twilio.Device.disconnect(function (conn) {
                console.log('Call ended.');
                updateLastCallHistory();
                Twilio.Device.disconnectAll();
            });
            Twilio.Device.incoming(function (conn) {
                console.log('Incoming connection from ' + conn.parameters.From);
                var archEnemyPhoneNumber = '+12099517118';
                if (conn.parameters.From === archEnemyPhoneNumber) {
                    conn.reject();
                    console.log('It\'s your nemesis. Rejected call.');
                } else {
                    // accept the incoming connection and start two-way audio
                    conn.accept();
                }
            });
        })
        .fail(function () {
            console.log('Could not get a token from server!');
        });
}
$(document).on("click", "#callbtn", function () {
    var path = $(location).attr('href');
    var split_data = path.split("/");
    var userId;
    if (split_data[4] = "patients") {
        userId = split_data[5];
    }
    var toNum = $("#js-phone-popup .toNumber").val();
    console.log('Calling ' + toNum + '...');
    var params = {
        To: '' + toNum + ''
    };
    var count = Twilio.Device.connect(params);
    console.log(count);
    $.ajax({
        url: api_site_url + '/createCallLogHistory',
        type: "post",
        data: {
            'userId': userId,
            "toNum": toNum,
            "direction": 'Outgoing',
            'rowID': 0,
            'com_type': 'Phone'
        },
        success: function (response) {
            response = JSON.parse(response);
            if (response.status == 'success') {
                $('#lastCallId').val(response.li_id);
                $('#lastCallId').val();
            } else if (response.status == 'error') {
                $("#lastCallId").val(0);
            }
        }
    });
    $('#callbtn').hide();
    $('#endcall').show();
});

$(document).on("click", "#endcall", function () {
    Twilio.Device.disconnectAll();
    updateLastCallHistory();
});

function updateLastCallHistory() {
    var path = $(location).attr('href');
    var split_data = path.split("/");
    var userId;
    if (split_data[4] = "patients") {
        userId = split_data[5];
    }
    var toNum = $("#js-phone-popup .toNumber").val();
    var lastCallLiId = $('#lastCallId').val();
    if (lastCallLiId > 0) {
        $.ajax({
            url: api_site_url + "/updateCallLogHistory",
            type: "post",
            data: {
                'userId': userId,
                "toNum": toNum,
                'rowID': lastCallLiId,
            },
            success: function (response) {
                if (response.status == 'success') {
                    console.log('success' + response);
                } else if (response.status == 'error') {
                    console.log('failed' + response);
                }
            }
        });
    }
    $('#callbtn').show();
    $('#endcall').hide();
}
$(document).on("click", ".close_Box", function () {
    //close the all the active Twilio Connecton.
    Twilio.Device.destroy();
});


function sendMessage() {
    var serialized_form_data = $('#sendSmsForm').serialize();
    var url = $('#sendSmsForm').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data: serialized_form_data,
        success: function (data) {
            js_sidebar_notification('success', 'message delivered successfully');
            $(".close_Box").click();
        }
    });
}
/*******************************
 *******************************
 Twilio work End || Author :Manikandan CD-19.
 *******************************
 *******************************/

$('tr[data-url="javascript:void(0)"]').click(function () {
    js_alert_popup("Non-financial data only can be edited after submission.");
});

/* Use this class for avoid the space */
$(document).on("keypress", ".js_no_space", function (e) {   //$('.js_no_space').keydown(function(e) {   
    if (e.keyCode == 32) // 32 is the ASCII value for a space
        e.preventDefault();
});
//$('#session_model').modal({backdrop: 'static', keyboard: false})  ;

/* Modal POPUP  background focus out */
/* $(document).ready(function() {
 $('form:first *:input[type!=hidden]:first').focus();
 $("#session_model").on('show.bs.modal', function(event) {
 $('input').not('.form-control, .js_ledger_claim_search').focus();
 });
 $('input').not('.form-control, .js_ledger_claim_search').focus();
 });
 */
function listingpageHighlight(tId) {
    if ($('#' + tId).length > 0) {
        var table = $('#' + tId).DataTable();
        table.on('draw', function () {
            var body = $(table.table().body());
            body.unhighlight();
            var str = $('.dataTables_filter input').val();
            if ($.trim(str) != '') {
                body.highlight(table.search());
            }
        });
    }
}

/* Show and hide loading image start */
var displayLoadingImage = function () {
    if ($('#selLoading').length)
        $("#selLoading").show();
};

var hideLoadingImage = function () {
    if ($('#selLoading').length)
        $("#selLoading").hide();
};
/* Show and hide loading image end */
/* Scroll  in model popup calendar hide the datepicker in Quick patient */
$("input .dm-date").datepicker();

$('.js_datepicker_scroll').scroll(function () {
    $("input").datepicker("hide");
    $("input").blur();
});

$(document).delegate('a[data-target=#create_notes]', 'click', function () {
    $("#create_notes .modal-body").html("");
    var target = api_site_url + '/' + $(this).attr("data-url");
    /* if($(this).attr("data-notes-type") != ''){
     $('#notes_type_hidden').val($(this).attr("data-notes-type"));
     } */
    var type = $(this).attr("data-notes-type");
    var claim_no = $(this).attr("data-notes-claim");
    $("#create_notes .modal-body").load(target, function () {
        $("select#jsclaimnumber").select2();
        $("select.select2").select2();
        if (type != '' && type != undefined) {
            $('#patient_notes_type').val(type).change().select2('readonly', true);
            $('#jsclaimnumber').val(claim_no).change().select2('readonly', true);
        }
    });
});

/*** Start to dropdown in patient Notes ***/
$(document).on('change', '.js_patient_notes_type', function () {
    value = $(this).val();
    /*content = content_sel = '';   
     content_sel = $('input[name="'+value+'"]');    
     if(value == 'alert_notes' || value == 'statement_notes') 
     content = content_sel.val();   */
    if (value == 'claim_notes') {
        $('.js_claim_note').removeClass('hide');
        $('.js_claim_note').addClass('show');
        $('#js-bootstrap-validator').data('bootstrapValidator').addField('jsclaimnumber').enableFieldValidators('jsclaimnumber', true);
    } else {
        $('.js_claim_note').removeClass('show');
        $('#js-bootstrap-validator').data('bootstrapValidator').addField('jsclaimnumber').enableFieldValidators('jsclaimnumber', false);
        if ($('.js_claim_note').hasClass("hide") == false) {
            $('.js_claim_note').addClass('hide');

        }
    }
    /*$('#content').val(content);
     if(typeof content != 'undefined')
     $('#js-exist-id').val(content_sel.attr("id"));*/
});

$(document).on("change", "#jsclaimnumber", function (event) {
    claim_number_val = $(this).val();
    if (claim_number_val == 'all') {
        $("#jsclaimnumber").select2({
            formatSelectionTooBig: function (a) {
                return "Delete all to get claims displayed";
            },
            maximumSelectionSize: 1
        });
    } else if (claim_number_val == null || claim_number_val == '') {
        var optionExists = $("#jsclaimnumber option[value=all]").length;
        if (!optionExists) {
            $('#jsclaimnumber').prepend('<option value="all">All</option>');
        }
        $("#jsclaimnumber").select2({});
    } else if (claim_number_val != 'all') {
        $('#jsclaimnumber option[value="all"]').remove();
    }
});
/*** End to dropdown in patient Notes ***/

$(document).delegate('.js-submit-popupform-notes', 'submit', function (e) {
    if (e.isDefaultPrevented()) {
        //alert('form is not valid');
    } else {
        var formData = $('form.js-submit-popupform-notes').serialize();
        id = $(this).attr('form-data');
        action_url = $(this).attr("action");
        $.ajax({
            type: 'post',
            url: action_url,
            data: formData,
            success: function (data) {
                // console.log(data);
                data_val = JSON.parse(data);
                //  console.log(data_val);
                status = data_val.status;
                msg = data_val.message;
                msg_data = msg.patient_notes_type;
                if (status == "success") {
                    $("#create_notes").hide();
                    js_sidebar_notification('success', msg);
                    if ($('a[data-target="#create_notes"]').attr('data-notes-type') != '' && $('a[data-target="#create_notes"]').attr('data-notes-type') != undefined) {
                        var current_active_class = $('.js_claimdetlink').parents('li.active').attr('class');
                        current_active_class = current_active_class.replace(" ", ".");
                        // Reload only for claim review tabs not for list               
                        if (current_active_class.indexOf("js-claim-tab-info_") >= 0) {
                            $('.' + current_active_class).find('span').click();
                            $('body').removeClass('modal-open');
                        }
                    } else {
                        location.reload();
                    }
                } else {
                    msg = (typeof msg_data != undefined) ? "Type already selected" : msg
                    js_sidebar_notification('error', msg);
                }
            }
        });
        return false;
    }
});

$(document).on('click', '#close-popup-here', function () {
    $('#close-button').click();
});

$(document).on('change', 'input[name="title"][id="title"]', function () {
    $('small[data-bv-for="title"]').hide();
});

//update user logout in admin dashboard
$(document).on("click", ".userLogout", function () {
    var id = $(this).attr('data-id');
    var URL = api_site_url;
    $.ajax({
        url: URL + '/admin/dashboard/updateuser',
        type: "post",
        data: { 'id': id, '_token': $('input[name="token"]').val() },
        success: function (data) {
            $('#comment_' + id).html(data);
        }
    });
    return false;
});

/* is_logged_in update logout user */
$(document).on("click", ".updatelogoutUser", function () {
    var id = $(this).attr('data-id');
    var URL = api_site_url;
    $.ajax({
        url: URL + '/admin/adminuser/updateLoggedin',
        type: "post",
        data: { 'id': id, '_token': $('input[name="token"]').val() },
        success: function (data) {
            if (data.status == 'success') {
                var user_id = 'user_id_logout_' + data.message;
                $('#' + user_id).addClass('med-red');
            } else if (data.status == 'error') {
                var user_id = 'user_id_logout_' + data.message;
                $('#' + user_id).addClass('med-green');
            }
        }
    });
    return false;
});

/* login attempt update in users*/
$(document).on("click", ".loginAttempt", function () {
    var id = $(this).attr('data-id');
    var URL = api_site_url;
    $.ajax({
        url: URL + '/admin/adminuser/updateLoginAttempt',
        type: "post",
        data: { 'id': id, '_token': $('input[name="token"]').val() },
        success: function (data) {
            if (data.status == 'success') {
                var user_id = 'user_id_loginAttempt_' + data.message;
                $('#' + user_id).addClass('med-red');
            } else if (data.status == 'error') {
                var user_id = 'user_id_loginAttempt_' + data.message;
                $('#' + user_id).addClass('med-green');
            }
        }
    });
    return false;
});

/* active or inactive status update in users table */
$(document).on("click", ".updateStatus", function () {
    var id = $(this).attr('data-id');
    var URL = api_site_url;
    $.ajax({
        url: URL + '/admin/adminuser/updateUserStatus',
        type: "post",
        data: { 'id': id, '_token': $('input[name="token"]').val() },
        success: function (data) {
            if (data.status == 'success') {
                var user_id = 'user_id_status_' + data.message;
                $('#' + user_id).addClass('med-red');
            } else if (data.status == 'error') {
                var user_id = 'user_id_status_' + data.message;
                $('#' + user_id).addClass('med-green');
            }
        }
    });
    return false;
});

// This function used for sidebar notification
function js_sidebar_notification(type, msg) {
    if (msg != '') {
        $("#show_error_type").html(type);
        $("#show_error_msg").html(msg);
        if(type == 'success')
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

$(document).on('click', '.snackbar-div', function () {
    $('.snackbar-div').removeClass('show');
    $('.snackbar-div').removeClass("error");
});

$(document).on("click mouseenter", ".js_hove_claim", function () {
    var data_val = $(this).attr("data-val");
    //data_val = data_val.replace(/\s/g, '') ;
    data_val = data_val.replace(/\,/g, '<br/>');
    var span_tag = '<div class="on-hover-content"><span class="med-orange font600">' + data_val + '</span></div>';
    $(".js_hove_claim_show").append(span_tag).show();
})

$(document).on("click mouseleave", ".js_hove_claim", function () {
    $(".js_hove_claim_show").html("").hide()
})

$(function(){    
	setTimeout(function () {	
		//**create all types of user hide and show element in admin dashboard start**//
		$(document).on('ifToggled change', '#admin_user', function () {
			if ($(this).is(":checked") == true && $(this).val() == 'customer') {
				$(".js_customer").hide();
				$(".js_customer_practice").hide();
				$(".js_user_access").hide();
				$(".js_prac_access_app").hide();
				$(".medcubics_role").show();
				$(".practice_role").hide();
			} else {
				$(".js_med_role").select2("val", null);
				$(".js_pra_role").select2("val", null);
				$(".medcubics_role").hide();
				$(".practice_role").show();
			}
		});

		//create user when click practice admin
		$(document).on('change', '#customer_practice_admin', function () {
			if ($(this).is(":checked") == true && $(this).val() == 'practice_admin') {
				$(".js_customer").show();
				$(".js_customer_practice").show();
				$(".js_user_access").hide();
				$(".js_prac_access_app").hide();
				$(".practice_role").show();
				$(".medcubics_role").hide();
			} else {
				$(".js_pra_role").select2("val", null);
				$("#js-customer").select2("val", null); console.log("1111");
				$("#customer_practice_list").select2("val", null);
				$(".js_customer_practice").hide();
				$(".medcubics_role").show();
				$(".practice_role").hide();
			}
		});
		//create user when click practice user
		$(document).on('change', '#customer_practice_user', function () {
			if ($(this).is(":checked") == true && $(this).val() == 'practice_user') {
				$(".js_customer").show();
				$(".js_customer_practice").show();
				$(".js_user_access").show();
				$(".practice_role").show();
				$(".medcubics_role").hide();
				if (document.getElementById('js_useraccess_web').checked) {
					$(".js_prac_access_app").hide();
					$(".js_customer_practice").show();
				} else {
					$(".js_prac_access_app").show();
					$(".js_customer_practice").hide();
				}
			} else {
				$(".js_pra_role").select2("val", null);
				$("#js-customer").select2("val", null); console.log("2222");
				$("#customer_practice_list").select2("val", null);
				$(".js_customer_practice").hide();
				$(".medcubics_role").show();
				$(".practice_role").hide();
			}
		});

		// User type provider related changes
		// Rev.1 - Ref.MR-2662 - 09-08-2019 - Ravi
		$(document).on('change', '#customer_provider', function () {
			if ($(this).is(":checked") == true && $(this).val() == 'provider') {
				$(".js_customer").show();
				$(".js_customer_practice").show();
				$(".js_provider").show();
			} else {
				$("#js-customer").select2("val", null); console.log("33333");
				$("#customer_practice_list").select2("val", null);
				$("#selected_pra_pro_list").select2("val", null);
			}
		});
	}, 100);

});

$(document).on('ifToggled change', '#js_useraccess_app', function () {
    if ($(this).is(":checked") == true && $(this).val() == 'app') {
        $(".js_prac_access_app").show();
        $(".js_customer_practice").hide();
    } else {
        $("#selected_practice_list").select2("val", null);
        $("#selected_pra_fac_list").select2("val", null);
        $("#selected_pra_pro_list").select2("val", null);
        $(".js_prac_access_app").hide();
    }
});

$(document).on('ifToggled change', '#js_useraccess_web', function () {
    if ($(this).is(":checked") == true && $(this).val() == 'web') {
        $(".js_prac_access_app").hide();
        $(".js_customer_practice").show();
    } else {
        $("#customer_practice_list").select2("val", null);
        $(".js_customer_practice").hide();
    }
});

$(document).on('change', "#js_selet_app", function () {
    var value = $(this).val();
    $('.js_app_data').hide();
    $("#" + value).show();
});
//**create all types of user hide and show element in admin dashboard end**//

//** customer related practice lists in user create admin dashboard start */
$(document).on('change', '.customer_practice_name', function () {
    var customer_name = $(this).val();
    if (customer_name != '') {
        $('#customer_practice_list option').remove();
        $('#selected_practice_list option').remove();
        $('#selected_pra_fac_list option').remove();
        $('#selected_pra_pro_list option').remove();
        $.ajax({
            url: api_site_url + '/admin/api/customer/' + customer_name,
            type: 'get',
            success: function (msg) {
                customer_practices = msg.customer_practices;
                $('#customer_practice_list').append("<option value=''>-- Select --</option>");
                $.each(msg.customer_practices, function (i, val) {
                    $('#customer_practice_list').append("<option value='" + i + "'>" + val + "</option>");
                });
                $.each(customer_practices, function (i, val) {
                    $('#selected_practice_list').append("<option value='" + i + "'>" + val + "</option>");
                });              
            }
        });
    }
});

/** Practice related Provider lists in user create admin dashboard start Thilagavathy */
$(document).on('change click', '.js_admin_practice_id', function () {console.log("test");
    var provider_name = $(this).val();
    if (provider_name != '') {    
        $('#selected_pra_fac_list option').remove();
        $('#selected_pra_pro_list option').remove();
        $.ajax({
            url: api_site_url + '/admin/api/practice/' + provider_name,
            type: 'get',
            success: function (msg) { 
                facility = msg.facility;
                provider = msg.provider;
             
                $.each(facility, function (i, val) {
                    $('#selected_pra_fac_list').append("<option value='" + i + "'>" + val + "</option>");
                });
                $.each(provider, function (i, val) {
                    $('#selected_pra_pro_list').append("<option value='" + i + "'>" + val + "</option>");
                });
            }
        });
    }
});
//** customer related practice lists in user create admin dashboard end*/

//**edit and update all types of user hide and show element in admin dashboard start**//
$(document).ready(function () {
    var user_type = $('.js_user_type').val();
    if (user_type == 'practice_admin') {
        $(".js_customer").show();
        $(".js_customer_practice").show();
        $(".js_user_access").hide();
        $(".js_prac_access_app").hide();
    } else if (user_type == 'practice_user') {
        $(".js_customer").show();
        $(".js_customer_practice").show();
        $(".js_user_access").show();
        if (document.getElementById('js_useraccess_web').checked) {
            $(".js_prac_access_app").hide();
            $(".js_customer_practice").show();
        } else {
            $(".js_prac_access_app").show();
            $(".js_customer_practice").hide();
        }
    } else if (user_type == 'provider') {
		$(".js_customer").show();
        $(".js_customer_practice").show();
		$(".js_provider").show();
	}
    //    GuarantorSelfValidate();
});
//**edit and update all types of user hide and show element in admin dashboard end**//
/* 
    ## Provider scheduler Add provider time for the facility 
    ## End date option is disable Or Enable Based on End_date_option is selected
*/
function endDateOptionEnableORDisable() {
    var dateOption = $("input[name='end_date_option']:checked").val();
    if (dateOption == 'after') {
        $("input[name='end_date']").prop("disabled", true);
        $("input[name='no_of_occurrence']").prop("disabled", false);
    }
    else if (dateOption == 'on') {
        $("input[name='no_of_occurrence']").prop("disabled", true);
        $("input[name='end_date']").prop("disabled", false);
    }
    else if (dateOption == 'never') {
        $("input[name='end_date']").val("");
        $("input[name='end_date']").prop("disabled", true);
        $("input[name='no_of_occurrence']").val('0').prop("disabled", true);
    }
}

$(document).on('click', "input[name='end_date_option']", function () {
    endDateOptionEnableORDisable();
});

/*Calendar and dropdown box overlapped issue fixed*/
$(document).on('select2-open', ".select2", function () {
    $(".dm-date").datepicker("hide");
});

/* horizontal scroll */
$('.selector ul li').click(function () {
    $('.selector ul li').removeClass('selected');
    $(this).addClass('selected');
});
/*$(document).ready(function () {
    $('.selector').mousewheel(function (e, delta) {
        this.scrollLeft -= (delta * 40);
        e.preventDefault();
    });     
});*/

//Validation for Guarantor
$(document).load(function () {
    //   if($('select[name="guarantor_relationship"]').val() =='Self'){
    //      $('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
    //      $('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
    //      $('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());
    //                
    //                $('input[name="guarantor_last_name"]').attr('readonly', true);
    //      $('input[name="guarantor_first_name"]').attr('readonly', true);
    //      $('input[name="guarantor_middle_name"]').attr('readonly', true);
    //
    //  }else{
    //      $('input[name="guarantor_last_name"]').val('');
    //      $('input[name="guarantor_first_name"]').val('');
    //      $('input[name="guarantor_middle_name"]').val('');
    //                
    //                $('input[name="guarantor_last_name"]').attr('readonly', false);
    //      $('input[name="guarantor_first_name"]').attr('readonly', false);
    //      $('input[name="guarantor_middle_name"]').attr('readonly', false);                
    //  }
});

$(document).on('change', 'select[name="guarantor_relationship"]', function () {
    GuarantorSelfValidate();
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_last_name');
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_first_name');
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_middle_name');
});
$('.js_arrow').load(function () {
    if ($(this).attr('id') == 'contact') {
        GuarantorSelfValidate();
    }
});
var inFormOrLink;
//$('a').bind('click', function() { inFormOrLink = true; });

$(window).bind("beforeunload", function () {
    var current_url = window.location.href;
    // return inFormOrLink ? "Do you really want to close?" : null; 
});

/* Dynamic listing page search */

$(document).on('click', '.dynamic_listing_search', function () {
    var page_name = $(this).attr('data-page-name');
    var search_id = $(this).attr('data-search-id');
    $.ajax({
        type: "GET",
        url: api_site_url + '/claims/generate/search/' + page_name + '/' + search_id,
        data: '',
        success: function (data) {
            $('.listing_search_append').html(data);
            $('select.form-select').select2();
            datePickerCall();
        }
    });
});

$(document).on('click', '.url_search_data', function () {
    var page_name = $(this).attr('data-page-name');
    var search_id = $(this).attr('data-search-id');
    var url = $(this).attr('data-url');
    $.ajax({
        type: "GET",
        url: api_site_url+'/claims/generate/searchSavedData/' + page_name + '/' + search_id,
        data: '',
        success: function (data) {
            window.location.href = url+$.trim(data);
        }
    });
});

/* Dynamic listing page search */
/* toggle icon in patient document summary list start*/
$('.accordion-toggle').click(function () {
    $(this).find('h4 i').toggleClass('fa-plus-circle fa-minus-circle');
});
/* toggle icon in patient document summary list end*/

/*Add icon Removed file in all uploaded file start*/
$(document).ready(function () {
    $(document).on("click", ".removeFile", function () {
        _formId = $(this).parents('form').attr("id");
        $(".uploadFile").val("");
        $('.js-display-error').html("");
        $(".removeFile").hide();
        $('#' + _formId).bootstrapValidator('revalidateField', "filefield");
    });
    $(document).on("click", ".uploadFile", function () {
        var url = window.location.href;
        var arr = url.split('/');
        if (jQuery.inArray("documentsummary#create_document", arr) != -1 || jQuery.inArray("documents", arr) != -1 || (jQuery.inArray("patients", arr) != -1 && jQuery.inArray("edit", arr) != -1) || jQuery.inArray("facility", arr) != -1 || jQuery.inArray("provider", arr) != -1) { } else {
            $('.js-display-error').html("");
            $(".removeFile").hide();
        }
    });
    $(".removeFile").hide();
});
/*Add icon Removed file in all uploaded file end*/

$(document).ready(function () {
    /* Patient Alert notes toggle starts */
    $('#showmenu').click(function () {
        $('.menu').slideToggle("fast");
    });

    $('#showmenu1').click(function () {
        $('.menu').slideToggle("fast");
    });
    /*Patient Alert notes toggle ends */

    $("input .dm-date").attr("autocomplete", "off");
});

/* search Remember */

function data_collection() {
    var final_data = '[';
    $('select.auto-generate:visible').each(function () {
        if ($(this).select2('val') != '')
            final_data = final_data + '{"type":"select","label_name":' + '"' + $(this).attr('name') + '","value":' + '"' + $(this).select2('val') + '"},';
    });

    $('input.auto-generate:visible').each(function () {
        if ($(this).val() != '')
            final_data = final_data + '{"type":"select","label_name":' + '"' + $(this).attr('name') + '","value":' + '"' + $(this).val() + '"},';
    });
    final_data = final_data.replace(/,\s*$/, "");
    final_data = final_data + ']';
    return final_data;
}

// Search more related common codes starts here

function __searchMoredata() {
    var moreArr = $('select.more_generate').select2('val');
    $.each(moreArr, function (index, item) {
        if ($('div#' + item).is(":visible") == false) {
            $('div.search_fields_container').last().append("<div class='dynamic_append'>" + $('div.' + item + '_more').html() + "</div>");
            $('div#s2id_' + item).hide();
            $('select#' + item).select2();
        }
    });
    if (moreArr.length == 1) {
        var all_selected_class = "#" + moreArr[0];
        $("div.dynamic_append").children().not(all_selected_class).remove();
    } else if (moreArr.length > 1) {
        var all_selected_class = "#" + moreArr.join(",#");
        $("div.dynamic_append").children().not(all_selected_class).remove();
    } else if (moreArr.length == 0) {
        $('div.dynamic_append').remove();
    }
    var data_arr = {};
    $('input.auto-generate:visible').each(function () {
        data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
    });                                                                        // Getting all data form input fields

    $('select.auto-generate').filter(':visible').each(function () {
        data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
    });                                                                        // Getting all data form select fields
    dataArr = { data: data_arr };
}

// Search more related common codes starts ends

//popup close to esc button
$(document).keyup(function (e) {
    //console.log("comes" + e.keyCode);
    if (e.keyCode == 27) {
        var ModelID = $('.modal:visible').attr("id");
        //console.log(get_current_id);
        var current_id = get_current_id[get_current_id.length - 1];
        //console.log(current_id);
        $("#" + current_id).modal('hide');
    }
});

//to open insurance payment popup
$(document).mapKey('Alt+i', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $('.js_pay_ins_key').click();
        return false;
    }
});

//to open patient payment popup
$(document).mapKey('Alt+p', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $('.js_pat_pay_key').click();
        return false;
    }
});

$('.dm-date').on('click', function (e) {
    //console.log("datepickr focused");
    e.preventDefault();
    $(this).attr("autocomplete", "off");
});


// To check whether a string is JSON or not
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

// $('.js_export').on('click', function(e) { 
//     $("#js_export").modal("show");

// });	

//input auto fill disabling
// disableAutoFill('#js-bootstrap-validator');
// disableAutoFill('.v2-insurance-info-form');
// disableAutoFill('.search_fields_container');
// disableAutoFill('.js-address-class');
// disableAutoFill('#js-bootstrap-validator');
// disableAutoFill('.select2-container');
//disableAutoFill('.patients-info-form');
function disableAutoFill(element) {
    // $(element).find('input:visible').each(function () {
    //     $(this).attr("autocomplete", "nope");
    // });
}

$(document).on('ifChanged', 'input[type="radio"][name="provider_entity_type"]', function () {
    var type = $(this).val();
    if (type == 'NonPersonEntity') {
        $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', false);
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'provider_degrees_id');
        $('.js-other-provider-options').addClass('hide');
        $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
        $('#npi_field_group').removeClass('hide');
        $('#npi_field_individual').addClass('hide');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
        $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'organization_name', true);

    } else {
        $('.js-other-provider-options').addClass('hide');
        $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
        $('#npi_field_group').addClass('hide');
        $('#npi_field_individual').removeClass('hide');
        $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'organization_name', false);
        $('#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'provider_degrees_id', true);
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'provider_degrees_id');
    }
})

// First input focus for all modal - START
/*$(document).ajaxComplete(function() {
        focusIn();
});*/

/*$(".modal").on('shown.bs.modal', function(){
    console.log('modal');$(this).find('input[type=text],textarea,select').filter(":visible:enabled:not([readonly]):not([disabled='disabled']):not([type='hidden']):first").trigger('focus')
    if(!$("#session_model.modal").hasClass('in')){
        focusIn();
    }
     setTimeout(function(){
        focusIn();
        if(!$("#session_model.modal").hasClass('in')){
            $('#js_newpatient_scheduler.in .modal-body').find('input[type=text],textarea,select').filter(":visible:enabled:not([readonly]):not([disabled='disabled']):not([type='hidden']):first").focus();
        }
    }, 5000); 
});

$(document).on('click','#addmore,#addmore_contact_v2,.js_quick_add', function(){
     setTimeout(function(){
        focusIn();
    }, 500); 
});

function focusIn(){
    if(!$("#session_model.modal").hasClass('in')){
        $('.modal.in .modal-body').find('input[type=text],textarea,select').filter(":visible:enabled:not([readonly]):not([disabled='disabled']):not([type='hidden']):first").focus();
    }
}*/
// First input focus for all modal - END

$(document).ready(function () {
    var ser = $(location).attr('href').split("/").splice(0, 3).join("/");
    var id = $(location).attr('href').split("/").splice(4, 4).join("/");
    var m_id = $(location).attr('href').split("/").splice(4, 1).join("/");
    // var mode = $(location).attr('href').split("/").splice(3, 1).join("/");
    var arr = id.split('/');
    var chk = ser + "/patients/" + id;
    var url = ser + "/patients/" + arr[0] + "/billing/create";
    var uri = $(location).attr("href");
    var value = 0;
    if (chk == uri) {
        // alert(chk+"<br>"+uri);
        $(document).on('keyup', function (e) {
            var n = e.which;
            if (n == 18 || n == 75) {
                value = value + n
                if (value == 93)
                    window.location.replace(url);
            }
            // var key = key+n;
        });
    }
    $(document).on('click','.heart',function(){
        var tempString = '';
        jQuery.fn.justtext = function () {
            tempString = $.trim($(this).clone().children().replaceWith(",").end().text());
            if ($.trim($(this).find('span').text()) != '') {
                tempString += $.trim($(this).find('span').text());
            }
            return tempString;
        };
        var heading = $('#heading').val();
        var mode = $("#selected_tab").val();
        var selected_tab = $(".toolbar-heading").justtext();
        var token = $('#token').val();
        var cur_url = $(this).attr('data-val');
        if ($(this).children().hasClass('fa-heart-o')) {
            $.ajax({
                type: "post",
                url: api_site_url + '/wishlists/create',
                data: { '_token': token, 'url': uri, 'module': heading, 'sub_module': selected_tab, 'mode': mode, 'mode_id': m_id },
                success: function (result) {
                    js_sidebar_notification("success", "Page added to Quicklist");
                    window.location.reload();
                }
            });
        }
        else {
            if (cur_url == uri)
                del_data = { '_token': token, 'url': uri };
            else
                del_data = { '_token': token, 'url': cur_url };
            $.ajax({
                type: "post",
                url: api_site_url + '/wishlists/delete',
                data: del_data,
                success: function (result) {
                    js_sidebar_notification("success", "Page removed from Quicklist");
                    window.location.reload();
                }
            });
        }
    });
});

$(document).ready(function () {
    var value = 0;
    $(document).on('keyup', function (e) {
        var patient = ser + "/patients/create";
        var n = e.which;
        if (n == 18 || n == 81) {
            value = value + n
            if (value == 99)
                window.location.replace(patient);
        }
        // var key = key+n;
    });
    $(document).on('keyup click', function () {
        setTimeout(function () {
            if ($('#js-model-swith-patient').is(':visible') == true) {
                $('#js-model-swith-patient').find('#js_modal_search_swith_patient_keyword').focus();
            }
        }, 100);
    });
});
// for date range picker first time click 
/*$(document).on('click change', 'div.ranges>ul>li', function () {
	var mod = $(location).attr('href').split("/").splice(3, 1).join("/");
	//if(mod != 'reports'){// for date range picker Don't work in reports module at first time click
    if(!$(location).attr('href').includes('reports')) {
		if ($(this).text() !== 'Custom Range')
			getData();
	}
});*/

//Search patient popup
$(document).mapKey('Ctrl+2', function (e) {
    if(isProvider=='no'){
        if (!$("body").hasClass("modal-open")) {
            /*if($('body #js-model-swith-patient').length){
                console.log('ba');
            }*/
            $('#js-model-swith-patient').modal();
            return false;
        }
    }
});

// -------------Online time checking function Start ------------


$(document).ready(function(){
	//setInterval(online_status, 1000 * 60 * 3);   /*  ------3 minutes------       */
	function online_status(){
        if(document.visibilityState == 'visible'){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"post",
                url: api_site_url + '/onlineStatus',
                success: function(){
                    //console.log("success");
                }
            })
        }
	}
});

// -------------Online time checking function End ------------

//Prevent BODY from scrolling when a modal is opened in ar workbench - issue fixed
$(document).on('click', '.js_show_problem_list', function (e) {
    $("#show_problem_list").on("show.bs.modal", function () {
        $('body').css("overflow", "hidden");
    }).on("hidden.bs.modal", function () {
        $('body').css("overflow", "scroll");
    });
});
//Prevent BODY from scrolling when a modal is opened in Documents - issue fixed
$(document).on('click ifToggled change', 'tr.js_show_document_assigned_list td:not(".js-prevent-show")', function (e) {
    $("#show_document_assigned_list").on("show.bs.modal", function () {
        $('body').css("overflow", "hidden");
    }).on("hidden.bs.modal", function () {
        $('body').css("overflow", "scroll");
    });
});

// Information popup for claim delete restirction 
$(document).on('click', '.js_del_alert', function (e) {
    js_alert_popup("Claim cannot be deleted due assigned to Attorney!!!");
});

/*
     * This Function For Security Code Updation
     * Author		: Kriti Srivastava
     * Created on	: 30July2021
	 * JIRA Id		: MED3-8
     */

/* Yes or No security code in users table */
$(document).on("click", ".updateCode", function () {
    var id = $(this).attr('data-id');
    var URL = api_site_url;
    $.ajax({
        url: URL + '/admin/adminuser/updateSecurityCode',
        type: "post",
        data: { 'id': id, '_token': $('input[name="token"]').val() },
        success: function (data) {
            if (data.status == 'success') {
                var user_id = 'update_security_code_' + data.message;
                $('#' + user_id).addClass('med-red');
            } else if (data.status == 'error') {
                var user_id = 'update_security_code_' + data.message;
                $('#' + user_id).addClass('med-green');
            }
			location.reload();
        }
    });
    return false;
});













