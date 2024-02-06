$(document).on('click', '.pagination li a', function (e) {
    e.preventDefault();
    var pagination = $(this).attr("href").split('page=');
    var get_form_id = $('.js_filter_search_submit').parents('form').attr("id");
    var dataurl = $('#' + get_form_id).attr("data-url");
    var form_data = $("#" + get_form_id).serialize();
    var form_action = $("#" + get_form_id).attr('action');
    var get_site_url = pagination[0].split("search/"); 
    if (typeof dataurl != 'undefined') {
        var url = api_site_url + '/' + dataurl + '/pagination?page=' + pagination[1];
    } else {
		// console.log(get_page);
        var get_page = (typeof get_site_url[1] !== 'undefined') ? get_site_url[1].split("/") : get_site_url;
        var url = api_site_url + '/reports/search/' + get_page[0] + '/pagination?page=' + pagination[1];
    }
    // for MR-2591 issue (create form data when pagination)
    var pagination_prt = '';
    if($('#pagination_prt').val() == 'string'){
        var pagination_prt = $('#pagination_prt').val();
        var data_arr = '';
        $('select.auto-generate:visible').each(function () {
            data_arr += $(this).attr('name') + '=' + $(this).select2('val') + '&';
        });
        $('input.auto-generate:visible').each(function () {
            data_arr += $(this).attr('name') + '=' + $(this).val() + '&';
        });

        form_data = data_arr + "_token=" + $('input[name=_token]').val();
    }
    getAjaxResponse(url, form_data);
});

$(document).on('click', '#js_exit_part', function (e) {
    var form_id_val = $("form").attr('id');
    $('#' + form_id_val).data("bootstrapValidator").resetForm();
    $('#js_ajax_part').removeClass("hide");
    $('.js_claim_list_part').html("");
    $('.js_claim_list_part,.js_exit_part').addClass("hide");
    checkTableListForExport();
});

$(document).on('click', '#js_exit_part_report', function (e) {
    var form_id_val = $("form").attr('id');
    //$('#'+form_id_val)[0].reset();
   // $('#' + form_id_val).data("bootstrapValidator").resetForm();
    $('#js_ajax_part').removeClass("hide");
    $('.js_claim_list_part').html("");
    $('.js_claim_list_part,.js_exit_part').addClass("hide");
    checkTableListForExport();
});

$(document).on('ifToggled change', '.js_select_basis_change', function () {
    var get_current_id = $(this).val();
    $('.js_' + get_current_id + "_aging.select2").select2("enable", false);
    $('.js_all_hide_col').addClass("hide");
    $('.js_' + get_current_id + "_aging").removeClass("hide");
    if (get_current_id != "all")
        $('.js_' + get_current_id + "_aging.select2").select2("enable", true);
});

$(document).on('ifToggled change', '.js_select_basis_change_adjusment', function () {
    var get_current_id = $(this).val();
    $('.js_all_hide_col').addClass("hide");
    $('.js_' + get_current_id + "_aging").removeClass("hide");
    if (get_current_id == "insurance") {
        $('.js_' + get_current_id + "_aging.select2").select2("enable", true);
        $('.js_patient_aging.select2').select2("enable", false);
    } else if (get_current_id == "patient") {
        $('.js_' + get_current_id + "_aging.select2").select2("enable", true);
        $('.js_insurance_aging.select2').select2("enable", false);
    } else {
        $('.js_' + get_current_id + "_aging.select2").select2("enable", false);
    }
});

