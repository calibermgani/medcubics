<div class="tab-content js-claim-dyanamic-tab">
<div class="active tab-pane" id="claim-tab-info_main0">
@if(count($claims_lists) > 0)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-b-4 margin-t-10">
		<div class="col-lg-9">
            <a class="js-create-claim claimdetail form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i></a>                
			<a href="javascript:void(0);" class="js-claim-view-tab form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.charges.review')}}"></i> Review</a>
			<a id="claim_notes_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> Notes</a>
			<a id="claim_assign_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
			
			<a id="js-ar-ready" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}}"></i> Ready</a>
			
			<a id="js-ar-pending" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.pending')}}"></i> Pending</a>
			<a id="js-ar-hold" class="form-cursor claimotherdetail font600 p-l-10 p-r-10  right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold</a>
			<a id="js-ar-substatus" class="form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.sub-status')}}"></i> Sub Status</a>
			</div>
			<!-- Added hold reason for bulk hold option in armanagement -->
			<!-- Revision 1 : MR-2786 : 4 Sep 2019 -->
			
		<div class="col-lg-3 pull-right">
			<div class="js-add-new-select hold-option no-margin hide col-lg-12 margin-t-m-5 no-padding" id= "js-holdoptions-type">
                <div class="form-group js_common_ins no-margin">                                                                                                   
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 p-r-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">
                        {!! Form::select('hold_reason_id', array('' => '-- Select Hold Reason --') + (array)$hold_option,  null,['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt js-ar-reason','id' =>'js-hold-reason']) !!} 
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        {!!Form::hidden('hold_reason_exist',null)!!}
                    </div>
                </div>
                <div class="form-group hide no-margin" id="add_new_span">                   
                    <div class="col-lg-11 col-md-11 col-sm-9 col-xs-8 p-r-0  no-margin">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  m-t-sm-5 m-t-xs-5 no-padding hold-option-reason pull-right" >      
                            {!! Form::text('other_reason',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add New','data-label-name'=>'hold reason','data-field-name'=>'option', 'data-table-name' => 'holdoptions']) !!}
                        </div>                                               
                        <a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
                        <a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
                    </div>
                </div>
            </div>
			</div>
			<div class="col-lg-3 pull-right">
				<div class="js-add-new-select substatus-option no-margin hide col-lg-12 margin-t-m-5 no-padding" id= "js-substatus-type">
					<div class="form-group js_common_ins no-margin">
						<?php $sub_status = App\Models\ClaimSubStatus::getClaimSubStatusList(); ?>
					
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 p-r-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">
							{!! Form::select('sub_status_id', array('' => '-- Select Sub Status --') + (array)$sub_status,  null,['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt js-ar-substatus','id' =>'js-claim-substatus']) !!} 
						</div>
						<div class="col-sm-12 col-xs-12">
							{!!Form::hidden('sub_status_exist',null)!!}
						</div>
					</div>
					<div class="form-group hide no-margin" id="add_new_span">                   
						<div class="col-lg-11 col-md-11 col-sm-9 col-xs-8 p-r-0  no-margin">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  m-t-sm-5 m-t-xs-5 no-padding hold-option-reason pull-right" >      
								{!! Form::text('other_substatus',null,['id'=>'newadded','class'=>'form-control','maxlength'=>'25', 'placeholder'=>'Add New','data-label-name'=>'Sub Status','data-field-name'=>'sub_status_desc', 'data-table-name' => 'claim_sub_status']) !!}
							</div>                                               
							<a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
							<a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	@elseif(count($claims_lists) == 0)
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-b-4 margin-t-10">
	
		<a href="javascript:void(0);" class="form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.charges.review')}}"></i> Review</a>
		<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> Notes</a>
		<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
		<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.pending')}}"></i> Pending</a>
		<a id="" class="form-cursor font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold</a>
		<a id="" class="form-cursor font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.sub-status')}}"></i> Claim Sub Status</a>
	</div>
	@endif
	<div class="no-border no-shadow">
		<div class="box-body table-responsive">
			<table class="claims table table-bordered table-striped">	

				<thead>
					<tr>
						<th class="chechbox-readytosubmit table-select-dropdown">
							<div class="no-margin" aria-checked="false" aria-disabled="false">
							   <select name="js-select-option">
								   <option value="none">None</option>
								   <option value="page">This List</option>
								   <option value="all">All List</option>
							   </select>
							   <label for="js-select-all" style="min-height: 10px;"></label>
							</div>
						</th>
						<th>DOS</th>
						<th>Claim No</th>                                                        
						<th>Provider</th>
						<th>Facility</th>
						<th>Billed To</th>
						<th>Charges($)</th>
						<th>Paid($)</th>                        
						<th>Pat AR($)</th>                        
						<th>Ins AR($)</th>                        
						<th>AR Due($)</th>
						<th>Status</th>
						<th>Sub Status</th>
						<th></th>
					</tr>
				</thead>               
				<tbody>
				   <?php 
						$count = 1;   
						$patId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id, 'decode');
						$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetailsByPatient('patient', $patId);  
					?>
				   @foreach($claims_lists as $keys=>$claim)                     
					<?php 
						$facility = $claim->facility_detail;
						$provider = $claim->rendering_provider;
						$status_display_class = ($claim->status == "Paid")?"js-listclaim ar-hide-class":"";
						$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id, 'encode');
						$url = url('patients/payment/popuppayment/'.$claim_id);
						// Charge edit link
						$insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;
						$detArr = ['patient_id'=> @$claim->patient_id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
						$edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Billing", $detArr);
					?>
					<tr>
					@if($count == 1)
						<input type="hidden" name="encodeClaim" value="{{ implode(',',$encodeClaimIds) }}" />
					@endif
                        <td>{!! Form::checkbox('claim_ids[]', @$claim->claim_number, null, ['class'=>"chk js_claim_ids js-select-all-sub-checkbox",'data-id'=>"",'id'=>$keys]) !!}<label for="{{$keys}}" class="no-bottom">&nbsp;</label></td> 
						<td> <a href="#js-model-popup-payment" claim_number = "{{$claim->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$url}}" class="claimbilling">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claim->date_of_service)}}</a></td>
						<td>
							<a href="{{ $edit_link }}" target="_blank">{{@$claim->claim_number}}</a>
						</td>  
						<td><a id="someelem{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$claim->rendering_provider->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claim->rendering_provider->short_name,15,' ...')}}</a> 
						<?php @$provider->id = 'p_'.@$claim->rendering_provider->id.$count; ?> 
						@include ('layouts/provider_hover')</td>
						<td> <a id="someelem{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$claim->facility_detail->id.$count)}}" href="javascript:void(0);"> {{str_limit(@$claim->facility_detail->short_name,15,' ...')}}</a> 
						<?php @$facility->id = 'f_'.@$claim->facility_detail->id.$count; ?> 
						@include ('layouts/facility_hover')</td> 
						<td>
						@if(empty($claim->insurance_details))
							Self
						@else	
							{!!App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id)!!}
						@endif
						</td>
						<td class="text-right">{{@$claim->total_charge}}</td>
						<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->total_paid)!!}</td>                        
						<td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->patient_due)!!}</span></td>
						<td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->insurance_due)!!}</span></td>
                        <td><span class="pull-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</span></td>
						<td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status}} @endif">{{ @$claim->status}}</span></td>
						<td>
							@if(isset($claim->claim_sub_status->sub_status_desc))
								{{ $claim->claim_sub_status->sub_status_desc }}
							@else 
								-Nil-
							@endif
						</td>
						<td>@if(isset($claim->followup_details)) 
							<i class="fa fa-file-text-o js_showing_history" data-url="{{ url('patients/armanagement/followup/history') }}/{{$claim->claim_number}}" data-claimno="{{$claim->claim_number}}"  data-placement="bottom" data-toggle="tooltip" data-original-title="Followup History"></i>
						@endif</td>
					</tr>
					<?php $count++;   ?> 
					@endforeach              
				</tbody>
			</table>

		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>

</div>

<!-- Showing Followup history in the popup using ajax -->
<div id="show_followup_history" class="modal fade in">
	<div class="modal-md-650">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"> Claim No : CHR401</h4>
			</div>
			<div class="modal-body no-padding" >
			</div>
		</div>
	</div>
</div>
<style>
.ar-hide-class {
	display: none;
}
</style>