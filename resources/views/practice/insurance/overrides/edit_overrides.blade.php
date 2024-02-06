@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}} font14"></i> Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/'.$overrides->id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
<div class="row-fluid">

    @include ('practice/insurance/insurance_tabs')  
@stop

@section('practice')

	{!! Form::model($overrides, array('method' => 'PATCH','id'=>'js-bootstrap-validator','url' =>'insurance/'.$insurance->id.'/insuranceoverrides/'.$overrides->id,'name'=>'medcubicsform','class'=>'medcubicsform')) !!}
	@include ('practice/insurance/overrides/form',['submitBtn'=>'Save'])  
	{!! Form::close() !!}

@stop 