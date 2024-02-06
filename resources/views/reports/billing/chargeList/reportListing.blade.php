<?php $search_by = (array)$search_by;   ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
	@if(!empty((array)$result))

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>
    

	<div class="box-body no-padding"><!-- Box Body Starts -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Revenue Analysis Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!}: </span>{{ @$val }}                           
                          <?php $i++; ?>
                     @endforeach </div>                   
                </div>                
            </div>
        </div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="box box-info no-shadow no-border no-bottom">
				<div class="box-body margin-t-10">
					<div class="table-responsive mobile-lg-scroll">
						<table class="table table-bordered table-striped dataTable">
							<thead>
	                        <tr>
	                            <th>Claim No</th>
	                            <th>Acc No</th>            
	                            <th>Patient Name</th>
                                    <th>Charge Created Date</th>
	                            <th>DOS</th>
	                            <th>Facility</th> 
	                            <th>Rendering</th>
	                            <th>Billing</th>            
	                            <th>Payer</th> 
	                            <th>Insurance Type</th>                     
	                            <th>Previous Unbilled($)</th>
	                            <th>Previous Billed($)</th>
	                            <th>Unbilled($)</th>
	                            <th>Billed($)</th>
	                            <th>W/O($)</th>
	                            <th>Pat Adj($)</th>
	                            <th>Ins Adj($)</th>
	                            <th>Total Adj($)</th>
                                <th>Paid($)</th>
                                <!--<th>Pat Bal($)</th>
                                <th>Ins Bal($)</th>-->
                                <th>AR Bal($)</th>
	                            <th>Status</th>
	                            <th>Sub Status</th>								
	                        </tr>
	                    </thead>              
							<tbody>
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
											if(isset($search_by['Transaction Date'])){
												$date = $search_by['Transaction Date']; 
												$date = explode('to',$date);
												$from = date("Y-m-d", strtotime($date[0]));
												if($from == '1970-01-01'){
													$from = '0000-00-00';
												}
												$to = date("Y-m-d", strtotime($date[1]));
											?>
											
											@if(App\Http\Helpers\Helpers::timezone($charge->created_at, 'Y-m-d') >= $from && App\Http\Helpers\Helpers::timezone($charge->created_at, 'Y-m-d') <= $to )
												
												<td><span class="pull-right">0.00</span></td>
												<td><span class="pull-right">0.00</span></td>
												<td><span class="pull-right">{!! !empty($unbilled)? App\Http\Helpers\Helpers::priceFormat(@$unbilled):'0.00' !!}</span></td>
												<td><span class="pull-right">{!! !empty($billed)? App\Http\Helpers\Helpers::priceFormat(@$billed):'0.00' !!}</span></td>
							               @else
												<td><span class="pull-right">{!! !empty($unbilled)? App\Http\Helpers\Helpers::priceFormat(@$unbilled):'0.00' !!}</span></td>
												<td><span class="pull-right">{!! !empty($billed)? App\Http\Helpers\Helpers::priceFormat(@$billed):'0.00' !!}</span></td>
												<td><span class="pull-right">0.00</span></td>
												<td><span class="pull-right">0.00</span></td>
											@endif
											<?php }else{ ?>
												<td><span class="pull-right">0.00</span></td>
												<td><span class="pull-right">0.00</span></td>
												<td><span class="pull-right">{!! !empty($unbilled)? App\Http\Helpers\Helpers::priceFormat(@$unbilled):'0.00' !!}</span></td>
												<td><span class="pull-right">{!! !empty($billed)? App\Http\Helpers\Helpers::priceFormat(@$billed):'0.00' !!}</span></td>
											<?php } ?>
											<td><span class="pull-right">{!! !empty($charge->clamTotalWithheld)? App\Http\Helpers\Helpers::priceFormat(@$charge->clamTotalWithheld):'0.00' !!}</span></td>
											<td><span class="pull-right">{!! !empty($charge->PatientAdj)? App\Http\Helpers\Helpers::priceFormat(@$charge->PatientAdj):'0.00' !!}</span></td>
											<td><span class="pull-right">{!! !empty($charge->InsuranceAdj)? App\Http\Helpers\Helpers::priceFormat(@$charge->InsuranceAdj):'0.00' !!}</span></td>
											<?php $claimTotalAdj = $charge->clamTotalWithheld + $charge->PatientAdj + $charge->InsuranceAdj; ?>
											<td><span class="pull-right">{!! !empty($claimTotalAdj)? App\Http\Helpers\Helpers::priceFormat(@$claimTotalAdj):'0.00' !!}</span></td>
											<td><span class="pull-right">{!! !empty($charge->clamPaid)? App\Http\Helpers\Helpers::priceFormat(@$charge->clamPaid):'0.00' !!}</span></td>
											<!--<td><span class="pull-right">{!! !empty($charge->patient_due)? App\Http\Helpers\Helpers::priceFormat(@$charge->patient_due):'-Nil-' !!}</span></td>
											<td><span class="pull-right">{!! !empty($charge->insurance_due)? App\Http\Helpers\Helpers::priceFormat(@$charge->insurance_due):'-Nil-' !!}</span></td>-->
											<td><span class="pull-right">{!! !empty($charge->balance_amt)? App\Http\Helpers\Helpers::priceFormat(@$charge->balance_amt):'0.00' !!}</span></td>
											<td><span class="@if($charge->status == 'Ready')ready-to-submit @elseif($charge->status == 'Partial Paid') c-ppaid @else {{ $charge->status }} @endif">{{ !empty($charge->status)? $charge->status : '-Nil-'}}</span></td>
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
							</tbody>
						</table>
					</div><!-- /.box-body -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                    Showing {{@$pagination->from }} to {{@$pagination->to }} of {{@$pagination->total }} entries
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt  !!}</div>
            </div>
				</div><!-- /.box -->
			</div><!-- /.box -->
		</div>
	</div><!-- Box Body Ends --> 
	@else
	    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
	@endif                  
</div><!-- /.box Ends-->