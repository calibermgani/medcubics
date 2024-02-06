<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding" id="dynamic_detail_message">

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"> 
		<table class="table-responsive table no-bottom">
			<thead>                                
			<th>Message</th>
			</thead>
		</table>
	</div>
	@if($message_count > 0)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-8" style="padding: 0px 5px;">
		<div class="btn-group">
		
			<button type="button" id="new_compose_mail_display" data-url="{{ url('profile/message/replaymail/'.$inbox_message[0]->message_id )}}" class="btn btn-default"><i class="fa fa-mail-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Reply"></i></button>
			
			<button type="button" id="new_compose_mail_display" data-url="{{ url('profile/message/forwardmail/'.$inbox_message[0]->message_id)}}" class="btn btn-default"><i class="fa fa-mail-forward" data-placement="bottom"  data-toggle="tooltip" data-original-title="Forward"></i></button>
			
			<button type="button" class="btn btn-default"><i class="fa fa-trash message-trash" data-message-id="{!! $inbox_message[0]->message_id !!}"  data-listing-id="0" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></button>                                                                                          
		</div>

		<div class="btn-group pull-right">                               
			<div class="btn-group" data-placement="top"  data-toggle="tooltip" data-original-title="Move">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
					<i class="fa fa-folder"></i> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					@foreach($label_list as $lists)
					<li data-message-structure-id="{!! $inbox_message[0]->id !!}" data-label-id="{!! $lists->id !!}" class="set_label"><a href="javascript:void(0)">{!! $lists->label_name !!}</a></li>
					@endforeach
				</ul>
			</div> 

			<div class="btn-group" data-placement="top"  data-toggle="tooltip" data-original-title="Label">
				<button type="button" class="btn btn-default dropdown-toggle"   data-toggle="dropdown" aria-expanded="true">
					<i class="fa fa-flag"></i> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#">Dropdown link</a></li>
					<li><a href="#">Dropdown link</a></li>
				</ul>
			</div> 
		</div>                                                                                  
	</div>
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5 border-bottom-f0f0f0">                            
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
		   
			<?php 
				$filename = $inbox_message[0]->sender_details->avatar_name.'.'.$inbox_message[0]->sender_details->avatar_ext;
				$img_details = [];
				$img_details['module_name']='user';
				$img_details['file_name']=$filename;
				$img_details['practice_name']="admin";
				$img_details['style']='width:50px;height:50px;border-radius:50%;float:left;margin-right:10px;margin-bottom:10px;';
				
				$img_details['class']='';
				$img_details['alt']='blog-image';
				$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
			?>
		
			{!! $image_tag !!}

			<p class="no-bottom margin-t-2">{!! $inbox_message[0]->sender_details->email !!} <span class="pull-right font10">{!! date('D d/m/y',strtotime($inbox_message[0]->created_at)) !!} | {!! date('H:i',strtotime($inbox_message[0]->created_at)) !!}</span></p>
			<input type="hidden" id="current_message_id" value="{!! $inbox_message[0]->message_id !!}" />
			<p class="no-bottom med-green font600">{!! $inbox_message[0]->message_detail->subject !!}
				<span class="pull-right">
					@if($inbox_message[0]->message_detail->attachment_file !='')
						<a href="{!! url('media/private_message/'.$inbox_message[0]->message_detail->attachment_file) !!}" target="_blank" ><i class="fa fa-paperclip p-l-5 margin-r-5"></i></a>
					@endif
					
				</span>
			</p>                                                                                                                              
		</div>
	</div>
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">                            
		<p class="text-justify">
			{!! $inbox_message[0]->message_detail->message_body !!}    
		</p>
	</div>
	@else	
	<p class="text-center med-gray margin-t-10">No Message Found</p>
	@endif
</div>
