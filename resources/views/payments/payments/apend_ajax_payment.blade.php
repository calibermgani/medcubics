<?php $claims =@$claims_list; ?>
@include ('patients/billing/tabs', ['tabpatientid' => @$patient_id, 'tab' => 'payment'])    
{!! Form::open(['url'=>'payments/insurancepost', 'id' => 'js-insurance-form', 'class' => 'js_insurance_create_form  medcubicsform','name' => "insurance-form"]) !!}  
@include ('payments/payments/common_insurance', ['from' => 'mainpayment'])
{!!Form::close()!!}
@include ('patients/problemlist/commonproblemlist') 
