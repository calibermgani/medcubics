@extends('admin')
@section('pageTitle', 'Charge Analytics')

@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Charge Analytics</small>
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
            <h3 class="margin-b-5 med-darkgray">Charge Analytics</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray hide" style="margin-right: -10px;">

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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0" style="border:1px solid #ccc; border-radius: 4px;">   


            <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                    <div class="box no-shadow no-border no-bottom" style="background: transparent">

                        <div class="box-body no-b-t no-padding " >
                            <div id="chart-11"></div> 
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 no-padding"  style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-0">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1 p-l-0 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 p-l-0 p-r-0">
                                <p class="font16" style="margin-bottom: 28px;"><span class="med-orange font600 font16">$1,25,000</span> Billed Charges (YTD)</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #5cb0c3; position: absolute; margin-left: -17px; margin-top: 3px;"></i><span class="font600" style="font-size: 18px;"> $46,000</span>  Pending Charges</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #a6bb50; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> $1163.30</span> Unbilled Charges</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #8c9ba8; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> $2245.00</span> Hold Charges</p>                               
                                <p class="margin-t-22 font14" style="margin-left:17px; margin-bottom: 1px;"><i class="fa fa-circle" style="color: #e45b5b; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 18px;"> $102.00</span> EDI Rejections</p>                               
                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 no-padding" style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px;">
                                <p class="margin-b-20" style="font-size: 40px"><span class="font600" style="color: #87a801">{{$total_charge_percentage}}%</span></p>
                                <p style="padding-bottom: 18px; font-size: 16px"><i class="fa fa-arrow-circle-up" style="color: #87a801; font-size: 18px;"></i> from last month</p>
                                <p class="margin-b-20" style="font-size: 40px;padding-top: 50px;"><span class="font600" style="color: #eb3d3d">{{$clean_claim}}%</span></p>
                                <p class="font16" style="margin-bottom: 0px;"><i class="fa fa-arrow-circle-down" style="color: #eb3d3d;font-size: 18px;"></i> Clean Claims</p>

                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border no-bottom">

                        <div class="box-body no-b-t margin-b-1 no-bottom" >
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
                                            <td style="line-height: 30px; color: #7fa708">Billed</td>
                                            <td  style="font-size: 15px;">3124</td>
                                            <td style="font-size: 15px;" class="text-right">332442.00</td>                                            
                                        </tr>
                                        <tr  style="font-size: 15px;">
                                            <td style="line-height: 30px;color: #eb3d3d;">Pending</td>
                                            <td>866</td>
                                            <td class="text-right">96742.00</td>                                            
                                        </tr>
                                        <tr  style="font-size: 15px;">
                                            <td style="line-height: 30px;color: #008ee4">Unbilled</td>
                                            <td>685</td>
                                            <td class="text-right">144636.00</td>                                            
                                        </tr>
                                        <tr  style="font-size: 15px;">
                                            <td style="line-height: 30px;">Hold</td>
                                            <td>34</td>
                                            <td class="text-right">7574.00</td>                                            
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

