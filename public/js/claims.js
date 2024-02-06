/*** Starts - Check/Uncheck checkbox ***/
$(document).on('click', '#js-select-all', function () {
    $("#claims_table input:checkbox").prop('checked', $(this).prop("checked"));
});
$(document).on('click', '#js-select-all', function () {
    $("#claims_table_edi input:checkbox").prop('checked', $(this).prop("checked"));
});

$(document).on('change', '.js-select-all-sub-checkbox', function () {
	if($('.js-select-all-sub-checkbox:checked').length != $('.js-select-all-sub-checkbox:checkbox').length)
		$('#js-select-all').prop('checked', false);
	else
		$('#js-select-all').prop('checked', true);
	
    if (($('.js-select-all-sub-checkbox:checked').length == $('.js-select-all-sub-checkbox:checkbox').length) && ($('.js-select-all-sub-checkbox:checked').length == $('input[name="encodeClaim"]').val().split(',').length)) {
        $('select[name="js-select-option"]').val('all');
    } else if (($('.js-select-all-sub-checkbox:checked').length == $('.js-select-all-sub-checkbox:checkbox').length)) {
        $('select[name="js-select-option"]').val('page');
    }
});

$(document).on('change', 'select[name="js-select-option"]', function () {
    var type = $(this).val();
    if (type == 'none') {
        $('.js-select-all-sub-checkbox').prop('checked', false);
    } else {
        $('.js-select-all-sub-checkbox').prop('checked', true);
    }
});
/*** Ends - Check/Incheck checkbox ***/

$(document).on('click', '#patientnote_model .js-claim', function () {
    var redirect_url = $('#redirect_url').val();
    window.location = api_site_url + "/" + redirect_url;
});

$(document).on('click', ".js-claim_error", function () {
    claim_id = $(this).attr('data-claimid');
    msg = $("#claim_error_msg_" + claim_id).val();
    $("#claims_error_model .med-green").html(msg);
    $("#claims_error_model").modal({ show: 'open', keyboard: false });
});

/*** Starts - Electronic claim submit ***/
$(document).on('click', '.js-claim-submit-electronic', function () {
    var type = $('select[name="js-select-option"]').val();
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        var paper_claim_selected_count = $('.cls-paper:checked, .cls-patient:checked').length;
        if (paper_claim_selected_count == 0) {
			
            $.confirm({
                text: "Are you sure to submit the claims?",
                confirm: function () {
                    if (type == 'page' || type == 'none') {
                        selected_claim_ids = [];
                        $('.js-select-all-sub-checkbox:checked').each(function () {
                            selected_claim_ids.push($(this).attr('value'));
                        });
                    } else if (type == 'all') {
                        selected_claim_ids = [];
                        selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
                    }
                    var myform = $('#js-claim-submit-electronic').not('input[name="encodeClaim"]');
                    var serialized = myform.serialize();
                    $("#selLoading").show();
					displayLoadingImage();
                    $.ajax({
                        type: "post",
                        datatype: 'JSON',
                        url: api_site_url + '/claims/initialediscrubbing',
                        data: serialized + '&claim_ids=' + selected_claim_ids,
                        success: function (result_values) {
                            $("#selLoading").hide();
							hideLoadingImage();
                            if (result_values['status'] == 'success') {
                                var result = result_values['data']['claim_process_details'];
                                var message = '<p>Total Selected Claims: ' + result['total_selected_claims'] + '</p>';
                                message = message + '<p>Passed Claims: ' + result['claim_success_count'] + '</p>';
                                message = message + '<p>Failed Claims: ' + result['claim_error_count'] + '</p>';
                                js_alert_popup(message);
                                $("#patientnote_model button").addClass('js-claim');

								// Changed Redirection claim to claims electronic page
								//Revision 1 - Ref: MR-2499 08 Aug 2019: Selva

                                $('#redirect_url').val('claims/status/electronic');
                                return false;
                            } else {
                                js_alert_popup(result_values['message']);
                            }

                        }
                    });
                }
            });
        } else {
            js_alert_popup("Unable to send claims electronically since some selected claims don't have insurance or payer id.");
        }
    } else {
        js_alert_popup('Select atleast one claim to do an action');
    }
    return false;
});

