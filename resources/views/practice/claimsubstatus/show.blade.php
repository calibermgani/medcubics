@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $holdoptionclaimsubstatus_encid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claimsubstatus->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Account Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Claim Sub-Status <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('claimsubstatus')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/hold_option')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/apisettings/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('claimsubstatus/{claimsubstatus}/edit') == 1)
    <a href="{{ url('claimsubstatus/'.$holdoptionclaimsubstatus_encid.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->   
    <div class="box box-info no-shadow"><!-- General Info Box Starts -->
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('substatus', 'Claim Sub Status', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}  
                <div class="col-lg-4 col-md-5 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ $claimsubstatus->sub_status_desc }}</p>								
                </div>                
            </div>  


            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-3 control-label']) !!}
                <div class="col-lg-8 col-md-7 col-sm-8 col-xs-9">  
					@if($claimsubstatus->status == 'Active')
						{!! Form::radio('status', 'Active','true',['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive                  
					@else
						{!! Form::radio('status', 'Active','null',['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive                
					@endif	
                </div>                
            </div>

            <div class="form-group">
                {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">@if($claimsubstatus->created_by != ''){{ App\Http\Helpers\Helpers::shortname($claimsubstatus->created_by) }} @endif</p>						
                </div>                
            </div> 

            <div class="form-group">
                {!! Form::label('Created On', 'Created On', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">@if($claimsubstatus->created_at !='' && $claimsubstatus->created_at !='-0001-11-30 00:00:00' && $claimsubstatus->created_at !='0000-00-00 00:00:00')
                        {{ App\Http\Helpers\Helpers::dateFormat($claimsubstatus->created_at, 'date') }}
                        @endif</p>						
                </div>                
            </div> 
            
            @if(@$claimsubstatus->updated_by != '')
            <div class="form-group">
                {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($claimsubstatus->updated_by) }}</p>
                </div>                
            </div>
            @endif
            
            @if($claimsubstatus->updated_at !='' && $claimsubstatus->updated_at !='-0001-11-30 00:00:00' && $claimsubstatus->updated_at !='0000-00-00 00:00:00')
            <div class="form-group">
                {!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-2 col-md-3 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">
                        {{ App\Http\Helpers\Helpers::dateFormat($claimsubstatus->updated_at, 'date') }}
                    </p>
                </div>                
            </div>
            @endif

        </div><!-- /.box-body -->

    </div><!-- General info box Ends-->
</div><!--  Left side Content Ends -->   
@stop            