@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="mail"></i> Mail </small>
        </h1>
        <ol class="breadcrumb">            
            <li><a href="{{ url('profile/maillist/settings')}}"><i class="fa fa-gears" data-placement="bottom"  data-toggle="tooltip" data-original-title="Settings"></i></a></li>        
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
	<!-- Content Wrapper. Contains page content -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <!-- Content Header (Page header) -->        
	
        <!-- Main content -->
		@if(@$mail_settings_datas->signature=="yes")
			{!! Form::hidden('mail_settings_signature',@$mail_settings_datas->signature_content,['class'=>'form-control input-sm','id'=>'mail_settings_signature']) !!}
		@else
			{!! Form::hidden('mail_settings_signature',null,['class'=>'form-control input-sm','id'=>'mail_settings_signature']) !!}
		@endif
		<div style="display:none;" id="mail-success-alert-part" class="col-lg-12">
			<p class="alert alert-success"><span id="mail-success-alert-part-content"></span></p>
		</div>
         
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 no-padding">
              <a href="javascript:void(0);" class="btn btn-google-plus btn-block margin-b-10" id="compose_mail_display">Compose</a>
              <div class="box box-view no-shadow">
                  <div class="box-header-view">
                      <h3 class="box-title">Favorites</h3>
                      <div class="box-tools">
                          <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                  </div>
                  <div class="box-body no-padding">
                      <ul class="nav nav-pills nav-stacked">
                          <li @if($mail_list_option=='inbox') class="active" @endif><a  href="{{ url('profile/maillist')}}"><i class="fa fa-inbox"></i> Inbox @if($message_inbox_list_unread_count > 0)<span class="label label-danger pull-right">{!! $message_inbox_list_unread_count !!}</span>@endif</a></li>
                          <li @if($mail_list_option=='send') class="active" @endif><a  href="{{ url('profile/maillist/sent')}}"><i class="fa fa-envelope-o"></i> Sent</a></li>
                          <li @if($mail_list_option=='draft') class="active" @endif><a  href="{{ url('profile/maillist/draft')}}"><i class="fa fa-file-text-o"></i> Drafts</a></li>
                          <li @if($mail_list_option=='trash') class="active" @endif><a  href="{{ url('profile/maillist/trash')}}"><i class="fa fa-trash-o"></i> Trash</a></li>
                      </ul>
                  </div><!-- /.box-body -->
              </div><!-- /. box -->
              <div class="box box-view no-shadow">
                  <div class="box-header-view">
                      <h3 class="box-title">Labels</h3>
                      <div class="box-tools">
                          <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                  </div>
                  <div class="box-body no-padding">
                      <ul class="nav nav-pills nav-stacked js-mail-labels-list">
                          <li class="js-add-new-label font600"><a href="javascript:void(0);" class="med-green"><i class="fa fa-plus-circle"></i>Add New</a></li>
                                            <!--<li><a href="#"><i class="fa fa-star text-yellow"></i>Important</a></li>-->
                          @foreach($lastlabeldet as $lastlabeldet_val)
                          <li @if(@$mail_list_option_val == $lastlabeldet_val->label_name) class="active" @endif ><a href="{{ url('profile/maillist/other/'.$lastlabeldet_val->id)}}"><i class="fa fa-circle-o text-light-blue"></i> {!! $lastlabeldet_val->label_name !!}</a></li>
                          @endforeach
                      </ul>
                  </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
			
            <div id="add_new_label_modal" class="modal fade in">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add New Label</h4>
                        </div>
                        <div class="modal-body form-horizontal">
                            <div class="form-group" style="margin-left: 0px; margin-right: 0px;">
                                <label class="col-lg-2 col-md-2 col-sm-3 control-label" for="title" style="margin-bottom: 10px;">Name</label>
                                <div class="col-lg-6 col-md-6 col-sm-6" style="margin-bottom: 10px;">
                                    <input id="label_name" class="form-control" type="text" name="label_name">

                                    <span id='label_name_err' style='display:none;'><small style='color:#a94442;' class='help-block' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'><span id="label_name_err_content"></span></small></span>

                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-3" style="margin-bottom: 10px;">
                                    <button type="button" class="btn btn-medcubics add-new-label-submit" style="margin-top: 0px;">Submit</button>
                                </div>
                            </div> 
                        </div>

                    </div>
                </div>
            </div>
            
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
			@include ('profile/maillist/composemail')
		  
		  </div><!-- /.row -->
		  
    </div>
</div>
@stop