@extends('admin')
@section('pageTitle', 'Payment Dashboard')
@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Payment Analytics</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" data-url="" class=""><i class="fa fa-refresh hide" data-placement="bottom"  data-toggle="tooltip" data-original-title="Refresh Data"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>            
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box-header no-border border-radius-4 transparent">
            <div class="font14 margin-t-5 med-darkgray payment-dashboard-multiselect">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group col-sm-2 no-padding">
                    {!! Form::Label('FilterBy', 'Filter By',['class' => 'control-label font600']) !!}
					<select type="text" class="form-control select2" id="FilterBy">
						<option> -- All -- </option>
						<option>Facility</option>
						<option>Billing Provider</option>
						<option>Rendering Provider</option>
					</select>  
                </div>
                <div class="form-group col-sm-2 hide p-r-0">
                    {!! Form::Label('facility_id', 'Facility',['class' => 'control-label font600']) !!}
                    {!! Form::select('facility_id', App\Http\Helpers\Helpers::getFacilityLists(), null, ['class' => 'form-control select2','multiple'=>'multiple','type'=> 'text']) !!}
                </div>
                <div class="form-group col-sm-2 hide p-r-0">
                    {!! Form::Label('billing_provider_id', 'Billing Provider',['class' => 'control-label font600']) !!}
                    {!! Form::select('billing_provider_id', App\Models\Provider::typeBasedAllTypeProviderlist('Billing'), null, ['class' => 'form-control select2','multiple'=>'multiple']) !!}
                </div>
                <div class="form-group col-sm-2 hide p-r-0">
                    {!! Form::Label('rendering_provider_id', 'Rendering Provider',['class' => 'control-label font600']) !!}
                    {!! Form::select('rendering_provider_id', App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'), null, ['class' => 'form-control select2','multiple'=>'multiple']) !!}
                </div>
                <div class="form-group col-sm-2">
                    {!! Form::Label('transaction_date', 'Transaction Date',['class' => 'control-label font600']) !!}
                <input type="text" name="transaction_date" class="date auto-generate bg-white form-control form-select js-date-range" id="transaction_date" style="width:100%">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-1">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0 border-radius-4 yes-border border-green">   
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                    <div class="box no-shadow no-border no-bottom">
                        <div class="box-body no-b-t no-padding " >
                            <div id="chart-pie" style="margin-top: 1px"></div> 
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-10">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 9px;">
                                <div class="col-lg-12 no-padding">
                                    
                                    <div class="col-lg-12 p-l-0 p-r-0 p-t-0" style="font-size: 12px;line-height: 22px; margin-top: 6px;padding-bottom: 20px;border-bottom: 1px solid #f0f0f0"> 
                                        <div class="p-r-5 margin-b-10" style="font-size: 28px;">
                                            <span class="billed-charges billed dashboard-number" >${!! App\Http\Helpers\Helpers::priceFormat($billed) !!}</span>
                                        </div>
                                        <span class="font600" style="font-size: 14px;"><i class="fa fa-money"></i> Billed Charges</span>
                                    <?php /*<br><span class="med-darkgray font600 billed">${!!App\Http\Helpers\Helpers::priceFormat($billed) !!}</span>,
                                        <i class="billed_fa fa @if($billedpercentage<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 13px;"></i>
                                        <span class="font600 billed_percentange @if($billedpercentage<0) med-orange @else med-green @endif">
                                            {{abs(round($billedpercentage))}}%
                                        </span> last month*/?></div>                                                                 
                                </div>                                
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 p-l-0" style="margin-top: 7px; padding-top:10px;">
                                <div class="col-lg-12 no-padding">
                                    
                                    <div class="col-lg-12 no-padding" style=" font-size: 12px;line-height: 22px; margin-top: 6px;"> 
                                        <div class="p-r-5 margin-b-10" style="font-size: 28px;" >
                                            <span class="unbilled-charges unbilled dashboard-number" >${!! App\Http\Helpers\Helpers::priceFormat($unbilled) !!}</span>
                                        </div>
                                        <span class="font600" style="font-size: 14px;"><i class="fa fa-money"></i> Unbilled Charges</span>
                                    <?php /*<br><span class="med-darkgray font600 unbilled">${!!App\Http\Helpers\Helpers::priceFormat($unbilled) !!}</span>,
                                        <i class="unbilled_fa fa @if($unbilled_percentange<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 13px;"></i>
                                        <span class="font600 unbilled_percentange @if($unbilled_percentange<0) med-orange @else med-green @endif">
                                            {{abs(round($unbilled_percentange))}}%
                                        </span> last month*/?></div>
                                </div>
                            </div>
                            
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-20">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="font-size: 12px;line-height: 22px; margin-top: 5px;padding-bottom: 20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    
                                <div class="col-lg-12 no-padding" style="font-size: 12px;line-height: 22px; margin-top: 0px;">
                                    <div class="p-r-5 margin-b-10" style="font-size: 28px;" >
                                        <span class="hold-charges chargesHoldValue dashboard-number" >{{$hold->chargesHoldValue}}</span>
                                    </div> 
                                    <span class="font600" style="font-size: 14px;"><i class="fa fa-shopping-cart"></i> Hold Claims</span>
                                <?php /*<br><span class="med-darkgray font600 chargesHoldValue">{!!App\Http\Helpers\Helpers::priceFormat($hold->chargesHoldValue) !!}</span>,
                                        <i class="chargesHoldValue_fa fa @if($hold->chargesHoldPercentage<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 13px;"></i> 
                                        <span class="font600 chargesHoldPercentage @if($hold->chargesHoldPercentage<0) med-orange @else med-green @endif">
                                            {{abs(round($hold->chargesHoldPercentage))}}%
                                        </span> last month*/?></div>                                                                             
                                </div>                                
                            </div>                            

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 15px;">
                                <div class="col-lg-12 no-padding">
                                    
                                    <div class="col-lg-12 no-padding" style="font-size: 12px;line-height: 22px; margin-top: 8px;"> 
                                        <div class=" p-r-5 margin-b-10" style="font-size: 28px;" >
                                            <span class="rejection-charges billed dashboard-number" >${!! App\Http\Helpers\Helpers::priceFormat($edirejection) !!}</span>
                                        </div>
                                        <span class="font600" style="font-size: 14px;"><i class="fa fa-shopping-cart"></i> Rejected Claims</span>
                                    <?php /*<br><span class="med-darkgray font600 edirejection">{!!App\Http\Helpers\Helpers::priceFormat($edirejection); !!}</span>,
                                        <i class="edirejection_fa fa @if($edirejection_percentage<0) fa fa-chevron-down med-orange @else fa fa-chevron-up med-green @endif" style="font-size: 13px;"></i> 
                                        <span class="font600 edirejectionPercentage @if($edirejection_percentage<0) med-orange @else med-green @endif">
                                            {{abs(round($edirejection))}}%
                                        </span> last month*/?></div>                                                                         
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>

            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-20">
                    <div class="box no-shadow no-border">
                        <div class="box-body no-b-t margin-b-1 p-r-0">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="font-size: 12px;line-height: 22px; margin-top: 5px;padding-bottom: 20px;border-bottom: 1px solid #f0f0f0">
                                <div class="col-lg-12 no-padding">
                                    
                                <div class="col-lg-12 no-padding" style="font-size: 12px;line-height: 22px; margin-top: 0px;">
                                    <div class="p-r-5 margin-b-10" style="font-size: 28px;">
                                        <span class="med-darkgray dashboard-number">{{$chargesDenied->chargesDeniedValue}}</span>
                                    </div> 
                                    <span class="font600" style="font-size: 14px;"><i class="fa fa-shopping-cart"></i> Denied Claims</span>
                                </div>                                                                             
                                </div>                                
                            </div>                            

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0" style="margin-top: 15px;">
                                <div class="col-lg-12 no-padding">
                                    
                                    <div class="col-lg-12 no-padding" style="font-size: 12px;line-height: 22px; margin-top: 8px;"> 
                                        <div class=" p-r-5 margin-b-10" style="font-size: 28px;">
                                            <span class="med-darkgray dashboard-number">{{$submittedClaims->ReadyToSubmitValue}}</span>
                                        </div>
                                        <span class="font600" style="font-size: 14px;"><i class="fa fa-shopping-cart"></i> Submitted Claims</span>
                                    </div>                                                                         
                                </div>                                
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 hide">
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
            <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 no-padding"  style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-0">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1 p-l-0 p-r-0" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-0 p-l-0 p-r-0">                                
                                <p class="margin-t-15 font14" style="margin-left:17px;"><i class="fa fa-circle" style="color: #5cb0c3; position: absolute; margin-left: -17px; margin-top: 3px;"></i><span class="font600" style="font-size: 16px;"> 12434.00</span> Medicare</p>
                                <p class="font14" style="margin-left:17px; margin-top: 25px;"><i class="fa fa-circle" style="color: #a6bb50; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 16px;"> 11613.00</span> Medicaid</p>
                                <p class="margin-t-22 font14" style="margin-left:17px;margin-top: 25px;"><i class="fa fa-circle" style="color: #8c9ba8; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 16px;"> 21345.00</span> UHC</p>                               
                                <p class="margin-t-22 font14" style="margin-left:17px; margin-top: 25px; margin-bottom: 1px;"><i class="fa fa-circle" style="color: #e45b5b; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 16px;"> 13240.00</span> BCBS</p> 
                                <p class="margin-t-22 font14" style="margin-left:17px; margin-top: 25px; margin-bottom: 1px;"><i class="fa fa-circle" style="color: #eb873d; position: absolute; margin-left: -17px; margin-top: 3px;"></i> <span class="font600" style="font-size: 16px;"> 23140.20</span> Aetna</p> 
                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding" style="border-right: 3px solid #f8f6f6;">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border">

                        <div class="box-body no-b-t margin-b-1" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 7px;">
                                <p class="margin-b-20" style="font-size: 40px"><span class="font600" style="color: #87a801">27%</span></p>
                                <p style="padding-bottom: 18px; font-size: 16px"><i class="fa fa-arrow-circle-up" style="color: #87a801; font-size: 18px;"></i> Insurance Payments</p>
                                <p class="margin-b-20" style="font-size: 40px;padding-top: 50px;"><span class="font600" style="color: #eb3d3d">3%</span></p>
                                <p class="font16" style="margin-bottom: 0px;"><i class="fa fa-arrow-circle-down" style="color: #eb3d3d;font-size: 18px;"></i> Patient Payments</p>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-5">
                    <div class="box no-shadow no-border no-bottom">

                        <div class="box-body no-b-t margin-b-1 no-bottom" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                               
                                <p class="margin-t-0 margin-b-1 font14"> <span class="font600 med-darkgray" style="font-size: 18px;">$1320.00</span></p>
                                <p class="font14"> <span class="font600 med-green" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 9px;">Insurance Payments</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801"></i> <span style="color: #87a801" class="font600">12%</span> from last month</p>

                                <p class="margin-t-20 margin-b-1 font14"> <span class="font600 med-darkgray" style="font-size: 18px;">$604.00</span></p>
                                <p class="font14"> <span class="font600 med-green" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 9px;">Patient Payments</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801"></i> <span style="color: #87a801" class="font600">3%</span> from last month</p>

                                <p class="margin-t-20 margin-b-1 font14"> <span class="font600 med-darkgray" style="font-size: 18px;">$604.00</span></p>
                                <p class="font14"> <span class="font600 med-green" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 9px;">Contractual Adjustment</span>, <i class="fa fa-arrow-circle-up" style="color: #87a801"></i> <span style="color: #87a801" class="font600">3%</span> from last month</p>

                                <p class="margin-t-20 margin-b-1 font14"> <span class="font600 med-darkgray" style="font-size: 18px;">$604.00</span></p>
                                <p class="font14 no-bottom"> <span class="font600 med-green"> Wallet Balance</span>, <i class="fa fa-arrow-circle-down" style="color: #eb3d3d"></i> <span style="color: #eb3d3d" class="font600">1%</span> from last month</p>

                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12" >
        <div class="box no-bottom no-shadow">
            <div class="box-header no-border border-radius-4 dash-bg-white">
                 <h4 class="dash-headings"><i class="fa fa-bar-chart"></i> Collections - By Responsibility</h4>
            </div>
            <div class="box-body no-b-t  dashboard-table">           
                <div id="collections_breakup_responsibility">Collections Breakup - By Responsibility</div>
            </div><!-- /.box-body -->           
        </div> 
    </div>
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="box no-bottom no-shadow">
            <div class="box-header no-border border-radius-4 dash-bg-white">
                <h4 class="dash-headings"><i class="fa fa-line-chart"></i> Top 10 Procedure Codes</h4> 
            </div>
            <div class="box-body no-b-t  dashboard-table">           
                <div id="top_ten_cpt">Top 10 Procedure Codes</div>
            </div><!-- /.box-body -->  
        </div>
    </div>  
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12" >
        <div class="box no-bottom no-shadow">
            <div class="box-header no-border border-radius-4 dash-bg-white">
                 <h4 class="dash-headings"><i class="fa fa-pie-chart"></i> Top 10 Payers</h4>
            </div>
            <div class="box-body no-b-t  dashboard-table">           
                <div id="chart-ins">Top 10 Payers</div>
            </div><!-- /.box-body -->           
        </div> 
    </div>
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="box no-bottom no-shadow">
            <div class="box-header no-border border-radius-4 dash-bg-white">
                <h4 class="dash-headings"><i class="fa fa-area-chart"></i> Collections Vs Adjustments</h4>
            </div>
            <div class="box-body no-b-t  dashboard-table">           
                <div id="chart-collect">Collections Vs Adjustments</div>
            </div><!-- /.box-body -->  
        </div>
    </div>  
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="box no-bottom no-shadow">
        <div class="box-header no-border border-radius-4 dash-bg-white">
            <h4 class="dash-headings" style="margin-bottom: 10px !important;">
                <i class="fa fa-list-alt"></i> Collections Breakup - By Payers
            </h4>
        </div>
        <div class="box-body no-b-t  dashboard-table">
            <table style="cursor: default !important;" class="table font13 dataTable no-footer" id="payers-pmt-dashboard">
                <thead>
                    <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important; width: auto;">Insurance Name</th>
                    @if(isset($payers_month))
                    @foreach($payers_month as $m)
                    <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important; width: 6%;">{{$m}}</th>
                    @endforeach
                    @endif
                    <th style="background: rgb(228, 231, 253); font-weight: 600; color: rgb(0, 127, 120) !important; width: auto;">Total($)</th>
                </thead>
                <tbody>
                        @if(isset($payers) && !empty($payers))
                            @foreach($payers as $key=>$p)
                                <?php $ins_tot_pmt = 0;?>
                                @if(array_sum($payers[$key])!=0)
                                    <tr>
                                        <td class="med-green">{{$key}}</td>
                                        @foreach($payers_month as $m)
                                            <?php
                                            $pmt = isset($p[$m])?$p[$m]:0;
                                            $ins_tot_pmt += $pmt;
                                            ?>
                                            <td style="text-align:right">{{isset($p[$m])?$p[$m]:'0.00'}}</td>
                                        @endforeach
                                        <td style="text-align:right" class="font600">{!! \App\Http\Helpers\Helpers::priceFormat($ins_tot_pmt) !!}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                </tbody>
            </table>
        </div><!-- /.box-body -->  
    </div>
</div>
<?php 
	$exp = explode('-',\App\Http\Helpers\Helpers::getPracticeCreatedDate()); 
	$now = time(); // or your date as well
	$your_date = strtotime(trim($exp[0]));
	$datediff = $now - $your_date;

	$start_date_of_practice = round($datediff / (60 * 60 * 24));
?>
@stop
@push('view.scripts')
{!! HTML::script('js/dashboard/fusioncharts.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint1.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fint2.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.theme.fintinsurance.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.charts.js') !!}
{!! HTML::script('js/dashboard/fusioncharts.powercharts.js') !!}

{!! HTML::style('css/search_fields.css') !!}
<style type="text/css">
    .table-condensed{
        pointer-events: none;
    }
</style>
<script>
    $.fn.digits = function(){ 
        return this.each(function(){ 
            $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
        })
    }
$(document).ready(function (){
// ------------- Start Transaction date filter ------------------------
    var end_date = '{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/y") }}';
    $('input[name="transaction_date"]').daterangepicker({
        //autoUpdateInput: false,
        startDate: moment().startOf('month'),
        endDate: end_date,
        alwaysShowCalendars: true,
        showDropdowns: true,
        locale: {
          customRangeLabel:''
        },
        ranges: {
           'This Month': [moment().startOf('month'), end_date],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Till Date': [moment().subtract({{$start_date_of_practice}},'days'),moment()],
        }
    });
    
});
// ------------- End Transaction date filter ------------------------

// ------------- Start Payment Analytics ------------------------

    FusionCharts.ready(function () {
        var analyticsChart = new FusionCharts({
            type: 'doughnut3d',
            renderAt: 'chart-pie',
            width: '100%',
            height: '200',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "animation": "1",
                    "subCaption": "",
                    "numberPrefix": "$",
                    "startingAngle": "20",
                    "showPercentValues": "1",
                    "enableSmartLabels": "1",
                    "manageLabelOverflow":"1",
                    "showPercentInTooltip": "0",
                    "showLabels": "1",
                    "enableMultiSlicing": "1",
                    "use3DLighting": "1",
                    "palettecolors": "#5cb0c3,#a6bb50,#e0c034,#e45b5b",
                    "showValues": "1",
                    "baseFontSize": "13",
                    "showLegend": "0",
                    "decimals": "2",
                    "plotBorderColor": "#000",
                    "plotBorderThickness": "0",
                    "showPlotBorder": "0",
                    "chartTopMargin": "22",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    "chartRighttMargin": "0",
                    "valueFontSize": "13",
                    "valueFontColor": "#697d94",
                    "toolTipFontSize": "15",
                    "usedataplotcolorforlabels": "1",                
                    //Theme
                    "theme": "fintinsurance"

                },
                "data": [
                    {
                        "label": "Unbilled",
                        "value": {!!$unbilled!!}
                    },
                    {
                        "label": "Billed",
                        "value": {!!$billed!!}
                    }
                ]
            }
        });
           <?php
              if($unbilled == '0' && $billed == '0'){?>         
              analyticsChart.setXMLData("<chart></chart>"); 
              analyticsChart.configure("ChartNoDataText", "No Records Found");
              <?php } ?>
              analyticsChart.render();
    });
