<div class="box-body no-padding"> 
    <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }      
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
        @if($checkpermission->check_url_permission('patients/{id}/edit') == 1)
			@if(!empty($patient_insurances))
                <a class="font600 font14 js-addmore_authorization_v2 form-cursor pull-right " accesskey="n"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Authorization</a>
			@endif	
        @endif
    </div>

    {!! Form::hidden('patient_id',$id,['class'=>'form-control','id'=>'patient_id']) !!}
	<input id="authorization_policy_ids" type="hidden" value="{{@$authorization_policy_ids}}">
    <?php
		$count = $authorization_count = count((array)@$authorizations);
    ?>		

    @if($authorization_count == 0)
	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 no-padding">
	    <p class="padding-10 font14 bg-white med-gray-dark text-center yes-border border-green">No Records Found </p>
	    </div>
    @else
    	<?php $authorization_count_v2 = 0 ?>
        @foreach(@$authorizations as $authorization)
    		<?php $authorization_count_v2++; ?>
        	@include('patients/patients/authorization-form',['authorization' => $authorization,'authorization_count_v2' => $authorization_count_v2])   
        @endforeach
    @endif


    {!! Form::open(['onsubmit'=>"event.preventDefault();",'class' => 'authorization-info-form medcubicsform','name'=>'authorization-info-form']) !!}
        {!! Form::hidden('current_tab','autherization',['class'=>'form-control']) !!}

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center no-padding">
            <a href="javascript:void(0);" @if(@$selectbox =='') id="insurance" @else id="contact" @endif class="js_arrow pull-left" > {!! Form::button('<<', ['class'=>'btn btn-medcubics']) !!} </a></center>
            <a href="{{ url('patients/'.$id.'/billing/create') }}" class="js_arrow js-next-tab pull-right"> {!! Form::button('Add Charge', ['class'=>'btn btn-medcubics']) !!} </a></center>

    {!! Form::close() !!} 
</div>  <!--- End Insurance -->


<div id="add_new_authorization" class="modal fade in" data-keyboard="false">
    <div class="modal-md">
        <div class="modal-content">

            <div class="modal-header">
				<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Authorization</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['onsubmit'=>"event.preventDefault();" ,'name'=>'v2_authorization_form','id'=>'js-bootstrap-validator-authorization','class'=>'v2-authorization-info-form popupmedcubicsform']) !!}

                <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
                    <?php // echo $practice_timezone;?>
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.authorization") }}' />
                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class" id="js-address-general-address">

                            <div class="form-group">
                                {!! Form::label('insurance name', 'Insurance', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}                                 
                                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">  
                                    {!! Form::select('auth_insurance_id',array('' => '-- Select --') +(array)$patient_insurances,null,['class'=>'select2 form-control insurance_id js-sel-insurance-address','id'=>'auth_insurance_id']) !!} 
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('Place of Service', 'POS', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::select('pos_id', array('' => '-- Select --') + (array)$pos,null,['class'=>'select2 form-control','id'=>'pos_id']) !!}       
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('Authorization Number', 'Auth No', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label star']) !!} 
                                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::text('authorization_no',null,['class'=>'form-control','maxlength'=>'29','id'=>'authorization_no-0', 'autocomplete'=>'off']) !!}
                                </div>
                                <!-- <div class="col-md-1 col-sm-1 col-xs-2">
                                        <a id="document_add_modal_link_authorization_number" href="#document_add_modal" data-url="{{url('api/adddocumentmodal/patients::Authorization::'.@$id.'/'.@$document_save_id.'/Authorization_Documents_Pre_Authorization_Letter')}}" data-backdrop="false" data-toggle="modal" data-target="#document_add_modal"><i class="{{Config::get('siteconfigs.document_upload_modal_icon')}}"></i></a>
                                </div> -->
                            </div>

                            <div class="form-group">
                                {!! Form::label('Start Date', 'Start Date', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!}
                                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
                                    <i class="fa fa-calendar-o form-icon"></i>     
                                    {!! Form::text('start_date',null,['class'=>'form-control form-cursor auth_datepicker dm-date', 'autocomplete'=>'off' ,'id'=>'start_date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                </div>
                                <div class="col-sm-1 col-xs-2"></div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('End Date', 'End Date', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!} 

                                <div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">  
                                    <i class="fa fa-calendar-o form-icon"></i>
                                    {!! Form::text('end_date',null,['class'=>'form-control form-cursor auth_datepicker dm-date','autocomplete'=>'off' ,'id'=>'end_date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                </div>
                                <div class="col-sm-1 col-xs-2"></div>
                            </div>
                          <?php /*?>
                            <div class="form-group" @if(@$registration->alert_on_appointment !=1) style="display:none;" @endif>
                                {!! Form::hidden('alert_appointment_single',(@$authorization->alert_appointment == 'Yes')?'Yes':'No',['class'=>'js_alert_appointment_single']) !!} 
                                {!! Form::label('alert_on_appointment', 'Alert on Appointment', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                    {!! Form::radio('alert_appointment','Yes',null,['class'=>'flat-red js-alert_appointment-change']) !!} Yes &emsp; {!! Form::radio('alert_appointment', 'No','No',['class'=>'flat-red js-alert_appointment-change']) !!} No                                       
                                </div>
                                <div class="col-md-1 col-sm-1 col-xs-2"></div>
                            </div>
                            --><?php */?>
                            {!! Form::hidden('alert_appointment','Yes') !!} 
							<div class="form-group" @if(@$registration->allowed_visit !=1) style="display:none;" @endif>
								<span @if(@$registration->allowed_visit !=1) style="display:none;" @endif>
									{!! Form::label('Allowed Visits', 'Allowed Visits', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!} 
									<div class="col-lg-6 col-md-7 col-sm-7 col-xs-10">
										{!! Form::text('allowed_visit',null,['class'=>'form-control js-visit dm-auth-visits','id'=>'allowed_visit-0','autocomplete'=>'off']) !!}
									</div>
								</span>									
							</div>

                            <?php /* Notes Added MED-2509 */?>
                            <div class="form-group" >
                                {!! Form::label('Auth Notes', 'Auth Notes', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
                                    {!! Form::textarea('authorization_notes',null, ['class' => 'form-control','maxlength'=>500]) !!}   
                                </div>                                
                            </div>
                        </div>
                    </div><!-- /.box-body -->   
                </div><!-- /.box Ends Contact Details-->

                <div id="authorization-info-footer" class="modal-footer">
                    <input id="js-authorizationform-submit-button-v2" data-id="js-bootstrap-validator-authorization" accesskey="s" class="btn btn-medcubics-small" type="submit" value="Save">
                    <button class="btn btn-medcubics-small" data-dismiss="modal" type="reset" id="configform">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->    
<script type="text/javascript">
    <?php if($get_default_timezone){ ?> 
    var get_default_timezone = '<?php echo $get_default_timezone; ?>';
    <?php }else{?>
    var get_default_timezone = '';
    <?php }?>
</script>