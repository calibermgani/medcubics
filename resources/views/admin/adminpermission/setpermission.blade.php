@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="users"></i>Admin Permission</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('admin/adminpermission')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/adminpermission')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
               {!! Form::open(['url'=>'admin/adminpermission','name'=>'myform','role' => 'form','action' => '','class' => 'medcubicsform', 'files' => true,'id'=>'js-bootstrap-validator']) !!} 

              
<!--End Sub Menu-->
@stop

@section('practice')
    @include ('admin/adminpermission/form',['submitBtn'=>'Save'])
{!! Form::close() !!}

@stop  