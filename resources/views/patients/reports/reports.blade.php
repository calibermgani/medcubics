@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-bar-chart med-breadcrum med-green" data-name="users-barchart"></i>Reports</span></small>
        </h1>
        <?php $patintid =  $patient_id; ?>
        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patintid)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
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

  


    <div class="col-lg-12 margin-t-m-10">
        <div class="box-body form-horizontal  bg-white">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!-- Inner width Starts -->  

                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                            <h4 class="med-green margin-b-1 med-orange">Patient Reports</h4>

                        </div>

                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                            <h4 class="med-green margin-b-1 med-orange text-right">Saved Reports</h4>
                        </div>
                    </div><!-- Inner width Ends -->    
                
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->  
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i> Patient Aging Summary : <span class="normal-font font13"><i>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</i></span></h4></td>                                    
                                    <td class="med-green font600 text-right"><span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/12/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/13/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/14/17</a></span></td>
                                </tr>
                                <tr>
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Eligibility Summary : <span class="normal-font font13"><i>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</i></span></h4></td>                                    
                                    <td class="med-green font600 text-right"><span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/12/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/13/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/14/17</a></span></td>
                                </tr>
                                
                                <tr>
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Statement Summary : <span class="normal-font font13"><i>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</i></span></h4></td>                                    
                                    <td class="med-green font600 text-right"><span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/12/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/13/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/14/17</a></span></td>
                                </tr>
                                
                                <tr>
                                    <td> <h4 class="font16 med-green margin-t-5"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Transaction Summary : <span class="normal-font font13"><i>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</i></span></h4></td>                                    
                                    <td class="med-green font600 text-right"><span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/12/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/13/17</a></span> </td>
                                </tr>
                                
                                <tr>
                                    <td> <h4 class="font16 med-green"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Authorization Summary : <span class="normal-font font13"><i>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</i></span></h4></td>                                    
                                    <td class="med-green font600 text-right"><span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/12/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/13/17</a></span> | <span class="med-green" style="padding: 0px 6px; border-radius: 4px; color: #00877f;"><a href="">12/14/17</a></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- Inner width Ends --> 
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10" style="padding: 10px; background: #fef9f1; border-radius: 4px;">
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