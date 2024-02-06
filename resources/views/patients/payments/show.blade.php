@extends('admin')
@section('toolbar')
<div class="row toolbar-header" >

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="laptop"></i> Billing </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#" onclick="history.go(-1);
                    return false;"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href=""><i class="fa {{Config::get('cssconfigs.common.edit')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')

@include ('patients/eligibility/tabs')
@stop
@section('practice')
<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
            <div class="box box-view no-shadow no-bottom" style="border: 1px solid #85E2E6"><!--  Box Starts -->
                <div class="box-header-view">
                    <i class="livicon" data-name="laptop"></i> <h3 class="box-title">Billing Info</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body no-bottom">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <table class="table-responsive table-striped-view table" style="border-top:1px solid #E4FAFD; border-bottom:1px solid #E4FAFD;">                    
                           <tbody>                           
                            
                           <tr>
                                <td>Provider</td>
                                <td>Brooke Bair</td>
                            </tr>
                            
                            <tr>
                                <td>Facility</td>
                                <td>Mercy Medical Center</td>
                            </tr>
                            
                            <tr>
                                <td>Resources</td>
                                <td>Resource 1</td>
                            </tr>
                            
                            <tr>
                                <td>Insurance</td>
                                <td>Medicare</td>
                            </tr>
                            <tr>
                                <td>Insurance Type</td>
                                <td>Primary</td>
                            </tr>    
                            
                            <tr>
                                <td>Referring Provider</td>
                                <td>Emmanuel Loucas</td>
                            </tr>                           
                        </tbody>    

                           
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <table class="table-responsive table-striped-view table" style="border-top:1px solid #E4FAFD; border-bottom:1px solid #E4FAFD;">                    
                            <tbody>
                            
                            <tr>
                                <td>DOS From</td>
                                <td><span class="patient-status-bg-form label-danger" style="font-weight: 500">04-09-2015</span></td>
                            </tr>                                                                 
                            
                            <tr>
                                <td>DOS To</td>
                                <td><span class="patient-status-bg-form label-danger" style="font-weight: 500">04-09-2015</span></td>
                            </tr>
                            
                            <tr>
                                <td>Submission Date</td>
                                <td><span class="patient-status-bg-form label-success" style="background: #c3cd1d; font-weight: 500;">05-09-2015</span></td>
                            </tr> 
                            
                            <tr>
                                <td>Claim No</td>
                                <td>74247348</td>
                            </tr>
                            
                            <tr>
                                <td>Authorization</td>
                                <td>63548347</td>
                            </tr>
                            
                            <tr>
                                <td>Claim Type</td>
                                <td>Paper</td>
                            </tr>  
                            
                            <tr>
                                <td>Status</td>
                                <td><span class="patient-status-bg-form label-warning" style="font-weight: 500;">Partially Paid</span></td>
                            </tr>   
                        </tbody>
                        </table>
                    </div>
                    
                   
                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends-->
        </div>
                
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
            <div class="box box-view no-shadow " style="border: 1px solid #85E2E6">
                <div class="box-header-view">
                    <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Claim Details</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="" class="table table-bordered table-striped" style="border-collapse: separate">	
                        <thead>
                            <tr>
                                <th>Transaction Dt</th>
                                <th>Insurance</th>
                                <th>Trans Code</th>
                                <th>Payment Amount</th>
                                <th>Adjustment</th>
                                <th>Withheld</th>
                                <th>Transfer to</th>
                                <th>Reference</th>
                                <th>Status</th>                               
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">04-09-2015</a></td>
                                <td>Cigna</td>
                                <td>Self Pay Transfer</td>
                                <td>$ 205.00</td>
                                <td>$ 55.00</td>
                                <td>$ 205.00</td>
                                <td>Self Pay</td>
                                <td>Trans to bill corr ins</td>                               
                                <td>Billed</td>
                            </tr>
                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">03-28-2015</a></td>
                                <td>Empire Blue</td>
                                <td>Rebilled Claim</td>
                                <td>$ 123.00</td>
                                <td>$ 24.00</td>
                                <td>$ 123.00</td>
                                <td>Self Pay</td>
                                <td>CHK#453454 453455</td>                                
                                <td>Partially Paid</td>
                            </tr>
                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">03-13-2015</a></td>
                                <td>Cigna</td>
                                <td>Rebilled Claim</td>
                                <td>$313.00</td>
                                <td>$ 13.00</td>
                                <td>$313.00</td>
                                <td>Self Pay</td>
                                <td>Trans to bill corr ins</td>                                
                                <td>Denied</td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
            <div class="box collapsed-box box-view no-shadow " style="border: 1px solid #85E2E6">
                <div class="box-header-view">
                    <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Line Item</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="" class="table table-bordered table-striped" style="border-collapse: separate">	
                        <thead>
                            <tr>
                                <th>DOS</th>
                                <th>POS</th>                                
                                <th>Units</th>
                                <th>CPT/HCPCS</th>
                                <th>Billed</th>
                                <th>Allowed</th>
                                <th>Deducted</th>
                                <th>CoIns</th>
                                <th>CoPay</th>
                                <th>Paid</th>
                                <th>Adj</th>
                                <th>Withheld</th>
                                <th>Balance</th>
                               
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">04-16-2015</a></td>
                                <td>Hospital</td>
                                <td>2.0</td>
                                <td>23224</td>
                                <td>$ 255.00</td>
                                <td>$ 405.00</td>
                                <td>$ 45.00</td>
                                <td>NJ Clinic</td>
                                <td>$ 112.00</td>
                                <td>$ 123.00</td>
                                <td>$ 12.00</td>
                                <td>$ 52.20</td>
                                <td>$ 152.20</td>
                            </tr>
                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">04-13-2015</a></td>
                                <td>Ambulance</td>
                                <td>1.3</td>
                                <td>31253</td>
                                <td>$ 534.00</td>
                                <td>$ 256.00</td>
                                <td>$ 104.00</td>
                                <td>Mercy Medical</td>
                                <td>$ 341.00</td>
                                <td>$ 97.00</td>
                                <td>$ 53.00</td>
                                <td>$ 78.20</td>
                                <td>$ 745.20</td>
                            </tr>
                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">04-12-2015</a></td>
                                <td>Office</td>
                                <td>1.0</td>
                                <td>57432</td>
                                <td>$ 255.00</td>
                                <td>$ 405.00</td>
                                <td>$ 45.00</td>                                
                                <td>NJ Clinic</td>
                                <td>$ 112.00</td>
                                <td>$ 123.00</td>
                                <td>$ 12.00</td>
                                <td>$ 52.20</td>
                                <td>$ 152.20</td>
                            </tr>
                            
                            <tr>
                                <td><a href="{{ url('patients/billing/show') }}">04-16-2015</a></td>
                                <td>Hospital</td>
                                <td>2.0</td>
                                <td>57432</td>
                                <td>$ 546.00</td>
                                <td>$ 165.00</td>
                                <td>$ 45.00</td>
                                <td>Mercy Medical</td>
                                <td>$ 708.00</td>
                                <td>$ 177.00</td>
                                <td>$ 0.00</td>
                                <td>$ 64.20</td>
                                <td>$ 254.20</td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>        
    </div>
</div> 

@stop 