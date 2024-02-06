@if(!empty((array)$charges))
	<?php
		$count = 1;
		$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
		$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();            
		// Patient copay payment included 
		$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('patient');
	?>
	@foreach($charges as $charge)                    
		<?php 
			$facility = @$charge;                     
			$provider = @$charge; 
			$patient = @$charge;                      
			$insurance_payment_count = (!empty($payment_claimed_det[$charge->claim_id])) ? $payment_claimed_det[$charge->claim_id] : 0;
			$patient_name = App\Http\Helpers\Helpers::getNameformat(@$charge->last_name, @$charge->first_name, @$charge->middle_name); 
			$detArr = ['patient_id'=> @$charge->id, 'status' => @$charge->status, 'charge_add_type' => @$charge->charge_add_type, 'claim_submit_count' => @$charge->claim_submit_count];
			$edit_link =  App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$charge->claim_id, @$insurance_payment_count, "Charge", $detArr);

			$insurance_name = "";
			if($charge->insurance_id==0){
				$insurance_name = "Self";
			} else {                                                                                                   
				$insurance_name = !empty($insurances[$charge->insurance_id]) ? $insurances[$charge->insurance_id] : App\Http\Helpers\Helpers::getInsuranceName(@$charge->insurance_id);
			}
			$patient_ins_name = '';
			if(isset($patient_insurances['all'][@$patient->patient_id])){ 
				$patient_ins_name = $patient_insurances['all'][@$patient->patient_id];                            
			}
			//$last = ($charge->charge_add_type == 'esuperbill' && $charge->status == 'E-bill') ? App\Models\Cpt::where('id', explode(",", $charge->cpt_codes)[0])->pluck('cpt_hcpcs') : $charge->cpt_codes;
			$charge_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($charge->claim_id,'encode');  
			$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->patient_id,'encode');
			// When billed amount comes unbilled amount should not come
        	$charge_amt = App\Http\Helpers\Helpers::BilledUnbilled($charge);
        	$billed = isset($charge_amt['billed'])?$charge_amt['billed']:0.00;
        	$unbilled = isset($charge_amt['unbilled'])?$charge_amt['unbilled']:0.00;
		?>            
		@if(isset($charge) &&!empty($charge))
			<?php
				if($charge->charge_add_type == 'esuperbill' && $charge->status == "E-bill"|| $charge->charge_add_type == 'ehr') {
					$url = url('/charges/'.$charge_id.'/charge_edit');
				} elseif($charge->status == 'Submitted' || $charge->status == 'Ready' || $charge->status == 'Denied' && 	$insurance_payment_count > 0 || $charge->status == 'Patient' && $insurance_payment_count > 0 || $charge->status == 'Paid' && $insurance_payment_count > 0|| $charge->status == 'Pending' && $insurance_payment_count > 0 || $charge->status == 'Hold' && $insurance_payment_count > 0) {
					$url = url('/charges/'.$charge_id.'/charge_edit');
				} else {
					$url = url('/charges/'.$charge_id.'/edit');
				}
				$popupurl = url('patients/payment/popuppayment/'.$charge_id.'/mainpopup');
				$dos = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$charge->date_of_service, '','-');
			?>
			<tr data-url="{{$edit_link}}" class="js-table-click-billing"> 
				<td>
					<?php
					$selected_icd = App\Models\Icd::getIcdValues($charge->icd_codes, 'yes');
					?>
					<a href="{{ $edit_link }}"><span class="someelem" data-id="icd{{ @$charge->claim_id }}" id="someelemicd{{ @$charge->claim_id }}">{{ !empty($charge->claim_number)? $charge->claim_number: '-Nil-'}}</span></a>					
					<div class="js-tooltip_icd{{$charge->claim_id }}" style='display:none;'><span style='display:block; padding-bottom: 2px; margin-bottom: 5px;'>{{ implode(',', @$selected_icd) }}</span></div>					
				</td>
				<td>
					<a href="{{ url('patients/'.$patient_id.'/ledger') }}">{{ !empty($charge->account_no)? $charge->account_no:'-Nil-'}}</a>
				</td>
				<td>                        
					@include ('layouts/patient_hover', array('maincharge' => 1))
				</td>
				<td> 
					<a href="#" class="js-prevent-redirect" claim_number = "{{$charge->claim_number}}" data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$popupurl}}" class="claimbilling">{{ $dos }}</a>
				</td>   
				<td> 
					<a id="someelem{{hash('sha256','f_'.@$charge->facility_id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$charge->facility_id.$count)}}" href="javascript:void(0);"> {{!empty($charge->facility_short_name)? str_limit(@$charge->facility_short_name,15,' ...'):'-Nil-' }}</a> 
					<?php @$facility->id = 'f_'.@$charge->facility_id.$count; ?>
					@include ('layouts/facility_hover')
				</td>
				<td>
					<a id="someelem{{hash('sha256','p_'.@$charge->rendering_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$charge->rendering_provider_id.$count)}}" href="javascript:void(0);"> {{!empty($charge->rendering_short_name)? str_limit(@$charge->rendering_short_name,15,' ...'):'-Nil-'}}</a> 
					<?php 
						@$provider->id = 'p_'.@$charge->rendering_provider_id.$count; 
						@$provider->provider_name = @$charge->rendering_short_name;
						@$provider->provider_dob = @$charge->rendering_dob;
						@$provider->gender = @$charge->rendering_gender;
						@$provider->etin_type = @$charge->rendering_etin_type;
						@$provider->etin_type_number = @$charge->rendering_etin_no;
						@$provider->npi = @$charge->rendering_npi;
						$provider->type_name = 'Rendering';
					?>
					@include ('layouts/provider_hover')
				</td>                    
					<?php $provider = $charge; ?>
				<td>
					<a id="someelem{{hash('sha256','p_'.@$charge->billing_provider_id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$charge->billing_provider_id.$count)}}" href="javascript:void(0);"> {{!empty($charge->billing_short_name)? str_limit(@$charge->billing_short_name,15,'..'):'-Nil-'}}</a> 
					<?php 
						@$provider->id = @$provider->billing_provider_id = 'p_'.@$charge->billing_provider_id.$count; 
						@$provider->provider_name = @$charge->billing_short_name;
						@$provider->provider_dob = @$charge->billing_dob;
						@$provider->gender = @$charge->billing_gender;
						@$provider->etin_type = @$charge->billing_etin_type;
						@$provider->etin_type_number = @$charge->billing_etin_no;
						@$provider->npi = @$charge->billing_npi;
						$provider->type_name = 'Billing';						
					?>
					@include ('layouts/provider_hover')
				</td>
				<td>{{!empty($insurance_name)? $insurance_name:'-Nil-' }}</td>              
				<td><span class="pull-right">{!! !empty($unbilled)? App\Http\Helpers\Helpers::priceFormat(@$unbilled):'-Nil-' !!}</span></td>
                <td><span class="pull-right">{!! !empty($billed)? App\Http\Helpers\Helpers::priceFormat(@$billed):'-Nil-' !!}</span></td>
				<td><span class="pull-right">{!! !empty($charge->total_paid)? App\Http\Helpers\Helpers::priceFormat(@$charge->total_paid):'-Nil-' !!}</span></td>
				<td><span class="pull-right">{!! !empty($charge->patient_due)? App\Http\Helpers\Helpers::priceFormat(@$charge->patient_due):'-Nil-' !!}</span></td>
				<td><span class="pull-right">{!! !empty($charge->insurance_due)? App\Http\Helpers\Helpers::priceFormat(@$charge->insurance_due):'-Nil-' !!}</span></td>
				<td><span class="pull-right">{!! !empty($charge->balance_amt)? App\Http\Helpers\Helpers::priceFormat(@$charge->balance_amt):'-Nil-' !!}</span></td>
				<td><span class="@if($charge->status == 'Ready')ready-to-submit @elseif($charge->status == 'Partial Paid') c-ppaid @else {{ $charge->status }} @endif">{{ !empty($charge->status)? $charge->status : '-Nil-'}}</span></td>
				<td>
					@if(isset($charge->sub_status_desc))
						{{ !empty($charge->sub_status_desc)? $charge->sub_status_desc:'-Nil-' }}
					@else 
						-Nil-
					@endif
				</td>
				<td class="hidden-print"> 
                    <a onClick="window.open('{{url('/getcmsform/'.$charge_id)}}', '_blank')" class = "js-prevent-redirect new-print"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
				</td>    
			</tr>
		@endif
	<?php $count++; ?>
	@endforeach
@else
    
@endif