@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="pen"></i> API List</small>
        </h1>
        <ol class="breadcrumb">
                <li><a href="{{ url('admin/apiconfig/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
				 {{-- @include('layouts.practice_module_export', ['url' => 'api/admin/apilistreports/']) --}}
            </li>

            <li><a href="#js-help-modal" data-url="{{url('help/apiconfig')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice')
 <div class="col-lg-12">
@if(Session::get('message')!== null) 
<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
@endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" ><!-- Col-12 starts -->
    <div class="box box-info no-shadow"><!-- Box Starts -->
        <div class="box-header with-border">
		    <i class="fa fa-bars font14"></i><h3 class="box-title">API List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">	<!-- Box Body Starts -->
            <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped ">         
                    <thead>
                        <tr>
                            <th>API For</th>
                            <th>API Name</th>
                            <th>Category</th>
							<th>Status</th>
                            <th>Created By</th>
							<th>Updated By</th>
							<th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apiconfig as $config)
						<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($config->id,'encode');  ?>
						
                        <tr style="cursor: pointer;" data-url="{{ url('admin/apiconfig/'.$apiid.'/show') }}" class="js-table-click clsCursor">
                            <td>{{ @$config->api_for }}</td>
                            <td>{{ @$config->api_name }}</td>
                            <td>{{ @$config->category }}</td>
							<td>{{ @$config->api_status }}</td>
                            <td>{{ @$config->created_by->name }}</td>
							<td>{{ @$config->updated_by->name }}</td>
							<td>{{ App\Http\Helpers\Helpers::dateFormat($config->updated_at,'date') }}</td>
                        </tr>
                        @endforeach      
                    </tbody>
                </table>
			</div>                                
        </div><!-- /.box-body ends -->
    </div><!-- /.box  ends -->
</div><!-- Col-12 Ends -->
<!--End-->
@stop