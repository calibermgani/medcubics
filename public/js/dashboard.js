//charges dashboard Code starts here
$(document).on("change", "#js-line-chart", function(){
	var line_val  = $(this).val(); //alert(line_val);
	$(".js-line-chart").slideUp("fast");
    $("#js-"+line_val).slideDown("slow");
});

$(document).on("change", "#js_choose_performance", function(){
	type = $(this).val();
	url = api_site_url + '/dashboard/charges/performance/' + type ;
	var title = "Billing Provider";
	if(type == "billing_provider_id") 
		title = "Billing Provider";
	 else if(type == "rendering_provider_id")
		title = "Rendering Provider";
	 else 
		title = "Facility";
	$.get(url, function(data){
		$('.js_title').html(title)
		$(".js_append_performancedata").html(data);
	})
})