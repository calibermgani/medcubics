var calendar = {

  init: function() {

    var mon = 'Mon';
    var tue = 'Tue';
    var wed = 'Wed';
    var thu = 'Thu';
    var fri = 'Fri';
    var sat = 'Sat';
    var sun = 'Sun';

    /**
     * Get current date
     */
    var d = new Date();
    var strDate = yearNumber + "/" + (d.getMonth() + 1) + "/" + d.getDate();
    var yearNumber = (new Date).getFullYear();
    /**
     * Get current month and set as '.current-month' in title
     */
    var monthNumber = d.getMonth() + 1;

    function GetMonthName(monthNumber) {
      var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      return months[monthNumber - 1];
    }

    setMonth(monthNumber, mon, tue, wed, thu, fri, sat, sun);

    function setMonth(monthNumber, mon, tue, wed, thu, fri, sat, sun) {
      $('.month').text(GetMonthName(monthNumber) + ' ' + yearNumber);
      $('.month').attr('data-month', monthNumber);
      printDateNumber(monthNumber, mon, tue, wed, thu, fri, sat, sun);
    }

    $('.btn-next').on('click', function(e) {
      var monthNumber = $('.month').attr('data-month');
      if (monthNumber > 11) {
        $('.month').attr('data-month', '0');
        var monthNumber = $('.month').attr('data-month');
        yearNumber = yearNumber + 1;
        setMonth(parseInt(monthNumber) + 1, mon, tue, wed, thu, fri, sat, sun);
      } else {
        setMonth(parseInt(monthNumber) + 1, mon, tue, wed, thu, fri, sat, sun);
      };
    });

    $('.btn-prev').on('click', function(e) {
      var monthNumber = $('.month').attr('data-month');
      if (monthNumber < 2) {
        $('.month').attr('data-month', '13');
        var monthNumber = $('.month').attr('data-month');
        yearNumber = yearNumber - 1;
        setMonth(parseInt(monthNumber) - 1, mon, tue, wed, thu, fri, sat, sun);
      } else {
        setMonth(parseInt(monthNumber) - 1, mon, tue, wed, thu, fri, sat, sun);
      };
    });

    /**
     * Get all dates for current month
     */

    function printDateNumber(monthNumber, mon, tue, wed, thu, fri, sat, sun) {

      $($('tbody.event-calendar tr')).each(function(index) {
        $(this).empty();
      });

      $($('thead.event-days tr')).each(function(index) {
        $(this).empty();
      });

      function getDaysInMonth(month, year) {
        // Since no month has fewer than 28 days
        var date = new Date(year, month, 1);
        var days = [];
        while (date.getMonth() === month) {
          days.push(new Date(date));
          date.setDate(date.getDate() + 1);
        }
        return days;
      }

      i = 0;

      setDaysInOrder(mon, tue, wed, thu, fri, sat, sun);

      function setDaysInOrder(mon, tue, wed, thu, fri, sat, sun) {
        var monthDay = getDaysInMonth(monthNumber - 1, yearNumber)[0].toString().substring(0, 3);
        if (monthDay === 'Mon') {
          $('thead.event-days tr').append('<td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thu + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sun + '</td>');
        } else if (monthDay === 'Tue') {
          $('thead.event-days tr').append('<td>' + tue + '</td><td>' + wed + '</td><td>' + thu + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sun + '</td><td>' + mon + '</td>');
        } else if (monthDay === 'Wed') {
          $('thead.event-days tr').append('<td>' + wed + '</td><td>' + thu + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sun + '</td><td>' + mon + '</td><td>' + tue + '</td>');
        } else if (monthDay === 'Thu') {
          $('thead.event-days tr').append('<td>' + thu + '</td><td>' + fri + '</td><td>' + sat + '</td><td>' + sun + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td>');
        } else if (monthDay === 'Fri') {
          $('thead.event-days tr').append('<td>' + fri + '</td><td>' + sat + '</td><td>' + sun + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thu + '</td>');
        } else if (monthDay === 'Sat') {
          $('thead.event-days tr').append('<td>' + sat + '</td><td>' + sun + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thu + '</td><td>' + fri + '</td>');
        } else if (monthDay === 'Sun') {
          $('thead.event-days tr').append('<td>' + sun + '</td><td>' + mon + '</td><td>' + tue + '</td><td>' + wed + '</td><td>' + thu + '</td><td>' + fri + '</td><td>' + sat + '</td>');
        }
      };
      $(getDaysInMonth(monthNumber - 1, yearNumber)).each(function(index) {
        var index = index + 1;
        if (index < 8) {
          $('tbody.event-calendar tr.1').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 15) {
          $('tbody.event-calendar tr.2').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 22) {
          $('tbody.event-calendar tr.3').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 29) {
          $('tbody.event-calendar tr.4').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        } else if (index < 32) {
          $('tbody.event-calendar tr.5').append('<td date-month="' + monthNumber + '" date-day="' + index + '" date-year="' + yearNumber + '">' + index + '</td>');
        }
        i++;
      });
      var date = new Date();
      var month = date.getMonth() + 1;
      var thisyear = new Date().getFullYear();
      setCurrentDay(month, thisyear);
      setEvent();
      displayEvent();
    }

    /**
     * Get current day and set as '.current-day'
     */
    function setCurrentDay(month, year) {
      var viewMonth = $('.month').attr('data-month');
      var eventYear = $('.event-days').attr('date-year');
      if (parseInt(year) === yearNumber) {
        if (parseInt(month) === parseInt(viewMonth)) {
          $('tbody.event-calendar td[date-day="' + d.getDate() + '"]').addClass('active');
        }
      }
    };

    /**
     * Add class '.active' on calendar date
     */
	 $(document).on( 'click', '.calendar tbody td', function (e) {
		var curent_date = $(this).text();
		var viewMonth = $(this).attr('date-month');
		var eventYear = $(this).attr('date-year');
		 $('.start_date').val(eventYear+"-"+viewMonth+"-"+curent_date);
        $('tbody.event-calendar td').removeClass('active');
		 $(this).addClass('active');
		 $(".event_calendar").addClass('hide');
    });

    /**
     * Add '.event' class to all days that has an event
     */
	function setEvent() {
	  $('.day-event').each(function(i) {
		var eventMonth = $(this).attr('date-month');
		var eventDay = $(this).attr('date-day');
		var eventYear = $(this).attr('date-year');
		var eventClass = $(this).attr('event-class');
		if (eventClass === undefined) eventClass = 'event';
		else eventClass = 'event ' + eventClass;

		if (parseInt(eventYear) === yearNumber) {
		  $('tbody.event-calendar tr td[date-month="' + eventMonth + '"][date-day="' + eventDay + '"]').addClass(eventClass);
		}
	  });
	};
	
    /**
     * Get current day on click in calendar
     * and find day-event to display
     */
    function displayEvent() {
      $('.calendar tbody.event-calendar td').on('click', function(e) {
        $('.day-event').slideUp('fast');
        var monthEvent = $(this).attr('date-month');
        var dayEvent = $(this).text();
        $('.day-event[date-month="' + monthEvent + '"][date-day="' + dayEvent + '"]').slideDown('fast');
		$('.today').slideDown('fast');
      });
    };
	
    /**
     * Close day-event
     */
   
	
	$(document).on( 'click', '#weekCal a', function () {
		var mark='';
		var day_mark = $(this).attr("data-index-number");
		var xtr = $(".selectedDay").attr("data-index-number");
		$('.selectedDay').each(function(i, selected){ 
			var reminder_days= $(selected).attr("data-index-number");
			mark+=reminder_days+",";
		});
		$("#reminder_days").val(mark);
		if(!mark){
			$('#js_reminder_type_repeated_by_Weekly .form-group').addClass('has-error');
			$('#js_reminder_type_repeated_by_Weekly .form-group').find('small.help-block').show();
			$('#js_reminder_type_repeated_by_Weekly .form-group').find('i.form-control-feedback').show();
			$('.update').attr('disabled',true);
		} 
		else{
			$('#js_reminder_type_repeated_by_Weekly .form-group').removeClass('has-error');
			$('#js_reminder_type_repeated_by_Weekly .form-group').addClass('has-success');
			$('#js_reminder_type_repeated_by_Weekly .form-group').find('small.help-block').hide();
			$('#js_reminder_type_repeated_by_Weekly .form-group').find('i.form-control-feedback').hide();
			$('.update').attr('disabled',false);
		}
		
    });
	$(document).on('ifToggled change', '.js_reminder_type,.js_reminder_type_repeated_by', function (event) {
		var parent_id= $(this).parents("div .in").attr("id");
		var chk = $(this).is(":checked");
		var name = $(this).attr('name');
		//alert(event.type);
		if(chk == true || event.type== "change"){ 
			var add_class = $(this).val();
			var id_name = $(this).attr('id');
			$("."+name).addClass('hide');
			$("#"+parent_id+" #"+id_name+"_"+add_class).removeClass('hide');
			var d =new Date($("#end_date_hide").val());
			var get_date = $.datepicker.formatDate('mm/dd/yy', d);
			d.setMonth( d.getMonth() + 1 );
			if((d.getMonth()+1) < 10) var newdate_month =  "0"+(d.getMonth()+1); else var newdate_month = (d.getMonth()+1);
			if((d.getDate()) < 10) var newdate_date =  "0"+d.getDate(); else var newdate_date = d.getDate();
			if(add_class != ""){
				switch (add_class) {
					case "one-time":
						$(".end_date").val(get_date);	
						$('#js_reminder_type_repeat').find('.form-group').removeClass('has-error');
						$('#js_reminder_type_repeat').find('.form-group').addClass('has-success');
						$('#js_reminder_type_repeat').find('.form-group').find('small.help-block').hide();
						$('#js_reminder_type_repeat').find('.form-group').find('i.form-control-feedback').hide();
						$('.update').attr('disabled',false);
						break;
					case "repeat":
						$("#end_date").val(newdate_month + '/' +newdate_date+ '/' +d.getFullYear());
						break;
					case "never":
						$('#js_reminder_type_repeat_on .form-group').removeClass('has-error');
						$('#js_reminder_type_repeat_on .form-group').addClass('has-success');
						$('#js_reminder_type_repeat_on .form-group').find('small.help-block').hide();
						$('#js_reminder_type_repeat_on .form-group').find('i.form-control-feedback').hide();
						$('.update').attr('disabled',false);
						break;
				}
			}
		}
	});
	
	$(document).on('focus','#start_time,#end_time,#timestamp,#edit-start_time', function() { 
		$(this).timepicker({ 'timeFormat': 'h:i A','step': 15 });
	});		

	$(document).on('focus','.js_date_picker', function() { 
		var curnt_id = $(this).attr('id');
		$("#"+curnt_id).datepicker({
			type:'date',
			dateFormat: 'mm/dd/yy', 
			minDate: 0,
			changeMonth: true,
			changeYear: true,
			autoclose: true
		});
	});
	$("#weekCal").weekLine(); 
  },
};

$(document).ready(function() {
  calendar.init();
});

$(document).on('click','.calendars tbody.event-calendar td', function() {
	$('.calendars tbody.event-calendar td').removeClass('active');
	$(this).addClass('active');
	var year = $(this).attr('date-year');
	var month = $(this).attr('date-month');
	var day = $(this).attr('date-day');
	var current_date=new Date(year+","+month+","+day);
	var d=new Date(); 
	var today_date=new Date(d.getFullYear()+","+(d.getMonth()+1)+","+ d.getDate()); 
	if (today_date.getTime() > current_date.getTime()) $(".add_events").attr('data-target','');
	else $("#add_events").attr('data-target','#form_modal_add');
	var timestamp=current_date.getTime()/1000;
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/profile/calendar/event/show/'+timestamp,
			success :  function(result)
			{ 
				if(result !='')
					{$("#js_list").html(result);}
				else
				{
					$("#list_replace").html('');
					$("h4 span").html("0 Events");
					$("#js_list_replace").html('<p class="med-orange">No Events For Next One Week!</p>');
				}
			}
	
	});
});

/***** Delete Fuction starts here *******/
$(document).on('click','.js-delete-confirm', function() {
	var unique_id=$(this).attr('name');
	$("#delete_conformation_link").attr('name',unique_id);
});
$(document).on('click','#delete_conformation_link', function() {
	var id=$(this).attr('name');
	$.ajax({
		type : 'GET',
		url  : api_site_url+'/profile/calendar/event/delete/'+id,
		success :  function(result) { 
			var res = result.split(",");
			var count = res[0];
			var date = res[1];
			var get_date = date.replace(/\b0(?=\d)/g, '');
			var r_date = get_date.split("-");
			var date_day = r_date[2];
			var date_month = r_date[1];
			var date_year = r_date[0];
			$(".row").find('.col-lg-12:first').html('<p class="alert alert-success" id="success-alert">Events deleted successfully.!</p>');
			
			var numItems = $('.js_count_list').length-1;
			$("h4 span").html(numItems + " Events");
			if(numItems ==0){
				var class_id=$("#event_list_"+id).parents("div .box-body").find("p").first().html("No events!");
			}
			if(count == 0){
			$('#update_event_list [date-day="'+date_day+'"][date-month="'+date_month+'"][date-year="'+date_year+'"]').remove();
			$('.event-calendar [date-day="'+date_day+'"][date-month="'+date_month+'"][date-year="'+date_year+'"]').removeClass('event');
				$("#js_delete_list"+id).parent().remove();
			}
			else{
				var url=$("#event_list_"+id).remove();
				$("#form-modal-edit"+id).remove();
			}
			$("#success-alert").fadeTo(1000, 600).slideUp(600, function(){
				//$("#add_doc_success-alert").alert('close');
			});
			return false;
		}
	});
});
/***** Delete Fuction end  here *******/
$(document).on('click','.edit_events', function(){
	var id=$(this).attr('name');
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/profile/calendar/event/edit/'+id,
			success :  function(result)
			{ 
				if(result !='') {
					$(".modal").html('');
					$("#form-modal-edit"+id).html(result);
					var parsed_result=$('#reminder_days').val(); 
					var parsed_result=parsed_result.split(","); 
					if(parsed_result !=''){
						$.each(parsed_result, function(i, val) {
							$('#weekCal a:eq('+val+')').addClass( "selectedDay");
						});
					}
					$(".select2").select2();
					//Flat red color scheme for iCheck
					$('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});
					$("#weekCal").weekLine(); 
				}
			}
	
	});
});
$(".add_events").click(function(){ 
	 
	var year = $("td.active").attr('date-year');
	var month = $("td.active").attr('date-month');
	var day =$("td.active").attr('date-day');
	
	var date=new Date(year+","+month+","+day);
	var dt = new Date();
	var app_fix = dt.getMinutes();
	if(date == "Invalid Date") {
		var months = $(".calendars span").attr('data-month');
		var date = new Date(dt.getFullYear()+","+months+","+"01");
	}
	var app_fix = dt.getMinutes();
	
    var url = $(this).attr('data-url');
    $.ajax({
        type: "GET",
        url: url,	
        data: '',
        success: function(data){	
			$("#form_modal_add").html(data);
			if(app_fix < 7){
				var start_time = dt.getHours()+ ":15 ";
				var usDatea = start_time.split(':');
				var end_time= dt.getHours() + ":45 ";
			}else if(app_fix >6 && app_fix < 21){
				var start_time = dt.getHours() + ":30 ";
				var end_time= dt.getHours()+1 + ":00 ";
			}else if(app_fix > 20 && app_fix < 36){
				var start_time = dt.getHours() + ":45 ";
				var end_time= dt.getHours()+1 + ":15 ";
			}else if(app_fix > 35 && app_fix < 51){
				var start_time = dt.getHours()+1 + ":00 ";
				var end_time= dt.getHours()+1 + ":30 ";
			}
			else if(app_fix >50 && app_fix <= 59){
				var start_time = dt.getHours()+1 + ":15 ";
				var end_time= dt.getHours()+1 + ":45 ";
			}
			var start = start_time.slice(0, 2);
			var end = end_time.slice(0, 2);
			var s_time = start-12;
			var e_time = end-12;
			if(start < 12) var start_time = start_time+"AM";
			else if(start ==12) var start_time = start_time+"PM";
			else if(start >12) var start_time = start_time.replace(start, s_time)+"PM";
			if(end < 12) var end_time = end_time+"AM";
			else if(end ==12) var end_time = end_time+"PM";
			else if(end >12) var end_time = end_time.replace(end, e_time)+"PM";
			
			if((date.getMonth()+1) < 10) var newdate_month =  "0"+(date.getMonth()+1); else var newdate_month = (date.getMonth()+1);
			if((date.getDate()) < 10) var newdate_date =  "0"+date.getDate(); else var newdate_date = date.getDate();
			$(".js_date_picker").val(newdate_month + '/' +newdate_date+ '/'+date.getFullYear());
			$("#end_date_hide").val(newdate_month + '/' +newdate_date+ '/' +date.getFullYear());
			$("#start_time").val(start_time);
			$(".end_time").val(end_time);
			
			//Flat red color scheme for iCheck
			$('input[type="radio"].flat-red').iCheck({radioClass: 'iradio_flat-green'});
			$("#weekCal").weekLine();
			$("select.select2.form-control").select2();
        }
    });
});

		
		
