@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="table"></i>API</small>
        </h1>
        <ol class="breadcrumb">
		@php  $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apiconfig->id,'encode');  @endphp
        <li><a href="{{url('admin/apiconfig')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if($checkpermission->check_adminurl_permission('admin/apiconfig/{apiconfig}/edit') == 1)
            <li><a href="{{ url('admin/apiconfig/'.$apiid.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            @endif

            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/apiconfig')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
                        <td>API For</td>
                        <td>{!! $apiconfig->api_for !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API Name</td>
                        <td>{!! $apiconfig->api_name !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API Username</td>
                        <td>{!! $apiconfig->api_username !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API Password</td>
                        <td>{!! $apiconfig->api_password !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API Category</td>
                        <td>{!! $apiconfig->category !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API USPS</td>
                        <td>{!! $apiconfig->usps_user_id !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API Token</td>
                        <td>{!! $apiconfig->token !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td>{!! $apiconfig->host !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Port</td>
                        <td>{!! $apiconfig->port !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{!! $apiconfig->api_status !!}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>API URL</td>
                        <td>{!! $apiconfig->url !!}</td>
                        <td colspan="2"></td>
                    </tr>
                   
                    </tbody>
                </table>		 
        </div><!-- /.box-body -->

    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
@stop
