@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop med-breadcrum med-green"></i> AR Management  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Summary</span></small>
        </h1>
        <ol class="breadcrumb">                
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10"><!-- Financial Red Alert Dates Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Outstanding Col Starts -->                                                                             
            <div class="box box-view no-shadow no-border-radius no-border no-bottom no-background"><!-- Outstanding Box Starts -->
                <div class="box-header no-border no-background">
                    <div class="box-tools pull-right margin-t-m-20">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive padding-4"><!-- Outstanding Box-body Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-8">
                        <table id="" class="table table-borderless margin-b-1 bg-white" style="border-collapse:separate; border: 1px solid #00877f; border-radius: 4px;">	
                            <thead>
                                <tr style="text-align: right">
                                    <th style="background: #00877f; font-weight: 600">Aging</th>
                                    <th style="background: #00877f; text-align: right">Unbilled</th>
                                    <th style="background: #00877f;text-align: right">0-30</th>
                                    <th style="background: #00877f;text-align: right">31-60</th>
                                    <th style="background: #00877f;text-align: right">61-90</th>
                                    <th style="background: #00877f;text-align: right">91-120</th>
                                    <th style="background: #00877f;text-align: right">>120</th>
                                    <th style="background: #00877f;text-align: right">Total</th>
                                </tr>
                            </thead>
                            <tbody >



                                <tr style="text-align: right">                                
                                    <td style="background: #00877f; color:#fff; text-align: left; width: 15%;">Patient</td>
                                    <td>56.00</td>
                                    <td>4.00</td>
                                    <td>7.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                    <td>67.00</td>
                                </tr>

                                <tr style="text-align: right">                                
                                    <td style="background: #00877f; color:#fff; text-align: left">Insurance</td>
                                    <td>5.00</td>
                                    <td>47.00</td>
                                    <td>13.00</td>
                                    <td>10.00</td>
                                    <td>1.00</td>
                                    <td>0.00</td>
                                    <td>76.00</td>                             
                                </tr>

                                <tr style="text-align: right">                                
                                    <td style="background: #00877f; color:#fff; text-align: left">Outstanding</td>
                                    <td style=" color:#00877f; font-weight: 600;">61.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">51.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">20.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">10.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">1.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">0.00</td>
                                    <td style=" color:#00877f; font-weight: 600;">143.00</td>                             
                                </tr>
                                <tr style="text-align: right">                                
                                    <td style="background: #00877f; color:#fff; text-align: left"></td>
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
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <!-- Tab Starts  -->
    <?php $activetab = 'ins_claims'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'ar_summary') active @endif"><a href="{{ url('armanagement') }}" ><i class="fa fa-bank i-font-tabs"></i> Summary</a></li>           	                      	           
            <li class="@if($activetab == 'ins_claims') active @endif"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> Insurance Wise</a></li>           	                      	           
            <li class="@if($activetab == 'status_summary') active @endif"><a href="{{ url('armanagement/insurance1') }}" ><i class="fa fa-bank i-font-tabs"></i> Status Wise</a></li> 
        </ul>
    </div>
    <!-- Tab Ends -->



    <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-pat-btns margin-t-15">
        <a href="{{ url('armanagement/insclaims') }}" class="form-cursor font600 p-r-10"><i class="fa fa-file-o"></i> Review</a>

    </div>
    <div class="no-border no-shadow">
        <div class="box-body table-responsive">
            <table class="claims table table-bordered table-striped">	

                <thead>
                    <tr>
                        <th class="td-c-3"></th>
                        <th>Insurance</th>
                        <th>Unbilled</th>                                                        
                        <th>0-30</th>
                        <th>31-60</th>
                        <th>61-90</th>
                        <th>91-120</th>
                        <th>>120</th>                        
                        <th>Total</th>                       
                    </tr>
                </thead>               
                <tbody>
                    <tr>
                        <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                        <td>Cigna Health Care Insurance</td>
                        <td>5.00</td>
                        <td>47.00</td>
                        <td>113.00</td>
                        <td>10.00</td>
                        <td>1.00</td>
                        <td>0.00</td>
                        <td>76.00</td>     
                    </tr>   

                    <tr>
                        <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                        <td>Atena</td>
                        <td>12.00</td>
                        <td>22.00</td>
                        <td>13.00</td>
                        <td>42.00</td>
                        <td>23.00</td>
                        <td>0.00</td>
                        <td>23.00</td>     
                    </tr>   

                    <tr>
                        <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                        <td>Cigna Health Care Insurance</td>
                        <td>5.00</td>
                        <td>47.00</td>
                        <td>13.00</td>
                        <td>10.00</td>
                        <td>1.00</td>
                        <td>0.00</td>
                        <td>76.00</td>     
                    </tr>   

                    <tr>
                        <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                        <td>Midland National Life Ins</td>
                        <td>46.00</td>
                        <td>4.00</td>
                        <td>64.00</td>
                        <td>10.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>65.00</td>     
                    </tr>   

                    <tr>
                        <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                        <td>Thomas Cooper</td>
                        <td>5.00</td>
                        <td>47.00</td>
                        <td>13.00</td>
                        <td>10.00</td>
                        <td>1.00</td>
                        <td>0.00</td>
                        <td>76.00</td>     
                    </tr>   
                </tbody>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop