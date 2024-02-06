@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Users <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit User</span></small>
			</h1>
			<?php 
				$customer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
				$customerusers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customerusers->id,'encode'); 
			?> 
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customerusers/'. $customerusers->id) }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
	@include ('admin/customer/tabs')
	{!! Form::model($customerusers, array('method' => 'PATCH', 'files' => true,'id'=>'js-bootstrap-validator1','url' =>'admin/customer/'.$customer->id.'/customerusers/'. $customerusers->id,'name'=>'medcubicsform','class'=>'medcubicsform')) !!}
    		@include ('admin/customer/customerusers/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
@stop