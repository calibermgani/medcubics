<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mail_list_part" id="sent_list">
  <div class="box box-view no-shadow">
	<div class="box-header-view with-border">
	  <h3 class="box-title">Sent</h3>
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
		
        <div id="from_sent" class="btn-group js-msglist-apply-dropdown" style="display: none;">
		  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" style="line-height: 10px;">Apply <span class="fa fa-caret-down"></span></button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
			<li class="js-apply-list-msg" id="mark_as_stared"><a style="color: black;" href="javascript:void(0);">Mark as Star</a></li>
			<li class="js-apply-list-msg" id="mark_as_unstared"><a style="color: black;" href="javascript:void(0);">Mark as UnStar</a></li>
		  </ul>
		</div>
		
	  </div>
	  <div class="table-responsive mailbox-messages">
		<table class="table table-hover table-striped">
		  <tbody>
			@if($message_sent_list_count > 0)
				@foreach($message_sent_list as $message_sent_list_val)
				<tr>
				  <td>
				  {!! Form::checkbox('message_sel_ids[]', $message_sent_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
				  </td>
				  <td class="mailbox-star js-make-star-sent" id="star_{!! $message_sent_list_val->message_id !!}"><a href="javascript:void(0);"><i @if($message_sent_list_val->sender_stared == "0") class="fa fa-star-o text-yellow" @else class="fa fa-star text-yellow" @endif></i></a></td>
				  <td class="mailbox-name"><a href="{{ url('profile/maillist/sent/'.$message_sent_list_val->message_id) }}">{!! @$message_sent_list_val->to_add_arr_emails !!}</a></td>
                                  <td class="mailbox-subject"><span class="font600">{!! substr($message_sent_list_val->subject, 0, 25) !!} </span>-  {!! @$message_sent_list_val->messagecontent_list !!}</td>
				  <td class="mailbox-attachment">@if($message_sent_list_val->attachment_file!='')<i class="fa fa-paperclip"></i>@endif</td>
				  <td class="mailbox-date">{!! @$message_sent_list_val->messagetimeago !!}</td>
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
		  