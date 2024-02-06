/*** Start Access for datatable process ***/
function accessDataTable(listFor) {
	var targetVal = 4; // To handle sort option for action column based on the list. 
	if (listFor != "practice")
		targetVal = 5

	$('#documents_tab').DataTable({
		"paging": true,
		"lengthChange": true,
		"searching": true,
		"oSearch": { "bSmart": false, "bRegex": true },
		"columnDefs": [{ "orderable": false, "targets": targetVal }],
		"info": true,
		"pageLength": 25,
		"searchHighlight": true,
		"fixedHeader": true,
		"responsive": true,
		"autoWidth": true
	});
}
accessDataTable("practice");
/*** End Access for datatable process ***/

/*** Documents search with highlight function start ***/
$('.form-group-billing input').on('keyup', function () {
	$('#documents_tab tr td').unhighlight();
	$(".dataTables_filter").find('input[type="search"]').val($(this).val()).trigger("keyup");
	$("#documents_tab tr td").highlight($(this).val());
});
/*** Documents search with highlight function end ***/

/*** Documents delete function start ***/
$(document).on('click', ".js_delete_confirm", function (ev) {
	ev.preventDefault();
	$("#js_confirm_popup").modal("show");
	var href = $(this).attr("data-href");
	$(".js_confirm_box").attr("data-href", href);
});

$(document).on('click', ".js_confirm_box", function (ev) {
	ev.preventDefault();
	var href = $(this).attr("data-href");
	$.ajax({
		type: 'GET',
		url: href,
		success: function (result) {
			var data = JSON.parse(result);
			var document_list = $(".js_search_module:checked").val();
			$("#js_confirm_popup").modal("hide");
			ajaxsearchModule(document_list);
			if (data["status"] == "success") {
				$(".alert").html('<p class="alert alert-success" id="success-alert">' + data["message"] + '</p>');
				$("#success-alert").fadeTo(1000, 600).slideUp(600, function () {
					$("#success-alert").alert('close');
				});
			} else {
				$(".alert").html('<p class="alert alert-error" id="error-alert">' + data["message"] + '</p>');
				$("#error-alert").fadeTo(1000, 600).slideUp(600, function () {
					$("#error-alert").alert('close');
				});
			}
		}
	});
});

/*** Documents delete function end ***/
function patient_search_func(){
    //console.log("patient search function");
    var patient_search_category = $('select#js-patient_search_category').select2("val");
   
    $('#patient_search').autocomplete({ 
        source: api_site_url+'/scheduler/documentsearchpatient/'+patient_search_category,
        minLength: 1,
		search: function( event, ui ) { displayLoadingImage(); },
        select: function( event, patients ) {
            $('.eligibility_gray').css('display','none');
            $('.js_eliactive').css('display','none');
            $('.js_eliinactive').css('display','none');
            $('.js_elierror').css('display','none');
            $('.js-show-authorization').hide();

            $('#patient_id').val(patients.item ? patients.item.id : "");
            $('.js-edit_patient_a_tag').attr('href',api_site_url+'/patients/'+patients.item.patient_encodeid+'/edit');            
            $('#js-search_patient_first_name').html(patients.item.first_name);
            $('#js-search_patient_last_name').html(patients.item.last_name);
            $('#js-search_patient_middle_name').html(patients.item.middle_name);
            $('#js-search_patient_dob').html(patients.item.dob);
            $('#js-search_patient_gender').html(patients.item.gender);
            $('#js-search_patient_address1').html(patients.item.address1);
            $('#js-search_patient_city').html(patients.item.city);
            $('#js-search_patient_state').html(patients.item.state);
            $('#js-search_patient_zipcode').html(patients.item.zipcode);
            if(patients.item.phone != '')
                $('#js-search_patient_home_phone').html(patients.item.phone);
            else
                $('.js_search_phon').addClass('hide');
            
            if(patients.item.mobile != '')
                $('#js-search_patient_mobile').html(patients.item.mobile);
            else
                $('.js_search_mob').addClass('hide');
                
            if(patients.item.ssn != '')
                $('#js-search_patient_ssn').html(patients.item.ssn);
            else {
                $('.js_search_ssn').addClass('hide');
            }   
            $('#js-search_patient_bal').html(patients.item.balance);
            auth_remain_msg = '';
            if(patients.item.auth_remain !='') 
                var auth_remain_msg = 'You have '+patients.item.auth_remain+' remaining visits';
            $('#js-search_auth_remain').html(auth_remain_msg);
            $('#js-search_patient_primary_insurance').html('');
            $('#js-search_patient_primary_insurance_policy_id').html('');
            if(patients.item.primary_insurance!='undefined' && patients.item.primary_insurance!=''){
                if(patients.item.primary_insurance != '')
                    $('#js-search_patient_primary_insurance').html(patients.item.primary_insurance);
                else    
                    $('.js_search_app_ins').addClass('hide');
            }
            if((patients.item.primary_insurance_policy_id != 'undefined')) {
                $('#js-search_patient_primary_insurance_policy_id').html(patients.item.primary_insurance_policy_id);                
            } else {
                $('.js_search_app_policy').addClass('hide');
            }   
            if(patients.item.secondary_insurance != 'undefined' && (patients.item.secondary_insurance != ''))   
                $('#js-search_patient_secondary_insurance').html(patients.item.secondary_insurance);
            else
                $('.js_search_app_ins').addClass('hide');
            if((patients.item.secondary_insurance_policy_id != "undefined") && (patients.item.secondary_insurance_policy_id != ""))
                $('#js-search_patient_secondary_insurance_policy_id').html(patients.item.secondary_insurance_policy_id);
            else
                $('.js_search_app_ins').addClass('hide');
            $('#js-searched_patient').removeClass('hide');
            $('#js-new_patient').addClass('hide');
            $('#is_new_patient').attr('checked',false);
            $('#is_new_patient').iCheck('update');   
            
            var patient_id = $('#patient_id').val();
            $('.js_get_eligiblity_details').attr('data-patientid',patient_id);
            
            if((patients.item.eligibility_verification == 'None' || patients.item.eligibility_verification == 'Error') && patients.item.primary_insurance_policy_id != '' && typeof patients.item.primary_insurance_policy_id != 'undefined'){
                $('.eligibility_gray').attr('data-patientid',patient_id);
                $('.eligibility_gray').css('display','block');
            } else if(patients.item.eligibility_verification == 'Active' && patients.item.getReachEndday <= 0){
                $('.js_eliactive').css('display','block');
            } else if(patients.item.eligibility_verification == 'Inactive' || patients.item.getReachEndday > 0){
                $('.js_eliinactive').css('display','block');
            }
            /*** Authorization poup show in applointment scheduler starts**/
            var auth_detail  =patients.item.autorization_detail;
            auth_url = api_site_url+'/patients/'+patients.item.patient_encodeid+'/billing_authorization/appointment';
            $('.js-authpopup').attr('data-url', auth_url);
            if(auth_detail)
                $('.js-show-authorization').show();
            var copay = $("#copay_option").val();
            var copay_check_number = $("#copay_check_number").val();
            var money_order_number = $('input[name="money_order_no"]').val();
            if(copay == "Check" && copay_check_number != "" && typeof copay_check_number != "undefined"){               
                $('#js-bootstrap-validator').data('bootstrapValidator').enableFieldValidators('copay_check_number', true)
                .revalidateField('copay_check_number');
            }
            if(copay == "Money Order" && money_order_number != "" && typeof money_order_number != "undefined"){               
                $('#js-bootstrap-validator').data('bootstrapValidator').enableFieldValidators('money_order_no', true)
                .revalidateField('money_order_no');
            }
		hideLoadingImage();
        /*** Authorization poup show in applointment scheduler ends**/
        },
        response: function(event, patients) {
            try {
                if (event.originalEvent.type != "menuselected"){
                    $("#patient_id").val("");
                    $('#js-searched_patient').addClass('hide');
                }
            } catch (err) {
                $("#patient_id").val("");
                $('#js-searched_patient').addClass('hide');
            }
			hideLoadingImage();
        }
		
    });
}

