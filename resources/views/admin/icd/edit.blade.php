@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.icd')}} font14"></i> ICD-10 <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit ICD-10</span></small>
			</h1>
			<?php $icd->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($icd->id,'encode'); ?>	
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{url('admin/icd/'.$icd->id)}}" class="js_next_process"> <i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="" data-target="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	{!! Form::model($icd, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'admin/icd/'.$icd->id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	<div class="col-md-12 margin-t-m-18">
		<div class="box-block box-info">
			<div class="box-body">
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
					<div class="text-center">
						<div class="safari_rounded">
						<div>{!! HTML::image('img/icd.png',null) !!}</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal">
					<div class="form-group">
						<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('icd_code')) error @endif">
							{!! Form::text('icd_code', null,['maxlength'=>'8','placeholder' => 'Code','class'=>'form-control js_need_regex']) !!}
							{!! $errors->first('icd_code', '<p> :message</p>')  !!}
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('medium_description')) error @endif">
							{!! Form::textarea('medium_description', null,['placeholder' => 'Medium Description','class'=>'form-control']) !!}
							{!! $errors->first('medium_description', '<p> :message</p>')  !!}
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
					<div class="form-group">
						{!! Form::label('Code Type', 'Code Type',  ['class'=>'col-lg-4 col-md-5 col-sm-3 col-xs-4 control-label']) !!}
						<div class="col-lg-6 col-md-6 col-sm-5 col-xs-7 @if($errors->first('icd_type')) error @endif">
							 {!! Form::select('icd_type', array('' => '-- Select ICD Type --','CM' => 'CM','PCS' => 'PCS'),null,['placeholder' => 'ICD Type','class'=>'form-control input-sm select2']) !!}						 
							{!! $errors->first('icd_type', '<p> :message</p>')  !!}                        
						</div>                    
					</div> 
                                    <div class="form-group">
                                        <div class="col-lg-11 col-md-11 col-sm-10 @if($errors->first('header')) error @endif">
                                            {!! Form::radio('header', 'V', True,['class'=>'','id'=>'e-v']) !!}<label for="e-v"> V - Valid for Submission</label><br>
                                            {!! Form::radio('header', 'H',null,['class'=>'','id'=>'e-h']) !!}<label for="e-h"> H -  Header, Not valid for Submission</label><br>
                                            {!! Form::radio('header', 'C',null,['class'=>'','id'=>'e-c']) !!}<label for="e-c"> C - Chapter, Not valid for Submission</label>
                                            {!! $errors->first('header', '<p> :message</p>')  !!}
                                        </div>
                                    </div>
				</div>
			</div><!-- /.box-body -->
		</div>
	</div>
@stop

@section('practice')
	@include ('admin/icd/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop