@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
<?php $templates->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($templates->id,'encode'); ?>
    <section class="content-header">
		<h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Templates <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> View </span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('apptemplate')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/app_templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
	@include ('practice/questionnaires/tabs')
@stop


@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('templates/{templates}/edit'))
    <a href="{{ url('apptemplate/'.$templates->id.'/edit')}}" class="font600 font14 pull-right margin-r-5 "><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif
</div>


<div class="col-md-12 print-m-t-30">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 med-right-border">
                 <h3 class="med-orange align-break">{{ $templates->name }}</h3>
				<p class="no-bottom"><span class="med-green font600">Status : </span><span class=" patient-status-bg-form @if($templates->status == 'Active')label-success @else label-danger @endif">{{ $templates->status }}</span></p>
            </div>
           <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                <ul class="icons push no-padding">
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated by </span> <span class="pull-right">@if(@$templates->modifier != ''){{ App\Http\Helpers\Helpers::shortname($templates->updated_by) }} @else <span class="nill">- Nil - </span> @endif</li>
                   <li class="col-lg-12 col-md-12 col-sm-6 col-xs-12"><span class="med-green font600">Updated On </span><span class="pull-right">@if($templates->updated_at !='' && $templates->updated_at !='-0001-11-30 00:00:00' && $templates->updated_at !='0000-00-00 00:00:00')<span class='bg-date'>
                    {{ App\Http\Helpers\Helpers::timezone($templates->updated_at, 'm/d/y') }}
                </span>@else <span class="nill">- Nil - </span> @endif</li> 
                </ul>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">Content</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<p>{!! $templates->content !!}</p>
		</div><!-- /.box-body -->
	</div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@stop 