<style>
	table, table tr, table tr td{font-size:10px;font-family:sans-serif;color:#646464;margin-bottom:20px;}
	.border-none tr td{border:none !important;padding:5px 10px 2px 10px;}
	table tr td{border:1px solid #e3e6e6;padding:5px 10px 2px 10px;}
	.pull-right { float:right;text-align:right}
	.med-green { color:#00877f;}
	.med-orange { color:#F07D08;}
	h3 {font-size:12px;font-family:sans-serif;margin: 20px 0px;clear: both;margin-bottom:20px;}
	h4 {color:#F07D08;font-size:12px;font-family:sans-serif;margin: 20px 0px;clear: both;margin-bottom:20px;}
	.bg-gray{border:1px solid #e3e6e6; background: #f4f4f4; width:100%;margin-top:-13px;}
	.tb-green{width:100%;margin-top:-10px;border: 1px solid #00877f;}
	.bg-green{background-color: #00877f;padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:10px;font-family:sans-serif;border-right: 1px solid #fff;}
	
 </style>
 <?php $result_array = $get_response;  ?>

<table class="table table-striped-view border-none" style="margin:0px;width:100%;">
	<tr>
		<td style="vertical-align: top; color:#00877f; font-size:11px;text-align:right;font-family:sans-serif; ">
            <div><span style="color:#868686;margin-left:-50px;">Eligibility ID :</span> {{@$result_array->eligible_id}} </div>
            <div style="margin-top:5px;"><span style="color:#868686;">Created Date :</span> {{App\Http\Helpers\Helpers::dateFormat(@$result_array->created_at,'datetime')}} </div>
        </td>        
    </tr>
</table>
<h4>General Information</h4>
<table class="table table-striped-view bg-gray border-none">   
    <tr>        
        <td>
			<table class="border-none">
				<tr> 
					<td class="med-green">Name</td><td> : </td><td class="med-orange">{{ @$result_array->last_name.", ".@$result_array->first_name." ".@$result_array->middle_name }}</td>
				</tr>
				@if(@$result_array->dob !='' && $result_array->dob !='0000-00-00')
				<tr>
					<td class="med-green">DOB</td><td> : </td><td>{{App\Http\Helpers\Helpers::dateFormat(@$result_array->dob,'dob')}} {{ @$result_array->gender }}</td>
				</tr>
				@else
				<tr> 
					<td class="med-green">Member ID</td><td> : </td><td>{{ @$result_array->member_id }}</td>
				</tr>	
				@endif
				@if(@$result_array->address->street_line_1 !='')
				<tr>
					<td class="med-green">Address</td><td> : </td><td>{{ ucwords(strtolower(@$result_array->address->street_line_1)) }},</td>
				</tr>
				@endif
				@if($result_array->address->street_line_2 !='')
				<tr>
					<td></td><td></td><td>{{ ucwords(strtolower(@$result_array->address->street_line_2)) }},</td>
				</tr>
				@endif
				<tr>
					<td></td><td></td><td>{{ ucwords(strtolower(@$result_array->address->city)) }}@if(@$result_array->address->state),&nbsp;{{ ucwords(strtolower(@$result_array->address->state)) }}@endif @if(@$result_array->address->zip)-{{ @$result_array->address->zip }}@endif</td>
				</tr>
			</table>
        </td>
		<td>
			<table class="border-none">
				@if(@$result_array->dob !='' && $result_array->dob !='0000-00-00')
				<tr>
					<td class="med-green">Member ID</td><td> : </td><td>{{ @$result_array->member_id }}</td>
				</tr>	
				@endif
				<tr>
					<td class="med-green">Group ID</td><td> : </td><td>{{ @$result_array->group_id }}</td>
				</tr>
				<tr>
					<td class="med-green">Group Name</td><td> : </td><td>{{ @$result_array->group_name }}</td>
				</tr>
				@if(@$result_array->date_of_death !='')<tr>
					<td class="med-green">Date of death</td><td> : </td><td> {{App\Http\Helpers\Helpers::dateFormat(@$result_array->date_of_death,'dob')}}</td>
				</tr>@endif
			</table>
        </td>
    </tr>
</table>
<h4>Insurance Information</h4>
<table class="table table-striped-view tb-green border-none" style=" background:#eaf6f5;margin-bottom: 20px;">   
    <tr>        
        <td style="padding:5px;"><span class="med-green">Payer ID</span> : {{ @$result_array->payer_id }} </td>
        <td><span class="med-green">Payer Name </span>: {{ @$result_array->payer_name }}</td>
		<td><span class="med-green">Plan Number</span> : {{@$result_array->plan_number }}</td>
    </tr>
</table>
<table class="table table-striped-view bg-gray" style="border-collapse: collapse;">   
    <tr>        
        <td class="bg-green"></td>
        <td class="bg-green">Start Date</td>
		<td class="bg-green">End Date</td>
    </tr>
	<tr>        
        <td>Eligibilty Date</td>
        <td>@if(@$result_array->eligibilty_dates->start !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->eligibilty_dates->start,'date')}} @else -- @endif</td>
		<td>@if(@$result_array->eligibilty_dates->end !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->eligibilty_dates->end,'date')}} @else -- @endif</td>
    </tr>
	<tr>        
        <td>Eligibility Date</td>
        <td>@if(@$result_array->eligibility_dates->start !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->eligibility_dates->start,'date')}} @else -- @endif </td>
		<td>@if(@$result_array->eligibility_dates->start !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->eligibility_dates->start,'date')}} @else -- @endif </td>
    </tr>
	<tr>        
        <td>Inactivity Date</td>
        <td>@if(@$result_array->inactivity_dates->start !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->inactivity_dates->start,'date')}} @else -- @endif </td>
		<td>@if(@$result_array->inactivity_dates->start !='') {{App\Http\Helpers\Helpers::dateFormat(@$result_array->inactivity_dates->start,'date')}} @else -- @endif </td>
    </tr>
