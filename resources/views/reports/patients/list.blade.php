@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Patient Reports</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')

<?php
$default_date = date('01/01/1990');
$start_date = date('m/01/Y');
$end_date = date('m/d/Y');
$full_date = $start_date . " - " . $end_date;
$till_date = $default_date . " - " . $end_date;
?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-8 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!-- Inner width Starts -->
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 p-l-0">
                        <h4 class="med-green margin-b-1 med-orange">Patient Reports</h4>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 hide">
                        <h4 class="med-green margin-b-1 med-orange text-right ">Saved Reports</h4>
                    </div>
                </div><!-- Inner width Ends -->                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->
                        <table class="table">
                            <tbody>
                                <tr data-url="{{ url('reports/patientdemographics') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Demographic Sheet</h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                <tr data-url="{{ url('reports/patientaddresslist') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Address Listing </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/patienticdworksheet') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> ICD Worksheet </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                <tr data-url="{{ url('reports/patientwallethistory') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Wallet History - Detailed </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/patientstatementhistory') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Statement History - Detailed </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                <tr data-url="{{ url('reports/patientstatementstatus') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Statement Status - Detailed </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/wallet-balance') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Wallet Balance </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/patient-itemized-bill') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Patient - Itemized Bill </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                <tr data-url="" class="js-table-click form-cursor hide">
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Patient Notes - Detailed </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                <tr data-url="" class="js-table-click form-cursor hide">
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Visits Without Charges </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="" class="js-table-click form-cursor hide">
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i> Payment Plans </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>

                                <!--tr data-url="{{ url('reports/patientarreport') }}" class="js-table-click form-cursor">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Patient Aging Analysis </h4></td>                                    
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr-->   
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- hide start--> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">
                                <i class="fa fa-star-o text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-user med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient Demographic</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <?php $url = url('reports/patientdemographics'); ?>
                                <div class="text-center">
                                    {!! App\Http\Helpers\Helpers::getSearchUserDate('patientdemographics',$url) !!}
                                </div>
                                <p class="text-center"><a class="generate-report" href="{{ url('reports/patientdemographics') }}">Generate</a></p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">
                                <i class="fa fa-star text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-file-powerpoint-o med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient Address List</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <p class="text-center font600"><a href=""></a></p>
                                <p class="text-center"><a class="generate-report" href="{{ url('reports/patientaddresslist') }}">Generate</a></p>
                            </div>
                        </div>        
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">
                                <i class="fa fa-star text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-file-text med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient ICD Worksheet</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <?php $url = url('reports/patienticdworksheet'); ?>
                                <div class="text-center">
                                    {!! App\Http\Helpers\Helpers::getSearchUserDate('patienticdworksheet',$url) !!}
                                </div>
                                <p class="text-center"><a class="generate-report" href="{{ url('reports/patienticdworksheet') }}">Generate</a></p>
                            </div>
                        </div>

                    </div>

                    <div class="row margin-t-20">

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">

                                <i class="fa fa-star text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-file-text med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient Wallet History</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <?php $url = url('reports/patientwallethistory'); ?>
                                <div class="text-center">
                                    {!! App\Http\Helpers\Helpers::getSearchUserDate('wallet_history',$url) !!}
                                </div>
                                <p class="text-center"><a class="generate-report" href="{{ url('reports/patientwallethistory') }}">Generate</a></p>

                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">

                                <i class="fa fa-star text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-file-text med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient Statement History</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <?php $url = url('reports/patientstatementhistory'); ?>
                                <div class="text-center">
                                    {!! App\Http\Helpers\Helpers::getSearchUserDate('statement_history',$url) !!}
                                </div>
                                <p class="text-center"><a class="generate-report" href="{{ url('reports/patientstatementhistory') }}">Generate</a></p>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block hide">
                                <i class="fa fa-star-o text-center med-orange margin-t-6 font16"></i>
                                <p class="text-center margin-t-0"><i class="fa fa-pie-chart med-green" style="font-size: 30px;"></i></p>
                                <h4 class="text-center med-darkgray">Patient Aging Analysis</h4>                                
                                <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                                <p class="text-center font600"><a href=""></a></p>
                                <p class="text-center"><a class="generate-report med-white" href="{{ url('reports/patientarreport') }}">Generate</a></p>
                            </div>
                        </div>
                    </div>
                </div><!-- hide end--> 

            </div>
        </div>
    </div>    


    <div class="col-lg-4 col-md-8 col-sm-12 col-xs-12 margin-t-m-10 hide">
        <div class="box box-view no-shadow ">

            <div class="box-body no-border">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edt', 'name'=>'medcubicsform', 'url'=>'reports/patientarlist/filter_ar']) !!}

                <h4 class="margin-b-25">Patient Aging Analysis</h4>

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <div class="form-group margin-b-20">
                                {!! Form::label('Group By', 'Transaction Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}								
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                    {!! Form::hidden('hidden_from_date', null,['tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  							                     

                            <div class="form-group margin-b-20">
                                {!! Form::label('Aging Days', 'Aging Days', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}                                                    
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-9 ">
                                    {!! Form::select('aging_days', ['all'=>'All','0-30' => '0-30','31-60'=>'31-60','61-90'=>'61-90','91-120'=>'91-120','121-150'=>'121-150','150-above'=>'> 	150'],null,['class'=>'select2 form-control  ']) !!}
                                </div> 

                            </div>
                            <div class="form-group margin-b-20">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                    {!! Form::hidden('hidden_to_date', null,['tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-10 col-xs-12 no-padding margin-b-20">
                                <input class="btn btn-medcubics js_filter_search_submit pull-right" tabindex="10" value="Search" type="submit">
                            </div>
                        </div>

                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 hide">
        <h4 class="margin-b-15 margin-t-10 med-orange">Quick Reports</h4>

        <div class="row quick-report">
            <div class="col-md-12 col-sm-12 col-12 margin-b-15">
                <div class="info-box1">
                    <span class="info-box-icon1"><i class="fa fa-area-chart med-white"></i></span>
                    <div class="info-box-content1">
                        <span class="info-box-text1">MTD - Patients Added</span>
                        <span class="info-box-number1" style="color: #dc3545;">{{ $full_date }} <a href="{{url('reports/patientdemographics?search=yes&created_at='.$full_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-12 col-sm-12 col-12 margin-b-15">
                <div class="info-box1">
                    <span class="info-box-icon1"><i class="fa fa-bar-chart med-white"></i></span>

                    <div class="info-box-content1">
                        <span class="info-box-text1">MTD - Patient Wallet</span>
                        <span class="info-box-number1" style="color: #ffc107;">{{ $full_date }}<a href="{{url('reports/patientwallethistory?search=yes&created_at='.$full_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>     

            <div class="col-md-12 col-sm-12 col-12 margin-b-15">
                <div class="info-box1">
                    <span class="info-box-icon1"><i class="fa fa-pie-chart med-white"></i></span>

                    <div class="info-box-content1">
                        <span class="info-box-text1">Total Patient Wallet</span>
                        <span class="info-box-number1" style="color: #007bff;">{{ $till_date }}<a href="{{url('reports/patientwallethistory?search=yes&created_at='.$till_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
    </div>      

</div>

<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input id="js_exit_part" class="btn btn-medcubics-small" value="Exit" type="button">
</div>

@stop
@push('view.scripts')      
<script type="text/javascript"> 

    $(window).load(function() {
        displayLoadingImage();
    });
    
    $(document).ready(function () {     
        // Shwo submit button after page render completed.
        Pace.on('done', function() {
            setTimeout(function(){
                $(".selFnBtn").removeClass("hide");
                hideLoadingImage();
                //$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false);
                //$('form#js-bootstrap-validator .js-charge_savesbt').prop('disabled', false);
            }, 1000);
        });
    
    });
</script>
@endpush 