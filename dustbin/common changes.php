<!-- Sample format for using hover content popup start -->
<td>
<?php $provider_name = $questionnaries->provider->provider_name; ?>   
	<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
		<a id="someelem{{@$questionnaries->provider->id}}" class="someelem" data-id="{{@$questionnaries->provider->id}}" href="#contact-info"> {{ str_limit($provider_name,25,'...') }}</a> 
	   
	   <div class="js-tooltip_{{@$questionnaries->provider->id}}" style="display:none;">
			<span class="med-orange sm-size">{{ $provider_name }}</span> 
			<p style="margin-bottom: 0px; color:#868686;"><b>Type:</b> {{ @$questionnaries->provider->provider_types->name }}
				<br>
				<b>DOB:</b> {{ App\Http\Helpers\Helpers::dateFormat(@$questionnaries->provider->provider_dob,'dob') }}<br>
				<b>Gender:</b> {{ @$questionnaries->provider->gender }}<br>
				<b>ETIN Type:</b> {{ @$questionnaries->provider->etin_type }}<br>
				<b>SSN or TAX ID Number:</b> {{ @$questionnaries->provider->etin_type_number }}<br>
				<b>NPI:</b> {{ @$questionnaries->provider->npi }}
			</p>
		</div>
	</div>   
</td>
<!-- Sample format for using hover content popup end -->
<?php

/************************************** Gopal JUly 02 2016  Start **************************************/

/*** Email function sample ***/
email:{
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
}
/*** Email function sample end***/

/*** Cell phone function sample ***/

//Home phone or work phone or phone jst give "home_phone_limit or work_phone_limit or phone_limit" in alert msg
cell_phone:{
	message: '',
	validators: {
		callback: {
			message: '',
			callback: function (value, validator,$field) {
				var cell_phone_msg = '{{ trans("common.validation.cell_phone_limit") }}';// alert message
				var ext_msg = '{{ trans("common.validation.cell_phone") }}';// extension alert message
				var ext_length = validator.getFieldElements('cell_phone').closest("div.form-group").find("input:last.dm-phone-ext").val().length;
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
}
/*** Cell phone function sample end***/

/*** Sample Effective date and termination date start ***/
	effectivedate: {
		message: '',
		trigger: 'keyup change',
		validators: {
			date: {
				format: 'MM/DD/YYYY',
				message: '{{ trans("common.validation.date_format") }}'
			},
			callback: {
				message: '{{ trans("common.validation.effectivedate") }}',
				callback: function (value, validator) {
					var stop_date = validator.getFieldElements('stop_date').val();
					var response = startDate(value,stop_date);
					if (response != true){
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
	terminationdate: {
		message: '',
		trigger: 'keyup change',
		validators: {
			date: {
				format: 'MM/DD/YYYY',
				message: '{{ trans("common.validation.date_format") }}'
			},
			callback: {
				message: '',
				callback: function (value, validator) {
					var eff_date = validator.getFieldElements('effectivedate').val();
					var ter_date = value;
					var response = endDate(eff_date,ter_date);
					if (response != true){
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
	function startDate(start_date,end_date) {
		var date_format = new Date(end_date);
		if (end_date != '' && date_format !="Invalid Date") {
			return (start_date == '') ? '{{ trans("practice/practicemaster/codes.validation.str_date_required") }}':true;
		}
		return true;
	}
	function endDate(start_date,end_date) {
		var eff_format = new Date(start_date);
		var ter_format = new Date(end_date);
		if (ter_format !="Invalid Date" && end_date != '' && eff_format !="Invalid Date" && end_date.length >7 && checkvalid(end_date)!=false) {
			var getdate = daydiff(parseDate(start_date), parseDate(end_date));
			return (getdate > 0) ? true : '{{ trans("common.validation.effectivedate") }}';
		}
		else if (start_date != '' && eff_format !="Invalid Date") {
			return (end_date == '') ? '{{ trans("common.validation.ter_date_required") }}':true;
		
		}
		return true;
	}
	function daydiff(first, second) {
		return Math.round((second-first)/(1000*60*60*24));
	}
	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
	function checkvalid(str) {
		var mdy = str.split('/');
		if(mdy[0]>12 || mdy[1]>31 || mdy[2].length<4 || mdy[0]=='00' || mdy[0]=='0' || mdy[1]=='00' || mdy[1]=='0' || mdy[2]=='0000') {
			return false;
		}
	}
	address1: {
		message: '',
		validators: {
			message: '',
			callback: function (value, validator) {
				var msg = addressValidation(value,"required");
				if(msg != true){
					return {
						valid: false,
						message: msg
					};
				}
				return true;
			}
		}
	},
	address2: {
		message: '',
		validators: {
			message: '',
			callback: function (value, validator) {
				var msg = addressValidation(value);
				if(msg != true){
					return {
						valid: false,
						message: msg
					};
				}
				return true;
			}
		}
	},
	city: {
		message: '',
		validators: {
			message: '',
			callback: function (value, validator) {
				var msg = cityValidation(value,"required");
				if(msg != true){
					return {
						valid: false,
						message: msg
					};
				}
				return true;
			}
		}
	},
	state: {
		message: '',
		validators: {
			message: '',
			callback: function (value, validator) {
				var msg = stateValidation(value,"required");
				if(msg != true){
					return {
						valid: false,
						message: msg
					};
				}
				return true;
			}
		}
	},
	zipcode5: {
		message: '',
		validators: {
			callback: {
				message: '',
				callback: function (value, validator) {
					var msg = zip5Validation(value,"required");
					if(msg != true){
						return {
							valid: false,
							message: msg
						};
					}
					return true;
				}
			}
		}
	},
	zipcode4: {
		message: '',
		trigger: 'change keyup',
		validators: {
		   message: '',
			callback: {
				message: '',
				callback: function (value, validator) {
					var msg = zip4Validation(value);
					if(msg != true){
						return {
							valid: false,
							message: msg
						};
					}
					return true;
				}
			}
		}
	}
	contactperson: { // Length validation
		message: '',
		validators: {
			callback: {
				message: '',
				callback: function (value, validator) {
					var msg = lengthValidation(value,'contactperson');
					if(msg != true){
						return {
							valid: false,
							message: msg
						};
					}
					return true;
				}
			}
		}
	}
/*** Effective date and termination date end ***/


### Need min and max validation means 
	var response = getMinAlert(min_value,max_value,error_msg); //if min_value is going to higher than max_value get error msg 

### Need to validate 2 field means use it 
	cityStatevalitation('current field','another field','form name');// now using city state validation only 

	
/*** Blade file related Common classes ***/

	### Decimal with thousand separaton
		just add class name in that field => js_amount_separation

	### All Capital Format
		just add class name in that field => js-all-caps-letter-format
	
	### First letter capital format
		just add class name in that field => js-letters-caps-format

	### All Lower Format
		just add class name in that field => js-email-letters-lower-format
		
	### Check all checkbox 
		just add class name in that field => js_submenu and need to add class "js_menu" parent div 
	
	### data-mask classes
		.dm-tax-id		in blade file	taxID
		.dm-npi			in blade file	Npi number
		.dm-zip5		in blade file	Zipcode 5
		.dm-zip4		in blade file	Zipcode 4
		.dm-phone		in blade file	phone number
		.dm-phone-ext	in blade file	phone ext number
		.dm-ssn			in blade file	ssn number
		.dm-date		in blade file	date number
		.dm-year		in blade file	year format
		.dm-time		in blade file	time format
		.dm-fax			in blade file	fax number
		.js-print		in blade file	Enable the print option

	### Name format like [last name, first name  middle name]
		$name = App\Http\Helpers\Helpers::getNameformat(@$app_list_val->patient->last_name,@$app_list_val->patient->first_name,@$app_list_val->patient->middle_name);	
		
	### Error message Do not hot code any error message in blade file, so use these format
		{{ trans("practice/patients/patients.validation.visits_used") }}		

	### Image file encode
		<?php 
			$filename = $practice->avatar_name . '.' . $practice->avatar_ext;
			$img_details =[];
			$img_details['module_name']='practice';
			$img_details['file_name']=$filename;
			$img_details['practice_name']="""";
			$img_details['class']='img-border';
			$img_details['alt']='practice-image';
			$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
		?>       */
		{!! $image_tag !!}  
	### Date format 
		{{ ($arg->date != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($arg->date,'date'): '' }}
	### Encode and Decode option
		<?php 	$arg->id	= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($arg->id,'encode'); ?> 
		<?php	$arg->id	= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($arg->id,'decode'); ?>      


	
/*** Blade file related Common classes ***/

/*** Regular Expression format ***/

	### Alpha only 
		/^[A-Za-z]+$/
		
	### Numeric only  
		/^[0-9]+$/        //and whatever u need just add inner side in [];
		
	### if you want to limit char length  
		/^[0-9]{0,end_length}$/      
		
	### if u need to set min and max is same jst use  
		/^[0-9]{length}$/ 
		
	### if you need to replace a string into string 
		string.replace(/[^0-9]/g, ""); // ^-> mentioning leave and 0-9 digit so meaning is leave digit and remainiing repaced dont use i g meaning global replace;
		
	### u want to check string is present or not 
		var re = new RegExp(/^[0-9() -]+$/);
		if(string.match(/[a-zA-Z0-9]/)) {true;}
		if (re.test(string)) { true }
		if (string.search("/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/") == -1) { true} //ex for phone number validation
	
/*** Regular Expression format ***/

/*** Ajax part ***/
	### checkbox or radio option
		$('input[type="checkbox"].flat-red').iCheck('update');
		or
		$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-red'});
		
	### Select box
		$("select select2").select2(); // trigger
		$("select select2").select2('val',''); //reset values
		$("select select2").select2('val',all_values); //add values all_values=> need as array format
	
	### Bootstrap validator
		$('#js-bootstrap-validator').bootstrapValidator('addField', 'field name'); // need to add and then only work
		$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'field name'); // revalidate
		$('#js-bootstrap-validator').bootstrapValidator('validate'); // trigger validator 

/*** Ajax part ***/

### Current url sample usage
	$routex = explode('.',Route::currentRouteName());
	$currnet_page = Route::getFacadeRoot()->current()->uri(); 
	if(strpos($currnet_page, 'check string') !== false)

/************************************** Gopal JUly 02 2016  End **************************************/