@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="mail"></i> Mail <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Settings</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('profile/maillist')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="#" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::model($mail_settings_datas, ['url'=>'profile/maillist/settings/store','id'=>'js-bootstrap-validator']) !!}   

<div class="col-md-12">
    <div class="box-body-block">
        <div class="col-md-12" >
            <div class="box box-info no-shadow">
				<div class="box-block-header with-border">
					<i class="livicon" data-name="info"></i> <h3 class="box-title">General Settings</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
				</div>

				<div class="box-body  form-horizontal">
					<div class="form-group">
					   {!! Form::label('signature', 'Signature', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-3 col-md-4 col-sm-6 col-xs-10">
							{!! Form::radio('signature', 'no',true,['class'=>'flat-red']) !!} No &emsp; {!! Form::radio('signature', 'yes',null,['class'=>'flat-red']) !!} Yes          
						</div>
					</div>

					<div id="signature_content_part" @if(@$mail_settings_datas->signature=='yes') class="form-group" @else class="form-group hide" @endif>
					   {!! Form::label('signature_content', 'Signature Content', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label']) !!} 
						<div class="col-lg-9 col-md-4 col-sm-6 col-xs-10">
							<textarea name="signature_content" id="signature_content" class="form-control">
							{!! @$mail_settings_datas->signature_content !!}
							</textarea>         
						</div>
					</div>
						   
					<div class="box-footer">
						<div class="col-lg-6 col-md-8 col-sm-10 col-xs-12">
						{!! Form::submit('Submit', ['name'=>'sample','class'=>'btn btn-medcubics']) !!}
						<a href="{{ url('profile/maillist')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>	
						</div>
					</div>
				</div><!-- /.box -->
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
@stop