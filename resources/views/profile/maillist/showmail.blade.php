<!doctype html>
<html>
    <head>
		<meta charset="utf-8" />
        <title>Medcubics Software</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta content="Medcubics Medical Billing Software" name="description" />
        <meta content="Medcubics" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php App\Http\Helpers\CssMinify::minifyCss(); ?>
        {!! HTML::style('css/'.md5("css_cache").'.css') !!}
		{!! HTML::style('https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700') !!} 
        {!! HTML::style('https://fonts.googleapis.com/css?family=Maven+Pro:400,500') !!}
		<?php App\Http\Helpers\CssMinify::minifyJs('common_js'); ?>
        {!! HTML::script('js/'.md5("common_js").'.js') !!}  
        {!! HTML::script('js/mailbox.js') !!}
		 <script>
			var api_site_url = '{{url('/')}}';
			setTimeout(function(){ 
				$("#js_loading_image").addClass("hide");
				$(".js_body").removeClass("hide");
			}, 800);
        </script>	
    </head>
	<?php //dd($msg_details); ?>
	<body style="overflow:hidden;"> 
		<div id="js_loading_image" class="col-xs-offset-2 med-green font26 font600">
			<i class="fa fa-spinner fa-spin med-green"></i>
		</div>
		<section class="content-header js_body hide">
			<h1>
				<small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="mail" id="livicon-25" style="width: 16px; height: 16px;"></i>Message</small>
			</h1>
		</section>
		<div class="box-body-block no-padding js_body hide"> <!--Background color for Inner Content Starts -->        
			<div class="col-lg-12 no-padding" style="background: #00877f"> <!-- Mailbox Toolbar Starts -->
				@if($msg_details->showed_from !="trash")
				<div class="btn-group" style="display:initial;">
					<button type="button" class="btn btn-mail btn-flat js_reply_mail" data-url="{{ url('profile/maillist/replymailprocess')}}" data-mailid = "{{$msg_details->message_id}}"><i class="fa fa-reply"></i>Reply</button>
					 <button class="btn btn-mail btn-flat js_reply_all_mail" data-mailid = "{{$msg_details->message_id}}" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-reply-all"></i>Reply All</button>
					<button class="btn btn-mail btn-flat js_reply_all_mail" data-mailid = "{{$msg_details->message_id}}" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-mail-forward"></i> Forward</button> 
				</div>
				@endif
				@if(count($lastlabeldet) >0)
				<span class="btn-group">
					<button type="button" style="border:none;" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown"><!--i class="fa fa-tags"></i--><i class="fa fa-th-large"></i> Categorize</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="left:25%;">
						@foreach($lastlabeldet as $lastlabeldet_vals)
						<?php $lastlabeldet_vals->id = App\Http\Helpers\Helpers ::getEncodeAndDecodeOfId($lastlabeldet_vals->id,'encode'); ?>
							<li class="js_assign_category" data-access ="popup" data-value="categorize" data-id="{{ $msg_details->message_id }}" data-from="{{ @$msg_details->showed_from }}" id="{{ $lastlabeldet_vals->id }}"><a href="javascript:void(0);">
							<span style="background:{{$lastlabeldet_vals->label_color}};">&emsp;&nbsp;</span>&nbsp;{!! $lastlabeldet_vals->label_name !!}</a></li>
						@endforeach
						<li class="js_assign_category" data-access ="popup" data-value="categorize" data-id="{{ $msg_details->message_id }}" data-from="{{ @$msg_details->showed_from }}" id="remove_all"><a href="javascript:void(0);">Clear Category</a></li>
					</ul>
				</span>
				@endif
				@if($msg_details->showed_from =="inbox" ||  $msg_details->showed_from =="label")
				<span class="btn-group js-label-move-dropdown">
					<button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
					{!! Form::hidden('curr_mail_id',@$msg_details->message_id,['class'=>'form-control input-sm','id'=>'curr_mail_id']) !!}
					@if($msg_details->showed_from =="inbox")
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						@foreach($lastlabeldet as $lastlabeldet_val)
						<li class="js-move-to-label" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="label_{{ @$lastlabeldet_val->label_id }}"><a href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
						@endforeach
						
					</ul>
					@elseif($msg_details->showed_from =="label")
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						<li class="js-move-to-label" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="label_0"><a href="javascript:void(0);">Inbox</a></li>
						@foreach($lastlabeldet as $lastlabeldet_val)
							@if($lastlabeldet_val->label_id != @$msg_details->label_id)
							<li class="js-move-to-label" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="label_{!! $lastlabeldet_val->label_id !!}"><a  href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
							@endif
						@endforeach
					</ul>
					@endif
				</span>
				@endif
				@if($msg_details->showed_from =="inbox" ||  $msg_details->showed_from =="label")
				<span id="from_{{ $msg_details->showed_from }}" class="btn-group js-msglist-apply-dropdown" style="display:initial;">
					<div class="btn-group">
					<button type="button" class="btn btn-mail btn-flat dropdown-toggle"   data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_read"><a href="javascript:void(0);">Mark as Read</a></li>
						<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_unread"><a href="javascript:void(0);">Mark as Unread</a></li>
						<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_stared"><a href="javascript:void(0);">Mark as Star</a></li>
						<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_unstared"><a href="javascript:void(0);">Mark as UnStar</a></li>
					</ul>
					</div>
				</span>
				@elseif($msg_details->showed_from =="send")
				<span id="from_sent" class="js-msglist-apply-dropdown">
					<div class="btn-group">
						<button type="button" class="btn btn-mail btn-flat dropdown-toggle" style="border-left: 0px;" data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
							<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_stared"><a href="javascript:void(0);">Mark as Star</a></li>
							<li class="js-apply-list-msg" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="mark_as_unstared"><a href="javascript:void(0);">Mark as UnStar</a></li>
						</ul>
					</div>
				</span>
				@elseif($msg_details->showed_from =="trash")
			   <span id="from_trash" class="btn-group js-label-move-dropdown">
					<button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						@if($msg_details->moved_to =="inbox")
							<li class="js-move-to-label-from-trash" data-from="trash" data-to="inbox"  data-access ="popup" data-id="{{ $msg_details->message_id }}"  id="label_0"><a href="javascript:void(0);">Inbox</a></li>@endif
						@if($msg_details->moved_to =="send")
							<li class="js-move-to-label-from-trash" data-from="trash" data-to="send" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="label_0"><a href="javascript:void(0);">Sent</a></li>@endif
						@foreach($lastlabeldet as $lastlabeldet_val)
							<li class="js-move-to-label" data-access ="popup" data-id="{{ $msg_details->message_id }}" id="label_{{ @$lastlabeldet_val->label_id }}"><a href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
						@endforeach
					</ul>
				</span>
				@endif
				
			</div><!-- /.btn-group -->
		</div> <!--Background color for Inner Content Starts -->
		<div class="box-header-view no-border js_body hide">
			<table class="table-responsive table-striped-view table">
				<tbody>
					<tr></tr>
					<tr>
						<td style="width:4%">From</td>
						<td>&nbsp;:&nbsp;</td>
						<td style="width:79%">{{ $to_details->name }} [ {{ $to_details->email }}] </td>
						<td style="width:17%"><span class="med-green">Sent </span>:&emsp;<span style="font-size:11px;">{{ $msg_details->sent_time }}</span></td>
					</tr>
					<tr></tr>
					 <tr>
						<td>To</td>
						<td>&nbsp;:&nbsp;</td>
						<td colspan="2">{{ $from_details }}</td>
					</tr>
					<tr></tr>
					<tr>
						<td>Subject</td>
						<td>&nbsp;:&nbsp;</td>
						<td colspan="2">{{ $msg_details->subject }}</td>
					</tr>
					@if(@$msg_details->attachment_file!='')                    
					<tr>
						<td>Attachment</td>
						<td>&nbsp;:&nbsp;</td>
						<td colspan="2"><a href="{{ url('/media/private_message/'.@$msg_details->attachment_file) }}" target="_blank" class="">Download Attachment <i class="fa fa-paperclip"></i></a></td>
					</tr>
                    @endif
					
					
				</tbody>
			</table>
			@if(@$msg_details->category_id!='') 
				<p colspan="3" style="color:#fff !important;background: {{ @$msg_details->category_id->label_color }};">{!! @$msg_details->subject !!}</p>
			@endif
		</div><!-- /.box-header -->
		<div class="box-header-view no-border js_body hide">
			<div class="box-body box box-view" style="height:320px;">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="color:#868686;"><?php echo $msg_details->message_body; ?></div>
			</div><!-- /.box-body -->
		</div><!-- /.box-header -->
		
	</body> 
</html>