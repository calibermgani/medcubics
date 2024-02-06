<div id="ajax_loading_part">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
		<div class="col-lg-12 margin-t-m-10 no-padding">
			<div class="box-body form-horizontal  bg-white no-padding">

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;"><!-- Inner width Starts -->  

						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
							<h4 class="margin-b-1 med-orange">Category</h4>
						</div>

						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 p-r-0">
							<h4 class="margin-b-1 med-orange text-right">Count</h4>
						</div>
					</div><!-- Inner width Ends -->

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;"><!-- Inner width Starts -->  
							<table class="table">
								<tbody>
									
									<tr class="@if($document_data->patient_document > 0) tab_details form-cursor  @endif" data-tab-id="pat_doc" data-tab-name="Patient Documents" data-tab-title="Patient Documents" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->patient_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Documents :  <span class="normal-font font13"><i>Registration Documents, Insurance Card Copy, Driving License, Consent Forms...</i></span></h4></td>                                    
										<td class="med-orange font600 text-right font16">{{ $document_data->patient_document }}</td>
									</tr>
									
									<tr class="@if($document_data->eligibility_document > 0) tab_details form-cursor  @endif" data-tab-id="elig_doc" data-tab-name="Eligibility" data-tab-title="Eligibility & Benefits" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->eligibility_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Eligibility & Benefits :   <span class="normal-font font13"><i>Payer Eligibility Reports, Benefit Verification Forms...</i></span></h4></td>                                    
										<td class="med-orange font600 text-right font16">{{ $document_data->eligibility_document }}</td>
									</tr>
									
									<tr class="@if($document_data->authorization_document > 0) tab_details form-cursor  @endif" data-tab-id="auth_doc" data-tab-name="Authorization" data-tab-title="Authorization Documents" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->authorization_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Authorization Forms :   <span class="normal-font font13"><i> Authorization Forms, Referral Forms...</i></span></h4></td>
										<td class="med-orange font600 text-right font16">{{ $document_data->authorization_document }}</td>
									</tr>
									
									<tr class="@if($document_data->procedure_document > 0) tab_details form-cursor  @endif" data-tab-id="prod_doc" data-tab-name="Procedure Doc" data-tab-title="Procedure Documents" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->procedure_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Procedure Documents :  <span class="normal-font font13"><i>Superbills, Surgery Reports, Procedure Reports, Medical Records...</i></span></h4></td>                                    
										<td class="med-orange font600 text-right font16">{{ $document_data->procedure_document }}</td>
									</tr>
									
									<tr class="@if($document_data->edi_document > 0) tab_details form-cursor  @endif" data-tab-id="edi_doc" data-tab-name="EDI Reports" data-tab-title="EDI Reports" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->edi_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> EDI Reports : <span class="normal-font font13"><i>Clearinghouse Reports, Payer Acknowledgments, Rejections...</i></span></h4></td>
										<td class="med-orange font600 text-right font16">{{ $document_data->edi_document }}</td>
									</tr>
									
									<tr class="@if($document_data->payer_document > 0) tab_details form-cursor  @endif" data-tab-id="pay_doc" data-tab-name="Payer Reports" data-tab-title="Payer Reports" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->payer_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Payer Reports : <span class="normal-font font13"><i>ERA/EOB, Correspondence Letter, Appeal Letters...</i></span></h4></td>
										<td class="med-orange font600 text-right font16">{{ $document_data->payer_document }}</td>
									</tr>
									
									<tr class="@if($document_data->clinical_document > 0) tab_details form-cursor  @endif" data-tab-id="cli_doc" data-tab-name="Clinical Doc" data-tab-title="Clinical Documents" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->clinical_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Clinical Documents : <span class="normal-font font13"><i>Signed Clinical Notes, CT/MRI Reports, X-ray Reports, Lab Results...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->clinical_document }}</td>
									</tr>
									
									<tr class="@if($document_data->patient_corresp_document > 0) tab_details form-cursor  @endif" data-tab-id="pat_let" data-tab-name="Patient Letter" data-tab-title="Patient Letters" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->patient_corresp_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Patient Correspondence : <span class="normal-font font13"><i>Patient Statements, Patient Payments Letter, Collection Letter...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->patient_corresp_document }}</td>
									</tr>
									
									<tr class="@if($document_data->prescription_document > 0) tab_details form-cursor  @endif" data-tab-id="pres_doc" data-tab-name="Prescription" data-tab-title="Prescription" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->prescription_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Prescriptions : <span class="normal-font font13"><i>Prescriptions, E-prescriptions Logs, Medications...</i></span></h4></td>
										<td class="med-orange font600 text-right font16">{{ $document_data->prescription_document }}</td>
									</tr>
									
									<tr class="@if($document_data->other_document > 0) tab_details form-cursor  @endif" data-tab-id="oth_doc" data-tab-name="Other Documen" data-tab-title="Other Documents" data-tab-model="patients">
										<td> <h4 class="font16 @if($document_data->other_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Other Documents : <span class="normal-font font13"><i>Scan Files, Fax Documents...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->other_document }}</td>
									</tr>
									
									<tr class="@if($document_data->facility_document > 0) tab_details form-cursor  @endif" data-tab-id="fac_doc" data-tab-name="Facility Docu" data-tab-title="Facility Documents" data-tab-model="facility">
										<td> <h4 class="font16 @if($document_data->facility_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Facility Documents : <span class="normal-font font13"><i>Payer Contracts, Other Documents...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->facility_document }}</td>
									</tr>
									<!-- 
									Added new category for provider document 
									Revision 1 - Ref: MR-2472 08 Aug 2019: Selva 
									-->
									<tr class="@if($document_data->provider_document > 0) tab_details form-cursor  @endif" data-tab-id="prov_doc" data-tab-name="Provider Doc" data-tab-title="Provider Documents" data-tab-model="provider">
										<td> <h4 class="font16 @if($document_data->provider_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Provider Documents : <span class="normal-font font13"><i>NPI Letter, W9 Form, State Medical License, Facesheet/Superbill...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->provider_document }}</td>
									</tr>
									
									<tr  class="@if($document_data->group_document > 0) tab_details form-cursor  @endif" data-tab-id="group_doc" data-tab-name="Group Doc" data-tab-title="Group Documents" data-tab-model="group">
										<td> <h4 class="font16 @if($document_data->group_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Group Documents : <span class="normal-font font13"><i>Common...</i></span></h4></td>                          
										<td class="med-orange font600 text-right font16">{{ $document_data->group_document }}</td>
									</tr>									
								</tbody>
							</table>
						</div><!-- Inner width Ends -->  
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="create_document" class="js_common_modal_popup modal fade">
		<div class="modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Filter</h4>
				</div>
				<div class="modal-body">
					<div class="box-body no-bottom no-padding"><!--Background color for Inner Content Starts -->
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
							<form>
							<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.documents") }}' />
							<div class="box no-shadow no-bottom">

								<!-- form start -->
								<div class="box-body  form-horizontal no-bottom">                        
									<div class="form-group">
										{!! Form::label('title', 'Title', ['class'=>'col-lg-4 col-md-4 col-sm-12 control-label star']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-12 @if($errors->first('title')) error @endif">
											{!! Form::text('title',null,['class'=>'form-control']) !!} 
											{!! $errors->first('title', '<p> :message</p>')  !!} 
										</div>
									</div> 

									<div class="form-group">
										{!! Form::label('category', 'Category', ['class'=>'col-lg-4 col-md-4 col-sm-12 control-label star']) !!} 
										<div class="col-lg-8 col-md-8 col-sm-12 @if($errors->first('category')) error @endif">
											{!! Form::select('category', array('' => '-- Select --') ,null,['class'=>'select2 form-control','id'=>'category']) !!}
											{!! $errors->first('category', '<p> :message</p>')  !!} 
										</div>
									</div> 

									<div class="form-group">
										{!! Form::label('category', 'Notes', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!}
										<div class="col-lg-12 col-md-12 col-sm-12">
										  {!! Form::textarea('title',null,['class'=>'form-control']) !!} 
										</div>
									</div>
								</div><!-- /.box-body -->
								<div class="box-footer no-padding">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										{!! Form::submit('View', ['class'=>'btn btn-medcubics-small form-group']) !!}
										<a href="javascript:void(0)" data-url="">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small', 'data-dismiss'=>'modal']) !!}</a>
									</div>
								</div><!-- /.box-footer -->
							</div><!-- /.box -->
							
							{!! Form::close() !!}
						</div><!--/.col (left) -->
					</div><!--Background color for Inner Content Ends -->	                                
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- Modal Light Box Ends --> 
</div>