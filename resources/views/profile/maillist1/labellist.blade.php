<div class="col-md-9 mail_list_part" id="label_list">
  <div class="box box-primary">
	<div class="box-header with-border" style="margin-top: 4px; padding-top: 4px; margin-bottom: 4px;">
	  <h3 class="box-title">{!! $mail_list_option_val !!}</h3>
	  <div class="box-tools pull-right">
		<div class="has-feedback">
		  <input type="text" class="form-control input-sm" placeholder="Search Mail">
		  <span class="glyphicon glyphicon-search form-control-feedback"></span>
		</div>
	  </div><!-- /.box-tools -->
	</div><!-- /.box-header -->
	<div class="box-body no-padding">
	  <div class="mailbox-controls">
		<button class="btn btn-default btn-sm checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i></button>
		<div class="btn-group">
		  <button class="btn btn-default btn-sm js-del-inbox-list-mail"><i class="fa fa-trash-o"></i></button>
		</div><!-- /.btn-group -->
		<div class="btn-group js-label-move-dropdown" style="display:none;">
		  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
			<li class="js-move-to-label" id="label_0"><a style="color: black;" href="javascript:void(0);">Inbox</a></li>
			@foreach($lastlabeldet as $lastlabeldet_val)
				@if($lastlabeldet_val->id != $label_id)
					<li class="js-move-to-label" id="label_{!! $lastlabeldet_val->id !!}"><a style="color: black;" href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
				@endif
			@endforeach
		  </ul>
		</div>
		
		<div id="from_label_{!! $label_id !!}" class="btn-group js-msglist-apply-dropdown" style="display:none;">
		  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
			<li class="js-apply-list-msg" id="mark_as_read"><a style="color: black;" href="javascript:void(0);">Mark as Read</a></li>
			<li class="js-apply-list-msg" id="mark_as_unread"><a style="color: black;" href="javascript:void(0);">Mark as Unread</a></li>
			<li class="js-apply-list-msg" id="mark_as_stared"><a style="color: black;" href="javascript:void(0);">Mark as Star</a></li>
			<li class="js-apply-list-msg" id="mark_as_unstared"><a style="color: black;" href="javascript:void(0);">Mark as UnStar</a></li>
		  </ul>
		</div>
		
	  </div>
	  <div class="table-responsive mailbox-messages">
		<table class="table table-hover table-striped">
		  <tbody>
			@if($message_label_list_count > 0)
				@foreach($message_label_list as $message_label_list_val)
				<tr @if($message_label_list_val->recipient_read == "0") style="font-weight: bold;" @endif>
				  <td>
				  {!! Form::checkbox('message_sel_ids[]', $message_label_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
				  </td>
				  <td class="mailbox-star js-make-star-inbox" id="star_{!! $message_label_list_val->message_id !!}"><a href="javascript:void(0);"><i @if($message_label_list_val->recipient_stared == "0") class="fa fa-star-o text-yellow" @else class="fa fa-star text-yellow" @endif></i></a></td>
				  <td class="mailbox-name"><a href="{{ url('profile/maillist/inbox/'.$message_label_list_val->message_id) }}">{!! @$message_label_list_val->from_add_email !!}</a></td>
				  <td class="mailbox-subject"><b>{!! substr($message_label_list_val->subject, 0, 25) !!}</b> - {!! @$message_label_list_val->messagecontent_list !!}</td>
				  <td class="mailbox-attachment">@if($message_label_list_val->attachment_file!='')<i class="fa fa-paperclip"></i>@endif</td>
				  <td class="mailbox-date">{!! @$message_label_list_val->messagetimeago !!}</td>
				</tr>
				@endforeach
			@else
				<tr>
				  <td align="center" style="font-weight: bold;">No Message Available</td>
				</tr>
			@endif
			
		  </tbody>
		</table><!-- /.table -->
	  </div><!-- /.mail-box-messages -->
	</div><!-- /.box-body -->
	<div class="no-padding">
	  <div class="mailbox-controls">
		<!-- Check all button -->
		<button class="btn btn-default btn-sm checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i></button>
		<div class="btn-group">
		  <button class="btn btn-default btn-sm js-del-inbox-list-mail"><i class="fa fa-trash-o"></i></button>
		</div><!-- /.btn-group -->
	  </div>
	</div>
  </div><!-- /. box -->
</div><!-- /.col -->
		  