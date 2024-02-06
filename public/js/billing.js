// To aoide past date we use this function start here
var tapped = false
function checkDate(value) {
    var EnteredDate = value;
    var date = EnteredDate.substring(3, 5);
    var month = EnteredDate.substring(0, 2);
    var year = EnteredDate.substring(6, 10);
    var myDate = new Date(year, month - 1, date);
    var today = new Date();
    today.setHours(0, 0, 0, 0);    
    if (myDate >= today) {
        return true;
    } else {
        return false;
    }
}

function validDateCheck(value) {
    if (value != '') {
        var comp = value.split('/');
        var m = parseInt(comp[0], 10);
        var d = parseInt(comp[1], 10);
        var y = parseInt(comp[2], 10);
        var date = new Date(y, m - 1, d);
        if (date.getFullYear() == y && comp[2].length == 4 && date.getMonth() + 1 == m && date.getDate() == d) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

$(document).on('change', "#js-search-val", function () {
    $(this).val($(this).val());
});

$(document).ready(function () {
    if ($('input[name="admit_date"]').length) {
        getmodifierandcpt();
    }
    changetext();
    patientbalancenew();
});

function Compare(type)
{
    var strStartTime = document.getElementById("anesthesia_start").value;
    var strEndTime = document.getElementById("anesthesia_stop").value;
    var returnval = [];
    var newstarttime = strStartTime.split(' ');
    var newendtime = strEndTime.split(' ');
    var stt = new Date("November 13, 2013 " + strStartTime); // Only comparision with time
    startTime = stt.getTime();
    var endt = new Date("November 13, 2013 " + strEndTime);
    endTime = endt.getTime();
    if (typeof newstarttime[1] != "undefined" && typeof newendtime[1] && newstarttime[1] != newendtime[1]) {
        returnval['return'] = true;
    }
    if (startTime > endTime && newstarttime[1] == newendtime[1]) {
        returnval['return'] = false;
        returnval['message'] = (type) ? "Stoptime is less than start time" : "Start time is greater than stoptime";
        $('.bootstrap-timepicker-widget').find(":input").val("");
    }
    if (startTime == endTime) {
        returnval['return'] = false;
        returnval['message'] = (type) ? "Stoptime equals to start time" : "Start time equals to stoptime";
        $('.bootstrap-timepicker-widget').find(":input").val("");
    }
    if (startTime < endTime && newstarttime[1] == newendtime[1]) {
        returnval['return'] = true;
        returnval['message'] = (type) ? "Stoptime is greater than start time" : "Start time is less than stoptime";
        $('.bootstrap-timepicker-widget').find(":input").val("");
    }
    return returnval;
}

// Default model popup cancel button starts here
$(document).on('click', ".js_popup_commonform_reset", function (element) {
    var form = $(this).parents('form:first').attr('id');
    $('#' + form)[0].reset();
    $("select.select2.form-control").select2();
    $('#' + form).data("bootstrapValidator").resetForm();
});

// Default model popup cancel button ends here
$(document).on('change', '#authorization', function () {
    if($(this).val() == '') {
        $('input[name="auth_no"]').val("");
        $('.js-remove-auth-number').hide();
        $('.js-popupauth').attr('checked', false);
        $('#auth_id').val("");
    }   
})

function getDateformat(value) {
    var fullDate = new Date(value)
    //Thu May 19 2011 17:25:38 GMT+1000 {}
    //convert month to 2 digits
    var twoDigitMonth = ((fullDate.getMonth().length + 1) === 1) ? (fullDate.getMonth() + 1) : '0' + (fullDate.getMonth() + 1);
    var currentDate = fullDate.getDate() + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
}

//  Popup data of provider selection work has been starts here
if ($('.js-dropdown').length) {
    $('div.js-dropdown').each(function () {
        if (!$(this).hasClass('notempty')) {
            $(this).hide();
        }
    });
}

//append more rows
$(document).delegate('.append', 'click', function () {
    line_item = $('.js-append-parent li').length;
    if (line_item >= 30) {
        $(this).hide();
        js_alert_popup(maximum_line_item);
        return false;
    } else {
        var i = $('#js-appendrow').val();
        enable_status = $(".js-append-parent li:last-child input[name^='cpt']").val();  // check the last cpt was not empty to enable the record
        $.get(api_site_url + '/patients/addmoredosrow/' + i, function (data) {
            $('.js-append-parent').last('li').append(data);
            setTimeout(function () {
                callicheck();
                $('.js-append-parent li').show();
                if (enable_status == "undefined" || enable_status == '') {
                    $('.js-disable-div-' + i).find(':input').prop("disabled", true);
                    //$('.js-disable-div-'+i).find('td').addClass('class-readonlyclass-readonly');
                }
            }, 50);
            $('#js-appendrow').val(parseInt(i) + parseInt(1));
            validatorcallback();
            // Enable bootsrape validation on appending row starts here
            // Enable bootsrape validation on appending row ends here
        });
        if (line_item == 9)
            $(this).hide();
        $('.append').show();
    }
});

function validatorcallback() {
    var validator = $('form#js-bootstrap-validator').data('bootstrapValidator');
    validator.addField('js_charge_amt');
    validator.addField('from_dos');
    validator.addField('to_dos');
    validator.addField('icd_pointer');
    validator.addField('refering_provider');
    validator.addField('copay_applied');
    validator.addField('box_24_AToG[]'); 
}

// Charge entry line item delete process starts here
var deleted_value = [];
$(document).on('click', '.js-chargelineitem-delete', function () {
    div_id = $(this).attr('data-id');
    $this = $(this);
    line_item = $('.js-append-parent li').length;
    total = 0.00;
    //var listItem = $(this).parent().parent().children().index("li");
    var listItem = $(this).parent().index();
    var lineitem_delete_id = $(this).attr('data_delete_id');
    $('#js_confirm_box_charges_content').html("Are you sure you want to delete?");
    $("#js_confirm_box_charges")
        .modal({show: 'false', keyboard: false})
        .one('click', '.js_modal_confirm1', function (eve) {
            confirm_alert = $(this).attr('id')
            if (confirm_alert == 'true') {
                if (line_item == 1) {
                    js_alert_popup(minimum_line_item);
                    return false;
                } else {
                    $('.js-disable-div-' + div_id).remove();
                    if (line_item <= 24) {
                        $('.append').show();
                    }
                    if (listItem == 0) {
                        next_id = $('.js-append-parent li:first-child').attr('id');

                        $('#' + next_id).find(':input').prop("disabled", false);
                        // prev_div = $('.js-disable-div-'+parseInt(parseInt(div_id) - parseInt(1))).find(':input[type="text"]').filter(":visible:first").val();
                        // console.log("previous div values"+prev_div);
                        // next_div = $('.js-disable-div-'+parseInt(1)+parseInt(div_id)).attr('class');
                        //   console.log("next div"+next_div);
                        //if(prev_div)
                        $('.js-disable-div-' + parseInt(parseInt(1) + parseInt(div_id))).find(':input').prop("disabled", false);
                    }
                    validatelineitem(div_id);
                    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt');
                    $('.js-charge').each(function () {
                        total += Number($(this).val());
                    });
                    $('.js-total').val(parseFloat(total).toFixed(2));
                    deleted_value.push(lineitem_delete_id);
                    $('#js_line_itemdelete_id').val(deleted_value);
                    validatorcallback();
                }
            } else {
                return false;
            }
        });
});

$(document).ready(function () {
    $("select.select2_payment.form-control").select2({maximumSelectionSize: 3});
});
// Charge entry line item delete process starts ends

// After ajax call for a popup if we used anyradio or checkbox on the popup use this funciton again starts
function callicheck() {
//Hided by Akash
/*    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    }); */
    $("select.select2.form-control").select2();
}

// After ajax call for a popup if we used anyradio or checkbox on the popup use this funciton again starts
// To aoide past date we use this function ends here
// When we load page initially we focus therendering provider as Devi mam asked starts
price_format = "";
//Ends

$(document).ready(function(){   
    if ($("#s2id_providerpop").length > 0)
        $('#s2id_providerpop').select2('focus');
    $('#insurance_id').change();
    $('#test_id').change();
    //$('#s2id_PatientDetail').addClass('select2-container-active');   
    $(document).delegate('a[data-target=#auth]', 'click', function () {
        $("#auth .modal-body").html("");
        var target = api_site_url + '/' + $(this).attr("data-url");
        insurance_id = $('#js-insurance').val();
        $("#auth .modal-body").load(target, function () {
            if ($('#authorization').val() != '')
                $('.js-remove-auth-number').show();
			popuploadedfunction(insurance_id);
			callicheck();            
			authorization_val = $('#authorization').val();
			//jQuery("#auth input:radio").attr('disabled',false); // Removed disabled feature
        });
    });
});

$(document).delegate('a[data-target=#js-model-popup]', 'click', function () {
    // Update by baskar - 27/02/19 - Start (last update 05/03/19)
    var page = $(this).data('page');
    var facility_id = $('#facility_id').val();
    var cpts = $('input[name*="cpt["]').serializeArray();
    create_cpt = '&&&'+facility_id+'_';
    edit_cpt = '?val='+facility_id+'_';
    $.each(cpts,function(index,value){
        create_cpt += value.value+'_';
        edit_cpt += value.value+"_";
    });
    if($(this).attr('data-url').indexOf('edit') != -1){
        //console.log("edit found");
        var url = $(this).attr('data-url').split('?');
        $(this).attr('data-url',url[0]+edit_cpt);
    } else {
        var url = $(this).attr('data-url').split('&&&');
        $(this).attr('data-url',url[0]+create_cpt);
    }
    /*if(page=='create'){
        var url = $(this).attr('data-url').split('&&&');
        $(this).attr('data-url',url[0]+create_cpt);
    }else{
        var url = $(this).attr('data-url').split('?');
        $(this).attr('data-url',url[0]+edit_cpt);
    }*/
    // Update by baskar - 27/02/19 - End

    $("#js-model-popup .modal-body").html("");
    addwallet = "add";
    addwallet_name = ""; // to prevent default submisssion
    enablequalifierfield();
    var target1 = $(this).attr("data-url");
    var target_window = $(this).attr('data-target');
    var text = $(this).text();
    text = text.trim();
    if (target1.indexOf('referingprovider') > -1) {
        text = "Add Provider";
    }
    $("#js-model-popup .modal-lg").addClass('modal-md');
    $("#js-model-popup .modal-title").html(text);
    if (text == 'Ambulance Billing' || text == 'Other Details' || text == 'Claim Details') {
        $("#js-model-popup .modal-md").removeClass('modal-md').addClass('modal-md-500');
    }
    $("#js-model-popup .modal-body").load(target1, function () {
        modelformsubmit();
        $("select").select2();
        callicheck();
    });
    removeHash();
});

$(document).delegate('.js_allowed_visit', 'keyup change blur', function () {
    allowed_visit = $("input[name='allowed_visit']").val();
    visit_used = $("input[name='visits_used']").val();
    total = allowed_visit - (!isNaN(visit_used) ? visit_used : 0);
    if (total > 0) {
        $('.js-visit-remaining').val(total);
    } else if (total == 0) {
        total = (allowed_visit && visit_used != '' && allowed_visit && visit_used != 'NaN') ? total : "";
        $('.js-visit-remaining').val(total);
    } else {
        $('.js-visit-remaining').val("");
    }
});

$(document).delegate('.js_allowed_amt', 'keyup change blur', function () {
    allowed_amt = $("input[name='allowed_amt']").val();
    amt_used = $("input[name='amt_used']").val();
    total = allowed_amt - amt_used;
    if (total > 0) {
        $('.js-amt-remaining').val(parseFloat(total).toFixed(2));
    }
});

$(document).delegate('.js_allowed_amt', 'change', function () {
    $(this).val(parseFloat($(this).val()).toFixed(2));
});

function popuploadedfunction(insurance_id)
{
    $('.js-collapse').click(function () {
        $('.js-collpased').slideToggle();
        if ($('.js-collapse').find('.fa').hasClass("fa-plus")) {
            $('.js-collapse').find('.fa').addClass("fa-minus").removeClass("fa-plus");
        } else {
            $('.js-collapse').find('.fa').removeClass("fa-minus").addClass("fa-plus");
        }
    });    
    $("input[type=radio]").attr('disabled', true);
    $('.insurance-' + insurance_id + ' :input').prop("disabled", false);
    $('#auth_insurance_id').val(insurance_id);
    if ($('#auth_id').val() != '') {
        $('input:radio[data-val="' + $('#auth_id').val() + '"]').prop('checked', true);
    }
}

$(document).on('ifToggled change', '.js-popupauth', function () {
    if ($(this).is(":checked")) {
        $('#authorization').val($('.js-authno-' + $(this).attr('id')).text());
        $('#auth_id').val($(this).attr('data-val'));
        $('#auth').modal('hide');
        checkforvalidauthorization($(this).attr('id'));
        $('input[name="auth_no"]').focus();
        $('input[name="admit_date"]').focus();
    }
});

$(document).delegate('.js-auth_datepicker', 'focus', function () {
    var eventDates = {};
    eventDates[ new Date( today_practice )] = new Date( today_practice );
    var maxDate  = new Date( today_practice );
    $('.js-auth_datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+100',
        maxDate : maxDate,
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

//DatePicker - date limit changed 1970 after date showing in the datepicker
//Revision 1 - Ref: MR-2494 14 Aug 2019: Selva

$(document).delegate('.js-payment_datepicker', 'focus', function () {
    $('.js-payment_datepicker').datepicker({
        yearRange: '-40:+10',
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        onClose: function (selectedDate) {this.focus();	}
    });
});

$(document).ready(function(){
	if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {  
		var eventDates = {};
		eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone ); 
		$('#backDate').datepicker({
			yearRange: '-40:+10',
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
			}
		});
    }
});

$(document).delegate('#backDate', 'focus', function () {
    var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone ); 
    $('#backDate').datepicker({
        yearRange: '-40:+10',
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
        }
    });
});

$(document).delegate('#date_of_injury', 'focus', function () {
    var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );  

    $("#date_of_injury").datepicker({
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

$(document).delegate('.call-datepicker', 'focus', function () {
    var eventDates = {};
    eventDates[ new Date( get_default_timezone )] = new Date( get_default_timezone );
  
    $('.call-datepicker').datepicker({
        maxDate: new Date(get_default_timezone),
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+10',
         beforeShowDay: function(d) {
            setTimeout(function() {
				if($(".ui-state-active").length)  {
					$("td.ui-state-highlight").removeClass("ui-state-highlight");
				}      
				$(document).find('a.ui-state-highlight').removeClass('ui-state-highlight');                    
            }, 10);

            var highlight = eventDates[d];
			if( highlight ) {
				 return [true, "ui-state-highlight", ''];
			} else {                   
				 return [true, '', ''];
			}
		},
        onClose: function (selectedDate) {     }
        
    }).on('change', function () {
        if ($("input[name='search_val_date']").val()) {
            $("input[name='search_val_date']").next('.js-error').remove();
        }
    }); 

});

if ($('#js-model-popup').length) {
    $confModal = $('#js-model-popup');
    var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
    $confModal.on('hidden', function () {
        $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
    });
}

//Authentication popup starts for form submit

county = 0;
$(document).delegate('#js-auth-pop', 'submit', function (e) {
    if (parseInt(county) == parseInt(1)) {    // Here form gets submitted thrice so added condition to avoid it
        return false;
    }
    if($('#authorization_no').val() != '' && $('#js_pos').val() != '' && $('#start_date').val() != '')
    county = county+1;
    if (e.isDefaultPrevented()) { 
    //alert('form is not valid');
    } else {
        e.preventDefault();
        action_url = $(this).attr('action');
        var formData = $('form#js-auth-pop').serialize();
        $("#js_wait_popup").modal("show");
        $.ajax({
            type: 'post',
            url: action_url,
            data: formData,
            success: function (data) {
                if (data != 'error') {
                    var target = api_site_url + "/patients/" + $.trim(data) + "/billing_authorization";
                    $("#auth .modal-body").load(target, function () {
                        insurance_id = $('#js-insurance').val();
                        county = 0;
                        popuploadedfunction(insurance_id);
                        callicheck();
                        if ($('#authorization').val() != '')
                            $('.js-remove-auth-number').show();
                        $("#js_wait_popup").modal("hide");
                    });
					
                } else {
                    if ($("input[name='authorization_no']").val() == '') {
                        $("<span id='auth_no' style='display:block;'><small style='color:#a94442;' class='help-block' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>" + auth_no + "</small></span>").insertAfter($("input[name='authorization_no']"));
                    }
                    if ($('#js_pos').val() == '') {
                        $("<span id='pos_name' style='display:block;'><small style='color:#a94442;' class='help-block' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>" + pos + "</small></span>").insertAfter($('#js_pos'));
                    }
                    $("#js_wait_popup").modal("hide");
                    return false;
                }
            }
        });
        county = county + 1;
    }
    return false;
});

//Authentication popup ends for form submit

function changeselectval(value, model, val, pat_id)
{ 
	category = '';
    if (model != 'Facility') {
        if (value != 'self' && value != 'undefined' && value != 1) {
            valuesplit = value.split('-');
            value = valuesplit[1];
            category = valuesplit[0];
            $('#authorization').removeAttr('readonly');
        } else {
            value = 'self';
            category = '';
            $('#authorization').val("").prop('readonly', true);
        }
    }
    url = (category != '') ? api_site_url + '/patients/getselectbasedvalues/' + value + '/' + model + '/' + category + '/' + pat_id : api_site_url + '/patients/getselectbasedvalues/' + value + '/' + model;
    if (value == 'self') {
        $('input[name="self"]').val(1);
        $('.js-insurance-message').html('');
        $('#authorization').val("");
        $('.js-authpopup').hide();
        $('.js-insurance-popup').hide();
        $('#auth_id').val("");
        $("input[name='insurance_category']").val("");
        $("#js-insurance").val("");
        if(typeof set_val != 'undefined' && set_val ==1) {
          //  f=0;
          //  set_val = 0;
        }       
        return false;
    }
    if (model != 'Facility') {
        $('.js-authpopup').show();
        $('.js-insurance-popup').show();
    }
    if (value != '' && typeof value != 'undefined') {
        $.get(url, function (data) {
            data = data.split('|');
            if (model == 'Facility') {
                if (val == 'frompop') {
                    $('.facility-detail').html(data[1]);
                } else {
                    $('input[name = "facility_clai_no"]').val(data[3]);
					var selPos = data[2];
					if(typeof setPos != "undefined"	&& setPos == 1) {
						if($('#pos_val').length && $('#pos_val').val() != '') {
							selPos = $('#pos_val').val(); 
							setPos = 0;
						}
					}
					if(selPos == 12){
						$('#pos_id').val(selPos).attr("readonly", "readonly").change();
						$('#s2id_pos_id').addClass('select2-container-disabled').css('pointer-events', 'none');
					}else {
						$('#pos_id').val(selPos).change().removeAttr("readonly").change(); //console.log("test else");
						$('#s2id_pos_id').removeClass('select2-container-disabled').css('pointer-events', 'all');
					}
                    $('.js-facility-detail').html(data[1]);
                }
                $('ul.js-facility-detail').closest('.js-dropdown').show();
            } else if (model == 'Attorney') {
                $('ul#js-attorny').html(data[1]);
            } else {
                //$("input[name='insurance_category']").val($("#insurance_id option:selected" ).text());
                $("input[name='insurance_category']").val(category);
                $("#js-insurance").val(value);
                $('.js-insurance-message').html(data[1]);
                $('.js-insurance-popup').show();
                $('.js-authpopup').attr("data-toggle", "modal");
                $('input[name="self"]').val(0);
            }
        });
        if(typeof set_val != 'undefined' && set_val ==1) {
		   // f=0;
		   // set_val = 0;
        }   
    } else {
        if (model == 'Facility') {
            $('#pos_name').val('');
            $('#pos_id').val('').trigger('change');
            $('.js-facility-detail').html('');
            $('.facility-detail').html('');
            $('ul.js-facility-detail').closest('.js-dropdown').hide();
        } else {
            $('.js-insurance-message').html('');
            $('.js-authpopup').attr("data-toggle", '');
            $('.js-insurance-popup').hide();
            $('input[name="self"]').val(0);
        }
        if(typeof set_val != 'undefined' && set_val ==1) {
          //  f=0;
           // set_val = 0;
        }   
    }
}

// Autocolplete search for referring provider and Employer Starts
$('#js-employer').blur(function () {
    if ($(this).val() == '') {
        $(this).siblings('span').css('display', 'none');
        $('ul#js-employer').html('');
    }
});
// Autocomplete search for referring provider and Employer Ends

//Provider info-message popup starts
if(window.location.href.indexOf('patients') > -1 && window.location.href.indexOf('billing') > -1){
    var ij = 1;
} else{
    var ij = 0;
}

function getselecteddetail(id, value, type)
{
    url = api_site_url + '/patients/getproviderdetail/' + value + '/' + type;
    if (value != '') {
        $.get(url, function (result) {
            var providertypeid = result.data.providers.provider_types_id;
            var statelicence = result.data.providers.statelicense;
            var statelicence1 = result.data.providers.statelicense_2;
            var statelicence2 = result.data.providers.specialitylicense;
            var statelicence_new = statelicence;
            var taxanomy1 = result.data.providers.taxanomy;
            var taxanomy2 = result.data.providers.taxanomy2;
            if (statelicence == '') {
                statelicence_new = statelicence1;
            } else if (statelicence_new == '') {
                statelicence_new = statelicence2;
            }
            var provider_degree = (result.data.providers.degrees != '' && result.data.providers.degrees != null && typeof result.data.providers.degrees != "undefined") ? result.data.providers.degrees.degree_name : '';
            var taxanomy;
            if (taxanomy1 != null)
                taxanomy = result.data.providers.taxanomy.code;
            if (taxanomy2 != null)
                taxanomy = result.data.providers.taxanomy2.code;
            var upin = result.data.providers.upin;
            if (type == 'Provider') {
                append_data = "<li><span>Name</span> : " + result.data.providers.provider_name + ' ' + provider_degree + "</li><li><span>NPI</span> : " + result.data.providers.npi + "</li><li><span>Provider Type</span> : " + result.data.providers.provider_types.name + "</li><li><a data-title='More Info' target = '_blank' href=" + api_site_url + "/provider/" + result.data.providers.encoded_id + "><i class='fa fa-info' data-placement='bottom' data-toggle='tooltip' data-original-title='More Details'></i></a></li>";
                $('ul#' + id).html(append_data);
                $('ul#' + id).closest('.js-dropdown').show();
            }
            $("#providertypeid").val(providertypeid);
            if (providertypeid != 1 && providertypeid != 5) {
                $("#statelicence").val(statelicence_new);
                $("#upin_no").val(upin);
            } else if (providertypeid == 5) {
                !(typeof (taxanomy) == 'undefined') ? $("#providertaxanomy").val(taxanomy) : '';
                $("#statelicence_billing").val(statelicence_new);

                var def_facility = result.data.providers.def_facility;
                if((def_facility!=0 && window.location.href.indexOf('newcharge') < -1) || (def_facility!=0 && ij != 0)){
                    $("#facility_id").val(def_facility).change();
                }else{
					/* Billing change facility empty issues fixed */
					/* if($("#facility_id").val()  == ''){
						if(ij!=0)
							$("#facility_id").val('').change();
					} */
					ij++;
                }
            }
        });
    } else {
        $('ul#' + id).html('');
        $('ul#' + id).closest('.js-dropdown').hide();
		/* Billing change facility empty issues fixed */
        //$("#facility_id").val('').change();
    }
}


$(document).delegate('select.js-disable-provider', 'change', function () {
    var statelicence = $("#statelicence").val();
    var upin_no = $("#upin_no").val();
    val = $(this).val();
    if (val == '0B') {
        $('input.js-disable-provider').val(statelicence);
    } else if (val == '1G') {
        $('input.js-disable-provider').val(upin_no);
    } else {
        $('input.js-disable-provider').val('');
    }
    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'provider_qualifier');
});

$(document).delegate('select.js-disable-billing', 'change', function () {
    var taxanomy = $("#providertaxanomy").val();
    var statelicence = $("#statelicence_billing").val();
    val = $(this).val();
    if (val == 'ZZ') {
        $('input.js-disable-billing').val(taxanomy);
    } else if (val == '0B') {
        $('input.js-disable-billing').val(statelicence);
    } else {
        $('input.js-disable-billing').val('');
    }
    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'billing_provider_qualifier');
});

$(document).delegate('select.js-disable-facility', 'change', function () {
    $('input.js-disable-facility').val("");
    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'service_facility_qual');
})

//Provider info-message popup ends
//ICD change Event starts
icd_length = '';
// When charge entry start screen comes default value set of icd length end here
$(document).ready(function () {
    icd_length = $('#js-count-icd input[type="text"]').filter(function () {
        return !!this.value;
    }).length;
});
// When charge entry edit screen comes default value set of icd length end here

/*$(document).delegate('.js-icd', 'blur', function(event){
 var url = api_site_url+'/api/checkicdexist/'+$(this).val();
 var icd_id = $(this).attr('id');
 var tabindex = $(this).attr('data-val'); // Tab index was changes due to avoid tab align issues on charge entry page
 var inc_count = parseInt(1)+parseInt(tabindex);
 var next_icd_id = 'icd'+inc_count;
 var icd_val = $(this).val();
 var name = $(this).attr('name');
 var is_not_valid = 0;
 var obj = { icd1: 1, icd2: 2, icd3: 3, icd4: 4, icd5: 5, icd6: 6, icd7: 7, icd8: 8, icd9: 9, icd10: 10, icd11: 11, icd12: 12 };
 if($(this).val() != '') {
 $.each(obj,function(i, val) {
 if($( "#" + i ).val() != '' && icd_val == $( "#" + i ).val() && i!= icd_id){
 $(".js-display-err").html("<span id='icd_exists-"+icd_id+"' style='display:block;'><small class='help-block med-red js-error-class' data-bv-validator='notEmpty' data-bv-for='"+name+"' data-bv-result='INVALID'>"+icd_exist+"</small></span>");
 $(".js-display-err").siblings('#icd_error-'+icd_id).remove();
 $('#'+icd_id).siblings('#icd_empty').remove();
 //$("#"+icd_id).addClass('errordisplay');
 is_not_valid = 1;
 return false;
 }else{
 
 }
 });
 if(!is_not_valid){
 $.get(url, function(data){
 if(data.statuss == 'error'){
 $("#"+icd_id).addClass('errordisplay');
 $(".js-display-err").html("<span id='icd_error-"+icd_id+"' style='display:none;'><small style='color:#a94442;' class='help-block js-error-class' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>"+invalid_icd+"</small></span>");
 $(".js-display-err").children('#icd_exists-'+icd_id).remove();
 $('#'+icd_id).siblings('#icd_empty').remove();
 $("#icd_imo_search_part").html("");
 $('#imosearch').modal('show');
 $('.js-icd-val-error').html($('#'+icd_id).val());
 $(".js_icd_val").val(icd_id);
 
 } else{
 corrected_icd = data.value.icd.icd_code;
 $(".js-display-err").children('#icd_exists-'+icd_id).remove();
 $(".js-display-err").children('#icd_error-'+icd_id).remove();
 $('#'+icd_id).siblings('#icd_empty').remove();
 $("#"+icd_id).removeClass('errordisplay');
 $.each(obj,function(i, val) {
 if($( "#" + i ).val() != '' && corrected_icd == $( "#" + i ).val() && i!= icd_id){
 $(".js-display-err").html("<span id='icd_exists-"+icd_id+"' style='display:block;'><small class='help-block med-red js-error-class' data-bv-validator='notEmpty' data-bv-for='"+name+"' data-bv-result='INVALID'>"+icd_exist+"</small></span>");
 $(".js-display-err").siblings('#icd_error-'+icd_id).remove();
 $('#'+icd_id).siblings('#icd_empty').remove();
 $("#"+icd_id).addClass('errordisplay');
 is_not_valid = 1;
 return false;
 }else{
 
 }
 });
 setTimeout(function(){
 description = data.value.icd.short_description;
 $('#'+icd_id).attr('title', description);
 $('input#'+icd_id).val(data.value.icd.icd_code);
 $('span#'+icd_id).text(description);
 if(!is_not_valid){
 $('#'+next_icd_id).prop("readonly", false);
 }
 icd_length = $('#js-count-icd input[type="text"]').filter(function () {
 return !!this.value;
 }).length;
 $('.js-append-parent input:not(:disabled):not([readonly])').each(function() {
 // This conditions is used to enable the icd pointer whe each icd was filled
 $("input[id^=icd"+tabindex+"_]").prop("readonly", false);
 
 });
 //$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_icd_validation');
 }, 100);
  }
 });
 }
  } else if($(this).val() == '' &&  !$('#'+icd_id).is('[readonly]')){
 $('#'+icd_id).removeAttr('title');
 //$("#"+icd_id).removeClass('errordisplay');
 $(".js-display-err").children('#icd_exists-'+icd_id).remove();
 $(".js-display-err").children('#icd_error-'+icd_id).remove();
 $('#'+icd_id).siblings('#icd_empty').remove();
 $('span#'+icd_id).text("");
 makeinputdisable(tabindex, 'val');
  }
 checkicdexistonicdpointer(tabindex);
 setTimeout(function(){
 makesubmitbuttondisabled();
 }, 1500);
 }); */
 
is_unique = 0;
icdval_used = [];
$(document).delegate('.js-icd', 'blur', function (event) {
    var url = api_site_url + '/api/checkicdexist/' + $(this).val();
    var icd_id = $(this).attr('id');
    var tabindex = $(this).attr('data-val'); // Tab index was changes due to avoid tab align issues on charge entry page
    var inc_count = parseInt(1) + parseInt(tabindex);
    var next_icd_id = 'icd' + inc_count;
    var is_not_valid = 0;
    var icd_val = $(this).val();
    var checkduplicate_val = checkduplicate();
    var obj = {icd1: 1, icd2: 2, icd3: 3, icd4: 4, icd5: 5, icd6: 6, icd7: 7, icd8: 8, icd9: 9, icd10: 10, icd11: 11, icd12: 12};
    if ($(this).val() != '') {
        // Unique value checking in ICD starts here
        if (checkduplicate_val.length) {
            if ($('#imosearch').hasClass('in')) {
                //
            } else {
                js_sidebar_notification('error',"Diagnosis code already exists. Please enter a new code."); 
               // js_alert_popup("ICD already exists");
            }
            return false;
        }
        // Unique value checking in ICD ends here
        if (!is_not_valid) {
            $.get(url, function (data) {
                if (data.statuss == 'error') {
                    //$("#icd_imo_search_part").html("");
                    //$('#imosearch').modal('show');
                    $('input[name="search_icd_keyword"]').val("");
                    $('.js-icd-val-error').html($('#' + icd_id).val());
                    $(".js_icd_val").val(icd_id);
                    if ($('#imosearch').hasClass('in')) {

                    } else {
                        //js_alert_popup(invalid_icd);
                    }
                    //$(".js-display-err").html("<span id='icd_error-"+icd_id+"'><small style='color:#a94442;' class='help-block js-error-class' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>"+invalid_icd+"</small></span>");
                    $('#' + icd_id).attr("data-icdval", "1");
                    $('span#' + icd_id).html("");
                    //$('#'+icd_id).addClass("erroricddisplay");
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_icd_validation');
                } else {
                    setTimeout(function () {
                        $(".js-display-err").children('#icd_error-' + icd_id).remove();
                        $('#' + icd_id).removeClass("erroricddisplay");
                        description = data.value.icd.medium_description;
                        age_upper = data.value.icd.age_limit_upper;
                        age_lower = data.value.icd.age_limit_lower;
                        patient_age = $('input[name="patient_age"]').val();
                        if (parseInt(age_lower) <= parseInt(patient_age) && parseInt(patient_age) <= parseInt(age_upper)) {
							
                        } else if (age_lower != '' && age_upper != '') {
                            js_alert_popup("Your patient age was mismatched");
                        }
                        //$('#'+icd_id).attr('title', description);
                        $('input#' + icd_id).val(data.value.icd.icd_code);

                        $('span#' + icd_id).text(upperCaseFirstLetter(lowerCaseAllWordsExceptFirstLetters(description)));
                        if (!is_not_valid) {
                            $('#' + next_icd_id).prop("readonly", false);
                        }
                        icd_length = $('#js-count-icd input[type="text"]').filter(function () {
                            return !!this.value;
                        }).length;
                        $('.js-append-parent input:not(:disabled):not([readonly])').each(function () {
                            // This conditions is used to enable the icd pointer whe each icd was filled
                            $("input[id^=icd" + tabindex + "_]").prop("readonly", false);
                        });
                        $('#' + icd_id).attr("data-icdval", "0");
                        setTimeout(function () {
                            $('#' + icd_id).change();
                        }, 50);
                        //$('#patientnote_model').modal('hide');
                    }, 100);
                }
            });
        }
    } else if ($(this).val() == '' && !$('#' + icd_id).is('[readonly]')) {
        $(".js-display-err").children('#icd_error-' + icd_id).remove();
        $('#' + icd_id).removeClass("erroricddisplay");
        $('#' + icd_id).removeAttr('title');
        $('span#' + icd_id).text("");
    }
    checkicdexistonicdpointer(tabindex);
    setTimeout(function () {
        makesubmitbuttondisabled();
    }, 1500);
});

// For ICD description we use the below convert from uppercase to lower case starts
function upperCaseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function lowerCaseAllWordsExceptFirstLetters(string) {
    return string.replace(/\w\S*/g, function (word) {
        return word.charAt(0) + word.slice(1).toLowerCase();
    });
}
// For ICD description we use the below convert from uppercase to lower case ends
//ICD change Event ends

// This function is used to check whether the icd pointer exist starts here
function checkicdexistonicdpointer(icd_val)
{
    var icdid_val = $('#icd' + icd_val).val();
    $('.js-append-parent .icd_pointer').each(function () {
        if (($(this).val() != '' || typeof $(this).val() != 'undefined') && $(this).val() == icd_val && icdid_val == '') {
            //$(this).addClass("erroricddisplay");
            $(this).blur().change();
        } else if ($(this).hasClass('erroricddisplay') && icdid_val != '' && $(this).val() == icd_val) {
            //$(this).removeClass("erroricddisplay");
            $(this).blur().change();
        }
    });
}

// This function is used to check whether the icd pointer exist ends here
function makesubmitbuttondisabled()
{
    if ($('.js-error-class').length) {
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
    } else {
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
    }
}

$(document).on('keyup', 'input[name="other_reason"]', function (){
    $('input[name="hold_reason_exist"]').val(0);  // For hold reason add new it was added
});

/*
 function makeinputdisable(count, val){
 for ( var i = count; i <= 12; i++ ) {
 if($('#icd'+i).val()){
 if(!($('#icd'+count).siblings('#icd_empty').length)){
 $("<span id='icd_empty' style='display:block;'><small style='color:#a94442;' class='help-block js-error-class' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>"+icd1+"</small></span>").insertAfter("#icd"+count);
 }
 }else if($('#icd'+i).val() == ''){
 for ( var k = parseInt(count)-parseInt(1); k >= 1; k-- ) {
 if($('#icd'+k).val() == '' && $('#icd'+k).siblings('#icd_empty').length){
 $('#icd'+k).siblings('#icd_empty').remove();
 }
 }
 }
 }
 }*/
 
function removemodifier(modfier_remove_data, dataid) {
    $("#" + modfier_remove_data).find('.js-modifier').val("");
    cpt_value = $('input[name="cpt['+dataid+']"]').val();    
    if(cpt_value =='' || typeof cpt_value =='undefined') {
        $('input[name="unit['+dataid+']"]').val("");
    }
}

$(document).delegate('.js-cpt:not([readonly])', 'change', function () { 
	if($('input[name="icd_autopopulate"]').val() == 'Yes'){
		var icdCount = parseInt($(this).attr("id"));
		var elementCount = 1;
		$('.js-icd').each(function(){
			if($(this).val() != ''){ 
				$('input[name=icd'+elementCount+"_"+icdCount+']').val(elementCount);
				getcpticdmappingICDAutopopulate(icdCount);
				elementCount++;
			}
		});
	}

	if(localStorage.getItem("data") != 'undefined' && localStorage.getItem("data") != '')
		var Mcpt = JSON.parse(localStorage.getItem("data"));
	else
		var Mcpt = {};
	if($('#insurance_id').val() != 'self' && typeof $('#insurance_id').val() != 'undefined'){
		var insurance = $('#insurance_id').val().split('-');
		var insurance_id = insurance[1];
	} else {
		var insurance_id = 0 ;
	}
	var year = $('input[name="dos_from['+$(this).attr('id')+']"]').val().split('/');
	var chargeYear = year[2];
	var chargeInsurance = insurance_id;
	var Mcptlists = McptBilledArr = '';
	$.each(Mcpt, function(y, year) {
		if(y == chargeYear){
			$.each(year, function(i, ins) {
				if(i == chargeInsurance){
					Mcptlists = ins.cpt_lists;
					McptBilledArr = ins.cpt_billed_arr;
				}else if(i == 0){
					Mcptlists = ins.cpt_lists;
					McptBilledArr = ins.cpt_billed_arr;
				}
			})
		}
	})
	var cpt_arr = Mcptlists;
	var cpt_billed_arr = JSON.stringify(McptBilledArr);
	cpt_val = $(this).val().toUpperCase();
	check_exist_cpt = $.inArray(cpt_val, cpt_arr);
	if(check_exist_cpt == -1)
		cpt_arr = '';
    reg = /^[a-z\d\-_\s]+$/i;
    var val = $(this).val();
    var check = reg.test(val);
    var id = $(this).attr('id');
    // Remove tabindex for based on ICD
    
    var j = 1;
    $('.js-icd').each(function () {
        if ($(this).val() != '') {
            $('#icd' + j + '_' + id).removeAttr('tabindex');
            j++;
        }
    });
    select = $(this);
    modfier_remove_data = $(this).parents(".js-validate-lineitem").attr("id");
    removemodifier(modfier_remove_data, id);  
	if(cpt_arr == ''){
		var cpt_arr_list = localStorage.getItem("cpt_lists");
		var cpt_billed_arr = localStorage.getItem("cpt_billed_arr");
		cpt_arr = JSON.parse(cpt_arr_list);
	}
    var cpt_mod_arr_list = localStorage.getItem("cpt_mod_arr");
		cpt_billed_arr = JSON.parse(cpt_billed_arr);
		cpt_billed_arr = cpt_billed_arr; // Get billed amount and anesthesia  
    if (check) {
        var url = api_site_url + '/checkcptexist/' + $(this).val();   
        var unit = $('input[name= "unit[' + id + ']"]').val();
        cpt_val = $(this).val().toUpperCase();
        cpt_val = cpt_val.replace(/\s/g, '');
        check_exist_cpt = $.inArray(cpt_val, cpt_arr);
        if (cpt_mod_arr_list != '')
            cpt_mod_arr_list = JSON.parse(cpt_mod_arr_list);
        if ($(this).val() != '') {
            //$.get(url, function(data){
            $("input[name='unit[" + id + "]']").val(1);
            if (check_exist_cpt != -1) {
                if ($('input[name= "unit[' + id + ']"]').val() == '')
                    $("input[name='unit[" + id + "]']").val(1);

                unit = $('input[name= "unit[' + id + ']"]').val();
                //var amt = data.value.cpt.billed_amount;
                //var amt_allowed = data.value.cpt.allowed_amount;
                var value = cpt_arr[check_exist_cpt];
                var mod = cpt_mod_arr_list[value];
                var amt = isNaN(cpt_billed_arr[value]) ? 0.00 : cpt_billed_arr[value];
                //console.log("amount"+amt);
                var amt_allowed = amt;
                $('.cpt_amt_' + id).val(parseFloat(amt).toFixed(2));
                $('.cpt_allowed_amt_' + id).val(parseFloat(amt_allowed).toFixed(2));
                if (unit != '') {
                    $("input[id = charge_" + id + "]").val(parseFloat(amt).toFixed(2));
                    var total = 0;
                    setTimeout(function () {
                        $('.js-charge').each(function () {
                            total += Number($(this).val());
                        });
                        $('.js-total').val(parseFloat(total).toFixed(2));
                        $('#cpt-error').remove();
                        next_sel = select.closest('li');
                        next_sel.next('li').find(':input').prop("disabled", false);
                        //make other list as dispable false
                    }, 300);
                }
                visible_li = $('.js-append-parent li:visible').length;
                if (id == 5 && visible_li < 10) {
                    $('.append').show();
                }
                $("input[name='cpt[" + id + "]']").val(value);
                $("#icd1_" + id).removeAttr('tabindex');
                $('#icd1_' + id).attr('readonly', false);
                //if($('#icd1').val() != '')
                //$('#icd1_'+id).val(1).blur();
                // Identifies the modifier for cpts starts here
                get_url = api_site_url + '/getcptmodifier/' + value+'/'+chargeYear+"/"+chargeInsurance;
                $.get(get_url, function (data) {
                    data_val = JSON.parse(data);
                    refering_provider = data_val.refering_provider;
                    var anesthesia = data_val.anesthesia_unit;
                    var descriptioncpt = data_val.short_description;
                    var ndc_number = data_val.ndc_number;
                    var unit_code = data_val.unit_code;
                    var unit_cpt = data_val.unit_cpt;
                    var unit_ndc = data_val.unit_ndc;
                    var unit_value = data_val.unit_value;
                    if(unit_value==0 || unit_value == null || unit_value == 'NULL' )
                        unit_value = '';
                    if(unit_code=='UN'){
                        $("#js_box_24_"+id+" input").val('N4'+ndc_number+' '+unit_code+unit_cpt+' '+unit_value);
                    }else if(unit_code !== 'UN' && unit_ndc !== '' && unit_ndc !== null && unit_ndc !== 'NULL' ){
                        $("#js_box_24_"+id+" input").val('N4'+ndc_number+' UN'+unit_cpt+unit_code+unit_ndc+' '+unit_value);
                    } else if(ndc_number!='') {
                        $("#js_box_24_"+id+" input").val('N4'+ndc_number+' '+unit_code+unit_cpt+' '+unit_value);
                    }
                    $('span#cpt-' + id).text(upperCaseFirstLetter(lowerCaseAllWordsExceptFirstLetters(descriptioncpt)));
                    if (unit == '' && anesthesia == '' || anesthesia == 'undefined') {
                        $("input[name='unit[" + id + "]']").val(1);
                        select.removeAttr('data-anesthesia');
                    } else if (anesthesia != '') {
                        select.attr('data-anesthesia', anesthesia);
                        $("input[name='unit[" + id + "]']").val(anesthesia);
                    }
                    if (!$.isEmptyObject(data_val.modifier_code) && typeof data_val.modifier_code != 'undefined') {
                        $.each(data_val.modifier_code, function (key, value) {
                            id_val = parseInt(key) + parseInt(1);
                            $('#modifier' + id_val + '-' + id).attr('readonly', false).val(value).change();
                        });
                    } else if ($('#modifier1-' + id).val() == '') {
                        $('#modifier1-' + id).attr('readonly', false).val(mod).change();
                    }
                    referingprovidercount(refering_provider, id);
                });
                // Identifies the modifier for cpts ends here
                // Make validation for charge fields
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt');
            } else {
                $(this).addClass('cpt-error');
                $('span#cpt-' + id).hide();
                referingprovidercount("No", id);
                $("input[name='cpt[" + id + "]']").val('');
                $("input[name='unit[" + id + "]']").val("");
                js_alert_popup(invalid_cpt_msg);
            }
            //});
        } else if ($(this).val() == '') {
            if (unit != '') {
                var subtract_amt = $("input[id = charge_" + id + "]").val();
                $("input[id = charge_" + id + "]").val('');
                $('.js-total').val(parseInt($('.js-total').val()) - parseInt(subtract_amt));
            }
            referingprovidercount("No", id);
            $('span#cpt-' + id).hide();
        }
    } else if ($(this).val() != '') {
        $(this).val("");
        referingprovidercount("No", id);
        js_alert_popup(invalid_cpt_msg);
       $("input[name='unit[" + id + "]']").val("");
        $('span#cpt-' + id).hide();
    } else {
        referingprovidercount("No", id);
        $("input[id = charge_" + id + "]").val('');
        $('span#cpt-' + id).hide();
    }
    $("input[id = charge_" + id + "]").change();
    validatelineitem(id);
});

function referingprovidercount(status, id) {
    status_val = (status == "Yes") ? 1 : 0;
    $('#refering-' + id).val(status_val);
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'refering_provider');
}

$(document).ready(function () {
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
            $(this).nextAll('input').first().attr('readonly', false);
            $(this).parent().next().find('input').attr('readonly', false);
            $(this).removeAttr('tabindex');
        } else {
            data = $(this).attr('id');
            data = data.split('_');
            if($('input[name="cpt['+data[1]+']"').val() != ''){
                 $(this).attr('readonly', false);
            }            
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
});

$(document).delegate('.js-icd', 'mouseenter', function () {
    id = $(this).attr('id');
    if ($(this).val() != '' && $('span#' + id).text() != '') {
        $('span#' + id).show();
    } else {
        return false;
    }
});

$(document).delegate('.js-icd', 'mouseleave', function () {
    id = $(this).attr('id');
    if ($(this).val() != '' && $('span#' + id).text() != '') {
        $('span#' + id).hide();
    } else {
        return false;
    }
});

// CPT Hover functionality
$(document).on('mouseenter', '.js-cpt, .js-ed-cpt', function () {
    id = $(this).attr('id');
    if ($(this).val() != '' && $('span#cpt-' + id).text() != '') {
        $('span#cpt-' + id).show();
    } else {
        return false;
    }
});

$(document).on('mouseleave', '.js-cpt, .js-ed-cpt', function () {
    id = $(this).attr('id');
    if ($(this).val() != '' && $('span#cpt-' + id).text() != '') {
        $('span#cpt-' + id).hide();
    } else {
        return false;
    }
});

calltimepicker();
function calltimepicker() {
    $(".timepicker1").timepicker({
        showInputs: false,
        defaultTime: false,
    }).on('changeTime.timepicker', function (e) {
        calculateTime();
    }).on('hide.timepicker', function (e) {
        calculateTime();
    });
}

function humanReadableToMinutes(time) {
    var parts = time.split(/ |:/);
    return (parts[2] == 'PM' * 12 * 60) + parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
}

function calculateTime() {
    var day = '1/1/1970 ';
    var day2 = '2/1/1970 ';
    // 1st January 1970
    start = $('#anesthesia_start').val(); //eg "09:20 PM"
    end = $('#anesthesia_stop').val(); //eg "10:00 PM"
    
    var newStart = start.split(' ');
    var newEnd = end.split(' ');
    if((newStart[1] == newEnd[1])){
        var starttime = new Date("April 14, 2014 "+start);
        var endtime = new Date("April 14, 2014 "+end);
        diff = (endtime - starttime) / 1000 / 60;
    } else if(newStart[1] == "AM" && newEnd[1] == "PM" ) {
        var starttime = new Date("April 14, 2014 "+start);
        var endtime = new Date("April 14, 2014 "+end);
        diff = (endtime - starttime) / 1000 / 60;
    } else {
        var starttime = new Date("April 14, 2014 "+start);
        var endtime = new Date("April 15, 2014 "+end);
        diff = (endtime - starttime) / 1000 / 60;
    }
    
    if (diff != 0 && end != '' && start != '' && $('#anesthesia_start').parents('.bootstrap-timepicker').hasClass('has-success') && $('#anesthesia_stop').parents('.bootstrap-timepicker').hasClass('has-success')) {
        $('input[name="anesthesia_minute"]').val(Math.abs(diff));
        anesthesia_unit = diff / 15;
        anesthesia_unit = Math.floor(anesthesia_unit);
        remaining_muinute = diff % 15
        if (remaining_muinute >= 10) {
            anesthesia_unit = parseInt(anesthesia_unit) + parseInt(1);
        }
        if (diff != 0)
            $('input[name="anesthesia_unit"]').val(Math.abs(anesthesia_unit));
    } else if ($('#anesthesia_start').parents('.bootstrap-timepicker').hasClass('.has-error') || $('#anesthesia_stop').parents('.bootstrap-timepicker').hasClass('has-error') || end == '' || start == '') {     // When we remove the anesthesia  unit the minutes has been changed
        $('input[name="anesthesia_minute"]').val("");
        $('input[name="anesthesia_unit"]').val("");
    } 
}

$(document).delegate('.icd_pointer', 'keypress', function (e) {
    var id = $(this).attr('id');
    var id_val = id.split('_');
    $(this).nextAll('input').first().removeAttr('tabindex');

    if ($(this).val() != '') {
        $(this).parent().next().find('input').removeAttr('tabindex');
    }
    if (($(this).val()).length == 2) {
        //$(this).parent().next().find('input').focus();
    }
    if (($(this).val()).length >= 2 && parseInt($(this).val()) > 12) {
        $(this).val("");
        js_alert_popup(invalid_icd_msg);
        setTimeout(function () {
            validatelineitem(id_val[1], id);
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'icd_pointer');
        }, 400);
    }
});

$(document).delegate('.copay_applied', 'keypress blur change', function (e) {
    amt = $(this).val();
    max  = 10;
    // copay_apply = 0;
    if (this.value.length >=max || amt >= 1000000) {
        $(this).val("0.00").change();       
        js_alert_popup("Invalid units. Billed charges cannot be greater than or equal to $1,000,000.");
       // e.preventDefault();
    }
    if ((isNaN($(this).val()) || $(this).val() < 0) && $(this).val() != '') {        
        js_alert_popup(numeric_err_msg);
        $(this).val("0.00").change(); 
    }  
    // console.log("copay_applied change");
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay_applied');
});

$(document).delegate('.icd_pointer', 'blur', function () {
    var value = $(this).val();
    var id = $(this).attr('id');
    var id_val = id.split('_');
    if (value <= 12) {
        var exis_arr = [];
        $(this).parent().siblings().find("input[name^=icd]").each(function () {
            exis_arr.push(parseInt($(this).val()));
        });
        var icd_val = $('#icd' + value + ':not(.errordisplay)').val(); // Do not allow error displayed icd at adding in icd pointers.
        if (typeof icd_val !== 'undefined' && icd_val !== '' && $.inArray(parseInt(value), exis_arr) == -1) {
            //$('#icd'+value).addClass("highlight");
            get_icd_poision = id_val[0].slice(3);     // To check the the maximum icd enabled
            if (get_icd_poision < icd_length)
                $('#' + id).parent().next().find('input').attr('readonly', false).removeAttr('tabindex');
            getcpticdmapping(id_val[1]);
        } else {
            highlightcheckboxpointer(id_val[1]);
            //$('#icd'+value).removeClass("highlight");
            $('#' + id).val('');
            getcpticdmapping(id_val[1]);
        }
    } else {
        $(this).val("");
    }
    setTimeout(function () {
        validatelineitem(id_val[1]);
    }, 50);    
});

var insert_val = '';
var cpt_icd_val = '';
function getcpticdmapping(id_val) {
    final_val = [];
    final_icd_val = [];
    $('.js-disable-div-' + id_val + ' .icd_pointer').each(function () {
        var map_val = $(this).val(); // console.log(map_val);
        var icd_val = '';
        if (map_val != '') {
            final_val.push(map_val);
            icd_val = $('#icd' + map_val).val();
            final_icd_val.push(icd_val);
        }
        $("input[name='cpt_icd_map_key[" + id_val + "]']").val(final_val);
        $("input[name='cpt_icd_map[" + id_val + "]']").val(final_icd_val);
    });
}

$(document).delegate('.cpt_unit', 'change keyup', function () {    
    cpt_code = $('input[name ="cpt[' + $(this).attr('id') + ']"]').val();

    if (($(this).val() != '') && cpt_code == '' || typeof cpt_code == "undefined") {
        $(this).val("");
    } else if ($(this).val() !== '') {
        cpt_amt = $('.cpt_amt_' + $(this).attr('id')).val();
        if (cpt_amt != 'undefined' || cpt_amt == 0) {
            //$("input[id = charge_"+$(this).attr('id')+"]").val(""); // If there is no fee schedule amount was available empty the change value
        }
        var unit = $(this).val();
        if (isNaN(unit) || unit == 0) {
            //js_alert_popup(numeric_err_msg);
            $(this).val(1);
            return false;
        }
        if (parseFloat(cpt_amt) != '0.00' && cpt_amt != '') {
            cpt_amt = $(".cpt_amt_" + $(this).attr('id')).val();
            charge_amt = unit * cpt_amt;
        } else {
            charge_amt = $("input[id = charge_" + $(this).attr('id') + "]").val();
        }

        $("input[id = charge_" + $(this).attr('id') + "]").val(parseFloat(charge_amt).toFixed(2));
        var total = 0;
        setTimeout(function () {
            $('.js-charge').each(function () {
                total += Number($(this).val());
            });
            $('.js-total').val(parseFloat(total).toFixed(2));
        }, 200);

    } else if(($(this).val() == '') && cpt_code != '' && typeof cpt_code != "undefined") {        
        $(this).val(1);
    }
});

function highlightcheckboxpointer(id) {
    $('.js-icd').removeClass('highlight');
    $('.js-icd-highlight').each(function () {
        $('.js-disable-div-' + id + ' .icd_pointer').each(function () {
            var $this = $(this);
            //$('#icd'+$this.val()).addClass('highlight');
        });
    });
}

function highlightcheckbox() {
    $('.js-icd').removeClass('highlight');
    $('.js-icd-highlight').each(function () {
        if ($(this).is(":checked")) {
            var id = $(this).attr('id');
            $('.js-disable-div-' + id + ' .icd_pointer').each(function () {
                var $this = $(this);
                $('#icd' + $this.val()).addClass('highlight');
            });
        }
    });
}

//Checkbox hightlight
$(document).delegate('.js-icd-highlight', 'ifToggled click', function () {
    highlightcheckbox();
    /*$('.js-icd-highlight').each(function(){
     if($(this).is(':checked')){
     var id =  $(this).attr('id');
     console.log("id value"+id);
     highlightcheckbox(id);
     }
     });
     if($(this).is(':checked')){
     $('.js-icd-highlight').not($(this)).prop('checked', false).iCheck('update');
     }*/
});

//append more rows
$(document).delegate('.append', 'click', function () {
    visible_li = $('.js-append-parent li:visible').length;
    if (visible_li == 9) {      // After 10 line items hide the append link
        //$(this).hide();
    }
    $(".js-append-parent li:hidden:first").show("fast");
});

$(document).delegate('.js-submit-popupform', 'submit', function (e) { 
    if (e.isDefaultPrevented()) {
        //alert('form is not valid');
    } else {
        if ($('#js-claim-id').val() != '')
            $('.js-popclaim_id').val($('#js-claim-id').val());
        var formData = $('form.js-submit-popupform').serialize();
        id = $(this).attr('form-data');
        $('#ajx-loader').html('<img src="' + api_site_url + '/img/ajax-loader.gif">');
        action_url = $(this).attr("action");
        $.ajax({
            type: 'post',
            url: action_url,
            data: formData,
            success: function (data) {
                data = $.parseJSON(data);
                status = data.status;
                msg = data.message;
                data = data.data;
                if (status != 'failiur' && status != 'error') {
                    $('input.' + id).val(data);
                    $('a.' + id).attr('data-url', api_site_url + '/patients/' + id + '/' + data + '/edit');
                    $('a.' + id).attr('data-page', 'edit');
                    $('#ajx-loader').html('');
                    js_sidebar_notification('success',msg);
                    $('#js-model-popup').modal('hide');
                    return false;
                } else {
					js_sidebar_notification('error',msg);
                    return false;
                }
            }
        });
        return false;
    }
});

// Provider and employer ajax form submission
function modelformsubmit() {
    $('#ModelForm').on('submit', function (e) {
        if (e.isDefaultPrevented()) {
            //alert('form is not valid');
        } else {
            e.preventDefault();
            action_url = $(this).attr('action');
            var formData = $('form#ModelForm').serialize();
            $("#js_wait_popup").modal("show");
            $.ajax({
                type: 'post',
                url: action_url,
                data: formData,
                success: function (data) {
                    var msg = data.message
                    if (data.status != 'error' && data.status != '' && typeof (data.status) != 'undefined') {
                        $('#js-model-popup').modal('hide');
                        if (msg != '' && typeof msg != 'undefined') {
                            js_alert_popup(msg);
                        } else {
                            js_alert_popup(succ_msg);
                        }
                        if (typeof (data.data) != 'undefined' && data.data !== null) {
                            provider_id = data.data.provider_data.providerid;
                            Provider_name = data.data.provider_data.providername;
                            $('#refering_provider_id').val(data.data.provider_data.providerid);
                            $('#js-refer-provider').val(Provider_name);
                            getselecteddetail('js-refer-provider', provider_id, 'Provider');
                            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'refering_provider');
                        }
                        $("#js_wait_popup").modal("hide");
                    } else {
                        if (data.message != '' && typeof data.message != 'undefined' && data.data > parseInt(0)) {
                            js_alert_popup(data.message);
                        } else {
                            js_alert_popup("Enter valid NPI");
                        }
                        $("#js_wait_popup").modal("hide");
                    }
                }
            });
            return false;
        }
        return false;
    });
}

// Make All the input as disabled for dos details
$(document).on('change blur', '.js-charge', function (e) {
    id = $(this).attr('id');
    id_val = id.split('_');
    id = id_val[1];
    cpt = $("input[name='cpt[" + id + "]']").val();
    total = 0;
    //amt = parseInt($(this).val(),10);
    amt = $(this).val();
    max  = 10;
    if (this.value.length >=max || amt >= 1000000) {
        $(this).val("0.00").change();
        $("input[name='unit[" + id + "]']").val("");
        js_alert_popup("Invalid units. Billed charges cannot be greater than or equal to $1,000,000.");
        e.preventDefault();
    }
    if ((isNaN($(this).val()) || $(this).val() < 0) && $(this).val() != '') {        
        js_alert_popup(numeric_err_msg);
        amt = 0;
    }

    curr_val = (cpt != '' && typeof cpt != 'undefined') ? (!isNaN(parseFloat(amt).toFixed(2)) && amt != '') ? parseFloat(amt).toFixed(2) : '0.00' : "";
    $(this).val(curr_val);
    $('.js-charge').each(function () {
        total += Number($(this).val());
    });
    $('.js-total').val(parseFloat(total).toFixed(2));
    if ($(this).val() != '' || $(this).val() != 0) {
        $(this).removeClass("js-error-class erroricddisplay");
    }
    validatelineitem(id);
    //$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt');
    // setTimeout(function(){$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_charge_amt');   }, 500);
});

// Make All the input as disabled for dos details
/*$(document).on('change blur', '.copay_applied', function (e) {
    amt = $(this).val();
    max  = 10;
    if (this.value.length >=max || amt >= 1000000) {
        $(this).val("").change();       
        js_alert_popup("Invalid units. Billed charges cannot be greater than or equal to $1,000,000.");
       // e.preventDefault();
    }
    if ((isNaN($(this).val()) || $(this).val() < 0) && $(this).val() != '') {        
        js_alert_popup(numeric_err_msg);
        $(this).val("").change(); 
    }  
      $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay_applied');
    
});*/

$(document).on('keypress', '.js-charge', function (e) {
    max = 10;
    id = $(this).attr('id');
    var amount = amt = parseInt($(this).val(),10);
    
    if (e.which < 0x20) {
        return;     // Do nothing
    }
    if (this.value.length >= max || amount >= 1000000) {
         $(this).blur();
         //$("input[name='unit[" + id + "]']").val("");
         $(this).val("0.00").change();
         js_alert_popup("Billed charges cannot be greater than or equal to $1,000,000.");
         e.preventDefault();
    } else if (this.value.length > max) {
        // Maximum exceeded
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.setSelectionRange(start, end);
        // this.value = this.value.substring(0, max);
    }
});

$(document).delegate('#anesthesia_start, #anesthesia_stop', 'change', function () {
    calculateTime();
});

function callajaxfunction() {
    $('.js-append-parent li').each(function () {
        if ($(this).hasClass('js-disable-div-0')) {
            var date_sel = $(this).find(':input.js_to_date').val();
            if ($(this).find(':input.js-cpt').val() || date_sel != '' && typeof date_sel != 'undefined')
                $(this).find(':input.js-cpt').attr('readonly', false);
        } else {
            var cpt_sel = $(this).find(':input.js-cpt');
            var date_sel = $(this).find(':input.js_to_date').val();
            if ($(this).find(':input.js-cpt').val() || date_sel != '' && typeof date_sel != 'undefined') {
                $(this).find(':input.js-cpt').attr('readonly', false);
                $(this).next("li").addClass('next-li');
            } else {
                if ($(this).hasClass('next-li')) {
                    $(this).removeClass('next-li');
                } else {
                    $(this).find(':input').prop("disabled", true);
                }
            }
        }
        if ($('.js-disable-div-0').find(':input.js-cpt').val())
            $('.js-disable-div-1').find(':input').prop("disabled", false);
    });
    setTimeout(function () {
        total = 0;
        $('.js-charge').each(function () {
            total += Number($(this).val());
        });
        if (total != 0)
            $('.js-total').val(parseFloat(total).toFixed(2));
    }, 200);

    $('input.autocomplete-ajax').each(function (i, el) {
        var ev = $(el).attr('id');
        var id_display = $(el).attr('id');
        var url = $(el).attr('data-url');
        var ev = $('#' + ev);
        typeval = "";
        ev.autocomplete({
            source: function (request, response) {
                query = request.term
                $.get(api_site_url + "/" + url + "/" + query, { }, function (value) {
                    val = value.data.referring_providers;
                    provider_id = Object.keys(val);
                    var arr = Object.keys(val).map(function (k) { //return val[k]
                        return {
                            label: val[k],
                            value: val[k],
                            id: k
                        }
                    });
                    response(arr);
                });
            },
            delay: 0,
			/*
			search: function( event, ui ) {
					console.log("search here");
					$('ul#' + id_display).html('<i class="fa fa-spinner fa-spin"></i>');
			},
			open: function(){	$(this).removeClass('working'); },
			*/
            focus: function (event, ui) {
                this.value = ui.item.label;
                typeval = $.type(this.value);
                if (typeval == 'null' && ev.val() != '') {
                    $('ul#' + id_display).html('');
                    $(this).siblings('span').addClass('js-error-class').css('display', 'block');  // To display the invalid provider message when we type provider name on the input
                } else {
                    if (ev.val() == '') {
                        $('ul#' + id_display).html('');
                        $('ul#' + id_display).closest('.js-dropdown').hide();
                    }
                    $(this).siblings('span').removeClass('js-error-class').css('display', 'none');                    
                    $("input[type='submit']").attr('disabled', false);
                }
                event.preventDefault();
                // or return false;
            },
            change: function (event, ui) {
                if ((typeval == 'null' || typeval == '') && ev.val() != '') {
                    $('ul#' + id_display).html('');
                    $(this).siblings('span').addClass('js-error-class').css('display', 'block');  // To display the invalid provider message when we type provider name on the input
                } else {
                    if (ev.val() == '') {
                        $('ul#' + id_display).html('');
                        $('ul#' + id_display).closest('.js-dropdown').hide();
                    }
                    $(this).siblings('span').removeClass('js-error-class').css('display', 'none');                    
                    $("input[type='submit']").attr('disabled', false);
                }
                typeval = '';
            },
            select: function (event, ui) {
                if (typeof ui.item != 'undefined' && ui.item != '') {
                    var providerid = ui.item.id;
                    providerid = providerid.split(';');
                    providerid = providerid[0];
                }
                if (id_display == 'js-refer-provider') {
                    $('#refering_provider_id').val(providerid);
                    getselecteddetail(id_display, providerid, 'Provider');
                    $('ul#' + id_display).closest('.js-dropdown').show();
                } else if (id_display == 'js-employer') {
                    $('#employer_id').val(ui.item.id);
                    //$('#js-employer').val(ui.item.value);
                    changeselectval(ui.item.id, 'Employer');
                }
            }
        });
    });

    $(document).on('keyup', '#js-refer-provider', function () {
        var refering_provider_val = $(this).val();
        var id = $(this).attr('id');
        if (refering_provider_val == '') {
            $('ul#' + id).html('');
            $('ul#' + id).closest('.js-dropdown').hide();
            $('#refering_provider_id').val("");
        }
    });

    //To make the First icd as not in readonly formate
    $('.js-icd').each(function () {
        /*$(this).attr('readonly', 'readonly');
        if ($(this).attr('id') == 'icd1') {
            $(this).attr('readonly', false);
        }*/
    });
    /* $('.js-modifier').each(function(){
     $(this).attr('readonly', 'readonly');
     });*/
}

//To remove error message from provider and Employer
$(document).delegate('.js-remove-err', 'keyup focusout', function (e) {
    if ($(this).val() == '') {
        $(this).siblings('span').css('display', 'none');
    }
    if (e.type == "focusout") {
        if ($(this).val() == '') {
            $(this).siblings('span').css('display', 'none');
        }
    }
});

date_exist = [];
is_from_admit_date = 0;
exist = '';
function datevalidation(i) {    
    var dateFrom = $("input[name='dos_from[" + i + "]']").val();
    from_selector = $("input[name='dos_from[" + i + "]']");
    to_selector = $("input[name='dos_to[" + i + "]']");
    var dateTo = $("input[name='dos_to[" + i + "]']").val();
    var admitDate = $("input[name='admit_date']").val();
    var dischargeDate = $("input[name='discharge_date']").val();
    var authDate = $("input[name='authentication']").val();
    var doi = $("input[name='doi']").val();
    var patient_id = $("input[name='patient_id']").val();
    var authdate = $("input[name='auth_date']").val();
    var dos = btoa(dateFrom);
    var url = api_site_url + '/api/patients/getexistingdosdetail/' + patient_id + '/' + dos;
    var is_checkedadmit = is_after_doi = is_after_toDate = is_auth_valid = "valid";
    removetabindex(i);
    if (dateFrom != '' && typeof dateFrom != 'undefined') {
        is_future = isFutureDate(dateFrom);
        is_validdate = isValidDate(dateFrom);
		var is_valid_date_format = validDateCheck(dateFrom);
        if (!is_future && is_validdate && is_valid_date_format) {
            from_selector.next('.js-date-err').remove();
            if (admitDate != '' || dischargeDate != '') {
                if (dischargeDate == '' || typeof dischargeDate == "undefined") {
                    is_checkedadmit = checkdateinbetween(admitDate, dateFrom, dischargeDate) ? 'valid' : 'invalidadmit';
                } else {
                    is_checkedadmit = checkdateinbetween(admitDate, dateFrom) ? 'valid' : 'invalidadmit';
                }
            }
            if (doi != '') {
                is_after_doi = checkdateinbetween(doi, dateFrom) ? 'valid' : 'invaliddoi';
            }
            if (dateTo != '' && isValidDate(dateTo)) {
                is_after_toDate = checkdateinbetween(dateFrom, dateTo) ? 'valid' : 'invalidtodate';  // To must be grater here
            }
            if (authdate != '' && isValidDate(authdate)) {
                is_auth_valid = checkdateinbetween(dateFrom, authdate) ? 'valid' : 'invalidauth';  // To must be grater here
            }
            if (is_auth_valid == "invalidauth") {
                js_alert_popup("Selected authentication enddate was Expired");
            }           
            if (is_checkedadmit != 'invalidadmit' && is_after_doi != "invaliddoi" && is_after_toDate != "invalidtodate") {
                $.get(url, function (data) {                   
                    if ($.trim(data) == 'true') {
                        if(is_from_admit_date != 'admit'){
                            exist = $.inArray(dateFrom, date_exist)
                          //  console.log("exist"+exist);
                            date_exist.push(dateFrom) ; 
                        }                                               
						if(exist < 0 && is_from_admit_date != 'admit'){
							$('#js_confirm_box_charges_content').html(dos_cnfm_msg);
							$("#js_confirm_box_charges")
								.modal({show: 'false', keyboard: false})
								.one('click', '.js_modal_confirm1', function (eve) {
									alert_val = $(this).attr('id');
									if (alert_val != 'true') {
										$("input[name='dos_from[" + i + "]']").val("");
										$("input[name='dos_to[" + i + "]']").val("");
										from_selector.focus();
									} else {
										if (dateTo == '' || typeof dateTo == 'undefined' || to_selector.next().hasClass('js-date-err')) {
											$("input[name='dos_to[" + i + "]']").val(dateFrom).change();
											to_selector.next('.js-date-err').remove();
											from_selector.next('.js-date-err').remove();
										}
										$("input[name='cpt[" + i + "]']").attr('readonly', false).focus();
									}
								});

						} else {
							if (dateTo == '' || typeof dateTo == 'undefined' || to_selector.next().hasClass('js-date-err')) {
								$("input[name='dos_to[" + i + "]']").val(dateFrom).change();
								to_selector.next('.js-date-err').remove();
								from_selector.next('.js-date-err').remove();
							}
							$("input[name='cpt[" + i + "]']").attr('readonly', false).focus();
						}
                        
                    } else {
                        if (dateTo == '' || typeof dateTo == 'undefined' || to_selector.next().hasClass('js-date-err')) {
                            to_selector.next('.js-date-err').remove();
                            $("input[name='dos_to[" + i + "]']").val(dateFrom).change();
                        }
                        $("input[name='cpt[" + i + "]']").attr('readonly', false).focus();
                    }
                });
            } else {
                newclass = '';
                from_selector.next('.js-date-err').remove();
                if (is_checkedadmit == "invalidadmit") {
                    error_msg = "Dos must be between admit date and discharge date";
                    newclass = "js-admit-error";
                } else if (is_after_doi == "invaliddoi") {
                    error_msg = "Dos must be after Date of injury date";
                } else if (is_after_toDate == "invalidtodate") {
                    error_msg = "Dos must be before dos to date";
                }
                $('<small class="help-block js-error-class js-date-err ' + newclass + '" data-bv-validator="date" id="date-error" data-bv-result="INVALID" style="display: none;">' + error_msg + '</small>').insertAfter(from_selector);
                js_sidebar_notification('error',error_msg); 
            }
        } else {
            from_selector.next('.js-date-err').remove();
            error_msg = "Should not be the future date";
            if (!is_validdate || !is_valid_date_format)
                error_msg = "Enter valid date";
           $('<small class="help-block js-error-class js-date-err" data-bv-validator="date" id="date-error" data-bv-result="INVALID" style="display: none;">' + error_msg + '</small>').insertAfter(from_selector);
           js_sidebar_notification('error',error_msg); 
        }
    } else if (dateFrom == '' || typeof dateFrom == "undefined" || (dateTo == '' && dateFrom == '') || dateFrom == '') {
        from_selector.next('.js-date-err').remove();
    }
    setTimeout(function () {
        validatelineitem(i);
		enabledisablevalidator('enableFieldValidators', 'admit_date', true);
		enabledisablevalidator('enableFieldValidators', 'discharge_date', true);
    }, 500);
     is_from_admit_date = 0;
}

function checkdateinbetween(dateCheck, dateFrom, dateTo) {
    var d1 = dateFrom.split("/");
    var c = dateCheck.split("/");
    if (dateTo != '' && typeof dateTo != "undefined") {
        var d2 = dateTo.split("/");
        var to = new Date(d2[2], parseInt(d2[0]) - 1, d2[1]);
    }
    var from = new Date(d1[2], parseInt(d1[0]) - 1, d1[1]);  // -1 because months are from 0 to 11
    var check = new Date(c[2], parseInt(c[0]) - 1, c[1]);
    if (dateTo != '' && typeof dateTo != "undefined") {
        return (check >= from && check <= to) ? true : false;
    } else {
        return  (check <= from) ? true : false;
    }
}

function todatevalidation(i) {
    var dateFrom = $("input[name='dos_from[" + i + "]']").val();
    to_selector = $("input[name='dos_to[" + i + "]']");
    from_selector = $("input[name='dos_from[" + i + "]']");
    var dateTo = $("input[name='dos_to[" + i + "]']").val();
    var admitDate = $("input[name='admit_date']").val();
    var dischargeDate = $("input[name='discharge_date']").val();
    var doi = $("input[name='doi']").val();
    removetabindex(i);
    var is_checkedadmit = is_after_doi = is_after_toDate = "valid";
    if (dateTo != '' && typeof dateTo != 'undefined') {
        is_future = isFutureDate(dateTo);
        is_validdate = isValidDate(dateTo);
        var is_valid_date_format = validDateCheck(dateTo);
        if (!is_future && is_validdate && is_valid_date_format) {
            to_selector.next('.js-date-err').remove();
            if (admitDate != '' || dischargeDate != '') {
                if (dischargeDate == '' || typeof dischargeDate == "undefined") {
                    is_checkedadmit = checkdateinbetween(admitDate, dateTo, dischargeDate) ? 'valid' : 'invalidadmit';
                } else {
                    is_checkedadmit = checkdateinbetween(admitDate, dateTo) ? 'valid' : 'invalidadmit';
                }
            }
            if (doi != '') {
                is_after_doi = checkdateinbetween(doi, dateTo) ? 'valid' : 'invaliddoi';
            }
            if (dateTo != '' && isValidDate(dateTo) && dateFrom!='') {
                is_after_toDate = checkdateinbetween(dateFrom, dateTo) ? 'valid' : 'invalidtodate';  // To must be grater here
            }
            if (is_checkedadmit != 'invalidadmit' && is_after_doi != "invaliddoi" && is_after_toDate != "invalidtodate") {
                if (dateFrom == '' || typeof dateFrom == 'undefined' || from_selector.next().hasClass('js-date-err'))
                    $("input[name='dos_from[" + i + "]']").val(dateTo);
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'from_dos');
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'to_dos');
                from_selector.next().remove();
                $("input[name='cpt[" + i + "]']").attr('readonly', false).focus();
            } else {
                newclass = '';
                to_selector.next('.js-date-err').remove();
                if (is_checkedadmit == "invalidadmit") {
                    error_msg = "Dos must be between admit date and discharge date";
                    newclass = "js-admit-error"
                } else if (is_after_doi == "invaliddoi") {
                    error_msg = "Dos must be after Date of injury date";
                } else if (is_after_toDate == "invalidtodate") {
                    error_msg = "Dos must be before dos to date";
                }
               $('<small class="help-block js-error-class js-date-err ' + newclass + '" data-bv-validator="date" id="date-error" data-bv-result="INVALID" style="display: none;">' + error_msg + '</small>').insertAfter(to_selector);
                 js_sidebar_notification('error',error_msg); 
            }
        } else {
            to_selector.next('.js-date-err').remove();
            error_msg = "Should not be the future date";
             if (!is_validdate || !is_valid_date_format)
                error_msg = "Enter valid date";
            js_sidebar_notification('error',error_msg); 
           $('<small class="help-block js-error-class js-date-err" data-bv-validator="date" id="date-error" data-bv-result="INVALID" style="display: none;">' + error_msg + '</small>').insertAfter(to_selector);
        }
    } else if (dateFrom == '' || typeof dateFrom == "undefined" || (dateTo == '' && dateFrom == '') || dateTo == '') {
        to_selector.next('.js-date-err').remove();
    }
    setTimeout(function () {
        validatelineitem(i);
        enabledisablevalidator('enableFieldValidators', 'admit_date', true);
        enabledisablevalidator('enableFieldValidators', 'discharge_date', true);
    }, 50);
}

function isValidDate(s) {
    var bits = s.split('/');
    var d = new Date(bits[2] + '/' + bits[0] + '/' + bits[1]);
    return !!(d && (d.getMonth() + 1) == bits[0] && d.getDate() == Number(bits[1]));
}

function checkforvalidauthorization(type) {
    var authid = $('#auth_id').val();
    var big_date = $('#big_date').val();
    var start_date = $('.js-startdate-' + authid).text();
    var end_date = $('.js-enddate-' + authid).text();
    var is_auth_valid = '';
    $('input[name="auth_date"]').val(end_date);
    if (big_date != '' && end_date != '' && isValidDate(end_date)) {
        var is_auth_valid = checkdateinbetween(big_date, end_date) ? 'valid' : 'invalidauth';  // To must be grater here
        if (is_auth_valid == "invalidauth") {
            js_alert_popup("Selected authentication enddate was Expired");
        } else {
            //
        }
    }
    //val = compareDate(date,end_date);
    //if(!val && end_date != ''){
    //  js_alert_popup("Invalid authorization");
    //}
}

// This is function called when we change the admit date to dos the dos eeor message needs to be removed
function removeerrormessage(date, type) {
    compare_date = [];
    $('.' + type).each(function () {
        if ($(this).val() != '')
            compare_date.push($(this).val());
    });
    var date = new Date(date);
    if (date != 'Invalid Date') {
        for (var i = 0; i <= compare_date.length; i++) {
            var TempDate = new Date(compare_date[i]);
            if (TempDate >= date && typeof compare_date[i] != undefined) {
                $("input[name='dos_from[" + i + "]']").next('.js-admit-error').remove();
                $("input[name='dos_to[" + i + "]']").next('.js-admit-error').remove();
            }
        }
    } else {
        $('.js-admit-error').remove();
    }
}

function removetabindex(i)
{
    $("input[name='cpt[" + i + "]']").removeAttr('tabindex');
}

//Check for future date validation starts
function isFutureDate(idate) {
    var month = idate.substring(0, 2);
    var date = idate.substring(3, 5);
    var year = idate.substring(6, 10);
    var dateToCompare = new Date(year, month - 1, date);
    if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {
        var currentDate = new Date(get_default_timezone);
    }else{
        var currentDate = new Date();
    }
    if (dateToCompare > currentDate) {
        return true;
    } else {
        return false;
    }
}
//Check for future date validation ends

//Make checkbox hold options
$(document).delegate("#hold-option", 'ifToggled click', function (){
    id = $(this).attr('id');
    if ($(this).is(':checked')) {
        removeerrormessagehold();
        $('#js-hold-reason').prop("disabled", false).select2();
        enablefieldvalidation('js-bootstrap-validator', false);
        enabledisablevalidator('enableFieldValidators', 'hold_reason_id', true);
    } else {
        enablefieldvalidation('js-bootstrap-validator', true);
        $('form#js-bootstrap-validator').data('bootstrapValidator').resetForm();
        if ($('#js-hold-reason').val() == '') {
            enabledisablevalidator('enableFieldValidators', 'hold_reason_id', false);
        }
       
        $('#js-hold-reason').prop("disabled", true).select2();
        if ($('#js-hold-reason').val() !== '')
            $("select#js-hold-reason").val("").trigger("change");
        $('.js_common_ins').removeClass('hide');
    }
});

// On edit page when we manually make the check box as checked for hold option icheck not getting reflected
if ($('input[name="is_hold"]:checked').length > 0) {
    setTimeout(function () {
        $('input[name="is_hold"]').prop('checked', true);
    }, 200)
}

$(document).delegate('#js-hold-reason', 'change', function () {
    text = $('#js-hold-reason option:selected').text();
    if (text == 'Add New') {
        enabledisablevalidator('enableFieldValidators', 'other_reason', true);
        $('.hold-option-reason').show();
    } else {
        $('.hold-option-reason').hide();
        enabledisablevalidator('enableFieldValidators', 'other_reason', false);
        $(".js-reason").val('');
    }
});

function enabledisablevalidator(type, field, visibility) {
    $('form#js-bootstrap-validator').bootstrapValidator(type, field, visibility)
    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', field);
}

function enabledisablevalidatorclaimform(type, field, visibility) {
    $('form#ClaimValidate').bootstrapValidator(type, field, visibility)
    $('form#ClaimValidate').bootstrapValidator('revalidateField', field);
}

// Batch No from tab to create page
/*$(".batch_no").on('change', function(){
 $('#'+$(this).attr('name')).val($(this).val());
 }); */
 
$(document).on('click','.js-charge-validate',function(){
	$("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
	$(this).attr("clicked", "true");
});
 

$('#js-bootstrap-validator').on('submit', function (e) { 
    //anesthesiacalculation(); return false;
	var val = $("input[type=submit][clicked=true]").val();
	
	if ($('.js-error-class').length) {
        $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
        $("input[type='submit']").attr('disabled', 'disabled');	
        return false;
    }
	if(val == 'Save and Resubmit'){ 
		var $this = $(this);   
		$.confirm({
			text: 'Charge will move to "Ready" status on Save',
			confirm: function() {			
				$this.off('submit').submit(); // Submit the form 
				return true;
			},
			cancel: function() {
				$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
				$("input[type='submit']").removeAttr('disabled', 'disabled');
				return false;
			}
		});
		return false;
	}
});

$('.js-save-charge').on('click', function (e) {
    e.preventDefault();
    $('#ajax-charge-loader').html('<img src="' + api_site_url + '/img/ajax-loader.gif">');
    var action = $(this).parent().attr('href');
    var BillingformData = $('form#js-bootstrap-validator').serialize();
    $('form#js-bootstrap-validator').bootstrapValidator('validate');
    $.ajax({
        type: "POST",
        url: action,
        data: BillingformData, // serializes the form's elements.
        success: function (data) {
            $('#js-claim-id').val(data); // show response from the php script.
            $('#ajax-charge-loader').html('');
        }
    });
});

// Billing listing page payment details
$(document).delegate('a[data-target=#payment_details], tr[data-target=#payment_details]', 'click', function () {
    var target1 = $(this).attr("data-url");
    var target_id = $(this).attr("id");
    $('.cms-close').attr("data-url", target1);
    $("#payment_details .modal-body").load(target1, function () {
        if (typeof target_id != 'undefined') {
            val = target_id.split("-");
            $('#js-edit-billing').attr('href', api_site_url + "/charges/" + val[1] + '/edit');
        }
    });
});

//CMS 1500 popup open and close
$(document).delegate('a[data-target=#cms]', 'click', function () {
    $("#payment_details").modal('hide');
    var target1 = $(this).attr("data-url");
    $("#cms .modal-body").load(target1, function () {
    });
});

$(document).delegate('.cms-close', 'click', function () {
    var url = $(this).attr('data-url');
    $("#payment_details .modal-body").load(url, function () {
    });
    $("#payment_details").modal('show');
});

$(document).delegate('#anesthesia_start, #anesthesia_stop', 'keydown', function (e) {
    var code = e.keyCode || e.which;
    if (code == 9) {
        $('.bootstrap-timepicker-widget').removeClass("open");
    }
});

// charges page Patient search and Charges related code starts here
$('#js-search-patient').on('click', function () {
    var search_val = $("input[name='search']").val();
    //var search_val = $(this).closest('#js-search-val').val();
    var sel_val = $('#PatientDetail').val();
    $(this).blur();//remove the focus from the button 
    searchpatient(search_val, sel_val);
});

$(document).delegate("input[name='search']", 'blur', function () {
    var search_val = $("input[name='search']").val();
    var sel_val = $('#PatientDetail').val();
    //searchpatient(search_val, sel_val);
});

function searchpatient(search_val, sel_val) { 
    if (search_val == '') {
        if (!$('#search').length) {
            $('<small class="help-block js-error-class" data-bv-validator="search" id="search" data-bv-result="INVALID" style="display: block;">Enter search value</small>').insertAfter($("input[name='search']"));
        }
    }
    if (sel_val == '' && sel_val == 'undefined') {
        if (!$('#err-employer').length) {
            $('<small class="help-block js-error-class" data-bv-validator="err-employer" id="err-employer"" data-bv-result="INVALID" style="display: block;">Select type</small>').insertAfter($('.js-replace-patient-info .dropdown-wrapper'));
        }
    }
    if (sel_val == 'dob') {
        search_val = btoa(search_val);
    }
    if (sel_val == '' || sel_val == 'undefined') {
        sel_val = 'name'
    }
   
    if (search_val != '' && sel_val != '') {
        $("#js_wait_popup").modal("show");
        var target = api_site_url + '/charges/searchpatient/' + sel_val + '/' + search_val;

        $.get(target, function (data) {
            addModalClass();
            append_data = '';
            if (data != '') {
                $.each(data, function (key, data) {
                    dob = '-';
                    if (data.dob != '' && data.dob != '1901-01-01') {
                        dob_date = data.dob;
                        var re = /-/gi;
                        dob_date = dob_date.replace(re, "/");   // Safari issues fixed
                        var date = new Date(dob_date);
                        dob = ("0" + (date.getMonth() + 1)).slice(-2) + '/' + ("0" + date.getDate()).slice(-2) + '/' + date.getFullYear();
                    }
                    append_data += "<tr><td><input type='checkbox'  id='" + data.id + "' class = '' name='charge_patient' value='" + data.id + "'><label for='" + data.id + "' class='no-bottom'>&nbsp;</label></td><td>" + data.account_no + "</td><td>" + data.last_name + ', ' + data.first_name + ' ' + data.middle_name + "</td><td>" + dob + "</td><td>" + data.ssn + "</td></tr>";
                });
            } else {
                append_data = "<tr><td colspan ='5'>No Patient Lists Available</td></tr>";
            }
            var value_append = "<table id='patientList' class='table table-bordered table-striped table-separate'><thead><tr><th></th><th>Acc No</th><th>Patient Name</th><th>DOB</th><th>SSN</th></thead><tbody>" + append_data + "</tbody></table>";
            $("#Patient_list .modal-body").html(value_append);
            callicheck();
            $("#js_wait_popup").modal("hide");
            $('#Patient_list').modal('show');
            $("#session_model").modal("hide");
        });
    }
}

// Patient search from charges page starts here
$(document).delegate('#PatientDetail', 'change', function () {
    var searchobj = $("input[name='search']");
    if (searchobj.hasClass('hasDatepicker'))
        searchobj.datepicker('hide');
    if ($('#PatientDetail').val()) {
        $('.dropdown-wrapper').next('.help-block').remove();
    } else {
        if (!$('#err-employer').length) {
            $('<small class="help-block js-error-class" data-bv-validator="err-employer" id="err-employer" data-bv-result="INVALID" style="display: block;">Select type</small>').insertAfter($('.js-replace-patient-info .dropdown-wrapper'));
        }
    }
    if ($('#PatientDetail').val() == 'dob') {
        searchobj.datepicker({
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+10',
            onClose: function (selectedDate) {this.focus();	}
        }).on('change', function () {
            if (searchobj.val()) {
                searchobj.next('.help-block').remove();
            }
        });
      searchobj.removeClass('js-search-text').addClass('dm-date');
    } else {
        searchobj.removeClass('dm-date').addClass('js-search-text');
        searchobj.datepicker("destroy");
        searchobj.unmask();
       //searchobj.removeClass('dm-date').addClass('js-search-text').removeAttr('id');
    }
     searchobj.val("");
});

//select SearchFilter
function selectSearchFilter(element,searchInput){
     var searchobj = $("#"+searchInput+"");
    if (searchobj.hasClass('hasDatepicker'))
        searchobj.datepicker('hide');
    if (searchobj.val()) {
        $('.dropdown-wrapper').next('.help-block').remove();
    } else {
        if (!$('#err-employer').length) {
            $('<small class="help-block js-error-class" data-bv-validator="err-employer" id="err-employer" data-bv-result="INVALID" style="display: block;">Select type</small>').insertAfter($('.js-replace-patient-info .dropdown-wrapper'));
        }
    }
    if ($(element).val() == 'dob') {
        searchobj.datepicker({
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+10',
            onClose: function (selectedDate) {this.focus();	}
        }).on('change', function () {
            if (searchobj.val()) {
                searchobj.next('.help-block').remove();
            }
        });
		searchobj.removeClass('js-search-text').addClass('dm-date');
    } else {
        searchobj.removeClass('dm-date').addClass('js-search-text');
        searchobj.datepicker("destroy");
        searchobj.unmask();
       //searchobj.removeClass('dm-date').addClass('js-search-text').removeAttr('id');
    }
    searchobj.val("");
}

// Patient search from charges page ends here
$(document).delegate("input[name='search']", 'keyup', function () {
    if ($("input[name='search']").val()) {
        $(this).next('.help-block').remove();
    } else {
        if (!$('#search').length) {
            $('<small class="help-block js-error-class" data-bv-validator="search" id="search" data-bv-result="INVALID" style="display: block;">Enter data</small>').insertAfter($("input[name='search']"));
        }
    }
});

$(document).delegate("input[name='charge_patient']", 'ifToggled click', function () {
    $('#Patient_list').modal('hide');
    $("#js_wait_popup").modal("show");
	setPos = 1;
    var patient_id = $(this).val();
    var charge_url = api_site_url + "/charges/create/" + patient_id;
    // this is for batch process
    var facility_id = $('#facilityy_id').val();
    var rendering_id = $('#rendering_id').val();
    var billing_id = $('#billing_id').val();
    // POS, dos_from, dos_to values bulk insert related chnages
    var pos_id = $('#pos_val').val();
    var dos_from = $('#dos_from_val').val();
    var dos_to = $('#dos_to_val').val();
    var self = $("#test_id").val();
    //var tab_batch_date = $('#tab_batch_date').val();
    var reference = $('#js_reference').val();
	var query_data = $('#js_query').val();
    $.get(charge_url, function (data) {
        $('.js-replace-section').html(data);
        setTimeout(function () {
            patient_notes(patient_id);

            if (rendering_id != 0) {
                $("select#providerpop").val(rendering_id).change();
            }
            if (billing_id != 0) {
                $("select#billingprovider-pop").val(billing_id).change();
            }
            if (facility_id != 0) {
                $("select#facility_id").val(facility_id).change();
            }
            if (pos_id != 0) { 
                $("select#pos_id").val(pos_id).change();
            }

            if(dos_from != '') {
                $('input[name ="dos_from[0]"]').val(dos_from);//.change();
				$('input[name ="cpt[0]"]').removeAttr("readonly");
            }

            if(dos_to != '') {
                $('input[name ="dos_to[0]"]').val(dos_to);//.change();
				$('input[name ="cpt[0]"]').removeAttr("readonly");
            }

            if (self != '' && typeof self != 'undefined') {
                $("select#test_id").val(self).change();
            }
            $("select.select2.form-control").select2();

            $('#insurance_id').change();
            callajaxfunction();
            calltimepicker();
            $("input[name= 'is_from_charge']").val(1);
            //$(".js-entry-date").html(tab_batch_date);
            $('input[name="copay_detail"]').val(reference); 
			$('#jsquery').val(query_data);
            $("input[name= 'is_create']").val(0);
            if ($('.js-dropdown').length) {
                $('.js-dropdown').hide();
            }
            // Add new process dropdown select starts
            if ($("div").hasClass("js-add-new-select")) {
                $("div.js-add-new-select").find('select').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
            }
            /*$('input[type="checkbox"], input[type="radio"]').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });*/
            billingvalidation('js-bootstrap-validator'); // Call billing validation on ajax request.
            enablefieldvalidation('js-bootstrap-validator', true);
            /*$("#search_table").DataTable(
             {
             "paging": true,
             "lengthChange": false,
             "searching": true,
             "responsive": true,
             });*/
            $("#js_wait_popup").modal("hide"); 
            $('.js-search-text').blur(); 
            f = 0;
        }, 50);
		if(dos_from != '' || dos_to != '') {
			$('input[name ="cpt[0]"]').removeAttr("readonly").removeAttr('tabindex');
			// validatelineitem(0);
			datevalidation(0); todatevalidation(0);
		}
    });
});

// Validation for line items starts here (If any one was filled remaining line ithems also needs to be filled)
function validatelineitem(i, icd_id) {
    var from_date = $("input[name='dos_from[" + i + "]']");
    var to_date = $("input[name='dos_to[" + i + "]']");
    var cpt = $("input[name='cpt[" + i + "]']");
    var icd = $("#icd1_" + i);  // Minimun 1 values should be given at icd pointers
    var charge = $("input[name='charge[" + i + "]']");
    var icd_val = '';
    var hold_cheked_len = $("#hold-option").is(':checked');
    var from_date_error_class = from_date.next("small").hasClass("js-date-err");
    var to_date_error_class = to_date.next("small").hasClass("js-date-err");    
    if (typeof icd_id != 'undefined' && icd_id != '') {
        var icdid = $('#' + icd_id);
        var icd_val_id = $('#' + icd_id).val();
        var icd_val = $('#icd' + icd_val_id).val();
        var loop = [from_date, to_date, cpt, charge, icd];
        loop.push(icdid);
    } else {
        var loop = [from_date, to_date, cpt, icd, charge];
    }
    if (from_date.val() == '' && to_date.val() == '' && cpt.val() == '' && icd.val() == '' && (charge.val() == "" || charge.val() == parseFloat(0.00)) && (icd_val == '' || typeof icd_val == 'undefined') || from_date.val() != '' && to_date.val() != '' && cpt.val() != '' && icd.val() != '' && (charge.val() != "" || charge.val() != parseFloat(0.00)) && (icd_val == '' || typeof icd_val == 'undefined')) {
        $.each(loop, function (index, value) {
            value.removeClass("js-error-class erroricddisplay");
        });        
        makesubmitbuttondisabled();
    } else {
        $.each(loop, function (index, value) {
            if ((value.val() == '' || value.val() == parseFloat(0.00)) && !hold_cheked_len) {                
                value.addClass("js-error-class erroricddisplay");
            } else {
                if(value != 'cpt' && value != 'icd' && value != 'charge' && value.next("small").hasClass("js-date-err")) {
					value.addClass("js-error-class erroricddisplay");
				} else{
					value.removeClass("js-error-class erroricddisplay");
				}
            }
            makesubmitbuttondisabled();
        });
    }
    date_val = [];
    $('.js_validate_date').each(function () {
        if ($(this).val() != '')
            date_val.push($(this).val());
    });
     $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'icd_pointer');  
     $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'box_24_AToG[]'); 
    var minDate = GetSmallestDate(date_val);
}

// This function is used to remove the error messages from line item starts here

function removeerrormessagehold() {
    $('.js-append-parent input:not(:disabled):not([readonly])').each(function () {
        if ($(this).hasClass('erroricddisplay')) {
            $(this).removeClass("js-error-class erroricddisplay");
        }
    });
}

// This function is used to remove the error messages from line item  ends here
if ($('#big_date').length) {
    getDateEntry("fromautoload");
}

function getDateEntry(type) {
    date_val = [];
    $('.js_validate_date').each(function () {
        if ($(this).val() != '')
            date_val.push($(this).val());
    });
    var minDate = GetSmallestDate(date_val, type);
}

function GetSmallestDate(DateArray, type) {
    var SmallestDate = new Date(DateArray[0]);
    var BiggestDate = new Date(DateArray[0]);
    for (var i = 1; i < DateArray.length; i++) {
        var TempDate = new Date(DateArray[i]);
        if (TempDate < SmallestDate) {
            SmallestDate = TempDate;
        } else if (SmallestDate < TempDate) {
            BiggestDate = TempDate;
        }
    }
    date_val = parseInt(SmallestDate.getMonth() + parseInt(1)) + '/' + SmallestDate.getDate() + '/' + SmallestDate.getFullYear();
    if (BiggestDate != '' || typeof BiggestDate != 'undefined')
        big_val = parseInt(BiggestDate.getMonth() + parseInt(1)) + '/' + BiggestDate.getDate() + '/' + BiggestDate.getFullYear();
    $("#small_date").val(isValidDate(date_val) ? date_val : '');
    $("#big_date").val(isValidDate(big_val) ? big_val : '');
    admin_date = $("input[name='admit_date']").val();
    if (admin_date != '' && (type == '' || typeof type == 'undefined')) {
        // val = compareDate(admin_date, date_val)
        //enabledisablevalidator('enableFieldValidators', 'admit_date', true);
        //enabledisablevalidator('enableFieldValidators', 'discharge_date', true);
    }
}

function compareDate(admit_date, dos)
{
    admit = new Date(admit_date);
    dos = new Date(dos);
    if (admit_date != '' && dos != '') {
        if (new Date(admit_date) <= new Date(dos)) {
            return true;
        } else {
            return false;
        }
    }
}

// Validation for line items ends here
//Check modifier starts
$(document).delegate('.js-modifier', 'blur', function () {
    modifier = $(this).val();
    sel = $(this).parents('li').attr('id');
    
    var inputs = $('#' + sel + ' .js-modifier');
    url = api_site_url + '/api/checkmodifier/' + modifier;
    name = $(this).attr('name');
    id = $(this).attr('id');
    var idval = id.split('-');
    is_exist = 0;
    modifier = modifier.toUpperCase();
    inputs.not(this).each(function (i) {      // If same modifier code entered
        if ($(this).val() != '' && $(this).val() == modifier) {
            js_alert_popup(exist_err_msg);
            $('input[name = "' + name + '"]').val("");
            is_exist = 1;
            return false;
        }
    });
    //$('#'+id).parent().next().find('input').removeAttr('tabindex');
    next_val = $(this).parent().next().find('input').val();
    check = /^[a-z0-9]+$/i.test(modifier);
    if (!check)
        $('input[name = "' + name + '"]').val("");
    if (modifier != '' && !is_exist && check) {
        check_exist = $.inArray(modifier, modifier_arr);
        if (check_exist != -1) {
            val = modifier_arr[check_exist];

            $('#' + id).parent().next().find('input').attr('readonly', false);
            $('input[name = "' + name + '"]').val(val);
            if ($('input[name = "' + name + '"]').hasClass('js-error-class')) {
                $('input[name = "' + name + '"]').removeClass('js-error-class erroricddisplay');
            }
        } else {
            js_alert_popup(modifier_err_msg);
            $('input[name = "' + name + '"]').val("");
            $('input[name = "' + name + '"]').focus();
        }
    } else if (modifier == '') {
        prev_val = $(this).parent().prev().find('input.js-modifier').val();
        next_val = $(this).parent().next().find('input.js-modifier').val();
        if (next_val != '' && typeof next_val != 'undefined') {
            $(this).addClass("js-error-class erroricddisplay");
        } else {
            $(this).removeClass("js-error-class erroricddisplay");
        }

        if ($(this).parent().prev().find('input.js-modifier').hasClass('js-error-class')) {
            $(this).parent().prev().find('input.js-modifier').removeClass('js-error-class erroricddisplay');
        }
        if (next_val == '' && $(this).parent().prev().find('input.js-modifier').val() == '') {
            $(this).removeClass("js-error-class erroricddisplay");
        }
    }
    makemodifiervalidation(idval[1])
    makesubmitbuttondisabled();
});

function makemodifiervalidation(id){  
	var arr = ['modifier1', 'modifier2', 'modifier3', 'modifier4'];
	$.each(arr, function(i,v){ 
		var previous = $('#'+arr[i-1]+'-'+id);
		var next = $('#'+arr[i+1]+'-'+id).val();
		var mod = $('#'+v+'-'+id).val();
		var iddata = $('#'+v+'-'+id).attr('id');
		if(previous.val() == '' && mod != '' && typeof mod != 'undefined'){
			previous.addClass("js-error-class erroricddisplay");
		}
	})
}

function enablequalifierfield() {
    provider = $('input[name="refering_provider"]').val();
    billing = $("select#billingprovider-pop").val();
    facility = $("select#facility_id").val();
    setTimeout(function () {
        if (provider == '')
            $('.js-disable-provider').attr("disabled", true);
        if (billing == '')
            $('.js-disable-billing').attr("disabled", true);
        if (facility == '')
            $('.js-disable-facility').attr("disabled", true);
    }, 500);
}

$(document).on('click', '.js-table-click-billing', function (e) { 
    var target = $(e.target);	
    if (target.is("a.js-prevent-redirect") || target.is("i.js-prevent-redirect") || target.is("span.js-prevent-redirect") || target.is("div.js-prevent-redirect")) {
        //
    } else {
        var getUrl = $(this).attr('data-url');
        window.location = getUrl;
    }
});

$(document).on('click', '.js-create-claim', function (e) {
    if ($("form#js-batch-submit").length) {
        $("form#js-batch-submit")[0].reset();
        $('select.select2.form-control').select2();
    }
    removeHash();
});

$('.js-create-batch').click(function (e) {
    // Prevent unload confirmation message
    // Rev.1 - Ref: MR-2528 06-08-2019 - Ravi
    window.onbeforeunload = null;
    e.preventDefault();
	var dateErr = 0;
	var errMsg = '';
	var dateFrom = $("#dos_from").val();
    var dateTo = $("#dos_to").val();
	if(dateFrom != '' && dateTo != '') {
		if (dateFrom != '' && typeof dateFrom != 'undefined') {
			var is_future = isFutureDate(dateFrom);
			var is_validdate = isValidDate(dateFrom);
			var is_valid_date_format = validDateCheck(dateFrom);
			
			if (!is_future && is_validdate && is_valid_date_format) {
				// console.log("in from success");
			} else {
				dateErr = 1;
				errMsg = "Invalid DOS From Date."; 
				$('input[name="dos_from"]').focus();
			}
		}
		
		if (dateTo != '' && typeof dateTo != 'undefined') {
			var is_future = isFutureDate(dateTo);
			var is_validdate = isValidDate(dateTo);
			var is_valid_date_format = validDateCheck(dateFrom);
			
			if (!is_future && is_validdate && is_valid_date_format) {
				var is_after_toDate = checkdateinbetween(dateFrom, dateTo) ? 'valid' : 'invalidtodate';  // To must be grater here
				if(is_after_toDate == 'invalidtodate') {
					dateErr = 1;
					errMsg  = "Invalid DOS To Date."; 
					$('input[name="dos_to"]').focus();
				}
			} else {
				dateErr = 1;
				errMsg = "Invalid DOS To Date.";
				$('input[name="dos_to"]').focus();
			}
		}
	}	
	
	if(dateErr != 0) {
		if(errMsg == '')
			errMsg = 'Invalid DOS Date';
		js_sidebar_notification('error',errMsg); 
		return false;
	} else {
		$('#js-batch-submit').submit();	
	}    
});

$(".js-disable").css('pointer-events', 'none').find("input:not(.js_cancel_site)"); // Make charge entry screen as unclickable before patient search
$('#js-charge-cancel').css('pointer-events', 'auto');

function removeErrormsg(sel) {
    sel.next('.help-block').remove();
}

//Charge entry  IMO search starts here
$(document).on('change click', ".js_search_icd_list", function () {
    var search_keyword = $('input[type=text][name="search_icd_keyword"]').val();
    if (search_keyword != '') {
        $('#ajx-loader').html('<i class="fa fa-spinner fa-spin">');
        var sel_icds = $("#selected_codes_ids_arr").val();
        var formData = "search_keyword=" + search_keyword + "&from=icd&search_from=charge&sel_icds=" + sel_icds;
        $.ajax({
            type: 'POST',
            url: api_site_url + '/api/get_superbill_search_icd_cpt_list',
            data: formData,
            success: function (result) {
                $("#icd_imo_search_part").html(result);
                $("#ajx-loader").html("");
                //$('input[type=text][name="search_icd_keyword"]').val('');
            }
        });
    } else {
        $("#icd_imo_search_part").html('');
        $('#search_icd_keyword_err').removeClass('hide');
    }
});

$(document).on('ifToggled change', "input[name='imo_search_icds[]']", function () {
    var formData = "search_value=" + $(this).val() + "&from=icd&sel_from=charge";
    var icdval_used = checkuniqueicd();
    var checked_value = $(this).val();
    checked_value = checked_value.split('::');
    checked_value = checked_value[0];
    checked_value_update = checked_value.replace(/\./g, '_');
    if ($.inArray(checked_value, icdval_used) > -1) {
        js_alert_popup("ICD already chosen");
        $('input[name="imo_search_icds[]"]:checkbox[data-id="icd_' + checked_value_update + '"]').attr("checked", false);
        setTimeout(function () {
            $('input[name="imo_search_icds[]"]:checkbox[data-id="icd_' + checked_value_update + '"]');
        }, 100);
        return false;
    }
    $.ajax({
        type: 'POST',
        url: api_site_url + '/api/select_api_search_icd_cpt_list',
        data: formData,
        success: function (result) {
            icd_id = $('.js_icd_val').val();
            $('#' + icd_id).val(result.icd_code);
            $('#imosearch').modal('hide');
            $('#' + icd_id).blur().change();
        }
    });
});

function checkuniqueicd() {
    var icdval = [];
    $('.js-icd').each(function () {
        icdval.push($(this).val());
    });
    //icdval = icdval.filter(v=>v!='');
    icdval = icdval.filter(function (i, v) {
        return v != '';
    });
    return icdval;
}

function checkduplicate() {
    var values = $('.js-icd').map(function () {
        return this.value.toUpperCase();
    }).toArray();
    var sorted_arr = values.sort();
    var results = [];    
    for (var i = 0; i < values.length - 1; i++) {
        if (sorted_arr[i + 1] == sorted_arr[i]) {
            if (sorted_arr[i] != '')
                results.push(sorted_arr[i]);
        }
    }
    return results;
}

$(document).delegate('.js_search_icd_list', 'keyup', function () {
    if ($('.js_search_icd_list').val() != '') {
        $('#search_icd_keyword_err').addClass('hide');
    } else {
        $("#icd_imo_search_part").html('');
    }
});

$(document).delegate('input[name="copay_amt"]', 'change', function () {
    val = $(this).val();
    if (val != '' && typeof val != "undefined" && !isNaN(val)) {
        $(this).val(parseFloat(val).toFixed(2));
    } else {
        $('.copay_applied').val('');
    }
    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay_applied');
})


/*$(document).on("keyup",".allownumericwithdecimal",function (event) {
 length = $(this).val().length;
 if(length >= 8){
 return false;
 }
 var start = this.selectionStart;
 var end = this.selectionEnd;
 value = $(this).val().replace(/[^0-9.,-]/g, "");
 $(this).val(value);
 this.setSelectionRange(start, end);
 if ((value.indexOf('.') != -1) && (value.substring(value.indexOf('.')).length > 2)) {
 event.preventDefault();
 }
 });*/

$(document).on('keyup', '.js-numeric-val', function () { // For check number only allow numeric values
    var start = this.selectionStart;
    var end = this.selectionEnd;
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    this.setSelectionRange(start, end);
});

$(document).on("change", ".allownumericwithdecimal", function (event) {
    var value;
    val = $(this).val();
    if (val != '') {
        value = (!isNaN(val) && typeof val != 'undefined' && val != '') ? parseFloat(val).toFixed(2) : parseFloat('0.00').toFixed(2);
        $(this).val(value);
    }
});

$(document).on('keypup', '.allownumericwithdecimal', function (event) {
    var i = 0;
    value = $(this).val();
    value = value.replace(/(?!^)-/g, '').replace(/\./g, function (match) {
        return match === "." ? (i === 0 ? '.' : '') : '';
    });
    $(this).val(value);
});

$(document).on('keypress change', '.allownumericwithdecimal', function (event) {
    var $this = $(this);
    if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
            ((event.which < 48 && event.which != 45 || event.which > 57) &&
                    (event.which != 0 && event.which != 8))) {
        event.preventDefault();
    }

    var text = $(this).val();
    if ((event.which == 46) && (text.indexOf('.') == -1)) {
        setTimeout(function () {
            if ($this.val().substring($this.val().indexOf('.')).length > 3) {
                $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
            }
        }, 1);
    }
    var start = $(this)[0].selectionStart;
    var end = $(this)[0].selectionEnd;   
    var difference = end - start;    
    if ((text.indexOf('.') != -1) &&
        (text.substring(text.indexOf('.')).length > 2) &&
        (event.which != 0 && event.which != 8) &&
        (start >= text.length - 2 && difference != 2 && difference != 1)) {            
            event.preventDefault();
    }
});

$("div.js-replace-section .js-disable").on('click keyup keypress blur', function (event) {
    var clicked_element = $(this).attr('class');
    var target = $(event.target);
    if (!target.hasClass('js_cancel_site')) {
        js_alert_popup("Select a patient to create a claim");
        return false;
    }
});

setTimeout(function () {
    var type = $('input[name="payment_type"]').val();
    //if(type != "Refund")
    //changeadjustmentamount();
    calculateamount();
    changelabel();
}, 200);

// To calculate adjustment and total adjustment when page loads starts here
function changeadjustmentamount() {
    tot_adjustment = 0;
    $('li.js-calculate').each(function () {
        i = $(this).attr('id');
        billed_amt = $('input[name="cpt_billed_amt[' + i + ']"]').val();
        allowed_amt = $('input[name="cpt_allowed_amt[' + i + ']"]').val();
        insurance_paid = $('input[name="insurance_paid[' + i + ']"]').val();
        var adjustment = parseFloat(billed_amt) - parseFloat(allowed_amt);
        status = $('input[name="paid_status"]').val();
        tot_adjustment += adjustment;
        if (status != 'Paid') {
            $('input[name="adjustment[' + i + ']"]').val(adjustment.toFixed(2));
        }
        $("span#js-adjust").html(tot_adjustment.toFixed(2));
    });
}
// To calculate adjustment and total adjustment ends here

/*** Extract Insurance related information by changing and onchange and onload ends here***/

// Payment related  code starts here
$(document).on('click ifToggled', '.js-sel-pay', function () {
    payment_type = $('input[name=payment_type]:radio:checked').val();
    if (payment_type == "Credit Balance") {
        setTimeout(function () {
            checkpaytypeanddeselectinput("Credit Balance");
        }, 100);
    }
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false); // To enable the disabled submit button
    getclaimid();
    $('.js-patient-paymentform').bootstrapValidator('disableSubmitButtons', false);
    //Newly added for select all feature starts here
    var checked_length = $('input:checkbox.js-sel-pay:checked').length;
    var total_length = $('input:checkbox.js-sel-pay').length;
    if (checked_length == total_length)
        $('.js_menu_payment').prop('checked', true);
    else
        $('.js_menu_payment').prop('checked', false);
    //Newly added for select all feature ends here
});

function checkpaytypeanddeselectinput(type) {
    credit_balance = $('#payment_credit_balance').val();
    patient_id = $('input[name="patient_id"]').val();
   // console.log("patient id"+patient_id);
    if (typeof credit_balance == 'undefined' || credit_balance == '' || credit_balance == 0 || credit_balance < 0) {
        if (type == "Credit Balance" && patient_id != '' && typeof patient_id != "undefined")
            js_alert_popup("Unavailable wallet balance.");
        $('.js-sel-pay').each(function () {
            $(this).prop('checked', false);
            $('.js_menu_payment').prop('checked', false);
        });
    }
    getclaimid();
}
// Make patient and insurance pop up as enabled


// To enable popup for patient payment and insurance payment starts
function makemodeltoggle() {
    var n = $('.js_payment').find("input:checked").length;
    if (n >= 1) {
        $('.js_addatrr').attr('data-toggle', 'modal');
    } else {
        $('.js_addatrr').removeAttr('data-toggle', 'modal');
    }
}
// To enable popup for patient payment and insurance payment ends

// Get claim ids when we clicks on checkbox from payment listings starts here
$(document).delegate('a[data-target=#patient_posting]', 'click', function () {
    chkd = $('input[name=payment_type]:radio:checked').val();
    idval = getclaimid();
    $("form.paymentpopupform")[0].reset();
    if (chkd == "Credit Balance") {
        tot_amt = $('#payment_credit_balance').val();
        tot_amt = !isNaN(tot_amt) ? tot_amt : 0;
        $('input[name="payment_amt_pop"]').val(tot_amt);
        $('input[name="payment_amt_calc"]').val(tot_amt);
    } else {
        $('.paymentpopupform').not('input[name=payment_type], input[name="payment_amt_pop"],input[name="credit_balance"] ').trigger("reset");
    }
});
// Get claim ids when we clicks on checkbox from payment listings ends here

// Hide and show related data depends upon the payment type at popuppage starts here
$(document).on('ifToggled change', '.js-payment-type', function () {
    //var payment_model = $(this).parents("div.modal").attr("id");
    var value = $('input[name=payment_type]:radio:checked').val(); 
    var pay_mode  = $('#js-payment-mode');  
    pay_mode.attr('disabled', false).select2();
    $('.js-checkdetail-div').show();
    $('.js-hide-adjustment').hide();
    $('.js-show-refund').hide();
    $('.js-amt').text(value);
    $('div.js-payment-mode').show();
    $('.js-payment-amount').show();
    $('.js-hide-creditbalance').show();
    $('.js-addtowallet').hide();
    $('input[name="payment_amt_pop"]').attr("readonly", false);
    // Get previously selected mode to come back again starts here
    if (typeof paymode_val != 'undefined' && value == "Payment")
        $('#js-payment-mode').val(paymode_val).change();
    var payamount = isNaN($('#payamount').val()) ? "" : $('#payamount').val();
    $('input[name= "payment_amt_pop"],input[name= "payment_amt_calc"]').val(payamount);
    $('.js-upload').show();
    if (value == "Payment") {
        paymode_val = $('#js-payment-mode').val();
    }
    // Get previously selected mode to come back again ends here
    $('.js-show-amountbox').hide();
    if (value == "Adjustment" || value == "Credit Balance") {
        if (value == "Adjustment")
            $('.js-payment-amount').hide();
        $('.js-checkdetail-div').hide();
        if (value != "Credit Balance")
            $('.js-hide-adjustment').show();
        $('.js-carddetail-div').hide("fast");
        $('div.js-payment-mode').hide();
        if (value == "Credit Balance") {
            checkpaytypeanddeselectinput(value);
            $('.js-hide-creditbalance').hide();
            getnegativeamount();
        }
        $('.js-upload').hide();
    } else if (value == "Refund") {
        $('.js-show-refund').hide(); //.show();
        $('#js-removewallet').show();
        $('#js-payment-mode').val('Check').trigger('change').prop('disabled', true).select2();
    } else {
        
        $('#js-addwallet').show();
    }
    disableunpaidcheckbox(); // Call checkbox disable function
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);

    // If wallet checkbox was enabled disable it when other than refund was choosen
    $('input[name="wallet_amt"]').attr('checked', false);
    $('form#js-bootstrap-validator').data('bootstrapValidator').resetForm();
    var check_no = $("input[name='check_no']").val();
    if((value == "Payment" || value == "Refund") && pay_mode.val() == "Check" && check_no != '' && typeof check_no != "undefined"){
        $(".js-check-number").trigger("blur");
    }    
    //Validation of amount
    //$('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'payment_amt_pop');
});
// Hide and show related data depends upon the payment type ends here

// negative amount calcumations for credit balance amount starts here
function getnegativeamount() {
    total = 0;
    claims = [];
    total = $('#payment_credit_balance').val();
    total = isNaN(total) ? 0 : Math.abs(total);
    $('input[name="payment_amt_calc"]').val(total);
    $('input[name="payment_amt_pop"]').val(total).prop("readonly", true);
    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'payment_amt_pop');
} 

function getclaimid() {
    var idval = [];
    $('.js_payment').find('input:not(:radio):not(.js_menu_payment):checked').each(function () {
        id = $(this).attr('id');
        (id != '' && typeof id != 'undefined' && id != 'pat-checkall') ? idval.push(id) : "";
    });

    setTimeout(function () {
        $('input[name="claim_ids"]').val(idval);
    }, 100);
    return idval;
}

function getinsuranceid(insurance_id)
{
    var insidval = [];
    var datainsid = [];
    var insid = ($('#js-insurance-id').length) ? $('#js-insurance-id').val() : "";    
    if (insid != '' && insid.indexOf('-') != -1) {
        insurance_split = insid.split('-');
        insu_id = insurance_split[1];
    } else {
        insu_id = insurance_id;
    }
    $('.js_payment').find('input:checked').each(function () {
        datainsid = $(this).attr('data-insid');
		datainsmulti = $(this).attr('data-ismultiins'); // To handle is insurance available in multiple category itself.
        return (datainsid != '' && typeof insu_id != 'undefined' && (insu_id != datainsid || (datainsmulti == 1 && insu_id == datainsid )) ) ? insidval.push(datainsid) : insidval;
    });    
    return insidval;
}

$(document).delegate('input[name="payment_amt_pop"]:not([readonly]), input[name="payment_amt"]:not([readonly])', 'blur', function () {
    var payamt;
    payamt = !isNaN($(this).val()) ? $(this).val() : $(this).val();
    payamt = (payamt != '') ? parseFloat(payamt).toFixed(2) : "";
    $('input[name="payment_amt_calc"]').val(payamt);
    if (!isNaN(payamt))
        $('input[name="unapplied"]').val(payamt);
    $('#payamount').val(payamt);
    //getbaalnceamount();   // Devi mam asked to disable the feature of auto calculation of amount
});

// show and hide the related div when we change the payment mode at popup page starts here
$(document).on('change', '#js-payment-mode', function () {
    var val = $(this).val();
    $('.js-hide-money').hide();
    $('.js-upload').hide();
    if (val == "Cash" || val == "Money Order") {
        $('.js-checkdetail-div').slideUp("fast");
        $('.js-carddetail-div').slideUp("fast");
        if (val == "Money Order") {
            $('.js-hide-money').show();
            $('.js-hide-money').find("input").attr('readonly', false);
            $('form.js-patient-paymentform').bootstrapValidator('enableFieldValidators', 'money_order_no', true);
            $('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'money_order_no');
        }
    } else if (val == "Check") {
        $('.js-checkdetail-div').slideDown("fast");
        $('.js-carddetail-div').slideUp("fast");
        $('.js-upload').show();
        $("input[name='check_no']").trigger("blur");

    } else if (val == "Credit") {
        $('.js-checkdetail-div').slideUp("fast");
        $('.js-carddetail-div').slideDown("fast");
    }
    payment_type = $('input[name=payment_type]:radio:checked').val();
    if (payment_type == "Payment")
        paymode_val = $('#js-payment-mode').val();
    //revalidationfields('js-bootstrap-validator');
    $(".js-patient-paymentform").data('bootstrapValidator').resetForm();
    //$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
});
// show and hide the related div when we change the payment mode ends here

// Autopost calculation at payment page starts here
$(document).on('click', '.js-autopost', function () {
    var tot_amt = parseFloat($('input[name="pay_amt"]').val());
    $('.js-calculate').each(function () {
        bal = $(this).find('.js-balance').val();
        bal = parseFloat(bal);
        if (tot_amt >= bal && tot_amt != '' && bal>parseFloat(0)) {
            $(this).find('.js_pateint_paid').val(bal);
            tot_amt = tot_amt - bal;
            $('input[name="unapplied_amt"]').val(tot_amt);
        }else if (tot_amt < bal && tot_amt != '' && bal>parseFloat(0)) {
            $(this).find('.js_pateint_paid').val(tot_amt);
            tot_amt = 0.00;
            $('input[name="unapplied_amt"]').val(0.00);
        } else {           
            if(parseFloat(bal)<=parseFloat(0)){
               $(this).find('.js_pateint_paid').val(0.00); 
               tot_amt = tot_amt - 0;
            } else {
               $(this).find('.js_pateint_paid').val(tot_amt); 
            }
            $('input[name="unapplied_amt"]').val(0.00);
        }
    });   
    $('.js_pateint_paid').change();
});
// Autopost calculation at payment page ends here

// Revalidation fields at the submission starts here
function revalidationfields(formid) {
    $('form#' + formid).bootstrapValidator('revalidateField', 'card_type');
    $('form#' + formid).bootstrapValidator('revalidateField', 'card_no');
    $('form#' + formid).bootstrapValidator('revalidateField', 'name_on_card');
    $('form#' + formid).bootstrapValidator('revalidateField', 'check_no');
    $('form#' + formid).bootstrapValidator('revalidateField', 'check_date');
    $('form#' + formid).bootstrapValidator('revalidateField', 'adjustment_reason');
    if (formid != "js-payment")
        $('form#' + formid).bootstrapValidator('revalidateField', 'payment_amt_pop');
    //$('.js-adjust').change();
    //$('form#'+formid).bootstrapValidator('revalidateField', 'wallet_refund');
}
// Revalidation fields at the submission ends here

// Revalidation fields at the submission starts here
function revalidationinsurfield(formid) {
    $('form#js-insurance-form').bootstrapValidator('revalidateField', 'check_no');
    $('form#js-insurance-form').bootstrapValidator('revalidateField', 'check_date');
    $('form#js-insurance-form').bootstrapValidator('revalidateField', 'payment_amt');
    $('form#js-insurance-form').bootstrapValidator('revalidateField', 'adjustment_reason');
}
// Revalidation fields at the submission ends here

//Submit popup starts here
$(document).on('submit', '.paymentpopupform', function () {
    claim_ids = getclaimid();
    payment_type = $('input[name=payment_type]:radio:checked').val();
    var MaxAllowed = $('input[name="insurance_checkbox"]').attr("data-max");
    var find_length = $('.js-paid-cal').find('input[name="insurance_checkbox"]:checked').length;    
    if (claim_ids == '') {
        js_alert_popup(min_choose_err_msg);
        if (payment_type == "refund") {
            //
        }
        return false;
    } /*else if (find_length > MaxAllowed) {
        js_alert_popup('Maximum limit will be ' + MaxAllowed);
        return false;
    }*/
    payment_mode = $('#js-payment-mode').val();
    if (payment_mode == "Cash") {
        $('#choose_claims').find('input:not([type=hidden],[type=radio],[type=submit],[name=wallet_refund],[name=payment_amt_pop],[name=payment_amt_calc], [name=_token], [name=reference], [name=claim_ids])').val('');
    } else if (payment_mode == "Credit") {
        $('#choose_claims').find('input:not([type=hidden],[type=radio],[type=submit],[name=wallet_refund],[name=payment_amt_pop],[name=payment_amt_calc], [name=_token], [name=reference], [name=claim_ids], [name=card_type] ,[name=card_no], [name=name_on_card],[name=cardexpiry_date])').val('');
    }else if (payment_mode == "Money Order") {
        $('#choose_claims').find('input:not([type=hidden],[type=radio],[type=submit],[name=wallet_refund],[name=payment_amt_pop],[name=payment_amt_calc], [name=_token], [name=reference], [name=claim_ids], [name=money_order_no] ,[name=money_order_date])').val('');
    } else if (payment_mode == "Check") {
        $('#choose_claims').find('input:not([type=hidden],[name=filefield_eob],[type=radio],[type=submit],[name=wallet_refund],[name=payment_amt_pop],[name=payment_amt_calc], [name=_token], [name=reference], [name=claim_ids], [name=check_no], [name=check_date], [name=bankname])').val('');
    }
    if (payment_type == 'Adjustment') {
        $('#choose_claims').find('input:not([type=hidden],[type=radio],[type=submit],[name=wallet_refund], [name=_token], [name=reference], [name=claim_ids], [name=adjustment_reason])').val('');
    }
});
//Submit popup ends here

// Edit page payment type change function starts here
$(document).delegate('#js-pay-type', 'change', function () { 
    var val = $('select#js-pay-type option:selected').val();
    var mode = $('select.js-pay-mode option:selected').val();
    if (val == "Adjustment" || val == "Credit Balance") {
        if (val == "Adjustment")
            $('.js-pay-mode').val('').change().attr('disabled', true);
        $('.js-checkdetail, .js-carddetail').find("input").attr('readonly', 'readonly').val("");
    } else if (val == "Payment") {
        $('.js-pay-mode').val('Check').change().attr('disabled', false);
        $('.js-checkdetail').find("input").attr('readonly', false).val("");
        $('.js-carddetail').find("input").attr('readonly', true).val("");
    } else if (val == 'Refund') {
        $('.js-checkdetail').find("input").attr('readonly', false);
        $('.js-pay-mode').val('Check').change().attr('disabled', true);
        $('.js-carddetail').find("input").attr('readonly', true).val("");
    } else {
        //
    }
});
// Edit page payment type change function ends here

paymentmode();
//Payment mode change at edit section starts here
$(document).delegate('.js-pay-mode', 'change', function () {
    paymentmode();
    revalidationfields('js-payment');
});
//Payment mode change at edit section ends here

// function default when payment mode was selected starts

function paymentmode() { 
    var mode = $('select.js-pay-mode option:selected').val();
    var val = $('select#js-pay-type option:selected').val();
    if (val == "Credit Balance") {
        $('.js-checkdetail, .js-carddetail').find("input").attr('readonly', 'readonly').val("");
        $('.js-carddetail').find("select").attr('disabled', true).val('');
    } else {
        if (mode == "Money Order" || mode == "Cash") {
            $('.js-checkdetail, .js-carddetail').find("input").attr('readonly', 'readonly');
            $('.js-carddetail').find("select").attr('disabled', true).val('');
            if (mode == "Money Order") {
                $('.js-checkdetail').find("input:not([name=bankname])").attr('readonly', false);
            }
        } else if (mode == 'Credit') {
            $('.js-checkdetail').find("input").attr('readonly', true);
            $('.js-carddetail').find("input").attr('readonly', false);
        } else if (mode == '') {
            $('.js-carddetail').find("select").attr('disabled', true).val('');
        } else {
            $('.js-checkdetail').find("input").attr('readonly', false);
            $('.js-carddetail').find("input").attr('readonly', true);
            $('.js-carddetail').find("select").attr('disabled', true).val('');
            $('.js-hide-money').find("input").attr('readonly', true);
        }
    }
}
//ends

// amount calculation that will done when we change pateint paid amount starts here
$(document).delegate('.js_pateint_paid', 'change', function () {
    id = $(this).attr('id');
    var pay_type = $('#js-pay-type option:selected').val();
   // var takeback = $('input[name="takeback"]').val();
   
   /* if (takeback == 1 && (parseFloat(currentpaid.val()) > parseFloat(patpaid) || parseFloat(patpaid) <= 0)) {
        js_alert_popup("Only paid amount should be given as payment takeback");        
        $('input[name="patient_paid[' + id + ']"]').val("0.00");
        //return false;
    }
    if (takeback == 1 && parseFloat(currentpaid.val()) > 0) {        
        $('input[name="patient_paid[' + id + ']"]').val(-1 * currentpaid.val());
    }*/
    balance = parseFloat($('input[name= "balance[' + id + ']"]').val());
    if ($(this).val() > 0 && pay_type == 'Payment' && balance <= 0) {
        $(this).val("0.00");
        //return false;
    }      
    if ($(this).val() < 0 && pay_type == "Payment") {
        js_alert_popup("Negative patient payment not allowed");
        $(this).val("0.00");
        //return false;
    }
    var patpaid = $('input[name="patient_paid_calc[' + id + ']"]').val();
    var currentpaid = $('input[name="patient_paid[' + id + ']"]');    
    paid = parseFloat($('input[name= "patient_paid[' + id + ']"]').val());
    
    if(balance<0 && paid>0){        
        //js_alert_popup("Patient payment can't be processed while negative balance");
       // $(this).val("0.00");
    } 
      
    patient_adjusted = parseFloat($('input[name= "patient_adjusted[' + id + ']"]').val());    
    if (isNaN(paid)) {
        $('input[name= "patient_balance[' + id + ']"]').val("");
    }
    // This is used when we apply negative adjustment starts here
    if (pay_type == "Adjustment") {
        if (patient_adjusted < Math.abs(paid) && paid < parseInt(0)) {
            var adjust_msg = "Only the patient adjusted amount should be made as in negative";
            js_alert_popup(adjust_msg);
            $('input[name= "patient_balance[' + id + ']"]').val("");
            $('input[name="patient_paid[' + id + ']"]').val("");
            calculatepatientpaymentamount(id);
            return false;
        } else if (patient_adjusted >= Math.abs(paid) && paid < parseInt(0)) {
            //calculatepatientpaymentamount(id);
            //return false;
        } else if (balance <= parseInt(0) && paid > parseInt(0) ||( paid > balance && paid>0)) {
            var adjust_msg = "Adjustment can't be done more than balance amount";
            js_alert_popup(adjust_msg);
            $('input[name= "patient_balance[' + id + ']"]').val("");
            $('input[name="patient_paid[' + id + ']"]').val("");
            calculatepatientpaymentamount(id);
            return false;
        }
    }
    // This is used when we apply negative adjustment ends here
    if ((pay_type == "Credit Balance" || pay_type == "Payment")&& paid > balance && balance >=0) { // Asked restriction from balance amount on 15/2/2018
        js_alert_popup(baalnce_ant_err_msg);   // Adjustment only done for balance amount
        $('input[name= "patient_balance[' + id + ']"]').val("");
        $('input[name="patient_paid[' + id + ']"]').val("");
        calculatepatientpaymentamount(id);
        return false;
    }
    paid = !isNaN(paid) ? paid : 0;
    if (pay_type == "Refund") {
        //paid = paid > 0? -1*paid:paid;
        paid = paid > 0 ? paid : paid;
        $('input[name= "patient_paid[' + id + ']"]').val(paid.toFixed(2));
        paid = Math.abs(paid);
    }
    balance = !isNaN(balance) ? balance : 0;
    // console.log("balance"+balance);
    patient_balance = balance - paid;
    if (pay_type == "Refund") {
        patient_balance = balance + paid;
        patient_balance = (paid != parseFloat(0)?patient_balance:"");
    }
    //console.log("patient_balance"+patient_balance);
    patient_balance = (patient_balance == '')?patient_balance:parseFloat(patient_balance).toFixed(2);
    //console.log("patient_balance after"+patient_balance);
    $('input[name= "patient_balance[' + id + ']"]').val(patient_balance);
    calculatepatientpaymentamount(id);
    if ($('#js-patient-form').length) {
        $('form#js-patient-form').bootstrapValidator('revalidateField', 'js_pateint_paid');
    } else {
        $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_pateint_paid');
    }
    // $('form#js-insurance-form').bootstrapValidator('revalidateField', 'js_pateint_paid');
});

function calculatepatientpaymentamount(id) {
    var val = ['js-cpt-billed', 'js-cpt-allowed', 'js-paid-amt', 'js-balance', 'js_pateint_paid', 'js-patient-balance'];
    var pay_type = $('#js-pay-type option:selected').val();
    var tot_charge = '';
    var value = $('#js-pay-type option:selected').val();
    $.each(val, function (index, value) {
        var tot_charge = 0;
        $('.' + value).each(function () {
            var amt_val = $(this).val();
            var amt_val = !isNaN(amt_val) ? amt_val : 0;
            tot_charge += Number(amt_val);
        });
        tot_charge = parseFloat(tot_charge).toFixed(2);
        wallet = $('input[name="wallet_refund"]').val();
        wallet = !isNaN(wallet) ? wallet : 0;
        //tot_charge = parseFloat(wallet)+parseFloat(tot_charge);
        $('span#' + value).html(price_format + " " + tot_charge);
        if (value == "js_pateint_paid") {
            setTimeout(function () {
                tot_amt = $('input[name="pay_amt"]').val();
                remaning = tot_amt - Math.abs(tot_charge);
                remaning = remaning - Math.abs(wallet);  // reduce amount from wallet   we change refund amount in negative
                remaning = parseFloat(remaning.toFixed(2));
                remaning = !isNaN(remaning) ? remaning : '0.00';
                if (pay_type == 'Adjustment') {
                    $('input[name="payment_amt"]').val(tot_charge);
                }
                calculateunbilledamt(remaning, id);

            }, 50);
        }
        if (value == "js_pateint_paid" || value == "js-patient-balance") {  //To fix error arised on firefox
            setTimeout(function () {
                final_charge = 0;
                $('.' + value).each(function () {
                    var amt_val = $(this).val();
                    var amt_val = !isNaN(amt_val) ? amt_val : 0;
                    final_charge += Number(amt_val);
                });
                final_charge = parseFloat(final_charge).toFixed(2);
                $('span#' + value).html(price_format + " " + final_charge);
            }, 75);
        }
    });
}

// amount calculation that will done when we change pateint paid amount ends here
function calculateunbilledamt(remaning, id) {
    var value = $('#js-pay-type option:selected').val();
    var pateint_paid = $('input[name="patient_paid_calc[' + id + ']"]').val();
    var currt_paid = $('input[name="patient_paid[' + id + ']"]').val();
    var billed_amount = $('input[name="cpt_billed_amt[' + id + ']"]').val();
    var payment_amt = $('input[name="pay_amt"]').val();
    var takeback = $('input[name="takeback"]').val();
    pateint_paid = parseFloat(pateint_paid);
    currt_paid = parseFloat(currt_paid);
    tot_paid = calculatepatientpaid();
    if (value == 'Refund') {
        amt_uapplied = parseFloat(payment_amt) - parseFloat(tot_paid); //changed from minus concept to plus concept on refund
    } else {
        amt_uapplied = parseFloat(payment_amt) - parseFloat(tot_paid);
    }
    currt_paid = Math.abs(currt_paid);
    if (remaning < 0 && value != 'Adjustment' && takeback != 1) {
        js_alert_popup(unappied_err_msg);
        $('input[name="patient_paid[' + id + ']"]').val("");
        $('input[name="patient_balance[' + id + ']"]').val("");
        // Apply the final amount to the unapplied box starts here
        tot_paid_final = calculatepatientpaid();
        tot_paid_final = !isNaN(tot_paid_final) ? tot_paid_final : 0;
        if (value == 'Refund') {
            amt_uapplied = parseFloat(payment_amt) - parseFloat(tot_paid_final);
        } else {
            amt_uapplied = parseFloat(payment_amt) - parseFloat(tot_paid_final);
        }
        $('input[name="unapplied_amt"]').val(parseFloat(amt_uapplied).toFixed(2));
        // Apply the final amount to the unapplied box ends here
        //return false;
    } else if (value == 'Adjustment' && typeof currt_paid != "undefined" && parseFloat(currt_paid) > parseFloat(billed_amount)) {
        js_alert_popup(billed_amt_err_msg);
        $('input[name="patient_paid[' + id + ']"]').val("");
        $('input[name="patient_balance[' + id + ']"]').val("");
        //return false;
    } else {
        tot_paid_amt = calculatepatientpaid();
        if (value == 'Refund') {
            unapplied_amt = parseFloat(payment_amt) - parseFloat(tot_paid_amt);
        } else {
            unapplied_amt = parseFloat(payment_amt) - parseFloat(tot_paid_amt);
        }
        unapplied_amt = unapplied_amt.toFixed(2);
        if (value != 'Adjustment')
            $('input[name="unapplied_amt"]').val(unapplied_amt);
        if (value == "Refund" && (typeof pateint_paid != "undefined" || pateint_paid != '' || pateint_paid == 0)) {
            var unapplied_amt = $('input[name="unapplied_amt"]').val();
            if (pateint_paid >= currt_paid) {

            } else if (typeof pateint_paid != "undefined" && pateint_paid != '' && !isNaN(pateint_paid) || tot_paid > unapplied_amt || pateint_paid == 0) {
                js_alert_popup(paid_amt_err_msg);
                $('input[name="patient_paid[' + id + ']"]').val("");
                $('input[name="patient_balance[' + id + ']"]').val("");
                tot_paid_amt = calculatepatientpaid();
                var total_amt = parseFloat(payment_amt) + parseFloat(tot_paid_amt);
                $('input[name="unapplied_amt"]').val(total_amt.toFixed(2));
            }
        }
    }
    $('form#js-payment').bootstrapValidator('revalidateField', 'js_pateint_paid');
    $('.js_pateint_paid').on('change', function () {
        $('form#js-payment').bootstrapValidator('revalidateField', 'js_pateint_paid');
    });
}

$(document).delegate('input[name="payment_amt"]', 'change', function () {
    var payment_type = $('#js-pay-type option:selected').val();
    value = $(this).val();

    if (isNaN(value)) {
        value = "";
    } else if (value != '' && typeof value != 'undefined') {
        value = parseFloat(value).toFixed(2);
    }
    changepatientpaid();
    $('input[name="pay_amt"]').val(value);
    if (payment_type != "Adjustment")
        $('input[name="unapplied_amt"]').val(value); //return false;
    setTimeout(function () {
        calculatepatientpaymentamount();
    }, 50);
});

// To change text depeds on the payment type starts here
function changetext() {
    var text = $('#js-pay-type option:selected').text();
    $('.js-adjustment').attr('disabled', true);
    $('#js-pay-type').attr('readonly', true);
    if (text == 'Adjustment') {
        $('.js-disable-amount').find("input,select").attr('disabled', true);
        $('.js-adjustment').attr('disabled', false);
        $('.js-change-text').text("Adj Amount");
    } else if (text == 'Refund') {
        $('.js-change-text').text("Refund Amt");
    } else {
        $('.js-change-text').text("Patient Paid");
    }
}
// To change text depeds on the payment type ends here

// This is for to show the  refund amount box starsts
$(document).on('ifToggled click', '.js-creditbalance', function () {
    $('input[name="wallet_refund"]').val("");
    if ($(this).is(':checked')) {
        $('.js-show-amountbox').show();
        $('#js-removewallet').hide();
    } else {
        $('.js-show-amountbox').hide();
        $('#js-removewallet').show();
    }
      
   /* setTimeout(function(){ alert("dsfsdfdsfsds")
         $('form#js-bootstrap-validator').bootstrapValidator('addField', "wallet_refund");

            $('form#js-bootstrap-validator')
                    .data('bootstrapValidator')
                    .updateStatus('wallet_refund', 'NOT_VALIDATED')
                    .validateField('wallet_refund');
       
       // $('.js-patient-paymentform').bootstrapValidator('updateStatus', "wallet_refund", 'VALIDATING')
      // .bootstrapValidator('validateField', "wallet_refund");
       //  $('.js-patient-paymentform').bootstrapValidator('validateField', 'wallet_refund');
        // $('.js-patient-paymentform').bootstrapValidator("enableFieldValidators", "wallet_refund", true)
       $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', "wallet_refund");
       // $('form.js-patient-paymentform').bootstrapValidator("updateStatus", "wallet_refund");
    }, 1000);
   */
});
// This is for to show the refund amount box ends

// Inform user that if theu have negative balance amount or unapplied amount that will be mvoed to wallet
function unappliedpopup() {
    unapplied_val = $('input[name ="unapplied_amt"]').val();
    payment_type = $('input[name="payment_type"]').val();
    balance = '';
    $('.js-patient-balance').each(function () {
        var name = $(this).attr("name");
        var name_paid = name.replace("patient_balance", "patient_paid");
        paid_sel = $('input[name="'+name_paid+'"]');        
        if ($(this).val() < 0 && paid_sel.val()>0) {
            balance = $(this).val();
            return false;
        }
    });
    if ((unapplied_val < 0 || balance < 0) && payment_type == "Payment") {
        return true;
    }
}

is_valid = 0;
$('#js-payment').on('submit', function (e) {
    if (e.isDefaultPrevented()) {
    } else {
        val = unappliedpopup();
        if (val) {
            $('#js_confirm_box_charges_content').html(wallet_confm_msg)
            $("#js_confirm_box_charges")
                .modal({show: 'false', keyboard: false})
                .one('click', '.js_modal_confirm1', function (eve) {
                    revalidationfields('js-payment');
                    if ($(this).attr('id') == "true") {
                        is_valid = 1;
                        $('#js-payment').submit();
                    } else {
                        return false;
                    }
                });
        } else {
            is_valid = 1;
        }
        if (!is_valid)
            e.preventDefault();
    }
});
// Ends poup information

// This is when i come back after entering popup on payment page data by default checkbox div classes are added but not selected isue fixed
/*$(document).ready(function () {
    $('.icheckbox_flat-green').each(function () {
        area_chk = $(this).attr('aria-checked');
        if (area_chk == "false") {
            $(this).removeClass('checked');
        }
    });
});*/

// Ends popup checkbox issues

// Defauly radio option as payment duw to avaide disabled activity of payment radio
if ($('.js-payment-mode').length) {
    var radios = $('input:radio[name=payment_type]');
    radios.filter('[value=Payment]').prop('checked', true);
}

// when we change the amount  after applied to patient paid need to delete paid amount

function changepatientpaid() {
    $('.js_pateint_paid').each(function () {
        $(this).val("");
    });
    $('.js-patient-balance').each(function () {
        $(this).val("");
    });
    $(".jsrefund").val("");
}

// Calculate patient paid and wallet
function calculatepatientpaid() {
    var paid_total = 0;
    var tot = 0;
    wallet = $('input[name="wallet_refund"]').val();
    $('.js_pateint_paid').each(function () {
        paid_total += Number($(this).val());
    });
    wallet = !isNaN(wallet) ? wallet : 0;
    paid_total = !isNaN(paid_total) ? paid_total : 0;
    tot = (wallet != '') ? parseFloat(wallet) + parseFloat(paid_total) : parseFloat(paid_total);
    return tot;
}

$(document).on('change', 'input[name="wallet_refund"]', function () {
    if ($(this).val() <= 0) {
        $(this).val("");
    }
    //$('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'wallet_refund');
    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'wallet_refund');
});

// check conditions for wallet and patient pasi refund amount
$(document).on('change', '.jsrefund', function () {
    wallet_bal = $('input[name="credit_balance"]').val();
    var paymenttype = $('input[name="payment_type"]').val();
    if (wallet_bal == 0) {
        js_alert_popup("Insufficient wallet balance");
        $(this).val("");
        return false;
    }
    if ($(this).val() > parseFloat(wallet_bal)) {
        $(this).val("");
        js_alert_popup("Entered amount was greater than the wallet balance");
        //return false;
    }
    var unapplied = 0;
    var paid_total = 0;
    var payment_amt = $('input[name="pay_amt"]').val();
    wallet_amt = $('input[name="wallet_refund"]').val();
    //wallet_amt = (wallet_amt<0)   ?wallet_amt:-1*wallet_amt;
    wallet_amt = (wallet_amt !='' && typeof wallet_amt != "undefined")?parseFloat(wallet_amt).toFixed(2):"";    
    $(this).val(wallet_amt);
    totpatient_paid = calculatepatientpaid();
    payment_amt = !isNaN(payment_amt) ? payment_amt : 0;
    unapplied = !isNaN(unapplied) ? parseFloat(payment_amt) - parseFloat(totpatient_paid) : 0;
    unapplied = unapplied.toFixed(2);
    if (!isNaN(unapplied))
        $('input[name ="unapplied_amt"]').val(unapplied);
    if (unapplied < 0) {
        $('.js_pateint_paid').each(function () {
            paid_total += Number($(this).val());
        });
        paid_total = Math.abs(paid_total);
        unapply = payment_amt - paid_total;
        unapply = unapply.toFixed(2);
        $('input[name ="unapplied_amt"]').val(unapply);
        $(this).val("");
    }
});

$(document).delegate('a[data-target=#js-model-popup-payment]', 'click', function () {
    $("#js-model-popup-payment .modal-title").html("");
    $("#js-model-popup-payment .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
    var target1 = $(this).attr("data-url");
    var claim_no = $(this).attr('claim_number');
    $("#js-model-popup-payment .modal-body").load(target1, function () {
        $("#js-model-popup-payment .modal-title").html("Claim No : " + claim_no);
    });
});

// Make the checkboc input as disabled for unpaid claims at refund process starts here
function disableunpaidcheckbox() {
    chkd = $('input[name=payment_type]:radio:checked').val();
    if (chkd == "Refund") {
        $('.js-paid-cal input[name = "patient_paid_amt"]').each(function () {
            if ($(this).val() == "0.00") {
                clss = $(this).attr('class');
                $('input[data-claim="' + clss + '"]').attr("disabled", true);
            }
        });
    } else {
        $('.js-paid-cal input[type=checkbox]:disabled').each(function () {
            clss = $(this).attr('data-claim');
            $('input[data-claim="' + clss + '"]').prop("disabled", false);
        });
    }
}
// Make the checkboc input as disabled for unpaid claims at refund process ends here

// Insurance payment posting related code starts here
$(document).delegate('.js-sel-claim', 'ifToggled click', function (e) {
    $('.jsinsuranceform').bootstrapValidator('disableSubmitButtons', false);
    $('.js-patient-paymentform').bootstrapValidator('disableSubmitButtons', false);
    /*
     var length = $('input[name="insurance_checkbox"]:checked').length;
     var type =   $('input[name=payment_type_ins]:radio:checked').val();
     if(length > 0 && type == "Refund"){
     $('.js-sel-claim').not($(this)).attr('checked', false).iCheck('update');
     $('.js-sel-claim').not($(this)).prop('disabled', true).iCheck('update');
     $('.js-sel-claim').attr('data-insurance','Patient').prop('disabled', true).iCheck('update');
     } else if(type == "Refund"){
     disableclaim(ins_id);
     $('.js-sel-claim').prop('disabled', false).iCheck('update');
     $('.js-sel-claim').attr('data-insurance','Patient').prop('disabled', true).iCheck('update');
     }
     form = $('.js-search-patient').closest("form");
     make_checkbox_enable = 0;
     if(form.hasClass('js-patient-paymentform') || $('#js-patient-form').length || $('#js-insurance-form').length){
     make_checkbox_enable = 1;
     }
     if(make_checkbox_enable) {
     $('.js-sel-claim').prop('disabled', false).iCheck('update');
     } else{
     $('input[data-insurance ="Patient"]').prop('disabled', true).iCheck('update');  // To make the patient pais amount as default
     }*/
    getclaimid();
});

lots_of_stuff_already_done = false;
// Insurance payment posting related code ends here
$(document).delegate('.jsinsuranceform', 'submit', function (e) {   //console.log("submit event called"); 
    var ar_claim_id = $('input[name="claim_ids"]').val();
    var edit_payment_claim_ids = '';
    find_length = $('.js-paid-cal').find('input[name="insurance_checkbox"]:checked').length;
    if (ar_claim_id != '' && typeof ar_claim_id != 'undefined') {
        edit_payment_claim_ids = 1;
    }
    var MaxAllowed = $('input[name="insurance_checkbox"]').attr("data-max");
    if (find_length == 0 && edit_payment_claim_ids == '') { 	// console.log("min choose error block");
        js_alert_popup(min_choose_err_msg);
        return false;
    } /*else if (find_length > MaxAllowed && edit_payment_claim_ids == '') {

        js_alert_popup('Maximum limit will be ' + MaxAllowed);
        return false;
    }*/
    //revalidationinsurfield('js-insurance-form');
    //e.preventDefault();	
	
	// Handled the insurance selection option.
	check_popup = $('input[name="change_insurance_id"]').length; 	//console.log("check_popup :"+check_popup);
	var insurance_id = $('#js-insurance-id').val(); //console.log("insurance_id = "+insurance_id);
	if (check_popup) {           //	console.log("check popup block");
		insurance_value = getinsuranceid(insurance_id);
		var change_ins_id = $('input[name="change_insurance_id"]').val();
		if (typeof insurance_value != 'undefined' && insurance_value != '' && change_ins_id == '') {				
			var patient_id = $('input[name="patient_id"]').val();
			getInsuranceList(patient_id);
			return false;
		} else if(change_ins_id == '') {
			// console.log("checking for others");
			// Show popup for choose payment post insurance category
			
			checkcat_popup = $('input[name="pmt_post_ins_cat"]').length;
			var selPmtOtherInsCat = $("#pmt_post_ins_cat").val(); 	
			if(checkcat_popup && selPmtOtherInsCat == '') {
				
				if(insurance_id.indexOf('-') != -1) {
					// Patient payment block
					var pmt_post_ins_cat = $('input[name="pmt_post_ins_cat"]').val();
					insurance_split = insurance_id.split('-');
					pmt_ins_cat = insurance_split[0];
					if (typeof pmt_ins_cat != 'undefined' && pmt_post_ins_cat == '' && pmt_ins_cat == 'Others') {			
						$('#patient_insurance_cat_model').modal('show');
						return false;
					}
				} else {
					// Main payment block
					var patOtherIns = $("#patient_other_ins").val(); 
					var items = patOtherIns.split(/\s*,\s*/); 
					var isContained = items.some(function(v) { return v === insurance_id; });	
					if (typeof patOtherIns !== typeof undefined && isContained) {
						$('#patient_insurance_cat_model').modal('show');
						return false;
					}
				}	
			}
		}
	}
	
	if (e.isDefaultPrevented()) {
        //
    } else {
        formid = "js-insurance-form";
        type = "Insurance"
        checknumber = $('#' + formid).find("input[name='check_no']").val();
        checktype = $('#' + formid).find("input[name='payment_method']").val();
        payment_id = $('#' + formid).find("input[name='payment_detail_id']").val();
        url = api_site_url + '/payments/checkexist/' + type + '/' + checknumber + '/' + checktype;
        if (checknumber != '' && payment_id == '') {
            $.get(url, function (data) {
                if (data == "error") {
                    $('input[name="checkexist"]').val(1);
                    $('form#' + formid).bootstrapValidator('revalidateField', 'check_no');
                    return false;
                } else {
                    $('input[name="checkexist"]').val("");
                    $('form#' + formid).bootstrapValidator('revalidateField', 'check_no');
                    //$('#'+formid).unbind('submit').submit();
                    //$("#js-insurance-form").submit();
                    return true;
                }
            });
			//return false;
		}
    }
});

$(document).on('blur', '.js-check-number:not([readonly])', function () {
    formid = $(this).closest("form").attr('id');
    type = $(this).attr('data-type');    
    var check_val = $(this).val();   
    checkno = $('#' + formid).find("input[name='check_no']").val();
    if (checkno != '' && typeof checkno != "undefined")
        if (checknumbervalidation(checkno)) {
            checkvalidation(formid, type);
        }
});

$(document).on('keyup keypress', '.js-check-number', function () {
    var check_val = $(this).val();
    check_val = $.trim(check_val);
    $(this).val(check_val.toUpperCase());
    form_id = $(this).parents("form").attr("id");
    $('#' + form_id).bootstrapValidator('disableSubmitButtons', true);
});

$(document).on('ifToggled change', 'input[name="insur_payment_mode"]', function () { 
    formid = $(this).closest("form").attr('id');
    type = "Insurance";
    checkno = $('#' + formid).find("input[name='check_no']").val();
    if (checkno != '' && typeof checkno != "undefined")
        if (checknumbervalidation(checkno)) {
            checkvalidation(formid, type);
        }
        if($(this).val()== "Credit"){
            $('.js-cc').show()
        } else{
            $('.js-cc').hide()
        }
});

//  check number unique validation starts here
function checkvalidation(formid, type)
{
    checknumber = $('#' + formid).find("input[name='check_no']").val();
    check_type = $('#' + formid).find("input[name='payment_mode']").val();
    patient_id =  $('#' + formid).find("input[name='patient_id']").val(); 
    pmt_type =  $('#' + formid).find("input[name='payment_type_ins']:checked").val(); 
    patient_id = (typeof patient_id != "undefined" && patient_id != "")?'/'+patient_id:""; 
    if(pmt_type == "Refund") {
        check_type = "Check";
    } else if ($('input[name=insur_payment_mode]').length && (typeof check_type == 'undefined' || check_type == '')) {
        check_type = $('#' + formid).find('input[name=insur_payment_mode]:radio:checked').val();
    } else if (typeof check_type == 'undefined' || check_type == '') {
        check_type = "Check";
    }
    url = api_site_url + '/payments/checkexist/' + type + '/' + checknumber + '/' + check_type+patient_id;
    if (checknumber != '' && typeof check_type != 'undefined') {
        $.get(url, function (data) {
            if (data == "error") {
                $('input[name="checkexist"]').val(1);
                $('form#' + formid).bootstrapValidator('revalidateField', 'check_no');
                return false;
            } else {
                $('input[name="checkexist"]').val("");
                $('form#' + formid).bootstrapValidator('revalidateField', 'check_no');
                return true;
            }
        });
	}	
}

$(document).on('click ifToggled change', '.js-inscat-type', function () {
    var insurancecat_val = $(this).attr('data-insurance_cat');
    $('input[name="pmt_post_ins_cat"]').val(insurancecat_val);
});

$(document).on('click change', '.js-otherpmt_ins_cat', function () {
	var radio_length = $(':radio[name= "pmt_otherins_cat"]:checked').length;
	if (!radio_length) {
		js_alert_popup("Choose any one category");
		return false;
	} else {
		// For handle payment from Both AR and payment posting page
		if ($('.js-popupinsuranceadd:visible').length) {
			$('.js-popupinsuranceadd').click()
		} else {
			$('.jsinsuranceform').submit();
		}
 
		$("#patient_insurance_cat_model").modal('hide');
	}
});

$(document).on('click', '.js-continue-btn', function (e) {
    formid = $(this).closest("form").attr('id');
    type = $(this).attr('data-type');
    checkvalidation(formid, type);
    //checkvalidation();
})

$(document).on('blur', 'input[name="check_no"]', function () {
    $('.js-check-error').remove();
});
// Check number unique validation ends here

// Payment remark codes Append starts here
code_arr = [];
$(document).delegate('.js-remarkcode', 'change', function (e) {
    id = $(this).attr('id');
    remark_id = $(this).val();
    append = 'js-append-' + id;
    url = api_site_url + '/getremarkcode/' + remark_id;
    append_data = '';
    deniel_id = $("#js-denial-" + id + " option:selected").val();
    deniel_id = (deniel_id != 0) ? deniel_id : "";
    cpt_code = $("#js-cpt-code-" + id).val();
    if (remark_id != '') {
        $.get(url, function (data) {
            data = $.parseJSON(data);
            codes = data.code;
            appended_val = '';
            $.each(codes, function (key, value) {
                code = value.transactioncode_id;
                description = value.description;
                if (description != '') {
                    //scode_arr.push(code);
                    span_class = "js-remark-description-" + id;
                    appended_val += '<p class="no-bottom med-gray-dark ' + span_class + '"><span class="med-orange font600">' + cpt_code + ' - ' + deniel_id + code + ' : </span>' + description + '</p>';
                }
                if ($.inArray(code, code_arr) === -1)
                    code_arr.push(code);
            });
            $('#' + append).html("");
            append_data = '<p id = "' + append + '" class="no-bottom med-gray-dark"><span class="med-orange font600">' + appended_val + ' </span></p>';
            if ($('#js-remark-append-data-' + id).length) {
                $('.js-remark-append #js-remark-append-data-' + id).html(append_data);
            } else {
                $('.js-remark-append').append("<span id= 'js-remark-append-data-" + id + "'>" + append_data + "</span>");
            }
        });
    } else {
        if ($('#' + append).length) {
            $('#' + append).html(append_data);
            return false;
        }
    }
});

// Payment remark codes Append ends here
$(document).delegate('.js-denieal-code', 'change', function (e)
{
    id = $(this).attr('data-id');
    var value = $(this).val();
    var span_class = "js-remark-append-data-" + id;
    var replace_data = ['CO', 'PR', 'OA', 'PI'];
    $('span#' + span_class + ' p').each(function () {
        text = $(this).find('span').text();
        if (text != '' && typeof text != 'undefined') {
            newtext = text.split('-');
            var repplace_text = newtext[1];
            var replace = repplace_text.substr(0, 2);
            if ($.inArray(replace, replace_data) > -1) {
                if (value == 0) {
                    var text = text.replace(replace, "");
                } else {
                    var text = text.replace(replace, value);
                }
            } else if (value != '') {
                text = newtext[0] + '-' + value + replace;
            }
            $(this).find('span').text(text);
        }
    });
});

$(document).on('hidden.bs.modal', '#payment_editpopup', function (e) {
    // put your default event here
	// Facing Edit document popup hide issues so we comment this line
   // $(this).find(".modal-body").html("");
});

// Insurance popup ajax call starts here
addwallet = "add";
$(document).on('click', 'a[data-target=#choose_claims], a[data-target=#choose_claim]', function () {
    var target = $(this).attr("data-url");
    var datatarget = $(this).attr("data-target");
    var title_info = $(this).attr("data-tile");
    addwallet = "add";
    addwallet_name = ""; // to prevent default submisssion
    $(datatarget + " .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
    if (title_info != '' && typeof title_info != 'undefined')
        $(datatarget + ' .modal-title').html(title_info);
    $(datatarget + " .modal-body").load(target, function () {
        $confModal = $(datatarget);
        var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $confModal.on('hidden', function () {
            $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
        });
        if(datatarget =="#choose_claims") {
             $("div#post_payments .modal-body").html("");
        }
        /** This is used when we use from main payment screen starts**/
        if ($("a[data-target=#choose_claims]").hasClass('js-show-patientsearch') && datatarget != '#choose_claim') {
            // this condition is applied for check reopn and for patient payment we can show the claims instead of searching again
            $('.js-addtowallet').hide().removeAttr("id");
            $('.js-patient-search').show();
            $('.js-append-mainpayment-table').hide();
        } else if (datatarget == '#choose_claim' && $("a[data-target=#choose_claim]").hasClass('js-show-patientsearch')) {
            $('.js-patient-search').show();
            $('.js-append-mainpayment-table').hide();
            disableinputforcheckdata('js-popupinsurance-data');  // To make the already entered check information as readonly
        } else if (datatarget == '#choose_claim' && !$("a[data-target=#choose_claim]").hasClass('js-show-patientsearch')) {
            $('.js-append-mainpayment-table').show();
            disableinputforcheckdata('js-popuppatient-data');  // To make the already entered check information as readonly
            $('.js-addtowallet').hide().removeAttr("id");
            enabledataTables();
        } else {
			// Remove the continue button link
			if($('.js-append-mainpayment-table').prev('#js_selected_insurance').prev('div.box-header-view-white').length)
				$('.js-append-mainpayment-table').prev('#js_selected_insurance').prev('div.box-header-view-white').remove();
            $('.js-append-mainpayment-table').show(); 
             enabledataTables();
            //$('.js-patient-search').hide();

        }
        callicheck();
        $('select.select2.form-control').select2(); // This is put for quickfix
        /** This is used when we use from main payment screen ends**/
        // SelectTo and checkbox call
        validatepatientpayment();
        validateinsurancepayment();
    });
    removeHash();
});

$(document).on('click', 'td[data-target=#cpt_details]', function () { 
    var target = $(this).attr("data-url");
    var datatarget = $(this).attr("data-target");
    var title_info = $(this).attr("data-tile");

    $(datatarget + " .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
    if (title_info != '' && typeof title_info != 'undefined')
        $(datatarget + ' .modal-title').html(title_info);
    $(datatarget + " .modal-body").load(target, function () {
        $confModal = $(datatarget);
        var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $confModal.on('hidden', function () {
            $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
        });
    });
    removeHash();
});

$(document).on('click', 'td[data-target=#auto_post_status]', function () { 
    var target = $(this).attr("data-url");
    var datatarget = $(this).attr("data-target");
    var title_info = $(this).attr("data-tile");

    $(datatarget + " .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
    if (title_info != '' && typeof title_info != 'undefined')
        $(datatarget + ' .modal-title').html(title_info);
    $(datatarget + " .modal-body").load(target, function () {
        $confModal = $(datatarget);
        var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $confModal.on('hidden', function () {
            $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
        });
    });	
    removeHash();
});

$(document).on('click', '.dropdown-toggle', function () {
    window.location.hash = ''; // for older browsers, leaves a # behind
    history.pushState('', document.title, window.location.pathname); // nice and clean
});

if ($('#js-payment').length) {
    validatepatientpayment('#js-payment');
}

if ($('#js-insurance-form').length) {
    validateinsurancepayment('#js-insurance-form');
}

$(document).delegate('.js-ins-type', 'ifToggled change', function () { 
    // clear claim ids for visible selection block only as well multiple claims listed then.
	if ($(".js-append-mainpayment-table").is(':visible') && $('.js-sel-claim').length > 1) {
		$('input[name="claim_ids"]').val('');
	}
	// uncheck it only when claim list available more than one than.
	if($('.js-sel-claim').length > 1) {
		$('.js-sel-claim').attr('checked', false);
		$('.js-sel-claim').prop('disabled', false); 
	}
	
    var type = $(this).val();  
    var ins_type = $('input[name="insur_payment_mode"]:checked').val();
    $('.js-hide-adjustment').hide();
    $('.js-only-show-check').hide();
    $('.js-check-no').text('Check/EFT/CC No');
    $('.js-check-date').text('Check/EFT/CC Date')
    if (type == "Payment") {
        $('.js-refund').slideDown('slow');
        $('.js-adjustment').slideDown('slow');
    } else if (type == "Refund") {
        $('.js-refund').slideUp('slow');
        $('.js-adjustment').slideDown('slow');
        $('.js-only-show-check').show();
        $('.js-check-no').text('Check No');
        $('.js-check-date').text('Check Date');
    } else {
        $('.js-hide-adjustment').show();
        $('.js-refund').slideUp('slow');
        $('.js-adjustment').slideUp('slow');
    }   
    if(ins_type== "Credit" && type == "Payment"){
        $('.js-cc').show()
    } else{
        $('.js-cc').hide()
    }
    if (type != "Payment")
        //$('input[data-insurance ="Patient"]').prop('disabled', true).iCheck('update'); This feature was disabled
        $("#js-insurance-form").data('bootstrapValidator').resetForm();
    //revalidationinsurfield('js-insurance-form');
   
    ins_id = $('#js-insurance-id').val(); 
    if($('input[name="check_no"]').val() != "" && (type =="Payment" || type =="Refund")) { 
        $('.js-check-number').trigger("blur");
    }
	$('input[data-hold ="Hold"]').prop('disabled', true); // For insurance payment hold claims are disabled
	//console.log("cheeck"+$(".js_menu_payment").is(":checked"));
	$('.js_submenu_payment').not(":disabled").prop('checked', $(".js_menu_payment").is(":checked"));

    //disableclaim(ins_id);
});
// Insurance popup ajax call ends here

// Change event at bootsrape starts
function changeevent(field) {
    $('form#js-insurance-form').bootstrapValidator('revalidateField', field);
}
// Change event at bootsrape ends

$(document).delegate('.js-next', 'click', function () {
    $('input[name="next"]').val(1);
    $('input[name="resubmit"]').val("");
});

$(document).delegate('.js-save', 'click', function () {
    $('input[name="resubmit"]').val("");
    $('input[name="next"]').val("");
});

$(document).delegate('.js_limit_amt', 'change', function () {
    $(this).val(" ");
});

function deductunuppliedamount() {
    var arr_val = ['js-deductible', '']
}

// Other than insurance payment make the fields as readonly for adjustment and refunds starts here
function makeallfieldsreadonly(type) {
    if (type == "Adjustment") {
        $(".js-disable-div :input").not(".js-adjust,.js-withheld,.js_other_adj,.btn,.other_adj_total").addClass('class-readonly').prop("readonly", true);
        $(".js-disable-div :input").not(".js-adjust,.js-withheld,.js_other_adj,.btn,.other_adj_total").parent('td').addClass('class-readonly');
    } else if (type == "Refund") {
        $(".js-disable-div :input").not(".js-paid-amt").addClass('class-readonly').prop("readonly", true);
        $(".js-disable-div :input").not(".js-paid-amt").parent('td').addClass('class-readonly');
    }
}
// Other than insurance payment make the fields as readonly for adjustment and refunds ends here

// function for EOB calculations starts here
function commonselector(i, classval, curr) {
    billed = $('input[name="cpt_billed_amt[' + i + ']"]');
    allowed = $('input[name="cpt_allowed_amt[' + i + ']"]');
    paid_amt = $('input[name="paid_amt[' + i + ']"]');
    adjustment = $('input[name="adjustment[' + i + ']"]');
    coins = $('input[name="co_ins[' + i + ']"]');
    copay = $('input[name="co_pay[' + i + ']"]');
    deductable = $('input[name="deductable[' + i + ']"]');
    withheld = $('input[name="with_held[' + i + ']"]');
    balance = $('input[name="balance[' + i + ']"]');
    balance_secondary = $('input[name="balance_secondary[' + i + ']"]');
   // balance_secondary = $('input[name="balance_original[' + i + ']"]');  
    secondary = $('input[name="secondary_ins"]');
    type = $('input[name="payment_type"]').val();
    insurance_paid = $('input[name="insurance_paid[' + i + ']"]');
    balance_original = $('input[name="balance_original[' + i + ']"]');
    insurance_adjustment = $('input[name="insurance_adjusted[' + i + ']"]');
    common_sel_arr = {'billed': billed, 'allowed': allowed, 'paid_amt': paid_amt, 'adjustment': adjustment, 'coins': coins, 'copay': copay, 'deductable': deductable, 'withheld': withheld, 'balance': balance, 'balancesecondary': balance_secondary, 'secondary': secondary, 'type': type, 'insurance_paid': insurance_paid, 'balance_original': balance_original, 'insurance_adjustment': insurance_adjustment};
    return common_sel_arr;
}

function calculationnew(i, classval, curr) {
    var current_name = $(curr).attr('name');
    var newval = isNaN($('input[name="' + current_name + '"]').val()) ? $('input[name="' + current_name + '"]').val("0.00") : ""
    comon_selector = commonselector(i, classval, curr);
    var billed_amt = comon_selector['billed'].val();
    allowed = comon_selector['allowed'];
    secondary = comon_selector['secondary'];
    billedAmt = comon_selector['billed'];
    var balance_secondary = comon_selector['balancesecondary'].val();
    var paidamt = comon_selector['paid_amt'].val();
    var balance = parseFloat(balance_secondary) - parseFloat(paidamt);
    type = comon_selector['type'];
    paid_amt = comon_selector['paid_amt']; 
    var adjustment;
    var existpaidamt = $('input[name="' + current_name + '"]').attr('data-paid');
    var cur_paidamt = $('input[name="' + current_name + '"]').val();    
    //balance_secondary = balance;   // Hided because secondary payment adjustment amount cant enter
    var attr_paid_amt = (typeof existpaidamt != 'undefined') ? existpaidamt : "";
    if (cur_paidamt < 0 && Math.abs(parseFloat(cur_paidamt)) > parseFloat(attr_paid_amt)) {
        js_alert_popup("Entered amount exceeds paid amount");
        $('input[name="' + current_name + '"]').val("0.00");
    }    
    if (type == "Payment") {
        var allowed_amt = allowed.val();  // Primary insurance payment
        var billed_amt = billedAmt.val();
        if (secondary.length) {   // we use this for secondary insurance payment
            billed_amt = (balance_secondary < 0) ? 0 : balance_secondary;
        }
        if (allowed.val() != '' && parseFloat(allowed.val()) > parseFloat(billedAmt.val())) {
            // If the entered allowed values exceeds billed make allowed equals to billed
            js_alert_popup("Enter amount less than billed or balance due"); console.log("4306");
            allowed.val(billed_amt);
            adjustment = 0;
        }
        adjustment = (adjustment != 0) ? billed_amt - allowed_amt : adjustment;
        var adjustment = (parseFloat(allowed_amt) >= parseFloat(0) && allowed_amt != '') ? adjustment : "0.00";
        adjustment = (current_name.indexOf('cpt_allowed_amt') > -1)?parseFloat(adjustment).toFixed(2):comon_selector['adjustment'].val();
        comon_selector['adjustment'].val(adjustment);  // Adjustment amount calculations and display
        coins = comon_selector['coins'];
        copay = comon_selector['copay'];
        deductable = comon_selector['deductable'];
        withheld = comon_selector['withheld'];
        var total_other_paid = Number(copay.val()) + Number(deductable.val()) + Number(withheld.val()) + Number(coins.val());
        if (parseFloat(allowed_amt) < total_other_paid) {
            withheld.val("0.00");
            deductable.val("0.00");
            coins.val("0.00");
            copay.val("0.00");
        }   	
        calculationpaid(i, classval, curr);
    } else if (type == "Adjustment") {
        var adjusted_already = comon_selector['insurance_adjustment'].val();
        adjustment = comon_selector['adjustment'];
        withheld = comon_selector['withheld'];
        adjustment_val = adjustment.val();
        withheld_val = withheld.val();
        if (adjustment_val < parseFloat("0.00") && parseFloat(Math.abs(adjustment_val)) > parseFloat(Math.abs(adjusted_already))) {
            js_alert_popup("Reversal amount exceeds your actual adjustments.");
            adjustment.val("0.00");
        }
        if (secondary.length)    // we use this for secondary insurance payment
            billed_amt = isNaN(balance_secondary) ? 0 : balance_secondary;         
        if (parseFloat(Number(adjustment_val)+Number(withheld_val)) > parseFloat(billed_amt) && parseFloat(billed_amt)>0) {
            js_alert_popup("Adjustment amount greater than billed or balance amount.");
            adjustment.val('');
            withheld.val('');
            _CancelAdj(i);
        } else if(balance_secondary<=0&&parseFloat(Number(adjustment_val)+Number(withheld_val))>0) {
            //console.log("adjustment data");
            $('.js-display-error').show();
            adjustment.val(0.00);
            withheld.val(0.00);
            setInterval(function(){ $('.js-display-error').slideUp(); }, 1000);
        }
        var adjustment = !isNaN(adjustment.val()) ? adjustment.val() : 0;
        var withheld = !isNaN(withheld.val()) ? withheld.val() : 0;
        var balance = !isNaN(billed_amt) ? billed_amt : 0;
        var balance = balance - (Number(adjustment)+Number(withheld));
        comon_selector['balance'].val(parseFloat(balance).toFixed(2));
        calculateamount(i);
    } else if (type == "Refund") {
        calculaterefundamount(i);
    }
    calculatetotalbalance(i);
}

// This function is used to calculate total amount and patient balance and insurance balance starts here
function calculatetotalbalance(i) {
    calculatelineitemamount(i);
    patientbalancenew();
}

function calculatelineitemamount(i) {
    var val = ['js-cpt-billed', 'js-cpt-allowed', 'js-paid-amt', 'js-coins', 'js-copay', 'js-deductible', 'js-withheld', 'js-balance', 'js-adjust', 'js-withheld'];
    $.each(val, function (index, value) {
        var tot_charge = 0;
        $('.' + value).each(function () {
            tot_charge += Number($(this).val());
        });
        tot_charge = parseFloat(tot_charge).toFixed(2);
        $('span#' + value).html(price_format + " " + tot_charge);
        $('#' + value).val(tot_charge);
        if (value == "js-balance")
            $('.js-totaldue').html(price_format + " " + tot_charge);
    });
}

function changelabel(){
    var type = $('input[name="payment_type"]').val();
    if(type == "Refund"){
        $('.js-paid-label').html("Refund Amt")
    }
}

function patientbalancenew() {
    var deductibile = 0;
    var copay = 0;
    var coins = 0;
    var payment_type = $('#js-insurance').val();
     var type = $('input[name="payment_type"]').val();
    $('.js-deductible').each(function () {
        deductibile += Number($(this).val());
    });
    $('.js-copay').each(function () {
        copay += Number($(this).val());
    });
    $('.js-coins').each(function () {
        coins += Number($(this).val());
    });
    var patient_due = deductibile + copay + coins;
    if(type == "Payment" || type == "Refund") {
      balancecalculation();  
    } else{
      insurancebalancenew(patient_due)
    }       
}

function insurancebalancenew(patient_due) {
    var allowed_amt = 0.0;
    var insurance_due = 0.0;
    var paid_amt = 0.0;
    var withheld = 0.0;
    var balance_amt = 0.0;
    var adjustment = 0.0;
    var type = $('input[name="payment_type"]').val();
    var payment_type = $('#js-insurance').val();
    var insurance_id = $('input[name="insurance_id"]').val();
    $('.js-cpt-allowed').each(function () {
        var attr_id = $(this).attr('dataid');
        if ($(this).val() != 0 && typeof $(this).val() != 'undefined') {
            allowed_amt += Number($(this).val());
        } else {
            if ($('input[name="balance_secondary[' + attr_id + ']"]').length) {
                allowed_amt += Number($('input[name="balance_secondary[' + attr_id + ']"]').val());
            } else {
                allowed_amt += Number($('input[name="cpt_billed_amt[' + attr_id + ']"]').val());
               // console.log("allowed" + allowed_amt)
            }
        }
    });
    allowed_amt = isNaN(allowed_amt) ? 0 : allowed_amt;
    $('.js-withheld').each(function () {
        withheld += Number($(this).val());
    });
    $('.js-paid-amt').each(function () {
        paid_amt += Number($(this).val());
    });
    $('.js-balance').each(function () {
        balance_amt += Number($(this).val());
    });
    $('.js-adjust').each(function () {
        adjustment += Number($(this).val());
    });
    if (type == "Refund") {
        allowed_amt = balance_amt;
    }
    //allowed_amt = balance_amt;
    var add_amt = parseFloat(withheld) + parseFloat(paid_amt) + parseFloat(patient_due);
    var add_amt = add_amt.toFixed(2);    //alert("patient_due"+patient_due);
    if (payment_type == "patient" && balance_amt >0) { 
        patient_due = balance_amt;        
    } else if (payment_type != '' && payment_type != "patient" || type == "Adjustment" || insurance_id != '' && typeof insurance_id != 'undefined') {       
       // if (payment_type != '') commented because when change responsibility at over payment it make error
         //   patient_due = 0;
        insurance_due = (type == "Refund") ? allowed_amt : (parseFloat(allowed_amt) - (parseFloat(add_amt)));    
        if (type == "Adjustment")
            insurance_due = balance_amt;
    } else {       
        insurance_due = (type == "Refund") ? allowed_amt : allowed_amt - (parseFloat(withheld) + parseFloat(paid_amt) + parseFloat(patient_due));
        if (type == "Adjustment")
            insurance_due = balance_amt;
    }    
    if(insurance_due == 0){
        insurance_due = Math.abs(insurance_due);
    }
    if(patient_due == 0){
        patient_due = Math.abs(patient_due);
    }
    if(adjustment <0 && type == "Payment" && payment_type != "patient"){
        insurance_due = balance_amt;
    }
    $('input#js-insurancedue').val(insurance_due);
    insurance_due = parseFloat(insurance_due).toFixed(2);
    if(insurance_due.indexOf('-') >-1){
	   insurance_due = "<span class='med-red'>"+parseFloat(insurance_due).toFixed(2)+'</span>'; 
	}
    $('.js-insurancedue').html(insurance_due);
    $('.js-patientdue').html(price_format + " " + parseFloat(patient_due).toFixed(2));
    $('input#js-patientdue').val(patient_due);
}

// This function is used to calculate total amount and patient balance and insurance balance ends here
function calculationpaid(i, classval, curr) {
    var current_name = $(curr).attr('name');
    comon_selector = commonselector(i, classval, curr);
    var insurance_paid = comon_selector['insurance_paid'].val();
    var paid_amt = comon_selector['paid_amt'].val();    
    if (type == "Payment" && paid_amt < 0) {
        // -- Commented for "Allow to takeback from other than payment posted cheque" - on 09-05-2019

        if (parseFloat(Math.abs(paid_amt)) > parseFloat(insurance_paid)) {
            js_alert_popup("Reversal amount exceeds your actual payments.");
            comon_selector['paid_amt'].val("0.00");
            return false;
        }
        
    }
    if ($('input[name="' + current_name + '"]').hasClass('js-paid-amt')) {
        var paid_val = $('input[name="' + current_name + '"]').val();
        isNaN(paid_val) ? $('input[name="' + current_name + '"]').val("0.00") : "";
        unappliedamountcalc(i);
    }
    var withheld = comon_selector['withheld'].val();
    copay = comon_selector['copay'];
    deductable = comon_selector['deductable'];
    coins = comon_selector['coins'];
    var balance_secondary = comon_selector['balancesecondary'].val();
    secondary = comon_selector['secondary'];
    type = comon_selector['type'];
    var paid_amt = comon_selector['paid_amt'].val();
    var payamt = comon_selector['paid_amt'];
    var adjustment = comon_selector['adjustment'].val();
    var total_other_paid = Number(copay.val())+Number(deductable.val())+Number(withheld)+ Number(coins.val()) +Number(paid_amt);
   
    // When the dedcutibile and copay , coinsurance,paid goes more than allowed we clear the deductible, copay, information starts here
    /*
    var total_other_paid = Number(copay.val())+Number(deductable.val())+Number(withheld)+ Number(coins.val()) +Number(paid_amt);
     if(comon_selector['allowed'].val() < total_other_paid) {     
         comon_selector['withheld'].val("0.00");
         deductable.val("0.00");
         coins.val("0.00");
         copay.val("0.00");
     }
     */
    if (type == "Payment" && parseFloat(paid_amt) < parseFloat("0.00")) {
        //comon_selector['withheld'].val("0.00");
        //deductable.val("0.00");
        //  coins.val("0.00");
        //  copay.val("0.00");
        //  comon_selector['allowed'].val("0.00");
    }

    if ($('.js-insurance-list').val() == 0 && paid_amt > 0 && type != 'Refund') {
        comon_selector['paid_amt'].val(0.00);
        js_alert_popup("Claim responsibility was in patient, Insurance payment cant be done");
        $('input[name="insurance_unapplied_amt"]').val($('input[name="payment_unapplied_amt"]').val());
        return false;
    }
    //balance_secondary = (balance_secondary<0)?0:balance_secondary; // For excess payment the balance was in negative
    billedamt = (!secondary.length) ? comon_selector['billed'].val() : balance_secondary;  // Take the billed amount when allowed has given
    if (type == "Payment") { 
        var allowed_amt = (comon_selector['allowed'].val() == 0) ? billedamt : comon_selector['allowed'].val();

        if (secondary.length && classval != 'js-cpt-allowed' && allowed_amt <= 0 && (typeof balance_secondary != 'undefined' || balance_secondary != '')) {   // we use this for secondary insurance payment
            allowed_amt = balance_secondary;
        } 
        if (comon_selector['allowed'].val() == parseFloat(0) && adjustment != parseFloat(0) || payamt.val() < parseFloat(0) && adjustment != parseFloat(0)) { 
            var balance_val = (!secondary.length) ? comon_selector['balance_original'].val() : balance_secondary;           
           // balance_amt = balance_val - Number(total_other_paid);
            balance_amt = balance_val - (Number(payamt.val()) + Number(adjustment));
        } else {
            //balance_amt = allowed_amt - Number(total_other_paid);
            balance_amt = allowed_amt - (Number(payamt.val()) + Number(withheld));
        }  
       
        var balance_amt = parseFloat(balance_amt).toFixed(2);
        //comon_selector['balance'].val(balance_amt); return false;
        comon_selector['balance'].val(balance_amt);
        if (balance_amt < 0 && $(curr).hasClass('js-paid-amt') && paid_amt>0)
            js_alert_popup("Paid amount exceeds the check value or unapplied amount.");
    } else if (type == "Refund") {
        //(paid_amt>0)?comon_selector['paid_amt'].val(-1*paid_amt):"";
        (paid_amt > 0) ? comon_selector['paid_amt'].val(paid_amt) : "";
        calculaterefundamount(i)
    }
    calculatetotalbalance(i)
}

function balancecalculation(i,allowed_amt){
    //console.log("allowed amount"+allowed_amt);
    var length = $('.js-copay').length;
    var type = $("#js-insurance").val(); 
    var pmt_type = $('input[name="payment_type"]').val(); 
   // console.log("pmt_type"+pmt_type);
    var pat_balace_new = ins_balance_new = 0;
    for(i=0; i<length;i++){
        var pat_balace = ins_balance = 0;
        var common_values = commonselector(i);
        var allowed_amt = common_values['allowed'].val();
        var billed = (typeof common_values['balancesecondary'].val() != "undefined")?common_values['balancesecondary'].val():common_values['billed'].val();
        var allowed_amt = (parseFloat(allowed_amt) == 0)?billed:billed;
        var copay = common_values['copay'];
        var coins = common_values['coins'];
        var paid_amt = common_values['paid_amt'];
        var adjustment = common_values['adjustment'];
        var deductable = common_values['deductable'];
        var withheld = common_values['withheld'];
        var balance_amt = common_values['balance'].val();
        var pat_balace = Number(copay.val())+Number(deductable.val())+Number(coins.val()); 
       // console.log("balance_amt before"+balance_amt); 
        if(pmt_type == "Payment"){
            var ins_balance = allowed_amt - (pat_balace+Number(withheld.val()) +Number(paid_amt.val()) +Number(adjustment.val()));        
        } else{
            balance_amt = parseFloat(balance_amt);
            var ins_balance = (typeof balance_amt != "undefined" && !isNaN(balance_amt))?balance_amt.toFixed(2):"0.00";        
        }
       // console.log("balance_amt"+balance_amt);
        if(ins_balance < 0 && type=="patient"){
            ins_balance_new = parseFloat(ins_balance)+parseFloat(ins_balance_new);
            pat_balace_new = parseFloat(pat_balace) + parseFloat(pat_balace_new);  
        } else if(ins_balance > 0 && type=="patient"){
            ins_balance_new = (ins_balance_new<0)?ins_balance_new:0;
            pat_balace_new = parseFloat(pat_balace) + parseFloat(pat_balace_new)+parseFloat(ins_balance); 
        }else if(ins_balance < 0 && type!="patient"){
            ins_balance_new = parseFloat(ins_balance)+parseFloat(ins_balance_new);
            pat_balace_new = pat_balace;
        } else {
             pat_balace_new = parseFloat(pat_balace) + parseFloat(pat_balace_new);      
            ins_balance_new = parseFloat(ins_balance) + parseFloat(ins_balance_new); 
        }        
    } 
    var total_balance = parseFloat(ins_balance_new)+parseFloat(pat_balace_new);      
    ins_bal = "'"+ins_balance_new+"'";
    tot_bal = "'"+total_balance+"'";   
    if(typeof ins_balance_new != "undefined" && ins_bal.indexOf('-') >-1){
       $('.js-insurancedue').addClass('med-red');
    } else {
        $('.js-insurancedue').removeClass('med-red'); 
    }
    if(typeof total_balance != "undefined" && tot_bal.indexOf('-') >-1){
       $('.js-totaldue').addClass('med-red').removeClass('med-orange');
    } else {
        $('.js-totaldue').addClass('med-orange').removeClass('med-red'); 
    }
   // console.log("final ins"+ins_balance_new);
    ins_balance_new = parseFloat(ins_balance_new).toFixed(2);
    pat_balace_new = parseFloat(pat_balace_new).toFixed(2);
    total_balance = parseFloat(total_balance).toFixed(2);
    if(total_balance==-0.00){
        total_balance = 0;
        total_balance = parseFloat(total_balance).toFixed(2);
        $('.js-totaldue').addClass('med-orange').removeClass('med-red'); 
    }
    if(ins_balance_new==-0.00){
        ins_balance_new = 0;
        ins_balance_new = parseFloat(ins_balance_new).toFixed(2);
        $('.js-insurancedue').removeClass('med-red'); 
    }
	
	/* Added for when total balance is zero or negative value current insurance selected  */
    if(total_balance == '0.00' || total_balance <= 0.00){
		var insurance_category_Type = '';
		var insurance_id = $('#js-insurance-list').val();
		
		if($('input[name="change_insurance_category"]').val() != '' && $('input[name="change_insurance_category"]').val() != undefined){
			insurance_category_Type = $('input[name="change_insurance_category"]').val();
		}else if($('input[name="other_pmt_ins_cat"]').val() != '' && $('input[name="other_pmt_ins_cat"]').val() != undefined){
			insurance_category_Type = $('input[name="other_pmt_ins_cat"]').val();
		}else{
			if($('input[name="primary"]').val() == 'Primary-'+insurance_id)
				insurance_category_Type = "Primary";
			else if($('input[name="secondary"]').val() == 'Secondary-'+insurance_id)
				insurance_category_Type = "Secondary";
			else if($('input[name="tertiary"]').val() == 'Tertiary-'+insurance_id)
				insurance_category_Type = "Primary";
		}
		$("#js-insurance").select2("val", insurance_category_Type+"-"+insurance_id);
	}else{
		//$("#js-insurance").select2("val", "");
	}
	
    $('.js-insurancedue').html(ins_balance_new)
    $('.js-patientdue').html(pat_balace_new)
    $('.js-totaldue').html(total_balance)
}

// This is the function called when we change adjustment values starts here
function allowedfromadjustment(i, classval, curr) {
    comon_selector = commonselector(i, classval, curr);
    var billed_amt = comon_selector['billed'].val();
    var paidamt = comon_selector['paid_amt'].val();
    var balance_secondary = comon_selector['balancesecondary'].val(); 
    var current_name = $(curr).attr('name');
    var existpaidamt = $('input[name="' + current_name + '"]').attr('data-paid'); 
    var cur_paidamt = $('input[name="' + current_name + '"]').val();
    var attr_paid_amt = (typeof existpaidamt != 'undefined') ? existpaidamt : "";
    if (cur_paidamt < 0 && Math.abs(parseFloat(cur_paidamt)) > parseFloat(attr_paid_amt)) {
         $('input[name="' + current_name + '"]').val("0.00");
        js_alert_popup("Entered amount exceeds adjusted amount");
    }  
     var adjustment = comon_selector['adjustment'].val();
    var balance = (!isNaN(balance_secondary)) ? parseFloat(balance_secondary) - parseFloat(paidamt) : billed_amt;
    var allowed_calc = billed_amt - adjustment;    
    //var data_paid = $(curr).attr('data-paid');
    // Take back concept implemented starts here
    //if(balance_secondary ==parseFloat(0) || typeof balance_secondary == 'undefined' || balance_secondary == ''&& balance != parseFloat(0)){       
    if(current_name.indexOf("adjustment") <0)
    balance_secondary = balance;
    //}
    // Take back concept implemented ends here
    if (balance_secondary != '' && typeof balance_secondary != 'undefined' && balance_secondary != 0) {
        var allowed = balance_secondary - adjustment;
        allowed_calc = (parseFloat(adjustment) <= parseFloat(balance_secondary) && parseFloat(allowed) > 0) ? allowed : 0;
        billed_amt = balance_secondary;
    }
    if ((balance_secondary <= 0 && attr_paid_amt)) {
        allowed_calc = 0;   // When the balance was zero or in negative we should not allow them to adjust
        adjustment = (adjustment < 0) ? adjustment : 0;
        comon_selector['adjustment'].val(adjustment);
    }
    allowed_calc = (allowed_calc >= 0) ? parseFloat(allowed_calc).toFixed(2) : parseFloat(billed_amt).toFixed(2);     
    comon_selector['allowed'].val(allowed_calc);
    if (adjustment >0 && parseFloat(adjustment) > parseFloat(billed_amt)) {
        //console.log("adjustment amount"+parseFloat(adjustment));
        //console.log("billed_amt amount"+parseFloat(billed_amt));
       // console.log("comes inside");
        comon_selector['adjustment'].val(0);
    }
    setTimeout(function () {
        calculationpaid(i, classval, curr);
    }, 100);
}

// This is the function called when we change adjustment values ends here
function calculatepatientbalance() {
    //
}
// This function is called to check whether amount available in unapplied or not starts here

function unappliedamountcalc(i) {
    var paid_amt = 0;
    var unapplied = 0;
    original_unapplied_amt = $('input[name="payment_unapplied_amt"]').val();
    paid_amt = paideachamt('js-paid-amt');
    paid_amt = isNaN(paid_amt) ? 0 : paid_amt;
    unapplied = original_unapplied_amt - paid_amt;
    if ((parseFloat(unapplied).toFixed(2)) < parseFloat(0)) {
        // $('input[name="paid_amt['+i+']"]').val("");
        paid_amt = paideachamt('js-paid-amt');
        // js_alert_popup(unappied_err_msg);
        js_alert_popup(checkamount_excedd);
        //unapplied = original_unapplied_amt - paid_amt;
        //$('input[name="insurance_unapplied_amt"]').val(unapplied);
        // return false;
    }
    unapplied = original_unapplied_amt - paid_amt;
    unapplied = unapplied.toFixed(2);
    unapplied = (unapplied == 0) ? Math.abs(unapplied) + ".00" : unapplied;
    $('input[name="insurance_unapplied_amt"]').val(unapplied);
}
// This function is called to check whether amount available in unapplied or not ends here

function paidamountchange(i, classval, curr) {
    setTimeout(function () {
        calculatepaymentamount(i, classval, curr);
    }, 100);
}

function withheldchangeaction(i, classval, curr) {
    withheld = $('input[name="with_held[' + i + ']"]').val();
    adjustment = $('input[name="adjustment[' + i + ']"]').val();
    adjustment_tot = Number(adjustment) + Number(withheld);
    adjustment_tot = parseFloat(adjustment_tot).toFixed(2);
    $('input[name="adjustment[' + i + ']"]').val(adjustment_tot);
    setTimeout(function () {
        calculatepaymentamount(i, classval, curr);
    }, 100);
}

function getpaidamountcalc() {
    patientbal = Number(copay.val()) + Number(deductable.val()) + coins_val;
}

// Bottom line amount calculation starts here
function calculateamount(i) {
    var val = ['js-cpt-billed', 'js-cpt-allowed', 'js-paid-amt', 'js-coins', 'js-copay', 'js-deductible', 'js-withheld', 'js-balance', 'js-adjust'];
    unapplied = $('input[name="payment_unapplied_amt"]');
    payment_type = $('input[name="payment_type"]').val();
    var unapplied_amt = unapplied.val();
    var tot_charge;
    $.each(val, function (index, value) {
        tot_charge = 0;
        $('.' + value).each(function () {
            tot_charge += Number($(this).val());
        });         
        tot_charge = parseFloat(tot_charge).toFixed(2);
        $('span#' + value).html(price_format + " " + tot_charge);
        $('#' + value).val(tot_charge);
        if (value == "js-paid-amt" && payment_type != "Adjustment") {
            var amount = unapplied_amt - tot_charge;
            if (amount < 0) {
                //js_alert_popup(amt_err_msg);
                $('input[name = "paid_amt[' + i + ']"').val('');
                tot_charge = paideachamt('js-paid-amt');
                amt = unapplied_amt - tot_charge;
                $('input[name="insurance_unapplied_amt"]').val(parseFloat(amt).toFixed(2));    // Update unapplied amount on text while entering and removing from tezt box
                $('span#' + value).html(price_format + " " + tot_charge);
                $('#' + value).val(tot_charge);
            } else {
                if (!$('.js-check-remaining').length)   // This made NaN for insurance by default page loading at remaing check amount  posting at main posting screen
                    $('input[name="insurance_unapplied_amt"]').val(parseFloat(amount).toFixed(2));
                $('span#' + value).html(price_format + " " + tot_charge);
                $('#' + value).val(tot_charge);
            }
        } else if (value == "js-paid-amt" && payment_type == "Adjustment") {
            tot_charge = paideachamt('js-adjust');
            tot_charge = parseFloat(tot_charge).toFixed(2);
            withheld = paideachamt('js-withheld');
            withheld = parseFloat(withheld).toFixed(2);
            $('input[name="insurance_unapplied_amt"]').val(Number(tot_charge)+Number(withheld));            
            $('input[name="payment_unapplied_amt"]').val(Number(tot_charge)+Number(withheld));            
            $('input[name="payment_amt"]').val(Number(tot_charge)+Number(withheld));            
        }        
        if(tot_charge != 0 && tot_charge.indexOf('-') >-1 && value == "js-balance"){
           $('.js-totaldue').addClass('med-red').removeClass('med-orange');           
        } else if(value == "js-balance"){
            $('.js-totaldue').addClass('med-orange').removeClass('med-red'); 
        }       
        if (value == "js-balance")
            $('.js-totaldue').html(price_format + " " + tot_charge);
    });
}
// Bottom line amount calculation ends here

function paideachamt(type) {
    var val = 0;
    $('.' + type).each(function () {
        val += Number($(this).val());
    });
    return val;
}

function selectinsurance() {
    curenly_choosen = $('#js-insurance-list').val()  // Currenly choosen insurance details
    next_responsibility = $('#js-insurance').val();
    if (next_responsibility == '') {        // If next responsibility was already choosen do not allow automatic selection
        if (curenly_choosen.indexOf("Primary") > -1) {
            next_choose = "secondary";
        } else if (curenly_choosen.indexOf("Secondary") > -1) {
            next_choose = "tertiary";
        } else {
            next_choose = "patient";
        }
        primary = $('input[name="primary"]').val();
        secondary = $('input[name="secondary"]').val();
        tertiary = $('input[name="tertiary"]').val();
        if (next_choose == "secondary" && secondary != '') {
            $('#js-insurance').val(secondary).change();
        } else if (next_choose == "tertiary" && tertiary != '') {
            $('#js-insurance').val(tertiary).change();
        } else {
            $('#js-insurance').val("patient").change();
        }
    }
}

// Amount insertion part ends here
function calculaterefundamount(i) {
    var insurance_paid = $('input[name="insurance_paid[' + i + ']"]').val();
    var balance_amt = $('input[name="balance[' + i + ']"]').val();
    var paid_amt = $('input[name="paid_amt[' + i + ']"]').val();
    var old_balance = $('input[name="old_balance[' + i + ']"]').val();
    var balance_amt = !isNaN(balance_amt) ? balance_amt : 0;
    var old_balance = !isNaN(old_balance) ? old_balance : 0;
    var paid_amt = (!isNaN(paid_amt) && paid_amt != '') ? -1 * paid_amt : 0;    // Changed becaue of feedback (minus removed from refund)
    if (Math.abs(parseFloat(paid_amt)) > parseFloat(insurance_paid)) {
        js_alert_popup(paid_amt_err_msg);
        var new_balance = parseFloat(old_balance);
        $('input[name="balance[' + i + ']"]').val(parseFloat(new_balance).toFixed(2));
        $('input[name="paid_amt[' + i + ']"]').val(0);
        unappliedamountcalc(i)
        //$('input[name="balance['+i+']"]').val(old_balance);
    } else {
        var new_balance = parseFloat(old_balance) - parseFloat(paid_amt);
        $('input[name="balance[' + i + ']"]').val(parseFloat(new_balance).toFixed(2));
        calculateamount(i);
    }
}

function patientbalance() {
    var payment_type = $('#js-insurance').val();
    var patient_bal = 0;
    $('.js-patient-due').each(function () {
        patient_bal += Number($(this).val());
    });
    var insurance_due = 0;
    $('.js-insurance-due').each(function () {
        insurance_due += Number($(this).val());
    });
    if (payment_type == "patient" || payment_type == '') {
        patient_bal = parseFloat(patient_bal) + parseFloat(insurance_due);
    }
    $('.js-patientdue').html(price_format + " " + parseFloat(patient_bal).toFixed(2));
    $('input#js-patientdue').val(patient_bal);
}

function insurancebalance() {
    var payment_type = $('#js-insurance').val();
    var insurance_due = 0;
    $('.js-insurance-due').each(function () {
        insurance_due += Number($(this).val());
    });
    if (payment_type == "patient" || payment_type == '') {
        insurance_due = 0;
    }
    var patientdue = $('.js-patientdue').html().replace(/[^A-Z0-9-.]/ig, "");
    $('input#js-insurancedue').val(insurance_due);
    var total_balance = parseFloat(insurance_due) + parseFloat(patientdue);
    $('.js-insurancedue').html(price_format + " " + parseFloat(insurance_due).toFixed(2));
    if(total_balance.indexOf('-') >-1){
		$('.js-totaldue').addClass('med-red').removeClass('med-orange');           
	} else {
		$('.js-totaldue').addClass('med-orange').removeClass('med-red'); 
	}
	$('.js-totaldue').html(price_format + " " + parseFloat(total_balance).toFixed(2));
}

$(document).delegate('#js-insurance', 'change', function () {
    insurance_text = $("#js-insurance option:selected").text();
    if (insurance_text.indexOf("Workerscomp") > -1) {
        js_alert_popup("Workerscomp insurance can't be submitted for secondary claims");
    }   
    patientbalancenew();
});

/*** Extract Insurance related information by changing and onchange and onload starts here***/
$(document).delegate('#js-insurance-list', 'change', function () {
    changeinsurance($(this).val());
});

$(document).delegate('#js-insurance', 'change', function () {
    changeinsurance($(this).val(), 'next');
});

function changeinsurance(value, type) {
    if (value) {
        insurance = value.split('-');
        var ins_category = insurance[0]
        var ins_id = insurance[1];
        if (type == 'next') {
            $('input[name="next_insurance_id"]').val(ins_id);
        } else {
            $('input[name="insurance_id"]').val(ins_id);
            $('input[name="insurance_cat"]').val(ins_category);
        }
    } else{     
        $('input[name="next_insurance_id"]').val("");
    }
}

function emptyunrelatetypedata() {
    var arr = ['check_no', 'check_date', 'insurance_unapplied_amt', 'payment_unapplied_amt', 'payment_amt', 'payment_mode'];
    $.each(arr, function (index, value) {
        $('input[name="' + value + '"]').attr('readonly', true).val("");
        if (value == 'payment_mode') {
            $('select[name="' + value + '"]').val("").change();
        }
    });
}

setTimeout(function () {
    //changeinsurance($('#js-insurance-list').val());
    payment_type = $('input[name="payment_type"]').val();
    if (payment_type == 'Adjustment')
        emptyunrelatetypedata();
    makeallfieldsreadonly(payment_type);
}, 200);

// Charges pages copay related hide and show data starts here

$(document).delegate('.js-copay-select', 'change', function () {
    $('.js-hide-money').addClass('hide');
    if ($(this).val() == "Check") {
        $(".js-check-number").trigger("blur");
        $('.js-show-check').removeClass('hide');
        $('.js-show-card-type').addClass('hide');
        $('input[name="check_no"]').attr("readonly", false);
    } else if($(this).val() == "Money Order"){ 
        $('.js-hide-money').removeClass('hide').find(":input").prop("readonly", false);
        $('.js-show-check').addClass('hide');
        $('.js-show-card-type').addClass('hide');
    } else if ($(this).val() == "Credit") {
        $('.js-show-card-type').removeClass('hide');
        $('.js-show-check').addClass('hide');
    } else {
        $('input[name="check_no"]').attr("readonly", true);
        $('input[name="check_no"]').val("");
        $('.js-show-card-type').addClass('hide');
        $('.js-show-check').removeClass('hide');
    }
   /* if ($(this).val() == "Credit") {
        $('.js-show-card-type').removeClass('hide');
        $('.js-show-check').addClass('hide');
    } else {
        $('.js-show-card-type').addClass('hide');
        $('.js-show-check').removeClass('hide');
    }*/
    addStartolabel();
});

// Charges pages copay related hide and show data ends here
function addStartolabel() {
    var copay = $('.js-copay-select option:selected').val();
    $('.js-copay-label').removeClass('star');
    if (copay != '') {
        $('.' + copay).addClass('star');
        $('.jsamt').addClass('star');
    }
}

// Check the validation for anesthesia time starts here
function anesthesiacalculation() {
    var anesthesia_cpt = '';
    $('.js-append-parent li .js-cpt').each(function () {
        if ($(this).val() != '') {
            anesthesia = $(this).attr('data-anesthesia');
            anesthesia_unit = $('input[name="anesthesia_unit"]').val();
            if (typeof anesthesia != 'undefined')
                anesthesia_cpt = anesthesia;
            if (typeof anesthesia_cpt != 'undefined' && anesthesia_unit == '') {
                js_alert_popup("Please Enter anesthesia start and stoptime");
                return false
            } else if (typeof anesthesia_cpt == 'undefined' && anesthesia_unit != '') {
                js_alert_popup("Please Enter anesthesia related CPT");
                return false;
            } else {
                return true;
            }
        }
    });
}

$(document).delegate('#js-insurance-id', 'change', function () {
    var ins_id = $(this).val();
    if (ins_id.indexOf('-') != -1) {
        insurance_split = ins_id.split('-');
        insurance_id = insurance_split[1];
    } else {
        insurance_id = ins_id;
    }
    //getinsuranceid(insurance_id);
    /*var patient_id = $('input[name="patient_id"]').val();
     var selected_insurance = '';
     if($('#js_selected_insurance').length)
     var selected_insurance = $('#js_selected_insurance').val();
     console.log("selected insurance"+selected_insurance);
     //disableclaim(ins_id);
     if(ins_id.indexOf('-') != -1) {
     insurance_split = ins_id.split('-');
     insurance_id = insurance_split[1];
     } else{
     insurance_id = ins_id;
     }
     if(selected_insurance != '' && selected_insurance == insurance_id || selected_insurance != '' && selected_insurance == 0 || selected_insurance != '' && insurance_id == '') {
     return true;
     } else if(selected_insurance != '' && selected_insurance != insurance_id){
     $('#js-insurance-id').val("").select2();
     $('#js-insurance-form').bootstrapValidator('revalidateField', 'insurance_id');
     js_alert_popup("Claim insurance do not match with the check insurance");
     } else{
     if(patient_id != '')
     searchclaim(insurance_id,patient_id)   s
     }*/
});

// Check the validation for anesthesia time ends here
function disableclaim(insurance_id) {
    insurance_split = insurance_id.split('-');
    insurance_id = insurance_split[1];
    $('.js-sel-claim').prop('disabled', false).prop('checked', false);
    $('.js-paid-cal input[name = "insurance_checkbox"]').each(function () {
        $('.js-paid-cal input:not([data-insurance="' + insurance_id + '"])').prop('disabled', true);
    });
}
// function that only enables the selected insurance

// Function that is used to search the claims wiht the selected insurance at payment posting
function searchclaim(insurance, patient)
{
    insurance = (insurance == '') ? "insurance" : insurance;
    var search_url = api_site_url + '/payments/searchclaims/' + insurance + '/' + patient;
    $('input[name="claim_ids"]').val("");
    $.get(search_url, function (data) {
        $('.js_payment').html(data);
        //callicheck();
        /*$('input[type="checkbox"], input[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });*/
    });
}

// Function that is used to search the claims wiht the selected insurance at payment posting

// Main Payment posting starts here
$(document).on('click', ".js-search-patient", function () {
    var next_val = $(this).next().val();
    var type_val = ''
    if (next_val == 1)
        type_val = "popup";
    searchpatientorclaim(type_val);
});

$(document).on('keypress change', '#js-search-val', function (e) {
    if (e.keyCode == 13) {
         var selector = "js-search-patient";
        if($('.js-search-clamorpatient').length) {
            var selector = "js-search-clamorpatient";
        }
        $('.'+selector).trigger('click');
    }
});

$(document).on('keyup', '#js-search-val', function (e) {
    $("#js-search-val").val($(this).val());
});

$(document).on('blur', 'input[name="search_val"]', function () {
    // if($(this).val() != '')
    //searchpatientorclaim();
});

// From main payment poup search used
function searchpatientorclaim(type_val) {
    var search_val = $('#js-search-val').val();
    if (typeof search_val == 'undefined'){
     var search_val = $('#js-posting-search-val').val();
    }
    var sel_val = $('#PatientSearch').val();
    if ($('#js-insurance-list').length) {
        var insurance_id = $('#js-insurance-list').val();
    } else {
        var insurance_id = $('#js-insurance-id').val();
    }
    if (type_val == 'popup') {
        var search_val = $('#choose_claims').find('input[name="search_val"]').val();
        var sel_val = $('#choose_claims').find('#PatientSearch').val();
    }
    if (sel_val == 'dob') {
        //search_val= $('input[name="search_val_date"]').val();
        var search_val = btoa(search_val);
    }
    if (sel_val == 'claim_number' && search_val != '') {
        search_val = search_val + '::' + insurance_id;
    }
    if (sel_val == '' || sel_val == 'undefined') {
        sel_val = 'name'
    }    
    var type = '';
    // To show the spinner still searching of patients or claims start here
    if (search_val != '' && typeof search_val != 'undefined') {
        $(".js-append-mainpayment-table").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
        $(".js-append-mainpayment-table").show();
    }
    // To show the spinner still searching of patients or claims ends here
    if ($('.js-length').length == '')
        var type = "/payment"; // This is to indicate payment popup screen
    var target = api_site_url + '/payments/searchpatient/' + sel_val + '/' + search_val + '' + type;
    var actionurl = api_site_url + '/payments/insurancecreate';   // Action url for insurance posting when we clicks on patient
    $('#payment_credit_balance').val("0.00");   //To make credit balance amount as 0 when search operation
    payment_type_radio = $('input[name=payment_type]:radio:checked').val();
    if (payment_type_radio == 'Credit Balance')
        $('input[name="payment_amt_pop"]').val("0.00");
    var ins_length = $('#js-insurance-list').val();
    form = $('.js-search-patient').closest("form");

    make_checkbox_enable = 0;

    if (form.hasClass('js-patient-paymentform')) {
        make_checkbox_enable = 1;
    }
    if (search_val != '' && typeof search_val != 'undefined') {
        
		hideLoadingImage();
		$.ajax({
            type: 'get',
            url: target,
            success: function (data) {
               
				$('.js-append-mainpayment-table').html(data);
				$("form#js-insurance-form").attr("action", actionurl);
				if ($('.js-length').length == '') {
					// This popup has been opened when we search for the next page only with search option after we post payment
					$("#choose_claims .modal-body").html("<div class = 'js-append-mainpayment-table'>" + data + "</div>");
					callicheck(); // CAll icheck
					$('#choose_claims').modal('show'); // This is used at main payment posting

					if (ins_length) {       // This is to hide and show the length
						$('.js-popupinsuranceadd').show();
						$('.js-popuppatientadd').hide().removeClass('js-popuppatientadd');
					} else {
						$('.js-popuppatientadd').show();
						$('.js-popupinsuranceadd').hide().removeClass('js-popuppatientadd');
					}
				}
				credit_bal = $('#payment_credit_balance').val();
				if (payment_type_radio == 'Credit Balance') {
					$('input[name="payment_amt_pop"]').val(credit_bal);
					$('input[name="payment_amt_calc"]').val(credit_bal);
				}
				callicheck(); // Call icheck
				//$("#js_wait_popup").modal("hide"); // @todo check and remove
				hideLoadingImage();
				var payment_type = $(".js-payment-type").val();
                var payment_mode = $("#js-payment-mode").val();
                var check_no = $(".js-check-number").val();
                if(payment_type == "Payment" && payment_mode == "Check" && check_no != "" && sel_val == 'claim_number'){
                     $('.js-check-number').trigger("blur");
                }
                if($("#js-insurance-id").length && typeof $("#js-insurance-id").length != "undefined" && sel_val == 'claim_number')
                $('input[data-hold ="Hold"]').prop('disabled', true); // For insurance payment hold claims are disabled
            }

        });
        /*$.get(target, function (data) {
            $('.js-append-mainpayment-table').html(data);
            $("form#js-insurance-form").attr("action", actionurl);
            if ($('.js-length').length == '') {
                // This popup has been opened when we search for the next page only with search option after we post payment
                $("#choose_claims .modal-body").html("<div class = 'js-append-mainpayment-table'>" + data + "</div>");
                callicheck(); // CAll icheck
                $('#choose_claims').modal('show'); // This is used at main payment posting

                if (ins_length) {       // This is to hide and show the length
                    $('.js-popupinsuranceadd').show();
                    $('.js-popuppatientadd').hide().removeClass('js-popuppatientadd');
                } else {
                    $('.js-popuppatientadd').show();
                    $('.js-popupinsuranceadd').hide().removeClass('js-popuppatientadd');
                }
            }
            credit_bal = $('#payment_credit_balance').val();
            if (payment_type_radio == 'Credit Balance') {
                $('input[name="payment_amt_pop"]').val(credit_bal);
                $('input[name="payment_amt_calc"]').val(credit_bal);
            }
            callicheck(); // Call icheck
            //$("#js_wait_popup").modal("hide"); // @todo check and remove
            var payment_type = $(".js-payment-type").val();
                var payment_mode = $("#js-payment-mode").val();
                var check_no = $(".js-check-number").val();
                if(payment_type == "Payment" && payment_mode == "Check" && check_no != "" && sel_val == 'claim_number'){
                        $('.js-check-number').trigger("blur");
                }
        });*/

        if (make_checkbox_enable)
            $('.js-sel-claim').prop('disabled', false);
		
		// Create Data table for enable sorting option.
		//$('#js_MainPayment').DataTable().clear().destroy();
		//Create new Datatable
		$('#js_MainPayment').DataTable({ });
		
    } else {
        if(type_val == "popup") {
            if (sel_val == "dob") {
				if ($('.js-append-mainpayment-table .js-error').length == '')
					$("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($('.js-append-mainpayment-table input[name="search_val_date"]'));
            } else {
                if ($('.js-append-mainpayment-table .js-error').length == '')
                    $("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($('.js-append-mainpayment-table input[name="search_val"]'));
            }
        } else if($("div.js-patient-search").length) {
            if (sel_val == "dob") {
				if ($('.js-error').length == '')
					$("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($('input[name="search_val_date"]'));
            } else {
                if ($('.js-error').length == '')
                    $("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($(' input[name="search_val"]'));
            }
        } else {
            if (sel_val == "dob") {
				if ($('#js-next-searchform .js-error').length == '')
					$("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($('#js-next-searchform input[name="search_val_date"]'));
            } else {
                if ($('#js-next-searchform .js-error').length == '')
                    $("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($('#js-next-searchform input[name="search_val"]'));
            }
        }        
    }
}

$(document).on('blur', 'input[name="search_val"], input[name="payment_search_val"]', 'input[name="search_val_date"]', function () {
    if ($(this).val() == '') {
        //if($('.js-error').length == '')
        //$("<span class='med-red js-error'>"+search_msg+"</span>").insertAfter($('input[name="search_val"]'));
    } else {
        $('.js-error').remove();
    }
});

// Selecting patient from the lists displayed when we search patient from popup data starts here
$(document).on('ifToggled click change', '.js-sel-patient', function () { 
    temp_patient_id = $(this).attr("data-id");
    sel_val = "patient";
    var type = '';
    if ($('.js-length').length == '')
        var type = "/payment"; // This is to indicate payment popup screen
    var target = api_site_url + '/payments/searchpatient/' + sel_val + '/' + temp_patient_id + '' + type;
   
    var ins_length = $('#js-insurance-list').val();
    var payment_type = $('input[name=payment_type]:radio:checked').val();    // For patient payment
    if (typeof payment_type == "undefined")
        var payment_type = $('input[name=payment_type_ins]:radio:checked').val();  // For insurance payment
    var patientactionurl = api_site_url + '/payments/create';   // Action url for patientpayment  posting when we clicks on patient
    form = $('.js-search-patient').closest("form");

    // Dynamic append div checkbox enable for patient payment starts here
    make_checkbox_enable = 0;
    if (form.hasClass('js-patient-paymentform') || $('#js-patient-form').length || $('#js-payment').length) {
        make_checkbox_enable = 1;
    }
    // Dynamic append div checkbox enable for patient payment ends here
    // To show the spinner still searching of patients or claims start here
    if (temp_patient_id != '' && typeof temp_patient_id != 'undefined') {
        $(".js-append-mainpayment-table").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
        $(".js-append-mainpayment-table").show();
    }
    type  = payment_type;
   
    // To show the spinner still searching of patients or claims ends here
    $.get(target, function (data) {
      
        $('.js-append-mainpayment-table').html(data);
        $("form.js-patient-paymentform").attr("action", patientactionurl);
        if (ins_length) {       // Thisd is to hide and show the
            $('.js-popupinsuranceadd').show();
            $('.js-popuppatientadd').hide().removeClass('js-popuppatientadd');
        } else {
            $('.js-popuppatientadd').show();
            $('.js-popupinsuranceadd').hide().removeClass('js-popuppatientadd');
        }
        credit_bal = $('#payment_credit_balance').val();
        if (type == "Credit Balance") {
            $('input[name="payment_amt_pop"]').val(credit_bal);
            $('input[name="payment_amt_calc"]').val(credit_bal);
           // $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'payment_amt_pop');
        }

        if (make_checkbox_enable) {
           // $('.js-sel-claim').prop('disabled', false).iCheck('update'); //teporary disabled
        } else {
            //if(payment_type != "Payment")
            //$('input[data-insurance ="Patient"]').prop('disabled', true).iCheck('update');   // For refund and adjsutment make it as enable
        }
        callicheck(); // CAll icheck
        $('input[name="patient_id"]').val(temp_patient_id);
       // var payment_type = $(".js-payment-type").val();
        var payment_type = type; 
        var payment_mode = $("#js-payment-mode").val();
        var check_no = $(".js-check-number").val(); 
       
        if(payment_type == "Payment" && payment_mode == "Check" && check_no != ""){
             $('.js-check-number').trigger("blur");
        } 
        enablewalleturl(payment_type, temp_patient_id);
        if($("#js-insurance-id").length && typeof $("#js-insurance-id").length != "undefined")
         $('input[data-hold ="Hold"]').prop('disabled', true); // For insurance payment hold claims are disabled
        if($('.js-patient-paymentform').length){
            var validator = $('form.js-patient-paymentform').data('bootstrapValidator');
            validator.addField('wallet_refund');    
        }        
       // $("form.js-patient-paymentform").bootstrapValidator('addField',  "wallet_refund");
        $('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'money_order_no');
    }).done(function() {
		
		// Append datatable for enable sorting option
		//$('#js_MainPayment').DataTable().clear().destroy();
		//Create new Datatable
		$('#js_MainPayment').DataTable({ });
		
	});
});

// Selecting patient from the lists displayed when we search patient from popup data ends here
function enablewalleturl(type, wallet_patient_id){ 
    var walet_url = api_site_url +  '/patients/'+wallet_patient_id+'/payments/addtowallet'; 
    var credit_bal_amt = $("#payment_credit_balance").val();   
    if(parseFloat(credit_bal_amt) > 0 && type == "Refund"){
        $(".js-addtowallet").not($('.js-remove-wal')).attr("data-url", walet_url).attr("id", "js-addwallet");
        walet_url = walet_url+"/type";
        $(".js-remove-wal").attr("data-url", walet_url).attr("id", "js-removewallet").show();
        $(".js-show-refund").hide(); //.show();
    } else if(type == "Payment"){
        $(".js-addtowallet").not($('.js-remove-wal')).attr("id", "js-addwallet").attr("data-url", walet_url).show();  
        walet_url = walet_url+"/type";
        $(".js-remove-wal").attr("data-url", walet_url).attr("id", "js-removewallet");  
    }
}

$(document).on('click', '.js-search-clamorpatient', function () {
    var search_val = $('input[name="payment_search_val"]').val();
    var sel_val = $('#Paymentsearch').val();
    var insurance_id = $('input[name="insurance_id"]').val();
    if (search_val != '') {
        if (sel_val == "claim_number") {
            search_val = search_val + "::" + insurance_id;
        }
        if(sel_val == "dob")
            search_val = btoa(search_val);
        var ins_length = $('#js-insurance-id').val(); 
        var type = "payment"; // This is to indicate payment popup screen
        var target = api_site_url + '/payments/searchpatient/' + sel_val + '/' + search_val + '/' + type;
        $("#js_wait_popup").modal("show");
        $.get(target, function (data) {
            $("#choose_claims .modal-body").html("<div class = 'js-append-mainpayment-table'>" + data + "</div>");
            $("#js_wait_popup").modal("hide");
            $('#choose_claims').modal('show');
            addModalClass();
            modelformfocus();
            callicheck(); // CAll icheck
            if (ins_length) {       // This is to hide and show the length
                $('.js-popupinsuranceadd').show();
                $('.js-popuppatientadd').hide().removeClass('js-popuppatientadd');
            } else {
                $('.js-popuppatientadd').show();
                $('.js-popupinsuranceadd').hide().removeClass('js-popuppatientadd');
            }
        });
    } else {
		// Handling multiple time showing error message in payment patient search
		if($('.js-error').length == 0)
			$("<span class='med-red js-error'>" + search_msg + "</span>").insertAfter($(this));
    }
    //fetchinsurancedata("js-insurance-form");
});

function modelformfocus(){
	$('#choose_claims :input:first').focus();
	$('#choose_claims :input:last').on('keydown', function (e) { 
		if ($("this:focus") && (e.which == 9)) {
			e.preventDefault();
			$('#choose_claims :input:first').focus();
		}
	});
}

$(document).on('click', '.js-prevent-redirect', function (e) { 
    e.stopImmediatePropagation();
});
// Main Payment posting starts ends

$(document).on('click', '.js-modalboxopen', function (e) {    
    target_new = $(this).attr('data-target-new');
    target = (typeof target_new != 'undefined')?target_new:$(this).attr('data-target');  
    url = $(this).attr('data-url');
    title_info = "";
    title_info = $(this).attr('data-payment-info');
    title_info_number = $(this).attr('data-payment-info-number');
    payment_type = $(this).attr('data-payment-type');
    addwallet = "add";
    addwallet_name = "";   
    if (typeof title_info_number != 'undefined') {
        $(target + ' .js-replace').html(title_info_number);
    } 
    var payment_url = '';
    if (payment_type == 'Patient') {
        var payment_url = api_site_url + "/payments/create";
    } else if (payment_type == 'Insurance') {
        var payment_url = api_site_url + "/payments/insurancecreate";
    }
    $(target + " .modal-body").html("");
    $(target + " .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
    if (payment_type == "notes")   {
        $(target).find('.modal-md-800').addClass('modal-md').removeClass('modal-md-800');
        $(target + ' .modal-title').html(title_info);
    }
    if (title_info != '' && typeof title_info != 'undefined')
        $(target + ' .modal-title').html(title_info);

    if (payment_type == "notes") {
        $(target).find('.modal-md-800').addClass('modal-md').removeClass('modal-md-800');
        $(target + ' .modal-title').html(title_info);
    }    
    if(target =="#post_payments") {
        $("div#choose_claims .modal-body").html("");
        $("div#choose_claim .modal-body").html("");
    } else if(target =="#choose_claims") {
         $("div#post_payments .modal-body").html("");
    }

    $(target + " .modal-body").load(url, function () {
        $("select.select2.form-control").select2();
       /* $('input[type="checkbox"], input[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });*/
        $confModal = $(target);
        var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $confModal.on('hidden', function () {
            $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
        });
        if (target == "#post_payments") {
            $('.js-append-mainpayment-table').show();
            validatepatientpayment();
            validateinsurancepayment();
        }
        if (payment_url != '' && typeof payment_url != 'undefined') {
            $(this).find('form').attr('action', payment_url);
        }
        if (payment_type == "notes") {
            popuploadedfunction();
        }
    });
    if(target == "#payment_detail")
    $('#payment_detail').modal('show');
     
    removeHash();
});

$(document).on('submit', '#js-payment-search', function (e) {
    if (e.isDefaultPrevented()) {
        //
    } else {
        var formData = $('form#js-payment-search').serialize();
        id = $(this).attr('form-data');
        action_url = $(this).attr("action");
        $("#js_wait_popup").modal("show");
        $.ajax({
            type: 'post',
            url: action_url,
            data: formData,
            success: function (data) {
                $('.js-append-table').html(data);
                $('.fn-searchchk-reset').removeClass('hide');   // For show reset search link in main payment page.
                // Call data table again after search starts here
                searchcheckdatatable();
                // Call data table again after search ends here
                $('#search_check').modal('hide');
                $("#js_wait_popup").modal("hide");
            }
        });
        return false;
    }
});

$(document).on('click', '.js-popupinsuranceadd', function () {
    actionurl = $("form#js-insuranceajax").attr('action');
    var formData = $('form#js-insuranceajax').serialize();
    var payment_type = $('input[name="payment_type"]').val();
    var MaxAllowed = $('input[name="insurance_checkbox"]').attr("data-max");
    var find_length = $('.js-paid-cal').find('input[name="insurance_checkbox"]:checked').length;
    if ($('#js-next-searchform').length) {
        data = fetchinsurancedata("js-next-searchform");  // next page form id
    } else {
        data = fetchinsurancedata("js-insurance-form");
    }
    formdata = formData + '&&' + data;
    claim_ids = $('input[name = "claim_ids"]').val();
    check_popup = $('input[name="change_insurance_id"]').length;
    var change_ins_id = $('input[name="change_insurance_id"]').val();
    if (claim_ids == '') {
        js_alert_popup(min_choose_err_msg);
        return false;
    } else if (check_popup && typeof change_ins_id != 'undefined' && change_ins_id == '') {
        if ($('#js-insurance-id').length) {
            var insurance_id = $('#js-insurance-id').val();
        } else {
            var insurance_id = $('#js-insurance-list').val();
        }
        insurance_value = getinsuranceid(insurance_id);
        var change_ins_id = $('input[name="change_insurance_id"]').val();
        if (typeof insurance_value != 'undefined' && insurance_value != '' && change_ins_id == '') {
            var patient_id = $('input[name="patient_id"]').val();
            getInsuranceList(patient_id)
            return false;
		} else if(change_ins_id == '') {
			// console.log("checking for others");
			// Show popup for choose payment post insurance category
			checkcat_popup = $('input[name="pmt_post_ins_cat"]').length;
			var selPmtOtherInsCat = $("#pmt_post_ins_cat").val();
			if(checkcat_popup && selPmtOtherInsCat == '') {
				if(insurance_id.indexOf('-') != -1) {
					// Patient payment block
					var pmt_post_ins_cat = $('input[name="pmt_post_ins_cat"]').val();
					insurance_split = insurance_id.split('-');
					pmt_ins_cat = insurance_split[0];
					if (typeof pmt_ins_cat != 'undefined' && pmt_post_ins_cat == '' && pmt_ins_cat == 'Others') {			
						$('#patient_insurance_cat_model').modal('show');
						return false;
					}
				} else { 
					// Main payment block
					var patOtherIns = $("#patient_other_ins").val(); 
					var items = patOtherIns.split(/\s*,\s*/); 
					var isContained = items.some(function(v) { return v === insurance_id; });	
					if (typeof patOtherIns !== typeof undefined && isContained) {
						$('#patient_insurance_cat_model').modal('show');
						return false;
					}
				}
			}
			ajaxprocess(actionurl, formdata, payment_type);
        } else {
            ajaxprocess(actionurl, formdata, payment_type);
        }
	} else {
        ajaxprocess(actionurl, formdata, payment_type);
    }
});
// Main payment posting claim  and patient search process we use it starts here

function ajaxprocess(actionurl, formdata, payment_type) {
    $("#js_wait_popup").modal("show");
    $.ajax({
        type: 'post',
        url: actionurl,
        data: formdata,
        success: function (data) {
            $('.js-append-payment').html(data);
            $('#choose_claims').modal('hide');
            $("select.select2.form-control").select2();
            payment_detail_id = $('input[name="payment_detail_id"]').val();
            if (payment_detail_id != '' && typeof payment_detail_id != 'undefined')
                disableinputforcheckdata('js-insurance-data-disable'); // For check already added we ned to disabled the editable option here
            calculateamount();
            makeallfieldsreadonly(payment_type);
            validateinsurancepayment();
            callicheck();
            patientbalancenew();
            $("#js_wait_popup").modal("hide");
            callpaymentdenial();
        }
    });
}
// Main payment posting claim  and patient search process we use it ends here

$(document).on('click', '.js-popuppatientadd', function () {
    var actionurl = api_site_url + '/payments/create';
    if ($('#js-patient-form').length) {
        var formData = $('form#js-patient-form').serialize();
    } else {
        var formData = $('form#js-payment').serialize();
    }   //data = fetchpaymentdata("js-patient-form");
    claim_ids = $('input[name = "claim_ids"]').val();
    var MaxAllowed = $('input[name="insurance_checkbox"]').attr("data-max");
    var find_length = $('.js-paid-cal').find('input[name="insurance_checkbox"]:checked').length;
    formdata = formData + '&claim_ids=' + claim_ids;
    if (claim_ids == '') {
        js_alert_popup(min_choose_err_msg);
        return false;
    } else {
        $('#choose_claims').modal('hide');
        $("#js_wait_popup").modal("show");
        $.ajax({
            type: 'post',
            url: actionurl,
            data: formdata,
            success: function (data) {
                $('.js-append-payment').html(data);
                $("#js_wait_popup").modal("hide");
                $("select.select2.form-control").select2();
                changetext();
                calculatelineitemamount();
                validatepatientpayment('#js-patient-form');
            }
        });
    }
});

function fetchinsurancedata(formname) {  //Form id has been passed here
    insurance_val = $('form#' + formname).find('input[name = "insurance_id"]').val();
    payment_type = $('form#' + formname).find('input[name = "payment_type"]').val();
    billing_provider = $('form#' + formname).find('input[name ="billing_provider_id"]').val();
    payment_mode = $('form#' + formname).find('input[name ="payment_mode"]').val();
    payment_amt = $('form#' + formname).find('input[name ="payment_amt"]').val();
    check_no = $('form#' + formname).find('input[name ="check_no"]').val();
    reference = $('form#' + formname).find('input[name ="reference"]').val();
    check_date = $('form#' + formname).find('input[name ="check_date"]').val();
    payment_detail_id = (typeof $('form#' + formname).find('input[name ="payment_detail_id"]').val() != 'undefined') ? $('form#' + formname).find('input[name ="payment_detail_id"]').val() : "";
    unapplied = $('form#' + formname).find('input[name ="insurance_unapplied_amt"]').val();
    final_val = 'insurance_id=' + insurance_val + '&payment_type_ins=' + payment_type + '&privider_id=' + billing_provider + '&insur_payment_mode=' + payment_mode + '&payment_amt=' + payment_amt + '&check_no=' + check_no + '&check_date=' + check_date + '&payment_detail_id=' + payment_detail_id + '&unapplied=' + unapplied;
    return final_val;
}

function fetchpaymentdata(formname)
{
    payment_type = $('form#' + formname).find('input[name = "payment_type"]').val();
    payment_amt = $('form#' + formname).find('input[name = "payment_amt"]').val();
    payment_mode = $('form#' + formname).find('.js-pay-mode').val();
    check_no = $('form#' + formname).find('input[name = "check_no"]').val();
    check_date = $('form#' + formname).find('input[name = "check_date"]').val();
    bankname = $('form#' + formname).find('input[name = "bankname"]').val();
    card_type = $('form#' + formname).find('input[name = "card_type"]').val();
    card_no = $('form#' + formname).find('input[name = "card_no"]').val();
    name_on_card = $('form#' + formname).find('input[name = "name_on_card"]').val();
    cardexpiry_date = $('form#' + formname).find('input[name = "cardexpiry_date"]').val();
    claim_ids = $('input[name = "claim_ids"]').val();
    patient_id = $('input[name = "patient_id"]').val();
    final_val = 'payment_type=' + payment_type + '&payment_amt=' + payment_amt + '&payment_mode=' + payment_mode + '&check_no=' + check_no + '&check_date=' + check_date + '&bankname=' + bankname + '&card_type=' + card_type + '&card_no=' + card_no + '&name_on_card=' + name_on_card + '&cardexpiry_date=' + cardexpiry_date + '&claim_ids=' + claim_ids + '&patient_id=' + patient_id;
    return final_val;
}

//Next page search includes starts here
$(document).on('click', '#js-next-searchform', function () {
    //
});
//Next page search includes ends here

// Add to wallet concept starts here

addwallet_name = "";
$(document).on('click', '.js-addtowallet', function () {
    //console.log("Add to wallet first addwallet value"+addwallet);
    var action_url = $(this).attr('data-url');
    var attr_id = $(this).attr("id");    
    if(addwallet == "add"){ // Added because this works only if we clicks twice
        addwallet = "";       
        $("#"+attr_id).click();
    }     
    addwallet_name = "addwallet";   
    $('.js-patient-paymentform').bootstrapValidator('validate');
    $('.js-patient-paymentform').on('success.form.bv', function (e) {
        $("#js_wait_popup").modal("show");
        var formData = new FormData($("form.js-patient-paymentform")[0]);
        if(addwallet_name == "addwallet") {     
			setTimeout(function () {             
				$.ajax({
					type: 'post',
					url: action_url,
					data: formData,
					timeout: 0,
					processData: false,
					contentType: false,
					success: function (data) {
						status = data.status;
						amt = data.data;
						message = data.message;
						addwallet_name = ""; 
						if (status == 'success') {
							js_sidebar_notification("success",message);
							$('#choose_claims').modal('hide');
							$('#post_payments').modal('hide');
							$('#js-model-popup-payment').modal('hide');
							$("#js_wait_popup").modal("hide");
							$('.js-wallet-bal').text(amt)
							$('.js-wallet-bal-header').text(amt)
						} else {
							js_sidebar_notification("error",message);
							$("#js_wait_popup").modal("hide");
						}
					}
				});
			}, 100);
        }
    });
});

function disableinputforcheckdata(selector) { 
    //$('.'+selector).find(':input').attr('readonly', true);
    $('.' + selector).find(':input').not('input[name=posting_date], input[name=wallet_refund]').attr('readonly', true);
    $('.' + selector).find('select').attr('disabled', true);
    $('.'+selector).find('input[type=radio]').attr('disabled', false);
}
// Add to wallet concept ends here

// Search check related works starts here
$(document).on('change', '.js-search-check', function () {
    value = $('.js-search-check option:selected').text();
    $('.js-insurance-dropdown').hide();
    $('.js-search-date').hide();
    if ($(this).val() == 'check_date' || $(this).val() == 'created_at') {
        $('.js-search-date').show();
        $('.js-name').hide();
    } else if ($(this).val() == 'insurance_name') {
        $('.js-insurance-dropdown').show();
        $('.js-name').hide();
    } else {
        $('input[name="name"]').val("");
        $('input[name="search_date"]').val("");
        $('.js-name').show();
    }
    $('form#js-payment-search').bootstrapValidator('revalidateField', 'name');
    $('form#js-payment-search').bootstrapValidator('revalidateField', 'search_date');
    if ($(this).val() == '') {
        $('.js-name').hide();
    } else {
        $('.js-change-label').text(value);
    }
});

//Search check related works ends here

// Make the check related fields as readonly at payment page
if ($('input[name="payment_detail_id"]').length && $('input[name="payment_detail_id"]').val() != '' && typeof $('input[name="payment_detail_id"]').val() != 'undefined') {
    disableinputforcheckdata('js-insurance-data-disable');
    disableinputforcheckdata('js-paymenttakebackdisable');
}

// Make the check related fields as readonly at payment page
//$(document).on('ifToggled click', '.js-search-claim', function () {
$(document).on('ifChecked click change', '.js-search-claim', function () {
    if ($(this).val() == "All") {
        $('.js-search-claim').not($(this)).attr('checked', false);
    } else if ($(this).val() == "pending" || $(this).val() == "Paid") {
        $("input[type=radio][value='All']").prop("checked", false);
    }
    var paymentdetail = $('input[name="paymentdetail"]').val();
    var payment_type = $('input[name=payment_type]:radio:checked').val();    // For patient payment
    var pay_method = $('input[name="payment_method"]').val();
    var insurance_id = $('input[name="insurance_id"]').val();
    if (typeof payment_type == "undefined")
        var payment_type = $('input[name=payment_type_ins]:radio:checked').val();  // For insurance payment
    form = $('.js-search-patient').closest("form");
    // Dynamic append div checkbox enable for patient payment starts here
    make_checkbox_enable = 0;
    if (form.hasClass('js-patient-paymentform') || $('#js-patient-form').length) {
        make_checkbox_enable = 1;
    }
    var status = [];
    $('.js_payment input[name=claim_paid]').each(function () {
        if (this.checked) {
            status.push(this.value);
        }		
    });
    type = "search";
    search_val = $("#js_patient_id").val();
    if ($('.js-length').length == '')
        var type = (insurance_id != '' && typeof insurance_id != 'undefined') ? "payment::" + insurance_id : "payment";
    sel_val = "patient";
	if (status == '')
        status = "alll";
    status = status;
	var target = api_site_url + '/payments/searchpatient/' + sel_val + '/' + search_val + '/' + type + '/' + status;
    $(".js-append-mainpayment-table").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
	$.get(target, function (data) {
		$('.js-append-mainpayment-table').html(data);
		if (!make_checkbox_enable && payment_type != "Payment") {
            $('input[data-insurance ="Patient"]').prop('disabled', true);   // For refund and adjsutment make it as enable
			// This is given due to two continue button added while searching starts here
			if (pay_method == "Patient" && paymentdetail == 1) {
				$('.js-popupinsuranceadd').hide();
			} else if (paymentdetail == 1 && (pay_method == '' || typeof pay_method == 'undefined')) {
				$('.js-popuppatientadd').hide();
			}
        }
		
		$('input[data-hold ="Hold"]').prop('disabled', true); // For insurance payment hold claims are disabled
		// This is given due to two continue button added while searching ends here
		callicheck();
	}).done(function() {
            //Create new Datatable
            $('#js_MainPayment').DataTable({ });
        });
});

// Reset link work for patient search at popup starts here
$(document).on('click', '.js-reset-patient', function (e) {	
	$('input[name ="search_val"]').val("");
	$('.js-append-mainpayment-table').html("");
	$('input[name="payment_amt_pop"]').val("");
	$('#PatientSearch').val("name").select2();
	$('.js-show-datepicker').hide();
	$('.js-hide-datepicker').show();
	$('input[name="search_val_date"]').val("").change();
	$('.js-error').remove();
	var formId = $(this).parents("form").attr("id");    
	$('form#'+formId).trigger("reset"); //.data('bootstrapValidator').resetForm();
	// $('form#'+formId).find("input[type=text], textarea").val(""); // If needs enable this for reset field values.
	e.preventDefault();
	$('.js-addtowallet').hide().removeAttr("id");
});
// Reset link work for patient search at popup ends here

//Edit payment check related data starts here
$(document).on('submit', '#js-editcheck-form', function (e) {
    action_url = $(this).attr('action');
    var formData = new FormData($("#js-editcheck-form")[0]);
    formData.append('temp_doc_id', 1);
    if (e.isDefaultPrevented()) {
    } else {
        $("#js_wait_popup").modal("show");
        action_url = $(this).attr("action");
        $.ajax({
            type: "POST",
            url: action_url,
            data: formData,
            timeout: 0,
            processData: false,
            contentType: false,
            success: function (data) {
                // To display check amount ans posted amount and balances immediately to the popup we used this
                data = JSON.parse(data);
                url = data.url;
                if (data.id != '') {
                    var payamt = data.payment_amt;
                    var amtused = data.amt_used;
                    var balance = data.balance;
                    var checkType = data.pmt_type;
                    $('.js-check-amt').html(payamt);
                    $('.js-amt-used').html(amtused);
                    $('.js-unapplied-amt').html(balance);
                    $('.js-bal-amt').html(balance);
                    $('.js-check-type').html(checkType);
                    if (url != null) {
                        $('a.js_change_attchurl').attr('href', url);
                    }
                }
                /*$.get(api_site_url + '/payments/paymentsList', function (listData) {
                    $('#search_table_payment > tbody').html(listData.data);
                    // Call data table again after search starts here  
                     //searchcheckdatatable();
                    $("#js_wait_popup").modal("hide");
                    $('#payment_editpopup').modal('hide');
                });*/
                table_sel = $('tr[data-id="js_pmt_'+data.pmt_no+'"]');
                table_sel.find('.js_check_pmtamt').html(payamt);
                table_sel.find('.js_check_used').html(amtused);
                table_sel.find('.js_check_bal').html(balance);
				$('.js-search-filter').click();
                $("#js_wait_popup").modal("hide");
				$('#payment_editpopup').modal('hide'); 
				js_sidebar_notification('success',"Updated successfully"); 
            }
        });
        return false;
    }
});

function searchcheckdatatable(){
    var table = $('#search_table_payment').DataTable({"paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
             }); 
    table.on( 'draw', function () {
        var body = $( table.table().body() );
        body.unhighlight();
        body.highlight( table.search() );  
    });    
}
//Edit payment related check data ends here

// when we reload the page that have check box checked already we need update it for icheck starts here
$(document).ready(function () {
    setTimeout(function () {
        /*$('input[type="checkbox"], input[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });*/
        if ($('.js-insurance-list').length) {
            insurance_val = $('.js-insurance-list').val();
            type = $('input[name="payment_type"]').val();
            if (insurance_val == 0) {
                if (type == "Refund") {
                    $('.js-disable-div').find(":input").not('input[name^=paid_amt]').addClass('class-readonly').attr('readonly', true);
                    $('.js-disable-div:input').not('input[name^=paid_amt]').parent('td').addClass('class-readonly');
                } else {
                    $('.js-disable-div').find(":input").addClass('class-readonly').attr('readonly', true);
                    $('.js-disable-div:input').parent('td').addClass('class-readonly');
                }
                //When responsibility was in patient they can't enter amount
            }
        }
    }, 200);
});
// when we reload the page that have check box checked already we need update it for icheck ends here

$(document).on('ifToggled click', '.js_active_lineitem', function (e) {
    name = $(this).attr('name');
    id_val = $(this).attr('data-id');
    /*if($(this).prop('checked') == false){
     val = window.confirm("If you disable this check box it will be omited on claim processing");
     console.log("value"+val);
     if(!val){
     $('input[name="active_lineitem['+id_val+']"]:checkbox[data-id="'+id_val+'"]').prop("checked", true);
     $('input[name="active_lineitem['+id_val+']"]:checkbox[data-id="'+id_val+'"]').iCheck('update');
     }
     } */
    var isDisabled = $('.js-save').prop('disabled');
    if (isDisabled) {
        $('.js-save').prop('disabled', false);
        $('.js-next').prop('disabled', false);
    }
})

$(document).on('ifToggled click', 'input[name ="active_lineitem"]', function (e) {
    var isDisabled = $('.js-save').prop('disabled');
    if (isDisabled) {
        $('.js-save').prop('disabled', false);
        $('.js-next').prop('disabled', false);
    }
})

$(document).on('submit', '.js_insurance_create_form', function () {
    checked_length = $('input:checkbox.js_active_lineitem:checked').length;
    if (checked_length == 0) {
        js_alert_popup("minimum one line item should get selected");
        return false;
    }
    return true;
})

// Use arrow keys to go to next cell.
var active;
$(document).keydown(function (e) {
    taget_id = $(e.target).attr("id"); // To prevent the dropdown issues
    var modal_open_length = $('.modal:visible').length   
    if ((e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40) && taget_id != "js-refer-provider" && !modal_open_length) {
        var get_position = $('td.active').children().attr("data-postition");
        var get_top_position = $('td.active').parents('.billing-grid').attr('id');
        var get_bottom_position = $('td.active').parents('.billing-grid').next().find('.js-icd-highlight').attr("disabled");
        active = $('td.active').removeClass('active');
        var x = active.index();
        var y = active.parents('li').index();
    }

    if (e.keyCode == 37 && get_position != 'left_first_row') {
        x--;
    }
    if (e.keyCode == 38 && get_top_position != 'js-modifier-list-0') {
        y--;
    }
    if (e.keyCode == 39 && get_position != 'right_last_row') {
        x++
    }
    if (e.keyCode == 40 && get_bottom_position != 'disabled') {
        y++
    }
    if ((e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40) && !modal_open_length)
        active = $('.js-append-parent li:eq(' + y + ')').find("td").eq(x).addClass('active').children().focus();
});

$(document).on('focus', '.textboxrow', function () {
    var modal_open_length = $('.modal:visible').length
    if(!modal_open_length) {
        $('td').removeClass('active');
        $(this).parent().addClass('active');  
    }    
});

// End to arrow keys.
$(document).on('click', 'a.dropdown-toggle', function (event) {
    event.preventDefault();
});

$(document).on('click', ".js-close-addclaim", function () {
    addModal('choose_claim');
    addModal('choose_claims');
    addModal('post_payments');
});

function checklineitemfilled() {
    //
}

function checknumbervalidation(value) {
    reg = /^[a-zA-Z0-9_ ]*$/;
    var check = reg.test(value);
    return check;
}

$(document).on('keypress change', '.js-search-text', function (e) { 
    value = $(this).val();
    valuenew = value.replace(/[^a-zA-Z0-9-,\s]/g, '');
    var start = this.selectionStart;
    var end = this.selectionEnd;
    $(this).val(valuenew);
    this.setSelectionRange(start, end);
    if (e.keyCode == 13) {
        $('.js-search-patient').trigger('click');
        $(this).blur();
       //$('.jsinsuranceform').unbind("submit");
       e.preventDefault();
        return false;
    }
});

// This is used at the search popup at main payment posting
$(document).on('change', '#js-select-searchcat', function () {
    value = $(this).val();
    if (value == "Patient") {
        $("select.js-search-check option[value='insurance_name']").remove();
        $("select.js-search-check").val("paymentnumber").change();
    } else {
        if ($("select.js-search-check option[value='insurance_name']").length <= 0)
            $("select.js-search-check").append('<option value="insurance_name">Insurance</option>');
    }
});

//$('#').data('bootstrapValidator').resetForm();
$(document).on('click', '.js-cancel-payment, .js_next_process', function (e) { 
    var target = $(e.target);
    var payment_detail_length = $('.js-alert-popupdisable').length;
    if (target.is("a.js_next_process")) {
        e.preventDefault();
        var form_element = $(document).find('form');
        form = form_element.attr('id');
    } else {
        var form = $(this).parents('form:first').attr('id');
    }
    if (payment_detail_length) {
        $('#' + form).data('bootstrapValidator').resetForm();
        var formdata = $('form#' + form).serialize();
    }
    var paymentdetailid = $('input[name="payment_detail_id"]').val();
    //redirect_location = $(this).parent('.js-cancel').attr('href');
    redirect_location = $('.js-cancel').attr('href');
    if (paymentdetailid == '' && payment_detail_length) {
        actionurl = api_site_url + "/payments/paymentinfo";
        e.preventDefault();
        $('#js_confirm_box_charges_content_payments').html("Would you like to save the check and post payments later?");
        $("#js_confirm_box_charges_payment")
            .modal({show: 'false', keyboard: false})
            .one('click', '.js_modal_confirm1', function (eve) {
                checkdata = $(this).attr('id');
                if (checkdata == "true") {
                    //validateinsurancepayment('#'+form);
                    //$('#'+form).data("bootstrapValidator").resetForm();
                    //$('#'+form).bootstrapValidator('validate');
                    //$('#'+form).on('success.form.bv', function(e)
                    //{
                    e.preventDefault();
                    $.ajax({
                        type: 'post',
                        url: actionurl,
                        data: formdata,
                        success: function (data) {
                            payment_id = data.data;
                            $('input[name="payment_detail_id"]').val(payment_id);
                            window.location = redirect_location;
                        }
                    });
                    // });
                } else {
                    window.location = redirect_location;
                }
            });
    } else if (paymentdetailid != '' && payment_detail_length) {
        $('#js_confirm_box_charges_content_payments').html("Would you like to cancel the payment");
        $("#js_confirm_box_charges_payment")
            .modal({show: 'false', keyboard: false})
            .one('click', '.js_modal_confirm1', function (eve) {
                if ($(this).attr('id') == 'true') {
                    window.location = redirect_location;
                } else {
                    e.preventDefault();
                }
            });
        e.preventDefault();
    }
});

$(document).ready(function () {
    $(document).on('keydown', 'table.js-insurance-table', function (e) {
        var $table = $(this);
        var $active = $('input:focus,select:focus', $table);
        var $next = null;
        var focusableQuery = 'input:visible,select:visible,textarea:visible';
        var position = parseInt($active.closest('td').index()) + 1;
        var $checkclass = null;
        var taget_id = $(e.target).attr("id"); // To prevent the dropdown issues       
        switch (e.keyCode) {
            /*case 37: // <Left>
                $next = $active.closest('td').prev().find(focusableQuery);
                var $checkclass = $active.closest('td').prev();
                if ($checkclass.hasClass('readonlytable')) {
                    $next = $active.closest('td').prev().prev().find(focusableQuery);
                }
                break;*/

            case 38: // <Up>
                $next = $active
                        .closest('li.js-calculate')
                        .prev()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery) ;
                break;
                
           /* case 39: // <Right>
                $next = $active.closest('td').next().find(focusableQuery);
                var $checkclass = $active.closest('td').next();
                if ($checkclass.hasClass('readonlytable')) {
                    $next = $active.closest('td').next().next().find(focusableQuery);
                }
                break;
                */
            case 40: // <Down>
                $next = $active
                        .closest('li.js-calculate')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery)
                        ;
                break;
        }

        if ($next && $next.length) {
            if((e.keyCode == "38" || e.keyCode == "40") && taget_id == "js-payment-denial") {
                //console.log("comes here"+ f);
            } else{
                $next.focus();  
            }
        }
    });
});

$(document).on('change', 'input[name="payment_amt_pop"]', function () {
    $('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'payment_amt_pop');
});

$(document).on('change', 'input[name="payment_amt"]', function () {
    if (("#js-payment").length) {
        $('form#js-payment').bootstrapValidator('revalidateField', 'payment_amt');
    } else {
        $('form#js-insurance-form').bootstrapValidator('revalidateField', 'payment_amt');
    }
})

//$('#choose_claims').modal({backdrop: 'static', keyboard: false});
function validatepatientpayment(selector)
{
    selector = (selector != '' && typeof selector != 'undefined') ? selector : '.js-patient-paymentform';
    var form_validator_payment = $(selector).bootstrapValidator({
        message: 'This value is not valid',
        excluded: [":disabled"],
        feedbackIcons: {
            valid: '',
            invalid: '',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            payment_amt_pop: {
                message: empty_amt,
                validators: {
                    callback: {
                        message: empty_amt,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            value_remaining = value.split('.');
                            value_remaining = value_remaining[0];
                            if (typeof value != "undefined" && value != '' && value <= 0 && chkd != 'Adjustment' && chkd != 'Credit Balance') {
                                return {
                                    valid: false,
                                    message: greater_zero_amt
                                }
                            }
                            if (value != '' && isNaN(value)) {
                                return {
                                    valid: false,
                                    message: valid_amt
                                }
                            } else if (value_remaining.length > 10) {
                                return {
                                    valid: false,
                                    message: "Please enter less than 10 characters"
                                }
                            }
                            if (chkd == 'Adjustment') {
                                return true;
                            } else {
                                return (value == '') ? false : true;
                            }
                        },
                    },
                }
            },
            payment_amt: {
                message: empty_amt,
                validators: {
                    callback: {
                        message: empty_amt,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            var takeback = $('input[name="takeback"]').val();
                            value_remaining = value.split('.');
                            value_remaining = value_remaining[0];
                            if (typeof value != "undefined" && value != '' && value <= 0 && chkd != 'Adjustment' && chkd != 'Credit Balance' && takeback != 1) {
                                return {
                                    valid: false,
                                    message: greater_zero_amt
                                }
                            }
                            if (value != '' && isNaN(value)) {
                                return {
                                    valid: false,
                                    message: valid_amt
                                }
                            } else if (value_remaining.length > 10) {
                                return {
                                    valid: false,
                                    message: "Please enter less than 10 characters"
                                }
                            }
                            if (chkd == 'Adjustment') {
                                return true;
                            } else {
                                return (value == '') ? false : true;
                            }
                        },
                    },
                }
            },
            card_type: {
                validators: {
                    callback: {
                        message: card_empty,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            card_type = $('select[name=card_type]').val();
                            if (chkd == "Payment" && mode == 'Credit') {
								return true;
                                return (card_type == '') ? false : true;
                            }
                            return true;
                        }
                    }
                }
            },
            wallet_refund: {
                 trigger: 'change keyup',
                validators: {
                    callback: {
                        message: valid_amt,
                        callback: function (value, validator) {
                           // console.log("comes wallet refund")
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            chkd_len = $('input[name=wallet_amt]:checked').length;
                            tot_amt = $('#payment_credit_balance').val();
                            payment_amount = $('input[name="payment_amt_pop"]').val();
                            if (payment_amount == '' && chkd == "Refund" && chkd_len) {
                                $('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'payment_amt_pop');
                            }
                            if (chkd == "Refund" && chkd_len) {
                                if (parseFloat(value) > parseFloat(tot_amt)) {
                                    return {
                                        valid: false,
                                        message: wallet_amt_exceed,
                                    }
                                } else if (parseFloat(value) > parseFloat(payment_amount)) {
                                    return {
                                        valid: false,
                                        message: refund_amt_exceed,
                                    }
                                } else if (value == '') {
                                    return {
                                        valid: false,
                                        message: valid_amt,
                                    }
                                }
                                return (value == '') ? false : true;
                            }
                            return true;
                        }
                    }
                }
            },
            card_no: {
                validators: {
                    callback: {
                        message: card_no,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            if (chkd == "Payment" && mode == 'Credit') {
                                return (value == '') ? false : true;
                            }
                            return true;
                        },
                    },
                    regexp: {
                        regexp: /^[0-9\s]+$/i,
                        message: only_numeric_lang_err_msg
                    }
                }
            },
            name_on_card: {
                validators: {
                    callback: {
                        message: card_on_card,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            if (chkd == "Payment" && mode == 'Credit') {
                                return (value == '') ? false : true;
                            }
                            return true;
                        }
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: only_alpha_lang_err_msg
                    }
                }
            },
            check_no: {
                validators: {
                    
                    callback: {
                        message: empty_check_no,
                        callback: function (value, validator, $field) {
                           // console.log("coems check number")
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            check_number_exist = $('input[name="checkexist"]').val();
                            parseval = parseFloat(value);
                            if (chkd == "Payment" && mode == 'Check' || chkd == "Refund") {
                                if (value == '') {
                                    return{
                                        valid: false,
                                        message: empty_check_no
                                    }
                                } else if (value != '' && parseval == 0) {
                                    return{
                                        valid: false,
                                        message: "Zero check number not allowed"
                                    }
                                } else if (value != '' && !checknumbervalidation(value)) {
                                    return{
                                        valid: false,
                                        message: alphanumeric_lang_err_msg
                                    }
                                } else if (value != '' && value.length < lengthval) {
                                    return {
                                        valid: false,
                                        message: checkminlength,
                                    }
                                } else if (value != '' && check_number_exist == 1) {
                                    return{
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
            adjustment_reason: {
                message: adjustment_reason,
                validators: {
                    callback: {
                        message: adjustment_reason,
                        callback: function (value, validator) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            if (chkd == "Adjustment") {
                                return (value == '') ? false : true;
                            }
                            return true;
                        }
                    },
                }
            },
            check_date: {
                message: '',
                trigger: 'change keyup',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: date_format
                    },
                    callback: {
                        message: date_format,
                        callback: function (value, validator, $field) {
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            var check_date = $('input[name="check_date"]').val();
                            var current_date = new Date(check_date);
                            var d = new Date();
                            if (value == '' && (chkd == "Payment" && mode == 'Check' || chkd == "Refund")) {
                                return {
                                    valid: false,
                                    message: "Enter check date",
                                };
                            }
                            if (check_date != '' && d.getTime() < current_date.getTime()) {
                                return {
                                    valid: false,
                                    message: future_date,
                                };
                            }
                            if (chkd == "Payment" && mode == 'Check' || chkd == "Refund") {
                                return (value == '') ? false : true;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            },
            money_order_no:{
				enabled: false,
				trigger: 'change',
				validators:{ 
					regexp: {
						regexp: /^[a-zA-Z0-9_ ]*$/,
						message:"Enter valid numbers"
					},
					remote: {
						message: 'This Money order number already exists',
						url: api_site_url+'/payments/checkexist',  
						data:function(validator){
						   return {
								type:"MO",
								patient_id: $('input[name="patient_id"]').val(),
								value:validator.getFieldElements('money_order_no').val(),
								_token:$('input[name="_token"]').val()
						   } 
						},
						type: 'POST'
					},
					callback: {
						message: "",
						callback: function (value, validator, $field) {
							chkd = $('input[name=payment_type]:radio:checked').val();
							if (typeof chkd == 'undefined' || chkd == '')
								chkd = $('select[name=payment_type]').val();
							mode = $('select[name=payment_mode]').val();
							if (value == '' && (chkd == "Payment" && mode == 'Money Order')) {
								return {
									valid: false,
									message: "Enter Money order number",
								};
							}
							return true;
						}
					}
				}
			},              
			money_order_date: {  
                trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: date_format
					},
					callback: {
						message: check_date_msg,
						callback: function (value, validator, $field) {
							chkd = $('input[name=payment_type]:radio:checked').val();
							if (typeof chkd == 'undefined' || chkd == '')
								chkd = $('select[name=payment_type]').val();
							mode = $('select[name=payment_mode]').val();
							var check_date = $('input[name="check_date"]').val();
							var current_date = new Date(check_date);
							var d = new Date();
							if (value == '' && (chkd == "Payment" && mode == 'Money Order')) {
								return {
									valid: false,
									message: "Enter Money order date",
								};
							}
							if (check_date != '' && d.getTime() < current_date.getTime()) {
								return {
									valid: false,
									message: future_date,
								};
							}
							if (chkd == "Payment" && mode == 'Money Order') {
								return (value == '') ? false : true;
							} else {
								return true;
							}
						}
					}
				}
			},
            cardexpiry_date: {
                validators: {
                    callback: {
                        message: '',
                        callback: function (value, validator) {
                            var check_date = checkDate(value);
                            chkd = $('input[name=payment_type]:radio:checked').val();
                            if (typeof chkd == 'undefined' || chkd == '')
                                chkd = $('select[name=payment_type]').val();
                            mode = $('select[name=payment_mode]').val();
                            var valid_date = isValidDate(value);
                            if (!valid_date && value != '') {
                                return {
                                    valid: false,
                                    message: date_format
                                }
                            } else if (chkd == "Payment" && mode == "Credit" && check_date == false && value != '') {
                                return {
                                    valid: false,
                                    message: past_date
                                }
                            }
                            return true;
                        }
                    }
                }
            },
            bankname: {
                validators: {
                    callback: {
                        message: bankname,
                        callback: function (value, validator) {
                            bankname_val = $.isNumeric(value);
                            return (bankname_val) ? false : true;
                        }
                    }
                }
            },
            js_pateint_paid: {
                selector: '.js_pateint_paid',
                message: '',
                validators: {
                    callback: {
                        message: empty_amt,
                        callback: function (value, validator, $field) {
                            var get_field = $field.parents(".js-calculate").attr("id");
                            var value = $("#" + get_field + " .js_pateint_paid").val();
                            if (value == '') {
                                return {
                                    valid: false,
                                    message: empty_amt
                                }
                            }
                            /*else if(value != '' && value == 0){
                             return {
                             valid:false,
                             message: greater_zero_amt
                             }
                             } */
                            else if (value != '' && isNaN(value)) {
                                return {
                                    valid: false,
                                    message: valid_amt
                                }
                            }
                            return (value == '') ? false : true;
                        },
                    }
                }
            },
            filefield_eob: {
                message: '',
                validators: {
                    file: {
                        extension: 'pdf,jpeg,jpg,png,gif,doc',
                        message: attachment_valid_lang_err_msg
                    },
                    callback: {
                        message: attachment_length_lang_err_msg,
                        callback: function (value, validator, $field) {
                            if ($('[name="filefield_eob"]').val() != "") {
                                var size = parseFloat($('[name="filefield_eob"]')[0].files[0].size / 1024).toFixed(2);
                                var get_image_size = Math.ceil(size);
                                return (get_image_size > eob_attacment_max_defined_length) ? false : true;
                            }
                            return true;
                        }
                    }
                }
            }
        },
    }).on('success.form.bv', function (e) {
       //console.log("add wallet name"+addwallet_name);
       if(addwallet_name == "addwallet"){
            e.preventDefault();
       }       
       //$(selector).find("input[type='submit']").prop("disabled", "disabled");
       //$('#js-addwallet').hide();
    });
}

$(document).on("change", 'input[name="money_order_no"]', function(){
	if($('form#js-payment').length) {
		$('form#js-payment').bootstrapValidator('enableFieldValidators', 'money_order_no', true);
		$('form#js-payment').bootstrapValidator('revalidateField', 'money_order_no');
	} else {
		$('form.js-patient-paymentform').bootstrapValidator('enableFieldValidators', 'money_order_no', true);
		$('form.js-patient-paymentform').bootstrapValidator('revalidateField', 'money_order_no');
	}
});

function validateinsurancepayment(selector)
{
    selector = (selector != '' && typeof selector != 'undefined') ? selector : '#js-insurance-form';
    $(selector)
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				payment_amt: {
					validators: {
						callback: {
							message: valid_amt,
							callback: function (value, validator) {
								chkd = $('input[name=payment_type_ins]:radio:checked').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								value_remaining = value.split('.');
								value_remaining = value_remaining[0];
								if (chkd == 'Adjustment') {
									return true;
								} else if (value < 0 && value != '' && !isNaN(value)) {
									return {
										valid: false,
										message: greater_than_zero_err_msg
									}
								} else if (value_remaining.length > 10) {
									return {
										valid: false,
										message: "Please enter less than 10 characters"
									}
								} else {
									if (value == '') {
										return {
											valid: false,
											message: "Enter amount"
										}
									}
									return (value != '' && !isNaN(value)) ? true : false;
								}
							},
						},
					}
				},
				insurance_id: {
					validators: {
						notEmpty: {
							message: sel_insurance
						},
					}
				},
				privider_id: {
					validators: {
						notEmpty: {
							message: sel_provider
						},
					}
				},
				check_no: {
					validators: {
					   /* creditCard: {
								message: 'The credit card number is not valid'
							},*/
						callback: {
							message: "",
							callback: function (value, validator) {
								chkd = $('input[name=payment_type_ins]:radio:checked').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								check_number_exist = $('input[name="checkexist"]').val();
								var mode = $('input[name="insur_payment_mode"]:checked').val() ;                                    
								if (chkd == "Payment" || chkd == "Refund" || mode == "Credit") {
									if (value == '') {
										return{
											valid: false,
											message: check_eft_no
										}
									} else if (value != '' && !checknumbervalidation(value)) {
										return {
											valid: false,
											message: alphanumeric_lang_err_msg,
										}
									}
									/*else if(value != '' && value.length < lengthval){
									 return {
									 valid:false,
									 message:checkminlength,
									 }
									 }*/else if (value != '' && check_number_exist == 1) {
										return{
											valid: false,
											message: checkeftexist
										}
									}
								}
								return true;
							},
						},
					}
				},
				adjustment_reason: {
					validators: {
						callback: {
							message: adjustment_reason,
							callback: function (value, validator) {
								chkd = $('input[name=payment_type_ins]:radio:checked').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								if (chkd == "Adjustment") {
									return (value == '') ? true : true;
								}
								return true;
							},
						},
					}
				},
				check_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: check_eft_date,
							callback: function (value, validator) {
								chkd = $('input[name=payment_type_ins]:radio:checked').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								var check_date = $('input[name="check_date"]').val();
								var current_date = new Date(check_date);
								if(typeof(get_default_timezone) != "undefined" && get_default_timezone !== null) {  
                                    var d = new Date(get_default_timezone);
                               }else{
                                    var d = new Date();
                               }
								if (value == '' && (chkd == "Payment" || chkd == "Refund")) {
									return {
										valid: false,
										message: "Enter check date",
									};
								}
								if (check_date != '' && d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: future_date,
									};
								}
								if (chkd == "Payment" || chkd == "Refund") {
									return (value == '') ? false : true;
								} else {
									return true;
								}
							}
						}
					}
				},
				deposite_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: date_deposite,
							callback: function (value, validator) {
								chkd = $('input[name=payment_type]').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								var deposite_date = $('input[name="deposite_date"]').val();
								var current_date = new Date(deposite_date);
								var d = new Date(today_practice); 
								if (deposite_date != '' && d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: future_date,
									};
								}
								if (chkd == "Payment" || chkd == "Refund") {
									return (value == '') ? false : true;
								} else {
									return true;
								}
							}
						}
					}
				},
				posting_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: date_posting,
							callback: function (value, validator) {
								chkd = $('input[name=payment_type]').val();
								if (typeof chkd == 'undefined' || chkd == '')
									chkd = $('input[name=payment_type]').val();
								check_date = isFutureDate(value);
								if (check_date == true) {
									return {
										valid: false,
										message: future_date
									}
								}
								if (chkd == "Payment" || chkd == "Refund") {
									return (value == '') ? false : true;
								}
								return true;
							}
						}
					}
				},
				filefield_eob: {
					message: '',
					validators: {
						file: {
							extension: 'pdf,jpeg,jpg,png,gif,doc',
							message: attachment_valid_lang_err_msg
						},
						callback: {
							message: attachment_length_lang_err_msg,
							callback: function (value, validator, $field) {
								if ($('[name="filefield_eob"]').val() != "") {
									var size = parseFloat($('[name="filefield_eob"]')[0].files[0].size / 1024).toFixed(2);
									var get_image_size = Math.ceil(size);
									return (get_image_size > eob_attacment_max_defined_length) ? false : true;
								}
								return true;
							}
						}
					}
				}
			},
    }).on('success.form.bv', function (e) {
        //console.log("success");
        check_popup = $('input[name="change_insurance_id"]').length;
        var insurance_id = $('#js-insurance-id').val();
        if (check_popup) {           
            insurance_value = getinsuranceid(insurance_id);
            var change_ins_id = $('input[name="change_insurance_id"]').val();
			//console.log("33"+check_popup+"INS Val "+insurance_value+" CID: "+change_ins_id);
            if (typeof insurance_value != 'undefined' && insurance_value != '' && change_ins_id == '') {
                var patient_id = $('input[name="patient_id"]').val();
                getInsuranceList(patient_id)
                return false;
            }
        }
    }).on('error.form.bv', function () {
        
    });
}

$(document).on('change', '.js-search-popup', function () {
    var value = $(this).val();
    var search_sel = $('#js-search-val');
    search_sel.val("");
    search_sel.attr('maxlength', 30);
    if (value == 'dob') {
        $('input[name="search_val"]').val("");
        $('.js-show-datepicker').show();
        $('.js-hide-datepicker').hide();
    } else if (value == 'policy_id') {
        search_sel.attr('maxlength', 20);
    } else if (value == 'ssn') {
        search_sel.attr('maxlength', 10);
    } else if (value == 'last_name') {
        search_sel.attr('maxlength', 28);
    } else if (value == 'first_name') {
        search_sel.attr('maxlength', 28);
    }
    if (value != 'dob') {
        $('.js-show-datepicker').hide();
        $('.js-hide-datepicker').show();
        $(".call-datepicker").datepicker("hide");
        $('input[name="search_val_date"]').val("").change();
    }
});

$(document).on('change', 'input[name="search_val_date"]', function () {
    var value = $(this).val();
    $('input[name="search_val"]').val(value);   // Date again passed to url that's why removed the not empty conditions
});

function checkpreviousnotempty(icd_id) {
    var k;
    var objvalue = [];
    for (i = 1; i <= 12; i++) {
        objvalue[i] = 'icd' + i;
    }
    ival = objvalue.indexOf(icd_id);
    kval = parseInt(ival) - parseInt(1);
    var prev_count = 0;
    for (var k = kval; k >= 1; k--) {
        if (($('#' + objvalue[k]).val() == '' || typeof $('#' + objvalue[k]).val() == 'undefined') && $('#' + icd_id).val() != '') {
            prev_count++;
        }
    }
    if (prev_count) {
        return false;
    }
    return true;
}

function checknextnotempty(icd_id) {
    var objvalue = [];
    var k;
    for (i = 1; i <= 12; i++) {
        objvalue[i] = 'icd' + i;
    }
    var next_count = 0;
    ival = objvalue.indexOf(icd_id);
    for (var k = parseInt(ival) + parseInt(1); k <= objvalue.length; k++) {
        if ($('#' + objvalue[k]).val() != '' && typeof $('#' + objvalue[k]).val() != 'undefined' && $('#' + icd_id).val() == '') {
            next_count++;
        }
    }
    if (next_count) {
        return false;
    }
    return true;
}

$(document).delegate(".js-icd", 'change', function (e) {
    $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'js_icd_validation');
});
/*$(document).on('keyup keydown',".js-icd",function (e) {
    value = $(this).val();
   
    if (value.match(/[^[\.]$]/)) { 
      
        e.preventDefault()
       return false;
    } else{
       // console.clear();
    }
});*/

function checknextnotemptyicdpointer(li_id, icd_pointer_id) {
    var list_id = [];
    var k;
    var next_count = 0;
    list_id = $("#" + li_id + " .icd_pointer").map(function (i, index) {
        return  $(this).attr('id');
    });
    ival = $.inArray(icd_pointer_id, list_id);
    for (var k = parseInt(ival) + parseInt(1); k <= list_id.length; k++) {
        if ($('#' + list_id[k]).val() != '' && typeof $('#' + list_id[k]).val() != 'undefined' && $('#' + icd_pointer_id).val() == '') {
            next_count++;
        }
    }
    if (next_count) {
        return false;
    }
    return true;
}

function checkpreviousnotemptyicdpointer(li_id, icd_pointer_id) {
    var k;
    var list_id = [];
    var prev_count = 0;
    list_id = $("#" + li_id + " .icd_pointer").map(function (i, index) {
        return  $(this).attr('id');
    });
    ival = $.inArray(icd_pointer_id, list_id);
    kval = parseInt(ival) - parseInt(1);
    for (var k = kval; k >= 1; k--) {
        if (($('#' + list_id[k]).val() == '' || typeof $('#' + list_id[k]).val() == 'undefined') && $('#' + icd_pointer_id).val() != '') {
            prev_count++;
        }
    }
    if (prev_count) {
        return false;
    }
    return true;
}

$(document).on("change", 'input[name="filefield_eob"]', function () {
    var form_clas_name = $(this).parents("form").hasClass('medcubicsform');
    if (form_clas_name) {
        $(".medcubicsform").bootstrapValidator('addField', "filefield_eob");
        $(".medcubicsform").bootstrapValidator('revalidateField', "filefield_eob");
    } else {
        var form_id = $(this).parents("form").attr('id');
        $('#' + form_id).bootstrapValidator('addField', "filefield_eob");
        $('#' + form_id).bootstrapValidator('revalidateField', "filefield_eob");
    }
});

$(document).delegate('.js-patient-notes', 'submit', function (e) {
    if (e.isDefaultPrevented()) {
        //alert('form is not valid');
    } else {
        var formData = $('form.js-patient-notes').serialize();
        $('#ajx-loader').html('<img src="' + api_site_url + '/img/ajax-loader.gif">');
        action_url = $(this).attr("action");
        $.ajax({
            type: 'post',
            url: action_url,
            data: formData,
            success: function (data) {
                data = $.parseJSON(data);
                data = data.status;
                if (data == 'success') {
                    $('#post_payments').modal('hide');
                    return false;
                } else {
                    js_alert_popup("Note has not been added succesfully");
                    return false;
                }
            }
        });
        return false;
    }
});

$(document).ready(function () { 
    var arpayment_display = localStorage.getItem("arpayment_display");
    var popupDisplayType = localStorage.getItem("pmt_type");       
    if ($('.js-arvar').length && typeof arpayment_display != "undefined" && arpayment_display == 0) {
		if(popupDisplayType == 'ins_pmt'){
			var patient_id = $('input[name="ar_var_patient"]').val();
			var target = api_site_url + '/payments/paymentinsurance/insurance'; // Main payment target url
			if (patient_id != '' && typeof patient_id != 'undefined')
				var target = api_site_url + '/patients/' + patient_id + '/paymentinsurance/insurance'; // Patient payment target url
			$("#choose_claims .modal-body").html('<p class = "text-center med-green"><i class="fa fa-spinner fs-spin font20"></i> Processing</span>'); // Need to make a spinner
			$("#choose_claims .modal-title").html("Post Insurance Payment");
			$("#choose_claims .modal-body").load(target, function () {
				localStorage.setItem("arpayment_display", 1);
				$("#choose_claims").modal('show');
				callicheck();
				$('select.select2.form-control').select2();
				validateinsurancepayment();
				if (typeof patient_id == 'undefined')
					$('#js-insurance-form').attr('action', api_site_url + '/payments/insurancecreate');
				setTimeout(function () {
					var claim_ids = $('input[name="claim_ids"]').val();
					var geturl = api_site_url + '/payments/getclaimdata/' + claim_ids;
					$.get(geturl, function (data) {
						$('.js_payment tbody').html(data);
					});
				}, 50);
			});
		}else if(popupDisplayType == 'pat_pmt'){
			$('.js_pat_pay_key').click();
			$('div.js-patient-search').addClass('hide');
			 var claimNo = localStorage.getItem("claim_no");
			setTimeout(function () { 
				$('#PatientSearch').val('claim_number').trigger('change');
				$('#js-search-val').val(claimNo);
				var dataClaim = "js-bal-"+claimNo;
				$('a.js-search-patient').click().attr('disabled',true);
				$('a.js-reset-patient').click().attr('disabled',true);
				$('div.js-patient-search').addClass('hide');
				if($('#js_popup_claimpatient_table:visible')){
					$('input[type="checkbox"][data-claim="'+dataClaim+'"]').attr("checked",true);
					$('input.js_submenu_payment:not(:checked)').closest("tr").hide();
				}
			}, 2000);
		}
    }
});

$(document).on("click", 'a[id*="claim-payment_"]', function(){
    localStorage.setItem("arpayment_display", 0);
});

$(document).on('keypress', '.js_avoid_negative', function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});

// CPT search related works starts here
$(document).on('dblclick', '.js-cpt:not([readonly])', function () {
    cptname = $(this).attr('name');
    $(this).blur();
    //If the error message shows hide them and show the search text
    if ($('.modal:visible').length)
        ;
    $('.modal:visible').hide();
    var target = api_site_url + '/cptsearch' + '';
    $("#js-model-popup .modal-body").load(target, function () {
        $("#js-model-popup").modal('show');
        $("#js-model-popup .modal-title").html("Search CPT");
        $('input[name="search_keyword"]').focus();
    });
});

function double_tap(cptname) {
    if ($('.modal:visible').length)
        ;
    $('.modal:visible').hide();
    var target = api_site_url + '/cptsearch' + '';
    $("#js-model-popup .modal-body").load(target, function () {
        $("#js-model-popup").modal('show');
        $("#js-model-popup .modal-title").html("Search CPT");
        $('input[name="search_keyword"]').focus();
    });
}

var tapped1 = false
$(document).on("touchstart", ".js-cpt:not([readonly])", function (e) {
    cptname = $(this).attr('name');
    $(this).blur();
    if (!tapped1) {
        tapped1 = setTimeout(function () {
            //single_tap()
            tapped1 = null
        }, 300); //wait 300ms
    } else {
        clearTimeout(tapped1);
        tapped1 = null
        double_tap(cptname);
    }
});

$(document).on('click', '.js-submit', function () {
    searchcpt();
});

function searchcpt() {
    var search_key = $('input[name="search_keyword"]').val();
    if (search_key != '' && search_key.length >= 2) {
        var target = api_site_url + '/cptsearch/' + search_key;
        $('.js-spin-image').html('<i class="fa fa-spinner fa-spin">');
        addModalClass();
        $("#js-model-popup .modal-body").load(target, function () {
            callicheck();
            $("#js-model-popup").modal('show');
            $('.js-spin-image').html('');
        });
    } else {
        if (!$('#cpt_search_number').length && $('input[name="search_keyword"]').val() == '') {
            $("<span id='cpt_search_number' style='display:block;'><small class='help-block med-orange font13' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'></small><p class='med-orange font13 no-bottom'>Please enter search keyword!</p></span>").insertAfter($(".input-group-sm"));
        } else if (search_key.length < 2 && !$('#cpt_search_number_max').length) {
            $("<span id='cpt_search_number_max' style='display:block;'><small class='help-block med-orange font13' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'></small><p class='med-orange font13 no-bottom'>Enter more than one word</p></span>").insertAfter($(".input-group-sm"));
        }
    }
}

$(document).on('change', 'input[name="search_keyword"]', function (e) {
    if ($(this).val().length >= 2 || $(this).val() == '') {
        $('#cpt_search_number_max').remove();
    }
    if ($(this).val() != '')
        $('#cpt_search_number').remove();
    $('.js-spin').remove();
    searchcpt();
});

$(document).on('ifToggled click', '.js-sel-cpt', function () {
    var value = $(this).val();
    $("#js-model-popup").modal('hide');
    $('input[name="' + cptname + '"]').val(value).trigger('change');
});
// CPT search related works ends here

// ICD double click search related works starts here
$(document).on('dblclick', '.js-icd:not([readonly])', function () {
    var icd_id = $(this).attr('id');
    $(".js_icd_val").val(icd_id);
    $('.js_search_icd_list').val("");
    $("#icd_imo_search_part").html("");
    $('#imosearch').modal('show');
    //$('.js-icd').off('focus');
    $(this).blur();
    $('input[name = "search_icd_keyword"]').focus();
    //$('.js-icd').unbind('blur');
});
// ICD double click search related works ends here

function double_icdtap(id) {
    var icd_id = $("#" + id).attr('id');
    $(".js_icd_val").val(icd_id);
    $('.js_search_icd_list').val("");
    $("#icd_imo_search_part").html("");
    $('#imosearch').modal('show');
    //$('.js-icd').off('focus');
    $("#" + id).blur();
    $('input[name = "search_icd_keyword"]').focus();
}

$(".js-icd:not([readonly])").on("touchstart", function (e) {
    var id = $(this).attr('id');
    if (!tapped) {
        tapped = setTimeout(function () {
            //single_tap()
            tapped = null
        }, 300); //wait 300ms
    } else {
        clearTimeout(tapped);
        tapped = null
        double_icdtap(id)
    }
    e.preventDefault()
});

function holdClaim()
{
    //
}

$(document).ready(function () {
    billingvalidation('js-bootstrap-validator');
    if (!$("#hold-option").is(':checked')) {
        enablefieldvalidation('js-bootstrap-validator', true);
    }
});
	
$(document).on("change click",".call-datepicker",function (e) {
    billingvalidation('js-bootstrap-validator');
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_date');
});

function billingvalidation(formname) {
    $('#' + formname)
		.bootstrapValidator({
			message: 'This value is not valid',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: ''
			},
			excluded: [":disabled", ":hidden", ":not(:visible)"],
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
										message: date_valid_lang_err_msg,
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
					enabled: false,
					selector: '.js_charge_amt',
					validators: {
						callback: {
							message: greater_zero_amt,
							callback: function (value, validator, $field) {
								var amount_id = $field.parents('.js-validate-lineitem').attr("id");
								value = $("#" + amount_id).find(".js_charge_amt").val();
								var amount_eror = $('.js_charge_amt').map(function () {
									return  ($(this).val() != '' && $(this).val() == parseFloat(0)) ? 1 : 0;
								});  
								//console.log("amount error"+amount_eror);
								if (value != '' && value <= parseFloat(0) && $.inArray(parseInt(1), amount_eror) > -1 && typeof value != "undefined") {                                        
									return {
										valid: false,
										message: greater_zero_amt,
									}
								}
								return true;
							}
						}
					}
				},
				from_dos: {
					enabled: false,
					message: '',
					selector: '.from_dos',
					validators: {
						callback: {
							message: 'DOS must be given',
							callback: function (value, validator, $field) {
								var get_field = $(".js-append-parent li:first-child").attr("id");
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
					enabled: false,
					message: '',
					selector: '.to_dos',
					validators: {
						callback: {
							message: 'Dos to date must be given',
							callback: function (value, validator, $field) {
								var get_field = $(".js-append-parent li:first-child").attr("id");
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
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: charge_billing_provider,
							callback: function (value, validator, $field) {
								return (value != '') ? true : false;
							}
						}
					}
				},
				refering_provider: {
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: 'Refering provider',
							callback: function (value, validator, $field) {
								var is_required = 0;
								cpt_refering_length = $('.js-refering-count').filter(function () {
									return $(this).val() == 1;
								}).length;
								if (cpt_refering_length) {
									var is_required = 1;
								}
								if (is_required && value.trim() == '')	{
									return {
										valid: false,
										message: "Referring provider needed"
									}
								} else {
									return true;
								}
							}
						}
					}
				},
				rendering_provider_id: {
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: charge_rendering_provider,
							callback: function (value, validator, $field) {
								return (value.trim() != '') ? true : false;
							}
						}
					}
				},
				hold_reason_id: {
					enabled: false,
					message: '',
					validators: {
						notEmpty: {
							message: hold_reason
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
								var hold_reason_exist = '';
								var holereason = $('#js-hold-reason option:selected').val();
								var hold_reason_exist = $('input[name="hold_reason_exist"]').val();
								if (value.trim() != '' && hold_reason_exist == 1) {
									return {
										valid: false,
										message: "Already exists"
									}
								}
								if (holereason == 0) {
									return false;
								} else {
									return true;
								}
							}
						}
					}
				},
				facility_id: {
					enabled: false,
					message: '',
					validators: {
						notEmpty: {
							message: charge_facility_id
						}
					}
				},
				insurance_id: {
					enabled: false,
					message: '',
					validators: {
						notEmpty: {
							message: charge_insurance_id
						}
					}
				},
				pos_id: {
					enabled: false,
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: "Select POS"
						},
						callback: {
							message: charge_pos_name,
							callback: function (value, validator, $field) {
								pos_val = $("#pos_id :selected").text();
								pos_code = [6, 8, 21, 31, 51, 61, 34];     // Place of service codes that must need admission date
								if($.inArray(parseInt(pos_val), pos_code) > -1) {
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
					enabled: false,
					message: '',
					trigger: 'change',
					validators: {
						callback: {
							message: enter_admit_date,
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
										message: enter_admit_date,
										valid: false
									}
								}
								removeerrormessage(value, 'js_from_date');
								if (value && compval) {
									return true;
								} else if (value != '') {
								   return {
										message: "Admit date should before DOS",
										valid: false
									}
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
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: dischargedate,
							callback: function (value, validator) {
								var current_date = new Date(value);
								var d = new Date();
								var is_valid_date = validDateCheck(value);
								if (value != '' && !is_valid_date) {
									return {
										valid: false,
										message: date_format,
									};
								} else if (value != '' && d.getTime() < current_date.getTime()) {
									// Should not be future the date
									return {
										valid: false,
										message: not_future,
									};
								}
								var m = validator.getFieldElements('admit_date').val();
								var n = value;
								dos = $("#big_date").val();
								compval_dis = 1;
								compval_dis = (dos != '' && value != '') ? compareDate(dos, value) : compval_dis;
								// check with dos value
								var current_date = new Date(n);
								if (current_date != 'Invalid Date' && n != '' && m != '' && compval_dis) {
									var getdate = daydiff(parseDate(m), parseDate(n));
									return (getdate >= 0) ? true : false;
								} else {
									if (!compval_dis) {
										return {
											valid: false,
											message: dos_msg,
										};
									}
									return true;
								}
							}
						}
					}
				},
				copay: {
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: charge_copay,
							callback: function (value, validator, $field) {
								$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_no');
								$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_date');
								$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'card_type');
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
					enabled: false,
					message: charge_copay_amt,
					validators: {
						callback: {
							message: charge_copay_amt,
							callback: function (value, validator, $field) {
								mode = $('select[name=copay]').val();
								if (value != '' && parseFloat(value) < 0) {
									return {
										valid: false,
										message: "Negative amount not allowed"
									}
								} else if (value != '' && !$.isNumeric(value)) {
									return {
										valid: false,
										message: "Enter valid amount"
									}
								} else if (value != '' && value == 0) {
									return {
										valid: false,
										message: charge_not_zero
									}
								} else if (value != '') {
									var regexp = (value.indexOf(".") == -1) ? /^[0-9]{0,5}$/ : /^[0-9.]{0,8}$/;
									if (!regexp.test(value)) {
										return {
											valid: false,
											message: maximum_amt
										};
									} else {
										return true;
									}
								} else if (value == '' && mode != '') {
									return {
										valid: false,
										message: charge_copay_amt
									};
								}
								return true;
							}
						}
					}
				},
				anesthesia_start: {
					enabled: false,
					message: '',
					validators: {
						callback: {
							message: anesthesia_start,
							callback: function (value, validator, $field) {
								var endTime = validator.getFieldElements('anesthesia_stop').val();
								var startTime = validator.getFieldElements('anesthesia_start').val();
								if (endTime != '' && startTime == '') {
									return {
										valid: false,
										message: anesthesia_start_time,
									}
								}
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
								return true;
							}
						}
					}
				},
				anesthesia_stop: {
					enabled: false,
					validators: {
						callback: {
							message: anesthesia_stop,
							callback: function (value, validator, $field) {
								var endTime = validator.getFieldElements('anesthesia_stop').val();
								var startTime = validator.getFieldElements('anesthesia_start').val();
								if (endTime == '' && startTime != '') {
									return {
										valid: false,
										message: anesthesia_end_time,
									}
								}
								if (startTime == '') {
									return true;
								}
								if ((startTime != '' && endTime != '')) {
									returnval = Compare("stop");
									return {
										valid: returnval['return'],
										message: returnval['message'],
									}
								}
								return true;
							}
						}
					}
				},
				check_no: {
					enabled: false,
					validators: {
						callback: {
							message: empty_check_no,
							callback: function (value, validator, $field) {
								mode = $('select[name=copay]').val();
								check_number_exist = $('input[name="checkexist"]').val();
								parseval = parseFloat(value);
								if (mode == 'Check') {
									if (value == '') {
										return{
											valid: false,
											message: empty_check_no
										}
									} else if (value != '' && parseval == 0) {
										return{
											valid: false,
											message: "Zero check number not allowed"
										}
									} else if (value != '' && !checknumbervalidation(value)) {
										return{
											valid: false,
											message: alphanumeric_lang_err_msg
										}
									} else if (value != '' && value.length < lengthval) {
										return {
											valid: false,
											message: checkminlength
										}
									} else if (value != '' && check_number_exist == 1) {
										return{
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
					enabled: false,
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: check_date_msg,
							callback: function (value, validator) {
								mode = $('select[name=copay]').val();
								var current_date = new Date(value);
								var d = new Date();
								if (value != '' && d.getTime() < current_date.getTime()) {    // Should not be future the date
									return {
										valid: false,
										message: future_date,
									};
								}
								if (mode != '' && mode == 'Check') {
									return (value == '') ? false : true;
								}
								return true;
							}
						}
					}
				},
				card_type: {
					enabled: false,
					validators: {
						callback: {
							message: card_empty,
							callback: function (value, validator) {
								mode = $('select[name=copay]').val();
								if (mode == 'Credit') {
									return true; // card type validation removed
									return (value == '') ? false : true;
								}
								return true;
							}
						}
					}
				},
				card_no: {
					validators: {
						callback: {
							message: card_no,
							callback: function (value, validator) {
								mode = $('select[name=copay]').val();
								if (mode == 'Credit') {
									return (value == '') ? false : true;
								}
								return true;
							},
						},
						regexp: {
							regexp: /^[0-9\s]+$/i,
							message: only_numeric_lang_err_msg
						}
					}
				},
				auth_no: {
					validators: {
						regexp: {
							regexp: /^[A-Za-z0-9 ]+$/,
							message: alphanumeric_lang_err_msg
						},                           
					}
				},
				js_icd_validation: {
					enabled: false,
					selector: '.js_icd_validation',
					validators: {
						callback: {
							message: 'Enter icd',
							callback: function (value, validator, $field) {
								data_icdid = $field.attr('id');
								var dataicd_val = $('#' + data_icdid).attr('data-icdval');
								var $current = $field;
								is_unique = 0;
							   var regexp = (value.indexOf(".") == -1) ? /^[a-zA-Z0-9]{0,7}$/ : /^[a-zA-Z0-9.]{0,8}$/;
							   // var regexp =  /^[a-zA-Z0-9]{0,8}$/;
								if (value.trim() != '' && !regexp.test(value)) {
									return {
										valid: false,
										message: 'Enter valid code'
									};
								}
								var count = value.split(".").length - 1;
								if (count > 1 && value != '') {
									return {
										valid: false,
										message: 'Enter vaild ICD 10'
									};
								}
								$('.js-icd').each(function () {
									current_val  = $current.val().toUpperCase();
									if ($(this).val() == current_val && $(this).attr('id') != $current.attr('id')) {
										is_unique = 1;
									}
								});
								var check_next = checknextnotempty(data_icdid);
								var check_previous = checkpreviousnotempty(data_icdid);
								if ($field.attr('id') == 'icd1' && value == '') {
									js_alert_popup("Diagnosis code is required field. Please enter the same.");
									return {
										valid: false,
										message: 'Diagnosis code is required field. Please enter the same.'
									};
								} else if ($field.attr('id') != 'icd1' && !check_next) {
									//js_alert_popup("Diagnosis code is required field. Please enter the same.");
									return {
										valid: false,
										message: 'Enter ICD values'
									};
								} else if (is_unique && value != '') {                                        
									//js_alert_popup('Entered ICD should be unique')
									return {
										valid: false,
										message: 'Entered ICD should be unique'
									};
								} else if (dataicd_val != 0 && value != '') {
									return {
										valid: false,
										message: 'Invalid icd mapping'
									};
								} else {

								}
								if (value != '' && check_next && check_previous) {
									return true;
								}
								return true;
							}
						}
					}
				},
				icd_pointer: {
					enabled: false,
					selector: '.icd_pointer',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator, $field) {
								var exis_arr = [];
								var get_field_id = $field.parents('li').attr("id");
								var icdpointer_id = $field.attr("id");
								var next_icd_pointer = checknextnotemptyicdpointer(get_field_id, icdpointer_id);
								var previous_icd_pointer = checkpreviousnotemptyicdpointer(get_field_id, icdpointer_id);
								var icd_attr_id = $("#" + get_field_id).find('.icd_pointer:first').attr('id');
								var cpt_value = $("#" + get_field_id).find('.js-cpt').val();
								if (icdpointer_id == icd_attr_id && value == '' && cpt_value != '' && typeof cpt_value != "undefined") {
									$field.addClass('erroricddisplay js-error-class');
									return  {
										valid: false,
										message: 'Enter ICD pointer'
									};
								}
								if (!next_icd_pointer && icdpointer_id != "icd1_0") {
									$field.addClass('erroricddisplay js-error-class');
									return  {
										valid: false,
										message: 'Enter icd values'
									};
								}
								if (value != '' && next_icd_pointer && previous_icd_pointer) {
									$field.removeClass('erroricddisplay js-error-class');
									return true;
								}
								if ($field.hasClass('erroricddisplay')) {
									$field.removeClass('erroricddisplay js-error-class');
								}
								$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);// To enable the disabled submit button
								return true;
							}
						}
					}
				},
			   /* copay_applied: {
				  //  enabled: false,
					message: '',
				//  trigger: 'change keyup',
					selector: '.copay_applied',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator, $field) {
								
								var copay_amt = $('input[name="copay_amt"]').val();
								var chargeId = $field.parent().prev().find("input:not(:hidden)").attr('id');
								var charge_val = $("#"+chargeId).val()                                   
								var tot_copay = 0;
								$(".copay_applied:not(:disabled)").map(function () {
									 tot_copay += Number($(this).val());                                      
								});                                    
								value_new = checkcopayamount(); 
								//console.log("copay apply"+copay_apply) ;
								 if(value != "" && parseFloat(tot_copay)>parseFloat(copay_amt) && copay_amt != ''){
									 //$field.addClass('js-error-class');
									console.log("comes if")
									return {
										valid:false,
										message:"Entered amount should be less than the copay amount"
									}
								}else if(tot_copay != "" && (parseFloat(copay_amt) == undefined || parseFloat(copay_amt) ==0 || copay_amt=='')){
									// console.log("comes elseif")
									 //$field.addClass('js-error-class');
									
									return {
										valid:false,
										message:"Enter Copay amount"
									}
								} else if(value != "" && !value_new && copay_amt != ''){
									//$field.addClass('js-error-class');
								  // console.log("comes elseif second")
									return {
										valid:false,
										message:"Enter valid copay applied "
									}
								} 
								else if(value != "undefined"&& copay_amt != "" && typeof copay_amt != "undefined" && (tot_copay == "" || tot_copay == 0)){  
									 //  console.log("comes elseif third")
									return {
										valid:false,
										message:"Enter copay applied "
									}
								}  else{
								   // console.log("comes else")  
								   //  $('form#js-bootstrap-validator').bootstrapValidator('enableFieldValidators', 'copay_applied', true);   
									//$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);                              
									return true; 
								} 
							   
							}
						}
					}
				},*/
				copay_applied: {
				  //  enabled: false,
					message: '',
				//  trigger: 'change keyup',
					selector: '.copay_applied',
					validators: {
						callback: {
							message: 'not valid',
							callback: function (value, validator, $field) {                                    
								var copay_amt = $('input[name="copay_amt"]').val();
								var tot_copay = 0;
								$(".copay_applied:not(:disabled)").map(function () {
									tot_copay += Number($(this).val());                                      
								});
								if(tot_copay != "" && (parseFloat(copay_amt) == undefined || parseFloat(copay_amt) ==0 || copay_amt=='')){                                        
									return {
										valid:false,
										message:"Enter Copay amount"
									}
								}
								else if(!isNaN(parseFloat(copay_amt)) && parseFloat(copay_amt) != parseFloat('0') && typeof copay_amt != 'undefined' && 
									parseFloat(tot_copay) == '0')   {
									return {
										valid:false,
										message:"Enter copay applied "
									}
								} else if(!isNaN(parseFloat(copay_amt)) && parseFloat(copay_amt) != parseFloat('0') && parseFloat(copay_amt) != parseFloat(tot_copay)) {								   
									return {
										 valid:false,
										 message:"Enter valid copay applied "
									 } 
								}
								return true;
							}
						}
					}
				},
				'box_24_AToG[]': {
					trigger: 'change keyup',
					validators: {
						 callback: {
							message: 'Enter valid text for box24',
							callback: function (value, validator, $field) {
								var regexp =  /^[A-Za-z0-9 _.]+$/;
								var id = $field.attr('id'); //console.log("id value"+id);
								var cpt_check = $('input[name="cpt['+id+']"]').val(); //console.log("cpt_check"+cpt_check);
								if(value != '' &&(cpt_check == '' || typeof cpt_check == "undefined")){
									return {
										valid:false,
										message:"Please fill CPT"
									}
								} else if(!regexp.test(value) && value != '' && typeof value != "undefined"){
									return {
										valid:false,
										message:"Enter valid text for box24"
									}
								} else {
									 return true;
								}
							}
						}
					}
				}, 
				money_order_no:{
					enabled: false,
					trigger: 'change',
					validators:{
					regexp: {
						regexp: /^[a-zA-Z0-9_ ]*$/,
						message:"Enter valid numbers"
					},
					remote: {
						message: 'This Money order number already exists',
						url: api_site_url+'/payments/checkexist',  
						data:function(validator){
                            var url = $(location).attr('href'),
                            parts = url.split("/"),
                            last_part = parts[parts.length-1];
							return {
								type:"MO",
								patient_id: $('input[name="patient_id"]').val(),
								value:validator.getFieldElements('money_order_no').val(),
                                _token:$('input[name="_token"]').val(),
                                last_part: last_part
                            }
						},
						type: 'POST',
					   // validKey:'valid',
					},
					callback: {
						message: "",
						callback: function (value, validator, $field) {
							chkd = $('select[name=copay]').val(); 
							if (value == '' && chkd == "Money Order") { 
								return {
									valid: false,
									message: "Enter Money order number",
								};
							}
							return true;
						}
					}
				}},
				money_order_date: { 
				  enabled: false, 
					trigger: 'change keyup',
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: date_format
						},
						callback: {
							message: check_date_msg,
							callback: function (value, validator, $field) {
								var chkd = $('select[name=copay]').val();
								var check_date = value;
								var current_date = new Date(check_date);
								var d = new Date();
								if (value == '' && chkd == 'Money Order') {
									return {
										valid: false,
										message: "Enter Money order date",
									};
								}
								if (check_date != '' && d.getTime() < current_date.getTime()) {
									return {
										valid: false,
										message: future_date,
									};
								}
								else {
									return true;
								}
							}
						}
					}
				},                  
			},
		}).on('success.form.bv', function (e) {
			var resubcode = $('input[name="resubmission_code_value"]');
			/*
			if(resubcode.val() == '') {
				$('a.claimdetail').click(); 
				setTimeout(() => {
					$('select[name="resubmission_code"]').select2("val",7).change();                   
				}, 1000);
				e.preventDefault()  ;
			}
			*/
			if(($('.js-cpt').val() == "") && ($('input:checkbox#hold-option').prop('checked') == false)){
				$("#js-bootstrap-validator").data('bootstrapValidator').updateStatus('cpt', 'NOT_VALIDATED').validateField('cpt');
			}
			$('#' + formname).bootstrapValidator('disableSubmitButtons', false);      
		}).on('error.field.bv', function (event, data)  { 
			
       /* if (data.bv.getInvalidFields().length > 0) {    // There is invalid field
            field_name = data.field;   
          //  console.log("fields"+field_name);
             if(field_name=="copay_applied") {
                copay_apply = 1;
              //  valid_fields = $('.help-block[data-bv-for="'+field_name+'"]').attr("data-bv-result");  
                //if(valid_fields != "NOT_VALIDATED"){
                    $('.help-block[data-bv-for="'+field_name+'"]').show()
                // }
             }
        }*/
        data.element.data('bv.messages').find('.help-block[data-bv-for="js_icd_validation"]').hide();
    }).on('success.field.bv', function (e, data) {
        
        if (data.bv.getInvalidFields().length == 0) {    // There is invalid field
            data.bv.disableSubmitButtons(false);
        }
    });
}

//copay_apply = 0;
function daydiff(first, second) {
    return Math.round((second - first) / (1000 * 60 * 60 * 24));
}

function parseDate(str) {
    var mdy = str.split('/')
    return new Date(mdy[2], mdy[0] - 1, mdy[1]);
}

function enablefieldvalidation(formname, status)
{    
    if ($('#' + formname).length) {
        field_arr = ['js_charge_amt', 'from_dos', 'to_dos', 'billing_provider_id','refering_provider', 'rendering_provider_id', 'facility_id', 'insurance_id', 'pos_id', 'admit_date', 'discharge_date', 'copay', 'copay_amt', 'anesthesia_start','anesthesia_stop','check_no','check_date','card_type', 'js_icd_validation', 'icd_pointer', 'money_order_no', 'money_order_date'];
        $.each(field_arr, function (index, value) {
            //console.log(value);
            $('form#' + formname).bootstrapValidator('enableFieldValidators', value, status);
        });
    }
}

// Modifier search starts
$(document).on('dblclick', '.modifier_open:not([readonly])', function () {
    $('#search_table').DataTable().search('').draw();
    $('#search_table input[type=checkbox]').attr("checked", false);
    modifier_id_value = $(this).attr('id');
    modifier_id = modifier_id_value.substr(modifier_id_value.indexOf("-") + 1)
    $('#js-modifier-popup').modal('show');
    setTimeout(function () {
        $('#search_table_filter input[type="search"]').val("");
    }, 100);
    $("input[type='search']").keyup();
    makeexistmodifier(modifier_id);
    setTimeout(function () {
        modifier_checked_len = $('input[name="modifier_search[]"]:checked').length;        
    }, 600);
});

function double_modifiertap(modifier_id_value) {
    modifier_checked_len_val = '';
    $('#search_table input[type=checkbox]').attr("checked", false);
    modifier_id_value = modifier_id_value;
    modifier_id = modifier_id_value.substr(modifier_id_value.indexOf("-") + 1)
    $('#js-modifier-popup').modal('show');
    setTimeout(function () {
        $('#search_table_filter input[type="search"]').val("");
    }, 100);
    makeexistmodifier(modifier_id);
    setTimeout(function () {
        modifier_checked_len = $('input[name="modifier_search[]"]:checked').length;
    }, 600);
}

$(document).on("touchstart", ".modifier_open:not([readonly])", function (e) {
    modifier_id_value = $(this).attr('id');
    if (!tapped) {
        tapped = setTimeout(function () {
            //single_tap()
            tapped = null
        }, 300); //wait 300ms
    } else {
        clearTimeout(tapped);
        tapped = null
        double_modifiertap(modifier_id_value)
    }
});

$(document).on('ifToggled click', 'input[name="modifier_search[]"]', function () {
    var sel_popup_mod = $("#modifier_code_order").val();
    var mod_val = $(this).val();
    var checkedModifierCount =  sel_popup_mod.split(',').length;
    if ($(this).prop('checked') == true && checkedModifierCount !=4) {
		var sel_popup_modifiers_arr = sel_popup_mod.split(',');
		sel_popup_modifiers_arr.push($(this).val());
		sel_popup_mod = sel_popup_modifiers_arr.join();
		sel_popup_mod = sel_popup_mod.replace(/(^,)|(,$)/g, "");
		$('input[type=hidden][name="modifier_code_order"]').val(sel_popup_mod);
		modifierdisplay(sel_popup_mod)
	   checkedModifierCount = sel_popup_modifiers_arr.length 
	} else if (($(this).prop('checked') == false)) {
		remove_seleted_popup_mods($(this).val());
	} else{
		js_alert_popup("Maximum number of modifiers exceeds");
		setTimeout(function () {
			$('#search_table input[type=checkbox][value="' + mod_val + '"]').prop("checked", false);
		}, 50);
		return false;
	}
    
    if ($("#modifier_code_order").val() != '' && checkedModifierCount > 0) {
        $('.js-apply-modifier').show();
    } else {
        $('.js-apply-modifier').hide();
    }
});

function makecheckboxchecked(sel_popup_mod) {
    sel_popup_modifiers_arr.push($(this).val());
    sel_popup_mod = sel_popup_modifiers_arr.join();
    sel_popup_mod = sel_popup_mod.replace(/(^,)|(,$)/g, "");
    $('input[type=hidden][name="modifier_code_order"]').val(sel_popup_mod);
    modifierdisplay(sel_popup_mod);
    //$('.js-modifier-display').html(sel_popup_mod);
}

function remove_seleted_popup_mods(remove_id) {
    var sel_popup_mod = $("#modifier_code_order").val();
    var sel_popup_modifiers_arr = sel_popup_mod.split(',');
    sel_popup_modifiers_arr.splice(sel_popup_modifiers_arr.indexOf(remove_id), 1);
    sel_popup_mod = sel_popup_modifiers_arr.join();
    sel_popup_mod = sel_popup_mod.replace(/(^,)|(,$)/g, "");
    $('input[type=hidden][name="modifier_code_order"]').val(sel_popup_mod);
    modifierdisplay(sel_popup_mod);
    //$('.js-modifier-display').html(sel_popup_mod);
}

$(document).on('click', '.js-apply-modifier', function () {
    var sel_popup_mod = $("#modifier_code_order").val();
    var sel_popup_modifiers_arr = sel_popup_mod.split(',');
    $('li#js-modifier-list-' + modifier_id).find('.js-modifier').val("");
    $.each(sel_popup_modifiers_arr, function (key, value) {
        id_val_mod = parseInt(key) + parseInt(1);
        $('#modifier' + id_val_mod + '-' + modifier_id).attr('readonly', false).val(value).change();
    });
    $('#js-modifier-popup').modal('hide');
});

function makeexistmodifier(modifier_id) {
    var modifier_arr_val = [];
    var mod_val = '';
    setTimeout(function () {
        for (i = 1; i <= 4; i++) {
            mod_val = $('#modifier' + i + '-' + modifier_id).val();
            modifier_arr_val.push(mod_val);
            $('#search_table input[type=checkbox][value="' + mod_val + '"]').prop("checked", true);
        }
        //modifier_arr_val = modifier_arr_val.filter(v=>v!='');
        modifier_arr_val = modifier_arr_val.filter(function (i, v) {
            return i != '';
        });
        modifier_arr_val_join = modifier_arr_val.join();
        modifier_arr_val_joined = modifier_arr_val_join.replace(/(^,)|(,$)/g, "");
        modifierdisplay(modifier_arr_val_joined);
        //$('.js-modifier-display').html(modifier_arr_val_joined);
        $('input[type=hidden][name="modifier_code_order"]').val(modifier_arr_val_joined);
        modifier_checked_len_val = modifier_arr_val.length;
    }, 500);
}

function modifierdisplay(modifier) {
    var modifier = modifier.replace(/,/g, ", ");
    if (modifier != '') {
        $('.js-modifier-display').html("<span class='med-gray-dark no-bottom'>Selected Modifiers: </span>" + modifier);
    } else {
        $('.js-modifier-display').html("");
    }
}

$(document).on('click', '.paginate_button', function () {
    callicheck();
});

// Modifier search ends
$(document).ready(function () {
    callpaymentdenial();
});

function split(val) {
    return val.split(/,\s*/);
}

function extractLast(term) {
    return split(term).pop();
}

function callpaymentdenial() {
    $(".payment-denial")
    // don't navigate away from the field on tab when selecting an item
    .on("keydown", function (event) {
        if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
            event.preventDefault();
        }
    })
    .autocomplete({
        minLength: 1,
        autoFocus: true,
        scroll: true,
        source: function (request, response) {
            // delegate back to autocomplete, but extract the last term
            remarkval = $.parseJSON(remark);
            response($.ui.autocomplete.filter(remarkval, extractLast(request.term)));
        },
        focus: function () {
            return false;
        },
        select: function (event, ui) {
            var terms = split(this.value);
            var current_val = ui.item.value.toUpperCase();
            if (terms.length <= 3) {
                // remove the current input
                terms.pop();
                // add the selected item
                arr_val = $.inArray(current_val, terms);
                if (arr_val == -1) {
                    terms.push(current_val);
                }
                terms.push("");
                this.value = terms.join(", ");
                denialdescription();
                return false;
            } else {
                var last = terms.pop();
                $(this).val(this.value.substr(0, this.value.length - last.length - 2)); // removes text from input
                return false;
            }
        },
        change: function (event, ui) {
            if (!ui.item) {
                val = $(this).val();
                val = val.split(',');
                val.splice(-1, 1)
                $(this).val(val);
            }
            var terms = split(this.value);
            var denial_join_val = terms.join(", ");
            var denial_codes = terms.map(function (x) {
                return x.toUpperCase()
            });
            $(this).val(denial_codes);
            denial_code = denialdescription();
        }
    });
}

$(document).on('change', '.payment-denial', function () {
    //var val = $(this).val();
    //console.log("value"+val);
    //$(this).val()
});

function denialdescription() {
    var values = $('.payment-denial').map(function () {
        return $.trim(this.value);
    }).toArray();
    url = api_site_url + '/getremarkcode';
    values = values.join();
    values = encodeURI(values)
    if (values != '') {
        $.get(url = api_site_url + '/getremarkcode/' + values, function (data) {
            data = $.parseJSON(data);
            codes = data.code;
            appended_val = '';
            $.each(codes, function (key, value) {
                appended_val += '<p class="no-bottom med-gray-dark">' + value + '</p>';
            });
            $('.js-remark-append').html(appended_val);
        });
    } else {
        $('.js-remark-append').html("");
    }
    return values;
}

$(document).on('click', '.js-list-patientins', function () {
    patient_id = $('input[name="patient_id"]').val();
    getInsuranceList(patient_id);
});

function getInsuranceList(patient_id)
{
    var url = api_site_url + '/payments/searchpatient/' + patient_id;
    $.get(url, function (data) {
        $('#patient_insurance_model .modal-body').html(data);
        $('#patient_insurance_model').modal('show');
        callicheck();
    });
}

$(document).on('click ifToggled change', '.js-patient-ins', function () {
    var insurance_val = $(this).attr('data-insurance'); //console.log("insurance sel val"+insurance_val );
    $('input[name="change_insurance_id"]').val(insurance_val);
});

$(document).on('click change', '.js-change-insurance', function () {
    var radio_length = $(':radio[name= "patient_insurance_list"]:checked').length
    if (!radio_length) {
        js_alert_popup("Choose any one insurance");
        return false;
    } else {
        if ($('.js-popupinsuranceadd:visible').length) {
            $('.js-popupinsuranceadd').click()
        } else {
            $('.jsinsuranceform').submit();
        }
        $("#patient_insurance_model").modal('hide');
    }
})

$(document).on('show.bs.modal','#patient_insurance_model', function () { 
	$('input[name="change_insurance_id"]').val(""); // To reset selected insurance id as null 
}); 

$(document).on('hide.bs.modal','#patient_insurance_model', function () { 
    // Click on close icon of the popup needs to enable the continue button in post insurance screen.    
   // $('.jsinsuranceform').find('.js-sel-claim').prop('disabled', false).prop('checked', false).iCheck('update');
    $('.jsinsuranceform').bootstrapValidator('disableSubmitButtons', false);    
	//$('input[name="claim_ids"]').val("");	
}); 

$(document).on('click ifToggled', '.js_menu_payment', function () {
    var paymenttype = $('input[name=payment_type]:radio:checked').val();
    $('.js_submenu_payment').not(":disabled").prop('checked', $(this).is(":checked"));
    if (paymenttype == "Credit Balance") {
        setTimeout(function () {
            checkpaytypeanddeselectinput("Credit Balance");
        }, 100);
    } else {
        getclaimid();
    }
    $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
    $('.js-patient-paymentform').bootstrapValidator('disableSubmitButtons', false);
});

$(document).on('click', '.js_void_claim', function () {
    var data_delete = $(this).attr("data-url");
    $('#js_confirm_box_charges_content').html("Are you sure you want to delete?");
    $("#js_confirm_box_charges")
        .modal({show: 'false', keyboard: false})
        .one('click', '.js_modal_confirm1', function (eve) {
            confirm_alert = $(this).attr('id')
            if (confirm_alert == 'true') {
                $('.js_void_claim').hide();
                $.get(data_delete, function (data) {
                    if (data = "success") {
                        js_sidebar_notification('success',"Deleted successfully"); 
                        window.location = api_site_url + '/payments';
                    } else{
                        return false; 
                    }
                });
            } else {
                return false;
            }
        });
});

$(document).on('keydown', '.js-insurance-tabchange', function (e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode == 9) {
        //$('.js-authpopup').click();
        $('.call-datepicker').datepicker('hide');
        $('#date_of_injury').datepicker('hide');
    }
});

function startDate(start_date, end_date) {
    var date_format = new Date(end_date);
    if (end_date != '' && date_format != "Invalid Date") {
        return (start_date == '') ? eff_date_valid : true;
    }
    return true;
}

function endDate(start_date, end_date) {
    var eff_format = new Date(start_date);
    var ter_format = new Date(end_date);
    if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
        var getdate = daydiff(parseDate(start_date), parseDate(end_date));
        return (getdate > 0) ? true : ter_date_valid;
    } else if (start_date != '' && eff_format != "Invalid Date") {
        return (end_date == '') ? ter_date_valid : true;
    }
    return true;
}

function enabledataTables()
{
    if($("#js_popup_claimpatient_table:visible").length > 0)  
    {   
        if ($("#js_popup_claimpatient_table:visible")) {
            $("#js_popup_claimpatient_table").DataTable({
                "paging": false,
                "ordering": true,
                "info": false,
                "order": [1, 'asc'],
                "columnDefs": [{
                        "targets": 0,
                        "orderable": false
                    }]
            });
        }
    }    
}

/*
 *
 * Era auto post function
 *
 */

$(document).on('click', '#js-post', function () {
    var action_url = api_site_url + "/autoPostData";
    var token = $('input[name="_token"]').val();
    var checkedVals = $('.js-era-post:not(:disabled):checkbox:checked').map(function () {
        return this.value;
    }).get();
    var checkedids = $('.js-era-post:not(:disabled):checkbox:checked').map(function () {
        return this.id;
    }).get();
	
    if (checkedVals == '') {
        js_alert_popup("");
        $(".nav.nav-list.line-height-26").append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center">Please select atleast one check</div>');
    } else {
        displayLoadingImage();
        $.ajax({
            type: 'post',
            url: action_url,
            data: {'_token': token, 'claim_no': checkedVals, 'id': checkedids},
            success: function (data) {
                /* js_alert_popup("");
                $(".nav.nav-list.line-height-26").append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center">Successfully Submitted Claim :' + data.claim_success_count + '</div>');
                $(".nav.nav-list.line-height-26").append('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center">Failed Claim :' + data.claim_fail_count + '</div>'); */
				
				$('#autoPostDetails').modal('toggle');
				$('#autoPostDetails').find('.modal-body-content').html(data);
				hideLoadingImage();
            }
        });
    }
});

/*
 *
 * Era auto post function
 *
 */


/*
 *
 * Era check box checked and unchecked function  Start
 *
 */

$(document).on('click', '#checkAll', function () {
    $('.js-era-post:not(:disabled)').prop('checked', $(this).is(":checked"));
    icheck_eras();
});

$(document).on('click', '.js-era-post', icheck_eras);

// Billing Team Feedback : Era Auto 500 error fixed  : 16 Aug 2019 : Selva

$(document).on('change', 'input[name="era_claim"]', function () {
    if ($(".js-era-post").not(":disabled").length == $('.js-era-post:checked').length) {
        $('#checkAll').prop('checked', true);
    } else {
        $('#checkAll').prop('checked', false);
    }
});

function icheck_eras() {
    var Claim_count = 0;
    $(':checkbox:checked').each(function (i) {
        Claim_count = parseInt(Claim_count) + parseInt($(this).attr('data-claim-count'));
        if (Claim_count > 250) {
            js_alert_popup("Only 250 Claim can process at a time");
            $(this).prop('checked', false);
        } else {
            $('#checked_count').html(Claim_count);
        }
    });
    var not_disabled_boxes = $('.js-era-post').not(":disabled");
    if ($('.js-era-post:checked').length == 0)
        $('#checked_count').html("0");
    if ($('.js-era-post:checked').length == not_disabled_boxes.length) {
        // $('#checkAll').prop('checked', $(this).is(":checked"));
        $('#checkAll').prop('checked', true);
    } else {
        $('#checkAll').prop('checked', false);
    }
   /* setTimeout(function () {
        $('.js-era-post').iCheck('update');
    }, 50); */
}

/*
 *
 * Era check box checked and Unchecked function END
 *
 */
 
 /* 
 *  Showing error message for price filed 
 */
 
$(document).on('focusout','.js_charge_amt',function(){
    var AmountArr = [];
    $('.js_charge_amt').each(function(){
        AmountArr.push($(this).val());
    });
    if(AmountArr.indexOf("0.00") != -1)
        $("[data-bv-for='js_charge_amt']").show();
    else
        $("[data-bv-for='js_charge_amt']").hide();  
});

 /* 
 *  Showing error message for price filed 
 */
 
$(document).on('click','.js-charge_save',function(e){ 
	if($(this).attr('data-type-msg') == 'yes'){ 
		var $this = $(this);   
		e.preventDefault();
		$.confirm({
			text: 'Charge will move to "Ready" status on Save',
			confirm: function() {			
				$(".dm-date").datepicker("hide"); //Charges : If calendar is opened and clicking on save button, design issue fixed
				var cptArr = [];
				var from_date = [];
				$('.js_from_date').each(function(){
					from_date.push($(this).val());
				});
				
				$('.js-cpt').each(function(){
					cptArr.push($(this).val());
				});
				cptArr = cptArr.filter(Boolean);
				from_date = from_date.filter(Boolean);
				var hold_cheked_len = $("#hold-option").is(':checked');
				if(from_date.length != cptArr.length && !hold_cheked_len){
					js_alert_popup("Procedure code is required field. Please enter the same.");
					e.preventDefault();
				}
				formid = $('.js-check-number:not([readonly]').closest("form").attr('id');
				type = $('.js-check-number:not([readonly]').attr('data-type');    
				var check_val = $('.js-check-number:not([readonly]').val();   
				checkno = $('#' + formid).find("input[name='check_no']").val();
				if (checkno != '' && typeof checkno != "undefined")
					if (checknumbervalidation(checkno)) {
						checkvalidation(formid, type);
				   //     e.preventDefault();
					}
				$this.off('submit').submit(); // Submit the form 
				return true;
			},
			cancel: function() {
				$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);
				$("input[type='submit']").removeAttr('disabled', 'disabled');
				return false;
			}
		});
		
	}else{
		$(".dm-date").datepicker("hide"); //Charges : If calendar is opened and clicking on save button, design issue fixed
		var cptArr = [];
		var from_date = [];
		$('.js_from_date').each(function(){
			from_date.push($(this).val());
		});
		
		$('.js-cpt').each(function(){
			cptArr.push($(this).val());
		});
		cptArr = cptArr.filter(Boolean);
		from_date = from_date.filter(Boolean);
		var hold_cheked_len = $("#hold-option").is(':checked');
		if(from_date.length != cptArr.length && !hold_cheked_len){
			js_alert_popup("Procedure code is required field. Please enter the same.");
			e.preventDefault();
		}
		formid = $('.js-check-number:not([readonly]').closest("form").attr('id');
		type = $('.js-check-number:not([readonly]').attr('data-type');    
		var check_val = $('.js-check-number:not([readonly]').val();   
		checkno = $('#' + formid).find("input[name='check_no']").val();
		if (checkno != '' && typeof checkno != "undefined")
			if (checknumbervalidation(checkno)) {
				checkvalidation(formid, type);
		   //     e.preventDefault();
			}
	}
});
 
$(document).on('click','.js_note_confirm.btn.btn-medcubics-small.js_common_modal_popup_save.close_popup', function(){
     setTimeout(function(){ 
        $('body').removeClass('modal-open');
        //$('body').removeClass('fixed');
    }, 2000);
});
  
// event listener for keyup
function checkTabPress(e) { 
    "use strict";
    // pick passed event or global event object if passed one is empty
    e = e || event;
    if (e.keyCode == 9 && $('.js_common_modal_popup').hasClass('fade in')) {
        $(".close").focus();
    }
    if (e.keyCode == 9 && $('#choose_claims').is(':visible')) {
        $('#choose_claims').find('input.foc:first').focus();
        $('input').removeClass("foc");
    }
}

var body = document.querySelector('body');
body.addEventListener('keyup', checkTabPress);

//shortcut keys
$(document).mapKey('Alt+p', function (e) {
    if (!$("body").hasClass("modal-open")) { 
        $('input[name="search"]').focus();
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+d', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").not('#providerpop').click();
        $('#providerpop').select2('open');
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+i', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").click();
        $('#icd1').focus();
        //$('#icd1').trigger("focus");
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+c', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").click();
        $('input[name="dos_from[0]"]').focus();
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+u',function(e){
    if (!$("body").hasClass("modal-open")) { 
        $(".js-authpopup").click();
        $(".js-collpased").css("display","block");
        closeTheCalendar();
    }
});

$(document).mapKey('Alt+E',function(e){
    if (!$("body").hasClass("modal-open")) { 
        var crAuth = $(".js-collpased").css('display');
        if(crAuth == 'none'){
            $(".js-collpased").css("display","block");
            $("#js_pos").select2('open');
        }
    }
});

/*$(document).mapKey('Alt+n', function (e) {
    if (!$("body").hasClass("modal-open")) { 
        $("#select2-drop-mask").click();
        $('input[name="note"]').focus();
        closeTheCalendar();
        return false;
    }
});*/

$(document).mapKey('Alt+w', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").click();
        $('.js-cpt-allowed:first').focus();
    }
});

//insurance payment Posting – Focus on "Adjustments reason" in first line item popup
$(document).mapKey('Alt+o', function (e) {
    if (!$("body").hasClass("modal-open")) {
        _makeToggle(0);
        $('input[data-type="adjcalc"]:first').focus();
    }
});

//Payment Posting – Focus on "Patient Paid" in first line item
$(document).mapKey('Alt+o', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $('.js_pateint_paid:first').focus();
    }
});

$(document).mapKey('Alt+s', function (e) {
    if (!$("body").hasClass("modal-open")) { 
		var auth = $("#auth").css('display');
		if(auth == 'block'){
			county = 0;
			$("#js-auth-pop").submit();
		} else {
         //removed this functionality
         // $('form[name="chargeform"]').submit();
         // window.onbeforeunload = UnPopIt;
		}
		return false;
    }
});

$(document).mapKey('Alt+a', function (e) {
    if (!$("body").hasClass("modal-open")) { 
        var disapleCheck =  $("div.js-replace-section .js-disable").attr("class");
        $("#select2-drop-mask").click();
        closeTheCalendar();
        $('#anesthesia_start').focus();
        return false;
    }
});

$(document).mapKey('Alt+shift', function (e) {
    if (!$("body").hasClass("modal-open")) { 
        if($('.append:visible')){
            $('.append').click();
            return false;
        }    
    }
});

function UnPopIt() 
{ 
    /* nothing to return */ 
}

function highlight(tableIndex,tblName) {
    // Just a simple check. If .highlight has reached the last, start again
    var tbl ="#"+tblName;
    if( (tableIndex+1) > $(''+tbl+' tbody tr').length )
        tableIndex = 0;
    
    // Element exists?
    if($(''+tbl+' tbody tr:eq('+tableIndex+')').length > 0) {
        // Remove other highlights
        $(''+tbl+' tbody tr').removeClass('highlight-table');
        
        // Highlight your target
        $(''+tbl+' tbody tr:eq('+tableIndex+')').addClass('highlight-table');
    }
}

window.tabClickCount = 0;
$(document).keydown(function (e) {
    var auth = $("#auth").css('display');
    var patientMoSt = $("#Patient_list").css('display');
    var patientMainPay = $("#choose_claims").css('display'); 
    var imosearch = $("#imosearch").css('display');
    switch(e.which) 
    {
        case 38:
            if(patientMoSt == 'block'){
               highlight($('#patientList tbody tr.highlight-table').index() - 1,'patientList');
              }else if(auth == 'block'){
               highlight($('#auth_result tbody tr.highlight-table').index() - 1,'auth_result');
              }else if(patientMainPay == 'block'){
               highlight($('#js_MainPayment tbody tr.highlight-table').index() - 1,'js_MainPayment');
              }else if(imosearch == 'block'){
               highlight($('#imosearch tbody tr.highlight-table').index() - 1,'imosearch');
              }
            break;

        case 40:
                if(patientMoSt == 'block'){
                    highlight($('#patientList tbody tr.highlight-table').index() + 1,'patientList');
                }else if(auth == 'block'){
                   highlight($('#auth_result tbody tr.highlight-table').index() + 1,'auth_result');
                }else if(patientMainPay == 'block'){
                   highlight($('#js_MainPayment tbody tr.highlight-table').index() + 1,'js_MainPayment');
                }else if(imosearch == 'block'){
                   highlight($('#imosearch tbody tr.highlight-table').index() + 1,'imosearch');
                }
            break;

        case 32:
            if(patientMoSt == 'block'){
                $(this).find('tr.highlight-table td:first input').trigger("click");
                e.preventDefault();
                $('td:first',$('#patientList tbody tr.highlight-table')).children();
            }else if(auth == 'block'){
                $('td:first',$('#auth_result tbody tr.highlight-table')).children();
                e.stopPropagation();
            }else if(patientMainPay == 'block'){//alert('space');
                $(this).find('tr.highlight-table td:first input').trigger("click");
                $('td:first',$('#js_MainPayment tbody tr.highlight-table')).children();
                //e.stopPropagation();
            }else if(imosearch == 'block'){
                $(this).find('tr.highlight-table td:first input').trigger("click");
                e.preventDefault();
                $('td:first',$('#imosearch tbody tr.highlight-table')).children();
            }    
            break;
			
        case 9:
             if(patientMainPay == 'block'){
                 tabClickCount = tabClickCount + 1;
                 if(tabClickCount > 2)
                    highlight($('#js_MainPayment tbody tr.highlight-table').index() + 1,'js_MainPayment'); 
            } else if(imosearch == 'block'){
                highlight($('#imosearch tbody tr.highlight-table').index() + 1,'imosearch');
            }   
            break;
			
        case 13:
            if(patientMoSt == 'none'){
                $('form[name="chargeform"]').submit();
            }else if(patientMainPay == 'block'){
               $('td:first',$('#js_MainPayment tbody tr.highlight-table')).children();
            }
            tabClickCount = 0;
            break;
    }
});

$(document).on('ifToggled click', '.js-edit-lineitem', function(){
    var sList = 0;
    $(".js-edit-lineitem").each(function(){
        sList = this.checked ? parseInt(sList)+parseInt(1) : sList;
    });
    var disabled_len = $('.js-edit-lineitem').not(":disabled").length;
    if(disabled_len == 1 && sList == 0){
        js_sidebar_notification('error',"Minimum one line item should checked"); 
        if(!$(this).is(":checked")) { 
            setTimeout(function(){
                $('.js-edit-lineitem').not(":disabled").prop('checked', true); 
			},10);
        }        
    }else if(sList == 0){
        js_sidebar_notification('error',"Minimum one line item should checked"); 
        $('.js-edit-lineitem').not($(this)).not(":disabled").prop('checked', true);
    }      
});

function closeTheCalendar(){
    var status = $('#ui-datepicker-div').css("display");
    if( status= "block"){
         $('#ui-datepicker-div').hide();
    }
    var timePickerStatus = $('.bootstrap-timepicker-widget').css("display");
    if(timePickerStatus ="block"){
        $('.bootstrap-timepicker-widget').removeClass('open');
    }
}

function checkcopayamount($field){
   var copay_amt = $("input[name='copay_amt']").val();
   var tot_copay = 0;
   var newcopay= 0; 
    tot_copay = $(".copay_applied:not(:disabled)").map(function () {
        newcopay = ($(this).val()!= '')?parseFloat(newcopay)+ parseFloat($(this).val()):newcopay+ parseFloat(0);
        return  parseFloat(newcopay);
    });     
    if(copay_amt == newcopay) {
        return true;
    }
    return false;   
}

$(document).on('click ifToggled', ".js-showhide-box24", function(){
    var data_id = $(this).attr('id');
    var _this = $(this);
    $('#js_box_24_'+data_id).slideToggle(100,function(){
        visibleElements  = $('div#js_box_24_'+data_id+':visible').length
        if(visibleElements){
            _this.removeClass("fa-plus").addClass("fa-minus");
        } else{
            _this.removeClass("fa-minus").addClass("fa-plus");
        } 
    });
});

$(document).on('show.bs.modal', '.modal', function () {
    var  Id = $(this).attr("id");
    var  content = $(".js_show_content").html(); 
    var  data_patient = $(".js_show_content").attr("data-patient");
    if((Id == "payment_editpopup" || Id == "choose_claim") && typeof content != "undefined"){
        if(data_patient != "" && typeof data_patient != "undefined" && Id == "choose_claim") {
            $(this).find(".modal-title").html("Credit balance");
        } else{
            $(this).find(".modal-title").html(content);
        }            
    }    
});
 
 $(document).on('hidden.bs.modal', '#choose_claims', function () {   
    $("form#js-insuranceajax").find('input[name="change_insurance_id"]').remove(); // Here form submission makes problem due to this fields so removed
 });

// Dos from and To dates automatic papulate starts
function getDosdata(){    
    var from_date = $('.js-append-parent').find('.js_from_date').filter(function(){
            return  (!$(this).val()?"":$(this).val())
        }).val();
    var to_date = $('.js-append-parent').find('.js_to_date').filter(function(){
            return  (!$(this).val()?"":$(this).val())
        }).val();
    date_return = from_date;
	if(typeof from_date == "undefined"){
		date_return = (typeof from_date != "undefined")?to_date:"";
	}
	return date_return;    
}

$(document).on("keypress", function(e){
    if($('.js_validate_date').length)
        dateEntry(e);
});

$(document).on('focus', '.js_validate_date', function(e){
    if($('.js_validate_date').length)
    dateEntry(e);
});

function dateEntry(e){
  var tag = $(e.target);
    if(tag.hasClass('js_from_date') && tag.val() == ''){
       var to_sel = tag.attr('name').replace("from", "to");
       to_sel = $(`input[name='${to_sel}']`)
       dos_date = getDosdata(); 
       tag.val(dos_date).change() ;
      // to_sel.val(dos_date)
    }    
}

$(document).on('click',".unposted_notes_save", function () {
	var pmt_id = $(this).attr('data-pmt-id');
	$('#unposted_icon_status_'+pmt_id).addClass('fa-spinner fa-spin');
	var note = $("textarea.unposted_notes_"+pmt_id).val();
	var url = api_site_url+'/payments/noteAdd';
	
	$.ajax({
		type : 'post',
		url  : url,
		data : {'_token':$('#csrf_token').val(),'note':note,'pmt_id':pmt_id},
		success :  function(data) {
			$('#unposted_icon_status_'+pmt_id).removeClass('fa-spinner fa-spin').addClass('fa-check');
		}
	});
});

// Other Adjsutment related calculations starts here
$(document).on("change", '.js_other_adj', function(){
    var id = $(this).attr("id");
    var type = $(this).attr("data-type");
    OtherAdjCalculation(id, type);
});

$(document).on("click", ".js_other_adj_toggle", function(){
    var id = $(this).attr("id");
    var type = $(this).attr("data-type");
    var parentdatalength = $("#js_other_adj_"+id).children('.js-div-append').length; 
    
    switch(type) {
        case 'add':
            (parentdatalength >parseInt(3))?js_sidebar_notification('error',"Maximum limit reached"):AddRemvoveEle(id, type, this);return false;
			break;
			
        case 'remove':
            AddRemvoveEle(id, type, this);
			break;
			
        case 'adjcalc':
            OtherAdjCalculation(id, type); 
			break;
			
        case 'adjsav':
            OtherAdjCalculation(id, type); 
            _makeToggle(id);
			break;
			
        case 'adjcancel':
            _makeToggle(id);
            _CancelAdj(id);
			break;
			
        default:
            _makeToggle(id);
    }
});

var _makeToggle =  (index) =>
{
    var _iD = "js_other_adj_"+index;//alert(_iD);
    $('.js_other_adjust').not("#"+_iD).hide();
    $("#"+_iD).toggle();   
};

/*
function AddRemvoveEle(index, type, currenObj)
{    
    var _iD = "js_other_adj_"+index;
    var parentdata = $(currenObj).parent();
    console.log("parent data");
    console.log(parentdata);
    var parent = parentdata.get(0);  
    console.log("parent");
    console.log(parent);  //return false;
    parentdata.children("select").select2("destroy");
    var len = $('#'+_iD).find(".js-div-append").length;
    switch(type) {
        case 'remove':
         (len >1)?parentdata.remove():"";  
         OtherAdjCalculation(index, 'adjcalc');
        break;
        default:
         var cln = parent.cloneNode(true);
            $("#"+_iD).find('.js-div-append').last().after(cln);
            setTimeout(function(){                
                parentdata.find("select").select2();
                $(currenObj).hide();
             }, 200)        
    }
    _addRemoveIcon(index, type);
    
}*/
function AddRemvoveEle(index, type, currenObj)
{    
    var _iD = "js_other_adj_"+index;
    var parentdata = $(currenObj).parents('.js-child'); 
    var parentdataNew = $("#"+_iD).children('.js-div-append').first();
    var parent = parentdataNew.get(0);   
    $("#"+_iD).children('.js-div-append').find("select").select2("destroy");
    var len = $('#'+_iD).find(".js-child").length;
    switch(type) {
        case 'remove':
         (len >0)?parentdata.remove():"";  
         OtherAdjCalculation(index, 'adjcalc');
        break;
		
        default:
			var cln = parentdataNew.clone(true);
			$("#"+_iD).find('.js-div-append').last().after(function(){
				id =  $('.js-child').length;
				return '<div class="js-div-append form-group-billing js-child" id = js-childiv-'+id+' data_id = '+id+'>' + cln.html() + "</div>";
			});
    }
    setTimeout(function(){
		$('.js-child').find("select").select2();
	}, 200) 
    _addRemoveIcon(index, type, currenObj);
}

function OtherAdjCalculation(index, type)
{     
    var _iD = "js_other_adj_"+index;
    var _name = $('input[name="other_adj_total['+index+']"]');
    var _OtherAdjName = $('input[name="with_held['+index+']"]');
    var tot_amt = 0;     
    switch(type)   {
        case 'adjcalc':
			$('#'+_iD+' .js_other_adj').map(function () {
				tot_amt += Number($(this).val());
			});
			_name.val(parseFloat(tot_amt).toFixed(2));
			break;
		
        case 'adjsav':
			let value = _name.val();
			value = (value == '')?"0.0":value;
			if($('input[name="payment_type"]').val()!="Adjustment"){
				return_data = getOtherAdjCalcRes(index, value);
				(return_data == true)?_OtherAdjName.val(value).change():"";
			} else{
            _OtherAdjName.val(value).change();
			}
			break;
    }     
}

function getOtherAdjCalcRes(index, value)
{    
    comon_selector = commonselector(index);
    var allowed_amt = comon_selector['allowed'];
    var coins = comon_selector['coins'];
    var copay = comon_selector['copay'];
    var deductable = comon_selector['deductable'];
    var withheld = comon_selector['withheld'];
    var total_other_paid = Number(copay.val()) + Number(deductable.val()) + Number(value) + Number(coins.val());    
    if(allowed_amt.val()<=0 && total_other_paid>parseFloat(0)){
        js_sidebar_notification("error","Allowed Must be given");
        return false;
    } else if (parseFloat(allowed_amt.val()) < total_other_paid) {
        withheld.val("0.00");       
        return false;
    } else {
       return true;
    }
}

var _CancelAdj = (index) => 
{
	var _iD = "js_other_adj_"+index;
	var _OtherAdjName = $('input[name="with_held['+index+']"]');
	$('#'+_iD).find("input[type=text]:input:not([readonly])").val("");
	_OtherAdjName.val("0.00").change();
	if($('input[name="payment_type"]').val()=="Adjustment"){
		$(".js_other_adjust").find('input').removeClass('js-error-class erroricddisplay');
		$("td .js-withheld").removeClass('js-error-class erroricddisplay');
		$(".js_other_adjust").find('input[value="Save"]').prop('disabled',false);
		$(".js-save").prop('disabled',false);
	}
}

var _addRemoveIcon = (index, type, currenObj) => {
     var _iD = "js_other_adj_"+index;
     //var len = $('#'+_iD).find(".js-div-append:visible").length;
     //var parentId = $(currenObj).parents('.js-child').attr("data_id");
     var _totaldiv = $("div#"+_iD).find('.js-total-div');
     var childdiv = $("div#"+_iD).find('.js-child').length;
     $('#'+_iD).find('.js-addremove-adj:not([data-type="remove"])').hide();
     $('#'+_iD).find('.js-addremove-adj[data-type="remove"]:first').hide();
     if($("div#"+_iD+"  div.js-div-append:visible").length == 0){ 
        $("div#"+_iD+"  div.js-div-append").siblings('.js_main_div').find('.js-addremove-adj').show();
     } else{
        $("div#"+_iD+"  div.js-div-append:visible:last").find('.js-addremove-adj').show();   
     } 
     (childdiv >0)?  _totaldiv.show():_totaldiv.hide();   
     $("div#"+_iD).find('.js-total-div').removeClass('hide');
}
//Other adjustment related calculation ends here

$(document).bind('keyup', function(event) {
	if(event.which === 9) {
		var target = $( event.target );
		var ename = target.attr("name");
		if(typeof ename != "undefined" && ename.indexOf("with_held") > -1){
			var id = target.attr("id");
			_makeToggle(id);
		}
	}
});

/* search icd-10 using shortcut key (Alt+0) in Diagnosis - ICD 10 input text box if focus*/
$(document).mapKey('Alt+o', function (e) {
    if (!$("body").hasClass("modal-open")) { 
		if($('.js-icd:not([readonly])').is(':focus')) {
			var icd_id = $(this).attr('id');
			$(".js_icd_val").val(icd_id);
			$('.js_search_icd_list').val("");
			$("#icd_imo_search_part").html("");
			$('#imosearch').modal('show');
			$(this).blur();
			$('input[name = "search_icd_keyword"]').focus();
		}
		return false;
    }
});

/*Charges : Dos already exists popup :if save click yes using shortcut key (Alt+y)*/
$(document).mapKey('Alt+y', function (e) {
    if($('#js_confirm_box_charges').is(':visible')){
        $('.js_common_modal_popup_save').click();
    }
    return false;
});

/*Charges : Dos already exists popup :if cancel click no using shortcut key (Alt+n)*/
$(document).mapKey('Alt+n', function (e) {
    if($('#js_confirm_box_charges').is(':visible')){
        $('.js_common_modal_popup_cancel').click();
    }
    return false;
});

$(document).on("click", ".js_save_resubmit", function(){
    $("input[name='save_resumit']").val("1");
});

$(document).on('change','#backDate',function(){ 
	$('#form_backDate').val($(this).val());
});

$(document).ready(function(){
	if($('#backDate').val() != ''){
		$('#form_backDate').val($('#backDate').val());
	}
})

$(document).mapKey('Alt+r', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").not('#js-insurance').click();
        $('#js-insurance').select2('open');
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+m', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").not('#js_claim_status').click();
        $('#js_claim_status').select2('open');
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+h', function (e) {
    if (!$("body").hasClass("modal-open")) {
        $("#select2-drop-mask").not('#js_hold_reason').click();
        $('#js_hold_reason').select2('open');
        closeTheCalendar();
        return false;
    }
});

$(document).mapKey('Alt+t', function (e) {
    var url = api_site_url + '/payments/get-e-remittance';
    var win = window.open(url, '_blank');
    //console.log(e.target.className);
    if (!$("body").hasClass("modal-open") || e.target.className == 'js_eri_key') {
       // $("#js_e-remittance").click();
        win.focus();
        closeTheCalendar();
        return false;
    }
});

//insurance payment Posting – hide "Adjustments reason"  popup for focus on other element
/*$(document).on('click keyup', '.js_insurance_create_form', function (e) {
    //console.log(e.target.className);
    if (e.target.className != 'js_other_adjust' && e.target.className != 'js-withheld-tootltip js_other_adj_toggle fa fa-sticky-note-o fa-5x cur-pointer bill-lblue')
        $(".js_other_adjust").hide();

});*/

//Enter button for patient search when create new charge in Main charges
$(document).on('keypress change', 'input[name="search"]', function (e) {
    if (e.keyCode == 13) {
        $('#js-search-patient').trigger('click');
    }
});

// Adjustment reverse entry validation
// Author: baskar
$(".js-insurance-table").on("keyup change", "input,select", function(){
    payment_type = $('input[name="payment_type"]').val();
    payment_method = $('input[name="payment_method"]').val();
    if((payment_type=='Adjustment' && payment_method =='Insurance') || (payment_type=='Payment' && payment_method =='Insurance')){
        var val = '';
        var amt = 0;

        id = $(this).closest('li').attr('id');
        insurance_id = $('input[name="insurance_id"]').val();
        claim_id = $('input[name="claim_id"]').val();
        cpt = $('input[name="cpt['+id+']"]').val();
        if($(this).parent().prev().find('select option:selected').val()){
            val = $(this).parent().prev().find('select option:selected').val();
        } else {
            if($(this).parent().prev().find('input').val() == 'CO253'){
                val = 0;
                error = $(this);
            } else{
                if($(this)[0]['tagName']!='SELECT')
                    val = 'all';
                else
                    val = $(this).val();
            }
        }
        if($(this)[0]['tagName']=='SELECT'){
            amt = $(this).parent().next().find('input').val();
            error = $(this).parent().next().find('input');
        } else {
            amt = $(this).val();
            error = $(this);
        }
        if(amt<0){
            if(payment_type=='Adjustment' && payment_method =='Insurance') {
                $.ajax({
                    url:api_site_url+'/api/adjustment_validation',
                    method:"post",
                    data:{val:val,amt:amt,insurance_id:insurance_id,claim_id:claim_id,cpt:cpt,name:$(this).attr('name')},
                    success:function(result){
                        if(result<0){
                            error.addClass('js-error-class erroricddisplay');
                            $("td .js-withheld").addClass('js-error-class erroricddisplay');
                            js_alert_popup("Reversal amount exceeds your actual adjustments.");
                        } else {
                            error.removeClass('js-error-class erroricddisplay');
                            $("td .js-withheld").removeClass('js-error-class erroricddisplay');
                        }
                    }
                });
            }
        } else{
            error.removeClass('js-error-class erroricddisplay');
        }
        if($('.js_other_adj.js-error-class.erroricddisplay').length==0){
            $(".js_other_adjust").find('input[value="Save"]').prop('disabled',false);
            $(".js-save").prop('disabled',false);
        } else {
            $(".js_other_adjust").find('input[value="Save"]').prop('disabled',true);
            $(".js-save").prop('disabled',true);
            $("td .js-withheld").addClass('js-error-class erroricddisplay');
            js_alert_popup("Reversal amount exceeds your actual adjustments.");
        }
    }
});

$(document).keyup(function (e) {
    if (e.keyCode === 27) {
        $('.js_other_adjust').hide();
    }
});

// Get last used of ICD for patient
// Author: baskar
$(document).mapKey('Alt+Ctrl+i', function (e) {
    if (!$("body").hasClass("modal-open")) {
        var patient_id = $("input[name='patient_id']").val();
        if(($('form').attr('name')=='chargeform') || ($('form').attr('name')=='all_claim_assign_form') && patient_id != "")
        $.ajax({
            url:api_site_url+'/api/lastIcd',
            method:"post",
            data:{patient_id:patient_id},
            success:function(result){
                $.each(result,function(index,value){
                    id=index+1;
                    $("#icd"+id).val(value.icd_code);
                });
            }
        });
    }
});
// Undo Get last used of ICD for patient
// Author: baskar
$(document).mapKey('Alt+Ctrl+z', function (e) {
    if (!$("body").hasClass("modal-open")) {
        var patient_id = $("input[name='patient_id']").val();
        if(($('form').attr('name')=='chargeform') || ($('form').attr('name')=='all_claim_assign_form') && patient_id != "")
            $('input.js-icd').val('');
    }
});

//hold reason in payment posting disable the hold block only for unhold patients
if($('#js_hold_reason').val() != 'patient')
	$('.js_hold_block').prop("disabled", true);

$(document).on('change', '#js_hold_reason', function () {
    var payment_hold = $(this).val();
    if (payment_hold == 'patient') {
        $('.js_hold_block').prop("disabled", false);
    } else {
        $('#hold_reason').select2("val", "").prop("disabled", true);
        $('#hold_release_date').val("").prop("disabled", true);
    }
});

// Patient payment posting hold reason, hold release date show / hide block start 
$(document).on('change', '#hold-statement', function () {
    var stmthold = $(this).is(":checked");    
    if (stmthold) {
        $('.js_pmt_hold_block').prop("disabled", false);
    } else {
        $('#hold_reason').select2("val", "").prop("disabled", true);
        $('#hold_release_date').val("").prop("disabled", true);
    }    
});
// Patient payment posting hold reason, hold release date show / hide block end 

// Select all check box unchecked If single claim unchecked for selected all in payment popup (18/02/19)
$(document).on('click, change','.js_submenu_payment',function(){
    var checked_length = $('input:checkbox').hasClass('js-sel-claim')?$('input:checkbox.js-sel-claim:checked').length:$('input:checkbox.js-sel-pay:checked').length;
    var total_length = $('input:checkbox').hasClass('js-sel-claim')?$('input:checkbox.js-sel-claim').length:$('input:checkbox.js-sel-pay').length;
    if(checked_length==total_length){
        $("#pat-checkall").prop('checked',true);
    } else {
        $("#pat-checkall").prop('checked',false);
    }
});

//Prevent BODY from scrolling when a 2nd modal is opened in Payments and Patient payments - issue fixed
$(document).on('click', 'a[data-target=#choose_claims]', function (e) {
    $("#choose_claims").on("show.bs.modal", function () {
        $('body').css("overflow", "hidden");
    }).on("hidden.bs.modal", function () {
        if ($('.modal:visible').length > 0) {
            //if 2nd model popup opened dont remove the overflow hidden.
        }else{
            $('body').css("overflow", "scroll");
        }
    });
});

$(document).on('click','.js_archive_era',function(){
	var getCheckedCount = $('input.js-era-post:checkbox:checked').length;
	var type = $(this).attr('data-type');
	if(getCheckedCount == 0){
		js_alert_popup('Select a minimum of one Check / ERA');
	}else{
		var selected_era_ids = [];
		$('input.js-era-post:checkbox:checked').each(function(){ 
			selected_era_ids.push($(this).attr('id'));
		})
		
		$.ajax({
			datatype: 'JSON',
			url: api_site_url + '/payments/updateArchiveStatus',
			data: {'erasId':selected_era_ids,'type':type},
			success: function (result_values) {
				js_sidebar_notification('success',type+' Succesfully');
				$('.js-search-filter').click();
			}
		})
	}
});

$(document).on('change','input[name="archive_list"]',function(){
	if($(this). prop("checked") == true){
		$('.js_archive_era').html('<i class="fa fa-pie-chart"></i> Unarchive');
		$('.js_archive_era').attr("data-type","Unarchive");
	}else{
		$('.js_archive_era').html('<i class="fa fa-pie-chart"></i> Archive');
		$('.js_archive_era').attr("data-type","Archive");
	}
});


function getcpticdmappingICDAutopopulate(id_val) {
    final_val = [];
    final_icd_val = [];
    $('.js-disable-div-' + id_val + ' .icd_pointer').each(function () {
        var map_val = $(this).val();  
        var icd_val = '';
        if (map_val != '') {
            final_val.push(map_val);
            icd_val = $('#icd' + map_val).val();
            final_icd_val.push(icd_val);
        }
        $("input[name='cpt_icd_map_key[" + id_val + "]']").val(final_val);
        $("input[name='cpt_icd_map[" + id_val + "]']").val(final_icd_val);
    });
}