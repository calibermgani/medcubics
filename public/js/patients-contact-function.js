/****
 Author: 	Kannan
 Date: 		22 May 2018
 Updated:	Kannan
 ----------- INDEX ------------- 
 1. "Add more contact" Form
    1. Open Form
    2. Select contact category in select box
 2. Patients Page Form Related JS 
 ***/

//1.    "Add more contact" Form open
//1.1.  
$(document).on('click','.js-addmore_contact_v2',function (e) {
        //Set hidden variables
	$('input[type=hidden][name="add_type"]').val('new');
	$('input[type=hidden][name="edit_type_id"]').val('');
        //Set popup title
	var cat_name_title = "Contact Category";        
	$('.js-category-title-v2').html(cat_name_title);
        // Hide all options 
	$("#v2-guarantor").removeClass('show').addClass('hide');
	$("#v2-emergency_contact").removeClass('show').addClass('hide');
	$("#v2-employer").removeClass('show').addClass('hide');
	$("#v2-attorney").removeClass('show').addClass('hide');
	//$('.js_add_new_contact_form').find(".select2-container.select2").remove();
	$('#add_new_contact').modal({ show: 'true'});
	$('#add_new_contact').html($('.js_add_new_contact_form').html());
	$('#add_new_contact select').removeClass("select_2").addClass("select2");
	$('#add_new_contact input').removeClass("flat_red").addClass("flat-red");
	$('#add_new_contact select').select2();

});

//1.2. "Contact Category" change in "Add new contact" form
$(document).on('change', '.js_contact_category_v2', function () {
	
	var contact_form_id_val = $(this).parents("form").attr("id");
	
	$('input[type="checkbox"].flat-red').prop("checked",false);
	//$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-red'});
	current_form_id = $(this).parents("form").attr("id");
//	console.log(current_form_id);
	contactForm(contact_form_id_val);
	$('#js-bootstrap-validator-contact').data("bootstrapValidator").resetForm();
	var current_option = $(this).val();
	current_option = current_option.replace(" ","_");
	current_option = current_option.toLowerCase();

	if(current_option==""){
		var cat_name_title = "Contact Category";
	} else {
		var cat_name_title = "New "+current_option.replace(/_/g, ' ');
		cat_name_title = cat_name_title.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}
	$('.js-category-title-v2').html(cat_name_title);	
	if(current_option=="guarantor"){
		if($('input[name="guarantor_count"]').val() >= 2){ 
			$('#'+current_form_id+' #show_error_msgs').removeClass('hide').addClass('show');
			$('#'+current_form_id+' #add_new_contact').find('.close').addClass('not_showpopup');
			$('#'+current_form_id+' #contact-info-footer').removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-guarantor").removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-emergency_contact").removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-attorney").removeClass('show').addClass('hide');
			$("#"+current_form_id+"#v2-employer").removeClass('show').addClass('hide');
			$('#show_error_msgs').html('<span>You cannot add more than two guarantor</span>');
		} else {
			// Start Recently added 
			// New gurantor self already exist msg ->click cancel->new contact->gurantor->should show fileds 
			$('input[name="guarantor_last_name"]').attr('readonly', false);
            $('input[name="guarantor_first_name"]').attr('readonly', false);
            $('input[name="guarantor_middle_name"]').attr('readonly', false); 

			$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false);
			$("#"+current_form_id+" .same_address").removeClass('hide');
			$("#"+current_form_id+" .self-address").removeClass('hide');
			$("#"+current_form_id+" .address-class").removeClass('hide');
			// End
			$('#show_error_msgs').removeClass('show').addClass('hide');
			$('#add_new_contact').find('.close').removeClass('not_showpopup');
			$('#contact-info-footer').removeClass('hide').addClass('show');
			$("#v2-emergency_contact").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('show').addClass('hide');
			$("#v2-guarantor").removeClass('hide').addClass('show');
			$('#v2-guarantor').find('input:not([type=checkbox]), textarea').val('');
			$("#v2-guarantor select").select2('val','');
		}
	} else if(current_option=="emergency_contact") { 		
		if($('input[name="emergency_count"]').val() >= 2){			 
			$('#show_error_msgs').removeClass('hide').addClass('show');
			$('#contact-info-footer').removeClass('show').addClass('hide');
			$('#add_new_contact').find('.close').addClass('not_showpopup');
			$("#v2-guarantor").removeClass('show').addClass('hide');
			$("#v2-emergency_contact").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('show').addClass('hide');
			$('#show_error_msgs').html('<span>You cannot add more than two emergency contact</span>');
		} else {
			$('#show_error_msgs').removeClass('show').addClass('hide');
			$('#contact-info-footer').removeClass('hide').addClass('show');
			$('#add_new_contact').find('.close').removeClass('not_showpopup');
			$("#v2-guarantor").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('show').addClass('hide');
			$("#v2-emergency_contact").removeClass('hide').addClass('show');
			$('#v2-emergency_contact').find('input:not([type=checkbox]), textarea').val('');
			$("#v2-emergency_contact select").select2('val','');
			$("#"+contact_form_id_val+" .same_address").removeClass('hide');
            $("#"+contact_form_id_val+" .self-address").removeClass('hide');
            $("#"+contact_form_id_val+" .address-class").removeClass('hide');
		}
	} else if(current_option=="employer") { 
		if($('input[name="employer_count"]').val() >= 2){ 
			$('#show_error_msgs').removeClass('hide').addClass('show');
			$('#add_new_contact').find('.close').addClass('not_showpopup');
			$('#contact-info-footer').removeClass('show').addClass('hide');
			$("#v2-guarantor").removeClass('show').addClass('hide');
			$("#v2-emergency_contact").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('show').addClass('hide');
			$('#show_error_msgs').html('<span>You cannot add more than two employer</span>');
		} else {
			$('#show_error_msgs').removeClass('show').addClass('hide');
			$('#contact-info-footer').removeClass('hide').addClass('show');
			$('#add_new_contact').find('.close').removeClass('not_showpopup');
			$("#v2-guarantor").removeClass('show').addClass('hide');
			$("#v2-emergency_contact").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('hide').addClass('show');
			$('#v2-employer').find('input:not([type=checkbox]), textarea').val('');
			$("#v2-employer select").select2('val','');
			$("#"+contact_form_id_val+" .same_address").removeClass('hide');
            $("#"+contact_form_id_val+" .self-address").removeClass('hide');
            $("#"+contact_form_id_val+" .address-class").removeClass('hide');

		}
	} else if(current_option=="attorney") {
		if($('input[name="attorney_count"]').val() >= 2){ 
			$('#'+current_form_id+' #show_error_msgs').removeClass('hide').addClass('show');
			$('#'+current_form_id+' #add_new_contact').find('.close').addClass('not_showpopup');
			$('#'+current_form_id+' #contact-info-footer').removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-guarantor").removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-emergency_contact").removeClass('show').addClass('hide');
			$("#"+current_form_id+" #v2-attorney").removeClass('show').addClass('hide');
			$("#"+current_form_id+"#v2-employer").removeClass('show').addClass('hide');
			$('#show_error_msgs').html('<span>You cannot add more than two Attorney</span>');	
		}else{	
			$("#v2-guarantor").removeClass('show').addClass('hide');
			$("#v2-emergency_contact").removeClass('show').addClass('hide');
			$("#v2-employer").removeClass('show').addClass('hide');
			$("#v2-attorney").removeClass('hide').addClass('show');
			$('#v2-attorney').find('input:not([type=checkbox]), textarea').val('');
			$("#v2-attorney select").select2('val','');
		}
	} else {
		$("#v2-guarantor").removeClass('show').addClass('hide');
		$("#v2-emergency_contact").removeClass('show').addClass('hide');
		$("#v2-employer").removeClass('show').addClass('hide');
		$("#v2-attorney").removeClass('show').addClass('hide');
	}
	//$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-red'});
	$("#js-bootstrap-validator-contact").find(".js-address-success").removeClass('show').addClass('hide');
});
/* start edit contact form model open by Thilaga */

/*$(document).on('click','.edit-contact-form-model-edits',function (e) {
	var contact_id = $(this).attr('data-id');
	var category_type = $(this).attr('data-category-type');
	current_form_id = $(this).parents("form").attr("id");

	$('#add_edit_contact').modal({ show: 'true'}).attr("data_id",contact_id);
	$('#add_edit_contact').html($('.js_add_edit_contact_form').html());
//	$("#add_edit_contact .select_2").select2();
	$('.js-category-title-v2').html('Edit '+category_type);
				
	if(category_type == 'Guarantor'){
		
		$('#v2-edit-guarantor').removeClass('hide').addClass('show');
					
		if($('input[name="guarantor_count"]').val() >= 2){ 
			$('#edit_show_error_msgs').removeClass('hide').addClass('show');
		}
		var elems = ['guarantor_realationship', 'guarantor_last_name', 'guarantor_first_name', 'guarantor_middle_name', 'guarantor_address1', 'guarantor_address2',
		'guarantor_city', 'guarantor_state', 'guarantor_zip5', 'guarantor_zip4', 'guarantor_home_phone', 'guarantor_cell_phone', 'guarantor_email'];

		elems.forEach(myFunction);
			function myFunction(item, index) {			
			$(".js-bootstrap-validator-contact-edit")
			.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item+"_e1").text());
			
			}
			var guarantor_select = $("#"+current_form_id).find("#guarantor_realationship").val();
			if(guarantor_select == 'Self'){
				$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_last_name"]').attr('readonly', true);
				$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_first_name"]').attr('readonly', true);
				$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_middle_name"]').attr('readonly', true);
			//	$("#edit-contact_"+contact_id+" .self-address").addClass('hide');
			}	
			$('#guarantor_relationship option[value="'+guarantor_select+'"]').attr('selected','selected');
			$("#add_edit_contact .select_2").select2();
			$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
			$('.js-v2-edit-contact').attr('id', 'edit-contact_'+contact_id);
			$('.js-v2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);

			if($("#"+current_form_id).find("#edit-sameaddress-insurance").val() =='yes'){
				$("#edit-contact_"+contact_id+" .same_address").addClass('hide');
				$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").prop("checked",true);	
			}else{
				//$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
				$("#edit-contact_"+contact_id+" .same_address").removeClass('hide');
			}
			if(guarantor_select == 'Self'){
				$("#edit-contact_"+contact_id+" .self-address").addClass('hide');
				$("#edit-contact_"+contact_id+" .address-class").addClass('hide');
			}			
	}else if(category_type == 'Emergency Contact'){	
		$('#v2-edit-emergency').removeClass('hide').addClass('show');
		var elems = ['emergency_last_name', 'emergency_first_name', 'emergency_middle_name', 'emergency_relationship', 'emergency_address1', 'emergency_address2',
		'emergency_city', 'emergency_state','emergency_zip4', 'emergency_zip5', 'emergency_home_phone', 'emergency_cell_phone', 'emergency_email'];
		elems.forEach(myFunction);
			function myFunction(item, index) {			
				$(".js-bootstrap-validator-contact-edit")
				.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item).text());
			}
			console.log( $("#"+current_form_id).find("#emergency_cell_phone").text());
			var emergency_state =  $("#"+current_form_id).find("#emergency_state").text().trim();
			$(".js-bootstrap-validator-contact-edit")
				.find("input[name=emergency_state]").val(emergency_state);	
			var emergency_select = $("#"+current_form_id).find("#emergency_relationship").val();			
			$('#emergency_relationship option[value="'+emergency_select+'"]').attr('selected','selected');
			$("#add_edit_contact .select_2").select2();
			$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
			$('.js-v2-edit-contact').attr('id', 'edit-contact_'+contact_id);
			$('.js-v2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);

			if($("#"+current_form_id).find("#emergency-sameaddress-insurance").val() =='yes'){
				$("#edit-contact_"+contact_id+" .same_address").addClass('hide');
				$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").prop("checked",true);
			}else{
				//$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
				$("#edit-contact_"+contact_id+" .same_address").removeClass('hide');
			}
	}else if(category_type == 'Employer'){
		$('#v2-edit-employer').removeClass('hide').addClass('show');
		$('.employed_option_sub_field').removeClass('hide').addClass('show');
		var elems = ['employer_status', 'employer_name', 'employer_occupation', 'employer_address1', 'employer_address2', 'employer_city',
		'employer_state', 'employer_zip5', 'employer_zip4', 'employer_work_phone', 'employer_phone_ext'];
		elems.forEach(myFunction);		
			function myFunction(item, index) {		
				$(".js-bootstrap-validator-contact-edit")
				.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item).text());		
			}			
			var employer_status =  $("#"+current_form_id).find("#employer_status").text().trim();
			//$('#edit_employer_status option[value="'+employer_status+'"]').attr('selected','selected');
			$("#add_edit_contact .select_2").select2();
			$("select#edit_employer_status").select2("val", employer_status).trigger("change"); 
			$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
			$('.js-v2-edit-contact').attr('id', 'edit-contact_'+contact_id);
			$('.js-v2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);
			if(employer_status == 'Retired'){
				$("#edit-contact_"+contact_id+" .employed_option_sub_field").removeClass('show').addClass('hide');
				$("#edit-contact_"+contact_id+" .employer-retired-field").removeClass('show').addClass('hide');				
			}


			
	}
	
      
});*/

