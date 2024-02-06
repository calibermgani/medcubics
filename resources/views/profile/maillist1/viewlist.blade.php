<div class="col-md-9 mail_list_part">
    <div class="box box-primary col-md-9">
        <div class="box-header with-border">
            <h3 class="box-title">Read Mail</h3>
        </div><!-- /.box-header -->
        {!! Form::hidden('curr_mail_id',$curr_mail_id,['class'=>'form-control input-sm','id'=>'curr_mail_id']) !!}
        {!! Form::hidden('reply_mail_from',$reply_mail_from,['class'=>'form-control input-sm','id'=>'reply_mail_from']) !!}
        <div class="box-body no-padding">
            <div class="mailbox-read-info" style="margin-bottom: 10px;">
                <h3>
                    {!! $mail_subject !!}
                    <span class="mailbox-read-time pull-right">
                        <button @if($reply_mail_from!='trash') class="btn btn-default js-del-all-mail" @else class="btn btn-default js-del-all-trash-mail" @endif><i class="fa fa-trash-o"></i> Delete</button>
                    </span>
                </h3>
            </div><!-- /.mailbox-read-info -->
        </div><!-- /.box-body -->


        @foreach($view_msg_det as $view_msg_det_key=>$view_msg_det_val)

        <div id="singlemail_{!! $view_msg_det_val->message_id !!}" @if($view_msg_det_key==0) class="box box-primary" @else class="box box-primary collapsed-box" @endif>
             <button class="btn btn-box-tool" data-widget="collapse"><i @if($view_msg_det_key==0) class="fa fa-minus" @else class="fa fa-plus" @endif ></i></button>
            <span style="margin-right: 15px; color: black; font-weight: bold;">{!! $view_msg_det_val->mail_add_cont_header !!}</span>
            <span class="pull-right" style="margin-right: 15px; color: black; font-weight: bold;">{!! $view_msg_det_val->sent_datetime_dis !!}</span>
            <div class="box-body no-padding">
                @if($view_msg_det_val->mail_add_cont != "")
                <div class="mailbox-read-info">
                    <h5>{!! $view_msg_det_val->mail_add_cont !!}</h5>
                </div>
                @endif
                @if($reply_mail_from!='trash')
                <div class="mailbox-controls with-border pull-right">
                    <div class="btn-group">
                        <button id="delmail_{!! $view_msg_det_val->message_id !!}" class="btn btn-default btn-sm js-del-single-mail" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
                        <button id="replymail_{!! $view_msg_det_val->message_id !!}" class="btn btn-default btn-sm js-reply-single-mail" data-toggle="tooltip" title="Reply"><i class="fa fa-reply"></i></button>
                        @if($view_msg_det_val->reply_all_process == "yes")
                        <button id="replyallmail_{!! $view_msg_det_val->message_id !!}" class="btn btn-default btn-sm js-reply-all-mail" data-toggle="tooltip" title="Reply All"><i class="fa fa-reply-all"></i></button>
                        @endif
                    </div>
                </div>
                <div class="box-body no-padding">
                </div><!-- /.box-body -->
                @endif
                <div class="mailbox-read-message">
                    {!! $view_msg_det_val->message_body !!}
                </div>
            </div>
            @if($view_msg_det_val->attachment_file!='')
            <div class="box-footer">
                <ul class="mailbox-attachments clearfix">
                    <li>
                        <div class="mailbox-attachment-info">
                            <a href="{{ URL::to( '/media/private_message/' . $view_msg_det_val->attachment_file)  }}" target="_blank" class="mailbox-attachment-name">Click To Download <i class="fa fa-paperclip"></i></a>
                        </div>
                    </li>
                </ul>
            </div>
            @endif

            <!--<hr style="padding-top: 10px; padding-bottom: 10px; border-top-width: 4px; padding-left: 0px; margin-left: 15px; margin-right: 15px;">-->
        </div>
        @endforeach

    </div><!-- /. box -->
</div><!-- /.col -->