$(document).delegate('a[data-target=#js-model-popup-payment]', 'click', function () {
	var target1 = $(this).attr("data-url");
	var claim_no = $(this).attr('claim_number');
	$("#js-model-popup-payment .modal-body").load(target1, function () {
		$("#js-model-popup-payment .modal-title").html("Claim No :" + claim_no);
	});
});

$(document).on('click', ".js-claim-view-tab", function () {
	var claim_id_values = $('input:visible[type=checkbox]:checked.js_claim_ids').map(function () {
		return ($(this).is(':visible')) ? this.value : "";
	}).get();
	var def_open_claim_tab = '';
	var max_len = Number($('#js_ar_max_claim_seleted').val());
	if (claim_id_values.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	} else if (claim_id_values.length > max_len) {
		js_alert_popup("Select maximum of 5 claims only.");
	} else {
		var selected_claim_id_values = claim_id_values.toString();
		var prev_sel_claim_id_values = $('#selected_claim_ids_arr').val();
		var TempArray = prev_sel_claim_id_values.split(",");
		$.each(TempArray,function(i,value){
			$('#remove_claim_id-'+value).click();
			$('input[name="claim_ids[]"]:checkbox[value="' + value + '"]').prop("checked", true);
			$('input[name="claim_ids[]"]:checkbox[value="' + value + '"]').iCheck('update');
		});
		prev_sel_claim_id_values = '';
		var data = "selected_claim_id_values=" + selected_claim_id_values + "&prev_sel_claim_id_values=" + prev_sel_claim_id_values + "&tab_type=all";
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimtabdetails',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: data,
			success: function (res) {
				var result = res.split('^^::^^');
				var added_claim_tabs = result[0];
				var remove_claim_tabs = result[1];
				var added_claim_tab_details = result[2];
				if (added_claim_tabs != '') {
					var added_claim_tabs_arr = added_claim_tabs.split(',');
					var append_data = '';
					var scount = 1;
					$.each(added_claim_tabs_arr, function (key, data) {
						append_data += '<li class="active js-claim-tab-info_' + $.trim(data) + '"><a href="javascript:void(0);"><span id="claimdetlink_' + $.trim(data) + '" class="js_claimdetlink"><i class="fa fa-navicon i-font-tabs"></i>&nbsp' + data + '</span>&nbsp&nbsp<i style="cursor: pointer;" id="remove_claim_id-' + $.trim(data) + '" class="js-remove-claim-tab fa fa-times pull-right"></i></a></li>';
						if (scount == 1) {
							def_open_claim_tab = data;
						}
						scount++;
					});
					$(".js-dynamic-tab-menu ul").append(append_data);
				}
				if (remove_claim_tabs != '') {
					var remove_claim_tabs_arr = remove_claim_tabs.split(',');
					$.each(remove_claim_tabs_arr, function (key, data) {
						$('.js-claim-tab-info_' + $.trim(data)).remove();
						$('#claim-tab-info_' + $.trim(data)).remove();
					});
				}
				$('.js-claim-dyanamic-tab').append(added_claim_tab_details);
				if (result.length == 4) {
					$('.js_indivual_insurance').html(result[3]);
				}
				$('.ar-notes').slimScroll({
					height: '150px'
				});
				$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
				$("select.select2.form-control").select2();
				window.location.hash = '';
				history.pushState('', document.title, window.location.pathname);
				$('#selected_claim_ids_arr').val(selected_claim_id_values);

				if (def_open_claim_tab != '') {
					$('.js-claim-tab-info_' + $.trim(def_open_claim_tab)).parents('ul').find("li").removeClass('active');
					$(".tab-content").find("div").removeClass('active');
					$('.js-claim-tab-info_' + $.trim(def_open_claim_tab)).closest("li").addClass('active');
					$("#claim-tab-info_" + $.trim(def_open_claim_tab)).addClass('active');
					$('#selected_curr_claim_id').val(def_open_claim_tab);
				}

				$("a#ar_href").attr('href', 'javascript:void(0)');
			}
		});
	}
});

$(document).on('focus', '.timepicker1', function () {
	$(".timepicker1").timepicker({
		showInputs: false,
		defaultTime: false
	});
});

$(document).on('click', '.js-remove-claim-tab', function () {
	var id = $(this).attr('id');
	var id_val = id.split('-');
	var prev_claim_cls = $('.js-claim-tab-info_' + id_val[1]).prev('li').attr('class');
	$('.js-claim-tab-info_' + id_val[1]).remove();
	$('#claim-tab-info_' + id_val[1]).remove();
	$('input[name="claim_ids[]"]:checkbox[value="' + id_val[1] + '"]').prop("checked", false);
	$('input[name="claim_ids[]"]:checkbox[value="' + id_val[1] + '"]').iCheck('update');
	var selected_curr_claim_id = $('#selected_curr_claim_id').val();
	if ($.trim(selected_curr_claim_id) == id_val[1]) {
		if (prev_claim_cls != '') {
			var prv_claim_arr = prev_claim_cls.split('_');
			var prv_claim_num = prv_claim_arr[1];
			$('.js-claim-tab-info_' + prv_claim_num).parents('ul').find("li").removeClass('active');
			$(".tab-content").find("div").removeClass('active');
			$('.js-claim-tab-info_' + prv_claim_num).closest("li").addClass('active');
			$("#claim-tab-info_" + prv_claim_num).addClass('active');
			$('#selected_curr_claim_id').val(prv_claim_num);
			if (prev_claim_cls == 'hide') {
				$('#claimdetlink_main0').closest("li").addClass('active');
				$("#claim-tab-info_main0").addClass('active');
			}
		} else {

			$('#claimdetlink_main0').parents('ul').find("li").removeClass('active');
			$(".tab-content").find("div").removeClass('active');
			$('#claimdetlink_main0').closest("li").addClass('active');
			$("#claim-tab-info_main0").addClass('active');
		}
	}
	var prev_selected_values = $('#selected_claim_ids_arr').val();
	if (prev_selected_values != '') {
		var prev_selected_values_arr = prev_selected_values.split(',');
		prev_selected_values_arr = jQuery.grep(prev_selected_values_arr, function (value) {
			return value != id_val[1];
		});
		prev_selected_values_arr.join(",");
		$('#selected_claim_ids_arr').val(prev_selected_values_arr);
	}
	/*$("#js_confirm_arlist_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		if(conformation == "true") {
		}
	});*/

});

$(document).on('ifToggled', "input[name='claim_ids[]']", function () {
	var remove_claim = $(this).val();
	if ($(this).prop('checked') == false) {
		var prev_selected_values = $('#selected_claim_ids_arr').val();
		if (prev_selected_values != '') {
			var prev_selected_values_arr = prev_selected_values.split(',');
			if ($.inArray(remove_claim, prev_selected_values_arr) != -1) {
				$('.js-claim-tab-info_' + remove_claim).remove();
				$('#claim-tab-info_' + remove_claim).remove();
				prev_selected_values_arr = jQuery.grep(prev_selected_values_arr, function (value) {
					return value != remove_claim;
				});
				prev_selected_values_arr.join(",");
				$('#selected_claim_ids_arr').val(prev_selected_values_arr);
				/*
				$("#js_confirm_arlist_remove")
				.modal({show: 'false', keyboard: false})
				.one('click', '.js_modal_confirm', function (e) {
					var conformation = $(this).attr('id');
					if (conformation == "true") {
						$('.js-claim-tab-info_'+remove_claim).remove();
						$('#claim-tab-info_'+remove_claim).remove();
						prev_selected_values_arr = jQuery.grep(prev_selected_values_arr, function( value ) {
						  return value != remove_claim;
						});
						prev_selected_values_arr.join(",");
						$('#selected_claim_ids_arr').val(prev_selected_values_arr);
					}
					else{
						setTimeout(function(){ 
						$('input[name="claim_ids[]"]:checkbox[value="'+remove_claim+'"]').prop("checked", true);
						$('input[name="claim_ids[]"]:checkbox[value="'+remove_claim+'"]').iCheck('update');
						}, 20); 
					}
				});
				*/
			}
		}
	}
});

$(document).on('keyup', ".js-claim-notes-txt", function () {
	var curr_id = $(this).attr('id');
	var curr_value = $(this).val();
	var curr_claim_val_arr = curr_id.split('_');
	var curr_claim_val = curr_claim_val_arr[1];
	if (curr_value == "") {
		$("#js-claim-notes-div_" + curr_claim_val).addClass('has-error');
		$("#js-claim-notes-err_" + curr_claim_val).removeClass('hide').addClass('show');
	} else {
		$("#js-claim-notes-div_" + curr_claim_val).removeClass('has-error');
		$("#js-claim-notes-err_" + curr_claim_val).removeClass('show').addClass('hide');
	}
});

$(document).on('click', ".js-claim-notes-submitbtn", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var curr_claim_val = curr_claim_val_arr[1];
	var txt_value = $('#js-claim-notes-txt_' + curr_claim_val).val();
	if (txt_value == "") {
		$("#js-claim-notes-div_" + curr_claim_val).addClass('has-error');
		$("#js-claim-notes-err_" + curr_claim_val).removeClass('hide').addClass('show');
	} else {
		$('#js-claim-notes-submitbtn-footer_' + curr_claim_val).html('<i class="fa fa-spinner fa-spin" style="margin-right: 30px;"></i>');
		var data = "claim_id=" + curr_claim_val + "&claim_notes=" + encodeURIComponent(txt_value);
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimnotesadded',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: data,
			success: function (resp) {
				claim_tab_reload_process_indivual(curr_claim_val);
				js_alert_popup("Notes added successfully");
			}
		});
	}
});

$(document).on('click', "#claim_notes_all_link", function () {
	/* Armanagement  bulk notes and workbench chnaged based on all */
	/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
	var type = $('select[name="js-select-option"]').val();
	if (type == 'page' || type == 'none') {
		selected_claim_ids = [];
		$('input:checkbox:checked.js_claim_ids').each(function () {
			selected_claim_ids.push($(this).attr('value'));
		});
	} else if (type == 'all') {
		selected_claim_ids = [];
		selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
	}else{
		var selected_claim_ids = $('input:checkbox:checked.js_claim_ids').map(function () { return this.value; }).get();
	}
	if (selected_claim_ids.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	} else {
		$('.js_claim_notes_all_txt').val('');
		$('#claim_notes_all').modal({
			show: 'true'
		});
	}
});

$(document).on('click','.claim_assign_ar_link',function(){
	var claim_no = $(this).attr('data-id');
	var form_tag = $('.js_all_claim_assign_form');
	//console.log(form_tag[0]);
	if (typeof form_tag[0] !== 'undefined')
		form_tag[0].reset();
	$('.js_assign_to, .js_priority, .js_status').select2("val", "");
	$('#claim_assign_all').modal({
		show: 'true'
	});
	claimallassignformvalidationAR(claim_no);
	if (typeof form_tag[0] !== 'undefined')
		form_tag.data("bootstrapValidator").resetForm();
});


function claimallassignformvalidationAR(claim_no) { 

	var form_validator = $('.js_all_claim_assign_form').bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled, :hidden, :not(:visible)',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			assign_to: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select assign user'
					}
				}
			},
			follow_up_date: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Select follow up date'
					},
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var current_date = new Date(value);
							var d = new Date();
							/* if(value != '' && d.getTime() > current_date.getTime()){
								return {
									valid: false,
									message: 'Followup date give future date'
								};
							} else { */
							return true;
							/* } */
						}
					}
				}
			},
			priority: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select priority'
					}
				}
			},
			status: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select status'
					}
				}
			},
			description: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Enter description'
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function (eve) {
		eve.preventDefault();
		
		selected_claim_assign_id_values = claim_no;
		var data = $('.js_all_claim_assign_form').serialize();
		data += "&claim_id=" + selected_claim_assign_id_values + "&claim_assign_type=all";
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimassignadded',
			type: "POST",
			data: data,
			success: function (resp) {
				js_sidebar_notification("success", "Claims assigned to Work Bench successfully");
				$('#claim_assign_all').modal('hide');
				$('#claimdetlink_'+claim_no).click();
			}
		});
	});
}


$(document).on('click', "#claim_assign_all_link,.claim_assign_all_link", function () {
	if ($(this).attr("data-index") == "ledger") {
		var claim_id_values = $(this).attr("data-id");
		$(".js_all_claim_assign_form").attr("data-value", claim_id_values);
	}
	else
		var claim_id_values = $('input:checkbox:checked.js_claim_ids').map(function () { return this.value; }).get();

	if (claim_id_values.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	} else {
		var form_tag = $('.js_all_claim_assign_form');
		//console.log(form_tag[0]);
		if (typeof form_tag[0] !== 'undefined')
			form_tag[0].reset();
		$('.js_assign_to, .js_priority, .js_status').select2("val", "");
		$('#claim_assign_all').modal({
			show: 'true'
		});
		claimallassignformvalidation();
		if (typeof form_tag[0] !== 'undefined')
			form_tag.data("bootstrapValidator").resetForm();
	}
});

$(document).on('keyup', ".js_claim_notes_all_txt", function () {
	var curr_value = $(this).val();
	if (curr_value == "") {
		$("#js-claim-notes-all-div").addClass('has-error');
		$("#claim_notes_all_err").removeClass('hide').addClass('show');
	} else {
		$("#js-claim-notes-all-div").removeClass('has-error');
		$("#claim_notes_all_err").removeClass('show').addClass('hide');
	}
});

$(document).on('click', ".js_claim_notes_all_btn", function () {
	var curr_value = $(".js_claim_notes_all_txt").val();
	if (curr_value == "") {
		$("#js-claim-notes-all-div").addClass('has-error');
		$("#claim_notes_all_err").removeClass('hide').addClass('show');
	} else {
		/* Armanagement  bulk notes and workbench chnaged based on all */
		/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
		var type = $('select[name="js-select-option"]').val();
		if (type == 'page' || type == 'none') {
			selected_claim_ids = [];
			$('input:checkbox:checked.js_claim_ids').each(function () {
				selected_claim_ids.push($(this).attr('value'));
			});
		} else if (type == 'all') {
			selected_claim_ids = [];
			selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
		}else{
			var selected_claim_ids = $('input:checkbox:checked.js_claim_ids').map(function () { return this.value; }).get();
		}
		var selected_claim_id_values = selected_claim_ids.toString();
		var data = "claim_id=" + selected_claim_id_values + "&claim_notes=" +  encodeURIComponent(curr_value);
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimnotesadded',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: data,
			success: function (resp) {
				claim_tab_reload_process();
				js_sidebar_notification("success", "Notes added successfully");
				$('#claim_notes_all').modal('hide');
			}
		});
	}
});

$(document).on('focus', '.js_claim_denial_date', function () {
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$(".js_claim_denial_date").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+0',
		maxDate:  new Date(get_default_timezone),
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
	    },
		onClose: function (selectedDate) { this.focus(); }
	});
});

$(document).on('focus', '.js_follow_up_date', function () {
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$(".js_follow_up_date").datepicker({
		changeMonth: false,
		changeYear: false,
		minDate: get_default_timezone,
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
	    },
		onClose: function (selectedDate) { this.focus(); }
	});
});

$(document).on('focus', '.js_claim_status_popup_fields', function () {
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$(".js_claim_status_popup_fields").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+0',
		maxDate: new Date(get_default_timezone),
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
	    },
		onClose: function (selectedDate) { this.focus(); }
	});
});

$(document).on('focus', '.js_claim_status_popup_future_date', function () {
	$(".js_claim_status_popup_future_date").datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+100',
		onClose: function (selectedDate) { this.focus(); }
	});
});

$(document).on('click', ".js_claim_denial_form_link", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var mg = 0;
	$('#denial_details_' + curr_claim_val_arr[1]).modal('show');
	var ar_denials_new_height = $('#ar-denials-new_' + curr_claim_val_arr[1]).height();
	if (ar_denials_new_height > 250) {
		$('#ar-denials-new_' + curr_claim_val_arr[1]).slimScroll({
			height: '250px'
		});
	}
	var form_tag = $('#bootstrap-validator-denial_' + curr_claim_val_arr[1]);
	form_tag[0].reset();
	$('#bootstrap-validator-denial_' + curr_claim_val_arr[1] + ' .js_denial_insurance').select2("val", "");
	claimdenailformvalidation(curr_claim_val_arr[1]);
	form_tag.data("bootstrapValidator").resetForm();
	$('.denail_codes_list_part_' + curr_claim_val_arr[1]).html(""); // Empty the previous entry, to avoid showing on previous search result.
	triggerdenialsearch(curr_claim_val_arr[1]);
	// /$(".js_denial_search_btn").trigger('click'); // Re populating the result.
});

function claimdenailformvalidation(form_id) {
	var form_validator = $('#bootstrap-validator-denial_' + form_id).bootstrapValidator({
		message: 'This value is not valid',
		//excluded: ':disabled',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			denial_date: {
				selector: '.js_denial_frm_denail_date_' + form_id,
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Select denial date'
					},
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var current_date = new Date(value);
							var d = new Date();
							if (value != '' && d.getTime() < current_date.getTime()) {
								return {
									valid: false,
									message: date_format
								};
							} else {
								return true;
							}
						}
					}
				}
			},
			check_no: {
				selector: '.js_denial_frm_check_no_' + form_id,
				message: '',
				validators: {
					notEmpty: {
						message: 'Enter check number'
					},
					callback: {
						callback: function (value, validator) {
							check_number_exist = $('#bootstrap-validator-denial_' + form_id + ' input[name="checkexist"]').val();
							if (!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value != '') {
								return {
									valid: false,
									message: 'Alpha numeric only allowed'
								};
							} else if (check_number_exist == 1) {
								return {
									valid: false,
									message: checkexist
								};
							}
							return true;
						}
					}
				}
			},
			denial_insurance: {
				selector: '#js_denial_frm_denial_insurance_' + form_id,
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select insurance'
					}
				}
			}/*,
			'denial_codes[]': {
				selector: '.js_denial_frm_denial_codes_'+form_id,
				message: '',
				validators: {
					notEmpty: {
						message: 'Select anyone denial code'
					}
				}
			}*/
		}
	}).unbind("success").on('success.form.bv', function (eve) {

		var sel_code_val = $('input:checkbox:checked.js_denial_frm_denial_codes_' + form_id).map(function () { return this.value; }).get();
		if (sel_code_val == '') {
			eve.preventDefault();
			js_sidebar_notification("error", "Select anyone denial code");
		} else {
			eve.preventDefault();
			$('#denial-form-footer_' + form_id).html('<i class="fa fa-spinner fa-spin">');
			var data = $('#bootstrap-validator-denial_' + form_id).serialize();
			data += "&claim_id=" + form_id;
			$.ajax({
				url: api_site_url + '/patients/armanagement/getclaimdenailnotesadded',
				type: "POST",
				data: data,
				success: function (resp) {
					$('#denial_details_' + form_id).modal('hide');
					claim_tab_reload_process_indivual(form_id);
					js_sidebar_notification("success", "Denials added successfully");
				}
			});
		}
	});
}

$(document).on('click', ".js-denail-form-submit", function (e) {
	e.preventDefault();
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var curr_claim_val = curr_claim_val_arr[1];
	$('#bootstrap-validator-denial_' + curr_claim_val).data("bootstrapValidator").resetForm();
	$('#bootstrap-validator-denial_' + curr_claim_val).bootstrapValidator('validate');
	$(this).off('click');
});

$(document).on('ifToggled', "input[name='denial_codes[]']", function () {
	var form_id = $(this).parents("form").attr("id");
	var form_tag = $('#' + form_id);
	form_tag.bootstrapValidator('revalidateField', "denial_codes[]");
});

function claimallassignformvalidation() { 

	var form_validator = $('.js_all_claim_assign_form').bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled, :hidden, :not(:visible)',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			assign_to: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select assign user'
					}
				}
			},
			follow_up_date: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Select follow up date'
					},
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var current_date = new Date(value);
							var d = new Date();
							/* if(value != '' && d.getTime() > current_date.getTime()){
								return {
									valid: false,
									message: 'Followup date give future date'
								};
							} else { */
							return true;
							/* } */
						}
					}
				}
			},
			priority: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select priority'
					}
				}
			},
			status: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select status'
					}
				}
			},
			description: {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Enter description'
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function (eve) {
		eve.preventDefault();
		if ($(".claim_assign_all_link").attr("data-index") == "ledger") {
			var selected_claim_assign_id_values = $(".js_all_claim_assign_form").attr("data-value");
		}
		else {
			/* Armanagement  bulk notes and workbench chnaged based on all */
			/* Revision 1 : Ref: MR-2756 : 27 Aug 2019 : selva */
			
			/* Claims module workbech issues fixed */
			/* Revision 1 : Ref: MR-2761 : 28 Aug 2019 : selva */
			var type = $('select[name="js-select-option"]').val();
			if (type == 'page' || type == 'none') {
				selected_claim_ids = [];
				$('input:checkbox:checked.js_claim_ids').each(function () {
					selected_claim_ids.push($(this).attr('value'));
				});
				var selected_claim_assign_id_values = selected_claim_ids.toString();
			} else if (type == 'all') {
				selected_claim_ids = [];
				selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
				var selected_claim_assign_id_values = selected_claim_ids.toString();
			}else{
				var selected_claim_ids = $('input:checkbox:checked.js_claim_ids').map(function () { return this.value; }).get();
				selected_claim_assign_id_values = selected_claim_ids;
				
			}
		}
		
		var data = $('.js_all_claim_assign_form').serialize();
		data += "&claim_id=" + selected_claim_assign_id_values + "&claim_assign_type=all";
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimassignadded',
			type: "POST",
			data: data,
			success: function (resp) {
				if ($(".claim_assign_all_link").attr("data-index") != "ledger") {
					claim_tab_reload_process();
				}
				$('#claim_assign_all').modal('hide');
				js_sidebar_notification("success", "Claims assigned to Work Bench successfully");
			}
		});
		location.reload();
	});
}

