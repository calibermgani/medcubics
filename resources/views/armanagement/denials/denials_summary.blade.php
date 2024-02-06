@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Denial Summary</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'api/arDenialListExport'])
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="col-lg-12 col-md-12 margin-t-20">
	<div class="med-tab">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="js-tab-heading active"><a accesskey="u"  id="" class="js_arrow"><i class="fa fa-user i-font-tabs"></i> Summary</a></li>
                 <li class="js-tab-heading"><a accesskey="l" id="" href="{{ url('armanagement/denials') }}" class="js_arrow"><i class="fa fa-bars i-font-tabs"></i> Denial List</a></li>
            </ul>

            <div class="tab-content patient-tab-bg">
				<div class="tab-pane active" id="demo-info">
					<!-- Form 1 -->
					<!-- Tab Ends -->	
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding  margin-t-20">
						<div class="col-lg-11 col-md-11 col-sm-12 col-xs-12 mobile-scroll table-responsive margin-t-10">
								<table class="popup-table-transaction table mobile-width bg-white">                    
									<thead>
										<tr style="">
											<th style="background: #96dcd8; width: 300px;" class="med-green line-height-30 font600">Denial Sub Status</th>
											<th style="background: #96dcd8; width: 100px;" class="med-green line-height-30 font600"># of Claims</th>
											<th style="background: #96dcd8; width: 100px;" class="med-green text-right line-height-30 font600">Value ($)</th>
											<th style="background: #96dcd8; width: 150px;" class="med-green line-height-30 font600">Avg. Claim Age</th>
											<th style="background: #96dcd8" class="med-green line-height-30 font600"></th>
										</tr>
									</thead>
									<tbody>
										<?php $max_claims = 0; ?>
										@if(COUNT($summary) > 0)
											@foreach($summary as $key =>  $result)
												<?php 
													$avg_claim_age = ($result['claims'] > 0) ? $result['claim_age_days'] / $result['claims'] : 0;
													$avg_claim_age = round($avg_claim_age);
													$balance = App\Http\Helpers\Helpers::priceFormat(@$result['balance_amt'],'',1);
													
												?>
												@if($key != 'total')
												@if($max_claims == 0)<?php $max_claims = @$result['claims'] ?>@endif
												<?php 
													$progress_bar_width = round((@$result['claims'] / $max_claims) * 100);
													if($progress_bar_width <= 2){
														$progress_bar_width = 2;
													}
												?>
												<tr>
													<td><span class="normal-font font13 font600">{{ @$result['description'] }}</span></td>
													<td><span class="normal-font font13">{{ @$result['claims'] }}</span></td>
													<td><span class="normal-font font13 pull-right">{{ $balance }}</span></td>
													<td><span class="normal-font font13 margin-l-10">{{ @$avg_claim_age }}</span></td>
													<td>
														@if(@$result['description']  != '-Nil-')
															<div style="width: 100%; height: 20px; background-color: #fefefe;">
						    									<div style="background-color: rgba({{ $progress_bar_width*2.4 }},156,18,.7) !important; width:{{ $progress_bar_width }}%; height: 20px; border-radius: 5px;">
															    </div>
															</div>
														@endif
													</td>
												</tr>
												@endif
											@endforeach
											<?php 
												$tavg_claim_age = ($summary['total']['claims'] > 0) ? $summary['total']['claim_age_days'] / $summary['total']['claims'] : 0;
												$tavg_claim_age = round($tavg_claim_age);
											?>
											<tr>
												<td><span class="normal-font font13 med-orange font600">Totals</span></td>
												<td><span class="normal-font font13 med-orange font600">{{ @$summary['total']['claims'] }}</span></td>
												<td><span class="normal-font font13 pull-right med-orange font600">{{ App\Http\Helpers\Helpers::priceFormat(@$summary['total']['balance_amt']) }}</span></td>
												<td><span class="normal-font font13 med-orange font600 margin-l-10">{{ $tavg_claim_age }}</span></td>
											</tr>
										@endif

									</tbody>
								</table>
							</div>
					</div>
				</div>
				
			    <div>	
		        	
			</div>

			<input type="hidden" class="js_selected_claim_ids_arr" id="selected_claim_ids_arr" />
			<input type="hidden" class="js_curr_claim_id" id="selected_curr_claim_id" />
			<input type="hidden" class="js_ar_max_claim_seleted" id="js_ar_max_claim_seleted" value="{{Config::get('siteconfigs.ar_max_claim_seleted')}}" />
			<input type="hidden" name="_token" value="{{ csrf_token() }}" />

			<!--End-->
			<div id="export_csv_div"></div>  
                
                
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
			 });// Getting all data in select fields 
			 $('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			 });// Getting all data in input fields
			 dataArr = {data:data_arr};
			 accessAll();// Calling data table server side scripting
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
    $('.js_claim_export').addClass("hide");
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