/* End edit contact form model open by Thilaga */
$(document).on('click','.js-v2-delete-contact',function (event) {

});

$(document).on('click','.js-v2-delete-contact',function (event) {	
	var contact_id = $(this).attr('data-id');
	$("#js_confirm_patient_demo_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		if(conformation == "true") {
			var patient_id 	= $('#encode_patient_id').val();
			var data = "current_option=contact_delete"+"&patient_id="+patient_id+"&contact_id="+contact_id;
			$.ajax({
				url: api_site_url+'/patients/contact_module',
				headers: {
					 'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
				},
				type: "POST",
				data: data,
				success: function(result) {
					//js_alert_popup('Deleted successfully');
					var res = $.trim(result);
					if(res =='success'){
						moveNextTabv2(patient_id, 'contact', 'Deleted successfully');
					} else {
						moveNextTabv2(patient_id, 'contact', 'Guarantor cannot be deleted if age is less than 18');
					}
				}
			});
		}
	});	
});



function contactForm(form_name) {	
	var contact_options = $('#'+form_name).bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled',
		ignore: ":hidden",
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
        contact_category: {
            validators: {
                notEmpty: {
                    message: con_category
                }				
            }
        },
		'guarantor_last_name': {
			enabled: false,
			trigger:'keyup',
            validators: {
                notEmpty: {
                    message: guar_last_name
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				},
				callback: function (value, validator) {
					
					return true;
				}
            }
        },
		'guarantor_first_name': {
			enabled: false,
			validators: {
                notEmpty: {
                    message: guar_fst_name
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}				
            }
        },
		'guarantor_relationship':{
			trigger: 'change',
			validators: {
				notEmpty: {
					message: "Select guarantor relationship"
				},
				callback: {
						message:'',
						callback: function (value, validator, $field) {
							if($('.js-contact-edit-e2').find('#guarantor_realationship_e1').val() == 'Self' && value == 'Self'){								
								return {
										valid: false, 
										message: 'Self gurantor Already Exist'
									};						   

							}
							return true;
						}
					}
			}	
		},
		'guarantor_home_phone': {
            message:'This field is invalid',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator) {
						var home_phone_msg = home_phone_limit_lang_err_msg;
						var response = phoneValidation(value,home_phone_msg);
						if(response !=true) {
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
		'guarantor_cell_phone': {
            message:'This field is invalid',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator) {
						var cell_phone_msg = cell_phone_limit_lang_err_msg;
						var response = phoneValidation(value,cell_phone_msg);
						if(response !=true) {
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
		'guarantor_email': {
            message: '',
			validators: {
				callback: {
					message: '',
					callback: function (value, validator) {
						var response = emailValidation(value);
						if(response !=true) {
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
		'guarantor_address1': {
			validators: {
                /*regexp: {
					regexp: /^[a-zA-Z0-9 ]+$/,
					message: alphanumericspace_lang_err_msg
				},*/
				callback: {
					message: add1_limit,
					callback: function (value, validator) {
						var address1_value = value.trim();
						var add_length = address1_value.length;
						return (add_length !='' && add_length>28) ? false : true;
					}
				}				
            }
        },
		'guarantor_address2': {
			/*validators: {
                regexp:{
					regexp: /^[A-Za-z0-9 ]+$/,
					message: alphanumericspace_lang_err_msg
				}				
            }*/
        },
		'guarantor_state': {
			 validators: {
                regexp:{
					regexp: /^[A-Za-z]+$/,
					message: only_alpha_lang_err_msg
				}				
            }
        },
		'guarantor_city': {
			validators: {
                regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}				
            }
        },
		'guarantor_zip5': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{5}$/,
					message: zip5_limit_lang_err_msg
				}
			}
        },
        'guarantor_zip4': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{4}$/,
					message: zip4_limit_lang_err_msg
				}
			}
        },
        'emergency_last_name': {
        	 enabled: false,
			 validators: {
                notEmpty: {
                    message: lastname_lang_err_msg
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}
				
            }
        },
        'emergency_first_name': {
        	enabled: false,
			validators: {
                notEmpty: {
                    message: firstname
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}
				
            }
        },
        'emergency_relationship':{
				validators: {
					notEmpty: {
						message: 'Select emergency relationship'
					}
				}	
		},
        'emergency_home_phone': {
            message:'',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator) {
						var home_phone_msg = home_phone_limit_lang_err_msg;
						var response = phoneValidation(value,home_phone_msg);
						if(response !=true) {
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
        'emergency_cell_phone': {
            message:'',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator) {
						var cell_phone_msg = cell_phone_limit_lang_err_msg;
						var response = phoneValidation(value,cell_phone_msg);
						if(response !=true) {
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
        'emergency_email': {
            message: '',
			validators: {
				callback: {
					message: '',
					callback: function (value, validator) {
						var response = emailValidation(value);
						if(response !=true) {
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
		'emergency_address1': {
			validators: {
                /* regexp: {
					regexp: /^[a-zA-Z0-9 ]+$/,
					message: alphanumericspace_lang_err_msg
				}, */
				callback: {
					message: add1_limit,
					callback: function (value, validator) {
						var address1_value = value.trim();
						var add_length = address1_value.length;
						return (add_length !='' && add_length>28) ? false : true;
					}
				}					
            }
        },
		'emergency_address2': {
			/* validators: {
                regexp:{
					regexp: /^[A-Za-z0-9 ]+$/,
					message: alphanumericspace_lang_err_msg
				}				
            } */
        },
		'emergency_city': {
			validators: {
                regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}				
            }
        },
		'emergency_state': {
			validators: {
                regexp:{
					regexp: /^[A-Za-z]+$/,
					message: only_alpha_lang_err_msg
				}				
            }
        },
        'emergency_zip5': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{5}$/,
					message: zip5_limit_lang_err_msg
				}
			}
        },
        'emergency_zip4': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{4}$/,
					message: zip4_limit_lang_err_msg
				}
			}
        },
        'employer_status': {
        	enabled: false,
			validators: {
                notEmpty: {
                    message: employer_status
                }				
            }
        },
		/* 'employer_organization_name': {
			validators: {
				callback: {
					callback: function (value, validator,$field) {
						if($field.attr("id") != undefined){
							var currcon_id_val_arr = $field.attr("id").split('-');
							var employment_status_val1 = $('#employer_status-'+currcon_id_val_arr[1]).val();
							var contact_category_val = $("#contact_category-0").select2('val');
							if((employment_status_val1=='Employed'||employment_status_val1=='Self Employed')&&contact_category_val=='Employer'){
								if(value =="") {
									return {
										valid: false, 
										message: 'Enter organization name'
									};
								}
								if(!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value)) {
									return {
										valid: false, 
										message: 'Special characters not allowed'
									};
								}
							}
						}
						return true;
					}
				}
			}
		}, */
		'employer_occupation': {
			validators: {
				callback: {
					callback: function (value, validator, $field) {
						if($field.attr("id") != undefined){
							var currcon_id_val_arr = $field.attr("id").split('-');
							var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).val();
							var contact_category_val = $("#contact_category-0").select2('val');
							if((employment_status_val=='Employed'||employment_status_val=='Self Employed')&&contact_category_val=='Employer'){
								/* if(value =="") {
									return {
										valid: false, 
										message: 'Enter occupation'
									};
								} */
								if(value !="" && !new RegExp(/^[a-zA-Z0-9 ]+$/).test(value)) {
									return {
										valid: false, 
										message: 'Special characters not allowed'
									};
								}
							}
						}
						return true;
					}
				}
			}
		},
		'employer_student_status': {
			message: '',
			validators: {
				callback: {
					message:'',
					callback: function (value, validator, $field) {
						if($field.attr("id") != undefined){
							var currcon_id_val_arr = $field.attr("id").split('-');
							var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).val();
							var contact_category_val = $("#contact_category-0").select2('val');
							if(employment_status_val=='Student'&&contact_category_val=='Employer'){
								if(value =="") {
									return {
										valid: false, 
										message: 'Select student status'
									};
								}
							}
						}
						return true;
					}
				}
			}
		},
        /*'employer_name': {
        	enabled: false,
			validators: {
                notEmpty: {
                    message: employer_name
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}
				
            }
        },*/
		'employer_name': {
			enabled: false,
			validators: {
				callback: {
					callback: function (value, validator, $field) {
						if($field.attr("id") != undefined){
							var currcon_id_val_arr = $field.attr("id").split('-');
							var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).val();
							var contact_category_val = $("#contact_category-0").select2('val');
							if((employment_status_val=='Employed'||employment_status_val=='Employed(Part Time)')&&contact_category_val=='Employer'){
								if(value =="") {
									return {
										valid: false, 
										message: employer_name
									};
								}
								if(!new RegExp(/^[A-Za-z ]+$/).test(value)) {
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								}
							}
						}
						return true;
					}
				}
			}
		},
        'employer_work_phone': {
            message:'This field is invalid',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator,$fields) { 
						var phone_msg = phone_limit_lang_err_msg;
						var ext_msg = phone_lang_err_msg;
						var ext_length = $('#'+form_name).find('input[name="employer_phone_ext"]').val().length;
						var response = phoneValidation(value,phone_msg,ext_length,ext_msg);
						if(response !=true) {
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
        'employer_address1': {
			validators: {
                /* regexp: {
					regexp: /^[a-zA-Z0-9 ]+$/,
					message: alphanumericspace_lang_err_msg
				}, */
				callback: {
					message: add1_limit,
					callback: function (value, validator) {
						var address1_value = value.trim();
						var add_length = address1_value.length;
						return (add_length !='' && add_length>28) ? false : true;
					}
				}				
            }
        },
		'employer_address2': {
			/* validators: {
                regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}				
            } */
        },
		'employer_city': {
			validators: {
                regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}				
            }
        },
		'employer_state': {
			validators: {
                regexp:{
					regexp: /^[A-Za-z]+$/,
					message: only_alpha_lang_err_msg
				}				
            }
        },
        'employer_zip5': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{5}$/,
					message: zip5_limit_lang_err_msg
				}
			}
        },
        'employer_zip4': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{4}$/,
					message: zip4_limit_lang_err_msg
				}
			}
        },
		'attorney_adjuster_name': {
			enabled: false,
			trigger:'keyup',
            validators: {
                notEmpty: {
                    message: adjustor_name
                },
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				}							
            }
        },
        'attorney_doi': {
            message: '',
			trigger: 'keyup change',
			validators: {
				date: {
					format: 'MM/DD/YYYY',
					message: date_format
				}
			}
        },
        'attorney_claim_num': {
            validators: {
				regexp: {
					regexp: /^[A-Za-z0-9\s]+$/,
					message: alphanumeric_lang_err_msg
				}
			}
        },
        'attorney_work_phone': {
            message:'This field is invalid',
			trigger: 'change keyup',
			validators:{
				callback: {
					message: '',
					callback: function (value, validator) {
						var phone_msg = phone_limit_lang_err_msg;
						var ext_msg = phone_lang_err_msg;
						$fields = validator.getFieldElements('attorney_work_phone');
						//var ext_length = $fields.closest("div").next().next().find("input").val().length;
						var response = phoneValidation(value,phone_msg,ext_msg);
						if(response !=true) {
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
       'attorney_fax': {
            message: '',
			validators: {
				callback: {
					message: '',
					callback: function (value, validator) {
						var fax_msg = fax_limit_lang_err_msg;
						var response = phoneValidation(value,fax_msg);
						if(response !=true) {
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
        'attorney_email': {
            message: '',
            trigger: 'blur',
			validators: {
				callback: {
					message: '',
					callback: function (value, validator) {
						var response = emailValidation(value);
						if(response !=true) {
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
        'attorney_zip5': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{5}$/,
					message: zip5_limit_lang_err_msg
				}
			}
        },
        'attorney_zip4': {
            message: '',
			trigger: 'change keyup',
			validators: {
				regexp: {
					regexp: /^\d{4}$/,
					message: zip4_limit_lang_err_msg
				}
			}
        }
    }
	});
}

/*$(document).on('click', '.js-v2-edit-contact',function (e) {
	//e.preventDefault();	
	var contact_form_id_val = $(this).attr('data-id');
	contactForm(contact_form_id_val);
	var current_option =  $("#"+contact_form_id_val).find("[id^='edit_contact_category_v2']").val();
	current_option = current_option.replace(" ","_");
	current_option = current_option.toLowerCase();
	contactValidtator(current_option, contact_form_id_val);
});*/
$(document).delegate('input[name="attorney_doi"]', 'focus', function () {
    var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );  
    console.log(get_default_timezone);
    $('input[name="attorney_doi"]').datepicker({
        maxDate: new Date( get_default_timezone ),
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
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
        onClose: function (selectedDate) {this.focus(); }
    });
});
function contactValidtators(current_option, contact_form_id_val){
	/* What are the mandatory field in Patient Contact that are add/remove here 
		mandatory Field Enabled here. 
	*/
	if(current_option=="guarantor"){		
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_last_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_relationship',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_first_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_status',false);
		//$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
	}
	else if(current_option=="emergency_contact"){
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_last_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_first_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_relationship',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_status',false);
		//$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
	}
	else if(current_option=="employer"){
		//$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_status',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_relationship',false);
		//$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
	}
	else if(current_option=="attorney"){	
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_last_name',false);console.log(contact_form_id_val + " sdfgdf");
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_status',false);
	}
	$('#'+contact_form_id_val).data("bootstrapValidator").resetForm();
	$('#'+contact_form_id_val).bootstrapValidator('validate');
}
$(document).on('change','.guarantor_relationship',function(){
	var guarantor_relationship = $(this).val();
      	if(guarantor_relationship =='Self'){ 
      	    $('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
	        $('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
	        $('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());

			$('input[name="guarantor_last_name"]').attr('readonly', true);
			$('input[name="guarantor_first_name"]').attr('readonly', true);
			$('input[name="guarantor_middle_name"]').attr('readonly', true); 
	} else{
		
			$('input[name="guarantor_last_name"]').val('');
            $('input[name="guarantor_first_name"]').val('');
            $('input[name="guarantor_middle_name"]').val('');

            $('input[name="guarantor_last_name"]').attr('readonly', false);
            $('input[name="guarantor_first_name"]').attr('readonly', false);
            $('input[name="guarantor_middle_name"]').attr('readonly', false); 
            
	    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_last_name');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_first_name');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_middle_name'); 
      
	}
			
});


$(document).on('click', '#js-form-submit-button-v2',function (e) {
	//e.preventDefault();	
	var contact_form_id_val = $(this).attr('data-id');
	contactForm(contact_form_id_val);
	var current_option = $(".js_contact_category_v2").select2('val');
	if($(this).is(".js-v2-edit-contact")){
		current_option = $("#"+contact_form_id_val).find("[id^='edit_contact_category_v2']").val();
	}
	current_option = current_option.replace(" ","_");
	current_option = current_option.toLowerCase();
	/* What are the mandatory field in Patient Contact that are add/remove here 
		mandatory Field Enabled here. 
	*/
	contactValidtators(current_option, contact_form_id_val);
	$('#'+contact_form_id_val).unbind('success').on('success.form.bv', function(ev) {
		ev.preventDefault();
		patient_id = $('#encode_patient_id').val();
		$('#contact-info-footer').html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		var data 		= $('#'+contact_form_id_val).serialize();
		data += "&current_option=" + current_option +"&patient_id=" + patient_id ;
		$.ajax({
			url: api_site_url+'/patients/contact_module',
			type : 'post', 
			data : data,
			success: function(result){
				$('#add_new_contact').modal('hide');
				$('body').removeAttr('style');
				//js_alert_popup('Contact added successfully');
				moveNextTabv2(patient_id, 'contact', 'Contact added successfully.');
			}
		});
		$('#'+contact_form_id_val).off('success');
	});
	$(this).off('click');
});
$('#container').hide();		
$(document).on('keypress keyup','.emp_search_texts',function() {
	var current_form_id     = $(this).parents("form").attr("id");
	emp_search = $("#"+current_form_id).find("input[name='employer_name']").val(); 	
	current = $(this).parents('.js-address-employer');
	if(emp_search.length == "0"){
		$(".emp_search_texts").removeClass('ui-autocomplete-loading');
	}		
	if(api_site_url == "")
		api_site_url = window.location.href;
	src = api_site_url+'/patients/emp_serach/'+emp_search;
	 $(".emp_search_texts").autocomplete({
		source: function(request, response) {
			$.ajax({
				url: src,
				dataType: "json",
				data: {
					term : request.term
				},
				success: function(data) {
					if(data.length > 0){						
						$('#container').hide();												
						response(data);
					}else if(data.length == 0){
						$(".emp_search_texts").removeClass('ui-autocomplete-loading');
					}else{
						$('input[name="exist_emp_id"]').val('create');	
					}						   
				},error: function (error) {
				   $('#container').show();
				//   $(".emp_search_texts").removeClass('ui-autocomplete-loading');
				}
			});
		},select: function( event, ui ) {
			url = api_site_url+'/patients/emp_result/'+ui.item;
			$.ajax({
					url: url,
					dataType: "json",
					data: {
						term : ui.item
					},
					success: function(msg) {
						$(current).find('input[name="employer_address1"]').val(msg.address1)
						$(current).find('input[name="employer_address2"]').val(msg.address2)
						$(current).find('input[name="employer_city"]').val(msg.city)
						$(current).find('input[name="employer_state"]').val(msg.State)
						$(current).find('input[name="employer_zip4"]').val(msg.zip4)
						$(current).find('input[name="employer_zip5"]').val(msg.zip5)
						$(current).find('input[name="employer_work_phone"]').val(msg.work_phone)
						$(current).find('input[name="employer_phone_ext"]').val(msg.work_phone_ext)	
						$('input[name="work_phone"]').val(msg.work_phone);	
						$('input[name="work_phone_ext"]').val(msg.work_phone_ext);	
						$('input[name="exist_emp_id"]').val('no');	
						$(".emp_search_texts").removeClass('ui-autocomplete-loading');		
					}
				});
		},
		change:function(event, ui){
			if($(this).val() == ""){
				//var currcon_id_val_arr = form_name.split('_');
			
				id_val = $('.js_emp_addr_empty').attr("id");
				//console.log("vjhhj"+vjhhj);
				/*$('#'+id_val).each(function() {
					$(this).find(":input").val("");
				});*/
				$('#'+id_val+" :input").val("");
			}
		},
		minLength: 3,			   
	});
});
/*$(document).on('change','.guarantor_relationship_chk',function(){
	var guarantor_relationship = $(this).val();	
	var current_form_id     = $(this).parents("form").attr("id");	
      	if(guarantor_relationship =='Self'){ 
      	    $('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
	        $('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
	        $('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());

 			// for popup form model gurantor in contacts page
 			$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false); 		
 			$("#"+current_form_id+" .same_address").addClass('hide');
 			$("#"+current_form_id+" .self-address").addClass('hide');
 			$("#"+current_form_id+" .address-class").addClass('hide');
 			

			$('input[name="guarantor_last_name"]').attr('readonly', true);
			$('input[name="guarantor_first_name"]').attr('readonly', true);
			$('input[name="guarantor_middle_name"]').attr('readonly', true);

			$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_last_name');
			$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_first_name');
		//	$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_middle_name'); 



	} else{		
			$('input[name="guarantor_last_name"]').val('');
            $('input[name="guarantor_first_name"]').val('');
            $('input[name="guarantor_middle_name"]').val('');
            // for popup form model gurantor in contacts page
            if($("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked') === false){
	            $("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false);
	            $("#"+current_form_id+" .same_address").removeClass('hide');
	            $("#"+current_form_id+" .self-address").removeClass('hide');
	            $("#"+current_form_id+" .address-class").removeClass('hide');
            }
           	

            $('input[name="guarantor_last_name"]').attr('readonly', false);
            $('input[name="guarantor_first_name"]').attr('readonly', false);
            $('input[name="guarantor_middle_name"]').attr('readonly', false); 
            
	    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_last_name');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_first_name');
      //  $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_middle_name'); 
      
	}
			
});*/
function contactFormedit(form_name) {	
	var contact_options_v2 = $('#'+form_name).bootstrapValidator({
		message: 'This value is not valid',
		excluded: ':disabled',
		ignore: ":hidden",
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			'guarantor_last_name': {
				trigger:'keyup',
				validators: {
					notEmpty: {
						message: guar_last_name
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					},
					callback: function (value, validator) {

						
						return true;
					}
				}
			},
			'guarantor_first_name': {
				validators: {
					notEmpty: {
						message: guar_fst_name
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}				
				}
			},
			'guarantor_relationship':{
				validators: {
					notEmpty: {
						message: 'Select guarantor relationship'
					}
				}	
			},
			'guarantor_home_phone': {
				message:'This field is invalid',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var home_phone_msg = home_phone_limit_lang_err_msg;
							var response = phoneValidation(value,home_phone_msg);
							if(response !=true) {
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
			'guarantor_cell_phone': {
				message:'This field is invalid',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var cell_phone_msg = cell_phone_limit_lang_err_msg;
							var response = phoneValidation(value,cell_phone_msg);
							if(response !=true) {
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
			'guarantor_email': {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var response = emailValidation(value);
							if(response !=true) {
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
			'guarantor_address1': {
				validators: {
					/* regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}, */
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							var address1_value = value.trim();
							var add_length = address1_value.length;
							return (add_length !='' && add_length>28) ? false : true;
						}
					}				
				}
			},
			'guarantor_address2': {
				/* validators: {
					regexp:{
						regexp: /^[A-Za-z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}				
				} */
			},
			'guarantor_state': {
				 validators: {
					regexp:{
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					}				
				}
			},
			'guarantor_city': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}				
				}
			},
			'guarantor_zip5': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			'guarantor_zip4': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			'emergency_last_name': {
				 validators: {
					notEmpty: {
						message: lastname_lang_err_msg
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}
					
				}
			},
			'emergency_first_name': {
				validators: {
					notEmpty: {
						message: firstname
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}					
				}
			},
			
			'emergency_home_phone': {
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var home_phone_msg = home_phone_limit_lang_err_msg;
							var response = phoneValidation(value,home_phone_msg);
							if(response !=true) {
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
			'emergency_cell_phone': {
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var cell_phone_msg = cell_phone_limit_lang_err_msg;
							var response = phoneValidation(value,cell_phone_msg);
							if(response !=true) {
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
			'emergency_email': {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var response = emailValidation(value);
							if(response !=true) {
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
			'emergency_address1': {
				validators: {
					regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					},
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							var address1_value = value.trim();
							var add_length = address1_value.length;
							return (add_length !='' && add_length>28) ? false : true;
						}
					}					
				}
			},
			'emergency_address2': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}				
				}
			},			
			'emergency_city': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}				
				}
			},
			'emergency_state': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					}				
				}
			},
			'emergency_zip5': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			'emergency_zip4': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			'employer_status': {
				validators: {
					notEmpty: {
						message: employer_status
					}
					
				}
			},
			/* 'employer_organization_name': {
				validators: {
					callback: {
						callback: function (value, validator,$field) {
							if($field.attr("id") != undefined){
								var currcon_id_val_arr = $field.attr("id").split('-');
								var employment_status_val1 = $('#employer_status-'+currcon_id_val_arr[1]).val();
								if(employment_status_val1=='Employed'||employment_status_val1=='Self Employed'){
									if(value =="") {
										return {
											valid: false, 
											message: 'Enter organization name'
										};
									}
									if(!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value)) {
										return {
											valid: false, 
											message: 'Special characters not allowed'
										};
									}
								}
							}
							return true;
						}
					}
				}
			}, */
			'employer_occupation': {
				validators: {
					callback: {
						callback: function (value, validator, $field) {
							if($field.attr("id") != undefined){
								var currcon_id_val_arr = $field.attr("id").split('-');
								var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).val();
								if(value !="" && (employment_status_val=='Employed'||employment_status_val=='Self Employed')){
									/* if(value =="") {
										return {
											valid: false, 
											message: 'Enter occupation'
										};
									} */
									if(!new RegExp(/^[a-zA-Z0-9 ]+$/).test(value)) {
										return {
											valid: false, 
											message: 'Special characters not allowed'
										};
									}
								}
							}
							return true;
						}
					}
				}
			},
			'employer_student_status': {
				message: '',
				validators: {
					callback: {
						message:'',
						callback: function (value, validator, $field) {
							if($field.attr("id") != undefined){
								var currcon_id_val_arr = $field.attr("id").split('-');
								var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).val();
								if(employment_status_val=='Student'){
									if(value =="") {
										return {
											valid: false, 
											message: 'Select student status'
										};
									}
								}
							}
							return true;
						}
					}
				}
			},
			/*'employer_name': {
				validators: {
					notEmpty: {
						message: employer_name
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}
					
				}
			},*/
			'employer_name': {
				trigger:'keyup change',
				validators: {
					callback: {
						callback: function (value, validator,$field) { 
							if($field.attr("id") != undefined){
								var currcon_id_val_arr = form_name.split('_');
								var employment_status_val = $('#employer_status-'+currcon_id_val_arr[1]).select2('val');
								var contact_category_val = $("#contact_category-0").select2('val');
								/*if((employment_status_val=='Employed'||employment_status_val=='Employed(Part Time)')){
									if(value =="") {
										return {
											valid: false, 
											message: employer_name
										};
									}
									if(!new RegExp(/^[A-Za-z ]+$/).test(value)) {
										return {
											valid: false, 
											message: alphaspace_lang_err_msg
										};
									}
								}*/
							}
							return true;
						}
					}
				}
			},
			'employer_work_phone': {
				message:'This field is invalid',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator,$fields) {
							var phone_msg = phone_limit_lang_err_msg;
							var ext_msg = phone_lang_err_msg;
							var ext_length = $('#'+form_name).find('input[name="employer_phone_ext"]').val().length;
							var response = phoneValidation(value,phone_msg,ext_length,ext_msg);
							if(response !=true) {
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
			'employer_address1': {
				validators: {
					/*regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					},*/
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							var address1_value = value.trim();
							var add_length = address1_value.length;
							return (add_length !='' && add_length>28) ? false : true;
						}
					}				
				}
			},
			'employer_address2': {
				/*validators: {
					regexp:{
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}				
				}*/
			},
			'employer_city': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}				
				}
			},
			'employer_state': {
				validators: {
					regexp:{
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					}				
				}
			},
			'employer_zip5': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			'employer_zip4': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			'attorney_adjuster_name': {
				trigger:'keyup',
				validators: {
					notEmpty: {
						message: adjustor_name
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}							
				}
			},
			'attorney_doi': {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					}
				}
			},
			'attorney_claim_num': {
				validators: {
					regexp: {
						regexp: /^[A-Za-z0-9\s]+$/,
						message: alphanumeric_lang_err_msg
					}
				}
			},
			'attorney_work_phone': {
				message:'This field is invalid',
				trigger: 'change keyup',
				validators:{
					callback: {
						message: '',
						callback: function (value, validator) {
							var phone_msg = phone_limit_lang_err_msg;
							var ext_msg = phone_lang_err_msg;
							$fields = validator.getFieldElements('attorney_work_phone');
							//var ext_length = 0;//$fields.closest("div").next().next().find("input").val().length;
							var response = phoneValidation(value,phone_msg,ext_msg);
							if(response !=true) {
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
		   'attorney_fax': {
				message: '',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var fax_msg = fax_limit_lang_err_msg;
							var response = phoneValidation(value,fax_msg);
							if(response !=true) {
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
			'attorney_email': {
				message: '',
				trigger: 'blur',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var response = emailValidation(value);
							if(response !=true) {
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
			'attorney_zip5': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			'attorney_zip4': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			}
		}
	});
}

/*$(document).on('click','.js-v2-edit-contact',function (event) { 
	var form_id_val = $(this).attr('data-id');	
	form_id_val_arr = form_id_val.split('_');
	edit_contact_id = form_id_val_arr[1];	
	var current_option	= $('#edit_contact_category_e2-'+edit_contact_id).val();	
	current_option = current_option.replace(" ","_");
	current_option = current_option.toLowerCase();	
	contactFormedit(form_id_val);
	$('#'+form_id_val).data("bootstrapValidator").resetForm();
	$('#'+form_id_val).bootstrapValidator('validate');		
	contactValidtator(current_option, form_id_val);

	$('#'+form_id_val).unbind('success').on('success.form.bv', function(e) {
		e.preventDefault();	
		var data 		= $('#'+form_id_val).serialize();			
		var patient_id 	= $('#encode_patient_id').val();		
		$('#edit-contact-info-footer').html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		//$('#v2-contacteditffooter_'+edit_contact_id).html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		$('#v2-contacteditffooter_'+edit_contact_id).html('<i class="fa fa-spinner fa-spin">');
		data += "&current_option="+current_option+"&patient_id="+patient_id+"&edit_type_id="+edit_contact_id+"&add_type=edit";
		$.ajax({
		url: api_site_url+'/patients/contact_module',
			type : 'post', 
			data : data,
			success: function(result){
				//js_alert_popup('Contact updated successfully');
				moveNextTabv2(patient_id, 'contact', 'Contact updated successfully');
				$('body').removeClass('modal-open');
			}
		});	
	});
	$(this).off('click');

});*/


function moveNextTabv2(encode_patient_id, current_tab, alert_msg_val){ console.log("dfgf");
	
	if(current_tab != 'demo') {
		
		if(current_tab == 'archiveinsurance'){
			window.location.href = "";
			js_sidebar_notification('success',alert_msg_val);
		}
		else 
		{
			$.ajax({
			   	url: api_site_url+'/patients/'+encode_patient_id+'/edit/'+current_tab,
			   	type : 'get', 
			   	success: function(msg){			   	
					$('.js-tab-heading').removeClass('active');
					$('.tab-pane').removeClass('active');
					
					if(current_tab == 'insurance'){
						var is_self_pay_val = $('.js-is_self_pay:checked').val();
						if(is_self_pay_val=="No"){
							var exist_selins_ids = $('input[name="pat_inslist_name[]"]:checkbox:checked').map(function(){
							  return $(this).val();
							}).get();
						}
                                               
					}
					
					$("#"+current_tab+"-info").html(msg);	
					if(current_tab == 'insurance'){
						var is_self_pay_val = $('.js-is_self_pay:checked').val();
						if(is_self_pay_val=="No" && exist_selins_ids != 'undefined' && exist_selins_ids != ''){ 
							$.each(exist_selins_ids, function(ins_key,ins_value){
								$("input[name='pat_inslist_name[]'][type=checkbox][value="+ins_value+"]").prop("checked",true);
								$('#v2-insuranceeditform_'+ins_value).removeClass('hide').addClass('show');
							});
						}
                                               
					}
					relationship = $("#guarantor_relationship").val(); 
					if(current_tab == 'contact' && relationship == "Self"){
						$('input[name="guarantor_last_name"]').attr('readonly', true);
						$('input[name="guarantor_first_name"]').attr('readonly', true);
						$('input[name="guarantor_middle_name"]').attr('readonly', true);           
					}
					
					$.AdminLTE.boxWidget.activate();
					$("#"+current_tab+"-info").addClass('active');
					$('#js-tab-heading-'+current_tab).addClass('active');
					if(current_tab == 'insurance')
						$( ".js-add-new-select-opt" ).append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
					$("select.select2.form-control").select2();
					//$('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});	
					//$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' }); 				
					$('#patientnote_model').attr('data-id','session_model');			  
					if(alert_msg_val!="")
						js_sidebar_notification('success',alert_msg_val);
					return false;
				}
			});	
		}
	}
	else {
		$('.js-tab-heading').removeClass('active');
		$('#js-tab-heading-demo').addClass('active');
		
		$('.tab-pane').removeClass('active');
		$("#demo-info").addClass('active');
	}
}

/******** Insurance Info Tab ******************/
function insuranceForm(form_name) { 
	var insurance_options_v2 = $('#'+form_name).bootstrapValidator({
		message: 'This value is not valid',
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			'category': {
				validators: {
					notEmpty: {
						message: category
					},
					callback: {
						callback: function(value, validator) {
							var form_id_val = 0;
							if(form_name!='js-bootstrap-validator-insurance'){
								var form_id_val_arr = form_name.split('_');
								form_id_val = form_id_val_arr[1];
							}
							
							if(value=='Primary'){
								var primary_ins_id = $('#primary_ins_id').val();
								if((form_name=='js-bootstrap-validator-insurance'&&primary_ins_id!='')||(primary_ins_id!=form_id_val&&primary_ins_id!='')){
									return {
										valid: false,
										message: 'Primary category already selected'
									}; 
								}
							} else if(value=='Secondary'){
								var secondary_ins_id = $('#secondary_ins_id').val();
								if((form_name=='js-bootstrap-validator-insurance'&&secondary_ins_id!='')||(secondary_ins_id!=form_id_val&&secondary_ins_id!='')){
									return {
										valid: false,
										message: 'Secondary category already selected'
									}; 
								}
							} else if(value=='Tertiary'){
								var tertiary_ins_id = $('#tertiary_ins_id').val();
								if((form_name=='js-bootstrap-validator-insurance'&&tertiary_ins_id!='')||(tertiary_ins_id!=form_id_val&&tertiary_ins_id!='')){
									return {
										valid: false,
										message: 'Tertiary category already selected'
									}; 
								}
							}
							else if (value == '_empty_'){
								/*
								* Select Option is empty IN insurance Archive Module 
								*/
								return {
										valid: false,
										message: category
									}; 
							}
							return true;
						}
					}
				}
			},
			'insurance_id': {
				validators: {
					notEmpty: {
						message: insurance
					}
				}
			},
			/*'medical_secondary_code': {
				validators: {
					callback: {
						message: medical_secondary_code_lang_err_msg,
						callback: function (value, validator,element) {
							var form_id = element.attr("data-id");
							var type_id	= $("#js_insurancetype_chk_"+form_id).val();
							if(type_id=='' || type_id==null) {
								return true;
							}
							else if(type_id =="error" && value=='') {
								return false
							}
							return true;
						}
					}
				}
			},*/
			
			policy_id: {
				validators: {
					callback: {
						message: '',
						callback: function (value, validator,element) { 
							if(value =="") {
								return {
									valid: false, 
									message: policyid
								};
							}
							if(value !=""){
								var regExp = /^[A-Za-z0-9]+$/;
								if (!regExp.test(value) ){
									return {
										valid: false, 
										message: alphanumeric_lang_err_msg
									};
								}	
							}	
							if(value.length>0){
								
								var form_id_val_arr = element.attr("id").split('-');
								var sel_ins_id		= $("#insurance_id-"+form_id_val_arr[1]).val();
								var ex_policy 		= $("#js_policyid_chk_"+form_id_val_arr[1]).val();
								
								if(sel_ins_id!=''&& ex_policy!='no'&&ex_policy!=''){
									var ex_policy_ids_values = ex_policy.split(',');
									var ccount = 0;
									
									$.each(ex_policy_ids_values, function( key1, value1 ){
										if(value1==value){
											ccount++;
											return false;
										}
									});
									
									if(ccount>0){
										return {
											valid: false,
											message: 'Policy id already used'
										};
									}										
									return true;
								}
							}
							return true;
						}
					}
				}
			},
			'group_name': {
				validators: {
					callback: {
						message: '',
						callback: function (value, validator,element) { 		
								if(value !=""){
									var regExp = /^[a-zA-Z0-9]*$/;
									if (!regExp.test(value)){
										if(value.length>1) {
										return {
											valid: false, 
											message: alphanumeric_lang_err_msg
										};
										}
									}	
								}							
								if(value.length > 28){
									return {
											valid: false,
											message: 'Max length is 28'
										};
								}
							return true;															
						}
					}	
				}
			},
			effective_date: {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '{{ trans("common.validation.effectivedate") }}',
						callback: function (value, validator) {
							var termination_date = validator.getFieldElements('termination_date').val();
							var response = startDate(value,termination_date);
							if (response != true){
								if(value!=""){
									return {
										valid: false,
										message: response
									};  
								}
								return true;	

							/*	if(value==""){
									return {
										valid: false,
										message: 'Enter effective date'
									}; 
								}
								else{
									return {
										valid: false,
										message: response
									}; 
								}*/
							} 
							return true;
						}
					}
				}
			},
			termination_date: {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							var eff_date = validator.getFieldElements('effective_date').val();
							var ter_date = value;
							var response = endDate(eff_date,ter_date);
							if (response != true){
								if(value!=""){
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
			'adjustor_ph': {
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
						callback: function (value, validator) {
							var adjustor_ph_msg = adjustor_ph_limit;
							var response = phoneValidation(value,adjustor_ph_msg);
							if(response !=true) {
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
			'phone': {
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
						callback: function (value, validator) {
							var response = phoneValidation(value,home_phone_limit_lang_err_msg);
							if(response !=true) {
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
			'adjustor_fax': {
				message: '',
				validators: {
					callback: {
						message:'',
						callback: function (value, validator) {
							var adjustor_fax_msg = adjustor_fax;
							var response = phoneValidation(value,adjustor_fax_msg);
							if(response !=true) {
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
			insured_last_name: {
				message:'',
				trigger: 'keyup change',
				validators: {
					notEmpty: {
						message: insured_lstname
					},
					callback: {
						message: '',
						callback: function (value, validator) {  
						var first_name = $('#'+form_name).find("input[name='insured_last_name']").val();
						var last_name = $('#'+form_name).find("input[name='insured_first_name']").val();
						var middle_name = $('#'+form_name).find("input[name='insured_middle_name']").val();
							if (value.length != ''){
								var regExp = /^[A-Za-z' ]+$/;
								if (!regExp.test(value)){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								} else if(value.indexOf("''")!=-1){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								} else { 
									var name_val = isurednameValidation(first_name,last_name,middle_name);									
									if(!name_val){
										return {
											valid: false, 
											message: 'Name field allowed only 29 characters'
										};
									}
									return true;
								}
							}
							return true;
						}
					}
				}
			},
			insured_first_name: {
				message:'',
				trigger: 'keyup change',
				validators: {
					notEmpty: {
						message: insured_fstname
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							var first_name = $('#'+form_name).find("input[name='insured_last_name']").val();
							var last_name = $('#'+form_name).find("input[name='insured_first_name']").val();
							var middle_name = $('#'+form_name).find("input[name='insured_middle_name']").val();
							if (value.length != ''){
								var regExp = /^[A-Za-z' ]+$/;
								if (!regExp.test(value)){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								} else if(value.indexOf("''")!=-1) {
									console.log(value.indexOf("''"));
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								} else {
									var name_val = isurednameValidation(first_name,last_name,middle_name);
									if(!name_val){
										return {
											valid: false, 
											message: 'Name field allowed only 29 characters'
										};
									}
									return true;
								}
							}
							return true;
						}
					}
				}
			},
			insured_middle_name:{
				message:'',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					},
					callback: {
						message: 'Name field allowed only 29 characters',
						callback: function (value, validator) {
							var first_name = $('#'+form_name).find("input[name='insured_last_name']").val();
							var last_name = $('#'+form_name).find("input[name='insured_first_name']").val();
							var middle_name = $('#'+form_name).find("input[name='insured_middle_name']").val();
							var regExp = /^[A-Za-z]+$/;
							//if ($("#last_name").val() != '' && regExp.test(value)) 
								return isurednameValidation(first_name,last_name,middle_name);
							return true;
						}
					}
				}
			},
			'insured_ssn': {
				message:'',
				trigger: 'change keyup',
				validators:{
					regexp: {
						regexp: /^[0-9]{9}$/,
						message: ssn_lang_err_msg
					}
				}
			},
			'insured_dob': {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function(value, validator) {
							if($('form#'+form_name+" .js-insured-dob-part").is(':visible')){
								var insured_dob = value;
								if(insured_dob==""){
									return {
										valid: false,
										message: 'Enter insured dob'
									};
								} else {
									var current_insured_dob=new Date(insured_dob);
									var d_new =new Date();
									if(new RegExp(/^\d{2}\/\d{2}\/\d{4}$/).test(insured_dob) && d_new.getTime() < current_insured_dob.getTime()){
										return {
											valid: false,
											message: valid_dob_format_err_msg
										};
									}
									return true;
								}
							}
							return true;
						}
					}
				}
			},
			'insured_address1': {
				message: '',
				trigger: 'change keyup',
				validators: {
					/*regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					},*/
					notEmpty: {
						message: "Address Line is mandatory"
					},
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							var address1_value = value.trim();
							var add_length = address1_value.length;
							return (add_length !='' && add_length>28) ? false : true;
						}
					}
				}
			},
			'insured_address2': {
				message: '',
				trigger: 'change keyup',
				/*validators: {
					regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumeric_lang_err_msg
					}
				}*/
			},
			'insured_city': {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: "Enter City"
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}

				}
			},
			'insured_state': {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: "Enter State"
					},
					callback: {
						message:'',
						callback: function (value, validator) {
							var regExp = /^[A-Za-z]+$/;
							if (value !='' && regExp.test(value) ==false) {
								return {
									valid: false, 
									message: only_alpha_lang_err_msg
								};
							}
							if(value != '' && $('form#'+form_name+" input[name='insured_state']").val().length <2) {
								return {
									valid: false, 
									message: state_limit_lang_err_msg
								};
							}
							return true;
						}
					}
				}
			},
			'insured_zip5': {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: "Enter Zip"
					},
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			'insured_zip4': {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			 gender: {
                        message: '',
                        trigger:"change ifToggled",
                        validators: {
                            notEmpty: {
                            	 message: 'Select Gender'
                            }
                        }
                    }
		}
	});
}

/*** Insurance type based medicare code category function start ***/
$(document).on('change',".js-add-new-select-opt",function(){
	var get_value = $(this).val();
	var curr_id 	= $(this).attr('id');
	form_id_val_arr = curr_id.split('-');
	curr_insname_id = form_id_val_arr[1];
	checkInsurancetype(get_value,curr_insname_id);
});
/*** Insurance type based medicare code category function end ***/

$(document).on('click','#js-insuranceform-submit-button-v2',function (event) {
	//event.preventDefault();
	var form_type =	$(this).data('method');
	var form_id_val = $(this).attr('data-id');
	var idd = 0;
	
	// Pass patient insurance id for omit current policy id and insurance id to current patient.
	if(form_type == 'move'){
		var patientins_id = $('.js_patient_insurance_id').val();
		policyIdchagevalidation(patientins_id);
	} else {
		policyIdchagevalidation(idd);
	}
	
	insuranceForm(form_id_val);
	$('#'+form_id_val).data("bootstrapValidator").resetForm();
	$('#'+form_id_val).bootstrapValidator('validate');
	$('#'+form_id_val).unbind('success').on('success.form.bv', function(e) {
	//$('#'+form_id_val).on('success.form.bv', function(e) {
		//e.preventDefault();
		$('#insurancetype_id-0').prop("disabled", false);
		//$('#insurancetype_id-0').select2();
		$("#relationship-0").select2('enable',true);
		var data 		= $('#'+form_id_val).serialize();
		var patient_id 	= $('#encode_patient_id').val();
		$('#insurance-info-footer').html('<div class="js_loading"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
		
		if(form_type == 'move'){
			data += "&current_option=move"+"&patient_id=" + patient_id ;
		} else {
			data += "&current_option=new"+"&patient_id=" + patient_id ;	
		}
		
		$.ajax({
			url: api_site_url+'/patients/insurance_module',
			type : 'post', 
			data : data,
			success: function(result){				
				if(form_type == 'move'){
					$('#js_move_insurance_model').modal('hide');
					if(result == 1) {
						moveNextTabv2(patient_id, 'archiveinsurance', 'Moved from archive to active insurance successfully.');	
					} else {
						moveNextTabv2(patient_id, 'archiveinsurance', 'Updated Successfully');	
					}					
				} else {
					$('#add_new_insurance').modal('hide');                                      
					if(result == 1) {
						//Primary insurance is empty or not check here if empty error alert pop up show 
						var primary_ins_id = $('#primary_ins_id').val();
						var insp_category = $('#js-category').val();
						//if Empty show added popup messsage and primary insurance empty message show
						if((primary_ins_id == '') && (primary_ins_id == 'Primary')){
							moveNextTabv2(patient_id, 'insurance', "Insurance Added Successfully, Primary insurance not available ");
						}
						else {	 
							//Else already added primary insurance insurance 
							moveNextTabv2(patient_id, 'insurance', "Insurance Added Successfully");
						}	
					} else {
						moveNextTabv2(patient_id, 'insurance', 'Moved to Archive Successfully');	
					}
				}
				$('body').removeAttr('style');
				//js_alert_popup('Insurance Added successfully');				
			}
		});
	});
	$(this).off('click');
});

$(document).on('click change','.js-v2-edit-insurance',function (event) {
	var form_id_val = $(this).attr('data-id');
	form_id_val_arr = form_id_val.split('_');
	edit_insurance_id = form_id_val_arr[1];
	policyIdchagevalidation(edit_insurance_id);
	/* Revalidation is not work for second time submit key press afer one line is added here*/
	insuranceForm(form_id_val);
	$('#'+form_id_val).data("bootstrapValidator").resetForm();
	$('#'+form_id_val).bootstrapValidator('validate');
	$('#'+form_id_val).on('success.form.bv', function(e) {
		e.preventDefault();
		$('#insurancetype_id-'+edit_insurance_id).prop("disabled", false);
		$('#insurancetype_id-'+edit_insurance_id).select2();
		$("#relationship-"+edit_insurance_id).select2('enable',true);
		var data 		= $('#'+form_id_val).serialize();
		var patient_id 	= $('#encode_patient_id').val();
		//$('#v2-insuranceeditffooter_'+edit_insurance_id).html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		$('#v2-insuranceeditffooter_'+edit_insurance_id).html('<div class="js_loading"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
		data += "&current_option=edit"+"&patient_id="+patient_id+"&edit_insurance_id="+edit_insurance_id;
		$.ajax({
			url: api_site_url+'/patients/insurance_module',
			type : 'post', 
			data : data,
			success: function(result){
				if(result == 1){
					moveNextTabv2(patient_id, 'insurance', 'Insurance updated successfully.');	
				}
				else{
					moveNextTabv2(patient_id, 'insurance', 'Insurance archived successfully.');	
				}
				//js_alert_popup('Updated successfully');
			}
		});
	});
	$(this).off('click');
});

$(document).on('click','.js-v2-insurance-responsible-btn',function (event) {
	var patient_id 	= $('#encode_patient_id').val();
	var get_url = $(this).data('url');	
	var is_self_pay_val = $('.js-is_self_pay:checked').val();
	var data = "current_option=insurance_responsible"+"&patient_id="+patient_id+"&is_self_pay_val="+is_self_pay_val;
	$('.js-v2-insurance-responsible').html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
	$.ajax({
		url: api_site_url+'/patients/insurance_module',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		success: function(result) {
			if(is_self_pay_val=="No"){
				moveNextTabv2(patient_id, 'insurance', '');
				setTimeout(function(){ 
					if(result > 0) {
						$('.js_alert_archive').attr('data-url',get_url);
						$('#patientarchive_model').modal('show');
					} else {
						$('.js-addmore_insurance').trigger('click');
					}
				}, 1700);
			} else {
				moveNextTabv2(patient_id, 'insurance', 'Changed successfully');
			}
		}
	});
});

$(document).on('click','.js-v2-delete-insurance',function (event) {
	var insurance_id = $(this).attr('data-id');
	$("#js_confirm_patient_demo_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		if(conformation == "true") {
			var patient_id 	= $('#encode_patient_id').val();
			var data = "current_option=insurance_delete"+"&patient_id="+patient_id+"&insurance_id="+insurance_id;
			$.ajax({
				url: api_site_url+'/patients/insurance_module',
				headers: {
					'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
				},
				type: "POST",
				data: data,
				success: function(result) {
					//js_alert_popup('Deleted successfully');
					moveNextTabv2(patient_id, 'insurance', result);
				}
			});
		}
	});
});

function authorizationForm(form_name) {
	var authorization_options_v2 = $('#'+form_name).bootstrapValidator({
		message: 'This value is not valid',
		excluded: [':disabled', ':hidden', ':not(:visible)'],
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
		 	'auth_insurance_id': {
				validators: {
					notEmpty: {
						message: auth_insurance
					}				
				}
			},/*
			'pos_id': {
				validators: {
					notEmpty: {
						message: pos
					}				
				}
			}, */
			'authorization_no': {
				validators: {
					notEmpty: {
						message: auth_no
					},
					regexp: {
						regexp: /^[A-Za-z0-9]+$/,
						message: alphanumeric_lang_err_msg
					},
					callback: {
						message: auth_limit,
						callback: function (value, validator, element) {
							//var authorization_no_value = value.trim();
							//var add_length = authorization_no_value.length;
							var authorization_policy_ids = $("#authorization_policy_ids").val();
							var form_id_val_arr = element.attr("id").split('-');
							var cur_auth_id		= form_id_val_arr[1];
							if(authorization_policy_ids!=''){
								var authorization_policy_id_values = authorization_policy_ids.split(',');
								var ccount = 0;
								$.each(authorization_policy_id_values, function( key1, value1 ){
                                    var val_arr = value1.split('::');
									if((cur_auth_id==0&&value==val_arr[1])||(cur_auth_id!=val_arr[0]&&value==val_arr[1])){
										ccount++;
									}
								});
								if(ccount>0){
									return {
										valid: false,
										message: 'Auth number already used'
									};
								}
								return true;
							}
							return true;
						}
					}
				}
			},
			'start_date': {
				message: '',
				trigger: 'keyup change',
				validators: {
					/*
					//####JIRA ISSUE MED-2488
					notEmpty: {
						message: 'Enter start date'
					}, */
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '',
						callback: function (value, validator) {							
							var end_date = validator.getFieldElements('end_date').val();
							if(value != ''){
								var response = startDate(value,end_date);
								if (response != true){
									return {
										valid: false,
										message: 'This date is not before end date'
									}; 
								} 	
							}							
							return true;
						}
					}
				}
			},
			'end_date': {
				message: '',
				trigger: 'keyup change',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'Enter valid date format'//'{{ trans("common.validation.date_format") }}'
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							var start_date = validator.getFieldElements('start_date').val();
							var end_date = value;
							
							if(end_date != ''){
								var response = endDate(start_date,end_date);
								if (response != true){
									return {
										valid: false,
										message: 'This date is not after start date'
									}; 
								} 
							}							
							return true;
						}
					}
				}
			},
			'allowed_visit': {
				message: '',
				trigger: 'keyup change',
				validators: {
					numeric: {
						message: only_numeric_lang_err_msg
					}
				}
			},
			'visits_used': {
				message: '',
				trigger: 'keyup change',
				validators: {
					numeric: {
						message: only_numeric_lang_err_msg
					},
					between: {
						min: 0,
						max: 'allowed_visit',
						message: visits_used
					}
				}
			},
			'alert_visit_remains': {
				message: '',
				trigger: 'keyup change',
				validators: {
					numeric: {
						message: only_numeric_lang_err_msg
					},
					between: {
						min: 0,
						max: 'allowed_visit',
						message: visit_remains
					}
				}
			}
			/*'alert_visit_remains': {
				message: '',
				trigger: 'keyup change',
				validators: {
					numeric: {
						message: only_numeric_lang_err_msg
					},
					callback: {
						message: '',
						callback: function (value, validator,element) {
							var curr_id_val_arr 		= element.attr("id").split('-');
							var allowed_visit_value		= $("#allowed_visit-"+curr_id_val_arr[1]).val();
							var used_visit_value		= $("#visit_used-"+curr_id_val_arr[1]).val();
							if(allowed_visit_value=="")
								allowed_visit_value = 0;
							if(used_visit_value=="")
								used_visit_value = 0;
							if(value!=''){
								if(allowed_visit_value>used_visit_value){
									var remain_val = allowed_visit_value - used_visit_value;
									if(value>=remain_val||value<=0){
										return {
											valid: false,
											message: 'Alert remain visit value invalid'
										};
									}
								}
							}
							return true;
						}
					}
				}
			}*/
		}
	});
}

$(document).on('click','#js-authorizationform-submit-button-v2',function (event) {
	//event.preventDefault();
	var form_id_val = $(this).attr('data-id');
	authorizationForm(form_id_val);
	$('#'+form_id_val).data("bootstrapValidator").resetForm();
	//$('#'+form_id_val).bootstrapValidator('revalidateField', 'start_date');
	//$('#'+form_id_val).bootstrapValidator('revalidateField', 'end_date');
	$('#'+form_id_val).bootstrapValidator('validate');
	$('#'+form_id_val).unbind('success').on('success.form.bv', function(e) {
	//$('#'+form_id_val).on('success.form.bv', function(e) {
		e.preventDefault();
		var data 		= $('#'+form_id_val).serialize();
		var patient_id 	= $('#encode_patient_id').val();
		$('#authorization-info-footer').html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		data += "&current_option=new"+"&patient_id=" + patient_id ;
		$.ajax({
			url: api_site_url+'/patients/authorization_module',
			type : 'post', 
			data : data,
			success: function(result){
				$('#add_new_authorization').modal('hide');
				$('body').removeAttr('style');
				//js_alert_popup('Authorization added successfully');
				moveNextTabv2(patient_id, 'authorization', 'Authorization added successfully');
			}
		});
	});
	$(this).off('click');
});

$(document).on('click','.js-v2-edit-authorization',function (event) {
	//event.preventDefault();
	var form_id_val = $(this).attr('data-id');
	form_id_val_arr = form_id_val.split('_');
	edit_authorization_id = form_id_val_arr[1];
	authorizationForm(form_id_val);
	$('#'+form_id_val).data("bootstrapValidator").resetForm();
	$('#'+form_id_val).bootstrapValidator('validate');
	$('#'+form_id_val).unbind('success').on('success.form.bv', function(e) {
		e.preventDefault();
		var data 		= $('#'+form_id_val).serialize();
		var patient_id 	= $('#encode_patient_id').val();
		//$('#v2-authorizationeditffooter_'+edit_authorization_id).html('<img style="width: 50px; height: 50px;" src="'+api_site_url+'/img/ajax-loader.gif">');
		$('#v2-authorizationeditffooter_'+edit_authorization_id).html('<i class="fa fa-spinner fa-spin">');
		data += "&current_option=edit"+"&patient_id="+patient_id+"&edit_authorization_id="+edit_authorization_id;
		$.ajax({
			url: api_site_url+'/patients/authorization_module',
			type : 'post', 
			data : data,
			success: function(result){
				//js_alert_popup('Authorization updated successfully.');
				moveNextTabv2(patient_id, 'authorization', 'Authorization updated successfully.');
			}
		});
	});
	$(this).off('click');
});

$(document).on('click','.js-v2-delete-authorization',function (event) {
	
	var authorization_id = $(this).attr('data-id');
	$("#js_confirm_patient_demo_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		if(conformation == "true") {
			var patient_id 	= $('#encode_patient_id').val();
			var data = "current_option=authorization_delete"+"&patient_id="+patient_id+"&authorization_id="+authorization_id;
			$.ajax({
				url: api_site_url+'/patients/authorization_module',
				headers: {
					'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
				},
				type: "POST",
				data: data,
				success: function(result) {
					//js_alert_popup('Deleted successfully');
					moveNextTabv2(patient_id, 'authorization', 'Deleted successfully');
				}
			});
		}
	});	
});

