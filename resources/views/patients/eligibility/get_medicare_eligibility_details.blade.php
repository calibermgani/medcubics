<style>
 span{font-size:12px;}
 button span{font-size:18px;}
</style>
<div class="box-body no-padding m-b-m-15" style="margin-bottom: 3px;">                
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-4">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding ">
			<p class="no-bottom font12">Eligibility ID : <span class="med-green">{{ @$ediEligibility->edi_eligibility_id }} </span></p>    
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding" style="text-align:right;">           
			<p class="no-bottom font12"> Checked Date : <span class="med-green">{{ App\Http\Helpers\Helpers::dateFormat(@$ediEligibility->edi_eligibility_created,'date')}} </span></p>
			<p class="no-bottom font12"> Checked By : <span class="med-green">{{ ucwords(@$ediEligibility->user->name) }} </span></p>
		</div>        
	</div>    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border:1px solid #f0f0f0;line-height: 22px;">
        <span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding med-green">Patient Name</span>
		<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:right;">:</span>
        <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8 med-orange">{{ @$ediEligibility->patient->last_name.', '.@$ediEligibility->patient->first_name }}</span>
        <span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding med-green">Policy ID </span>
		<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:right;">:</span>
        <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{($patient_insurance!='')?$patient_insurance->policy_id :'' }}</span>
        <span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding med-green">Plan Number</span>
		<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:right;">:</span>
        <span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{ @$ediEligibility->plan_number }}</span>
    </div>
</div><!-- /.box-body -->
	<?php  $genderarray = ['M'=>'male','F'=>'Female']; ?>
	 
<h6 class="med-orange" style="margin-bottom: 3px;">General Information</h6> 	
<div class="box box-view no-border no-shadow" style="border:1px solid #f0f0f0;line-height: 22px;"><!--  Box Starts -->
	<div class="box-header-view no-border-radius" style="line-height: 10px;">
		 <h3 class="box-title"  style="line-height: 10px;">Subscriber</h3>
		 <div class="box-tools pull-right">
			 <button class="btn btn-box-tool margin-t-m-4" data-widget="collapse"><i class="fa fa-minus "></i></button>
		 </div>
	</div><!-- /.box-header -->
	<div class="box-body no-padding">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-4 ">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Name</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6 med-orange">{{ ucwords(strtolower(@$ediEligibility->contact_details->last_name)).', '.ucwords(strtolower(@$ediEligibility->contact_details->first_name)) }}</span>
				</div>
				@if(@$ediEligibility->contact_details->dob!='0000-00-00')
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">D.O.B</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$ediEligibility->contact_details->dob!='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$ediEligibility->contact_details->dob,'dob') }} @endif{{ (@$genderarray[$ediEligibility->contact_details->gender]!='')? ', '.$genderarray[$ediEligibility->contact_details->gender]:'' }}</span>
				</div>	
				@else
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Member ID</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6 med-orange">{{ @$ediEligibility->contact_details->member_id }}</span>
				</div>
				@endif
				@if(@$ediEligibility->contact_details->address1!='')
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Address</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						{{ ucwords(strtolower(@$ediEligibility->contact_details->address1)) }}@if(@$ediEligibility->contact_details->address1 !=''), @endif{{ ucwords(strtolower(@$ediEligibility->contact_details->address2)) }}
						@if(@$ediEligibility->contact_details->address2 !=''), @endif
					</span>
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">&emsp;</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&emsp;</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						{{ucwords(strtolower(@$ediEligibility->contact_details->city))}}@if(@$ediEligibility->contact_details->state !=''), @endif{{ucwords(strtolower(@$ediEligibility->contact_details->state)) }}@if(@$ediEligibility->contact_details->zip5!='0'), @endif{{@$ediEligibility->contact_details->zip5}}@if(@$ediEligibility->contact_details->zip4!='0')-{{@$ediEligibility->contact_details->zip4}}@endif
					</span>
				</div>
				 @endif
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">           
				@if(@$ediEligibility->contact_details->dob!='0000-00-00')
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-3 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Member ID</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-8 col-md-6 col-sm-6 col-xs-6 med-orange">{{ @$ediEligibility->contact_details->member_id }}</span>
				</div>
				@endif
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-3 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Group Name</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-8 col-md-6 col-sm-6 col-xs-6 med-orange">{{ ucwords(strtolower(@$ediEligibility->contact_details->group_name)) }}</span>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<span class="col-lg-3 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Group ID</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-8 col-md-6 col-sm-6 col-xs-6 med-orange">{{ @$ediEligibility->contact_details->group_id }}</span>
				</div>
			</div>        
		</div>
	</div>
</div><!-- /.box-body -->
<h6 class="med-orange" style="margin-bottom: 3px;">Plan Details</h6> 

@foreach(@$edi_medicare_detail as $med_detail_key => $med_detail_val)
<div class="box box-view no-border no-shadow" style="border:1px solid #f0f0f0;line-height: 22px;"><!--  Box Starts -->
	<div class="box-header-view no-border-radius" style="line-height: 10px;">
		 <h3 class="box-title"  style="line-height: 10px;">{{ $med_detail_val['plan_type'] }} [{{ $med_detail_val['plan_type_label'] }}]</h3>
		 <div class="box-tools pull-right">
			 <button class="btn btn-box-tool margin-t-m-4" data-widget="collapse"><i class="fa fa-minus "></i></button>
		 </div>
	</div><!-- /.box-header -->
	<div class="box-body no-padding">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="table-responsive"  style="border:1px solid #E7FCFD;width:100%;line-height: 22px; font-size: 12px;">
			@if(@$med_detail_val['active']!= null)
				@if(@$med_detail_val['payer_name']!='')
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
						<span class="med-green" style="width:21%;float:left;">Payer Name</span>
						<span style="width:4%;float:left;padding-left:10px;" >:</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{ ucwords(strtolower(@$med_detail_val['payer_name']))  }}</span>
						@if(@$med_detail_val['policy_number']!='')
						<span class="med-green" style="width:21%;float:left;">Policy Number</span>
						<span style="width:4%;float:left;padding-left:10px;" >:</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{ @$med_detail_val['policy_number'] }}</span>
						@endif
						@if(@$med_detail_val['insurance_type']!='')
						<span class="med-green" style="width:21%;float:left;">{{ @$med_detail_val['insurance_type'] }}</span>
						<span style="width:4%;float:left;padding-left:10px;" >:</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{ ucwords(strtolower(@$med_detail_val['insurance_type_label'])) }}</span>
						@endif
						@if(@$med_detail_val['mco_bill_option_code']!='')
						<span class="med-green" style="width:21%;float:left;">{{@$med_detail_val['mco_bill_option_code'] }}</span>
						<span style="width:4%;float:left;padding-left:10px;" >:</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">{{ ucwords(strtolower(@$med_detail_val['mco_bill_option_label'])) }}</span>
						@endif
						@if(count(@$med_detail_val['contact_details'])>0)
						<span class="med-green" style="width:21%;float:left;">Address</span>
						<span style="width:4%;float:left;padding-left:10px;" >:</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
							{{ ucwords(strtolower(@$med_detail_val['contact_details']['address1'])) }}@if($med_detail_val['contact_details']['address1'] !=''), @endif{{ ucwords(strtolower(@$med_detail_val['contact_details']['address2'])) }}@if($med_detail_val['contact_details']['address2'] !=''), @endif
						</span>
						<span style="width:21%;float:left;">&emsp;</span>
						<span style="width:4%;float:left;padding-left:10px;" >&emsp;</span>
						<span class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
						{{ucwords(strtolower(@$med_detail_val['contact_details']['city']))}}@if(@$med_detail_val['contact_details']['state'] !=''), @endif{{ucwords(strtolower(@$med_detail_val['contact_details']['state'])) }}@if(@$med_detail_val['contact_details']['zip5'] !=''), @endif{{@$med_detail_val['contact_details']['zip5']}}@if(@$med_detail_val['contact_details']['zip4']!='')-@endif{{@$med_detail_val['contact_details']['zip4']}}
						</span>
						@endif
					</div>
				@endif	
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Active</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['active'] }}&emsp;</span>
				@if(@$med_detail_val['deductible']!='')
					@if(@$med_detail_val['deductible']!='')
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Deductible</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['deductible'] }}&emsp;</span>
					@endif
					@if(@$med_detail_val['deductible_remaining']!='')
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Deductible Remaining</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['deductible_remaining'] }}&emsp;</span>
					@endif
				@else
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Effective Date</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$med_detail_val['effective_date']!='' && $med_detail_val['effective_date']!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$med_detail_val['effective_date'],'date')}} @else Nil @endif</span>
				@endif
					@if(@$med_detail_val['copayment']!='')
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Copayment</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['copayment'] }}&emsp;</span>
					@endif
					@if(@$med_detail_val['coinsurance_percent']!='')
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Coinsurance Percent</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['coinsurance_percent'] }}&emsp;</span>
					@endif
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding">
					
				@if($med_detail_val['plan_type'] =='MA' || $med_detail_val['plan_type'] =='MB')
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Start Date</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$med_detail_val['start_date']!='' && $med_detail_val['start_date']!='0000-00-00'){{App\Http\Helpers\Helpers::dateFormat(@$med_detail_val['start_date'],'date')}} @else Nil @endif</span>
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">End Date</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$med_detail_val['end_date']!='' && $med_detail_val['end_date']!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$med_detail_val['end_date'],'date')}} @else Nil @endif</span>
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Info Valid Till</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$med_detail_val['info_valid_till']!='' && $med_detail_val['info_valid_till']!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$med_detail_val['info_valid_till'],'date')}} @else Nil @endif</span>
					
				@else
					@if($med_detail_val['plan_type'] =='MC')
						@if(@$med_detail_val['locked']!='')
						<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Locked</span>
						<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
						<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">{{ @$med_detail_val['locked'] }}</span>
						@endif
					@else
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">&emsp;</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&emsp;</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">&emsp;</span>
					@endif
					<span class="col-lg-5 col-md-5 col-sm-5 col-xs-5 no-padding med-green">Termination Date</span>
					<span class="col-lg-1 col-md-1 col-sm-1 col-xs-1">:</span>
					<span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">@if(@$med_detail_val['termination_date']!='' && $med_detail_val['termination_date']!='0000-00-00') {{App\Http\Helpers\Helpers::dateFormat(@$med_detail_val['termination_date'],'date')}} @else Nil @endif</span>
				@endif
				</div>
			</div>
			@else 
				<table class="tb-green" style="background:#eaf6f5;">   
					<tr>        
						<td style="padding:5px;">No records found</td>
					</tr>
				</table>
			@endif
		</div>
	</div>
</div><!-- /.box-body -->
@endforeach	
	
 
<!-- Insurance  -->
@if(@$patient_eligibility->is_edi_atatched=='1' && $patient_eligibility->edi_filename!='')
	@if($patient_eligibility->patients_id == 0)
		<?php
		$patientencode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_eligibility->temp_patient_id,'encode'); 
		?>
	@else
		<?php
		$patientencode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_eligibility->patients_id,'encode'); 
		?>
	@endif		
<div class="pull-right">	
	<a  class="btn btn-medcubics" onclick="window.open('{{ url('patients/getEligibilityMoreInfo/'.$patientencode_id.'/'.$patient_eligibility->edi_filename) }}', '_blank')">More Details </a>
	
</div>	
@endif

@if($patient_id != '')	
<button type="button" data-patientid="{{ $patient_id }}" @if($page_type == 'pat_ins') data-insuranceid="{{ $insuranceid }}" data-policyid="{{$policyid }}" data-page="pat_ins"  @endif data-category="{{ $category }}" class="btn btn-medcubics js_recheck_eligibility">Recheck Eligibility</button> <i style="display:none" id="coverimg" class="fa fa-spinner fa-spin coverloadingimg"></i>
@endif
<!--  Show Icon for Temp Popup  -->	
@if($patient_id == '')	
	<button type="button" data-patientid="" data-category="{{ $category }}" class="btn btn-medcubics js_recheck_eligibility">Recheck Eligibility</button> <i style="display:none" id="coverimg" class="fa fa-spinner fa-spin coverloadingimg"></i>
@endif