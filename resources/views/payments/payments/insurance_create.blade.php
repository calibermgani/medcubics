@extends('admin')
@section('toolbar')

<?php 
	$id = Route::current()->parameter('id');
	$claim_id = (!empty($claims_lists)) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_list->id, 'encode') : 0; 
?>
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20 margin-b-6">
     <div class="box box-info no-shadow orange-border">
        <div class="box-body textbox-bg-orange border-radius-4 p-b-5">
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-bottom form-horizontal">                               
                <div class="form-group-billing">                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 billing-select2-orange">
                        {!! Form::select('patient_detail',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','claim_number'=>'Claim No','account_no'=>'Account No','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),null,['class'=>'form-control select2', 'id' => 'Paymentsearch','onchange' => 'selectSearchFilter(this,"js-search-val")']) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 no-bottom form-horizontal p-r-0">
                <div class="form-group-billing">                                                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 p-r-0">
                       {!! Form::text('payment_search_val','',['id' => 'js-search-val','maxlength'=>'25','class'=>'js-search-text form-control input-sm-modal-billing','placeholder'=>'Search','style'=>'border:1px solid #ccc;', 'accesskey'=>'p']) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 no-bottom form-horizontal p-r-0">
                <div class="form-group-billing">                                                
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 p-r-0">
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
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
        <?php
			$patient_id = (empty($patient_id)) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims_list->patient->id, 'encode'):$patient_id;
			$claims =@$claims_list;
        ?>		
        @include ('patients/billing/tabs', ['tabpatientid' => $patient_id, 'tab' => 'payment'])
        {!! Form::open(['url'=>'payments/insurancepost', 'id' => 'js-insurance-form','class' => 'js_insurance_create_form medcubicsform','name' => "insurance-form"]) !!}  
        @include ('payments/payments/common_insurance', ['from' => 'mainpayment'])
        {!!Form::close()!!}
    </div> 
	@include ('patients/problemlist/commonproblemlist')	
@stop
@push('view.scripts')
<script type="text/javascript">    
	$(function () {
		$('input[type="text"]').attr('autocomplete','off');		
	});
	/*
	$(document).ready(function () {		
		// Show submit button after page render completed.
		Pace.on('done', function() {
			setTimeout(function(){
				$(".selFnBtn").removeClass("hide");
				hideLoadingImage();
			}, 1000);
		});
	});		
	*/
	function doInitWork(callback) {
		displayLoadingImage();
		payment_type = $('input[name="payment_type"]').val();
		if (payment_type == 'Adjustment')
			emptyunrelatetypedata();
		makeallfieldsreadonly(payment_type);
		callback();
	}
	function doFinishWork(){
		$(".selFnBtn").removeClass("hide");
		hideLoadingImage();
	}
	
	doInitWork(doFinishWork);	
		
	// To handle browser back button. click on back redirect to Payments page.
	window.addEventListener('popstate', function(event) {
		//console.log("Pop state called"); alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
        //window.location = "{{url('payments')}}";
	}, false);	
	
   /* $(document).ready(function () {
        $('#js-insurance-form')
                .bootstrapValidator({
                    message: '',
                    excluded: ':disabled',
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
                                            chkd =   $('input[name=payment_type]').val();                                 
                                             if(chkd == 'Adjustment'){
                                                return true;
                                             } else{
                                                return (value != '')?true:false; 
                                             }
                                        },    
                                    },
                                }
                        },
                        check_no: {
                            validators: {
                                callback: {
                                    message: '{{trans("common.validation.check_eft_no")}}',
                                    callback: function (value, validator) {  
                                        chkd =   $('input[name=payment_type]').val();      
                                        check_number_exist = $('input[name="checkexist"]').val();
                                        lengthval = '{{ Config::get("siteconfigs.payment.check_no_minlength") }}';
                                        if(chkd == "Payment" || chkd == "Refund"){                                            
                                             if(value == ''){
                                                return{
                                                    valid:false,
                                                    message:'{{trans("common.validation.check_eft_no")}}'
                                                }
                                             }else if(value != '' && !checknumbervalidation(value)){
                                                 return {
                                                    valid:false,
                                                    message:'{{trans("common.validation.alphanumeric")}}',
                                                }
                                             }else if(value != '' && value.length < lengthval){
                                                 return {
                                                    valid:false,
                                                    message:"Please provide minimum length of "+lengthval,
                                                }
                                             }else if(value != '' && check_number_exist == 1){                                              
                                                return{
                                                    valid:false,
                                                    message:'Check/EFT number already exist'
                                                }
                                             }
                                        }
                                        return true;
                                    },
                                },
                            }
                        },           
                        check_date: {
                            validators: {
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{trans("common.validation.date_format")}}'
                                },
                                callback: {
                                    message: '',
                                    callback: function (value, validator) {  
                                       chkd =   $('input[name=payment_type]').val();                                       
                                        var check_date = $('input[name="check_date"]').val();
                                        var current_date=new Date(check_date);
                                        var d=new Date();                                       
                                        if(check_date != '' && d.getTime() < current_date.getTime()){
                                            return {
                                                valid: false,
                                                message: '{{ trans("practice/patients/payments.validation.furute_date")}}',
                                            };
                                        }
                                        if(chkd == "Payment" || chkd == "Refund"){
                                             return (value == '')?false:true;
                                        }
                                         return true;
                                    }
                                }
                            }
                        }, 
                        deposite_date: {
                            validators: {
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{trans("common.validation.date_format")}}'
                                },
                                callback: {
                                    message: '{{trans("common.validation.deposit_date")}}',
                                    callback: function (value, validator) {  
                                       chkd =   $('input[name=payment_type]').val();                                       
                                        var deposite_date = $('input[name="deposite_date"]').val();
                                        var current_date=new Date(deposite_date);
                                        var d=new Date();                                       
                                        if(deposite_date != '' && d.getTime() < current_date.getTime()){
                                            return {
                                                valid: false,
                                                message: '{{ trans("practice/patients/payments.validation.furute_date")}}',
                                            };
                                        }
                                        if(chkd == "Payment" || chkd == "Refund"){
                                             return (value == '')?false:true;
                                        } else{
                                            return true;  
                                        }
                                    }
                                }
                            }
                        }, 
                        posting_date: {
                            validators: {
                                date: {
                                    format: 'MM/DD/YYYY',
                                    message: '{{trans("common.validation.date_format")}}'
                                },
                                callback: {
                                    message: '{{trans("common.validation.posting_date")}}',
                                    callback: function (value, validator) {  
                                       chkd =   $('input[name=payment_type]').val();                                       
                                        check_date = isFutureDate(value);  
                                        if(check_date == true){
                                             return {
                                                valid: false,
                                                message: '{{ trans("practice/patients/payments.validation.furute_date")}}'
                                            } 
                                        }
                                        if(chkd == "Payment" || chkd == "Refund"){
                                             return (value == '')?false:true;
                                        }
                                         return true;
                                    }
                                }
                            }
                        }, 
                        js_insurance_paid: {
                            selector: '.js_insurance_paid',
                            validators: {
                                callback: {
                                     message: '{{trans("common.validation.amountnotempty")}}',
                                     callback: function (value, validator) { 
                                     chkd =   $('input[name=payment_type]').val();                                     
                                        return (value == '' && chkd != "Adjustment")?false:true;
                                    },
                               }
                            }
                        },
                    },
                });
        }); */
</script> 
@endpush 