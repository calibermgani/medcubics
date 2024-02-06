@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="money"></i> Payments </small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
    @include ('patients/layouts/tabs')
@stop

@section('practice')
<?php $id = Route::getCurrentRoute()->parameter('id'); ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php $activetab = 'payments_list'; 
        	$routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'financials') active @endif"><a href="{{ url('patients/'.$id.'/payments') }}" ><i class="fa fa-navicon i-font-tabs"></i> List</a></li>           	                      	           
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> Transaction Details</a></li>                          	           
        </ul>
    </div>
    <!-- Tab Ends -->
    
    <div class="box no-border no-shadow">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0 padding-t-20">
            <div href="" class="col-lg-5 col-md-5 col-sm-5 col-xs-12 font600 pull-right no-bottom" style="text-align: right"> Available Balance : <span class="med-orange font14">534.60</span></div> 
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  form-horizontal">
            
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <div class="form-group-billing">
                    {!! Form::label('Unapplied', 'From', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">    
                        <i class="fa fa-calendar-o form-icon-billing"></i>
                        {!! Form::text('insurance',null,['class'=>' form-control input-sm-header-billing']) !!}
                    </div>                                   
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <div class="form-group-billing">
                {!! Form::label('Unapplied', 'To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600','style'=>'text-align:right']) !!}                                                  
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">    
                    <i class="fa fa-calendar-o form-icon-billing"></i>
                    {!! Form::text('insurance',null,['class'=>' form-control input-sm-header-billing']) !!}
                </div>                                   
            </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <div class="form-group-billing">
                {!! Form::label('mode', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600','style'=>'text-align:right']) !!}
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">                                    
                    {!! Form::select('stmt_add', ['Check' => 'Check','Cash' => 'Cash','EFT' => 'Credit Card','money_order' => 'Money Order'],null,['class'=>'select2 form-control']) !!}
                </div>
            </div>  
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 margin-t-m-8">
                {!! Form::submit('Get Statement', ['class'=>'btn btn-medcubics-small','style'=>'padding:2px 16px;']) !!}
            </div>            
        </div> 
        </div>
        <div class="box-body table-responsive">
            <table id="example2" class=" table table-bordered table-striped">	

                <thead>
                    <tr>                        
                        <th>Transaction Date</th>
                        <th>Transaction Details</th>
                        <th>Check No</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance</th>
                        
                    </tr>
                </thead>               
                <tbody>
                    <tr>
                        <td>06/14/2016</td>
                        <td>Amount Credited for the claim CH342325</td>
                        <td>4366456</td>
                        <td>345.50</td>                        
                        <td></td>
                        <td>600.50</td>
                    </tr> 
                    <tr>
                        <td>06/14/2016</td>
                        <td>Amount Debited for the claim CH342325</td>
                        <td>4366456</td>
                        <td></td>                        
                        <td>200.00</td>
                        <td>400.50</td>
                    </tr> 
                    <tr>
                        <td>06/14/2016</td>
                        <td>Amount Debited for the claim CH342325</td>
                        <td>4366456</td>
                        <td></td>                        
                        <td>250.00</td>
                        <td>150.50</td>
                    </tr> 
                    <tr>
                        <td>06/14/2016</td>
                        <td>Amount Credited for the claim CH342325</td>
                        <td>4366456</td>
                        <td>345.50</td>                        
                        <td></td>
                        <td>496.00</td>
                    </tr> 
                    <tr>
                        <td>06/14/2016</td>
                        <td>Amount Debited for the claim CH342325</td>
                        <td>4366456</td>
                        <td></td>                        
                        <td>324.50</td>
                        <td>171.50</td>
                    </tr> 
                </tbody>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>


<!-- Modal PAyment details starts here -->
<div id="payment_details" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Details</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal">



                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-5 margin-b-10">
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                <h6>Billed : <span class="med-green">Empire Blue</span></h6>
                            </div>




                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
                                <h6>Bill : <span class="med-orange"> 450.00</span>&emsp; Paid : <span class="med-orange"> 250.00</span>&emsp; Bal: <span class="med-orange"> 200.00</span></h6>
                            </div>



                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-b-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Claim Details</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Rend Prov</td>
                                            <td>{{@$claim->rendering_provider->provider_name}}</td>  
                                            <td class="med-green font600">Status</td>
                                            <td><span class="">Submitted</span></td> 
                                        </tr>
                                        <tr>
                                            <td class="font600">Bill Prov</td>
                                            <td>{{@$claim->billing_provider->provider_name}}</td>
                                            <td class="med-green font600">Claim Type</td>
                                            <td>Electronic</td>                                           
                                        </tr>
                                        <tr>
                                            <td class="font600">Facility</td>
                                            <td>{{@$claim->facility_detail->facility_name}}</td> 
                                            <td class="med-green font600">DOI</td>
                                            <td><span class="bg-date">12-12-2015</span></td> 
                                        </tr>   
                                        <tr>
                                            <td class="font600">Ref Prov</td>
                                            <td>{{@$claim->refering_provider->provider_name}}</td>  
                                            <td class="med-green font600">Claim No</td>
                                            <td>Ebill4t1</td>
                                        </tr>
                                        <tr>
                                            <td class="font600">Auth #</td>
                                            <td>43763456</td>
                                            <td class="med-green font600">Submitted Dt</td>
                                            <td><span class="bg-date">03-03-2016</span></td>
                                        </tr>  
                                        <tr>
                                            <td class="font600">Primary ICD</td>
                                            <td>95643</td>  
                                            <td class="med-green font600">Last Submitted Dt</td>
                                            <td><span class="bg-date">04-02-2016</span></td>  
                                        </tr>                                                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-t-m-13">
                                <table class="popup-table-wo-border table margin-b-5">                    

                                    <thead>
                                        <tr>  
                                            <th>DOS</th>                                       
                                            <th>CPT</th>                               
                                            <th>Billed</th>
                                            <th>Allowed</th>
                                            <th>Paid</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>56747</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>200</td>
                                            <td>80</td>                                       
                                            <td><span class="c-paid">Paid</span></td>                                   
                                        </tr>
                                        <tr>
                                            <td>04-05-2015</td>                                                                                                                                       
                                            <td>96542</td>
                                            <td>670</td>
                                            <td>240</td>
                                            <td>165</td>
                                            <td>0.00</td>                                       
                                            <td><span class="c-denied">Denied</span></td>                               
                                        </tr>                                  
                                        <tr>
                                            <td>04-05-2015</td>                                                    
                                            <td>56747</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>200</td>
                                            <td>80</td>                                     
                                            <td><span class="c-paid">Paid</span></td>                                    
                                        </tr>
                                        <tr>
                                            <td>04-05-2015</td>       
                                            <td>96542</td>
                                            <td>670</td>
                                            <td>240</td>
                                            <td>165</td>
                                            <td>0.00</td>                                      
                                            <td><span class="c-denied">Denied</span></td>                                 
                                        </tr>    
                                    </tbody>
                                </table>                    
                            </div>
                        </div>

                        <div class="payment-links pull-right margin-t-m-10">
                            <ul class="nav nav-pills">
                                <li><a data-toggle = "collapse" data-target = "#view_transaction" > <i class="fa fa-file-text-o"></i> View Transaction</a></li>
                            </ul>
                        </div>
                        <div id = "view_transaction" class="collapse out col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive yes-border tabs-border">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="bg-white med-orange padding-0-4"> Transaction Details</span>
                                    </div>

                                    <table class="popup-table-wo-border table table-responsive">                    
                                        <thead>
                                            <tr> 
                                                <th>CPT</th>
                                                <th>Date</th>                                
                                                <th>Description</th>                               
                                                <th>Amt</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>      
                                                <td> <a href="#" class="toggler font600 toggle-plus" data-prod-cat="1"> </a> 99088</td>
                                                <td>12-12-15</td> 
                                                <td>Lorem Ipsum is simply dummy </td>
                                                <td> 23,000.00</td>                                        
                                            </tr>
                                            <tr class="cat1 med-l-green-bg" style="display:none; ">   
                                                <td></td>
                                                <td>01-10-16</td> 
                                                <td>Lorem Ipsum is simply dummy  text </td>
                                                <td> 400.00</td>                                        
                                            </tr>

                                            <tr class="cat1 med-l-green-bg" style="display:none;">
                                                <td></td>
                                                <td>01-12-16</td> 
                                                <td>Lorem Ipsum is simply dummy text of the printing </td>
                                                <td> 240.00</td>                                        
                                            </tr>    
                                            <tr class="cat1 med-l-green-bg" style="display:none;">
                                                <td></td>
                                                <td>01-18-16</td> 
                                                <td>Lorem Ipsum is simply dummy   </td>
                                                <td> 217.00</td>                                        
                                            </tr>
                                            <tr> 
                                                <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="2"> </a>  75478</td>
                                                <td>01-22-16</td> 
                                                <td>Lorem Ipsum is simply dummy text of the printing </td>
                                                <td> 40.00</td>                                        
                                            </tr>    
                                            <tr class="cat2 med-l-green-bg" style="display:none;"> 
                                                <td></td>
                                                <td>01-18-16</td> 
                                                <td>Lorem Ipsum is simply dummy   </td>
                                                <td> 217.00</td>                                        
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- Inner Content for full width Ends -->
                        </div><!--Background color for Inner Content Ends -->
                    </div>
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->
<!--End-->
@stop