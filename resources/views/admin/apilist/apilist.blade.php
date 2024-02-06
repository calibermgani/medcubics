@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="pen"></i> API List</small>
        </h1>
        <ol class="breadcrumb">            
     		@if($checkpermission->check_url_permission('apilist') == 1)
                <li><a href="{{ url('admin/apilist/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>    
    		@endif		
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
				 @include('layouts.practice_module_export', ['url' => 'api/admin/apilistreports/'])
            </li>

            <li><a href="#js-help-modal" data-url="{{url('help/apilist')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
                            <th>API Name</th>
							<th>Status</th>
							<th>Created By</th>
							<th>Updated By</th>
							<th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apilist as $apilist)
						
						<?php $apiid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($apilist->id,'encode');  ?>
						
                        <tr data-url="{{ url('admin/apilist/'.$apiid) }}" class="js-table-click clsCursor">
                            <td>{{ @str_limit($apilist->api_name,25) }}</td>
							<td>{{ @$apilist->status }}</td>
							<td>{{ @$apilist->created_by->name }}</td>
							<td>{{ @$apilist->updated_by->name }}</td>
							<td>{{ App\Http\Helpers\Helpers::dateFormat($apilist->updated_at,'date') }}</td>
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



