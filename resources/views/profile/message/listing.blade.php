<div class="col-lg-4 col-md-4 col-sm-9 col-xs-12 no-padding min-height-profile-messages-left" style="border-right:2px solid #cfdbe8;" >
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"> 
		<table class="table-responsive table no-bottom">
			<thead>                                
			<th style="border-right:1px solid #fff;" id="cat_type">Inbox</th>
		 
			</thead>
		</table>
	</div> 
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding" style="padding-left: 3px; padding-right:3px;" >
		{!! Form::text('message_filter',null,['class'=>'form-control no-border-radius no-b-l no-b-r no-b-t ','placeholder'=>'Search Mailbox','style'=>'line-height:30px;','data-page-type'=>'inbox']) !!}
	</div>
	<input type="hidden" id="current_listing" value="inbox" />
	<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
	<i class="fa fa-search pull-right med-gray" style="position: absolute; text-align: right; margin-top:35px;margin-right:0px; right:10px;"></i>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding  min-height-profile-messages" id="dynamic_listing">     @if($message_count > 0)                    
		<div class="mailbox-messages">
			<table class="table-responsive table-striped-view table table-hover table-mail js_inbox_table">
				<tbody>
					<?php $i=0; ?>
					@foreach($inbox_message as $list) 
					<tr id="{!! $i !!}" class="list_message @if($i == 0) active @endif" data-message="{!! $list->message_id !!}">
						<td>
							<a class="form-cursor" >
								<span class='@if($list->read_status == 0) text-black @if($i == 0) temp @endif @else text-gray @endif'>{!! $list->sender_details->email !!}</span>
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
</div>