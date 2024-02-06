@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<?php $code->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($code->id,'encode'); ?>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.codes')}} font14"></i><a href ="../../../admin/code" > Codes </a><i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Code</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{ url('admin/code/'.$code->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="javascript:void(0);" data-url="{{url('help/code')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
    {!! Form::model($code, ['method'=>'PATCH', 'url'=>'admin/code/'.$code->id, 'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		@include ('admin/code/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
@stop            