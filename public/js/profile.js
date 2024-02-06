/*** Start Profile cover image ***/
var newWindow;
$(document).on('change', '.custom',function () {
	var get_action = $(this).attr('data-action');
	var formData = new FormData();
	file = $('input[id='+get_action)[0].files[0]; 
	var img = new Image();
	img.src = window.URL.createObjectURL( file );
	img.onload = function() {
		var width = img.naturalWidth,
			height = img.naturalHeight;

	window.URL.revokeObjectURL( img.src );
	$('.js_cover').hide();
	if(width>600)
	{
		$('#coverimg').show();
		formData.append('attachment', $('input[id='+get_action)[0].files[0]);
		formData.append('action',get_action);
		$.ajax({
			url: api_site_url+'/profile/addcover', // Url to which the request is send
			 headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').val()
			},
			type: "POST",             // Type of request to be send, called as method
			data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(result)   // A function to be called if request succeeds
			{
				$('#coverphotimg').attr('src',result);
				$('.js_editcover').show();
				$('#coverimg').hide();
			}
		});	
	}
	else
	{
		js_alert_popup('Upload image width should be 600px');
		return false;
	}
	};
    });
/*** End Profile cover image ***/


$(document).on('click', '.js_removecover',function () {
   $.confirm({
		text: "Are you sure you want to remove the cover?",
		confirm: function() {
			 $('#coverimg').show();
			 $('.js_cover').hide();
			 $.get(api_site_url+'/profile/removecover',function(resultcover, status){
				 $('#coverimg').hide();
				$('#coverphotimg').attr('src',api_site_url+resultcover);
				$('.js_addcover').show();
			 });
		},
		cancel: function() {
			// nothing to do
		}
	});
});
$(document).on('click','#new_compose_mail_display', function(event) { 
	var url = $(this).attr("data-url"); 
	launchApplication(url,'newWindow');  
});
function launchApplication(url, title) { 
	var offsetHeight = 550;
	var offsetwidth = $(window).width()-200;
	newWindow = window.open(url, title, 'width=' + offsetwidth + ', height=' + offsetHeight); 
	newWindow.moveTo(180,150);	
	if (window.focus) {  
        newWindow.focus(); 
    }
}
/*** Send compose mail start ***/
$(document).on( 'click', '#send_compose_message', function (e) { 
	e.preventDefault();
	var message_type	= $(this).attr('data-message-type');
	var page_type	= $(this).attr('data-page-type');
	var pre_message_id	= $(this).attr('data-previous-id');
	
	var message_id	= $(this).attr('data-message-id');
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
					mailsendfunc(to_address,message_type,message_id,page_type,pre_message_id);
				}
				else{
				  return false;
				}
			}
			else{
				mailsendfunc(to_address,message_type,message_id,page_type,pre_message_id);
			}
		}
	}
});
/*** Send compose mail end ***/

/*** Send mail process start ***/
function mailsendfunc(to_address,message_type,message_id,page_type,pre_message_id) {
	var formData		= new FormData($('[name="mailcomposerform"]')[0]);
	var submit_type 	= "compose_mail_send";

	formData.append('submit_type',submit_type);
	formData.append('message_type',message_type);
	formData.append('current_message_id',message_id);
	formData.append('pre_message_id',pre_message_id);
	formData.append('page_type',page_type);
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
		url  		:	api_site_url+'/api/profile/messageinsert',
		data		:	formData,
		processData	: 	false,
		contentType	: 	false,
		success 	:  function(msg) {
			var data = JSON.parse(msg);
			if(data['status'] =="success") {
				self.close ();
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

$(document).on("click change",".list_message",function(){ 
	var current_message_id = $('#current_message_id').val();
	var message_id = $(this).attr('data-message');
	var current_id = $(this).attr('id');
	//if(current_message_id != message_id){
		$('#dynamic_detail_message').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"><table class="table-responsive table no-bottom"><thead><th>Message</th></thead></table></div><i class="fa fa-refresh fa-spin fa-3x fa-fw" id="detailed_spin" style="margin:200px;"></i>');
		$('.list_message').removeClass('active');
		$(this).addClass('active');
		var type = $('.find_type.active').attr('data-type');
		var token = $('input[name=_token]').val();
		
		/*if($('tr').find('a').children("span").hasClass('temp') == true){
			$('.text-black.temp').removeClass('text-black');
			$('.temp').addClass('text-gray');
			$('.temp.text-gray').removeClass('temp');
		}*/
		$(this).find('td span').removeClass('text-black');
                $(this).find('td span').addClass('text-gray');
		if($(this).find('a').children("span").hasClass("text-black") == true)
			$(this).find('a').children("span").addClass("temp");

		$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/getMessageData',
			data		:	{'message_id':message_id,'type':type,'_token':token,'current_id':current_id},
			success 	:  function(res) {
				$('#dynamic_detail_message').html(res);
                                
                                $.ajax({
                                    type 		: 	'POST',
                                    url  		:	api_site_url+'/profile/getInboxCount',
                                    data		:	{'type':type,'_token':token},
                                    success 	:  function(msg) {
                                        if(msg.msg!=0)
                                            $('.inbox_unread_count').text(msg.msg);
                                        else
                                            $('.inbox_unread_count').text('');
                                    }
                                });
				
			}
		});
		
	//}
});

$(document).on('click change','.find_type',function(){
	var type = $(this).attr('data-type');
	var current_listing = $('#current_listing').val();
	$('th#cat_type').text(type);
	$('input[name="message_filter"]').attr('data-page-type',type);
	//if(type != current_listing){

		$('.find_type').removeClass('active');
		$(this).addClass('active');
		var token = $('input[name=_token]').val();
		$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/getMessageTypeData',
			data		:	{'type':type,'_token':token},
			success 	:  function(msg) {
				$('#dynamic_listing').html(msg);
				if($('tr').hasClass('list_message') == true){
                                    $('.list_message.active').click();
				}else{
                                    $('#dynamic_detail_message').html('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"><table class="table-responsive table no-bottom"><thead><th>Message</th></thead></table></div><p class="text-center med-gray margin-t-10">No Message Found</p>');
				} 
				$('#current_listing').val(type);
                                //$('.inbox_unread_count').text(msg);
			}
		});
	//}
});

$(document).on("click",'.message-trash',function(){
	var message_id = $(this).attr('data-message-id');
	var id = $(this).attr('data-listing-id');
	var token = $('input[name=_token]').val();
	$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/getSetTrash',
			data		:	{'message_id':message_id,'_token':token},
			success 	:  function(msg) {
				/* $('tr#'+id).remove();
				id = parseInt(id) + parseInt(1);
				$('tr#'+id).click(); */
				$(".find_type.active").click();
                                $("#total_message").text(msg.data.msgCnt);
			}
	});
});

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

$(document).on('click','.set_label',function(){
	var message_structure_id = $(this).attr('data-message-structure-id');
	var label_id = $(this).attr('data-label-id');
	var token = $('input[name=_token]').val();
	$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/setLabel',
			data		:	{'message_structure_id':message_structure_id,'label_id':label_id,'_token':token},
			success 	:  function(msg) {
				/* var count_id = $('.list_message.active').attr('id');
				$('tr#'+count_id).remove();
				count_id = parseInt(count_id)+parseInt(1);
				$('tr#'+count_id).click(); */
				$(".find_type.active").click();
			}
	});
});

var typingTimer;                
var doneTypingInterval = 500;  

$('input[name="message_filter"]').on('keyup', function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(searchmessage, doneTypingInterval);
});

$('input[name="message_filter"]').on('keydown', function () {
  clearTimeout(typingTimer);
});
function searchmessage () {
	var searchkey 	= $('input[name="message_filter"]').val();
	var token 		= $('input[name=_token]').val();
	var page_type 	= $('input[name="message_filter"]').attr('data-page-type'); 
	$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/searchmessage',
			data		:	{'searchkey':searchkey,'page_type':page_type,'_token':token},
			success 	:  function(msg) {
				$('#dynamic_listing').html(msg);
			}
	});
}
$('textarea#personal_note').on('keyup',function(){
	if($('textarea#personal_note').val() == ''){
		$('.personal_note_error').html('Enter note description');
	}else{
		$('.personal_note_error').text('');
	}
});

$('input#note_date').on('keyup',function(){
	$('input#note_date').bootstrapValidator({
        message: 'This value is not valid',
		excluded: ':disabled',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			note_date:{
				selector: '#note_date',
				trigger: 'change keyup',
				validators: {
					date:{
						format:'MM/DD/YYYY'
					},
					callback: {
						message: '',
						callback: function(value, validator, $field) {
							var dob = value;
							var current_date=new Date(dob);
							var d=new Date();	
							if(new RegExp(/^\d{2}\/\d{2}\/\d{4}$/).test(value) && dob != '' && d.getTime() > current_date.getTime()){
								$('button#note_save').attr('disabled','disabled');
								return {
									valid: false,
									message: "Past date not applicable"
								};
							}
							else{
								
								if($('small.help-block:visible').length > 0){	
										$('button#note_save').attr('disabled','disabled');
								}else{
								$('button#note_save').removeAttr('disabled','disabled');
								return true;
								}
							}
						}
					}
				}
			}
		}
	});
});
$('#note_save').click(function(){
	var token 		= $('input[name=_token]').val();
	var personal_note 		= $('textarea#personal_note').val();
	if(personal_note == ''){
		$('.personal_note_error').html('Enter note description');
		return false;
	}else{
		$('.personal_note_error').text('');
	}
	var note_date 		= $('input[name=note_date]').val();
	var note_id 		= $('input[name=note_id]').val();
	$.ajax({
			type 		: 	'POST',
			url  		:	api_site_url+'/profile/personal-notes',
			data		:	{'personal_note':personal_note,'_token':token,'note_date':note_date,'note_id':note_id},
			success 	:  function(msg) { 
				$('#dynamicnotes').html(msg);
				$("#notes_count").text($('.postIt').length);
			}
	});
});

$(document).on('click','.js-popupnotes-delete',function(){
	var note_id = $(this).attr('data-note-id');
	var token 		= $('input[name=_token]').val();
	 $("#js_confirm_patient_demo_remove")
	.modal({show: 'false', keyboard: false})
	.one('click', '.js_modal_confirm', function (e) {
		var conformation = $(this).attr('id');
		
		 if(conformation == "true"){ 
			$.ajax({
				type 		: 	'DELETE',
				url  		:	api_site_url+'/profile/personal-notes/'+note_id,
				data		:	{'_token':token},
				success 	:  function(msg) { 
						$('#dynamicnotes').html(msg);
						$("#notes_count").text($('.postIt').length);
				}
			});
			
		 }
	}); 
  
	
});

$(document).on('click','.edit_notes',function(){ 
	var note_id = $(this).attr('data-note-id');
	$.ajax({
			type 		: 	'GET',
			url  		:	api_site_url+'/profile/personal-notes/'+note_id,
			dataType	:	'json',
			success 	:  function(msg) { 
				$('#popup').click();
				$('#note_id').val(msg.note_id);
				$('#note_date').val(msg.date);
				$('#personal_note').val(msg.notes);
				$('#notes_title').text('Edit Note');
			}
	});
	
});

$(document).on('click','#popup',function(){
        $('#notes_title').text('New Note');
	$('#personal_note').val('');
	$('#note_id').val('');
	$('#note_date').val(''); 
});

$(document).on('dblclick touchend','.postIt',function(){
	$(this).find('.edit_notes').click();
});

$(document).on( 'click', '.add-new-label-submit', function(event) { 
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


