@extends('admin')

@section('toolbar')
    <div class="row toolbar-header">
        <section class="content-header">
            <h1>
                <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="pen"></i>Roles <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Set Page Permission for {{$role_name}}</span></small>
            </h1>
            <ol class="breadcrumb">
            <li><a href="{{ url('admin/practicerole') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
                <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
                <li><a href="#js-help-modal" data-url="{{url('help/role')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            </ol>
        </section>
    </div>
@stop

@section('practice-info')
    @include ('admin/role/tabs')
@stop

@section('practice')
    {!! Form::model($setpagepermissions, array('method' => 'PATCH','id'=>'js-bootstrap-validator','url' =>array('admin/setpagepermissions/'.$id))) !!}
    @include ('admin/setpagepermissions/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}                                                                       
@stop            