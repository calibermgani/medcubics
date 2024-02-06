<?php 
	$current_page = Route::getFacadeRoot()->current()->uri(); 
	$cur_page = strpos($current_page, 'edit');
?>
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.clinicalnotes") }}' />

<div class="box no-shadow margin-t-m-10">
    <div class="box-block-header ">
        <i class="fa fa-info-circle font14"></i> <h3 class="box-title">General Details</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body form-horizontal margin-l-10 margin-t-10">
        <div class="form-group">
            {!! Form::label('title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('title')) error @endif">
                {!! Form::text('title',null,['class'=>'form-control','maxlength'=>'100']) !!} 
                {!! $errors->first('title', '<p> :message</p>')  !!} 
            </div>
        </div> 
		 
		<?php @$claim_id = (@$clinical_detail->claim_id ==0) ? '' : @$clinical_detail->claim_id ?>  
		
        <div class="form-group">
            {!! Form::label('Claim No', 'Claim No', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('claim_id')) error @endif">
                {!! Form::select('claim_id', array('' => '-- Select --') + (array)@$claims,null,['class'=>'select2 form-control','id'=>'claim_no']) !!}
                {!! $errors->first('claim_id', '<p> :message</p>')  !!}

            </div>
        </div> 
        <div class="form-group">
            {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('facility_id')) error @endif">
                <input type="hidden" name="facility_id" class="js_select_facility_input" value="{{ @$clinical_detail->facility_id }}" />
                {!! Form::select('facility_id', array('' => '-- Select --') + (array)@$facility,null,['class'=>'select2 form-control js_select_facility js_select_change',($cur_page !== false && $claim_id !='') ? 'disabled':'']) !!}
                {!! $errors->first('facility_id', '<p> :message</p>')  !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('provider_id')) error @endif">
                <input type="hidden" name="provider_id" class="js_select_rendering_input" value="{{ @$clinical_detail->provider_id }}" />
                {!! Form::select('provider_id', array('' => '-- Select --') + (array)@$provider,null,['class'=>'select2 form-control js_select_rendering js_select_change',($cur_page !== false && $claim_id !='') ? 'disabled':'']) !!}
                {!! $errors->first('provider', '<p> :message</p>')  !!}
            </div>
        </div> 	
        <div class="form-group">
            {!! Form::label('DOS', 'DOS', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('dos')) error @endif">
                <input type="hidden" name="dos" class="js_select_dos_input" value="{{ @$clinical_detail->dos }}"/>
                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon"></i> {!! Form::text('dos',null,['class'=>'form-control dob js_select_dos js_select_change input-view-border1 dm-date form-cursor',($cur_page !== false && $claim_id !='') ? 'disabled':'','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                {!! $errors->first('dos', '<p> :message</p>')  !!}
            </div>
        </div> 
        <div class="form-group">
            {!! Form::label('category', 'Category', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('document_categories_id')) error @endif">
                {!! Form::select('document_categories_id', array('' => '-- Select --') + (array)@$category,null,['class'=>'select2 form-control','id'=>'category']) !!}
                {!! $errors->first('document_categories_id', '<p> :message</p>')  !!} 
            </div>
        </div> 
		@if(strpos($current_page, 'edit') !== false)
		<div class="form-group">
            {!! Form::label('', 'Last Attachment', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label']) !!} 
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('document_categories_id')) error @endif">
               <a href="{{ url('patients/'.$patient_id.'/clinicalnotes/'.@$clinical_detail->filename) }}" target="_blank"><i class="fa fa-clipboard form-cursor"></i></a>
            </div>
        </div> 
		@endif
		<div class="form-group">
            <?php $webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); ?>
            <?php $scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); ?>
            @if($webcam || $scanner)  
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                {!! Form::label('Attachment', 'Attachment', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-5 control-label star']) !!} 
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7">
                    {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                    @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                    @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
                </div>                                                                          
            </div> 
            @endif
			<div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!} 
				<div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 no-padding @if($errors->first('filefield')) error @endif">
                    <span class="fileContainer" style="padding:1px 20px;"><input class="col-lg-2 col-md-2 col-sm-3 form-control" name="filefield" type="file">	 Upload </span>
                    {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                    &emsp;<span class="js-display-error"></span>
                </div>
				
            </div>  
            <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                {!! Form::label('', '', ['class'=>'col-lg-2 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 no-padding">
                    <span class="fileContainer js-webcam-class" style="padding:1px 20px 1px 11px;">
                        <input type="hidden" class="js_err_webcam" /> Webcam</span>
                    {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                    &emsp;<span class="js-display-error"></span>
                </div>               
            </div> 
			
            <div class="box-footer js-scanner" style="display:none"> 
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                </div>
            </div>

            <input type="hidden" name="scanner_filename" id="scanner_filename">
            <input type="hidden" name="scanner_image" id="scanner_image">
            @if($errors->first('filefield'))
            <div class="form-group">
                {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                    {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                </div>                                                                          
            </div>
            @endif
			
        </div><!-- /.box-body -->
    </div><!-- /.box-body -->

    
        
   
</div><!-- /.box -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
	{!! Form::submit('Save', ['class'=>'btn btn-medcubics form-group']) !!}
	@if(strpos($current_page, 'edit') !== false)
		@if($checkpermission->check_url_permission('clinicalnotes/delete/{id}') == 1 )
		<a class="btn btn-medcubics js-delete-confirm"data-text="Are you sure you want to delete?" href="{{ url('patients/'.@$patient_id.'/clinicalnotes/delete/'.@$clinical_detail->id) }}">Delete</a>
		@endif
	@endif
	<a href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/clinicalnotes')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
</div>

<div style="display:none" id="js-show-webcam">
    <?php $document_type = "patients"; ?>  
    @if($document_type=='patients')
    @include ('layouts/webcam', ['type' => 'patient_document'])
    @endif
</div>
@push('view.scripts1')  
<script type="text/javascript">

    $(document).ready(function () {
        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            excluded: [':disabled'],
            fields: {
                title: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("common.validation.title") }}'
                        },
						regexp: {
							regexp: /^[a-zA-Z0-9 ]+$/,
							message: alphanumericspace_lang_err_msg
						}
                    }
                },
                facility_id: {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/clinical_notes.validation.facility") }}',
                            callback: function (value, validator, $field) {
                                if (value == '' || value == null) {
                                    return false;
                                }
                                else
                                    return true;
                            }
                        }
                    }
                },
                provider_id: {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/clinical_notes.validation.provider") }}',
                            callback: function (value, validator, $field) {
                                if (value == '' || value == null) {
                                    return false;
                                }
                                else
                                    return true;
                            }
                        }
                    }
                },
                dos: {
                    message: '',
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/clinical_notes.validation.dos") }}'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{ trans("common.validation.date_format") }}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator, $field) {
                                var dob = value;
                                var current_date = new Date(dob);
                                var d = new Date();
                                if (dob != '' && d.getTime() < current_date.getTime()) {
                                    return {
                                        valid: false,
                                        message: '{{ trans("practice/patients/clinical_notes.validation.dos_valid") }}'
                                    };
                                }
                                else {
                                    return true;
                                }
                            }
                        }
                    }
                },
                document_categories_id: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("common.validation.category") }}'
                        },
                    }
                },
                js_err_webcam: {
                    message: '',
                    selector: '.js_err_webcam',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
								var form_page = $("form").attr("data-page");
                                if (form_page != 'edit') {
									var get_checked_val = $('input[name="upload_type"]:checked').val();
									var err_msg = $('#error-cam').val();
									if ((err_msg == '' || err_msg == null || err_msg == 1) && get_checked_val == "webcam") {
										if (value == '' || value == null)
											return false;
										else
											return true;
									}
									return true;
								}
								return true;
                            }
                        }
                    }
                },
                filefield: {
                    message: '',
                    validators: {
                        file: {
                            extension: 'pdf,jpeg,jpg,png,gif,doc,zip,xls,csv,docx,xlsx,txt',
                            message: attachment_valid_lang_err_msg
                        },
                        callback: {
                            callback: function (value, validator) {
                                var form_page = $("form").attr("data-page");
                                if (form_page == 'create' && $('[name="filefield"]').val() == '') {
                                    return {
                                        valid: false,
                                        message: attachment_lang_err_msg
                                    };
                                }
                                if ($('[name="filefield"]').val() != "") {
                                    var size = parseFloat($('[name="filefield"]')[0].files[0].size / 1024).toFixed(2);
                                    var get_image_size = Math.ceil(size);
                                    if (get_image_size > filesize_max_defined_length) {
                                        return {
                                            valid: false,
                                            message: attachment_length_lang_err_msg
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
    function loadwebcam()
    {
        $.getScript(api_site_url + "/js/webcam/webcam.js", function () {
            webcam.set_quality(100); // JPEG quality (1 - 100)          
            webcam.set_api_url($('#apiurl').val());
            webcam.set_shutter_sound(true);
            webcam.set_hook('onComplete', 'my_completion_handler');
            webcam.set_hook('onError', function () {
                $('#error-cam').val(1);
                $('#webcam_div').hide();
                $('#js-show-webcam').hide();
            });
            $('div#webcam').html(webcam.get_html(320, 240));
            $(document).delegate('#js-snap', 'click', function () {
                if ($('#error-cam').val() == 1) {
                    js_alert_popup("JPEGCam Flash Error: No camera was detected");
                    addModalClass();
                    return false;
                }
                $(".js_err_webcam").val(1); //Remove error in bootstrap validator
                document.getElementById('upload_results').innerHTML = 'Snapshot<br>' + '<img src="{{ URL::to(' / ') }}/img/ajax-loader.gif">';
                webcam.snap();
            });
        });
    }
    function scan() {
        var com_asprise_scan_applet_info_codebase = api_site_url + '/js/';

        com_asprise_scan_request(myCallBackFunc,
                com_asprise_scan_cmd_method_SCAN_THEN_UPLOAD, // normal scan with the applet UI
                com_asprise_scan_cmd_return_IMAGES_AND_THUMBNAILS,
                {
                    'upload-url': 'http://asprise.com/scan/applet/upload.php?action=upload'
                    , 'format': 'JPEG'
                    , 'upload-cookies': document.cookie
                });


        /** Use this callback function to get notified about the scan result. */
        function myCallBackFunc(success, mesg, thumbs, images) {
            $("#scanner_filename").val($.trim(mesg));
            $("#scanner_image").val('1');
        }
    }
</script>
@endpush