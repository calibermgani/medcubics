<!-- Append search bar at popup starts here-->
@if(isset($payment_type) &&$payment_type == "payment")
    {!! Form::open(['url'=>'payments/search']) !!}
    @include ('payments/payments/append_payment', ['from' => 'paymentdetail'])
    {!!Form::close()!!} 
    @include('payments/payments/payments_popup')  
@endif
<!-- Append search bar at popup ends here-->

<!-- Claims listing after search from the popup starts here-->
@if(!empty($claims) || $type == "patient" || $type == "claim_number")
<?php 
	$patientid = @$patient_data->id;
	$get_data = ($patientid != '')?App\Models\Patients\Patient::getPatienttabData($patientid):"";
	$wallet_balance = !empty($get_data)?$get_data['wallet_balance']:"0.00";
	$patient_name = App\Http\Helpers\Helpers::getNameformat(@$patient_data->last_name, @$patient_data->first_name, @$patient_data->middle_name);
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patientid,'encode');
	$payment_hide_class = 'hide';   // Used to hide and display the continue submit
	$continue_hide_class = "";
	if(isset($payment_type) &&$payment_type == "payment") {
		$payment_hide_class = ""; 
		$continue_hide_class = "hide";
	} 
?>
{!! Form::open(['url'=>'/payments/insurancecreate', 'id' => 'js-insuranceajax']) !!}
	@if(isset($patient_name) && !empty($patient_name))
	<p class="margin-l-10 font600">
		<span class="med-green">Patient Name :</span> 
		<span class="">{{$patient_name}}</span> 
		<span class="pull-right margin-r-10">
			<span class="med-green">Balance :</span> 
			<span class="med-orange font600">{!!isset($get_data['total_ar'])?App\Http\Helpers\Helpers::priceFormat($get_data['total_ar']):"0.00"!!}</span>
		</span>
	</p>
	@endif
	<div class="box-body table-responsive  margin-t-10 no-padding js-paid-cal js_payment"><!-- Notes Box Body starts -->
	@if($type == "patient" && !empty($claims) && is_null($status) || empty($claims) && !empty($status) || !empty($claims) && !empty($status))
	<div class= "margin-l-10">
		<?php 
			$paid_yes = $pending_yes = null;
			$status = explode(',',$status);
			$all_yes = "true";
			if(in_array('All', $status)) {
				$all_yes = "true";   
			}
			if(in_array('Paid', $status)) {
				$paid_yes = "true"; 
				$all_yes  = null;  
			}
			if(in_array('pending', $status)) {
				$pending_yes = "true"; 
				 $all_yes  = null;
			}
		?>       
		<?php /*
		{!!Form::radio('claim_paid','All',$all_yes, ['class' => 'js-search-claim'])!!} <span class="med-green">All</span>&emsp;
		{!!Form::radio('claim_paid','Paid',$paid_yes, ['class' => 'js-search-claim'])!!} <span class="med-green">Paid</span> &emsp;
		{!!Form::radio('claim_paid','pending',$pending_yes, ['class' => 'js-search-claim'])!!} <span class="med-green">Pending</span>
		'id'=>'claim_paid_a', 'id'=>'claim_paid_p','id'=>'claim_paid_pen',
		*/ ?>	
		{!! Form::radio('claim_paid', 'All',$all_yes,['class'=>'js-search-claim flat-red','id'=>'r-all']) !!} 
		{!! Form::label('r-all', 'All',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
		{!! Form::radio('claim_paid', 'Paid',$paid_yes,['class'=>'js-search-claim flat-red','id'=>'r-paid']) !!} 
		{!! Form::label('r-paid', 'Paid',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
		{!! Form::radio('claim_paid', 'pending',$pending_yes,['class'=>'js-search-claim flat-red','id'=>'r-pending']) !!} 
		{!! Form::label('r-pending', 'Pending',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
	</div>
	@endif
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive claim-transaction-scroll">
            <?php  $max = Config::get("siteconfigs.payment.max_claim_choose_onsearch"); ?>
            @if(!empty($claims))
            <table class="popup-table-wo-border table table-responsive" id = "js_MainPayment">
                <thead>
                    <tr>
                        <th style="width:2%"><input type="checkbox" id="pat-checkall" class="js_menu_payment" ><label for='pat-checkall' class="no-bottom">&nbsp;</label></th>
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
                    @foreach($claims as $claim)
                    <tr>
						<?php 
							$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');
							$disabled = '';
							$insur_data = '';
							$claim_insurance_id = '';
							$insur_data_val = 'Patient';
							$claim_multi_insurance = 0;
							if(empty($claim->insurance_details)) {
								$insurance_data = "Self";
								//$disabled  = "disabled = disabled" ;
								$disabled  = "" ;
								$insur_data = "Patient";
								$claim_insurance_id = "patient";
								$insur_data_val = "Patient";
							} else {
								$insurance_data =  App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
								$insur_data = @$claim->insurance_details->id;
								$claim_multi_insurance = App\Http\Helpers\Helpers::checkIsMultiInsurance($insur_data, @$claim->patient_id);
								$claim_insurance_id = (!empty($claim->insurance_details))? $insur_data : "patient"; 
							// $insur_data_val ="Insurance";
								$url=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
								if (strpos($url, 'insurance') !== FALSE || strpos($url, 'paymentadd') !== FALSE) {
									$insur_data_val = "Insurance";
								} else {
									$insur_data_val = "Patient";
								}
							}
							$disabled_class = (App\Http\Helpers\Helpers::checkForPaymnetRequirement($claim->id,$claim->status, $insur_data_val))?"":"disabled";           
							if(isset($claim->total_adjusted) || isset($claim->total_withheld))	{
								$adj = App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim->total_adjusted, @$claim->total_withheld);
							} elseif(isset($claim->totalAdjustment) || isset($claim->withheld))	{
								//$adj = App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim->totalAdjustment, @$claim->withheld);
								// Total adjustment included with withheld.
								$adj = App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim->totalAdjustment);
							}
						?>
                        <td><a href="javascript:void(0)"><input id = "{{$claim_id}}" data-insid = "{{$claim_insurance_id}}" data-insurance = "{{$insur_data}}" type="checkbox" data-ismultiins = "{{ $claim_multi_insurance }}" class="js-sel-claim js_submenu_payment" data-max= "{{ $max}}" name = "insurance_checkbox" data-hold = "{{$claim->status}}" data-claim = "js-bal-{{$claim->claim_number}}" {{$disabled}} {{$disabled_class}}><label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>
                        <?php $url = url('patients/popuppayment/'.$claim->id); ?>
                        <td> <a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}</a></td>
                        <td>{{@$claim->claim_number}}</td>
                        <td>{!!$insurance_data!!}</td>
                        <td class="text-right">{{@$claim->total_charge}}</td>
                        <td class="text-right">
                            {!!App\Http\Helpers\Helpers::priceFormat(@$claim->total_paid)!!}
                        </td>
                        <td class="text-right">{!!$adj!!}</td> 
                        <td class="text-right" id = "js-bal-{{$claim->claim_number}}">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</td>
                        <td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status }} @endif">{{@$claim->status}}</span></td>
                    </tr>
                    @endforeach 
                </tbody>
            </table>
            @else
            <div class="text-center" style="padding-bottom:15px;"><span class="med-gray-dark">No Claims Available</span></div>
            @endif
	</div>  
	{!! Form::hidden('claim_ids',null) !!}
	{!! Form::hidden('credit_balance',$wallet_balance,['id' => 'payment_credit_balance']) !!}
	{!! Form::hidden('patient_other_ins',@$patient_data->other_ins,['id' => 'patient_other_ins']) !!}

	 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-aqua padding-10 js-show-refund" style="display:none;">
		<div class = "col-lg-6 col-md-6 col-sm-6 col-xs-6 p-l-0">
			<input type="checkbox" name = "wallet_amt" class="js-creditbalance" id="cb_refund_wallet"><label for="cb_refund_wallet" class="med-green font600">Refund from Wallet : </label> <span class="med-orange font600">$ {{$wallet_balance}}</span>
		</div>
		<div class="form-group js-show-amountbox" style="display:none;">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
				{!!Form::text('wallet_refund', null,['id' => 'js-wallet-refund','class'=>'form-control allownumericwithdecimal'])!!}
			</div>
		</div>
	</div>
	</div><!-- Notes box-body Ends-->

	<div class="box-header-view-white text-center p-b-10">
	<button class="btn btn-medcubics-small js-popupinsuranceadd <?php echo $payment_hide_class;?>"  type="button" style="padding: 2px 16px;">Continue</button> 
	<button class="btn btn-medcubics-small js-popuppatientadd <?php echo $payment_hide_class;?>"  type="button" style="padding: 2px 16px;">Continue</button>
	{!! Form::submit("Continue", ['class'=>'btn btn-medcubics-small '.$continue_hide_class, 'accesskey'=>'u']) !!} 
	{!! Form::hidden('patient_id', @$patient_id, ['id' => 'js_patient_id'])!!}
	{!! Form::hidden('change_insurance_id',"") !!}
	{!! Form::hidden('pmt_post_ins_cat',"",['id' => 'pmt_post_ins_cat']) !!}
	{!! Form::close()!!}
	<button class="btn btn-medcubics-small js-close-addclaim" accesskey="c" aria-label="Close" type="button" style="padding: 2px 16px;">Cancel</button>
	</div>
<!-- Claims listing after search from the popup ends here-->
@else
	<div class="box-body table-responsive payment-pop-scroll p-r-0 p-l-1 p-t-0">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
			<table class="popup-table-border table table-responsive" id = "js_MainPayment">
				<thead>
					<tr>
						<th style="width:2%"></th>
						<th>Acc No</th>
						<th>Patient Name</th>
						<th>DOB</th>
						<th>SSN</th>
						<th class="text-right">AR Due</th>
					</tr>
				</thead>
				<tbody>
					@if(!empty($patient_details))
						@foreach($patient_details as $keys=>$patient) 
						<?php
							if(isset($patient->patient_claim_fin) ) {
								if(!empty($patient->patient_claim_fin)) {
									$fin_data = @$patient->patient_claim_fin;
									$get_data['total_ar'] = @$fin_data[0]->total_ar;
								} else {
									$get_data['total_ar'] = 0;
								}
							} else { 
								$get_data = App\Models\Patients\Patient::getPatienttabData($patient->id);
							}	
							$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient->id,'encode');
						?>
						<tr>
							<td><input type= "checkbox" class ="js-sel-patient" name ="patient" data-id = "<?php echo $patient_id;?>" id={{$keys}}> <label for="{{$keys}}" class="no-bottom">&nbsp;</label></td>
							<td> {{ @$patient->account_no }} </td>
							<td> {{ App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name)}}</td>
							<td> {{ ($patient->dob != "0000-00-00" && @$patient->dob != "1901-01-01")?App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'dob'):"-" }}</td>
							<td> {{ !empty($patient->ssn)?$patient->ssn:"-"}} </td>
							<td class="text-right"> {!!App\Http\Helpers\Helpers::priceFormat($get_data['total_ar'])!!} </td>
						</tr>
						@endforeach
					@else
						<tr><td colspan="6"><p class="med-gray-dark text-center">No Patients Available</p></td></tr>
					@endif
				</tbody>
			</table>
		</div>
	</div><!-- Notes box-body Ends-->
@endif