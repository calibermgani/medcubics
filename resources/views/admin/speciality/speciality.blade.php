@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.speciality')}} font14"></i> Specialty</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('api/admin/specialityreports/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/specialityreports/'])
				</li>
				@endif
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/speciality')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
					<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Specialty List</h3>
					<div class="box-tools pull-right margin-t-2">
						@if($checkpermission->check_adminurl_permission('admin/speciality/create') == 1)
							<a href="{{ url('admin/speciality/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Specialty</a>
						@endif
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
				  <div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Specialty</th>
								<th>Created On</th>
								<th>Updated On</th>
								<th>Created By</th>
								<th>Updated By</th>
							</tr>
						</thead>
						<tbody>
							@foreach($specialities as $speciality)
								<?php 
									$createdBy = $updatedBy = '';
									$speciality_encid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($speciality->id,'encode');
								?>
								<tr data-url="{{ url('admin/speciality/'.$speciality_encid) }}" class="js-table-click clsCursor">
									<td>{{ $speciality->speciality}}</td>
									<td>
										@if($speciality->created_at != '' && $speciality->created_at != "-0001-11-30 00:00:00" )
											{{ App\Http\Helpers\Helpers::dateFormat($speciality->created_at,'date')}}
										@endif
									</td>
									<td>
										@if($speciality->updated_at != '' && $speciality->updated_at != "-0001-11-30 00:00:00" )
											{{ App\Http\Helpers\Helpers::dateFormat($speciality->updated_at,'date')}}
										@endif
									</td>
									<td>
										<div class="col-lg-12 p-b-0 p-l-0">
											<a id="someelem{{hash('sha256',@$speciality->created_by)}}" class="someelem" data-id="{{hash('sha256',@$speciality->created_by)}}" href=""> {{ @$speciality->user->short_name }}</a> 
											<?php $user = $speciality->user; ?>  
											@include ('layouts/user_hover')
										</div>
									</td>
									<td>
										<div class="col-lg-12 p-b-0 p-l-0">
											<a id="someelem{{hash('sha256',@$speciality->updated_by)}}" class="someelem" data-id="{{hash('sha256',@$speciality->updated_by)}}" href=""> {{ @$speciality->userupdate->short_name }}</a> 
											<?php $user = $speciality->userupdate; ?>  
											@include ('layouts/user_hover')
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				  </div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div><!-- Inner Content for full width Ends -->
<!--End-->

@stop