@extends('admin')
@section('toolbar')
<?php
	if ($type == 'submitted') {
            $type_heading = 'Submitted';
        } elseif ($type == 'hold') {
            $type_heading = 'Claims on Hold';
        } elseif ($type == 'rejected') {
            $type_heading = 'EDI Rejections';
        } elseif ($type == 'pending') {
            $type_heading = 'Pending Claims';
        } else {
            $type_heading = 'Ready to Submit';
        }
?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading">
                <i class="fa font14 {{$heading_icon}}"></i> {{$type_heading}}                    
            </small>                
        </h1>
        <ol class="breadcrumb">                
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu js_claim_export hide">
                
                @include('layouts.practice_module_export', ['url' => 'claims/search/tosubmit/export/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/$type')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>   
    </section>
</div>
@stop
@section('practice')
<!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow collapsed-box">
        <div class="box-header with-border">
            <i class="fa fa-filter"></i> <h3 class="box-title">Filter</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>                    
            </div>
        </div>
                    
        <div class="box-body table-responsive bg-aqua">
            <div class="js_claim_search col-lg-12 col-md-12 col-sm-12 col-xs-12">   @include('claims/claims/search_filter_option')
                {!! Form::hidden('js_search_option_url',url('claims/'.$type),['id'=>'js_search_option_url']) !!}                    
            </div>             
        </div>
    </div>-->
	 {!! Form::hidden('js_search_option_url',url('claims/'.$type),['id'=>'js_search_option_url']) !!} 
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                    
            </div>
        </div><!-- /.box-header -->
        <input id="js_page_name" value="claims" type="hidden">
        <div class="box-body table-responsive">
            <div class="col-lg-12 col-md-4 col-sm-5 col-xs-12 p-l-0 p-r-0 margin-b-4 margin-t-10">                        
                @if($type == 'tosubmit')
                    <a class="claimdetail font600 form-cursor js-hold-claims p-r-10 right-border orange-b-c hide"><i class="fa fa-lock font14"></i> Hold</a>
                    <a class="claimdetail font600 form-cursor js-pending-claims p-r-10 p-l-5"><i class="fa fa-exclamation-triangle font14"></i> Pending</a>
                @endif                    

                @if($type == 'tosubmit')
                    <span class="btn-group pull-right">       
                        <a class="claimdetail font600 form-cursor js-claim-submit-electronic p-r-10 right-border orange-b-c"><i class="fa fa-tv font14"></i> Electronic</a>               
                        <a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-r-10 p-l-10 right-border orange-b-c" data-type="print"><i class="fa fa-print font13"></i> Print</a>
                        <a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-l-10" data-type="download"><i class="fa fa-download font13" ></i> Download</a>
                    </span>
                @endif
            </div>    
			<div class="ajax_table_list hide"></div>
            <div class="data_table_list" id="js_ajax_part">
				<div id="js-table_listing">
					@if($type == 'rejected')
						@include('claims/claims/rejection_listing')
					@else
						@include('claims/claims/claims_listing')
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
                <button type="button" class="close hidden-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim No : <span class = "js-replace"></h4>
            </div>
            <div class="modal-body no-padding" >
            </div>
        </div>
    </div>
</div> 
<!-- dos popup content -->
@stop

@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!}	
<script>
	var type = '{{$type}}';
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
	
	// Server side script related change
	var api_site_url = '{{url("/")}}';   	
	var listing_page_ajax_url = api_site_url+"/claims/claimsList/"+type ;
	
	function searchHighLight(){
		$('#claims_table td').unhighlight();
		if ($('.dataTables_filter input').val() != "") {			
			var selector_name = $("#claims_table tr td");
			var str = $('.dataTables_filter input').val();	
			selector_name.highlight($.trim(str));
			if(str.indexOf("/") > 0){
				var ds = str.split("/");
				var mydate = new Date(str);
				var str1 = ds[0]+"/"+ds[1]+"/"+mydate.getFullYear();
				selector_name.highlight(str1);
			}			
		}
	}
  

	$(document).ready(function(){
		/* Search function start */
		var column_length = $('#claims_table thead th').length; 		
	
		function accessAll() {			
			var selected_column = ['DOS','Claim No','Patient Name','Billed To','Payer ID','Rendering','Billing','Facility','Billed','Status'];
			var allcolumns = [];
			for (var i = 0; i < column_length; i++) {
				allcolumns.push({"name": selected_column[i], "bSearchable": true});
			}
			claimSearch(allcolumns); /* Trigger datatable */
		}		
		accessAll(); /* Trigger datatable */
	});
	/* Search highlight function start */	
	$(document).on('keyup','.dataTables_filter input',function() {						
		searchHighLight();		
	});
	/* Search highlight function end */

	// Call datatables, and return the API to the variable for use in our code
	// Binds datatables to all elements with a class of datatable
	var dtable = $("#claims_table").dataTable().api();

	// Grab the datatables input box and alter how it is bound to events
	$(".dataTables_filter input")
	    .unbind() // Unbind previous default bindings
	    .bind("input", function(e) { // Bind our desired behavior
	        // If the length is 3 or more characters, or the user pressed ENTER, search
	        if(this.value.length >= 3 || e.keyCode == 13) {
	            // Call the API search function
	            dtable.search(this.value).draw();
	        }
	        // Ensure we clear the search if they backspace far enough
	        if(this.value == "") {
	            dtable.search("").draw();
	        }
	        return;
	    });
	
	function claimSearch(allcolumns) {
		$("#claims_table").DataTable({
			
			"createdRow": 	function ( row, data, index ) {
								if(data[1] != undefined)
									data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"paging"	: 	true,
			//"searching"	: 	true,
			//"ordering"	: 	true,
			"info"		: 	true,
			//"order"		: 	[],
			"aoColumns"	: 	allcolumns,
			"columnDefs": 	[ { orderable: false, targets: [0,6,7,9,10,11] } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: true,
			"searchHighlight"	: true,
			//"processing": true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[0,"asc"],[1,"desc"]],
			"ajax": $.fn.dataTable.pipeline({
				url: listing_page_ajax_url,
				pages: 2, // number of pages to cache
				success: function(){
                                    
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
				//var length = settings._iDisplayStart;
				//var sorting_length = settings.aLastSort.length;
				/*							
				$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
					checkboxClass: 'icheckbox_flat-green',
					radioClass: 'iradio_flat-green'
				});
				*/
				hideLoadingImage(); // Hide loader once content get loaded.
				searchHighLight();
			}
		});
	}
</script>
@endpush