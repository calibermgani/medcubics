<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 mail_list_part  no-padding margin-t-m-10" id="draft_list">
    <div class="box box-view no-shadow no-border-radius" style="border:1px solid #e6e6e8;"><!-- Box Starts -->
        <div class="box-body no-padding">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 no-padding" style="border-right:1px solid #e6e6e8;"> <!-- Col Email Listing Starts -->
                <div class="box box-view no-shadow no-border"><!-- Box Starts -->
                    <div class="box-header-view no-border-radius with-border no-padding no-background">
                            <div class="has-feedback">
                                <input type="text" onfocus="this.value = this.value;" class="js_input_mail_search form-control input-sm-header-billing no-border-radius" style="border-left: 0px; border-right: 0px;" placeholder="Search Mail" data-index="draft">
                                <span class="glyphicon glyphicon-search text-gray form-control-feedback margin-t-m-5"></span>
                            </div>
                    </div><!-- /.box-tools -->
                    <div class="box-body text-center js_processing hide"><p><i class="fa fa-spinner fa-spin med-green"></i>&emsp;Processing</p></div>
                    <div class="js_listmail js_listmail_add"> <!-- Box Body Starts -->
						<div class="box-body mail-list-body"> <!-- Box Body Starts -->
							<div class="table-responsive mailbox-messages">
								<table class="table table-hover table-mail js_draft_table">
									<tbody>
								@if($message_draft_list_count > 0)
									@foreach($message_draft_list as $message_draft_list_val)
									<tr id="js_last_id" class="form-cursor js_list_view_url js_draft_open" data-value="draft" data-url="{{ url('profile/maillist/replymailprocess')}}" data-index="{{ @$message_draft_list_val->message_id }}">
											<td>
												{!! Form::checkbox('message_sel_ids[]', $message_draft_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
											</td>
											<td class=""><a class="form-cursor"><span class="text-gray">{!! @$message_draft_list_val->to_add_arr_emails !!}</span></a>
												<span class="mailbox-attachment pull-right font12">@if(@$message_draft_list_val->attachment_file!='')<i class="fa fa-paperclip  med-green"></i> @endif </span>
												<p><span class="font600 med-green font12">{!! substr($message_draft_list_val->subject, 0, 25) !!}</span> - {!! @$message_draft_list_val->message_body !!} <span class="pull-right font12">{{ $message_draft_list_val->messagetimeago }}</span></p>
											</td>                                        
										 <!--   <td class="mailbox-date" style="width: 20%">{!! @$message_inbox_list_val->messagetimeago !!}</td> -->
										</tr>
									@endforeach
								@else
											<h4 class="text-center med-green">No Messages Available</h4>
										@endif
									</tbody>
								</table><!-- /.table -->
							</div><!-- /.mail-box-messages -->
						</div><!-- /.mail-box-messages -->
                    </div>
                </div><!-- /.mail-box-messages -->
            </div><!-- /.box-header -->
            <div class="col-lg-7 col-md-7 col-sm-5 col-xs-12 no-padding" id="js_mail_view_draft"> 
            </div>
        </div><!-- /.box-body -->
    </div><!-- /. box -->
</div><!-- /. box -->