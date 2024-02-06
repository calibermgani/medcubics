@extends('admin')

@section('toolbar')

<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.patient.file_text')}} font14"></i> Templates  <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Mail</span></small>
        </h1>
        <ol class="breadcrumb">
			<?php 
				$uniquepatientid = $patient_id;
            	$id = $uniquepatientid;
			?>

            <li><a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/correspondence')}}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
            @include ('patients/layouts/swith_patien_icon')	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/correspondence')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
    <div id="correspondence_preview_modal" class="modal fade"  data-keyboard="false">
        <div class="modal-md-650">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Subject of letter</h4>
                </div>
                <div class="modal-body">											
                    <p class="js_mail_content"></p>											
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- Modal Light Box Ends --> 
</div>
@stop

@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@include ('patients/correspondence/tabs')
@stop
<?php  $hide_class = ""; $hide_class_content = "hide";?>
@section('practice')
@if(count((array)$set_input_col)>0)
<?php $hide_class = "hide"; $hide_class_content = "";?>
<div class="js_generate_information">
    {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'bootstrap-validator-correspondence','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
    @include ('patients/correspondence/forms',['submitBtn'=>'Save'])
    {!! Form::close() !!}
</div>
@endif
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.correspondence") }}' />

<div class="js_template_information @if(count((array)$set_input_col)>0) hide @endif">
    {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'bootstrap-validator-correspondence_send','name'=>'medcubicsform','class'=>'medcubicsform correspondence_send',"data-url"=>url("patients/".$patient_id."/correspondence/send")]) !!}
    <input type="hidden" name="template_id" value="{{ $templates->id}}" />
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
        <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
                <div class="box box-info no-shadow">
                    <div class="box-block-header with-border">
                        <i class="livicon" data-name="mail"></i> <h3 class="box-title">Mail Content</h3>

                    </div><!-- /.box-header -->              
                </div>
            </div>
            <div class="box-body form-horizontal">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                        <div class="form-group margin-b-10">
                            {!! Form::label('to', 'To Address', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                            <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                                {!! Form::text('email_id',@$temp_pair->to,['class'=>'form-control js_mail_to']) !!}
                            </div>
                        </div>
                        <div class="form-group margin-b-10">
                            {!! Form::label('subject', 'Subject', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}
                            <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                                {!! Form::text('subject',@$templates->name,['class'=>'form-control js_mail_subject','maxlength'=>'100']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 js_original_content ">
					{!! Form::label('subject', 'Content', ['class'=>'col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label']) !!}
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pull-right <?php echo $hide_class_content;?>"> 
						<a class="js_edit_content font600 font14 form-cursor pull-right"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
                    </div>
                </div>
				<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 js_original_content <?php echo $hide_class_content;?>">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10"> 
						<div class="box box-info no-shadow l-green-b">
							<div class="box-body js_set_original_content">
								<?php echo $templates->content; ?>
							</div>
						</div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_editor_content <?php echo $hide_class;?>">
                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            {!! Form::textarea('message',@$templates->content,['class'=>'form-control','id'=>"editor1"]) !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Light Box Starts --> 
        </div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
				
				{!! Form::button("Preview", ['name'=>'preview','class'=>'btn btn-medcubics form-group js_preview', 'id' => 'js_template_preview']) !!}
				<a target="_blank" class="btn btn-medcubics" href="javascript:void('Print')" tabindex="-1" aria-haspopup="false" onclick="CKEDITOR.tools.callFunction(16,this);return false;">Print</a>
				{!! Form::submit("Send", ['name'=>'send','class'=>'btn btn-medcubics form-group js_preview_send', 'id' => 'js_template_send']) !!}
				<a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/correspondence')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
			</div>
		</div>
    </div>
    {!! Form::close() !!}		
</div>
@stop

@push('view.scripts')                           
<script type="text/javascript">
    $(document).ready(function () {
        $(function () {
            $("#js_dosfrom").datepicker({
                changeMonth: true,
                maxDate: '0',
                changeYear: true,
                onClose: function (selectedDate) {
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dosfrom"]'));
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dosto"]'));
                }
            });
            $("#js_dosto").datepicker({
                changeMonth: true,
                changeYear: true,
                onClose: function (selectedDate) {
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dosfrom"]'));
                    $('#js-bootstrap-validator').bootstrapValidator('revalidateField', $('input[name="dosto"]'));
                }

            });
        });       
        $('#js_pay_date,#js_currentdate').datepicker({
            yearRange: "-10:+10",
            changeMonth: true,
            changeYear: true
        });
        if ($(".js_template_information").hasClass("hide"))
            correspondenceValidation();
        else
            correspondencesendValidation();
    });
    function correspondenceValidation() {
        $('#bootstrap-validator-correspondence')
		.bootstrapValidator({
			message: '',
			excluded: [':disabled', ':hidden'],
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				email: {
					message: '',
					validators: {
						notEmpty: {
							message: '{{ trans("common.validation.email") }}'
						},
						callback: {
							message: '',
							callback: function (value, validator) {
								var response = emailValidation(value);
								if (response != true) {
									return {
										valid: false,
										message: response
									};
								}
								return true;
							}
						}
					}
				},
				currentdate: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.date") }}'
						},
						date: {
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						}
					}
				},
				dosfrom: {
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
									var stop_date = validator.getFieldElements('dosto').val();
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
				dosto: {
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
									var eff_date = validator.getFieldElements('dosfrom').val();
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
				},
				pay_date: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.date") }}'
						},
						date: {
							format: 'MM/DD/YYYY',
							message: '{{ trans("common.validation.date_format") }}'
						}
					}
				},
				insurance_id: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.insurance") }}'
						}
					}
				},
				/*insurance_addr: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.insaddr") }}'
						}
					}
				},*/
				policyid: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.policy_id") }}'
						}
					}
				},
				message: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.message") }}'
						}
					}
				},
				provider_id: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("common.validation.provider_required") }}'
						}
					}
				},
				ein: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.ein") }}'
						}
					}
				},
				npi: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.npi") }}'
						}
					}
				},
				claims: {
					message: '',
					trigger: 'keyup change',
					validators: {
						notEmpty: {
							message: '{{ trans("practice/patients/correspondence.validation.claim") }}'
						}
					}
				},
				practicephone: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator, $field) {
								var work_phone_msg = '{{ trans("common.validation.phone_limit") }}';
								var response = phoneValidation(value, work_phone_msg);
								if (response != true) {
									return {
										valid: false,
										message: response
									};
								}
								return true;
							}
						}
					}
				},
				practicefax: {
					message: '',
					validators: {
						callback: {
							message: '',
							callback: function (value, validator) {
								var fax_msg = '{{ trans("common.validation.fax_limit") }}';
								var response = phoneValidation(value, fax_msg);
								if (response != true) {
									return {
										valid: false,
										message: response
									};
								}
								return true;
							}
						}
					}
				}
			}
		}).on('success.form.bv', function (e) {
			e.preventDefault();
			$('.js_field_list').each(function (i, selected) {
				var tag_name = $(this).prop("tagName");
				if (tag_name != "DIV") {
					var get_content = $(".js_content").val();
					var get_key = $(this).attr("data-key");					
					var get_val = (tag_name == "SELECT") ? $(this).find("option:selected").text() : $(this).val();
					if (get_key.indexOf("DATE") >= 0 && get_key.indexOf("PAYBYDATE") == -1 && get_key.indexOf("VAR-DAT") == -1) {
						var d = new Date(get_val);
						var curr_date = d.getDate();
						var curr_month = d.getMonth();
						var curr_year = d.getFullYear();
						curr_year = curr_year.toString().substr(2,2);
						get_val   = curr_month+"/"+curr_date+"/"+curr_year;						

					}					
					if ($(this).hasClass("js_multi_claim_detail")) {
						var get_val = $(this).find("option:selected").map(function () {
							return $(this).text();
						}).get().join(", ");						
						$('input[name="claim_number"]').val(get_val);
						var get_arr = get_val.split(",");
						get_val = (get_arr.length == 1) ? get_arr[0] : get_val;
					}
					var regex = new RegExp(get_key, "g");
					var text_value = get_content.replace(regex, get_val);
					$(".js_content").val(text_value);
				}
			});
            CKEDITOR.instances['editor1'].setData($(".js_content").val());
            correspondencesendValidation();
            $(".js_template_information").removeClass("hide");
            $(".js_set_original_content").html($(".js_content").val());
            $(".js_generate_information").addClass("hide");
            $('#bootstrap-validator-correspondence').unbind('success');
            $('.js_edit_content').trigger('click');
        });
    }
    $(document).on('change', '.js_change_type', function(){    	
    	if($(this).attr("name") == "insurance_id"){
    		$("input[name='insurance_id']").val($(this).val());
    	}
    })
    function correspondencesendValidation() {

        $('#bootstrap-validator-correspondence_send')
                .bootstrapValidator({
                    message: '',
                    excluded: ':disabled',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        email_id: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.email") }}'
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {
                                        var response = emailValidation(value);
                                        if (response != true) {
                                            return {
                                                valid: false,
                                                message: response
                                            };
                                        }
                                        return true;
                                    }
                                }
                            }
                        },
                        subject: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("practice/patients/correspondence.validation.subject") }}'
                                }
                            }
                        },
                        message: {
                            message: '',
                            validators: {
                                notEmpty: {
                                    message: '{{ trans("common.validation.content") }}'
                                },
								callback: {
									message: '',
									callback: function (value, validator) {
										var value = CKEDITOR.instances['editor1'].getData();
										value = $($.parseHTML(value)).text();
										if(value.length > 0){
											var get_val = value.trim();
											if(get_val.length == 0){
												return {
													valid: false,
													message: '{{ trans("common.validation.not_only_space") }}'
												};
											}
										}
										return true;
									}
								}
                            }
                        }
                    }
                }).on('success.form.bv', function (e) {
            e.preventDefault();
            var form_url = $(".correspondence_send").attr("data-url");
            var formData = $(".correspondence_send").serialize();
            var dosfrom = $('input[name="dosfrom"]').val();
            var dosto = $('input[name="dosto"]').val();
            var insurance_id = $('input[name="insurance_id"]').val();
            var claim_number = $('input[name="claim_number"]').val();
            append_data = formData+"&dosfrom="+dosfrom+"&dosto="+dosto+"&insurance_id="+insurance_id+"&claim_number="+claim_number;
            console.log(dosfrom);
            console.log("searcialize");
            console.log(formData);
            $.ajax({
                url: form_url,
                type: "POST",
                data: append_data,
                success: function (res) {
                    var data = JSON.parse(res);
                    if (data["status"] == "success") {
                        var url = data["data"];
                        window.location.href = api_site_url + "/" + url;
						sessionStorage.setItem("mail_success_msg","yes");
                    }
                    else
                    {
                        //js_alert_popup("Must Replace these variables " + data["data"]);
                        js_sidebar_notification('error',"Must Replace these variables " + data["data"]);
						$('#bootstrap-validator-correspondence_send').bootstrapValidator('disableSubmitButtons', false);
						$('#bootstrap-validator-correspondence_send').data("bootstrapValidator").resetForm();
                    }
                }
            });
        });
		CKEDITOR.instances.editor1.on('change', function () {
			
            CKEDITOR.instances['editor1'].updateElement();
            $('#bootstrap-validator-correspondence_send').bootstrapValidator();
            $('#bootstrap-validator-correspondence_send').bootstrapValidator('revalidateField', 'message');
        });

        CKEDITOR.instances.editor1.on('save', function () {
        	var data = CKEDITOR.instances.editor1.getData();
        });

    }
	$(document).on('click','.js_edit_content', function (e) {
		$(".js_original_content").addClass("hide");
		$(".js_editor_content").removeClass("hide");
		$("#editor1").trigger("change");
		CKEDITOR.instances.editor1.on('instanceReady', function () {
			CKEDITOR.instances['editor1'].focus();
        });
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
            return (getdate > 0) ? true : '{{ trans("practice/patients/correspondence.validation.dosto") }}';
        }
        else if (start_date != '' && eff_format != "Invalid Date") {
            return (end_date == '') ? '{{ trans("practice/patients/correspondence.validation.dos_to_req") }}' : true;

        }
        return true;
    }
</script>
@endpush