function DoInitialScrubbing() {
    selected_claim_ids = [];
    $('.js-select-all-sub-checkbox:checked').each(function () {
        selected_claim_ids.push($(this).attr('value'));
    });
    var myform = $('#js-claim-submit-electronic');
    var serialized = myform.serialize();
    $.ajax({
        //type: "GET",
        datatype: 'JSON',
        url: api_site_url + '/claims/initialscrubbing',
        data: serialized + '&claim_ids=' + selected_claim_ids,
        success: function (result_values) {
            if (result_values['status'] == 'success') {
                var result = result_values['data']['claim_process_details'];
                var message = '<p>Total Claims: ' + result['total_selected_claims'] + '</p>';
                message = message + '<p>Passed Claims: ' + result['claim_success_count'] + '</p>';
                message = message + '<p>Scrubbed Claims: ' + result['claim_error_count'] + '</p>';
                js_alert_popup(message);
            } else {
                js_alert_popup(result_values['message']);
            }
        }
    });
}
/*** Starts - Electronic claim submit ***/

/*** Starts - Paper claim submit ***/
$(document).on('click', '.js-claim-submit-paper', function () {
    var data_type = $(this).attr("data-type");
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        if ($('.js-select-all-sub-checkbox:checked').length < 26) {
            var paper_claim_selected_count = $('.cls-patient:checked').length;
            var text_content = (data_type == "print") ? "Would you like to print now?" : "Would you like to download?";
            if (paper_claim_selected_count == 0) {
                $.confirm({
                    text: text_content,
                    confirm: function () {
                        $("body").on("keyup", function (e) {
                            if (e.keyCode == 27) {
                                return false;
                            }
                        });
                        $("#js_confirm_box_charges").attr("data-keyboard", "false");
                        selected_claim_ids = [];
                        $('.js-select-all-sub-checkbox:checked').each(function () {
                            selected_claim_ids.push($(this).attr('value'));
                        });
                        var alert_content = (data_type == "print") ? "Would you like to print without scale?" : "Do you want to download without scale?";
                        var myform = $('#js-claim-submit-electronic');
                        var serialized = myform.serialize();
                        $('#js_confirm_box_charges_content').html(alert_content);
                        $("#js_confirm_box_charges")
                            .modal({ show: 'false', keyboard: false })
                            .one('click', '.js_modal_confirm1', function (eve) {
                                confirm_alert = $(this).attr('id');
                                var scale = (confirm_alert == 'true') ? "/noscale" : "";

                                $('#js_confirm_box_claims_content').html("Would you like to change the claims status to Submitted?");
                                $("#js_confirm_box_claims")
                                    .modal({ show: 'false', keyboard: false })
                                    .one('click', '.js_modal_confirm1', function (eve) {
                                        submitted_status = $(this).attr('id');
                                        var sub_status = (submitted_status == 'true') ? "submit" : "unsubmit";


                                        $.ajax({
                                            type: "GET",
                                            datatype: 'JSON',
                                            url: api_site_url + '/claims/initialpaperscrubbing',
                                            data: serialized + '&claim_ids=' + selected_claim_ids + '&submission_status=' + sub_status,
                                            success: function (result_values) {
                                                ids = result_values['data']['claim_process_details']['success_claim'].toString();
                                                status = result_values.status;
                                                if (status == "success" && result_values['data']['claim_process_details']['success_claim'].length != 0) {
                                                    if (data_type == "print") {
                                                        var claim_url = api_site_url + '/claims/printclaims/' + ids + scale;
                                                    } else {
                                                        var claim_url = api_site_url + '/claims/downloadclaims/' + ids + scale;
                                                    }
                                                    if (data_type == "print") {
                                                        window.open(claim_url).print();
                                                    } else {
                                                        window.open(claim_url);
                                                    }

                                                }
                                                if (status == 'success') {
                                                    var result = result_values['data']['claim_process_details'];
                                                    var message = '<p>Total Claims: ' + result['total_selected_claims'] + '</p>';
                                                    message = message + '<p>Passed Claims: ' + result['claim_success_count'] + '</p>';
                                                    message = message + '<p>Scrubbed Claims: ' + result['claim_error_count'] + '</p>';
                                                    js_alert_popup(message);
                                                } else {
                                                    js_alert_popup(result_values['message']);
                                                }
                                                $('.js_note_confirm').addClass('paper_claim_submit');
                                                $('.js_common_modal_popup_cancel').addClass('hide');
                                            }
                                        });

                                    });

                            });

                    },
                    cancel: function () {
                        console.log("Cancel");

                    }
                });
            } else {
                js_alert_popup("Unable to submit claims since some selected claims in patient responsibility.");
            }
        } else {
            js_alert_popup('Select maximum 25 claims only');
        }

    } else {
        js_alert_popup('Select atleast one claim to do an action');
    }
    return false;
});

$(document).on('click', '.paper_claim_submit', function () {
    location.reload();
});
/*** Ends - Paper claim submit ***/

/*** Starts - Hold claims ***/

