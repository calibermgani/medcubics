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
                @include('layouts.practice_module_export', ['url' => 'api/patientsreports/'])
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
                <li class="js-tab-heading"><a accesskey="l" id="" href="{{ url('patients') }}" class="js_arrow"><i class="fa fa-info-circle i-font-tabs"></i> <span class="text-underline">L</span>isting</a></li>
                <li class="js-tab-heading active"><a accesskey="u"  id="" class="js_arrow"><i class="fa fa-cloud-upload i-font-tabs"></i> Patient <span class="text-underline">U</span>pload</a></li>
            </ul>

            <div class="tab-content patient-tab-bg">                 
                
                    <div class="tab-pane active" id="demo-info">
                        <!-- Form 1 -->
                                  

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-20">
							<div class="box box-info no-shadow">
								<div class="box-header">
									<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
								</div><!-- /.box-header -->
								<div class="box-body table-responsive mobile-scroll p-t-0">

									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">  		
										@include('layouts.search_fields', ['search_fields'=>$search_fields])           
										@if(Session::get('message')!== null) 
										<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
										@endif
									</div>
									<div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 margin-t-10">             
                    					<a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a> 
                   						@if($checkpermission->check_url_permission('patients/create') == 1)
                                            <a><span class="font13 font600 js-uploadpatient cur-pointer p-l-10 p-r-10 right-border orange-b-c" accesskey="u"  data-toggle="modal" data-target="#uploadedPatient"> <i class="fa fa-upload"></i> Upload Patient Template</span></a>
                                            <a href="{{ url('/downloadTemplate') }}" target="_blank" title="Uploaded Document">
												<span class="font13 font600 js-uploadpatient cur-pointer p-l-10 p-r-10" accesskey="d" > <i class="fa fa-download"></i> Download Template</span>
											</a>
										@endif
                					</div>
									<div class="ajax_table_list hide"></div>
									<div class="data_table_list" id="js_ajax_part">
										<table id="uploadedpatient" class="table table-bordered table-striped mobile-width">   
											<thead>
												<tr>                        
													<th>File Name</th>
													<th>Notes</th>
													<th>Total Patients</th>
													<th>Uploaded Patients</th>
													<th>Total Charges</th>
													<th>Failed Charges</th>
													<th>Status</th>
													<th style="width: 40px;">User</th>
													<th style="width: 95px;">Created Date</th>
													<th style="width: 85px;"></th>
												</tr>
											</thead>
											<tbody>    
												@include ('patients/patients/uploadedpatient_list')
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
@stop
@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
    var api_site_url = '{{url('/')}}';
    var total_rec = '{{ $total_rec }}';
    var listing_page_ajax_url = api_site_url + "/getUploadedPatientAjax";
    var dataArr = {};   
    var wto = '';
      
    /* Search function start */
    var column_length = $('#patients_column_list thead th').length;   

    /* Dynamic append */
    function accessAll() {
        var selected_column = ['File Name','Notes','Total Patients','Uploaded Patients','Total Charges','Failed Charges','Status','User','Created Date'];
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
			});                                                          // Getting all data in select fields 
			$('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			});                                                          // Getting all data in input fields
			dataArr = {data:data_arr};
			accessAll();
		}, 100);
	}
	/* function for get data for fields End */


    function patientSearch(allcolumns) {
        var dtable= $("#uploadedpatient").DataTable({
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
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url,  
                data:{'dataArr':dataArr},
                beforeSend:displayLoadingImage(), 
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
            },
            "fnDrawCallback": function (settings) {
                $('#js-select-all').prop('checked',false); 
                $('input:checkbox').iCheck('update');
				hideLoadingImage(); // Hide loader once content get loaded.
            }
        });
    }
	
	$(document).on('click','.js-uploadpatient',function(){
		$('#js-uploadpatient-validator').bootstrapValidator({
			feedbackIcons: {
				valid: "",
				invalid: "",
				validating: "glyphicon glyphicon-refresh"
			},
			excluded: [':disabled'],
			fields: {
				filefield:{
					message:'',
					validators:{
						notEmpty:{
							message: 'choose Import file'
						},
						callback: {
							message: 'Invaild file format, please choose csv file',
							callback: function (value, validator) {
								if ($('[name="filefield"]').val() != "") {
									var extension_Arr 	= ['csv','CSV'];
									var file_name 		= $('[name="filefield"]')[0].files[0].name;
									var temp			= file_name.split(".");
									filename_length = ((temp.length) - 1);
									if(extension_Arr.indexOf(temp[filename_length]) == -1){
										return false;
									}else{
										return true;
									}
								}
								return true;
							}
						}
					}
				}
			}
		});
	});
	
	$(document).on('click','.selProcessUpload',function(){
		var ident= $(this).attr("id");
		var callUrl = api_site_url+'/processupload/'+ident;
		$(this).html("In Progress");
		$.get(callUrl,function(data){             
			//console.log(data);
			setTimeout(function () {
				js_sidebar_notification('success',"Uploaded process is completed");
			}, 100);			
			location.reload();
		});
	});
	
	// Check if pending records found the call check status
	$(document).ready(function(){
		setInterval(function() {  
		var recCnt = $("tr.uploadList").length;		//console.log("@@"+recCnt);
		if(recCnt > 0)
			getStatus();
		}, 5000);
	});	
	
	var ajaxUrl = api_site_url+'/getUploadStatus';
	function getStatus(){
		$.ajax({
			type: "GET",        
			url: ajaxUrl,             
			success: function (result) {					
				var data = JSON.parse(result);
				$.each($.parseJSON(result), function(idx, obj) {
					if ($('.js_comppat'+obj.id).length){
						$('.js_totpat'+obj.id).html(obj.total_patients); 
						$('.js_comppat'+obj.id).html(obj.completed_patients);
						$('.js_status'+obj.id).html(obj.status);
						if(obj.status == 'Completed') {
							$('.js_process'+obj.id).addClass("hide");
						}
					}						
				});				
			}
		});    
	}
</script>
@endpush