@extends('admin')
<?php $id = Route::getCurrentRoute()->parameter('id'); ?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>{{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Claims Analytics </span>  </small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->            
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')

@stop

@section('practice')
@include('claims/claims/stats_tabs')

<?php $id = Route::getCurrentRoute()->parameter('id'); ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php 
		$activetab = 'payments_list'; 
        $routex = explode('.',Route::currentRouteName());
    ?>

    <div class="box no-border no-shadow bg-transparent">
        <div class="box-body margin-t-10 table-responsive no-padding">
            <div class="col-lg-12 no-padding ">
                <div class="box-body form-horizontal yes-border border-radius-4 no-padding border-green">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white border-radius-4 p-b-12">
                            <!--<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 table-responsive hide">
                                <h4 class="med-darkgray"></h4>
                                <table class="popup-table-wo-border table-billing-view table margin-t-20">                    
                                    <thead>
                                        <tr>
                                            <th class="line-height-24 bg-white med-green font600 font16">EDI Status</th>
                                            <th class="line-height-24 bg-white med-green font600 font16">No of Claims</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="font600 line-height-30"><span style="">Claims Submitted</span></td>
                                            <td class="text-center"> <span>{{ @$dataArr->daily_submitted}}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="font600 line-height-30"><span style="">Claims Accepted</span></td>
                                            <td class="text-center"> <span>{{ @$dataArr->daily_accepted}}</span></td>
                                        </tr>  
                                        <tr>
                                            <td class="font600 line-height-30"><span style="">Claims Rejected</span></td>
                                            <td class="text-center"> <span>{{ @$dataArr->daily_rejected}}</span></td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>-->

                            
							<div class="box no-bottom no-shadow bg-transparent border-radius-4">
								<div class="box-body no-bottom p-b-0 p-t-0 p-r-0">               
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
										<div class="box no-shadow no-border bg-transparent no-bottom">
											<div class="box-body no-b-t  dashboard-table p-b-0 p-t-0">           
												<div id="chart-p">EDI Summary..</div>
											</div><!-- /.box-body -->
										</div><!-- /.box -->
									</div>                      
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row no-padding"><!-- Row Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Demo Financials Red Alerts  Starts -->

                </div><!-- Demo Financials Red Alerts  Ends Full 1st row -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-1 margin-t-10">
                        <h4 class="med-darkgray margin-b-5 med-orange"><i class="fa fa-bars i-font-tabs font16 "></i> 267 / 277 Response</h4>
                    </div>     

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 p-b-5 margin-t-m-10" ><!-- Outstanding Col Starts -->                                                                            
                        <div class="box box-view no-shadow no-border-radius no-border bg-transparent no-bottom p-b-12"><!-- Outstanding Box Starts -->
                            <div class="box-body table-responsive bg-transparent"><!-- Outstanding Box-body Starts -->
                                <div class="col-lg-7 col-md-8 col-sm-12 col-xs-12 no-padding table-fixed-header">
                                    <table class="bg-white table-separate table m-b-m-1 table-bordered">
                                        <thead>
                                        <th class="font600 text-center" style="border-right: 1px solid #fff;">Insurance</th>
                                        <th class="font600 text-center" style="border-right: 1px solid #fff;">Electronic Claims</th>
                                        <th colspan="2" class="font600  text-center" style="border-right: 1px solid #fff;">Clearing House Performance</th>
                                        <th colspan="2" class="font600  text-center" style="">Payer Performance</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font600 bg-white text-center" style="border-right: 1px solid #a4ede9"><span class="med-green"></span></td> 
                                                <td class="font600 text-center " style="border-right: 1px solid #a4ede9"><span></span></td> 
                                                <td class="font600 text-center " style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green">Accepted</span></td> 
                                                <td class="font600 text-center " style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green">Rejected</span></td>
                                                <td class="font600 text-center " style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green">Accepted</span></td> 
                                                <td class="font600 text-center " style="background: #dbfaf8;"><span class="med-green">Rejected</span></td>
                                            </tr>
                                            @foreach($responseArr as $list)
                                            <tr>
                                                <td class="font600 bg-white" style="border-right: 1px solid #f0f0f0"><span class="med-green">{{ $list->insurance_shortname }}</span></td> 
                                                <td class="font600 text-right" style="border-right: 1px solid #f0f0f0"><span>{{ @$list->Submitted_count + @$list->Resubmitted_count }}</span></td> 
                                                <?php 
													$clearingAccetedCount = (isset($list->Clearing_House_Accepted_count) ? $list->Clearing_House_Accepted_count : 0);												
													$clearingRejectionCount = (isset($list->Clearing_House_Rejection_count) ? $list->Clearing_House_Rejection_count : 0);												
													$cTotal = $clearingAccetedCount + $clearingRejectionCount;
												?>
                                                <td class="font600 text-right" style="border-right: 1px solid #f0f0f0">
													<span>
                                                        @if($cTotal != 0)
                                                        {{ round((($clearingAccetedCount / $cTotal ) * 100 ), 2) }}%
                                                        @else
                                                        0%
                                                        @endif
                                                    </span>
												</td> 
                                                <td class="font600 text-right" style="border-right: 1px solid #f0f0f0">
                                                    @if($cTotal != 0)
                                                    {{ round((($clearingRejectionCount / $cTotal ) * 100 ), 2) }}%
                                                    @else
                                                    0%
                                                    @endif
                                                </td>
                                                <?php 
													$payerAccetedCount = (isset($list->Payer_Accepted_count) ? $list->Payer_Accepted_count : 0);
													$payerRejectionCount = (isset($list->Payer_Rejected_count) ? $list->Payer_Rejected_count : 0);
													$pTotal = $payerAccetedCount + $payerRejectionCount;
												?>
                                                <td class="font600 text-right" style="border-right: 1px solid #f0f0f0">
													<span>
                                                        @if($pTotal != 0)
                                                        {{ round((($payerAccetedCount / $pTotal ) * 100 ), 2) }}%
                                                        @else
                                                        0%
                                                        @endif
                                                    </span>
												</td> 
                                                <td class="font600 text-right" style="border-right: 1px solid #f0f0f0">
                                                    @if($pTotal != 0)
                                                    {{ round((($payerRejectionCount / $pTotal ) * 100 ), 2) }}%
                                                    @else
                                                    0%
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td class="font600 text-center" style="border-right: 1px solid #f0f0f0"><span class="med-green"></span></td> 
                                                <td class="font600 text-center med-green" style="border-right: 1px solid #f0f0f0"></td> 
                                                <td class="font600 text-center  med-green" style="border-right: 1px solid #f0f0f0"></td> 
                                                <td class="font600 text-center  med-green" style="border-right: 1px solid #f0f0f0"></td>
                                                <td class="font600 text-center " style="border-right: 1px solid #f0f0f0"><span></span></td> 
                                                <td class="font600 text-center " style="border-right: 1px solid #f0f0f0"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div><!-- Outstanding box-body Ends -->
                        </div><!-- Outstanding box Ends -->
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->         
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
            type: 'MSColumn2D',
            renderAt: 'chart-p',
            width: '100%',
            height: '250',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisName": "",
                    "yAxisName": "",
                    "numberPrefix": "",
                    "theme": "fint1",
                    "palette": "1",
                    "numVisiblePlot": "12",
                    "bgColor": "#ffffff",
                    "palettecolors": "#008ee4,#f8bd19,#e45b5b,#8c9ba8",
                    "bgAlpha": "1",
                    "canvasBgColor": "#ffffff", //this 2 lines for graph bg color
                    "canvasBgAlpha": "0",
                    "chartTopMargin": "35",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                    "showAlternateHGridColor": "0",
                    "subcaptionFontBold": "0",
                    "subcaptionFontSize": "14",
                    "showAxisLines": "0"
                },
                "categories": [{
					"category": [<?php echo $dataArr->month; ?>]
				}],
                "dataset": [{
					"seriesname": "Submitted",
					"data": [<?php echo $dataArr->submitted_lable; ?>]
				},
				{
					"seriesname": "Rejected",
					"data": [<?php echo $dataArr->rejected_lable; ?>]
				}]
            }
        });
        revenueChart.render();
    });
</script>
@endpush