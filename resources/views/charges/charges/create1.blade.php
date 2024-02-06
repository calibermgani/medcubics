<div class="js-replace-patient-info">
    <div class="col-md-12 margin-t-m-10">
        <div class="box box-info no-shadow tabs-border">
            <div class="box-body tabs-bg">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b m-b-m-8">
                    <div class="text-center"><?php $filename = $patients->avatar_name.'.'.$patients->avatar_ext;$avatar_url = App\Http\Helpers\Helpers::checkAndGetAvatar('patient',$filename);?></div>
                     {!! HTML::image($avatar_url,null,['class'=>' img-border-sm  margin-r-20']) !!}
                        <h5 class="med-green no-margin">{{ @$patients->first_name  }}, {{ @$patients->last_name  }}  </h5>
                        <p style="margin-top:5px;"><span class="med-orange sm-size"><i class="med-orange med-gender fa @if($patients->gender == 'Male') fa-male @else fa-female @endif margin-r-5 "></i> @if($patients->dob != "0000-00-00" && $patients->dob != "1970-01-01"){{ App\Http\Helpers\Helpers::dateFormat($patients->dob,'date').", ".$patients->age }}&nbsp;Years @else @endif </span>  </p>
                        <p class="space-m-t-7 "><span class="med-green font600">Acc No :</span> {{ @$patients->account_no }}</p>
                        <p class="space-m-t-7"><span class="med-green font600">SSN No : </span>@if(@$patients->ssn){{ @$patients->ssn }} @else  - Nil - @endif</p>
                </div>
                <?php $tab_insurance = App\Models\Patients\PatientInsurance::gettabinsurance($patients->id); ?>
                <?php $insurance = (array) $tab_insurance; ?>              
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12  tab-l-b-1 m-b-m-8 tab-t-10">
                    <p style="margin-top:-2px;"><span class="font600" style="color:#D98400">Pri</span>&nbsp;  :
                        @if(!empty($insurance['Primary'])){{@$insurance['Primary']['insurancename']}} - <span class="med-orange">$145</span>@endif</p>
                    <p class="space-m-t-7"><span class="font600" style="color:#798C01">Sec</span>&nbsp; :
                        @if(!empty($insurance['Secondary'])){{@$insurance['Secondary']['insurancename']}} - <span class="med-orange">$145</span>@endif</p>
                    <p  class="space-m-t-7"><span class="font600" style="color:#0572A1">Ter</span>&nbsp; : @if(!empty($insurance['Tertiary'])){{@$insurance['Tertiary']['insurancename']}} - <span class="med-orange">$145</span>@endif</p>
                    <p  class="space-m-t-7"><span class="font600" style="color:#720294">Gua</span>&nbsp; :  
                        @if(!empty($patients->age) && $patients->age < 18)
                        {{ @$patients->guarantor_last_name.', '.@$patients->guarantor_first_name.' '.@$patients->guarantor_middle_name }}
                        @else
                        {{ @$patients->last_name.', '.@$patients->first_name.' '.@$patients->middle_name }}
                        @endif
                    </p>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 tab-l-b-2 md-display" style="margin-bottom: -10px;">
                    <p style="margin-top:-2px;"><span class="med-green font600">Status </span><span class="pull-right @if(@$patients->status == 'Active')med-green-o @else med-red @endif">{{ @$patients->status }}</span></p>
                    <p class="space-m-t-7"><span class="med-green font600">AR Days </span> <span class="pull-right font12"> 120 + </span></p>
                    <p class="space-m-t-7"><span class="med-green font600">Last Appt </span> <span class="pull-right font12">Jan 29, 2015</span></p>
                    <p class="space-m-t-7"><span class="med-green font600">Last Statement </span> <span class="pull-right font12">Feb 16, 2015</span></p>
                </div>
                 <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 m-b-m-8  tab-l-b-3 md-display">
                    <h5 class="no-margin"><a data-toggle="modal" href="" data-target="#all_claim_details" style="text-decoration: underline">Claims </a></h5>
                    <h5><a data-toggle="modal" href="" data-target="#all_claim_details" style="text-decoration: underline">Payments </a></h5>
                    <h5><a data-toggle="modal" href="" data-target="#all_claim_details" style="text-decoration: underline">Transactions </a></h5>
                    <h5><a data-toggle="modal" href="" data-target="#all_claim_details" style="text-decoration: underline">Batch Details </a></h5>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
@include ('patients/billing/billing_create')