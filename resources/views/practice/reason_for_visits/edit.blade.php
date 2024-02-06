@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
	<?php $reason->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($reason->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Account Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Reason For Visit <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Edit</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a  href="javascript:void(0)" data-url="{{ url('reason/'.$reason->id)}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/reason_for_visit')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('practice/apisettings/tabs')
@stop

@section('practice')
	{!! Form::model($reason, ['method'=>'PATCH','id'=>'js-bootstrap-validator','name'=>'myform','role' => 'form','action' => '', 'files' => true ,'class'=>'medcubicsform','url'=>'reason/'.$reason->id]) !!} 
		@include ('practice/reason_for_visits/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop            