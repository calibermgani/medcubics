/*/////////////////////////////////////////////////////////////////////////////
 ----------- INDEX -------------
 1. On load of registration page
 2. Webcam
 3. Billing Cycle
 4. Date picker

 Author: 	Anitha
 Date: 		08 March 2016
 Updated:	Kannan
 
Common functions throughout patient demographics
 
/*/////////////////////////////////////////////////////////////////////////////

//1.    On load of registration page
//1.2.  Age calculation using date of birth field on page load
$(document).ready(function(){	
	var dob = $('#txtAge').val();
	dob = new Date(dob);
	var today = new Date();
	var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
	if(dob != 'Invalid Date')
		$('#age').val(age);
});

//1.3.  DOB Empty confirmation 
function dobempty_confirm(){
	$('#js_confirm_patient_demo_info_content1').html('Do you want to proceed without mentioning date of birth?');
	$("#js_confirm_patient_demo_info_box1")
		.modal({show: 'false', keyboard: false})
		.one('click', '.js_modal_confirm1', function (eve) {
			var conformation1 = $(this).attr('id');
			if(conformation1 == "true") {
				$('.patients-info-form').unbind('submit').submit();
			}
	});
}

//2.    webcam functions starts
$(document).on("ifToggled click", '.js-webcam-class', function () {
    if (!isFlashEnabled()) {
        js_alert_popup("Flash plugin was disabled");
        return false;
    }
    if ($('#error-cam').val() == 1) {
        $('#webcam_div').hide();
        $('#js-show-webcam').hide();
        js_alert_popup("JPEGCam Flash Error: No camera was detected");
        return false;
    } else {
        $('#webcam_div').show();
        $('#js-show-webcam').show();
    }
});

//3.    Billing Cycle: Check and Set Billing cycle depending upon last name
$(document).on('keyup', "input[name='last_name']",function () {
	if($(this).val()!=''){
		var first_letter   = $(this).val().substr(0, 1);
		first_letter = first_letter.toUpperCase();
		var bill_cycle_arr = ['A-G','H-M','N-S','T-Z'];
		for (var i = 0; i < bill_cycle_arr.length; i++) {
			var str_arr = bill_cycle_arr[i].split('-');
			if(first_letter>=str_arr[0] && first_letter<=str_arr[1]){
				$('input[name="bill_cycle"]').val(str_arr[0]+" - "+str_arr[1]);
			}
		}
	} else {
		$('input[name="bill_cycle"]').val('');
	}
});


//4.    Date picker
//4.1.  For Deceased and DOI (Date cannot be a future date) ***/
$(document).on('focus','#deceased_date,.doi,.auth_requested_picker', function(){ 
	var id = $(this).attr('id');
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$("#"+id).datepicker({
		yearRange:'1900:+0',
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
        maxDate: new Date( get_default_timezone ),
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
        onClose: function (selectedDate) {
        	if(id == 'txtAge' || id == 'deceased_date'){
				$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="dob"]'));
				$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="deceased_date"]'));
			}
		}
	});
});
	
//4.2.  For date of birth in registration page
$("#txtAge").datepicker({ 
	changeMonth: true,
	changeYear: true,
	maxDate: '0',
	dateFormat: 'mm/dd/yy',
	yearRange: '1901:+0',
	onSelect: function (date) {
		var dob = new Date(date);
		var validate_date = new Date(1901,00,01);
		getDOBAndCalculateAge(dob, validate_date);
	},
	onClose: function (selectedDate) {
		$('#txtAge').focus();
		$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="dob"]'));
		//$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="deceased_date"]'));
	}
});

//4.3.  For Insurance effective date or termination date
$(document).on('focus','.js_datepicker', function(){ 
	var id_name = $(this).attr('id');
	//var split_id_name = id_name.split('_');
	//var id = split_id_name[2];
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$("#"+id_name).datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+100',
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
        onClose: function (selectedDate) {
        	//if(id == 'txtAge' || id == 'deceased_date'){
				//$('.insurance-info-form').bootstrapValidator('revalidateField', $('input[name="effective_date[]"]'));
				//$('.insurance-info-form').bootstrapValidator('revalidateField', $('input[name="termination_date[]"]'));
			//}
		}
	});
});

//4.4.  For Authorization start date and end date
$(document).on('focus','.auth_datepicker', function(){ 
	var id_name = $(this).attr('id');
	var split_id_name = id_name.split('_');
	var id = split_id_name[2];
    var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$("#"+id_name).datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: '1901:+100',		
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
        onClose: function (selectedDate) {
			/*var form_id_val = $(this).parents("form").attr("id");
			$('#'+form_id_val).bootstrapValidator('revalidateField', 'start_date');
			$('#'+form_id_val).bootstrapValidator('revalidateField', 'end_date');*/
		},
		todayHighlight: false
	});

});

//4.5.  For Insured Date of birth
$(document).on('focus','.js-insurance_dob', function(){
	var id_name = $(this).attr('id');
	var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
	$("#"+id_name).datepicker({
		changeMonth: true,
		changeYear: true,
		maxDate: '0',
		dateFormat: 'mm/dd/yy',
		yearRange: '1901:+0',
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
		onClose: function (selectedDate) {
			//$('.insurance-info-form').bootstrapValidator('revalidateField', $('input[name="insured_dob[]"]'));
		}
	});
});

//4.6.  For Hold Release Date
$(document).on('focus','#hold_release_date', function(){ 
	var id_name = $(this).attr('id');
	var eventDates = {};	
	var selDate = new Date( get_default_timezone );		
	selDate.setDate(selDate.getDate()+1);	
    eventDates[ selDate] = selDate;
	$("#"+id_name).datepicker({
		dateFormat: 'mm/dd/yy',
		changeMonth: true,
		changeYear: true,
		//minDate: "0M+1",
		minDate: selDate, 
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
        onClose: function (selectedDate) {
        	if(id_name == 'hold_release_date'){
				//$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="hold_release_date"]'));
			}
		}
	});
});

$(document).on('keyup keydown keypress blur change', '#txtAge',function (e) {
	var txtAge_dob = $('#txtAge').val();
	if(isValidDatefct(txtAge_dob)){
		var validate_date = new Date(1901,00,01);
		var dob = new Date(txtAge_dob);
		getDOBAndCalculateAge(dob, validate_date);	
		$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="dob"]'));
		//$('.patients-info-form').bootstrapValidator('revalidateField', $('input[name="deceased_date"]'));	
	} else {
		$("#age").val('');
		$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_last_name');
		$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_first_name');
		$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_middle_name');
	}	
	if(e.type == "change" || e.type == "focusout")
		setTimeout(() => {checkpatientnamedobexist();}, 200)	
});

function isValidDatefct(date) {
    var temp = date.split('/');
    var d = new Date(temp[2] + '/' + temp[0] + '/' + temp[1]);
    return (d && (d.getMonth() + 1) == temp[0] && d.getDate() == Number(temp[1]) && d.getFullYear() == Number(temp[2]));
}

function getDOBAndCalculateAge(dob, validate_date){
	if(dob!='' && validate_date <= dob){
		
		/*var today = new Date();
		var diff = new Date(today - dob);
		var days = diff/1000/60/60/24;
		var year = days/365;
		var age= Math.floor(year);*/
		
		var age = calculateAge(parseDate1($('#txtAge').val()), new Date());
		
		//console.log(age);
		if(age >= 0){			
			$("#age").val(age);
			if(age <= 18){ 
				$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_relationship');
			}else{ 
				$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_relationship');
			}			
		}else {		
			$("#age").val('');
		}
	} else {
		$("#age").val('');
	}
	$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_last_name');
	$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_first_name');
	$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_middle_name');
}
/*** Ends - Datepicker for DOB and age calculation ***/

function parseDate1(dateStr) {
  var dateParts = dateStr.split("/");
  return new Date(dateParts[2], (dateParts[0] - 1), dateParts[1]);
}

function calculateAge (dateOfBirth, dateToCalculate) {
    var calculateYear = dateToCalculate.getFullYear();
    var calculateMonth = dateToCalculate.getMonth();
    var calculateDay = dateToCalculate.getDate();

    var birthYear = dateOfBirth.getFullYear();
    var birthMonth = dateOfBirth.getMonth();
    var birthDay = dateOfBirth.getDate();

    var age = calculateYear - birthYear;
    var ageMonth = calculateMonth - birthMonth;
    var ageDay = calculateDay - birthDay;

    if (ageMonth < 0 || (ageMonth == 0 && ageDay < 0)) {
        age = parseInt(age) - 1;
    }
    return age;
}

/*** Starts - Confirmation message while submiting ***/
function continue_next_tab(){
	var cnf_msg1 = confirm('Continue to next tab?');
	if(cnf_msg1 == false){
		$('#next_tab').val("no");
	} else {
		$('#next_tab').val("yes");
	}
}
/*** Starts - Confirmation message while submiting ***/

$(document).on('keyup keydown keypress blur change', '.js-insurance_dob',function (e) {
	var insurance_dob = $(this).val();
	if(insurance_dob.length == 10){		
		//$('.insurance-info-form').bootstrapValidator('revalidateField', $('input[name="insured_dob[]"]'));	
	}
});
/*** Starts - Demographic tab bootstrap validator ***/
$(document).ready(function () {	
	$(document).on('keyup','.patients-info-form [name="first_name"]', function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));		
	});
	$(document).on('keyup','.patients-info-form [name="last_name"]', function () { 		
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]')); 		
	});
	$(document).on('keyup','.patients-info-form [name="middle_name"]', function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="first_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="last_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="middle_name"]'));
	});
	$(document).on('keyup','[name="emer_last_name"], [name="emer_first_name"]', function () {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_first_name"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_last_name"]'));		
	});
	$(document).on('change','.emergency_relationship',function(){
		var emergency_relationship = $(this).val();
		if(emergency_relationship != ''){  
			$('input[name="emer_last_name"]').val('').attr('readonly', false);
			$('input[name="emer_first_name"]').val('').attr('readonly', false);
			$('input[name="emer_middle_name"]').val('').attr('readonly', false);
			$('input[name="emer_email"]').val('').attr('readonly', false);
			$('input[name="emer_cell_phone"]').val('').attr('readonly', false);

			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_first_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_last_name"]'));	 
		} else{
			
			$('input[name="emer_last_name"]').val('').attr('readonly', true);
			$('input[name="emer_first_name"]').val('').attr('readonly', true);
			$('input[name="emer_middle_name"]').val('').attr('readonly', true);	
			$('input[name="emer_email"]').val('').attr('readonly', true);	
			$('input[name="emer_cell_phone"]').val('').attr('readonly', true);
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_first_name"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="emer_last_name"]'));		
		}				
	});
	
