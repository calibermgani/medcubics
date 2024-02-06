<?php
/*
@if(!empty($provider_name_1)) 
<div class="@if(!empty($provider_name_2)) col-lg-6 col-md-6 @else col-lg-12 col-md-12  @endif col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
    <div class="box no-shadow"><!-- Primary Location Box Starts -->
        <div class="box-block-header with-border">
            <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Provider: {{$provider_name_1}}</h3>
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
                                    <div id="chart-container-11"></div> 
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- Primary Location box Ends-->
</div><!--  Left side Content Ends -->  
@endif
@if(!empty($provider_name_2)) 
<div class="@if(!empty($provider_name_1)) col-lg-6 col-md-6 @else col-lg-12 col-md-12  @endif col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
    <div class="box no-shadow"><!-- Primary Location Box Starts -->
        <div class="box-block-header with-border">
            <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Provider: {{$provider_name_2}}</h3>
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
                                    <div id="chart-container-12"></div> 
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- Primary Location box Ends-->
</div><!--  Left side Content Ends -->  
@endif
*/
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" id="js_ajax_part">
    <div class="box box-view no-shadow">
        <div class="box-header-view">
            <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
            <div class="pull-right">
                <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
            </div>
        </div>
        <div class="box-body bg-white border-radius-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                    <div class="margin-b-15">Provider Summary by Location</div>
                </h3>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        <?php $i=1; ?>
                        @if(isset($searchBy) && !empty($searchBy))
                        @foreach($searchBy as $header_name => $header_val)
                        <span class="med-green">
                        {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i< count((array)$searchBy)) | @endif
                            <?php $i++; ?>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
                <div class="box no-shadow"><!-- Primary Location Box Starts -->
                    @if(!empty($providers)) 
                    @foreach($providers as $key => $value) 
                        <div class="box-block-header with-border">
                            <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Rendering: {{explode('_',$key)[0]}}</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div><!-- /.box-header -->

                        <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                            <table class="popup-table-border  table-separate table m-b-m-1 margin-t-10">
                                <tr style="border-bottom: 2px solid #00877f;">
                                    <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">By Location</th>
                                    <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Charges</th>
                                    <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Payments</th>
                                    <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Adjustments </th>                                            
                                    <th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Outstanding</th>                                           
                                    <th class="text-right font600 hide" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Expected Payment</th>
                                </tr>
                                <?php $tot_charges = $tot_payments = $tot_adjustments = $tot_ar = $tot_expected = 0	?>	
                                @foreach($value as $k=>$v)
                                <?php
									$tot_charges += $v['total_charge'];
									$tot_payments += $v['payments'];
									$tot_adjustments += $v['adjustment'];
									$tot_ar += $v['total_ar'];
									$tot_expected += $v['expected'];
                                ?>
                                <tr>
                                    <td class="" style="line-height: 24px">{{$v['facility_name']}}</td>
                                    <td class="text-right">${{\App\Http\Helpers\Helpers::priceFormat($v['total_charge'])}}</td>
                                    <td class="text-right">	${!! \App\Http\Helpers\Helpers::priceFormat($v['payments']) !!}</td>
                                    <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['adjustment']) !!}</td>
                                    <td class="text-right">${!! \App\Http\Helpers\Helpers::priceFormat($v['total_ar']) !!}</td>
                                    <td class="text-right hide">${!! \App\Http\Helpers\Helpers::priceFormat($v['expected']) !!}</td>   
                                </tr>
                                @endforeach
                                <tr class="med-green">
                                        <td class="med-orange font600">Totals</td>
                                        <td class="med-green font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_charges) !!}</td>
                                        <td class="med-green font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_payments) !!}</td>
                                        <td class="med-green font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_adjustments) !!}</td>
                                        <td class="med-green font600 text-right">${!! \App\Http\Helpers\Helpers::priceFormat($tot_ar) !!}</td>
                                        <td class="med-green font600 text-right hide">${!! \App\Http\Helpers\Helpers::priceFormat($tot_expected) !!}</td>   
                                </tr>
                            </table>
                        </div><!-- /.box-body -->

                    @endforeach
                    @else
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
                    @endif
                </div><!-- Primary Location box Ends-->
            </div>
        </div>
    </div>
</div>
@if(!empty($provider_name_1)) 
<script>
    // Provider 1
    FusionCharts.ready(function () {
        var providerOne = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container-11',
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
                        "category": {!! (!empty($chart_1['facility_name']))?json_encode($chart_1['facility_name']):[] !!}
                    }],
                "dataset": [{
                        "seriesname": "Total Charges",
                        "data": {!! (!empty($chart_1['total_charge']))?json_encode($chart_1['total_charge']):[] !!}
                    },
                    {
                        "seriesname": "Total Payments",
                        "data": {!! (!empty($chart_1['payments']))?json_encode($chart_1['payments']):[] !!}
                    },
                    {
                        "seriesname": "Total Adjustments",
                        "data": {!! (!empty($chart_1['adjustment']))?json_encode($chart_1['adjustment']):[] !!} 
                    },
                    {
                        "seriesname": "Total Outstanding",
                        "data": {!! (!empty($chart_1['total_ar']))?json_encode($chart_1['total_ar']):[] !!} 
                    }
                ]
            }
        });

        providerOne.render();
    });
</script>
@endif
@if(!empty($provider_name_2)) 
<script>
    // Provider 2
    FusionCharts.ready(function () {
        var providerTwo = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container-12',
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
                        "category": {!! (!empty($chart_2['facility_name']))?json_encode($chart_2['facility_name']):[] !!}
                    }],
                "dataset": [{
                        "seriesname": "Total Charges",
                        "data": {!! (!empty($chart_2['total_charge']))?json_encode($chart_2['total_charge']):[] !!}
                    },
                    {
                        "seriesname": "Total Payments",
                        "data": {!! (!empty($chart_2['payments']))?json_encode($chart_2['payments']):[] !!}
                    },
                    {
                        "seriesname": "Total Adjustments",
                        "data": {!! (!empty($chart_2['adjustment']))?json_encode($chart_2['adjustment']):[] !!} 
                    },
                    {
                        "seriesname": "Total Outstanding",
                        "data": {!! (!empty($chart_2['total_ar']))?json_encode($chart_2['total_ar']):[] !!} 
                    }
                ]
            }
        });

        providerTwo.render();
    });
</script>
@endif