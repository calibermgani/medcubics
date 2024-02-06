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
var current_checked_provider_id = '';  

$(document).on('ifChecked change','.js-default_view_option',function() {
    var default_view = $(this).val();
    $.ajax({
        url: api_site_url+'/scheduler/setdefaultandresourcelist/'+default_view,   
        type: 'get',
        success: function( result ){  
            $('.js-default-view-div').html(result);
           /* $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            }); */

            var resourcelisting_height = $('#js-resource-listing').height();
            if(resourcelisting_height > 158){
                $('#js-resource-listing').slimScroll({
                    height: '158px'
                });
            }
            var resourcelisting1_height = $('#js-resource-listing1').height();
            if(resourcelisting1_height > 158){
                $('#js-resource-listing1').slimScroll({
                    height: '158px'
                });
            }
                        
            triggerCalendar('yes');
            appointmentStatsdynamic('def');
        }
    });
});

$(document).on('ifToggled change','.js-resource_id',function() {
    current_checked_provider_id = $(this).val();
	var default_view_value = $("input[name='default_view']:checked").val();

    if ($('input[name=resource]:checked').length == 0) { 
        //MR-2832 - Schedule:deselect all providers alert information shows wrong - Anjukaselvan - 16/09/2019
        if(default_view_value == 'Facility'){
            js_alert_popup('Select atleast one provider.');
        }else{
            js_alert_popup('Select atleast one facility.');
        }
        setTimeout(function(){
            $('input[name="resource"]:checkbox[value='+current_checked_provider_id+']').prop("checked", true);
            //$('input[name="resource"]:checkbox[value='+current_checked_provider_id+']').iCheck('update');
        }, 1); 
        return false;
    } else { 
        triggerCalendar('yes');
        appointmentStatsdynamic('def');
    } 
    //$('input').iCheck('update');
});

$(document).on('ifChecked change','.js-sch_cal_default_view_list',function() {
    //$('input[name="default_view_list"]').iCheck('update');
    default_view_list_id = $('input[name=default_view_list]:checked').val();
    default_view = $('input[name=default_view]:checked').val();
    $.ajax({
        url: api_site_url+'/scheduler/getresourceslisting/list/'+default_view+'/'+default_view_list_id,   
        type: 'get',
        success: function( result ){
            $('#js-scheduler_resource_stats').html(result);
            /*$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });*/ 
            triggerCalendar('yes');
            appointmentStatsdynamic('def');
        },
        error: function() {
           js_alert_popup('Sorry no data available');
        }
    });   
});

function triggerCalendar(chk_exists_view){
    
    if(chk_exists_view=='yes'){
        var exists_view = $('#calendar').fullCalendar('getView');
        var exists_view_val = exists_view.name;
    } else {
        var exists_view_val = 'agendaDay';
    }
    
    var selected_resource_ids = ['0']; //add def dummy id for ajax url err
    $('input[name=resource]:checked').each(function() {
        selected_resource_ids.push($(this).attr('value'));
    });
    default_view_list_id = $('input[name=default_view_list]:checked').val();
    default_view = $('input[name=default_view]:checked').val();
    $('.fc-draggable').addTouch();
    $('#calendar').fullCalendar('destroy');
    $('#calendar').fullCalendar({ 
        defaultView: exists_view_val,
        defaultDate: current_date,
        groupByDateAndResource: true,
        allDaySlot: false,
        selectable: true,
        
        slotDuration: '00:05:00',
        //slotLabelInterval: '00:15:00', 
        slotLabelFormat:'hh:mm a',
        scrollTime: '06:00:00',
        
        editable: true,
		timezone: 'America/New_York',
        eventLimit: true, // allow "more" link when too many events        
        eventDurationEditable: false,
        
        //resourceAreaWidth: '20%',
        //aspectRatio: 1.5,
        //scrollTime: '00:00',
        header: {
			left: 'prev,next today',
			center: 'title',
			right: 'agendaDay,agenda7Day,month'
        },
        buttonText: {
			today: 'Today',
			month: 'Month',
			week: 'Week',
			day: 'Day'                       
		},
        views: {          
            agenda7Day: {
                type: 'agenda',
                duration: { days: 7 },               
                columnFormat: 'ddd M/D'
            },
            month: {
                type: 'agenda',
                groupByDateAndResource: false
            }                                          
        },
        resources: api_site_url+'/scheduler/getresourceslistingcalendar/json/'+default_view+'/'+default_view_list_id+'/'+selected_resource_ids,
        events: api_site_url+'/scheduler/getresourceevents/'+default_view_list_id+'/'+selected_resource_ids,                                         
        dayClick: function(date, jsEvent, view, resource) { 
            resource_id = resource ? resource.id : '';
            current_date_time = current_date_time.replace(' ','-');
            current_date_time = current_date_time.replace(':','-');
            current_date_split = current_date_time.split('-');

            var select_click_date = date.format('YYYY,MM,DD,HH,mm');
            var to_date_aplit = select_click_date.split(',');
            /*
             * JIRA issue is MED-2345
             * Month calender->select today not open this so, i use under format 
             * var from_date = new Date(current_date_split[0],current_date_split[1]-1,current_date_split[2],current_date_split[3],current_date_split[4]);          
            var to_date = new Date(to_date_aplit[0],to_date_aplit[1]-1,to_date_aplit[2],to_date_aplit[3],to_date_aplit[4]);
             */
            var from_date = new Date(current_date_split[0],current_date_split[1]-1,current_date_split[2]);          
            var to_date = new Date(to_date_aplit[0],to_date_aplit[1]-1,to_date_aplit[2]);
            /*
                Past date  Appointment Time schedule enable in the Calender
            */
            //if(from_date <= to_date){
                selected_time = date.format();    
                CreateNewAppointment(default_view_list_id, resource_id, selected_time, default_view);
           // } else {
                //selected_time = '';    
                //CreateNewAppointment(default_view_list_id, resource_id, selected_time, default_view);
            //    js_alert_popup(unable_to_appt_past_err_msg);
           // }
        },
        eventClick: function(calEvent, jsEvent, view) {
            UpdateAppointment(calEvent.id);
        },
        eventAfterRender: function( event, element, view ) { 
            // Add touch dragging to event element 
            $(".fc-draggable").addTouch();
        },
       /* eventAfterRender: function(calEvent, jsEvent, view) {
            //UpdateAppointment(calEvent.id);
            jsEvent.height(240);
            $(jsEvent).find('.fc-title').prepend('<span class="glyphicon"></span> '); 
        },
        eventMouseover: function(event, jsEvent, view) {
            console.log('MOUSEOVER ' + event.title);
            //console.log(jsEvent);
            //console.log(view);
            //console.log(this);
        },*/
        eventDrop: function(event, delta, revertFunc) {
         /* Issue MED-1640
         * Error Desc: Browser Popup displayed, need to show Medcubics Popup
         * Fix: Browser popuop disabled and custom popup shown
         * Date: June 1, 2017, Nallasivam
         */
            confirm_msg = "Would you like to reschedule this appointment?";
            $("#js_schedular_popup_model .med-green").html(confirm_msg);
            $("#js_schedular_popup_model")
            .modal({show: 'false', keyboard: false})
            .one('click', '.js_schedular_confirm', function (e) {
                default_view_list_id = $('input[name=default_view_list]:checked').val();
                var conformation = $(this).attr('id');
                
                if(conformation == "true"){
                    $.ajax({
                        type: "get",
                        url: api_site_url+'/scheduler/rescheduleappintmentdrag?id='+event.id+'&default_view_list_id='+default_view_list_id+'&resource_id='+event.resourceId+
                                '&start_date='+event.start.format('YYYY-MM-DD hh:mm a')+'&end_date='+event.end.format('YYYY-MM-DD hh:mm a'),
                        success: function(result) { 
                            if(result['status'] == 'success'){
                             triggerCalendar();
                                //scheduler/getresourceevents/1/0,3?start=2017-06-05&end=2017-06-06&_=1496665167774
                                js_sidebar_notification("success",result['message']);                               
                            } else { 
                                js_sidebar_notification("error",result['message']);
                                revertFunc();
                            } 
                        }
                    }); 
                } else {
                    revertFunc();
                }
            });
            
            $(document).on('click','.close_popup',function(){
                revertFunc();
            });                        
        }
    }); 
}

function formatAMPM(date,value) {
    var hours = date.getHours();
    var minutes = date.getMinutes();  
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    if(value == 1) {
        var get_time = date.getMinutes() + 2;
        get_time = get_time < 10 ? '0'+get_time : get_time;
        var strTime = hours + ':' + get_time + ' ' + ampm;
    } else {
      var strTime = hours + ':' + minutes + ' ' + ampm;
    }
    return strTime;
}

function CreateNewAppointment(default_view_list_id, resource_id, selected_time, default_view){
    
    var current_url = window.location.search.substring(1);
//    console.log(current_url.substring(1));
    var target = api_site_url+'/scheduler/getappointment?default_view_list_id='+default_view_list_id+'&resource_id='+resource_id+'&user_selected_date='+selected_time+'&default_view='+default_view+'&'+current_url;
    if(selected_time!='')
        $('#check_selected_time').val(selected_time);
    else
        $('#check_selected_time').val('');
  
    $("#fullCalendarModal").load(target, function(){        
        $('#fullCalendarModal').on('show.bs.modal', function(e){

            $.AdminLTE.boxWidget.activate();  
            default_view = $('#default_view').val();
            default_view_list_caption = $('#default_view_list_caption').val();
            resource_caption = $('#resource_caption').val();

            $("select.select2.form-control").select2();
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });         
          
            $(document).delegate('#copay_date','focus', function(){
                $('#copay_date').datepicker({
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    yearRange:'-100:+10',
                    onSelect: function(date, instance) {
                        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('copay_date');
                    }
                });
            });         
            
            // Add new reason visit
            if($("div").hasClass( "js-add-new-reason" ) && $( "div.js-add-new-reason" ).find('select optgroup[label=Others]').text() != 'Add New'){ 
                $( "div.js-add-new-reason" ).find('select').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
            }
            calltimepicker();
            
            /*var time = new Date($.now());
            var get_currenttimefirst = formatAMPM(time,0);
            var get_currenttimesecond = formatAMPM(time,1);
            
            setTimeout(function(){
                $('#check_in_time').val(get_currenttimefirst);
                $('#check_out_time').val(get_currenttimesecond);
            }, 500);*/
            
            $("#scheduled_on").datepicker({
                dateFormat: "mm/dd/yy",
                setDate: $('#user_selected_date').val(),
                beforeShowDay: function(d) {  
                    provider_available_dates = $('#provider_available_dates').val(); 
                    provider_available_dates = provider_available_dates.split(',');
                    var dmy = d.getFullYear()+"-";                    
                    if(d.getMonth()<9) 
                        dmy+="0";
                    dmy+=(d.getMonth()+1)+"-";
                    
                    if(d.getDate()<10) 
                        dmy+="0"; 
                    dmy+=d.getDate();

                    if ($.inArray(dmy, provider_available_dates) != -1) {
                        return [true, "","Available"]; 
                    } else {
                         return [false,"","Unavailable"]; 
                    }
                },
                onSelect: function(date, instance) {                                   
                    default_view_list_id = $('#js-ptsh_default_view_list').val();
                    resource_id = $('#js-ptsh_resource').val();
                    date = $('#scheduled_on').val();
                    $.ajax({
                        type: "get",
                        datatype:'json',
                        url: api_site_url+'/scheduler/getavailabletimeslot?default_view_list_id='+default_view_list_id+'&resource_id='+resource_id+'&user_selected_date='+date+'&default_view='+default_view,
                        success: function(result) {  
                           setTimeout(function(){
                                if (result['data']['time_slot_arr'] != '') {
                                    $('#appointment_time').html(result['data']['array_of_time']);
                                    $('#appointment_time').select2('val','');
                                    // Revalidate Fields
                                    revalidateAppointmentFields();
                                    $('.snackbar-div').removeClass('show');
                                }else{
                                    js_sidebar_notification("error","Appointment time is not available");
                                    return false;
                                }
                            }, 1);
                        }
                    }); 
                }
            }); 

            $(document).on('change','#copay_option',function() { 
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                fv.enableFieldValidators('money_order_no', false).revalidateField('money_order_no');                    
                    if($(this).val()=='Cheque'){
                        $('.js_copay_check_part').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                        $('.js-hide-money').addClass('hide').removeClass('show');
                        fv.enableFieldValidators('copay_check_number', true).revalidateField('copay_check_number');
                    }
                    if($(this).val()=='Check'){
                        $('.js_copay_check_part').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                         $('.js-hide-money').addClass('hide').removeClass('show');
                        fv.enableFieldValidators('copay_check_number', true).revalidateField('copay_check_number');
                    } else if($(this).val()=='CC'){
                        $('.js_copay_check_part').removeClass('show').addClass('hide');
                        $('.js_copay_card_part').removeClass('hide').addClass('show');
                         $('.js-hide-money').addClass('hide').removeClass('show');
                    } else if($(this).val()=='Money Order'){ 
                        $('.js-hide-money').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                       // $('.js-label-change').text("MO date");
                        fv.enableFieldValidators('money_order_no', true).revalidateField('money_order_no');
                    } else {
                         $('.js-hide-money').addClass('hide').removeClass('show');
                        $('.js_copay_check_part').removeClass('show').addClass('hide');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                        
                        fv.enableFieldValidators('copay_check_number', false).revalidateField('copay_check_number');
                    }
                    
                    $('.js_copay_date_part').removeClass('hide').addClass('show');
                    fv.enableFieldValidators('copay', true).revalidateField('copay');
                    fv.enableFieldValidators('copay_date', true).revalidateField('copay_date');                 
                } else {
                    $('.js_copay_check_part').removeClass('show').addClass('hide');
                    $('.js_copay_card_part').removeClass('show').addClass('hide');
                    $('.js_copay_date_part').removeClass('show').addClass('hide');
                    fv.enableFieldValidators('money_order_no', false).revalidateField('money_order_no');
                    fv.enableFieldValidators('copay', false).revalidateField('copay');
                    fv.enableFieldValidators('copay_check_number', false).revalidateField('copay_check_number');
                    fv.enableFieldValidators('copay_date', false).revalidateField('copay_date');
                }
            });
            
            $(document).on('keyup','#copay_amount',function() {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                     fv.enableFieldValidators('copay_option', true).revalidateField('copay_option');
                } else {
                     fv.enableFieldValidators('copay_option', false).revalidateField('copay_option');
                }
            });
            
            $('#js-bootstrap-validator').bootstrapValidator({
                message: 'This value is not valid',
                excluded: ':disabled',
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    default_view_list_id: {
                        validators: {
                            notEmpty: {
                                message: 'Select '+default_view_list_caption
                            }
                        }
                    },
                    resource_id: {
                        validators: {
                            notEmpty: {
                                message: 'Select '+resource_caption
                            }
                        }
                    },
                    scheduled_on: {
                        validators: {
                             callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    check_selected_time = $('#check_selected_time').val();
                                    if(value=='' && check_selected_time==''){
                                        return {
                                                valid: false,
                                                message: select_appt_date_err_msg
                                            };
                                    } else if(value=='' && check_selected_time!=''){
                                        return {
                                                valid: false,
                                                message: appt_date_not_availe_err_msg
                                            };
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    },
                    appointment_time: {
                        validators: {
                             callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    get_scheduled = $('#scheduled_on').val();
                                    seleted_avail_hidden_val = $('input[type=hidden][name="seleted_avail_hidden"]').val();
                                    if((value=='' && get_scheduled=='') || seleted_avail_hidden_val=="no"){
                                        return {
                                                valid: false,
                                                message: slot_time_not_available_err_msg
                                            };
                                    } else if(value=='' && get_scheduled!=''){
                                        return {
                                                valid: false,
                                                message: select_appt_slot_time_err_msg
                                            };
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    },
                    reason_for_visit: {
                        validators: {
                            callback: {
                                message:'',
                                callback: function (value, validator) {
                                    if(value == 0 || value == '' ) {
                                        return {
                                            valid: false, 
                                            message: select_reason_visit_err_msg
                                        };
                                    }
                                    return true;
                                }
                            }
                        }
                    },              
                    copay_option: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: select_copay_option_err_msg
                            }
                        }
                    },              
                    copay: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: enter_copay_amt_err_msg
                            },
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    if(value <= 0 && value != ''){
                                        return {
                                            valid: false,
                                            message: greater_than_zero_err_msg
                                        };
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    }, 
                    patient_search: {
                        validators: {
                            callback: {
                                message: select_patient_err_msg,
                                callback: function(value, validator, $field) {
									if( $("#copay_option").val() !="undefined") {
                                    // $('#js-bootstrap-validator').data('bootstrapValidator').enableFieldValidators('copay_check_number', true)
                                      //  .revalidateField('copay_check_number');
									}       
                                    return ((!$('#is_new_patient').is(':checked')) && value == '') ? false : true;
                                }
                            }
                        }
                    },
                    copay_date: {
                        enabled: false,
                        validators: {
                            date: {
                                format: 'MM/DD/YYYY',
                                message: 'Enter valid date format'
                            },
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var current_date=new Date(value);
                                    var d=new Date();   
                                    if(value==''){
                                        return {
                                            valid: false,
                                            message:'Enter date',
                                        };
                                    }
                                    else if(value != '' && d.getTime() < current_date.getTime()){
                                        return {
                                            valid: false,
                                            message:'Should not be the future date',
                                        };
                                    }
                                    else{
                                        return true;
                                    }
                                }
                            }                            
                        }
                    }, 
                    copay_check_number: {
                        enabled: false,
                        validators: {
                            /*  remote: {
                                url: api_site_url+'/scheduler/check_no',
                                data: function(validator) {
                                    return {
                                        copay_check_number: validator.getFieldElements('copay_check_number').val(),
                                        _token: validator.getFieldElements('_token').val()
                                    }
                
                                },                              
                            },  */
                            callback: {
                                message: empty_check_no,
                                callback: function (value, validator, $field) {                                         
                                    lengthval = $('#check_no_minlength').val();
                                    /* function check_no(){                                 
                                        var check = '';
                                            $.get(api_site_url+'/scheduler/check_no/'+value, function(data){
                                            alert('Ajax'+data);
                                                
                                                data = data.status;
                                                 console.log("data"+data);
                                                if(data == "success") {
                                                    check = 'success';
                                                    
                                                } else{                                         
                                                    check = 'error';                                                
                                                } 
                                            });
                                        alert('check'+check);       
                                        return check;
                                    } */ 
                                    patientId = $('input[name="patient_id"]').val();
                                    //console.log("patient id");
                                    //console.log(patientId);
                                    if(value !='' && (value.length)>2 && patientId != "" && typeof patientId != "undefined"){
                                        // $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', true);
                                        $.ajax({
                                               url: api_site_url+'/scheduler/check_no/'+value+"/"+patientId,
                                               type : 'GET', 
                                               success: function(msg){ 
                                               //console.log(msg.status);
                                                    if(msg.status=='success'){
                                                        $('#check_no').html('');        
                                                        $('.js-submit-btn').removeAttr('disabled');
                                                    }else if(msg.status == 'error'){
                                                        $('.js-submit-btn').attr('disabled','disabled');
                                                        $('#check_no').html('<small class="help-block med-red" data-bv-validator="notEmpty" data-bv-for="copay" data-bv-result="INVALID" style="">The copay check number has already been taken</small>');
                                                    }
                                                }
                                            }); 
                                    } else {
                                        $('#check_no').html('');
                                    }
                                     
                                    /* payment_check_no = $('#payment_check_no').val();
                                    payment_check_no= payment_check_no.split(",");
                                    $.each( payment_check_no, function( value ) {
                                      //alert(  ": " + value );
                                    });alert(payment_check_no)
                                     */
                                     if(value == ''){
                                        return{
                                            valid:false,
                                            message:empty_check_no
                                        }
                                     } else if(value != '' && !checknumbervalidation(value)){
                                        return{
                                            valid:false,
                                            message:alphanumeric_lang_err_msg
                                        }
                                     }else if(value != '' && value.length < lengthval){
                                         return {
                                            valid:false,
                                            message:checkminlength,
                                        }
                                     } else {                                       
                                        /* setTimeout(function(){ 
                                            $('.js_copay_check_part').removeClass('has-error');
                                        }, 5);  */
                                        return true;
                                     }
                                },                                    
                            } 
                        }
                    },
                    check_in_time: {
                        validators: {                                
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var endTime = validator.getFieldElements('check_out_time').val();
                                    
                                    var timeStart = new Date("01/01/2007 " + value);
                                    var timeEnd = new Date("01/01/2007 " + endTime);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                                    
                                    if(value == '' && endTime!='') {
                                        return {
                                            valid: false,
                                            message: enter_checkin_time_err_msg
                                        };
                                    }
                                    else if(diff<=0 && endTime!=''){
                                        return {
                                            valid: false,
                                            message: check_in_out_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }
                        }
                    },
                    money_order_no:{
                        enabled: false,
                        trigger: 'change keyup',
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
                                type: 'POST',
                                validKey:'valid',
                            },
                            callback: {
								message: "",
								callback: function (value, validator, $field) {
									chkd = $('select[name=copay_option]').val(); 
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
                    check_out_time: {
                        validators: {                            
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) { 
                                    var startTime = validator.getFieldElements('check_in_time').val();
                                    
                                    var timeStart = new Date("01/01/2007 " + startTime);
                                    var timeEnd = new Date("01/01/2007 " + value);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                                    
                                    /*if(value == '' && startTime!='') {
                                        return {
                                                    valid: false,
                                                    message: 'Enter check out time'
                                                };
                                    }*/
                                    if(diff<=0 && startTime!='' && value != ''){
                                        return {
                                            valid: false,
                                            message: check_out_in_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;   
                                }
                            }
                        }
                    }
               }
            })
            .on('ifToggled', '[name="is_new_patient"]', function(e) {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).is(':checked')){
                    fv.enableFieldValidators('patient_search', false);
                } else {
                    fv.enableFieldValidators('patient_search', true).revalidateField('patient_search');
                }
            })       
            .on('success.form.bv', function(e) {
                // Prevent form submission
                e.preventDefault();

                /*var disabled_form_ids = [];
                $('#js-bootstrap-validator .js-display-days-cls :input:disabled').each(function() {
                    disabled_form_ids.push($(this).attr('id'));
                });*/
                
                var myform = $('#js-bootstrap-validator');
                var disabled = myform.find(':input:disabled').removeAttr('disabled');
                var serialized = myform.serialize();
                $('.help-block').addClass('hide');
                $.ajax({
                    type : 'GET',
                    dataType: 'json',
                    url  : api_site_url+'/scheduler/storeappointment',
                    data : serialized,
                    success :  function(result){
                        if(result['status'] == 'success'){
                            js_sidebar_notification('success','Appointment added successfully');
                            $('#fullCalendarModal').modal("hide");
                            
                            var view = $('#calendar').fullCalendar('getView');
                            settempCookie('last_view_for', view.name);
                            if(view.name=='month'){
                                var start_date_str  = view.intervalStart;
                            } else {
                                var start_date_str  = view.start;
                            }
                            var stt = new Date(start_date_str);
                            var last_view_date  = stt.getFullYear()+'-'+(stt.getMonth()+1)+'-'+stt.getDate();
                            settempCookie('last_view_date', last_view_date);
                            
                            window.location.reload(true);
                            return false;
                        } else {
                           /* var length_of_disabled_div = disabled_form_ids.length;
                            for(i=0;i<length_of_disabled_div;i++){
                                div_id_name = disabled_form_ids[i];
                                $('#provider_scheduler_modal #'+div_id_name).attr("disabled",true);
                            };
                            
                            error_type = result['data']['error_array']['error_type'];
                            error_type_value = result['data']['error_array']['error_type_value'];
                            
                            $('#js-error-msg').html(result['message']);
                            $('#js-error-msg').removeClass('hide');
                            
                            if(error_type == 'days_timings'){
                                split_days = error_type_value.split(', ');
                                split_days_count = split_days.length;
                                for(i=0; i< split_days_count; i++){
                                    day_name = split_days[i].toLowerCase(); 
                                    $('#js-error-'+day_name).html('Select time');
                                    $('#js-error-'+day_name).removeClass('hide');
                                }
                            } else if(error_type == 'mismatch_time_selection'){
                                $.each( error_type_value, function( key, value ){
                                    if(value > 0){
                                        $('#js-error-'+key).html('Select From and To time');
                                        $('#js-error-'+key).removeClass('hide');
                                    }
                                });
                            } else {
                                $('#js-error-'+error_type).html(error_type_value);
                                $('#js-error-'+error_type).removeClass('hide');
                            } */
                            return false;
                        }
                    }
                });
                return false;
            });          
        }); 
       /* $('#fullCalendarModal').on('hidden.bs.modal', function(){    
            $('.modal-body').html('');
        });  */  
       // $('.modal-title').html('New Appointment');
        $("#fullCalendarModal").modal("show");
        var seleted_avail_hidden = $('input[type=hidden][name="seleted_avail_hidden"]').val();
        if(($('#user_selected_date').val()=="" && selected_time!="") || seleted_avail_hidden=="no" ){
            revalidateAppointmentFields();
        }
        return false;
    });
}

$(document).on('keyup keypress', '.js-check-number', function () {
    //console.log("keyup keypress");
    var check_val = $(this).val();
    check_val = $.trim(check_val);
    $(this).val(check_val.toUpperCase());
    form_id = $(this).parents("form").attr("id");
    $('#' + form_id).bootstrapValidator('disableSubmitButtons', true);
}); 

function UpdateAppointment(event_id){
    var view = $('#calendar').fullCalendar('getView');
    if(view.name=='month'){
        $.ajax({
            type: "get",
            url: api_site_url+'/scheduler/geteventschedulardate/'+event_id,
            success: function(result) { 
                $('#calendar').fullCalendar('changeView','agendaDay');
                $('#calendar').fullCalendar('gotoDate',result['scheduled_on_date']);
                var view_d = $('#calendar').fullCalendar('getView');
                var start_date_str  = view_d.start;
                var s = new Date(start_date_str);
                var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
                appointmentStatsdynamic(start_date+'::'+start_date);
            }
        });
        return false;
    }
      
    var target = api_site_url+'/scheduler/getappointmentbyevent?event_id='+event_id;
    $("#fullCalendarModal").load(target, function(){ 
        
        $('#fullCalendarModal').on('show.bs.modal', function(e){  
            $.AdminLTE.boxWidget.activate();  
            $('#fullCalendarModal').attr('data-eventid',event_id);
            default_view = $('#default_view').val();
            default_view_list_caption = $('#default_view_list_caption').val();
            resource_caption = $('#resource_caption').val();       
            
            $("select.select2.form-control").select2();
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });         
            $("#dob").datepicker({ 
                changeMonth: true,
                changeYear: true,
                maxDate: '0',
                dateFormat: 'mm/dd/yy',
                yearRange: '1901:+0',
                onSelect: function(date, instance) {
                    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('dob');
                }
            });

            $("#scheduled_on").datepicker({
                dateFormat: "mm/dd/yy",
                setDate: $('#user_selected_date').val(),
                beforeShowDay: function(d) {  
                    provider_available_dates = $('#provider_available_dates').val(); 
                    provider_available_dates = provider_available_dates.split(',');                    
                    var dmy = d.getFullYear()+"-";                    
                    if(d.getMonth()<9) 
                        dmy+="0";
                    dmy+=(d.getMonth()+1)+"-";
                    
                    if(d.getDate()<10) 
                        dmy+="0"; 
                    dmy+=d.getDate();

                    if ($.inArray(dmy, provider_available_dates) != -1) {
                        return [true, "","Available"]; 
                    } else {
                        return [false,"","Unavailable"]; 
                    }
                },
                onSelect: function(date, instance) {                    
                    default_view_list_id = $('#js-ptsh_default_view_list').val();                    
                    resource_id = $('#js-ptsh_resource').val();
                    scheduled_date = $('#scheduled_on').val(); 
                    
                    // Revalidate Fields
                    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('scheduled_on');
                    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('appointment_time');
                    //revalidateAppointmentFields();
                    $.ajax({
                        type: "get",
                        datatype: "json",
                        url: api_site_url+'/scheduler/getavailabletimeslot?default_view_list_id='+default_view_list_id+'&resource_id='+resource_id+'&user_selected_date='+scheduled_date+'&default_view='+default_view,
                        success: function(result) { 
                            setTimeout(function(){
                                $('#appointment_time').html(result['data']['array_of_time']);
                                $('#appointment_time').select2('val','');
                            }, 1);
                        }
                    });  
                }
            });

           /* $("[data-mask]").inputmask(); */   
			$(document).on('change','#copay_option',function() {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                    fv.enableFieldValidators('copay', true).revalidateField('copay');
                } else {
                    fv.enableFieldValidators('copay', false).revalidateField('copay');
                }
            });
            
            $(document).on('keyup','#copay_amount',function() {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                    fv.enableFieldValidators('copay_option', true).revalidateField('copay_option');
                } else {
                    fv.enableFieldValidators('copay_option', false).revalidateField('copay_option');
                }
            });       
                   
            $('#js-bootstrap-validator').bootstrapValidator({
                message: 'This value is not valid',
                excluded: ':disabled',
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    default_view_list_id: {
                        validators: {
                            notEmpty: {
                                message: 'Select '+default_view_list_caption
                            }
                        }
                    },
                    resource_id: {
                        validators: {
                            notEmpty: {
                                message: 'Select '+resource_caption
                            }
                        }
                    },
                    scheduled_on: {
                        validators: {
                            notEmpty: {
                                message: select_appt_date_err_msg
                            }
                        }
                    },
                    appointment_time: {
                        validators: {
                            notEmpty: {
                                message: select_appt_slot_time_err_msg
                            }
                        }
                    },
                    reason_for_visit: {
                        validators: {
                            callback: {
                                message:'',
                                callback: function (value, validator) {
                                    if(value == 0 || value == '' ) {
                                        return {
                                            valid: false, 
                                            message: select_reason_visit_err_msg
                                        };
                                    }
                                    return true;
                                }
                            }
                        }
                    },
                    patient_search: {
                        validators: {
                            callback: {
                                message: select_patient_err_msg,
                                callback: function(value, validator, $field) {
                                    return ((!$('#is_new_patient').is(':checked')) && value == '') ? false : true;
                                }
                            }
                        }
                    },
                    last_name: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_last_name_err_msg
                            }                            
                        }
                    },
                    first_name: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_first_name_err_msg
                            }                            
                        }
                    },
                    address1: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_address1_err_msg
                            }                            
                        }
                    },
                    city: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_city_err_msg
                            }                            
                        }
                    },
                    state: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_state_err_msg
                            }                            
                        }
                    },
                    zip5: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: enter_zipcode_err_msg
                            },
                            regexp: {
                                regexp: /^\d{5}$/,
                                message: zip5_limit_lang_err_msg
                            }                         
                        }
                    },
                    zip4: {
                        enabled: false,
                        validators: {
                            regexp: {
                                regexp: /^\d{4}$/,
                                message: zip4_limit_lang_err_msg
                            }                            
                        }
                    },
                    dob: {
                        enabled: false,
                        validators: {                            
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var current_date=new Date(value);
                                    var d=new Date();   
                                    if(value != '' && d.getTime() < current_date.getTime()){
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
                    primary_insurance_id: {
                        enabled: false,
                        validators: {
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    is_self_pay = $('input[name=is_self_pay]:checked').val();
                                    
                                    if(value == '' && is_self_pay=='No') {
                                        return {
                                            valid: false,
                                            message: select_pri_ins_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }                            
                        }
                    },
                    mobile: {
                        enabled: false,
                        validators: {
                            callback: {
                                message:'',
                                callback: function (value, validator) {
                                    var mobile_phone_msg = 'Mobile phone must be 10 digits';
                                    var response = phoneValidation(value,mobile_phone_msg);
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
                    home_phone: {
                        enabled: false,
                        validators: {
                            callback: {
                                message:'',
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
                    primary_insurance_policy_id: {
                        enabled: false,
                        validators: {
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    is_self_pay = $('input[name=is_self_pay]:checked').val();
                                    
                                    if(value == '' && is_self_pay=='No') {
                                        return {
                                            valid: false,
                                            message: enter_policy_id_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }                            
                        }
                    },
                    copay_option: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: select_copay_option_err_msg
                            }
                        }
                    },              
                    copay: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: enter_copay_amt_err_msg
                            }                          
                        }
                    }, 
                    check_in_time: {
                         trigger: 'keyup change',
                        validators: {                                
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) { 
                                    var endTime = validator.getFieldElements('check_out_time').val();
                                    
                                    var timeStart = new Date("01/01/2007 " + value);
                                    var timeEnd = new Date("01/01/2007 " + endTime);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                                    
                                    if(value == '' && endTime!='') {
                                        return {
                                            valid: false,
                                            message: enter_checkin_time_err_msg
                                        };
                                    }
                                    else if(diff<=0 && endTime!=''){
                                        return {
                                            valid: false,
                                            message: check_in_out_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }
                        }
                    },
                    money_order_no:{
                        enabled: false,
                        trigger: 'change keyup',
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
                                type: 'POST',
                                validKey:'valid',
                            },
                            callback: {
                                message: "",
                                callback: function (value, validator, $field) {
                                    chkd = $('select[name=copay_option]').val(); 
                                    if (value == '' && chkd == "Money Order") { 
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
                    check_out_time: {
                        validators: {                            
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) { 
                                    var startTime = validator.getFieldElements('check_in_time').val();
                                    
                                    var timeStart = new Date("01/01/2007 " + startTime);
                                    var timeEnd = new Date("01/01/2007 " + value);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                                    
                                    /*if(value == '' && startTime!='') {
                                        return {
                                                    valid: false,
                                                    message: 'Enter check out time'
                                                };
                                    }*/
                                    if(diff<=0 && startTime!='' && value != ''){
                                        return {
                                            valid: false,
                                            message: check_out_in_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;   
                                }
                            }
                        }
                    },
                }
            })           
            .on('ifToggled', '[name="is_new_patient"]', function(e) {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).is(':checked')){
                    fv.enableFieldValidators('last_name', true);
                    fv.enableFieldValidators('first_name', true);
                    fv.enableFieldValidators('address1', true);
                    fv.enableFieldValidators('city', true);
                    fv.enableFieldValidators('state', true);
                    fv.enableFieldValidators('zip5', true);
                    fv.enableFieldValidators('zip4', true);
                    fv.enableFieldValidators('dob', true);
                    fv.enableFieldValidators('patient_search', false);
                    fv.enableFieldValidators('primary_insurance_id', true);
                    fv.enableFieldValidators('primary_insurance_policy_id', true);
                    fv.enableFieldValidators('mobile', true);
                    fv.enableFieldValidators('home_phone', true);
                } else {
                    fv.enableFieldValidators('last_name', false).revalidateField('last_name');
                    fv.enableFieldValidators('first_name', false).revalidateField('first_name');
                    fv.enableFieldValidators('address1', false).revalidateField('address1');
                    fv.enableFieldValidators('city', false).revalidateField('city');
                    fv.enableFieldValidators('state', false).revalidateField('state');
                    fv.enableFieldValidators('zip5', false).revalidateField('zip5');
                    fv.enableFieldValidators('zip4', false).revalidateField('zip4');
                    fv.enableFieldValidators('dob', false).revalidateField('dob');
                    fv.enableFieldValidators('patient_search', true).revalidateField('patient_search');
                    fv.enableFieldValidators('primary_insurance_id', false).revalidateField('primary_insurance_id');
                    fv.enableFieldValidators('primary_insurance_policy_id', false).revalidateField('primary_insurance_policy_id');
                    fv.enableFieldValidators('mobile', false).revalidateField('mobile');
                    fv.enableFieldValidators('home_phone', false).revalidateField('home_phone');
                }
            })      
            .on('success.form.bv', function(e) {
                // Prevent form submission
                e.preventDefault();
                
                var myform = $('#js-bootstrap-validator');
                var disabled = myform.find(':input:disabled').removeAttr('disabled');
                var serialized = myform.serialize();
                $('.help-block').addClass('hide');
                $.ajax({
                    type : 'GET',
                    dataType: 'json',
                    url  : api_site_url+'/scheduler/updateappointment',
                    data : serialized,
                    success :  function(result){
                        if(result['status'] == 'success'){
                            $('#fullCalendarModal .modal-body').html(update_lang_err_msg);
                            $('#fullCalendarModal').modal("hide");
                            
                            var view = $('#calendar').fullCalendar('getView');
                            settempCookie('last_view_for', view.name);
                            if(view.name=='month'){
                                var start_date_str  = view.intervalStart;
                            } else {
                                var start_date_str  = view.start;
                            }
                            var stt = new Date(start_date_str);
                            var last_view_date  = stt.getFullYear()+'-'+(stt.getMonth()+1)+'-'+stt.getDate();
                            settempCookie('last_view_date', last_view_date);
                            
                            window.location.reload(true);
                            return false;
                        } else {                           
                            return false;
                        }
                    }
                });
                return false;
            });          
        }); 
        $('#fullCalendarModal').on('hidden.bs.modal', function(){    
            $('#fullCalendarModal .modal-body').html('');
        });       
        $('#fullCalendarModal .modal-title').html('Edit Appointment');
        $("#fullCalendarModal").modal("show");
        return false;
    });
}