/* preferred communication is select  */
	$(document).on('change','[name="preferred_communication"]', function () {
		var preferred_communication = $("#js_preferred_communication").val();
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="mobile"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="email"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('#js_preferred_communication'));
	});	 
}); 

/* preferred communication for mobile no Re validation */
$(document).on('keypress','[name="mobile"]', function () {
	var mobile_no = $("#mobile").val();
	if(mobile_no.length >= 12) {
		var preferred_communication = $("#js_preferred_communication").val();
		if(( preferred_communication == 'Text Message') || ( preferred_communication == 'Voice Calls')) {
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="mobile"]'));
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('#js_preferred_communication'));
		}	
	}
});

/* preferred communication for email no Re validation */
$(document).on('keypress','[name="email"]', function () {
	var mobile_no = $("#js_email_demo").val();
	var preferred_communication = $("#js_preferred_communication").val();
	if((preferred_communication == 'Regular Mail') || (preferred_communication == 'Email')) {
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="email"]'));
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('#js_preferred_communication'));
	}	
});

function formpersonalinfo() { 
	
	var guarantor_relationship = $('select[name=guarantor_relationship]').val();
	
	if(guarantor_relationship == '')
	{
		$('[name=guarantor_last_name]').attr('disabled','disabled');	
		$('[name=guarantor_first_name]').attr('disabled','disabled');	
		$('[name=guarantor_middle_name]').attr('disabled','disabled');
		$('[name=guarantor_last_name]').val('');
		$('[name=guarantor_first_name]').val('');
		$('[name=guarantor_middle_name]').val('');
	}

	var employment_status = $('select[name=employment_status]').val();
	var student_status = $('select[name=student_status]').val();
	if(employment_status == '' || employment_status == 'Retired' || employment_status == 'Unknown' || student_status == 'Full Time' || (student_status == 'Unknown' && employment_status == 'Student'))	{
		$('[name=work_phone]').prop('disabled',true);
		$('[name=work_phone_ext]').prop('disabled',true);	
		$('[name=work_phone]').val(''); $('[name=work_phone_ext]').val('')	 
	}
	
	$(document).on("blur", "input[name='phone']", function(){ 
		//$('form.patients-info-form').bootstrapValidator('revalidateField', $('input[name="phone_reminder"]'));
	});
	
	$(document).on("blur", "input[name='email']", function(){ 
		//$('form.patients-info-form').bootstrapValidator('revalidateField', $('input[name="email_notification"]'));
	})
    var form_validator = $('.patients-info-form').bootstrapValidator({
        message: 'This value is not valid',
		excluded: ':disabled',
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			last_name: {
				message: '',
				validators: {
					notEmpty: {
						message: lastname_lang_err_msg
					},
					callback: { 
						message: name_limit,
						callback: function (value, validator) {
							if ($("#last_name").val() != ''){
								var regExp = /^[A-Za-z- ]+$/;
								if (!regExp.test(value)){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								}
								else if(value.indexOf("''")!=-1){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								}
								else{
									var name_val = nameValidation();
									if(!name_val){
										return {
											valid: false, 
											message: name_limit
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
			first_name: {
				message: '',
				validators: {
					notEmpty: {
						message: firstname
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							if ($("#first_name").val() != ''){
								var regExp = /^[A-Za-z- ]+$/;
								if (!regExp.test(value)){
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								}
								else if(value.indexOf("''")!=-1){
									// console.log(value.indexOf("''"));
									return {
										valid: false, 
										message: alphaspace_lang_err_msg
									};
								}
								else{
									var name_val = nameValidation();
									if(!name_val){
										return {
											valid: false, 
											message: name_limit
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
			middle_name: {
				message: '',
				validators: {
					regexp: {
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					},
					callback: {
						message: name_limit,
						callback: function (value, validator) {
							var regExp = /^[A-Za-z]+$/;
							if ($("#last_name").val() != '' && regExp.test(value)) 
								return nameValidation();
							return true;
						}
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
            },
			address1: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: address1_lang_err_msg
					},
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
			address2: {
				message: '',
				trigger: 'change keyup',
				/* validators: {
					regexp: {
						regexp: /^[A-Za-z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}
				} */
			},
			city: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: city_lang_err_msg
					},
					regexp: {
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}
				}
			},
			state: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: state_lang_err_msg
					},
					regexp: {
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					},
					callback: {
						message:'',
						callback: function (value, validator) {
							/* var regExp = /^[A-Za-z]+$/;
							if (value !='' && regExp.test(value) ==false) {
								return {
									valid: false, 
									message: only_alpha_lang_err_msg
								};
							} else */ if(value !='' && $('input[name="state"]').val().length <2) {
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
			driver_license: {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^[A-Za-z0-9]+$/,
						message: alphanumeric_lang_err_msg
					}
				}
			},
			hold_release_date: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: 'Enter valid hold release date',
						callback: function (value, validator, $field) {
							if(($('[name="hold_release_date"]').prop('disabled')) == false){
								var m = validator.getFieldElements('hold_release_date').val();
								var n = value;
								var current_date=new Date(n); // console.log(m);
								if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {
									var d = new Date(get_default_timezone);	
								}else{
									var d = new Date();	
								}
								if(current_date != 'Invalid Date' && n != '' && m != '') {
									var getdate = daydiff(parseDate(m), parseDate(n));
									//return (getdate >= 0)? true : false; 
									if(getdate < 0){
										return {
											valid: false,
											message: 'Enter valid hold release date',
										};
									} else {
										if(d.getTime() > current_date.getTime()){
											return {
												valid: false,
												message: 'Release date is not before current date'
											};
										}
										return true;
									}
								} else {  
									return true;
								}
							}
						}
                    }
				}
            },
			zip5: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: zip5_lang_err_msg
					},
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					}
				}
			},
			zip4: {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			ssn:{
				message:'',
				trigger: 'change',
				validators:{
					regexp: {
						regexp: /^[0-9]{9}$/,
						message: ssn_lang_err_msg
					},
					remote: {
						message: 'This SSN already exists',
						url: api_site_url+'/ssn-validation',
						data:{'ssn':$('input[name="ssn"]').val(),'_token':$('input[name="_token"]').val(),'patient_id':$('input[name="patient_id"]').val()},
						type: 'POST'
					}
				}
			},
			phone:{
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
						callback: function (value, validator) { 
							var home_phone_msg = home_phone_limit_lang_err_msg;
							var response = phoneValidation(value,home_phone_msg);
							var value_phone = $('input[name=phone_reminder]:checked').val();  //alert(value_phone)							
							if(response !=true) {
								return {
									valid: false, 
									message: response
								};
							} else if(value_phone == "Yes" && value ==""){
								return {
									valid: false, 
									message: "Enter phone"
								};
							}							
							return true;
						}
					}
				}
			},
			phone_reminder: {                   
                message: '',
                trigger:"change ifToggled",
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {// alert("dfgdf");
                        	value = $("input[name='phone_reminder']:checked").val();
                            value_phone = $('input[name=phone]').val();                                 
                            /* if(value == "No" && typeof value_phone != "undefined" && value_phone != ""){
                                	return {
                                		valid:false,
                                		message:"Select phone remainder"
                                		
                                	};
                                } */
                            $('.patients-info-form').bootstrapValidator('revalidateField', 'phone');
                            return true;
                        }
                    }
                }
            },
			work_phone:{
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
						callback: function (value, validator) {
							var work_phone_msg = work_phone_limit_lang_err_msg;
							var ext_msg = work_phone_lang_err_msg;
							$fields = validator.getFieldElements('work_phone');
							var ext_length = $fields.closest("div").next().find("input").val().length;
							var response = phoneValidation(value,work_phone_msg,ext_length,ext_msg);
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
			mobile:{
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
						callback: function (value, validator) {
							/*if(value =='') {
								return {
									valid: false, 
									message: cell_phone_lang_err_msg
								};
							}
							else{*/
								//JIRA Task for MED-2827
								preferred_communication = $("#js_preferred_communication").val();

								if(((preferred_communication == 'Text Message') || (preferred_communication == 'Voice Calls' )) && (value == '') ) {
									return {
										valid: false, 
										message: 'Enter Cell phone no.'
									};
								}
								var cell_phone_msg = cell_phone_limit_lang_err_msg;
								var response = phoneValidation(value,cell_phone_msg);
								if(response !=true) {
									return {
										valid: false, 
										message: response
									};
								}
							//}
							return true;
						}
					}
				}
			},
			dob:{
				excluded: false,
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Enter Date Of Birth'
					},
					date:{
						format:'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function(value, validator, $field) {
							var dob = $('.patients-info-form').find('[name="dob"]').val();
							var current_date=new Date(dob);
							var d=new Date();	
							var selectYear = dob.split('/');
							//Before 1900 validation the year
							if((selectYear[2] <= '1900') && (dob.length ==10) &&((selectYear[2] >= '1000'))) {
								return {
									valid: false,
									message: date_format
								};
							}
							//return (dob.length != '' && d.getTime() < current_date.getTime())? false : true;
							if(new RegExp(/^\d{2}\/\d{2}\/\d{4}$/).test(value) && dob != '' && d.getTime() < current_date.getTime()){
								return {
									valid: false,
									message: valid_dob_format_err_msg
								};
							} else {
								return true;
							}
						}
					}
				}
			},
			email: {
				message: '',
				trigger: 'change',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							//JIRA Task for MED-2827
							preferred_communication = $("#js_preferred_communication").val();
							var email_notofication = $('input[name=email_notification]:checked').val(); 
							if((preferred_communication == 'Email'  || email_notofication == "Yes" )&& (value == '') ) {
								return {
									valid: false, 
									message: 'Enter Email-ID'
								};
							}
							var response = emailValidation(value);
							if(response !=true && (value.length > 1) ) {
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
			email_notification: {                   
                message: '',
               	trigger:"change ifToggled",
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator, $field) {// alert("dfgdf");
                        	value = $("input[name='email_notification']:checked").val();
                            value_email = $('input[name=email]').val();                                 
                            /* if(value == "No" && typeof value_email != "undefined" && value_email != ""){
                                	return {
                                		valid:false,
                                		message:"Select email yes"
                                		
                                	};
                                } */
                            $('.patients-info-form').bootstrapValidator('revalidateField', 'email');
                            return true;
                        }
                    }
                }
            },
			medical_chart_no: {
				message: '',
					validators: {
					regexp: {
						regexp: /^[a-zA-Z0-9]{0,30}$/,
						message: alphanumeric_lang_err_msg
					}
				}
			},
			emer_last_name: {
				message: '',
				//trigger: 'change keyup',
				validators: {
				
					callback: {
						message: lastname_lang_err_msg,
						callback: function(value, validator, $field) {
							var emer_first_name = $('.patients-info-form').find('[name="emer_first_name"]').val();
							var emergency_relationship = $('.patients-info-form').find('[name="emergency_relationship"]').val();						

							if(($('[name="emergency_relationship"]').prop('disabled')) == false) {
								if(emergency_relationship !='' && emer_first_name !='' && value == '') {
									return {
										valid: false, 
										message: lastname_lang_err_msg
									};									
								} else if(emergency_relationship !='' && value == ''){									
									return {
										valid: false, 
										message: lastname_lang_err_msg
									};
								} else {
									return true;
								}								
							}                         
						}
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}                              
				}
			},
			emer_first_name: {
				message: '',
				trigger: 'change keyup',
				validators: {
					callback: {
						message: firstname,
						callback: function(value, validator, $field) {
							var emer_last_name = $('.patients-info-form').find('[name="emer_last_name"]').val();
							var emergency_relationship = $('.patients-info-form').find('[name="emergency_relationship"]').val();
							if(($('[name="emergency_relationship"]').prop('disabled')) == false){
								if(emergency_relationship !='' && emer_last_name !='' && value == '') {
									return {
										valid: false, 
										message: firstname
									};									
								} else if(emergency_relationship !='' && value == '') {
									return {
										valid: false, 
										message: firstname
									};
								} else {
									return true;
								}								
							}                           
						}
					},
					regexp:{
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					}                              
				}
			},
			emer_cell_phone:{
				message:'',
				trigger: 'change keyup',
				validators:{
					callback: {
						message:'',
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
			emer_email: {
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
			guarantor_last_name: {
				trigger: 'change keyup',
				validators: {
				   callback: {
						message: guar_last_name,
						callback: function(value, validator, $field) {
							var dob = $('.patients-info-form').find('[name="dob"]').val();
							var age_value = $('.patients-info-form').find('[name="age"]').val();
							var guarantorfirstname = $('.patients-info-form').find('[name="guarantor_first_name"]').val();
							var guarantorrelationship = $('.patients-info-form').find('[name="guarantor_relationship"]').val();
							
							if(($('[name="guarantor_last_name"]').prop('disabled')) == false) {
								if(guarantorrelationship !='' && guarantorfirstname !='' && value == '') {
									return {
										valid: false, 
										message: guar_last_name
									};									
								} else if(guarantorrelationship !='' && value == ''){									
									return {
										valid: false, 
										message: guar_last_name
									};
								} else {
									return (((age_value >= 18)|| (age_value == ""))||(!dob)) ? true : (value !== '')                          	
								}
							}
						}
					},
					regexp:{
						regexp: /^[A-Za-z- ]+$/,
						message: alphaspace_lang_err_msg
					}                              
				}
			},
			guarantor_first_name: {
				trigger: 'change keyup',
				validators: {
					callback: {
						message: guar_fst_name,
						callback: function(value, validator, $field) {
							var dob = $('.patients-info-form').find('[name="dob"]').val();
							var age_value = $('.patients-info-form').find('[name="age"]').val();
							
							var guarantor_last_name = $('.patients-info-form').find('[name="guarantor_last_name"]').val();
							var guarantorrelationship = $('.patients-info-form').find('[name="guarantor_relationship"]').val();
														
							if(($('[name="guarantor_first_name"]').prop('disabled')) == false){
							
								if(guarantorrelationship !='' && guarantor_last_name !='' && value == ''){									
									return {
										valid: false, 
										message: guar_fst_name
									};
								} else if(guarantorrelationship !='' && value == ''){									
									return {
										valid: false, 
										message: guar_fst_name
									};
								} else {									
									return (((age_value >= 18)|| (age_value == ""))||(!dob)) ? true : (value !== '');                  
								}
							}
							//$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_relationship');
						}
					},
					regexp:{
						regexp: /^[A-Za-z- ]+$/,
						message: alphaspace_lang_err_msg
					}                              
				}
			},
			guarantor_middle_name: {
				trigger: 'change keyup',
				validators: {
					regexp:{
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					}                              
				}
			},
			dob:{
				excluded: false,
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'Enter Date Of Birth'
					},
					date:{
						format:'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: '',
						callback: function(value, validator, $field) {
							var dob = $('.patients-info-form').find('[name="dob"]').val();
							var current_date=new Date(dob);
							var d=new Date();	
							var selectYear = dob.split('/');
							//Before 1900 validation the year
							if((selectYear[2] <= '1900') && (dob.length ==10) &&((selectYear[2] >= '1000'))) {
								return {
									valid: false,
									message: date_format
								};
							}
							//return (dob.length != '' && d.getTime() < current_date.getTime())? false : true;
							if(new RegExp(/^\d{2}\/\d{2}\/\d{4}$/).test(value) && dob != '' && d.getTime() < current_date.getTime()){
								return {
									valid: false,
									message: valid_dob_format_err_msg
								};
							} else {
								return true;
							}
						}
					}
				}
			},
            deceased_date: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: valid_deceased_format,
						callback: function (value, validator, $field) {
                			var m = validator.getFieldElements('dob').val();
							var n = value;
							var current_date=new Date(n);
							var d = new Date();	
							if(current_date != 'Invalid Date' && n != '' && m != '') {
								var getdate = daydiff(parseDate(m), parseDate(n));
								//return (getdate >= 0)? true : false; 
								if(getdate < 0){
									return {
										valid: false,
										message: valid_deceased_format
									};
								} else {
									if(d.getTime() < current_date.getTime()){
										return {
											valid: false,
											message: 'Deceased date is not after current date'
										};
									}
									return true;
								}
							} else {  
								return true;
							}
						}
                    }
				}
            },
			organization_name: {
				trigger: 'change keyup keydown',
				validators: {
					callback: {
						callback: function (value, validator) {
							var employment_status_val1 = $('[name="employment_status"]').val();
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
							return true;
						}
					}
				}
			},
			occupation: {
				trigger: 'change keyup keydown',
				validators: {
					callback: {
						callback: function (value, validator) {
							var employment_status_val = $('#employment_status').val();
							if(employment_status_val=='Employed'||employment_status_val=='Self Employed'){
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
							return true;
						}
					}
				}
			},
			'employer_name': {
				validators: {
					callback: {
						callback: function (value, validator,$field) {
							
							var employment_status_val1 = $('[name="employment_status"]').val();
							if(employment_status_val1=='Employed'||employment_status_val1=='Self Employed'){
								if(value =="") {
									return {
										valid: true, 
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
							return true;
						}
					}
				}
			},
			student_status: {
				message: '',
				validators: {
					callback: {
						message:'',
						callback: function (value, validator, $field) {
							var employment_status_val = $('#employment_status').val();
							if(employment_status_val=='Student'){
								if(value =="") {
									return {
										valid: false, 
										message: 'Select student status'
									};
								} else if(value =="Full Time" || value =="Unknown"){
									$('[name=work_phone]').prop('disabled',true);
									$('[name=work_phone_ext]').prop('disabled',true);
								} else if(value =="Part Time"){
									$('[name=work_phone]').prop('disabled',false);
									$('[name=work_phone_ext]').prop('disabled',false);	
								}
							}
							return true;
						}
					}
				}
			},
			guarantor_relationship:{
				trigger: 'change',
				validators: {
					callback: {
						message:'',
						callback: function (value, validator, $field) {
							var guarantor_last_name = $('[name=guarantor_last_name]').val();
							var guarantor_first_name = $('[name=guarantor_first_name]').val();
							var guarantor_middle_name = $('[name=guarantor_middle_name]').val();
							
							if(value == '')	{	
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_last_name');
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_first_name');
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_middle_name');
								$('[name=guarantor_last_name]').attr('disabled','disabled');	
								$('[name=guarantor_first_name]').attr('disabled','disabled');	
								$('[name=guarantor_middle_name]').attr('disabled','disabled');	
								$('[name=guarantor_last_name]').val('');
								$('[name=guarantor_first_name]').val('');
								$('[name=guarantor_middle_name]').val('');							 
							} else { 
								if($('#gu_self_check').val() == 'Yes' && value == 'Self'){
									return {
										valid: false, 
										message: 'Self gurantor Already Exist'
									};	
								}
								try{
									$('[name=guarantor_last_name]').prop( "disabled", false );	
									$('[name=guarantor_first_name]').prop( "disabled", false );	
									$('[name=guarantor_middle_name]').prop( "disabled", false );
									$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_last_name');
									$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_first_name');
									$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_middle_name'); 
								} catch(err){
									console.log(err);
								}
							}
							
							if(($('[name="age"]').val() < 18 && $('[name="age"]').val() !='' && value == '')){
								return {
									valid: false, 
									message: 'Select guarantor relationship'
								};								
							}else if(($('[name="age"]').val() < 18 && value == 'Self')){
								return {
									valid: false, 
									message: 'Choose another relationship'
								};
							}else if(($('[name="age"]').val() >= 18)){								
								return true;								
							}							
							
							/* if(guarantor_last_name == '' && value != '') { 
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_last_name');	
							}
							if(guarantor_first_name == '' && value != '') {
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_first_name');
							}
							if(guarantor_middle_name == '' && value != '') {
								$('.patients-info-form').data('bootstrapValidator').revalidateField('guarantor_middle_name');
							} */
							
						/*	if(guarantor_last_name !='' || guarantor_first_name !=''){
								if(value =="") {
									return {
										valid: false, 
										message: 'Select guarantor relationship'
									};
								}
							}
							
							 */
							return true;
						}
					}
				}
			},
			preferred_communication: {
				message: '',
				validators: {
					callback: {
						message:'',
						callback: function (value, validator, $field) {
							preferred_communication = $("#js_preferred_communication").val();
							var mobile_no = $('#mobile').val();
							var js_email_demo = $('#js_email_demo').val();
							if((mobile_no == "") && (( preferred_communication == 'Text Message') || ( preferred_communication == 'Voice Calls'))) {
								return {
									valid: false, 
									message: 'Enter the Cell Phone no.'
								};
							}
							if( (js_email_demo == "") && (preferred_communication == 'Email') ) {
								return {
									valid: false, 
									message: 'Enter the Email-ID'
								};
							}
							return true;
						}
					}
				}
			},
			filefield: {
				validators: {
					file: {
						extension: 'png,jpg,jpeg',
						type: 'image/png,image/jpg,image/jpeg',
						maxSize: 1024*1024, // 5 MB
						message: 'The selected file is not valid, it should be (png, jpg) and 1 MB at maximum.'
					}
				}
			},
			other_address1: {
				message: '',
				trigger: 'change keyup',
				validators: {
					/*notEmpty: {
						message: address1_lang_err_msg
					},*/
					/* regexp: {
						regexp: /^[a-zA-Z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}, */
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							if($('input[name="send_statement_to"]:checked').val() == 'Other Address'){
								if(value != ''){
								var address1_value = value.trim();
								var add_length = address1_value.length;
								return (add_length !='' && add_length>28) ? false : true;
								}else{
									return {
									valid: false,
									message: "Address Line is mandatory"
								};
							}
						}else{
							return true;
						}
						
						}
					}
				}
			},
			other_address2: {
				message: '',
				trigger: 'change keyup',
				/* validators: {
					regexp: {
						regexp: /^[A-Za-z0-9 ]+$/,
						message: alphanumericspace_lang_err_msg
					}
				} */
			},
			other_city: {
				message: '',
				trigger: 'change keyup',
				validators: {
				/*	notEmpty: {
						message: city_lang_err_msg
					},*/
					regexp: {
						regexp: /^[A-Za-z ]+$/,
						message: alphaspace_lang_err_msg
					},
					callback: {
						message: add1_limit,
						callback: function (value, validator) {

							if($('input[name="send_statement_to"]:checked').val() == 'Other Address'){
								if(value != ''){
								var address1_value = value.trim();
								var add_length = address1_value.length;
								return (add_length !='' && add_length>28) ? false : true;
								}else{
									return {
									valid: false,
									message: "Enter City"
								};
							}
						}else{
							return true;
						}
						
						}
					}
				}
			},
			other_state: {
				message: '',
				trigger: 'change keyup',
				validators: {
					/*notEmpty: {
						message: state_lang_err_msg
					},*/
					regexp: {
						regexp: /^[A-Za-z]+$/,
						message: only_alpha_lang_err_msg
					},
					callback: {
						message:'',
						callback: function (value, validator) {
							/* var regExp = /^[A-Za-z]+$/;
							if (value !='' && regExp.test(value) ==false) {
								return {
									valid: false, 
									message: only_alpha_lang_err_msg
								};
							} else */
							if($('input[name="send_statement_to"]:checked').val() == 'Other Address'){ 
								if(value !='' && $('input[name="state"]').val().length <2) {
									return {
										valid: false, 
										message: state_limit_lang_err_msg
									};
								}
								if(value == '' && $('input[name="state"]').val().length ==0){
									return {
										valid: false, 
										message: state_lang_err_msg
									};
								}
							}else{
								return true;
							}
							return true;
						}
					}
				}
			},
			other_zip5: {
				message: '',
				trigger: 'change keyup',
				validators: {
					/*notEmpty: {
						message: zip5_lang_err_msg
					},*/
					regexp: {
						regexp: /^\d{5}$/,
						message: zip5_limit_lang_err_msg
					},
					callback: {
						message: add1_limit,
						callback: function (value, validator) {
							if($('input[name="send_statement_to"]:checked').val() == 'Other Address'){
								if(value != ''){
								var zip5_value = value.trim();
								var add_length = zip5_value.length;
								return (add_length !='' && add_length>5) ? false : true;
								}else{
									return {
									valid: false,
									message: "Enter Zip Code"
								};
							}
						}else{
							return true;
						}
						
						}
					}
				}
			},
			other_zip4: {
				message: '',
				trigger: 'change keyup',
				validators: {
					regexp: {
						regexp: /^\d{4}$/,
						message: zip4_limit_lang_err_msg
					}
				}
			},
			js_err_webcam: {
				message: '',
				selector: '.medcubicsform .js_err_webcam',
				validators: {
					callback: {
						message: attachment_lang_err_msg,
						callback: function (value, validator) {
							/*var get_checked_val = $('input[name="upload_type"]:checked').val();
							var err_msg = $('#error-cam').val();
							if ((err_msg == '' || err_msg == null || err_msg == 1) && get_checked_val == "webcam") {
								if (value == '' || value == null)
									return false;
								else
									return true;
							}*/
							return true;
						}
					}
				}
			}
		}
	});
}

