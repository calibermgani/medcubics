@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Patient Statement </span></small>
        </h1>
        <ol class="breadcrumb">
                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/patientstatement')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
<?php $patintid = Route::current()->parameters['id']; ?>

@section('practice-info')
	@include ('patients/layouts/tabs',['tabpatientid'=>@$patintid,'needdecode'=>'yes'])
	@include ('patients/patients/budgetplan/tabs')
@stop

@section('practice')

<div class="col-lg-12 margin-t-m-10">
    <div class="box-body form-horizontal  padding-4">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                
                
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive ">
                    <h4 class="med-orange margin-t-5 font16">Patient Aging</h4>
                    <table class="popup-table-border table m-b-m-1">                    
                        <thead>                       
							<th class="font600 med-green" style="background: #c1fbf7">30</th>
							<th class="font600 med-green" style="background: #c1fbf7">60</th> 
							<th class="font600 med-green" style="background: #c1fbf7">90</th>
							<th class="font600 med-green" style="background: #c1fbf7">120</th>
							<th class="font600 med-green" style="background: #c1fbf7">Total</th>
                        </thead>
                        <tbody>
                            <tr>                               
                                <td><span> 201.00</span></td> 
                                <td>643.00</td>
                                <td>86.00</td>
                                <td>145.00</td>
                                <td class="med-orange font600">34145.00</td>
                            </tr> 
                        </tbody>
                    </table>
                </div>      
                
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive tab-l-b-1 tab-r-b-1 p-l-0  md-display tabs-lightgreen-border">
                    <table class="popup-table-wo-border table margin-b-5 margin-t-5">                    
                        <tbody>
                                                      
                            <tr>
                                <td class="font600">Total Patient Payment($)</td>
                                <td><span class="pull-right">4256.00</span></td>
                            </tr>
                            <tr>
                                <td class="font600">Last Patient Payment($)</td>
                                <td><span class="pull-right">1240.00</span></td>
                            </tr>  
                            <tr>
                                <td class="font600" style="width:50%">Last Patient Payment Date</td>
                                <td><span class="pull-right"> 12/12/2017</span></td> 
                            </tr>  
                          
                        </tbody>
                    </table>
                </div>
                                          
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive  ">
                     <h4 class="med-orange margin-t-5 font16 margin-b-4">Notes On File</h4>
                    <p class="text-justify">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    </p>
                </div>                                
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
    <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-striped table-bordered space table-separate " id="example1">
            <thead>
                <tr>    
                    <th class="td-c-2"></th>
                    <th>Date</th>                     
                    <th>Statements</th>
                    <th>Balance</th> 
                    <th>Recipient </th>
                    <th>Notes</th>
                    <th>Template</th>
                    <th>Type</th>                  
                    <th class="td-c-2"></th>
                </tr>
            </thead>
            <tbody>
                <tr>    
                    <td><input type="checkbox" class="flat-red"></td>
                    <td>11/21/2017</td>
                    <td>1</td>
                    <td>124.00</td>
                    <td>Self</td>
                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
                    <td>Template 1</td>
                    <td>Paper</td>
                    <td><i class="fa fa-file-pdf-o med-green"></i></td>
                </tr>

                <tr>
                    <td><input type="checkbox" class="flat-red"></td>
                    <td>11/21/2017</td>
                    <td>2</td>
                    <td>124.00</td>
                    <td>Self</td>
                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting</td>
                    <td>Template 3</td>
                    <td>Paper</td>
                    <td><i class="fa fa-file-pdf-o med-green"></i></td>
                </tr>

                <tr>
                    <td><input type="checkbox" class="flat-red"></td>
                    <td>11/21/2017</td>
                    <td>3</td>
                    <td>124.00</td>
                    <td>Guarantor</td>
                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem</td>
                    <td>Template 2</td>
                    <td>Electronic</td>
                    <td><i class="fa fa-file-pdf-o med-green"></i></td>
                </tr>

                <tr>
                    <td><input type="checkbox" class="flat-red"></td>
                    <td>11/21/2017</td>
                    <td>4</td>
                    <td>124.00</td>
                    <td>Self</td>
                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry</td>
                    <td>Template 1</td>
                    <td>Paper</td>
                    <td><i class="fa fa-file-pdf-o med-green"></i></td>
                </tr>

                <tr>
                    <td><input type="checkbox" class="flat-red"></td>
                    <td>11/21/2017</td>
                    <td>5</td>
                    <td>124.00</td>
                    <td>Self</td>
                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem</td>
                    <td>Template 4</td>
                    <td>Paper</td>
                    <td><i class="fa fa-file-pdf-o med-green"></i></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
@stop 