$(".js-hold-claims").click(function () {
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        var selected_claim_ids = [];
        $('.js-select-all-sub-checkbox:checked').each(function () {
            selected_claim_ids.push($(this).attr('value'));
        });
        var target = api_site_url + '/claims/holdreason';
        $("#jsClaimHoldOption").load(target, function () {
            $('#jsClaimHoldOption').on('show.bs.modal', function (e) {
                $('#hold_claim_ids').val(selected_claim_ids);
                $.AdminLTE.boxWidget.activate();
                $("select.select2.form-control").select2();

                $('#js-bootstrap-validator').bootstrapValidator({
                    message: 'This value is not valid',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: '',
                        invalid: '',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        hold_reason_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Select reason'
                                }
                            }
                        },
                        hold_reason: {
                            enabled: false,
                            validators: {
                                notEmpty: {
                                    message: 'Enter new reason'
                                }
                            }
                        }
                    }
                })
                    .on('change', '[name="hold_reason_id"]', function (e) {
                        fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                        if ($(this).val() == 'add_new') {
                            $('#js_new_hold_reason').removeClass('hide');
                            fv.enableFieldValidators('hold_reason', true).revalidateField('hold_reason');
                        } else {
                            $('#js_new_hold_reason').addClass('hide');
                            fv.enableFieldValidators('hold_reason', false).revalidateField('hold_reason');
                        }
                    })
                    .on('success.form.bv', function (e) {
                        // Prevent form submission
                        e.preventDefault();

                        var myform = $('#js-bootstrap-validator');
                        var serialized = myform.serialize();
                        $('.help-block').addClass('hide');
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: api_site_url + '/claims/updateholdclaims',
                            data: serialized,
                            success: function (result) {
                                if (result['status'] == 'success') {
                                    $('#jsClaimHoldOption .modal-body').html(result['message']);
                                    $('#jsClaimHoldOption').modal("hide");
                                    js_alert_popup('Updated successfully!!!');
                                    $("#patientnote_model button").addClass('js-claim');
                                    $('#redirect_url').val('claims');
                                    return false;
                                } else {
                                    js_alert_popup(result['message']);
                                    return false;
                                }
                            }
                        });
                        return false;
                    });
            });
            $('#jsClaimHoldOption').on('hidden.bs.modal', function () {
                $('#jsClaimHoldOption .modal-body').html('');
            });
            $("#jsClaimHoldOption").modal("show");
            return false;
        });
    } else {
        js_alert_popup('Select atleast one claim to hold');
    }
});
/*** Ends - Hold claims ***/

/* ** Starts - Pending claims ** */

/*  Per claim change pending status */
$(document).on('click', '.js-change-pending-claims', function () {
    var page_url = $(this).attr('data-page-url');
    var claim_id = $(this).attr('data-claim-id');
    selected_claim_ids = [];
    selected_claim_ids.push(claim_id);
    $.confirm({
        text: "Would you like to change the claims status to Pending?",
        confirm: function () {
            $.ajax({
                type: "GET",
                datatype: 'JSON',
                url: api_site_url + '/claims/pendingclaims',
                data: '&claim_ids=' + selected_claim_ids,
                success: function (result_values) {
                    if (result_values['status'] == 'success') {
                        var message = result_values['message'];
                        js_alert_popup(message);
                        $("#patientnote_model button").addClass('js-claim');
                        $('#redirect_url').val(page_url);
                        return false;
                    } else {
                        js_sidebar_notification('error', result_values['message']);
                    }
                }
            });
        }
    });
});

/*  Per claim change pending status */

/*  Multi claim change pending status */

//All list selection not working for Pending and Paper Status
//Revision 1 - Ref: MR-2710 20 Aug 2019: Selva
$(document).on('click', '.js-pending-claims', function () {
	var type = $('select[name="js-select-option"]').val();
    var page_url = $(this).attr('data-page-url');
	// Added condition for checkbox page listing selection
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        $.confirm({
            text: "Would you like to change the claims status to Pending?",
            confirm: function () {
				displayLoadingImage();
                if (type == 'page' || type == 'none') {
					selected_claim_ids = [];
					$('.js-select-all-sub-checkbox:checked').each(function () {
						selected_claim_ids.push($(this).attr('value'));
					});
				} else if (type == 'all') {
					selected_claim_ids = [];
					selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
				}else{
					selected_claim_ids = [];
					$('.js-select-all-sub-checkbox:checked').each(function () {
						selected_claim_ids.push($(this).attr('value'));
					});
				}
                $.ajax({
                    type: "GET",
                    datatype: 'JSON',
                    url: api_site_url + '/claims/pendingclaims',
                    data: '&claim_ids=' + selected_claim_ids,
                    success: function (result_values) {
						hideLoadingImage();
                        if (result_values['status'] == 'success') {
                            var message = result_values['message'];
                            js_alert_popup(message);
                            $("#patientnote_model button").addClass('js-claim');
                            $('#redirect_url').val(page_url);
                            return false;
                        } else {
                            js_sidebar_notification('error', result_values['message']);
                        }
						
                    }
                });
            }
        });
    } else {
        js_alert_popup('Select atleast one claim to do an action');
        //js_sidebar_notification('info', 'Select atleast one claim to do an action');
    }
    return false;
});

/*  Multi claim change pending status */

/* ** Ends - Pending claims ** */


/* ** Starts - Paper claims ** */ 
$(document).on('click', '.js-change-paper-click', function () {
    var page_url = $(this).attr('data-page-url');
    var claim_id = $(this).attr('data-claim-id');
    selected_claim_ids = [];
    selected_claim_ids.push(claim_id);
    $.confirm({
        text: "Would you like to change the claims status to Paper?",
        confirm: function () {
            $.ajax({
                type: "GET",
                datatype: 'JSON',
                url: api_site_url + '/claims/paperclaims',
                data: '&claim_ids=' + selected_claim_ids,
                success: function (result_values) {
                    if (result_values['status'] == 'success') {
                        var message = result_values['message'];
                        js_alert_popup(message);
                        $("#patientnote_model button").addClass('js-claim');
                        $('#redirect_url').val(page_url);
                        return false;
                    } else {
                        js_sidebar_notification('error', result_values['message']);
                    }
                }
            });
        }
    });
    return false;
});

//All list selection not working for Pending and Paper Status
//Revision 1 - Ref: MR-2710 20 Aug 2019: Selva
$(document).on('click', '.js-paper-claims', function () {
	
	var type = $('select[name="js-select-option"]').val();
    var page_url = $(this).attr('data-page-url');
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        $.confirm({
            text: "Would you like to change the claims status to Paper?",
            confirm: function () {
				displayLoadingImage();
                if (type == 'page' || type == 'none') {
					selected_claim_ids = [];
					$('.js-select-all-sub-checkbox:checked').each(function () {
						selected_claim_ids.push($(this).attr('value'));
					});
				} else if (type == 'all') {
					selected_claim_ids = [];
					selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
				}
                $.ajax({
                    type: "GET",
                    datatype: 'JSON',
                    url: api_site_url + '/claims/paperclaims',
                    data: '&claim_ids=' + selected_claim_ids,
                    success: function (result_values) {
						hideLoadingImage();
                        if (result_values['status'] == 'success') {
                            var message = result_values['message'];
                            js_alert_popup(message);
                            $("#patientnote_model button").addClass('js-claim');
                            $('#redirect_url').val(page_url);
                            return false;
                        } else {
                            js_sidebar_notification('error', result_values['message']);
                        }
						
                    }
                });
            }
        });
    } else {
        js_alert_popup('Select atleast one claim to do an action');
        //js_sidebar_notification('info', 'Select atleast one claim to do an action');
    }
    return false;
});
/* ** Ends - Paper claims ** */

/* ** Starts - Electronic claims ** */

/*  Per Claims change paper to electronic  */
$(document).on('click', '.js-change-electronic-click', function () {
    var page_url = $(this).attr('data-page-url');
    var claim_id = $(this).attr('data-claim-id');
    selected_claim_ids = [];
    selected_claim_ids.push(claim_id);
    $.confirm({
        text: "Would you like to change the claims status to Electronic?",
        confirm: function () {
            $.ajax({
                type: "GET",
                datatype: 'JSON',
                url: api_site_url + '/claims/electronicclaims',
                data: '&claim_ids=' + selected_claim_ids,
                success: function (result_values) {
                    if (result_values['status'] == 'success') {
                        var message = result_values['message'];
                        js_alert_popup(message);
                        $("#patientnote_model button").addClass('js-claim');
                        $('#redirect_url').val(page_url);
                        return false;
                    } else {
                        js_sidebar_notification('error', result_values['message']);
                    }
                }
            });
        }
    });
});

/*  Per Claims change paper to electronic  */


