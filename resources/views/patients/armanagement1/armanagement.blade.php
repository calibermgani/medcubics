@extends('admin')
<?php $id = Route::current()->parameters['id']; ?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-wheelchair"></i> AR Management </small>
        </h1>
        <ol class="breadcrumb">

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
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
    <?php $activetab = 'payments_list'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-cog i-font-tabs"></i> Financials</a></li>           	                      	           
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/armanagement1/list') }}" ><i class="fa fa-navicon i-font-tabs"></i> Lists</a></li>           	                      	           
        </ul>
    </div>
    <!-- Tab Ends -->


    <div class="box no-border no-shadow">

        <div class="box-body margin-t-10 table-responsive">


            <div class="row no-padding"><!-- Row Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5"><!-- Demo Financials Red Alerts  Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Demographics Starts -->
                        <div class=" col-lg-3 col-md-3 p-r-0"><!-- Financial Col Starts -->
                            <div class="box box-view-border no-shadow no-border-radius no-b-r"><!-- Financial Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-green font13"> Financials</strong>                       
                                </div><!-- /.box-header Ends  -->
                                <div class="box-body table-responsive"><!-- Financial Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">

                                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Created</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding">
                                                <h6><span class=" bg-date"> 11-16-2015</span></h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Submitted</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 01-23-2016</span></h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Last Submitted</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 03-16-2016</span></h6>
                                            </div>
                                        </div>

                                        
                                        
                                        
                                    </div>
                                </div><!-- Financial box-body Ends -->
                            </div><!-- Financial box Ends -->
                        </div><!-- Financial Col Ends -->

                        <div class="col-lg-3 col-md-3 p-r-0 p-l-0"><!-- Red Alerts Starts -->
                            <div class="box box-view-border no-shadow no-border-radius"><!-- Red Alert Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-green font13"> Red Alerts</strong>                                  
                                </div><!-- /.box-header ends  -->
                                <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
                                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Billed</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6>120</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Unbilled</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="">0</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Unapplied</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6>0</h6>
                                            </div>
                                        </div>

                                       
                                    </div>
                                </div><!-- Red Alert box-body ends -->
                            </div><!-- Red Alert box ends -->
                        </div><!-- Red Alert Ends -->

                        <div class=" col-lg-3 col-md-3 p-r-0 p-l-0"><!-- Financial Col Starts -->
                            <div class="box box-view-border no-shadow no-border-radius no-b-r no-b-l"><!-- Financial Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-green font13"> Financials</strong>                       
                                </div><!-- /.box-header Ends  -->
                                <div class="box-body table-responsive"><!-- Financial Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">

                                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Ins Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding">
                                                <h6>163</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Pat Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6>234</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Returned chk</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-orange">CH352352623</h6>
                                            </div>
                                        </div>                                                                
                                        
                                    </div>
                                </div><!-- Financial box-body Ends -->
                            </div><!-- Financial box Ends -->
                        </div><!-- Financial Col Ends -->

                        <div class="col-lg-3 col-md-3 p-l-0"><!-- Red Alerts Starts -->
                            <div class="box box-view-border no-shadow no-border-radius"><!-- Red Alert Box Starts -->
                                <div class="box-header-view-white m-b-m-10 no-border-radius"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-green font13"> Red Alerts</strong>                                  
                                </div><!-- /.box-header ends  -->
                                <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Cancelled Appt</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6>3</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Auth No</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="">- Nil  -</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">D.O.I</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 03-16-2016</span></h6>
                                            </div>
                                        </div>                                                                              
                                    </div>
                                </div><!-- Red Alert box-body ends -->
                            </div><!-- Red Alert box ends -->
                        </div><!-- Red Alert Ends -->

                    </div><!-- Demographics Ends -->


                </div><!-- Demo Financials Red Alerts  Ends Full 1st row -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Financial Red Alert Dates Starts -->

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 margin-t-m-13 "><!-- Outstanding Col Starts -->                                                                             
                        <div class="box box-view no-shadow no-border-radius no-border"><!-- Outstanding Box Starts -->
                          
                            <div class="box-body table-responsive"><!-- Outstanding Box-body Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    <table id="" class="table table-borderless" style="border-collapse:separate; border: 1px solid #00877f; border-radius: 4px;">	
                                        <thead>
                                            <tr>
                                                <th style="background: #00877f; font-weight: 600">Outstanding</th>
                                                <th style="background: #00877f;">Unbilled</th>
                                                <th style="background: #00877f;">0-30</th>
                                                <th style="background: #00877f;">31-60</th>
                                                <th style="background: #00877f;">61-90</th>
                                                <th style="background: #00877f;">91-120</th>
                                                <th style="background: #00877f;">>121</th>
                                                <th style="background: #00877f;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody >



                                            <tr>                                
                                                <td style="background: #00877f; color:#fff">Patient</td>
                                                <td>$ 56.00</td>
                                                <td>$ 4.00</td>
                                                <td>$ 7.00</td>
                                                <td>$ 0.00</td>
                                                <td>$ 0.00</td>
                                                <td>$ 0.00</td>
                                                <td>$ 67.00</td>
                                            </tr>

                                            <tr>                                
                                                <td style="background: #00877f; color:#fff">Insurance</td>
                                                <td>$ 5.00</td>
                                                <td>$ 47.00</td>
                                                <td>$ 13.00</td>
                                                <td>$ 10.00</td>
                                                <td>$ 1.00</td>
                                                <td>$ 0.00</td>
                                                <td>$ 76.00</td>                             
                                            </tr>

                                            <tr>                                
                                                <td style="background: #00877f; color:#fff">Outstanding</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 61.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 51.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 20.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 10.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 1.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 0.00</td>
                                                <td style=" color:#00877f; font-weight: 600;">$ 143.00</td>                             
                                            </tr>
                                            <tr>
                                                <td style="background: #00877f; color:#fff">Percentage</td>
                                                <td class="med-orange font600">27 %</td>
                                                <td class="med-orange font600">34 %</td>
                                                <td class="med-orange font600">74 %</td>
                                                <td class="med-orange font600">7 %</td>
                                                <td class="med-orange font600">0.4 %</td>
                                                <td class="med-orange font600">0 %</td>
                                                <td class="med-orange font600" style="border-radius:0px 0px 4px 0px;">46 %</td>                                
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                            </div><!-- Outstanding box-body Ends -->
                        </div><!-- Outstanding box Ends -->
                    </div><!-- Outstanding Col Ends -->    
                </div><!-- Financial Red Alert Dates Ends -->


                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20"><!-- Reports Div Starts -->
                    <div class="box box-info no-shadow no-border">

                        <div class="box-body table-responsive">       


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-r-0 margin-t-m-20">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
                                    <div class="box box-view-border no-shadow ">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Area Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                    
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive no-padding">
                                            <div class="chart" id="revenue-chart" style="height: 200px;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="box box-view-border no-shadow">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Line Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive no-padding">
                                            <div class="chart" id="line-chart" style="height: 200px;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <!-- DONUT CHART -->
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 hide">
                                    <div class="box box-view-border no-shadow">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Donut Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                   
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive">
                                            <div class="chart" id="sales-chart" style="height: 180px; position: relative;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-r-0">
                                    <div class="box box-view-border no-shadow">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Bar Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                   
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive">
                                            <div class="chart" id="bar-chart" style="height: 180px; position: relative;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
                                    <div class="box box-view-border no-shadow">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Bar Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                   
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive">
                                            <div class="chart" id="line-chart1" style="height: 180px; position: relative;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="box box-view-border no-shadow">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Bar Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                   
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive">
                                            <div class="chart" id="bar-chart1" style="height: 180px; position: relative;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-l-0">
                                    <div class="box box-view-border no-shadow ">
                                        <div class="box-header-view-white">
                                            <h3 class="box-title">Area Chart</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                    
                                            </div>
                                        </div>
                                        <div class="box-body chart-responsive no-padding">
                                            <div class="chart" id="revenue-chart1" style="height: 200px;"></div>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                
                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- Reports div Ends -->

                
                
                <!--- Hided Delete after design confirmed -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20 hide"><!-- Demographics Starts -->
                        <div class=" col-lg-3 col-md-3 p-r-0"><!-- Financial Col Starts -->
                            <div class="box box-view no-shadow no-border-radius no-b-r" style="border-color: #ebe0c6"><!-- Financial Box Starts -->
                                <div class="box-header-view bg-aqua m-b-m-10 no-border-radius" style="border-bottom: 1px solid #ebe0c6"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-orange font13"> Financials</strong>                       
                                </div><!-- /.box-header Ends  -->
                                <div class="box-body table-responsive"><!-- Financial Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Created</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding">
                                                <h6><span class=" bg-date"> 11-16-2015</span></h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Submitted</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 01-23-2016</span></h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Last Submitted</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 03-16-2016</span></h6>
                                            </div>
                                        </div>

                                        
                                        
                                        
                                    </div>
                                </div><!-- Financial box-body Ends -->
                            </div><!-- Financial box Ends -->
                        </div><!-- Financial Col Ends -->

                        <div class="col-lg-3 col-md-3 p-r-0 p-l-0"><!-- Red Alerts Starts -->
                            <div class="box box-view no-shadow no-border-radius" style="border-color: #ebe0c6"><!-- Red Alert Box Starts -->
                                <div class="box-header-view bg-aqua m-b-m-10 no-border-radius" style="border-bottom: 1px solid #ebe0c6"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-orange font13"> Red Alerts</strong>                                  
                                </div><!-- /.box-header ends  -->
                                <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Billed</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6>120</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Unbilled</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="">0</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green">Unapplied</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6>0</h6>
                                            </div>
                                        </div>

                                       
                                    </div>
                                </div><!-- Red Alert box-body ends -->
                            </div><!-- Red Alert box ends -->
                        </div><!-- Red Alert Ends -->

                        <div class=" col-lg-3 col-md-3 p-r-0 p-l-0"><!-- Financial Col Starts -->
                            <div class="box box-view no-shadow no-border-radius no-b-r no-b-l" style="border-color: #ebe0c6"><!-- Financial Box Starts -->
                                <div class="box-header-view bg-aqua m-b-m-10 no-border-radius" style="border-bottom: 1px solid #ebe0c6"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-orange font13"> Financials</strong>                       
                                </div><!-- /.box-header Ends  -->
                                <div class="box-body table-responsive"><!-- Financial Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green">Ins Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding">
                                                <h6>163</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding" style="margin-top: -10px;">
                                                <h6 class="med-green">Pat Payment</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding" style="margin-top: -10px;">
                                                <h6>234</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 no-padding" style="margin-top: -10px;">
                                                <h6 class="med-green">Returned chk</h6>
                                            </div>
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 no-padding" style="margin-top: -10px;">
                                                <h6 class="med-orange">CH352352623</h6>
                                            </div>
                                        </div>                                                                                
                                        
                                    </div>
                                </div><!-- Financial box-body Ends -->
                            </div><!-- Financial box Ends -->
                        </div><!-- Financial Col Ends -->

                        <div class="col-lg-3 col-md-3 p-l-0"><!-- Red Alerts Starts -->
                            <div class="box box-view no-shadow no-border-radius" style="border-color: #ebe0c6"><!-- Red Alert Box Starts -->
                                <div class="box-header-view bg-aqua m-b-m-10 no-border-radius" style="border-bottom: 1px solid #ebe0c6"><!-- Box Header Starts -->
                                    <i class="livicon" data-name="responsive-menu"></i> <strong class="med-orange font13"> Red Alerts</strong>                                  
                                </div><!-- /.box-header ends  -->
                                <div class="box-body table-responsive"><!-- Red Alert Box Body Starts -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6 class="med-green" style="">Cancelled Appt</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
                                                <h6>3</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green" style="">Authorization</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="">- Nil  -</h6>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6 class="med-green" style="">D.O.I</h6>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding margin-t-m-10">
                                                <h6><span class=" bg-date"> 03-16-2016</span></h6>
                                            </div>
                                        </div>                                                                              
                                    </div>
                                </div><!-- Red Alert box-body ends -->
                            </div><!-- Red Alert box ends -->
                        </div><!-- Red Alert Ends -->

                    </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->         
    </div>



    @stop