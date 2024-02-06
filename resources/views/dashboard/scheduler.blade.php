@extends('admin')
@section('pageTitle', 'Scheduler Dashboard')

@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Scheduler Analytics</small>
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
            <h3 class="margin-b-5 med-darkgray">Scheduler Analytics</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -10px;">

            </div>
        </div>
    </div>
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
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

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 7px; padding-top:20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-10" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 pull-right" style="color: #87a801;">143 </span></p>
                                    <p style="padding-bottom: 10px; font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> last month</p>                                    
                                </div>                                
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <p class="" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 pull-right" style="color: #e45b5b;">127 </span></p>
                                    <p style="font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Billed Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> last month</p>                                    
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 no-padding" style="border-right: 3px solid #f8f6f6">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 7px;padding-top:20px; border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-10" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 pull-right" style="color: #5cb0c3;">99 </span></p>
                                    <p style="padding-bottom: 10px; font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> last month</p>                                    
                                </div>                                
                            </div>                            

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <p class="" style="font-size: 40px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600 pull-right" style="color: #e0c034;">183 </span></p>
                                    <p style="font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-down med-red" style="font-size: 14px;"></i> <span class="font600 med-red">12%</span> last month</p>                                    
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>


            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px; ">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-5" style="font-size: 30px;padding-top: 25px;"><span class="font600" style="color: #697d94 ">27%</span></p>
                                    <p style="font-size: 13px"><i class="fa fa-arrow-circle-up" style="color: #87a801; font-size: 13px;"></i> from last month</p>
                                    <p class="margin-b-5" style="font-size: 30px;padding-top: 30px;"><span class="font600" style="color: #697d94 ">3%</span></p>
                                    <p class="font13" style="margin-bottom: 0px;"><i class="fa fa-arrow-circle-down" style="color: #eb3d3d;font-size: 13px;"></i> Clean Claims</p>
                                </div>                                
                            </div>                            
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">
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

            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px; padding-top:20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-10" style="font-size: 48px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600" style="color: #87a801;">143 </span></p>
                                    <p style="padding-bottom: 10px; font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> from last month</p>
                                </div>                                
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <p class="" style="font-size: 48px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600" style="color: #e45b5b;">127 </span></p>
                                    <p style="font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Billed Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> from last month</p>                                    
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px;padding-top:20px; border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    <p class="margin-b-10" style="font-size: 48px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600" style="color: #5cb0c3;">99 </span></p>
                                    <p style="padding-bottom: 10px; font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801;font-size: 14px;"></i> <span style="color: #87a801" class="font600">12%</span> from last month</p>
                                </div>                                
                            </div>                            

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 25px;">
                                <div class="col-lg-12 no-padding">
                                    <p class="" style="font-size: 48px;float: left;min-width: 21%;margin-right: 10px;"><span class="font600" style="color: #e0c034;">183 </span></p>
                                    <p style="font-size: 13px;margin-top: -10px;line-height: 22px; "> <span class="font14">Unbilled Charges</span><br><span class="med-darkgray font600">$2324.00</span>, <i class="fa fa-arrow-circle-down med-red" style="font-size: 14px;"></i> <span class="font600 med-red">12%</span> from last month</p>                                    
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <h3 class="margin-b-5 med-darkgray">Performance Management</h3>
            </div>
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="width:180px;overflow:hidden ">
                    <select type="text" class="form-control select2">
                        <option>-- Sort by Facility --</option>
                        <option>Facility 1</option>
                        <option>Facility 2</option>
                    </select>       
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="width:190px;overflow:hidden ">
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

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0"  style="border:1px solid #dedddd; border-radius: 4px;">   
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15" >
                    <div class="box no-shadow no-border no-bottom" >
                        <div class="box-body no-b-t">
                            <center><div id="chart-container" class=" text-center text-centre">FusionCharts will render here</div></center>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
        </div>
    </div>
</div> 


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding" >
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Insurance Wise</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

            </div>
        </div>
    </div>  

    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4" style="background: transparent">
            <h3 class="margin-b-5 med-darkgray">Collections</h3>                           
            <div class="box-tools pull-right font14 margin-t-5 med-darkgray" style="margin-right: -20px;">

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
                        <div id="chart-insurances">Chart will render here</div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
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
                        <div id="chart-payments">Chart will render here</div>
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
                    "palettecolors": "#5cb0c3,#a6bb50,#e45b5b,#e0c034",
                    "showValues": "1",
                    "baseFontSize": "13",
                    "showLegend": "0",
                    "decimals": "1",
                    "plotBorderColor": "#fff",
                    "plotBorderThickness": "0",
                    "showPlotBorder": "0",
                    "chartTopMargin": "22",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    "valueFontSize": "13",
                    "valueFontColor": "#697d94",
                    "toolTipFontSize": "13",
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
            type: 'MSColumn2D',
            renderAt: 'chart-container',
            width: '90%',
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
                            },
                            {
                                "value": "8000"
                            },
                            {
                                "value": "14000"
                            },
                            {
                                "value": "11000"
                            },
                            {
                                "value": "11500"
                            },
                            {
                                "value": "17000"
                            },
                            {
                                "value": "21000"
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
</script>
@endpush