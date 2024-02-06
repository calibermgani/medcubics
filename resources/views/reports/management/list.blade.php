@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Management Reports</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
           <!--li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/facilityreports/'])
            </li-->
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop


@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal no-padding">
        <div class="box box-view no-shadow">
            <div class="box-body yes-border no-padding" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">          
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding reports-table js_search_part report-menu">
                    <table class="table no-bottom">
                        <thead>                        
                        <th>Name</th>
                        <th>Last Generated</th>                        
                        <th></th>                        
                        </thead>
                        <tbody>
                            <tr>                                
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Aged Trial Balance</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                                
                            </tr>
                            
                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Comparative Analysis Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                            
                            <tr>                                
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Payer Mix Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                                
                            </tr>
                            
                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Performance Management Report</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                            
                            <tr>                                
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Productivity Analysis</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                                
                            </tr>
                             <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Provider Appointments</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                             <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Credit Balance Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							 <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Daily Transaction Analysis</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                            <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Wallet Transaction Analysis</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							 
                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Authorizations List</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							
							 <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Eligibility Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>



                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Reimbursement Analysis</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							 
                            <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Deductible Report</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							 <tr>                                
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Patient Mailing Label</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                                
                            </tr>
                            <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Statement History</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							  <tr>                                
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> ANSI Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                                
                            </tr>
                              
                            <tr>                              
                                <td class="font16"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Daily EDI Reports</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                            
                              
                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-inbox font16 margin-r-10 med-green"></i> Fee Schedule</td>
                                <td>12/12/2016</td>
                                <td><a><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                            
							<tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Claims Submission Analysis</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
                          
                            <tr>                              
                                <td class="font16 med-green"><i class="fa fa-bar-chart font16 margin-r-10 med-green"></i> Rejection Report</td>
                                <td>12/12/2016</td>
                                <td><a href=""><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>
							 <tr>                              
                                <td class="font16 med-green"><i class="fa fa-file-archive-o font16 margin-r-10 med-green"></i> ICD List</td>
                                <td>12/12/2016</td>
                                <td><a><i class="fa fa-angle-double-right font26"></i></a></td>                               
                            </tr>

                        </tbody>
                    </table>
                </div>
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
