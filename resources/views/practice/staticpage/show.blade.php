@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $staticpages->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($staticpages->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Help <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('staticpage') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-15 margin-b-10">
    <a href="{{ url('staticpage/'.$staticpages->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->


        <div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->  
            <div class="form-group">
                {!! Form::label('Module Name', 'Module Name', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">
                        <?php if (strlen($staticpages->slug) == 3)
                                echo (strtoupper($staticpages->slug));
                             else
                                echo (ucfirst($staticpages->slug));
                        ?>
                    </p>
                </div>                
            </div>    

            <div class="form-group">
                {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ $staticpages->title}}</p>
                </div>                
            </div> 

            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-4  control-label']) !!}                                       
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 @if($errors->first('provider_id')) error @endif">  
                 @if($staticpages->status == 'Active')   
					{!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red','disabled']) !!} Inactive 
				@else	
					{!! Form::radio('status', 'Active',null,['class'=>'flat-red','disabled']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',true,['class'=>'flat-red']) !!} Inactive 
				@endif	
                    {!! $errors->first('status', '<p> :message</p>')  !!}          
                </div>              
            </div>
            
            <div class="form-group">
                {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">@if(@$staticpages->created_by != ''){{ App\Http\Helpers\Helpers::shortname($staticpages->created_by) }}@endif</p>
                </div>                
            </div> 
            
            <div class="form-group">
                {!! Form::label('Created On', 'Created On', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">
                        {{ App\Http\Helpers\Helpers::timezone($staticpages->created_at, 'm/d/y') }}
                    </p>
                </div>                
            </div> 
            @if(@$staticpages->updated_by != '')
            <div class="form-group">
                {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($staticpages->updated_by) }}</p>
                </div>                
            </div>
            @endif
            
            @if($staticpages->updated_at  >='12-11-2015')
            <div class="form-group">
                {!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::dateFormat($staticpages->updated_at,'date')}}</p>
                </div>                
            </div>
            @endif
            
            
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right Side content Starts -->
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="livicon" data-name="question"></i> <h3 class="box-title">Help Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body" style="word-break:break-word;">
            <p>{!! $staticpages->content !!}</p>

        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Right Side content Ends -->
<!--End-->
@stop 