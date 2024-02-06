{!!  Form::open(['onsubmit'=>"event.preventDefault();",'name'=>'v2-authorizationeditform_'.@$authorization->id,'id'=>'v2-authorizationeditform_'.@$authorization->id,'class'=>'v2-authorization-info-form medcubicsform']) !!}

<?php  
	$authorization_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$authorization->id,'encode');
	$patient_id_encode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$authorization->patient_id,'encode');  
?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.authorization") }}' />
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-border no-shadow p-l-2 p-r-2 bg-white">
    <div class="col-lg-12 margin-t-10 no-padding"  style="border-bottom: 2px solid #f0f0f0;">
        <div class="box-body form-horizontal">
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">                                                                
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 table-responsive">
                        <h4 class="med-darkgray margin-t-5"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Authorization   {{$authorization_count_v2}}</h4>
                        <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
                                <div id="add-form-value">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  form-horizontal"><!-- Left side Content Starts -->
                                        <div class="form-group margin-b-10">
                                            {!! Form::label('insurance name', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                                                {!! Form::select('auth_insurance_id',array('' => '-- Select --') +(array)$patient_insurances,@$authorization->insurance_id,['class'=>'select2 form-control insurance_id js-sel-insurance-address','id'=>'auth_insurance_id']) !!} 
                                            </div>
                                        </div>

                                        <div class="form-group margin-b-10">
                                            {!! Form::label('Place of Service', 'POS', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10 @if($errors->first('type')) error @endif">
                                                {!! Form::select('pos_id', array('' => '-- Select --') + (array)$pos,@$authorization->pos_id,['class'=>'select2 form-control','id'=>'pos_id']) !!}   
                                            </div>
                                        </div>
                                        <div class="form-group margin-b-10">
                                            {!! Form::label('Authorization Number', 'Auth No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">  
                                                {!! Form::text('authorization_no',@$authorization->authorization_no,['autocomplete'=>'off' ,'class'=>'form-control','maxlength'=>'29','id'=>'authorization_no-'.@$authorization->id]) !!} 
                                            </div>

                                            <div class="col-md-1 col-sm-1 col-xs-2 p-l-0">
                                                <a id="document_add_modal_link_authorization_number" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::Authorization::'.@$id.'/'.@$patient_id_encode_id.'/Authorization_Documents_Pre_Authorization_Letter')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}} <?php echo(isset($authorization_auth->category)?'icon-orange-attachment':'icon-green-attachment') ?>"></i></a>
                                            </div>

                                        </div>
                                        <div class="form-group margin-b-10">
                                            {!! Form::label('Start Date', 'Start Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                                                <i class="fa fa-calendar-o form-icon"></i>     
                                                {!! Form::text('start_date',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$authorization->start_date),['class'=>'form-control form-cursor auth_datepicker dm-date','autocomplete'=>'off', 'id'=>'start_date-'.@$authorization->id,'placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                            </div>
                                        </div>
                                        <div class="form-group margin-b-10">
                                            {!! Form::label('End Date', 'End Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                                                <i class="fa fa-calendar-o form-icon"></i>   
                                                {!! Form::text('end_date',App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$authorization->end_date),['class'=>'form-control form-cursor auth_datepicker dm-date','id'=>'end_date-'.@$authorization->id,'placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                            </div>
                                        </div>                           
                                        <div class="form-group margin-b-10" @if(@$registration->allowed_visit !=1) style="display:none;" @endif>
                                             {!! Form::label('Allowed Visits', 'Allowed Visits', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                             <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                                {!! Form::text('allowed_visit',@$authorization->allowed_visit,['class'=>'form-control js-visit dm-auth-visits','id'=>'allowed_visit-'.@$authorization->id,'autocomplete'=>'off']) !!}
                                            </div>                                
                                        </div>
                                        <?php /* Notes Added MED-2509 */ ?>
                                        <div class="form-group margin-b-10" @if(@$registration->allowed_visit !=1) style="display:none;" @endif>
                                             {!! Form::label('Auth Notes', 'Auth Notes', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                             <div class="col-lg-4 col-md-5 col-sm-7 col-xs-10">
                                                {!! Form::textarea('authorization_notes', @$authorization->authorization_notes, ['class' => 'form-control','maxlength'=>500]) !!}   
                                            </div>                                
                                        </div>
                                        <!-- 
                                         <div class="form-group margin-b-10 " @if(@$registration->alert_on_appointment !=1) style="display:none;" @endif>
                                            {!! Form::label('alert_on_appointment', 'Alert on Appointment', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                                            <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                                {!! Form::radio('alert_appointment','Yes',(@$authorization->alert_appointment == 'Yes')?true:null,['class'=>'flat-red js-alert_appointment-change']) !!} Yes &emsp; {!! Form::radio('alert_appointment', 'No',(@$authorization->alert_appointment != 'Yes')?true:null,['class'=>'flat-red js-alert_appointment-change']) !!} No  
                                            </div>
                                        </div>
                                        -->
                                    </div><!-- Left side content Ends-->
                                </div>
                                {!! Form::hidden('alert_appointment','Yes') !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                
                            </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center" id="v2-authorizationeditffooter_{{@$authorization->id}}">
                                    <input data-id="v2-authorizationeditform_{{@$authorization->id}}" class="btn btn-medcubics js-v2-edit-authorization" accesskey='s' type="submit" value="Save">
                                    <a class="btn btn-medcubics js-v2-delete-authorization margin-l-10" data-id='{{@$authorization->id}}'> Delete </a>
                                </div>
                            </div>
                        </div><!-- /.box-body -->   
                    </div>                                
                </div>
            </div>
        </div>
    </div>
</div><!-- Box Ends -->
{!! Form::close() !!}