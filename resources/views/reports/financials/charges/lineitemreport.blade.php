<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{ Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y  H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body bg-white border-radius-4"><!-- Box Body Starts -->
        @if($header !='' && !empty($header))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange" >
                <div class="margin-b-15">Charge Analysis - Detailed</div>
            </h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding text-center">
                    <?php $i = 1; ?>
                    @foreach($header as $header_name => $header_val)
                    <span class="med-green">
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}
                    </span> : {{str_replace('-','/', @$header_val)}}
                    @if($i<count((array)$header)) | @endif 
                    <?php $i++; ?>
                        @endforeach
				</div>
				<?php
					$date_cal = json_decode(json_encode($header), true);
					$trans = str_replace('-', '/', @$date_cal['Transaction Date']);
					$dos = str_replace('-', '/', @$date_cal['Date Of Service']);
				?>
			</div>
		</div>
		@endif
		@if(count($claims)>0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="box box-info no-shadow no-border no-bottom">
				<div class="box-body">
					<div class="table-responsive">
						<?php $count = 0;
						$total_amt_bal = 0;
						$count_cpt = 0;
						$claim_billed_total = 0;
						$claim_paid_total = 0;
						$claim_bal_total = $total_claim = $total_cpt = 0; ?>
						@foreach($claims as $claims_list)
						<?php
							$patient = $claims_list->patient;
							$set_title = (@$patient->title) ? @$patient->title . ". " : '';
							$patient_name = $set_title . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
						?>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
								<span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">Claim No: {{ $claims_list->claim_number }}</span>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
									{{ !empty($claims_list->patient->account_no)? $claims_list->patient->account_no : '-Nill-' }}
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
									{{ !empty($patient_name)? $patient_name : '-Nill-' }}
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing</label>
									{{ !empty($claims_list->billing_provider->short_name)? $claims_list->billing_provider->short_name : '-Nill-' }}
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="rendering" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
									{{ !empty($claims_list->rendering_provider->short_name)? $claims_list->rendering_provider->short_name : '-Nill-' }}
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
									{{ !empty($claims_list->facility_detail->short_name)? $claims_list->facility_detail->short_name : '-Nill-' }}
								</div>    
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
									@if($claims_list->self_pay=="Yes")
										Self
									@else
										{{ !empty($claims_list->insurance_details->short_name)? $claims_list->insurance_details->short_name :'-Nill-' }}
									@endif
								</div>   
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">User</label>
									{{ !empty($claims_list->user->short_name)? $claims_list->user->short_name : '-Nill-' }}
								</div>  
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Entry date</label>
									@if(@$claims_list->created_at != "0000-00-00" && $claims_list->created_at != "1970-01-01" && !empty($claims_list->created_at))
									<span class="bg-date">{{ App\Http\Helpers\Helpers::timezone($claims_list->created_at, 'm/d/y') }} </span>
									@else
									<span class="bg-date">{{ '-Nill-' }} </span>
									@endif
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="entrydate_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">POS</label>
									@if(!empty($claims_list->pos->code))
									<td> {{ @$claims_list->pos->code}} - {{@$claims_list->pos->pos }}</td>
									@else
									<td> {{ '-Nill-' }}</td>
									@endif
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Status</label>
									{{ !empty($claims_list->status)? $claims_list->status : '-Nill-' }}
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Insurance Type</label>		
									@if($claims_list->self_pay=="Yes")
										N/A
									@else
										@if(isset($claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name)) {{ @$claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name }}
										@else
											Nil
										@endif
									@endif
								</div>
								
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 table-responsive">
									<label for="user_lbl" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Reference</label>
									{{ !empty($claims_list->claim_reference)? $claims_list->claim_reference : '-Nill-' }}
								</div>
							</div> 
							
							@if(isset($claims_list->hold_option->option))
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="col-lg-12 col-md-12 col-sm-12">
									<label for="user_lbl" class=" med-orange font600">Hold Reason :</label>
									{{ !empty($claims_list->hold_option->option)? $claims_list->hold_option->option : '-Nill-' }}
									</div>
								</div>
							@endif
							
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<table class="popup-table-wo-border table table-responsive" style="margin-bottom: 5px; margin-top: 5px;">                    
									<thead>
										<!-- Claim Header -->
										<tr>
											<th style="background: #d9f3f0; color: #00877f;">DOS </th>
											<th style="background: #d9f3f0; color: #00877f;">CPT</th>
											@if(in_array('include_cpt_description',$include_cpt_option))
											<th style="background: #d9f3f0; color: #00877f;">CPT Description</th>
											@endif
											@if(in_array('include_modifiers',$include_cpt_option))
											<th style="background: #d9f3f0; color: #00877f;">Modifiers</th>
											@endif                                            
											@if(in_array('include_icd',$include_cpt_option))
											<th class="text-left" style="background: #d9f3f0; color: #00877f;" colspan="12">ICD-10</th>
											@endif
											<th class="text-left" style="background: #d9f3f0; color: #00877f;">Units</th>
											<th class="text-right" style="background: #d9f3f0; color: #00877f;">Charges($)</th>
											<th class="text-right" style="background: #d9f3f0; color: #00877f;">Paid($)</th>
											<!--<th class="text-right" style="background: #d9f3f0; color: #00877f;">Total Bal($)</th>-->
										</tr>
									</thead>                
									<tbody> 
										<!-- Claim Row -->
										@foreach($claims_list->cpttransactiondetails as $cpt_details)    
										<?php $icd_values = App\Models\Icd::getIcdValues(@$cpt_details->cpt_icd_map_key); ?>
										<tr>                              
											<td>{{ !empty($cpt_details->dos_from)? App\Http\Helpers\Helpers::dateFormat($cpt_details->dos_from,'dob') : '-Nill-' }}</td>     
											<td>{{ !empty($cpt_details->cpt_code)? $cpt_details->cpt_code : '-Nill-' }}</td> 
											@if(in_array('include_cpt_description',$include_cpt_option))
											<td>{{ !empty($cpt_details->cpt_code)? App\Models\Medcubics\Cpt::Cptshortdescription(@$cpt_details->cpt_code) : '-Nill-' }}</td>
											@endif
											@if(in_array('include_modifiers',$include_cpt_option))
											<?php
												$modifier_arr = array();
												if ($cpt_details->modifier1 != '')
													array_push($modifier_arr, $cpt_details->modifier1);
												if ($cpt_details->modifier2 != '')
													array_push($modifier_arr, $cpt_details->modifier2);
												if ($cpt_details->modifier3 != '')
													array_push($modifier_arr, $cpt_details->modifier3);
												if ($cpt_details->modifier4 != '')
													array_push($modifier_arr, $cpt_details->modifier4);
												if (count($modifier_arr) > 0) {
													$modifier_val = implode($modifier_arr, ',');
												} else {
													$modifier_val = '-Nil-';
												}
											?>
											<td>{{ !empty($modifier_val)? $modifier_val : '-Nill-' }}</td>
											@endif
											<?php $exp = explode(',', $cpt_details->cpt_icd_code); ?>

											@if(in_array('include_icd',$include_cpt_option))
												@for($i=0; $i<12;$i++)                                               
													<td> {{ !empty($exp[$i])? $exp[$i] : '-Nill-' }}</td>  
												@endfor 
											@endif
											<td class="text-left">{!! !empty($cpt_details->unit)? $cpt_details->unit : '-Nill-' !!}</td>
											<td class="text-right">{!! !empty($cpt_details->charge)? App\Http\Helpers\Helpers::priceFormat(@$cpt_details->charge) : '-Nill-' !!}
											<?php $claim_billed_total += @$cpt_details->charge; ?></td>
											<?php $text = App\Http\Helpers\Helpers::priceFormat(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid); ?>
											<td class="text-right">{!! !empty($text)? $text : '-Nill-' !!}
											</td>                                           
											<?php
												$claim_paid_total += @$cpt_details->claim_cpt_fin_details->patient_paid + @$cpt_details->claim_cpt_fin_details->insurance_paid;
												$bal = @$cpt_details->charge - (@$cpt_details->claim_cpt_fin_details->patient_paid + @$cpt_details->claim_cpt_fin_details->insurance_paid + @$cpt_details->claim_cpt_fin_details->insurance_adjusted + @$cpt_details->claim_cpt_fin_details->patient_adjusted + @$cpt_details->claim_cpt_fin_details->with_held);
												$total_amt_bal += @$bal;
												$claim_bal_total += @$bal;
												$count_cpt += 1;
											?>                                                        
											<!--<td class="text-right">{!! !empty($bal)? App\Http\Helpers\Helpers::priceFormat($bal) : '-Nill-' !!}</td>-->
										</tr>
										@endforeach
										<!-- Claim Total Row -->
										<tr>                              
											<td class="text-right"></td>     
											<td class="text-right"></td> 
											@if(in_array('include_cpt_description',$include_cpt_option))
											<td></td>											
											@endif
											@if(in_array('include_modifiers',$include_cpt_option))
											<td class="text-right"></td>
											@endif
											@if(in_array('include_icd',$include_cpt_option))
											<td colspan="12"></td>
											@endif
											<td style="background: #f5fffe;border-radius: 20px 0px 0px 20px" class="text-right"><label for="total" class="med-green font600 no-bottom">Total</label></td>
											<td style="background: #f5fffe" class="text-right">{!! !empty($claim_billed_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_billed_total) : '-Nill-' !!}
											</td>
											<?php $claim_billed_total = 0; ?>
											<td style="background: #f5fffe" class="text-right">{!! !empty($claim_paid_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_paid_total) : '-Nill-' !!}
											</td>							
											<?php $claim_paid_total = 0; ?>
											<!-- <td style="background: #f5fffe" class="text-right">{!! !empty($claim_bal_total)? App\Http\Helpers\Helpers::priceFormat(@$claim_bal_total) : '-Nill-' !!}
											</td>-->
											<?php $claim_bal_total = 0; ?>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<?php $count++; ?>
						@endforeach

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-10 no-padding dataTables_info">
								Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
							</div>
							<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding margin-t-m-10">{!! $pagination->pagination_prt !!}</div>
						</div>
						<?php // @if(@$pagination->last_page != 1)--> ?>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg hide">
							<label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">Charge Count : <span class="med-orange">{{ @count($sinpage_claim_arr) }}</span></label>
							<label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">Charge Value : <span class="med-orange">${{@$sinpage_charge_amount}}</span></label>
							<label for="name" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 font600">No. of CPT Billed : <span class="med-orange">{{@$sinpage_total_cpt}}</span></label>
						</div>
						<?php // @endif ?>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
								<div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
									<i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>
								</div><!-- /.box-header -->                          
								
							   <div class="col-lg-12 no-padding">
									<div class="col-lg-3 ">
										<div class="col-lg-12 reports-sections p-b-10 padding-t-15">
											<h2 class="margin-b-1 med-darkgray dashboard-number  margin-t-5 p-l-10">{{$tot_summary->total_patient}}</h2>
											<h3 class="p-l-10">Total Patients</h3>
											<h4 class="p-l-10">Value : ${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}} </h4>
										</div>
									</div>
							
									<div class="col-lg-3 ">
										<div class="col-lg-12 reports-sections p-b-10 padding-t-15">
											<h2 class="margin-b-1 med-darkgray dashboard-number  margin-t-5 p-l-10">{{$tot_summary->total_cpt}}</h2>
											<h3 class="p-l-10">Total CPT</h3>
											<h4 class="p-l-10">Value : ${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}} </h4>
										</div>
									</div>
									
									<div class="col-lg-3 ">
										<div class="col-lg-12 reports-sections p-b-10 padding-t-15">
											<h2 class="margin-b-1 med-darkgray dashboard-number  margin-t-5 p-l-10">{{$tot_summary->total_unit}}</h2>
											<h3 class="p-l-10">Total Units</h3>
											<h4 class="p-l-10">Value : ${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}} </h4>
										</div>
									</div>
						
									<div class="col-lg-3 ">
										<div class="col-lg-12 reports-sections p-b-10 padding-t-15">
											<h2 class="margin-b-1 med-darkgray dashboard-number  margin-t-5 p-l-10">{{ @$tot_summary->total_claim }}</h2>
											<h3 class="p-l-10">Total Charges</h3>
											<h4 class="p-l-10">Value : ${{App\Http\Helpers\Helpers::priceFormat($tot_summary->total_charge)}} </h4>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- /.box -->
		</div>

		@else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
		@endif
	</div><!-- Box Body Ends --> 

</div><!-- /.box Ends-->
<link href="https://fonts.googleapis.com/css?family=Fjalla+One&display=swap" rel="stylesheet">