$(document).on('click','.js-addmore_authorization_v2', function () {
	$('#add_new_authorization').modal({
		show: 'true'
	});
	$('#js-bootstrap-validator-authorization')[0].reset();
	if($('#js-bootstrap-validator-authorization .form-group.has-feedback').length)
		$('#js-bootstrap-validator-authorization').data("bootstrapValidator").resetForm();
});	


$(document).on('click','.js_move_archiveins', function () {
	var target = $(this).data('url');	
	$("#js_move_insurance_model .modal-body").load(target, function(){		
			$(".select_2").select2();
			//callicheck();
			Check();
			var lfckv = $(".input:checkbox").checked;
	});
});
function Check() {
        var chkPassport = $("#sameaddress-insurance").is(':checked');		
        if (chkPassport.checked) { 
       		$(' .same_address').addClass('hide');
        } else {
       		$(' .same_address').addClass('show');

        }
    }
$(document).on('change','.v2-js-insurance-change-old', function () {
	var curr_id 	= $(this).attr('id');
	var ins_value   = $(this).val();
	form_id_val_arr = curr_id.split('-');
	curr_insname_id = form_id_val_arr[1];
	ins_value_arr = ins_value.split('::'); 
	$('#insurancetype_id-'+curr_insname_id).prop("disabled", false);
	if(ins_value_arr[1]!=0){
		$('#insurancetype_id-'+curr_insname_id).select2('val',ins_value_arr[1]);
		$('#insurancetype_id-'+curr_insname_id).prop("disabled", true);
		checkInsurancetype(ins_value_arr[1],curr_insname_id);
	} else {
		$('#insurancetype_id-'+curr_insname_id).select2('val','');
		$("#relationship-"+curr_insname_id).select2('enable',true);
	}
	//var current_val		= $("#insurancetype_id-"+curr_insname_id).val();
	//checkInsurancetype(current_val,curr_insname_id);
	$('#js-insurance-type-'+form_id_val_arr[1]).children("#add_new_span").removeClass('show').addClass('hide');
	$('#js-insurance-type-'+form_id_val_arr[1]).children(".js_common_ins").removeClass('hide').addClass('show');	
	$('#insurancetype_id-'+curr_insname_id).select2();	
	
	var current_value		= $("#policy_id-"+curr_insname_id).val();
	var sel_ins_id	= $(this).val();//$("#insurance_id-"+curr_policy_id).val();
	if(current_value.trim().length>0 && sel_ins_id !=0) {
		var patient_id 		= $('#encode_patient_id').val();
		var return_response = policyidValidation(patient_id,sel_ins_id,curr_insname_id);
		$("#js_policyid_chk_"+curr_insname_id).val(return_response);
		
		if(curr_insname_id==0)
			var form_id_val = 'js-bootstrap-validator-insurance';
		else
			var form_id_val = 'v2-insuranceeditform_'+curr_insname_id;
		
		if($('#'+form_id_val+" .form-group.has-feedback").length){
			$('#'+form_id_val).data('bootstrapValidator').revalidateField('policy_id');
		}
	}	
});	

function checkInsurancetype(ins_type_id,form_id) {
	var data = "insurance_type_id="+ins_type_id;	
	$.ajax({ type : 'POST',
		url: api_site_url+'/patients/insurance/checktypeid',
		headers: {
			'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
		},
		type: "POST",
		data: data,
		async:false,
		success: function(res){
			var result = res.trim();
			if(result =="error"){
				var form_id_val = $("#relationship-"+form_id).parents("form").attr("id");
				$("#relationship-"+form_id).select2('val','Self');
				$("#relationship-"+form_id).select2('disable');
				//$("#relationship-"+form_id).select2();
				$("#prev_relationship-"+form_id).val('Self');
				
				var self_last_name 	= $("#self_last_name").val(); 
				var self_first_name = $("#self_first_name").val(); 
				var self_middle_name 	= $("#self_middle_name").val(); 
				var self_ssn = $("#self_ssn").val(); 
				var self_gender = $("#self_gender").val(); 
				var self_dob = $("#self_dob").val();
				var self_address1 = $("#self_address1").val();
				var self_address2 = $("#self_address2").val();
				var self_city = $("#self_city").val();
				var self_state = $("#self_state").val();
				var self_zip5 = $("#self_zip5").val();
				var self_zip4 = $("#self_zip4").val();
				$('#gender-'+form_id+' input[name="gender"]:radio[value="'+self_gender+'"]').prop("checked", true);
				$("#insured_last_name-"+form_id).val(self_last_name).attr('readonly', true);; 
				$("#insured_first_name-"+form_id).val(self_first_name).attr('readonly', true);; 
				$("#insured_middle_name-"+form_id).val(self_middle_name).attr('readonly', true);; 
				$("#insured_ssn-"+form_id).val(self_ssn).attr('readonly', true);; 
				$("#insured_dob-"+form_id).val(self_dob).attr('readonly', true);;
				$("#insured_address1-"+form_id).val(self_address1);
				$("#insured_address2-"+form_id).val(self_address2);
				$("#insured_city-"+form_id).val(self_city);
				$("#insured_state-"+form_id).val(self_state);
				$("#insured_zip5-"+form_id).val(self_zip5);
				$("#insured_zip4-"+form_id).val(self_zip4);
				
				$('#'+form_id_val+" .js-same_as_patient_address-v2").prop("checked", true);

				
				$('#gender-'+form_id+' input[name="gender"]:radio').attr("disabled", true);
				$('#gender-'+form_id+' input[name="gender"]:radio[value="'+self_gender+'"]').attr("disabled", false);
				
				$("#insuredrelation_part-"+form_id).removeClass('show').addClass('hide');
				
			}
			else{
				$("#relationship-"+form_id).select2('enable',true);
			}
			/*$("#js_insurancetype_chk_"+form_id).val(result);
			if(result =="error")
				$(".js_medicareins_"+form_id).removeClass("hide");
			else
				$(".js_medicareins_"+form_id).addClass("hide");
			if($(".js_medicareins_"+form_id).hasClass("has-feedback"))
				$("#v2-insuranceeditform_"+form_id).data('bootstrapValidator').revalidateField('medical_secondary_code');*/
		}
	});
}