/*  multi claims change paper to electronic  */
$(document).on('click', '.js-electronic-claims', function () {
	var type = $('select[name="js-select-option"]').val();
    var page_url = $(this).attr('data-page-url');
    $('.js-select-all-sub-checkbox:disabled').each(function () {
        $(this).prop('checked', false);
    });
    if ($('.js-select-all-sub-checkbox:checked').length > 0) {
        $.confirm({
            text: "Would you like to change the claims status to Electronic?",
            confirm: function () {
                 if (type == 'page' || type == 'none') {
					selected_claim_ids = [];
					$('.js-select-all-sub-checkbox:checked').each(function () {
						selected_claim_ids.push($(this).attr('value'));
					});
				} else if (type == 'all') {
					selected_claim_ids = [];
					selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
				}
                $.ajax({
                    type: "GET",
                    datatype: 'JSON',
                    url: api_site_url + '/claims/electronicclaims',
                    data: '&claim_ids=' + selected_claim_ids,
                    success: function (result_values) {
                        if (result_values['status'] == 'success') {
                            var message = result_values['message'];
                            js_alert_popup(message);
                            $("#patientnote_model button").addClass('js-claim');
                            $('#redirect_url').val(page_url);
                            return false;
                        } else {
                            js_sidebar_notification('error', result_values['message']);
                        }
                    }
                });
            }
        });
    } else {
        js_alert_popup('Select atleast one claim to do an action');
        //js_sidebar_notification('info', 'Select atleast one claim to do an action');
    }
    return false;
});

/*  multi claims change paper to electronic  */

/* ** Ends - Electronic claims ** */

// $(document).on('ifToggled change', "input[name='archive_list']", function () {
//     var list_page = 'non_archive_list';
//     if ($(this).prop('checked') == true) {
//         var list_page = 'archive_list';
//     }
//     // var data = 'list_page=' + list_page + '&res_option=list';
//     var data ={res_option:'list', list_page:list_page};
//     getData(data);
// });

$(document).on('click change', '#edireport_make_read, #edireport_make_unread, #edireport_move_archive, #edireport_move_unarchive', function () {
    var list_page = $("#list_page_type").val();
    var res_option = $(this).attr('id');
    var edi_id_values = $('input:checkbox:checked.js_sel_edireport_ids').map(function () { return this.value; }).get();
    if (edi_id_values.length === 0) {
        js_alert_popup("Select atleast one record to do an action");
    }
    else {
        var alrt_msg = "";
        if (res_option == "edireport_make_read")
            alrt_msg = 'Do you want to make as read?';
        else if (res_option == "edireport_make_unread")
            alrt_msg = 'Do you want to make as unread?';
        else if (res_option == "edireport_move_archive")
            alrt_msg = 'Do you want to archive?';
        else if (res_option == "edireport_move_unarchive")
            alrt_msg = 'Do you want to retrieve from archive?';
        var selected_edi_id_values = edi_id_values.toString();
        $('#js_confirm_patient_demo_info_content1').html(alrt_msg);
        $("#js_confirm_patient_demo_info_box1")
            .modal({ show: 'false', keyboard: false })
            .one('click', '.js_modal_confirm1', function (eve) {
                var conformation1 = $(this).attr('id');
                if (conformation1 == "true") {
                    var data ={res_option: res_option, list_page:list_page, selected_edi_id_values:selected_edi_id_values};
                    getData(data);
                }
            });
    }
});

$(document).on('click', '.js-edi_reportdelete-confirm', function () {
    var curr_id = $(this).attr('id');
    var curr_id_arr = curr_id.split('_');
    var ediid = curr_id_arr[1];
    var list_page = $("#list_page_type").val();
    var alrt_msg = "Are you sure would you like to delete?";
    $('#js_confirm_patient_demo_info_content1').html(alrt_msg);
    $("#js_confirm_patient_demo_info_box1")
        .modal({ show: 'false', keyboard: false })
        .one('click', '.js_modal_confirm1', function (eve) {
            var conformation1 = $(this).attr('id');
            if (conformation1 == "true") {
                var data ={res_option:'deleteedi', list_page:list_page, edi_id:edi_id};
                // var data = 'res_option=deleteedi&list_page=' + list_page + '&ediid=' + ediid;
                getData(data);
                // getedireportlist(data);
                js_alert_popup("Deleted successfully");
            }
        });
});

