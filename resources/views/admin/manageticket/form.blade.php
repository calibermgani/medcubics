{!! Form::model(@$ticket, ['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-validator','name'=>'myform','data-id'=>$ticket_id]) !!} 
<div class="col-md-12 col-md-12 col-sm-12 col-xs-12" >
	<div class="box box-info no-shadow">
		<div class="box-block-header margin-b-10">
			<h3 class="box-title"><i class="fa fa-reply  font14"></i> Reply Conversation</h3>			
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10">
			<div class="form-group">
				{!! Form::label('Description', 'Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10 @if($errors->first('description')) error @endif">
					{!! Form::textarea('description', null,['placeholder' => 'Description','class'=>'form-control']) !!}
					{!! $errors->first('description', '<p> :message</p>') !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!}
				<div class="col-lg-8 col-md-7 col-sm-8 col-xs-9">
                                    {!! Form::radio('status', 'Open', true,['class'=>'','id'=>'s-open']) !!} {!! Form::label('s-open', 'Open',['class'=>'']) !!} &emsp;
                                    {!! Form::radio('status', 'Closed', false,['class'=>'','id'=>'s-closed']) !!} {!! Form::label('s-closed', 'Closed',['class'=>'']) !!}
				</div>                
			</div>
			 <div class="form-group js-upload">
				{!! Form::label('', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
				<div class="col-lg-8 col-md-7 col-sm-8 col-xs-9 @if($errors->first('filefield')) error @endif">
				   <span class="fileContainer col-lg-3" style="padding:1px 20px;margin:0px;">
					<input class="col-lg-2 col-md-2 col-sm-3 form-control" name="filefield1" type="file">Upload </span>
					{!! $errors->first('filefield', '<p> :message</p>')  !!} 
					&emsp;<div class="js-display-error col-lg-3"></div>
				 </div>
				<div class="col-sm-1"></div>
			</div>  
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 text-center">
				{!! Form::submit("Send", ['class'=>'btn btn-medcubics js_submit']) !!}&nbsp;
				{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel']) !!}
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div><!--/.col (left) -->
{!! Form::close() !!}