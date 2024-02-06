<?php
	$check_status = $cc_status = null;
	$eft_status = "Yes";
	$eft_disabled = $check_disabled = $cc_disabled = "";
	$check_no = ($payment_details->pmt_mode != 'Credit' && isset($payment_details->check_no) ) ? @$payment_details->check_no : @$payment_details->card_no;
	if (isset($payment_details->pmt_mode) && $payment_details->pmt_mode == "Check" ) {
		$check_status = "Yes";
		$eft_status = null;
		$eft_disabled = "disabled";
	} else if (isset($payment_details->pmt_mode) && $payment_details->pmt_mode == "EFT") {
		$check_disabled = "disabled";
		$eft_status = "Yes";
		$eft_disabled = "";
	}else if (isset($payment_details->pmt_mode) && $payment_details->pmt_mode == "Credit") {
		$check_disabled = "disabled";
		$eft_disabled = "disabled";
		$cc_status = "Yes";
		$eft_status = null;
	}
	$pmt_type = isset($payment_details->pmt_type) ? $payment_details->pmt_type : "";
	$payment_id = isset($payment_details->id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->id, 'encode') : "";
	$check_date = (isset($payment_details->check_date)) ? date('m/d/Y', strtotime($payment_details->check_date)) : "";
	$cardexpiry_date = (isset($payment_details->cardexpiry_date) && $payment_details->cardexpiry_date != '0000-00-00') ? date('m/d/Y', strtotime($payment_details->cardexpiry_date)) : "";
	$add_calender = 'call-datepicker';
	$payment_type_class = (!empty($payment_details) ) ? "hide" : "";
