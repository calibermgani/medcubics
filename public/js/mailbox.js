/*** Get compose mail display start ***/
$(document).on('click','#new_compose_mail_display', function(event) {
	var url = $(this).attr("data-url"); 
	launchApplication(url,'Compose Mail');  
});
/*** Get compose mail display end ***/

/*** Get attachment file name display start ***/
$(document).on('change','input[name="attachment_file"]', function() {
	var file = $('input[name=attachment_file]').val().replace(/C:\\fakepath\\/i, '');
	$('.js_display_attachment').html('&nbsp;&nbsp;'+file); 
});
/*** Get attachment file name display end ***/

/*** Get error message display on compose mail start ***/
$(document).on('change','.tokenfield', function() {
	$('.tokenfield').parents('DIV.form-group').find("small").remove();
	$('.tokenfield').parents('DIV.form-group').removeClass("has-error").addClass("has-success");
	$('.tokenfield').parents('DIV.form-group').find("i").removeClass('glyphicon-remove').addClass('glyphicon-ok');
});
/*** Get error message display on compose mail end ***/

/*** Get reply mail display start ***/
$(document).on( 'click','.js_reply_mail,.js_reply_all_mail', function (event) {
	event.preventDefault();
	var url = $(this).attr("data-url"); 
	var mail_id    = $('[name="current_mail_id"]').val();
	if(mail_id == "" || mail_id == null) var mail_id    = $(this).attr("data-mailid"); 
	var conversation = $(this).text();
	
	var data	= "/"+mail_id+"/"+conversation;
	
	if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
		window.open("about:blank", "reply Mail");	
	}
	if(mail_id !='' && mail_id !=null) {
	$.ajax({
		type : 'GET',
		url  : url+data,
		success :  function(msg){
			var offsetHeight = $(".content").height()+50;
			var offsetwidth = $(window).width()-200;
			var newWindow = window.open(url+data,'reply Mail','width=' + offsetwidth + ', height=' + offsetHeight);  
			newWindow.moveTo(180,150);	
			if (window.focus) {  
				newWindow.focus(); 
				newWindow.document.write(msg);
				newWindow.document.close();
			}
			//window.location = '';
		}	
	});
	}
});
/*** Get reply mail display end ***/

/*** Set new window settings start ***/
function launchApplication(url, title) { 
	var offsetHeight = $(".content").height()+50;
	var offsetwidth = $(window).width()-200;
	var newWindow = window.open(url, title, 'width=' + offsetwidth + ', height=' + offsetHeight); 
	newWindow.moveTo(180,150);	
	if (window.focus) {  
        newWindow.focus(); 
    }
}
/*** Set new window settings end ***/

/*** Send compose mail start ***/
$(document).on( 'click', '#send_compose_mail', function (e) {
	e.preventDefault();
	var to_address 					= $('[name="to_address"]').val();
	var mail_subject 				= $('[name="mail_subject"]').val();
	var mail_body 					= $('#compose-textarea').val();
	var attachment_file_val 		= $('input[type=file][name="attachment_file"]').val();
	var attachement_err 			= 'no';
	if(to_address=="" || to_address == null) {
		if($('.tokenfield').parents('DIV.form-group').find('.help-block').length == 0 ) {
			$('.tokenfield').parents('DIV.form-group').removeClass("has-success").addClass("has-error");
			$('.tokenfield').parents('DIV.form-group').append('<small class="help-block" style="margin-left:70px;">Enter Email ID!</small>');
		}
	}
	else {
		var att_filefield_ext   = attachment_file_val.split('.').pop().toLowerCase();
		var valid_file_arr		= ["pdf","jpeg","jpg","png","gif","doc","xls","csv","docx","xlsx","txt"];
		if(attachment_file_val!='' && valid_file_arr.indexOf(att_filefield_ext)==-1){
			alert('Invalid Attachment');
		}
		else {
			$('.tokenfield').parents('DIV.form-group').find("small").remove();
			mail_subject = mail_subject.trim();
			mail_body = mail_body.trim();
			if(mail_subject.length==0 && mail_body.length<=4){
				var checkcnf =  confirm('Send this message without a subject or text in the body?');
				if(checkcnf == true){
					mailsendfunc(to_address);
				}
				else{
				  return false;
				}
			}
			else{
				mailsendfunc(to_address);
			}
		}
	}
});
/*** Send compose mail end ***/

