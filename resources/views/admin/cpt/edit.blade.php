@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font14"></i> CPT <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit CPT</span></small>
			</h1>
			<?php $cpt->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($cpt->id,'encode'); ?>	
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{url('admin/cpt/'.$cpt->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	{!! Form::model($cpt, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'admin/cpt/'.$cpt->id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	<div class="col-md-12 margin-t-m-15">
		<div class="box-block box-info">
			<div class="box-body">
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
					<div class="text-center">
						<div class="safari_rounded">
					{{-- {{$cpt->cpt_hcpcs}} --}}
					{!! HTML::image('img/cpt.png',null) !!} 
						</div>
					</div>
				</div>

				<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
					<div class="form-group">
						<div class="col-md-12 @if($errors->first('cpt_hcpcs')) error @endif">
							{!! Form::text('cpt_hcpcs', null,['placeholder' => 'CPT / HCPCS','class'=>'form-control','style'=>'margin-bottom:10px;',
							 'maxlength'=>5]) !!}
							{!! $errors->first('cpt_hcpcs', '<p> :message</p>')  !!}
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 @if($errors->first('medium_description')) error @endif">
							{!! Form::textarea('medium_description', null,['placeholder' => 'Medium Description','class'=>'form-control','maxlength'=>100]) !!}
						   {!! $errors->first('medium_description', '<p> :message</p>')  !!}
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
					<div class="form-group">
						{!! Form::label('Code Type', 'Code Type',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('code_type')) error @endif">
						   {!! Form::text('code_type', null,['class'=>'form-control input-sm-header-billing dm-address']) !!}
						   {!! $errors->first('code_type', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('Medicare', 'Global Period',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('medicare_global_period')) error @endif">
						   {!! Form::text('medicare_global_period', null,['placeholder' => 'Medicare Global Period','class'=>'form-control input-sm-header-billing','maxlength'=> 3]) !!}
						   {!! $errors->first('medicare_global_period', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('icd', 'ICD',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('icd')) error @endif">
						   {!! Form::text('icd', null,['class'=>'form-control input-sm-header-billing','maxlength'=>8]) !!}
						   {!! $errors->first('icd', '<p> :message</p>')  !!}
						</div>
					</div>
					@if($cpt->effectivedate!='0000-00-00')
					 <?php $cpt->effectivedate=date("m/d/Y",strtotime($cpt->effectivedate)) ?>
									@else
									 <?php $cpt->effectivedate='' ?>
					@endif
					@if($cpt->terminationdate!='0000-00-00')
					<?php $cpt->terminationdate=date("m/d/Y",strtotime($cpt->terminationdate)) ?>
									 @else
									 <?php $cpt->terminationdate='' ?>
					@endif
					<div class="form-group">
						{!! Form::label('effective date', 'Effective Date',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('effectivedate')) error @endif">
							<i class="fa fa-calendar-o form-icon-billing"></i>
							{!! Form::text('effectivedate', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'dm-date form-control input-sm-header-billing','id'=>'effectivedate']) !!}
						   {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('termination date', 'Inactive Date',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('terminationdate')) error @endif">
							<i class="fa fa-calendar-o form-icon-billing"></i>
							{!! Form::text('terminationdate', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'terminationdate','class'=>'dm-date form-control input-sm-header-billing']) !!}
						   {!! $errors->first('terminationdate', '<p> :message</p>')  !!}
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
		</div>
	</div>
@stop

@section('practice')
		@include ('admin/cpt/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop
