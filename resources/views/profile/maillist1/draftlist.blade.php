{!! Form::hidden('curr_mail_id',null,['class'=>'form-control input-sm','id'=>'curr_mail_id']) !!}
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mail_list_part" id="sent_list">
  <div class="box box-view no-shadow">
	<div class="box-header-view with-border">
	  <h3 class="box-title">Draft</h3>
	  <div class="box-tools pull-right">
		<div class="has-feedback">
		  <input type="text" class="form-control input-sm-header-billing" placeholder="Search Mail" style="margin-top: 1px; background: #e7fcfd; border:0px;">
                    <span class="glyphicon glyphicon-search form-control-feedback margin-t-m-5"></span>
		</div>
	  </div><!-- /.box-tools -->
	</div><!-- /.box-header -->
	<div class="box-body no-padding">
	  <div class="mailbox-controls">
		<!-- Check all button -->
		<div class="btn-group">
                    <button class="btn btn-info btn-sm checkbox-toggle js-list-main-checkbox" style="line-height: 10px;"><i class="fa fa-square-o"></i></button>
		  <button class="btn btn-info btn-sm js-del-sent-list-mail" style="line-height: 10px;"><i class="fa fa-trash-o"></i></button>
		</div><!-- /.btn-group -->
		
		
	  </div>
	  <div class="table-responsive mailbox-messages">
		<table class="table table-hover table-striped">
		  <tbody>
			@if($message_draft_list_count > 0)
				@foreach($message_draft_list as $message_draft_list_val)
				<tr>
				  <td>
				  {!! Form::checkbox('message_sel_ids[]', $message_draft_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
				  </td>
				  <td class="mailbox-name"><a href='javascript:void(0);' id="draftmail_{!! $message_draft_list_val->message_id !!}" class="js-draft-mail-compose">{!! $message_draft_list_val->to_add_arr_emails !!}</a></td>
				  <td class="mailbox-subject"><b>{!! $message_draft_list_val->subject !!}</b> - {!! $message_draft_list_val->message_body !!}</td>
				  <td class="mailbox-date">{!! $message_draft_list_val->messagetimeago !!}</td>
				</tr>
				@endforeach
			@else
				<h4 class="text-center med-green">No Messages Available</h4>
			@endif
		  </tbody>
		</table><!-- /.table -->
	  </div><!-- /.mail-box-messages -->
	</div><!-- /.box-body -->
	<div class="no-padding">
	  <div class="mailbox-controls">
		<!-- Check all button -->
		<div class="btn-group">
		  <button class="btn btn-info btn-sm checkbox-toggle js-list-main-checkbox" style="line-height: 10px;"><i class="fa fa-square-o"></i></button>
		  <button class="btn btn-info btn-sm js-del-inbox-list-mail" style="line-height: 10px;"><i class="fa fa-trash-o"></i></button>
		</div><!-- /.btn-group -->
		
	  </div>
	</div>
  </div><!-- /. box -->
</div><!-- /.col -->
		  