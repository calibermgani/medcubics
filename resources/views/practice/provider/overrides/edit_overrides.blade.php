@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} font14"></i> Providers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('provider/'.$provider->id.'/provideroverrides/'.$overrides->id) }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('practice/provider/tabs')
@stop

@section('practice')
	{!! Form::model($overrides, array('method' => 'PATCH','name'=>'myForm','url' =>'provider/'.$provider->id.'/provideroverrides/'.$overrides->id, 'id' => 'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform')) !!}
		@include ('practice/provider/overrides/form',['submitBtn'=>'Save']) 
	{!! Form::close() !!}
@stop 