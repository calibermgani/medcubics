@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.role')}} font14"></i> Roles <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Medcubics </span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				 @if($checkpermission->check_adminurl_permission('api/admin/medcubicsrole/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/medcubicsrole/'])
				</li>
				 @endif
				 @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/role')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				 @endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/role/tabs')
@stop


@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null) 
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
    </div> 
        
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
			   <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Role List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_adminurl_permission('admin/role/create') == 1)
						<a href="{{ url('admin/role/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
			   <table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Role Name</th>
								<th>Role Type</th>
								<th>Status</th>
								<th>Created By</th>
								<th>Updated By</th>
								<th>Options</th>
							</tr>
						</thead>
						<tbody>
							@foreach($roles as $role)
							<?php $role->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($role->id,'encode'); ?>	
							<tr data-url="{{ url('admin/role/'.$role->id) }}" class="js-table-click clsCursor">
								<td>{{ $role->role_name }}</td>
								<td>{{ $role->role_type }}</td>
								<td>{{ $role->status }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($role->created_by) }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($role->updated_by) }}</td>
								<td>
									 @if($checkpermission->check_adminurl_permission('admin/adminpermission/{adminpermission}') == 1)
										<a href="{{ url('admin/adminpermission/'.$role->id) }}">Set Permission</a>
									 @endif
								</td>
							</tr>
							@endforeach
						</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
	@include('practice/layouts/favourite_modal')
@stop