/**** Start to check insurance eligiblity ***/
$(document).on('click', '.js-patient-eligibility_check',function (e) {
	
	var page = $(this).attr('data-page');  
	if(page =="app_listing") {
		var unique_id = $(this).attr('data-unqid');
		var patient_id = $(this).attr('data-patientid');
	}
	else {
		var patient_id = $(this).data('patientid');
		var unique_id = patient_id;
	}
	var category = $(this).data('category');
	var insid = $(this).data('insid');
	var policyid = $(this).data('policyid');
	if(page == "eligibility"){		
		//var insid = $('input[name="insid"]').val();
		var category = $('input[name="category"]').val();
		 var ins_id = $('select[name="insurance_id"]').val();
		 var elig_dos_from = $('input[id="elig_dos_from"]').val();
		 var elig_dos_to = $('input[id="elig_dos_to"]').val();
	 	if(ins_id == ''){
	 		js_alert_popup("Select insurance");
	 		return false;	
	 	}
	}else{
		elig_dos_from = elig_dos_to = '';
	}
	if(category==undefined)
		var par = 'patient_id='+patient_id+'&insuranceid='+insid+'&policyid='+policyid;
	else
		var par = 'patient_id='+patient_id+'&category='+category; 
	
	
	if(elig_dos_from != '' && elig_dos_to != '')
		var par = par+'&dos_from='+btoa(elig_dos_from)+'&dos_to='+btoa(elig_dos_to);
	/*** get Patient insurance listing id ***/ 
	var unid = '';
	if($(this).data('unid')!=undefined)
		var unid = $(this).data('unid');
	
	/*** Get patient insurance details ***/
	if(page  == 'pat_ins'){
		var patient_id = $('#encode_patient_id').val();
		var getformid			= $(this).parents('form').attr('id');
		var getprimary_insurance_id = $('#'+getformid).find('[name=insurance_id]').val().split("::");
		var primary_insurance_type 	= $('#'+getformid).find('[name=insurancetype_id] :selected').text();
		var primary_insurance_policy_id = $('#'+getformid).find('.js-bootstrap-policyid').val();
		var category					= $('#'+getformid).find('[name=category]').val();
		var primary_insurance_id 		= getprimary_insurance_id[0];
		
		var par = 'patient_id='+patient_id+'&category='+category+'&primary_insurance_id='+primary_insurance_id+'&insurancetype='+primary_insurance_type+'&primary_insurance_policy_id='+primary_insurance_policy_id+'&type=pat_ins';
	}
	/*** End patient insurance details ***/
	

	var provider_id = $('#js-ptsh_provider').val();
	var scheduled_on = $('#scheduled_on').val();
	
	if(provider_id !=undefined){
		var par = par+'&provider_id='+provider_id;	
	}
	
	if(scheduled_on!=undefined) {
		var par = par+'&scheduled_on='+scheduled_on;	
	}
	
    if(patient_id == '' || patient_id == 'new'){
        var patient_last_name = $('#last_name').val();
        var patient_first_name = $('#first_name').val();
        var patient_dob = $('#dob').val();
        var primary_insurance_id = $('#primary_insurance_id').val();
        var primary_insurance_policy_id = $('#primary_insurance_policy_id').val();
        par = par+'&patient_last_name='+patient_last_name+'&patient_first_name='+patient_first_name+'&patient_dob='+patient_dob+'&primary_insurance_id='+primary_insurance_id+'&primary_insurance_policy_id='+primary_insurance_policy_id;
    }  
	
	if($.isNumeric(unique_id)){
		$('.js_gray'+unique_id).hide();
	}
		
	$('.js_insgray'+unid).hide();
	$('.eligibility_gray').hide();
	$('.eligibility_gray_temp').hide();
	
	// show loading image.
	if($.isNumeric(unique_id)){
		$('.patientloadingimg'+unique_id).show();	
	}
	
	$('.patientinsloadingimg'+unid).show();
	$('.eligibilityloadingimg').show();
	
    $.ajax({
        url: api_site_url+'/patients/checkEligibility',
        type : 'get', 
        data : par,
        success: function(result){
			var data = JSON.parse(result);
			
			// Hide loading image.
			if($.isNumeric(unique_id)){
				$('.patientloadingimg'+unique_id).hide();	
			}
			$('.patientinsloadingimg'+unid).hide();
			$('.eligibilityloadingimg').hide();
			
			if(patient_id == '' || patient_id == 'new'){
				$('.js-temp_id').val(data['tempid']);
				$('.js_get_temp_eligiblity_details').attr('data-tempid',data['tempid']);
			}	
			
            if(data['status'] == 'success'){
				if(data['error'] == '') {
					if($.isNumeric(unique_id)){
						$('.js_green'+unique_id).show();
					}
					$('.js_insgreen'+unid).show();
					$('.js_eliactive').show();	
					$('.js_eliactive_temp').show();
					// Pass status msg to icon
					$('.js_get_temp_eligiblity_details').attr('data-type','1');
					if(page == "eligibility" || page=='pat_ins') {
						 $('.eligibility-check').toggleClass('label-success label-danger');
						 $('.eligibility-check').text('Yes');
						js_alert_popup("Eligibility verfication done successfully");
					}
					
				}
				else {
					if($.isNumeric(unique_id)){
						$('.js_red'+unique_id).show();
					}
					$('.js_insred'+unid).show();
					$('.js_eliinactive').show();
					$('.js_eliinactive_temp').show();
					$('.js_get_temp_eligiblity_details').attr('data-type','2');
					if(page == "eligibility") {
						js_alert_popup(result['error']);
					}
					
				}
            }
			if(data['status'] == 'error'){
				$('.eligibility_gray').show();
				$('.eligibility_gray_temp').show();
				if(page == "eligibility") {
						js_alert_popup(data['error']);
					} else{
						//js_alert_popup(data['error']);
                                                js_sidebar_notification('error', data['error']);
					}
				
				if($.isNumeric(unique_id)){
					$('.js_gray'+unique_id).show();	
				}
				
				$('.js_insgray'+unid).show();
			 }
            return false;
        }
    }); 
});
/**** End to check insurance eligiblity ***/

/**** Start to call recheck insurance eligiblity function ***/
function recheck_eligibility(category,patient_id,page,insuranceid,policyid)
{
	var par = 'patient_id='+patient_id+'&category='+category;

	var provider_id = $('#js-ptsh_provider').val();
	var scheduled_on = $('#scheduled_on').val();
	
	if(provider_id !=undefined){
		var par = par+'&provider_id='+provider_id;	
	}
	
	if(scheduled_on!=undefined) {
		var par = par+'&scheduled_on='+scheduled_on;	
	}
	
	/*** Get patient insurance details ***/
	if(page  == 'pat_ins'){
		var primary_insurance_policy_id = policyid;
		var primary_insurance_id 		= insuranceid;
		
		var par = 'patient_id='+patient_id+'&primary_insurance_id='+primary_insurance_id+'&primary_insurance_policy_id='+primary_insurance_policy_id+'&type=pat_ins';
	}
	/*** End patient insurance details ***/
	
    if(patient_id == '' || patient_id == 'new'){
        var patient_last_name = $('#last_name').val();
        var patient_first_name = $('#first_name').val();
        var patient_dob = $('#dob').val();
        var primary_insurance_id = $('#primary_insurance_id').val();
        var primary_insurance_policy_id = $('#primary_insurance_policy_id').val();
        par = par+'&patient_last_name='+patient_last_name+'&patient_first_name='+patient_first_name+'&patient_dob='+patient_dob+'&primary_insurance_id='+primary_insurance_id+'&primary_insurance_policy_id='+primary_insurance_policy_id;
    }
	$.ajax({
        url: api_site_url+'/patients/checkEligibility',
        type : 'get', 
        data : par,
        success: function(result){
			var data = JSON.parse(result);
			
			if(patient_id == '' || patient_id == 'new'){
				$('.js-temp_id').val(data['tempid']);
				$('.js_get_temp_eligiblity_details').attr('data-tempid',data['tempid']);
			}	
			
            if(data['status'] == 'success'){
				
				if(page  == 'pat_ins'){
					var par_get = 'patient_id='+patient_id+'&insuranceid='+primary_insurance_id+'&policyid='+primary_insurance_policy_id+'&type='+page;
				}else if(category != '') {
					var par_get = 'patient_id='+patient_id+'&category='+category;	
				}
				else {
					var par_get = 'tempid='+data['tempid']+'&category='+category;	
				}
				
				 $.ajax({
					url: api_site_url+'/patients/getEligibility',
					type : 'get', 
					data : par_get,
					success: function(result){
						$('.coverloadingimg').css('display','none');
						$('#getEligibilityDetails').html(result);
						
						if(patient_id == ''){
							$('.js_get_patient').html($('#last_name').val()+', '+$('#first_name').val()+' '+$('#middle_name').val());
							$('.js_get_insurance').html($('#primary_insurance_id option:selected').text());
							$('.js_get_policy').html($('#primary_insurance_policy_id').val());
							if($("#js-ptsh_resource option:selected").val().length!='0') {
								$('.js_get_provider').html($("#js-ptsh_resource option:selected").text());
							}
						}	
						$.AdminLTE.boxWidget.activate();
					}
				});
            }
			if(data['status'] == 'error'){
				js_alert_popup(data['error']);
				$('.coverloadingimg').css('display','none');
			}
            return false;
        }
    }); 	
}
/**** End to call recheck insurance eligiblity function ***/

