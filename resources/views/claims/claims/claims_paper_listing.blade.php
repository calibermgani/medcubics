@include('layouts.search_fields', ['search_fields'=>$search_fields])
<div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 margin-t-10"> 
	<a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a> 
	<a class="claimdetail font600 form-cursor js-pending-claims p-r-10 p-l-5 right-border orange-b-c" data-page-url="claims/status/paper"><i class="fa fa-exclamation-triangle font14"></i> Pending</a>
	<a class="claimdetail font600 form-cursor js-electronic-claims p-r-10 p-l-5 right-border orange-b-c" data-page-url="claims/status/paper"><i class="fa fa-tv font14"></i> Electronic </a>
	<a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-r-10 p-l-10 right-border orange-b-c" data-type="print"><i class="fa fa-print font14"></i> Print</a>
	<a class="claimotherdetail font600 form-cursor js-claim-submit-paper p-l-10" data-type="download"><i class="fa fa-download font14" ></i> Download</a>
</div>

<table id="claims_table" class="table table-bordered table-striped">    
    <thead>
        <tr>
            <th class="table-select-dropdown">
               <div class="no-margin" aria-checked="false" aria-disabled="false">
                   <select name="js-select-option">
                       <option value="none">None</option>
                       <option value="page">This List</option>
                       <option value="all">All List</option>
                   </select>
                   <label for="js-select-all" style="min-height: 10px;"></label>
               </div>
			</th>
			<th>Claim No</th>
			<th>DOS</th>
			<th>Patient Name</th>
			<th>Billed To</th>
			<th>Category</th>
			<th>Payer ID</th>
			<th>Rendering</th>
			<th>Billing</th>
			<th>Facility</th>                               
			<th>Charge Amt($)</th>                                
			<th>AR Bal($)</th>                                
			<th>Created Date</th>                                
			<th>Filed Date</th>                                
			<!-- Adding class for column space
			Revision 1 - Ref: MR-1891 1 Augest 2019: Pugazh  -->
			<th Class="td-c-5" ></th>                             
		</tr>
	</thead>
	<tbody>  
		<?php 
			$count = 1;   
			$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
			$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();  
			$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('payment');  
			$billed_amounts_list = App\Models\Payments\ClaimCPTInfoV1::getAllBilledAmountByActiveLineItem();
		?>  
		@if(isset($claims))	
		@foreach($claims as $key => $claim)
		<?php 
			$facility = @$claim->facility_detail;
			$provider = @$claim->rendering_provider;
			$patient = @$claim->patient;
			$patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name);
			if (@$claim->insurance_details->payerid != '' && @$claim->self_pay == 'No')
				$class_name = 'cls-electronic';
			else if (@$claim->status == 'Patient' || $claim->self_pay == 'Yes')
				$class_name = 'cls-patient';
			else
				$class_name = 'cls-paper';
			$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id);
			$billed_amount = (!empty($billed_amounts_list[$claim->id])) ? $billed_amounts_list[$claim->id] : 0;
			$billed_amount = App\Http\Helpers\Helpers::priceFormat($billed_amount);
			$insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;

			$insurance_name = "";
			if ($claim->self_pay == 'Yes')
				$insurance_name = "Self";
			else
				$insurance_name = !empty($insurances[@$claim->insurance_details->id]) ? $insurances[@$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
			// Edit link new function call included
			$detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
			$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);

			$patient_ins_name = '';
			if (isset($patient_insurances['all'][@$claim->patient->id])) {
				$patient_ins_name = $patient_insurances['all'][@$claim->patient->id];
			}
		?>
		<tr data-url="{{$edit_link}}" class="js-table-click-billing">
			@if($claim->self_pay == 'Yes' || @$claim->insurance_details->payerid == '')
			<td style="cursor:not-allowed;">
				{!! Form::checkbox("claim_ids[]",$claim_id,null,["class" => "no-margin js-select-all-sub-checkbox $class_name",'id'=>$key,'data-claim-electronic-status','No']) !!}<label for="{{$key}}" class="no-bottom">&nbsp;</label>
			</td>
			@else
			<td>
				{!! Form::checkbox("claim_ids[]",$claim_id,null,["class" => "no-margin js-select-all-sub-checkbox $class_name",'id'=>$key,'data-claim-electronic-status','Yes']) !!}<label for="{{$key}}" class="no-bottom">&nbsp;</label>
			</td>
			@endif                                                                    
			<td>{{ !empty($claim->claim_number)? $claim->claim_number: '-Nill-'}}</td>
			<td>
				<?php $popupurl = url('patients/payment/popuppayment/'.$claim_id) ?> 
				<a href="#" class="js-prevent-redirect" claim_number="{{$claim->claim_number}}" data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$popupurl}}" class="claimbilling">{{ !empty($claim->date_of_service)? App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate'):'-Nill-'}}</a></td>
			<td>@include ('layouts/patient_hover')</td>
			<td>
				<?php /*
				  @if($claim->self_pay == 'No'){!!App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id)!!} @elseif($claim->self_pay == 'Yes') Self @endif
				 */ ?>
				{{ !empty($insurance_name)? $insurance_name:'-Nill-' }}
			</td>
			<td>
			@if(!empty($claim->insurance_category)){{ $claim->insurance_category }} @else {{'-Nill-'}} @endif
			</td>
			{{dd($claim->insurance_details->payerid)}}
			<td>@if($claim->self_pay == 'No')@if(!empty($claim->insurance_details->payerid)){{@$claim->insurance_details->payerid}}@else{{'-Nill-'}} @endif @endif</td>
			<td>
				<a id="someelem{{hash('sha256','p_'.@$claim->rendering_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->rendering_provider->id.$key)}}" href="javascript:void(0);"> {{ !empty($claim->rendering_provider->short_name)? str_limit(@$claim->rendering_provider->short_name,15,' ...'):'-Nill-'}}</a> 
				<?php @$provider->id = 'p_'.@$claim->rendering_provider->id.$key; ?> 
				@include ('layouts/provider_hover')                                        
			</td>
			<?php $provider = $claim->billing_provider; ?>
			<td>
				<a id="someelem{{hash('sha256','p_'.@$claim->billing_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->billing_provider->id.$key)}}" href="javascript:void(0);"> {{ !empty($claim->billing_provider->short_name)? str_limit(@$claim->billing_provider->short_name,15,' ...'): '-Nill-'}}</a> 
				<?php @$provider->id = 'p_'.@$claim->billing_provider->id.$key; ?> 
				@include ('layouts/provider_hover')                                        
			</td>
			<td>
				<a id="someelem{{hash('sha256','f_'.@$claim->facility_detail->id.$key)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claim->facility_detail->id.$key)}}" href="javascript:void(0);"> {{ !empty($claim->facility_detail->short_name)? str_limit(@$claim->facility_detail->short_name,15,' ...'):'-Nill-'}}</a> 
					<?php @$facility->id = 'f_'.@$claim->facility_detail->id.$key; ?> 
					@include ('layouts/facility_hover')
			</td>
			<td  class="text-right">{{ !empty($billed_amount)? $billed_amount:'-Nill-'}}</td>  
			<td  class="text-right">{{ !empty($claim->arbal)? $claim->arbal:'-Nill-'}}</td>  
			<?php /*
			<td>{{ App\Http\Helpers\Helpers::dateFormat($claim->created_at,'date')}}</td>
			*/ ?> 
			<td>@if(!empty($claim->created_at)){{ App\Http\Helpers\Helpers::dateFormat($claim->created_at, 'date')}}@else{{'-Nill-'}} @endif</td>.
			
			<td>@if(!empty($claim->filed_date)){{ App\Http\Helpers\Helpers::dateFormat($claim->filed_date,'date')}}@else{{'-Nill-'}} @endif</td>
			<td>
				<a class="js-not-click" onClick="window.open('{{url('/getcmsform/'.$claim_id)}}', '_blank')">  <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}" data-placement="bottom" data-toggle="tooltip" data-original-title="CMS 1500"></i></a>
				
				<a class="js-change-pending-claims" data-page-url="claims/status/paper" data-claim-id="{{ $claim_id }}"><i class="fa fa-exclamation-triangle" data-placement="bottom" data-toggle="tooltip" data-original-title="Pending"></i></a>
				
				@if(@$claim->insurance_details->payerid != '')
					<a class="js-change-electronic-click" data-page-url="claims/status/paper" data-claim-id="{{ $claim_id }}"><i class="fa fa-tv" data-placement="bottom" data-toggle="tooltip" data-original-title="Electronic"></i></a>
				@endif
			</td>
		</tr>
		<?php $count++;   ?> 
		@endforeach     
		@endif	
	</tbody>
</table>