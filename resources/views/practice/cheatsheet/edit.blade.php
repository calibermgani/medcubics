@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading">Cheatsheet - {{ $cheatsheet->id }} : <span>Edit Cheatsheet</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/cheatsheet')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::model($cheatsheet, ['method'=>'PATCH', 'url'=>'cheatsheet/'.$cheatsheet->id,'id'=>'js-bootstrap-validator']) !!}
    @include ('practice/cheatsheet/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            