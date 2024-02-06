<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding  min-height-profile-messages">    
	@if($message_count > 0)                    
		<div class="mailbox-messages">
			<table class="table-responsive table-striped-view table table-hover table-mail js_inbox_table">
				<tbody>
					<?php $i=0; ?>
					@foreach($inbox_message as $list) 
					<tr id="{!! $i !!}" class="list_message @if($i == 0) active @endif" data-message="{!! $list->message_id !!}">
						<td>
							<a class="form-cursor" >
								<span class='@if($list->read_status == 0 && $request_type == "inbox") text-black @else text-gray @endif'> @if(isset($list->receiver_details)){!! $list->receiver_details->email !!} @else {!! $list->sender_details->email !!} @endif</span>
							</a>
							<span class="mailbox-attachment pull-right font12">
								@if($list->message_detail->attachment_file !='')
									<i class="fa fa-paperclip  med-green"></i>
								@endif
								<span class="text-right">
									<?php $current_date = date('Y-m-d');
									$date1=date_create($list->created_at);
									$date2=date_create($current_date);
									$diff=date_diff($date1,$date2);
									$date_diff_count = $diff->format('%a') + 1;?>
									@if(date('Y-m-d',strtotime($list->created_at)) == date('Y-m-d'))
										{!! date('H:i a',strtotime($list->created_at)) !!}
									@elseif($date_diff_count < 7)
										{!! date('D H:i',strtotime($list->created_at)) !!}
									@elseif($date_diff_count > 7 && $date_diff_count < 30)
										{!! date('D d-m H:i',strtotime($list->created_at)) !!}
									@elseif($date_diff_count > 30)
										{!! date('D d-M-y',strtotime($list->created_at)) !!}
									@endif
								</span>
							</span>
							<p class="margin-t-m-5 margin-b-4"><span class="font600 med-green font12">{!! $list->message_detail->subject !!}</span></p>
							
						</td>                                        
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table><!-- /.table -->
		</div><!-- /.mail-box-messages -->
	@else
		<p class="text-center med-gray margin-t-10">No Message Found</p>
	@endif
</div>