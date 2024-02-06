/**** Start Superbill claim form submit ajax *****/

/*** header dropdown change function starts ***/
$(document).on('change', ".js_choose_header",function () {
	var current_list = $(this).val(); 
	var current_list_length = (current_list != null) ? current_list.length:0; 
	
	var prev_list = $("#selected_list").val().split(",");
	var prev_list_length = (prev_list != '') ? prev_list.length:0;
	
	$("#selected_list").val(current_list);
	if(prev_list_length >0 && prev_list_length > current_list_length) {
		for(i=0;i<prev_list_length;i++){
			if(jQuery.inArray(prev_list[i], current_list) == -1){
				last_selected_value = prev_list[i];
				var div_id=$(this).parents("div .js_common_header").attr("data-index");
				$(".close").attr('id','js_colse_reset');
				$(".close").attr('data-index',last_selected_value);
				$('.select2').select2("enable",false);
				$("#superbill_modal")
				.modal({show: 'false', keyboard: false})
				.one('click', '.js_modal_confirm', function (e) {
					var conformation = $(this).attr('id');
					$('.select2').select2("enable",true);
					if (conformation == "true") {
						$('#superbill_modal').modal('hide');
						$("#js_"+last_selected_value).remove();
						$("#js_drop_down").find('option:gt(0)').remove();
						if($(".js_common_header").length == 0) $(".js_search_section").addClass("hide");
						$.each(current_list, function(i, val) {
							var current_selected_text = $(".js_choose_header option[value='"+val+"']").text();
							$("#js_drop_down").append("<option value='"+val+"'>"+ current_selected_text+ "</option>");
						});
						$("#js_drop_down").select2();
						
					}
					else {
						$('#superbill_modal').modal('hide');
						$(".js_choose_header option[value='"+last_selected_value+"']").prop("selected", true);
						var all_values = []; 
						$('.js_choose_header option:selected').each(function(i, selected){ 
							all_values[i] = $(selected).val(); 
						});
						$('.js_choose_header').select2('val',all_values);
						$("#selected_list").val(all_values);
						$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'header_list');
					}
				});
			}
		}
	}
	else {
		if(current_list_length >0 && prev_list_length < current_list_length) addHeader(current_list,prev_list);
	}
});
/*** header dropdown change function ends ***/

/*** Add header ajax fuction starts ***/
function addHeader(current_list,prev_list) {
	for(i=0;i<current_list.length;i++) {
		if(jQuery.inArray(current_list[i], prev_list) == -1) {
			$("#js_wait_popup").modal("show");
			var last_selected_value = current_list[i];
			var last_selected_key = $('.js_choose_header option[value="'+last_selected_value+'"]').text(); 
			var formData		= "key="+last_selected_key+"&value="+last_selected_value;
			$.ajax({
				url: api_site_url+'/superbills/create', // Url to which the request is send
				headers: {
					'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
				},
				type: "POST",             	// Type of request to be send, called as method
				data: formData,
				success: function(res) {
					$(".js_add_header").append(res);
					$(".js_search_section").removeClass("hide");
					$("#js_drop_down").find('option:gt(0)').remove();
					$.each(current_list, function(i, val) {
						var current_selected_text = $(".js_choose_header option[value='"+val+"']").text();
						$("#js_drop_down").append("<option value='"+val+"'>"+ current_selected_text+ "</option>");
					});
					$("#js_drop_down").val("").trigger("change");
					$("#js_drop_down").select2();
					$("#js_wait_popup").modal("hide");
				}
			});
		}
	}
}
/*** Add header ajax fuction ends ***/

/*** cancel the popup option function starts ***/
$(document).on('click', "#js_colse_reset",function () {
	var last_selected_value =$(".close").attr('data-index');
	$('#superbill_modal').modal('hide');
	$(".js_choose_header option[value='"+last_selected_value+"']").prop("selected", true);
	var all_values = []; 
	$('.js_choose_header option:selected').each(function(i, selected){ 
	  all_values[i] = $(selected).val(); 
	});
	$('.js_choose_header').select2('val',all_values);
	$("#selected_list").val(all_values);
	$(".js_choose_header").removeAttr("disabled");
});
/*** cancel the popup option function ends ***/

