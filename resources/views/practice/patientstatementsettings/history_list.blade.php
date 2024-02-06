<div class="table-responsive">
	<table  id="example1" class="table table-bordered table-striped ">
		<thead>
			<tr>
			@if($pageType =='page')
				<th>Patient Name</th>	
				<th>Acc No</th>
			@endif
				<th>Statements</th>
				<th>Last Payment Date</th>
				<th>Last Payment Amt($)</th>
				<th>Statement Date</th>
				<th>Statement Balance($)</th>
				<th>Type</th> 
			@if($pageType =='page')
				<th>Created By</th>
			@endif
			</tr>
		</thead>
		<tbody>
		@if(count($statementlist)>0)
			<?php 
				$patDetArr = []; 
				$insurances = json_decode(json_encode($insurance_list), TRUE); 
			?>
			@foreach(@$statementlist as $patient_value)
			<?php // Patient last payment show include with wallet transaction.
				$patID = $patient_value->patient_id;
				if(!isset($patDetArr['last_pmt'][$patID])) {
					$pat_last_pmt = $patDetArr['last_pmt'][$patID] = App\Http\Helpers\Helpers::getPatientLastPaymentAmount($patient_value->patient_id, 'Patient');
				} else {
					$pat_last_pmt = $patDetArr['last_pmt'][$patID]; //App\Http\Helpers\Helpers::getPatientLastPaymentAmount($patient_value->patient_id, 'Patient');
				}
				
				$patPmtDate = isset($pat_last_pmt['created_at']) ? $pat_last_pmt['created_at'] : @$patient_value->latest_payment_date;
				$patPmtAmt = isset($pat_last_pmt['total_paid']) ? $pat_last_pmt['total_paid'] : $patient_value->latest_payment_amt;
				$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_value->patient_id,'encode');
				$patient_name = App\Http\Helpers\Helpers::getNameformat(@$patient_value->patient_detail->last_name,@$patient_value->patient_detail->first_name,@$patient_value->patient_detail->middle_name);
				
				if ($patient_value->patient_detail->is_self_pay == 'Yes') {
					$patientInsurance = "Self Pay";
				} else {					
					$patientInsurance = (isset($insurances['all'][$patID])) ? ($insurances['all'][$patID]) : '';		
				}										
			?>
			<tr style="cursor:default;">		
			@if($pageType =='page')
				<td><span><a href="{{ url('patients/'.$patient_id.'/ledger') }}" target="_blank"><span data-id="{{@$patient_value->patient_id}}" class="someelem" id="someelem{{@$patient_value->patient_id}}">
					@if(@$patient_value->patient_detail->title){{ @$patient_value->patient_detail->title }}. @endif{{ str_limit($patient_name,25,'...') }}
					</span> </a></span> 
				</td>
				<td>{{ @$patient_value->patient_detail->account_no }}</td>
			@endif
				<td>{{ @$patient_value->statements }}</td>
				<td>
					{!! (App\Http\Helpers\Helpers::dateFormat(@$patPmtDate) == '01/01/70') ? 'Nil' : App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patPmtDate, '', 'Nil') !!}
					<?php /* 
					{{ ($patient_value->latest_payment_date != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat(@$patient_value->latest_payment_date,'date') : '-' }}
					*/ ?>
				</td> 
				<td class="text-right">
					{!!  App\Http\Helpers\Helpers::priceFormat($patPmtAmt) !!}
				<?php /*
				{!! ($patient_value->latest_payment_amt != '0.00')? App\Http\Helpers\Helpers::priceFormat(@$patient_value->latest_payment_amt,'yes') : '-' !!}
				*/ ?>
				</td>                
				<td>{{ App\Http\Helpers\Helpers::timezone(@$patient_value->send_statement_date,'m/d/Y') }}</td>
				<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$patient_value->balance,'yes') !!}</td>	
				
				<td>{{ @$patient_value->type_for }}</td>
				@if($pageType =='page')
					<td>{{ @$patient_value->user_detail->short_name }}</td> 
				@endif
				
				<div class="on-hover-content js-tooltip_{{@$patient_value->patient_detail->id}}" style="display:none;">
					<span class="med-orange font600">@if(@$patient_value->patient_detail->title){{ @$patient_value->patient_detail->title }}. @endif{{ @$patient_name }}</span> 
					<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$patient_value->patient_detail->account_no }}
						<br>					
						@if(@$patient_value->patient_detail->dob !='' && @$patient_value->patient_detail->dob != "0000-00-00" && @$patient_value->patient_detail->dob != "1901-01-01" )
							<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_value->patient_detail->dob,'claimdate') }}
							<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$patient_value->patient_detail->dob) }}
						@endif
						<span class="font600">Gender :</span> {{ @$patient_value->patient_detail->gender }}<br>
						<span class="font600">Ins :</span> {{ $patientInsurance}}<br>
						<span class="font600">Address :</span> {{ @$patient_value->patient_detail->address1 }}<br>
						{{ @$patient_value->patient_detail->city}}, {{ @$patient_value->patient_detail->state}}, {{ @$patient_value->patient_detail->zip5}}-{{ @$patient_value->patient_detail->zip4}}<br>
						@if(@$patient_value->patient_detail->phone)<span class="font600">Home Phone :</span>{{@$patient_value->patient_detail->phone}} <br>@endif
						@if(@$patient_value->patient_detail->work_phone)<span class="font600">Work Phone :</span> {{@$patient_value->patient_detail->work_phone}}@endif
					</p>
				</div>	
			</tr>
		   @endforeach
	   	@endif
		</tbody>
	</table>
</div>