@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>AR Reports</span></small>
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
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 ">
                        <h4 class="med-green margin-b-1 med-orange">AR Reports</h4>
                    </div>
                </div><!-- Inner width Ends -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->
                        <table class="table">
                            <tbody>
                                <tr data-url="{{ url('reports/ar/aginganalysis') }}" class="js-table-click form-cursor">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Aging Summary </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/ar/aginganalysisdetails') }}" class="js-table-click form-cursor">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Aging Analysis- Detailed </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/ar/workbench') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> AR Workbench Report </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/ar/denialtrendanalysis') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Denial Trend Analysis </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="" class="js-table-click hide <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-gray"><i class="fa fa-angle-double-right med-orange font20"></i> AR Status Summary </h4></td>
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>                                
                                
                                <tr data-url="" class="js-table-click hide <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-gray"><i class="fa fa-angle-double-right med-orange font20"></i> Payer Mix Analysis </h4></td>
                                    <td class="med-gray font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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