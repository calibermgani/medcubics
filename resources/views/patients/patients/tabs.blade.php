<div class="col-md-12" style="margin-top:-10px;">
    <div class="box box-info no-shadow" style="border: 1px solid #85E2E6">
        <div class="box-body" style="padding-bottom: 20px;">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 tab-border-bottom">
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12 p-l-0">
                    <div class="text-center">
                       <?php
							$filename = $patients->avatar_name.'.'.$patients->avatar_ext;
							$img_details = [];
							$img_details['module_name']='patient';
							$img_details['file_name']=$filename;
							$img_details['practice_name']="";

							$img_details['class']='img-border margin-t-5';
							$img_details['alt']='patient-image';
							$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
						?>
						{!! $image_tag !!}
                   </div>
               </div>
                <h3 style=" font-size: 18px;">
				<?php $patient_name = App\Http\Helpers\Helpers::getNameformat("$patients->last_name","$patients->first_name","$patients->middle_name"); ?>
				{{ $patient_name }}
				 <span class="med-orange sm-size"><i class="med-orange med-gender fa @if($patients->gender == 'Male') fa-male @else fa-female @endif margin-r-5 "></i> @if($patients->dob != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat($patients->dob,'dob').", ".$patients->age }}&nbsp; Yrs @else @endif </span>

                @if(@$patients->ssn)<h5><span class="med-green">SSN: </span>{{ $patients->ssn }}</h5>@endif
                @if(@$patients->account_no)<h5><span class="med-green">Acc No: </span>{{ $patients->account_no }}</h5>@endif
            </div>
            <?php $insurance = (array)@$tab_insurance; ?>
            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12  med-ts-separator">
                <h5><span style="color:#D98400">Pri</span>&nbsp;  -
                    @if(!empty($patients_insurance['Primary'])){{ str_limit($patients_insurance['Primary'],35,'...') }} - <span class="med-orange">$145</span>@endif</h5>
                <h5><span style="color:#798C01">Sec</span>&nbsp; -
                    @if(!empty($patients_insurance['Secondary'])){{ str_limit($patients_insurance['Secondary'],35,'...')}} - <span class="med-orange">$145</span>@endif</h5>
                <h5><span style="color:#0572A1">Ter</span>&nbsp; - @if(!empty($patients_insurance['Tertiary'])) {{ str_limit($patients_insurance['Tertiary'],35,'...')}} - <span class="med-orange">$145</span>@endif</h5>
                <h5><span style="color:#720294">Gau</span>&nbsp; -
                    @if(!empty($patients->age) && $patients->age < 18)
                         {{ $patients->guarantor_last_name.', '.$patients->guarantor_first_name.' '.$patients->guarantor_middle_name }}
                    @else
                        {{ $patients->last_name.', '.$patients->first_name.' '.$patients->middle_name }}
                    @endif
                </h5>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <h5><span class="med-green">Status </span><span class="patient-status-bg pull-right @if($patients->status == 'Active')label-success @else label-danger @endif">{{ $patients->status }}</span></h5>
                <h5><span class="med-green">AR Days </span> <span class="patient-status-bg pull-right label-success"> &nbsp;120 +</span></h5>
                <h5><span class="med-green">Last Appt </span> <span class="patient-status-bg pull-right label-success">Jan 29, 2015</span></h5>
                <h5><span class="med-green">Last Statement </span> <span class="patient-status-bg pull-right label-success">Feb 16, 2015</span></h5>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<div class="bottomMenu hidden-sm hidden-xs">
    <div class="col-md-12" style="background: #FAFAFA;">
        <div class="col-lg-3 col-md-3 col-sm-3" style="margin-top: 5px;">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="text-center" style="line-height:130px;">
					<?php
						$filename = $patients->avatar_name.'.'.$patients->avatar_ext;
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
			   </div>
		   </div>
            <h3 style=" font-size: 18px;">{{ $patients->last_name.', '.$patients->first_name.' '.$patients->middle_name }} </h3>
            <span class="med-orange sm-size"><i class="med-orange med-gender fa @if($patients->gender == 'Male') fa-male @else fa-female @endif margin-r-5 "></i> @if($patients->dob != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat($patients->dob,'dob').", ".$patients->age }}&nbsp; Yrs @else @endif </span>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 med-ts-separator" style="margin-top: 5px;">
            @if(@$patients->ssn)<h5><span class="med-green">SSN: </span>{{ $patients->ssn }}</h5>@endif
            @if(@$patients->account_no)<h5><span class="med-green">Acc No: </span>{{ $patients->account_no }}</h5>@endif
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3" style="margin-top: 5px;">
		  <h5><span style="background: #F9EFD3; padding: 2px 10px; color:#D98400">Pri</span>&nbsp;  -
					@if(!empty($insurance['Primary'])){{$insurance['Primary']->insurancename}} - <span class="med-orange">$145</span>@endif</h5>
                 <h5><span style="background: #F2F9C8; padding: 2px 10px; color:#798C01">Sec</span>&nbsp; -
				 @if(!empty($insurance['Secondary'])){{$insurance['Secondary']->insurancename}} - <span class="med-orange">$145</span>@endif</h5>
        </div>
        <div class="col-lg-3 col-md-3  hidden-sm" style="margin-top: 5px; border-left: 1px dashed #ccc;">
            <h5><span class="med-green">Status </span><span class="patient-status-bg  @if($patients->status == 'Active')label-success @else label-danger @endif">{{ $patients->status }}</span></h5>
            <h5><span class="med-green">Last Statement </span> <span class="patient-status-bg  label-success">Feb 16, 2015</span></h5>
        </div>
    </div>
</div>