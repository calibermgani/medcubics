@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.calendar')}} font14"></i> Followup Template <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Claim Status <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('followup/category') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->            
        </ol>
    </section>
</div><!-- Toolbar row ends -->
@stop
@section('practice')
	{!! Form::open(['url'=>'followup/store/category','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		@include ('practice/followup/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop