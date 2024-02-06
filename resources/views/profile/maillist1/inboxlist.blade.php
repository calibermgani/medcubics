<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mail_list_part" id="inbox_list" style="padding-right: 0px;">
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <h3 class="box-title">Inbox</h3>
            <div class="box-tools pull-right">
                <div class="has-feedback">
                    <input type="text" class="form-control input-sm-header-billing" placeholder="Search Mail" style="margin-top: 1px; background: #e7fcfd; border:0px;">
                    <span class="glyphicon glyphicon-search form-control-feedback margin-t-m-5"></span>
                </div>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body no-padding">
            <div class="mailbox-controls">
                <!-- Check all button -->
                <div class="btn-group">
                    <button class="btn btn-info btn-sm checkbox-toggle js-list-main-checkbox" style="line-height: 10px;"><i class="fa fa-square-o"></i></button>
                    <button class="btn btn-info btn-sm js-del-inbox-list-mail" style="line-height: 10px;"><i class="fa fa-trash-o"></i></button>
                </div>
                <!-- <div class="btn-group">		 
                  <button class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                  <button class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                </div> -->

                <div class="btn-group js-label-move-dropdown" style="display: none;">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" style="line-height: 10px;">Move <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        @foreach($lastlabeldet as $lastlabeldet_val)
                        <li class="js-move-to-label" id="label_{!! $lastlabeldet_val->id !!}"><a style="color: black;" href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div id="from_inbox" class="btn-group js-msglist-apply-dropdown" style="display:none;">
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li class="js-apply-list-msg" id="mark_as_read"><a href="javascript:void(0);">Mark as Read</a></li>
                        <li class="js-apply-list-msg" id="mark_as_unread"><a href="javascript:void(0);">Mark as Unread</a></li>
                        <li class="js-apply-list-msg" id="mark_as_stared"><a href="javascript:void(0);">Mark as Star</a></li>
                        <li class="js-apply-list-msg" id="mark_as_unstared"><a  href="javascript:void(0);">Mark as UnStar</a></li>
                    </ul>
                </div>

<!--<button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>-->
                <!--
                <div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                        <button class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                        <button class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                </div>
                -->
            </div>
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <tbody>
                        @if($message_inbox_list_count > 0)
                        @foreach($message_inbox_list as $message_inbox_list_val)
                        <tr @if($message_inbox_list_val->recipient_read == "0") style="font-weight: bold;" @endif>
                             <td>
                                {!! Form::checkbox('message_sel_ids[]', $message_inbox_list_val->message_id, null, ['class'=>'chk flat-red']) !!}
                            </td>
                            <td class="mailbox-star js-make-star-inbox" id="star_{!! $message_inbox_list_val->message_id !!}"><a href="javascript:void(0);"><i @if($message_inbox_list_val->recipient_stared == "0") class="fa fa-star-o text-yellow" @else class="fa fa-star text-yellow" @endif></i></a></td>
                            <td class="mailbox-name"><a href="{{ url('profile/maillist/inbox/'.$message_inbox_list_val->message_id) }}">{!! @$message_inbox_list_val->from_add_email !!}</a></td>
                            <td class="mailbox-subject"><span class="font600">{!! substr($message_inbox_list_val->subject, 0, 25) !!}</span> - {!! @$message_inbox_list_val->messagecontent_list !!}</td>
                            <td class="mailbox-attachment">@if($message_inbox_list_val->attachment_file!='')<i class="fa fa-paperclip"></i>@endif</td>
                            <td class="mailbox-date">{!! @$message_inbox_list_val->messagetimeago !!}</td>
                        </tr>
                        @endforeach
                        @else
                    <h4 class="text-center med-green">No Messages Available</h4>
                    @endif

                    </tbody>
                </table><!-- /.table -->
            </div><!-- /.mail-box-messages -->
        </div><!-- /.box-body -->
        <div class="no-padding">
            <div class="mailbox-controls">
                <!-- Check all button -->

                <div class="btn-group">
                    <button class="btn btn-info btn-sm checkbox-toggle js-list-main-checkbox" style="line-height: 10px;"><i class="fa fa-square-o"></i></button>
                    <button class="btn btn-info btn-sm js-del-inbox-list-mail" style="line-height: 10px;"><i class="fa fa-trash-o"></i></button>
                    <!--<button class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                    <button class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>-->
                </div><!-- /.btn-group -->
                <!--<button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>-->
                <!--
                <div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                        <button class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                        <button class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                </div>
                -->
            </div>
        </div>
    </div><!-- /. box -->
</div><!-- /.col -->
