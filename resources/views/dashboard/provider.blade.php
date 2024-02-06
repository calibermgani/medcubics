@extends('admin')
@section('pageTitle', 'Provider Dashboard')

@section('toolbar')
<div class="row toolbar-header" >
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="dashboard"></i>Provider Analytics </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" data-url="" class="js-refresh-data-provider"><i class="fa fa-refresh" data-placement="bottom"  data-toggle="tooltip" data-original-title="Refresh Data"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>            
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Dashboard Top: Unbilled Charges -->
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-shopping-cart"></i> Unbilled Charges <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">To Date</sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5"><?php echo App\Http\Helpers\Helpers::priceFormat($stats_list['api_UnBilled'],'yes'); ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_UnbilledPercentage'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_UnbilledPercentage'])); ?> % <i class="<?php
                                                             if ($stats_list['api_UnbilledPercentage'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-ban"></i> EDI Rejections <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">To Date</sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5"><?php echo App\Http\Helpers\Helpers::priceFormat($stats_list['api_EdiRejection'],'yes'); ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_EdiRejectionPercentage'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_EdiRejectionPercentage'])); ?> % <i class="<?php
                                                             if ($stats_list['api_EdiRejectionPercentage'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-money"></i> Billed Charges <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">MTD</sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number margin-t-5"><?php echo App\Http\Helpers\Helpers::priceFormat($stats_list['api_Billed'],'yes'); ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_BilledPercentageApi'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_BilledPercentageApi'])); ?> % <i class="<?php
                                                             if ($stats_list['api_BilledPercentageApi'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-bank"></i> Ins Payments <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">MTD</sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5"><?php echo $stats_list['api_InsPayment']; ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_InsurancePercentage'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_InsurancePercentage'])); ?> % <i class="<?php
                                                             if ($stats_list['api_InsurancePercentage'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3">
        <p class="no-bottom med-darkgray font600"><i class="fa fa-users"></i> Pat Payments <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">MTD</sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5"><?php echo $stats_list['api_PatPayment']; ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_PatPymtPercentage'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_PatPymtPercentage'])); ?> % <i class="<?php
                                                             if ($stats_list['api_PatPymtPercentage'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>
    </div>
    <?php
        $insuarnce_ar = $stats_list['api_Insurance_ar'];
        $patient_ar = $stats_list['api_Patient_ar'];
    ?>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6  m-t-md-5 m-t-sm-5 m-t-xs-5 dash-b-l-3 dash-b-l-3 p-r-0">
        <!-- don't align tooltip title -->
        <p class="no-bottom med-darkgray font600 tooltipTitle" data-toggle="tooltip" title="Patient AR: <?php echo strip_tags($patient_ar);?>
           Insurance AR: <?php echo strip_tags($insuarnce_ar); ?>"><i class="fa fa-bank"></i> Outstanding AR <sup style="background:#00877f; color:#fff; padding: 0px 4px; border-radius: 4px;">To Date </sup></p>
        <h3 class="no-bottom med-darkgray dashboard-number  margin-t-5"><?php echo App\Http\Helpers\Helpers::priceFormat($stats_list['api_Outstanding_ar'],'yes'); ?></h3>
        <p class="med-gray-dark font600 no-bottom"><span class="<?php
            if ($stats_list['api_Outstanding_ar'] < '0') {
                echo "med-orange";
            } else {
                echo "med-green";
            }
            ?>"><?= abs(round($stats_list['api_OutstandingArApi'])); ?> % <i class="<?php
                                                             if ($stats_list['api_OutstandingArApi'] < '0') {
                                                                 echo "fa fa-chevron-down";
                                                             } else {
                                                                 echo "fa fa-chevron-up";
                                                             }
                                                             ?>"></i> </span>from last month</p>

        <div class="js-show-ar hide">
            <span>Patient AR: <?php $patient_ar; ?></span>
            <span>Insurance AR: <?php echo $insuarnce_ar; ?></span>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 no-padding">
    <div class="box no-bottom no-shadow" style="background: transparent">
        <div class="box-body no-bottom no-padding">   
			<?php /*
            <!--  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 no-padding" style="border-bottom: 1px solid #78899b;">
                 <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 no-padding">
                     <h3 class="margin-b-5 med-darkgray">Performance Management</h3>
                 </div>
                 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 no-padding pull-right">
                     <p class="pull-right no-bottom med-darkgray"> <i class="fa fa-calendar margin-r-5"></i> Last Month</p>
                 </div> 
             </div>-->
            <!-- <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-padding">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 margin-t-15">
                    <div class="box no-shadow no-border">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="margin-t-0 med-orange"><i class="fa fa-bar-chart"></i> YTD Analysis </h4>                            
                            <div class="box-tools pull-right font14">
                                <i class="fa fa-square dash-charge-color" style=""></i> Charges   <i class="fa fa-square margin-l-10 dash-collections-color"></i> Collections
                            </div>
                        </div>

                        <div class="box-body no-b-t" >
                            <div class="chart">
                                <canvas id="barChart" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>             
            </div> -->
			*/ ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="box no-bottom no-shadow"  style="border:1px solid #dedddd; border-radius: 4px;" >
                    <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                            <div class="box no-shadow no-border">

                                <div class="box-body no-b-t  dashboard-table">           
                                    <div id="charges">Charges</div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> 

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15">
    <div class="box no-bottom no-shadow" >
        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">   

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding dash-b-r-5">    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 p-r-0 margin-t-5">
                    <div class="box no-shadow no-border no-bottom">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-pie-chart"></i> Statistics</h4>
                        </div>
                        <div class="box-body no-b-t no-padding" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-15 margin-l-5 margin-b-5">

                                <h3 class="no-bottom med-darkgray dashboard-number margin-t-5 dashboard-bignumber ">                                   
                                    {!!App\Http\Helpers\Helpers::priceFormat($stats_list['api_CleanClaims']) !!}
                                    <span class="font26">%</span>
                                </h3>
                                <p class="font16 margin-b-10 med-green">Clean Claims</p>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dash-netcollection">
                                <h3 class="no-bottom med-darkgray dashboard-number dashboard-bignumber">
                                   {!!App\Http\Helpers\Helpers::priceFormat($stats_list['netcollections']) !!}  <span class="font26">%</span>
                                </h3>
                                <p class="med-green no-bottom font16">Net Collection Rate</p>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dash-ardays">
                                <h3 class="no-bottom med-darkgray dashboard-number dashboard-bignumber">                                    
                                    <?php echo $stats_list['ar_days']; ?> 
                                </h3>
                                <p class="med-green no-bottom font16">Days in AR</p>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>             
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding dash-b-r-5" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box no-shadow no-border margin-b-10">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-list-alt"></i> To Do List</h4>
                        </div>

                        <div class="box-body no-b-t  dashboard-table">           
                            <table class="table table-responsive table-stripped">                                
                                <tbody>

                                    <tr class="dash-b-t-2">
                                        <td <?php if ($stats_list['chargesHoldValue'] != 0) { ?>class="font600 med-green"<?php } ?> > Charges on Hold</td>
                                        <td <?php if ($stats_list['chargesHoldValue'] != 0) { ?>class="font600 med-red"<?php } ?>><?= $stats_list['chargesHoldValue']; ?></td>
                                        <td><i class="fa <?php if ($stats_list['chargesHoldValue'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red <?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>

                                    <tr>                                                           
                                        <td <?php if ($stats_list['ReadyToSubmitValue'] != 0) { ?>class="font600 med-green"<?php } ?>> Ready to Submit</td>
                                        <td <?php if ($stats_list['ReadyToSubmitValue'] != 0) { ?>class="font600 med-red"<?php } ?>> <?= $stats_list['ReadyToSubmitValue']; ?></td>
                                        <td><i class="fa <?php if ($stats_list['ReadyToSubmitValue'] == 0) { ?> fa-check-square-o med-green<?php } else { ?> fa-square-o med-red <?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>                               

                                    <tr>                                           
                                        <td <?php if ($stats_list['chargesDeniedValue'] != 0) { ?>class="font600 med-green"<?php } ?>> Denied Claims</td>
                                        <td <?php if ($stats_list['chargesDeniedValue'] != 0) { ?>class="font600 med-red"<?php } ?>><?php echo $stats_list['chargesDeniedValue']; ?></td>
                                        <td><i class="fa <?php if ($stats_list['chargesDeniedValue'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red<?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>

                                    <tr>                                        
                                        <td <?php if ($stats_list['chargesPendingValue'] != 0) { ?>class="font600 med-green"<?php } ?>> Pending Claims</td>
                                        <td <?php if ($stats_list['chargesPendingValue'] != 0) { ?>class="font600 med-red"<?php } ?>><?php echo $stats_list['chargesPendingValue']; ?></td>
                                        <td><i class="fa <?php if ($stats_list['chargesPendingValue'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red<?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>

                                    <tr>                                        
                                        <td <?php if ($stats_list['problem_list_count'] != 0) { ?>class="font600 med-green"<?php } ?>>Assigned Workbench</td>
                                        <td <?php if ($stats_list['problem_list_count'] != 0) { ?>class="font600 med-red"<?php } ?>>{{(@$stats_list['problem_list_count'])}}</td>
                                        <td><i class="fa <?php if ($stats_list['problem_list_count'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red<?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>
                                    <tr>                                        
                                        <td <?php if ($stats_list['document_count'] != 0) { ?>class="font600 med-green"<?php } ?>>Assigned Documents</td>
                                        <td <?php if ($stats_list['document_count'] != 0) { ?>class="font600 med-red"<?php } ?>> {{$stats_list['document_count'] }}</td>
                                        <td><i class="fa <?php if ($stats_list['document_count'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red<?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr>
                                    <tr>                                        
                                        <td <?php if ($stats_list['ediRejectionCount'] != 0) { ?>class="font600 med-green"<?php } ?>>EDI Rejections</td>
                                        <td <?php if ($stats_list['ediRejectionCount'] != 0) { ?>class="font600 med-red"<?php } ?>> {{$stats_list['ediRejectionCount'] }}</td>
                                        <td><i class="fa <?php if ($stats_list['ediRejectionCount'] == 0) { ?> fa-check-square-o med-green <?php } else { ?> fa-square-o med-red<?php } ?> font16 margin-r-10 dash-icon"></i></td>
                                    </tr> 
                                    
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box no-bottom no-shadow">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-pie-chart"></i> Aging days</h4>
                        </div>
                        <div class="box-body no-bottom p-b-0 p-t-0 p-l-0">               
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                                <div class="box no-shadow no-border no-bottom">

                                    <div class="box-body no-b-t  dashboard-table">           
                                        <div id="chart-ins">Aging Days</div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div>                      
                        </div>
                    </div>
                </div>
			</div>
			<?php /*
            <!-- 
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding dash-b-l-5">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box no-shadow no-border no-bottom">
                        <div class="box-header no-border border-radius-4 dash-bg-white">
                            <h4 class="dash-headings"><i class="fa fa-users"></i> Aging Days</h4>
                        </div>
                       
                        <div class="box no-shadow no-bottom">

                            <div class="box-body margin-t-18">
                                <div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 pull-left p-r-0 p-l-0 margin-t-m-10">
                                    <canvas id="pieChart" style="height:180px;"></canvas>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12 p-r-0 pull-left dashboard-table">
                                    <table class="table table-responsive">
                                        <tbody>
                                            <tr class="dash-b-t-2">
                                                <td><i class="fa fa-square dash-0-30"></i> 0 - 30</td>
                                                <td><?= $AgingPiePercentValue0_30; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-square dash-31-60"></i> 31 - 60</td>
                                                <td><?= $AgingPiePercentValue31_60; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-square dash-61-90"></i> 61 - 90</td>
                                                <td><?= $AgingPiePercentValue61_90; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-square dash-91-120"></i> 91 - 120</td>
                                                <td><?= $AgingPiePercentValue91_120; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-square dash-121-150"></i> 121 - 150</td>
                                                <td><?= $AgingPiePercentValue121_150; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fa fa-square dash-151"></i> 151 - 180</td>
                                                <td><?= @$AgingPiePercentValue151_180; ?>%</td>
                                            </tr>
                                            <tr class="hide">
                                                <td><i class="fa fa-square" style="color:#b5b6b7"></i> 180+</td>
                                                <td><?= $AgingPiePercentValue180_above; ?>%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> 
                        </div> 
                        
                    </div>
                </div>
            </div>
            -->
            */ ?>
        </div>
    </div>
</div>
<!-- Start sidebar notification by baskar -04/02/19-->
@if(isset(Session::all()['sidebar_notify']))
@if(isset($stats_list['workbench_by_user']) && $stats_list['workbench_by_user']!=0 || isset($stats_list['document_by_user']) && $stats_list['document_by_user']!=0 )
<span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
<div class="snackbar-alert success menu">
    <h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Users</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>      
    @if(isset($stats_list['workbench_by_user']) && $stats_list['workbench_by_user']!=0)      
        <div class='font600 col-sm-6'>Workbench</div>
        <div class="font600 col-sm-6">{{isset($stats_list['workbench_by_user'])?$stats_list['workbench_by_user']:0}}</div>
    @endif
    @if(isset($stats_list['document_by_user']) && $stats_list['document_by_user']!=0 )
        <div class="font600 col-sm-6">Documents</div>
        <div class="font600 col-sm-6">{{isset($stats_list['document_by_user'])?$stats_list['document_by_user']:0 }}</div>
    @endif
    
</div>
@endif
  <?php Session::forget('sidebar_notify','sidebar_notify'); ?>
@endif
<!-- End sidebar notification by baskar -04/02/19-->

@stop
@push('view.scripts')
{!! HTML::script('js/plugins/Chart.min.js') !!} 
<script>
    var curr_url = '{{url('analytics/providers')}}';
    
    $(document).on('ifToggled click', '.js-refresh-data-provider', function () {
        $.ajax({
            type: 'POST',
            url: api_site_url + '/dashboard/refresh',
            data: {'_token':'<?php echo csrf_token(); ?>'},
            success: function (result) {                             
                url = $(this).attr('href');
                setTimeout(function () {
                    document.location.href = curr_url;
                }, 500);
            }
        });
    });
    
<?php $i = 6000; ?>
    setTimeout(function () {
    @foreach($stats_list['result'] as $list)
            $.bootstrapGrowl('{!! $list->notes !!}', {
            type: 'info',
                    delay: <?php echo $i; ?>,
            });
<?php $i = $i + 500; ?>
    @endforeach
    }, 3000);
            $(document).ready(function(){
    if ($("#bar-chart-horizontal").length > 0) {
    new Chart(document.getElementById("bar-chart-horizontal"), {
    type: 'horizontalBar',
            height: 600,
            data: {
            labels: [<?php
foreach ($stats_list['cpt_code'] as $value) {
    $value = $value->cpt_codes;
    echo "'" . $value . "'" . ",";
}
?>],
                    datasets: [{
                    label: "CPT Count",
                            backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850", "#3e9544", "#8e33a2", "#56ba9f", "#e8c3b9", "#c45850"],
                            data: [<?php
foreach ($stats_list['cpt_code'] as $value) {
    $value = $value->order_count;
    echo "'" . $value . "'" . ",";
}
?>]
                    }]
            },
            options: {
            legend: {display: false},
                    title: {
                    display: true,
                            maintainAspectRatio: false,
                            text: 'Most Used CPT List'
                    }
            }
    });
    }
    });

            
            // ----------------- Start charges, Insurance, patient and adjustments payments -----------------------
            FusionCharts.ready(function () {
            var revenueChart = new FusionCharts({
            type: 'msstackedcolumn2d',
                    renderAt: 'charges',
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
                                    "numberScaleValue": "1000",
                                    "forceDecimals": "0",
                                    "decimals": "0",
                                    "theme": "fint1",
                                    "palette": "1",
                                    "numVisiblePlot" : "12",
                                    "bgColor": "#ffffff",
                                    "palettecolors": "#008ee4,#e45b5b,#f8bd19,#8c9ba8",
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
                        "dataset": [
                          {
                            "dataset": [
                                {
                                "seriesname": "Charges",
                                        "data": <?php echo $charge_data; ?>
                                }]
                          },
                          {
                            "dataset": [
                                {
                                "seriesname": "Pat.Payments",
                                        "data": <?php echo $pat_payment; ?>
                                },
                                {
                                "seriesname": "Ins.Payments",
                                        "data": <?php echo $ins_payment; ?>
                                },
                                {
                                "seriesname": "Adjustments",
                                        "data": <?php echo $adjsutment; ?>
                                }]
                          }
                        ],
                    }
            });

            <?php
            if($charge_data == '0' && $pat_payment == '0' && $ins_payment == '0' && $adjsutment == '0'){?>         
            revenueChart.setXMLData("<chart></chart>"); 
            revenueChart.configure("ChartNoDataText", "No Records Found");
            <?php } ?>
            revenueChart.render();
            });
            // ----------------- End Insurance, patient and adjustments payments -----------------------

            FusionCharts.ready(function () {
            var revenueChart = new FusionCharts({
            type: 'pie3d',
                    renderAt: 'chart-ins',
                    width: '100%',
                    height: '200',
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
						"numberprefix": "",
						"numbersuffix": "%",
						"toolTipColor": "#ffffff",
						"showPercentInTooltip":"0",
						"showValues":"0",
						"plotToolText": "<p>$value</p>",
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
                    "data": [{"label":"0-30", "value":"<?= $AgingPiePercentValue0_30; ?>%"},
                            {"label":"31-60", "value":"<?= $AgingPiePercentValue31_60; ?>%"},
                            {"label":"61-90", "value":"<?= $AgingPiePercentValue61_90; ?>%"},
                            {"label":"91-120", "value":"<?= $AgingPiePercentValue91_120; ?>%"},
                            {"label":"121-150", "value":"<?= $AgingPiePercentValue121_150; ?>%"},
                            {"label":"151-180", "value":"<?= @$AgingPiePercentValue151_180; ?>%"}
                            ]

                    }
            });
            <?php
            if($AgingPiePercentValue0_30 == '0' && $AgingPiePercentValue31_60 == '0' && @$AgingPiePercentValue61_90 == '0' && $AgingPiePercentValue91_120 == '0' && $AgingPiePercentValue121_150 == '0' && @$AgingPiePercentValue151_180 == '0'){?>         
            revenueChart.setXMLData("<chart></chart>"); 
            revenueChart.configure("ChartNoDataText", "No Aging Data Found");
            <?php } ?>
            revenueChart.render();
            });
            
            //
    $('.tooltipTitle').tooltip();
            
</script>
<style>
.tooltip-inner {
    white-space:pre-wrap;
}
</style>
@endpush