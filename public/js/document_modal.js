function guid() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000)
			.toString(16)
			.substring(1);
	}
	return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4();
}

var ms = 0;

$(document).delegate("a[data-target=#document_add_modal]", 'click', function (e) {
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
		var document_category = $("#documents").attr("data-category");
		if (document_category != 'alldocument') {
			$("select.select2.form-control").select2();
			//	callicheck();
			formValidate();
			//	loadwebcam();
		}
		if ($("#document_list_form_part").css('visibility') == 'visible') {
			$('#dynamic-size').removeClass('modal-md').addClass('modal-lg');
		} else {
			$('#dynamic-size').removeClass('modal-lg').addClass('modal-md');
		}
	});
});

$(document).on('click', "a[data-target=#document_add_modal]", function (e) {
	setTimeout(function () {
		$("#follow_up_date").datepicker({ minDate: 0 });
		$("#checkdate").datepicker();
	}, 1000);
	var target = $(this).attr("data-url");
	$("#document_add_modal .modal-content").load(target, function () {
		$("select.select2.form-control").select2();
		callicheck();
		formValidate();
		loadwebcam();
		// $("#follow_up_date").datepicker({minDate: 0});
	});
});

$(document).on('change', '#checkdate', function () {
	$('form#document_add_popupform').bootstrapValidator('revalidateField', 'checkdate');
});

$(document).on('change', 'input[type="file"]', function () {
	var url = window.location.href;
	var arr = url.split('/');
	if ((jQuery.inArray("patients", arr) != -1 && jQuery.inArray("create", arr) != -1) || (jQuery.inArray("patients", arr) != -1 && jQuery.inArray("edit", arr) != -1) || jQuery.inArray("facility", arr) != -1 || jQuery.inArray("provider", arr) != -1) {
		var get_form_id = $(this).parents("form").attr("id");
		var element = $(this);
		// var file = $('#' + get_form_id).find('input[name="filefield[]"]').val();
		var file = [];
		var filelist = document.getElementById("filefield1").files || [];
		for (var i = 0; i < filelist.length; i++) {
			file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
		}
		if (filelist.length > 5) {

		}
		// Added queries
		// Revision 1 - Ref: MR-2666 08 Augest 2019: Pugazh
		
		else if (filelist.length == 0){
			element.parent("span").closest("div").children("div").not(':first').remove();
			element.parent("span").closest("div").find('.js-display-error').html("");
		}else {
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
					}
					else {
						element.parent("span").closest("div").find('.js-display-error').html(file);
					}
				}
				if (file != '')
					$(".removeFile").hide();
			});
		}
	}
});

function formValidate() {
	$('#documentaddmodalform').bootstrapValidator({
		feedbackIcons: {
			valid: "",
			invalid: "",
			validating: "glyphicon glyphicon-refresh"
		},
		excluded: [':disabled'],
		fields: {
			title: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: title_lang_err_msg
					},
					regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					},
					remote: {
						message: 'Title already taken in the selected category',
						url: api_site_url+'/documentTitle',
						data:{'title':$('input[name="title"]').val(),'_token':$('input[name="_token"]').val(),'category_id':function() { return $('#category').val(); }},
						type: 'POST'
					},
					callback: {
						message: '',
						callback: function (value, validator, $field) {
							var count = $("#title").val().length;
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
			category: {
				message: '',
				validators: {

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
							category_value = $('#category').val();
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
			/*	js_err_webcam:{
					message:'',
					selector: '#documentaddmodalform .js_err_webcam',
					validators:{
						callback: {
							message: attachment_lang_err_msg,
							callback: function (value, validator) {
								var get_checked_val 	= $('#documentaddmodalform input[name="upload_type"]:checked').val();
								var err_msg				= $('#error-cam').val();
								if((err_msg =='' || err_msg ==null || err_msg ==1) && get_checked_val =="webcam") {
									if(value =='' || value == null)
										return false;
									else
										return true;
								}
								return true;
							}
						}
					}
				}, */
			"filefield[]": {
				message: '',
				validators: {
					notEmpty: {
						message: attachment_lang_err_msg
					},
					file: {
						maxSize: 1024 * 32000,
						message: "Maximum allowed only 32MB file per file"
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
									}
									else {
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
		//Removed the Disabled attr to avoid category undefined
		//$("#category").removeAttr('disabled');
		var formData = new FormData($("#documentaddmodalform")[0]);
		var document_type = $("#document_type").val();
		var document_type_id = $("#document_type_id").val();
		var document_sub_type = $("#document_sub_type").val();
		var main_type_id = $("#main_type_id").val();
		var document_category = $("#document_category").val();
		var doc_title = $("#title").val();
		var doc_category = $("#category").val();
		var doc_description = $("#document_description").val();
		var doc_filefield = $('input[type=file][name=filefield]').val();//$("#filefield1").val();
		var doc_dynamic = $("#js-dynamic_data").val();
		var temp_doc_id = (document_type_id == 0) ? temp_doc_id : '';

		if (document_type_id == 0) {
			var temp_doc_id = $("#temp_doc_id").val();
			if (temp_doc_id == "") {
				temp_doc_id = guid();
				$("#temp_doc_id").val(temp_doc_id);
			}
		}
		var check = $(".jscount-" + doc_dynamic).val();
		if (check == "") {
			temp_doc_id = guid();
			$(".jscount-" + doc_dynamic).val(temp_doc_id);
		}
		formData.append('temp_doc_id', temp_doc_id);
		if (typeof doc_dynamic !== 'undefined') {
			var temp_doc_id = $(".jscount-" + doc_dynamic).val();
			formData.append('temp_doc_id', temp_doc_id);
		}

		$("#add_doc_error-alert,#footer_part").addClass("hide");
		$(".spin_image").removeClass("hide");
		$.ajax({
			type: 'POST',
			url: api_site_url + '/api/documentmodal/store/' + document_type + '::' + document_sub_type + '::' + main_type_id + '/' + document_type_id,
			data: formData,
			dataType: 'json',
			timeout: 0,
			processData: false,
			contentType: false,
			success: function (result) {
				if (result['status'] == "success") {
					ms = 'submitted';
					if (document_type_id == 0) {
						$('#document_add_modal_link_' + doc_category).attr('data-url', api_site_url + '/api/adddocumentmodal/' + document_type + '::' + document_sub_type + '::' + main_type_id + '/' + document_type_id + '/' + doc_category + '/' + temp_doc_id);
						var target = api_site_url + '/api/adddocumentmodal/' + document_type + '::' + document_sub_type + '::' + main_type_id + '/' + document_type_id + '/' + document_category + '/' + temp_doc_id;
					}
					else {
						var target = api_site_url + '/api/adddocumentmodal/' + document_type + '::' + document_sub_type + '::' + main_type_id + '/' + document_type_id + '/' + document_category;
					}
					$("#document_add_modal .modal-content").load(target, function () {
						// Change modal size according to form size
						// Revision 1 - Ref: MR-1560 31 July 2019: Pugazh
						$('#dynamic-size').removeClass('modal-md').addClass('modal-lg');
						$("#add_doc_success-alert").fadeTo(1000, 600).slideUp(600, function () {
							$("#add_doc_success-alert").alert('close');
						});
						$("select.select2.form-control").select2();
						//	callicheck();
						formValidate();
						//	loadwebcam();
					});
					// setTimeout(function () { 

					// }, 100);
				}
				else {
					$.each(result["message"], function (i, val) {
						$('.modal.in [name="' + i + '"]').parents(".form-group").addClass('has-error').removeClass('has-success');
						$('[name="' + i + '"]').parent("div").find('small:last').html(val).show();
					});
					$("#add_doc_error-alert,#footer_part").removeClass("hide");
					$(".spin_image").addClass("hide");
					$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
					$(".js_document_err_msg").html(result['message']);
				}
			}
		});
	});
}
$(document).on('click', '.edit-practice-user-model', function (e) {
	//console.log("jdfg");
	var url_val = $(this).attr('data-url');

	$.ajax({
		url: url_val,
		type: 'get',
		success: function (result) {
			//console.log(result.html);
			$('#add_edit_practiceuser .modal-body').html(result.html);
			$('#add_edit_practiceuser').modal('show');
		}
	});
});

$(document).on('click', '.add-practice-user-model', function (e) {
	//console.log("add");
	var url_val = $(this).attr('data-url');

	$.ajax({
		url: url_val,
		type: 'get',
		success: function (result) {
			//console.log(result);
			$('#add_setpractice_user .modal-body').html(result.html);
			$('#add_setpractice_user').modal('show');
		}
	});
});

$(document).on('click', ".js_popup_form_reset", function () {
	$(".js_document_err_msg,span.js-display-error").html("");
	$('#documentaddmodalform')[0].reset();
	$('#documentaddmodalform select').select2();
	$("select.select2").select2({
		placeholder: "--Select--"
	});
	$('.js-display-error').text('');
	$('#documentaddmodalform').data("bootstrapValidator").resetForm();
});

$(document).on('click', '#add_more', function () {
	$('#document_list_form_part').hide();
	$("#document_add_modal .modal-title").html('New Document');
	$("#dynamic-size").removeClass('modal-md-650').addClass('modal-md');
	$('#document_add_form_part').show();
	// $("#follow_up_date").datepicker({minDate: 0});
	$("#checkdate").datepicker();

});

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

function get_sub_category_process(document_type, id) {
	var sub_category_type = $("#sub_category_type").val();
	if (sub_category_type != 'no_sub_type') {
		var docData = "document_type_datas=" + document_type + "::" + sub_category_type;
		if (id != "") {
			var url_val = api_site_url + '/api/document/get_document_subgategory_list/' + id;
		}
		else {
			var url_val = api_site_url + '/api/document/get_document_subgategory_list';
		}
		$.ajax({
			type: 'POST',
			url: url_val,
			data: docData,
			dataType: 'json',
			timeout: 0,
			success: function (res) {
				var newOptions = res['data']['result'];
				$("#sub_category_id_part").show();
				var $el = $("#sub_category_id");
				var cc = 0;
				$el.empty();
				$.each(newOptions, function (value, key) {
					$el.append($("<option></option>")
						.attr("value", value).text(key));
					if (cc == 0) {
						$("#sub_category_id").select2("val", value);
					}
					cc++;
				});
			}
		});
	}
	else {
		$("#sub_category_id_part").hide();
	}
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

$(document).on('click', '.js-popupdocument-delete', function () {

	var element = $(this);
	var doc_id = $(this).attr('data-id');
	$("#js_confirm_patient_demo_remove")
		.modal({ show: 'false', keyboard: false })
		.one('click', '.js_modal_confirm', function (e) {
			var conformation = $(this).attr('id');
			if (conformation == "true") {
				var url_val = api_site_url + '/api/document/deletePopupDocument/' + doc_id;
				$.ajax({
					type: 'POST',
					url: url_val,
					dataType: 'json',
					success: function (res) {
						if (res.status == 'success') {
							element.parent().parent().remove();
							$("#delete_doc_success-alert").fadeTo(1000, 600).slideUp(600, function () {
								$("#delete_doc_success-alert").alert('close');
							});
							var rowCount = $('#documents tr').length;
							if (rowCount == 1) {
								$('#document_list_form_part').hide();
								// Change modal size according to form size
								// Revision 1 - Ref: MR-1560 31 July 2019: Pugazh
								$('#dynamic-size').removeClass('modal-lg').addClass('modal-md');
								$("#document_add_modal .modal-title").html('New Document');
								$('#document_add_form_part').show();
							}
						} else {
							$("#delete_doc_error-alert").fadeTo(1000, 600).slideUp(600, function () {
								$("#delete_doc_error-alert").alert('close');
							});
						}
					}
				});

			}
		});
});

$(document).on('change', '#category', function () {
	var category_val = $(this).val();
	if (category_val == 'claim_document') {
		$('.js-claim-number').show();
	} else {
		$('.js-claim-number').hide();
	}
	$("#js-bootstrap-validator").bootstrapValidator('revalidateField', "jsclaimnumber");
});

$(document).on('click', '.js-new-dcoument', function () {	 //$('#category').val("").change();	  
	resetform('js-bootstrap-validator');
	if (click_count == 1) {
		$("form#js-bootstrap-validator").find("input:not(:hidden, :submit, :file), textarea,select").val("").change();
		$('div.error').find('p').remove();
		$('div.error').removeClass('error');
		mg = 0;
	}
	// $("#follow_up_date").datepicker({minDate: 0});
	$('#js-bootstrap-validator').data("bootstrapValidator").resetForm();
});

/*$(document).ready(function(){
	$('.js-new-dcoument').click(function(){ 
	  setTimeout(function(){
	  			if($('#create_document select.select2').length){
			  		$('#create_document select.select2').select2({
		       		 			dropdownParent: $('#create_document')
		   		 			});
			  		}	  		
	  } ,	  		
	  500);
	});	
});*/


$(document).on('click', '.js-open-img', function () {
	$('.js-document').attr('src', $(this).attr('data-url'));
	setTimeout(function () { $('#document_attachment').modal('show'); }, 500)
})

function resetform(formid) {
	var $el = $('#' + formid);
	$el.wrap('<form>').closest('form').get(0).reset();
	$el.unwrap();
	$('.js-display-error').text("");
}

$(document).on('click', '.remove-zindex', function () {
	setTimeout(function () { $("#js_confirm_patient_demo_remove").css("z-index", ''); }, 500)
});

$(document).on('click', '.js-status-change', function () {
	var note_id = $(this).attr('data-note');
	var status = $(this).attr('data-status');
	var token = $('input[name="csrf_toten_id"]').val();
	$.ajax({
		type: 'post',
		url: api_site_url + '/patients/notes/status',
		context: this,
		data: { 'note_id': note_id, 'status': status, '_token': token },
		dataType: 'json',
		success: function (resp) {
			if (resp.status == 'success') {
				$(this).html(resp.data);
				$(this).attr('data-status', resp.data);
				js_sidebar_notification('success', 'Successfully status changed');
			} else {
				js_sidebar_notification('error', 'Something went wrong');
			}
		}
	});
});

function edit_followup_revalidate() {
	$('#js-bootstrap-validator-edit')
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: [':disabled', ':hidden', ':not(:visible)', '.group'],
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				category: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Enter Category'
						}
					}
				},
				question: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Enter Question'
						}
					}
				},
				hint: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Enter Hint'
						}
					}
				},
				date_type: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Choose Date Type'
						}
					}
				},
				field_type: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Choose Field Type'
						}
					}
				},
				field_validation: {
					message: '',
					validators: {
						notEmpty: {
							message: 'Choose Field Validation'
						}
					}
				}
			}
		});
}

