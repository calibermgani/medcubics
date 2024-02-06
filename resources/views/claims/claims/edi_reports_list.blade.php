<input type="hidden" class="js_selected_edireport_ids_arr" id="selected_edireport_ids_arr" />
<input type="hidden" class="js_curr_edireport_id" id="selected_curr_edireport_id" />
<input type="hidden" name="_token" value="{{ csrf_token() }}" />

<div class="tab-content js-edireport-dyanamic-tab">
	<div class="active tab-pane" id="edireport-tab-info_main0">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
			@include('layouts.search_fields', ['search_fields'=>$search_fields])
			@if(Session::get('message')!== null) 
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
			@endif
		</div>
		<div class="btn-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 margin-b-10">
			<input type="hidden" id="list_page_type"/>
			<a id="edireport_make_read" class="form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa fa-folder-open"></i> Read</a>
			<a id="edireport_make_unread" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa fa-folder"></i> Unread</a>
				<a id="edireport_move_archive" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa fa-inbox"></i> Archive</a>
				<a id="edireport_move_unarchive" class="hide form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa fa-inbox"></i> Unarchive</a>
			<span class="p-l-10 p-r-10 orange-b-c">{!! Form::checkbox("archive_list",'yes',(@$list_page =="non_archive_list")?null:true,["class" => "","id"=>"f".$list_page]) !!}&nbsp;<label for="f{{$list_page}}" class="font600 no-bottom ">Show Archive</label></span>
			<span class="btn-group pull-right">
				<a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a>
				<a id="js-edireport-view-tab" class="form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa fa-file-o"></i> View</a>
				<a id="js_generate_report" class="form-cursor font600 p-l-10 p-r-10"><i class="fa fa-pie-chart"></i> Generate Report</a>
			</span>
		</div>
		<div class="no-border no-shadow">
			<div class="box-body table-responsive">
            	<div class="ajax_table_list hide"></div>
            	<div class="data_table_list" id="js_ajax_part">
				<table id="claims_table_edi" class="table table-bordered table-striped">	
					<thead>
						<tr> 
							<th style="width:2%; text-align: center; vertical-align: middle;">
                            {!! Form::checkbox("selectall",null,null,["class" => "no-margin",'id'=>'js-select-all']) !!}
                            <label for='js-select-all' class="no-bottom">&nbsp;</label>
							</th>
							<th>Name</th>
							<th>Date Created</th>                                
							<th>Status</th>                                
							<th>Size(KB)</th>                                
							<th>Note</th>
							<th class="text-center"></th>                                
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/datatables_serverside.js') !!}
<script>
	var api_site_url = '{{url("/")}}';   
    var allcolumns = [];
    var type = "{{ last(Request::segments()) }}";
    var listing_page_ajax_url = api_site_url+'/claims/edireports';
	var column_length = $('#claims_table_edi thead th').length; 	
    // var data ={res_option:'list', list_page:'non_archive_list'};
	dataArr = [];	
	function accessAll() {
		var selected_column = ['Name','Date Created','Status','Size(KB)','Note'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		login_his_search(allcolumns); /* Trigger datatable */
	}	
    var dataArr = {};   
    var wto = '';
	if($('#list_page_type').val()=="non_archive_list"){
		$('#edireport_move_unarchive').addClass('hide');
		$('#edireport_move_archive').removeClass('hide');
	}
	else if($('#list_page_type').val()=="archive_list"){
		$('#edireport_move_unarchive').removeClass('hide');
		$('#edireport_move_archive').addClass('hide');
	}
    $(document).ready(function(){
    	$(document).on('change', "input[name='archive_list']", function () {
    		var list_page = 'non_archive_list';
		    if ($(this).prop('checked') == true) {
		    	var list_page = 'archive_list';
		    }
		    var data ={res_option:'list', list_page:list_page};
		    getData(data);
		});
        getData();
    });
    function getData(data =''){
        clearTimeout(wto);
        var data_arr = {};
        wto = setTimeout(function() {  
             $('select.auto-generate').each(function(){
                 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
             });                                                                                // Getting all data in select fields 
             $('input.auto-generate:visible').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
             });                                                                                // Getting all data in input fields
             dataArr = {data:data_arr,data_update:data};
             accessAll();                                                                       // Calling data table server side scripting
        }, 100);
    }
    $(document).on('click change','div.ranges>ul>li',function(){
    	if($(this).text() !== 'Custom Range'){
    		if($('input[name="archive_list"').prop('checked') == true)
    			var data ={res_option:'list', list_page:'archive_list'};
    		getData(data);
    	}
    });
    $(document).on('change','.auto-generate',function(){
    	if($('input[name="archive_list"').prop('checked') == true)
    		var data ={res_option:'list', list_page:'archive_list'};
    	if($(this).hasClass('js-date-range') == false)
    		getData(data);
	});
	/* Onchange code for field End */ 

    function login_his_search(allcolumns) { 
		search_url = listing_page_ajax_url;
		var dtable = $("#claims_table_edi").DataTable({			
			"createdRow": 	function ( row, data, index ) {
			if(data[1] != undefined)
    			data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"searching": false,
			"paging"	: 	true,
			"info"		: 	true,
			//"aoColumns"	: 	allcolumns,
			"columnDefs":   [ { orderable: false, targets: [0,6] } ],
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			//"processing": true,
			//"searchHighlight"	: true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[8,"desc"]],
			
            "ajax": $.fn.dataTable.pipeline({
                url: search_url, 
                data:{'dataArr':dataArr},
                pages: 1, // number of pages to cache
                success: function(){
                    // Hide loader once content get loaded.
                }   
            }),
	        "columns": [
	            {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" }
				
	        ],	
	        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {	
				$(".ajax_table_list").html(aData+"</tr>");
				var get_orig_html = $(".ajax_table_list tr").html();
				var get_attr = $(".ajax_table_list tr").attr("data-url");
				var get_class = $(".ajax_table_list tr").attr("class");
				$(nRow).addClass(get_class).attr('data-url', get_attr);
				$(nRow).closest('tr').html(get_orig_html);
				$(".ajax_table_list").html("");				
			},
			"fnDrawCallback": function(settings) {
				$('#list_page_type').val($('#list_page').val());
				if($('#list_page_type').val()=="non_archive_list"){
					$('input[name="archive_list"').prop('checked',false);
					$('input[name="selectall"').prop('checked',false);
					$('#edireport_move_unarchive').addClass('hide');
					$('#edireport_move_archive').removeClass('hide');
				}
				else if($('#list_page_type').val()=="archive_list"){
					$('input[name="archive_list"').prop('checked',true);
					$('input[name="selectall"').prop('checked',false);
					$('#edireport_move_unarchive').removeClass('hide');
					$('#edireport_move_archive').addClass('hide');
				}
                hideLoadingImage(); // Hide loader once content get loaded.
			}
		});
	}
</script>
@endpush