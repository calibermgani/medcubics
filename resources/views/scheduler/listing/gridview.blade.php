<div class="box-body">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div id="products" class="row list-group">
            @if(count(@$app_list)>0)
            <?php 
				$last_visit = [];
				$count = 1; 
			?>
            @foreach($app_list as $app_list_val)
            <div class="item col-lg-3 col-md-3 col-sm-4 col-xs-12 grid-height @if(@$view_type =='listview')list-group-item @endif" style="padding: 10px 15px;">
                <div class="thumbnail">
                    <div class="caption">
                        <?php 
							$patient_name = App\Http\Helpers\Helpers::getNameformat(@$app_list_val->patient->last_name,@$app_list_val->patient->first_name,@$app_list_val->patient->middle_name); $patient_name = trim($patient_name); 
							if((isset($last_visit[@$patient->id]))) {
								$last_visit_date = $last_visit[$patient->id];
							} else {
								$last_visit_date = App\Models\Scheduler\PatientAppointment::getLastappointmentDate(@$patient->id);
							}
						?>  
                        
                        <p class="med-green font14 no-bottom font600">
                            @if($patient_name != '' &&  strlen($patient_name)>1)
                        <div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
                            <span class="js_gray{{@$app_list_val->id }}" @if(@$app_list_val->patient->eligibility_verification == 'Active' ||@$app_list_val->patient->eligibility_verification == 'Inactive') style="display:none;" @endif >	
                                <a title="Check Eligibility"  data-unqid="{{ @$app_list_val->id }}" data-page="app_listing" data-patientid="{{ @$app_list_val->patient->id }}" data-category="Primary" class="js-patient-eligibility_check" href="javascript:void(0);"><i class="fa fa-user text-gray font10"></i></a> 
                            </span>
                            <i class="fa fa-spinner fa-spin patientloadingimg{{@$app_list_val->id }} font12" style="display:none;"></i> 

                            <span class="js_green{{@$app_list_val->id }}" @if(@$app_list_val->patient->eligibility_verification == 'None' || @$app_list_val->patient->eligibility_verification == 'Inactive' || @$app_list_val->patient->eligibility_verification == 'Error') style="display:none;" @endif >	
                                <a title="Eligibility Details" class="js_get_eligiblity_details" data-unqid="{{ @$app_list_val->id }}" data-page="app_listing"  data-patientid="{{ @$app_list_val->patient->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-green font10"></i></a> 
                            </span>

                            <span class="js_red{{@$app_list_val->id }}" @if(@$app_list_val->patient->eligibility_verification == 'None' || @$app_list_val->patient->eligibility_verification == 'Active' || @$app_list_val->patient->eligibility_verification == 'Error') style="display:none;" @endif >	
                                <a title="Eligibility Details" class="js_get_eligiblity_details" data-page="app_listing"  data-patientid="{{ @$app_list_val->patient->id }}" data-unqid="{{ @$app_list_val->id }}" data-category="Primary" data-toggle="modal" href="#eligibility_content_popup"><i class="fa fa-user text-red font10"></i></a> 
                            </span>
                            <?php  
								$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($app_list_val->patient->id,'encode');  
								$time_arr = explode("-",@$app_list_val->appointment_time);
								$patient = @$app_list_val->patient; 
								$patient_data_id = "pat_".$count;
								$provider = $app_list_val->provider;
								$provider->id = 'p_'.@$provider->id.$count;
								$facility = $app_list_val->facility;
								$facility->id = 'f_'.@$facility->id.$count;
							?>  
                            <span class="p-b-0 p-l-0">
                                @include ('layouts/patient_hover')
                            </span>
                        </div>
                        @else
                        <div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">&emsp;</div>
                        @endif
                        </p>
                        <p class="birthday no-bottom sm-size">@if(@$patient->dob != "0000-00-00" && @$patient->dob != "1901-01-01")<i class="fa fa-birthday-cake font12"></i> {{ App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'dob') }} @endif @if(@$patient->gender !='') 
                            {{ @$patient->gender }} @endif</p>

                        <p class="med-gray-dark hide-grid margin-t-10">{{ @$patient->address1 }}{{(@$patient->city =='') ? '':','}}{{ @$patient->city }}&nbsp;{{@$patient->state}}&nbsp;{{@$patient->zip5 }}{{(@$patient->zip4 =='') ? '':'-'}}{{ @$patient->zip4 }}<br>{{ @$patient->phone }} &emsp;</p>
                        <div class="group inner list-group-item-text m-t">
                            <p class="list-group-item-text"><span class="med-green font600">Facility : </span><a id="someelem{{hash('sha256',$facility->id)}}" class="someelem med-gray-dark font600" data-id="{{hash('sha256',$facility->id)}}" href="javascript:void(0);">{{ @$facility->short_name }}</a> 
                                @include ('layouts/facility_hover')</p>
                            <p class="list-group-item-text margin-t-m-10">
                            <div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
                                <span class="med-green font600">Provider : </span>
                                <a id="someelem{{hash('sha256',$provider->id)}}" class="someelem med-gray-dark font600" data-id="{{hash('sha256',$provider->id)}}" href="javascript:void(0);">{{ @$provider->short_name }}</a> 
                                @include ('layouts/provider_hover')
                            </div>
                            </p>
                            <p class="list-group-item-text margin-t-m-10 hide-grid"><span class="med-green font600">Acc No : </span>{!! $app_list_val->patient->account_no  !!}</p>
                        </div>
                        <div class="row">
                            <?php $status = $app_list_val->status; ?>  
                            @if($status !='') <?php $class = preg_replace('/\s+/', ' ',strtolower($status)); ?> @endif 
                            <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 m-{{ @$status }}" style="margin-left: 4px;">

                                <p class="font600 no-bottom"><span style="background: #eaf1f4; padding: 0px 6px; border-radius: 4px; color:#919ca2">{{ @$time_arr[0] }}</span>  <span class="{{ @$status }}">{{ @$status }}</span> <span class="pull-right tot-amt padding-r-5" style="font-weight: 500;">@if(@$app_list_val->scheduled_on != "0000-00-00" && @$app_list_val->scheduled_on != "1901-01-01"){{ App\Http\Helpers\Helpers::dateFormat(@$app_list_val->scheduled_on,'date') }} @endif</span></p>
                            </div>                       
                        </div>
                    </div>
                </div>
            </div>
            <?php $count++; ?>
            @endforeach
            @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                <p class="text-center med-gray margin-t-10 font16 bg-white padding-14-4 yes-border">No Records Found</p>
            </div>
            @endif
        </div>
    </div>
</div>