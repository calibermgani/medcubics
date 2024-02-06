$(document).on('click','.edit-contact-form-model',function (e) {
	var contact_id = $(this).attr('data-id');
	var category_type = $(this).attr('data-category-type');
	current_form_id = $(this).parents("form").attr("id");//console.log(current_form_id);	
	$('#add_edit_contact').modal({ show: 'true'}).attr("data_id",contact_id);
	$('#add_edit_contact').html($('.js_add_edit_contact_form').html());
//	$("#add_edit_contact .select_2").select2();
	$('.js-category-title-e2').html('Edit '+category_type);//console.log(contact_id);
	
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
		var guarantor_select = $("#"+current_form_id).find("#guarantor_realationship_e1").val();
		$("#"+current_form_id+" .js-address-check").trigger();
		if(guarantor_select == 'Self'){
			$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_last_name"]').attr('readonly', true);
			$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_first_name"]').attr('readonly', true);
			$(".js-bootstrap-validator-contact-edit").find('input[name="guarantor_middle_name"]').attr('readonly', true);
		//	$("#edit-contact_"+contact_id+" .self-address").addClass('hide');
		}	
		$('#guarantor_relationship option[value="'+guarantor_select+'"]').attr('selected','selected');
		$("#add_edit_contact .select_2").select2();
		$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
		$('.js-e2-edit-contact').attr('id', 'edit-contact_'+contact_id);
		$('.js-e2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);

		if($("#"+current_form_id).find("#edit-sameaddress-insurance").val() =='yes'){
			$("#edit-contact_"+contact_id+" .same_address").addClass('hide');
			$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").prop("checked",true);	
		}else{
			//$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
			$("#edit-contact_"+contact_id+" .same_address").removeClass('hide');
		}
		if(guarantor_select == 'Self'){
			$("#edit-contact_"+contact_id+" .self-address").addClass('hide');
			$("#edit-contact_"+contact_id+" .same_address").addClass('hide');
			$("#edit-contact_"+contact_id+" .address-class").addClass('hide');
		}		
	}
	if(category_type == 'Emergency Contact'){	 	
		$('#v2-edit-emergency').removeClass('hide').addClass('show');
		
		var elems = ['emergency_last_name', 'emergency_first_name', 'emergency_middle_name', 'emergency_relationship', 'emergency_address1', 'emergency_address2',
		'emergency_city', 'emergency_state','emergency_zip4', 'emergency_zip5', 'emergency_home_phone', 'emergency_cell_phone', 'emergency_email'];
		elems.forEach(myFunction);
		function myFunction(item, index) {			
			$(".js-bootstrap-validator-contact-edit")
			.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item+"_e1").text());
		}
		var emergency_state =  $("#"+current_form_id).find("#emergency_state_e1").text().trim();
		$(".js-bootstrap-validator-contact-edit")
			.find("input[name=emergency_state]").val(emergency_state);	
		var emergency_select = $("#"+current_form_id).find("#emergency_relationship_e1").val();	

		$('#emergency_relationship option[value="'+emergency_select+'"]').attr('selected','selected');
		$("#add_edit_contact .select_2").select2();
		$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
		$('.js-e2-edit-contact').attr('id', 'edit-contact_'+contact_id);
		$('.js-e2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);

		if($("#"+current_form_id).find("#emergency-sameaddress-insurance").val() =='yes'){
			$("#edit-contact_"+contact_id+" .same_address").addClass('hide');
			$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").prop("checked",true);
		}else{
			//$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
			$("#edit-contact_"+contact_id+" .same_address").removeClass('hide');
		}
		$("#edit-contact_"+contact_id+" .self-address").removeClass('hide');
		//$("#edit-contact_"+contact_id+" .same_address").removeClass('hide');
		$("#edit-contact_"+contact_id+" .address-class").removeClass('hide');	
	}
	if(category_type == 'Employer'){
		$('#v2-edit-employer').removeClass('hide').addClass('show');
		$('.employed_option_sub_field').removeClass('hide').addClass('show');
		var elems = ['employer_status', 'employer_name', 'employer_occupation', 'employer_address1', 'employer_address2', 'employer_city',
		'employer_state', 'employer_zip5', 'employer_zip4', 'employer_work_phone', 'employer_phone_ext'];
		elems.forEach(myFunction);		
		function myFunction(item, index) {		
			$(".js-bootstrap-validator-contact-edit")
			.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item+"_e1").text());		
		}			
		var employer_status =  $("#"+current_form_id).find("#employer_status_e1").text().trim();
		//console.log(employer_status);
		//$('#edit_employer_status option[value="'+employer_status+'"]').attr('selected','selected');
		$("#add_edit_contact .select_2").select2();
		$("select#edit_employer_status").select2("val", employer_status).trigger("change"); 
		$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
		$('.js-e2-edit-contact').attr('id', 'edit-contact_'+contact_id);
		$('.js-e2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);
		if(employer_status == 'Retired'){
			$("#edit-contact_"+contact_id+" .employed_option_sub_field").removeClass('show').addClass('hide');
			$("#edit-contact_"+contact_id+" .employer-retired-field").removeClass('show').addClass('hide');				
		}
		if(employer_status == 'Self Employed'){
			$("#edit-contact_"+contact_id+" .emp-status-class").removeClass('hide');
		//	$("#edit-contact_"+contact_id+" .employer-retired-field").removeClass('hide');	
		}if(employer_status == 'Active Military Duty'){
			$("#edit-contact_"+contact_id+" .emp-status-class").removeClass('show').addClass('hide');
			$("#edit-contact_"+contact_id+" .employer-retired-field").removeClass('hide').addClass('show');
		}
		if(employer_status == 'Unknown'){
			$("#edit-contact_"+contact_id+" .employed_option_sub_field").removeClass('show').addClass('hide');
			$("#edit-contact_"+contact_id+" .employer-retired-field").removeClass('show').addClass('hide');		
		}
		if(employer_status == 'Employed'){
			$("#edit-contact_"+contact_id+" .emp-status-class").removeClass('hide').addClass('show');
		}
	}
	if(category_type == 'Attorney'){
		$('#v2-edit-attorney').removeClass('hide').addClass('show');
	//	$('.employed_option_sub_field').removeClass('hide').addClass('show');
		var elems = ['attorney_adjuster_name', 'attorney_doi', 'attorney_work_phone', 'attorney_phone_ext', 'attorney_fax', 'attorney_email',
		'attorney_address1', 'attorney_address2', 'attorney_city', 'attorney_state', 'attorney_zip5','attorney_zip4'];
		elems.forEach(myFunction);		
		function myFunction(item, index) {		
			$(".js-bootstrap-validator-contact-edit")
			.find("input[name="+item+"]").val($("#"+current_form_id).find("#"+item+"_e1").text());		
		}			
		var values =  $("#"+current_form_id).find("#attorney_claim_num_e1").text().trim();
		$.each(values.split(","), function(i,e){
		    $("#edit_attorney_claim_number option[value='" + e + "']").prop("selected", true);
		});
		$("#add_edit_contact .select_2").select2();
	//	$("select#edit_employer_status").select2("val", employer_status).trigger("change"); 
		$('.v2-contact-info-form-edit').attr('id', 'edit-contact_'+contact_id);	
		$('.js-e2-edit-contact').attr('id', 'edit-contact_'+contact_id);
		$('.js-e2-edit-contact').attr('data-id', 'edit-contact_'+contact_id);		
	}
	$("#edit-contact_"+contact_id+" .js-address-check").trigger("blur");
});
 
