@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>  </span></small>
        </h1>
        <ol class="breadcrumb">
            <?php 
                $type = '';
                $currnet_page = Route::getFacadeRoot()->current()->uri();
                if(count((array)$currnet_page) > 0) {
                    if($currnet_page == 'armanagement/problemlist') {
                        $type = 'problemlist';
                    } elseif($currnet_page == 'armanagement/myproblemlist') {
                        $type = 'myproblemlist';
                    }
                }
            ?>
            
            @include('layouts.practice_module_stream_export', ['url' => 'armanagement/problemlistAjax/'.$type.'/'])
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')	
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->	   
    @include('armanagement/problemlist/tabs')

    <!-- Tab Ends -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding space20">
        <div class="box no-border no-shadow">
			<!-- Workbench Start -->
			@include('layouts.search_fields')
            <div id ="js_table_search_listing" class="box-body table-responsive js_problem_list_loop">
                <?php 
					$activetab = ""; 
                    $listFor = 'common';  // show for charges in patients / common.
                ?>				
                <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 margin-t-10">             
                    <a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a> 
                    <a id="claim_assign_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10"><i class="fa fa-edit"></i> Re-assign</a>
                </div>
                  
                @include ('patients/problemlist/problemlistloop') 
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
@include ('patients/problemlist/commonproblemlist') 
<!-- New Problem details starts here -->
<div id="create_problem_list" class="modal fade in"></div><!-- /.modal-dialog -->
<!-- Modal New Problem Details ends here -->
<!-- Show Problem list start-->
<div id="show_problem_list" class="modal fade in"></div><!-- /.modal-dialog -->
<!-- Show Problem list end--> 	 
<!--End-->
@endsection

<!-- Server script start -->
@push('view.scripts') 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
	var api_site_url = '{{url('')}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/armanagement/problemlistAjax";
	var get_practice_timzone = '{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/Y H:i:s") }}';
    /* Search function start */
	var column_length = $('#search_table_WB thead th').length;         

	function accessAll() {                      
		var selected_column = ['DOS','Claim No','Patient Name','Provider','Facility','Billed To','Paid','AR Due','Status','Followup Dt','Assigned To','Priority'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		claimSearch(allcolumns); /* Trigger datatable */
	}  

	var dataArr = {};	
	var wto = '';
		
	$(document).ready(function(){
		$('small>span').text($("ul.nav-tabs>li.active").text());
	});
		
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = {};
		wto = setTimeout(function() {  
			 $('select.auto-generate').each(function(){
				 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
			 });																				// Getting all data in select fields 
			 $('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			 });																				// Getting all data in input fields
			 data_arr['type'] = "<?php  echo (Request::segment(2) == 'problemlist') ? '' : 'assigned'; ?>";
			 dataArr = {data:data_arr};
			 accessAll();																		// Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */


		
    function claimSearch(allcolumns) {
		$("#search_table_WB").DataTable({          
            "createdRow":   function ( row, data, index ) {
                                if(data[1] != undefined)
                                    data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "paging"    :   true,
			"searching"	: 	false,
            "info"      :   true,
			//"processing": true,
            //"aoColumns"   :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [0,12,13] } ],
            "autoWidth" :   false,
            "lengthChange"      : false,
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true, 
            "order": [[1,"desc"],[2,"desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url, 
				data:{'dataArr':dataArr},
                beforeSend:displayLoadingImage(),   
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
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
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
                $('#js-select-all').prop('checked',false); // uncheck select all option while paginating
                hideLoadingImage(); // Hide loader once content get loaded.
            }
        });
    }

</script>    
@endpush
<!-- Server script end -->