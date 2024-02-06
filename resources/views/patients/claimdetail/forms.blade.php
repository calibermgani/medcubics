<h4 class="modal-title"> Claim Details</h4>
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20">           
            <?php 
				$currnet_page = Route::getFacadeRoot()->current()->uri();
            	$patient_id = Route::getCurrentRoute()->parameter('patient_id'); 
			?>
            <!--{!!Form::hidden('patient_id', $patient_id)!!}-->
            <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.charges_edit_claim_detail") }}' />			
            @if(strpos($currnet_page, 'create') !== false)
            {!!Form::hidden('claim_id',null,['class' => 'js-popclaim_id'])!!}
            @endif
        </div> 

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="form-group margin-b-10">                             
                {!! Form::label('Employment (Box 10a)', 'Employment Status (Box 10a)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                    {!! Form::radio('is_employment', 'Yes',null,['class'=>'flat-red','id'=>'c-em-y']) !!} {!! Form::label('c-em-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('is_employment', 'No',1,['class'=>'flat-red','id'=>'c-em-n']) !!} {!! Form::label('c-em-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group margin-b-10">  
            <?php $diabled = (@$claimdetail->is_autoaccident == "Yes")?"":"disabled";?>                           
                {!! Form::label('Auto Accident', 'Auto Accident (Box 10b) / State', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                    {!! Form::radio('is_autoaccident', 'Yes',null,['class'=>'flat-red','id'=>'c-au-y']) !!} {!! Form::label('c-au-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('is_autoaccident', 'No',1,['class'=>'flat-red','id'=>'c-au-n']) !!} {!! Form::label('c-au-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>        
                <div class="col-lg-2 col-md-2 col-sm-2 select2-white-popup">
                    {!! Form::select('autoaccident_state',array('0' => '--')+(array)$state,@$claimdetail->autoaccident_state,['class'=>'form-control select2 input-sm-header-billing js-auto-accident', $diabled]) !!}   
				</div>
            </div>

            <div class="form-group margin-b-10">                             
                {!! Form::label('Other Accident', 'Other Accident (Box 10c)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::radio('is_otheraccident', 'Yes',null,['class'=>'flat-red','id'=>'c-ot-y']) !!} {!! Form::label('c-ot-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('is_otheraccident', 'No',1,['class'=>'flat-red','id'=>'c-ot-n']) !!} {!! Form::label('c-ot-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                        
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">                             
                {!! Form::label('Claim Codes', 'Claim Codes (Box 10d)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::text('claim_code',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing', 'maxlength' => '19']) !!}
                </div>                        
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">                             
                {!! Form::label('Other Claim ID (Box 11b)', 'Other Claim ID (Box 11b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                                          
                 <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
                      {!! Form::text('otherclaimid_qual',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing','maxlength' => '2']) !!}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ">
                    {!! Form::text('otherclaimid',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing','maxlength' => '28']) !!}  
                </div>
                 <div class="col-sm-1"></div>
            </div>

            <div class="form-group">                             
                {!! Form::label('box12', 'Print Signature on File (Box 12)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::radio('print_signature_onfile_box12', 'Yes',1,['class'=>'flat-red','id'=>'c-pr-y']) !!} {!! Form::label('c-pr-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('print_signature_onfile_box12', 'No',null,['class'=>'flat-red','id'=>'c-pr-n']) !!} {!! Form::label('c-pr-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}                                     
                </div>                                                        
            </div>

            <div class="form-group margin-b-10">                             
                {!! Form::label('box13', 'Print Signature on File (Box 13)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::radio('print_signature_onfile_box13', 'Yes',1,['class'=>'flat-red','id'=>'c-si-y']) !!} {!! Form::label('c-si-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('print_signature_onfile_box13', 'No',null,['class'=>'flat-red','id'=>'c-si-n']) !!} {!! Form::label('c-si-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}                                     
                </div>                                                        
            </div>
             <?php 
				$gender = @$patient_lists->gender; 
				if($gender == "Female") {
					$readonly = "";
					$datepick =  "call-datepicker";
				} else{
					$readonly = "readonly";
					$datepick =  "";
				}
			?>
            <div class="form-group">                             
                {!! Form::label('Date of LMP (Box 14)', 'Date of LMP (Box 14)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick= "iconclick('illness_box14')"></i>
                    {!! Form::text('illness_box14',(isset($claimdetail->illness_box14) && $claimdetail->illness_box14 != '1970-01-01' && $claimdetail->illness_box14 != '0000-00-00')?@date('m/d/Y',strtotime($claimdetail->illness_box14)):'',['autocomplete' => 'off','class'=>'form-control input-sm-header-billing inputmask-class dm-date '.$datepick,'placeholder'=>Config::get('siteconfigs.default_date_format'), $readonly]) !!}     
                </div>                        
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">                             
                {!! Form::label('Additional Claim Info (Box 19)', 'Additional Claim Info (Box 19)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::textarea('additional_claim_info',null,['class'=>'form-control input-sm-header-billing', 'maxlength' => 71]) !!}
				</div>    
            </div>

            <div class="form-group margin-b-10">                             
                {!! Form::label('Resubmission Code (Box 22)', 'Resubmission Code (Box 22)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('resubmission_code',array('' => '-- Select --', '7' => '7 - Replacement of prior claim', '8' => '8 - Void/cancel of prior claim'),null,['class'=>'form-control input-sm-header-billing']) !!}
                </div>                
            </div>

            <div class="form-group margin-b-10">  
                {!! Form::label('Original Reference No. (Box 22)', 'Original Reference No. (Box 22)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::text('original_ref_no',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing', 'maxlength' => '18']) !!}
                </div>
            </div>

            <div class="form-group margin-b-10">  
                {!! Form::label('Prior Authorization ', 'Prior Authorization ', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                    {!! Form::select('box23_type',array(''=>'-- Select --', 'referal_number' => 'Referral Number', 'mamography' => 'Mammography Number', 'clia_no' => 'CLIA Number'),($clia_no=='')?@$claimdetail->box23_type:'clia_no',['class'=>'form-control input-sm-header-billing', 'id' => 'js-pre-auth']) !!}                                     
                </div>
            </div>

            <div class="form-group">   
                {!! Form::label('Box 23', 'Prior Authorization (Box 23)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                       
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::text('box_23',(isset($clia_no) && $clia_no!='')?$clia_no:@$claimdetail->box_23,(isset($clia_no) && $clia_no!='' || isset($claimdetail->box_23) && $claimdetail->box_23!='')?['autocomplete'=>'off','class'=>'form-control input-sm-header-billing','maxlength' => '10']:['class'=>'form-control input-sm-header-billing','maxlength' => '29']) !!}
                </div>                                                        
            </div> 

            <div class="form-group margin-b-10">                             
                {!! Form::label('Accept Assignments (Box 27)', 'Accept Assignments (Box 27)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                    {!! Form::radio('accept_assignment', 'Yes',1,['class'=>'flat-red','id'=>'c-acc-y']) !!} {!! Form::label('c-acc-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
                    {!! Form::radio('accept_assignment', 'No',null,['class'=>'flat-red','id'=>'c-acc-n']) !!} {!! Form::label('c-acc-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
                </div>                                                        
            </div>
            
			<div id="intro" class="collapse">    

				<div class="form-group margin-b-10">                             
					{!! Form::label('Provider Employed in Hospice?', 'Provider Employed in Hospice?', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
						{!! Form::radio('is_provider_employed', 'Yes',null,['class'=>'flat-red','id'=>'c-pro-y']) !!} {!! Form::label('c-pro-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!}
						&emsp; {!! Form::radio('is_provider_employed', 'No',true,['class'=>'flat-red','id'=>'c-pro-n']) !!} {!! Form::label('c-pro-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                        
					<div class="col-sm-1"></div>
				</div>

				<div class="form-group">
					{!! Form::label('Other Date QUAL', 'Other Date QUAL', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('other_date_qualifier',array(''=>'-- Select --', '454' => '454 - Initial Treatment', '304' =>'304 - Latest Visit or Consultation', '453' =>'453 - Acute Manifestation of a Chronic Condition', '439' =>'439 - Accident','455' =>'455 - Last X-ray', '471' =>'471 - Prescription','090' =>'090 - Report Start (Assumed Care Date)','091' =>'091 - Report End (Relinquished Care Date)','444' =>'444 - First Visit or Consultation'),null,['class'=>'form-control input-sm-header-billing']) !!}
					</div>                        
					<div class="col-sm-1"></div>
				</div>  
				
				<div class="form-group">
					{!! Form::label('Other Date (Box 15)', 'Other Date (Box 15)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick= "iconclick('other_date')"></i>   
						{!! Form::text('other_date',(isset($claimdetail->other_date) && $claimdetail->other_date != '1970-01-01' && $claimdetail->other_date != '0000-00-00')?@date('m/d/Y',strtotime($claimdetail->other_date)):'',['autocomplete'=>'off','class'=>'form-control input-sm-header-billing inputmask-class call-datepicker dm-date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
					</div>                        
					<div class="col-sm-1"></div>
				</div>   
				
				<div class="form-group">                             
					{!! Form::label('Unable to Work', 'Unable to Work (Box 16)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
						<i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick= "iconclick('unable_to_work_from')"></i>  
						{!! Form::text('unable_to_work_from',(isset($claimdetail->unable_to_work_from) && $claimdetail->unable_to_work_from != '1970-01-01' && $claimdetail->unable_to_work_from != '0000-00-00')?@date('m/d/Y',strtotime($claimdetail->unable_to_work_from)):'',['autocomplete'=>'off','class'=>'form-control call-datepicker dm-date input-sm-header-billing ','placeholder'=>"From"]) !!}
					</div>        

					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ">
						{!! Form::text('unable_to_work_to',(isset($claimdetail->unable_to_work_to) && $claimdetail->unable_to_work_to != '1970-01-01'  && $claimdetail->unable_to_work_to != '0000-00-00')?@date('m/d/Y',strtotime($claimdetail->unable_to_work_to)):'',['autocomplete'=>'off','class'=>'form-control input-sm-header-billing call-datepicker dm-date','placeholder'=>"To"]) !!}   
					</div>
				</div>
				<?php
					$data = 1;
					if ($data) {
						$provider_qual = array('' => '-- Select --', '0B' => '0B - State License Number', '1G' => '1G - Provider UPIN Number', 'G2' => 'G2 - Provider Commercial Number', 'LU' => 'LU - Location Number');
					} else {
						$provider_qual = array('' => '-- Select --', '0B' => '0B - State License Number', '1G' => '1G - Provider UPIN Number', 'G2' => 'G2 - Provider Commercial Number');
					}
					$provider_qual = array('' => '-- Select --', '0B' => '0B - State License Number', '1G' => '1G - Provider UPIN Number', 'G2' => 'G2 - Provider Commercial Number', 'LU' => 'LU - Location Number');
				?>
				<div class="form-group">                             
					{!! Form::label('Provider Qual (Box 17a)', 'Provider Qual (Box 17a)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('provider_qualifier',$provider_qual,null,['class'=>'form-control input-sm-header-billing js-disable-provider']) !!}                                      
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Provider Qual (Box 17a)', 'Provider Qual (Box 17a) Identifier', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::text('provider_otherid',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing js-disable-provider', 'maxlength' => '17']) !!}
					</div>    
				</div>            

				<div class="form-group margin-b-10">                             
					{!! Form::label('Outside Lab (Box 20)', 'Outside Lab (Box 20)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::radio('outside_lab', 'Yes',null,['class'=>'flat-red','id'=>'c-ou-y']) !!} {!! Form::label('c-ou-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
						{!! Form::radio('outside_lab', 'No',1,['class'=>'flat-red','id'=>'c-ou-n']) !!} {!! Form::label('c-ou-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                                                        
				</div>

				<div class="form-group">                             
					{!! Form::label('Outside Lab charges(Box 20)', 'Outside Lab Charges (Box 20)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::text('lab_charge',(isset($claimdetail->lab_charge) && $claimdetail->lab_charge != 0?$claimdetail->lab_charge:""),['autocomplete'=>'off','class'=>'form-control input-sm-header-billing js_need_regex', 'maxlength' => 9]) !!}           
					</div>                                                        
				</div>            

				<div class="form-group margin-b-10">                             
					{!! Form::label('Emergency (Box 24c)', 'Emergency (Box 24c)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::radio('emergency', 'Yes',null,['class'=>'flat-red','id'=>'c-eme-y']) !!} {!! Form::label('c-eme-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
						{!! Form::radio('emergency', 'No',1,['class'=>'flat-red','id'=>'c-eme-n']) !!} {!! Form::label('c-eme-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                                                        
				</div>
				
				<div class="form-group margin-b-10">                             
					{!! Form::label('EPSDT(Box 24h)', 'EPSDT (Box 24h)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::radio('epsdt', 'Yes',null,['class'=>'flat-red','id'=>'c-eps-y']) !!} {!! Form::label('c-eps-y', 'Yes',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
						{!! Form::radio('epsdt', 'No',1,['class'=>'flat-red','id'=>'c-eps-n']) !!} {!! Form::label('c-eps-n', 'No',['class'=>'med-darkgray font600 form-cursor']) !!}
					</div>                                                        
				</div>

				<div class="form-group">                             
					{!! Form::label('Facility Qual(Box 32b)', 'Facility Qual (Box 32b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('service_facility_qual',['' => '-- Select --','0B' =>'0B - State License Number','G2' => 'G2 - Provider Commercial Number','LU' => 'LU - Location Number'],null,['class'=>'form-control input-sm-header-billing js-disable-facility']) !!}
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Facility Other ID(Box 32b)', 'Facility Other ID (Box 32b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::text('facility_otherid',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing js-disable-facility', 'maxlength' => '12']) !!}
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Billing provider Qual(Box 33b)', 'Billing Provider Qual (Box 33b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
						{!! Form::select('billing_provider_qualifier',['' => '-- Select --','0B'=>'0B - State License Number','G2' => 'G2 - Provider Commercial Number','ZZ' => 'ZZ - Provider Taxonomy'],null,['class'=>'form-control input-sm-header-billing js-disable-billing']) !!}
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Billing provider OtherId (Box 33b)', 'Billing Provider Other ID (Box 33b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::text('billing_provider_otherid',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing js-disable-billing', 'maxlength' => '15']) !!}
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Rendering provider Qual (Box 24I)', 'Rendering provider Qual(Box 24I)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                    
						{!! Form::select('rendering_provider_qualifier',['' => '-- Select --','0B'=>'0B - State License Number', '1G'=>'1G - Provider UPIN Number','G2' => 'G2 - Provider Commercial Number','LU'=>'LU - Location Number','ZZ' => 'ZZ - Provider Taxonomy'],null,['class'=>'form-control input-sm-header-billing js-disable-rendering']) !!}
					</div>    
				</div>
				
				<div class="form-group">                             
					{!! Form::label('Rendering provider OtherId#(Box 24J)', 'Rendering provider OtherId (Box 24J)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
						{!! Form::text('rendering_provider_otherid',null,['autocomplete'=>'off','class'=>'form-control input-sm-header-billing js-disable-rendering', 'maxlength' => '11']) !!}
					</div>    
				</div>  
			</div>
            
            <button1 type="button" class="test btn btn-info-green pull-right block med-green more-hover margin-t-10" data-toggle="collapse" data-target="#intro">Show More</button1>

        </div>
    </div><!-- /.box-body -->                                
</div>
<div class="modal-footer">
    {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics-small form-group js-submit-popup', 'id' => 'claimdetail']) !!}     
    <button class="btn btn-medcubics-small close_popup" type="button">Cancel</button>
</div>
<script>
    
    $('button1').click(function(){
		$(this).text(function(i,old){
			return old=='Show More' ?  'Hide' : 'Show More';
		});
	});

    // Update by baskar - 28/02/19 - Start
    $("#js-pre-auth").on('change',function(){
        val = $(this).val();
        if(val=='clia_no'){
            $("input[name='box_23']").val('');
            $("input[name='box_23']").attr('maxlength',10);
            $("input[name='box_23']").attr('minlength',10);
        }
        else{
            $("input[name='box_23']").removeAttr('minlength');
            $("input[name='box_23']").attr('maxlength',29);
        }
    });
    // Update by baskar - 28/02/19 - End

    $(document).ready(function () {
        if($("input[name='outside_lab']:checked").val() != 'Yes'){
            $('input[name=lab_charge]').attr('disabled',true); 
        }else{
            $('input[name=lab_charge]').attr('disabled',false); 
        }
       // $('select[name="autoaccident_state"]').prop("disabled", true);
        $(document).delegate('input[name="box_23"]', 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'box23_type');
        });
        $(document).delegate('input[name="unable_to_work_to"]', 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'unable_to_work_to');
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'unable_to_work_from');
        });
        $(document).delegate('input[name="unable_to_work_from"]', 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'unable_to_work_from');
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'unable_to_work_to');
        });
        $(document).delegate('input[name="illness_box14"]', 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'illness_box14');
        });
        $(document).delegate('input[name="is_autoaccident"]', 'ifToggled change', function () { 
            var status = $('select[name="autoaccident_state"]').prop("disabled");
            if ($(this).val() == "Yes") {
                $('select[name="autoaccident_state"]').prop("disabled", false);
            } else {
                $('form#ClaimValidate').bootstrapValidator('revalidateField', 'is_autoaccident');
                $('select[name="autoaccident_state"]').select2("val", "0");
                $('select[name="autoaccident_state"]').prop("disabled", true);
            }
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'is_autoaccident');
        });
        $(document).delegate("input[name='other_date']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'other_date');
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'other_date_qualifier');
        });
        $(document).delegate("input[name='provider_otherid']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'provider_qualifier');
        });
        $(document).delegate("input[name='facility_otherid']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'service_facility_qual');
        });
        $(document).delegate("input[name='billing_provider_otherid']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'billing_provider_qualifier');
        });
         $(document).delegate("input[name='rendering_provider_otherid']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'rendering_provider_qualifier');
        });
        $(document).delegate("input[name='outside_lab']", 'ifToggled change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'lab_charge');
        });
        $(document).on("change", 'input[name="lab_charge"]', function(){
            var val = $(this).val();
            if(typeof val != "undefined" && !isNaN(val) && val != ""){
                $(this).val(parseFloat(val).toFixed(2));
            }
        });
        
        $(document).delegate("input[name='otherclaimid']", 'change', function () {
            $('form#ClaimValidate').bootstrapValidator('revalidateField', 'otherclaimid_qual');
        });
        
        $(document).delegate('input[name="original_ref_no"]', 'change', function () {           
           // $('form#ClaimValidate').bootstrapValidator('revalidateField', 'resubmission_code');
        
        });
        $(document).delegate('input[name="original_ref_no"]', 'keyup change', function () {          
           $("input[name='resubmission_code_value']").val($(this).val());
        });
        $('#ClaimValidate').bootstrapValidator({
            framework: 'bootstrap',
            excluded: ':disabled',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                box23_type: {
                    message: 'The date of service is invalid is invalid',
                    validators: {
                        callback: {
                            message: 'Select Prior authorization',
                            callback: function (value, validator, $field) {
                                auth_no = $('input[name="auth_no"]').val();
                                if (auth_no != '' && value != '') {
                                    $('#js_confirm_box_charges_content').html('{{ trans("practice/patients/claim_detail.validation.prior_auth_msg") }}');
                                    $("#js_confirm_box_charges")
										.modal({show: 'false', keyboard: false})
										.one('click', '.js_modal_confirm1', function (eve) {
											if ($(this).attr('id') == 'true' && value !== '') {
												$('form#ClaimValidate').bootstrapValidator('enableFieldValidators', 'box_23', true);
												clai_no = $('input[name = "facility_clai_no"]').val();
												if (value == 'clia_no' && clai_no != '' && typeof clai_no != 'undefined') {
													$("input[name='box_23']").attr('readonly', 'readonly').val(clai_no);
												} else if ($("input[name='box_23']").val() == '' || value == 'clia_no') {
													$("input[name='box_23']").attr('readonly', false).val("");
												}
												$('form#ClaimValidate').bootstrapValidator('revalidateField', 'box_23');
												$('input[name="auth_no"]').val("");
												return true;
											} else {
												if ($("input[name='box_23']").val() != '' && value == '') {
													return false;
												}
												$('form#ClaimValidate').bootstrapValidator('enableFieldValidators', 'box_23', false)
												$('form#ClaimValidate').bootstrapValidator('revalidateField', 'box_23');
												return true;
											}
										});
                                    return true;
                                }
                                else {
                                    if ($("input[name='box_23']").val() != '' && value == '') {
                                        return false;
                                    }
                                    if (value !== '') {
                                        $('form#ClaimValidate').bootstrapValidator('enableFieldValidators', 'box_23', true);
                                    } else {
                                        $('form#ClaimValidate').bootstrapValidator('enableFieldValidators', 'box_23', false);
                                    }
                                    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'box_23');
                                    return true;
                                }
                            }
                        }
                    }
                },
                box_23: {
                    //enabled: false,
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.box_23") }}',
                            callback: function (value, validator, $field) {
                                // Check the password strength
                            type = $("#js-pre-auth").val();
                            if(type=='clia_no')
                            if (value.length < 10) {
                                return {
                                    valid: false,
                                    message: 'It must be 10 characters'
                                };
                            }
                                return (value == '') ? false : true;
                            }
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9]+$/,
                            message: '{{ trans("common.validation.alphanumeric") }}'
                        }
                    }
                },
                unable_to_work_from: {
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
                                var m = validator.getFieldElements('unable_to_work_to').val();
                                var n = value;
                                if (m != '') {

                                    if (n == '') {
                                        return {
                                            valid: false,
                                            message: "Enter start date"
                                        }
                                    } else {
                                        return true;
                                    }
                                }
                                else
                                    return true;
                            }
                        }
                    }
                },
                unable_to_work_to: {
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

                                var m = validator.getFieldElements('unable_to_work_from').val();
                                var n = value;
                                var current_date = new Date(n);
                                if (current_date != 'Invalid Date' && n != '' && m != '') {
                                    var getdate = daydiff(parseDate(m), parseDate(n));
                                    if (getdate >= 0) {
                                        return true;
                                    } else {
                                        return {
                                            valid: false,
                                            message: '{{ trans("practice/patients/claim_detail.validation.unable_to_work_to_call") }}'
                                        }
                                    }
                                }
                                else if (m != '') {
                                    if (n == '') {
                                        return {
                                            valid: false,
                                            message: "Enter end date"
                                        }
                                    } else {
                                        return true;
                                    }
                                }
                                else
                                    return true;
                            }
                        }
                    }
                },
                hospitalization_to: {
                    validators: {
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{ trans("common.validation.date_format") }}'
                        }
                    }
                },
                is_autoaccident: {                   
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.is_autoaccident") }}',
                            callback: function (value, validator, $field) {
                                value = $('input[name=is_autoaccident]:checked').val();                                
                                $('form#ClaimValidate').bootstrapValidator('revalidateField', 'autoaccident_state');
                                return true;
                            }
                        }
                    }
                },
                autoaccident_state: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.is_autoaccident") }}',
                            callback: function (value, validator, $field) {
                                checked_val = $('input[name=is_autoaccident]:checked').val();                                
                                if (checked_val == "Yes" && value == 0) {
                                    return false;
                                } else {
                                    var selcetedVal = $('select[name="autoaccident_state"]').val();
                                    return true;
                                }
                                return true;
                            }
                        }
                    }
                },
                other_date_qualifier: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.other_date_qualifier") }}',
                            callback: function (value, validator, $field) {
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'other_date', true);
                                    return true;
                                } else {
                                    if ($("input[name='other_date']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'other_date', false);
                                        return true;
                                    }
                                    if ($("input[name='other_date']").val() != '') {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                },
                other_date: {
                    message: '',
                    validators: {
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{ trans("common.validation.date_format") }}'
                        },
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.other_date") }}',
                            callback: function (value, validator, $field) {
                                var other_date = $('input[name="other_date"]').val();
                                var current_date = new Date(other_date);
                                var d = new Date();
                                if (other_date != '' && d.getTime() < current_date.getTime()) {
                                    return {
                                        valid: false,
                                        message: future_date,
                                    };
                                }
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
                illness_box14: {
                    message: '',
                    validators: {
                        date: {
                            format: 'MM/DD/YYYY',
                            message: '{{ trans("common.validation.date_format") }}'
                        },
                        callback: {
                            message: '{{ trans("practice/patients/charges.validation.doi_future") }}',
                            callback: function (value, validator, $field) {
                                var lmp_date = value;
                                var current_date = new Date(lmp_date);
                                var d = new Date();
                                if (lmp_date != '' && d.getTime() < current_date.getTime()) {
                                    return {
                                        valid: false,
                                        message: future_date,
                                    };
                                }
                                return true;
                            }
                        }
                    }
                },
                provider_qualifier: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.provider_qualifier") }}',
                            callback: function (value, validator, $field) {                               
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'provider_otherid', true);
                                    return true;
                                } else {
                                    if ($("input[name='provider_otherid']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'provider_otherid', false);
                                        return true;
                                    }
                                    if ($("input[name='provider_otherid']").val() != '') {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                },
                provider_otherid: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.provider_otherid") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
                service_facility_qual: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.service_facility_qual") }}',
                            callback: function (value, validator, $field) {
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'facility_otherid', true);
                                    return true;
                                } else {
                                    if ($("input[name='facility_otherid']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'facility_otherid', false);
                                        return true;
                                    }
                                    if ($("input[name='facility_otherid']").val() != '') {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                },
                facility_otherid: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.facility_otherid") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
                billing_provider_qualifier: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.billing_provider_qualifier") }}',
                            callback: function (value, validator, $field) {                                
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'billing_provider_otherid', true);
                                    return true;
                                } else {
                                    if ($("input[name='billing_provider_otherid']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'billing_provider_otherid', false);
                                        return true;
                                    }
                                    if ($("input[name='billing_provider_otherid']").val() != '') {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                },
                billing_provider_otherid: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.billing_provider_otherid") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
                rendering_provider_qualifier: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.billing_provider_qualifier") }}',
                            callback: function (value, validator, $field) {
                                console.log(value);
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'rendering_provider_otherid', true);
                                    return true;
                                } else {
                                    if ($("input[name='rendering_provider_otherid']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'rendering_provider_otherid', false);
                                        return true;
                                    }
                                    if ($("input[name='rendering_provider_otherid']").val() != '') {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                },
                rendering_provider_otherid: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.billing_provider_otherid") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
                outside_lab: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.outside_lab") }}',
                            callback: function (value, validator, $field) {
                                value = $('input[name=outside_lab]:checked').val();
                                lab_charge = $('input[name=lab_charge]').val();
                                if (value == 'Yes') {                                   
                                    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'lab_charge');
                                    $('input[name=lab_charge]').attr('disabled',false);
                                    return true;
                                } else {
                                    $('input[name=lab_charge]').val('');
                                    $('form#ClaimValidate').bootstrapValidator('revalidateField', 'lab_charge');
                                    $('input[name=lab_charge]').attr('disabled',true); 
                                }
                                return true;
                            }
                        }
                    }
                },
                lab_charge: {
                    message: '',
                    trigger:"keyup change",
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.lab_charge") }}',
                            callback: function (value, validator, $field) {
                                checked_val = $('input[name=outside_lab]:checked').val();
                                console.log("asad");
                                if (checked_val == "Yes" && value == '') {
									return {
										valid: false,
										message: '{{ trans("practice/patients/claim_detail.validation.lab_charge") }}'
									};
                                } else if (value != '' && $.isNumeric(value)) {
									var regexp = (value.indexOf(".") == -1) ? /^[0-9]{0,8}$/ : /^[0-9.]{0,11}$/;
									if (!regexp.test(value)) {
										return {
											valid: false,
											message: maximum_amt
										};
									} else {                                            
										return true;
									}
								}
								return true;
                            },
                        },
                        regexp: {
                            regexp: /^[0-9,.]+$/,
                            message: '{{ trans("practice/patients/claim_detail.validation.lab_charge_numeric") }}'
                        }
                    }
                },
				otherclaimid_qual: {
                    trigger: 'keyup',
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.otherclaimid_qual") }}',
                            callback: function (value, validator, $field) {
                                console.log(value);
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'otherclaimid', true);
                                    return true;
                                } else {
                                    if ($("input[name='otherclaimid']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'otherclaimid', false);
                                        return true;
                                    }
                                    if ($("input[name='otherclaimid']").val() != '') {
                                        return false;
                                    }
                                }
                                return true;

                            }
                        }
                    }
                },
                otherclaimid: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.otherclaimid") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },
				/*
                resubmission_code: {                    
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.resubmission_code") }}',
                            callback: function (value, validator, $field) {
								if($('input[name="resubmission_code_value"]').length){
									var resubcode = $('input[name="resubmission_code_value"]').val(); 
								} else {
									var resubcode = "";
									enabledisablevalidatorclaimform('enableFieldValidators', 'original_ref_no', false);
									return true;
								}	
                                console.log("resubmission code"+resubcode);
                                console.log(value);
                                if (value != '') {
                                    enabledisablevalidatorclaimform('enableFieldValidators', 'original_ref_no', true);
                                    return true;
                                } else if(resubcode == '' && value == ''){
                                    return {
                                        valid:false,
                                        message:"Select resubmission code"
                                    }
                                }else {
                                    if ($("input[name='original_ref_no']").val() == '') {
                                        enabledisablevalidatorclaimform('enableFieldValidators', 'original_ref_no', false);
                                        return true;
                                    }
                                    if ($("input[name='original_ref_no']").val() != '') {
                                        return false;
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                original_ref_no: {
                    message: '',
                    validators: {
                        callback: {
                            message: '{{ trans("practice/patients/claim_detail.validation.original_ref_code") }}',
                            callback: function (value, validator, $field) {
                                return (value == '') ? false : true;
                            }
                        }
                    }
                },*/
				
            }
        }).on('success.form.bv', function (e) {        
            var resubcode = $('input[name="resubmission_code_value"]'); 
            resubcode.val($('input[name="original_ref_no"]').val())                
        });
    });

</script>