@extends('admin')

@section('toolbar')
<?php $uniquepatientid = $patient_id;  ?>
<div class="row toolbar-header">
    <section class="content-header">

        <h1>
            <small class="toolbar-heading"><i class="fa fa-money"></i> Wallet History <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Return Check <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>

        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('patients/'.$patient_id.'/returncheck')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            @include ('patients/layouts/swith_patien_icon')	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/return_check')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
<?php  $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); ?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'no'])
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <?php 
	$activetab = 'return check';
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'encode'); 
	$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$returncheck->id,'encode'); 
	 ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        @include ('patients/checkreturn/tab')
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10"><!--  Left side Content Starts -->
    @if($checkpermission->check_url_permission('returncheck/{returncheck}/edit') == 1)
    <a href="{{ url('patients/'.$patient_id.'/returncheck/'.$id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>	


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->   
    <div class="box box-info no-shadow"><!-- General Info Box Starts -->
        <div class="box-block-header with-border">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <?php
        $check_date = date('m/d/y', strtotime($returncheck->check_date))
        ?>
        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('Check No', 'Check No', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}   						
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ $returncheck->check_no }}</p>					
                </div>                
            </div>   
            <div class="form-group">
                {!! Form::label('check date', 'Check Date', ['class'=>'ccol-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12 ">                   
                    <p class="show-border no-bottom">{{ @$check_date}}</p>                   
                </div>                
            </div> 
            <div class="form-group">
                {!! Form::label('Financial Charge', 'Financial Charge', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
                    <p class="show-border no-bottom">{{ @$returncheck->financial_charges}}</p>
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
                    <p class="show-border no-bottom">@if($returncheck->created_by != ''){{ App\Http\Helpers\Helpers::shortname($returncheck->created_by) }} @endif</p>
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('Created On', 'Created On', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
                    <p class="show-border no-bottom">
                        @if($returncheck->created_at !='' && $returncheck->created_at !='-0001-11-30 00:00:00' && $returncheck->created_at !='0000-00-00 00:00:00')
                        {{ App\Http\Helpers\Helpers::dateFormat($returncheck->created_at,'date')}}
                        @endif
                    </p>
                </div>                
            </div>
            @if(@$returncheck->updated_by != '')
            <div class="form-group">
                {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($returncheck->updated_by) }}</p>
                </div>                
            </div>
            @endif
            
            @if($returncheck->updated_by != '')
            <div class="form-group">
                {!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">  
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::dateFormat($returncheck->updated_at,'date')}}</p>
                </div>                
            </div>
            @endif
        </div><!-- /.box-body -->

    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   
@stop      