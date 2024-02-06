@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $adjustmentreason->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($adjustmentreason->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> User Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Adjustment Reason <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('adjustmentreason')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/adjustmentreason')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/apisettings/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">                         
    @if($checkpermission->check_url_permission('adjustmentreason/{adjustmentreason}/edit') == 1)
    <a href="{{ url('adjustmentreason/'.$adjustmentreason->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
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

        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}   						
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12">
                 @if($adjustmentreason->adjustment_type =='Insurance')   
					{!! Form::radio('adjustment_type', 'Insurance','true',['class'=>'flat-red']) !!} Insurance &emsp; {!! Form::radio('adjustment_type', 'Patient',null,['class'=>'flat-red','disabled']) !!} Patient				
                 @else   
					{!! Form::radio('adjustment_type', 'Insurance','null',['class'=>'flat-red','disabled']) !!} Insurance &emsp; {!! Form::radio('adjustment_type', 'Patient',true,['class'=>'flat-red']) !!} Patient													
				@endif	
                </div>                
            </div>   
            <div class="form-group">
                {!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ @$adjustmentreason->adjustment_reason}}</p>         
                </div>                
            </div> 
			<div class="form-group">
                {!! Form::label('Adjustment ShortName', 'Adjustment ShortName', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ @$adjustmentreason->adjustment_shortname}}</p>         
                </div>                
            </div> 
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">  
				@if($adjustmentreason->status == 'Active')
					 {!! Form::radio('status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive                   
				@else
					 {!! Form::radio('status', 'Active','null',['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive                 
				@endif	
                                                         
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">@if($adjustmentreason->created_by != ''){{ App\Http\Helpers\Helpers::shortname($adjustmentreason->created_by) }} @endif</p>         
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('Created On', 'Created On', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">
                        @if($adjustmentreason->created_at !='' && $adjustmentreason->created_at !='-0001-11-30 00:00:00' && $adjustmentreason->created_at !='0000-00-00 00:00:00')
                        {{ App\Http\Helpers\Helpers::dateFormat($adjustmentreason->created_at, 'date') }}
                        @endif
                    </p>         
                </div>                
            </div>
            @if(@$adjustmentreason->updated_by != '' && $adjustmentreason->updated_at !='-0001-11-30 00:00:00' && $adjustmentreason->updated_at !='0000-00-00 00:00:00')
            <div class="form-group">
                {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($adjustmentreason->updated_by) }}</p>
                </div>                
            </div>
            @endif
            
            @if($adjustmentreason->updated_at !='' && $adjustmentreason->updated_at !='-0001-11-30 00:00:00' && $adjustmentreason->updated_at !='0000-00-00 00:00:00')
            <div class="form-group">
                {!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-2 col-md-2 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">
                        {{ App\Http\Helpers\Helpers::dateFormat($adjustmentreason->updated_at, 'date') }}
                    </p>         
                </div>                
            </div>
            @endif
        </div><!-- /.box-body -->

    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   

@stop            