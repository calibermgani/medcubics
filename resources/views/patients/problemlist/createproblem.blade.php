{!! Form::open(['id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'popupmedcubicsform js_problem_create']) !!}   
<?php
	$priority = ['High' => 'High','Moderate' => 'Moderate','Low' => 'Low'];
	$status = ['Assigned' => 'Assigned','Inprocess' => 'Inprocess','Completed' => 'Completed'];
	?>
	<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.problemlist") }}' />
	
	<div class="problem_create">
		<div class="modal-sm-usps ">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close js_problemlist_update" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"> New Workbench</h4>
				</div>
				<div class="modal-body no-padding" >
					<div class="box box-view no-shadow no-border"><!--  Box Starts -->
						<div class="box-body form-horizontal">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Left side Content Starts -->
								<div class="form-horizontal"><!-- Box Starts -->
									<input type="hidden" class="js_patient_id_claims" value="{{ @$id }}" />
									<input type="hidden" class="js_claim_list" value="{{ @$claim_number }}" />
									<div class="form-group">
										{!! Form::label('Claim No', 'Claim No', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!}                   
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
											{!! Form::select('claim_search', [''=>'-- Select --']+$claims_number,null,['class'=>'select2 form-control input-sm-modal-billing claim_no']) !!}
										</div>
									</div> 
									<input type="hidden" class="js_patient_id" value="{{$id}}"/>
									<div class="form-group">
										<?php
										$practice = App\Http\Helpers\Helpers::user_list();
										?>
										{!! Form::label('assigned_to', 'Assigned To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup"> 
											{!! Form::select('assign_user_id', [''=>'-- Select --']+$practice,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('followup_date', 'Followup Date', ['class'=>'col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!}                                                  
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
											<i class="fa fa-calendar-o form-icon-billing"></i>  {!! Form::text('fllowup_date',null,['autocomplete'=>'off' ,'id'=>'followup_date','placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control followup_date input-sm-header-billing form-cursor dm-date datepicker ']) !!} 

										</div>
									</div> 

									<div class="form-group">
										{!! Form::label('Priority', 'Priority', ['class'=>'col-lg-4  col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!}                                                  
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">  
											{!! Form::select('priority', [''=>'-- Select --']+$priority,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
										</div>                                       
									</div>  

									<div class="form-group">					
										{!! Form::label('Status', 'Status', ['class'=>'col-lg-4  col-md-4 col-sm-4 col-xs-12 control-label-popup star']) !!}                  
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup"> 
											{!! Form::select('status', [''=>'-- Select --']+$status,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
										</div>
									</div>  
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-10"> 
											{!! Form::textarea('description',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Description']) !!}
										</div>
									</div> 
								</div>                                                        
							</div><!--  Left side Content Ends -->
						</div>
						<div id="js_create_problem_list_footer" class="modal-footer m-b-m-15 show">
							{!! Form::submit("Save", ['class'=>'btn btn-medcubics-small form-group js_problem_save','accesskey'=>'s']) !!}
							<button class="btn btn-medcubics-small js_problemlist_update" data-dismiss="modal" type="button">Cancel</button>
						</div>
						<div id="js_create_problem_list_loading" class="modal-footer m-b-m-15 text-centre hide">
							<i class="fa fa-spinner fa-spin med-green text-centre"></i> Processing
						</div>
					</div><!-- /.box-body -->                                
				</div><!-- /.box Ends Contact Details-->
			</div>
		</div><!-- /.modal-content -->
	</div>
{!! Form::close() !!}