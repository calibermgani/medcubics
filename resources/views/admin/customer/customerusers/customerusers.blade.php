@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Users</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/'.$customer->id) }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<?php $customer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customer->id,'encode'); ?>
				@if($checkpermission->check_adminurl_permission('admin/customerusersmedcubics/{id}/{export}') == 1)
					<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'admin/customerusersmedcubics/'.$customer->id.'/'])
					</li>
				@endif
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop
    
@section('practice')
	@include ('admin/customer/tabs')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="box box-info no-shadow space20">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User List</h3>
				<div class="box-tools pull-right margin-t-2">
				   @if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers/create') == 1)
						<a href="{{ url('admin/customer/'.$customer->id.'/customerusers/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Customer User</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Short Name</th>
							<th>User Name</th>
							<th>User Type</th>
							<th>Practice</th>
							<th>User Access</th>
							<th>Email</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($customerusers as $customeruser)
							<?php
								$customeruser_encid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($customeruser->id,'encode');
								$customeruser_name = App\Http\Helpers\Helpers::getNameformat("$customeruser->lastname","$customeruser->firstname",""); 
							?> 
							<tr data-url="{{ url('admin/customer/'.$customer->id.'/customerusers/'.$customeruser_encid) }}" class="js-table-click clsCursor">
								<td>{{ $customeruser->short_name }}</td>
								<td>{{ $customeruser_name }}</td>
								<td>{{ @$customeruser->useraccess }}</td>
								<td>
									@if($customeruser->practice_user_type!='practice_admin')
										{{ App\Http\Helpers\Helpers::getPracticeNames($customeruser->practice_access_id,$customeruser->id) }}
									@elseif($customeruser->practice_user_type=='practice_admin')
										{{ App\Http\Helpers\Helpers::getPracticeNames($customeruser->admin_practice_id,$customeruser->id) }}
									@endif
								</td>
								<td>{{ @$customeruser->practice_user_type }}</td>
								<td>{{ @$customeruser->email }}</td>
								<td>{{ @$customeruser->status }}</td>
								<td>
									@if(@$customeruser->useraccess == "web")
										@if($checkpermission->check_adminurl_permission('admin/customer/{id}/customerusers/{id}/setpracticeforusers') == 1)
											<a href="{{ url('admin/customer/'.$customer->id.'/customerusers/'.$customeruser_encid.'/setpracticeforusers') }}">Set Practice</a>
										@endif
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div> 
@stop