// ------------- End Payment Analytics ------------------------
// ------------- Start Collections Breakup - By Responsibility ------------------------

    FusionCharts.ready(function () {
        var responsibilityChart = new FusionCharts({
            type: 'scrollstackedcolumn2d',
            dataEmptyMessageFontSize : '18px',
            renderAt: 'collections_breakup_responsibility',
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
                    "formatnumberscale":'0',
                    "chartRighttMargin": "0"
                },
                "categories": [{
                        "category": {!!json_encode($months)!!}
                    }],
                "dataset": [{
                        "seriesname": "Primary",
                        "data": {!!json_encode($primary)!!}
                    },
                    {
                        "seriesname": "Secondary",
                        "data": {!!json_encode($secondary)!!}
                    },
                    {
                        "seriesname": "Tertiary",
                        "data": {!!json_encode($tertiary)!!}
                    },
                    {
                        "seriesname": "Self",
                        "data": {!!json_encode($self)!!}
                    }
                ]
            }
        });
        <?php
              if(empty($primary) && empty($secondary) && empty($tertiary) && empty($self)){?>         
              responsibilityChart.setXMLData("<chart></chart>"); 
              responsibilityChart.configure("ChartNoDataText", "No Records Found");
              <?php } ?>
              responsibilityChart.render();
    });

// ------------- End Collections Breakup - By Responsibility ------------------------
// ------------- Start Top 10 Procdeure Codes ------------------------

    FusionCharts.ready(function () {
        var top_ten_cptChart = new FusionCharts({
            type: 'scrollline2d',
            dataEmptyMessageFontSize : '18px',
            dataFormat: 'json',
            renderAt: 'top_ten_cpt',
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
                    "numVisiblePlot": "10",
                    "showHoverEffect": "1",
                    "chartTopMargin": "20",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "20",                    
                },
                "categories": [{
                        "category": {!!json_encode($cpt_label)!!}
                }],
                "dataset": [{
                      "seriesname": '{{date("Y",strtotime("-1 year"))}}',
                      "data": {!!json_encode($last_year_cpt_value)!!}
                    },
                    {
                      "seriesname": '{{date("Y")}}',
                      "data": {!!json_encode($current_year_cpt_value)!!}
                    }],
            }
        });
           <?php
              if(empty($last_year_cpt_value) && empty($current_year_cpt_value)){?>         
              top_ten_cptChart.setXMLData("<chart></chart>"); 
              top_ten_cptChart.configure("ChartNoDataText", "No Records Found");
              <?php } ?>
              top_ten_cptChart.render();
    });
// ------------- End Top 10 Procdeure Codes ------------------------

// ------------- Start Top 10 Payers ------------------------------------------------------
    FusionCharts.ready(function () {
        var payerChart = new FusionCharts({
            type: 'pie3d',
            dataEmptyMessageFontSize : '18px',
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
                    "baseFontColor": "#999696",
                    "baseFontSize": "13",
                    "baseFont": "'Open Sans', sans-serif",
                    "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
                    "decimals": "2",
                    "numberprefix": "$",
                    "pieslicedepth": "30",
                    "startingangle": "125",
                    "toolTipColor": "#ffffff",
                    "chartTopMargin": "35",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "20",
                    "chartRighttMargin": "0",
                    "toolTipBorderThickness": "0",
                    "toolTipBgColor": "#000000",
                    "toolTipBgAlpha": "85",
                    "toolTipBorderRadius": "4",
                    "toolTipPadding": "10",
                    "showborder": "0",
                    "usedataplotcolorforlabels": "1",
                    "theme": "fusion"
                },
                "data": {!!json_encode($top_ten_payer)!!}
            }
        });
           <?php
              if(empty($top_ten_payer)){?>         
              payerChart.setXMLData("<chart></chart>"); 
              payerChart.configure("ChartNoDataText", "No Records Found");
              <?php } ?>
              payerChart.render();
    });

// ------------- End Top 10 Payers ------------------------------------------------------

