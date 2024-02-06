@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="archive-extract"></i>ICD-9</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="#" onclick="history.go(-1);return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
           @if($checkpermission->check_adminurl_permission('admin/icd09/create') == 1)
            <li><a href="{{ url('admin/icd09/create') }}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i></a></li>
            @endif
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/icd09reportsmedcubics/{export}') == 1)
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/icd09reportsmedcubics/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('admin/icd/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
           <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">ICD-9 List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="list-icd9-admin" class="table table-bordered table-striped">
                <thead>
                    <tr>
                      <th>Code</th>
                      <th>Change Indicator</th>
                      <th>Code Status</th>
                      <th>Short Description</th>
                      <th>Created By</th>
                      <th>Updated By</th>
                    </tr>
                </thead>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop