$(function() {
		$( "#sortable" ).sortable({ 
			cursor: "move",
			connectWith  	: ".sortable",
			update         	: function(event,ui){ removeAddquestion(); }
		});
	});
	function addField() {
		$('.js_dummy_input').removeClass('option_value');
		$('#js-questionaire-template').bootstrapValidator('addField',"title");
		$('#js-questionaire-template').bootstrapValidator('addField',"ques_answer[]");
		$('#js-questionaire-template').bootstrapValidator('addField', 'question[]');
		
		/*$('#js-questionaire-template').bootstrapValidator('revalidateField', "title");
		$('#js-questionaire-template').bootstrapValidator('revalidateField',"ques_answer[]");
		$('#js-questionaire-template').bootstrapValidator('revalidateField', 'question[]');
		*/
		if($('#js-questionaire-template input.option_value').length) {
			$('#js-questionaire-template').bootstrapValidator('enableFieldValidators', 'option_value',true);
			$('#js-questionaire-template').bootstrapValidator('addField', 'option_value');
			$('#js-questionaire-template').bootstrapValidator('revalidateField', 'option_value');
		}
		else {
			$('#js-questionaire-template').bootstrapValidator('enableFieldValidators', 'option_value',false);
		}
		
	}
	function revalidateField() {
		addField();
		$('#js-questionaire-template').bootstrapValidator('revalidateField', "title");
		$('#js-questionaire-template').bootstrapValidator('revalidateField',"ques_answer[]");
		$('#js-questionaire-template').bootstrapValidator('revalidateField', 'question[]');
	}
	function removeAddquestion() {
		var total_count	= $(".js_set_question").length-1;
		var first_val =1;
		if($(".js_delete_record").length) {
			var arr = [];
			$(".js_question .js_set_question").each(function(i,val){
				var data_count = $(this).attr("data-count");
				if (data_count.indexOf("new") ==-1) {
					arr[i] = data_count;
				}
			});
			arr.sort(function(a, b){return b-a});
		}
		var length_count = 1;
		$(".js_question .js_set_question").each(function(){
			$(this).attr("id","js_question_"+first_val);
			var data_count = $(this).attr("data-count");
			if (data_count.indexOf("new") !=-1) {
				var new_count = parseInt(arr[0])+length_count;
				$(this).attr("data-count","new"+new_count);
				length_count++;
			}
			
			if(total_count ==1 && first_val == 1) {
				$(this).find('.js_addmore_question').removeClass("hide");
				$(this).find('.js_delete_question').addClass("hide");
				$(this).find('.js_delete_record').not('.js_set_answer_type span').addClass("hide");
			}
			else if(total_count >1 && first_val >= 1)  {
				$(this).find('.js_addmore_question').addClass("hide");
				$(this).find('.js_delete_question').removeClass("hide");
				$(this).find('.js_delete_record').removeClass("hide");
			}
			first_val++;
		});
		if(total_count >1) $(".js_question .js_set_question:last").find('.js_addmore_question').removeClass("hide");
	}
	removeAddquestion();
	/*** Answer option set process start ***/
	$(document).on('click', '.js_input_type', function () {
		var currnt_val 	= $(this).attr('data-identify');
		var parent_id	= $(this).parents("DIV.js_set_question").attr("id");
		$("#"+parent_id+" .js_select_ans").addClass("hide");
		$("#"+parent_id+" .js_set_answer_type").removeClass("hide");
		var get_html = $(".js_input_add_"+currnt_val).html();
		if(currnt_val == "text") {
			$("#"+parent_id+" .js_box").addClass("hide");
			$("#"+parent_id+" .js_text").removeClass("hide");
		}
		else {
			$("#"+parent_id+" .js_box").removeClass("hide");
			$("#"+parent_id+" .js_text").addClass("hide");
		}
		$("#"+parent_id+" .js_add_option").html(get_html);
		$('.js_add_option').find('input[data-name="text"]')
		if($("#"+parent_id+" .js_add_option").find('input[data-name="text"]').length == 0) {
			$("#"+parent_id+" .js_add_option .form-group input").addClass("option_value");
		}
		$("#"+parent_id+" .ques_answer").val(currnt_val);
		$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
	});
	/*** Answer option set process end ***/
	
	/*** Option reset process start ***/
	$(document).on('click', '.js_add_reset', function () {
		var parent_id	= $(this).parents("DIV.js_set_question").attr("id");
		$("#"+parent_id+" .js_select_ans").removeClass("hide");
		$("#"+parent_id+" .js_set_answer_type").addClass("hide");
		$("#"+parent_id+" .ques_answer").val("");
		$("#"+parent_id+" .js_add_option").html("");
		$('#js-questionaire-template').bootstrapValidator('revalidateField',"ques_answer[]");
		$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
	});
	/*** Option reset process end ***/
	
	/*** Option addmore process start ***/
	$(document).on('click', '.js_addmore_option', function () {
		var parent_id	= $(this).parents("DIV.js_set_question").attr("id");
		var option_count= $("#"+parent_id+' .js_add_option input[type="text"]').length;
		var option_count= parseInt(option_count)+1;
		var get_identify = $("#"+parent_id+" .js_add_option").find("input").attr("data-name");
		var get_html = $(".js_input_add_"+get_identify).html();
		$("#"+parent_id+" .js_add_option").append(get_html);
		//var value_text= get_identify.replace('_', ' ');
		$("#"+parent_id+" .js_add_option").find("input:last").attr("plaecholder","option "+option_count).addClass("option_value");
		$("#"+parent_id+" .js_add_option").find("input:last").attr("data-count","add"+option_count);
		var assign_name = $("#"+parent_id+" .js_add_option").find("input").attr('name');
		$("#"+parent_id+" .js_add_option").find("span:last").removeClass("js_add_reset").addClass("js_remove_option").html('&nbsp;<i class="fa fa-times-circle font16" data-placement="bottom" data-toggle="tooltip" data-original-title="Remove"></i>');
	});
	/*** Option addmore process end ***/
	
	/*** Question addmore process start ***/
	$(document).on('click', '.js_addmore_question', function () {
		var question_count  = $(this).parents("DIV.js_question").find(".js_set_question:last").attr("data-count");
		var parent_id		= $(this).parents("DIV.js_set_question").attr("id");
		var set_new_count  = parseInt(question_count)+1;
		var get_html = $(".js_question_add").html();
		$(".js_question").append(get_html);
		$(".js_question .js_set_question:last").attr("id","js_question_"+set_new_count).attr("data-count",set_new_count);
		
		/*** Edit page process start ***/
		if($(".js_delete_record").length) {
			$(".js_question .js_set_question:last").attr('data-count','new');
		}
		/*** Edit page process end ***/
		//$('#js-questionaire-template').bootstrapValidator('addField',$("#js_question_"+set_new_count+' input[name="ques_answer[]"]'));
		//$('#js-questionaire-template').bootstrapValidator('addField',$("#js_question_"+set_new_count+' input[name="question[]"]'));
		removeAddquestion();
	});
	/*** Question addmore process end ***/
	
	/*** Option remove process start ***/
	$(document).on('click', '.js_remove_option', function () {
		$(this).closest(".form-group").remove();
		$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
	});
	/*** Option remove process end ***/
	
	/*** Question remove process start ***/
	$(document).on('click', '.js_delete_question', function () {
		var parent_id  = $(this).parents("DIV.js_set_question").attr("id");
		var get_html = $("#"+parent_id).remove();
		removeAddquestion();
		$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
	});
	/*** Question remove process end ***/
	
	/*** Question or option saved detail remove process start ***/
	$(document).on('click', '.js_delete_record', function () { 
		if($(this).closest(".form-group").length >0)
			var parent_id = $(this).closest(".form-group").attr('id');
		else
			var parent_id = $(this).closest(".form-group-space").attr('id');
		var id  = $(this).attr("data-id");
		var delete_from  = $(this).attr("data-from");
		if(delete_from == "delete_all_option")
			var text = "Are you sure would you like to reset?";
		if(delete_from == "delete_single_option" || delete_from == "delete_question")
			var text = "Are you sure would you like to delete?";
		
		$("#conform_delete .modal-body").html(text);
		$("#conform_delete")
			.modal({show: 'false', keyboard: false})
			.one('click', '.confirm', function (e) {
			var conformation = $(this).text();
			if (conformation == "Yes") {
				$(".js_set_delete_id").val(id);
				$(".js_set_delete_from").val(delete_from);
				var form_value = $(".all_values").serialize();
				$.ajax({
					url: api_site_url+'/questionnaire/template/quesansdelete',
					type : 'post', 
					data : form_value,
					success: function(res){
						var result = res.trim();
						var class_name = result.split("::::");
						if(delete_from != "delete_all_option" && class_name[0] == "alert-success") {
							$("#"+parent_id).remove();
						}
						if(delete_from == "delete_all_option" && class_name[0] == "alert-success") {
							$("#"+parent_id).find("span").removeClass("js_delete_record").addClass("js_add_reset").trigger("click");
						}
						/*var add_html = '<p class="alert '+class_name[0]+'" id="success-alert">'+class_name[1]+'</p>';
						$(".row").find('.col-lg-12:first').html(add_html);*/
						js_sidebar_notification('success',class_name[1]);
						$("#success-alert").delay(2000).slideUp(1000, function(){
						});
						removeAddquestion();
						revalidateField();
						$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
					}
				});
			}
		});
	});
	/*** Question or option saved detail remove process end ***/
	
	/*** General valisdation start ***/
	$(document).ready(function () {
        $('#js-questionaire-template').bootstrapValidator({
			message : 'This value is not valid',
			excluded: [':disabled'],
			feedbackIcons : {
				valid : '',
				invalid : '',
				validating : 'glyphicon glyphicon-refresh'
			},
            fields: {
                title: {
					trigger: 'keyup change',
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
						callback: {
							message: '',
							callback: function (value, validator,element, $field) {
								var alphaspace = alphaspace_lang_err_msg;
								var regex = new RegExp(/^[A-Za-z ]+$/);
								var msg = lengthValidation(value,'feeschedule',regex,alphaspace);
								if(value.length>0 && msg != true){
									return {
										valid: false,
										message: msg
									};
								}
								if(value.length>0 && msg == true){
									/* element.on('focusout', function (event) {
										var current_value		= element.val();
										var current_id_string	= element.parents("form").attr("action").split("/");
										var current_id	= current_id_string[current_id_string.length-1];
										var valid_msg 	= titleValidation(current_value,current_id); //backend validation
										return 
									});*/
									var error_msg = $("#js_title_exist").attr("data-msg");
									if($("#js_title_exist").val()=="yes") {
										return {
											valid: false,
											message: error_msg
										};
									}
								}
								
								return true;
							}
						}
                    }
                }, 
				'question[]': {
					trigger: 'keyup change',
                    validators: {
                        notEmpty: {
                            message: question_lang_err_msg
                        },
						callback: {
							message: '',
							callback: function (value, validator) {
								var alphanumericspac = question_regex_lang_err_msg;
								var regExp = new RegExp(/^[a-zA-Z0-9 ?!]+$/);
								var msg = lengthValidation(value,'feeschedule',regExp,alphanumericspac);
								if(value.length>0 && msg != true){
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
				'ques_answer[]': {
					validators: {
                        notEmpty: {
                            message: question_answer_lang_err_msg
                        }
                    }
                },
				'option_value': {
					enabled:false,
					selector: '.option_value',
					trigger: 'keyup change',
                    validators: {
                        notEmpty: {
                            message: option_lang_err_msg
                        },
						callback: {
							message: option_limit_lang_err_msg,
							callback: function (value, validator) {
								return (value.length>50) ? false : true;
							}
						}
                    }
                }
            }
        }).on('success.form.bv', function(e) {
			// Prevent form submission
			e.preventDefault();
			var question = [];
			var ans_type = [];
			$(".js_pull_input ").html('');
			$(".js_question .js_set_question").each(function(key, val){
				var position = key+1;
				var option_val = [];
				id = $(this).attr('data-count');
				question = $(this).find('input[name="question[]"]').val();
				ans_type = $(this).find('input[name="ques_answer[]"]').val();
				$(this).find(".js_add_option .form-group").each(function(i, selected){
					type  = $(this).find('input').attr('name');
					option_id  = $(this).find('input').attr('data-count');
					if(type == "text")
						var val = 'text';
					else
						var val = $(this).find('input').val();
					option_val[i] = option_id +"::"+val;
				});
				$(".js_pull_input").append('<input type="hidden" name="order['+position+']" value="'+id+'" /><input type="hidden" name="ques['+id+']" value="'+question+'"/><input type="hidden" name="ans['+id+']" value="'+ans_type+'" /><input type="hidden" name="ans_values['+id+']" value="'+option_val+'" />');
			});
			var formData = $('#js-questionaire-template').serialize();
			console.log(formData);
			var url = $('#js-questionaire-template').attr("action");
			$.ajax({
				type : 'POST',
				url  : url,
				data : formData,
				success :  function(result){
					var data = JSON.parse(result);
					if(data["status"] =="success") {
						$('#js-questionaire-template').unbind("success");
						if(window.location.href.indexOf("edit") > -1) {
						js_sidebar_notification('success','Updated successfully ');
						}else{
						js_sidebar_notification('success','Added successfully');
						}
						setInterval(function(){ 
						window.location.href=api_site_url+"/questionnaire/template/"+data["data"];
						}, 2000);
					}
					else if(data["status"] =="failure") 
						$(".js_common_error").html(data["message"]);
					else if(data["status"] =="error") {
						$("p.js_error").addClass("hide");
						$("p.js_error").parent("div").removeClass("error");
						$.each(data["message"], function(key, value) {
							$(".js_"+key).parent("div").addClass("error");
							$(".js_"+key).html(value).removeClass("hide");
						});
						$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
					}
				}
			});
			
		}); 
    });
	/*** Set questions validation in edit form start ***/
	$(document).on('click', '.js_form_submit', function (e) {
		e.preventDefault();
		$('input[name="title"]').trigger('blur');
		revalidateField();
		$('#js-questionaire-template').bootstrapValidator('validate');
	});
	/*** Set questions validation in edit form end ***/
	
	/*** Set questions validation in edit form start ***/
	$(document).on('blur', 'input[name="title"],input[name="question[]"]', function () {
		revalidateField();
		$('#js-questionaire-template').bootstrapValidator('disableSubmitButtons', false);
		
	});
	$(document).on('focusin', 'input[name="title"]', function () {
		$("#js_title_exist").val("no");
	});
	$(document).on('focusout', 'input[name="title"]', function () {
		var current_value		= $(this).val();
		if(current_value.trim().length>0) {
			var current_id_string	= $(this).parents("form").attr("action").split("/");
			var current_id	= current_id_string[current_id_string.length-1];
			var valid_msg 	= titleValidation(current_value,current_id); //backend validation
			var set_msg = (valid_msg != 0) ? "yes":"no";
			$("#js_title_exist").val(set_msg).attr("data-msg",valid_msg);
			$('#js-questionaire-template').bootstrapValidator('revalidateField',"title");
		}
	});
	/*** Set questions validation in edit form end ***/
	
	/*** General valisdation end ***/
	function titleValidation(title,id) {
		var target 	= api_site_url+'/api/questionnaire/getvalidation/'+title+'/'+id; 
		return $.ajax({ type : 'GET',
						url: target,
						async:false,
						success: function(res){
						}
					}).responseText;
	}
