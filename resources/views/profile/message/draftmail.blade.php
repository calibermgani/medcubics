<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Medcubics Software</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta content="Medcubics Medical Billing Software" name="description" />
        <meta content="Medcubics" name="author" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php App\Http\Helpers\CssMinify::minifyCss(); ?>
        {!! HTML::style('css/'.md5("css_cache").'.css') !!}
        {!! HTML::style('https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600,700') !!} 
        {!! HTML::style('https://fonts.googleapis.com/css?family=Maven+Pro:400,500') !!}
		<style>
			.form-control.select2-container .select2-choices{height: 25px !important;}
			
		</style>
    </head>
    <body> 
		<div id="js_loading_image" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding col-xs-offset-2 med-green font26 font600">
			<i class="fa fa-spinner fa-spin med-green"></i>
		</div>
		<div id="compose_mail" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding hide"> 
			{!! Form::open(['name'=>'mailcomposerform','id'=>'mailcomposerform','files'=>true]) !!}
			{!! Form::hidden('mail_sent_type',"new",['class'=>'form-control input-sm','id'=>'mail_sent_type','class'=>'mail_sent_type']) !!}
			<div>
				<h3 class="box-title"><span id="compose_message_header"></span></h3>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background:#e7fcfd;margin-top:-10px;padding:10px 0px;">
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<a class="col-lg-11 col-md-11 col-sm-11 col-xs-11 med-btn" data-message-type="send" id="send_compose_message" data-message-id="{!! $message_data[0]->message_id !!}" data-page-type="draft" style="padding:16px 10px;">
						<span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><i class="fa fa-envelope-o" style="font-size:30px;padding-left:10px;margin-top: 5px;"></i></span>											
						<span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style=    "font-size:17px;padding-left:5px !important;" >Send</span>	
					</a> 
				</div>
				<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 form-horizontal">
					<div class="form-group">
						<a class="med-btn pull-left med-green" style="cursor:default;margin-top:3px;"><i class="fa fa-pencil"></i>To...</a>
						<span class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
						{!! Form::select('to_address', (array)@$users_list,$message_data[0]->sender_details->id, ['multiple'=>'multiple','name'=>'to_address', 'class' => 'form-control tokenfield select2 ']) !!}
						</span>
					</div>
					<div class="form-group">
						<span class="pull-left" style="color:#00877f;">Subject&emsp; </span>
						<span class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
							{!! Form::text('mail_subject',$message_data[0]->message_detail->subject ,['id'=>'mail_subject','class'=>'form-control  input-sm','placeholder'=>'Subject:']) !!} 
						</span>
					</div>
					<div style="margin-left: -15px;">                
						<div class="btn btn-default btn-file med-btn" style="line-height:20px;color:#00877f;">
							<i class="fa fa-paperclip"></i> Attachment
							{!! Form::file('attachment_file',null,['class'=>'form-control','id'=>'attachment_file']) !!}	
						</div>
						<span class="js_display_attachment"></span>
					</div>
					<div class="addmorefield"></div>
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<a class="col-lg-9 col-md-9 col-sm-9 col-xs-9 med-btn" id="send_compose_message" data-message-type="draft" data-message-id="{!! $message_data[0]->message_id !!}" data-page-type="draft" style="padding:10px 5px;">
						<i class="fa fa-pencil"></i>Draft	
					</a> 
					<a class="col-lg-9 col-md-9 col-sm-9 col-xs-9 med-btn" id="compose_mail_discard" style="padding:10px 0px;font-size:12px;top:10px;">
						<i class="fa fa-times"></i>Discard		
					</a> 
						
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
				<div class="box-body no-padding">
					<div class="form-group">
						<textarea name="compose-textarea" id="compose-textarea"><br>{!! @$mail_settings_datas->signature_content !!}</textarea>
					</div>
				</div>
			</div><!-- /. box -->
			{!! Form::close() !!}
		</div>
        <div class="control-sidebar-bg"></div>
        <?php App\Http\Helpers\CssMinify::minifyJs('common_js'); ?>
        {!! HTML::script('js/'.md5("common_js").'.js') !!}   
		<?php App\Http\Helpers\CssMinify::minifyJs('form_js'); ?>
        {!! HTML::script('js/'.md5("form_js").'.js') !!} <!-- select 2  --> 
        {!! HTML::script('js/profile.js') !!}
        {!! HTML::script('js/ckeditor/ckeditor.js') !!}
        
        <script>
			CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
			CKEDITOR.config.autoParagraph = false;
			CKEDITOR.config.fillEmptyBlocks = false;	// Prevent filler nodes in all empty blocks.

			// Prevent filler node only in float cleaners.
			CKEDITOR.config.removePlugins = 'save';
			$(document).ready(function () {
                var CK_EDITOR = CKEDITOR.replace('compose-textarea');
                var textbox = $("textarea#compose-textarea");
                CK_EDITOR.on('change', function (event) {
                    textbox.text(this.getData());
                });
			var message_data = '{!! preg_replace("/[\r\n]*/","",$message_data[0]->message_detail->message_body); !!}';
				CKEDITOR.instances['compose-textarea'].setData(message_data);
            });
			
			$('.select2').select2();
			
			var api_site_url = '{{url("/")}}';   
			
			setTimeout(function(){ 
				$("#js_loading_image").addClass("hide");
				$("#compose_mail").removeClass("hide");
			}, 800);
        </script>	
	    
    </body>
</html>