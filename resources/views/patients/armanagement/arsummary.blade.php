@extends('admin')
<?php $id = Route::current()->parameters['id']; ?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>AR Summary</span> </small>
        </h1>
        <ol class="breadcrumb">
           <?php $uniquepatientid = $id; ?>
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
       
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o hide" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])
@stop

@section('practice')

<?php $id = Route::current()->parameters['id']; ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php 
		$activetab = 'payments_list';
        $routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-cog i-font-tabs"></i> Su<span class="text-underline">m</span>mary</a></li>
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/list') }}" accesskey="s" ><i class="fa fa-navicon i-font-tabs"></i> Li<span class="text-underline">s</span>ts</a></li>
            <li class="@if($activetab == 'workorder') active @endif hide"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> Work Order</a></li>
        </ul>
    </div>
    <!-- Tab Ends -->


    <div class="box no-border no-shadow bg-transparent">

        <div class="box-body no-padding table-responsive margin-t-5">

            <div class="col-lg-12 no-padding ">
                <div class="box-body form-horizontal  no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 no-padding bg-white yes-border border-radius-4" style="border-color: #dedddd;">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
                                <table class="popup-table-wo-border table-billing-view table margin-t-20">
                                    <tbody>
                                        <?php
                                            foreach($claim_status_wise_count as $key => $claim) {
                                               $claim_value[$claim->status]['charge'] = $claim->total_billed;
                                               $claim_value[$claim->status]['count'] = $claim->total_count;
                                            }                                          
											$status_arrs = ['Hold' => 'Claims on Hold', 'Rejection' => 'EDI Rejections', 'Denied' => 'Denied Claims', 'Pending' => 'Pending Claims', 'Submitted' => 'Submitted'];
                                       ?>
                                        <tr>
                                            <td class="font600 med-orange"><h4 class="med-orange font16 no-bottom p-l-5">Status</h4></td>
                                            <td> <h4 class="med-orange font16 no-bottom">Claims</h4></td>
                                            <td><h4 class="med-orange pull-right font16 no-bottom">Amount </h4></td>

                                        </tr>
                                        @foreach($status_arrs as $key => $status_arr)
                                        <tr>
                                            <td class="font600" style="line-height: 26px"><span style="">{{$status_arr}}</span></td>
                                            <td> <span>{{$claim_value[$key]['count'] or '0'}}</span></td>
                                            <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_value[$key]['charge'])!!}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-7 col-md-8 col-sm-12 col-xs-12 p-r-0 col-lg-offset-1">
                                <div class="box no-bottom no-shadow bg-transparent"  style="border-radius: 4px;" >
                                    <div class="box-body no-bottom p-b-0 p-t-0 p-r-0">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="box no-shadow no-border bg-transparent no-bottom">

                                                <div class="box-body no-b-t  dashboard-table p-b-0 p-t-0">
                                                    <div id="chart-container">AR Management Chart Loading</div>
                                                </div><!-- /.box-body -->
                                            </div><!-- /.box -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row no-padding"><!-- Row Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-0 no-padding"><!-- Demo Financials Red Alerts  Starts -->

                </div><!-- Demo Financials Red Alerts  Ends Full 1st row -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 p-r-0"><!-- Financial Red Alert Dates Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-1 margin-t-5">
                                <h4 class="med-darkgray no-bottom"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Aging</h4>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 p-b-0 margin-t-m-10" ><!-- Outstanding Col Starts -->
                                <div class="box box-view no-shadow no-border-radius no-border bg-transparent no-bottom p-b-5"><!-- Outstanding Box Starts -->

                                    <div class="box-body table-responsive p-r-0"><!-- Outstanding Box-body Starts -->
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-right-border-5">
                                            <table class="popup-table-border bg-white table-separate table m-b-m-1">
                                                <thead>
                                                <th class="font600 med-green text-center"></th>
                                                <th class="font600 med-green text-center">Unbilled</th>
                                                <th class="font600 med-green text-center">0-30</th>
                                                <th class="font600 med-green text-center">31-60</th>
                                                <th class="font600 med-green text-center">61-90</th>
                                                <th class="font600 med-green text-center">91-120</th>
                                                <th class="font600 med-green text-center">121-150</th>
                                                <th class="font600 med-green text-center">>150</th>
                                                <th class="font600 med-green text-center">Total</th>
                                                </thead>
                                                <?php
													$total_ar_bal = 0;
												?>
                                                <tbody>
                                                    <?php
                                                      $total_ar["Unbilled"] = 0;
                                                      $total_ar["0-30"] = 0;
                                                      $total_ar["31-60"] = 0;
                                                      $total_ar["61-90"] = 0;
                                                      $total_ar["91-120"] = 0;
                                                      $total_ar["121-150"] = 0;
                                                      $total_ar[">150"] = 0;
                                                      $total_ar['total_data'] = 0;
                                                    ?>
                                                    @foreach($ins_category_value as $key_value => $ins_category)
                                                    <tr>
                                                        <td class="font600" style="border-right: 5px solid #f0f0f0; "><span class="med-green">{{@$key_value}}</span></td>
                                                        <?php //dd($ins_category_value);
															$total_value = 0;
                                                        ?>
                                                        @foreach($ins_category  as $key => $aging)
															<td class="font600 text-right"><span>
															<?php
																$total_ar_data = 0;
																$total_balance = (strpos(@$key_value, "Patient") !== false)?@$aging[0]->patient_balance:@$aging[0]->insurance_balance;
																$total_ar_data = $total_balance;
																$total_value+= @$total_balance;
																if($key == "Unbilled") {
																	$total_ar["Unbilled"]+= $total_ar_data;
																} else if($key == "0-30"){
																	$total_ar["0-30"]+= $total_ar_data;
																}else if($key == "31-60"){
																	$total_ar["31-60"]+= $total_ar_data;
																} else if($key == "61-90"){
																	$total_ar["61-90"]+= $total_ar_data;
																}else if($key == "91-120"){
																	$total_ar["91-120"]+= $total_ar_data;
																} else if($key == "121-150"){
																	$total_ar["121-150"]+= $total_ar_data;
																} else if($key == ">150"){
																	$total_ar[">150"]+= $total_ar_data;
																 }
															?>
															{!!App\Http\Helpers\Helpers::priceFormat($total_balance) !!}</span></td>
                                                        @endforeach

                                                        <?php $total_ar['total_data']+= $total_value; ?>
                                                        <td class="font600 text-right line-height-26"><span>
                                                        {!!App\Http\Helpers\Helpers::priceFormat(@$total_value) !!}</span></td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
														<td class="font600"><span class="med-green">Outstanding AR</span></td>
                                                        @foreach(@$total_ar as $key => $total_value)
                                                        <?php $total_percent[$key] = ($total_value != 0 && $total_ar['total_data'] != 0)?$total_value/$total_ar['total_data']:0; ?>
                                                        <td class="font600 text-right line-height-26"><span>
                                                        {!!App\Http\Helpers\Helpers::priceFormat($total_value) !!}</span>
                                                         </td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
														<td class="font600"><span class="med-green">%</span></td>
                                                    <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent['Unbilled']*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent["0-30"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent["31-60"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent["61-90"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent["91-120"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent["121-150"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                    {!!round($total_percent[">150"]*100)!!}%</span>
                                                         </td>
                                                         <td class="font600 text-right line-height-26"><span>
                                                          <?php $percentage = ($total_value == 0)?"0":100;?>
                                                    {!!1*$percentage!!}%</span>
                                                         </td>
                                                         </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div><!-- Outstanding box-body Ends -->
                                </div><!-- Outstanding box Ends -->
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h4 class="med-darkgray no-bottom margin-t-0"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Status Wise Summary</h4>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="box-body table-responsive p-t-0 p-r-0 table-right-border-5">
                                    <table class="popup-table-border margin-t-2 bg-white table-separate table m-b-m-1 ">
                                        <thead>
                                        <th class="font600 med-green text-center">Status</th>
                                        <th class="font600 med-green text-center">Unbilled</th>
                                        <th class="font600 med-green text-center">0-30</th>
                                        <th class="font600 med-green text-center">31-60</th>
                                        <th class="font600 med-green text-center">61-90</th>
                                        <th class="font600 med-green text-center">91-120</th>
                                        <th class="font600 med-green text-center">121-150</th>
                                         <th class="font600 med-green text-center">>150</th>
                                        <th class="font600 med-green text-center">Total</th>
                                        </thead>
                                       <tbody>
                                            <?php 
												$claims_status_balances = (array)$claim_status_wise_sum;
                                                $total_status_ar["Unbilled"] = 0;
                                                $total_status_ar["0-30"] = 0;
                                                $total_status_ar["31-60"] = 0;
                                                $total_status_ar["61-90"] = 0;
                                                $total_status_ar["91-120"] = 0;
                                                $total_status_ar["121-150"] = 0;
                                                $total_status_ar[">150"] = 0;
                                                $total_status_ar['total_data'] = 0;
                                            ?>
                                            @foreach($claims_status_balances as $key => $claims_balances)
                                            <tr>
                                            <?php $total_data= 0; ?>
                                                <td class="font600 line-height-26"><span class="med-green"> {{$key}}</span></td>
                                                    <?php $claims_balance = (array)$claims_balances; ?>
                                                    @foreach($claims_balance as $key => $claims_bal)

                                                    <td class="font600 text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_bal[0]->total_ar) !!}</td>
                                                    <?php
														$total_starus_ar_data = 0;
														$total_data+= @$claims_bal[0]->total_ar;
														$total_starus_ar_data = @$claims_bal[0]->total_ar;
														if($key == "Unbilled"){
                                                            $total_status_ar["Unbilled"]+= $total_starus_ar_data;
														} else if($key == "0-30"){
															$total_status_ar["0-30"]+= $total_starus_ar_data;
														}else if($key == "31-60"){
															$total_status_ar["31-60"]+= $total_starus_ar_data;
														} else if($key == "61-90"){
															$total_status_ar["61-90"]+= $total_starus_ar_data;
														}else if($key == "91-120"){
															$total_status_ar["91-120"]+= $total_starus_ar_data;
														} else if($key == "121-150"){
                                                            $total_status_ar["121-150"]+= $total_starus_ar_data;
														} else if($key == ">150"){
															$total_status_ar[">150"]+= $total_starus_ar_data;
														}
                                                    ?>
                                                    @endforeach
                                                    <?php
                                                        $total_status_ar['total_data']+= $total_data;?>
                                                    <td class="font600 text-right line-height-26"><span>
                                                    {!!App\Http\Helpers\Helpers::priceFormat(@$total_data) !!}</span></td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                            <td class="font600 line-height-26 totals-highlight"><span class="med-green">Totals</span></td>
                                                @foreach(@$total_status_ar as $key=>$total_value)
                                                <?php 
													$total_status_ar_percent[$key] = ($total_value != 0 && $total_status_ar['total_data'] != 0)?$total_value/$total_status_ar['total_data']:0;
												?>
                                                <td class="font600 text-right line-height-26 totals-highlight"><span class="med-green">
                                                {!!App\Http\Helpers\Helpers::priceFormat($total_value) !!}</span></td>
                                                @endforeach
                                            </tr>
                                            <tr>
												<td class="font600  line-height-26"><span class="med-green">%</span></td>

													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["Unbilled"]*100)!!}%</span>
													 </td>
													 <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["0-30"]*100)!!}%</span>
													 </td>
													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["31-60"]*100)!!}%</span>
													 </td>
													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["61-90"]*100)!!}%</span>
													 </td>
													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["91-120"]*100)!!}%</span>
													 </td>
													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent["121-150"]*100)!!}%</span>
													 </td>
													  <td class="font600 text-right line-height-26"><span>
												{!!round($total_status_ar_percent[">150"]*100)!!}%</span>
													 </td>
													 <td class="font600 text-right line-height-26"><span class="med-orange">
													  <?php $percentage = ($total_value == 0)?"0":100;?>
												{!!1*$percentage!!}%
												</span>
												</td>
											</tr>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div>
                        </div>

                    </div><!-- Financial Red Alert Dates Ends -->
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
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
	/*
	FusionCharts.ready(function () {
		var revenueChart = new FusionCharts({
			type: 'MSColumn2D',
			renderAt: 'chart-p',
			width: '100%',
			height: '190',
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
					"showLegend": "0",
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
							}]
					}],
				"dataset": [{
						"seriesname": "Credit",
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
							}]
					},
					{
						"seriesname": "Check",
						"data": [{
								"value": "12000"
							}, {
								"value": "5000"
							}, {
								"value": "8000"
							},
							{
								"value": "11000"
							},
							{
								"value": "10000"
							},
							{
								"value": "16000"
							}]
					},
					{
						"seriesname": "Others",
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
							}]
					}]
			}
		});
		revenueChart.render();
	});
	
	FusionCharts.ready(function () {
		var salesChart = new FusionCharts({
			type: 'scrollline2d',
			dataFormat: 'json',
			renderAt: 'chart-payments',
			width: '100%',
			height: '180',
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
					"paletteColors": "#008ee4,#ff780b,#fea500",
					"baseFontColor": "#00877f",
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
							{"label": "Primary"},
							{"label": "Secondary"},
							{"label": "Tertiary"},
							{"label": "Self"}

						]
					}
				],
				"dataset": [
					{
						"data": [
							{"value": "27400"},
							{"value": "29800"},
							{"value": "25800"},
							{"value": "26800"}

						]
					},
					{
						"data": [
							{"value": "17400"},
							{"value": "19800"},
							{"value": "15800"},
							{"value": "40800"}

						]
					}
				]
			}
		}).render();
	});
	*/
	
	FusionCharts.ready(function () {
		var revenueChart = new FusionCharts({
			type: 'msstackedcolumn2d',
			renderAt: 'chart-container',
			width: '100%',
			height: '230',
			dataFormat: 'json',
			dataSource: {
				"chart": {
					"xaxisname": "",
					"yaxisname": "",
					"paletteColors": "#67b6e8,#ed9a56,#758289,#bfdd68,#f06666,#f8bd19",
					"numberPrefix": "$",
					"numbersuffix": "",
					"bgColor": "#ffffff",
					"showBorder": "0",
					"theme": "fint1",
					"borderAlpha": "20",
					"showCanvasBorder": "0",
					"usePlotGradientColor": "0",
					"plotBorderAlpha": "10",
					"legendBorderAlpha": "0",
					"legendShadow": "0",
					"valueFontColor": "#ffffff",
					"showXAxisLine": "1",
					"xAxisLineColor": "#fff",
					"divlineColor": "#999999",
					"divLineIsDashed": "1",
					"showAlternateHGridColor": "0",
					"subcaptionFontBold": "0",
					"subcaptionFontSize": "14",
					"showAxisLines": "0"
				},
				"categories": <?php echo $insurance_chart_label;?>,
				"dataset": <?php echo $insurance_chart_data;?>
			}
		}).render();
	});
	
	/*
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
	
	
	FusionCharts.ready(function () {
		var revenueChart = new FusionCharts({
			type: 'pie3d',
			renderAt: 'chart-pataging1',
			width: '100%',
			height: '200',
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
	*/
</script>
@endpush