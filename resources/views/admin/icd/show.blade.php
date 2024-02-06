@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.icd')}} font14"></i> ICD-10 <i class="fa fa-angle-double-right med-breadcrum"></i><span>View </span></small>
			</h1>
			<?php $icd->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($icd->id,'encode'); ?>	
			<ol class="breadcrumb">
				<li><a href="{{url('admin/icd/')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				 @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="" data-target="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/icd/icd10-tab')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">                                                
				  @if($checkpermission->check_adminurl_permission('admin/icd/{icd}/edit') == 1)
				<a href="{{url('admin/icd/'.$icd->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
				@endif
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			<i class="livicon" data-name="doc-landscape"></i> <h3 class="box-title">Description</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
						<span class="med-green font600"> Short Description</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
						<p>{{ $icd->short_description }}</p>
					</div>

				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
						<span class="med-green font600"> Long Description</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
						<p>{{ $icd->long_description }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			   <i class="livicon" data-name="code"></i> <h3 class="box-title">Code Information</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
				  <tbody>
						<tr>
							<td>Gender</td>
							<td>{{ $icd->sex }}</td>
							<td colspan="2"></td>
						</tr>

						<tr>
							<td>Age Limit Lower</td>
							<td>{{ $icd->age_limit_lower }}</td>
							<td colspan="2"></td>
						</tr>

						<tr>
							<td>Age Limit Upper</td>
							<td>{{ $icd->age_limit_upper }}</td>
							<td colspan="2"></td>
						</tr>

						 <tr>
							<td>Map to ICD 09</td>
							<td>{{ $icd->map_to_icd9 }}</td>
							<td colspan="2"></td>
						</tr>

						<tr>
							<td>Effective Date</td>
							<td>@if($icd->effectivedate !='0000-00-00' && $icd->effectivedate !='')<span class="bg-date">{{ ($icd->effectivedate !='0000-00-00') ?  App\Http\Helpers\Helpers::dateFormat($icd->effectivedate,'date') : '' }}</span>@endif</td>
							<td colspan="2"></td>
						</tr>

						<tr>
							<td>Inactive Date</td>
							<td>@if($icd->inactivedate !='0000-00-00' && $icd->inactivedate !='')<span class="bg-date">{{($icd->inactivedate !='0000-00-00') ?  App\Http\Helpers\Helpers::dateFormat($icd->inactivedate,'date') : '' }}</span>@endif</td>
							<td colspan="2"></td>
						</tr>
					</tbody>
				</table>
			</div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			   <i class="livicon" data-name="info"></i> <h3 class="box-title">General Information</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Created On</td>
							<td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($icd->created_at,'date')}}</span></td>
						</tr>
						
						<tr>
							<td>Created By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($icd->created_by) }}</td>
						</tr>

						<tr>
							<td>Updated On</td>
							<td>@if($icd->updated_by !='' || $icd->updated_by != 0 )<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($icd->updated_at,'date')}}</span>@endif</td>
						</tr>
						
						<tr>
							<td>Updated By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($icd->updated_by) }}</td>
						</tr>			
					</tbody>
				</table>
			</div>
        </div>
    </div>
@stop