@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.user')}} font14"></i> Users <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>User History </span></small>
			</h1>
			<ol class="breadcrumb">
				<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
				<li class="dropdown messages-menu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'export/userhistory/'])
            </li>
				@if($checkpermission->check_url_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/history')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif 
			</ol>
		</section>
	</div>
@stop
@section('practice-info')
@include ('practice/user/user_tabs')
@stop
@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null)
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div>
	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
		<div class="col-xs-12 space20">
			<div class="box box-info no-shadow">
				<div class="box-header margin-b-10">
					<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">User History List</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
				  <div class="table-responsive">
					<table id="list_noorder" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>IP address</th>
								<th>Browser name</th>
								<!--th>MAC address</th-->
								<th>Login time</th>
								<th>Logout time</th>
								<th>User</th>
							</tr>
						</thead>
						<tbody>
							@foreach($history as $history)
							<?php
								$logout_time = trim($history->logout_time);
							?>
								
								<tr class="default-cursor">
									<td>{{ $history->ip_address}}</td>
									<td>{{ $history->browser_name}}</td>
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$history->login_time,'time') }}</td>
									<td>@if(@$logout_time !='') {{ App\Models\Profile\UserLoginHistory::LogoutTime(@$history->logout_time) }} @else Current User @endif</td>
									<td>{{ App\Http\Helpers\Helpers::shortname($history->user_id) }}</td>									
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