/**** Start to get the eligiblity details ***/
$(document).on('click', '.js_get_eligiblity_details,.js_get_temp_eligiblity_details',function (e) {
	var patient_id = $(this).data('patientid');
	var tempid 	   = $(this).data('tempid');
	var category = $(this).data('category');
	var eligibility = $(this).data('eligibility');
	var page 	= $(this).data('page');
	
	
	if(patient_id == '' || patient_id == null) {
		 var primary_insurance_id = $('#primary_insurance_id').val();
        var primary_insurance_policy_id = $('#primary_insurance_policy_id').val();
		
		var par = 'tempid='+tempid+'&insuranceid='+primary_insurance_id+'&policyid='+primary_insurance_policy_id;
	}
	
	if(tempid == '' || tempid == null && eligibility == '' || eligibility == undefined) {
		var par = 'patient_id='+patient_id+'&category='+category;
	}
	
	if(eligibility != undefined && eligibility != '' && patient_id != ''){
		var par = 'patient_id='+patient_id+'&eligibility='+eligibility;
	}
	
	if(page == 'pat_ins') {
		var getformid			= $(this).parents('form').attr('id');
		var primary_insurance_id = $('#'+getformid).find('[name=insurance_id]').val();
		var primary_insurance_policy_id = $('#'+getformid).find('.js-bootstrap-policyid').val();
		var patient_id = $('#encode_patient_id').val();
		
		var par = 'patient_id='+patient_id+'&insuranceid='+primary_insurance_id+'&policyid='+primary_insurance_policy_id+'&type='+page;
	}
	
	$('#getEligibilityDetails').html('');
	 $.ajax({
        url: api_site_url+'/patients/getEligibility',
        type : 'get', 
        data : par,
        success: function(edi_result){
			$('#getEligibilityDetails').html(edi_result);
			
			if(patient_id == '' || patient_id == null) {
				$('.js_get_patient').html($('#last_name').val()+', '+$('#first_name').val()+' '+$('#middle_name').val());
				$('.js_get_insurance').html($('#primary_insurance_id option:selected').text());
				$('.js_get_policy').html($('#primary_insurance_policy_id').val());
				if($("#js-ptsh_resource option:selected").val().length!='0') {
					$('.js_get_provider').html($("#js-ptsh_resource option:selected").text());
				}
			}
			$.AdminLTE.boxWidget.activate();
        }
    });
});
/**** End to get the eligiblity details ***/



/**** Start to get the eligiblity waystar details ***/
$(document).on('click', '.js_get_eligiblity_details_waystar',function (e) {
	var patient_id = $(this).data('patientid');
	var tempid 	   = $(this).data('tempid');
	var category = $(this).data('category');
	var insurance_id = $(this).data('unid');
	var eligibility = $(this).data('eligibility');
	var page 	= $(this).data('page');
	var par = 'patient_id='+patient_id+'&insuranceid='+insurance_id+'&category='+category;
	
	$('#getEligibilityDetails').html('');
	 $.ajax({
        url: api_site_url+'/patients/getEligibilityWaystar',
        type : 'get', 
        data : par,
        success: function(edi_result){
			$('#getEligibilityDetails').html(edi_result);
			
			if(patient_id == '' || patient_id == null) {
				$('.js_get_patient').html($('#last_name').val()+', '+$('#first_name').val()+' '+$('#middle_name').val());
				$('.js_get_insurance').html($('#primary_insurance_id option:selected').text());
				$('.js_get_policy').html($('#primary_insurance_policy_id').val());
				if($("#js-ptsh_resource option:selected").val().length!='0') {
					$('.js_get_provider').html($("#js-ptsh_resource option:selected").text());
				}
			}
			$.AdminLTE.boxWidget.activate();
        }
    });
});
/**** End to get the eligiblity waystar details ***/