function startDate(start_date,end_date) {
	var date_format = new Date(end_date);
	if (end_date != '' && date_format !="Invalid Date") {
		return (start_date == '') ? eff_date_valid:true;
	}	
	return true;
}

function endDate(start_date,end_date) {
	var eff_format = new Date(start_date);
	var ter_format = new Date(end_date);
	if (ter_format !="Invalid Date" && end_date != '' && eff_format !="Invalid Date" && end_date.length >7 && checkvalid(end_date)!=false) {
		var getdate = daydiff(parseDate(start_date), parseDate(end_date));
		return (getdate > 0) ? true : ter_date_valid;
	} else if (start_date != '' && eff_format !="Invalid Date") {
		return (end_date == '') ? ter_date_valid:true;
	
	}
	return true;
}

function checkvalid(str) {
	var mdy = str.split('/');
	if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
		return false;
	}
}

$(document).on('focusin', 'input[name="policy_id"]', function () {
	var curr_id 	= $(this).attr('id');
	form_id_val_arr = curr_id.split('-');
	curr_policy_id = form_id_val_arr[1];
	$("#js_policyid_chk_"+curr_policy_id).val("no");
});

/*$(document).on('focusout', 'input[name="policy_id"]', function () {
	var current_value		= $(this).val();
	var sel_ins_id	= $("#insurance_id-"+curr_policy_id).val();
	if(current_value.trim().length>0 && sel_ins_id !=0) {
		var curr_id 	= $(this).attr('id');
		form_id_val_arr = curr_id.split('-');
		curr_policy_id 	= form_id_val_arr[1];
		var sel_ins_id_arr 	= sel_ins_id.split('::');
		var patient_id 		= $('#encode_patient_id').val();
		var return_response = policyidValidation(patient_id,sel_ins_id_arr[0],curr_policy_id);
		$("#js_policyid_chk_"+curr_policy_id).val(return_response);
		
		if(curr_policy_id==0)
			var form_id_val = 'js-bootstrap-validator-insurance';
		else
			var form_id_val = 'v2-insuranceeditform_'+curr_policy_id;
		
		if($('#'+form_id_val+" .form-group.has-feedback").length){
			$('#'+form_id_val).data('bootstrapValidator').revalidateField('policy_id');
		}
	}
});*/


