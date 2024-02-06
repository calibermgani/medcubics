<table class="main">
	<tr>
		<td valign="top">
			<div class="border">
			Live Webcam<br>                           
			</div>
			<br/><input type="button" class="snap" value="SNAP IT" id="js-snap">
		</td>
		<td width="50">&nbsp;</td>
		<td valign="top">
			<div id="upload_results" class="border">
				Snapshot<br>
				<img src="{{ URL::to('/') }}/img/web_logo.jpg" />
			</div>
		</td>
	</tr>
	
	<?php 
		$api_url = url('/api/getwebcamimage/').$type;
		$swf_url = url('/js/webcam').'/webcam.swf';
		$shutter_url = url('/js/webcam').'/shutter.mp3';
	?>
	{!! Form::hidden('webcam_image',null,['id' => 'webcam_image']) !!}
	{!! Form::hidden('webcam_filename',null, ['id' => 'filename_image']) !!} 
	{!! Form::hidden('error-cam',null,['id' => 'error-cam'])!!}
</table>      
@push('view.scripts')
{!! HTML::script('js/webcam/webcam.js') !!} 
@if($type=='practice'||$type=='facility'||$type=='provider')
<script type="text/javascript">
    $(document).ready(function() {
	var url = window.location.href;
	var arr = url.split('/');
	if (jQuery.inArray("facilitydocument", arr) != -1 || jQuery.inArray("providerdocuments", arr) != -1) {
		        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
			excluded: [':disabled'],
            fields: {
                title:{
                    message:'',
					trigger: 'change keyup',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.title") }}'
                        },
						regexp: {
							regexp: /^[a-zA-Z0-9 ]+$/,
							 message: '{{ trans("common.validation.alphanumericspac") }}'
						},
						callback: {
	                        message: '',
	                        callback: function(value, validator, $field) {
	                            var count = $("#tle").val().length;
	                            if(count >= 120){
	                                return {
	                                    valid: false,
	                                    message: "Maxmimum Character Exceeded"
	                                };
	                            } else {
	                                return true;
	                            }
	                        }
	                    }
                    }
                },
                category:{
                    message:'',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.category") }}'
                        },
                    }
                },
				assigned: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Assigned To'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				priority: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Priority'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				'followup': {
					trigger: 'change keyup',
					validators: {
						notEmpty: {
							message: 'Select followup Date'
						},
						date:{
							format:'MM/DD/YYYY',
							message: 'Invalid date format'
						},
						callback: {
							message: '',
							callback: function(value, validator, $field) {
								var fllowup_date = $('#follow_up_date').val();
								var current_date=new Date(fllowup_date);
								var d=new Date();	
								if(fllowup_date != '' && ( d.getTime()-96000000 ) > current_date.getTime()){
									return {
										valid: false,
										message: "Please give future date"
									};
								} else {
									return true;
								}
							}
						}
					}
				},
				status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Status'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				notes: {
                    message: '',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				// page: {
    //                 message: '',
    //                 validators: {
				// 		integer: {
				// 			message: 'The value is not an integer'
				// 		},
    //                     notEmpty: {
    //                         message: 'Enter Pages'
    //                     },
    //                     callback: {
    //                         message: attachment_lang_err_msg,
    //                         callback: function (value, validator) {
    //                             return true;
    //                         }
    //                     }
    //                 }
    //             },
				js_err_webcam:{
					message:'',
					selector: '.js_err_webcam',
					validators:{
						callback: {
							message: attachment_lang_err_msg,
							callback: function (value, validator) {
								var get_checked_val 	= $('input[name="upload_type"]:checked').val();
								var err_msg				= $('#error-cam').val();
								if((err_msg =='' || err_msg ==null || err_msg ==1) && get_checked_val =="webcam") {
									if(value =='' || value == null)
										return false;
									else
										return true;
								}
								return true;
							}
						}
					}
				},
                "filefield[]":{
                    message:'',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.upload") }}'
                        },
						file: {
							// extension: 'pdf,jpeg,jpg,png,gif,doc,zip,xls,csv,docx,xlsx,txt',
							maxSize: 1024 * 32000,
							message: "Maximum allowed only 32MB file per file"
						},
						callback: {
							message: '{{ trans("common.validation.upload_limit") }}',
							callback: function (value, validator) {
							var file = [];
							var filelist = document.getElementById("filefield1").files || [];
							for (var i = 0; i < filelist.length; i++) {
								file.push((filelist[i].name).replace(/C:\\fakepath\\/i, ''));
							}
							if (filelist.length > 5) {
								return {
									valid: false,
									message: "Please upload only five files"
								};
							} else {
								if (filelist != "") {
									var extension_Arr = ['pdf', 'jpeg', 'jpg', 'png', 'gif', 'doc', 'xls', 'csv', 'docx', 'xlsx', 'txt', 'PDF', 'JPEG', 'JPG', 'PNG', 'GIF', 'DOC', 'XLS', 'CSV', 'DOCX', 'XLSX', 'TXT'];
									var validation = [];
									$.each(file, function (i, val) {
										var file_name = val;
										var temp = file_name.split(".");
										filename_length = ((temp.length) - 1);
										if (extension_Arr.indexOf(temp[filename_length]) == -1) {
											validation.push(false);
											// return false;
										} else {
											validation.push(true);
											// return true;
										}
									});
									if (jQuery.inArray(false, validation) == -1) {
										return true;
									}
									else {
										return {
											valid: false,
											message: "Please ensure all the file is of correct format"
										};
									}
								}
								return true;
							}								
								// if(value =="" || value ==null)
								// 	return true;
								// var size = parseFloat($('[name="filefield"]')[0].files[0].size/1024).toFixed(2);
								// var get_image_size = Math.ceil(size);
								// return (get_image_size>filesize_max_defined_length)?false : true;
							}
						}
                    }
                }
            }
        });
	} else {

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
			excluded: [':disabled'],
            fields: {
                title:{
                    message:'',
					trigger: 'change keyup',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.title") }}'
                        },
						regexp: {
							regexp: /^[a-zA-Z0-9 ]+$/,
							 message: '{{ trans("common.validation.alphanumericspac") }}'
						},
						callback: {
	                        message: '',
	                        callback: function(value, validator, $field) {
	                            var count = $("#tle").val().length;
	                            if(count >= 120){
	                                return {
	                                    valid: false,
	                                    message: "Maxmimum Character Exceeded"
	                                };
	                            } else {
	                                return true;
	                            }
	                        }
	                    }
                    }
                },
                category:{
                    message:'',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.category") }}'
                        },
                    }
                },
				assigned: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Assigned To'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				priority: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Priority'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				'followup': {
					trigger: 'change keyup',
					validators: {
						notEmpty: {
							message: 'Select followup Date'
						},
						date:{
							format:'MM/DD/YYYY',
							message: 'Invalid date format'
						},
						callback: {
							message: '',
							callback: function(value, validator, $field) {
								var fllowup_date = $('#follow_up_date').val();
								var current_date=new Date(fllowup_date);
								var d=new Date();	
								if(fllowup_date != '' && ( d.getTime()-96000000 ) > current_date.getTime()){
									return {
										valid: false,
										message: "Please give future date"
									};
								} else {
									return true;
								}
							}
						}
					}
				},
				status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Status'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				notes: {
                    message: '',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				// page: {
    //                 message: '',
    //                 validators: {
				// 		integer: {
				// 			message: 'The value is not an integer'
				// 		},
    //                     notEmpty: {
    //                         message: 'Enter Pages'
    //                     },
    //                     callback: {
    //                         message: attachment_lang_err_msg,
    //                         callback: function (value, validator) {
    //                             return true;
    //                         }
    //                     }
    //                 }
    //             },
				js_err_webcam:{
					message:'',
					selector: '.js_err_webcam',
					validators:{
						callback: {
							message: attachment_lang_err_msg,
							callback: function (value, validator) {
								var get_checked_val 	= $('input[name="upload_type"]:checked').val();
								var err_msg				= $('#error-cam').val();
								if((err_msg =='' || err_msg ==null || err_msg ==1) && get_checked_val =="webcam") {
									if(value =='' || value == null)
										return false;
									else
										return true;
								}
								return true;
							}
						}
					}
				},
                filefield:{
                    message:'',
                    validators:{
                        notEmpty:{
                            message: '{{ trans("common.validation.upload") }}'
                        },
						file: {
							extension: 'pdf,jpeg,jpg,png,gif,doc,zip,xls,csv,docx,xlsx,txt',
							message: attachment_valid_lang_err_msg
						},
						callback: {
							message: '{{ trans("common.validation.upload_limit") }}',
							callback: function (value, validator) {
								if(value =="" || value ==null)
									return true;
								var size = parseFloat($('[name="filefield"]')[0].files[0].size/1024).toFixed(2);
								var get_image_size = Math.ceil(size);
								return (get_image_size>filesize_max_defined_length)?false : true;
							}
						}
                    }
                }
            }
        });

	}	
});
</script>
@endif  
<script type="text/javascript">

$(function() {
	webcam.set_quality( 100 ); // JPEG quality (1 - 100)
	webcam.set_api_url('<?php echo $api_url;?>');
	$('#webcam_movie').attr('src', '<?php echo $swf_url; ?>');
	webcam.set_shutter_sound(true);
	webcam.set_hook( 'onComplete', 'my_completion_handler' );
	webcam.set_hook( 'onError', function() {
	  $('#error-cam').val(1);
	  $('#webcam_div').hide();
	  $('#js-show-webcam').hide();
	} );
	$('div#webcam').html(webcam.get_html(320, 240));
	$('#js-snap').click(function() { 
		if($('#error-cam').val() == 1) {
			js_alert_popup("JPEGCam Flash Error: No camera was detected");  
			addModalClass();
			return false;
		}
		$(".js_err_webcam").val(1); //Remove error in bootstrap validator
		document.getElementById('upload_results').innerHTML = 'Snapshot<br>'+'<img src="{{ URL::to('/') }}/img/ajax-loader.gif">';
		 webcam.snap();
	});
});     
</script>                       <!--End-->
<script>
document.write( webcam.get_html(320, 240) );
</script>
@endpush     