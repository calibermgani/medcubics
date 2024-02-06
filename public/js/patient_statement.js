$(document).on('ifChecked change','input[name="bulkstatement"]', function () {
	if($(this).val()==1) {
		$('.js_cycle').addClass('show').removeClass('hide');
	} else {
		$('.js_cycle').addClass('hide').removeClass('show');
		$('.js_statmentcycle').addClass('hide').removeClass('show');
		$(this).parents('.form-group').next().find('.select2').select2("val","All");
		$('.js_statmentcycle').find('.select2').select2("val","");
		//cyclevalidate('All');
		/*
		//alert($(this).parents('.form-group').next().find('.select2').select2("val"));
		*/
	}
});

if($('select[name="statementcycle"]').length>0) {
	if($('select[name="statementcycle"]').val()!='') {
		if($('select[name="statementcycle"]').val() != 'All'){
			var cycleval = $('select[name="statementcycle"]').val().toLowerCase();
			$('.js_'+cycleval+'_box').addClass('show').removeClass('hide');
		}
	}
}

$(document).on('change','select[name="statementcycle"]', function () {
	$('.js_statmentcycle').addClass('hide').removeClass('show');
	if($(this).val() != 'All'){
		var cycleval = $(this).val().toLowerCase();
		$('.js_'+cycleval+'_box').addClass('show').removeClass('hide');		
		cyclevalidate(cycleval);
	}	
});

function cyclevalidate(fldValue){
	try{
		if(fldValue != 'All'){
			if(fldValue == 'billcycle')
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'billcycleweek1[]');	
		/*	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'billcycleweek2[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'billcycleweek3[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'billcycleweek4[]');	*/
			if(fldValue == 'facility')
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'facilityweek1[]');	
		/*	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'facilityweek2[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'facilityweek3[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'facilityweek4[]');	*/
			
			if(fldValue == 'provider')			
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'providerweek1[]');	
		/*	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'providerweek2[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'providerweek3[]');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'providerweek4[]');	*/
			if(fldValue == 'account') {
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'fromaccountweek1');	
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'toaccountweek1');	
			}
		/*	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'fromaccountweek2');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'toaccountweek2');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'fromaccountweek3'); 
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'toaccountweek3');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'fromaccountweek4');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'toaccountweek4');	*/
		}
	} catch(err){
		// console.log("Error "+err);
	}
}

$(document).on('ifToggled click change','input[name="paymentmessage"]:checked', function () {
	if($(this).val()==1) {
		$('.js_message_box').addClass('show').removeClass('hide');
	} else {
		$('.js_message_box').addClass('hide').removeClass('show');
		$('.js_message_box').find('textarea').val('');
	}
	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'paymentmessage_1');	
});	 

$(document).on('click','a[data-target="#patient_statement_modal"]', function () {
	var target_url = $(this).data('url');
	$("#patient_statement_modal .modal-body").load(target_url, function(){
		//callicheck();
			
		if($('#check_payable_address_id').val()!=''){
			var unique = $('#check_payable_address_id').val();
			$('[data-id="'+unique+'"]').prop('checked', true); 
			//$('input.flat-red').iCheck('update');
		}
		
		$(document).on('ifToggled click change','input[name="collectaddress"]:checked', function () {
			
			var uniqueid = $(this).data('id');
			$('#check_payable_address_id').val(uniqueid);
			
			var checkpayaddress = $(this).closest('.form-group');
			var address1 = checkpayaddress.find('.address1').text();
			var address2 = checkpayaddress.find('.address2').text();
			var city = checkpayaddress.find('.city').text();
			var state = checkpayaddress.find('.state').text();
			var zip5 = checkpayaddress.find('.zip5').text();
			var zip4 = checkpayaddress.find('.zip4').text();
			
			$('#check_add_1').val(address1);
			$('#check_add_2').val(address2);
			$('#check_city').val(city);
			$('#check_state').val(state);
			$('#check_zip5').val(zip5);
			$('#check_zip4').val(zip4);
			
			$('#patient_statement_modal').modal('hide');
			$( ".js-address-check" ).trigger( "blur" );
			
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_add_1');	
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_city');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_state');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_zip5');
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_zip4');
		});	
	});
});	

$(document).on('keyup change','#patient_search',function() {  
	if($(this).val().trim().length >2) {
		// $('.js_indpatientlist').html(''); 
	}
});

$(document).on('click','.js_individual_go',function() {
	$('#js-bootstrap-validator').bootstrapValidator('validate');		
	var patient_search = $('#patient_search').val();
        //Sent the json data 
    patient_search_key = patient_search; 
	patient_search = patient_search.replace(/[^a-zA-Z0-9]/g, '');
	
	if(patient_search.length>2){
		$('.js_loading').removeClass('hide').addClass('show');
		$('.js_indpatientlist').html('');
		$.ajax({
			type: "GET",
	        data: { "patient_search_key":patient_search_key},
			url: api_site_url+'/individualpatientlist/'+patient_search,	
			success: function(result){
				$('.js_loading').removeClass('show').addClass('hide');
				$('.js_indpatientlist').html(result);
				
				$.AdminLTE.boxWidget.activate(); 
				$('#patientstatement').dataTable( {
					"bDestroy": true,
					"columnDefs": [{ "orderable": false, "targets": -1 }]
				});
				$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'patient_search');	
			}
		});	
	}
});

$(document).on('click','.js_add_row_individual',function() {	
	var patientid = $(this).data('id');
	var buttonlist = $('.indbuttonlist'+patientid).html();
	$(this).html('<i class="fa fa-minus"></i>');
	$(this).removeClass('js_add_row_individual');
	$(this).addClass('js_remove_row_individual');
	$(this).closest('tr').after('<tr role="row" class="odd"><td colspan="8">'+buttonlist+'</td></tr>').slideDown(1200);
	$('#patientstatement').dataTable();		
});	

$(document).on('click','.js_remove_row_individual',function() {
	$(this).html('<i class="fa fa-plus"></i>');
	$(this).removeClass('js_remove_row_individual');
	$(this).addClass('js_add_row_individual');
	$(this).closest('tr').next().remove();
	$('#patientstatement').dataTable();
});	

$(document).on('click','.js_submit_type',function(e) {
	
	var type = $(this).data('name');
	var patientid = $(this).data('id');
	var unique = $(this).data('unique');
	var module = $(this).data('module');	
	var bulkobj = $(this);
	
	// Get payment message in bulk statement
	var get_message = '';
	if($('textarea[name=paymentmessage_1]').val() != undefined)
	{
		var get_message = '::'+$('textarea[name=paymentmessage_1]').val();
	}
	
	$('.js_loading'+unique).removeClass('hide');
	$('.js_hide'+unique).addClass('hide');
	
	$.ajax({
		type: "GET",
		url: api_site_url+'/individualstatementtype/'+patientid+'/'+type+get_message,	
		success: function(result){
			$('.js_loading'+unique).addClass('hide');
			$('.js_hide'+unique).removeClass('hide');
			if(isJson(result)) {
				var obj = jQuery.parseJSON(result);
					
				if(obj.status == 'failure'){
				// change js_alert_popup to js_sidebar_notification 
				// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh					
					js_sidebar_notification('failure',obj.msg);
					$('#patientstatement_model').css('z-index','999');
				}
			
				if(obj.status == 'success') {
					if(type == 'preview'){
						var urlpage = obj.msg;
						setTimeout(function() {
							window.open(urlpage);
						}, 5);
					}
					if(type == 'sendstatement' || type == 'sendcsvstatement' || type == 'sendxmlstatement'){
						if(module == 'bulk') {
							bulkobj.parents('tr').remove();
						}
						//window.location = api_site_url+'/individualstatementdownload/'+obj.filename+'/'+patientid+'/'+obj.msg;
						var urlpage = obj.msg;
						setTimeout(function() {
							if(type == 'sendxmlstatement') {
								var file_path = urlpage;
								var a = document.createElement('A');
								a.href = urlpage;
								a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
								document.body.appendChild(a);
								a.click();
								document.body.removeChild(a);
							} else {
								window.open(urlpage);
							}						
						}, 5);
					}
					if(type == 'emailstatement'){
						if(module == 'bulk') {
							bulkobj.parents('tr').remove();
						}
						// change js_alert_popup to js_sidebar_notification 
						// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh
						js_sidebar_notification('success',obj.msg);
						$('#patientstatement_model').css('z-index','999');
					}	
				}
			} else {
				// change js_alert_popup to js_sidebar_notification 
				// Revision 1 - Ref: MR-2647 06 Augest 2019: Pugazh
				js_sidebar_notification('failure',"Some thing went wrong. Please try after some times");
			}
		}
	});		
});

$(document).on('click','.js_bulkstatement',function(e) {
	e.preventDefault();
	var i = 0;
	$('.js_sub_checkbox:checked').each(function(){ 
		$('.js_remove'+$(this).val()).remove();
		i++;
	});
	
	if(i == 0){
		$('#patientnote_model').find('.text-center').html('Select any one record');
		$('#patientnote_model').modal('show');
	} else {
		$(".js_sendtype").val($(this).val());
		//$(".js_patientids").val('');
		$( "#js_bulkstatement_form").submit();
	}
	
	$('.js_patientids').val('');
	var oTable = $('#bulkstatementsearch').dataTable();	
    $(oTable.fnGetNodes()).find('.js_sub_checkbox:checkbox').each(function () {
    	$(this).attr('checked', false);
	});
	setTimeout(function() {
		if ($('.js_sub_checkbox:checked').length == $('.js_sub_checkbox').length ){
			$('.js_bulkpatientlist').html('No Patient Available');
			$('.js_button').hide();			
		}
	}, 100);
	
});	