$(document).on('click', '.edit_followup', function () {
	var question_id = $(this).attr('data-question-id');
	$.ajax({
		type: 'get',
		url: api_site_url + '/followup/view/question/' + question_id,
		success: function (data) {

			$('#edit_followup').html(data);
			$('#edit_followup').modal('toggle');
			setTimeout(function () {
				$('select.form-select').select2();
				var value = $('#edit_field_type').val();
				if (value == 'date') {
					$('#edit_field_validation').addClass('hide');
					$('#edit_date_type').removeClass('hide');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'question');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
				} else if (value == 'text' || value == 'number') {
					$('#edit_field_validation').removeClass('hide');
					$('#edit_date_type').addClass('hide');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field_validation');
					$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'date_type');
				}
				edit_followup_revalidate();
			}, 300);
		}
	})
});


$(document).on('click', '.js-category-edit', function () {
	var category_id = $(this).attr('data-category-id');
	$.ajax({
		type: 'get',
		url: api_site_url + '/followup/view/category/' + category_id,
		success: function (data) {
			$('#edit_catQ').html(data);
			$('#edit_catQ').modal('toggle');
			$('#js-bootstrap-validator-editcategory').bootstrapValidator({
				message: 'This value is not valid',
				excluded: [':disabled', ':hidden', ':not(:visible)', '.group'],
				feedbackIcons: {
					valid: '',
					invalid: '',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					category: {
						message: '',
						validators: {
							notEmpty: {
								message: 'Enter Category'
							}
						}
					}
				}
			});
		}
	})
});