/* function for get data for fields Start */
function getData(){
	clearTimeout(wto);
	var data_arr = '';
	wto = setTimeout(function() {  
		$('select.auto-generate:visible').each(function(){
			if($(this).select2('val'))
				data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';           
		});                                                    
		$('input.auto-generate:visible').each(function(){
			if($(this).val())
				data_arr += $(this).attr('name')+'='+$(this).val()+'&';
		});
		
		final_data = data_arr+"_token="+$('input[name=_token]').val(); 
		getAjaxResponse(url, final_data);
	}, 100);
}
/* function for get data for fields End */
//var alertCount = 0;
function getAjaxResponse(url, form_data) {
    //$(".js_spin_image").removeClass("hide");
    //$(".js_claim_list_part").html('');
	/* var last = url.substring(url.lastIndexOf("/") + 1, url.length);
	if(alertCount == 0){
		
		if(last == 'filter_result' || last == 'financial')
			alert("Your report is being generated! Please note the following:\n\n"+' • This report provides data by Transaction Date only.');
		
		if(last == "filter_result_summary" || last == "filter_insurance_result" || last == "filter_resultProvider")
			alert("Your report is being generated! Please note the following:\n\n"+' • If search filter "Include Refund" is Yes, total payments will include refund deductions.');
		
		if(last == 'monthend' || last == 'charges_payments' || last == 'patientInsurancePayment')
			alert("Your report is being generated! Please note the following:\n\n"+' • Totals from reports generated using DOS may not be equal to consolidated reports such as "Year End Financials" and "End of the Day Totals". Since consolidated reports use only transaction date. \n \n'+' • If search filter "Include Refund" is Yes, total payments will include refund deductions.');
		
		if(last == 'charges' || last == 'filter_unbilled' || last == 'workrvureport' || last == 'chargecategoryreport' || last == 'chargelistreport' || last == 'aginganalysis' || last == 'aginganalysisdetails' || last == 'payments' || last == 'adjustment' || last == 'procedurereport')
			alert("Your report is being generated! Please note the following:\n\n"+' • Totals from reports generated using DOS may not be equal to consolidated reports such as "Year End Financials" and "End of the Day Totals". Since consolidated reports use only transaction date.');
	}
	alertCount++; */
    processingImageShow("#js_ajax_part","show");
    $(".js_claim_list_part").html('');
    $(".js_exit_part").addClass("hide");
	$.ajax({
		type: 'POST',
		url: url,
		data: form_data,
		success: function (response) {
			//$(".js_spin_image").addClass("hide");
			processingImageShow("#js_ajax_part","hide");
			$(".js_claim_list_part").html(response).removeClass("hide");
			//$("#js_ajax_part").addClass("hide");
			$(".js_exit_part").removeClass("hide");
			if(url !== typeof undefined)
				var page_name = url.split("/").pop();
			if (page_name == "payments")
				$("#list_noorder").dataTable({"paging": true, "iDisplayLength": 25, "info": true, "lengthChange": false, "searching": false, "aaSorting": [], "scrollX": true});
			else if (page_name == "financial")
				$("#example").dataTable({"paging": false, "info": false, "lengthChange": false, "searching": false, "ordering": false});
			else
				$("#list_noorder").dataTable({"paging": true, "iDisplayLength": 25, "info": true, "lengthChange": false, "searching": false, "aaSorting": []});

			$("#sort_list_noorder").DataTable({
				"aaSorting": [],
				"bSort" : false,
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": true,
				"bInfo": false,
				"bAutoWidth": false,
				//"scrollX": true,
				"responsive": true,
				"searching": false
			});
			$(".js_filter_search_submit").prop("disabled",false);
			checkTableListForExport();
		  //  $.AdminLTE.boxWidget.activate();
			//openNewReportTab();
			//alertCount = 0;
		}
	});
}

function storeReportSessionData(form_data) {
	var url = api_site_url+"/reports/setreportsessiondata";
	$.ajax({
		type: 'POST',
		url: url,
		data: form_data,
		success: function (response) {
			if(response.status == 'success'){
				openNewReportTab();
			}
		}
	});
}

function openNewReportTab(){
	var tab_url = api_site_url+"/reports/financials/adjustments";
	window.open(tab_url);
}
/*** Common Export with searched critiria start ***/
/** Commented since not going to use
$(document).on('click', '.js_search_export', function (e) {
    //e.preventDefault();    
    var get_form_id = $(".js_filter_search_submit").parents('form').attr("id");
    var url = $("#" + get_form_id).attr("action");
    var active_url = /[^/]*$/.exec(url)[0];
    $(this).attr('href', api_site_url + '/reports/' + active_url + '/export/' + $(this).attr('data-option') + '?' + $("#" + get_form_id).serialize());
    removeHash();
});
*/
/*** Common Export with searched critiria end ***/

/*** Outstanding AR Claim reports start ***/
$(document).on('change', '.js_group_by', function (e) {
    $('#js-bootstrap-searchvalidator').data("bootstrapValidator").resetForm();
    if ($(this).val() == 'all') {
        $('.search_start_date,.search_end_date').val('');
        $('.search_start_date,.search_end_date').attr('disabled', true);
    } else {
        $('.search_start_date,.search_end_date').attr('disabled', false);
    }
});
/*** Outstanding AR Claim reports end ***/

/*** payment reports start ***/
$(document).on('ifToggled click', '[name="date_option"]', function () {
    var get_current_id = $(this).attr("id");
    $(".js_date_option").addClass("hide");
    $("." + get_current_id).removeClass("hide");
});

$(document).on('ifToggled change', '.js_payment_type', function () {
    $(".js_payment_part label").removeClass("med-green").addClass("med-gray");
    $('.js_payment_part input[type="checkbox"]').prop("checked", false).attr("disabled", true);
    var current_value = $(this).val();
    $('.js_' + current_value + '_payment').parent("div").next("label").addClass("med-green").removeClass("med-gray");
    $('.js_' + current_value + '_payment').prop("checked", false).attr("disabled", false);
    $('input[type="checkbox"].flat-red').iCheck("update");
});
/*** payment reports end ***/

/*** Date function check start here ***/
$(document).on('keyup change', '.search_start_date,.search_end_date', function () {
    var get_parent_id = $(this).parents(".js_date_validation").attr("id");
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .search_start_date'));
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .search_end_date'));
    setTimeout(function () {
        if ($(".has-error").length == 0)
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
        else
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', true);
    }, 50); //Getting reinitialized confirm popup 
});

$(document).on('ifToggled change', '.js_changedate_validation', function () {
    var get_parent_id = $(this).parents(".js_date_validation").attr("id");
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .search_start_date'));
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .search_end_date'));
    setTimeout(function () {
        if ($(".has-error").length == 0)
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
        else
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', true);
    }, 50); //Getting reinitialized confirm popup 
});

$(document).on('keyup change', '.app_search_start_date,.app_search_end_date', function () {
    var get_parent_id = $(this).parents(".js_date_validation").attr("id");
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .app_search_start_date'));
    $('#js-bootstrap-searchvalidator').bootstrapValidator('revalidateField', $("#" + get_parent_id + ' .app_search_end_date'));
    setTimeout(function () {
        if ($(".has-error").length == 0)
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
        else
            $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', true);
    }, 50); //Getting reinitialized confirm popup 
});

$(document).on('change', '.js_change_date_option', function (e) {
    var current_val = $(this).val();
    if (current_val == "enter_date") {
        var str_date = '';
        var end_date = '';
    } else {
        var str_date = getStartDate(current_val);
        var end_date = getEndDate(current_val);
    }
    if ($(".search_start_date").length > 0) {
        $(".search_start_date").val(str_date);
        $(".hidden_search_from_date").val(str_date);
        $(".hidden_search_to_date").val(end_date);
        $(".search_end_date").val(end_date);
        $(".search_end_date").trigger("keyup");
        if (current_val != "enter_date") {
            $(".search_start_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
            $(".search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
        } else {
            $(".search_start_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
            $(".search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
        }
    }
    else if ($(".app_search_start_date").length > 0) {
        $(".app_search_start_date").val(str_date);
        $(".app_search_end_date").val(end_date);
        $(".app_search_end_date").trigger("keyup");
        if (current_val != "enter_date") {
            $(".app_search_start_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
            $(".app_search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
        } else {
            $(".app_search_start_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
            $(".app_search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
        }

    }
});

function getStartDate(date_option) {

    var d = new Date();
    switch (date_option) {
        case "daily":
            var strDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;
        case "today":
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
        case "last_month":
            var date = new Date(d.getFullYear(), (d.getMonth() - 1), 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;
        case "previous_year":
            var date = new Date(d.getFullYear() - 1, 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;
        case "prev_year":
            var date = new Date(d.getFullYear() - 1, 0, 1);
            var strDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;
        default:
            d = new Date(date_option, 0, 1);
            var strDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
    }
    return MakeDate(strDate);
}

function getEndDate(date_option) {
    var d = new Date();
    switch (date_option) {
        case "daily":
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
            break;
        case "today":
            var endDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
            break;
        case "current_month":
            var endDate = (d.getMonth() + 1) + "/" + (d.getDate()) + "/" + d.getFullYear();
            break;
        case "last_month":
            var date = new Date(d.getFullYear(), (d.getMonth()), 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
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
        case "prev_year":
            var date = new Date(d.getFullYear(), 0, 0);
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + date.getFullYear();
            break;
        default:
            d = new Date(date_option, 0, 1);
            var date = new Date(d.getFullYear(), (d.getMonth()), 0);
            //var endDate = (d.getMonth()+1) + "/" + d.getDate()+ "/" +d.getFullYear();
            var endDate = (date.getMonth() + 1) + "/" + (date.getDate()) + "/" + (date.getFullYear() + 1);
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

$(function () {
    $(".search_start_date").datepicker({
        maxDate: 0,
        changeMonth: true,
        changeYear: true,
        dateFormat: "mm/dd/yy",
    });
    $(".search_end_date").datepicker({
        maxDate: 0,
        changeMonth: true,
        changeYear: true,
        dateFormat: "mm/dd/yy",
    });

    $(".app_search_start_date").datepicker({
        maxDate: 900,
        changeMonth: true,
        changeYear: true,
        dateFormat: "mm/dd/yy",
    });
    $(".app_search_end_date").datepicker({
        maxDate: 900,
        changeMonth: true,
        changeYear: true,
        dateFormat: "mm/dd/yy",
    });
});

$('#js-bootstrap-searchvalidator')
	.bootstrapValidator({
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
			'year_type': {
				validators: {
					notEmpty: {
						message: 'Select Year'
					}
				}
			},
			'select_year': {
				validators: {
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							if (value.length != 4 && value != "") {
								return {
									valid: false,
									message: 'Enter valid Year'
								};
							}
							else if (value == "") {
								return {
									valid: false,
									message: 'Enter Year'
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
			},
			app_search_start_date: {
				selector: '.app_search_start_date',
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
							var end_date = $("#" + get_field + " .app_search_end_date").val();
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
			app_search_end_date: {
				message: '',
				selector: '.app_search_end_date',
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
							var start_date = $("#" + get_field + " .app_search_start_date").val();
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
		var get_form_id = $('form').attr("id");
		var disabled  = $("#" + get_form_id).find(':input:disabled').removeAttr('disabled');
		var form_data = $("#" + get_form_id).serialize();
		var url = $('form').attr("action");
		var formClass = $('form').attr("class");
		disabled.attr('disabled','disabled');
		var searchFrom = $("input[name='searchFrom']").val()
		if(searchFrom == 'reportPageList') {
			storeReportSessionData(form_data);
		}else{
			getAjaxResponse(url, form_data);
		}
		$('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
	});


$('#js-bootstrap-searchvalidator_provider')
	.bootstrapValidator({
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
			'year_type': {
				validators: {
					notEmpty: {
						message: 'Select Year'
					}
				}
			},
			'select_year': {
				validators: {
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							if (value.length != 4 && value != "") {
								return {
									valid: false,
									message: 'Enter valid Year'
								};
							}
							else if (value == "") {
								return {
									valid: false,
									message: 'Enter Year'
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
			},
			rendering_provider: {
				message: '',
				trigger: 'change',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							rendering_provider = $('select[name="rendering_provider"]').val();
							billing_provider = $('select[name="billing_provider"]').val();
							if (rendering_provider == '' && billing_provider == '') {
								return {
									valid: false,
									message: 'Select Provider'
								};
							}
							return true;
						}
					}
				}
			},
			billing_provider: {
				message: '',
				trigger: 'change',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							rendering_provider = $('select[name="rendering_provider"]').val();
							billing_provider = $('select[name="billing_provider"]').val();
							if (rendering_provider == '' && billing_provider == '') {
								return {
									valid: false,
									message: 'Select Provider'
								};
							}
							return true;
						}
					}
				}
			},
			app_search_start_date: {
				selector: '.app_search_start_date',
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
							var end_date = $("#" + get_field + " .app_search_end_date").val();
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
			app_search_end_date: {
				message: '',
				selector: '.app_search_end_date',
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
							var start_date = $("#" + get_field + " .app_search_start_date").val();
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
		var get_form_id = $('form').attr("id");
		var form_data = $("#" + get_form_id).serialize();
		var url = $('form').attr("action");
		getAjaxResponse(url, form_data);
		$('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
	});

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

/*** Date function check end here ***/

/*** Start to Refund module ***/
$(document).on('ifToggled click', '.js_refund_type:checked', function () {
    var get_value = $(this).val();
    $('.js_insurance_part input[type="checkbox"]').prop("checked", true);
    $('.js_insurance_part').removeClass("hide");
    if (get_value == 'patient') {
        $('.js_insurance_part').addClass("hide");
        $('.js_insurance_part input[type="checkbox"]').prop("checked", false);
    }
    $('input[type="checkbox"].flat-red').iCheck("update");
});
/*** End to Refund module ***/

/****   Billing reports module in end of the day total ****/
$(document).on('click', '.set_date_export', function () {    
    var get_form_id = $(".js_filter_search_submit").parents('form').attr("id");
    current_url = $(this).attr('href');   
    adding_url = '?'+$("#" + get_form_id).serialize(); 
    url = current_url.concat(adding_url);
 //   console.log(url);   
    $(this).attr('href', url);
});

$(document).on('click', '.set_date', function () {
    current_url = $(this).attr('href');
    start_date = $('input[name="from_date"]').val();
    end_date = $('input[name="to_date"]').val();
    adding_url = '?start-date=' + start_date + '&end-date=' + end_date;
    url = current_url.concat(adding_url);
    $(this).attr('href', url);
});

$(document).on('click', '.set_parameter', function () {
    current_url = $(this).attr('href');
    start_date = $('input[name="from_date"]').val();
    end_date = $('input[name="to_date"]').val();
    rendering_provider = $('input[name="rendering_provider"]').val();
    billing_provider = $('input[name="billing_provider"]').val();
    adding_url = '?start-date=' + start_date + '&end-date=' + end_date + '&rendering_provider=' + rendering_provider + '&billing_provider=' + billing_provider;
    url = current_url.concat(adding_url);
    $(this).attr('href', url);
});

$(document).on('click', '.set_payer', function () {
    current_url = $(this).attr('href');
    start_date = $('input[name="from_date"]').val();
    end_date = $('input[name="to_date"]').val();
    payer = $('select[name="payer"]').val();
    adding_url = '?start-date=' + start_date + '&end-date=' + end_date + '&payer=' + payer;
    url = current_url.concat(adding_url);
    $(this).attr('href', url);
});

$(document).on('change', '.js_change_date_option_edt', function (e) {
    var current_val = $(this).val();
    if (current_val == "enter_date") {
        var str_date = '';
        var end_date = '';
        $('input[name="hidden_from_date"]').val('');
        $('input[name="hidden_to_date"]').val('');
    } else {
        var str_date = getStartDate(current_val);
        var end_date = getEndDate(current_val);
    }
    if ($(".search_start_date").length > 0) {
        $(".search_start_date").val(str_date);
        $(".search_end_date").val(end_date);
        $(".search_end_date").trigger("keyup");
        if (current_val != "enter_date") {
            $(".search_start_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");
            $(".search_end_date").attr("disabled", 'disabled').removeClass("datepicker dm-date hasDatepicker");

            $('input[name="hidden_from_date"]').val(str_date);
            $('input[name="hidden_to_date"]').val(end_date);
            $('.set_date').attr('data-start_date', str_date);
            $('.set_date').attr('data-end_date', end_date);

            $('#js-bootstrap-searchvalidator_edt').data('bootstrapValidator').revalidateField('from_date');
        }
        else {
            $(".search_start_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
            $(".search_end_date").removeAttr("disabled").addClass("datepicker dm-date hasDatepicker");
            $('#js-bootstrap-searchvalidator_edt').data('bootstrapValidator').revalidateField('from_date');
        }
    }
});

/* function getStartDate(date_option) {
 var d = new Date();
 switch(date_option) {
 case "today":
 var strDate = (d.getMonth()+1) + "/" + (d.getDate()) + "/" +d.getFullYear();
 break;
 case "current_month":
 var date = new Date(d.getFullYear(), (d.getMonth()), 1);
 var strDate = (date.getMonth()+1) + "/" + (date.getDate()) + "/" +date.getFullYear();
 break;
 case "last_month":
 var date = new Date(d.getFullYear(), (d.getMonth()-1), 1);
 var strDate = (date.getMonth()+1) + "/" + (date.getDate()) + "/" +date.getFullYear();
 break;
 case "current_year":
 var date = new Date(d.getFullYear(), 0, 1);
 var strDate = (date.getMonth()+1) + "/" + (date.getDate()) + "/" +date.getFullYear();
 break;
 default:
 d = new Date(date_option,0,1);
 var strDate = (d.getMonth()+1) + "/" + d.getDate()+ "/" +d.getFullYear();
 }
 return MakeDate(strDate);
 } */
/* function getEndDate(date_option) {
 var d = new Date();
 switch(date_option) {
 case "today":
 var endDate = (d.getMonth()+1) + "/" + d.getDate()+ "/" +d.getFullYear();
 break;
 case "current_month":
 var endDate = (d.getMonth()+1) + "/" + (d.getDate())+ "/" +d.getFullYear();
 break;
 case "last_month":
 var date = new Date(d.getFullYear(), (d.getMonth()), 0);
 var endDate = (date.getMonth()+1) + "/" + (date.getDate()) + "/" +date.getFullYear();
 break;
 case "current_year":
 var endDate = (d.getMonth()+1) + "/" + (d.getDate())+ "/" +d.getFullYear();
 break;
 
 default:
 d = new Date(date_option,0,1);
 var date = new Date(d.getFullYear(), (d.getMonth()), 0);
 var endDate = (date.getMonth()+1) + "/" + (date.getDate()) + "/" +(date.getFullYear()+1);
 }
 return MakeDate(endDate);
 
 } */
function MakeDate(date_value) {
    var date = date_value.split("/");
    date[0] = ((date[0]) < 10) ? "0" + date[0] : date[0];
    date[1] = ((date[1]) < 10) ? "0" + date[1] : date[1];
    var return_date = date.join("/");
    return return_date;
}

/* Unbilled Report Start */

/* $(document).ready(function(){
$('#unbilled_form')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: '',
                invalid: '',
                validating: 'glyphicon glyphicon-refresh'
            },
            excluded: ':disabled, :hidden, :not(:visible)',
            fields: {
                transaction_date: {
                    trigger: 'keyup change',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {
								 if (value == '') {
									$('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', true);
                                    return {
                                        valid: false,
                                        message: 'Select Transaction Date'
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
    var get_form_id = $(this).closest('form').attr("id");
    var form_data = $("#" + get_form_id).serialize();
    var url = $(this).closest('form').attr("action");
    getAjaxResponse(url, form_data);
    $('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
});
	//$('#unbilled_form').data('bootstrapValidator').revalidateField('transaction_date');
}); */

/* $(document).on('click','.ranges',function(){
	$('#unbilled_form').data('bootstrapValidator').revalidateField('transaction_date');
}); */

/* Unbilled Report End */

$('#js-bootstrap-searchvalidator_edt')
	.bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		excluded: ' :hidden, :not(:visible)',
		fields: {
			from_date: {
				message: '',
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
							if (value == '') {
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
			to_date: {
				message: '',
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
		$('.set_date').attr('data-start_date', $('input[name="from_date"]').val());
		$('.set_date').attr('data-end_date', $('input[name="to_date"]').val());
		var get_form_id = $('form').attr("id");
		//console.log(get_form_id);
		var form_data = $("#" + get_form_id).serialize();
		var url = $('form').attr("action");
		getAjaxResponse(url, form_data);
		$('#js-bootstrap-searchvalidator_edt').bootstrapValidator('disableSubmitButtons', false);
	});

/*** CSELVA  For Starts report***/
$('#js-bootstrap-searchvalidator_provider_list')
	.bootstrapValidator({
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
			'year_type': {
				validators: {
					notEmpty: {
						message: 'Select Year'
					}
				}
			},
			'select_year': {
				validators: {
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							if (value.length != 4 && value != "") {
								return {
									valid: false,
									message: 'Enter valid Year'
								};
							}
							else if (value == "") {
								return {
									valid: false,
									message: 'Enter Year'
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
			},
			/*provider_type: {
			 message: '',
			 trigger: 'change',
			 validators: {
			 callback: {
			 message: '',
			 callback: function (value, validator,$field) {
			 
			 provider_type = $('select[name="provider_type"]').val();
			 if (provider_type == ''){
			 return {
			 valid: false,
			 message: 'Select Provider'
			 }; 
			 } 
			 return true;
			 }
			 
			 }
			 }
			 },
			 provider_type_group: {
			 message: '',
			 trigger: 'change',
			 validators: {
			 callback: {
			 message: '',
			 callback: function (value, validator,$field) {
			 
			 provider_type_group = $('select[name="provider_type_group"]').val();
			 if (provider_type_group == ''){
			 return {
			 valid: false,
			 message: 'Select Provider'
			 }; 
			 } 
			 return true;
			 }
			 
			 }
			 }
			 }, */

			app_search_start_date: {
				selector: '.app_search_start_date',
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
							var end_date = $("#" + get_field + " .app_search_end_date").val();
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
			app_search_end_date: {
				message: '',
				selector: '.app_search_end_date',
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
							var start_date = $("#" + get_field + " .app_search_start_date").val();
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
		var get_form_id = $(this).closest('form').attr("id");
		var form_data = $("#" + get_form_id).serialize();
		var url = $(this).closest('form').attr("action");

		getAjaxResponse(url, form_data);
		$('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
	});
/****   for group   *****/

$('#js-bootstrap-searchvalidator_provGroup_list')
        .bootstrapValidator({
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
                'year_type': {
                    validators: {
                        notEmpty: {
                            message: 'Select Year'
                        }
                    }
                },
                'select_year': {
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {
                                if (value.length != 4 && value != "") {
                                    return {
                                        valid: false,
                                        message: 'Enter valid Year'
                                    };
                                }
                                else if (value == "") {
                                    return {
                                        valid: false,
                                        message: 'Enter Year'
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
                },
                provider_type: {
                    message: '',
                    trigger: 'change',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {

                                provider_type = $('select[name="provider_type"]').val();
                                if (provider_type == '') {
                                    return {
                                        valid: false,
                                        message: 'Select Provider'
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                pos_code: {
                    message: '',
                    trigger: 'change',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {

                                pos_code = $('select[name="pos_code"]').val();
                                if (pos_code == '') {
                                    return {
                                        valid: false,
                                        message: 'Select Provider'
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                insurance_category: {
                    message: '',
                    trigger: 'change',
                    validators: {
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {

                                insurance_category = $('select[name="insurance_category"]').val();
                                if (insurance_category == '') {
                                    return {
                                        valid: false,
                                        message: 'Select Group'
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                app_search_start_date: {
                    selector: '.app_search_start_date',
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
                                var end_date = $("#" + get_field + " .app_search_end_date").val();
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
                app_search_end_date: {
                    message: '',
                    selector: '.app_search_end_date',
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
                                var start_date = $("#" + get_field + " .app_search_start_date").val();
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
			var get_form_id = $(this).closest('form').attr("id");
			var form_data = $("#" + get_form_id).serialize();
			var url = $(this).closest('form').attr("action");

			getAjaxResponse(url, form_data);
			$('#js-bootstrap-searchvalidator').bootstrapValidator('disableSubmitButtons', false);
		});
/*** CSELVA   Ends Reports ****/
/****   Billing reports module in end of the day total ****/

$(document).on('change', 'select[name="rendering_provider"]', function () {
    $('#js-bootstrap-searchvalidator_provider').bootstrapValidator('revalidateField', "rendering_provider");
    $('#js-bootstrap-searchvalidator_provider').bootstrapValidator('revalidateField', "billing_provider");
});

$(document).on('change', 'select[name="billing_provider"]', function () {
    $('#js-bootstrap-searchvalidator_provider').bootstrapValidator('revalidateField', "rendering_provider");
    $('#js-bootstrap-searchvalidator_provider').bootstrapValidator('revalidateField', "billing_provider");
});

$(document).on('ifChanged', '.provider_list', function () {
    $("#sh2").addClass('hide').removeClass('show');
    $("#sh1").addClass('show').removeClass('hide');
});

$(document).on('ifChanged', '.provider_group', function () {
    $("#sh1").addClass('hide').removeClass('show');
    $("#sh2").addClass('show').removeClass('hide');
});

$(document).on('change', '.js-provider_type', function () {
    var provider_type_val = $(this).val();

    if (provider_type_val == 'provider_list' || provider_type_val == '') {
        $("#js_select_option_provider").addClass('hide');
        //$('.pro_type').empty();
    } else {

        $("#js_select_option_provider").removeClass('hide').addClass('show');
    }
    //$("#js_select_option_provider").removeClass('show').addClass('hide');
});

$(document).on("change", "#wallet", function(){
    if($(this).is(":checked")){
        $("#unposted").prop("disabled", true);
    }else {
        $("#unposted").prop("disabled", false);
    }
})

$(document).on('change', '.js_payment_patien', function () {
    var patient_type_val = $(this).val();
    if (patient_type_val == 'patient' || patient_type_val == '') {
        $("#js_patient_insurance").addClass('hide').parent("div").addClass('margin-t-22');
        //$('.pro_type').empty();
        $("#wallet").parent("div").show();
    } else {
        $("#js_patient_insurance").removeClass('hide').addClass('show').parent("div").removeClass('margin-t-22');
        $("#wallet").prop("checked",false).parent("div").hide();
        $("#unposted").prop("disabled",false);
    }
    //$("#js_select_option_provider").removeClass('show').addClass('hide');
});

/* hide insurance div while change adjusment type as patient */
/* $(document).on( 'change', '.js_adjus_typ', function () { 
 var patient_adjusment_type_val = $(this).val();
 
 
 if(patient_adjusment_type_val=='patient'||patient_adjusment_type_val==''){
 $("#js_adjusment_patient_insurance").addClass('hide');
 //$('.pro_type').empty();
 }
 else{
 
 $("#js_adjusment_patient_insurance").removeClass('hide').addClass('show');
 }
 //$("#js_select_option_provider").removeClass('show').addClass('hide');
 
 });
 */
 
$(document).on("change", "#js_ins_adj_typ", function () {
    var _this = $(this);
    var ins_obj = $("#js-insurance-adj");
    if (_this.val() == 'patient') {
        ins_obj.attr('disabled', 'disabled').val('').select2();
    } else {
        ins_obj.removeAttr('disabled');
    }
}); // added trigger to calculate initial state

$( document ).ready(function() {
    if($( ".filterSerachBtn" ).length) {
        $(".filterSerachBtn").trigger("click");
    }
});

$(document).on("change", ".js-unposted", function() {
    var data = false;
    if($(this).is(":checked")) {
        data = true;
        $("#wallet").not($(this)).prop("disabled", true);
    } else{            
        $("#wallet").not($(this)).prop("disabled", false);   
    }
        
    $('select.js_unposted_disable:not(select[name="insurance_id"])').each(function(){
        $(this).prop("disabled", data).select2();
    });
});

/*$(document).on('change','.auto-generate',function(){
	if($(this).val() == 'wallet_balance'){
		$('select#insurance_charge').val('self').select2();
	}else{
		$('select#insurance_charge').val('all').select2();
	}
});*/


/* 
 * This script used for change the year end option  
 */
 
 $(document).on('change','select[name="year_option"]',function(){
	if($(this).val() == 'current_year'){
		$('select[name="year"]').val($("select[name='year'] option:first").val()).trigger('change.select2');
		$('select[name="year"]').prop('disabled',true);
	}else if($(this).val() == 'previous_year'){
		$('select[name="year"]').val($("select[name='year'] option:first").next().val()).trigger('change.select2');
		$('select[name="year"]').prop('disabled',true);
	}else{
		$('select[name="year"]').prop('disabled',false);
	}
 });
 
 
 /* Selected field to export start */
$(document).on('click', '.js_claim_export',function (e) {
	//console.log('pageName'+pageName);
	if (typeof pageName != 'undefined' && (pageName == 'charge_report' || pageName == 'payment_report') ) {
		e.preventDefault();
		$('#export_fields_model').modal('show');
		return false;
	}
});

$(document).on('ifToggled click', '#check_all', function () {
    $('.srch_exp_flds').prop('checked', $(this).is(":checked"));
});

$(document).on('click', '.js_srch_export', function (e) {
	var data_arr = '';
    if($('input[name="aging_summary"]').val()=='aging-summary') {
        $('select.auto-generate').each(function(){
			if($(this).select2('val') != '')
				data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
        });
    } else {
    	$('select.auto-generate:visible').each(function(){
    		if($(this).select2('val') != '')
    			data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
    	});
    }
	$('input.auto-generate:visible').each(function(){
		if($(this).val() != '')
			data_arr += $(this).attr('name')+'='+$(this).val()+'&';
	});
    if($('input[name="practiceoption"]').length > 0)
        data_arr += $('input[name="practiceoption"]').attr('name')+'='+$('input[name="practiceoption"]').val()+'&';
	
	
	if($(".srch_exp_flds").length > 0){
		var expFld = [];
				
		$('input.srch_exp_flds:visible:checked').each(function(){
			//if($(this).val() != '')
			//	data_arr += $(this).attr('id')+'='+$(this).val()+'&';
			expFld.push($(this).attr('id'));
		});
		data_arr += 'exp_flds='+expFld.join(",")+'&';	
	}
	
	final_data = data_arr+"_token="+$('input[name=_token]').val()+"&export=yes";
	//alert(final_data); return false;
	var url = $(this).attr('data-url');
    if (url.length) {
        $(this).attr('href', url + '/' + $(this).attr('data-option') + '?' + final_data);
    }
	/* Export as background call start */
	report_controller_name = $('input[name="report_controller_name"]').val();
	if (report_controller_name != "" && typeof report_controller_name != "undefined") {
		//console.log($('input[name="report_controller_name"]').val());
		var url = api_site_url + "/generateReportExport";
		$.ajax({
			url: url,
			type: 'post',
			data: {'_token': $('input[name="token"]').val(),'report_url':url + '/' + $(this).attr('data-option') + '?' + final_data, 'report_controller_name':$('input[name="report_controller_name"]').val(), 'report_controller_func':$('input[name="report_controller_func"]').val(), 'report_name':$('input[name="report_name"]').val()},
			success: function (data) {
               if($('input[name="report_name"]').val()=='Charge Analysis Detailed'){
                   js_sidebar_notification("success","Converting Text to Column and Downloading processing");  
               }else{
                   js_sidebar_notification("success","Your report is being generated, Please wait for 5 minutes and check the Generated Reports section in Reports module for your downloadable report");  
               }
			}
		})
		return false;
	}
	/* Export as background call end */
    //removeHash();    
});

/* Selected field to export end */

/* Refresh reports start */
$(document).on('click', '.js-refreshreport', function (e) {
	$(".fa-refresh").addClass("fa-spin");
	var url = api_site_url + "/storeReportFile";
	$.ajax({
		url: url,
		type: 'get',
		//data: {'_token': $('input[name="token"]').val()},
		success: function (data) {
			//console.log("in success block");
		   js_sidebar_notification("success","Refreshed Successfully!!!");  
		   setTimeout(function(){ location.reload(); }, 500);
		   
		}
	})
});
/* Refresh reports end */


$(document).on('click','.js-parameter', function(){
	var id = $(this).attr('data-export-id');
	var url = api_site_url + "/reports/getParameter/"+id;
	$.ajax({
		url: url,
		type: 'get',
		success: function (data) {
			$('#paramenter_info').html(data);
		   $('#myModal').modal('toggle');
		}
	})
});

/* patient statement status report hold fields start  */
$(document).on("change", ".selStmts", function(){
    if($(this).val() == 'Hold'){
        $(".selholdBlk").prop("disabled", false);
    } else {		
		$(".selholdBlk").select2('val', '').val("").prop("disabled", true); // Clear already selected hold reason and release date.
    }
})
/* patient statement status report hold fields end  */

/* Charge analysis report hold fields start  */
$(document).on("change", ".selClaimStatus", function(){
    var isHold = 0;
    $("select.selClaimStatus option:selected").each(function () {
        if($(this).text() == 'Hold')
        isHold = 1;
    });

    //if($(this).val() == 'Hold'){
    if(isHold) {    
        $(".selholdBlk").prop("disabled", false);
    } else {        
        $(".selholdBlk").select2('val', '').val("").prop("disabled", true); // Clear already selected hold reason and release date.
    }
})
/* Charge analysis report hold fields end  */

/* Delete Function for generate reports */
$(document).on('click','.js-generate-delete',function(){
	var currentThis = $(this);
    var table = $('table.table-striped').DataTable();
	$('#js_confirm_box_charges_content').html("Would you like to delete?");
	$("#js_confirm_box_charges")
		.modal({show: 'false', keyboard: false})
		.one('click', '.js_modal_confirm1', function (eve) {
			confirm_alert = $(this).attr('id');
			if (confirm_alert == 'true') {
				var generate_id = $(currentThis).attr('data-generate-id');
				var url = api_site_url + "/exportDelete/"+generate_id;
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: url,
					type: 'post',
					success: function (data) {
						table.rows(currentThis.parents('tr')).remove().draw(false);
					}
				});
			}
		});
});
/* Delete Function for generate reports */