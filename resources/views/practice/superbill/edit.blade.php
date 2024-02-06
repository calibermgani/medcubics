@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.superbills')}} font14"></i> Superbills <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Superbill Template</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('superbills/'.$superbill_array->id) }}" class="js_next_process" ><i 	class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/superbills')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

{!! Form::model($superbill_array, ['method'=>'PATCH', 'url'=>'superbills/'.$superbill_array->id,'id'=>'js-bootstrap-validator','class'=>'js_superbill_template medcubicsform']) !!}
<?php  $header_list = implode(",",$superbill_array->header_list); ?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.superbills") }}' />

<div class="col-md-12 space-m-t-7">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12">
                <p class="push">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('template_name', 'Template Name', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label']) !!} 
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::text('template_name',null,['class'=>'form-control js-letters-caps-format','name'=>'template_name']) !!}
                            <p class="js_template_name js_error hide"></p>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('provider', 'Provider', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label']) !!}                                                                                             
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::select('provider_id', array(''=>'-- Select --')+(array)$providers,  NULL,['class'=>'form-control select2 js-sel-provider-change','id'=>'provider_id']) !!}
                            <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                            <p class="js_provider_id js_error hide"></p>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('Header', 'Header', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label']) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::select('header_list', ['office_visit'=>'Office Visit','office_procedures'=>'Office Procedures','laboratory'=>'Laboratory','well_visit'=>'Well Visit','medicare_preventive_services'=>'Medicare preventive services','skin_procedures'=>'Skin Procedures','consultation_preop_clearance'=>'Consultation/preop clearance','vaccines'=>'Vaccines','medications'=>'Medications','other_services'=>'Other Services'],null,['class'=>'form-control select2 js_choose_header','multiple'=>'multiple']) !!}
                            <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                            <p class="js_header_list js_error hide"></p>
                            <input type="hidden" id="selected_list" value="{{ $header_list }}" />
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label']) !!} 
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 @if($errors->first('status')) error @endif">
                            {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive 
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>	
                    <!--div class="form-group">
                            {!! Form::Button('Show template',['class'=>'btn btn-flat btn-medgreen']) !!} 
                    </div-->
                </div>
                </p>
            </div>
        </div><!-- /.box-body -->
    </div>
</div>
@include ('practice/superbill/edit-template')
@stop