formpersonalinfo();

function checkpatientnamedobexist(){
	var firstname = $("input[name='first_name']").val();
	var lastname = $("input[name='last_name']").val();
	var encode_patient_id = $("input[name='encode_patient_id']").val();		
	var dob_val			= $('#txtAge').val();
	$.when($.ajax({
			url: api_site_url+'/patient-check',
			type : 'post', 
			data : {'_token':$('input[name="_token"]').val(),'dob':dob_val,'first_name':firstname,'last_name':lastname,'encode_patient_id':encode_patient_id},
			dataType: 'json',		
		})
	).then((status)=>{if(status.patient_status)  js_sidebar_notification('error',status.msg); }, 
	     (error)=>{//console.log(error); 
	     });	
}
// Patient statment category based values update in registration page
$(document).on('change','.js_stmt_cat',function(){		
	var selCat = $(this).val();
	var pars = '';
	$.ajax({
		url: api_site_url + '/GetPatSmtCatDetails/'+selCat,
		dataType: 'json',
		type: 'get',
		data: pars,
		success: function (data, textStatus, jQxhr) {	
			$(".js-stmts").select2('val',data.stmt_option);
			if(data.stmt_option == "Hold") {
				$('#hold_reason').select2("val",data.hold_reason).prop("disabled", false); 
				$('#hold_release_date').val(data.hold_release_date).prop("disabled", false); 
			} else {
				$('#hold_reason').select2("val",data.hold_reason).prop("disabled", true); 
				$('#hold_release_date').val(data.hold_release_date).prop("disabled", true); 
			}
		}
	});

});