/*** Close compose mail window start ***/
$(document).on( 'click', '#compose_mail_discard', function () {
	window.close();
});
/*** Close compose mail window end ***/

/*** Send mail process start ***/
function mailsendfunc(to_address) {
	var formData		= new FormData($('[name="mailcomposerform"]')[0]);
	var submit_type 	= "compose_mail_send";
	
	formData.append('submit_type',submit_type);
	formData.append('to_mail_id',to_address);
	formData.append('mail_content',$('[name="compose-textarea"]').val());
	var curr_mail_id = $('#curr_mail_id').val();
	var mail_sent_type = $('#mail_sent_type').val();
	
	if(mail_sent_type=="reply") {
		formData.append('curr_mail_id',curr_mail_id);
		formData.append('mail_sent_type',mail_sent_type);
	}
	if(mail_sent_type=="mail_update_draft") {
		formData.append('curr_mail_id',curr_mail_id);
		formData.append('mail_sent_type',mail_sent_type);
	}
	$.ajax({
		type 		: 	'POST',
		url  		:	api_site_url+'/api/profile/mailsendprocess',
		data		:	formData,
		processData	: 	false,
		contentType	: 	false,
		success 	:  function(msg) {
			var data = JSON.parse(msg);
			if(data['status'] =="success") {
				window.close();
				window.opener.focus();
				window.opener.location.reload();
			}
			else{
				if($(".js_attach_err").length == 0)
					$(".js_display_attachment").closest("div").append('<p class="js_attach_err med-orange"></p>');
				$(".js_attach_err").html(data['message']);
			}
		}
		
	});
}
/*** Send mail process end ***/
/*** Send mail process start ***/
function mailsendfunc(to_address) {
	var formData		= new FormData($('[name="mailcomposerform"]')[0]);
	var submit_type 	= "compose_mail_send";
	
	formData.append('submit_type',submit_type);
	formData.append('to_mail_id',to_address);
	formData.append('mail_content',$('[name="compose-textarea"]').val());
	var curr_mail_id = $('#curr_mail_id').val();
	var mail_sent_type = $('#mail_sent_type').val();
	
	if(mail_sent_type=="reply") {
		formData.append('curr_mail_id',curr_mail_id);
		formData.append('mail_sent_type',mail_sent_type);
	}
	if(mail_sent_type=="mail_update_draft") {
		formData.append('curr_mail_id',curr_mail_id);
		formData.append('mail_sent_type',mail_sent_type);
	}
	$.ajax({
		type 		: 	'POST',
		url  		:	api_site_url+'/api/profile/mailsendprocess',
		data		:	formData,
		processData	: 	false,
		contentType	: 	false,
		success 	:  function(msg) {
			var data = JSON.parse(msg);
			if(data['status'] =="success") {
				window.close();
				window.opener.focus();
				window.opener.location.reload();
			}
			else{
				if($(".js_attach_err").length == 0)
					$(".js_display_attachment").closest("div").append('<p class="js_attach_err med-orange"></p>');
				$(".js_attach_err").html(data['message']);
			}
		}
		
	});
}
/*** Send mail process end ***/

/*** Update ckeditor start ***/
function CKupdate(){
    for ( instance in CKEDITOR.instances ){
        CKEDITOR.instances[instance].updateElement();
        CKEDITOR.instances[instance].setData('');
    }
}
/*** Update ckeditor end ***/

/*** Signature show start ***/
$(document).on('ifChecked', "input[name='signature']",function() {
	var signature_val = $(this).val();
	if(signature_val=="no") {
		$('#signature_content_part').removeClass('show').addClass('hide');
	}
	else {
		$('#signature_content_part').removeClass('hide').addClass('show');
	}
});	
/*** Signature show end ***/

/*** Star update start ***/
$(document).on( 'click', '.js-list-main-checkbox', function (event) {
	var i_chk = $(this).find("i").attr('class');
	if(i_chk=='fa fa-square-o'){
		$('.js-list-main-checkbox').find("i").removeClass('fa fa-square-o').addClass('fa fa-check-square-o');
		$('input[name="message_sel_ids[]"]:checkbox').prop("checked", true);
		$('input[name="message_sel_ids[]"]:checkbox').iCheck('update');
	}
	else{
		$('.js-list-main-checkbox').find("i").removeClass('fa fa-check-square-o').addClass('fa fa-square-o');
		$('input[name="message_sel_ids[]"]:checkbox').prop("checked", false);
		$('input[name="message_sel_ids[]"]:checkbox').iCheck('update');
	}
});
/*** Star update end ***/

