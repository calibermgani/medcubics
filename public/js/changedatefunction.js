// Charge and payments created date change function starts here
$(".js-change-date").datepicker({
	maxDate: 0,
	changeMonth: true,
	changeYear: true,
	yearRange:'-1:+0',
 });

$(document).on('change', ".js-change-date", function(){
	changedatefunction($(this));
});

function changedatefunction(curr_val){
	datatype = $(curr_val).attr("data-type");
	dataval = $(curr_val).val();
	dataid = $(curr_val).attr("data-id");
	newdate = isFutureDate(dataval); 
	dataval = btoa(dataval);	
	url = api_site_url+"/postchangedate/"+datatype+"/"+dataid+"/"+dataval; 
	if(newdate) {
		js_alert_popup("Future date not allowed");
		return false;
	} else{
		$("#js_wait_popup").modal("show");
		$.get(url, function(data){			
		    $('.js-append-data').html(data);
			$("#js_wait_popup").modal("hide");	
			$(".js-change-date").datepicker({
				maxDate: 0,
				changeMonth: true,
				changeYear: true,
				yearRange:'-1:+0',
			 });
		});	
	}

}
function isFutureDate(idate){ 
	var month = idate.substring(0, 2);
	var date = idate.substring(3, 5);
	var year = idate.substring(6, 10);
	var dateToCompare = new Date(year, month - 1, date);
	var currentDate = new Date(); 
	if (dateToCompare > currentDate) {
		return true;       
	}
	else {
		return false;
		
	}                 
}

// Charge and payments created date change function ends here