/*$('input.js-v2-demography-submit[type="submit"]').on('click', function(event) {
	$('.patients-info-form').bootstrapValidator('validate');
	$('.patients-info-form').on('success.form.bv', function(e) {
		$('.patients-info-form').bootstrapValidator('disableSubmitButtons', false);
        e.preventDefault();
		var firstname = $("input[name='first_name']").val();
		var lastname = $("input[name='last_name']").val();
		var encode_patient_id = $("input[name='encode_patient_id']").val();
		var marital_status 	= $('select[name=marital_status]').val();
		var guarantor_relationship = $('select[name=guarantor_relationship]').val();
		var dob_val			= $('#txtAge').val();
		$.ajax({
			url: api_site_url+'/patient-check',
			type : 'post', 
			data : {'_token':$('input[name="_token"]').val(),'dob':dob_val,'first_name':firstname,'last_name':lastname,'encode_patient_id':encode_patient_id},
			dataType: 'json',
			success: function(result){ alert(result);
				if(result.patient_status == 'true'){
					$("#js_confirm_patient_demo_info_content1").html(result.msg);
					$(".js_common_modal_popup_save").html('Ignore');
					$(".js_common_modal_popup_cancel").html('Cancel');
					$("#js_confirm_patient_demo_info_content1").html(result.msg);
					$("#js_confirm_patient_demo_info_box1")
					.modal({show: 'false', keyboard: false})
					.one('click', '.js_modal_confirm1', function (eve) {
							var conformation1 = $(this).attr('id');
							if(conformation1 == "true") {
							
								if(dob_val=='' || dob_val<18){
									if(parseInt(dob_val)<parseInt('18')){
										$('#js_confirm_patient_demo_info_content').html('Patient age is less than 18 years, It is mandatory to have a Guarantor');
										$("#js_confirm_patient_demo_info_box")
											.modal({show: 'false', keyboard: false})
											.one('click', '.js_modal_confirm', function (ent) {
												var conformation = $(this).attr('id');
												if(conformation == "true") {
													
												}else{
													$('.patients-info-form').unbind('submit').submit();
												}
											});
										}				
									else {
										dobempty_confirm();
									}
								}
								else {
									$('.patients-info-form').unbind('submit').submit();
								}
							}else if(conformation1 == "false"){
								window.location.href = api_site_url+'/patients';
							}
					});
				}else if(result.patient_status == 'false'){
					if(dob_val=='' || dob_val<18){
						if(parseInt(dob_val)<parseInt('18')){
							$('#js_confirm_patient_demo_info_content').html('Patient age is less than 18 years, It is mandatory to have a Guarantor');
							$("#js_confirm_patient_demo_info_box")
								.modal({show: 'false', keyboard: false})
								.one('click', '.js_modal_confirm', function (ent) {
									var conformation = $(this).attr('id');
									if(conformation == "true") {
										
									}else{
										$('.patients-info-form').unbind('submit').submit();
									}
								});
							}								
						
						else {
							dobempty_confirm();
						}
					}
					else {
						$('.patients-info-form').unbind('submit').submit();
					}
				}			
			}
		});
		return false;
    });
	$(this).off('click');
});*/

