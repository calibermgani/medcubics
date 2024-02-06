<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<table class="table table-striped table-bordered space" id="patientstatement">
			<thead>
				<tr>
					<th>Patient Name</th>
					<th>Acc No</th>
					<th>Statements</th>
					<th>Last Payment Date</th>
					<th>Last Payment Amt($)</th>
					<th>Pat Balance($)</th> 
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				@foreach($patients_arr as $patient_value)
				<?php // Patient last payment show include with wallet transaction.
					$pat_last_pmt = App\Http\Helpers\Helpers::getPatientLastPaymentAmount($patient_value->id, 'Patient');
					$patPmtDate = isset($pat_last_pmt['created_at']) ? $pat_last_pmt['created_at'] : @$patient_balance[$patient_value->id]['lastpaymentdate'];
					$patPmtAmt = isset($pat_last_pmt['total_paid']) ? $pat_last_pmt['total_paid'] : @$patient_balance[$patient_value->id]['lastpayment'];
					$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_value->id,'encode');
					$patient_name = App\Http\Helpers\Helpers::getNameformat("$patient_value->last_name","$patient_value->first_name","$patient_value->middle_name");
				?>
				
				<tr style="cursor:default">
					<td style="cursor:pointer">
						<span><a href="{{ url('patients/'.$patient_id.'/ledger') }}" target="_blank"><span data-id="{{@$patient_value->id}}" class="someelem" id="someelem{{@$patient_value->id}}">@if(@$patient_value->title){{ @$patient_value->title }}. @endif{{ str_limit($patient_name,25,'...') }}</span> </a></span> 
					</td>
					<td>{{ @$patient_value->account_no }}</td>
					<td>{{ @$patient_value->statements_sent }}</td>
					<td>
						@if(trim($patPmtDate) == '')
							-Nil-
						@else
							{!! App\Http\Helpers\Helpers::dateFormat(@$patPmtDate) !!}
						@endif
					</td>
					<td class= "text-right">{!! App\Http\Helpers\Helpers::priceFormat($patPmtAmt) !!}</td> 
					<td class= "text-right">{!! App\Http\Helpers\Helpers::priceFormat($patient_balance[$patient_value->id]['balance']) !!}</td>	
					<td>
						<div class="js_loading{{ $patient_value->id }} hide">
							<i class="fa fa-spinner fa-spin font20 med-green"></i> Processing
						</div>
						
						{!! Form::button('<i class="fa fa-picture-o med-green"></i>', ['data-placement'=>"bottom",  	'data-toggle'=>'tooltip','data-original-title'=>"Preview",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-name'=>'preview','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!}
						
						{!! Form::button('<i class="fa fa-download med-green send-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip','data-original-title'=>"Send PDF Statement",'data-module'=>"individual",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-name'=>'sendstatement','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!}
						
						{!! Form::button('<i class="fa fa-file-excel-o med-green send-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip','data-original-title'=>"Send CSV Statement",'data-module'=>"individual",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-name'=>'sendcsvstatement','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!} 
						
						{!! Form::button('<i class="fa fa-file-code-o med-green send-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip','data-original-title'=>"Send XML Statement",'data-module'=>"individual",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-name'=>'sendxmlstatement','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!} 
						
						@if($patient_value->email != '') 
							{!! Form::button('<i class="fa fa-envelope-o med-green email-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip', 'data-module'=>"individual", 'data-original-title'=>"Email Statement",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id, 'data-name'=>'emailstatement', 'data-unique'=>$patient_value->id, 'data-id'=>$patient_id]) !!} 
						@endif
					</td>
					<div class="on-hover-content js-tooltip_{{$patient_value->id}}" style="display:none;">
						<span class="med-orange font600">@if($patient_value->title){{ @$patient_value->title }}. @endif{{ $patient_name }}</span> 
						<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$patient_value->account_no }} <br>
							@if(@$patient_value->dob != "0000-00-00"  && @$patient_value->dob != "" && @$patient_value->dob != "1901-01-01")
								<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_value->dob,'claimdate') }}
								<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$patient_value->dob) }} 
							@endif
							<span class="font600">Gender :</span> {{ $patient_value->gender }}<br>
							<span class="font600">Ins :</span> {{ App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$patient_value->id)}} <br>
							<span class="font600">Address :</span> {{ $patient_value->address1 }}<br>
							{{ $patient_value->city}}, {{ $patient_value->state}}, {{ $patient_value->zip5}}-{{ $patient_value->zip4}}<br>
							@if(@$patient_value->phone)<span class="font600">Home Phone :</span>{{$patient_value->phone}} <br>@endif
							@if(@$patient_value->work_phone)<span class="font600">Work Phone :</span> {{$patient_value->work_phone}}@endif
						</p>
					</div>
				</tr>
			   @endforeach
			</tbody>
		</table>
	</div>
</div>