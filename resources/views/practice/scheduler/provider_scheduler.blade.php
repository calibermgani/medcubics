@extends('admin')

@section('toolbar')
<div class="row toolbar-header" >
<?php
	// $provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode'); 
?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Scheduler Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Provider <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>List</span></small>
        </h1>
        <ol class="breadcrumb">
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			
			@if(count($providerschedulers) > 0)
				<li class="dropdown messages-menu">
                    
					<!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
					
					@include('layouts.practice_module_stream_export', ['url' => 'api/schproviderlistingreports/'.$provider->id.'/'])
				</li>
			@endif
           <li class="hide"><a href="javascript:void(0)" data-url="{{ url('practiceproviderschedulerlist')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/scheduler/provider_tabs')
@stop

@section('practice')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
				<i class="fa fa-bars"></i><h3 class="box-title">Scheduler List</h3>
				<div class="box-tools pull-right margin-t-2">				
					<a class="js-load-modal font600 font14" href="" data-target="#provider_scheduler_modal" data-url="{{url('addproviderscheduler/'.$provider->id)}}" data-backdrop="false" data-toggle="modal" data-target="#provider_scheduler_modal"><i class="fa fa-plus-circle"></i> New</a>   
				</div>
			</div><!-- /.box-header -->
			<div class="scheduler box-body">
				<div class="table-responsive mobile-scroll">
					<table id="mytable" class="table table-striped mobile-width">
					<thead>
						<tr>
							<th>Facility</th>                                
							<th>Schedule Type</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>No of Occurrence</th>
							<th>Repeat Every</th>
							<th>Action</th>
						</tr>
					</thead>
						<tbody>
							@if(count($providerschedulers) > 0)
							@foreach($providerschedulers as $provider_scheduler)
							<?php 
								$provider_scheduler->facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider_scheduler->facility_id,'encode');
							?>
							<tr>
								<td colspan="7" style="background: #fff; border-right: none !important; border-left: none !important;"><span class="med-orange font600">{{$provider_scheduler->facility->facility_name}}</span></td>
							</tr>
							<?php
								$allproviderschedulers = App\Models\ProviderScheduler::getAllProviderSchedulerByFacilityId($provider_scheduler->facility_id, $provider->id);
							?>
							@foreach($allproviderschedulers as $all_provider_scheduler)
							<?php
								$all_provider_scheduler_encid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($all_provider_scheduler->id,'encode'); 
							?>
							<tr data-url="{{ url('practiceproviderscheduler/'.$provider->id.'/'.$all_provider_scheduler_encid) }}" class="js-table-click clsCursor cur-pointer">
								<td></td>
								<td><span class="@if($all_provider_scheduler->schedule_type == 'Weekly') med-weekly @elseif($all_provider_scheduler->schedule_type == 'Daily') med-daily @else med-monthly @endif">{{$all_provider_scheduler->schedule_type}}</span></td>
								<td>{{ App\Http\Helpers\Helpers::dateFormat($all_provider_scheduler->start_date,'date') }}</td>
								<td>@if($all_provider_scheduler->end_date_option != 'never'){{ App\Http\Helpers\Helpers::dateFormat($all_provider_scheduler->end_date,'date') }}@else Never @endif</td> 
								<td>@if($all_provider_scheduler->end_date_option == 'after'){{$all_provider_scheduler->no_of_occurrence}}@else -- @endif</td>
								<td>@if($all_provider_scheduler->repeat_every > 1){{$all_provider_scheduler->repeat_every}} @endif 
									@if($all_provider_scheduler->schedule_type == 'Daily')Days 
									@elseif($all_provider_scheduler->schedule_type == 'Weekly')Weeks 
									@elseif($all_provider_scheduler->schedule_type == 'Monthly')Months 
									@endif
								</td>
								<td class="js-table-click hidden-print js-prevent-show">
									<a target ="" class="js-delete-confirm" data-text='{{ trans("common.validation.confirmation_deleted") }}' href="{{ url('practicescheduler/provider/'.$provider->id.'/delete/'.$all_provider_scheduler_encid) }}"><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-name="trash" data-title='Delete'></i></a>
								</td>
							</tr>
							@endforeach
							@endforeach
							@else
							<tr><td colspan="7"><p class="med-gray text-center no-bottom margin-t-10">No Records Found</p></td></tr>
							@endif
						</tbody>
					</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
<div id="export_csv_div"></div> 
@stop

@push('view.scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$("#fee_sch_example").DataTable({
			"order": [5, "desc"],
			columnDefs: [ { orderable: false, targets: [6] } ],
		});
	});

	/* Export Excel for Charges list*/
    $('.js_search_export_csv').click(function(){
        current_page = window.location.pathname.split("/");
        provider_id = current_page[current_page.length - 1];
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/providerScheduledList";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "ProviderSchedulerController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "providerScheduledListExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Provider_Scheduled_List"
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
        form_data  += "<input type='hidden' name='provider_id' value = '"+provider_id+"'><input type='hidden' name='export' value = 'xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_csv_div").html(form_data);
        $("#export_csv").submit();
        $("#export_csv").empty();
    });
</script>
@endpush