// ------------- Start Collections Vs Adjustments ---------------------------------------
    
    FusionCharts.ready(function () {
        var colVsAdjChart = new FusionCharts({
            type: 'mscombi2d',
            dataEmptyMessageFontSize : '18px',
            renderAt: 'chart-collect',
            width: '100%',
            height: '300',
            dataFormat: 'json',
            dataSource: {
                "chart": {
                    "caption": "",
                    "subCaption": "",
                    "xAxisname": "",
                    "pYAxisName": "",
                    "sYAxisName": "",
                    "numberPrefix": "$",
                    "sNumberPrefix": "$",
                    "sYAxisMaxValue": "50",
                    labelFontSize: "13",
                    labelFontColor: "#999696",
                    labelFontBold: "0",
                    baseFontColor: "#999696",
                    baseFontSize: "13",
                    baseFont: "'Open Sans', sans-serif",
                    //Cosmetics
                    "paletteColors": "#0075c2,#f2c500,#1aaf5d",
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
                    "captionFontSize": "14",
                    "subcaptionFontSize": "14",
                    "subcaptionFontBold": "0",
                    "showBorder": "0",
                    "showvalues": "0",
                    "bgColor": "#ffffff",
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
                    "xAxisLineColor": "#999999",
                    "showAlternateHGridColor": "0",
                    "showAlternateVGridColor": "0",
                    "chartTopMargin": "10",
                    "chartBottomMargin": "0",
                    "chartLeftMargin": "0",
                    //"formatnumberscale":'0',
                    "chartRighttMargin": "0"

                },
                "categories": [{
                        "category": {!!json_encode($col_vs_adj_months)!!}
                    }
                ],
                "dataset": [
                    {
                        "seriesName": "Collections",
                        "data": {!!json_encode($collections)!!}
                   },
                    {
                        "seriesName": "Adjustments",
                        "parentYAxis": "S",
                        "renderAs": "line",
                        "showValues": "10",
                        "data": {!!json_encode($adjustments)!!}
                    }
                ]
            }
        });

        <?php
            if(empty($adjustments) && empty($collections)){?>         
            colVsAdjChart.setXMLData("<chart></chart>"); 
            colVsAdjChart.configure("ChartNoDataText", "No Records Found");
            <?php } ?>
            colVsAdjChart.render();
    });