// To set Validation in events form
$(document).on('click','.event-info-form', function(){
			 $('#js-bootstrap-validator')
            .bootstrapValidator({
                message: 'This value is not valid',
                        excluded: ':disabled',
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: 'glyphicon glyphicon-refresh'
                },
		fields: {
			title: {
				message: '',
				validators: {
					notEmpty: {
						message: 'This field is required and can\'t be empty'
					},
					regexp: {
						regexp: /^[a-z\d\-_\s]+$/i,
						message: 'Special characters are not allowed'
					}
				}
			},
			start_date: {
				message: '',
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'This value is not a date'
					},
					callback: {
						message: 'Date must be Before End date',
						callback: function (value, validator) {
							var m = validator.getFieldElements('end_date').val();
							$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'end_date');
							var n = value;
							if(m != '') {
								return (n=='')? false : true;
							 } 
							 else  return true;
						}
					}
				}
			},
			end_date: {
				message: '',
				trigger: 'change keyup',
				validators: {
					date: {
						format: 'MM/DD/YYYY',
						message: 'This value is not a date'
					},
					callback: {
						message: '',
						callback: function (value, validator) {
							var m = validator.getFieldElements('start_date').val();
							var n = value;
							var y = new Date(n);
							if(n != '' && y !="Invalid Date") {   
								var getdate = daydiff(parseDate(m), parseDate(n));
								var reminder_days = $('.event-info-form').find('[name="reminder_type"]:checked').val();
								if(getdate < 0){
									return {
										valid: false,
										message: 'The date is not after start date'
									};
								}
								else if(reminder_days =="one-time"){
										return true;
								}
								else if(reminder_days =="repeat"){
									var repeated_by = $('.event-info-form').find('[name="repeated_by"]').val();	
									var x = new Date(m);
									if(repeated_by =="Weekly"){
										var week_date = new Date(x.getFullYear()+","+(x.getMonth()+1)+","+ (x.getDate()+6));
										if(y <= week_date){ 
											return {
													valid: false,
													message: 'This date is not 1 week after start date'
												};
										}
										return true;
									}
									if(repeated_by =="Monthly"){
										var month_date = new Date(x.getFullYear()+","+(x.getMonth()+2)+","+ x.getDate());
										if(y <= month_date){ 
											return {
													valid: false,
													message: 'This date is not 1 month after start date'
												}; 
										}
										return true;
									}
									if(repeated_by =="Yearly"){
										var year_date = new Date((x.getFullYear()+1)+","+(x.getMonth()+1)+","+x.getDate());
										if(y <= year_date){ 
											return {
													valid: false,
													message: 'This date is not 1 year after start date'
												}; 
										}
										return true;
									}
									return true;
								}
							}
						}
					}
				}
			},
			reminder_days: {
				trigger: 'change keyup',
				validators: {
					callback: {
						message: 'Select remind days',
						callback: function(value, validator, $field) {
							var repeated_by = $('.event-info-form').find('[name="repeated_by"]').val();
							var reminder_days = $('.event-info-form').find('[name="reminder_days"]').val();
							return ((!reminder_days)&&(repeated_by !="Weekly")) ? true : (value !== '');                           
						}
					}                                
				}
			},
			repeated_by: {
				trigger: 'change keyup',
				validators: {
					callback: {
						message: 'This field is required and can\'t be empty',
						callback: function(value, validator, $field) {
							var repeated_by = $('.event-info-form').find('[name="repeated_by"]').val();
							var reminder_type_repeat = $('.event-info-form').find('[name="reminder_type_repeat"]').val();
							return (reminder_type_repeat =="on" && !repeated_by) ?(value !== '') : true;                           
						}
					}                                
				}
			},
			reminder_date: {
				validators: {
					callback: {
						message: 'Select reminder date',
						callback: function(value, validator, $field) {
							var reminder_date = $('.event-info-form').find('[name="reminder_date"]').val();
							var repeated_by = $('.event-info-form').find('[name="repeated_by"]').val();
							return ((!repeated_by)&&(reminder_date =="Monthly" || reminder_date =="Yearly")) ? true : (value !== '');                           
						}
					}                                
				}
			},
			description: {
				message: '',
				trigger: 'change keyup',
				validators: {
					notEmpty: {
						message: 'This field is required and can\'t be empty'
					}
				}
			}
		}
	}).on('success.form.bv', function(e) {
			// Prevent form submission
			e.preventDefault();
			var form_submit = $('.event-info-form #form_submit').val();
			var form_type = $('.event-info-form #form_type').val();
			var data = $(".event-info-form").serialize();//only input
			if(form_type == "create") var url =api_site_url+'/profile/calendar/event/create';
			else if(form_type == "update") {
				var id =$('.event-info-form #event_id').val();
				var url =api_site_url+'/profile/calendar/event/update/'+id;
			}
			if(form_submit == "true"){
				$('#form_submit').val('false');
				$.ajax({
					type : 'POST',
					url  : url,
					data : data,
					success :  function(result)
					{ 
						if(result){
							window.location.href='';
						}
					}
				});
			}
	});
	function daydiff(first, second) {
		return Math.round((second-first)/(1000*60*60*24));
	}


	function parseDate(str) {
		var mdy = str.split('/')
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
});