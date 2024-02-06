var icd_popup_list = "";

$( "#date_of_service" ).datepicker({
	maxDate: 0,
	yearRange: "-90:+0",
	dateFormat: 'mm/dd/yy',
	changeMonth: true,
	changeYear: true
});

$("#date_of_service").on('change keyup paste', function() {
	inactive_tab_link('icd_tab');
	if($("#date_of_service").val()!=""){
		remove_err_msg('date_of_service_err');
	}
});

$('#providers_id').on('change', function (e) {
	inactive_tab_link('icd_tab');
	if(this.value!=""){
		remove_err_msg('providers_id_err');
	}
});

$('#templates_id').on('change', function (e) {
	var sel_template_id = this.value;
	
	if(sel_template_id=="0"){
		$("#selected_templates_display_part").html('<p class="text-center med-gray font14">No Template selected</p>');
		$("#template_seleted_display").html('');
	}
	else{
		if($('input[type=hidden][name="selected_codes_cpts_arr"]').val()!=''){
			var sel_cpts_vals = $('input[type=hidden][name="selected_codes_cpts_arr"]').val();
		}
		else{
			var sel_cpts_vals = "no";
		}
		$.ajax({
			type 		: 'GET',
			url  		: api_site_url+'/api/superbill_getseletedtemplatedetails/'+sel_template_id+'/'+sel_cpts_vals,
			success 	: function(res){
							$("#selected_templates_display_part").html(res);
							var superbill_name = $("#temp_superbill_name").val();
							//$("#template_seleted_display").html('<span style="background: #4fc5cc; color:#fff; padding: 2px 10px; border-radius: 4px;">Template : '+superbill_name+'</span>');
							$("#template_seleted_display").html('');
						}
		});
	
	}
});

function remove_err_msg(type_err){
	$("#"+type_err).hide();
}

function inactive_tab_link(val_type){
	if(val_type=='icd_tab'){
		document.getElementById('select_procedure_tab').className = 'inactivelink';
		document.getElementById('create_claim_tab').className = 'inactivelink';
	}
	if(val_type=='cpt_tab'){
		document.getElementById('create_claim_tab').className = 'inactivelink';
	}
}

function form_supmit(val_type){
	
	var formdata     = $('form[name="superbillclaim_form"]').serialize();
	
	$.ajax({
			type 		: 'POST',
			url  		: api_site_url+'/api/superbillformvalidation/'+val_type,
			data 		: formdata,
			dataType	: 'json',
			timeout		: 0,
			success 	: function(res){
						var err_arr 	= $.makeArray(res['errors']);
						var no_err_arr 	= $.makeArray(res['no_errors']);
						
						if(err_arr.length>0){
							$.each( err_arr, function( key, value ) {
							  $("#"+value).show();
							});
						}
						if(no_err_arr.length>0){
							$.each( no_err_arr, function( key1, value1 ) {
							  $("#"+value1).hide();
							});
						}
							if(res['icd_tab_error']=='yes'){
								window.location.href="#select_icd";
								document.getElementById('select_icd_tab').className = '';
							}
							else if(res['cpt_tab_error']=='yes' && val_type!='icd_tab'){
								window.location.href="#select_procedure";
								document.getElementById('select_procedure_tab').className = '';
							}
							else if(res['claim_tab_error']=='yes' && val_type!='icd_tab' && val_type!='procedure_tab'){
								window.location.href="#create_claim";
								document.getElementById('create_claim_tab').className = '';
							}
							else{
								
								if(val_type=='icd_tab'){
									var dos_seleted_display = $("#date_of_service").val();
									//dos_seleted_display = dos_seleted_display.replace(/\//g, '-');
									$("#dos_seleted_display").html(dos_seleted_display);
									$("#dos_seleted_display1").html(dos_seleted_display);
									$("#selected_templates_display_part").html('<p class="text-center med-gray font14">No Template Selected</p>');
									$('input[type=hidden][name="temp_popup_icds_val"]').val('');
									$('input[type=hidden][name="temp_popup_cpt_val"]').val('');
									$('input[type=hidden][name="selected_codes_cpts_arr"]').val('');
									$("#selected_cpt_codes_display_part").html('');
									$("#no_cpt_codes_display_part").show();
									$('.icds_for_cpts').remove();
									$('input[name="cpt_codes_seleted[]"]:checkbox').prop("checked", false);
									$('input[name="cpt_codes_seleted[]"]:checkbox').iCheck('update');
									$('input[name="imo_search_cpts[]"]:checkbox').prop("checked", false);
									$('input[name="imo_search_cpts[]"]:checkbox').iCheck('update');
									$("#icd_imo_search_part").html('');
									$("#cpt_imo_search_part").html('');	
									$.ajax({
										type 		: 'POST',
										url  		: api_site_url+'/api/superbill_getseletedproviderdetails/'+$("#providers_id").val(),
										data 		: formdata,
										dataType	: 'json',
										timeout		: 0,
										success 	: function(res){
														$("#providername_seleted_display").html(res['provider_details']['provider_name'].substr(0, 11)+'&nbsp;'+res['provider_details']['degrees']['degree_name']);
														$("#providername_seleted_display1").html(res['provider_details']['provider_name'].substr(0, 11)+'&nbsp;'+res['provider_details']['degrees']['degree_name']);
														
														var newOptions = res['superbill_list'];
														var $el = $("#templates_id");
														$el.empty();
														$.each(newOptions, function(value,key) {
															$el.append($("<option></option>")
															.attr("value", value).text(key));
															if(value=="0"){
																$("#templates_id").select2("val", value); 
															}
														});
														icd_popup_list = res['icd_popup_list'];
										}
				
									});
									
									window.location.href="#select_procedure";
									document.getElementById('select_procedure_tab').className = '';
									$('html, body').animate({
										scrollTop: $("#select_procedure_tab").offset().top
									}, 200);
								}
								else if(val_type=='procedure_tab'){
									
									$.ajax({
										type 		: 'POST',
										url  		: api_site_url+'/api/superbill_getcreatebilltab_details',
										data 		: formdata,
										success 	: function(res){
													$("#create_bill_main_list_part").html(res);
										}
									});
									
									$("#icd_imo_search_part").html('');
									$("#cpt_imo_search_part").html('');	
									window.location.href="#create_claim";
									document.getElementById('create_claim_tab').className = '';
									$('html, body').animate({
										scrollTop: $("#create_claim_tab").offset().top
									}, 200);
									
								}
							}
			}
	});

}

$(document).on('ifToggled', "input[name='existing_icds[]']",function () {
	inactive_tab_link('icd_tab');
	if ($(this).prop('checked')==true){ 
		icd_code_selected($(this).val());
	}
	else{
		remove_seleted_icds($(this).val());
	}
});

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

function remove_seleted_icds(remove_id){
	inactive_tab_link('icd_tab');
	$('input[name="existing_icds[]"]').attr("disabled", false);
	var sel_icds = $( "#selected_codes_ids_arr" ).val();
	var sel_icds_arr = sel_icds.split(',');
	sel_icds_arr.remove(remove_id);
	sel_icds = sel_icds_arr.join();
	sel_icds = sel_icds.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="selected_codes_ids_arr"]').val(sel_icds);
	
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/api/get_seleted_icd_details/'+remove_id,
			data : '',
			dataType: 'json',
			timeout: 0,
			processData: false,
			contentType: false,
			success :  function(result){
						var remove_code_val = result['icd_details']['icd_code'].replace('.', '_');
						$('input[name="existing_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').prop("checked", false);
						$('input[name="existing_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').iCheck('update');
						$('input[name="imo_search_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').prop("checked", false);
						$('input[name="imo_search_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').iCheck('update');
			}
	});
	$(".select_icd_code_"+remove_id).remove();
	if(sel_icds_arr.length==0){
		$("#no_codes_display_part").show();
	}
}

