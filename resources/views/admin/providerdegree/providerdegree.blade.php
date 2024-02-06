@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.providerdegree')}}" data-name="certificate"></i> Provider Degree</small>
        </h1>
        <ol class="breadcrumb">                  
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('api/admin/providerdegreereportsmedcubics/{export}') == 1)
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/providerdegreereportsmedcubics/'])
            </li>
            @endif
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/provider_degree')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="col-xs-12">
        <div class="box box-info no-shadow">
            <div class="box-header margin-b-10">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Provider Degree List</h3>
                <div class="box-tools pull-right margin-t-2">
                    @if($checkpermission->check_adminurl_permission('admin/providerdegree/create') == 1)
						<a href="{{ url('admin/providerdegree/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Provider Degree</a>
					@endif
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Provider Degree Name</th>
								<th>Created On</th>
								<th>Updated On</th>
								<th>Created By</th>
								<th>Updated By</th>
							</tr>
						</thead>
						<tbody>
							@foreach($degrees as $degree)
							<?php $degree->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($degree->id,'encode'); ?>
							<tr data-url="{{ url('admin/providerdegree/'.$degree->id) }}" class="js-table-click clsCursor">
								<td>{{ $degree->degree_name }}</td>
								<td>
									@if($degree->created_at != '' && $degree->created_at !="-0001-11-30 00:00:00" )
										{{ App\Http\Helpers\Helpers::dateFormat($degree->created_at,'date')}}
									@endif
								</td>
								<td>
									@if($degree->updated_by != '')
										{{ App\Http\Helpers\Helpers::dateFormat($degree->updated_at,'date')}}
									@endif
								</td>
								<td>{{ @$degree->user->short_name }}</td>
								<td>{{ @$degree->userupdate->short_name }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
<!--End-->
@stop