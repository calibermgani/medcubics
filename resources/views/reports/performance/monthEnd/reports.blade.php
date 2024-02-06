@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/performance/list') }}">Performance Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Month End Performance Summary Report</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'reports/performance/export/'])
            <input type="hidden" name="report_controller_name" value="PerformanceController" />
            <input type="hidden" name="report_controller_func" value="monthendperformanceExport" />
            <input type="hidden" name="report_name" value="Month End Performance Summary Report" />
            <li><a href="{{ url('reports/performance/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding margin-t-10"><!--  Left side Content Starts -->                    
            <div class="box no-shadow yes-border border-radius-4 border-green"><!-- Primary Location Box Starts -->

                <div class="box-body form-horizontal js-address-class p-b-20 no-border" id="js-address-primary-address"><!-- Box Body Starts -->
                    {!! Form::open(['url'=>'reports/search/performance/monthend','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                    @include('layouts.search_fields', ['search_fields'=>$search_fields])
                    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8  ">                                            
                        <input class="btn generate-btn js_filter_search_submit pull-left m-r-m-3" value="Generate Report" type="button">
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div><!--  Left side Content Ends -->  
        
    </div>
</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_exit_part text-center">
    <input class="btn btn-medcubics-small hide" id="js_exit_part" value="Exit" type="button">
</div>
<?php 
	$exp = explode('-',\App\Http\Helpers\Helpers::getPracticeCreatedDate()); 
	$now = time(); // or your date as well
	$your_date = strtotime(trim($exp[0]));
	$datediff = $now - $your_date;

	$start_date_of_practice = round($datediff / (60 * 60 * 24));
?>
@stop
@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">



	var wto = '';
	var url = $('#js-bootstrap-searchvalidator').attr("action");
    var api_site_url = '{{url('/')}}';
    var listing_page_ajax_url = api_site_url + "/reports/search/performance/monthend";

    //--------------------------------- FORM SUBMIT ----------------------

    $(".js_filter_search_submit").on("click", function () {
        $(".result_data").addClass('hide');
        getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
    });
	
	$(document).ready(function (){
	$('input[name="performance_date"]').daterangepicker({
        //autoUpdateInput: false,
        startDate: moment().subtract({{$start_date_of_practice}},'days'),
        endDate: end_date,
        alwaysShowCalendars: true,
        showDropdowns: true,
        linkedCalendars:false,
        locale: {
          cancelLabel: 'Cancel'
        },
        ranges: {
           'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
           'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
           'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")],
		   'Till Date': [moment().subtract({{$start_date_of_practice}},'days'),moment()],
        }
    });

    $('input[name="performance_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
   });

</script>
@endpush