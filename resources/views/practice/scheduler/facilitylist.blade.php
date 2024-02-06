@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Scheduler Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Facility</span></small>
        </h1>
        <ol class="breadcrumb">
			
			 <li class="dropdown messages-menu">
                             <!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
				 @include('layouts.practice_module_stream_export', ['url' => 'api/facilityschedulerreports/'])
            </li>
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <!--li><a href="{{ url('api/facilityschedulerreports/export') }}"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li-->
            <li class="hide"><a href="javascript:void(0)" data-url="{{ url('practiceproviderschedulerlist')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/scheduler')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('practice/scheduler/scheduler-tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">	
	<div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Facility List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="js_table_search_listing">	<!-- Box Body Starts -->
				@include('practice/scheduler/facilitytablelist')
			</div><!-- /.box-body ends -->
        </div>
    </div><!-- /.box -->
</div>
<div id="export_csv_div"></div>
@stop

@push('view.scripts')
<script type="text/javascript">
    
/* Export Excel for Charges list*/
    $('.js_search_export_csv').click(function(){
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/facilityScheduler";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "FacilitySchedulerController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "getFacilitySchedulerExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Facility_Scheduler_List"
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
    });
</script>
@endpush