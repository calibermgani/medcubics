@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.modifiers')}}  font14"></i> Modifiers 
			<i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
		</h1>
		<ol class="breadcrumb">
			<?php $modifiers->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifiers->id,'encode'); ?>
			<li><a href="{{ url('admin/modifierlevel1')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			<li><a href ="" data-target="#js-help-modal" data-url="{{url('help/modifiers')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
    @include ('admin/modifier/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">                        
		@if($checkpermission->check_url_permission('admin/modifierlevel1/{modifierlevel1}/edit') == 1)
			<a href="{{ url('admin/modifierlevel1/'.$modifiers->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		@endif	
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="anchor"></i> <h3 class="box-title">Modifier Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body"><!-- Box Body Starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<span class="med-green font600"> Code</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
						<p>{{ $modifiers->code }}</p>
					</div>                               
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<span class="med-green font600"> Name</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
						<p>{{ $modifiers->name }}</p>
					</div>                               
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<span class="med-green font600"> Description</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
						<p class="line-height-26">{{ $modifiers->description }}</p>
					</div>                               
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<span class="med-green font600"> Anesthesia Base Unit</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
						<p>@if($modifiers->anesthesia_base_unit == '')<span class="nill">- Nil - </span> @else {{ $modifiers->anesthesia_base_unit }}@endif</p>
					</div>                               
				</div>  

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-desc-bg">
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<span class="med-green font600"> Status</span>
					</div>

					<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
						<p><span class="patient-status-bg-form @if($modifiers->status == 'Active')label-success @else label-danger @endif">{{ $modifiers->status }}</span></p>
					</div>                               
				</div>                                                        
			</div><!-- Box Body Ends --> 
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
	
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
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
							<td>Created By</td>
							<td>@if(@$modifiers->created_by != ''){{ App\Http\Helpers\Helpers::shortname($modifiers->created_by) }}@endif</td>
						</tr>
						<tr>
							<td>Created On</td>
							<td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($modifiers->created_at,'date')}}</td>
						</tr>
						<tr>
							<td>Updated By</td>
							<td>{{ App\Http\Helpers\Helpers::shortname($modifiers->updated_by) }}</td>
						</tr>
						<tr>
							<td>Updated On</td>
							<td>@if(($modifiers->updated_at !='') && ($modifiers->updated_at != '-0001-11-30 00:00:00'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($modifiers->updated_at,'date')}}</span>@endif</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
@stop 