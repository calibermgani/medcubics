@extends('admin')
@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.provider')}} font14"></i> Provider <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Overrides <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('provider/'.$provider->id.'/provideroverrides') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		@if($checkpermission->check_url_permission('provider/{providerid}/provideroverrides/{id}/edit') == 1)
            <li><a href="{{ url('provider/'.$provider->id.'/provideroverrides/'.$overrides->id.'/edit') }}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
		@endif	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/provider/tabs')
@stop
@section('practice')

<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table-responsive table-striped-view table">                    
                        <tbody>
                            <tr>
                                <td>Provider Name</td>
                                <td>{{ $overrides->provider_override->provider_name }}</td>
								<td></td>
                            </tr>
                            <tr>
                                <td>Tax ID</td>
                                <td><span class="bg-number">{{ $overrides->provider_override->etin_type_number }}</span></td>
								<td></td>
                            </tr>
                            <tr>
                                <td>NPI</td>
                                <td><span class="bg-number">{{ $overrides->provider_override->npi }}</span></td>
								<td></td>
                            </tr>
                            <tr>
                                <td>Provider ID</td>
                                <td>{{ $overrides->provider_id }}</td>
								<td>
								
								<a id="document_add_modal_link_provider_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/provider::overrides::'.$provider->id.'/'.$overrides->id.'/provider_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon_view')}}"></i></a>
								
								</td>
                            </tr>
                            <tr>
                                <td>ID Type</td>
                                <td>{{ $overrides->id_qualifier->id_qualifier_name }}</td>
								<td></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box Ends-->
        </div><!--  Left side Content Ends -->
    </div>
</div>
@stop 