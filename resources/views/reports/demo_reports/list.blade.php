@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Performance Reports</span></small>
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
                        <h4 class="med-green margin-b-1 med-orange">Performance Reports</h4>
                    </div>

                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 hide">
                        <h4 class="med-green margin-b-1 med-orange text-right">Saved Reports</h4>
                    </div>
                </div><!-- Inner width Ends -->                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->
                        <table class="table">
                            <tbody>
								<tr data-url="{{ url('reports/performance/monthend') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Month End Performance Summary Report </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/performance/provider') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Provider Summary by Location </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/demo/outstandingardemo') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Location Performance Summary </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                <tr data-url="{{ url('reports/performance/denials') }}" class="js-table-click form-cursor <?php if(Auth::user()->isProvider()) echo 'hide'; ?>">
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Denial and Pending Claims Summary </h4></td>
                                    <td class="med-green font600 text-right"><i class="fa fa-external-link pull-right"></i></td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div><!-- Inner width Ends -->
                    <!--div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10" style="padding: 10px; background: #fef9f1; border-radius: 4px;">
                        <p class="no-bottom"><span class="med-orange font600">Note:</span> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, </p>
                    </div-->
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-4 col-md-8 col-sm-12 col-xs-12 margin-t-m-10 hide">
        <div class="box box-view no-shadow bg-transparent">

            <div class="box-body no-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform','class'=>'setSessionData', 'url'=>'', 'data-url'=>'']) !!}

                <?php
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
					$reffering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Referring'); 
				?> 


                <h4 class="p-l-2 margin-b-25" style="">Adjustment Analysis</h4>

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            {!! Form::hidden('searchFrom', 'reportPageList') !!}
                            <div class="form-group margin-b-20">
                                {!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option', ['enter_date' => 'Choose Date','daily' => 'Today','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],null,['class'=>'select2 form-control js_change_date_option','tabindex'=>'1']) !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', null,['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  

                            <div class="form-group margin-b-20">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', null,['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_type', ['all'=>'All','insurance' => 'Insurance','patient' => 'Patient'],null,['class'=>'select2 form-control js_select_basis_change_adjusment js_select_change_adj','tabindex'=>'4', 'id' => "js_ins_adj_typ"]) !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance', [''=>'All']+(array)@$insurance,null,['class'=>'select2 form-control','tabindex'=>'6', 'id' => "js-insurance-adj"]) !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('Billing', 'Billing', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'8']) !!}
                                </div>                        
                            </div>                                

                            <div class="form-group margin-b-20">
                                {!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('adjustment_reason_id',['Patient'=>'All']+(array)@$adj_reason_patient,null,['class'=>'select2 form-control js_patient_aging js_all_hide_col','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['Insurance'=>'All']+(array)@$adj_reason_ins,null,['class'=>'select2 form-control js_insurance_aging js_all_hide_col hide','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['all'=>'All'],null,['class'=>'select2 form-control js_all_aging js_all_hide_col hide','disabled','tabindex'=>'5']) !!}
                                </div>                        
                            </div>

                            <div class="form-group margin-b-20">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('facility_id',['all'=>'All']+(array)@$facilities,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_facility",'tabindex'=>'7']) !!}
                                </div>                        
                            </div>  

                            <div class="form-group margin-b-20">
                                {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider,"all",['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'9']) !!}
                                </div>                        
                            </div>



                            <div class="col-lg-12 col-md-11 col-sm-10 col-xs-12 no-padding">
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
                        <span class="info-box-text1">MTD - Charge Analysis</span>
                        <span class="info-box-number1" style="color: #dc3545;">{{ $full_date }} <a href="{{url('reports/financials/charges?search=yes&transaction_date='.$full_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
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
                        <span class="info-box-text1">MTD - Payment Analysis</span>
                        <span class="info-box-number1" style="color: #ffc107;">{{ $full_date }} <a href="{{url('reports/financials/payments?search=yes&transaction_date='.$full_date.'&generate=yes')}}"   class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>     

            <div class="col-md-12 col-sm-12 col-12 margin-b-15">
                <div class="info-box1">
                    <span class="info-box-icon1"><i class="fa fa-pie-chart med-white"></i></span>

                    <div class="info-box-content1">
                        <span class="info-box-text1">MTD - End of Day Totals</span>
                        <span class="info-box-number1" style="color: #007bff;">{{ $full_date }} <a href="{{url('reports/financials/enddaytotal?search=yes&created_at='.$full_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-12 col-sm-12 col-12 margin-b-15">
                <div class="info-box1">
                    <span class="info-box-icon1" ><i class="fa fa-flag-o med-white"></i></span>

                    <div class="info-box-content1">
                        <span class="info-box-text1">Aging Analysis</span>
                        <span class="info-box-number1" style="color: #28a745;">{{ $till_date }} <a href="{{url('reports/financials/aginganalysisdetails?search=yes&created_at='.$till_date.'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div> 

            <div class="col-md-12 col-sm-12 col-12 ">
                <div class="info-box1">
                    <span class="info-box-icon1"><i class="fa fa-pie-chart med-white"></i></span>

                    <div class="info-box-content1">
                        <span class="info-box-text1">Year End Financials</span>
                        <span class="info-box-number1" style="">{{ date('Y') }}<a href="{{url('reports/financials/yearend?search=yes&transaction_date='.date('Y').'&generate=yes')}}" class="pull-right font12 generate-report" target="_blank"> Generate</a></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
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