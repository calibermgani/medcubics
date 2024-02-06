@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{url('cpt/'.@$id)}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')
{!! Form::model($cpt, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'cpt/'.@$id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
<div class="col-md-12 margin-t-m-18"><!-- Col-12 Starts -->
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <?php 
				$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($cpt->id,'decode'); 
				$text = ($cpt->favourite == null) ? "Add to favorite":"Remove from favorite"; 
			?>
            <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12 margin-t-8">
                <div class="text-center med-circle">
                    {{$cpt->cpt_hcpcs}}
                </div>
            </div>

            <div class="col-lg-7 col-md-7 col-sm-9 col-xs-12">                               
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">CPT / HCPCS</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <?/*
                                * Add or Remove favorite select option given here
                            */?>
                            <span class="med-orange">{!! $cpt->cpt_hcpcs !!}<a href="javascript:void(0);" class="js-favourite-record" data-id="{{$cpt->id}}" data-url='{{url("togglecptfavourites/".$cpt->id)}}'> <i class="fav_button fa @if($cpt->favourite) fa-star @else  fa-star-o  @endif font16" data-placement="bottom" data-toggle="tooltip" data-original-title="{{$text}}"></i></a></span>   
                        </div>
                    </div>
                </div>
                
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-8">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">Medium Description</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            {!! $cpt->medium_description !!}
                        </div>
                    </div>
                </div>
               
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-8">
                        <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                            <span class="med-green font600">Medicare Global Period</span>  
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            {!! $cpt->medicare_global_period !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 med-left-border form-horizontal">
                <div class="form-group">    
                    {!! Form::label('code_type', 'Code Type', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label med-green']) !!}
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-12 @if($errors->first('code_type')) error @endif">
                        {!! Form::text('code_type', null,['class'=>'form-control input-sm-modal-billing','title'=>'Code Type','maxlength'=>'50']) !!}
                        {!! $errors->first('code_type', '<p> :message</p>')  !!}
                    </div>
                </div>                
                <div class="form-group">
                    {!! Form::label('icd', 'ICD', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label med-green']) !!}
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-12 @if($errors->first('icd')) error @endif">
                        {!! Form::text('icd', null,['maxlength'=>'8','class'=>'form-control input-sm-modal-billing','title'=>'ICD']) !!}
                        {!! $errors->first('icd', '<p> :message</p>')  !!}
                    </div>
                </div>
				<?php
					if($cpt->effectivedate!='0000-00-00' && $cpt->effectivedate != '') {
						$cpt->effectivedate=date("m/d/Y",strtotime($cpt->effectivedate));
					} else {
						$cpt->effectivedate='';
					}
					if($cpt->terminationdate!='0000-00-00'&& $cpt->terminationdate != '') {
						$cpt->terminationdate=date("m/d/Y",strtotime($cpt->terminationdate));
					} else {
						$cpt->terminationdate='';
					}	                
				?>
                <div class="form-group">
                    {!! Form::label('effective date', 'Effective Date', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label med-green']) !!}
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-12 @if($errors->first('effectivedate')) error @endif">
                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> {!! Form::text('effectivedate',null,['class'=>'form-control dm-date input-sm-modal-billing form-cursor','id'=>'effectivedate','title'=>'Effective Date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                        {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
                    </div>					
                </div>

                <div class="form-group">
                    {!! Form::label('termination date', 'Inactive Date',  ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-12  @if($errors->first('terminationdate')) error @endif">
                        <i class="fa fa-calendar-o form-icon-billing"></i>
                        {!! Form::text('terminationdate', null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'terminationdate','class'=>'dm-date form-control input-sm-header-billing']) !!}
						{!! $errors->first('terminationdate', '<p> :message</p>')  !!}
                    </div>
                </div>
                <!--div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10 @if($errors->first('effectivedate')) error @endif">
                        {!! Form::text('effectivedate', null,['placeholder' => 'Effective Date','class'=>'form-control input-sm datepicker','readonly']) !!}
                        {!! $errors->first('effectivedate', '<p> :message</p>')  !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10 @if($errors->first('terminationdate')) error @endif">
                        {!! Form::text('terminationdate', null,['placeholder' => 'Termination Date','class'=>'form-control input-sm datepicker','readonly']) !!}
                        {!! $errors->first('terminationdate', '<p> :message</p>')  !!}
                    </div>
                </div-->
            </div>
        </div><!-- /.box-body Ends-->
    </div><!-- Box Ends -->
</div><!-- Col-12 Ends -->
@include('practice/layouts/favourite_modal') 
@stop
@section('practice')
@include ('practice/cpt/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop