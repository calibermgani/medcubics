@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="archive-extract"></i>ICD-9 <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit ICD-9</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{url('admin/icd/'.$icd->id)}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::model($icd, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'admin/icd09/'.$icd->id]) !!}
<div class="col-md-12">
    <div class="box-block box-info">
        <div class="box-body">
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div>{!! HTML::image('img/icd.png',null,['class'=>'img-border']) !!}</div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 med-right-border">
                <div class="form-group">
                    <div class="col-md-12 @if($errors->first('code')) error @endif">
                        {!! Form::text('code', null,['maxlength'=>'7','placeholder' => 'Code','class'=>'form-control','style'=>'margin-bottom:10px;']) !!}
                        {!! $errors->first('code', '<p> :message</p>')  !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 @if($errors->first('medium_desc')) error @endif">
                        {!! Form::textarea('medium_desc', null,['placeholder' => 'Medium Description','class'=>'form-control']) !!}
                        {!! $errors->first('medium_desc', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10">
                        {!! Form::text('category', null,['placeholder' => 'Code Category','class'=>'form-control input-sm','readonly']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10 @if($errors->first('code_status')) error @endif">
                        {!! Form::text('code_status', null,['placeholder' => 'Code Status','class'=>'form-control input-sm']) !!}
                        {!! $errors->first('code_status', '<p> :message</p>')  !!}
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div>
</div>
@stop

@section('practice')
@include ('admin/icd/formicd09',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop