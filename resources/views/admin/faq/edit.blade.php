@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $faq->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($faq->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.faq')}}  font14"></i> FAQ <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit FAQ</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/faq/'.$faq->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/hold_option')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice')
	{!! Form::model($faq, ['method'=>'PATCH','id'=>'js-bootstrap-validator','name'=>'myform','role' => 'form','action' => '', 'files' => true ,'url'=>'admin/faq/'.$faq->id,'class'=>'medcubicsform']) !!} 
		@include ('admin/faq/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}

@stop      