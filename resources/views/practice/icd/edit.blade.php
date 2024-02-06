@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> ICD 10 <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{url('icd/'.$icd->id)}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal"  data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
{!! Form::model($icd, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'icd/'.$icd->id,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-15"><!-- Col Starts -->
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body"><!-- Box Body Starts -->
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                <div class="text-center">
                    <div class="safari_rounded">
                    <div>{!! HTML::image('img/icd.png',null) !!}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 med-right-border form-horizontal">                       
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">ICD 10</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <span class="med-orange">{!! $icd->icd_code !!} </span>   
                        </div>
                    </div>
                </div>
                
                <div class="form-group">				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                       <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
                        <span class="med-green font600">Medium Description</span>  
                       </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            @if($icd->medium_description!='') {!! $icd->medium_description !!} @else <span class="nill">- Nil -</span> @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">                    
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                        
                    </div>
                </div>                   
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10 @if($errors->first('icd_type')) error @endif">
                        {!! Form::select('icd_type', array('' => '-- Select ICD Type --','CM' => 'CM','PCS' => 'PCS'),null,['placeholder' => 'ICD Type','class'=>'form-control input-sm select2']) !!}
                        {!! $errors->first('icd_type', '<p> :message</p>')  !!}                        
                    </div>                    
                </div>             
                <div class="form-group">
                    <div class="col-lg-11 col-md-11 col-sm-10 med-height-30 @if($errors->first('header')) error @endif">
                        @if($icd->header == "V")
                        V - Valid for Submission
                        @elseif($icd->header == "H")	
                        H -  Header, Not valid for Submission
                        @elseif($icd->header == "C")
                        C - Chapter, Not valid for Submission
                        @endif	

                        {!! $errors->first('header', '<p> :message</p>')  !!}                        
                    </div>                    
                </div>    
            </div>
        </div><!-- /.box-body ends -->
    </div><!-- Box Ends -->
</div><!-- Col Ends -->
@stop

@section('practice')
@include ('practice/icd/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop