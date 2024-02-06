<?php 
$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$total_list->patient_id,'encode');
$claims =@$claims_lists;
?>
@include ('patients/billing/tabs', ['tabpatientid' => $patient_id, 'tab' => 'payment']) 
{!! Form::open(['url'=>'payments/patientpost', 'id' => 'js-patient-form', 'name' => "patient-form", 'class' => 'medcubicsform']) !!}  
@include('payments/payments/patient/common_patient_payment', ['id' => @$patient_id, 'from' => 'mainpayment'])
{!!Form::close()!!}

<!--<script type="text/javascript">
    $(document).ready(function () {
        $('input[name="check_date"]').on('change', function(){
            $('form#js-patient-form').bootstrapValidator('revalidateField', 'check_date'); 
        });
        $('input[name="cardexpiry_date"]').on('change', function(){
            $('form#js-patient-form').bootstrapValidator('revalidateField', 'cardexpiry_date'); 
        });
        $(document).delegate('input[name="payment_amt"]', 'change', function(){

        }); 
         $('select[name="card_type"]').on('change', function(){ 
             $('form#js-patient-form').bootstrapValidator('revalidateField', 'card_type');
        });        
        $('#js-patient-form')
        .bootstrapValidator({
            message: 'This value is not valid',
           // excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                payment_amt: {
                    validators: {                               
                         callback: {
                            message: '{{ trans("practice/patients/payments.validation.valid_amt")}}',
                             callback: function (value, validator) {
                                chkd = $('select[name=payment_type]').val();                                        
                                if(value != '' && value <= 0){
                                    return {
                                        valid:false,
                                        message: "{{ trans('practice/patients/payments.validation.grater_amt')}}"
                                    }
                                }
                                if (chkd == 'Adjustment') {
                                    return true;
                                } else {                                             
                                    return (value != ''&& !isNaN(value)) ? true : false;
                                 }

                            },
                         },     
                    },
                          

                },
               card_type: {
                validators: {
                    callback: {
                            message: '{{trans("common.validation.card_notempty")}}',
                            callback: function (value, validator) {  
                                chkd =   $('select[name=payment_type]').val();
                                mode =   $('select[name=payment_mode]').val()                                                                             
                                if(chkd == "Payment" && mode == 'Credit'){
                                     return (value == '')?false:true;
                                }
                                 return true;
                            }
                    }
                    
                }

                },
                 card_no: {
                    validators: {
                        callback: {
                            message: '{{trans("common.validation.card_no")}}',
                            callback: function (value, validator) {  
                                chkd =   $('select[name=payment_type]').val();
                                mode =   $('select[name=payment_mode]').val()                                      
                                 console.log("card_no"+mode);
                                console.log("card_no chkd"+chkd);
                                if(chkd == "Payment" && mode == 'Credit'){
                                     return (value == '')?false:true;
                                }
                                 return true;
                            }
                        }
                    }

                },
                name_on_card: {
                    validators: {
                        callback: {
                            message: '{{trans("common.validation.name_on_card")}}',
                            callback: function (value, validator) {  
                                chkd =   $('select[name=payment_type]').val();
                                mode =   $('select[name=payment_mode]').val()                                      
                                console.log("name_on_card"+mode);
                                console.log("name_on_card chkd"+chkd);
                                if(chkd == "Payment" && mode == 'Credit'){
                                     return (value == '')?false:true;
                                }
                                 return true;
                            }
                        },
                        regexp: {
                            regexp: /^[a-z\s]+$/i,
                            message: '{{ trans("practice/patients/payments.validation.aplabet_only")}}'
                        }
                    }

                },
                 bankname:{
                     validators: {
                      callback: {
                            message: '{{ trans("practice/patients/payments.validation.bankname")}}',                                
                                callback: function (value, validator) {  
                                   bankname_val = $.isNumeric(value);
                                   return (bankname_val)?false:true;                                       
                                }
                            }                        
                    }
                },
                check_no: {
                    validators: {
                        callback: {
                            message: '{{trans("common.validation.check_no")}}',
                            callback: function (value, validator) {  
                                chkd =   $('select[name=payment_type]').val();
                                mode =   $('select[name=payment_mode]').val();
                                check_number_exist = $('input[name="checkexist"]').val(); 
                                lengthval = '{{ Config::get("siteconfigs.payment.check_no_minlength") }}';                                       
                                if(chkd == "Payment" && mode == 'Check' || chkd == "Refund"){
                                   if(value == ''){
                                       return{
                                            valid:false,
                                            message:'{{trans("common.validation.check_no")}}'
                                        }
                                     } else if(value != '' && !checknumbervalidation(value)){
                                        return{
                                            valid:false,
                                            message:'{{trans("common.validation.alphanumeric")}}'
                                        }
                                     }else if(value != '' && value.length < lengthval){
                                         return {
                                            valid:false,
                                            message:"Please provide minimum length of "+lengthval,
                                        }
                                     }else if(value != '' && check_number_exist == 1){                                              
                                        return{
                                            valid:false,
                                            message:'check number already exist'
                                        }
                                     }       
                                }
                                 return true;
                            }
                        },                                  
                    }

                },                       
                 adjustment_reason: {
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/payments.validation.adjustment")}}',
                            callback: function (value, validator) {  
                                chkd =  $('select[name=payment_type]').val();
                                if(chkd == "Adjustment"){
                                     return (value == '')?false:true;
                                }
                                 return true;
                            }
                        }
                    }

                },
                check_date: {
                     message: '',
                     trigger: 'change keyup',
                    validators: {
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{trans("common.validation.date_format")}}'
                        },
                        callback: {
                            message: '{{trans("common.validation.date_format")}}',
                            callback: function (value, validator) {
                                chkd =   $('select[name=payment_type]').val();
                                mode =   $('select[name=payment_mode]').val()                                      
                                var check_date = $('input[name="check_date"]').val();
                                var current_date=new Date(check_date);
                                var d=new Date();                                                                         
                                if(check_date != '' && d.getTime() < current_date.getTime()){
                                    return {
                                        valid: false,
                                        message: '{{ trans("practice/patients/payments.validation.furute_date")}}',
                                    };
                                }
                                if(chkd == "Payment" && mode == 'Check' || chkd == "Refund"){
                                     return (value == '')?false:true;
                                } else{
                                    return true; 
                                }
                                
                            }
                        }
                    }

                },  
               cardexpiry_date: {
                    validators: {
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{trans("common.validation.date_format")}}'
                        },
                        callback: {
                            message: '',
                            callback: function (value, validator) {                                        
                                check_date = checkDate(value);
                                if (check_date == false && value != '') {
                                    return {
                                        valid: false,
                                        message: '{{ trans("practice/patients/payments.validation.past_date")}}'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },             
                 js_pateint_paid: {
                     selector: '.js_pateint_paid',
                            validators: {
                                callback: {
                                     message: '{{ trans("practice/patients/payments.validation.paid_amount")}}',
                                     callback: function (value, validator,$field) {
                                     var get_field = $field.parents(".js-calculate").attr("id");                                   
                                     var value = $("#"+get_field+" .js_pateint_paid").val();                                    
                                     if(value != '' && value == 0){
                                            return {
                                                valid:false,
                                                message: "{{ trans('practice/patients/payments.validation.grater_amt')}}"
                                            }
                                        } 
                                        if(value != '' && isNaN(value)){
                                            return {
                                                valid:false,
                                                message: "{{ trans('practice/patients/payments.validation.valid_amt')}}"
                                            }
                                        }                                         
                                        return (value == '')?false:true;                                  
                                        
                                   },
                               }
                            }
                }, 
            },
        });
    });
</script> -->