@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="table"></i>API</small>
        </h1>
        <ol class="breadcrumb">
		<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apilist->id,'encode');  ?>
        <li><a href="{{url('admin/apilist')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if($checkpermission->check_adminurl_permission('admin/apilist/{apilist}/edit') == 1)
            <li><a href="{{ url('admin/apilist/'.$apiid.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            @endif

            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/apilist')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice')

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">API Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
           
		<table class="table-responsive table-striped-view table">
                    <tbody>

                    <tr>
                        <td>API Name</td>
                        <td>{!! $apilist->api_name !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{!! $apilist->status !!}</td>
                        <td colspan="2"></td>
                    </tr>
                   
                    </tbody>
                </table>		 
        </div><!-- /.box-body -->

    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@stop
