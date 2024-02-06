<div class="col-md-12 margin-t-m-20 print-m-t-30">
    <div class="box box-info no-shadow l-orange-b">
        <div class="box-body tabs-orange-bg">
            <?php $tabpatientid = $patient_id = $tabpatientid; ?>
            @if(!empty($alert_notes))
            <span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
            <div class="snackbar-alert success menu">
                <h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Alert Notes</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>            
			 <p>{!! $alert_notes !!}</p>
            </div>
            @endif
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="text-center">
                    <div class="js_patient_img_part" data-provides="fileupload">
                        <?php						
							if(@$needdecode=='yes') {
								$encodepatientid = $tabpatientid; 
								if(!is_numeric($tabpatientid))
									$tabpatientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$tabpatientid,'decode'); 
							} else {
								$encodepatientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$tabpatientid);
							}                        
							$patient_tabs_api_response = App\Http\Controllers\Patients\Api\PatientApiController::getPatientTabsDetails(@$tabpatientid);
							$patient_tabs_api_res_data = $patient_tabs_api_response->getData();
							$patient_tabs_details_new = $patient_tabs_api_res_data->data->patients;
							$patient_tabs_insurance_count_new = $patient_tabs_api_res_data->data->patient_insurance_count;
							$patient_tabs_insurance_details_new = json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true);

							$filename = @$patient_tabs_details_new->avatar_name . '.' . @$patient_tabs_details_new->avatar_ext;
							$img_details = [];
							$img_details['module_name'] = 'patient';
							$img_details['file_name'] = $filename;
							$img_details['practice_name'] = "";
							$cur_demo_page = Route::getFacadeRoot()->current()->uri();

							if (@$patient_tabs_details_new->avatar_name != "" && $cur_demo_page == "patients/{id}/edit/{tab?}/{more?}")
								$img_details['class'] = 'img-border-sm margin-b-20 margin-r-20';
							else
								$img_details['class'] = 'img-border-sm  margin-r-20 margin-t-31';
							$img_details['alt'] = 'patient-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
                        ?>                        
                        {!! $image_tag !!}
                        <i class="fa fa-circle @if($patient_tabs_details_new->status == 'Active')med-green-o @else med-red @endif hidden-print @if(@$patient_tabs_details_new->avatar_name !='' && $cur_demo_page=='patients/{id}/edit/{tab?}/{more?}')  patient-status-w-delete  @else  patient-status @endif" data-placement="bottom"  data-toggle="tooltip" data-original-title="{{ $patient_tabs_details_new->status }} Patient"></i>   <!-- Dont remove this inline style -->
                    </div>                   
                </div>
				
                <p class="med-green font600 font16"> 
					{{ @$patient_tabs_details_new->last_name.', '.@$patient_tabs_details_new->first_name.' '.@$patient_tabs_details_new->middle_name }} @if($cur_demo_page!='patients/{id}/edit/{tab?}/{more?}')<a href="{{ url('patients/'.@$encodepatientid.'/edit') }}" class="hidden-print"><i class="fa fa-edit form-cursor margin-l-5" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i></a> @endif 
                </p>                                        
                <p class="margin-t-8"><span class="med-orange ">
					<span class="med-orange ">
						@if(@$patient_tabs_details_new->dob != "0000-00-00" && @$patient_tabs_details_new->dob != "1901-01-01"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_tabs_details_new->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$patient_tabs_details_new->dob) }}  - @endif @if(@$patient_tabs_details_new->gender == 'Male') M @elseif(@$patient_tabs_details_new->gender == 'Female') F @else O @endif
					</span>
                </p>
                @if(@$patient_tabs_details_new->account_no)
                <p class=" "><span class="med-green font600">Acc No : </span>{{ @$patient_tabs_details_new->account_no }}</p>
                @endif
                <p class=""><span class="med-green font600">SSN : </span> @if(@$patient_tabs_details_new->ssn) {{ @$patient_tabs_details_new->ssn }} @else - Nil  - @endif</p>

                @if(@$patient_tabs_details_new->avatar_name !="" && $cur_demo_page=="patients/{id}/edit/{tab?}/{more?}")		
                <span data-id="{{@$encodepatientid}}" class="js-delete-patient-image pat-img-delete" style=""><a href="javascript:void(0);"><i class="fa {{Config::get('cssconfigs.common.delete')}} pat-delete-icon" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></a>
                </span>
                @endif                 
            </div>
            <?php $get_data = App\Models\Patients\Patient::getPatienttabData($tabpatientid); ?>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12  tab-l-b-1  tab-t-10 tabs-lightorange-border">
                <p><span class=" med-green font600">Bill Cycle</span><span class="pull-right">{{@$patient_tabs_details_new->bill_cycle}}</span></p>             
                <p><span class=" med-green font600">Eligibility </span> <span class="eligibility-check text-bg  pull-right @if(@$get_data['eligibility'] != 'No')label-success @else label-danger @endif">{{$get_data['eligibility']}}</span></p>
                <p class="margin-t-5"><span class=" med-green font600">Budget Plan</span>
                    <span class="pull-right text-bg @if(@$get_data['patient_budget'] != 'No')label-success @else label-danger @endif">{{$get_data['patient_budget']}}</span>
                </p>
                <p><span class=" med-green font600">Unbilled</span><span class="pull-right text-bg label-warning">{!! App\Http\Helpers\Helpers::priceFormat($get_data['unbilled']) !!}</span></p>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 form-horizontal tab-l-b-2 md-display tabs-lightorange-border">
                <p class="margin-t-5"><span class=" med-green font600">Wallet Balance</span><span class="js-wallet-bal pull-right med-orange font600"> {!!App\Http\Helpers\Helpers::priceFormat($get_data['wallet_balance'])!!}</span></p>
                <p class=""><span class=" med-green font600">Insurance Balance</span><span class="pull-right font600">{!! App\Http\Helpers\Helpers::priceFormat($get_data['insurance_due'])!!}</span></p>             
                <p class=""><span class=" med-green font600">Patient Balance</span><span class="pull-right font600">{!! App\Http\Helpers\Helpers::priceFormat($get_data['patient_due'])!!}</span></p>
                <p  class=""><span class=" med-green font600">Total AR</span><span class="pull-right font600">{!! App\Http\Helpers\Helpers::priceFormat($get_data['total_ar'])!!}</span></p>
            </div>
            <?php 	
				$fdate = App\Models\Payments\ClaimInfoV1::selectRaw('MIN(DATE(date_of_service)) as date')->where('status','!=','Paid')->where('patient_id',$tabpatientid)->where('date_of_service','<>',"0000-00-00")->value('date');
				if(empty($fdate)) {
					$count = 0; 
				} else {
					$last_week_days = App\Models\Payments\ClaimInfoV1::arDays('week',$tabpatientid); 
					$datetime1 = new DateTime($fdate);
					$datetime2 = new DateTime(App\Http\Helpers\Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d'));
					$interval = $datetime1->diff($datetime2);
					$count = $interval->format('%a');//now do whatever you like with $days
				}
				$ar_days = ($count == 1 || $count == 0) ? ($count == 0 ? "0" : $count . " Day") : $count . " Days";
			?>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 tab-l-b-3 md-display tabs-lightorange-border">
                <p class="margin-t-5"><span class="med-green font600">AR Days </span> <span class="pull-right">{{ $ar_days }} </span></p>
                <p class=""><span class="med-green font600">Acc Created </span> <span class="pull-right bg-date-tab"> {{App\Http\Helpers\Helpers::DateFormat(@$patient_tabs_details_new->created_at,'date')}} </span></p>
                <p class=""><span class="med-green font600">Last Appointment </span> 
                    @if(@$get_data['last_appoinment']!='-')
                    <span class="pull-right bg-date-tab">{{ App\Http\Helpers\Helpers::dateFormat($get_data['last_appoinment'] ,'date') }}</span>
                    @else
                    <span class="pull-right">- Nil -</span>
                    @endif
                </p>
                <p class=""><span class="med-green font600">Last Statement </span> 
                    @if(@$get_data['last_statement']!='-')
                    <span class="pull-right bg-date-tab">{{App\Http\Helpers\Helpers::dateFormat($get_data['last_statement'],'date')}}</span>
                    @else
                    <span class="pull-right">- Nil -</span>
                    @endif
                </p>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<div class="bottomMenu hidden-sm hidden-xs">
    <div class="col-md-12 patient-tab-bg">
        <div class="col-lg-3 col-md-3 col-sm-3 margin-t-5">

            <div class="text-center js_patient_img_part" style="line-height:130px;">
                <?php
					$filename = @$patient_tabs_details_new->avatar_name . '.' . @$patient_tabs_details_new->avatar_ext;
					$img_details = [];
					$img_details['module_name']='patient';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="";
					
					$img_details['class']=' margin-r-20';
					$img_details['alt']='patient-image';
					$img_details['style']='display:inline; border-radius:4px; border:2px solid #ccc;float:left;margin-bottom:10px; width:50px; height:50px;';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>	
                {!! $image_tag !!}
                <i class="fa fa-circle @if($patient_tabs_details_new->status == 'Active')med-green-o @else med-red @endif hidden-print patient-status-scrolltab"  data-placement="bottom"  data-toggle="tooltip" data-original-title="{{ $patient_tabs_details_new->status }} Patient" style=""></i>   <!-- Dont remove this inline style -->
            </div>

            <h3 class="font14 margin-t-2">{{ @$patient_tabs_details_new->last_name.', '.@$patient_tabs_details_new->first_name.' '.@$patient_tabs_details_new->middle_name }} </h3>
            <span class="med-orange">
                @if(@$patient_tabs_details_new->dob != "0000-00-00" && @$patient_tabs_details_new->dob != "1901-01-01"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_tabs_details_new->dob,'dob').", "}} {{ App\Http\Helpers\Helpers::dob_age(@$patient_tabs_details_new->dob) }} - @endif {{ @$patient_tabs_details_new->gender }} 
            </span> 
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 med-ts-separator">           
            @if(@$patient_tabs_details_new->account_no)<p class="no-bottom margin-t-5"><span class="med-green">Acc No </span><span class="pull-right med-orange">{{ $patient_tabs_details_new->account_no }}</span></p>@endif
            <p class="no-bottom margin-t-5"><span class="med-green">Unbilled  </span> <span class="pull-right">{!! App\Http\Helpers\Helpers::priceFormat($get_data['unbilled'])!!}</span> </p>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3">
            <p class="no-bottom margin-t-5"><span class="med-green">Patient Bal  </span> <span class="pull-right">{!! App\Http\Helpers\Helpers::priceFormat($get_data['patient_due'])!!}</span> </p>
            <p class="no-bottom margin-t-5"><span class="med-green">Insurance Bal  </span> <span class="pull-right">{!! App\Http\Helpers\Helpers::priceFormat($get_data['insurance_due'])!!}</span> </p>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3  hidden-sm" style="border-left: 1px dashed #ccc;">
            <p class="no-bottom margin-t-5"><span class="med-green ">Wallet Bal </span> <span class="pull-right js-wallet-bal-header">{!! App\Http\Helpers\Helpers::priceFormat($get_data['wallet_balance'])!!}</span> </p>
            <p class="no-bottom margin-t-5"><span class="med-green">Total AR </span> <span class="pull-right">{!! App\Http\Helpers\Helpers::priceFormat($get_data['total_ar'])!!}</span></p>
        </div>
    </div>
</div>

