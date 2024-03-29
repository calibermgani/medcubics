@include('layouts.search_fields', ['search_fields'=>$search_fields])

<div class="btn-group col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-b-10">
	<span class="btn-group">
        <a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a> 
		<a id="js_generate_rejection_report" class="form-cursor font600 p-r-0"><i class="fa fa-pie-chart"></i> Generate Rejection Report</a>
	</span>
</div>
{!! Form::open(['url'=>'claims/initialscrubbing','id'=>'js-claim-submit-electronic','name'=>'electronic_claim','class'=>'electronic_claim']) !!}    
	
    <table id="claims_table" class="table table-bordered table-striped">    
        <thead>
            <tr>                           
                <th>Claim No</th>
                <th>DOS</th>
                <th>ACC No</th>
                <th>Patient Name</th>
                <th>Charge Amt($)</th>
                <th>Payer</th>
                <th>Payer ID</th>
                <th>Submitted Date</th>
                <th>Rejected Date</th>
                <!-- Adding class for column space
			    Revision 1 - Ref: MR-1891 1 Augest 2019: Pugazh  -->
                <th Class="td-c-5" ></th>                              
            </tr>
        </thead>
        <tbody>              
            <?php 
				$count = 1;
                $payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('payment');  
                $billed_amounts_list = App\Models\Payments\ClaimCPTInfoV1::getAllBilledAmountByActiveLineItem();
            ?>   				
            @foreach($claims as $key => $claim)
				@if(!empty($claim->patient) && !empty($claim->insurance_details))
                <?php
					$facility = @$claim->facility_detail; 
					$provider = @$claim->rendering_provider;
					$patient = @$claim->patient;
					$patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name);
					$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim->patient->id,'encode');
				   
                    if(@$claim->insurance_details->payerid != '' && @$claim->self_pay == 'No')
                        $class_name = 'cls-electronic';
                    elseif(@$claim->status == 'Patient' || $claim->self_pay == 'Yes')
                        $class_name = 'cls-patient';
                    else
                        $class_name = 'cls-paper';
                    $claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id);
                    $billed_amount = (!empty($billed_amounts_list[$claim->id])) ? $billed_amounts_list[$claim->id]: 0;
                    $billed_amount = App\Http\Helpers\Helpers::priceFormat($billed_amount);
                    $insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;

                    $insurance_name = "";                                   
                    $detArr = ['patient_id'=> @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
                    $edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);
                ?>
                <tr data-url="{{$edit_link}}" class="js-table-click-billing">
					<td>
						{{$claim->claim_number}}
					</td>
                    <td class="text-left">
                        <?php $popupurl = url('patients/payment/popuppayment/'.$claim_id) ?> 
						<a href="#"  claim_number="{{$claim->claim_number}}" data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$popupurl}}" class="claimbillingclass js-prevent-redirect">{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</a>						
					</td>
                   <td> <a href="{{ url('patients/'.$patient_id.'/ledger') }}" target="_blank"><span class="js-not-click js-prevent-redirect"> {!! $claim->patient->account_no !!}</span></a></td>
					<td>@include ('layouts/patient_hover')</td>
					<td class="text-right">{{$billed_amount}}</td>
                    <?php $provider = $claim->billing_provider; ?>
                    <td>{!! $claim->insurance_details->short_name !!}</td>
					<td>@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>
                    <td class="text-left">					
					   {{App\Http\Helpers\Helpers::dateFormat($claim->submited_date,'claimdate')}}
                    </td>		
                    <td class="text-left">{{App\Http\Helpers\Helpers::dateFormat(@$claim->rejected_date)}}</td>      
					<?php $fileType = explode('.',$claim->response_file_path); ?>
					@if(@$fileType[1] != '277')
						<td>
							<?php $data = str_replace('"', '',$claim->denial_codes); ?>
							<span><i data-claim-no="{{ $claim->claim_number }}" data-denial-codes="{{ $data }}"  class="fa fa-external-link js-rejection-denial-popup js-prevent-redirect"  data-toggle="modal" data-target="#js-rejection-denial-popup" ></i></span>
						</td>
					@else
						<td>
							<a class="js-not-click js-prevent-redirect" href="{{ url('download-response-file/'.@$claim->response_file_path) }}" target="_self"><i class="fa fa-file-text-o js-prevent-redirect" data-placement="bottom" data-toggle="tooltip" data-original-title="Rejection file"></i></a>
						</td>
					@endif
                </tr>
				@endif
            @endforeach                                                 
        </tbody>
    </table>
{!! Form::close() !!}