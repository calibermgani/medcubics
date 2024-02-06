@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Appointment Reports</span></small>
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
$full_date = $start_date." - ".$end_date; 
$till_date = $default_date." - ".$end_date; 

?>


<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 no-padding">
    <div class="col-lg-12 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 p-l-0">
                        <h4 class="med-green margin-b-1 med-orange">Appointment Reports</h4>
                    </div>

                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 hide">
                        <h4 class="med-green margin-b-1 med-orange text-right ">Saved Reports</h4>
                    </div>
                </div><!-- Inner width Ends -->    
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    
                    
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->  
                        <table class="table">
                            <tbody>
                                <tr data-url="{{ url('reports/appointments/appointmentanalysis') }}" class="js-table-click form-cursor ">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Appointment Analysis Report</h4></td>                                    
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>

                                <tr data-url="" class="js-table-click form-cursor hide">      
                                    <td> <h4 class="font16 med-gray"><i class="fa fa-angle-double-right med-orange font20"></i>  Appointment Status Report</h4></td>                                    
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                              
                                <tr data-url="" class="js-table-click form-cursor hide">   
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i>  Visits Status Report </h4></td>                                    
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="" class="js-table-click hide <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">   
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i>  Eligibility Status Report </h4></td>                                    
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                               
                                <tr data-url="" class="js-table-click hide <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">   
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i>  Pre-Authorization Summary </h4></td>                                    
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="" class="js-table-click hide <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">   
                                    <td> <h4 class="font16 med-gray margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i>  Appointments Reminder List </h4></td>                                    
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>

                               
                            </tbody>
                        </table>
                    </div>
                </div>
                
               <!-- hide start--> 
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide">    
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block inactivereport">
                            <i class="fa fa-star text-center med-orange margin-t-6 font16"></i>
                            <p class="text-center margin-t-0"><i class="fa fa-calendar med-green" style="font-size: 30px;"></i></p>
                            <h4 class="text-center med-darkgray">Appointment Analysis</h4>                                
                            <p class="text-center margin-t-20 margin-b-4">Saved Reports</p>
                            <p class="text-center font600"><a href=""></a></p>
                            <p class="text-center"><a class="generate-report" href="#">View</a></p>
                              <!--  <p class="text-center"><a class="generate-report" href="{{ url('reports/scheduling/appointments') }}">View</a></p> -->
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">

                            <p class="text-center margin-t-22"><i class="fa fa-area-chart med-gray" style="font-size: 28px;margin-top: 45px;"></i></p>
                            <h4 class="text-center med-gray" style="font-size: 30px;">Empty Block</h4>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-block">

                            <p class="text-center margin-t-22"><i class="fa fa-area-chart med-gray" style="font-size: 28px;margin-top: 45px;"></i></p>
                            <h4 class="text-center med-gray" style="font-size: 30px;">Empty Block</h4>

                        </div>
                    </div>
                </div>
            </div>
               <!-- hide end-->
               
            </div>
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
                    <span class="info-box-text1">MTD - Appointments</span>
                    <span class="info-box-number1" style="color: #dc3545;">{{ $full_date }}<span class="pull-right font12 generate-report" style="">Generate</span></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
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