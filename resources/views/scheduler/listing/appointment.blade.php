@extends('admin')

@section('toolbar')
<style type="text/css">
.light_green{
    color: #00877f80 !important;
}
</style>
<style type="text/css">
    .snackbar-alert, .snackbar-alert{
        min-width: 400px;
    }
    .progress, .progress > .progress-bar, .progress .progress-bar, .progress > .progress-bar .progress-bar{
        border-radius: 25px;
    }
    .progress-bar{
        background-color: #f07d08;
        height : 100%;        
    }
    .progress-bar-animated{
        animation: progress-bar-stripes 1s linear infinite;
    }
    .progress{  
        padding: 0px;
        height: 15px;
    }
</style>
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-calendar-o font16"></i> Scheduler <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Appointment List</span></small>
        </h1>
        <ol class="breadcrumb">
          
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_stream_export', ['url' => 'api/schedulerlistreport'])
            </li>
         
            <li><a href="#js-help-modal" data-url="{{url('help/appointmentlist')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>

        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop
@section('practice')
<div class="col-lg-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>    

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Appointment List</h3>
        </div><!-- /.box-header -->
        
        <div class="box-body table-responsive">
          <!--  <p><input type="checkbox" class="js_all_appointment font600" id="all-appointments"> <label class="cur-pointer med-orange font600" for="all-appointments">All Appointments</label></p>-->
            @include('layouts.search_fields', ['search_fields'=>$search_fields])   
            <div class="table-responsive">                    
                <?php /*
                 * Called the Appointment listing blade file
                 */ ?>                        

                <div class="ajax_table_list hide"></div>
                <div class="data_table_list table-responsive" id="js_ajax_part">
                    <table id="scheduler_column_list" class="table table-striped mobile-width">   
                        <thead>
                            <tr>
                                <th>Acc No</th>
                                <th>Patient Name</th>
                                <th>DOB</th>
                                <th>Facility</th>
                                <th>Rendering</th>
                                <th>Appt Date</th>
                                <th>Appt Time</th>
                                <th>Reason for Visit</th>
                                <th> Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>                     
                        </tbody>
                    </table>
                </div>
                <style>
                    .disabled {
                        opacity: 0.5;
                        pointer-events: none;
                        cursor: default;
                    }           
                </style>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
    <!--End-->

<?php /* popup open in */ ?>
<div id="js_scheduler_edit_appt_list" class="modal fade in"></div>
<div id="fullCalendarModal" class="modal fade in"></div>
<div id="fullCalendarModal_schedular" class="modal fade in">
	<div class="modal-md-scheduler">
		<div class="modal-content">           
			<div class="modal-body no-padding yes-border med-border-color">

			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<?php
    /*
        ### Appointment Listing page in click for Delete Appointment & Cancel appointment event ###
    */