$(document).on('ifToggled', "input[name='message_sel_ids[]']",function () {
	list_checkboxchange_funct();
});
function list_checkboxchange_funct(){
	var total_len = $("input[name='message_sel_ids[]']").length;
	var len = $("input[name='message_sel_ids[]']:checked").length;
	if(total_len == len){
		$('.js-list-main-checkbox').find("i").removeClass('fa fa-square-o').addClass('fa fa-check-square-o');
	}
	else{
		$('.js-list-main-checkbox').find("i").removeClass('fa fa-check-square-o').addClass('fa fa-square-o');
	}
}
/*** Delete mail process start ***/
$(document).on('click','.js-del-inbox-list-mail,.js-del-label-list-mail,.js-del-sent-list-mail,.js-del-draft-list-mail,.js-del-trash-list-mail', function(event) {
	event.preventDefault();
	var sel_mail_ids	= 	$('input[name="message_sel_ids[]"]:checked').map(function () {
								return this.value;
							}).get();
	var page_access  = $(this).attr('data-value');
	if(sel_mail_ids ==null || sel_mail_ids ==''){
		var sel_mail_ids=$('input[name="current_mail_id"]').val();
	}
	deleteFunction(sel_mail_ids,page_access);
});
$(document).on('click', '.js_current_delete', function (event) {
	event.preventDefault();
	var page_access  = $(this).attr('data-value');
	var sel_mail_ids  = $(this).attr('data-id');
	deleteFunction(sel_mail_ids,page_access);
});

function deleteFunction(sel_mail_ids,page_access){
	if(sel_mail_ids!=""){
		var checkcnf =  confirm('Are you sure want to delete?');
		if(checkcnf == true){
			var formData		= "from="+page_access+"list&sel_mail_ids="+sel_mail_ids;
			$.ajax({
				type : 'POST',
				url  : api_site_url+'/api/profile/message_del_list',
				data : formData,
				success :  function(result){
					$('html').scrollTop($(window).scrollTop());
					$('#mail-success-alert-part-content').html('Message deleted successfully');
					$("#mail-success-alert-part").fadeTo(1000, 600).slideUp(600, function(){
						$("#mail-success-alert-part").alert('close');
					});
					window.location = '';
				}
			});
		}
		else{
			return false;
		}
	}
}
/*** Delete mail process start ***/

/*** View mail process start ***/
function listView(current_msg_id,page_name){
	$.ajax({
		type : 'GET',
		url  : api_site_url+'/profile/maillist/'+page_name+'/'+current_msg_id,
		success :  function(result){
			$("#js_mail_view_"+page_name).html('').html(result);
			if(page_name=="inbox"){
				var unread_msg_count =$("#js_unread_msg_count").val();
				if(unread_msg_count ==0) $(".js_unread_msg_count_show").hide();
				$(".js_unread_msg_count_show").html('').html(unread_msg_count);
				$('.js_list_view_url[data-index="'+current_msg_id+'"]').css("border","");
			}
		}
	});
}
$(document).ready(function () {
	var current_msg_id  = $("#js_last_id").attr('data-index');
	var page_name  = $("#js_last_id").attr('data-value');
	if(current_msg_id && page_name){
	$(".js_inbox_table:first").find('tbody tr:eq(0)').css( "background-color", "#e8e8e8" ).css( "font-weight","normal");
	$(".js_inbox_table:first").find('tbody tr:eq(0)').find("td.text-black").addClass( "text-gray").removeClass( "text-Black");
	listView(current_msg_id,page_name);
	}
});


$(document).on( 'click', '.js_list_view_url', function () {
	$(".js_list_view_url").css( "background-color", "#fff");
	var current_msg_id  = $(this).attr('data-index');
	var page_name  = $(this).attr('data-value');
	listView(current_msg_id,page_name);
	$(this).find("td.text-black").addClass("text-gray").removeClass("text-Black");
	$(this).css( "background-color", "#e8e8e8" ).css( "font-weight","normal");
});
/*** View mail process end ***/