$(document).on('click','.js-new_appointment',function() {  
	if ($('.modal:visible').length < 1) {
		// If already provider not assigned popup opened then dont call new appointment popup
        if((typeof providerSchCnt != undefined && providerSchCnt < 1)){
            $('#popup_provider_scheduler_msg').modal('show');
        } else {
    		CreateNewAppointment('','','','');
        }
	}
});

/*** Start to display ins. eligiblity check icon ***/
$(document).on('change','.modal-timing-bg input.form-control,.select2',function() {
    var getnewpatient = $('#is_new_patient').is(':checked');
    if(getnewpatient == true){
        var patient_last_name = $('#last_name').val();
        var patient_first_name = $('#first_name').val();
        var primary_insurance_id = $('#primary_insurance_id').val();
        var primary_insurance_policy_id = $('#primary_insurance_policy_id').val();
        
        if(patient_last_name != '' && patient_first_name != ''  && primary_insurance_id != '' && primary_insurance_policy_id !=''){
            if($('.js_eliactive_temp').attr('data-tempid')=='' && $('.js_eliinactive_temp').attr('data-tempid')=='')
                $('.eligibility_gray_temp').css('display','block');
        } else {
            $('.eligibility_gray_temp').css('display','none');
        }
    }
});
/*** End to display ins. eligiblity check icon ***/

$(document).on('keyup keypress blur change','#patient_search',patientSearchAppointment);

$('#patient_search').bind('cut',patientSearchAppointment);
function patientSearchAppointment() {
    if($('#patient_search').val().trim().length >1) {    
        patient_search_func();
    } else {
        if($('#patient_search').hasClass('ui-autocomplete-input')) {
           $('#patient_search').autocomplete("destroy");           
        }        
        $("#patient_id").val("");
        $('#js-searched_patient').addClass('hide');
    }
}

/** Call autocomplete on paste text start **/
$(document).on('paste', '#patient_search', function(){
    patient_search_func();   
});
/** Call autocomplete on paste text end **/

$(document).on('change','#js-patient_search_category',function() {  
    var patient_search_category = $('select#js-patient_search_category').select2("val");
    //console.log(patient_search_category)
    if(patient_search_category=='dob'){
        $('#patient_search').val('');
        $('#patient_search').addClass('js_pat_search_datepicker');
    } else {
    //  $('#patient_search').val('');
        $(".js_pat_search_datepicker, #patient_search").datepicker("destroy");
        $('#patient_search').removeClass('js_pat_search_datepicker');
    }
    patient_search_func();
});
 
$(document).on('click','.js-available_time_selection',function() {  
    $('#appointment_time').val($(this).attr('data-value'));     
    $('.js-available_slot').removeClass('clsSelectedSlot');
    $('.js-available_slot').addClass('clsSelectSlot');   
    $(this).addClass('clsSelectedSlot');
    $('input[type=hidden][name="seleted_avail_hidden"]').val('yes');
    // Revalidate Fields
    revalidateAppointmentFields();
});
 
 // New patient popup form. 
 
$('.js_patient_dob ').on('keypress click change ifToggled', function () {           
	if ($(this).val() != "" && $('[name="ssn"]') != '' )
		$('#js-newpatient-validator').bootstrapValidator('revalidateField', $('[name="ssn"]'));
});

