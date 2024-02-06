@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-laptop med-breadcrum med-green"></i> AR Management  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Insurance Wise</span></small>
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


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <!-- Tab Starts  -->
    <?php 
		$activetab = 'cigna'; 
        $routex = explode('.',Route::currentRouteName());
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">                       	           
            <li class="@if($activetab == 'status_summary') active @endif"><a href="{{ url('armanagement/insurancewise') }}" ><i class="fa fa-bank i-font-tabs"></i> Insurance Wise</a></li> 
            <li class="@if($activetab == 'cigna') active @endif"><a href="" ><i class="fa fa-bank i-font-tabs"></i> Cigna ..</a></li> 
        </ul>
    </div>
    <!-- Tab Ends -->

    <div class="no-border no-shadow">
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
<!--End-->
@stop