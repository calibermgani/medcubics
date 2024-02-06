@extends('admin')



@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.help')}} font14"></i> Help <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Help</span></small>
        </h1>
        <ol class="breadcrumb">
        <li> <a href="javascript:void(0)" data-url="{{ url('admin/staticpage/'.$staticpages->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
        </ol>
    </section>

</div>
@stop
@section('practice')

{!! Form::model($staticpages, ['method'=>'PATCH', 'url'=>'admin/staticpage/'.$staticpages->id,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
    @include ('admin/staticpage/form',['submitBtn'=>'Save'])
{!! Form::close() !!}

@stop            