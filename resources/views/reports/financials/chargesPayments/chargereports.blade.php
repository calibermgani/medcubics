@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/financials/list') }}">Billing Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Charges & Payments Summary</span></small>
        </h1>
        <ol class="breadcrumb">            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            @include('layouts.practice_module_stream_export', ['url' => 'reports/search/charges_payments/export/'])
			<input type="hidden" name="report_controller_name" value="ReportController" />
			<input type="hidden" name="report_controller_func" value="chargepaymentsearch" />
			<input type="hidden" name="report_name" value="Charges Payments Summary" />        
            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow">        
            <div class="box-body yes-border border-radius-4 border-green" >
                <!-- SEARCH FIELDS FILE -->
                {!! Form::open(['url'=>'reports/search/charges_payments','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
                <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8 no-padding margin-l-10">
                    <!-- <input  name="insurance_charge" type="hidden" value="all">-->
                    <input  name="insurance_type" type="hidden" value="all">
                    <input class="btn generate-btn js_filter_search_submit pull-left m-r-m-3" value="Generate Report" type="submit">
                </div>
                {!! Form::close() !!}
            </div>
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
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
@stop

@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">
    var wto = '';
    var url = $('#js-bootstrap-searchvalidator').attr("action");
    var api_site_url = '{{url("/")}}';     
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/reports/search/charges_payments"; 

	//--------------------------------- FORM SUBMIT ----------------------

	$(".js_filter_search_submit").on("click",function(){
	    getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
	});
	
	$(document).ready(function(){
	    getMoreFieldData();
		$('[id^=insurance_id]').hide();
		$('#select_date_of_service').parent().parent().hide();
	});

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});
	/* Onchange code for more field End */ 

	$("#insurance_charge.js_select_basis_change").on("click",function(){
		//$("#insurance_id").hide();
		if($(this).val()=='insurance'){
		   $('[id^=insurance_id]').show();   
		} else{
		   $('[id^=insurance_id]').hide();  
		}
	});
	
	$("#choose_date.js_select_basis_change").on("click",function(){
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
	

</script>
@endpush
<!-- Server script end -->