/*** Showing mail process start ***/
$(document).on( 'dblclick', '.js_list_view_url', function() {
	var current_msg_id  = $(this).attr('data-index');
	$(this).css( "background-color", "#e8e8e8" ).css( "font-weight","normal");
	if($(this).attr("data-value")!="draft"){
		$.ajax({
			type : 'GET',
			url  : api_site_url+'/profile/maillist/show/'+current_msg_id,
			success :  function(msg){
				var offsetHeight = $(".content").height()+100;
				var newWindow = window.open("about:blank",'reply Mail','width=' + 1180 + ', height=' + offsetHeight);  
				newWindow.moveTo(180,100);	
				if (window.focus) {  
					newWindow.focus(); 
					newWindow.document.write(msg);
					newWindow.document.close();
				}
			}	
		});
	}
});
/*** Showing mail process end ***/

/*** Draft mail edit start ***/
$(document).on( 'dblclick', '.js_draft_open', function(e) {
	e.preventDefault();
	var url = $(this).attr("data-url"); 
	var mail_id = $(this).attr("data-index"); 
	var data	= "/"+mail_id;
	$.ajax({
		type : 'GET',
		url  : url+data,
		success :  function(msg){
			var offsetHeight = $(".content").height()+150;
			var newWindow = window.open("about:blank",'Draft Mail','width=' + 1180 + ', height=' + offsetHeight);  
			newWindow.moveTo(180,100);	
			if (window.focus) {  
				newWindow.focus(); 
				newWindow.document.write(msg);
				newWindow.document.close();
			}
		}	
	});
});
/*** Showing mail process end ***/

/*** Search mail process start ***/
$(document).on( 'keyup', '.js_input_mail_search', function() {
	var from_access  = $(this).attr('data-index');
	var status_read  = $(".js_unread.med-orange").attr('data-value');
	var order_by	= $("#js_select_filter").val();
	var keyword  = $(this).val();
	$(".js_processing").removeClass('hide');
	$(".js_listmail").addClass('hide');
	var label_id =	$(".js_label_id").val();
	var getorder =	$(".js_filter_by").val();
	data	= "search_keyword="+keyword+"&from_access="+from_access+"&getorder="+getorder+"&order_by="+order_by+"&status_read="+status_read;
	if(label_id !='' && label_id !=null)
		data	= "search_keyword="+keyword+"&from_access="+from_access+"&label_id="+label_id+"&getorder="+getorder+"&order_by="+order_by+"&status_read="+status_read;
	$.ajax({
		type : 'POST',
		url  : api_site_url+'/profile/maillist/keywordsearch',
		headers: {
			'X-CSRF-TOKEN': $('input[name="csrf_token"]').attr('value')
		},
		data : data,
		success :  function(msg){
			$(".js_response_text").html(msg);
			$(".js_listmail_add .mail-list-body").html($(".js_response_text .mail-list-body").html());
			$(".js_response_text").html('');
			$(".js_processing").addClass('hide');
			$(".js_listmail").removeClass('hide');
			$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-green'});
			var current_msg_id  = $("#js_last_id").attr('data-index');
			var page_name  = $("#js_last_id").attr('data-value');
			if(current_msg_id && page_name){
				$(".js_inbox_table").find('tbody tr:eq(0)').css( "background-color", "#fff" ).css( "font-weight","normal");
				listView(current_msg_id,page_name);
			}
			
			$('.mail-list-body').slimScroll({ height: '380px' });
			if(label_id !='')$(".js_label_id").val(label_id);
			$.AdminLTE.boxWidget.activate();
			$('.js_unread').removeClass("med-orange");
			$('.js_unread').css("color","#a3aaaa");
			$('.js_unread[data-value="'+status_read+'"]').addClass("med-orange");
			if(order_by == "ASC") {
				$(".js_check-in").html('Oldest on top <i class="fa fa-long-arrow-down"></i>');
				$(".js_check-in").find('i').removeClass('fa-long-arrow-up').addClass('fa-long-arrow-down');
				$(".js_check-in").removeClass('js_check-in').addClass('js_check-out');
				$("#js_select_filter").val("ASC");
			}
			if(order_by == "DESC") {
				$(".js_check-out").html('Newest on top <i class="fa fa-long-arrow-up"></i>');
				$(".js_check-out").find('i').removeClass('fa-long-arrow-down').addClass('fa-long-arrow-up');
				$(".js_check-out").removeClass('js_check-out').addClass('js_check-in');
				$("#js_select_filter").val("DESC");
			}
		}	
	});
});
/*** Search mail process end ***/

