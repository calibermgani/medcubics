@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-wheelchair"></i> AR Management </small>
        </h1>
        <ol class="breadcrumb">

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/ar_management')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
    <?php 
		$activetab = 'view'; 
        $routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'payments_list') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/armanagement') }}" ><i class="fa fa-cog i-font-tabs"></i> Financials</a></li>           	                      	           
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/armanagement/list') }}" ><i class="fa fa-bars i-font-tabs"></i> Lists</a></li>           	                      	           
            <li class="@if($activetab == 'view') active @endif"><a href="" ><i class="fa fa-bars i-font-tabs"></i> View</a></li>           	                      	           
        </ul>
    </div>
    <!-- Tab Ends -->
    
    <div class="box-view no-shadow no-border"><!--  Box Starts -->                        
        <div class="box-body form-horizontal no-padding"> 
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
                <div class="payment-links pull-right">
                    <ul class="nav nav-pills">
                        <li><a data-toggle="modal" data-target="#eligibility_details"><i class="fa {{Config::get('cssconfigs.common.check')}}"></i> Eligibility</a></li>
                        <li><a ><i class="fa fa-folder-open"></i> Documents</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle med-orange" data-toggle="dropdown" href="#">
                                More
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" style="margin-left:-70px;">                            
                                <li class="form-cursor"><a data-toggle="modal" data-target="#billing1"><i class="fa {{Config::get('cssconfigs.charges.charges')}}"></i> Edit Charge</a></li>
                                <li><a><i class="fa {{Config::get('cssconfigs.payments.payments')}}"></i> Edit Payment</a></li>
                                <li class="form-cursor"><a data-toggle="modal" data-target="#billing_details"> <i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i> Billing Details</a></li>
                            </ul>
                        </li>                    
                    </ul>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white padding-10">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border margin-t-5 bg-white tabs-border border-radius-4">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
						<span class="med-orange padding-0-4 font13 margin-l-10 bg-white font600"> Claim Details</span>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
							<table class="popup-table-claims table">                    
								<tbody>
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Rend Prov</td>
										<td>John, Miller</td>                                  
									</tr>
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Ref Prov</td>
										<td>Correy Willamson</td>   
									</tr>
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Bill Prov</td>
										<td>Correy Willamson</td>                                 
									</tr>

									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Facility</td>
										<td>NJ Clinic</td>                                  
									</tr>  

								</tbody>
							</table>
						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
							<table class="popup-table-claims table">                    
								<tbody>
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">POS</td>
										<td>Ambulance</td>                                  
									</tr>
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Billed To</td>
										<td>Cigna Health Care</td>                                
									</tr>

									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Claim No</td>
										<td>5474747567</td>                                
									</tr>  
									<tr class="tab-r-b-1 green-b-c">
										<td class="font600">Claim Type</td>
										<td>Electronic</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 table-responsive" >
							<table class="popup-table-claims table">                    
								<tbody>

									<tr>
										<td class="font600">Initial Submitted</td>
										<td><span class="bg-date">24-01-2016</span></td>                            
									</tr>  
									<tr>
										<td class="font600">Last Submitted</td>
										<td><span class="bg-date">06-02-2016</span></td>
									</tr>
									<tr>
										<td class="font600">Aging Days</td>
										<td><span class="bg-number">1</span></td>                         
									</tr>
									<tr>
										<td class="font600">Status</td>
										<td><span class="med-green-o">Submitted</span></td>                                   
									</tr>

								</tbody>
							</table>
						</div>
					</div>


					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mobile-scroll table-responsive margin-t-m-13">                                                                                
						<table class="popup-table-transaction table mobile-width">                    
							<thead>
								<tr>    
									<th>DOS</th>                                   
									<th>CPT</th> 
									<th>ICD</th>
									<th>Units</th>
									<th>Billed</th>                                                    
									<th>Allowed</th>
									<th>Paid</th>
									<th>Co-Ins</th>
									<th>Co-Pay</th>
									<th>Deductible</th>
									<th>With Held</th>
									<th>Adj</th>
									<th>Remark Code</th>                                        
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="1"> </a> 04-25-2015</td>                                    
									<td>96787</td>
									<td>97985</td>
									<td>1</td>
									<td>$800</td>
									<td>$600</td>
									<td>$200</td>
									<td>$80.00</td>
									<td>$0.00</td>
									<td>$0.00</td>  
									<td>$0.00</td>
									<td>$0.00</td>
									<td>Text</td>                                        
									<td><span class="c-paid">Paid</span></td>                                   
								</tr>
								<tr class="cat1 med-l-green-bg" style="display:none;">
									<td>Primary</td>
									<td class="med-gray-dark font11" colspan="3">Cigna Healthcare</td>
									<td class="med-gray-dark">$600</td>
									<td class="med-gray-dark">$400</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark"></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr>   
								<tr class="cat1 med-l-green-bg" style="display:none;">
									<td>Secondary</td>
									<td class="med-gray-dark" colspan="3">Magna Health Insurance</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr>   
								<tr>
									<td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="2">  </a> 11-21-2015</td>
									<td>96787</td>
									<td>56747</td>
									<td>2</td>
									<td>$600</td>
									<td>$800</td>
									<td>$600</td>
									<td>$200</td>
									<td>$80</td>
									<td>$0.00</td>
									<td>$0.00</td>                                        
									<td>$0.00</td>
									<td> </td>                                        
									<td><span class="c-paid">Paid</span></td>                                     
								</tr>
								<tr class="cat2 med-l-green-bg" style="display:none;">
									<td>Primary</td>
									<td class="med-gray-dark font11" colspan="3">Cigna Healthcare</td>
									<td class="med-gray-dark">$600</td>
									<td class="med-gray-dark">$400</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark"></td>                                        
									<td class="med-gray-dark"></td>                       
								</tr>   
								<tr class="cat2 med-l-green-bg" style="display:none;">
									<td>Secondary</td>
									<td class="med-gray-dark" colspan="3">Magna Health Insurance</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td></td>                                        
									<td class="med-gray-dark"></td>                             
								</tr>   
								<tr>  
									<td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="3">  </a> 10-16-2015</td>
									<td>88245</td>
									<td>96542</td>
									<td>1</td>
									<td>$640</td>
									<td>$670</td>
									<td>$240</td>
									<td>$165</td>
									<td>$0.00</td>
									<td>$0.00</td>
									<td>$10.00</td>                                        
									<td>$0.00</td>
									<td></td>                                       
									<td><span class="c-denied">Denied</span></td>                                
								</tr>     
								<tr class="cat3 med-l-green-bg no-border" style="display:none;">
									<td>Primary</td>
									<td class="med-gray-dark" colspan="3">Cigna Healthcare</td>
									<td class="med-gray-dark">$600</td>
									<td class="med-gray-dark">$400</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark"></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr>   

								<tr>
									<td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="4"> </a> 04-25-2015</td>
									<td>96787</td>
									<td>97985</td>
									<td>1</td>
									<td>$800</td>
									<td>$600</td>
									<td>$200</td>
									<td>$80.00</td>
									<td>$0.00</td>
									<td>$0.00</td>  
									<td>$0.00</td>
									<td>$0.00</td>
									<td>Text</td>                                        
									<td><span class="c-paid">Paid</span></td>                                   
								</tr>
								<tr class="cat4 med-l-green-bg" style="display:none;">
									<td>Primary</td>
									<td class="med-gray-dark" colspan="3">Cigna Healthcare</td>
									<td class="med-gray-dark">$600</td>
									<td class="med-gray-dark">$400</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark"></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr>   
								<tr class="cat4 med-l-green-bg" style="display:none;">
									<td>Secondary</td>
									<td class="med-gray-dark" colspan="3">Magna Health Insurance</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr> 

								<tr class="ar-last-tr-bg">
									<td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="5"> </a></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="font600 med-orange">$800</td>
									<td class="font600 med-orange">$600</td>
									<td class="font600 med-orange">$200</td>
									<td class="font600 med-orange">$80.00</td>
									<td class="font600 med-orange">$0.00</td>
									<td class="font600 med-orange">$0.00</td>  
									<td class="font600 med-orange">$0.00</td>
									<td class="font600 med-orange">$0.00</td>
									<td></td>                                        
									<td></td>                                   
								</tr>
								<tr class="cat5" style="display:none;">
									<td>Primary</td>
									<td class="med-orange" colspan="3">Cigna Healthcare</td>
									<td class="med-gray-dark">$600</td>
									<td class="med-gray-dark">$400</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark"></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr>   
								<tr class="cat5" style="display:none">
									<td>Secondary</td>
									<td class="med-orange" colspan="3">Magna Health Insurance</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$200</td>
									<td class="med-gray-dark">$100</td>
									<td></td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$0.00</td>
									<td class="med-gray-dark">$10.00</td>                                        
									<td class="med-gray-dark">$0.00</td>
									<td></td>                                        
									<td class="med-gray-dark"></td>                               
								</tr> 

							</tbody>
						</table>                    
					</div>
				</div>
            </div>            
            
            
            <div class="collapse out" id = "notes">
                <div  class=" col-lg-12 col-md-12 col-sm-12 col-xs-12  bg-white p-b-12">
                <div  class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Notes Col starts -->
                    <div class=" box-view no-shadow tabs-border border-radius-4 no-bottom"><!-- VOB Box starts -->

                        <p class="med-orange no-bottom  font13 margin-l-10 margin-t-m-10"> <span class="bg-white padding-0-4 font600 ">Notes</span></p>
                        <p class="no-bottom margin-t-m-5 p-r-10 pull-right font600 form-cursor"><a data-toggle="modal" data-target="#full-notes"><i class="fa fa-arrows-alt"></i></a></p>

                        <div class="box-body chat ar-notes margin-t-m-5"><!-- Notes Box Body starts -->

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-bottom-dotted margin-t-5 p-b-5">
                                <p class="no-bottom font600"><span class="med-green">John Miller</span><span class="pull-right font12"><span class=" med-orange"><i class="fa fa-calendar-o"></i> 12 May 2016</span></span></p>
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap   Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap                                           
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-bottom-dotted margin-t-5 p-b-5">
                                <p class="no-bottom font600"><span class="med-green">George Bailey</span><span class="pull-right font12"><span class=" med-orange"><i class="fa fa-calendar-o"></i> 23 Apr 2016</span></span></p>
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap                                           
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-bottom-dotted margin-t-5 p-b-5">
                                <p class="no-bottom font600"><span class="med-green"> Correy Anderson</span><span class="pull-right font12"> <span class=" med-orange"><i class="fa fa-calendar-o"></i> 7 Apr 2016</span></span></p>
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap                                           
                            </div>                                                
                        </div><!-- Notes box-body Ends-->

                        <div class="box-body margin-t-m-5"><!-- Notes Box Body starts -->

                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 table-responsive margin-t-5 p-b-5">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group-billing">                                
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                                    
                                            {!! Form::textarea('insurance',null,['class'=>'form-control ar-notes-minheight','placeholder'=>'Type your Notes']) !!}
                                        </div>                                
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group-billing">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5">
                                            <button class="btn btn-medcubics-small margin-t-m-5 pull-right">Submit</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 table-responsive margin-t-5 p-b-5 left-border">                                                        
                                <p class="no-bottom font600 form-cursor"><a data-toggle="modal" data-target="#denial_details"><i class="fa {{Config::get('cssconfigs.common.denials')}}"></i> Denials</a></p>
                                <p class="no-bottom margin-t-5 font600 form-cursor"><a data-toggle="modal" data-target="#followup_details"><i class="fa {{Config::get('cssconfigs.patient.calendar')}}"></i> Followup Template</a></p>
                                <p class="no-bottom margin-t-5 font600 form-cursor"><a><i class="fa {{Config::get('cssconfigs.charges.voice')}}"></i> Voice</a></p>
                                <p class="no-bottom margin-t-5 font600 form-cursor"><a data-toggle="modal" data-target="#assign"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> Assign To</a></p>
                            </div>
                        </div><!-- Notes box-body Ends-->
                    </div><!-- Notes box Ends -->           
                </div><!-- Notes COl Ends -->  
                </div>
            </div>
            
 
            <div class="payment-links pull-right margin-t-10">              
                <p class="ar-claim-status"><span class="font600">Status :</span> <span class="med-orange font600">Claim In Process</span> <span class="margin-l-10 form-cursor"><a data-toggle="modal" data-target="#status_details"><i class="fa fa-edit"></i> </a></span></p>
                <ul class="nav nav-pills">
                                       
                    <li><a data-toggle="modal" data-target="#payment_details"> <i class="fa fa-file-text-o"></i> View Transaction</a></li>
                    <li><a data-toggle="collapse" data-target="#notes"> <i class="fa fa-sticky-note"></i> Notes</a></li>                                                           
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-file-pdf-o"></i> CMS 1500
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href=""><i class="fa fa-picture-o"></i> Preview</a></li>
                            <li><a href=""><i class="fa fa-file-text-o"></i> Fax</a></li>
                        </ul>
                    </li>                  
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-exclamation-triangle"></i> Hold  <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                            <li><a href=""><i class="fa fa-picture-o"></i> Claim</a></li>
                            <li><a href=""><i class="fa fa-file-text-o"></i> Statement</a></li>
                        </ul>
                    </li>
                    <li><a data-toggle="collapse" data-target="#notes1"><i class="fa fa-check"></i> Submit</a></li>
                </ul>
            </div>
            
        </div>
    </div><!-- /.box-body -->                                

   

    <div id="full-notes" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Notes</h4>
                </div>
                <div class="modal-body no-padding" >
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                        <div class="box-body no-padding form-horizontal">
                           
                                
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 border-bottom-dotted">
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"><a class="message-img-popup">{!! HTML::image('img/user-icon2.png',null,['class'=>'img-circle img-md'])!!}</a></div>
                                <div class="col-lg-11">
                                    <p class="no-bottom">                                    
                                        <a href="">DOS : 05-05-2016 </a>  
                                        <span class='notesdate'>                                               
                                            <span class="med-gray"><i class="fa fa-user"></i> John Miller</span> |  04 May 2016
                                        </span>
                                    </p>
                                    <p class=""> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 border-bottom-dotted">
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"><a class="message-img-popup">{!! HTML::image('img/profile-pic.jpg',null,['class'=>'img-circle img-md'])!!}</a></div>
                                <div class="col-lg-11">
                                    <p class="no-bottom">                                    
                                        <a href="">DOS : 05-05-2016 </a>  
                                        <span class='notesdate'>                                               
                                            <span class="med-gray"><i class="fa fa-user"></i> Correy Anderson</span> |  16 Apr 2016
                                        </span>
                                    </p>
                                    <p class="">1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap</p>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 border-bottom-dotted">
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"><a class="message-img-popup">{!! HTML::image('img/user-icon2.png',null,['class'=>'img-circle img-md'])!!}</a></div>
                                <div class="col-lg-11">
                                    <p class="no-bottom">                                    
                                        <a href="">DOS : 05-05-2016 </a>  
                                        <span class='notesdate'>                                               
                                            <span class="med-gray"><i class="fa fa-user"></i> George Bailey</span> |  11 Apr 2016
                                        </span>
                                    </p>
                                    <p class=""> Type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap</p>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 border-bottom-dotted">
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"><a class="message-img-popup">{!! HTML::image('img/profile-pic.jpg',null,['class'=>'img-circle img-md'])!!}</a></div>
                                <div class="col-lg-11">
                                    <p class="no-bottom">                                    
                                        <a href="">DOS : 05-05-2016 </a>  
                                        <span class='notesdate'>                                               
                                            <span class="med-gray"><i class="fa fa-user"></i> John Miller</span> |  23 Feb 2016
                                        </span>
                                    </p>
                                    <p class=""> Standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap</p>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->


  

    <div id="billing1" class="modal fade in">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Edit Charge</h4>
                </div>
                <div class="modal-body no-padding" >
                    <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                        <div class="box-body form-horizontal">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding margin-t-5 border-green"><!-- General Details Full width Starts -->
                                <div class="col-lg-12 col-md-8 col-sm-12 col-xs-12 no-padding"><!-- Only general details content starts -->
                                    <div class="box no-border  no-shadow"><!-- Box Starts -->
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tab-r-b-1 border-green"><!--  1st Content Starts -->
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                                                <span class="font600 bg-white padding-0-4">General Details</span>
                                            </div>
                                            <span id="ajax-charge-loader"></span>
                                            <div class="box-body form-horizontal"><!-- Box Body Starts -->
                                                <div class="form-group-billing">
                                                    {!!Form::hidden('patient_id',$patient_id)!!}
                                                    @if(empty($claims))
                                                    {!!Form::hidden('charge_add_type','billing')!!}
                                                    @endif
                                                    {!! Form::label('Rendering Provider', 'Rend Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green','id'=>'demo']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('rendering_provider_id')) error @endif">                                                      
                                                        {!! Form::select('rendering',array(''=>'--','Rendering 1'=>'Rendering 1','Cheque'=>'Rendering 2','CC'=>'Rendering 3','Moneyorder' => 'Rendering 4','Others'=>'Rendering 4'),null,['class'=>'form-control select2']) !!}
                                                    </div>                                
                                                </div>                            
                                                <div class="form-group-billing">
                                                    {!! Form::label('', 'Refe Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green','id'=>'ref_label']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('referring_provider_id')) error @endif">
                                                       {!! Form::select('referring',array(''=>'--','Referring 1'=>'Referring 1','Cheque'=>'Referring 2','CC'=>'Referring 3','Moneyorder' => 'Referring 4','Others'=>'Referring 4'),null,['class'=>'form-control select2']) !!}
                                                    </div>                                
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('Billing Provider', 'Bill Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('billing_provider_id')) error @endif">
                                                      {!! Form::select('referring',array(''=>'--','Referring 1'=>'Billing 1','Cheque'=>'Billing 2','CC'=>'Billing 3','Moneyorder' => 'Billing 4','Others'=>'Billing 4'),null,['class'=>'form-control select2']) !!}
                                                    </div>                                
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('facility_id')) error @endif">  
                                                      {!! Form::select('referring',array(''=>'--','Referring 1'=>'Facility 1','Cheque'=>'Facility 2','CC'=>'Facility 3','Moneyorder' => 'Facility 4','Others'=>'Facility 4'),null,['class'=>'form-control select2']) !!}
                                                    </div>                                           
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">  
                                                       {!! Form::select('referring',array(''=>'--','Referring 1'=>'Insurance 1','Cheque'=>'Insurance 2','CC'=>'Insurance 3','Moneyorder' => 'Insurance 4','Others'=>'Insurance 4'),null,['class'=>'form-control select2']) !!}
                                                    </div>                                                                
                                                </div>

                                                <div class="form-group-billing"> 
                                                    {!! Form::label('authorization', 'Auth No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">  
                                                        {!! Form::text('auth_no',null,['maxlength'=>'25','id'=>'authorization','class'=>'form-control input-sm-header-billing','readonly'=>'readonly']) !!}
                                                        {!! Form::hidden('authorization_id',null,['id'=>'25','id'=>'auth_id']) !!}
                                                    </div>
                                                   
                                                </div>

                                            </div><!-- /.box-body Ends-->
                                        </div><!--  1st Content Ends -->

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" ><!--  2nd Content Starts -->
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding margin-t-m-10">&emsp; </div>

                                            <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">                         

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('Admit', 'Admission', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing  med-green']) !!}                           
                                                    <div class="col-lg-5 col-md-5 col-sm-4 col-xs-5 ">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>  
                                                        {!! Form::text('admit_date',(isset($claims->admit_date) && $claims->admit_date != '1970-01-01')?@date('m/d/Y',strtotime($claims->admit_date)):'',['class'=>'form-control call-datepicker dm-date input-sm-header-billing dm-date p-r-0','placeholder'=>"From"]) !!}                                       
                                                    </div>        
                                                    {!!Form::hidden("small_date",null,['id' => 'small_date'])!!}
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 ">
                                                        {!! Form::text('discharge_date',(isset($claims->discharge_date) && $claims->discharge_date != '1970-01-01')?@date('m/d/Y',strtotime($claims->discharge_date)):'',['class'=>'form-control dm-date input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"To"]) !!}   
                                                    </div>
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('mode', 'DOI',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>                                       
                                                        {!! Form::text('doi',(@$claims->doi && $claims->doi !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claims->doi)):'',['class'=>'form-control dm-date input-sm-header-billing', 'id' => 'date_of_injury']) !!}
                                                    </div>                          
                                                </div>   

                                                <div class="form-group-billing">
                                                    {!! Form::label('Bill Cycle', 'Bill Cycle',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green p-r-0']) !!} 
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 billing-select2-disabled-yellow">
                                                        {!! Form::text('bill_cycle',@$patients->bill_cycle,['class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly','tabindex'=>'-1']) !!}
                                                    </div>
                                                    {!! Form::label('pos', 'POS',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label-billing med-green p-l-0']) !!} 
                                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-4">                                                                        
                                                        {!! Form::text('pos_name',  @$claims->pos_name, ['class'=>'form-control input-sm-header-billing', 'id' => 'pos_name' ,'readonly' => 'readonly','tabindex'=>'-1']) !!}
                                                        {!! Form::hidden('pos_code', @$claims->pos_code, ['id' => 'pos_code']) !!}
                                                    </div>    
                                                </div>



                                                <div class="form-group-billing">
                                                    {!! Form::label('Employer', 'Employer',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 billing-select2">
                                                        {!! Form::text('employer_detail', @$claims->employer_details->employer_name,['class'=>'form-control input-sm-header-billing js-remove-err autocomplete-ajax', 'id' => 'js-employer','data-url' => 'api/getreferringprovider/'.$patient_id]) !!}
                                                        {!! Form::hidden('employer_id', @$claims->employer_id,['id'=>'employer_id']) !!}
                                                        <span style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>Choose valid employer</small></span>
                                                    </div>                                 
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('Copay', 'Co-Pay',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 select2-white-popup">
                                                        {!! Form::select('copay',array(''=>'--','Cash'=>'Cash','Cheque'=>'Cheque','CC'=>'CC','Moneyorder' => 'Moneyorder','Others'=>'Others'),@$claims->copay,['class'=>'form-control select2']) !!}
                                                    </div>
                                                    {!! Form::label('pos', 'Amt',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label-billing med-green p-l-0']) !!} 
                                                    <div class="col-lg-4 col-md-4 col-sm-3 col-xs-5">                                                                        
                                                        {!! Form::text('copay_amt',(@$claims->copay_amt != 0? @$claims->copay_amt:''),['class'=>'form-control input-sm-header-billing']) !!}                    
                                                    </div>    
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('mode', 'Details',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                                    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 billing-select2">
                                                        {!! Form::text('copay_detail',@$claims->copay_detail,['class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                          
                                                </div>                                                          

                                            </div><!-- /.box-body -->
                                        </div><!--  2nd Content Ends -->
                                    </div><!--  Box Ends -->
                                </div><!-- Only general details Content Ends -->
                                <!-- Display ICD orders from E-superbill -->          
                            </div><!-- General Details Full width Ends -->


                            @if(!empty($claims)) 
							<?php 
								$icd_lists = array_flip(array_combine(range(1, count(explode(',', $claims->icd_codes))), explode(',', $claims->icd_codes)));
								$icd = App\Models\Icd::getIcdValues($claims->icd_codes); 
							?>
                            @endif
                            <div id="js-count-icd">
                                <!-- Display ICD orders from E-superbill -->          
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-green no-b-t">
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding"><!-- ICD Details Starts here -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                                        <span class="font600 bg-white padding-0-4">Diagnosis - ICD 10</span>
                                    </div>
                                    <div class="box-body form-horizontal margin-t-10">
                                        <div class="form-group-billing">                            
                                            {!! Form::label('icd1', '1',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd1',@$icd[1],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                                <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                                            </div>                                                     
                                        </div>

                                        <div class="form-group-billing">                            
                                            {!! Form::label('icd2', '2',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd2',@$icd[2],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"2"]) !!}
                                                <span id="icd2" class="icd-hover">@if(!empty($icd[2])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                                            </div>

                                        </div>

                                        <div class="form-group-billing">                            
                                            {!! Form::label('icd3', '3',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd3',@$icd[3],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"3"]) !!}
                                                <span id="icd3" class="icd-hover">@if(!empty($icd[3])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                                            </div>                                                                                         
                                        </div>

                                        <div class="js-display-err"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding">

                                    <div class="box-body form-horizontal margin-t-10">
                                        <div class="form-group-billing">                            
                                            {!! Form::label('icd4', '4',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd4',@$icd[4],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"4"]) !!}
                                                <span id="icd4" class="icd-hover">@if(!empty($icd[4])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                                            </div>                                            
                                        </div>

                                        <div class="form-group-billing">                            
                                            {!! Form::label('icd5', '5',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd5',@$icd[5],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"5"]) !!}
                                                <span id="icd5" class="icd-hover">@if(!empty($icd[5])){{App\Models\Icd::getIcdDescription($icd[5])}}@endif</span>
                                            </div>                                                                             
                                        </div>

                                        <div class="form-group-billing margin-b-5">                            
                                            {!! Form::label('icd6', '6',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd6',@$icd[6],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"6"]) !!}
                                                <span id="icd6" class="icd-hover">@if(!empty($icd[6])){{App\Models\Icd::getIcdDescription($icd[6])}}@endif</span>
                                            </div>                                          
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-8">
                                        <span class="font600">&emsp;</span>
                                    </div>
                                    <div class="box-body form-horizontal margin-t-10">
                                        <div class="form-group-billing">                                                    
                                            {!! Form::label('icd7', '7',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd7',@$icd[7],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"7"]) !!}                    
                                                <span id="icd7" class="icd-hover">@if(!empty($icd[7])){{App\Models\Icd::getIcdDescription($icd[7])}}@endif</span>
                                            </div>                       
                                        </div>

                                        <div class="form-group-billing">                                                   
                                            {!! Form::label('icd8', '8',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd8',@$icd[8],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"8"]) !!}                    
                                                <span id="icd8" class="icd-hover">@if(!empty($icd[8])){{App\Models\Icd::getIcdDescription($icd[8])}}@endif</span>
                                            </div>                             
                                        </div>

                                        <div class="form-group-billing">                                                   
                                            {!! Form::label('icd9', '9',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd9',@$icd[9],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"9"]) !!}                     
                                                <span id="icd9" class="icd-hover">@if(!empty($icd[9])){{App\Models\Icd::getIcdDescription($icd[9])}}@endif</span>
                                            </div>                                  
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 no-padding"><!-- ICD Details Starts here -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                                        <span class="font600">&emsp;</span>
                                    </div>
                                    <div class="box-body form-horizontal margin-t-10">
                                        <div class="form-group-billing">
                                            {!! Form::label('icd10', '10',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd10',@$icd[10],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"10"]) !!}                   
                                                <span id="icd10" class="icd-hover">@if(!empty($icd[10])){{App\Models\Icd::getIcdDescription($icd[10])}}@endif</span>
                                            </div>                              
                                        </div>

                                        <div class="form-group-billing">                                                   
                                            {!! Form::label('icd11', '11',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd11',@$icd[11],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"11"]) !!}                      
                                                <span id="icd11" class="icd-hover">@if(!empty($icd[11])){{App\Models\Icd::getIcdDescription($icd[11])}}@endif</span>
                                            </div>                                           
                                        </div>

                                        <div class="form-group-billing margin-b-5">                                                   
                                            {!! Form::label('icd12', '12',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                                                {!! Form::text('icd12',@$icd[12],['class'=>'form-control input-sm-header-billing js-icd','data-val'=>"12"]) !!}                 
                                                <span id="icd12" class="icd-hover">@if(!empty($icd[12])){{App\Models\Icd::getIcdDescription($icd[12])}}@endif</span>
                                            </div>     

                                        </div>    

                                    </div>
                                </div>
                                </div>
                            </div> 


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
                                <ul class="billing line-height-26 border-radius-4 no-padding mobile-width billing-charge-table" id="">
                                    <li class="billing-grid">
                                        <table class="table-billing-view">
                                            <thead>
                                                <tr>
                                                    <th class="td-c-3">&emsp;</th>
                                                    <th class="td-c-6">From</th>                                                
                                                    <th class="td-c-6">To</th>                                
                                                    <th class="td-c-8">CPT</th>
                                                    <th class="td-c-4">M1</th>
                                                    <th class="td-c-4">M2</th>
                                                    <th class="td-c-4">M3</th>    
                                                    <th class="td-c-4">M4</th>  
                                                    <th class="td-c-7">ICD Pnts</th>
                                                    <th class="td-c-3 no-padding">Un</th>
                                                    <th class="td-c-6"> ($)</th>
                                                </tr>
                                            </thead>
                                        </table>                                     
                                    </li>
                                    <!-- Display CPT from E-superbill -->
                                    <?php
										$count = 10;
										$count_cnt = 0;
										if (!empty($claims)) {

											$cpt_codes = explode(',', $claims->cpt_codes);
											$count_cnt = count($cpt_codes);
											if ($count_cnt > 6)
												$count = 10;
											$cpt_icd = explode('::', $claims->cpt_codes_icd);
										}
                                    ?>
                                    <!-- Display CPT from E-superbill -->
                                    @if(!empty($claims->dosdetails))
                                        <?php if (count($claims->dosdetails) > $count) $count = count($claims->dosdetails); ?>
                                    <div class="js-append-parent">
                                        @for($i=0;$i<$count;$i++)
                                        <?php 
											$date_to = '';
											$date_from = '';                                       
											if (!empty($claims->dosdetails[$i]->dos_to)) {
												$date_to = (@$claims->dosdetails[$i]->dos_to && $claims->dosdetails[$i]->dos_to != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claims->dosdetails[$i]->dos_to)) : '';
											}
											if (!empty($claims->dosdetails[$i]->dos_from)) {
												$date_from = (@$claims->dosdetails[$i]->dos_from && $claims->dosdetails[$i]->dos_from != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claims->dosdetails[$i]->dos_from)) : '';
											}                                        
											$icd_map = isset($claims->dosdetails[$i]->cpt_icd_map_key) ? array_combine(range(1, count(explode(',', $claims->dosdetails[$i]->cpt_icd_map_key))), explode(',', $claims->dosdetails[$i]->cpt_icd_map_key)) : '';
											$style = '';
											if ($i >= 6) {
												$style = "style = display:none;";
											}
                                        ?>
                                        <li id = "js-modifier-list-{{$i}}" class="billing-grid js-disable-div-{{$i}}" <?php echo $style; ?>>
                                            <table class="table-billing-view superbill-claim">
                                                <tbody>
                                                    <tr>
                                                        <td class="td-c-2"><input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight"></td>  
                                                        <td class="td-c-6"><input type="text" class="js_validate_date dm-date billing-noborder js_from_date" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}"   onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
                                                        <td class="td-c-6"><input type="text" class="js_validate_date dm-date billing-noborder" name=<?php echo "dos_to[" . $i . "]"; ?>  value = "{{@$date_to}}" onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
                                                        <td class="td-c-8">
                                                            <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder" value = "{{@$claims->dosdetails[$i]->cpt_code}}"
                                                                   name= <?php echo "cpt[" . $i . "]"; ?> >
                                                            <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
                                                        </td>

                                                        <td class="td-c-4">{!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier1-'.$i ]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier2-'.$i]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier3-'.$i]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier4-'.$i]) !!}</td>

                                                        <td class="td-c-23">
                                                            @for($j=1;$j<=6;$j++)
                                                            <input type="text" class="icd_pointer" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_map[$j] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>" >
															<?php echo ($j != 6) ? '<span class="billing-pipeline">|</span>' : '' ?>
                                                            @endfor                                
                                                        </td>
                                                        <td class="td-c-3"><input class="cpt_unit billing-noborder" type="text" id="<?php echo $i ?>"  maxlength = 5 name=<?php echo "unit[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->unit}}" ></td>
                                                        <td class="td-c-6"><input type="text" maxlength = 6 class = "js-charge form-control input-sm-header-billing billing-noborder" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->charge}}">
                                                            <input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
                                                            <input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_code}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()">
                                                            <input type="hidden" class="cpt_icd_map_key billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_map_key}}" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>                              
                                                    </tr>
                                                </tbody>
                                            </table>                                     
                                        </li>                
                                        @endfor
                                    </div>
                                    @else
                                    <div class="js-append-parent">                    
                                        <?php $dos_date = (!empty($claims)) ? date('m/d/Y', strtotime($claims->date_of_service)) : ''; ?>
                                        @for($i=0;$i<$count;$i++)
                                        <?php
                                        $icd_val = isset($cpt_icd[$i]) ? App\Models\Icd::getIcdValues($cpt_icd[$i]) : '';
                                        $icd_val_split = !empty($icd_val) ? implode(',', $icd_val) : '';
                                        $icd_map = isset($cpt_icd[$i]) ? array_combine(range(1, count(explode(',', $cpt_icd[$i]))), explode(',', $cpt_icd[$i])) : '';
                                        $style = '';
                                        if ($i >= 6) {
                                            $style = "style = display:none;";
                                        }
                                        ?>
                                        <li id = "js-modifier-list-{{$i}}" class="billing-grid js-disable-div-{{$i}}" <?php echo $style; ?>>
                                            <table class="table-billing-view superbill-claim">
                                                <tbody>
                                                    <tr>
                                                        <td class="td-c-2" tabindex="0"><input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight flat-red dm-date"></td>  
                                                        <td class="td-c-6"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date js_from_date dm-date billing-noborder" name=<?php echo "dos_from[" . $i . "]"; ?>  onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
                                                        <td class="td-c-6"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date dm-date billing-noborder" name=<?php echo "dos_to[" . $i . "]"; ?>  onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
                                                        <td class="td-c-8">
                                                            <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder" tabindex = -1 value = "<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('cpt_hcpcs') : ''; ?>" 
                                                                   name= <?php echo "cpt[" . $i . "]"; ?> >
                                                            <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">

                                                        <td class="td-c-4">{!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier1-'.$i]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier2-'.$i]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier3-'.$i ]) !!}</td>
                                                        <td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier4-'.$i]) !!}</td>

                                                        <td class="td-c-7">
                                                            <?php $a = array();
                                                            $cpt_icd_key = ''
                                                            ?>
                                                            @for($j=1;$j<=4;$j++)
                                                            <input type="text" class="icd_pointer billing-icd-pointers" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_lists[$icd_map[$j]] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
															<?php echo ($j != 4) ? ' <span class="billing-pipeline-popup">|</span>' : '' ?>
															<?php
															if (!empty($icd_map[$j]))
																$key = array_push($a, $icd_lists[$icd_map[$j]]);
															?>
                                                            @endfor             
                                                        </td>
                                                        <td class="td-c-3"><input class="cpt_unit billing-noborder" value= "<?php echo isset($cpt_codes[$i]) ? 1 : '' ?>" maxlength = 5 type="text" id="<?php echo $i ?>" name=<?php echo "unit[" . $i . "]"; ?> ></td>
                                                        <td class="td-c-6"><input type="text" class = "js-charge billing-noborder" maxlength = 6 id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value="<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('billed_amount') : ''; ?>">
                                                            <input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$icd_val_split}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()"></td>
                                                <input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
                                                <input type="hidden" class="cpt_icd_map_key billing-nb" value = "<?php echo!empty($a) ? implode(',', $a) : ''; ?>" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>                              
                                                </tr>
                                                </tbody>
                                            </table>                                     
                                        </li>                
                                        @endfor
                                    </div>
                                    @endif
									<?php
										$display_class = 'style="display:none;"';
										if ($count_cnt >= 6 || !empty($claims->dosdetails) && count($claims->dosdetails) >= 6) {
											$display_class = '';
										}
									?>
                                    {!!Form::hidden('appentvalue', $i,['id' => 'js-appendrow'])!!}
                                </ul>

                                <div class="margin-t-m-8 margin-b-5">
                                    <span class="append cur-pointer font600 med-green" <?php echo $display_class; ?>><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i> Add</span>                
                                </div>
                            </div>

                            <div class="pull-right"> 
                                <span class=" med-green font600" >Total Charges ($) : </span>
                                <span class="med-orange font600 margin-l-20">  0.00<input type="text" readonly = "readonly"name = "total_charge" class="js-total billing-noborder text-right td-c-50"></span>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-13">
                                {!! Form::textarea('note',null,['class'=>'form-control minheight-50','placeholder'=>'Notes']) !!}
                            </div>                        


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 no-padding">
                                <div class="payment-links">                                             
								@if(!empty($claims) && $claims->charge_add_type != 'esuperbill')
									<?php $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims->id,'encode'); ?>            
									<ul class="nav nav-pills  pull-right">                   
										<li><a class=" claimotherdetail font600" onClick="window.open('{{url('/getcmsform/'.$id)}}', '_blank')"> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}"></i> CMS 1500</a></li>
										 
										<li><a href="#" data-toggle="modal" data-target="#" class=" claimotherdetail font600" data-url=""> <i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a></li> 
									</ul>            
								 @endif
								</div>            
                            </div>

                            <div class="box-footer space20">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    {!! Form::hidden('batch_no',(@$claims->batch_no)?@$claims->batch_no:App\Http\Helpers\Helpers::getRandonCharacter(),['maxlength'=>'25','id'=>'batch_no']) !!}
                                    {!! Form::hidden('batch_date',(@$claims->batch_date && $claims->batch_date !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claims->batch_date)):date('m/d/Y'),['maxlength'=>'25','class'=>'form-control input-sm-header-billing','readonly'=>'readonly']) !!}
                                    {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small padding-2-8']) !!}
                                    @if(empty($claims))
                                    {!! Form::hidden('is_create',1) !!}
                                    {!! Form::hidden('is_from_charge',null,['class' => 'js-charge-input']) !!}
                                    {!! Form::hidden('batch_id',null,['class' => 'js-batch-input']) !!}
                                    @endif        
                                    <!-- <a href="{{url('patients/billing')}}">{!! Form::button('Save', ['class'=>'btn btn-medcubics js-save-charge']) !!}</a> -->      
                                    <a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/billing')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small js_cancel_site']) !!}</a>
                                    @if(!empty($claims) && $claims->status = 'Ready')
                                    <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
                                    <a href="{{(strpos($currnet_page, 'charge') !== false)? url('charges/delete/'.$claims->id):url('patients/billing/delete/'.$claims->id)}}">
                                        {!! Form::button('Delete', ['class'=>'btn btn-medcubics-small']) !!}</a>  
                                    @endif 

                                </div>	  
                            </div><!-- /.box-footer -->
                        </div>
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

<!-- Modal PAyment details starts here -->
<div id="payment_details" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Transaction Details</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal p-b-0">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-10">
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                <h6>Billed : <span class="med-green">Empire Blue</span></h6>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
                                <h6>Bill : <span class="med-orange">$ 450.00</span>&emsp; Paid : <span class="med-orange">$ 250.00</span>&emsp; Bal: <span class="med-orange">$ 200.00</span></h6>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-b-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 padding-0-4 font13 font600"> Claim Details</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Rend Prov</td>
                                            <td>George Willamson</td>  
                                            <td class="med-green font600">Status</td>
                                            <td>Submitted</td> 
                                        </tr>
                                        <tr>
                                            <td class="font600">Bill Prov</td>
                                            <td>George Willamson</td>
                                            <td class="med-green font600">Claim Type</td>
                                            <td>Electronic</td>                                           
                                        </tr>

                                        <tr>
                                            <td class="font600">Facility</td>
                                            <td>NJ Clinic</td> 
                                            <td class="med-green font600">DOI</td>
                                            <td><span class="bg-date">12-12-2015</span></td> 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-t-m-13">
                                <table class="popup-table-wo-border table table-responsive margin-b-5">                    
                                <thead>
                                    <tr>    
                                        <th>CPT</th>
                                        <th>Date</th>                                
                                        <th>Description</th>                               
                                        <th>Amount</th>
                                        <th>Pat Bal</th>
                                        <th>Ins Bal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>     
                                        <td class="td-c-13"><a href="#" class="toggler font600 toggle-plus" data-prod-cat="3"> </a> 00176</td>
                                        <td class="td-c-13">12-12-15</td> 
                                        <td>Lorem Ipsum is simply dummy </td>
                                        <td class="td-c-12">224.00</td>
                                        <td class="td-c-12">0.00</td>
                                        <td class="td-c-12">224.00</td>
                                    </tr>
                                    <tr class="cat3 med-l-green-bg" style="display:none;">    
                                        <td></td>
                                        <td>01-10-16</td> 
                                        <td>Lorem Ipsum is simply dummy  text of the printing </td>
                                        <td>400.00</td>
                                        <td>0.00</td>
                                        <td>400.00</td>
                                    </tr>

                                    <tr class="cat3 med-l-green-bg" style="display:none;">  
                                        <td></td>
                                        <td>01-12-16</td> 
                                        <td>Lorem Ipsum is simply dummy text of the printing and typesetting </td>
                                        <td>240.00</td>  
                                         <td>0.00</td>
                                        <td>240.00</td>
                                    </tr>    
                                    <tr> 
                                        <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="4"> </a> 00210</td>
                                        <td>01-18-16</td> 
                                        <td>simply dummy  text of the printing   </td>
                                        <td>217.00</td>
                                         <td>17.00</td>
                                        <td>200.00</td>
                                    </tr>

                                    <tr class="cat4 med-l-green-bg" style="display:none;"> 
                                        <td></td>
                                        <td>01-22-16</td> 
                                        <td>Lorem Ipsum is simply dummy text of the printing </td>
                                        <td>40.00</td> 
                                         <td>40.00</td>
                                        <td>0.00</td>
                                    </tr>    
                                    
                                    <tr> 
                                        <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="5"> </a> 00210</td>
                                        <td>01-18-16</td> 
                                        <td>Ipsum is simply dummy text of the printing    </td>
                                        <td>217.00</td>
                                         <td>17.00</td>
                                        <td>200.00</td>
                                    </tr>

                                    <tr class="cat5 med-l-green-bg" style="display:none;"> 
                                        <td></td>
                                        <td>01-22-16</td> 
                                        <td>Lorem Ipsum is simply dummy text of the printing </td>
                                        <td>40.00</td> 
                                         <td>40.00</td>
                                        <td>0.00</td>
                                    </tr>
                                    
                                    <tr> 
                                        <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="6"> </a> 00210</td>
                                        <td>01-18-16</td> 
                                        <td>Simply dummy  text of the printing   </td>
                                        <td>217.00</td>
                                         <td>17.00</td>
                                        <td>200.00</td>
                                    </tr>

                                    <tr class="cat6 med-l-green-bg" style="display:none;"> 
                                        <td></td>
                                        <td>01-22-16</td> 
                                        <td>Lorem Ipsum is simply dummy text of the printing </td>
                                        <td>40.00</td> 
                                         <td>40.00</td>
                                        <td>0.00</td>
                                    </tr>
                                    
                                    <tr> 
                                        <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="7"> </a> 00210</td>
                                        <td>01-18-16</td> 
                                        <td>Lorem Ipsum is simply dummy text   </td>
                                        <td>217.00</td>
                                         <td>17.00</td>
                                        <td>200.00</td>
                                    </tr>

                                    <tr class="cat7 med-l-green-bg" style="display:none;"> 
                                        <td></td>
                                        <td>01-22-16</td> 
                                        <td>Lorem Ipsum is simply dummy text of the printing </td>
                                        <td>40.00</td> 
                                         <td>40.00</td>
                                        <td>0.00</td>
                                    </tr>    
                                </tbody>
                            </table>                  
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->


<!-- Modal Payment details starts here -->
<div id="billing_details" class="modal fade in">
    <div class="modal-md-550">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Billing Details</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Practice Info</span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-10" >
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Name</td>
                                            <td>Prime Health Medical PC</td>                                              
                                        </tr>                                       
                                        <tr>
                                            <td class="font600">Tax ID</td>
                                            <td>200155109</td>                                             
                                        </tr> 
                                         <tr>
                                             <td class="font600">NPI</td>
                                            <td>1265637284</td>                                                                                       
                                        </tr>
                                        <tr>
                                            <td class="font600">Specialty</td>
                                            <td>Internal Medicine</td>                                             
                                        </tr>  
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10" >
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td class="font600 med-green">Pay to Address</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>29 Hamilton PL</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Garden City - NY 11530 - 5922</td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>                         
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Facility Info</span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Name</td>
                                            <td>Franklin Hospital Medical</td>                                              
                                        </tr>                                       
                                        
                                         <tr>
                                             <td class="font600">NPI</td>
                                            <td>1265637284</td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td class="font600 med-green">Address</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>29 Hamilton PL</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Garden City - NY 11530 - 5922</td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>                         
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Provider Info</span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive  m-b-m-10">
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Rendering Prov</td>
                                            <td>George Bailey</td>                                              
                                        </tr>                                       
                                        <tr>
                                            <td class="font600">Tax ID</td>
                                            <td>200155109</td>                                             
                                        </tr> 
                                         <tr>
                                             <td class="font600">NPI</td>
                                            <td>1265637284</td>                                                                                       
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-10">
                                <table class="popup-table-wo-border table">                    
                                    <tbody>
                                        <tr>
                                            <td class="font600">Referring Prov</td>
                                            <td>David Willams</td>                                              
                                        </tr>                                                                              
                                         <tr>
                                             <td class="font600">NPI</td>
                                            <td>1265637284</td>                                                                                       
                                        </tr>                                                                                                     
                                    </tbody>
                                </table>
                            </div>                         
                        </div>
                        
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-15">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Insurance Info</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ar-bottom-border">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-15">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Primary Ins</td>
                                                <td>Fidelis Care New York</td>                                              
                                            </tr>                                       
                                            <tr>
                                                <td class="font600">Policy ID</td>
                                                <td>74037714600</td>                                             
                                            </tr> 
                                            <tr>
                                                <td class="font600">Group Name</td>
                                                <td>-- Nil --</td>                                                                                       
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive m-b-m-15">
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Phone</td>
                                                <td>(231) 3424 353</td>                                              
                                            </tr>                                                                              
                                            <tr>
                                                <td class="font600">Payer ID</td>
                                                <td>1265637284</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive" >
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Secondary Ins</td>
                                                <td>First Priority (Bcbs Ne Penn)</td>                                              
                                            </tr>                                       
                                            <tr>
                                                <td class="font600">Policy ID</td>
                                                <td>IXKAN3907407</td>                                             
                                            </tr> 
                                            <tr>
                                                <td class="font600">Group Name</td>
                                                <td>-- Nil --</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 table-responsive" >
                                    <table class="popup-table-wo-border table">                    
                                        <tbody>
                                            <tr>
                                                <td class="font600">Phone</td>
                                                <td>(231) 3424 353</td>                                              
                                            </tr>                                                                              
                                            <tr>
                                                <td class="font600">Payer ID</td>
                                                <td>1265637284</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>                                                        
                        </div>
                    </div>
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

<!-- Modal PAyment details starts here -->
<div id="denial_details" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Denials : CN3435435 </h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                                                                                                                        
                    <div class="box-body form-horizontal p-b-0">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-5">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Denial Info</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-b-10">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'Denial Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">
                                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'Check No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">                                           
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                    <div class="form-group-billing">
                                        {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">  
                                            
                                        </div>                                                                
                                    </div>
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">                                           
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>
                                </div>                                 
                            </div>                          
                        </div>
                    </div>
                    
                    
                    <div class="box-header-view bg-white  margin-t-10">                                                                        
                        <div class="input-group input-group-sm">
                            <input name="denial" type="text" class="form-control" placeholder="Search Denial Codes">
                            <span class="input-group-btn">
                                <button class="btn btn-flat btn-medgreen" type="button">Search</button>
                            </span>
                        </div>   
                    </div><!-- /.box-header -->
                    
                    <div class="box-body table-responsive chat ar-denials no-padding margin-t-m-5"><!-- Notes Box Body starts -->
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
                        <ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
                            <li class="denials">
                                <table class="table-striped-view">
                                    <tbody>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                                <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td style="width: 10%">A0</td>
                                            <td style="width: 85%">Patient refund amount.</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>A5</td>
                                            <td>Medicare Claim PPS Capital Cost Outlier Amount.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>A6</td>
                                            <td>Prior hospitalization or 30 day transfer requirement not met.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>B1</td>
                                            <td>Non-covered visits.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>B13</td>
                                            <td>Previously paid. Payment for this claim/service may have been provided in a previous payment.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>B23</td>
                                            <td>Procedure billed is not authorized per your Clinical Laboratory Improvement Amendment (CLIA) proficiency test.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>P15</td>
                                            <td>Workers' Compensation Medical Treatment Guideline Adjustment. To be used for Workers' Compensation only.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>M129</td>
                                            <td>Missing/incomplete/invalid indicator of x-ray availability for review.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>M132</td>
                                            <td>Missing pacemaker registration form.</td>
                                        </tr>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">                                               
                                               <input type="checkbox" class="flat-red">
                                            </td>                                                
                                            <td>M142</td>
                                            <td>Missing American Diabetes Association Certificate of Recognition.</td>
                                        </tr>
                                    </tbody>
                                </table>                                     
                            </li>	
                        </ul>
                    </div>
                                                                     
                    </div><!-- Notes box-body Ends-->
                    <div class="box-header-view-white ar-bottom-border text-center">                                                                        
                       {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small padding-2-8']) !!}
                       {!! Form::submit('Cancel', ['class'=>'btn btn-medcubics-small padding-2-8' ,'data-dismiss'=>"modal"]) !!}
                    </div><!-- /.box-header -->
                    
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->


<!-- Modal PAyment details starts here -->
<div id="followup_details" class="modal fade in">
    <div class="modal-md-550">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Followup</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                                                                                                                        
                    <div class="box-body form-horizontal p-b-0">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-5">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> General</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-b-10">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                                    <div class="form-group-billing">
                                        {!! Form::label('Billed To', 'Insurance', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">  
                                           
                                        </div>                                                                
                                    </div>
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'Phone', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">                                           
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>                                    
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">                                    
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'Rep Name', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">                                           
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>
                                    <div class="form-group-billing">
                                        {!! Form::label('', 'DOS', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">
                                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                            {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                        </div>                                
                                    </div>
                                </div>                                 
                            </div>
                        </div>
                    </div>
                   
                    <div class="box-body table-responsive  no-padding margin-t-m-5"><!-- Notes Box Body starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                            <label><input type="radio" name="colorRadio"  value="claim_nis"> Claim NIS</label>
                            <label style="margin-left:40px;"><input type="radio" name="colorRadio"  value="claim_in_process"> Claim In Process</label>
                            <label style="margin-left:40px;"><input type="radio" name="colorRadio"  value="claim_paid"> Claim Paid</label>
                            <br>
                            <label><input type="radio" name="colorRadio"  value="claim_denied"> Claim Denied</label>
                            <label style="margin-left:17px;"><input type="radio" name="colorRadio" value="left_voice_message"> Left Voice Message</label>
                            <label style="margin-left:23px;"><input type="radio" name="colorRadio"  value="others"> Others</label>

                            <div class="claim_nis followup-box">
                                <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Not In System</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the effective date of policy?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'From']) !!}
                                                </div>   
                                               
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'To']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. Select your category', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Primary</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> Secondary</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="ter"> Tertiary</label>
                                                </div>                                                   
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the filling limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Do you accept claims thru fax?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> No</label>                                                  
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Can I have the fax number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Who\'s attention should I send the fax?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7. What\'s electronic payer ID?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What\'s the claim mailing address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                                                                                                                    
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                     <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div> 
                                            
                                            <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                                                    
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                                   
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            <div class="claim_in_process followup-box">
                                 <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim In Process</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. When did you receive the claim', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the processing time?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. When shall I callback to check the status?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Is there any claim or reference number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> No</label> &emsp;
                                                </div>                                
                                            </div>
                                                
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal hide">
                                                <div class="form-group-billing">
                                                    {!! Form::label('', 'Claim #', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                        {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                
                                                </div>                                                                               
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal hide">                                    
                                                <div class="form-group-billing">
                                                    {!! Form::label('', 'Reference #', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                        {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                
                                                </div> 
                                            </div>
                                           
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                             <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                    
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="claim_paid followup-box">
                                 <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Paid</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                        <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
													<label><input type="radio" name="colorRadio1" value="paid_to_provider"> Paid to Provider</label> &emsp;
													<label><input type="radio" name="colorRadio1" value="paid_to_patient"> Paid to Patient</label>
                                                </div>                                
                                            </div>
                                        </div>
                                        <div class="paid_to_provider followup-box1 col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the paid amount', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                   
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the paid date?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the allowed amount?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                                
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Can I have a copy of EOB?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Is there any Co-Insurance?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Is there any Co-Pay?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7. Is there any deductible?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What is the check number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Is it a single or bulk check?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Single</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> Bulk</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9a. If bulk, whats the bulk check amount?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '10. Is it cashed?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '10a. If yes, cashed date?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">   
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '11. Whats the pay to address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                       
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>
                                      
                                    </div>
                                </div>
                            </div>
                            
                            <div class="claim_denied followup-box">
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Denied</span>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <ul style="list-style-type:none; margin-left: -45px; line-height: 26px;">
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing CPT</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing POS</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Modifier</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Policy ID</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Diagnosis</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Patient DOB</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Provider No</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Provider NPI</li>
                                                <li><input type="checkbox" name="" class="flat-red">No Coverage on DOS</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need W9 Form</li>
                                                <li><input type="checkbox" name="" class="flat-red">Covered by Another Payer</li>
                                                <li><input type="checkbox" name="" class="flat-red">Additional Info from Patient</li>
                                                <li><input type="checkbox" name="" class="flat-red">Additional Info from Doctor</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need CLIA #</li>                                                
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <ul style="list-style-type:none; margin-left: -45px; line-height: 26px;">
                                                <li><input type="checkbox" name="" class="flat-red">Duplicate</li>
                                                <li><input type="checkbox" name="" class="flat-red">Primary EOB</li>
                                                <li><input type="checkbox" name="" class="flat-red">Authorization</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need Referral</li>
                                                <li><input type="checkbox" name="" class="flat-red">Untimely Filing</li>
                                                <li><input type="checkbox" name="" class="flat-red">COB Information</li>
                                                <li><input type="checkbox" name="" class="flat-red">Medical Records</li>
                                                <li><input type="checkbox" name="" class="flat-red">Inclusive Procedure</li>
                                                <li><input type="checkbox" name="" class="flat-red">Coverage Terminated</li>
                                                <li><input type="checkbox" name="" class="flat-red">Pre-Existing Condition</li>
                                                <li><input type="checkbox" name="" class="flat-red">Non Covered as per PT Plan</li>
                                                <li><input type="checkbox" name="" class="flat-red">Non Covered as per Provider Contract</li>
                                                <li><input type="checkbox" name="" class="flat-red">Others</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the denied date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                   
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the filing limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>                                                                                        
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the appeal limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. What is the appeal address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                                
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Is there any claim or reference no?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', 'If yes', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Claim No']) !!}
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Reference No']) !!}
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Is patient policy active?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7.What is the electronic payer ID?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What is the claim mailing address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                              
                                            </div>
                                                                                        
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                       
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>                                      
                                    </div>
                                </div>
                            </div>
                            
                            <div class="left_voice_message followup-box">
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Left Voice Message</span>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Type your details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>
 
                                    </div>
                                </div>
                            </div>
                            
                            <div class="others followup-box">
                                
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Others</span>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                                            
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">                                                       
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Search ...']) !!}
                                                </div>  
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <span class="med-orange font600 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Add New</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  margin-b-5">                                                                                        
                                            <ul style="list-style-type:none; margin-left:-40px;">
                                                <li><label><input type="radio" name="status" value=""> Adjustment approval</label></li>
                                                <li><label><input type="radio" name="status" value=""> Appeals sent</label></li>
                                                <li><label><input type="radio" name="status" value=""> Approved to pay</label></li>
                                                <li><label><input type="radio" name="status" value=""> Bundled</label></li>
                                                <li><label><input type="radio" name="status" value=""> Capitation</label></li>
                                                <li><label><input type="radio" name="status" value=""> Claim in process</label></li>
                                                <li><label><input type="radio" name="status" value=""> CLIA# Missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> COB </label></li>
                                                <li><label><input type="radio" name="status" value=""> Completed</label></li>
                                                <li><label><input type="radio" name="status" value=""> Coverage terminated</label></li>
                                                <li><label><input type="radio" name="status" value=""> Deductible</label></li>
                                                <li><label><input type="radio" name="status" value=""> Demographic missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Duplicate claim</label></li>
                                                <li><label><input type="radio" name="status" value=""> EDI rejection</label></li>
                                                <li><label><input type="radio" name="status" value=""> Follow up</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance credit</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance hold</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance Info missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance refund</label></li>
                                                <li><label><input type="radio" name="status" value=""> Medical records request</label></li> 
                                                <li><label><input type="radio" name="status" value=""> Medically not necessary</label></li>
                                                <li><label><input type="radio" name="status" value=""> Medicare crossover</label></li> 
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  margin-b-5">                                                                                        
                                            <ul style="list-style-type:none; margin-left:-40px;">
                                                <li><label><input type="radio" name="status" value=""> NDC# missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Non covered services</label></li>
                                                <li><label><input type="radio" name="status" value=""> Out of network benefits</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paid to patient</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paid waiting for EOB</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paper Claim</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient credit</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient refund</label></li>
                                                <li><label><input type="radio" name="status" value=""> Payer rejection</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending appeals</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending collections</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending credentials</label></li>
                                                <li><label><input type="radio" name="status" value=""> Prior authorization</label></li>
                                                <li><label><input type="radio" name="status" value=""> Ready</label></li>
                                                <li><label><input type="radio" name="status" value=""> Referral# missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Resubmitted</label></li>
                                                <li><label><input type="radio" name="status" value=""> Secondary billed</label></li>
                                                <li><label><input type="radio" name="status" value=""> Statement hold</label></li>
                                                <li><label><input type="radio" name="status" value=""> Submitted</label></li> 
                                                <li><label><input type="radio" name="status" value=""> TFL crossed</label></li>
                                            </ul>
                                        </div>                                                                                
                                    </div>
                                </div>
                            </div>
                        </div>
                                                                     
                    </div><!-- Notes box-body Ends-->
                    <div class="box-header-view-white ar-bottom-border text-center">                                                                        
                       {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small','style'=>'padding:2px 16px;']) !!}
                       {!! Form::submit('Cancel', ['class'=>'btn btn-medcubics-small','style'=>'padding:2px 16px;','data-dismiss'=>"modal"]) !!}
                    </div><!-- /.box-header -->
                    
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

<!-- Modal PAyment details starts here -->
<div id="status_details" class="modal fade in">
    <div class="modal-md-550">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change Status</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->                   
                    <div class="box-body table-responsive  no-padding margin-t-m-5"><!-- Notes Box Body starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                            <label><input type="radio" name="colorRadio"  value="claim_nis"> Claim NIS</label>
                            <label style="margin-left:40px;"><input type="radio" name="colorRadio"  value="claim_in_process"> Claim In Process</label>
                            <label style="margin-left:40px;"><input type="radio" name="colorRadio"  value="claim_paid"> Claim Paid</label>
                            <br>
                            <label><input type="radio" name="colorRadio"  value="claim_denied"> Claim Denied</label>
                            <label style="margin-left:17px;"><input type="radio" name="colorRadio" value="left_voice_message"> Left Voice Message</label>
                            <label style="margin-left:23px;"><input type="radio" name="colorRadio"  value="others"> Others</label>

                            <div class="claim_nis followup-box">
                                <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Not In System</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the effective date of policy?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'From']) !!}
                                                </div>   
                                               
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'To']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. Select your category', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Primary</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> Secondary</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="ter"> Tertiary</label>
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the filling limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Do you accept claims thru fax?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Can I have the fax number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Who\'s attention should I send the fax?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7. What\'s electronic payer ID?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What\'s the claim mailing address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div>

                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                     <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                                   
                                            </div> 
                                            
                                            <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">                                                    
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                                   
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            <div class="claim_in_process followup-box">
                                 <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim In Process</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. When did you receive the claim', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the processing time?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. When shall I callback to check the status?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!}
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Is there any claim or reference number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
                                                    <label><input type="radio" name="colorRadio" value="sec"> No</label> &emsp;
                                                </div>                                
                                            </div>
                                                
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal hide">
                                                <div class="form-group-billing">
                                                    {!! Form::label('', 'Claim #', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                        {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                
                                                </div>                                                                               
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal hide">                                    
                                                <div class="form-group-billing">
                                                    {!! Form::label('', 'Reference #', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                        {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                
                                                </div> 
                                            </div>
                                           
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                             <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                    
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="claim_paid followup-box">
                                 <div class="box-body form-horizontal no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Paid</span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                        <div class="form-group-billing">                                                
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
													<label><input type="radio" name="colorRadio1" value="paid_to_provider"> Paid to Provider</label> &emsp;
													<label><input type="radio" name="colorRadio1" value="paid_to_patient"> Paid to Patient</label>
                                                </div>                                
                                            </div>
                                        </div>
                                        <div class="paid_to_provider followup-box1 col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the paid amount', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                   
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the paid date?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the allowed amount?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                                
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. Can I have a copy of EOB?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Is there any Co-Insurance?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Is there any Co-Pay?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7. Is there any deductible?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What is the check number?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Is it a single or bulk check?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Single</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> Bulk</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9a. If bulk, whats the bulk check amount?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '10. Is it cashed?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                        <label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
                                                        <label><input type="radio" name="colorRadio" value="sec"> No</label>                                                                              
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '10a. If yes, cashed date?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">   
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '11. Whats the pay to address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                       
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>
                                        
                                       
                                    </div>
                                </div>
                            </div>
                            
                            <div class="claim_denied followup-box">
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Claim Denied</span>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <ul style="list-style-type:none; margin-left: -45px; line-height: 26px;">
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing CPT</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing POS</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Modifier</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Policy ID</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Diagnosis</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Patient DOB</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Provider No</li>
                                                <li><input type="checkbox" name="" class="flat-red">Incorrect / Missing Provider NPI</li>
                                                <li><input type="checkbox" name="" class="flat-red">No Coverage on DOS</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need W9 Form</li>
                                                <li><input type="checkbox" name="" class="flat-red">Covered by Another Payer</li>
                                                <li><input type="checkbox" name="" class="flat-red">Additional Info from Patient</li>
                                                <li><input type="checkbox" name="" class="flat-red">Additional Info from Doctor</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need CLIA #</li>                                                
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            <ul style="list-style-type:none; margin-left: -45px; line-height: 26px;">
                                                <li><input type="checkbox" name="" class="flat-red">Duplicate</li>
                                                <li><input type="checkbox" name="" class="flat-red">Primary EOB</li>
                                                <li><input type="checkbox" name="" class="flat-red">Authorization</li>
                                                <li><input type="checkbox" name="" class="flat-red">Need Referral</li>
                                                <li><input type="checkbox" name="" class="flat-red">Untimely Filing</li>
                                                <li><input type="checkbox" name="" class="flat-red">COB Information</li>
                                                <li><input type="checkbox" name="" class="flat-red">Medical Records</li>
                                                <li><input type="checkbox" name="" class="flat-red">Inclusive Procedure</li>
                                                <li><input type="checkbox" name="" class="flat-red">Coverage Terminated</li>
                                                <li><input type="checkbox" name="" class="flat-red">Pre-Existing Condition</li>
                                                <li><input type="checkbox" name="" class="flat-red">Non Covered as per PT Plan</li>
                                                <li><input type="checkbox" name="" class="flat-red">Non Covered as per Provider Contract</li>
                                                <li><input type="checkbox" name="" class="flat-red">Others</li>                                                                                             
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5">
                                            <div class="form-group-billing">
                                                {!! Form::label('', '1. What is the denied date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                   
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '2. What is the filing limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                    
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                
                                            </div>                                                                                        
                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '3. What is the appeal limit?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '4. What is the appeal address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                  
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                                
                                            <div class="form-group-billing">
                                                {!! Form::label('', '5. Is there any claim or reference no?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', 'If yes', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Claim No']) !!}
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                       {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Reference No']) !!}                                                                           
                                                </div>                                
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '6. Is patient policy active?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
													<label><input type="radio" name="colorRadio" value="pri"> Yes</label> &emsp;
													<label><input type="radio" name="colorRadio" value="sec"> No</label>
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '7.What is the electronic payer ID?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                               
                                            </div>
                                            <div class="form-group-billing">
                                                {!! Form::label('', '8. What is the claim mailing address?', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                              
                                            </div>                                            
                                            <div class="form-group-billing">
                                                {!! Form::label('', '9. Call back date', ['class'=>'col-lg-8 col-md-8 col-sm-8 col-xs-12 control-label-billing med-green ']) !!} 
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">                                                       
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing']) !!}
                                                </div>                                 
                                            </div>
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Other Details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                            
                            <div class="left_voice_message followup-box">
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Left Voice Message</span>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                            
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                                                       
                                                    {!! Form::textarea('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Type your details']) !!}
                                                </div>                                 
                                            </div>
                                        </div>
 
                                    </div>
                                </div>
                            </div>
                            
                            <div class="others followup-box">
                                
                                <div class="box-body form-horizontal no-padding ">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Others</span>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                                            
                                            <div class="form-group-billing">                                               
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">                                                       
                                                    {!! Form::text('refering_provider', null,['class'=>'form-control input-sm-header-billing','placeholder'=>'Search ...']) !!}
                                                </div>  
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                    <span class="med-orange font600 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Add New</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  margin-b-5">                                                                                        
                                            <ul style="list-style-type:none; margin-left:-40px;">
                                                <li><label><input type="radio" name="status" value=""> Adjustment approval</label></li>
                                                <li><label><input type="radio" name="status" value=""> Appeals sent</label></li>
                                                <li><label><input type="radio" name="status" value=""> Approved to pay</label></li>
                                                <li><label><input type="radio" name="status" value=""> Bundled</label></li>
                                                <li><label><input type="radio" name="status" value=""> Capitation</label></li>
                                                <li><label><input type="radio" name="status" value=""> Claim in process</label></li>
                                                <li><label><input type="radio" name="status" value=""> CLIA# Missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> COB </label></li>
                                                <li><label><input type="radio" name="status" value=""> Completed</label></li>
                                                <li><label><input type="radio" name="status" value=""> Coverage terminated</label></li>
                                                <li><label><input type="radio" name="status" value=""> Deductible</label></li>
                                                <li><label><input type="radio" name="status" value=""> Demographic missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Duplicate claim</label></li>
                                                <li><label><input type="radio" name="status" value=""> EDI rejection</label></li>
                                                <li><label><input type="radio" name="status" value=""> Follow up</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance credit</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance hold</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance Info missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Insurance refund</label></li>
                                                <li><label><input type="radio" name="status" value=""> Medical records request</label></li> 
                                                <li><label><input type="radio" name="status" value=""> Medically not necessary</label></li>
                                                <li><label><input type="radio" name="status" value=""> Medicare crossover</label></li> 
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  margin-b-5">                                                                                        
                                            <ul style="list-style-type:none; margin-left:-40px;">
                                                <li><label><input type="radio" name="status" value=""> NDC# missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Non covered services</label></li>
                                                <li><label><input type="radio" name="status" value=""> Out of network benefits</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paid to patient</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paid waiting for EOB</label></li>
                                                <li><label><input type="radio" name="status" value=""> Paper Claim</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient credit</label></li>
                                                <li><label><input type="radio" name="status" value=""> Patient refund</label></li>
                                                <li><label><input type="radio" name="status" value=""> Payer rejection</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending appeals</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending collections</label></li>
                                                <li><label><input type="radio" name="status" value=""> Pending credentials</label></li>
                                                <li><label><input type="radio" name="status" value=""> Prior authorization</label></li>
                                                <li><label><input type="radio" name="status" value=""> Ready</label></li>
                                                <li><label><input type="radio" name="status" value=""> Referral# missing</label></li>
                                                <li><label><input type="radio" name="status" value=""> Resubmitted</label></li>
                                                <li><label><input type="radio" name="status" value=""> Secondary billed</label></li>
                                                <li><label><input type="radio" name="status" value=""> Statement hold</label></li>
                                                <li><label><input type="radio" name="status" value=""> Submitted</label></li> 
                                                <li><label><input type="radio" name="status" value=""> TFL crossed</label></li>                                                 
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- Notes box-body Ends-->
                    <div class="box-header-view-white ar-bottom-border text-center">                                                                        
                       {!! Form::submit('Save', ['class'=>'btn btn-medcubics-small','style'=>'padding:2px 16px;']) !!}
                       {!! Form::submit('Cancel', ['class'=>'btn btn-medcubics-small','style'=>'padding:2px 16px;']) !!}
                    </div><!-- /.box-header -->
                    
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
                                                    {!! Form::label('Followup Date', 'Followup Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">     
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing font12"></i>
                                                        {!! Form::text('followup_date',date('m/d/Y'),['class'=>'form-control dm-date']) !!}
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



<!-- Modal PAyment details starts here -->
<div id="eligibility_details" class="modal fade in">
    <div class="modal-md-650">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Eligibility Details</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body">                      
						<div class="table-responsive">
							<table id="example2" class="table table-bordered table-striped ">         
								<thead>
									<tr>
										<th>DOS</th>
										<th>Insurance</th>            
										<th>DOB</th>
										<th>EDI</th>
										<th>Benefit Verification</th>
									</tr>
								</thead>
								<tbody>
								   
									<tr class="clsCursor">
										<td><a href="">12/12/16</a></td>        
										<td>Cigna Health Care</td>
										<td>06/23/1978</td>        
										<td><a target = "_blank" href= "" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"><i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i></a></td> 
										<td><a target = "_blank" href= "" data-toggle="tooltip" data-original-title="View more" data-placement="bottom"><i class="fa {{Config::get('cssconfigs.patient.file_text')}}"></i></a></td> 
									</tr>                    
								</tbody>
							</table>
						</div>      

                    </div>

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal Payment Details ends here -->

@stop

@push('view.scripts')
<script type="text/javascript">
    $('#authorization').attr('autocomplete','off');
</script>
@endpush