/*** already code exist alert show fuction starts ***/
$(document).on( 'ifToggled click', '.chk', function () {
	var chk = $(this).is(":checked");
	if(chk ==false) {
		$(this).closest("tr").next('tr').find('td').css("opacity","0");
	}
	var chked_length = $('[name="search_cpts[]"]:checked').length;
	if(chked_length >0) $(".js_add_section").removeClass("hide");
	else $(".js_add_section").addClass("hide");
});
/*** already code exist alert show fuction ends ***/

/*** attach codes fuction starts ***/
$(document).on( 'click', '.js_add', function () {
	var attached_section = $("#js_drop_down").val();
	if(attached_section !=''){
		var get_details = []; 
		var get_code = []; 
		$('[name="search_cpts[]"]:checked').each(function(i, val) { 
			get_detail = $(this).val();
			get_cod = $(this).attr('data-value');
			
			if(get_detail !="")get_details[i] = get_detail;
			if(get_cod !="undefined")get_code[i] = get_cod;
		});
		
		var added_code = []; 
		$('.js_all_code').each(function(i, val) { 
			add_code = $(this).text();
			if(add_code!="")added_code[i] = add_code;
		});
		$(".js_alert_msg").addClass("hide");
		
		if(added_code !=""){   
			for(i=0;i<get_code.length;i++){
				if(jQuery.inArray(get_code[i], added_code) !== -1){
					$("#js_alert_"+get_code[i]).removeClass("hide");
				}
				else{
					var new_code = [];
					new_code[0] = $('.chk[data-value="'+get_code[i]+'"]').val();
					
					attachList(new_code,attached_section);
				}
			}
		}
		else{
			attachList(get_details,attached_section);
		}
		
	}
	else{
		var header_list = $(".js_choose_header").select2('val');
		if(header_list =="") {
			$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'header_list');
		}
		else { 
			$('.search_form').bootstrapValidator('revalidateField', 'selected_list');
		}
	}
	var total_attached_length = $(".js_count").length;
	$(".js_genarate").removeClass("hide");
	if(total_attached_length >1) $(".js_genarate #js_show_template").attr("disabled",false);
	else $(".js_genarate #js_show_template").attr("disabled","disabled");
});
/*** attach codes fuction ends ***/

/*** Delete selected codes fuction starts ***/
$(document).on( 'click', '.remove_selected_icds', function() {
	var div_id=$(this).parents("div .js_common_header").attr("id");
	$(this).closest('ul').remove();
	var ul_length= $("#"+div_id+" ul").length; 
	if(ul_length ==0) $("#"+div_id+" .js_div_empty_alert").val("0");
	var total_attached_length = $(".js_count").length;
	if(total_attached_length >1) $(".js_genarate").removeClass("hide");
	else $(".js_genarate").addClass("hide");
});
/*** Delete selected codes fuction ends ***/

/*** Delete selected template header fuction starts ***/
$(document).on('click', ".js_close_header",function() {
	$(".js_choose_header").attr("disabled","disabled");
	var div_id=$(this).parents("div .js_common_header").attr("data-index");
	$("#superbill_modal")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function(e) {
		var conformation = $(this).attr('id');
		if (conformation == "true") {
			$("#js_"+div_id).remove();
			$('#superbill_modal').modal('hide');
			$(".js_choose_header option[value='"+div_id+"']").prop("selected", false);
			var all_values = []; 
			$("#js_drop_down").find('option:gt(0)').remove();
			$('.js_choose_header option:selected').each(function(i, selected) { 
				all_values[i] = $(selected).val(); 
			});
			
			$.each(all_values, function(i, val) {
				var current_selected_text = $(".js_choose_header option[value='"+val+"']").text();
				$("#js_drop_down").append("<option value='"+val+"'>"+ current_selected_text+ "</option>");
			});
			$('.js_choose_header').select2('val',all_values);
			$("#selected_list").val(all_values);
			$("#js_drop_down").select2();
			if($(".js_common_header").length == 0){ $(".js_search_section").addClass("hide");
			$(".js_genarate").addClass("hide");
			}
		}
		$(".js_choose_header").removeAttr("disabled");
	});
});
/*** Delete selected template header fuction ends ***/


