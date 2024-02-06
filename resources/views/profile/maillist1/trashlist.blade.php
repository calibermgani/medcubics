<div class="col-md-9 mail_list_part" id="inbox_list">
  <div class="box box-view no-shadow">
	<div class="box-header-view">
	  <h3 class="box-title">Inbox</h3>
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
		  <button class="btn btn-info btn-sm js-del-inbox-list-mail" style="line-height: 10px;"><i class="fa fa-trash-o"></i></button>
		</div><!-- /.btn-group -->
		<div class="btn-group js-label-move-dropdown" style="display:none;">
		  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
			<li class="js-move-to-label-from-trash" id="label_0"><a style="color: black;" href="javascript:void(0);">Inbox</a></li>
			@foreach($lastlabeldet as $lastlabeldet_val)
				<li class="js-move-to-label-from-trash" id="label_{!! $lastlabeldet_val->id !!}"><a style="color: black;" href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
			@endforeach
		  </ul>
		</div>
		
	  </div>
	  <div class="table-responsive mailbox-messages">
		<table class="table table-hover table-striped">
		  <tbody>
			@if($message_trash_list_count > 0)
				@foreach($message_trash_list as $message_trash_list_val)
				<tr>
				  <td>
				  {!! Form::checkbox('message_sel_ids[]', $message_trash_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
				  </td>
				  <td class="mailbox-name"><a href="{{ url('profile/maillist/trash/'.$message_trash_list_val->message_id) }}">{!! @$message_trash_list_val->from_add_email !!}</a></td>
				  <td class="mailbox-subject"><b>{!! substr($message_trash_list_val->subject, 0, 25) !!}</b> - {!! @$message_trash_list_val->messagecontent_list !!}</td>
				  <td class="mailbox-attachment">@if($message_trash_list_val->attachment_file!='')<i class="fa fa-paperclip"></i>@endif</td>
				  <td class="mailbox-date">{!! @$message_trash_list_val->messagetimeago !!}</td>
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