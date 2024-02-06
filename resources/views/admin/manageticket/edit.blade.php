@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.ticket')}}  font14"></i> Ticket <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Reply Ticket</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/manageticket/'.$ticket->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/manageticket')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
	{!! Form::model($ticket, ['method'=>'PATCH','id'=>'js-bootstrap-validator','name'=>'myform','role' => 'form','url'=>'admin/manageticket/'.$ticket->id,'class'=>'medcubicsform']) !!} 
		@include ('admin/manageticket/form',['submitBtn'=>'Send'])
	{!! Form::close() !!}
@stop      