function edit_seleted_cpts(edit_id){
	
	var newOptions 		= $.map(icd_popup_list, function(el) { return el });
	var icd_popup_body 	= "";
	var exists_icds		= $('input[type=hidden][name="icd_for_cpt_'+edit_id+'"]').val();
	var exists_icds_arr = exists_icds.split(',');
	var checked			= "";
	
	$.each(newOptions, function(value,key) {
		if(exists_icds_arr.indexOf(''+key['id']+'') != -1){ 
			checked = "checked";
		}
		else{
			checked = "";
		}
		icd_popup_body = icd_popup_body + '<div class="form-group" style="margin-left: 0px; margin-right: 0px;"><div class="col-sm-1"><input type="checkbox" name="popup_icd_temp_ids[]" value="'+key['id']+'" class="chk flat-red" '+checked+'></div><div class="col-lg-9 col-md-9 col-sm-9 ">'+key['short_description']+'</div><div class="col-sm-1">'+key['icd_code']+'</div></div>';
	});
	$('#popup_icd_modal').modal({
		show: 'true'
	});
	icd_popup_body = icd_popup_body + '<script type="text/javascript">$(document).ready(function() {  $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({ checkboxClass: \'icheckbox_flat-green\',radioClass: \'iradio_flat-green\' }); }); </script>';
	$('#popup_icd_modal .modal-body').html(icd_popup_body);
	
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/api/get_seleted_cpt_details/'+edit_id+'::no',
			data : '',
			dataType: 'json',
			success :  function(result){
						$('#popup_cpt_title').html(result['cpt_details']['short_description']);
			}
	});
	$('input[type=hidden][name="temp_popup_cpt_val"]').val(edit_id);
	$('input[type=hidden][name="temp_popup_icds_val"]').val($('input[type=hidden][name="icd_for_cpt_'+edit_id+'"]').val());
}

function icd_code_selected(icd_code){
	
	var sel_icds = $( "#selected_codes_ids_arr" ).val();
	var sel_icds_arr = sel_icds.split(',');
	
	if(sel_icds_arr.length<12){
		
		if($.inArray(''+icd_code+'', sel_icds_arr) === -1){
		
		sel_icds_arr.push(icd_code);
		sel_icds = sel_icds_arr.join();
		sel_icds = sel_icds.replace(/(^,)|(,$)/g, "");
		if(sel_icds!=""){
			remove_err_msg('selected_codes_ids_arr_err');
		}
		
		$('input[type=hidden][name="selected_codes_ids_arr"]').val(sel_icds);
		
		$.ajax({
				type : 'GET',
				url  : api_site_url+'/api/get_seleted_icd_details/'+icd_code,
				data : '',
				dataType: 'json',
				timeout: 0,
				processData: false,
				contentType: false,
				success :  function(result){
							
							$( "#no_codes_display_part" ).hide();
							
							var add_list = '<span class="select_icd_code_'+result['icd_details']['id']+'"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><ul class="cpt-grid no-bottom" style="list-style-type:none; padding:0px; line-height:26px;" id=""><li class=""><table class="table-striped-view"><tbody><tr><td style="width: 1%"></td><td style="width: 79%">'+result['icd_details']['short_description']+'</td><td style="width: 15%" class="med-orange font600">'+result['icd_details']['icd_code']+'</td><td style="width: 10%;"><i class="fa fa-close modal-icon med-gray cur-pointer" data-original-title="Delete" data-toggle="tooltip" data-placement="bottom"  onclick="remove_seleted_icds(\''+result['icd_details']['id']+'\');"></i></td></tr></tbody></table></li></ul></div></span>';
							
							var remove_code_val = result['icd_details']['icd_code'].replace('.', '_');
							$('input[name="existing_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').prop("checked", true);
							$('input[name="existing_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').iCheck('update');
							$('input[name="imo_search_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').prop("checked", true);
							$('input[name="imo_search_icds[]"]:checkbox[data-id="icd_'+remove_code_val+'"]').iCheck('update');
							
							$( "#selected_codes_display_part" ).append(add_list);
				}
		});
		}
	}
	else{
		js_alert_popup("Maximum twelve icd only allowed");
	}
}

$(document).on('ifToggled', "input[name='cpt_codes_seleted[]']",function () {
	
	inactive_tab_link('cpt_tab');
	
	if ($(this).prop('checked')==true){
		
			var newOptions 		= $.map(icd_popup_list, function(el) { return el });
			var icd_popup_body 	= "";
			$.each(newOptions, function(value,key) {
				icd_popup_body = icd_popup_body + '<div class="form-group" style="margin-left: 0px; margin-right: 0px;"><div class="col-sm-1"><input type="checkbox" name="popup_icd_temp_ids[]" value="'+key['id']+'" class="chk flat-red"></div><div class="col-lg-9 col-md-9 col-sm-9 ">'+key['short_description']+'</div><div class="col-sm-1">'+key['icd_code']+'</div></div>';
			});
			$('#popup_icd_modal').modal({
				show: 'true'
			});
			icd_popup_body = icd_popup_body + '<script type="text/javascript">$(document).ready(function() {  $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({ checkboxClass: \'icheckbox_flat-green\',radioClass: \'iradio_flat-green\' }); }); </script>';
			$('#popup_icd_modal .modal-body').html(icd_popup_body);
			
			$.ajax({
					type : 'GET',
					url  : api_site_url+'/api/get_seleted_cpt_details/'+$(this).val()+'::no',
					data : '',
					dataType: 'json',
					success :  function(result){
						$('#popup_cpt_title').html(result['cpt_details']['short_description']);
						$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
						$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
						$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
						$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
					}
			});
			$('input[type=hidden][name="temp_popup_cpt_val"]').val($(this).val());
	}
	else{
		remove_seleted_cpts($(this).val());
	}
	
});

function remove_seleted_cpts(remove_id){
	
	inactive_tab_link('cpt_tab');
	$('input[type=hidden][name="icd_for_cpt_'+remove_id+'"]').remove();
	var sel_cpts = $( "#selected_codes_cpts_arr" ).val();
	var sel_cpts_arr = sel_cpts.split(',');
	sel_cpts_arr.remove(remove_id);
	sel_cpts = sel_cpts_arr.join();
	sel_cpts = sel_cpts.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="selected_codes_cpts_arr"]').val(sel_cpts);
	
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/api/get_seleted_cpt_details/'+remove_id+'::no',
			data : '',
			dataType: 'json',
			timeout: 0,
			processData: false,
			contentType: false,
			success :  function(result){
				$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
				$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
				$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
				$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
			}
	});
	
	$(".select_cpt_code_"+remove_id).remove();
	if(sel_cpts_arr.length==0){
		$("#no_cpt_codes_display_part").show();
	}
}

function cpt_code_selected(cpt_code,popup_temp_icd_ids,selected_cpt_edit){
	
	inactive_tab_link('cpt_tab');
	$.ajax({
			type : 'GET',
			url  : api_site_url+'/api/get_seleted_cpt_details/'+cpt_code+'::'+popup_temp_icd_ids,
			data : '',
			dataType: 'json',
			success :  function(result){
						
						$( "#no_cpt_codes_display_part" ).hide();
						
						var add_cpt_list = '<span class="select_cpt_code_'+result['cpt_details']['id']+'"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><ul class="cpt-grid no-bottom line-height-26 mobile-width" style="list-style-type:none; padding-left:10px;" id=""><li class=""><table class="table-striped-view"><tbody><tr><td style="width: 25%; color: #868686 !important;">'+result['cpt_details']['short_description']+'</td><td style="width: 5%;color: #f07d08 !important; border-right:1px solid #bbeff1; ">'+result['cpt_details']['cpt_hcpcs']+'</td><td style="width: 60%;padding-left:5px; background: rgb(246, 253, 253) none repeat scroll 0px 0px; color:#868686 !important; text-align: left;padding-left: 7px;">'+result['icd_ids_details']+'</td><td style="width: 10%;"><i class="fa fa-edit modal-icon form-cursor med-green" data-original-title="Edit" data-toggle="tooltip" data-placement="bottom" onclick="edit_seleted_cpts(\''+result['cpt_details']['id']+'\');"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle modal-icon med-green" data-original-title="Delete" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;"  onclick="remove_seleted_cpts(\''+result['cpt_details']['id']+'\');"></i></td></tr></tbody></table></li></ul></div></span>';
						if(selected_cpt_edit!="yes"){
							$("#selected_cpt_codes_display_part").append(add_cpt_list);
							$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", true);
							$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
							$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').prop("checked", true);
							$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+result['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
						}
						else{
							$(".select_cpt_code_"+result['cpt_details']['id']).html(add_cpt_list);
						}
			}
	});
}

function popup_icds_reset(){
	$('#popup_icd_check_validation').modal('hide');
	$('input[name="popup_icd_temp_ids[]"]:checkbox').prop("checked", false);
	$('input[name="popup_icd_temp_ids[]"]:checkbox').iCheck('update');
	$('input[type=hidden][name="temp_popup_icds_val"]').val('');
}

function popup_icds_close(){
	$('#popup_icd_check_validation').modal('hide');
	$('input[name="popup_icd_temp_ids[]"]:checkbox').prop("checked", false);
	$('input[name="popup_icd_temp_ids[]"]:checkbox').iCheck('update');
	$('input[type=hidden][name="temp_popup_icds_val"]').val('');
	$('input[type=hidden][name="temp_popup_cpt_val"]').val('');
	$('#popup_icd_modal').modal('hide');
}

$(document).on('ifToggled', "input[name='popup_icd_temp_ids[]']",function () {
	var sel_popup_icds = $( "#temp_popup_icds_val" ).val();
	
	if ($(this).prop('checked')==true){
			$('#popup_icd_check_validation').modal('hide');
			var sel_popup_icds_arr = sel_popup_icds.split(',');
			sel_popup_icds_arr.push($(this).val());
			sel_popup_icds = sel_popup_icds_arr.join();
			sel_popup_icds = sel_popup_icds.replace(/(^,)|(,$)/g, "");
			$('input[type=hidden][name="temp_popup_icds_val"]').val(sel_popup_icds);
	}
	else{
		remove_seleted_popup_icds($(this).val());
	}
});

function remove_seleted_popup_icds(remove_id){
	var sel_popup_icds = $( "#temp_popup_icds_val" ).val();
	var sel_popup_icds_arr = sel_popup_icds.split(',');
	sel_popup_icds_arr.remove(remove_id);
	sel_popup_icds = sel_popup_icds_arr.join();
	sel_popup_icds = sel_popup_icds.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="temp_popup_icds_val"]').val(sel_popup_icds);
}

