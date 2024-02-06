@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			@php $modifiers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifiers->id,'encode'); @endphp
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.modifiers')}}  font14"></i> Modifiers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Modifier</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{url('admin/modifierlevel2/'.$modifiers->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<li><a href ="" data-target="#js-help-modal" data-url="{{url('help/modifiers')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/modifier/tabs')
@stop

@section('practice')
	{!! Form::model($modifiers, ['method'=>'PATCH', 'url'=>'admin/modifierlevel2/'.$modifiers->id,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		@include ('admin/modifier/modifierlevel2/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop            