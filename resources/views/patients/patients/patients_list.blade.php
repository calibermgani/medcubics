@if(!empty($patients))
<?php $insurances = json_decode(json_encode($insurance_list), TRUE); ?>
	@foreach($patients as $patient)
		@if($patient !='')
		<?php
			$getReachEndday = '';
			$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id);
			$plan_end_date = ''; //App\Http\Helpers\Helpers::getPatientPlanEndDate(@$patient->id,'','','Primary'); 
			if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
				$getReachEndday = 0;
			} else {
				$now = strtotime(date('Y-m-d')); // or your date as well
				$your_date = strtotime($plan_end_date);
				$datediff = $now - $your_date;
				$getReachEndday = floor($datediff / (60 * 60 * 24));
			}
			$insurance_name = "";
			if ($patient->is_self_pay == 'Yes') {
				$insurance_name = "Self Pay";
			} else {
				//$insurance_name = App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$patient->id)	
				$insurance_name = (isset($insurances['all'][$patient->id])) ? ($insurances['all'][$patient->id]) : '';		
			}
			$patient_ins_name = $insurance_name;
			$open_new_window = 0; // open patient view in same page. 		

			$fin_details = @$patient->patient_claim_fin[0];
			//$patient_due = (!empty($fin_details->total_pat_due)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_pat_due): '0.00';  
			$patient_due = (!empty($patient->total_pat_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_pat_due) : '0.00';
			//$ins_due  = (!empty($fin_details->total_ins_due)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_ins_due): '0.00';  
			$ins_due = (!empty($patient->total_ins_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ins_due) : '0.00';
			//$ar_due = (!empty($fin_details->total_ar)) ? App\Http\Helpers\Helpers::priceFormat(@$fin_details->total_ar): '0.00';  
			$ar_due = (!empty($patient->total_ar)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ar) : '0.00';
			$patient_name = App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
		?>
		<tr class="form-cursor js-table-click" data-url="{{ url('patients/'.$patient_id.'/ledger') }}">
			<td>
				<span>{{ @$patient->account_no }}</span>
			</td>
			<td>			
				<div class="p-b-0 p-l-0" style="width:10%;float:left;"> 
					<span class="js_insgray{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'None' || @$patient->eligibility_verification == '' || is_null(@$patient->eligibility_verification) || @$patient->eligibility_verification == 'Error') @else style="display:none;" @endif >  
						<a title="Check Eligibility" data-unid="{{ @$patient->id }}"  data-patientid="{{ @$patient_id }}" data-category="Primary" class="js-patient-eligibility_check js-claimlink" href="javascript:void(0);"><i class="fa {{Config::get('cssconfigs.common.user')}} text-gray font10"></i></a> 
					</span>                             
					<i class="fa fa-spinner fa-spin font11 patientinsloadingimg{{ @$patient->id }}" style="display:none;"></i>
					<span class="js_insgreen{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'Active' && $getReachEndday <= 0)  @else style="display:none;" @endif >    
						<a title="Eligibility Details" data-unid="{{ @$patient->id }}" @if(Session::get('practice_dbid') == 40) class="js_get_eligiblity_details_waystar js-claimlink" @else class="js_get_eligiblity_details js-claimlink" @endif data-patientid="{{ @$patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.common.user')}} text-green font10"></i></a> 
					</span>
					<span class="js_insred{{ @$patient->id }} hidden-print" @if(@$patient->eligibility_verification == 'Inactive' || $getReachEndday>0) @else style="display:none;" @endif >    
						<a title="Eligibility Details" data-unid="{{ @$patient->id }}" @if(Session::get('practice_dbid') == 40) class="js_get_eligiblity_details_waystar js-claimlink" @else class="js_get_eligiblity_details js-claimlink" @endif data-patientid="{{ @$patient_id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa {{Config::get('cssconfigs.common.user')}} text-red font10"></i></a> 
					</span>                                         
				</div>
				<!-- Patient name block click not redirect issue fixed -->
				<div class="p-b-0 p-l-0 js-table-click">                
					@include ('layouts/patient_hover')                         
				</div>
			</td>
			<td class="js-table-click">@if($patient->mobile != '') {!! @$patient->mobile !!} @else -Nil- @endif</td>
			<td class="js-table-click">{{@$patient->gender}} </td>        
			<td class="js-table-click">{{ App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'claimdate') }}</td>
			<td class="js-table-click">@if($patient->ssn != '') {!! @$patient->ssn !!} @else -Nil- @endif</td>        
			<td class="js-table-click">@if($insurance_name != '') {!! @$insurance_name !!} @else -Nil- @endif</td>
			<td class="text-right">            
				<?php /* Patient Payment popup */ ?>
				<a href= "#" data-toggle="modal" data-tile = "Post Patient Payment" data-target="#choose_claims" data-url = "{{url('patients/'.$patient_id.'/paymentinsurance/patient')}}" class=" form-cursor js-claimlink claimotherdetail">{{ @$patient_due }} </a>    
			</td>
			<td class="text-right">
				<?php /* Insurance Payment popup */ ?>
				<a href= "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "{{url('patients/'.$patient_id.'/paymentinsurance/insurance')}}" class="js-claimlink js_pay_ins claimdetail js_pay_dea form-cursor">{{ @$ins_due }} </a>                
			</td>
			<td class="text-right">
				<?php /* Patient AR page open */ ?>
				<a target =""  href="{{url('patients/'.$patient_id.'/armanagement/arsummary')}}" class="">{{ @$ar_due }}</a>
			</td>        
			<?php /*<td class="js-table-click">{{ App\Http\Helpers\Helpers::dateFormat(@$patient->created_at,'date')}}</td>        */?>
			<td class="js-table-click">{{ App\Http\Helpers\Helpers::timezone(@$patient->created_at, 'm/d/y') }}</td>        
			<td class="js-table-click hidden-print js-prevent-show"> 
				{!! Form::checkbox("status",1,(@$patient->status == "Active") ? $patient->status : null,["class" => "js_patient_status",'name'=>'js_document_model','data-patientid'=>@$patient_id,'id'=>$patient->id]) !!}<label class="no-bottom" for="{{$patient->id}}">&nbsp;</label>
				
				<?php /* Patient Charge page link to */ ?>
				<a target =""  href="{{url('patients/'.$patient_id.'/billing')}}" class="font14 font600 p-r-5"><i class="fa fa-list" data-placement="bottom" data-toggle="tooltip" data-original-title="Charge"></i></a>
				<?php /* Patient Appointment page link to */ ?>
				<a target =""  href="{{url('patients/'.$patient_id.'/appointments')}}" class="font14 font600 p-r-5"><i class="fa fa-calendar" data-placement="bottom" data-toggle="tooltip" data-original-title="Appointments"></i></a>
				<?php /* Patient Demographics page link to */ ?>
				<a target =""  href="{{url('patients/'.$patient_id.'/edit')}}" class="font14 font600 p-r-5"><i class="fa fa-edit" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i></a>
			</td>
			<td class="js-table-click hidden-print"> 
				<!--<input type="text" class="knob" data-readonly="true" value="{{@$patient->percentage}}" data-thickness=".26" data-width="20" data-height="20" data-fgColor="#39CCCC"/> -->
				<div class="@if(@$patient->percentage =='100') patient-100 @elseif(@$patient->percentage =='40') patient-40 @elseif(@$patient->percentage =='60') patient-60 @else patient-0 @endif" style=""><span>{{@$patient->percentage}}</span></div>
			</td>
		</tr>
		@endif
	@endforeach
@else
	<tr>
		<td colspan="13">No Records Found</td>
	</tr>
@endif