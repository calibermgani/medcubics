@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.qualifier')}}" data-name="balance"></i> ID Qualifiers <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
			</h1>
			<?php $qualifiers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($qualifiers->id,'encode'); ?>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/qualifiers')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/id_qualifier')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">    		
		@if($checkpermission->check_adminurl_permission('admin/qualifiers/{qualifiers}/edit') == 1)
			<a href="{{ url('admin/qualifiers/'.$qualifiers->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		@endif
	</div>

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>ID Qualifiers Name</td>
							<td>{{strtoupper( $qualifiers->id_qualifier_name) }} </td>
						</tr>

						<tr>
							<td>Created On</td>
							<td>@if(App\Http\Helpers\Helpers::dateFormat($qualifiers->created_at,'date'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($qualifiers->created_at,'date')}}</span>@endif</td>
						</tr>

						<tr>
							<td>Created By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($qualifiers->created_by) }}</td>
						</tr>

						<tr>
							<td>Updated On</td>
							<td>@if($qualifiers->updated_at !='' && $qualifiers->updated_at != $qualifiers->created_at)<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($qualifiers->updated_at,'date')}}</span>@endif</td>
						</tr>

						<tr>
							<td>Updated By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($qualifiers->updated_by) }}</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
	<!--End-->
@stop