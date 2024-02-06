@extends('admin')
@section('toolbar')
@php $id = Route::getCurrentRoute()->parameter('id'); @endphp
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-money font14"></i> Payments <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Create Payment</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('payments')}}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
<div class="col-md-12 margin-t-m-20 margin-b-5" >
     <div class="box box-info no-shadow orange-border">
        <div class="box-body textbox-bg-orange border-radius-4 p-b-0">
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-bottom form-horizontal m-b-m-10 margin-t-m-4">                               
                <div class="form-group-billing">                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 billing-select2-orange">
                        {!! Form::select('patient_detail',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','claim_number'=>'Claim No','account_no'=>'Account No','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),null,['class'=>'form-control select2', 'id' => 'Paymentsearch']) !!}
                    </div>                                                     
                </div>                                    
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 no-bottom form-horizontal margin-t-m-4 m-b-m-8">
                <div class="form-group-billing">                                                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                       {!! Form::text('payment_search_val','',['id' => 'js-search-val','maxlength'=>'25','class'=>'form-control input-sm-modal-billing yes-border','placeholder'=>'Search', 'accesskey'=>'p']) !!}                       
                    </div>
                </div>                  
            </div>
                                  
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 no-bottom form-horizontal m-b-m-8 margin-t-m-4">
                <div class="form-group-billing">                                                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                       <a href= "javascript:void(0)" class="btn btn-medcubics-small pull-right js-search-clamorpatient" style = "margin-top:1px;">Search</a>
                    </div>
                </div>                  
            </div>                              
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<div id="choose_claims" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div>
@stop
@section('practice')
<div class="js-append-payment"> 
    @php $claims =@$claims_lists;@endphp
	@include ('patients/billing/tabs', ['tabpatientid' => @$post_val['patient_id'], 'tab' => 'payment'])    
    {!! Form::open(['url'=>'payments/patientpost', 'id' => 'js-payment', 'class' => 'medcubicsform',  'name' => "patient-form"]) !!}
     @include('payments/payments/patient/common_patient_payment', ['id' => @$post_val['patient_id'], 'from' => 'mainpayment'])
    {!!Form::close()!!}
 </div>
 <div id="post_payments" class="modal fade in">
    <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->  
</div><!-- /.modal-dialog -->  
@stop
@push('view.scripts')     
<script type="text/javascript">
	$('input[type="text"]').attr('autocomplete','off');
	$(document).ready(function () {		
		// Show submit button after page render completed.
		Pace.on('done', function() {
			setTimeout(function(){
				$(".selFnBtn").removeClass("hide");
				//$('form#js-bootstrap-validator .js-charge_save').prop('disabled', false);
				//$('form#js-bootstrap-validator .js-charge_savesbt').prop('disabled', false);
			}, 1000);
		});
	});	
	/*
    $(document).ready(function () {
        $('input[name="check_date"]').on('change', function(){
            $('form#js-payment').bootstrapValidator('revalidateField', 'check_date'); 
        });
        $('input[name="cardexpiry_date"]').on('change', function(){
            $('form#js-payment').bootstrapValidator('revalidateField', 'cardexpiry_date'); 
        });
        $('select[name="card_type"]').on('change', function(){ 
             $('form#js-payment').bootstrapValidator('revalidateField', 'card_type');
        });       
         $('#js-payment')
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
                            message: empty_amt,
                            validators: {
                                callback: {
                                    message: empty_amt,
                                    callback: function (value, validator) {                         
                                        chkd = $('input[name=payment_type]:radio:checked').val();                       
                                        if(typeof chkd =='undefined' || chkd == '')
                                        chkd = $('select[name=payment_type]').val();
                                        var takeback = $('input[name="takeback"]').val();
                                        value_remaining = value.split('.');
                                        value_remaining = value_remaining[0];
                                        console.log("value remaining");
                                        console.log(value_remaining);
                                        if(typeof value != "undefined" && value != '' && value <= 0 && chkd != 'Adjustment' && chkd != 'Credit Balance' && takeback != 1)
                                        {                                                                 
                                            return {
                                                valid:false,
                                                message: greater_zero_amt
                                            }
                                        }
                                        if(value != '' && isNaN(value)){                           
                                            return {
                                                valid:false,
                                                message: valid_amt
                                            }
                                        }else if(value_remaining.length>10) 
                                             {
                                                return {
                                                    valid:false,
                                                    message: "Please enter less than 10 characters"
                                                }
                                            }       
                                        if (chkd == 'Adjustment') {
                                            return true;
                                        } else {                                             
                                            return (value == '') ? false : true;
                                        }

                                    },
                                },                                
                            }

                        },
                       card_type: {
                                validators: {
                                    callback: {
                                        message: card_empty,
                                        callback: function (value, validator) {                                       
                                            chkd = $('input[name=payment_type]:radio:checked').val();
                                            if(typeof chkd =='undefined' || chkd == '')
                                            chkd = $('select[name=payment_type]').val(); 
                                            mode = $('select[name=payment_mode]').val();
                                            card_type = $('select[name=card_type]').val();                        
                                            if (chkd == "Payment" && mode == 'Credit') {                                            
                                                return (card_type == '') ? false : true;
                                            }                                        
                                            return true;
                                        }
                                    }

                                }

                            },
                         card_no: {
                                validators: {
                                    callback: {
                                        message: card_no,
                                        callback: function (value, validator) {                                       
                                            chkd = $('input[name=payment_type]:radio:checked').val();
                                            if(typeof chkd =='undefined' || chkd == '')
                                            chkd = $('select[name=payment_type]').val();                        
                                            mode = $('select[name=payment_mode]').val();
                                            if (chkd == "Payment" && mode == 'Credit') {                                             
                                                return (value == '') ? false : true;
                                            }                                        
                                            return true;
                                        },
                                    },                
                                    regexp: {
                                        regexp: /^[0-9\s]+$/i,
                                        message: only_numeric_lang_err_msg
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
	*/
</script> 
@endpush 