function getedireportlist(data) {
	displayLoadingImage();
    $.ajax({
        type: "GET",
        url: api_site_url + '/claims/status_edireports',
        data: data,
        success: function (res) {
            $('.jsremovealltab').remove();
            $('.jsremovealltab_details').remove();
            $("#edi_report_list_part").html(res);
            $('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
			setTimeout(function(){ 
				
				$('#claims_table_edi').DataTable({
					"paging": true,
					"lengthChange": false,
					"searching": true,
					"ordering": true,
					"info": true,
					"responsive": true,
					"order": [1, 'asc'],
					"columnDefs": [{ "orderable": false, "targets": 0 }]
				});
				hideLoadingImage();
			}, 1000);
        }
    });
}

$(document).on('click', '#js_generate_report', function () {
    js_alert_popup('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing');
    $.ajax({
        type: "GET",
        datatype: 'JSON',
        url: api_site_url + '/claims/generateedireports',
        success: function (result_values) {
            if (result_values['status'] == 'success') {
                js_alert_popup(result_values['message']);
                $("#patientnote_model button").addClass('js-claim');
                $('#redirect_url').val('claims/edireports');
                return false;
            } else {
                js_alert_popup(result_values['message']);
            }
        }
    });
    return false;
});

$(document).on('click', "#js-edireport-view-tab", function () {
    var edireport_id_values = $('input:checkbox:checked.js_sel_edireport_ids').map(function () { return this.value; }).get();
    var def_open_claim_tab = '';
    if (edireport_id_values.length === 0) {
        js_alert_popup("Select any one report");
    }
    else if (edireport_id_values.length > 5) {
        js_alert_popup("Maximum five only allowed");
    }
    else {
        var selected_edireport_id_values = edireport_id_values.toString();
        var prev_sel_edireport_id_values = $('#selected_edireport_ids_arr').val();
        var data = "selected_edireport_id_values=" + selected_edireport_id_values + "&prev_sel_edireport_id_values=" + prev_sel_edireport_id_values;
        $.ajax({
            url: api_site_url + '/claims/getedireporttabdetails',
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            },
            type: "POST",
            data: data,
            success: function (res) {
                var result = res.split('^^::^^');
                var added_edireport_tabs = result[0];
                var remove_edireport_tabs = result[1];
                var edireport_tab_list = result[2];
                var added_edireport_tab_details = result[3];
                if (edireport_tab_list != '') {
                    var edireport_tab_list_arr = edireport_tab_list.split(',');
                    var append_data = '';
                    var scount = 1;
                    $.each(edireport_tab_list_arr, function (key, data) {
                        var datakey_arr = data.split('-::^^-');
                        append_data += '<li class="jsremovealltab js-edireport-tab-info_' + datakey_arr[1] + '"><a href="javascript:void(0);"><span id="edireportdetlink_' + datakey_arr[1] + '" class="js_edireportdetlink"><i class="fa fa-navicon i-font-tabs"></i>&nbsp' + datakey_arr[0].substring(0, 10) + '</span>&nbsp&nbsp<i style="cursor: pointer;" id="remove_edireport_id-' + datakey_arr[1] + '" class="js-remove-edireport-tab fa fa-times pull-right"></i></a></li>';
                        if (scount == 1) {
                            def_open_edireport_tab = datakey_arr[1];
                        }
                        scount++;
                    });
                    $(".js-dynamic-tab-menu ul").append(append_data);
                }
                if (remove_edireport_tabs != '') {
                    var remove_edireport_tabs_arr = remove_edireport_tabs.split(',');
                    $.each(remove_edireport_tabs_arr, function (key, data) {
                        $('.js-edireport-tab-info_' + data).remove();
                        $('#edireport-tab-info_' + data).remove();
                    });
                }
                $('.js-edireport-dyanamic-tab').append(added_edireport_tab_details);
                window.location.hash = '';
                history.pushState('', document.title, window.location.pathname);
                $('#selected_edireport_ids_arr').val(selected_edireport_id_values);
                if (def_open_edireport_tab != '') {
                    $('.js-edireport-tab-info_' + def_open_edireport_tab).parents('ul').find("li").removeClass('active');
                    $(".tab-content").find("div").removeClass('active');
                    $('.js-edireport-tab-info_' + def_open_edireport_tab).closest("li").addClass('active');
                    $("#edireport-tab-info_" + def_open_edireport_tab).addClass('active');
                    $('#selected_curr_edireport_id').val(def_open_edireport_tab);
                }
            }
        });
    }
});

$(document).on('click', ".js_edireportdetlink", function (e) {
    var curr_id = $(this).attr('id');
    var curr_edireport_val_arr = curr_id.split('_');
    $(this).parents('ul').find("li").removeClass('active');
    $(".tab-content").find("div").removeClass('active');
    $(this).closest("li").addClass('active');
    $("#edireport-tab-info_" + curr_edireport_val_arr[1]).addClass('active');
    $('#selected_curr_edireport_id').val(curr_edireport_val_arr[1]);
});

$(document).on('click', '.js-remove-edireport-tab', function () {
    var id = $(this).attr('id');
    var selected_curr_edireport_id = $('#selected_curr_edireport_id').val();
    $("#js_confirm_arlist_remove")
        .modal({ show: 'false', keyboard: false })
        .one('click', '.js_modal_confirm', function (e) {
            var conformation = $(this).attr('id');
            if (conformation == "true") {
                var id_val = id.split('-');
                var prev_edireport_cls = $('.js-edireport-tab-info_' + id_val[1]).prev('li').attr('class');
                $('.js-edireport-tab-info_' + id_val[1]).remove();
                $('#edireport-tab-info_' + id_val[1]).remove();
                $('input[name="edireport_ids[]"]:checkbox[value="' + id_val[1] + '"]').prop("checked", false);
                $('input[name="edireport_ids[]"]:checkbox[value="' + id_val[1] + '"]').iCheck('update');
                $('input[name="selectall"]:checkbox').prop("checked", false);
                if (selected_curr_edireport_id == id_val[1]) {
                    if (prev_edireport_cls != '') {
                        var prv_edireport_arr = prev_edireport_cls.split('_');
                        var prv_edireport_num = prv_edireport_arr[1];
                        $('.js-edireport-tab-info_' + prv_edireport_num).parents('ul').find("li").removeClass('active');
                        $(".tab-content").find("div").removeClass('active');
                        $('.js-edireport-tab-info_' + prv_edireport_num).closest("li").addClass('active');
                        $("#edireport-tab-info_" + prv_edireport_num).addClass('active');
                        $('#selected_curr_edireport_id').val(prv_edireport_num);
                    }
                    else {
                        $('#edireportdetlink_main0').parents('ul').find("li").removeClass('active');
                        $(".tab-content").find("div").removeClass('active');
                        $('#edireportdetlink_main0').closest("li").addClass('active');
                        $("#edireport-tab-info_main0").addClass('active');
                    }
                }
                var prev_selected_values = $('#selected_edireport_ids_arr').val();
                if (prev_selected_values != '') {
                    var prev_selected_values_arr = prev_selected_values.split(',');
                    prev_selected_values_arr = jQuery.grep(prev_selected_values_arr, function (value) {
                        return value != id_val[1];
                    });
                    prev_selected_values_arr.join(",");
                    $('#selected_edireport_ids_arr').val(prev_selected_values_arr);
                }
            }
        });
});

$(document).on('ifToggled change', "input[name='edireport_ids[]']", function () {
    var remove_edireport = $(this).val();
    if ($(this).prop('checked') == false) {
        var prev_selected_values = $('#selected_edireport_ids_arr').val();
        if (prev_selected_values != '') {
            var prev_selected_values_arr = prev_selected_values.split(',');
            if ($.inArray(remove_edireport, prev_selected_values_arr) != -1) {
                $("#js_confirm_arlist_remove")
                    .modal({ show: 'false', keyboard: false })
                    .one('click', '.js_modal_confirm', function (e) {
                        var conformation = $(this).attr('id');
                        if (conformation == "true") {
                            $('.js-edireport-tab-info_' + remove_edireport).remove();
                            $('#edireport-tab-info_' + remove_edireport).remove();
                            prev_selected_values_arr = jQuery.grep(prev_selected_values_arr, function (value) {
                                return value != remove_edireport;
                            });
                            prev_selected_values_arr.join(",");
                            $('#selected_edireport_ids_arr').val(prev_selected_values_arr);
                        }
                        else {
                            setTimeout(function () {
                                $('input[name="edireport_ids[]"]:checkbox[value="' + remove_edireport + '"]').prop("checked", true);
                                $('input[name="edireport_ids[]"]:checkbox[value="' + remove_edireport + '"]').iCheck('update');
                            }, 20);
                        }
                    });
            }
        }
    }
});
$(document).delegate('a[data-target=#js-model-popup-payment]', 'click', function () {

    $("#js-model-popup-payment .modal-title").html("");
    $("#js-model-popup-payment .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner 
    var target1 = $(this).attr("data-url");
    var claim_no = $(this).attr('claim_number');
    $("#js-model-popup-payment .modal-body").load(target1, function () {
        $("#js-model-popup-payment .modal-title").html("Claim No :" + claim_no);
    });

});

$(document).on('click', '.js_claim_search', function () {
    $('#js_claim_search_option').removeClass('hide');
});

/*$("#js-search_form").submit(function(){
    searchClaims();
    return false;
}); */

$('#js-search_form').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: '',
        invalid: '',
        validating: 'glyphicon glyphicon-refresh'
    },
    excluded: ':disabled, :hidden, :not(:visible)',
    fields: {
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
                        var response = searchStartDate(value, end_date);
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
                        var response = searchEndDate(start_date, end_date);
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
        }
    }
}).on('success.form.bv', function (e) {
    e.preventDefault();
    searchClaims()
    $('#js-search_form').bootstrapValidator('disableSubmitButtons', false);
});

$(document).on('keyup change', '#billed_option', function () {
    fv = $('#js-search_form').data('bootstrapValidator');
    if ($(this).val() != '') {
        fv.enableFieldValidators('billed', true).revalidateField('billed');
    }
    else {
        fv.enableFieldValidators('billed', false).revalidateField('billed');
    }
});