<!-- /.New Chart Start -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-0">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0" style="border:1px solid #ccc; border-radius: 4px;">   
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                    <div class="box no-shadow no-border no-bottom" style="background: transparent">
                        <div class="box-body no-b-t no-padding " >
                            <div id="chart-1"></div> 
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <?php            
				$unbilled = $data->Unbilled; 
				$billed = $data->Billed;
				$hold = $data->Hold;
				$rejection = $data->Rejection;
            ?>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <!--div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 7px; padding-top:20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-10" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 unbilled-charges">{{$unbilled->claim_count}} </span></p>
                                    <p style="padding-bottom: 10px; font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($unbilled->total_charge) !!}</span>, 
                                    <i class="fa @if($data->percentage->Unbilled<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i> 
                                    <span class="font600 @if($data->percentage->Unbilled<0) med-orange @else med-green @endif">
                                        {{abs($data->percentage->Unbilled)}}%
                                    </span> last month</p>                                    
                                </div>                                
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <p class="" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 billed-charges">{{$billed->claim_count}} </span></p>
                                    <p style="font-size: 13px;margin-top: -10px;line-height: 22px; margin-right: -1px;"> <span class="font14">Billed Charges</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($billed->total_charge) !!}</span>,                                         
                                        <i class="fa @if($data->percentage->Billed<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i> 
                                    <span class="font600 @if($data->percentage->Billed<0) med-orange @else med-green @endif">
                                        {{abs($data->percentage->Billed)}}%
                                    </span> last month</p>                                                                 
                                </div>                                
                            </div-->
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 7px; padding-top:20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <div class="col-lg-4 p-r-5" style="font-size: 40px;" ><span class="font600 unbilled-charges pull-right" >{{$unbilled->claim_count}}</span></div>
                                    <div class="col-lg-8 no-padding" style=" font-size: 13px;line-height: 22px; margin-top: 6px;"> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($unbilled->total_charge) !!}</span>,
                                        <i class="fa @if($data->percentage->Unbilled<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i>
                                        <span class="font600 @if($data->percentage->Unbilled<0) med-orange @else med-green @endif">
                                            {{abs(round($data->percentage->Unbilled))}}%
                                        </span> last month</div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <div class="col-lg-4 p-r-5" style="font-size: 40px;"><span class="font600 billed-charges pull-right" >{{$billed->claim_count}}</span></div>
                                    <div class="col-lg-8 no-padding" style="font-size: 13px;line-height: 22px; margin-top: 6px;"> <span class="font14">Billed Charges</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($billed->total_charge) !!}</span>,
                                        <i class="fa @if($data->percentage->Billed<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i>
                                        <span class="font600 @if($data->percentage->Billed<0) med-orange @else med-green @endif">
                                            {{abs(round($data->percentage->Billed))}}%
                                        </span> last month
									</div>                                                                 
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 no-padding" style="border-right: 3px solid #f8f6f6">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 7px;padding-top:20px; border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <div class="col-lg-4 p-r-5" style="font-size: 40px;" ><span class="font600 hold-charges pull-right" >{{$hold->claim_count}}</span></div>
                                    <div class="col-lg-8 no-padding" style="font-size: 13px;line-height: 22px; margin-top: 6px;"> <span class="font14">Hold Charges</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($hold->total_charge) !!}</span>,
                                        <i class="fa @if($data->percentage->Hold<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i> 
                                        <span class="font600 @if($data->percentage->Hold<0) med-orange @else med-green @endif">
                                            {{abs(round($data->percentage->Hold))}}%
                                        </span> last month
									</div>
                                </div>                                
                            </div>                            

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <div class="col-lg-4 p-r-5" style="font-size: 40px;" ><span class="font600 rejection-charges pull-right" >{{$rejection->claim_count}}</span></div>
                                    <div class="col-lg-8 no-padding" style="font-size: 13px;line-height: 22px; margin-top: 6px;"> <span class="font14">EDI Rejection</span><br><span class="med-darkgray font600">{!!App\Http\Helpers\Helpers::priceFormat($rejection->total_charge); !!}</span>,
                                        <i class="fa @if($data->percentage->Rejection<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 14px;"></i> 
                                        <span class="font600 @if($data->percentage->Rejection<0) med-orange @else med-green @endif">
                                            {{abs(round($data->percentage->Rejection))}}%
                                        </span> last month
									</div>
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px; ">
                                <div class="col-lg-12 no-padding text-center">
                                    <p class="margin-b-5" style="font-size: 30px;padding-top: 65px;"><span class="font600 @if($clean_claim<0) med-orange @else  med-green @endif">{{$clean_claim}}%</span></p>
                                    <p class="font13" style="margin-bottom: 0px;"><i class="fa @if($clean_claim<0) fa-arrow-circle-down med-orange @else fa-arrow-circle-up med-green @endif" style="color: #eb3d3d;font-size: 13px;"></i> Clean Claims</p>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.New Chart End -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Performance Management</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">
                <div class="col-lg-6">
                    <select type="text" class="form-control select2">
                        <option>-- All Facility --</option>
                        <option>Facility 1</option>
                        <option>Facility 2</option>
                    </select>       
                </div>
                <div class="col-lg-6">
                    <select type="text" class="form-control select2" >
                        <option>-- Sort by Provider --</option>
                        <option>Provider 1</option>
                        <option>Provider 2</option>
                    </select>       
                </div>
            </div>
        </div>

    </div>
    <!-- <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding pull-right">
        <p class="pull-right no-bottom med-darkgray"> <i class="fa fa-calendar margin-r-5"></i> Last Month</p>
    </div> -->