?>
 <span class="js_show_content" style="display: none;">{{$payment_details->pmt_type}}</span>
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal"> 
        {!! Form::open(['url'=>'payments/editcheck', 'id' => 'js-editcheck-form', 'class' => 'popupmedcubicsform', 'files' => true]) !!}

        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.payment_edit") }}' />
		<input type="hidden" name="temp_type_id" value="" id="temp_type_id" />
        @if($payment_details->pmt_method == "Insurance")
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive form-horizontal margin-b-10">

            <div class="col-lg-12 col-md-12 col-sm-12 margin-t-5 col-xs-12 text-center font600 med-green {{$payment_type_class}}">
                @if($payment_details->pmt_type == "Payment")                     
					{!! Form::radio('pmt_type', 'Payment','Yes',['class'=>'flat-red js-payment-type']) !!} Payment &emsp; 
                @else 
					{!! Form::radio('pmt_type', 'Refund','Yes',['class'=>'flat-red js-payment-type']) !!} Refund &emsp;   
                @endif 
                <?php $pmt_mode = $payment_details->pmt_mode; ?>                                                  
            </div>                   

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                    <span class="bg-white med-orange margin-l-10 padding-0-4 font600 js-amt"> Payment info</span>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal margin-t-10">
                    <div class="form-group">
                        {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}

                        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8 select2-white-popup">                                    
                            {!! Form::select('insurance_id',array('' => '-- Select -- ')+(array)$insurance_detail,@$payment_details->insurance_id,['class'=>'select2 form-control', 'id' => 'js-insurance-id', 'disabled' => 'disabled']) !!}
                        </div>
                    </div>
                   <!-- <div class="form-group">
                        {!! Form::label('Pay to Addr', 'Pay to Addr', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
                        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8 select2-white-popup">                                    
                            {!! Form::select('privider_id', array('' => '-- Select -- ')+(array)$billing_providers,@$payment_details->billing_provider_id,['class'=>'select2 form-control', 'disabled' => 'disabled']) !!}
                        </div>
                    </div>-->   
                    <div class="form-group js-refund">          
                        {!! Form::label('Mode', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
                        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12"> 
                            @if($check_status == "Yes")                     
								{!! Form::radio('insur_pmt_mode', 'Check', $check_status,['class'=>'flat-red', $check_disabled]) !!} Check &emsp;
                            @elseif($cc_status == "Yes")                     
								{!! Form::radio('insur_pmt_mode', 'Credit', $cc_status,['class'=>'flat-red', $cc_disabled]) !!} CC &emsp;
                            @else
								{!! Form::radio('insur_pmt_mode', 'EFT',$eft_status,['class'=>'flat-red', $eft_disabled]) !!} EFT &emsp;
                            @endif                      
                        </div>
                    </div>                 
                    <div class="form-group  js-adjustment">
                        {!! Form::label('check no', 'Check/EFT/CC No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 js-check-no star']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8">                                    
                            {!! Form::text('check_no',$check_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly']) !!}
                        </div>                                   
                    </div>
                    @if($payment_details->pmt_mode == "Credit")
						<div class="form-group">
                        {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600']) !!}                                                
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 select2-white-popup" >                                    
								{!! Form::hidden('card_type', @$payment_details->card_type)!!}
                                {!! Form::select('card_type', ['' => '--Select--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$payment_details->card_type,['class'=>'select2 form-control','readonly' => 'readonly', 'disabled']) !!}
                            </div>                                   
                        </div> 
                    @endif
                </div>
                {!! Form::hidden('payment_id',@$payment_id) !!} 
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal left-border margin-t-10"> 
                    <div class = "js-adjustment">                       
                        <div class="form-group">
                            {!! Form::label('amount', 'Check/EFT/CC Date', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12control-label med-green font600 js-check-date star']) !!}
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10"> 
                                <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i>                                   
                                {!! Form::text('check_date', $check_date,['class'=>'form-control input-sm-header-billing  dm-date '. $add_calender, 'maxlength' => 10]) !!} 
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount', 'Amount', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label med-green font600 star']) !!}                                                  
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">                                    
                                {!! Form::text('pmt_amt',@$payment_details->pmt_amt,['class'=>'form-control input-sm-header-billing allownumericwithdecimal']) !!}
                            </div>
                        </div>
                    </div>
                    <?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Insurance'); ?>
                    <div class="form-group">
                        {!! Form::label('amount', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12control-label med-green font600 ']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                            {!! Form::text('insur_reference',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly','maxlength' => '15']) !!}
                        </div>
                    </div>
                    <div class="form-group js-hide-adjustment" style="display:none;">
                        {!! Form::label('adjustment Reason', 'Reason', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}     
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">                                    
                            {!! Form::select('adjustment_reason', array('' => '--Select--')+$adjustment_reason,@$payment_details->adjustment_reason_id,['class'=>'select2 form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group no-padding js-upload">
                        {!! Form::label('Attachment', 'Attachment', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-4 control-label-billing med-green font600']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-6 col-xs-8">
                            <span class="fileContainer upload-payment-btn" data-toggle="modal" data-target="#AddDocEra">Add Doc</span>
                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                            &emsp;<p class="js-display-error no-bottom"></p>
                        </div>
                    </div>

                </div>  
            </div>

        </div>
        @else
        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
            <div class="box-body-block no-padding" ><!--Background color for Inner Content Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                    <div class="box no-border no-shadow"><!-- Box Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-10 no-padding">              
                            <!--  1st Content Starts -->
                            <div class="box-body form-horizontal"><!-- Box Body Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 margin-t-5 col-xs-12 text-center font600 med-green {{$payment_type_class}}">                         
                                    @if($payment_details->pmt_type == "Payment")                     
										{!! Form::radio('pmt_type', 'Payment','Yes',['class'=>'flat-red js-payment-type']) !!} Payment &emsp; 
                                    @else 
										{!! Form::radio('pmt_type', 'Refund','Yes',['class'=>'flat-red js-payment-type']) !!} Refund &emsp;   
                                    @endif 
                                    <?php $pmt_mode = $payment_details->pmt_mode; ?>                                                  
                                </div>                     
                                {!! Form::hidden('patient_id',@$patient_id) !!} 
                                {!! Form::hidden('payment_id',@$payment_id) !!} 
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10 js-popuppatient-data">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="bg-white med-orange margin-l-10 padding-0-4 font600 js-amt"> Payment</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
                                        <?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Patient'); ?>
                                        <div class="col-lg-12">                                                        
                                        </div> 
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">  
                                            <div class="form-group-billing js-payment-mode">
                                                {!! Form::label('type', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">
                                                    {!! Form::select('pmt_mode', ['Check' => 'Check','Cash' => 'Cash','Credit' => 'Credit Card', 'Money Order' => 'Money Order'],@$payment_details->pmt_mode,['class'=>'select2 form-control', 'id' => 'js-payment-mode', 'disabled' => 'disabled']) !!}
                                                    {!! Form::hidden('pmt_mode',@$payment_details->pmt_mode) !!}
                                                </div>
                                            </div>
                                            @if($pmt_mode == "Check") 
                                            <div class="js-checkdetail-div">
                                                <div class="form-group-billing">
                                                    {!! Form::label('check no', 'Check No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        {!! Form::text('check_no',@$payment_details->check_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly']) !!}
                                                    </div>                                   
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('check dt', 'Check Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i>
                                                        {!! Form::text('check_date',$check_date,['maxlength'=>'25','class'=>'form-control input-sm-header-billing dm-date call-datepicker']) !!}
                                                    </div>
                                                </div>
                                            </div> 
                                            @elseif($pmt_mode == "Money Order") 
                                            <div class="js-checkdetail-div">
                                                <div class="form-group-billing">
                                                    {!! Form::label('check no', 'MO No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        {!! Form::text('money_order_no',@$payment_details->check_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly']) !!}
                                                    </div>
                                                </div>

                                                <div class="form-group-billing">
                                                    {!! Form::label('check dt', 'MO Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i>
                                                        {!! Form::text('money_order_date',$check_date,['maxlength'=>'25','class'=>'form-control input-sm-header-billing dm-date call-datepicker']) !!}
                                                    </div>
                                                </div>
                                            </div> 
                                            @elseif($pmt_mode == "Credit")   
                                            <div class="credit">
                                                <div class="form-group-billing ">
                                                    {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">
                                                        {!! Form::select('card_type', ['Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$payment_details->card_type,['class'=>'select2 form-control', 'readonly' => 'readonly', 'disabled' => 'disabled']) !!}
                                                    </div>
                                                </div> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Card No', 'Card No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star' ,'style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        {!! Form::text('card_no',@$payment_details->card_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly']) !!}
                                                    </div>
                                                </div> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Name on Card', 'Name on Card', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star p-r-0','style'=>'font-weight:600;']) !!}
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        {!! Form::text('name_on_card',@$payment_details->name_on_card,['maxlength'=>'25','class'=>'form-control input-sm-header-billing','readonly' => 'readonly']) !!}
                                                    </div>
                                                </div> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Expiry Date', 'Expiry Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green','style'=>'font-weight:600;']) !!}
                                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('cardexpiry_date')"></i>
                                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        {!! Form::text('cardexpiry_date',@$cardexpiry_date,['maxlength'=>'25','class'=>'form-control input-sm-header-billing dm-date js-payment_datepicker']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">                                                                                         
                                            <div class="form-group-billing js-payment-amount">
                                                {!! Form::label('amount', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','id' =>'Payment', 'style'=>'font-weight:600;']) !!}
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">                                    
                                                    {!! Form::text('pmt_amt',@$payment_details->pmt_amt,['class'=>'form-control input-sm-header-billing allownumericwithdecimal', 'maxlength' => '10']) !!}
                                                </div>
                                            </div>
											
                                            <div class="form-group-billing">                           
                                                {!! Form::label('ref', 'Reference', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">                                     
                                                    {!! Form::text('reference',null,['class'=>'form-control  input-sm-header-billing', 'readonly' => 'readonly','maxlength' => '15']) !!}
                                                </div> 
                                            </div>  

                                            <div class="form-group-billing no-padding js-upload">                               
                                                {!! Form::label('Attachment', 'Attachment', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                    <span class="fileContainer upload-payment-btn" data-toggle="modal" data-target="#AddDocEra">Add Doc</span>
                                                    {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                                    &emsp;<p class="js-display-error no-bottom"></p>
                                                </div>
                                            </div> 
											
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box-body Ends-->
                        </div><!--  1st Content Ends -->
                    </div><!--  Box Ends -->
                </div><!-- Only general details Content Ends -->
            </div><!-- Inner Content for full width Ends -->
        </div>        
        @endif

        @if(!empty($check_document_exist))
        <?php $document_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($check_document_exist->id, 'encode'); ?>
        {!! Form::hidden('document_id',@$document_id) !!}
        @endif

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit("Submit", ['class'=>'btn btn-medcubics-small']) !!}     
            <button class="btn btn-medcubics-small " data-dismiss="modal" aria-label="Close" type="button" style="padding: 2px 16px;">Cancel</button>
        </div>

        {!!Form::close()!!}
    </div>
</div>


<div id="AddDocEra" class="modal fade" role="dialog">
  <div class="modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Add Document
			</h4>
		</div>
      <div class="modal-body">
			<div class="box-body no-bottom no-padding"><!--Background color for Inner Content Starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
					{!! Form::open(['url'=>'','id'=>'js-bootstrap-validator-adddocument','files'=>true,'method'=>'POST','name'=>'medcubicsform','class'=>'popupmedcubicsform  medcubicsform' ]) !!}
                        <div class="box no-shadow no-bottom">
								<!-- form start -->
                            <div class="box-body form-horizontal no-bottom">                        
                                <div class="form-group">
                                    {!! Form::label('title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('title')) error @endif">
                                        {!! Form::text('title',null,['class'=>'form-control', 'autocomplete'=>'off']) !!} 
                                        {!! $errors->first('title', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
								<div class="form-group">
                                    {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('category', array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::getDocumentCategory(),'Payer_Reports_ERA_EOB',['class'=>'select2 form-control','id'=>'category','disabled'=>'disabled']) !!}
                                        {!! $errors->first('category', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
								
								<div class="form-group">
                                    {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('assigned', array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::user_list(),null,['class'=>'select2 form-control','id'=>'assigned']) !!}
                                        {!! $errors->first('assigned', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('priority')) error @endif">
										<?php $priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');  ?>
                                        {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
                                        {!! $errors->first('priority', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 p-r-0 control-label star ']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('followup',null,['class'=>'form-control dm-date','id'=>'follow_up_date','autocomplete'=>'off']) !!}
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
                                        {!! $errors->first('status', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('page', 'Pages', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::text('page',null,['class'=>'form-control js_numeric','autocomplete'=>'off','maxlength'=> 7]) !!}
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('notes', 'Notes', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label ']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::textarea('notes',null,['class'=>'form-control']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
								<div class="form-group">
                                    <?php 
										$webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam'); 
										$scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); 
									?>
                                    @if($webcam || $scanner)  
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                                            @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                                            @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div> 
                                    @endif
                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                           <div class="dropdown pull-right">
                                               <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="fa fa-question-circle margin-t-3 med-green form-icon-billing pull-right"  data-placement="top" data-toggle="tooltip" data-original-title="Info"></i>
                                               </a>
                                               <div class="dropdown-menu1">
                                                   <p class="font12 padding-4">pdf, jpeg, jpg, png, gif, doc, xls, csv, docx, xlsx, txt</p>
                                               </div>
                                           </div>
                                       </div>
                                        
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer " style="padding:1px 16px;"> 
                                                <input class="form-control form-cursor uploadFile" name="filefield" type="file" id="filefield1">Upload  </span>
                                                
                                            {!! $errors->first('filefield', '<p> :message</p>') !!} 
                                            <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                                                <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                                            </div>
                                        </div>
                                    </div>							 

                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer js-webcam-class" style="padding:1px 20px;">
                                                <input type="hidden" class="js_err_webcam" /> Webcam</span>
                                            {!! $errors->first('filefield', '<p> :message</p>') !!} 
                                            &emsp;<span class="js-display-error"></span>
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>
                                    <div class="box-footer js-scanner" style="display:none"> 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="upload_type" value="browse">
                                    <input type="hidden" name="scanner_filename" id="scanner_filename">
                                    <input type="hidden" name="scanner_image" id="scanner_image">
                                    @if($errors->first('filefield'))
                                    <div class="form-group">
                                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>
                                    @endif
                                </div><!-- /.box-body -->
                                <div class="box-footer no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                        {!! Form::submit('Continue', ['class'=>'btn btn-medcubics-small form-group']) !!}
                                        {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup margin-l-20', 'data-label'=>'close']) !!}	
                                    </div>
                                </div><!-- /.box-footer -->	
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>						
      </div>     
    </div>
  </div>
</div>

<script type="text/javascript">
	$('input[type="text"]').attr('autocomplete','off');
	
    $(document).ready(function () {
        $('input[name="check_date"]').on('change', function () {
            $('form#js-editcheck-form').bootstrapValidator('revalidateField', 'check_date');
        });
		
        $('input[name="cardexpiry_date"]').on('change', function () {
            $('form#js-editcheck-form').bootstrapValidator('revalidateField', 'cardexpiry_date');
        });
		
        $('#js-editcheck-form')
			.bootstrapValidator({
				message: 'This value is not valid',
				excluded: ':disabled',
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
					pmt_amt: {
						validators: {
							callback: {
								message: '{{ trans("practice/patients/payments.validation.valid_amt")}}',
								callback: function (value, validator) {
									if (value == '') {
										return {
											valid: false,
											message: "Enter amount"
										}
									}
									if (value != '' && value <= 0) {
										return {
											valid: false,
											message: "Should be greater than zero"
										}
									}
									return (value != '') ? true : false;
								},
							},
						}
					},
					name_on_card: {
						validators: {
							callback: {
								message: '{{trans("common.validation.name_on_card")}}',
								callback: function (value, validator) {
									chkd = $('input[name=pmt_type]:radio:checked').val();
									mode = $('input[name=pmt_mode]').val();
									if (chkd == "Payment" && mode == 'Credit') {
										return (value == '') ? false : true;
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
					insurance_id: {
						validators: {
							notEmpty: {
								message: '{{ trans("practice/patients/payments.validation.insurance_notempty")}}'
							},
						}
					},
					privider_id: {
						validators: {
							notEmpty: {
								message: '{{ trans("practice/patients/payments.validation.provider_notempty")}}'
							},
						}
					},
					card_no: {
						validators: {
							callback: {
								message: '{{trans("common.validation.card_no")}}',
								callback: function (value, validator) {
									chkd = $('input[name=pmt_type]:radio:checked').val();
									mode = $('input[name=pmt_mode]').val();
									if (chkd == "Payment" && mode == 'Credit') {
										return (value == '') ? false : true;
									}
									return true;
								},
							},
							numeric: {
								message: '{{trans("common.validation.numeric")}}'
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
								message: '{{trans("common.validation.check_date")}}',
								callback: function (value, validator) {
									chkd = $('input[name=pmt_type_ins]:radio:checked').val();
									var check_date = $('input[name="check_date"]').val();
									var current_date = new Date(check_date);
									var d = new Date();
									if (check_date != '' && d.getTime() < current_date.getTime()) {
										return {
											valid: false,
											message: '{{ trans("practice/patients/payments.validation.furute_date")}}',
										};
									}
									if (chkd == "Payment" || chkd == "Refund") {
										return (value == '') ? false : true;
									} else {
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
								message: '{{trans("common.validation.check_date")}}',
								callback: function (value, validator) {
									var exp_date = $('input[name="cardexpiry_date"]').val();
									var current_date = new Date(exp_date);
									var d = new Date();
									if (exp_date != '' && d.getTime() > current_date.getTime()) {
										return {
											valid: false,
											message: '{{ trans("practice/patients/payments.validation.past_date")}}',
										};
									}
									return true;
								}
							}
						}
					},
					filefield_eob: {
						message: '',
						validators: {
							file: {
								extension: 'pdf,jpeg,jpg,png,gif,doc',
								message: attachment_valid_lang_err_msg
							},
							callback: {
								message: attachment_length_lang_err_msg,
								callback: function (value, validator, $field) {
									if ($('[name="filefield_eob"]').val() != "") {
										var size = parseFloat($('[name="filefield_eob"]')[0].files[0].size / 1024).toFixed(2);
										var get_image_size = Math.ceil(size);
										return (get_image_size > eob_attacment_max_defined_length) ? false : true;
									}
									return true;
								}
							}
						}
					}
				},
			});
    });	
	
	$(document).ready(function () {
        $('#js-bootstrap-validator-adddocument').bootstrapValidator({
            message: 'This value is not valid',
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: alphanumericspace_lang_err_msg
                        }
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
                filefield: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: attachment_lang_err_msg
                        },
                        file: {
							maxSize: filesize_max_defined_length * 32768,
                            message: attachment_length_lang_err_msg
                        },
                        callback: {
                            message: attachment_valid_lang_err_msg,
                            callback: function (value, validator) {
                                if ($('[name="filefield"]').val() != "") {
									var extension_Arr 	= ['pdf','jpeg','jpg','png','gif','doc','xls','csv','docx','xlsx','txt','PDF','JPEG','JPG','PNG','GIF','DOC','XLS','CSV','DOCX','XLSX','TXT'];
									var file_name 		= $('[name="filefield"]')[0].files[0].name;
									var temp			= file_name.split(".");
									filename_length = ((temp.length) - 1);
									if(extension_Arr.indexOf(temp[filename_length]) == -1){
										return false;
									}else{
										return true;
									}
                                }
                                return true;
                            }
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
			e.preventDefault();  
			var formData = new FormData(this);
			$.ajax({
				type 		: 	'POST',
				url  		:	api_site_url+'/documents/paymentPostingUpload',
				data		:	formData,
				processData	: 	false,
				contentType	: 	false,
				success 	:  function(temp_type_id) {
					$('#temp_type_id').val(temp_type_id);
					$('#AddDocEra').modal('toggle');
				}
			});
		});
	});
	
	$(document).ready(function(){
		$("#follow_up_date").datepicker({minDate: 0});
	});
	
	/* Appending Category for onclick */
	$(document).on('click','span[data-target=#AddDocEra]',function(){
		$('select[name=category]').select2('val','Payer_Reports_ERA_EOB');
	});

    // Only numeric allow to enter
    $(document).on('keypress keyup blur','.js_numeric',function(event){
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>