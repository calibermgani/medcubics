<script type="text/javascript">
    jQuery("#monday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "monday", "monday_forenoon");
        }, callback: function() {
		//console.log($(this));
            slideIt('monday','monday_forenoon');
        }
    });

    jQuery("#tuesday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "tuesday", "tuesday_forenoon");
        }, callback: function() {
            slideIt("tuesday", "tuesday_forenoon");
        }
    });
    jQuery("#wednesday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "wednesday", "wednesday_forenoon");
        }, callback: function() {
            slideIt("wednesday", "wednesday_forenoon");
        }
    });
    jQuery("#thursday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "thursday", "thursday_forenoon");
        }, callback: function() {
            slideIt("thursday", "thursday_forenoon");
        }
    });
    jQuery("#friday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "friday", "friday_forenoon");
        }, callback: function() {
            slideIt("friday", "friday_forenoon");
        }
    });
    jQuery("#saturday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "saturday", "saturday_forenoon");
        }, callback: function() {
            slideIt("saturday", "saturday_forenoon");
        }
    });
    jQuery("#sunday").slider({from: 0, to: 719, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinforenoon(value, "sunday", "sunday_forenoon");
        }, callback: function() {
            slideIt("sunday", "sunday_forenoon");
        }
    });
    jQuery("#monday-af").slider({from: 720, to: 1439, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value){
            return SetTimeinafternoon(value, "monday-af", "monday_afternoon");
        }, callback: function() {
            slideIt("monday-af", "monday_afternoon");
        }
    });
    jQuery("#tuesday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "tuesday-af", "tuesday_afternoon");
        }, callback: function() {
            slideIt("tuesday-af", "tuesday_afternoon");
        }
    });
    jQuery("#wednesday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "wednesday-af", "wednesday_afternoon");
        }, callback: function() {
            slideIt("wednesday-af", "wednesday_afternoon");
        }
    });
    jQuery("#thursday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "thursday-af", "thursday_afternoon");
        }, callback: function() {
            slideIt("thursday-af", "thursday_afternoon");
        }
    });
    jQuery("#friday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "friday-af", "friday_afternoon");
        }, callback: function() {
            slideIt("friday-af", "friday_afternoon");
        }
    });
    jQuery("#saturday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "saturday-af", "saturday_afternoon");
        }, callback: function() {
            slideIt("saturday-af", "saturday_afternoon");
        }
    });
    jQuery("#sunday-af").slider({from: 720, to: 1440, step: 15, dimension: '', scale: [], limits: false, skin: "round_plastic", calculate: function (value) {
            return SetTimeinafternoon(value, "sunday-af", "sunday_afternoon");
        }, callback: function() {
            slideIt("sunday-af", "sunday_afternoon");
        }
    });

    function SetTimeinforenoon(value, id, name) {
        var hours = Math.floor(value / 60);
        var mins = (value - hours * 60);
        var asign_val = $("#" + id).val();
        $("input[name='" + name + "']").val(asign_val);
        var second_value = asign_val.split(";");
        if (second_value[0] < 720 && second_value[1] == 720)
            $("input[name='" + name + "']").val(second_value[0] + ";" + 719);
        else if (second_value[0] == 720 && second_value[1] < 720)
            $("input[name='" + name + "']").val(719 + ";" + second_value[1]);
        else if (second_value[0] == 720 && second_value[1] == 720)
            $("input[name='" + name + "']").val(719 + ";" + 719);
        if (hours == 12 && mins == 00)
            return 11 + ":" + 59;
        else
            return (hours < 10 ? "0" + hours : hours) + ":" + (mins == 0 ? "00" : mins);
    }

    function SetTimeinafternoon(value, id, name) {
		var value = value - 720;
        var hours = Math.floor(value / 60);
        var mins = (value - hours * 60);
        var asign_val = $("#" + id).val();
        $("input[name='" + name + "']").val(asign_val);
        var second_value = asign_val.split(";");
        if (second_value[0] < 1440 && second_value[1] == 1440)
            $("input[name='" + name + "']").val(second_value[0] + ";" + 1439);
        else if (second_value[0] == 1440 && second_value[1] < 1440)
            $("input[name='" + name + "']").val(1439 + ";" + second_value[1]);
        else if (second_value[0] == 1440 && second_value[1] == 1440)
            $("input[name='" + name + "']").val(1439 + ";" + 1439);
        if (hours == 12 && mins == 00)
            return 11 + ":" + 59;
        else
            return (hours < 10 ? "0" + hours : hours) + ":" + (mins == 0 ? "00" : mins);
    }

    function slideIt(selID, selFor){
	    if ($(".facility_id_val").length > 0) {
        var facility_id = $('.facility_id_val').val();
        var select_time = $("#"+selID).slider("calculatedValue");
		/* console.log($("#"+selID).slider());
		console.log($("#"+selID).slider("value"));
		
		console.log($(this)); */
		console.log(select_time)
		var spl_select_time = select_time.split(";");
		var spl_stVal = spl_select_time[0];
		var spl_endVal = spl_select_time[1];
		if((select_time != '00:00;00:00') &&(spl_stVal == spl_endVal)){
			var old_slider_val =  $("#"+selID).attr("alt");
			var result = old_slider_val.split(";");
            var stVal = result[0];
            var endVal = result[1];
			$("#"+selID).slider("value", stVal, endVal);			
			$("#claims_error_model").find(".modal-body .modal-desc").addClass("text-center med-green font600");
            $("#claims_error_model").find(".modal-body .modal-desc").html("Same start time & End time only 00:00");
            $("#claims_error_model").modal("show");
			return false;
		}
				
		$.ajax({
                type: "POST",
                url: api_site_url+'/facilityappoinemtcheck/'+facility_id+'/'+select_time,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "facility_id": facility_id,
                    "select_time": select_time,
                    "schedule_day": selFor
                },
                dataType: 'json',
                success: function(resp){
                    //console.log(resp.status)
                    if(resp.status == 'error'){
                        var old_slider_val =  $("#"+selID).attr("alt");
						//current DB store value
                        var result = old_slider_val.split(";");
                        var stVal = result[0];
                        var endVal = result[1];
						//selected time value
						var select_time = $("#"+selID).slider("value");
						var curr_time = select_time.split(";");
                        var curStTime = curr_time[0];
                        var curEndTime = curr_time[1];
                        //POPup msg show if Already Appointment is created
                        /* $("#claims_error_model").find(".modal-body .modal-desc").addClass("text-center med-green font600");
                        $("#claims_error_model").find(".modal-body .modal-desc").html(resp.msg);
                        $("#claims_error_model").modal("show"); */
                            js_sidebar_notification('error',resp.msg);
						if(resp.data == 'end_time'){
							$("#"+selID).slider("value", curStTime, endVal);							
						} else if(resp.data == 'start_time') {	
							//console.log(stVal);	console.log(curEndTime);
							$("#"+selID).slider("value", stVal, curEndTime);
						}	
                    } else if(resp.status == 'error'){
                        // handle success part//$("#monday").slider("value", "50", "500");
                    }                             
                }
            });
        } 
    }
</script>    