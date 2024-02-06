@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Scheduler Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Provider</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			 <li class="dropdown messages-menu">
                             <!--<a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
				 @include('layouts.practice_module_stream_export', ['url' => 'api/schproviderreports/'])
            </li>
            
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
            <i class="fa fa-bars"></i><h3 class="box-title">Provider List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            
			<div id="js_table_search_listing">	<!-- Box Body Starts -->
				 @include('practice/scheduler/providertablelist')
			</div><!-- /.box-body ends -->		
			
        </div>
    </div><!-- /.box -->
</div>

<div id="form-content" class="modal fade in">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Event</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-list">

                    <li class="nav-header">Upload</li>
                    <li><input class="input-xlarge" value="" type="file" name="upload"></li>
                    <li class="nav-header">Message</li>
                    <li><textarea class="form-control" placeholder="Description"> 
                        </textarea></li>
                </ul> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-medcubics-small">Submit</button>
                <a href="#" class="btn btn-medcubics-small" data-dismiss="modal">Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  
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
    $('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/providerScheduler";
        form = $('form').serializeArray();
        var data_arr = [];
        data_arr.push({
            name : "controller_name", 
            value:  "ProviderSchedulerController"
        });
        data_arr.push({
            name : "function_name", 
            value:  "getProviderSchedulerExport"
        });
        data_arr.push({
            name : "report_name",
            value:  "Providers_Scheduler_List"
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