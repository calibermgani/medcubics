<div class="box box-view no-shadow"><!--  Box Starts -->
<?php
 $s= explode( '_', $aging_report_list->aging_by );
 $aging_by= ucfirst(implode( ' ', $s ));
 $claim= explode( '_', @$aging_report_list->claim_by );
 $claim_by= ucfirst(implode( ' ', $claim ));
 $days= explode( '_', @$aging_report_list->aging_days );
 $aging_days= ucfirst(implode( ' ', $days ));
 
 ?>
    <div class="box-header-view">
        <i class="livicon" data-name="info"></i> <h3 class="box-title">{{'Aging By :'}}{{ $aging_by}} {{'Aging Days :'}}{{ $aging_days}} {{'Claim By :'}}{{ $claim_by}} </h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>

<div class="box-body no-padding"><!-- Box Body Starts -->

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-info no-shadow no-border no-bottom">
            <div class="box-body margin-t-10">
                <div class="table-responsive">
				<!-- Aging list rendering_provider start -->
				
				@if(@$aging_report_list->rendering_provider_id == 'all' && @$aging_report_list->aging_by == 'rendering_provider') 
				@php  
					 $provider = [];
					 $i = 1;$emp='';
				@endphp
					
				@foreach($aging_report_list->unbilled as $keys=>$unbilleds)
				 {{App\Models\Provider::getProviderNamewithDegree(@$aging_report_list->render_provider[$i])}}
					@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
					<table class="table table-bordered table-striped dataTable">	
					
					@if(!in_array($aging_report_list->rendering_provider_id, $provider))
					<thead>
						<tr>
							@foreach($header as $header_name => $header_val)

							<th>{{ @ $header_val }}</th>
							@endforeach
						</tr>
					</thead> 
					<tbody>
					@foreach($unbilleds as $billed)
					@foreach($billed as $keys=>$unbilled)
					@foreach($unbilled as $unbilled)
						<tr>
							@php   $emp = 1;
								$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
							<td>{{@$unbilled->patient->account_no}}</td>
							<td>{{@$patient_name}}</td>
							<td>{{@$unbilled->claim_number}}</td>
							<td>{{@$unbilled->date_of_service}}</td>
							<td>{{!empty($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient'}}</td>
							
							<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
							<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
									@php  $_0to30 += @$unbilled->balance_amt; @endphp
								 @else 0.00 @endif</td>
							<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
									@php  $_31to60 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
									@php  $_61to90 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
									@php  $_91to120 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
								@php  $_121to150 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
								@php  $_150_above += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>{{@$unbilled->patient_due}}
								@php  $patient_due += @$unbilled->patient_due; @endphp</td>
							<td>{{@$unbilled->insurance_due}}
								@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
							<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
								@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
								</td>
						</tr>	   
					</tbody>
						@php  $provider[] = @$unbilled->rendering_provider_id;	@endphp 
					@endforeach
					@endforeach
					@endforeach
						@if(@$emp == 1 )
							<tr>
								<td>{{ 'Total' }}</td>
								<td> </td><td> </td><td> </td><td> </td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
							</tr>
							@else
							<tr><td class="col-span6">No Records Found</td></tr>
							@endif
					@endif
					</table>
					@php  @$i++;@endphp
				@endforeach 
				<!-- Aging list rendering_provider End -->
				
				<!-- Aging list billing provider End -->	
				@elseif(@$aging_report_list->billing_provider_id == 'all' &&@$aging_report_list->aging_by == 'billing_provider')
					@php  
						$provider = [];
						$i = 1;$emp='';
					@endphp
				@foreach($aging_report_list->unbilled as $keys=>$unbilleds)
					{{App\Models\Provider::getProviderNamewithDegree(@$aging_report_list->billing_provider[$i])}}
					@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
					<table class="table table-bordered table-striped dataTable">	
						@if(!in_array($aging_report_list->rendering_provider_id, $provider))
						<thead>
							<tr>
								@foreach($header as $header_name => $header_val)

								<th>{{ @ $header_val }}</th>
								@endforeach
							</tr>
						</thead> 
					 <tbody>
					@foreach($unbilleds as $billed)
					@foreach($billed as $keys=>$unbilled)
					@foreach($unbilled as $unbilled)
						<tr>
							@php   $emp = 1;
							$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
							<td>{{@$unbilled->patient->account_no}}</td>
							<td>{{@$patient_name}}</td>
							<td>{{@$unbilled->claim_number}}</td>
							<td>{{@$unbilled->date_of_service}}</td>
							<td>{{!empty($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient'}}</td>
							<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
							<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
									@php  $_0to30 += @$unbilled->balance_amt; @endphp
								 @else 0.00 @endif</td>
							<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
									@php  $_31to60 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
									@php  $_61to90 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
									@php  $_91to120 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
								@php  $_121to150 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
								@php  $_150_above += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>{{@$unbilled->patient_due}}
								@php  $patient_due += @$unbilled->patient_due; @endphp</td>
							<td>{{@$unbilled->insurance_due}}
								@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
							<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
								@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
							</td>
						</tr>	 
						@php  $provider[] = @$unbilled->rendering_provider_id;	@endphp 
					@endforeach
					@endforeach
					@endforeach
						@if(@$emp == 1 )
							<tr>
								<td>{{ 'Total' }}</td>
								<td> </td><td> </td><td> </td><td> </td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
							</tr>
							@else
							<tr><td class="col-span6">No Records Found</td></tr>
							@endif
						@endif
					</table>
					@php  @$i++;@endphp
				@endforeach 
				
		<!--Aging by billing provider-->	
		<!--Aging by patient -->	
		
				@elseif(@$aging_report_list->aging_by == 'patient')
					@php  
						$provider = [];
						$i = 0;$emp='';
					@endphp
				@foreach($aging_report_list->unbilled as $keys=>$unbilleds)
				 {{App\Models\Patients\Patient::getPatientname(@$aging_report_list->patient_id[$i])}}
						@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
						<table class="table table-bordered table-striped dataTable">	
						@if(!in_array($aging_report_list->rendering_provider_id, $provider))
						<thead>
							<tr>
								@foreach($header as $header_name => $header_val)
								<th>{{ @ $header_val }}</th>
								@endforeach
							</tr>
						</thead> 
					 <tbody>
					@foreach($unbilleds as $billed)
					@foreach($billed as $keys=>$unbilled)
					@foreach($unbilled as $unbilled)
						<tr>
							@php  $emp = 1;
								$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
							<td>{{@$unbilled->patient->account_no}}</td>
							<td>{{@$patient_name}}</td>
							<td>{{@$unbilled->claim_number}}</td>
							<td>{{@$unbilled->date_of_service}}</td>
							<td>{{!empty($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient'}}</td>
							<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
							<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
									@php  $_0to30 += @$unbilled->balance_amt; @endphp
								 @else 0.00 @endif</td>
							<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
									@php  $_31to60 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
									@php  $_61to90 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
									@php  $_91to120 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
								@php  $_121to150 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
								@php  $_150_above += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>{{@$unbilled->patient_due}}
								@php  $patient_due += @$unbilled->patient_due; @endphp</td>
							<td>{{@$unbilled->insurance_due}}
								@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
							<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
								@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
							</td>
						</tr>	 
						@php  $provider[] = @$unbilled->rendering_provider_id;	@endphp 
					@endforeach
					@endforeach
					@endforeach
						@if(@$emp == 1 )
							<tr>
								<td>{{ 'Total' }}</td>
								<td> </td><td> </td><td> </td><td> </td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
							</tr>
							@else
							<tr><td class="col-span6">No Records Found</td></tr>
							@endif
						</table>	
					@endif
						@php  @$i++;@endphp
				@endforeach 
				<!-- Aging by facility all-->
				@elseif(@$aging_report_list->aging_by == 'facility' && @$aging_report_list->facility_id[0] == "all" )
					@php  
						$provider = [];
						$i = 1;$emp='';
					@endphp
					
				@foreach($aging_report_list->unbilled as $keys=>$unbilleds)
				  {{App\Models\Facility::getFacilityName(@$aging_report_list->facility_id[$i])}}		
						@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
						<table class="table table-bordered table-striped dataTable">	
						
						@if(!in_array($aging_report_list->rendering_provider_id, $provider))
						<thead>
							<tr>
								@foreach($header as $header_name => $header_val)

								<th>{{ @ $header_val }}</th>
								@endforeach
							</tr>
						</thead> 
					 <tbody>
					@foreach($unbilleds as $billed)
					@foreach($billed as $keys=>$unbilled)
					@foreach($unbilled as $unbilled)
						<tr>
							@php  $emp = 1;
								$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
							<td>{{@$unbilled->patient->account_no}}</td>
							<td>{{@$patient_name}}</td>
							<td>{{@$unbilled->claim_number}}</td>
							<td>{{@$unbilled->date_of_service}}</td>
							<td>{{!empty($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient'}}</td>
							<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
							<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
									@php  $_0to30 += @$unbilled->balance_amt; @endphp
								 @else 0.00 @endif</td>
							<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
									@php  $_31to60 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
									@php  $_61to90 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
									@php  $_91to120 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
								@php  $_121to150 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
								@php  $_150_above += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>{{@$unbilled->patient_due}}
								@php  $patient_due += @$unbilled->patient_due; @endphp</td>
							<td>{{@$unbilled->insurance_due}}
								@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
							<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
								@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
							</td>
						</tr>	 
						@php  $provider[] = @$unbilled->rendering_provider_id;	@endphp 
					@endforeach
					@endforeach
					@endforeach					
						<tr>
							<td>{{ 'Total' }}</td>
							<td> </td><td> </td><td> </td><td> </td>
							<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
							<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
							<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
							<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
							<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
							<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
							<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
							<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
							<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
							<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
						</tr>
					@endif
					</table>
					@php  @$i++;@endphp
				@endforeach 
		<!--Aging by insurance -->		
				@elseif(@$aging_report_list->aging_by == 'insurance' && @$aging_report_list->insurance_id == 'all' )
					@php  
						$provider = [];
						$i = 0; $emp='';
						$ins = @$aging_report_list->ins;
					@endphp
				@foreach($aging_report_list->unbilled as $keys=>$unbilleds)
				
				  {{App\Models\Insurance::getInsuranceName(@$ins[$i])}}		
						@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
						<table class="table table-bordered table-striped dataTable">	
							@if(!in_array($aging_report_list->rendering_provider_id, $provider))
							<thead>
								<tr>
									@foreach($header as $header_name => $header_val)
									<th>{{ @ $header_val }}</th>
									@endforeach
								</tr>
							</thead> 
						 <tbody>
					@foreach($unbilleds as $billed)
					@foreach($billed as $keys=>$unbilled)
					@foreach($unbilled as $unbilled)
						<tr>
							@php  $emp = 1;
								$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
							<td>{{@$unbilled->patient->account_no}}</td>
							<td>{{@$patient_name}}</td>
							<td>{{@$unbilled->claim_number}}</td>
							<td>{{@$unbilled->date_of_service}}</td>
							<td>{{!empty($unbilled->insurance_details)? $unbilled->insurance_details->insurance_name: 'Patient'}}</td>
							<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
							<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
									@php  $_0to30 += @$unbilled->balance_amt; @endphp
								 @else 0.00 @endif</td>
							<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
									@php  $_31to60 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
									@php  $_61to90 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
									@php  $_91to120 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
								@php  $_121to150 += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
								@php  $_150_above += @$unbilled->balance_amt; @endphp
								@else 0.00 @endif</td>
							<td>{{@$unbilled->patient_due}}
								@php  $patient_due += @$unbilled->patient_due; @endphp</td>
							<td>{{@$unbilled->insurance_due}}
								@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
							<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
								@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
							</td>
						</tr>	 
						@php  $provider[] = @$unbilled->rendering_provider_id;	@endphp 
					@endforeach
					@endforeach
					@endforeach					
						@if($emp ==1 )
							<tr>
								<td>{{ 'Total' }}</td>
								<td> </td><td> </td><td> </td><td> </td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
								<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
								<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
							</tr>
						@else
							<tr><td class="col-span-6">No Records Found</td></tr>
						@endif
					@endif
					</table>
					@php  @$i++;@endphp
				@endforeach 
				
		<!--Aging by other type-->		
			@else  
				@php  
					 $i = 0;
					 $emp='';
				@endphp
				@php $billed_amt = $_0to30 = $_31to60 = $_61to90 = $_91to120 = $_121to150 = $_150_above = $insurance_due = $patient_due =$total_due = 0;@endphp	
				@if($aging_report_list->rendering_provider_id != 'all')
					{{App\Models\Provider::getProviderNamewithDegree(@$aging_report_list->rendering_provider_id)}}
				@elseif($aging_report_list->billing_provider_id != 'all')
					{{App\Models\Provider::getProviderNamewithDegree(@$aging_report_list->billing_provider_id)}}
				@elseif($aging_report_list->facility_id != 'all')
					{{App\Models\Facility::getFacilityName(@$aging_report_list->facility_id[0])}}
				@elseif($aging_report_list->insurance_id != 'all')
					 {{App\Models\Insurance::getInsuranceName(@$aging_report_list->insurance_id)}}
				@endif
					
				<table class="table table-bordered table-striped dataTable">
					<thead>
						<tr>
							@foreach($header as $header_name => $header_val)
							<th>{{ @ $header_val }}</th>
							@endforeach
						</tr>
					</thead> 
					<tbody>
						@foreach($aging_report_list->unbilled->unbilled as $keys=>$billed)
							@foreach($billed as $unbilled)
								@php  $emp = 1;
									$patient_name = App\Http\Helpers\Helpers::getNameformat(@$unbilled->patient->last_name,@$unbilled->patient->first_name,@$unbilled->patient->middle_name); @endphp
								<tr>
									<td>{{@$unbilled->patient->account_no}}</td>
									<td>{{@$patient_name}}</td>
									<td>{{@$unbilled->claim_number}}</td>
									<td>{{@$unbilled->date_of_service}}</td>
									<td>{{!empty(@$unbilled->insurance_details)? @$unbilled->insurance_details->insurance_name: 'Patient'}}</td>
									<td>{{@$unbilled->total_charge}} @php  $billed_amt += @$unbilled->total_charge; @endphp</td>
									<td>@if(@$keys == "0-30"){{@$unbilled->balance_amt}}
											@php  $_0to30 += @$unbilled->balance_amt; @endphp
										 @else 0.00 @endif</td>
									<td>@if(@$keys == "31-60"){{@$unbilled->balance_amt}}
											@php  $_31to60 += @$unbilled->balance_amt; @endphp
										@else 0.00 @endif</td>
									<td>@if(@$keys == "61-90"){{@$unbilled->balance_amt}}
											@php  $_61to90 += @$unbilled->balance_amt; @endphp
										@else 0.00 @endif</td>
									<td>@if(@$keys == "91-120"){{@$unbilled->balance_amt}}
											@php  $_91to120 += @$unbilled->balance_amt; @endphp
										@else 0.00 @endif</td>
									<td>@if(@$keys == "121-150"){{@$unbilled->balance_amt}}
										@php  $_121to150 += @$unbilled->balance_amt; @endphp
										@else 0.00 @endif</td>
									<td>@if(@$keys == "151-above"){{@$unbilled->balance_amt}}
										@php  $_150_above += @$unbilled->balance_amt; @endphp
										@else 0.00 @endif</td>
									<td>{{@$unbilled->patient_due}}
										@php  $patient_due += @$unbilled->patient_due; @endphp</td>
									<td>{{@$unbilled->insurance_due}}
										@php  $insurance_due += @$unbilled->insurance_due; @endphp</td>
									<td>{{App\Http\Helpers\Helpers::priceFormat(@$unbilled->patient_due + @$unbilled->insurance_due)}}
										@php  $total_due += @$unbilled->patient_due + @$unbilled->insurance_due; @endphp
									</td>
								</tr>
							
							@endforeach  
								
							@endforeach
								@if(@$emp == 1 )
								<tr>
									<td>{{ 'Total' }}</td>
									<td> </td><td> </td><td> </td><td> </td>
									<td> {{App\Http\Helpers\Helpers::priceFormat(@$billed_amt)}}</td>
									<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_0to30) }}</td>
									<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_31to60) }}</td>
									<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_61to90) }}</td>
									<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_91to120) }}</td>
									<td> {{ App\Http\Helpers\Helpers::priceFormat(@$_121to150) }}</td>
									<td> {{App\Http\Helpers\Helpers::priceFormat(@$_150_above) }}</td>
									<td> {{App\Http\Helpers\Helpers::priceFormat(@$patient_due) }}</td>
									<td> {{App\Http\Helpers\Helpers::priceFormat(@$insurance_due) }}</td>
									<td> {{App\Http\Helpers\Helpers::priceFormat(@$total_due)}}</td>
								</tr>
								@else
								<tr><td class="col-span6">No Records Found</td></tr>
								@endif
							</tbody>	
						</table>
			@endif