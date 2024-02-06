@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.codes')}} font14"></i> Codes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="{{ url('admin/code')}}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<?php $code->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($code->id,'encode'); ?>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="javascript:void(0);" data-url="{{url('help/code')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
	<?php 
	$codecategoryarray = json_decode(json_encode($codecategory), True); 
	?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">                        
		@if($checkpermission->check_adminurl_permission('admin/code/{code}/edit') == 1)
			<a href="{{ url('admin/code/'.$code->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
		@endif
	</div>
	<div class="col-md-6 col-xs-12"><!--  Right side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			   <i class="livicon" data-name="code"></i> <h3 class="box-title">Code Details</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Code Category</td>
							<td><span >{{ @$codecategoryarray[$code->codecategory_id]   }}</span></td>
						</tr>
						<tr>
							<td>Transaction Code</td>
							<td><span>{{ $code->transactioncode_id   }}</span></td>
						</tr>
						<tr>
							<td>Description</td>
							<td style="width:70%"><span>{{  $code->description }}</span></td>
						</tr>
						<tr>
							<td>Start Date</td>
							<td><span <span @if(@$code->start_date != "0000-00-00") class="bg-date" @endif>{{ ($code->start_date!='0000-00-00')?App\Http\Helpers\Helpers::dateFormat($code->start_date,'date') : ''}}</span></td>
						</tr>
						<tr>
							<td>Last Modified Date</td>
							<td><span @if($code->last_modified_date!='0000-00-00') class='bg-date' @endif>{{ ($code->last_modified_date!='0000-00-00')? App\Http\Helpers\Helpers::dateFormat($code->last_modified_date,'date'): ''}}</span></td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
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
							 <td>Status</td>
							<td><span class="patient-status-bg-form @if($code->status == 'Active')  label-success  @else label-warning @endif">{{ $code->status }}</span></td>
						</tr>
						<tr>
							<td>Created On</td>
							<td>@if($code->created_at !='' && $code->created_at != '-0001-11-30 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($code->created_at,'date')}}</span>
							@endif			
							</td>
						</tr>
						 <tr>
							<td>Created By</td>
							<td>@if(@$code->created_by != ''){{ App\Http\Helpers\Helpers::shortname($code->created_by) }}@endif</td>
						</tr>
						<tr>
							<td>Updated On</td>
							<td>@if($code->updated_at !='' && $code->updated_at != '-0001-11-30 00:00:00')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($code->updated_at,'date')}}</span>@endif</td>
						</tr>
						<tr>
							<td>Updated By</td>
							<td>@if(@$code->updated_by != ''){{ App\Http\Helpers\Helpers::shortname($code->updated_by) }}@endif</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
	
	
	<div class="col-md-6 col-xs-12"><!--  Right side Content Starts -->
		<div class="box box-view no-shadow"><!--  Box Starts -->
			<div class="box-header-view">
			   <i class="livicon" data-name="code"></i> <h3 class="box-title">{{ $code->transactioncode_id   }} - Rule Engine</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table class="table-responsive table-striped-view table">
					<tbody>
						<tr>
							<td>Reason Type</td>
							<td><span >Billing</span></td>
						</tr>
						<tr>
							<td>Claim Status</td>
							<td><span>Denied</span></td>
						</tr>
						<tr>
							<td>Action</td>
							<td style="width:70%"><span>Next Responsibility</span></td>
						</tr>
						<tr>
							<td>Priority</td>
							<td><span>High</span></td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
	</div><!--  Left side Content Ends -->
<!--End-->
@stop