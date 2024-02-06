$(document).on( 'click', '.js_call_reply_form', function () {
		var get_reply_form = $('.js_get_reply_form').html();
		$('.js_display_reply_form').removeClass('hide');
		$('.js_display_reply_form').addClass('show');
		$('.js_display_reply_form').html(get_reply_form);	
		$('.js_checkbox').html('<input name="closeticket" type="checkbox" value="1">');
		setTimeout(function(){ 
			callicheck();
		}, 10);	
		$('.js_reply_success_msg').removeClass('show');
		$('.js_reply_success_msg').addClass('hide');
	});
	
$(document).on( 'click', '.js_reset', function () {
	$('#js-bootstrap-validator')[0].reset();
	$('#js-bootstrap-validator').bootstrapValidator('resetForm', true);
	$('.js-display-attachment').html('');
});	

$(document).on( 'click', '.js_reply_cancel', function () {
	$('.js_reply_validator')[0].reset();
	$('.js-display-attachment').html('');
	$('.js_display_reply_form').html('');
});

// Reply form in practice
$(document).ready(function() {	
	$(document).on( 'click', '.js_save_reply_from', function () {
		ValidateIt();		
		$('.js_reply_validator').bootstrapValidator('validate');
		var description = $('textarea[name=description]').val();
		
		var attachmentfile = $('input[name=attachmentfile]').val();
		
		var file_upload = 0;
		if(attachmentfile != ''){
			var get_multiplefile = attachmentfile.split('.');
			if(get_multiplefile[1] == 'js' || get_multiplefile[1] == 'css' || get_multiplefile[1] == 'exe' || get_multiplefile[1] == 'php'){
				var file_upload = 1;
			}	
		}
		if(description != '' && file_upload ==0)
		{
			$('.js_display_reply_form').addClass('hide');
			$('.js_display_reply_form').removeClass('show');
			
			$('.js_reply_loading_msg').addClass('show');
			$('.js_reply_loading_msg').removeClass('hide');
				
			var ticket_id = $('input[name=ticket_id]').val();
			var close_ticket = $('input[name=closeticket]:checked').val();
			var description = $('textarea[name=description]').val();
			var page = $('input[name=page]').val();
			var emailid = $('input[name=emailid]').val();
			
			if(page == 'view_status')
			{
				page = emailid;
			}
			
			var formData = new FormData();
			formData.append('attachment', $('input[name=attachmentfile]')[0].files[0]);
			formData.append('description', description);
			formData.append('ticket_id', ticket_id);
			formData.append('closeticket', close_ticket);
		
			
			$.ajax({
			url: api_site_url+'/replyticket', // Url to which the request is send
			 headers: {
				'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
			},
			type: "POST",             // Type of request to be send, called as method
			data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(result)   // A function to be called if request succeeds
			{
				resetticketcoversation(ticket_id,page,'2'); 
				
				$('.js_reply_validator')[0].reset();
				$('.js_reply_validator').data("bootstrapValidator").resetForm();
	
				if(close_ticket == 1)
				{
					$('.js_close').text('Closed');
					$('.js_close').removeClass('label-danger').addClass('label-success');
				}
			}
			});
		}
	});
});	

	
function ValidateIt() {
    $('.js_reply_validator').bootstrapValidator({
        feedbackIcons: {
            valid: "",
            invalid: "",
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
			description:{
				message:'',
				validators:{
					notEmpty:{
						message: desc_errormessage
					}
				}
			},
			attachmentfile:{
				message:'',
				validators:{
						file: {
							extension: 'pdf,jpeg,jpg,png,gif,doc,xls,csv,docx,xlsx,txt',
							message: attachment_valid_lang_err_msg
						},
						callback: {
							message: attachment_length_lang_err_msg,
							callback: function (value, validator) {
								if($('[name="attachmentfile"]').val() !="") {
									var size = parseFloat($('[name="attachmentfile"]')[0].files[0].size/1024).toFixed(2);
									var get_image_size = Math.ceil(size);
									return (get_image_size>filesize_max_defined_length)?false : true;
								}
								return true;
							}
						}
					}
				}
		}
    });
}

function resetticketcoversation(ticket_id,page,argument)
{
	$.ajax({
		url		: api_site_url+'/getticketdetail/'+ticket_id+'/'+page,
		type 	: 'GET', 
		success: function(msg){
			$('.ticket_convarsation').html(msg);
			if(argument == 2)
			{
				$('.js-display-attachment').html('');
				
				$('.js_reply_loading_msg').addClass('hide');
				$('.js_reply_loading_msg').removeClass('show');
				
				$('.js_reply_success_msg').addClass('show');
				$('.js_reply_success_msg').removeClass('hide');
				
				setTimeout(function(){
					$('.js_reply_success_msg').addClass('hide');
					$('.js_reply_success_msg').removeClass('show');
				}, 4000);
			}
			
			if(argument == 1)
			{
				$("#patientnote_model").modal('hide');	
			}
		}
	})
}


