<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
            <div class="box  no-shadow">
                <div class="box-block-header with-border">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>                   
                </div><!-- /.box-header -->              
            </div>
        </div>
        <?php $currnet_page = Route::getFacadeRoot()->current()->uri(); 
				$current_page = 'create';
				$provider = App\Models\Provider::getAllprovider(); 
				$facility = App\Models\Facility::getAllfacilities(); 
				$patientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'decode'); 
		 ?>	
        @if(strpos($currnet_page, 'edit') == true) 
        <?php 	
			$current_page = 'edit';
			$eligibility->dos_from = App\Http\Helpers\Helpers::dateFormat($eligibility->dos_from,'dob');
			$eligibility->dos_to = App\Http\Helpers\Helpers::dateFormat($eligibility->dos_to,'dob');
			$template_id = $eligibility->template_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($eligibility->template_id,'encode');
		?>
		@else
			<?php
				$template_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($eligibility->id,'encode');
			?>
        @endif

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
            <div class="box-info no-shadow js-template">
                <div class="box-body  form-horizontal">        
                    <div class="form-group">
                        {!! Form::label('Dos', 'DOS From',  ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label star']) !!} 

                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('dos_from')) error @endif">    
                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                            {!! Form::text('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                            {!! $errors->first('dos_from', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('Dos', 'DOS To',  ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label star']) !!} 

                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('dos_to')) error @endif">    
                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                            {!! Form::text('dos_to',NULL,['id'=>'js_dosto','class'=>'dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                            {!! $errors->first('dos_to', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">        
                        {!! Form::label('provider', 'Provider', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}                                                                                 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('provider_id')) error @endif">
                            {!! Form::select('provider_id',array('' => '-- Select --') + (array)$provider,null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('provider_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    {!! Form::hidden('patients_id',$patientid,['class'=>'form-control']) !!}
                                      
                </div>

              

            </div>

        </div>
        
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >
            <div class="box-info no-shadow js-template">
                <div class="box-body  form-horizontal">        

                    <div class="form-group">        
                        {!! Form::label('facility', 'Facility', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}                                                                                 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('facility_id')) error @endif">
                            {!! Form::select('facility_id',array('' => '-- Select --') + (array)$facility,null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">        
                        {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-5 col-sm-5 col-xs-12 control-label star']) !!}                                                                                 
                        <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10 @if($errors->first('patient_insurance_id')) error @endif">
                            {!! Form::select('patient_insurance_id',array('' => '-- Select --') + (array)$insurance,null,['class'=>'select2 form-control']) !!}
                            {!! $errors->first('patient_insurance_id', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    
                </div>
                {!! Form::hidden('template_id',$template_id) !!}
                {!! Form::hidden('content',null,['class'=>'form-control', 'id' =>'js-template-content']) !!}

            </div>

        </div>

    </div>
    <div data-page="{{$current_page}}" class="col-lg-12 col-md-10 col-sm-10 col-xs-10 box-body tabs-green-bg hide">  
       
        <?php   
        //dd(html_entity_decode (htmlspecialchars_decode($eligibility->content)));     
        $content = htmlspecialchars_decode($eligibility->content); ?>
        
       
    </div>
    
    
    
    
    
    <div class="col-lg-12 col-md-12 l-green-b bg-white margin-t-20 js-show-template" style="padding: 20px;">
        <?php echo $content; ?>
                   </div>
    
    
    
    <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
        {!! Form::submit($submitBtn, ['name'=>'sample','class'=>'btn btn-medcubics form-group', 'id' => 'js-template-submit']) !!}				
        @if(strpos($currnet_page, 'edit') !== false)
        <a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure you want to delete?" href="{{ url('patients/'.$patient_id.'/eligibility/'.$eligibility->id.'/delete') }}">Delete</a>
        <a href="javascript:void(0)" data-url="{{url('patients/'. $patient_id.'/eligibility/'.$eligibility->id)}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        @else
        <a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/eligibility')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        @endif
    </div>
</div>



@push('view.scripts')                           
<script type="text/javascript">
    $(function () {
        $("#eligibility_dos").datepicker();
        $("#js_dosfrom").datepicker({
            changeMonth: true,
            maxDate: '0',
            changeYear: true,
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dos_from"]'));
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dos_to"]'));
            }
        });
        $("#js_dosto").datepicker({
            changeMonth: true,
            changeYear: true,
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dos_from"]'));
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dos_to"]'));
            }

        });
    });
    $(document).ready(function () {
        $("#dob_new").datepicker({
            yearRange: '1999:+0',
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            maxDate: '0',
            onClose: function (selectedDate) {
                $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dos"]'));
            }
        });
        function dosFrom(start_date, end_date) {
            var date_format = new Date(end_date);
            if (end_date != '' && date_format != "Invalid Date") {
                return (start_date == '') ? '{{ trans("practice/patients/correspondence.validation.dos_from_req") }}' : true;
            }
            return true;
        }

        function dosTo(start_date, end_date) {
            var eff_format = new Date(start_date);
            var ter_format = new Date(end_date);
            if (ter_format != "Invalid Date" && end_date != '' && eff_format != "Invalid Date" && end_date.length > 7 && checkvalid(end_date) != false) {
                var getdate = daydiff(parseDate(start_date), parseDate(end_date));
                return (getdate >= 0) ? true : '{{ trans("practice/patients/correspondence.validation.dosto") }}';
            }
            else if (start_date != '' && eff_format != "Invalid Date") {
                return (end_date == '') ? '{{ trans("practice/patients/correspondence.validation.dos_to_req") }}' : true;

            }
            return true;
        }
        changeInput();
        $('#js-bootstrap-validator')
                .bootstrapValidator({
                    message: '',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        patient_insurance_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/eligibility.validation.patient_insurance_id") }}'
                                }
                            }
                        },
                       /* template_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/eligibility.validation.template_id") }}'
                                }
                            }
                        },*/
                        provider_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/eligibility.validation.provider_required") }}'
                                }
                            }
                        },
                        facility_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/eligibility.validation.facility_required") }}'
                                }
                            }
                        },
                        dos_from: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/correspondence.validation.dos_from_req") }}'
                                },
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{ trans("common.validation.date_format") }}'
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        if ($("#js_dosto").length > 0) {
                                            var stop_date = validator.getFieldElements('dos_to').val();
                                            var current_date = new Date(value);
                                            var d = new Date();
                                            var response = dosFrom(value, stop_date);
                                            if (value != '' && d.getTime() < current_date.getTime()) {
                                                return {
                                                    valid: false,
                                                    message: future_date
                                                };
                                            }
                                            if (value.length > 0 && response != true) {
                                                return {
                                                    valid: false,
                                                    message: response
                                                };
                                            }
                                            return true;
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        dos_to: {
                            message: '',
                            trigger: 'keyup change',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/correspondence.validation.dos_to_req") }}'
                                },
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{ trans("common.validation.date_format") }}'
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        if ($("#js_dosfrom").length > 0) {
                                            var eff_date = validator.getFieldElements('dos_from').val();
                                            var ter_date = value;
                                            var response = dosTo(eff_date, ter_date);
                                            if (value.length > 0 && response != true) {
                                                return {
                                                    valid: false,
                                                    message: response
                                                };
                                            }
                                            return true;
                                        }
                                        return true;
                                    }

                                }
                            }
                        }
                    }
                });
    });
</script>
@endpush