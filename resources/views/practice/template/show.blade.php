@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
<?php $templates->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($templates->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('templates')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>		
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
	@include ('practice/template/show_tabs')
@stop

@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
        @if($checkpermission->check_url_permission('templates/{templates}/edit'))
        <a href="{{ url('templates/'.$templates->id.'/edit')}}" class="font600 font14 pull-right margin-r-5 "><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
        @endif
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
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