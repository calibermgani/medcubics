@if(!empty($patients_arr))
	@foreach($patients_arr as $keys=>$patient_value)			
	<?php
		$patID = $patient_value->id;	
		$patPmtDate = isset($patient_value->patient_last_pmt->created_at) ? $patient_value->patient_last_pmt->created_at : '';
		$patPmtAmt = isset($patient_value->patient_last_pmt->total_paid) ? $patient_value->patient_last_pmt->total_paid : 0;
		$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_value->id,'encode');
		$patient_name = App\Http\Helpers\Helpers::getNameformat("$patient_value->last_name","$patient_value->first_name","$patient_value->middle_name");	
	?>                            
	<tr class="js_remove{{@$patient_value->id}} cur-cursor">                                
		<td><input type="checkbox" name="bulkcheck" class="js_sub_checkbox" value="{{ $patient_value->id }}" id="{{$keys}}"><label for="{{$keys}}" class="no-bottom">&nbsp;</label></td>
		<td>				
			<span><a href="{{ url('patients/'.$patient_id.'/ledger') }}" target="_blank"><span data-id="{{@$patient_value->id}}" class="someelem" id="someelem{{@$patient_value->id}}">@if(@$patient_value->title){{ @$patient_value->title }}. @endif{{ str_limit($patient_name,25,'...') }}</span> </a></span> 

			<div class="on-hover-content js-tooltip_{{$patient_value->id}}" style="display:none;">
				<span class="med-orange font600">@if($patient_value->title){{ @$patient_value->title }}. @endif{{ $patient_name }}</span> 
				<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$patient_value->account_no }}
					<br>
					@if(@$patient_value->dob != "0000-00-00" && @$patient_value->dob != "" && @$patient_value->dob != "1901-01-01")<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_value->dob,'claimdate') }} <span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$patient_value->dob) }} @endif
					<span class="font600">Gender :</span> {{ @$patient_value->gender }}<br>
					<span class="font600">Address :</span> {{ @$patient_value->address1 }}<br>
					{{ @$patient_value->city}}, {{ @$patient_value->state}}, {{ @$patient_value->zip5}}-{{ @$patient_value->zip4}}<br>
					@if(@$patient_value->phone)<span class="font600">Home Phone :</span>{{$patient_value->phone}} <br>@endif
					@if(@$patient_value->work_phone)<span class="font600">Work Phone :</span> {{$patient_value->work_phone}}@endif
				</p>
			</div>
		</td>		
		<td>{{ @$patient_value->account_no }}</td>
		<td>{{ @$patient_value->statements_sent }} </td>
		<td>{{ App\Http\Helpers\Helpers::timezone(@$patPmtDate, 'm/d/y') }}</td>
		<td class= "text-right">{!! App\Http\Helpers\Helpers::priceFormat($patPmtAmt) !!}</td>
		<td class= "text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$patient_value->patient_due) !!}</td> 	
		<td>	
			<div class="js_loading{{ $patient_value->id }} hide">
				<i class="fa fa-spinner fa-spin font20 med-green"></i> Processing
			</div>
			{!! Form::button('<i class="fa fa-picture-o med-green"></i>', ['data-placement'=>"bottom",  	'data-toggle'=>'tooltip','data-original-title'=>"Preview",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-name'=>'preview','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!}
			
			{!! Form::button('<i class="fa fa-download med-green hide send-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip','data-original-title'=>"Send Statement",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-module'=>"bulk",'data-name'=>'sendstatement','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!}

			@if($patient_value->email != '') 
			{!! Form::button('<i class="fa fa-envelope-o med-green email-statement"></i>', ['data-placement'=>"bottom",  'data-toggle'=>'tooltip','data-original-title'=>"Email Statement",'class'=>'bg-white no-border no-background js_submit_type js_hide'.$patient_value->id,'data-module'=>"bulk",'data-name'=>'emailstatement','data-unique'=>$patient_value->id,'data-id'=>$patient_id]) !!} 
			@endif
		</td>		
	</tr>
	@endforeach
@else 
	<tr>
		<td colspan="8">No Records Found</td>
	</tr>	
@endif