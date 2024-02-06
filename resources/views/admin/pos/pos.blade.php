@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">

		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.pos')}}" data-name="location"></i> POS</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				 @if($checkpermission->check_adminurl_permission('api/admin/placeofservicereports/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/placeofservicereports/'])
				</li>
				 @endif
				 @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/pos')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
					<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">POS List</h3>
					<div class="box-tools pull-right margin-t-2">
						 @if($checkpermission->check_adminurl_permission('admin/placeofservice/create') == 1)
						 <a href="{{ url('admin/placeofservice/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New POS</a>
					 @endif
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
				  <div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Code</th>
								<th>Place Of Service</th>
								<th>Created By</th>
								<th>Created On</th>
								<th>Updated By</th>
								<th>Updated On</th>
							</tr>
						</thead>
						<tbody>
							@foreach($pos as $item)
							<?php $item->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($item->id,'encode'); ?>
							<tr data-url="{{ url('admin/placeofservice/'.$item->id) }}" class="js-table-click clsCursor">
								<td>{{ $item->code}}</td>
								<td>{{ str_limit($item->pos, 20, '..')}}</td>
								<td>{{ @$item->user->short_name }}</td>
								<td>@if(@$item->created_at != ''){{ App\Http\Helpers\Helpers::dateFormat(@$item->created_at) }}@endif</td>
								<td>{{ @$item->userupdate->short_name }}</td>
								<td>@if(($item->updated_at != '') &&($item->updated_at != '-0001-11-30')){{ App\Http\Helpers\Helpers::dateFormat($item->updated_at) }} @endif</td>
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