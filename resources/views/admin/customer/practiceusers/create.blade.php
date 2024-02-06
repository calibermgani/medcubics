@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right" ></i>Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Users</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'admin/practiceuserreports/customer/'.$customer_id.'/'.$practice->id.'/'])
				</li>				
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
	@include ('admin/customer/customerpractices/tabs')
    {!! Form::open(['url'=>'admin/customer/'.$cust_id.'/practice/'.$prac_id.'/practiceusers', 'files' => true,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}   
		@include ('admin/customer/practiceusers/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop   