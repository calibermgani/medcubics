@extends('admin')

@section('toolbar')
<div class="row toolbar-header" >
    <?php $facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Scheduler Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Facility <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>List</span></small>
        </h1>
        <ol class="breadcrumb">
            <li class="hide"><a href="javascript:void(0)" data-url="{{ url('practicefacilityschedulerlist')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a>
            </li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            @if(count($facilityschedulers) > 0)
            <li class="dropdown messages-menu">
                <!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_stream_export', ['url' => 'api/schedulerfacilityreports/'.$facility->id.'/'])
            </li>
            @endif

            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/scheduler/facility_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">Scheduler List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="scheduler box-body">
            <div class="table-responsive mobile-scroll">
                <table id="mytable" class="table table-striped mobile-width" >
                    <thead>
                        <tr>
                            <th>Provider</th>                                
                            <th>Schedule Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>No of Occurrence</th>
                            <th>Repeat Every</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($facilityschedulers) > 0)
                        @foreach($facilityschedulers as $facility_scheduler)
                        <?php $facility_scheduler->provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$facility_scheduler->provider_id,'encode'); ?>
                        <tr>
                            <td colspan="7" style="background: #fff; border-right: none !important; border-left: none !important;"><span class="med-orange font600">{{ @$facility_scheduler->provider->provider_name.' '.@$facility_scheduler->provider->degrees->degree_name}}</span></td>
                        </tr>
                        <?php
                        $allfacilityschedulers = App\Models\ProviderScheduler::getAllfacilitySchedulerByProviderId($facility_scheduler->provider_id, $facility->id);
                        ?>
                        @foreach($allfacilityschedulers as $all_facility_scheduler)
                        <?php $all_facility_scheduler_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($all_facility_scheduler->id,'encode'); ?>
                        <tr data-url="{{ url('practicefacilityscheduler/'.$facility->id.'/'.$all_facility_scheduler_id) }}" class="js-table-click clsCursor cur-pointer">
                            <td></td>
                            <td><span class="@if($all_facility_scheduler->schedule_type == 'Daily') med-daily @elseif($all_facility_scheduler->schedule_type == 'Weekly') med-weekly @else med-monthly @endif">{{$all_facility_scheduler->schedule_type}}</span></td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat($all_facility_scheduler->start_date,'date') }}</td>
                            <td>@if($all_facility_scheduler->end_date_option != 'never'){{ App\Http\Helpers\Helpers::dateFormat($all_facility_scheduler->end_date,'date') }}@else Never @endif</td>
                            <td>@if($all_facility_scheduler->end_date_option == 'after'){{$all_facility_scheduler->no_of_occurrence}}@else -- @endif</td>
                            <td>@if($all_facility_scheduler->repeat_every > 1){{$all_facility_scheduler->repeat_every}} @endif 
                                @if($all_facility_scheduler->schedule_type == 'Daily')Days 
                                @elseif($all_facility_scheduler->schedule_type == 'Weekly')Weeks 
                                @elseif($all_facility_scheduler->schedule_type == 'Monthly')Months 
                                @endif
                            </td>
                            <td class="js-table-click hidden-print js-prevent-show">
                                <a target ="" class="js-delete-confirm" data-text='{{ trans("common.validation.confirmation_deleted") }}' href="{{ url('facilityscheduler/facility/'.$facility->id.'/delete/'.$all_facility_scheduler->id) }}"><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-name="trash" data-title='Delete'></i></a>
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
<script type="text/javascript">

/* Export Excel for Charges list*/
    $('.js_search_export_csv').click(function(){
        current_page = window.location.pathname.split("/");
        facility_id = current_page[current_page.length - 1];
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/facilityScheduledList";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "FacilitySchedulerController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "facilityScheduledListExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Facility_Scheduled_List"
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
        form_data  += "<input type='hidden' name='facility_id' value = '"+facility_id+"'><input type='hidden' name='export' value = 'xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $("#export_csv_div").html(form_data);
        $("#export_csv").submit();
        $("#export_csv").empty();
    });
</script>
@endpush