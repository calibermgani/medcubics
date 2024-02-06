@extends('admin')
@section('toolbar')
	<div class="row toolbar-header">
	<?php $code->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($code->id,'encode'); ?>
		<section class="content-header">
	        <h1>
	            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Remittance Codes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span> </small>
	        </h1>
                    
			<ol class="breadcrumb">
				<li><a href="javascript:void(0)" data-url="{{url('code/'.$code->id)}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
				<li><a href="javascript:void(0);" data-url="{{url('help/codes')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div>
@stop

@section('practice')
    {!! Form::model($code, ['method'=>'PATCH', 'url'=>'code/'.$code->id, 'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
        @include ('practice/code/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
	<!--End-->
@stop            