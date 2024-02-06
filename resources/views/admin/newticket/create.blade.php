@extends('admin')
@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}} font14"></i> Manage Ticket <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> All Ticket <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Create New Ticket</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{url('admin/managemyticket')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<li><a href="#js-help-modal" data-url="{{url('help/manageticket')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
@include ('admin/manageticket/tabs')
@stop 

@section('practice')
	<!--1st Data-->
	{!! Form::open(['url'=>['admin/createnewticket'],'files' => true,'id'=>'js-newticket-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}   
		@include ('admin/newticket/form',['submitBtn'=>'Submit'])</center>
	{!! Form::close() !!}
@stop            