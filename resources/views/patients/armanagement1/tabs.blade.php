<div class="col-md-12 margin-t-m-20">
    <div class="box box-info no-shadow tabs-border">
        <div class="box-body tabs-bg">


            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 p-tabs-border-color sm-b-border m-b-m-8">
               @if(@$needdecode=='yes')
					<?php $tabpatientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$tabpatientid,'decode'); ?>
				@endif
				<?php
					$patient_tabs_api_response 			= App\Http\Controllers\Patients\Api\PatientApiController::getPatientTabsDetails(@$tabpatientid);
					$patient_tabs_api_res_data 			= $patient_tabs_api_response->getData();
					$patient_tabs_details_new			= $patient_tabs_api_res_data->data->patients;
					$patient_tabs_insurance_count_new	= $patient_tabs_api_res_data->data->patient_insurance_count;
					$patient_tabs_insurance_details_new	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true);
					$filename = @$patient_tabs_details_new->avatar_name.'.'.@$patient_tabs_details_new->avatar_ext;
					$img_details = [];
					$img_details['module_name']='patient';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="";
					
					$img_details['class']='img-border-sm  margin-r-20';
					$img_details['alt']='patient-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
				{!! $image_tag !!} 

                <h5 class="med-green no-margin">Andrew, Russell  </h5>
                <h5 class=""><span class="med-orange sm-size"><i class="med-orange med-gender fa fa-male margin-r-5 "></i> Feb 12,1975, 41 years</span> </h5>
                
                <h5 class="space-m-t-0"><span class="med-green">Acc No: </span>45346</h5>
                <h5 class="space-m-t-0"><span class="med-green">SSN No: </span>634325673</h5>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 med-ts-separator  no-bottom p-tabs-border-color sm-display m-b-m-8" >

                @if(@$patient_tabs_details_new->is_self_pay=='No')
                <p class="margin-t-m-3"><span class="font600 pri-color">Pri</span>&nbsp;&nbsp;  :
                    @if(@$patient_tabs_insurance_count_new>0 && !empty(@$patient_tabs_insurance_details_new['Primary'])){{ str_limit(@$patient_tabs_insurance_details_new['Primary']['insurance_details']['insurance_name'],40,'...') }} - <span class="med-orange">$145</span> @else <span class="margin-l-5"> - Nil -</span> @endif</p>
                <p class="space-m-t-7"><span class="font600 sec-color">Sec</span>&nbsp; :
                    @if(@$patient_tabs_insurance_count_new>0 && !empty(@$patient_tabs_insurance_details_new['Secondary'])){{ str_limit(@$patient_tabs_insurance_details_new['Secondary']['insurance_details']['insurance_name'],40,'...')}} - <span class="med-orange">$20</span> @else <span class="margin-l-5"> - Nil -</span> @endif</p>
                @else
                <p class="margin-t-m-3"><span class="font600 pri-color">Self Pay</span></p>
                @endif

                <p class="space-m-t-7"><span class="font600 ter-color">Patient Due</span>&nbsp; : <span class="med-orange">$20</span></p>
                <p  class="space-m-t-7"><span class="font600 gua-color">AR Due</span>&nbsp; : <span class="med-orange">$20</span></p>
            </div>
                   
                
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 m-b-m-8 p-tabs-border-color sm-display">
                <h5 class="no-margin"><span class="med-green">Ins Due </span><span class="pull-right font12">$ 354.30</span></h5>
                <h5><span class="med-green">Pat Due </span> <span class=" pull-right font12">$ 0.00</span></h5>
                <h5><span class="med-green">AR Due</span> <span class=" pull-right font12">$ 354.30</span></h5>
                <h5><span class="med-green">AR Days </span> <span class="pull-right font12">+120</span></h5>
                
            </div>
                
                
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 no-bottom med-left-border m-b-m-8 p-tabs-border-color sm-display">
                <h5 class="no-margin"><span class="med-green">Status </span><span class="pull-right font12 status-active">Active</span></h5>
                <h5><span class="med-green">Last Appt </span> <span class=" pull-right font12">Jan 29, 2015</span></h5>
                <h5><span class="med-green">Statement Sent</span> <span class=" pull-right font12">2</span></h5>
                <h5><span class="med-green">Last Statement </span> <span class="pull-right font12">Feb 16, 2015</span></h5>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>    