function policyIdchagevalidation(policy_id_index) {
	var current_value		= $("#policy_id-"+policy_id_index).val();
	var sel_ins_id	= $("#insurance_id-"+policy_id_index).val();
	if(current_value.trim().length>0 && sel_ins_id !=0) {
		curr_policy_id 	= policy_id_index;
		var sel_ins_id_arr 	= sel_ins_id.split('::');
		var patient_id 		= $('#encode_patient_id').val();
		var return_response = policyidValidation(patient_id,sel_ins_id_arr[0],curr_policy_id);
		$("#js_policyid_chk_"+curr_policy_id).val(return_response);
		if(curr_policy_id==0)
			var form_id_val = 'js-bootstrap-validator-insurance';
		else
			var form_id_val = 'v2-insuranceeditform_'+curr_policy_id;
		
		if($('#'+form_id_val+" .form-group.has-feedback").length){
			$('#'+form_id_val).data('bootstrapValidator').revalidateField('policy_id');
		}
	}
}

function policyidValidation(patient_id,sel_ins_id,curr_policy_id) {
	
	var data = "current_option=check_ins_policy"+"&patient_id="+patient_id+"&sel_ins_id="+sel_ins_id+"&cur_ins_id="+curr_policy_id;
	return $.ajax({
				url: api_site_url+'/patients/insurance_module',
				headers: {
					'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
				},
				type: "POST",
				data: data,
				async:false,
				success: function(res){	}
			}).responseText;
}

