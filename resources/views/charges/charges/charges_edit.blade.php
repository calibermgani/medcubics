@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.charges.charges')}} font14"></i> Charges <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Charge</span></small>
        </h1>
        <ol class="breadcrumb">
            <?php 
				$id = Route::current()->parameter('id');
				$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'encode');
				$url = url("charges");
			?>
            <li><a accesskey="b" href="{{ $url}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
           
			<?php $uniquepatientid = !empty($patient_id)?$patient_id:$id; ?>    
            <?php /*
			@include ('patients/layouts/patientstatement_icon') 
			*/ ?>
            
            @include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/charges')}}" class="js-help" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@include ('patients/problemlist/commonproblemlist') 
@stop

@section('practice-info')
	@include ('patients/billing/tabs', ['tabpatientid' => $patient_id, 'fromcharge' => 1]) 
@stop

@section('practice')
@include ('patients/billing/model-inc')
{!! Form::open(['url'=>'charges/update','id' => 'js-bootstrap-validator', 'name'=>'patients-form', 'files' => true,'class'=>'medcubicsform', 'name' => "chargeform"]) !!}
@include ('patients/billing/billing_edit')
{!! Form::close() !!} 
@stop
<!--End-->
@push('view.scripts') 
<script type="text/javascript">
   $(document).ready(function() {   
        Pace.on('done', function() {
            setTimeout(function(){
                $('form#js-bootstrap-validator .js-charge_save').prop('disabled', false); 
            }, 1000);
        });
                     
        $( "#date_of_injury" ).on('change', function(){
          $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
        });
		
        $('#anesthesia_start').on('change', function(){
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_start');
             $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_stop');
        });
		
         $('#anesthesia_stop').on('change', function(){
             $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_start');
             $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'anesthesia_stop');
        });
		
        $("input[name='admit_date']").on('change', function(){ 
           $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'discharge_date');
           $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');
           $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');
        });
		
        $("input[name='discharge_date']").on('change', function(){ 
            $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'discharge_date');
           $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'admit_date');
        });
		
        $('[name="doi"]').on('change',function(){
               $('#js-bootstrap-validator').bootstrapValidator('revalidateField', 'doi');            
        });        
       
        $(document).delegate(".icd_pointer", 'change', function(){           
           setTimeout(function(){
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'icd_pointer'); 
            $('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false); 
           }, 500);           
        });  
		
        $(document).delegate(".box_24_atoj", 'change', function(){      
              $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'box_24_AToG[]'); 
        });     
		
		var TIME_PATTERN = /^(00|1[0-9]{1}):[0-5]{1}[0-9]{1}$/;
		var empty_arr = []; 
        $('#js-bootstrap-validator')
            .bootstrapValidator({ 
                message: 'This value is not valid', 
                feedbackIcons: {
                    valid: '',
                    invalid: '',
                    validating: ''
                },
                excluded: [":disabled", ":hidden", ":not(:visible)"],               
                fields: {                    
                     doi:{
                        message:'',                       
                        validators:{                                                        
                             callback: {
                               message: '{{ trans("practice/patients/charges.validation.grater_than_admit") }}',
                                callback: function(value, validator, $field) {                                        
                                    var admit_date = $('input[name="admit_date"]').val();                                       
                                    var month = value.substring(0, 2);
                                    var date = value.substring(3, 5);
                                    var year = value.substring(6, 10);
                                    var dateToCompare = new Date(year, month - 1, date);
                                    var currentDate = new Date();
                                    var doi = isFutureDate(value);  
                                    comp_val = 1;                                       
                                    comp_val = (admit_date != '')?compareDate(value, admit_date):comp_val;                                         
                                    var current_date=new Date(value);
                                    var d=new Date();                                        
                                    if(value != '' && current_date == "Invalid Date")   {
                                        return {
                                            valid: false,
                                            message: '{{ trans("common.validation.date_format") }}',
                                      };
                                    } 
                                    else if(value != '' && d.getTime() < current_date.getTime()){    // Should not be future the date
                                      return {
                                        valid: false,
                                        message: '{{ trans("practice/patients/charges.validation.doi_future") }}',
                                      };
                                    } 
                                    else if(!comp_val && value != ''&& admit_date != '')   {
                                         return {
                                            message : '{{ trans("practice/patients/charges.validation.grater_than_admit") }}',
                                            valid: false
                                        } 
                                    }  
                                    else if (dateToCompare > currentDate && admit_date != '') {
                                        return {
                                            valid: false,
                                            message: '{{ trans("practice/patients/charges.validation.grater_than_admit") }}',
                                         }                                                                                   
                                    }
                                    else {
                                        return true;                                        
                                    }               
                                },                                   
                            }                                
                        }
                    },                                     
                    admit_date:{
                         message:'',
                          trigger: 'change',
                            validators:{                                                             
                                callback: {
                                   message: 'etertertert',
                                    callback: function(value, validator, $field) {
                                        pos_val = $("#pos_id :selected").text(); 
                                        pos_code = [6,8,21,31,51,61,34];
                                        var m = validator.getFieldElements('discharge_date').val();                                       
                                        var current_date=new Date(value);
                                        var d=new Date();   
                                        var n = value;                                       
                                        dos = $("#small_date").val();                                       
                                        compval = 1;                                          
                                        compval = (dos != '')?compareDate(value, dos):compval;  // check with dos value
                                        var is_valid_date = validDateCheck(value);                                       
                                        if(value != '' && !is_valid_date)   {
                                            return {
                                                valid: false,
                                                message: '{{ trans("common.validation.date_format") }}',
											};
                                        }                                    
                                        else if(value != '' && d.getTime() < current_date.getTime()){    // Should not be future the date
											return {
												valid: false,
												message: '{{ trans("practice/patients/charges.validation.doi_future") }}',
											};
                                        }  
                                        else if($.inArray(parseInt(pos_val), pos_code) > -1 && value == '')   {  // For few place of service admit date was required
                                            console.log("pos valuedfgdfgdf");
                                            return {
                                                message : '{{ trans("practice/patients/billing.validation.admit_date") }}',
                                                valid: false
                                            } 
                                        }
                                        removeerrormessage(value, 'js_from_date'); 
                                        if(value && compval){
                                            
                                            return true;
                                        } 
                                        if (m != '') { 
                                           
                                            return (n == '') ? false : true;
                                        }                                        
                                        return true;                                      
                                     
                                    }
                                }                               
                            }
                    },
                    discharge_date:{
                        message:'',
						validators:{                               
							callback: {
								message: '{{ trans("practice/patients/charges.validation.dischargedate") }}',
								callback: function (value, validator) {
									var current_date=new Date(value);
									var d=new Date();
									var is_valid_date = validDateCheck(value);       
									if(value != '' && !is_valid_date)   {
										return {
											valid: false,
											message: '{{ trans("common.validation.date_format") }}',
										};
									}                                            
									else if(value != '' && d.getTime() < current_date.getTime()){    // Should not be future the date
									  return {
										valid: false,
										message: '{{ trans("practice/patients/charges.validation.doi_future") }}',
									  };
									}        
									var m = validator.getFieldElements('admit_date').val();
									var n = value;
									dos = $("#big_date").val();                                       
									compval_dis = 1;   
									compval_dis = (dos != '' && value != '')?compareDate(dos, value):compval_dis;  // check with dos value                                      
									var current_date = new Date(n); 
									if (current_date != 'Invalid Date' && n != '' && m != '' && compval_dis) { 
										var getdate = daydiff(parseDate(m), parseDate(n));
										 return (getdate >= 0) ? true : false;
									}                                                                       
									else {
										if(!compval_dis){
										  return {
											valid: false,
											message: '{{ trans("practice/patients/charges.validation.dos") }}',
										  };
										}
										return true;  
									}										
								}
							}                                                                                    
						}
                    },                                               
                    anesthesia_start: {
                        message:'',
						validators: {                               
							callback: {
								message: '{{ trans("practice/patients/billing.validation.anesthesia_start") }}',
								callback: function(value, validator, $field) {
									var endTime = validator.getFieldElements('anesthesia_stop').val();
									var startTime = validator.getFieldElements('anesthesia_start').val();
									if (endTime === '') {
										return true;
									}
									if((startTime != '' && endTime != ''))  {
										returnval = Compare();                                         
										return {
											valid:returnval['return'],
											message:returnval['message'],
										}
									}                                       
								  	return false;
								}
							}
						}
                    },
                    anesthesia_stop: {
                        validators: {                           
                            callback: {
                                message: '{{ trans("practice/patients/billing.validation.anesthesia_stop") }}',
                                callback: function(value, validator, $field) {
                                    var endTime = validator.getFieldElements('anesthesia_stop').val();
                                    var startTime = validator.getFieldElements('anesthesia_start').val();

                                    if (startTime == '') {
                                        return true;
                                    }                                    
                                    if((startTime != '' && endTime != ''))  {
										returnval = Compare();                                         
										return {
											valid:returnval['return'],
											message:returnval['message'],
										}
									} 
                                    return false;
                                }
                            }
                        }
                    },                                                              
					js_icd_validation:{
						selector: '.js_icd_validation',                                                     
						validators: {                              
								callback: {
								message: 'Diagnosis code is required field. Please enter the same.',
								callback: function(value, validator, $field) {                                       
									$field.val(value.toUpperCase());                                       
									data_icdid = $field.attr('id');
									var $current = $field;
									is_unique = 0;
									var regexp = (value.indexOf(".")== -1) ? /^[a-zA-Z0-9]{0,7}$/:/^[a-zA-Z0-9.]{0,8}$/;
									if(!regexp.test(value)) {
										return {
											valid: false,
											message : '{{ trans("admin/icd.validation.code_regex") }}'
										};   
									}
									var count = value.split(".").length - 1;
									if(count>1 && value != '') {
										return {
											valid: false,
											message: '{{ trans("practice/practicemaster/icd.validation.map_to_icd10") }}'
										}; 
									}
									$('.js-icd').each(function() {
										if ($(this).val() == $current.val() && $(this).attr('id') != $current.attr('id'))
										{
											is_unique = 1;
										}
									});
									
									var check_next = checknextnotempty(data_icdid);                                       
									var check_previous = checkpreviousnotempty(data_icdid);                                          
									if($field.attr('id') == 'icd1' && value == ''){                                           
										return {
											valid: false,
											message: 'Diagnosis code is required field. Please enter the same.'
										};  
									} else if($field.attr('id') != 'icd1' && !check_next){                                           
										return {
											valid: false,
											message: 'Enter ICD values'
										};  
									} else if(is_unique && value != '') {
										return {
											valid: false,
											message: 'Entered ICD should be unique'
										};    
									}
									if(value != '' && check_next && check_previous){
										return true;
									}                                                                            
									return true;
								}
							}
						}
					},
					icd_pointer: {
					    selector: '.icd_pointer',
					    trigger: 'change',    
                        validators: {
							callback: {
								message: '',
								callback: function (value, validator, $field) {                                           
									var get_field_id = $field.parents('li').attr("id");
									var icdpointer_id = $field.attr("id");                                              
									var next_icd_pointer = checknextnotemptyicdpointer(get_field_id, icdpointer_id);
									var previous_icd_pointer = checkpreviousnotemptyicdpointer(get_field_id, icdpointer_id);                                               
									var icd_attr_id = $( "#"+get_field_id).find('.icd_pointer:first').attr('id');
									var cpt_value = $( "#"+get_field_id).find('.js-cpt').val();                                              
									if(icdpointer_id == icd_attr_id && value == ''&& cpt_value != ''){
									$field.addClass('erroricddisplay'); 
										return  {
											valid: false,
											message: 'Enter ICD pointer'
										};  
									}                                             
									if(!next_icd_pointer && icdpointer_id != "icd1_0" )
									{                                                
										$field.addClass('erroricddisplay');                                            
										return  {
											valid: false,
											message: 'Enter icd values'
										};  
									}                                
									if(value != '' && next_icd_pointer && previous_icd_pointer)
									{                                                
										$field.removeClass('erroricddisplay js-error-class');                                             
										return true;
									} 
									if($field.hasClass('erroricddisplay'))
									{
										$field.removeClass('erroricddisplay js-error-class');        
									}                                              
									$('#js-bootstrap-validator').bootstrapValidator('disableSubmitButtons', false);  // To enable the disabled submit button                                                                                                                   
									return true;
								}
							}						
						}
					}, 
                },
        });
                
    });

    function daydiff(first, second) {
        return Math.round((second - first) / (1000 * 60 * 60 * 24));
    }

    function parseDate(str) {
        var mdy = str.split('/')
        return new Date(mdy[2], mdy[0] - 1, mdy[1]);
    }
</script> 
@endpush