@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.resources')}} font14"></i> Resources</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('resources') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="{{ url('resources/create') }}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
          <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/resourcesreports/'])
                      </li>
            
            <li><a href="#js-help-modal" data-url="{{url('help/resources')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>            
        </ol>
    </section>

</div>
@stop

@section('practice')
<div class="col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="fa fa-bars"></i><h3 class="box-title">Resources List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive"> 
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Resource Name</th>
                        <th>Resource Code</th>
                        <th>Resource Facility</th>
                        <th>Default Provider</th>
						<th>Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                    <tr data-url="{{ url('resources/'.$resource->id) }}" class="js-table-click clsCursor">
                        <td>{{ str_limit($resource->resource_name, 25, '...') }}</td>						  
                        <td>{{ $resource->resource_code }}</td>                        
						<td>
    						@if($resource->facility)
    						<span class="js-display-detail">{{ $resource->facility->facility_name }}</span>
    					    @include('layouts.facilitypop', array('data' => $resource->facility))
    					    @endif
						</td>
                        <td>@if($resource->provider)@include ('layouts/provider_popup_msg', array('provider_det'=>$resource->provider))@endif</td>
                        <td>{{ $resource->phone_number }}</td>      
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop 