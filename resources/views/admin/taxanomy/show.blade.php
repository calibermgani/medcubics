@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.taxanomy')}} font14"></i> Taxonomy <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
			</h1>
			@php $taxanomy->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($taxanomy->id,'encode'); @endphp
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/taxanomy')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			   <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/taxanomy')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">           				
		@if($checkpermission->check_adminurl_permission('admin/taxanomy/{taxanomy}/edit') == 1)
			<a href="{{ url('admin/taxanomy/'.@$taxanomy->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
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
								<td>Code</td>
								<td><span @if(@$taxanomy->code !="") class="bg-number" @endif>{{ @$taxanomy->code }}</td>
							</tr>
							<tr>
								<td>Specialty</td>
								<td>{{ @$taxanomy->speciality->speciality }}</td>
							</tr>
							 <tr>
								<td>Description</td>
								<td>{{ @$taxanomy->description }}</td>
							</tr>
							<tr>
								<td>Created By</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($taxanomy->created_by) }}</td>
							</tr>
							<tr>
								<td>Created On</td>
								<td>@if((@$taxanomy->created_at != '') &&($taxanomy->created_at != '-0001-11-30'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($taxanomy->created_at) }} </span>@endif</td>
							</tr>
							<tr>
								<td>Updated By</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($taxanomy->updated_by) }}</td>
							</tr>
							<tr>
								<td>Updated On</td>
								<td>@if((@$taxanomy->updated_by != '' || @$taxanomy->updated_by != 0) )<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($taxanomy->updated_at) }}</span> @endif</td>
							</tr>
						</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->

@stop