$(document).on('keyup', '#billed', function () {
    fv = $('#js-search_form').data('bootstrapValidator');
    if ($(this).val() != '') {
        fv.enableFieldValidators('billed_option', true).revalidateField('billed_option');
    }
    else {
        fv.enableFieldValidators('billed_option', false).revalidateField('billed_option');
    }
});


function searchClaims() {
    var myform = $('#js-search_form');
    var serialized = myform.serialize();
    //console.log("serialized");
    //console.log(serialized);
    var url = $("#js_search_option_url").val();
    //console.log("serialized url");
    //alert(url); return false;
    processingImageShow("#js-table_listing", "show");
    $.ajax({
        //dataType: 'json',
        url: url,
        type: 'POST',
        data: serialized,
        success: function (result_values) {

            processingImageShow("#js-table_listing", "hide");
            $('#js-table_listing').html(result_values);
            $('#claims_table').DataTable({
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

$(document).on('click', '.js_search_reset_claim', function () {  // Changed the class name due to already exists on function.js and post was not worked well 
    $('input:text').val("");
    $(".select2").select2("val", "");
    $.AdminLTE.boxWidget.activate();
    $('#js-search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
    $('#js-search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
    $('#js-search_form').bootstrapValidator('disableSubmitButtons', false);
    searchClaims();
    return false;
});

/// Starts Date Validation (From and To Date) ///
function searchStartDate(start_date, end_date) {
    /*if (start_date == '') {
        return start_date_req_lang_err_msg;
    }*/
    var date_format = new Date(end_date);
    if (end_date != '' && date_format != "Invalid Date") {
        return (start_date == '') ? start_date_req_lang_err_msg : true;
    }
    return true;
}
function searchEndDate(start_date, end_date) {
    /*if (end_date == '') {
        return end_date_req_lang_err_msg;
    }*/
    var eff_format = new Date(start_date);
    var ter_format = new Date(end_date);
    if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
        var getdate = searchDaydiff(parseDate(start_date), parseDate(end_date));
        return (getdate >= 0) ? true : end_date_val_lang_err_msg;
    }
    else if (start_date != '' && eff_format != "Invalid Date") {
        return (end_date == '') ? end_date_req_lang_err_msg : true;
    }
    return true;
}
function searchDaydiff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}
/// Ends Date Validation (From and To Date) ///


$(document).on('click', '.unread_status', function () {
    $(this).closest("tr").removeClass();
});


$(document).on('click', '#js_generate_rejection_report', function () {

    js_alert_popup('');
    $('.nav.nav-list.line-height-26').html('');
    $('h4.modal-title').text('Rejection Report');
    $('.nav.nav-list.line-height-26').append('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Generating EDI Rejection Report');

    $.ajax({
        type: "GET",
        datatype: 'JSON',
        url: api_site_url + '/clearing-house-response',
        success: function (result_values) {

            if (result_values['status'] == 'success') {
                $.ajax({
                    type: "GET",
                    datatype: 'JSON',
                    url: api_site_url + '/clearing-house-edi-response',
                    success: function (result_valuess) {
                        js_alert_popup('');
                        $('.nav.nav-list.line-height-26').html('');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange font600 bg-f0fdfc margin-t-10">EDI Status Summary: </div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Files </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['file_count'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_count'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600r">Total Claims Accepted </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_accpet'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Rejected </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_reject'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange font600 bg-f0fdfc margin-t-10">Payer Response Summary:</div>');

                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Files </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_valuess['file_count'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_valuess['claim_count'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Accepted </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_valuess['claim_accpet'] + '</div>');
                        $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Rejected </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_valuess['claim_reject'] + '</div>');
                        $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').addClass('clearing_house_response');
                        return false;
                    }
                });
            } else {
                $('.nav.nav-list.line-height-26').html('');

                $('.nav.nav-list.line-height-26').append('<div class=" med-orange col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding bg-f0fdfc"><div class="med-orange col-lg-2 col-md-2 col-sm-2 col-xs-2 med-green font600">Note:  </div><div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 red">' + result_values['message'] + '</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange font600 bg-f0fdfc margin-t-10">EDI Status Summary:</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Files </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['file_count'] + '</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_count'] + '</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Accepted</div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_accpet'] + '</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Rejected </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">' + result_values['claim_reject'] + '</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange font600 bg-f0fdfc margin-t-10">Payer Response Summary:</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Files  </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">0</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims  </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">0</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Accepted </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">0</div>');

                $('.nav.nav-list.line-height-26').append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 med-green font600">Total Claims Rejected  </div><div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">0</div>');
                // $('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').addClass('clearing_house_response');
            }
        }
    });
    return false;
});

$(document).on('click', '.clearing_house_response', function () {
    location.reload();

});








