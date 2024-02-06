@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <a href="{{ url('reports/performance/list') }}">Performance Reports</a> <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Denial & Pending Claims Summary</span></small>
        </h1>
        <ol class="breadcrumb">
            @include('layouts.practice_module_stream_export', ['url' => 'reports/performance/export/'])
            <input type="hidden" name="report_controller_name" value="PerformanceController" />
            <input type="hidden" name="report_controller_func" value="denialsSummaryExport" />
            <input type="hidden" name="report_name" value="Denial & Pending Claims Summary" />
            <li><a href="{{ url('reports/performance/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20 hide"><!--  Left side Content Starts -->                    
            <div class="box no-shadow yes-border"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border" style="background: #fff;border-bottom: 1px solid #e9ecf3;border-radius: 4px 4px 0px 0px">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title" style="font-size: 18px;line-height: 30px;"> Filters</h2>
                    <div class="box-tools pull-right" style="line-height: 38px">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20 no-border" id="js-address-primary-address"><!-- Box Body Starts -->
                    {!! Form::open(['url'=>'reports/search/charges','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-8 no-padding margin-l-10">                                            
                        <input class="btn btn-medcubics-small js_filter_search_submit pull-left m-r-m-3" value="Generate Report" type="button">
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div><!--  Left side Content Ends -->    


        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20 hide"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title"> Provider Graph</h2>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <div class="box no-bottom no-shadow bg-transparent" style="border-radius: 4px;">
                            <div class="box-body no-bottom no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <div class="box no-shadow no-border bg-transparent no-bottom">

                                        <div class="box-body no-padding  dashboard-table ">
                                            <div id="chart-insaging"></div> 
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div><!--  Left side Content Ends -->

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 space20 hide"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title"> Provider Location</h2>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <div class="box no-bottom no-shadow bg-transparent" style="border-radius: 4px;">
                            <div class="box-body no-bottom no-padding">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <div class="box no-shadow no-border bg-transparent no-bottom">

                                        <div class="box-body no-padding  dashboard-table ">
                                            <div id="chart-container2"></div> 
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div><!--  Left side Content Ends -->		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->

                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title"> Denied/Pending Claims - Status Summary					</h2>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                @if(!empty($denials_billing))
                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Reasons Identified</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                            
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Contracted per Fee Schedule</th>                                           
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Approx. Time line to Expect Payment</th>
                        </tr>
                        @foreach($denials_billing as $billings)
                        @foreach($billings as $billing)
                        <tr>
                            <td class="" style="line-height: 24px;">{{ $billing['status']}}{{(!empty($billing['description']))?' - '.Str::words($billing['description'],2):'' }} </td>
                            <td class="text-center">{{array_sum($billing['claims'])}}</td>
                            <td class="text-right">${{$billing['value']}}</td>
                            <td class="text-right">${{$billing['fee_schedule']}}</td>
                            <td class="text-right">21</td>   
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="med-green hide">
                            <td class="med-orange font600">Totals</td>
                            <td class="med-green font600 text-right"></td>
                            <td class="med-green font600 text-center">334</td>
                            <td class="med-green font600 text-right">$17411.00</td>
                            <td class="med-green font600 text-right">$15059.00</td>

                            <td class="med-green font600 text-right"></td>   
                        </tr>
                    </table>
                </div><!-- /.box-body -->
                @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center thumbnail"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                @endif
            </div><!-- Primary Location box Ends-->
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->

                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title"> Coding Denial - Status Summary</h2>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                @if(!empty($denials_coding))
                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f; text-align: left !important;">Coding Denials</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                                                                        
                        </tr>
                        @foreach($denials_coding as $coding)
                        <tr>
                            <td class="" style="line-height: 24px;">{{ $coding['status']}}{{(!empty($coding['description']))?' - '.Str::words($coding['description'],2):'' }}</td>
                            <td class="text-center">{{array_sum($coding['claims'])}}</td>
                            <td class="text-right">${{$coding['value']}}</td>
                        </tr>
                        @endforeach
                        <tr class="med-green hide">
                            <td class="med-orange font600">Totals</td>
                            <td class="med-green font600 text-right"></td>
                            <td class="med-green font600 text-center">307</td>
                            <td class="med-green font600 text-right">$16695.00</td>

                        </tr>
                    </table>
                </div><!-- /.box-body -->
                @else
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center thumbnail"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                @endif
            </div><!-- Primary Location box Ends-->
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
{!! HTML::script('js/dashboard/fusioncharts.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint1.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.ar.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint2.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fintinsurance.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.charts.js') !!}

{!! HTML::script('js/dashboard/fusioncharts.powercharts.js') !!}
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>
	/*	
    FusionCharts.ready(function () {
        var comparisonChart = new FusionCharts({
            type: 'msstepline',
            renderAt: 'chart-2',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "captionFontSize": "14",
                    "subcaptionFontSize": "14",
                    "subcaptionFontBold": "0",
                    "labelFontSize": "13",
                    "labelFontColor": "#999696",
                    "labelFontBold": "0",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "baseFont": "'Open Sans', sans-serif",
                    "xaxisname": "",
                    "yaxisname": "",
                    "usePlotGradientColor": "0",
                    "bgColor": "#ffffff",
                    "palettecolors": "#6baa01, #d35400",
                    "showBorder": "0",
                    "showPlotBorder": "0",
                    "showValues": "0",
                    "showShadow": "0",
                    "showAlternateHGridColor": "0",
                    "showCanvasBorder": "0",
                    "showXAxisLine": "1",
                    "numberprefix": "$",
                    "drawverticaljoints": "1",
                    "useforwardsteps": "0",
                    "xAxisLineThickness": "1",
                    "xAxisLineColor": "#ccc",
                    "canvasBgColor": "#ffffff",
                    "divlineAlpha": "20",
                    "divlineColor": "#999999",
                    "divlineThickness": "1",
                    "divLineIsDashed": "1",
                    "divLineDashLen": "1",
                    "divLineGapLen": "1",
                    "showLegend": "1",
                    "toolTipColor": "#ffffff",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "legendBgAlpha": "10",
                    "legendBgColor": "#00877f",
                    "legendBorderAlpha": "1",
                    "legendShadow": "1",
                    "legendItemFontSize": "13",
                    "legendBorderRadius": "4",
                    "legendItemFontColor": "#666666",
                    "legendCaptionFontSize": "20",
                    "legendItemHoverFontColor": "#00877f",
                    "legendshadow": "1",
                    "legendPosition": "bottom",
                    "legendAllowDrag": "1",
                    "legendIconScale": "1",
                    "chartTopMargin": "20",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "20"
                },
                "categories": [
                    {
                        "category": [
                            {"label": "Jan"},
                            {"label": "Feb"},
                            {"label": "Mar"},
                            {"label": "Apr"},
                            {"label": "May"},
                            {"label": "Jun"},
                            {"label": "Jul"},
                            {"label": "Aug"},
                            {"label": "Sep"},
                            {"label": "Oct"},
                            {"label": "Nov"},
                            {"label": "Dec"}
                        ]
                    }
                ],
                "dataset": [
                    {
                        "seriesname": "Revenue",
                        "linethickness": "3",
                        "anchorradius": "3",
                        "data": [
                            {"value": "374000"},
                            {"value": "350000"},
                            {"value": "380000"},
                            {"value": "340000"},
                            {"value": "398000"},
                            {"value": "326000"},
                            {"value": "448000"},
                            {"value": "379000"},
                            {"value": "355000"},
                            {"value": "374000"},
                            {"value": "348000"},
                            {"value": "402000"}
                        ]
                    },
                    {
                        "seriesname": "Expense",
                        "linethickness": "3",
                        "anchorradius": "3",
                        "data": [
                            {"value": "100000"},
                            {"value": "115000"},
                            {"value": "135000"},
                            {"value": "150000"},
                            {"value": "110000"},
                            {"value": "98000"},
                            {"value": "118000"},
                            {"value": "197000"},
                            {"value": "228000"},
                            {"value": "249000"},
                            {"value": "229000"},
                            {"value": "208000"}
                        ]
                    }
                ]
            }
        });
        comparisonChart.render();
    });
	*/
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie3d',
            renderAt: 'chart-insaging',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "palette": "2",
                    "animation": "1",
                    "formatnumberscale": "1",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "baseFont": "'Open Sans', sans-serif",
                    "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
                    "decimals": "0",
                    "numberprefix": "$",
                    "pieslicedepth": "30",
                    "startingangle": "125",
                    "toolTipColor": "#ffffff",
                    "chartTopMargin": "0",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "showborder": "0"
                },
                "data": [
                    {
                        "label": "Denied-No Auth",
                        "value": "1",
                        "issliced": "0"
                    },
                    {
                        "label": "Denied-Global",
                        "value": "1",
                        "issliced": "0"
                    },
                    {
                        "label": "Denied-No Coverage",
                        "value": "1",
                        "issliced": "0"
                    },
                    {
                        "label": "Pending-Recently Filed",
                        "value": "1",
                        "issliced": "0"
                    },
                    {
                        "label": "Pending-Inprocess",
                        "value": "1",
                        "issliced": "0"
                    }
                ]
            }
        });
        revenueChart.render();
    });
	/*
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie3d',
            renderAt: 'chart-pataging',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "palette": "2",
                    "animation": "1",
                    "formatnumberscale": "1",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "baseFont": "'Open Sans', sans-serif",
                    "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
                    "decimals": "0",
                    "numberprefix": "$",
                    "pieslicedepth": "30",
                    "startingangle": "125",
                    "toolTipColor": "#ffffff",
                    "chartTopMargin": "0",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "showborder": "0"
                },
                "data": [
                    {
                        "label": "0-30",
                        "value": "100524",
                        "issliced": "0"
                    },
                    {
                        "label": "31-60",
                        "value": "87790",
                        "issliced": "0"
                    },
                    {
                        "label": "61-90",
                        "value": "81898",
                        "issliced": "0"
                    },
                    {
                        "label": "91-120",
                        "value": "76438",
                        "issliced": "0"
                    },
                    {
                        "label": "> 121",
                        "value": "41637",
                        "issliced": "0"
                    }
                ]
            }
        });

        revenueChart.render();
    });
	*/
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'MSColumn2D',
            renderAt: 'chart-container2',
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
                    "paletteColors": "#1aaf5d,#f8bd19,#f2c500",
                    "theme": "fint1",
                    "palette": "1",
                    "bgColor": "#ffffff",
                    "bgAlpha": "1",
                    "canvasBgColor": "#ffffff", //this 2 lines for graph bg color
                    "canvasBgAlpha": "0",
                },
                "categories": [{
                        "category": [{
                                "label": "Jan"
                            }, {
                                "label": "Feb"
                            }, {
                                "label": "Mar"
                            },
                            {
                                "label": "Apr"
                            },
                            {
                                "label": "May"
                            },
                            {
                                "label": "Jun"
                            },
                            {
                                "label": "Jul"
                            },
                            {
                                "label": "Aug"
                            },
                            {
                                "label": "Sep"
                            },
                            {
                                "label": "Oct"
                            },
                            {
                                "label": "Nov"
                            },
                            {
                                "label": "Dec"
                            }]
                    }],
                "dataset": [{
                        "seriesname": "Total Denial",
                        "data": [{
                                "value": "10000"
                            }, {
                                "value": "11500"
                            }, {
                                "value": "12500"
                            },
                            {
                                "value": "13500"
                            },
                            {
                                "value": "11500"
                            },
                            {
                                "value": "22500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "10000"
                            },
                            {
                                "value": "14500"
                            },
                            {
                                "value": "21300"
                            },
                            {
                                "value": "9000"
                            },
                            {
                                "value": "15000"
                            }]
                    },
                    {
                        "seriesname": "Coding Denial",
                        "data": [{
                                "value": "8400"
                            }, {
                                "value": "12800"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "21800"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "26800"
                            }]
                    }]
            }
        });
        revenueChart.render();
    });
	/*
    FusionCharts.ready(function () {
        var fuelWidget = new FusionCharts({
            type: 'cylinder',
            dataFormat: 'json',
            id: 'fuelMeter',
            renderAt: 'chart-3',
            width: '100%',
            height: '300',
            dataSource: {
                "chart": {
                    "theme": "ar",
                    "caption": "",
                    "subcaption": "",
                    "lowerLimit": "0",
                    "upperLimit": "120",
                    "labelFontSize": "13",
                    "labelFontColor": "#999696",
                    "labelFontBold": "0",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "baseFont": "'Open Sans', sans-serif",
                    "lowerLimitDisplay": "0",
                    "upperLimitDisplay": " 150 days",
                    "numberSuffix": " days",
                    "showValue": "1",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "30",
                    "bgColor": "#ffffff",
                    "showValues": "0",
                    "showShadow": "0",
                    "canvasBgColor": "#ffffff",
                    //Changing the Cylinder fill color
                    "cylFillColor": "#fe0000"
                },
                "value": "81"
            }
        }).render();
    });
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'errorline',
            renderAt: 'chart-collections',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisname": "",
                    "yAxisName": "",
                    "numberPrefix": "$",
                    "halferrorbar": "0",
                    "plotTooltext": "<div id='nameDiv' style='font-size: 14px; border-bottom: 1px dashed #999999; font-weight:bold; padding-bottom: 3px; margin-bottom: 5px; display: inline-block;'>$label :</div>{br}$seriesName : <b>$dataValue</b>{br} {br}Deviation : <b> $errorDataValue</b>",
                    //Cosmetics
                    "paletteColors": "#1aaf5d,#f8bd19,#f2c500",
                    "baseFontColor": "#999696",
                    "baseFont": "'Open Sans', sans-serif",
                    "baseFontSize": "13",
                    "captionFontSize": "14",
                    "subcaptionFontSize": "14",
                    "subcaptionFontBold": "0",
                    "showBorder": "0",
                    "bgColor": "#ffffff",
                    "showValues": "0",
                    "showShadow": "0",
                    "canvasBgColor": "#ffffff",
                    "canvasBorderAlpha": "0",
                    "divlineAlpha": "20",
                    "divlineColor": "#999999",
                    "divlineThickness": "1",
                    "divLineIsDashed": "1",
                    "divLineDashLen": "1",
                    "divLineGapLen": "1",
                    "usePlotGradientColor": "0",
                    "showplotborder": "0",
                    "showXAxisLine": "1",
                    "xAxisLineThickness": "1",
                    "xAxisLineColor": "#ccc",
                    "showAlternateHGridColor": "0",
                    "showAlternateVGridColor": "0",
                    "toolTipColor": "#ffffff",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "legendBgAlpha": "10",
                    "legendBgColor": "#00877f",
                    "legendBorderAlpha": "1",
                    "legendShadow": "1",
                    "legendItemFontSize": "13",
                    "legendBorderRadius": "4",
                    "legendItemFontColor": "#666666",
                    "legendCaptionFontSize": "20",
                    "legendItemHoverFontColor": "#00877f",
                    "legendshadow": "1",
                    "legendPosition": "bottom",
                    "legendAllowDrag": "1",
                    "legendIconScale": "1",
                    "errorBarColor": "#ccc",
                    "errorBarAlpha": "50",
                    "errorBarThickness": "0",
                    "errorBarWidthPercent": "30",
                    "chartTopMargin": "20",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "20"
                },
                "categories": [{
                        "category": [
                            {"label": "Jan"},
                            {"label": "Feb"},
                            {"label": "Mar"},
                            {"label": "Apr"},
                            {"label": "May"},
                            {"label": "Jun"},
                            {"label": "Jul"},
                            {"label": "Aug"},
                            {"label": "Sep"},
                            {"label": "Oct"},
                            {"label": "Nov"},
                            {"label": "Dec"}
                        ]
                    }
                ],
                "dataset": [
                    {
                        "seriesName": "Collections",
                        "dashed": "1",
                        "dashlen": "2",
                        "dashGap": "2",
                        "data": [
                            {
                                "value": "16000",
                                "errorvalue": "2000"
                            },
                            {
                                "value": "20000",
                                "errorvalue": "4000"
                            },
                            {
                                "value": "18000",
                                "errorvalue": "1000"
                            },
                            {
                                "value": "19000",
                                "errorvalue": "1500"
                            },
                            {
                                "value": "15000",
                                "errorvalue": "1000"
                            },
                            {
                                "value": "21000",
                                "errorvalue": "4500"
                            },
                            {
                                "value": "16000",
                                "errorvalue": "1500"
                            },
                            {
                                "value": "20000",
                                "errorvalue": "3000"
                            },
                            {
                                "value": "17000",
                                "errorvalue": "2000"
                            },
                            {
                                "value": "22000",
                                "errorvalue": "4000"
                            },
                            {
                                "value": "19000",
                                "errorvalue": "2500"
                            },
                            {
                                "value": "23000",
                                "errorvalue": "3000"
                            }
                        ]
                    },
                    {
                        "seriesName": "Adjustments",
                        "dashed": "1",
                        "dashlen": "2",
                        "dashGap": "2",
                        "data": [
                            {
                                "value": "8000",
                                "errorvalue": "2000"
                            },
                            {
                                "value": "9000",
                                "errorvalue": "2000"
                            },
                            {
                                "value": "7000",
                                "errorvalue": "2700"
                            },
                            {
                                "value": "8000",
                                "errorvalue": "2750"
                            },
                            {
                                "value": "6000",
                                "errorvalue": "1200"
                            },
                            {
                                "value": "11000",
                                "errorvalue": "3000"
                            },
                            {
                                "value": "6900",
                                "errorvalue": "1250"
                            },
                            {
                                "value": "8000",
                                "errorvalue": "1400"
                            },
                            {
                                "value": "6500",
                                "errorvalue": "1200"
                            },
                            {
                                "value": "12000",
                                "errorvalue": "3000"
                            },
                            {
                                "value": "6000",
                                "errorvalue": "1500"
                            },
                            {
                                "value": "11000",
                                "errorvalue": "2500"
                            }
                        ]
                    }
                ]
            }
        });
        revenueChart.render();
    });
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'doughnut2d',
            renderAt: 'chart-container1',
            width: '90%',
            height: '230',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "numberPrefix": "$",
                    "startingAngle": "20",
                    "showPercentValues": "1",
                    "showPercentInTooltip": "0",
                    "enableSmartLabels": "0",
                    "enableMultiSlicing": "0",
                    "baseFontSize": "13",
                    "decimals": "1",
                    "chartTopMargin": "-20",
                    "chartBottomMargin": "-20",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    //Theme
                    "theme": "fint2"
                },
                "data": [
                    {
                        "label": "Food",
                        "value": "285040"
                    },
                    {
                        "label": "Apparels",
                        "value": "146330"
                    },
                    {
                        "label": "Household",
                        "value": "49100",
                        // "isSliced": "1"s
                    }
                ]
            }
        }).render();
    });
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container',
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
                    "showvalues": "0",
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
                        "category": [{
                                "label": "Jan"
                            }, {
                                "label": "Feb"
                            }, {
                                "label": "Mar"
                            },
                            {
                                "label": "Apr"
                            },
                            {
                                "label": "May"
                            },
                            {
                                "label": "Jun"
                            },
                            {
                                "label": "Jul"
                            },
                            {
                                "label": "Aug"
                            },
                            {
                                "label": "Sep"
                            },
                            {
                                "label": "Oct"
                            },
                            {
                                "label": "Nov"
                            },
                            {
                                "label": "Dec"
                            }]
                    }],
                "dataset": [{
                        "seriesname": "Primary",
                        "data": [{
                                "value": "32000"
                            }, {
                                "value": "51500"
                            }, {
                                "value": "22500"
                            },
                            {
                                "value": "53500"
                            },
                            {
                                "value": "41500"
                            },
                            {
                                "value": "22500"
                            },
                            {
                                "value": "42500"
                            },
                            {
                                "value": "38000"
                            },
                            {
                                "value": "34500"
                            },
                            {
                                "value": "39300"
                            },
                            {
                                "value": "47000"
                            },
                            {
                                "value": "45000"
                            }]
                    },
                    {
                        "seriesname": "Secondary",
                        "data": [{
                                "value": "18400"
                            }, {
                                "value": "32800"
                            },
                            {
                                "value": "19500"
                            },
                            {
                                "value": "21800"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "29500"
                            },
                            {
                                "value": "27500"
                            },
                            {
                                "value": "32500"
                            },
                            {
                                "value": "42500"
                            },
                            {
                                "value": "19500"
                            },
                            {
                                "value": "32500"
                            },
                            {
                                "value": "26800"
                            }]
                    },
                    {
                        "seriesname": "Tertiary",
                        "data": [{
                                "value": "8400"
                            }, {
                                "value": "12800"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "21800"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "24500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "26800"
                            }]
                    },
                    {
                        "seriesname": "Self",
                        "data": [{
                                "value": "8400"
                            }, {
                                "value": "12800"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "21800"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "9500"
                            },
                            {
                                "value": "24500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "12500"
                            },
                            {
                                "value": "26800"
                            }]
                    }
                ]
            }
        });
        revenueChart.render();
    });
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie2d',
            renderAt: 'chart-insurances',
            width: '100%',
            height: '400',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "numberPrefix": "$",
                    "startingAngle": "20",
                    "showPercentValues": "1",
                    "showPercentInTooltip": "0",
                    "showLabels": "0",
                    "enableSmartLabels": "0",
                    "enableMultiSlicing": "0",
                    "plotBorderColor": "#fff",
                    "plotBorderThickness": "1",
                    "showPlotBorder": "1",
                    "showLegend": "1",
                    "decimals": "1",
                    "chartTopMargin": "0",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    //Theme
                    "theme": "fintinsurance"
                },
                "data": [
                    {
                        "label": "Insurance 1",
                        "value": "185040"
                    },
                    {
                        "label": "Insurance 2",
                        "value": "146330"
                    },
                    {
                        "label": "Insurance 3",
                        "value": "105070"
                    },
                    {
                        "label": "Insurance 5",
                        "value": "49100",
                        "isSliced": "0"
                    }
                ]
            }
        }).render();
    });
	
    FusionCharts.ready(function () {
        var salesChart = new FusionCharts({
            type: 'scrollline2d',
            dataFormat: 'json',
            renderAt: 'chart-payments',
            width: '100%',
            height: '400',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisName": "",
                    "yAxisName": "",
                    "showValues": "0",
                    "numberPrefix": "$",
                    "showBorder": "0",
                    "showShadow": "0",
                    "showLabels": "1",
                    "enableSmartLabels": "0",
                    "enableMultiSlicing": "0",
                    "toolTipColor": "#ffffff",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "showLegend": "1",
                    "legendBgAlpha": "10",
                    "legendBgColor": "#00877f",
                    "legendBorderAlpha": "1",
                    "legendShadow": "1",
                    "legendItemFontSize": "13",
                    "legendBorderRadius": "4",
                    "legendItemFontColor": "#666666",
                    "legendCaptionFontSize": "20",
                    "legendItemHoverFontColor": "#00877f",
                    "legendshadow": "1",
                    "legendborderalpha": "1",
                    "legendPosition": "bottom",
                    "legendAllowDrag": "1",
                    "legendIconScale": "1",
                    "bgColor": "#ffffff",
                    "paletteColors": "#008ee4,#6baa01,#fea500",
                    "baseFontColor": "#999696",
                    "baseFontSize": "12",
                    "baseFont": "'Open Sans', sans-serif",
                    "yAxisNameFontSize": "14",
                    "yAxisNameFontColor": "#00877f",
                    "xAxisNameFontSize": "14",
                    "xAxisNameFontColor": "#00877f",
                    "showCanvasBorder": "0",
                    "showAxisLines": "0",
                    "showAlternateHGridColor": "0",
                    "divlineAlpha": "20",
                    "divlineThickness": "1",
                    "divLineIsDashed": "1",
                    "divLineDashLen": "1",
                    "divLineGapLen": "1",
                    "lineThickness": "3",
                    "flatScrollBars": "1",
                    "scrollheight": "10",
                    "numVisiblePlot": "6",
                    "showHoverEffect": "1",
                    "chartTopMargin": "20",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "20",
                },
                "categories": [
                    {
                        "category": [
                            {"label": "Jan 2012"},
                            {"label": "Feb 2012"},
                            {"label": "Mar 2012"},
                            {"label": "Apr 2012"},
                            {"label": "May 2012"},
                            {"label": "Jun 2012"},
                            {"label": "Jul 2012"},
                            {"label": "Aug 2012"},
                            {"label": "Sep 2012"},
                            {"label": "Oct 2012"},
                            {"label": "Nov 2012"},
                            {"label": "Dec 2012"},
                            {"label": "Jan 2013"},
                            {"label": "Feb 2013"},
                            {"label": "Mar 2013"},
                            {"label": "Apr 2013"},
                            {"label": "May 2013"},
                            {"label": "Jun 2013"},
                            {"label": "Jul 2013"},
                            {"label": "Aug 2013"},
                            {"label": "Sep 2013"},
                            {"label": "Oct 2013"},
                            {"label": "Nov 2013"},
                            {"label": "Dec 2013"}
                        ]
                    }
                ],
                "dataset": [
                    {
                        "data": [
                            {"value": "27400"},
                            {"value": "29800"},
                            {"value": "25800"},
                            {"value": "26800"},
                            {"value": "29600"},
                            {"value": "32600"},
                            {"value": "31800"},
                            {"value": "36700"},
                            {"value": "29700"},
                            {"value": "31900"},
                            {"value": "34800"},
                            {"value": "24800"},
                            {"value": "26300"},
                            {"value": "31800"},
                            {"value": "30900"},
                            {"value": "33000"},
                            {"value": "36200"},
                            {"value": "32100"},
                            {"value": "37500"},
                            {"value": "38500"},
                            {"value": "35400"},
                            {"value": "38200"},
                            {"value": "33300"},
                            {"value": "38300"}
                        ]
                    },
                    {
                        "data": [
                            {"value": "47400"},
                            {"value": "49800"},
                            {"value": "45800"},
                            {"value": "46800"},
                            {"value": "49600"},
                            {"value": "42600"},
                            {"value": "41800"},
                            {"value": "26700"},
                            {"value": "19700"},
                            {"value": "53900"},
                            {"value": "43800"},
                            {"value": "14800"},
                            {"value": "6300"},
                            {"value": "24800"},
                            {"value": "64900"},
                            {"value": "13000"},
                            {"value": "24200"},
                            {"value": "28100"},
                            {"value": "17500"},
                            {"value": "28500"},
                            {"value": "15400"},
                            {"value": "28200"},
                            {"value": "31300"},
                            {"value": "32300"}
                        ]
                    },
                    {
                        "data": [
                            {"value": "17400"},
                            {"value": "19800"},
                            {"value": "15800"},
                            {"value": "40800"},
                            {"value": "40600"},
                            {"value": "40600"},
                            {"value": "46800"},
                            {"value": "22700"},
                            {"value": "29700"},
                            {"value": "23900"},
                            {"value": "31800"},
                            {"value": "16800"},
                            {"value": "11300"},
                            {"value": "14800"},
                            {"value": "44900"},
                            {"value": "43000"},
                            {"value": "27200"},
                            {"value": "21100"},
                            {"value": "27500"},
                            {"value": "21500"},
                            {"value": "21400"},
                            {"value": "21200"},
                            {"value": "32300"},
                            {"value": "22300"}
                        ]
                    }
                ]
            }
        }).render();
    });
	
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie2d',
            renderAt: 'chart-1',
            width: '90%',
            height: '230',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "numberPrefix": "$",
                    "startingAngle": "20",
                    "showPercentValues": "1",
                    "showPercentInTooltip": "0",
                    "showLabels": "0",
                    "enableSmartLabels": "0",
                    "enableMultiSlicing": "0",
                    "palettecolors": "#5cb0c3,#a6bb50,#e45b5b,#8c9ba8, #eb873d",
                    "showValues": "0",
                    "showLegend": "0",
                    "decimals": "1",
                    "plotBorderColor": "#fff",
                    "plotBorderThickness": "2",
                    "showPlotBorder": "1",
                    "chartTopMargin": "25",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    //Theme
                    "theme": "fintinsurance"
                },
                "data": [
                    {
                        "label": "Insurance AR",
                        "value": "385040"
                    },
                    {
                        "label": "Patient AR",
                        "value": "146330"
                    },
                    {
                        "label": "Denied Claims",
                        "value": "96330"
                    },
                    {
                        "label": "AR Days",
                        "value": "114100",
                        "isSliced": "0"
                    }
                ]
            }
        }).render();
    });
	*/
</script>

@endpush