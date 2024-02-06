@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-hospital-o font14"></i> API <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Config</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/apiconfig') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/apiconfig')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')
{!! Form::open(['url'=>'admin/apiconfig/create','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}   
@include ('admin/apiconfig/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop

