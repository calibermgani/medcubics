<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding mail_list_part">
    <input type="hidden" id="js_unread_msg_count" value="{{ @$unread_msg_count }}" /> 
    <?php $count  = 0; $count_line  = 0; ?>
    @foreach($view_msg_det as $view_msg_det_key=>$view_msg_det_val)	
	@if($count == 0)
    <div class="btn-group pull-right">
        <button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @if(@$reply_mail_from !="Draft" && $view_msg_det_val->from !="trash_category_id")
			<li><a href="javascript:void(0)" class="js_reply_mail" data-url="{{ url('profile/maillist/replymailprocess')}}" ><i class="fa fa-reply"></i> Reply</a></li>
            <li><a href="javascript:void(0)" class="js_reply_all_mail" data-url="{{ url('profile/maillist/replymailprocess')}}" ><i class="fa fa-reply-all"></i> Reply All</a></li>
            <li><a href="javascript:void(0)" class="js_reply_all_mail" data-url="{{ url('profile/maillist/replymailprocess')}}" ><i class="fa fa-mail-forward"></i> Forward</a></li>
			@elseif(@$reply_mail_from =="Draft")
			<li><a href="javascript:void(0)" class="js_reply_mail" data-url="{{ url('profile/maillist/replymailprocess')}}" ><i class="fa fa-mail-forward"></i> Edit</a></li>
			@endif
            <li class="@if(@$reply_mail_from!='trash') js-del-all-mail @else js-del-all-trash-mail @endif">
			<a href="javascript:void(0)" data-id="{{  @$view_msg_det_val->message_id }}" class="js_current_delete" data-value="{{$reply_mail_from}}" ><i class="fa fa-trash"></i> Delete</a></li>

        </ul>
    </div>
    @endif
	
    @if(@$count == 0)
    <p class="margin-l-10 med-orange font20">{!! @$view_msg_det_val->subject !!} </p>

    <p class="font16 margin-t-m-5 margin-b-10 margin-l-10 font12">{!! @$view_msg_det_val->mail_add_cont_header !!} <span class="pull-right font12 padding-r-5">{!! @$view_msg_det_val->sent_datetime_dis !!}</span></p>   
    @endif
	
	@if(@$view_msg_det_val->category_id != '')
	 <p style="background: {{ @$view_msg_det_val->category_id->label_color }};color:#fff; padding: 4px 10px;">{!! @$view_msg_det_val->subject !!} </p>
	@endif
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding" @if($count == 0) style="border-top:0px solid #ddd;max-height: 350px; min-height: 350px; overflow-y: scroll" @endif>
         <div class="box-body-block no-shadow no-border no-padding"> 
           
            {!! Form::hidden('reply_mail_from',@$reply_mail_from,['class'=>'form-control input-sm','id'=>'reply_mail_from']) !!}
			<input type="hidden" name="current_mail_id" id="curr_mail_id" value="{{ @$curr_mail_id }}" />
			<div class="box-body-block no-shadow no-padding no-border">
                <div id="singlemail_{!! @$view_msg_det_val->message_id !!}" @if(@$view_msg_det_key==0) class="box no-border no-shadow" @else class="box no-border" @endif>
                     <div class="box-body-block no-padding no-border no-shadow">
                        @if($count == 0)
                        <div class="mailbox-read-info">
                            <p class="font12 margin-t-m-13 m-b-m-20">{!! @$view_msg_det_val->mail_add_cont !!}</p>
                        </div>
                        @elseif($count != 0)
                        <div class="mailbox-read-info">
                            
                            <p class="font12 margin-l-5 ">{!! @$view_msg_det_val->mail_add_cont_header !!} | <span class="med-green">{!! @$view_msg_det_val->sent_datetime_dis !!}</span></p>
                           
                            <p class="font12 margin-l-5 margin-t-m-8">{!! @$view_msg_det_val->mail_add_cont !!}</p>
                        </div>
                        @endif
                        @if(@$view_msg_det_val->attachment_file!='')  
							<p class="mailbox-attachments clearfix bg-aqua text-gray font600 padding-4"><span class="" style="background: #00877f; color:#fff; padding: 4px 10px">Message</span> <a href="{{ URL::to( '/media/private_message/' . @$view_msg_det_val->attachment_file)  }}" target="_blank" class="">Download Attachment <i class="fa fa-paperclip"></i></a></p>
						@endif
                        <div class="mailbox-read-message">
                            {!! @$view_msg_det_val->message_body !!}
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php $count ++; ?>
            @endforeach
        </div>
    </div>
</div><!-- /. box -->