$('[name="ssn"]').on('ifToggled', function () {
	if ($(this).val() != "")
		$('#js-newpatient-validator').bootstrapValidator('revalidateField', $('[name="dob"]'));
});
 
 
$(document).on('ifToggled', "#is_new_patient",function () {
    newPatientEnable();
    $('#js_newpatient_scheduler').modal('show');
     
    $('#session_model').css('z-index','1500');
    var get_target      = api_site_url+'/scheduler/addnewpatient';
    $("#js_newpatient_scheduler .modal-body").load(get_target, function(){ 
        $("select.select2.form-control").select2();
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });         
        $("#dob").datepicker({ 
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            dateFormat: 'mm/dd/yy',
            yearRange: '1901:+0',
            onSelect: function(date, instance) {
                $('#js-newpatient-validator').data('bootstrapValidator').revalidateField('dob');
                $('#js-newpatient-validator').data('bootstrapValidator').revalidateField('ssn');
                //$('#js-newpatient-validator').bootstrapValidator('revalidateField', $('[name="ssn"]'));
            }
        });
                //Self pay is Yes
        var selfpay = $("input[name='is_self_pay']").val(); 
        if(selfpay == "Yes")  
        {
            $('.js-primary-ins-part').addClass('hide');
            $('.js-policy-id-part').addClass('hide');
        }
        // Fill already added values
        if($('#js_lastname').val() != ''){
            $('#last_name').val($('#js_lastname').val());
            $('#first_name').val($('#js_firstname').val());
            $('#middle_name').val($('#js_middlename').val());
            $('#address1').val($('#js_address1').val());
            $('#address2').val($('#js_address2').val());
            $('#city').val($('#js_city').val());
            $('#state').val($('#js_state').val());
            $('#zip5').val($('#js_zip5').val());
            $('#zip4').val($('#js_zip4').val());
            $('#dob').val($('#js_dob').val());
            $('#mobile').val($('#js_mobile').val());
            $('#ssn').val($('#js_ssn').val());
            $('#home_phone').val($('#js_home_phone').val());
            $('#primary_insurance_policy_id').val($('#js_primary_insurance_policy_id').val());
            
            if($('#js_gender').val()=='Male')
                $("input[value='Male']").prop('checked', true);
            else if($('#js_gender').val()=='Female')
                $("input[value='Female']").prop('checked', true);
            else if($('#js_gender').val()=='Others')
                $("input[value='Others']").prop('checked', true);
            
            if($('#js_selfpay').val()=='Yes')
                $("input[value='Yes']").prop('checked', true);
            else if($('#js_selfpay').val()=='No')
                $("input[value='No']").prop('checked', true);
            
            $('#primary_insurance_id').val($('#js_primary_ins').val()).change();            
            //$('input.flat-red').iCheck('update');
        }
        
        $('#js-newpatient-validator').bootstrapValidator({
                message: 'This value is not valid',
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    last_name: {
                        validators: {
                            notEmpty: {
                                message: enter_last_name_err_msg
                            },
                            callback: {
                                message: '',
                                callback: function (value, validator) {
                                    if ($("#last_name").val() != ''){
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
                                        } else{
                                            var name_val = nameValidation();
                                            if(!name_val){
                                                return {
                                                    valid: false, 
                                                    message: 'Name field allowed only 28 characters'
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
                        validators: {
                            notEmpty: {
                                message: enter_first_name_err_msg,
                                callback: function (value, validator) {
                                    bootstrapValidator.enableFieldValidators('middle_name', false);
                                }
                            },
                            callback: {
                                message: '',
                                callback: function (value, validator) {
                                    if ($("#first_name").val() != ''){
                                        var regExp = /^[A-Za-z' ]+$/;
                                        if (!regExp.test(value)){
                                            return {
                                                valid: false, 
                                                message: alphaspace_lang_err_msg
                                            };
                                        } else if(value.indexOf("''")!=-1){
                                            //console.log(value.indexOf("''"));
                                            return {
                                                valid: false, 
                                                message: alphaspace_lang_err_msg
                                            };
                                        } else {
                                            var name_val = nameValidation();
                                            if(!name_val){
                                                return {
                                                    valid: false, 
                                                    message: 'Name field allowed only 28 characters'
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
                                message: 'Name field allowed only 28 characters',
                                callback: function (value, validator) {
                                    var regExp = /^[A-Za-z]+$/;
                                    if ($("#last_name").val() != '' && regExp.test(value)) 
                                        return nameValidation();
                                    return true;
                                }
                            }
                        }
                    },
                    address1: {
                        validators: {
                            notEmpty: {
                                message: enter_address1_err_msg
                            }/*, 
                             regexp: {
                                regexp: /^[a-zA-Z0-9\s\.\-\,]{0,50}$/,
                                message: "Alpha letters only allowed"
                            }   */
                        }
                    },
                    address2: {
                        /*validators: {
                             regexp: {
                                regexp: /^[a-zA-Z0-9\s\.\-\,]{0,50}$/,
                                message: "Alpha letters only allowed"
                            }   
                        }*/
                    },
                    city: {
                        validators: {
                            notEmpty: {
                                message: enter_city_err_msg
                            },
                            regexp:{
                                regexp: /^[A-Za-z\s]+$/,
                                message: "Alpha letters only allowed"
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
                                message: "Alpha letters only allowed"
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
                                    } else */ if((value !='') && ($('#state').val().length <2)) {
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
                    zip5: {
                        message: '',
                        trigger: 'keyup change',
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
                    zip4: {
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
                    },
                    dob: {
                        trigger: 'change',
                        validators: {
                            notEmpty: {
                                message: 'Enter Date Of Birth'
                            },
                            /*remote: {
                                message: pat_exist_err_msg,
                                url: api_site_url+'/patient-check',
                                data:{'_token':$('input[name="_token"]').val(),'dob':$('#dob').val(),'first_name':$("input[name='first_name']").val(),'last_name':$("input[name='last_name']").val(),'encode_patient_id':$("input[name='encode_patient_id']").val()},
                                type: 'POST'
                            }, */                         
                           /* callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var current_date=new Date(value);
                                    var d=new Date();   
                                    
                                    if(value != '' && current_date=='Invalid Date') {
                                        return {
                                            valid: false,
                                            message: date_format
                                        };
                                    }
                                    else if(value != '' && d.getTime() < current_date.getTime()){
                                        return {
                                            valid: false,
                                            message: valid_dob_format_err_msg
                                        };
                                    }
                                    else{
                                        return true;
                                    }
                                }
                            }  */
                                date:{
									format:'MM/DD/YYYY',
                                    message: date_format
                                },
                                callback: {
									message: '',
									callback: function(value, validator, $field) {
                                        var dob = $('.popupmedcubicsform').find('[name="dob"]').filter(function()
                                        { return $(this).val() != ''; }).val();
                                                var current_date = new Date(dob);
                                                var d = new Date();
                                                is_valid_date = true;
                                                var selectYear = [];
                                                if (typeof dob != "undefined" && dob != '') {
                                        var selectYear = dob.split('/');
                                                var is_valid_date = validDateCheck(dob);
                                        }

                                        //Before 1900 validation the year
                                        if ((selectYear[2] <= '1900') && typeof dob != "undefined" && (dob.length == 10) && ((selectYear[2] >= '1000')))
                                        {
											return {
												valid: false,
												message: date_format
											};
                                        }
                                        //return (dob.length != '' && d.getTime() < current_date.getTime())? false : true;
                                        if (new RegExp(/^\d{2}\/\d{2}\/\d{4}$/).test(value) && dob != '' && dob != "undefined" && d.getTime() < current_date.getTime()){
											return {
												valid: false,
                                                message: valid_dob_format_err_msg
											};
                                        }
                                        else if (typeof dob != "undefined" && dob != '' && is_valid_date){
                                        //console.log("comes true")
                                                checkpatientnamedobexist(); return true;
                                        } else{
											return true;
                                        }
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
                    copay_option: {
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: select_copay_option_err_msg
                            }
                        }
                    },              
                    copay: {
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: enter_copay_amt_err_msg
                            },
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    if(value <= 0 && value != ''){
                                        return {
                                            valid: false,
                                            message: greater_than_zero_err_msg
                                        };
                                    } else {
                                        return true;
                                    }
                                }
                            }                          
                        }
                    },  
                    reason: {
                        validators: {
                            notEmpty: {
                                message: 'Enter reason'
                            }                            
                        }
                    },
                     primary_insurance_id: {
                        validators: {
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    is_self_pay = $('input[name=is_self_pay]:checked').val();
                                    
                                    if(value == '' && is_self_pay=='No') {
                                        return {
                                            valid: false,
                                            message: select_pri_ins_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }                            
                        }
                    },
                    mobile: {
                        trigger: 'change keyup',
                        validators: {
                            callback: {
                                message:'',
                                callback: function (value, validator) {
                                    if(value !='') {                                        
                                        var cell_phone_msg = cell_phone_limit_lang_err_msg;
                                        var response = phoneValidation(value,cell_phone_msg);
                                        if(response !=true) {
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
                    home_phone: {
                        validators: {
                            callback: {
                                message:'',
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
                    ssn:{
                        trigger: 'change',
                        validators:{
                            regexp: {
                                regexp: /^[0-9]{9}$/,
                                message:ssn_lang_err_msg
                            },
                            remote: {
                                message: 'This SSN already exists',
                                url: api_site_url+'/ssn-validation',
                                data:{'ssn':$('input[name="ssn"]').val(),'_token':$('input[name="_token"]').val()},
                                type: 'POST'
                            }
                        }
                    },
                    primary_insurance_policy_id: {
                        validators: {
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    
                                    is_self_pay = $('input[name=is_self_pay]:checked').val();
                                   
                                    var pattern = new RegExp(/[~`!#$@%\^&*+=\-\[\]\\';,/{}|\\:<>\?]/); 
                                     
                                    if(value == '' && is_self_pay=='No') {
                                        return {
                                            valid: false,
                                            message: enter_policy_id_err_msg
                                        };
                                    } else if(value != '' && is_self_pay=='No' && pattern.test(value)) {
                                        return {
                                            valid: false,
                                            message: alphanumeric_lang_err_msg
                                        };
                                    } else {                                                                               
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }                            
                        }
                    } 
                }
            })           
            .on('success.form.bv', function(e) { 
                // Prevent form submission
                e.preventDefault();
                $('#js-new_patient').removeClass('hide');
                $('.js_lastname').html($('#last_name').val());
                $('#js_lastname').val($('#last_name').val());
                
                $('.js_firstname').html($('#first_name').val());
                $('#js_firstname').val($('#first_name').val());
                
                $('.js_middlename').html($('#middle_name').val());
                $('#js_middlename').val($('#middle_name').val());
                
                $('.js_address1').html($('#address1').val());
                $('#js_address1').val($('#address1').val());
                
                $('.js_address2').html($('#address2').val());
                $('#js_address2').val($('#address2').val());
                
                $('.js_city').html($('#city').val());
                $('#js_city').val($('#city').val());

                $('.js_dob').html($('#dob').val());
                $('#js_dob').val($('#dob').val());
                
                $('.js_state').html($('#state').val());
                $('#js_state').val($('#state').val());
                
                $('.js_zip5').html($('#zip5').val());
                $('#js_zip5').val($('#zip5').val());
                
                $('.js_zip4').html($('#zip4').val());
                $('#js_zip4').val($('#zip4').val());
                
                $('#js_dob').val($('#dob').val());
                $('#js_gender').val($('input[name=gender]:checked').val());
                
                if($('#dob').val() != ''){
                    $('.js_dob_gender').html($('#dob').val()+', '+$('input[name=gender]:checked').val());
                } else {
                    $('.js_dob_gender').html('');
                }
                var pat_lastname = $('#js_lastname').val()+' ';
                var pat_firstname = $('#js_firstname').val();
                $('#patient_search').val(pat_lastname.concat(pat_firstname));
                
                $('.js_ssn').html($('#ssn').val());
                $('#js_ssn').val($('#ssn').val());
                
                $('.js_mobile').html($('#mobile').val());
                $('#js_mobile').val($('#mobile').val());
                
                $('.js_home_phone').html($('#home_phone').val());
                $('#js_home_phone').val($('#home_phone').val());
                
                $('.js_selfpay').html($('input[name=is_self_pay]:checked').val());
                $('#js_selfpay').val($('input[name=is_self_pay]:checked').val());
                var priIns = $('#primary_insurance_id option:selected').text(); 
                if( priIns == '-- Select --') {
                    $('.js_primary_ins').html('Self Pay');
                } else { 
                    $('.js_primary_ins').html($('#primary_insurance_id option:selected').text());
                    $('#js_primary_ins').val($('#primary_insurance_id option:selected').val());
                }
                $('.js_primary_insurance_policy_id').html($('#primary_insurance_policy_id').val());
                $('#js_primary_insurance_policy_id').val($('#primary_insurance_policy_id').val());
                
                $('#js_newpatient_scheduler').modal('hide');
                $('.help-block').addClass('hide');
                return false;
            }); 
    }); 
    /*if($(this).is(':checked')){
        $('#patient_search').val('');
        $('#patient_id').val('new');
        $('.js-searched_patient_cls').html('');
        $('#js-searched_patient').addClass('hide');
        $('#js-new_patient').removeClass('hide');
    } else {
        $('#js-searched_patient').addClass('hide');
        $('#js-new_patient').addClass('hide');
        $('.js-searched_patient_cls').html('');
        $('#patient_id').val('');
    } */
 });

 function newPatientEnable(){
    if($('#is_new_patient').is(':checked')){
        $('#patient_search').val('');
        $('#patient_id').val('new');
        $('.js-searched_patient_cls').html('');
        $('#js-searched_patient').addClass('hide');
    } else {
        $('#js-searched_patient').addClass('hide');
        $('#js-new_patient').addClass('hide');
        $('.js-searched_patient_cls').html('');
        $('#patient_id').val('');
    }
}
 
 // New Patient popup form
$(document).on('click','.js_pclose_form',function() {
    add_modelclass();
    $("#is_new_patient").prop('checked', false);
    $('input[type="checkbox"].flat-red').iCheck('update');
    fv = $('#js-bootstrap-validator').data('bootstrapValidator');
    fv.enableFieldValidators('patient_search', true).revalidateField('patient_search');
});
 
$(document).on('click','.js_psubmit_form',function() {
    add_modelclass();
});

function add_modelclass()  {
    setTimeout(function() { 
        $('body').addClass('modal-open'); 
    }, 500);
}
 
/* Starts - Facility on change event in appointment popup */
$(document).on('change','#js-ptsh_default_view_list',function() {
    $.ajax({
        url: api_site_url+'/scheduler/getresourcesbydefaultviewlistid',
        type: 'get',
        data: 'default_view_list_id='+$(this).val()+'&default_view='+$('#default_view').val(),
        success: function( result ){
            // Set/replace provider options list
            $('#js-ptsh_resource').html(result);
            $('#js-ptsh_resource').select2("val","");
            
            // Reset/disbale available dates
            $('#provider_available_dates').val('');
            $('#scheduled_on').val('');
            
            // Clear available slot timings
            $('#appointment_time').val('');
            //$('#js-available_slot_timings').html('');
            
            // Revalidate Fields
            revalidateAppointmentFields();
        }        
    });
});
/* Ends - Facility on change event in appointment popup */

/* Starts - Provider on change event in appointment popup */
//$('#js-ptsh_resource').on('change', function() {
$(document).on('change','#js-ptsh_resource',function() {
    $.ajax({
        url: api_site_url+'/scheduler/getscheduledatesbyresourceid',
        type: 'get',
        data: 'default_view_list_id='+$('#js-ptsh_default_view_list').val()+'&resource_id='+$(this).val()+'&default_view='+$('#default_view').val(),
        success: function( result ){           
            // Reset/disbale available dates
            $('#provider_available_dates').val(result);
            $('#scheduled_on').val('');
            
            // Clear available slot timings
            $('#appointment_time').val('');
            //$('#js-available_slot_timings').html('');
            
            // Revalidate Fields
            revalidateAppointmentFields();
        }        
    });
});
/* Ends - Provider on change event in appointment popup */

/* Function to Re-validate fields */
function revalidateAppointmentFields(){
    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('scheduled_on');
    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('appointment_time');
}
/* Function to Re-validate fields */

/** update Appointment in Edit page visit status  **/
$(document).on('change click tigger ','.js-visit_status',function() {
    var visit_status = $(this).val();
    fv = $('#js-bootstrap-validator').data('bootstrapValidator');
    if(visit_status == 'Reschedule' || visit_status == 'Rescheduled'){
        $('#js-edit-appointment').removeClass('hide');
        $('#js-nonedit-appointment').addClass('hide');

        fv.enableFieldValidators('default_view_list_id', true).revalidateField('default_view_list_id');
        fv.enableFieldValidators('resource_id', true).revalidateField('resource_id');
        fv.enableFieldValidators('scheduled_on', true).revalidateField('scheduled_on');
        fv.enableFieldValidators('appointment_time', true).revalidateField('appointment_time');
        fv.enableFieldValidators('patient_search', true).revalidateField('patient_search');
        fv.enableFieldValidators('check_in_time', true).revalidateField('check_in_time');
        fv.enableFieldValidators('check_out_time', true).revalidateField('check_out_time');
    } else {
        $('#js-edit-appointment').addClass('hide');
        $('#js-nonedit-appointment').removeClass('hide');

        fv.enableFieldValidators('default_view_list_id', false).revalidateField('default_view_list_id');
        fv.enableFieldValidators('resource_id', false).revalidateField('resource_id');
        fv.enableFieldValidators('scheduled_on', false).revalidateField('scheduled_on');
        fv.enableFieldValidators('appointment_time', false).revalidateField('appointment_time');
        fv.enableFieldValidators('patient_search', false).revalidateField('patient_search');
    }   
    
    if(visit_status == 'Canceled') {
        $('.js-reason').removeClass('hide');
        $('.reasontitle').html('Reason for Cancel');
        fv.enableFieldValidators('reason', true).revalidateField('reason');
    } else if(visit_status == 'No Show'){
        $('.js-reason').removeClass('hide');
        $('.reasontitle').html('Reason for No Show');
        fv.enableFieldValidators('reason', true).revalidateField('reason');
    } else {
        $('.js-reason').addClass('hide');
        fv.enableFieldValidators('reason', false).revalidateField('reason');
    }   
    //fv.revalidateField('check_in_time');  
});

$(document).on('click','.js-app_resch_appointment',function() {
    var event_id = $(this).attr('data-id');
    //$('#fullCalendarModal').modal('hide');
    UpdateAppointmentScheduler(event_id,'Reschedule');
});
/*
    ## Appointment listing page change the appointment no show option is select time
*/
$(document).on('click','.js-app_noShow_appointment',function() {
    var event_id = $(this).attr('data-id');
    UpdateAppointmentScheduler(event_id,'NoShow');
});

$(document).on('click','.js-app_edit_appointment',function() {
    var event_id = $(this).attr('data-id');
    //$('#fullCalendarModal').modal('hide');
    UpdateAppointmentScheduler(event_id);
});

$(document).on('click','.js-app_appointment_cancel',function() {
    var event_id = $(this).attr('data-id');
    $('#fullCalendarModal_schedular').modal('hide');
    //$('#fullCalendarModal').modal('show');
    UpdateAppointment(event_id);
});

$(document).on('click','.js-app_appointment_operation_cancel',function() {
    var event_id = $(this).attr('data-id');
    $('#appointment_cancel_delete_modal').modal('hide');
    //$('#fullCalendarModal').modal('show');
    //UpdateAppointment(event_id);
});

$(document).on('click','.js-app_cancel_appointment',function() {
    $('#reason_err').removeClass('show').addClass('hide');
    var event_id = $(this).attr('data-id');
    //$('#fullCalendarModal').modal("hide");
    $('#appointment_cancel_delete_modal .modal-title').html('Cancel Appointment');  
    $('#appointment_cancel_delete_modal').modal('show');
    $('input[type=hidden][name="cancel_delete_option"]').val(event_id+'::cancel');
    $('.js-appointment_cancel_delete_reason').val('');
    $('.js-app_appointment_operation_cancel').attr('data-id',event_id);
});

$(document).on('click','.js-app_delete_appointment',function() {
    $('#reason_err').removeClass('show').addClass('hide');
    var event_id = $(this).attr('data-id');
    //$('#fullCalendarModal').modal("hide");
    $('#appointment_cancel_delete_modal .modal-title').html('Delete Appointment');  
    $('#appointment_cancel_delete_modal').modal('show');
    $('input[type=hidden][name="cancel_delete_option"]').val(event_id+'::delete');
    $('.js-app_appointment_operation_cancel').attr('data-id',event_id);
});

$(document).on('click','.js-app_cancel_del_submit',function() {
    var event_id_str    = $('input[type=hidden][name="cancel_delete_option"]').val();
    var event_id_arr    = event_id_str.split('::');
    var reason          = $('.js-appointment_cancel_delete_reason').val();
    if(reason!=''){
        
        $.ajax({
            url: api_site_url+'/scheduler/appointmentdeletecancelprocess/'+event_id_arr[0]+'::'+event_id_arr[1],
            type: 'get',
            data: 'reason='+reason,
            success: function( result ){           
                if(result['status']=='success'){
                    $('#appointment_cancel_delete_modal').modal('hide');
                    if(result['operation']=='cancel')
                        js_sidebar_notification("success",appt_canceled_err_msg);
                    else if(result['operation']=='delete')
                        js_sidebar_notification("success",appt_deleted_err_msg);
                    
                    var view = $('#calendar').fullCalendar('getView');
                    settempCookie('last_view_for', view.name);
                    if(view.name=='month'){
                        var start_date_str  = view.intervalStart;
                    } else {
                        var start_date_str  = view.start;
                    }
                    var stt = new Date(start_date_str);
                    var last_view_date  = stt.getFullYear()+'-'+(stt.getMonth()+1)+'-'+stt.getDate();
                    settempCookie('last_view_date', last_view_date);
                    
                    window.location.reload(true);
                }
            }        
        });     
    } else {
        $('#reason_err').removeClass('hide').addClass('show');
    }   
});

function UpdateAppointmentScheduler(event_id,visit_status){
    var target = api_site_url+'/scheduler/getappointmentbyeventreschedule?event_id='+event_id+'&visit_status='+visit_status;
    $("#fullCalendarModal_schedular").load(target, function(){
        $('#fullCalendarModal_schedular').on('show.bs.modal', function(e){   
            $.AdminLTE.boxWidget.activate();  

            default_view = $('#default_view').val();
            default_view_list_caption = $('#default_view_list_caption').val();
            resource_caption = $('#resource_caption').val();       
            /*$('#fullCalendarModal').removeClass('in');
            $('#fullCalendarModal').css('display',"none");*/
            $("select.select2.form-control").select2();
            
            var get_visitstatus = $('#set_visitstatus').val();
            if((visit_status=='Reschedule') || (visit_status=='Scheduled') || (visit_status=='undefined')){
                $(".js-visit_status").select2("val","Rescheduled");
                $('#js-nonedit-appointment').addClass('hide');
                $('#js-edit-appointment').removeClass('hide');
                //$('#js-nonedit-appointment').addClass('hide');
                // $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('appointment_time');                 
            } else {
                $(".js-visit_status").select2("val",get_visitstatus); 
                $('#js-edit-appointment').addClass('hide');
                $('#js-nonedit-appointment').removeClass('hide');
            }
            
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });         
            
            $(document).delegate('#copay_date','focus', function(){
                $('#copay_date').datepicker({
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    yearRange:'-100:+10',
                    onSelect: function(date, instance) {
                        $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('copay_date');
                    }
               });
            });
            
            calltimepicker();
            
            var get_checkin = $('#set_check_in_time').val();
            var get_checkout = $('#set_check_out_time').val();
            var get_reasonvisit = $('#set_reasonforvisit').val();
            
            setTimeout(function(){
                $('#check_in_time').val(get_checkin);
                $('#check_out_time').val(get_checkout);
            }, 500);
            
            $("select.js-add-new-reasonvisit").select2("val",get_reasonvisit);

            $("#scheduled_on").datepicker({
                dateFormat: "mm/dd/yy",
                minDate:'0',
                setDate: $('#user_selected_date').val(),
                beforeShowDay: function(d) {  
                    provider_available_dates = $('#provider_available_dates').val(); 
                    provider_available_dates = provider_available_dates.split(',');                    
                    var dmy = d.getFullYear()+"-";                    
                    if(d.getMonth()<9) 
                        dmy+="0";
                    dmy+=(d.getMonth()+1)+"-";
                    
                    if(d.getDate()<10) 
                        dmy+="0"; 
                    dmy+=d.getDate();

                    if ($.inArray(dmy, provider_available_dates) != -1) {
                        return [true, "","Available"]; 
                    } else {
                         return [false,"","Unavailable"]; 
                    }
                },
                onSelect: function(date, instance) {                    
                    default_view_list_id = $('#js-ptsh_default_view_list').val();                    
                    resource_id = $('#js-ptsh_resource').val();
                    scheduled_date = $('#scheduled_on').val(); 
                    
                    $.ajax({
                        type: "get",
                        datetype: 'json',
                        url: api_site_url+'/scheduler/getavailabletimeslot?default_view_list_id='+default_view_list_id+'&resource_id='+resource_id+'&user_selected_date='+scheduled_date+'&default_view='+default_view,
                        success: function(result) { 
                            setTimeout(function(){
                                $('#appointment_time').html(result['data']['array_of_time']);
                                $('#appointment_time').select2('val','');
                                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                                fv.enableFieldValidators('scheduled_on', true).revalidateField('scheduled_on');
                                fv.enableFieldValidators('appointment_time', true).revalidateField('appointment_time');
                            }, 1);
                        }
                    });                     
                }
            });
           /* $("[data-mask]").inputmask(); */      

           // Add new reason visit
            if($("div").hasClass( "js-add-new-reason" ) && $( "div.js-add-new-reason" ).find('select optgroup[label=Others]').text() != 'Add New'){ 
                $( "div.js-add-new-reason" ).find('select').append('<optgroup label="Others"><option value="0">Add New</option></optgroup>');
            }
            
            $(document).on('change','#copay_option',function() {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                    if($(this).val()=='Cheque'){
                        $('.js_copay_check_part').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                        fv.enableFieldValidators('copay_check_number', true).revalidateField('copay_check_number');
                    }
                    if($(this).val()=='Check'){
                        $('.js_copay_check_part').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                        fv.enableFieldValidators('copay_check_number', true).revalidateField('copay_check_number');
                    } else if($(this).val()=='CC'){
                        $('.js_copay_check_part').removeClass('show').addClass('hide');
                        $('.js_copay_card_part').removeClass('hide').addClass('show');
                    } else if($(this).val()=='Money Order'){ 
                         $('.js-hide-money').removeClass('hide').addClass('show');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                       // $('.js-label-change').text("MO date");
                        fv.enableFieldValidators('money_order_no', true).revalidateField('money_order_no');
                        /*$('.js_copay_check_part').removeClass('show').addClass('hide');
                        $('.js_copay_card_part').removeClass('hide').addClass('show');
                        fv.enableFieldValidators('money_order_no', true).revalidateField('money_order_no');*/
                    } else {
                        $('.js_copay_check_part').removeClass('show').addClass('hide');
                        $('.js_copay_card_part').removeClass('show').addClass('hide');
                        fv.enableFieldValidators('copay_check_number', false).revalidateField('copay_check_number');
                    }
                    $('.js_copay_date_part').removeClass('hide').addClass('show');
                    fv.enableFieldValidators('copay', true).revalidateField('copay');
                    fv.enableFieldValidators('copay_date', true).revalidateField('copay_date');
                } else {
                    $('.js_copay_check_part').removeClass('show').addClass('hide');
                    $('.js_copay_card_part').removeClass('show').addClass('hide');
                    $('.js_copay_date_part').removeClass('show').addClass('hide');
                    fv.enableFieldValidators('copay', false).revalidateField('copay');
                    fv.enableFieldValidators('money_order_no', false).revalidateField('money_order_no');
                    fv.enableFieldValidators('copay_check_number', false).revalidateField('copay_check_number');
                    fv.enableFieldValidators('copay_date', false).revalidateField('copay_date');
                }
            });
            
            $(document).on('keyup','#copay_amount',function() {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).val()!=''){
                     fv.enableFieldValidators('copay_option', true).revalidateField('copay_option');
                } else {
                     fv.enableFieldValidators('copay_option', false).revalidateField('copay_option');
                }
            });

            $(document).on('change','.js-visit_status',function() {
                $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('check_in_time');
            });
            
            $('#js-bootstrap-validator').bootstrapValidator({
                message: 'This value is not valid',
                excluded: [':disabled', ':hidden', ':not(:visible)'],
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    default_view_list_id: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: 'Select '+default_view_list_caption
                            }
                        }
                    },
                    resource_id: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: 'Select '+resource_caption
                            }
                        }
                    },
                    scheduled_on: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: select_appt_date_err_msg
                            }
                        }
                    },
                    /*appointment_time: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: 'Select appointment slot time'
                            }
                        }
                    },*/
                    appointment_time: {
                        enabled: false,
                        validators: {
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    get_scheduled = $('#scheduled_on').val();
                                    if(value=='' && get_scheduled!=''){
                                        return {
                                                valid: false,
                                                message: select_appt_slot_time_err_msg
                                            };
                                    } else {
                                        return true;
                                    }
                                }
                            }
                        }
                    },
                    reason_for_visit: {
                        validators: {
                            callback: {
                                message:'',
                                callback: function (value, validator) {
                                    if(value == 0 || value == '' ) {
                                        $('#add_new_span').addClass('has-error');
                                        return {
                                            valid: false, 
                                            message: select_reason_visit_err_msg
                                        };
                                    }
                                    return true;
                                }
                            }
                        }
                    },
                    patient_search: {
                        enabled: false,
                        validators: {
                            callback: {
                                message: select_patient_err_msg,
                                callback: function(value, validator, $field) {
                                    return ((!$('#is_new_patient').is(':checked')) && value == '') ? false : true;
                                }
                            }
                        }
                    },
                    copay_option: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: select_copay_option_err_msg
                            }
                        }
                    },              
                    copay: {
                        enabled: false,
                        trigger: 'keyup change',
                        validators: {
                            notEmpty: {
                                message: enter_copay_amt_err_msg
                            },
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    if(value <= 0 && value != ''){
                                        return {
                                            valid: false,
                                            message: greater_than_zero_err_msg
                                        };
                                    } else {
                                        return true;
                                    }
                                }
                            }                          
                        }
                    },  
                    reason: {
                        enabled: false,
                        validators: {
                            notEmpty: {
                                message: 'Enter reason'
                            }                            
                        }
                    },
                    copay_date: {
                        enabled: false,
                        validators: {
                            date: {
                                format: 'MM/DD/YYYY',
                                message: 'Enter valid date format'
                            },
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var current_date=new Date(value);
                                    var d=new Date();   
                                    if(value==''){
                                        return {
                                            valid: false,
                                            message:'Enter date',
                                        };
                                    } else if(value != '' && d.getTime() < current_date.getTime()){
                                        return {
                                            valid: false,
                                            message:'Should not be the future date',
                                        };
                                    } else {
                                        return true;
                                    }
                                }
                            }                            
                        }
                    }, 
                    copay_check_number: {
                        enabled: false,
                        validators: {
                            callback: {
                                message: 'Enter check number',
                                callback: function (value, validator) {                                         
                                    lengthval = $('#check_no_minlength').val();
                                    if(value == ''){
                                        return{
                                            valid:false,
                                            message:empty_check_no
                                        }
                                    } else if(value != '' && !checknumbervalidation(value)){
                                        return{
                                            valid:false,
                                            message:alphanumeric_lang_err_msg
                                        }
                                    } else if(value != '' && value.length < lengthval){
                                        return {
                                            valid:false,
                                            message:checkminlength,
                                        }
                                    } else {
                                        setTimeout(function(){ 
                                            $('.js_copay_check_part').removeClass('has-error');
                                        }, 5); 
                                        return true;
                                    }
                                },                                    
                            } 
                        }
                    },

                      money_order_no:{
                        enabled: false,
                        trigger: 'change keyup',
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
                                type: 'POST',
                                validKey:'valid',
                            },
                            callback: {
								message: "",
								callback: function (value, validator, $field) {
									chkd = $('select[name=copay_option]').val(); 
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
                    check_in_time: {
                        validators: {                                
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) {
                                    var endTime = validator.getFieldElements('check_out_time').val();
                                    var visit_status_val = $('select.js-visit_status').select2("val");
                                                                            var timeStart = new Date("01/01/2007 " + value);
                                    var timeEnd = new Date("01/01/2007 " + endTime);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds
                                    if((value == '' && endTime!='') || (value == '' && visit_status_val=='In Session')) {
                                        return {
                                            valid: false,
                                            message: enter_checkin_time_err_msg
                                        };
                                    } else if(diff<=0 && endTime!=''){
                                        return {
                                            valid: false,
                                            message: check_in_out_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;
                                }
                            }
                        }
                    },
                    check_out_time: {
                            validators: {                            
                            callback: {
                                message: '',
                                callback: function(value, validator, $field) { 
                                    var startTime = validator.getFieldElements('check_in_time').val();
                                    
                                    var timeStart = new Date("01/01/2007 " + startTime);
                                    var timeEnd = new Date("01/01/2007 " + value);

                                    var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds                                    
                                    
                                    if(diff<=0 && startTime!='' && value != ''){
                                        return {
                                            valid: false,
                                            message: check_out_in_diff_err_msg
                                        };
                                    } else {
                                        return {
                                            valid: true,
                                            message: ''
                                        };
                                    }
                                    return false;   
                                }
                            }
                        }
                    }
                }
            })           
            .on('ifToggled', '[name="is_new_patient"]', function(e) {
                fv = $('#js-bootstrap-validator').data('bootstrapValidator');
                if($(this).is(':checked')){
                    fv.enableFieldValidators('patient_search', false);
                } else {
                    fv.enableFieldValidators('patient_search', true).revalidateField('patient_search');
                }
            })      
            .on('success.form.bv', function(e) {
                // Prevent form submission
                e.preventDefault();
                var myform = $('#js-bootstrap-validator');
                var current_event_id = $('#fullCalendarModal').attr("data-eventid");
                var disabled = myform.find(':input:disabled').removeAttr('disabled');
                var serialized = myform.serialize();
                $('.help-block').addClass('hide');
                $.ajax({
                    type : 'GET',
                    dataType: 'json',
                    url  : api_site_url+'/scheduler/updateappointment',
                    data : serialized,
                    success :  function(result){
                        if(result['status'] == 'success'){
                            $('#fullCalendarModal_schedular').html(update_lang_err_msg);
                            $("#js_wait_popup").modal("show");
                            var cur_event_id = (result['message'] =="" || result['message'] ==null)?current_event_id:result['message']; 
                            js_sidebar_notification("success",'Appointment updated successfully');
                            addModal('fullCalendarModal_schedular',cur_event_id);// reschedule update
                            /* 
                                    ### Status is No Show means Page will be reload the appointment listing page
                            */
                            if(visit_status == "NoShow")
                                location.reload();
                            return false;
                        } else {    
                            js_sidebar_notification("error",'Something went wrong');
                            return false;
                        }
                    }
                });
                return false;
            });          
        }); 
        mg = 0; // clear popup edit values 
        $('#fullCalendarModal_schedular').on('hidden.bs.modal', function(){    
            $('#fullCalendarModal_schedular').html('');
        });       
        //$('#fullCalendarModal_schedular .modal-title').html('Edit Appointment');
        $("#fullCalendarModal_schedular").modal("show");
        
        if(visit_status=='Reschedule'){
            fv = $('#js-bootstrap-validator').data('bootstrapValidator');
            fv.enableFieldValidators('default_view_list_id', true).revalidateField('default_view_list_id');
            fv.enableFieldValidators('resource_id', true).revalidateField('resource_id');
            fv.enableFieldValidators('scheduled_on', true).revalidateField('scheduled_on');
            fv.enableFieldValidators('appointment_time', true).revalidateField('appointment_time');
            //fv.enableFieldValidators('patient_search', true).revalidateField('patient_search'); 
            fv.enableFieldValidators('check_in_time', true).revalidateField('check_in_time');
            fv.enableFieldValidators('check_out_time', true).revalidateField('check_out_time');
        }       
        return false;
    }); 
 }

 /*** Main Scheduler appointment update starts ***/
function reloadOldform(get_id,event_id){
    event_id = (event_id =='' || event_id ==null)?'':event_id;
    if(event_id !='') {
        setTimeout(function() { 
            triggerCalendar('yes'); // update background calendar in main scheduler
            UpdateAppointment(event_id); // update appointment detail in main scheduler
        }, 1050); 
        setTimeout(function() {
            addModal("js_wait_popup"); //close update popup window
        }, 1100); 
    }
}
/*** Main Scheduler appointment update end ***/
 
 //starts add new reason for visit 
$(document).on( 'change', '.js-add-new-reasonvisit', function () {
    var current_divid  = $(this).parents('div.js-add-new-reason').attr('id');   
    var selected_value = $(this).val(); 
    $('#'+current_divid).find('p.js-error').html('');
    $('#'+current_divid).find('p.js-error').removeClass('show').addClass('hide');
    if(selected_value == '0'){      
        $(this).parent('div').addClass('hide');
        $('#'+current_divid).children("#add_new_span").removeClass('hide').addClass('show');        
        $('#'+current_divid).find('#newadded_visit').val('');
    } else {        
        $("#add_new_span").removeClass('show').addClass('hide');
    }
});

$(document).on( 'click', '#new_save_visit', function () {   
    var lblname = $(this).parents('div.js-add-new-reason').find('#newadded_visit').attr('data-label-name'); 
    if( !$(this).parents('div.js-add-new-reason').find("#newadded_visit").val() ) {
          $(this).parents('div.js-add-new-reason').find("#newadded_visit").parent('div').addClass('has-error');                       
          $(this).parents('div.js-add-new-reason').find('p.js-error').html('Enter new '+lblname.toLowerCase());
          $(this).parents('div.js-add-new-reason').find('p.js-error').removeClass('hide').addClass('show');
    } else {        
        $(this).parents('div.js-add-new-reason').find("#newadded_visit").parent('div').removeClass('has-error');
        var tablename = $(this).parents('div.js-add-new-reason').find('#newadded_visit').attr('data-table-name');
        var fieldname = $(this).parents('div.js-add-new-reason').find('#newadded_visit').attr('data-field-name');       
        var addedvalue = $(this).parents('div.js-add-new-reason').find('#newadded_visit').val();        
        var seldivid = $(this).parents('div.js-add-new-reason').attr('id');
        var pars = 'tablename='+tablename+'&fieldname='+fieldname+'&addedvalue='+addedvalue;
        $.ajax({
            url: api_site_url+'/scheduler/addnewselect',
            type: 'get',
            data: pars,
            success: function( data ){              
                if(data == 'error'){                    
                    $('#'+seldivid).find("#newadded_visit").parent('div').addClass('has-error');
                    $('#'+seldivid).find('p.js-error').html(lblname+' already exist');
                    $('#'+seldivid).find('p.js-error').removeClass('hide').addClass('show');
                }else{                  
                    $('#'+seldivid).find('.js-add-new-reasonvisit').parent('div').removeClass('hide').addClass('show');
                    $('#'+seldivid).find('#add_new_span').addClass('hide');
                    getreasonvalues(tablename,fieldname,seldivid,addedvalue,data);
                    $('#js-add-new-reasonvisit').trigger("chosen:updated");
                }                
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
            }
        });
    }   
});

$(document).on( 'click', '#new_cancel_visit', function () { 
    $(this).parents('div.js-add-new-reason').find("#newadded_visit").parent('div').removeClass('has-error');
    $(this).parents('div.js-add-new-reason').find("#add_new_span").removeClass('show').addClass('hide');    
    var seldivid = $(this).parents('div.js-add-new-reason').attr('id'); 
    $(this).parents('#'+seldivid).find('.js-add-new-reasonvisit').parent('div').removeClass('hide').addClass('show');   
    $(this).parents('#'+seldivid).find('.js-add-new-reasonvisit').select2("val", "");
    $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('reason_for_visit');
});

$(document).on( 'keyup', '#newadded_visit', function () {   
    $(this).parent('div').removeClass('has-error');
    $(this).parents('div#add_new_span').find('.js-error').removeClass('show').addClass('hide');
});

function getreasonvalues(tablename,fieldname,seldivid,addedvalue,getreason) {
    $.ajax({
        type: "GET",
        url: api_site_url+'/getoptionvalues',   
        data: 'tablename='+tablename+'&fieldname='+fieldname+'&addedvalue='+addedvalue,
        success: function(result){  
            $('#'+seldivid).find("select.js-add-new-reasonvisit").html(result);
            $("select.js-add-new-reasonvisit").select2("val",getreason);
            $("select.js-add-new-reasonvisit").select2();
            $('#'+seldivid).find('.js-add-new-reasonvisit').parents('div.js-add-new-reason').find("#add_new_span").removeClass('show').addClass('hide');
            $('#js-bootstrap-validator').data('bootstrapValidator').revalidateField('reason_for_visit');
        }
    });
}
//ends add new reason for visit 

function calltimepicker(){ 
    $(".timepicker1").timepicker({
       showInputs: false,
       defaultTime:false,
    });
}

$(document).on( 'change', '#check_in_time, #check_out_time', function () {  
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_in_time');
    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_out_time');
});

$(document).on('click','#scheduled_on_icon',function() {  
    $('#scheduled_on').focus();
});
/*$(document).on('keyup keydown keypress blur change', '#dob',function (e) {
    if((e.type == "change" || e.type == "focusout"))
            setTimeout(() => {checkpatientnamedobexist();}, 200)
    
});*/

function checkpatientnamedobexist(){
    var firstname = $("input[name='first_name']:visible").val();
    var lastname = $("input[name='last_name']:visible").val();
    var encode_patient_id = $("input[name='encode_patient_id']").val();     
    var dob_val         = $('#dob:visible').val();
    $.when($.ajax({
        url: api_site_url+'/patient-check',
        type : 'post', 
        data : {'_token':$('input[name="_token"]').val(),'dob':dob_val,'first_name':firstname,'last_name':lastname,'encode_patient_id':encode_patient_id},
        dataType: 'json',       
    })
    ).then((status)=>{if(status.patient_status)  
	js_sidebar_notification('error',status.msg); }, 
    (error)=>{//console.log(error); 
        });    
}

$(document).on('click','#new_patient_dob_icon',function() {  
    $('#dob').focus();
});

function appointmentStatsdynamic(date_range){
    
    var view = $('#calendar').fullCalendar('getView');
    if(view.name=='agenda7Day' || view.name=='month'){
        if(view.name=='month'){
            var start_date_str  = view.intervalStart;
            var end_date_str    = view.intervalEnd;
        } else {
            var start_date_str  = view.start;
            var end_date_str    = view.end;
        }
        var s = new Date(start_date_str);
        var e = new Date(end_date_str - (24 * 60 * 60 * 1000));
        var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
        var end_date    = e.getFullYear()+'-'+(e.getMonth()+1)+'-'+e.getDate();
    } else {
        var start_date_str  = view.start;
        var s = new Date(start_date_str);
        var start_date = end_date = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
    }
    
    /*if(date_range == '' || date_range == 'def'){
        var scheduler_calendar_val  = $('#scheduler_calendar').val();
        var view_option             = 'day';
    } else {
        var scheduler_calendar_val  = date_range;
        var view_option             = 'week';
    }*/
    
    var scheduler_calendar_val  = start_date+'::'+end_date;
    var view_option             = 'week';
    if($('input[name="default_view"]').is(':checked') && $('input[name="default_view_list"]').is(':checked')) {
        var default_view_option_val = $('input[name=default_view]:checked').val();
        var default_view_list_option_val = $('input[name=default_view_list]:checked').val();
        var resource_option_val = [];
        $('input[name=resource]:checked').each(function() {
            resource_option_val.push($(this).attr('value'));
        });
        
        if(default_view_option_val != '' && typeof default_view_option_val != "undefined" &&  
            default_view_list_option_val != '' && typeof default_view_list_option_val != "undefined" && 
            resource_option_val!= '' && typeof resource_option_val != "undefined"){
            $.ajax({
                url: api_site_url+'/scheduler/appointmentStatsdynamic_count/'+scheduler_calendar_val+'/'+default_view_option_val+'/'+default_view_list_option_val+'/'+resource_option_val+'/'+view_option,   
                type: 'get',
                success: function(result){
                    $('#js-scheduler_stats').html(result);
                }
            }); 
        }
    }
}

$(document).on('click','.fc-agenda7Day-button,.fc-prev-button,.fc-next-button,.fc-month-button,.fc-agendaDay-button',function() {
    var view = $('#calendar').fullCalendar('getView');
    if(view.name=='agenda7Day' || view.name=='month'){
        if(view.name=='month'){
            var start_date_str  = view.intervalStart;
            var end_date_str    = view.intervalEnd;
        } else {
            var start_date_str  = view.start;
            var end_date_str    = view.end;
        }
        var s = new Date(start_date_str);
        var e = new Date(end_date_str - (24 * 60 * 60 * 1000));
        var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
        var end_date    = e.getFullYear()+'-'+(e.getMonth()+1)+'-'+e.getDate();
        appointmentStatsdynamic(start_date+'::'+end_date);
    } else {
        var start_date_str  = view.start;
        var s = new Date(start_date_str);
        var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
        appointmentStatsdynamic(start_date+'::'+start_date);
    }
});

$(document).delegate('.js_pat_search_datepicker','focus', function(){
    $('.js_pat_search_datepicker').datepicker({
        maxDate: 0,
        changeMonth: true,
        changeYear: true,
        yearRange:'-100:+100',
        onSelect: function(date, instance) {
            $("#patient_search").autocomplete('search',date);
        }
    });
});

function patient_search_func(){
    //console.log("patient search function");
    var patient_search_category = $('select#js-patient_search_category').select2("val");
   
    $('#patient_search').autocomplete({ 
        source: api_site_url+'/scheduler/searchpatient/'+patient_search_category,
        minLength: 1,

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
            if((patients.item.primary_insurance_policy_id != 'undefined'))
            {   
                $('#js-search_patient_primary_insurance_policy_id').html(patients.item.primary_insurance_policy_id);                
            }   
            else { 
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
            }
            else if(patients.item.eligibility_verification == 'Active' && patients.item.getReachEndday <= 0){
                $('.js_eliactive').css('display','block');
            }
            else if(patients.item.eligibility_verification == 'Inactive' || patients.item.getReachEndday > 0){
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
        }
    });
}

$(document).delegate('a[data-target=#auth]', 'click', function(){   
    var target = $(this).attr("data-url");   
    $("#auth .modal-body").load(target, function(){    
    });     
});

// Patient search option
$(document).on('click','.ui-menu-item',function() {
    var getPatient = $(this).text().split(',');
    $('#patient_search').val(getPatient[0]);
});

$(document).on('click','.js-submit-btn',function() {
    if($('#patient_id').val()==''){
        $('#patient_search').val('');
        $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'patient_search');
        $('#js-bootstrap-validator').bootstrapValidator('validate');
    }
});

$(document).on('ifChecked change', "input[name='is_self_pay']", function () {
    fv = $('#js-newpatient-validator').data('bootstrapValidator');
        if(this.value=='No'){
         $('.js-primary-ins-part').removeClass('hide');
         $('.js-policy-id-part').removeClass('hide');
         fv.enableFieldValidators('primary_insurance_id', true).revalidateField('primary_insurance_id');
         fv.enableFieldValidators('primary_insurance_policy_id', true).revalidateField('primary_insurance_policy_id');
    } else {
         $('.js-primary-ins-part').addClass('hide');
         $('.js-policy-id-part').addClass('hide');
         $("#primary_insurance_id").select2("val","");
         $('#primary_insurance_policy_id').val('');
         fv.enableFieldValidators('primary_insurance_id', false).revalidateField('primary_insurance_id');
         fv.enableFieldValidators('primary_insurance_policy_id', true).revalidateField('primary_insurance_policy_id');
    }
});

$(document).on('focusin','#js-bootstrap-validator input, #check_in_time, #check_out_time',function() {
    $('.bootstrap-timepicker-widget').removeClass("open");
    var scheduler_input_name = $(this).attr("name");
    if(scheduler_input_name =="check_in_time" ||scheduler_input_name =="check_out_time")
        $(this).parents("DIV.bootstrap-timepicker").find(".bootstrap-timepicker-widget").addClass("open");  
});

function settempCookie(cname, cvalue) {
    var d = new Date();
    d.setTime(d.getTime() + (60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
} 

function gettempCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
} 

$(document).ready(function () {
    var last_view_for = gettempCookie('last_view_for');
    var last_view_date = gettempCookie('last_view_date');
   
    if(last_view_for!=''){
        setTimeout(function(){ $('#calendar').fullCalendar('changeView',last_view_for); }, 500);
    }
    if(last_view_date!=''){
       var last_view_date = new Date(last_view_date.replace(/-/g, "/"));
       setTimeout(function(){ $('#calendar').fullCalendar('gotoDate',last_view_date); }, 500);
    }
    
    if(last_view_for!='' || last_view_date!=''){
        setTimeout(function(){  
            var view = $('#calendar').fullCalendar('getView');
            if(view.name=='agenda7Day' || view.name=='month'){
                if(view.name=='month'){
                    var start_date_str  = view.intervalStart;
                    var end_date_str    = view.intervalEnd;
                } else {
                    var start_date_str  = view.start;
                    var end_date_str    = view.end;
                }
                var s = new Date(start_date_str);
                var e = new Date(end_date_str - (24 * 60 * 60 * 1000));
                var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
                var end_date    = e.getFullYear()+'-'+(e.getMonth()+1)+'-'+e.getDate();
                appointmentStatsdynamic(start_date+'::'+end_date);
            } else {
                var start_date_str  = view.start;
                var s = new Date(start_date_str);
                var start_date  = s.getFullYear()+'-'+(s.getMonth()+1)+'-'+s.getDate();
                appointmentStatsdynamic(start_date+'::'+start_date);
            }
        }, 500);
    }
   
    settempCookie('last_view_for','');
    settempCookie('last_view_date','');
});

$(document).on("change",".allownumericwithdecimal",function (event) { 
    var value;
    val = $(this).val();  
    value = (!isNaN(val) && typeof val != 'undefined' && val != '')?parseFloat(val).toFixed(2):parseFloat('0.00').toFixed(2);
});

$(document).on('keypup', '.allownumericwithdecimal', function(event) {
    //console.log("keyup conditions");
    var i = 0; 
    value = $(this).val();  
    value = value.replace(/(?!^)-/g, '').replace(/\./g, function(match) { 
        return match === "." ? (i === 0 ? '.' : '') : ''; 
    });   
    $(this).val(value);
});

$(document).on('keypress change', '.allownumericwithdecimal', function(event) {
    var $this = $(this);    
    if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
       ((event.which < 48 && event.which != 45|| event.which > 57) &&
       (event.which != 0 && event.which != 8))) {       
           event.preventDefault();
    }
    
    var text = $(this).val();
    if ((event.which == 46) && (text.indexOf('.') == -1)) {
        setTimeout(function() {
            if ($this.val().substring($this.val().indexOf('.')).length > 3) {
                $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
            }
        }, 1);       
    }

    if ((text.indexOf('.') != -1) &&
        (text.substring(text.indexOf('.')).length > 2) &&
        (event.which != 0 && event.which != 8) &&
        ($(this)[0].selectionStart >= text.length - 2)) {        
            event.preventDefault();
    }
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

function checknumbervalidation(value)
{
    reg = /^[a-zA-Z0-9]*$/;
    var check = reg.test(value); 
    return check;
}
$("input .dm-date").datepicker();
/* Scroll  in model popup calendar hide the datepicker in Quick patient */
$('#js_newpatient_scheduler, .modal').scroll(function(){
    $("input").datepicker("hide");
    $("input").blur();
});/* 
// New Appointment facilty select  
$(document).on('keypress click change load','#js-ptsh_default_view_list',function(){
    var facility_id = $(this).val();
    if(facility_id == ""){
        $('#js-ptsh_resource').prop('disabled', true);
        $('#scheduled_on').prop('disabled', true);
        $('#appointment_time').prop('disabled', true);
    } else {
        $('#js-ptsh_resource').prop('disabled', false);
    }
});
//New Appointment provider select
$(document).on('keypress click change load','#js-ptsh_resource',function(){
    var ptsh_resource = $(this).val();
    if(ptsh_resource == ""){
        $('#scheduled_on').prop('disabled', true);
        $('#appointment_time').prop('disabled', true);
    } else {
        $('#scheduled_on').prop('disabled', false);     
    }
});
// New Appointment Date option click
$(document).on('keypress click change load','#scheduled_on',function(){
    var scheduled_on = $(this).val();
    if(scheduled_on == ""){
        $('#appointment_time').prop('disabled', true);
    } else {
        $('#appointment_time').prop('disabled', false);
    }
});
function myFunction() {
    $(document).on('keypress click change load','#scheduled_on',function(){
        var scheduled_on = $(this).val();
        if(scheduled_on == ""){
            $('#appointment_time').prop('disabled', true);
        } else {
            $('#appointment_time').prop('disabled', false);
        }
    });
} */
function ListAppointment(event_id,){
    $("#js_open_scheduler_pop_up").html('');
    
    /* $("#create_problem_list").load(target, function(){ 
        $('#create_problem_list').on('show.bs.modal', function(e){
            $(function() {
                $('#create_problem_list .followup_date').datepicker({ 
                    changeMonth: false,
                    changeYear: false,
                    minDate:'0',
                    dateFormat: 'mm/dd/yy',
                    yearRange: '0+:2150'
                });  
            });     
            problemlistCreate(); 
            $.AdminLTE.boxWidget.activate();  
            $("select.select2").select2();             
        });             
        $("#create_problem_list").modal("show");            
    });  */
}
//Appointment list popup open 
$(document).on('click', '.js_popup_appt', function(){
    var appt_id = $(this).attr('data-id');
    //console.log(appt_id);
    UpdateAppointment(appt_id);
    //ListAppointment(appt_id,target);
});
//
/*  Appointment listing in AJAX request Passing  */
$(document).on('click, ifToggled change', '.js_all_appointment',function(){
    if ($('.js_all_appointment').is(":checked"))
    {
        // it is checked
        url_line = api_site_url+'/scheduler/appointmentlist/ajax';
        
    } else {
        //Not checked
        url_line = api_site_url+'/scheduler/appointmentlist/ajax/type';
    }
    $('.js_append_appointment').html("");
        
        //Ajax Request 
        $.ajax({
                url: url_line,
                type: 'get',
                success: function( data, textStatus, jQxhr ){
                    $('.js_append_appointment').html(data);
                    $("#example1").DataTable();
                },
                error: function( jqXhr, textStatus, errorThrown ){
                        console.log( errorThrown );
                }
        });
});
/* 
    ## Appointment Co pay amount is number or not check here
*/
$(document).on('change',"#copay_amount",function(){
  var x = parseFloat($(this).val());
  $(this).val(parseFloat($(this).val()).toFixed(2));
  if ( isNaN(x) ) 
    $(this).val(""); 
});