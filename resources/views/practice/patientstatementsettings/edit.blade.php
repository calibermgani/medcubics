@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Patient Statement <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Settings</span></small>
		</h1>
		<ol class="breadcrumb">
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/patientstatement')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
	@include ('practice/patientstatementsettings/tabs')
@stop

@section('practice')
	{!! Form::model(@$psettings, ['id' => 'js-bootstrap-validator','class'=>'medcubicsform','name'=>'medcubicsform','url' => 'patientstatementsettings','method' => 'post']) !!}
	@include ('practice/patientstatementsettings/forms',['submitBtn'=>'Save']) 
@stop 