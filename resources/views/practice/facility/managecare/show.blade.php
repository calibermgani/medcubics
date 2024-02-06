@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row starts -->
    <section class="content-header">
        <h1>
		<?php 
			$facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode');
			$managedcare->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($managedcare->id,'encode'); 
		?>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Managed Care <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('facility/'.$facility->id.'/facilitymanagecare') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/facility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row ends -->
@stop

@section('practice-info')
@include ('practice/facility/tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">			
	@if($checkpermission->check_url_permission('facility/{id}/facilitymanagecare/{facilitymanagecare}/edit') == 1)
    <a href="{{ url('facility/'.$facility->id.'/facilitymanagecare/'.$managedcare->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Col Starts -->
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" tabindex="-1"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('insurance', 'Insurance', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ @$managedcare->insurance->insurance_name }}</p>
                </div>               
            </div>

            <div class="form-group">
                {!! Form::label('provider', 'Provider', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                   <p class="show-border no-bottom">{{ @$managedcare->provider->provider_name}} {{ @$managedcare->provider->degrees->degree_name}}</p>
                </div>               
            </div>

            <div class="form-group">
                {!! Form::label('credential', 'Credential', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                 @if($managedcare->enrollment == 'Par')   
					{!! Form::radio('enrollment', 'Par',true,['class'=>'flat-red']) !!} Par &emsp; {!! Form::radio('enrollment', 'Non-Par',null,['class'=>'flat-red','disabled']) !!} Non-Par
				@else	
					{!! Form::radio('enrollment', 'Par',null,['class'=>'flat-red']) !!} Par &emsp; {!! Form::radio('enrollment', 'Non-Par',true,['class'=>'flat-red']) !!} Non-Par
				@endif	
                </div>               
            </div>

            <div class="form-group">
                {!! Form::label('EntityType', 'Entity Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                 @if($managedcare->entitytype == 'Individual')   
                    {!! Form::radio('entitytype', 'Group',null,['class'=>'flat-red','disabled']) !!} Group &nbsp;&nbsp; &nbsp;{!! Form::radio('entitytype', 'Individual',true,['class'=>'flat-red']) !!} Individual 
				@else	
					{!! Form::radio('entitytype', 'Group',true,['class'=>'flat-red']) !!} Group &nbsp;&nbsp; &nbsp;{!! Form::radio('entitytype', 'Individual',null,['class'=>'flat-red','disabled']) !!} Individual
				@endif	
                </div>               
            </div>

            <div class="form-group">
                {!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                  <p class="show-border no-bottom">{{ $managedcare->provider_id }}</p>
                </div>               
            </div>

          

            <div class="form-group">
                {!! Form::label('effectivedate', 'Effective Date', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}

                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-10">
                    <p class="show-border no-bottom">@if($managedcare->effectivedate != '0000-00-00') 
										 {{ App\Http\Helpers\Helpers::dateFormat($managedcare->effectivedate,'date') }} 
									@endif </p>
                </div>               
            </div>

            <div class="form-group">
                {!! Form::label('terminationdate', 'Termination Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}

                <div class="col-lg-2 col-md-4 col-sm-5 col-xs-10">
                  <p class="show-border no-bottom">@if($managedcare->terminationdate != '0000-00-00')
										{{ App\Http\Helpers\Helpers::dateFormat($managedcare->terminationdate,'date') }}
									@endif</p>
                </div>                
            </div>

            <div class="form-group">
                {!! Form::label('feeschedule', 'Fee Schedule', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-10">
                  <p class="show-border no-bottom">{{ $managedcare->feeschedule }}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>


        </div><!-- /.box-body ends -->      

    </div><!-- /.box ends -->
</div><!-- Col Ends  -->

@stop 