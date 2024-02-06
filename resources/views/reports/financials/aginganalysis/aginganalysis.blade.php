@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/ar/list') }}" class="js_next_process">AR Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Aging Summary</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li class="dropdown messages-menu hide js_claim_export">
                @include('layouts.practice_module_stream_export', ['url' => '/reports/aginganalysis/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" /> 
                <input type="hidden" name="report_controller_func" value="getAgingReportSearchExport" />
                <input type="hidden" name="report_name" value="Aging Summary" />
            </li>
            <li><a href="{{ url('reports/ar/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/claim_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">

            <div class="box-body yes-border form-horizontal border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform', 'url'=>'reports/financials/search/aginganalysis']) !!}
                <input type="hidden" name="aging_summary" value="aging-summary" />
                @php
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
				@endphp 
                    @include('layouts.search_fields', ['search_fields'=>$search_fields])
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input class="btn generate-btn js_filter_search_submit pull-left" value="Generate Report" type="submit">
                        </div>
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
@stop
@push('view.scripts') 

{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script type="text/javascript">
    var api_site_url = '{{url("/")}}';      
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/reports/financials/search/aginganalysis"; 

//--------------------------------- FORM SUBMIT ----------------------
	$(document).ready(function(){
	    getMoreFieldData();
		$("#aging_insurance_id").hide();
		$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		if($('#aging_group_by :selected').val() == 'insurance'){   
			$("#aging_"+$('#aging_group_by :selected').val()+"_id").show();
			$("#aging_"+$('#aging_group_by :selected').val()+"_id .select2-container").removeClass('hide');
		}
		else{
	   
			$("#"+$('#aging_group_by :selected').val()+"_id").removeClass('hide').show();
			$("#"+$('#aging_group_by :selected').val()+"_id .select2-container").removeClass('hide');
		}
	});
	
	$(".js_filter_search_submit").on("click",function(){
	    getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
	});

	$("#aging_group_by.js_select_basis_change").on("click",function(){
		$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		$("#aging_insurance_id").hide();
		if($(this).val()=='insurance'){
		    $("#aging_"+$(this).val()+"_id").show();
		    $("#aging_"+$(this).val()+"_id .select2-container").removeClass('hide');
		}
		else{
		    $("#"+$(this).val()+"_id").show();
		    $("#"+$(this).val()+"_id .select2-container").removeClass('hide');
		}
		console.log($(this).val());
	});

</script>
@endpush
<!-- Server script end -->