/*** Restrict and add cpt codes fuction starts ***/
function attachList(get_details,attached_section) {
	$.each(get_details, function(i, val) {
		var codes_text='';
		var codes_text=val.split("::");
		$(".js_checked_content .js_checked_content_code").removeClass("js_checked_content_code").addClass("js_checked_content_code_"+codes_text[0]);
		
		$(".js_checked_content .js_checked_content_text").removeClass("js_checked_content_text").addClass("js_checked_content_text_"+codes_text[0]);
		
		$(".js_checked_content .all_html_values").attr("id","js_"+codes_text[0]);
		
		var attached_row = $(".js_checked_content").html();
		$("#"+attached_section).append(attached_row);
		var header_text = $("#js_"+attached_section+" h3").text();
		$("#js_"+attached_section+" .js_div_empty_alert").val("1");
		$("#js_"+attached_section+" p").html("");
		$("#"+attached_section+" .js_checked_content_code_"+codes_text[0]).html(codes_text[0]);
		$("#"+attached_section+" .js_checked_content_code_"+codes_text[0]).attr("data-value",codes_text[1]);
		
		$("#"+attached_section+" .js_checked_content_text_"+codes_text[0]).html(codes_text[1]);
		
		$(".js_checked_content"+" .js_checked_content_code_"+codes_text[0]).removeClass("js_checked_content_code_"+codes_text[0]).addClass("js_checked_content_code");
		$(".js_checked_content"+" .js_checked_content_text_"+codes_text[0]).removeClass("js_checked_content_text_"+codes_text[0]).addClass("js_checked_content_text");
		$("#"+attached_section+" #js_"+codes_text[0]).val(attached_section+"::"+val+"::"+header_text);
		$(".js_checked_content .all_html_values").attr("id","all_html_values");
		
	});
	$(".js_search_reslut").closest("div").addClass("hide");
	$(".js_add_section").addClass("hide");
	
}
/*** Restrict and add cpt codes fuction ends ***/

/*** validation for search form fuction starts ***/
$(document).ready(function () {
    $(".js_search").click(function () {
        $('.search_form').bootstrapValidator('validate');
		
    });
    ValidateIt();
});
function ValidateIt() {
    var validator = $('.search_form').bootstrapValidator({
        feedbackIcons: {
            valid: "",
            invalid: "",
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
			search_keyword: {
				message: '',
				validators: {
					notEmpty: {
						message: keyword_req
					}
				}
			},
			selected_list: {
				message: '',
				validators: {
					notEmpty: {
						message: header_req
					}
				}
			}
		},
		onSuccess: function(e) {
			e.preventDefault();
			$(".js_search_reslut").addClass("hide");
			$("#js_loading_image").removeClass("hide");
			var data = $('[name=search_keyword]').serialize();//only input
			$.ajax({
				url		: api_site_url+'/superbills/template/search',
				type 	: 'POST', 
				data	:	data,
				success: function(msg) {
					$("#js_loading_image").addClass("hide");
					$(".js_add_section").addClass("hide");
					$(".js_search_reslut").removeClass("hide");
					$(".js_search_reslut").closest("div").removeClass("hide");
					$(".js_search_reslut").closest("div").html("");
					$(".js_search_reslut").closest("div").css({"height":"100%","overflow":"hidden"});
					if(msg != 1) {
						$(".js_search_reslut").html(msg);
						$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-green'});
						if($(".js_search_reslut .col-lg-6").length > 30)
							$(".js_search_reslut").closest("div").css({"height":"300px","overflow-y":"scroll"});
					}
					else {
						$(".js_search_reslut").html("<p class='text-center'> No results found </p>");
					}
					$("#js_drop_down").select2("val", "");
				}
			})
		}
    });
}
/*** validation for search form fuction ends ***/

