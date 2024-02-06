@extends('admin')
@php  $id = Route::getCurrentRoute()->parameter('id'); @endphp
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop"></i> AR Management <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Summary </span></small>
        </h1>
        <ol class="breadcrumb">

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"
                                                                  data-toggle="tooltip"
                                                                  data-original-title="Print"></i></a></li-->
            <li class="hide"><a href=""><i class="fa fa-share-square-o" data-placement="bottom" data-toggle="tooltip"
                              data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')

@stop

@section('practice')
@if(Request::segment(2) != 'myfollowup')
	<?php $module = "armanagement"; ?>
	@include('armanagement/armanagement/tabs')
@endif
@php  $id = Route::getCurrentRoute()->parameter('id'); @endphp
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php 
		$activetab = 'payments_list';
        $routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-20 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'payments_list') active @endif"><a href=""><i
                        class="fa fa-cog i-font-tabs"></i> Summary</a></li>
            <li class="@if($activetab == 'list') active @endif"><a
                    href="{{ url('armanagement/armanagementlist') }}"><i class="fa fa-navicon i-font-tabs"></i>
                    Lists</a></li>
        </ul>
    </div>
    <!-- Tab Ends -->
    <div class="box no-border no-shadow bg-transparent">

        <div class="box-body margin-t-10 table-responsive no-padding">

            <div class="col-lg-12 no-padding ">
                <div class="box-body form-horizontal  no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding bg-white">
                          
                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 p-r-0">
                                <div class="box no-bottom no-shadow bg-transparent" style="border-radius: 4px;">
                                    <div class="box-body no-bottom p-b-0 p-t-0 p-r-0">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="box no-shadow no-border bg-transparent no-bottom">

                                                <div class="box-body no-b-t  dashboard-table p-b-0 p-t-0">
                                                    <div id="chart-container">AR Summary</div>
                                                </div><!-- /.box-body -->
                                            </div><!-- /.box -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-r-0 ">
                                <div class="box no-bottom no-shadow bg-transparent" style="border-radius: 4px;">
                                    <div class="box-body no-bottom p-b-0 p-t-0 p-r-0">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                            <div class="box no-shadow no-border bg-transparent no-bottom">

                                                <div class="box-body no-b-t  dashboard-table p-b-0 p-t-0 p-r-0">
                                                    <div id="chart-payments1">Payment Summary</div>
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
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <!-- Demo Financials Red Alerts  Starts -->

                </div><!-- Demo Financials Red Alerts  Ends Full 1st row -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-1 margin-t-10">
                        <h4 class="med-darkgray no-bottom"><i class="fa fa-bars i-font-tabs font16 med-orange"></i>
                            Aging</h4>
                    </div>


                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 p-b-5 margin-t-m-10">
                        <!-- Outstanding Col Starts -->
                        <div class="box box-view no-shadow no-border-radius no-border bg-transparent no-bottom p-b-12">
                            <!-- Outstanding Box Starts -->

                            <div class="box-body table-responsive"><!-- Outstanding Box-body Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">


                                    <table class="popup-table-border  table-separate table m-b-m-1">
                                        <thead>
                                            <tr>
                                                <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0; "></th>
                                                    <?php
													   // $aging_data = (array) $aging_data;
														$patient_aging_data_value = (array)$aging_data->patient;
														$insurance_aging_data_value = (array)$aging_data->insurance;
														$oustanding_aging_data = (array)$aging_data->oustanding;
														$total_patient_due = 0;
														$total_insurance_due = 0;
														$total_due = 0;
														$total_percent = 0;
														$total_due_percent = 0;
                                                    ?>                                               
                                                <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">Unbilled</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">0-30</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">31-60</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">61-90</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">91-120</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">121-150</th>
                                                    <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8; border-right: 5px solid #f0f0f0"
                                                    colspan="2">>150</th>
                                                <th class="font600 med-green text-center line-height-24"
                                                    style="background: #96dcd8;">Total
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="font600 bg-white line-height-26 text-center"
                                                    style="border-right: 5px solid #f0f0f0"><span
                                                        class="med-green"></span></td>
                                                <td class="font600 text-center bg-white line-height-26"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white line-height-26"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 bg-white text-center"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 1px solid #CDF7FC"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center bg-white"
                                                    style="border-right: 5px solid #f0f0f0"><span class="med-green"> Value</span>
                                                </td>
                                                <td class="font600 bg-white text-center" style=""><span
                                                        class="med-green"> </span></td>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                          
                                                <td class="font600 bg-white line-height-30 text-center"
                                                    style="border-right: 5px solid #f0f0f0;"><span class="med-green"> Patient AR</span>
                                                </td>
                                                <td class="font600 text-center bg-white line-height-30" style="border-right: 1px solid #CDF7FC">
                                                    <span> NA</span></td>
                                                <td class="font600 text-right bg-white line-height-30"
                                                    style="border-right: 5px solid #f0f0f0;">
                                                    <span>0 </span>
                                                </td>
                                                <?php  
												   // unset($aging_data_patient->claim_count);
												   // $patient_aging_data_value = (array)$patient_aging_data_value;
												  // $$patient_aging_data_value = (array)$patient_aging_data_value;
												?>
                                                @foreach($patient_aging_data_value as $key =>$aging_data_patient)
                                                    <?php
														$claim_count = @$aging_data_patient[0]->claim_count;
													?>
                                                    <td class="font600 text-center bg-white line-height-30" style="border-right: 1px solid #CDF7FC"> <span> {!!@$claim_count!!} </span></td>
                                                    <td class="font600 text-right bg-white line-height-30" style="border-right: 5px solid #f0f0f0;">
                                                        <span> {!!App\Http\Helpers\Helpers::priceFormat(@$aging_data_patient[0]->patient_balance)!!} </span>
                                                    </td>
													<?php 
														$total_patient_due = @$total_patient_due + $aging_data_patient[0]->patient_balance; 
													?>
                                                @endforeach
													<td class="font600 bg-white text-right" style="">{!!App\Http\Helpers\Helpers::priceFormat(@$total_patient_due)!!} 
													</td>
                                            </tr>
                                            <tr>
                                                <td class="font600 bg-white line-height-30 text-center"
                                                    style="border-right: 5px solid #f0f0f0; "><span class="med-green"> Insurance</span>
                                                </td>
                                                @foreach($insurance_aging_data_value as $key =>$aging_data_insurance)
                                                <?php                                               
													$claim_count_ins = @$aging_data_insurance[0]->claim_insurance_count;
													unset($aging_data_insurance->claim_count);
                                                ?>
                                                <td class="font600 text-center bg-white line-height-30" style="border-right: 1px solid #CDF7FC">
                                                    <span> {!!@$claim_count_ins!!} </span></td>
                                                <td class="font600 text-right bg-white line-height-30"
                                                    style="border-right: 5px solid #f0f0f0">
													<span> {!!App\Http\Helpers\Helpers::priceFormat(@$aging_data_insurance[0]->insurance_balance)!!}  </span>
                                                </td>
                                                <?php                                                
													$total_insurance_due = $total_insurance_due + $aging_data_insurance[0]->insurance_balance; 
												?>
                                                @endforeach
                                                <td class="font600 bg-white text-right"
                                                    style=""> {!!App\Http\Helpers\Helpers::priceFormat(@$total_insurance_due)!!}  </td>
											</tr>
											<tr>
                                                <td class="font600 bg-white  line-height-30 text-center"
                                                    style="border-right: 5px solid #f0f0f0;"><span class="med-orange"> Outstanding AR</span>
                                                </td>
                                                @foreach($oustanding_aging_data as $key =>$aging_data_total)
                                                <td  class="font600 text-center bg-white line-height-30">{{$aging_data_total[0]}}</td>
                                                <td class="font600 text-right bg-white med-orange line-height-30"
                                                    style="border-right: 5px solid #f0f0f0"> {!!App\Http\Helpers\Helpers::priceFormat(@$aging_data_total[1])!!}</td>
                                                <?php $total_due = $total_due + $aging_data_total[1]; ?>
                                                @endforeach
                                                <td class="med-orange font600 bg-white text-right"
                                                    style="">{!!App\Http\Helpers\Helpers::priceFormat(@$total_due)!!}</td>
                                            </tr>

                                            <tr>
                                                <td class="font600 bg-white line-height-30 text-center"
                                                    style="border-right: 5px solid #f0f0f0;">
														<span class="med-green"> %</span>
												</td>
                                                @foreach($oustanding_aging_data as $key =>$aging_data_total)
                                                <?php
													$total_due_percent = ($aging_data_total[1] != 0) ? $aging_data_total[1] / $total_due : 0;
													$total_percent = $total_due_percent + $total_percent;
                                                ?>
                                                
                                                <td colspan="2" class="font600 text-center bg-white line-height-30"
                                                    style="border-right: 5px solid #f0f0f0;"><span> {!!round($total_due_percent*100)!!}
                                                        %</span></td>
                                                @endforeach
                                                <td class="font600 text-center bg-white line-height-30" style="">
													<span> {!!round($total_percent*100)!!} %</span>
												</td>
                                            </tr>


                                        </tbody>
                                    </table>

                                </div>
                            </div><!-- Outstanding box-body Ends -->
                        </div><!-- Outstanding box Ends -->
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Financial Red Alert Dates Starts -->
                        <!-- Outstanding Col Ends -->


                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10">
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 no-padding">
                                <div class="box-header no-border border-radius-4" style="background: transparent">
                                    <h4 class="med-darkgray no-bottom"><i
                                            class="fa fa-bars i-font-tabs font16 med-orange"></i> AR Days</h4>
                                    <div class="box-tools pull-right font14  med-darkgray"
                                         style="margin-right: -20px;">

                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding">
                                <div class="box-header no-border border-radius-4" style="background: transparent">
                                    <h4 class="med-darkgray no-bottom"><i
                                            class="fa fa-bars i-font-tabs font16 med-orange"></i> Insurance
                                        Aging</h4>
                                    <div class="box-tools pull-right font14 med-darkgray"
                                         style="margin-right: -20px;">

                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding">
                                <div class="box-header no-border border-radius-4" style="background: transparent">
                                    <h4 class="med-darkgray no-bottom">
										<i class="fa fa-bars i-font-tabs font16 med-orange"></i> Patient Aging
                                    </h4>
                                    <div class="box-tools pull-right font14 med-darkgray"
                                         style="margin-right: -20px;">

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <div class="box no-bottom no-shadow"
                                 style="border:1px solid #dedddd; border-radius: 4px;">
                                <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                                        <div class="box no-shadow no-border no-bottom">

                                            <div class="box-body no-b-t dashboard-table">
                                                <div id="chart-3">AR Days</div>
                                            </div><!-- /.box-body -->
                                        </div><!-- /.box -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                            <div class="box no-bottom no-shadow"
                                 style="border:1px solid #dedddd; border-radius: 4px;">
                                <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                                        <div class="box no-shadow no-border no-bottom">

                                            <div class="box-body no-b-t  dashboard-table">
                                                <div id="chart-insaging">Insurance Aging</div>
                                            </div><!-- /.box-body -->
                                        </div><!-- /.box -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                            <div class="box no-bottom no-shadow"
                                 style="border:1px solid #dedddd; border-radius: 4px;">
                                <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                                        <div class="box no-shadow no-border no-bottom">
                                            <div class="box-body no-b-t  dashboard-table ">
                                                <div id="chart-pataging">Patient Aging</div>
                                            </div><!-- /.box-body -->
                                        </div><!-- /.box -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0 margin-t-20">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 margin-t-10">
                                <h4 class="med-darkgray no-bottom margin-t-5">
									<i class="fa fa-bars i-font-tabs font16 med-orange"></i> Insurance wise Summary
								</h4>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="box-body table-responsive p-t-0">
                                    <table class="popup-table-border  table-separate table m-b-m-1">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="border-right: 1px solid #fff ">
                                                    Insurance
                                                </th>
                                                <th class="text-center" colspan="2"
                                                    style="border-right: 1px solid #fff">Unbilled
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
                                                <td class="font600 text-center line-height-26"
                                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                                </td>
                                                <td class="font600 text-center line-height-26"
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
                                                <td class="font600 bg-white text-center"><span
                                                        class="med-green"> </span></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
												$insurance_aging_data = (array) $insurance_aging_data;
												//dd($insurance_aging_data);
												$total_arr = [];
												$main_total = 0
                                            ?>
                                            @foreach($insurance_aging_data as $key => $insurances)
                                            <?php 
												$insurances_val = (array) $insurances; $count;
                                                if(isset($insurances_val['Unbilled']{0}->insurance_id) && !empty($insurances_val['Unbilled']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['Unbilled']{0}->insurance_id;
                                                elseif(isset($insurances_val['0-30']{0}->insurance_id) && !empty($insurances_val['0-30']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['0-30']{0}->insurance_id;
                                                elseif(isset($insurances_val['31-60']{0}->insurance_id) && !empty($insurances_val['31-60']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['31-60']{0}->insurance_id;
                                                elseif(isset($insurances_val['61-90']{0}->insurance_id) && !empty($insurances_val['61-90']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['61-90']{0}->insurance_id;
                                                elseif(isset($insurances_val['91-120']{0}->insurance_id) && !empty($insurances_val['91-120']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['91-120']{0}->insurance_id;                                                
                                                elseif(isset($insurances_val['121-150']{0}->insurance_id) && !empty($insurances_val['121-150']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['121-150']{0}->insurance_id;
                                                elseif(isset($insurances_val['>150']{0}->insurance_id) && !empty($insurances_val['>150']{0}->insurance_id))
                                                    $insuranceId = $insurances_val['>150']{0}->insurance_id;    
                                                else
                                                    $insuranceId = '';
                                             ?>

                                            <tr>
                                                <?php $total = 0; ?>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                                        class="med-green"><a href="{{url('/armanagement/armanagementlist?search=yes&insurance_id=')}}{{ $insuranceId }}" target="_blank"> {{$key}}</a></span>
                                                </td>
                                                <?php $insurances_val = (array) $insurances; ?>
                                                @foreach($insurances_val as $key => $insurance)
                                                <?php
													if (array_key_exists($key, $total_arr)) {
														array_push($total_arr[$key], @$insurance[0]->insurance_balance);
														$total_arr['count'][$key] = $total_arr['count'][$key] + @$insurance[0]->claim_insurance_count;
													} else {
														$total_arr[$key] = [ @$insurance[0]->insurance_balance];
														$total_arr['count'][$key] = @$insurance[0]->claim_insurance_count;
													}
													$claim_count_single = (@$insurance[0]->claim_insurance_count >0)?@$insurance[0]->claim_insurance_count:0;
                                                ?>
                                                <td class="font600 text-center bg-white">{{@$claim_count_single}}</td>
                                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$insurance[0]->insurance_balance)!!}</td>
                                                <?php
													$total+= @$insurance[0]->insurance_balance;
													$main_total+= @$insurance[0]->insurance_balance;
                                                ?>
                                                @endforeach
                                                <td class="font600 text-right bg-white line-height-26"><span>
                                                    {!!App\Http\Helpers\Helpers::priceFormat(@$total)!!}</span>
												</td>
                                            </tr>
                                            @endforeach
                                            <?php
												$total_sum_insurance = 0;
												$total_percent_cal = 0;
												$total_claim_count = @$total_arr['count'];
												if (!empty($total_arr['count']))
													unset($total_arr['count']);
                                            ?>
                                            <tr>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Total Insurance AR</span>
                                                </td>
                                                <?php //dd($total_arr);?>
                                                @if(!empty($total_arr))
                                                @foreach($total_arr as $key=>$tot_arr)
                                                <?php
													$value = 0;
													$total_sum = array_sum($tot_arr);
													$total_sum_insurance+= $total_sum;
													$value = ($total_sum != 0) ? $total_sum / $main_total : 0;
													$total_ins_percent[$key] = round(($value * 100),2);
													$total_percent_cal = @$total_ins_percent[$key] + $total_percent_cal;
                                                ?>
                                                <?php $claim_count = (@$total_claim_count[$key] >0)?@$total_claim_count[$key]:'0';?>
                                                <td class="font600 text-center bg-white med-orange">{{$claim_count}}</td>
                                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum)!!}</td>
                                                @endforeach
                                               
                                                <td class="font600 text-right bg-white med-orange">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum_insurance)!!}</td>                                               
                                                @endif
                                            </tr>
<?php //dd($total_ins_percent);   ?>
                                            <tr>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                                        class="med-green"> %</span></td>

                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['Unbilled']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['0-30']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['31-60']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['61-90']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['91-120']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['121-150']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['>150']!!}
                                                    %
                                                </td>
                                                <?php $total_percent_cal = ($total_percent_cal>100)?100:$total_percent_cal;?>
                                                <td class="font600 text-center bg-white med-green">{{round(@$total_percent_cal)}}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div>
                        </div>


                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 margin-t-10">
                                <h4 class="med-darkgray no-bottom margin-t-5"><i
                                        class="fa fa-bars i-font-tabs font16 med-orange"></i> Patient wise
                                    Summary</h4>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="box-body table-responsive p-t-0">
                                    <table class="popup-table-border  table-separate table m-b-m-1">
                                        <tr>
                                            <th class="text-center" style="border-right: 1px solid #fff">Bill Cycle</th>
                                            <th class="text-center" style="border-right: 1px solid #fff">Patient</th>
                                            <th class="text-center" colspan="2" style="border-right: 1px solid #fff">0-30</th>
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
                                        
                                        <?php
											$patient_aging_data = (array) $patient_aging_data;                                       
											$total_patient_aging_arr = [];
											$total_claim_count = [];
											$main_total_patient_aging = 0; 
                                            $total_patient_count = 0;   
                                            $patient_count_tot = 0; 
                                            $patient_value = json_decode(json_encode($patient_aging_data), true);
                                            $patient_count = array_sum(array_column($patient_value, 'patientCount'));
                                        ?>
                                        @foreach($patient_aging_data as $key => $patient_data)
                                        <tr>
                                            <?php 
												$aging =  $key;                                               
												$total_data = 0;  ?>
												<td class="font600 bg-white line-height-26 "><span class="med-green"> {{$key}}</span></td>
												<?php $patient_data = (array) $patient_data; 
												$patient_count = 0;
												$patient_count = $patient_data['patientCount'];
												$patient_count_tot += $patient_data['patientCount'];
												unset($patient_data['patientCount']);
											?>
                                            <td class="font600 text-center bg-white">{{$patient_count}}</td>
                                            @foreach($patient_data as $key => $patient_bill_cycle)
                                            <?php
												if (array_key_exists($key, $total_patient_aging_arr)) {
													array_push($total_patient_aging_arr[$key], @$patient_bill_cycle[0]->patient_balance);
												} else {
													$total_patient_aging_arr[$key] = [ @$patient_bill_cycle[0]->patient_balance];
												}  
												if (array_key_exists($key, $total_claim_count)) {
													array_push($total_claim_count[$key], @$patient_bill_cycle[0]->claim_count);
												} else {
													$total_claim_count[$key] = [ @$patient_bill_cycle[0]->claim_count];
												}                                            
                                            ?>
                                            <td class="font600 text-center bg-white">{{@$patient_bill_cycle[0]->claim_count}}</td>
                                            <td class="font600 text-right bg-white">{!!App\Http\Helpers\Helpers::priceFormat(@$patient_bill_cycle[0]->patient_balance)!!}</td>
                                            <?php
												$total_data+= @$patient_bill_cycle[0]->patient_balance;
												$main_total_patient_aging+= @$patient_bill_cycle[0]->patient_balance;
                                            ?>
                                            @endforeach
                                            <td class="font600 text-right bg-white line-height-26"><span>
                                                {!!App\Http\Helpers\Helpers::priceFormat(@$total_data)!!}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
											$total_sum_patient = 0;
											$total_pat_percent_cal = 0;
											$total_pat_percent = [];                                       
                                        ?>
                                        <tr>
                                            <td class="font600 bg-white line-height-26"><span class="med-orange"> Total Patient AR</span>
                                            </td>
                                            <td class="font600 text-center bg-white med-orange">{{$patient_count_tot}}</td>
                                            @foreach($total_patient_aging_arr as $key=>$tot_arr)
                                            <?php                                            
												$value = 0;
												$total_sum = array_sum($tot_arr);
												$total_claim = array_sum($total_claim_count[$key]);
												$total_sum_patient+= $total_sum;
												$value = ($total_sum != 0) ? $total_sum / $main_total_patient_aging : 0;
												$total_pat_percent[$key] = round(($value * 100),2);
												$total_pat_percent_cal = $total_pat_percent[$key] + $total_pat_percent_cal;
                                            ?>
                                            <td class="font600 text-center bg-white med-orange">{{$total_claim}}</td>
                                            <td class="font600 text-right bg-white med-orange">
                                                {!!App\Http\Helpers\Helpers::priceFormat(@$total_sum)!!}</td>
                                            @endforeach
                                            <td class="font600 text-right bg-white med-orange">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum_patient)!!}</td>
                                        </tr>
                                        <tr>
                                            <td class="font600 bg-white line-height-26"><span
                                                    class="med-green"> %</span></td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white med-green">{!!$total_pat_percent['0-30']!!}
                                                %
                                            </td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white med-green">{!!$total_pat_percent['31-60']!!}
                                                %
                                            </td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white med-green">{!!$total_pat_percent['61-90']!!}
                                                %
                                            </td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white">{!!$total_pat_percent['91-120']!!}
                                                %
                                            </td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white med-green">{!!$total_pat_percent['121-150']!!}
                                                %
                                            </td>
                                            <td class="font600 text-center bg-white"></td>
                                            <td class="font600 text-center bg-white med-green">{!!$total_pat_percent['>150']!!}
                                                %
                                            </td>

                                            <td class="font600 text-center bg-white med-green">{{round($total_pat_percent_cal)}}%</td>
                                        </tr>
                                    </table>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-t-0 p-r-0">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 margin-t-10">
                                <h4 class="med-darkgray no-bottom margin-t-5"><i
                                        class="fa fa-bars i-font-tabs font16 med-orange"></i> Status wise
                                    Summary</h4>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="box-body table-responsive p-t-0">
                                    <table class="popup-table-border  table-separate table m-b-m-1">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="border-right: 1px solid #fff ">Status
                                                </th>
                                                <th class="text-center" colspan="2"
                                                    style="border-right: 1px solid #fff">Unbilled
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
                                                <td class="font600 bg-white text-center"><span
                                                        class="med-green"> </span></td>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
												$claims_status_balances = (array) $claims_status_balances;
												$total_status_arr = [];
												$main_status_total = 0;
                                            ?>
                                            @foreach($claims_status_balances as $keys => $claims_balances)
                                            <tr>
                                                <?php $total_data = 0; ?>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                                        class="med-green"><a href="{{url('/armanagement/armanagementlist?search=yes&status=')}}{{ $keys }}" target="_blank"> {{$keys}}</a></span></td>
                                                <?php $claims_balance = (array) $claims_balances; ?>
                                                @foreach($claims_balance as $key => $claims_bal)
                                                <?php
													if (array_key_exists($key, $total_status_arr)) {
														array_push($total_status_arr[$key], @$claims_bal[0]->total_ar);
														if(is_numeric($total_status_arr['count']) && is_numeric($claims_bal[0]->claim_count))
														$total_status_arr['count'][$key] = $total_status_arr['count'][$key] + @$claims_bal[0]->claim_count;
													} else {
														$total_status_arr[$key] = [ @$claims_bal[0]->total_ar];
														$total_status_arr['count'][$key] = @$claims_bal[0]->claim_count;
													}
													$claim_count_sin  = (@$claims_bal[0]->claim_count>0)?@$claims_bal[0]->claim_count:0;
													if($key=='Unbilled' && $keys=='Patient')
														$claim_count_sin  = (@$claims_bal[0]->claim_count=='NA')?@$claims_bal[0]->claim_count:'na';
                                                ?>
                                                <td class="font600 text-center bg-white">{{@$claim_count_sin}}</td>
                                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_bal[0]->total_ar)!!}</td>
                                                <?php
													$total_data+= @$claims_bal[0]->total_ar;
													$main_status_total+= @$claims_bal[0]->total_ar;
                                                ?>
                                                @endforeach
                                                <td class="font600 text-right bg-white line-height-26"><span>
													{!!App\Http\Helpers\Helpers::priceFormat(@$total_data)!!} </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                            <?php
												$total_status_sum = 0;
												$total_percent_status_cal = 0;
												$total_claim_count = $total_status_arr['count'];
												unset($total_status_arr['count']);
                                            ?>
                                            <tr>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Outstanding AR</span>
                                                </td>
                                                @foreach($total_status_arr as $key=>$tot_arr)
                                                <?php
													$value = 0;
													$total_sum = array_sum($tot_arr);
													$total_status_sum+= $total_sum;
													$value = ($total_sum != 0) ? $total_sum / $main_status_total : 0;
													$total_status_percent[$key] = round(($value * 100),2);
													$total_percent_status_cal = $total_status_percent[$key] + $total_percent_status_cal;
													$claim_count_total = (@$total_claim_count[$key]>0)?@$total_claim_count[$key]:0;
                                                ?>
                                                <td class="font600 text-center bg-white med-orange">{{@$claim_count_total}}</td>
                                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum)!!}</td>
                                                @endforeach
                                                <td class="font600 text-right bg-white med-orange">{!!App\Http\Helpers\Helpers::priceFormat(@$total_status_sum)!!}</td>
                                            </tr>
