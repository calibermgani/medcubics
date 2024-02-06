/****
 Author: 	Ravi
 Date: 		22 May 2018
 Updated:	Kannan
 ----------- INDEX ------------- 
 1. Shortcut Keys
 2. Patients Page Form Related JS 
 ***/


function LoadContactsTab() {
    GuarantorSelfValidate();
}

$(document).ready(function(e) {
	if($('select[name="guarantor_relationship"]').val() =='Self'){ //console.log("test tester");
		$('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
		$('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
		$('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());

		$('input[name="guarantor_last_name"]').attr('readonly', true);
		$('input[name="guarantor_first_name"]').attr('readonly', true);
		$('input[name="guarantor_middle_name"]').attr('readonly', true);
	} 

	if($('select[name="emergency_relationship"]').val() == ''){ //console.log("test tester");         
		$('input[name="emer_last_name"]').attr('readonly', true);
		$('input[name="emer_first_name"]').attr('readonly', true);
		$('input[name="emer_middle_name"]').attr('readonly', true);
		$('input[name="emer_email"]').attr('readonly', true); 
		$('input[name="emer_cell_phone"]').attr('readonly', true);  
    } 
});

function GuarantorSelfValidate()
{  
	if($('select[name="guarantor_relationship"]').val() =='Self'){
		$('input[name="guarantor_last_name"]').val($('input[name="last_name"]').val());
		$('input[name="guarantor_first_name"]').val($('input[name="first_name"]').val());
		$('input[name="guarantor_middle_name"]').val($('input[name="middle_name"]').val());

		$('input[name="guarantor_last_name"]').attr('readonly', true);
		$('input[name="guarantor_first_name"]').attr('readonly', true);
		$('input[name="guarantor_middle_name"]').attr('readonly', true);
	}else{
	  //  setTimeout(function(){
	  //  $('input[name="guarantor_last_name"]').val('');
	 //   $('input[name="guarantor_first_name"]').val('');
	 //   $('input[name="guarantor_middle_name"]').val('');

		$('input[name="guarantor_last_name"]').attr('readonly', false);
		$('input[name="guarantor_first_name"]').attr('readonly', false);
		$('input[name="guarantor_middle_name"]').attr('readonly', false);
   
  //  },200);
	}
}

// Short Cut Keys on Registration

//Insurance Tab
//  Responsibility Change (Alt + r)
/*(document).mapKey('Alt+r', function () {
       var selfPay = $("#r-selfpay").parent().hasClass("checked");
        if(selfPay){
             $('input[type="radio"].flat-red').eq(10).iCheck('check');
        }else{
             $('input[type="radio"].flat-red').eq(9).iCheck('check'); 
        }
       return false;
});*/

//  Update Responsibility (Alt + u)
$(document).mapKey('Alt+u', function () {
   $('.js-v2-insurance-responsible-btn').click();
   return false;
});

//All Tabs
//  Open New Insurance, New Contact or New Authorization based on current tab (Alt + n)
/*$(document).mapKey('Alt+n', function (e) {
    var tabName = $.trim($('.nav-tabs .active').text());
     if (!$("body").hasClass("modal-open")) {
    if(tabName =="Insurance"){
        $(".js-addmore_insurance").click();
    }else if(tabName == "Contacts"){
        $("#addmore_contact_v2").click();
        $("#contact_category-0").select2("open");
    }else if(tabName == "Authorization"){
        $(".js-addmore_authorization_v2").click();
    }
  }
    return false;
}); */

//  Save form (Alt + s)
/*$(document).mapKey('Alt+s', function () {
    if($("#add_new_insurance").css('display') === 'block'){
         $('#js-insuranceform-submit-button-v2').click();
    }else if($("#add_new_contact").css('display') === "block"){
         $('#js-form-submit-button-v2').click();
    }else if($("#add_new_authorization").css('display') == "block"){
         $("#js-authorizationform-submit-button-v2").click();
    }else{
       $('#js-v2-demography-submit').click();
    }
}); */

// Quit Form by Escape key press
$(document).keyup(function(e) {
	if(e.keyCode==27) {
		if($("#add_new_insurance").css('display') === 'block'){
			$('.close').click();         
		}else if($("#add_new_contact").css('display') === "block"){
			$('.close').click(); 
		}else if($("#add_new_authorization").css('display') == "block"){
			$('.close').click(); 
		}else if($('#js_confirm_patient_demo_info_box1').css('display') === 'block') {
			$('.js_common_modal_popup_cancel').click();
		}
	}
});

$(document).on('change','.js-ajax-append-category-selection',function(){
	var dataId = $(this).attr('data-insid');
	if(this.value == 'Secondary'){
		$('div#secondaryInsuranceCode_'+dataId).removeClass('hide').show();
	}else{
		$('div#secondaryInsuranceCode_'+dataId).addClass('hide').hide();
		$('select[name="medical_secondary_code"].js-medical-secondary-'+dataId).val('');
	}
})

$(document).on('change','.js-popup-category-selection',function(){
	if(this.value == 'Secondary'){
		$('.js_medicareins_0').removeClass('hide').show();
	}else{
		$('.js_medicareins_0').hide().addClass('hide');
	}
})

// $(".js-address-check" ).trigger( "blur" );
