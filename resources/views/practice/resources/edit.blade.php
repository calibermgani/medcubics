@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} font14"></i> Resources <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Resource</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('resources/'.$resources->id)}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/resources')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::model($resources, ['method'=>'PATCH', 'url'=>'resources/'.$resources->id,'id'=>'js-bootstrap-validator']) !!}
    @include ('practice/resources/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            