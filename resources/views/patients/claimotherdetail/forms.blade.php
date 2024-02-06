<div class="box box-view no-shadow no-border"><!--  Box Starts -->
	<div class="box-body form-horizontal no-padding">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group-billing"> 
				<?php $patient_id = Route::getCurrentRoute()->parameter('patient_id'); ?>
				 {!!Form::hidden('patient_id', $patient_id)!!}                            
				{!! Form::label('family_plan', 'Family Plannings', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('family_plan',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
				</div>                        
				<div class="col-sm-1"></div>
			</div>
			<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
			<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.other_details") }}' />
			
			@if(strpos($currnet_page, 'create') !== false)
					{!!Form::hidden('claim_id',null,['class' => 'js-popclaim_id'])!!}
				@endif
			<div class="form-group-billing">                             
				{!! Form::label('original_reference', 'Original Reference', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('original_reference',null,['class'=>'form-control js-letters-caps-format input-sm-modal-billing']) !!}                                    
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('ref_id_qualifier', 'Reference ID Qualifier', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('reference_id',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">                            
			<div class="form-group-billing">                             
				{!! Form::label('resubmission_no', 'Resubmission No', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('resubmission_no',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('medicalid_referral_no', 'Medicaid Referral no', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('medicalid_referral_no',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('service_auth_exception', 'Service Auth Exception', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
					{!! Form::text('service_auth_exception',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>

		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5 bg-brown">
			<div class="form-group-billing">                             
				{!! Form::label('non_avaiability', 'Non Availability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
					{!! Form::text('non_avaiability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('sponsor_status', ' Sponsor Status', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
					{!! Form::text('sponsor_status',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('sponsor_grade', 'Sponsor Grade', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
					{!! Form::text('sponsor_grade',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                                                        
			</div>

		</div>

		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5 bg-brown">
			<div class="form-group-billing">                             
				{!! Form::label('branch_service', 'Branch of Service', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
					{!! Form::text('branch_of_service',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>

			<div class="form-group-billing">                             
				{!! Form::label('special_program', 'Special Program', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
					{!! Form::text('special_program',null,['class'=>'form-control input-sm-modal-billing']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>                                                       

			<div class="form-group-billing">                             
				{!! Form::label('start_end_date', 'Effective Start / End Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
					<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('effective_start')"></i>
					{!! Form::text('effective_start',(isset($claimotherdetail->effective_start) && $claimotherdetail->effective_start != '0000-00-00' && $claimotherdetail->effective_start != '')?@date('m/d/Y',strtotime($claimotherdetail->effective_start)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                        
				</div>                        
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
					{!! Form::text('effective_end',(isset($claimotherdetail->effective_end) && $claimotherdetail->effective_end != '0000-00-00' && $claimotherdetail->effective_end != '0000-00-00')?@date('m/d/Y',strtotime($claimotherdetail->effective_end)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                        
				</div>
			</div>                                                        

		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding  margin-t-10">                      
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
				<div class="form-group-billing">                             
					{!! Form::label('disability_percent', 'Percent Permanent Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('disability_percent',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('Service_status', 'Service Status', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('service_status',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('service_card_effective', 'Service Card Effective', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('serive_card_effective',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('handicaped_program', 'Handicapped Program', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('handicaped_program',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

			</div>


			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
				<div class="form-group-billing">                             
					{!! Form::label('branch_of_service', 'Branch of Service', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('branch_of_service',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('service_grade', 'Service Grade', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('service_grade',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('non_available_statement', 'Non Available Statement', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('non_available_statement',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>                                                        
			</div>                                                   
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10 bg-brown">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('therapy_type', 'Therapy Type', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('therapy_type',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>

				<div class="form-group-billing">                             
					{!! Form::label('class_finding', 'Class Finding', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
						{!! Form::text('class_finding',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>                                                        

			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('systemic Condition', 'Systemic Condition', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('systemic_condition',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>                                                          

			</div>
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
				<div class="form-group-billing">                             
					{!! Form::label('nature_of_condition', 'Nature Of Condition', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('nature_of_condition',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>                                                                                                                                                                                                  
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
				<div class="form-group-billing">                             
					{!! Form::label('complication_indicator', 'Complication Indicator', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('complication_indicator',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>                                                                                                                                                                                                  
			</div>
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10 bg-brown">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('date_of_last_xray', 'Date of Last X-Ray', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('date_of_last_xray')"></i>
						{!! Form::text('date_of_last_xray',(isset($claimotherdetail->date_of_last_xray) && $claimotherdetail->date_of_last_xray != '0000-00-00'&& $claimotherdetail->date_of_last_xray != '')?@date('m/d/Y',strtotime($claimotherdetail->date_of_last_xray)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                       
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('total_disability', 'Total Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('total_disability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('hospitalization', 'Hospitalization', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('hospitalization',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

				<div class="form-group-billing">                             
					{!! Form::label('prescription_date', 'Prescription Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('prescription_date')"></i>
						 {!! Form::text('prescription_date',(isset($claimotherdetail->prescription_date) && $claimotherdetail->prescription_date != '0000-00-00' && $claimotherdetail->prescription_date != '' )?@date('m/d/Y',strtotime($claimotherdetail->prescription_date)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                      
						
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

				<div class="form-group-billing">                             
					{!! Form::label('month_treated', 'Months Treated', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('month_treated',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

			</div>


			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('consultations_dates', 'Consultation Dates', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('consultations_dates')"></i>
						{!! Form::text('consultations_dates',(isset($claimotherdetail->consultations_dates) && $claimotherdetail->consultations_dates != '0000-00-00'&& $claimotherdetail->consultations_dates != '')?@date('m/d/Y',strtotime($claimotherdetail->consultations_dates)):'',['class'=>'form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                       
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('partial_disability', 'Partial Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('partial_disability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('assumed_relinquished_care', 'Assumed Relinquished Care', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('assumed_relinquished_care',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

				<div class="form-group-billing">                             
					{!! Form::label('date_of_last_visit', 'Date of Last Visit', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('date_of_last_visit')"></i>
						{!! Form::text('date_of_last_visit',(isset($claimotherdetail->date_of_last_visit) && $claimotherdetail->date_of_last_visit != '0000-00-00'&& $claimotherdetail->date_of_last_visit != '')?@date('m/d/Y',strtotime($claimotherdetail->date_of_last_visit)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                        
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

				<div class="form-group-billing">                             
					{!! Form::label('date_of_manifestation', 'Date of Manifestation', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('date_of_manifestation')"></i>
						{!! Form::text('date_of_manifestation',(isset($claimotherdetail->date_of_manifestation) && $claimotherdetail->date_of_manifestation != '0000-00-00' && $claimotherdetail->date_of_manifestation != '')?@date('m/d/Y',strtotime($claimotherdetail->date_of_manifestation)):'',['class'=>'dm-date form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                         
					</div>                        
					<div class="col-sm-1"></div>
				</div> 

			</div>
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding margin-t-10">        
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
				<div class="form-group-billing">                             
					{!! Form::label('epsdt', 'EPSDT', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('epsdt',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     

				<div class="form-group-billing">                             
					{!! Form::label('ambulatory_service_req', 'Ambulatory Service Req', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('ambulatory_service_req',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     

				<div class="form-group-billing">                             
					{!! Form::label('levels_of_submission', 'Levels of Submission', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('levels_of_submission',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     

				<div class="form-group-billing">                             
					{!! Form::label('weight_unit', 'Weight Units', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('weight_unit',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">
				<div class="form-group-billing">                             
					{!! Form::label('third_party_liability', 'Third Party Liability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('third_party_liability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     

				<div class="form-group-billing">                             
					{!! Form::label('birth_weight', 'Birth Weight', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('birth_weight',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                                                        
				</div>     

			</div>  
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10 bg-brown">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('pregnant', 'Pregnant', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('pregnant',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('last_menstrual_period', 'Last Menstrual Period', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('last_menstrual_period',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('referal_items', 'Referal Items', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('referal_item',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>                                                         

			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
				<div class="form-group-billing">                             
					{!! Form::label('estimated_dob', 'Estimated DOB', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick="iconclick('estimated_dob')"></i>
						 {!! Form::text('estimated_dob',(isset($claimotherdetail->estimated_dob) && $claimotherdetail->estimated_dob != '0000-00-00'&& $claimotherdetail->estimated_dob != '')?@date('m/d/Y',strtotime($claimotherdetail->estimated_dob)):'',['class'=>'form-control input-sm-modal-billing dm-date call-datepicker','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}                                         
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('findings', 'Findings', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('findings',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>  

				<div class="form-group-billing">                             
					{!! Form::label('referal_code', 'Referal Codes', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
						{!! Form::text('referal_code',null,['class'=>'form-control input-sm-modal-billing']) !!} 
					</div>                        
					<div class="col-sm-1"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-t-5">
			<div class="form-group-billing">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					{!! Form::textarea('note',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Notes']) !!} 
				</div>                        
				<div class="col-sm-1"></div>
			</div>
		</div>
	</div><!-- /.box-body -->                                
</div><!-- /.box Ends Contact Details-->
<div class="modal-footer">
{!! Form::submit("Submit", ['class'=>'btn btn-medcubics form-group js-submit-popup', 'id' => 'claimotherdetail']) !!}
<button class="btn btn-medcubics close_popup" type="button">Cancel</button>
</div>
 <script>
$(document).ready(function() {
     $(document).delegate('input[name="effective_start"]', 'change', function(){
        $('form.js-submit-popupform').bootstrapValidator('revalidateField', 'effective_end'); 
         $('form.js-submit-popupform').bootstrapValidator('revalidateField', 'effective_start'); 
    });
      $(document).delegate('input[name="effective_end"]', 'change', function(){
        $('form.js-submit-popupform').bootstrapValidator('revalidateField', 'effective_start'); 
         $('form.js-submit-popupform').bootstrapValidator('revalidateField', 'effective_end'); 
    });     
    $('.js-submit-popupform').bootstrapValidator({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        effective_start: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: '{{ trans("common.validation.date_format") }}'
                    },
                    callback: {
                        message: '{{ trans("practice/patients/claim_detail.validation.unable_to_work_from_call") }}',
                        callback: function (value, validator) {

                            var m = validator.getFieldElements('effective_end').val();
                            var n = value;
                            if (m != '') {
                                return (n == '') ? false : true;
                            }
                            else
                                return true;

                        }
                    }
                }
            },
            effective_end: {
                message: '',
                trigger: 'keyup change',
                validators: {
                    date: {
                        format: 'MM/DD/YYYY',
                        message: '{{ trans("common.validation.date_format") }}'
                    },
                    callback: {
                        message: '{{ trans("practice/patients/claim_detail.validation.unable_to_work_to_call") }}',
                        callback: function (value, validator) {

                            var m = validator.getFieldElements('effective_start').val();
                            var n = value;
                            var current_date = new Date(n);
                            if (current_date != 'Invalid Date' && n != '' && m != '') {
                                var getdate = daydiff(parseDate(m), parseDate(n));
                                return (getdate > 0) ? true : false;
                            }
                            else if (m != '') {
                                return (n == '') ? false : true;
                            }
                            else
                                return true;
                        }

                    }
                }
            },                                          
        }
    });
 
});

</script>     