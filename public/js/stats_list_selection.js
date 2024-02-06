/*** Get the stats details function starts ***/ 
$(document).on('click','.js_stats_select_option', function(){
	var stats_name= $(this).text().trim(); //Stats name
	var position= parseInt($(this).parents("div .js_position_count").attr("data-index"))+1; //Stats position
	var module_name= $("#js_page_name").val(); //module name
	var csrf_token = $('#csrf_token').val();
	var formData  = "stats_name="+stats_name+"&position="+position+"&module_name="+module_name;
	ListSelection(formData);
});
/*** Get the stats details function Ends ***/ 

/*** Get the stats updated icon function starts ***/ 
function ListSelection(formData) {
	$.ajax({
		url		: api_site_url+'/stats/listchange/'+formData,
		type 	: 'GET',
		success: function(msg){ 
			$(".js_update_stats").html(msg);
			$.AdminLTE.boxWidget.activate();
			var alert_msg = $("#js_message").val().trim();
			if(alert_msg.length >0)
				js_alert_popup(alert_msg);
		}
	})
}
/*** Get the stats updated icon function Ends ***/