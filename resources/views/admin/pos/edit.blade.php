@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $pos->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($pos->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.pos')}}" data-name="location"></i> POS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit POS</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="javascript:void(0)" data-url="{{ url('admin/placeofservice/'.$pos->id)}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/pos')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice')
    {!! Form::model($pos, ['method'=>'PATCH', 'url'=>'admin/placeofservice/'.$pos->id, 'id'=>'js-bootstrap-validator', 'name'=>'medcubicsform', 'class'=>'medcubicsform']) !!}
        @include ('admin/pos/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
<!--End-->
@stop            