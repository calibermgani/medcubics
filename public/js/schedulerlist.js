/**** Start to search list ***/ 

$(document).on('change', '.js_filter_search',function (e) {
	ajaxFunction();
});

/**** End to search list ***/

/**** Changing the view function start ***/ 

$(document).on('click', '.js_view_port',function() {
	$('.js_view_port').removeClass("active_list");
	$(this).addClass("active_list");
	ajaxFunction();
});

/**** Changing the view function end  ***/

/*** Start to replace new selected value in export url ***/
$(document).on('click', '.menu li a',function(e) {
	var href = $(this).attr('data-url');
	var option = $(this).attr('data-option');
	var patient_id = $('[name="patient_id"]').val();
	var provider_id = $('[name="provider_id"]').val();
	var facility_id = $('[name="facility_id"]').val();
	var status = $('[name="status"]').val();
	var date = $(".js_filter_search:last").val();
	var pro_id 		= (provider_id =='' || provider_id ==null) ? "empty" : provider_id;
	var fac_id 		= (facility_id =='' || facility_id ==null) ? "empty" : facility_id;
	var cur_date 	= (date =='' || date ==null) ? "empty" : date;
	var pat_id 		= (patient_id =='' || patient_id ==null) ? "empty" : patient_id;
	var cur_status 	= (status =='' || status ==null) ? "empty" : status;
	new_href = href+"/"+option+"/"+pro_id+"/"+fac_id+"/"+cur_date+"/"+pat_id+"/"+cur_status;
	$(this).attr('href',new_href);
});
/*** End to replace new selected value in export url ***/

/*** Ajax fuction starts ***/
function ajaxFunction() {
	$('.js_add_detail').addClass("hide");
	$('#js_loading_image').removeClass("hide");
	
	var list_view = $('.active_list').attr("id");
	
	var sel_filter_val = $('.js_filter_search').map(function () { return this.value; }).get();
	var fieldHTML = "<input type='hidden' value='"+list_view+"' name='list_view'>";
	$('select.js_filter_search').each(function (i,val) {
		var keys = $(this).find("option:first").text();
		fieldHTML = fieldHTML + "<input type='hidden' value='"+sel_filter_val[i]+"' name='"+keys+"'>";
	});
	$(".all_values_input").append(fieldHTML);
	var data = $('[name=all_values]').serialize();//only input
	$(".all_values_input").html('');
	$.ajax({
        url: api_site_url+'/scheduler/keywordsearch',
		type: "POST",
		data: data,
		success: function(response){
			if(response !=""){
				$('#js_loading_image').addClass("hide");
				$('.js_add_detail').html(response).removeClass("hide");
				if($(".table-responsive #example1").length>0)
					$('#example1').dataTable();
			}
        }
    }); 
}
/*** Ajax fuction ends ***/