function popup_icds_save(){
	var popup_temp_cpt_id 	= $('input[type=hidden][name="temp_popup_cpt_val"]').val();
	var popup_temp_icd_ids 	= $('input[type=hidden][name="temp_popup_icds_val"]').val();
	var selected_cpt_edit   = "";
	
	if(popup_temp_icd_ids!=""){
	var sel_cpts 			= $( "#selected_codes_cpts_arr" ).val();
	
	var sel_cpts_arr = sel_cpts.split(',');
	if(sel_cpts_arr.indexOf(''+popup_temp_cpt_id+'') == -1){ 
			sel_cpts_arr.push(popup_temp_cpt_id);
	}
	else{
			selected_cpt_edit = "yes";
	}
	
	sel_cpts = sel_cpts_arr.join();
	sel_cpts = sel_cpts.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="selected_codes_cpts_arr"]').val(sel_cpts);
	$('input[type=hidden][name="icd_for_cpt_'+popup_temp_cpt_id+'"]').remove();
	$('<input>').attr({
		type: 'hidden',
		id: 'icd_for_cpt_'+popup_temp_cpt_id,
		class: 'icds_for_cpts',
		name: 'icd_for_cpt_'+popup_temp_cpt_id,
		value: popup_temp_icd_ids
	}).appendTo('#popup_icd_modal');
	
	cpt_code_selected(popup_temp_cpt_id,popup_temp_icd_ids,selected_cpt_edit);
	
	$('input[name="popup_icd_temp_ids[]"]:checkbox').prop("checked", false);
	$('input[name="popup_icd_temp_ids[]"]:checkbox').iCheck('update');
	$('input[type=hidden][name="temp_popup_icds_val"]').val('');
	$('input[type=hidden][name="temp_popup_cpt_val"]').val('');
	$('#popup_icd_modal').modal('hide');
	$('#selected_codes_cpts_arr_err').hide();
	}
	else{
		$('#popup_icd_check_validation').modal('show');
	}
}

function cpt_icd_change_bill(cpt_id,icd_position,icd_id){
	var changed_icd 	= $('input[type=hidden][name="icd_for_cpt_'+cpt_id+'"]').val();
	var changed_icd_arr = changed_icd.split(',');
	var new_changed_pos = "";
	var tmp_value		= "";
	var tmp_key			= "";
	$.each(changed_icd_arr, function(key,value) {
		if(icd_position==(key+1)){
			tmp_value = value;
			new_changed_pos = new_changed_pos+","+icd_id;
		}
		else if(value==icd_id){
			tmp_key = key+1;
			new_changed_pos = new_changed_pos+",::";
		}
		else{
			new_changed_pos = new_changed_pos+","+value;
		}
	});
	new_changed_pos = new_changed_pos.replace("::",tmp_value);
	new_changed_pos = new_changed_pos.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="icd_for_cpt_'+cpt_id+'"]').val(new_changed_pos);
	if(tmp_key!=''){
		$('#icd_'+cpt_id+'_'+tmp_key).select2("val", tmp_value);
	}
	
}

$(document).on("change",'.js_cpticdsel',function() {	
	var icd_id 			= $(this).val();
	var curr_id			= $(this).attr('id');
	var curr_id_arr 	= curr_id.split('_');
	var cpt_id			= curr_id_arr[1];
	var icd_position	= curr_id_arr[2];
	cpt_icd_change_bill(cpt_id,icd_position,icd_id);
});

