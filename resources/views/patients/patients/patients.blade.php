@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="users"></i> Patients </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0);" class="js-print hide" accesskey="P"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li class="dropdown messages-menu">
                <!--<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_stream_export', ['url' => 'api/patientsreports/'])
            </li>            
            <li><a href="#js-help-modal" data-url="{{url('help/patients')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 margin-t-20">
	<div class="med-tab">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="js-tab-heading active"><a accesskey="l" id="" class="js_arrow"><i class="fa fa-info-circle i-font-tabs"></i> <span class="text-underline">L</span>isting</a></li>
                <li class="js-tab-heading"><a accesskey="u" href="{{ url('uploadedpatients') }}" id="" class="js_arrow"><i class="fa fa-cloud-upload i-font-tabs"></i> Patient <span class="text-underline">U</span>pload</a></li>
            </ul>

            <div class="tab-content patient-tab-bg">                
				<div class="tab-pane active" id="demo-info">
					<!-- Form 1 -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-20">
						<div class="box box-info no-shadow">
							<div class="box-header">
								<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
								<div class="box-tools pull-right margin-t-5">
									@if($checkpermission->check_url_permission('patients/create') == 1)
									<a class="font13 font600" accesskey="a" href="{{ url('patients/create') }}" target="_blank"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Patient</a>
									@endif
								</div>
							</div><!-- /.box-header -->
							<div class="box-body table-responsive mobile-scroll p-t-0">

								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">  		
									@include('layouts.search_fields', ['search_fields'=>$search_fields])           
									@if(Session::get('message')!== null) 
									<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
									@endif
								</div>
								
								<div class="ajax_table_list hide"></div>
								<div class="data_table_list" id="js_ajax_part">
									<table id="patients_column_list" class="table table-bordered table-striped mobile-width">   
										<thead>
											<tr>                        
												<th>Acc No</th>
												<th>Patient Name</th>
												<th>Cell Phone</th>
												<th>Gender</th>
												<th>DOB</th>                            
												<th>SSN</th>
												<th>Payer</th>
												<th>Pat Due($)</th>
												<th>Ins Due($)</th>
												<th>AR Due($)</th>
												<th>Created On</th>
												<th class="hidden-print">Action</th>
												<th class="hidden-print"> % </th>
											</tr>
										</thead>
										<tbody>    
											@include ('patients/patients/patients_list', ['patients' => $patients, 'insurance_list' => $insurance_list])
										</tbody>
									</table>
								</div>
								<style>
									.disabled {
										opacity: 0.5
											pointer-events: none
											cursor: default
									}           
								</style>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>
				</div><!-- /.tab-pane --> 
                
                
            </div><!-- /.tab-content -->
        </div><!-- /.nav-tabs-custom -->
    </div>
</div>

@include ('layouts/popupmodal')
<div id="eligibility_content_popup" class="modal fade in">
    @include ('layouts/eligibility_modal_popup')
</div>
<!--End-->
<!-- Insurance payment posting starts here -->
<div id="choose_claims" class="modal fade in">
    <div class="modal-md-700">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div>
<div id="export_csv_div"></div> 
<div id="export_pdf_div"></div> 
@stop
@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
    var api_site_url = '{{url('/')}}';
    var total_rec = '{{ $total_rec }}';
    var listing_page_ajax_url = api_site_url + "/patients/patientsList";
    var dataArr = {};   
    var wto = '';
    $(document).on('click, ifToggled', '.js_all_patients, .js_app_patients', function () {
        if (($('.js_all_patients').is(":checked")) && ($('.js_app_patients').is(":checked"))) {
            listing_page_ajax_url = '';
            listing_page_ajax_url = api_site_url + "/patients/patientsList/all/app";
            patientSearch();
        } else if ($('.js_all_patients').is(":checked")) {
            listing_page_ajax_url = '';
            listing_page_ajax_url = api_site_url + "/patients/patientsList/all";
            patientSearch();
        } else if ($('.js_app_patients').is(":checked")) {
            listing_page_ajax_url = '';
            listing_page_ajax_url = api_site_url + "/patients/patientsList/from/app";
            patientSearch();
        } else {
            listing_page_ajax_url = '';
            listing_page_ajax_url = api_site_url + "/patients/patientsList";
            patientSearch();
        }
    });
  
    /* Search function start */
    var column_length = $('#patients_column_list thead th').length;   

    /* Dynamic append */
    function accessAll() {
        var selected_column = ['Acc No', 'Patient Name','Cell Phone' ,'Gender', 'DOB', 'SSN', 'Payer', 'Patient Due', 'Insurance Due', 'AR Due', 'Created On'];
        var allcolumns = [];
        for (var i = 0; i < column_length; i++) {
            allcolumns.push({"name": selected_column[i], "bSearchable": true});
        }
        patientSearch(allcolumns); /* Trigger datatable */
    }
	
	
		
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

	
    $(document).ready(function () {
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
			// Removed X close button in popup
			$('.close_popup').not('.js_note_confirm').addClass('hide');
            $("#session_model")
                .modal({show: 'false', keyboard: false})
                .one('click', '.js_session_confirm', function (e) {
                    var conformation = $(this).attr('id');
                    if (conformation == "true") {
                        $.ajax({
                            type: 'GET',
                            url: api_site_url + '/patients/status/' + patientid + '/' + status,
							beforeSend: displayLoadingImage(),
                            success: function (result) {                                  
                                if (result == 1){
                                     $("#"+id).parent().addClass('checked');
                                     $("#" + id).prop('checked', true);                                                     
                                }
                                else{
                                    $("#"+id).parent().removeClass('checked');
                                    $("#" + id).attr('checked', false);                                    
                                }
								hideLoadingImage();
                                js_alert_popup('Status changed successfully');
                                return false;
                            }
                        });
                    } else {
						
                        if (cancel == 1) {
                            $("#" + id).prop('checked', true);
                        } else {
                            $("#" + id).attr('checked', false);
                        }
                    }
                });
				
			// Revision 1 : MEDV2-549 : issues has been fixed
			
            // Handle close icon click event same as confirmation no button    
           /*  $(document).on('hide.bs.modal', '#session_model', function (e) {   
                //e.preventDefault(); 
                if (cancel == 1) {
                    $("#" + id).prop('checked', true);
                } else {
                    $("#" + id).attr('checked', false);
                }
            }); */
        });    
    });    

    function patientSearch(allcolumns) {
        var dtable= $("#patients_column_list").DataTable({
            "createdRow": function (row, data, index) {
                if (data[3] != undefined)
                    data[3] = data[3].replace(/[\-,]/g, '');
            },
            "bDestroy": true,
            "paging": true,
			//"processing": true,
            "searching"   :   false,
            "info": true,
            "aoColumns": allcolumns,
            "columnDefs": [{orderable: false, targets: [11, 12]}],
            "autoWidth": false,
            "lengthChange": false,			
            "searchHighlight": true,
            "serverSide": true,
            "order": [[0, "desc"], [1, "desc"]],
            //"deferLoading": total_rec, // Commented since all / app patient filter not working when using deferloading.
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url,  
                data:{'dataArr':dataArr},
				beforeSend: displayLoadingImage(),
                pages: 2, // number of pages to cache
                success: function () {
                    
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
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
                $(".ajax_table_list").html(aData + "</tr>");
				var elem = $(".ajax_table_list tr");
                var get_orig_html = elem.html();
                var get_attr = elem.attr("data-url");
                var get_class = elem.attr("class");
                $(nRow).addClass(get_class);
                $(nRow).attr('data-url', get_attr);
                $(nRow).closest('tr').html(get_orig_html);
                $(".ajax_table_list").html("");
                // Enable pdf icon - @toto have to remove to function.js                
                $(".js_search_export_csv").parent('.js_claim_export').removeClass("hide");
                $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");
            },
            "fnDrawCallback": function (settings) {
                //var length = settings._iDisplayStart;
                //var sorting_length = settings.aLastSort.length;
                $('#js-select-all').prop('checked',false); // uncheck select all option while paginating
                $('input:checkbox').iCheck('update');
                hideLoadingImage(); // Hide loader once content get loaded.
            }
        });
    }
	
	$("form").find('input:visible').each(function () {
         $(this).attr("autocomplete", "nope");
    });

   
   

</script>
@endpush