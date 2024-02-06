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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php $activetab = 'ar_summary'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'ar_summary') active @endif"><a href="" ><i class="fa fa-bank i-font-tabs"></i> Insurance Aging</a></li>           	                      	           

        </ul>
    </div>
    <!-- Tab Ends -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!-- Financial Red Alert Dates Starts -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Outstanding Col Starts -->                                                                             
                <div class="box box-view no-shadow no-border-radius no-border no-bottom no-background"><!-- Outstanding Box Starts -->

                    <div class="box-body table-responsive padding-4"><!-- Outstanding Box-body Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
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
                                        <td style="background: #00877f; color:#fff; text-align: left">Patient</td>
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
                                        <td style="background: #00877f; color:#fff; text-align: left">Percentage</td>
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



    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
        <div class="box no-border no-shadow">

            <div class="box-header">
                <i class="fa fa-bars font14"></i><h3 class="box-title">Insurance Aging</h3>
                <div class="box-tools pull-right">   
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-pat-btns margin-t-15">
                <a href="{{ url('armanagement/insurance1') }}" class="form-cursor font600 p-r-10"><i class="fa fa-file-o"></i> Review</a>
            </div>
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
                        <td>13.00</td>
                        <td>10.00</td>
                        <td>1.00</td>
                        <td>0.00</td>
                        <td>76.00</td>     
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
                        <td>Cigna Health Care Insurance</td>
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



    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box no-border no-shadow">

            <div class="box-header">
                <i class="fa fa-bars font14"></i><h3 class="box-title">Status Wise Summary</h3>
                <div class="box-tools pull-right">   
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive">                               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 med-green">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 margin-t-5 p-l-0 font600">
                    <input type="radio" name="filter" class="flat-red"> All &emsp;<input type="radio" name="filter" class="flat-red"> NIS   &emsp;<input type="radio" name="filter" class="flat-red"> Paid  &emsp;
                    <input type="radio" name="filter" class="flat-red"> In Process  &emsp;<input type="radio" name="filter" class="flat-red"> Denied   &emsp;<input type="radio" name="filter" class="flat-red"> Pending
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 p-r-0">
                        <input type="text" class="form-control" placeholder="Search ...">
                    </div>
                    
                </div>
                <table class=" table table-bordered table-striped" style="border-collapse: separate;">	

                    <thead>
                        <tr>
                            <th class="td-c-3"></th>
                            <th>DOS</th>
                            <th>Claim No</th>                                                        
                            <th>Provider</th>
                            <th>Facility</th>
                            <th>Billed To</th>
                            <th>Billed Amt</th>
                            <th>Paid</th>                        
                            <th>AR Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>               
                    <tbody>
                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                        
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>Claim In Process</td>
                        </tr>  
                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                        
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>In Process</td>
                        </tr>  

                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                       
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>NIS</td>
                        </tr>  

                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                        
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>Paid</td>
                        </tr> 

                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                        
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>Denied</td>
                        </tr> 

                        <tr>
                            <td>{!! Form::checkbox('name', null, ['class'=>"chk flat-red",'data-id'=>""]) !!}</td> 
                            <td>12/03/2015</td>
                            <td>CH0023</td>
                            <td>John Willams</td>
                            <td>NJ Clinic</td>                        
                            <td>19.00</td>
                            <td>10.00</td>
                            <td>76.00</td>
                            <td>0.00</td>
                            <td>Pending</td>
                        </tr> 


                    </tbody>
                </table>

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
<!--End-->
@stop