</table>

<h4 style="margin: 20px 0 -10px 0;">Plan Details</h4>
<h3 class="med-green" style="margin:15px 0px;">MA [{{ $all_detail['MA']['plan_type_label'] }}]</h4>
@if(@$all_detail['MA']['active'] != null)
<table class="table table-striped-view tb-green" style="border-collapse: collapse;margin-top:-13px;width:100%;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green">Deductible</td>
		<td class="bg-green">Deductible Remaining</td>
		<td class="bg-green">Copayment</td>
		<td class="bg-green">Coinsurance<br>Percent</td>
		<td class="bg-green">Info Valid Till</td>
		<td class="bg-green">Start Date</td>
		<td class="bg-green">End Date</td>
	</tr>
	<tr>        
		<td>@if(@$all_detail['MA']['active'] =="true") True @else False @endif</td>
		<td>@if(@$all_detail['MA']['deductible'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MA']['deductible']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MA']['deductible_remaining'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MA']['deductible_remaining']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MA']['copayment'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MA']['copayment']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MA']['coinsurance_percent'] !=''){{ $all_detail['MA']['coinsurance_percent'] }} @else -- @endif</td>
		<td>
		@if(@$all_detail['MA']['info_valid_till'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MA']['info_valid_till'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MA']['start_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MA']['start_date'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MA']['end_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MA']['end_date'],'date')}} @else -- @endif
		</td>
	</tr>
</table>
@else
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h3 class="med-green" style="margin:15px 0px;">MB [{{ $all_detail['MB']['plan_type_label'] }}]</h4>
@if(@$all_detail['MB']['active'] != null)
<table class="table table-striped-view tb-green" style="border-collapse: collapse;margin-top:-13px;width:100%;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green">Deductible</td>
		<td class="bg-green">Deductible Remaining</td>
		<td class="bg-green">Copayment</td>
		<td class="bg-green">Coinsurance Percent</td>
		<td class="bg-green">Info Valid Till</td>
		<td class="bg-green">Start Date</td>
		<td class="bg-green">End Date</td>
	</tr>
	<tr>        
		<td>@if(@$all_detail['MB']['active'] =="true") True @else False @endif</td>
		<td>@if(@$all_detail['MB']['deductible'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MB']['deductible']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MB']['deductible_remaining'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MB']['deductible_remaining']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MB']['copayment'] !=''){{ App\Http\Helpers\Helpers::priceFormat(@$all_detail['MB']['copayment']) }} @else -- @endif</td>
		<td>@if(@$all_detail['MB']['coinsurance_percent'] !=''){{ $all_detail['MB']['coinsurance_percent'] }} @else -- @endif</td>
		<td>
		@if(@$all_detail['MB']['info_valid_till'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MB']['info_valid_till'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MB']['start_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MB']['start_date'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MB']['end_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MB']['end_date'],'date')}} @else -- @endif
		</td>
	</tr>
</table>
@else
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h3 class="med-green" style="margin:15px 0px;">MC [{{ $all_detail['MC']['plan_type_label'] }}]</h4>
@if(@$all_detail['MC']['active'] != null)
<table class="table table-striped-view tb-green mc" style="border-collapse: collapse;margin-top:-13px;width:100%;font-size:8px;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green" >Insurance Details</td>
		<td class="bg-green" >Contacts Details</td>
		<td class="bg-green" >Locked</td>
		<td class="bg-green" >Effective Date</td>
		<td class="bg-green" >Termination Date</td>
	</tr>
	<tr>        
		<td>@if(@$all_detail['MC']['active'] =="true") True @else False @endif</td>
		<td>
			@if(@$all_detail['MC']['payer_name'] !='')<span class="med-green">Payer Name</span> : <span>{{ ucwords(strtolower(@$all_detail['MC']['payer_name'])) }} </span><br>@endif
			@if(@$all_detail['MC']['policy_number'] !='')<span class="med-green">Policy Number</span> : <span>{{ @$all_detail['MC']['policy_number'] }} </span><br>@endif
			@if(@$all_detail['MC']['insurance_type_label'] !='')<span class="med-green">{{ @$all_detail['MC']['insurance_type'] }} </span> : <span>{{ ucwords(strtolower(@$all_detail['MC']['insurance_type_label'])) }} </span><br>@endif
			@if(@$all_detail['MC']['mco_bill_option_label'] !='')<span class="med-green">{{ @$all_detail['MC']['mco_bill_option_code'] }} </span> : <span>{{ ucwords(strtolower(@$all_detail['MC']['mco_bill_option_label'])) }} </span><br>@endif
		</td>
		<td>
			<span>{{ ucwords(strtolower(@$all_detail['MC']['contacts']['address1'])) }}@if(@$all_detail['MC']['contacts']['address1'] !=''),<br>@endif{{ ucwords(strtolower(@$all_detail['MC']['contacts']['address2'])) }}
		@if(@$all_detail['MC']['contacts']['address2'] !=''),<br>@endif
		{{ ucwords(strtolower(@$all_detail['MC']['contacts']['city'])) }}@if(@$all_detail['MC']['contacts']['state']),&nbsp;{{ ucwords(strtolower(@$all_detail['MC']['contacts']['state'])) }}@endif
		@if(@$all_detail['MC']['contacts']['zip5']), {{ @$all_detail['MC']['contacts']['zip5']}}@endif @if(@$all_detail['MC']['contacts']['zip4'])-{{ @$all_detail['MC']['contacts']['zip4']}}@endif</span>
		@if(@$all_detail['MC']['contacts']['telephone']!='')<br><span class="med-green">Telephone</span> : <span>{{ @$all_detail['MC']['contacts']['telephone']}}</span>@endif <br>
		@if(@$all_detail['MC']['contacts']['url']!='')<span class="med-green">Url</span> : <span>{{ @$all_detail['MC']['contacts']['url']}} </span>@endif
		</td>
		
		<td>@if(@$all_detail['MC']['locked']) True @else False @endif</td>
		<td>@if(@$all_detail['MC']['effective_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MC']['effective_date'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MC']['termination_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MC']['termination_date'],'date')}} @else -- @endif
		</td>
	</tr>
</table>
@else
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h3 class="med-green" style="margin:15px 0px;">MD [{{ @$all_detail['MD']['plan_type_label'] }}]</h4>
@if(@$all_detail['MD']['active'] != null)
<table class="table table-striped-view tb-green" style="border-collapse: collapse;margin-top:-13px;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green" >Insurance Details</td>
		<td class="bg-green" >Contacts Details</td>
		<td class="bg-green" >Effective Date</td>
		<td class="bg-green" >Termination Date</td>
	</tr>
	<tr>        
		<td>@if(@$all_detail['MD']['active'] =="true") True @else False @endif</td>
		<td>
			@if(@$all_detail['MD']['payer_name'] !='')<span class="med-green">Payer Name</span> : <span>{{ ucwords(strtolower(@$all_detail['MD']['payer_name'])) }} </span><br>@endif
			@if(@$all_detail['MD']['policy_number'] !='')<span class="med-green">Policy Number</span> : <span>{{ @$all_detail['MD']['policy_number'] }} </span>@endif

		</td>
		<td>
			<span>{{ ucwords(strtolower(@$all_detail['MD']['contacts']['address1'])) }}@if(@$all_detail['MD']['contacts']['address1'] !=''),<br>@endif{{ ucwords(strtolower(@$all_detail['MD']['contacts']['address2'])) }}@if(@$all_detail['MD']['contacts']['address2'] !=''),<br>@endif{{ ucwords(strtolower(@$all_detail['MD']['contacts']['city'])) }}@if(@$all_detail['MD']['contacts']['state']),&nbsp;{{ ucwords(strtolower(@$all_detail['MD']['contacts']['state'])) }}@endif
			@if(@$all_detail['MD']['contacts']['zip5']), {{ @$all_detail['MD']['contacts']['zip5']}}@endif @if(@$all_detail['MD']['contacts']['zip4'])-{{ @$all_detail['MD']['contacts']['zip4']}}@endif
		</span>
		@if(@$all_detail['MD']['contacts']['telephone']!='')<br><span class="med-green">Telephone</span> : <span>{{ @$all_detail['MD']['contacts']['telephone'] }} </span><br> @endif 
		@if(@$all_detail['MD']['contacts']['url']!='')<span class="med-green">Url</span> : <span>{{ @$all_detail['MD']['contacts']['url'] }} </span>@endif
		</td>
		<td>@if(@$all_detail['MD']['effective_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MD']['effective_date'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['MD']['termination_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['MD']['termination_date'],'date')}} @else -- @endif
		</td>
	</tr>
</table>
@else
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h3 class="med-green" style="margin:15px 0px;">PR [{{ $all_detail['PR']['plan_type_label'] }}]</h4>
@if(@$all_detail['PR']['active'] != null)
<table class="table table-striped-view tb-green" style="border-collapse: collapse;margin-top:-13px;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green" >Insurance Details</td>
		<td class="bg-green" >Contacts Details</td>
		<td class="bg-green" >Effective Date</td>
		<td class="bg-green" >Termination Date</td>
	</tr>
	<tr>        
		<td>@if(@$all_detail['PR']['active']  == "true") True @else False @endif</td>
		<td>
			@if(@$all_detail['PR']['payer_name'] !='')<span class="med-green">Payer Name</span> : <span>{{ ucwords(strtolower(@$all_detail['PR']['payer_name'])) }} </span><br>@endif
			@if(@$all_detail['PR']['policy_number'] !='')<span class="med-green">Policy Number</span> : <span>{{ @$all_detail['PR']['policy_number'] }} </span>@endif

		</td>
		<td>
			<span>{{ ucwords(strtolower(@$all_detail['PR']['contacts']['address1'])) }}@if(@$all_detail['PR']['contacts']['address1'] !=''),<br>@endif{{ ucwords(strtolower(@$all_detail['PR']['contacts']['address2'])) }}@if(@$all_detail['PR']['contacts']['address2'] !=''),<br>@endif{{ ucwords(strtolower(@$all_detail['PR']['contacts']['city'])) }}@if(@$all_detail['PR']['contacts']['state']),&nbsp;{{ ucwords(strtolower(@$all_detail['PR']['contacts']['state'])) }}@endif
			@if(@$all_detail['PR']['contacts']['zip5']), {{ @$all_detail['PR']['contacts']['zip5']}}@endif @if(@$all_detail['PR']['contacts']['zip4'])-{{ @$all_detail['PR']['contacts']['zip4']}}@endif
			</span>
		@if(@$all_detail['PR']['contacts']['telephone'] !='')<br><span class="med-green">Telephone</span> : <span>{{ @$all_detail['PR']['contacts']['telephone'] }} </span><br> @endif 
		@if(@$all_detail['PR']['contacts']['url'] !='')<span class="med-green">Url</span> : <span>{{ @$all_detail['PR']['contacts']['url'] }} </span> @endif
		</td>
		<td>@if(@$all_detail['PR']['effective_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['PR']['effective_date'],'date')}} @else -- @endif </td>
		<td>@if(@$all_detail['PR']['termination_date'] !='') {{App\Http\Helpers\Helpers::dateFormat(@$all_detail['PR']['termination_date'],'date')}} @else -- @endif
		</td>
	</tr>
</table>
@else
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h4 class="med-green">Requested Service Types</h4>
 @if( count(@$result_array->requested_service_types)>0)
<table class="table table-striped-view tb-green" style="border-collapse: collapse;margin-top:-13px;">   
	<tr>        
		<td class="bg-green">Active</td>
		<td class="bg-green">Plan Details</td>
		<td class="bg-green">Deductible</td>
		<td class="bg-green">Deductible Remaining</td>
		<td class="bg-green">Copayment</td>
		<td class="bg-green">Coinsurance Percent</td>
		<td class="bg-green">Info Valid Till</td>
		<td class="bg-green">Start Date</td>
		<td class="bg-green">End Date</td>
	</tr>
	@foreach(@$result_array->requested_service_types as $value)
	<tr>        
		<td>@if(@$value->active) True @else False @endif</td>
		<td>@if(@$value->plan_type !='') <span class="med-green">{{ @$value->plan_type }}@endif</span>@if(@$value->type_label !=''):{{ ucwords(strtolower(@$value->type_label)) }}@endif</td>
		<td>@if(@$value->deductible !='') {{ App\Http\Helpers\Helpers::priceFormat(@$value->deductible) }} @else -- @endif</td>
		<td>@if(@$value->deductible_remaining !='') {{ App\Http\Helpers\Helpers::priceFormat(@$value->deductible_remaining) }} @else -- @endif</td>
		<td>@if(@$value->copayment !='') {{ App\Http\Helpers\Helpers::priceFormat(@$value->copayment) }} @else -- @endif</td>
		<td>@if(@$value->coinsurance_percent !='') {{ App\Http\Helpers\Helpers::priceFormat(@$value->coinsurance_percent) }} @else -- @endif</td>
		<td>@if(@$value->info_valid_till !='') {{App\Http\Helpers\Helpers::dateFormat(@$value->info_valid_till,'date')}} @else -- @endif </td>
		<td>@if(@$value->start_date !='') {{App\Http\Helpers\Helpers::dateFormat(@$value->start_date,'date')}} @else -- @endif </td>
		<td>@if(@$value->end_date !='') {{App\Http\Helpers\Helpers::dateFormat(@$value->end_date,'date')}} @else -- @endif </td>
		
	</tr>
	@endforeach
</table>
@else 
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif
<h4 class="med-green">Requested Procedure Codes</h4>
 @if( count(@$result_array->requested_procedure_codes)>0)
	<table class="tb-green" style="border-collapse: collapse;margin-top:-13px;">  
	@foreach(@$result_array->requested_procedure_codes as $key => $value)
		<tr>
		@foreach(@$value as $value_key => $value_val)
			<?php $key_name = str_replace("_"," ",$value_key);
			$name = ucwords($key_name);  ?>
			 <td class="bg-green"> {{ @$name }}</td>
		@endforeach
		</tr>
	@endforeach
	@foreach(@$result_array->requested_procedure_codes as $keys => $values)
		<tr>
		@foreach(@$values as $value_keys => $value_vals)
			 <td>{{ @$value_vals }}</td>
		@endforeach
		</tr>
	@endforeach
	</table>
@else 
	<table class="tb-green" style="background:#eaf6f5;">   
		<tr>        
			<td style="padding:5px;">No records found</td>
		</tr>
	</table>
@endif