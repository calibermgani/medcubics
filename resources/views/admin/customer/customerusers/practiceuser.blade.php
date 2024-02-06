@extends('admin')

@section('toolbar')
<?php
if(isset($practice->encid) && $practice->encid != ''){
	$practice->id = $practice->encid;
}else{
	$practice->id = $practice->id;
}
?>
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>            
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right" ></i>Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Users</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/customer/'.$customer_id.'/customerpractices/'.$practice->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'admin/practiceuserreports/customer/'.$customer_id.'/'.$practice->id.'/'])
				</li>				
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop
    
@section('practice')
	@include ('admin/customer/customerpractices/tabs')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="box box-info no-shadow space20">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/practiceusers') == 1)
						<a href="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice_id.'/practiceusers') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Practice User</a>
					@endif			   
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>User Name</th>
							<th>Practice Name</th>
							<th>created By</th>
							<th>updated By</th>
							<th>updated On</th>		
							<th>Action</th>	
						</tr>
					</thead>
					<tbody>
						@foreach($setpracticeforusers as $setpracticeforuser)  
							<?php @$setpracticeforuser->user->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$setpracticeforuser->user->id,'encode');
							@$setpracticeforuser->user->customer_id= App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$setpracticeforuser->user->customer_id,'encode'); ?>
							<tr data-url="{{ url('admin/customer/'.$customer_id.'/practice/'.$practice_id.'/practiceusers/show/'.@$setpracticeforuser->user->id) }}" class="js-table-click clsCursor">
								<td>{{ @$setpracticeforuser->user->name }}</td>
								<td>{{ @$setpracticeforuser->practice->practice_name }}</td>
								<td>{{ @$setpracticeforuser->created_by->name }}</td>
								<td>{{ @$setpracticeforuser->updated_by->name }}</td>
								<td>{{ App\Http\Helpers\Helpers::dateFormat(@$setpracticeforuser->updated_at,'date') }}</td>
								<td>
									@if(@$setpracticeforuser->user->useraccess == "web")
										@if($checkpermission->check_adminurl_permission('customer/{customer_id}/customerusers/{practice_id}/setusersforpractice/{customerusers_id}/user') == 1)
											<a href="{{ url('admin/customer/'.$setpracticeforuser->user->customer_id.'/customerusers/'.$practice->id.'/setusersforpractice/'.$setpracticeforuser->user->id.'/user') }}">Set Practice</a>
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