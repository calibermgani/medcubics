@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $clearing_house->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($clearing_house->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{@$heading_icon}} font14"></i> {{ $heading }} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit EDI</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('edi/'.$clearing_house->id) }}" class="js_next_process"> <i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if($checkpermission->check_url_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/edi')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice')
    {!! Form::model($clearing_house, ['method'=>'PATCH', 'url'=>'edi/'.$clearing_house->id, 'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
         @include ('practice/clearinghouse/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
<!--End-->
@stop    