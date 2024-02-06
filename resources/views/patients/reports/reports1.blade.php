@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-bar-chart med-breadcrum med-green" data-name="users-barchart"></i>Reports</span></small>
        </h1>
        <?php $patintid =  $patient_id; ?>
        <ol class="breadcrumb">
            @include ('patients/layouts/swith_patien_icon')
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patintid,'needdecode'=>'yes'])
@include ('patients/reports/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide" >
        <div class="box box-info no-shadow">
            <div class="box-header">
                <i class="fa fa-bars font14"></i><h3 class="box-title">List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <!-- form start -->

            <div class="box-body"><!-- Box Body Starts -->     


                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style=" border-bottom: 4px solid #f0f0f0;padding-bottom: 10px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h3 class="med-green margin-b-1 med-orange" style="font-size: 22px;">Report Type</h3>

                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <h3 class="med-green margin-b-1 med-orange" style="font-size: 22px;">Last Statement <sup><i class="fa fa-info-circle font13 med-gray-dark"></i></sup></h3>
                    </div>
                </div><!-- Inner width Ends -->   


                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 15px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h4 class="med-green margin-b-5">Patient Aging Summary</h4>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <table class="popup-table-wo-border table margin-b-1 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                                 
                                </tr>                            
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                      
                                </tr>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>             
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner width Ends -->  

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 15px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h4 class="med-green margin-b-5">Patient Transaction Summary</h4>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <table class="popup-table-wo-border table margin-b-1 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                                 
                                </tr>                            
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                      
                                </tr>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>             
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner width Ends -->  

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 15px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h4 class="med-green margin-b-5">Patient Authorization Summary</h4>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <table class="popup-table-wo-border table margin-b-1 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                                 
                                </tr>                            
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                      
                                </tr>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>             
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner width Ends -->  

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 15px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h4 class="med-green margin-b-5">Patient Statement Summary</h4>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <table class="popup-table-wo-border table margin-b-1 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                                 
                                </tr>                            
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                      
                                </tr>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>             
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner width Ends -->  


                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px; padding-top: 15px;"><!-- Inner width Starts -->  

                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                        <h4 class="med-green margin-b-5">Patient Eligibilty Summary</h4>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                        <table class="popup-table-wo-border table margin-b-1 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                                 
                                </tr>                            
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>                      
                                </tr>
                                <tr>
                                    <td class="font600">12/12/2017 - <span class="med-gray-dark">ANX</span></td>             
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner width Ends -->    

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 10px; background: #fef9f1;  border-radius: 4px;">
                    <p class="no-bottom"><span class="med-orange font600">Note:</span> Only you can preview the last 3 generated reports</p>
                </div>

            </div> <!-- Box Body Ends -->    

        </div><!-- /.box-body -->
    </div><!-- /.box -->


    <div class="col-lg-12 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!-- Inner width Starts -->  

                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <h4 class="med-green margin-b-1 med-orange">Patient Reports</h4>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-1">
                            <h4 class="med-green margin-b-1 med-orange">Saved Reports</h4>
                        </div>
                    </div><!-- Inner width Ends -->    



                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->  

                        <div class="col-lg-6 col-md-6 col-sm-7 col-xs-12">
                            <h5 class="med-green margin-b-5" style="font-size:16px;"><input type="radio" class="flat-red"> <a href="">Patient Aging Summary <i class="fa fa-angle-double-right med-orange font26"></i></a></h5>
                            <p class="" style="margin-left: 22px;"><i> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</i></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-2">
                            <table class="popup-table-wo-border table margin-b-1 margin-t-10">                    
                                <tbody>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                                 
                                    </tr>                            
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                      
                                    </tr>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>             
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner width Ends -->    

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 0px; padding-top: 0px;"><!-- Inner width Starts -->  

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h5 class="med-green margin-b-5" style="font-size:16px;"><input type="radio" class="flat-red"> <a href="#input_fields"  data-toggle = 'modal' data-target="#input_fields">Patient Eligibility Summary <i class="fa fa-angle-double-right med-orange font26"></i> </a></h5>                       
                            <p class="" style="margin-left: 22px;"><i> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</i></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-2">
                            <table class="popup-table-wo-border table margin-b-1 margin-t-10">                    
                                <tbody>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                                 
                                    </tr>                            
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                      
                                    </tr>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>             
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner width Ends -->  


                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 0px; padding-top: 0px;"><!-- Inner width Starts -->  

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h5 class="med-green margin-b-5" style="font-size:16px;"><input type="radio" class="flat-red"> <a href="">Patient Statement Summary <i class="fa fa-angle-double-right med-orange font26"></i></a></h5>                       
                            <p class="" style="margin-left: 22px;"><i> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</i></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-2">
                            <table class="popup-table-wo-border table margin-b-1 margin-t-10">                    
                                <tbody>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                                 
                                    </tr>                            
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                      
                                    </tr>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>             
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner width Ends -->  

                    
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 1px solid #bee6e2; padding-bottom: 0px; padding-top: 0px;"><!-- Inner width Starts -->  

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h5 class="med-green margin-b-5" style="font-size:16px;"><input type="radio" class="flat-red"> <a href="">Patient Transaction Summary <i class="fa fa-angle-double-right med-orange font26"></i> </a></h5>                       
                            <p class="" style="margin-left: 22px;"><i> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</i></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-2">
                            <table class="popup-table-wo-border table margin-b-1 margin-t-10">                    
                                <tbody>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                                 
                                    </tr>                            
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                      
                                    </tr>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>             
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner width Ends -->  


                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 10px; padding-top: 0px;"><!-- Inner width Starts -->  

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h5 class="med-green margin-b-5" style="font-size:16px;"><input type="radio" class="flat-red"> <a href="">Patient Authorization Summary <i class="fa fa-angle-double-right med-orange font26"></i></a></h5>                       
                            <p class="" style="margin-left: 22px;"><i> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text</i></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 col-lg-offset-2">
                            <table class="popup-table-wo-border table margin-b-1 margin-t-10">                    
                                <tbody>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                                 
                                    </tr>                            
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>                      
                                    </tr>
                                    <tr>
                                        <td class="font600"><a href=""><i> 12/12/2017 </i></a>- <span class="med-gray-dark"><i>ANX</i>  <i class="fa fa-download margin-l-20"></i></span></td>             
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner width Ends -->  

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10" style="padding: 10px; background: #fef9f1; border: 1px solid #fde9c9; border-radius: 4px;">
                        <p class="no-bottom"><span class="med-orange font600">Note:</span> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<div id="input_fields" class="js_common_modal_popup modal fade">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title margin-l-5">Patient Reports</h4>
            </div>
            <div class="modal-body">
                <div class="box-body no-bottom p-b-0"><!--Background color for Inner Content Starts -->

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding" >

                        <div class="box box-info no-shadow no-bottom">

                            <!-- form start -->
                            <div class="box-body form-horizontal">                                
                                <div class="form-group">
                                    {!! Form::label('patient_notes_type', 'Type', ['class'=>'col-lg-4 col-md-4 col-sm-3 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-12 @if($errors->first('patient_notes_type')) error @endif">
                                        {!! Form::select('patient_notes_type',[''=>'-- Select --','alert_notes' => 'Alert Notes','patient_notes' => 'Patient Notes','claim_notes'=>'Claim Notes','statement_notes'=>'Statement Notes'],null,['class'=>'select2 form-control js_patient_notes_type']) !!}
                                        {!! $errors->first('patient_notes_type', '<p> :message</p>')  !!}
                                    </div>
                                </div>  
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!--/.col (left) -->
                    
                    
                    
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding" >

                        <div class="box box-info no-shadow no-bottom">

                            <!-- form start -->
                            <div class="box-body form-horizontal">                                
                                <div class="form-group">
                                    {!! Form::label('patient_notes_type', 'Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label star']) !!} 
                                    <div class="col-lg-7 col-md-7 col-sm-12 @if($errors->first('patient_notes_type')) error @endif">
                                        {!! Form::select('patient_notes_type',[''=>'-- Select --','alert_notes' => 'Alert Notes','patient_notes' => 'Patient Notes','claim_notes'=>'Claim Notes','statement_notes'=>'Statement Notes'],null,['class'=>'select2 form-control js_patient_notes_type']) !!}
                                        {!! $errors->first('patient_notes_type', '<p> :message</p>')  !!}
                                    </div>
                                </div>  
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!--/.col (left) -->
                    
                    
                </div><!--Background color for Inner Content Ends -->	                                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 
@stop 