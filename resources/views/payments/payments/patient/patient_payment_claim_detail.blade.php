<!-- Patient payment posting starts here -->
@php $id = Route::getCurrentRoute()->parameter('id'); @endphp   
<?php
    $url = url('/payments/create');
    $patient = (is_null($id) && !empty($payment_details) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_details->patient_id, 'encode') : $id);
    $credit_balance_val = !is_null($patient) ? App\Models\Patients\Patient::getPatienttabData($patient) : "0.00"; // credit balance from check amount
    $payment_type = @$payment_details->payment_type;
    $check_date = (isset($payment_details->check_date) && $payment_details->check_date != '0000-00-00') ? date('m/d/Y', strtotime($payment_details->check_date)) : "";
    $cardexpiry_date = (isset($payment_details->cardexpiry_date) && $payment_details->cardexpiry_date != '0000-00-00') ? date('m/d/Y', strtotime($payment_details->cardexpiry_date)) : "";
    $payment_detail_id = isset($payment_details->id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->id, 'encode') : "";
    $patient_id = isset($payment_details->id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->patient_id, 'encode') : "";
    $bootsrapeid = (!empty($payment_type) && $payment_type == "Payment") ? "" : "js-bootstrap-validator";
    $sel_claim = @$claim_id;
    $payment_mode = $payment_details->payment_mode;
?>
{!! Form::open(['url'=> $url,'class'=>'paymentpopupform']) !!}    
<div class="box box-view no-shadow no-border no-bottom">
    <span class = "js-length"></span>
    <div class="box-body no-padding">
        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
            <div class="box-body-block no-padding" ><!--Background color for Inner Content Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                    <div class="box no-border no-shadow"><!-- Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <!--  1st Content Starts -->
                            <div class="box-body form-horizontal"><!-- Box Body Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center font600 med-green"> 
                                    <?php $payment_text = "Payment"; ?>
                                    {!! Form::radio('payment_type', 'Payment','Yes',['class'=>'flat-red js-payment-type']) !!} Payment &emsp;                                  
                                </div>
                                {!! Form::hidden('payment_detail_id',@$payment_detail_id, ['id' => "js-payment-detail-id"]) !!} 
                                {!! Form::hidden('patient_id',!empty($patient)?$patient:$patient_id) !!} 
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10 js-popuppatient-data">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="bg-white med-orange margin-l-10 padding-0-4 font600 js-amt"> <?php echo $payment_text; ?></span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                        <?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Patient'); ?>
                                        <div class="col-lg-12">                                                        
                                        </div>                                    
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">  
                                            <div class="form-group-billing js-payment-mode">
                                                {!! Form::label('type', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green','style'=>'font-weight:600;']) !!}
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup"> 
                                                    {!! Form::hidden('payment_mode', @$payment_details->payment_mode)!!}                                   
                                                    {!! Form::select('payment_mode', ['Check' => 'Check','Cash' => 'Cash','Credit' => 'Credit Card'],@$payment_details->payment_mode,['class'=>'select2 form-control', 'id' => 'js-payment-mode']) !!}
                                                </div>
                                            </div> 
                                            @if($payment_mode == "Check")  

                                            <div class="js-checkdetail-div">
                                                <div class="form-group-billing">
                                                    {!! Form::label('check no', 'Check No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}                                                  
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"> 
                                                        {!!Form::hidden('checkexist', null)!!}
                                                        <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>
                                                        {!! Form::text('check_no',@$payment_details->check_no,['maxlength'=>$lengthval,'class'=>'form-control input-sm-header-billing', 'data-type'=> 'Patient']) !!}
                                                    </div>                                   
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('check dt', 'Check Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}                                                  
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">                                    
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i>
                                                        {!! Form::text('check_date',$check_date,['maxlength'=>'25','class'=>'form-control input-sm-header-billing dm-date']) !!}
                                                    </div>                                   
                                                </div>
                                            </div>

                                            @elseif($payment_mode == "Credit")  
                                            <div class="js-carddetail-div">
                                                <div class="form-group-billing ">
                                                    {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}                                                  
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">
                                                        {!! Form::hidden('card_type', @$payment_details->card_type)!!}
                                                        {!! Form::select('card_type', ['' => '--Select--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$payment_details->card_type,['class'=>'select2 form-control']) !!}
                                                    </div>                                   
                                                </div> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Card No', 'Card No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}                                                  
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">                                    
                                                        {!! Form::text('card_no',@$payment_details->card_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                   
                                                </div> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Name on Card', 'Name on Card', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">                                    
                                                        {!! Form::text('name_on_card',@$payment_details->name_on_card,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                                    </div>                                   
                                                </div>                                                                                                         
                                            </div>                                       
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <div class="js-carddetail-div">
                                                <div class="form-group-billing">
                                                    {!! Form::label('Expiry Date', 'Expiry Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600']) !!}                                                        
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8  p-l-0">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i>
                                                        {!! Form::text('cardexpiry_date',@$cardexpiry_date,['maxlength'=>'25','class'=>'form-control input-sm-header-billing dm-date js-payment_datepicker']) !!}
                                                    </div>                                   
                                                </div>

                                                <div class="form-group-billing js-payment-amount">
                                                    {!! Form::label('amount', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star','id' =>'Payment']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 p-l-0">
                                                        {!! Form::text('payment_amt_pop',@$payment_details->payment_amt,['class'=>'form-control input-sm-header-billing allownumericwithdecimal js_amt_format', 'maxlength' => '10']) !!}
                                                        {!! Form::hidden('payment_amt_calc',@$payment_details->balance,['class'=>'form-control']) !!}
                                                        {!! Form::hidden('payamt',null,['id'=>'payamount']) !!}
                                                    </div>

                                                </div>                                                        
                                                <div class="form-group-billing">                           
                                                    {!! Form::label('ref', 'Reference', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 p-l-0">
                                                        {!! Form::text('reference',null,['class'=>'form-control  input-sm-header-billing','maxlength' => 20]) !!}
                                                    </div> 
                                                </div>
                                            </div> 
                                        </div>  
                                        @endif 
                                        @if($payment_mode != "Credit")
                                        <div class="form-group-billing js-payment-amount">
                                            {!! Form::label('amount', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star','id' =>'Payment']) !!}                                                  
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                {!! Form::text('payment_amt_pop',@$payment_details->payment_amt,['class'=>'form-control input-sm-header-billing allownumericwithdecimal js_amt_format', 'maxlength' => '10']) !!}
                                                {!! Form::hidden('payment_amt_calc',@$payment_details->balance,['class'=>'form-control']) !!}
                                                {!! Form::hidden('payamt',null,['id'=>'payamount']) !!}
                                            </div>

                                        </div>                                                        
                                        <div class="form-group-billing">                           
                                            {!! Form::label('ref', 'Reference', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}                                                  
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                {!! Form::text('reference',null,['class'=>'form-control  input-sm-header-billing','maxlength' => 20]) !!}
                                            </div> 
                                        </div>
                                        @endif

                                        {!! Form::hidden('claim_ids',@$sel_claim,['id' => 'payment_claim_id']) !!}
                                        {!! Form::hidden('takeback',1) !!} 


                                    </div>
                                </div>

                            </div><!-- /.box-body Ends-->

                        </div><!--  1st Content Ends -->

                    </div><!--  Box Ends -->
                </div><!-- Only general details Content Ends -->

            </div><!-- Inner Content for full width Ends -->
        </div><!--Background color for Inner Content Ends -->



    </div>    
    <div>
        <div class="box-body table-responsive  no-padding "><!-- Notes Box Body starts -->  
            <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12 chat ar-notes js_payment no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive claim-transaction-scroll">
                    <table class="popup-table-wo-border table table-responsive">                    
                        <thead>
                            <tr>   
                                <th style="width:2%"><input type="checkbox" class="flat-red js_menu_payment"></th>
                                <th>DOS</th>
                                <th>Claim No</th>                                
                                <th>Billed To</th>                               
                                <th class="text-right">Billed</th>
                                <th class="text-right">Paid</th>
                                <th class="text-right">Adj</th>
                                <th class="text-right">Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $claims_lists = @$payment_details->payment_claim_detail;
                            ?>
                            @if(!empty($claims_lists))                                    
                            @foreach($claims_lists as $claim)  
                            <?php $claim = $claim->claim; ?>                              
                            <tr>                                      
                                @php 
									$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');   
									$default_checked = ($sel_claim != '' && $sel_claim == $claim_id)? 'checked':''; 
								@endphp                                      
                                <td><a href="javascript:void(0)"><input id = "{{$claim_id}}" type="checkbox" class="js-sel-pay js_submenu_payment" data-claim = "js-bal-{{$claim->claim_number}}" {{$default_checked}} name="insurance_checkbox"><label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>
                                @php $url = url('patients/popuppayment/'.$claim->id) @endphp                              
                                <td> <a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}</a></td>     
                                <td>{{@$claim->claim_number}}</td> 
                                @if(empty($claim->insurance_details))
                                <td>Self</td>  
                                @else                                                               
                                <td>{!!App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id)!!}</td>
                                @endif                         
                                <td class="text-right">{{@$claim->total_charge}}</td>
                                {!!Form::hidden('patient_paid_amt', $claim->patient_paid,['class' => 'js-bal-'.$claim->claim_number])!!}
                                <td class="text-right">{{@$claim->total_paid}}</td>
                                <td class="text-right"> {{App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim->total_adjusted, @$claim->total_withheld)}}</td>                                
                                <td class="text-right" id = "js-bal-{{$claim->claim_number}}">{{@($claim->balance_amt)}}</td>

                                <td><span class="ready-to-submit">{{@$claim->status}}</span></td>
                            </tr>
                            @endforeach
                            @else
                            <tr><td colspan="9" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
                            @endif
                        </tbody>
                    </table>
                </div> 
            </div>                         
        </div><!-- Notes box-body Ends-->
        <div class="box-header-view-white ar-bottom-border text-center">  
            {!! Form::submit("Continue", ['class'=>'btn btn-medcubics-small margin-b-10']) !!} 
            <button class="btn btn-medcubics-small js-close-addclaim margin-b-10" aria-label="Close" type="button" style="padding: 2px 16px;">Cancel</button>               
            {!! Form::close() !!} 
        </div>
    </div>   
</div> 
<!--Patient payment posting ends here -->
<script type="text/javascript">
$(document).on('blur', '.js_amt_format', function () {
    value = $(this).val();
    if (value != '' && !isNaN(value)) {
        var num = parseFloat(value).toFixed(2);
        $(this).val(num);
    }
});
    $(document).ready(function () {
        $('input[name="check_date"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_date');
        });
        $('input[name="cardexpiry_date"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'cardexpiry_date');
        });
        $('input[name="payment_amt_pop"]').on('change', function () {
            if ($('input[name="wallet_refund"]').length)
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'wallet_refund');
        });
        $('select[name="card_type"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'card_type');
        });
    });
</script> 