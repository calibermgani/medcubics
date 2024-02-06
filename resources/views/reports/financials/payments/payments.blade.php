@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <a href="{{ url('reports/collections/list') }}" >Collection Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Payment Analysis – Detailed Report</span></small>

        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
                @include('layouts.practice_module_stream_export', ['url' => 'reports/payments/export/'])
				<input type="hidden" name="report_controller_name" value="ReportController" />
				<input type="hidden" name="report_controller_func" value="paymentsearchexport" />
				<input type="hidden" name="report_name" value="Payment Analysis Detailed Report" />
            <li><a href="{{ url('reports/collections/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/payment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow">        
            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/search/payments','data-url'=>'reports/search/payments']) !!}
                <?php
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
				?>
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
				<div class="col-lg-12 col-md-12 col-sm-10 col-xs-12 p-r-0">
					<input class="btn generate-btn js_filter_search_submit pull-left" tabindex="10" value="Generate Report" type="submit">
				</div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_exit_part text-center hide">
    <input id="js_exit_part" class="btn btn-medcubics-small" value="Exit" type="button">
</div>
<div id="export_pdf_div"></div>
@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">
    var api_site_url = '{{url('/')}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/reports/search/payments"; 
	var dataArr = {};	
	var wto = '';
	var url = listing_page_ajax_url;
	//--------------------------------- FORM SUBMIT ----------------------

	$(document).ready(function(){
		getMoreFieldData(); 
		$('[id^=insurance_id]').hide();
		$('#select_date_of_service').parent().parent().hide();
	});

	$(".js_select_basis_change#insurance_charge").on("click",function(){
		if($(this).val()=='insurance'){
		   $('[id^=insurance_id]').show();
			$("[id^=insurance_id] .select2-container").removeClass('hide');
		} else {
		   $('[id^=insurance_id]').hide();
		}
	});

	$(".js_select_basis_change#choose_date").on("click",function(){
		if($(this).val()=='transaction_date'){
            $('#select_transaction_date').parent().parent().show();
            $('#select_date_of_service').parent().parent().hide();
        } else if($(this).val()=='DOS'){
            $('#select_date_of_service').parent().parent().show();
            $('#select_transaction_date').parent().parent().hide();
        } else{
            $('#select_transaction_date').parent().parent().show();
            $('#select_date_of_service').parent().parent().show();
        }
	});
	
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = '';
		wto = setTimeout(function() {  
			 $('select.auto-generate:visible').each(function(){
				 data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';           
			 });                                                    
			 $('input.auto-generate:visible').each(function(){
				data_arr += $(this).attr('name')+'='+$(this).val()+'&';
			 });
			
			final_data = data_arr+"_token="+$('input[name=_token]').val(); 
			getAjaxResponse(url, final_data);
		}, 100);
	}
	/* function for get data for fields End */
        
    // $('.js_search_export_pdf').click(function(){
    //     var baseurl = '{{url('/')}}';
    //     var url = baseurl+"/reports/export_pdf/payment-analysis-detailed-report"
    //     var data_arr = [];
    //     form = $('form').serializeArray();
    //     form_data = "<form id='export_pdf' target='_blank' method='POST' action='"+url+"'>";
        
    //     $('select.auto-generate:visible').each(function(){
    //         data_arr.push({
    //             name : $(this).attr('name'),
    //             value: ($(this).select2('val'))
    //         });
    //     });
    //     $('input.auto-generate:visible').each(function(){
    //         data_arr.push({
    //             name : $(this).attr('name'),
    //             value: ($(this).val())
    //         });
    //     });
    //         data_arr.push({
    //             name : "controller_name", 
    //             value:  "ReportController"
    //         });
    //         data_arr.push({
    //             name : "function_name", 
    //             value:  "paymentsearchexport"
    //         });
    //         data_arr.push({
    //             name : "report_name", 
    //             value:  "Payment-Analysis-Detailed-Report"
    //         });
    //     $.each(data_arr,function(index,value){
    //         if($.isArray(value.value)) {
    //             if(value.value.length > 0) {
    //                 var avoid ="[]"
    //                 form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
    //             }
    //         } else {
    //             if(value.value.length > 0) {
    //                 form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
    //             }
    //         }
    //     });
    //     form_data  += "<input type='hidden' name='exports' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
    //     form_data += "</form>";
    //     $("#export_pdf_div").html(form_data);
    //     $("#export_pdf").submit();
    //     $("#export_pdf").empty();
    // });
</script>
@endpush
<!-- Server script end -->