$(document).on( 'click', '.js-delete-reply', function () {
		var replyid = $(this).attr('data-replyid');
		var ticket_id = $('input[name=ticket_id]').val();
		var notifiymsg = $(this).attr('data-text');
		var page	   = $('input[name=page]').val();
		var emailid = $('input[name=emailid]').val();
			
		if(page == 'view_status')
		{
			page = emailid;
		}
		$("#session_model .med-green").html(notifiymsg);
			$("#session_model")
			.modal({show: 'false', keyboard: false})
			.one('click', '.js_session_confirm', function (e) {
				var conformation = $(this).attr('id');
				if (conformation == "true") {
					
					$("#patientnote_model .med-green").html('<div style="text-align:center;color:#00877f"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
					$('.js_note_confirm').addClass('hide');
					$('.modal-header').addClass('hide');
					$("#patientnote_model").modal('show');
					
					$.ajax({
						url		: api_site_url+'/removereplyticket/'+ticket_id+'/'+replyid,
						type 	: 'GET', 
						success: function(msg){
							if(msg=='yes'){
								$('.js_reply_success_msg').addClass('hide');
								$('.js_reply_success_msg').removeClass('show');
								resetticketcoversation(ticket_id,page,'1');
							}
							else
							{
								js_alert_popup(emptymsg);
								$("#patientnote_model").modal('hide');	
							}
						}
					})
				}
			});	
});

$(document).on('change','input[name="filefield"],#attachment', function(){
	 var file   = $(this).val().replace(/C:\\fakepath\\/i, '');
	 $('.js-display-attachment').html('&nbsp;&nbsp;'+file); 
});	

// Assign ticket
$(document).on('click','.js_ticketassign', function(event){
	 event.stopPropagation();
	 var ticketid = $(this).data('ticketid');
	 var existuser_id = $(this).data('userid');
	 
	 if($(this).text() == 'Reassign'){
		 $("#ticketassign_modal .modal-title").html('Reassign Ticket');
		 var get_target		 = api_site_url+'/admin/getmedcubicsuserlist/'+ticketid+'/'+existuser_id;
	 }
	 else {
		 $("#ticketassign_modal .modal-title").html('Assign Ticket');
		var get_target		 = api_site_url+'/admin/getmedcubicsuserlist/'+ticketid;	
	 }
	 
	$("#ticketassign_modal .modal-body").html('');

	$("#ticketassign_modal .modal-body").load(get_target, function(){	
		$(".js_select_user_id").select2();	
		assignTicketValidate();	
	}); 
});	

// Assign ticket form validation.
function assignTicketValidate() {
	
	$('#ticketassign_form').bootstrapValidator({
		feedbackIcons: {
			valid: "",
			invalid: "",
			validating: "glyphicon glyphicon-refresh"
		},
		excluded: [':disabled'],
		fields: {
			userlist_id:{
				message:'',
				validators:{
					notEmpty:{
						message: support_select_user_err_msg
					}
				}
			}
		}
	}).unbind("success").on('success.form.bv', function(e) {
		e.preventDefault();
		var userid = $('.js_select_user_id').select2('val');
		var ticketid = $('input[name=ticket_id]').val();
		
		$.ajax({
			url		: api_site_url+'/admin/assignticket/'+ticketid+'/'+userid,
			type 	: 'GET', 
			success: function(msg){
				$('.changeassigntype'+ticketid).html(msg);
				$('.js_reply_ticket').hide();
				$('#ticketassign_modal').modal('hide');
				
			}
		})
					
		$('#ticketassign_form').unbind("success");
	});
}

$(document).on('click',".js_ticket_reset", function () {
	$('#ticketassign_form')[0].reset();
	$('#ticketassign_form').data("bootstrapValidator").resetForm();
});

// User type based display fields in Create new ticket form.
$(document).on('ifChecked','input[name="usertype"]', function () {
	if($(this).val()=='guestuser') {
		$('.guestuser').addClass('show');
		$('.guestuser').removeClass('hide');
		
		$('.registereduser').addClass('hide');
		$('.registereduser').removeClass('show');
	}else {
		$('.guestuser').addClass('hide');
		$('.guestuser').removeClass('show');
		
		$('.registereduser').addClass('show');
		$('.registereduser').removeClass('hide');
	}
	
	$('#js-newticket-validator').bootstrapValidator('revalidateField', 'userlist_id');	
	$('#js-newticket-validator').bootstrapValidator('revalidateField', 'email_id');	
	$('#js-newticket-validator').bootstrapValidator('revalidateField', 'name');	
});


$('#ticketback').click(function(){
		parent.history.back(); 
		return false;							   
	});