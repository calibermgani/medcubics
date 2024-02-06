@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New CPT / HCPCS</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('listfavourites') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop
<?php 
    if(!isset($get_default_timezone)){
       $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
    }      
?>
@section('practice-info')
	{!! Form::open(['url'=>['cpt'],'id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
	<div class="col-md-12 margin-t-m-15">
		<div class="box-block box-info">
			<div class="box-body">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					<div class="text-center">
						<div class="safari_rounded">
					  {!! HTML::image('img/cpt.png',null) !!} 
						</div>
					</div>
				</div>

				<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 form-horizontal">
					<div class="form-group">
						<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('cpt_hcpcs')) error @endif">
							{!! Form::text('cpt_hcpcs', null,['placeholder' => 'CPT / HCPCS','class'=>'form-control','maxlength'=>6,'autocomplete'=>'off']) !!}
							{!! $errors->first('cpt_hcpcs', '<p> :message</p>')  !!}
						</div>
					</div>

					<div class="form-group">
						<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 @if($errors->first('medium_description')) error @endif">
							{!! Form::textarea('medium_description', null,['placeholder' => 'Medium Description','class'=>'form-control','maxlength'=>100]) !!}
							{!! $errors->first('medium_description', '<p> :message</p>')  !!}
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
					<div class="form-group">
						{!! Form::label('Code Type', 'Code Type',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('code_type')) error @endif">
						   {!! Form::text('code_type', null,['class'=>'form-control input-sm-header-billing dm-address','autocomplete'=>'off']) !!}
						   {!! $errors->first('code_type', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('Medicare', 'Global Period',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('medicare_global_period')) error @endif">
						   {!! Form::text('medicare_global_period', null,['placeholder' => 'Medicare Global Period','class'=>'form-control input-sm-header-billing','maxlength'=> 3,'autocomplete'=>'off']) !!}
						   {!! $errors->first('medicare_global_period', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('icd', 'ICD',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('icd')) error @endif">
						   {!! Form::text('icd', null,['class'=>'form-control input-sm-header-billing','maxlength'=>8,'autocomplete'=>'off']) !!}
						   {!! $errors->first('icd', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('effective date', 'Effective Date',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('effectivedate')) error @endif">
							<i class="fa fa-calendar-o form-icon-billing"></i>
							{!! Form::text('effectivedate', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'effectivedate','class'=>'dm-date form-control input-sm-header-billing','autocomplete'=>'off']) !!}
						   {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('termination date', 'Inactive Date',  ['class'=>'col-lg-5 col-md-5 col-sm-3 col-xs-12 control-label']) !!}
						<div class="col-lg-6 col-md-7 col-sm-8 col-xs-10 @if($errors->first('terminationdate')) error @endif">
							<i class="fa fa-calendar-o form-icon-billing"></i>
							{!! Form::text('terminationdate', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'terminationdate','class'=>'dm-date form-control input-sm-header-billing','autocomplete'=>'off']) !!}
						   {!! $errors->first('terminationdate', '<p> :message</p>')  !!}
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
		</div>
	</div>
@stop

@section('practice')
	@include ('practice/cpt/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop
@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function () {
		getmodifierandcpt();
	});
</script>	
@endpush