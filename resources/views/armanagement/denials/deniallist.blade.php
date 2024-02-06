@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Denial List</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'api/arDenialListExport'])
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="med-tab nav-tabs-custom space10 no-bottom">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               <li class="js-tab-heading">
               	<a accesskey="u" href="{{ url('armanagement/summary') }}" id="" class="js_arrow"><i class="fa fa-user i-font-tabs"></i> Summary</a></li>
               <li class="js-tab-heading active"><a accesskey="l" id="" class="js_arrow"><i class="fa fa-bars i-font-tabs"></i> Denial List</a></li>
            </ul>
            <div class="tab-content patient-tab-bg">                
                <div class="tab-pane active" id="demo-info">				
				    <div>	
				        @include('armanagement/denials/claimslist')
				    </div>
				</div>
				<input type="hidden" class="js_selected_claim_ids_arr" id="selected_claim_ids_arr" />
				<input type="hidden" class="js_curr_claim_id" id="selected_curr_claim_id" />
				<input type="hidden" class="js_ar_max_claim_seleted" id="js_ar_max_claim_seleted" value="{{Config::get('siteconfigs.ar_max_claim_seleted')}}" />
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
            </div><!-- /.tab-content -->
        </div><!-- /.nav-tabs-custom -->
    </div>
</div>
@include ('patients/problemlist/commonproblemlist') 
@stop
@push('view.scripts') 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
	var api_site_url = '{{url("/")}}'; 
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/armanagement/deniallistAjax";
	
	<?php 
		if(Request::get('cpt_type') != ''){   
			if(Request::get('cpt_type') == 'custom_type' ){?>
				$("#custom_type_from").show();
				$('#custom_type_to').show();
			<?php }else{ ?>
				$("#custom_type_from").hide();
				$('#custom_type_to').hide();
			<?php }?>
			<?php if(Request::get('cpt_type') == 'cpt_code' ){?>
				$("#cpt_code_id").show();
				<?php }else{ ?>
				$("#cpt_code_id").hide();
				<?php }?> 
				<?php if(Request::get('cpt_type') == 'All' ){?>
				$("#custom_type_from").hide();
				$('#custom_type_to').hide();
				$("#cpt_code_id").hide();
				<?php }?>

	<?php }else{?>  
		$("#custom_type_from").hide();
		$('#custom_type_to').hide();
		$("#cpt_code_id").hide();
	<?php }?> 
		
	
    /* Search function start */
	var column_length = $('#search_table_claims thead th').length;         

	function accessAll() {	
		var selected_column = ['Claim No','DOS','Acc No', 'Patient Name','Insurance','Category','Denied CPT', 'Denied Date', 'Claim Age','Workbench Status', 'Charge Amt', 'Outstanding AR'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		denialSearch(allcolumns); /* Trigger datatable */
	}  
	
	$(document).ready(function(){        
        $("#cpt_type .js_select_basis_change").trigger("click");
		$(".selArOptions").trigger("change");
    });
	
	var dataArr = {};	
	var wto = '';
	
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
			 dataArr = {data:data_arr};
			 accessAll();																		// Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */
    function denialSearch(allcolumns) {
	
        var dtable = $("#search_table_claims").DataTable({          
            "createdRow":   function ( row, data, index ) {
                                if(data[1] != undefined)
                                    data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "paging"    :   true,
			"searching"	: 	false,
            "info"      :   true,
			"processing": true,
			"ordering"	: false,
            "aoColumns" :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [0,13] } ],
            "autoWidth" :   false,
            "lengthChange"      : false,
			"language": {
					"processing": "<div class='loader'>"+$("#selLoading").html()+"</div>"
				},
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true, 
            //"order": [[1,"desc"],[2,"desc"]],
            "ajax": $.fn.dataTable.pipeline({                
				url: listing_page_ajax_url,  
                data:{'dataArr':dataArr},        
                beforeSend: displayLoadingImage(),
                pages: 1, // number of pages to cache
                success: function () {              
                   // hideLoadingImage();
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
				if($(".fnWB").length) {
					$(".fnWBHead").show();
				} else {
					$(".fnWBHead").hide();
				}
					
                hideLoadingImage(); // Hide loader once content get loaded.				
            }
        });
    } 
	
	/* 0 Line item include / exclude show / hide start  */
	$(document).on("change", ".selArOptions", function(){
		var isClaim = 0;
		
		if($(this).val() == 'claim')
			isClaim = 1;
	
		if(!isClaim) {    
			$(".selExcZeroAr").prop("disabled", false).select2('val', 'Include');
		} else {        
			$(".selExcZeroAr").select2('val', '').val("").prop("disabled", true);
			// Clear already selected option.
		}
	})
	/* 0 Line item include / exclude show / hide end  */
	$("#choose_date.js_select_basis_change").on("click",function(){
		if($(this).val()=='submitted_date'){
			$('#submitted_date').parent().parent().show();
			$('#date_of_service').parent().parent().hide();
		} else if($(this).val()=='DOS'){
			$('#date_of_service').parent().parent().show();
            $('#submitted_date').parent().parent().hide();
		} else{
			$('#submitted_date').parent().parent().show();
			$('#date_of_service').parent().parent().show();
		}
	});
	$("#cpt_type .js_select_basis_change").on("click",function(){		
		if($(this).val() == 'custom_type'){
			$("#custom_type_from").show();
			$("#custom_type_to").show();
			$("#cpt_code_id").hide();
		}else if($(this).val() == 'All'){
			$("#custom_type_from").hide();
			$("#custom_type_to").hide();
			$("#cpt_code_id").hide();
		}else if($(this).val() == 'cpt_code'){
			$("#cpt_code_id").show();
			$("#custom_type_from").hide();
			$("#custom_type_to").hide();
		}
		//console.log($(this).val());
	});
	
</script>    

@endpush
<!-- Server script end -->