/* 
 Function patient selection focusout
 */
 
 $('input.js-sel_Patient_list').focusout(function(){
	 var category = $(this).val();
	 if (category != "") {
		$.ajax({
			url: api_site_url + '/documents/getcategorylist/' + category,
			type: "GET", // Type of request to be send, called as method
			success: function (res) {
				var data = JSON.parse(res);
				$.each(data["cat_list"], function (i, val) {
					$("select.js_select_category").append("<option value='" + i + "'>" + val + "</option>");
				});
				if (category != "" && category != "practice" && category != 'group') {
					if(category != 'patient'){
						$.each(data["type_list"], function (i, val) {
							$("select.js_select_type_id").addClass(addclassname).append("<option value='" + i + "'>" + val + "</option>");
						});
						$("select.js_select_type_id").removeClass('hide');
						$(".js_select_patient_id").addClass("hide");
					}else{
						
						$("select.js_select_type_id").addClass('hide');
						$(".js_select_patient_id").removeClass("hide");
					}
					$(".js_select_type_label").text(title).attr("data-text", title);
					$(".js_select_type").removeClass("hide");
					if ($('input[name="type_id"]').length) {
						$('#document_add_popupform').data('bootstrapValidator').revalidateField('type_id');
						$('#document_add_popupform').data('bootstrapValidator').revalidateField('category');
					}
				}
				$("select.select2").select2();
				if (category != "" && category == "patient") {
					$(".js-claim-data").show();
				} else {
					$(".js-claim-data").hide();
				}
				$(".js_select_type_id").val('').change();
				hideLoadingImage();
			}
		});
		$('form#document_add_popupform').bootstrapValidator('revalidateField', 'category');
	}
 })
 
/* 
 Function patient selection focusout
 */

/*** Documents Get type based category function start ***/
$(document).on('change', ".js_select_module", function () {
	var category = $(this).val();
	var title = $(this).select2('data')['text'];
	var addclassname = "js_select_" + title;
	
	$(".js_select_type").addClass("hide");
	$(".js_select_patient_id").addClass("hide");
	$(".js_select_category").find('option:gt(0)').remove();
	$(".js_select_type_id").find('option:gt(0)').remove();
	
	if (category != "") {
		displayLoadingImage();
		$.ajax({
			url: api_site_url + '/documents/getcategorylist/' + category,
			type: "GET", // Type of request to be send, called as method
			success: function (res) {
				hideLoadingImage();
				var data = JSON.parse(res);
				$.each(data["cat_list"], function (i, val) {
					$("select.js_select_category").append("<option value='" + i + "'>" + val + "</option>");
				});
				if (category != "" && category != "practice" && category != 'group') {
					if(category != 'patient'){
						$.each(data["type_list"], function (i, val) {
							$("select.js_select_type_id").addClass(addclassname).append("<option value='" + i + "'>" + val + "</option>");
						});
						$("select.js_select_type_id").removeClass('hide');
						$(".js_select_patient_id").addClass("hide");
					}else{
						$("select.js_select_type_id").addClass('hide');
						$(".js_select_patient_id").removeClass("hide");
					}
					$(".js_select_type_label").text(title).attr("data-text", title);
					$(".js_select_type").removeClass("hide");
					if ($('input[name="type_id"]').length) {
						$('#document_add_popupform').data('bootstrapValidator').revalidateField('type_id');
						$('#document_add_popupform').data('bootstrapValidator').revalidateField('category');
					}
				}
				$("select.select2").select2();
				if (category != "" && category == "patient") {
					$(".js-claim-data").show();
				} else {
					$(".js-claim-data").hide();
				}
				
				$(".js_select_type_id").val('').change();
			}
		});
		$('form#document_add_popupform').bootstrapValidator('revalidateField', 'category');
	}else{
		hideLoadingImage();
	}
});
/*** Documents Get type based category function end ***/


/*** Documents Get type based category function start ***/
/* $(document).on('change', ".js_select_Patient", function () {
	var patient_id = $(this).val();
	$("select#jsclaimnumber").find('option').remove().end();
	if (patient_id != "") {
		$.ajax({
			url: api_site_url + '/documents/getpatientclaim/' + patient_id,
			type: "GET", // Type of request to be send, called as method
			success: function (res) {
				var data = JSON.parse(res);
				$("select#jsclaimnumber").append("<option  value = ''>-- Select --</option>");
				$.each(data["claim_number"], function (i, val) {
					$("select#jsclaimnumber").append("<option value='" + i + "'>" + val + "</option>");
				});
				$("select.select2").select2();
			}
		});
	}
}); */

