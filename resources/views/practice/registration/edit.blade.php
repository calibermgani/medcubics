@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.registration')}}"></i> Registration</small>
		</h1>
		<ol class="breadcrumb">
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/registration')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice')
	{!! Form::model(@$registration, ['id' => '','url' => 'registration/edit','method' => 'post']) !!}
	@include ('practice/registration/forms',['submitBtn'=>'Save']) 
@stop 