$('input.js-v2-demography-submit[type="submit"]').on('click', function(event) {
	if($('.patients-info-form').length)
	$('.patients-info-form').bootstrapValidator('validate');
	$('.patients-info-form').on('success.form.bv', function(e) {
		$('.patients-info-form').bootstrapValidator('disableSubmitButtons', false);
        e.preventDefault();
		var marital_status 	= $('select[name=marital_status]').val();
		var guarantor_relationship = $('select[name=guarantor_relationship]').val();
		var dob_val			= $('#txtAge').val();
		if(dob_val=='' || dob_val<18){
			if(parseInt(dob_val)<parseInt('18')){
                $('#js_confirm_patient_demo_info_content').html('Patient age is less than 18 years, It is mandatory to have a Guarantor');
				$("#js_confirm_patient_demo_info_box")
					.modal({show: 'false', keyboard: false})
					.one('click', '.js_modal_confirm', function (ent) {
						var conformation = $(this).attr('id');
						if(conformation == "true") {
							
						} else {							
							$('.patients-info-form').unbind('submit').submit();
						}
					});
			} else {
				dobempty_confirm();
			}
		} else {
			$('.patients-info-form').unbind('submit').submit();
		}
		return false;
    });
	$(this).off('click');
});

function nameValidation() {
	var last_name = $("#last_name").val();
	var first_name = $("#first_name").val();
	var middle_name = $("#middle_name").val();
	var ln_val = last_name.trim();
	var fn_val = first_name.trim();
	var mn_val = middle_name.trim();
	var add_length = ln_val.length + fn_val.length + mn_val.length;
	checkpatientnamedobexist(); // Name, DOB already exist function call 
	return (add_length>28) ? false : true;
}
/*** Ends - Demographic tab bootstrap validator ***/

/*** Starts - Move to next tab  ***/
$(document).on('click', '.js_arrow',function (e) {
	var current_tab = $(this).attr('id');
	var encode_patient_id = $('#encode_patient_id').val();
	moveNextTab(encode_patient_id, current_tab);
	// hideLoadingImage();
});
/*** Ends - Move to next tab  ***/

/*** Starts -  Delete options in patient tab  ***/
$(document).on('click', '.js-patient-delete',function (e) {
	var current_div_data = $(this).attr('id');
	split_current_div_data = current_div_data.split('_');
	current_delete_type = split_current_div_data[1];
	current_div_id = split_current_div_data[2];
	current_delete_typeid = split_current_div_data[3];
	
	$('#current_delete_type').val(current_delete_type);
	$('#current_div_id').val(current_div_id);
	$('#current_delete_typeid').val(current_delete_typeid);

	$('#delete-form-modal .modal-body').html($(this).attr('data-text'));

	$("#delete-form-modal").modal("show");	
});
/** Patient Address check **/
$(document).on( 'change', '.send_statement_to', function () { 
	var stmtVal = $(this).val();
	var form_id_val = $(this).parents("form").attr("id");
	if(stmtVal == 'Other Address') {		
		$('.patient-other-address').removeClass("hide").addClass('show');		
	} else {
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_address1',true);
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_address2',true);
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_city',true);
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_state',true);		
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_zip5',true);		
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'other_zip4',true);		
		$('.patient-other-address').addClass("hide").removeClass('show');				
	}	
});

/** Start - Patient statement based show / hide corresponding block **/
$(document).on( 'change', '.js-stmts', function () { 
	var stmtVal = $(this).val();
	if(stmtVal == 'Hold') {
		$('.js_hold_blk').prop("disabled", false);
	} else {
		$('#hold_reason').select2("val","").prop("disabled", true);
		$('#hold_release_date').val("").prop("disabled", true);
	}
	$(".js_stmt_cat").select2("val", "");
});
/** End - Patient statement based show / hide corresponding block **/

function moveNextTab(encode_patient_id, current_tab){
	displayLoadingImage();
	if(current_tab == 'insurance'){
		var exist_selins_ids = $('input[name="pat_inslist_name[]"]:checkbox:checked').map(function(){
			return $(this).val();
		}).get();
	}
	
	$('#insurance-info').html('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing');
	$('#contact-info').html('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing');
	$('#authorization-info').html('<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing');
	
	if(current_tab != 'demo') {
		$.ajax({
		   url: api_site_url+'/patients/'+encode_patient_id+'/edit/'+current_tab,
		   type : 'get', 
		   success: function(msg){
			   	$('.js-tab-heading').removeClass('active');
				$('.tab-pane').removeClass('active');
				$("#"+current_tab+"-info").html(msg);	
				
				if(current_tab == 'insurance'){
					$.each(exist_selins_ids, function(ins_key,ins_value){    
						$("input[name='pat_inslist_name[]'][type=checkbox][value="+ins_value+"]").prop("checked",true);
						$('#v2-insuranceeditform_'+ins_value).removeClass('hide').addClass('show');						
					});
				}
				$.AdminLTE.boxWidget.activate();
				$("#"+current_tab+"-info").addClass('active');
				$('#js-tab-heading-'+current_tab).addClass('active');
				if(current_tab == 'contact') {
				  //  LoadContactsTab();
				}
					
				if(current_tab == 'insurance')
					$( ".js-add-new-select-opt" ).append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
				$("select.select2.form-control").select2();
			   // $('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});	
				//$('input[type="checkbox"].flat-red').iCheck({ checkboxClass: 'icheckbox_flat-green' });

		  		hideLoadingImage();
				return false;
		   	}
		});
	} else {
		$('.js-tab-heading').removeClass('active');
		$('#js-tab-heading-demo').addClass('active');
		
		$('.tab-pane').removeClass('active');
		$("#demo-info").addClass('active');
	}
}

/*** Starts - Is self pay or not check an show insurance details ***/
 $(document).on('ifToggled change', '.js-is_self_pay',function (e) {
 	is_self_pay = $(this).val();
	type = $(this).attr('id');	
	//console.log(type);
	if($('#patient_ins_count').val() != 0) {
		if(type == 'r-selfpay'){
			msg = "Responsibility changed to Self-pay. Do you want to proceed?";
		//    msg = "All active insurance will be moved to archive. <br /> do you still want to proceed?";
		}else{
			msg = "You will be asked to add an insurance. <br />do you still want to proceed?";
		}
	} else {
	   if(type == 'r-selfpay'){    
			$('.js-v2-insurance-responsible-btn').click(); 
			return false;      			
			//    msg = "Changing responsibility to self pay will move all your active insurance to Archive, do you want to proceed?";
		}else{
			msg = "Responsibility changed to Insurance. Do you want to proceed?";
		}
	}
 	//$(".js-v2-insurance-responsible").removeClass('hide').addClass('show');
	$('#js_confirm_patient_demo_info_content1').html(msg);
	$("#js_confirm_patient_demo_info_box1")
		.modal({show: 'false', keyboard: false})
		.one('click', '.js_modal_confirm1', function (eve) {
			var conformation1 = $(this).attr('id');
		//	console.log(conformation1);
			if(conformation1 == "true") {
				$('.js-v2-insurance-responsible-btn').click();
				return false;
			}else{
				if($('#r-selfpay').is(':checked') == true){
					$("#r-selfpay").prop('checked', false);
					$("#r-insurance").prop('checked', true);
					//$("#r-insurance").closest("div").addClass("checked");
					return false;
				}else if($('#r-insurance').is(':checked') == true){
					$("#r-insurance").prop('checked', false);
					$("#r-selfpay").prop('checked', true);
					//$("#r-selfpay").closest("div").addClass("checked");
					return false;
				}
			}
	});
	//$('#js_confirm_patient_demo_info_content1').html('');		
});
/*** Ends - Is self pay or not check an show insurance details ***/

/*** Starts - Add more insurance ***/
$(document).on('click','.js-addmore_insurance', function () {
	$('.eligibility_gray').hide();
	$('.js_eliactive').hide();
	$('.js_eliinactive').hide();
	$('.eligibilityloadingimg').hide();
	is_self_pay_val = $('#is_self_pay_ori_val').val();//$('.js-is_self_pay:checked').val();
	if(is_self_pay_val!='Yes') {
		$('#add_new_insurance').modal({ show: 'true'});
		$('#add_new_insurance').html($('.js_add_new_ins_form').html());
		insuranceForm('js-bootstrap-validator-insurance');
		$('#add_new_insurance select').removeClass("select_2").addClass("select2");
		$('#add_new_insurance input').removeClass("flat_red").addClass("flat-red");
		$('#add_new_insurance select').select2();
		/*$('#add_new_insurance input[type="checkbox"].flat-red, #add_new_insurance input[type="radio"].flat-red').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});*/
        // Hided by Akash
		$('#s2id_js-category').select2('focus');
	} else {
		js_alert_popup('You are in self pay');
	}
});	
/*** Ends - Add more insurance ***/

/*** Starts - Add more contact ***/
$(document).on('click','.js-addmore_contact', function () {
	contact_count = $('#contact_count').val();
	contact_count = parseInt(contact_count)+1;
	$('#contact_count').val(contact_count);	

	patient_id = $('#encode_patient_id').val();
	$.ajax({
		url: api_site_url+'/patients/addmore/contact/'+contact_count+'/'+patient_id,
		type : 'GET', 
		success: function(msg){ 
			$('#contact_box').first().prepend(msg);
			$.AdminLTE.boxWidget.activate();
			$("select.select2.form-control").select2();
			$('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});
		    $('.contact-info-form').bootstrapValidator('addField', 'contact_category[]');
		}
	});
});	
/*** Ends - Add more contact ***/

//Insurance Tab
//  Responsibility change

$(document).on( 'change', '.js-relationship', function () {

	var rel = $(this).val();
	var id_name 	= $(this).attr('id');
	var curr_id_val = id_name.split('-');
	var id_val 		= curr_id_val[1];
	var pre_rel_val = $("#prev_relationship-"+id_val).val();
	$("#prev_relationship-"+id_val).val(rel);
	var form_id_val = $(this).parents("form").attr("id");
	if(rel == 'Self') {
		$('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_last_name',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_first_name',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_ssn',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_dob',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_address1',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_address2',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_city',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_state',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_zip5',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'insured_zip4',true);
        $('#'+form_id_val).bootstrapValidator('enableFieldValidators', 'gender',true);
		$("#insuredrelation_part-"+id_val).removeClass('show').addClass('hide');
	} else {
		$("#insuredrelation_part-"+id_val).removeClass('hide').addClass('show');
	}
		
	insuranceForm(form_id_val);
	
	if(rel == 'Self') {
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
		$('#gender-'+id_val+' input[name="gender"]:radio[value="'+self_gender+'"]').prop("checked", true);
		$("#insured_last_name-"+id_val).val(self_last_name); 
		$("#insured_first_name-"+id_val).val(self_first_name); 
		$("#insured_middle_name-"+id_val).val(self_middle_name); 
		$("#insured_ssn-"+id_val).val(self_ssn); 
		$("#insured_dob-"+id_val).val(self_dob);
		$("#insured_address1-"+id_val).val(self_address1);
		$("#insured_address2-"+id_val).val(self_address2);
		$("#insured_city-"+id_val).val(self_city);
		$("#insured_state-"+id_val).val(self_state);
		$("#insured_zip5-"+id_val).val(self_zip5);
		$("#insured_zip4-"+id_val).val(self_zip4);
		
		$('#'+form_id_val+" .js-same_as_patient_address-v2").prop("checked", true);
	//	$('#'+form_id_val+" .js-same_as_patient_address-v2").iCheck('update');
		
		$("#insured_last_name-"+id_val).attr('readonly', true);
		$("#insured_first_name-"+id_val).attr('readonly', true);
		$("#insured_middle_name-"+id_val).attr('readonly', true);
		$("#insured_ssn-"+id_val).attr('readonly', true);
		$("#insured_dob-"+id_val).attr('readonly', true);
		$('#gender-'+id_val+' input[name="gender"]:radio').attr("disabled", true);
		$('#gender-'+id_val+' input[name="gender"]:radio[value="'+self_gender+'"]').attr("disabled", false);
	} else if(pre_rel_val=='Self'||pre_rel_val==''){
		$("#insured_last_name-"+id_val).val(''); 
		$("#insured_first_name-"+id_val).val(''); 
		$("#insured_middle_name-"+id_val).val(''); 
		$("#insured_ssn-"+id_val).val(''); 
		$("#insured_dob-"+id_val).val('');
		$("#insured_address1-"+id_val).val('');
		$("#insured_address2-"+id_val).val('');
		$("#insured_city-"+id_val).val('');
		$("#insured_state-"+id_val).val('');
		$("#insured_zip5-"+id_val).val('');
		$("#insured_zip4-"+id_val).val('');
		$('#gender-'+id_val+' input[name="gender"]:radio[value="Male"]').prop("checked", false);
		$('#gender-'+id_val+' input[name="gender"]:radio[value="Female"]').prop("checked", false);
		$('#gender-'+id_val+' input[name="gender"]:radio[value="Others"]').prop("checked", false);
		$('#'+form_id_val+" .js-same_as_patient_address-v2").prop("checked", false);
		$('#'+form_id_val+" .js-same_as_patient_address-v2").iCheck('update');
		
		$("#insured_last_name-"+id_val).attr('readonly', false);
		$("#insured_first_name-"+id_val).attr('readonly', false);
		$("#insured_middle_name-"+id_val).attr('readonly', false);
		$("#insured_ssn-"+id_val).attr('readonly', false);
		$("#insured_dob-"+id_val).attr('readonly', false);
		$('#gender-'+id_val+' input[name="gender"]').attr("disabled", false);
		
	}else{
		$('#'+form_id_val+" .js-same_as_patient_address-v2").prop("checked", false);
	}
	if($('#'+form_id_val+" .form-group.has-feedback").length){ 
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_last_name');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_first_name');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_ssn');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_dob');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_address1');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_address2');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_city');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_state');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_zip5');
		$('#'+form_id_val).data('bootstrapValidator').revalidateField('insured_zip4');
	}
	// For handle same as patient check box issue.
	if($('#'+form_id_val+" .js-same_as_patient_address-v2:visible").is(':checked')) { 
		$('#'+form_id_val).find('.same_address').addClass('hide');
	} else { 
		$('#'+form_id_val).find('.same_address').removeClass('hide');
	}	
	$('#gender-'+id_val+' input[name="gender"]:radio').iCheck('update');
	if($("#insured_address1-"+id_val).is(':visible') == true){
		var address = $("#insured_address1-"+id_val).val();
		var city = $("#insured_city-"+id_val).val();
		var zip = $("#insured_zip5-"+id_val).val();
		if(address =='' && city =='' && zip ==''){
			setTimeout(function() {
				$('span.js-address-error :visible').addClass('hide');
				$('span.js-address-success :visible').addClass('hide');
			}, 10);
		}
	}
});
/*** Ends - Insurance tab - Relationship change ***/

$(document).on('keyup', '.js-visit', function () {
	var id_name = $(this).attr('id');
	var split_id_name = id_name.split('-');
	var id = split_id_name[1];

	var allowed_visit = $('#allowed_visit-'+id).val();
	var visit_used = $('#visit_used-'+id).val();
	$('#visit_remaining-'+id).val('');	
	if(allowed_visit != '' &&  visit_used != ''){
		var allowed_visit = parseInt(allowed_visit);
		var visit_used = parseInt(visit_used);
		if(allowed_visit >= visit_used){
			var visit_remaining = allowed_visit-visit_used;
			$('#visit_remaining-'+id).val(visit_remaining);
		}
	} else if(allowed_visit != '' && allowed_visit >= 0) {
		$('#visit_remaining-'+id).val(allowed_visit);
	}
		
	/*$('.authorization-info-form').bootstrapValidator('revalidateField', $('input[name="visits_used[]"]'));
	$('.authorization-info-form').bootstrapValidator('revalidateField', $('input[name="allowed_visit[]"]'));
	$('.authorization-info-form').bootstrapValidator('revalidateField', $('input[name="alert_visit_remains[]"]'));*/
});

$(document).on('change', '.js-sel-insurance-address',function (e) {
	var sel_insurance_id = this.value;
	var curr_part_id 	 = $(this).closest('div.form-group').find('p.js-address-part-dis').attr('id');
	if(sel_insurance_id!=''){
		$.ajax({
			type : 'GET',
			url  : api_site_url+'/sel_patientinsurance_address/'+sel_insurance_id,
			success :  function(result)	{
				$("#"+curr_part_id).html(result);
				$("#"+curr_part_id).removeClass('hide').addClass('show');
			}
		});
	} else {
		$("#"+curr_part_id).html('');
		$("#"+curr_part_id).removeClass('show').addClass('hide');
	}
});

/* Starts - USPS Address display if the address is correct */
$(document).on("click",".js-address-success",function(){ 
	current_address_class = $(this).parents("div .js-address-class").attr("id");
	parent_address_id = $(this).find('a').attr('href');
	$(parent_address_id+' #modal_show_success_message').show();
	$(parent_address_id+' #modal_show_error_message').hide();
	$('.modal_show_error_message').removeClass('hide'); 
	$(parent_address_id+' .modal_address').html($('#'+current_address_class+' .js-address-address1').val());
	$(parent_address_id+' .modal_city').html($('#'+current_address_class+' .js-address-city').val());
	$(parent_address_id+' .modal_state').html($('#'+current_address_class+' .js-address-state').val());
	$(parent_address_id+' .modal_zip5').html($('#'+current_address_class+' .js-address-zip5').val()+'-'+$('#'+current_address_class+' .js-address-zip4').val());
});
/* Ends - USPS Address display if the address is correct */
	
/*** Starts USPS ADRESS CHECK  if the address is not correct IN PATIENTS CONTACT PAGE ///

$(document).on("click",".js-address-error",function(){
	current_address_class = $(this).parents("div .js-address-class").attr("id");
	parent_address_id = $(this).find('a').attr('href');
	$(parent_address_id+' #modal_show_error_message').html($('#'+current_address_class+' .js-address-error-message').val());
	$(parent_address_id+' #modal_show_error_message').show();
	$(parent_address_id+' #modal_show_success_message').hide();
});
/* Ends - USPS Address error message, if the address is not correct */

/*function activateafterajaxcall()
{
	var count_dropdown = $("input[name='contact_category[]']").val();
	(count_dropdown != null) ? $("#addmore_contact").attr('disabled',false):$("#addmore_contact").attr('disabled','disabled'); 
}*/	 

/*############## Starts - correspondence page ################# */
/* Dropdown based value changes start */
$(document).on( 'ifToggled change', '.js_change_type', function () {
	var get_identify = $(this).attr("data-access");
	var get_value = $(this).val();
	if(get_identify =="provider") {
		if($('[name="ein"]').length>0) {
			$('.js_show_provider_ein').val($('select[name="ein_number"] option[value="'+get_value+'"]').text());
			$('#bootstrap-validator-correspondence').bootstrapValidator('revalidateField', $('[name="ein"]'));
		}
		if($('[name="npi"]').length>0) {
			$('.js_show_provider_npi').val($('select[name="npi_number"] option[value="'+get_value+'"]').text());
			$('#bootstrap-validator-correspondence').bootstrapValidator('revalidateField', $('[name="npi"]'));
		}
	} else if(get_identify =="insurance") {
		var ins_addr = $('select[name="insurance_add"] option[value="'+get_value+'"]').text();
		if($('[name="insurance_addr"]').length>0) {
			$('input.js_show_insaddr').val(ins_addr);
			$('textarea.js_show_insaddr').val(ins_addr.replace("<br>",""));
			//$('#bootstrap-validator-correspondence').bootstrapValidator('revalidateField', $('[name="insurance_addr"]'));
		}
		if($('[name="policyid"]').length>0) {
			$('.js_show_inspolicy').val($('select[name="policy_id"] option[value="'+get_value+'"]').text());
			$('#bootstrap-validator-correspondence').bootstrapValidator('revalidateField', $('[name="policyid"]'));
		}
	}
});
/* Dropdown based value changes end */


/*** Change keyword function start ***/
$(document).on( 'click', '.js_generate', function () {
	$('#bootstrap-validator-correspondence').data("bootstrapValidator").resetForm();
	$('#bootstrap-validator-correspondence').bootstrapValidator('validate');
	$(this).off('click');
});
/*** Change keyword function end ***/

/*** Preview function start ***/
$(document).on( 'click', '#js_template_preview', function () {
	var content = CKEDITOR.instances['editor1'].getData();
	if(content.trim().length>0) {	
		$(".js_mail_content").html(content);
		$("#correspondence_preview_modal h4").html($(".js_mail_subject").val());
		$("#correspondence_preview_modal").modal("show");
        // $("correspondence_preview_modal").style.zIndex = "9999";
	} else {
		js_alert_popup("Enter Content");
	}
});
/*** Preview function end ***/
	
/*** Preview mail send process start ***/
$(document).on( 'click', '.js_preview_send', function (e) {
	$('#bootstrap-validator-correspondence_send').data("bootstrapValidator").resetForm();
	$('#bootstrap-validator-correspondence_send').bootstrapValidator('validate');
	$(this).off('click');
});	

$(document).ready(function(){
/*** Correspondence mail sended success msg show functions start ***/
	var data = sessionStorage.getItem("mail_success_msg");
	if(data == "yes"){
		setTimeout(function () { 
			$("#msetTimail_success_alert").removeClass('hide').addClass('show');
			$("#mail_success_alert").fadeTo(1000, 600).slideUp(600, function(){
				$("#mail_success_alert").alert('close');
				$("#mail_success_alert").addClass('hide');
				sessionStorage.setItem("mail_success_msg","");
			});
			
		}, 10);
	}
});
/*** Correspondence mail sended success msg show functions end ***/
	
/*############## End - correspondence page ################# */
	
function daydiff(first, second) {
	return Math.round((second-first)/(1000*60*60*24));
}

function parseDate(str) {
	var mdy = str.split('/')
	return new Date(mdy[2], mdy[0]-1, mdy[1]);
}
	
//DOB datepicker  
$(document).on('focus','.dob', function(){ 
	$(this).datepicker({
		yearRange:'1900:+0',
		changeMonth: true,
		changeYear: true,
		maxDate: '0'
	});
});
	

$(document).on('ifToggled change', '.js-alert_appointment-change:checked', function () {
	var valch = $(this).val();
	var parent_id = $(this).parents("DIV.js-visit-calc").attr('id');
	$("#"+parent_id+" .js_alert_appointment_single").val(valch);
});

$(document).on('ifToggled change', '.js-alert_billing-change:checked', function () {
	var valch = $(this).val();
	var parent_id = $(this).parents("DIV.js-visit-calc").attr('id');
	$("#"+parent_id+" .js_alert_billing_single").val(valch);
});

/*function existspatientconact_validation(){
	var cat_options 		= $('select[name="contact_category[]"]');
	var cat_values = $.map(cat_options ,function(option) {
		return option.value;
	});
	if(cat_values!=''){
		var cat_values_arr = cat_values.toString().split(',');
		if(cat_values_arr.indexOf("Guarantor") != -1){
			setTimeout(function(){ $('.contact-info-form').bootstrapValidator('addField', 'guarantor_email[]'); }, 1000);
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('guarantor_last_name[]').enableFieldValidators('guarantor_last_name[]', true); }, 1000);
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('guarantor_first_name[]').enableFieldValidators('guarantor_first_name[]', true); }, 1000);
		}
		if(cat_values_arr.indexOf("Emergency Contact") != -1){
			setTimeout(function(){ $('.contact-info-form').bootstrapValidator('addField', 'emergency_email[]'); }, 1000);
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('emergency_last_name[]').enableFieldValidators('emergency_last_name[]', true); }, 1000);
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('emergency_first_name[]').enableFieldValidators('emergency_first_name[]', true); }, 1000);
		}
		if(cat_values_arr.indexOf("Employer") != -1){
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('employer_name[]').enableFieldValidators('employer_name[]', true); }, 1000);
		}
		if(cat_values_arr.indexOf("Attorney") != -1){
			setTimeout(function(){ $('.contact-info-form').bootstrapValidator('addField', 'attorney_email[]'); }, 1000);
			setTimeout(function(){ $('.contact-info-form').data('bootstrapValidator').addField('attorney_adjuster_name[]').enableFieldValidators('attorney_adjuster_name[]', true); }, 1000);
		}
	}
}*/

/*$(document).on('change', '.insurance_id', function(){ 
	$('.insurance-info-form').bootstrapValidator('revalidateField', 'insurance_id[]');
});*/

/*$(document).on('change', '.js_select_category_class', function(){ 
	//$('.insurance-info-form').bootstrapValidator('revalidateField', 'category[]');
	var id_name 		= $(this).attr('id');
	var cat_name		= $(this).val();
	var split_id_name 	= id_name.split('_');
	var count_val 		= split_id_name[1];
	if(cat_name==""){
		cat_name = "New Category";
	}
	else{
		cat_name = cat_name+" Category";
	}
	$('#js-insurance-title_'+count_val).html(cat_name);
});*/


$(document).on( 'change', '.js-employment_status', function () { 
	var employment_status_val = $(this).val();
	$('[name=work_phone]').closest('.form-group').show();
	$('[name=work_phone]').val(''); $('[name=work_phone_ext]').val('');
	// For employement details gurantor edit
	$('[name=employer_name]').val('');	
	$('[name=occupation]').val('');	

	$('[name=work_phone]').prop('disabled',false);
	$('[name=work_phone_ext]').prop('disabled',false);
	
	if(employment_status_val == '' || employment_status_val == 'Unknown'){
		$('[name=work_phone]').val(''); $('[name=work_phone_ext]').val('');	
		$('[name=employer_name]').val('');	
		$('[name=occupation]').val('');	
		$('[name=work_phone]').closest('.form-group').hide();
		$('[name=work_phone]').prop('disabled',true);
		$('[name=work_phone_ext]').prop('disabled',true);
	}
		
	if(employment_status_val=='Employed'||employment_status_val=='Self Employed'){
		$(".employed_option_sub_field").removeClass('hide').addClass('show');
		$(".student_option_sub_field").addClass('hide');
	} else if(employment_status_val=='Retired'){
		$(".employed_option_sub_field").addClass('hide');
		$(".student_option_sub_field").addClass('hide');
		$('[name=work_phone]').closest('.form-group').hide();
		$('[name=work_phone]').prop('disabled',true);
		$('[name=work_phone_ext]').prop('disabled',true);
	} else if(employment_status_val=='Student'){
		$(".student_option_sub_field").removeClass('hide').addClass('show');
		$(".employed_option_sub_field").addClass('hide');
		if($('[name=student_status]').val()=='Unknown' || $('[name=student_status]').val()=='Full Time') {
			$('[name=work_phone]').prop('disabled',true);
			$('[name=work_phone_ext]').prop('disabled',true);
		} else {
			$('[name=work_phone]').prop('disabled',false);
			$('[name=work_phone_ext]').prop('disabled',false);
		}
	} else{
		$(".employed_option_sub_field").addClass('hide');
		$(".student_option_sub_field").addClass('hide');
	}
	$('.patients-info-form').bootstrapValidator('revalidateField', "employer_name");
	$('.patients-info-form').bootstrapValidator('revalidateField', "occupation");
	$('.patients-info-form').bootstrapValidator('revalidateField', "student_status");
});

//Added datepicker dropdown issue in firefox
var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
$.fn.modal.Constructor.prototype.enforceFocus = function() {};

/*** Clinical Notes start ***/
$(document).on('keyup change',".js_select_change",function(){
	var current_val = $(this).val();
	$(this).closest(".form-group").find('input[type="hidden"]').val(current_val);
	var current_name = $(this).attr("name");
	$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField(current_name);
});


// Comment for unused code

/*

$(document).on('change',"#claim_no",function(){
	var claim_no = $(this).val();
	var patient_id = $(this).parents('form').attr('data-id');
	if(claim_no !="") {
		$("#js_wait_popup").modal("show");
	
		$.ajax({
			url: api_site_url + '/patients/'+patient_id+'/clinicalnotes/'+claim_no+'/claimdetail',
			type: "GET",// Type of request to be send, called as method
			success: function(res) {
				var data = JSON.parse(res);
				$('.js_select_dos').val(data.date_of_service);
				$('.js_select_facility').select2("val",data.facility_id);
				$('.js_select_rendering').select2("val",data.rendering_provider_id);
				$('.js_select_rendering_input').val(data.rendering_provider_id);
				$('.js_select_facility_input').val(data.facility_id);
				$('.js_select_dos_input').val(data.date_of_service);
				revalidateField();
				$('.js_select_dos').attr('disabled',"disabled");
				$('.js_select_facility').select2('disable');
				$('.js_select_rendering').select2('disable');
				$("#js_wait_popup").modal("hide");
			}
		});		
	}
	else {
		$('.js_select_dos').val("").removeAttr('disabled');
		$('.js_select_facility').select2("val",'').select2('enable',true);
		$('.js_select_rendering').select2("val",'').select2('enable',true);
		$('.js_select_rendering_input').val('');
		$('.js_select_facility_input').val('');
		$('.js_select_dos_input').val('');
		revalidateField();
	}
	
});

function revalidateField() {
	$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('facility_id');
	$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('provider_id');
	$('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('dos');
}
*/
/*** Clinical Notes End ***/


$(document).on('change','.js-bootstrap-policyid',function() {
	var getformid			= $(this).parents('form').attr('id');	
	var primary_insurance_id = $('#'+getformid).find('[name=insurance_id]').val();
	var primary_insurance_policy_id = $('#'+getformid).find('.js-bootstrap-policyid').val();
	var patient_id = $('#encode_patient_id').val();
	var insurance_id = $('#'+getformid).find('.js-v2-delete-insurance').data('id');
	if(insurance_id != undefined){
		$('.js_insgray'+insurance_id).css('display','none');
		$('.js_insgreen'+insurance_id).css('display','none');
		$('.js_insred'+insurance_id).css('display','none');
	}
	$('.eligibility_gray').css('display','none');
	$('.js_eliactive').css('display','none');
	$('.js_eliinactive').css('display','none');
	if(patient_id!='' && primary_insurance_id!='' && primary_insurance_policy_id!=''){
		$.ajax({
			type : 'GET',
			url  : api_site_url+'/patients/checkinsurance/'+patient_id+'/'+primary_insurance_id+'/'+primary_insurance_policy_id,
			success :  function(result){
				if(insurance_id != undefined){
					if(result == 1) {
						$('.js_insgray'+insurance_id).css('display','block');	
					}else if(result == 2){
						$('.js_insgreen'+insurance_id).css('display','block');		
					}else if(result == 3){
						$('.js_insred'+insurance_id).css('display','block');			
					}
				} else {
					if(result == 1) {
						$('.eligibility_gray').css('display','block');					
					}else if(result == 2){
						$('.js_eliactive').css('display','block');			
					}else if(result == 3){
						$('.js_eliinactive').css('display','block');			
					}
				}
			}
		});	
	}
});

//Patient Eligibility template select starts
$('.js-template').on( 'change', '#template_id',function() {
	var template_id = $(this).val();
	$('.js-show-template').html("");
	$('.js-show-template').addClass("hide");
	if(template_id != '') {
		$("#js_wait_popup").modal("show");
	
		$.ajax({
			url: api_site_url+'/patients/gettemplates/'+template_id,	
			type: 'get',
			success: function( data, textStatus, jQxhr ){
				$('.js-show-template').html(data);
				$("#js_wait_popup").modal("hide");
				$('.js-show-template').removeClass("hide");
				$('.js-show-template').attr("data-page","edit");
				changeInput();
			},
			error: function( jqXhr, textStatus, errorThrown ){
				console.log( errorThrown );
			}
		});
	}
});

function changeInput() {
	if($(".js-show-template").attr("data-page") =="edit") {
		$(".js-show-template input").each(function(){
			$(this).addClass("form-control input-sm-modal-billing").css({"width" : "23%","display": "inline"});
		});
		$(".js-show-template textarea").each(function(){
			$(this).addClass("form-control input-view-border1").css({"width" : "23%","display": "inline"});
		});
		$(".js-show-template").removeClass("hide");
	}
}

$("#js-template-submit").click(function() { 
	$("input").each(function(){
		$(this).attr("value", $(this).val());
	});
	$('[type=text], textarea').each(function(){ this.defaultValue = this.value; });
	$('[type=checkbox], [type=radio]').each(function(){ this.defaultChecked = this.checked; });
	$('select option').each(function(){ this.defaultSelected = this.selected; });
	$('#js-template-content').val($('.js-show-template').html()); 
}) ;
//Patient Eligibility template select ends

$(document).on('click','.js_show_patient_payment_details',function() {
	target = $(this).attr('data-url');
	$("#js_create_patient_detail_list").load(target, function(){        
        $('#js_create_patient_detail_list').on('show.bs.modal', function(e){
			//alert('x');
			$.AdminLTE.boxWidget.activate();
			$("#success-alert").addClass("hide");           
		}); 
		
        $("#show_problem_list").modal("show");
        return false;			
	});
	alert(target)
	$.ajax({
		type : 'POST',
		url  : target,
		data : serialized,
		success :  function(result){
			alert(result)
		}
	});	
});
/*
function problemListTable(target){
	processingImageShow("#js_table_search_listing","show");
	$.ajax({
		type : 'GET',
		url  : target,
		success :  function(result){
			$('.js_problem_list_loop').html(result);
			$('.js_problemlist_table').dataTable();
			processingImageShow("#js_table_search_listing","hide");
		}
	});
}
$(document).on('click','.js_problemlist_update',function(){
	var get_url = $(this).parents('form').attr('action');
	var get_url_arr = get_url.split('/problem');
	var target = get_url_arr[0]+'/ajaxupdate/problemlist';
	problemListTable(target);					
}); 
*/

/*$(document).on('click','.js_move_archive',function(e){
var current_url = $(this).attr('data-url');
	$("#js_move_confirm_modal")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_move_confirm_yes', function (e) {
		var conformation = $(this).html();
		if (conformation == "Yes") {
			console.log(current_url)
			$.ajax({
				type : 'GET',
				url  : current_url,
				success: function( data, textStatus, jQxhr ){
					var result = JSON.parse(data);
					if(result.status =="success") {
						$("#js_move_confirm_modal").modal("hide");
						js_alert_popup(result.message);
						window.location.href ='';
					}
					else {
						$("#js_move_confirm_modal").modal("hide");
						$("#claims_error_model").find(".modal-body .modal-desc").addClass("text-center med-green font600");
						$("#claims_error_model").find(".modal-body .modal-desc").html(result.message);
						$("#claims_error_model").modal("show");
					}
				},
				error: function( jqXhr, textStatus, errorThrown ){
					js_alert_popup("Something went wrong, try after sometime");
				}
			});
		}
	});
});
*/

$(document).on('click','.js_alert_archive', function () {
	var type = $(this).data('name');
	
	if(type == 'archive'){
		var archiveurl = $(this).data('url');
		window.location.href = archiveurl;
	} else if(type == 'newinsurance'){
		$('#patientarchive_model').modal("hide");
		$('.js-addmore_insurance').trigger('click');
	}	
});	

$('select').select2('readonly',true);
 
$(document).on('click','.same_checkbox',function(){  
	if($(this).is(':checked') == false){
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('.same_address').removeClass('hide');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('#guarantor_address1').val('');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('input[name="guarantor_address2"]').val('');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('input[name="guarantor_city"]').val('');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('input[name="guarantor_state"]').val('');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('input[name="guarantor_zip5"]').val('');
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('input[name="guarantor_zip4"]').val('');
	} else {
		$('#'+$(this).parents('div.js-address-class').attr('id')).find('.same_address').addClass('hide');
	}
});

$(document).on('click','.ajax-loading-demo',function(){
	var patient_id = $("#encode_patient_id").val();
	$.ajax({
		type 		: 	'GET',
		url  		:	api_site_url+'/patients/ajax_loading_demographics/'+patient_id,
		success 	:  function(msg) { 
			$(".test-demo").html(msg);
			$("#txtAge").datepicker({
				yearRange: "-90:+0",
				changeMonth: true,
				changeYear: true
			});
			if($("#txtAge").val() == '0000-00-00' || $("#txtAge").val() == '01/01/1901' || $("#txtAge").val() == '1901-01-01' || $("#txtAge").val() == '01/01/1970')
				$("#txtAge").val('');
			if($("#deceased_date").val() == '0000-00-00' || $("#deceased_date").val() == '1901-01-01' || $("#deceased_date").val() == '11/30/-0001' || $("#deceased_date").val() == '01/01/1970')
				$("#deceased_date").val('');
			$('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});
			formpersonalinfo();		
		}
	});
});

$(document).ready(function(){
	if($("#txtAge").val() == '0000-00-00' || $("#txtAge").val() == '1901-01-01' || $("#txtAge").val() == '01/01/1970'){
		$("#txtAge").val('');
		$("#age").val('');	
	}	
	if($("#deceased_date").val() == '0000-00-00' || $("#deceased_date").val() == '1901-01-01' || $("#deceased_date").val() == '01/01/1970')
		$("#deceased_date").val('');
});

/* Patient Authorization ->New Authorization->cancel button click event for select2 */
$(document).on('click','#configform',function(){
/* select box set default value  */
	$('#js-bootstrap-validator-authorization .select2-chosen').html('-- Select --')
	/*Insurance id  //auth_insurance_id */
	/*Pao id  // pos_id */
	/* Rest the form */
	$('#js-bootstrap-validator-authorization')[0].reset();
});

function UnPopItDig() 
{ 
    /* nothing to return */ 
}

/* Enabled the Save button in in model popup button  */
$(document).keypress(function(event){

	var keycode = (event.keyCode ? event.keyCode : event.which);
	setTimeout(function() {
		if(keycode == '13'){
			$('#js-insuranceform-submit-button-v2').removeAttr("disabled")
		}
	},200);

});

//Patient document module table view starts here
/*$(document).on('click', '.js-tab-document', function(){ 
	var document_url = [];
	var checked_len = $("input[name='document']:checked").length
	if(checked_len<1){
		js_alert_popup('Please select document');
	} else if(checked_len>5){
		js_alert_popup('Maximum allowed 5 documents');

	} else{
		$.each($("input[name='document']:checked"), function(){            
            document_url.push($(this).attr('data-url'));
     	});
		for(i=0;i<document_url.length;i++){
			openInNewTab(document_url[i]);
		}		
	}	
})
function openInNewTab(url) {	
  var win = window.open(url, '_blank');
  win.focus();
}*/
//Patient document module table view ends here


/*$(document).delegate('a[data-target=#create_notes]', 'click', function () { 
    $("#create_notes .modal-body").html("");
    var target = api_site_url + '/' + $(this).attr("data-url");   
    $("#create_notes .modal-body").load(target, function () {
       	$("select").select2();
    });
});
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
            console.log(data)             ;           	
              	data_val = JSON.parse(data); 

              	status= data_val.status;
              	msg= data_val.message;              	
              	msg_data = msg.patient_notes_type;
              	if(status == "success"){
              		$("#create_notes").hide();
              		js_alert_popup(msg);
              		location.reload();
              	} else {
              	    msg = (typeof msg_data != undefined)?"Type already selected":msg
              		js_alert_popup(msg);              		
              	}
            }
        });
        return false;
    }
});
*/
//      Same as patient address checkbox
/*$(document).on( 'ifToggled click change', 'input:checkbox.js-same_as_patient_address-v2', function () { console.log("tester same as");
	var current_div_id 	= $(this).closest(".js-address-class").attr("id");
	var current_form_id     = $(this).parents("form").attr("id");
       // alert(current_div_id+' and form '+current_form_id);
	if ($('#'+current_form_id+' #'+current_div_id+' input:checkbox.js-same_as_patient_address-v2').prop('checked') === true) {
		setTimeout(function() {
			$('#'+current_form_id+' #'+current_div_id+' .same_address').addClass('hide');               
		}, 100);
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-address1').val($('#contact_same_as_address1').val());
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-address2').val($('#contact_same_as_address2').val());
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-city').val($('#contact_same_as_city').val());
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-state').val($('#contact_same_as_state').val());
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-zip5').val($('#contact_same_as_zip5').val());
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-zip4').val($('#contact_same_as_zip4').val());
           
		$('#'+current_form_id+' #'+current_div_id+' .js-v2-address1').blur();
		
	} else {		
        setTimeout(function () { 
			$(".js-bootstrap-validator-contact-edit").find("input[name='same_as_patient_address'][type=checkbox]").val('off');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-address1').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-address2').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-city').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-state').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-zip5').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-v2-zip4').val('');
			$('#'+current_form_id+' #'+current_div_id+' .js-address-error').addClass('hide');
			$('#'+current_form_id+' #'+current_div_id+' .js-address-success').addClass('hide');
			$('#'+current_form_id+' #'+current_div_id+' .same_address').removeClass('hide');
		}, 100);
	}
});*/

/*Change responsibility to insurance using shortcut key (Alt+r) in patient registration */
$(document).mapKey('Alt+r', function(e){
    if($("#r-selfpay").attr('checked'))    {
    	$("#r-insurance").next().trigger("click");
    } else {
    	$("#r-selfpay").next().trigger("click");
    }              
});