function cpt_position_change_track(start_pos,end_pos){
	
	var sel_cpts 	 	= $( "#selected_codes_cpts_arr" ).val();
	var sel_cpts_arr 	= sel_cpts.split(',');
	var cpt_id		 	= sel_cpts_arr[start_pos];
	var new_changed_pos = "";
	
	$.each(sel_cpts_arr, function(key,value) {
		if(end_pos==key){
			if(start_pos>end_pos){
				new_changed_pos = new_changed_pos+","+cpt_id;
				new_changed_pos = new_changed_pos+","+value;
			}
			else{
				new_changed_pos = new_changed_pos+","+value;
				new_changed_pos = new_changed_pos+","+cpt_id;
			}
		}
		else if(start_pos==key){
			new_changed_pos = new_changed_pos;
		}
		else{
			new_changed_pos = new_changed_pos+","+value;
		}
		
	});
	new_changed_pos = new_changed_pos.replace(/(^,)|(,$)/g, "");
	$('input[type=hidden][name="selected_codes_cpts_arr"]').val(new_changed_pos);
}

$(document).ready(function () {
	var hash 		= window.location.hash.substring(1);
	if(hash=='select_procedure' || hash=='create_claim'){
		if($("#providers_id").val()==""){
			window.location.href = "#select_icd";
			location.reload();
		}
	}
});

$(document).on('click', ".js_search_icd_button",function () {
	var search_keyword = $('input[type=text][name="search_icd_keyword"]').val();
	if(search_keyword!=''){
		$("#icd_imo_search_part").html('');
		$("#js_loading_image_icd").removeClass("hide");
		var sel_icds 		= $( "#selected_codes_ids_arr" ).val();
		var formData		= "search_keyword="+search_keyword+"&from=icd&sel_icds="+sel_icds;
		$.ajax({
			type 	: 'POST',
			url 	: api_site_url+'/api/get_superbill_search_icd_cpt_list',
			data 	: formData,
			success :  function(result){
				$("#js_loading_image_icd").addClass("hide");
				$("#icd_imo_search_part").html(result);
				$('input[type=text][name="search_icd_keyword"]').val('');
			}
		});
	}
	else{
		$("#icd_imo_search_part").html('');
		$('#search_icd_keyword_err').removeClass('hide');
	}
	
});

$(document).on('keyup', "input[type='text'][name='search_icd_keyword']",function () {
	if($(this).val()!=''){
		$('#search_icd_keyword_err').addClass('hide');
	}
});

$(document).on('ifToggled', "input[name='imo_search_icds[]']",function () {
	inactive_tab_link('icd_tab');
	
	if ($(this).prop('checked')==true){
		var chk_pro = 'yes';
	}
	else{
		var chk_pro = 'no';
	}
	
	var formData		= "search_value="+$(this).val()+"&from=icd";
	$.ajax({
		type 	: 'POST',
		url 	: api_site_url+'/api/select_api_search_icd_cpt_list',
		data 	: formData,
		success :  function(result){
			
			if(chk_pro=='yes'){
				icd_code_selected(result);
			}
			else{
				remove_seleted_icds(result);
			}
			
		}
	});
	
});

$(document).on('click', ".js_search_cpt_button",function () {
	
	var search_keyword = $('input[type=text][name="search_cpt_keyword"]').val();
	if(search_keyword!=''){
		var sel_cpts 		= $( "#selected_codes_cpts_arr" ).val();
		var formData		= "search_keyword="+search_keyword+"&from=cpt&sel_cpts="+sel_cpts;
		$("#cpt_imo_search_part").html('');
		$("#js_loading_image_cpt").removeClass("hide");
		$.ajax({
			type 	: 'POST',
			url 	: api_site_url+'/api/get_superbill_search_icd_cpt_list',
			data 	: formData,
			success :  function(result){
				$("#js_loading_image_cpt").addClass("hide");
				$("#cpt_imo_search_part").html(result);
				$('input[type=text][name="search_cpt_keyword"]').val('');
			}
		});
	}
	else{
		$("#cpt_imo_search_part").html('');
		$('#search_cpt_keyword_err').removeClass('hide');
	}
	
});

$(document).on('keyup', "input[type='text'][name='search_cpt_keyword']",function () {
	if($(this).val()!=''){
		$('#search_cpt_keyword_err').addClass('hide');
	}
});