$(document).on('click', ".js_allclaim_assign_btn", function (e) {
	e.preventDefault();
	$('.js_all_claim_assign_form').data("bootstrapValidator").resetForm();
	$('.js_all_claim_assign_form').bootstrapValidator('validate');

	$(this).off('click');
});

function claim_tab_reload_process() {
	var curr_claim_ids_arr = $('input:checkbox:checked.js_claim_ids').map(function () { return this.value; }).get();
	var prev_sel_claim_ids_values = $('#selected_claim_ids_arr').val();
	var prev_sel_claim_ids_arr = prev_sel_claim_ids_values.split(',');

	var common_ids_arr = $.grep(curr_claim_ids_arr, function (element) {
		return $.inArray(element, prev_sel_claim_ids_arr) !== -1;
	});

	if (common_ids_arr.length > 0) {

		$.each(common_ids_arr, function (key, data) {
			$('#claim-tab-info_' + data).remove();
		});

		var selected_claim_id_values = common_ids_arr.toString();
		var prev_sel_claim_id_values = '';
		var data = "selected_claim_id_values=" + selected_claim_id_values + "&prev_sel_claim_id_values=" + prev_sel_claim_id_values + "&tab_type=all";
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimtabdetails',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: data,
			success: function (res) {
				var result = res.split('^^::^^');
				var added_claim_tab_details = result[2];
				$('.js-claim-dyanamic-tab').append(added_claim_tab_details);
				if (result.length == 4) {
					$('.js_indivual_insurance').html(result[3]);
				}
				$('.ar-notes').slimScroll({
					height: '150px'
				});
				$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
				$("select.select2.form-control").select2();
				window.location.hash = '';
				history.pushState('', document.title, window.location.pathname);
			}
		});
	}
}

/********************************************************************************************************************/
$(document).on('click', ".js_claim_assign_form_link", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	$('#assign_details_' + curr_claim_val_arr[1]).modal({
		show: 'true'
	});
	claimindivualassignformvalidation(curr_claim_val_arr[1]);
	var form_tag = $('#bootstrap-validator-assign_' + curr_claim_val_arr[1]);
	form_tag[0].reset();
	$('.js_indivual_assign_to, .js_indivual_priority, .js_indivual_status').select2("val", "");
	form_tag.data("bootstrapValidator").resetForm();


	var assigndet_popup_height = $('#popupassign_details_' + curr_claim_val_arr[1]).height();
	if (assigndet_popup_height > 380) {
		$('#popupassign_details_' + curr_claim_val_arr[1]).slimScroll({
			height: '380px'
		});
	}
});

function claimindivualassignformvalidation(form_id) { 
	var form_validator = $('#bootstrap-validator-assign_' + form_id).bootstrapValidator({
		message: 'This value is not valid',
		//excluded: ':disabled',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			indivual_description: {
				selector: '#js_indivual_description_' + form_id,
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Enter description'
					}
				}
			},
			indivual_assign_user_id: {
				selector: '#js_indivual_assign_user_id_' + form_id,
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select assign user'
					}
				}
			},
			indivual_followup_date: {
				selector: '#js_indivual_followup_date_' + form_id,
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Select followup date'
					},
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var current_date = new Date(value);
							if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {
								var d = new Date(get_default_timezone);
							}else{
								var d = new Date();
							}
							if (value != '' && d.getDate() > current_date.getDate()) {
								return {
									valid: false,
									message: 'Followup date give future date'
								};
							}
							else {
								return true;
							}
						}
					}
				}
			},
			indivual_status: {
				selector: '#js_indivual_status_' + form_id,
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select status'
					}
				}
			},
			indivual_priority: {
				selector: '#js_indivual_priority_' + form_id,
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select priority'
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function (eve) {
		eve.preventDefault();
		$('#assign-form-footer_' + form_id).html('<i class="fa fa-spinner fa-spin">');
		var assign_to = $('#js_indivual_assign_user_id_' + form_id).val();
		var follow_up_date = $('#js_indivual_followup_date_' + form_id).val();
		var status = $('#js_indivual_status_' + form_id).val();
		var description = $('#js_indivual_description_' + form_id).val();
		var priority = $('#js_indivual_priority_' + form_id).val();
		var data = 'assign_to=' + assign_to + '&follow_up_date=' + follow_up_date + '&status=' + status + '&description=' + description + '&priority=' + priority + '&claim_id=' + form_id + '&claim_assign_type=indivual';
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimassignadded',
			type: "POST",
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			data: data,
			success: function (resp) {
				claim_tab_reload_process_indivual(form_id);
				$('#assign_details_' + form_id).modal('hide');
				js_alert_popup("Claim assigned successfully");
			}
		});
		location.reload();
	});
}

$(document).on('click', ".js-assign-form-submit", function (e) {
	e.preventDefault();
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var curr_claim_val = curr_claim_val_arr[1];
	$('#bootstrap-validator-assign_' + curr_claim_val).data("bootstrapValidator").resetForm();
	$('#bootstrap-validator-assign_' + curr_claim_val).bootstrapValidator('validate');
	$(this).off('click');
});

function claim_tab_reload_process_indivual(curr_claim_val) {
	var selected_claim_id_values = curr_claim_val;
	var prev_sel_claim_id_values = '';
	var data = "selected_claim_id_values=" + selected_claim_id_values + "&prev_sel_claim_id_values=" + prev_sel_claim_id_values + "&tab_type=indivual";
	//processingImageShow('#claim-tab-info_'+curr_claim_val,"show");
	$.ajax({
		url: api_site_url + '/patients/armanagement/getclaimtabdetails',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		success: function (res) {
			var result = res.split('^^::^^');
			var added_claim_tab_details = result[2];
			//processingImageShow('#claim-tab-info_'+curr_claim_val,"hide");
			$('#claim-tab-info_' + curr_claim_val).html(added_claim_tab_details);
			if (result.length == 4) {
				$('.js_indivual_insurance').html(result[3]);
			}
			$("#notes_" + curr_claim_val).removeClass('out').addClass('in');
			$('.ar-notes').slimScroll({
				height: '150px'
			});
			$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
			$("select.select2.form-control").select2();
		}
	});
}

$(document).on('click', ".js_claim_status_notes_form_link", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	$(".followup-box").hide();
	$('#claim_status_notes_form').find('input:text, textarea').val('');
	$('#claim_status_notes_form').find('input:checkbox, input:radio:not([name=claim_paid_type])').prop("checked", false);
	$('#claim_status_notes_form').find('input:checkbox, input:radio').iCheck('update');
	$('#claim_status_notes_form').find("form").attr("data-formid", curr_claim_val_arr[1]);
	$('.js-status-commonselect-cls').select2("val", "");
	//$('.js-followup-insurance').removeClass('show').addClass('hide');

	$("#js_status_notes_part2, #js_arnotes_edit_part, #js-claim-status_final-submitbtn-footer, #js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
	$("#js_status_notes_part1, #js-claim-status_submitbtn-footer").removeClass('hide').addClass('show');

	$("#claim_status_notes_form .modal-title").html('Change Status');

	var patient_id = $(this).attr('data-id');
	var $el = $(".js_claim_nis_insurance");

	$.ajax({
		url: api_site_url + '/patients/armanagement/getclaimpatientinsurance/' + patient_id,
		type: "GET",
		success: function (resp) {
			var newOptions = resp['insurance_arr'];
			$el.empty();
			$el.append($("<option></option>").attr("value", '').text('-- Select--'));
			$.each(newOptions, function (value, key) {
				$el.append($("<option></option>").attr("value", value).text(key));
			});
			$("select.select2.form-control").select2();
		}
	});
	$('#claim_status_notes_form').modal({
		show: 'true'
	});
	claimstatuschageformvalidation();
	var form_tag = $('#bootstrap-validator-claim-status-chage-form');
	form_tag.data("bootstrapValidator").resetForm();
});

$(document).on('ifChecked', "input[name='claim_paid_type']", function () {
	if (this.value == 'paid_to_patient') {
		$(".js_paid_provider_fields").removeClass('show').addClass('hide');
		$(".js_paid_patient_fields").removeClass('hide').addClass('show');
	} else {
		$(".js_paid_patient_fields").removeClass('show').addClass('hide');
		$(".js_paid_provider_fields").removeClass('hide').addClass('show');
	}
});

$(document).on('click', ".js_claim_followup_notes_form_link", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	$(".followup-box").hide();	
	$('#claim_status_notes_form').find('input:text, textarea').val('');
	$('#claim_status_notes_form').find('input:checkbox, input:radio:not([name=claim_paid_type])').prop("checked", false);
	$('#claim_status_notes_form').find('input:checkbox, input:radio').iCheck('update');
	$('#claim_status_notes_form').find("form").attr("data-formid", curr_claim_val_arr[1]);
	$('.js-status-commonselect-cls').select2("val", "");
	//$('.js-followup-insurance').removeClass('hide').addClass('show');
	$("#claim_status_notes_form .modal-title").html('Followup Template');
	$("#js_arnotes_edit_part, #js-claim-status_final-submitbtn-footer, #js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
	$("#js_status_notes_part1, #js_status_notes_part2, #js-claim-status_submitbtn-footer").removeClass('hide').addClass('show');

	var patient_id = $(this).attr('data-id');
	var $el = $(".js_claim_nis_insurance");

	$.ajax({
		url: api_site_url + '/patients/armanagement/getclaimpatientinsurance/' + patient_id,
		type: "GET",
		success: function (resp) {
			var newOptions = resp['insurance_arr'];
			$el.empty();
			$el.append($("<option></option>").attr("value", '').text('-- Select--'));
			$.each(newOptions, function (value, key) {
				$el.append($("<option></option>").attr("value", value).text(key));
			});
			$("select.select2.form-control").select2();
		}
	});

	$('#claim_status_notes_form').modal({
		show: 'true'
	});
	claimstatuschageformvalidation();
	var form_tag = $('#bootstrap-validator-claim-status-chage-form');
	form_tag.data("bootstrapValidator").resetForm();
});

