@extends('admin')
@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1><small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Fee Schedule</span></small></h1>
			<ol class="breadcrumb">
				
				<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
				@if(count($feeschedules) > 0)
				<li class="dropdown messages-menu">
                                    <!--<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
				@include('layouts.practice_module_stream_export', ['url' => 'api/feeschedulereports/'])
				</li>
				@endif
				<li><a href="#js-help-modal" data-url="{{url('help/fees_schedule')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div>
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- col-12 starts -->
		<div class="box box-info no-shadow"><!-- Box Starts -->
			<div class="box-header margin-b-10">
				<i class="fa fa-bars"></i><h3 class="box-title">Fee Schedule List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_url_permission('feeschedule/create') == 1)
						<a href="{{ url('feeschedule/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Fee Schedule</a>
					@endif	
				</div>
			</div><!-- /.box-header -->
			<div class="box-body"><!-- Box body starts -->
				<div class="table-responsive"> 
					<table id="fee_sch_example" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>File Name</th>
								<th>Year</th>
								<th>Insurance</th>
								<th>Percentage</th>
								<th>Created By</th>
								<th>Uploaded On</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($feeschedules as $feeschedule)
								<tr style="cursor: default;">
									@if(@$feeschedule->fee_schedule->file_name!='')
										<?php
										$feeschedule_arr = explode(".",@$feeschedule->fee_schedule->file_name);
										$file_name		 = $feeschedule_arr[0];
										?>
									@else
										<?php $file_name = ''; ?>
									@endif
									<td>{{ str_limit(@$file_name, 25, '...') }}</td>
									<td>{{ @$feeschedule->fee_schedule->choose_year }}</td>
									<td>{{ (isset($feeschedule->insurance_info->short_name) && !empty($feeschedule->insurance_info->short_name) ? $feeschedule->insurance_info->short_name : 'Default') }}</td>
									<td>@if(@$feeschedule->fee_schedule->percentage != '') {{ @$feeschedule->fee_schedule->percentage }} @else - @endif</td>
									<td>@if(@$feeschedule->fee_schedule->created_by != ''){{ App\Http\Helpers\Helpers::shortname(@$feeschedule->fee_schedule->created_by) }}@endif</td>
									<?php /*
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$feeschedule->fee_schedule->created_at,'date') }}</td>
									*/ ?>
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$feeschedule->fee_schedule->created_at, 'date') }}</td>
									<td class="td-c-5 text-center">
										<span>
											<a href="{{url('feeschedule_file/'.@$feeschedule->fee_schedule->saved_file_name )}}" class="font14 font600 margin-r-5"><i class="fa fa-clipboard" data-placement="bottom"  data-toggle="tooltip" data-original-title="Download"></i></a>
										</span>
										<?php $feeschedule_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$feeschedule->fee_schedule->id,'encode'); ?>
										<a href="{{ url('feeschedule/delete/'.@$feeschedule_id) }}" class="js-delete-confirm hide" data-text="Are you sure to delete the entry?">
										<span data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete">
										<i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-name="trash" data-title='Delete'></i>
										</span>
										</a>
										@if(@$feeschedule->status == "Active")
											<a href="{{ url('feeschedule/statusChange/'.@$feeschedule_id.'/Inactive') }}" class="js-delete-confirm" data-text="Are you sure would you like to Inactive?">
												<span data-placement="bottom"  data-toggle="tooltip" data-original-title="Active">
													<i class="fa fa-check" data-placement="bottom"  data-name="check" data-title='Active'></i>
												</span>
											</a>
										@else
											<a href="{{ url('feeschedule/statusChange/'.@$feeschedule_id.'/Active') }}" class="js-delete-confirm" data-text="Are you sure would you like to Active?">
												<span data-placement="bottom"  data-toggle="tooltip" data-original-title="Inactive">
													<i class="fa fa-ban" data-placement="bottom"  data-name="ban" data-title='Inactive'></i>
												</span>
											</a>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- col-12 ends -->
	<!--End-->
<div id="export_csv_div"></div>
@stop   

@push('view.scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$("#fee_sch_example").DataTable(
	{
		"order": [5, "desc"],
		columnDefs: [ { orderable: false, targets: [6] } ],
	});
});

/* Export Excel for Charges list*/
/*    $('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/feeschedule";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "FeescheduleController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "getReport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Fee_Schedule"
        });
        form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value.length > 0) {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='export' value = 'yes'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_csv_div").html(form_data);
        $("#export_csv").submit();
        $("#export_csv").empty();
    });*/
</script>
@endpush