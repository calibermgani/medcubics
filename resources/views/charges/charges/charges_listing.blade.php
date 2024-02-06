<table id="search_table_payment" class="table table-bordered table-striped mobile-width">   
    <thead>
        <tr>
			<th>Claim No</th>
            <th>Acc No</th>            
            <th>Patient Name</th>
			<th>DOS</th>
            <th>Facility</th> 
			<th>Rendering</th>
            <th>Billing</th>            
            <th>Payer</th>                      
            <th>Unbilled($)</th>                                                        
            <th>Billed($)</th>
            <th>Paid($)</th>
            <th>Pat Bal($)</th>
            <th>Ins Bal($)</th>
            <th>AR Bal($)</th>
            <th>Status</th> 
			<th>Sub Status</th> 
            <th class="hidden-print"></th>                   
        </tr>
    </thead>
    <tbody>                                   
    @if(!empty($charges))
        <?php 
			$count = 1;   
			$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
			$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();            
			$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails();
        ?>         
        @foreach($charges as $charge)                    
            <?php 
                $facility = @$charge->facility_detail;    
                $patient = @$charge->patient;                      
                $patient = @$charge->pmt_count;
                $insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone($charge->id, 'payment');
                //$insurance_payment_count = (!empty($payment_claimed_det[$charge->id])) ? $payment_claimed_det[$charge->id] : 0;
                $patient_name = App\Http\Helpers\Helpers::getNameformat(@$charge->patient->last_name, @$charge->patient->first_name, @$charge->patient->middle_name); 
               // $edit_link = App\Http\Helpers\Helpers::getChareEditLink(@$charge->id, @$insurance_payment_count, "Charge");
                $detArr = ['patient_id'=> @$charge->patient->id, 'status' => @$charge->status, 'charge_add_type' => @$charge->charge_add_type, 'claim_submit_count' => @$charge->claim_submit_count];
                $edit_link =  App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$charge->id, @$insurance_payment_count, "Charge", $detArr);

                $insurance_name = "";
                if(empty($charge->insurance_details)){
                    $insurance_name = "Self";
                } else {                                                                                                   
                    $insurance_name = !empty($insurances[$charge->insurance_details->id]) ? $insurances[$charge->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$charge->insurance_details->id);
                }
                $patient_ins_name = '';
                if(isset($patient_insurances['all'][@$patient->id])){ 
                    $patient_ins_name = $patient_insurances['all'][@$patient->id];                            
                }
                //$last = ($charge->charge_add_type == 'esuperbill' && $charge->status == 'E-bill') ? App\Models\Cpt::where('id', explode(",", $charge->cpt_codes)[0])->pluck('cpt_hcpcs') : $charge->cpt_codes;
                $charge_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($charge->id,'encode');  
                $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id,'encode');
				// When billed amount comes unbilled amount should not come
                if($charge->status == "Ready" && $charge->claim_submit_count == 0 && $insurance_payment_count == 0) {
                    $unbilled =$charge->total_charge;
					$billed = 0;
                } else {
                    $unbilled = 0; 
					$billed = $charge->total_charge;
                }            
            ?>            
            @if(isset($charge->patient) &&!empty($charge->patient))
                @if($charge->charge_add_type == 'esuperbill' && $charge->status == "E-bill"|| $charge->charge_add_type == 'ehr')
                    <?php $url = url('/charges/'.$charge_id.'/charge_edit') ?>
                @elseif($charge->status == 'Submitted' || $charge->status == 'Ready' || $charge->status == 'Denied' && $insurance_payment_count > 0 || $charge->status == 'Patient' && $insurance_payment_count > 0 || $charge->status == 'Paid' && $insurance_payment_count > 0|| $charge->status == 'Pending' && $insurance_payment_count > 0 || $charge->status == 'Hold' && $insurance_payment_count > 0)  
                    <?php $url = url('/charges/'.$charge_id.'/charge_edit') ?>                             
                @else
                    <?php $url = url('/charges/'.$charge_id.'/edit') ?>                                                                                                                     
                @endif
                <?php $popupurl = url('patients/payment/popuppayment/'.$charge_id.'/mainpopup') ?> 
                <tr data-url="{{$edit_link}}" class="js-table-click-billing"> 
					<td>
                        {{@$charge->claim_number}}                                                
                    </td>
                    <td>
                        <a href="{{ url('patients/'.$patient_id.'/ledger') }}">{{@$charge->patient->account_no}}</a>
                    </td>
                    <td>                        
                        @include ('layouts/patient_hover', array('maincharge' => 1))
                    </td>
                    <td> 
                        <a href="#" class="js-prevent-redirect" claim_number = "{{$charge->claim_number}}" data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$popupurl}}">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$charge->date_of_service, '','-') }}</a>
                    </td>   
					<td> 
                        <a id="someelem{{hash('sha256','f_'.@$charge->facility_detail->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$charge->facility_detail->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$charge->facility_detail->short_name,15,' ...')}}</a> 
                        <?php @$facility->id = 'f_'.@$charge->facility_detail->id.$count; ?> 
                        @include ('layouts/facility_hover')
                    </td>                    	
                    <td>
                        <a id="someelem{{hash('sha256','p_'.@$charge->rendering_provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$charge->rendering_provider->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$charge->rendering_provider->short_name,15,' ...')}}</a> 
                        <?php $provider = $charge->rendering_provider;  ?>
                        <?php @$provider->id = 'p_'.@$charge->rendering_provider->id.$count; ?> 
                         @include ('layouts/provider_hover')
                    </td>                    
                    <td>
                        <a id="someelem{{hash('sha256','p_'.@$charge->billing_provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$charge->billing_provider->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$charge->billing_provider->short_name,15,'..')}}</a> 
                         <?php  $provider = $charge->billing_provider;  ?>
                        <?php @$provider->id = 'p_'.@$charge->billing_provider->id.$count; ?> 
                          @include ('layouts/provider_hover')
                    </td>
                    <td>{{ @$insurance_name }}</td>              
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$unbilled)!!}</span></td>
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$billed)!!}</span></td>
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$charge->total_paid)!!}</span></td>
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$charge->patient_due)!!}</span></td>
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$charge->insurance_due)!!}</span></td>
                    <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$charge->balance_amt)!!}</span></td>                       
                    <td><span class="@if($charge->status == 'Ready')ready-to-submit @elseif($charge->status == 'Partial Paid') c-ppaid @else {{ $charge->status }} @endif">{{$charge->status}}</span></td>
					<td>
						@if(isset($claims_list->claim_sub_status->sub_status_desc))
							{{ $claims_list->claim_sub_status->sub_status_desc }}
						@else 
							-Nil-
						@endif
					</td>
                    <td class="hidden-print"> 
                        <a onClick="window.open('{{url('/getcmsform/'.$charge_id)}}', '_blank')" class = "js-prevent-redirect new-print"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                    </td>    
                </tr>
            @endif
        <?php $count++;   ?> 
        @endforeach
    @else
        
    @endif
    </tbody>
</table>  