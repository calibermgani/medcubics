<!-- Patient payment posting starts here -->
<?php
	$id = Route::current()->parameter('id');
	$credit_balance = '';
	$url = 'patients/' . $id . '/payments/create';
	if (isset($tab) && $tab == "patient")   // To open check from main payment
		$url = "payments/create";
	$patient = (is_null($id) && !empty($payment_details) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_details->patient_id, 'encode') : $id);
	$credit_balance_val = !is_null($patient) ? App\Models\Patients\Patient::getPatienttabData($patient) : "0.00"; // credit balance from check amount
	$credit_balance = (!empty($credit_balance_val) && is_array(($credit_balance_val))) ? $credit_balance_val['wallet_balance'] : "0.00";
	$style_class = "style='display:none;'";
	if (!empty($claims_lists))
		$style_class = '';
	$payment_type = @$payment_details->pmt_type;
	$check_date = (isset($payment_details->check_date) && $payment_details->check_date != '0000-00-00') ? date('m/d/Y', strtotime($payment_details->check_date)) : "";
	$cardexpiry_date = (isset($payment_details->cardexpiry_date) && $payment_details->check_date != '0000-00-00') ? date('m/d/Y', strtotime($payment_details->cardexpiry_date)) : "";
	$payment_detail_id = isset($payment_details->id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->id, 'encode') : "";
	$patient_id = isset($payment_details->id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->patient_id, 'encode') : "";
	$bootsrapeid = (!empty($payment_type) && $payment_type == "Payment") ? "" : "js-bootstrap-validator";
	$check_validation = empty($payment_details) ? "js-check-number" : "";
	$check_date_datepicker = empty($payment_details) ? "call-datepicker" : "";
	$sel_claim = @$claim_id;
	$select_claim_count = count((array)$sel_claim) ;
	$claims_count = count($claims_lists);
	$default_checked_main = ($claims_count == $select_claim_count)? 'checked':'';
	if(!isset($get_default_timezone)){
		$get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
	}
	//dd($url);
?>
{!! Form::open(['url'=> $url,'class'=>'js-avoid-savepopup js-patient-paymentform paymentpopupform', 'id'=>$bootsrapeid, 'files'=>true]) !!}
@if($credit_balance >0)
{!! Form::hidden('credit_balance',$credit_balance,['id' => 'payment_credit_balance']) !!}
@endif
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
									@if(!empty($payment_type) && $payment_type == "Refund")
										<?php $payment_text = "Refund"; ?>
										<span style="display: none"> {!! Form::radio('payment_type', $payment_type ,'Yes',['class'=>'js-payment-type foc','tabindex'=>'1']) !!} {{$payment_type}} &emsp;</span>
									@elseif(!empty($payment_type) && ($payment_type == "Payment" || $payment_type == "Credit Balance")  )
										<?php $payment_text = "Credit Balance"; ?>
										<span style="display: none"> {!! Form::radio('payment_type', "Credit Balance" ,'Yes',['class'=>'js-payment-type foc','id'=>'cc-credit']) !!} {!! Form::label('cc-credit', 'Credit Balance',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;</span>
									@else
										<?php $payment_text = "Payment"; ?>
										{!! Form::radio('payment_type', 'Payment','Yes',['class'=>'js-payment-type foc','id'=>'c-payment']) !!} {!! Form::label('c-payment', 'Payment',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
										{!! Form::radio('payment_type', 'Refund',null,['class'=>'js-payment-type foc','id'=>'c-refund']) !!} {!! Form::label('c-refund', 'Refund',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
										{!! Form::radio('payment_type', 'Adjustment',null,['class'=>'js-payment-type foc','id'=>'c-adjustment']) !!} {!! Form::label('c-adjustment', 'Adjustment',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
										{!! Form::radio('payment_type', 'Credit Balance',null,['class'=>'js-payment-type foc','id'=>'c-credit']) !!} {!! Form::label('c-credit', 'Credit Balance',['class'=>'med-darkgray font600 form-cursor']) !!}
									@endif
								</div>
								{!! Form::hidden('payment_detail_id',@$payment_detail_id, ['id' => "js-payment-detail-id"]) !!}
								{!! Form::hidden('patient_id',!empty($patient)?$patient:$patient_id) !!}
								{!! Form::hidden('payment_method','Patient') !!}
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-10 js-popuppatient-data">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
										<span class="bg-white med-orange margin-l-10 padding-0-4 font600 js-amt"> <?php echo $payment_text; ?></span>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-5">
										<?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Patient'); ?>
										<div class="col-lg-12">
										</div>
										<input type="hidden" name="temp_type_id" value="" id="temp_type_id" />
										@if(!empty($payment_type)  && $payment_type == "Payment")

										<div class="form-group-billing js-payment-amount">
											{!! Form::label('amount', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 font600 control-label med-green margin-l-10','id' =>'Payment']) !!}
											<div class="col-lg-3 col-md-3 col-sm-5 col-xs-8">
												{!! Form::text('payment_amt_pop',@$credit_balance,['class'=>'form-control input-sm-header-billing allownumericwithdecimal', 'maxlength' => '10', 'autocomplete'=>'nope']) !!}
												{!! Form::hidden('payment_amt_calc',@$credit_balance,['class'=>'form-control']) !!}
											</div>
										</div>
										@else
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal js-hide-creditbalance">
											<div class="form-group-billing js-payment-mode">
												{!! Form::label('type', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green','style'=>'font-weight:600;']) !!}
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">
													{!! Form::hidden('payment_mode', @$payment_details->payment_mode)!!}
													{!! Form::select('payment_mode', ['Check' => 'Check','Cash' => 'Cash','Credit' => 'Credit Card', 'Money Order' => "Money Order"],@$payment_details->payment_mode,['class'=>'select2 form-control', 'id' => 'js-payment-mode']) !!}
												</div>
											</div>

											<div class="form-group-billing js-hide-adjustment" style="display:none;">
												{!! Form::label('adjustment Reason', 'Reason', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 select2-white-popup">
													{!! Form::select('adjustment_reason', array('' => '-- Select -- ')+$adjustment_reason,@$payment_details->adjustment_reason_id,['class'=>'select2 form-control js-adjust']) !!}
												</div>
											</div>

											<div class="js-checkdetail-div">
												<div class="form-group-billing">
													{!! Form::hidden('checkexist', null) !!}
													{!! Form::label('check no', 'Check No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
														
														<?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>
														{!! Form::text('check_no',@$payment_details->check_no,['maxlength'=>$lengthval,'class'=>'form-control input-sm-header-billing '.$check_validation, 'data-type'=> 'Patient', 'autocomplete'=>'nope']) !!}
													</div>
												</div>

												<div class="form-group-billing">
													{!! Form::label('check dt', 'Check Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star']) !!}
													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
														<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i>
														{!! Form::text('check_date',$check_date,['maxlength'=>'10','class'=>'form-control input-sm-header-billing dm-date '.$check_date_datepicker, 'autocomplete' => 'nope']) !!}
													</div>
												</div>
											</div>


											<div class="js-carddetail-div" style="display:none;">
												<div class="form-group-billing ">
													{!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 ']) !!}
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
													{!! Form::label('Name on Card', 'Name on Card', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star p-r-0']) !!}
													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
														{!! Form::text('name_on_card',@$payment_details->name_on_card,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
													</div>
												</div>                                                                                    
											</div>
											<div class="js-hide-money" style="display:none;">
												 <div class="form-group-billing">
													{!! Form::label('Money order No.', 'MO No', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
														{!! Form::text('money_order_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
													</div> 
												</div>
												 <div class="form-group-billing">
													 {!! Form::label('Money order No.', 'MO Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green star','style'=>'font-weight:600;']) !!}
													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">   
													  <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('money_order_date')"></i>                                 
														{!! Form::text('money_order_date',null,['class'=>'form-control input-sm-header-billing dm-date call-datepicker']) !!}
													</div>  
												</div>  
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="js-carddetail-div" style="display:none;">
												<div class="form-group-billing">
													{!! Form::label('Expiry Date', 'Expiry Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600']) !!}

													<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
													  <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('cardexpiry_date')"></i>
														{!! Form::text('cardexpiry_date',@$cardexpiry_date,['maxlength'=>'10','class'=>'form-control input-sm-header-billing dm-date js-payment_datepicker']) !!}
													</div>
												</div>
											</div>
											<div class="form-group-billing js-payment-amount">
												{!! Form::label('amount', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label med-green font600 star','id' =>'Payment']) !!}
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 ">
													{!! Form::text('payment_amt_pop',@$payment_details->balance,['class'=>'form-control input-sm-header-billing allownumericwithdecimal js_amt_format', 'autocomplete'=>'nope']) !!}
													{!! Form::hidden('payment_amt_calc',@$payment_details->balance,['class'=>'form-control']) !!}
													{!! Form::hidden('payamt',null,['id'=>'payamount']) !!}
												</div>

											</div>
											<div class="form-group-billing js-hide-creditbalance">
												{!! Form::label('ref', 'Reference', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}
												<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 ">
													{!! Form::text('reference',null,['class'=>'form-control  input-sm-header-billing','maxlength' => 20, 'autocomplete'=>'nope']) !!}
												</div>
											</div>


											<div class="form-group-billing">

												@if(empty($payment_type))
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding  js-upload">
													{!! Form::label('ref', 'Attachment', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}
													<div class="col-lg-7 col-md-7 col-sm-6 col-xs-8 no-padding  margin-b-10 margin-l-10">
														<span class="fileContainer upload-payment-btn" data-toggle="modal" data-target="#AddDocEra">Add Doc</span>
													</div>
												</div>
												@endif
											</div>
										</div>
										@endif
										{!! Form::hidden('claim_ids',@$sel_claim,['id' => 'payment_claim_id']) !!}

									</div>
								</div>

							</div><!-- /.box-body Ends-->

						</div><!--  1st Content Ends -->

					</div><!--  Box Ends -->
				</div><!-- Only general details Content Ends -->

			</div><!-- Inner Content for full width Ends -->
		</div><!--Background color for Inner Content Ends -->


		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p class="pull-right font600"><a href = "javascript:void(0)" data-url="{{url('patients/'.$id.'/payments/addtowallet')}}" class="js-addtowallet" id="js-addwallet"> <i class="fa {{Config::get('cssconfigs.payments.payments')}}"></i> Add to wallet</a>
			   <?php//  if($credit_balance != 0) ?>
				<a href = "javascript:void(0)" data-url="{{url('patients/'.$id.'/payments/addtowallet/type')}}" class="js-addtowallet js-remove-wal" id = "js-removewallet" style="display:none;"><i class="fa {{Config::get('cssconfigs.payments.payments')}}"></i> Refund from wallet</a></p>
			   <?php//   endif ?>
		</div>
		@if(empty($claims_lists))
		<div class="js-patient-search" style="display:none;"><!-- Notes Box Body starts -->
			<div class="col-md-12 m-b-m-12" >
				<div class="box box-info no-shadow" style="border: 1px solid #f1d392">
					<div class="box-body form-horizontal border-radius-4 p-b-0" style="background: #f9f0db;">

						<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 no-bottom form-horizontal"  style=" margin-top: -4px;">
							<div class="form-group-billing">
								<div class="col-lg-10 col-md-10 col-sm-12 col-xs-10 billing-select2-orange">
									{!! Form::select('patient_detail',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','claim_number'=>'Claim No','account_no'=>'Acc no','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),null,['class'=>'js-search-popup form-control select2', 'id' => 'PatientSearch']) !!}
								</div>
							</div>
						</div>
						<div class="js-hide-datepicker col-lg-6 col-md-6 col-sm-6 col-xs-6 no-bottom form-horizontal"  style="margin-bottom: -8px;  border-color:#8ce5bb;  margin-top: -4px;">
							<div class="form-group-billing">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									{!! Form::text('search_val',null,['id' => 'js-search-val','class'=>'js-search-text form-control input-sm-header-billing textbox-bg-orange','style'=>'border:1px solid #ccc;', 'accesskey'=>'s']) !!}
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-bottom form-horizontal m-b-m-8 margin-t-m-4 js-show-datepicker"  style="border-color:#8ce5bb;display:none;">
							<div class="form-group-billing">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('search_val_date')"></i>
									{!! Form::text('search_val_date',null,['class'=>'form-control input-sm-header-billing textbox-bg-orange yes-border dm-date call-datepicker']) !!}
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-2 col-xs-2 no-bottom form-horizontal m-b-m-8 margin-t-m-4"  style="border-color:#8ce5bb; ">
							<div class="form-group-billing">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 ">
									<a href= "javascript:void(0)" class="btn btn-medcubics-small pull-left js-search-patient margin-t-0">Search</a>
									<a href= "javascript:void(0)" class="btn btn-medcubics-small js-reset-patient margin-l-10 margin-t-0">Reset</a>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
			@endif
		</div>
	</div>
	<?php  $claims_class = (!empty($claims_lists))?"js_popup_claimpatient_table":"";?>
	<div class = "js-append-mainpayment-table" {{$style_class}}>
		<div class="box-body no-padding "><!-- Notes Box Body starts -->
			<div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12 chat ar-notes js_payment no-padding">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive payment-pop-scroll">
					<table id = "{{$claims_class}}" class="popup-table-wo-border table table-responsive">
						<thead>
							<tr>
								<th style="width:2%"><input type="checkbox" id="pat-checkall" class="js_menu_payment" {{$default_checked_main}}><label for='pat-checkall' class="no-bottom">&nbsp;</label></th>
								<th>DOS</th>
								<th>Claim No</th>
								<th>Billed To</th>
								<th class="text-right">Charge Amt</th>
								<th class="text-right">Paid</th>
								<th class="text-right">Adj</th>
								<th class="text-right">Balance</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php $max = Config::get("siteconfigs.payment.max_claim_choose_onsearch");  ?>
							@if(!empty($claims_lists))
							@foreach($claims_lists as $claim)
							<tr>
								<?php 
									$disabled_class = (App\Http\Helpers\Helpers::checkForPaymnetRequirement($claim->id,$claim->status, 'Patient'))?"":"disabled"; $claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');
									$default_checked = ($sel_claim != '' && $sel_claim == $claim_id)? 'checked':''; 
								?>
								<td><a href="javascript:void(0)"><input id = "{{$claim_id}}" type="checkbox"
								class="js-sel-pay js_submenu_payment" data-max= "{{ $max}}"
								data-claim = "js-bal-{{$claim->claim_number}}" {{$default_checked}} {{$disabled_class}}><label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>
								<?php $url = url('patients/popuppayment/'.$claim->id) ?>
								<td> <a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}</a></td>
								<td>{{@$claim->claim_number}}</td>
								@if(empty($claim->insurance_details))
								<td>Self</td>
								@else
								<td>
									{!!App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id)!!}
								</td>
								@endif
								<td class="text-right">{{@$claim->total_charge}}</td>
								{!!Form::hidden('patient_paid_amt', $claim->patient_paid,['class' => 'js-bal-'.$claim->claim_number])!!}
								<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat(@$claim->total_paid)!!}</td>
								<td class="text-right"> {!!$claim->totalAdjustment!!}</td>
								<td class="text-right" id = "js-bal-{{$claim->claim_number}}">
									{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</td>

								<td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status }} @endif">{{@$claim->status}}</span></td>
							</tr>
							@endforeach
							@else
							<tr><td colspan="9" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
		   @if($credit_balance != 0)
			<?php /*    - Commented since refund from claim and wallet not getting updated the previous check as posted issue 
			MR-1358 - Payment: Wallet History: Amount is not reduced in Unposted column after doing patient claim refund along with wallet refund.
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-aqua padding-10 js-show-refund" style="display:none;">
				<div class = "col-lg-6 col-md-6 col-sm-6 col-xs-6 p-l-0">
				   <input type="checkbox" name = "wallet_amt" class="js-creditbalance" id="refund_wallet"><label for="refund_wallet" class="med-green font600">Refund from Wallet : </label> <span class="med-orange font600">$ {{$credit_balance}}</span>
				</div>
				<div class="form-group js-show-amountbox" style="display:none;">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
						{!!Form::text('wallet_refund', null,['id' => 'js-wallet-refund','class'=>'form-control allownumericwithdecimal'])!!}
					</div>
				</div>
			</div>
			*/ ?>
			@endif
		</div><!-- Notes box-body Ends-->
		<div class="box-header-view-white ar-bottom-border text-center">
			{!! Form::submit("Continue", ['class'=>'btn btn-medcubics-small margin-b-10', 'accesskey'=>'u']) !!}
			<button class="btn btn-medcubics-small js-close-addclaim margin-b-10" accesskey="c" aria-label="Close" type="button" style="padding: 2px 16px;">Cancel</button>
			{!! Form::close() !!}
		</div>
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
										{!! Form::text('title',null,['class'=>'form-control']) !!} 
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
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
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
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
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
												<input class="form-control form-cursor js_file_field uploadFile" name="filefield" type="file" id="filefield1">Upload  </span>

											{!! $errors->first('filefield',  '<p> :message</p>')  !!} 
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
											{!! $errors->first('filefield',  '<p> :message</p>')  !!} 
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

<!--Patient payment posting ends here -->
<script type="text/javascript">
$('input[type="text"]').attr('autocomplete','off');
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
		// Revision 1 : MR-2895 : 26 Sep 2019 : Selva
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
<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>