$(document).on('ifToggled', "input[name='imo_search_cpts[]']",function () {
	inactive_tab_link('cpt_tab');
	
	if ($(this).prop('checked')==true){
		var chk_pro = 'yes';
	}
	else{
		var chk_pro = 'no';
	}
	
	var formData		= "search_value="+$(this).val()+"&from=cpt";
	$.ajax({
		type 	: 'POST',
		url 	: api_site_url+'/api/select_api_search_icd_cpt_list',
		data 	: formData,
		success :  function(result){
			
			if(chk_pro=='yes'){
				
				var newOptions 		= $.map(icd_popup_list, function(el) { return el });
				var icd_popup_body 	= "";
				$.each(newOptions, function(value,key) {
					icd_popup_body = icd_popup_body + '<div class="form-group" style="margin-left: 0px; margin-right: 0px;"><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><input type="checkbox" name="popup_icd_temp_ids[]" value="'+key['id']+'" class="chk flat-red"></div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">'+key['short_description']+'</div><div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 med-orange">'+key['icd_code']+'</div></div>';
				});
				$('#popup_icd_modal').modal({
					show: 'true'
				});
				icd_popup_body = icd_popup_body + '<script type="text/javascript">$(document).ready(function() {  $(\'input[type="checkbox"].flat-red, input[type="radio"].flat-red\').iCheck({ checkboxClass: \'icheckbox_flat-green\',radioClass: \'iradio_flat-green\' }); }); </script>';
				$('#popup_icd_modal .modal-body').html(icd_popup_body);
				
				$.ajax({
						type : 'GET',
						url  : api_site_url+'/api/get_seleted_cpt_details/'+result+'::no',
						data : '',
						dataType: 'json',
						success :  function(res){
							$('#popup_cpt_title').html(res['cpt_details']['short_description']);
							$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+res['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
							$('input[name="imo_search_cpts[]"]:checkbox[data-id="cpt_'+res['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
							$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+res['cpt_details']['cpt_hcpcs']+'"]').prop("checked", false);
							$('input[name="cpt_codes_seleted[]"]:checkbox[data-id="cpt_'+res['cpt_details']['cpt_hcpcs']+'"]').iCheck('update');
						}
				});
			
			$('input[type=hidden][name="temp_popup_cpt_val"]').val(result);
				
			}
			else{
				remove_seleted_cpts(result);
			}
			
		}
	});
});

/**** Start Superbill claim form submit ajax *****/
$(document).on('click', ".js-submit-superbillclaim_form",function () {  
	var data = $(".superbillclaim_form").serialize();
	$.ajax({
		type : 'POST',
		url  : api_site_url+'/patients/superbill/store',
		data : data,
		success :  function(res){
			js_alert_popup('E-Superbill added successfully');
			window.location.href = api_site_url+'/patients/'+res['patient_id']+'/superbill/create';
		}
	});
});

$(document).delegate('.js_billed_amt', 'change', function(){ 
	$(this).val(parseFloat($(this).val()).toFixed(2));	
});

$(document).ready(function(){
	getmodifierandcpt();
});

function getmodifierandcpt(){	
	var url = api_site_url+'/getmodifier';
	$.get(url, function(data){
	modifier = data.modifier;						
	modifier_arr = $.map(modifier, function(el) { return el; });		
	});	
	
}
$(document).delegate('.js-modifier', 'change', function(){	
	modifier_val = $(this).val();
	var modifier = modifier_val.trim();
	modifier = modifier.toUpperCase(); 
	var id = $(this).attr('id'); 
	sel = $(this).parents('li').attr('id');
	var inputs = $('#'+sel+' .js-modifier');
	is_exist = 0;
	name = $(this).attr('name');
	inputs.not(this).each(function(i){      // If same modifier code entered
		if($(this).val() != '' && $(this).val() == modifier_val){
			js_alert_popup(exist_err_msg);
			$('.js-modifier').val("");			
			is_exist = 1;
			return false;
		}
	});
	check_exist = $.inArray(modifier,modifier_arr);    
	if(check_exist == -1 && !is_exist){    		 
		js_alert_popup(modifier_err_msg);
		$("#"+id).val(" ");
	} else{
		val = modifier_arr[check_exist];    		
		$("#"+id).val(val);
		$("#"+id).parent().next().find('input').attr('readonly', false); 
	}    	
});



/**** End Superbill claim form submit ajax *****/
