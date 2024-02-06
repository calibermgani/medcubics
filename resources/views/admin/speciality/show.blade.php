@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
	@php $speciality->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($speciality->id,'encode'); @endphp
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.speciality')}} font14"></i> Specialty <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/speciality') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/speciality')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">			
		@if($checkpermission->check_adminurl_permission('admin/speciality/{speciality}/edit') == 1)
			<a href="{{ url('admin/speciality/'.$speciality->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		@endif
	</div>

	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			   <i class="livicon" data-name="checked-on"></i> <h3 class="box-title">Specialty Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">				
                <table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Specialty</td>
							<td>{{ $speciality->speciality }}</td>
						</tr>
						<tr>
							<td>Created On</td>
							<td>@if($speciality->created_at !='' && $speciality->created_at != '-0001-11-30 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($speciality->created_at,'date')}}</span>@endif</td>
						</tr>
						 <tr>
							<td>Created By</td>
							<td>
								<div class="col-lg-12 p-b-0 p-l-0">
									<a id="someelem{{hash('sha256',@$speciality->created_by)}}" class="someelem" data-id="{{hash('sha256',@$speciality->created_by)}}" href=""> {{ @$speciality->user->short_name }}</a> 
									<?php $user = $speciality->user; ?>
									@include ('layouts/user_hover')
								</div>
							</td>
						</tr>
						<tr>
							<td>Updated On</td>
							<td>@if($speciality->updated_at !='' && $speciality->updated_at != '-0001-11-30 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($speciality->updated_at,'date')}}</span>@endif</td>
						</tr>
						<tr>
							<td>Updated By</td>
							<td>
								<div class="col-lg-12 p-b-0 p-l-0">
									<a id="someelem{{hash('sha256',@$speciality->updated_by)}}" class="someelem" data-id="{{hash('sha256',@$speciality->updated_by)}}" href=""> {{ @$speciality->userupdate->short_name }}</a> 
									<?php $user = $speciality->userupdate; ?>
									@include ('layouts/user_hover')
								</div>
							</td>
						</tr>
						
					</tbody>
				</table>                                                        				
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
	<!--End-->
@stop