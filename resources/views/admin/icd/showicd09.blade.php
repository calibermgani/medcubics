@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="archive-extract"></i>ICD-9</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{url('admin/icd09')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if($checkpermission->check_adminurl_permission('admin/icd09/{icd09}/edit') == 1)
            <li><a href="{{url('admin/icd09/'.$icd->id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
             @endif

            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
             @endif

        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('admin/icd/icd9-tab')
@stop

@section('practice')
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="code"></i> <h3 class="box-title">Code Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
            <tbody>
                    <tr>
                        <td>Category</td>
                        <td>{{ $icd->category }}</td>
                    </tr>
                    <tr>
                        <td>Code</td>
                        <td>{{ $icd->code }}</td>
                    </tr>
                    <tr>
                        <td>Change Indicator</td>
                        <td>{{ $icd->change_indicator }}</td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($icd->created_at,'date')}}</span></td>
                    </tr>
                    <tr>
                        <td>Created By</td>
                        <td>@if($icd->created_by != ''){{ $icd->user->name }}@endif</td>
                    </tr>

            </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 space20"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table class="table-responsive table-striped-view table">
            <tbody>
                    <tr>
                         <td>Short Description</td>
                        <td>{{ $icd->short_desc }}</td>
                    </tr>
                    <tr>
                        <td>Medium Description</td>
                        <td>{{ $icd->medium_desc }}</td>
                    </tr>
                    <tr>
                        <td>Long Description</td>
                        <td>{{ $icd->long_desc }}</td>
                    </tr>
                    <tr>
                        <td>Updated At</td>
                        <td>@if($icd->updated_at !='')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($icd->updated_at,'date')}}</span>@endif</td>
                    </tr>

                        <tr>
                            <td>Updated By</td>
                            <td>@if($icd->updated_by != ''){{ $icd->userupdate->name }}@endif</td>
                        </tr>
            </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
</div>
</div>
<!--End-->
@stop