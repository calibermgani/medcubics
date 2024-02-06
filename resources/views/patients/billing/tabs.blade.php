<div class="col-md-12 margin-t-m-20 print-m-t-30">

    <div class="box box-info no-shadow l-orange-b">
        <div class="box-body tabs-orange-bg">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b p-r-0">
                <div class="text-center"> </div>   
                <?php
	                if (!empty($tabpatientid) && isset($tab) && $tab == "payment") {
	                    $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$tabpatientid, 'decode');
	                    $patients = App\Models\Patients\Patient::where('id', $patient_id)->first();
	                }
					$encodepatientid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patients->id);
					$filename = $patients->avatar_name.'.'.$patients->avatar_ext;
					$img_details = [];
					$img_details['module_name']='patient';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="";
				
					$img_details['class']='img-border-sm  margin-r-20 margin-t-31';
					$img_details['alt']='patient-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
      			?>
                {!! $image_tag !!}
                <p class="med-green font600"> {{ App\Http\Helpers\Helpers::getNameformat($patients->last_name, $patients->first_name, $patients->middle_name)}} <a href="{{ url('patients/'.@$encodepatientid.'/edit') }}" class="hidden-print"><i class="fa fa-edit form-cursor margin-l-10" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Patient"></i></a></p>   
                <p class="margin-t-8"><span class="med-orange ">
                    @if(@$patients->dob != "0000-00-00" && @$patients->dob != "1901-01-01" && @$patients->dob != ""){{ App\Http\Helpers\Helpers::dateFormat(@$patients->dob,'dob').", "}}{{ App\Http\Helpers\Helpers::dob_age(@$patients->dob)}} -  @endif @if(@$patients->gender == 'Male') M @elseif(@$patients->gender == 'Female') F @else O @endif</span></p>
                <p><span class="med-green">Acc No : </span>@if(@$patients->account_no) {{ @$patients->account_no }} @else - Nil - @endif</p>

                <p><span class="med-green">SSN : </span> @if(@$patients->ssn) {{ @$patients->ssn }} @else - Nil  - @endif</p>     
                <i class="fa fa-circle @if($patients->status == 'Active')med-green-o @else med-red @endif hidden-print patient-status"  data-placement="bottom"  data-toggle="tooltip" data-original-title="{{ $patients->status }} Patient" style=""></i>   <!-- Dont remove this inline style -->
            </div>
            <?php $get_data = App\Models\Patients\Patient::getPatienttabData($tabpatientid); ?>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12  tab-l-b-1  tab-t-10 tabs-lightorange-border">
                <p><span class=" med-green">Bill Cycle</span><span class="pull-right">{{@$patients->bill_cycle}}</span></p>
                <p><span class=" med-green">Eligibility </span> <span class="pull-right text-bg @if(@$get_data['eligibility'] == 'Yes')label-success @else label-danger @endif">{{$get_data['eligibility']}}</span></p>
                <p class="margin-t-5"><span class=" med-green">Budget Plan</span><span class="pull-right text-bg @if(@$get_data['patient_budget'] == 'Yes')label-success @else label-danger @endif">{{$get_data['patient_budget']}}</span></p>
                <p><span class=" med-green">Unbilled</span><span class="pull-right text-bg label-warning">{!!App\Http\Helpers\Helpers::priceFormat($get_data['unbilled'])!!}</span></p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 form-horizontal tab-l-b-2 md-display tabs-lightorange-border">              
                <p class="margin-t-5"><span class=" med-green">Wallet Balance</span><span class="pull-right med-orange font600"> {!!App\Http\Helpers\Helpers::priceFormat($get_data['wallet_balance'])!!}</span></p>
                <p class=""><span class=" med-green">Insurance Balance</span><span class="pull-right font600">{!!App\Http\Helpers\Helpers::priceFormat($get_data['insurance_due'])!!}</span></p>             
                <p class=""><span class=" med-green">Patient Balance</span><span class="pull-right font600">{!!App\Http\Helpers\Helpers::priceFormat($get_data['patient_due'])!!}</span></p>
                <p  class=""><span class=" med-green">Total AR</span><span class="pull-right font600">{!!App\Http\Helpers\Helpers::priceFormat($get_data['total_ar'])!!}</span></p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12  tab-l-b-3 md-display tabs-lightorange-border">
                <p class="margin-t-5"><span class="med-green">Claim No </span> <span class="pull-right"> 
                @if(isset($claims->claim_number) &&!empty($claims->claim_number)) {{$claims->claim_number}} @else - Nil - @endif </span></p>
                <p class=""><span class="med-green">Entry Date </span> <span class="pull-right bg-date-tab"> {{ empty($claims)? App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y'):App\Http\Helpers\Helpers::dateFormat(@$claims->created_at,'date') }} </span></p>
                <p class=""><span class="med-green">First Submission </span> 
                    <?php $submitted_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->submited_date, '', '', 'm/d/y'); ?>
                    <span class="pull-right @if(!empty($claims) && $submitted_date != '') bg-date-tab @endif"> {{ (!empty($claims) && $submitted_date != "")? $submitted_date :"- Nil - " }} </span>
                </p>

                <p class=""><span class="med-green">Claim Status </span> <span class="pull-right">{{ $claims->status or '- Nil -'}}</span></p>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
    <!-- Sub Menu -->
    <?php 
		$id = Route::getCurrentRoute()->parameter('id');
		$activetab = 'create_claim'; 
        $routex = explode('.',Route::currentRouteName());
        
		if ((!empty($patient_id) || !empty($id)) && !Request::ajax() && !isset($fromcharge)) {
			$list_url = url('patients/' . $patient_id . '/billing');
			$create_url = url('patients/' . $id . '/billing/create');
		} else if (Request::ajax() || isset($fromcharge)) {
			$list_url = url('charges');
			$create_url = url('charges/create');
		}
    ?> 	
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
	<div class="med-tab nav-tabs-custom no-bottom margin-t-m-10">
		<ul class="nav nav-tabs">
			@if(empty($tabpatientid) || (!isset($tab))) 
			@if($checkpermission->check_url_permission('scheduler/scheduler') == 1 && !isset($fromcharge))
			@if($checkpermission->check_url_permission('patients/{id}/appointments') == 1)
			<li class="@if($activetab == 'patientappointments') active @endif"><a href="{{ url('patients/'.$id.'/appointments') }}" ><i class="fa fa-bars i-font-tabs"></i> Appo<span class="text-underline">i</span>ntments</a></li>
			@endif
			@endif
			@if(!Request::ajax() && !isset($fromcharge))           
			<li class="@if($activetab == 'charges_list') active @endif"><a href="{{ $list_url }}" class="js_next_process" accesskey="m"><i class="fa fa-bars i-font-tabs"></i> Clai<span class="text-underline">m</span>s</a></li>
			@endif                  	                      	
			<li class="@if($activetab == 'create_claim') active @endif"><a href="javascript::void(0)" class="js_next_process"><i class="fa {{Config::get('cssconfigs.charges.charges')}} i-font-tabs"></i> @if(empty(@$claims))Create @else Edit @endif Claim</a>
			</li>
			@endif
			
			
			@if((Request::segment(1) == 'charges' || Request::segment(3) == 'billing') && (Request::segment(3) != 'charge_edit') && (Request::segment(4) != 'edit') && (Request::segment(5) == ''))
			<li class="pull-right p-t-2">
			   <?php $backDate =  App\Http\Helpers\Helpers::getBackDate(); ?>
				@if($backDate == 'Yes')
				<label for="backDate" class="control-label-billing  med-green font600">Created Date</label>
				{!! Form::text('backDate',App\Http\Helpers\Helpers::timezone(date('m/d/y H:i:s'),'m/d/Y'),['id'=>'backDate','class'=>'form-control input-sm-header-billing dm-date','style'=>'width:120px; display:inline-block;','readonly'=>'readonly']) !!}  
				@else				
				<span class="font600">Created Date :</span> <span class="med-orange font600">{{ App\Http\Helpers\Helpers::timezone(date('m/d/y H:i:s'),'m/d/y')}}</span>  
				@endif
			</li>
			@endif
		</ul>   
	</div> 	
</div>