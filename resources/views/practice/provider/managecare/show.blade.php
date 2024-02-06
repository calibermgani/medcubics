@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Managed Care <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('provider/'.$provider->id.'/providermanagecare') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>           
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
@include ('practice/provider/tabs')
@stop
@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('provider/{provider_id}/providermanagecare/{providermanagecare}/edit') == 1)
    <a href="{{ url('provider/'.$provider->id.'/providermanagecare/'.$managedcare->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body  form-horizontal margin-l-10 p-b-20"><!-- Box Body Starts -->			
            <div class="form-group">
                {!! Form::label('insurance', 'Insurance', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label star']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                    <p class="show-border no-bottom">{{ @$managedcare->insurance->insurance_name }}</p>
                </div>                        
            </div>

            <div class="form-group bottom-space-10">
                {!! Form::label('credential', 'Credential', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
				@if($managedcare->enrollment == 'Par') 
                    {!! Form::radio('enrollment', 'Par',true,['class'=>'flat-red']) !!} Par &emsp; {!! Form::radio('enrollment', 'Non-Par',null,['class'=>'flat-red']) !!} Non-Par
                 @else    
                    {!! Form::radio('enrollment', 'Par',null,['class'=>'flat-red','disabled']) !!} Par &emsp; {!! Form::radio('enrollment', 'Non-Par',true,['class'=>'flat-red']) !!} Non-Par
                    {!! $errors->first('enrollment', '<p> :message</p>')  !!}
				@endif	
                </div>                        
            </div>

            <div class="form-group bottom-space-10">
                {!! Form::label('EntityType', 'Entity Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                @if($managedcare->entitytype == 'Individual')   
					{!! Form::radio('entitytype', 'Group',null,['class'=>'flat-red','disabled']) !!} Group &nbsp;&nbsp; &nbsp;{!! Form::radio('entitytype', 'Individual',true,['class'=>'flat-red']) !!} Individual
                  @else      
					{!! Form::radio('entitytype', 'Group',true,['class'=>'flat-red']) !!} Group &nbsp;&nbsp; &nbsp;{!! Form::radio('entitytype', 'Individual',null,['class'=>'flat-red','disabled']) !!} Individual
                    {!! $errors->first('entitytype', '<p> :message</p>')  !!}
                    @endif  
                </div>                       
            </div>

            <div class="form-group">
                {!! Form::label('provider_id', 'Provider ID', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                    <p class="show-border no-bottom">{{ $managedcare->provider_id }}</p>
                </div>                        
            </div>


            <div class="form-group">
                {!! Form::label('effectivedate', 'Effective Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}

                <div class="col-lg-2 col-md-3 col-sm-5 col-xs-9">
                    <p class="show-border no-bottom">{{ ($managedcare->effectivedate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managedcare->effectivedate,'date') : '' }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('terminationdate', 'Termination Date', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}

                <div class="col-lg-2 col-md-3 col-sm-5 col-xs-9">
                    <p class="show-border no-bottom">{{ ($managedcare->terminationdate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managedcare->terminationdate,'date') : '' }}</p>
                </div>                       
            </div>


            <div class="form-group">
                {!! Form::label('feeschedule', 'Fee Schedule', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-3 control-label']) !!}
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-9">
                    <p class="show-border no-bottom">{{ $managedcare->feeschedule }}</p>
                </div>                       
            </div>


        </div><!-- /.box-body -->

    </div><!-- /.box -->
</div><!--/.col (left) -->
@stop 