<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 mail_list_part  no-padding margin-t-m-10" id="trash_list">
    <div class="no-shadow no-border-radius" style="border:1px solid #e6e6e8;"><!-- Box Starts -->
        <div class="box-body no-padding">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding" style="border-right:1px solid #e6e6e8;"> <!-- Col Email Listing Starts -->
                <div class="no-shadow no-border"><!-- Box Starts -->
					<div class="no-border-radius with-border no-padding no-background" style="font-size: 12px;">
						<div class="has-feedback">
							<input type="text" onfocus="this.value = this.value;" class="js_input_mail_search form-control input-sm-header-billing no-border-radius" style="border-left: 0px; border-right: 0px;" placeholder="Search Mail" data-index="trash">
							<span class="glyphicon glyphicon-search text-gray form-control-feedback margin-t-m-5"></span>
						</div>              
                        <p class="no-bottom bg-white js_listmail" style="border-top:0px solid #eceef2; padding-left: 5px; padding-bottom: 0px; margin-top:5px;">
							<span class="med-orange font600">
								<a class="form-cursor js_unread med-orange" data-index="trash" data-value="All" >All</a>&nbsp;&nbsp;
								<a class="form-cursor text-gray js_unread" data-index="trash" data-value="Read" >Read</a>&nbsp;&nbsp;
								<a class="form-cursor text-gray js_unread" data-index="trash" data-value="Unread" >Unread</a>
							</span>
							<span class="pull-right margin-r-5 margin-l-10">
								<a class="form-cursor text-gray js_check-in js_select_filter" data-index="DESC" data-value="{{ $getorder }}">Newest on top <i class="fa fa-long-arrow-up"></i></a>
							</span>
							<span class="pull-right margin-r-5 text-gray">By {{ $getorder }}<i class="fa fa-caret-down"></i></span>
							<input type="hidden" class="js_filter_by" value='{{ $getorder }}' />
							<input type="hidden" id="js_select_filter" value='DESC' />
                        </p>
                    </div><!-- /.box-tools -->
                    <div class="box-body text-center js_processing hide"><p><i class="fa fa-spinner fa-spin med-green"></i>&emsp;Processing</p></div>
                     <div class="js_listmail mail-list-body"> <!-- Box Body Starts -->
					@if($message_trash_list_count > 0)
					@foreach($message_trash_list as $message_trash_list_key =>$message_trash_list_val)
						<?php $current_messagetimeago  = $message_trash_list_val->messagetimeago; 
							$prev_messagetimeago  = ' ';  $next_messagetimeago  = ' '; ?>
						@if( $message_trash_list_key >0)
							<?php $prev_messagetimeago  = $message_trash_list[$message_trash_list_key-1]->messagetimeago; ?>
						@endif
						@if( $message_trash_list_count >$message_trash_list_key+1)
							<?php $next_messagetimeago  = $message_trash_list[$message_trash_list_key+1]->messagetimeago; ?>
						@endif
					@if($prev_messagetimeago != $current_messagetimeago)
					<div class="box box-view no-shadow no-border" style="margin-bottom: 0px;">
						<div class="box-header-view">
							<div class="box-tools" style="left:0px;">
								<button class="btn btn-box-tool" data-widget="collapse">[<i class="fa fa-minus"></i>]
								</button>
							</div><h3 class="box-title">&emsp;&emsp;{{ $message_trash_list_val->messagetimeago }}</h3>
						</div><!-- /.box-header -->
						
						<div class="box-body table-responsive"> <!-- Box Body Starts -->
							<div class="mailbox-messages">@endif
							<table class="table table-hover table-striped js_trash_table">
								<tbody>
									<tr id="js_last_id" class="js_list_view_url" data-value="trash" data-index="{{ @$message_trash_list_val->message_id }}" @if($message_trash_list_val->recipient_deleted == "1" && $message_trash_list_val->recipient_read == "0") style="background-color: #f3fffe;font-weight:bold; border-left:3px solid #ccc;"  @endif  >
										<td>
											{!! Form::checkbox('message_sel_ids[]', $message_trash_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
										</td>
										<td class=""><a class="form-cursor"><span class="text-gray">{!! @$message_trash_list_val->from_add_email !!}</span></a>
											<span class="mailbox-attachment pull-right font12">@if($message_trash_list_val->attachment_file!='')<i class="fa fa-paperclip  med-green"></i> @endif 
											<span style="height:15px;border:1px solid #ccc;border-radius:3px;background:{{ @$message_trash_list_val->category_id->label_color }};">&emsp;</span>
											</span>
											<p><span class="font600 med-green font12">{!! substr($message_trash_list_val->subject, 0, 25) !!}</span> - {!! @$message_trash_list_val->messagecontent_list !!} <span class="pull-right font12">{!! @$message_trash_list_val->messagetimeago !!}</span></p>
										</td>
									</tr>
								</tbody>
							</table><!-- /.table -->
						@if($current_messagetimeago != $next_messagetimeago)</div><!-- /.mail-box-messages -->
						</div><!-- /.mail-box-messages -->
						</div>@endif
							
						@endforeach
						@else
							<h4 class="text-center med-green">No Messages Available</h4>
						@endif
					</div>
                </div><!-- /.mail-box-messages -->
            </div><!-- /.box-header -->
            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding" id="js_mail_view_trash"> 
            </div>
        </div><!-- /.box-body -->
    </div><!-- /. box -->
</div><!-- /. box -->