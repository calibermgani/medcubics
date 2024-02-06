@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/performance/list') }}">Performance Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Provider Summary by Location</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'reports/performance/export/'])
            <input type="hidden" name="report_controller_name" value="PerformanceController" />
            <input type="hidden" name="report_controller_func" value="providerSummaryExport" />
            <input type="hidden" name="report_name" value="Provider Summary by Location" />
            <li><a href="{{ url('reports/performance/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10"><!--  Left side Content Starts -->                    
            <div class="box no-shadow yes-border border-green"><!-- Primary Location Box Starts -->
                

                <div class="box-body form-horizontal  js-address-class p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    {!! Form::open(['url'=>'reports/search/performance/provider','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
                <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8">                                            
                    <input class="btn generate-btn  js_filter_search_submit pull-left m-r-m-3" value="Generate Report" type="button">
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
@stop
@push('view.scripts')
{!! HTML::script('js/dashboard/fusioncharts.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint1.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.ar.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint2.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fintinsurance.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.charts.js') !!}

{!! HTML::script('js/dashboard/fusioncharts.powercharts.js') !!}
{!! HTML::script('js/daterangepicker_dev.js') !!}

<script>
	var wto = '';
	var url = $('#js-bootstrap-searchvalidator').attr("action");
    var api_site_url = '{{url('/')}}';   
    var listing_page_ajax_url = api_site_url+"/reports/search/performance/provider"; 

    //--------------------------------- FORM SUBMIT ----------------------

    $(".js_filter_search_submit").on("click",function(){
        $(".result_data").addClass('hide');
        getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
    });
	// Provider 1
    /*FusionCharts.ready(function () {
        var providerOne = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container-1',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisName": "",
                    "yAxisName": "",
                    "numberPrefix": "$",
                    "theme": "fint1",
                    "palette": "1",
                    "numVisiblePlot": "12",
                    showvalues: "0",
                    "legendShadow": "0",
                    "valueFontColor": "#fff",
                    "valueFontSize": "10",
                    "bgColor": "#ffffff",
                    "palettecolors": "#6bd5d3,#fed039,#a2cf48,#fd9e32",
                    "bgAlpha": "1",
                    "canvasBgColor": "#ffffff", //this 2 lines for graph bg color
                    "canvasBgAlpha": "0",
                    "chartTopMargin": "35",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0"
                },
                "categories": [{
                        "category": {!! (!empty($chart_1['facility_name']))?json_encode($chart_1['facility_name']):'[]' !!}
                    }],
                "dataset": [{
                        "seriesname": "Total Charges",
                        "data": {!! (!empty($chart_1['total_charge']))?json_encode($chart_1['total_charge']):'[]' !!}
                    },
                    {
                        "seriesname": "Total Payments",
                        "data": {!! (!empty($chart_1['payments']))?json_encode($chart_1['payments']):'[]' !!}
                    },
                    {
                        "seriesname": "Total Adjustments",
                        "data": {!! (!empty($chart_1['adjustment']))?json_encode($chart_1['adjustment']):'[]' !!}	
                    },
                    {
                        "seriesname": "Total Outstanding",
                        "data": {!! (!empty($chart_1['total_ar']))?json_encode($chart_1['total_ar']):'[]' !!}	
                    }
                ]
            }
        });

        providerOne.render();
    });*/

	// Provider 2
    /*FusionCharts.ready(function () {
        var providerTwo = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container-2',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisName": "",
                    "yAxisName": "",
                    "numberPrefix": "$",
                    "theme": "fint1",
                    "palette": "1",
                    "numVisiblePlot": "12",
                    showvalues: "0",
                    "legendShadow": "0",
                    "valueFontColor": "#fff",
                    "valueFontSize": "10",
                    "bgColor": "#ffffff",
                    "palettecolors": "#6bd5d3,#fed039,#a2cf48,#fd9e32",
                    "bgAlpha": "1",
                    "canvasBgColor": "#ffffff", //this 2 lines for graph bg color
                    "canvasBgAlpha": "0",
                    "chartTopMargin": "35",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0"
                },
                "categories": [{
                        "category": {!! (!empty($chart_2['facility_name']))?json_encode($chart_2['facility_name']):'[]' !!}
                    }],
                "dataset": [{
                        "seriesname": "Total Charges",
                        "data": {!! (!empty($chart_2['total_charge']))?json_encode($chart_2['total_charge']):'[]' !!}
                    },
                    {
                        "seriesname": "Total Payments",
                        "data": {!! (!empty($chart_2['payments']))?json_encode($chart_2['payments']):'[]' !!}
                    },
                    {
                        "seriesname": "Total Adjustments",
                        "data": {!! (!empty($chart_2['adjustment']))?json_encode($chart_2['adjustment']):'[]' !!}	
                    },
                    {
                        "seriesname": "Total Outstanding",
                        "data": {!! (!empty($chart_2['total_ar']))?json_encode($chart_2['total_ar']):'[]' !!}	
                    }
                ]
            }
        });

        providerTwo.render();
    });*/
</script>
@endpush