$(document).on( 'change', '.v2-js-employment_status', function () {
	var employment_status_val = $(this).val();

	var curr_id 		= $(this).attr('id');
	//console.log(employment_status_val+"employment_status_val");
	contact_id_val_arr 	= curr_id.split('-');	
	curr_contact_id 	= contact_id_val_arr[1];	
	var current_form_id     = $(this).parents("form").attr("id");
	if(employment_status_val=='Employed'||employment_status_val=='Self Employed'){
		$("#"+current_form_id+" .employed_option_sub_field").removeClass('hide').addClass('show');
		$("#"+current_form_id+" .emp-status-class").removeClass('hide').addClass('show');
		$("#"+current_form_id+" .employer-retired-field").removeClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		if(employment_status_val=='Employed' ||employment_status_val=='Self Employed' ){
			$("#employer_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		} else {
			$("#employer_option_sub_field-"+curr_contact_id).addClass('hide');
		}
		
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).removeClass('hide');
	} else if(employment_status_val=='Student'){
		if($("#employer_student_status-"+curr_contact_id).val()=="")
		$("#employer_student_status-"+curr_contact_id).select2('val','Unknown');
		$("#student_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).removeClass('hide');
	} else if(employment_status_val=='Employed(Part Time)')	{

		$("#"+current_form_id+" .emp-status-class").removeClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');		
		$("#employer-retired-field-"+curr_contact_id).removeClass('hide');
	} else if(employment_status_val=='Retired'){
		$("#"+current_form_id+" .emp-status-class").addClass('hide');
		$("#"+current_form_id+" .employer-retired-field").addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).addClass('hide');
	
	}else if(employment_status_val=='Active Military Duty'){
		$("#"+current_form_id+" .emp-status-class").removeClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');		
		$("#employer-retired-field-"+curr_contact_id).removeClass('hide');
	} else {
		$("#"+current_form_id+" .emp-status-class").addClass('hide');
		$("#"+current_form_id+" .employer-retired-field").addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).addClass('hide');

	}
	
	if(curr_contact_id==0)
		var form_id_val = 'js-bootstrap-validator-contact';
	else
		var form_id_val = 'v2-contacteditform_'+curr_contact_id;
	if($('#'+form_id_val+" .form-group.has-feedback").length){
		//$('#'+form_id_val).data('bootstrapValidator').revalidateField('employer_organization_name');
		//$('#'+form_id_val).data('bootstrapValidator').revalidateField('employer_occupation');
		//$('#'+form_id_val).data('bootstrapValidator').revalidateField('employer_student_status');
		//$('#'+form_id_val).data('bootstrapValidator').revalidateField('employer_name');
	}
});

