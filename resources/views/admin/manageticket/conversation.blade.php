<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- DIRECT CHAT -->
    <div class="direct-chat direct-chat-warning">                                        
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
            <!-- Message. Default to the left -->    
			<?php $loggedname = '';  ?>
            @foreach($get_ticket_detail as $detail)
				@if($detail->description !="")
					<?php
						$parent_class 	= (@$detail->posted_by == 'User') ? "direct-chat-name pull-left med-green" : "direct-chat-name pull-right med-orange"; 
						$current_class 	= (@$detail->posted_by == 'User') ? "left" : "right"; 
						$current_insideclass = (@$detail->posted_by == 'User') ? "direct-chat-timestamp pull-right" : "direct-chat-timestamp pull-left"; 
						$detail_id	 	= '';
						$posted_by	 	= @$detail->posted_by;
						$detail_id 		= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($detail->id,'encode');
						
						if($detail->postedby_id == 0){				
							if($loggedname == '') {
								$name = $loggedname = App\Http\Controllers\Support\Api\TicketStatusApiController::ticketName($detail->ticket_id);
							} else {
								$name = $loggedname;
							}
						} else {
							$name = $detail->posted_user->name;
						}
					?>				
					<div class="direct-chat-msg {{ $current_class }}">
						<div class="direct-chat-info clearfix">
							<span class="{{ $parent_class }}">{{ @$name }}</span>
							<span class="{{ $current_insideclass }}">
								@if($detail->attach_details !='')<a class="" style="border-right:1px solid #ccc; padding-right: 8px; padding-left: 4px;" href="#" onClick="window.open('{{ url('getticketdocument/'.$detail_id) }}', '_blank')" ><i class="fa fa-clipboard form-cursor" data-placement="right" data-toggle="tooltip" data-original-title="Attachment" ></i></a>@endif     
								<span class="p-l-5 med-gray"> 
								{{ App\Http\Helpers\Helpers::dateFormat($detail->created_at,'datetime') }}
								</span> 
							</span>
						</div><!-- /.direct-chat-info -->
						<?php
							$img_details = [];
							$img_details['module_name']='user';
							$img_details['file_name']=@$detail->posted_user->avatar_name.'.'.@$detail->posted_user->avatar_ext;
							$img_details['practice_name']="";
							$img_details['class']='direct-chat-img';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
						{!! $image_tag !!}
						<div class="direct-chat-text" style="word-wrap:break-word">
							{{ $detail->description }} 
						</div><!-- /.direct-chat-text -->
					</div><!-- /.direct-chat-msg -->
				@endif
            @endforeach
        </div><!--/.direct-chat-messages-->
    </div><!--/.direct-chat -->
</div>

<input type="hidden" value="{{ @$posted_by }}" class="js_lastupdate" />
<input type="hidden" value="{{ $get_ticket->status }}" class="js_class_status" />