?>
<div id="appointment_cancel_delete_modal" class="modal fade in">
	<div class="modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close js-app_appointment_operation_cancel" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				{!! Form::hidden('cancel_delete_option','',['class'=>'form-control']) !!} 
				<div class="form-group">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						{!! Form::textarea('reason',null,['class'=>'form-control js-appointment_cancel_delete_reason','maxlength'=>'150','placeholder'=>'Reason']) !!} 
						<small id='reason_err' class='hide help-block med-red' data-bv-validator='notEmpty' data-bv-for='reason_err' data-bv-result='INVALID'>Enter the reason!</small>
					</div>
					<div class="col-sm-1"></div>
				</div>
				<!-- Modal Footer -->
				<div class="modal-footer text-right margin-r-5">
					{!! Form::button('Save', ['class'=>'btn btn-medcubics-small margin-t-8 js-app_cancel_del_submit']) !!}
					{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small margin-t-8 js-app_appointment_operation_cancel']) !!}
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 
<div id="export_csv_div"></div> 
  <div class="hide" id="showmenu-bar">
    <span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
      <div class="snackbar-alert success menu">
        <h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Generate Report</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>  
        <div id="append_report_list">
           
        </div>          
    </div>
</div>
@stop

@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
    var api_site_url = '{{url("/")}}';
	var listing_page_ajax_url = api_site_url + '/api/getschedulertablevalues';
	var dataArr = {};   
	var wto = '';
	var column_length = $('#scheduler_column_list thead th').length;
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = {};
		wto = setTimeout(function() {  
			$('select.auto-generate').each(function(){
				 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
			});                                                                                // Getting all data in select fields 
			$('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			});                                                                                // Getting all data in input fields
			dataArr = {data:data_arr};
			accessAll();
			//$("#patients_column_list").DataTable().clearPipeline().draw();                      // Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */
	
	function accessAll() {
		var selected_column = ['Acc No', 'Patient Name', 'DOB', 'Facility', 'Rendering Provider', 'Appt Date', 'Appt Time', 'Status'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		schedulerSearch(allcolumns); /* Trigger datatable */
	}
		
	$(document).on('click, ifToggled change', '.js_all_appointment', function () {
		if ($('.js_all_appointment').is(":checked")) {
			listing_page_ajax_url = '';
			listing_page_ajax_url = api_site_url + "/api/getschedulertablevalues/all";
			schedulerSearch();
		} else {
			listing_page_ajax_url = '';
			listing_page_ajax_url = api_site_url + "/api/getschedulertablevalues";
			schedulerSearch();
		}
	});
	
	$(function () {
		displayLoadingImage();
	});
	
	$(document).ready(function () { 
		/* Search function start */		
		$("select[name='patient_search']").on('change', function (e) {
			$('#scheduler_column_list td').unhighlight();
			var allcolumns = [];
			var selected_text = $(this).select2('data')['text'];
			//var selected_text = $(this).find("option:selected").text();               
			var selected_column = ['Acc No', 'Patient Name', 'DOB', 'Facility', 'Rendering Provider', 'Appt Date', 'Appt Time', 'Status'];
			var access_all = ['All'];
			if (jQuery.inArray(selected_text, selected_column) !== -1) {
				var return_index = selected_column.indexOf(selected_text);
				for (var i = 0; i < column_length; i++) {
					if (i == return_index) {
						allcolumns.push({"name": selected_column[i], "bSearchable": true});
					} else {
						allcolumns.push({"name": selected_column[i], "bSearchable": false}); /* searchable value */
					}
				}
				//patientSearch(allcolumns); /* Trigger datatable */
			}
			if (jQuery.inArray(selected_text, access_all) !== -1) {
				accessAll(); /* Trigger datatable */
			}
		});
		/* MEDV2-470 - issues fixed */
		//setTimeout(function(){$('input[name="scheduled_at"]').val('<?php echo Request::get('scheduled_at'); ?>'); }, 50);
		//setTimeout(function(){$('input[name="created_at"]').val('<?php echo Request::get('created_at'); ?>'); }, 50);				

		$(document).on('keyup', '.dataTables_filter input', function () {
			var str = $('.dataTables_filter input').val();		
			if($.trim(str) != ''){	
				listingpageHighlight('scheduler_column_list');
			}	
		});
		/* Search highlight function end */

		/* Search function end */
		$('.disableAfterClick').click(function (e) {
			$(this).addClass('disabled');
		});

		$(document).on('ifToggled click', '.js_patient_status', function () {
			var patientid = $(this).attr('data-patientid');
			var id = $(this).attr('id');
			if ($(this).is(':checked')) {
				$("#session_model .med-green").html("Do you wish to activate this patient again?");
				var status = 'Active';
				var cancel = '0';
			} else {
				$("#session_model .med-green").html("Patient will be deactivated and cannot be used. Do you wish to continue?");
				var status = 'Inactive';
				var cancel = '1';
			}
			$("#session_model")
				.modal({show: 'false', keyboard: false})
				.one('click', '.js_session_confirm', function (e) {
					var conformation = $(this).attr('id');
					if (conformation == "true") {
						$.ajax({
							type: 'GET',
							url: api_site_url + '/patients/status/' + patientid + '/' + status,							
							success: function (result) {
								if (result == 1)
									$("#" + id).prop('checked', true);
								else
									$("#" + id).attr('checked', false);
								hideLoadingImage();
								js_alert_popup('Status changed successfully');
							}
						});
					} else {
						if (cancel == 1) {
							$("#" + id).prop('checked', true);
						} else {
							$("#" + id).attr('checked', false);
						}
					}
					$("#" + id).iCheck('update');
				});
		});
	});

	function schedulerSearch(allcolumns) {
		$("#scheduler_column_list").DataTable({
			"createdRow": function (row, data, index) {
				if (data[3] != undefined)
					data[3] = data[3].replace(/[\-,]/g, '');
			},
			"bDestroy": true,
			"paging": true,
			"searching"	: 	false,
			"info": true,
			"aoColumns": allcolumns,
			"columnDefs": [{orderable: false, targets: [ 10, 11]}],
			"autoWidth": false,
			"lengthChange"      : false,
			"searchHighlight": true,
			"serverSide": true,
			"order": [[0, "asc"], [1, "desc"]],
			"ajax": $.fn.dataTable.pipeline({
				url: listing_page_ajax_url,
				data:{'dataArr':dataArr},
                pages: 2, // number of pages to cache
				success: function () {
                    hideLoadingImage();
				}
			}),
			"columns": [
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
				{"datas": "id", sDefaultContent: ""},
			],
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(".ajax_table_list").html(aData + "</tr>");
				var get_orig_html = $(".ajax_table_list tr").html();
				var get_attr = $(".ajax_table_list tr").attr("data-url");
				var get_class = $(".ajax_table_list tr").attr("class");
				$(nRow).addClass(get_class);
				$(nRow).attr('data-url', get_attr);
				$(nRow).closest('tr').html(get_orig_html);
				$(".ajax_table_list").html("");

			},
			"fnDrawCallback": function (settings) {
				var length = settings._iDisplayStart;
				var sorting_length = settings.aLastSort.length;
				if (length > 0 || sorting_length > 0) {
					$('[name="js_document_model"]').iCheck({checkboxClass: 'icheckbox_flat-green'});
				}
				$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
					checkboxClass: 'icheckbox_flat-green',
					radioClass: 'iradio_flat-green'
				});
				var str = $('.dataTables_filter input').val();		
				if($.trim(str) != ''){	
					listingpageHighlight('scheduler_column_list');
				}
				hideLoadingImage(); // Hide loader once content get loaded.
			}
		});
	}
	
	/*$('.js_search_export_csv').click(function(){
		var baseurl = '{{url("/")}}';
		var url = baseurl+"/reports/streamcsv/export/schedulerAppointmentlist";
		form = $('form').serializeArray();
        var data_arr = [];
		$('select:visible').each(function(){
			//  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
			data_arr.push({
				name : $(this).attr('name'), 
				value:  ($(this).select2('val'))
			});
		});       
		$('input.auto-generate:visible').each(function(){
			// data_arr += $(this).attr('name')+'='+$(this).val()+'&';
			data_arr.push({
				name : $(this).attr('name'), 
				value:  ($(this).val())
			});
		});
		data_arr.push({
			name : "controller_name", 
			value:  "AppointmentListController"
		});
		data_arr.push({
			name : "function_name", 
			value:  "schedulerTableDataExport"
		});
		data_arr.push({
			name : "report_name", 
			value:  "Appointmentlist"
		});
				// console.log(data_arr);
		form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
		$.each(data_arr,function(index,value){	
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
					var avoid ="[]";
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
		//  console.log(form_data);
		$("#export_csv_div").html(form_data);
		$("#export_csv").submit();
		$("#export_csv").empty();
	});	*/
/**/
</script>
@endpush