/*** New label add process start ***/
$(document).on( 'click', '.js-add-new-label', function(event) {
	$('#add_new_label_modal').modal({
		show: 'true'
	});
	readcolor();
});

function readcolor() {
  var demo2 = $('#demo2');
  demo2.colorpickerplus();
  demo2.on('changeColor', function(e,color){
		if(color==null) {
		  //when select transparent color
		  $('.color-fill-icon', $(this)).addClass('colorpicker-color');
		} else {
			
		  $('.color-fill-icon', $(this)).removeClass('colorpicker-color');
		  $('.color-fill-icon', $(this)).css('background-color', color);
		   $('#label_color').val(color);
		}
	});
}

$(document).on( 'click', '.add-new-label-submit', function(event) { alert();
	event.preventDefault();
	var label_name = $('#label_name').val();
	if(label_name=="") {
		$('#label_name_err_content').html("Enter Name!");
		$('#label_name_err').show();
	}
	else{
		var formData	= new FormData($('[name="add_new_label"]')[0]);
		$.ajax({
			type : 'POST',
			url  : api_site_url+'/api/profile/newmaillabeladd',
			data : formData,
			processData	: 	false,
			contentType	: 	false,
			success :  function(result) {
				if(result['status']=="failure") {
					$('#label_name_err_content').html("label Name exists!");
					$('#label_name_err').show();
				}
				else {
					window.location='';
					$('#add_new_label_modal').modal('hide');
					$('html').scrollTop($(window).scrollTop());
					$('#mail-success-alert-part-content').html('New Label added successfully');
					$("#mail-success-alert-part").fadeTo(1000, 600).slideUp(600, function(){
					$("#mail-success-alert-part").alert('close');
					});
					
				}
			}
		});
	}
});
/*** New label add process end ***/

/*** Add draft mail process start ***/
$(document).on( 'click', '#add_draft_compose_mail', function (event) {
	event.preventDefault();
	var to_address 					= $('[name="to_address"]').val();
	var mail_subject 				= $('[name="mail_subject"]').val().trim();
	var attachment_file_val 		= $('input[type=file][name="attachment_file"]').val();
	var attachement_err 			= 'no';
	var mail_body = CKEDITOR.instances['compose-textarea'].getData();
	if((to_address=="" || to_address==null) && (mail_subject.length==0 && mail_body.length==0)) {
		js_alert_popup('Please fill any one field');
		return false;
	}
	if(to_address=="" || to_address == null) {
		js_alert_popup(chk);
		var cnfcheck =  confirm('To address is empty. Are you sure want to save the details?');
		if(cnfcheck == true) {
			var chk =1;
		}
		else {
			var chk =0;
			return false;
		}
	}
	if((to_address!="") && (mail_subject.length !=0 || mail_body.length !=0)) {
		var chk =1;
	}
	if((to_address!="") && (mail_subject.length ==0 || mail_body.length ==0)) {
		var chk =1;
	}
	if(chk == 1) {
		var formData 	= new FormData($("#mailcomposerform")[0]);
		var formData		= new FormData($('[name="mailcomposerform"]')[0]);
		var submit_type 	= "mail_compose_draft";
		formData.append('submit_type',submit_type);
		if(to_address =="" || to_address ==null)formData.append('to_address','');
		formData.append('to_mail_id',to_address);
		formData.append('mail_content',$('[name="compose-textarea"]').val());
		var curr_mail_id = $('#curr_mail_id').val();
		var mail_sent_type = $('#mail_sent_type').val();
		
		if(mail_sent_type=="reply") {
			formData.append('curr_mail_id',curr_mail_id);
			formData.append('mail_sent_type',mail_sent_type);
		}
		if(mail_sent_type=="mail_update_draft") {
			formData.append('curr_mail_id',curr_mail_id);
			formData.append('mail_sent_type',mail_sent_type);
		}
		$.ajax({
			type : 'POST',
			url  : api_site_url+'/api/profile/draftmailprocess',
			data : formData,
			processData	: 	false,
			contentType	: 	false,
			success :  function(msg) {
				var data = JSON.parse(msg);
				if(data['status'] =="success") {
					window.close();
					window.opener.focus();
					window.opener.location.reload();
					window.location = api_site_url+'/profile/maillist/draft';
				}
				$('html').scrollTop($(window).scrollTop());
				$('#mail-success-alert-part-content').html("Maillist drafted successfully");
				$("#mail-success-alert-part").fadeTo(1000, 600).slideUp(600, function(){
				$("#mail-success-alert-part").alert('close');
				});
			}
		});
	}
});
/*** Add draft mail process end ***/

