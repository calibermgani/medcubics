<div class="tab-content js-claim-dyanamic-tab">
    <div class="active tab-pane" id="claim-tab-info_main0">
        <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-pat-btns margin-t-15">
    		<a href="javascript:void(0);" class="js-claim-view-tab form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.charges.review')}}"></i> Review</a>
            <a id="claim_notes_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> Notes</a>
            <a id="claim_assign_all_link" class="form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
        </div>
        <div class="no-border no-shadow">
            <div class="box-body table-responsive">
                <table class="claims table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:2%; margin: -1px 0px 1px; padding: 0px; text-align: center; vertical-align: middle;">
    							{!! Form::checkbox("selectall",null,null,["class" => "no-margin",'id'=>'js-select-all']) !!}
    						</th>
                            <th>DOS</th>
                            <th>Claim No</th>                                                        
                            <th>Patient</th>                                                        
                            <th>Provider</th>
                            <th>Facility</th>
                            <th>Billed To</th>
                            <th>Charge Amt</th>
                            <th>Paid</th>                        
                            <th>AR Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>               
                    <tbody>
                       <?php 
						$count = 1;
                        $insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
                        $patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();                         
                        $payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('payment'); 
                        ?>
    				    @foreach($claims_lists as $claim) 
							@if(in_array($claim->id,$myfollowup_list))
								<?php 
									$facility = $claim->facility_detail;
									$provider = $claim->rendering_provider;
									$status_display_class = ($claim->status == "Paid")?"js-listclaim ar-hide-class":"";
									$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id, 'encode');
									$url = url('patients/payment/popuppayment/'.$claim_id);
									$insurance_name = "";
									if(empty($claim->insurance_details) || $claim->insurance_details->id == '' || $claim->insurance_details->id == 0){
										$insurance_name = "Self";
									} else {      
										$insurance_name = !empty($insurances[$claim->insurance_details->id]) ? $insurances[$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
									}

									$insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;	
									$detArr = ['patient_id'=> @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
	        						$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);
								?>
								<tr class = "">
									<td>
										{!! Form::checkbox('claim_ids[]', @$claim->claim_number, null, ['class'=>"chk flat-red js_claim_ids js-select-all-sub-checkbox",'data-id'=>""]) !!}
									</td>
									<td> 
										<a href="#js-model-popup-payment" claim_number = "{{$claim->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$url}}" class="claimbilling">{{date('m/d/Y',strtotime($claim->date_of_service))}}</a>
									</td> 
									<td>
										<a href="{{ $edit_link }}">{{@$claim->claim_number}}</a>
									</td>  
									<td>
										<?php 
											$patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name);
                                            $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim->patient->id); 
                                        ?>
										<a id="someelem{{@$claim->id.$count}}" class="someelem" data-id="{{@$claim->id.$count}}" href="{{ url('patients/'.$patient_id.'/ledger') }}"> {{ $patient_name }}</a>

										<span class="on-hover-content js-tooltip_{{@$claim->id.$count}}" style="display:none;">
											<span class="med-orange font600">{{ $patient_name }}</span> 
											<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$claim->patient->account_no }}
												<br>
												@if(@$claim->patient->dob != "0000-00-00" && @$claim->patient->dob != "" && @$claim->patient->dob != "1901-01-01")<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->patient->dob,'claimdate') }}
												<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$claim->patient->dob) }} @endif
												<span class="font600">Gender :</span> {{ @$claim->patient->gender }}<br>
												<span class="font600">Ins :</span> 
												<?php                                            
													$patient_insurance_name = '';
													if(isset($patient_insurances['primary'][@$claim->patient->id]))
														$patient_insurance_name = $patient_insurances['primary'][@$claim->patient->id];
													elseif(isset($patient_insurances['secondary'][@$claim->patient->id]))
														$patient_insurance_name = $patient_insurances['secondary'][@$claim->patient->id];
													elseif(isset($patient_insurances['others'][@$claim->patient->id]))
														$patient_insurance_name = $patient_insurances['others'][@$claim->patient->id];
													// $patient_insurance_name = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$claim->patient->id);   
												?>    
												{{ $patient_insurance_name }}<br>
												<span class="font600">Address :</span> {{ @$claim->patient->address1 }}<br>
												 {{ @$claim->patient->city}}, {{ @$claim->patient->state}}, {{ @$claim->patient->zip5}}-{{ @$claim->patient->zip4}}<br>
												@if(@$claim->patient->phone)<span class="font600">Home Phone :</span>{{@$claim->patient->phone}} <br>@endif
												@if(@$claim->patient->work_phone)<span class="font600">Work Phone :</span> {{@$claim->patient->work_phone}}@endif
											</p>
										</span>                                 
									</td>
									<td>
										<a id="someelem{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" href="javascript:void(0);">{{str_limit(@$claim->rendering_provider->short_name,15,' ...')}}</a>
										<?php @$provider->id = 'p_'.@$claim->rendering_provider->id.$count; ?> 
										@include ('layouts/provider_hover')
									</td>
									<td> 
										<a id="someelem{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claim->facility_detail->short_name,15,' ...')}}</a>
										<?php @$facility->id = 'f_'.@$claim->facility_detail->id.$count; ?> 
										@include ('layouts/facility_hover')
									</td> 
									<td>
										{{ $insurance_name }}
									</td>
									<td class="text-right">{{@$claim->total_charge}}</td>
									<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->total_paid)!!}</td>
									<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</td>
									<td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status}} @endif">{{ @$claim->status}}</span></td>
								</tr>
								<?php $count++;   ?> 
								@endif
                        @endforeach              
                    </tbody>
                </table>

            </div><!-- /.box-body -->
        </div><!-- /.box -->
	</div>	
</div>
</div>
<style>
.ar-hide-class {
    display: none;
}
</style>