<!-- Help Modal Light Box starts -->   
<?php 
    if(!isset($get_default_timezone)){
        $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
    }
?>
<div id="js-help-modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Help : <span id="js-help-modal-title"></span></h4>
			</div>
			<div class="modal-body">
				<p id="js-help-modal-msg" style="word-wrap:break-word"></p>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal  Ends-->

<div id="form-modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Facility</h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<div class="col-md-12">
					<button class="btn btn-medcubics">Save</button>
					<a href="#" class="btn btn-medcubics-small-modal" data-dismiss="modal">Cancel</a>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="provider_scheduler_modal" class="modal fade" data-keyboard="false" >
	<div class="modal-dialog" >
		<div class="modal-content" >
			<div class="modal-header">
				<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Schedule availability by facility</h4>
			</div>
			<div class="modal-body">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="patient_insurance_model" class="modal fade in">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Patient Insurance List</h4>
			</div>
			<div class="modal-body">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
	
<div id="patient_insurance_cat_model" class="modal fade in">
	<div class="modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Choose Insurance Category</h4>
			</div>
			<div class="modal-body">
				<div class="text-center margin-t-10 margin-b-10">
					{!! Form::radio('pmt_otherins_cat', 'Primary',null,['class'=>'js-inscat-type','id'=>'c-primary', 'data-insurance_cat' => 'Primary']) !!} 
					{!! Form::label('c-primary', 'Primary',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					
					{!! Form::radio('pmt_otherins_cat', 'Secondary', null,['class'=>'js-inscat-type','id'=>'c-secondary', 'data-insurance_cat' => 'Secondary']) !!} 
					{!! Form::label('c-secondary', 'Secondary',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
					
					{!! Form::radio('pmt_otherins_cat', 'Tertiary', null,['class'=>'js-inscat-type','id'=>'c-tertiary', 'data-insurance_cat' => 'Tertiary']) !!} 
					{!! Form::label('c-tertiary', 'Tertiary',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
				</div>
				<div class="text-center">
					<button class="btn btn-medcubics-small js-otherpmt_ins_cat" type="button">Ok</button>
					<button class="btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
	
<div id="document_add_modal" class="modal fade" data-keyboard="false" >  
<!-- above div removed style (style="overflow-y:inherit;") scroll not working in main document page by selvakumar  -->
	<div class="modal-md" id="dynamic-size">
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Document</h4>
			</div>
			<div class="modal-body">
			</div>-->
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="patient_statement_modal" class="modal fade" data-keyboard="false">
	<div class="js_insurance_search_popup modal-md">
		<div class="modal-content no-padding">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Address List</h4>
			</div>
			<div class="modal-body">
				<div class="js_inscontent"> </div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<!-- Session Alert Window Starts  -->
<div id="session_model" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Warning </h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center font600">Your session has been expired!. click Yes to continue </div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button [focus]='true'class="js_session_confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="js_session_confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<!-- Schedular popup Move -->
<div id="js_schedular_popup_model" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Warning !!</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center font600">Your session has been expired!. click Yes to continue </div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_schedular_confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="js_schedular_confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<!-- Schedular popup Move -->

 <!-- Patient Note Alert Window Starts  -->
<div id="patientnote_model" class="js_common_modal_popup modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Information</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="redirect_url" value="">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_note_confirm btn btn-medcubics-small js_common_modal_popup_save close_popup" id="true" type="button">Ok</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<!-- Superbills Alert Window Starts  -->
<div id="superbill_modal" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center">Are you sure to remove this?</div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="js_modal_confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="js-model-insurance-details" class="modal fade" data-keyboard="false">
	<div class="modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close js_insurance_search_modal_close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Insurance Details</h4>
			</div>
			<div class="form-horizontal modal-body">
				<div class="form-group">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
							{!! Form::select('insurace_search_category_modal', ['insurance_name'=>'Insurance Name','payerid' => 'Payer ID','address' => 'Address'],'insurance_name',['class'=>'select2 form-control js_insurace_search_category_modal', 'id'=>'js_insurace_search_category_modal']) !!}
						</div>
						<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 input-group input-group-sm">
							<input name="modal_search_insurance_keyword" type="text" class="form-control js_modal_search_insurance_keyword" placeholder="Search key words">
							<span class="input-group-btn">
								<button class="btn btn-flat btn-medgreen js_modal_search_insurance_button" type="button">Search</button>
							</span>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<span id='search_insurance_keyword_err' class='help-block med-red hide' data-bv-validator='notEmpty' data-bv-for='search_insurance_keyword_err' data-bv-result='INVALID'><span class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="search_insurance_keyword_err_content">Please enter search keyword!</span></span>
					</div>
				</div>
				<div class="form-horizontal pat-ins-search-scroll" id="insurance_search_result"></div>
			</div>
		</div>
	</div>
</div>

<!-- Superbills Alert Window Starts  -->
<!-- Processing Popup Alert Window Starts  -->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide" id="js_wait_alert_confirm">
	<div class="js_wait_alert_confirm">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center js_processing_image hide"> <i class="fa fa-spinner fa-spin font20 med-green"></i> Processing </div>
	</div>
</div>

<div id="js_wait_popup" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"> <i class="fa fa-spinner fa-spin font20 med-green"></i> Processing </div>
					</div>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Processing Popup Alert Window Ends  -->

<!-- Delete Popup Window Starts  -->
<div id="js_confirm_popup" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body text-center med-green font600">Are you sure to delete?</div>
			<div class="modal-footer">
				<button class="width-60 js_confirm_box btn btn-medcubics-small" type="button">Yes</button>
				<button class="width-60 cancel btn btn-medcubics-small" type="button" data-dismiss="modal">No</button>
            </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_arlist_remove" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center">Are you sure to remove?</div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="js_modal_confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_patient_demo_remove" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center">Are you sure to delete?</div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Yes</button>
					<button class="js_modal_confirm js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_patient_demo_info_box" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="js_confirm_patient_demo_info_content" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Yes</button>
					<button class="js_modal_confirm js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_patient_demo_info_box1" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="js_confirm_patient_demo_info_content1" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm1 js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Continue</button>
					<button class="js_modal_confirm1 js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">Ignore</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<!-- Patient Statement Window Starts  -->
<div id="patientstatement_model" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Patient Statement</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="js_patientbalance col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green-wo-span text-center font600"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_submit_type btn btn-medcubics-small width-60 js_hide" data-unique='' data-name='preview' type="button" >Preview</button>

					<button class="js_submit_type btn btn-medcubics-small width-60 js_hide" 'data-original-title'="Send PDF Statement" data-unique='' data-name='sendstatement' type="button" ><i class="fa fa-file-pdf-o"></i> PDF</button>

					<button class="js_submit_type btn btn-medcubics-small width-60 js_hide" 'data-original-title'="Send CSV Statement" data-unique='' data-name='sendcsvstatement' type="button" ><i class="fa fa-file-excel-o"></i> CSV</button>

					<button class="js_submit_type btn btn-medcubics-small width-60 js_hide" 'data-original-title'="Send XML Statement" data-unique='' data-name='sendxmlstatement' type="button" ><i class="fa fa-file-code-o"></i> XML</button>
					
					<button class="js_submit_type js_emailstatement btn btn-medcubics-small width-60 js_hide" data-unique='' data-name='emailstatement' type="button"><i class="fa fa-envelope-o"></i> Email</button>
					
					<div class="js_loading hide">
						<i class="fa fa-spinner fa-spin font20 med-green"></i> Processing
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="ticketassign_modal" class="modal fade" data-keyboard="false">
	<div class="modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Ticket</h4>
			</div>
			<div class="modal-body">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="claims_error_model" class="js_common_modal_popup modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Information</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 modal-desc"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_note_confirm btn btn-medcubics-small js_common_modal_popup_save" id="true" type="button" data-dismiss="modal">Ok</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="js_confirm_box_charges" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="js_confirm_box_charges_content" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm1 js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Yes</button>
					<button class="js_modal_confirm1 js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_box_claims" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="js_confirm_box_claims_content" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm1 js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Yes</button>
					<button class="js_modal_confirm1 js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_confirm_box_charges_payment" class="modal fade js_common_modal_popup">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>		
				<h4 class="modal-title">Alert</h4>				
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="js_confirm_box_charges_content_payments" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="js_modal_confirm1 js_common_modal_popup_save btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal" >Yes</button>
					<button class="js_modal_confirm1 js_common_modal_popup_cancel btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal" accesskey="">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="js_move_confirm_modal" class="modal fade in">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">Alert</h4></div>
            <div class="modal-body">
				<div class="text-center med-orange font600">Note : {{ trans("practice/patients/patients.validation.category_exist") }}</div>
				<div class="text-center med-green font600">Are you sure to move?</div>
			</div>
            <div class="modal-footer">
                <button class="confirm btn btn-medcubics-small js_move_confirm_yes" type="button">Yes</button>
                <button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">No</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div id="js_newpatient_scheduler" class="modal fade js_datepicker_scroll" data-keyboard="false" style="padding-top: 50px;">
	<div class="modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close js_pclose_form" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">New Patient</h4>
			</div>
			<div class="modal-body">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="patientarchive_model" class="js_common_modal_popup modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
            <div class="modal-header"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">Alert</h4></div>
            <div class="modal-body">
				<div class="text-center med-green font600">Check already existing insurance?</div>
			</div>
            <div class="modal-footer">
				<button class="confirm btn btn-medcubics-small js_alert_archive" type="button" data-name="archive">Insurance Archive</button>
                <button class="confirm btn btn-medcubics-small js_alert_archive" type="button" data-name="newinsurance">Add Insurance</button>
                <button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="js_move_insurance_model" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button><h4 class="modal-title">Move Insurance</h4></div>
            <div class="modal-body">
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Help Modal Light Box starts -->  
<div id="js-phone-popup" class="modal fade">
	<div class="modal-md-calling">
		<div class="modal-content clsViewAppointment">			
			<div class="modal-body no-padding yes-border med-border-color">
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal  Ends-->

<!-- Modal Light Box Address starts -->  
<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends -->

<div id="popup_facility_msg" class="modal fade" data-keyboard="false" >
	<div class="modal-dialog" >
		<div class="modal-content" >
			<div class="modal-header">
				<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<div id="js_confirm_patient_demo_info_content1" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
					<p class="text-center med-green font600 margin-b-5">There is no facility available for the practice, You will be redirected to add a facility</p></div>
				<center>
					<a href="{{ url('facility') }}" class="js_note_confirm btn btn-medcubics-small margin-b-10 js_common_modal_popup_save close_popup" id="true" >OK</a>
					<a href="#" class="btn btn-medcubics-small margin-b-10" data-dismiss="modal">Cancel</a>	
				</center>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="popup_provider_msg" class="modal fade" data-keyboard="false" >
	<div class="modal-dialog" >
		<div class="modal-content" >
			<div class="modal-header">
				<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<div id="js_confirm_patient_demo_info_content1" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
				<p class="text-center med-green font600 margin-b-5">There is no provider available for this practice, You will be redirected to add a provider</p>
				<center>
					<a href="{{ url('provider') }}" class="js_note_confirm btn btn-medcubics-small margin-b-10 js_common_modal_popup_save close_popup" id="true" >OK</a>
					<a href="#" class="btn btn-medcubics-small margin-b-10" data-dismiss="modal">Cancel</a>	
				</center>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="popup_provider_scheduler_msg" class="modal fade" data-keyboard="false" >
	<div class="modal-dialog" >
		<div class="modal-content" >
			<div class="modal-header">
				<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<div id="js_confirm_patient_demo_info_content1" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center"></div>
				<p class="text-center med-green font600 margin-b-5">No provider is scheduled with a facility, You will be redirected to schedule a provider</p>
				<center>
					<a href="{{ url('practiceproviderschedulerlist') }}" class="js_note_confirm btn btn-medcubics-small margin-b-10 js_common_modal_popup_save close_popup" id="true" >OK</a>
					<a href="#" class="btn btn-medcubics-small margin-b-10" data-dismiss="modal">Cancel</a>	
				</center>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->

<div id="create_notes" class="js_common_modal_popup modal fade">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title margin-l-5">Notes</h4>
            </div>
            <div class="modal-body">                                          
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 

<div id="statement_hold" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title margin-l-5">Hold</h4>
            </div>
            <div class="modal-body">
				<input type="hidden" id="patientId" value="">
                <!-- Statement hold reason block start -->
				<?php $stmt_holdreason = App\Models\STMTHoldReason::getStmtHoldReasonList(); ?>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                   <div class="box box-info no-shadow no-bottom">
                       <div class="box-body form-horizontal">
                           <div class="form-group">
                               {!! Form::label('Hold Reason', 'Hold Reason', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                               <div class="col-lg-8 col-md-8 col-sm-12 ">
									{!! Form::select('hold_reason', array(''=>'-- Select --')+(array)@$stmt_holdreason,null,['id' => 'hold_reason', 'class'=>'select2 form-control js_hold_blk']) !!} 
									{!! $errors->first('hold_reason', '<p> :message</p>')  !!}
                               </div>
                           </div>
                           <div class="form-group">
                               {!! Form::label('Hold Release Date', 'Hold Release Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                               <div class="col-lg-6 col-md-6 col-sm-9 ">
                                   {!! Form::text('hold_release_date',null,['id'=>'hold_release_date','class'=>'form-control form-cursor dm-date js_hold_blk','tabindex'=>'80','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                                   {!! $errors->first('hold_release_date', '<p> :message</p>')  !!}
                               </div>
                           </div>
                       </div>
                   </div>
               </div>				
				<!-- Statement hold reason block ends -->
				<center>
					<button class="confirm btn btn-medcubics-small js-ar-hold" type="button">Hold</button>
					<button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
				</center>
            </div>
        </div>
    </div>
</div>

<?php /* Selected field export related block. uncomment and use it.
<!-- Selected field export starts -->
<div id="export_fields_model" class="modal fade in">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Export</h4>
			</div>
			<div class="modal-body">
				@if(isset($export_fields) && !empty($export_fields))					
					<p>Select fields to export</p>		
					<p>
						{!! Form::checkbox('check_all', 'Check_all', '', ["class" => "js_active_lineitem","id"=> "check_all"]) !!}&nbsp;
						<label for="check_all" class="no-bottom">&nbsp;All</label>
					</p>	
					@foreach(array_chunk($export_fields, 5) as $chunk)
						<p>
							@foreach($chunk as $item)
								<span>
									{!! Form::checkbox('exp_flds[]', $item,'', ["class" => "js_active_lineitem srch_exp_flds","id"=>$item]) !!}&nbsp;
									<label for="{{$item}}" class="no-bottom">&nbsp;{{ $item }}</label>								
								</span>
							@endforeach 
						</p>
					@endforeach
					
				@endif
				<?php 
					$url = isset($expUrl) ?$expUrl : url('/') ; //'reports/charges/export/';
				?>				
				
                <a class="js_srch_export btn btn-medcubics-small" href="{{ url($url.'xlsx') }}" data-url="{{ url($url) }}" data-option = "xlsx">
                    <i class="fa fa-file-excel-o"></i> Excel
                </a>
				
                <a class="js_srch_export btn btn-medcubics-small" href="{{ url($url.'pdf') }}" data-url="{{ url($url) }}" data-option = "pdf">
                    <i class="fa fa-file-pdf-o" data-placement="right"  data-toggle="tooltip" data-original-title="pdf"></i> PDF
                </a>
				
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
<!-- Selected field export ends -->
*/ ?>


<div id="uploadedPatient" class="modal fade">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close js_common_modal_popup_cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title margin-l-5">Upload</h4>
            </div>
			{!! Form::open(['method' => 'POST','url' => 'uploaded_patient','enctype'=>'multipart/form-data','id'=>'js-uploadpatient-validator']) !!}
            <div class="modal-body">	
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                   <div class="box box-info no-shadow no-bottom">
                       <div class="box-body form-horizontal">
                           <div class="form-group">
                               {!! Form::label('Document', 'Document', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                               <div class="col-lg-8 col-md-8 col-sm-12 ">
									{!! Form::file('filefield',null,['id' => 'filefield', 'class'=>'form-control']) !!} 
                               </div>
                           </div>
                           <div class="form-group">
                               {!! Form::label('Notes', 'Notes', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                               <div class="col-lg-8 col-md-6 col-sm-9 ">
									{!! Form::textarea('msg',null,['id'=>'msg','class'=>'form-control form-cursor']) !!}
                               </div>
                           </div>
                       </div>
                   </div>
               </div>				
				<!-- Statement hold reason block ends -->
				<center>
					<button class="confirm btn btn-medcubics-small" type="submit">Submit</button>
					<button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
				</center>
            </div>
			{!! Form::close() !!}
        </div>
    </div>
</div><!-- Modal Light Box Ends --> 
<script type="text/javascript">

<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>