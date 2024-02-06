<div class="col-md-12 margin-t-m-20">
    <div class="box box-info no-shadow" style="border: 1px solid #8ce5bb">
        <div class="box-body" style="background: #e5faf0; border-radius: 4px;">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b m-b-m-8">               
                <?php
					$filename = @$patient_detail->avatar_name.'.'.@$patient_detail->avatar_ext;
					$img_details = [];
					$img_details['module_name']='insurance';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="patient";
					
					$img_details['class']='img-border-sm  margin-r-20';
					$img_details['alt']='patient-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
                {!! $image_tag !!}
                <h5 class="med-green no-margin">{{ @$patient_detail->last_name.' '.@$patient_detail->first_name.' '.@$patient_detail->middle_name }} </h5>
                <p style="margin-top:5px;"><span class="med-orange sm-size"><i class="med-orange med-gender fa @if(@$patients->gender == 'Male') fa-male @else fa-female @endif margin-r-5 "></i> @if(@$patient_detail->dob != "0000-00-00"){{ App\Http\Helpers\Helpers::dateFormat(@$patient_detail->dob,'dob').", ".@$patient_detail->age }}&nbsp;Years @else @endif </span></p>

                <p class="space-m-t-7 "><span class="med-green font600">Acct No : </span>ACC20161</p>

                <p class="space-m-t-7"><span class="med-green font600">SSN : </span> @if(@$patient_detail->ssn) {{ @$patient_detail->ssn }} @else - Nil  - @endif</p>     

            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12  tab-l-b-1 m-b-m-8 tab-t-10">
                <p style="margin-top:-2px;"><span class="font600 med-green">Claim Number</span> &nbsp;&nbsp;  :
                <span class="bg-number">{{@$claims_list->claim_number}}</span></p>
                <p  class="space-m-t-7"><span class="font600 med-green">Billed To</span> &nbsp; &nbsp; &nbsp; &nbsp; : 
                {!!App\Http\Helpers\Helpers::getInsuranceName(@$claims_list->insurance_details->id) !!}
                </p>
                <p class="space-m-t-7"><span class="font600 med-green">Billing Prov</span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; :
                {{str_limit(@$claims_list->billing_provider->provider_name,25,'...')}}</p>

                <p  class="space-m-t-7"><span class="font600 med-green">Rendering Prov</span>&nbsp; :
                {{str_limit(@$claims_list->rendering_provider->provider_name,25,'...')}}
                </p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 form-horizontal tab-l-b-2 md-display" style="margin-bottom: -10px;">
                <p style="margin-top:-2px;"><span class="font600 med-green">Facility</span> &nbsp;&nbsp;  :
                {{str_limit(@$claims_list->facility_detail->facility_name,25,'...')}}</p>
                <p class="space-m-t-7"><span class="font600 med-green">Referring Prov</span>&nbsp; :
                {{str_limit(@$claims_list->refering_provider->provider_name,25,'...')}}</p>
                <p  class="space-m-t-7"><span class="font600 med-green">Auth No</span>&nbsp; &nbsp;: 
                547456846</p>
                <p  class="space-m-t-7"><span class="font600 med-green">DOI</span>&nbsp; :  
                <span class="bg-date">Feb 16, 2015{{@date(M, d, y, strtotime($claims_list->doi))}}</span>
                </p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 m-b-m-8  tab-l-b-3 md-display">
                <p style="margin-top:-2px;"><span class="med-green font600">Status </span><span class="pull-right font600" style="color:#2698F8">Partially Paid</span></p>
                <p class="space-m-t-7"><span class="med-green font600">Claim Type </span> <span class="pull-right font12 font600"><i class="fa fa-tv"></i> Electronic </span></p>
                <p class="space-m-t-7"><span class="med-green font600">Submitted Date </span> <span class="pull-right font12 font600 bg-date">Jan 29, 2015</span></p>
                <p class="margin-t-m-5"><span class="med-green font600">Last Submitted Date </span> <span class="pull-right font12 font600 bg-date">Feb 16, 2015</span></p>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>