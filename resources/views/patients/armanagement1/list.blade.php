@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-wheelchair"></i> AR Management </small>
        </h1>
        <ol class="breadcrumb">

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
<?php $id = Route::current()->parameters['id']; ?>
@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes'])
@stop

@section('practice')


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php $activetab = 'payments_list'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'financials') active @endif"><a href="{{ url('patients/'.$id.'/armanagement1/armanagement') }}" ><i class="fa fa-navicon i-font-tabs"></i> Financials</a></li>           	                      	           
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> List</a></li>             
            <li class="pull-right @if($activetab == 'claim_list') active @endif"><a href="" ><i class="fa fa-navicon i-font-tabs"></i> Claim List</a></li>           	                      	           
        </ul>
    </div>
    <!-- Tab Ends -->

    <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-pat-btns margin-t-15">
        <a href="{{ url('patients/'.$id.'/armanagement1/view1') }}" class="js-create-claim claimdetail form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa fa-file-o"></i> Review</a>
        <a data-toggle="modal" data-target="#notes" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa fa-sticky-note"></i> Notes</a>
        <a data-toggle="modal" data-target="#assign" class="form-cursor claimotherdetail font600 p-l-10"><i class="fa fa-user"></i> Assign</a>
    </div>
    <div class="no-border no-shadow">
        <div class="box-body table-responsive">
            <table class="claims table table-bordered table-striped">	

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
                    @foreach($claims_lists as $claim)                     
                    <tr>
                        <td><a ><input type="checkbox" class="flat-red"></a></td> 
                        <td><a data-toggle="modal" data-target="#payment_details">{{date('m/d/Y',strtotime($claim->date_of_service))}}</a></td>                    
                        <td>{{@$claim->claim_number}}</td>  
                        <td>{{str_limit(@$claim->rendering_provider->provider_name,20,' ...') }}</td>                        
                        <td>{{str_limit(@$claim->facility_detail->facility_name,20,' ...') }}</td>
                        <td>{{ str_limit(@$claim->insurance_details->insurance_name,20,' ...') }}</td>
                        <td>{{@$claim->total_charge}}</td>
                        <td>$120.00</td>                        
                        <td>$20.00</td>

                        <td class='claim-denied'>Denied</td>
                    </tr>
                    @endforeach              
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
                                <h6>Bill : <span class="med-orange">$ 450.00</span>&emsp; Paid : <span class="med-orange">$ 250.00</span>&emsp; Bal: <span class="med-orange">$ 200.00</span></h6>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border">
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
                                <table class="popup-table-wo-border table margin-b-10">                    

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
                                            <td>$800</td>
                                            <td>$600</td>
                                            <td>$200</td>
                                            <td>$80</td>                                       
                                            <td><span class="c-paid">Paid</span></td>                                   
                                        </tr>
                                        <tr>
                                            <td>04-05-2015</td>                                                                                                                                       
                                            <td>96542</td>
                                            <td>$670</td>
                                            <td>$240</td>
                                            <td>$165</td>
                                            <td>$0.00</td>                                       
                                            <td><span class="c-denied">Denied</span></td>                               
                                        </tr>                                  
                                        <tr>
                                            <td>04-05-2015</td>                                                    
                                            <td>56747</td>
                                            <td>$800</td>
                                            <td>$600</td>
                                            <td>$200</td>
                                            <td>$80</td>                                     
                                            <td><span class="c-paid">Paid</span></td>                                    
                                        </tr>
                                        <tr>
                                            <td>04-05-2015</td>                                                                                                                                
                                            <td>96542</td>
                                            <td>$670</td>
                                            <td>$240</td>
                                            <td>$165</td>
                                            <td>$0.00</td>                                      
                                            <td><span class="c-denied">Denied</span></td>                                 
                                        </tr>     

                                    </tbody>
                                </table>                    
                            </div>
                        </div>





                        



                        <div id = "view_transaction" class="collapse out col-md-12 no-padding "><!-- Inner Content for full width Starts -->
                            <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive yes-border tabs-border  no-b-t">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="bg-white med-orange padding-0-4 font600"> Transaction Details</span>
                                    </div>

                                    <table class="popup-table-wo-border table table-responsive no-bottom margin-t-15">                    
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
                                                <td>$ 23,000.00</td>                                        
                                            </tr>
                                            <tr class="cat1 med-l-green-bg" style="display:none; ">   
                                                <td></td>
                                                <td>01-10-16</td> 
                                                <td>Lorem Ipsum is simply dummy  text </td>
                                                <td>$ 400.00</td>                                        
                                            </tr>

                                            <tr class="cat1 med-l-green-bg" style="display:none;">
                                                <td></td>
                                                <td>01-12-16</td> 
                                                <td>Lorem Ipsum is simply dummy text of the printing </td>
                                                <td>$ 240.00</td>                                        
                                            </tr>    
                                            <tr class="cat1 med-l-green-bg" style="display:none;">
                                                <td></td>
                                                <td>01-18-16</td> 
                                                <td>Lorem Ipsum is simply dummy   </td>
                                                <td>$ 217.00</td>                                        
                                            </tr>

                                            <tr> 
                                                <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="2"> </a>  75478</td>
                                                <td>01-22-16</td> 
                                                <td>Lorem Ipsum is simply dummy text of the printing </td>
                                                <td>$ 40.00</td>                                        
                                            </tr>    
                                            <tr class="cat2 med-l-green-bg" style="display:none;"> 
                                                <td></td>
                                                <td>01-18-16</td> 
                                                <td>Lorem Ipsum is simply dummy   </td>
                                                <td>$ 217.00</td>                                        
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>





                            </div><!-- Inner Content for full width Ends -->
                        </div><!--Background color for Inner Content Ends -->
                        <div class="payment-links pull-right">
                            <ul class="nav nav-pills margin-t-10">
                                <li><a data-toggle = "collapse" data-target = "#view_transaction" > <i class="fa fa-file-text-o"></i> View Transaction</a></li>                                
                            </ul>
                        </div>
                    </div>

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

<!-- Modal PAyment details starts here -->
<div id="notes" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Notes</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group-billing">                                
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                                    
                                            {!! Form::textarea('insurance',null,['class'=>'form-control ar-notes-minheight','placeholder'=>'Type your Notes']) !!}
                                        </div>                                
                                    </div>
                                </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5">                                    
                            <button class="btn btn-medcubics-small margin-t-m-5 pull-right">Submit</button>
                        </div>
                    </div>

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->
<div id="assign" class="modal fade in">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"> Assign To</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                    
                    <div class="box-body form-horizontal no-padding">                        
                         
                        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block med-bg-f0f0f0"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->                           
                                    <div class="box no-border  no-shadow" ><!-- Box Starts -->
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-bg-f0f0f0"><!--  1st Content Starts -->
                                           
                                            <div class="box-body form-horizontal no-padding"><!-- Box Body Starts --> 
                                                <div class="form-group-billing margin-t-10">
                                                    {!! Form::label('Assigned To', 'Assigned To', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                        {!! Form::select('insurance', [''=>'-- Select --','Paid' => 'User 1','Cash' => 'User 2','ppaid'=>'User 3'],null,['class'=>'select2 form-control input-sm-modal-billing']) !!}
                                                    </div>                                   
                                                </div>
                                               
                                                <div class="form-group-billing">
                                                    {!! Form::label('Priority', 'Priority', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                       {!! Form::select('insurance', [''=>'-- Select --','Paid' => 'High','Cash' => 'Moderate','ppaid'=>'Low'],null,['class'=>'select2 form-control input-sm-modal-billing']) !!}
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Status', 'Status', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                       {!! Form::select('insurance', [''=>'-- Select --','Paid' => 'Assigned','Cash' => 'Inprocess','ppaid'=>'Completes'],null,['class'=>'select2 form-control input-sm-modal-billing']) !!}
                                                    </div>                                   
                                                </div>                                                                                                                                    
                                            </div><!-- /.box-body Ends-->
                                           
                                        </div><!--  1st Content Ends -->                            
                                    </div><!--  Box Ends -->

                                </div><!-- General Details Full width Ends -->
                            </div><!-- Inner Content for full width Ends -->
                         
                        </div><!--Background color for Inner Content Ends --> 
                            {!!Form::submit('Assign', ['class' => 'pull-right margin-b-6 margin-t-5 margin-r-10 btn btn-medcubics-small'])!!}
                     {!! Form::close() !!}                                           
                    </div>    
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->  
<!--End-->
@stop