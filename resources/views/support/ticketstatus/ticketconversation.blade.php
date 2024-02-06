@if(@$page!='view_ticket')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">	       
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 no-padding">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 med-green no-padding font600">Title&nbsp;:&nbsp;<span class="">{{ $get_ticket->title }} </span> </div> 
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 med-green font600">Status&nbsp;:&nbsp;<span class="">
                    {{ $get_ticket->status }}
                </span> 
			</div>
            @if(@Auth::user()->id !='')
            <div class="med-green pull-right font600">Email ID&nbsp;:&nbsp;<span class="">{{ $get_ticket->email_id }}
                </span> </div> 
            @endif
        </div>       
    </div>	
</div>
@endif


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4">
    <!-- DIRECT CHAT -->
    <div class="direct-chat direct-chat-warning">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
            <!-- Message. Default to the left -->
            <?php	$loggedname = '';  ?>
            @foreach(@$get_ticket_detail as $detail)
            <?php 
				$parent_class 	= ($detail->posted_by == 'User') ? "direct-chat-name pull-left med-green" : "direct-chat-name pull-right med-orange"; 
				$current_class 	= ($detail->posted_by == 'User') ? "left" : "right"; 
				$current_insideclass = ($detail->posted_by == 'User') ? "direct-chat-timestamp pull-right" : "direct-chat-timestamp pull-left"; 
				$detail_id = '';
				$detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($detail->id,'encode');
				
				$created_time = strtotime($detail->created_at)+60*60;
				$current_time = strtotime(date('Y-m-d h:i:s'));

				if(@$detail->postedby_id == 0) {
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
                        @if($detail->posted_by =='User' && in_array($detail->id,$getlastticket) &&  $created_time>$current_time && $get_ticket->status != 'Closed')
							<a class="js-delete-reply margin-r-5" style="border-right:1px solid #ccc; padding-right: 8px; " data-text="Are you sure you want to delete?"  data-replyid="{{ $detail_id }}">
								<i class="fa fa-trash form-cursor" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i>
							</a>
							@endif  
							@if($detail->attach_details !='')
							<a class="js_delete_confirm" style="border-right:1px solid #ccc; padding-right: 8px; padding-left: 4px;" href="#" data-placement="bottom"  data-toggle="tooltip" data-original-title="View Attachment" onClick="window.open('{{ url('getticketdocument/'.$detail_id) }}', '_blank')" ><i class="fa fa-clipboard form-cursor"></i></a>
                        @endif  
                        <span class="p-l-5 med-gray"> {{ App\Http\Helpers\Helpers::dateFormat($detail->created_at,'datetime') }}</span> </span>
                </div><!-- /.direct-chat-info -->
                <?php
					$img_details = [];
					$img_details['module_name']=(@Auth::user ()->practice_user_type == "customer") ? 'customers' : 'user';
					$img_details['file_name']=@$detail->posted_user->avatar_name.'.'.@$detail->posted_user->avatar_ext;
					$img_details['practice_name']="admin";
					$img_details['class']='direct-chat-img';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
                {!! $image_tag !!}

                <div class="direct-chat-text" style="word-wrap: break-word;">
                    {{ $detail->description }} 
                </div><!-- /.direct-chat-text -->                                                   												                                                   
            </div><!-- /.direct-chat-msg -->                                               
            @endforeach
        </div><!--/.direct-chat-messages-->                                                                                
    </div><!--/.direct-chat -->
</div>

<input type="hidden" name="ticket_id" value="{{ $get_ticket->ticket_id }}">
@if($get_ticket->status != 'Closed')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding"><a href="javascript:void(0)" class="js_call_reply_form med-white med-bg-green padding-4-15">Reply</a></div>
@endif