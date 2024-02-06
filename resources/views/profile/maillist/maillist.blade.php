@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Messages</span></small>
        </h1>
        <ol class="breadcrumb"> 
            @if($checkpermission->check_url_permission('profile/maillist/settings') == 1)
            <li><a href="{{ url('profile/maillist/settings')}}"><i class="fa fa-gears" data-placement="bottom"  data-toggle="tooltip" data-original-title="Settings"></i></a></li>  
            @endif
            <li><a href="#js-help-modal" data-url="{{url('help/message')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Ends -->
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-20"> <!-- Inner Content for full width Starts -->
    <div class="box-body-block no-padding"> <!--Background color for Inner Content Starts -->        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"> <!-- Full Col Starts  -->

            @if(@$mail_settings_datas->signature=="yes")
            {!! Form::hidden('mail_settings_signature',@$mail_settings_datas->signature_content,['class'=>'form-control input-sm','id'=>'mail_settings_signature']) !!}
            @else
            {!! Form::hidden('mail_settings_signature',null,['class'=>'form-control input-sm','id'=>'mail_settings_signature']) !!}
            @endif
            <div  style="display:none;" id="mail-success-alert-part" class="col-lg-12 success_alert">
                <p class="alert alert-success"><span id="mail-success-alert-part-content" class="success_alert_part" ></span></p>
            </div>

            <div class="col-lg-12 margin-t-m-3" style="background: #00877f"> <!-- Mailbox Toolbar Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					 <?php $current_page = Route::getFacadeRoot()->current()->uri();  ?>
					@if(strpos($current_page, 'draft') !== false)
					<div class="btn-group">
                        <button type="button" class="btn btn-mail btn-flat" id="new_compose_mail_display" data-url="{{ url('profile/maillist/composemail')}}" style="border-left: none;"><i class="fa fa-pencil"></i> Compose</button>
						 @if($message_draft_list_count>0)
							<button type="button" class="btn btn-mail btn-flat js_reply_mail" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-reply"></i> Edit</button>
						 @endif
                    </div>
					@else
					<div class="btn-group">
						<button type="button" class="btn btn-mail btn-flat" id="new_compose_mail_display" data-url="{{ url('profile/maillist/composemail')}}" style="border-left: none;"><i class="fa fa-pencil"></i> Compose</button>
						@if($mail_list_option !="trash" && $checkpermission->check_url_permission('profile/maillist/sent') == 1)
						 <button type="button" class="btn btn-mail btn-flat js_reply_mail" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-reply"></i> Reply</button>
						 <button type="button" class="btn btn-mail btn-flat js_reply_all_mail" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-reply-all"></i> Reply All</button>
						<button type="button" class="btn btn-mail btn-flat js_reply_all_mail" data-url="{{ url('profile/maillist/replymailprocess')}}"><i class="fa fa-mail-forward"></i> Forward</button> 
						@endif
					</div>
					@endif
					
                    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
                    <div class="btn-group" class="mailbox-controls">
                        @if(($mail_list_option =="inbox" || $mail_list_option =="label") && (@$message_inbox_list_count >0 ||  @$message_label_list_count >0) )
                        <button class="btn btn-mail btn-flat checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i> Check All</button>
                        <button class="btn btn-mail btn-flat js-del-{{ @$mail_list_option}}-list-mail" data-value="{{ @$mail_list_option}}"><i class="fa fa-trash"></i> Delete</button>
                        @if(count(@$lastlabeldet)>0)
						<span class="btn-group js-label-move-dropdown">
                            <button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
                            @if($mail_list_option =="inbox" && @$message_inbox_list_count >0)
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                @foreach($lastlabeldet as $lastlabeldet_val)
								<?php $id = App\Http\Helpers\Helpers ::getEncodeAndDecodeOfId($lastlabeldet_val->id,'encode'); ?>
                                <li class="js-move-to-label" data-to="label" id="label_{{ $id }}"><a href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
                                @endforeach
                            </ul>
                            @elseif($mail_list_option =="label" &&  @$message_label_list_count >0)
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <li class="js-move-to-label" id="label_0"><a href="javascript:void(0);">Inbox</a></li>
                                @foreach($lastlabeldet as $lastlabeldet_val)
								<?php $idr = App\Http\Helpers\Helpers ::getEncodeAndDecodeOfId($lastlabeldet_val->id,'encode'); ?>
								@if($idr != $label_id)
                                <li class="js-move-to-label" data-from="label" id="label_{{ $idr }}"><a  href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
                                @endif
                                @endforeach
                            </ul>
                            @endif
                        </span>
						 @endif
                        <span  class="btn-group js-msglist-apply-dropdown" @if(@$message_inbox_list_count >0)  id="from_inbox" @else id ="from_label_{{ @$lastlabeldet_val->label_id }}" @endif >
                            <div class="btn-group">
                            <button type="button" class="btn btn-mail btn-flat dropdown-toggle"   data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <li class="js-apply-list-msg" id="mark_as_read"><a href="javascript:void(0);">Mark as Read</a></li>
                                <li class="js-apply-list-msg" id="mark_as_unread"><a href="javascript:void(0);">Mark as Unread</a></li>
                                <li class="js-apply-list-msg" id="mark_as_stared"><a href="javascript:void(0);">Mark as Star</a></li>
                                <li class="js-apply-list-msg" id="mark_as_unstared"><a href="javascript:void(0);">Mark as UnStar</a></li>
                            </ul>
                            </div>
                        </span>
                        
                        @elseif($mail_list_option =="send" &&  @$message_sent_list_count >0)
						<button class="btn btn-mail btn-flat checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i> Check All</button>
                        <button class="btn btn-mail btn-flat js-del-sent-list-mail" data-value="sent"><i class="fa fa-trash"></i> Delete</button>
                        <span id="from_sent" class="js-msglist-apply-dropdown" style="position:relative;float:left;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-mail btn-flat dropdown-toggle" style="border-left: 0px;" data-toggle="dropdown">Apply <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <li class="js-apply-list-msg" id="mark_as_stared"><a href="javascript:void(0);">Mark as Star</a></li>
                                <li class="js-apply-list-msg" id="mark_as_unstared"><a href="javascript:void(0);">Mark as UnStar</a></li>
                            </ul>
                            </div>
                        </span>
      
                        
                        @elseif($mail_list_option =="draft" &&  @$message_draft_list_count >0)
                        <button class="btn btn-mail btn-flat checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i> Check All</button>
                        <button class="btn btn-mail btn-flat js-del-draft-list-mail" data-value="draft"><i class="fa fa-trash-o"></i> Delete</button>
                        @elseif($mail_list_option =="trash" && @$message_trash_list_count >0)
						<button class="btn btn-mail btn-flat checkbox-toggle js-list-main-checkbox"><i class="fa fa-square-o"></i> Check All</button>
                        <button class="btn btn-mail btn-flat js-del-trash-list-mail" data-value="trash"><i class="fa fa-trash"></i> Delete</button>
						<span id="from_trash" class="js-label-move-dropdown" style="position:relative;float:left;">
						<div class="btn-group">
                            <button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown">Move <span class="fa fa-caret-down"></span></button>
							
							<ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="margin-left:0px;">
                               <li class="js-move-to-label-from-trash" data-from="trash" data-to="inbox" id="label_0" id="label_0"><a href="javascript:void(0);">Inbox</a></li>
								 @foreach($lastlabeldet as $lastlabeldet_val)
								 <?php $idrs = App\Http\Helpers\Helpers ::getEncodeAndDecodeOfId($lastlabeldet_val->id,'encode'); ?>
								<li class="js-move-to-label-from-trash" data-from="trash" data-to="label" id="label_{{$idrs}}"><a  href="javascript:void(0);">{!! $lastlabeldet_val->label_name !!}</a></li>
                                @endforeach
                            </ul>
						</div>
                        </span>
                        @endif
					@if($mail_list_option !="draft")
					<div class="btn-group">
						<button class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown"><i class="fa fa-filter"></i> Filter Email</button>
						
						<ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="margin-left:0px;">
							<li class="js_select_filter" data-index="DESC" data-value="date" ><a href="javascript:void(0);">Date</a></li>
							<li class="js_select_filter" data-index="DESC" data-value="from" ><a href="javascript:void(0);">From</a></li>
							<li class="js_select_filter" data-index="DESC" data-value="to" ><a href="javascript:void(0);">To</a></li>
							<li class="js_select_filter" data-index="DESC" data-value="subject" ><a href="javascript:void(0);">Subject</a></li>
							<li class="js_select_filter" data-index="DESC" data-value="categorize" ><a href="javascript:void(0);">Category wise</a></li>
						</ul>
					</div>
                    @endif
					<a href=""><button class="btn btn-mail btn-flat"><i class="fa fa-refresh"></i> Refresh</button></a>
                    </div><!-- /.btn-group -->
					@if($mail_list_option !="draft" && count($lastlabeldet) >0)
					<span class="btn-group dropdown">
                        <button type="button" class="btn btn-mail btn-flat dropdown-toggle" data-toggle="dropdown"><!--i class="fa fa-tags"></i--><i class="fa fa-th-large"></i> Categorize</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="left:25%;">
							@foreach($lastlabeldet as $lastlabeldet_vals)
							<?php $lastlabeldet_vals->id = App\Http\Helpers\Helpers ::getEncodeAndDecodeOfId($lastlabeldet_vals->id,'encode'); ?>
								<li class="js_assign_category" data-value="categorize" data-from="{{ $mail_list_option}}" id="{{ $lastlabeldet_vals->id }}"><a href="javascript:void(0);">
								<span style="background:{{$lastlabeldet_vals->label_color}};">&emsp;&nbsp;</span>&nbsp;{!! $lastlabeldet_vals->label_name !!}</a></li>
							@endforeach
							<li class="js_assign_category" data-value="categorize" data-from="{{ $mail_list_option}}" id="remove_all"><a href="javascript:void(0);">Clear Category</a></li>
						</ul>
					</span>
					@endif
                </div>
               

            </div> <!-- Mailbox Toolbar Ends -->

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 no-padding" style="border-bottom: 1px solid #e6e6e8; background:#f6f7f8"><!-- Favorites Col Starts -->
                <div class="box box-view no-shadow no-border no-padding" style="background:#f6f7f8"><!-- Box Starts -->
                    <div class="bg-white" style="min-height: 100px;">
                        <h3 class="padding-4 no-bottom m-b-m-10 med-orange margin-l-5" style="font-size:18px;">John, Britto</h3>
                        {!! HTML::image('img/profile-pic.jpg',null,['class'=>'img-border-sm margin-r-20','style'=>'width:50px; height:50px; margin-left:10px; margin-top:15px;']) !!}
                        <p  style="padding-top:15px"> <span class="med-green font600">Admin</span></p>
                        <p style="margin-top:-10px; padding-right:10px;"> <span style="color:#646464">Be Happy with what U have</span></p>
                    </div>
                    <div class="box-body mail-list">
                        <ul class="nav nav-pills nav-stacked">
                            @if($checkpermission->check_url_permission('profile/maillist') == 1)
                            <li @if($mail_list_option=='inbox') class="active" @endif><a  href="{{ url('profile/maillist')}}"><i class="fa fa-inbox"></i> Inbox @if(@$message_inbox_list_unread_count > 0)<span class="label label-danger pull-right js_unread_msg_count_show">{{ @$message_inbox_list_unread_count }}</span>@endif</a></li>
                            @endif
                            @if($checkpermission->check_url_permission('profile/maillist/sent') == 1)
                            <li @if($mail_list_option=='send') class="active" @endif><a  href="{{ url('profile/maillist/sent')}}"><i class="fa fa-envelope-o"></i> Sent @if(@$message_sent_list_count > 0)<span class="label label-warning pull-right">{{ @$message_sent_list_count }}</span>@endif</a></li>
                            @endif
                            @if($checkpermission->check_url_permission('profile/maillist/draft') == 1)
                            <li @if($mail_list_option=='draft') class="active" @endif><a  href="{{ url('profile/maillist/draft')}}"><i class="fa fa-file-text-o"></i> Drafts @if(@$message_draft_list_count > 0)<span class="label label-warning pull-right">{{ @$message_draft_list_count }}</span>@endif</a></li>
                            @endif
                            @if($checkpermission->check_url_permission('profile/maillist/trash') == 1)
                            <li @if($mail_list_option=='trash') class="active" @endif><a  href="{{ url('profile/maillist/trash')}}"><i class="fa fa-trash-o"></i> Trash @if(@$message_trash_list_count > 0)<span class="label label-warning pull-right">{{ @$message_trash_list_count }}</span>@endif</a></li>
                            @endif
                            
                            
                        </ul>

                       
                        <ul class="nav nav-pills nav-stacked js-mail-labels-list nav-border">

                  <!--<li><a href="#"><i class="fa fa-star text-yellow"></i>Important</a></li>-->
                            @foreach($lastlabeldet as $lastlabeldet_val)
                            <!--li @if(@$mail_list_option_val == $lastlabeldet_val->label_name) class="active" @endif ><a href="{{ url('profile/maillist/other/'.$lastlabeldet_val->id)}}"><i class="fa fa-folder-open"></i> {!! $lastlabeldet_val->label_name !!}</a></li-->
							<li @if(@$mail_list_option_val == $lastlabeldet_val->label_name) class="active" @endif ><a href="{{ url('profile/maillist/category/'.$lastlabeldet_val->id)}}"> @if(@$lastlabeldet_val->label_image !='')<img src="{{ url('/media/labelimage/'.@$lastlabeldet_val->label_image) }}" style="width:10%;" /> @else <i class="fa fa-folder-open"></i>@endif &nbsp;{!! $lastlabeldet_val->label_name !!}</a></li>
                            @endforeach  
                            <li class="js-add-new-label font600" style="background: #f0f0f0;"><a href="javascript:void(0);" class="med-green"><i class="fa fa-plus"></i>New Label</a></li>
                        </ul>

                    </div><!-- /.box-body -->
                </div><!-- /. box Ends-->
            </div> <!-- Favorites Col Ends -->

            <div id="add_new_label_modal" class="modal fade in"> <!-- Modal for creating New Label Starts -->
                <div class="col-lg-6 col-md-6 col-sm-6 modal-dialog" style="left:30%;margin: 30px auto;position: relative;"> <!-- Modal Dialog Starts -->
                    <div class="modal-content"> <!-- Modal Content Starts -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add New Label</h4>
                        </div>
						
                        <div class="modal-body form-horizontal"> <!-- Modal Body Starts -->
						{!! Form::open(['name'=>'add_new_label','id'=>'add_new_label','files'=>true]) !!}
                            <div class="form-group margin-l-5">
                                <label class="col-lg-4 col-md-4 col-sm-4 margin-l-5 control-label-popup" for="title">Name</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
									{!! Form::text('label_name',null,['class'=>'form-control','id'=>'label_name']) !!}
                                    <span id='label_name_err' style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'><span id="label_name_err_content"></span></small></span>
                                </div>                                
                            </div>
							<div class="form-group margin-l-5">
                                <label class="col-lg-4 col-md-4 col-sm-4 margin-l-5 control-label-popup" for="title">Label image</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    {!! Form::file('label_image',null,['class'=>'form-control','id'=>'label_image']) !!}
                                    <span id='label_name_err' style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'><span id="label_name_err_content"></span></small></span>
                                </div>                                
                            </div>
							<div class="form-group margin-l-5">
                                <label class="col-lg-4 col-md-4 col-sm-4 margin-l-5 control-label-popup" for="title">Categorize color</label>

								 <input class="form-control form-cursor" value="#00877F" type="hidden" id="label_color" name="label_color" style="padding: 0px;width: 20%;">
								 
								<div class="btn-group btn-group-sm">
								  <button id="demo2" class="btn btn-default" type="button"><span style="background-color:#000;" class="color-fill-icon dropdown-color-fill-icon"></span>&nbsp;<b class="caret"></b></button>
								</div>
								
                            </div>
							<div class="modal-footer">
								<button class="btn btn-medcubics-small add-new-label-submit" id="true" type="button">Submit</button>
								<button class="btn btn-medcubics-small" id="false" type="button" data-dismiss="modal">Cancel</button>
							</div>
						{!! Form::close() !!}	
                        </div> <!-- Modal Body Ends -->
                    </div> <!-- Modal Content Ends -->
                </div> <!-- Modal Dialog Ends -->
            </div> <!-- Modal for creating New Label Ends -->
			<div class="js_response_text hide"></div>
			<div class="js_listmail_add">
				@if($mail_list_option=='inbox')
					@include ('profile/maillist/inboxlist')
				@elseif($mail_list_option=='send')
					@include ('profile/maillist/sentlist')
				@elseif($mail_list_option=='draft')
					@include ('profile/maillist/draftlist')
				@elseif($mail_list_option=='trash')
					@include ('profile/maillist/trashlist')
				@elseif($mail_list_option=='view')
					@include ('profile/maillist/viewlist')
				@elseif($mail_list_option=='label')
					@include ('profile/maillist/labellist')
				@endif
			</div> 
        </div> <!-- Full Col Ends  -->
    </div> <!--Background color for Inner Content Starts -->
</div> <!-- Inner Content for full width Ends -->
@stop