<?php //dd($total_ins_percent);   ?>
                                            <tr>
                                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                                        class="med-green"> %</span></td>
                                                <!--td class="font600 text-right bg-white"></td-->
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['Unbilled']!!}
                                                    %
                                                </td>
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['0-30']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['31-60']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['61-90']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['91-120']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['121-150']!!}
                                                    %
                                                </td>
                                                
                                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['>150']!!}
                                                    %
                                                </td>

                                                <td class="font600 text-center bg-white med-green">{{round($total_percent_status_cal)}}
                                                    %
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
    
<?php
$current_year_line_chart = $insuranceLineChart->current_year;
//$last_year_line_chart = $insuranceLineChart->last_year;
?>
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
        var salesChart = new FusionCharts({
            type: 'scrollline2d',
            dataFormat: 'json',
            renderAt: 'chart-payments1',
            width: '100%',
            height: '230',
            dataSource: {
                "chart": {
                    caption: "",
                    subCaption: "",
                    xAxisName: "",
                    yAxisName: "",
                    showValues: "0",
                    numberPrefix: "$",
                    labelFontColor: "#999696",
                    baseFontColor: "#999696",
                    showBorder: "0",
                    showShadow: "0",
                    showLabels: "1",
                    enableSmartLabels: "0",
                    enableMultiSlicing: "0",
                    toolTipColor: "#ffffff",
                    toolTipBorderThickness: "0",
                    toolTipBgColor: "#000000",
                    toolTipBgAlpha: "85",
                    toolTipBorderRadius: "4",
                    toolTipPadding: "10",
                    showLegend: "1",
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
                    legendborderalpha: "1",
                    bgColor: "#ffffff",
                    paletteColors: "#008ee4,#ff780b,#fea500",
                    baseFontSize: "12",
                    baseFont: "'Open Sans', sans-serif",
                    yAxisNameFontSize: "14",
                    yAxisNameFontColor: "#00877f",
                    xAxisNameFontSize: "14",
                    xAxisNameFontColor: "#00877f",
                    showCanvasBorder: "0",
                    showAxisLines: "0",
                    showAlternateHGridColor: "0",
                    divlineAlpha: "20",
                    divlineThickness: "1",
                    divLineIsDashed: "1",
                    divLineDashLen: "1",
                    divLineGapLen: "1",
                    lineThickness: "3",
                    flatScrollBars: "1",
                    scrollheight: "10",
                    numVisiblePlot: "6",
                    showHoverEffect: "1",
                    chartTopMargin: "20",
                    chartBottomMargin: "0",
                    chartLeftMargin: "20",
                    chartRighttMargin: "20"
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
                            {"value": "<?= (@$current_year_line_chart->Primary != 0) ? $current_year_line_chart->Primary : "0.00"; ?>"},
                            {"value": "<?= (@$current_year_line_chart->Secondary != 0) ? $current_year_line_chart->Secondary : "0.00"; ?>"},
                            {"value": "<?= (@$current_year_line_chart->Tertiary != 0) ? $current_year_line_chart->Tertiary : "0.00"; ?>"},
                            {"value": "<?= (@$current_year_line_chart->Self != 0) ? $current_year_line_chart->Self : "0.00"; ?>"}

                        ]
                    },                   
                ]
            }
        }).render();
    });


  /*  FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'msstackedcolumn2d',
            renderAt: 'chart-container',
            width: '100%',
            height: '250',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "xaxisname": "",
                    "yaxisname": "",
                    "paletteColors": "#5598c3,#2785c3,#31cc77,#1aaf5d,#f45b00",
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
                    "legendShadow": "1",
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
                "categories": <?php //echo $insurance_chart_label; ?>,
                "dataset": <?php //echo $insurance_chart_data; ?>

            }
        }).render();
    });*/
    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'msstackedcolumn2d',
            renderAt: 'chart-container',
            width: '100%',
            height: '250',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "xaxisname": "",
                    "yaxisname": "",
                    "paletteColors": "#5598c3,#2785c3,#31cc77,#1aaf5d,#f45b00",
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
                    "legendShadow": "1",
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
                "categories": {
                    "category": <?= $insurance_chart_label;?>
                },
                 "dataset": [
                {
                    "dataset": [
                        {
                            "seriesname": "Billed",
                            "data": <?= $charge;?>
                        },
                       
                    ]
                },
                {
                    "dataset": [
                        {
                            "seriesname": "Collections",
                           "data": <?= $payment;?>
                        },
                        
                    ]
                },
                {
                    "dataset": [
                        {
                            "seriesname": "Outstanding",
                            "data": <?= $balance;?>
                        },
                        
                    ]
                }
            ]

            }
        }).render();
    });

    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie3d',
            renderAt: 'chart-insaging',
            width: '100%',
            height: '250',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "palette": "20",
                    "animation": "1",
                    "formatnumberscale": "1",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "pieslicedepth": "30",
                    "startingangle": "165",
                    "baseFont": "'Open Sans', sans-serif",
                    "palettecolors": "#f8bd19,#f95757,#38a3e5,#f59547,#5b6b73,#b9dd4c,#4ebeac,#e13375,#374b56",
                    "decimals": "2",
                    "numberprefix": "$",
                    "numbersuffix": "",
                    "toolTipColor": "#ffffff",
                    "showPercentInTooltip": "1",
                    "showValues": "0",
                    "plotToolText": "<p>$$value</p>",
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
                "data": [{"label": "0-30", "value": "<?= $total_insurance_aging_chart->{'0-30'}; ?>"},
                    {"label": "31-60", "value": "<?= $total_insurance_aging_chart->{'31-60'}; ?>"},
                    {"label": "61-90", "value": "<?= $total_insurance_aging_chart->{'61-90'}; ?>"},
                    {"label": "91-120", "value": "<?= $total_insurance_aging_chart->{'91-120'}; ?>"},
                    {"label": "121-150", "value": "<?= $total_insurance_aging_chart->{'121-150'}; ?>"},
                    {"label": ">150", "value": "<?= @$total_insurance_aging_chart->{'>150'}; ?>"}
                ]
            }
        });
      <?php      
        foreach($total_insurance_aging_chart as $key => $chart){
          $chart += $chart;
        } if($chart == '0'){
        ?>
    //  revenueChart.configure("ChartNoDataText":"No Aging Data Found.", "InvalidXMLText"  : "Please validate data");
         revenueChart.setXMLData("<chart></chart>"); // For MR-1437 message show
         revenueChart.configure("ChartNoDataText", "No Aging Data Found");
        <?php } ?>
        revenueChart.render();
    });

    FusionCharts.ready(function () {
        var revenueChart = new FusionCharts({
            type: 'pie3d',
            renderAt: 'chart-pataging',
            width: '100%',
            height: '250',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "palette": "20",
                    "animation": "1",
                    "formatnumberscale": "1",
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "pieslicedepth": "30",
                    "startingangle": "165",
                    "baseFont": "'Open Sans', sans-serif",
                    "palettecolors": "#f8bd19,#f95757,#38a3e5,#f59547,#5b6b73,#b9dd4c,#4ebeac,#e13375,#374b56",
                    "decimals": "2",
                    "numberprefix": "$",
                    "numbersuffix": "",
                    "toolTipColor": "#ffffff",
                    "showPercentInTooltip": "1",
                    "showValues": "0",
                    "plotToolText": "<p>$$value</p>",
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
                "data": [{"label": "0-30", "value": "<?php if(!empty($total_patient_aging_chart->{'0-30'})) { echo $total_patient_aging_chart->{'0-30'}; } else { echo 0; }  ?>"},
                    {"label": "31-60", "value": "<?php if(!empty($total_patient_aging_chart->{'31-60'})) { echo $total_patient_aging_chart->{'31-60'}; } else { echo 0; } ?>"},
                    {"label": "61-90", "value": "<?php if(!empty($total_patient_aging_chart->{'61-90'})) { echo $total_patient_aging_chart->{'61-90'}; } else { echo 0; } ?>"},
                    {"label": "91-120", "value": "<?php if(!empty($total_patient_aging_chart->{'91-120'})) { echo $total_patient_aging_chart->{'91-120'}; } else { echo 0; } ?>"},
                    {"label": "121-150", "value": "<?php if(!empty($total_patient_aging_chart->{'121-150'})) { echo $total_patient_aging_chart->{'121-150'}; } else { echo 0; } ?>"},
                    {"label": "151-180", "value": "<?php if(!empty($total_patient_aging_chart->{'>150'})) { echo $total_patient_aging_chart->{'>150'}; } else { echo 0; } ?>"}
                ]
            }
        });

        <?php

        foreach($total_patient_aging_chart as $key => $chart){
          $chart += $chart;
        } if($chart == '0'){?>
	//	revenueChart.configure("ChartNoDataText":"No Aging Data Found.", "InvalidXMLText"  : "Please validate data");
         revenueChart.setXMLData("<chart></chart>"); // For MR-1437 message show
         revenueChart.configure("ChartNoDataText", "No Aging Data Found");
        <?php } ?>
        revenueChart.render();
    });

    FusionCharts.ready(function () {
        var fuelWidget = new FusionCharts({
            type: 'cylinder',
            dataFormat: 'json',
            id: 'fuelMeter',
            renderAt: 'chart-3',
            width: '100%',
            height: '250',
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
                    "numberprefix": "",
                    "numbersuffix": "",
                    baseFont: "'Open Sans', sans-serif",
                    "lowerLimitDisplay": "0",
                    //"upperLimitDisplay": " 150 days",
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
                "value": "<?php echo $ar_days; ?>"
            }
        }).render();
    });

</script>

@endpush