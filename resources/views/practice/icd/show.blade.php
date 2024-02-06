@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> ICD 10 <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('icd')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
@include ('practice/icd/icd10-tab')
@stop

@section('practice')
<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 margin-b-10 margin-t-10">    
    <a href="{{url('icd/'.@$icd->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
</div>

<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 no-padding">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Code Info Col Starts -->
        <div class="box no-shadow margin-b-10"><!-- Box Starts -->
            <div class="box-block-header with-border">
                <i class="livicon" data-name="code"></i> <h3 class="box-title">Code Details</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" tabindex="-1"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body  form-horizontal margin-l-10 p-b-40"><!-- Box Body Starts -->
                               
                <div class="form-group bottom-space-10">    
                    {!! Form::label('sex', 'Gender', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        @if($icd->sex == 'Male')  
							{!! Form::radio('sex', 'Male',true,['class'=>'flat-red']) !!} Male &emsp;
							{!! Form::radio('sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
							{!! Form::radio('sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
						@elseif($icd->sex == 'Female')	
							{!! Form::radio('sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
							{!! Form::radio('sex', 'Female',true,['class'=>'flat-red']) !!} Female &emsp;
							{!! Form::radio('sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
					   @elseif($icd->sex == 'Others')
							{!! Form::radio('sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
							{!! Form::radio('sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
							{!! Form::radio('sex', 'Others',true,['class'=>'flat-red']) !!} Others
					   @else
							{!! Form::radio('sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
							{!! Form::radio('sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
							{!! Form::radio('sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
						@endif	
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('age_limit_lower', 'Age Limit Lower', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-2 col-md-3 col-sm-8 col-xs-4">
                        <p class="show-border no-bottom">{{@$icd->age_limit_lower }}</p>
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('age_limit_upper', 'Age Limit Upper', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-2 col-md-3 col-sm-8 col-xs-4">
                        <p class="show-border no-bottom">{{ @$icd->age_limit_upper }}</p>
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('map_to_icd9', 'Map to ICD 9', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4">
                        <p class="show-border no-bottom">{{ @$icd->map_to_icd9 }}</p>
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('effectivedate_label', 'Effective Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4">
                        <p class="show-border no-bottom">{{ (@$icd->effectivedate!='0000-00-00' && @$icd->effectivedate != '') ? App\Http\Helpers\Helpers::dateFormat(@$icd->effectivedate,'date') : '' }}</p>
                    </div>
                    <div class="col-sm-1"></div>
                </div>

                <div class="form-group">    
                    {!! Form::label('inactivedate_label', 'Inactive Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-4">
                        <p class="show-border no-bottom">{{ (@$icd->inactivedate!='0000-00-00'  && @$icd->inactivedate != '') ? App\Http\Helpers\Helpers::dateFormat(@$icd->inactivedate,'date') : '' }}</p>
                    </div>
                    <div class="col-sm-1"></div>
                </div>


            </div><!-- Box Body Ends -->
        </div><!-- Box Starts -->
    </div><!-- Code Info Col Ends -->


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Description Col Starts -->
        <div class="box no-shadow margin-b-10"><!-- Box Starts -->
            <div class="box-block-header with-border">
                <i class="livicon" data-name="doc-landscape"></i> <h3 class="box-title">Description</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" tabindex="-1"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body  form-horizontal margin-l-10">                
                <div class="form-group">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600">
                        Short Description
                    </div>                                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p class="show-border no-bottom">{{ $icd->short_description }}</p>

                    </div>
                </div>

                <div class="form-group space20">                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600">
                        Long Description
                    </div>	
                   
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p class="show-border no-bottom">{{ $icd->long_description }}</p>

                    </div>
                </div>

                <div class="form-group space20">                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600">
                        Statement Description
                    </div>	                   
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p class="show-border no-bottom">{{ $icd->statement_description }}</p>

                    </div>
                </div>
            </div><!-- Box Body Ends -->                        
        </div><!-- Box Ends -->
    </div><!-- Description Col Ends -->
</div>
@include('practice/layouts/favourite_modal')
@stop            