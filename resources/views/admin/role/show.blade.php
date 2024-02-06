@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.role')}} font14"></i> Roles 
                @if($role->role_type=='Medcubics')<i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Medcubics</span>@endif
                @if($role->role_type=='Practice')<i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Practice</span>@endif
            </small>
        </h1>
        <ol class="breadcrumb">
            <?php $role->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($role->id,'encode'); ?>	
            @if($role->role_type=='Practice') 
            	<li><a href="{{ url('admin/practicerole')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @endif

            @if($role->role_type=='Medcubics') 
				<li><a href="{{ url('admin/medcubicsrole')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @endif
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
		@if($checkpermission->check_adminurl_permission('admin/role/{role}/edit') == 1)
			<a href="{{ url('admin/role/'.$role->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		@endif            
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">Role Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Role Name</td>
							<td>{{ $role->role_name }} </td>
						</tr>

						<tr>
							<td>Role Type</td>
							<td>{{ $role->role_type }} </td>
						</tr>

						<tr>
							<td>Status</td>
							<td><span class="patient-status-bg-form @if($role->status == 'Active') label-success @else label-danger @endif">{{ $role->status }}</span></td>
						</tr>

						<tr>
							<td>Created On</td>
							<td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($role->created_at,'date')}}</span></td>
						</tr>

						<tr>
							<td>Created By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($role->created_by) }}</td>
						</tr>

						<tr>
							<td>Updated On</td>
							<td>@if($role->updated_at !='' && ($role->updated_at != $role->created_at))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($role->updated_at,'date')}}</span>@endif</td>
						</tr>

						<tr>
							<td>Updated By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($role->updated_by) }}</td>
						</tr>

						@if($role->role_type=='Medcubics' && $checkpermission->check_adminurl_permission('admin/adminpermission/{adminpermission}') == 1)
							<tr>
								<td>Options</td>
								<td>
									<a href="{{ url('admin/adminpermission/'.$role->id) }}"><span class="med-orange font600">Set Permission</span></a>
								</td>
							</tr>
						@elseif($role->role_type=='Practice' && $checkpermission->check_adminurl_permission('admin/setpagepermissions/{setpagepermissions}/edit') == 1)
							<tr>
								<td>Options</td>
								<td>
									<a href="{{ url('admin/setpagepermissions/'.$role->id.'/edit') }}"><span class="med-orange font600">Set Permission</span></a>
								</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
@stop