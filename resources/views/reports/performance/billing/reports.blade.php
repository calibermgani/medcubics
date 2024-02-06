@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/performance/list') }}">Performance Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Weekly Billing Report</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'reports/charges/export/'])
            <li><a href="{{ url('reports/performance/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow yes-border"><!-- Primary Location Box Starts -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20 no-border" id="js-address-primary-address"><!-- Box Body Starts -->
                	<input type="hidden" name="report_controller_name" value="PerformanceController" />
	                <input type="hidden" name="report_controller_func" value="weeklyBillingReportExport" />
	                <input type="hidden" name="report_name" value="Weekly Billing Report" />
                    {!! Form::open(['onsubmit'=>"event.preventDefault();",'url'=>'reports/search/performance/billing', 'name'=>'medcubicsform']) !!}
                    <div class="search_fields_container col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div id="select_transaction_date" class="margin-b-4 margin-t-10 margin-r-5 " style="float:left; width: 200px;">
                            <div class="right-inner-addon">
                                <label for="select_transaction_date" class="control-label font600">Transaction Date</label>
                                <input class="date auto-generate bg-white form-control form-select js-date-range" id="" autocomplete="off" readonly="readonly" data-label-name="Transaction Date" name="select_transaction_date" type="text" value=""><i class="fa fa-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8 no-padding margin-l-10">                                            
                        <input class="btn generate-btn pull-left m-r-m-3 js_search_export_csv" value="Generate Report" type="submit">
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div><!--  Left side Content Ends -->    
    </div>
</div>
@stop

@push('view.scripts')
{!! HTML::style('css/search_fields.css') !!}
{!! HTML::script('js/daterangepicker_dev.js') !!}
@endpush