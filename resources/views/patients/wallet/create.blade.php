@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-users font14"></i> Patients <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('patients/'.$patient_id.'/billing')}}" ><i class="fa fa-search" data-placement="bottom"  data-toggle="tooltip" data-original-title="Search Patient"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/billing/tabs') 
@stop

@section('practice')
@include ('patients/billing/model-inc')
{!! Form::open(['url'=>'patients/billing','id' => 'js-bootstrap-validator','name'=>'patients-form', 'files' => true,'class'=>'medcubicsform']) !!}
@include ('patients/armanagement/billing_create')
{!! Form::close() !!} 
@stop
<!--End-->
@push('view.scripts') 

<script type="text/javascript">
	function myFunction() {
	   
	}

   $(document).ready(function() {                    
                
        $( "#date_of_injury" ).on('change', function(){
          $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
        });
        $('#anesthesia_start, #anesthesia_stop').on('change', function(){
             $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_start');
              $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_stop');
        });
        $($("input[name='admit_date']")).on('change', function(){ 
           // $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'pos_name');
           $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');
        });
		
        function validatecopay()
        {

        }
		
        $('[name="dos_to[0]"]').on('change',function(){
			$('#js-bootstrap-validator')
					.data('bootstrapValidator')
					.updateStatus('dos_to[0]', 'NOT_VALIDATED')
					.validateField('dos_to[0]');
        });
		
         $('[name="dos_from[0]"]').on('change',function(){
			$('#js-bootstrap-validator')
					.data('bootstrapValidator')
					.updateStatus('dos_from[0]', 'NOT_VALIDATED')
					.validateField('dos_from[0]');
        });
		
        $('[name="doi"]').on('change',function(){
			$('#js-bootstrap-validator')
					.data('bootstrapValidator')
					.updateStatus('doi', 'NOT_VALIDATED')
					.validateField('doi');
        }); 
		
        $(document).delegate("input[name='copay_amt']", 'keyup', function(){
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'copay'); 
        });
		
		var TIME_PATTERN = /^(00|1[0-9]{1}):[0-5]{1}[0-9]{1}$/;
        $('#js-bootstrap-validator')
            .bootstrapValidator({ 
                message: 'This value is not valid',
                        excluded: ':disabled',
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                     'dos_from[0]':{
                        validators:{
                           notEmpty:{
                                message: 'Enter From Date'
                            },
                             date: {
                                format: 'MM/DD/YYYY',
                                message: 'The value is not a valid date'
                            }                                
                        }

                     },
                     'doi':{
                        validators:{                           
                             date: {
                                format: 'MM/DD/YYYY',
                                message: 'The value is not a valid date'
                            },
                             callback: {
								message: 'Should not be the future date',
								callback: function(value, validator, $field) {                                      
									var month = value.substring(0, 2);
									var date = value.substring(3, 5);
									var year = value.substring(6, 10);
									var dateToCompare = new Date(year, month - 1, date);
									var currentDate = new Date();
								 
									if (dateToCompare > currentDate) {
										return false;
									}
									else {
										return true;
									}                                   
								}
							}                                
                        }

                     },
                     'dos_to[0]':{
                        validators:{
                           notEmpty:{
                                message: 'Enter To Date'
                            },
                             date: {
                                format: 'MM/DD/YYYY',
                                message: 'The value is not a valid date'
                            },                                                     
                        }

                     },
                     'cpt[0]':{
                        validators:{
                         notEmpty:{
                            message: 'Procedure code is required field. Please enter the same.'
                            }
                        }

                     },
                     billing_provider_id:{
						message:'The date of service is invalid is invalid',
						validators:{
							notEmpty:{
								message: 'Select Billling provider'
								}
						}
                    },
                   rendering_provider_id:{
						message:'The date of service is invalid is invalid',
						validators:{
							notEmpty:{
								message: 'Select Rendering provider'
								}
						}
                    },
                    hold_reason_id:{
                        enabled: false,
						message:'Select hold reason',
						validators:{
							notEmpty:{
								message: 'Select hold reason'
								}
						}
                    },
                     other_reason:{
						enabled: false,
						message:'Enter hold reason',
						validators:{
							notEmpty:{
								message: 'Enter hold reason'
								}
						}
                    },
                     icd1:{
						message:'Diagnosis code is required field. Please enter the same.',
						validators:{
							notEmpty:{
								message: 'Diagnosis code is required field. Please enter the same.'
								}
						}
                    },                   
                    facility_id:{
						message:'The date of service is invalid is invalid',
						validators:{
							notEmpty:{
								message: 'Select Facility'
								}
						}
                    },
                    insurance_id:{
						message:'Select Insurance',
						validators:{
							notEmpty:{
								message: 'Select Insurance'
								}
						}
                    },
                    pos_name:{
                        message:'The date of service is invalid is invalid',
                         trigger: 'keyup change',
                            validators:{
                            callback: {
								message: 'Select admit Date',
								callback: function(value, validator, $field) {  
									pos_code = [6,8,21,31,51,61,34];     // Place of service codes that must need admission date
									if($.inArray(parseInt(value), pos_code) > -1)
									 {  
										enabledisablevalidator('enableFieldValidators', 'admit_date', true);  
										return true;
									 }else{

										enabledisablevalidator('enableFieldValidators', 'admit_date', false);                                              
										 return true;
									}
								 
								}
							}
                        }
                    },
                    admit_date:{
                         message:'The date of service is invalid is invalid',
                          trigger: 'keyup change',
                            validators:{
                               date: {
                                    format: 'MM/DD/YYYY',
                                    message: 'The value is not a valid date'
                                },                               
                                callback: {
                                   message: 'select valid admission Date',
                                    callback: function(value, validator, $field) { 
                                        dos = $("#small_date").val();
                                        compval = 1;                                       
                                        compval = (dos != '')?compareDate(value, dos):compval;  // check with dos value
                                        if(value && compval){                                           
                                            return true;
                                        } else{                                            
                                            return false;
                                        }                                       
                                     
                                    }
                                }                               
                            }
                    },
                    admit_date:{
                        message:'The date of service is invalid is invalid',
						validators:{
						   date: {
								format: 'MM/DD/YYYY',
								message: 'The value is not a valid date'
							},                                                                                     
						}
                    },
                    copay:{
						message:'The date of service is invalid is invalid',
						validators:{
						callback: {
							   message: 'Select Copay amount',
								callback: function(value, validator, $field) {    
									if (value !== '') {
											enabledisablevalidator('enableFieldValidators', 'copay_amt', true);  
										return true;
									}else{
										if($("input[name='copay_amt']").val() !=''){
											return false;
										}
										$("input[name='copay_amt']").val('');
											enabledisablevalidator('enableFieldValidators', 'copay_amt', false);                                              
										 return true;
									}
								 
								}
							}
                        }
                    }, 
                    copay_amt:{
						//enabled: false,
						message:'The date of service is invalid is invalid',
						validators:{
							numeric:{
								message: 'Only numeric values accepted'
							},                               
							callback: {
							   message: 'Enter Copay amount',
								callback: function(value, validator, $field) {    
									return (value == '')?false:true;
								 
								}
							}                               
						}
                    },                                         
                    anesthesia_start: {
                        message:'The date of service is invalid is invalid',
                            validators: {                               
                                callback: {
                                    message: 'The start time must be earlier then the end one',
                                    callback: function(value, validator, $field) {
                                        var endTime = validator.getFieldElements('anesthesia_stop').val();
                                        if (endTime === '') {
                                            return true;
                                        }
                                        var startHour    = parseInt(value.split(':')[0], 10),
                                            startMinutes = parseInt(value.split(':')[1], 10),
                                            endHour      = parseInt(endTime.split(':')[0], 10),
                                            endMinutes   = parseInt(endTime.split(':')[1], 10);

                                        if (startHour < endHour || (startHour == endHour && startMinutes < endMinutes)) {
                                            // The end time is also valid
                                            // So, we need to update its status
                                            validator.updateStatus('anesthesia_stop', validator.STATUS_VALID, 'callback');
                                            return true;
                                        }

                                        return false;
                                    }
                                }
                            }
                    },
                    anesthesia_stop: {
                        validators: {                           
                            callback: {
                                message: 'The end time must be later then the start one',
                                callback: function(value, validator, $field) {
                                    var startTime = validator.getFieldElements('anesthesia_start').val();
                                    if (startTime == '') {
                                        return true;
                                    }
                                    var startHour    = parseInt(startTime.split(':')[0], 10),
                                        startMinutes = parseInt(startTime.split(':')[1], 10),
                                        endHour      = parseInt(value.split(':')[0], 10),
                                        endMinutes   = parseInt(value.split(':')[1], 10);

                                    if (endHour > startHour || (endHour == startHour && endMinutes > startMinutes)) {
                                        // The start time is also valid
                                        // So, we need to update its status
                                        validator.updateStatus('anesthesia_start', validator.STATUS_VALID, 'callback');
                                        return true;
                                    }

                                    return false;
                                }
                            }
                        }
                    },
                    icd1_0: {                       
                        validators: {
                            notEmpty: {
                                message: 'ICD pointer required'
                            }
                        }
                    },                                                  
                },
        });
                
    });
</script> 

@endpush 