// ------------- End Collections Vs Adjustments ---------------------------------------

    
    $("#FilterBy,#facility_id,#billing_provider_id,#rendering_provider_id").on('change',function(){
        var filter =  $("#FilterBy").val();
        if(filter=='Facility'){
            $("#facility_id").closest('.form-group').removeClass('hide');
            $("#billing_provider_id").closest('.form-group').addClass('hide');
            $("#rendering_provider_id").closest('.form-group').addClass('hide');
            var facility_id = $("#facility_id").val();
        }else if(filter=='Billing Provider'){
            $("#billing_provider_id").closest('.form-group').removeClass('hide');
            $("#facility_id").closest('.form-group').addClass('hide');
            $("#rendering_provider_id").closest('.form-group').addClass('hide');
            var billing_provider_id = $("#billing_provider_id").val();
        }else if(filter=='Rendering Provider'){
            $("#rendering_provider_id").closest('.form-group').removeClass('hide');
            $("#billing_provider_id").closest('.form-group').addClass('hide');
            $("#facility_id").closest('.form-group').addClass('hide');
            var rendering_provider_id = $("#rendering_provider_id").val();
        }
        var transaction_date = $("#transaction_date").val();
        $.ajax({
            method:"post",
            url:"{{url('/analytics/financials')}}",
            data:{filterBy:filter,'_token':$('input[name=_token]').val(),facility_id:facility_id,billing_provider_id:billing_provider_id,rendering_provider_id:rendering_provider_id,transaction_date:transaction_date},
            success:function(result){
                $(".billed").text('$'+result.billed).digits();
                $(".billed_percentage").text(Math.abs(Math.round(result.billedpercentage))+'%');
                if(result.billedpercentage<0){
                    $(".billed_fa").removeClass("fa-chevron-up med-green");
                    $(".billed_fa").addClass("fa-chevron-down med-orange");
                     $(".billed_percentange").removeClass("med-green");
                    $(".billed_percentange").addClass("med-orange");
                }else{
                    $(".billed_fa").removeClass("fa-chevron-down med-orange");
                    $(".billed_fa").addClass("fa-chevron-up med-green");
                    $(".billed_percentange").removeClass("med-orange");
                    $(".billed_percentange").addClass("med-green");
                }
                $(".unbilled").text('$'+result.unbilled).digits();
                $(".unbilled_percentange").text(Math.abs(Math.round(result.unbilled_percentange))+'%');
                if(result.unbilled_percentange<0){
                    $(".unbilled_fa").removeClass("fa-chevron-up med-green");
                    $(".unbilled_fa").addClass("fa-chevron-down med-orange");
                    $(".unbilled_percentange").removeClass("med-green");
                    $(".unbilled_percentange").addClass("med-orange");
                }else{
                    $(".unbilled_fa").removeClass("fa-chevron-down med-orange");
                    $(".unbilled_fa").addClass("fa-chevron-up med-green");
                    $(".unbilled_percentange").removeClass("med-orange");
                    $(".unbilled_percentange").addClass("med-green");
                }
                $(".chargesHoldValue").text(result.hold.chargesHoldValue);
                $(".chargesHoldPercentage").text(Math.abs(Math.round(result.hold.chargesHoldPercentage))+'%');
                if(result.chargesHoldPercentage<0){
                    $(".chargesHoldValue_fa").removeClass("fa-chevron-up med-green");
                    $(".chargesHoldValue_fa").addClass("fa-chevron-down med-orange");
                    $(".chargesHoldPercentage").removeClass("med-green");
                    $(".chargesHoldPercentage").addClass("med-orange");
                }else{
                    $(".chargesHoldValue_fa").removeClass("fa-chevron-down med-orange");
                    $(".chargesHoldValue_fa").addClass("fa-chevron-up med-green");
                    $(".chargesHoldPercentage").removeClass("med-orange");
                    $(".chargesHoldPercentage").addClass("med-green");
                }
                $(".rejection-charges").text('$'+result.edirejection).digits();
                $(".edirejectionPercentage").text(Math.abs(Math.round(result.edirejection_percentage))+'%');
                if(result.edirejection_percentage<0){
                    $(".edirejection_fa").removeClass("fa-chevron-up med-green");
                    $(".edirejection_fa").addClass("fa-chevron-down med-orange");
                    $(".edirejectionPercentage").removeClass("med-green");
                    $(".edirejectionPercentage").addClass("med-orange");
                }else{
                    $(".edirejection_fa").removeClass("fa-chevron-down med-orange");
                    $(".edirejection_fa").addClass("fa-chevron-up med-green");
                    $(".edirejectionPercentage").removeClass("med-orange");
                    $(".edirejectionPercentage").addClass("med-green");
                }
                $(".denied").text(result.chargesDenied.chargesDeniedValue);
                $(".submitted").text(result.submittedClaims.ReadyToSubmitValue);
                FusionCharts.ready(function () {
                    var analyticsChart = new FusionCharts({
                        type: 'doughnut3d',
                        renderAt: 'chart-pie',
                        width: '100%',
                        height: '200',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                "caption": "",
                                "animation": "1",
                                "subCaption": "",
                                "numberPrefix": "$",
                                "startingAngle": "-50",
                                "showPercentValues": "1",
                                "enableSmartLabels": "1",
                                "manageLabelOverflow":"1",
                                "showPercentInTooltip": "1",
                                "showLabels": "1",
                                "enableMultiSlicing": "1",
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
                                "valueFontSize": "13",
                                "valueFontColor": "#697d94",
                                "toolTipFontSize": "13",
                                "theme": "fusion"
                            },
                            "data": [
                                {
                                    "label": "Unbilled",
                                    "value": result.unbilled
                                },
                                {
                                    "label": "Billed",
                                    "value": result.billed
                                },
                                {
                                    "label": "Hold",
                                    "value": result.hold.chargesHoldValue
                                },
                                {
                                    "label": "Rejection",
                                    "value": result.edirejection
                                }
                            ]
                        }
                    }).render();
        
                });
                // ------------- Start Collections Breakup - By Responsibility ------------------------

                FusionCharts.ready(function () {
                    var responsibilityChart = new FusionCharts({
                        type: 'scrollstackedcolumn2d',
                        renderAt: 'collections_breakup_responsibility',
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
                                    "category": result.months
                                }],
                            "dataset": [{
                                    "seriesname": "Primary",
                                    "data": result.primary
                                },
                                {
                                    "seriesname": "Secondary",
                                    "data": result.secondary
                                },
                                {
                                    "seriesname": "Tertiary",
                                    "data": result.tertiary
                                },
                                {
                                    "seriesname": "Self",
                                    "data": result.self
                                }
                            ]
                        }
                    });

                    responsibilityChart.render();
                });

            // ------------- End Collections Breakup - By Responsibility ------------------------
            // ------------- Start Top 10 Procdeure Codes ------------------------

                FusionCharts.ready(function () {
                    var top_ten_cptChart = new FusionCharts({
                        type: 'scrollline2d',
                        dataFormat: 'json',
                        renderAt: 'top_ten_cpt',
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
                            "categories": [{
                                    "category": result.cpt_label
                            }],
                            "dataset": [{
                                  "seriesname": '{{date("Y",strtotime("-1 year"))}}',
                                  "data": result.last_year_cpt_value
                                },
                                {
                                  "seriesname": '{{date("Y")}}',
                                  "data": result.current_year_cpt_value
                                }],
                        }
                    }).render();
                });
            // ------------- End Top 10 Procdeure Codes ------------------------

            // ------------- Start Top 10 Payers ------------------------------------------------------
                FusionCharts.ready(function () {
                    var payerChart = new FusionCharts({
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
                                "baseFontColor": "#999696",
                                "baseFontSize": "13",
                                "baseFont": "'Open Sans', sans-serif",
                                "palettecolors": "#008ee4,#f8bd19,#f83939,#8c9ba8,#31b9a3,#fc8727,#b5e133,#e13375,#374b56",
                                "decimals": "2",
                                "numberprefix": "$",
                                "pieslicedepth": "30",
                                "startingangle": "125",
                                "toolTipColor": "#ffffff",
                                "chartTopMargin": "35",
                                "chartBottomMargin": "0",
                                "chartLeftMargin": "20",
                                "chartRighttMargin": "0",
                                "toolTipBorderThickness": "0",
                                "toolTipBgColor": "#000000",
                                "toolTipBgAlpha": "85",
                                "toolTipBorderRadius": "4",
                                "toolTipPadding": "10",
                                "showborder": "0",
                                "theme": "fusion"
                            },
                            "data": result.top_ten_payer
                        }
                    }).render();
                });

            // ------------- End Top 10 Payers ------------------------------------------------------

            // ------------- Start Collections Vs Adjustments ---------------------------------------
                
                FusionCharts.ready(function () {
                    var colVsAdjChart = new FusionCharts({
                        type: 'mscombidy2d',
                        renderAt: 'chart-collect',
                        width: '100%',
                        height: '300',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                "caption": "",
                                "subCaption": "",
                                "xAxisname": "",
                                "pYAxisName": "",
                                "sYAxisName": "",
                                "numberPrefix": "$",
                                "sNumberSuffix": "%",
                                "sYAxisMaxValue": "50",
                                labelFontSize: "13",
                                labelFontColor: "#999696",
                                labelFontBold: "0",
                                baseFontColor: "#999696",
                                baseFontSize: "13",
                                baseFont: "'Open Sans', sans-serif",
                                //Cosmetics
                                "paletteColors": "#0075c2,#f2c500,#1aaf5d",
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
                                "captionFontSize": "14",
                                "subcaptionFontSize": "14",
                                "subcaptionFontBold": "0",
                                "showBorder": "0",
                                "showvalues": "0",
                                "bgColor": "#ffffff",
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
                                "xAxisLineColor": "#999999",
                                "showAlternateHGridColor": "0",
                                "showAlternateVGridColor": "0",
                                "chartTopMargin": "10",
                                "chartBottomMargin": "0",
                                "chartLeftMargin": "0",
                                "chartRighttMargin": "0"

                            },
                            "categories": [{
                                    "category": result.col_vs_adj_months
                                }
                            ],
                            "dataset": [
                                {
                                    "seriesName": "Collections",
                                    "data": result.collections
                               },
                                {
                                    "seriesName": "Adjustments",
                                    "parentYAxis": "S",
                                    "renderAs": "line",
                                    "showValues": "10",
                                    "data": result.adjustments
                                }
                            ]
                        }
                    });

                    colVsAdjChart.render();
                });

            // ------------- End Collections Vs Adjustments ---------------------------------------
            // -----------------------Start Collections Breakup - By Payers --------------------------------------

                head = '<table class="table table-striped table-bordered table-separate mobile-lg-width" id="payers"><thead>';
                head +='<th style="text-align: left;border-bottom: 3px solid #87cdc7 !important;">Insurance Name</th>';

                $.each(result.payers_month,function(index,value){
                    head += '<th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">'+value+'</th>';
                });

                head +='<th style="text-align: right;border-bottom: 3px solid #87cdc7 !important;">Total($)</th>';
                head += '</thead>';

                body = '<tbody>';
                
                $.each(result.payers,function(index,value){
                    var ins_tot_pmt = 0.00;
                    body += '<tr><td class="med-green">'+index+'</td>';
                    $.each(result.payers_month,function(i,v){
                        var pmt = (value[v])?value[v]:0.00;
                        var td = (value[v])?value[v]:0.00;
                        ins_tot_pmt = ins_tot_pmt+parseFloat(pmt);
                        body += '<td style="text-align:right">'+parseFloat(td).toFixed(2) +'</td>';
                    });
                    body += '<td style="text-align:right" class="med-green font600">'+parseFloat(ins_tot_pmt).toFixed(2)+'</td></tr>';
                });

                body += '</tbody></table>';
                $(".payers-data-table").html(head+body);
                $('#payers').dataTable({"paging": true, "iDisplayLength": 15, "info": true, "lengthChange": false, "searching": false, "aaSorting": [], "scrollX": true,language: {
                    paginate: {
                        previous: '«',
                        next: '»'
                    }
                }});

            // -----------------------End Collections Breakup - By Payers --------------------------------------
            }
        });
    });

	$(".date").on('change',function(){
        var filter =  $("#FilterBy").val();
        if(filter=='Facility'){
            $("#facility_id").closest('.form-group').removeClass('hide');
            $("#billing_provider_id").closest('.form-group').addClass('hide');
            $("#rendering_provider_id").closest('.form-group').addClass('hide');
            var facility_id = $("#facility_id").val();
        }else if(filter=='Billing Provider'){
            $("#billing_provider_id").closest('.form-group').removeClass('hide');
            $("#facility_id").closest('.form-group').addClass('hide');
            $("#rendering_provider_id").closest('.form-group').addClass('hide');
            var billing_provider_id = $("#billing_provider_id").val();
        }else if(filter=='Rendering Provider'){
            $("#rendering_provider_id").closest('.form-group').removeClass('hide');
            $("#billing_provider_id").closest('.form-group').addClass('hide');
            $("#facility_id").closest('.form-group').addClass('hide');
            var rendering_provider_id = $("#rendering_provider_id").val();
        }
        var transaction_date = $("#transaction_date").val();
        $.ajax({
            method:"post",
            url:"{{url('/analytics/financials')}}",
            data:{filterBy:filter,'_token':$('input[name=_token]').val(),facility_id:facility_id,billing_provider_id:billing_provider_id,rendering_provider_id:rendering_provider_id,transaction_date:transaction_date},
            success:function(result){
                $(".billed").text('$'+result.billed).digits();
                $(".billed_percentage").text(Math.abs(Math.round(result.billedpercentage))+'%');
                if(result.billedpercentage<0){
                    $(".billed_fa").removeClass("fa-chevron-up med-green");
                    $(".billed_fa").addClass("fa-chevron-down med-orange");
                     $(".billed_percentange").removeClass("med-green");
                    $(".billed_percentange").addClass("med-orange");
                }else{
                    $(".billed_fa").removeClass("fa-chevron-down med-orange");
                    $(".billed_fa").addClass("fa-chevron-up med-green");
                    $(".billed_percentange").removeClass("med-orange");
                    $(".billed_percentange").addClass("med-green");
                }
                $(".unbilled").text('$'+result.unbilled).digits();
                $(".unbilled_percentange").text(Math.abs(Math.round(result.unbilled_percentange))+'%');
                if(result.unbilled_percentange<0){
                    $(".unbilled_fa").removeClass("fa-chevron-up med-green");
                    $(".unbilled_fa").addClass("fa-chevron-down med-orange");
                    $(".unbilled_percentange").removeClass("med-green");
                    $(".unbilled_percentange").addClass("med-orange");
                }else{
                    $(".unbilled_fa").removeClass("fa-chevron-down med-orange");
                    $(".unbilled_fa").addClass("fa-chevron-up med-green");
                    $(".unbilled_percentange").removeClass("med-orange");
                    $(".unbilled_percentange").addClass("med-green");
                }
                $(".chargesHoldValue").text(result.hold.chargesHoldValue);
                $(".chargesHoldPercentage").text(Math.abs(Math.round(result.hold.chargesHoldPercentage))+'%');
                if(result.chargesHoldPercentage<0){
                    $(".chargesHoldValue_fa").removeClass("fa-chevron-up med-green");
                    $(".chargesHoldValue_fa").addClass("fa-chevron-down med-orange");
                    $(".chargesHoldPercentage").removeClass("med-green");
                    $(".chargesHoldPercentage").addClass("med-orange");
                }else{
                    $(".chargesHoldValue_fa").removeClass("fa-chevron-down med-orange");
                    $(".chargesHoldValue_fa").addClass("fa-chevron-up med-green");
                    $(".chargesHoldPercentage").removeClass("med-orange");
                    $(".chargesHoldPercentage").addClass("med-green");
                }
                $(".rejection-charges").text('$'+result.edirejection).digits();
                $(".edirejectionPercentage").text(Math.abs(Math.round(result.edirejection_percentage))+'%');
                if(result.edirejection_percentage<0){
                    $(".edirejection_fa").removeClass("fa-chevron-up med-green");
                    $(".edirejection_fa").addClass("fa-chevron-down med-orange");
                    $(".edirejectionPercentage").removeClass("med-green");
                    $(".edirejectionPercentage").addClass("med-orange");
                }else{
                    $(".edirejection_fa").removeClass("fa-chevron-down med-orange");
                    $(".edirejection_fa").addClass("fa-chevron-up med-green");
                    $(".edirejectionPercentage").removeClass("med-orange");
                    $(".edirejectionPercentage").addClass("med-green");
                }
                $(".denied").text(result.chargesDenied.chargesDeniedValue);
                $(".submitted").text(result.submittedClaims.ReadyToSubmitValue);

                FusionCharts.ready(function () {
                    var analyticsChart = new FusionCharts({
                        type: 'doughnut3d',
                        dataEmptyMessageFontSize : '18px',
                        renderAt: 'chart-pie',
                        width: '100%',
                        height: '200',
                        dataFormat: 'json',
                        dataSource: {
                            "chart": {
                                "caption": "",
                                "animation": "1",
                                "subCaption": "",
                                "numberPrefix": "$",
                                "startingAngle": "-50",
                                "showPercentValues": "1",
                                "enableSmartLabels": "1",
                                "manageLabelOverflow":"1",
                                "showPercentInTooltip": "1",
                                "showLabels": "1",
                                "enableMultiSlicing": "1",
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
                                "valueFontSize": "13",
                                "valueFontColor": "#697d94",
                                "toolTipFontSize": "13",
                                "theme": "fusion"
                            },
                            "data": [
                                {
                                    "label": "Unbilled",
                                    "value": result.unbilled
                                },
                                {
                                    "label": "Billed",
                                    "value": result.billed
                                },
                                {
                                    "label": "Hold",
                                    "value": result.hold.chargesHoldValue
                                },
                                {
                                    "label": "Rejection",
                                    "value": result.edirejection
                                }
                            ]
                        }
                    });
                    if(result.unbilled == 0 && result.billed == 0 && result.hold.chargesHoldValue == 0 && result.edirejection == 0 )  {
                    analyticsChart.setXMLData("<chart></chart>");
                    analyticsChart.configure("ChartNoDataText", "No Records Found");
                    analyticsChart.render();
                    }
                });
            }
        });
    });
	
	$('#payers-pmt-dashboard').dataTable({"paging": true, "info": true, "lengthChange": false, "searching": false, "aaSorting": [], language: {
                    paginate: {
                        previous: '«',
                        next: '»'
                    }
                }});

</script>
<link href="https://fonts.googleapis.com/css?family=Fjalla+One" rel="stylesheet">
@endpush