$(document).on('click','.js-delete-patient-image',function (event) {
	
	var patient_id = $(this).attr('data-id');
	$("#js_confirm_patient_demo_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		if(conformation == "true") {			
			$.ajax({
			   url: api_site_url+'/patients/delete_patient_picture/'+patient_id,
			   type : 'get', 
			   success: function(msg){
					$(".js-delete-patient-image").addClass('hide'); 
					$(".js_patient_img_part img").attr('src', api_site_url+"/img/patient_noimage.png");
					js_alert_popup('Image removed successfully');
				}
			});			
		}
	});	
});

$(document).on('keyup',  function (e) {
	setTimeout(function(){ 
		if($('#js-model-insurance-details').is(':visible')) {
			var key = e.which;
			if (key == 13) {
				$('.js_modal_search_insurance_button').trigger('click');
			}
			else if (key == 27) {
				$('.modal-header .close').trigger('click');
			}
		}
		if($('#js-model-swith-patient').is(':visible')) {			
			var key = e.which;
			if (key == 13) {
				$('.js_modal_search_swith_patient_button').trigger('click');
			} else if (key == 27) {
				$('.modal-header .close').trigger('click');
			}
		}
	}, 5);
});




$(document).on( 'click', 'input[name="pat_inslist_name[]"]:checkbox', function () {
	var sel_ins_id = $(this).val();
	insuranceForm('v2-insuranceeditform_'+sel_ins_id);
						
	if ($(this).prop('checked')==true) {
		$("#v2-insuranceeditform_"+sel_ins_id+" .js-address-check").trigger('blur');
		$('#v2-insuranceeditform_'+sel_ins_id).removeClass('hide').addClass('show');
	} else {
		$('#v2-insuranceeditform_'+sel_ins_id).removeClass('show').addClass('hide');
	}
});

$(document).on('keyup','input.dm-phone-exts', function () {
	var current_class = $(this).attr('name');
	var form_name = $(this).parents("form").attr('name');
	var form_id = $(this).parents("form").attr('id');
	var finder_name = (form_name ==''|| form_name ==null) ?  "#"+form_id :'[name="'+form_name+'"]';
	if(current_class.match("ext").length > 0) {
		var another_name = $(this).closest("div").prev().prev().find("input").attr("name");
		if(another_name == null || another_name =="") {
			var another_name = $(this).closest('div').prev().find('input').attr("name");
		}
		if($(this).closest(".form-group").hasClass("has-feedback")) { 
			contactFormedit(form_id);
			$('#'+form_id).data("bootstrapValidator").resetForm();
			$('#'+form_id).bootstrapValidator('validate');			
		}
	}	
});

function isurednameValidation(last,first,middle) {
	var last_name = last;
	var first_name = first;
	var middle_name = middle;
	var ln_val = last_name.trim();
	var fn_val = first_name.trim();
	var mn_val = middle_name.trim();
	var add_length = ln_val.length + fn_val.length + mn_val.length;
	return (add_length>28) ? false : true;
}

$(document).on('keyup','input[name="insured_last_name"]',function(){
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_last_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_first_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_middle_name');
});

$(document).on('keyup','input[name="insured_first_name"]',function(){
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_last_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_first_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_middle_name');
});

$(document).on('keyup','input[name="insured_middle_name"]',function(){
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_last_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_first_name');
	$('#'+$(this).closest("form").attr('id')).bootstrapValidator('revalidateField', 'insured_middle_name');
});
/* MED-2735 JIRA issue */
$(document).on('keyup','#js-bootstrap-validator-contact',function(){	
	setTimeout(function(){
		//$("#etin_type_number").inputmask("mask", {"mask": "999-99-9999"});
		$("#js-form-submit-button-v2").removeAttr("disabled");
	}, 50);  
});