/*** Common validation starts ***/
$('.js_superbill_template').bootstrapValidator({
	feedbackIcons: {
		valid: "",
		invalid: "",
		validating: "glyphicon glyphicon-refresh"
	},
	fields: {
		template_name: {
			message: '',
			validators: {
				notEmpty: {
					message: template_req
				},
				regexp:{
					regexp: /^[A-Za-z ]+$/,
					message: alphaspace_lang_err_msg
				},
			}
		},
		provider_id: {
			message: '',
			validators: {
				notEmpty: {
					message: provider_req
				}
			}
		},
		header_list: {
			message: '',
			validators: {
				notEmpty: {
					message: header_req
				}
			}
		}
	}
});
/*** Common validation ends ***/

/*** show Templates function ajax starts ***/
$("#js_show_template").click(function (e) {
	$(".all_values_input").html('');
	var set_alert=[];
	$(".js_div_empty_alert").each(function(i,selected) {
		set_alert[i] = $(this).val();
	});
	if(set_alert !='') {
		if(jQuery.inArray("0", set_alert) !== -1) {
			if($(".js_div_empty_alert[value=0]").parents("div .js_common_header").find(".table-responsive p").length ==0){
				$(".js_div_empty_alert[value=0]").parents("div .js_common_header").find(".table-responsive").append("<p style='font-size: 11px;color: red;'></p>");
			}
			$(".js_div_empty_alert[value=0]").parents("div .js_common_header").find(".table-responsive p").html(header_empty_lang_err_msg);
		}
		else {
			$(".all_html_values").each(function(i,selected) {
				var get_values = $(this).val();
				if(get_values !='') {
					var get_key = get_values.split("::");
					var get_value = get_key[3]+"::"+get_key[1]+"::"+get_key[2];
					if(get_key[0] == "skin_procedures" || get_key[0] == "medications" ) {
						if(get_key.length == 5) {
							var org_values = get_key[3]+"::"+get_key[1]+"::"+get_key[2]+"::"+get_key[4];
						}
						else {
							var org_values = get_value;
						} 
					}
					else {
						var org_values = get_value;
					}
					var fieldHTML = "<input type='hidden' value='"+org_values+"' name='"+get_key[0]+"[]'>";
					if($('.js_template_id').length >0) {
						var template_id = $('.js_template_id').val();
						fieldHTML = fieldHTML + "<input type='hidden' value='"+template_id+"' name='template_id'>";
					} 
					$(".all_values_input").append(fieldHTML);
				} 
			});
			e.preventDefault();
			var data = $('[name=all_values]').serialize();//only input
			$.ajax({
				url: api_site_url+'/superbills/template/show', // Url to which the request is send
				type: "POST",
				data:data,
				success: function(res) {
					$('.js_prev_template').removeClass('hide');
					$('.js_orgin').addClass('hide');
					$('.js_prev_template').html(res);
					$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-green'});
					$.AdminLTE.boxWidget.activate();
					//second option drag div
					$(function() {
						$('.droppable').droppable({
							tolerance: 'fit'
						});
						$('.draggable').draggable({
							revert: 'invalid',
							stop: function(){
								$(this).draggable('option','revert','invalid');
							}
						});
						$('.draggable').droppable({
							greedy: true,
							tolerance: 'touch',
							drop: function(event,ui){
								ui.draggable.draggable('option','revert',true);
							}
						});
					});
					$(".js_choose_header").attr("disabled","disabled");
					/*setTimeout(function(){ 
						var max_height = [];
						$(".draggable").each(function(i,selected) {
							var get_top 		= 	$(this).css("top");
							var get_width 		= 	$(this).width();
							var get_inner_height= 	$(this).find("ul").height();
							$(this).width(get_width-10);
							$(this).height(get_inner_height);
							var get_ul_height 	= 	$(this).find("ul").innerHeight();
							var total_height	=	parseInt(get_top)+parseInt(get_ul_height);
							max_height.push(total_height);
						});
						var get_max_height  = Math.max.apply(Math, max_height);
						var set_max_height  = (get_max_height*2)+10;
						$(".droppable").css({'width':'','height':set_max_height+"px"});
						$('select[name="header_list"]').parents('.form-group').hide();
					}, 100);*/
					
				}
			});
		}
	}
});
/*** show Templates function ajax ends ***/
function unitFieldValidation() {
	$('.submit_template').bootstrapValidator({
		feedbackIcons: {
			valid: "",
			invalid: "",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			anesthesia_unit: {
				message: '',
				selector: '.anesthesia_unit',
				trigger: 'keyup change',
				validators: {
					callback: {
						message: '',
						callback: function (value, validator) {
							var count = value.split(".").length - 1;
							if(count>1) {
								return {
									valid: false,
									message: alphanumericdot_lang_err_msg
								}; 
							}
							return true;
						}
					}
					
				}
			}
		}
	});
}
/*** Submit Templates function ajax starts ***/
$(document).on( 'click', '#js_submit_template', function (e) {
	/*$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'template_name');
	$('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'provider_id');*/
	
	$('#js-bootstrap-validator').bootstrapValidator('validate');
	if($(".anesthesia_unit").length>0) {
		unitFieldValidation();
		$('.submit_template').bootstrapValidator('validate');
	}
	e.preventDefault();
	$('.js_get_all_need_values').html('');
	var template_name = $('[name=template_name]').val(); 	
	var provider_id = $('[name=provider_id]').val();
	var header_list = $('[name=header_list]').val();
	
	var status = $('[name=status]:checked').val();
	var get_value=[];
	var get_text=[];
	var get_style=[];
	$(".js_header_order").each(function(i,selected) {
		var get_val = $(this).text();
		var get_text_val = $(this).attr('data-value');
		var get_styl = $(this).parents('.draggable').attr('style');
		if(get_val !='') {
			get_value[i]=get_val;
			get_text[i]=get_text_val;
			get_style[i]=get_styl;
		} 
	});
	
	var fieldHTML= "<input type='hidden' value='"+template_name+"' name='template_name'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+provider_id+"' name='provider_id'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+status+"' name='status'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+header_list+"' name='header_list[]'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+get_text+"' name='get_list_order[]'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+get_value+"' name='order_header[]'>";
	fieldHTML = fieldHTML + "<input type='hidden' value='"+get_style+"' name='header_style[]'>";
	if($('.js_template_id').length >0) {
		var template_id = $('.js_template_id').val();
		fieldHTML = fieldHTML + "<input type='hidden' value='"+template_id+"' name='template_id'>";
	} 
	$('.js_get_all_need_values').append(fieldHTML);
	var data = $('[name=submit_template]').serialize();//only input
	
	if((template_name !='') &&(provider_id !='')) {
		$.ajax({
			url: api_site_url+'/superbills/store', // Url to which the request is send
			type: "POST",
			data:data,
			success: function(res) {
				var data = JSON.parse(res);
				if(data["status"] =="success") {
					window.location.href=api_site_url+"/superbills/"+data["data"];
					/*$(".js_alert_class").html('<p class="alert alert-success" id="success-alert">'+data["message"]+'</p>');
					$(".js_alert_class").removeClass("hide");
					$("#success-alert").fadeTo(1000, 600).slideUp(600, function(){
						$("#success-alert").alert('close');
					});*/
				}
				else {
					$("p.js_error").addClass("hide");
					$("p.js_error").parent("div").removeClass("error");
					$.each(data["message"], function(key, value) {
						$(".js_"+key).parent("div").addClass("error");
						$(".js_"+key).html(value).removeClass("hide");
					});
				}
			
			}
		});
	}
});
/*** Submit Templates function ajax ends ***/

/**** End Superbill claim form submit ajax *****/