$(document).on('change', ".js_select_Patient", function () {
	var patient_id = $('input#patient_id').val();
	$("select#jsclaimnumber").find('option').remove().end();
	if (patient_id != "") {
		$.ajax({
			url: api_site_url + '/documents/getpatientclaim/' + patient_id,
			type: "GET", // Type of request to be send, called as method
			success: function (res) {
				var data = JSON.parse(res);
				$("select#jsclaimnumber").append("<option  value = ''>-- Select --</option>");
				$.each(data["claim_number"], function (i, val) {
					$("select#jsclaimnumber").append("<option value='" + i + "'>" + val + "</option>");
				});
				$("select.select2").select2();
			}
		});
	}
});

/*** Documents Get type based category function end ***/
/*** Documents popup open function start ***/
var ms = 0;//asking form submission
$(document).on('click', "a[data-target=#document_add_modal]", function (e) { 
	ms = 0;
	var target = $(this).attr("data-url");
	var id = $(this).attr('id');
	if (id == 'patient' && !isFlashEnabled()) {
		js_alert_popup("Flash plugin was disabled");
		return false;
	}
	removeHash();
	$("#document_add_modal").find('div.modal-content').html('');
	setTimeout(function () {
		$("#follow_up_date").datepicker({ minDate: 0 });
		$("#checkdate").datepicker();
	}, 1000);
	$("#document_add_modal .modal-content").load(target, function () {
		$("select.select2.form-control").select2();
		callicheck();
		formValidate();
		//loadwebcam();
		$("#follow_up_date").datepicker({ minDate: 0 });
	});
});

/*** Documents popup open function end ***/
$(document).on('change', '#checkdate', function () {
	$('form#document_add_popupform').bootstrapValidator('revalidateField', 'checkdate');
});

$(document).on('change', 'input[type="file"]', function () {
	var url = window.location.href;
	var arr = url.split('/');
	if (jQuery.inArray("documents", arr) != -1) {
		var get_form_id = $(this).parents("form").attr("id");
		var element = $(this);
		// var file = $('#' + get_form_id).find('input[name="filefield[]"]').val();
		var file = [];
		var filelist = document.getElementById("filefield1").files || [];
		for (var i = 0; i < filelist.length; i++) {
			file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
		}
		// console.log(file);
		if (filelist.length > 5) {

		}
		// Added queries
		// Revision 1 - Ref: MR-2666 08 Augest 2019: Pugazh
		else if (filelist.length == 0){
			element.parent("span").closest("div").children("div").not(':first').remove();
			element.parent("span").closest("div").find('.js-display-error').html("");
		} else {
			$.each(file, function (i, val) {
				var file_name = val.substring(0, 30);
				var file = (val.length > 30) ? file_name + ".." : val; // changed due to insufficient space
				if (i != 0) {
					var ele = element.parent("span").next("div");
					var parent = element.parent("span").closest("div").find("small").first();
					ele.clone().insertBefore(parent).find('.js-display-error').html(file);
					// $(parent+"> div:last-child > .js-display-error").html(file);
					// parent.children().last().find('.js-dispaly-error').html(file);
					// .find('.js-display-error').html(file);
					// $(this).parents("span").closest("div").find('.js-display-error').html(file);
					// console.log(ele);
				} else {
					var parent_child = element.parent("span").closest("div").children("div").size();
					if (parent_child > 1) {
						element.parent("span").closest("div").children("div").not(':first').remove();
						element.parent("span").closest("div").find('.js-display-error').html(file);
						//     var ele = element.parents("span").next("div");
						//     var parent = element.parents("span").closest("div");
						//     ele.clone().appendTo(parent).find('.js-display-error').html(file);
						// $(parent+"> div:last-child > .js-display-error").html(file);
						// parent.children().last().find('.js-dispaly-error').html(file);
					} else {
						element.parent("span").closest("div").find('.js-display-error').html(file);
					}
				}
				if (file != '')
					$(".removeFile").hide();
			});
		}
	}
});

