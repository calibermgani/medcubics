@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Managed Care <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('managecare')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('practice/practice/practice-tabs')
@stop

@section('practice')
	{!! Form::model($managecare, array('method' => 'PATCH','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform','route' =>array('managecare.update', $managecare->id))) !!}
		@include ('practice/practice/managecare/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop