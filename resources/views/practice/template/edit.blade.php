@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $templates->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($templates->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('templates/'.$templates->id) }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::model($templates, ['method'=>'PATCH', 'url'=>'templates/'.$templates->id,'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-15">
    <div class="box-block">
        <div class="box-body">

            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 med-right-border">              
                
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! HTML::decode(Form::label('name', 'Name <sup class="med-orange">*</sup>', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label js-letters-caps-format'])) !!} 
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 @if($errors->first('name')) error @endif">
                            {!! Form::text('name',null,['class'=>'form-control input-sm-modal-billing','maxlength'=>'100','name'=>'name','autocomplete'=>'off']) !!}
                            {!! $errors->first('name', '<p> :message</p>')  !!}
                        </div>                        
                    </div>

                    <!--div class="form-group">
                        {!! HTML::decode(Form::label('templatetypes', 'Category <sup class="med-orange">*</sup>', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label'])) !!}                                                                                             
                        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-10 select2-white-popup @if($errors->first('template_type_id')) error @endif">  
                            {!! Form::select('template_type_id', array('' => '-- Select --') + (array)$templatestype,  $template_type_id,['class'=>'form-control select2']) !!}
                            {!! $errors->first('template_type_id', '<p> :message</p>')  !!}
                        </div>                         
                    </div--> 
					<div class="js-add-new-select" id="js-insurance-type">                   
    					<div class="js-add-new-select" id="js-insurance-type">
                            <div class="form-group js_common_ins">
                                {!! Form::label('templatetypes', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!}
                                <div class="col-lg-4 col-md-6 col-sm-6 @if($errors->first('template_type_id')) error @endif"> 
                                    {!! Form::select('template_type_id', array('' => '-- Select --') + (array)$templatestype,  $template_type_id,['class'=>'form-control select2 js-add-new-select-opt','autocomplete'=>"off"]) !!}
                                    {!! $errors->first('template_type_id', '<p> :message</p>')  !!}
                                </div>                        
                            </div> 
                        </div> 
    					<div class="form-group hide" id="add_new_span">
                        {!! Form::label('templatetypes', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label star']) !!} 
                        <div class="col-lg-4 col-md-6 col-sm-6 ">
                            {!! Form::text('newadded',null,['maxlength'=>'50','id'=>'newadded','class'=>'form-control','placeholder'=>'Add new Template Type','data-table-name'=>'templatetypes','data-field-name'=>'templatetypes','data-field-id'=>@$template_type_id,'data-label-name'=>'template type']) !!}
                            <p class="js-error help-block hide"></p>
                            <p class="pull-right no-bottom">
                            <i class="fa fa-save med-green" id="add_new_save" data-placement="bottom"  data-toggle="tooltip" data-original-title="Save"></i>
                            <i class="fa fa-ban med-green margin-l-5" id="add_new_cancel" data-placement="bottom"  data-toggle="tooltip" data-original-title="Cancel"></i>                         
                            </p>
                        </div>
                    </div>
                </div>
                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 @if($errors->first('status')) error @endif">
                            
                            {!! Form::radio('status', 'Active',true,['class'=>'','id'=>'c-active']) !!} {!! Form::label('c-active', 'Active',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                            {!! Form::radio('status', 'Inactive',null,['class'=>'','id'=>'c-inactive']) !!} {!! Form::label('c-inactive', 'Inactive',['class'=>'med-darkgray font600 form-cursor']) !!} 
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>                        
                    </div>
                </div>                
            </div>                      
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal">
                <ul class="icons push no-padding">
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Created by </span> <span class="pull-right">{{ @$templates->creator->name }}</span></li> 
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Created On </span> <span class="pull-right bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->created_at,'date')}}</span></li>				   
                    <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated by </span> <span class="pull-right">@if(@$templates->modifier->name =="")<span class=" nill ">  - Nil - </span>@else {{ @$templates->modifier->name}} @endif</span></li>
					<li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated On </span> @if(@$templates->updated_at =="" || @$templates->updated_at =="0000-00-00 00:00:00" || @$templates->updated_at =="-0001-11-30 00:00:00")<span class=" nill pull-right">  - Nil - </span>@else <span class="pull-right bg-date">{{ App\Http\Helpers\Helpers::dateFormat($templates->updated_at,'date')}}</span>@endif</li>
                </ul>
            </div>            
            
        </div><!-- /.box-body -->
    </div>
</div>
@include ('practice/template/form',['submitBtn'=>'Save'])
{!! Form::close() !!}

@stop            