/*** Documents validation function start ***/
function formValidate() { 
	$('#document_add_popupform').bootstrapValidator({
		feedbackIcons: {
			valid: "",
			invalid: "",
			validating: "glyphicon glyphicon-refresh"
		},
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		fields: {
			title: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: title_lang_err_msg
					},
					// maxlength: {
					//     maxlength : 120,
					//     message: "Maxmimum Character Exceeded"
					// },
					regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					},remote: {
						message: 'Title already taken in the selected category',
						url: api_site_url+'/documentTitle',
						data:{'title':$('input[name="title"]').val(),'_token':$('input[name="_token"]').val(),'category_id':function() { return $('#category_new').val(); }},
						type: 'POST'
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var count = $("#tle").val().length;
							if (count >= 120) {
								return {
									valid: false,
									message: "Maxmimum Character Exceeded"
								};
							} else {
								return true;
							}
						}
					}
				}
			},
			// page:{
			// 	message:'',
			// 	validators:{
			// 		integer: {
			// 				message: 'The value is not an integer'
			// 			},
			// 		notEmpty:{
			// 			message: 'Enter Pages'
			// 		},
			// 		regexp: {
			// 			regexp: /^[ 0-9 ]+$/,
			// 			 message: alphanumericspace_lang_err_msg
			// 		}
			// 	}
			// },
			category: {
				message: '',
				validators: {
					notEmpty: {
						message: category_lang_err_msg
					},                           
					
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'jsclaimnumber');
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'payer');
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'checkno');
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'checkdate');
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'checkamt');
							$('form#document_add_popupform').bootstrapValidator('revalidateField', 'title');
							return true;
						}
					}
				}
			},
			assigned: {
				message: '',
				validators: {
					notEmpty: {
						message: 'Select Assigned To'
					},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							return true;
						}
					}
				}
			},
			priority: {
				message: '',
				validators: {
					notEmpty: {
						message: 'Select Priority'
					},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							return true;
						}
					}
				}
			},
			'followup': {
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Select followup Date'
					},
					date: {
						format: 'MM/DD/YYYY',
						message: 'Invalid date format'
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var fllowup_date = $('#follow_up_date').val();
							var current_date = new Date(fllowup_date);
							var d = new Date();
							if (fllowup_date != '' && (d.getTime() - 96000000) > current_date.getTime()) {
								return {
									valid: false,
									message: "Please give future date"
								};
							} else {
								return true;
							}
						}
					}
				}
			},
			status: {
				message: '',
				validators: {
					notEmpty: {
						message: 'Select Status'
					},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							return true;
						}
					}
				}
			},
			notes: {
				message: '',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							return true;
						}
					}
				}
			},
			// page: {
			//                 message: '',
			//                 validators: {
			// 		integer: {
			// 			message: 'The value is not an integer'
			// 		},
			//                     notEmpty: {
			//                         message: 'Enter Pages'
			//                     },
			//                     callback: {
			//                         message: attachment_lang_err_msg,
			//                         callback: function (value, validator) {
			//                             return true;
			//                         }
			//                     }
			//                 }
			//             },
			'jsclaimnumber': {
				message: '',
				selector: '#jsclaimnumber',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category_new').val();
							value = $('#jsclaimnumber').val();

							var need_claim_catg = ["Eligibility_Benefits_Eligibility_Reports", "Eligibility_Benefits_Benefit_Verification", "Authorization_Documents_Pre_Authorization_Letter", "Authorization_Documents_Referral_Letter", "Clinical_Documents_Progress_Notes", "Clinical_Documents_CTMRI_Reports", "Clinical_Documents_X_ray_Reports", "Clinical_Documents_Lab_Results", "Clinical_Documents_Consult_Notes", "Clinical_Documents_Admit_Discharge_Summary", "Procedure_Documents_Superbills", "Procedure_Documents_Surgery_Reports", "Procedure_Documents_Procedure_reports", "EDI_Reports_Clearinghouse_Reports", "EDI_Reports_Payer_Acknowledgements", "EDI_Reports_Rejections", "Payer_Reports_ERA_EOB", "Payer_Reports_Correspondence_Letter", "Payer_Reports_Appeal_Letters"];
							var a = need_claim_catg.indexOf(category_value);
							if ((value == '' || value == null) && a != -1) {
								return {
									valid: false,
									message: "Select claim no"
								}
							} else {
								return true;
							}
						}
					}
				}
			},
			'payer': {
				message: '',
				selector: '#payer',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category_new').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB", "Payer_Reports_Correspondence_Letter", "Payer_Reports_Appeal_Letters"];
							var a = need_claim_catg.indexOf(category_value);
							if ((value == '' || value == null) && a != -1) {
								return {
									valid: false,
									message: "Select payer"
								}
							} else {
								return true;
							}
						}
					}
				}
			},
			'checkno': {
				message: '',
				selector: '#checkno',
				validators: {
					regexp: {
						regexp: /^[A-Za-z0-9 \t]*$/i,
						message: 'Special characters are not allowed'
					},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category_new').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB", "Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if (category_value == 'Payer_Reports_ERA_EOB') {
								if ((value == '' || value == null) && a != -1) {
									return {
										valid: false,
										message: "Check no is needed"
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
			'checkdate': {
				message: '',
				selector: '#checkdate',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Invalid date format'
					},
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category_new').val();
							var fllowup_date = $('#checkdate').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB", "Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if (category_value == 'Payer_Reports_ERA_EOB') {
								if ((fllowup_date == '' || fllowup_date == null) && a != -1) {
									return {
										valid: false,
										message: "Check date is needed"
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
			'checkamt': {
				message: '',
				selector: '#checkamt',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							category_value = $('#category_new').val();
							var need_claim_catg = ["Payer_Reports_ERA_EOB", "Payer_Reports_Correspondence_Letter"];
							var a = need_claim_catg.indexOf(category_value);
							if (category_value == 'Payer_Reports_ERA_EOB') {
								if ((value == '' || value == null) && a != -1) {
									return {
										valid: false,
										message: "Check Amount is needed"
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
			document_type: {
				message: '',
				validators: {
					notEmpty: {
						message: module_lang_err_msg
					}
				}
			},
			type_id: {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var mesg = $('label.js_select_type_label').attr("data-text");

							if(mesg != 'Patient'){
								if ($('.js_select_type').hasClass("hide") == true) {
									return true;
								}
								else if (value == '') {
									return {
										valid: false,
										message: "Select " + mesg.toLowerCase(),
									};
								}
								return true;
							}else if(mesg == 'Patient'){
				
								
								if ($('.js_select_type').hasClass("hide") == true) {
									return true;
								}
								else if($('input#patient_id').val() == ''){
									return {
										valid: false,
										message: "Select " + mesg.toLowerCase(),
									};
								}
								return true;
							}
						}
					}
				}
			},
			js_err_webcam: {
				message: '',
				selector: '.js_err_webcam',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							var get_checked_val = $('input[name="upload_type"]:checked').val();
							var err_msg = $('#error-cam').val();
							if ((err_msg == '' || err_msg == null || err_msg == 1) && get_checked_val == "webcam") {
								if (value == '' || value == null)
									return false;
								else
									return true;
							}
							return true;
						}
					}
				}
			},
			"filefield[]": {
				message: '',
				validators: {
					notEmpty: {
						message: attachment_lang_err_msg
					},
					file: {
						maxFiles: 5,
						maxSize: 1024 * 32000,
						message: "Maximum allowed only 32MB per file"
						// message: attachment_length_lang_err_msg
					},
					callback: {
						message: attachment_valid_lang_err_msg,
						callback: function (value, validator) {
							var file = [];
							var filelist = document.getElementById("filefield1").files || [];
							for (var i = 0; i < filelist.length; i++) {
								file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
							}
							if (filelist.length > 5) {
								return {
									valid: false,
									message: "Please upload only five files"
								};
							} else {
								if (filelist != "") {
									var extension_Arr = ['pdf', 'jpeg', 'jpg', 'png', 'gif', 'doc', 'xls', 'csv', 'docx', 'xlsx', 'txt', 'PDF', 'JPEG', 'JPG', 'PNG', 'GIF', 'DOC', 'XLS', 'CSV', 'DOCX', 'XLSX', 'TXT'];
									var validation = [];
									$.each(file, function (i, val) {
										var file_name = val;
										var temp = file_name.split(".");
										filename_length = ((temp.length) - 1);
										if (extension_Arr.indexOf(temp[filename_length]) == -1) {
											validation.push(false);
											// return false;
										} else {
											validation.push(true);
											// return true;
										}
									});
									if (jQuery.inArray(false, validation) == -1) {
										return true;
									} else {
										return {
											valid: false,
											message: "Please ensure all the file is of correct format"
										};
									}
								}
								return true;
							}
						}
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function (e) {
		// Prevent form submission
		e.preventDefault();
		var doc_type = $('[name="document_type"]').val();
		var formData = new FormData($("#document_add_popupform")[0]);
		formData.append('practice_id', $("#practice_id").val());
		formData.append('type_id', $('[name="type_id"]').val());
		//formData.append( 'document_type', doc_type);
		formData.append('main_type_id', $('[name="type_id"]').val());
		formData.append('upload_type', $("#upload_type").val());
		$("#add_doc_error-alert,#footer_part").addClass("hide");
		$(".spin_image").removeClass("hide");
		$.ajax({
			type: 'POST',
			url: api_site_url + '/documents/module/addform',
			data: formData,
			dataType: 'json',
			timeout: 0,
			processData: false,
			contentType: false,
			success: function (result) {
				if (result['status'] != "success" || result.status != "success") {
					$.each(result["message"], function (i, val) {
						$('[name="' + i + '"]').parents(".form-group").addClass('has-error').removeClass('has-success');
						if (i == "filefield") {
							$('[name="' + i + '"]').parent("span").parent("div").find('small:last').html(val).show();
						} else {
							$('[name="' + i + '"]').parent("div").find('small:last').html(val).show();
						}
					});
					$("#add_doc_error-alert,#footer_part").removeClass("hide");
					$(".spin_image").addClass("hide");
					$(".js_document_err_msg").html(result['message']);
					$('#document_add_popupform').bootstrapValidator('disableSubmitButtons', false);
				} else {
					//ajaxsearchModule(result['data']);
					$("#document_add_modal").modal("hide");
					$("#summery").click();
					js_sidebar_notification("success", "Successfully Added");
				}
			}
		});
	});
}
/*** Documents validation function end ***/

/*** Documents form reset function start ***/
$(document).on('click', ".js_popup_form_reset", function () {
	fieldReset();
	$("select.select2").select2({
		placeholder: "--Select--"
	});
	$('.js-display-error').text('');
	$('#document_add_popupform').data("bootstrapValidator").resetForm();
});
/*** Documents form reset function end ***/

/*** Documents search based list function start ***/
$(document).on('ifToggled click', ".js_search_module:checked", function () {
	var document_list = $(this).val();
	ajaxsearchModule(document_list);
});

function ajaxsearchModule(document_list) {
	//$("#js_wait_popup").modal("show");	
	processingImageShow("#js_ajax_part", "show");
	$.ajax({
		type: 'GET',
		url: api_site_url + '/documents/list/' + document_list,
		success: function (result) {
			$(".js_table_list").html(result);
			var doc_count = $("tr.js_table_click").attr("data-count");
			doc_count = (doc_count == '' || doc_count == null) ? 0 : doc_count;
			$(".js_doc_count").html(doc_count);
			accessDataTable(document_list); // Passed list for control sort option in action field.
		}
	});
	ajaxrefreshStatsicon();
}

function ajaxrefreshStatsicon() {
	$.ajax({
		type: 'GET',
		url: api_site_url + '/documents/statsdetail/list',
		success: function (result) {
			$(".js_retrive_stats_detail").html(result);
			var get_stats_html = $(".js_retrive_stats_detail .js_update_stats").html();
			$('.js_update_stats:first').html(get_stats_html);
			$(".js_retrive_stats_detail").html("");
			$.AdminLTE.boxWidget.activate();
			processingImageShow("#js_ajax_part", "hide");
			//$("#js_wait_popup").modal("hide");
		}
	});
}
/*** Documents search based list function start ***/

/*** Documents webcam function start ***/
function loadwebcam() {
	$.getScript(api_site_url + "/js/webcam/webcam.js", function () {
		webcam.set_quality(100); // JPEG quality (1 - 100)
		webcam.set_api_url($('#apiurl').val());
		webcam.set_shutter_sound(true);
		webcam.set_hook('onComplete', 'my_completion_handler');
		webcam.set_hook('onError', function () {
			$('#error-cam').val(1);
			$('#webcam_div').hide();
			$('#js-show-webcam').hide();
		});
		$('div#webcam').html(webcam.get_html(320, 240));
		$(document).delegate('#js-snap', 'click', function () {
			if ($('#error-cam').val() == 1) {
				js_alert_popup("JPEGCam Flash Error: No camera was detected");
				addModalClass();
				return false;
			}
			$(".js_err_webcam").val(1); //Remove error in bootstrap validator
			document.getElementById('upload_results').innerHTML = 'Snapshot<br>' + '<img src="{{ URL::to(' / ') }}/img/ajax-loader.gif">';
			webcam.snap();
		});
	});
}
/*** Documents webcam function end ***/

function js_alert_popup(msg) {
	$("#patientnote_model .med-green").html(msg);
	$("#patientnote_model").modal('show');
}

function fieldReset() {
	$(".js_select_category").select2("val", '');
	$(".js_select_module").select2("val", '');
	$(".js_select_type_id").select2("val", '');
	$(".js_select_type").addClass("hide");
	$("span.js-display-error").html("");
	$('#document_add_popupform')[0].reset();
}

function scan() {
	var com_asprise_scan_applet_info_codebase = api_site_url + '/js/';
	com_asprise_scan_request(myCallBackFunc,
		com_asprise_scan_cmd_method_SCAN_THEN_UPLOAD, // normal scan with the applet UI
		com_asprise_scan_cmd_return_IMAGES_AND_THUMBNAILS,
		{
			'upload-url': 'http://asprise.com/scan/applet/upload.php?action=upload'
			, 'format': 'JPEG'
			, 'upload-cookies': document.cookie
		});
	/** Use this callback function to get notified about the scan result. */
	function myCallBackFunc(success, mesg, thumbs, images) {
		$("#scanner_filename").val($.trim(mesg));
		$("#scanner_image").val('1');
	}
}

/* 
Document Dynamic Tab Management function
*/

$(document).on('click', '.tab_details', function () {
	var tab_id = $(this).attr('data-tab-id');
	var tab_name = $(this).attr('data-tab-name');
	var tab_title = $(this).attr('data-tab-title');
	var tab_model = $(this).attr('data-tab-model');
	if ($('.default-dynamic-details').length < 8) {
		if ($("#" + tab_id).length == 0) {
			$("ul#document_dynanic_tab").append('<li class="default-dynamic-details" data-type="' + tab_id + '" id="' + tab_id + '" data-title="' + tab_title + '" data-model="' + tab_model + '"><a href="javascript:void(0)"><i class="fa fa-navicon i-font-tabs"></i> ' + tab_name + ' <i data-type="' + tab_id + '" style="cursor: pointer;" class="remove_document_tab fa fa-times pull-right"></i> </a></li>');
			$('#' + tab_id).click();
		} else {
			$('#' + tab_id).click();
		}
	} else {
		js_alert_popup("Maximum Five Tab Only Allowed");
	}
});

$(document).on('click', '.claims_tab_details', function () {
	var tab_id = $(this).attr('data-tab-id');
	var tab_name = $(this).attr('data-tab-name');
	var tab_title = $(this).attr('data-tab-title');
	var tab_model = $(this).attr('data-tab-model');
	if ($('.default-dynamic-details').length < 8) {
		if ($("#" + tab_id).length == 0) {
			$("ul#document_dynanic_tab").append('<li class="itegrity_default-dynamic-details" data-type="' + tab_id + '" id="' + tab_id + '" data-title="' + tab_title + '" data-model="' + tab_model + '"><a href="javascript:void(0)"><i class="fa fa-navicon i-font-tabs"></i> ' + tab_name + ' <i data-type="' + tab_id + '" style="cursor: pointer;" class="remove_document_tab fa fa-times pull-right"></i> </a></li>');
			$('#' + tab_id).click();

		} else {
			$('#' + tab_id).click();
		}
	} else {
		js_alert_popup("Maximum Five Tab Only Allowed");
	}
});

$(document).on('click', '.default-dynamic-details', function () { 
	var type = $(this).attr('data-type');
	var active_id = $(this).attr('id');
	var title = $(this).attr('data-title');
	var model = $(this).attr('data-model');
	var _token = $('input[name="_token"]').val();
	/** Select box default value selection starts**/
	if (model == "facility" || model == "provider" || model == "patients") {
		$('#js-main-module').val(model).change().attr('disabled', true);
	} else {
		$('#js-main-module').val("").change().attr('disabled', false);
	}
	/** Select box default value selection ends**/
	displayLoadingImage();
	$.ajax({
		type: 'post',
		url: api_site_url + '/documents/dynamic',
		data: { '_token': _token, 'type': type, 'title': title, 'model': model },
		success: function (data) {
			$('#ajax_loading_part').html(data);
			$('li.default-dynamic-details').removeClass('active');
			$("#" + active_id).addClass('active');
			$('select[name="date_option"]').val('current_month').trigger('change');
			$("#documents").DataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"info": true,
				"columnDefs": [{ "orderable": false, "targets": -1 }],
				"fnDrawCallback": function (settings) {
					var str = $('.dataTables_filter input').val();
					if ($.trim(str) != '') {
						listingpageHighlight('documents');
					}
				}
			});
			hideLoadingImage(); // Hide loader once content get loaded.
			$('.datepicker').datepicker({ format: "yyyy-mm-dd", autoclose: true });
			if (active_id == "summery") {
				$('.js-common-document-link').hide();
			} else {
				$('.js-common-document-link').show();
			}
			/*if(type == "assigned") 
			{					 
				   $('select[name="assigned_to"]').val("").attr("disabled", "disabled").trigger("change");
			} 
			else
			{
			   $('select[name="assigned_to"]').val("").attr("disabled", false).trigger("change");
			}	*/
			Pace.restart();
			$('#ajax_loading_part').find('#documents_wrapper').find('div.row:eq( 1 )').addClass('monitor-scroll');
			callicheck();
		}
	});
});

$(document).on('click', '.itegrity_default-dynamic-details', function () {
	var type = $(this).attr('data-type');
	var active_id = $(this).attr('id');
	var title = $(this).attr('data-title');
	var model = $(this).attr('data-model');
	var _token = $('input[name="_token"]').val();
	/** Select box default value selection starts**/
	if (model == "facility" || model == "provider" || model == "patients") {
		$('#js-main-module').val(model).change().attr('disabled', true);
	} else {
		$('#js-main-module').val("").change().attr('disabled', false);
	}
	/** Select box default value selection ends**/
	$.ajax({
		type: 'post',
		url: api_site_url + '/admin/dynamic',
		data: { '_token': _token, 'type': type, 'title': title, 'model': model },
		success: function (data) {
			
			$('#integrity_ajax_loading_part').html(data);
			$('li.itegrity_default-dynamic-details').removeClass('active');
			$("#" + active_id).addClass('active');

			if (active_id == "summery") {
				$('.js-common-document-link').hide();
			}else if(active_id == "assigned") {
				$('.js-common-document-link').hide();
			}else {
				$('.js-common-document-link').show();
			}
		}
	});
});

$(document).on('click', '.remove_document_tab', function () {
	var tab_id = $(this).attr('data-type');
	var next_li_id = $("#" + tab_id).prev("li").attr("id");
	$('#' + tab_id).remove();
	$("#" + next_li_id).click();
});

/* 
Document Dynamic Tab Management function
*/


/* 
Document Common Delete function 
*/

$(document).on('click', '.common-delete-document', function () {
	var _token = $('input[name="_token"]').val();
	var doc_id = $(this).attr('data-doc-id');
	$.ajax({
		type: 'post',
		context: this,
		url: api_site_url + '/documents/api/common_delete',
		data: { '_token': _token, 'doc_id': doc_id },
		dataType: 'json',
		success: function (json) {
			js_sidebar_notification('success', json.message);
			// js_alert_popup(json.message);
			if (json.status == 'success') {
				var table = $('#documents').DataTable();
				table.row($(this).parents('tr')).remove().draw();
			}
		}
	});
});

$(document).on('click', '.js-common-delete-document', function () {
	var _token = $('input[name="_token"]').val();
	var doc_id = $(this).attr('data-doc-id');
	var current_sel = $(this);
	var current_type = $(this).attr('data-type');
	var accor_selection = $(this).parents('.js_accordion_content').prev('.js_accordion_header').find("span.js-count");
	var table_id = current_sel.parents(".table").attr("id");
	//current_sel.parents('.table-responsive').addClass("asdsadasdasdsfdsfd");
	$('#js_confirm_box_charges_content').html("Are you sure to delete the entry?");
	$("#js_confirm_box_charges")
		.modal({ show: 'false', keyboard: false })
		.one('click', '.js_modal_confirm1', function (eve) {
			alert_val = $(this).attr('id');
			if (alert_val == 'true') {
				var _token = $('input[name="_token"]').val();
				$.ajax({
					type: 'post',
					context: this,
					url: api_site_url + '/documents/api/common_delete',
					data: { '_token': _token, 'doc_id': doc_id },
					dataType: 'json',
					success: function (json) {
						js_sidebar_notification('success', json.message);
						if (json.status == 'success') {
							if (typeof current_type != "undefined" && current_type == "doc") {
								current_sel.parents('tr').remove();
								if (table_id != '')
									var length = $('#' + table_id + ' >tbody >tr').length;
								if (length == 0) {
									$("#" + table_id).parent('div').text("No documents found");
									$("#" + table_id).remove();
								}
								$(accor_selection).html(length);
							} else {
								var table = $('#documents').DataTable();
								table.row(current_sel.parents('tr')).remove().draw();
							}
						}
					}
				});
			} else {
				//
			}
		});
	return false;
});

/* 
Document Common Delete function 
*/

$(document).on('change', '#category_new', function () {
	if ($(this).val() == 'Payer_Reports_ERA_EOB' || $(this).val() == 'Payer_Reports_Correspondence_Letter' || $(this).val() == 'Payer_Reports_Appeal_Letters') {
		$('.show_payer_details').removeClass('hide');
		if ($(this).val() == 'Payer_Reports_Appeal_Letters') {
			$('.payer_appeal').addClass('hide');
		}
	} else {
		$('.show_payer_details').addClass('hide');
	}
});

/*$(document).on('click','.js_document_filter',function(){
	//var _token = $('input[name="_token"]').val();
	//var start_date = $('.search_start_date').val();
	//var end_date = $('.search_end_date').val();
	var title = $('.dynamic-title').text();
	doc_type = $('.default-dynamic-details.active').attr('data-type');
	var data = $( "form#document_search_form" ).serialize(); 
	data = data+"&doc_type="+doc_type+"&title="+title;
	$.ajax({
	   type:'post',
	   url: api_site_url + '/documents/dynamic/filter',
	   data:data,
	   success:function(data){
			$('#ajax_loading_part').html(data);
			$('li.default-dynamic-details').removeClass('active');
			$("#"+doc_type).addClass('active');
				
			$("#documents").DataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"info": true,
				"columnDefs": [{ "orderable": false, "targets": -1 }],
				"fnDrawCallback": function(settings) {		
					listingpageHighlight('documents');
				} 
			});
			$('.datepicker').datepicker({format: "yyyy-mm-dd", autoclose: true});
	   }
	});
});*/

$(document).on('click', '.js-tab-document', function (evt) {
	evt.stopPropagation();
	var document_url = [];
	var checked_len = $("input[name='document']:checked").length
	if (checked_len < 1) {
		js_alert_popup('Please select a document.');
	} else if (checked_len > 5) {
		js_alert_popup('Select maximum of 5 documents only.');
	} else {
		$.each($("input[name='document']:checked"), function () {
			document_url.push($(this).attr('data-url'));
		});

		for (i = 0; i < document_url.length; i++) {
			openInNewTab(document_url[i]);
		}
	}
});

function openInNewTab(url) {
	var win = window.open(url, '_blank');
	win.focus();
}

$(document).on('click', '.js-document-action', function (evt) {
	evt.stopPropagation();
	var document_ids = [];
	var data_type = $(this).attr('data-type');
	var checked_len = $("input[name='document']:checked").length
	var pat_id = $('#patient_id').val();
	var data_action = $(this).attr('data-action');
	document_action = "";
	if (checked_len < 1) {
		js_alert_popup('Please select a document.');
		return false;
	} else {
		$.each($("input:checkbox[name='document']:checked"), function () {
			document_ids.push($(this).attr('data-id'));
		});
	}
	if (data_type == "download") {
		document_action = api_site_url + "/documents/bulkdownload/" + document_ids;
		window.open(document_action, '_blank');
	} else if (data_type == "delete") {
		$('#js_confirm_box_charges_content').html("Are you sure to delete the entry?");
		$("#js_confirm_box_charges")
			.modal({ show: 'false', keyboard: false })
			.one('click', '.js_modal_confirm1', function (eve) {
				alert_val = $(this).attr('id');
				if (alert_val == 'true') {
					var current_tab = $("#document_dynanic_tab").find("li.active").attr("id");
					document_action = api_site_url + "/documents/api/common_delete";
					var _token = $('input[name="_token"]').val();
					$.ajax({
						type: 'post',
						url: document_action,
						data: { '_token': _token, 'document_ids': document_ids },
						success: function (data) {
							//console.log(data.doc_count);
							if (typeof data_action != "undefined" && data_action == "provider") {
								if (data.doc_count > 0) {
									js_alert_popup("Already documents assigned and can't be deleted.");
								} else {
									js_sidebar_notification('success', data.message);
								}
								location.reload();
							}
							if (data.status == "success") {

								if (typeof pat_id != "undefined") {
									url = api_site_url + "/documents/getpatient_document/" + pat_id
									$.get(url, function (data) {
										$('.js-append-data-document').html(data);
										$("#documents").DataTable({
											"paging": true,
											"lengthChange": true,
											"searching": true,
											"info": true,
											"columnDefs": [{ "orderable": false, "targets": -1 }],
											"fnDrawCallback": function (settings) {
												var str = $('.dataTables_filter input').val();
												if ($.trim(str) != '') {
													listingpageHighlight('documents');
												}
												hideLoadingImage(); // Hide loader once content get loaded.
											}
										});
										callicheck();
									})

								} else {
									$("#" + current_tab).click();
								}
								if (data.doc_count > 0) {
									js_alert_popup("Already documents assigned and can't be deleted.");
								} else {
									js_sidebar_notification('success', data.message);
								}

							} else {
								js_sidebar_notification('success', data.message);
							}
						}
					});
				} else {
					// 
				}
			});
		return false;
	}
})

$('#document_search_form').bootstrapValidator({
	feedbackIcons: {
		valid: "",
		invalid: "",
		validating: "glyphicon glyphicon-refresh"
	},
	excluded: ':disabled, :hidden, :not(:visible)',
	fields: {
		from_date: {
			trigger: 'keyup change',
			message: '',
			validators: {
				callback: {
					message: "Enter from date",
					callback: function (value, validator, $field) {
						var start_date = value;
						var document_date = $("select.js_change_date_option").val();
						var end_date = $(".search_end_date").val();
						if (document_date != "" && start_date == '') {
							return false;
						}
						if (end_date != "") {
							response = searchStartDate(start_date, end_date);
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
		to_date: {
			trigger: 'keyup change',
			message: charge_copay_amt,
			validators: {
				callback: {
					message: charge_copay_amt,
					callback: function (value, validator, $field) {
						var start_date = $(".search_start_date").val();
						var end_date = value;
						if (start_date != "") {
							response = searchEndDate(start_date, end_date);
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
		followup_start: {
			message: '',
			validators: {
				callback: {
					message: "Enter valid date",
					callback: function (value, validator, $field) {
						var start_date = value;
						var end_date = $(".followup_end").val();
						if (end_date != "") {
							response = searchStartDate(start_date, end_date);
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
		followup_end: {
			message: charge_copay_amt,
			validators: {
				callback: {
					message: charge_copay_amt,
					callback: function (value, validator, $field) {
						var start_date = $(".followup_start").val();
						var end_date = value;
						if (start_date != "") {
							response = searchEndDate(start_date, end_date);
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
		checkdate_start: {
			message: '',
			validators: {
				callback: {
					message: "Enter valid date",
					callback: function (value, validator, $field) {
						var start_date = value;
						var end_date = $(".checkdate_end").val();
						if (end_date != "") {
							response = searchStartDate(start_date, end_date);
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
		checkdate_end: {
			message: charge_copay_amt,
			validators: {
				callback: {
					message: charge_copay_amt,
					callback: function (value, validator, $field) {
						var start_date = $(".checkdate_start").val();
						var end_date = value;
						if (start_date != "") {
							response = searchEndDate(start_date, end_date);
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
		check_number: {
			trigger: 'keyup change',
			validators: {
				callback: {
					message: "",
					callback: function (value, validator, $field) {
						parseval = parseFloat(value);
						if (value != '' && parseval == 0) {
							return {
								valid: false,
								message: "Zero check number not allowed"
							}
						} else if (value != '' && !checknumbervalidation(value)) {
							return {
								valid: false,
								message: alphanumeric_lang_err_msg
							}
						}
						return true;
					}
				},
			}
		},
		check_amt_start: {
			message: "",
			validators: {
				callback: {
					message: empty_amt,
					callback: function (value, validator) {
						$('form#document_search_form').bootstrapValidator('revalidateField', 'check_amt_end');
						if (typeof value != "undefined" && value != '' && value <= 0) {
							return {
								valid: false,
								message: greater_zero_amt
							}
						} else if (value != '' && isNaN(value)) {
							return {
								valid: false,
								message: valid_amt
							}
						}
						return true;
					},
				},
			}
		},
		check_amt_end: {
			message: "",
			validators: {
				callback: {
					message: empty_amt,
					callback: function (value, validator) {
						check_amt = $('input[name="check_amt_start"]').val();
						if (typeof value != "undefined" && value != '' && value <= 0) {
							return {
								valid: false,
								message: greater_zero_amt
							}
						} else if (value != '' && isNaN(value)) {
							return {
								valid: false,
								message: valid_amt
							}
						} else if (value != "" && check_amt != "" && parseFloat(value) < parseFloat(check_amt)) {
							return {
								valid: false,
								message: "Amount should be graterthan start amount"
							}
						}
						return true;
					},
				},
			}
		},
	}
}).unbind("success").on('success.form.bv', function (e, data) {
	// Prevent form submission
	e.preventDefault();
	var title = $('.dynamic-title').text();
//	console.log(title);

	doc_type = $('.default-dynamic-details.active').attr('data-type');
	var myform = $("form#document_search_form");
	var disabled = myform.find(':input:disabled').removeAttr('disabled');

	// serialize the form
	var data = myform.serialize();

	// re-disabled the set of inputs that you previously enabled
	disabled.attr('disabled', 'disabled');
	var pat_id = $('#patient_id').val();
	//console.log("data");
	//console.log(JSON.stringify(data));
	if (typeof pat_id != "undefined") {
		url = api_site_url + "/patientdocuments/dynamic/filter/" + pat_id;
	} else {
		url = api_site_url + '/documents/dynamic/filter';
		data = data + "&doc_type=" + doc_type + "&title=" + title;
	}
	$.ajax({
		type: 'post',
		url: url,
		data: data,
		success: function (data) {
			$('#ajax_loading_part').html(data);
			$('.js-append-data-document').html(data);
			$('li.default-dynamic-details').removeClass('active');
			$("#" + doc_type).addClass('active');
			$("#documents").DataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"info": true,
				"columnDefs": [{ "orderable": false, "targets": -1 }],
				"fnDrawCallback": function (settings) {
					listingpageHighlight('documents');
				}
			});
			callicheck();
			$('.datepicker').datepicker({ format: "yyyy-mm-dd", autoclose: true });
			Pace.restart();
			setTimeout(function () { $('#documents_wrapper').find('div.row:eq( 1 )').addClass('monitor-scroll'); }, 620);
		}
	});
}).on('success.field.bv', function (e, data) {
	if (data.bv.getInvalidFields().length == 0) {    // There is invalid field        	
		data.bv.disableSubmitButtons(false);
	}
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

function checknumbervalidation(value) {
	reg = /^[a-zA-Z0-9_ ]*$/;
	var check = reg.test(value);
	return check;
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

$(document).on('change', ".search_start_date, .search_end_date, .js_change_date_option", function () {
	$('form#document_search_form').bootstrapValidator('revalidateField', 'from_date');
	$('form#document_search_form').bootstrapValidator('revalidateField', 'to_date');
});

$(document).on('change', ".followup_start, .followup_end", function () {
	$('form#document_search_form').bootstrapValidator('revalidateField', 'followup_start');
	$('form#document_search_form').bootstrapValidator('revalidateField', 'followup_end');
});

$(document).on('change', ".checkdate_end, .checkdate_start", function () {
	$('form#document_search_form').bootstrapValidator('revalidateField', 'checkdate_start');
	$('form#document_search_form').bootstrapValidator('revalidateField', 'checkdate_end');
});

$(document).on('click','.close_popup',function(){
	if($('#document_add_popupform').length)
		$('#document_add_popupform')[0].reset();
});

// Only numeric allow to enter
$(document).on('keypress keyup blur','.js_numeric',function(event){
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});