<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Revenue Analysis Report</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>
    <body>
		<?php 
		@$headers = array_except((array)$result['search_by'],['function_name','controller_name','report_name','practice_id','export','_token']);
		@$header = $result['header'];
		@$title = $result['title'];
		@$createdBy = $result['createdBy'];
		@$practice_id = $result['practice_id'];
		@$result = $result['result'];
		$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr style="background: #fff;">
                <td colspan="17" style="text-align: center;color: #00877f;font-weight: 800">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">Revenue Analysis Report</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;"><span>User :</span><span> {{  @$createdBy }} </span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">
                    @if($headers !='' && count((array)$headers)>0)
                    <?php $i = 1; ?>
                    @foreach($headers as $header_name => $header_val)
                    <span class="med-green">
                        <?php $hn = $header_name; 
                        if(ucfirst(str_replace('_', ' ', @$header_name))=='Facility')
                        	$facility_name = @array_flatten(App\Models\Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ',  ') as short_name")->whereIn('id',array_filter(explode(',',$header_val)))->get()->toArray())[0];
                    	if(ucfirst(str_replace('_', ' ', @$header_name))=='Rendering' || ucfirst(str_replace('_', ' ', @$header_name))=='Billing' || ucfirst(str_replace('_', ' ', @$header_name))=='Referring')
                        	$provider_name = @array_flatten(App\Models\Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ',  ') as short_name")->whereIn('id',array_filter(explode(',',$header_val)))->get()->toArray())[0];
                        if(ucfirst(str_replace('_', ' ', @$header_name))=='Insurance id')
                        	$insurance_name = @array_flatten(App\Models\Insurance::selectRaw("GROUP_CONCAT(short_name SEPARATOR ',  ') as short_names")->whereIn('id',array_filter(explode(',',$header_val)))->get()->toArray())[0];
                        if(ucfirst(str_replace('_', ' ', @$header_name))=='Status reason')
                        	$sub_status_desc = @array_flatten(App\Models\ClaimSubStatus::selectRaw("GROUP_CONCAT(sub_status_desc SEPARATOR ',  ') as short_names")->whereIn('id',array_filter(explode(',',$header_val)))->get()->toArray())[0];
                        if(ucfirst(str_replace('_', ' ', @$header_name))=='Hold reason')
                        	$hold_reason = @array_flatten(App\Models\Holdoption::selectRaw("GROUP_CONCAT(option SEPARATOR ',  ') as short_names")->whereIn('id',array_filter(explode(',',$header_val)))->get()->toArray())[0];
                        	?>
                        	@if(ucfirst(str_replace('_', ' ', @$header_name))=='Insurance id')
                        	Payer
                        	@else
		                        {{ ucfirst(str_replace('_', ' ', @$header_name)) }}
	                        @endif
	                    </span> : 
	                    @if(ucfirst(str_replace('_', ' ', @$header_name))=='Facility')
	                    	{{$facility_name}}
                    	@elseif(ucfirst(str_replace('_', ' ', @$header_name))=='Rendering' || ucfirst(str_replace('_', ' ', @$header_name))=='Billing' || ucfirst(str_replace('_', ' ', @$header_name))=='Referring')
	                    	{{$provider_name}}
                    	@elseif(ucfirst(str_replace('_', ' ', @$header_name))=='Insurance id')
	                    	{{$insurance_name}}
	                    @elseif(ucfirst(str_replace('_', ' ', @$header_name))=='Status reason')
		                    {{$sub_status_desc}}
	                    @elseif(ucfirst(str_replace('_', ' ', @$header_name))=='Hold reason')
		                    {{$hold_reason}}
                    	@else
	                        {{ @$header_val}} 
                        @endif 
                        @if($i < count((array)$headers)) | @endif <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
		<table class="table table-bordered table-striped dataTable">
			<thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
            <tr>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>            
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Created Date</th>            
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th> 
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th>            
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payer</th>  
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance Type</th>  
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Previous Unbilled($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Previous Billed($)</th>                  
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Unbilled($)</th>                  
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billed($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">W/O($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Adj($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Adj($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Adj($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Paid($)</th>
                <!--<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Bal($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Bal($)</th>-->
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">AR Bal($)</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Status</th>
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Sub Status</th>								
            </tr>
        </thead>              
			<tbody>
				@if(!empty($result))
				<?php
					$count = 1;
					$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
					$patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();            
					// Patient copay payment included 
					$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('patient');
				?>
				@foreach($result as $charge)                    
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
							$chargeDate = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$charge->created_at, '','-');
						?>
						<tr> 
							<td>
								{{ !empty($charge->claim_number)? $charge->claim_number: '-Nil-'}}					
							</td>
							
							<td>
								{{ !empty($charge->account_no)? $charge->account_no:'-Nil-'}}
							</td>
							<td>                        
								{{ str_limit($patient_name,25,'...') }}
							</td>
                             <td>
							{{ App\Http\Helpers\Helpers::timezone($charge->created_at, 'm/d/y') }}	
							</td>
							<td> 
								{{ $dos }}
							</td>   
							<td> 
								 {{!empty($charge->facility_short_name)? str_limit(@$charge->facility_short_name,15,' ...'):'-Nil-' }}
							</td>
							<td>
								{{!empty($charge->rendering_short_name)? str_limit(@$charge->rendering_short_name,15,' ...'):'-Nil-'}}
							</td>                    
							<td>
								 {{!empty($charge->billing_short_name)? str_limit(@$charge->billing_short_name,15,'..'):'-Nil-'}}
							</td>
							<td>{{!empty($insurance_name)? $insurance_name:'-Nil-' }}</td>
							<td>{{!empty($charge->insurance_type_name)? $charge->insurance_type_name:'-Nil-' }}</td>

							<?php
							
							if(isset($headers['Transaction Date'])){
								$date = $headers['Transaction Date']; 
								$date = explode('to',$date);
								$from = date("Y-m-d", strtotime($date[0]));
								if($from == '1970-01-01'){
									$from = '0000-00-00';
								}
								$to = date("Y-m-d", strtotime($date[1]));
							?>
							@if(App\Http\Helpers\Helpers::timezone($charge->created_at, 'Y-m-d') >= $from && App\Http\Helpers\Helpers::timezone($charge->created_at, 'Y-m-d') <= $to )
												
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
								<td style="text-align: right;<?php echo($unbilled)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($unbilled)? @$unbilled :'0.00' !!}</td>
								<td style="text-align: right;<?php echo($billed)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($billed)? @$billed:'0.00' !!}</td>
						   @else
								<td style="text-align: right;<?php echo($unbilled)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($unbilled)? @$unbilled :'0.00' !!}</td>
								<td style="text-align: right;<?php echo($billed)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($billed)? @$billed :'0.00' !!}</td>
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
							@endif
							<?php }else{ ?>
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
								<td style="text-align: right" data-format="#,##0.00">0.00</td>
								<td style="text-align: right;<?php echo($unbilled)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($unbilled)? @$unbilled :'0.00' !!}</td>
								<td style="text-align: right;<?php echo($billed)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($billed)? @$billed :'0.00' !!}</td>
							<?php } ?>

							<td style="text-align: right;<?php echo($charge->clamTotalWithheld)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($charge->clamTotalWithheld)? (@$charge->clamTotalWithheld):'0.00' !!}</td>
							<td style="text-align: right;<?php echo($charge->PatientAdj)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($charge->PatientAdj)? (@$charge->PatientAdj):'0.00' !!}</td>
							<td style="text-align: right;<?php echo($charge->InsuranceAdj)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($charge->InsuranceAdj)? (@$charge->InsuranceAdj):'0.00' !!}</td>
							<?php $claimTotalAdj = $charge->clamTotalWithheld + $charge->PatientAdj + $charge->InsuranceAdj; ?>
							<td style="text-align: right;<?php echo($claimTotalAdj)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($claimTotalAdj)? (@$claimTotalAdj):'0.00' !!}</td>
							<td style="text-align: right;<?php echo($charge->clamPaid)<0?'color:#ff0000':'' ?>" data-format="#,##0.00">{!! !empty($charge->clamPaid)? (@$charge->clamPaid):'0.00' !!}</td>
							<!--<td style="text-align: right" data-format="#,##0.00">{!! !empty($charge->patient_due)? (@$charge->patient_due):'0.00' !!}</td>
							<td style="text-align: right" data-format="#,##0.00">{!! !empty($charge->insurance_due)? (@$charge->insurance_due):'0.00' !!}</td>-->
							<td style="text-align: right" data-format="#,##0.00">{!! !empty($charge->balance_amt)? (@$charge->balance_amt):'0.00' !!}</td>
							<td>{{ !empty($charge->status)? $charge->status : '-Nil-'}}</td>
							<td>
								@if(isset($charge->sub_status_desc))
									{{ !empty($charge->sub_status_desc)? $charge->sub_status_desc:'-Nil-' }}
								@else 
									-Nil-
								@endif
							</td>
						</tr>
					@endif
				<?php $count++; ?>
				@endforeach
			@endif                  
			</tbody>
		</table>
<td colspan="17">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>