$(document).on('ifToggled change','#js_select_all', function () {
	$(".js_sub_checkbox").prop('checked', $(this).prop("checked"));
	
	var getpatientids = $(".js_sub_checkbox:checked").map( function () {return this.value;}).get().join(",");
	$('.js_patientids').val(getpatientids);
	//$('input.flat-red').iCheck('update');
	$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'patient_ids');
});

$(document).on('ifToggled change','.js_sub_checkbox', function () {
	if(false == $(this).prop("checked")){ //if this item is unchecked
        $("#js_select_all").prop('checked', false); //change "select all" checked status to false
    }
	if ($('.js_sub_checkbox:checked').length == $('.js_sub_checkbox').length ){
        $("#js_select_all").prop('checked', true);
    }
	
	var ischecked= $(this).is(':checked');	
	if(!ischecked) {
		//console.log("Unchecked: "+$(this).val());
		var str = $('.js_patientids').val();
		var strArray = str.split(',');
		for (var i = 0; i < strArray.length; i++) {
			if (strArray[i] === $(this).val()) {
				strArray.splice(i, 1);
			}
		}
		//console.log(strArray);
		$('.js_patientids').val(strArray);
	} else {
		//console.log("Checked :"+$(this).val());

		// Selected check box value include hidden
		/*
		var oTable = $('#bulkstatementsearch').dataTable();
		var selected = new Array();
		$(oTable.fnGetNodes()).find('.js_sub_checkbox:checkbox').each(function () {
			$this = $(this);
			if($(this).is(":checked")){
				selected.push($this.val());
			}
		});	
		// convert to a string
		var getpatientids = selected.join();		
		*/
		var getpatientids = $(".js_sub_checkbox:checked").map( function () {return this.value;}).get().join(",");
		var prePatient = $('.js_patientids').val();
		if(prePatient != '') {
			//getpatientids = prePatient+","+getpatientids;
			getpatientids = Array.from(new Set([prePatient, getpatientids].join(',').split(','))).join(',');
		}
		//console.log("pat IDS "+getpatientids);
		$('.js_patientids').val(getpatientids);
	}
	//console.log("Final: "+$('.js_patientids').val() );
	//$('input.flat-red').iCheck('update');
	$('#js-bootstrap-validator1').bootstrapValidator('revalidateField', 'patient_ids');	
});


// Patient module.
$(document).on('click','.js-patientstatement',function() {
	var patientid = $(this).data('patientid');
	$('.js_submit_type').prop('disabled', false);
	$.ajax({
		type: "GET",
		url: api_site_url+'/individualpatientdetails/'+patientid,	
		success: function(result){
			var obj = jQuery.parseJSON(result);
			
			if(obj.settings == 0)
			{
				$("#patientstatement_model .js_patientbalance").html(show_patient_balance+' '+obj.balance+'<br>'+unknownsettingsmsg);	
				$('#patientstatement_model').find('.modal-footer').hide();
			}
			else
			{
				$("#patientstatement_model .js_patientbalance").html(show_patient_balance+' '+obj.balance);	
				if(obj.email==''){
					$('.js_emailstatement').hide();
				}
				$('.js_submit_type').attr('data-id',patientid);
			}			
		  	$("#patientstatement_model").modal({show: 'false', keyboard: false});
		}
	});	
});

$(document).on('click','.js_reset',function() {  
	$('#js-bootstrap-validator')[0].reset();
	$('.js_indpatientlist').html('');
	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'patient_search');		
});

$(document).on('click','#js_get_statementhistory',function(e) {  
	e.preventDefault();
	var geturl = $(this).attr('href');
	$.ajax({
		type: "GET",
		url: geturl,	
		success: function(result){
			$('#patient_statement_modal .modal-md');
			$('#patient_statement_modal .modal-title').text('Statement History List');
			$('#patient_statement_modal .modal-body').html(result);
			$('#example1').dataTable();	
			$('#patient_statement_modal').modal();
			$('#patient_statement_modal .js_insurance_search_popup').removeClass('modal-md').addClass('modal-lg');
		}
	});	
});

//"Bulk statement datatable set sorting."
if($('#bulkstatementsearch').length > 0) {
	/*
	var table = $("#bulkstatementsearch").DataTable({
		"order": [1, 'asc'],
		"columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": -1 }]
	});
	table.on( 'draw', function () {
		var body = $( table.table().body() );
			body.unhighlight();
		body.highlight( table.search() );  
	});
	*/
}