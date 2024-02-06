@if(empty($patient_id))
<?php $patient_id = Route::getCurrentRoute()->parameter('id'); ?>
@endif 
<?php
	$patient_id = Route::getCurrentRoute()->parameter('id');
	$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->id,'encode');
	$statelicense = isset($claims->refering_provider_id) ? App\Models\Provider::checkstatelicense($claims->refering_provider_id) : '';
	$statelicense_billing = isset($claims->billing_provider_id) ? App\Models\Provider::checkstatelicense($claims->billing_provider_id) : '';
	$patient_id = is_null($patient_id) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->patient_id, 'encode') : $patient_id;
	$encodePatientId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
	$practice_details = App\Models\Practice::getPracticeIcdInfo();
	
?>
{!!Form::hidden('claim_detail_id',@$claims->claim_details->id,['class' => 'claimdetail'])!!}
{!!Form::hidden('claim_other_detail_id',@$claims->claim_detail_id,['class' => 'claimotherdetail'])!!}
{!!Form::hidden('ambulance_billing_id',@$claims->claim_detail_id,['class' => 'claimbilling'])!!}
{!!Form::hidden('claim_id',@$claim_id, ['id' => 'js-claim-id'])!!}
{!!Form::hidden('providertypeid',@$claims->refering_provider->provider_types_id,['id' => 'providertypeid'])!!}
{!!Form::hidden('statelicence',@$statelicense, ['id' => 'statelicence'])!!}
{!!Form::hidden('statelicence_billing',@$statelicense_billing, ['id' => 'statelicence_billing'])!!}
{!!Form::hidden('taxanomy',@$claims->billing_provider->taxanomy->code, ['id' => 'providertaxanomy'])!!}
{!!Form::hidden('upinno',@$claims->refering_provider->upin, ['id' => 'upin_no'])!!}
{!!Form::hidden('icd_autopopulate',@$practice_details['icd_autopopulate'])!!}
<?php @$patients->age = date_diff(date_create(@$patients->dob), date_create('today'))->y; ?>
{!!Form::hidden('patient_age',@$patients->age)!!}
<?php 
if (!empty($claims->claim_ids)) {
    $claims_count = explode(',', $claims->claim_ids);
    if (count($claims_count) > 1) {
        $key = array_search($claims->id, $claims_count);
        if ($key + 1 <= count($claims_count)) {
            ?>
            {!!Form::hidden('next_id',@$claims_count[$key+1])!!}
            <?php
        }
    }
}
 if(!isset($get_default_timezone)){
    $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
}
?>  
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->       
    <!-- For batch process from E-superbill batch-->
    <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.demographics") }}' />
	<?php $backDate =  App\Http\Helpers\Helpers::getBackDate(); ?>
    @if($backDate == 'Yes')
		<input type="hidden" name="backDate"  value="{{ date('m/d/Y') }}"  id="form_backDate" />
	@endif
		
    <div class="box-body-block print-m-t-m-15"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding margin-t-10 border-green"><!-- General Details Full width Starts -->
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 tab-r-b-1 no-padding border-green" style="display: table;"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="font600 bg-white padding-0-4">General Details</span>
                        </div>
                        <span id="ajax-charge-loader"></span>
                        <div class="box-body form-horizontal margin-t-5"><!-- Box Body Starts -->
                            <div class="form-group form-group-billing">
                                {!!Form::hidden('patient_id',$patient_id)!!}
                                {!!Form::hidden('jsqueryvalue',null, ['id'=>'jsquery'])!!}
                                @if(empty($claims))
									{!!Form::hidden('charge_add_type','billing')!!}
                                @endif

                                {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600 star p-r-0','id'=>'demo']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('rendering_provider_id')) error @endif">
									<?php 
										/***
											   here rendering provider split used for Scheduler module 
										****/
										if(isset($rendering_provider_id) && $rendering_provider_id != "") 
											$ren_provider =App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$rendering_provider_id,'decode'); 
										else 
										   $ren_provider= @$claims->rendering_provider_id;
									?>    
                                    
                                    {!! Form::select('rendering_provider_id', array('' => '-- Select --') + (array)$rendering_providers, @$ren_provider,['class'=>'select2 form-control', 'id' => 'providerpop', 'onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');']) !!}  
                                    {!! $errors->first('rendering_provider_id', '<p> :message</p>')  !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu js-dropdown @if(!empty($claims->rendering_provider))notempty @endif">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#" tabindex = -1>
                                            <span class="hidden-xs">
                                                <i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <?php
											$render_provider = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->rendering_provider_id, 'encode');
											$billing_provider = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->billing_provider_id, 'encode');
											$refer_provider = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->refering_provider_id, 'encode');
											$facility = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->facility_id, 'encode');
											$pos_id = @$claims->pos_id;
                                        ?>
                                        <div class="dropdown-menu1">
                                            <ul id="providerpop">                                                
                                                @if(!(empty($claims->rendering_provider)))													
                                                <li><span>Name</span> : {{@$claims->rendering_provider->provider_name ." ".@$claims->rendering_provider->degrees->degree_name  }}</li>
                                                <li><span>NPI</span> : {{@$claims->rendering_provider->npi}}</li>
                                                <li><span>Provider Type</span> : {{@$claims->rendering_provider->provider_types->name}}</li>
													@if(Auth::user()->practice_user_type == 'customer' || Auth::user()->practice_user_type == 'practice_admin')
														<li><a data-title='More Info' target= "_blank" href = "{{url('provider/'.@$render_provider)}}">
														<i class='fa {{Config::get('cssconfigs.common.info')}}' data-placement='bottom' data-toggle='tooltip' data-original-title='More Details'></i></a></li>
													@endif
                                                @endif  
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>       
							<?php
							
							$referringInfo = App\Models\Provider::getPatientReferringProvider($encodePatientId);
							$referringProviderId = (isset($claims)) ? $claims->refering_provider_id : $referringInfo['id'];
							$referringProviderName = (isset($claims)) ? @$claims->refering_provider->provider_name ." ".@$claims->refering_provider->degrees->degree_name : $referringInfo['name'];
							?>
                            <div class="form-group form-group-billing">
                                {!! Form::label('', 'Referring Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600','id'=>'ref_label']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 billing-select2 @if($errors->first('referring_provider_id')) error @endif">									
                                    {!! Form::text('refering_provider', @$referringProviderName,['class'=>'form-control input-sm-header-billing js-remove-err autocomplete-ajax', 'id' => 'js-refer-provider','data-url' => 'api/getreferringprovider/provider']) !!} 
                                    {!! Form::hidden('refering_provider_id', $referringProviderId, ['id' => 'refering_provider_id']) !!}
                                    {!! $errors->first('referring_provider_id', '<p> :message</p>')  !!}
                                    <span style='display:none;'><small class='help-block med-orange' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>{{ trans("practice/patients/charges.validation.valid_provider") }}</small></span>
                                </div>

                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                    <!-- Popup Starts -->                                   
                                    <a href="#" data-toggle="modal" data-target="#js-model-popup" data-url="{{url('/patients/referingprovider/provider')}}" class="hidden-print" tabindex=-1>
                                        <i class="fa {{Config::get('cssconfigs.common.plus_circle')}} med-orange form-icon-billing margin-l-5"></i></a>
                                    <div class="dropdown user user-menu js-dropdown @if(!empty($claims->refering_provider))notempty @endif">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
                                            <span class="hidden-xs">
                                                <i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i>
                                            </span>
                                        </a>                                      
                                        <div class="dropdown-menu1">
                                            <ul id ="js-refer-provider">                                                
                                                @if(!(empty($claims->refering_provider)))
                                                <li><span>Name</span> : {{@$claims->refering_provider->provider_name ." ".@$claims->refering_provider->degrees->degree_name }}</li>
                                                <li><span>NPI</span> : {{$claims->refering_provider->npi}}</li>
                                                <li><span>Provider Type</span> : {{@$claims->refering_provider->provider_types->name}}</li>
													@if(Auth::user()->practice_user_type == 'customer' || Auth::user()->practice_user_type == 'practice_admin')
													<li><a target= "_blank" href = "{{url('/').'/provider/'.@$refer_provider}}" data-title='More Info'><i class='fa {{Config::get('cssconfigs.common.info')}}' data-placement='bottom' data-toggle='tooltip' data-original-title='More Details'></i></a></li>
													@endif                                                
                                                @endif                                                
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>                           
                            <div class="form-group form-group-billing">
                                {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600 star']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('billing_provider_id')) error @endif">
                                    {!! Form::select('billing_provider_id', array('' => '-- Select --') + (array)$billing_providers,  @$claims->billing_provider_id,['class'=>'select2 form-control','id' => 'billingprovider-pop','onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');']) !!}  
                                    {!! $errors->first('billing_provider_id', '<p> :message</p>') !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">

                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu js-dropdown @if(!empty($claims->billing_provider))notempty @endif">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#" tabindex = -1>
                                            <span class="hidden-xs">
                                                <i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul id="billingprovider-pop">
                                                @if(!(empty($claims->billing_provider)))
                                                <li><span>Name</span> : {{@$claims->billing_provider->provider_name ." ".@$claims->billing_provider->degrees->degree_name }}</li>
                                                <li><span>NPI</span> : {{$claims->billing_provider->npi}}</li>
                                                <li><span>Provider Type</span> : {{@$claims->billing_provider->provider_types->name}}</li>
													@if(Auth::user()->practice_user_type == 'customer' || Auth::user()->practice_user_type == 'practice_admin')
													<li><a target= "_blank" href = "{{url('/').'/provider/'.@$billing_provider}}" data-title='More Info'><i class='fa {{Config::get('cssconfigs.common.info')}}' data-placement='bottom' data-toggle='tooltip' data-original-title='More Details'></i></a></li>
													@endif     
                                                @endif     
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->                            
                                </div>
                            </div>

                            <div class="form-group form-group-billing">
                                <?php 
									/***
                                       here rendering Facility split used for Scheduler module 
									****/
                                    if(isset($facility_id) && $facility_id != "") 
                                       $facility_id =App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$facility_id,'decode'); 
                                    else 
                                       $facility_id= @$claims->facility_id;                                    
                                ?>   
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600 star']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('facility_id')) error @endif">  
                                    {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities,  @$facility_id,['class'=>'select2 form-control','id'=>'facility_id', 'onChange' => 'changeselectval(this.value,\'Facility\', \'\');']) !!}   
                                    {!! Form::hidden('facility_clai_no')!!}
                                    {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                                </div>      
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu js-dropdown @if(!empty($claims->facility_detail))notempty @endif">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#" tabindex = -1>
                                            <span class="hidden-xs">
                                                <i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul class="js-facility-detail">                                               
                                                @if(!(empty($claims->facility_detail)))
                                                <li><span>Facility</span> : {{$claims->facility_detail->facility_name}}</li>
                                                <li>{{$claims->facility_detail->facility_address->address1.', '.$claims->facility_detail->facility_address->city.', '.$claims->facility_detail->facility_address->pay_zip5.' - '.$claims->facility_detail->facility_address->pay_zip4}}</li> 
													@if(Auth::user()->practice_user_type == 'customer' || Auth::user()->practice_user_type == 'practice_admin')
													<li>
														<a target= "_blank" href = "{{url('/').'/facility/'.@$facility}}"  data-title='More Info'><i class='fa {{Config::get('cssconfigs.common.info')}}' data-placement='bottom' data-toggle='tooltip' data-original-title='More Details'></i></a>
													</li>
													@endif    
                                                @endif    
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>                            
                            </div>
                            <?php
								$insurance_category = @$claims->insurance_category;
								$insurance_id = @$claims->insurance_id;
								$claim_insurance_id = !empty($claims) ? ($insurance_id != 0) ? $insurance_category . '-' . $insurance_id : 'self' : "";
								$decoded_patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id, 'decode');

								$ins_id = (!is_null($insurance_id)) ? [1 => $insurance_id] : (!empty($insurance_data) ? explode('-', key((array) $insurance_data)) : "self");
								$auth_count = App\Models\Patients\PatientAuthorization::getalertonAuthorization($decoded_patient_id, $ins_id[1]);
								$insurance_tab_class = ($auth_count > 0) ? 'js-insurance-tabchange' : "";
                            ?>
                            <div class="form-group form-group-billing">
                                {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                @if($patients->is_self_pay == 'Yes' || empty($insurance_data))
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 select2-white-popup">  
                                    {!! Form::select('self', ['1' => 'Self'], 1,['class'=>'select2 form-control','id'=>'test_id', 'onChange' => 'changeselectval(this.value,\'Insurance\');']) !!}
                                </div> 
                                {!!Form::hidden('self',1)!!}
                                @else                                              
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 select2-white-popup">  
                                    {!! Form::select('insurance_id', (array)$insurance_data+array('self' => 'Self'), @$claim_insurance_id,['readonly' => 'readonly','class'=>'select2 form-control '. $insurance_tab_class,'id'=>'insurance_id', 'onChange' => "changeselectval(this.value,'Insurance','','$patient_id');"]) !!}
                                    {!!Form::hidden('insurance_category')!!}
                                    {!!Form::hidden('self')!!}
                                </div>
                                @endif
                                {!!Form::hidden('insurance_id', null,['id' => 'js-insurance'])!!} 
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu js-insurance-popup">
                                        @if(@$patients->is_self_pay != 'Yes')
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#" tabindex="-1">
                                            <span class="hidden-xs">
                                                <i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i>
                                            </span>
                                        </a>
                                        @endif                                        
                                        <div class="dropdown-menu1">
                                            <ul class="js-insurance-message"> 
                                                @if(!empty(@$claims->insurance_details))                                     

                                                @elseif(isset($insurances->insurance_name)&& !empty($insurances->insurance_name))

                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>
                            {!!Form::hidden('auth_date', null)!!}
                            <div class="form-group form-group-billing"> 
                                {!! Form::label('authorization', 'Auth No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-7 col-xs-10 billing-select2">  
                                    {!! Form::text('auth_no',@$claims->auth_no,['autocomplete'=>'off' ,'maxlength'=>'29','id'=>'authorization','class'=>'form-control input-sm-header-billing']) !!}
                                    {!! Form::hidden('authorization_id',@$claims->auth_no,['id'=>'29','id'=>'auth_id']) !!}
                                </div>
                                @if(!empty($claims->insurance_details) || !empty($insurances->insurance_name) || !empty($insurance_data) && ($patients->is_self_pay != 'Yes'))   
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 hidden-print"><a class="js-authpopup" href="#"  data-toggle = 'modal' data-target="#auth" data-url="patients/{{$patient_id}}/billing_authorization" tabindex="-1"><i class="fa {{Config::get('cssconfigs.common.comments')}} med-orange form-icon-billing"></i></a>
                                </div>
                                @endif
                            </div>
                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tab-l-b-1 border-green"><!--  2nd Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding margin-t-m-10">&emsp; </div>
                        <div class="box-body form-horizontal js-address-class margin-t-5" id="js-address-primary-address">
                            <div class="form-group form-group-billing">                             
                                {!! Form::label('Admission', 'Admission', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing  med-green font600']) !!}       
								<?php
									$admit_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->admit_date);
									$discharge_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->discharge_date);
									$doi_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->doi);
								?>								
                                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-5 ">
                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('admit_date')"></i>  
                                    {!! Form::text('admit_date',$admit_date,['class'=>'form-control input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"From"]) !!}
                                </div>        
                                {!!Form::hidden("small_date",null,['id' => 'small_date'])!!}
                                {!!Form::hidden("big_date",null,['id' => 'big_date'])!!}
                                {!!Form::hidden("entry_date",null,['id' => 'entry_date'])!!}
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                                    {!! Form::text('discharge_date',$discharge_date,['class'=>'form-control input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"To"]) !!}
                                </div>
                            </div>
                            <div class="form-groups form-group-billing">
                                {!! Form::label('DOI', 'DOI',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 p-r-0']) !!} 
                                <div class="col-lg-4 col-md-4 col-sm-3 col-xs-4">
                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('doi')"></i> 									
                                    {!! Form::text('doi',$doi_date,['class'=>'form-control dm-date input-sm-header-billing','placeholder'=>Config::get('siteconfigs.default_date_format'), 'id' => 'date_of_injury']) !!}
                                </div>
                                {!! Form::label('pos', 'POS',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label-billing med-green font600 p-l-0']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 select2-white-popup">   
                                    {!! Form::select('pos_id', array(''=>'--')+(array)$pos, @$pos_id, ['class'=>'form-control input-sm-header-billing select2', 'id' => 'pos_id']) !!}

                                </div>    
                            </div>  
                            <?php							
								$copay_detail = @$claims->payment_info;//(!empty($claims->id) && !empty($claims->copay)) ? App\Models\Patients\Payment::getPaymentInfo($claims->id) : [];
								$disabled_class = '';
								$readonly_class = '';
								$date = 'call-datepicker';
								$credit = "hide";
								$other_type = "";
								$money_order = "hide";
								$check_date = '';
								if (!empty($copay_detail)) {
									$disabled_class = "disabled";
									$readonly_class = "readonly";
									$date = '';
									if ($copay_detail->pmt_mode == "Credit") {
										$credit = "";
										$other_type = "hide";
									} else if ($copay_detail->pmt_mode == "Money Order"){  
										$checkNo = explode('-',$copay_detail->check_details->check_no)[1];
										$copay_detail->check_details->check_no = $checkNo;
										$other_type = "hide";
										$money_order = "";
										$check_date = "hide";
									}
								}
								$amt_star = $check_star = $card_star = $date_star = "";
                            ?>                          
                            <div class="form-group form-group-billing">
                                {!! Form::label('Copay', 'Co-Pay',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 js-copay-label']) !!} 
                                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-4 select2-white-popup">
                                    {!! Form::select('copay',['' => '--','Check' => 'Check','Cash' => 'Cash','Credit' => 'Credit / Debit', 'Money Order' => "MO"],@$copay_detail->pmt_mode,['class'=>'form-control select2 js-copay-select',$disabled_class]) !!}
                                </div>
                                @if(!empty(@$copay_detail->pmt_amt) && @$copay_detail->pmt_amt!= 0) <?php $amt_star = "star"; ?>@endif
                                {!! Form::label('pos', 'Amt',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label-billing med-green font600 p-l-0 js-copay-label jsamt '.$amt_star]) !!}
                                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-5">
                                    {!! Form::text('copay_amt',(@$copay_detail->pmt_amt != 0? @$copay_detail->pmt_amt:''),['class'=>'form-control js_no_space js_need_regex input-sm-header-billing',$readonly_class]) !!}                    
                                </div>    
                            </div> 
                            @if(!empty($copay_detail->check_no)) <?php $check_star = "star"; ?>@endif                           
                            <div class="form-group form-group-billing js-show-check <?php echo $other_type; ?>">
                                {!! Form::label('mode', 'Check No',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 Check js-copay-label '.$check_star]) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    {!!Form::hidden('checkexist', null)!!}
                                    <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>
                                    {!! Form::text('check_no',isset($copay_detail->check_details->check_no)?$copay_detail->check_details->check_no:'',['maxlength'=>$lengthval,'class'=>'form-control js-check-number js_need_regex input-sm-header-billing', 'readonly' => 'readonly','data-type'=> 'Patient']) !!}
                                </div>                          
                            </div> 
                            <div class="js-hide-money {{$money_order}}">
                             <div class="form-group form-group-billing">
                                {!! Form::label('Money order No.', 'MO No', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label med-green star','style'=>'font-weight:600;']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">                                    
                                    {!! Form::text('money_order_no', isset($copay_detail->check_details->check_no)?$copay_detail->check_details->check_no:'', ['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div> 
                            </div>
                             <div class="form-group form-group-billing">
                                 {!! Form::label('Money order No.', 'MO Date', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label med-green star','style'=>'font-weight:600;']) !!}  
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">   
                                  <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('money_order_date')"></i>                                 
                                    {!! Form::text('money_order_date',(isset($copay_detail->check_details->check_date) &&($copay_detail->check_details->check_date != '0000-00-00'))?date('m/d/Y', strtotime($copay_detail->check_details->check_date)):'',['class'=>'form-control input-sm-header-billing dm-date call-datepicker']) !!}
                                </div>  
                                </div>  
                            </div>
                            @if(!empty($copay_detail->card_type)) <?php $card_star = "star"; ?>@endif   
                            <div class="form-group form-group-billing <?php echo $credit; ?> js-show-card-type">
                                {!! Form::label('mode', 'Card Type',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 js-copay-label ']) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">                                  
                                    {!! Form::select('card_type', ['' => '-- Select--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],isset($copay_detail->card_type)?$copay_detail->card_type:'',['class'=>'select2 form-control', $disabled_class]) !!}
                                </div>                          
                            </div> 
							@if(!empty($copay_detail->card_no)) <?php $check_star = "star"; ?>@endif                           
                            <div class="form-group form-group-billing <?php echo $credit; ?> js-show-card-type">
                                {!! Form::label('card_no', 'Card No',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 Credit Check js-copay-label '.$check_star]) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>
                                    {!! Form::text('card_no', isset($copay_detail->card_details->card_no) ? $copay_detail->card_details->card_no : '', ['class'=>'form-control js_need_regex input-sm-header-billing',$disabled_class,'data-type'=> 'Patient']) !!}
                                </div>                          
                            </div> 
                            @if(!empty($copay_detail->check_details->check_date) && $copay_detail->check_details->check_date != '0000-00-00') <?php $date_star = "star"; ?>@endif                                  
                            <div class="form-group form-group-billing js-show-check {{$check_date}}">
                                {!! Form::label('Date', 'Check Dt',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 Check js-copay-label '.$date_star]) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
                                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i> 
                                    {!! Form::text('check_date',(isset($copay_detail->check_details->check_date) &&($copay_detail->check_details->check_date != '0000-00-00'))?date('m/d/Y', strtotime($copay_detail->check_details->check_date)):'',['placeholder'=>Config::get('siteconfigs.default_date_format'),'autocomplete'=>'off','class'=>'dm-date form-control input-sm-header-billing '.$date, $readonly_class]) !!}
                                </div>                          
                            </div> 

                            <div class="form-group form-group-billing">
                                {!! Form::label('mode', 'Reference',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-10 billing-select2">
                                    {!! Form::text('copay_detail', isset($claims->claim_reference) ? $claims->claim_reference : '', ['class'=>'form-control input-sm-header-billing js_need_regex', $readonly_class, 'maxlength' => '20']) !!}
                                </div>                          
                            </div>                                                          

                        </div><!-- /.box-body -->
                    </div><!--  2nd Content Ends -->
                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->
            <!-- Display ICD orders from E-superbill -->
            @if(!empty($claims)) 
            <?php  
				$icd_lists = array_flip(array_combine(range(1, count(explode(',', $claims->icd_codes))), explode(',', $claims->icd_codes))); 
				$icd = App\Models\Icd::getIcdValues($claims->icd_codes);
			?>
            @endif
            <div id="js-count-icd">
                <!-- Display ICD orders from E-superbill -->          
                <div class="col-lg-4 col-md-4 col-sm-10 col-xs-12" id= "js-input-div">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                        <span class="font600 bg-white padding-0-4">Diagnosis - ICD 10</span>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margin-t-m-4"><!-- ICD Details Starts here -->
                        <div class="box-body form-horizontal">
                            <div class="form-group form-group-billing">                            
                                {!! Form::label('icd1', '1',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd1',@$icd[1],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation', 'data-val'=>"1", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                                </div>
                                {!! $errors->first('icd1', '<p class = "med-red"> :message</p>')  !!}  
                            </div>
                            <div class="form-group form-group-billing">                            
                                {!! Form::label('icd2', '2',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd2',@$icd[2],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"2", 'autocomplete'=>'off','data-icdval' => 0]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[2])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                                </div>

                            </div>

                            <div class="form-group form-group-billing">                            
                                {!! Form::label('icd3', '3', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd3',@$icd[3],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"3", 'autocomplete'=>'off','data-icdval' => 0]) !!}
                                    <span id="icd3" class="icd-hover">@if(!empty($icd[3])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                                </div>                                                                                         
                            </div>

                            <div class="form-group form-group-billing">                            
                                {!! Form::label('icd4', '4',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd4',@$icd[4],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"4", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd4" class="icd-hover">@if(!empty($icd[4])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                                </div>                                            
                            </div>

                            <div class="form-group form-group-billing">                            
                                {!! Form::label('icd5', '5',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd5',@$icd[5],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"5", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd5" class="icd-hover">@if(!empty($icd[5])){{App\Models\Icd::getIcdDescription($icd[5])}}@endif</span>
                                </div>                                                                             
                            </div>

                            <div class="form-group form-group-billing margin-b-5">                            
                                {!! Form::label('icd6', '6',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd6',@$icd[6],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"6", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd6" class="icd-hover">@if(!empty($icd[6])){{App\Models\Icd::getIcdDescription($icd[6])}}@endif</span>
                                </div>                                          
                            </div>
                            <div class="js-display-err"></div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margin-t-m-4"><!-- ICD Details Starts here -->                
                        <div class="box-body form-horizontal">
                            <div class="form-group form-group-billing">                                                    
                                {!! Form::label('icd7', '7',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd7',@$icd[7],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation', 'data-val'=>"7", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd7" class="icd-hover">@if(!empty($icd[7])){{App\Models\Icd::getIcdDescription($icd[7])}}@endif</span>
                                </div>                       
                            </div>
                            <div class="form-group form-group-billing">                                                   
                                {!! Form::label('icd8', '8',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd8',@$icd[8],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"8", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd8" class="icd-hover">@if(!empty($icd[8])){{App\Models\Icd::getIcdDescription($icd[8])}}@endif</span>
                                </div>                             
                            </div>
                            <div class="form-group form-group-billing">                                                   
                                {!! Form::label('icd9', '9',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd9',@$icd[9],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"9", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd9" class="icd-hover">@if(!empty($icd[9])){{App\Models\Icd::getIcdDescription($icd[9])}}@endif</span>
                                </div>                                  
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('icd10', '10',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd10',@$icd[10],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"10", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd10" class="icd-hover">@if(!empty($icd[10])){{App\Models\Icd::getIcdDescription($icd[10])}}@endif</span>
                                </div>                              
                            </div>
                            <div class="form-group form-group-billing">                                                   
                                {!! Form::label('icd11', '11',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd11',@$icd[11],['class'=>'form-control text_class input-sm-header-billing js-icd bg-white js_icd_validation','data-val'=>"11", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd11" class="icd-hover">@if(!empty($icd[11])){{App\Models\Icd::getIcdDescription($icd[11])}}@endif</span>
                                </div>                                           
                            </div>
                            <div class="form-group form-group-billing margin-b-5">
                                {!! Form::label('icd12', '12',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!} 
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                    {!! Form::text('icd12',@$icd[12],['class'=>'form-control text_class js_icd_validation input-sm-header-billing js-icd bg-white','data-val'=>"12", 'autocomplete'=>'off', 'data-icdval' => 0]) !!}
                                    <span id="icd12" class="icd-hover">@if(!empty($icd[12])){{App\Models\Icd::getIcdDescription($icd[12])}}@endif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- General Details Full width Ends -->       
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">   
			
            <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 pull-right no-padding">
                <div class="margin-t-8">                            
					<?php //dd($claims->anesthesia_details);
						$anesthesia_minute = @$claims->anesthesia_details->anesthesia_minute;
						$anesthesia_unit = (is_numeric($anesthesia_minute)) ? $anesthesia_minute / 15 : 0;
						//$anesthesia_unit = Math.floor(anesthesia_unit);
						$remaining_muinute = is_numeric($anesthesia_minute) ? $anesthesia_minute % 15 : 0;
						if ($remaining_muinute >= 10) {
							$anesthesia_unit = $anesthesia_unit + 1;
						}
			        ?>				
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding p-r-0">
						
                        <div class="col-lg-2 col-md-3 col-sm-2 col-xs-12 med-green font600 no-padding p-l-0 ">Anesthesia</div>
                        <div class="form-group col-lg-3 col-md-2 col-sm-3 col-xs-5 bootstrap-timepicker">
                            <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('anesthesia_start')"></i> 
                            {!! Form::text('anesthesia_start',@$claims->anesthesia_details->anesthesia_start,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Start Time', 'id' => 'anesthesia_start']) !!}
                        </div>
                        <div class="form-group col-lg-3 col-md-2 col-sm-2 col-xs-5 bootstrap-timepicker m-t-xs-5">
                            <i class="fa {{Config::get('cssconfigs.common.clock')}} form-icon-billing" onclick= "iconclick('anesthesia_stop')"></i> 
                            {!! Form::text('anesthesia_stop',@$claims->anesthesia_details->anesthesia_stop,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Stop Time','id' => 'anesthesia_stop']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 m-t-xs-5">
                            {!! Form::text('anesthesia_minute', @$claims->anesthesia_details->anesthesia_minute, ['class'=>'form-control input-sm-modal-billing','placeholder'=>'Min', 'readonly' => 'readonly']) !!}
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 m-t-xs-5">
                            {!! Form::text('anesthesia_unit',(isset($anesthesia_unit) && $anesthesia_unit != 0)?$anesthesia_unit:"",['class'=>'form-control input-sm-modal-billing','placeholder'=>'Units', 'readonly' => 'readonly']) !!}
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        <div class="has-error col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
            <ul class="billing line-height-26 border-radius-4 no-padding mobile-width billing-charge-table" id="">
                <li class="billing-grid">
                    <table class="table-billing-view">
                        <thead>
                            <tr>
                                <th class="td-c-4">&emsp;</th>
                                <th class=" td-c-6">From</th>                                                
                                <th class=" td-c-6">To</th>                                
                                <th class="td-c-8">CPT</th>
                                <th class="td-c-4">M 1</th>
                                <th class="td-c-4">M 2</th>
                                <th class="td-c-4">M 3</th>    
                                <th class="td-c-4">M 4</th>  
                                <th class="td-c-24" colspan="12">ICD Pointers</th>
                                <th class="td-c-3">Units</th>
                                <th class="td-c-6">Charges</th>
                                <th class="td-c-6">Co-Pay</th>
                            </tr>
                        </thead>
                    </table>                                     
                </li>
                <!-- Display CPT from E-superbill -->
                <?php
					$count = 6;
					$count_cnt = 0;
					if (!empty($claims)) {
						$cpt_codes = explode(',', @$claims->cpt_codes);
						$count_cnt = count($cpt_codes);
						if ($count_cnt > 6)
							$count = 6;
						$cpt_icd = explode('::', @$claims->cpt_codes_icd);
					}
					$modifier_readonly = "readonly";
					$payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone(@$claims->id, 'any');
					$notes_count = App\Models\Payments\ClaimInfoV1::checkChargeActivity(@$claims->id);
                ?>
                <!-- Display CPT from E-superbill -->
                @if(!empty($claims->dosdetails))
                <?php
                if (count($claims->dosdetails) > $count)
                    $count = count($claims->dosdetails);
                ?>
                <div class="js-append-parent">
                    @for($i=0;$i<$count;$i++)
                    <?php 
						$date_to = $date_from = '';                    
						if (!empty($claims->dosdetails[$i]->dos_to)) {
							$date_to = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->dosdetails[$i]->dos_to);
						}
						if (!empty($claims->dosdetails[$i]->dos_from)) {
							$date_from = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->dosdetails[$i]->dos_from);
						}                    
						$icd_map = isset($claims->dosdetails[$i]->cpt_icd_map_key) ? array_combine(range(1, count(explode(',', $claims->dosdetails[$i]->cpt_icd_map_key))), explode(',', $claims->dosdetails[$i]->cpt_icd_map_key)) : '';
						$style = '';
						if ($i >= 6 && $count < 6) {
							$style = "style = display:none;";
						}
						$dos_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->dosdetails[$i]->id, 'encode');

						$dos_shade_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->dosdetails[$i]->claim_cpt_shaded_details->id, 'encode');
						$refering_count = (@$claims->dosdetails[$i]->cptdetails->referring_provider == "Yes") ? 1 : 0;
                    ?>
                    <li id = "js-modifier-list-{{$i}}" class="billing-grid js_line_validate js-validate-lineitem js-disable-div-{{$i}}" <?php echo $style; ?>>
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>
                                    <td class="td-c-4 active">
                                        @if(empty(@$claims->dosdetails[$i]->dos_to))
                                        <a href="javascript:void(0);" class="js-chargelineitem-delete" data-id= "<?php echo $i ?>" data_delete_id = "<?php echo $dos_id; ?>" tabindex = -1><i class= "fa  {{Config::get('cssconfigs.common.delete')}}"></i></a>
                                        @endif
                                        <input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight " name="is_active[]"><label for="{{$i}}" class="no-bottom">&nbsp;</label>
										<i class="fa fa-plus med-green form-cursor js-showhide-box24 textboxrow" data-placement="top" data-toggle="tooltip" data-original-title="Shaded Area"  id="<?php echo $i; ?>"></i>	
                                    </td>                                    
                                    <td class="td-c-6">
										<input type="text" accesskey="" data-postition="left_first_row" class="js_need_regex js_validate_date from_dos dm-date billing-noborder js_from_date textboxrow" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}" onchange="datevalidation(<?php echo $i; ?>)" onblur="datevalidation(<?php echo $i; ?>)">
									</td>
                                    <td class="td-c-6">
										<input type="text" class="js_validate_date to_dos dm-date billing-noborder js_to_date textboxrow js_need_regex" name=<?php echo "dos_to[" . $i . "]"; ?>  value = "{{@$date_to}}" onchange="todatevalidation(<?php echo $i; ?>)">
									</td>                                   
                                    <td class="td-c-8">
                                        <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder textboxrow" value = "{{@$claims->dosdetails[$i]->cpt_code}}" name= <?php echo "cpt[" . $i . "]"; ?> > 
                                        <span id="cpt-<?php echo $i; ?>" class="cpt-hover" style="display:none;">@if(!empty(@$claims->dosdetails[$i]->cpt_code)){{ucfirst(App\Models\Cpt::where('cpt_hcpcs', $claims->dosdetails[$i]->cpt_code)->value('short_description'))}}@endif</span>
									</td>                                    
                                    <td class="td-c-4">
                                        <input type="hidden" class="billing-noborder js-hidden-cpt" value = "{{@$claims->dosdetails[$i]->cpt_code}}">
                                        {!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1, ['class'=>'form-control textboxrow billing-noborder js-modifier modifier_open bg-white', 'maxlength' => 2, 'id' =>'modifier1-'.$i , $modifier_readonly, 'data-cpt' => @$claims->dosdetails[$i]->cpt_code]) !!}
                                    </td>
                                    <td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier2-'.$i, $modifier_readonly]) !!}</td>
                                    <td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier3-'.$i , $modifier_readonly]) !!}</td>
                                    <td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier4-'.$i, $modifier_readonly]) !!}</td>
                                    @for($j=1;$j<=12;$j++)
                                    <td class="td-c-2"> 
                                        <input type="text" class="icd_pointer textboxrow" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_map[$j] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
                                    </td>
                                    @endfor                                    
                                    <td class="td-c-3"><input class="textboxrow cpt_unit billing-noborder" type="text" id="<?php echo $i ?>"  maxlength = 3 name=<?php echo "unit[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->unit}}" >
                                    </td>
                                    <td class="td-c-6">
                                        <input type="text" data-postition="right_last_row" autocomplete="off" class = "text-right textboxrow js-charge js_charge_amt form-control input-sm-header-billing billing-noborder allownumericwithdecimal" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->charge}}">
                                        <input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed_amt}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
                                        <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cptdetails->billed_amount}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
                                        <input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_code}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()">
                                        <input type="hidden" class="cpt_icd_map_key billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_map_key}}" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> >
									</td>                                         
                                    <td class="td-c-6">										
                                        <input type="text" data-postition="right_last_row" autocomplete="off" class = "copay_applied text-right textboxrow form-control input-sm-header-billing billing-noborder bg-white allownumericwithdecimal" value = "{{@$claims->dosdetails[$i]->claim_cpt_patient_tx_details->paid }}" name=<?php echo "copay_applied[" . $i . "]"; ?>>
                                    </td> 
                                    
									<input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden" tabindex = -1>
									<?php 
										$claim_cpt_TxId = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims->dosdetails[$i]->claim_cpt_patient_tx_details->id, 'encode');
									?>
									<input name= <?php echo "copay_Transcation_ID[" . $i . "]"; ?> value = "{{@$claim_cpt_TxId}}" type="hidden" tabindex = -1>
									{!!Form::hidden('refering_provider_count['.$i.']', $refering_count, ['id' =>'refering-'.$i, 'class' => 'js-refering-count'])!!} 
									
									<input name=<?php echo "box_ids[" . $i . "]"; ?> value= "{{@$dos_shade_id}}" type="hidden" tabindex = -1>
								</tr>
                            </tbody>
                        </table>
                        <div id="js_box_24_{{$i}}" style="display: none;">
                            <input type="text" maxlength= '61' class = "textboxrow form-control input-sm-header-billing box_24_atoj" name=<?php echo "box_24_AToG[]"; ?> value="{{ (isset($claims->dosdetails[$i]->claim_cpt_shaded_details->box_24_AToG) && $claims->dosdetails[$i]->claim_cpt_shaded_details->box_24_AToG != 'N4 UNnull null') ? $claims->dosdetails[$i]->claim_cpt_shaded_details->box_24_AToG : '' }}" id="<?php echo $i; ?>">
                        </div>
                    </li>                
                    @endfor
					   {!!Form::hidden('copay_id',@$claims->payment_info->id)!!}
                </div>
                @else
                <div class="js-append-parent">
                    <?php 
						$dos_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims->date_of_service);
					?>					
                    @for($i=0;$i<$count;$i++)
                    <?php
						$icd_val = isset($cpt_icd[$i]) ? App\Models\Icd::getIcdValues($cpt_icd[$i]) : '';
						$icd_val_split = !empty($icd_val) ? implode(',', $icd_val) : '';
						$icd_map = isset($cpt_icd[$i]) ? array_combine(range(1, count(explode(',', $cpt_icd[$i]))), explode(',', $cpt_icd[$i])) : '';
						$style = '';
						if ($i >= 6) {
							$style = "style = display:none;";
						}
                    ?>
                    <li id = "js-modifier-list-{{$i}}" class="billing-grid js_line_validate js-validate-lineitem js-disable-div-{{$i}}" <?php echo $style; ?>>
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>
                                    <td class="td-c-4">
                                        <a href="javascript:void(0);" class="js-chargelineitem-delete" data-id= "<?php echo $i ?>" tabindex = -1><i class= "fa  {{Config::get('cssconfigs.common.delete')}}"></i></a> <input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight" name="is_active[]"><label for="{{$i}}" class="no-bottom">&nbsp;</label>
                                        <i class="fa fa-plus med-green form-cursor js-showhide-box24" data-placement="top" data-toggle="tooltip" data-original-title="Shaded Area" id="<?php echo $i; ?>"></i>
                                    </td>  
                                    <td class="td-c-6">
										<input type="text" accesskey="" data-postition="left_first_row" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="textboxrow js_validate_date js_from_date from_dos dm-date billing-noborder js_need_regex" name=<?php echo "dos_from[" . $i . "]"; ?>  onchange="datevalidation(<?php echo $i; ?>)">
									</td>
                                    <td class="td-c-6">
                                        <input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="textboxrow js_validate_date dm-date to_dos billing-noborder js_to_date js_need_regex" name=<?php echo "dos_to[" . $i . "]"; ?>  onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
                                    <td class="td-c-8">
                                        <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="textboxrow js-cpt billing-noborder" tabindex = -1 value = "<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('cpt_hcpcs') : ''; ?>" 
                                               name= <?php echo "cpt[" . $i . "]"; ?> > 
                                        <span id="cpt-<?php echo $i; ?>" class="cpt-hover" style="display:none;">@if(!empty(@$claims->dosdetails[$i]->cpt_code)){{ucfirst(App\Models\Cpt::where('cpt_hcpcs', $claims->dosdetails[$i]->cpt_code)->value('short_description'))}}@endif</span></td>                                   
                                    <td class="td-c-4">
                                        <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_billed_amt}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
                                        {!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'form-control textboxrow billing-noborder js-modifier modifier_open bg-white', 'maxlength' => 2, 'id' =>'modifier1-'.$i , $modifier_readonly]) !!}</td>
                                    <td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier2-'.$i, $modifier_readonly]) !!}</td>
                                    <td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier3-'.$i , $modifier_readonly]) !!}</td>
                                    <td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier4-'.$i, $modifier_readonly]) !!}</td>
                                    <?php
										$a = array();
										$cpt_icd_key = '';
                                    ?>
                                    @for($j=1;$j<=12;$j++)
                                    <td class="td-c-2"> 
                                        <input type="text" class="textboxrow icd_pointer billing-icd-pointers" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? @$icd_lists[$icd_map[$j]] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
                                        <?php // echo ($j != 12) ? ' <span class="billing-pipeline">|</span>' : ''  
                                        if (!empty($icd_map[$j]))
                                            $key = array_push($a, $icd_lists[$icd_map[$j]]);
                                        ?>
                                    </td>
                                    @endfor                                                    
                                    <td class="td-c-3"><input class="textboxrow cpt_unit billing-noborder" value= "<?php echo isset($cpt_codes[$i]) ? 1 : '' ?>" maxlength = 3 type="text" id="<?php echo $i ?>" name=<?php echo "unit[" . $i . "]"; ?> ></td>
                                    <td class="td-c-6"><input type="text" data-postition="right_last_row" autocomplete="off" class = "text-right textboxrow js-charge js_charge_amt  billing-noborder allownumericwithdecimal" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value="<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('billed_amount') : ''; ?>">
                                        <input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$icd_val_split}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()">
									</td>                                    
                                     <td class="td-c-6">
                                         <input type="text" data-postition="right_last_row" autocomplete="off" class = "copay_applied text-right textboxrow form-control input-sm-header-billing billing-noborder allownumericwithdecimal bg-white" name=<?php echo "copay_applied[" . $i . "]"; ?>>

										<input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
										<input type="hidden" class="cpt_icd_map_key billing-nb" value = "<?php echo!empty($a) ? implode(',', $a) : ''; ?>" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> >
									</td>
									{!!Form::hidden('refering_provider_count['.$i.']', null, ['id' =>'refering-'.$i, 'class' => 'js-refering-count'])!!} 
									<input name= <?php echo "copay_Transcation_ID[" . $i . "]"; ?> value = "{{@$claim_cpt_TxId}}" type="hidden" tabindex = -1>                             
									<input name= <?php echo "box_ids[" . $i . "]"; ?> value = "{{@$dos_shade_id}}" type="hidden" tabindex = -1>								
								</tr>                            
                            </tbody>
                        </table>
                        <div id="js_box_24_{{$i}}" style="display: none;">
                            <input type="text" maxlength= '61' class = "textboxrow form-control input-sm-header-billing box_24_atoj" name=<?php echo "box_24_AToG[]"; ?> id="<?php echo $i; ?>" value="{{@$claims->dosdetails[$i]->claim_cpt_shaded_details->box_24_AToG}}">
                        </div>
                    </li>                
                    @endfor
                </div>
                @endif
                <?php
					$display_class = 'style="display:none;"';
					if (isset($claims) && count($claims->dosdetails) != 10 && ($count_cnt >= 6 || (!empty($claims->dosdetails) && count($claims->dosdetails) >= 6))) {
						$display_class = '';
					}
                ?>
                {!!Form::hidden('item_ids', null,['id' => 'js_line_itemdelete_id'])!!}
                {!!Form::hidden('appentvalue', $i,['id' => 'js-appendrow'])!!}
            </ul>
            <div class="margin-t-m-8">                
                <span class="append cur-pointer font600 med-green" <?php echo $display_class; ?>><i class="fa fa-plus"></i> Add</span>
            </div>
            {{-- @if(!empty($claims))
             {!!Form::hidden('fromedit',1)!!}
             @endif --}}
            {!! $errors->first('dos_from[0]', '<p class = "med-red"> :message</p>') !!} 
            {!! $errors->first('dos_to[0]', '<p class = "med-red">  :message</p>') !!}
            {!! $errors->first('cpt[0]', '<p class = "med-red"> :message</p>') !!}            
        </div>
        <div class="pull-right margin-b-10 margin-t-5 m-r-m-12"> 
            <span class=" med-green font600" >Total Charges : </span>
            <span class="med-orange font600 margin-l-20"> 
                <input type="text" readonly = "readonly"name = "total_charge" value = "0.00" class="js-total billing-noborder text-right td-c-50">
                <span class="js-total"></span>
            </span>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 charge-notes-bg">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                {!! Form::text('note',null,['class'=>'form-control input-sm-modal-billing js_need_regex','placeholder'=>'Notes', 'accesskey'=>'n']) !!}                       
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&emsp;</div>
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 m-t-sm-5 margin-t-4 p-r-0">
                <?php
					$checked = '';
					$style = 'style="display:none;"';
					$disabled = "disabled";
					if (!empty($claims->hold_reason_id) && $claims->status == 'Hold') {
						$checked = "checked";
						$disabled = "";
					} 
                ?>
                <input type="checkbox" class="" id="hold-option" name="is_hold" value=1 <?php echo $checked; ?>> <label for="hold-option" class="med-darkgray cur-pointer font600">Hold</label>
            </div>
            <div class="js-add-new-select hold-option no-margin" id= "js-holdoptions-type">
                <div class="form-group js_common_ins no-margin">                                                                                                   
                    <div class="col-lg-4 col-md-4 col-sm-9 col-xs-8 p-r-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">
                        {!! Form::select('hold_reason_id', array('' => '-- Select --') + (array)$hold_option,  @$claims->hold_reason_id,['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt','id' =>'js-hold-reason', $disabled]) !!} 
                    </div>
                    <div class="col-sm-1 col-xs-1">
                        {!!Form::hidden('hold_reason_exist',null)!!}
                    </div>
                </div>
                <div class="form-group hide no-margin" id="add_new_span">                   
                    <div class="col-lg-4 col-md-4 col-sm-9 col-xs-8 p-r-0  no-margin">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  m-t-sm-5 m-t-xs-5 no-padding hold-option-reason pull-right" <?php echo $style; ?>>      
                            {!! Form::text('other_reason',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add New','data-label-name'=>'hold reason','data-field-name'=>'option', 'data-table-name' => 'holdoptions']) !!}
                        </div>                                               
                        <a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
                        <a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
                    </div>
                </div>
            </div>           
        </div>
		
		<?php
			if(!empty($claims->claim_details)) {
				$claim_detail_url = url('/patients/claimdetail/'.$claims->claim_details->id.'/edit');
			} else {
				$claim_detail_url = url('/patients/claimdetail/create/'. $patient_id);
			}
			if(!empty($claims->ambulance_billing_id)) {
				$claim_billing_url = url('/patients/claimbilling/'.$claims->ambulance_billing_id.'/edit');
			} else {
				$claim_billing_url = url('/patients/claimbilling/create/'. $patient_id);
			}
			if(!empty($claims->claim_other_detail_id)) {
				$claim_other_detail_url = url('/patients/claimotherdetail/'.$claims->claim_other_detail_id.'/edit');
			} else {
				$claim_other_detail_url = url('/patients/claimotherdetail/create/'.$patient_id);
			}
		?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 no-padding hidden-print">
            <div class="payment-links ">
                <ul class="nav nav-pills pull-left">
                    <li><a href="#" data-toggle="modal" data-target="#js-model-popup" data-page=@if(isset($claims->claim_details->id) && !empty($claims->claim_details->id)) 'edit' @else 'create' @endif class="claimdetail font600" data-url="{{$claim_detail_url}}"> <i class="fa {{Config::get('cssconfigs.charges.view_trans')}}"></i> Claim Details</a></li>
                   <?php /* <li><a href="#" data-toggle="modal" data-target="#js-model-popup" class="claimbilling font600" data-url="{{$claim_billing_url}}"> <i class="fa {{Config::get('cssconfigs.charges.ambulance')}}"></i>Ambulance Billing</a></li> 
                    <li><a href="#" data-toggle="modal" data-target="#js-model-popup" class="claimotherdetail font600" data-url="{{$claim_other_detail_url}}"> <i class="fa {{Config::get('cssconfigs.charges.hand-right')}}"></i> Other Details</a></li>
					*/ ?>
                </ul>
                @if(!empty($claims) && $claims->charge_add_type != 'esuperbill')                        
                <ul class="nav nav-pills  pull-right">
                    <li><a class=" claimotherdetail font600"  onClick="window.open('{{url('/getcmsform/'.$claim_id)}}', '_blank')"> <i class="fa {{Config::get('cssconfigs.patient.cms_icon')}}"></i> CMS 1500</a></li>

                    <li><a data-index="ledger" data-id ="{{ @$claim_id}}" class="claim_assign_all_link form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a></li>
                </ul>            
                @endif
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center hidden-print margin-b-10">                
			@if(Request::segment(5) == '')
				{!! Form::submit('Save', ['class'=>'btn btn-medcubics js-charge_save','style'=>'padding:2px 40px;', 'disabled' => 'disabled', 'accesskey'=>'s']) !!}
			@else
				{!! Form::submit('Save', ['class'=>'btn btn-medcubics js-charge_save','style'=>'padding:2px 40px;', 'disabled' => 'disabled', 'accesskey'=>'s']) !!}
			@endif
            @if(empty($claims))
				{!! Form::hidden('is_create',1) !!}
				{!! Form::hidden('is_from_charge',null,['class' => 'js-charge-input']) !!}
				{!! Form::hidden('batch_id',null,['class' => 'js-batch-input']) !!}
            @endif
            <?php 
				$currnet_page = Route::getFacadeRoot()->current()->uri(); 
				$attrony_assigned = isset($attrony_assigned) ? $attrony_assigned : 0; 
			?>
            @if(!empty($claims) && ($claims->status == 'Ready' || $claims->status == 'Hold' || $claims->status == 'Pending' || $claims->status == 'Patient') && $payment_count == 0 && $notes_count == 0 && $attrony_assigned == 0)
				@if($attrony_assigned != 0)
					<a class="btn btn-medcubics js_del_alert" data-text="Claim cannot be deleted due assigned to Attorney" href="#">Delete </a>
				@else
					<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete claim?" href="{{(strpos($currnet_page, 'charge') !== false)? url('charges/delete/'.$claim_id):url('patients/billing/delete/'.$claim_id)}}">Delete </a>
				@endif
            @endif
            <!-- <a href="{{url('patients/billing')}}">{!! Form::button('Save', ['class'=>'btn btn-medcubics js-save-charge']) !!}</a> -->  
            <?php
				$request = Request::ajax();
				$url = ($request) ? url('charges/create') : url('patients/' . $patient_id . '/billing');
				if (strpos($currnet_page, 'charge') !== false && strpos($currnet_page, 'patients') == false && !$request) {
					$url = url('charges');
				}
            ?>    
            <a href="javascript:void(0)" data-url="{{$url}}" id="js_cancel_link">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site ']) !!}</a>
        </div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->

@include ('patients/billing/modifierpopup')
<style>
    div.dataTables_filter input{min-width: 200px;}
    div.dataTables_info{display: none;}
    div.dataTables_paginate{margin-left:-100px;}
    .icd_pointer { width:17px;}
    .td-c-24 {text-align: center;width: 24%;}
    .text_class { text-transform: uppercase;}
    ul.ui-autocomplete li.ui-menu-item::first-letter {font-weight: 600;color: #f07d08;}
</style>

@push('view.scripts')
<script type="text/javascript">
    $('#authorization').attr('autocomplete','off');
    $('input[type="text"]').attr('autocomplete','off');
    $("#pos_id").on('change', function(){
        if($(this).val()==''){
            $(this).closest('.form-groups').removeClass('has-success');
        }else{
            $(this).closest('.form-groups').addClass('has-success');
        }
    });
    @if(isset($rendering_provider_id) && $rendering_provider_id != "")
    $(document).ready(function () {
        $("#facility_id").trigger("change");
     });   
    @endif
<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
 <?php }?>
</script>
@endpush