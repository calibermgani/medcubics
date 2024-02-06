@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-hospital-o font14"></i> API <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit API</span></small>
        </h1>
		<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apiconfig->id,'encode');  ?>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/apiconfig/'.$apiid.'/show')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/apiconfig')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
	{!! Form::model($apiconfig, ['method'=>'post','id'=>'js-bootstrap-validator','name'=>'myform','role' => 'form','action' => '', 'files' => true ,'url'=>'admin/apiconfig/'.$apiid.'/update']) !!} 
		@include ('admin/apiconfig/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop