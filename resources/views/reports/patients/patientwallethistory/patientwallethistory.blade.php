@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/patients/list') }}">Patient Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Wallet History - Detailed</span></small>
        </h1>
        <ol class="breadcrumb">
            
            <li class="">
                @include('layouts.practice_module_stream_export', ['url' => 'reports/patientwallethistory/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="patientWalletHistoryExport" />
                <input type="hidden" name="report_name" value="Wallet History - Detailed" />
            </li>	
            <li><a href="{{ url('reports/patients/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>			
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border border-green border-radius-4">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'patient_wallet_history_report', 'name'=>'medcubicsform', 'url'=>'reports/patientwallethistory/filter','data-url'=>'reports/patientwallethistory/filter']) !!}
					@include('layouts.search_fields', ['search_fields'=>$search_fields])         
                
					<div class="col-lg-11 col-md-12 col-sm-9 col-xs-12 no-padding margin-l-10">
						<input class="btn generate-btn js_filter_search_submit pull-left" tabindex="10" value="Generate Report" type="submit">
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
	<input id="js_exit_part_report" class="btn btn-medcubics-small" value="Exit" type="button">
</div>
@stop
@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
	var wto = '';
	var url = $('#patient_wallet_history_report').attr("action");
	$(document).ready(function(){
		getMoreFieldData();
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

	/* Onchange code for field Start */
	$(document).on('click','.js_filter_search_submit',function(){
		getData();
	});
	/* Onchange code for field End */ 

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});
	/* Onchange code for more field End */
</script>
@endpush