/**** Start to get the eligiblity waystar details ***/
$(document).on('click', '.js_get_eligiblity_details_waystar_history',function (e) {
	var patient_id = $(this).data('patientid');
	var id 	   = $(this).data('id');
	
	
	var par = 'patient_id='+patient_id+'&id='+id;
	
	$('#getEligibilityDetails').html('');
	 $.ajax({
        url: api_site_url+'/patients/getEligibilityWaystarHistory',
        type : 'get', 
        data : par,
        success: function(edi_result){
			$('#getEligibilityDetails').html(edi_result);	
        }
    });
});
/**** End to get the eligiblity waystar details ***/



/**** Start to close eligiblity popup *** / 
$(document).on('click', '.close_eligiblity_popup',function (e) {
	$('#eligibility_content_popup').modal('hide');	
});	
/**** End to close eligiblity popup *** /


/**** Start to recheck insurance eligiblity details ***/
$(document).on('click','.js_recheck_eligibility',function() {
	$("#session_model .med-green").html('Would you like to recheck eligibility?');
	var patient_id = $(this).data('patientid');
	var category = $(this).data('category');
	var page  = $(this).data('page');
	var insuranceid = $(this).data('insuranceid');
	var policyid  = $(this).data('policyid');
	// issue fixed MR-2016 
	   $("#session_model").modal({ show: 'false', keyboard: false })
            .on('click', '.js_session_confirm', function (e) {
            		  var conformation = $(this).attr('id');
                if (conformation == "true") {
				$('.coverloadingimg').css('display','inline-block');				
				recheck_eligibility(category,patient_id,page,insuranceid,policyid);
            }
        });
});
/**** End to recheck insurance eligiblity details ***/

/*** Insurance Eligibility Verification store in DB End ***/
$(document).on('click','.js-add_edi_verification', function (e) {
	 e.preventDefault();    	
	$('#add_new_edi_verification').modal({ show: 'true'});
	$('#add_new_edi_verification').html($('.js-edi_verification').html());
	$('#js-bootstrap-validator').bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				'insurance_name': {
					validators: {
						notEmpty: {
							message: 'Select insurance_name'
						}
					}
				},
			 }
		})                  
		.on('success.form.bv', function(e) {
				e.preventDefault();
				$("#js_wait_popup").modal("show");
				addModal('add_new_edi_verification');
				var patient_id = $('.js_edi_patient').val();
				var get_data = $('#js-bootstrap-validator').serialize();
				
				$.ajax({
					type : 'POST',
					data: get_data,    
					url  : api_site_url+'/patients/'+patient_id+'/edi/eligibility/verification',
				   success :  function(result){
						window.location.href= "";
					}
				});
		});
});
/*** Insurance Eligibility Verification  End***/

$(document).on('change','.edi_insurance_id ', function (e) {		
		ins_value= $(this).val();
		new_data = ins_value.split('-');
		insid = new_data[0]; 
		category = new_data[1];
		$('input[name="category"]').val(category);
		$('input[name="insid"]').val(insid);
});


/* Facility Address Clear Code When pos selected home */
$(document).on('change','select[name="pos_id"]',function(){
	if($(this).val() == 12){
		$('input[id="address1"]').val('').attr('readonly', 'true');	
		$('input[id="address2"]').val('').attr('readonly', 'true');	
		$('input[id="city"]').val('').attr('readonly', 'true');	
		$('input[id="state"]').val('').attr('readonly', 'true');	
		$('input[id="pay_zip5"]').val('').attr('readonly', 'true');	
		$('input[id="pay_zip4"]').val('').attr('readonly', 'true');	
		$('select[name="county"]').select2().select2('val', '').attr('readonly', 'true');
	}else{
		$('input[id="address1"]').attr('readonly', false);	
		$('input[id="address2"]').attr('readonly', false);	
		$('input[id="city"]').attr('readonly', false);	
		$('input[id="state"]').attr('readonly', false);	
		$('input[id="pay_zip5"]').attr('readonly', false);	
		$('input[id="pay_zip4"]').attr('readonly', false);	
		$('select[name="county"]').select2().attr('readonly', 'false');
	}
});
/* Facility Address Clear Code When pos selected home */


