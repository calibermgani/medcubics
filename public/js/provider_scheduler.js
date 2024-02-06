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
                                            var currentDate = new Date()
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
                    endDateOptionEnableORDisable();

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
        });
        $("#provider_scheduler_modal").modal("show");
        return false;
    });
});

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

function daydiff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}

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
