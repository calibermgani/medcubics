@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $insurancetypes->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurancetypes->id,'encode'); ?>
    <section class="content-header">
        <h1>
             <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> User Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Insurance Type <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('insurancetypes')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>           
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance_types')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/apisettings/tabs')
@stop


@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">    
    <a href="{{ url('insurancetypes/'.$insurancetypes->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >

    <div class="box box-info no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->
        <div class="box-body  form-horizontal margin-l-10">
            <div class="form-group">
                {!! Form::label('typename', 'Type Name', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    <p class="show-border no-bottom">{!! $insurancetypes->type_name !!}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
			<div class="form-group">
                {!! Form::label('code', 'Code', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    <p class="show-border no-bottom">{!! $insurancetypes->code !!}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('cmstype', 'CMS Type', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    <p class="show-border no-bottom">{!! $insurancetypes->cms_type !!}</p>
                </div>
                <div class="col-sm-1 col-xs-2"></div>
            </div>
            <div class="form-group">
                {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">@if(@$insurancetypes->created_by != ''){{ App\Http\Helpers\Helpers::shortname($insurancetypes->created_by) }}@endif</p>         
                </div>                
            </div>
            <div class="form-group">
                {!! Form::label('Created On', 'Created On', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom"> @if($insurancetypes->created_at !='' && $insurancetypes->created_at !='-0001-11-30 00:00:00' && $insurancetypes->created_at !='0000-00-00 00:00:00')
                        {{ App\Http\Helpers\Helpers::dateFormat($insurancetypes->created_at, 'date') }}
                        @endif</p>         
                </div>                
            </div>
            @if(@$insurancetypes->updated_by != '' && @$insurancetypes->updated_by != 0)
            <div class="form-group">
                {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">{{ App\Http\Helpers\Helpers::shortname($insurancetypes->updated_by) }}</p>         
                </div>                
            </div>
            
            <div class="form-group">
                {!! Form::label('Updated On', 'Updated On', ['class'=>'col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label']) !!}                          
                <div class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
                    <p class="show-border no-bottom">
                        {{ App\Http\Helpers\Helpers::dateFormat($insurancetypes->updated_at, 'date') }}
                    </p>         
                </div>                
            </div>
            @endif
        </div>				

    </div><!-- /.box -->
</div><!--/.col (left) -->
@stop