</div>
 
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding hide" >
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Performance Management</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>  

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Responsibility Breakup</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>

    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding" >
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Top 10 CPT</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -10px;">
                <div class="" style="width:80px;overflow:hidden ">
                    <select type="text" class="form-control select2" id="js-line-chart" >                    
                    <option>Year</option>
                    <option>Month</option>
                </select>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.Performance chart hide -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 hide">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-container1">FusionCharts will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>
<!-- /.Insurances chart -->
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0" style="height: 345px;">               
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
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 js-line-chart" id="js-Year">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-payments-year">Yearly Payment Chart</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 js-line-chart" style="display: none;" id="js-Month">
    <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                <div class="box no-shadow no-border">

                    <div class="box-body no-b-t  dashboard-table">           
                        <div id="chart-payments-month">Monthly Payment Chart</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Performance Management </h3>                           
            <div class="box-tools pull-right font14 margin-t-8 med-darkgray" style="margin-right: -10px;">
                <div class="" style="width:170px; overflow:hidden">
                    <!--p class="font600 med-orange font16">$4531.00</p-->              
                    <?php $select = ['billing_provider_id' => 'Billing Provider', 'facility_id' => 'Facility', 'rendering_provider_id' => 'Rendering Provider'] ?>
                    {!! Form::select('provider',$select,null,['class'=>'form-control select2', 'id' => 'js_choose_performance']) !!}
                </div>
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
                            <th class="js_title" style="text-align: left;border-bottom: 3px solid #87cdc7 !important;">Billing Provider</th>
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
                            <tbody class="js_append_performancedata">                                     
                               @include ('dashboard/dashboard_performance')                                
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>                      
        </div>
    </div>
</div>
<?php 
	$current_cpt = json_encode(@$topCPT->Year->current);
	$previous_cpt = json_encode(@$topCPT->Year->previous);
	$label_cpt = json_encode(@$topCPT->Year->label_data);

	$current_cpt_month = json_encode(@$topCPT->Month->current);

	$previous_cpt_month = json_encode(@$topCPT->Month->previous);
	$label_cpt_month= json_encode(@$topCPT->Month->label_data);
?>
@stop
@push('view.scripts')
{!! HTML::script('js/dashboard/fusioncharts.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint1.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint2.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fintinsurance.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.charts.js') !!}
<script>
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'doughnut3d',
            renderAt: 'chart-1',
            width: '100%',
            height: '200',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "animation": "1",
                    "subCaption": "",
                    "numberPrefix": "$",
                    "startingAngle": "54",
                    "showPercentValues": "1",
                    "enableSmartLabels": "1",
                    "showPercentInTooltip": "0",
                    "showLabels": "0",
                    "enableMultiSlicing": "0",
                    "use3DLighting": "1",
                    "palettecolors": "#5cb0c3,#a6bb50,#e0c034,#e45b5b",
                    "showValues": "1",
                    "baseFontSize": "13",
                    "showLegend": "0",
                    "decimals": "2",
                    "plotBorderColor": "#fff",
                    "plotBorderThickness": "0",
                    "showPlotBorder": "0",
                    "chartTopMargin": "22",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    valueFontSize: "13",
                    valueFontColor: "#697d94",
                    toolTipFontSize: "13",
                    //Theme
                    "theme": "fintinsurance"
                },
                "data": [
                    {
                        "label": "Unbilled",
                        "value": "<?= $unbilled->total_charge;?>"
                    },
                    {
                        "label": "Billed",
                        "value": "<?= $billed->total_charge;?>"
                    },
                    {
                        "label": "Hold",
                        "value": "<?= $hold->total_charge;?>"
                    },
                    {
                        "label": "Rejection",
                        "value": "<?= $rejection->total_charge;?>",
                        "isSliced": "0"
                    }
                ]
            }
        }).render();

    });
</script>

<!-- <script>
    FusionCharts.ready(function () {
        var revenueCompChart = new FusionCharts({
            type: 'scrollcombi2d',
            renderAt: 'chart-edi',
            width: '100%',
            height: '400',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisname": "",
                    "yAxisName": "",
                    "numberPrefix": "$",
                    "numVisiblePlot": "12",
                    //Cosmetics
                    "paletteColors": "#55cfea,#fb1010,#f2c500",
                    labelDisplay: "auto",
                    labelFontSize: "13",
                    labelFontColor: "#999696",
                    labelFontBold: "0",
                    baseFontColor: "#999696",
                    baseFontSize: "13",
                    baseFont: "'Open Sans', sans-serif",
                    use3dlighting: "0",
                    showPlotBorder: "0",
                    toolTipColor: "#ffffff",
                    toolTipBorderThickness: "0",
                    toolTipBgColor: "#000000",
                    toolTipBgAlpha: "85",
                    toolTipBorderRadius: "4",
                    toolTipPadding: "10",
                    "chartTopMargin": "22",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    "captionFontSize": "14",
                    "subcaptionFontSize": "14",
                    "subcaptionFontBold": "0",
                    "showBorder": "0",
                    "bgColor": "#ffffff",
                    "showShadow": "0",
                    "canvasBgColor": "#ffffff",
                    "canvasBorderAlpha": "0",
                    "showValues": "0",
                    "divlineAlpha": "20",
                    "divlineColor": "#999999",
                    "divlineThickness": "1",
                    "divLineIsDashed": "1",
                    "divLineDashLen": "1",
                    "divLineGapLen": "1",
                    "usePlotGradientColor": "0",
                    "showplotborder": "0",
                    showLegend: "0",
                    "showXAxisLine": "1",
                    "xAxisLineThickness": "1",
                    "xAxisLineColor": "#999999",
                    "showAlternateHGridColor": "0",
                    "showAlternateVGridColor": "0",
                    "legendBgAlpha": "0",
                    "legendBorderAlpha": "0",
                    "legendShadow": "0",
                    "legendItemFontSize": "10",
                    "legendItemFontColor": "#666666",
                    "scrollheight": "10",
                    "flatScrollBars": "0",
                    "scrollShowButtons": "0",
                    "scrollColor": "#cccccc",
                    "showHoverEffect": "1"
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
                        "seriesName": "Submitted",
                        "data": [
                            {"value": "16000"},
                            {"value": "20000"},
                            {"value": "18000"},
                            {"value": "19000"},
                            {"value": "15000"},
                            {"value": "21000"},
                            {"value": "16000"},
                            {"value": "20000"},
                            {"value": "17000"},
                            {"value": "25000"},
                            {"value": "19000"},
                            {"value": "23000"}


                        ]
                   },
                    {
                        "seriesName": "EDI",
                        "renderAs": "area",
                        "showValues": "0",
                        "data": [
                            {"value": "4000"},
                            {"value": "5000"},
                            {"value": "3000"},
                            {"value": "4000"},
                            {"value": "1000"},
                            {"value": "7000"},
                            {"value": "1000"},
                            {"value": "4000"},
                            {"value": "1000"},
                            {"value": "8000"},
                            {"value": "2000"},
                            {"value": "7000"}
                        ]
                    }
                ]
            }
        });
        revenueCompChart.render();
    });
</script>
 -->

<!-- 
<script>
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie2d',
            renderAt: 'chart-11',
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
                    "palettecolors": "#5cb0c3,#a6bb50,#e45b5b,#8c9ba8",
                    "showValues": "0",
                    "showLegend": "0",
                    "decimals": "1",
                    "plotBorderColor": "#fff",
                    "plotBorderThickness": "1",
                    "showPlotBorder": "1",
                    "chartTopMargin": "22",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    //Theme
                    "theme": "fintinsurance"
                },
                "data": [
                    {
                        "label": "Insurance 1",
                        "value": "105040"
                    },
                    {
                        "label": "Insurance 2",
                        "value": "146330"
                    },
                    {
                        "label": "Insurance 2",
                        "value": "146330"
                    },
                    {
                        "label": "Insurance 5",
                        "value": "9100",
                        "isSliced": "0"
                    }
                ]
            }
        }).render();

    });
</script> -->

<script>
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
                    "numVisiblePlot" : "12",
                    "bgColor": "#ffffff",
                    "palettecolors": "#008ee4,#f8bd19,#e45b5b,#8c9ba8",
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
                        "seriesname": "Patient",
                        "data": <?= $patient_paid;?>
                    },
                    {
                        "seriesname": "Insurance",
                        "data": <?= $insurance_paid;?>
                    }]
            }
        });

        revenueChart.render();
    });
</script>

<!-- <script>
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie3d',
            renderAt: 'chart-ins',
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
         "chartTopMargin": "35",
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
            "label": "Insurance1",
            "value": "100524",
            "issliced": "1"
        },
        {
            "label": "Insurance2",
            "value": "87790",
            "issliced": "1"
        },
        {
            "label": "Insurance3",
            "value": "81898",
            "issliced": "0"
        },
        {
            "label": "Insurance4",
            "value": "76438",
            "issliced": "0"
        },
        {
            "label": "Insurance5",
            "value": "57430",
            "issliced": "0"
        },
        {
            "label": "Insurance6",
            "value": "55091",
            "issliced": "0"
        },
        {
            "label": "Insurance7",
            "value": "43962",
            "issliced": "0"
        },
        {
            "label": "Insurance8",
            "value": "22474",
            "issliced": "0"
        },
        {
            "label": "Insurance9",
            "value": "41637",
            "issliced": "0"
        }
    ]
}
        });

        revenueChart.render();
    });
</script> -->

<!-- <script>
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
                        "label": "Insurance 4",
                        "value": "49100",
                        "isSliced": "0"
                    }
                ]
            }
        }).render();

    });
</script> -->

<script>
    FusionCharts.ready(function () {
        var salesChart = new FusionCharts({
            type: 'scrollline2d',
            dataFormat: 'json',
            renderAt: 'chart-payments-year',
            width: '100%',
            height: '300',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisName": "CPT",
                    "yAxisName": "Units",
                    "showValues": "0",
                    "numberPrefix": "",
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
                    "scrollheight": "5",
                    "numVisiblePlot": "6",
                    "showHoverEffect": "1",
                    "chartTopMargin": "20",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "20",                    
                },
                "categories": [
                    {
                        "category": <?= $label_cpt;?>
                    }
                ],
                "dataset": [
                     {
                        "seriesName": "<?= date("Y", strtotime("previous year"));?>",
                        "data": <?= $previous_cpt;?>
                    },
                    {
                        "seriesName": "<?= date('Y');?>",
                        "data": <?= $current_cpt;?>
                    }
                    
                   
                ]
            }
        }).render();
    });
    
	FusionCharts.ready(function () {
        var salesChart = new FusionCharts({
            type: 'scrollline2d',
            dataFormat: 'json',
            renderAt: 'chart-payments-month',
            width: '100%',
            height: '300',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                     "xAxisName": "CPTs",
                    "yAxisName": "Units",
                    "showValues": "0",
                    "numberPrefix": "",
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
                        "category": <?= $label_cpt_month;?>
                    }
                ],
                "dataset": [
                    {
                        "seriesName": "<?= date("M-y", strtotime("previous month"));?>",
                        "data": <?= $previous_cpt_month;?>
                    },
                    {
                        "seriesName": "<?= date('M-y');;?>",
                        "data": <?= $current_cpt_month;?>
                    }
                    
                    
                ]
            }
        }).render();
    });

    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            renderAt: 'chart-container1',
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
                    "numVisiblePlot": "10",
                    showvalues: "0",
                    "showLegend": "0",
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
                    }
                ]
            }
        });

        revenueChart.render();
    });
</script>
@endpush