function claimstatuschageformvalidation() {
	$('.common_error_validate').map(function () { $(this).attr('data-bv-field', 'common_error_validate'); }).get();
	var form_validator = $('#bootstrap-validator-claim-status-chage-form').bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled, :hidden, :not(:visible)',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {
			common_error_validate: {
				selector: '.common_error_validate',
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator, $field) {
							var validation_type = $field.attr('data-field-type');
							if (value != '') {
								if (validation_type == 'number') {

									if (!new RegExp(/^[. 0-9*#+()-/%$&^@!]*$/).test(value)) {

										return {
											valid: false,
											message: 'Numeric value only'
										};
									}
									else {
										return true;
									}
								} else if (validation_type == 'text') {
									if (!new RegExp(/^[a-zA-Z\s*#+()-/%$&^@!]*$/).test(value)) {
										return {
											valid: false,
											message: 'Alphabet value Only'
										};
									} else {
										return true;
									}
								} else if (validation_type == 'phone_number') {
									var response = phoneValidation(value, home_phone_limit_lang_err_msg);
									if (response != true) {
										return {
											valid: false,
											message: response
										};
									}
									else {
										return true;
									}
								} else if (validation_type == 'both') {
									 if(!new RegExp(/^[. a-zA-Z0-9*#+()-/%$&^@!]+$/).test(value)){
										return {
											valid: false, 
											message: 'Special characters not allowed'
										};
									}else{ 
									return true;
									}
								} else {
									return true;
								}
							} else {
								return true;
							}
						}
					}
				}
			},
			claim_status_radio: {
				trigger: 'change',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').length;
							if (claim_status_option == 0) {
								$(".show_manual_error").show();
								$(".js-claim-status_submit-btn").prop('disabled', true);
								return {
									valid: false,
									message: 'Select Category'
								};


							} else {
								$(".show_manual_error").hide();
								$(".js-claim-status_submit-btn").prop('disabled', false);
								return true;
							}

						}
					}
				}
			},
			insurance: {
				trigger: 'change',
				validators: {
					notEmpty: {
						message: 'Select Insurance'
					}
				}
			},
			followup_rep_name: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var form_type = 'claim_status';
							if (!$('.js-followup-insurance').hasClass('hide')) {
								form_type = 'follow_up';
							}
							if (form_type == 'follow_up') {
								if (value == '') {
									return {
										valid: true,
										message: 'Enter rep  name'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			followup_phone: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var form_type = 'claim_status';
							if (!$('.js-followup-insurance').hasClass('hide')) {
								form_type = 'follow_up';
							}
							if (form_type == 'follow_up') {
								var home_phone_msg = home_phone_limit_lang_err_msg;
								var response = phoneValidation(value, home_phone_msg);
								if (value == '') {
									return {
										valid: true,
										message: 'Enter phone'
									};
								} else if (response != true) {
									return {
										valid: true,
										message: response
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			followup_dos: {
				trigger: 'change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var form_type = 'claim_status';
							if (!$('.js-followup-insurance').hasClass('hide')) {
								form_type = 'follow_up';
							}
							if (form_type == 'follow_up') {
								if (value == '') {
									return {
										valid: true,
										message: 'Enter Date'
									};
								} else {
									var current_date = new Date(value);
									var d = new Date();
									if (d.getTime() < current_date.getTime()) {
										return {
											valid: true,
											message: 'Enter valid date'
										};
									}
									return true;
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_insurance: {
				enabled: false,
				trigger: 'change',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (value == '') {
									return {
										valid: true,
										message: 'Select insurance'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_policy_id: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_group_id_name: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_rep_exec_name: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Special characters not allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_effective_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_filling_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_fax_number_attention: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								var home_phone_msg = home_phone_limit_lang_err_msg;
								var response = phoneValidation(value, home_phone_msg);
								if (response != true) {
									return {
										valid: false,
										message: response
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_electronic_payerid: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_callback_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			/*claim_nis_reference_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if(claim_status_option=='claim_nis'){
								if(!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value!='') {
									return {
										valid: false, 
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},*/
			claim_inprocess_receive_on: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_in_process') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_inprocess_filling_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_in_process') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_inprocess_appeal_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_in_process') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_inprocess_callback_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_in_process') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			/*claim_inprocess_reference_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if(claim_status_option=='claim_in_process'){
								if(!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value!='') {
									return {
										valid: false, 
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},*/
			claim_paid_processed_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_amount: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[1-9]\d*(\.\d+)?$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Enter valid amount'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_allowed_amount: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[1-9]\d*(\.\d+)?$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Enter valid amount'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_coinsurance: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Special characters not allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_copay: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Special characters not allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_deductible: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[1-9]\d*(\.\d+)?$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Enter valid amount'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_eft_check_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_bulk_check_amount: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								if (!new RegExp(/^[1-9]\d*(\.\d+)?$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Enter valid amount'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_paid_cash_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_paid') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_policy_active_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_filing_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_electronic_payerid: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_appeal_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_appeal_fax_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								var home_phone_msg = home_phone_limit_lang_err_msg;
								var response = phoneValidation(value, home_phone_msg);
								if (response != true) {
									return {
										valid: false,
										message: response
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			/*claim_denied_reference_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if(claim_status_option=='claim_denied'){
								if(!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value!='') {
									return {
										valid: false, 
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},*/
			claim_denied_callback_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			left_voice_msg: {
				enabled: false,
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'left_voice_message') {
								if (value == '') {
									return {
										valid: false,
										message: 'Enter voice message'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_pending_receive_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_pending') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_denied_policy_active_date_to: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_denied') {
								var current_date = new Date(value);
								var d = new Date();
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_effective_date_from: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								var current_date = new Date(value);
								var d = new Date();
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_nis_effective_date_to: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_nis') {
								var current_date = new Date(value);
								var d = new Date();

								return true;
							}
							return true;
						}
					}
				}
			},
			claim_pending_reason: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_pending') {
								if (!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_pending_filing_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_pending') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			claim_pending_appeal_limit: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_pending') {
								if (!new RegExp(/^[0-9]+$/).test(value) && value != '') {
									return {
										valid: false,
										message: 'Numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			/*claim_pending_reference_number: {
				trigger: 'change keyup',
				validators: {
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if(claim_status_option=='claim_pending'){
								if(!new RegExp(/^[a-zA-Z0-9]+$/).test(value) && value!='') {
									return {
										valid: false, 
										message: 'Alpha numeric only allowed'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},*/
			claim_pending_callback_date: {
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'
					},
					callback: {
						callback: function (value, validator) {
							var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
							if (claim_status_option == 'claim_pending') {
								var current_date = new Date(value);
								var d = new Date();
								if (d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: 'Enter valid date'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function (eve) {
		eve.preventDefault();
		$("#js-claim-status_submitbtn-footer").removeClass('show').addClass('hide');
		$("#js-claim-status_submitbtn-load-footer").removeClass('hide').addClass('show');
		var form_modue_type = 'claim_status';
		if ($('.js-followup-insurance').hasClass('show')) {
			form_modue_type = 'follow_up';
		}
		var curr_claim_val = $('#claim_status_notes_form').find("form").attr("data-formid");
		var data = $('#bootstrap-validator-claim-status-chage-form').serialize();
		data += '&claim_id=' + curr_claim_val + '&form_modue_type=' + form_modue_type;
		CKEDITOR.instances['areditor1'].setData('');
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimstatusnotesadded',
			type: "POST",
			data: data,
			success: function (resp) {
				
				//$("#js_status_notes_part2, #js_status_notes_part1, #js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
				//$("#js_arnotes_edit_part, #js-claim-status_final-submitbtn-footer").removeClass('hide').addClass('show');			
				CKEDITOR.instances['areditor1'].setData(resp);
				setTimeout(function () { $('#js_claim_final_save_btn').click(); }, 30);


				/*
				claim_tab_reload_process_indivual(curr_claim_val);
				$('#claim_status_notes_form').modal('hide');
				js_alert_popup("Claim status changed successfully");
				$("#js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
				$("#js-claim-status_submitbtn-footer").removeClass('hide').addClass('show');
				*/
			}
		});
	});
}

$(document).on('click', "#js_claim_reedit_back_btn", function (e) {
	$("#js_arnotes_edit_part, #js-claim-status_final-submitbtn-footer, #js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
	$("#js_status_notes_part1, #js-claim-status_submitbtn-footer").removeClass('hide').addClass('show');

	var cur_claim_nte_type = $("#claim_status_notes_form .modal-title").html();
	if (cur_claim_nte_type == 'Followup Template') {
		$("#js_status_notes_part2").removeClass('hide').addClass('show');
	} else {
		$("#js_status_notes_part2").removeClass('show').addClass('hide');
	}
});

$(document).on('click', "#js_claim_final_save_btn", function (e) {
	var ar_notes_val = CKEDITOR.instances.areditor1.getData();
	var curr_claim_val = $('#claim_status_notes_form').find("form").attr("data-formid");
	var claim_status_option = $('input[type=radio][name="claim_status_radio"]:checked').val();
	var user_id = $('select[name="user_id"]').val();
	$.ajax({
		url: api_site_url + '/patients/armanagement/getclaimstatusfinalnotesadded',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: { "ar_notes_val": ar_notes_val, "curr_claim_val": curr_claim_val, "claim_status_option": claim_status_option, "user_id": user_id },
		success: function (resp) {
			claim_tab_reload_process_indivual(curr_claim_val);
			$('#claim_status_notes_form').modal('hide');
			js_sidebar_notification("success", "Template added successfully");
			$("#js_arnotes_edit_part, #js-claim-status_final-submitbtn-footer, #js-claim-status_submitbtn-load-footer").removeClass('show').addClass('hide');
			$("#js_status_notes_part1, #js-claim-status_submitbtn-footer").removeClass('hide').addClass('show');
		}
	});
});

$(document).on('click', ".js-claim-status_submit-btn", function (e) {
	e.preventDefault();
	$('#bootstrap-validator-claim-status-chage-form').data("bootstrapValidator").resetForm();
	$('#bootstrap-validator-claim-status-chage-form').bootstrapValidator('validate');
	$(this).off('click');
});

$(document).on('ifChecked change', "input[name='claim_status_radio']", function () {
	claimstatuschageformvalidation();
	var form_tag = $('#bootstrap-validator-claim-status-chage-form');
	form_tag.data("bootstrapValidator").resetForm();
	$(".followup-box").not("." + $(this).attr("value")).hide();
	$(".show_manual_error").hide();
	$(".js-claim-status_submit-btn").prop('disabled', false);
	$("." + $(this).attr("value")).show();
});

$(document).on('click', ".js_claim_edit_charge_link", function () {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');

	$("#claim-charge-modal-popup .modal-body").html('');
	var target = api_site_url + '/patients/armanagement/getclaimchargeeditdetails/' + curr_claim_val_arr[1];
	$('#claim-charge-modal-popup').modal({
		show: 'true'
	});
	$("#claim-charge-modal-popup .modal-body").load(target, function () {
		$("#claim-charge-modal-popup select.select2.form-control").select2();
		/*$('#claim-charge-modal-popup input[type="checkbox"], input[type="radio"]').iCheck({
		   checkboxClass: 'icheckbox_flat-green',
		   radioClass: 'iradio_flat-green'
		});*/

		if ($('input[name="admit_date"]').length) {
			getmodifierandcpt();
		}
		changetext();
		patientbalancenew();

		icd_length = $('#js-count-icd input[type="text"]').filter(function () {
			return !!this.value;
		}).length;

		callajaxfunction();
		// Make readonly field as false for icd list from E- suberbill as well as edit section
		k = 1;
		$('.js-icd').each(function () {
			if ($(this).val() != '') {
				$(this).attr('readonly', false);
				k++;
			}
			$('#icd' + k).attr('readonly', false);
		});
		// ICD poniters must not be reaonly if it has values
		l = 0;
		$('.icd_pointer').each(function (i) {
			if ($(this).val() != '') {
				$(this).attr('readonly', false);
				//$(this).nextAll('input').first().attr('readonly', false); 			
				$(this).parent().next().find('input').attr('readonly', false);
				$(this).removeAttr('tabindex');
			}
		});

		$('.js-modifier').each(function () {
			next_val = $(this).attr('data-cpt');
			if (next_val && next_val != 'undefined') {
				$(this).attr('readonly', false);
			}
			if ($(this).val() != '') {
				$(this).attr('readonly', false);
				$(this).parent().next().find('input').attr('readonly', false);
			}
		})

		calltimepicker();
		changetext();

		claimeditchargeformvalidation();
		var form_tag_charge = $('#js-bootstrap-validator');
		form_tag_charge.data("bootstrapValidator").resetForm();
	});
	/*************************************************************************************/
});

function claimeditchargeformvalidation() {
	var form_validator = $('#js-bootstrap-validator').bootstrapValidator({
		message: 'This value is not valid',
		//excluded: ':disabled',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			doi: {
				message: '',
				validators: {
					callback: {
						message: charge_grater_than_admit,
						callback: function (value, validator, $field) {
							var admit_date = $('input[name="admit_date"]').val();
							var month = value.substring(0, 2);
							var date = value.substring(3, 5);
							var year = value.substring(6, 10);
							var dateToCompare = new Date(year, month - 1, date);
							var currentDate = new Date();
							var doi = isFutureDate(value);
							comp_val = 1;
							comp_val = (admit_date != '') ? compareDate(value, admit_date) : comp_val;
							var current_date = new Date(value);
							var d = new Date();
							if (value != '' && current_date == "Invalid Date") {
								return {
									valid: false,
									message: date_format,
								};
							} else if (value != '' && d.getTime() < current_date.getTime()) {    // Should not be future the date
								return {
									valid: false,
									message: not_future,
								};
							} else if (!comp_val && value != '' && admit_date != '') {
								return {
									message: charge_grater_than_admit,
									valid: false
								}
							} else if (dateToCompare > currentDate && admit_date != '') {
								return {
									valid: false,
									message: charge_grater_than_admit,
								}
							} else {
								return true;
							}
						},
					}
				}
			},
			js_charge_amt: {
				selector: '.js_charge_amt',
				validators: {
					callback: {
						message: greater_zero_amt,
						callback: function (value, validator, $field) {
							var get_field = $field.parents(".js-append-parent li").attr("id");
							var value = $("#" + get_field + " .js_charge_amt").val();
							if (value != '' && parseFloat(value) == parseFloat(0)) {
								return false;
							}
							return true;
						}
					}
				}
			},
			from_dos: {
				message: '',
				selector: '.from_dos',
				validators: {
					callback: {
						message: 'Dos must be given',
						callback: function (value, validator, $field) {
							var get_field = $field.parents(".js-append-parent li:nth-child(1)").attr("id");
							var start_date = $("#" + get_field + " .js_from_date").val();
							if (start_date == '') {
								return false;
							}
							return true;
						}
					}
				}
			},
			to_dos: {
				message: '',
				selector: '.to_dos',
				validators: {
					callback: {
						message: 'Dos to date must be given',
						callback: function (value, validator, $field) {
							var get_field = $field.parents(".js-append-parent li:nth-child(1)").attr("id");
							var start_date = $("#" + get_field + " .js_to_date").val();
							if (start_date == '') {
								return false;
							}
							return true;
						}
					}
				}
			},
			billing_provider_id: {
				message: '',
				validators: {
					notEmpty: {
						message: charge_billing_provider
					}
				}
			},
			rendering_provider_id: {
				message: '',
				validators: {
					notEmpty: {
						message: charge_rendering_provider
					}
				}
			},
			hold_reason_id: {
				enabled: false,
				message: '',
				validators: {
					notEmpty: {
						message: '{{ trans("practice/patients/billing.validation.hold_reason_id") }}'
					},
				}
			},
			other_reason: {
				enabled: false,
				message: '',
				validators: {
					callback: {
						message: 'Enter the reason',
						callback: function (value, validator, $fields) {
							holereason = $('#js-hold-reason option:selected').val();
							if (holereason == 0) {
								return false;
							} else {
								return true;
							}
						}
					}
				}
			},
			icd1: {
				message: '',
				validators: {
					notEmpty: {
						message: charge_icd1
					}
				}
			},
			facility_id: {
				message: '',
				validators: {
					notEmpty: {
						message: charge_facility_id
					}
				}
			},
			insurance_id: {
				message: '',
				validators: {
					notEmpty: {
						message: charge_insurance_id
					}
				}
			},
			pos_id: {
				message: '',
				trigger: 'keyup change',
				validators: {
					callback: {
						message: charge_pos_name,
						callback: function (value, validator, $field) {
							pos_val = $("#pos_id :selected").text();
							pos_code = [6, 8, 21, 31, 51, 61, 34];     // Place of service codes that must need admission date
							if ($.inArray(parseInt(pos_val), pos_code) > -1) {
								enabledisablevalidator('enableFieldValidators', 'admit_date', true);
								return true;
							} else {
								enabledisablevalidator('enableFieldValidators', 'admit_date', false);
								return true;
							}
						}
					}
				}
			},
			admit_date: {
				message: '',
				trigger: 'change',
				validators: {
					callback: {
						message: charge_admit_date,
						callback: function (value, validator, $field) {
							pos_val = $("#pos_id :selected").text();
							pos_code = [6, 8, 21, 31, 51, 61, 34];
							var m = validator.getFieldElements('discharge_date').val();
							var current_date = new Date(value);
							var d = new Date();
							var n = value;
							dos = $("#small_date").val();
							compval = 1;
							compval = (dos != '') ? compareDate(value, dos) : compval;  // check with dos value                                         
							var is_valid_date = validDateCheck(value);
							if (value != '' && !is_valid_date) {
								return {
									valid: false,
									message: date_format,
								};
							} else if (value != '' && d.getTime() < current_date.getTime()) {    // Should not be future the date
								return {
									valid: false,
									message: not_future,
								};
							} else if ($.inArray(parseInt(pos_val), pos_code) > -1 && value == '') {  // For few place of service admit date was required
								return {
									message: charge_admit_date,
									valid: false
								}
							}
							removeerrormessage(value, 'js_from_date');
							if (value && compval) {
								return true;
							} else if (value != '') {
								return false;
							}
							if (m != '') {
								return (n == '') ? false : true;
							}
							return true;
						}
					}
				}
			},
			discharge_date: {
				message: '',
				trigger: 'change',
				validators: {
					callback: {
						message: invalid_end_date,
						callback: function (value, validator) {
							var current_date = new Date(value);
							var d = new Date();
							var is_valid_date = validDateCheck(value);
							if (value != '' && !is_valid_date) {
								return {
									valid: false,
									message: date_format,
								};
							} else if (value != '' && d.getTime() < current_date.getTime()) {    // Should not be future the date
								return {
									valid: false,
									message: not_future,
								};
							}
							var m = validator.getFieldElements('admit_date').val();
							var n = value;
							dos = $("#big_date").val();
							compval_dis = 1;
							compval_dis = (dos != '' && value != '') ? compareDate(dos, value) : compval_dis;  // check with dos value                                      
							var current_date = new Date(n);
							if (current_date != 'Invalid Date' && n != '' && m != '' && compval_dis) {
								var getdate = daydiff(parseDate(m), parseDate(n));
								return (getdate >= 0) ? true : false;
							} else {
								if (!compval_dis) {
									return {
										valid: false,
										message: '{{ trans("practice/patients/charges.validation.dos") }}',
									};
								}
								return true;
							}
						}
					}
				}
			},
			copay: {
				message: '',
				validators: {
					callback: {
						message: charge_copay,
						callback: function (value, validator, $field) {
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_no');
							if (value !== '') {
								enabledisablevalidator('enableFieldValidators', 'copay_amt', true);
								return true;
							} else {
								if ($("input[name='copay_amt']").val() != '') {
									return false;
								}
								$("input[name='copay_amt']").val('');
								enabledisablevalidator('enableFieldValidators', 'copay_amt', false);
								return true;
							}
						}
					}
				}
			},
			copay_amt: {
				//enabled: false,
				message: '',
				validators: {
					numeric: {
						message: only_numeric_lang_err_msg,
					},
					callback: {
						message: charge_copay_amt,
						callback: function (value, validator, $field) {
							mode = $('select[name=copay]').val();
							if (value != '' && value == 0) {
								return {
									valid: false,
									message: charge_not_zero
								}
							}
							return (value == '' && mode != '') ? false : true;
						}
					}
				}
			},
			anesthesia_start: {
				message: '',
				validators: {
					callback: {
						message: '{{ trans("practice/patients/billing.validation.anesthesia_start") }}',
						callback: function (value, validator, $field) {
							var endTime = validator.getFieldElements('anesthesia_stop').val();
							var startTime = validator.getFieldElements('anesthesia_start').val();
							if (endTime === '') {
								return true;
							}
							if ((startTime != '' && endTime != '')) {
								returnval = Compare();
								return {
									valid: returnval['return'],
									message: returnval['message'],
								}
							}
							return false;
						}
					}
				}
			},
			anesthesia_stop: {
				validators: {
					callback: {
						message: '{{ trans("practice/patients/billing.validation.anesthesia_stop") }}',
						callback: function (value, validator, $field) {
							var endTime = validator.getFieldElements('anesthesia_stop').val();
							var startTime = validator.getFieldElements('anesthesia_start').val();
							if (startTime == '') {
								return true;
							}
							if ((startTime != '' && endTime != '')) {
								returnval = Compare();
								return {
									valid: returnval['return'],
									message: returnval['message'],
								}
							}
							return false;
						}
					}
				}
			},
			check_no: {
				validators: {
					callback: {
						message: empty_check_no,
						callback: function (value, validator, $field) {

							mode = $('select[name=copay]').val();
							check_number_exist = $('input[name="checkexist"]').val();
							lengthval = '{{ Config::get("siteconfigs.payment.check_no_minlength") }}';
							if (mode == 'Check') {
								if (value == '') {
									return {
										valid: false,
										message: empty_check_no
									}
								} else if (value != '' && !checknumbervalidation(value)) {
									return {
										valid: false,
										message: alphanumeric_lang_err_msg
									}
								} else if (value != '' && value.length < lengthval) {
									return {
										valid: false,
										message: checkminlength,
									}
								} else if (value != '' && check_number_exist == 1) {
									return {
										valid: false,
										message: checkexist
									}
								}
							}
							return true;
						}
					},
				}
			},
			check_date: {
				trigger: 'change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: date_format,
						callback: function (value, validator) {
							mode = $('select[name=copay]').val();
							var current_date = new Date(value);
							var d = new Date();
							if (value != '' && d.getTime() < current_date.getTime()) {    // Should not be future the date
								return {
									valid: false,
									message: charge_doi_future,
								};
							}
							if (mode != '') {
								return (value == '') ? false : true;
							}
							return true;
						}
					}
				}
			},
			card_type: {
				validators: {
					callback: {
						message: charge_card_notempty,
						callback: function (value, validator) {
							mode = $('select[name=copay]').val();
							if (mode == 'Credit') {
								return (value == '') ? false : true;
							}
							return true;
						}
					}
				}
			},
		},
	});
}


$(document).on('click', ".js-claimeditcharge-form-submit", function (e) {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var curr_claim_val = curr_claim_val_arr[1];
	$('#js-bootstrap-validator').data("bootstrapValidator").resetForm();
	$('#js-bootstrap-validator').bootstrapValidator('validate');
	$('#js-bootstrap-validator').on('success.form.bv', function (eve) {
		eve.preventDefault();
		$('#claim_editcharge_footer_' + curr_claim_val).html('<i class="fa fa-spinner fa-spin">');
		var data = $('#js-bootstrap-validator').serialize();
		data += "&claim_id=" + curr_claim_val;
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimchargeeditprocess',
			type: "POST",
			data: data,
			success: function (resp) {
				//$('#claim-charge-modal_'+curr_claim_val).modal('hide');
				$('#claim-charge-modal-popup').modal('hide');
				claim_tab_reload_process_indivual(curr_claim_val);
				js_alert_popup("Charge edited successfully");
			}
		});
	});
	$(this).off('click');
});

$(document).on('click', ".js_claimdetlink", function (e) {
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	$(this).parents('ul').find("li").removeClass('active');
	$(".tab-content").find("div").removeClass('active');
	$(this).closest("li").addClass('active');
	if (curr_claim_val_arr[1] != 'main0') {
		var curr_claim_val = curr_claim_val_arr[1];
		var data = "selected_claim_id_values=" + curr_claim_val + "&prev_sel_claim_id_values=&tab_type=indivual";
		processingImageShow('#claim-tab-info_' + curr_claim_val, "show");
		$.ajax({
			url: api_site_url + '/patients/armanagement/getclaimtabdetails',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: data,
			success: function (res) {
				var result = res.split('^^::^^');
				var added_claim_tab_details = result[2];
				processingImageShow('#claim-tab-info_' + curr_claim_val, "hide");
				$('#claim-tab-info_' + curr_claim_val).html(added_claim_tab_details);
				if (result.length == 4) {
					$('.js_indivual_insurance').html(result[3]);
				}
				$("#notes_" + curr_claim_val).removeClass('out').addClass('in');
				$('.ar-notes').slimScroll({
					height: '150px'
				});
				$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });
				$("select.select2.form-control").select2();
			}
		});
	}
	$("#claim-tab-info_" + curr_claim_val_arr[1]).addClass('active');
	$('#selected_curr_claim_id').val(curr_claim_val_arr[1]);
});
//Review: after select document option then select another one document selection color not changed.
$(document).on('click', 'li.active.documents-view', function() {
      $(this).removeClass('active');
    
});

$(document).on('click', ".js-billingdet-popup-link", function (e) {
	var claim_id = $(this).attr('data-id');
	var billingdet_popup_height = $('#js-billingdet-popup_' + claim_id).height();
	$(".modal-title").text('Billing Details');
	if (billingdet_popup_height > 540) {
		$('#js-billingdet-popup_' + claim_id).slimScroll({
			height: '540px'
		});
	}
});

$(document).on('click', ".js_arfullnotes_link", function (e) {
	var claim_id = $(this).attr('data-id');
	var billingdet_popup_height = $('#full-notes-details_' + claim_id).height();
	if (billingdet_popup_height > 480) {
		$('#full-notes-details_' + claim_id).slimScroll({
			height: '480px'
		});
	}
});

$(document).on('click', ".js_denial_search_btn", function (e) {
	var curr_id = $(this).attr('id');
	var curr_id_arr = curr_id.split('-');
	var claim_number = curr_id_arr[1];
	triggerdenialsearch(claim_number)
	/*console.log("called "+claim_number);
	var denial_search_str = $('#denial_details_'+claim_number+' input[name="denial_search_str"]').val();
	var sel_code_val = $('input:checkbox:checked.js_denial_frm_denial_codes_'+claim_number).map(function () { return this.value; }).get();
	$('.denail_codes_list_part_'+claim_number).html('<div class="js_loading text-center"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>'); 
	var data = "claim_number="+claim_number+"&denial_search_str="+denial_search_str+"&sel_code_val="+sel_code_val;
	$.ajax({
		url: api_site_url+'/patients/armanagement/getdenialsearchlist',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		success: function(res){
			if(res!=''){	
				var denial_class = 'denail_codes_list_part_'+claim_number;			
				//$('#ar-denials-new_'+claim_number).closest('div .slimScrollDiv').css("height", "250px");
				//$('#ar-denials-new_'+claim_number).css("height", "250px");
				console.log("classsdsfdsfss");
				console.log($('tbody[class="'+denial_class+'"]').attr("class"));
				$('tbody[class="'+denial_class+'"]').html(res);		// To fix mulitple claims tab open time not loaded denial issue.
				//$('.denail_codes_list_part_'+claim_number).html(res);				
				
				setTimeout(function(){
					$('.js_denial_codes').iCheck({ checkboxClass: 'icheckbox_flat-green' }); 
					$("select.select2.form-control").select2();		    				
				}, 100)
							
				// for set height for the block 
			} else {
				$("tbody[class^='denail_codes_list_part_']").html('<div class="text-center">No Records Found</div>');								
				//$('.denail_codes_list_part_'+claim_number).html('<div class="text-center">No Records Found</div>');
			}
		}
	});*/
});

/*
 * This function added for armanagement denails posting maximum 3 deanils code
 *
 */
$(document).on('click', '.js_denial_codes', function () {
	if ($('input:checkbox.js_denial_codes:checked').length > 3) {
		setTimeout(function () {
			$(this).prop("checked", false);
		}, 50);
		js_alert_popup("Maximum denials are chosen");
		return false;
	}
});


function triggerdenialsearch(claim_number) {
	var denial_search_str = $('#denial_details_' + claim_number + ' input[name="denial_search_str"]').val();
	var sel_code_val = $('input:checkbox:checked.js_denial_frm_denial_codes_' + claim_number).map(function () { return this.value; }).get();
	$('.denail_codes_list_part_' + claim_number).html('<div class="js_loading text-center"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
	var data = "claim_number=" + claim_number + "&denial_search_str=" + denial_search_str + "&sel_code_val=" + sel_code_val;
	$.ajax({
		url: api_site_url + '/patients/armanagement/getdenialsearchlist',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		success: function (res) {
			if (res != '') {
				var denial_class = 'denail_codes_list_part_' + claim_number;
				$('tbody[class="' + denial_class + '"]').html(res);		// To fix mulitple claims tab open time not loaded denial issue.				
			} else {
				$("tbody[class^='denail_codes_list_part_']").html('<div class="text-center">No Records Found</div>');
			}
		}
	});
}

$(document).on('click', '.js_arsearch_reset', function () {
	$('input:text').val("");
	$(".select2").select2("val", "");
	$.AdminLTE.boxWidget.activate();
	$('#js_ar_search_form').bootstrapValidator('revalidateField', $('.search_start_date'));
	$('#js_ar_search_form').bootstrapValidator('revalidateField', $('.search_end_date'));
	$('#js_ar_search_form').bootstrapValidator('disableSubmitButtons', false);
	searcharform();
	return false;
});

$('#js_ar_search_form').bootstrapValidator({
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
					message: "date_valid_lang_err_msg"
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
					message: "date_valid_lang_err_msg"
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
	searcharform()
	$('#js_ar_search_form').bootstrapValidator('disableSubmitButtons', false);
});

$(document).on('keyup change', '#billed_option', function () {
	fv = $('#js_ar_search_form').data('bootstrapValidator');
	if ($(this).val() != '') {
		fv.enableFieldValidators('billed', true).revalidateField('billed');
	} else {
		fv.enableFieldValidators('billed', false).revalidateField('billed');
	}
});

$(document).on('keyup', '#billed', function () {
	fv = $('#js_ar_search_form').data('bootstrapValidator');
	if ($(this).val() != '') {
		fv.enableFieldValidators('billed_option', true).revalidateField('billed_option');
	} else {
		fv.enableFieldValidators('billed_option', false).revalidateField('billed_option');
	}
});

function searcharform() {
	var myform = $('#js_ar_search_form');
	var serialized = myform.serialize();
	var url = $('#js_ar_search_form').attr('action');
	processingImageShow("#js_artable_listing", "show");
	$.ajax({
		url: url,
		type: 'POST',
		data: serialized,
		success: function (result_values) {
			processingImageShow("#js_artable_listing", "hide");
			$('#js_artable_listing').html(result_values);
			$(".claims").DataTable({
				"paging": true,
				"lengthChange": false,
				"searching": true,
				"ordering": true,
				"info": true,
				"order": [1, 'asc'],
				"autoWidth": false
			});
			$.AdminLTE.boxWidget.activate();
		}
	});
}

$('.datepicker').datepicker({ format: "yyyy-mm-dd", autoclose: true });

/*** Starts - Check/Uncheck checkbox ***/
$(document).on('ifToggled change', '#js-select-all', function () {
	$("input:checkbox.js-select-all-sub-checkbox").prop('checked', $(this).prop("checked"));
	//$('input:checkbox').iCheck('update');
	if (!($(this).prop("checked"))) {
		//$('.js-dynamic-tab-menu ul li:not(:first)').remove();
		$('#selected_claim_ids_arr').val('');
	}
});

$(document).on('ifToggled change', '.js-select-all-sub-checkbox', function () {
	if ($('.js-select-all-sub-checkbox:checked').length == $('.js-select-all-sub-checkbox:checkbox').length) {
		$('#js-select-all').prop('checked', true);
	} else {
		$('#js-select-all').prop('checked', false);
	}
	//$('input:checkbox').iCheck('update');
});
/*** Ends - Check/Incheck checkbox ***/

$(document).on('click', ".js_claim_edit_charge_link_new1", function () {
	var edit_ch_url = $(this).attr("data-url");
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var pmt_type = $(this).attr("data-type");
	var claim_no = $(this).attr("data-claim");
	if (pmt_type != '')
		localStorage.setItem('pmt_type', pmt_type);
	if (claim_no != '')
		localStorage.setItem('claim_no', claim_no);
	// Commented the below lines to avoid the tabs active getting retain for issue #1814 
	/*
	$('.js-dynamic-tab-menu ul li:not(:first)').removeClass('active');
	$('.js-dynamic-tab-menu ul li:first').addClass('active'); 
	$("#claim-tab-info_"+curr_claim_val_arr[1]).removeClass('active');
	$("#claim-tab-info_main0").addClass('active'); 
	*/
	window.open(edit_ch_url, '_blank');
});

$(document).on('click', ".js_hold_common_link", function () {
	var hold_type = $(this).attr("data-url");
	var hold_id = $(this).attr("data-id");
	var curr_id = $(this).attr('id');
	var curr_claim_val_arr = curr_id.split('_');
	var data = "hold_type=" + hold_type + "&hold_id=" + hold_id;
	processingImageShow('#claim-tab-info_' + curr_claim_val_arr[1], "show");
	$.ajax({
		url: api_site_url + '/patients/armanagement/claimholdprocess',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		success: function (resp) {
			processingImageShow('#claim-tab-info_' + curr_claim_val_arr[1], "hide");
			claim_tab_reload_process_indivual(curr_claim_val_arr[1]);
			if (hold_type == 'claim_hold') {
				js_alert_popup("Claims hold successfully");
			}
			if (hold_type == 'statement_hold') {
				js_alert_popup("Statement hold successfully");
			}
		}
	});
});

$(document).on("keyup", "input[type='search']", function () {
	if ($(this).val() != '') {
		$('tr.js-listclaim').removeClass("ar-hide-class");
	} else {
		$('tr.js-listclaim').addClass("ar-hide-class");
	}
})




/* 
 *
 * Showing cob details in alert
 * @ Author  : Selvakumar
 * @ Created : 28 FEB 18
 */


$(document).on('click', '.js-show-ins', function () {
	var patient_id = $(this).attr("data-patient-id");
	$.ajax({
		url: api_site_url + '/patients/armanagement/getpatientinsurance/' + patient_id,
		type: "GET",
		success: function (data) {
			$(".modal-title").text('Insurances');
			js_alert_popup(data);
		}
	});
});


/* 
*
* Hold the patient statement
* @ Author  : Selvakumar
* @ Created : 28 FEB 18
*/

$(document).on('click', '.js-ar-hold', function () {
	var patient_id = $("#statement_hold").find("#patientId").val(); // $(this).attr("data-patient-id");
	var hold_reason = $("#statement_hold").find("#hold_reason").val();
	var hold_release_date = $("#statement_hold").find("#hold_release_date").val();
	$.ajax({
		url: api_site_url + '/patients/armanagement/setpatienthold/' + patient_id + '?hold_reason=' + hold_reason + '&hold_release_date=' + hold_release_date,
		type: "GET",
		success: function (data) {
			js_sidebar_notification('success', 'Statement status updated.');
			setTimeout(function () {
				var class_details = $('.nav-tabs').find('li.active').attr('class');
				var ret = class_details.split("_");
				var result = ret[1].split(" ");
				// Empty the selected values
				$("#statement_hold").find("#hold_reason").select2('val', '');
				$("#statement_hold").find("#hold_release_date").val('');
				$("#statement_hold").modal("hide");
				$('span#claimdetlink_' + $.trim(result[0])).click();
			}, 1000);
		}
	});
});


/* 
*
* Unhold the patient statement
* @ Author  : Selvakumar
* @ Created : 29 JUN 18
*/

$(document).on('click', '.js-ar-unhold', function () {
	var patient_id = $(this).attr("data-patient-id");

	$.ajax({
		url: api_site_url + '/patients/armanagement/setpatientunhold/' + patient_id,
		type: "GET",
		success: function (data) {
			js_sidebar_notification('success', 'Statement status updated.');
			setTimeout(function () {
				var class_details = $('.nav-tabs').find('li.active').attr('class');
				var ret = class_details.split("_");
				var result = ret[1].split(" ");
				$('span#claimdetlink_' + $.trim(result[0])).click();
			}, 1000);
		}
	});
});
//If Patient statement not available and preview is clicked, show some notifications like statement unavailable
$(document).on('click','.js_submit_type',function(e) {
	
	var type = $(this).data('name');
	var patientid = $(this).data('id');
	var unique = $(this).data('unique');
	var module = $(this).data('module');	
	var bulkobj = $(this);
	
	// Get payment message in bulk statement
	var get_message = '';
	if($('textarea[name=paymentmessage_1]').val() != undefined)
	{
		var get_message = '::'+$('textarea[name=paymentmessage_1]').val();
	}
	
	$('.js_loading'+unique).removeClass('hide');
	$('.js_hide'+unique).addClass('hide');
	
	$.ajax({
		type: "GET",
		url: api_site_url+'/individualstatementtype/'+patientid+'/'+type+get_message,	
		success: function(result){
			$('.js_loading'+unique).addClass('hide');
			$('.js_hide'+unique).removeClass('hide');
			if(isJson(result)) {
				var obj = jQuery.parseJSON(result);
					
				if(obj.status == 'failure'){
				// change js_alert_popup to js_sidebar_notification 
				// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh					
					js_sidebar_notification('failure',obj.msg);
					$('#patientstatement_model').css('z-index','999');
				}
			
				if(obj.status == 'success') {
					if(type == 'preview'){
						var urlpage = obj.msg;
						setTimeout(function() {
							window.open(urlpage);
						}, 5);
					}
					if(type == 'sendstatement' || type == 'sendcsvstatement' || type == 'sendxmlstatement'){
						if(module == 'bulk') {
							bulkobj.parents('tr').remove();
						}
						//window.location = api_site_url+'/individualstatementdownload/'+obj.filename+'/'+patientid+'/'+obj.msg;
						var urlpage = obj.msg;
						setTimeout(function() {
							if(type == 'sendxmlstatement') {
								var file_path = urlpage;
								var a = document.createElement('A');
								a.href = urlpage;
								a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
								document.body.appendChild(a);
								a.click();
								document.body.removeChild(a);
							} else {
								window.open(urlpage);
							}						
						}, 5);
					}
					if(type == 'emailstatement'){
						if(module == 'bulk') {
							bulkobj.parents('tr').remove();
						}
						// change js_alert_popup to js_sidebar_notification 
						// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh
						js_sidebar_notification('success',obj.msg);
						$('#patientstatement_model').css('z-index','999');
					}	
				}
			} else {
				// change js_alert_popup to js_sidebar_notification 
				// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh
				js_sidebar_notification('failure',"Some thing went wrong. Please try after some times");
			}
		}
	});		
});
/** Commented since same function available in patient_statment.js
$(document).on('click','.js_submit_type111',function(e) {
	
   var type = $(this).data('name');
   var patientid = $(this).data('id');
   var unique = $(this).data('unique');
   var module = $(this).data('module');	
   var bulkobj = $(this);
	
   // Get payment message in bulk statement
   var get_message = '';
   if($('textarea[name=paymentmessage_1]').val() != undefined)
   {
	   var get_message = '::'+$('textarea[name=paymentmessage_1]').val();
   }
	
   $('.js_loading'+unique).removeClass('hide');
   $('.js_hide'+unique).addClass('hide');
	
   $.ajax({
	   type: "GET",
	   url: api_site_url+'/individualstatementtype/'+patientid+'/'+type+get_message,	
	   success: function(result){
		   $('.js_loading'+unique).addClass('hide');
		   $('.js_hide'+unique).removeClass('hide');

		   if(isJson(result)) {
				   var obj = jQuery.parseJSON(result);
			   	
			   if(obj.status == 'failure'){
				   js_alert_popup(obj.msg);
				   $('#patientstatement_model').css('z-index','999');
			   }
		   	
			   if(obj.status == 'success') {
				   if(type == 'preview'){
					   var urlpage = obj.msg;
					   setTimeout(function() {
						   window.open(urlpage);
					   }, 5);
				   }
			   	
				   if(type == 'sendstatement'){
					   if(module == 'bulk') {
						   bulkobj.parents('tr').remove();
					   }
					   window.location = api_site_url+'/individualstatementdownload/'+obj.filename+'/'+patientid+'/'+obj.msg;
				   }

				   if(type == 'emailstatement'){
					   if(module == 'bulk') {
						   bulkobj.parents('tr').remove();
					   }
					   js_alert_popup(obj.msg);
					   $('#patientstatement_model').css('z-index','999');
				   }	

			   }
		   }
	   }
   });		
});
*/

$(document).on("click", ".js_denial_close", function () {
	var data_claim_number = $(this).attr("data_claim_number");
	$("#denial_details_" + data_claim_number).find(".denail_codes_list_part_" + data_claim_number).html("");

});

/*
	Armanagement Change Insurance Responbility 
 */

$(document).on("click", '#js_next_ar_responsb', function () {
	var claim_no = $(this).attr('data-claim-no');
	var patientId = $(this).attr('data-patientId');
	var claimId = $(this).attr('data-claimId');
	var claimNo = $(this).attr('data-claimNo');
	var errorMsg = $(this).attr('data-error-msg');
	var insurance = $('#js_denial_frm_denial_insurances_' + claim_no).val();
	if (insurance == '') {
		if (errorMsg == '' || errorMsg==null){
			//js_sidebar_notification('error', 'Select responsibility');
		}else {
			js_sidebar_notification('error', errorMsg);
			$('#next_resp_ar').hide();
		}
		$('#next_resp_ar').removeClass('hide');
	} else {
		$.ajax({
			url: api_site_url + '/patients/armanagement/changeClaimResponsibility',
			headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",
			data: { 'patientId': patientId, 'claimId': claimId, 'insuranceId': insurance, '_token': $('input[name="_token"]').attr('value') },
			dataType: 'json',
			success: function (res) {
				if (res.status == 'success') {
					js_sidebar_notification(res.status, res.message);
					$("#claimdetlink_" + $.trim(claimNo)).click();
				} else {
					js_sidebar_notification(res.status, res.message);
				}
				$('#next_resp_ar').addClass('hide');
			}
		});

	}
});

$(document).on('change', '.js_Ar_insurance', function () {
	if ($(this).val() == '') {
		$('#next_resp_ar').removeClass('hide');
	} else {
		$('#next_resp_ar').addClass('hide');
	}
});

/*
	Armanagement Change Insurance Responbility 
 */


/* 
* Author : Selvakumar V
* Desc : Showing the followup history in popup
* Created On : 25-Apr-2018
*/

$(document).on('click', '.js_showing_history', function () {
	var target = $(this).attr("data-url");
	var claim_no = $(this).attr('data-claimno');
	$("#show_followup_history .modal-body").load(target, function () {
		$("#show_followup_history").modal("show");
		$("#show_followup_history .modal-title").html("Claim No :" + claim_no);
	});
});

$(document).on('click', '.js_statement_hold', function () {
	var patient_id = $(this).attr("data-patient-id");
	$("#patientId").val(patient_id);
	$("#statement_hold").modal("show");
});


$(document).on('focus', '#hold_release_date', function () {
	var id_name = $(this).attr('id');
	$("#" + id_name).datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		minDate: "0M+1",
		onClose: function (selectedDate) {
			//
		}
	});
});

$(document).ready(function () {
	//$("input['data-field-type=number']").removeClass('allownumericwithdecimal');
	// $('.common_error_validate ').attr('data-field-type', 'phone_number').addClass('dm-phone');
	// $("input[data-field-type=phone_number]").attr('type', 'number');
	$("input[data-field-type=phone_number]").addClass('dm-phone');

});



/* Armanagement via change the claims status Pending */
/* Revision 1 : MR-2716 : 22 Aug 2019 : Selva */

$(document).on('click','#js-ar-pending',function(){
	var type = $('select[name="js-select-option"]').val();
	if (type == 'page' || type == 'none') {
		selected_claim_ids = [];
		$('input:checkbox:checked.js_claim_ids').each(function () {
			selected_claim_ids.push($(this).attr('value'));
		});
	} else if (type == 'all') {
		selected_claim_ids = [];
		selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
	}
	if (selected_claim_ids.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	}else{
		$.confirm({
			text: "Are you sure you want to continue?",
			confirm: function () {
				$.ajax({
                        type: "post",
						headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                        url: api_site_url + '/claims/changeClaimStatus',
                        data: {'type' : 'Pending', 'claim_ids' : selected_claim_ids},
                        success: function (result_values) {
                            js_sidebar_notification("success", "Claims status changed successfully");
							location.reload();
						}
				})
			}
		});
		
	}
});

/* Armanagement via change the claims status Ready */
/* Revision 1 : MEDV2-1011 : 24 Mar 2020: Selva */

$(document).on('click','#js-ar-ready',function(){
	var type = $('select[name="js-select-option"]').val();
	if (type == 'page' || type == 'none') {
		selected_claim_ids = [];
		$('input:checkbox:checked.js_claim_ids').each(function () {
			selected_claim_ids.push($(this).attr('value'));
		});
	} else if (type == 'all') {
		selected_claim_ids = [];
		selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
	}
	if (selected_claim_ids.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	}else{
		$.confirm({
			text: "Are you sure you want to continue?",
			confirm: function () {
				$.ajax({
                        type: "post",
						headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                        url: api_site_url + '/claims/changeClaimStatus',
                        data: {'type' : 'Ready', 'claim_ids' : selected_claim_ids},
						datatype: 'JSON',
                        success: function (result_values) {
							
							var message = '<p> Claim Status Change </p>';
							message = message + '<p>Pending: ' + result_values['pending'] + '</p>';
							message = message + '<p>Hold: ' + result_values['hold'] + '</p>';
							message = message + '<p>Rejection: ' + result_values['rejection'] + '</p>';
							message = message + '<p>Submitted: ' + result_values['submitted'] + '</p>';
							message = message + '<p>Denied: ' + result_values['denied'] + '</p>';
							$('.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup').addClass('js-ready-reload');
							js_alert_popup(message);
							
						}
				})
			}
		});
		
	}
});

$(document).on('click','.js-ready-reload',function(){
	location.reload();
});

/* Armanagement via change the claims status Hold */
/* Revision 1 : MR-2716 : 22 Aug 2019 : Selva */

$(document).on('click','#js-ar-hold',function(){
	var type = $('select[name="js-select-option"]').val();
	if (type == 'page' || type == 'none') {
		selected_claim_ids = [];
		$('input:checkbox:checked.js_claim_ids').each(function () {
			selected_claim_ids.push($(this).attr('value'));
		});
	} else if (type == 'all') {
		selected_claim_ids = [];
		selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
	}
	// Added hold reason for bulk hold option in armanagement
	// Revision 1 : MR-2786 : 4 Sep 2019
	if (selected_claim_ids.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	}else{
		// Close if already status select option open.
		$('#js-substatus-type').addClass('hide');
		$('select[name="sub_status_id"]').select2('close');
		$('#js-holdoptions-type').removeClass('hide');
		$('select[name="hold_reason_id"]').select2('open');
	}
	
});

$(document).on('change','.js-ar-reason',function(){
	var type = $('select[name="js-select-option"]').val();
	if($(this).val() != '' && $(this).val() != '0'){
		if (type == 'page' || type == 'none') {
			selected_claim_ids = [];
			$('input:checkbox:checked.js_claim_ids').each(function () {
				selected_claim_ids.push($(this).attr('value'));
			});
		} else if (type == 'all') {
			selected_claim_ids = [];
			selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
		}
		
		if (selected_claim_ids.length === 0) {
			js_alert_popup("Please select atleast one Claim");
		}else{
			var labeltext = $("select[name='hold_reason_id'] option:selected").text();
			var reasonVal = $('select[name="hold_reason_id"]').val();
			$.confirm({
				//text: 'You have selected hold reason as "'+labeltext+'" do you want to proceed?',
				text: 'Are you sure you want to continue?',
				confirm: function () {
					$.ajax({
							type: "post",
							headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
							url: api_site_url + '/claims/changeClaimStatus',
							data: {'type' : 'Hold', 'claim_ids' : selected_claim_ids,'reasonVal':reasonVal},
							success: function (result_values) {
								js_sidebar_notification("success", "Claims status changed successfully");
								location.reload();
							}
					})
				},
				cancel: function(){
					$('.js-ar-reason').val('').trigger('change');
				}
			});
			
		}
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

// Claim sub status start
// Listing page start
$(document).on('click','#js-ar-substatus',function(){
	var type = $('select[name="js-select-option"]').val();
	if (type == 'page' || type == 'none') {
		selected_claim_ids = [];
		$('input:checkbox:checked.js_claim_ids').each(function () {
			selected_claim_ids.push($(this).attr('value'));
		});
	} else if (type == 'all') {
		selected_claim_ids = [];
		selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
	}
	// Added hold reason for bulk hold option in armanagement
	// Revision 1 : MR-2786 : 4 Sep 2019
	if (selected_claim_ids.length === 0) {
		js_alert_popup("Please select atleast one Claim");
	} else {
		$('#js-holdoptions-type').addClass('hide');
		$('select[name="hold_reason_id"]').select2('close');
		$('#js-substatus-type').removeClass('hide');
		$(this).closest('select[name="sub_status_id"]').select2('open');
	}	
});

$(document).on('change','.js-ar-substatus',function(){
	var type = $('select[name="js-select-option"]').val();
	if($(this).val() != '' && $(this).val() != '0'){
		if (type == 'page' || type == 'none') {
			selected_claim_ids = [];
			$('input:checkbox:checked.js_claim_ids').each(function () {
				selected_claim_ids.push($(this).attr('value'));
			});
		} else if (type == 'all') {
			selected_claim_ids = [];
			selected_claim_ids = $('input[name="encodeClaim"]').val().split(',');
		}
		
		if (selected_claim_ids.length === 0) {
			js_alert_popup("Please select atleast one Claim");
		} else {
			var labeltext = $("select[name='sub_status_id'] option:selected").text();
			var statusVal = $('select[name="sub_status_id"]').val();
			$.confirm({
				//text: 'You have selected sub status as "'+labeltext+'" do you want to proceed?',
				text: 'Are you sure to edit claim sub-status?',
				confirm: function () {
					displayLoadingImage();
					$.ajax({
							type: "post",
							headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
							url: api_site_url + '/claims/changeClaimStatus',
							data: {'type' : 'SubStatus', 'claim_ids' : selected_claim_ids,'statusVal':statusVal},
							success: function (result_values) {
								js_sidebar_notification("success", "Claims sub-status edited successfully");
								hideLoadingImage();
								location.reload();
							}
					})
				},
				cancel: function(){
					$('.js-ar-substatus').val('').trigger('change');
				}
			});
			
		}
	}
});

// Listing page end


$(document).on('change', '.js-ar-review-substatus', function(){
	selected_claim_ids = [];
	if($(this).val() != '' && $(this).val() != '0' ){
		var claim_id  = $(this).attr('data-id');
		var labeltext = $(this).find('option:selected').text();
		var statusVal = $(this).find('option:selected').val();
		var claimOldSubStatus = $("#claim_sub_status_"+claim_id).val(); 
		selected_claim_ids.push(claim_id);
		//console.log("claim ID "+claim_id+" Old Status "+claimOldSubStatus+" lbl txt"+labeltext+"ST VAL"+statusVal);
		if(claimOldSubStatus != statusVal) {
			$.confirm({
				//text: 'You have selected claim sub status as "'+labeltext+'" do you want to proceed?',
				text: 'Are you sure to edit claim sub-status?',
				confirm: function () {
					displayLoadingImage();
					$.ajax({
							type: "post",
							headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
							url: api_site_url + '/claims/changeClaimStatus',
							data: {'type' : 'SubStatus', 'claim_ids' : selected_claim_ids,'statusVal':statusVal},							
							success: function (result_values) {
								js_sidebar_notification("success", "Claim sub-status edited successfully");
								//location.reload();
								hideLoadingImage();
								$("#claimdetlink_"+claim_id).trigger("click");
							}
					})
				},
				cancel: function(){
					$('.js-claimsubstatus').val('').trigger('change');
				}
			});
		}
	} else if($(this).val() != '0' ) {
		//
	}
});

// MEDV2-903 - AR Management: Claim sub status: Mouse pointer and click options to be done on when clicked on text itself(Simliar like workbench)
$(document).on('click','.fa-claimsubstatus, .js_claimsubstasg_link',function(){
	$('.fa-claimsubstatus').addClass("hide");
	var selId = $(this).attr("alt");
	$("#js-review-substatus-type_"+selId).removeClass("hide").addClass("show");
});

$(document).on('click','.js-convert-claim-notes',function(){
	var id = $(this).attr('data-id');
	$.ajax({
		url: api_site_url + '/patients/armanagement/getaddedfollowupdetails',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: {'id':id},
		success: function (resp) {
			js_sidebar_notification("success",resp);
		}
	});
});

// Claim sub status update end



/* Clearing textbox when change followup catedory option in popup */

$(document).on('change','input[name="claim_status_radio"][type="radio"]',function(){
	$('input.common_error_validate').val('');
});


/* Clearing textbox when change followup catedory option in popup */