// Realationship for edit blade
$(document).on('change','.guarantor_relationship_chk_edit',function(){
	var guarantor_relationship = $(this).val();	
	var current_form_id     = $(this).parents("form").attr("id");	
	//console.log(current_form_id+"cur form");
	if(guarantor_relationship =='Self'){ 
		$('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
		$('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
		$('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());
		// if previously  checked means have to uncheck
		$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false); 
		$("#"+current_form_id+" .same_address").addClass('hide');
		$("#"+current_form_id+" .self-address").addClass('hide');
		$("#"+current_form_id+" .address-class").addClass('hide');

		$('input[name="guarantor_last_name"]').attr('readonly', true);
		$('input[name="guarantor_first_name"]').attr('readonly', true);
		$('input[name="guarantor_middle_name"]').attr('readonly', true);

		var elems = ['guarantor_address1', 'guarantor_address2',
		'guarantor_city', 'guarantor_state', 'guarantor_zip5', 'guarantor_zip4', 'guarantor_home_phone', 'guarantor_cell_phone', 'guarantor_email'];

		elems.forEach(myFunction);
		function myFunction(item, index) {			
			$(".js-bootstrap-validator-contact-edit")
			.find("input[name="+item+"]").val('');			
		}

	} else{		
		$('input[name="guarantor_last_name"]').val('');
		$('input[name="guarantor_first_name"]').val('');
		$('input[name="guarantor_middle_name"]').val('');  

		if($("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked') === false){
			$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false);
			$("#"+current_form_id+" .same_address").removeClass('hide');
			$("#"+current_form_id+" .self-address").removeClass('hide');
			$("#"+current_form_id+" .address-class").removeClass('hide');
		}
		$('input[name="guarantor_last_name"]').attr('readonly', false);
		$('input[name="guarantor_first_name"]').attr('readonly', false);
		$('input[name="guarantor_middle_name"]').attr('readonly', false); 
	}
});

$(document).on('change','.guarantor_relationship_chk',function(){
	var guarantor_relationship = $(this).val();	
	var current_form_id     = $(this).parents("form").attr("id");
	if(guarantor_relationship =='Self'){ 
		$('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
		$('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
		$('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());		

		$('input[name="guarantor_last_name"]').attr('readonly', true);
		$('input[name="guarantor_first_name"]').attr('readonly', true);
		$('input[name="guarantor_middle_name"]').attr('readonly', true);

		$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false); 
		$("#"+current_form_id+" .same_address").addClass('hide');
		$("#"+current_form_id+" .self-address").addClass('hide');
		$("#"+current_form_id+" .address-class").addClass('hide');

		$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_last_name');
		$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_first_name');
	//	$("#"+current_form_id).bootstrapValidator('revalidateField', 'guarantor_middle_name'); 
	} else{		
		$('input[name="guarantor_last_name"]').val('');
		$('input[name="guarantor_first_name"]').val('');
		$('input[name="guarantor_middle_name"]').val('');
		
	  //   if($("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked') === false){
			$("#"+current_form_id).find("input[name='same_as_patient_address'][type=checkbox]").prop('checked',false);
			$("#"+current_form_id+" .same_address").removeClass('hide');
			$("#"+current_form_id+" .self-address").removeClass('hide');
			$("#"+current_form_id+" .address-class").removeClass('hide');
	  //  }

		$('input[name="guarantor_last_name"]').attr('readonly', false);
		$('input[name="guarantor_first_name"]').attr('readonly', false);
		$('input[name="guarantor_middle_name"]').attr('readonly', false); 
            
	    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_last_name');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_first_name');
      //  $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'guarantor_middle_name');       
	}			
});

// same as address check
$(document).on('click change', 'input:checkbox.js-same_as_patient_address-v2', function () { 
	var current_div_id 	= $(this).closest(".js-address-class").attr("id");
	var current_form_id     = $(this).parents("form").attr("id");

	if ($('#'+current_form_id+' #'+current_div_id+' input:checkbox.js-same_as_patient_address-v2').prop('checked') === true) {
          
		$('#'+current_form_id+' #'+current_div_id+' #emergency_address1').val($('#self_address1').val());
		$('#'+current_form_id+' #'+current_div_id+' #emergency_address1').val($('#self_address2').val());
		$('#'+current_form_id+' #'+current_div_id+' #emergency_city').val($('#self_city').val());
		$('#'+current_form_id+' #'+current_div_id+' #emergency_state').val($('#self_state').val());
		$('#'+current_form_id+' #'+current_div_id+' #emergency_zip5').val($('#self_zip5').val());
		$('#'+current_form_id+' #'+current_div_id+' #emergency_zip4').val($('#self_zip4').val());
       	
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_address1').val($('#self_address1').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_address2').val($('#self_address2').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_city').val($('#self_city').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_state').val($('#self_state').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_zip5').val($('#self_zip5').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_zip4').val($('#self_zip4').val());  

       	$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_address1').val($('#self_address1').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_address2').val($('#self_address2').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_city').val($('#self_city').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_state').val($('#self_state').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_zip5').val($('#self_zip5').val());
		$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_zip4').val($('#self_zip4').val()); 

		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_address1"]').val($('#self_address1').val());  
		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_address2"]').val($('#self_address2').val());  
		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_city"]').val($('#self_city').val());  
		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_state"]').val($('#self_state').val());  
		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_zip5"]').val($('#self_zip5').val());  
		$('#'+current_form_id+' #'+current_div_id+' input[name="insured_zip4"]').val($('#self_zip4').val());  
		//$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_zip4').val($('#self_zip4').val());  
		//MR-2738 issue fixed
		if(current_div_id != "v2-emergency_contact" && current_div_id != "v2-edit-emergency"){
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_address1');
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_address2');
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_city');
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_state');
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_zip5');
			$('#'+current_form_id).data('bootstrapValidator').revalidateField('insured_zip4');
		}

		setTimeout(function() {
            $('#'+current_form_id+' #'+current_div_id+' .same_address').addClass('hide');               
        }, 100);  
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-address1').blur();		
	} else {		
		setTimeout(function () { 
			$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_address1').val('');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_address1').val('');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_city').val('');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_state').val('');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_zip5').val('');
			$('#'+current_form_id+' #'+current_div_id+' #emergency_zip4').val('');

			$('#'+current_form_id+' #'+current_div_id+' #guarantor_address1').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_address2').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_city').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_state').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_zip5').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_zip4').val('');  

			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_address1').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_address2').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_city').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_state').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_zip5').val('');
			$('#'+current_form_id+' #'+current_div_id+' #guarantor_edit_zip4').val(''); 

			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_address1"]').val('');  
			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_address2"]').val(''); 
			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_city"]').val(''); 
			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_state"]').val(''); 
			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_zip5"]').val(''); 
			$('#'+current_form_id+' #'+current_div_id+' input[name="insured_zip4"]').val(''); 

			$('#'+current_form_id+' #'+current_div_id+' .js-address-error').addClass('hide');
			$('#'+current_form_id+' #'+current_div_id+' .js-address-success').addClass('hide');
			$('#'+current_form_id+' #'+current_div_id+' .same_address').removeClass('hide');
		}, 100);
	}
});

