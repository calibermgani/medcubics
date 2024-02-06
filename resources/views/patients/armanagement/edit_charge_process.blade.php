<?php
	$claim_detail_val_edit = (object)$claim_detail_val_edit[0];
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val_edit->patient->id,'encode');
	$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val_edit->id,'encode');
	$statelicense = isset($claim_detail_val_edit->refering_provider_id)?App\Models\Provider::checkstatelicense($claim_detail_val_edit->refering_provider_id):'';
	$statelicense_billing = isset($claim_detail_val_edit->billing_provider_id)?App\Models\Provider::checkstatelicense($claim_detail_val_edit->billing_provider_id):'';
?>  
	{!! Form::open(['onsubmit'=>"event.preventDefault();",'method'=>'POST','name'=>'claim_editcharge_form','id' => 'js-bootstrap-validator','class'=>'popupmedcubicsform js_billingform']) !!}
		{!!Form::hidden('claim_id',@$claim_id, ['id' => 'js-claim-id'])!!}
		{!!Form::hidden('auth_date', null)!!}
		{!!Form::hidden('providertypeid',@$claim_detail_val_edit->refering_provider->provider_types_id,['id' => 'providertypeid'])!!}
		{!!Form::hidden('statelicence',@$statelicense, ['id' => 'statelicence'])!!}
		{!!Form::hidden('statelicence_billing',@$statelicense_billing, ['id' => 'statelicence_billing'])!!}
		{!!Form::hidden('taxanomy',@$claim_detail_val_edit->billing_provider->taxanomy->code, ['id' => 'providertaxanomy'])!!}
		{!!Form::hidden('upinno',@$claim_detail_val_edit->refering_provider->upin, ['id' => 'upin_no'])!!}
		@if(!empty($claim_detail_val_edit->claim_ids))
		<?php
		$claims_count = explode(',', $claim_detail_val_edit->claim_ids);
		if (count($claims_count) > 1) { 
			$key = array_search($claim_detail_val_edit->id, $claims_count);
			if ($key + 1 <= count($claims_count)) {
				?>
				{!!Form::hidden('next_id',@$claims_count[$key+1])!!}
			<?php
			}
		}   
		?>
		@endif

		<?php 
			if(is_null($patient_id)){
				$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val_edit->patient_id,'encode');;
			} 
			$payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone(@$claim_detail_val_edit->id, 'payment');
			
			if(($claim_detail_val_edit->status = 'Ready' || $claim_detail_val_edit->status = 'Patient') && $payment_count == 0){
				$rest_field = 'no';
			} else{
				$rest_field = 'yes';
			}
		?>
	
		<div class="box box-view no-shadow no-border"><!--  Box Starts -->
			<div class="box-body form-horizontal">

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding margin-t-5 border-green"><!-- General Details Full width Starts -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Only general details content starts -->
						<div class="box no-border  no-shadow"><!-- Box Starts -->
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 "><!--  1st Content Starts -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
									<span class="font600 bg-white padding-0-4">General Details</span>
								</div>
								<span id="ajax-charge-loader"></span>
								<div class="box-body form-horizontal"><!-- Box Body Starts -->
									<div class="form-group-billing">
										{!!Form::hidden('patient_id',$patient_id)!!}
										@if(empty(@$claim_detail_val_edit))
										{!!Form::hidden('charge_add_type','billing')!!}
										@endif
										<label for="Rendering Provider" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green" id="demo">Rend Prov</label> 
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup @if($errors->first('rendering_provider_id')) error @endif">
											{!! Form::select('rendering_provider_id', array('' => '-- Select --') + (array)$rendering_providers,  @$claim_detail_val_edit->rendering_provider_id,['class'=>'select2 form-control', 'id' => 'providerpop', 'onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');',(@$rest_field == 'yes') ? 'disabled' : '']) !!}  
											{!! $errors->first('rendering_provider_id', '<p> :message</p>')  !!}
										</div>
									</div>                            
									<div class="form-group-billing">
										<label for="" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green" id="ref_label">Refe Prov</label> 
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup ">
										   {!! Form::select('refering_provider_id', array('' => '-- Select --') + (array)$referring_providers,  @$claim_detail_val_edit->refering_provider->id,['class'=>'select2 form-control']) !!}
										</div>                                
									</div>

									<div class="form-group-billing">
										<label for="Billing Provider" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green">Bill Prov</label> 
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup ">
											{!! Form::select('billing_provider_id', array('' => '-- Select --') + (array)$billing_providers,  @$claim_detail_val_edit->billing_provider_id,['class'=>'select2 form-control','id' => 'billingprovider-pop','onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');',(@$rest_field == 'yes') ? 'disabled' : '']) !!}  
											{!! $errors->first('billing_provider_id', '<p> :message</p>')  !!}
										</div>  
									</div>

									<div class="form-group-billing">
										<label for="Facility" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green">Facility</label>                                                  
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup ">  
											{!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities,  @$claim_detail_val_edit->facility_id,['class'=>'select2 form-control','id'=>'facility_id', 'onChange' => 'changeselectval(this.value,\'Facility\', \'\');',(@$rest_field == 'yes') ? 'disabled' : '']) !!}   
											{!! Form::hidden('facility_clai_no')!!}
											{!! $errors->first('facility_id', '<p> :message</p>')  !!}
										</div>                                           
									</div>
									<?php 
										$insurance_arr = App\Models\Patients\Patient::getARPatientInsurance($claim_detail_val_edit->patient_id); 
										$insurance_arr = (array)$insurance_arr;
										$search_text = 'Primary';
										$primary_val = [];
										$primary_val = array_filter($insurance_arr, function($el) use ($search_text) {
											return ( strpos($el, $search_text) !== false );
										});
									  
										if(isset($claim_detail_val_edit->patient->is_self_pay) && ($claim_detail_val_edit->patient->is_self_pay == 'Yes') && !empty($insurance_arr)){
											unset($primary_val);
											$primary_val['self']   = 'self';
										}                            
									?>
									
									<div class="form-group-billing">
										<label for="Billed To" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green">Billed To</label>        
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 select2-white-popup">  
											@if($claim_detail_val_edit->patient->is_self_pay == 'Yes' || empty($insurance_arr))
												{!! Form::select('insurance_id', ['self' => 'Self'], 'self',['readonly' => 'readonly','class'=>'select2 form-control']) !!}
											@else
												{!! Form::select('insurance_id', array('self' => 'Self')+$insurance_arr,!empty($claim_detail_val_edit->insurance_id)?$claim_detail_val_edit->insurance_id:array_keys($primary_val),['readonly' => 'readonly','class'=>'select2 form-control',(@$rest_field == 'yes') ? 'disabled' : '']) !!}
												{!!Form::hidden('insurance_category')!!}
												{!!Form::hidden('self')!!}
											@endif
										</div>                                                                
									</div>
									
									<div class="form-group-billing"> 
										<label for="authorization" class="col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green">Auth No</label>
										<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
											{!! Form::text('auth_no',@$claim_detail_val_edit->auth_no,['maxlength'=>'25','id'=>'authorization','class'=>'form-control input-sm-header-billing','readonly'=>'readonly']) !!}
											{!! Form::hidden('authorization_id',null,['id'=>'25','id'=>'auth_id']) !!}
										</div>
									   
									</div>

								</div><!-- /.box-body Ends-->
							</div><!--  1st Content Ends -->

							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tab-l-b-1 border-green" ><!--  2nd Content Starts -->
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding margin-t-m-10">&emsp; </div>

								<div class="box-body form-horizontal js-address-class" id="js-address-primary-address">                         

									<div class="form-group-billing">                             
										<label for="Admit" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing  med-green">Admission</label>                           
										<div class="col-lg-5 col-md-5 col-sm-3 col-xs-5 ">
											<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('admit_date')"></i>  
											{!! Form::text('admit_date',(isset($claim_detail_val_edit->admit_date) && $claim_detail_val_edit->admit_date != '1970-01-01' && $claim_detail_val_edit->admit_date != '0000-00-00')?@date('m/d/Y',strtotime($claim_detail_val_edit->admit_date)):'',['class'=>'form-control call-datepicker dm-date input-sm-header-billing dm-date p-r-0','placeholder'=>"From"]) !!}
										</div>        
											{!!Form::hidden("small_date",null,['id' => 'small_date'])!!}
											{!!Form::hidden("big_date",null,['id' => 'big_date'])!!}													
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 ">
											{!! Form::text('discharge_date',(isset($claim_detail_val_edit->discharge_date) && $claim_detail_val_edit->discharge_date != '1970-01-01' && $claim_detail_val_edit->discharge_date != '0000-00-00')?@date('m/d/Y',strtotime($claim_detail_val_edit->discharge_date)):'',['class'=>'form-control dm-date input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"To"]) !!}
										</div>
									</div>

									<div class="form-group-billing">
										<label for="mode" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green">DOI</label> 
										<div class="col-lg-9 col-md-9 col-sm-7 col-xs-10">
											<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('doi')"></i> 
											{!! Form::text('doi',(@$claim_detail_val_edit->doi && $claim_detail_val_edit->doi !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claim_detail_val_edit->doi)):'',['class'=>'form-control dm-date input-sm-header-billing', 'id' => 'date_of_injury']) !!}
										</div>                          
									</div>   

									<div class="form-group-billing">
										<label for="Bill Cycle" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green p-r-0">Bill Cycle</label> 
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 billing-select2-disabled-yellow">
											{!! Form::text('bill_cycle',@$claim_detail_val_edit->patient->bill_cycle,['maxlength'=>'25','class'=>'form-control input-sm-header-billing','readonly'=>'readonly']) !!}
										</div>
										<label for="pos" class="col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label-billing med-green p-l-0">POS</label> 
										<div class="col-lg-4 col-md-4 col-sm-2 col-xs-4 select2-white-popup">    
											{!! Form::select('pos_id', (array)$pos, @$claim_detail_val_edit->pos_id, ['class'=>'select2 form-control input-sm-header-billing', 'id' => 'pos_id', 'disabled' => 'disabled']) !!}
										</div>    
									</div>
									
									<!--<div class="form-group-billing">
										<label for="Employer" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green">Employer</label> 
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 billing-select2">
											<input class="form-control input-sm-header-billing js-remove-err autocomplete-ajax" id="js-employer" data-url="api/getreferringprovider/MQ==" name="employer_detail" type="text">
											<input id="employer_id" name="employer_id" type="hidden">
											<span style='display:none;'><small class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>Choose valid employer</small></span>
										</div>                                 
									</div>-->

									<div class="form-group-billing">
									
										<?php 
											$copay_detail = (!empty($claim_detail_val_edit->id))?App\Models\Payments\PMTInfoV1::getPaymentInfo($claim_detail_val_edit->id):[];
											
											if(@$rest_field == 'yes'){
												$disabled_class = 'disabled';
												$readonly_class = 'readonly';
												$date = '';
											}
											else{
												$disabled_class = '';
												$readonly_class = '';
												$date = 'call-datepicker';
											}
											$credit= "hide";
											$other_type = "";
											 if(!empty($copay_detail)){
												$disabled_class = "disabled";
												$readonly_class = "readonly";  
												$date = '';                              
												if($copay_detail->payment_mode == "Credit") {
													$credit = "";
													$other_type = "hide"; 
												}
											   
											 }
										?> 											
										<label for="Copay" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green">Co-Pay</label> 
										<div class="col-lg-4 col-md-4 col-sm-3 col-xs-4 select2-white-popup">
											{!! Form::select('copay',['' => '--','Check' => 'Check','Cash' => 'Cash','Credit' => 'CC'],@$copay_detail->payment_mode,['class'=>'form-control select2 js-copay-select',$disabled_class]) !!}
										</div>
										<label for="pos" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label-billing med-green p-l-0">Amt</label> 
										<div class="col-lg-4 col-md-4 col-sm-3 col-xs-5">
											{!! Form::text('copay_amt',(@$claim_detail_val_edit->copay_amt != 0? @$claim_detail_val_edit->copay_amt:''),['class'=>'form-control input-sm-header-billing', 'maxlength' => '7',$readonly_class]) !!}
										</div>    
									</div>
																				
									<div class="form-group-billing js-show-check <?php echo $other_type;?>">
										{!! Form::label('mode', 'Check#',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
										<div class="col-lg-9 col-md-9 col-sm-7 col-xs-10">
											{!! Form::text('check_no',isset($copay_detail->check_no)?$copay_detail->check_no:'',['class'=>'form-control input-sm-header-billing js_need_regex', 'readonly' => 'readonly', 'maxlength'=>'25']) !!}
										</div>                          
									</div> 
									
									<div class="form-group-billing <?php echo $credit;?> js-show-card-type">
										{!! Form::label('mode', 'Card Type',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 p-r-0 control-label-billing med-green']) !!} 
										<div class="col-lg-9 col-md-9 col-sm-7 col-xs-10 select2-white-popup">                                  
											{!! Form::select('card_type', ['' => '--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],isset($copay_detail->card_type)?$copay_detail->card_type:'',['class'=>'select2 form-control', $disabled_class]) !!}
										</div>                          
									</div> 
									
									<div class="form-group-billing <?php echo $credit; ?> js-show-card-type">
										{!! Form::label('card_no', 'Card No',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600 js-copay-label ']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-7 col-xs-10">
											{!! Form::text('card_no',isset($copay_detail->card_no)?$copay_detail->card_no:'',['class'=>'form-control input-sm-header-billing', 'readonly' => 'readonly']) !!}
										</div>                          
									</div> 
									
									<div class="form-group-billing">
										{!! Form::label('mode', 'Date',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
										<div class="col-lg-9 col-md-9 col-sm-7 col-xs-10">
											<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i> 
											{!! Form::text('check_date',(isset($copay_detail->check_date) &&($copay_detail->check_date != '0000-00-00'))?date('m/d/Y', strtotime($copay_detail->check_date)):'',['class'=>'dm-date form-control input-sm-header-billing '.$date, $readonly_class]) !!}
										</div>                          
									</div> 

									<div class="form-group-billing">
										{!! Form::label('mode', 'Ref',  ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
										<div class="col-lg-9 col-md-9 col-sm-7 col-xs-10 billing-select2">
											{!! Form::text('copay_detail',isset($copay_detail->reference)?$copay_detail->reference:'',['class'=>'form-control input-sm-header-billing js_need_regex', $readonly_class, 'maxlength' => '20']) !!}
										</div>                          
									</div>
									<!--<div class="form-group-billing">
										<label for="mode" class="col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label-billing med-green">Details</label> 
										<div class="col-lg-9 col-md-9 col-sm-8 col-xs-10 billing-select2">
											<input class="form-control input-sm-header-billing" name="copay_detail" type="text">
										</div>                          
									</div>-->                                                        

								</div><!-- /.box-body -->
							</div><!--  2nd Content Ends -->
						</div><!--  Box Ends -->
					</div><!-- Only general details Content Ends -->
					<!-- Display ICD orders from E-superbill -->          
				</div><!-- General Details Full width Ends -->

				@if(!empty($claim_detail_val_edit)) 
				<?php 
					$icd_lists = array_flip(array_combine(range(1, count(explode(',', $claim_detail_val_edit->icd_codes))), explode(',', $claim_detail_val_edit->icd_codes)));
					$icd = App\Models\Icd::getIcdValues($claim_detail_val_edit->icd_codes); 
				?>
				@endif
				<div id="js-count-icd">
					<!-- Display ICD orders from E-superbill -->          
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-green no-b-t">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding"><!-- ICD Details Starts here -->
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
								<span class="font600 bg-white padding-0-4">Diagnosis - ICD 10</span>
							</div>
							<div class="box-body form-horizontal margin-t-10">
								<div class="form-group-billing">                            
									<label for="icd1" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">1</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd1',@$icd[1],['class'=>'form-control input-sm-header-billing js-icd bg-white', 'data-val'=>"1",'id'=>'icd1']) !!}
										<span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
									</div> 
									{!! $errors->first('icd1', '<p class = "med-red"> :message</p>')  !!}  
								</div>

								<div class="form-group-billing">                            
									<label for="icd2" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">2</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd2',@$icd[2],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"2",'id'=>'icd2']) !!}
										<span id="icd2" class="icd-hover">@if(!empty($icd[2])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
									</div>
									{!! $errors->first('icd2', '<p class = "med-red"> :message</p>')  !!}  
								</div>

								<div class="form-group-billing">                            
									<label for="icd3" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">3</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd3',@$icd[3],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"3",'id'=>'icd3']) !!}
										<span id="icd3" class="icd-hover">@if(!empty($icd[3])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
									</div>
									{!! $errors->first('icd3', '<p class = "med-red"> :message</p>')  !!}  										
								</div>

								<div class="js-display-err"></div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">

							<div class="box-body form-horizontal margin-t-10">
								<div class="form-group-billing">                            
									<label for="icd4" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">4</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd4',@$icd[4],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"4",'id'=>'icd4']) !!}
										<span id="icd4" class="icd-hover">@if(!empty($icd[4])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
									</div>
									{!! $errors->first('icd4', '<p class = "med-red"> :message</p>')  !!}  
								</div>

								<div class="form-group-billing">                            
									<label for="icd5" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">5</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd5',@$icd[5],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"5",'id'=>'icd5']) !!}
										<span id="icd5" class="icd-hover">@if(!empty($icd[5])){{App\Models\Icd::getIcdDescription($icd[5])}}@endif</span>
									</div> 
									{!! $errors->first('icd5', '<p class = "med-red"> :message</p>')  !!}  
								</div>

								<div class="form-group-billing margin-b-5">                            
									<label for="icd6" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">6</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd6',@$icd[6],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"6",'id'=>'icd6']) !!}
										<span id="icd6" class="icd-hover">@if(!empty($icd[6])){{App\Models\Icd::getIcdDescription($icd[6])}}@endif</span>
									</div> 
									{!! $errors->first('icd6', '<p class = "med-red"> :message</p>')  !!}  
								</div>
								
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-8">
								<span class="font600">&emsp;</span>
							</div>
							<div class="box-body form-horizontal margin-t-10">
								<div class="form-group-billing">                                                    
									<label for="icd7" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">7</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd7',@$icd[7],['class'=>'form-control input-sm-header-billing js-icd bg-white', 'data-val'=>"7",'id'=>'icd7']) !!}
										<span id="icd7" class="icd-hover">@if(!empty($icd[7])){{App\Models\Icd::getIcdDescription($icd[7])}}@endif</span>
									</div> 
									{!! $errors->first('icd7', '<p class = "med-red"> :message</p>')  !!} 
								</div>

								<div class="form-group-billing">                                                   
									<label for="icd8" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">8</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd8',@$icd[8],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"8",'id'=>'icd8']) !!}                    
										<span id="icd8" class="icd-hover">@if(!empty($icd[8])){{App\Models\Icd::getIcdDescription($icd[8])}}@endif</span>
									</div>
									{!! $errors->first('icd8', '<p class = "med-red"> :message</p>')  !!} 
								</div>

								<div class="form-group-billing">                                                   
									<label for="icd9" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">9</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd9',@$icd[9],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"9",'id'=>'icd9']) !!}                     
										<span id="icd9" class="icd-hover">@if(!empty($icd[9])){{App\Models\Icd::getIcdDescription($icd[9])}}@endif</span>
									</div>
									{!! $errors->first('icd9', '<p class = "med-red"> :message</p>')  !!} 
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 no-padding"><!-- ICD Details Starts here -->
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
								<span class="font600">&emsp;</span>
							</div>
							<div class="box-body form-horizontal margin-t-10">


								<div class="form-group-billing">                                                                         
									<label for="icd10" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">10</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd10',@$icd[10],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"10",'id'=>'icd10']) !!}                   
										<span id="icd10" class="icd-hover">@if(!empty($icd[10])){{App\Models\Icd::getIcdDescription($icd[10])}}@endif</span>
									</div>
									{!! $errors->first('icd10', '<p class = "med-red"> :message</p>')  !!} 
								</div>

								<div class="form-group-billing">                                                   
									<label for="icd11" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">11</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd11',@$icd[11],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"11",'id'=>'icd11']) !!}                      
										<span id="icd11" class="icd-hover">@if(!empty($icd[11])){{App\Models\Icd::getIcdDescription($icd[11])}}@endif</span>
									</div> 
									{!! $errors->first('icd11', '<p class = "med-red"> :message</p>')  !!} 
								</div>

								<div class="form-group-billing margin-b-5">                                                   
									<label for="icd12" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">12</label> 
									<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
										{!! Form::text('icd12',@$icd[12],['class'=>'form-control input-sm-header-billing js-icd bg-white','data-val'=>"12",'id'=>'icd12']) !!}                 
										<span id="icd12" class="icd-hover">@if(!empty($icd[12])){{App\Models\Icd::getIcdDescription($icd[12])}}@endif</span>
									</div>     
									{!! $errors->first('icd12', '<p class = "med-red"> :message</p>')  !!} 
								</div>    

							</div>
						</div>
					</div>
				</div> 


				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
					<ul class="billing line-height-26 border-radius-4 no-padding mobile-width billing-charge-table" id="">
						<li class="billing-grid">
							<table class="table-billing-view">
								<thead>
									<tr>
										<th class=" td-c-7">From</th>                                                
										<th class=" td-c-7">To</th>                                
										<th class="td-c-8">CPT</th>
										<th class="td-c-4">M 1</th>
										<th class="td-c-4">M 2</th>
										<th class="td-c-4">M 3</th>    
										<th class="td-c-4">M 4</th>  
										<th class="td-c-12">ICD Pnts</th>
										<th class="td-c-3">Un</th>
										<th class="td-c-6">($)</th>
									</tr>
								</thead>
							</table>                                     
						</li>
						<!-- Display CPT from E-superbill -->
						
						<?php
							$count = 6;
							$count_cnt = 0;
							if (!empty($claim_detail_val_edit)) {
								$cpt_codes = explode(',', $claim_detail_val_edit->cpt_codes);
								$count_cnt = count($cpt_codes);
								if ($count_cnt > 6)
									$count = 6;
								$cpt_icd = explode('::', $claim_detail_val_edit->cpt_codes_icd);
							}
							$modifier_readonly = "readonly";
						?>								
						<!-- Display CPT from E-superbill -->							

						@if(!empty($claim_detail_val_edit->dosdetails))
						<?php if (count($claim_detail_val_edit->dosdetails) > $count) $count = count($claim_detail_val_edit->dosdetails); ?>
						<div class="js-append-parent">
						@for($i=0;$i<$count;$i++)
						<?php 
							$date_to = '';
							$date_from = ''; 
							if (!empty($claim_detail_val_edit->dosdetails[$i]->dos_to)) {
								$date_to = (@$claim_detail_val_edit->dosdetails[$i]->dos_to && $claim_detail_val_edit->dosdetails[$i]->dos_to != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claim_detail_val_edit->dosdetails[$i]->dos_to)) : '';
							}
							if (!empty($claim_detail_val_edit->dosdetails[$i]->dos_from)) {
								$date_from = (@$claim_detail_val_edit->dosdetails[$i]->dos_from && $claim_detail_val_edit->dosdetails[$i]->dos_from != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claim_detail_val_edit->dosdetails[$i]->dos_from)) : '';
							}							
							$icd_map = isset($claim_detail_val_edit->dosdetails[$i]->cpt_icd_map_key) ? array_combine(range(1, count(explode(',', $claim_detail_val_edit->dosdetails[$i]->cpt_icd_map_key))), explode(',', $claim_detail_val_edit->dosdetails[$i]->cpt_icd_map_key)) : '';
							$style = '';
							if ($i >= 6 && $count <6) {
								$style = "style = display:none;";
							}
							$dos_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claim_detail_val_edit->dosdetails[$i]->id,'encode');
							$modifier_readonly = "readonly";
							$insurance_payment_count = App\Models\Payments\PMTInfoV1::checkpaymentDone(@$claim_detail_val_edit->id);
							$charges_readonly = ($insurance_payment_count>0)?"readonly":"";
						?>
						
						<li id = "js-modifier-list-{{$i}}" class="billing-grid js-validate-lineitem js-disable-div-{{$i}}" <?php echo $style; ?>>
							<table class="table-billing-view superbill-claim">
								<tbody>
									<tr>
										<td class="td-c-7"><input type="text" <?php if(@$rest_field == 'yes') echo "readonly = 'readonly'"; ?> data-postition="left_first_row" class="js_validate_date from_dos dm-date billing-noborder js_from_date textboxrow" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}"   onchange="datevalidation(<?php echo $i; ?>)"></td>									
										<td class="td-c-7"><input type="text" <?php if(@$rest_field == 'yes') echo "readonly = 'readonly'"; ?> class="js_validate_date to_dos dm-date billing-noborder js_to_date textboxrow" name=<?php echo "dos_to[" . $i . "]"; ?>  value = "{{@$date_to}}" onchange="todatevalidation(<?php echo $i; ?>)"></td>
										<td class="td-c-8">
											<input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder textboxrow" value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_code}}"name= <?php echo "cpt[" . $i . "]"; ?> >
											<input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claim_detail_val_edit->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
										</td>									
										<td class="td-c-4">
										<input type="hidden" class="billing-noborder js-hidden-cpt" value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_code}}">
										{!! Form::text('modifier1['.$i.']',@$claim_detail_val_edit->dosdetails[$i]->modifier1,['class'=>'form-control textboxrow billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier1-'.$i , $modifier_readonly, 'data-cpt' => @$claim_detail_val_edit->dosdetails[$i]->cpt_code]) !!}</td>
										<td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier2,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2,$modifier_readonly,'id' =>'modifier2-'.$i]) !!}</td>
										<td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier3,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2,$modifier_readonly,'id' =>'modifier3-'.$i]) !!}</td>
										<td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier4,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2,$modifier_readonly,'id' =>'modifier4-'.$i]) !!}</td>
										 
										@for($j=1;$j<=4;$j++)
											<td class="td-c-3"> 
											<input type="text" class="icd_pointer textboxrow icd_pointer_popup" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_map[$j] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
											<?php //echo ($j != 12) ? '<span class="billing-pipeline">|</span>' : '' ?>
											</td>
										@endfor
										
										<td class="td-c-3"><input class="cpt_unit billing-noborder" type="text" id="<?php echo $i ?>"  maxlength = 5 name=<?php echo "unit[" . $i . "]"; ?> value = "{{@$claim_detail_val_edit->dosdetails[$i]->unit}}" {{$charges_readonly}}></td>
										<td class="td-c-6 "><input type="text"class = "js-charge form-control input-sm-header-billing bg-white billing-noborder js_charge_amt text-right" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value = "{{@$claim_detail_val_edit->dosdetails[$i]->charge}}" {{$charges_readonly}}>
										<input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_allowed_amt}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
										<input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_icd_code}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()">
										<input type="hidden" class="cpt_icd_map_key billing-nb " value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_icd_map_key}}" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>                              
										<input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden">
										
									</tr>
								</tbody>
							</table>                                     
						</li>                
						@endfor
						</div>
						@else
						<div class="js-append-parent">                    
							<?php $dos_date = (!empty($claim_detail_val_edit)) ? date('m/d/Y', strtotime($claim_detail_val_edit->date_of_service)) : ''; ?>
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
							<li id = "js-modifier-list-{{$i}}" class="billing-grid js-disable-div-{{$i}}" <?php echo $style; ?>>
								<table class="table-billing-view superbill-claim">
									<tbody>
										<tr>
											<td class="td-c-2" tabindex="0"><input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight flat-red" onClick = "highlightcheckbox(<?php echo $i; ?>,this)"></td>  
											<td class="td-c-6"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date js_from_date dm-date billing-noborder" name=<?php echo "dos_from[" . $i . "]"; ?>  onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
											<td class="td-c-6"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date dm-date billing-noborder" name=<?php echo "dos_to[" . $i . "]"; ?>  onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
											<td class="td-c-8">
												<input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder" tabindex = -1 value = "<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('cpt_hcpcs') : ''; ?>" name= <?php echo "cpt[" . $i . "]"; ?> >
												<input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claim_detail_val_edit->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
											</td>
											<td class="td-c-4">{!! Form::text('modifier1['.$i.']',@$claim_detail_val_edit->dosdetails[$i]->modifier1,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier1-'.$i]) !!}</td>
											<td class="td-c-4">{!! Form::text('modifier2['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier2,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier2-'.$i]) !!}</td>
											<td class="td-c-4">{!! Form::text('modifier3['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier3,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier3-'.$i ]) !!}</td>
											<td class="td-c-4">{!! Form::text('modifier4['.$i.']' ,@$claim_detail_val_edit->dosdetails[$i]->modifier4,['class'=>'billing-noborder js-modifier bg-white', 'maxlength' => 2, 'id' =>'modifier4-'.$i]) !!}</td>
											
											<td class="td-c-10">
												<?php $a = array();
												$cpt_icd_key = '' ?>
												@for($j=1;$j<=4;$j++)
												<input type="text" class="icd_pointer_popup billing-icd-pointers" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_lists[$icd_map[$j]] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
											<?php echo ($j != 4) ? ' <span class="billing-pipeline">|</span>' : ''; ?>
											<?php if (!empty($icd_map[$j]))
												$key = array_push($a, $icd_lists[$icd_map[$j]]);
											?>
												@endfor             
											</td>
											<td class="td-c-3"><input class="cpt_unit billing-noborder" value= "<?php echo isset($cpt_codes[$i]) ? 1 : '' ?>" maxlength = 5 type="text" id="<?php echo $i ?>" name=<?php echo "unit[" . $i . "]"; ?> ></td>
											<td class="td-c-6"><input type="text" class = "js-charge js_charge_amt billing-noborder" id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value="<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('billed_amount') : ''; ?>">
											<input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$icd_val_split}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()"></td>
											<input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claim_detail_val_edit->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
											<input type="hidden" class="cpt_icd_map_key billing-nb" value = "<?php echo!empty($a) ? implode(',', $a) : ''; ?>" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>                              
										</tr>
									</tbody>
								</table>                                     
							</li>                
							@endfor
						</div>
					@endif
					</ul>

					<div class="margin-t-m-8 margin-b-5">
						<span class="append cur-pointer font600 med-green" style="display:none;"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i> Add</span>                
					</div>
				</div>

				<div style="margin-bottom: 20px;" class="pull-right"> 
					<span class=" med-green font600" >Total Charges : </span>
					<span class="med-orange font600 margin-l-20"><input type="text" readonly = "readonly"name = "total_charge" class="js-total billing-noborder text-right td-c-50">
					</span>
				</div>


				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-13">
					<input type="text" name="note" placeholder="Notes" class="form-control input-sm-modal-billing">
				</div>                        

				
				<!--
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 no-padding">
					<div class="payment-links">
					</div>            
				</div>
				-->
				
				<div class="box-footer space20">
					<div id="claim_editcharge_footer_{{@$claim_detail_val_edit->claim_number}}" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						{!! Form::submit('Save', ['class'=>'btn btn-medcubics-small padding-2-8 js-claimeditcharge-form-submit','id'=>'editcharge-form-submit_'.@$claim_detail_val_edit->claim_number]) !!}
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small padding-2-8' ,'data-dismiss'=>"modal"]) !!}
					</div>	  
				</div>
				

			</div>
		</div><!-- /.box-body -->                                
	{!! Form::close() !!} 

@push('view.scripts')
<script type="text/javascript">
    $('#authorization').attr('autocomplete','off');
</script>
@endpush