@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Performance Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Month End Performance Summary Report</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('reports/demo/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'reports/charges/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="chargesearchexport" />
                <input type="hidden" name="report_name" value="Charge Analysis Detailed" />
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow yes-border"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border" style="background: #fff;border-bottom: 1px solid #e9ecf3;border-radius: 4px 4px 0px 0px">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title" style="font-size: 18px;line-height: 30px;"> Filters</h2>
                    <div class="box-tools pull-right" style="line-height: 38px">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20 no-border" id="js-address-primary-address"><!-- Box Body Starts -->
                    {!! Form::open(['url'=>'reports/search/charges','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                    @include('layouts.search_fields', ['search_fields'=>$search_fields])
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


        
		
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 margin-t-10">
                <h4 class="med-darkgray no-bottom margin-t-5"><i
                        class="fa fa-bars i-font-tabs font16 med-orange"></i> Outstanding AR - By Location</h4>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="box-body table-responsive p-t-0">
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <thead>
                            <tr>
                                <th class="text-center" style="border-right: 1px solid #fff ">Status
                                </th>

                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">0-30
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">31-60
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">61-90
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">91-120
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">121-150
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">>150
                                </th>
                                <th class="text-center" style=" ">Total</th>
                            </tr>
                            <tr>
                                <td class="font600 bg-white line-height-26 text-center"
                                    style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"></span></td>

                                <td class="font600 text-center  line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center "
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 bg-white text-center"><span class="med-green"> </span></td>
							</tr>
                        </thead>

                        <tbody>

                            <tr>
										

                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-green"><a href="" target="_blank"> Facility 1</a></span></td>
                                <td class="font600 text-center bg-white">5</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$200.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>3</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>3</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>2</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>7</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$375.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>20</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$3,301.00</td>
                                <td class="font600 text-right bg-white">$6,966.00</td>
                            </tr>       

                            <tr>
												

                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-green"><a href="" target="_blank"> Facility 2</a></span></td>
                                <td class="font600 text-center bg-white">6</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$575.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>14</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$1,768.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>33</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$3,239.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>11</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$1,370.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>14</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$2,580.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>78</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$9,242.00	</td>
                                <td class="font600 text-right bg-white">$32,560.00</td>
                            </tr>       

                            <tr>
								

                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-green"><a href="" target="_blank"> Facility 3</a></span></td>
                                <td class="font600 text-center bg-white">4</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$120.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>2</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>3</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$200.00	</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>2</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00		</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>1</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$100.00</td>                                
                                <td class="font600 text-center bg-white line-height-26"><span>12</span></td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">$200.00	</td>
                                <td class="font600 text-right bg-white">$2,590.00</td>
                            </tr>       
                            <tr>
											

                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Total</span></td>                                                
                                <td class="font600 text-center bg-white med-orange">15</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$895.00	</td>                                                
                                <td class="font600 text-center bg-white med-orange">19</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$1,968.00</td>                                                
                                <td class="font600 text-center bg-white med-orange">39</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$3,539.00</td>                                                
                                <td class="font600 text-center bg-white med-orange">15</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$1,570.00</td>                                                
                                <td class="font600 text-center bg-white med-orange">22</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$3,155.00	</td>                                                
                                <td class="font600 text-center bg-white med-orange">110</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$12,843.00	</td>                                                
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">$42,116.00</td>                                                
                            </tr>


                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Insurance Claims - Paid By Location</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->

                    <h4>Facility 1</h4>
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Payer</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Billed</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Paid</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Difference </th>                                            
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Outstanding </th>                                           
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">%</th>
                        </tr>

                        <tr>
                            <td class="" style="line-height: 24px;">Medicare</td>
                            <td class="text-center">120	</td>
                            <td class="text-center">65</td>
                            <td class="text-center">	55</td>
                            <td class="text-center">	$14802.00</td>
                            <td class="text-right">	32%</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Medicaid</td>
                            <td class="text-center">360</td>
                            <td class="text-center">	180</td>
                            <td class="text-center">	180</td>
                            <td class="text-center">	$27714.00</td>
                            <td class="text-right">	43%</td>  
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">BCBS</td>
                            <td class="text-center">560</td>
                            <td class="text-center">	440</td>
                            <td class="text-center">	120	</td>
                            <td class="text-center">$42516.00</td>
                            <td class="text-right">	43%</td>  
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Aetna</td>
                            <td class="text-center">84	</td>
                            <td class="text-center">54	</td>
                            <td class="text-center">30	</td>
                            <td class="text-center">$1471.00	</td>
                            <td class="text-right">14%</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">UHC</td>
                            <td class="text-center">30</td>
                            <td class="text-center">	0</td>
                            <td class="text-center">	30</td>
                            <td class="text-center">	$1000.00	</td>
                            <td class="text-right">3%</td>   
                        </tr>
						<tr>
                            <td class="" style="line-height: 24px;">Humana</td>
                            <td class="text-center">25	</td>
                            <td class="text-center">	10	</td>
                            <td class="text-center">	15	</td>
                            <td class="text-center">	$120.00		</td>
                            <td class="text-right">2%</td>   
                        </tr>
						
                        <tr class="med-green">
                            <td class="med-orange font600">Totals</td>
                            <td class="font600 med-orange text-center">1179</td>
                            <td class="font600 med-orange text-center">	749	</td>
                            <td class="font600 med-orange text-center">430	</td>
                            <td class="font600 med-orange text-center">$87623.00	</td>
                            <td class="font600 med-orange text-right">100.00%</td>    
                        </tr>
                    </table>


                    <h4 style="margin-top: 20px">Facility 2</h4>
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Payer</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Billed</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims Paid</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Difference </th>                                            
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Outstanding</th>                                           
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">%</th>
                        </tr>

                        <tr>
                            <td class="" style="line-height: 24px;">Medicare</td>
                            <td class="text-center">45</td>
                            <td class="text-center">	20	</td>
                            <td class="text-center">25	</td>
                            <td class="text-center">$560.00	</td>
                            <td class="text-right">4%</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Medicaid</td>
                            <td class="text-center">20	</td>
                            <td class="text-center">15	</td>
                            <td class="text-center">5	</td>
                            <td class="text-center">$1471.00	</td>
                            <td class="text-right">10%</td>  
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">BCBS</td>
                            <td class="text-center">65</td>
                            <td class="text-center">	15	</td>
                            <td class="text-center">50	</td>
                            <td class="text-center">$1400.00	</td>
                            <td class="text-right">9%</td>  
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Aetna</td>
                            <td class="text-center">30	</td>
                            <td class="text-center">12</td>
                            <td class="text-center">	18	</td>
                            <td class="text-center">$1370.00	</td>
                            <td class="text-right">9% </td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">UHC</td>
                            <td class="text-center">30	</td>
                            <td class="text-center">15	</td>
                            <td class="text-center">15	</td>
                            <td class="text-center">$1840.00	</td>
                            <td class="text-right">12%</td>   
                        </tr>
						<tr>
                            <td class="" style="line-height: 24px;">Humana</td>
                            <td class="text-center">25		</td>
                            <td class="text-center">10		</td>
                            <td class="text-center">15		</td>
                            <td class="text-center">$8160.00	</td>
                            <td class="text-right">55%</td>   
                        </tr>
                        <tr class="med-green">
                            <td class="med-orange font600">Totals</td>
                            <td class="font600 med-orange text-center">215	</td>
                            <td class="font600 med-orange text-center">87	</td>
                            <td class="font600 med-orange text-center">128	</td>
                            <td class="font600 med-orange text-center">$87623.00	</td>
                            <td class="font600 med-orange text-right">100.00%</td>    
                        </tr>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Location Status Summary</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="box-body table-responsive p-t-0 no-padding">
                                <table class="popup-table-border  table-separate table m-b-m-1 margin-t-10">
                                    <tr>
                                        <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Location</th>
                                        <th class="font600 text-center" colspan="4" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;border-right: 1px solid #a4ede9;border-left: 1px solid #a4ede9">Total</th>
                                        <th class="font600 text-center" colspan="2" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Avg Collections per</th>                                        
                                    </tr>

                                    <tr>
                                        <td class=""></td>
                                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Visits/Claims</span></td>
                                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Days Worked</span></td>
                                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Charges</span></td>
                                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Payment</span></td>
                                        <td class="font600 text-center  line-height-26"style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Patient/Appt</span></td>
                                        <td class="font600 text-center  line-height-26"style="background: #dbfaf8;"><span class="med-green"> Day</span></td>                                        
                                    </tr>
                                    <tr>
                                        <td class="" style="line-height: 24px">Facility 1</td>
                                        <td class="text-center">1179</td>
                                        <td class="text-center">15</td>
                                        <td class="text-center">$23694.00</td>
                                        <td class="text-right">$5,329.00</td>
                                        <td class="text-center">$4.51</td> 
                                        <td class="text-right">$355.26</td> 
                                    </tr>
                                    <tr>
                                        <td class="" style="line-height: 24px">Facility 2</td>
                                        <td class="text-center">215</td>
                                        <td class="text-center">20</td>
                                        <td class="text-center">$9580.00</td>
                                        <td class="text-right">$6525.00</td>
                                        <td class="text-center">$30.34</td> 
                                        <td class="text-right">$326.25</td> 
                                    </tr>
									<tr>
                                        <td class="" style="line-height: 24px">Facility 3</td>
                                        <td class="text-center">15</td>
                                        <td class="text-center">15</td>
                                        <td class="text-center">$1000.00</td>
                                        <td class="text-right">$600.00</td>
                                        <td class="text-center">$40</td> 
                                        <td class="text-right">$40</td> 
                                    </tr>
                                    
                                    <tr>
                                        <td class="font600 med-orange">Total</td>
                                        <td class="text-center font600 med-orange">1409</td>
                                        <td class="text-center font600 med-orange">50</td>
                                        <td class="text-center font600 med-orange">$32,274.00</td>
                                        <td class="text-right font600 med-orange">$12454.00</td>
                                        <td class="text-center font600 med-orange">$75</td> 
                                        <td class="text-right font600 med-orange">$721</td> 
                                    </tr>
                                    
                                </table>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div>
		
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Provider : Gloria Robert</h3>
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
							<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Expected Payment</th>
						</tr>
						
						<tr>
							<td class="" style="line-height: 24px">Facility 1</td>
							<td class="text-right">$1,210.00</td>
							<td class="text-right">	$1,051.00	</td>
							<td class="text-right">$150.00	</td>
							<td class="text-right">$49.00	</td>
							<td class="text-right">$49.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 2</td>
							<td class="text-right">$5,730.00</td>
							<td class="text-right">	$884.00</td>
							<td class="text-right">	$205.00</td>
							<td class="text-right">	$4,716.00</td>
							<td class="text-right">	$4,716.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 3</td>
							<td class="text-right">$2,640.00</td>
							<td class="text-right">	$2,195.00</td>
							<td class="text-right">	$100.00</td>
							<td class="text-right">	$2,280.00	</td>
							<td class="text-right">$2,280.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 4</td>
							<td class="text-right">$2,285.00</td>
							<td class="text-right">	$975.00</td>
							<td class="text-right">	$300.00	</td>
							<td class="text-right">$1,080.00</td>
							<td class="text-right">	$1,080.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 5</td>
							<td class="text-right">$22,919.00</td>
							<td class="text-right">	$12,594.00</td>
							<td class="text-right">	$2,863.00</td>
							<td class="text-right">	$7,409.00</td>
							<td class="text-right">	$7,409.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 6</td>
							<td class="text-right">$1,840.00</td>
							<td class="text-right">	$605.00</td>
							<td class="text-right">	$140.00</td>
							<td class="text-right">	$1,125.00</td>
							<td class="text-right">	$1,125.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 7</td>
							<td class="text-right">$1,640.00</td>
							<td class="text-right">	$640.00</td>
							<td class="text-right">	$175.00</td>
							<td class="text-right">	$975.00</td>
							<td class="text-right">	$975.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 8</td>
							<td class="text-right">$1,450.00</td>
							<td class="text-right">	$195.00</td>
							<td class="text-right">	$20.00</td>
							<td class="text-right">	$1,225.00</td>
							<td class="text-right">	$1,225.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 9</td>
							<td class="text-right">$8,120.00</td>
							<td class="text-right">	$4,945.00</td>
							<td class="text-right">	$1,134.00</td>
							<td class="text-right">	$2,036.00</td>
							<td class="text-right">	$2,036.00</td>   
						</tr>
						<tr>
							<td class="" style="line-height: 24px">Facility 10</td>
							<td class="text-right">$1,490.00</td>
							<td class="text-right">	$385.00</td>
							<td class="text-right">	$90.00</td>
							<td class="text-right">	$1,025.00</td>
							<td class="text-right">	$1,025.00</td>   
						</tr>
						<tr class="med-green">
							<td class="med-orange font600">Totals</td>
							<td class="med-green font600 text-right">$49,324.00</td>
							<td class="med-green font600 text-right">	$24,469.00</td>
							<td class="med-green font600 text-right">	$5,177.00</td>
							<td class="med-green font600 text-right">	$21,920.00</td>
							<td class="med-green font600 text-right">	$21,920.00</td>   
						</tr>
					</table>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->
                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h3 class="box-title"> Provider : John Smith</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">
						
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="box-body table-responsive p-t-0 no-padding">
								<table class="popup-table-border  table-separate table m-b-m-1 margin-t-10">
									<tr style="border-bottom: 2px solid #00877f;">
										<th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">By Location</th>
										<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Charges</th>
										<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Payments</th>
										<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Adjustments </th>                                            
										<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Total Outstanding</th>                                           
										<th class="text-right font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Expected Payment</th>
									</tr>
												
									<tr>
										<td class="" style="line-height: 24px">Facility 1</td>
										<td class="text-right">$2,750.00</td>
										<td class="text-right">	$1,371.00</td>
										<td class="text-right">	$315.00</td>
										<td class="text-right">	$1,204.00</td>
										<td class="text-right">	$1,204.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 2</td>
										<td class="text-right">$18,140.00	</td>
										<td class="text-right">$3,678.00	</td>
										<td class="text-right">$1,684.00</td>
										<td class="text-right">	$13,145.00	</td>
										<td class="text-right">$13,145.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 3</td>
										<td class="text-right">$2,840.00	</td>
										<td class="text-right">$280.00	</td>
										<td class="text-right">$0.00	</td>
										<td class="text-right">$2,560.00	</td>
										<td class="text-right">$2,560.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 4</td>
										<td class="text-right">$2,645.00	</td>
										<td class="text-right">$985.00	</td>
										<td class="text-right">$185.00	</td>
										<td class="text-right">$1,565.00	</td>
										<td class="text-right">$1,565.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 5</td>
										<td class="text-right">$18,765.00	</td>
										<td class="text-right">$10,640.00	</td>
										<td class="text-right">$3,052.00	</td>
										<td class="text-right">$6,440.00	</td>
										<td class="text-right">$6,440.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 6</td>
										<td class="text-right">$115.00	</td>
										<td class="text-right">$70.00	</td>
										<td class="text-right">$35.00	</td>
										<td class="text-right">$10.00	</td>
										<td class="text-right">$10.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 7</td>
										<td class="text-right">$2,300.00	</td>
										<td class="text-right">$1,120.00	</td>
										<td class="text-right">$1,060.00	</td>
										<td class="text-right">$160.00	</td>
										<td class="text-right">$160.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 8</td>
										<td class="text-right">$2,050.00	</td>
										<td class="text-right">$1,105.00	</td>
										<td class="text-right">$200.00	</td>
										<td class="text-right">$950.00	</td>
										<td class="text-right">$950.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 9</td>
										<td class="text-right">7,050.00</td>
										<td class="text-right">$2,190.00</td>
										<td class="text-right">$635.00</td>
										<td class="text-right">$4,515.00</td>
										<td class="text-right">$4,515.00</td>   
									</tr>
									<tr>
										<td class="" style="line-height: 24px">Facility 10</td>
										<td class="text-right">$2,785.00</td>
										<td class="text-right">$1,275.00</td>
										<td class="text-right">$350.00</td>
										<td class="text-right">$1,154.00</td>
										<td class="text-right">$1,154.00</td>   
									</tr>
									<tr class="med-green">
										<td class="med-orange font600">Totals</td>
										<td class="med-green font600 text-right">59,440.00</td>
										<td class="med-green font600 text-right">$22,714.00</td>
										<td class="med-green font600 text-right">$7,516.00</td>
										<td class="med-green font600 text-right">$31,703.00</td>
										<td class="med-green font600 text-right">$31,703.00</td>   
									</tr>
								</table>
							</div><!-- /.box-body -->
						</div>
					</div>
                </div><!-- /.box-body -->
            </div><!-- Primary Location box Ends-->
        </div>
		
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"><!--  Left side Content Starts -->                    
            <div class="box no-shadow"><!-- Primary Location Box Starts -->

                <div class="box-block-header with-border">
                    <i class="fa fa-navicon" data-name="mail"></i> <h2 class="box-title"> Denied/Pending Claims - Status Summary					</h2>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->


                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Reasons Identified</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Action Taken</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                            
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Contracted per Fee Schedule</th>                                           
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Approx. Time line to Expect Payment</th>
                        </tr>

                        <tr>
                            <td class="" style="line-height: 24px;">Denied - No Auth</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">150</td>
                            <td class="text-right">$2000.00</td>
                            <td class="text-right">$1500.00</td>
                            <td class="text-right">21</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Global</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">21</td>
                            <td class="text-right">$451.00</td>
                            <td class="text-right">$410.00</td>
                            <td class="text-right">21</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - No Coverage</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">31</td>
                            <td class="text-right">$1125.00</td>
                            <td class="text-right">$1000.00</td>
                            <td class="text-right">21</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Not Medically Necessary</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">45</td>
                            <td class="text-right">$1819.00</td>
                            <td class="text-right">$1519.00</td>
                            <td class="text-right">21</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Pending - Inprocess</td>
                            <td class="text-center">Inprocess</td>
                            <td class="text-center">12</td>
                            <td class="text-right">$2400.00</td>
                            <td class="text-right">$2100.00</td>
                            <td class="text-right">15</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Pending - Awaiting EOB/ERA</td>
                            <td class="text-center">Pending</td>
                            <td class="text-center">48</td>
                            <td class="text-right">$8900.00</td>
                            <td class="text-right">$7900.00</td>
                            <td class="text-right">15</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Pending - Recently Filed</td>
                            <td class="text-center">Pending</td>
                            <td class="text-center">22</td>
                            <td class="text-right">$560.00</td>
                            <td class="text-right">$510.00</td>
                            <td class="text-right">15</td>   
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Pending - No Response</td>
                            <td class="text-center">Pending</td>
                            <td class="text-center">5</td>
                            <td class="text-right">$156.00</td>
                            <td class="text-right">$120.00</td>
                            <td class="text-right">15</td>   
                        </tr>

                        <tr class="med-green">
                            <td class="med-orange font600">Totals</td>
                            <td class="med-green font600 text-right"></td>
                            <td class="med-green font600 text-center">334</td>
                            <td class="med-green font600 text-right">$17411.00</td>
                            <td class="med-green font600 text-right">$15059.00</td>

                            <td class="med-green font600 text-right"></td>   
                        </tr>
                    </table>
                </div><!-- /.box-body -->
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


                <div class="box-body form-horizontal js-address-class margin-l-10 p-b-20" id="js-address-primary-address"><!-- Box Body Starts -->
                    <table class="popup-table-border  table-separate table m-b-m-1">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f; text-align: left !important;">Coding Denials</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Action Taken</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                                                                        
                        </tr>

                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Unspecified</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">31</td>
                            <td class="text-right">$2000.00</td>                                              
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Procedure inconsistent with Modifier</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">45</td>
                            <td class="text-right">$451.00</td>                                           
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Pre-existing condition</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">12</td>
                            <td class="text-right">$1125.00</td>                                             
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Invalid POS</td>
                            <td class="text-center">Pending</td>
                            <td class="text-center">48</td>
                            <td class="text-right">$1819.00</td>                                             
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Units exceeds maximum limit</td>
                            <td class="text-center">Inprocess</td>
                            <td class="text-center">150</td>
                            <td class="text-right">$2400.00</td>                                            
                        </tr>
                        <tr>
                            <td class="" style="line-height: 24px;">Denied - Medically not necessary</td>
                            <td class="text-center">Resubmitted</td>
                            <td class="text-center">21</td>
                            <td class="text-right">$89000.00</td>                                            
                        </tr>
                        <tr class="med-green">
                            <td class="med-orange font600">Totals</td>
                            <td class="med-green font600 text-right"></td>
                            <td class="med-green font600 text-center">307</td>
                            <td class="med-green font600 text-right">$16695.00</td>

                        </tr>
                    </table>
                </div><!-- /.box-body -->
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
</script>

@endpush