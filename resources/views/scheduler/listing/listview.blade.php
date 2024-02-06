@if( !is_null($app_list) &&  count(@$app_list)>0)
<div class="box-body table-responsive mobile-scroll">
	<table id="scheduler_reports" class="table table-bordered table-striped mobile-width table-separate">	
		<thead>
			<tr>
				<th>Acc No</th> 
				<th>Patient Name</th>
				<th>DOB</th>
				<th>Facility</th>
				<th>Rendering Provider</th>
				<th>Appt Date</th>
				<th>Appt Time</th>
				<th>Status</th>
				
			</tr>
		</thead>
		<tbody>
			<?php 
				$last_visit = [];
				$count = 1; 
            ?>
                                
			@foreach($app_list as $app_list_val)
				<?php  
					$time_arr = explode("-",@$app_list_val->appointment_time); 
					$status = @$app_list_val->status;                                                
				
					if(isset($last_visit[@$app_list_val->patient->id])) {
						$last_visit_date = $last_visit[@$app_list_val->patient->id];
					} else {
						$last_visit_date = App\Models\Scheduler\PatientAppointment::getLastappointmentDate(@$app_list_val->patient->id);
					}
					
					$patient_name = App\Http\Helpers\Helpers::getNameformat(@$app_list_val->patient->last_name,@$app_list_val->patient->first_name,@$app_list_val->patient->middle_name); $patient_name = trim($patient_name); 
				?>
				@if(strlen($patient_name)>1)
				<tr style="cursor: auto;">
					<td>{!! $app_list_val->patient->account_no !!}</td>
					<td>
						<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
							<span class="js_gray{{@$app_list_val->id }}" style="@if(@$app_list_val->patient->eligibility_verification != 'None' ||@$app_list_val->patient->eligibility_verification != 'Error') display:none; @endif">	
								<a title="Check Eligibility"  data-unqid="{{ @$app_list_val->id }}" data-page="app_listing" data-patientid="{{ @$app_list_val->patient->id }}" data-category="Primary" class="js-patient-eligibility_check" href="javascript:void(0);"><i class="fa fa-user text-gray font10"></i></a> 
							</span>
							<i class="fa fa-spinner fa-spin patientloadingimg{{@$app_list_val->id }}" style="font-size: 11px; display:none;"></i>
							
							<span class="js_green{{@$app_list_val->id }}" @if(@$app_list_val->patient->eligibility_verification == 'Active' ) @else style="display:none;" @endif >	
								<a title="Eligibility Details" class="js_get_eligiblity_details" data-unqid="{{ @$app_list_val->id }}" data-page="app_listing"  data-patientid="{{ @$app_list_val->patient->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a> 
							</span>
							
							<span class="js_red{{@$app_list_val->id }}" @if(@$app_list_val->patient->eligibility_verification == 'Inactive' ) @else style="display:none;" @endif >	
								<a title="Eligibility Details" class="js_get_eligiblity_details" data-page="app_listing"  data-patientid="{{ @$app_list_val->patient->id }}" data-unqid="{{ @$app_list_val->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a> 
							</span>
							<?php  
								$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$app_list_val->patient->id,'encode');
								$patient = @$app_list_val->patient; 
								$patient_data_id = "pat_".$count;
								$provider = @$app_list_val->provider;
								$provider->id = 'p_'.@$provider->id.$count;
								$facility = @$app_list_val->facility;          
                                $facility->id = 'f_'.@$facility->id.@$count;
                                                                       
							?>  
							<span class="p-b-0 p-l-0">
								@include ('layouts/patient_hover')
							</span>
						</div></td>
						<td>
						{{ App\Http\Helpers\Helpers::dateFormat(@$app_list_val->patient->dob,'claimdate') }}
						</td>
					<td>
						<a id="someelem{{hash('sha256',$facility->id)}}" class="someelem" data-id="{{hash('sha256',$facility->id)}}" href="javascript:void(0);"> {{ @$facility->short_name }}</a> 
						@include ('layouts/facility_hover')
					</td>
					
					<td>
						<a id="someelem{{hash('sha256',$provider->id)}}" class="someelem" data-id="{{hash('sha256',$provider->id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a> 
                        @include ('layouts/provider_hover') 
					</td>
					<td>@if(@$app_list_val->scheduled_on != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$app_list_val->scheduled_on,'date') }} @endif</td>
					<td>{{ @$time_arr[0] }}</td>
					
					@if($status !='') <?php $class = preg_replace('/\s+/', ' ',strtolower($status)); ?> @endif
					<td class="{{ @$app_list_val->status }}">@if(@$app_list_val->status == 'Canceled') canceled @else {{ @$app_list_val->status }}@endif
						<a target ="_blank"  href="{{ url('patients/'.$patient_id.'/billing/create')}}" class=" pull-right font14 font600 margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i></a>
					</td>
				</tr>
				@endif
				<?php $count++; ?>
			@endforeach
		</tbody>
	</table>
</div>
@else
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<p class="text-center med-gray margin-t-10 font16 bg-white padding-14-4 yes-border">No Records Found</p>
	</div>
@endif