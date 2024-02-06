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

@section('practice-info')
@include ('patients/layouts/tabs')
@stop

@section('practice')

<?php $id = Route::getCurrentRoute()->parameter('id'); ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php $activetab = 'wallet'; 
        	$routex = explode('.',Route::currentRouteName());
        ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'wallet') active @endif"><a href="" ><i class="fa fa-google-wallet i-font-tabs"></i> Wallet</a></li>           	                      	           
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/wallet_transaction') }}" ><i class="fa fa-navicon i-font-tabs"></i> Transaction Details</a></li>           	                      	           
        </ul>
    </div>
    <!-- Tab Ends -->

    <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-pat-btns margin-t-15">
        <span class="bg-aqua border-radius-4 padding-4-15 claimdetail form-cursor p-r-10 med-gray-dark">  <span class="med-orange font14 font600">$534.60</span> Available Balance</span>
       
    </div>
    <div class="box no-border no-shadow">
        <div class="box-body table-responsive">
            <table class="claims table table-bordered table-striped">	

                <thead>
                    <tr>                        
                        <th>Transaction Date</th>
                        <th>Transaction ID</th>                        
                        <th>Mode</th>                                                                        
                        <th>Check / MO / CC</th>
                        <th>Check / MO / CC Date</th>
                        <th>Amount</th>
                        <th>Applied</th>
                        <th>Unapplied</th>
                    </tr>
                </thead>               
                <tbody>
                    <tr>
                        <td><a data-toggle="modal" data-target="#transaction_details">04/04/2016</a></td>
                        <td>345534546</td>                        
                        <td>Money Order</td>                        
                        <td></td>
                        <td>05/06/2016</td>
                        <td>345.50</td>
                        <td>245.50</td>
                        <td>100.00</td>
                    </tr> 
                    
                    <tr>
                        <td><a data-toggle="modal" data-target="#transaction_details">04/04/2016</a></td>
						<td>345534546</td>                        
                        <td>Check</td>
                        <td>8785CH23434</td>
                        <td>05/06/2016</td>
                        <td>345.50</td>
                        <td>245.50</td>
                        <td>100.00</td>
                    </tr> 
                    
                    <tr>
                        <td><a data-toggle="modal" data-target="#transaction_details">04/04/2016</a></td>
                        <td>345534546</td>                        
                        <td>Check</td>
                        <td>8785CH23434</td>
                        <td>05/06/2016</td>
                        <td>345.50</td>
                        <td>245.50</td>
                        <td>100.00</td>
                    </tr> 
                    
                    <tr>
                        <td><a data-toggle="modal" data-target="#transaction_details">04/04/2016</a></td>
                        <td>345534546</td>                        
                        <td>Cash</td>
                        <td></td>
                        <td>05/06/2016</td>
                        <td>345.50</td>
                        <td>245.50</td>
                        <td>100.00</td>
                    </tr> 
                </tbody>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->

</div>

<!-- Modal PAyment details starts here -->
<div id="transaction_details" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Transaction Details</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-5">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-right">
                                <h6 class="pull-right">Applied : <span class="med-orange"> 250.00</span>&emsp; Unapplied: <span class="med-orange"> 200.00</span></h6>
                            </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Transaction</span>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                                <table class="popup-table-wo-border table margin-b-5">                    

                                    <thead>
                                        <tr>  
                                            <th>DOS</th>                                       
                                            <th>Claim No</th>                               
                                            <th>Billed</th>                                            
                                            <th>Applied</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>CN23244</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>100</td>                                                           
                                        </tr>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>CN23244</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>100</td>                                                           
                                        </tr>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>CN23244</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>100</td>                                                           
                                        </tr>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>CN23244</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>100</td>                                                           
                                        </tr>
                                        <tr> 
                                            <td>04-05-2015</td>                                                  
                                            <td>CN23244</td>
                                            <td>800</td>
                                            <td>600</td>
                                            <td>100</td>                                                           
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
@stop