// edit form submit
$(document).on('click','.js-e2-edit-contact',function (event) { 
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
	//console.log(form_id_val+"haii test");
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
});

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
					},
					callback: {
						message:'',
						callback: function (value, validator, $field) {
							if(value == 'Self'){
								var test =[];
								$.each( $('#contact-info .e2-contact-info-form'), function(i, left) {
									var forms = $(this).attr("id");
									var	relations = $('#'+forms).find('#guarantor_realationship_e1').val();
									test.push( relations); 
								});
								var form_id = form_name.split("_");
								var gua_relation = $('#e2-contacteditform_'+form_id[1]).find('#guarantor_realationship_e1').val();							
								
								if(test.indexOf("Self") != -1){
									if(gua_relation != 'Self'){
										return {
											valid: false, 
											message: 'Self gurantor Already Exist'
										};
									}
								}							
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
			'emergency_relationship':{
				validators: {
					notEmpty: {
						message: 'Select emergency relationship'
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
			enabled: false,
			trigger:'keyup',
            validators: {
                notEmpty: {
                    message: "Enter Attorney/Adjuster name"
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

function contactValidtator(current_option, contact_form_id_val){
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
	//	$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
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
	//	$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
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
	//	$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',false);
	}
	else if(current_option=="attorney"){console.log("trdsf");
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'attorney_adjuster_name',true);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'guarantor_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_last_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_first_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'emergency_relationship',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_name',false);
		$('#'+contact_form_id_val).bootstrapValidator('enableFieldValidators', 'employer_status',false);
	}
	$('#'+contact_form_id_val).data("bootstrapValidator").resetForm();
	$('#'+contact_form_id_val).bootstrapValidator('validate');
}

$(document).on( 'change', '.v2-js-employment_status-edit', function () {
	var employment_status_val = $(this).val();

	var curr_id 		= $(this).attr('id');
//	console.log(employment_status_val+"employment_status_val");
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
	} else if(employment_status_val=='Retired' || employment_status_val=='Unknown' ){ 
		$("#"+current_form_id+" .emp-status-class").addClass('hide');
		$("#"+current_form_id+" .employer-retired-field").addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).addClass('hide');

	}else if(employment_status_val=='Active Military Duty'){
		$("#"+current_form_id+" .emp-status-class").removeClass('show').addClass('hide');
		$("#"+current_form_id+" .employer-retired-field").removeClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).removeClass('hide').addClass('show');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');		
		$("#employer-retired-field-"+curr_contact_id).removeClass('hide');
	} /* remoed for 1. Employer edit: Address field is not showing
	else { 
		$("#"+current_form_id+" .emp-status-class").addClass('hide');
		$("#"+current_form_id+" .employer-retired-field").addClass('hide');
		$("#employed_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#student_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer_option_sub_field-"+curr_contact_id).addClass('hide');
		$("#employer-retired-field-"+curr_contact_id).addClass('hide');
	}*/
	
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

function moveNextTabv2(encode_patient_id, current_tab, alert_msg_val){
	
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
								insuranceForm('v2-insuranceeditform_'+ins_value);
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

					if(alert_msg_val!=""){
						if(current_tab == 'contact'){
							if(alert_msg_val == 'Deleted successfully'){
									js_sidebar_notification('success',alert_msg_val);
								}else if(alert_msg_val == 'Contact added successfully.'){
									js_sidebar_notification('success',alert_msg_val);
								}else if(alert_msg_val == 'Contact updated successfully'){
									js_sidebar_notification('success',alert_msg_val);
								}				
								else{
									js_sidebar_notification('error',alert_msg_val);
								}
						}else{
							js_sidebar_notification('success',alert_msg_val);
						}
					}
					if(current_tab == 'insurance'){
						var is_self_pay_val = $('.js-is_self_pay:checked').val();
						if(is_self_pay_val=="No" && exist_selins_ids != 'undefined' && exist_selins_ids != ''){ 
							$.each(exist_selins_ids, function(ins_key,ins_value){
								$('#v2-insuranceeditform_'+ins_value+' .js-address-check').trigger('click');								
							});
						}                                               
					}
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