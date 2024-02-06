<div class="js_reply_success_msg hide alert alert-success">
	{{ trans("support/ticket.validation.reply_success") }}
</div>		
<div class="col-md-12 col-md-12 col-sm-12 col-xs-12 margin-t-10" >
	<div class="box box-info no-shadow">
		<div class="box-header-view with-border">
			<h3 class="box-title"><i class="fa {{Config::get('cssconfigs.common.search')}} i-font-tabs"></i> Search Ticket</h3>
			<div class="box-tools pull-right">
			  <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body  form-horizontal margin-t-10 margin-l-10">
			{!! Form::open(['url'=>['ticketstatus'],'id'=>'js-bootstrap-validator','name'=>'medcubicsform']) !!}
			{!! Form::hidden('user_id',@Auth::user ()->id,['class'=>'form-control']) !!}
			<input type="hidden" name="page" value="view_status">
			@if(Auth::user ()=='')
			<div class="form-group">
				{!! Form::label('Enter email ID', 'Enter email ID', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('ticketno')) error @endif">
					{!! Form::text('emailid',null,['class'=>'form-control','maxlength'=>'40']) !!}
					{!! $errors->first('emailid', '<p> :message</p>')  !!}
				</div>
			</div>
			@endif
			<div class="form-group">
				{!! Form::label('Enter ticket no', 'Enter Ticket No', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('ticketno')) error @endif">
					{!! Form::text('ticketno',null,['class'=>'form-control','maxlength'=>'25']) !!}
					{!! $errors->first('ticketno', '<p> :message</p>')  !!}
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 margin-t-m-10">
					{!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group']) !!}
				</div>
			</div>

			{!! Form::close() !!}	
			
			<div class="ticket_convarsation"></div>
			
			<div class="js_reply_loading_msg hide" style="text-align:center; color:#00877f;">	
				<i class="fa fa-spinner fa-spin font20"></i> Processing..
			</div>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide js_display_reply_form">
				<i class="fa fa-spinner fa-spin font20"></i> Processing..
			</div>
			
		</div><!-- /.box-body -->	
			
	
		<div class="hide js_get_reply_form" >	
			<div class="col-lg-6 col-md-3 col-sm-3 col-xs-12 form-horizontal margin-t-15 no-padding">	
			{!! Form::open(['url'=>['ticketstatus'],'name'=>'medcubicsform','id'=>'js_reply_validator','class'=>'js_reply_validator']) !!}
			<div class="form-group">					
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
					{!! Form::textarea('description',null,['class'=>'form-control','placeholder'=>'Description']) !!}
				</div>					
			</div>
			<div class="form-group ">
			<div class="col-lg-6 col-md-4 col-sm-6 col-xs-10 no-padding">
				<div class="col-lg-1 col-md-4 col-sm-6 col-xs-10 js_checkbox">
					{!! Form::checkbox('closeticket'); !!}
				</div>					
				<div class="col-lg-6 col-md-4 col-sm-6 col-xs-10 med-green font600">
					Close Ticket
				</div>
			</div>	
			</div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="fileContainer" style="padding:1px 10px;"> 
					{!! Form::file('attachmentfile',['class'=>'form-control form-cursor','id'=>'attachment']) !!}Attachment  </span>
					&emsp;<span class="js-display-error"></span>
				</div>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-padding">
				{!! Form::button('Submit', ['name'=>'sample','class'=>'btn btn-medcubics-small js_save_reply_from']) !!}
				<a href="javascript:void(0)" class="btn btn-medcubics-small js_reply_cancel">Cancel</a>
			</div>
			{!! Form::close() !!}	
			</div>
		</div>		
	</div><!-- /.box -->
</div><!--/.col (left) -->
	

@push('view.scripts')
<script type="text/javascript">
	ValidateIt();
	$(document).ready(function() {
		 $('#js-bootstrap-validator').bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: '',
				invalid: '',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
			  ticketno:{
				message: '',
					validators: {
						notEmpty: {
							message: '{{ trans("support/ticket.validation.ticket_no") }}'
						},
						regexp: {
							regexp: /^[0-9]+$/,
							message: '{{ trans("common.validation.numeric") }}'
						}
					}
				},
				emailid:{
				//enabled: false,	
				message: '',
					validators: {
						notEmpty: {
							message: '{{ trans("common.validation.email") }}'
						},
						callback: {
							message: '',
							callback: function (value, validator) {
								var response = emailValidation(value);
								if(response !=true) {
									return {
										valid: false, 
										message: response
									};
								}
								return true;
							}
						}
					}
				}
			},
			onSuccess: function(e) {
				e.preventDefault();
				var ticketno = $('[name=ticketno]').val();
				var emailid  = $('[name=emailid]').val();
					$('.ticket_convarsation').html('<div style="text-align:center;color:#00877f"><i class="fa fa-spinner fa-spin font20"></i> Processing</div>');
				$.ajax({
					url		: api_site_url+'/getticketdetail/'+ticketno+'/'+emailid,
					type 	: 'GET', 
					success: function(msg){
						$('.ticket_convarsation').html(msg);
						$('.js_reply_validator')[0].reset();
						$('.js-display-attachment').html('');
						$('.js_display_reply_form').html('');
						
						$('.js_reply_success_msg').addClass('hide');
						$('.js_reply_success_msg').removeClass('show');
				
						readMore();
					}
				})
			}
		});
	});
</script>
@endpush