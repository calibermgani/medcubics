@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font14"></i> CPT <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
			</h1>
			<?php $cpt->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($cpt->id,'encode'); ?>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/cpt') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				 @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				 @endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/cpt/cpt-tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">                        
		 @if($checkpermission->check_adminurl_permission('admin/cpt/{cpt}/edit') == 1)
			<a href="{{url('admin/cpt/'.$cpt->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		 @endif
	</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
		   <i class="livicon" data-name="code"></i> <h3 class="box-title">Codes</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body table-responsive">
			<table class="table-responsive table-striped-view table">
				<tbody>
					<tr>
						<td>Type Of Service</td>
						<td>{{ $cpt->type_of_service }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Place Of Service</td>
						<td>@if($cpt->pos){{ $cpt->pos->pos }}@endif</td>
						<td></td>
					</tr>

					<tr>
						<td>Applicable Sex</td>
						<td>{{ $cpt->applicable_sex }}</td>
						<td></td>
					</tr>
					<tr>
						<td>Referring Provider</td>
						<td>{{ $cpt->referring_provider }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Age Limit</td>
						<td>{{ $cpt->age_limit }}</td>
						<td></td>
					</tr>
					
					<tr>
						<td>Modifier</td>
						<td>{{ @$cpt->modifier_id }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Revenue Code</td>
						<td><span @if($cpt->revenue_code != "")class="bg-number" @endif>{{ $cpt->revenue_code }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Drug Name</td>
						<td style="word-break:break-word;">{{ $cpt->drug_name }}</td>
						<td></td>
					</tr>

					<tr>
						<td>NDC Number</td>
						<td><span @if($cpt->ndc_number != "")class="bg-number" @endif>{{ $cpt->ndc_number }}</td>
						 <td><!--a id="document_add_modal_link_ndc_number" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/cpt/'.$cpt->id.'/ndc_number')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a--></td>
					</tr>

					<tr>
						<td>Min Units</td>
						<td>{{ $cpt->min_units }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Max Unit</td>
						<td>{{ $cpt->max_units }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Anesthesia Base Unit</td>
						<td>{{ $cpt->anesthesia_unit }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Service ID Qualifier</td>
						<td>{{ @$cpt->qualifier->id_qualifier_name }}</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		  <div class="bottom-space-10 hidden-sm hidden-xs">&emsp;</div>
		</div>
	</div>
</div><!--  Left side Content Ends -->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
		   <i class="livicon" data-name="credit-card"></i> <h3 class="box-title">Billing</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
				<tbody>
					<tr>
						<td>Allowed Amount</td>
						<td>{{ $cpt->allowed_amount }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Billed Amount</td>
						<td>{{ $cpt->billed_amount }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Required CLIA ID</td>
						<td>{{ $cpt->required_clia_id }}</td>
						<td></td>
					</tr>
					@if($cpt->required_clia_id == "Yes")
					<tr>
						<td>CLIA ID</td>
						<td><span @if($cpt->clia_id != "")class="bg-number" @endif>{{ $cpt->clia_id }}</td>
						<td><!--a id="document_add_modal_link_clia_id" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/cpt/'.$cpt->id.'/clia_id')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal">
						<i class="{{Config::get('app.document_upload_modal_icon')}}"></i></a--></td>
					</tr>
					@endif
					<tr>
						<td>Work RVU</td>
						<td>{{ $cpt->work_rvu }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Facility Practice RVU</td>
						<td>{{ $cpt->facility_practice_rvu }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Non Facility RVU</td>
						<td>{{ $cpt->nonfacility_practice_rvu }}</td>
						<td></td>
					</tr>

					<tr>
						<td>PLI RVU</td>
						<td>{{ $cpt->pli_rvu }}</td>
						<td></td>
					</tr>
					<tr>
						<td>Total Facility RVU</td>
						<td>{{ $cpt->total_facility_rvu }}</td>
						<td></td>
					</tr>

					<tr>
						<td>Total Non Facility RVU</td>
						<td>{{ $cpt->total_nonfacility_rvu }}</td>
						<td></td>
					</tr>
				   <tr>
						<td>Created On</td>
						<td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($cpt->created_at,'date')}}</span></td>
						  <td></td>
					</tr>

					<tr>
						<td>Created By</td>
						<td>@if($cpt->created_by != ''){{ App\Http\Helpers\Helpers::shortname($cpt->created_by) }}@endif</td>
						<td></td>
					</tr>
					
					<tr>
						<td>Updated On</td>
						<td>@if($cpt->updated_at !='' && $cpt->updated_at != $cpt->created_at)<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($cpt->updated_at,'date')}}</span>@endif</td>
						 <td></td>
					</tr>
					
					<tr>
						<td>Updated By</td>	
						<td>@if($cpt->updated_by != ''){{ App\Http\Helpers\Helpers::shortname($cpt->updated_by) }}@endif</td>
						 <td></td>
					</tr>
					
				</tbody>
			</table>
		</div>
	</div>
</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Full width Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
				<i class="livicon" data-name="doc-portrait"></i> <h3 class="box-title">Procedure Description</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Short Description</td>
							<td>{{ $cpt->short_description }}</td>
						</tr>

						 <tr>
							<td>Long Description</td>
							<td>{{ $cpt->long_description }}</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
    </div>
   
@stop
