<style>

/** First **/

.ui-widget-content {
	border: 1px solid #aaaaaa;
	background: #ffffff 50% 50% repeat-x;
	color: #222222;
}
.ui-widget-content a {
	color: #222222;
}

/** second **/

/*!
 * bootstrap-tokenfield
 * https://github.com/sliptree/bootstrap-tokenfield
 * Copyright 2013-2014 Sliptree and other contributors; Licensed MIT
 */
@-webkit-keyframes blink {
  0% {
    border-color: #ededed;
  }
  100% {
    border-color: #b94a48;
  }
}
@-moz-keyframes blink {
  0% {
    border-color: #ededed;
  }
  100% {
    border-color: #b94a48;
  }
}
@keyframes blink {
  0% {
    border-color: #ededed;
  }
  100% {
    border-color: #b94a48;
  }
}
.tokenfield {
  height: auto;
  min-height: 34px;
  padding-bottom: 0px;
}
.tokenfield.focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, 0.6);
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, 0.6);
}
.tokenfield .token {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  display: inline-block;
  border: 1px solid #d9d9d9;
  background-color: #ededed;
  white-space: nowrap;
  margin: -1px 5px 5px 0;
  height: 22px;
  vertical-align: top;
  cursor: default;
}
.tokenfield .token:hover {
  border-color: #b9b9b9;
}
.tokenfield .token.active {
  border-color: #52a8ec;
  border-color: rgba(82, 168, 236, 0.8);
}
.tokenfield .token.duplicate {
  border-color: #ebccd1;
  -webkit-animation-name: blink;
  animation-name: blink;
  -webkit-animation-duration: 0.1s;
  animation-duration: 0.1s;
  -webkit-animation-direction: normal;
  animation-direction: normal;
  -webkit-animation-timing-function: ease;
  animation-timing-function: ease;
  -webkit-animation-iteration-count: infinite;
  animation-iteration-count: infinite;
}
.tokenfield .token.invalid {
  background: none;
  border: 1px solid transparent;
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  border-radius: 0;
  border-bottom: 1px dotted #d9534f;
}
.tokenfield .token.invalid.active {
  background: #ededed;
  border: 1px solid #ededed;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}
.tokenfield .token .token-label {
  display: inline-block;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-left: 4px;
  vertical-align: top;
}
.tokenfield .token .close {
  font-family: Arial;
  display: inline-block;
  line-height: 100%;
  font-size: 1.1em;
  line-height: 1.49em;
  margin-left: 5px;
  float: none;
  height: 100%;
  vertical-align: top;
  padding-right: 4px;
}
.tokenfield .token-input {
  background: none;
  width: 60px;
  min-width: 60px;
  border: 0;
  height: 20px;
  padding: 0;
  margin-bottom: 6px;
  -webkit-box-shadow: none;
  box-shadow: none;
}
.tokenfield .token-input:focus {
  border-color: transparent;
  outline: 0;
  /* IE6-9 */
  -webkit-box-shadow: none;
  box-shadow: none;
}
.tokenfield.disabled {
  cursor: not-allowed;
  background-color: #eeeeee;
}
.tokenfield.disabled .token-input {
  cursor: not-allowed;
}
.tokenfield.disabled .token:hover {
  cursor: not-allowed;
  border-color: #d9d9d9;
}
.tokenfield.disabled .token:hover .close {
  cursor: not-allowed;
  opacity: 0.2;
  filter: alpha(opacity=20);
}
.has-warning .tokenfield.focus {
  border-color: #66512c;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
}
.has-error .tokenfield.focus {
  border-color: #843534;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
}
.has-success .tokenfield.focus {
  border-color: #2b542c;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
}
.tokenfield.input-sm,
.input-group-sm .tokenfield {
  min-height: 30px;
  padding-bottom: 0px;
}
.input-group-sm .token,
.tokenfield.input-sm .token {
  height: 20px;
  margin-bottom: 4px;
}
.input-group-sm .token-input,
.tokenfield.input-sm .token-input {
  height: 18px;
  margin-bottom: 5px;
}
.tokenfield.input-lg,
.input-group-lg .tokenfield {
  min-height: 45px;
  padding-bottom: 4px;
}
.input-group-lg .token,
.tokenfield.input-lg .token {
  height: 25px;
}
.input-group-lg .token-label,
.tokenfield.input-lg .token-label {
  line-height: 23px;
}
.input-group-lg .token .close,
.tokenfield.input-lg .token .close {
  line-height: 1.3em;
}
.input-group-lg .token-input,
.tokenfield.input-lg .token-input {
  height: 23px;
  line-height: 23px;
  margin-bottom: 6px;
  vertical-align: top;
}
.tokenfield.rtl {
  direction: rtl;
  text-align: right;
}
.tokenfield.rtl .token {
  margin: -1px 0 5px 5px;
}
.tokenfield.rtl .token .token-label {
  padding-left: 0px;
  padding-right: 4px;
}

.tokenfield.form-control{
  padding-top: 5px;
}
</style>

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 hide" id="compose_mail"> 
{!! Form::open(['name'=>'mailcomposerform','id'=>'mailcomposerform','files'=>true]) !!}
{!! Form::hidden('users_list',$users_list,['class'=>'form-control input-sm','id'=>'users_list']) !!}     
{!! Form::hidden('mail_sent_type',null,['class'=>'form-control input-sm','id'=>'mail_sent_type']) !!}
  <div class="box box-view no-shadow">
	<div class="box-header-view">
	  <h3 class="box-title"><span id="compose_message_header"></span></h3>
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="form-group">		 
            {!! Form::text('to_address',null,['id'=>'to_address','class'=>'form-control input-sm-header-billing','placeholder'=>'To:','style'=>'height:24px;']) !!}
		</div>
                        
		<div class="form-group">
			{!! Form::text('mail_subject',null,['id'=>'mail_subject','class'=>'form-control input-sm-modal-billing','placeholder'=>'Subject:']) !!} 
		</div>
		
		<div class="form-group">
			<textarea name="compose-textarea" id="compose-textarea" class="form-control" style="height: 300px">
				
			</textarea>
		</div>
		
		<div class="form-group">
			<div class="btn btn-default btn-file" style="line-height:10px;">
			<i class="fa fa-paperclip"></i> Attachment
			{!! Form::file('attachment_file',null,['class'=>'form-control','id'=>'attachment_file']) !!} 
		</div>
		<!--<p class="help-block">Max. 32MB</p>-->
		</div>
	</div><!-- /.box-body -->
	<div class="box-footer">
	  <div class="pull-right">
		<button type="button" class="btn btn-default" id="add_draft_compose_mail" style="line-height:10px;"><i class="fa fa-pencil"></i> Draft</button>
		<button type="button" class="btn btn-twitter" id="send_compose_mail" style="line-height:10px;"><i class="fa fa-envelope-o"></i> Send</button>
	  </div>
	  <div class="pull-left">
              <button type="button" id="compose_mail_discard" class="btn btn-danger" style="line-height:10px;"><i class="fa fa-times"></i> Discard</button>
	  </div>
	</div><!-- /.box-footer -->
	<div id="overlay_part" class="overlay hide">
		<i class="fa fa-spinner fa-spin med-green" style="font-size:40px;"></i>
	</div>
  </div><!-- /. box -->
{!! Form::close() !!}
</div><!-- /.col -->

@push('view.scripts')
<script type="text/javascript">
$('#to_address').tokenfield({
  autocomplete: {
    source: [{!! $users_list_arr !!}],
    delay: 100
  },
  showAutocompleteOnFocus: true
})
</script>
@endpush