/*** Move mail process start ***/
$(document).on( 'click', '.js-move-to-label,.js-move-to-label-from-trash', function(event) {
	event.preventDefault();
	var label_id_val 	= $(this).attr('id');
	var from_page 		= $(this).attr('data-access');
	var from_trash 		= $(this).attr('data-from');
	var to_trash 		= $(this).attr('data-to');
	var label_id_arr 	= label_id_val.split('_');
	var sel_mail_ids	= $('input[name="message_sel_ids[]"]:checked').map(function() {
								return this.value;
						  }).get();
	if(from_page =="popup") var sel_mail_ids=$(this).attr('data-id');
	else if(sel_mail_ids ==null || sel_mail_ids ==''){
		var sel_mail_ids=$('input[name="current_mail_id"]').val();
	}
	var formData		= "label_id="+label_id_arr[1]+"&sel_mail_ids="+sel_mail_ids;
	if(from_trash =="trash") {
		var formData = "label_id="+label_id_arr[1]+"&sel_mail_ids="+sel_mail_ids+"&msg_from="+from_trash+"&msg_to="+to_trash;
	}
	if(from_trash =="label") {
		var formData = "label_id="+label_id_arr[1]+"&sel_mail_ids="+sel_mail_ids+"&from="+from_trash;
	}
	$.ajax({
		type : 'POST',
		url  : api_site_url+'/api/profile/msgmoveprocess',
		data : formData,
		success :  function(result){
			if(from_page =="popup"){
				window.close();
				window.opener.focus();
				window.opener.location.reload();
			}	
			$('html').scrollTop($(window).scrollTop());
			$('#mail-success-alert-part-content').html('Message moved successfully');
			$("#mail-success-alert-part").fadeTo(1000, 600).slideUp(600, function(){
			$("#mail-success-alert-part").alert('close');
			});
			window.location = '';
		}
	});
});
/*** Move mail process end ***/

/*** Filter option mail process start ***/
$(document).on( 'click', '.js_select_filter', function (event) {
	event.preventDefault();
	var from_access  = $(".js_input_mail_search").attr('data-index');
	var status_read  = $(".js_unread.med-orange").attr('data-value');
	var keyword  = $(this).attr('data-value');
	var from_order  = $(this).attr('data-index');
	var desc_chk  = $(this).hasClass('js_check-in');
	var asc_chk  = $(this).hasClass('js_check-out');
	if(desc_chk == true) from_order="ASC"; 
	if(asc_chk == true) from_order="DESC";
	if($('.js_list_view_url').length > 0) {
		$(".js_processing").removeClass('hide');
		$(".js_listmail").addClass('hide');
		var label_id =	$(".js_label_id").val();
		data	= "search_keyword="+keyword+"&from_access="+from_access+"&order="+from_order+"&status_read="+status_read;
		if(label_id !='' && label_id !=null)
			data	= "search_keyword="+keyword+"&from_access="+from_access+"&label_id="+label_id+"&order="+from_order+"&status_read="+status_read;
	
		$.ajax({
			type : 'POST',
			url  : api_site_url+'/profile/maillist/keywordfilter',
			headers: {
				'X-CSRF-TOKEN': $('input[name="csrf_token"]').attr('value')
			},
			data : data,
			success :  function(msg){
				$(".js_listmail_add").html(msg);
				$(".js_processing").addClass('hide');
				$(".js_listmail").removeClass('hide');
				$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-green'});
				var current_msg_id  = $("#js_last_id").attr('data-index');
				var page_name  = $("#js_last_id").attr('data-value');
				if(current_msg_id && page_name){
					$(".js_inbox_table").find('tbody tr:eq(0)').css( "background-color", "#fff" ).css( "font-weight","normal");
					listView(current_msg_id,page_name);
				}
				$('.mail-list-body').slimScroll({ height: '380px' });
				if(label_id !='')$(".js_label_id").val(label_id);
				$.AdminLTE.boxWidget.activate();
				
				if(desc_chk == true) {
					$(".js_check-in").html('Oldest on top <i class="fa fa-long-arrow-down"></i>');
					$(".js_check-in").find('i').removeClass('fa-long-arrow-up').addClass('fa-long-arrow-down');
					$(".js_check-in").attr('data-index',"ASC");
					$("#js_select_filter").val("ASC");
					$(".js_check-in").removeClass('js_check-in').addClass('js_check-out');
				}
				if(asc_chk == true) {
					$(".js_check-out").html('Newest on top <i class="fa fa-long-arrow-up"></i>');
					$(".js_check-out").find('i').removeClass('fa-long-arrow-down').addClass('fa-long-arrow-up');
					$(".js_check-out").attr('data-index',"DESC");
					$("#js_select_filter").val("DESC");
					$(".js_check-out").removeClass('js_check-out').addClass('js_check-in');
				}
				$('.js_unread').removeClass("med-orange");
				$('.js_unread').css("color","#a3aaaa");
				$('.js_unread[data-value="'+status_read+'"]').addClass("med-orange");
			}	
		});
	}
});
/*** Filter option mail process end ***/

