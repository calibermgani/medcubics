@extends('admin')
@section('pageTitle', 'AR Dashboard')

@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>AR Management Analytics</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" data-url="" class=""><i class="fa fa-refresh" data-placement="bottom"  data-toggle="tooltip" data-original-title="Refresh Data"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>            
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Dashboard Top: Unbilled Charges -->

</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">AR Management Analytics</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -10px;">

                <select type="text" class="form-control select2">
                    <option>-- Sort by Facility --</option>
                    <option>Facility 1</option>
                    <option>Facility 2</option>
                </select>                                
            </div>
        </div>

    </div>
    <!-- <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding pull-right">
        <p class="pull-right no-bottom med-darkgray"> <i class="fa fa-calendar margin-r-5"></i> Last Month</p>
    </div> -->
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0" style="border:1px solid #ccc; border-radius: 4px;">   


            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                    <div class="box no-shadow no-border no-bottom" style="background: transparent">

                        <div class="box-body no-b-t no-padding " >
                            <div id="chart-1"></div> 
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 no-padding"  style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-0">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1 p-l-0 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 p-l-0 p-r-0">
                                <p class="font20" style="margin-bottom: 28px;"><span class="med-orange font600 font26">$34150.00</span> Outstanding AR</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #5cb0c3; position: absolute; margin-left: -17px; margin-top: 3px;"></i><span class="font600" style="font-size: 18px;"> $24234.60</span> Insurance AR</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #a6bb50; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> $11623.00</span> Patient AR</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #e45b5b; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> 2</span> Denied Claims</p>                               
                                <p class="margin-t-22 font14" style="margin-left:17px; margin-bottom: 1px;"><i class="fa fa-circle" style="color: #8c9ba8; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> 10</span> AR Days</p>                               
                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 no-padding" style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px;">
                                <p class="margin-b-20" style="font-size: 40px"><span class="font600" style="color: #87a801">27%</span></p>
                                <p style="padding-bottom: 18px; font-size: 16px"><i class="fa fa-arrow-circle-up" style="color: #87a801; font-size: 18px;"></i> Charges</p>
                                <p class="margin-b-20" style="font-size: 40px;padding-top: 50px;"><span class="font600" style="color: #eb3d3d">3%</span></p>
                                <p class="font16" style="margin-bottom: 0px;"><i class="fa fa-arrow-circle-down" style="color: #eb3d3d;font-size: 18px;"></i> Clean Claims</p>

                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border no-bottom">

                        <div class="box-body no-b-t margin-b-1 no-bottom p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-l-5">
                                <p class="font20" style="margin-bottom: 13px;"><span class="font20"></span></p>
                                <table class="table table-separate table-striped">
                                    <thead>
                                    <th style="background: #fff; color:#00877f; border-bottom: 2px solid #00877f !important;" class="font600">Name</th>
                                    <th style="background: #fff; color:#00877f; border-bottom: 2px solid #00877f !important;" class="font600">Claim</th>
                                    <th style="background: #fff; color:#00877f; border-bottom: 2px solid #00877f !important;" class="font600 text-right">Value</th>
                                    </thead>
                                    <tbody>
                                        <tr style="font-size: 16px;">
                                            <td style="line-height: 0px;">&emsp;</td>
                                            <td></td>
                                            <td></td>                                            
                                        </tr>

                                        <tr style="font-size: 15px;">
                                            <td style="line-height: 30px; color: #7fa708">Denied Claims</td>
                                            <td  style="font-size: 15px;">3124</td>
                                            <td style="font-size: 15px;" class="text-right">332442.00</td>                                            
                                        </tr>
                                        <tr style="font-size: 15px;">
                                            <td style="line-height: 30px;color: #008ee4">TFL Crossed</td>
                                            <td>685</td>
                                            <td class="text-right">144636.00</td>                                            
                                        </tr>
                                        <tr style="font-size: 15px;">
                                            <td style="line-height: 30px;">Insurance AR</td>
                                            <td>34</td>
                                            <td class="text-right">7574.00</td>                                            
                                        </tr>
                                        <tr style="font-size: 15px;">
                                            <td style="line-height: 30px;color: #eb3d3d;">Patient AR</td>
                                            <td>866</td>
                                            <td class="text-right">96742.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 no-padding" >
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Performance Management</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  

    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Collections Vs Adjustments</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  
</div>
<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-container">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>

<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-2">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 no-padding" >
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">AR Days</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  

    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Insurance Aging</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  
    
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Patient Aging</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-3">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>

<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-pataging">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>

<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-insaging">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Insurance Wise</h3>                           
            <div class="box-tools pull-right font14 margin-t-8 med-darkgray" style="margin-right: -10px;">
                <p class="font600 med-orange font16">$4531.00</p>
            </div>
        </div>
    </div>  
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table p-r-0">           
                        <table class="table table-bordered table-striped" id="example1">
                            <thead style="">
                            <th style="text-align: left;border-bottom: 3px solid #87cdc7 !important;">Insurance Name</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Jan</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Feb</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Mar</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Apr</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">May</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Jun</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Jul</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Aug</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Sep</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Oct</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Nov</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Dec</th>
                            <th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Total</th>

                            </thead>
                            <tbody>
                                <tr>
                                    <td class="med-green" style="background: #fff"></td>
                                    <td style="text-align:right;background: #fff">&emsp;</td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff"></td>
                                    <td style="text-align:right;background: #fff" class="med-green font600"></td>
                                </tr>

                                <tr>
                                    <td class="med-green">Cigna Healthcare</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right" class="med-green font600">1253.00</td>
                                </tr>
                                <tr>
                                    <td class="med-green">Alaska United Food</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right" class="med-green font600">5367.00</td>
                                </tr>

                                <tr>
                                    <td class="med-green">8th District Electrical</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right" class="med-green font600">2585.00</td>
                                </tr>
                                <tr>
                                    <td class="med-green">Aarp Medicare</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right">243.00</td>
                                    <td style="text-align:right">53.00</td>
                                    <td style="text-align:right" class="med-green font600">2541.00</td>
                                </tr>
                                <tr>
                                    <td class="med-orange font600">Total</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="font600 med-green">243.00</td>
                                    <td style="text-align:right" class="font600 med-green">53.00</td>
                                    <td style="text-align:right" class="med-orange font600">4531.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
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

<script>
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
                    labelFontSize: "13",
                    labelFontColor: "#999696",
                    labelFontBold: "0",
                    baseFontColor: "#999696",
                    baseFontSize: "13",
                    baseFont: "'Open Sans', sans-serif",
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
                    showLegend: "1",
                    toolTipColor: "#ffffff",
                    toolTipBorderThickness: "0",
                    toolTipBgColor: "#000000",
                    toolTipBgAlpha: "85",
                    toolTipBorderRadius: "4",
                    toolTipPadding: "10",
                    legendBgAlpha: "10",
                    legendBgColor: "#00877f",
                    legendBorderAlpha: "1",
                    legendShadow: "1",
                    legendItemFontSize: "13",
                    legendBorderRadius: "4",
                    legendItemFontColor: "#666666",
                    legendCaptionFontSize: "20",
                    legendItemHoverFontColor: "#00877f",
                    legendshadow: "1",
                    legendPosition: "bottom",
                    legendAllowDrag: "1",
                    legendIconScale: "1",
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
        baseFontColor: "#999696",
                            baseFontSize: "13",
                            baseFont: "'Open Sans', sans-serif",
        "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
        "decimals": "0",
        "numberprefix": "$",
        "pieslicedepth": "30",
        "startingangle": "125",
        toolTipColor: "#ffffff",
         "chartTopMargin": "0",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                            toolTipBorderThickness: "0",
                            toolTipBgColor: "#000000",
                            toolTipBgAlpha: "85",
                            toolTipBorderRadius: "4",
                            toolTipPadding: "10", 
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
        baseFontColor: "#999696",
                            baseFontSize: "13",
                            baseFont: "'Open Sans', sans-serif",
        "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
        "decimals": "0",
        "numberprefix": "$",
        "pieslicedepth": "30",
        "startingangle": "125",
        toolTipColor: "#ffffff",
         "chartTopMargin": "0",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                            toolTipBorderThickness: "0",
                            toolTipBgColor: "#000000",
                            toolTipBgAlpha: "85",
                            toolTipBorderRadius: "4",
                            toolTipPadding: "10", 
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

    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'MSColumn2D',
            renderAt: 'chart-container2',
            width: '100%',
            height: '400',
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
					"seriesname": "Insurance AR",
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
                        "seriesname": "Patient AR",
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
	
    FusionCharts.ready(function(){
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
					labelFontSize: "13",
					labelFontColor: "#999696",
					labelFontBold: "0",
					baseFontColor: "#999696",
					baseFontSize: "13",
					baseFont: "'Open Sans', sans-serif",
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
                    baseFontColor: "#999696",
                    baseFont: "'Open Sans', sans-serif",
                    baseFontSize: "13",
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
                    toolTipColor: "#ffffff",
                    toolTipBorderThickness: "0",
                    toolTipBgColor: "#000000",
                    toolTipBgAlpha: "85",
                    toolTipBorderRadius: "4",
                    toolTipPadding: "10",
                    legendBgAlpha: "10",
                    legendBgColor: "#00877f",
                    legendBorderAlpha: "1",
                    legendShadow: "1",
                    legendItemFontSize: "13",
                    legendBorderRadius: "4",
                    legendItemFontColor: "#666666",
                    legendCaptionFontSize: "20",
                    legendItemHoverFontColor: "#00877f",
                    legendshadow: "1",
                    legendPosition: "bottom",
                    legendAllowDrag: "1",
                    legendIconScale: "1",
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
                    toolTipColor: "#ffffff",
                    toolTipBorderThickness: "0",
                    toolTipBgColor: "#000000",
                    toolTipBgAlpha: "85",
                    toolTipBorderRadius: "4",
                    toolTipPadding: "10",
                    "showLegend": "1",
                    legendBgAlpha: "10",
                    legendBgColor: "#00877f",
                    legendBorderAlpha: "1",
                    legendShadow: "1",
                    legendItemFontSize: "13",
                    legendBorderRadius: "4",
                    legendItemFontColor: "#666666",
                    legendCaptionFontSize: "20",
                    legendItemHoverFontColor: "#00877f",
                    legendshadow: "1",
                    legendborderalpha: "1",
                    legendPosition: "bottom",
                    legendAllowDrag: "1",
                    legendIconScale: "1",
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
</script>
@endpush