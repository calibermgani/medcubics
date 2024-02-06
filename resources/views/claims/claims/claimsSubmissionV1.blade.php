@extends('admin')
@section('toolbar')
<?php
	if($type == 'electronic') {
		$type_heading = 'Electronic Claims';
		$heading_icon = 'fa-tv';
	} elseif($type == 'paper') {
		$type_heading = 'Paper Claims';
		$heading_icon = 'fa-file-text';
	} elseif($type == 'error') {
		$type_heading = 'Claim Edits';
		$heading_icon = 'fa-close';
	} elseif($type == 'submitted') {
		$type_heading = 'Submitted Claims';
		$heading_icon = 'fa-check';
	} elseif($type == 'rejected') {
		$type_heading = 'Rejection Claims';
		$heading_icon = 'fa-ban';
	}
?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa font14 fa-cart-arrow-down"></i> Claims <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> {{$type_heading}} </span></small>
        </h1>
        <ol class="breadcrumb">                
            <li><a href="javascript:void(0);" class="js-print hide"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li>
			
            <li class="dropdown messages-menu js_claim_export">
                <!--<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_stream_export', ['url' => 'claims/search/'.$type.'/export/'])
            </li>
			
            <li><a href="#js-help-modal" data-url="{{url('help/$type')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>   
    </section>
</div>
@endsection

@section('practice')
{!! Form::hidden('js_search_option_url',url('claims/'.$type),['id'=>'js_search_option_url']) !!} 
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
            <!--div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                    
            </div-->
        </div><!-- /.box-header -->
        <input id="js_page_name" value="claims" type="hidden">
        <div class="box-body table-responsive">
            <!--div class="col-lg-12 col-md-4 col-sm-5 col-xs-12 p-l-0 p-r-0 margin-b-4 margin-t-10">                                        
				<span class="btn-group pull-right">       
					@if($type == 'electronic' )
						<a class="claimdetail font600 form-cursor js-claim-submit-electronic p-r-10 right-border "><i class="fa fa-tv font14"></i> Electronic</a>               
					@elseif($type == 'paper')
						<a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-r-10 p-l-10 right-border orange-b-c" data-type="print"><i class="fa fa-print font13"></i> Print</a>
						<a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-l-10" data-type="download"><i class="fa fa-download font13" ></i> Download</a>
					 @endif
				</span>
            </div-->    
			<div class="ajax_table_list hide"></div>
            <div class="data_table_list" id="js_ajax_part">
				<div id="js-table_listing">
					@if($type == 'electronic')
						@include('claims/claims/claims_electronic_listing')
					@elseif($type == 'paper')
						@include('claims/claims/claims_paper_listing')
					@elseif($type == 'error')
						@include('claims/claims/claims_error_listing')
					@elseif($type == 'submitted')
						@include('claims/claims/claims_submitted_listing')
					@elseif($type == 'rejected')
						@include('claims/claims/rejection_listing')
					@endif
				</div>                
			</div>		
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<div id="jsClaimHoldOption" class="modal fade in">        
</div>
<!-- dos popup content -->	
<div id="js-model-popup-payment" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hidden-print" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
                <h4 class="modal-title"> Claim No : <span class = "js-replace"></h4>
            </div>
            <div class="modal-body no-padding" >
            </div>
        </div>
    </div>
</div> 
<!-- dos popup content -->


<!-- Rejection denial popup content -->	
<div id="js-rejection-denial-popup" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hidden-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim No : <span class = "js-replace"> </span></h4>
            </div>
            <div class="modal-body edi-rejections-list" >
				<span>
					<ul class="js-rejection-content">
					
					</ul>
				</span>
            </div>
        </div>
    </div>
</div> 
<!-- Rejection denial popup content -->
<div id="export_csv_div"></div> 

@endsection

@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!}	
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
	var createDate = '{{ App\Http\Helpers\Helpers::getPracticeCreatedDate()  }}';
	var datePickerLable = "Clear";
	$(document).ready(function(){
		$('input[name="created_at"]').val(createDate);
	});
	var type = '{{$type}}';
	var dataArr = {};	
	var wto = '';
	if(type == 'pending' || type == 'hold' || type == 'rejected') {
	    $(document).on('click', '.js-table-click-billing', function (e) {
	        var target = $(e.target);
	        if (target.is("a.js-prevent-redirect") || target.is("i.js-prevent-redirect") || target.is("span.js-prevent-redirect")) {
	        }  else { 
	            var getUrl = $(this).attr('data-url');
	            window.open(getUrl,'_blank');
	        }
	    });
	}
	
	if(type == 'rejected'){
		var dyn_targets = [9]; 
	} else if(type == 'error') {
		var dyn_targets = [0,14];
	}
	else{
		var dyn_targets = [0,13];
	}
	
	// Server side script related change
	var api_site_url = '{{url('/')}}';	
	var listing_page_ajax_url = api_site_url+"/claims/search/"+type;
	var pagination_length = '<?php echo $pagination_count; ?>';
	
	/* Search highlight function start */	
			
	var column_length = $('#claims_table thead th').length;		
	function accessAll() {			
		var selected_column = ['DOS','Claim No','Patient Name','Billed To','Payer ID','Rendering','Billing','Facility','Billed','Status'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
																		// comment for server side scripting
		claimSearch(allcolumns); 										// Trigger datatable 
	}
			
	

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
			accessAll();
			$("#claims_table").DataTable().clearPipeline().draw();								// Calling data table server side scripting
			$('input#js-select-all').attr('checked',false);
		}, 100);
	}
	/* function for get data for fields End */

	
	//var dtable = $("#claims_table").dataTable().api();
	function claimSearch(allcolumns) {
		$("#claims_table").DataTable({
			
			"createdRow": 	function ( row, data, index ) {
								if(data[1] != undefined)
									data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"paging"	: 	true,
			"searching"	: 	false,
			//"ordering"	: 	true,
			"info"		: 	true,
			"pageLength": 50,
			//"order"		: 	[],
			"aoColumns"	: 	allcolumns,
			"columnDefs": 	[ { orderable: false, targets: dyn_targets } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			"searchHighlight"	: false,
			//"processing": true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[1,"desc"],[2,"desc"]],
			"ajax": $.fn.dataTable.pipeline({
				url: listing_page_ajax_url,	
				method: 'post',
				data:{'_token':'<?php echo csrf_token(); ?>','dataArr':dataArr},
				pages: 1, // number of pages to cache
				success: function(){
                                    
                                }
                            }),
			"deferLoading":pagination_length,
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
				var ClaimIds = $('input[name="encodeClaim"]').val();
				var type = $('select[name="js-select-option"]').val();
				if(ClaimIds != 'undefined' && type == 'all'){
					 $('.js-select-all-sub-checkbox').prop('checked',true);
				}else if(type == 'none'){
					$('.js-select-all-sub-checkbox').prop('checked',false);
				}else if(type == 'page'){
					 $('select[name="js-select-option"]').val('none');
				}
				hideLoadingImage(); // Hide loader once content get loaded.
				$(".js_search_export_csv").parent('.js_claim_export').removeClass("hide");
                $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");
			}			
		});
	}	
	
	$(document).on('click','.js-rejection-denial-popup',function(){
		$('.js-rejection-content').html($(this).attr('data-denial-codes'));
		$('.js-replace').text($(this).attr('data-claim-no'));
	});

	/*$('.js_search_export_csv').click(function(){
    current_page = window.location.pathname.split("/").pop();
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/claims/"+current_page;
		if(current_page == 'electronic'){
            var file_name = 'Electronic_Claims_list';
        }else if(current_page == 'paper'){
            var file_name = 'Paper_Claims_list';
        }else if(current_page == 'error'){
            var file_name = 'Error_Claims_list';
        }else if(current_page == 'submitted'){
            var file_name = 'Submitted_Claims_list';
        }else if(current_page == 'rejected'){
            var file_name = 'Rejected_Claims_list';
        }
		 form = $('form').serializeArray();
         var data_arr = [];
            $('select.auto-generate:visible').each(function(){
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
                    value:  "ClaimControllerV1"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "ClaimsDataSearchExport"
                });
                data_arr.push({
                    name : "report_name", 
                    value:  file_name
                });
				// console.log(data_arr);
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
		//  console.log(form_data);
		 $("#export_csv_div").html(form_data);
		 $("#export_csv").submit();
		 $("#export_csv").empty();
	});	*/
	
</script>
@endpush