/*** Multiple mail apply process start ***/
$(document).on( 'click', '.js-apply-list-msg', function(event) {
	event.preventDefault();
	var apply_msg_type 	= $(this).attr('id');
	var from_page 		= $(this).attr('data-access');
	var sel_mail_ids	= $('input[name="message_sel_ids[]"]:checked').map(function() {
								return this.value;
						  }).get();
	var parent_id= $(this).parents("div .js-msglist-apply-dropdown").attr("id");
	if(from_page =="popup") var sel_mail_ids=$(this).attr('data-id');
	else if(sel_mail_ids ==null || sel_mail_ids ==''){
		var sel_mail_ids=$("#curr_mail_id").val();
	}
	var formData		= "apply_msg_type="+apply_msg_type+"&sel_mail_ids="+sel_mail_ids;
	$.ajax({
		type : 'POST',
		url  : api_site_url+'/api/profile/msglist_applyprocess',
		data : formData,
		success :  function(result){
			if(from_page =="popup") {
				window.close();
				window.opener.focus();
				window.opener.location.reload();
			}
			else {
				if(parent_id=="from_inbox") {
						window.location = api_site_url+'/profile/maillist';
					}
					else if(parent_id=="from_sent") {
						window.location = api_site_url+'/profile/maillist/sent';
					}
					else {
						var label_id_arr 	= parent_id.split('_');
						window.location = api_site_url+'/profile/maillist/other/'+label_id_arr[2];
					}
			}
			if(apply_msg_type=="mark_as_read"){
				var succ_msg = "Selected Messages applied read successfully";
			}
			else if(apply_msg_type=="mark_as_unread"){
				var succ_msg = "Selected Messages applied unread successfully";
			}
			else if(apply_msg_type=="mark_as_stared"){
				var succ_msg = "Selected Messages stared successfully";
			}
			else if(apply_msg_type=="mark_as_unstared"){
				var succ_msg = "Selected Messages unstared successfully";
			}
			
			$('.js-list-main-checkbox').find("i").removeClass('fa fa-check-square-o').addClass('fa fa-square-o');
			$('input[name="message_sel_ids[]"]:checkbox').prop("checked", false);
			$('input[name="message_sel_ids[]"]:checkbox').iCheck('update');
			
			$('html').scrollTop($(window).scrollTop());
			$('#mail-success-alert-part-content').html(succ_msg);
			$("#mail-success-alert-part").fadeTo(1000, 600).slideUp(600, function(){
			$("#mail-success-alert-part").alert('close');
			});
		}
	});
});
/*** Multiple mail apply process end ***/

/*** Stared mail apply process start ***/
$(document).on('click', '.js_make_star', function(event) {
	event.preventDefault();
	var msg_id	= $(this).attr('data-id');
	var page_access	= $(this).attr('data-access');
	var formData		= "star_msg_id="+msg_id+"&from="+page_access;
	$.ajax({
		type 	: 'POST',
		url  	: api_site_url+'/api/profile/message_stared_list',
		data 	: formData,
		success :  function(result){
			if(result['star_fill']=="yes"){
				$('.js_make_star[data-id="'+msg_id+'"]').children().find("i").removeClass('fa fa-star-o text-yellow').addClass('fa fa-star text-yellow');
			}
			else{
				$('.js_make_star[data-id="'+msg_id+'"]').children().find("i").removeClass('fa fa-star text-yellow').addClass('fa fa-star-o text-yellow');
			}
		}
	});
});
/*** Stared mail apply process end ***/

/*** Category mail apply process start ***/
$(document).on('click', '.js_assign_category', function(event) {
	event.preventDefault();
	var apply_msg_type 	= $(this).attr('data-value');
	var categorize_id 	= $(this).attr('id');
	var from_page 		= $(this).attr('data-access');
	var from_access 		= $(this).attr('data-from');
	var sel_mail_ids	= $('input[name="message_sel_ids[]"]:checked').map(function() {
								return this.value;
						  }).get();
	var parent_id= $(this).parents("div .js-msglist-apply-dropdown").attr("id");
	if(from_page =="popup") var sel_mail_ids=$(this).attr('data-id');
	else if(sel_mail_ids ==null || sel_mail_ids ==''){
		var sel_mail_ids=$('input[name="current_mail_id"]').val();
	}
	var formData		= "apply_msg_type="+apply_msg_type+"&sel_mail_ids="+sel_mail_ids+"&categorize_id="+categorize_id+"&from_page="+from_access;
	$.ajax({
		type : 'POST',
		url  : api_site_url+'/api/profile/msglist_applyprocess',
		data : formData,
		success :  function(result){
			if(result['status'] && from_page !="popup") {
				window.location="";
			}
			else if(from_page =="popup") {
				window.close();
				window.opener.focus();
				window.opener.location.reload();
			}
		}
	});
});
/*** Stared mail apply process end ***/

/*** Sorting process start ***/
$(document).on('click', '.js_unread', function (event) {
	event.preventDefault();
	$(".js_processing").removeClass('hide');
	var status_read = $(this).html();
	var order_by	= $("#js_select_filter").val();
	$(".js_listmail").addClass('hide');
	var label_id =	$(".js_label_id").val();
	var page_access	= $(this).attr('data-index');
	var get_order	= $(".js_filter_by").val();
	$.ajax({
		type 	: 'GET',
		url  	: api_site_url+'/profile/maillist/'+status_read+'/'+page_access+'/'+get_order+'/'+order_by+'/'+label_id,
		success :  function(msg) {
			$(".js_listmail_add").html(msg);
			$(".js_processing").addClass('hide');
			$(".js_listmail").removeClass('hide');
			$('input[type="checkbox"].flat-red').iCheck({checkboxClass: 'icheckbox_flat-green'});
			var current_msg_id  = $("#js_last_id").attr('data-index');
			var page_name  = $("#js_last_id").attr('data-value');
			if(current_msg_id && page_name) {
				$(".js_inbox_table").find('tbody tr:eq(0)').css( "background-color", "#fff" ).css( "font-weight","normal");
				listView(current_msg_id,page_name);
			}
			$('.mail-list-body').slimScroll({ height: '380px' });
			if(label_id !='' && label_id !=null)$(".js_label_id").val(label_id);
			$.AdminLTE.boxWidget.activate();
			$('.js_unread').removeClass("med-orange");
			$('.js_unread').css("color","#a3aaaa");
			$('.js_unread[data-value="'+status_read+'"]').addClass("med-orange");
			
			if(order_by == "ASC") {
				$(".js_check-in").html('Oldest on top <i class="fa fa-long-arrow-down"></i>');
				$(".js_check-in").find('i').removeClass('fa-long-arrow-up').addClass('fa-long-arrow-down');
				$(".js_check-in").removeClass('js_check-in').addClass('js_check-out');
			}
			if(order_by == "DESC") {
				$(".js_check-out").html('Newest on top <i class="fa fa-long-arrow-up"></i>');
				$(".js_check-out").find('i').removeClass('fa-long-arrow-down').addClass('fa-long-arrow-up');
				$(".js_check-out").removeClass('js_check-out').addClass('js_check-in');
			}
		}
	});
});
/*** Sorting process end ***/