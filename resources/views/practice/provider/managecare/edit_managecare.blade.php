@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
		<?php $managecare->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($managecare->id,'encode'); ?>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Managed Care <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('provider/'.$provider->id.'/providermanagecare/'.$managecare->id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
@include ('practice/provider/tabs')  
@stop
@section('practice')
{!! Form::model($managecare, array('method' => 'PATCH','url' =>'provider/'.$provider->id.'/providermanagecare/'.$managecare->id,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform')) !!}
@include ('practice/provider/managecare/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop 